<?php
class Ramadan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('hr_model'); 
    }

    /**
     * Ramadan Attendance Logic
     * Supports Standard (9 AM - 2 AM) and Debt Collectors Split Shifts (1 PM-5 PM & 8 PM-1 AM)
     */
    public function get_ramadan_attendance_data($start_date, $end_date, $filters = []) {
        // --- Added 'profession' to the select statement ---
        $this->db->select('id, employee_id, subscriber_name as name, n1 as department, company_name, joining_date, profession');
        $this->db->from('emp1')->where('status', 'active');
        if (!empty($filters['department'])) $this->db->where('n1', $filters['department']);
        if (!empty($filters['company'])) $this->db->where('company_name', $filters['company']);
        $employees = $this->db->get()->result_array();
        
        if (empty($employees)) return [];
        $emp_ids = array_column($employees, 'employee_id');

        // Fetch logs with an extended window
        $tables = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'attendance_logs%'")->result_array();
        $raw_logs = [];
        if (!empty($tables)) {
            $unions = [];
            $start_dt = $this->db->escape($start_date . ' 06:00:00');
            $end_dt_plus = date('Y-m-d', strtotime($end_date . ' +1 day'));
            $end_dt = $this->db->escape($end_dt_plus . ' 06:00:00');
            $id_list = implode(',', array_map([$this->db, 'escape'], $emp_ids));

            foreach ($tables as $t) {
                $tb = $t['TABLE_NAME'];
                if ($this->db->field_exists('emp_code', $tb)) {
                    $unions[] = "SELECT emp_code, punch_time FROM `$tb` WHERE punch_time BETWEEN $start_dt AND $end_dt AND emp_code IN ($id_list)";
                }
            }
            if (!empty($unions)) {
                $sql = implode(" UNION ALL ", $unions);
                $raw_logs = $this->db->query($sql)->result_array();
            }
        }

        // Group punches by shift date (Any punch before 6 AM belongs to yesterday)
        $logs_by_shift = [];
        foreach ($raw_logs as $log) {
            $ts = strtotime($log['punch_time']);
            $hour = (int)date('H', $ts);
            
            if ($hour < 6) {
                $shift_date = date('Y-m-d', strtotime('-1 day', $ts));
            } else {
                $shift_date = date('Y-m-d', $ts);
            }
            $logs_by_shift[$log['emp_code']][$shift_date][] = $ts;
        }

        // Support Data (Vacations, Corrections, Saturdays)
        $this->db->from('orders_emp')->where_in('emp_id', $emp_ids)->where('status', '2');
        $this->db->group_start()
             ->where("date BETWEEN '$start_date' AND '$end_date'")
             ->or_where("vac_start <= '$end_date' AND vac_end >= '$start_date'")
             ->or_where("correction_date BETWEEN '$start_date' AND '$end_date'")
             ->group_end();
        $requests = $this->db->get()->result_array();
        
        $req_map = [];
        foreach ($requests as $r) {
            if ($r['type'] == 5) {
                $v_start = max($r['vac_start'], $start_date);
                $v_end = min($r['vac_end'], $end_date);
                $curr = strtotime($v_start);
                while($curr <= strtotime($v_end)) {
                    $req_map[$r['emp_id']][date('Y-m-d', $curr)]['vacation'] = true;
                    $curr = strtotime('+1 day', $curr);
                }
            } elseif ($r['type'] == 2) {
                $req_map[$r['emp_id']][$r['correction_date']]['correction'] = $r;
            }
        }

        $sats = $this->db->where_in('employee_id', $emp_ids)->where("saturday_date BETWEEN '$start_date' AND '$end_date'")->get('saturday_work_assignments')->result_array();
        $sat_map = [];
        foreach ($sats as $s) $sat_map[$s['employee_id']][$s['saturday_date']] = true;

        $report_data = [];
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));

        foreach ($employees as $emp) {
            $eid = $emp['employee_id'];
            $prof = trim($emp['profession'] ?? '');
            $is_collector = ($prof === 'محصل ديون' || $prof === 'محصل');
            
            $joining_ts = !empty($emp['joining_date']) ? strtotime($emp['joining_date']) : 0;
            
            $t = [
                'emp_id' => $eid, 'name' => $emp['name'],
                'total_working_hours' => 0, 'late_arrival_mins' => 0, 'early_exit_mins' => 0,
                'absence' => 0, 'single_fingerprint' => 0, 'working_days' => 0
            ];

            foreach ($period as $dt) {
                $date_str = $dt->format('Y-m-d');
                $ts = $dt->getTimestamp();
                if ($joining_ts > 0 && $ts < $joining_ts) continue;

                $day_num = $dt->format('N');
                $is_sat = ($day_num == 6);
                $is_mandatory_sat = $is_sat && isset($sat_map[$eid][$date_str]);

                if ($day_num == 5 || ($is_sat && !$is_mandatory_sat) || isset($req_map[$eid][$date_str]['vacation'])) { 
                    continue; 
                } 

                $next_day_str = date('Y-m-d', strtotime("$date_str +1 day"));
                $punches = $logs_by_shift[$eid][$date_str] ?? [];
                
                // Add Corrections to punches array
                if (isset($req_map[$eid][$date_str]['correction'])) {
                    $corr = $req_map[$eid][$date_str]['correction'];
                    if (!empty($corr['attendance_correction'])) $punches[] = strtotime("$date_str " . $corr['attendance_correction']);
                    if (!empty($corr['correction_of_departure'])) {
                        $corr_time = $corr['correction_of_departure'];
                        if (strpos($corr_time, '00:') === 0 || strpos($corr_time, '01:') === 0 || strpos($corr_time, '02:') === 0 || strpos($corr_time, '03:') === 0) {
                            $punches[] = strtotime("$next_day_str $corr_time"); 
                        } else {
                            $punches[] = strtotime("$date_str $corr_time");
                        }
                    }
                }

                if (empty($punches)) {
                    $t['absence']++;
                    continue;
                }

                if (count($punches) < 2) {
                    $t['single_fingerprint']++;
                    $t['working_days']++;
                    continue;
                }

                $worked_hours = 0;

                // ==========================================
                // SPLIT LOGIC: COLLECTORS vs REGULAR
                // ==========================================
                if ($is_collector) {
                    // --- Shift 1: 1 PM to 5 PM (Cap 2 Hrs) ---
                    $s1_start = strtotime("$date_str 13:00:00");
                    $s1_end = strtotime("$date_str 17:00:00");
                    
                    // Filter punches that roughly belong to the afternoon
                    $s1_punches = array_filter($punches, function($p) use ($date_str) {
                        return $p >= strtotime("$date_str 10:00:00") && $p <= strtotime("$date_str 18:00:00");
                    });

                    $worked_s1 = 0;
                    if (count($s1_punches) >= 2) {
                        $eff_start = max(min($s1_punches), $s1_start);
                        $eff_end = min(max($s1_punches), $s1_end);
                        $worked_s1 = max(0, $eff_end - $eff_start) / 3600;
                    }

                    // --- Shift 2: 8 PM to 1 AM (Cap 4 Hrs) ---
                    $s2_start = strtotime("$date_str 20:00:00");
                    $s2_end = strtotime("$next_day_str 01:00:00");
                    
                    // Filter punches that roughly belong to the night
                    $s2_punches = array_filter($punches, function($p) use ($date_str, $next_day_str) {
                        return $p >= strtotime("$date_str 18:00:00") && $p <= strtotime("$next_day_str 05:00:00");
                    });

                    $worked_s2 = 0;
                    if (count($s2_punches) >= 2) {
                        $eff_start = max(min($s2_punches), $s2_start);
                        $eff_end = min(max($s2_punches), $s2_end);
                        $worked_s2 = max(0, $eff_end - $eff_start) / 3600;
                    }

                    // Apply individual shift caps
                    $worked_s1 = min($worked_s1, 2.0);
                    $worked_s2 = min($worked_s2, 4.0);
                    $worked_hours = $worked_s1 + $worked_s2;

                } else {
                    // --- Standard 9 AM to 2 AM Logic ---
                    $shift_start_ts = strtotime("$date_str 09:00:00");
                    $shift_end_ts = strtotime("$next_day_str 02:00:00");
                    
                    $first_punch = min($punches);
                    $last_punch = max($punches);
                    
                    $effective_start = max($first_punch, $shift_start_ts);
                    $effective_end = min($last_punch, $shift_end_ts); 
                    
                    $duration = max(0, $effective_end - $effective_start);
                    $worked_hours = $duration / 3600;
                }

                // ==========================================
                // PENALTY CALCULATION (Same for everyone)
                // ==========================================
                $t['working_days']++;
                $t['total_working_hours'] += $worked_hours;

                $daily_required = 6.0;
                if ($worked_hours < $daily_required) {
                    $shortage_mins = round(($daily_required - $worked_hours) * 60);
                    $t['late_arrival_mins'] += $shortage_mins; 
                }
            }
            $t['total_working_hours'] = number_format($t['total_working_hours'], 2);
            $report_data[] = $t;
        }
        return $report_data;
    }
    // --- 1. Find Direct Manager ---
    public function get_direct_manager($emp_id) {
        $this->db->group_start()
                 ->where('n7', $emp_id)->or_where('n6', $emp_id)
                 ->or_where('n5', $emp_id)->or_where('n4', $emp_id)
                 ->or_where('n3', $emp_id)->or_where('n2', $emp_id)
                 ->group_end();
        $structure = $this->db->get('organizational_structure')->row_array();
        
        if (!$structure) return '1001'; // Default fallback to CEO/Admin

        // Traverse up the tree to find the immediate boss
        if ($structure['n7'] == $emp_id) return $structure['n6'];
        if ($structure['n6'] == $emp_id) return $structure['n5'];
        if ($structure['n5'] == $emp_id) return $structure['n4'];
        if ($structure['n4'] == $emp_id) return $structure['n3'];
        if ($structure['n3'] == $emp_id) return $structure['n2'];
        if ($structure['n2'] == $emp_id) return $structure['n1'];
        
        return $structure['n1'];
    }

    // --- 2. Approve Request & Insert to Attendance Logs ---
    public function approve_remote_request($request_id) {
        $req = $this->db->where('id', $request_id)->get('remote_work_requests')->row();
        if (!$req || $req->status != 0) return false;

        // 1. Mark as approved
        $this->db->where('id', $request_id)->update('remote_work_requests', ['status' => 1]);

        // 2. Get Employee Details
        $emp = $this->db->where('employee_id', $req->emp_id)->get('emp1')->row();
        $name_parts = explode(' ', $emp->subscriber_name);
        $first_name = $name_parts[0] ?? '';
        $last_name = isset($name_parts[1]) ? end($name_parts) : '';

        // 3. Insert Check In
        $this->db->insert('attendance_logs', [
            'emp_code' => $req->emp_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'punch_time' => $req->request_date . ' ' . $req->start_time,
            'punch_state' => 'Check In', 
            'area_alias' => 'عمل عن بعد', // Remote Work
            'terminal_sn' => 'Manual',
            'terminal_alias' => 'Manual',
            'upload_time' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 4. Insert Check Out
        $this->db->insert('attendance_logs', [
            'emp_code' => $req->emp_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'punch_time' => $req->request_date . ' ' . $req->end_time,
            'punch_state' => 'Check Out',
            'area_alias' => 'عمل عن بعد',
            'terminal_sn' => 'Manual',
            'terminal_alias' => 'Manual',
            'upload_time' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }
    // --- 1. Fetch Requests based on Role ---
    public function get_remote_requests($emp_id, $is_hr) {
        $this->db->select('r.*, e.subscriber_name as manager_name');
        $this->db->from('remote_work_requests r');
        $this->db->join('emp1 e', 'r.manager_id = e.employee_id', 'left');
        
        if (!$is_hr) {
            // If not HR, show their own requests AND requests where they are the manager
            $this->db->group_start();
            $this->db->where('r.emp_id', $emp_id);
            $this->db->or_where('r.manager_id', $emp_id);
            $this->db->group_end();
        }
        $this->db->order_by('r.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    // --- 2. Check if user is a Manager ---
    public function is_manager($emp_id) {
        $this->db->group_start()
                 ->where('n1', $emp_id)->or_where('n2', $emp_id)
                 ->or_where('n3', $emp_id)->or_where('n4', $emp_id)
                 ->or_where('n5', $emp_id)->or_where('n6', $emp_id)
                 ->group_end();
        $count = $this->db->count_all_results('organizational_structure');
        return $count > 0;
    }

    // --- 3. Process Approval & Insert to Attendance ---
    // --- 3. Process Approval & Insert to Attendance ---
    // --- 3. Process Approval & Insert to Attendance ---
    public function process_remote_approval($request_id, $action, $manager_id, $is_hr = false) {
        $req = $this->db->where('id', $request_id)->get('remote_work_requests')->row();
        if (!$req) return ['status' => 'error', 'message' => 'الطلب غير موجود.'];

        // Determine Checkout Date (If it crosses midnight, add 1 day to the checkout date!)
        $checkout_date = $req->request_date;
        if ($req->end_time < $req->start_time) {
            $checkout_date = date('Y-m-d', strtotime($req->request_date . ' +1 day'));
        }

        // HR SUPERPOWER: Reject an ALREADY APPROVED request
        if ($req->status == 1 && $action == 'reject' && $is_hr) {
            $punch_in = $req->request_date . ' ' . $req->start_time;
            $punch_out = $checkout_date . ' ' . $req->end_time;
            
            $this->db->where('emp_code', $req->emp_id)
                     ->where_in('punch_time', [$punch_in, $punch_out])
                     ->where('area_alias', 'عمل عن بعد')
                     ->delete('attendance_logs');

            $this->db->where('id', $request_id)->update('remote_work_requests', ['status' => 2]);
            return ['status' => 'success', 'message' => 'تم إلغاء الاعتماد، ورفض الطلب، ومسح البصمات السابقة من النظام.'];
        }

        // Standard workflow check
        if ($req->status != 0) return ['status' => 'error', 'message' => 'تمت معالجة هذا الطلب مسبقاً.'];

        $new_status = ($action == 'approve') ? 1 : 2;
        $this->db->where('id', $request_id)->update('remote_work_requests', ['status' => $new_status]);

        if ($action == 'approve') {
            $emp = $this->db->where('employee_id', $req->emp_id)->get('emp1')->row();
            $name_parts = explode(' ', $emp->subscriber_name);
            $first_name = $name_parts[0] ?? '';
            $last_name = isset($name_parts[1]) ? end($name_parts) : '';

            // Insert Check In (Uses the requested date)
            $this->db->insert('attendance_logs', [
                'emp_code' => $req->emp_id, 'first_name' => $first_name, 'last_name' => $last_name,
                'punch_time' => $req->request_date . ' ' . $req->start_time, 'punch_state' => 'Check In', 
                'area_alias' => 'عمل عن بعد', 'terminal_sn' => 'Remote', 'terminal_alias' => 'Remote',
                'upload_time' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')
            ]);

            // Insert Check Out (Uses the dynamically calculated checkout date)
            $this->db->insert('attendance_logs', [
                'emp_code' => $req->emp_id, 'first_name' => $first_name, 'last_name' => $last_name,
                'punch_time' => $checkout_date . ' ' . $req->end_time, 'punch_state' => 'Check Out',
                'area_alias' => 'عمل عن بعد', 'terminal_sn' => 'Remote', 'terminal_alias' => 'Remote',
                'upload_time' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')
            ]);
            return ['status' => 'success', 'message' => 'تم الاعتماد وتسجيل البصمات بنجاح.'];
        }

        return ['status' => 'success', 'message' => 'تم رفض الطلب.'];
    }
    public function get_ramadan_salary_calculation($employee_id, $sheet_id) {
        $this->db->reset_query();
        $employee = $this->db->where('employee_id', $employee_id)->get('emp1')->row();
        if (!$employee) return [];

        $total_salary = (float)$employee->total_salary;
        $daily_salary = $total_salary / 30;
        
        $ramadan_working_hours = 6.0; 
        $minute_salary = ($daily_salary / $ramadan_working_hours) / 60;

        $summary = $this->db->where('emp_id', $employee_id)->where('id_sheet', $sheet_id)->get('attendance_summary')->row();
        $absence_days = $summary ? (float)$summary->absence : 0;
        $minutes_late = $summary ? (int)$summary->minutes_late : 0;
        $minutes_early = $summary ? (int)$summary->minutes_early : 0;
        $single_punch_days = $summary ? (int)$summary->single_thing : 0;

        $attendance_deduct = ($daily_salary * $absence_days) 
                           + ($minute_salary * ($minutes_late + $minutes_early)) 
                           + ($daily_salary * $single_punch_days);

        $ins = $this->db->where('n1', $employee_id)->get('insurance_discount')->row();
        $gosi_deduction = 0.0;
        if (trim($employee->nationality) === 'سعودي') {
            $gosi_rate = $ins ? (float)$ins->n3 : 0.0;
            $gosi_deduction = ((float)$employee->base_salary + (float)$employee->housing_allowance) * $gosi_rate;
        }

        $disc = $this->db->select('SUM(amount) as amt')->where('emp_id', $employee_id)->where('sheet_id', $sheet_id)->get('discounts')->row();
        $rep = $this->db->select('SUM(amount) as amt')->where('emp_id', $employee_id)->where('sheet_id', $sheet_id)->get('reparations')->row();
        
        $other_discounts = $disc ? (float)$disc->amt : 0;
        $reparations = $rep ? (float)$rep->amt : 0;

        $total_deductions = $attendance_deduct + $other_discounts + $gosi_deduction;
        $net_salary = $total_salary + $reparations - $total_deductions;

        return [
            'total_salary' => $total_salary,
            'daily_salary' => $daily_salary,
            'minute_salary' => $minute_salary,
            'attendance_total_deduct' => $attendance_deduct,
            'gosi_deduction' => $gosi_deduction,
            'other_discounts' => $other_discounts,
            'reparations' => $reparations,
            'net_salary' => $net_salary
        ];
    }
}