<?php
    class hr_model extends CI_Model{

        /**
     * يبني نتيجة مقارنة GOSI مقابل EMP1
     * @param null|int $company 1=مرسوم, 2=مكتب الدكتور, null=الكل
     * @return array
     */

        // In hr_model.php
// REPLACE the old get_end_of_service_data function with this one
// In hr_model.php

public function get_all_balances_for_export()
{
    // This re-uses the same query logic from your DataTable, but without the page limit.
    $this->_get_datatables_query_balances();
    $query = $this->db->get();
    return $query->result();
}

// In hr_model.php

/**
 * Helper function to get all attendance log table names.
 * @return array
 */
// --- ADD TO hr_model.php ---
public function get_new_year_holiday_status($employee_id) {
    $this->db->select('holiday');
    $this->db->from('new_year_holiday');
    $this->db->where('emp_id', $employee_id);
    $query = $this->db->get();
    
    if ($query->num_rows() > 0) {
        $result = (int)$query->row()->holiday;
        // Log for debugging
        error_log("New Year holiday check for {$employee_id}: {$result}");
        return $result; // Returns 1 for holiday, 0 for work day
    }
    
    // Log for debugging
    error_log("New Year holiday check for {$employee_id}: NO RECORD FOUND");
    return null; // No record found
}
// 1. Fetch all active employees combined with their holiday status
public function get_new_year_holiday_data($employee_ids) {
    if (empty($employee_ids)) {
        return [];
    }
    
    $this->db->select('emp_id, holiday');
    $this->db->from('new_year_holiday');
    $this->db->where_in('emp_id', $employee_ids);
    $query = $this->db->get();
    
    $result = [];
    if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {
            $result[$row->emp_id] = (int)$row->holiday; // 1 for holiday, 0 for work day
        }
    }
    return $result;
}

// 2. Update or Insert the holiday status
public function update_holiday_status($emp_id, $emp_name, $status) {
    // Check if record exists
    $this->db->where('emp_id', $emp_id);
    $query = $this->db->get('new_year_holiday');

    if ($query->num_rows() > 0) {
        // Update existing
        $this->db->where('emp_id', $emp_id);
        return $this->db->update('new_year_holiday', ['holiday' => $status, 'emp_name' => $emp_name]);
    } else {
        // Insert new
        return $this->db->insert('new_year_holiday', [
            'emp_id' => $emp_id,
            'emp_name' => $emp_name,
            'holiday' => $status
        ]);
    }
}
public function get_daily_attendance_log_ramadan($employee_id, $sheet_id) {
    try {
        // =========================================================
        // MANUAL OVERRIDE: HARDCODED DATES AS REQUESTED
        // =========================================================
        $start_date_str = '2026-02-18';
        $end_date_str   = '2026-03-17';
        // =========================================================

        // 2. Fetch Public Holidays
        $public_holidays = [];
        $h_query = $this->db->query("SELECT holiday_date FROM public_holidays WHERE holiday_date BETWEEN '$start_date_str' AND '$end_date_str'");
        if ($h_query !== false) {
            foreach($h_query->result() as $h) {
                $public_holidays[] = $h->holiday_date;
            }
        }

        // 3. Fetch Approved Leaves & Corrections
        // 3. Fetch Approved Leaves & Corrections
$this->db->select('oe.*');
$this->db->from('orders_emp oe');
$this->db->where('oe.emp_id', $employee_id);
$this->db->where('oe.status', '2'); 

// UPDATED FIX: Match your specific workflow data (type '5' and status 'approved')
$this->db->where("EXISTS (
    SELECT 1 FROM approval_workflow aw 
    WHERE aw.order_id = oe.id 
    AND aw.order_type = '5' 
    AND aw.approval_level = '3'
    AND aw.status = 'approved' 
    AND aw.action_date <= '$end_date_str 23:59:59'
)", NULL, FALSE);

$this->db->group_start();
    $this->db->where("oe.correction_date BETWEEN '$start_date_str' AND '$end_date_str'");
    $this->db->or_where("((oe.vac_start <= '$end_date_str' AND oe.vac_end >= '$start_date_str') OR (vac_half_date BETWEEN '$start_date_str' AND '$end_date_str'))", NULL, FALSE);
$this->db->group_end();

$req_query = $this->db->get();
$requests = $req_query ? $req_query->result() : [];

$corrections = []; $leaves = [];
foreach($requests as $req) {
    if($req->type == 2 && !empty($req->correction_date)) {
        $corrections[$req->correction_date] = $req;
    }
    
    if($req->type == 5) {
        if (!empty($req->vac_start) && !empty($req->vac_end)) {
            $p = new DatePeriod(
                new DateTime(max($req->vac_start, $start_date_str)), 
                new DateInterval('P1D'), 
                (new DateTime(min($req->vac_end, $end_date_str)))->modify('+1 day')
            );
            foreach($p as $d) {
                $leaves[$d->format('Y-m-d')] = true;
            }
        }
        if(!empty($req->vac_half_date)) {
            $leaves[$req->vac_half_date] = $req->vac_half_period ?? 'am';
        }
    }
}

        // 4. Fetch Mandatory Saturdays
        $this->db->select('saturday_date');
        $this->db->where('employee_id', $employee_id);
        $this->db->where('saturday_date >=', $start_date_str);
        $this->db->where('saturday_date <=', $end_date_str);
        $sat_query = $this->db->get('saturday_work_assignments');
        $sat_result = $sat_query ? $sat_query->result_array() : [];
        $mandatory_saturdays = array_column($sat_result, 'saturday_date');

        // 5. Fetch Punches & Apply 6-Hour Night Shift Offset
        $tables = $this->_get_attendance_tables();
        $shift_punches = [];
        // Extend to capture next day checkout (March 18th 05:59 AM to catch everything safely)
        $end_date_query = '2026-03-18 05:59:59';

        if(!empty($tables)) {
            $unions = [];
            foreach ($tables as $table) {
                 if ($this->db->table_exists($table)) {
                     $unions[] = "SELECT punch_time FROM `$table` WHERE emp_code = ".$this->db->escape($employee_id)." AND punch_time BETWEEN '$start_date_str 00:00:00' AND '$end_date_query'";
                 }
            }
            if(!empty($unions)) {
                $union_sql = implode(" UNION ALL ", $unions);
                $p_query = $this->db->query("SELECT punch_time FROM ($union_sql) as u ORDER BY punch_time ASC");
                if ($p_query) {
                    foreach($p_query->result_array() as $p) {
                        $ts = strtotime($p['punch_time']);
                        $hour = (int)date('H', $ts);
                        $shift_date = ($hour < 6) ? date('Y-m-d', strtotime('-1 day', $ts)) : date('Y-m-d', $ts);
                        $shift_punches[$shift_date][] = $ts;
                    }
                }
            }
        }

        // 6. Calculate Daily Violations
        $daily_log = [];
        $summary = ['minutes_late' => 0, 'minutes_early' => 0, 'absence' => 0, 'single_thing' => 0];

        $days_arabic = ['Sunday'=>'الأحد','Monday'=>'الإثنين','Tuesday'=>'الثلاثاء','Wednesday'=>'الأربعاء','Thursday'=>'الخميس','Friday'=>'الجمعة','Saturday'=>'السبت'];

        $rules = [];
        $rules_query = $this->db->get_where('work_restrictions', ['emp_id' => $employee_id]);
        if ($rules_query !== false) { $rules = $rules_query->row_array(); }
        $is_breastfeeding = (!empty($rules['working_hours']) && (float)$rules['working_hours'] == 8.0);
        
        $emp_details = null;
        $emp_query = $this->db->select('profession')->where('employee_id', $employee_id)->get('emp1');
        if ($emp_query !== false) { $emp_details = $emp_query->row(); }
        $is_collector = ($emp_details && strpos($emp_details->profession, 'محصل') !== false);

        // ========================================================================
        // HARDCODED TIMESTAMP LOOP: Guaranteed to run until 23:59:59 of March 16
        // ========================================================================
        $current_ts = strtotime($start_date_str . ' 00:00:00');
        $end_ts     = strtotime($end_date_str . ' 23:59:59');

        while ($current_ts <= $end_ts) {
            $date = date('Y-m-d', $current_ts);
            $day_name = $days_arabic[date('l', $current_ts)] ?? date('l', $current_ts);
            $day_num = date('N', $current_ts);

            // SAFE EXCLUSIONS
            if (in_array($date, $public_holidays) || (isset($leaves[$date]) && $leaves[$date] === true)) {
                $current_ts = strtotime('+1 day', $current_ts); continue;
            }
            
            $is_mandatory_sat = ($day_num == 6 && in_array($date, $mandatory_saturdays));
            if ($day_num == 5 || ($day_num == 6 && !$is_mandatory_sat)) {
                $current_ts = strtotime('+1 day', $current_ts); continue; 
            }

            // Setup Required Hours
            $is_ramadan = (strtotime($date) >= strtotime('2026-02-18') && strtotime($date) <= strtotime('2026-03-19'));
            $base_hours = 9.0;
            if ($is_ramadan) {
                $base_hours = $is_breastfeeding ? 5.0 : 6.0;
            } elseif ($is_mandatory_sat) {
                $base_hours = 6.0;
            }
            $is_half_day = isset($leaves[$date]) && $leaves[$date] !== true;
            $daily_required_hours = $is_half_day ? ($base_hours / 2) : $base_hours;

            $punches_today = isset($shift_punches[$date]) ? $shift_punches[$date] : [];
            $correction_data = isset($corrections[$date]) ? $corrections[$date] : null;

            if ($correction_data) {
                 if(!empty($correction_data->attendance_correction)) $punches_today[] = strtotime("$date " . $correction_data->attendance_correction);
                 if(!empty($correction_data->correction_of_departure)) {
                     $corr_out = $correction_data->correction_of_departure;
                     if ($is_ramadan && (strpos($corr_out, '00:') === 0 || strpos($corr_out, '01:') === 0 || strpos($corr_out, '02:') === 0 || strpos($corr_out, '03:') === 0 || strpos($corr_out, '04:') === 0 || strpos($corr_out, '05:') === 0)) {
                         $punches_today[] = strtotime(date('Y-m-d', strtotime("$date +1 day")) . " $corr_out");
                     } else {
                         $punches_today[] = strtotime("$date $corr_out");
                     }
                 }
            }

            if (empty($punches_today)) {
                if (!$is_half_day) {
                    $summary['absence']++;
                    $daily_log[] = [
                        'date' => $date, 'day_name' => $day_name, 'check_in' => '--', 'check_out' => '--',
                        'is_absent' => true, 'is_single' => false, 'late_minutes' => 0, 'early_minutes' => 0, 'violation_details' => []
                    ];
                }
            } else {
                $punches_today = array_unique($punches_today);
                sort($punches_today);

                $first_ts = min($punches_today);
                $last_ts = max($punches_today);
                
                if (count($punches_today) == 1 || ($last_ts - $first_ts) < 60) {
                    $summary['single_thing']++;
                    $daily_log[] = [
                        'date' => $date, 'day_name' => $day_name, 'check_in' => date('H:i', $first_ts), 'check_out' => '--',
                        'is_absent' => false, 'is_single' => true, 'late_minutes' => 0, 'early_minutes' => 0, 'violation_details' => []
                    ];
                } else {
                    if ($is_ramadan) {
                        if ($is_collector) {
                            $s1_pts = []; $s2_pts = [];
                            foreach ($punches_today as $p) {
                                if ($p >= strtotime($date . ' 06:00:00') && $p <= strtotime($date . ' 17:59:59')) $s1_pts[] = $p;
                                if ($p >= strtotime($date . ' 18:00:00') && $p <= strtotime($date . ' +1 day 05:59:59')) $s2_pts[] = $p;
                            }
                            $worked_s1 = count($s1_pts) >= 2 ? (max($s1_pts) - min($s1_pts)) / 3600 : 0;
                            $worked_s2 = count($s2_pts) >= 2 ? (max($s2_pts) - min($s2_pts)) / 3600 : 0;
                            $worked_hours = $worked_s1 + $worked_s2;
                        } else {
                            $worked_hours = ($last_ts - $first_ts) / 3600;
                        }
                        
                        $shortage_hours = $daily_required_hours - $worked_hours;
                        if ($shortage_hours > 0.033) {
                            $shortage_mins = round($shortage_hours * 60);
                            $summary['early_minutes'] += $shortage_mins;
                            $daily_log[] = [
                                'date' => $date, 'day_name' => $day_name, 'check_in' => date('H:i', $first_ts), 'check_out' => date('H:i', $last_ts),
                                'is_absent' => false, 'is_single' => false, 'late_minutes' => 0, 'early_minutes' => $shortage_mins, 'violation_details' => ["نقص ساعات: $shortage_mins دقيقة"]
                            ];
                        }
                    }
                }
            }

            // Move to the next day safely
            $current_ts = strtotime('+1 day', $current_ts);
        }

        return ['daily_log' => $daily_log, 'summary' => $summary];
    } catch (Exception $e) {
        log_message('error', 'Error in get_daily_attendance_log_ramadan: ' . $e->getMessage());
        return ['daily_log' => [], 'summary' => []];
    }
}
public function check_mandate_overlap($emp_id, $start_date, $end_date, $exclude_id = null) {
        $this->db->where('emp_id', $emp_id);
        
        // Exclude rejected requests (Assuming 9 is your Rejected status)
        // If you have a 'Canceled' status, add it here (e.g., [8, 9])
        $this->db->where('status !=', 9); 
        
        // If editing an existing mandate, exclude its own ID from the check
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        
        // Date Overlap Logic: 
        // Existing mandate overlaps if its start is before the NEW end 
        // AND its end is after the NEW start.
        $this->db->where('start_date <=', $end_date);
        $this->db->where('end_date >=', $start_date);
        
        $query = $this->db->get('mandate_requests');
        
        return $query->num_rows() > 0; // Returns TRUE if an overlap exists
    }
public function check_delegate_availability_conflict($delegate_id, $start_date, $end_date)
{
    if (empty($delegate_id) || empty($start_date) || empty($end_date)) {
        return null;
    }

    $this->db->select('id'); // We only need to know if a conflict exists
    $this->db->from('orders_emp');
    
    // Check for approved vacations FOR THE DELEGATE
    $this->db->where('emp_id', $delegate_id); 
    $this->db->where('type', 5);    // Vacation
    $this->db->where('status', '2'); // Approved

    // Overlap logic: (new_start <= old_end) AND (new_end >= old_start)
    // This also covers half-day leaves since vac_start/vac_end are set to the same day
    $this->db->where('vac_start <=', $end_date);
    $this->db->where('vac_end >=', $start_date);
    
    $this->db->limit(1);
    $query = $this->db->get();

    return $query->row_array();
}
public function get_comprehensive_attendance_report($start_date, $end_date, $filters = [])
{
    // 1. Fetch Active Employees
    // Select 'address' (which holds the location info)
    $this->db->select('id, employee_id, subscriber_name as name, n1 as department, company_name, joining_date, address'); 
    $this->db->from('emp1');
    $this->db->where('status', 'active');
    
    if (!empty($filters['department'])) $this->db->where('n1', $filters['department']);
    if (!empty($filters['company'])) $this->db->where('company_name', $filters['company']);
    
    // NEW: Filter by Address if 'location' filter is set
    if (!empty($filters['location'])) {
        $this->db->where('address', $filters['location']);
    }
    
    $employees = $this->db->get()->result_array();
    
    // If no employees found, return empty
    if (empty($employees)) return [];
    
    $emp_ids = array_column($employees, 'employee_id');

    // =========================================================
    // 2. Fetch Raw Attendance Logs
    // =========================================================
    $tables = $this->_get_attendance_tables(); 
    $raw_logs_query = [];

    if (!empty($tables) && !empty($emp_ids)) {
        $unions = [];
        $start_dt = $this->db->escape($start_date . ' 00:00:00');
        $end_dt = $this->db->escape($end_date . ' 23:59:59');
        $escaped_ids = array_map(function($id) { return $this->db->escape($id); }, $emp_ids);
        $id_list = implode(',', $escaped_ids);

        foreach ($tables as $table) {
            if ($this->db->field_exists('emp_code', $table) && $this->db->field_exists('punch_time', $table)) {
                $unions[] = "SELECT emp_code, punch_time FROM `$table` 
                             WHERE punch_time BETWEEN $start_dt AND $end_dt 
                             AND emp_code IN ($id_list)";
            }
        }

        if (!empty($unions)) {
            $sql = implode(" UNION ALL ", $unions) . " ORDER BY punch_time ASC";
            $query = $this->db->query($sql);
            $raw_logs_query = $query->result_array();
        }
    }

    $logs_by_day = [];
    foreach ($raw_logs_query as $log) {
        $dt = new DateTime($log['punch_time']);
        $date = $dt->format('Y-m-d');
        $logs_by_day[$log['emp_code']][$date][] = $dt->getTimestamp();
    }

    // 3. Fetch Supporting Data
    $this->db->from('orders_emp');
    $this->db->where_in('emp_id', $emp_ids)->where('status', '2'); 
    $this->db->group_start();
        $this->db->where("date BETWEEN '$start_date' AND '$end_date'"); 
        $this->db->or_where("vac_start <= '$end_date' AND vac_end >= '$start_date'");
        $this->db->or_where("correction_date BETWEEN '$start_date' AND '$end_date'");
    $this->db->group_end();
    $requests = $this->db->get()->result_array();
    
    $req_map = [];
    foreach ($requests as $r) {
        if ($r['type'] == 5) {
            $v_start = max($r['vac_start'], $start_date);
            $v_end = min($r['vac_end'], $end_date);
            $curr = strtotime($v_start);
            $last = strtotime($v_end);
            while($curr <= $last) {
                $req_map[$r['emp_id']][date('Y-m-d', $curr)]['vacation'] = $r['vac_main_type'] ?? 'إجازة';
                $curr = strtotime('+1 day', $curr);
            }
        } elseif ($r['type'] == 2) {
            $req_map[$r['emp_id']][$r['correction_date']]['correction'] = $r;
        } elseif ($r['type'] == 3) {
            $req_map[$r['emp_id']][$r['date']]['overtime'] = (float)$r['ot_hours'];
        }
    }

    $this->db->where_in('emp_id', $emp_ids)->where('status', 'Approved');
    $this->db->where("start_date <= '$end_date' AND end_date >= '$start_date'");
    $mandates = $this->db->get('orders.mandate_requests')->result_array();
    $mandate_map = [];
    foreach ($mandates as $m) {
        $curr = strtotime(max($m['start_date'], $start_date));
        $last = strtotime(min($m['end_date'], $end_date));
        while($curr <= $last) {
            $mandate_map[$m['emp_id']][date('Y-m-d', $curr)] = true;
            $curr = strtotime('+1 day', $curr);
        }
    }

    $this->db->where_in('employee_id', $emp_ids)->where("saturday_date BETWEEN '$start_date' AND '$end_date'");
    $sats = $this->db->get('orders.saturday_work_assignments')->result_array();
    $sat_map = [];
    foreach ($sats as $s) $sat_map[$s['employee_id']][$s['saturday_date']] = true;

    $this->db->where_in('emp_id', $emp_ids);
    $rules_res = $this->db->get('orders.work_restrictions')->result_array();
    $rules_map = [];
    foreach ($rules_res as $rule) {
        $rules_map[$rule['emp_id']] = [
            'hours' => (float)$rule['working_hours'] > 0 ? (float)$rule['working_hours'] : 9.0,
            'start' => !empty($rule['first_punch']) ? $rule['first_punch'] : '08:00:00',
            'threshold' => !empty($rule['last_punch']) ? $rule['last_punch'] : '08:30:00'
        ];
    }

    // 4. Calculation Loop
    $report_data = [];
    $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));

    foreach ($employees as $emp) {
        $eid = $emp['employee_id'];
        $rule = $rules_map[$eid] ?? ['hours' => 9.0, 'start' => '08:00:00', 'threshold' => '08:30:00'];
        $joining_ts = !empty($emp['joining_date']) ? strtotime($emp['joining_date']) : 0;

        $t = [
            'emp_id' => $eid, 
            'name' => $emp['name'],
            'location' => $emp['address'], // <--- Map 'address' DB column to 'location' output
            'total_working_hours' => 0, 'total_office_hours' => 0,
            'late_arrival' => 0, 'late_arrival_excuse' => 0,
            'early_exit' => 0, 'early_exit_permission' => 0,
            'single_fingerprint' => 0, 'overtime' => 0, 'confirmed_overtime' => 0,
            'absence' => 0, 'holidays' => 0, 'rest_days' => 0, 'business_travel' => 0, 'working_days' => 0
        ];

        foreach ($period as $dt) {
            $date_str = $dt->format('Y-m-d');
            $ts = $dt->getTimestamp();
            $day_num = $dt->format('N'); 

            if ($joining_ts > 0 && $ts < $joining_ts) continue;

            $is_sat = ($day_num == 6);
            $is_mandatory_sat = $is_sat && isset($sat_map[$eid][$date_str]);

            if ($day_num == 5) { $t['rest_days']++; continue; }
            if ($is_sat && !$is_mandatory_sat) { $t['rest_days']++; continue; }

            if (isset($mandate_map[$eid][$date_str])) { $t['business_travel']++; continue; }
            if (isset($req_map[$eid][$date_str]['vacation'])) { $t['holidays']++; continue; }

            $raw_punches = $logs_by_day[$eid][$date_str] ?? [];
            $first_punch = null; 
            $last_punch = null;

            $cutoff_in = strtotime("$date_str 12:00:00");
            $start_out = strtotime("$date_str 13:00:00");
            $limit_out = strtotime("$date_str 21:00:00");

            foreach ($raw_punches as $p_ts) {
                if ($p_ts < $cutoff_in) {
                    if ($first_punch === null || $p_ts < $first_punch) $first_punch = $p_ts;
                }
                if ($p_ts >= $start_out && $p_ts <= $limit_out) {
                    if ($last_punch === null || $p_ts > $last_punch) $last_punch = $p_ts;
                }
            }

            $has_correction = isset($req_map[$eid][$date_str]['correction']);
            if ($has_correction) {
                $corr = $req_map[$eid][$date_str]['correction'];
                if (!empty($corr['attendance_correction'])) $first_punch = strtotime("$date_str " . $corr['attendance_correction']);
                if (!empty($corr['correction_of_departure'])) $last_punch = strtotime("$date_str " . $corr['correction_of_departure']);
            }

            if ($is_mandatory_sat) {
                $daily_required = 6.0;
                $t['total_office_hours'] += $daily_required;
                if (!$first_punch && !$last_punch) {
                    if (!$has_correction) $t['absence']++;
                } else {
                    $t['working_days']++;
                    if ($first_punch && $last_punch) {
                        $duration = $last_punch - $first_punch;
                        if ($duration > 0) $t['total_working_hours'] += ($duration / 3600);
                    }
                }
                continue; 
            }

            $daily_required = $rule['hours'];
            $t['total_office_hours'] += $daily_required;

            if (!$first_punch && !$last_punch) {
                if (!$has_correction) $t['absence']++;
                continue;
            }

            $duration = ($first_punch && $last_punch) ? ($last_punch - $first_punch) : 0;
            if (!$first_punch || !$last_punch || $duration < 3600) {
                if (!$has_correction) $t['single_fingerprint']++; 
                $t['working_days']++;
                continue;
            }

            $t['working_days']++;
            $work_hours = $duration / 3600;
            $t['total_working_hours'] += $work_hours;

            if ($work_hours > $daily_required) {
                $t['overtime'] += ($work_hours - $daily_required);
            }

            $late_threshold_str = $rule['threshold'];
            $threshold_ts = strtotime("$date_str $late_threshold_str");
            $late_mins = 0;
            if ($first_punch > $threshold_ts) {
                $late_mins = floor(($first_punch - $threshold_ts) / 60);
                if ($has_correction) $t['late_arrival_excuse'] += $late_mins;
                else $t['late_arrival'] += $late_mins;
            }

            $req_seconds = $daily_required * 3600;
            $expected_out = $first_punch + $req_seconds;
            $gross_early_mins = max(0, floor(($expected_out - $last_punch) / 60));
            $net_early_mins = max(0, $gross_early_mins - $late_mins);

            if ($net_early_mins > 2) { 
                if ($has_correction) $t['early_exit_permission'] += $net_early_mins;
                else $t['early_exit'] += $net_early_mins;
            }

            if (isset($req_map[$eid][$date_str]['overtime'])) {
                $t['confirmed_overtime'] += $req_map[$eid][$date_str]['overtime'];
            }
        }
        
        $t['total_working_hours'] = number_format($t['total_working_hours'], 2);
        $t['total_office_hours'] = number_format($t['total_office_hours'], 2);
        $t['overtime'] = number_format($t['overtime'], 2);
        
        $report_data[] = $t;
    }

    return $report_data;
}
/**
     * Determines the manager dynamically based on the n1-n7 structure
     */
    public function get_hierarchy_manager($emp_id) {
        $this->db->select('n1, n2, n3, n4, n5, n6, n7, manager');
        $this->db->where('employee_id', $emp_id);
        $query = $this->db->get('emp1');
        
        if ($query->num_rows() > 0) {
            $row = $query->row_array();

            // Walk up the hierarchy chain to find the next manager
            if ($row['n7'] == $emp_id && !empty($row['n6'])) return $row['n6'];
            if ($row['n6'] == $emp_id && !empty($row['n5'])) return $row['n5'];
            if ($row['n5'] == $emp_id && !empty($row['n4'])) return $row['n4'];
            if ($row['n4'] == $emp_id && !empty($row['n3'])) return $row['n3'];
            if ($row['n3'] == $emp_id && !empty($row['n2'])) return $row['n2'];
            if ($row['n2'] == $emp_id && !empty($row['n1'])) return $row['n1'];

            // Fallback to standard 'manager' column if the N structure isn't populated
            if (!empty($row['manager'])) return $row['manager'];
        }
        return false;
    }
    /**
     * Sends the actual email using custom SMTP and LOGS the result
     */
    public function send_html_email($to, $subject, $html, $order_id = null) {
        $this->load->library('email');

        $email_config = array(
            'protocol'    => 'smtp',
            'smtp_host'   => 'MAR-PRD-EXH01.MARSOOM.NET',
            'smtp_user'   => 'itsystem@marsoom.net',
            'smtp_pass'   => 'Asd@123123', 
            'smtp_port'   => 587,
            'smtp_crypto' => 'tls',
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
            'crlf'        => "\r\n",
            'wordwrap'    => TRUE,
            'smtp_timeout'=> 30,
            'smtp_keepalive' => FALSE,
            'smtp_conn_options' => array(
                'ssl' => array(
                    'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true
                )
            )
        );

        $this->email->initialize($email_config);
        $this->email->clear(TRUE);
        $this->email->from("IT.systems@marsoom.net", "Marsoom • HR");
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($html);

        // Variables for logging
        $status = 'success';
        $error_message = null;
        $send_result = false;

        // Attempt to send
        if($this->email->send()) {
            $send_result = true;
        } else {
            $send_result = false;
            $status = 'failed';
            // Capture the exact SMTP error for debugging
            $error_message = $this->email->print_debugger(['headers']);
            log_message('error', 'Email failed: ' . $error_message);
        }

        // --- NEW: Insert record into email_logs table ---
        $log_data = [
            'order_id'        => $order_id,
            'recipient_email' => $to,
            'subject'         => $subject,
            'status'          => $status,
            'error_message'   => $error_message,
            'sent_at'         => date('Y-m-d H:i:s')
        ];
        $this->db->insert('email_logs', $log_data);
        // ------------------------------------------------

        return $send_result;
    }

    /**
     * Figures out who gets the first email and builds the HTML template
     */
   /**
     * Figures out who gets the first email and builds the HTML template
     */
  /**
     * Get order details from database and prepare for email template
     */
   /**
     * Get order details from database and prepare for email template (Array Safe Version)
     */
    public function get_order_details_for_email($order_id) {
        
        // Fetch the order from database as an ARRAY, which is much safer for missing columns
        $this->db->where('id', $order_id);
        $order = $this->db->get('orders_emp')->row_array();
        
        if (!$order) {
            return false;
        }
        
        // Prepare details array
        $details_array = [];
        
        // Common fields for all orders
        $details_array['رقم الطلب'] = $order['id'] ?? '—';
        $details_array['الموظف'] = $order['emp_name'] ?? '—';
        $details_array['التاريخ'] = $order['date'] ?? '—';
        
        $order_name = trim($order['order_name'] ?? '');
        $order_type = (int)($order['type'] ?? 0);
        
        // Check order type matching exactly how your views handle it
        if ($order_name === 'إجازة' || $order_type === 5) {
            $details_array['نوع الإجازة'] = $order['vac_main_type'] ?? '—';
            $details_array['تاريخ البدء'] = $order['vac_start'] ?? '—';
            $details_array['تاريخ الانتهاء'] = $order['vac_end'] ?? '—';
            $details_array['عدد الأيام'] = ($order['vac_days_count'] ?? '0') . ' يوم';
            $details_array['السبب'] = $order['vac_reason'] ?? '—';
            if (!empty($order['delegation_employee_id'])) {
                $details_array['الموظف المفوض'] = $order['delegation_employee_id'];
            }
            
        } elseif ($order_name === 'عمل إضافي') {
            $details_array['نوع الإضافي'] = $order['ot_type'] ?? '—';
            if (($order['ot_type'] ?? '') === 'single') {
                $details_array['تاريخ الإضافي'] = $order['ot_date'] ?? '—';
            } else {
                $details_array['من تاريخ'] = $order['ot_from'] ?? '—';
                $details_array['إلى تاريخ'] = $order['ot_to'] ?? '—';
            }
            $details_array['عدد الساعات'] = $order['ot_hours'] ?? '—';
            if (!empty($order['ot_amount'])) {
                $details_array['قيمة الإضافي'] = $order['ot_amount'] . ' ريال';
            }
            $details_array['السبب'] = $order['ot_reason'] ?? '—';
            
        } elseif ($order_name === 'مصاريف مالية') {
            $details_array['اسم المصروف'] = $order['exp_item_name'] ?? '—';
            $details_array['المبلغ'] = ($order['exp_amount'] ?? '0') . ' ريال';
            $details_array['تاريخ المصروف'] = $order['exp_date'] ?? '—';
            $details_array['السبب'] = $order['exp_reason'] ?? '—';
            
        } elseif ($order_name === 'طلب عُهدة') {
            $details_array['نوع العهدة'] = $order['asset_type'] ?? '—';
            $details_array['وصف العهدة'] = $order['asset_desc'] ?? '—';
            $details_array['السبب'] = $order['asset_reason'] ?? '—';
            
        } elseif ($order_name === 'مهمة عمل' || $order_name === 'work_mission' || $order_type === 9) {
            $details_array['نوع المهمة'] = $order['mission_type'] ?? '—';
            $details_array['تاريخ المهمة'] = $order['mission_date'] ?? '—';
            $details_array['وقت البدء'] = $order['mission_start_time'] ?? '—';
            $details_array['وقت الانتهاء'] = $order['mission_end_time'] ?? '—';
            $details_array['ملاحظات'] = $order['mission_note'] ?? '—';
            
        } elseif ($order_name === 'الاستئذان' || $order_type === 12) {
            // Using safe fallbacks in case 'permission_date' isn't standard in the DB schema
            $details_array['تاريخ الاستئذان'] = $order['permission_date'] ?? $order['date'] ?? '—';
            $details_array['وقت البدء'] = $order['permission_start_time'] ?? '—';
            $details_array['وقت الانتهاء'] = $order['permission_end_time'] ?? '—';
            $details_array['المدة'] = ($order['permission_hours'] ?? '—') . ' ساعة';
            $details_array['السبب'] = $order['note'] ?? '—';
            
        } elseif ($order_name === 'تصحيح بصمة') {
            $details_array['تاريخ التصحيح'] = $order['correction_date'] ?? '—';
            $details_array['تصحيح الحضور'] = $order['attendance_correction'] ?? '—';
            $details_array['تصحيح الانصراف'] = $order['correction_of_departure'] ?? '—';
            $details_array['السبب'] = $order['reason_for_correction'] ?? '—';
            
        } elseif ($order_name === 'طلب خطاب') {
            $details_array['نوع الخطاب'] = $order['letter_type'] ?? '—';
            $details_array['إلى (عربي)'] = $order['letter_to_ar'] ?? '—';
            $details_array['السبب'] = $order['letter_reason'] ?? '—';
            
        } elseif ($order_name === 'استقالة') {
            $details_array['تاريخ آخر يوم عمل'] = $order['date_of_the_last_working'] ?? '—';
            $details_array['سبب الاستقالة'] = $order['reason_for_resignation'] ?? '—';
            
        } else {
            // Generic fallback for any other custom order types
            $details_array['تفاصيل إضافية'] = $order['note'] ?? '—';
        }
        
        // Status information exactly matching your view_request_details logic
        $st = (int)($order['status'] ?? 0);
        if ($st === 2) { 
            $details_array['حالة الطلب'] = '<span style="color:#28a745">معتمد نهائياً</span>'; 
        } elseif ($st === 3) { 
            $details_array['حالة الطلب'] = '<span style="color:#dc3545">مرفوض</span>'; 
        } elseif ($st === 1) { 
            $details_array['حالة الطلب'] = '<span style="color:#17a2b8">بانتظار موافقة الموارد البشرية</span>'; 
        } else { 
            $details_array['حالة الطلب'] = '<span style="color:#ffc107">بانتظار المعالجة المبدئية</span>'; 
        }
        
        // Add reason for rejection if it exists
        if (!empty($order['reason_for_rejection'])) {
            $details_array['سبب الرفض'] = '<span style="color:#dc3545">' . htmlspecialchars($order['reason_for_rejection']) . '</span>';
        }
        
        return $details_array;
    }
/**
     * Get mandate details from database and prepare for email template
     */
    public function get_mandate_details_for_email($req_id) {
        $this->db->select('mr.*, e.subscriber_name as emp_name');
        $this->db->from('mandate_requests mr');
        $this->db->join('emp1 e', 'e.employee_id = mr.emp_id', 'left');
        $this->db->where('mr.id', $req_id);
        $order = $this->db->get()->row_array();
        
        if (!$order) return false;
        
        $details_array = [
            'رقم الطلب' => $order['id'] ?? '—',
            'الموظف' => $order['emp_name'] ?? '—',
            'نوع الطلب' => 'انتداب (Mandate)',
            'تاريخ الطلب' => $order['request_date'] ?? '—',
            'تاريخ البدء' => $order['start_date'] ?? '—',
            'تاريخ الانتهاء' => $order['end_date'] ?? '—',
            'مدة الانتداب' => ($order['duration_days'] ?? '0') . ' يوم',
            'إجمالي المبلغ' => ($order['total_amount'] ?? '0') . ' ريال',
            'السبب' => $order['reason'] ?? '—',
        ];
        
        if ($order['current_approver'] === 'Completed' || $order['status'] === 'Approved') {
            $details_array['حالة الطلب'] = '<span style="color:#28a745">معتمد نهائياً</span>';
        } elseif ($order['status'] === 'Rejected') {
            $details_array['حالة الطلب'] = '<span style="color:#dc3545">مرفوض نهائياً</span>';
            if (!empty($order['rejection_reason'])) {
                $details_array['سبب الرفض'] = '<span style="color:#dc3545">' . htmlspecialchars($order['rejection_reason']) . '</span>';
            }
        } elseif ($order['status'] === 'Returned') {
            $details_array['حالة الطلب'] = '<span style="color:#ffc107">مُعاد للمراجعة</span>';
            if (!empty($order['rejection_reason'])) {
                $details_array['ملاحظات الإرجاع'] = '<span style="color:#dc3545">' . htmlspecialchars($order['rejection_reason']) . '</span>';
            }
        } else {
            $details_array['حالة الطلب'] = '<span style="color:#FF8C00">قيد الاعتماد</span>';
        }
        
        return $details_array;
    }

    /**
     * Trigger email for Mandate Request routing
     */
    public function trigger_mandate_email($req_id) {
        $this->db->where('id', $req_id);
        $order = $this->db->get('mandate_requests')->row_array();
        
        if (!$order) return false;

        $applicant_id = $order['emp_id'];
        $status = $order['status']; 
        $current_approver = $order['current_approver'];
        
        $next_approver_id = null;
        $email_subject = "طلب انتداب بحاجة لاعتمادك - Marsoom HR";
        $action_text = "مراجعة واعتماد الانتداب";
        $status_color = "#FF8C00";
        $custom_link = "https://services.marsoom.net/hr/users1/mandate_approvals"; // Link for approvers

        // 1. ROUTING LOGIC
        if ($current_approver === 'Completed' || $status === 'Approved') {
            $next_approver_id = $applicant_id;
            $email_subject = "تم اعتماد الانتداب بنجاح ✅ - Marsoom HR";
            $action_text = "الاطلاع على الانتداب";
            $status_color = "#28a745";
            $custom_link = "https://services.marsoom.net/hr/users1/my_mandates"; // Link for applicant
        } elseif ($status === 'Rejected') {
            $next_approver_id = $applicant_id;
            $email_subject = "تم رفض طلب الانتداب ❌ - Marsoom HR";
            $action_text = "تفاصيل الرفض";
            $status_color = "#dc3545";
            $custom_link = "https://services.marsoom.net/hr/users1/my_mandates";
        } elseif ($status === 'Returned') {
            $next_approver_id = $current_approver; // Goes to whoever it was returned to (e.g. 2784)
            $email_subject = "إرجاع طلب انتداب للمراجعة ⚠️ - Marsoom HR";
            $status_color = "#ffc107";
        } elseif (!empty($current_approver)) {
            $next_approver_id = $current_approver; // Normal pending workflow
        } else {
            return false;
        }

        // 2. FETCH RECIPIENT
        $recipient = $this->db->select('email, subscriber_name')->where('employee_id', $next_approver_id)->get('emp1')->row_array();
        
        if (!$recipient || empty(trim($recipient['email']))) {
            $this->db->insert('email_logs', [
                'order_id' => $req_id,
                'recipient_email' => $next_approver_id . ' (NO EMAIL FOUND)',
                'subject' => $email_subject,
                'status' => 'failed',
                'error_message' => 'لم يتم إرسال إيميل (الانتداب) لأن الموظف ' . $next_approver_id . ' ليس لديه بريد مسجل.',
                'sent_at' => date('Y-m-d H:i:s')
            ]);
            return false;
        }

        $applicant = $this->db->select('subscriber_name')->where('employee_id', $applicant_id)->get('emp1')->row_array();
        $recipient_name = $recipient['subscriber_name'] ?? 'الموظف';
        $applicant_name = $applicant['subscriber_name'] ?? 'موظف';

        // 3. BUILD TEXT
        if ($next_approver_id == $applicant_id) {
            if ($status === 'Rejected') {
                $headline = "نعتذر، تم رفض طلب الانتداب";
                $body_text = "نأسف لإبلاغك بأنه قد تم رفض طلب الانتداب الخاص بك من قبل المسؤول المختص.";
            } else {
                $headline = "تم اعتماد الانتداب النهائي";
                $body_text = "يسعدنا إبلاغك بأنه تم الاعتماد النهائي لطلب الانتداب الخاص بك، وهو مسجل الآن في النظام.";
            }
        } else {
            if ($status === 'Returned') {
                $headline = "طلب انتداب مُعاد للمراجعة";
                $body_text = "تم إرجاع طلب انتداب مقدم من <b>{$applicant_name}</b> لمراجعته واستكماله بناءً على ملاحظات الرفض.";
            } else {
                $headline = "طلب انتداب بانتظار اعتمادك";
                $body_text = "يوجد طلب انتداب مقدم من <b>{$applicant_name}</b> بانتظار مراجعتك واعتمادك لإكمال سلسلة الموافقات المالية والإدارية.";
            }
        }

        $details = $this->get_mandate_details_for_email($req_id);

        // Pass the custom link as the 8th parameter
        $html = $this->build_beautiful_email_template($recipient_name, $headline, $body_text, $details, $req_id, $action_text, $status_color, $custom_link);
        
        return $this->send_html_email($recipient['email'], $email_subject, $html, $req_id);
    }
    /**
     * Highly Detailed & Beautiful Email Template Builder (FIXED PHP CONCATENATION)
     */
    public function build_beautiful_email_template($recipient_name, $headline, $body_text, $details_array, $order_id, $action_text, $main_color = "#001f3f", $custom_action_link = null) {
        
        // If a custom link is provided, use it. Otherwise default to the standard view_request link.
     //   $action_link = $custom_action_link ? $custom_action_link : "https://services.marsoom.net/hr/users1/view_request/" . $order_id;
        $dept = isset($details_array['القسم']) ? ' — ' . $details_array['القسم'] : '';

        // FIX: Build the dynamic details HTML string before returning!
        $dynamic_details_html = '';
        foreach ($details_array as $label => $value) {
            if (!in_array($label, ['رقم الطلب', 'الموظف', 'التاريخ', 'حالة الطلب'])) {
                $dynamic_details_html .= '<b>' . $label . ':</b> ' . $value . '<br>';
            }
        }

        return '
        <!doctype html>
        <html lang="ar" dir="rtl">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width,initial-scale=1">
          <style>
            body, table, td, div, p, a, span, h1, h2, h3, h4 {
              font-family: "Segoe UI", Tahoma, Arial, sans-serif !important;
            }
          </style>
        </head>

        <body style="margin:0;padding:0;background:#f3f6fb;font-family:Segoe UI,Tahoma,Arial,sans-serif;">

          <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
            طلب جديد رقم ' . $order_id . '
          </div>

          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6fb;padding:24px 0;">
            <tr>
              <td align="center">

                <table role="presentation" width="640" cellpadding="0" cellspacing="0"
                       style="width:640px;max-width:640px;background:#ffffff;border:1px solid #e7eef8;border-radius:16px;overflow:hidden;">

                  <tr>
                    <td style="background:#001f3f;padding:18px 20px;">
                      <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                          <td style="color:#fff;font-size:14px;font-weight:800;">
                            Request #' . $order_id . '
                          </td>
                          <td align="left" style="color:#fff;font-size:16px;font-weight:900;">
                            Marsoom • HR
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" style="padding-top:8px;color:#cfe2ff;font-size:12px;">
                            إشعار بطلب جديد - يرجى المراجعة
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:18px 20px 8px 20px;">
                      <div style="font-size:20px;font-weight:900;color:#0b1b2a;line-height:1.5;">
                        ' . $headline . '
                      </div>
                      <div style="margin-top:6px;font-size:13px;color:#49627a;font-weight:800;">
                        المستلم: ' . $recipient_name . $dept . '
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:0 20px 14px 20px;">
                      <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                          <td style="padding:10px;border:1px solid #e7eef8;border-radius:12px;background:#f7fbff;">
                            <div style="font-size:12px;color:#49627a;font-weight:900;">رقم الطلب</div>
                            <div style="font-size:14px;color:#0b1b2a;font-weight:900;padding-top:4px;">' . $order_id . '</div>
                          </td>
                          <td width="10"></td>
                          <td style="padding:10px;border:1px solid #ffe4bf;border-radius:12px;background:#fff7ea;">
                            <div style="font-size:12px;color:#7a4a00;font-weight:900;">تاريخ الطلب</div>
                            <div style="font-size:14px;color:#0b1b2a;font-weight:900;padding-top:4px;">' . date('Y-m-d') . '</div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:0 20px 14px 20px;">
                      <div style="border:1px solid #e7eef8;border-radius:12px;background:#ffffff;padding:12px;">
                        <div style="font-size:13px;color:#49627a;font-weight:900;margin-bottom:6px;">تفاصيل الطلب</div>
                        <div style="font-size:14px;color:#0b1b2a;line-height:1.9;">
                          ' . $dynamic_details_html . '
                        </div>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:0 20px 20px 20px;">
                      <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                             style="border:1px dashed #ffcf8b;border-radius:12px;background:#fff9ef;">
                        <tr>
                          <td style="padding:14px;">
                            <div style="font-size:13px;color:#7a4a00;font-weight:900;margin-bottom:10px;">
                              الإجراء المطلوب
                            </div>

                            <div style="font-size:13px;color:#0b1b2a;line-height:1.9;margin-bottom:12px;">
                              ' . $body_text . '
                            </div>

                            <table role="presentation" cellpadding="0" cellspacing="0">
                              <tr>
                                <td style="background:#ff8c00;border-radius:10px;">
                                  <a href="https://services.marsoom.net/hr" style="display:inline-block;padding:10px 14px;font-size:14px;font-weight:900;color:#001f3f;text-decoration:none;">
                                    فتح نظام الموارد البشرية
                                  </a>
                                </td>
                                <td width="10"></td>
                                
                              </tr>
                            </table>

                            <div style="font-size:12px;color:#7a4a00;line-height:1.8;margin-top:10px;">
                              بعد الدخول: <b>لوحة الطلبات</b> ← اختر الطلب ← <b>مراجعة الطلب</b>.
                            </div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td style="background:#f7fbff;border-top:1px solid #e7eef8;padding:12px 20px;">
                      <div style="font-size:11px;color:#6b7c8f;line-height:1.7;">
                        هذه رسالة تلقائية من نظام مرسوم. الرجاء عدم الرد على هذا البريد.
                      </div>
                    </td>
                  </tr>

                </table>

              </td>
            </tr>
          </table>
        </body>
        </html>';
    }

    /**
     * Trigger email on NEW request creation
     */
    /**
     * Trigger email on NEW request creation
     */
    public function trigger_new_request_email($emp_id, $request_type, $post_data) {
        // Fetch the ID of the request just created
        $this->db->select_max('id');
        $this->db->where('emp_id', $emp_id);
        $latest_order = $this->db->get('orders_emp')->row_array();
        $order_id = $latest_order ? $latest_order['id'] : '0';

        $approver_id = null;
        
        // RULE 1: Delegate is manually selected and comes first (Outside hierarchy)
        if ($request_type === 'vacation' && !empty($post_data['vac']['delegation_employee_id'])) {
            $approver_id = $post_data['vac']['delegation_employee_id'];
        } else {
            // RULE 2: If no delegate, the official hierarchy starts with the Direct Manager
            $approver_id = $this->get_hierarchy_manager($emp_id);
        }

        if (!$approver_id) { $approver_id = '2774'; } // Fallback to HR

        $approver = $this->db->select('email, subscriber_name')->where('employee_id', $approver_id)->get('emp1')->row_array();
        $applicant = $this->db->select('subscriber_name')->where('employee_id', $emp_id)->get('emp1')->row_array();

        if ($approver && !empty(trim($approver['email'])) && $applicant) {
            $approver_name = $approver['subscriber_name'] ?? 'المدير المباشر';
            $applicant_name = $applicant['subscriber_name'] ?? 'موظف';
            
            $details = $this->get_order_details_for_email($order_id);
            if (!$details) {
                $details = ['الموظف' => $applicant_name, 'الطلب' => $request_type]; // Fallback
            }

            $html = $this->build_beautiful_email_template(
                $approver_name, 
                "طلب جديد بانتظار اعتمادك", 
                "لقد قام الموظف <b>{$applicant_name}</b> بتقديم طلب جديد، وهو بانتظار مراجعتك واعتمادك.", 
                $details, 
                $order_id, 
                "عرض واعتماد الطلب", 
                "#FF8C00" 
            );

            $this->send_html_email($approver['email'], "طلب جديد بحاجة لاعتمادك - Marsoom HR", $html, $order_id);
        } else {
            // LOG IF MISSING EMAIL
            $this->db->insert('email_logs', [
                'order_id' => $order_id,
                'recipient_email' => $approver_id . ' (NO EMAIL FOUND)',
                'subject' => 'طلب جديد بحاجة لاعتمادك - Marsoom HR',
                'status' => 'failed',
                'error_message' => 'لم يتم إرسال الإيميل لأن الموظف رقم ' . $approver_id . ' ليس لديه بريد إلكتروني مسجل في النظام.',
                'sent_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Trigger email for NEXT approvals in the chain (Bulletproof Routing)
     */
    public function trigger_next_approval_email($order_id, $current_approver_id = null) {
        $this->db->where('id', $order_id);
        $order = $this->db->get('orders_emp')->row_array();
        
        if (!$order) return false;

        $applicant_id = $order['emp_id'];
        $status = (int)$order['status'];
        $responsible_employee = $order['responsible_employee']; // Trust the database!
        
        $next_approver_id = null;
        $email_subject = "طلب بحاجة لاعتمادك المباشر - Marsoom HR";
        $action_text = "عرض التفاصيل والاعتماد";
        $status_color = "#FF8C00";

        // 1. DYNAMIC ROUTING BASED ON EXACT DATABASE STATE
        if ($status === 1 || $status === 0) {
            // The system perfectly calculated the next person! 
            // (e.g. After Delegate approved, DB set it to Manager '2803')
            $next_approver_id = $responsible_employee;
            
            if (empty($next_approver_id)) {
                $next_approver_id = '2774'; // Absolute fallback
            }
        } elseif ($status === 2) {
            // Fully Approved -> Email Applicant
            $next_approver_id = $applicant_id;
            $email_subject = "تم اعتماد طلبك بنجاح ✅ - Marsoom HR";
            $action_text = "الدخول لملف الطلب";
            $status_color = "#28a745";
        } else {
            return false;
        }

        // 2. FETCH RECIPIENT AND SEND
        $recipient = $this->db->select('email, subscriber_name')->where('employee_id', $next_approver_id)->get('emp1')->row_array();
        
        // CRITICAL FIX: Explicitly log it to the database if the user has no email!
        if (!$recipient || empty(trim($recipient['email']))) {
            $this->db->insert('email_logs', [
                'order_id' => $order_id,
                'recipient_email' => $next_approver_id . ' (NO EMAIL FOUND)',
                'subject' => $email_subject,
                'status' => 'failed',
                'error_message' => 'لم يتم إرسال الإيميل لأن الموظف رقم ' . $next_approver_id . ' ليس لديه بريد إلكتروني مسجل في النظام.',
                'sent_at' => date('Y-m-d H:i:s')
            ]);
            return false;
        }

        $applicant = $this->db->select('subscriber_name')->where('employee_id', $applicant_id)->get('emp1')->row_array();
        $recipient_name = $recipient['subscriber_name'] ?? 'الموظف';
        $applicant_name = $applicant['subscriber_name'] ?? 'موظف';

        if ($next_approver_id == $applicant_id) {
            $headline = "تم اعتماد طلبك النهائي";
            $body_text = "يسعدنا إبلاغك بأنه تم الاعتماد النهائي لطلبك، وهو الآن مسجل رسمياً في النظام.";
        } else {
            $headline = "طلب بانتظار اعتمادك";
            $body_text = "يوجد طلب مقدم من <b>{$applicant_name}</b> بانتظار مراجعتك واعتمادك لإكمال سلسلة الموافقات في النظام.";
        }

        $details = $this->get_order_details_for_email($order_id);

        $html = $this->build_beautiful_email_template($recipient_name, $headline, $body_text, $details, $order_id, $action_text, $status_color);
        
        return $this->send_html_email($recipient['email'], $email_subject, $html, $order_id);
    }
// ====================================================
    // PASTE THIS AT THE BOTTOM OF hr_model.php
    // ====================================================

    // 1. Get List of Employees (Fixes 500 Error for 1127)
    public function get_employeess() {
        return $this->db->select('employee_id, subscriber_name, n1 as department, job_tag')
                        ->get('emp1')
                        ->result_array();
    }
    // --- HISTORY LOGGING FUNCTIONS ---
    
    // 1. Log an action
    public function log_history($req_id, $user_id, $action_name) {
        $this->db->insert('id_renewal_history', [
            'req_id' => $req_id,
            'action_by' => $user_id,
            'action_name' => $action_name
        ]);
    }

    // 2. Get history for a specific request (with user names)
    public function get_request_history($req_id) {
        $this->db->select('h.*, e.subscriber_name, e.job_tag');
        $this->db->from('id_renewal_history h');
        $this->db->join('emp1 e', 'e.employee_id = h.action_by', 'left');
        $this->db->where('h.req_id', $req_id);
        $this->db->order_by('h.id', 'ASC'); // Oldest first
        return $this->db->get()->result_array();
    }
    // 2. Get Single Request Details
    public function get_renewal_request_by_id($id) {
        $this->db->select('r.*, e.subscriber_name, e.n1 as department, e.joining_date, e.job_tag');
        $this->db->from('id_renewal_requests r');
        $this->db->join('emp1 e', 'e.employee_id = r.emp_id', 'left');
        $this->db->where('r.id', $id);
        return $this->db->get()->row_array();
    }
    
    // 3. Update Function
    public function update_renewal_request($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('id_renewal_requests', $data);
    }

    // 4. Update Official Employee Table (Final Step)
    public function update_employee_iqama_expiry($emp_id) {
        $new_date = date('Y-m-d', strtotime('+1 year'));
        $this->db->where('employee_id', $emp_id);
        return $this->db->update('emp1', ['Iqama_expiry_date' => $new_date, 'id_expiry' => $new_date]);
    }
public function get_employees_by_location($location)
{
    if (empty($location)) {
        return [];
    }
    
    // Selects in the format needed by the attendance dropdown ('username', 'name')
    $this->db->select('employee_id as username, subscriber_name as name, status');
    $this->db->from('emp1');
    $this->db->where('location', $location);
    $this->db->where('status !=', 'deleted'); // Exclude deleted employees
    $this->db->order_by('subscriber_name', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}

/**
 * Checks if a specific employee belongs to a specific location.
 * @param string $employee_id
 * @param string $location
 * @return bool
 */
public function is_employee_in_location($employee_id, $location)
{
    if (empty($employee_id) || empty($location)) {
        return false;
    }
    
    $this->db->select('id');
    $this->db->from('emp1');
    $this->db->where('employee_id', $employee_id);
    $this->db->where('location', $location);
    $this->db->limit(1);
    
    $query = $this->db->get();
    return $query->num_rows() > 0;
}
private function _get_attendance_tables() {
    $tables = [];
    // This query finds all tables with names like 'attendance_logs...'
    $query = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'attendance_logs%'");
    if ($query) {
        foreach ($query->result_array() as $row) {
            $tables[] = $row['TABLE_NAME'];
        }
    }
    // Make sure the main table is always included
    if (!in_array('attendance_logs', $tables)) {
        array_unshift($tables, 'attendance_logs');
    }
    return array_unique($tables);
}

/**
 * Fetches the first and last punch for each employee on a given date.
 * @param string $date The date in 'Y-m-d' format.
 * @return array
 */
// In hr_model.php

public function get_daily_first_last_punches($date, $employee_id = null)
{
    $tables = $this->_get_attendance_tables();
    if (empty($tables)) return [];

    $unions = [];
    foreach ($tables as $table) {
        // Build the WHERE clause for each subquery
        $where_clause = "DATE(punch_time) = " . $this->db->escape($date);
        if (!empty($employee_id)) {
            $where_clause .= " AND emp_code = " . $this->db->escape($employee_id);
        }

        if ($this->db->field_exists('id', $table)) {
            $unions[] = sprintf("(SELECT id, emp_code, punch_time FROM `%s` WHERE %s)", $table, $where_clause);
        } else {
            $unions[] = sprintf("(SELECT NULL as id, emp_code, punch_time FROM `%s` WHERE %s)", $table, $where_clause);
        }
    }
    $union_sql = implode(" UNION ALL ", $unions);

    // The rest of the query remains the same
    $sql = "
        WITH RankedPunches AS (
            SELECT
                id, emp_code, punch_time,
                ROW_NUMBER() OVER (PARTITION BY emp_code ORDER BY punch_time ASC) as rn_asc,
                ROW_NUMBER() OVER (PARTITION BY emp_code ORDER BY punch_time DESC) as rn_desc
            FROM ($union_sql) as AllPunches
        )
        SELECT
            rp.emp_code,
            e.subscriber_name,
            MAX(CASE WHEN rp.rn_asc = 1 THEN rp.id END) as first_punch_id,
            MAX(CASE WHEN rp.rn_asc = 1 THEN rp.punch_time END) as first_punch_time,
            MAX(CASE WHEN rp.rn_desc = 1 THEN rp.id END) as last_punch_id,
            MAX(CASE WHEN rp.rn_desc = 1 THEN rp.punch_time END) as last_punch_time
        FROM RankedPunches rp
        JOIN emp1 e ON e.employee_id = rp.emp_code
        WHERE rp.rn_asc = 1 OR rp.rn_desc = 1
        GROUP BY rp.emp_code, e.subscriber_name
        ORDER BY e.subscriber_name ASC
    ";
    
    $query = $this->db->query($sql);
    return $query->result_array();
}


// In hr_model.php

public function get_saturday_assignments_for_month($start_date, $end_date)
{
    $this->db->select('swa.id, swa.employee_id, swa.saturday_date, e.subscriber_name');
    $this->db->from('saturday_work_assignments swa');
    $this->db->join('emp1 e', 'e.employee_id = swa.employee_id', 'left');
    $this->db->where('swa.saturday_date >=', $start_date);
    $this->db->where('swa.saturday_date <=', $end_date);
    return $this->db->get()->result_array();
}

public function add_saturday_assignments($employee_ids, $saturday_date, $assigner_id)
{
    $batch_data = [];
    foreach ($employee_ids as $emp_id) {
        $batch_data[] = [
            'employee_id'   => $emp_id,
            'saturday_date' => $saturday_date,
            'assigned_by'   => $assigner_id
        ];
    }
    // 'ignore' prevents errors if a duplicate is inserted
    return $this->db->insert_batch('saturday_work_assignments', $batch_data, true);
}

public function remove_saturday_assignment($employee_id, $saturday_date)
{
    $this->db->where('employee_id', $employee_id);
    $this->db->where('saturday_date', $saturday_date);
    return $this->db->delete('saturday_work_assignments');
}

// Helper function we will use in Part 2
public function is_mandatory_saturday($employee_id, $date)
{
    // Add debugging
    log_message('debug', "Checking mandatory Saturday for employee: $employee_id, date: $date");
    
    $this->db->where('employee_id', $employee_id);
    $this->db->where('saturday_date', $date);
    $query = $this->db->get('saturday_work_assignments');
    
    $result = $query->num_rows() > 0;
    
    // Log the SQL query and result
    log_message('debug', "SQL: " . $this->db->last_query());
    log_message('debug', "Found " . $query->num_rows() . " records");
    log_message('debug', "Result: " . ($result ? 'MANDATORY' : 'NOT MANDATORY'));
    
    return $result;
}
public function update_punch_record($record_id, $new_punch_time)
{
    $tables = $this->_get_attendance_tables();
    foreach ($tables as $table) {
        $this->db->where('id', $record_id);
        $this->db->update($table, ['punch_time' => $new_punch_time]);
        // If a row was updated, we found the right table and can stop
        if ($this->db->affected_rows() > 0) {
            return true;
        }
    }
    return false; // Record ID was not found in any table
}

public function get_all_filtered_requests_for_export($is_hr_user, $logged_in_user_id)
{
    // This re-uses all your existing filter logic
    $this->_get_datatables_query_orders($is_hr_user, $logged_in_user_id);
    
    // The key difference: NO LIMIT or OFFSET
    $query = $this->db->get();
    return $query->result_array();
}
public function update_employee_leave_balance($employee_id, $new_balance)
{
    if (empty($employee_id) || !is_numeric($new_balance)) {
        return false;
    }

    $this->db->where('employee_id', $employee_id);
    $this->db->where('leave_type_slug', 'annual'); // We are only editing the annual leave
    $this->db->where('year', date('Y')); // Target the current year's record

    // For simplicity in this form, we set the allotted and remaining balance to the same new value,
    // assuming consumed balance is not a factor here.
    $data = [
        'remaining_balance' => (float)$new_balance,
        'balance_allotted'  => (float)$new_balance
    ];

    return $this->db->update('employee_leave_balances', $data);
}

public function get_half_day_vacations_for_employees($employee_ids, $start_date, $end_date)
{
    if (empty($employee_ids)) return [];
    
    $this->db->select('emp_id, COUNT(*) as half_day_count');
    $this->db->from('orders_emp');
    $this->db->where_in('emp_id', $employee_ids);
    $this->db->where('type', 5);
    $this->db->where('status', '2');
    $this->db->where('vac_half_date IS NOT NULL');
    $this->db->where("vac_half_date BETWEEN '$start_date' AND '$end_date'");
    $this->db->group_by('emp_id');
    
    $query = $this->db->get();
    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->emp_id] = (int)$row->half_day_count;
    }
    return $map;
}


private function _calculate_violations_for_period($employee_id, $start_date_str, $end_date_str) {
        if (empty($employee_id) || empty($start_date_str) || empty($end_date_str) || strtotime($start_date_str) > strtotime($end_date_str)) {
            // Return default zero values if input is invalid
            return ['absence_days' => 0, 'lateness_minutes' => 0, 'early_departure_minutes' => 0];
        }

        $start_date = new DateTime($start_date_str);
        $end_date   = new DateTime($end_date_str);
        // Ensure end date includes the full day for range checks
        $end_date_for_query = (clone $end_date)->setTime(23, 59, 59);

        // --- 1. Get Employee Work Rules & Saturday Assignments ---
        $rules = $this->db->get_where('work_restrictions', ['emp_id' => $employee_id])->row();
        // Provide defaults if rules are not found
        $default_flex_end_time = $rules->last_punch ?? '08:30:00'; // Lateness threshold time
        $default_working_hours = (float)($rules->working_hours ?? 8.0); // Required work duration

        // --- 2. Fetch Raw Punches ---
        $tables = $this->_get_attendance_tables(); // Use your existing helper
        $punches_by_day = [];
        if (!empty($tables)) {
            $unions = [];
            foreach ($tables as $table) {
                // Ensure table exists and has necessary columns before adding to UNION
                if ($this->db->table_exists($table) && $this->db->field_exists('emp_code', $table) && $this->db->field_exists('punch_time', $table)) {
                     $unions[] = sprintf(
                        "(SELECT punch_time FROM `%s` WHERE emp_code = %s AND punch_time BETWEEN %s AND %s)",
                        $table,
                        $this->db->escape($employee_id),
                        $this->db->escape($start_date->format('Y-m-d H:i:s')),
                        $this->db->escape($end_date_for_query->format('Y-m-d H:i:s'))
                    );
                }
            }
            if (!empty($unions)) {
                $union_sql = implode(" UNION ALL ", $unions);
                $punches_query = $this->db->query(
                    "SELECT DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch, COUNT(punch_time) as punch_count
                     FROM ({$union_sql}) as punches
                     GROUP BY punch_date"
                );
                foreach ($punches_query->result() as $row) {
                    $punches_by_day[$row->punch_date] = $row;
                }
            }
        }

        // --- 3. Fetch Approved Corrections, Leaves, and Public Holidays ---
        $this->db->from('orders_emp');
        $this->db->where('emp_id', $employee_id);
        $this->db->where('status', '2'); // Only approved
        $this->db->group_start(); // Group conditions for corrections and leaves
            $this->db->where('type', 2)->where("correction_date BETWEEN '{$start_date_str}' AND '{$end_date_str}'"); // Corrections within period
            $this->db->or_where('type', 5)->where("((vac_start <= '{$end_date_str}' AND vac_end >= '{$start_date_str}') OR (vac_half_date BETWEEN '{$start_date_str}' AND '{$end_date_str}'))", NULL, FALSE); // Leaves overlapping period
        $this->db->group_end();
        $requests = $this->db->get()->result();

        $corrections_by_day = [];
        $leaves_by_day = []; // Stores true for full day, 'am'/'pm' for half day
        foreach ($requests as $req) {
            if ($req->type == 2) {
                $corrections_by_day[$req->correction_date] = true;
            } elseif ($req->type == 5) {
                if (!empty($req->vac_start) && !empty($req->vac_end)) {
                    $leave_start_dt = new DateTime(max($req->vac_start, $start_date_str));
                    $leave_end_dt = new DateTime(min($req->vac_end, $end_date_str));
                    $leave_end_dt->modify('+1 day'); // Include end date
                    $period = new DatePeriod($leave_start_dt, new DateInterval('P1D'), $leave_end_dt);
                    foreach ($period as $day) {
                        $leaves_by_day[$day->format('Y-m-d')] = true; // Mark as full day leave initially
                    }
                }
                if (!empty($req->vac_half_date) && $req->vac_half_date >= $start_date_str && $req->vac_half_date <= $end_date_str) {
                    $leaves_by_day[$req->vac_half_date] = $req->vac_half_period ?? 'am'; // Mark specifically as half day
                }
            }
        }

        $public_holidays = $this->get_holidays_as_flat_array($start_date_str, $end_date_str); // Fetch holidays

        // --- 4. Initialize Counters ---
        $total_absence_days = 0;
        $total_lateness_minutes = 0;
        $total_early_departure_minutes = 0;

        // --- 5. Iterate Through Dates in the Period ---
        $interval = new DateInterval('P1D');
        // Modify end date for the period to be inclusive
        $period_end_date = (clone $end_date)->modify('+1 day');
        $date_range = new DatePeriod($start_date, $interval, $period_end_date);

        foreach ($date_range as $day) {
            $current_date_str = $day->format('Y-m-d');
            $day_of_week_N = $day->format('N'); // 1 (Mon) to 7 (Sun)

            // --- Apply Exclusions ---
            if (isset($corrections_by_day[$current_date_str])) {
                continue; // Skip day if there's an approved correction
            }
            if (in_array($current_date_str, $public_holidays)) {
                continue; // Skip public holidays
            }
            if (isset($leaves_by_day[$current_date_str]) && $leaves_by_day[$current_date_str] === true) {
                continue; // Skip full day leaves
            }

            $is_weekend = ($day_of_week_N == 5); // Only Friday is weekend by default
            $is_saturday = ($day_of_week_N == 6);
            $is_mandatory_saturday = $is_saturday && $this->is_mandatory_saturday($employee_id, $current_date_str);

            if ($is_weekend || ($is_saturday && !$is_mandatory_saturday)) {
                continue; // Skip normal weekends
            }

            // --- Determine Rules for the Day ---
            $lateness_threshold_time_str = $default_flex_end_time;
            $working_hours_today = $default_working_hours;
            if ($is_mandatory_saturday) {
                // Apply Saturday specific rules if needed
                // Example: $lateness_threshold_time_str = '09:00:00';
                 $working_hours_today = 6.0; // Example: 6 hours on Saturday
            }

            // Adjust for half-day leave
            $is_half_day_leave = isset($leaves_by_day[$current_date_str]) && $leaves_by_day[$current_date_str] !== true;
            if ($is_half_day_leave) {
                $working_hours_today /= 2;
                // Adjust lateness threshold if it's an AM leave
                if ($leaves_by_day[$current_date_str] === 'am' || $leaves_by_day[$current_date_str] === 'صباحي') {
                    // Example: If work resumes at 1 PM after AM leave
                     $lateness_threshold_time_str = '13:00:00';
                }
            }


            // --- Check Attendance and Calculate Violations ---
            if (!isset($punches_by_day[$current_date_str])) {
                // No punches recorded for a required working day/part-day
                if (!$is_half_day_leave) { // Count as full day absence only if not a half-day leave
                   $total_absence_days++;
                } else {
                    // Decide how to handle absence on a half-day (e.g., count as 0.5 absence?)
                    // For now, we don't count it as a full absence day.
                }
            } else {
                // Punches exist
                $punch = $punches_by_day[$current_date_str];
                $first_punch_dt = !empty($punch->first_punch) ? new DateTime($punch->first_punch) : null;
                $last_punch_dt = !empty($punch->last_punch) ? new DateTime($punch->last_punch) : null;

                // Calculate Lateness
                if ($first_punch_dt) {
                    $lateness_cutoff_dt = new DateTime($first_punch_dt->format('Y-m-d') . ' ' . $lateness_threshold_time_str);
                    if ($first_punch_dt > $lateness_cutoff_dt) {
                        $late_minutes = round(($first_punch_dt->getTimestamp() - $lateness_cutoff_dt->getTimestamp()) / 60);
                        if ($late_minutes > 0) {
                            $total_lateness_minutes += $late_minutes;
                        }
                    }
                }

                // Calculate Early Departure (only if both punches exist and are different)
                if ($first_punch_dt && $last_punch_dt && $first_punch_dt != $last_punch_dt) {
                    $required_seconds = $working_hours_today * 3600;
                    $required_end_dt = (clone $first_punch_dt)->modify('+' . round($required_seconds) . ' seconds');

                    // If it's a PM half-day leave, the required end time is based on the normal start, not the punch time.
                    // This logic gets complex. For simplicity, we'll base it on the first punch for now.
                    // A more accurate calculation might need the official shift start time.

                    if ($last_punch_dt < $required_end_dt) {
                        $early_minutes = round(($required_end_dt->getTimestamp() - $last_punch_dt->getTimestamp()) / 60);
                        if ($early_minutes > 0) {
                            $total_early_departure_minutes += $early_minutes;
                        }
                    }
                }
            }
        } // End date loop

        return [
            'absence_days' => $total_absence_days,
            'lateness_minutes' => $total_lateness_minutes,
            'early_departure_minutes' => $total_early_departure_minutes
        ];
    }
public function get_direct_manager_details($employee_id)
{
    if (empty($employee_id)) {
        return null;
    }

    $cols = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7'];
    $this->db->select(implode(',', $cols));
    $this->db->from('organizational_structure');

    // Find rows where the employee exists in any level
    $this->db->group_start();
    foreach ($cols as $col) {
        $this->db->or_where($col, $employee_id);
    }
    $this->db->group_end();

    $query = $this->db->get();
    $rows = $query->result_array();

    if (empty($rows)) {
        log_message('debug', "No org structure row found for employee ID: " . $employee_id);
        return null; // Employee not found in any structure
    }

    $deepest_level_index = -1; // Index of the column (0=n1, 1=n2, etc.)
    $structure_row_at_deepest = null;

    // Find the deepest level the employee appears at (their primary position)
    foreach ($rows as $row) {
        foreach ($cols as $index => $col_name) {
            if (isset($row[$col_name]) && $row[$col_name] == $employee_id) {
                if ($index > $deepest_level_index) {
                    $deepest_level_index = $index;
                    $structure_row_at_deepest = $row;
                }
                break; // Found in this row, move to next row
            }
        }
    }

    // Check if found and not at the top level (n1)
    if ($structure_row_at_deepest === null || $deepest_level_index <= 0) {
        log_message('debug', "Employee ID " . $employee_id . " is top level or not positioned correctly.");
        return null; // Top level or error
    }

    // Get the manager's ID from the previous level column
    $manager_col_index = $deepest_level_index - 1;
    $manager_id = $structure_row_at_deepest[$cols[$manager_col_index]] ?? null;

    if (empty($manager_id)) {
        log_message('debug', "Manager ID not found at index " . $manager_col_index . " for employee ID: " . $employee_id);
        return null; // No manager defined at the level above
    }

    // Get the manager's name from the emp1 table
    $this->db->select('subscriber_name');
    $this->db->from('emp1');
    $this->db->where('employee_id', $manager_id);
    $manager_query = $this->db->get();
    $manager_details = $manager_query->row_array();

    if ($manager_details) {
        return [
            'manager_id'   => $manager_id,
            'manager_name' => $manager_details['subscriber_name']
        ];
    } else {
         log_message('debug', "Manager details not found in emp1 for manager ID: " . $manager_id);
        // Return ID only if name not found, but indicate name is missing
         return [
            'manager_id' => $manager_id,
            'manager_name' => 'غير متوفر (' . $manager_id.')' // Name Unavailable
         ];
    }
}


// In hr_model.php

// In hr_model.php

// 1. UPDATE THE FUNCTION SIGNATURE TO ACCEPT THE CUSTOM DATE
public function get_end_of_service_data($employee_id, $include_deleted = false, $custom_deduction_start_date = null)
{
    $employee_id = trim($employee_id);

    // --- Fetch employee and resignation data ---
    $this->db->select("id, employee_id, subscriber_name, profession, joining_date, total_salary, base_salary, housing_allowance, nationality, other_allowances, n4 as transportation_allowance");
    if (!$include_deleted) {
        $this->db->where('status !=', 'deleted');
    }
    $this->db->where('employee_id', $employee_id);
    $emp = $this->db->get('emp1')->row_array();

    // Added "date as request_date" to fetch the day the HR made the request
    $this->db->select("id as resignation_order_id, reason_for_resignation, date_of_the_last_working, date as request_date");
    $this->db->where('emp_id', $employee_id);
    $this->db->where('type', 1);
    $this->db->where_in('status', [2, 11]); // Approved or In Clearance
    $this->db->order_by('id', 'DESC')->limit(1);
    $order = $this->db->get('orders_emp')->row_array();

    if (!$emp || !$order || empty($order['date_of_the_last_working'])) {
        log_message('error', "EOS Data Fetch Error: Employee or approved resignation with last day not found for ID: " . $employee_id);
        return null;
    }

    // --- Determine Calendar Month Period ---
    $last_working_day = $order['date_of_the_last_working'];
    try {
        $last_working_day_dt = new DateTime($last_working_day);
        $month_start_date_str = $last_working_day_dt->format('Y-m-01'); // First day of the month
        $calculation_end_date_str = $last_working_day_dt->format('Y-m-d'); // Last working day
    } catch (Exception $e) {
        log_message('error', "Error parsing last working day: " . $last_working_day . " - " . $e->getMessage());
        return null; // Cannot proceed without a valid date
    }
    
    // --- Recalculate Relevant Working Days (For legacy reference) ---
    $relevant_working_days = 0;
    $leaves_and_holidays_map = []; 

    $public_holidays = $this->get_holidays_as_flat_array($month_start_date_str, $calculation_end_date_str);
    foreach($public_holidays as $h_date) {
        $leaves_and_holidays_map[$h_date] = 'holiday';
    }

    $this->db->select('vac_start, vac_end');
    $this->db->from('orders_emp');
    $this->db->where('emp_id', $employee_id);
    $this->db->where('type', 5);
    $this->db->where('status', '2');
    $this->db->where('vac_half_date IS NULL');
    $this->db->where('vac_start <=', $calculation_end_date_str);
    $this->db->where('vac_end >=', $month_start_date_str);
    $full_leaves = $this->db->get()->result();

    foreach ($full_leaves as $leave) {
        try {
            $leave_period_start = new DateTime(max($leave->vac_start, $month_start_date_str));
            $leave_period_end = new DateTime(min($leave->vac_end, $calculation_end_date_str));
            $leave_period_end->modify('+1 day');
            $leave_interval = new DateInterval('P1D');
            $leave_range = new DatePeriod($leave_period_start, $leave_interval, $leave_period_end);
            foreach ($leave_range as $leave_day) {
                $leaves_and_holidays_map[$leave_day->format('Y-m-d')] = 'leave';
            }
        } catch (Exception $e) {
             log_message('error', "Error processing leave dates: " . $leave->vac_start . " to " . $leave->vac_end);
        }
    }
     try {
        $period_start = new DateTime($month_start_date_str);
        $period_end = new DateTime($calculation_end_date_str);
        $period_end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($period_start, $interval, $period_end);

        foreach ($date_range as $day) {
            $current_date_str = $day->format('Y-m-d');
            $day_of_week_N = $day->format('N');
            $is_friday = ($day_of_week_N == 5);
            $is_saturday = ($day_of_week_N == 6);
            $is_mandatory_saturday = $is_saturday && $this->is_mandatory_saturday($employee_id, $current_date_str);
            if ($is_friday || ($is_saturday && !$is_mandatory_saturday) || (isset($leaves_and_holidays_map[$current_date_str]) && $leaves_and_holidays_map[$current_date_str] == 'leave')) {
                 continue;
            }
            $relevant_working_days++;
        }
    } catch (Exception $e) {
        log_message('error', "Error creating date period for salary calculation: " . $e->getMessage());
        $relevant_working_days = 0;
    }

    // --- Fetch Leave Balance ---
    // --- Fetch Leave Balance ---
    $this->db->select('remaining_balance');
    $this->db->where('employee_id', $employee_id)->where('leave_type_slug', 'annual')->where('year', date('Y', strtotime($calculation_end_date_str)));
    $balance = $this->db->get('employee_leave_balances')->row();
    $leave_balance = $balance ? (float)$balance->remaining_balance : 0;

    // --- FIX: Adjust leave balance for late resignation requests ---
    

    // =================================================================================
    // --- 2. UPDATE THIS ENTIRE BLOCK TO HANDLE THE CUSTOM DATE ---
    // =================================================================================
    $last_working_dt_calc = new DateTime($calculation_end_date_str);
    
    // NEW LOGIC: Use custom date if HR provided it
    if (!empty($custom_deduction_start_date)) {
        $violation_start_date_str = date('Y-m-d', strtotime($custom_deduction_start_date));
        log_message('debug', "EOS: Custom deduction start date used: " . $violation_start_date_str);
    } else {
        // 1. Set Standard Payroll Start (16th of last month)
        $violation_start_date_str = $last_working_dt_calc->modify('first day of last month')->format('Y-m-16');

        // 2. Check Joining Date: If employee joined AFTER the standard payroll start, use Joining Date
        if (!empty($emp['joining_date'])) {
            $joining_dt = new DateTime($emp['joining_date']);
            $standard_start_dt = new DateTime($violation_start_date_str);

            if ($joining_dt > $standard_start_dt) {
                $violation_start_date_str = $joining_dt->format('Y-m-d');
            }
        }
    }

    // Safety check: Start date cannot be after end date
    if ($violation_start_date_str > $calculation_end_date_str) {
        $violation_start_date_str = $calculation_end_date_str;
    }

    // --- Call Violation Calculation ---
    $calculated_violations = $this->_calculate_eos_violations_for_calendar_month(
        $employee_id,
        $violation_start_date_str,     
        $calculation_end_date_str      
    );
    // =================================================================================
    // --- END DATE CALCULATION BLOCK ---
    // =================================================================================

    // ... [Keep the rest of your original function EXACTLY as it is from here] ...
    $total_absences = $calculated_violations['absence_days'];
    $total_lateness = $calculated_violations['lateness_minutes'];
    $total_early = $calculated_violations['early_departure_minutes'];

    // --- Fetch Insurance Rate ---
    $this->db->select('n3 as discount_rate');
    $insurance_row = $this->db->get_where('insurance_discount', ['n1' => $employee_id])->row();
    $insurance_rate = ($insurance_row && isset($insurance_row->discount_rate)) ? ((float)$insurance_row->discount_rate) : 0.0;
    
    // --- Calculate Financials ---
    $total_salary = (float)($emp['total_salary'] ?? 0);
    $daily_rate = $total_salary > 0 ? $total_salary / 30.0 : 0; 
    $minute_rate = $daily_rate > 0 ? $daily_rate / 8.0 / 60.0 : 0; 

    // Check if employee is in stop_salary table
    $this->db->where('emp_id', $employee_id);
    $is_salary_stopped = $this->db->count_all_results('stop_salary') > 0;

    $calendar_days_worked_in_month = 0;
    $month_start_dt = new DateTime($last_working_day_dt->format('Y-m-01'));
    $salary_calc_start = $month_start_dt;
    
    if (!empty($emp['joining_date'])) {
        $join_dt = new DateTime($emp['joining_date']);
        if ($join_dt > $month_start_dt) {
            $salary_calc_start = $join_dt;
        }
    }

    if ($last_working_day_dt >= $salary_calc_start) {
        $calendar_days_worked_in_month = $last_working_day_dt->diff($salary_calc_start)->days + 1;
    }

    $current_day_of_month = (int)$last_working_day_dt->format('d');
    $should_calculate_in_eos = $is_salary_stopped || ($current_day_of_month < 25);

    if (!$should_calculate_in_eos) {
        $calculated_prorated_salary = 0.0;
        $calculated_insurance_deduction = 0.0;
    } else {
        $calculated_prorated_salary = $calendar_days_worked_in_month * $daily_rate;
        $calculated_insurance_deduction = 0.0;
        if (trim($emp['nationality']) === 'سعودي') { 
            $insurance_rate_decimal = ($insurance_rate > 1) ? $insurance_rate / 100.0 : $insurance_rate;
            if($insurance_rate_decimal > 0){
                $base_plus_housing_full = (float)($emp['base_salary'] ?? 0) + (float)($emp['housing_allowance'] ?? 0);
                $gosi_base_full_month = min($base_plus_housing_full, 45000.0); 
                $prorated_gosi_base_amount = ($gosi_base_full_month / 30.0) * $calendar_days_worked_in_month;
                $calculated_insurance_deduction = $prorated_gosi_base_amount * $insurance_rate_decimal;
            }
        }
    }

    $calculated_gratuity = 0;
    if (!empty($emp['joining_date']) && !empty($order['date_of_the_last_working'])) {
        $start = new DateTime($emp['joining_date']);
        $end = new DateTime($order['date_of_the_last_working']);
        $diff = $start->diff($end);
        $years_of_service = $diff->y + ($diff->m / 12) + ($diff->d / 365.25);
        $base_salary_for_gratuity = (float)($emp['base_salary'] ?? $total_salary); 

        if ($years_of_service >= 10) {
             if ($years_of_service <= 5) {
                 $calculated_gratuity = ($base_salary_for_gratuity / 2) * $years_of_service;
             } else {
                 $calculated_gratuity = (($base_salary_for_gratuity / 2) * 5) + ($base_salary_for_gratuity * ($years_of_service - 5));
             }
        } elseif ($years_of_service >= 5) {
            $full_gratuity = 0;
             if ($years_of_service <= 5) { 
                 $full_gratuity = ($base_salary_for_gratuity / 2) * $years_of_service;
             } else {
                 $full_gratuity = (($base_salary_for_gratuity / 2) * 5) + ($base_salary_for_gratuity * ($years_of_service - 5));
             }
            $calculated_gratuity = $full_gratuity * (2/3);
        } elseif ($years_of_service >= 2) {
            $full_gratuity = ($base_salary_for_gratuity / 2) * $years_of_service;
            $calculated_gratuity = $full_gratuity * (1/3);
        }
    }

    $calculated_leave_compensation = ($leave_balance > 0) ? ($leave_balance * $daily_rate) : 0;
    $calculated_negative_leave_deduction = ($leave_balance < 0) ? (abs($leave_balance) * $daily_rate) : 0;
    $calculated_absence_deduction = $total_absences * $daily_rate;
    $calculated_lateness_deduction = ($total_lateness + $total_early) * $minute_rate;
    $calculated_single_punch_deduction = 0; 
    
    $result = array_merge($emp, $order);
    $result['leave_balance'] = $leave_balance;
    $result['total_absences'] = $total_absences;
    $result['total_lateness'] = $total_lateness;
    $result['total_early'] = $total_early;
    $result['calculated_gratuity'] = $calculated_gratuity;
    $result['calculated_leave_compensation'] = $calculated_leave_compensation;
    $result['calculated_negative_leave_deduction'] = $calculated_negative_leave_deduction;
    $result['calculated_absence_deduction'] = $calculated_absence_deduction;
    $result['calculated_lateness_deduction'] = $calculated_lateness_deduction;
    $result['calculated_single_punch_deduction'] = $calculated_single_punch_deduction;
    $result['calculated_insurance_deduction'] = $calculated_insurance_deduction;
    $result['calculated_prorated_salary'] = $calculated_prorated_salary;

    $this->db->select('id');
    $this->db->where('employee_id', $employee_id);
    $this->db->where('is_archived', 0);
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $settlement = $this->db->get('end_of_service_settlements')->row_array();

    if ($settlement) {
        $result['approval_log'] = $this->get_approval_log($settlement['id'], 8); 
    } else {
        $result['approval_log'] = [];
    }
    return $result;
}
// public function correct_leave_balance_on_resignation($employee_id, $last_working_date) {
//     // 1. Fetch the EXACT date the HR created the request from the DB
//     $this->db->select('date as request_date');
//     $this->db->where('emp_id', $employee_id);
//     $this->db->where('type', 1); // 1 = Resignation
//     $this->db->order_by('id', 'DESC');
//     $order = $this->db->get('orders.orders_emp')->row();

//     if (!$order || empty($last_working_date)) {
//         return "لم يتم الخصم: بيانات غير مكتملة.";
//     }

//     $lwd_dt = new DateTime($last_working_date);
//     $req_dt = new DateTime($order->request_date);

//     if ($req_dt > $lwd_dt) {
//         $diff_days = $lwd_dt->diff($req_dt)->days;
        
//         // THE FIX: The cron didn't run on the day HR made the request.
//         // So the wrongly added days are strictly the days BETWEEN LWD and Request Date.
//         $wrong_days_added = $diff_days - 1; 

//         if ($wrong_days_added > 0) {
//             $daily_rate = 0.0712; 
//             $excess_balance = $wrong_days_added * $daily_rate;

//             // Fetch current balance
//             $this->db->where('employee_id', $employee_id);
//             $this->db->where('leave_type_slug', 'annual');
//             $this->db->order_by('year', 'DESC');
//             $balance_row = $this->db->get('employee_leave_balances')->row();

//             if ($balance_row) {
//                 $current_balance = (float) $balance_row->remaining_balance;
                
//                 // Deduct the exact excess and round it nicely
//                 $new_balance = round($current_balance - $excess_balance, 4);

//                 // Update DB permanently
//                 $this->db->where('id', $balance_row->id);
//                 $this->db->update('employee_leave_balances', [
//                     'remaining_balance' => $new_balance
//                 ]);

//                 return "نجاح: تم خصم {$wrong_days_added} يوم (بمقدار {$excess_balance}). الرصيد أصبح {$new_balance}.";
//             }
//         } else {
//             return "الرصيد سليم: لا يوجد أيام خاطئة لخصمها.";
//         }
//     }
//     return "لم يتم الخصم: تاريخ الطلب ليس بعد آخر يوم عمل.";
// }
// In application/models/hr_model.php

public function update_settlement_verification_data($task_id, $ver_data)
{
    // 1. Get the order_id from the approval task
    $task = $this->db->get_where('approval_workflow', ['id' => $task_id])->row_array();
    if (!$task) return false;
    
    $settlement_id = $task['order_id'];
    
    // 2. Fetch current settlement data
    $settlement = $this->db->get_where('end_of_service_settlements', ['id' => $settlement_id])->row_array();
    if (!$settlement) return false;
    
    // 3. Decode existing items JSON
    $items = json_decode($settlement['items_json'], true);
    if (!is_array($items)) $items = [];
    
    // 4. Add/Update verification flags in the items array
    // We add them as specific keys so they can be retrieved later
    $items['verification_flags'] = $ver_data;
    
    // 5. Save back to DB
    $this->db->where('id', $settlement_id);
    return $this->db->update('end_of_service_settlements', [
        'items_json' => json_encode($items)
    ]);
}
public function get_all_employees_for_export()
{
    // This helper function already reads all the $_POST filters
    $this->_get_datatables_query();
    
    // The key difference: NO LIMIT or OFFSET
    $query = $this->db->get();
    return $query; // <-- RETURN THE QUERY OBJECT, NOT THE ARRAY
}

public function get_all_subordinates_ids($manager_id)
{
    if (empty($manager_id)) {
        return [];
    }

    // Trim whitespace just in case
    $manager_id = trim($manager_id);

    $cols = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7'];
    $this->db->select(implode(',', $cols));
    
    // Find all rows where the manager exists
    $this->db->group_start();
    for ($i = 0; $i < 6; $i++) { 
        $this->db->or_where($cols[$i], $manager_id);
    }
    $this->db->group_end();

    $query = $this->db->get('organizational_structure');
    $rows = $query->result_array();

    if (empty($rows)) {
        return []; 
    }

    $subordinate_ids = [];

    foreach ($rows as $row) {
        $manager_level = -1;
        // Find the *first* level the manager appears at
        for ($i = 0; $i < 6; $i++) {
            if (isset($row[$cols[$i]]) && $row[$cols[$i]] == $manager_id) {
                $manager_level = $i;
                break;
            }
        }

        if ($manager_level !== -1) {
            // Add all non-empty employees *below* this level
            for ($j = $manager_level + 1; $j < 7; $j++) { 
                if (!empty($row[$cols[$j]])) {
                    $subordinate_ids[] = $row[$cols[$j]];
                }
            }
        }
    }

    return array_unique($subordinate_ids);
}
// In hr_model.php - ADD THIS NEW FUNCTION
private function _calculate_eos_violations_for_calendar_month($employee_id, $month_start_date_str, $last_working_day_str) {
    // Input validation
    if (empty($employee_id) || empty($month_start_date_str) || empty($last_working_day_str) || strtotime($month_start_date_str) > strtotime($last_working_day_str)) {
        log_message('error', "Invalid input for _calculate_eos_violations_for_calendar_month. Emp: $employee_id, Start: $month_start_date_str, End: $last_working_day_str");
        return ['absence_days' => 0, 'lateness_minutes' => 0, 'early_departure_minutes' => 0];
    }

    try {
        $start_date = new DateTime($month_start_date_str);
        $end_date   = new DateTime($last_working_day_str);
        // EXTENDED END DATE: Add 1 day to 05:59 AM to catch cross-midnight checkouts on the very last working day
        $extended_query_end = (clone $end_date)->modify('+1 day')->setTime(5, 59, 59); 
    } catch (Exception $e) {
        log_message('error', "Invalid date format in _calculate_eos_violations_for_calendar_month: " . $e->getMessage());
        return ['absence_days' => 0, 'lateness_minutes' => 0, 'early_departure_minutes' => 0];
    }

    // --- 1. Fetch Raw Punches with 6-Hour Night Shift Offset ---
    $tables = $this->_get_attendance_tables(); 
    $punches_by_day = [];
     if (!empty($tables)) {
        $unions = [];
        foreach ($tables as $table) {
            if ($this->db->table_exists($table) && $this->db->field_exists('emp_code', $table) && $this->db->field_exists('punch_time', $table)) {
                 $unions[] = sprintf(
                    "(SELECT punch_time FROM `%s` WHERE emp_code = %s AND punch_time BETWEEN %s AND %s)",
                    $table,
                    $this->db->escape($employee_id),
                    $this->db->escape($start_date->format('Y-m-d 00:00:00')),
                    $this->db->escape($extended_query_end->format('Y-m-d H:i:s')) 
                );
            }
        }
        if (!empty($unions)) {
            $union_sql = implode(" UNION ALL ", $unions);
            // FIXED SQL: Subtracts 6 hours from the punch_time just for grouping purposes!
            // This forces a 02:00 AM punch on March 2nd to be grouped under March 1st.
            $punches_query = $this->db->query(
                "SELECT DATE(DATE_SUB(punch_time, INTERVAL 6 HOUR)) as punch_date, 
                        MIN(punch_time) as first_punch, 
                        MAX(punch_time) as last_punch, 
                        COUNT(punch_time) as punch_count
                 FROM ({$union_sql}) as punches
                 GROUP BY DATE(DATE_SUB(punch_time, INTERVAL 6 HOUR))"
            );
             if ($punches_query) { 
                foreach ($punches_query->result() as $row) {
                    $punches_by_day[$row->punch_date] = $row;
                }
            } else {
                 log_message('error', 'Database error fetching punches in EOS violation calc: ' . print_r($this->db->error(), true));
            }
        }
    }


    // --- 2. Fetch Approved Corrections, Leaves, and Public Holidays ---
    $this->db->from('orders_emp');
    $this->db->where('emp_id', $employee_id);
    $this->db->where('status', '2'); // Only approved
    $this->db->group_start();
        $this->db->where('type', 2)->where("correction_date BETWEEN '{$month_start_date_str}' AND '{$last_working_day_str}'");
        $this->db->or_where('type', 5)->where("((vac_start <= '{$last_working_day_str}' AND vac_end >= '{$month_start_date_str}') OR (vac_half_date BETWEEN '{$month_start_date_str}' AND '{$last_working_day_str}'))", NULL, FALSE);
    $this->db->group_end();
    $requests_query = $this->db->get();
    $requests = $requests_query ? $requests_query->result() : [];

    $corrections_by_day = [];
    $leaves_by_day = []; 
    foreach ($requests as $req) {
        if ($req->type == 2 && !empty($req->correction_date)) {
             $corrections_by_day[$req->correction_date] = true;
         } elseif ($req->type == 5) {
            if (!empty($req->vac_start) && !empty($req->vac_end)) {
                try {
                    $leave_start_dt = new DateTime(max($req->vac_start, $month_start_date_str));
                    $leave_end_dt = new DateTime(min($req->vac_end, $last_working_day_str));
                    $leave_end_dt->modify('+1 day'); 
                    $period = new DatePeriod($leave_start_dt, new DateInterval('P1D'), $leave_end_dt);
                    foreach ($period as $day) {
                        $leaves_by_day[$day->format('Y-m-d')] = true;
                    }
                } catch (Exception $e) {
                     log_message('error', "Error processing leave range in EOS violation calc: " . $e->getMessage());
                }
            }
            if (!empty($req->vac_half_date) && $req->vac_half_date >= $month_start_date_str && $req->vac_half_date <= $last_working_day_str) {
                 if (!isset($leaves_by_day[$req->vac_half_date]) || $leaves_by_day[$req->vac_half_date] !== true) {
                    $leaves_by_day[$req->vac_half_date] = $req->vac_half_period ?? 'am';
                 }
            }
        }
    }

    $public_holidays = $this->get_holidays_as_flat_array($month_start_date_str, $last_working_day_str); 
    
    // FETCH RULES FOR RAMADAN BREASTFEEDING OVERRIDE
    $rules = $this->db->get_where('work_restrictions', ['emp_id' => $employee_id])->row_array();
    $is_breastfeeding = (isset($rules['working_hours']) && (float)$rules['working_hours'] == 8.0);

    // --- 3. Initialize Counters ---
    $total_absence_days = 0;
    $total_lateness_minutes = 0;
    $total_early_departure_minutes = 0;

    // --- 4. Iterate Through Dates ---
    $interval = new DateInterval('P1D');
    $period_end_date_inclusive = (clone $end_date)->modify('+1 day'); 
    try {
        $date_range = new DatePeriod($start_date, $interval, $period_end_date_inclusive);
    } catch (Exception $e) {
        log_message('error', "Error creating date period for EOS violation calc: " . $e->getMessage());
        return ['absence_days' => 0, 'lateness_minutes' => 0, 'early_departure_minutes' => 0]; 
    }

    foreach ($date_range as $day) {
        $current_date_str = $day->format('Y-m-d');
        $day_of_week_N = $day->format('N'); 

        // --- Absence Exclusions ---
        if (isset($corrections_by_day[$current_date_str]) ||
            in_array($current_date_str, $public_holidays) ||
            (isset($leaves_by_day[$current_date_str]) && $leaves_by_day[$current_date_str] === true)) {
            continue;
        }

        $is_friday = ($day_of_week_N == 5);
        $is_saturday = ($day_of_week_N == 6);
        $is_mandatory_saturday = $is_saturday && $this->is_mandatory_saturday($employee_id, $current_date_str);

        if ($is_friday || ($is_saturday && !$is_mandatory_saturday)) {
            continue;
        }

        // --- Determine Daily Rules ---
        $is_half_day_leave = isset($leaves_by_day[$current_date_str]) && $leaves_by_day[$current_date_str] !== true;
        $is_ramadan = (strtotime($current_date_str) >= strtotime('2026-02-18') && strtotime($current_date_str) <= strtotime('2026-03-19'));

        if ($is_mandatory_saturday) {
            $lateness_threshold_time_str = '13:00:00'; 
            $working_hours_today = 6.0;            
        } else { 
            $lateness_threshold_time_str = '11:00:00'; 
            $working_hours_today = 9.0;            
        }

        // === RAMADAN OVERRIDE ===
        if ($is_ramadan) {
            $base_ramadan_hours = $is_breastfeeding ? 5.0 : 6.0;
            $working_hours_today = $base_ramadan_hours;
            $lateness_threshold_time_str = null; // Flexible shift: remove strict morning requirement
        }

        // Adjust for half-day leave
        if ($is_half_day_leave) {
            $working_hours_today /= 2; 
            if (!$is_ramadan && ($leaves_by_day[$current_date_str] === 'am' || $leaves_by_day[$current_date_str] === 'صباحي')) {
                 $lateness_threshold_time_str = '13:00:00';
            }
        }

        // --- Check Attendance and Calculate Violations ---
        if (!isset($punches_by_day[$current_date_str])) {
            if (!$is_half_day_leave) {
                 $total_absence_days++; // <--- March 2nd will correctly trigger here now!
            }
        } else {
            $punch = $punches_by_day[$current_date_str];
            $first_punch_dt = null;
            $last_punch_dt = null;
            try {
                 if(!empty($punch->first_punch)) $first_punch_dt = new DateTime($punch->first_punch);
                 if(!empty($punch->last_punch)) $last_punch_dt = new DateTime($punch->last_punch);
            } catch (Exception $e) {
                log_message('error', "Error parsing punch times in EOS violation calc for $current_date_str: " . $e->getMessage());
                continue;
            }

            // Calculate Lateness (Skipped during flexible Ramadan shifts)
            if ($first_punch_dt && $lateness_threshold_time_str) {
                try {
                    $lateness_cutoff_dt = new DateTime($first_punch_dt->format('Y-m-d') . ' ' . $lateness_threshold_time_str);
                    if ($first_punch_dt > $lateness_cutoff_dt) {
                        $late_minutes = round(($first_punch_dt->getTimestamp() - $lateness_cutoff_dt->getTimestamp()) / 60);
                        if ($late_minutes > 0) {
                            $total_lateness_minutes += $late_minutes;
                        }
                    }
                } catch (Exception $e) {}
            }

            // Calculate Shortage / Early Departure
            if ($first_punch_dt && $last_punch_dt && $first_punch_dt != $last_punch_dt && $working_hours_today > 0) {
                 try {
                    $required_seconds = $working_hours_today * 3600;
                    $required_end_dt = (clone $first_punch_dt)->modify('+' . round($required_seconds) . ' seconds');

                    if ($last_punch_dt < $required_end_dt) {
                        $early_minutes = round(($required_end_dt->getTimestamp() - $last_punch_dt->getTimestamp()) / 60);
                        if ($early_minutes > 0) {
                            // During Ramadan, this perfectly captures the "shortage" of hours regardless of what time they started
                            $total_early_departure_minutes += $early_minutes;
                        }
                    }
                 } catch (Exception $e) {}
            }
        } 
    } 

    return [
        'absence_days' => $total_absence_days,
        'lateness_minutes' => $total_lateness_minutes,
        'early_departure_minutes' => $total_early_departure_minutes
    ];
}
public function save_end_of_service_settlement($data)
{
    // Debug: Check what data is received
    error_log('Data received in save_end_of_service_settlement: ' . print_r($data, true));

    // Get items from the data - it should be in $data['items']
    $items = isset($data['items']) ? $data['items'] : [];

    // Debug: Check items
    error_log('Items extracted: ' . print_r($items, true));

    // Initialize all amounts to 0.00
    $extracted_amounts = [
        'gratuity_amount'           => 0.00,
        'compensation'              => 0.00,
        'prorated_salary'           => 0.00, 
        'insurance_compensation'    => 0.00,
        'insurance_deduction'       => 0.00,
        'leave_balance_deduction'   => 0.00,
        'absence_deduction'         => 0.00,
        'lateness_deduction'        => 0.00,
        'penalty_clause_deduction'  => 0.00,
        'absence_penalty_deduction' => 0.00,
    ];

    // Extract amounts based on the key field
    foreach ($items as $item) {
        $amount = (float)($item['amount'] ?? 0);
        $key = $item['key'] ?? null;

        if ($key && isset($extracted_amounts[$key])) {
            $extracted_amounts[$key] = $amount;
        }
    }

    // Get final_amount from settlement array
    $final_amount = isset($data['settlement']['final_amount']) ? (float)$data['settlement']['final_amount'] : 0.00;

    // Prepare the main data array
    $main_data = [
        'employee_id'               => $data['employee_id'],
        'resignation_order_id'      => $data['resignation_order_id'],
        'created_by_id'             => $data['created_by_id'],
        'status'                    => 'pending_review',
        'current_approver'          => '2774', 
        'final_amount'              => $final_amount,
        'items_json'                => json_encode($items), 
        
        // --- ADD THE DATE HERE ---
        // Save the custom deduction date if it was passed, otherwise null
        'deduction_start_date'      => isset($data['deduction_start_date']) && !empty($data['deduction_start_date']) ? $data['deduction_start_date'] : null,

        'gratuity_amount'           => $extracted_amounts['gratuity_amount'],
        'compensation'              => $extracted_amounts['compensation'],
        'insurance_compensation'    => $extracted_amounts['insurance_compensation'],
        'insurance_deduction'       => $extracted_amounts['insurance_deduction'],
        'leave_balance_deduction'   => $extracted_amounts['leave_balance_deduction'],
        'absence_deduction'         => $extracted_amounts['absence_deduction'],
        'lateness_deduction'        => $extracted_amounts['lateness_deduction'],
        'penalty_clause_deduction'  => $extracted_amounts['penalty_clause_deduction'],
        'absence_penalty_deduction' => $extracted_amounts['absence_penalty_deduction'],
        'prorated_salary_amount'    => $extracted_amounts['prorated_salary'], 
    ];

    // Insert into database
    $result = $this->db->insert('end_of_service_settlements', $main_data);

    if ($result) {
        $insert_id = $this->db->insert_id();
        error_log("Insert successful! ID: $insert_id");
        return $insert_id;
    } else {
        $error = $this->db->error();
        error_log('Database error: ' . print_r($error, true));
        return false;
    }
}

public function approve_settlement($approval_task_id, $approver_id, $comments = '')
{
    // Start transaction
    $this->db->trans_start();

    // Get current approval task
    $this->db->where('id', $approval_task_id);
    $this->db->where('approver_id', $approver_id);
    $this->db->where('status', 'pending');
    $current_task = $this->db->get('approval_workflow')->row_array();

    if (!$current_task) {
        $this->db->trans_rollback();
        return false;
    }

    $settlement_id = $current_task['order_id'];

    // Update current task to approved
    $this->db->where('id', $approval_task_id);
    $this->db->update('approval_workflow', [
        'status' => 'approved',
        'action_date' => date('Y-m-d H:i:s'),
        'comments' => $comments
    ]);

    // Get next approval level
    $next_level = $current_task['approval_level'] + 1;

    // Check if there's a next approver
    $this->db->where('order_id', $settlement_id);
    $this->db->where('order_type', 8);
    $this->db->where('approval_level', $next_level);
    $next_task = $this->db->get('approval_workflow')->row_array();

    if ($next_task) {
        // Activate next approver
        $this->db->where('id', $next_task['id']);
        $this->db->update('approval_workflow', [
            'status' => 'pending'
        ]);

        // Update settlement current approver
        $this->db->where('id', $settlement_id);
        $this->db->update('end_of_service_settlements', [
            'current_approver' => $next_task['approver_id']
        ]);
    } else {
        // No more approvers - settlement is fully approved
        $this->db->where('id', $settlement_id);
        $this->db->update('end_of_service_settlements', [
            'status' => 'approved',
            'current_approver' => null
        ]);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}
public function reject_settlement($approval_task_id, $approver_id, $comments = '')
{
    // Start transaction
    $this->db->trans_start();

    // Get current approval task
    $this->db->where('id', $approval_task_id);
    $this->db->where('approver_id', $approver_id);
    $this->db->where('status', 'pending');
    $current_task = $this->db->get('approval_workflow')->row_array();

    if (!$current_task) {
        $this->db->trans_rollback();
        return false;
    }

    $settlement_id = $current_task['order_id'];

    // Update current task to rejected
    $this->db->where('id', $approval_task_id);
    $this->db->update('approval_workflow', [
        'status' => 'rejected',
        'action_date' => date('Y-m-d H:i:s'),
        'comments' => $comments
    ]);

    // Update settlement status to rejected and clear current approver
    $this->db->where('id', $settlement_id);
    $this->db->update('end_of_service_settlements', [
        'status' => 'rejected',
        'current_approver' => null
    ]);

    // Set all waiting tasks to cancelled
    $this->db->where('order_id', $settlement_id);
    $this->db->where('order_type', 8);
    $this->db->where('status', 'waiting');
    $this->db->update('approval_workflow', [
        'status' => 'cancelled'
    ]);

    $this->db->trans_complete();
    return $this->db->trans_status();
}
    public function add_approval_step($order_id, $order_type, $approver_id, $approval_level)
{
    $data = [
        'order_id'       => $order_id,
        'order_type'     => $order_type, // This uses the new column
        'approver_id'    => $approver_id,
        'approval_level' => $approval_level,
        'status'         => 'pending' // Initial status
    ];
    
    // Inserts into your correctly named table 'approval_workflow'
    return $this->db->insert('approval_workflow', $data);
}


/**
 * Fetches all pending approvals for a specific user from all relevant tables.
 * This now correctly queries both standard orders and End of Service settlements.
 *
 * @param string $approver_id The employee ID of the logged-in manager.
 * @return array A merged list of all pending approvals.
 */
public function get_pending_approvals_for_user($approver_id)
{
    // --- 1. Get Standard Requests (from orders_emp table) ---
    $this->db->select('
        aw.id as approval_id, 
        aw.order_id, 
        aw.order_type,
        oe.order_name, 
        oe.emp_name, 
        oe.date,
        "users1/view_request/" as url_prefix,  -- URL for standard requests
        oe.emp_id as url_suffix
    ');
    $this->db->from('approval_workflow aw');
    $this->db->join('orders_emp oe', 'aw.order_id = oe.id AND aw.order_type = oe.type');
    $this->db->where('aw.approver_id', $approver_id);
    $this->db->where('aw.status', 'pending');
    $standard_requests = $this->db->get()->result_array();

    // --- 2. Get End of Service Settlements (from end_of_service_settlements table) ---
    $this->db->select('
        aw.id as approval_id, 
        aw.order_id, 
        aw.order_type,
        "مستحقات نهاية الخدمة" as order_name, 
        e.subscriber_name as emp_name, 
        eos.created_at as date,
        "users1/end_of_service?emp=" as url_prefix, -- URL for EOS requests
        eos.employee_id as url_suffix
    ');
    $this->db->from('approval_workflow aw');
    $this->db->join('end_of_service_settlements eos', 'aw.order_id = eos.id AND aw.order_type = 8');
    $this->db->join('emp1 e', 'eos.employee_id = e.employee_id');
    $this->db->where('aw.approver_id', $approver_id);
    $this->db->where('aw.status', 'pending');
    $settlement_requests = $this->db->get()->result_array();

    // --- 3. Merge both results into a single list ---
    return array_merge($standard_requests, $settlement_requests);
}
// In hr_model.php

/**
 * (READ) Gets all parameters with department and approver names.
 */
public function get_all_clearance_parameters()
{
    $this->db->select('
        cp.id, cp.parameter_name, cp.approver_user_id, cp.is_active,
        d.name as department_name,
        e.subscriber_name as approver_name
    ');
    $this->db->from('clearance_parameters AS cp');
    $this->db->join('departments AS d', 'd.id = cp.department_id', 'left');
    $this->db->join('emp1 AS e', 'e.employee_id = cp.approver_user_id', 'left');
    $this->db->order_by('d.name', 'ASC');
    $this->db->order_by('cp.parameter_name', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}

/**
 * (READ) Gets a single parameter by its ID.
 */
public function get_parameter_by_id($id)
{
    $query = $this->db->get_where('clearance_parameters', ['id' => $id]);
    return $query->row_array();
}

/**
 * (CREATE/UPDATE) Saves a parameter to the database.
 */
public function save_clearance_parameter($id, $data)
{
    if (empty($id)) {
        // ID is empty, so this is a new record
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('clearance_parameters', $data);
    } else {
        // ID exists, so update the existing record
        $this->db->where('id', $id);
        return $this->db->update('clearance_parameters', $data);
    }
}

/**
 * (DELETE) Deletes a parameter from the database.
 */
public function delete_clearance_parameter($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('clearance_parameters');
}
public function process_gosi_batch($data, $company_code, $upload_mode, $branch = null)
{
    if (empty($data) || empty($company_code)) {
        return false;
    }

    $this->db->trans_start();

    // If mode is "replace", delete records.
    if ($upload_mode === 'replace') {
        
        // ⭐️ NEW: Check if this is a branch-specific replace for Company 1
        if ($company_code == '1' && !empty($branch)) {
            
            // 1. Get all employee ID numbers for this company AND branch from emp1
            $this->db->select('id_number');
            $this->db->from('emp1');
            $this->db->where('n13', $company_code); // n13 is company code in emp1
            $this->db->where('location', $branch);  // location is branch in emp1
            $query = $this->db->get();
            $id_numbers = array_column($query->result_array(), 'id_number');

            // 2. Delete from gosi ONLY where company matches AND ID is in our branch list
            if (!empty($id_numbers)) {
                $this->db->where('n7', $company_code); // n7 is company code in gosi
                $this->db->where_in('n2', $id_numbers); // n2 is identity number in gosi
                $this->db->delete('gosi');
            }
            
        } else {
            // This is the original logic: delete ALL records for the company
            $this->db->where('n7', $company_code);
            $this->db->delete('gosi');
        }
    }

    // Insert the new batch of records (this works for both "add" and "replace")
    $this->db->insert_batch('gosi', $data);

    $this->db->trans_complete();

    return $this->db->trans_status();
}
public function build_gosi_emp_compare($company = null, $branch = null)
{
    $companies = [
        1 => 'شركة مرسوم',
        2 => 'مكتب الدكتور صالح الجربوع للمحاماة',
    ];

    $result = [
        'blocks' => []
    ];

    foreach ($companies as $code => $label) {
        // Filter by Company if selected
        if ($company !== null && (string)$company !== (string)$code) {
            continue; 
        }

        // =====================================================
        // 1. Fetch ACTIVE EMP1 (Filtered by Branch if set)
        // =====================================================
        $this->db->select("id_number, subscriber_name, employee_id, COALESCE(total_salary,0) AS total_salary", false)
            ->from('emp1')
            ->where('n13', $code)
            ->where('status !=', 'resigned')
            ->where('status !=', 'deleted'); // Ensure deleted are excluded too

        // Apply Branch Filter for Marsoom (Code 1)
        if ($code == '1' && !empty($branch)) {
            $this->db->where('location', $branch);
        }
        
        $empData = $this->db->get()->result_array();

        $empMap = [];
        $empTotal = 0;
        $active_ids_in_branch = []; // List of IDs found in this branch

        foreach ($empData as $r) {
            $r['total_salary'] = floatval($r['total_salary']);
            $empMap[$r['id_number']] = $r;
            $empTotal += $r['total_salary'];
            
            if (!empty($r['id_number'])) {
                $active_ids_in_branch[] = $r['id_number'];
            }
        }

        // =====================================================
        // 2. Fetch Resigned IDs (Global for Company)
        // We need this to filter out resigned people if NO branch is selected
        // =====================================================
        $resigned_ids = [];
        if (empty($branch)) {
            $this->db->select('id_number')->from('emp1')
                ->where('n13', $code)->where('status', 'resigned');
            $resQuery = $this->db->get()->result_array();
            $resigned_ids = array_column($resQuery, 'id_number');
        }

        // =====================================================
        // 3. Fetch GOSI Data (Strictly Filtered)
        // =====================================================
        $this->db->select("
                n2           AS id_number,
                n1           AS name,
                COALESCE(n3,0) AS basic,
                COALESCE(n4,0) AS housing,
                COALESCE(n5,0) AS commission,
                COALESCE(n6,0) AS other
            ", false)
            ->from('gosi')
            ->where('n7', $code);

        // *** CRITICAL FIX ***
        // If a branch is selected, ONLY fetch GOSI records for people who are 
        // physically in that branch (based on EMP1 IDs).
        // This prevents Abha employees from showing up as "GOSI Only" in the Riyadh report.
        if ($code == '1' && !empty($branch)) {
            if (!empty($active_ids_in_branch)) {
                $this->db->where_in('n2', $active_ids_in_branch);
            } else {
                // If branch has 0 employees, GOSI result should be 0 too
                $this->db->where('1=0'); 
            }
        }

        $gosiData = $this->db->get()->result_array();

        $gosiMap = [];
        $gosiTotal = 0;
        foreach ($gosiData as $r) {
            $r['total'] = floatval($r['basic']) + floatval($r['housing']) + floatval($r['commission']) + floatval($r['other']);
            $gosiMap[$r['id_number']] = $r;
            $gosiTotal += $r['total'];
        }


        // =====================================================
        // 4. Comparison Logic
        // =====================================================
        
        // A) GOSI Only (In GOSI, missing from Active EMP1)
        $onlyGosi = [];
        $onlyGosiTotals = ['basic'=>0,'housing'=>0,'commission'=>0,'other'=>0,'total'=>0,'count'=>0];

        foreach ($gosiMap as $id => $r) {
            if (!isset($empMap[$id])) {
                // Skip if Resigned (Only relevant if no branch selected)
                if (in_array($id, $resigned_ids)) continue;

                $onlyGosi[] = $r;
                $onlyGosiTotals['basic']      += $r['basic'];
                $onlyGosiTotals['housing']    += $r['housing'];
                $onlyGosiTotals['commission'] += $r['commission'];
                $onlyGosiTotals['other']      += $r['other'];
                $onlyGosiTotals['total']      += $r['total'];
                $onlyGosiTotals['count']++;
            }
        }

        // B) EMP1 Only (In Active EMP1, missing from GOSI)
        $onlyEmp = [];
        $onlyEmpTotals = ['total_salary'=>0,'count'=>0];

        foreach ($empMap as $id => $r) {
            if (!isset($gosiMap[$id])) {
                $onlyEmp[] = $r;
                $onlyEmpTotals['total_salary'] += $r['total_salary'];
                $onlyEmpTotals['count']++;
            }
        }

        // C) Mismatch (In both, salaries differ)
        $mismatch = [];
        $deltaAbsTotal = 0;
        foreach ($gosiMap as $id => $g) {
            if (isset($empMap[$id])) {
                $e = $empMap[$id];
                $gTotal = $g['total'];
                $eTotal = $e['total_salary'];
                if (abs($gTotal - $eTotal) > 0.01) {
                    $mismatch[] = [
                        'id_number' => $id,
                        'name_gosi' => $g['name'],
                        'employee_id' => $e['employee_id'],
                        'total_gosi' => $gTotal,
                        'total_emp'  => $eTotal,
                        'diff'       => $eTotal - $gTotal
                    ];
                    $deltaAbsTotal += abs($eTotal - $gTotal);
                }
            }
        }

        // Summaries
        $cntGosi = count($gosiMap);
        $cntEmp  = count($empMap);
        $deltaCount = $cntEmp - $cntGosi;
        $deltaTotal = $empTotal - $gosiTotal;
        $pctCount = ($cntGosi > 0) ? ($deltaCount / $cntGosi * 100) : ($cntEmp > 0 ? 100 : 0);
        $pctTotal = ($gosiTotal > 0) ? ($deltaTotal / $gosiTotal * 100) : ($empTotal > 0 ? 100 : 0);
        
        $report_label = $label . (!empty($branch) && $code == '1' ? ' - ' . $branch : '');

        $summary = [
            'label'        => $report_label,
            'cntGosi'      => $cntGosi,
            'cntEmp'       => $cntEmp,
            'totalGosi'    => $gosiTotal,
            'totalEmp'     => $empTotal,
            'deltaCount'   => $deltaCount,
            'deltaTotal'   => $deltaTotal,
            'pctCount'     => $pctCount,
            'pctTotal'     => $pctTotal,
            'mismatchAbsDelta' => $deltaAbsTotal,
        ];

        $result['blocks'][] = [
            'code'      => $code,
            'label'     => $report_label,
            'onlyGosi'  => $onlyGosi,
            'onlyEmp'   => $onlyEmp,
            'mismatch'  => $mismatch,
            'onlyGosiTotals' => $onlyGosiTotals,
            'onlyEmpTotals'  => $onlyEmpTotals,
            'summary'  => $summary,
        ];
    }

    return $result;
}

         function __construct(){
          $this->load->database();
        }  


            function showAllUsers(){
            $this->db->order_by('id', 'asc');       
            $query = $this->db->get('users');
            if($query->num_rows() > 0){
                return $query->result();
            }else{
                return false;
            }
        }

        public function get_all_org_paths() {
        return $this->db->select('n1,n2,n3,n4,n5,n6,n7')
                        ->from('organizational_structure')
                        ->get()->result_array();
    }

public function execute_final_resignation_approval($order_id, $approver_id) {
    if (!in_array($approver_id, ['2784', '2901'])) {
        return "المستخدم غير مصرح له بتعديل حالة الاستقالة."; 
    }

    $this->db->select('emp_id, date_of_the_last_working, type');
    $this->db->where('id', $order_id);
    $order = $this->db->get('orders_emp')->row();

    if (!$order || $order->type != 1 || empty($order->date_of_the_last_working)) {
        return "بيانات الطلب غير مكتملة أو ليس طلب استقالة.";
    }

    $employee_id = $order->emp_id;

    // ==========================================
    // ACTION 1: Update Employee Status in emp1
    // ==========================================
    $this->db->where('employee_id', $employee_id);
    if (!$this->db->update('emp1', ['status' => 'resigned'])) {
        $err = $this->db->error();
        return "فشل تحديث جدول emp1: " . ($err['message'] ?? 'خطأ غير معروف');
    }

  // ==========================================
    // ACTION 2: Correct the Leave Balance
    // ==========================================
    // 1. Force both dates to be strict 'YYYY-MM-DD' strings (ignores hidden times)
   $lwd_dt = new DateTime($order->date_of_the_last_working);
    $lwd_dt->setTime(0, 0, 0);

    $today_dt = new DateTime(date('Y-m-d'));
    $today_dt->setTime(0, 0, 0);

    if ($today_dt > $lwd_dt) {
        // 2. Use %a to get the ABSOLUTE total number of calendar days between the dates
        $wrong_days_added = (int) $lwd_dt->diff($today_dt)->format('%a');

        if ($wrong_days_added > 0) {
            $daily_rate = 0.0712;
            $excess_balance = $wrong_days_added * $daily_rate;

            $this->db->where('employee_id', $employee_id);
            $this->db->where('leave_type_slug', 'annual');
            $this->db->order_by('year', 'DESC');
            $balance_row = $this->db->get('employee_leave_balances')->row();

            if ($balance_row) {
                $current_balance = (float) $balance_row->remaining_balance;
                $new_balance = $current_balance - $excess_balance;

                if ($new_balance < 0 && $current_balance >= 0) {
                    $new_balance = 0;
                }

                // Ensure the number format is exactly 123.4567 (no commas)
                $safe_balance_string = number_format($new_balance, 4, '.', '');

                $this->db->where('employee_id', $employee_id);
                $this->db->where('leave_type_slug', 'annual');
                $this->db->where('year', $balance_row->year);
                
                if (!$this->db->update('employee_leave_balances', ['remaining_balance' => $safe_balance_string])) {
                    $err = $this->db->error();
                    return "Balance update failed: " . ($err['message'] ?? 'Unknown error');
                }
            }
        }
    }

    return true; // Success!
}
public function check_delegation_conflict($delegate_id, $start_date, $end_date)
{
    if (empty($delegate_id) || empty($start_date) || empty($end_date)) {
        return null;
    }

    $this->db->select('emp_name, vac_start, vac_end');
    $this->db->from('orders_emp');
    $this->db->where('delegation_employee_id', $delegate_id);
    $this->db->where('type', 5);    // Vacation
    $this->db->where('status', '2'); // Approved

    // Overlap logic: (new_start <= old_end) AND (new_end >= old_start)
    $this->db->where('vac_start <=', $end_date);
    $this->db->where('vac_end >=', $start_date);
    
    $this->db->limit(1);
    $query = $this->db->get();

    return $query->row_array();
}

    // تجيب الاسم + المسمى الوظيفي بالجملة
    // users.username = emp1.employee_id
    public function get_people_bulk(array $usernames) {
        if (empty($usernames)) return [];

        $rows = $this->db->select('u.username, u.name, e.profession')
                         ->from('users u')
                         ->join('emp1 e', 'e.employee_id = u.username', 'left')
                         ->where_in('u.username', $usernames)
                         ->get()->result_array();

        $map = [];
        foreach ($rows as $r) {
            $map[$r['username']] = [
                'name'  => $r['name'] ?: $r['username'],
                'title' => $r['profession'] ?: '—'
            ];
        }
        return $map;
    }

          

            public function getmydata101($user_id){
            //validate
            $sql = "SELECT * FROM `users` WHERE `username`='$user_id' LIMIT 1;";
            $query = $this->db->query($sql);
            $data = $query->row_array();
            return $data;
                }

                  

                
         





    ////////////////

    function ex_emp1(){
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "emp1.csv";
        $query = "SELECT * FROM emp1"; //USE HERE YOUR QUERY
        $result = $this->db->query($query);
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, "\xEF\xBB\xBF" . $data);
        //force_download($filename, $data);

    }


     public function insert(array $data): bool
    {
        $this->db->insert('tasks', $data);
        return $this->db->affected_rows() > 0;
    }

    public function update_status(int $id, string $status, array $meta = []): bool
{
    $data = ['status' => $status];

    if (isset($meta['date']))     $data['date']     = $meta['date'];
    if (isset($meta['time']))     $data['time']     = $meta['time'];
    if (isset($meta['username'])) $data['username'] = $meta['username'];
    if (isset($meta['name']))     $data['name']     = $meta['name'];

    $this->db->where('id', $id)->update('tasks', $data);
    return $this->db->affected_rows() > 0;
}


public function update_due_date(int $id, string $due_date, array $meta = []): bool
{
    $data = ['due_date' => $due_date];
    if (isset($meta['date']))     $data['date']     = $meta['date'];
    if (isset($meta['time']))     $data['time']     = $meta['time'];
    if (isset($meta['username'])) $data['username'] = $meta['username'];
    if (isset($meta['name']))     $data['name']     = $meta['name'];

    $this->db->where('id', $id)->update('tasks', $data);
    return $this->db->affected_rows() > 0;
}

public function update_assignee(int $id, string $assignee, array $meta = []): bool
{
    $data = ['assignee' => $assignee];
    if (isset($meta['date']))     $data['date']     = $meta['date'];
    if (isset($meta['time']))     $data['time']     = $meta['time'];
    if (isset($meta['username'])) $data['username'] = $meta['username'];
    if (isset($meta['name']))     $data['name']     = $meta['name'];

    $this->db->where('id', $id)->update('tasks', $data);
    return $this->db->affected_rows() > 0;
}





    public function get_tasks1(array $filters = []): array
    {
        $this->_apply_filters($filters);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('tasks')->result_array();
    }

    /** إرجاع تصنيفات مميّزة من البيانات (تشمل ما كُتب في "أخرى") */
    public function get_distinct_categories(): array
    {
        $this->db->select('DISTINCT(category) AS category', false);
        $this->db->from('tasks');
        $this->db->where('category IS NOT NULL AND category <> ""', null, false);
        $this->db->order_by('category', 'ASC');
        $rows = $this->db->get()->result_array();
        return array_map(function($r){ return $r['category']; }, $rows);
    }

    /** إحصائيات حسب التصنيف */
    public function get_stats_by_category(array $filters = []): array
    {
        // حالاتنا الثابتة
        $statuses = [
            'تم الانجاز','جاري التنفيذ','طلب مستقبلي','جاري دراسة الطلب','طلب مرفوض'
        ];

        $this->db->select("
            category,
            SUM(CASE WHEN status='تم الانجاز' THEN 1 ELSE 0 END) AS done_count,
            SUM(CASE WHEN status='جاري التنفيذ' THEN 1 ELSE 0 END) AS inprog_count,
            SUM(CASE WHEN status='طلب مستقبلي' THEN 1 ELSE 0 END) AS future_count,
            SUM(CASE WHEN status='جاري دراسة الطلب' THEN 1 ELSE 0 END) AS study_count,
            SUM(CASE WHEN status='طلب مرفوض' THEN 1 ELSE 0 END) AS reject_count,
            COUNT(*) AS total_count
        ", false);
        $this->db->from('tasks');
        $this->_apply_filters($filters);
        $this->db->group_by('category');
        $this->db->order_by('category', 'ASC');
        $rows = $this->db->get()->result_array();

        // إضافة نسبة الإنجاز
        foreach ($rows as &$r) {
            $total = (int)$r['total_count'];
            $done  = (int)$r['done_count'];
            $r['done_percent'] = $total > 0 ? round($done * 100 / $total, 1) : 0;
        }
        return $rows;
    }

    /** تطبيق الفلاتر على الاستعلام */
    private function _apply_filters(array $filters)
    {
        // تصنيف
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }

        // حالة/حالات
        if (!empty($filters['statuses']) && is_array($filters['statuses'])) {
            $this->db->where_in('status', $filters['statuses']);
        } elseif (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        // نطاق تاريخ الإنجاز (نستخدم due_date كنص بصيغة YYYY/MM/DD)
        // نقبل القيم القادمة بـ yyyy-mm-dd من الـinput ونحوّلها لـ yyyy/mm/dd
        if (!empty($filters['date_from'])) {
            $from = str_replace('-', '/', $filters['date_from']);
            $this->db->where('due_date >=', $from);
        }
        if (!empty($filters['date_to'])) {
            $to = str_replace('-', '/', $filters['date_to']);
            $this->db->where('due_date <=', $to);
        }
    }


    function ex_vacations(){
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "vacations.csv";
        $query = "SELECT * FROM vacations"; //USE HERE YOUR QUERY
        $result = $this->db->query($query);
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, "\xEF\xBB\xBF" . $data);
        //force_download($filename, $data);

    }

    function ex_fingerprint_correction(){
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "fingerprint_correction.csv";
        $query = "SELECT * FROM fingerprint_correction"; //USE HERE YOUR QUERY
        $result = $this->db->query($query);
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, "\xEF\xBB\xBF" . $data);
        //force_download($filename, $data);

    }

    
    function ex_work_restrictions(){
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "work_restrictions.csv";
        $query = "SELECT * FROM work_restrictions"; //USE HERE YOUR QUERY
        $result = $this->db->query($query);
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, "\xEF\xBB\xBF" . $data);
        //force_download($filename, $data);

    }


           public function get_gosi_emp1_comparison($limit = 10000, $offset = 0, $opts = [])
    {
        $sql = "
        /* الجزء الأول: من gosi مع ربط emp1 */
        SELECT
            g.n2                                          AS id_number,
            g.n1                                          AS emp_name_gosi,
            e.subscriber_name                             AS emp_name_emp1,

            COALESCE(g.n3,0)                              AS g_base,
            COALESCE(g.n4,0)                              AS g_housing,
            (COALESCE(g.n5,0) + COALESCE(g.n6,0))         AS g_other,

            e.id_number                                   AS e_id_number,
            COALESCE(e.base_salary,0)                     AS e_base,
            COALESCE(e.housing_allowance,0)               AS e_housing,
            /* EMP1 Other = n4..n12 */
            (
                COALESCE(e.n4,0) + COALESCE(e.n5,0) + COALESCE(e.n6,0) +
                COALESCE(e.n7,0) + COALESCE(e.n8,0) + COALESCE(e.n9,0) +
                COALESCE(e.n10,0) + COALESCE(e.n11,0) + COALESCE(e.n12,0)
            )                                             AS e_other,

            /* الإجمالي: GOSI محسوب، EMP1 من total_salary */
            (COALESCE(g.n3,0) + COALESCE(g.n4,0) + (COALESCE(g.n5,0)+COALESCE(g.n6,0))) AS g_total,
            COALESCE(e.total_salary,0)                    AS e_total

        FROM gosi g
        LEFT JOIN emp1 e ON e.id_number = g.n2

        UNION ALL

        /* الجزء الثاني: سجلات emp1 التي لا تقابلها gosi */
        SELECT
            e.id_number                                   AS id_number,
            NULL                                          AS emp_name_gosi,
            e.subscriber_name                             AS emp_name_emp1,

            0                                             AS g_base,
            0                                             AS g_housing,
            0                                             AS g_other,

            e.id_number                                   AS e_id_number,
            COALESCE(e.base_salary,0)                     AS e_base,
            COALESCE(e.housing_allowance,0)               AS e_housing,
            (
                COALESCE(e.n4,0) + COALESCE(e.n5,0) + COALESCE(e.n6,0) +
                COALESCE(e.n7,0) + COALESCE(e.n8,0) + COALESCE(e.n9,0) +
                COALESCE(e.n10,0) + COALESCE(e.n11,0) + COALESCE(e.n12,0)
            )                                             AS e_other,

            0                                             AS g_total,
            COALESCE(e.total_salary,0)                    AS e_total

        FROM emp1 e
        LEFT JOIN gosi g ON g.n2 = e.id_number
        WHERE g.n2 IS NULL

        ORDER BY id_number
        LIMIT ? OFFSET ?";

        $query = $this->db->query($sql, [(int)$limit, (int)$offset]);
        $rows  = $query->result_array();

        $tol = isset($opts['tolerance']) ? (float)$opts['tolerance'] : 0.0;

        $out = [];
        foreach ($rows as $r) {
            // اسم نهائي
            $r['emp_name_final'] = !empty($r['emp_name_emp1']) ? $r['emp_name_emp1'] : $r['emp_name_gosi'];

            // فروقات خام
            $diff_base    = (float)$r['g_base']    - (float)$r['e_base'];
            $diff_housing = (float)$r['g_housing'] - (float)$r['e_housing'];
            $diff_other   = (float)$r['g_other']   - (float)$r['e_other'];
            $diff_total   = (float)$r['g_total']   - (float)$r['e_total'];

            // تطبيق التسامح (tolerance)
            if (abs($diff_base)    <= $tol) $diff_base    = 0.0;
            if (abs($diff_housing) <= $tol) $diff_housing = 0.0;
            if (abs($diff_other)   <= $tol) $diff_other   = 0.0;
            if (abs($diff_total)   <= $tol) $diff_total   = 0.0;

            // تقريب للعرض
            $diff_base    = round($diff_base, 2);
            $diff_housing = round($diff_housing, 2);
            $diff_other   = round($diff_other, 2);
            $diff_total   = round($diff_total, 2);

            // تفاصيل وحالة
            $diff_details = [];
            if ($r['emp_name_gosi'] === NULL && (float)$r['g_total'] == 0.0 && (float)$r['e_total'] > 0.0) {
                $diff_details[] = 'الموظف غير موجود في GOSI وموجود في EMP1';
            }
            if ($r['e_id_number'] === NULL && (float)$r['e_total'] == 0.0 && (float)$r['g_total'] > 0.0) {
                $diff_details[] = 'الموظف غير موجود في EMP1 وموجود في GOSI';
            }
            if ($diff_base    != 0.0) $diff_details[] = 'فرق في الراتب الأساسي';
            if ($diff_housing != 0.0) $diff_details[] = 'فرق في بدل السكن';
            if ($diff_other   != 0.0) $diff_details[] = 'فرق في بدل أخرى (GOSI n5+n6 مقابل EMP1 n4..n12)';
            if ($diff_total   != 0.0 && ($diff_base==0.0 && $diff_housing==0.0 && $diff_other==0.0)) {
                $diff_details[] = 'فرق في الإجمالي';
            }
            $status = (empty($diff_details)) ? 'مطابق' : 'غير مطابق';

            // حقول العرض
            $r['diff_base']    = $diff_base;
            $r['diff_housing'] = $diff_housing;
            $r['diff_other']   = $diff_other;
            $r['diff_total']   = $diff_total;
            $r['status']       = $status;
            $r['diff_details'] = implode('، ', $diff_details);

            $out[] = $r;
        }

        // فلترة بالبحث q (هوية أو اسم)
        if (!empty($opts['q'])) {
            $q = mb_strtolower(trim($opts['q']), 'UTF-8');
            $out = array_values(array_filter($out, function($r) use ($q) {
                $id  = mb_strtolower((string)($r['id_number'] ?? ''), 'UTF-8');
                $nm1 = mb_strtolower((string)($r['emp_name_final'] ?? ''), 'UTF-8');
                return (strpos($id, $q) !== false) || (strpos($nm1, $q) !== false);
            }));
        }

        // فلترة غير المطابق فقط
        if (!empty($opts['only_mismatched'])) {
            $out = array_values(array_filter($out, function($r) {
                return $r['status'] !== 'مطابق';
            }));
        }

        // ترتيب
        $sort = isset($opts['sort']) ? $opts['sort'] : 'id_asc';
        if ($sort === 'diff_total_desc' || $sort === 'diff_total_asc') {
            usort($out, function($a, $b) use ($sort) {
                $cmp = abs($a['diff_total']) <=> abs($b['diff_total']);
                return ($sort === 'diff_total_desc') ? -$cmp : $cmp;
            });
        } else { // id_asc
            usort($out, function($a, $b){
                return strnatcasecmp((string)$a['id_number'], (string)$b['id_number']);
            });
        }

        return $out;
    }

    public function get_summary_stats($opts = [])
    {
        $rows = $this->get_gosi_emp1_comparison(100000, 0, $opts);
        $matched = 0; $mismatched = 0;
        foreach ($rows as $r) {
            ($r['status'] === 'مطابق') ? $matched++ : $mismatched++;
        }
        return ['matched' => $matched, 'mismatched' => $mismatched, 'total' => count($rows)];
    }

     public function get_items101(){
        $this->db->order_by('id');
        $query = $this->db->get('items');
        return $query->result_array();
    }

    function add_vacations(){
         date_default_timezone_set('Asia/Riyadh');
           

            $d=date("Y/m/d");
             $time=date("h:i:s");
            $data = array(
                 'username' => $this->input->post('username'),
                 'name' => $this->input->post('name'),
                 'start_date' => $this->input->post('start_date'),
                 'end_date' => $this->input->post('end_date'),
                 'status' =>'1',
                 'date' =>$d,
                 'time' =>$time,
                 'type' =>$this->input->post('type')
               
                  
                 
                
            );
            return $this->db->insert('vacations', $data);
        }


// In models/hr_model.php
/**
 * Fetches all unique types from the reparations table for HR selection.
 */
public function get_all_unique_reparation_types() {
    $this->db->distinct();
    $this->db->select('type');
    $query = $this->db->get('reparations');
    return array_column($query->result_array(), 'type');
}

/**
 * Fetches all unique types from the discounts table for HR selection.
 */
public function get_all_unique_discount_types() {
    $this->db->distinct();
    $this->db->select('type');
    $query = $this->db->get('discounts');
    return array_column($query->result_array(), 'type');
}
public function save_reparation($id, $data)
{
    if (empty($id)) {
        // ID is empty, so this is a new record
        return $this->db->insert('reparations', $data);
    } else {
        // ID exists, so update the existing record
        $this->db->where('id', $id);
        return $this->db->update('reparations', $data);
    }
}
public function insert_reparations_batch($data)
{
    if (empty($data)) {
        return false;
    }
    // insert_batch is a highly efficient way to insert multiple rows
    return $this->db->insert_batch('reparations', $data);
}
           public function update_attendance_summary($id1, $id2)
{
    // قراءة النوع من POST
    $type = (int) $this->input->post('type');

    switch ($type) {
        case 1: // تصفير بصمة منفردة
            $data = ['single_thing' => 0];
            $this->db->where('emp_id', $id1)->where('id_sheet', $id2);
            $this->db->update('attendance_summary', $data);
            return $this->db->affected_rows();

        case 2: // تصفير دقائق التأخير والخروج المبكر
            $data = ['minutes_late' => 0, 'minutes_early' => 0];
            $this->db->where('emp_id', $id1)->where('id_sheet', $id2);
            $this->db->update('attendance_summary', $data);
            return $this->db->affected_rows();

        case 3: // تصفير أيام الغياب
            $data = ['absence' => 0];
            $this->db->where('emp_id', $id1)->where('id_sheet', $id2);
            $this->db->update('attendance_summary', $data);
            return $this->db->affected_rows();

        case 4: // تصفير الجميع
            $data = [
                'absence'       => 0,
                'minutes_late'  => 0,
                'minutes_early' => 0,
                'single_thing'  => 0,
            ];
            $this->db->where('emp_id', $id1)->where('id_sheet', $id2);
            $this->db->update('attendance_summary', $data);
            return $this->db->affected_rows();

        case 5: // إضافة إيقاف راتب في stop_salary بتاريخ/وقت الرياض
            $dt = new DateTime('now', new DateTimeZone('Asia/Riyadh'));
            $insert = [
                'emp_id' => $id1,
                'date'   => $dt->format('Y-m-d'), // تاريخ اليوم بالرياض
                'time'   => $dt->format('H:i:s'), // الوقت الحالي بالرياض

                // إذا حاب تحفظ رقم المسير أيضًا:
                // 'id_sheet' => $id2,
            ];
            $this->db->insert('stop_salary', $insert);
            return $this->db->affected_rows(); // 1 عند النجاح

        default:
            return 0; // نوع غير مدعوم
    }
}






    public function get_employee_by_id($employee_id) {
        // استعلام SQL للبحث عن الموظف بناءً على الرقم الوظيفي
        $this->db->select('subscriber_name');
        $this->db->from('emp1');
        $this->db->where('employee_id', $employee_id);
        
        $query = $this->db->get();
        
        // التحقق إذا كان الموظف موجودًا
        if($query->num_rows() > 0) {
            return $query->row_array(); // إرجاع بيانات الموظف
        } else {
            return false; // إرجاع false إذا لم يتم العثور على الموظف
        }
    }



public function update_employee_status($id, $new_status)
{
    $this->db->where('id', $id);
    return $this->db->update('emp1', ['status' => $new_status]);
}

public function upsert_batch_employees($employees)
{
    $this->db->trans_start();
    $inserted = 0;
    $updated = 0;
    $errors = [];

    foreach ($employees as $index => $emp_data) {
        try {
            // Clean and validate data - FIX: Call the method that exists
            $emp_data = $this->clean_csv_employee_data($emp_data);
            
            // Skip if essential data is missing
            if (empty($emp_data['employee_id']) || empty($emp_data['subscriber_name'])) {
                $errors[] = "سطر " . ($index + 2) . ": missing employee_id or subscriber_name";
                continue;
            }

            // Convert employee_id to string for consistent comparison
            $emp_data['employee_id'] = (string)$emp_data['employee_id'];

            // Check if employee exists by employee_id
            $this->db->where('employee_id', $emp_data['employee_id']);
            $query = $this->db->get('emp1');
            $existing_employee = $query->row();

            if ($existing_employee) {
                // Update existing record
                $this->db->where('id', $existing_employee->id);
                $this->db->update('emp1', $emp_data);
                
                if ($this->db->affected_rows() > 0) {
                    $updated++;
                }
            } else {
                // Insert new record
                $this->db->insert('emp1', $emp_data);
                
                if ($this->db->affected_rows() > 0) {
                    $inserted++;
                }
            }
            
        } catch (Exception $e) {
            $errors[] = "سطر " . ($index + 2) . ": " . $e->getMessage();
            continue;
        }
    }
    
    $this->db->trans_complete();
    
    return [
        'inserted' => $inserted,
        'updated' => $updated,
        'errors' => $errors,
        'total_processed' => count($employees)
    ];
}

// ADD THIS METHOD TO YOUR HR_MODEL
private function clean_csv_employee_data($data)
{
    foreach ($data as $key => $value) {
        // Trim whitespace
        if (is_string($value)) {
            $data[$key] = trim($value);
        }
        
        // Convert empty strings to NULL for database
        if ($value === '') {
            $data[$key] = null;
        }
        
        // Handle numeric fields - remove any non-numeric characters except decimal point
        $numeric_fields = [
            'employee_id', 'id_number', 'base_salary', 'housing_allowance', 
            'commissions', 'other_allowances', 'total_salary', 'salary_subject_to_contribution',
            'n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7', 'n8', 'n9', 'n10', 'n11', 'n12', 'n13'
        ];
        
        if (in_array($key, $numeric_fields) && $value !== null && $value !== '') {
            // Remove any non-numeric characters except decimal point and minus sign
            $cleaned_value = preg_replace('/[^\d.-]/', '', $value);
            $data[$key] = is_numeric($cleaned_value) ? $cleaned_value : null;
        }
        
        // Handle date fields
        $date_fields = ['birth_date', 'joining_date', 'contract_start', 'contract_end', 'Iqama_expiry_date', 'id_expiry'];
        if (in_array($key, $date_fields) && !empty($value)) {
            $data[$key] = $this->parse_csv_date($value);
        }
        
        // Handle phone numbers - remove any non-digit characters
        if ($key === 'phone' && !empty($value)) {
            $data[$key] = preg_replace('/\D/', '', $value);
        }
        
        // Handle boolean/status fields
        if ($key === 'status' && !empty($value)) {
            $data[$key] = in_array(strtolower($value), ['active', '1', 'yes', 'true']) ? 'active' : 'inactive';
        }
        
        if ($key === 'availability_status' && !empty($value)) {
            $data[$key] = in_array(strtolower($value), ['available', '1', 'yes', 'true']) ? 'available' : 'unavailable';
        }
    }
    
    return $data;
}
// ADD THIS METHOD TO YOUR HR_MODEL
private function parse_csv_date($value)
{
    if (empty($value)) return null;
    
    // Remove any existing time portion if present
    $value = trim(explode(' ', $value)[0]);
    
    // Try different date formats - prioritize your CSV format
    $formats = [
        'Y/m/d',  // Your CSV uses this: 1993/08/09
        'Y-m-d',
        'd/m/Y',
        'd-m-Y',
        'm/d/Y', 
        'm-d-Y'
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $value);
        if ($date !== false) {
            // Return only the date in Y-m-d format (no time)
            return $date->format('Y-m-d');
        }
    }
    
    // Final fallback
    $timestamp = strtotime($value);
    return $timestamp !== false ? date('Y-m-d', $timestamp) : null;
}
// ADD THIS METHOD TO YOUR HR_MODEL

public function process_insurance_discounts_batch($discounts_data, $update_existing = false)
{
    $this->db->trans_start();
    $inserted = 0;
    $updated = 0;
    $errors = [];

    // Use the correct table name: insurance_discount
    $table_name = 'insurance_discount';

    foreach ($discounts_data as $index => $discount_data) {
        try {
            // Validate required fields
            if (empty($discount_data['n1']) || empty($discount_data['n2'])) {
                $errors[] = "سطر " . ($index + 2) . ": missing employee ID or name";
                continue;
            }

            // Check if record exists (based on n1 - employee ID)
            $this->db->where('n1', $discount_data['n1']);
            $query = $this->db->get($table_name);
            $existing_record = $query->row();

            if ($existing_record && $update_existing) {
                // Update existing record
                $this->db->where('id', $existing_record->id);
                $this->db->update($table_name, $discount_data);
                
                if ($this->db->affected_rows() > 0) {
                    $updated++;
                } else {
                    $errors[] = "سطر " . ($index + 2) . ": no changes made for employee " . $discount_data['n1'];
                }
            } elseif (!$existing_record) {
                // Insert new record
                $this->db->insert($table_name, $discount_data);
                
                if ($this->db->affected_rows() > 0) {
                    $inserted++;
                } else {
                    $errors[] = "سطر " . ($index + 2) . ": failed to insert record for employee " . $discount_data['n1'];
                }
            } else {
                // Record exists but update_existing is false
                $errors[] = "سطر " . ($index + 2) . ": record exists (n1: " . $discount_data['n1'] . ") - use update option";
            }
            
        } catch (Exception $e) {
            $errors[] = "سطر " . ($index + 2) . ": " . $e->getMessage();
            continue;
        }
    }
    
    $this->db->trans_complete();
    
    if ($this->db->trans_status() === FALSE) {
        $errors[] = "فشل في معاملة قاعدة البيانات";
    }
    
    return [
        'inserted' => $inserted,
        'updated' => $updated,
        'errors' => $errors,
        'total_processed' => count($discounts_data)
    ];
}
     public function get_employees() {
        $this->db->select('*');
        $this->db->from('emp1');
        $query = $this->db->get();
        return $query->result();
    }

     function get_attendance_summary($id){
      
        $sql = "select * from attendance_summary where id_sheet=$id;";
        $query = $this->db->query($sql);
        return $query->result();
    }

// Add these methods to your hr_model


public function get_deductions($employee_id, $sheet_id)
{
    $this->db->where('emp_id', $employee_id);
    $this->db->where('sheet_id', $sheet_id);
    $this->db->order_by('date', 'ASC');
    return $this->db->get('discounts')->result();
}

public function get_additions($employee_id, $sheet_id)
{
    $this->db->where('emp_id', $employee_id);
    $this->db->where('sheet_id', $sheet_id);
    $this->db->order_by('date', 'ASC');
    return $this->db->get('reparations')->result();
}

// For more detailed per-day breakdown (optional)
public function get_deductions_detailed($employee_id, $sheet_id)
{
    $this->db->where('emp_id', $employee_id);
    $this->db->where('sheet_id', $sheet_id);
    $this->db->order_by('discount_date', 'ASC');
    return $this->db->get('discounts')->result();
}

public function get_additions_detailed($employee_id, $sheet_id)
{
    $this->db->where('emp_id', $employee_id);
    $this->db->where('sheet_id', $sheet_id);
    $this->db->order_by('reparation_date', 'ASC');
    return $this->db->get('reparations')->result();
}


public function get_all_exemptions()
    {
        $this->db->select('ex.id, ex.n1 as employee_id, ex.name, emp.subscriber_name');
        $this->db->from('exemption AS ex');
        $this->db->join('emp1 AS emp', 'emp.employee_id = ex.n1', 'left');
        $this->db->order_by('emp.subscriber_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
public function update_resigned_employee_statuses()
    {
        // This query joins emp1 with orders_emp
        // It looks for employees who are:
        // 1. Still 'active' in emp1
        // 2. Have a Resignation request (type=1)
        // 3. That request is Approved (status=2)
        // 4. Their last working day is in the past ( < CURDATE())
        
        $sql = "UPDATE emp1 e
                JOIN orders_emp o ON e.employee_id = o.emp_id
                SET e.status = 'resigned'
                WHERE e.status = 'active'
                  AND o.type = 1
                  AND o.status = 2
                  AND o.date_of_the_last_working < CURDATE()";
        
        $this->db->query($sql);
        
        return $this->db->affected_rows();
    }
    /**
     * (READ) Gets a single exemption record by its ID.
     */
    public function get_exemption_by_id($id)
    {
        $query = $this->db->get_where('exemption', ['id' => $id]);
        return $query->row_array();
    }

    /**
     * (READ) Checks if an exemption already exists for an employee.
     * @param string $employee_id
     * @return bool
     */
    public function check_exemption_exists($employee_id)
    {
        $this->db->where('n1', $employee_id);
        $this->db->from('exemption');
        return $this->db->count_all_results() > 0;
    }

    /**
     * (CREATE) Adds a new exemption record.
     */
    public function add_exemption($data)
    {
        return $this->db->insert('exemption', $data);
    }

    /**
     * (UPDATE) Updates an existing exemption record.
     */
    public function update_exemption($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('exemption', $data);
    }

    /**
     * (DELETE) Deletes an exemption record.
     */
    public function delete_exemption($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('exemption');
    }
public function get_salary_calculation_details($employee_id_input, $sheet_id = 0)
{
    // [0] Reset Query Builder to ensure no previous errors or debug code interfere
    $this->db->reset_query(); 

    // [1] Get employee salary information from emp1 table
    $this->db->select('total_salary, base_salary, housing_allowance, other_allowances, commissions, nationality');
    
    // CORRECT COLUMN: employee_id (not emp_id)
    $this->db->where('employee_id', $employee_id_input); 
    
    // Ensure we don't get terminated employees
    $this->db->where_not_in('status', ['-1', '3', '-2']); 
    
    $employee = $this->db->get('emp1')->row();
    
    // Return empty if employee not found
    if (!$employee) {
        return [];
    }
    
    // Cast variables
    $total_salary = (float)$employee->total_salary;
    $base_salary = (float)$employee->base_salary;
    $housing_allowance = (float)$employee->housing_allowance;
    $other_allowances = (float)$employee->other_allowances;
    $commissions = (float)$employee->commissions;
    $nationality = $employee->nationality ?? '';
    
    // [2] Daily salary calculation
    $daily_salary = $total_salary / 30;
    
   // [3] Get actual working hours
    // IMPORTANT: Clear previous query again before starting new one
    $this->db->reset_query();
    
    /* --- COMMENTING OUT DYNAMIC HOURS TO FORCE 8 HOURS GLOBALLY ---
    $this->db->select('working_hours');
    $this->db->where('emp_id', $employee_id_input); 
    $rules = $this->db->get('work_restrictions')->row();
    
    $actual_working_hours = 8.0; 
    if ($rules && !empty($rules->working_hours)) {
        $wh_raw = trim((string)$rules->working_hours);
        if (strpos($wh_raw, ':') !== false) {
            $parts = array_map('intval', explode(':', $wh_raw));
            $h = $parts[0] ?? 0; $m = $parts[1] ?? 0; $s = $parts[2] ?? 0;
            $actual_working_hours = $h + ($m/60) + ($s/3600);
        } else {
            $actual_working_hours = floatval($wh_raw);
        }
    }
    */
    
    // FORCE STRICT 8 HOURS FOR FINANCIAL MATH
    $actual_working_hours = 8.0; 
    if ($actual_working_hours <= 0) { $actual_working_hours = 8.0; }
    
    // [4] Calculate Rates
    $hourly_salary = $daily_salary / $actual_working_hours;
    $minute_salary = $hourly_salary / 60;
    
    // [4] Calculate Rates
    $hourly_salary = $daily_salary / $actual_working_hours;
    $minute_salary = $hourly_salary / 60;
    
    // [5] Attendance Deductions
    $absence_deduct = 0; $late_deduct = 0; $early_deduct = 0; $single_punch_deduct = 0;
    $absence_days = 0; $minutes_late = 0; $minutes_early = 0; $single_punch_days = 0;

    if ($sheet_id > 0) {
        $this->db->reset_query(); // Clear before next query
        $this->db->where('emp_id', $employee_id_input);
        $this->db->where('id_sheet', $sheet_id);
        $summary = $this->db->get('attendance_summary')->row();
        
        $absence_days = $summary ? (float)$summary->absence : 0;
        $minutes_late = $summary ? (int)$summary->minutes_late : 0;
        $minutes_early = $summary ? (int)$summary->minutes_early : 0;
        $single_punch_days = $summary ? (int)$summary->single_thing : 0; 
        
        $absence_deduct = $daily_salary * $absence_days;
        $late_deduct = $minute_salary * $minutes_late;
        $early_deduct = $minute_salary * $minutes_early;
        $single_punch_deduct = $daily_salary * $single_punch_days;
    }
    
    $attendance_total_deduct = $absence_deduct + $late_deduct + $early_deduct + $single_punch_deduct;
    
    // [6] GOSI
    $gosi_deduction = 0.0;
    $gosi_rate = 0.0;
    if ($nationality === 'سعودي' || $nationality === 'Saudi') {
        $this->db->reset_query(); // Clear before next query
        $this->db->select('n3 as discount_rate');
        $this->db->where('n1', $employee_id_input);
        $insurance = $this->db->get('insurance_discount')->row();
        
        $gosi_rate = $insurance ? (float)$insurance->discount_rate : 0.0;
        $gosi_base = $base_salary + $housing_allowance;
        $gosi_deduction = $gosi_base * $gosi_rate;
    }
    
    // [7] Other Discounts
    $other_discounts_amount = 0;
    if ($sheet_id > 0) {
        $this->db->reset_query(); // Clear before next query
        $this->db->select('SUM(amount) as total_discounts');
        $this->db->where('emp_id', $employee_id_input);
        $this->db->where('sheet_id', $sheet_id);
        $discounts = $this->db->get('discounts')->row();
        $other_discounts_amount = $discounts->total_discounts ?? 0;
    }
    
    // [8] Reparations
    $reparations_amount = 0;
    if ($sheet_id > 0) {
        $this->db->reset_query(); // Clear before next query
        $this->db->select('SUM(amount) as total_reparations');
        $this->db->where('emp_id', $employee_id_input);
        $this->db->where('sheet_id', $sheet_id);
        $reparations = $this->db->get('reparations')->row();
        $reparations_amount = $reparations->total_reparations ?? 0;
    }
    
    // [9] Final Net
    $total_deductions = $attendance_total_deduct + $other_discounts_amount + $gosi_deduction;
    $net_salary = $total_salary + $reparations_amount - $total_deductions;
    
    return [
        'total_salary' => $total_salary,
        'base_salary' => $base_salary,
        'housing_allowance' => $housing_allowance,
        'other_allowances' => $other_allowances,
        'commissions' => $commissions,
        'nationality' => $nationality,
        
        'daily_salary' => $daily_salary,
        'actual_hourly_salary' => $hourly_salary,
        'actual_minute_salary' => $minute_salary,
        'actual_working_hours' => $actual_working_hours,
        'single_punch_days' => $single_punch_days,      // The Count
        'single_punch_deduct' => $single_punch_deduct,  // The Money Amount
        'attendance_total_deduct' => $attendance_total_deduct,
        'gosi_deduction' => $gosi_deduction,
        'other_discounts' => $other_discounts_amount,
        'reparations' => $reparations_amount,
        
        'total_deductions' => $total_deductions,
        'net_salary' => $net_salary
    ];
}
public function get_attendance_summary_for_employees($employee_ids, $sheet_id) {
    if (empty($employee_ids)) return [];
    $this->db->from('attendance_summary');
    $this->db->where_in('emp_id', $employee_ids);
    $this->db->where('id_sheet', $sheet_id);
    $query = $this->db->get();
    
    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->emp_id] = $row;
    }
    return $map;
}

public function get_unique_reparation_types($sheet_id) {
    $sheet = $this->db->get_where('salary_sheet', ['id' => $sheet_id])->row();
    $start = $sheet ? $sheet->start_date : date('Y-m-01');
    $end   = $sheet ? $sheet->end_date : date('Y-m-t');

    $this->db->distinct();
    $this->db->select('type');
    
    $this->db->group_start();
        $this->db->where('sheet_id', $sheet_id);
        // Date Range Check Only (No Recurring)
        $this->db->or_group_start();
            $this->db->where('reparation_date >=', $start);
            $this->db->where('reparation_date <=', $end);
        $this->db->group_end();
    $this->db->group_end();
    
    $query = $this->db->get('reparations');
    return array_column($query->result_array(), 'type');
}

// 2. Get Unique Deduction Column Headers (e.g., "Damage", "Loan")
public function get_unique_discount_types($sheet_id) {
    $this->db->distinct();
    $this->db->select('type');
    
    // Logic: Match Sheet ID OR is_recurring = 1
    $this->db->group_start();
        $this->db->where('sheet_id', $sheet_id);
        $this->db->or_where('is_recurring', 1);
    $this->db->group_end();
    
    $query = $this->db->get('discounts');
    return array_column($query->result_array(), 'type');
}

// 3. Get Detailed Additions Map: [emp_id][ColumnName] = Amount
public function get_reparations_detailed_map($sheet_id) {
    $sheet = $this->db->get_where('salary_sheet', ['id' => $sheet_id])->row();
    $start = $sheet ? $sheet->start_date : date('Y-m-01');
    $end   = $sheet ? $sheet->end_date : date('Y-m-t');

    $this->db->select('emp_id, type, SUM(amount) as total');
    
    $this->db->group_start();
        $this->db->where('sheet_id', $sheet_id);
        // Date Range Check Only (No Recurring)
        $this->db->or_group_start();
            $this->db->where('reparation_date >=', $start);
            $this->db->where('reparation_date <=', $end);
        $this->db->group_end();
    $this->db->group_end();
    
    $this->db->group_by(['emp_id', 'type']);
    $query = $this->db->get('reparations');
    
    $map = [];
    foreach($query->result() as $row) {
        $map[$row->emp_id][$row->type] = (float)$row->total;
    }
    return $map;
}
public function get_payroll_descriptions($date_input)
{
    // --- STEP 1: PARSE THE DATE (Handle Arabic Text) ---
    $start_date = '';
    $end_date = '';

    // Check if it's a standard date (e.g., 2026-01-01)
    $timestamp = strtotime($date_input);
    
    if ($timestamp && date('Y', $timestamp) > 1970) {
        // It's a normal date
        $start_date = date('Y-m-01', $timestamp);
        $end_date   = date('Y-m-t', $timestamp);
    } else {
        // It's likely Arabic Text like "مسير راتب شهر يناير 2026"
        // We need to extract the Month and Year manually
        
        $arabic_months = [
            'يناير' => '01', 'فبراير' => '02', 'مارس' => '03', 'أبريل' => '04',
            'مايو' => '05', 'يونيو' => '06', 'يوليو' => '07', 'أغسطس' => '08',
            'سبتمبر' => '09', 'أكتوبر' => '10', 'نوفمبر' => '11', 'ديسمبر' => '12'
        ];

        $found_month = '01'; // Default
        foreach ($arabic_months as $ar_name => $en_num) {
            if (mb_strpos($date_input, $ar_name) !== false) {
                $found_month = $en_num;
                break;
            }
        }

        $found_year = date('Y'); // Default
        if (preg_match('/[0-9]{4}/', $date_input, $matches)) {
            $found_year = $matches[0];
        }

        // Construct the range
        $start_date = "$found_year-$found_month-01";
        $end_date   = date("Y-m-t", strtotime($start_date));
    }

    // --- STEP 2: RUN QUERIES ---
    $results = [];

    // A. Discounts
    $sql_disc = "SELECT emp_id, GROUP_CONCAT(DISTINCT type SEPARATOR ' + ') as desc_text 
                 FROM orders.discounts 
                 WHERE discount_date >= ? AND discount_date <= ? 
                 GROUP BY emp_id";
    $query_disc = $this->db->query($sql_disc, array($start_date, $end_date));
    if ($query_disc) {
        foreach ($query_disc->result_array() as $row) {
            $results[trim($row['emp_id'])]['discount_note'] = $row['desc_text'];
        }
    }

    // B. Reparations
    $sql_rep = "SELECT emp_id, GROUP_CONCAT(DISTINCT type SEPARATOR ' + ') as desc_text 
                FROM orders.reparations 
                WHERE reparation_date >= ? AND reparation_date <= ? 
                GROUP BY emp_id";
    $query_rep = $this->db->query($sql_rep, array($start_date, $end_date));
    if ($query_rep) {
        foreach ($query_rep->result_array() as $row) {
            $results[trim($row['emp_id'])]['reparation_note'] = $row['desc_text'];
        }
    }

    return $results;
}
// 4. Get Detailed Deductions Map: [emp_id][ColumnName] = Amount
public function get_discounts_detailed_map($sheet_id) {
    $this->db->select('emp_id, type, SUM(amount) as total');
    
    // Logic: Match Sheet ID OR is_recurring = 1
    $this->db->group_start();
        $this->db->where('sheet_id', $sheet_id);
        $this->db->or_where('is_recurring', 1);
    $this->db->group_end();
    
    $this->db->group_by(['emp_id', 'type']);
    $query = $this->db->get('discounts');
    
    $map = [];
    foreach($query->result() as $row) {
        $map[$row->emp_id][$row->type] = (float)$row->total;
    }
    return $map;
}
public function get_discounts_for_employees($employee_ids, $sheet_id, $start_date, $end_date) {
    if (empty($employee_ids)) return [];

    $this->db->select('emp_id, SUM(amount) as total_amount');
    $this->db->from('discounts');
    $this->db->where_in('emp_id', $employee_ids);
    
    // --- UPDATED LOGIC: Strict Sheet ID Check OR Recurring ---
    $this->db->group_start();
        // Case 1: One-Time Discount (Must match this specific Sheet ID)
        $this->db->group_start();
            $this->db->where('sheet_id', $sheet_id);
            $this->db->where('is_recurring', 0); // Explicitly 0 or NULL if default
        $this->db->group_end();

        // Case 2: Recurring Discount (Always applies, ignores Sheet ID)
        $this->db->or_where('is_recurring', 1);
    $this->db->group_end();
    // -------------------------------------------

    $this->db->group_by('emp_id');
    
    $query = $this->db->get();

    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->emp_id] = (float)$row->total_amount;
    }
    return $map;
}
// In application/models/hr_model.php
// In hr_model.php

public function get_distinct_employees_field($field)
{
    $this->db->distinct();
    $this->db->select($field);
    $this->db->from('emp1');
    $this->db->where('status', 'active');
    $this->db->where("$field IS NOT NULL");
    $this->db->where("$field !=", '');
    $this->db->order_by($field, 'ASC');
    $query = $this->db->get();
    
    // Returns a simple array of strings: ['Riyadh', 'Jeddah', ...]
    return array_column($query->result_array(), $field);
}
public function get_reparations_for_employees($employee_ids, $sheet_id, $start_date, $end_date) {
    if (empty($employee_ids)) return [];

    $this->db->select('emp_id, SUM(amount) as total_amount');
    $this->db->from('reparations');
    $this->db->where_in('emp_id', $employee_ids);

    $this->db->group_start();
        $this->db->where('sheet_id', $sheet_id);
        // Date Range Check Only (No Recurring)
        $this->db->or_group_start();
            $this->db->where('reparation_date >=', $start_date);
            $this->db->where('reparation_date <=', $end_date);
        $this->db->group_end();
    $this->db->group_end();

    $this->db->group_by('emp_id');
    $query = $this->db->get();

    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->emp_id] = (float)$row->total_amount;
    }
    return $map;
}
// In application/models/hr_model.php

public function get_subordinate_leave_stats($manager_id, $limit_percentage = 25)
{
    $today = date('Y-m-d');

    // 1. Get List of Subordinates (plus the manager themselves if they belong to a dept)
    // We use your existing hierarchy function
    $subordinates = $this->get_all_subordinates_ids($manager_id);
    
    // If no subordinates, return empty (unless you want to show just their own dept)
    if (empty($subordinates)) {
        return []; 
    }

    // 2. Get Total Active Employees (Filtered by Subordinates List)
    $this->db->select('n1 as dept_name, COUNT(*) as total_count');
    $this->db->from('emp1');
    $this->db->where('status', 'active');
    $this->db->where_in('employee_id', $subordinates); // KEY FILTER
    $this->db->group_by('n1');
    $total_query = $this->db->get()->result_array();

    // 3. Get Employees Currently on Approved Leave (Filtered by Subordinates List)
    $this->db->select('e.n1 as dept_name, COUNT(o.id) as on_leave_count');
    $this->db->from('orders_emp o');
    $this->db->join('emp1 e', 'e.employee_id = o.emp_id');
    $this->db->where('o.type', 5); // Vacation
    $this->db->where('o.status', '2'); // Approved
    $this->db->where('o.vac_start <=', $today);
    $this->db->where('o.vac_end >=', $today);
    $this->db->where_in('o.emp_id', $subordinates); // KEY FILTER
    $this->db->group_by('e.n1');
    $leave_query = $this->db->get()->result_array();

    // 4. Merge & Calculate Stats
    $stats = [];
    $leave_map = [];
    foreach ($leave_query as $row) {
        $leave_map[$row['dept_name']] = $row['on_leave_count'];
    }

    foreach ($total_query as $row) {
        $dept = $row['dept_name'] ?: 'غير محدد';
        $total = (int)$row['total_count'];
        $on_leave = (int)($leave_map[$dept] ?? 0);
        
        $max_allowed = ceil(($total * $limit_percentage) / 100);
        $remaining_slots = max(0, $max_allowed - $on_leave);
        $usage_percent = ($total > 0) ? round(($on_leave / $total) * 100, 1) : 0;
        
        $status_color = 'success';
        if ($on_leave >= $max_allowed) $status_color = 'danger';
        elseif ($on_leave >= ($max_allowed * 0.8)) $status_color = 'warning';

        $stats[] = [
            'department'      => $dept,
            'total_employees' => $total,
            'on_leave'        => $on_leave,
            'max_allowed'     => $max_allowed,
            'remaining_slots' => $remaining_slots,
            'usage_percent'   => $usage_percent,
            'status_color'    => $status_color
        ];
    }

    return $stats;
}
// In hr_model.php

public function get_department_leave_stats($limit_percentage = 25)
{
    $today = date('Y-m-d');

    // 1. Get Total Active Employees per Department
    $this->db->select('n1 as dept_name, COUNT(*) as total_count');
    $this->db->from('emp1');
    $this->db->where('status', 'active');
    $this->db->group_by('n1');
    $total_query = $this->db->get()->result_array();

    // 2. Get Employees Currently on Approved Leave per Department
    $this->db->select('e.n1 as dept_name, COUNT(o.id) as on_leave_count');
    $this->db->from('orders_emp o');
    $this->db->join('emp1 e', 'e.employee_id = o.emp_id');
    $this->db->where('o.type', 5); // Vacation
    $this->db->where('o.status', '2'); // Approved
    $this->db->where('o.vac_start <=', $today);
    $this->db->where('o.vac_end >=', $today);
    $this->db->group_by('e.n1');
    $leave_query = $this->db->get()->result_array();

    // 3. Merge Data
    $stats = [];
    $leave_map = [];
    foreach ($leave_query as $row) {
        $leave_map[$row['dept_name']] = $row['on_leave_count'];
    }

    foreach ($total_query as $row) {
        $dept = $row['dept_name'] ?: 'غير محدد';
        $total = (int)$row['total_count'];
        $on_leave = (int)($leave_map[$dept] ?? 0);
        
        $max_allowed = ceil(($total * $limit_percentage) / 100);
        $remaining_slots = max(0, $max_allowed - $on_leave);
        
        $usage_percent = 0;
        if ($total > 0) {
            $usage_percent = round(($on_leave / $total) * 100, 1);
        }
        
        $status_color = 'success';
        if ($on_leave >= $max_allowed) {
            $status_color = 'danger';
        } elseif ($on_leave >= ($max_allowed * 0.8)) {
            $status_color = 'warning';
        }

        $stats[] = [
            'department'      => $dept,
            'total_employees' => $total,
            'on_leave'        => $on_leave,
            'max_allowed'     => $max_allowed,
            'remaining_slots' => $remaining_slots,
            'usage_percent'   => $usage_percent,
            'status_color'    => $status_color
        ];
    }

    return $stats;
}
// In application/models/hr_model.php
// In application/models/hr_model.php

public function get_distinct_devices()
{
    if (!$this->db->table_exists('attendance_logs')) { return []; }

    // Get actual device names from DB
    $this->db->distinct();
    $this->db->select('terminal_alias');
    $this->db->from('attendance_logs');
    $this->db->where("terminal_alias IS NOT NULL");
    $this->db->where("terminal_alias !=", "");
    $this->db->not_like('terminal_alias', 'Mobile'); // Exclude explicit 'Mobile' strings to avoid duplicates
    $this->db->order_by('terminal_alias', 'ASC');
    $query = $this->db->get();
    
    $devices = array_column($query->result_array(), 'terminal_alias');
    
    // Manually add "Mobile App" to the top of the list
    array_unshift($devices, 'Mobile App');
    
    return $devices;
}
public function get_balances_for_list($employee_ids)
{
    if (empty($employee_ids)) return [];

    $this->db->select('elb.*, e.subscriber_name, e.n1 as department');
    $this->db->from('employee_leave_balances elb');
    $this->db->join('emp1 e', 'e.employee_id = elb.employee_id', 'left');
    $this->db->where_in('elb.employee_id', $employee_ids);
    $this->db->where('elb.year', date('Y')); // Filter by current year
    
    $query = $this->db->get();
    $results = $query->result_array();

    // Reorganize data: EmpID => [Type => Data]
    $organized = [];
    foreach ($results as $row) {
        $emp_id = $row['employee_id'];
        if (!isset($organized[$emp_id])) {
            $organized[$emp_id] = [
                'info' => [
                    'name' => $row['subscriber_name'],
                    'id' => $emp_id,
                    'dept' => $row['department']
                ],
                'balances' => []
            ];
        }
        $organized[$emp_id]['balances'][$row['leave_type_slug']] = [
            'total' => $row['balance_allotted'],
            'used' => $row['balance_consumed'],
            'remaining' => $row['remaining_balance']
        ];
    }
    return $organized;
}
public function save_generated_letter($data)
{
    // Check if exists
    $this->db->where('order_id', $data['order_id']);
    $query = $this->db->get('generated_letters');

    if ($query->num_rows() > 0) {
        // Update
        $this->db->where('order_id', $data['order_id']);
        return $this->db->update('generated_letters', $data);
    } else {
        // Insert
        return $this->db->insert('generated_letters', $data);
    }
}

public function get_letter_data($order_id, $letter_type)
{
    $this->db->where('order_id', $order_id);
    $this->db->where('letter_type', $letter_type);
    $query = $this->db->get('letter_data');
    return $query->row_array();
}
// In application/models/hr_model.php
// ADD THESE TWO NEW FUNCTIONS

public function truncate_discounts_table()
{
    return $this->db->truncate('discounts');
}

public function truncate_reparations_table()
{
    return $this->db->truncate('reparations');
}
public function save_letter_data($data)
{
    // Check if exists
    $this->db->where('order_id', $data['order_id']);
    $this->db->where('letter_type', $data['letter_type']);
    $query = $this->db->get('letter_data');

    if ($query->num_rows() > 0) {
        // Update
        $this->db->where('order_id', $data['order_id']);
        $this->db->where('letter_type', $data['letter_type']);
        return $this->db->update('letter_data', $data);
    } else {
        // Insert
        return $this->db->insert('letter_data', $data);
    }
}
public function get_generated_letter_by_order_id($order_id)
{
    $this->db->where('order_id', $order_id);
    $query = $this->db->get('generated_letters');
    return $query->row_array();
}

/**
 * Retrieves a saved letter using its own primary key.
 * @param int $letter_id The ID from the generated_letters table.
 * @return array|null
 */
public function get_generated_letter_by_id($letter_id)
{
    $this->db->where('id', $letter_id);
    $query = $this->db->get('generated_letters');
    return $query->row_array();
}
// In hr_model.php

public function get_unpaid_leave_days_for_employees($employee_ids, $start_date, $end_date)
{
    if (empty($employee_ids)) {
        return [];
    }

    $this->db->from('orders_emp');
    $this->db->where_in('emp_id', $employee_ids);
    $this->db->where('type', 5); // Leave requests
    $this->db->where('status', '2'); // Approved
    $this->db->where('vac_main_type', 'unpaid'); // Specifically unpaid leave

    // Find leaves that overlap with the payroll period
    $this->db->where("(`vac_start` <= '$end_date' AND `vac_end` >= '$start_date')", NULL, FALSE);
    
    $query = $this->db->get();

    $unpaid_leave_map = array_fill_keys($employee_ids, 0);

    $payroll_start = new DateTime($start_date);
    $payroll_end = new DateTime($end_date);
    
    // Calculate total days in the current payroll sheet/month
    $sheet_total_days = $payroll_end->diff($payroll_start)->days + 1;

    foreach ($query->result() as $leave) {
        $leave_start = new DateTime($leave->vac_start);
        $leave_end = new DateTime($leave->vac_end);

        // Find the actual start and end of the overlap
        $overlap_start = max($payroll_start, $leave_start);
        $overlap_end = min($payroll_end, $leave_end);

        if ($overlap_start <= $overlap_end) {
            // Calculate the number of days in the overlapping period (inclusive)
            $days_in_overlap = $overlap_end->diff($overlap_start)->days + 1;
            $unpaid_leave_map[$leave->emp_id] += $days_in_overlap;
        }
    }

    // --- FIX: Normalize Full Month Unpaid Leave to 30 Days ---
    // If the sheet represents a full month (28-31 days) and the employee 
    // has unpaid leave for the entire duration, cap it at 30 days.
    if ($sheet_total_days >= 28 && $sheet_total_days <= 31) {
        foreach ($unpaid_leave_map as $emp_id => $days) {
            if ($days >= $sheet_total_days) {
                $unpaid_leave_map[$emp_id] = 30;
            }
        }
    }

    return $unpaid_leave_map;
}

/**
 * Gets join dates for employees who are in the new_employees table.
 * @param array $employee_ids
 * @return array Mapped by employee_id
 */
public function get_new_employee_data_for_employees($employee_ids) {
    if (empty($employee_ids)) return [];
    $this->db->select('employee_id, join_date, nationality');
    $this->db->from('new_employees');
    $this->db->where_in('employee_id', $employee_ids);
    $query = $this->db->get();
    
    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->employee_id] = $row;
    }
    return $map;
}

/**
 * Gets insurance discount rates for a list of employees.
 * @param array $employee_ids
 * @return array Mapped by employee_id
 */
public function get_insurance_discounts_for_employees($employee_ids) {
    if (empty($employee_ids)) return [];
    $this->db->select('n1 as employee_id, n3 as discount_rate');
    $this->db->from('insurance_discount');
    $this->db->where_in('n1', $employee_ids);
    $query = $this->db->get();
    
    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->employee_id] = (float)$row->discount_rate;
    }
    return $map;
}

/**
 * Gets a map of employees whose salary is stopped.
 * @param array $employee_ids
 * @return array Mapped by employee_id with value true
 */
// In hr_model.php
// Get list for the table view
// In hr_model.php

public function get_all_stop_salary_requests() {
    // Add ss.stop_date to the select list
    $this->db->select('ss.id, ss.emp_id, ss.sheet_id, ss.start_date, ss.reason, ss.date, ss.time, emp1.subscriber_name as emp_name, salary_sheet.type as sheet_name, salary_sheet.start_date, salary_sheet.end_date');
    $this->db->from('stop_salary ss');
    $this->db->join('emp1', 'emp1.employee_id = ss.emp_id', 'left');
    $this->db->join('salary_sheet', 'salary_sheet.id = ss.sheet_id', 'left');
    $this->db->order_by('ss.id', 'DESC');
    return $this->db->get()->result_array();
}
// In hr_model.php

public function get_salary_sheet_by_date($date)
{
    $this->db->select('id, start_date, end_date');
    $this->db->from('salary_sheet');
    $this->db->where('start_date <=', $date);
    $this->db->where('end_date >=', $date);
    $this->db->limit(1); // Get the first matching sheet
    $query = $this->db->get();
    
    return $query->row_array();
}
// Save (Add/Edit)
public function save_stop_salary_request($id, $data) {
    if (empty($id)) {
        return $this->db->insert('stop_salary', $data);
    } else {
        $this->db->where('id', $id);
        return $this->db->update('stop_salary', $data);
    }
}

// Get single row for Edit Modal
public function get_stop_salary_by_id($id) {
    return $this->db->get_where('stop_salary', ['id' => $id])->row_array();
}

// Delete
public function delete_stop_salary($id) {
    $this->db->where('id', $id);
    return $this->db->delete('stop_salary');
}
// In hr_model.php

public function get_stopped_salaries_for_employees($employee_ids, $sheet_id = null) 
{
    if (empty($employee_ids)) return [];
    
    $this->db->select('emp_id');
    $this->db->from('stop_salary');
    $this->db->where_in('emp_id', $employee_ids);

    // [MODIFIED]: Removed the strict sheet_id check as requested.
    // Now, if a record exists in stop_salary table for an employee, 
    // their salary will be stopped regardless of the sheet_id.
    /* if (!empty($sheet_id)) {
        $this->db->where('sheet_id', $sheet_id);
    }
    */

    $query = $this->db->get();

    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->emp_id] = true;
    }
    return $map;
}
/**
 * Gets a map of employees who are exempt from deductions.
 * @param array $employee_ids
 * @return array Mapped by employee_id with value true
 */
public function get_exemptions_for_employees($employee_ids) {
    if (empty($employee_ids)) return [];
    $this->db->select('n1 as emp_id');
    $this->db->from('exemption');
    $this->db->where_in('n1', $employee_ids);
    $query = $this->db->get();

    $map = [];
    foreach ($query->result() as $row) {
        $map[$row->emp_id] = true;
    }
    return $map;
}

// In hr_model.php



// START: Employee Requests Report Functions (FIXED)
// =========================================================

// 1. Private Query Builder
// =========================================================
// REPLACE THESE 4 FUNCTIONS IN: application/models/hr_model.php
// =========================================================

private function _get_datatables_query_requests()
{
    // 1. Define Searchable Columns
    $column_search = [
        'oe.id', 
        'oe.emp_id', 
        'oe.emp_name', 
        'oe.order_name', 
        'oe.date',
        'emp1.profession'
    ];

    // 2. Select Columns
    $this->db->select('
        oe.id, 
        oe.emp_id, 
        oe.emp_name, 
        oe.order_name, 
        oe.date, 
        oe.status,
        oe.type,
        
        emp1.profession,

        oe.vac_start,
        oe.vac_end,
        oe.vac_days_count,
        oe.vac_main_type,
        oe.vac_type,
        oe.vac_half_date,
        oe.vac_half_period,
        
        oe.mission_date,
        oe.mission_type,
        
        oe.date_of_the_last_working,
        oe.correction_date
    ');

    $this->db->from('orders_emp as oe');
    $this->db->join('emp1', 'oe.emp_id = emp1.employee_id', 'left');

    // --- FILTERS ---
    if ($this->input->post('filter_employee_id')) {
        $this->db->like('oe.emp_id', $this->input->post('filter_employee_id'));
    }
    if ($this->input->post('filter_name')) {
        $this->db->like('oe.emp_name', $this->input->post('filter_name'));
    }
    if ($this->input->post('filter_type')) {
        $this->db->where('oe.type', $this->input->post('filter_type'));
    }
    if ($this->input->post('filter_status') !== '' && $this->input->post('filter_status') !== null) {
        $this->db->where('oe.status', $this->input->post('filter_status'));
    }

    // --- DYNAMIC START DATE FILTER ---
    if ($this->input->post('filter_start_date')) {
        $sd = $this->input->post('filter_start_date');
        $this->db->group_start();
            $this->db->where("(oe.type = 5 AND DATE(oe.vac_start) >= '$sd')", NULL, FALSE);
            $this->db->or_where("(oe.type = 1 AND DATE(oe.date_of_the_last_working) >= '$sd')", NULL, FALSE);
            $this->db->or_where("(oe.type = 9 AND DATE(oe.mission_date) >= '$sd')", NULL, FALSE);
            $this->db->or_where("(oe.type = 2 AND DATE(oe.correction_date) >= '$sd')", NULL, FALSE);
            // Default/Type 7 checks creation date
            $this->db->or_where("(oe.type NOT IN (1, 2, 5, 9) AND DATE(oe.date) >= '$sd')", NULL, FALSE);
        $this->db->group_end();
    }

    // --- DYNAMIC END DATE FILTER ---
    if ($this->input->post('filter_end_date')) {
        $ed = $this->input->post('filter_end_date');
        $this->db->group_start();
            $this->db->where("(oe.type = 5 AND DATE(oe.vac_start) <= '$ed')", NULL, FALSE);
            $this->db->or_where("(oe.type = 1 AND DATE(oe.date_of_the_last_working) <= '$ed')", NULL, FALSE);
            $this->db->or_where("(oe.type = 9 AND DATE(oe.mission_date) <= '$ed')", NULL, FALSE);
            $this->db->or_where("(oe.type = 2 AND DATE(oe.correction_date) <= '$ed')", NULL, FALSE);
            $this->db->or_where("(oe.type NOT IN (1, 2, 5, 9) AND DATE(oe.date) <= '$ed')", NULL, FALSE);
        $this->db->group_end();
    }

    // --- SEARCH BAR ---
    if (!empty($_POST['search']['value'])) {
        $this->db->group_start();
        $i = 0;
        foreach ($column_search as $item) {
            if ($i === 0) {
                $this->db->like($item, $_POST['search']['value']);
            } else {
                $this->db->or_like($item, $_POST['search']['value']);
            }
            $i++;
        }
        $this->db->group_end();
    }

    // --- SORTING ---
    if (isset($_POST['order'])) {
        $column_index = $_POST['order']['0']['column'];
        $column_name = $_POST['columns'][$column_index]['data'];
        $dir = $_POST['order']['0']['dir']; // asc or desc
        
        if ($column_name == 'profession') {
            $this->db->order_by('emp1.profession', $dir);
        } 
        elseif ($column_name == 'effective_start_date') {
             // CRITICAL FIX: Added FALSE as 3rd parameter to prevent SQL Error
             $this->db->order_by("CASE 
                WHEN oe.type = 5 THEN oe.vac_start
                WHEN oe.type = 1 THEN oe.date_of_the_last_working
                WHEN oe.type = 9 THEN oe.mission_date
                WHEN oe.type = 2 THEN oe.correction_date
                ELSE oe.date 
             END", $dir, FALSE);
        }
        else {
            // Default handling
            if (in_array($column_name, ['id', 'emp_id', 'emp_name', 'date', 'status'])) {
                $column_name = 'oe.' . $column_name;
            }
            // Fallback for simple columns
            if (strpos($column_name, '.') === false && strpos($column_name, 'emp1') === false) {
                 $column_name = 'oe.' . $column_name;
            }
            $this->db->order_by($column_name, $dir);
        }
    } else {
        $this->db->order_by('oe.id', 'DESC');
    }
}

public function get_datatables_requests() {
    $this->_get_datatables_query_requests();
    if ($_POST['length'] != -1) {
        $this->db->limit($_POST['length'], $_POST['start']);
    }
    return $this->db->get()->result();
}

public function count_filtered_requests() {
    $this->_get_datatables_query_requests();
    return $this->db->count_all_results();
}

public function count_all_requests() {
    $this->db->from('orders_emp');
    return $this->db->count_all_results();
}
public function get_employees_by_company($company_code)
{
    if (empty($company_code)) {
        return [];
    }
    
    // Selects in the format needed by the attendance dropdown ('username', 'name')
    $this->db->select('employee_id as username, subscriber_name as name, status');
    $this->db->from('emp1');
    $this->db->where('n13', $company_code); // n13 is the company code
    $this->db->where('status !=', 'deleted');
    $this->db->order_by('subscriber_name', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}

/**
 * Check if an employee belongs to a specific company code.
 */
public function is_employee_in_company($employee_id, $company_code)
{
    if (empty($employee_id) || empty($company_code)) {
        return false;
    }
    
    $this->db->select('id');
    $this->db->from('emp1');
    $this->db->where('employee_id', $employee_id);
    $this->db->where('n13', $company_code);
    $this->db->limit(1);
    
    $query = $this->db->get();
    return $query->num_rows() > 0;
}
   public function get_violations_summary($id, $employee_id = null)
{
    $params = [$id];
    $sql = "
        SELECT *
        FROM attendance_summary
        WHERE id_sheet = ?
          AND (
                COALESCE(absence, 0) <> 0
             OR COALESCE(minutes_late, 0) <> 0
             OR COALESCE(minutes_early, 0) <> 0
             OR COALESCE(single_thing, 0) <> 0
          )
    ";

    // If an employee ID is provided, add it to the query
    if (!empty($employee_id)) {
        $sql .= " AND emp_id = ? ";
        $params[] = $employee_id;
    }

    $sql .= " ORDER BY emp_id ";
    
    return $this->db->query($sql, $params)->result();
}


     function get_salary_sheet($id){
      
        $sql = "select * from salary_sheet where id=$id;";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

// Add these functions to hr_model.php
public function insert_violation($data)
{
    // 1. Insert into Employee Violations
    $this->db->insert('orders.employee_violations', $data);
    $insert_id = $this->db->insert_id();

    // 2. Prepare data for Discounts table
    // We fetch the session name for the 'name' field in discounts
    $user_name = $this->session->userdata('name') ?: 'System'; 
    $username = $data['created_by'] ?? $this->session->userdata('username');

    $discount_data = [
        'emp_id'        => $data['employee_id'],
        'emp_name'      => $data['emp_name'],
        'type'          => 'مخالفة', // Setting type as Violation
        'amount'        => $data['amount'],
        'username'      => $username,
        'name'          => $user_name,
        'date'          => date('Y-m-d'), // Transaction Date
        'time'          => date('H:i:s'), // Transaction Time
        'sheet_id'      => 0, // Default or find active sheet if needed
        'notes'         => 'خصومات أخرى', // As requested
        'discount_date' => $data['violation_date'], // Date of the violation/discount effect
        'is_recurring'  => 0
    ];

    // 3. Insert into Discounts
    $this->db->insert('orders.discounts', $discount_data);

    return $insert_id;
}
public function delete_violation($id)
{
    $violation = $this->db->get_where('orders.employee_violations', ['id' => $id])->row();

    if ($violation) {
        // Delete from Discounts where details match
        $this->db->where('emp_id', $violation->employee_id);
        $this->db->where('discount_date', $violation->violation_date);
        $this->db->where('type', 'مخالفة'); // Removed amount check here as well
        $this->db->delete('orders.discounts');

        // Delete the violation itself
        $this->db->where('id', $id);
        return $this->db->delete('orders.employee_violations');
    }
    return false;
}
public function update_violation_with_discount($id, $new_data)
{
    // 1. Get the OLD data before updating (to find the linked discount)
    $old_violation = $this->db->get_where('orders.employee_violations', ['id' => $id])->row();

    if (!$old_violation) return false;

    // 2. Update the Violation Table
    $this->db->where('id', $id);
    $updated = $this->db->update('orders.employee_violations', $new_data);

    if ($updated) {
        // 3. Update the Discounts Table
        // FIX: Removed the strict 'amount' check to prevent failed matches due to decimal differences
        $this->db->where('emp_id', $old_violation->employee_id);
        $this->db->where('discount_date', $old_violation->violation_date);
        $this->db->where('type', 'مخالفة'); // Look for the specific type set during insert
        
        // Set the NEW values
        $discount_update = [
            'amount' => $new_data['amount'],
            'discount_date' => $new_data['violation_date']
        ];
        
        $this->db->update('orders.discounts', $discount_update);
        return true;
    }

    return false;
}
public function save_discount($id, $data)
{
    if (empty($id)) {
        return $this->db->insert('discounts', $data);
    } else {
        $this->db->where('id', $id);
        return $this->db->update('discounts', $data);
    }
}
 function get_salary_salary_sheet(){
      
        $sql = "select * from salary_sheet;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
public function delete_discount($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('discounts');
}

public function get_discount_by_id($id)
{
    $query = $this->db->get_where('discounts', ['id' => $id]);
    return $query->row_array();
}

public function insert_discounts_batch($data)
{
    if (empty($data)) {
        return false;
    }
    return $this->db->insert_batch('discounts', $data);
}
    function get_salary_vacations(){
      
        $sql = "select * from vacations;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

     function get_salary_discounts(){
      
        $sql = "select * from discounts;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function get_salary_reparations(){
      
        $sql = "select * from reparations;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    // Add these functions to hr_model.php



public function delete_reparation($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('reparations');
}

public function get_reparation_by_id($id)
{
    $query = $this->db->get_where('reparations', ['id' => $id]);
    return $query->row_array();
}



// 1. Helper to get unique values for dropdowns
public function get_unique_values($column)
{
    $this->db->distinct();
    $this->db->select($column);
    $this->db->from('emp1');
    $this->db->where("$column IS NOT NULL");
    $this->db->where("$column !=", '');
    $this->db->order_by($column, 'ASC');
    $query = $this->db->get();
    return array_column($query->result_array(), $column);
}

// 2. Update the DataTable Query Logic
private function _get_datatables_query()
{
    $this->db->from('emp1');

    // --- Existing Filters ---
    if ($this->input->post('filter_employee_id')) {
        $this->db->like('employee_id', $this->input->post('filter_employee_id'));
    }
    if ($this->input->post('filter_id_number')) {
        $this->db->like('id_number', $this->input->post('filter_id_number'));
    }
    if ($this->input->post('filter_name')) {
        $this->db->like('subscriber_name', $this->input->post('filter_name'));
    }
    
    // --- NEW FILTERS ---
    if ($this->input->post('filter_department')) {
        $this->db->where('n1', $this->input->post('filter_department'));
    }
    if ($this->input->post('filter_company')) {
        $this->db->where('company_name', $this->input->post('filter_company'));
    }
    if ($this->input->post('filter_manager')) {
        $this->db->where('manager', $this->input->post('filter_manager'));
    }
    if ($this->input->post('filter_location')) {
        $this->db->where('location', $this->input->post('filter_location'));
    }
    if ($this->input->post('filter_position')) {
        $this->db->where('profession', $this->input->post('filter_position'));
    }
    if ($this->input->post('filter_nationality')) {
        $this->db->where('nationality', $this->input->post('filter_nationality'));
    }
    if ($this->input->post('filter_gender')) {
        $this->db->where('gender', $this->input->post('filter_gender'));
    }

    // --- Status Filter ---
    $status = $this->input->post('filter_status');
    if ($status && $status !== 'all') {
        $this->db->where('status', $status);
    } else if ($status === 'all') {
        // Show everything except deleted (soft delete)
        $this->db->where('status !=', 'deleted');
    } else {
        // Default behavior
        $this->db->where('status !=', 'deleted');
        $this->db->where('status !=', 'resigned');
    }

    // Sorting
    if (isset($_POST['order'])) {
        $column_index = $_POST['order']['0']['column'];
        $column_name = $_POST['columns'][$column_index]['data'];
        // Disable sorting on 'actions' column
        if($column_name != 'actions') {
            $this->db->order_by($column_name, $_POST['order']['0']['dir']);
        }
    } else {
        $this->db->order_by('id', 'DESC');
    }
}
// In hr_model.php
public function check_for_overlapping_holidays($start_date, $end_date)
{
    $this->db->from('public_holidays');
    // Check if the new range overlaps with any existing range
    $this->db->where("start_date <=", $end_date);
    $this->db->where("end_date >=", $start_date);
    return $this->db->count_all_results() > 0;
}
public function get_datatables_employees()
{
    $this->_get_datatables_query();
    if ($_POST['length'] != -1) {
        $this->db->limit($_POST['length'], $_POST['start']);
    }
    $query = $this->db->get();
    return $query->result();
}
// In hr_model.php

public function get_all_saturday_assignments_mapped()
{
    $this->db->select('employee_id, saturday_date');
    $query = $this->db->get('saturday_work_assignments');
    
    $mapped_data = [];
    foreach ($query->result_array() as $row) {
        // Group all assigned dates under the employee's ID
        $mapped_data[$row['employee_id']][] = $row['saturday_date'];
    }
    return $mapped_data;
}

public function count_filtered_employees()
{
    $this->_get_datatables_query();
    $query = $this->db->get();
    return $query->num_rows();
}

public function count_all_employees()
{
    $this->db->from('emp1');
    return $this->db->count_all_results();
}
public function update_current_salary_sheet($data)
{
    if (empty($data)) {
        log_message('error', 'Empty data passed to update_current_salary_sheet');
        return false;
    }
    
    // Check if record with id=1 exists
    $this->db->where('id', 1);
    $existing = $this->db->get('salary_sheet')->row();
    
    if ($existing) {
        // Update existing record
        $this->db->where('id', 1);
        $result = $this->db->update('salary_sheet', $data);
        log_message('debug', 'Update result: ' . ($result ? 'true' : 'false'));
        log_message('debug', 'Last query: ' . $this->db->last_query());
        return $result;
    } else {
        // Insert new record with id=1
        $data['id'] = 1;
        $result = $this->db->insert('salary_sheet', $data);
        log_message('debug', 'Insert result: ' . ($result ? 'true' : 'false'));
        log_message('debug', 'Last query: ' . $this->db->last_query());
        return $result;
    }
}

    function get_emp1(){
      
        $sql = "select * from emp1 ;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


    function get_emp_residents(){
    $sql = "SELECT * 
            FROM emp1
            WHERE nationality NOT IN ('سعودي', 'القبائل النازحة');";
    $query = $this->db->query($sql);
    return $query->result_array();
}

// دالة مساعدة لتحديد المسؤول من الهيكل التنظيمي
private function get_responsible_from_org($username)
{
    $cols = ['n1','n2','n3','n4','n5','n6','n7'];

    $this->db->select(implode(',', $cols));
    $this->db->from('organizational_structure');
    // ابحث عن المستخدم في أي عمود من n1..n7
    $this->db->group_start();
    foreach ($cols as $c) {
        $this->db->or_where($c, $username);
    }
    $this->db->group_end();

    $rows = $this->db->get()->result_array();
    if (!$rows) return 'hr';

    foreach ($rows as $row) {
        foreach ($cols as $idx => $c) {
            if (isset($row[$c]) && (string)$row[$c] === (string)$username) {
                // إذا كان في n1 → المسؤول hr
                if ($idx === 0) return 'hr';
                // وإلا المسؤول هو العمود السابق n{i-1}
                $prev = $row[$cols[$idx - 1]] ?? '';
                return $prev !== '' ? $prev : 'hr';
            }
        }
    }

    return 'hr';
}

public function add_new_order1($relativePath)
    {
        $orderData = [
            'emp_id'                   => $this->target_employee_id,
            'emp_name'                 => $this->target_employee_name,
            'order_name'               => 'استقالة',
            'status'                   => '0',
            'type'                     => 1,
            'created_by_id'            => $this->session->userdata('username'),
            'date'                     => date('Y-m-d'),
            'time'                     => date('H:i:s'),
            'note'                     => $this->input->post('note'),
            'date_of_the_last_working' => $this->input->post('resign[last_day]'),
            'reason_for_resignation'   => $this->input->post('resign[reason]'),
            'resignation_details'      => $this->input->post('resign[details]'),
            'file'                     => $relativePath
        ];

        if ($this->db->insert('orders_emp', $orderData)) {
            $order_id = $this->db->insert_id();
            $this->create_approval_workflow($order_id, $this->target_employee_id, 1);
            return true;
        }
        return false;
    }

    // Request Type 2: Fingerprint Correction
    public function add_new_order2($relativePath)
    {
        $orderData = [
            'emp_id'                   => $this->target_employee_id,
            'emp_name'                 => $this->target_employee_name,
            'order_name'               => 'تصحيح بصمة',
            'status'                   => '0',
            'type'                     => 2,
            'date'                     => date('Y-m-d'),
            'time'                     => date('H:i:s'),
            'created_by_id'            => $this->session->userdata('username'),
            'note'                     => $this->input->post('note'),
            'correction_date'          => $this->input->post('fp[date]'),
            'attendance_correction'    => $this->input->post('fp[in_time]'),
            'note_on_entry'            => $this->input->post('fp[in_note]'),
            'correction_of_departure'  => $this->input->post('fp[out_time]'),
            'note_on_checkout'         => $this->input->post('fp[out_note]'),
            'reason_for_correction'    => $this->input->post('fp[reason]'),
            'details_of_the_reason'    => $this->input->post('fp[details]'),
            'file'                     => $relativePath
        ];

        if ($this->db->insert('orders_emp', $orderData)) {
            $order_id = $this->db->insert_id();
            $this->create_approval_workflow($order_id, $this->target_employee_id, 2);
            return true;
        }
        return false;
    }

    // Request Type 3: Overtime
   // Request Type 3: Overtime
    public function add_new_order3($file_path = '') 
    {
        // 1. Get Data
        $ot = $this->input->post('ot');
        if (!$ot) {
            $ot = ['type' => 'single', 'date' => date('Y-m-d'), 'hours' => 0, 'paid' => 0, 'reason' => ''];
        }

        // [FIX]: Use the class property set by 'set_target_employee' instead of session
        $emp_id = $this->target_employee_id;
        $emp_name = $this->target_employee_name;

        $data = [
            'emp_id'        => $emp_id,
            'emp_name'      => $emp_name,
            'created_by_id' => $this->session->userdata('username'), // Creator remains the logged-in user (HR)
            'date'          => date('Y-m-d'),
            'time'          => date('H:i:s'),
            'type'          => 3, // Overtime
            'order_name'    => 'عمل إضافي',
            'status'        => '0', // Default to Pending (0)
            
            'ot_type'   => $ot['type'],
            'ot_date'   => ($ot['type'] == 'single') ? $ot['date'] : null,
            'ot_from'   => ($ot['type'] == 'range') ? $ot['from'] : null,
            'ot_to'     => ($ot['type'] == 'range') ? $ot['to'] : null,
            'ot_hours'  => $ot['hours'],
            'ot_paid'   => $ot['paid'],
            'ot_reason' => $ot['reason'],
            'ot_amount' => $this->input->post('ot_amount_calculated') ?? 0.00,
            
            'file'      => $file_path,
        ];

        // 2. Insert into Database
        $insert = $this->db->insert('orders_emp', $data);

        // 3. TRIGGER THE WORKFLOW
        if ($insert) {
            $order_id = $this->db->insert_id(); // Get the new ID
            
            // Call the workflow function for Type 3
            // [FIX]: Pass the correct $emp_id to the workflow
            $this->create_approval_workflow($order_id, $emp_id, 3);
            
            return true;
        }

        return false;
    }

    // Request Type 4: Financial Expenses
    public function add_new_order4($relativePath)
    {
        $expenses = $this->input->post('exp');
        $first_expense = is_array($expenses) ? $expenses[0] : [];

        $orderData = [
            'emp_id'        => $this->target_employee_id,
            'emp_name'      => $this->target_employee_name,
            'order_name'    => 'مصاريف مالية',
            'status'        => '0',
            'type'          => 4,
            'date'          => date('Y-m-d'),
            'time'          => date('H:i:s'),
            'created_by_id'            => $this->session->userdata('username'),
            'note'          => $this->input->post('note'),
            'exp_item_name' => $first_expense['item'] ?? null,
            'exp_amount'    => $first_expense['amount'] ?? null,
            'exp_date'      => $first_expense['date'] ?? null,
            'exp_desc'      => $first_expense['desc'] ?? null,
            'exp_reason'    => $this->input->post('exp_reason'),
            'file'          => $relativePath
        ];

        if ($this->db->insert('orders_emp', $orderData)) {
            $order_id = $this->db->insert_id();
            // Note: In a real application, you might handle multiple expense items.
            // This code saves the first item to the main order table as per your structure.
            $this->create_approval_workflow($order_id, $this->target_employee_id, 4);
            return true;
        }
        return false;
    }

 
public function add_new_order5($relativePath)
{
    $vac_post_data = $this->input->post('vac');
    $day_type = $vac_post_data['day_type'] ?? 'full';
    
    // 1. Ensure leave type is captured. Default to 'unspecified' if blank.
    $leave_type = !empty($vac_post_data['main_type']) ? $vac_post_data['main_type'] : 'unspecified';
    
    // 2. Map English slugs to Arabic descriptive names for clarity
    $leave_type_names = [
        'annual'      => 'سنوية',
        'sick'        => 'مرضية',
        'unpaid'      => ' بدون راتب',
        'maternity'   => 'وضع',
        'marriage'    => 'زواج',
        'death'       => 'وفاة',
        'hajj'        => 'حج',
        'exam'        => 'اختبارات',
        'emergency'   => 'اضطرارية',
        'unspecified' => '(غير محددة)'
    ];

    // 3. Create a clear order_name. If it's an unknown type, it appends the raw text.
    $order_name = isset($leave_type_names[$leave_type]) ? $leave_type_names[$leave_type] : 'إجازة - ' . $leave_type;

    $days_requested = 0;

    if ($day_type === 'half') {
        $days_requested = 0.5;
    } else {
        $start_str = $vac_post_data['start'] ?? null;
        $end_str = $vac_post_data['end'] ?? null;

        if ($start_str && $end_str && $end_str >= $start_str) {
            
            if ($leave_type === 'annual' || $leave_type === 'sick') {
                
                $public_holidays = $this->get_all_holidays_as_dates();
                $start = new DateTime($start_str);
                $end = new DateTime($end_str);
                $end->modify('+1 day');
                $interval = new DateInterval('P1D');
                $date_range = new DatePeriod($start, $interval, $end);

                foreach ($date_range as $date) {
                    $current_date_str = $date->format('Y-m-d');
                    $day_of_week = (int)$date->format('N'); 
                    $is_weekend = ($day_of_week === 5 || $day_of_week === 6);
                    $is_mandatory_saturday = ($day_of_week === 6) && $this->is_mandatory_saturday($this->target_employee_id, $current_date_str);
                    
                    if (in_array($current_date_str, $public_holidays) || ($is_weekend && !$is_mandatory_saturday)) {
                        continue;
                    }
                    $days_requested++;
                }

            } else {
                $start = new DateTime($start_str);
                $end = new DateTime($end_str);
                $days_requested = $end->diff($start)->days + 1;
            }
        }
    }
    
    $orderData = [
        'emp_id' => $this->target_employee_id,
        'emp_name' => $this->target_employee_name,
        // CHANGED: Use the dynamically mapped Arabic name instead of the hardcoded 'إجازة'
        'order_name' => $order_name, 
        'status' => '0',
        'type' => 5,
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'created_by_id' => $this->session->userdata('username'),
        'note' => $this->input->post('note'),
        // CHANGED: Guaranteed to never be blank
        'vac_main_type' => $leave_type, 
        'vac_reason' => $vac_post_data['reason'] ?? null,
        'file' => $relativePath,
        'vac_days_count' => $days_requested,
        'vac_start' => ($day_type === 'full') ? ($vac_post_data['start'] ?? null) : ($vac_post_data['half_date'] ?? null),
        'vac_end' => ($day_type === 'full') ? ($vac_post_data['end'] ?? null) : ($vac_post_data['half_date'] ?? null),
        'vac_half_date' => ($day_type === 'half') ? ($vac_post_data['half_date'] ?? null) : null,
        'vac_half_period' => ($day_type === 'half') ? ($vac_post_data['half_period'] ?? null) : null,
        
        // **NEWLY ADDED LINE TO SAVE THE DELEGATE**
        'delegation_employee_id' => $vac_post_data['delegation_employee_id'] ?? null
    ];

    if ($this->db->insert('orders_emp', $orderData)) {
        $order_id = $this->db->insert_id();
        $this->create_approval_workflow($order_id, $this->target_employee_id, 5, ['leave_type_slug' => $leave_type]);
        return true;
    }
    return false;
}

    public function add_new_order6($relativePath)
    {
        $orderData = [
            'emp_id'       => $this->target_employee_id,
            'emp_name'     => $this->target_employee_name,
            'order_name'   => 'طلب عُهدة',
            'status'       => '0',
            'type'         => 6,
            'date'         => date('Y-m-d'),
            'time'         => date('H:i:s'),
            'created_by_id'            => $this->session->userdata('username'),
            'note'         => $this->input->post('note'),
            'asset_type'   => $this->input->post('asset[type]'),
            'asset_desc'   => $this->input->post('asset[desc]'),
            'asset_reason' => $this->input->post('asset[reason]'),
            'file'         => $relativePath
        ];

        if ($this->db->insert('orders_emp', $orderData)) {
            $order_id = $this->db->insert_id();
            $this->create_approval_workflow($order_id, $this->target_employee_id, 6);
            return true;
        }
        return false;
    }

    // Request Type 7: Letter Request
    public function add_new_order7($relativePath)
    {
        $orderData = [
            'emp_id'        => $this->target_employee_id,
            'emp_name'      => $this->target_employee_name,
            'order_name'    => 'طلب خطاب',
            'status'        => '0',
            'type'          => 7,
            'date'          => date('Y-m-d'),
            'time'          => date('H:i:s'),
            'note'          => $this->input->post('note'),
            'letter_type'   => $this->input->post('letter[type]'),
            'letter_to_en'  => $this->input->post('letter[to_en]'),
            'letter_to_ar'  => $this->input->post('letter[to_ar]'),
            'letter_reason' => $this->input->post('letter[reason]'),
            'file'          => $relativePath
        ];

        if ($this->db->insert('orders_emp', $orderData)) {
            $order_id = $this->db->insert_id();
            $this->create_approval_workflow($order_id, $this->target_employee_id, 7);
            return true;
        }
        return false;
    }

    public function add_new_order9($relativePath)
    {
        $orderData = [
            'emp_id'        => $this->target_employee_id,
            'emp_name'      => $this->target_employee_name,
            'created_by_id'            => $this->session->userdata('username'),
            'order_name'    => 'مهمة عمل',
            'status'        => '0',
            'type'          => 9,
            'date'          => date('Y-m-d'),
            'time'          => date('H:i:s'),
            'mission_type'          => $this->input->post('mission[type]'),
            'mission_start_time'   => $this->input->post('mission[start_time]'),
            'mission_end_time'  => $this->input->post('mission[end_time]'),
            'mission_date' => $this->input->post('mission[date]'),
            'mission_note' => $this->input->post('mission[note]'),
            
        ];

        if ($this->db->insert('orders_emp', $orderData)) {
            $order_id = $this->db->insert_id();
            $this->create_approval_workflow($order_id, $this->target_employee_id, 9);
            return true;
        }
        return false;
    }
// Add this to hr_model.php
protected $target_employee_id;
    protected $target_employee_name;

public function set_target_employee($id, $name)
{
    $this->target_employee_id = $id;
    $this->target_employee_name = $name;
}
private function _insert_attendance_logs_from_request($requestData)
{
    // Prepare and insert the "Check In" record if it exists
    if (!empty($requestData['attendance_correction'])) {
        $check_in_time = $requestData['correction_date'] . ' ' . $requestData['attendance_correction'];
        
        $checkInData = [
            'emp_code'       => $requestData['emp_id'],
            'punch_time'     => $check_in_time,
            'punch_state'    => 'Check In',
            'area_alias'     => 'Manual Correction',
            'terminal_alias' => 'HR System'
        ];
        $this->db->insert('attendance_logs', $checkInData);
    }

    // Prepare and insert the "Check Out" record if it exists
    if (!empty($requestData['correction_of_departure'])) {
        $check_out_time = $requestData['correction_date'] . ' ' . $requestData['correction_of_departure'];

        $checkOutData = [
            'emp_code'       => $requestData['emp_id'],
            'punch_time'     => $check_out_time,
            'punch_state'    => 'Check Out',
            'area_alias'     => 'Manual Correction',
            'terminal_alias' => 'HR System'
        ];
        $this->db->insert('attendance_logs', $checkOutData);
    }
}
// Helper to get dropdowns
public function get_distinct_list($table, $col) {
    $this->db->distinct()->select($col)->where("$col !=", "");
    $q = $this->db->get($table);
    return array_column($q->result_array(), $col);
}

// Stats for Top Cards
public function get_dashboard_stats() {
    $today = date('Y-m-d');
    // Active Employees
    $s['active'] = $this->db->where('status', 'active')->count_all_results('emp1');
    // Probation (Joined < 6 months)
    $s['probation'] = $this->db->where('joining_date >=', date('Y-m-d', strtotime('-6 months')))->where('status', 'active')->count_all_results('emp1');
    // On Vacation (Type 5, Approved)
    $s['on_vacation'] = $this->db->where('type', 5)->where('status', '2')->where('vac_start <=', $today)->where('vac_end >=', $today)->count_all_results('orders_emp');
    // Pending Requests
    $s['pending'] = $this->db->where('status', '0')->count_all_results('orders_emp');
    
    return $s;
}

// Main Data Logic
// In application/models/hr_model.php

public function get_report_data($type, $filters) {
    // 1. Base Select
    $this->db->select('e.employee_id as emp_id, e.subscriber_name as name, e.n1 as dept, e.profession as job');
    $this->db->from('emp1 e');
    
    // 2. Apply Filters
    if(!empty($filters['dept'])) $this->db->where('e.n1', $filters['dept']);
    if(!empty($filters['comp'])) $this->db->where('e.company_name', $filters['comp']);

    // 3. Switch Logic
    switch($type) {
        case 'probation':
            // 1. Normalize date format to YYYY-MM-DD
            $date_fix = "STR_TO_DATE(REPLACE(e.joining_date, '/', '-'), '%Y-%m-%d')";
            
            // 2. Filter: Joined within last 180 days
            $this->db->where("DATEDIFF(CURDATE(), $date_fix) <= 180");
            $this->db->where("DATEDIFF(CURDATE(), $date_fix) >= 0");
            $this->db->where('e.status', 'active');

            // 3. Select Columns
            $this->db->select('e.joining_date as col1');

            // 4. Calculate Duration as "X Months and Y Days"
            // Step A: Calculate full months
            $calc_months = "TIMESTAMPDIFF(MONTH, $date_fix, CURDATE())";
            // Step B: Calculate remaining days (Today - (StartDate + FullMonths))
            $calc_days   = "DATEDIFF(CURDATE(), DATE_ADD($date_fix, INTERVAL $calc_months MONTH))";
            
            // Step C: Concatenate
            $this->db->select("CONCAT($calc_months, ' شهر و ', $calc_days, ' يوم') as col2");
            break;
            
        case 'resigned':
            $this->db->join('orders_emp o', 'o.emp_id = e.employee_id AND o.type = 1', 'left');
            $this->db->where('e.status', 'resigned');
            if(!empty($filters['start'])) $this->db->where('o.date_of_the_last_working >=', $filters['start']);
            $this->db->select('o.date_of_the_last_working as col1, o.reason_for_resignation as col2');
            break;
            
        case 'on_vacation':
        case 'sick_leave':
            $today = date('Y-m-d');
            $this->db->join('orders_emp o', 'o.emp_id = e.employee_id');
            $this->db->where('o.type', 5);
            $this->db->where('o.status', '2'); // Approved
            $this->db->where('o.vac_start <=', $today)->where('o.vac_end >=', $today);
            if($type == 'sick_leave') $this->db->where('o.vac_main_type', 'sick');
            $this->db->select('o.vac_end as col1, DATEDIFF(o.vac_end, NOW()) as col2');
            break;
            
        case 'pending_requests':
            $this->db->join('orders_emp o', 'o.emp_id = e.employee_id');
            $this->db->where_in('o.status', ['0', '1']); // Pending
            $this->db->select('o.order_name as col1, o.date as col2');
            break;
            
        case 'balances':
            $this->db->join('employee_leave_balances elb', 'elb.employee_id = e.employee_id', 'left');
            $this->db->select('elb.leave_type_slug as col1, elb.remaining_balance as col2');
            break;
    }
    
    return $this->db->get()->result_array();
}
// --- ADD TO hr_model.php ---

// 1. DataTables Logic for Overtime Dashboard

public function get_overtime_datatables()
{
    $this->_get_overtime_query();
    if ($_POST['length'] != -1) {
        $this->db->limit($_POST['length'], $_POST['start']);
    }
    return $this->db->get()->result();
}

public function count_filtered_overtime()
{
    $this->_get_overtime_query();
    return $this->db->count_all_results();
}
// 1. Helper to get the Approval Chain (Visual Train)

// --- END OF SERVICE (EOS) SETTLEMENTS ---







// Actions



// 2. Updated Overtime Query (Merged Logic)
private function _get_overtime_query()
{
    // --- JOIN ADDED HERE ---
    $this->db->select('orders_emp.*, emp1.subscriber_name, emp1.company_name');
    $this->db->from('orders_emp');
    $this->db->join('emp1', 'emp1.employee_id = orders_emp.emp_id', 'left');
    
    $this->db->where('orders_emp.type', 3); 
    $this->db->where_not_in('orders_emp.status', [3, -2]); 

    // --- NEW DASHBOARD FILTERS (Company, Name, ID, Dates) ---
    if (!empty($_POST['filter_company'])) {
        $this->db->where('emp1.company_name', $_POST['filter_company']);
    }
    if (!empty($_POST['filter_emp_name_text'])) { // Renamed to avoid conflict with 'filter_search'
        $this->db->like('emp1.subscriber_name', $_POST['filter_emp_name_text']);
    }
    if (!empty($_POST['filter_emp_id_text'])) {
        $this->db->where('orders_emp.emp_id', $_POST['filter_emp_id_text']);
    }
    if (!empty($_POST['filter_date_from'])) {
        $this->db->where('orders_emp.ot_date >=', $_POST['filter_date_from']);
    }
    if (!empty($_POST['filter_date_to'])) {
        $this->db->where('orders_emp.ot_date <=', $_POST['filter_date_to']);
    }

    // --- EXISTING LOGIC (PRESERVED) ---
    
    // 1. Custom Text Search (Global Search Box)
    if (!empty($_POST['filter_search'])) {
        $val = $_POST['filter_search'];
        $this->db->group_start();
        $this->db->like('orders_emp.emp_name', $val);
        $this->db->or_like('emp1.subscriber_name', $val); // Added search on joined name
        $this->db->or_like('orders_emp.emp_id', $val);
        $this->db->group_end();
    }

    // 2. Filter Approval Status
    if (!empty($_POST['filter_status'])) {
        $status = $_POST['filter_status'];
        if ($status == 'approved') {
            $this->db->where('orders_emp.status', 2);
        } elseif ($status == 'rejected') {
            $this->db->where('orders_emp.status', 3);
        } elseif ($status == 'pending') {
            $this->db->where_not_in('orders_emp.status', [2, 3, -2]);
        }
    }

    // 3. Filter Payment Status
    if (!empty($_POST['filter_pay_status'])) {
        $pay = $_POST['filter_pay_status'];
        if ($pay == 'paid') {
            $this->db->group_start();
            $this->db->where('orders_emp.ot_payment_status', 'paid');
            $this->db->or_where('orders_emp.ot_payment_status', 1);
            $this->db->group_end();
        } elseif ($pay == 'requested') {
            $this->db->where('orders_emp.ot_payment_status', 'requested');
        } elseif ($pay == 'unpaid') {
            $this->db->group_start();
            $this->db->where('orders_emp.ot_payment_status', NULL);
            $this->db->or_where('orders_emp.ot_payment_status', '');
            $this->db->group_end();
        }
    }

    // 4. Finance Manager Restriction
    $current_user = $this->session->userdata('username');
    if ($current_user == '1693') {
        $this->db->group_start();
        $this->db->where('orders_emp.ot_payment_status', 'requested');
        $this->db->or_where('orders_emp.ot_payment_status', 'paid');
        $this->db->or_where('orders_emp.ot_payment_status', 1);
        $this->db->group_end();
    }

    // 5. Ordering
    if (isset($_POST['order'])) {
        // Updated column map to include joined columns if needed
        $cols = [0 => 'orders_emp.id', 1 => 'orders_emp.emp_id', 2 => 'emp1.subscriber_name', 3 => 'emp1.company_name', 4 => 'orders_emp.ot_date', 5 => 'orders_emp.ot_hours', 6 => 'orders_emp.ot_amount', 7 => 'orders_emp.status'];
        $col_idx = $_POST['order']['0']['column'];
        $dir = $_POST['order']['0']['dir'];
        if (isset($cols[$col_idx])) {
            $this->db->order_by($cols[$col_idx], $dir);
        }
    } else {
        $this->db->order_by('orders_emp.id', 'DESC');
    }
}

// 3. Updated Mandate Query (Merged Logic)
private function _get_mandate_query()
{
    // --- JOIN ADDED HERE ---
    $this->db->select('mandate_requests.*, emp1.subscriber_name, emp1.company_name');
    $this->db->from('mandate_requests');
    $this->db->join('emp1', 'emp1.employee_id = mandate_requests.emp_id', 'left');

    // --- NEW DASHBOARD FILTERS ---
    if (!empty($_POST['filter_company'])) {
        $this->db->where('emp1.company_name', $_POST['filter_company']);
    }
    if (!empty($_POST['filter_emp_name_text'])) {
        $this->db->like('emp1.subscriber_name', $_POST['filter_emp_name_text']);
    }
    if (!empty($_POST['filter_emp_id_text'])) {
        $this->db->where('mandate_requests.emp_id', $_POST['filter_emp_id_text']);
    }
    if (!empty($_POST['filter_date_from'])) {
        $this->db->where('mandate_requests.start_date >=', $_POST['filter_date_from']);
    }
    if (!empty($_POST['filter_date_to'])) {
        $this->db->where('mandate_requests.start_date <=', $_POST['filter_date_to']);
    }

    // --- EXISTING LOGIC ---
    
    // Default Status Filter (unless overridden)
    if(empty($_POST['filter_status'])) {
         $this->db->where_in('mandate_requests.status', ['Approved', 'Completed']);
    }

    // 1. Custom Text Search
    if (!empty($_POST['filter_search'])) {
        $val = $_POST['filter_search'];
        $this->db->group_start();
        $this->db->like('mandate_requests.emp_id', $val);
        $this->db->or_like('mandate_requests.id', $val);
        $this->db->or_like('emp1.subscriber_name', $val);
        $this->db->group_end();
    }

    // 2. Filter Status
    if (!empty($_POST['filter_status'])) {
        $status = $_POST['filter_status'];
        if ($status == 'approved') {
            $this->db->where_in('mandate_requests.status', ['Approved', 'Completed']);
        } elseif ($status == 'rejected') {
            $this->db->where('mandate_requests.status', 'Rejected');
        } elseif ($status == 'pending') {
            $this->db->where_not_in('mandate_requests.status', ['Approved', 'Completed', 'Rejected']);
        }
    }

    // 3. Filter Payment Status
    if (!empty($_POST['filter_pay_status'])) {
        $pay = $_POST['filter_pay_status'];
        if ($pay == 'paid') {
            $this->db->where('mandate_requests.payment_status', 'paid');
        } elseif ($pay == 'requested') {
            $this->db->where('mandate_requests.payment_status', 'requested');
        } elseif ($pay == 'unpaid') {
             $this->db->group_start();
            $this->db->where('mandate_requests.payment_status', NULL);
            $this->db->or_where('mandate_requests.payment_status', '');
            $this->db->group_end();
        }
    }

    // 4. Finance Manager Restriction
    $current_user = $this->session->userdata('username');
    if ($current_user == '1693') {
        $this->db->group_start();
        $this->db->where('mandate_requests.payment_status', 'requested');
        $this->db->or_where('mandate_requests.payment_status', 'paid');
        $this->db->group_end();
    }

    // 5. Ordering
    if (isset($_POST['order'])) {
        $cols = [0=>'id', 1=>'emp_id', 2=>'subscriber_name', 3=>'company_name', 4=>'start_date', 5=>'duration_days', 6=>'total_amount'];
        $idx = $_POST['order']['0']['column'];
        $dir = $_POST['order']['0']['dir'];
        if(isset($cols[$idx])) $this->db->order_by($cols[$idx], $dir);
    } else {
        $this->db->order_by('mandate_requests.id', 'DESC');
    }
}

// 4. Export Query Helper
public function get_export_query($type) {
    if ($type == 'overtime') {
        $this->_get_overtime_query();
    } else {
        $this->_get_mandate_query();
    }
    return $this->db->get();
}
public function count_all_overtime()
{
    $this->db->from('orders_emp');
    $this->db->where('type', 3);
    $this->db->where_not_in('status', [3, -2]);

    // --- Apply the same Finance Filter here ---
    $current_user = $this->session->userdata('username');
    
    if ($current_user == '1693' || $current_user == '2909' || $current_user == '1936' || $current_user == '2833') {
        $this->db->group_start();
        $this->db->where('ot_payment_status', 'requested');
        $this->db->or_where('ot_payment_status', 'paid');
        $this->db->or_where('ot_payment_status', 1);
        $this->db->group_end();
    }
    // ------------------------------------------
    
    return $this->db->count_all_results();
}
// --- ADD TO hr_model.php ---

// 1. Mandate Query Logic (Same Rules as Overtime)
// --- UPDATE THESE 2 FUNCTIONS IN hr_model.php ---


public function count_all_mandate() {
    $this->db->from('mandate_requests');
    // FIX: Use 'Approved' here too
    $this->db->where('status', 'Approved'); 
    return $this->db->count_all_results();
}
public function get_mandate_datatables() {
    $this->_get_mandate_query();
    if ($_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
    return $this->db->get()->result();
}


public function count_filtered_mandate() {
    $this->_get_mandate_query();
    return $this->db->count_all_results();
}

// 2. HR Request Action
public function request_mandate_payment($id) {
    $this->db->where('id', $id);
    return $this->db->update('mandate_requests', ['payment_status' => 'requested']);
}

// 3. Finance Confirm Action
public function update_mandate_payment($id, $date, $file) {
    $data = [
        'payment_status' => 'paid',
        'payment_date'   => $date
    ];
    if($file) $data['payment_receipt'] = $file;
    
    $this->db->where('id', $id);
    return $this->db->update('mandate_requests', $data);
}

// 2. Update Payment Status
// In hr_model.php

public function update_ot_payment_status($id, $date, $file)
{
    $data = [
        'ot_payment_status' => 'paid', // CHANGED: Use string 'paid' instead of 1
        'ot_paid'           => 1,      // Update the old column too just in case
        'payment_date'      => $date,
    ];
    
    if ($file) {
        $data['bank_receipt_file'] = $file;
    }

    $this->db->where('id', $id);
    return $this->db->update('orders_emp', $data);
}
// --- ADD TO hr_model.php ---

// Function for HR to send request to Finance
public function request_ot_payment($id)
{
    $data = [
        'ot_payment_status' => 'requested' // New status flag
    ];
    $this->db->where('id', $id);
    return $this->db->update('orders_emp', $data);
}
// =============================================================
    // NEW: Function to update any request data
    // =============================================================
    public function update_order_data($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('orders_emp', $data);
    }

    // =============================================================
    // NEW: Helper to re-calculate vacation days (logic from add_new_order5)
    // =============================================================
    public function calculate_vacation_days($start_str, $end_str, $emp_id)
    {
        $days = 0;
        $public_holidays = $this->get_all_holidays_as_dates();
        
        try {
            $start = new DateTime($start_str);
            $end = new DateTime($end_str);
            $end->modify('+1 day'); // Include end date
            $interval = new DateInterval('P1D');
            $date_range = new DatePeriod($start, $interval, $end);

            foreach ($date_range as $date) {
                $current_date_str = $date->format('Y-m-d');
                $day_of_week = (int)$date->format('N'); // 1 (Mon) to 7 (Sun)
                
                // Friday(5) and Saturday(6) are weekends
                $is_weekend = ($day_of_week === 5 || $day_of_week === 6);
                
                // Check mandatory Saturday (using your existing function)
                $is_mandatory_saturday = ($day_of_week === 6) && $this->is_mandatory_saturday($emp_id, $current_date_str);
                
                if (in_array($current_date_str, $public_holidays) || ($is_weekend && !$is_mandatory_saturday)) {
                    continue;
                }
                $days++;
            }
        } catch (Exception $e) {
            return 0;
        }
        
        return $days;
    }
    // =============================================================
    // SYNC FUNCTION: Updates orders_emp based on workflow
    // =============================================================
    public function sync_order_responsibility($order_id)
    {
        // 1. Find the *current* pending step (lowest level that is pending)
        $this->db->select('approver_id');
        $this->db->from('approval_workflow');
        $this->db->where('order_id', $order_id);
        $this->db->where('status', 'pending');
        $this->db->order_by('approval_level', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get();
        $step = $query->row_array();

        if ($step) {
            // 2. Get the new approver's name
            $this->db->select('name');
            $this->db->from('users');
            $this->db->where('username', $step['approver_id']);
            $user_query = $this->db->get();
            $user = $user_query->row_array();
            
            $approver_name = $user ? $user['name'] : 'Unknown';

            // 3. Update the main orders_emp table
            $update_data = [
                'responsible_employee_id'   => $step['approver_id'],
                'responsible_employee_name' => $approver_name
            ];

            $this->db->where('id', $order_id);
            $this->db->update('orders_emp', $update_data);
            
            return true;
        }
        
        return false;
    }
    // =============================================================
// ADD TO: application/models/hr_model.php
// =============================================================

public function get_direct_manager_from_structure($emp_id)
{
    // 1. Search for the employee in ANY of the n1-n7 columns
    $sql = "SELECT * FROM organizational_structure WHERE 
            n1 = ? OR n2 = ? OR n3 = ? OR n4 = ? OR n5 = ? OR n6 = ? OR n7 = ? 
            LIMIT 1";
            
    $query = $this->db->query($sql, array_fill(0, 7, $emp_id));
    $row = $query->row_array();

    if (!$row) {
        return null; // Employee not found in structure
    }

    // 2. Define the hierarchy order
    $levels = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7'];

    // 3. Loop to find where the employee is, then pick the previous column
    foreach ($levels as $index => $col) {
        if ($row[$col] == $emp_id) {
            // If they are n1 (CEO), they have no direct manager in this table
            if ($index === 0) {
                return null; 
            }
            
            // Return the value of the column directly "above" (left) them
            $manager_col = $levels[$index - 1];
            return $row[$manager_col];
        }
    }

    return null;
}
    // =============================================================
    // WORKFLOW MANAGEMENT FUNCTIONS
    // =============================================================

    // Fetch workflow steps for a specific order with Approver Names
    public function get_order_workflow($order_id)
    {
        $this->db->select('aw.*, u.name as approver_name');
        $this->db->from('approval_workflow aw');
        $this->db->join('users u', 'u.username = aw.approver_id', 'left');
        $this->db->where('aw.order_id', $order_id);
        $this->db->order_by('aw.approval_level', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Update a specific workflow row (change approver)
    public function update_workflow_approver($workflow_id, $new_approver_id)
    {
        $this->db->where('id', $workflow_id);
        // Only update if the step is pending to avoid messing up history
        $this->db->where('status', 'pending'); 
        return $this->db->update('approval_workflow', ['approver_id' => $new_approver_id]);
    }
public function resubmit_end_of_service_settlement($settlement_id, $final_amount, $items = [])
{
    $this->db->trans_start();

    // 1. Update the main settlement record with the new total and reset status
    $this->db->where('id', $settlement_id);
    $this->db->update('end_of_service_settlements', [
        'final_amount'     => $final_amount,
        'status'           => 'pending_review',
        'current_approver' => '2774'
    ]);

    // 2. Delete all old items and the old approval workflow to start fresh
    $this->db->where('settlement_id', $settlement_id)->delete('settlement_items');
    $this->db->where('order_id', $settlement_id)->where('order_type', 8)->delete('approval_workflow');

    // 3. Insert the new/updated items
    if (!empty($items)) {
        $batch_items = [];
        foreach ($items as $item) {
            if (!empty($item['description']) && isset($item['amount'])) {
                $batch_items[] = [
                    'settlement_id' => $settlement_id,
                    'description'   => $item['description'],
                    'amount'        => (float)($item['amount'] ?? 0),
                    'type'          => $item['type']
                ];
            }
        }
        if (!empty($batch_items)) {
            $this->db->insert_batch('settlement_items', $batch_items);
        }
    }

    // 4. Re-create the approval workflow from the beginning
    $this->add_approval_step($settlement_id, 8, '2774', 1);
    $this->add_approval_step($settlement_id, 8, '2230', 2);
    $this->add_approval_step($settlement_id, 8, '2833', 3);
    $this->add_approval_step($settlement_id, 8, '2909', 4);
    $this->add_approval_step($settlement_id, 8, '1693', 5);
    $this->add_approval_step($settlement_id, 8, '1001', 6);

    $this->db->trans_complete();
    return $this->db->trans_status();
}
// In hr_model.php
// ADD THIS NEW FUNCTION

/**
 * Fetches approved fingerprint correction requests within a date range.
 *
 * @param string $startDate 'Y-m-d' format
 * @param string $endDate   'Y-m-d' format
 * @return array List of approved requests.
 */
public function get_past_approved_fingerprint_requests($startDate, $endDate)
{
    $this->db->select('id, emp_id, correction_date, attendance_correction, correction_of_departure');
    $this->db->from('orders_emp');
    $this->db->where('type', 2); // Fingerprint correction
    $this->db->where('status', '2'); // Already approved
    // Filter by the date the request was *created* or *approved* (choose one, 'date' is likely creation date)
    $this->db->where('date >=', $startDate);
    $this->db->where('date <=', $endDate);
    // Add additional check to avoid processing if logs *might* already exist (optional but safer)
    // You might need to adjust this check based on how you identify manual entries
    // $this->db->where("NOT EXISTS (SELECT 1 FROM attendance_logs al WHERE al.emp_code = orders_emp.emp_id AND DATE(al.punch_time) = orders_emp.correction_date AND al.area_alias = 'Manual Correction')", NULL, FALSE);


    $query = $this->db->get();
    if (!$query) {
        log_message('error', 'Error fetching past approved fingerprint requests: ' . print_r($this->db->error(), true));
        return [];
    }
    return $query->result_array();
}

/**
 * Processes a single past approved request to insert logs.
 * Re-uses the _insert_attendance_logs_from_request helper.
 *
 * @param array $requestData The data fetched for the request.
 * @return bool True if logs were potentially inserted, false otherwise.
 */
public function process_single_past_request_logs($requestData)
{
    if (empty($requestData) || empty($requestData['emp_id']) || empty($requestData['correction_date'])) {
        log_message('error', 'process_single_past_request_logs: Invalid request data provided.');
        return false;
    }

    // Check if the helper function exists
    if (method_exists($this, '_insert_attendance_logs_from_request')) {
        log_message('debug', 'Processing past request ID: ' . $requestData['id'] . ' for employee: ' . $requestData['emp_id'] . ' on date: ' . $requestData['correction_date']);
        // Call the existing helper to insert the logs
        $this->_insert_attendance_logs_from_request($requestData);
        // We assume the helper function handles the insertion correctly.
        // It doesn't return success/failure, so we return true.
        return true;
    } else {
        log_message('error', 'process_single_past_request_logs: Helper _insert_attendance_logs_from_request not found!');
        return false;
    }
}
public function approve_fingerprint_correction($requestId)
{
    $this->db->trans_start();

    // 1. Get the full details of the correction request
    $request = $this->db->get_where('orders_emp', ['id' => $requestId])->row_array();

    if (!$request) {
        $this->db->trans_complete();
        return false;
    }

    // 2. Call the helper function to insert logs
    $this->_insert_attendance_logs_from_request($request);

    // 3. Update the original request's status to 'Approved'
    $this->db->where('id', $requestId);
    $this->db->update('orders_emp', ['status' => '2']); // Assuming '2' is your approval status code

    $this->db->trans_complete();
    
    return $this->db->trans_status();
}


// In hr_model.php
public function get_holidays_as_flat_array($range_start, $range_end) {
    $this->db->from('public_holidays');
    
    // Include both range-based holidays AND single-day holidays
    $this->db->group_start();
        // Range-based holidays (have start_date and end_date)
        $this->db->where("start_date <=", $range_end);
        $this->db->where("end_date >=", $range_start);
    $this->db->or_group_start();
        // Single-day holidays (only have holiday_date)
        $this->db->where("start_date IS NULL");
        $this->db->where("end_date IS NULL");
        $this->db->where("holiday_date >=", $range_start);
        $this->db->where("holiday_date <=", $range_end);
    $this->db->group_end();
    $this->db->group_end();
    
    $query = $this->db->get();

    $holiday_dates = [];
    foreach ($query->result() as $row) {
        if (!empty($row->start_date) && !empty($row->end_date)) {
            // Range-based holiday
            $period = new DatePeriod(
                new DateTime($row->start_date),
                new DateInterval('P1D'),
                (new DateTime($row->end_date))->modify('+1 day')
            );
            foreach ($period as $date) {
                $holiday_dates[] = $date->format('Y-m-d');
            }
        } else {
            // Single-day holiday
            $holiday_dates[] = $row->holiday_date;
        }
    }
    
    return array_unique($holiday_dates);
}
/**
     * جلب بيانات الموظف (اسم جديد)
     */
    public function fetch_staff_details($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('emp1');
        return $query->row_array();
    }

    /**
     * تحديث بيانات الموظف (اسم جديد)
     */
    public function save_staff_modifications($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('emp1', $data);
    }
    /**
     * جلب قائمة الموظفين لعرضها في جدول التعديل
     */
    public function get_all_employees_for_list() {
        // نجلب فقط الحقول الأساسية التي نحتاجها في القائمة لتسريع التحميل
        $this->db->select('id, employee_id, subscriber_name, id_number, profession, status');
        $this->db->from('emp1');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
public function get_all_departments() {
        // Change 'name' to your department name column if it's different
        $this->db->select('id, name'); 
        $this->db->from('departments');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetches the IDs of departments already selected for a given resignation request.
     * @param int $requestId
     * @return array
     */
    public function get_selected_clearance_departments($requestId) {
    // Corrected Query: Select the DISTINCT department_id by joining through the parameters table.
    $this->db->select('DISTINCT(params.department_id)');
    $this->db->from('resignation_clearances AS rc');
    $this->db->join('clearance_parameters AS params', 'rc.clearance_parameter_id = params.id');
    $this->db->where('rc.resignation_request_id', $requestId);
    
    $query = $this->db->get();
    
    // Return a simple flat array of department IDs, e.g., [1, 2]
    return array_column($query->result_array(), 'department_id');
}

    /**
     * Saves the selected clearance departments for a resignation request.
     * It deletes old entries and inserts the new ones.
     * @param int $requestId
     * @param array $departmentIds
     * @return bool
     */




  public function update_availability_statuses()
{
    // Step 1: Reset all employees to 'available' to handle returning employees.
    $this->db->update('emp1', ['availability_status' => 'available']);
    $reset_count = $this->db->affected_rows();

    // Step 2: Get the IDs of all employees currently on an approved vacation.
    $today = date('Y-m-d');
    $this->db->select('emp_id');
    $this->db->from('orders_emp');
    $this->db->where('type', 5); // Vacation requests
    $this->db->where('status', '2'); // Approved
    $this->db->where('vac_start <=', $today);
    $this->db->where('vac_end >=', $today);
    $vacationing_employees = $this->db->get()->result_array();

    $unavailable_count = 0;
    if (!empty($vacationing_employees)) {
        $employee_ids = array_column($vacationing_employees, 'emp_id');
        
        // Step 3: Set their status to 'unavailable'.
        $this->db->where_in('employee_id', $employee_ids);
        $this->db->update('emp1', ['availability_status' => 'unavailable']);
        $unavailable_count = $this->db->affected_rows();
    }

    return ['reset' => $reset_count, 'made_unavailable' => $unavailable_count];
}

/**
 * A simple helper function to check if a single employee is available.
 * @param string $employee_id
 * @return bool
 */
public function is_employee_available($employee_id)
{
    $this->db->select('availability_status');
    $this->db->from('emp1');
    $this->db->where('employee_id', $employee_id);
    $result = $this->db->get()->row();

    // If employee not found or status is 'available', return true.
    if (!$result || $result->availability_status === 'available') {
        return true;
    }
    return false;
}  // In hr_model.php

public function get_employee_details($employee_id)
{
    $this->db->select("
        emp1.*, emp1.id, emp1.subscriber_name AS full_name_ar, emp1.employee_id AS employee_code,
        emp1.id_number, emp1.joining_date AS join_date, emp1.n1 AS department, emp1.birth_date,
        emp1.profession AS job_title, emp1.gender, emp1.marital AS marital_status, emp1.religion,
        emp1.id_expiry, emp1.email AS personal_email, emp1.phone AS mobile,
        emp1.address, emp1.type AS employment_type, emp1.company_name AS company, emp1.location,
        emp1.manager AS direct_manager, emp1.n2 AS iban, emp1.n3 AS bank_name,
        emp1.base_salary AS basic_salary, emp1.housing_allowance,
        emp1.n4 AS transportation_allowance, emp1.other_allowances AS other_allowance,
        emp1.n5 AS work_nature_allowance, emp1.n6 AS headphone_allowance, emp1.other_allowances AS other_allowances,
        emp1.n8 AS fuel_allowance, emp1.n9 AS extra_transport_allowance,
        emp1.n10 AS supervision_allowance, emp1.n11 AS subsistence_allowance, emp1.total_salary
    ");
    $this->db->from('emp1');
    $this->db->where('emp1.employee_id', $employee_id);
    
    $query = $this->db->get();
    
    // THE FIX: Return an ARRAY to match the view, not an object.
    return $query->row_array(); 
}
// In application/models/hr_model.php

/**
 * Fetches all records from the insurance_discount table.
 * @return array
 */
public function get_all_insurance_discounts()
{
    $this->db->order_by('n2', 'ASC'); // Order by name
    $query = $this->db->get('insurance_discount');
    return $query->result_array();
}

/**
 * Adds a new insurance discount record.
 * @param array $data The data to insert.
 * @return bool
 */
public function add_insurance_discount($data)
{
    return $this->db->insert('insurance_discount', $data);
}

/**
 * Updates an existing insurance discount record.
 * @param int $id The ID of the record to update.
 * @param array $data The new data.
 * @return bool
 */
public function update_insurance_discount($id, $data)
{
    $this->db->where('id', $id);
    return $this->db->update('insurance_discount', $data);
}

/**
 * Deletes an insurance discount record.
 * @param int $id The ID of the record to delete.
 * @return bool
 */
public function delete_insurance_discount($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('insurance_discount');
}
// 1. Dynamic Export Function
public function get_dynamic_employee_export($selected_columns, $filters)
{
    // Map friendly names to actual DB columns
    $column_map = [
        'employee_id' => 'employee_id',
        'subscriber_name' => 'subscriber_name',
        'id_number' => 'id_number',
        'nationality' => 'nationality',
        'gender' => 'gender',
        'birth_date' => 'birth_date',
        'joining_date' => 'joining_date',
        'profession' => 'profession',
        'department' => 'n1',
        'company' => 'company_name',
        'manager' => 'manager',
        'location' => 'location',
        'email' => 'email',       // Personal Email
        'company_email' => 'n13', // Official Email
        'mobile' => 'phone',
        'base_salary' => 'base_salary',
        'housing' => 'housing_allowance',
        'transport' => 'n4',
        'other_allowance' => 'other_allowances',
        'total_salary' => 'total_salary',
        'bank_name' => 'n3',
        'iban' => 'n2'
    ];

    $this->db->from('emp1');

    // Build SELECT based on user choice
    $select_str = [];
    foreach ($selected_columns as $col) {
        if (isset($column_map[$col])) {
            $select_str[] = $column_map[$col];
        }
    }
    
    // Always include ID just in case, or default to all if empty
    if (empty($select_str)) {
        $this->db->select('*');
    } else {
        $this->db->select(implode(',', $select_str));
    }

    // --- APPLY SAME FILTERS AS VIEW ---
    if (!empty($filters['filter_employee_id'])) $this->db->like('employee_id', $filters['filter_employee_id']);
    if (!empty($filters['filter_id_number'])) $this->db->like('id_number', $filters['filter_id_number']);
    if (!empty($filters['filter_name'])) $this->db->like('subscriber_name', $filters['filter_name']);
    if (!empty($filters['filter_company'])) $this->db->where('company_name', $filters['filter_company']);
    if (!empty($filters['filter_department'])) $this->db->where('n1', $filters['filter_department']);
    if (!empty($filters['filter_manager'])) $this->db->where('manager', $filters['filter_manager']);
    
    $status = $filters['filter_status'] ?? 'all';
    if ($status && $status !== 'all') {
        $this->db->where('status', $status);
    } else {
        $this->db->where('status !=', 'deleted');
    }

    return $this->db->get();
}

// 2. Bulk Bank Update
public function update_banking_info($emp_id, $bank_name, $iban)
{
    $data = [
        'n3' => $bank_name, // Bank Name column
        'n2' => $iban       // IBAN column
    ];
    $this->db->where('employee_id', $emp_id);
    return $this->db->update('emp1', $data);
}
public function get_new_employees($start_date = null, $end_date = null)
{
    $this->db->from('new_employees');
    
    // Debug: Log what dates are being received
    log_message('debug', "Model - Start Date: " . $start_date . " | End Date: " . $end_date);
    
    // Add date filtering logic to the database query
    if (!empty($start_date)) {
        // Check what format the date is in the database
        $this->db->where('join_date >=', $start_date);
        log_message('debug', "Using Start Date: " . $start_date);
    }
    
    if (!empty($end_date)) {
        $this->db->where('join_date <=', $end_date);
        log_message('debug', "Using End Date: " . $end_date);
    }
    
    // Fix the ORDER BY clause
    // First, let's check what format the dates are in
    // If they're already in proper YYYY-MM-DD format, we can order directly
    $this->db->order_by('join_date', 'DESC'); 
    
    $query = $this->db->get();
    
    // Debug: Log the SQL query and results
    log_message('debug', "SQL Query: " . $this->db->last_query());
    log_message('debug', "Number of rows: " . $query->num_rows());
    
    return $query->result_array();
}
public function get_approval_log($order_id, $order_type) // <-- Add $order_type parameter
{
    $this->db->select('
        aw.approver_id,
        aw.status,
        aw.action_date,
        aw.rejection_reason,
        aw.approval_level,
        u.name as approver_name 
    ');
    $this->db->from('approval_workflow as aw');
    $this->db->join('users as u', 'u.username = aw.approver_id', 'left');
    $this->db->where('aw.order_id', $order_id);
    $this->db->where('aw.order_type', $order_type);
    $this->db->order_by('aw.approval_level', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}
public function update_employee($id, $data)
{
    // 1. Update the main emp1 table
    $this->db->where('id', $id);
    $success = $this->db->update('emp1', $data);

    // 2. NEW LOGIC: Sync joining_date with new_employees
    if ($success && isset($data['joining_date'])) {
        
        // Fetch the employee's actual 'employee_id' (e.g. 1127) using the table's primary key ($id)
        $this->db->select('employee_id');
        $this->db->where('id', $id);
        $emp = $this->db->get('emp1')->row();

        if ($emp && !empty($emp->employee_id)) {
            // Push the updated date to the new_employees table
            $this->db->where('employee_id', $emp->employee_id);
            $this->db->update('new_employees', ['join_date' => $data['joining_date']]);
        }
    }

    return $success;
}
    public function get_parameters_for_departments($departmentIds) {
    if (empty($departmentIds)) {
        return [];
    }
    $this->db->select('id, department_id, parameter_name, approver_user_id');
    $this->db->from('clearance_parameters');
    $this->db->where_in('department_id', $departmentIds);
    $this->db->where('is_active', 1);
    $query = $this->db->get();
    return $query->result_array();
}

/**
 * Creates individual clearance tasks based on selected departments.
 * @param int $requestId
 * @param array $departmentIds
 * @return bool
 */
// In hr_model.php
// In hr_model.php

// In hr_model.php

public function create_clearance_tasks_from_departments($requestId, $departmentIds, $directManagerId = null, $financeApproverId = null)
{
    $this->db->trans_start();

    // 1. Delete ALL old tasks
    $this->db->where('resignation_request_id', $requestId);
    $this->db->delete('resignation_clearances');

    $tasksToInsert = [];

    // 2. Add Direct Manager Task
    if (!empty($directManagerId)) {
        $tasksToInsert[] = [
            'resignation_request_id' => $requestId,
            'clearance_parameter_id' => 0,
            'task_description'       => 'موافقة المدير المباشر',
            'approver_user_id'       => $directManagerId,
            'created_by_user_id'     => $this->session->userdata('username'),
            'status'                 => 'pending'
        ];
    }

    // 3. Add Department Tasks
    if (!empty($departmentIds)) {
        $parameters = $this->get_parameters_for_departments($departmentIds);
        foreach ($parameters as $param) {
            
            $approver_id = $param['approver_user_id'];

            // === CUSTOM LOGIC: Override for Finance Department (ID 12) ===
            if ($param['department_id'] == 12 && !empty($financeApproverId)) {
                $approver_id = $financeApproverId;
            }
            // ============================================================

            $tasksToInsert[] = [
                'resignation_request_id' => $requestId,
                'clearance_parameter_id' => $param['id'],
                'task_description'       => null,
                'approver_user_id'       => $approver_id, // Use the overridden ID
                'created_by_user_id'     => $this->session->userdata('username'),
                'status'                 => 'pending'
            ];
        }
    }

    // 4. Insert
    if (!empty($tasksToInsert)) {
        $this->db->insert_batch('resignation_clearances', $tasksToInsert);
    }

    // 5. Update main status
    $this->db->where('id', $requestId);
    $this->db->update('orders_emp', ['status' => '11']);

    $this->db->trans_complete();
    return $this->db->trans_status();
}
public function get_employee_details_bulk($employee_ids)
{
    if (empty($employee_ids)) {
        return [];
    }
    
    // This matches the format of your get_all_employees() function
    $this->db->select('employee_id as username, subscriber_name as name, status');
    $this->db->from('emp1');
    $this->db->where_in('employee_id', $employee_ids);
    $query = $this->db->get();
    return $query->result_array();
}
public function get_pending_clearances_for_user($approver_id) {
    // Trim the approver ID
    $approver_id = trim((string)$approver_id);

    $this->db->select('
        rc.id as task_id,
        rc.resignation_request_id as request_id,
        emp.subscriber_name as resigning_employee_name,
        emp.employee_id as resigning_employee_id, 
        emp.manager as resigning_employee_manager, 
        -- Use task_description if parameter_id is 0 (manager task), else use parameter_name
        COALESCE(params.parameter_name, rc.task_description) as parameter_name,
        rc.status
    ');
    $this->db->from('resignation_clearances rc');
    $this->db->join('orders_emp orders', 'orders.id = rc.resignation_request_id', 'inner'); // Ensure the resignation request exists
    $this->db->join('emp1 emp', 'emp.employee_id = orders.emp_id', 'left'); // Get employee name
    // Use LEFT JOIN for parameters, so manager task (id=0) is included
    $this->db->join('clearance_parameters params', 'params.id = rc.clearance_parameter_id', 'left');

    $this->db->where('TRIM(rc.approver_user_id)', $approver_id); // Match the approver
    $this->db->where('rc.status', 'pending'); // Only get pending tasks

    $this->db->order_by('rc.created_at', 'ASC'); // Optional: order by creation time

    $query = $this->db->get();

    // Error check
    if (!$query) {
         log_message('error', 'Database error fetching clearance tasks: ' . print_r($this->db->error(), true));
         return [];
    }

    return $query->result_array();
}

// In hr_model.php
public function cancel_order_by_hr($order_id, $cancelling_user_id)
{
    // We will use status '-2' to indicate a request that was cancelled by HR.
    // We also record who cancelled it in the rejection reason field for auditing.
    $data = [
        'status' => '-2',
        'reason_for_rejection' => 'Cancelled by HR user: ' . $cancelling_user_id . ' on ' . date('Y-m-d H:i:s')
    ];

    $this->db->where('id', $order_id);
    return $this->db->update('orders_emp', $data);
}
// In hr_model.php, add this new function
// In hr_model.php
public function refund_leave_balance_on_cancellation($request_data)
{
    $this->db->trans_start();

    // Ensure we have data and a valid day count to refund
    if ($request_data && !empty($request_data['vac_days_count']) && $request_data['vac_days_count'] > 0) {
        
        $employee_id = $request_data['emp_id'];
        $leave_type_slug = $request_data['vac_main_type'];
        $days_to_refund = (float)$request_data['vac_days_count'];

        // --- FIX 1: Robust Date Handling ---
        // Try vac_start first, if empty (e.g. half day), try vac_half_date, else default to 'date' (request date)
        $date_source = $request_data['vac_start'];
        if (empty($date_source) || $date_source == '0000-00-00') {
            $date_source = $request_data['vac_half_date'];
        }
        if (empty($date_source) || $date_source == '0000-00-00') {
            $date_source = $request_data['date']; // Fallback to creation date
        }

        $year = date('Y', strtotime($date_source));

        // Debugging Log (Check application/logs/)
        log_message('error', "Refunding Balance: Emp [$employee_id], Type [$leave_type_slug], Year [$year], Days [$days_to_refund]");

        // Update the employee's leave balance table
        $this->db->where('employee_id', $employee_id);
        $this->db->where('leave_type_slug', $leave_type_slug);
   //     $this->db->where('year', $year);
        
        // --- FIX 2: Explicit Float Casting in Query ---
        $this->db->set('balance_consumed', "balance_consumed - $days_to_refund", FALSE);
        $this->db->set('remaining_balance', "remaining_balance + $days_to_refund", FALSE);
        
        $this->db->update('employee_leave_balances');

        if ($this->db->affected_rows() == 0) {
            log_message('error', "REFUND FAILED: No balance record found for Emp $employee_id Year $year Type $leave_type_slug");
        }
    } else {
        log_message('error', "REFUND SKIPPED: Invalid data or 0 days count.");
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}
// In hr_model.php

public function get_pending_requests_by_date_range($start_date, $end_date)
{
    // Added 'approver.name as current_approver' to the select
    $this->db->select('orders_emp.id, orders_emp.emp_id, orders_emp.emp_name, orders_emp.type, orders_emp.status, orders_emp.order_name, orders_emp.date as submission_date, 
        orders_emp.vac_start, orders_emp.vac_end, orders_emp.correction_date, orders_emp.ot_date, orders_emp.date_of_the_last_working,
        approver.name as current_approver'); // <--- Fetch Approver Name
        
    $this->db->from('orders_emp');
    
    // Join with users table to get the name of the responsible employee
    $this->db->join('users as approver', 'orders_emp.responsible_employee = approver.username', 'left');
    
    // 1. Status Check (0 = Pending, 1 = In Progress)
    $this->db->where_in('orders_emp.status', ['0', '1']);

    // 2. Date Range Check (Complex based on Type)
    $this->db->group_start();
        
        // A. Vacations (Type 5)
        $this->db->group_start();
            $this->db->where('orders_emp.type', 5);
            $this->db->where('orders_emp.vac_start <=', $end_date);
            $this->db->where('orders_emp.vac_end >=', $start_date);
        $this->db->group_end();

        // B. Fingerprint (Type 2)
        $this->db->or_group_start();
            $this->db->where('orders_emp.type', 2);
            $this->db->where('orders_emp.correction_date >=', $start_date);
            $this->db->where('orders_emp.correction_date <=', $end_date);
        $this->db->group_end();

        // C. Overtime (Type 3)
        $this->db->or_group_start();
            $this->db->where('orders_emp.type', 3);
            $this->db->where('orders_emp.ot_date >=', $start_date);
            $this->db->where('orders_emp.ot_date <=', $end_date);
        $this->db->group_end();

        // D. Resignation (Type 1)
        $this->db->or_group_start();
            $this->db->where('orders_emp.type', 1);
            $this->db->where('orders_emp.date_of_the_last_working >=', $start_date);
            $this->db->where('orders_emp.date_of_the_last_working <=', $end_date);
        $this->db->group_end();

        // E. Others
        $this->db->or_group_start();
            $this->db->where_not_in('orders_emp.type', [1, 2, 3, 5]);
            $this->db->where('orders_emp.date >=', $start_date);
            $this->db->where('orders_emp.date <=', $end_date);
        $this->db->group_end();

    $this->db->group_end();

    $this->db->order_by('orders_emp.emp_name', 'ASC');
    
    return $this->db->get()->result_array();
}
// Add these methods to your hr_model.php
public function get_approved_resignation_last_day($employee_id)
{
    if (empty($employee_id)) {
        return null;
    }

    $this->db->select('date_of_the_last_working');
    $this->db->from('orders_emp');
    $this->db->where('emp_id', $employee_id);
    $this->db->where('type', 1); // Resignation type
    $this->db->where('status', '2'); // Approved status
    $this->db->order_by('id', 'DESC'); // Get the most recent one if multiple exist
    $this->db->limit(1);

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        $row = $query->row();
        // Ensure the date format is consistent (YYYY-MM-DD)
        if (!empty($row->date_of_the_last_working)) {
             // Try parsing different potential formats
             $formats_to_try = ['Y-m-d', 'Y/m/d', 'd-m-Y', 'd/m/Y'];
             foreach ($formats_to_try as $format) {
                 $date_obj = DateTime::createFromFormat($format, $row->date_of_the_last_working);
                 if ($date_obj !== false) {
                     return $date_obj->format('Y-m-d'); // Return standard format
                 }
             }
             // If parsing fails, return the raw value but log a warning
             log_message('warning', "Could not parse resignation date format: " . $row->date_of_the_last_working . " for employee " . $employee_id);
             return $row->date_of_the_last_working;
        }
    }

    return null; // No approved resignation found or date is empty
}
public function get_leave_type_by_slug($slug) {
    $this->db->where('slug', $slug);
    $this->db->where('is_active', 1);
    $query = $this->db->get('leave_types');
    return $query->row_array();
}

public function get_employee_balance($employee_id, $leave_type) {
    $this->db->where('employee_id', $employee_id);
    $this->db->where('leave_type_slug', $leave_type);
    $this->db->where('year', date('Y'));
    $query = $this->db->get('employee_leave_balances');
    
    if ($query->num_rows() > 0) {
        return $query->row()->remaining_balance;
    }
    
    return 0;
}

public function calculate_business_days($start_date_str, $end_date_str, $employee_id) { // Added employee_id
    if (empty($start_date_str) || empty($end_date_str) || $end_date_str < $start_date_str) {
        return 0;
    }

    $public_holidays = $this->get_all_holidays_as_dates(); // Fetch holidays

    $start = new DateTime($start_date_str);
    $end = new DateTime($end_date_str);
    $end->modify('+1 day'); // Include end date in the loop

    $business_days = 0;
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start, $interval, $end);

    foreach ($date_range as $date) {
        $current_date_str = $date->format('Y-m-d');
        $day_of_week_N = (int)$date->format('N'); // 1=Mon, 5=Fri, 6=Sat

        $is_weekend = ($day_of_week_N === 5 || $day_of_week_N === 6);
        $is_mandatory_saturday = ($day_of_week_N === 6) && $this->is_mandatory_saturday($employee_id, $current_date_str);

        // Skip if it's a public holiday OR a weekend that IS NOT a mandatory Saturday
        if (in_array($current_date_str, $public_holidays) || ($is_weekend && !$is_mandatory_saturday)) {
            continue; // Skip this day
        }

        // If we reach here, it's either a normal weekday or a mandatory Saturday. Count it.
        $business_days++;
    }

    return $business_days;
}
// In hr_model.php

/**
 * Updates a single clearance task with the approver's decision.
 */
public function update_clearance_task($taskId, $status, $userId, $reason = null, $attachmentPath = null) {
    $data = [
        'status' => $status,
        'updated_by_user_id' => $userId,
        'rejection_reason' => ($status === 'rejected') ? $reason : null,
        'attachment_path' => $attachmentPath,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $this->db->where('id', $taskId);
    $this->db->where('status', 'pending'); // Can only update pending tasks
    return $this->db->update('resignation_clearances', $data);
}

/**
 * Checks if all tasks for a resignation are complete, and if so, updates the main order status.
 */
public function check_and_update_overall_status($taskId) {
    // First, find the main resignation_request_id from the task that was just updated
    $this->db->select('resignation_request_id');
    $this->db->where('id', $taskId);
    $request = $this->db->get('resignation_clearances')->row();
    if (!$request) return false;

    $requestId = $request->resignation_request_id;

    // Now, check if there are ANY other tasks for this request that are still 'pending' or 'rejected'
    $this->db->where('resignation_request_id', $requestId);
    $this->db->where_in('status', ['pending', 'rejected']);
    $pendingCount = $this->db->count_all_results('resignation_clearances');

    // If there are no pending/rejected tasks left, it means everything is approved
    if ($pendingCount === 0) {
        // Update the main status in the 'orders_emp' table to 'approved' (e.g., status '2')
        $this->db->where('id', $requestId);
        // CHANGE '2' to your final approval status code
        $this->db->update('orders_emp', ['status' => '2']); 
        return true;
    }
    return false;
}





    function get_orders_emp($emp_id, $is_hr = false) {
    $this->db->select('orders_emp.*, creator.name AS creator_name');
    $this->db->from('orders_emp');
    
    // Join with the users table to get the name of the person who created the request
    $this->db->join('users AS creator', 'creator.username = orders_emp.created_by_id', 'left');

    // If the user is NOT an HR user, filter the results by their employee ID
    if (!$is_hr) {
        $this->db->where('orders_emp.emp_id', $emp_id);
    }

    // For HR users, the 'where' clause is skipped, so all records are fetched.
    
    $this->db->order_by('orders_emp.id', 'DESC');
    $query = $this->db->get();
    return $query->result_array();
}
    function get_orders_emp_res(){
      $id=$this->session->userdata('username');
        $sql = "select * from orders_emp where emp_id='$id' and type='1' ;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function get_orders_emp_app(){
      $id=$this->session->userdata('username');
        $sql = "select * from orders_emp where responsible_employee='$id' and status='0' ;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


    function get_orders_emp_app_all(){
      $id=$this->session->userdata('username');
        $sql = "select * from orders_emp  ;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
public function check_for_duplicate_request($emp_id, $request_type, $post_data)
{
    $this->db->where('emp_id', $emp_id);
    
    // ✅ MODIFIED: Now also excludes canceled requests (status -2) along with rejected ones.
    $this->db->where_not_in('status', ['-1', '3', '-2']);

    switch ($request_type) {
        // Case 1: Requests with a single, unique date
        case 'fingerprint':
            $date = $post_data['fp']['date'] ?? null;
            if (!$date) return false;
            $this->db->where('type', 2);
            $this->db->where('correction_date', $date);
            if ($this->db->count_all_results('orders_emp') > 0) {
                return "لا يمكن تقديم طلب تصحيح بصمة لنفس اليوم مرة أخرى.";
            }
            break;

        case 'resign':
            $date = $post_data['resign']['last_day'] ?? null;
            if (!$date) return false;
            $this->db->where('type', 1);
            $this->db->where('date_of_the_last_working', $date);
            if ($this->db->count_all_results('orders_emp') > 0) {
                return "لا يمكن تقديم طلب استقالة بنفس تاريخ آخر يوم عمل مرة أخرى.";
            }
            break;

        case 'overtime':
            if (($post_data['ot']['type'] ?? '') === 'single') {
                $date = $post_data['ot']['date'] ?? null;
                if (!$date) return false;
                $this->db->where('type', 3);
                $this->db->where('ot_date', $date);
                 if ($this->db->count_all_results('orders_emp') > 0) {
                    return "لا يمكن تقديم طلب عمل إضافي لنفس اليوم مرة أخرى.";
                }
            }
            break;

        // Case 2: Leave request with an overlapping date range
        case 'vacation':
            $start_date = $post_data['vac']['start'] ?? null;
            $end_date = $post_data['vac']['end'] ?? null;
            if (!$start_date || !$end_date) return false;

            $this->db->where('type', 5);
            // Condition for overlap: (new_start <= old_end) AND (new_end >= old_start)
            $this->db->where('vac_start <=', $end_date);
            $this->db->where('vac_end >=', $start_date);
            
            if ($this->db->count_all_results('orders_emp') > 0) {
                return "لا يمكن تقديم طلب إجازة جديد لتداخل فترته مع إجازة حالية.";
            }
            break;

        default:
            // For other request types, no validation is needed
            return false;
    }

    return false; // No duplicate found
}
public function get_approval_chain($employee_id, $num_levels) {
        if ($num_levels <= 0) {
            return [];
        }

        $cols = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7'];
        $this->db->select(implode(',', $cols))->from('organizational_structure');
        foreach ($cols as $c) {
            $this->db->or_where($c, $employee_id);
        }
        $rows = $this->db->get()->result_array();

        $best_row = null;
        $employee_level = -1;

        foreach ($rows as $row) {
            foreach ($cols as $i => $c) {
                if (isset($row[$c]) && (string)$row[$c] === $employee_id) {
                    if ($i > $employee_level) {
                        $employee_level = $i;
                        $best_row = $row;
                    }
                }
            }
        }

        $chain = [];
        if ($best_row !== null && $employee_level > 0) {
            for ($i = 1; $i <= $num_levels; $i++) {
                $approver_level_index = $employee_level - $i;
                if ($approver_level_index >= 0) {
                    $approver_id = trim((string)($best_row[$cols[$approver_level_index]] ?? ''));
                    if ($approver_id !== '') {
                        $chain[] = $approver_id;
                    }
                }
            }
        }
        return $chain;
    }

   // In hr_model.php

// In hr_model.php
// In hr_model.php
// In hr_model.php

// In application/models/hr_model.php

// In hr_model.php

// In hr_model.php
public function check_department_capacity_conflict($employee_id, $start_date, $end_date)
{
    // --- STEP 1: Get Department & Staff List ---
    $emp = $this->db->select('n1')
                    ->where('employee_id', $employee_id)
                    ->get('emp1')
                    ->row();

    if (!$emp || empty($emp->n1)) return null; 

    $my_dept = $emp->n1;

    // Get list of ALL active employees in this department
    $staff_query = $this->db->query("SELECT employee_id FROM emp1 WHERE TRIM(n1) = ? AND status = 'active'", [trim($my_dept)]);
    $staff_list = $staff_query->result_array();
    
    $dept_ids = array_column($staff_list, 'employee_id');
    $total_staff = count($dept_ids);

    if ($total_staff == 0) return null;

    // --- STEP 2: Calculate Limit (Standard Rounding) ---
    // 6 * 0.30 = 1.8 -> round() = 2. 
    // This allows 2 employees to go.
    $max_allowed = round($total_staff * 0.50);
    
    if ($max_allowed < 1) $max_allowed = 1; 

    // --- STEP 3: Check Each Day Individually ---
    $current = new DateTime($start_date);
    $end     = new DateTime($end_date);
    $end->modify('+1 day');

    $interval = new DateInterval('P1D');
    $period   = new DatePeriod($current, $interval, $end);

    foreach ($period as $dt) {
        $check_date = $dt->format('Y-m-d');

        // Query: Count ONLY Approved ('2') vacations
        $this->db->from('orders_emp');
        $this->db->where_in('emp_id', $dept_ids); 
        $this->db->where('emp_id !=', $employee_id);
        
        // *** CHANGE HERE: Only Status 2 ***
        $this->db->where('status', '2'); 
        
        $this->db->where('type', 5); 
        
        // Date Logic
        $this->db->group_start();
            $this->db->where("'$check_date' BETWEEN vac_start AND vac_end", NULL, FALSE);
            $this->db->or_where('vac_half_date', $check_date);
        $this->db->group_end();

        $occupied_count = $this->db->count_all_results();

        // --- STEP 4: Validate ---
        if (($occupied_count + 1) > $max_allowed) {
            return [
                'is_blocked' => true,
                'msg' => "في تاريخ ($check_date)، يوجد $occupied_count موظفين في إجازة معتمدة. الحد المسموح هو $max_allowed (30%)."
            ];
        }
    }

    return ['is_blocked' => false];
}
public function create_approval_workflow($order_id, $employee_id, $request_type_code, $context = [])
{
    $this->db->trans_start();

    $submitter_id = $this->session->userdata('username');
    
    // HR Authority List
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    $is_submitter_hr = in_array($submitter_id, $hr_users);

    // --- 1. PREPARE THE STANDARD APPROVAL CHAIN ---
    $approvers = [];

    // [Logic A] Maternity Leave
    $leave_type_slug = $context['leave_type_slug'] ?? null;
    if ($request_type_code == 5 && $leave_type_slug === 'maternity') {
        $manager_chain = $this->get_approval_chain($employee_id, 1);
        if (!empty($manager_chain)) $approvers[] = $manager_chain[0];
        $approvers[] = '2774'; 
        $approvers[] = '2230'; 
    }
    // [Logic B] Resignation
    elseif ($request_type_code == 1) {
        $approvers = ['2784']; // CEO
    } 
    // [Logic C] Letter Request
    elseif ($request_type_code == 7) { 
        if (!isset($this->hr_model)) { $this->load->model('hr_model'); }
        $direct_manager = $this->hr_model->get_manager_from_structure($employee_id);
        if (!empty($direct_manager)) {
            $approvers[] = $direct_manager;
        }
        $approvers[] = '1127'; 
    } 
    // ============================================================
    // ✅ ADD NEW LOGIC: [Logic X] Expenses (Type 4)
    // ============================================================
    elseif ($request_type_code == 4) {
    // 1. Get Direct Manager ID from organizational structure
    $manager_details = $this->get_direct_manager_details($employee_id);
    $manager_id = $manager_details['manager_id'] ?? null;
    
    // Level 1: Direct Manager
    if (!empty($manager_id)) {
         $approvers[] = $manager_id; 
    }
    
    // 2. Determine Finance Approver based on n13
    $emp_data = $this->db->select('n13')
                         ->where('employee_id', $employee_id)
                         ->get('emp1')
                         ->row();

    // Level 2: Conditional Finance Manager
    if ($emp_data && $emp_data->n13 == 1) {
        $approvers[] = '1693'; // Original Finance Manager
    } else {
        $approvers[] = '2909'; // New Finance Manager for other cases
    }
}
    // ============================================================
    // [Logic D] Custom Logic: OT (3) and Work Mission (9)
    else {
        // CHANGED: Checks for 9 instead of 10
        if ($request_type_code == 3 || $request_type_code == 9) {
            
            // 1. Get Direct Manager ID (Common for both)
            $manager_details = $this->get_direct_manager_details($employee_id);
            $manager_id = $manager_details['manager_id'] ?? null;
            
            // --- TYPE 3: OVERTIME ---
            if ($request_type_code == 3) {
                if (!empty($manager_id)) {
                     $approvers[] = $manager_id; // Level 1: Direct Manager
                }
                $approvers[] = '2774'; // Level 2
                $approvers[] = '2230'; // Level 3
                $approvers[] = '1693'; // Level 4
                $approvers[] = '1001'; // Level 5
            } 
            // --- TYPE 9: WORK MISSION ---
            elseif ($request_type_code == 9) {
                
                // Level 1: Direct Manager (Always first)
                if (!empty($manager_id)) {
                     $approvers[] = $manager_id; 
                }

                // CHECK PROFESSION FOR COLLECTION TEAM
                // Fetch profession from emp1 table
                $emp_info = $this->db->select('profession')
                                     ->where('employee_id', $employee_id)
                                     ->get('emp1')
                                     ->row();
                                     
                $profession = $emp_info ? trim($emp_info->profession) : '';

                // Logic: Check if profession contains specific keywords
                // "محصل" covers "محصل" and "محصل ديون"
                // "مشرف تحصيل" covers the supervisor role
                $is_collection_team = false;
                
                if (strpos($profession, 'محصل') !== false || strpos($profession, 'مشرف تحصيل') !== false) {
                    $is_collection_team = true;
                }

                if ($is_collection_team) {
                    // === Collection Team Path ===
                    // Manager -> 1146 -> 1140 -> 2774
                    $approvers[] = '1146';
                    $approvers[] = '1140';
                    $approvers[] = '2774'; 
                } else {
                    // === Standard Path ===
                    // Manager -> 2774
                    $approvers[] = '2774'; 
                }
            }
        } 
        // ----------------------------------------------------
        // [Logic E] Generic Series (Fallback for other types)
        // ----------------------------------------------------
        else { 
            $series = $this->db->get_where('series_of_approvals', ['code' => $request_type_code])->row();
            $num_approvals = $series ? (int)$series->number_of_approvals : 0;
            $final_approver = $series ? trim((string)$series->final_approver_id) : null;
            
            if ($num_approvals < 1) {
                // Auto Approve Logic
                $this->db->where('id', $order_id)->update('orders_emp', ['status' => '2']);
                if ($request_type_code === 5) { $this->approve_leave_request($order_id); }
                if ($request_type_code === 2) { $this->approve_fingerprint_correction($order_id); }
                $this->db->trans_complete();
                return true;
            }

            $num_managers_to_find = $num_approvals - ($final_approver ? 1 : 0);
            $approvers = $this->get_approval_chain($employee_id, $num_managers_to_find);

            if (!empty($final_approver)) {
                $approvers[] = $final_approver;
            }
        }
    }

    // Ensure unique approvers and re-index array
    $approvers = array_values(array_unique($approvers));

    // ============================================================
    // INSERT DELEGATE AT START OF CHAIN
    // ============================================================
    $request_details = $this->db->select('delegation_employee_id')
                                ->where('id', $order_id)
                                ->get('orders_emp')
                                ->row_array();

    if (!empty($request_details['delegation_employee_id'])) {
        $delegate_id = $request_details['delegation_employee_id'];
        if ($delegate_id != $submitter_id) {
            array_unshift($approvers, $delegate_id); 
        }
    }

    // --- 4. INSERT WORKFLOW INTO DATABASE ---
    if (!empty($approvers)) {
        $workflow_steps = [];
        foreach ($approvers as $level => $approver_id) {
            $workflow_steps[] = [
                'order_id'       => $order_id,
                'order_type'     => $request_type_code,
                'approver_id'    => $approver_id,
                'approval_level' => $level + 1,
                'status'         => 'pending' 
            ];
        }
        $this->db->insert_batch('approval_workflow', $workflow_steps);
        
        $first_approver = $approvers[0];
        $this->db->where('id', $order_id)->update('orders_emp', [
            'responsible_employee' => $first_approver, 
            'status' => '0'
        ]);

    } else {
        // Auto Approve if list is empty
        $this->db->where('id', $order_id)->update('orders_emp', ['status' => '2']);
        if ($request_type_code === 5) { $this->approve_leave_request($order_id); }
        if ($request_type_code === 2) { $this->approve_fingerprint_correction($order_id); }
        if ($request_type_code === 3) { $this->calculate_and_record_ot_pay($order_id); }
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}

public function create_insurance_request($main_data, $family_data = [])
{
    // Start Transaction to ensure both save or neither saves
    $this->db->trans_start();

    // 1. Insert the Main Request
    $this->db->insert('orders.insurance_requests', $main_data);
    $request_id = $this->db->insert_id(); // GET THE NEW ID

    // 2. Process and Insert Family Members
    if ($request_id && !empty($family_data)) {
        
        // We must add the new 'request_id' to every family member row
        $final_family_data = [];
        foreach ($family_data as $member) {
            $member['request_id'] = $request_id; // <--- THIS WAS MISSING
            $final_family_data[] = $member;
        }

        // Batch Insert
        $this->db->insert_batch('orders.insurance_family_members', $final_family_data);
    }

    $this->db->trans_complete();

    // Return ID if successful, False if failed
    return $this->db->trans_status() ? $request_id : false;
}
// --- ADD TO hr_model.php ---
    
    // Fetch all fully approved expenses
    public function get_approved_expenses() {
        $this->db->select('id, emp_name, exp_item_name, exp_amount, exp_date, exp_desc, exp_reason, ot_payment_status, bank_receipt_file, payment_date');
        $this->db->from('orders_emp');
        $this->db->where('type', 4); // 4 = Expenses
        $this->db->where('status', '2'); // 2 = Fully Approved
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result_array();
    }

    // Update the payment status and bank receipt for any order
    public function update_order_payment_status($order_id, $status, $receipt_path = null, $payment_date = null) {
        $data = ['ot_payment_status' => $status];
        
        if ($receipt_path !== null) {
            $data['bank_receipt_file'] = $receipt_path;
        }
        if ($payment_date !== null) {
            $data['payment_date'] = $payment_date;
        }
        
        $this->db->where('id', $order_id);
        return $this->db->update('orders_emp', $data);
    }
   public function get_insurance_details($req_id) {
        // Change emp1.department to emp1.n1 as department
        $this->db->select('insurance_requests.*, emp1.subscriber_name, emp1.n1 as department');
        $this->db->from('insurance_requests');
        $this->db->join('emp1', 'emp1.employee_id = insurance_requests.emp_id', 'left');
        $this->db->where('insurance_requests.id', $req_id);
        $req = $this->db->get()->row_array();

        if ($req) {
            $req['family'] = $this->db->get_where('insurance_family_members', ['request_id' => $req_id])->result_array();
        }
        return $req;
    }
    // In application/models/Hr_model.php

/* Add this function to application/models/Hr_model.php 
   This handles your specific Job Tag rates + Distance multipliers
*/
   // In application/models/Hr_model.php

// In application/models/Hr_model.php
public function calculate_mandate_complex($emp_id, $legs_data, $days) {
    // Calculate total distance
    $total_km = 0;
    foreach ($legs_data as $leg) {
        $total_km += $leg['distance'];
    }
    
    // Get employee details
    $this->db->select('job_tag');
    $this->db->where('employee_id', $emp_id);
    $emp = $this->db->get('emp1')->row();
    $job_tag = $emp ? trim($emp->job_tag) : 'Employee';
    
    // Determine base rate
    $tag_normalized = strtolower(str_replace(' ', '_', $job_tag));
    $base_rate = 275; // Default
    
    if (strpos($tag_normalized, 'ceo') !== false) {
        $base_rate = 800;
    } elseif (strpos($tag_normalized, 'project_manager') !== false) {
        $base_rate = 550;
    } elseif (strpos($tag_normalized, 'Department_manager') !== false) {
        $base_rate = 450;
    }
    
    // Determine multiplier based on TOTAL trip distance
    $multiplier = 0;
    if ($total_km > 251) {
        $multiplier = 2; // Double allowance for trips > 250km TOTAL
    } elseif ($total_km >= 151 && $total_km <= 250) {
        $multiplier = 1;
    }
    
    // Calculate daily allowance (THIS IS THE KEY)
    $daily_allowance = $base_rate * $multiplier;
    
    return [
        'base_rate'   => $base_rate,
        'days'        => $days,
        'multiplier'  => $multiplier,
        'daily_total' => $daily_allowance, // Should be 550 for Employee with 1900km
        'total_allowance' => $daily_allowance * $days,
        'fuel_total'  => 0, // Calculate if needed
        'road_km'     => 0,
        'grand_total' => $daily_allowance * $days,
        'breakdown'   => $legs_data,
        'is_remote'   => false
    ];
}
    public function get_mandate_timeline($request_id) {
    $this->db->select('aw.*, e.subscriber_name as approver_name, e.job_role');
    $this->db->from('approval_workflow aw');
    // Join with employees table to get the name (adjust 'emp1' if your table is named differently)
    $this->db->join('emp1 e', 'aw.approver_id = e.employee_id', 'left');
    $this->db->where('aw.order_id', $request_id);
    $this->db->where('aw.order_type', 'Mandate'); // Important: Match the type used in submit
    $this->db->order_by('aw.id', 'ASC');
    return $this->db->get()->result_array();
}
public function delete_sheet_by_id($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('orders.salary_sheet');
}
/* =========================================================
   START: DISCOUNTS & REPARATIONS AJAX MODEL FUNCTIONS
   ========================================================= */

// --- DISCOUNTS ---
public function get_discounts_by_emp_sheet($emp_id, $sheet_id)
{
    $this->db->where('emp_id', $emp_id);
    // Fetch if it belongs to this sheet OR is recurring (recurring applied to all)
    $this->db->group_start();
        $this->db->where('sheet_id', $sheet_id);
        $this->db->or_where('is_recurring', 1);
    $this->db->group_end();
    return $this->db->get('orders.discounts')->result();
}

public function add_discount_ajax($data)
{
    return $this->db->insert('orders.discounts', $data);
}

public function delete_discount_ajax($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('orders.discounts');
}

// --- REPARATIONS ---
public function get_reparations_by_emp_sheet($emp_id, $sheet_id)
{
    $this->db->where('emp_id', $emp_id);
    $this->db->where('sheet_id', $sheet_id);
    return $this->db->get('orders.reparations')->result();
}

public function add_reparation_ajax($data)
{
    return $this->db->insert('orders.reparations', $data);
}

public function delete_reparation_ajax($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('orders.reparations');
}
/* =========================================================
   END: DISCOUNTS & REPARATIONS AJAX MODEL FUNCTIONS
   ========================================================= */
public function get_direct_managerssssss($emp_id) {
    // 1. Clean the ID
    $clean_id = trim((string)$emp_id);
    
    // 2. Query the 'organizational_structure' table
    // We search for the row where this employee exists in ANY of the hierarchy columns.
    // Since we don't know which column they are in, we can use OR WHERE clauses or just query the specific structure if known.
    // Assuming 'id' in this table links to the employee or we just search all N-columns.
    
    // Better approach: Search where ANY column equals the ID
    $this->db->group_start();
    for($i=1; $i<=7; $i++) {
        $this->db->or_where("n$i", $clean_id);
    }
    $this->db->group_end();
    
    $query = $this->db->get('organizational_structure');

    if ($query->num_rows() == 0) {
        // Fallback: The employee isn't in the structure table?
        // You might want to log this error or default to HR.
        return '2774'; 
    }

    $row = $query->row_array();
    
    // 3. Find the column and get the manager (Left Column)
    // Hierarchy: N1 (CEO) -> N2 -> ... -> N7 (Junior)
    
    // Loop from bottom (N7) up to N2
    for ($i = 7; $i >= 2; $i--) {
        $current_col = 'n' . $i;       // e.g. n3
        $manager_col = 'n' . ($i - 1); // e.g. n2

        if (isset($row[$current_col]) && trim((string)$row[$current_col]) === $clean_id) {
            // Found the user!
            if (!empty($row[$manager_col])) {
                return trim((string)$row[$manager_col]);
            }
        }
    }

    // 4. Special Case: N1 (CEO)
    if (isset($row['n1']) && trim((string)$row['n1']) === $clean_id) {
        return '2774'; // CEO goes to HR Specialist
    }

    return '2774'; // Default fallback if logic fails
}
public function calculate_mandate_estimate($emp_id, $legs_data, $days)
{
    // Get ticket amount (if needed, pass as parameter)
    $ticket_amount = 0; // You might need to pass this as parameter
    
    // 1. Fetch Employee Details
    $this->db->select('job_tag, subscriber_name');
    $this->db->where('employee_id', $emp_id);
    $emp = $this->db->get('emp1')->row();

    $job_tag = $emp ? trim($emp->job_tag) : 'Employee';
    $tag_normalized = strtolower(str_replace(' ', '_', $job_tag));

    // 2. Determine Base Daily Rate
    $base_rate = 275;
    if (strpos($tag_normalized, 'ceo') !== false) {
        $base_rate = 800;
    } elseif (strpos($tag_normalized, 'project_manager') !== false) {
        $base_rate = 550;
    } elseif (strpos($tag_normalized, 'department_manager') !== false) {
        $base_rate = 450;
    }

    // 3. Calculate distances
    $total_km = 0;
    $road_km_total = 0;
    $has_short_distance = false;
    $longest_one_way_distance = 0;
    
    if (is_array($legs_data)) {
        foreach ($legs_data as $leg) {
            $distance = isset($leg['distance']) ? (float)$leg['distance'] : (isset($leg['dist']) ? (float)$leg['dist'] : 0);
            $mode = isset($leg['mode']) ? $leg['mode'] : 'road';
            
            if ($distance > 0) {
                $total_km += $distance;
                
                if ($distance > $longest_one_way_distance) {
                    $longest_one_way_distance = $distance;
                }
                
                if ($distance < 150) {
                    $has_short_distance = true;
                }
                
                if ($mode === 'road') {
                    $road_km_total += $distance;
                }
            }
        }
    }

    // 4. Determine Multiplier based on ONE-WAY distance
    $multiplier = 0;
    
    if ($has_short_distance) {
        $multiplier = 0;
    } else {
        if ($longest_one_way_distance >= 150 && $longest_one_way_distance <= 250) {
            $multiplier = 1;
        } elseif ($longest_one_way_distance > 250) {
            $multiplier = 2;
        }
    }

    // 5. Calculate Totals
    $daily_payable = $base_rate * $multiplier;
    $total_allowance = $daily_payable * $days;
    
    // =========================================================
    // NEW CONDITION: Remote Employee (1-Day Mandate)
    // If employee is remote and duration is 1 day, zero the allowance
    // =========================================================
    // FIXED: Using strpos ensures we catch tags like "Remote Worker" or "Remote "
    $is_remote = (strpos($tag_normalized, 'remote') !== false);
    
    if ($is_remote && $days == 1) {
        $daily_payable = 0;
        $total_allowance = 0;
        $multiplier = 0; // Reset multiplier for consistency
    }
    // =========================================================
    
    // Calculate fuel cost
    $fuel_total = 0;
    if ($road_km_total > 0) {
        $fuel_total = ($road_km_total / 100) * 70;
        $fuel_total = round($fuel_total, 2);
    }
    
    $grand_total = $total_allowance + $fuel_total + $ticket_amount;

    return [
        'job_tag'         => $job_tag,
        'base_rate'       => $base_rate,
        'multiplier'      => $multiplier,
        'daily_payable'   => $daily_payable,
        'total_allowance' => $total_allowance,
        'fuel_total'      => $fuel_total,
        'grand_total'     => $grand_total,
        'total_km'        => $total_km,
        'road_km'         => $road_km_total,
        'is_remote'       => $is_remote 
    ];
}

// In application/models/Hr_model.php
// --- ADD THESE FUNCTIONS TO hr_model.php ---

/**
 * Get total permission hours used by the employee in a specific month
 */
/**
     * Get total permission hours used by the employee in a specific month
     */
    public function get_monthly_permission_hours($emp_id, $month, $year) {
        $this->db->select('SUM(permission_hours) as total_hours'); // تم التحديث هنا
        $this->db->from('orders_emp');
        $this->db->where('emp_id', $emp_id);
        $this->db->where('type', 12);
        $this->db->where_in('status', ['1', '2']); 
        $this->db->where('MONTH(permission_date)', $month); // تم التحديث هنا
        $this->db->where('YEAR(permission_date)', $year); // تم التحديث هنا
        
        $query = $this->db->get();
        $result = $query->row();
        return $result->total_hours ? (float)$result->total_hours : 0;
    }

    /**
     * Save Request Type 12 (Permission / الاستئذان)
     */
    public function add_new_order12($file = null) {
        $emp_id = isset($this->target_employee_id) ? $this->target_employee_id : $this->session->userdata('username');
        $emp_name = isset($this->target_employee_name) ? $this->target_employee_name : $this->session->userdata('name');
        
        $perm_date   = $this->input->post('perm_date');
        $perm_start  = $this->input->post('perm_start');
        $perm_end    = $this->input->post('perm_end');
        $perm_reason = $this->input->post('perm_reason');

        $start_ts = strtotime($perm_start);
        $end_ts   = strtotime($perm_end);
        $hours    = ($end_ts - $start_ts) / 3600;

        $data = [
            'emp_id'                => $emp_id,
            'emp_name'              => $emp_name,
            'created_by_id'         => $this->session->userdata('username'),
            'date'                  => date('Y-m-d'),
            'time'                  => date('H:i:s'),
            'type'                  => 12,
            'order_name'            => 'الاستئذان',
            
            // --- تم التحديث للأعمدة الجديدة هنا ---
            'permission_date'       => $perm_date,
            'permission_start_time' => $perm_start,
            'permission_end_time'   => $perm_end,
            'permission_hours'      => $hours,
            // --------------------------------------
            
            'note'                  => $perm_reason,
            'status'                => 1
        ];

        $this->db->insert('orders_emp', $data);
        $order_id = $this->db->insert_id();

        $manager_details = $this->get_direct_manager_details($emp_id);
        $manager_id = ($manager_details && !empty($manager_details['manager_id'])) ? $manager_details['manager_id'] : '2774';
        
        $this->add_approval_step($order_id, 12, $manager_id, 1);
        if ($manager_id != '2774') {
            $this->add_approval_step($order_id, 12, '2774', 2);
        }
        
        $this->db->update('orders_emp', ['responsible_employee' => $manager_id], ['id' => $order_id]);
        return true;
    }

    /**
     * Automate Attendance Log Insertion upon Final Approval
     */
    // --- ADD TO hr_model.php ---

// دالة لجلب جميع المستندات مع بيانات الموظف
public function get_all_documents() {
    $this->db->select('d.*, e.subscriber_name as emp_name');
    $this->db->from('employee_documents d');
    $this->db->join('emp1 e', 'd.employee_id = e.employee_id', 'left'); // ربط مع جدول emp1
    $this->db->order_by('d.id', 'DESC');
    $query = $this->db->get();
    return $query->result_array();
}

// دالة لحفظ مستند جديد
public function insert_document($data) {
    return $this->db->insert('employee_documents', $data);
}

// دالة لجلب مستند محدد (مفيدة عند التنزيل)
public function get_document_by_id($id) {
    $this->db->where('id', $id);
    $query = $this->db->get('employee_documents');
    return $query->row_array();
}

// دالة لجلب قائمة الموظفين لاستخدامها في قائمة الاختيار (Dropdown)
public function get_employees_list() {
    $this->db->select('employee_id, subscriber_name');
    $this->db->where('status', 'Active'); // افتراض أنك تريد الموظفين النشطين فقط
    $query = $this->db->get('emp1');
    return $query->result_array();
}
    public function auto_log_permission_attendance($order_id) {
        $order = $this->db->get_where('orders_emp', ['id' => $order_id, 'type' => 12])->row_array();
        if (!$order || $order['status'] != 2) return false;

        $emp = $this->db->get_where('emp1', ['employee_id' => $order['emp_id']])->row_array();
        if (!$emp) return false;

        // تم التحديث هنا لقراءة البيانات من الأعمدة الجديدة
        $date = $order['permission_date'];
        $start_time = $order['permission_start_time'];
        $end_time = $order['permission_end_time'];
        
        $name_parts = explode(' ', $emp['subscriber_name']);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';

        $punches = [
            $date . ' ' . $start_time,
            $date . ' ' . $end_time
        ];

        foreach ($punches as $punch_time) {
            $hour = (int)date('H', strtotime($punch_time));
            $punch_state = ($hour >= 12) ? 'Check Out' : 'Check In';
            
            $data = [
                'emp_code'       => $emp['employee_id'],
                'first_name'     => $first_name,
                'last_name'      => $last_name,
                'punch_time'     => $punch_time,
                'punch_state'    => $punch_state,
                'area_alias'     => $emp['address'] ?: 'Manual Entry',
                'terminal_sn'    => 'Permission',
                'terminal_alias' => 'Permission',
                'upload_time'    => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s')
            ];
            $this->db->insert('attendance_logs', $data);
        }
        return true;
    }
public function get_departments_list()
{
    $this->db->distinct();
    $this->db->select('n1'); // The column you specified
    $this->db->where('n1 !=', ''); // Exclude empty strings
    $this->db->where('n1 IS NOT NULL'); // Exclude NULLs
    $this->db->order_by('n1', 'ASC'); // Sort alphabetically
    $query = $this->db->get('emp1');
    return $query->result_array();
}
// In application/models/Hr_model.php

// In application/models/Hr_model.php

public function get_employee_insurance_requests($emp_id)
{
    // List of HR Users who can see everything
    $hr_users = ['2774', '2784', '2515', '2230', '1835', '2901'];

    $this->db->select('id, emp_id, request_type, created_at, status, attachments, current_approver, reason');
    $this->db->from('orders.insurance_requests');
    
    // LOGIC: If the user is NOT in the HR list, filter by their specific ID.
    // If they ARE in the list, we skip this line, so it fetches ALL records.
    if (!in_array($emp_id, $hr_users)) {
        $this->db->where('emp_id', $emp_id);
    }

    $this->db->order_by('created_at', 'DESC');
    $query = $this->db->get();
    
    $requests = $query->result_array();

    // Attach family members to each request
    foreach ($requests as &$req) {
        $req['members'] = $this->get_insurance_family_members($req['id']);
    }
    
    return $requests;
}
// In application/models/Hr_model.php

public function get_all_employees_simple()
{
    $this->db->select('employee_id, subscriber_name');
    $this->db->from('orders.emp1');
    $this->db->where('status', 'Active'); // Optional: only active employees
    return $this->db->get()->result_array();
}

public function get_employee_name_by_id($id)
{
    $this->db->select('subscriber_name');
    $this->db->where('employee_id', $id);
    return $this->db->get('orders.emp1')->row_array();
}


// In application/models/Hr_model.php

public function update_violation_feedback($id, $feedback)
{
    $data = [
        'employee_feedback' => $feedback,
      //  'updated_at' => date('Y-m-d H:i:s') // Optional if you have this column
    ];
    
    $this->db->where('id', $id);
    return $this->db->update('orders.employee_violations', $data);
}
public function get_violations_list($user_id, $is_hr)
{
    $this->db->select('*'); // This selects everything, including 'employee_feedback'
    $this->db->from('orders.employee_violations');
    
    // If NOT HR, restrict to own records
    if (!$is_hr) {
        $this->db->where('employee_id', $user_id);
    }
    
    $this->db->order_by('created_at', 'DESC');
    return $this->db->get()->result_array();
}


// 2. Function for the Excel Export
public function get_all_insurance_details_for_export($emp_id)
{
    // List of HR Users
    $hr_users = ['2774', '2784', '2515', '2230', '1835', '2901'];

    $this->db->select('
        req.id as request_id,
        req.emp_id,
        emp1.subscriber_name as emp_name_ar,
        "" as emp_name_en,  
        req.request_type,
        req.status,
        req.created_at,
        fam.full_name as fam_name_ar,
        fam.full_name_en as fam_name_en,
        fam.relationship,
        fam.national_id,
        fam.dob
    ');
    
    $this->db->from('orders.insurance_requests as req');
    
    // JOIN 1: Family Members
    $this->db->join('orders.insurance_family_members as fam', 'req.id = fam.request_id', 'left');
    
    // JOIN 2: Employee Details (Using correct table orders.emp1)
    // Connecting req.emp_id (e.g. 2774) with emp1.employee_id
    $this->db->join('orders.emp1 as emp1', 'emp1.employee_id = req.emp_id', 'left'); 
    
    // Filter for non-HR users
    if (!in_array($emp_id, $hr_users)) {
        $this->db->where('req.emp_id', $emp_id);
    }
    
    $this->db->order_by('req.created_at', 'DESC');
    
    return $this->db->get()->result_array();
}
public function get_insurance_family_members($request_id)
{
    $this->db->select('*');
    $this->db->from('orders.insurance_family_members');
    $this->db->where('request_id', $request_id);
    return $this->db->get()->result_array();
}
// Note: We added $employee_id = null to the inputs
public function get_all_eos_settlements($status = null, $employee_id = null) {
    
    $this->db->select('
        eos.id,
        eos.employee_id,
        emp.subscriber_name as employee_name,
        eos.status,
        eos.current_approver,
        eos.final_amount,
        eos.created_at,
        eos.resignation_order_id,
        approver.name as current_approver_name,
        creator.name as creator_name,
        aw_pending.id as current_pending_task_id
    ');
    
    $this->db->from('end_of_service_settlements as eos');
    $this->db->join('emp1 as emp', 'emp.employee_id = eos.employee_id', 'left');
    $this->db->join('users as approver', 'approver.username = eos.current_approver', 'left');
    $this->db->join('users as creator', 'creator.username = eos.created_by_id', 'left');

    // ** NEW JOIN **: Left join to find the *pending* task
    $this->db->join('approval_workflow as aw_pending',
                    'aw_pending.order_id = eos.id AND aw_pending.order_type = 8 AND aw_pending.status = \'pending\' AND aw_pending.approver_id = eos.current_approver',
                    'left'); 

    $this->db->where('eos.is_archived', 0);

    // 1. Filter by Status
    if ($status !== null && $status !== 'all' && $status !== '') {
        $this->db->where('eos.status', $status);
    }

    // 2. Filter by Employee ID (NEW)
    if (!empty($employee_id)) {
        // Using 'like' allows finding "1025" by typing "102"
        $this->db->like('eos.employee_id', $employee_id);
    }

    $this->db->order_by('eos.created_at', 'DESC');
    $query = $this->db->get();

    if (!$query) {
        log_message('error', 'Database error fetching EOS settlements: ' . print_r($this->db->error(), true));
        return [];
    }

    return $query->result_array();
}

    public function create_mandate_request($data, $destinations, $goals) {
        $this->db->trans_start();
        $this->db->insert('mandate_requests', $data);
        $req_id = $this->db->insert_id();

        foreach($destinations as $dest) {
            $dest['request_id'] = $req_id;
            $this->db->insert('mandate_destinations', $dest);
        }

        foreach($goals as $g) {
            $this->db->insert('mandate_goals', ['request_id'=>$req_id, 'goal_text'=>$g]);
        }
        $this->db->trans_complete();
        return $req_id;
    }

    public function get_policies() {
        return $this->db->get('mandate_policies')->result_array();
    }
public function get_manager_from_structure($employee_id)
{
    // 1. Search for the employee in ANY of the n columns
    $sql = "SELECT * FROM organizational_structure WHERE 
            n1 = ? OR n2 = ? OR n3 = ? OR n4 = ? OR n5 = ? OR n6 = ? OR n7 = ? 
            LIMIT 1";
            
    $query = $this->db->query($sql, array_fill(0, 7, $employee_id));
    $row = $query->row_array();

    if (!$row) {
        return null; // Employee not found in structure table
    }

    // 2. Define the hierarchy levels
    $levels = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7'];

    // 3. Find the employee's level and return the manager (previous level)
    foreach ($levels as $index => $col) {
        if ($row[$col] == $employee_id) {
            // If employee is n1 (CEO), they have no manager
            if ($index === 0) {
                return null; 
            }
            
            // Get the column immediately before this one (the manager)
            $manager_col = $levels[$index - 1];
            
            // Return the manager's ID
            return $row[$manager_col];
        }
    }

    return null;
}
public function get_employee_mandate_info($emp_id) {
        $this->db->select('id, employee_id, total_salary, job_tag, n1, n2, n3, n4, n5, n6, n7, n8, n9, n10');
        
        if(is_object($emp_id)) {
            $emp_id = $emp_id->id;
        }
        
        $this->db->where('id', $emp_id); 
        $this->db->or_where('employee_id', $emp_id); 
        return $this->db->get('emp1')->row();
    }
    // In application/models/Hr_model.php
public function save_mandate_details($req_id, $post_data) {
        // Save Destinations
        $froms = isset($post_data['from_city']) ? $post_data['from_city'] : [];
        if (is_array($froms)) {
            for ($i = 0; $i < count($froms); $i++) {
                if (!empty($froms[$i])) {
                    $this->db->insert('mandate_destinations', [
                        'request_id' => $req_id,
                        'from_city' => $froms[$i],
                        'to_city' => $post_data['to_city'][$i],
                        'distance_km' => $post_data['dist_km'][$i]
                    ]);
                }
            }
        }
        // Save Goals
        $goals = isset($post_data['goals']) ? $post_data['goals'] : [];
        if (is_array($goals)) {
            foreach ($goals as $goal) {
                if(!empty($goal)) {
                    $this->db->insert('mandate_goals', ['request_id' => $req_id, 'goal_text' => $goal]);
                }
            }
        }
    }
    // --- FINAL CORRECTED HIERARCHY FUNCTION ---
    // Look up manager in the 'organizational_structure' table
    public function get_manager_from_org_structure($emp_id) {
        // 1. Build query to find the row where this employee exists in ANY column (n1 to n7)
        $this->db->group_start();
        for($i = 1; $i <= 7; $i++) {
            $this->db->or_where('n'.$i, $emp_id);
        }
        $this->db->group_end();
        
        $query = $this->db->get('organizational_structure');
        $row = $query->row();

        // 2. If row not found, default to CEO
        if (!$row) {
            return '1001';
        }

        // 3. Find exactly which column has the ID, then pick the one before it
        // We loop backwards: If I am in n3, my manager is in n2.
        for ($i = 7; $i >= 2; $i--) {
            $current_col = 'n' . $i;       // e.g. n3
            $manager_col = 'n' . ($i - 1); // e.g. n2
            
            // Compare IDs (using trim to avoid space issues)
            if (trim($row->$current_col) == $emp_id) {
                $manager_id = trim($row->$manager_col);
                // Return the manager ID if it exists, otherwise 1001
                return !empty($manager_id) ? $manager_id : '1001';
            }
        }

        // If the user is in n1 (CEO), they have no manager. Return 1001 or 0.
        return '1001';
    }
// New function specifically for Mandate Hierarchy (N1-N10 logic)
public function get_mandate_manager_hierarchy($emp_data) {
        // Validation
        if (!$emp_data || !is_object($emp_data)) {
            return '1001'; 
        }

        // IMPORTANT: Use employee_id (e.g. 2901) for comparison, not the DB ID.
        // If employee_id is empty, fallback to id.
        $my_search_id = !empty($emp_data->employee_id) ? $emp_data->employee_id : $emp_data->id;
        
        // Loop from n10 down to n2
        // We look for MY ID in column N(i). If found, my manager is in N(i-1).
        for ($i = 10; $i >= 2; $i--) {
            $col_current = 'n' . $i;       // e.g. n3
            $col_manager = 'n' . ($i - 1); // e.g. n2
            
            if (isset($emp_data->$col_current)) {
                // Clean data and compare
                $cell_value = trim($emp_data->$col_current);
                if ($cell_value == $my_search_id) {
                    // Found me! Return the manager column value
                    $manager_id = isset($emp_data->$col_manager) ? trim($emp_data->$col_manager) : '';
                    return !empty($manager_id) ? $manager_id : '1001';
                }
            }
        }
        
        // Debugging: If you are still getting 1001, it means your ID was not found in columns n2-n10.
        // Check if you are in n1? If so, you have no manager (CEO).
        return '1001'; 
    }
public function get_direct_manager_id($emp_id)
{
    // Fetch all organizational structure columns for the employee from orders.emp1
    $this->db->select('n1, n2, n3, n4, n5, n6, n7');
    $this->db->from('emp1');
    $this->db->where('employee_id', $emp_id);
    $query = $this->db->get();
    $result = $query->row_array();

    if (empty($result)) {
        return null; // Employee not found
    }

    // Loop from the lowest level (n7) up to the second highest (n2)
    // to find the employee's level and get the manager from the level above.
    for ($i = 7; $i >= 2; $i--) {
        $level_key = 'n' . $i; // e.g., 'n7', 'n6', ... 'n2'
        
        // Check if the current employee is at this level
        if (isset($result[$level_key]) && $result[$level_key] == $emp_id) {
            // Manager is at the next higher level: n(i-1)
            $manager_level_key = 'n' . ($i - 1); // e.g., if i=3 (emp in n3), manager in n2
            
            // Return the manager's ID (value in the higher level column)
            return $result[$manager_level_key];
        }
    }

    // If the employee is the CEO (in n1) or not found in n2-n7, return null (no manager)
    return null; 
}
public function setup_ot_approval($order_id, $requesting_emp_id, $ot_type_id = 1)
{
    $manager_id = $this->get_direct_manager_id($requesting_emp_id);

    // 1. Define the mandatory approver IDs
    $hr_specialist_id = 2774;
    $hr_manager_id    = 2230;
    $ceo_id           = 1001;

    // 2. Build the approver list in order
    $approvers = [];
    if ($manager_id) {
        $approvers[] = $manager_id; // Level 1: Direct Manager
    }
    
    // Remaining levels are fixed regardless of direct manager existence
    $approvers[] = $hr_specialist_id; // Level 2: HR Specialist (2774)
    $approvers[] = $hr_manager_id;    // Level 3: HR Manager (2230)
    $approvers[] = $ceo_id;           // Level 4: CEO (1001)

    // 3. Insert into approval_workflow
    $data = [];
    $approval_level = 1;
    foreach ($approvers as $approver_id) {
        $data[] = [
            'order_id'       => $order_id,
            'order_type'     => $ot_type_id,
            'approver_id'    => $approver_id,
            'approval_level' => $approval_level++,
            'status'         => 'Pending', // Initial status
        ];
    }
    
    // Use insert_batch for multiple inserts
    return $this->db->insert_batch('approval_workflow', $data);
}
public function calculate_and_record_ot_pay($order_id)
{
    // 1. Fetch OT details and Employee ID & Salary
    $this->db->select('t1.emp_id, t1.emp_name, t1.ot_hours, t1.ot_date, t2.base_salary, t2.total_salary');
    $this->db->from('orders_emp t1');
    $this->db->join('emp1 t2', 't1.emp_id = t2.employee_id');
    $this->db->where('t1.id', $order_id);
    $this->db->where('t1.ot_paid', 1); // Only process if ot_paid is set to 1

    $ot_request = $this->db->get()->row();

    if (empty($ot_request) || empty($ot_request->ot_hours)) {
        log_message('error', "OT Pay Calculation skipped for Order ID: {$order_id}. Reason: Not found or no hours.");
        return false;
    }

    // Parse hours from format 'HH:MM' or decimal
    $ot_hours = $ot_request->ot_hours;
    
    // Check if format is HH:MM
    if (strpos($ot_hours, ':') !== false) {
        $time_parts = explode(':', $ot_hours);
        $hours = (int)$time_parts[0];
        $minutes = isset($time_parts[1]) ? (int)$time_parts[1] : 0;
        $total_minutes = ($hours * 60) + $minutes;
    } else {
        // Assume decimal hours (e.g., 9.5 for 9 hours 30 minutes)
        $decimal_hours = (float)$ot_hours;
        $total_minutes = $decimal_hours * 60;
    }

    $base_salary  = (float)$ot_request->base_salary;
    $total_salary = (float)$ot_request->total_salary;

    // ========================================================
    // CORRECT CALCULATION BASED ON YOUR FORMULA
    // ========================================================
    
    // A = (total / 30 / 8 / 60) * overtime minutes
    $A = ($total_salary / 30 / 8 / 60) * $total_minutes;
    
    // B = (basic / 2) / 30 / 8 / 60) * overtime minutes
    $B = (($base_salary / 2) / 30 / 8 / 60) * $total_minutes;
    
    // Total overtime amount
    $total_ot_amount = round($A + $B, 2);
    
    // ========================================================
    // END OF CORRECT CALCULATION
    // ========================================================

    // Debug log to see calculation
    log_message('debug', "OT Calculation for Order {$order_id}:");
    log_message('debug', "Total Salary: {$total_salary}, Base Salary: {$base_salary}");
    log_message('debug', "OT Hours: {$ot_hours}, Total Minutes: {$total_minutes}");
    log_message('debug', "A = {$A}, B = {$B}, Total = {$total_ot_amount}");

    // Also update the ot_amount in orders_emp table
    $this->db->where('id', $order_id)
             ->update('orders_emp', [
                 'ot_amount' => $total_ot_amount
             ]);

    // 2. Insert into orders.reparations
    if ($total_ot_amount > 0) {
        // Determine sheet_id for current open payroll period, or use null/0
        $sheet = $this->get_salary_sheet_by_date(date('Y-m-d'));
        
        $reparation_data = [
            'type'           => 'OT_PAY', // Custom code for Overtime Pay
            'emp_id'         => $ot_request->emp_id,
            'emp_name'       => $ot_request->emp_name,
            'amount'         => $total_ot_amount,
            'username'       => $this->session->userdata('username'),
            'name'           => $this->session->userdata('name'),
            'date'           => date('Y-m-d'),
            'time'           => date('H:i:s'),
            'sheet_id'       => $sheet ? $sheet['id'] : 0, // 0 if no sheet is open
            'notes'          => "Overtime Pay for Order ID: " . $order_id . " ({$ot_hours} hours)",
            'reparation_date' => $ot_request->ot_date, // Use the OT date or submission date
        ];

        return $this->db->insert('reparations', $reparation_data);
    }
    
    return true;
}
public function setup_work_mission_approval($order_id, $requesting_emp_id, $mission_type_id = 10)
{
    $manager_id = $this->get_direct_manager_id($requesting_emp_id);
    $hr_specialist_id = 2774;

    // 1. Build the approver list in order
    $approvers = [];
    if ($manager_id) {
        $approvers[] = $manager_id;         // Level 1: Direct Manager
    }
    $approvers[] = $hr_specialist_id;   // Level 2: HR Specialist (2774)

    // 2. Insert into approval_workflow
    $data = [];
    $approval_level = 1;
    foreach ($approvers as $approver_id) {
        $data[] = [
            'order_id'       => $order_id,
            'order_type'     => $mission_type_id,
            'approver_id'    => $approver_id,
            'approval_level' => $approval_level++,
            'status'         => 'Pending', // Initial status
        ];
    }
    
    return $this->db->insert_batch('approval_workflow', $data);
}
public function get_active_employees($employee_id = null)
{
    // 1. Get the list of employees who have officially resigned and passed their last day.
    $this->db->select('emp_id');
    $this->db->from('orders_emp');
    $this->db->where('type', 1); // Resignation request
    $this->db->where('status', '2'); // Approved status
    $this->db->where('date_of_the_last_working <=', date('Y-m-d')); // Last working day is today or in the past
    $resigned_employees_query = $this->db->get();
    $resigned_employee_ids = array_column($resigned_employees_query->result_array(), 'emp_id');

    // 2. Fetch all active employees, EXCLUDING the ones who have resigned.
    
    // --- THIS IS THE FIX ---
    // Select ALL columns from emp1, not just the employee_id
    $this->db->select('emp1.*'); 
    // --- END OF FIX ---
    
    $this->db->from('emp1');
    $this->db->where('status', 'active');

    if (!empty($resigned_employee_ids)) {
        $this->db->where_not_in('employee_id', $resigned_employee_ids);
    }
    
    if ($employee_id) {
        $this->db->where('employee_id', $employee_id);
    }
    
    // FIX 2: Return result() (array of objects) to match the original get_employees()
    return $this->db->get()->result(); 
}

public function get_active_employees_for_accrual($employee_id = null)
{
    // 1. Find employees who have resigned and passed their last working day
    $this->db->select('emp_id');
    $this->db->from('orders_emp');
    $this->db->where('type', 1);        // Resignation Request
    $this->db->where('status', '2');    // Approved
    $this->db->where('date_of_the_last_working <=', date('Y-m-d'));
    $resigned_query = $this->db->get();
    
    $resigned_ids = [];
    foreach($resigned_query->result_array() as $row) {
        $resigned_ids[] = $row['emp_id'];
    }

    // 2. Fetch active employees from emp1
    $this->db->select('employee_id, subscriber_name'); 
    $this->db->from('emp1');
    $this->db->where('status', 'active');

    // Exclude the resigned IDs found above
    if (!empty($resigned_ids)) {
        $this->db->where_not_in('employee_id', $resigned_ids);
    }
    
    // Filter by specific ID if provided (for testing)
    if ($employee_id) {
        $this->db->where('employee_id', $employee_id);
    }
    
    // IMPORTANT: Return result_array() so the loop in process_weekly_leave_accrual works
    return $this->db->get()->result_array(); 
}



public function process_weekly_leave_accrual($employee_id = null)
{
    $this->db->trans_start();
    
    // Get either one employee or all active employees
    $employees_to_process = $this->get_active_employees_for_accrual($employee_id);
    
    $weekly_accrual_amount = 0.0712;
    $current_year = date('Y');
    $previous_year = $current_year - 1;

    $updated_count = 0;
    $created_count = 0;

    foreach ($employees_to_process as $emp) {
        $emp_id = $emp['employee_id'];

        // Check if a balance record for the current year already exists
        $this->db->where('employee_id', $emp_id);
        $this->db->where('leave_type_slug', 'annual');
        $this->db->where('year', $current_year);
        $current_year_balance_query = $this->db->get('employee_leave_balances');

        if ($current_year_balance_query->num_rows() > 0) {
            // --- RECORD EXISTS: UPDATE IT ---
            $this->db->where('employee_id', $emp_id);
            $this->db->where('leave_type_slug', 'annual');
            $this->db->where('year', $current_year);
            
            $this->db->set('balance_allotted', 'balance_allotted + ' . $weekly_accrual_amount, FALSE);
            $this->db->set('remaining_balance', 'remaining_balance + ' . $weekly_accrual_amount, FALSE);
            $this->db->update('employee_leave_balances');
            $updated_count++;
        } else {
            // --- RECORD DOES NOT EXIST: CREATE IT WITH CARRY-OVER ---
            // Find last year's balance to carry it over.
            $this->db->select('remaining_balance');
            $this->db->where('employee_id', $emp_id);
            $this->db->where('leave_type_slug', 'annual');
            $this->db->where('year', $previous_year);
            $previous_year_balance = $this->db->get('employee_leave_balances')->row();
            
            $carry_over_balance = $previous_year_balance ? (float)$previous_year_balance->remaining_balance : 0;
            
            // Create the new record for the current year
            $new_balance_data = [
                'employee_id'       => $emp_id,
                'leave_type_slug'   => 'annual',
                'balance_allotted'  => $carry_over_balance + $weekly_accrual_amount,
                'balance_consumed'  => 0,
                'remaining_balance' => $carry_over_balance + $weekly_accrual_amount,
                'year'              => $current_year
            ];
            $this->db->insert('employee_leave_balances', $new_balance_data);
            $created_count++;
        }
    }

    $this->db->trans_complete();
    return ['updated' => $updated_count, 'created' => $created_count];
}
// In hr_model.php

// In hr_model.php

private function _get_datatables_query_balances()
{
    // Define the columns available for searching and sorting
    $column_search = [
        'elb.employee_id',
        'e.subscriber_name',
        'lt.name_ar',
        'elb.year'
    ];
    
    // ✅ CORRECTED: Removed 'elb.id' from the SELECT statement
    $this->db->select("
        elb.employee_id,
        e.subscriber_name,
        lt.name_ar AS leave_type_name,
        elb.balance_allotted,
        elb.balance_consumed,
        elb.remaining_balance,
        elb.year
    ");
    $this->db->from('employee_leave_balances AS elb');
    $this->db->join('emp1 AS e', 'e.employee_id = elb.employee_id', 'left');
    $this->db->join('leave_types AS lt', 'lt.slug = elb.leave_type_slug', 'left');

    // Handle the global search from DataTables
    if (!empty($_POST['search']['value'])) {
        $this->db->group_start();
        foreach ($column_search as $item) {
            if ($item) {
                $this->db->or_like($item, $_POST['search']['value']);
            }
        }
        $this->db->group_end();
    }

    // Handle column sorting
    if (isset($_POST['order'])) {
        $order_column_index = $_POST['order']['0']['column'];
        $order_column = $_POST['columns'][$order_column_index]['data'];
        $order_dir = $_POST['order']['0']['dir'];
        
        $column_map = [
            'employee_id' => 'elb.employee_id',
            'subscriber_name' => 'e.subscriber_name',
            'leave_type_name' => 'lt.name_ar',
            'balance_allotted' => 'elb.balance_allotted',
            'balance_consumed' => 'elb.balance_consumed',
            'remaining_balance' => 'elb.remaining_balance',
            'year' => 'elb.year'
        ];
        
        if (isset($column_map[$order_column])) {
            $this->db->order_by($column_map[$order_column], $order_dir);
        }
    } else {
        // Default sort order
        $this->db->order_by('e.subscriber_name', 'ASC');
    }
}

public function get_datatables_balances()
{
    $this->_get_datatables_query_balances();
    if ($_POST['length'] != -1) {
        $this->db->limit($_POST['length'], $_POST['start']);
    }
    $query = $this->db->get();
    return $query->result();
}

public function count_filtered_balances()
{
    $this->_get_datatables_query_balances();
    $query = $this->db->get();
    return $query->num_rows();
}

public function count_all_balances()
{
    $this->db->from('employee_leave_balances');
    return $this->db->count_all_results();
}

public function get_employee_status($employee_id)
{
    $this->db->select('availability_status');
    $this->db->from('emp1');
    $this->db->where('employee_id', $employee_id);
    $result = $this->db->get()->row();
    return $result ? $result->availability_status : 'Not Found';
}

// FIND THIS FUNCTION IN your hr_model.php
// REPLACE WITH THIS NEW VERSION
public function get_employees_with_pending_letter_requests($approver_id)
{
    $this->db->select('orders_emp.id as order_id, orders_emp.emp_id as id, emp1.subscriber_name as name, orders_emp.letter_type');
    $this->db->from('orders_emp');
    $this->db->join('emp1', 'emp1.employee_id = orders_emp.emp_id', 'left');
    $this->db->where('orders_emp.responsible_employee', $approver_id);
    $this->db->where('orders_emp.status IN (0, 1)'); // Pending requests
    $this->db->where('orders_emp.letter_type IS NOT NULL'); // Only letter requests
    $this->db->order_by('orders_emp.id', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
}
public function get_attendance_overview_data(array $filters) {
    $start_date = $filters['start_date'] ?? date('Y-m-01');
    $end_date   = $filters['end_date'] ?? date('Y-m-t');
    
    // =========================================================
    // 1. FILTER EMPLOYEES BASED ON DEVICE (NEW LOGIC)
    // =========================================================
    $device_emp_ids = null;
    
    if (!empty($filters['device'])) {
        // We need to find which employees used the selected device in this date range
        $this->db->distinct();
        $this->db->select('emp_code'); // Ensure this matches the column in attendance_logs
        $this->db->from('attendance_logs');
        
        // Optimize by filtering logs only within the selected date range
        $this->db->where('DATE(punch_time) >=', $start_date);
        $this->db->where('DATE(punch_time) <=', $end_date);

        if ($filters['device'] === 'Mobile App') {
            // SPECIAL LOGIC FOR MOBILE:
            // Mobile punches often have NULL/Empty terminal_alias OR have 'Mobile' in the alias/area
            $this->db->group_start();
                $this->db->where('terminal_alias', '');
                $this->db->or_where('terminal_alias IS NULL');
                $this->db->or_like('terminal_alias', 'Mobile');
                $this->db->or_like('area_alias', 'Mobile'); 
            $this->db->group_end();
        } else {
            // SPECIFIC PHYSICAL DEVICE LOGIC (Exact Match):
            // e.g. 'رياض - مكتب 731', 'Rajhi-6th', etc.
            $this->db->where('terminal_alias', $filters['device']);
        }
        
        $query = $this->db->get();
        $device_emp_ids = array_column($query->result_array(), 'emp_code');

        // If a device was selected but NO employees used it in this range, return empty result immediately
        if (empty($device_emp_ids)) {
            return ['kpis' => [], 'records' => [], 'total_records' => 0];
        }
    }
    // =========================================================

    // Select columns based on your specific schema
    $this->db->select("employee_id, subscriber_name as employee_name, n1 as department, profession, company_name, location");
    $this->db->from('emp1');
    // Ensure we only get active employees (or whatever status logic you prefer)
    $this->db->where('status !=', 'deleted');

    // --- APPLY FILTERS ---
    
    // 1. Apply Device Filter (If active)
    if ($device_emp_ids !== null) {
        $this->db->where_in('employee_id', $device_emp_ids);
    }

    // 2. Standard Filters
    if (!empty($filters['employee_id'])) {
        $this->db->group_start();
        $this->db->like('employee_id', $filters['employee_id']);
        $this->db->or_like('subscriber_name', $filters['employee_id']);
        $this->db->group_end();
    }
    if (!empty($filters['department'])) {
        $this->db->where('n1', $filters['department']);
    }
    if (!empty($filters['profession'])) {
        $this->db->where('profession', $filters['profession']);
    }
    if (!empty($filters['company'])) {
        $this->db->where('company_name', $filters['company']);
    }
    if (!empty($filters['location'])) {
        $this->db->where('location', $filters['location']);
    }
    if (!empty($filters['job_type'])) {
        $this->db->where('job_type', $filters['job_type']);
    }
    // ---------------------

    $employees = $this->db->get()->result_array();
    
    if (empty($employees)) {
        return ['kpis' => [], 'records' => [], 'total_records' => 0];
    }

    $employee_ids = array_column($employees, 'employee_id');

    // Fetch related data (Using existing helper functions)
    $attendance_map = $this->_get_attendance_for_employees_range($employee_ids, $start_date, $end_date);
    $events_map     = $this->_get_events_for_employees_range($employee_ids, $start_date, $end_date);
    $holidays_map   = $this->_get_holidays_for_range($start_date, $end_date);
    $rules_map      = $this->_get_rules_for_employees($employee_ids);
    $pending_requests_count = $this->_get_pending_requests_count($employee_ids, $start_date, $end_date);

    $records = [];
    $kpis = [
        'total_late' => 0,
        'total_absent' => 0,
        'total_incomplete' => 0,
        'total_on_leave' => 0,
        'total_requests' => $pending_requests_count,
    ];

    $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));

    foreach ($employees as $emp) {
        $emp_id = $emp['employee_id'];
        $rules = $rules_map[$emp_id] ?? ['first_punch' => '06:30', 'last_punch' => '08:30', 'working_hours' => 8.0];

        foreach (clone $period as $date) {
            $date_str = $date->format('Y-m-d');
            $day_of_week = (int)$date->format('w');

            $day_status = 'N/A';
            $check_in = null;
            $check_out = null;
            $work_duration = null;
            $violation = null;

            // 1. Holiday Check
            if (isset($holidays_map[$date_str])) {
                $day_status = 'عطلة رسمية';
            } 
            // 2. Weekend Check (Fri=5, Sat=6)
            elseif ($day_of_week === 5 || $day_of_week === 6) {
                $day_status = 'نهاية الأسبوع';
            } 
            // 3. Events (Vacation/Correction) Check
            elseif (isset($events_map[$emp_id][$date_str])) {
                $event = $events_map[$emp_id][$date_str][0];
                if ($event['type'] === 'corr') {
                    $day_status = 'تصحيح بصمة';
                } else {
                    $kpis['total_on_leave']++;
                    $day_status = $event['title'] ?: 'إجازة';
                }
            } 
            // 4. Attendance Check
            elseif (isset($attendance_map[$emp_id][$date_str])) {
                $att = $attendance_map[$emp_id][$date_str];
                $check_in  = $att['first_in'] ? new DateTime($att['first_in']) : null;
                $check_out = $att['last_out'] ? new DateTime($att['last_out']) : null;
                $day_status = 'حاضر';

                if (!$check_in || !$check_out || $check_in == $check_out) {
                    $violation = 'سجل غير مكتمل';
                    $kpis['total_incomplete']++;
                } else {
                    $late_threshold = new DateTime($date_str . ' ' . ($rules['last_punch'] ?? '08:30'));
                    if ($check_in > $late_threshold) {
                        $violation = 'حضور متأخر';
                        $kpis['total_late']++;
                    }
                    $diff = $check_out->getTimestamp() - $check_in->getTimestamp();
                    $work_duration = gmdate("H:i", $diff);
                }
            } 
            // 5. Absent Check
            else {
                if ($date < new DateTime('today')) {
                    $day_status = 'غياب';
                    $kpis['total_absent']++;
                } else {
                    $day_status = '—';
                }
            }
            
            // Only add if meaningful (or keep all if you want full grid)
            if($day_status !== '—' && $day_status !== 'نهاية الأسبوع') {
                 $records[] = [
                    'employee_id'   => $emp_id,
                    'employee_name' => $emp['employee_name'],
                    'department'    => $emp['department'], // n1
                    'profession'    => $emp['profession'], // profession
                    'company'       => $emp['company_name'], // company_name
                    'location'      => $emp['location'], // location
                    
                    'date'          => $date_str,
                    'day_status'    => $day_status,
                    'check_in'      => $check_in ? $check_in->format('H:i') : 'N/A',
                    'check_out'     => $check_out ? $check_out->format('H:i') : 'N/A',
                    'work_duration' => $work_duration,
                    'violation'     => $violation,
                ];
            }
        }
    }
    
    return ['kpis' => $kpis, 'records' => $records, 'total_records' => count($records)];
}
// In application/models/hr_model.php

/**
 * Fetches employees who have missing critical data.
 * Checks columns: employee_id, id_number, email, id_expiry, subscriber_name, nationality,
 * gender, birth_date, base_salary, housing_allowance, total_salary, profession,
 * joining_date, company_name, n1 (department), n2 (iban), n3 (bank name), n4 (other allowance),
 * location, manager, contract_period, contract_start, contract_end.
 */
// In application/models/hr_model.php

public function get_past_due_resignations($start_date = null, $end_date = null)
{
    $this->db->select('
        oe.emp_id, 
        oe.emp_name, 
        oe.date_of_the_last_working, 
        oe.reason_for_resignation,
        e.status as current_status,
        e.subscriber_name
    ');
    $this->db->from('orders_emp as oe');
    $this->db->join('emp1 as e', 'e.employee_id = oe.emp_id', 'left');
    
    $this->db->where('oe.type', 1);       // Resignation
    $this->db->where('oe.status', '2');   // Approved
    
    // --- FILTER LOGIC ---
    if (!empty($start_date) && !empty($end_date)) {
        $this->db->where('oe.date_of_the_last_working >=', $start_date);
        $this->db->where('oe.date_of_the_last_working <=', $end_date);
    } else {
        // Default: Show all past due (Before Today)
        $this->db->where('oe.date_of_the_last_working <', date('Y-m-d'));
    }
    // --------------------

    $this->db->order_by('oe.date_of_the_last_working', 'DESC');
    
    return $this->db->get()->result_array();
}
// In application/models/hr_model.php

// 1. Create a new task
public function create_task($data)
{
    return $this->db->insert('task_assignments', $data);
}

// 2. Get tasks created BY this manager (for tracking)
public function get_tasks_created_by_manager($manager_id)
{
    $this->db->select('t.*, e.subscriber_name as emp_name, e.n1 as dept');
    $this->db->from('task_assignments t');
    $this->db->join('emp1 e', 'e.employee_id = t.employee_id', 'left');
    $this->db->where('t.manager_id', $manager_id);
    $this->db->order_by('t.created_at', 'DESC');
    return $this->db->get()->result_array();
}
// In application/models/hr_model.php

public function update_task_attributes($task_id, $data)
{
    $this->db->where('id', $task_id);
    return $this->db->update('task_assignments', $data);
}
// 3. Get tasks assigned TO this employee (for working)
public function get_tasks_assigned_to_employee($employee_id)
{
    $this->db->select('t.*, m.name as manager_name');
    $this->db->from('task_assignments t');
    // Assuming 'users' table has the manager's name based on their username/id
    $this->db->join('users m', 'm.username = t.manager_id', 'left');
    $this->db->where('t.employee_id', $employee_id);
    $this->db->order_by('t.due_date', 'ASC'); // Urgent tasks first
    return $this->db->get()->result_array();
}

// 4. Update Status (Employee Action)
public function update_task_status($task_id, $status)
{
    $this->db->where('id', $task_id);
    return $this->db->update('task_assignments', ['status' => $status]);
}
public function get_employees_with_missing_data()
{
    $this->db->select('*');
    $this->db->from('emp1');
    $this->db->where('status', 'active'); // Only check active employees

    // Group conditions: If ANY of these are null or empty string
    $this->db->group_start();
        $columns_to_check = [
            'employee_id', 'id_number', 'email', 'id_expiry', 'subscriber_name',
            'nationality', 'gender', 'birth_date', 'base_salary', 'housing_allowance',
            'total_salary', 'profession', 'joining_date', 'company_name', 
            'n1', 'n2', 'n3', 'n4', 
            'location', 'manager', 'contract_period', 'contract_start', 'contract_end'
        ];

        foreach ($columns_to_check as $col) {
            $this->db->or_where("$col IS NULL", null, false);
            $this->db->or_where("$col = ''", null, false);
        }
    $this->db->group_end();

    $this->db->order_by('employee_id', 'ASC');
    return $this->db->get()->result_array();
}
// PASTE THIS NEW HELPER FUNCTION INTO your application/models/hr_model.php file

/**
 * Helper function to dynamically get all 'attendance_logs_%' table names.
 * This is required by the new attendance overview functions.
 * @return array
 */
private function getAttendanceTables() {
    $tables = [];
    $query = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'attendance_logs%'");
    
    if ($query) {
        foreach ($query->result_array() as $row) {
            $tables[] = $row['TABLE_NAME'];
        }
    }
    
    // Ensure the main table is always included, even if the LIKE query misses it.
    if (!in_array('attendance_logs', $tables)) {
        array_unshift($tables, 'attendance_logs');
    }
    
    return array_unique($tables);
}
// Helper function 1: Get attendance for MULTIPLE employees
private function _get_attendance_for_employees_range(array $emp_ids, $start_date, $end_date) {
    if (empty($emp_ids)) return [];
    
    // Using the getAttendanceTables method you already have in hr_model2
    $tables = $this->getAttendanceTables();
    if (empty($tables)) return [];

    $unions = [];
    foreach ($tables as $table) {
        $unions[] = sprintf(
            "(SELECT emp_code, punch_time FROM `%s` WHERE emp_code IN (%s) AND punch_time BETWEEN %s AND %s)",
            $table,
            implode(',', array_map([$this->db, 'escape'], $emp_ids)),
            $this->db->escape("$start_date 00:00:00"),
            $this->db->escape("$end_date 23:59:59")
        );
    }
    $union_sql = implode(" UNION ALL ", $unions);

    $sql = "SELECT emp_code, DATE(punch_time) AS day, MIN(punch_time) AS first_in, MAX(punch_time) AS last_out
            FROM ($union_sql) AS U GROUP BY emp_code, DATE(punch_time)";
    
    $query = $this->db->query($sql);
    $map = [];
    foreach ($query->result_array() as $row) {
        $map[$row['emp_code']][$row['day']] = $row;
    }
    return $map;
}

// Helper function 2: Get events for MULTIPLE employees
private function _get_events_for_employees_range(array $emp_ids, $start_date, $end_date) {
    if (empty($emp_ids)) return [];
    
    $this->db->from('orders_emp');
    $this->db->where_in('emp_id', $emp_ids);
    $this->db->where_in('type', [2, 5]);
    $this->db->where_in('status', ['0', '1', '2']); // Pending, In Progress, Approved
    $this->db->where("((vac_start <= '$end_date' AND vac_end >= '$start_date') OR (correction_date BETWEEN '$start_date' AND '$end_date'))", NULL, FALSE);
    $query = $this->db->get();

    $events = [];
    foreach ($query->result_array() as $row) {
        $emp_id = $row['emp_id'];
        if((int)$row['type'] === 5 && !empty($row['vac_start'])) {
            $period = new DatePeriod(new DateTime($row['vac_start']), new DateInterval('P1D'), (new DateTime($row['vac_end']))->modify('+1 day'));
            foreach($period as $date){
                $dateStr = $date->format('Y-m-d');
                $type = ($row['vac_half_date'] == $dateStr) ? 'vac_half' : 'vac_full';
                $events[$emp_id][$dateStr][] = ['title' => $row['order_name'], 'status' => $row['status'], 'type' => $type];
            }
        } elseif ((int)$row['type'] === 2 && !empty($row['correction_date'])) {
            $events[$emp_id][$row['correction_date']][] = ['title' => $row['order_name'], 'status' => $row['status'], 'type' => 'corr'];
        }
    }
    return $events;
}

// Helper function 3: Get holidays
private function _get_holidays_for_range($start_date, $end_date) {
    // This function already exists in your hr_model.php, so you might not need to add it again.
    // Just ensure it exists.
    $this->db->select('holiday_date, holiday_name')->from('public_holidays')->where('holiday_date >=', $start_date)->where('holiday_date <=', $end_date);
    $query = $this->db->get();
    $map = [];
    foreach ($query->result_array() as $row) {
        $map[$row['holiday_date']] = $row['holiday_name'];
    }
    return $map;
}

// Helper function 4: Get rules for MULTIPLE employees
private function _get_rules_for_employees(array $emp_ids) {
    if (empty($emp_ids)) return [];
    $this->db->from('work_restrictions');
    $this->db->where_in('emp_id', $emp_ids);
    $query = $this->db->get();
    $map = [];
    foreach ($query->result_array() as $row) {
        $map[$row['emp_id']] = $row;
    }
    return $map;
}

// Helper function 5: Get count of pending requests
private function _get_pending_requests_count(array $emp_ids, $start_date, $end_date) {
    if (empty($emp_ids)) return 0;
    $this->db->from('orders_emp');
    $this->db->where_in('emp_id', $emp_ids);
    $this->db->where('date >=', $start_date);
    $this->db->where('date <=', $end_date);
    $this->db->where_in('status', ['0', '1']); // 0=Pending, 1=In Progress
    return $this->db->count_all_results();
}


public function get_all_departmentssss() {
    // Change 'name' to your department name column if it's different
    $this->db->select('id, name'); 
    $this->db->from('departments');
    $this->db->order_by('name', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
}
// In hr_model.php, REPLACE the entire get_pending_requests_for_approver function
public function get_pending_requests_for_approver($approver_id) 
{
    // 1. Standard Requests Query (UPDATED to include Type 9)
    // 1. Standard Requests Query (UPDATED to include Type 9 and 12)
    $standard_query = "
        SELECT 
            aw.id as approval_id, 
            oe.id, 
            oe.id as order_id, 
            aw.order_type,
            oe.order_name, 
            oe.emp_name, 
            oe.emp_id,
            oe.date, 
            oe.time, 
            oe.status, 
            aw.status as approval_status, 
            aw.approval_level,
            'users1/view_request/' as url_prefix,
            oe.id as url_suffix,
            oe.reason_for_rejection,
            oe.file,
            NULL as remaining_balance,
            e.n1 as department,

            -- ✅ ADDED: Permission Columns
            oe.permission_start_time,
            oe.permission_end_time,
            oe.permission_hours,

            CASE
                WHEN oe.type = 12 THEN oe.permission_date  -- ✅ ADDED: Permission Date
                WHEN oe.type = 9 THEN oe.mission_date      -- Work Mission Date
                WHEN oe.type = 5 THEN oe.vac_start
                WHEN oe.type = 2 THEN oe.correction_date
                WHEN oe.type = 3 THEN oe.ot_date
                WHEN oe.type = 1 THEN oe.date_of_the_last_working
                ELSE oe.date
            END AS event_date

        FROM approval_workflow aw
        -- ✅ UPDATED JOIN: Allow types < 8 OR Type 9 OR Type 12 (الاستئذان)
        JOIN orders_emp oe ON aw.order_id = oe.id AND aw.order_type = oe.type AND (aw.order_type < 8 OR aw.order_type = 9 OR aw.order_type = 12)
        LEFT JOIN emp1 e ON oe.emp_id = e.employee_id 
        WHERE aw.approver_id = ?
        AND NOT EXISTS (
            SELECT 1 FROM approval_workflow aw2 
            WHERE aw2.order_id = aw.order_id AND aw2.order_type = aw.order_type
            AND aw2.approval_level < aw.approval_level AND aw2.status = 'pending'
        )
    ";
    
    $standard_requests = $this->db->query($standard_query, [$approver_id])->result_array();

    // 2. End of Service Settlements Query (Type 8)
    $settlement_query = "
        SELECT 
            aw.id as approval_id, 
            eos.id, 
            eos.id as order_id, 
            aw.order_type,
            'مستحقات نهاية الخدمة' as order_name, 
            e.subscriber_name as emp_name, 
            eos.employee_id as emp_id, 
            eos.created_at as date,
            DATE_FORMAT(eos.created_at, '%H:%i:%s') as time, 
            eos.status,
            aw.status as approval_status, 
            aw.approval_level,
            'users1/end_of_service?task_id=' as url_prefix,
            aw.id as url_suffix,
            aw.rejection_reason as reason_for_rejection,
            NULL as remaining_balance,
            NULL as file,
            e.n1 as department, 

            eos.created_at AS event_date

        FROM approval_workflow aw
        JOIN end_of_service_settlements eos ON aw.order_id = eos.id AND aw.order_type = 8
        JOIN emp1 e ON eos.employee_id = e.employee_id
        WHERE aw.approver_id = ?
        AND NOT EXISTS (
            SELECT 1 FROM approval_workflow aw2 
            WHERE aw2.order_id = aw.order_id AND aw2.order_type = aw.order_type
            AND aw2.approval_level < aw.approval_level AND aw2.status = 'pending'
        )
    ";
    
    $settlement_requests = $this->db->query($settlement_query, [$approver_id])->result_array();

    return array_merge($standard_requests, $settlement_requests);
}
// --- GET DISCOUNTS WITHIN SHEET DATE RANGE ---
public function get_employee_discounts_details($employee_id, $sheet_id)
{
    // 1. Get Sheet Dates
    $sheet = $this->db->get_where('salary_sheet', ['id' => $sheet_id])->row_array();
    if (!$sheet) return [];

    $start = $sheet['start_date'];
    $end   = $sheet['end_date'];

    // 2. Fetch Discounts
    $this->db->select('*');
    $this->db->from('orders.discounts');
    $this->db->where('emp_id', $employee_id);
    $this->db->where('discount_date >=', $start);
    $this->db->where('discount_date <=', $end);
    $this->db->order_by('discount_date', 'ASC');
    
    return $this->db->get()->result_array();
}

// --- GET REPARATIONS (ADDITIONS) WITHIN SHEET DATE RANGE ---
public function get_employee_reparations_details($employee_id, $sheet_id)
{
    // 1. Get Sheet Dates
    $sheet = $this->db->get_where('salary_sheet', ['id' => $sheet_id])->row_array();
    if (!$sheet) return [];

    $start = $sheet['start_date'];
    $end   = $sheet['end_date'];

    // 2. Fetch Reparations
    $this->db->select('*');
    $this->db->from('orders.reparations');
    $this->db->where('emp_id', $employee_id);
    $this->db->where('reparation_date >=', $start);
    $this->db->where('reparation_date <=', $end);
    $this->db->order_by('reparation_date', 'ASC');

    return $this->db->get()->result_array();
}

// In application/models/hr_model.php

public function insert_mission_attendance_logs($order_id)
{
    // 1. Get Order Details
    $order = $this->db->get_where('orders_emp', ['id' => $order_id])->row();

    // Ensure it's a Work Mission (Type 9) and has necessary data
    if (!$order || $order->type != 9) { 
        return;
    }

    // 2. Get Employee Details (To split Name)
    $emp = $this->db->get_where('emp1', ['employee_id' => $order->emp_id])->row();
    $full_name = $emp->subscriber_name ?? 'Unknown';
    
    // Split name into First and Last (Simple logic)
    $parts = explode(' ', $full_name, 2);
    $first_name = $parts[0] ?? '';
    $last_name  = $parts[1] ?? '';

    // 3. Calculate Timestamps
    $date = $order->mission_date; // Format: YYYY-MM-DD
    $start_time_str = "$date $order->mission_start_time";
    $end_time_str   = "$date $order->mission_end_time";

    // Handle case where mission ends the next day (e.g., Start 22:00, End 02:00)
    if (strtotime($end_time_str) < strtotime($start_time_str)) {
        $end_time_str = date('Y-m-d H:i:s', strtotime($end_time_str . ' +1 day'));
    }

    // 4. Prepare Records
    $current_time = date('Y-m-d H:i:s');
    
    // Record 1: Check In
    $log_in = [
        'emp_code'       => $order->emp_id,
        'first_name'     => $first_name,
        'last_name'      => $last_name,
        'punch_time'     => date('Y-m-d H:i:s', strtotime($start_time_str)),
        'punch_state'    => 'Check In',  // Change to '0' if your DB expects a number
        'area_alias'     => 'Work Mission',
        'terminal_sn'    => 'System',
        'terminal_alias' => 'Mission Approval',
        'upload_time'    => $current_time,
        'created_at'     => $current_time
    ];

    // Record 2: Check Out
    $log_out = [
        'emp_code'       => $order->emp_id,
        'first_name'     => $first_name,
        'last_name'      => $last_name,
        'punch_time'     => date('Y-m-d H:i:s', strtotime($end_time_str)),
        'punch_state'    => 'Check Out', // Change to '1' if your DB expects a number
        'area_alias'     => 'Work Mission',
        'terminal_sn'    => 'System',
        'terminal_alias' => 'Mission Approval',
        'upload_time'    => $current_time,
        'created_at'     => $current_time
    ];

    // 5. Insert into Database
    $this->db->insert('attendance_logs', $log_in);
    $this->db->insert('attendance_logs', $log_out);
    
    return true;
}

public function add_insurance_discount1($data)
{
    // Make sure employee ID is unique before inserting
    if ($this->check_discount_exists_for_employee($data['n1'])) {
        return false; // Prevent duplicate entry
    }
    return $this->db->insert('insurance_discount', $data);
}

public function delete_insurance_discount1($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('insurance_discount');
}

public function check_discount_exists_for_employee($employee_id)
{
    $this->db->where('n1', $employee_id);
    $this->db->from('insurance_discount');
    return $this->db->count_all_results() > 0;
}

public function get_employee_info1($employee_id) {
    if (empty($employee_id)) {
        return null;
    }
    $this->db->select('employee_id as username, subscriber_name as name'); // Select employee_id and subscriber_name
    $this->db->from('emp1'); // Query the correct employee table: emp1
    $this->db->where('employee_id', $employee_id); // Match using the employee_id field
    $query = $this->db->get();
    return $query->row_array(); // Return the result as an array
}

public function _get_aggregated_attendance_for_employees_range(array $emp_ids, $start_date_str, $end_date_str) {
    if (empty($emp_ids)) return [];

    // Use the existing helper to get all table names
    $tables = $this->_get_attendance_tables(); // Assuming this function exists and returns table names
    if (empty($tables)) return [];

    $start_datetime = $this->db->escape("$start_date_str 00:00:00");
    $end_datetime = $this->db->escape("$end_date_str 23:59:59");
    $emp_ids_sql = implode(',', array_map([$this->db, 'escape'], $emp_ids));

    $unions = [];
    foreach ($tables as $table) {
        // Basic check if table exists (optional but safer)
        if ($this->db->table_exists($table)) {
             $unions[] = sprintf(
                "(SELECT emp_code, punch_time FROM `%s` WHERE emp_code IN (%s) AND punch_time BETWEEN %s AND %s)",
                $table,
                $emp_ids_sql,
                $start_datetime,
                $end_datetime
            );
        }
    }

    if (empty($unions)) return [];

    $union_sql = implode(" UNION ALL ", $unions);

    // Outer query to aggregate MIN/MAX after UNION
    $sql = "SELECT
                emp_code,
                DATE(punch_time) AS punch_date,
                MIN(punch_time) AS first_punch,
                MAX(punch_time) AS last_punch,
                COUNT(punch_time) AS punch_count
            FROM ($union_sql) AS AllPunches
            GROUP BY emp_code, DATE(punch_time)";

    $query = $this->db->query($sql);

    // Structure the result as a map for easy lookup in the view
    $attendance_map = [];
    if ($query) {
        foreach ($query->result_array() as $row) {
            $attendance_map[$row['emp_code']][$row['punch_date']] = $row;
        }
    }
    return $attendance_map;
}
public function get_employees_with_approved_resignations()
{
    $this->db->select('id, emp_id, emp_name, date_of_the_last_working');
    $this->db->from('orders_emp');
    $this->db->where('type', 1); // 1 = Resignation
    $this->db->where('status', '2'); // 2 = Approved
    $this->db->order_by('emp_name', 'ASC');
    return $this->db->get()->result_array();
}

public function get_direct_manager($employee_id)
{
    if (empty($employee_id)) {
        return null;
    }

    $cols = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7'];
    $this->db->select(implode(',', $cols));
    $this->db->group_start();
    foreach ($cols as $col) {
        $this->db->or_where($col, $employee_id);
    }
    $this->db->group_end();
    $rows = $this->db->get('organizational_structure')->result_array();

    if (empty($rows)) return null;

    $deepest_level = -1;
    $structure_row = null;
    foreach($rows as $row) {
        foreach($cols as $level => $col) {
            if (isset($row[$col]) && $row[$col] == $employee_id && $level > $deepest_level) {
                $deepest_level = $level;
                $structure_row = $row;
            }
        }
    }

    if ($structure_row === null || $deepest_level === 0) {
        return null; // Employee is at the top or not found
    }

    $manager_id = $structure_row[$cols[$deepest_level - 1]] ?? null;
    if (!$manager_id) return null;

    return $this->db->select('username, name')->where('username', $manager_id)->get('users')->row_array();
}
// ADD this new function to your model
public function get_all_eos_tasks_for_user($approver_id)
{
    $this->db->select('
        aw.id as approval_id,
        aw.order_id as settlement_id,
        aw.status as approval_status,
        aw.action_date,
        eos.created_at as submission_date,
        e.subscriber_name as emp_name
    ');
    $this->db->from('approval_workflow aw');
    $this->db->join('end_of_service_settlements eos', 'eos.id = aw.order_id AND aw.order_type = 8');
    $this->db->join('emp1 e', 'e.employee_id = eos.employee_id', 'left');
    $this->db->where('aw.approver_id', $approver_id);
    $this->db->order_by('aw.id', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
}
// In hr_model.php
// REPLACE the entire get_full_resignation_process_details function with this one:

public function get_full_resignation_process_details($resignation_order_id)
{
    $details = [];

    // --- 1. Get main resignation request details ---
    $this->db->select('oe.*, u.name as creator_name');
    $this->db->from('orders_emp as oe');
    $this->db->join('users as u', 'oe.created_by_id = u.username', 'left');
    $this->db->where('oe.id', $resignation_order_id);
    $this->db->where('oe.type', 1); // Ensure it's a resignation request
    $resignation_data = $this->db->get()->row_array();

    if (!$resignation_data) {
        log_message('error', 'get_full_resignation_process_details: Resignation request ID ' . $resignation_order_id . ' not found or is not type 1.');
        return null;
    }

    $emp_id = $resignation_data['emp_id'];
    
    // --- 2. Try to get employee details from emp1 table, regardless of status ---
    $this->db->select('joining_date, total_salary');
    $this->db->from('emp1');
    $this->db->where('employee_id', $emp_id);
    $employee_data = $this->db->get()->row_array();
    
    // Merge resignation data with employee data
    $details['resignation'] = array_merge($resignation_data, $employee_data ?: []);

    $last_working_date_str = $details['resignation']['date_of_the_last_working'];
    $joining_date_str = $details['resignation']['joining_date'] ?? null;
    $total_salary = (float)($details['resignation']['total_salary'] ?? 0);
    $daily_rate = $total_salary > 0 ? $total_salary / 30.0 : 0;

    // --- 3. Calculate Service Period ---
    $service_period_string = 'غير متوفر';
    if (!empty($joining_date_str) && !empty($last_working_date_str)) {
        try {
            $join_date = new DateTime($joining_date_str);
            $last_day = new DateTime($last_working_date_str);
            $last_day->modify('+1 day'); 
            $diff = $join_date->diff($last_day);
            
            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' ' . ($diff->y > 1 ? 'سنوات' : 'سنة');
            if ($diff->m > 0) $parts[] = $diff->m . ' ' . ($diff->m > 1 ? 'أشهر' : 'شهر');
            if ($diff->d > 0) $parts[] = $diff->d . ' ' . ($diff->d > 1 ? 'أيام' : 'يوم');
            
            $service_period_string = !empty($parts) ? implode('، ', $parts) : 'أقل من يوم';
        } catch (Exception $e) {
             $service_period_string = 'خطأ في حساب المدة';
        }
    }
    $details['service_period'] = $service_period_string;

    // --- 4. Get End of Service settlement details ---
    $this->db->select('*'); 
    $this->db->from('end_of_service_settlements');
    $this->db->where('resignation_order_id', $resignation_order_id);
    $this->db->where('is_archived', 0); 
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $details['settlement'] = $this->db->get()->row_array();

    // --- 5. Determine Leave Balance ---
    $annual_leave_balance = 0.0;
    if ($details['settlement']) {
        // A settlement EXISTS. Use its values to reverse-calculate the balance.
        $compensation = (float)($details['settlement']['compensation'] ?? 0);
        $negative_deduction = (float)($details['settlement']['leave_balance_deduction'] ?? 0);
        
        if ($daily_rate > 0) {
            if ($compensation > 0) {
                // It was a positive balance
                $annual_leave_balance = $compensation / $daily_rate;
            } elseif ($negative_deduction > 0) {
                // It was a negative balance
                $annual_leave_balance = -($negative_deduction / $daily_rate);
            }
        }
        
    } else {
        // NO settlement exists. Get the CURRENT balance from the DB.
        $year = date('Y', strtotime($last_working_date_str ? $last_working_date_str : 'now'));
        $this->db->select('remaining_balance');
        $this->db->from('employee_leave_balances');
        $this->db->where('employee_id', $emp_id);
        $this->db->where('leave_type_slug', 'annual');
        $this->db->where('year', $year);
        $balance_query = $this->db->get()->row();
        $annual_leave_balance = $balance_query ? (float)$balance_query->remaining_balance : 0;
    }
    $details['annual_leave_balance'] = $annual_leave_balance;

    // --- 6. Get ALL Leave Balances ---
    $year = date('Y', strtotime($last_working_date_str ? $last_working_date_str : 'now')); 
    $this->db->select('lt.name_ar, elb.balance_allotted, elb.balance_consumed, elb.remaining_balance, elb.year');
    $this->db->from('employee_leave_balances as elb');
    $this->db->join('leave_types as lt', 'lt.slug = elb.leave_type_slug', 'left');
    $this->db->where('elb.employee_id', $emp_id);
    $this->db->where('elb.year', $year); 
    $this->db->where('lt.is_active', 1);
    $this->db->order_by('lt.name_ar', 'ASC');
    $details['all_leave_balances'] = $this->db->get()->result_array();

    // --- 7. Get settlement items and approval log ---
    if ($details['settlement'] && !empty($details['settlement']['id'])) {
        $settlement_id = $details['settlement']['id'];
        
        if (!empty($details['settlement']['items_json'])) {
            $decoded_items = json_decode($details['settlement']['items_json'], true);
            $details['settlement_items'] = is_array($decoded_items) ? $decoded_items : [];
        } else {
             $details['settlement_items'] = [];
        }
        
        $details['approval_log'] = $this->get_approval_log($settlement_id, 8);
    } else {
        $details['settlement_items'] = [];
        $details['approval_log'] = [];
    }

    // --- 8. Get all clearance tasks ---
    $this->db->select('
        rc.id as task_id, rc.status, rc.rejection_reason, rc.updated_at, rc.created_at,         
        rc.task_description, rc.approver_user_id,  cp.parameter_name,    
        d.name as department_name, u.name as approver_name 
    ');
    $this->db->from('resignation_clearances as rc');
    $this->db->join('clearance_parameters as cp', 'rc.clearance_parameter_id = cp.id', 'left');
    $this->db->join('departments as d', 'cp.department_id = d.id', 'left');
    $this->db->join('users as u', 'rc.approver_user_id = u.username', 'left');
    $this->db->where('rc.resignation_request_id', $resignation_order_id);
    $this->db->order_by('d.name ASC, rc.id ASC'); 
    
    $clearances_query = $this->db->get();
    $details['clearances'] = $clearances_query ? $clearances_query->result_array() : [];

    return $details;
}
public function reject_mandate_return_to_specialist($req_id, $approver_id, $reason)
{
    $this->db->trans_start();

    // 1. Mark the specific approver's step as 'Returned' in workflow
    // This ensures we know exactly which step was rejected
    $this->db->where('order_id', $req_id);
    $this->db->where('order_type', 'Mandate'); 
    $this->db->where('approver_id', $approver_id);
    $this->db->update('approval_workflow', [
        'status' => 'Returned', 
        'rejection_reason' => $reason, 
        'action_date' => date('Y-m-d H:i:s')
    ]);

    // 2. Update the main request to point to Specialist (2784)
    $data = [
        'current_approver' => '2784', // Send to Specialist instead
        'status'           => 'Returned', // Custom status
        'rejection_reason' => $reason,
        'rejected_from_id' => $approver_id // CRITICAL: Save WHO rejected it
    ];
    
    $this->db->where('id', $req_id);
    $this->db->update('mandate_requests', $data);

    $this->db->trans_complete();
    return $this->db->trans_status();
}

/**
 * RESUBMISSION LOGIC:
 * When 2784 submits the edit, we check the 'rejected_from_id' and route it back there.
 */
public function resubmit_mandate_after_revision($req_id)
{
    // 1. Find out who rejected it originally
    $this->db->select('rejected_from_id');
    $this->db->where('id', $req_id);
    $req = $this->db->get('mandate_requests')->row_array();
    
    $target_approver = $req['rejected_from_id'];

    if (!empty($target_approver)) {
        $this->db->trans_start();

        // 2. Reset that specific approver's workflow status to 'Pending'
        $this->db->where('order_id', $req_id);
        $this->db->where('order_type', 'Mandate'); 
        $this->db->where('approver_id', $target_approver);
        $this->db->update('approval_workflow', [
            'status' => 'Pending', 
            'action_date' => NULL, // Clear date so they can approve again
            'rejection_reason' => NULL
        ]);

        // 3. Point the main request back to that approver
        $data = [
            'current_approver' => $target_approver,
            'status'           => 'Pending',
            'rejection_reason' => NULL // Clear the error message
        ];
        
        $this->db->where('id', $req_id);
        $this->db->update('mandate_requests', $data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    return false;
}
public function get_pending_eos_settlements_for_approver($approver_id)
{
    $this->db->select('
        aw.id as approval_id, 
        eos.id,
        eos.id as order_id,
        aw.order_type,
        "مستحقات نهاية الخدمة" as order_name, 
        e.subscriber_name as emp_name, 
        eos.created_at as date,
        eos.status,
        "users1/end_of_service?task_id=" as url_prefix,
        aw.id as url_suffix
    ');
    $this->db->from('approval_workflow aw');
    $this->db->join('end_of_service_settlements eos', 'aw.order_id = eos.id AND aw.order_type = 8');
    $this->db->join('emp1 e', 'eos.employee_id = e.employee_id');
    $this->db->where('aw.approver_id', $approver_id);
    $this->db->where('aw.status', 'pending');
    $this->db->where("NOT EXISTS (
        SELECT 1 FROM approval_workflow aw2 
        WHERE aw2.order_id = aw.order_id AND aw2.order_type = aw.order_type
        AND aw2.approval_level < aw.approval_level AND aw2.status = 'pending'
    )", NULL, FALSE);
    
    return $this->db->get()->result_array();
}

// Add these new functions to hr_model.php
public function add_sequential_approval_step($order_id, $order_type, $approver_id, $approval_level, $status = 'pending')
{
    $data = [
        'order_id'       => $order_id,
        'order_type'     => $order_type,
        'approver_id'    => $approver_id,
        'approval_level' => $approval_level,
        'status'         => $status, // Now accepts status parameter
        
    ];
    
    return $this->db->insert('approval_workflow', $data);
}
// In hr_model.php
public function get_settlement_details_for_approval($approval_task_id, $approver_id) {
    // Get the main settlement data
    $this->db->select('eos.*, aw.status as approval_status, aw.id as approval_task_id');
    $this->db->from('end_of_service_settlements eos');
    $this->db->join('approval_workflow aw', 'aw.order_id = eos.id AND aw.order_type = 8');
    $this->db->where('aw.id', $approval_task_id);
    $this->db->where('aw.approver_id', $approver_id);
    // $this->db->where('aw.status', 'pending'); // <-- THIS LINE IS REMOVED
    $settlement = $this->db->get()->row_array();

    // If the main record is found, fetch all its associated items
    if ($settlement) {
        // <<< FIX: Read from items_json, not settlement_items table >>>
        if (!empty($settlement['items_json'])) {
            $decoded_items = json_decode($settlement['items_json'], true);
            $settlement['items'] = is_array($decoded_items) ? $decoded_items : [];
        } else {
            $settlement['items'] = [];
        }
    }

    return $settlement;
}
/**
     * Get End of Service details from database and prepare for email template
     */
    public function get_eos_details_for_email($settlement_id) {
        $this->db->select('eos.*, e.subscriber_name as emp_name');
        $this->db->from('end_of_service_settlements eos');
        $this->db->join('emp1 e', 'e.employee_id = eos.employee_id', 'left');
        $this->db->where('eos.id', $settlement_id);
        $order = $this->db->get()->row_array();
        
        if (!$order) return false;
        
        $details_array = [
            'رقم التسوية' => $order['id'] ?? '—',
            'الموظف' => $order['emp_name'] ?? '—',
            'نوع الطلب' => 'مخالصة نهاية الخدمة',
            'تاريخ الإنشاء' => $order['created_at'] ?? '—',
            'إجمالي الاستحقاق' => number_format((float)($order['compensation'] ?? 0), 2) . ' ريال',
            'مكافأة نهاية الخدمة' => number_format((float)($order['gratuity_amount'] ?? 0), 2) . ' ريال',
            'المبلغ النهائي' => '<strong style="color:#28a745">' . number_format((float)($order['final_amount'] ?? 0), 2) . ' ريال</strong>',
        ];
        
        $status = $order['status'] ?? '';
        if ($status === 'approved' || $status === 'Completed') {
            $details_array['حالة الطلب'] = '<span style="color:#28a745">معتمد نهائياً</span>';
        } elseif ($status === 'rejected' || $status === 'Rejected') {
            $details_array['حالة الطلب'] = '<span style="color:#dc3545">مرفوض</span>';
        } else {
            $details_array['حالة الطلب'] = '<span style="color:#FF8C00">قيد الاعتماد</span>';
        }
        
        return $details_array;
    }

    /**
     * Trigger email for End of Service Settlement
     */
    public function trigger_eos_email($settlement_id) {
        $this->db->where('id', $settlement_id);
        $order = $this->db->get('end_of_service_settlements')->row_array();
        
        if (!$order) return false;

        $applicant_id = $order['employee_id'];
        $status = strtolower($order['status'] ?? ''); 
        $current_approver = $order['current_approver'];
        
        $next_approver_id = null;
        $email_subject = "مخالصة نهاية خدمة بانتظار اعتمادك - Marsoom HR";
        $action_text = "مراجعة واعتماد المخالصة";
        $status_color = "#FF8C00";
        // Customize this link to wherever your approvers go to view EOS requests
        $custom_link = "https://services.marsoom.net/hr/users1/end_of_service?emp=" . $applicant_id; 

        // 1. ROUTING LOGIC
        if ($status === 'approved' || $current_approver === 'Completed') {
            $next_approver_id = $applicant_id;
            $email_subject = "تم اعتماد مخالصة نهاية الخدمة ✅ - Marsoom HR";
            $action_text = "الاطلاع على المخالصة";
            $status_color = "#28a745";
        } elseif ($status === 'rejected') {
            $next_approver_id = $applicant_id;
            $email_subject = "تم رفض مخالصة نهاية الخدمة ❌ - Marsoom HR";
            $action_text = "مراجعة التفاصيل";
            $status_color = "#dc3545";
        } elseif (!empty($current_approver)) {
            $next_approver_id = $current_approver; // Normal pending workflow
        } else {
            return false;
        }

        // 2. FETCH RECIPIENT
        $recipient = $this->db->select('email, subscriber_name')->where('employee_id', $next_approver_id)->get('emp1')->row_array();
        
        if (!$recipient || empty(trim($recipient['email']))) {
            $this->db->insert('email_logs', [
                'order_id' => $settlement_id,
                'recipient_email' => $next_approver_id . ' (NO EMAIL FOUND)',
                'subject' => $email_subject,
                'status' => 'failed',
                'error_message' => 'لم يتم إرسال إيميل المخالصة لأن الموظف ' . $next_approver_id . ' ليس لديه بريد مسجل.',
                'sent_at' => date('Y-m-d H:i:s')
            ]);
            return false;
        }

        $applicant = $this->db->select('subscriber_name')->where('employee_id', $applicant_id)->get('emp1')->row_array();
        $recipient_name = $recipient['subscriber_name'] ?? 'الموظف';
        $applicant_name = $applicant['subscriber_name'] ?? 'موظف';

        // 3. BUILD TEXT
        if ($next_approver_id == $applicant_id) {
            if ($status === 'rejected') {
                $headline = "تم رفض مخالصة نهاية الخدمة";
                $body_text = "نأسف لإبلاغك بأنه قد تم رفض أو إلغاء تسوية نهاية الخدمة الخاصة بك.";
            } else {
                $headline = "تم اعتماد المخالصة النهائية";
                $body_text = "تم الاعتماد النهائي لمخالصة نهاية الخدمة الخاصة بك. يمكنك الاطلاع على التفاصيل والمبلغ النهائي.";
            }
        } else {
            $headline = "مخالصة نهاية خدمة بانتظار اعتمادك";
            $body_text = "تم تجهيز مخالصة نهاية الخدمة للموظف <b>{$applicant_name}</b> وهي الآن بانتظار مراجعتك واعتمادك لإكمال الإجراءات.";
        }

        $details = $this->get_eos_details_for_email($settlement_id);

        $html = $this->build_beautiful_email_template($recipient_name, $headline, $body_text, $details, $settlement_id, $action_text, $status_color, $custom_link);
        
        return $this->send_html_email($recipient['email'], $email_subject, $html, $settlement_id);
    }

/**
     * Get Clearance Task details from database and prepare for email template
     */
    public function get_clearance_details_for_email($task_id) {
        $this->db->select('rc.*, cp.parameter_name, e.subscriber_name as emp_name');
        $this->db->from('resignation_clearances rc');
        $this->db->join('clearance_parameters cp', 'cp.id = rc.clearance_parameter_id', 'left');
        $this->db->join('orders_emp req', 'req.id = rc.resignation_request_id', 'left');
        $this->db->join('emp1 e', 'e.employee_id = req.emp_id', 'left');
        $this->db->where('rc.id', $task_id);
        $order = $this->db->get()->row_array();
        
        if (!$order) return false;
        
        // Fallback for task name in case it's a custom task
        $task_name = !empty($order['parameter_name']) ? $order['parameter_name'] : ($order['task_description'] ?? 'مهمة إخلاء طرف');
        
        $details_array = [
            'رقم طلب الاستقالة' => $order['resignation_request_id'] ?? '—',
            'الموظف' => $order['emp_name'] ?? '—',
            'نوع الطلب' => 'إخلاء طرف',
            'المهمة المطلوبة' => $task_name,
        ];
        
        $status = $order['status'] ?? '';
        if ($status === 'approved') {
            $details_array['حالة المهمة'] = '<span style="color:#28a745">مكتملة (تم الاعتماد)</span>';
        } elseif ($status === 'rejected') {
            $details_array['حالة المهمة'] = '<span style="color:#dc3545">مرفوضة</span>';
            if (!empty($order['rejection_reason'])) {
                $details_array['سبب الرفض'] = '<span style="color:#dc3545">' . htmlspecialchars($order['rejection_reason']) . '</span>';
            }
        } else {
            $details_array['حالة المهمة'] = '<span style="color:#FF8C00">قيد الاعتماد</span>';
        }
        
        return $details_array;
    }

    /**
     * Trigger an email for a specific Clearance Task (Approval, Rejection, or Pending)
     */
    public function trigger_clearance_email($task_id) {
        $this->db->select('rc.*, cp.parameter_name, req.emp_id');
        $this->db->from('resignation_clearances rc');
        $this->db->join('clearance_parameters cp', 'cp.id = rc.clearance_parameter_id', 'left');
        $this->db->join('orders_emp req', 'req.id = rc.resignation_request_id', 'left');
        $this->db->where('rc.id', $task_id);
        $order = $this->db->get()->row_array();
        
        if (!$order) return false;

        $applicant_id = $order['emp_id'];
        $status = strtolower($order['status'] ?? ''); 
        $approver_id = $order['approver_user_id'];
        
        $next_approver_id = null;
        $email_subject = "مهمة إخلاء طرف بانتظار اعتمادك - Marsoom HR";
        $action_text = "مراجعة واعتماد المهمة";
        $status_color = "#FF8C00";
        $custom_link = "https://services.marsoom.net/hr/users1/my_clearance_tasks"; 

        if ($status === 'approved') {
            // Send notification to the applicant that a department cleared them
            $next_approver_id = $applicant_id;
            $email_subject = "تم اعتماد مهمة إخلاء طرف ✅ - Marsoom HR";
            $action_text = "الدخول للنظام";
            $status_color = "#28a745";
            $custom_link = "https://services.marsoom.net/hr/users1/orders_emp"; 
        } elseif ($status === 'rejected') {
            // Send notification to applicant that there is an issue with their clearance
            $next_approver_id = $applicant_id;
            $email_subject = "تم رفض مهمة إخلاء طرف ❌ - Marsoom HR";
            $action_text = "مراجعة التفاصيل";
            $status_color = "#dc3545";
            $custom_link = "https://services.marsoom.net/hr/users1/orders_emp";
        } elseif ($status === 'pending') {
            // Send to the department head/manager assigned to the task
            $next_approver_id = $approver_id; 
        } else {
            return false;
        }

        $recipient = $this->db->select('email, subscriber_name')->where('employee_id', $next_approver_id)->get('emp1')->row_array();
        
        if (!$recipient || empty(trim($recipient['email']))) {
            $this->db->insert('email_logs', [
                'order_id' => $order['resignation_request_id'],
                'recipient_email' => $next_approver_id . ' (NO EMAIL FOUND)',
                'subject' => $email_subject,
                'status' => 'failed',
                'error_message' => 'لم يتم إرسال إيميل (إخلاء طرف) لأن الموظف ' . $next_approver_id . ' ليس لديه بريد مسجل.',
                'sent_at' => date('Y-m-d H:i:s')
            ]);
            return false;
        }

        $applicant = $this->db->select('subscriber_name')->where('employee_id', $applicant_id)->get('emp1')->row_array();
        $recipient_name = $recipient['subscriber_name'] ?? 'الموظف';
        $applicant_name = $applicant['subscriber_name'] ?? 'موظف';
        
        $task_name = !empty($order['parameter_name']) ? $order['parameter_name'] : ($order['task_description'] ?? 'مهمة إخلاء طرف');

        if ($next_approver_id == $applicant_id) {
            if ($status === 'rejected') {
                $headline = "تعذر إكمال إخلاء الطرف";
                $body_text = "نأسف لإبلاغك بأنه قد تم رفض أو إرجاع مهمة (<b>{$task_name}</b>) المتعلقة بإخلاء الطرف الخاص بك. يرجى مراجعة السبب.";
            } else {
                $headline = "تم إنجاز إحدى مهام إخلاء الطرف";
                $body_text = "لقد تم اعتماد وإنجاز مهمة (<b>{$task_name}</b>) الخاصة بك بنجاح.";
            }
        } else {
            $headline = "مهمة إخلاء طرف بانتظار اعتمادك";
            $body_text = "يوجد مهمة إخلاء طرف (<b>{$task_name}</b>) للموظف <b>{$applicant_name}</b> بانتظار مراجعتك واعتمادك.";
        }

        $details = $this->get_clearance_details_for_email($task_id);

        $html = $this->build_beautiful_email_template($recipient_name, $headline, $body_text, $details, $order['resignation_request_id'], $action_text, $status_color, $custom_link);
        
        return $this->send_html_email($recipient['email'], $email_subject, $html, $order['resignation_request_id']);
    }

    /**
     * Finds all pending tasks for a new clearance and emails everyone
     */
    public function trigger_clearance_initiation_emails($resignation_id) {
        $this->db->select('id');
        $this->db->where('resignation_request_id', $resignation_id);
        $this->db->where('status', 'pending');
        $tasks = $this->db->get('resignation_clearances')->result_array();
        
        foreach ($tasks as $task) {
            $this->trigger_clearance_email($task['id']);
        }
    }


    
/**
 * 
 * 
 * 
 * 
 * Gets a list of approved resignation requests to be used in the clearance form search.
 * This version is corrected to prevent a SQL syntax error.
 */
public function get_approved_resignations() {
    $this->db->select('id, emp_id, emp_name, date');
    $this->db->from('orders_emp');
    $this->db->where('type', 1); // Resignation type

    // === MODIFIED: Include statuses for approved AND in-clearance ===
    // Statuses:
    // '2': Approved (Clearance not started)
    // '10': Pending Manager Clearance Approval (Optional, but safe to include)
    // '11': Pending Departmental Clearance
    $valid_statuses = ['2', '10', '11'];
    $this->db->where_in('status', $valid_statuses);
    // === END MODIFIED ===

    // REMOVED: $this->db->where("NOT EXISTS (SELECT 1 FROM resignation_clearances WHERE resignation_request_id = orders_emp.id)", NULL, FALSE);
    $this->db->order_by('date', 'DESC');
    return $this->db->get()->result_array();
}
public function check_existing_clearance_tasks($resignation_id) {
    if (empty($resignation_id)) {
        return false;
    }
    $this->db->where('resignation_request_id', $resignation_id);
    $this->db->from('resignation_clearances');
    return $this->db->count_all_results() > 0;
}

public function cancel_existing_clearance_tasks($resignation_id) {
    if (empty($resignation_id)) {
        return 0;
    }
    $this->db->where('resignation_request_id', $resignation_id);
    $this->db->where('status', 'pending'); // Only cancel pending tasks
    $this->db->update('resignation_clearances', ['status' => 'cancelled']);
    return $this->db->affected_rows();
}
/**
 * Initiates the clearance process by saving selected departments and sending to the direct manager.
 */
public function initiate_clearance_process($resignation_id, $employee_id, $department_ids)
{
    $this->db->trans_start();

    // 1. Save the selected departments for later use
    $this->db->where('resignation_request_id', $resignation_id);
    $this->db->delete('clearance_selected_departments'); // Clear old ones first
    
    $batch_data = [];
    foreach ($department_ids as $dept_id) {
        $batch_data[] = [
            'resignation_request_id' => $resignation_id,
            'department_id' => $dept_id
        ];
    }
    $this->db->insert_batch('clearance_selected_departments', $batch_data);

    // 2. Find the direct manager (using your existing approval chain logic)
    $manager_chain = $this->get_approval_chain($employee_id, 1);
    $manager_id = !empty($manager_chain) ? $manager_chain[0] : null;

    if (!$manager_id) {
        // If no manager, we can't proceed with this workflow.
        // Or, you could have it skip to the next step. For now, we stop.
        $this->db->trans_complete();
        return false;
    }

    // 3. Update the main resignation request to a new status indicating it's waiting for manager's clearance approval
    $this->db->where('id', $resignation_id);
    $this->db->update('orders_emp', [
        'status' => '10', // Use '10' as a new status for "Pending Manager Clearance Approval"
        'responsible_employee' => $manager_id
    ]);

    // 4. Create the first approval step for the manager
    // We will use a new order_type, '9', for the overall Clearance Process
    $this->add_approval_step($resignation_id, 9, $manager_id, 1);
    
    $this->db->trans_complete();
    return $this->db->trans_status();
}

/**
 * This function is called AFTER a manager approves the initial clearance request.
 * It reads the selected departments and creates all individual department tasks.
 */
public function create_departmental_clearance_tasks($resignation_id)
{
    // 1. Get the department IDs that HR selected
    $this->db->select('department_id');
    $this->db->where('resignation_request_id', $resignation_id);
    $selected_depts_query = $this->db->get('clearance_selected_departments');
    $department_ids = array_column($selected_depts_query->result_array(), 'department_id');

    if (empty($department_ids)) {
        // No departments were selected, so we can consider the process complete.
        $this->db->where('id', $resignation_id)->update('orders_emp', ['status' => '2']); // Final Approved status
        return true;
    }

    // 2. Get all active parameters for these departments
    $parameters = $this->get_parameters_for_departments($department_ids);

    $this->db->trans_start();

    // 3. Create a new task in `resignation_clearances` for each parameter
    if (!empty($parameters)) {
        $tasks_to_insert = [];
        foreach ($parameters as $param) {
            $tasks_to_insert[] = [
                'resignation_request_id' => $resignation_id,
                'clearance_parameter_id' => $param['id'],
                'approver_user_id'       => $param['approver_user_id'],
                'created_by_user_id'     => $this->session->userdata('username'),
                'status'                 => 'pending'
            ];
        }
        $this->db->insert_batch('resignation_clearances', $tasks_to_insert);
    }

    // 4. Update the main resignation request status to show it's now in departmental clearance phase
    $this->db->where('id', $resignation_id);
    $this->db->update('orders_emp', ['status' => '11']); // Use '11' for "Pending Departmental Clearance"

    $this->db->trans_complete();
    return $this->db->trans_status();
}



public function update_settlement_first_approver($settlement_id, $approver_id) {
    $this->db->where('id', $settlement_id);
    $this->db->update('end_of_service_settlements', ['current_approver' => $approver_id]);
}

public function process_settlement_action($task_id, $action, $approver_id, $reason = null)
{
    $this->db->trans_start();

    // 1. Find the task and ensure it's pending for this user
    $this->db->where('id', $task_id);
    $this->db->where('approver_id', $approver_id);
    $this->db->where('status', 'pending');
    $task = $this->db->get('approval_workflow')->row();

    if (!$task) {
        $this->db->trans_complete();
        return false; // Task not found or not pending for this user
    }

    // 2. Update the current task
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';
    $this->db->where('id', $task_id);
    $this->db->update('approval_workflow', [
        'status' => $new_status, 
        'rejection_reason' => $reason, 
        'action_date' => date('Y-m-d H:i:s')
    ]);

    $settlement_id = $task->order_id;
    
   if ($action === 'approve') {
        // Find the next approver in the chain
        $next_approver_query = $this->db->from('approval_workflow')
            ->where('order_id', $settlement_id)
            ->where('order_type', 8)
            ->where('status', 'waiting')  // <-- **** FIX #1: Look for 'waiting' ****
            ->order_by('approval_level', 'ASC')
            ->limit(1)->get();

        if ($next_approver_query->num_rows() > 0) {
            // --- There is a next approver ---
            $next_approver = $next_approver_query->row();
            
            // **** FIX #2: Add this block to activate the next task ****
            $this->db->where('id', $next_approver->id);
            $this->db->update('approval_workflow', ['status' => 'pending']);
            // **** END OF FIX #2 ****

            $this->db->where('id', $settlement_id)->update('end_of_service_settlements', [
                'status' => 'pending_level_' . $next_approver->approval_level,
                'current_approver' => $next_approver->approver_id
            ]);
        } else {
            // --- This was the final approval ---
            $this->db->where('id', $settlement_id)->update('end_of_service_settlements', [
                'status' => 'approved',
                'current_approver' => null
            ]);
        }
    } else { 
        // --- Action is 'reject' ---
        // Update main settlement status to rejected
        $this->db->where('id', $settlement_id)->update('end_of_service_settlements', [
            'status' => 'rejected',
            'current_approver' => null
        ]);
        
        // Mark any subsequent steps as 'skipped'
        $this->db->where('order_id', $settlement_id)
                 ->where('order_type', 8)
                 ->where('approval_level >', $task->approval_level)
                 ->update('approval_workflow', ['status' => 'skipped']);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}
    // In hr_model.php - REPLACE the entire old function with this new one

public function process_approval($order_id, $approver_id)
{
    $this->db->trans_start();

    // 1. Get the current request details first, including its type
    $request = $this->db->get_where('orders_emp', ['id' => $order_id])->row_array();
    if (!$request) {
        $this->db->trans_complete();
        return false; // Request not found
    }
    $request_type = (int)$request['type'];

    // 2. Mark the current approval step as 'approved'
    $this->db->where('order_id', $order_id);
    $this->db->where('approver_id', $approver_id);
    $this->db->where('status', 'pending');
    $this->db->update('approval_workflow', ['status' => 'approved', 'action_date' => date('Y-m-d H:i:s')]);
    
    // If this update didn't affect any rows, it means the task wasn't pending for this user.
    if ($this->db->affected_rows() === 0) {
        $this->db->trans_complete(); // Finalize the transaction (even if it did nothing)
        return false; // Stop execution
    }

    // 3. Check if there are any OTHER pending approval steps for this order
    // Note: We intentionally skip checking "waiting" statuses and only look for "pending"
    $next_approver_query = $this->db->select('approver_id')
                                    ->from('approval_workflow')
                                    ->where('order_id', $order_id)
                                    ->where('order_type', $request_type)
                                    ->where('status', 'pending')
                                    ->order_by('approval_level', 'ASC')
                                    ->limit(1)
                                    ->get();

    if ($next_approver_query->num_rows() > 0) {
        // --- THERE IS A NEXT APPROVER ---
        // Update the main request to point to them and set status to "In Progress" (1)
        $next_approver_id = $next_approver_query->row()->approver_id;
        $this->db->where('id', $order_id)->update('orders_emp', ['status' => '1', 'responsible_employee' => $next_approver_id]);
    
    } else {
        // --- THIS IS THE FINAL APPROVAL ---
        
        // First, set the main status to 'Approved' (2) for all types.
        $this->db->where('id', $order_id)->update('orders_emp', ['status' => '2', 'responsible_employee' => null]);

        // Then, perform any special actions based on the request type.
        switch ($request_type) {
            case 5: // Leave Request
                // Deduct balance and handle sick leave compensation
                $this->approve_leave_request($order_id);
                break;
            case 2: // Fingerprint Correction
                // Insert manual punches into attendance logs
                $this->approve_fingerprint_correction($order_id);
                break;
            case 3: // Overtime Request
                // Calculate pay and insert into the reparations table
                $this->calculate_and_record_ot_pay($order_id); 
                break;
            case 10: // Work Mission (مهمة العمل)
                // No automated financial action is required upon final approval. 
                // Any subsequent attendance tracking or payroll effect is handled downstream.
                break;
            // Add other types here if they require final actions (e.g., generating letters, etc.)
        }
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}
// --- ADD TO hr_model.php ---
// --- UPDATE in hr_model.php ---

public function get_suspensions_in_range($emp_id, $range_start, $range_end) {
    // 1. Convert inputs to timestamps
    $r_start = strtotime($range_start);
    $r_end   = strtotime($range_end);
    
    // 2. Query stops that overlap with this range
    $this->db->from('stop_salary');
    $this->db->where('emp_id', $emp_id);
    
    // Overlap Logic: (Start <= RangeEnd) AND (End >= RangeStart OR End is NULL)
    $this->db->group_start();
        $this->db->where('start_date <=', $range_end);
        $this->db->group_start();
            $this->db->where('end_date >=', $range_start);
            $this->db->or_where('end_date', NULL);
        $this->db->group_end();
    $this->db->group_end();
    
    $suspensions = $this->db->get()->result();
    
    $suspended_days_count = 0;

    foreach ($suspensions as $sus) {
        // Determine intersection start
        $sus_start = strtotime($sus->start_date);
        $effective_start = max($r_start, $sus_start);
        
        // Determine intersection end
        if ($sus->end_date) {
            $sus_end = strtotime($sus->end_date);
            $effective_end = min($r_end, $sus_end);
        } else {
            // Open-ended stop: goes until the end of this payroll sheet
            $effective_end = $r_end;
        }

        // Count days if valid range
        if ($effective_end >= $effective_start) {
            // +1 because Date Difference needs to be inclusive (Jan 1 to Jan 1 is 1 day)
            $days = ($effective_end - $effective_start) / (60 * 60 * 24) + 1;
            $suspended_days_count += $days;
        }
    }
    
    return $suspended_days_count;
}
// --- ADD TO Hr_model.php ---

public function get_single_punches($start_date, $end_date) {
    // Logic: Group by Employee + Date -> Select those having Count = 1
    $this->db->select('emp_code, first_name, last_name, DATE(punch_time) as log_date, MIN(punch_time) as punch_time, terminal_alias, COUNT(*) as cnt');
    $this->db->from('attendance_logs');
    $this->db->where('punch_time >=', $start_date . ' 00:00:00');
    $this->db->where('punch_time <=', $end_date . ' 23:59:59');
    $this->db->group_by(['emp_code', 'DATE(punch_time)']);
    $this->db->having('cnt', 1); // Only where there is EXACTLY 1 punch
    $this->db->order_by('log_date', 'DESC');
    $this->db->order_by('emp_code', 'ASC');
    
    return $this->db->get()->result_array();
}
// --- ADD TO Hr_model.php ---

// Fetch raw logs for processing
public function get_raw_attendance_logs($start_date, $end_date) {
    $this->db->select('*');
    $this->db->from('attendance_logs');
    $this->db->where('punch_time >=', $start_date . ' 00:00:00');
    $this->db->where('punch_time <=', $end_date . ' 23:59:59');
    $this->db->order_by('emp_code', 'ASC');
    $this->db->order_by('punch_time', 'ASC');
    return $this->db->get()->result_array();
}

// Bulk delete function
public function delete_attendance_logs($ids) {
    if (empty($ids)) return false;
    $this->db->where_in('id', $ids);
    return $this->db->delete('attendance_logs');
}
// 1. Add Suspension Record
public function add_salary_suspension($data) {
    // Ensure sheet_id is set (your table requires it)
    if (!isset($data['sheet_id'])) {
        $data['sheet_id'] = 0;
    }
    return $this->db->insert('stop_salary', $data);
}
// 2. Get Active Suspensions for an Employee
public function get_employee_suspensions($emp_id) {
    $this->db->where('emp_id', $emp_id);
    $this->db->order_by('start_date', 'DESC');
    return $this->db->get('stop_salary')->result();
}

/**
 * CORE FUNCTION: Calculate Payable Days in a Month
 * This is what makes the payroll "Pro-Rata" work (e.g., paying only Jan 1-9)
 */
public function get_payable_days($emp_id, $month, $year) {
    // 1. Define Month Start/End
    $month_start = "$year-$month-01";
    $month_end   = date("Y-m-t", strtotime($month_start));
    $total_days_in_month = 30; // Standard HR calculation (or use date('t') for actual)

    // 2. Check for suspensions that overlap with this month
    $this->db->where('emp_id', $emp_id);
    $this->db->group_start();
        // Overlap Logic: (Start <= MonthEnd) AND (End >= MonthStart OR End is NULL)
        $this->db->where('start_date <=', $month_end);
        $this->db->group_start();
            $this->db->where('end_date >=', $month_start);
            $this->db->or_where('end_date', NULL);
        $this->db->group_end();
    $this->db->group_end();
    
    $suspensions = $this->db->get('stop_salary')->result();

    $suspended_days_count = 0;

    foreach ($suspensions as $sus) {
        // Calculate intersection of Suspension vs This Month
        $start = max(strtotime($sus->start_date), strtotime($month_start));
        
        if ($sus->end_date) {
            $end = min(strtotime($sus->end_date), strtotime($month_end));
        } else {
            $end = strtotime($month_end);
        }

        if ($end >= $start) {
            // Calculate days difference + 1
            $days = ($end - $start) / (60 * 60 * 24) + 1;
            $suspended_days_count += $days;
        }
    }

    // Ensure we don't go below 0
    $payable_days = max(0, $total_days_in_month - $suspended_days_count);
    
    return [
        'payable_days' => $payable_days,
        'suspended_days' => $suspended_days_count,
        'is_fully_stopped' => ($payable_days == 0)
    ];
}

/**
 * Helper to check if Leave should accrue on a specific date
 * Use this in your Leave Cron Job/Script
 */
public function is_leave_accrual_paused($emp_id, $date = null) {
    $date = $date ? $date : date('Y-m-d');
    
    $this->db->where('emp_id', $emp_id);
    $this->db->where('stop_leave_accrual', 1);
    $this->db->where('start_date <=', $date);
    $this->db->group_start();
        $this->db->where('end_date >=', $date);
        $this->db->or_where('end_date', NULL);
    $this->db->group_end();
    
    return $this->db->count_all_results('stop_salary') > 0;
}
public function get_all_salary_sheets_simple()
{
    $this->db->select('id, type, start_date, end_date');
    $this->db->from('salary_sheet');
    $this->db->order_by('id', 'DESC');
    return $this->db->get()->result_array();
}

private function _get_datatables_query_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor = false, $filter_my_requests = false, $filter_pending_ceo = false, $filter_mission_type = null)
{
    // ============================================================
    // 1. PRE-FETCH FILTER DATES (With Safety Checks)
    // ============================================================
    $sheet_start = null;
    $sheet_end = null;
    $sheet_id_input = $this->input->post('filter_sheet_id');

    if (!empty($sheet_id_input)) {
        // Run query directly
        $query_str = "SELECT start_date, end_date FROM salary_sheet WHERE id = ?";
        $sheet_query = $this->db->query($query_str, [$sheet_id_input]);
        
        // Safety Check: Ensure query succeeded and returned a row
        if ($sheet_query && $sheet_query->num_rows() > 0) {
            $sheet = $sheet_query->row();
            $sheet_start = $sheet->start_date;
            $sheet_end = $sheet->end_date;
        }
    }

    // ============================================================
    // 2. BUILD MAIN QUERY
    // ============================================================
    
    // Select standard columns - ADD mission_type
    $this->db->select('
        orders_emp.id, 
        orders_emp.emp_id, 
        orders_emp.emp_name, 
        orders_emp.date, 
        orders_emp.time, 
        orders_emp.order_name, 
        orders_emp.status, 
        orders_emp.responsible_employee, 
        orders_emp.file, 
        orders_emp.vac_main_type,
        orders_emp.type,
        orders_emp.mission_type, 
        creator.name AS creator_name,
        emp1.company_name,
        emp1.location
    ');

    // Select Event Date (Formatted as a single string to avoid syntax errors)
    // ADD mission_date for type 9
    $this->db->select('(CASE 
        WHEN orders_emp.type = 5 THEN orders_emp.vac_start 
        WHEN orders_emp.type = 2 THEN orders_emp.correction_date 
        WHEN orders_emp.type = 3 THEN orders_emp.ot_date 
        WHEN orders_emp.type = 1 THEN orders_emp.date_of_the_last_working 
        WHEN orders_emp.type = 9 THEN orders_emp.mission_date      
        ELSE orders_emp.date 
    END) AS event_date', FALSE);

    $this->db->from('orders_emp');
    $this->db->join('users AS creator', 'creator.username = orders_emp.created_by_id', 'left');
    $this->db->join('emp1', 'emp1.employee_id = orders_emp.emp_id', 'left');

    // ============================================================
    // 3. CORE PERMISSION & FILTER LOGIC
    // ============================================================
    
    // CASE A: CEO Pending Filter (Highest Priority)
    if ($filter_pending_ceo) {
        $this->db->group_start();
            $this->db->where('orders_emp.status', '0'); // Pending
            $this->db->or_where('orders_emp.status', '1'); // Processing
        $this->db->group_end();
        $this->db->where('orders_emp.responsible_employee', '1001');
    }
    // CASE B: My Requests Filter
    elseif ($filter_my_requests) {
        $this->db->where('orders_emp.emp_id', $logged_in_user_id);
    } 
    // CASE C: Standard Role-Based View
    else {
        if ($is_hr_user) {
            // HR sees all
        } elseif ($is_abha_supervisor) {
            $this->db->group_start();
            $this->db->where('orders_emp.emp_id', $logged_in_user_id);
            $this->db->or_where('emp1.location', 'أبها');
            $this->db->group_end();
        } else {
            // Regular employee sees only their own
            $this->db->where('orders_emp.emp_id', $logged_in_user_id);
        }
    }

    // --- Department Filter Logic (My Team) ---
    // Note: This should not apply when "My Requests" or "CEO Pending" filters are active
    if (!$filter_my_requests && !$filter_pending_ceo && $this->input->post('filter_my_department') === 'true') {
        // Fetch department in a separate query to keep main query clean
        $dept_query = $this->db->query("SELECT n1 FROM emp1 WHERE employee_id = ?", [$logged_in_user_id]);
        if ($dept_query && $dept_query->num_rows() > 0) {
            $my_dept = $dept_query->row()->n1;
            $this->db->where('emp1.n1', $my_dept);
        }
    }

    // --- Salary Sheet Filter Logic ---
    if ($sheet_start && $sheet_end) {
        // Open a bracket for the whole date logic: AND ( ... )
        $this->db->group_start(); 
            
            // 1. Vacation
            $this->db->group_start();
                $this->db->where('orders_emp.type', 5);
                $this->db->where('orders_emp.vac_start >=', $sheet_start);
                $this->db->where('orders_emp.vac_start <=', $sheet_end);
            $this->db->group_end();
            
            // 2. Fingerprint
            $this->db->or_group_start();
                $this->db->where('orders_emp.type', 2);
                $this->db->where('orders_emp.correction_date >=', $sheet_start);
                $this->db->where('orders_emp.correction_date <=', $sheet_end);
            $this->db->group_end();

            // 3. Overtime
            $this->db->or_group_start();
                $this->db->where('orders_emp.type', 3);
                $this->db->where('orders_emp.ot_date >=', $sheet_start);
                $this->db->where('orders_emp.ot_date <=', $sheet_end);
            $this->db->group_end();

            // 4. Resignation
            $this->db->or_group_start();
                $this->db->where('orders_emp.type', 1);
                $this->db->where('orders_emp.date_of_the_last_working >=', $sheet_start);
                $this->db->where('orders_emp.date_of_the_last_working <=', $sheet_end);
            $this->db->group_end();
            
            // 5. Work Mission (Type 9)
            $this->db->or_group_start();
                $this->db->where('orders_emp.type', 9); // <-- ADD THIS
                $this->db->where('orders_emp.mission_date >=', $sheet_start);
                $this->db->where('orders_emp.mission_date <=', $sheet_end);
            $this->db->group_end();
            
            // 6. Others (General Date)
            $this->db->or_group_start();
                $this->db->where_not_in('orders_emp.type', [1, 2, 3, 5, 9]); // <-- ADD 9 to exclude list
                $this->db->where('orders_emp.date >=', $sheet_start);
                $this->db->where('orders_emp.date <=', $sheet_end);
            $this->db->group_end();

        $this->db->group_end(); // Close the main date logic bracket
    }

    // --- Standard Filters ---
    if ($this->input->post('filter_request_id')) {
        $this->db->like('orders_emp.id', $this->input->post('filter_request_id'));
    }
    if ($this->input->post('filter_creator')) {
        $this->db->like('creator.name', $this->input->post('filter_creator'));
    }
    if ($this->input->post('filter_leave_type')) {
        $this->db->where('orders_emp.vac_main_type', $this->input->post('filter_leave_type'));
    }
    if ($this->input->post('filter_name')) {
        $this->db->like('orders_emp.emp_name', $this->input->post('filter_name'));
    }
    if ($this->input->post('filter_company')) {
        $this->db->where('emp1.company_name', $this->input->post('filter_company'));
    }
    if ($this->input->post('filter_employee_id')) {
        $this->db->like('orders_emp.emp_id', $this->input->post('filter_employee_id'));
    }
    if ($this->input->post('filter_date')) {
        $this->db->where('orders_emp.date', $this->input->post('filter_date'));
    }
    if ($this->input->post('filter_type')) {
        $this->db->where('orders_emp.order_name', $this->input->post('filter_type'));
    }
    if ($this->input->post('filter_status') !== '' && $this->input->post('filter_status') !== null) {
        $this->db->where('orders_emp.status', $this->input->post('filter_status'));
    }

    
    // --- NEW: Mission Type Filter ---
    if ($filter_mission_type) {
        $this->db->where('orders_emp.mission_type', $filter_mission_type);
    }

    $this->db->order_by('orders_emp.id', 'DESC');
}
public function get_eos_settlement_print_data($id)
{
    $this->db->select('
        eos.final_amount,
        eos.id as settlement_id,
        emp.subscriber_name,
        emp.id_number,
        emp.employee_id as emp_code,
        emp.n1 as department
    ');
    $this->db->from('end_of_service_settlements as eos');
    $this->db->join('emp1 as emp', 'emp.employee_id = eos.employee_id', 'left');
    $this->db->join('orders_emp as oe', 'oe.id = eos.resignation_order_id', 'left');
    
    $this->db->group_start();
        $this->db->where('eos.id', $id);
        $this->db->or_where('eos.resignation_order_id', $id);
    $this->db->group_end();
    
    return $this->db->get()->row_array();
}
public function get_all_active_employees() {
    $this->db->select('employee_id, subscriber_name');
    $this->db->from('emp1');
    // $this->db->where('status', 'Active'); // Uncomment if you have a status column
    $this->db->order_by('subscriber_name', 'ASC');
    return $this->db->get()->result_array();
}
// In Users1.php
public function debug_eos_print($settlement_id)
{
    if (!$this->session->userdata('logged_in')) { die("Not logged in"); }
    
    $this->load->model('hr_model');
    
    // 1. Test Model Query
    $data = $this->hr_model->get_eos_settlement_print_data($settlement_id);
    
    echo "<pre><h3>Database Result:</h3>";
    print_r($data);
    echo "</pre>";
    
    // 2. Check Calculated Values
    echo "<h3>Calculations Check:</h3>";
    
    if(empty($data)) {
        die("<h1 style='color:red'>Error: No data found for ID $settlement_id</h1>");
    }

    $join = $data['joining_date'] ?? null;
    $last = $data['date_of_the_last_working'] ?? null;
    
    echo "Joining Date: " . ($join ? $join : "NULL") . "<br>";
    echo "Last Working Date: " . ($last ? $last : "NULL") . "<br>";
    
    if ($join && $last) {
        try {
            $d1 = new DateTime($join);
            $d2 = new DateTime($last);
            $diff = $d1->diff($d2);
            echo "Diff: " . $diff->y . " years<br>";
        } catch (Exception $e) {
            echo "Date Error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Cannot calculate duration (missing dates)<br>";
    }
    
    exit;
}
public function get_datatables_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor = false, $filter_my_requests = false, $filter_pending_ceo = false)
{
    // Pass the new $filter_pending_ceo to the query builder
    $this->_get_datatables_query_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor, $filter_my_requests, $filter_pending_ceo);
    
    if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        
    return $this->db->get()->result();
}

public function count_filtered_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor = false, $filter_my_requests = false, $filter_pending_ceo = false)
{
    $this->_get_datatables_query_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor, $filter_my_requests, $filter_pending_ceo);
    return $this->db->get()->num_rows();
}
public function count_all_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor = false, $filter_my_requests = false, $filter_pending_ceo = false)
{
    $this->db->from('orders_emp');
    
    // --- 1. CEO Filter Logic (Highest Priority) ---
    if ($filter_pending_ceo) {
        $this->db->group_start();
            $this->db->where('orders_emp.status', '0'); // Pending
            $this->db->or_where('orders_emp.status', '1'); // Processing
        $this->db->group_end();
        $this->db->where('orders_emp.responsible_employee', '1001');
        
        // HR Permissions still apply? Usually CEO filter implies HR access, 
        // but if you want to be safe, you can remove permission checks here 
        // OR ensure only HR can trigger this flag in the first place.
    }
    // --- 2. My Requests Logic ---
    elseif ($filter_my_requests) {
        $this->db->where('orders_emp.emp_id', $logged_in_user_id);
    } 
    // --- 3. Role-based Permissions ---
    else {
        if ($is_hr_user) {
            // HR sees all
        } elseif ($is_abha_supervisor) {
            $this->db->join('emp1', 'emp1.employee_id = orders_emp.emp_id', 'left');
            $this->db->group_start();
                $this->db->where('orders_emp.emp_id', $logged_in_user_id);
                $this->db->or_where('emp1.location', 'أبها');
            $this->db->group_end();
        } else {
            // Regular user
            $this->db->where('orders_emp.emp_id', $logged_in_user_id);
        }
    }
    
    return $this->db->count_all_results();
}
public function count_my_requests($logged_in_user_id)
{
    $this->db->from('orders_emp');
    $this->db->where('orders_emp.emp_id', $logged_in_user_id);
    return $this->db->count_all_results();
}
// In hr_model.php
public function get_employee_names_bulk($employee_ids)
{
    if (empty($employee_ids)) {
        return [];
    }

    $this->db->select('employee_id, subscriber_name');
    $this->db->from('emp1');
    $this->db->where_in('employee_id', $employee_ids);
    $query = $this->db->get();

    $name_map = [];
    foreach ($query->result() as $row) {
        $name_map[$row->employee_id] = $row->subscriber_name;
    }
    return $name_map;
}

/**
 * Processes a rejection action from a manager.
 * Marks the current step as rejected and updates the main request.
 * @param int $order_id
 * @param string $approver_id
 * @param string $reason
 * @return bool
 */
public function process_rejection($order_id, $approver_id, $reason)
{
    $this->db->trans_start();
    
    // 1. Mark the current approval step as 'rejected'
    $this->db->where('order_id', $order_id);
    $this->db->where('approver_id', $approver_id);
    $this->db->where('status', 'pending');
    $this->db->update('approval_workflow', ['status' => 'rejected', 'action_date' => date('Y-m-d H:i:s'), 'rejection_reason' => $reason]);
    
    if ($this->db->affected_rows() === 0) {
        $this->db->trans_complete();
        return false;
    }
    
    // 2. Mark any subsequent approval steps as 'skipped' (optional but good practice)
    $this->db->where('order_id', $order_id);
    $this->db->where('status', 'pending');
    $this->db->update('approval_workflow', ['status' => 'skipped']);
    
    // 3. Update the main request status to 'Rejected'
    $this->db->where('id', $order_id);
    $this->db->update('orders_emp', ['status' => '3', 'reason_for_rejection' => $reason]); // '3' = Rejected

    $this->db->trans_complete();
    return $this->db->trans_status();
}




public function get_leave_types($gender = null) {
    $this->db->select('slug, name_ar, default_balance, is_gender_specific');
    $this->db->from('leave_types');
    $this->db->where('is_active', TRUE);

    if ($gender) {
        $this->db->group_start();
        $this->db->where('is_gender_specific IS NULL', null, false);
        $this->db->or_where('is_gender_specific', $gender);
        $this->db->group_end();
    }
    
    $query = $this->db->get();
    return $query->result_array();
}
    public function get_request_details($request_id)
{
    // You can join with the emp1 table here if you need more employee details
    $this->db->select('orders_emp.*, emp1.subscriber_name, emp1.company_name');
    $this->db->from('orders_emp');
    $this->db->join('emp1', 'emp1.employee_id = orders_emp.emp_id', 'left');
    $this->db->where('orders_emp.id', $request_id);
    
    $query = $this->db->get();
    return $query->row_array();
}
   
 
public function get_employee_company_bulk($employee_ids)
{
    if (empty($employee_ids)) {
        return [];
    }

    $this->db->select('employee_id, company_name');
    $this->db->from('emp1');
    $this->db->where_in('employee_id', $employee_ids);
    $query = $this->db->get();

    $company_map = [];
    foreach ($query->result() as $row) {
        $company_map[$row->employee_id] = $row->company_name;
    }
    return $company_map;
}

public function add_leave_request($leaveData, $file_path)
{
    $leave_type = $leaveData['vac_main_type'];
    $start = new DateTime($leaveData['vac_start']);
    $end = new DateTime($leaveData['vac_end']);
    $days_requested = 0;
    $holidays_query = $this->db->select('holiday_date')
                               ->from('public_holidays')
                               ->where('holiday_date >=', $start_str)
                               ->where('holiday_date <=', $end_str)
                               ->get();
    $public_holidays = array_column($holidays_query->result_array(), 'holiday_date');

    // --- Calculation Logic ---
    $start = new DateTime($start_str);
    $end = new DateTime($end_str);
    $days_requested = 0;
    // --- NEW LOGIC: Calculate days based on leave type ---
    if ($leave_type === 'maternity') {
        $days_requested = $end->diff($start)->days + 1;
    } else {
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($start, $interval, $end);
        foreach ($date_range as $date) {
            $current_date_str = $date->format('Y-m-d');
            $day_of_week = (int)$date->format('N');
            
            // --- MODIFIED: Skip weekends AND public holidays ---
            if ($day_of_week < 5 && !in_array($current_date_str, $public_holidays)) {
                $days_requested++;
            }
        }
    }
    // --- END OF NEW LOGIC ---

   if ($days_requested <= 0) {
        return ['status' => 'error', 'message' => 'الفترة المحددة لا تحتوي على أيام عمل.'];
    }

    $balances = $this->get_employee_balances($leaveData['emp_id']);
    $current_balance = $balances[$leave_type]['remaining'] ?? 0;
    
    if ($days_requested > $current_balance) {
        return ['status' => 'error', 'message' => 'رصيد الإجازات غير كافٍ.'];
    }

    // 3. Sick Leave Tier Validation (if applicable)
    if ($leave_type === 'sick') {
        $consumed_sick_days = $balances['sick']['consumed'] ?? 0;
        if (($consumed_sick_days + $days_requested) > 90) {
             return ['status' => 'error', 'message' => 'تجاوزت الحد الأقصى لرصيد الإجازات المرضية (90 يوم).'];
        }
    }

    // 4. Prepare and insert data
    $orderData = [
        'emp_id'           => $leaveData['emp_id'],
        'emp_name'         => $leaveData['emp_name'],
        'order_name'       => 'إجازة',
        'status'           => '0',
        'type'             => 5,
        'date'             => date('Y-m-d'),
        'time'             => date('H:i:s'),
        'vac_main_type'    => $leave_type,
        'vac_start'        => $leaveData['vac_start'],
        'vac_end'          => $leaveData['vac_end'],
        'vac_days_count'   => $days_requested,
        'vac_reason'       => $leaveData['vac_reason'],
        'file'             => $file_path
    ];
    if ($this->db->insert('orders_emp', $orderData)) {
        $order_id = $this->db->insert_id();
        $this->create_approval_workflow($order_id, $leaveData['emp_id'], 5);
        return ['status' => 'success', 'message' => 'تم إرسال طلب الإجازة بنجاح.'];
    }
    
    return ['status' => 'error', 'message' => 'حدث خطأ في قاعدة البيانات.'];
}

// In hr_model.php
public function get_all_holidays_as_dates()
{
    $query = $this->db->select('holiday_date')->get('public_holidays');
    return array_column($query->result_array(), 'holiday_date');
}
public function get_holidays_for_month($year, $month)
{
    $start_date = "$year-$month-01";
    $end_date = date("Y-m-t", strtotime($start_date));

    $this->db->select('holiday_date, holiday_name');
    $this->db->from('public_holidays');
    $this->db->where('holiday_date >=', $start_date);
    $this->db->where('holiday_date <=', $end_date);
    $query = $this->db->get();

    $holidays = [];
    foreach ($query->result() as $row) {
        $holidays[$row->holiday_date] = $row->holiday_name;
    }
    return $holidays;
}

// In hr_model.php
// REPLACE the old get_daily_attendance_log function with this new one.

// In hr_model.php
// REPLACE the old get_daily_attendance_log function with this new one.

// Add this new function to fetch the summary table
// --- START COPYING HERE ---

public function get_attendance_summary_data($employee_id, $sheet_id)
{
    // 1. Sanitize inputs to remove hidden spaces/tabs
    $clean_emp_id   = trim((string)$employee_id);
    $clean_sheet_id = trim((string)$sheet_id);

    // 2. Query - We use * to be safe, selecting from specific database/table
    // Make sure 'orders' is your actual database name
    $sql = "SELECT * FROM `orders`.`attendance_summary` 
            WHERE `emp_id` = ? AND `id_sheet` = ? LIMIT 1";

    $query = $this->db->query($sql, array($clean_emp_id, $clean_sheet_id));

    if ($query->num_rows() > 0) {
        return $query->row_array();
    }
    
    // Return FALSE so the view knows data is missing
    return false;
}
// --- Add this to hr_model.php ---

public function get_daily_attendance_log($employee_id, $sheet_id)
{
    // 1. Get Sheet
    $sheet = $this->db->get_where('salary_sheet', ['id' => $sheet_id])->row_array();
    if (!$sheet) {
        return ['daily_log' => [], 'summary' => $this->get_attendance_summary_data($employee_id, $sheet_id)];
    }
    
    $start_date = $sheet['start_date'];
    $end_date   = $sheet['end_date'];

    // 2. Fetch Summary (Official Totals)
    $official_summary = $this->get_attendance_summary_data($employee_id, $sheet_id);

    // 3. Fetch Raw Logs
    $sql_att = "SELECT DATE(punch_time) as log_date, MIN(punch_time) as check_in_dt, MAX(punch_time) as check_out_dt 
                FROM `orders`.`attendance_logs` 
                WHERE emp_code = ? AND DATE(punch_time) BETWEEN ? AND ? 
                GROUP BY DATE(punch_time)";
    $attendance_map = [];
    $att_data = $this->db->query($sql_att, [$employee_id, $start_date, $end_date])->result_array();
    foreach($att_data as $row) $attendance_map[$row['log_date']] = $row;

    // 3.5 Fetch Saturday Assignments
    $sat_assignments = [];
    $sql_sat = "SELECT saturday_date FROM `orders`.`saturday_work_assignments` 
                WHERE employee_id = ? AND saturday_date BETWEEN ? AND ?";
    $sat_data = $this->db->query($sql_sat, [$employee_id, $start_date, $end_date])->result_array();
    foreach($sat_data as $s) $sat_assignments[] = $s['saturday_date'];

    // 4. Fetch Vacations
    $orders_map = [];
    $sql_orders = "SELECT * FROM orders_emp WHERE emp_id = ? AND status = 2 AND ((vac_start <= ? AND vac_end >= ?) OR (mission_date BETWEEN ? AND ?))";
    $orders = $this->db->query($sql_orders, [$employee_id, $end_date, $start_date, $start_date, $end_date])->result_array();
    foreach ($orders as $order) {
        if (!empty($order['vac_start'])) {
            $period = new DatePeriod(new DateTime($order['vac_start']), new DateInterval('P1D'), (new DateTime($order['vac_end']))->modify('+1 day'));
            foreach ($period as $d) $orders_map[$d->format('Y-m-d')] = ['type' => 'vacation', 'desc' => $order['vac_type']];
        }
        if (!empty($order['mission_date'])) {
            $orders_map[$order['mission_date']] = ['type' => 'mission', 'desc' => $order['mission_type']];
        }
    }

    // 5. Fetch Holiday Status
    $ny_query = $this->db->select('holiday')->from('orders.new_year_holiday')->where('emp_id', $employee_id)->get();
    $is_ny_holiday = ($ny_query->num_rows() > 0 && $ny_query->row()->holiday == 1);

    // 6. Build Log
    $final_log = [];
    $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));

    foreach ($period as $dt) {
        $date_str = $dt->format('Y-m-d');
        $day_en   = $dt->format('l');
        $is_jan_1 = ($dt->format('m-d') == '01-01');
        
        $row = [
            'date' => $date_str, 
            'day_name' => $this->_get_day_ar($day_en), 
            'check_in' => '—', 'check_out' => '—', 
            'status' => '', 'violation_details' => [],
            'late_minutes' => 0, 'early_minutes' => 0, 
            'is_absent' => false, 'is_single' => false
        ];

        // A. Holiday
        if ($is_jan_1 && $is_ny_holiday) {
            $row['status'] = 'عطلة رسمية';
        }
        // B. Orders
        elseif (isset($orders_map[$date_str])) {
            $row['status'] = $orders_map[$date_str]['type'] == 'vacation' ? 'إجازة' : 'مهمة عمل';
            if(isset($attendance_map[$date_str])) {
                $row['check_in'] = date('H:i', strtotime($attendance_map[$date_str]['check_in_dt']));
                $row['check_out'] = date('H:i', strtotime($attendance_map[$date_str]['check_out_dt']));
            }
        } 
        // C. Attendance Present
        elseif (isset($attendance_map[$date_str])) {
            $att = $attendance_map[$date_str];
            $in_ts = strtotime($att['check_in_dt']);
            $out_ts = strtotime($att['check_out_dt']);

            $row['check_in'] = date('H:i', $in_ts);
            $row['check_out'] = date('H:i', $out_ts);
            $row['status'] = 'حاضر';
            
            // Calculate Duration in Seconds
            $duration_sec = $out_ts - $in_ts;

            // --- CALCULATION LOGIC ---
            $is_assigned_saturday = in_array($date_str, $sat_assignments);

            if ($is_assigned_saturday) {
                $row['status'] = 'عمل يوم السبت (إضافي)';
            } 
            else {
                // 1. Single Punch Logic (Improved)
                // If timestamps are same OR duration is less than 60 seconds -> Single Punch
                if ($in_ts == $out_ts || $duration_sec < 60) {
                    $row['violation_details'][] = "بصمة منفردة";
                    $row['is_single'] = true;
                    $row['status'] = 'بصمة منفردة'; // Set status explicitly
                } 
                else {
                    // 2. Late Calculation (After 11:00)
                    $flex_limit = strtotime("$date_str 11:00:00");
                    $shift_len = 8 * 3600; 

                    if ($in_ts > $flex_limit) {
                        $late_sec = $in_ts - $flex_limit;
                        $row['late_minutes'] = floor($late_sec / 60);
                        if($row['late_minutes'] > 0) {
                            $row['violation_details'][] = "تأخير {$row['late_minutes']} د";
                        }
                        $target_out = $flex_limit + $shift_len;
                    } else {
                        $target_out = $in_ts + $shift_len;
                    }

                    // 3. Early Calculation
                    if ($out_ts < $target_out) {
                        $early_sec = $target_out - $out_ts;
                        $row['early_minutes'] = floor($early_sec / 60);
                        if($row['early_minutes'] > 0) {
                            $row['violation_details'][] = "مبكر {$row['early_minutes']} د";
                        }
                    }
                }
            } 
        } 
        // D. Absence / Weekend
        else {
             if ($day_en != 'Friday' && $day_en != 'Saturday') {
                 $row['status'] = 'غياب';
                 $row['is_absent'] = true;
             } else {
                 $row['status'] = 'عطلة نهاية الأسبوع';
             }
        }
        $final_log[] = $row;
    }

    return ['daily_log' => $final_log, 'summary' => $official_summary];
}
// Helper for Arabic Days
private function _get_day_ar($day) {
    $days = [
        'Saturday' => 'السبت', 'Sunday' => 'الأحد', 'Monday' => 'الاثنين',
        'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة'
    ];
    return $days[$day] ?? $day;
}

// --- END COPYING ---
// In hr_model.php

// Add this new function anywhere inside your hr_model class
public function get_all_employee_balances_for_hr() {
    $current_year = date('Y');
    $employees = $this->get_all_employees();
    $all_balances = [];
    $this->db->select('employee_id, leave_type_slug, remaining_balance');
    $this->db->from('employee_leave_balances');
    $this->db->where('year', $current_year);
    $query = $this->db->get();
    
    $all_balances = [];
    foreach ($query->result() as $row) {
        $all_balances[$row->employee_id][$row->leave_type_slug] = [
            'remaining' => (float)$row->remaining_balance
        ];
    }
    return $all_balances;
}
public function get_all_employee_balances()
{
    // ✅ **FIX:** Removed the non-existent 'elb.id' from the SELECT statement.
    $this->db->select("
        elb.employee_id,
        emp.subscriber_name,
        lt.name_ar AS leave_type_name,
        elb.leave_type_slug,
        elb.balance_allotted,
        elb.balance_consumed,
        elb.remaining_balance,
        elb.year
    ");
    $this->db->from('employee_leave_balances AS elb');
    $this->db->join('emp1 AS emp', 'emp.employee_id = elb.employee_id', 'left');
    $this->db->join('leave_types AS lt', 'lt.slug = elb.leave_type_slug', 'left');
    $this->db->order_by('emp.subscriber_name', 'ASC');
    $this->db->order_by('elb.year', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
}
/**
 * NEW: Updates a single leave balance record and recalculates the remaining balance.
 * @param int $balance_id The primary key (id) of the record in employee_leave_balances.
 * @param array $data The data to update (e.g., ['balance_allotted' => 35, 'balance_consumed' => 5]).
 * @return bool True on success, false on failure.
 */
public function update_balance_record($keys, $data)
{
    // Ensure data is numeric
    $allotted = (float)($data['balance_allotted'] ?? 0);
    $consumed = (float)($data['balance_consumed'] ?? 0);

    // Prepare the update array
    $update_data = [
        'balance_allotted' => $allotted,
        'balance_consumed' => $consumed,
        'remaining_balance' => $allotted - $consumed
    ];

    // ✅ **FIX:** Use the composite key in the WHERE clause instead of a single ID.
    $this->db->where('employee_id', $keys['employee_id']);
    $this->db->where('leave_type_slug', $keys['leave_type_slug']);
   // $this->db->where('year', $keys['year']);
    
    return $this->db->update('employee_leave_balances', $update_data);
}
public function get_all_employees($include_deleted = false) {
    // OLD LINE: $this->db->select('employee_id as username, subscriber_name as name, status');
    
    // NEW LINE (Add joining_date):
    $this->db->select('employee_id as username, subscriber_name as name, status, joining_date'); // <--- Added joining_date
    
    $this->db->from('emp1');
    if (!$include_deleted) {
        $this->db->where('status !=', 'deleted');
    }
    $this->db->order_by('subscriber_name', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
}
public function get_existing_settlement($employee_id)
{
    return $this->db->get_where('end_of_service_settlements', ['employee_id' => $employee_id])->row();
}

public function delete_existing_settlement($employee_id)
{
    $settlement = $this->get_existing_settlement($employee_id);
    if ($settlement) {
        $settlement_id = $settlement->id;

        $this->db->trans_start();
        
        // Delete from approval_workflow first
        $this->db->where('order_type', 8)->where('order_id', $settlement_id)->delete('approval_workflow');

        // Delete from the main settlements table
        $this->db->where('id', $settlement_id)->delete('end_of_service_settlements');
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    return true; // No settlement existed, so it's a success
}

    /**
     * NEW: Fetches basic info for a single employee.
     * Used to get the name of the employee an HR user is submitting for.
     */
    public function get_employee_info($employee_id) {
        $this->db->select('username, name');
        $this->db->from('users');
        $this->db->where('username', $employee_id);
        $query = $this->db->get();
        return $query->row_array();
    }


public function upsert_employee_balance($data)
{
    // Check if a record already exists for this employee, leave type, and year
    $this->db->where('employee_id', $data['employee_id']);
    $this->db->where('leave_type_slug', $data['leave_type_slug']);
  //  $this->db->where('year', $data['year']);
    $existing = $this->db->get('employee_leave_balances')->row();

    if ($existing) {
        // Update the existing record
        $update_data = ['balance_allotted' => $data['balance_allotted']];
        // Recalculate remaining balance
        $update_data['remaining_balance'] = $data['balance_allotted'] - $existing->balance_consumed;
        
        $this->db->where('id', $existing->id);
        return $this->db->update('employee_leave_balances', $update_data);
    } else {
        // Insert a new record
        $insert_data = $data;
        $insert_data['balance_consumed'] = 0;
        $insert_data['remaining_balance'] = $data['balance_allotted'];
        return $this->db->insert('employee_leave_balances', $insert_data);
    }
}
public function get_payroll_data_maps($employee_ids, $sheet_id, $sheet_start_date, $sheet_end_date)
{
    if (empty($employee_ids)) {
        return [];
    }

    $data_map = [];

    // Load all required data
    $data_map['attendance'] = $this->get_attendance_summary_for_employees($employee_ids, $sheet_id);
    $data_map['discounts'] = $this->get_discounts_for_employees($employee_ids, $sheet_id, $sheet_start_date, $sheet_end_date);
    $data_map['half_day'] = $this->get_half_day_vacations_for_employees($employee_ids, $sheet_start_date, $sheet_end_date);
    
    $prev_sheet_id = $sheet_id - 1;
    $data_map['prev_attendance'] = $this->get_attendance_summary_for_employees($employee_ids, $prev_sheet_id);
    
    $data_map['reparations'] = $this->get_reparations_for_employees($employee_ids, $sheet_id, $sheet_start_date, $sheet_end_date);
    $data_map['new_employees'] = $this->get_new_employee_data_for_employees($employee_ids);
    $data_map['insurance'] = $this->get_insurance_discounts_for_employees($employee_ids);
    
  $data_map['stopped'] = $this->get_stopped_salaries_for_employees($employee_ids, $sheet_id);
    $data_map['exemptions'] = $this->get_exemptions_for_employees($employee_ids);
    
    // For notes - create empty array if method doesn't exist
    $data_map['notes'] = [];
    
    // Unpaid Leave
    $sheet_end_dt_obj = new DateTime($sheet_end_date);
    $calendar_month_start = $sheet_end_dt_obj->format('Y-m-01');
    $calendar_month_end = $sheet_end_dt_obj->format('Y-m-t');
    $data_map['unpaid_leave'] = $this->get_unpaid_leave_days_for_employees($employee_ids, $calendar_month_start, $calendar_month_end);
    
    // Working Hours Rules
    $this->db->where_in('emp_id', $employee_ids);
    $rules_query = $this->db->get('work_restrictions')->result();
    foreach($rules_query as $r) { 
        $data_map['rules'][$r->emp_id] = $r; 
    }

    return $data_map;
}
/**
 * NEW: Inserts or updates a batch of balance records from an Excel file.
 */
public function upsert_batch_balances($data_batch)
{
    $this->db->trans_start();
    foreach ($data_batch as $data) {
        $this->upsert_employee_balance($data);
    }
    $this->db->trans_complete();
    return $this->db->trans_status();
}


    public function get_employee_gender($employee_id) {
    $this->db->select('gender');
    $this->db->from('emp1'); // Assuming your employee table is emp1
    $this->db->where('employee_id', $employee_id);
    $query = $this->db->get();
    $result = $query->row_array();
    // Standardize the output, assuming 'ذكر' is male and 'أنثى' is female
    if (isset($result['gender'])) {
        if (trim($result['gender']) === 'ذكر') return 'male';
        if (trim($result['gender']) === 'أنثى') return 'female';
    }
    return null;
}

    public function get_request_by_id($request_id)
{
    return $this->db->get_where('orders_emp', ['id' => $request_id])->row_array();
}

// In hr_model.php -> Replace the old function with this one

// In hr_model.php -> Replace the old function with this one

// In application/models/hr_model.php

public function approve_leave_request($requestId)
{
    $this->db->trans_start();

    // 1. Get Request Details
    $this->db->select('orders_emp.*, emp1.total_salary, emp1.subscriber_name');
    $this->db->from('orders_emp');
    $this->db->join('emp1', 'emp1.employee_id = orders_emp.emp_id', 'left');
    $this->db->where('orders_emp.id', $requestId);
    $request = $this->db->get()->row();

    if ($request) {
        $employee_id = $request->emp_id;
        $leave_type = $request->vac_main_type; // e.g., 'sick', 'annual'
        $current_year = date('Y', strtotime($request->vac_start));
        $days_count = (float)$request->vac_days_count;

        // --- A. Update Leave Balance (For ALL leave types) ---
        if ($employee_id && $leave_type && $days_count > 0) {
            $this->db->where('employee_id', $employee_id);
            $this->db->where('leave_type_slug', $leave_type);
         //   $this->db->where('year', $current_year);
            
            $this->db->set('balance_consumed', 'balance_consumed + ' . $days_count, FALSE);
            $this->db->set('remaining_balance', 'remaining_balance - ' . $days_count, FALSE);
            $this->db->update('employee_leave_balances');
        }

        // --- B. Retroactive Compensation Logic (ONLY FOR SICK LEAVE) ---
        // We added the condition: && $leave_type === 'sick'
        if ($leave_type === 'sicks') {
            
            // 1. Get the CURRENT active Salary Sheet
            $current_sheet = $this->db->order_by('id', 'DESC')->limit(1)->get('salary_sheet')->row();

            if ($current_sheet) {
                $sheet_start_date = $current_sheet->start_date;
                $leave_end_date = $request->vac_end;

                // CHECK: Is the leave completely in the past AND is it Sick Leave?
                if ($leave_end_date < $sheet_start_date) {
                    
                    // Calculate Compensation Amount
                    $total_salary = (float)$request->total_salary;
                    $daily_rate = ($total_salary > 0) ? ($total_salary / 30) : 0;
                    $compensation_amount = round($daily_rate * $days_count, 2);

                    if ($compensation_amount > 0) {
                        // Prepare Data for Reparations Table
                        $reparation_data = [
                            'type'            => 'تعويض إجازة مرضية بأثر رجعي', // Specific Label
                            'emp_id'          => $employee_id,
                            'emp_name'        => $request->subscriber_name,
                            'amount'          => $compensation_amount,
                            'username'        => $this->session->userdata('username'),
                            'name'            => $this->session->userdata('name'),
                            'date'            => date('Y-m-d'),
                            'time'            => date('H:i:s'),
                            'sheet_id'        => $current_sheet->id,
                            'notes'           => "عن طلب إجازة مرضية رقم #$requestId (من $request->vac_start إلى $request->vac_end)",
                            'reparation_date' => date('Y-m-d')
                        ];

                        $this->db->insert('reparations', $reparation_data);
                        
                        log_message('info', "Retroactive Sick Pay added for Emp $employee_id: $compensation_amount SAR");
                    }
                }
            }
        }
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}
// In application/models/hr_model.php
// In hr_model.php, add this new function

public function add_manual_punch($data)
{
    // Inserts the new record into the main attendance table
    return $this->db->insert('attendance_logs', $data);
}
public function process_hr_override_approval($order_id, $approver_id)
{
    $this->db->trans_start();

    // 1. Get the request details to find its type
    $request = $this->db->get_where('orders_emp', ['id' => $order_id])->row_array();
    if (!$request) {
        $this->db->trans_complete();
        return ['success' => false, 'message' => 'Request not found.'];
    }
    $request_type = (int)$request['type'];

    // 2. Update the main request status to 'Approved'
    // We use the rejection reason field to store an audit note
    $this->db->where('id', $order_id)
             ->update('orders_emp', [
                'status' => '2', // 2 = Approved
                'responsible_employee' => null,
                'reason_for_rejection' => 'Approved directly by HR user: ' . $approver_id
             ]);

    // 3. Mark all workflow steps as "skipped" since we are bypassing them
    $this->db->where('order_id', $order_id)
             ->where('order_type', $request_type)
             ->update('approval_workflow', ['status' => 'skipped']);

    // 4. Trigger the specific final action for the request type
    // 4. Trigger the specific final action for the request type
    switch ($request_type) {
        case 1: // Resignation 
            $resignation_result = $this->execute_final_resignation_approval($order_id, $approver_id);
            if ($resignation_result !== true) {
                // If it fails, safely rollback the database immediately and show the exact error
                $this->db->trans_rollback();
                return ['success' => false, 'message' => $resignation_result];
            }
            break;
            // ==================================================
        case 5: // Leave Request
            $this->approve_leave_request($order_id);
            break;
        case 2: // Fingerprint Correction
            $this->approve_fingerprint_correction($order_id);
            break;
        case 3: // Overtime Request
            // *** ADDED: Trigger the OT calculation upon HR override approval ***
            $this->calculate_and_record_ot_pay($order_id); 
            break;
        case 10: // Work Mission (مهمة العمل)
            // No action needed for final approval
            break;
        // Add other request types here if they have special actions upon final approval.
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        // اجلب تفاصيل الخطأ الفعلي من قاعدة البيانات
        $db_error = $this->db->error(); 
        return ['success' => false, 'message' => 'خطأ SQL: ' . $db_error['message']];
    } else {
        return ['success' => true, 'new_status' => '2'];
    }
}
public function get_employee_balances($employee_id) {
    $current_year = date('Y');
    $this->db->select('leave_type_slug, balance_allotted, balance_consumed, remaining_balance');
    $this->db->from('employee_leave_balances');
    $this->db->where('employee_id', $employee_id);
    $this->db->where('year', $current_year);
    $query = $this->db->get();
    
    $balances = [];
    foreach ($query->result_array() as $row) {
        $balances[$row['leave_type_slug']] = [
            'allotted' => (float)$row['balance_allotted'],
            'consumed' => (float)$row['balance_consumed'],
            // Corrected (int) to (float) here as well
            'remaining' => (float)$row['remaining_balance']
        ];
    }
    return $balances;
}


public function get_all_requests_with_balances()
{
    $this->db->select("
        orders_emp.*, 
        (employee_leave_balances.balance_allotted - employee_leave_balances.balance_consumed) AS remaining_balance
    ");
    $this->db->from('orders_emp');
    
    // LEFT JOIN to get the balance ONLY for leave requests
    $this->db->join(
        'employee_leave_balances',
        'employee_leave_balances.employee_id = orders_emp.emp_id 
         AND employee_leave_balances.leave_type_slug = orders_emp.vac_main_type 
         AND employee_leave_balances.year = YEAR(orders_emp.vac_start)',
        'left'
    );
    
    $this->db->order_by('orders_emp.id', 'DESC');
    $query = $this->db->get();
    
    return $query->result_array();
}
// In hr_model.php

/**
 * Fetches all leave balances for a specific employee.
 * @param string $employee_id The employee's ID.
 * @return array A list of balance records.
 */
public function get_leave_balances_by_employee($employee_id) {
    $this->db->select("elb.*, lt.name_ar AS leave_type_name");
    $this->db->from('employee_leave_balances AS elb');
    $this->db->join('leave_types AS lt', 'lt.slug = elb.leave_type_slug', 'left');
    $this->db->where('elb.employee_id', $employee_id);
    $this->db->where('elb.year', date('Y')); // Fetch for the current year
    $this->db->order_by('lt.name_ar', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}
// In hr_model.php

/**
 * A single, efficient function to get all profile data from the emp1 table.
 * @param string $employee_id
 * @return array
 */
public function get_employee_profile_details($employee_id) {
    $this->db->from('emp1');
    $this->db->where('employee_id', $employee_id);
    $query = $this->db->get();
    return $query->row_array();
}

/**
 * Fetches contract details for an employee.
 * @param string $employee_id
 * @return array
 */

// In hr_model.php
// In hr_model.php

public function check_employee_id_exists($employee_id)
{
    $this->db->where('employee_id', $employee_id);
    $this->db->from('emp1');
    return $this->db->count_all_results() > 0;
}
// In hr_model.php

/**
 * Fetches all organizational structures with employee names.
 * @return array
 */
public function get_all_org_structures()
{
    $this->db->select('
        os.id,
        os.n1, e1.subscriber_name as n1_name,
        os.n2, e2.subscriber_name as n2_name,
        os.n3, e3.subscriber_name as n3_name,
        os.n4, e4.subscriber_name as n4_name,
        os.n5, e5.subscriber_name as n5_name,
        os.n6, e6.subscriber_name as n6_name,
        os.n7, e7.subscriber_name as n7_name
    ');
    $this->db->from('organizational_structure as os');
    $this->db->join('emp1 as e1', 'os.n1 = e1.employee_id', 'left');
    $this->db->join('emp1 as e2', 'os.n2 = e2.employee_id', 'left');
    $this->db->join('emp1 as e3', 'os.n3 = e3.employee_id', 'left');
    $this->db->join('emp1 as e4', 'os.n4 = e4.employee_id', 'left');
    $this->db->join('emp1 as e5', 'os.n5 = e5.employee_id', 'left');
    $this->db->join('emp1 as e6', 'os.n6 = e6.employee_id', 'left');
    $this->db->join('emp1 as e7', 'os.n7 = e7.employee_id', 'left');
    $this->db->order_by('os.id', 'ASC');
    return $this->db->get()->result_array();
}

/**
 * Fetches a single organizational structure row by its primary key.
 * @param int $id
 * @return array|null
 */
public function get_org_structure_by_id($id)
{
    return $this->db->get_where('organizational_structure', ['id' => $id])->row_array();
}

/**
 * Adds a new organizational structure row.
 * @param array $data
 * @return bool
 */
public function add_org_structure($data)
{
    return $this->db->insert('organizational_structure', $data);
}

/**
 * Updates an existing organizational structure row.
 * @param int $id
 * @param array $data
 * @return bool
 */
public function update_org_structure($id, $data)
{
    $this->db->where('id', $id);
    return $this->db->update('organizational_structure', $data);
}

/**
 * Deletes an organizational structure row by its primary key.
 * @param int $id
 * @return bool
 */
public function delete_org_structure_by_id($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('organizational_structure');
}
public function get_employees_for_restrictions_dropdown()
    {
        $this->db->select('employee_id as username, subscriber_name as name');
        $this->db->from('emp1');
        $this->db->where('status !=', 'deleted');
        $this->db->order_by('subscriber_name', 'ASC');
        return $this->db->get()->result_array();
    }
public function get_work_restriction_by_id($id)
    {
        $query = $this->db->get_where('work_restrictions', ['id' => $id]);
        return $query->row_array();
    }

    /**
     * Saves (Inserts or Updates) a work restriction record.
     */
    public function save_work_restriction($id, $data)
    {
        if (empty($id)) {
            // No ID provided, so insert a new record
            return $this->db->insert('work_restrictions', $data);
        } else {
            // ID provided, so update the existing record
            $this->db->where('id', $id);
            return $this->db->update('work_restrictions', $data);
        }
    }

    /**
     * Deletes a work restriction record by its primary ID.
     */
    public function delete_work_restriction($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('work_restrictions');
    }
public function get_all_employees_for_dropdown()
{
    $this->db->select('employee_id, subscriber_name');
    $this->db->from('emp1');
    $this->db->order_by('subscriber_name', 'ASC');
    return $this->db->get()->result_array();
}
// Ensure you also have this function from our previous steps
// In hr_model (7).php
public function add_employee($data)
{
    // Start a database transaction
    $this->db->trans_start();

    // 1. Insert into the main `emp1` table
    $this->db->insert('emp1', $data);
    $emp1_inserted = $this->db->affected_rows() > 0;

    if ($emp1_inserted) {
        
        // --- 2. Add to `new_employees` table ---
        // This runs for ALL new employees
        $new_employee_data = [
            'employee_id'     => $data['employee_id'],
            'subscriber_name' => $data['subscriber_name'],
            'nationality'     => $data['nationality'] ?? null,
            'join_date'       => $data['joining_date'] ?? null
        ];
        $this->db->insert('new_employees', $new_employee_data);

        
        // --- 3. Add to `insurance_discount` table ---
        // This only runs if nationality is 'سعودي'
        if (isset($data['nationality']) && $data['nationality'] === 'سعودي') {
            $insurance_data = [
                'n1' => $data['employee_id'],
                'n2' => $data['subscriber_name'],
                'n3' => 0.0975 // Your specified default value
            ];
            
            // Check if one already exists, just in case
            $this->db->where('n1', $data['employee_id']);
            $exists = $this->db->get('insurance_discount')->num_rows();
            if ($exists == 0) {
                 $this->db->insert('insurance_discount', $insurance_data);
            }
        }
        
        
        // --- 4. Add to `organizational_structure` table ---
        $new_emp_id = $data['employee_id'];
        $manager_name = $data['manager'] ?? null; // Get manager's NAME from emp1 data

        // <<< START OF FIX >>>
        // We must find the manager's ID from their name
        $manager_id = null;
        if (!empty($manager_name)) {
            $manager_row = $this->db->select('employee_id')
                                   ->from('emp1')
                                   ->where('subscriber_name', $manager_name)
                                   ->limit(1)
                                   ->get()
                                   ->row();
            if ($manager_row) {
                $manager_id = $manager_row->employee_id;
            }
        }
        // <<< END OF FIX >>>

        if (!empty($new_emp_id) && !empty($manager_id)) { // Now we check for $manager_id
            $cols = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7'];
            $base_row = null;
            $manager_level = -1; // 0-based index (0=n1, 6=n7)

            // Find the deepest row where the manager's ID is listed
            $this->db->from('organizational_structure');
            $this->db->group_start();
            foreach ($cols as $col) { $this->db->or_where($col, $manager_id); } // Search by ID
            $this->db->group_end();
            $manager_rows = $this->db->get()->result_array();

            if (!empty($manager_rows)) {
                foreach ($manager_rows as $row) {
                    // Search from n7 up to n1 to find the deepest level
                    for ($i = 6; $i >= 0; $i--) { 
                        if (isset($row[$cols[$i]]) && $row[$cols[$i]] == $manager_id) {
                            if ($i > $manager_level) {
                                $manager_level = $i;
                                $base_row = $row;
                            }
                            break; // Found in this row
                        }
                    }
                }
            }

            // Now, create the new row based on what we found
            if ($base_row) {
                // Case 1: Manager was found. Add employee in the next slot.
                $new_emp_level = $manager_level + 1;
                if ($new_emp_level <= 6) { // If there's space (up to n7)
                    $new_org_row = $base_row;
                    unset($new_org_row['id']); // Remove ID to create a new row
                    $new_org_row[$cols[$new_emp_level]] = $new_emp_id;
                    
                    // Clear levels below the new employee
                    for ($i = $new_emp_level + 1; $i <= 6; $i++) {
                        $new_org_row[$cols[$i]] = null;
                    }
                    $this->db->insert('organizational_structure', $new_org_row);
                }
                // else: Manager is already at n7, can't add employee below them.
            
            } else {
                // Case 2: Manager was NOT found. Create a new chain.
                // This assumes the manager is n1 and the new employee is n2
                $new_org_row = [
                    'n1' => $manager_id, // Manager's ID
                    'n2' => $new_emp_id, // New Employee's ID
                    'n3' => null, 'n4' => null, 'n5' => null, 'n6' => null, 'n7' => null
                ];
                $this->db->insert('organizational_structure', $new_org_row);
            }
        }
        // --- End of Org Structure insert ---
        
    } // End of `if ($emp1_inserted)`

    // Complete the transaction
    $this->db->trans_complete();
    
    // Return the status of the transaction (true if all inserts worked)
    return $this->db->trans_status();
}
// In Users1.php

public function delete_employee()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $id = $this->input->post('id');

    $this->load->model('hr_model');
    if ($this->hr_model->delete_employee_by_id($id)) {
        echo json_encode(['status' => 'success', 'message' => 'تم حذف الموظف بنجاح.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل حذف الموظف.']);
    }
}

// In hr_model.php

public function delete_employee_by_id($id)
{
    $this->db->where('id', $id);
    $this->db->update('emp1', ['status' => 'deleted']);
    return $this->db->affected_rows() > 0;
}
public function get_all_holidays()
{
    $this->db->order_by('start_date', 'DESC'); // ✅ Corrected to use the new column name
    return $this->db->get('public_holidays')->result_array();
}

public function add_new_holiday($data)
{
    // The UNIQUE KEY on holiday_date will prevent duplicates
    return $this->db->insert('public_holidays', $data);
}

public function delete_holiday_by_id($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('public_holidays');
}
public function get_employee_contract_details($employee_id) {
    // This is placeholder data. You should replace this query
    // with one that reads from your actual contracts table.
    $this->db->select("
        'active' AS status,
        'سنتان' AS contract_period,
        'سنة و 3 أشهر' AS remaining_renewal_period,
        contract_start_date AS contract_start,
        contract_end_date AS contract_end
    ");
    $this->db->from('emp1'); // Assuming contract dates are in emp1 for now
    $this->db->where('employee_id', $employee_id);
    $query = $this->db->get();
    return $query->row_array();
}


/**
 * Fetches all leave balances for a specific employee for the current year.
 * @param string $employee_id The employee's ID.
 * @return array A list of balance records.
 */


/**
 * Fetches the most recent payroll record for a specific employee.
 * @param string $employee_id The employee's ID.
 * @return array|null The payroll record or null if not found.
 */

/**
 * Fetches the most recent payroll record for a specific employee.
 * @param string $employee_id The employee's ID.
 * @return array|null The payroll record or null if not found.
 */
public function get_last_payroll_for_employee($employee_id) {
    $this->db->from('payroll_process');
    $this->db->where('n1', $employee_id); // Assuming n1 is the employee ID column
    $this->db->order_by('id', 'DESC'); // Order by ID to get the latest entry
    $this->db->limit(1);
    
    $query = $this->db->get();
    return $query->row_array();
}

    function get_leave_balance(){
      
        $sql = "select * from leave_balance;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function get_series_of_approvals(){
      
        $sql = "select * from series_of_approvals;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


// In hr_model.php, add this new function:

public function update_resignation_reason($resignation_order_id, $new_reason)
{
    // Update the reason in the original orders_emp table
    $this->db->where('id', $resignation_order_id);
    $this->db->where('type', 1); // Ensure it's a resignation (type 1)
    return $this->db->update('orders_emp', ['reason_for_resignation' => $new_reason]);
}

    function get_work_restrictions(){
      
        $sql = "select * from work_restrictions ;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    




    public function get_discounts($sheet_id, $emp_id) {
    // تأكد من أن استعلام SQL يتم بشكل صحيح
    $sql = "SELECT * FROM discounts WHERE sheet_id = ? AND emp_id = ?";
    $query = $this->db->query($sql, array($sheet_id, $emp_id));  // تم تمرير المعاملات بشكل صحيح
    return $query->row_array(); // إرجاع أول صف من البيانات
}


public function get_reparations($sheet_id, $emp_id) {
    // تأكد من أن استعلام SQL يتم بشكل صحيح
    $sql = "SELECT * FROM reparations WHERE sheet_id = ? AND emp_id = ?";
    $query = $this->db->query($sql, array($sheet_id, $emp_id));  // تم تمرير المعاملات بشكل صحيح
    return $query->row_array(); // إرجاع أول صف من البيانات
}








     



    // دالة لجلب أول وآخر بصمة لكل موظف من جميع جداول البصمات
    // public function get_attendance_data() {
    //     // استعلام لجلب أول وآخر بصمة لكل يوم من جدول attendance_logs
    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $this->db->group_by('emp_code, punch_date');
    //     $query1 = $this->db->get_compiled_select(); // حفظ الاستعلام في متغير

    //     // استعلامات لدمج بيانات البصمات من الجداول الأخرى
    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs_af4c194960167');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $query2 = $this->db->get_compiled_select(); // حفظ الاستعلام في متغير

    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs_af4c203360515');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $query3 = $this->db->get_compiled_select();

    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs_af4c214560921');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $query4 = $this->db->get_compiled_select();

    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs_af4c223260066');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $query5 = $this->db->get_compiled_select();

    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs_osa7010056122201235');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $query6 = $this->db->get_compiled_select();

    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs_rkq4235000038');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $query7 = $this->db->get_compiled_select();

    //     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    //     $this->db->from('attendance_logs_rkq4241900202');
    //     $this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');
    //     $query8 = $this->db->get_compiled_select();

    //     // دمج الاستعلامات
    //     $final_query = $query1 . " UNION " . $query2 . " UNION " . $query3 . " UNION " . $query4 . " UNION " . $query5 . " UNION " . $query6 . " UNION " . $query7 . " UNION " . $query8;

    //     // تنفيذ الاستعلام النهائي
    //     $query = $this->db->query($final_query);
    //     return $query->result();
    // }

    public function get_attendance_data() {
    // استعلام لجلب أول وآخر بصمة من جدول attendance_logs
    $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query1 = $this->db->get();

     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs_af4c194960167');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query2 = $this->db->get();

    $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs_af4c203360515');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query3 = $this->db->get();

    $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs_af4c214560921');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query4 = $this->db->get();

    $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs_af4c223260066');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query5 = $this->db->get();

     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs_osa7010056122201235');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query6 = $this->db->get();

     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs_rkq4235000038');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query7 = $this->db->get();


     $this->db->select('emp_code, DATE(punch_time) as punch_date, MIN(punch_time) as first_punch, MAX(punch_time) as last_punch');
    $this->db->from('attendance_logs_rkq4241900202');
    //$this->db->where('DATE(punch_time) BETWEEN "2025-07-20" AND "2025-08-20"');  // إزالة الفلتر مؤقتًا
    $this->db->group_by('emp_code, punch_date');
    $query8 = $this->db->get();




    


 

    // دمج البيانات من جميع الجداول
    $data = array_merge(
        $query1->result(),
        $query2->result(),
        $query3->result(),
        $query4->result(),
        $query5->result(),
        $query6->result(),
        $query7->result(),
        $query8->result()
    );

    return $data;
}




      public function max_tran_id(){  

           $this->db->select_max('id');
           $this->db->from('emp');
           $query = $this->db->get();
           return $query->row()->id;
        }

        public function delete_attendance_summary($id_sheet)
{
    $this->db->where('id_sheet', $id_sheet);
    return $this->db->delete('attendance_summary');
}




        

public function export_csv()
{
    $this->load->dbutil();
    $this->load->helper('file');
    $this->load->helper('download');

    $delimiter = ",";
    $newline = "\r\n";
    $filename = "emp.csv";

    // استعلام SQL - يمكنك تعديله حسب حاجتك
    $query = "SELECT * FROM emp";
    $result = $this->db->query($query);

    // تحويل النتيجة إلى CSV
    $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);

    // التحميل بصيغة UTF-8 مع BOM
    force_download($filename, "\xEF\xBB\xBF" . $data);
}







    function add_project(){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $day=date("l");
            $time = date("H:i:s");
           
         
           $data = array(
                 
               
                  'name' => $this->input->post('name'),
                  'mobile' => $this->input->post('mobile'),
                  'date' => $d,
                  'time' => $time
               
                 
              
            );
            return $this->db->insert('emp', $data);
        // } 


}



     function get_extension($id){
      
        $sql = "select * from extension where type=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function get_attribution22233(){
        $id=$this->session->userdata('username');
        $sql = "select * from test3 where username=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

          function get_attribution222334(){
        $id=$this->session->userdata('username');
        $sql = "select * from test3 where username=$id and status=0;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

        function get_attribution22233422225(){
        $id=$this->session->userdata('username');
        $sql = "select * from test3;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

       function get_attribution2223354($id){
    
        $sql = "select * from test3 where n12=$id;";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

       function get_attribution2223341(){
        $id=$this->session->userdata('username');
        $sql = "select * from test3 where username=$id and status=1;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }



     public function users_update_otp($username,$pass){
             
           $data = array(
                 'otp' =>$pass
            );

            $this->db->where('username', $username);
            return $this->db->update('users', $data);
       }

            public function orders_emp_update($id, $debug = false)
{
    $id = (int)$id;
    if ($id <= 0) return false;

    // --- Helper logging ---
    $log = function($msg, $ctx = []) use ($debug) {
        if ($debug) log_message('debug', $msg.' :: '.json_encode($ctx, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    };

    // 1) احضر الطلب (نأخذ type كرقم، و emp_id)
    $order = $this->db->select('id, emp_id, type')
                      ->from('orders_emp')
                      ->where('id', $id)
                      ->get()->row_array();
    if (!$order) { $log('order not found', ['id'=>$id]); return false; }

    $empId    = trim((string)$order['emp_id']);
    $typeCode = (int)$order['type']; // ← مباشر: type رقم 1..7
    $log('order row', $order);

    // 2) number_of_approvals (تحويل آمن لو BLOB/ASCII)
    $series = $this->db
        ->select("CASE
                    WHEN LENGTH(number_of_approvals)=1
                         AND ASCII(number_of_approvals) BETWEEN 48 AND 57
                      THEN ASCII(number_of_approvals)-48   -- '0'..'9'
                    ELSE ASCII(number_of_approvals)        -- 0x01/0x02...
                  END AS approvals", false)
        ->where('code', $typeCode)
        ->get('series_of_approvals')
        ->row_array();

    $numApprovals = isset($series['approvals']) ? (int)$series['approvals'] : 0;
    $log('approvals parsed', ['numApprovals'=>$numApprovals]);

    // 3) تحديد المسؤول
    $responsible = 'hr'; // افتراضي

    if ($numApprovals >= 2 && $empId !== '') {
        // نبحث في n1..n7 عن empId ونختار أعمق موضع
        $cols = ['n1','n2','n3','n4','n5','n6','n7'];

        $this->db->select(implode(',', $cols))
                 ->from('organizational_structure')
                 ->group_start();
        foreach ($cols as $c) { $this->db->or_where($c, $empId); }
        $this->db->group_end();

        $rows = $this->db->get()->result_array();
        $log('org rows matched', ['count'=>count($rows)]);

        $bestIdx = -1;
        $bestRow = null;

        foreach ($rows as $row) {
            foreach ($cols as $i => $c) {
                if (isset($row[$c]) && (string)$row[$c] === $empId) {
                    if ($i > $bestIdx) { $bestIdx = $i; $bestRow = $row; }
                }
            }
        }

        if ($bestRow !== null) {
            if ($bestIdx === 0) {
                $responsible = 'hr'; // الموظف في n1
            } else {
                $prev = trim((string)($bestRow[$cols[$bestIdx - 1]] ?? ''));
                $responsible = ($prev !== '') ? $prev : 'hr';
            }
        } else {
            $responsible = 'hr';
        }
    } else {
        // numApprovals 0 أو 1 أو empId فاضي ⇒ hr
        $responsible = 'hr';
    }

    // 4) حدّث الطلب
    $ok = $this->db->where('id', $id)
                   ->update('orders_emp', ['responsible_employee' => $responsible]);
    if (!$ok) {
        $err = $this->db->error();
        $log('update failed', ['err'=>$err]);
        return false;
    }

    $log('update success', ['id'=>$id, 'responsible'=>$responsible]);
    return true;
}





            public function users_test3($id){

              $pass=1;  
             
           $data = array(
                 'status' =>$pass
            );

            $this->db->where('id', $id);
            return $this->db->update('test3', $data);
       }

          function get_users_otp($username){
      
        $sql = "select * from users where username='$username';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

      


      function get_users_by_sub1118090(){
     
        $sql = "select * from users where type='1';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }




 function get_portfolio_for_editss1($ss1){
       
        $sql = "select * from portfolio where transfer_no='$ss1';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

       function get_portfolio_for_editss111($ss1){
       
        $sql = "select * from emp_data1 where n1='$ss1';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }





public function get_portfolio_selected($id){
          $sql = "select * from portfolio where id='$id';";
      $query=$this->db->query($sql);
        return $query->row_array();
    }

    public function get_test2_selected($id){
          $sql = "select * from test2 where id='$id';";
      $query=$this->db->query($sql);
        return $query->row_array();
    }


function delete_portfolio($id){         
        $this->db->where('id', $id);
        $this->db->delete('portfolio');
        return true;
        }


        function delete_delete($id){         
        $this->db->where('id', $id);
        $this->db->delete('test2');
        return true;
        }






function cuntt_study545451024(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='30';
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

function cuntt_study545451030(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='30';
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }





    function cuntt_study5454510public($id){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid=$id;
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


 function add_evaluation2020($ss2,$ss3,$ss4,$ss5){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
   
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'transfer_no' => $this->input->post('transfer_no'),
                 'successful_number_call' => $this->input->post('successful_number_call1'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'supervisor' =>  $ss4,
                 'emp_name' =>  $ss3,
                 'Job_number' =>  $ss2,
                 'place' =>  $ss5,
                 
                
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }



function cuntt_study5454510(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='23';
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



function cuntt_study5454510251(){
       
 $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d="25";
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_studyrr22(){
       
 $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'evaluation';
        $d="رافض المرسوم";
        $array = array('note55' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


      function cuntt_studyrr33(){
       
 $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'evaluation';
        $d="لا";
        $array = array('note66' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }









   function update_transfer($emp_name,$id){
        date_default_timezone_set('Asia/Riyadh');
        $d1=date('d');
            $data = array(
                 'transfer_no' => $this->input->post('transfer_no'),
                 'user_id' => $this->input->post('user_id'),
                 'username' => $emp_name,
                 'work_location' => $this->input->post('work_location'),
                 'direct_supervisor' =>$this->input->post('direct_supervisor'),
                 'emp_name' =>$this->input->post('emp_name'),
                 'emp_id' =>$this->input->post('emp_id'),
                 'status' =>$this->input->post('status'),
                 'date' => $d1
  
            );

           $this->db->where('id', $id);
         return $this->db->update('portfolio', $data);
        }

        function update_transfer101($id){
        date_default_timezone_set('Asia/Riyadh');
        $d1=date('d');
            $data = array(
                 'n1' => $this->input->post('n1'),
                 'n2' => $this->input->post('n2'),
                 'n3' => $this->input->post('n3'),
                 'n4' => $this->input->post('n4'),
                 'n5' => $this->input->post('n5'),
                 'n6' => $this->input->post('n6'),
                 'n7' => $this->input->post('n7'),
                 'n8' => $this->input->post('n8'),
                 'n9' => $this->input->post('n9') 
            );

           $this->db->where('id', $id);
         return $this->db->update('test2', $data);
        }







function cuntt_study5451024(){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d="30";
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

function cuntt_study5451030(){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d="30";
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



    function cuntt_study5451024_puplic($id){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d=$id;
        $array = array('user_id' =>$id); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

      



       function get_st1(){
       
        $sql = "select * from users;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


     function get_st1admin(){
       
        $sql = "select * from users where id='1';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }



     function get_st124(){
       
        $sql = "select * from users where id='24';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

     function get_st125(){
       
        $sql = "select * from users where id='25';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


    function get_st122(){
       
        $sql = "select * from users where id='22';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    function get_st126(){
       
        $sql = "select * from users where id='26';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


     function get_st127(){
       
        $sql = "select * from users where id='27';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


     function get_st128(){
       
        $sql = "select * from users where id='28';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


     function get_st129(){
       
        $sql = "select * from users where id='29';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }







 function cuntt_study10011111(){

          $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d=$this ->session->userdata('user_id');
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;


        
    }


     function noof_num_3(){

        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'watch';
        $d=date("Y/m/d");
        $userid='24';
         $array = array('day' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;


        
    }





 function cuntt_study545451111(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



     function get_candidate_position11(){
       
        $sql = "select * from candidate_position;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


    



     public function get_items_selected($id){
          $sql = "select * from order_detailes where id='$id';";
      $query=$this->db->query($sql);
        return $query->row_array();
    }


      public function get_user_selected($ss){
          $sql = "select * from users where id='$ss';";
      $query=$this->db->query($sql);
        return $query->row_array();
    }



     function get_notifications($userid){
         $response = array();   
         $this->db->select('*');
         $this->db->where('responder_id',$userid);
         $this->db->where('reed',0);
         $q = $this->db->get('notifications');
         $response = $q->result_array();
        
        return $response;
       }


         function get_notificationscode22(){
         $response = array(); 
         $this->db->select('*');
         $this->db->where('tables','ccmessage');
         $this->db->where('reed',0);
         $q = $this->db->get('notifications');
         $response = $q->result_array();
         return $response;
       }

          function get_notificationscode(){
         $response = array(); 
         $this->db->select('*');
         $this->db->where('tables','addcode');
         $this->db->where('reed',0);
         $q = $this->db->get('notifications');
         $response = $q->result_array();
         return $response;
       }

        function get_notifications_sales_to_ssuport(){
         $response = array(); 
         $this->db->select('*');
         $this->db->where('type','3');
         $this->db->where('reed',0);
         $q = $this->db->get('notifications');
         $response = $q->result_array();
         return $response;
       }

    function get_notifications_ssuport_to_sales1(){
$id=$this->session->userdata('username');
      $this->db->select('*');
$this->db->from('notifications');
$this->db->like('responder_name', $id);
$this->db->like('reed', '0');

$query = $this->db->get();
 $response = $query->result_array();
        return $response;


        // $id=$this->session->userdata('username');
        // $results = array();
        // $array = array('reed' => '0', 'responder_name=' => $id);
        // $table = 'notifications';
        // $this->db->select("*");
        // $this->db->where($array);
        // $this->db->from($table);
        // $query = $this->db->get();
        // $response = $query->result_array();
        // return $response;

      //  $num_of_records = $query->num_rows();
      //  return $num_of_records;
    }



       function get_notifications_ssuport_to_sales(){
         $response = array(); 
         $this->db->select('*');
         $this->db->where('responder_name',$this->session->userdata('username'));
         $this->db->where('reed',0);
         $q = $this->db->get('notifications');
         $response = $q->result_array();
         return $response;
       }


        function get_notifications_cc_to_clint(){
         $response = array(); 
         $this->db->select('*');
         $this->db->where('responder_id',$this->session->userdata('user_id'));
         $this->db->where('reed',0);
         $q = $this->db->get('notifications');
         $response = $q->result_array();
         return $response;
       }

         function get_reportboxin($s,$s1){  
      
         $sql="SELECT * FROM archives WHERE mdate between '$s' and '$s1' ;";
         $query = $this->db->query($sql);
        return $query->result_array();
        }


         function get_report_marsoom_date_admin($s,$s1){  
      
         $sql="SELECT * FROM payment WHERE marsoom_date_day between '$s' and '$s1' ;";
         $query = $this->db->query($sql);
        return $query->result_array();
        }


         function get_report_evaluation_admin($s){  
      
         $sql="SELECT * FROM evaluation WHERE transfer_no ='$s';";
         $query = $this->db->query($sql);
        return $query->result_array();
        }



         function get_report_marsoom_date($s,$s1){  
       $id=$this->session->userdata('user_id');
         $sql="SELECT * FROM payment WHERE marsoom_date_day between '$s' and '$s1' and user_id ='$id';";
         $query = $this->db->query($sql);
        return $query->result_array();
        }


         function get_report_marsoom_date_sub($s,$s1){  

             $project=$this ->session->userdata('project');
         $section=$this ->session->userdata('section');

         $sql="SELECT * FROM payment WHERE marsoom_date_day between '$s' and '$s1' and project ='$project' and section ='$section';";
         $query = $this->db->query($sql);
        return $query->result_array();
        }






         public function reportbox(){
        $sql = "select * from archives ;";
        $query = $this->db->query($sql);
        return $query->result_array();
        }


        //   function get_reportboxin($s,$s1){  
      
        //  $sql="SELECT * FROM archives WHERE mdate between '$s' and '$s1' ;";
        //  $query = $this->db->query($sql);
        // return $query->result_array();
        // }



       //////////////////////////
         public function getmydata($user_id){
            //validate
            $sql = "SELECT * FROM `users` WHERE `id`='$user_id' LIMIT 1;";
            $query = $this->db->query($sql);
            $data = $query->row_array();
            return $data;
                }
        
        function CHECK_USERS(){ 
            $id = $this->input->get('id');
            $sql = "select * from transactions  where did='$id';";
            $query = $this->db->query($sql);
            return $query->row_array();
        }
        function get_deals(){   
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend=u.id AND t.modest='واردة' AND t.dtype!='سري'  order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_deals1(){   
            $id=$this ->session->userdata('username');
              $sql = "select t.*,u.* from transactions t, users u where t.useridfuture='$id' AND t.useridsend=u.id AND t.modest='واردة'  order by did desc;";

         //   $sql = "select * from transactions   where  useridfuture='$id' and  modest='واردة' ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


        function get_dealsout(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u  where t.useridsend=u.id AND t.modest='صادرة' AND t.dtype!='سري'  order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

        function get_dealsout11(){   
             $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }




        function get_dealsout111(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u  where t.useridsend=u.id AND t.modest='صادرة' AND t.dtype!='سري' ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }



        function get_dealsout11_view(){   
             $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_dealsout12_view(){   
             $id="ali";
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_dealsout13_view(){   
             $id="amlak";
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


         function get_dealsout14_view(){   
             $id="ahmed";
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_dealsout15_view(){   
             $id="faisal";
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_dealsout16_view(){   
             $id="abdula";
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

        function get_dealsout17_view(){   
             $id="mohammed";
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

        function get_dealsout18_view(){   
             $id="saeed";
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.useridfuture='$id' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


        function get_dealsout1(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend='mtka1960' AND t.modest='صادرة' AND t.useridfuture='$id' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


         function get_dealsout121(){   
            $id=$this ->session->userdata('username');
            $id2=$this ->session->userdata('user_id');
            $sql="select * from transactions where useridsend='$id2' AND modest='صادرة' ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }



         function get_dealsout2(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend='u.id' AND t.modest='صادرة' AND t.useridfuture='$id' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }



         function get_dealsout3(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend='103' AND t.modest='صادرة' AND t.useridfuture='$id' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }



        function get_dealsin(){ 
            $id=$this ->session->userdata('username');  
            $sql = "select t.*,u.* from transactions t, users u where t.useridfuture='$id' AND t.useridsend=u.id AND t.modest='واردة' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_dealsin2(){ 
            $id=$this ->session->userdata('username');  
            $sql = "select t.*,u.* from transactions t, users u where t.useridfuture='mtka1960' AND t.useridsend=u.id AND t.modest='واردة' order by t.did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


          function get_dealsin3(){ 
            $id=$this ->session->userdata('username');  
            $sql = "select t.*,u.* from transactions t, users u where t.useridfuture='mohammed' AND t.useridsend=u.id AND t.modest='واردة' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }



          function get_dealsin1(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend=u.id AND useridfuture='admin2' AND t.modest='واردة' order by did desc;";
           // $query =$this->db->order_by('t.useridsend=u.did','DESC');  
            $query = $this->db->query($sql);
            return $query->result_array();
        }


         function get_dealsin11(){   
            $id='admin';
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend=u.id AND t.modest='واردة' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }



        function get_discharge(){   
            $id=$this ->session->userdata('username');
            $sql = "select  * from discharge    order by did desc;";
           // $query =$this->db->order_by('t.useridsend=u.did','DESC');  
            $query = $this->db->query($sql);
            return $query->result_array();
        }


         function get_discharge1(){   
            $id=$this ->session->userdata('username');
            $sql = "select  * from discharge where mode='دفترية'   order by did desc;";
           // $query =$this->db->order_by('t.useridsend=u.did','DESC');  
            $query = $this->db->query($sql);
            return $query->result_array();
        }


         function get_discharge2(){   
            $id=$this ->session->userdata('username');
            $sql = "select  * from discharge where mode='غير الدفترية'   order by did desc;";
           // $query =$this->db->order_by('t.useridsend=u.did','DESC');  
            $query = $this->db->query($sql);
            return $query->result_array();
        }

          function get_discharge3(){   
            $id=$this ->session->userdata('username');
            $sql = "select  * from discharge where mode='آخرى'   order by did desc;";
           // $query =$this->db->order_by('t.useridsend=u.did','DESC');  
            $query = $this->db->query($sql);
            return $query->result_array();
        }





 function get_dealsin55(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend=u.id AND t.modest='واردة' order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }
 

        function get_archivesout($id){   
            $sql = "select t.*,u.* from archives t, users u where t.useridsend=u.id AND t.modest='صادرة' AND t.dtype!='سري'  AND t.type=$id  order by did desc;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_tasks(){   
            $sql = "select t.*,u.* from tasks t, users u where t.useridsend=u.id ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


        function get_dealsvip(){   
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend=u.id AND t.dtype='سري' ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

          function get_tran_details($id){   
            $sql = "select t.*,u.* from tran_details t, users u where t.useridsend=u.id AND t.tid=$id ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

         function get_cc($id){   
            $sql = "SELECT * FROM cc where did=$id ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

        function get_cc_username(){   
          $id=$this ->session->userdata('username');
            $sql = "SELECT * FROM cc where username='$id' ;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }




          function get_dealsvip1(){   
             $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions t, users u where t.useridsend=u.id AND t.dtype='سري' AND t.useridfuture='$id';";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


        function get_action(){   
            $sql = "select * from action;";
            $query = $this->db->query($sql);
            return $query->result_array();
        }
        function editEmployee(){
            $id = $this->input->get('id');
            $this->db->where('did', $id);
            $query = $this->db->get('transactions');
            if($query->num_rows() > 0){
                return $query->row();
            }else{
                return false;
            }
        }

    function editdetails(){
      $id = $this->input->get('id');
      $this->db->where('did', $id);
      $query = $this->db->get('tran_details');
      if($query->num_rows() > 0){
        return $query->row();
      }else{
        return false;
      }
    }

        function deal_connect(){
            $id = $this->input->get('txtId');
            $connect        = implode(',',$this->input->get('connect'));
            $field = array(
                'connect' => $connect
            );
            $this->db->where('did', $id);
            $this->db->update('transactions', $field);
            if($this->db->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        } 


    function deal_replay_change(){
      $id = $this->input->get('txtId');
       $modest="واردة";
      $users    = implode(',',$this->input->get('useridfuture'));
      $field = array(
        'modest' =>$modest
      );
      $this->db->where('did', $id);
      $this->db->update('transactions', $field);
      if($this->db->affected_rows() > 0){
        return true;
      }else{
        return false;
      }
    } 


     function deal_replay_change1(){
      $id = $this->input->get('txtId');
      $modest="صادرة";
      $users    = implode(',',$this->input->get('useridfuture'));
      $field = array(
        'modest' =>$modest
      );
      $this->db->where('did', $id);
      $this->db->update('transactions', $field);
      if($this->db->affected_rows() > 0){
        return true;
      }else{
        return false;
      }
    } 




       function user_recipient($id2){   
             
            $sql = "select  * from users where username='$id2';";
            $query = $this->db->query($sql);
            return $query->row_array();
        }
         function user_recipient22($id22){   
             
            $sql = "select  * from users where id='$id22';";
            $query = $this->db->query($sql);
            return $query->row_array();
        }




        function deal_replay(){
            $id = $this->input->get('txtId');

      // $id2=$this->input->get('useridfuture');
      // $data['dealsout_numbers']=$this->user_model->user_recipient($id2);


            $users      = implode(',',$this->input->get('useridfuture'));
            $field = array(
                'useridfuture' => $users
            );
            $this->db->where('did', $id);
            $this->db->update('transactions', $field);
            if($this->db->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        } 
        function updateEmployee(){
            $id = $this->input->get('txtId');
            $action = $this->input->get('action');
            $field = array(
                'dstatus' => $action
            );
            $this->db->where('did', $id);
            $this->db->update('transactions', $field);
            if($this->db->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        } 

    function updatedetails(){
      $id = $this->input->get('txtId');
      $action = $this->input->get('action');
      $field = array(
        'dstatus' => $action
      );
      $this->db->where('did', $id);
      $this->db->update('tran_details', $field);
      if($this->db->affected_rows() > 0){
        return true;
      }else{
        return false;
      }
    } 


        function deleteEmployee(){
            $id = $this->input->get('id');
            $this->db->where('did', $id);
            $this->db->delete('transactions');
            if($this->db->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        }  

       function deleteevaluation103($id){
         
            $this->db->where('id', $id);
            $this->db->delete('evaluation103');
            if($this->db->affected_rows() > 0){
                return true;
            }else{
                return false;
            }
        }  




      function deletearchives(){
      $id = $this->input->get('id');
      $this->db->where('did', $id);
      $this->db->delete('archives');
      if($this->db->affected_rows() > 0){
        return true;
      }else{
        return false;
      }
    } 


        //Related_add
        function related_add($did,$uid,$name,$phone,$mail,$job,$address)
        {
            if($name !== null)
            {
                $data = array(
                    'did' => $did,
                    'uid' => $uid,
                    'name' => $name,
                    'mobile' => $phone,
                    'email' => $mail,
                    'transaction' => $address,
                    'office' => $job
                
                );
                $this->db->insert('relatedpeople', $data);
            }
        }
        //get_people
        function get_people($id){   
            $sql = "select * from relatedpeople where did='$id';";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

                function get_watch101($id) { 
    date_default_timezone_set('Asia/Riyadh');
    $d = date("Y/m/d");  

    $sql = "SELECT time FROM watch WHERE user_name = ? AND day = ? ORDER BY id ASC LIMIT 1;";
    $query = $this->db->query($sql, array($id, $d));
    
    return $query->row_array(); // ✅ إرجاع أول سجل تمت إضافته في هذا اليوم
}


        //get_deal_id
        function get_deal_id($id){   
            $sql = "select t.*,u.username from transactions t,users u where t.did='$id' AND t.useridsend=u.id;";
            $query = $this->db->query($sql);
            return $query->row_array();
        }

    //   function get_deal_id_view($id){   
    //   $sql = "select t.*,u.username from transactions_view t,users u where t.did='$id' AND t.useridsend=u.id;";
    //   $query = $this->db->query($sql);
    //   return $query->row_array();
    // }


    function get_deal_id_view(){   
            $id=$this ->session->userdata('username');
            $sql = "select t.*,u.* from transactions_view t, users u where t.useridsend=u.id AND useridfuture='admin2' AND t.modest='صادرة' order by did desc;";
           // $query =$this->db->order_by('t.useridsend=u.did','DESC');  
            $query = $this->db->query($sql);
            return $query->result_array();
        }




    function get_tran_details_id($id){   
      $sql = "select t.*,u.username from tran_details t,users u where t.did='$id' AND t.useridsend=u.id;";
      $query = $this->db->query($sql);
      return $query->row_array();
    }


      function get_archives_id($id){   
      $sql = "select t.*,u.username from archives t,users u where t.did='$id' AND t.useridsend=u.id;";
      $query = $this->db->query($sql);
      return $query->row_array();
    }

        //Deal_Add
        function deal_add($uid){
      $dete = date('Y/m');
            $users      = implode(',',$this->input->post('useridfuture'));
            $mdate      = $this->input->post('mdate');
            $hdate      = $this->input->post('hdate');
            $vip        = $this->input->post('vip');
            $mode       = $this->input->post('mode');
            $modest     = $this->input->post('list');
            $type       = $this->input->post('type');
      $name1   = $this->input->post('name1');
      $Identity_no   = $this->input->post('Identity_no');
      $mobile   = $this->input->post('mobile');
      $disc =$this->input->post('disc');
            
            $data = array(
                'title'         => $this->input->post('title'),
                'useridsend'    => $uid,
                'useridfuture'  => $users,
                //'dfile'       => $names,
                'dtype'         => $type,
                'dstatus'       => 'جديد',
                'mdate'         => $mdate,
                'hdate'         => $hdate,
                'vip'           => $vip,
                'mode'          => $mode,
                'modest'        => $modest,
        'date'    => $dete,
        'name'    => $name1,
                'Identity_no'=> $Identity_no,
        'mobile'      => $mobile,
        'disc' => $disc
      
            
            );

            // Insert user
            $this->db->insert('transactions', $data);
            $insert_id = $this->db->insert_id();
            return  $insert_id;
        }


            function discharge_add($uid){
      $dete = date('Y/m');
           // $users      = implode(',',$this->input->post('useridfuture'));
            $mdate      = $this->input->post('mdate');
            $hdate      = $this->input->post('hdate');
            $typen1        = $this->input->post('typen1');
            $typen11       = $this->input->post('typen11');
            $typen2     = $this->input->post('typen2');
            $status       = $this->input->post('status');
      $name1   = $this->input->post('name1');
      $Identity_no   = $this->input->post('Identity_no');
      $mobile   = $this->input->post('mobile');
      $mode   = $this->input->post('mode');
      

      $disc =$this->input->post('disc');
            
            $data = array(
                'title'         => $this->input->post('title'),
                'useridsend'    => $uid,
                'typen1'  => $typen1,
                'typen11'       => $typen11,
                'typen2'         => $typen2,
               
                'mdate'         => $mdate,
                'hdate'         => $hdate,
                'status'           => $status,
                'mode'          => $mode,
              //  'modest'        => $modest,
        'date'    => $dete,
        'name'    => $name1,
                'Identity_no'=> $Identity_no,
        'mobile'      => $mobile,
        'disc' => $disc
      
            
            );

            // Insert user
            $this->db->insert('discharge', $data);
            $insert_id = $this->db->insert_id();
            return  $insert_id;
        }






        function add_cc($id){
       
      $users    = implode(',',$this->input->post('useridfuture'));
      
      
      $data = array(
        'user_id'     => $this->session->userdata('user_id'),
        'did'  => $id,
        'username'  => $users,
         
      );

      // Insert user
      $this->db->insert('cc', $data);
      $insert_id = $this->db->insert_id();
      return  $insert_id;
        }



    function deal_add_view($uid,$id5){
      $dete = date('Y/m');
      $users    = implode(',',$this->input->post('useridfuture'));
      $mdate    = $this->input->post('mdate');
      $hdate    = $this->input->post('hdate');
      $vip    = $this->input->post('vip');
      $mode   = $this->input->post('mode');
      $modest   = $this->input->post('list');
      $type   = $this->input->post('type');
      $name1   = $this->input->post('name1');
      $Identity_no   = $this->input->post('Identity_no');
      $mobile   = $this->input->post('mobile');
      $disc =$this->input->post('disc');  
      $data = array(
        
        'title'     => $this->input->post('title'),
        'useridsend'  => $uid,
        'useridfuture'  => $users,
        'dtype'     => $type,
        'dstatus'     => 'تحت الطلب',
        'mdate'     => $mdate,
        'hdate'     => $hdate,
        'vip'       => $vip,
        'mode'      => $mode,
        'modest'    => $modest,
        'date'    => $dete,
        'name'    => $name1,
        'Identity_no'=> $Identity_no,
        'mobile'      => $mobile,
        'disc' => $disc,
        'did'  => $id5     
      );

      // Insert user
      $this->db->insert('transactions_view', $data);
      $insert_id = $this->db->insert_id();
      return  $insert_id;
        }

    public function tran_update($id,$tran1){

      
      $data = array(
        'id_point'=> $tran1
      );
            $this->db->where('did', $id);
            return $this->db->update('transactions', $data);
     }

  public function deal_update($uid,$names,$id){

      $dete = date('Y/m/d');
      //$users    = implode(',',$this->input->post('useridfuture'));
      $mdate    = $this->input->post('mdate');
      $hdate    = $this->input->post('hdate');
     // $vip    = $this->input->post('vip');
     // $mode   = $this->input->post('mode');
      //$modest   = $this->input->post('list');
     // $type   = $this->input->post('type');
      $name1   = $this->input->post('name1');
      $Identity_no   = $this->input->post('Identity_no');
      $mobile   = $this->input->post('mobile');
      //$disc =$this->input->post('disc');
      
      $data = array(
        'title'     => $this->input->post('title'),
        'useridsend'  => $uid,
        //'useridfuture'  => $users,
        'dfile'     => $names,
        //'dtype'     => $type,
        //'dstatus'     => 'تحت الطلب',
        'mdate'     => $mdate,
        'hdate'     => $hdate,
       // 'vip'       => $vip,
        //'mode'      => $mode,
       // 'modest'    => $modest,
        'date'    => $dete,
        'name'    => $name1,
        'Identity_no'=> $Identity_no,
        'mobile'      => $mobile
       // 'disc' => $disc
      
          
      );

 
            $this->db->where('did', $id);
            return $this->db->update('transactions', $data);
         }


           public function deal_update_view($uid,$names,$id){

      $dete = date('Y/m/d');
      //$users    = implode(',',$this->input->post('useridfuture'));
      $mdate    = $this->input->post('mdate');
      $hdate    = $this->input->post('hdate');
     // $vip    = $this->input->post('vip');
     // $mode   = $this->input->post('mode');
      //$modest   = $this->input->post('list');
     // $type   = $this->input->post('type');
      $name1   = $this->input->post('name1');
      $Identity_no   = $this->input->post('Identity_no');
      $mobile   = $this->input->post('mobile');
      //$disc =$this->input->post('disc');
      
      $data = array(
        'title'     => $this->input->post('title'),
        'useridsend'  => $uid,
        //'useridfuture'  => $users,
        'dfile'     => $names,
        //'dtype'     => $type,
        //'dstatus'     => 'تحت الطلب',
        'mdate'     => $mdate,
        'hdate'     => $hdate,
       // 'vip'       => $vip,
        //'mode'      => $mode,
       // 'modest'    => $modest,
        'date'    => $dete,
        'name'    => $name1,
        'Identity_no'=> $Identity_no,
        'mobile'      => $mobile
       // 'disc' => $disc
      
          
      );

 
            $this->db->where('did', $id);
            return $this->db->update('transactions_view', $data);
         }



     function notifications_add($id5,$id77,$id99){
      //$dete = date('Y/m/d');

      $users    = implode(',',$this->input->post('useridfuture'));
      $sender_name    = $this->session->userdata('username');
      
      
      $data = array(
        
        'sender_name'  => $id99,
        'responder_name'  =>$id77,
        'responder_id' =>'1',
        'message'     => 'تم إضافة معاملة جديدة',
        'oid'     => $id5,
        'type'     => '2'
         
          
      );

      // Insert user
      $this->db->insert('notifications', $data);
      $insert_id = $this->db->insert_id();
      return  $insert_id;
        }


              function notifications_add2(){
      //$dete = date('Y/m/d');
        $id5 = $this->input->get('txtId');
      $users    = implode(',',$this->input->get('useridfuture'));
      $sender_name    = $this->session->userdata('username');
      
      
      $data = array(
        
        'sender_name'  => $sender_name,
        'responder_name'  =>$users,
        'responder_id' =>'1',
         'reed' =>'0',
        'message'     => ' معاملة معاد توجيهها      ',
         
        'type'     => '2'
         
          
      );
 
      $this->db->where('oid', $id5);
      $this->db->update('notifications', $data);
      if($this->db->affected_rows() > 0){
        return true;
      }else{
        return false;
      }
 
       }


         public function not_update($id){ 
            $data = array(
                 'reed' =>'1'
              );
                  $this->db->where('oid', $id);
                  return $this->db->update('notifications', $data);
            }


          public function max_tran(){  

           $this->db->select_max('id');
           $this->db->from('orders');
           $query = $this->db->get();
           return $query->row()->id;
        }
        public function min_portfolio11(){ 
             $user_id=$this->session->userdata('user_id');
             date_default_timezone_set('Asia/Riyadh');
             $id=$this->session->userdata('user_id');
             $d=date("Y/m/d");
             $day= date('d',strtotime("-1 days")) ;


            $this->db->select_min('id');
            $array = array('date' => $day, 'user_id=' => $user_id);
           $this->db->from('portfolio');
           $this->db->where($array);
           $query = $this->db->get();
           return $query->row()->id;
 


}
 






   function tran_details_add($uid,$names,$id){
      $dete = date('Y/m/d');
    //  $users    = implode(',',$this->input->post('useridfuture'));
     // $mdate    = $this->input->post('mdate');
     // $hdate    = $this->input->post('hdate');
    //  $vip    = $this->input->post('vip');
    //  $mode   = $this->input->post('mode');
    //  $modest   = $this->input->post('list');
    //  $type   = $this->input->post('type');
      
      $data = array(
        
        'useridsend'  => $uid,
       // 'useridfuture'  => $users,
        'dfile'     => $names,
        
        'dstatus'     => 'تحت الطلب',
        
         
         
         
       
        'date'    => $dete,
        'disc'      => $this->input->post('disc'),
        'tid'    => $id      
      );
 
      // Insert user
      $this->db->insert('tran_details', $data);
      $insert_id = $this->db->insert_id();
      return  $insert_id;
        }


          function archives_add($uid,$names){

             $dete = date('Y/m/d');

                  $users    = implode(',',$this->input->post('useridfuture'));
                  $mdate    = $this->input->post('mdate');
                  $hdate    = $this->input->post('hdate');
                  $vip    = $this->input->post('vip');
                  $mode   = $this->input->post('mode');
                  $modest   = $this->input->post('list');
                  $type   = $this->input->post('type'); 
                  $data = array(
                    'title'     => $this->input->post('title'),
                    'useridsend'  => $uid,
                    'useridfuture'  => $users,
                    'dfile'     => $names,
                    'dtype'     => $type,
                    'dstatus'     => 'تحت الطلب',
                    'mdate'     => $mdate,
                    'hdate'     => $hdate,
                    'vip'       => $vip,
                    'mode'      => $mode,
                    'modest'    => $modest,
                    'disc'      => $this->input->post('disc'),
                    'type'    =>'1',
                    'date'=>$dete       
                  );

                  // Insert user
                  $this->db->insert('archives', $data);
                  $insert_id = $this->db->insert_id();
                  return  $insert_id;
        }


          function task_add($uid,$names){

                  $users    = implode(',',$this->input->post('useridfuture'));
                  $mdate    = $this->input->post('mdate');
                  $hdate    = $this->input->post('hdate');
                  $vip    = $this->input->post('vip');
                  $mode   = $this->input->post('mode');
                  $modest   = $this->input->post('list');
                  $type   = $this->input->post('type'); 
                  $data = array(
                    'title'     => $this->input->post('title'),
                    'useridsend'  => $uid,
                    'useridfuture'  => $users,
                    'dfile'     => $names,
                    'dtype'     => $type,
                    'dstatus'     => 'تحت الطلب',
                    'mdate'     => $mdate,
                    'hdate'     => $hdate,
                    'vip'       => $vip,
                    'mode'      => $mode,
                    'modest'    => $modest,
                    'disc'      => $this->input->post('disc')       
                  );

                  // Insert user
                  $this->db->insert('tasks', $data);
                  $insert_id = $this->db->insert_id();
                  return  $insert_id;
        }




     function get_userdata(){
        date_default_timezone_set('Asia/Riyadh');
        $id=$this->session->userdata('user_id');
        $sql = "select * from users where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

function get_max_moshrif(){
    
        $sql = "select max(total) from (select sum(payment_amount) as total from payment as x GROUP by user_id) as y;";
        $query = $this->db->query($sql);
       return $query->result_array();
         //return $result->payment_amount;

            }


      function get_ordersdata_star(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where user_id=$id order by id desc;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

       function get_ordersdata_star0101(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where user_id=$id and status='4';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


       function get_ordersdata_star010(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where user_id=$id and status='3';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


      function get_ordersdata_star00(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where user_id=$id and status='1' order by id desc;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }



      function get_ordersdata_admin(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders ORDER BY id DESC ;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

      function get_ordersdata_admin101(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where status='1' order by id desc;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


      function get_ordersdata_admin102(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where status='2';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


      function get_ordersdata_admin103(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where status='3';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

      function get_ordersdata_admin104(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from orders where status='4';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }








      function get_userdata_star(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from users;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


       function get_watch_star(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from watch;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }





       function get_itemdata_star(){
       
        $sql = "select * from items;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


       function get_candidate_positiondata_star(){
       
        $sql = "select * from candidate_position;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

      function get_evaluation_star(){
         date_default_timezone_set('Asia/Riyadh');
        $id=$this->session->userdata('user_id');
        $d=date("Y/m/d");
       
        $sql = "select * from evaluation where day='$d';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

      function get_portfolio_star(){
       
        $sql = "select * from portfolio;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


         function get_portfolio_star_empdata1(){
       
        $sql = "select * from emp_data1;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


      function get_test2(){
       
        $sql = "select * from test2;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }




       function get_evaluation_star1(){
        $id=1;
        $sql = "select * from evaluation where degree1=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


       function get_evaluation_star2(){
        $id=1;
        $sql = "select * from evaluation where degree2=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

       function get_evaluation_star3(){
        $id=1;
        $sql = "select * from evaluation where degree3=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


       function get_evaluation_star4(){
        $id=1;
        $sql = "select * from evaluation where degree4=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

       function get_evaluation_star5(){
        $id=1;
        $sql = "select * from evaluation where degree5=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }






       function get_evaluation_star_user_id(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from evaluation where user_id=$id;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

       function get_evaluation_star_user_id1(){
        $id=$this->session->userdata('user_id');
        $id2=1;
        $sql = "select * from evaluation where user_id=$id and degree1=$id2;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

       function get_evaluation_star_user_id2(){
        $id=$this->session->userdata('user_id');
        $id2=1;
        $sql = "select * from evaluation where user_id=$id and degree2=$id2;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


      function get_evaluation_star_user_id3(){
        $id=$this->session->userdata('user_id');
        $id2=1;
        $sql = "select * from evaluation where user_id=$id and degree3=$id2;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

      function get_evaluation_star_user_id4(){
        $id=$this->session->userdata('user_id');
        $id2=1;
        $sql = "select * from evaluation where user_id=$id and degree4=$id2;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }

       function get_evaluation_star_user_id5(){
        $id=$this->session->userdata('user_id');
        $id2=1;
        $sql = "select * from evaluation where user_id=$id and degree5=$id2;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }




      function get_direct_responsible_star(){
       
        $sql = "select * from direct_responsible;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }





       function get_userdata_star_edit_targit(){
        $id=$this->session->userdata('project');
        $id2=$this->session->userdata('section');
        $sql = "select * from users where project='$id' and section='$id2';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }






      function get_userdata_star_update(){
        $id=$this->session->userdata('user_id');
        $sql = "select * from users where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }


      function get_item_for_edit($id){
       
        $sql = "select * from items where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

       function get_portfolio_for_edit($id5){
       
        $sql = "select * from portfolio where id='$id5';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

       function get_portfolio_for_edit_user_id(){
        $ss=$this ->session->userdata('user_id');
        $sql = "select * from evaluation101 where user_id='$ss';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }


       function get_portfolio_for_edit_mobile($id){
       
        $sql = "select * from test3 where n12='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

             function get_portfolio_for_edit_mobile1111($id){
       
        $sql = "select * from test3 where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

        function get_portfolio_for_edit_mobile1111521($id){
       
        $sql = "select * from test3 where n12='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }

 function get_portfolio_for_edit_mobile11115212514($id){
    $sql = "SELECT * FROM evaluation103 WHERE mobile = ? ORDER BY id DESC LIMIT 1;";
    $query = $this->db->query($sql, array($id));
    return $query->row_array(); // ✅ إرجاع آخر سجل مضاف
}




       function get_portfolio_for_edit_mobile102($id){
       
        $sql = "select * from test4 where n9='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }





       function get_ev_for_edit($id){
       
        $sql = "select * from evaluation where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }




      function get_candidate_position_for_edit($id){
       
        $sql = "select * from candidate_position where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }
      function get_direct_responsible_for_edit($id){
       
        $sql = "select * from direct_responsible where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }




       function get_userdata_star_targit($id){
       // $id=$this->session->userdata('user_id');
        $sql = "select * from users where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
      }





       function get_marsoom_star_update(){
        date_default_timezone_set('Asia/Riyadh');
        $id=$this->session->userdata('user_id');
        $d=date("Y/m/d");
        $sql = "select * from payment where user_id='$id' and marsoom_date_day='$d';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


        function get_marsoom_edit_tranfar(){
        date_default_timezone_set('Asia/Riyadh');
        $id=$this->session->userdata('user_id');
        $d=date("Y/m/d");
        $sql = "select * from evaluation;";
        $query = $this->db->query($sql);
        return $query->result_array();
      }




       function get_marsoom_star_update_admin(){
        date_default_timezone_set('Asia/Riyadh');
        $d=date("Y/m/d");
        $sql = "select * from payment where marsoom_date_day='$d';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


     function get_marsoom_star_update_admin_moshrif(){ 
        $sql="SELECT username,user_id, SUM(payment_amount) FROM payment GROUP BY user_id";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


       function get_marsoom_star_update_admin_moshrif_project(){ 
        $sql="SELECT username,project, SUM(payment_amount) FROM payment GROUP BY project";
        $query = $this->db->query($sql);
        return $query->result_array();
      }





      function get_marsoom_star_update_admin_moshrif_day(){ 
        date_default_timezone_set('Asia/Riyadh');
        $d=date("Y/m/d");
        $sql="SELECT username,user_id, SUM(payment_amount) FROM payment where marsoom_date_day='$d' GROUP BY user_id";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


      function get_marsoom_star_update_admin_project_day(){ 
        date_default_timezone_set('Asia/Riyadh');
        $d=date("Y/m/d");
        $sql="SELECT username,project, SUM(payment_amount) FROM payment where marsoom_date_day='$d' GROUP BY project";
        $query = $this->db->query($sql);
        return $query->result_array();
      }



       function get_marsoom_star_update_admin_moshrif_month(){ 
        date_default_timezone_set('Asia/Riyadh');
        $m=date("Y/m");
        $sql="SELECT username,user_id, SUM(payment_amount) FROM payment where marsoom_date_month='$m' GROUP BY user_id";
        $query = $this->db->query($sql);
        return $query->result_array();
      }


      function get_marsoom_star_update_admin_project_month(){ 
        date_default_timezone_set('Asia/Riyadh');
        $m=date("Y/m");
        $sql="SELECT username,project, SUM(payment_amount) FROM payment where marsoom_date_month='$m' GROUP BY project";
        $query = $this->db->query($sql);
        return $query->result_array();
      }




 


      function get_marsoom_star_update_sub(){
        date_default_timezone_set('Asia/Riyadh');
         $project=$this ->session->userdata('project');
         $section=$this ->session->userdata('section');

         
        $d=date("Y/m/d");
        $sql = "select * from payment where project='$project' and section='$section' and marsoom_date_day='$d';";
        $query = $this->db->query($sql);
        return $query->result_array();
      }






 
     function register($enc_password,$post_image){
        date_default_timezone_set('Asia/Riyadh');
            $data = array(
                 'name' => $this->input->post('name'),
                 'email' => $this->input->post('email'),
                 'username' => $this->input->post('username'),
                 'mobile' => $this->input->post('mobile'),
                 'password' => $enc_password,
                 'type' =>$this->input->post('type'),
                 'status' =>"active",
                // 'section' =>$this->input->post('section'),
                // 'project' =>$this->input->post('project'),
                //  'tragit_month' => $this->input->post('tragit_month'),
                // 'tragit_day' => $this->input->post('tragit_day'),  
                 'path' => $post_image
               
            );

            // Insert user
            return $this->db->insert('users', $data);
        }


         function id_emp_move_tran_update($username,$id){
             
           $data = array(
                  'user_id' => $this->input->POST('user_id'),
                  'username' => $username      
            );
            $this->db->where('id', $id);
            return $this->db->update('portfolio', $data);
       }





         function add_transfer($emp_name){
        date_default_timezone_set('Asia/Riyadh');
        $d1=date('d');
            $data = array(
                 'transfer_no' => $this->input->post('transfer_no'),
                 'user_id' => $this->input->post('user_id'),
                 'username' => $emp_name,
                 'work_location' => $this->input->post('work_location'),
                 'direct_supervisor' =>$this->input->post('direct_supervisor'),
                 'emp_name' =>$this->input->post('emp_name'),
                 'emp_id' =>$this->input->post('emp_id'),
                 'status' =>'1',
 'active' =>$this->input->post('active'),
'tr_type' =>$this->input->post('tr_type'),
'registered' =>$this->input->post('registered'),

                 'date' => $d1
  
            );

            // Insert user
            return $this->db->insert('portfolio', $data);
        }


         function add_transfer101($emp_name){
        date_default_timezone_set('Asia/Riyadh');
        $d1=date('d');
            $data = array(
                 'n1' => $this->input->post('n1'),
                 'n2' => $this->input->post('n2'),
                 'n3' => $this->input->post('n3'),
                 'n4' => $this->input->post('n4'),
                 'n5' => $this->input->post('n5'),
                 'n6' => $this->input->post('n6'),
                 'n7' => $this->input->post('n7'),
                 'n8' => $this->input->post('n8'),
                 'n9' => $this->input->post('n9'),
                 
  
            );

            // Insert user
            return $this->db->insert('test2', $data);
        }






        function add_item(){
        date_default_timezone_set('Asia/Riyadh');
            $data = array(
                 'name' => $this->input->post('name'),
                 'type' => $this->input->post('type'),
                 'unit' => $this->input->post('unit'),
                 'status' => $this->input->post('status')
   
            );

            // Insert user
            return $this->db->insert('items', $data);
        }


        function add_candidate_position(){
        date_default_timezone_set('Asia/Riyadh');
            $data = array(
                 'name' => $this->input->post('name')
            );
            return $this->db->insert('candidate_position', $data);
        }

         function add_direct_responsible(){
        date_default_timezone_set('Asia/Riyadh');
            $data = array(
                 'name' => $this->input->post('name')
            );
            return $this->db->insert('direct_responsible', $data);
        }




        function add_emp(){
        date_default_timezone_set('Asia/Riyadh');
            $data = array(
                 'name' => $this->input->post('name'),
                 'Nationality' => $this->input->post('Nationality'),
                 'Candidate_position' => $this->input->post('Candidate_position'),
                 'Date_of_the_personal_interview' => $this->input->post('Date_of_the_personal_interview'),
                 'Time_of_the_interview' => $this->input->post('Time_of_the_interview'),
                 'Attendance_status_for_the_interview' => $this->input->post('Attendance_status_for_the_interview'),
                 'Total_evaluation_of_the_interview' => $this->input->post('Total_evaluation_of_the_interview'),
                 'Years_of_experience_in_the_same_field' => $this->input->post('Years_of_experience_in_the_same_field'),
                 '  The_final_result' => $this->input->post('The_final_result'),
                 'Description_of_previous_experience' => $this->input->post('Description_of_previous_experience'),
                 'Job_descriptions_for_the_candidate' => $this->input->post('Job_descriptions_for_the_candidate'),
                 'Total_salary' => $this->input->post('Total_salary'),
                 'The_project' => $this->input->post('The_project'),
                 'Work_location' => $this->input->post('Work_location'),
                 'The_candidate_receives_employment_documents' => $this->input->post('The_candidate_receives_employment_documents'),
                 'The_date_set_for_direct' => $this->input->post('The_date_set_for_direct'),
                 'Management' => $this->input->post('Management'),
                 'Direct_responsible' => $this->input->post('Direct_responsible'),
                 'Sponsorship_Transfer' => $this->input->post('Sponsorship_Transfer'),
                 'Job_guarantee' => $this->input->post('Job_guarantee'),
                 'medical_examination' => $this->input->post('medical_examination'),
                 'Criminal_Evidence' => $this->input->post('Criminal_Evidence'),
                 'Bank_account' => $this->input->post('Bank_account'),
                 'Qualification' => $this->input->post('Qualification'),
                 'email' => $this->input->post('email'),
                 'Direct_case' => $this->input->post('Direct_case'),
                 'Indirect_causes' => $this->input->post('Indirect_causes'),
                 'note' => $this->input->post('note')
   
            );

            
            return $this->db->insert('emp_candidate', $data);
        }

         function add_evaluation101(){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
              $emp_name=$this->input->post('n1');

             $q1=$this->input->post('q1');
             $q2=$this->input->post('q2');
             $q3=$this->input->post('q3');
             $q4=$this->input->post('q4');
             $q5=$this->input->post('q5');
             $q6=$this->input->post('q6');
             $q22=$this->input->post('q22');
             $q23=$this->input->post('q23');
             $q24=$this->input->post('q24');
             $q25=$this->input->post('q25');
             $q26=$this->input->post('q26');
             $q7=$this->input->post('q7');
             $q8=$this->input->post('q8');
             $q9=$this->input->post('q9');
             $q10=$this->input->post('q10');
             $q11=$this->input->post('q11');
             $q12=$this->input->post('q12');
             $q20=$this->input->post('q20');
             $q21=$this->input->post('q21');
             $q13=$this->input->post('q9131');
             $q131=$this->input->post('q91311');
             $q132=$this->input->post('q91312');
             $q133=$this->input->post('q91313');
             $q134=$this->input->post('q91314');
             $q135=$this->input->post('q91315');
             $q136=$this->input->post('q91316');
             $q137=$this->input->post('q91317');
             $q14=$this->input->post('q9141');
              
$call_rating=$this->input->post('call_rating');

            
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

            if ($q131== 1) {
                $note551="متعاون";
            }else{
                $note551="";
            }

            if ($q132== 1) {
                $note552=" واعد بالمرسوم  ";
            }else{
                $note552="";
            }
            if ($q133== 1) {
                $note553="أقارب العميل ";
            }else{
                $note553="";
            }
            if ($q134== 1) {
                $note554="تم المرسوم ";
            }else{
                $note554="";
            }
            if ($q135== 1) {
                $note555="لايستطيع المرسوم ";
            }else{
                $note555="";
            }
            if ($q136== 1) {
                $note556="مكالمة خاطئة  ";
            }else{
                $note556="";
            }
            if ($q137== 1) {
                $note557="لايوجد مكالمة  ";
            }else{
                $note557="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'r1' => $this->input->post('r1'),
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('transfer_no'),
                 'Job_number' => $this->input->post('n1'),
                 'date_call' => $this->input->post('date_call'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                // 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                // 'standard1' => $this->input->post('standard1'),
               //  'degree1' => $this->input->post('degree1'),
                
              //   'standard2' => $this->input->post('standard2'),
              //   'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call1'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'emp_name' =>$this->input->post('n7'),
                 'mobile' =>$this->input->post('n9'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note551' => $note551,
                 'note552' => $note552,
                 'note553' => $note553,
                 'note554' => $note554,
                 'note555' => $note555,
                 'note556' => $note556,
                 'note557' => $note557,
                 'note66' => $note66,
                 'n11' => $this->input->post('n11'),
                 'call_duration' =>$this->input->post('call_duration'),
                 'project' =>$this->input->post('n17'),
              //   'emp_name' =>$emp_name,
'call_rating' =>$this->input->post('call_rating'),
'r1' =>$this->input->post('r1'),
'type' =>$this->input->post('type3'),
'sc1' =>$this->input->post('sc1'),
'v1' =>$this->input->post('v1'),
'v2' =>$this->input->post('v2'),
'v3' =>$this->input->post('v3')


                 
               
                 
    
            );

            
            return $this->db->insert('evaluation101', $data);
        }


         function add_evaluation103(){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

            if ($this->input->post('j2') == 1) {
               $f1111=$this->input->post('n3');
            }else{
                $f1111=$this->input->post('j2');

            }
              $emp_name=$this->input->post('n1');

             $q1=$this->input->post('q1');
             $q2=$this->input->post('q2');
             $q3=$this->input->post('q3');
             $q4=$this->input->post('q4');
             $q5=$this->input->post('q5');
             $q6=$this->input->post('q6');
             $q22=$this->input->post('q22');
             $q23=$this->input->post('q23');
             $q24=$this->input->post('q24');
             $q25=$this->input->post('q25');
             $q26=$this->input->post('q26');
             $q7=$this->input->post('q7');
             $q8=$this->input->post('q8');
             $q9=$this->input->post('q9');
             $q10=$this->input->post('q10');
             $q11=$this->input->post('q11');
             $q12=$this->input->post('q12');
             $q20=$this->input->post('q20');
             $q21=$this->input->post('q21');
             $q13=$this->input->post('q13');
             $q131=$this->input->post('q91311');
             $q132=$this->input->post('q91312');
             $q133=$this->input->post('q91313');
             $q134=$this->input->post('q91314');
             $q135=$this->input->post('q91315');
             $q136=$this->input->post('q91316');
             $q137=$this->input->post('q91317');
             $q14=$this->input->post('q9141');
              
$call_rating=$this->input->post('call_rating');

            
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=5; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=4; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=4; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=5; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=20; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=10; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=10; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=10; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=5; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=5; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=5; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=5; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=5; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }
            if ($q13 == 1) {
                $q13=0;
            }else{
               $q13=5; 
            }

              if ($q13== 1) {
                $note55="  توجيه أقارب العميل المتوفي بشكل صحيح 
  ";
            }else{
                $note55="";
            }

            if ($q131== 1) {
                $note551="متعاون";
            }else{
                $note551="";
            }

            if ($q132== 1) {
                $note552=" واعد بالمرسوم  ";
            }else{
                $note552="";
            }
            if ($q133== 1) {
                $note553="أقارب العميل ";
            }else{
                $note553="";
            }
            if ($q134== 1) {
                $note554="تم المرسوم ";
            }else{
                $note554="";
            }
            if ($q135== 1) {
                $note555="لايستطيع المرسوم ";
            }else{
                $note555="";
            }
            if ($q136== 1) {
                $note556="مكالمة خاطئة  ";
            }else{
                $note556="";
            }
            if ($q137== 1) {
                $note557="لايوجد مكالمة  ";
            }else{
                $note557="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                // $q1=0;
                // $q2=0;
                // $q3=0;
                // $q4=0;
                // $q5=0;
                // $q6=0;
                // $q7=0;
                // $q8=0;
                // $q9=0;
                // $q10=0;
                // $q11=0;
                // $q12=0;
                //  $q13=0;
                // $q20=0;
                // $q21=0;
                // $q22=0;
                // $q23=0;
                // $q24=0;
                // $q25=0;
                // $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='   التفاوض مع العميل _طرح حلول مناسبة للسداد   ';
                // $note='عدم اللباقة في الحديث'; 
                // $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية';
                 $note22='  اللباقة في الحديث  ';
                 $note23='افادة العميل بمعلومات غير صحيحة أو غير نظامية ';
                 $note24='تصوير المستندات ';
                 $note25='';
                 $note26='';

            }elseif ($q6 == 0) {
                // $q1=0;
                // $q2=0;
                // $q3=0;
                // $q4=0;
                // $q5=0;
                // $q6=0;
                // $q7=0;
                // $q8=0;
                // $q9=0;
                // $q10=0;
                // $q11=0;
                // $q12=0;
                //  $q13=0;
                // $q20=0;
                // $q21=0;
                // $q22=0;
                // $q23=0;
                // $q24=0;
                // $q25=0;
                // $q26=0;
             
                $note='';

                  $note32='';
                 $note2='   التفاوض مع العميل _طرح حلول مناسبة للسداد   ';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q22 == 0) {
                // $q1=0;
                // $q2=0;
                // $q3=0;
                // $q4=0;
                // $q5=0;
                // $q6=0;
                // $q7=0;
                // $q8=0;
                // $q9=0;
                // $q10=0;
                // $q11=0;
                // $q12=0;
                //  $q13=0;
                //  $q20=0;
                // $q21=0;
                // $q22=0;
                // $q23=0;
                // $q24=0;
                // $q25=0;
                // $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22=' اللباقة في الحديث  ';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                // $q1=0;
                // $q2=0;
                // $q3=0;
                // $q4=0;
                // $q5=0;
                // $q6=0;
                // $q7=0;
                // $q8=0;
                // $q9=0;
                // $q10=0;
                // $q11=0;
                // $q12=0;
                //  $q13=0;
                //  $q20=0;
                // $q21=0;
                // $q22=0;
                // $q23=0;
                // $q24=0;
                // $q25=0;
                // $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='  افادة العميل بمعلومات غير صحيحة أو غير نظامية  ';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                // $q1=0;
                // $q2=0;
                // $q3=0;
                // $q4=0;
                // $q5=0;
                // $q6=0;
                // $q7=0;
                // $q8=0;
                // $q9=0;
                // $q10=0;
                // $q11=0;
                // $q12=0;
                //  $q13=0;
                //  $q20=0;
                // $q21=0;
                // $q22=0;
                // $q23=0;
                // $q24=0;
                // $q25=0;
                // $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='تصوير المستندات  ';
                 $note25='';
                 $note26='';
                
              
            }else{
               // $q6=20;
               // $q7=4; 
               // $q8=4; 
               // $q22=10; 
               // $q23=10; 
               // $q24=10; 
               // $q25=4; 
               // $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'r1' => $this->input->post('r1'),
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                  'emp_name' =>$this->input->post('n7'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n5'),
                 'transfer_no' => $this->input->post('transfer_no'),
                 'Job_number' => $this->input->post('n1'),
                 'date_call' => $this->input->post('date_call'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                // 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                // 'standard1' => $this->input->post('standard1'),
               //  'degree1' => $this->input->post('degree1'),
                
              //   'standard2' => $this->input->post('standard2'),
              //   'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call1'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                
                 'mobile' =>$this->input->post('n9'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                      'note_call11' =>$this->input->post('note_call11'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q13' =>$q13,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note551' => $note551,
                 'note552' => $note552,
                 'note553' => $note553,
                 'note554' => $note554,
                 'note555' => $note555,
                 'note556' => $note556,
                 'note557' => $note557,
                 'note66' => $note66,
                 'n11' => $this->input->post('n11'),
                 'call_duration' =>$this->input->post('call_duration'),
                 'project' =>$this->input->post('n17'),
              //   'emp_name' =>$emp_name,
'call_rating' =>$this->input->post('call_rating'),
'r1' =>$this->input->post('r1'),
'type' =>$this->input->post('type3'),
'sc1' =>$this->input->post('sc1'),
'v1' =>$this->input->post('v1'),
'v2' =>$this->input->post('v2'),
'v3' =>$this->input->post('v3'),
 'cu_status' =>$this->input->post('cu_status'),
                   'col_status' =>$this->input->post('col_status'),
                    'n444' =>$this->input->post('n444'),
                     'n555' =>$this->input->post('n555'),
                     'f1' =>$this->input->post('f1'),
'f2' =>$this->input->post('f2'),
'f3' =>$this->input->post('f3'),
'f4' =>$this->input->post('f4'),
'f5' =>$this->input->post('f5'),
'f6' =>$this->input->post('f6'),
'f7' =>$this->input->post('f7'),
'j1' =>$this->input->post('j1'),
'j2' =>$f1111


                 
               
                 
    
            );

            
            return $this->db->insert('evaluation103', $data);
        }


   function statement3(){
        $sql = "select * from users where type=1;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

function get_employee_reimbursements10155566677() {
    date_default_timezone_set('Asia/Riyadh');
    $d = date("Y/m/d");  

    $sql = "SELECT * FROM evaluation103 WHERE day = ?";
    $query = $this->db->query($sql, array($d));
    
    return $query->result_array();
}

function get_employee_reimbursements101555666771() {
    date_default_timezone_set('Asia/Riyadh');
    $d = date("Y/m/d");  

    // جلب اسم المستخدم من الجلسة
    $username = $this->session->userdata('name');

    // إعداد الاستعلام مع شرط username
    $sql = "SELECT * FROM evaluation103 WHERE day = ? AND username = ?";
    $query = $this->db->query($sql, array($d, $username));
    
    return $query->result_array();
}



public function get_data_by_date_range155($start_date, $end_date) {
     //$user_id = $this->session->userdata('username');
    $this->db->select('*'); // حدد الأعمدة المطلوبة
    $this->db->from('evaluation103'); // اسم الجدول المطلوب
    
    // شروط التواريخ
    $this->db->where('day >=', $start_date); // شرط تاريخ البداية
    $this->db->where('day <=', $end_date); // شرط تاريخ النهاية

    // شروط الأعمدة الإضافية
   // $this->db->where('n6', $user_id); // شرط العمود n1
     

    // تنفيذ الاستعلام
    $query = $this->db->get();

    return $query->result_array(); // إرجاع النتائج كمصفوفة
}

public function get_data_by_date_range155125($start_date, $end_date) {
    $this->db->select('*'); // تحديد جميع الأعمدة
    $this->db->from('evaluation103'); // تحديد الجدول
    
    // شروط التواريخ
    $this->db->where('day >=', $start_date); // شرط تاريخ البداية
    $this->db->where('day <=', $end_date); // شرط تاريخ النهاية

    // جلب اسم المستخدم من الجلسة
    $username = $this->session->userdata('name');

    // إضافة شرط username
    $this->db->where('username', $username);

    // تنفيذ الاستعلام
    $query = $this->db->get();

    return $query->result_array(); // إرجاع النتائج كمصفوفة
}

public function get_data_by_date_range1551255555($id) {
    $this->db->select('*'); // تحديد جميع الأعمدة
    $this->db->from('evaluation103'); // تحديد الجدول

    // ✅ إضافة شرط mobile = $id فقط
    $this->db->where('mobile', $id);

    // تنفيذ الاستعلام
    $query = $this->db->get();

    return $query->result_array(); // إرجاع النتائج كمصفوفة
}

 public function get_next_client_id($current_id) {
    // ✅ التحقق من أن $current_id رقم صحيح وليس فارغًا
    if (!is_numeric($current_id) || empty($current_id)) {
        return null; // منع الأخطاء في حالة إدخال قيمة غير صحيحة
    }

    // ✅ الحصول على اسم المستخدم من الجلسة
    $username = $this->session->userdata('username');

    // ✅ تنفيذ الاستعلام لجلب ID فقط
    $this->db->select('id'); // ✅ تحديد id فقط
    $this->db->from('test3'); // تحديد الجدول
    $this->db->where('id >', $current_id); // ✅ جلب السجل التالي فقط
    
    if (!empty($username)) {
        $this->db->where('username', $username); // ✅ التأكد من تطابق اسم المستخدم
    }

    $this->db->order_by('id', 'ASC'); // ✅ ترتيب تصاعدي للحصول على أقرب سجل لاحق
    $this->db->limit(1); // ✅ جلب سجل واحد فقط

    $query = $this->db->get();

    // ✅ التحقق من وجود نتيجة وإرجاع id فقط
    if ($query->num_rows() > 0) {
        return $query->row()->id; // ✅ إرجاع ID فقط
    }

    return null; // إذا لم يوجد سجل لاحق
}

public function get_previous_client_id($current_id) {
    // ✅ التحقق من أن $current_id رقم صحيح وليس فارغًا
    if (!is_numeric($current_id) || empty($current_id)) {
        return null; // منع الأخطاء في حالة إدخال قيمة غير صحيحة
    }

    // ✅ الحصول على اسم المستخدم من الجلسة
    $username = $this->session->userdata('username');

    // ✅ تنفيذ الاستعلام لجلب ID فقط
    $this->db->select('id'); // ✅ تحديد id فقط
    $this->db->from('test3'); // تحديد الجدول
    $this->db->where('id <', $current_id); // ✅ جلب السجل السابق فقط
    
    if (!empty($username)) {
        $this->db->where('username', $username); // ✅ التأكد من تطابق اسم المستخدم
    }

    $this->db->order_by('id', 'DESC'); // ✅ ترتيب تنازلي للحصول على أقرب سجل سابق
    $this->db->limit(1); // ✅ جلب سجل واحد فقط

    $query = $this->db->get();

    // ✅ التحقق من وجود نتيجة وإرجاع id فقط
    if ($query->num_rows() > 0) {
        return $query->row()->id; // ✅ إرجاع ID فقط
    }

    return null; // إذا لم يوجد سجل سابق
}






function cuntt5524999k52145214147sub11254() { 
     $userid = $this ->session->userdata('username');
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
    $this->db->where('username', $userid); // شرط اسم المستخدم

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}

function cuntt5524999k52145214147sub11254251($userid) { 
     
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
    $this->db->where('username', $userid); // شرط اسم المستخدم

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}


function cuntt5524999k52145214147sub11254555() { 
    
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
   

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}


function cuntt5524999k52145214147sub112541() { 
    $userid = $this->session->userdata('username');
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
    $this->db->where('username', $userid); // شرط اسم المستخدم
    $this->db->where('status', 1); // ✅ إضافة شرط status = 0

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}

function cuntt5524999k52145214147sub112541521($userid) { 
   
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
    $this->db->where('username', $userid); // شرط اسم المستخدم
    $this->db->where('status', 1); // ✅ إضافة شرط status = 0

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}

function cuntt5524999k52145214147sub112541147() { 
   
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
  
    $this->db->where('status', 1); // ✅ إضافة شرط status = 0

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}
function cuntt5524999k52145214147sub1125411() { 
    $userid = $this->session->userdata('username');
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
    $this->db->where('username', $userid); // شرط اسم المستخدم
    $this->db->where('status', 0); // ✅ إضافة شرط status = 0

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}

function cuntt5524999k52145214147sub11254118514($userid) { 
     
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
    $this->db->where('username', $userid); // شرط اسم المستخدم
    $this->db->where('status', 0); // ✅ إضافة شرط status = 0

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}

function cuntt5524999k52145214147sub11254113() { 
    
    
    // إعداد الاستعلام
    $this->db->select('COUNT(*) AS count'); // عدّ جميع السجلات
    $this->db->from('test3');
     
    $this->db->where('status', 0); // ✅ إضافة شرط status = 0

    // تنفيذ الاستعلام
    $result = $this->db->get()->row();

    // إرجاع العدد أو 0 إذا كانت النتيجة فارغة
    return $result->count ?? 0;
}





public function get_data_by_date_range1555($start_date, $end_date) {
    $this->db->select('*'); // حدد الأعمدة المطلوبة
    $this->db->from('evaluation103'); // اسم الجدول المطلوب
    
    // شروط التواريخ
    $this->db->where('day >=', $start_date); // شرط تاريخ البداية
    $this->db->where('day <=', $end_date); // شرط تاريخ النهاية

    // التحقق من وجود قيمة n1 قبل إضافة الشرط
    $username = $this->input->post('n1');
    if (!empty($username)) {
        $this->db->where('username', $username); // إضافة شرط username فقط إذا كان موجودًا
    }

    // تنفيذ الاستعلام
    $query = $this->db->get();

    return $query->result_array(); // إرجاع النتائج كمصفوفة
}








          function add_evaluation1011(){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
              $emp_name=$this->input->post('n1');

             $q1=$this->input->post('q1');
             $q2=$this->input->post('q2');
             $q3=$this->input->post('q3');
             $q4=$this->input->post('q4');
             $q5=$this->input->post('q5');
             $q6=$this->input->post('q6');
             $q22=$this->input->post('q22');
             $q23=$this->input->post('q23');
             $q24=$this->input->post('q24');
             $q25=$this->input->post('q25');
             $q26=$this->input->post('q26');
             $q7=$this->input->post('q7');
             $q8=$this->input->post('q8');
             $q9=$this->input->post('q9');
             $q10=$this->input->post('q10');
             $q11=$this->input->post('q11');
             $q12=$this->input->post('q12');
             $q20=$this->input->post('q20');
             $q21=$this->input->post('q21');
             $q13=$this->input->post('q9131');
             $q131=$this->input->post('q91311');
             $q132=$this->input->post('q91312');
             $q133=$this->input->post('q91313');
             $q134=$this->input->post('q91314');
             $q135=$this->input->post('q91315');
             $q136=$this->input->post('q91316');
             $q137=$this->input->post('q91317');
             $q14=$this->input->post('q9141');
              
$call_rating=$this->input->post('call_rating');

            
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=5; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=4; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=5; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=5; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=15; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=15; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=15; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=15; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=3; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=5; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

            if ($q131== 1) {
                $note551="متعاون";
            }else{
                $note551="";
            }

            if ($q132== 1) {
                $note552=" واعد بالمرسوم  ";
            }else{
                $note552="";
            }
            if ($q133== 1) {
                $note553="أقارب العميل ";
            }else{
                $note553="";
            }
            if ($q134== 1) {
                $note554="تم المرسوم ";
            }else{
                $note554="";
            }
            if ($q135== 1) {
                $note555="لايستطيع المرسوم ";
            }else{
                $note555="";
            }
            if ($q136== 1) {
                $note556="مكالمة خاطئة  ";
            }else{
                $note556="";
            }
            if ($q137== 1) {
                $note557="لايوجد مكالمة  ";
            }else{
                $note557="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0  and $q22 == 0 and $q23 == 0 and $q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='معرفة شكوى العميل وتقديم الحلول، تزويد العميل بالإفادة والاجراء المتخذ 
';
                 $note='تزويد العميل بالوقت المعياري لحل المشكلة ورقم الشكوى 
 '; 

  $note22='تزويد العميل بالوقت المعياري لحل المشكلة ورقم الشكوى 
 '; 
                 $note32=' اللباقة في الحديث 
';
                 $note22=' تزويد العميل بالوقت المعياري لحل المشكلة ورقم الشكوى 
';
                

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='معرفة شكوى العميل وتقديم الحلول، تزويد العميل بالإفادة والاجراء المتخذ 
';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='تزويد العميل بالوقت المعياري لحل المشكلة ورقم الشكوى 
';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='اللباقة في الحديث 
';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='افادة العميل بمعلومات غير صحيحة أو غير نظامية  
';
                 $note25='';
                 $note26='';
                
              
            }else{
               $q6=15;
               
               $q22=15; 
               $q23=15; 
               $q24=15; 
               
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'r1' => $this->input->post('r1'),
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('transfer_no'),
                 'Job_number' => $this->input->post('n1'),
                 'date_call' => $this->input->post('date_call'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                // 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                // 'standard1' => $this->input->post('standard1'),
               //  'degree1' => $this->input->post('degree1'),
                
              //   'standard2' => $this->input->post('standard2'),
              //   'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call1'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'emp_name' =>$this->input->post('n7'),
                 'mobile' =>$this->input->post('n9'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note551' => $note551,
                 'note552' => $note552,
                 'note553' => $note553,
                 'note554' => $note554,
                 'note555' => $note555,
                 'note556' => $note556,
                 'note557' => $note557,
                 'note66' => $note66,
                 'n11' => $this->input->post('n11'),
                 'call_duration' =>$this->input->post('call_duration'),
                 'project' =>$this->input->post('n17'),
              //   'emp_name' =>$emp_name,
'call_rating' =>$this->input->post('call_rating'),
'r1' =>$this->input->post('r1'),
'type' =>$this->input->post('type3'),
'sc1' =>$this->input->post('sc1'),
'v1' =>$this->input->post('v1'),
'v2' =>$this->input->post('v2'),
'v3' =>$this->input->post('v3'),



                 
               
                 
    
            );

            
            return $this->db->insert('evaluation1011', $data);
        }

        function add_evaluation102(){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q1');
             $q2=$this->input->post('q2');
             $q3=$this->input->post('q3');
             $q4=$this->input->post('q4');
             $q5=$this->input->post('q5');
             $q6=$this->input->post('q6');
             $q22=$this->input->post('q22');
             $q23=$this->input->post('q23');
             $q24=$this->input->post('q24');
             $q25=$this->input->post('q25');
             $q26=$this->input->post('q26');
             $q7=$this->input->post('q7');
             $q8=$this->input->post('q8');
             $q9=$this->input->post('q9');
             $q10=$this->input->post('q10');
             $q11=$this->input->post('q11');
             $q12=$this->input->post('q12');
             $q20=$this->input->post('q20');
             $q21=$this->input->post('q21');
             $q13=$this->input->post('q9131');
             $q131=$this->input->post('q91311');
             $q132=$this->input->post('q91312');
             $q133=$this->input->post('q91313');
             $q134=$this->input->post('q91314');
             $q135=$this->input->post('q91315');
             $q136=$this->input->post('q91316');
             $q137=$this->input->post('q91317');
             $q14=$this->input->post('q9141');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

            if ($q131== 1) {
                $note551="متعاون";
            }else{
                $note551="";
            }

            if ($q132== 1) {
                $note552=" واعد بالمرسوم  ";
            }else{
                $note552="";
            }
            if ($q133== 1) {
                $note553="أقارب العميل ";
            }else{
                $note553="";
            }
            if ($q134== 1) {
                $note554="تم المرسوم ";
            }else{
                $note554="";
            }
            if ($q135== 1) {
                $note555="لايستطيع المرسوم ";
            }else{
                $note555="";
            }
            if ($q136== 1) {
                $note556="مكالمة خاطئة  ";
            }else{
                $note556="";
            }
            if ($q137== 1) {
                $note557="لايوجد مكالمة  ";
            }else{
                $note557="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('supervisor'),
                 'transfer_no' => $this->input->post('transfer_no'),
                 'Job_number' => $this->input->post('Job_number'),
                 'date_call' => $this->input->post('date_call'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                // 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                // 'standard1' => $this->input->post('standard1'),
               //  'degree1' => $this->input->post('degree1'),
                
              //   'standard2' => $this->input->post('standard2'),
              //   'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call1'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'emp_name' =>$this->input->post('emp_name'),
                 'mobile' =>$this->input->post('mobile'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note551' => $note551,
                 'note552' => $note552,
                 'note553' => $note553,
                 'note554' => $note554,
                 'note555' => $note555,
                 'note556' => $note556,
                 'note557' => $note557,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration')
                 
               
                 
    
            );

            
            return $this->db->insert('evaluation102', $data);
        }


          function add_evaluation($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q1');
             $q2=$this->input->post('q2');
             $q3=$this->input->post('q3');
             $q4=$this->input->post('q4');
             $q5=$this->input->post('q5');
             $q6=$this->input->post('q6');
             $q22=$this->input->post('q22');
             $q23=$this->input->post('q23');
             $q24=$this->input->post('q24');
             $q25=$this->input->post('q25');
             $q26=$this->input->post('q26');
             $q7=$this->input->post('q7');
             $q8=$this->input->post('q8');
             $q9=$this->input->post('q9');
             $q10=$this->input->post('q10');
             $q11=$this->input->post('q11');
             $q12=$this->input->post('q12');
             $q20=$this->input->post('q20');
             $q21=$this->input->post('q21');
             $q13=$this->input->post('q9131');
             $q14=$this->input->post('q9141');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'r1' => $this->input->post('r1'),
                
                 'm1' => $this->input->post('m1'),
                
                 's1' => $this->input->post('s1'),
               
                 'sc1' => $this->input->post('sc1'),
               
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                 'date_call' => $this->input->post('date_call'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('m1'),
                 'place' => $n4,
              //   'standard1' => $this->input->post('standard1'),
             //    'degree1' => $this->input->post('degree1'),
                
              //   'standard2' => $this->input->post('standard2'),
               //  'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call1'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 
                 'mobile' =>$this->input->post('mobile'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration'),
        'call_rating' =>$this->input->post('call_rating')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }

         function add_evaluation2($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){


           
           date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q12');
             $q2=$this->input->post('q22');
             $q3=$this->input->post('q32');
             $q4=$this->input->post('q42');
             $q5=$this->input->post('q52');
             $q6=$this->input->post('q62');
             $q22=$this->input->post('q221');
             $q23=$this->input->post('q231');
             $q24=$this->input->post('q241');
             $q25=$this->input->post('q251');
             $q26=$this->input->post('q261');
             $q7=$this->input->post('q72');
             $q8=$this->input->post('q82');
             $q9=$this->input->post('q92');
             $q10=$this->input->post('q102');
             $q11=$this->input->post('q1121');
             $q12=$this->input->post('q1221');
             $q20=$this->input->post('q201');
             $q21=$this->input->post('q211');
             $q13=$this->input->post('q9132');
             $q14=$this->input->post('q9142');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
               'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call2'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                 
                 'mobile' =>$this->input->post('mobile2'),
                 'time_call' =>$this->input->post('time_call2'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'r1' => $this->input->post('r2'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m2'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s2'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc2'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration2'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')

                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }


         function add_evaluation3($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
           
           date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q13');
             $q2=$this->input->post('q23');
             $q3=$this->input->post('q33');
             $q4=$this->input->post('q43');
             $q5=$this->input->post('q53');
             $q6=$this->input->post('q63');
             $q7=$this->input->post('q73');
             $q8=$this->input->post('q83');
             $q9=$this->input->post('q93');
             $q10=$this->input->post('q103');
             $q11=$this->input->post('q113');
             $q12=$this->input->post('q123');
             $q13=$this->input->post('q9133');
             $q14=$this->input->post('q9143');
             $q20=$this->input->post('q203');
             $q21=$this->input->post('q213');
             $q22=$this->input->post('q223');
             $q23=$this->input->post('q233');
             $q24=$this->input->post('q243');
             $q25=$this->input->post('q253');
             $q26=$this->input->post('q263');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                  'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call3'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                
                 'mobile' =>$this->input->post('mobile3'),
                 'time_call' =>$this->input->post('time_call3'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                'r1' => $this->input->post('r3'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m3'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s3'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc3'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration3'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }

         function add_evaluation4($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
           
            
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q14');
             $q2=$this->input->post('q24');
             $q3=$this->input->post('q34');
             $q4=$this->input->post('q44');
             $q5=$this->input->post('q54');
             $q6=$this->input->post('q64');
             $q7=$this->input->post('q74');
             $q8=$this->input->post('q84');
             $q9=$this->input->post('q94');
             $q10=$this->input->post('q104');
             $q11=$this->input->post('q114');
             $q12=$this->input->post('q124');
             $q13=$this->input->post('q9134');
             $q14=$this->input->post('q9144');
             $q20=$this->input->post('q204');
             $q21=$this->input->post('q214');
             $q22=$this->input->post('q224');
             $q23=$this->input->post('q234');
             $q24=$this->input->post('q244');
             $q25=$this->input->post('q254');
             $q26=$this->input->post('q264');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call4'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
               
                 'mobile' =>$this->input->post('mobile4'),
                 'time_call' =>$this->input->post('time_call4'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'r1' => $this->input->post('r4'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m4'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s4'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc4'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration4'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }

          function add_evaluation5($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
            
            
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q15');
             $q2=$this->input->post('q25');
             $q3=$this->input->post('q35');
             $q4=$this->input->post('q45');
             $q5=$this->input->post('q55');
             $q6=$this->input->post('q65');
             $q7=$this->input->post('q75');
             $q8=$this->input->post('q85');
             $q9=$this->input->post('q95');
             $q10=$this->input->post('q105');
             $q11=$this->input->post('q115');
             $q12=$this->input->post('q125');
             $q13=$this->input->post('q9135');
             $q14=$this->input->post('q9145');
             $q20=$this->input->post('q205');
             $q21=$this->input->post('q215');
             $q22=$this->input->post('q225');
             $q23=$this->input->post('q235');
             $q24=$this->input->post('q245');
             $q25=$this->input->post('q255');
             $q26=$this->input->post('q265');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call5'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
              
                 'mobile' =>$this->input->post('mobile5'),
                 'time_call' =>$this->input->post('time_call5'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                  'r1' => $this->input->post('r5'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m5'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s5'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc5'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration5'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }


        function add_evaluation6($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
           
            
             date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q16');
             $q2=$this->input->post('q26');
             $q3=$this->input->post('q36');
             $q4=$this->input->post('q46');
             $q5=$this->input->post('q56');
             $q6=$this->input->post('q66');
             $q7=$this->input->post('q76');
             $q8=$this->input->post('q86');
             $q9=$this->input->post('q96');
             $q10=$this->input->post('q106');
             $q11=$this->input->post('q116');
             $q12=$this->input->post('q126');
             $q13=$this->input->post('q9136');
             $q14=$this->input->post('q9146');
             $q20=$this->input->post('q206');
             $q21=$this->input->post('q216');
             $q22=$this->input->post('q226');
             $q23=$this->input->post('q236');
             $q24=$this->input->post('q246');
             $q25=$this->input->post('q256');
             $q26=$this->input->post('q266');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call6'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                
                 'mobile' =>$this->input->post('mobile6'),
                 'time_call' =>$this->input->post('time_call6'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'r1' => $this->input->post('r6'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m6'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s6'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc6'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration6'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }


         function add_evaluation7($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
            
             date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

              $q1=$this->input->post('q17');
             $q2=$this->input->post('q27');
             $q3=$this->input->post('q37');
             $q4=$this->input->post('q47');
             $q5=$this->input->post('q57');
             $q6=$this->input->post('q67');
             $q7=$this->input->post('q77');
             $q8=$this->input->post('q87');
             $q9=$this->input->post('q97');
             $q10=$this->input->post('q107');
             $q11=$this->input->post('q117');
             $q12=$this->input->post('q127');
             $q13=$this->input->post('q9137');
             $q14=$this->input->post('q9147');
             $q20=$this->input->post('q207');
             $q21=$this->input->post('q217');
             $q22=$this->input->post('q227');
             $q23=$this->input->post('q237');
             $q24=$this->input->post('q247');
             $q25=$this->input->post('q257');
             $q26=$this->input->post('q267');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call7'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
               
                 'mobile' =>$this->input->post('mobile7'),
                 'time_call' =>$this->input->post('time_call7'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'r1' => $this->input->post('r7'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m7'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s7'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc7'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration7'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }
       function add_evaluation8($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
           
            
             date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

             $q1=$this->input->post('q18');
             $q2=$this->input->post('q28');
             $q3=$this->input->post('q38');
             $q4=$this->input->post('q48');
             $q5=$this->input->post('q58');
             $q6=$this->input->post('q68');
             $q7=$this->input->post('q78');
             $q8=$this->input->post('q88');
             $q9=$this->input->post('q98');
             $q10=$this->input->post('q108');
             $q11=$this->input->post('q118');
             $q12=$this->input->post('q128');
               $q13=$this->input->post('q9138');
             $q14=$this->input->post('q9148');
             $q20=$this->input->post('q208');
             $q21=$this->input->post('q218');
             $q22=$this->input->post('q228');
             $q23=$this->input->post('q238');
             $q24=$this->input->post('q248');
             $q25=$this->input->post('q258');
             $q26=$this->input->post('q268');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call8'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
               
                 'mobile' =>$this->input->post('mobile8'),
                 'time_call' =>$this->input->post('time_call8'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'r1' => $this->input->post('r8'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m8'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s8'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc8'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration8'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }


         function add_evaluation9($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
            
           
               date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");

              $q1=$this->input->post('q19');
             $q2=$this->input->post('q29');
             $q3=$this->input->post('q39');
             $q4=$this->input->post('q49');
             $q5=$this->input->post('q59');
             $q6=$this->input->post('q69');
             $q7=$this->input->post('q79');
             $q8=$this->input->post('q89');
             $q9=$this->input->post('q99');
             $q10=$this->input->post('q109');
             $q11=$this->input->post('q119');
             $q12=$this->input->post('q129');
               $q13=$this->input->post('q9139');
             $q14=$this->input->post('q9149');
             $q20=$this->input->post('q209');
             $q21=$this->input->post('q219');
             $q22=$this->input->post('q229');
             $q23=$this->input->post('q239');
             $q24=$this->input->post('q249');
             $q25=$this->input->post('q259');
             $q26=$this->input->post('q269');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call9'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
               
                 'mobile' =>$this->input->post('mobile9'),
                 'time_call' =>$this->input->post('time_call9'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'r1' => $this->input->post('r9'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m9'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s9'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc9'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration9'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }

           function add_evaluation10($n1,$n2,$n3,$n4,$n5,$n6,$n7,$n8,$n9,$n10,$n11,$n12,$n13){
            
            
           
               date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
             $q1=$this->input->post('q110');
             $q2=$this->input->post('q210');
             $q3=$this->input->post('q310');
             $q4=$this->input->post('q410');
             $q5=$this->input->post('q510');
             $q6=$this->input->post('q610');
             $q7=$this->input->post('q710');
             $q8=$this->input->post('q810');
             $q9=$this->input->post('q910');
             $q10=$this->input->post('q1010');
             $q11=$this->input->post('q1110');
             $q12=$this->input->post('q1210');
             $q13=$this->input->post('q91310');
             $q14=$this->input->post('q91410');
             $q20=$this->input->post('q2010');
             $q21=$this->input->post('q2110');
             $q22=$this->input->post('q2210');
             $q23=$this->input->post('q2310');
             $q24=$this->input->post('q2410');
             $q25=$this->input->post('q2510');
             $q26=$this->input->post('q2610');
           
            if ($q1 == 1) {
                $q1=0;
            }else{
               $q1=3; 
            }
             if ($q2 == 1) {
                $q2=0;
            }else{
               $q2=3; 
            }
             if ($q3 == 1) {
                $q3=0;
            }else{
               $q3=3; 
            }
             if ($q4 == 1) {
                $q4=0;
            }else{
               $q4=3; 
            }
             if ($q5 == 1) {
                $q5=0;
            }else{
               $q5=2; 
            }
             if ($q6 == 1) {
                $q6=0;
            }else{
               $q6=4; 
            }
             if ($q22 == 1) {
                $q22=0;
            }else{
               $q22=4; 
            }
             if ($q23 == 1) {
                $q23=0;
            }else{
               $q23=3; 
            }
             if ($q24 == 1) {
                $q24=0;
            }else{
               $q24=4; 
            }
             if ($q25 == 1) {
                $q25=0;
            }else{
               $q25=4; 
            }
             if ($q26 == 1) {
                $q26=0;
            }else{
               $q26=3; 
            }
             if ($q7 == 1) {
                $q7=0;
            }else{
               $q7=4; 
            }
             if ($q8 == 1) {
                $q8=0;
            }else{
               $q8=4; 
            }
              if ($q9 == 1) {
                $q9=0;
            }else{
               $q9=10; 
            }
              if ($q10 == 1) {
                $q10=0;
            }else{
               $q10=10; 
            }
             
             if ($q11 == 1) {
                $q11=0;
            }else{
               $q11=3; 
            }
             if ($q12 == 1) {
                $q12=0;
            }else{
               $q12=3; 
            }
             if ($q20 == 1) {
                $q20=0;
            }else{
               $q20=10; 
            }
             if ($q21 == 1) {
                $q21=0;
            }else{
               $q21=20; 
            }

              if ($q13== 1) {
                $note55="رافض المرسوم";
            }else{
                $note55="";
            }

             if ($q14== 1) {
                $note66="لا";
            }else{
                $note66="";
            }


            
   


            if ($q6 == 0 and $q7 == 0 and $q8 == 0 and $q22 == 0 and $q23 == 0 and $q24 == 0 and $q25 == 0 and $q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note='تصوير المستندات'; 
                 $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='افادة العميل بحل الممولين';
                 $note23='التعريف بصفة المحامي';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='تجنب استخدام اسم البنك المركزي';

            }elseif ($q6 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
             
                $note='';

                  $note32='';
                 $note2='الجدال / اغلاق الخط من قبل الموظف';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
            }elseif ($q7 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                   $note='تصوير المستندات'; 
                $note2='';
                  $note32='';
                  $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q8 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='افادة العميل بمعلومات غير صحيحة أو غير نظامية عند عدم';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q22 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='افادة العميل بحل الممولين';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q23 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='التعريف بصفة المحامي';
                 $note24='';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q24 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='  استغلال العامل مركزه الوظيفي - افادة العميل برقم الجوال الشخصي للموظف';
                 $note25='';
                 $note26='';
                
              
            }elseif ($q25 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='افشاء معلومات العميل لطرف ثالث';
                 $note26='';
                
              
            }elseif ($q26 == 0) {
                $q1=0;
                $q2=0;
                $q3=0;
                $q4=0;
                $q5=0;
                $q6=0;
                $q7=0;
                $q8=0;
                $q9=0;
                $q10=0;
                $q11=0;
                $q12=0;
                 $q20=0;
                $q21=0;
                $q22=0;
                $q23=0;
                $q24=0;
                $q25=0;
                $q26=0;
                // $q13=0;
                // $q14=0;
                // $q15=0;
                $note='';
                $note2='';
                $note32='';
                 $note22='';
                 $note23='';
                 $note24='';
                 $note25='';
                 $note26='تجنب استخدام اسم البنك المركزي';
                
              
            }else{
               $q6=4;
               $q7=4; 
               $q8=4; 
               $q22=4; 
               $q23=3; 
               $q24=4; 
               $q25=4; 
               $q26=3; 
               $note='';
               $note2='';
               $note32='';
               $note22='';
               $note23='';
               $note24='';
               $note25='';
               $note26='';
            }

     
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
               
              'note1' => $note,
                  'note2' => $note2,
                   'note3' => $this->input->post('note_call10'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
               
                 'mobile' =>$this->input->post('mobile10'),
                 'time_call' =>$this->input->post('time_call10'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'r1' => $this->input->post('r10'),
                 // 'r2' => $this->input->post('r2'),
                 // 'r3' => $this->input->post('r3'),
                 // 'r4' => $this->input->post('r4'),
                 // 'r5' => $this->input->post('r5'),
                 // 'r6' => $this->input->post('r6'),
                 // 'r7' => $this->input->post('r7'),
                 // 'r8' => $this->input->post('r8'),
                 // 'r9' => $this->input->post('r9'),
                 // 'r10' => $this->input->post('r10'),
                 'm1' => $this->input->post('m10'),
                 // 'm2' => $this->input->post('m2'),
                 // 'm3' => $this->input->post('m3'),
                 // 'm4' => $this->input->post('m4'),
                 // 'm5' => $this->input->post('m5'),
                 // 'm6' => $this->input->post('m6'),
                 // 'm7' => $this->input->post('m7'),
                 // 'm8' => $this->input->post('m8'),
                 // 'm9' => $this->input->post('m9'),
                 // 'm10' => $this->input->post('m10'),
                 's1' => $this->input->post('s10'),
                 // 's2' => $this->input->post('s2'),
                 // 's3' => $this->input->post('s3'),
                 // 's4' => $this->input->post('s4'),
                 // 's5' => $this->input->post('s5'),
                 // 's6' => $this->input->post('s6'),
                 // 's7' => $this->input->post('s7'),
                 // 's8' => $this->input->post('s8'),
                 // 's9' => $this->input->post('s9'),
                 // 's10' => $this->input->post('s10'),
                 'sc1' => $this->input->post('sc10'),
                 // 'sc2' => $this->input->post('sc2'),
                 // 'sc3' => $this->input->post('sc3'),
                 // 'sc4' => $this->input->post('sc4'),
                 // 'sc5' => $this->input->post('sc5'),
                 // 'sc6' => $this->input->post('sc6'),
                 // 'sc7' => $this->input->post('sc7'),
                 // 'sc8' => $this->input->post('sc8'),
                 // 'sc9' => $this->input->post('sc9'),
                 // 'sc10' => $this->input->post('sc10'),
                 'time' =>$time,
                 'q1' =>$q1,
                 'q2' =>$q2,
                 'q3' =>$q3,
                 'q4' =>$q4,
                 'q5' =>$q5,
                 'q6' =>$q6,
                 'q7' =>$q7,
                 'q8' =>$q8,
                 'q9' =>$q9,
                 'q10' =>$q10,
                 'q11' =>$q11,
                 'q12' =>$q12,
                 'q20' =>$q20,
                 'q21' =>$q21,
                 'q22' =>$q22,
                 'q23' =>$q23,
                 'q24' =>$q24,
                 'q25' =>$q25,
                 'q26' =>$q26,
                 'note32' => $note32,
                 'note22' => $note22,
                 'note23' => $note23,
                 'note24' => $note24,
                 'note25' => $note25,
                 'note26' => $note26,
                 'note55' => $note55,
                 'note66' => $note66,
                 'call_duration' =>$this->input->post('call_duration10'),
                  'call_rating' =>$this->input->post('call_rating'),
                  'date_call' =>$this->input->post('date_call')
                 
                 // 'q13' =>$q13,
                 // 'q14' =>$q14,
                 // 'q15' =>$q15,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }











           function user_update($id){

             
           $data = array(
          
            'date_call' =>$this->input->POST('date_call')   
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }



           function edit_evaluation($id){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
            $degree1=$this->input->post('degree1');
            $degree2=$this->input->post('degree2');
           // $degree3=$this->input->post('degree3');
            $degree4=$this->input->post('degree4');
            $degree5=$this->input->post('degree5');

            if ($degree1 == 0) {
                $note1= $this->input->post('note1');
            }else{
                $note1="";
            }

             if ($degree2 == 0) {
                $note2= $this->input->post('note2');
            }else{
                $note2="";
            }
            //  if ($degree3 == 0) {
            //     $note3= $this->input->post('note3');
            // }else{
            //     $note3="";
            // }

             if ($degree4 == 0) {
                $note4= $this->input->post('note4');
            }else{
                $note4="";
            }
             if ($degree5 == 0) {
                $note5= $this->input->post('note5');
            }else{
                $note5="";
            }

            if ($this->input->post('successful_number_call')=="") {
               $a=0;
            }else{
                 $a=$this->input->post('successful_number_call');
             }
              if ($this->input->post('unsuccessful_number_call')=="") {
               $b=0;
            }else{
                $b=$this->input->post('unsuccessful_number_call');
             }

           
            
            //$x=$a+$b;
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
               //  'date' => $y,
                // 'month' => $m,
               //  'day' =>  $d,
                'supervisor' => $this->input->post('n6'),
                 'transfer_no' => $this->input->post('n9'),
                 'Job_number' => $this->input->post('n1'),
                 'emp_name' =>$this->input->post('n7'),
                // 'number_of_call' => $x,
                 'successful_number_call' => $this->input->post('successful_number_call'),
                 'number_of_call' => $this->input->post('number_of_call'),
                 'place' => $this->input->post('place'),
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => $this->input->post('degree1'),
                 'note1' => $note1,
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => $this->input->post('degree2'),
                 'note2' => $note2,
                // 'standard3' => $this->input->post('standard3'),
                // 'degree3' => $this->input->post('degree3'),
                
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => $this->input->post('degree4'),
                // 'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => $this->input->post('degree5'),
                // 'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'emp_name' =>$this->input->post('emp_name'),
                 'mobile' =>$this->input->post('mobile'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                 'time' =>$time,
                 
    
            );

            $this->db->where('id', $id);
         return $this->db->update('evaluation', $data);
        }



      function add_evaluation_nn(){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
             $degree1=$this->input->post('degree1');
            $degree2=$this->input->post('degree2');
           // $degree3=$this->input->post('degree3');
            $degree4=$this->input->post('degree4');
            $degree5=$this->input->post('degree5');

            if ($degree1 == 0) {
                $note1= $this->input->post('note1');
            }else{
                $note1="";
            }

             if ($degree2 == 0) {
                $note2= $this->input->post('note2');
            }else{
                $note2="";
            }
            //  if ($degree3 == 0) {
            //     $note3= $this->input->post('note3');
            // }else{
            //     $note3="";
            // }

             if ($degree4 == 0) {
                $note4= $this->input->post('note4');
            }else{
                $note4="";
            }
             if ($degree5 == 0) {
                $note5= $this->input->post('note5');
            }else{
                $note5="";
            }

            if ($this->input->post('successful_number_call')=="") {
               $a=0;
            }else{
                 $a=$this->input->post('successful_number_call');
             }
              if ($this->input->post('unsuccessful_number_call')=="") {
               $b=0;
            }else{
                $b=$this->input->post('unsuccessful_number_call');
             }

           
            
            //$x=$a+$b;
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('supervisor'),
                 'transfer_no' => $this->input->post('transfer_no'),
                 'Job_number' => $this->input->post('Job_number'),
                 'date_call' => $this->input->post('date_call'),
                  'place' => $this->input->post('place'),
                // 'number_of_call' => $x,
                 'successful_number_call' => '0',
                 'number_of_call' => $this->input->post('number_of_call'),
                
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => '0',
                 'note1' => $note1,
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => '0',
                 'note2' => $note2,
               //  'standard3' => $this->input->post('standard3'),
                // 'degree3' => '0',
               //  'note3' =>$note3,
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => '0',
               //  'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => '0',
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'emp_name' =>$this->input->post('emp_name'),
                 'mobile' =>$this->input->post('mobile'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                   'mobile66' =>$this->input->post('mobile66'),
                     'time_call66' =>$this->input->post('time_call66'),
                 'time' =>$time,
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }





        function edit_evaluation_nn($id){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
             $degree1=$this->input->post('degree1');
            $degree2=$this->input->post('degree2');
           // $degree3=$this->input->post('degree3');
            $degree4=$this->input->post('degree4');
            $degree5=$this->input->post('degree5');

            if ($degree1 == 0) {
                $note1= $this->input->post('note1');
            }else{
                $note1="";
            }

             if ($degree2 == 0) {
                $note2= $this->input->post('note2');
            }else{
                $note2="";
            }
            //  if ($degree3 == 0) {
            //     $note3= $this->input->post('note3');
            // }else{
            //     $note3="";
            // }

             if ($degree4 == 0) {
                $note4= $this->input->post('note4');
            }else{
                $note4="";
            }
             if ($degree5 == 0) {
                $note5= $this->input->post('note5');
            }else{
                $note5="";
            }

            if ($this->input->post('successful_number_call')=="") {
               $a=0;
            }else{
                 $a=$this->input->post('successful_number_call');
             }
              if ($this->input->post('unsuccessful_number_call')=="") {
               $b=0;
            }else{
                $b=$this->input->post('unsuccessful_number_call');
             }

           
            
            //$x=$a+$b;
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
               //  'date' => $y,
               //  'month' => $m,
                // 'day' =>  $d,
                 'supervisor' => $this->input->post('supervisor'),
                 'transfer_no' => $this->input->post('transfer_no'),
                 'Job_number' => $this->input->post('Job_number'),
                 'date_call' => $this->input->post('date_call'),
                  'place' => $this->input->post('place'),
                // 'number_of_call' => $x,
                 'successful_number_call' => '0',
                 'number_of_call' => $this->input->post('number_of_call'),
                
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => '0',
                 'note1' => $note1,
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => '0',
                 'note2' => $note2,
               //  'standard3' => $this->input->post('standard3'),
                // 'degree3' => '0',
               //  'note3' =>$note3,
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => '0',
               //  'note4' => $note4,
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => '0',
               //  'note5' => $note5,
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'emp_name' =>$this->input->post('emp_name'),
                 'mobile' =>$this->input->post('mobile'),
                 'time_call' =>$this->input->post('time_call'),
                  'mobile4' =>$this->input->post('mobile4'),
                 'time_call4' =>$this->input->post('time_call4'),
                 'time' =>$time,
                 
    
            );

           $this->db->where('id', $id);
         return $this->db->update('evaluation', $data);
        }


        





         function add_evaluation_ss(){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
            if ($this->input->post('successful_number_call')=="") {
               $a=0;
            }else{
                 $a=$this->input->post('successful_number_call');
             }
              if ($this->input->post('unsuccessful_number_call')=="") {
               $b=0;
            }else{
                $b=$this->input->post('unsuccessful_number_call');
             }

           
            
            //$x=$a+$b;
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' =>$this ->session->userdata('name'),
                 'date' => $y,
                 'month' => $m,
                 'day' =>  $d,
                 'supervisor' => $this->input->post('supervisor'),
                 'transfer_no' => $this->input->post('transfer_no'),
                 'Job_number' => $this->input->post('Job_number'),
                 'date_call' => $this->input->post('date_call'),
                  'place' => $this->input->post('place'),
                // 'number_of_call' => $x,
                 'successful_number_call' => '0',
                 'number_of_call' => $this->input->post('number_of_call'),
                
                 'standard1' => $this->input->post('standard1'),
                 'degree1' => '0',
                 'note1' => $this->input->post('note1'),
                 'standard2' => $this->input->post('standard2'),
                 'degree2' => '0',
                 'note2' => $this->input->post('note2'),
                 'standard3' => $this->input->post('standard3'),
                 'degree3' => '0',
                 'note3' => $this->input->post('note3'),
                 'standard4' => $this->input->post('standard4'),
                 'degree4' => '0',
               //  'note4' => $this->input->post('note4'),
                 'standard5' => $this->input->post('standard5'),
                 'degree5' => '0',
             //    'note5' => $this->input->post('note5'),
                 'note_quality_controller' => $this->input->post('note_quality_controller'),
                 'emp_name' =>$this->input->post('emp_name'),
                 
    
            );

            
            return $this->db->insert('evaluation', $data);
        }


         function add_test7($result){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $time=date("h:i:s");            
            $data = array(
                 'n1' => $d,
                 'n2' => $this ->session->userdata('username'),
                 'n3' => $this->input->post('Job_number'),
                 'n4' => $this->input->post('supervisor'),
                 'n5' => $this->input->post('n1'),
                 'n6' => $this->input->post('transfer_no'),
                 'n7' => $this->input->post('date_call'),
                 'n8' => $this->input->post('time_call'),
                 'n9' => $this->input->post('call_duration'),
                 'n10' =>'0',
                 'n11' =>$time,
                 'n12' => '0%',
                 'n13' => $this->input->post('n17'),
                 'n14' => $result,
                 'n15' => $this->input->post('note_call1')
                 
                
            );
            return $this->db->insert('test7', $data);
        }







       function item_update($id){
             
           $data = array(
                  'name' =>$this->input->POST('name'),
                  'type' => $this->input->POST('type'),
                  'unit' => $this->input->POST('unit'),
                  'status' => $this->input->POST('status')         
            );
            $this->db->where('id', $id);
            return $this->db->update('items', $data);
       }

        function portfolio_update($id5){
             date_default_timezone_set('Asia/Riyadh');
            $d=date("d");
             
           $data = array(
                  'date' => $d,
                         
            );
            $this->db->where('id', $id5);
            return $this->db->update('portfolio', $data);
       }


       function candidate_position_update($id){
             
           $data = array(
                  'name' =>$this->input->POST('name')
                        
            );
            $this->db->where('id', $id);
            return $this->db->update('candidate_position', $data);
       }


        function direct_responsible_update($id){
             
           $data = array(
                  'name' =>$this->input->POST('name')
                        
            );
            $this->db->where('id', $id);
            return $this->db->update('direct_responsible', $data);
       }




       function add_watch($username,$op_name){
          date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
            $data = array(
                 'user_name' => $username,
                 'time' => $time,
                 'date' => $y,
                 'month' =>  $m,
                 'day' => $d,
                 'op_name' => $op_name,
                 'op_type' =>'1'
            );
            return $this->db->insert('watch', $data);
        }


         function add_watch_log_out($username,$op_name){
          date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
            $data = array(
                 'user_name' => $username,
                 'time' => $time,
                 'date' => $y,
                 'month' =>  $m,
                 'day' => $d,
                 'op_name' => $op_name,
                 'op_type' =>'2'
            );
            return $this->db->insert('watch', $data);
        }





         function add_evaluation_time($id5){
          date_default_timezone_set('Asia/Riyadh');
            $op_name="تقييم جديد";

            $username= $this->session->userdata('name');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
            
            $data = array(
                 'user_name' => $username,
                 'time' => $time,
                 'date' => $y,
                 'month' =>  $m,
                 'day' => $d,
                 'op_name' => $op_name,
                 'tr_no' =>$this->input->post('transfer_no'),
                 'op_type' =>'3',
                 'user_id' =>$this->session->userdata('user_id')
            );
            return $this->db->insert('watch', $data);
        }



         function add_watch101($username,$op_name,$op_type){
          date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
            $data = array(
                 'user_name' => $username,
                 'time' => $time,
                 'date' => $y,
                 'month' =>  $m,
                 'day' => $d,
                 'op_name' => $op_name,
                 'op_type' => $op_type


            );
            return $this->db->insert('watch', $data);
        }





    


         function get_items1(){
            $Payment=$this->session->userdata('Payment');
        $sql = "select * from items where type='$Payment';";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

       function get_candidate_position1(){
           
        $sql = "select * from candidate_position;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
     function get_candidate_position1_emp_data($id2){
           
        $sql = "select * from emp_data1 where n9='$id2';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    function get_candidate_position1_emp_data_users(){
         $ss=$this ->session->userdata('user_id');
           
        $sql = "select * from users where id='$ss';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }




     function get_direct_responsible1(){
           
        $sql = "select * from direct_responsible;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }




    function add_order(){
        date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");
            if ($this->input->post('priority') == 1) {
                $status='2';
            }else
            $status='1';
           

            $data = array(
                  'user_id' => $this ->session->userdata('user_id'),
                  'name' => $this ->session->userdata('name'),
                  'titel' => $this->input->post('titel'),
                  'reason' => $this->input->post('reason'),
                  'type' => $this->session->userdata('Payment'),
                  'priority' => $this->input->post('priority'),
                  'Payment' => $this->input->post('Payment'),
                  
                  //'path' => $names,
                  'date_order' => $d,
                  'date_month' => $m,
                  'date_years' => $y,
                  'time_order' => $time,  
                  'location' => $this->input->post('location'),    

                  'status' => $status
            );
            return $this->db->insert('orders', $data);
        }


         function create_attachment($post_image,$id){
        
            $data = array(
                  'user_id' => $this ->session->userdata('user_id'), 
                  'name' => $this->input->post('Name'),
                  'path' => $post_image,
                  'order_id' => $id,    
                  
            );
            return $this->db->insert('attachment', $data);
        }




          function add_detaies_order($id5){
          
            $data = array(
                  'order_id' => $id5,
                  'item_name' => $this->input->post('item_name'),
                  'quantity' => $this->input->post('quantity'),    
                  'price' => $this->input->post('price')
            );
            return $this->db->insert('order_detailes', $data);
         }
          function add_detaies_order1($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name1'),
                  'quantity' => $this->input->post('quantity1'),   
                  'price' => $this->input->post('price1') 
            );
            return $this->db->insert('order_detailes', $data);
         }
          function add_detaies_order2($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name2'),
                  'quantity' => $this->input->post('quantity2'),    
                  'price' => $this->input->post('price2')
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order3($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name3'),
                  'quantity' => $this->input->post('quantity3'), 
                  'price' => $this->input->post('price3')   
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order4($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name4'),
                  'quantity' => $this->input->post('quantity4'),   
                  'price' => $this->input->post('price4') 
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order5($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name5'),
                  'quantity' => $this->input->post('quantity5'),
                  'price' => $this->input->post('price5')    
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order6($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' =>$this->input->post('item_name6'),
                  'quantity' => $this->input->post('quantity6'),  
                  'price' => $this->input->post('price6')  
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order7($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name7'),
                  'quantity' => $this->input->post('quantity7'),
                  'price' => $this->input->post('price7')    
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order8($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name8'),
                  'quantity' => $this->input->post('quantity8'),  
                  'price' => $this->input->post('price8')  
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order9($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name9'),
                  'quantity' => $this->input->post('quantity9'),  
                  'price' => $this->input->post('price9')  
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order10($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name10'),
                  'quantity' => $this->input->post('quantity10'),  
                  'price' => $this->input->post('price10')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order11($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name11'),
                  'quantity' => $this->input->post('quantity11'),  
                  'price' => $this->input->post('price11')  
            );
            return $this->db->insert('order_detailes', $data);
         }


         function add_detaies_order12($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name12'),
                  'quantity' => $this->input->post('quantity12'),  
                  'price' => $this->input->post('price12')  
            );
            return $this->db->insert('order_detailes', $data);
         }


          function add_detaies_order13($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name13'),
                  'quantity' => $this->input->post('quantity13'),  
                  'price' => $this->input->post('price13')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order14($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name14'),
                  'quantity' => $this->input->post('quantity14'),  
                  'price' => $this->input->post('price14')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order15($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name15'),
                  'quantity' => $this->input->post('quantity15'),  
                  'price' => $this->input->post('price15')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order16($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name16'),
                  'quantity' => $this->input->post('quantity16'),  
                  'price' => $this->input->post('price16')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order17($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name17'),
                  'quantity' => $this->input->post('quantity17'),  
                  'price' => $this->input->post('price17')  
            );
            return $this->db->insert('order_detailes', $data);
         }
          function add_detaies_order18($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name18'),
                  'quantity' => $this->input->post('quantity18'),  
                  'price' => $this->input->post('price18')  
            );
            return $this->db->insert('order_detailes', $data);
         }
         function add_detaies_order19($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name19'),
                  'quantity' => $this->input->post('quantity19'),  
                  'price' => $this->input->post('price19')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order20($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name20'),
                  'quantity' => $this->input->post('quantity20'),  
                  'price' => $this->input->post('price20')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order21($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name21'),
                  'quantity' => $this->input->post('quantity21'),  
                  'price' => $this->input->post('price21')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order22($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name22'),
                  'quantity' => $this->input->post('quantity22'),  
                  'price' => $this->input->post('price22')  
            );
            return $this->db->insert('order_detailes', $data);
         }


          function add_detaies_order23($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name23'),
                  'quantity' => $this->input->post('quantity23'),  
                  'price' => $this->input->post('price23')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order24($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name24'),
                  'quantity' => $this->input->post('quantity24'),  
                  'price' => $this->input->post('price24')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order25($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name25'),
                  'quantity' => $this->input->post('quantity25'),  
                  'price' => $this->input->post('price25')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order26($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name26'),
                  'quantity' => $this->input->post('quantity26'),  
                  'price' => $this->input->post('price26')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order27($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name27'),
                  'quantity' => $this->input->post('quantity27'),  
                  'price' => $this->input->post('price27')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order28($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name28'),
                  'quantity' => $this->input->post('quantity28'),  
                  'price' => $this->input->post('price28')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order29($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name29'),
                  'quantity' => $this->input->post('quantity29'),  
                  'price' => $this->input->post('price29')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order30($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name30'),
                  'quantity' => $this->input->post('quantity30'),  
                  'price' => $this->input->post('price30')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order31($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name31'),
                  'quantity' => $this->input->post('quantity31'),  
                  'price' => $this->input->post('price31')  
            );
            return $this->db->insert('order_detailes', $data);
         }


         function add_detaies_order32($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name32'),
                  'quantity' => $this->input->post('quantity32'),  
                  'price' => $this->input->post('price32')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order33($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name33'),
                  'quantity' => $this->input->post('quantity33'),  
                  'price' => $this->input->post('price33')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order34($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name34'),
                  'quantity' => $this->input->post('quantity34'),  
                  'price' => $this->input->post('price34')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order35($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name35'),
                  'quantity' => $this->input->post('quantity35'),  
                  'price' => $this->input->post('price35')  
            );
            return $this->db->insert('order_detailes', $data);
         }


         function add_detaies_order36($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name36'),
                  'quantity' => $this->input->post('quantity36'),  
                  'price' => $this->input->post('price36')  
            );
            return $this->db->insert('order_detailes', $data);
         }


         function add_detaies_order37($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name37'),
                  'quantity' => $this->input->post('quantity37'),  
                  'price' => $this->input->post('price37')  
            );
            return $this->db->insert('order_detailes', $data);
         }


         function add_detaies_order38($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name38'),
                  'quantity' => $this->input->post('quantity38'),  
                  'price' => $this->input->post('price38')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order39($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name39'),
                  'quantity' => $this->input->post('quantity39'),  
                  'price' => $this->input->post('price39')  
            );
            return $this->db->insert('order_detailes', $data);
         }


         function add_detaies_order40($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name40'),
                  'quantity' => $this->input->post('quantity40'),  
                  'price' => $this->input->post('price40')  
            );
            return $this->db->insert('order_detailes', $data);
         }


         function add_detaies_order41($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name41'),
                  'quantity' => $this->input->post('quantity41'),  
                  'price' => $this->input->post('price41')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order42($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name42'),
                  'quantity' => $this->input->post('quantity42'),  
                  'price' => $this->input->post('price42')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         function add_detaies_order43($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name43'),
                  'quantity' => $this->input->post('quantity43'),  
                  'price' => $this->input->post('price43')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order44($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name44'),
                  'quantity' => $this->input->post('quantity44'),  
                  'price' => $this->input->post('price44')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order45($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name45'),
                  'quantity' => $this->input->post('quantity45'),  
                  'price' => $this->input->post('price45')  
            );
            return $this->db->insert('order_detailes', $data);
         }

          function add_detaies_order46($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name46'),
                  'quantity' => $this->input->post('quantity46'),  
                  'price' => $this->input->post('price46')  
            );
            return $this->db->insert('order_detailes', $data);
         }


          function add_detaies_order47($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name47'),
                  'quantity' => $this->input->post('quantity47'),  
                  'price' => $this->input->post('price47')  
            );
            return $this->db->insert('order_detailes', $data);
         }


          function add_detaies_order48($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name48'),
                  'quantity' => $this->input->post('quantity48'),  
                  'price' => $this->input->post('price48')  
            );
            return $this->db->insert('order_detailes', $data);
         }


          function add_detaies_order49($id){
          
            $data = array(
                  'order_id' => $id,
                  'item_name' => $this->input->post('item_name49'),
                  'quantity' => $this->input->post('quantity49'),  
                  'price' => $this->input->post('price49')  
            );
            return $this->db->insert('order_detailes', $data);
         }

         //  function add_detaies_order50($id){
          
         //    $data = array(
         //          'order_id' => $id,
         //          'item_name' => $this->input->post('item_name50'),
         //          'quantity' => $this->input->post('quantity50'),  
         //          'price9' => $this->input->post('price50')  
         //    );
         //    return $this->db->insert('order_detailes', $data);
         // }














         public function max_order(){  

           $this->db->select_max('id');
           $this->db->from('orders');
           $query = $this->db->get();
           return $query->row()->id;
        }

          public function max_test7(){  

           $this->db->select_max('id');
           $this->db->from('test7');
           $query = $this->db->get();
           return $query->row()->id;
        }


         public function max_test2(){  

           $this->db->select_max('id');
           $this->db->from('test2');
           $query = $this->db->get();
           return $query->row()->id;
        }





 
        function payment($post_image){
            date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $data = array(
                 'user_id' => $this ->session->userdata('user_id'),
                 'username' => $this ->session->userdata('username'),
                 'project' => $this ->session->userdata('project'),
                 'section' => $this ->session->userdata('section'),
                 'payment_amount' => $this->input->post('payment_amount'),
                 'date_amount' =>$d,
                 'time_amount' =>date("h:i:sa"),
                
                 'marsoom_date_years' =>$y,
                 'marsoom_date_month' =>$m,
                 'marsoom_date_day' =>$d,  
                 'path' => $post_image
               
            );

            // Insert user
            return $this->db->insert('payment', $data);
        }



          function add_member($enc_password,$post_image){
             $userid=$this ->session->userdata('user_id');
        // User data array
      $data = array(
         'name' => $this->input->post('name'),
         'email' => $this->input->post('email'),
                 'username' => $this->input->post('username'),
                 'mobile' => $this->input->post('mobile'),
                 'idno' => $this->input->post('idno'),
                 'email' => $this->input->post('email'),
                 // 'username'=>$this->input->post('username'),
                 'password' => $enc_password,
                 'type' =>$this->input->post('type'),
                 'group_id' =>$userid,
                  'path' => $post_image
               
      );

      // Insert user
      return $this->db->insert('users', $data);
        }



         function sendSMS($userAccount, $passAccount, $numbers, $sender, $msg, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
  global $arraySendMsg;
  $url = "http://www.mobily.ws/api/msgSend.php";
  $applicationType = "68";  
  $sender = urlencode($sender);
  $domainName = $_SERVER['SERVER_NAME'];

    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    }
    $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
  $result = curl_exec($ch);

  if($viewResult)
    $result = printStringResult(trim($result) , $arraySendMsg);
  return $result;
}

         function sendSMSWK($userAccount, $passAccount, $numbers, $sender, $msg, $msgKey, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
  global $arraySendMsgWK;
  $url = "https://www.mobily.ws/api/msgSendWK.php";
  $applicationType = "68";
  $sender = urlencode($sender);
  $domainName = $_SERVER['SERVER_NAME'];

    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    }

    $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
  $result = curl_exec($ch);

  if($viewResult)
    $result = printStringResult(trim($result) , $arraySendMsgWK);
  return $result;
}


        
   //       function check_username_exists($username){
            // $query = $this->db->get_where('users', array('username' => $username));
            // if(empty($query->row_array())){
            //  return true;
            // } else {
            //  return false;
            // }
   //      }
        
        // Check email exists
         function check_email_exists($email){
            $query = $this->db->get_where('users', array('email' => $email));
            if(empty($query->row_array())){
                return true;
            } else {
                return false;
            }
        }

     function check_idno_exists($idno){
      $query = $this->db->get_where('users', array('idno' => $idno));
      if(empty($query->row_array())){
        return true;
      } else {
        return false;
      }
    }


    function check_mobile_exists($mobile){
      $query = $this->db->get_where('users', array('mobile' => $mobile));
      if(empty($query->row_array())){
        return true;
      } else {
        return false;
      }
    }

     function check_username_exists($username){
      $query = $this->db->get_where('users', array('username' => $username));
      if(empty($query->row_array())){
        return true;
      } else {
        return false;
      }
    }


        
        
        
          function login($username, $password){
            //validate
            $this->db->where('username', $username);
            $this->db->where('password', $password);
            $result =$this->db->get('users');

            if($result->num_rows() == 1){
                return $result->row(0)->id;

            }else{
                return false;
            }

        }
        
          function customer_update($id,$post_image){
             
           $data = array(
                  'name' =>$this->input->POST('name'),
                  'username' => $this->input->POST('name'),
                  'mobile' => $this->input->POST('mobile'),
                  'job' => $this->input->POST('job'),
                  'email' => $this->input->POST('email'),
                  'titel' => $this->input->POST('titel'),
                  'idno' => $this->input->POST('idno'),
                  'path' => $post_image
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }

       function my_profile_update($post_image){
             $id=$this ->session->userdata('user_id');
           $data = array(
                  'name' =>$this->input->POST('name'),
                  'mobile' => $this->input->POST('mobile'),
                  'email' => $this->input->POST('email'),
                  'path' => $post_image
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }

        function my_password_update($password){
             $id=$this ->session->userdata('user_id');
           $data = array(
                  'password' =>$password,
                 
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }


        function my_targit_update($id){
             
           $data = array(
                  'tragit_day' => $this->input->POST('tragit_day'),
                  'tragit_month' => $this->input->POST('tragit_month'),
                  'path' => $post_image
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }

               function my_items_update($id){
             
           $data = array(
                  'item_name' => $this->input->POST('item_name'),
                  'quantity' => $this->input->POST('quantity'),
                  'price' => $this->input->POST('price')
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('order_detailes', $data);
       }




        function my_user_update(){
             $id=$this->input->POST('d1');
           $data = array(
                  'name' => $this->input->POST('d2')
                  
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }


        function my_user_update555(){

              date_default_timezone_set('Asia/Riyadh');
            $d=date("Y/m/d");
            $m=date("Y/m");
            $y=date("Y");
            $time=date("h:i:s");


               $id=$this->session->userdata('user_id');
           $data = array(
                  'status' => '1',
                   'login_time' =>  $time
   
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }


         function my_user_update666(){
               $id=$this->session->userdata('user_id');
           $data = array(
                  'status' => '0'
                  
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }







         function my_user_update101(){
             $id=$this ->session->userdata('user_id');
           $data = array(
                  'number_of_call' => $this->input->POST('number_of_call'),
                  'date_call' => $this->input->POST('date_call'),
                  'successful_number_call' => $this->input->POST('successful_number_call'),
                  'successful_number_call1' => $this->input->POST('successful_number_call1')
                  
                 
            );
            $this->db->where('id', $id);
            return $this->db->update('users', $data);
       }






        
         function get_customers($id){
        $sql = "select * from users where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

     function get_test7($id){
        $sql = "select * from test7 where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


      function get_orders202($id){
        $sql = "select * from orders where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


      function get_watch_24($id){
         $d=date("Y/m/d");
        $sql = "select * from watch where user_id='$id' and day='$d';";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


      function get_watch_241(){
         $d=date("Y/m/d");
        $sql = "select * from watch where user_id='24' and day='$d';";
        $query = $this->db->query($sql);
        return $query->result_array();
    }






      function get_attachment202($id){
        $sql = "select * from attachment where order_id='$id';";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


     function get_ordersdetailes_202($id){
        $sql = "select * from order_detailes where order_id='$id';";
        $query = $this->db->query($sql);
        return $query->result_array();
    }



    function get_customersss($id){
        $sql = "select * from users where username='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    
     function get_categories6(){   
        $sql =" SELECT * FROM  categories ORDER BY id DESC ;";

        //$sql = "select * from categories;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


     function get_attachment($id){   
        $sql =" SELECT * FROM  attachment where did='$id';";

        //$sql = "select * from categories;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


    
    
      function delete_user($id){         
       
        $this->db->where('id', $id);
        $this->db->delete('users');
        return true;
        }
        
      function delete_action($id){         
        $this->db->where('id', $id);
        $this->db->delete('action');
        return true;
        }


        function delete_attachment($id){         
        $this->db->where('id', $id);
        $this->db->delete('attachment');
        return true;
        }


         function delete_detailes_order($id){         
        $this->db->where('id', $id);
        $this->db->delete('order_detailes');
        return true;
        }



        
        
          public function addcategories(){
            $data = array(
                 'name' => $this->input->post('name'),    
            );
            return $this->db->insert('categories', $data);
        }
        
        
        
    function get_customersid6(){   
        $id='3';
        $sql = "select * from users;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function get_member(){   
        $id='3';
        $userid=$this ->session->userdata('user_id');
        $sql = "select * from users where group_id='$userid';";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    
        
      
      
      function get_categories55($id){
        $sql = "select * from categories where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


      function get_tran55($id){
        $sql = "select * from transactions where did='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


      public function categories_update($id){
             
           $data = array(
                 'name' =>$this->input->POST('name') 
            );
            $this->db->where('id', $id);
            return $this->db->update('categories', $data);
       }


       public function order_update_status($dd){
             
           $data = array(
                 'status' =>'2'
            );
            $this->db->where('id', $dd);
            return $this->db->update('orders', $data);
       }

        public function order_update_status102($dd){
             
           $data = array(
                 'status' =>'3'
            );
            $this->db->where('id', $dd);
            return $this->db->update('orders', $data);
       }

       public function order_update_status101($dd){
             
           $data = array(
                 'status' =>'3'
            );
            $this->db->where('id', $dd);
            return $this->db->update('orders', $data);
       }





        public function note_update($id){
             
           $data = array(
                 'note' =>$this->input->POST('note') 
            );
            $this->db->where('did', $id);
            return $this->db->update('discharge', $data);
       }


       public function attachment($post_image,$id){
           $data = array(
            'title' => $this->input->post('title'),
            'path' => $post_image,
            'did'=> $id,
            
           );

           return $this->db->insert('attachment', $data);
       }


    
    
    function cuntt4(){ 
         date_default_timezone_set('Asia/Riyadh');
         $userid=$this ->session->userdata('user_id');
         $d=date("Y/m/d");
         $array = array('marsoom_date_day' =>$d, 'user_id=' => $userid); 
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }


    //  function cuntt_study(){ 
        
    //      $d="1";
    //      $array = array('status' =>$d); 
    //      $this->db->select_sum('payment_amount');
    //      $this->db->where($array);
    //      $result = $this->db->get('payment')->row();  
    //      return $result->payment_amount;
    // }


 function cuntt_study(){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'evaluation';
        $d="1";
        $array = array('degree1' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function cuntt_study545(){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d="22";
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study54545(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='22';
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function cuntt_study5452(){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d="18";
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study545452(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='18';
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function cuntt_study54519(){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d="19";
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study5454519(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='19';
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function cuntt_study54510(){
        $results = array();
       // $userid=$this ->session->userdata('user_id');
        $table = 'portfolio';
        $d="23";
        $array = array('user_id' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study545451025(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='25';
        $table = 'portfolio';
        
        $array = array('date' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study5454510251r27(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='27';
        $table = 'portfolio';
        
        $array = array( 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


 



function cuntt_study5454510251r29(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='29';
        $table = 'portfolio';
        
        $array = array( 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


   


function cuntt_study5454510251r28(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='31';
        $table = 'portfolio';
        
        $array = array( 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function cuntt_study545451025r27(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='27';
        $table = 'portfolio';
        
        $array = array('date' =>$d,'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


function cuntt_study545451025r28(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='31';
        $table = 'portfolio';
        
        $array = array('date' =>$d,'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


function cuntt_study545451025r29(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='29';
        $table = 'portfolio';
        
        $array = array('date' =>$d,'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }





 // Get all sheets for the Kanban view
public function get_all_salary_sheets_full()
{
    $this->db->from('salary_sheet');
    $this->db->order_by('start_date', 'DESC'); // Newest first
    return $this->db->get()->result_array();
}

// Insert a new sheet (Do NOT update ID 1)
public function insert_salary_sheet($data)
{
    return $this->db->insert('salary_sheet', $data);
}



// PASTE THIS NEW FUNCTION INTO your application/models/hr_model.php

/**
 * Calculates and returns summary statistics for each processed payroll period.
 * Groups data by the payroll period identifier (n13).
 * @return array
 */
public function get_payroll_period_summaries()
{
    // Select fields, including the Sheet Name from salary_sheet table
    $this->db->select('
        pp.n13 as sheet_id,
        ss.type as sheet_name,  
        pp.n14 as company_name,
        COUNT(DISTINCT pp.n1) as employee_count,
        SUM(pp.n6) as total_gross_salary,
        SUM(pp.n11) as total_deductions,
        SUM(pp.n12) as total_net_salary
    ');
    $this->db->from('payroll_process pp');
    // Join to get the name
    $this->db->join('salary_sheet ss', 'ss.id = pp.n13', 'left');
    
    // Group by Sheet ID and Company Name
    $this->db->group_by(['pp.n13', 'pp.n14']); 
    
    $this->db->order_by('pp.n13', 'DESC'); 
    $this->db->order_by('pp.n14', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}



function cuntt_study545451025r(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='26';
        $table = 'portfolio';
        
        $array = array('date' =>$d,'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }





 function cuntt_study5454510251r(){
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("d");
        $results = array();
        $userid='26';
        $table = 'portfolio';
        
        $array = array( 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    private function month_key($ym) {
        // ندخل الأشهر كـ YYYY-MM ونطابق في SQL بـ LEFT(REPLACE(n13,'/','-'),7)
        return str_replace('/', '-', substr($ym, 0, 7));
    }

    public function month_summary($company_code, $ym)
    {
        $ym = $this->month_key($ym);
        $sql = "
            SELECT
                COUNT(DISTINCT n1)                               AS emp_count,
                SUM(COALESCE(n6,0))                              AS total_salary,
                SUM(COALESCE(n7,0))                              AS late_penalties,
                SUM(COALESCE(n8,0))                              AS early_leave_penalties,
                SUM(COALESCE(n9,0))                              AS absences_deductions,
                SUM(COALESCE(n10,0))                             AS social_insurance,
                SUM(COALESCE(n11,0))                             AS total_deductions,
                SUM(COALESCE(n12,0))                             AS net_salary
            FROM payroll_process
            WHERE n14 = ?
              AND LEFT(REPLACE(n13,'/','-'),7) = ?
        ";
        return $this->db->query($sql, [$company_code, $ym])->row_array();
    }

// PASTE THIS NEW FUNCTION INSIDE your application/models/hr_model.php file

/**
 * Saves a batch of processed payroll data for a specific sheet/period.
 * It uses a transaction to ensure data integrity.
 *
 * @param array $payroll_batch The array of employee salary data.
 * @param string $sheet_id The identifier for the current payroll sheet (e.g., "2025/10").
 * @return bool True on success, false on failure.
 */

// PASTE THESE TWO NEW FUNCTIONS INTO your application/models/hr_model.php

/**
 * Fetches distinct payroll periods (e.g., '2025/09') from the payroll_process table.
 * @return array
 */
public function get_distinct_payroll_periods()
{
    $this->db->distinct();
    $this->db->select('n13'); // n13 is the payroll period column
    $this->db->from('payroll_process');
    $this->db->where('n13 IS NOT NULL');
    $this->db->order_by('n13', 'DESC');
    $query = $this->db->get();
    return $query->result_array();
}
// In application/models/hr_model.php

// 1. Function to publish (send) the payroll
public function publish_payroll_sheet($month_key)
{
    // $month_key is in format "YYYY/MM" or "YYYY-MM"
    // We update the payroll_process records for this month to be visible
    // Or simpler: Update the salary_sheet table if you link n13 to it.
    // Since n13 in payroll_process is the sheet ID/Name, we'll update a status flag.
    
    // Assuming we add a status column or use a specific logic. 
    // For this solution, we will use a new table 'published_payrolls' to track visible months
    // to avoid altering large existing tables heavily.
    
    $data = [
        'month_key' => $month_key,
        'published_at' => date('Y-m-d H:i:s'),
        'published_by' => $this->session->userdata('username')
    ];
    
    // Insert or Ignore
    $sql = "INSERT IGNORE INTO published_payrolls (month_key, published_at, published_by) VALUES (?, ?, ?)";
    return $this->db->query($sql, [$month_key, $data['published_at'], $data['published_by']]);
}

// 2. Check if published
public function is_payroll_published($month_key)
{
    // First check exact match
    $this->db->select('month_key');
    $this->db->from('published_payrolls');
    $this->db->where('month_key', $month_key);
    $this->db->limit(1);
    
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return true;
    }
    
    // If no exact match, check if any published payroll matches this month
    // (in case month_key is stored in different format)
    $all_published = $this->db->get('published_payrolls')->result_array();
    
    foreach ($all_published as $row) {
        $normalized = $this->normalize_month_format($row['month_key']);
        if ($normalized === $month_key) {
            return true;
        }
    }
    
    return false;
}
private function normalize_month_format($month_string)
{
    if (empty($month_string)) {
        return date('Y-m');
    }
    
    // Define Arabic months mapping
    $arabic_months = [
        'يناير' => '01', 'فبراير' => '02', 'مارس' => '03', 'أبريل' => '04',
        'مايو' => '05', 'يونيو' => '06', 'يوليو' => '07', 'أغسطس' => '08',
        'سبتمبر' => '09', 'أكتوبر' => '10', 'نوفمبر' => '11', 'ديسمبر' => '12'
    ];
    
    // Clean the string
    $month_string = trim($month_string);
    
    // Check if it's already in YYYY-MM format
    if (preg_match('/^\d{4}-\d{2}$/', $month_string)) {
        return $month_string;
    }
    
    // Try to extract year and month from Arabic format
    foreach ($arabic_months as $arabic => $num) {
        if (strpos($month_string, $arabic) !== false) {
            // Extract year (look for 4-digit number)
            preg_match('/(\d{4})/', $month_string, $year_match);
            $year = $year_match[1] ?? date('Y');
            
            // Also check for 2-digit year
            if (!$year_match) {
                preg_match('/(\d{2})/', $month_string, $year_match2);
                if ($year_match2 && $year_match2[1] > 0) {
                    $year = '20' . $year_match2[1]; // Assuming 2000s
                }
            }
            
            return $year . '-' . $num;
        }
    }
    
    // Try to extract month and year from English month names
    $english_months = [
        'january' => '01', 'february' => '02', 'march' => '03', 'april' => '04',
        'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08',
        'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'
    ];
    
    foreach ($english_months as $english => $num) {
        if (stripos($month_string, $english) !== false) {
            preg_match('/(\d{4})/', $month_string, $year_match);
            $year = $year_match[1] ?? date('Y');
            return $year . '-' . $num;
        }
    }
    
    // If no month found, return current month as fallback
    return date('Y-m');
}
/* ---------------------------------------------------
   PASTE THIS AT THE END OF: application/models/hr_model.php
--------------------------------------------------- */
public function get_overtime_balance($emp_id, $date)
{
    // 1. Get Allotted Working Hours (Default to 8 if not found)
    $this->db->select('working_hours');
    $this->db->where('emp_id', $emp_id);
    $restriction = $this->db->get('work_restrictions')->row();
    $allotted_hours = ($restriction && !empty($restriction->working_hours)) ? (float)$restriction->working_hours : 8.0;

    // 2. Get Actual Punches from attendance_logs
    // We calculate from the FIRST punch to the LAST punch of the day
    $sql = "SELECT MIN(punch_time) as first_punch, MAX(punch_time) as last_punch 
            FROM attendance_logs 
            WHERE emp_code = ? AND DATE(punch_time) = ?";
    
    $query = $this->db->query($sql, array($emp_id, $date));
    $log = $query->row();

    $worked_hours = 0;
    $ot_balance = 0;

    if ($log && $log->first_punch && $log->last_punch && $log->first_punch != $log->last_punch) {
        $start = strtotime($log->first_punch);
        $end   = strtotime($log->last_punch);
        
        // Calculate worked hours (seconds / 3600)
        $worked_hours = round(abs($end - $start) / 3600, 2);

        // Logic: If worked more than allotted, the remainder is Overtime
        if ($worked_hours > $allotted_hours) {
            $ot_balance = $worked_hours - $allotted_hours;
        }
    }

    return [
        'status'   => 'success',
        'worked'   => $worked_hours,
        'allotted' => $allotted_hours,
        'balance'  => round($ot_balance, 2), // This goes into the hours input
        'start'    => $log->first_punch ? date('H:i', strtotime($log->first_punch)) : '-',
        'end'      => $log->last_punch ? date('H:i', strtotime($log->last_punch)) : '-'
    ];
}
public function get_published_payslip_for_month($emp_id, $month)
{
    // Assuming your table structure includes employee_id and month
    $this->db->select('*');
    $this->db->from('published_payrolls');
    $this->db->where('employee_id', $emp_id);
    $this->db->where('month_key', $month); // or whatever your month column is called
    $this->db->order_by('published_at', 'DESC');
    $this->db->limit(1);
    
    $query = $this->db->get();
    return $query->row_array();
}
// Get the latest published month
public function get_latest_published_month()
{
    $this->db->select('month_key, published_at');
    $this->db->from('published_payrolls');
    $this->db->order_by('published_at', 'DESC');
    $this->db->limit(1);
    
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        $row = $query->row();
        
        // Convert any Arabic month format to YYYY-MM
        $month_key = $this->normalize_month_format($row->month_key);
        
        return $month_key;
    }
    return false;
}
// 3. Get Latest Published Slip for Employee
public function get_latest_published_payslip($employee_id)
{
    // Find the latest month present in BOTH payroll_process AND published_payrolls
    $this->db->select('pp.n13 as month_key');
    $this->db->from('payroll_process pp');
    $this->db->join('published_payrolls pub', 'pp.n13 = pub.month_key');
    $this->db->where('pp.n1', $employee_id);
    $this->db->order_by('pp.n13', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();
    
    if ($query->num_rows() == 0) {
        return null;
    }
    
    $month_key = $query->row()->month_key;
    return $this->get_payslip_data($employee_id, $month_key);
}

// 4. Get Data for the PDF
// In hr_model.php

public function get_payslip_data($employee_id, $month_key)
{
    log_message('debug', 'get_payslip_data - Employee: ' . $employee_id . ', Month Key: ' . $month_key);
    
    // Convert month_key to match what's stored in n13
    $search_month = $this->convert_month_for_search($month_key);
    log_message('debug', 'Converted search month: ' . $search_month);
    
    // 1. Get Payroll Calculation Data
    $this->db->where('n1', $employee_id);
    
    // Try different formats for matching
    $this->db->group_start();
    $this->db->where('n13', $search_month); // Exact match with converted format
    $this->db->or_like('n13', $search_month, 'both'); // Partial match
    $this->db->group_end();
    
    $payroll = $this->db->get('payroll_process')->row_array();
    
    log_message('debug', 'Payroll query rows: ' . ($payroll ? '1' : '0'));
    
    if (!$payroll) {
        // Try alternative search if first attempt fails
        $payroll = $this->search_payroll_alternative($employee_id, $month_key);
        if (!$payroll) return null;
    }

    // 2. Get Static Employee Data
    $this->db->select('subscriber_name, profession, n2 as iban, n3 as bank_name, total_salary, base_salary, housing_allowance, n4 as transport_allowance, other_allowances');
    $this->db->where('employee_id', $employee_id);
    $emp = $this->db->get('emp1')->row_array();

    if (!$emp) {
        log_message('debug', 'No employee data found for ID: ' . $employee_id);
        return null;
    }

    // Prepare Result with Casting
    return [
        'month'          => $month_key,
        'emp_name'       => $payroll['n2'] ?? $emp['subscriber_name'], // Use snapshot name or fallback
        'emp_id'         => $payroll['n1'] ?? $employee_id,
        'designation'    => $emp['profession'] ?? '',
        'generated_date' => date('Y-m-d'),
        
        // Earnings
        'basic_salary'   => (float)($emp['base_salary'] ?? 0),
        'housing'        => (float)($emp['housing_allowance'] ?? 0),
        'transport'      => (float)($emp['transport_allowance'] ?? 0),
        // n16 is reparations, n15 is backpay. Ensure they are floats.
        'other_earnings' => (float)($emp['other_allowances'] ?? 0) + (float)($payroll['n16'] ?? 0) + (float)($payroll['n15'] ?? 0),
        'total_earnings' => (float)($emp['total_salary'] ?? 0) + (float)($payroll['n16'] ?? 0) + (float)($payroll['n15'] ?? 0),
        
        // Deductions
        'late_amount'    => (float)($payroll['n7'] ?? 0),
        'early_amount'   => (float)($payroll['n8'] ?? 0),
        'absence_amount' => (float)($payroll['n9'] ?? 0),
        'gosi_amount'    => (float)($payroll['n10'] ?? 0),
        'total_deductions'=> (float)($payroll['n11'] ?? 0),
        
        // Net
        'net_salary'     => (float)($payroll['n12'] ?? 0),
        
        // Bank
        'iban'           => $emp['iban'] ?? '',
        'bank_name'      => $emp['bank_name'] ?? ''
    ];
}

// Helper method to convert month format for search
private function convert_month_for_search($month_key)
{
    // If it's already in Arabic format, return as is
    if (preg_match('/[ء-ي]/u', $month_key)) {
        return $month_key;
    }
    
    // If it's in YYYY-MM format, convert to Arabic month name
    if (preg_match('/^(\d{4})-(\d{2})$/', $month_key, $matches)) {
        $year = $matches[1];
        $month_num = $matches[2];
        
        $arabic_months = [
            '01' => 'يناير', '02' => 'فبراير', '03' => 'مارس', '04' => 'أبريل',
            '05' => 'مايو', '06' => 'يونيو', '07' => 'يوليو', '08' => 'أغسطس',
            '09' => 'سبتمبر', '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر'
        ];
        
        if (isset($arabic_months[$month_num])) {
            return $arabic_months[$month_num] . ' ' . $year;
        }
    }
    
    // Return original if no conversion needed
    return $month_key;
}

// Alternative search method
private function search_payroll_alternative($employee_id, $month_key)
{
    // Try searching with different month formats
    $formats_to_try = [];
    
    // If month_key is YYYY-MM
    if (preg_match('/^(\d{4})-(\d{2})$/', $month_key, $matches)) {
        $year = $matches[1];
        $month_num = $matches[2];
        
        // Try different Arabic month formats
        $arabic_months = [
            '01' => ['يناير', 'كانون الثاني'],
            '02' => ['فبراير', 'شباط'],
            '03' => ['مارس', 'آذار'],
            '04' => ['أبريل', 'نيسان'],
            '05' => ['مايو', 'أيار'],
            '06' => ['يونيو', 'حزيران'],
            '07' => ['يوليو', 'تموز'],
            '08' => ['أغسطس', 'آب'],
            '09' => ['سبتمبر', 'أيلول'],
            '10' => ['أكتوبر', 'تشرين الأول'],
            '11' => ['نوفمبر', 'تشرين الثاني'],
            '12' => ['ديسمبر', 'كانون الأول']
        ];
        
        if (isset($arabic_months[$month_num])) {
            foreach ($arabic_months[$month_num] as $month_name) {
                $formats_to_try[] = $month_name . ' ' . $year;
                $formats_to_try[] = $month_name . $year;
                $formats_to_try[] = 'راتب شهر ' . $month_name . ' ' . $year;
            }
        }
    }
    
    // Try each format
    foreach ($formats_to_try as $format) {
        $this->db->where('n1', $employee_id);
        $this->db->like('n13', $format, 'both');
        $query = $this->db->get('payroll_process');
        
        if ($query->num_rows() > 0) {
            log_message('debug', 'Found payroll data with format: ' . $format);
            return $query->row_array();
        }
    }
    
    // Last resort: get the most recent payroll for this employee
    $this->db->where('n1', $employee_id);
    $this->db->order_by('id', 'DESC'); // Assuming there's an auto-increment ID
    $this->db->limit(1);
    $query = $this->db->get('payroll_process');
    
    if ($query->num_rows() > 0) {
        log_message('debug', 'Found most recent payroll data');
        return $query->row_array();
    }
    
    return null;
}
/**
 * Fetches processed payroll data based on filters.
 * @param array $filters
 * @return array
 */
public function get_processed_payroll_data(array $filters)
{
    $this->db->from('payroll_process');

    // Apply month filter (n13)
    if (!empty($filters['month'])) {
        $this->db->where('n13', $filters['month']);
    }

    // Apply company filter (n14)
    // FIX: Use isset() so that '0' is treated as a valid value, not empty
    if (isset($filters['company_code']) && $filters['company_code'] !== '') {
        $this->db->where('n14', $filters['company_code']);
    }

    // Apply employee search filter
    if (!empty($filters['employee_search'])) {
        $search_term = $filters['employee_search'];
        $this->db->group_start();
        $this->db->like('n1', $search_term); // Employee ID
        $this->db->or_like('n2', $search_term); // Employee Name
        $this->db->or_like('n3', $search_term); // National ID
        $this->db->group_end();
    }

    $this->db->order_by('n2', 'ASC'); 
    $query = $this->db->get();
    return $query->result_array();
}

// In hr_model.php

// In application/models/hr_model.php

public function save_processed_payroll($payroll_batch, $sheet_id)
{
    if (empty($payroll_batch) || empty($sheet_id)) {
        return false;
    }

    // استخراج أكواد الشركات لتنظيف البيانات القديمة
    $company_codes_in_batch = [];
    foreach ($payroll_batch as $record) {
        if (!empty($record['n14'])) {
            $company_codes_in_batch[$record['n14']] = true;
        }
    }
    $unique_company_codes = array_keys($company_codes_in_batch);

    $this->db->trans_start();

    // 1. حذف البيانات القديمة لنفس الشهر لتجنب التكرار
    $this->db->where('n13', $sheet_id); 
    $this->db->group_start(); 
        if (!empty($unique_company_codes)) {
            $this->db->where_in('n14', $unique_company_codes);
        }
        $this->db->or_where('n14 IS NULL', null, false);
        $this->db->or_where('n14', '');
        $this->db->or_where('n14', '0');
    $this->db->group_end();
    $this->db->delete('payroll_process');

    // 2. تجهيز البيانات للإدخال
    $batch_to_insert = [];
    foreach ($payroll_batch as $record) {
        $batch_to_insert[] = [
            // --- بيانات الموظف ---
            'n1'  => $record['n1'], 
            'n2'  => $record['n2'], 
            'n3'  => $record['n3'], 
            'n4'  => $record['n4'], 
            'n5'  => $record['n5'], 
            'n13' => $sheet_id,      
            'n14' => !empty($record['n14']) ? $record['n14'] : '0',

            // --- تفاصيل الراتب (تأكد من تنفيذ الخطوة 1 لإضافة هذه الأعمدة) ---
            'n21' => $record['n21'] ?? 0, // أساسي
            'n22' => $record['n22'] ?? 0, // سكن
            'n23' => $record['n23'] ?? 0, // نقل
            'n24' => $record['n24'] ?? 0, // أخرى
            'n6'  => $record['n6']  ?? 0, // الإجمالي
            
            // --- الاستحقاقات والتسويات ---
            'n20' => $record['n20'] ?? 0, 
            'n15' => $record['n15'] ?? 0, 
            'n16' => $record['n16'] ?? 0, 

            // --- الاستقطاعات ---
            'n9'  => $record['n9']  ?? 0, 
            'n29' => $record['n29'] ?? 0, 
            'n7'  => $record['n7']  ?? 0, 
            'n8'  => $record['n8']  ?? 0, 
            'n17' => $record['n17'] ?? 0, 
            'n18' => $record['n18'] ?? 0, // تمت إضافته لضمان حفظ القيمة (مثل 293.75)
            'n19' => $record['n19'] ?? 0,
            'n10' => $record['n10'] ?? 0, 
            'n11' => $record['n11'] ?? 0, 

            // --- الصافي ---
            'n12' => $record['n12'] ?? 0,
            
            // --- عدادات الحضور ---
            'n26' => $record['n26'] ?? 0,
            'n27' => $record['n27'] ?? 0,
            'n28' => $record['n28'] ?? 0,
            'n30' => 0

        ];
    }

    if (!empty($batch_to_insert)) {
        $this->db->insert_batch('payroll_process', $batch_to_insert);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}


public function get_delegation_list() {
        $this->db->select('
            o.delegation_employee_id, 
            d_emp.subscriber_name as delegate_name, 
            d_emp.profession as delegate_profession,
            d_emp.phone as delegate_phone,
            COUNT(o.id) as delegation_count,
            MAX(o.date) as last_delegation_date
        ');
        $this->db->from('orders_emp o');
        // Join with emp1 to get the delegate's personal info
        $this->db->join('emp1 d_emp', 'o.delegation_employee_id = d_emp.employee_id', 'left');
        
        // Only get records where a delegate actually exists
        $this->db->where('o.delegation_employee_id IS NOT NULL');
        $this->db->where('o.delegation_employee_id !=', '');
        
        $this->db->group_by('o.delegation_employee_id, d_emp.subscriber_name, d_emp.profession, d_emp.phone');
        $this->db->order_by('delegation_count', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Gets the detailed requests assigned to a specific delegate.
     */
    public function get_delegation_details($delegate_id) {
        $this->db->select('
            o.id as order_id,
            o.date as request_date,
            o.type,
            o.vac_start,
            o.vac_end,
            o.status,
            r_emp.employee_id as requestor_id,
            r_emp.subscriber_name as requestor_name,
            r_emp.profession as requestor_profession
        ');
        $this->db->from('orders_emp o');
        // Join with emp1 to get the ORIGINAL requestor's info
        $this->db->join('emp1 r_emp', 'o.emp_id = r_emp.employee_id', 'left');
        
        $this->db->where('o.delegation_employee_id', $delegate_id);
        $this->db->order_by('o.date', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Gets basic info for a single employee (used for the header in details view)
     */
    public function get_delegate_info($delegate_id) {
        $this->db->where('employee_id', $delegate_id);
        return $this->db->get('emp1')->row_array();
    }
// In hr_model.php

/**
 * Helper to find the end date of the most recent payroll period before a given date.
 * @param string $date (Y-m-d)
 * @return string|null (Y-m-d)
 */
public function get_eos_violation_details($employee_id, $last_working_date, $custom_start_date = null)
{
    // 1. Setup Date Range
    try {
        $last_working_dt = new DateTime($last_working_date);
        $end_date_str = $last_working_dt->format('Y-m-d'); 
        
        // --- NEW START DATE LOGIC ---
        if (!empty($custom_start_date)) {
            // If custom date exists, use it exactly as provided
            $start_date_str = date('Y-m-d', strtotime($custom_start_date));
        } else {
            // Default logic: 16th of last month
            $start_dt_calc = clone $last_working_dt;
            $start_date_str = $start_dt_calc->modify('first day of last month')->format('Y-m-16');
            
            // Also check joining date to avoid calculating before they joined
            $emp_join_check = $this->db->select('joining_date')->where('employee_id', $employee_id)->get('emp1')->row();
            if ($emp_join_check && !empty($emp_join_check->joining_date)) {
                $join_dt = new DateTime($emp_join_check->joining_date);
                $default_start_dt = new DateTime($start_date_str);
                if ($join_dt > $default_start_dt) {
                    $start_date_str = $join_dt->format('Y-m-d');
                }
            }
        }
        
        $start_date = new DateTime($start_date_str);
        
        // Extend end date query to catch next-day checkouts perfectly
        $end_date_query = new DateTime($end_date_str . ' +2 days');
        $end_date_query->setTime(5, 59, 59); 
    } catch (Exception $e) {
        return [];
    }

    // 2. Financial Rates
    $emp = $this->get_employee_details($employee_id);
    $total_salary = (float)($emp['total_salary'] ?? 0);
    $daily_rate = $total_salary > 0 ? $total_salary / 30.0 : 0;
    $minute_cost_rate = $daily_rate > 0 ? ($daily_rate / 8.0 / 60.0) : 0;

    // 3. Time Rules & Roles
    $rules = $this->db->get_where('work_restrictions', ['emp_id' => $employee_id])->row();
    $work_hours_for_time_calc = (float)($rules->working_hours ?? 8.0); 
    if ($work_hours_for_time_calc <= 0) $work_hours_for_time_calc = 8.0;
    
    $default_lateness_threshold = $rules->last_punch ?? '08:30:00'; 
    $is_breastfeeding = (isset($rules->working_hours) && (float)$rules->working_hours == 8.0);

    $emp_details = $this->db->select('profession')->where('employee_id', $employee_id)->get('emp1')->row();
    $is_collector = ($emp_details && strpos($emp_details->profession, 'محصل') !== false);

    // 4. Get Requests
    $this->db->from('orders_emp');
    $this->db->where('emp_id', $employee_id);
    $this->db->where('status', '2'); 
    $this->db->group_start();
        $this->db->where("correction_date BETWEEN '$start_date_str' AND '$end_date_str'");
        $this->db->or_where("((vac_start <= '$end_date_str' AND vac_end >= '$start_date_str') OR (vac_half_date BETWEEN '$start_date_str' AND '$end_date_str'))", NULL, FALSE);
    $this->db->group_end();
    $requests = $this->db->get()->result();

    $corrections = [];
    $leaves = [];
    foreach($requests as $req) {
        if($req->type == 2 && !empty($req->correction_date)) $corrections[$req->correction_date] = $req;
        if($req->type == 5) {
            if (!empty($req->vac_start) && !empty($req->vac_end)) {
                $p = new DatePeriod(new DateTime(max($req->vac_start, $start_date_str)), new DateInterval('P1D'), (new DateTime(min($req->vac_end, $end_date_str)))->modify('+1 day'));
                foreach($p as $d) $leaves[$d->format('Y-m-d')] = true;
            }
            if(!empty($req->vac_half_date)) $leaves[$req->vac_half_date] = $req->vac_half_period ?? 'am';
        }
    }

    // 5. Get Holidays & Punches (WITH 6-HOUR OFFSET)
    $holidays = $this->get_holidays_as_flat_array($start_date_str, $end_date_str);
    $tables = $this->_get_attendance_tables();
    $shift_punches = [];
    
    if(!empty($tables)) {
        $unions = [];
        foreach ($tables as $table) {
             if ($this->db->table_exists($table)) {
                 $unions[] = "SELECT punch_time FROM `$table` WHERE emp_code = ".$this->db->escape($employee_id)." AND punch_time BETWEEN '".$start_date->format('Y-m-d H:i:s')."' AND '".$end_date_query->format('Y-m-d H:i:s')."'";
             }
        }
        if(!empty($unions)) {
            $union_sql = implode(" UNION ALL ", $unions);
            // We fetch ALL raw punches instead of MIN/MAX because we need them for Collector split shifts
            $punches = $this->db->query("SELECT punch_time FROM ($union_sql) as u ORDER BY punch_time ASC")->result_array();
            foreach($punches as $p) {
                $ts = strtotime($p['punch_time']);
                $hour = (int)date('H', $ts);
                // The crucial 6-hour night shift offset!
                $shift_date = ($hour < 6) ? date('Y-m-d', strtotime('-1 day', $ts)) : date('Y-m-d', $ts);
                $shift_punches[$shift_date][] = $ts;
            }
        }
    }

    // 6. Get Saturday Assignments
    $this->db->select('saturday_date');
    $this->db->where('employee_id', $employee_id);
    $this->db->where('saturday_date >=', $start_date_str);
    $this->db->where('saturday_date <=', $end_date_str);
    $sat_query = $this->db->get('saturday_work_assignments')->result_array();
    $mandatory_saturdays = array_column($sat_query, 'saturday_date');

    // 7. Iterate and Calculate
    $details = [];
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start_date, $interval, (clone $end_date_query)->modify('+2 days'));

    foreach ($period as $dt) {
        $date_str = $dt->format('Y-m-d');
        
        if ($date_str > $end_date_str) break; 

        $day_num = $dt->format('N'); 
        $is_ramadan = (strtotime($date_str) >= strtotime('2026-02-18') && strtotime($date_str) <= strtotime('2026-03-19'));

        // Exclusions
        if (in_array($date_str, $holidays)) continue;
        if (isset($leaves[$date_str]) && $leaves[$date_str] === true) continue;
        
        $is_mandatory_sat = ($day_num == 6 && in_array($date_str, $mandatory_saturdays));
        if ($day_num == 5 || ($day_num == 6 && !$is_mandatory_sat)) continue;

        // Rules Setup
        $is_half_day = isset($leaves[$date_str]) && $leaves[$date_str] !== true;
        
        if ($is_ramadan) {
            $base_ramadan_hours = $is_breastfeeding ? 5.0 : 6.0;
            $daily_required_hours = $is_half_day ? ($base_ramadan_hours / 2) : $base_ramadan_hours;
            $lateness_threshold_str = null; 
        } else {
            $lateness_threshold_str = $default_lateness_threshold;
            $daily_required_hours = $work_hours_for_time_calc; 

            if ($is_mandatory_sat) {
                $lateness_threshold_str = '13:00:00';
                $daily_required_hours = 6.0;
            }
            if ($is_half_day) {
                $daily_required_hours /= 2;
                if ($leaves[$date_str] === 'am' || $leaves[$date_str] === 'صباحي') {
                    $lateness_threshold_str = '13:00:00';
                }
            }
        }

        // Get Punches for this specific calendar day (already offset properly!)
        $punches_today = $shift_punches[$date_str] ?? [];
        $correction_data = $corrections[$date_str] ?? null;

        if ($correction_data) {
             if(!empty($correction_data->attendance_correction)) $punches_today[] = strtotime("$date_str " . $correction_data->attendance_correction);
             if(!empty($correction_data->correction_of_departure)) {
                 $corr_out = $correction_data->correction_of_departure;
                 if ($is_ramadan && (strpos($corr_out, '00:') === 0 || strpos($corr_out, '01:') === 0 || strpos($corr_out, '02:') === 0 || strpos($corr_out, '03:') === 0 || strpos($corr_out, '04:') === 0 || strpos($corr_out, '05:') === 0)) {
                     $punches_today[] = strtotime(date('Y-m-d', strtotime($date_str . ' +1 day')) . ' ' . $corr_out);
                 } else {
                     $punches_today[] = strtotime("$date_str " . $corr_out);
                 }
             }
        }
        
        $punches_today = array_unique($punches_today);
        sort($punches_today);
        $has_attendance = count($punches_today) > 0;

        // --- VIOLATION LOGIC ---

        // 1. Absence
        if (!$has_attendance) {
             if (!$is_half_day) {
                $details[] = [
                    'date' => $date_str,
                    'day' => $dt->format('l'),
                    'type' => 'absence',
                    'label' => 'غياب',
                    'value' => 1,
                    'cost' => $daily_rate
                ];
             }
             continue;
        }

        // 2. Processing Punches
        $first_punch_ts = null; $last_punch_ts = null; $worked_hours = 0;

        if ($is_ramadan) {
            // RAMADAN LOGIC
            if ($is_collector) {
                $s1_start = strtotime($date_str . ' 13:00:00'); $s1_end = strtotime($date_str . ' 17:00:00');
                $s2_start = strtotime($date_str . ' 20:00:00'); $s2_end = strtotime($date_str . ' +1 day 01:00:00');
                $s1_pts = []; $s2_pts = [];
                
                foreach ($punches_today as $p) {
                    if ($p >= strtotime($date_str . ' 10:00:00') && $p <= strtotime($date_str . ' 18:00:00')) $s1_pts[] = $p;
                    if ($p >= strtotime($date_str . ' 18:00:01') && $p <= strtotime($date_str . ' +1 day 05:00:00')) $s2_pts[] = $p;
                }
                
                $worked_s1 = 0;
                if (count($s1_pts) >= 2) {
                    $worked_s1 = max(0, min(max($s1_pts), $s1_end) - max(min($s1_pts), $s1_start)) / 3600;
                }
                $worked_s2 = 0;
                if (count($s2_pts) >= 2) {
                    $worked_s2 = max(0, min(max($s2_pts), $s2_end) - max(min($s2_pts), $s2_start)) / 3600;
                }
                $worked_hours = min($worked_s1, 2.0) + min($worked_s2, 4.0);
            } else {
                $shift_start = strtotime($date_str . ' 09:00:00');
                $shift_end   = strtotime($date_str . ' +1 day 02:00:00');
                $valid_pts = [];
                foreach ($punches_today as $p) {
                    if ($p >= strtotime($date_str . ' 06:00:00') && $p <= strtotime($date_str . ' +1 day 05:00:00')) $valid_pts[] = $p;
                }
                if (count($valid_pts) >= 2) {
                    $worked_hours = max(0, min(max($valid_pts), $shift_end) - max(min($valid_pts), $shift_start)) / 3600;
                }
            }

            if (count($punches_today) > 1) {
                $shortage_hours = $daily_required_hours - $worked_hours;
                if ($shortage_hours > 0.033) {
                    $shortage_mins = round($shortage_hours * 60);
                    // Ramadan shortage acts as Early/Late
                    $details[] = [
                        'date' => $date_str, 'day' => $dt->format('l'), 'type' => 'early',
                        'label' => 'نقص ساعات الدوام', 'value' => $shortage_mins,
                        'cost' => $shortage_mins * $minute_cost_rate 
                    ];
                }
            }

        } else {
            // NORMAL LOGIC
            foreach ($punches_today as $pt) {
                 $time_str = date('H:i:s', $pt);
                 if ($time_str >= '06:30:00' && $time_str <= '12:59:59') {
                     if (!$first_punch_ts || $pt < $first_punch_ts) $first_punch_ts = $pt;
                 }
                 if ($time_str >= '13:00:00' && $time_str <= '20:59:59') {
                     if (!$last_punch_ts || $pt > $last_punch_ts) $last_punch_ts = $pt;
                 }
            }

            // A. Lateness
            if ($first_punch_ts && $lateness_threshold_str) {
                $in_time_no_seconds = strtotime(date('Y-m-d H:i:00', $first_punch_ts));
                $threshold_ts = strtotime("$date_str $lateness_threshold_str");
                
                if ($in_time_no_seconds > $threshold_ts) {
                    $late_mins = floor(($in_time_no_seconds - $threshold_ts) / 60);
                    if ($late_mins > 0) {
                         $details[] = [
                            'date' => $date_str, 'day' => $dt->format('l'), 'type' => 'late',
                            'label' => 'تأخير (' . date('H:i', $first_punch_ts) . ')', 'value' => $late_mins,
                            'cost' => $late_mins * $minute_cost_rate 
                        ];
                    }
                }
            }

            // B. Early Departure
            if ($first_punch_ts && $last_punch_ts && $first_punch_ts != $last_punch_ts && $daily_required_hours > 0) {
                $in_time_no_seconds = strtotime(date('Y-m-d H:i:00', $first_punch_ts));
                $out_time_no_seconds = strtotime(date('Y-m-d H:i:00', $last_punch_ts));
                $req_seconds = $daily_required_hours * 3600;
                $expected_exit = $in_time_no_seconds + $req_seconds;
                
                if ($out_time_no_seconds < $expected_exit) {
                    $gross_early_mins = floor(($expected_exit - $out_time_no_seconds) / 60);
                    $curr_late_mins = 0;
                    $threshold_ts = strtotime("$date_str $lateness_threshold_str");
                    
                    if ($in_time_no_seconds > $threshold_ts) {
                        $curr_late_mins = floor(($in_time_no_seconds - $threshold_ts) / 60);
                    }
                    $net_early_mins = max(0, $gross_early_mins - $curr_late_mins);

                    if ($net_early_mins > 2) { 
                        $details[] = [
                            'date' => $date_str, 'day' => $dt->format('l'), 'type' => 'early',
                            'label' => 'خروج مبكر (' . date('H:i', $last_punch_ts) . ')', 'value' => $net_early_mins,
                            'cost' => $net_early_mins * $minute_cost_rate
                        ];
                    }
                }
            }
        }
    }

    return $details;
}
private function _get_last_payroll_end_date_before($date) {
    $this->db->select_max('end_date');
    $this->db->from('salary_sheet');
    $this->db->where('end_date <', $date);
    $query = $this->db->get();
    $result = $query->row();
    return $result ? $result->end_date : null;
}
public function get_employee_notes_for_payroll($employee_ids, $start_date, $end_date) {
    if (empty($employee_ids)) {
        return [];
    }

    $notes_map = [];

    // 1. Check for Resignation Requests (Pending or Approved)
    $this->db->select('emp_id, status');
    $this->db->from('orders_emp');
    $this->db->where('type', 1); // Type 1 = Resignation
    $this->db->where_in('emp_id', $employee_ids);
    $this->db->where_in('status', ['0', '1', '2']);
    $resignations = $this->db->get()->result_array();

    foreach ($resignations as $res) {
        $status_text = '';
        switch ($res['status']) {
            case '0':
                $status_text = 'طلب استقالة قيد الانتظار';
                break;
            case '1':
                $status_text = 'طلب استقالة قيد المعالجة';
                break;
            case '2':
                $status_text = 'طلب استقالة معتمد';
                break;
        }
        $notes_map[$res['emp_id']] = $status_text;
    }

    // 2. SIMPLIFIED: Check if employee exists in new_employees table at all
    $this->db->select('employee_id, join_date');
    $this->db->from('new_employees');
    $this->db->where_in('employee_id', $employee_ids);
    $new_employees = $this->db->get()->result_array();

    foreach ($new_employees as $employee) {
        // Only add if no resignation note exists
        if (!isset($notes_map[$employee['employee_id']])) {
            $notes_map[$employee['employee_id']] = 'موظف جديد - تاريخ المباشرة: ' . $employee['join_date'];
        }
    }

    return $notes_map;
}




    // موظفو الشهر الأول غير الموجودين في الشهر الثاني
    public function left_only($company_code, $ym1, $ym2)
    {
        $ym1 = $this->month_key($ym1);
        $ym2 = $this->month_key($ym2);

        $sql = "
            SELECT t1.n1 AS emp_no,
                   MAX(t1.n2) AS emp_name,
                   SUM(COALESCE(t1.n6,0)) AS total_salary
            FROM payroll_process t1
            WHERE t1.n14 = ?
              AND LEFT(REPLACE(t1.n13,'/','-'),7) = ?
              AND t1.n1 NOT IN (
                    SELECT n1 FROM payroll_process
                    WHERE n14 = ? AND LEFT(REPLACE(n13,'/','-'),7) = ?
                    GROUP BY n1
              )
            GROUP BY t1.n1
            ORDER BY emp_name
        ";
        return $this->db->query($sql, [$company_code, $ym1, $company_code, $ym2])->result_array();
    }
// --- END OF SERVICE (EOS) MODEL FUNCTIONS ---

// 1. Fetch Query
private function _get_eos_query()
{
    // Select basic fields + Joined Name/Company + Payment Receipt
    $this->db->select('end_of_service_settlements.*, emp1.subscriber_name, emp1.company_name');
    $this->db->from('end_of_service_settlements');
    $this->db->join('emp1', 'emp1.employee_id = end_of_service_settlements.employee_id', 'left');

    // --- FILTER: Show only Approved Requests ---
    // Adjust 'Approved' to '2' if your database uses numbers for EOS status
    $this->db->group_start();
    $this->db->where('end_of_service_settlements.status', 'Approved'); 
    $this->db->or_where('end_of_service_settlements.status', 2); 
    $this->db->group_end();

    // --- FILTERS (Dashboard) ---
    if (!empty($_POST['filter_company'])) {
        $this->db->where('emp1.company_name', $_POST['filter_company']);
    }
    if (!empty($_POST['filter_emp_name_text'])) {
        $this->db->like('emp1.subscriber_name', $_POST['filter_emp_name_text']);
    }
    if (!empty($_POST['filter_emp_id_text'])) {
        $this->db->where('end_of_service_settlements.employee_id', $_POST['filter_emp_id_text']);
    }
    if (!empty($_POST['filter_date_from'])) {
        $this->db->where('DATE(end_of_service_settlements.created_at) >=', $_POST['filter_date_from']);
    }
    if (!empty($_POST['filter_date_to'])) {
        $this->db->where('DATE(end_of_service_settlements.created_at) <=', $_POST['filter_date_to']);
    }

    // --- SORTING ---
    if (isset($_POST['order'])) {
        $cols = [0=>'id', 1=>'employee_id', 2=>'subscriber_name', 3=>'company_name', 5=>'final_amount', 7=>'payment_status'];
        $idx = $_POST['order']['0']['column'];
        $dir = $_POST['order']['0']['dir'];
        if(isset($cols[$idx])) $this->db->order_by($cols[$idx], $dir);
    } else {
        $this->db->order_by('end_of_service_settlements.id', 'DESC');
    }
}

public function get_eos_datatables() {
    $this->_get_eos_query();
    if (isset($_POST['length']) && $_POST['length'] != -1) {
        $this->db->limit($_POST['length'], $_POST['start']);
    }
    return $this->db->get()->result();
}

public function count_filtered_eos() {
    $this->_get_eos_query();
    return $this->db->count_all_results();
}

public function count_all_eos() {
    $this->db->from('end_of_service_settlements');
    // Keep count consistent with the "Approved" filter
    $this->db->group_start();
    $this->db->where('status', 'Approved'); 
    $this->db->or_where('status', 2); 
    $this->db->group_end();
    return $this->db->count_all_results();
}

// 2. HR Request Action
public function request_eos_payment($id) {
    $this->db->where('id', $id);
    return $this->db->update('end_of_service_settlements', ['payment_status' => 'requested']);
}

// 3. Finance Confirm Action
public function update_eos_payment($id, $date, $file) {
    $data = [
        'payment_status' => 'paid',
        'payment_date'   => $date
    ];
    if($file) {
        $data['payment_receipt'] = $file;
    }
    
    $this->db->where('id', $id);
    return $this->db->update('end_of_service_settlements', $data);
}
    // موظفو الشهر الثاني غير الموجودين في الشهر الأول
    public function right_only($company_code, $ym1, $ym2)
    {
        $ym1 = $this->month_key($ym1);
        $ym2 = $this->month_key($ym2);

        $sql = "
            SELECT t2.n1 AS emp_no,
                   MAX(t2.n2) AS emp_name,
                   SUM(COALESCE(t2.n6,0)) AS total_salary
            FROM payroll_process t2
            WHERE t2.n14 = ?
              AND LEFT(REPLACE(t2.n13,'/','-'),7) = ?
              AND t2.n1 NOT IN (
                    SELECT n1 FROM payroll_process
                    WHERE n14 = ? AND LEFT(REPLACE(n13,'/','-'),7) = ?
                    GROUP BY n1
              )
            GROUP BY t2.n1
            ORDER BY emp_name
        ";
        return $this->db->query($sql, [$company_code, $ym2, $company_code, $ym1])->result_array();
    }

    public function company_name($code)
    {
        if ((string)$code === '1') return 'شركة مرسوم لتحصيل الديون';
        if ((string)$code === '2') return 'مكتب د. صالح الجربوع للمحاماة';
        return 'غير معروف';
    }











     function cuntt_study1001(){
        $results = array();
        $userid=$this ->session->userdata('user_id');
        $table = 'evaluation';
        $d="1";
        $array = array('degree1' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function cuntt_study101(){
        $results = array();
        $table = 'evaluation';
         $d="6";
         $array = array('degree1' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function cuntt_study10001(){
        $results = array();
        $table = 'evaluation';
         $d="6";
         $userid=$this ->session->userdata('user_id');
         $array = array('degree2' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



     function cuntt_study102(){
        $results = array();
        $table = 'evaluation';
         $d="1";
         $array = array('degree3' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study10002(){
        $results = array();
        $table = 'evaluation';
         $d="1";
         $userid=$this ->session->userdata('user_id');
         $array = array('degree3' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



     function cuntt_study103(){
        $results = array();
        $table = 'evaluation';
         $d="1";
         $array = array('degree4' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study104(){
        $results = array();
        $table = 'evaluation';
         $d="1";
         $array = array('degree5' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function cuntt_study10003(){
        $results = array();
        $table = 'evaluation';
         $d="1";
         $userid=$this ->session->userdata('user_id');
         $array = array('degree4' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function cuntt_study10004(){
        $results = array();
        $table = 'evaluation';
         $d="1";
         $userid=$this ->session->userdata('user_id');
         $array = array('degree5' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }




     function cuntt_study_day(){
        $results = array();
        $table = 'orders';
        date_default_timezone_set('Asia/Riyadh');
         
         $d=date("Y/m/d");
         $array = array('date_order' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function cuntt_study_day00(){
        $results = array();
        $table = 'orders';
        date_default_timezone_set('Asia/Riyadh');
        $userid=$this ->session->userdata('user_id');
         
         $d=date("Y/m/d");
         $array = array('date_order' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



    function cuntt_study_month(){
        $results = array();
        $table = 'orders';
        date_default_timezone_set('Asia/Riyadh');
         
         $d=date("Y/m");
         $array = array('date_month' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_study_month00(){
        $results = array();
        $table = 'orders';
        date_default_timezone_set('Asia/Riyadh');
         
         $d=date("Y/m");
          $userid=$this ->session->userdata('user_id');
         $array = array('date_month' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



    function cuntt_study_years(){
        $results = array();
        $table = 'orders';
        date_default_timezone_set('Asia/Riyadh');
         
         $d=date("Y");
         $array = array('date_years' =>$d); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function cuntt_study_years00(){
        $results = array();
        $table = 'orders';
        date_default_timezone_set('Asia/Riyadh');
         
        $d=date("Y");
        $userid=$this ->session->userdata('user_id');
        $array = array('date_years' =>$d, 'user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



     function cuntt_studys_all(){
        $results = array();
        $table = 'orders';
       
        $this->db->select("*");
        $this->db->from($table);
        
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function cuntt_studys_all00(){
        $results = array();
        $table = 'orders';
       
        $userid=$this ->session->userdata('user_id');
        $array = array('user_id=' => $userid); 
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }









 function cuntt555444(){
     date_default_timezone_set('Asia/Riyadh');
         
         $d=date("Y/m/d");
         $array = array('day' =>$d); 


        $results = array();
      
        $table = 'evaluation';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function cuntt5554449998888(){
       // $ss='37';
        $ss=$this ->session->userdata('user_id');
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("Y/m/d");
        $array = array('user_id' =>$ss, 'day=' => $d); 
        //$array = array('user_id' =>$ss); 
        $results = array();
        $table = 'evaluation101';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function cuntt5554449998888111(){
        //$ss='37';
        $ss=$this ->session->userdata('user_id');
        date_default_timezone_set('Asia/Riyadh'); 
        $d=date("Y/m/d");
        $array = array('user_id' =>$ss, 'day=' => $d); 
        //$array = array('user_id' =>$ss); 
        $results = array();
        $table = 'evaluation';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }






     function cuntt4_admin(){ 
         
         date_default_timezone_set('Asia/Riyadh');
         
         $d=date("Y/m/d");
         $array = array('day' =>$d); 
         $this->db->select_sum('evaluation');
         $this->db->where($array);
         $result = $this->db->get('evaluation')->row();  
         return $result->payment_amount;
    }


    function cuntt4_admin_month(){ 
         
         $m=date("Y/m");
         $array = array('marsoom_date_month' =>$m); 
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }


    function cuntt4_admin_month_project(){ 
         
         $m=date("Y/m");
         $array = array('marsoom_date_month' =>$m); 
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }






    function cuntt4_admin_all(){ 
         
        // $d=date("Y/m/d");
        // $array = array('marsoom_date_day' =>$d); 
         $this->db->select_sum('payment_amount');
        // $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }

    function cuntt4_admin_all_project(){ 
         
        // $d=date("Y/m/d");
        // $array = array('marsoom_date_day' =>$d); 
         $this->db->select_sum('payment_amount');
        // $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }




     function cuntt4_sub(){ 
         
         date_default_timezone_set('Asia/Riyadh');
         $project=$this ->session->userdata('project');
         $section=$this ->session->userdata('section');
         $d=date("Y/m/d");
         $array = array('marsoom_date_day' =>$d, 'project=' => $project, 'section=' => $section); 
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }




    function cuntt4_month(){ 
         date_default_timezone_set('Asia/Riyadh');
         $userid=$this ->session->userdata('user_id');
         $m=date("Y/m");
         $array = array('marsoom_date_month' =>$m, 'user_id=' => $userid); 
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }
 

    function cuntt4_max(){     
        $sql="SELECT user_id, SUM(payment_amount) FROM payment GROUP BY user_id";
         $this->db->select_max($sql);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;     
    }

 






   //  public function getPeriodeNummer($bedrijf_id) {
   //  $this->db->select_max('id');
   //  $this->db->where('bedrijf_id', $bedrijf_id);
   //  $result = $this->db->get('rapporten');

   //  $this->db->select('periode_nummer');
   //  $this->db->where('rapporten_id', $result);
   //  $query = $this->db->get('statistieken_onderhoud');

   //  $data = $query + 1;

   //  return $data;
   // }



     function cuntt4_month_sub(){ 
        date_default_timezone_set('Asia/Riyadh');
         $project=$this ->session->userdata('project');
         $section=$this ->session->userdata('section');
        
         $m=date("Y/m");
         $array = array('marsoom_date_month' =>$m, 'project=' => $project, 'section=' => $section); 
        
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }


     function cuntt4_month_admin(){ 
        
         date_default_timezone_set('Asia/Riyadh');
         $m=date("Y/m");
         $array = array('marsoom_date_month' =>$m); 
        
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }




    function cuntt4_years(){ 
         date_default_timezone_set('Asia/Riyadh');
         $userid=$this ->session->userdata('user_id');
         $Y=date("Y");
         $array = array('marsoom_date_years' =>$Y, 'user_id=' => $userid); 
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }

    function cuntt4_years_sub(){ 
         date_default_timezone_set('Asia/Riyadh');
         $project=$this ->session->userdata('project');
         $section=$this ->session->userdata('section');
         
         $Y=date("Y");
         $array = array('marsoom_date_years' =>$Y, 'project=' => $project, 'section=' => $section); 
         
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }


     function cuntt4_years_admin(){ 
         
         date_default_timezone_set('Asia/Riyadh');
         $Y=date("Y");
         $array = array('marsoom_date_years' =>$Y); 
         
         $this->db->select_sum('payment_amount');
         $this->db->where($array);
         $result = $this->db->get('payment')->row();  
         return $result->payment_amount;
    }




    function cuntt444(){
        $results = array();
        $array = array('type' => '1');
        $table = 'archives';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function cuntt555(){
        $results = array();
        $array = array('type' => '2');
        $table = 'archives';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function cuntt666(){
        $results = array();
        $array = array('type' => '3');
        $table = 'archives';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function cuntt777(){
        $results = array();
        $array = array('type' => '4');
        $table = 'archives';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }






    function dealsin_numbers(){
        $results = array();
        $array = array('modest' => 'واردة');
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function dealsin_numbers22(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => 'admin2');
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function dealsin_numbers22_ar(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function get_cc_number(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('username' => $id);
        $table = 'cc';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



     function dealsin_numbers22_ar1(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => "ali");
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function dealsin_numbers22_ar2(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => "amlak");
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function dealsin_numbers22_ar3(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => "ahmed");
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function dealsin_numbers22_ar4(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => "faisal");
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function dealsin_numbers22_ar5(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => "abdula");
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function dealsin_numbers22_ar6(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => "mohammed");
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsin_numbers22_ar7(){
       $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture' => "saeed");
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }










     function dealsin_numbers82(){
       $id='mtka1960';
        $results = array();
    $array = array('modest' => 'واردة', 'useridfuture' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsin_numbers99(){
       $id='ali';
        $results = array();
    $array = array('modest' => 'واردة', 'useridfuture' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsin_numbers100(){
       $id='ahmed';
        $results = array();
    $array = array('modest' => 'واردة', 'useridfuture' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsin_numbers101(){
       $id='faisal';
        $results = array();
    $array = array('modest' => 'واردة', 'useridfuture' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsin_numbers102(){
       $id='abdula';
        $results = array();
    $array = array('modest' => 'واردة', 'useridfuture' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsin_numbers103(){
       $id='mohammed';
        $results = array();
    $array = array('modest' => 'واردة', 'useridfuture' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

      function dealsin_numbers104(){
       $id='saeed';
        $results = array();
    $array = array('modest' => 'واردة', 'useridfuture' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }



    function dealsin_numbers2(){
        $id=$this->session->userdata('username');
        $results = array();
        $array = array('modest' => 'واردة', 'useridfuture=' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


      function descharge_numbers2(){
       // $id=$this->session->userdata('username');
        $results = array();
       // $array = array('modest' => 'واردة', 'useridfuture=' => $id);
        $table = 'discharge';
        $this->db->select("*");
      //  $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function descharge_numbers3(){
       // $id=$this->session->userdata('username');
        $results = array();
        $array = array('mode' => 'دفترية');
        $table = 'discharge';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function descharge_numbers4(){
       // $id=$this->session->userdata('username');
        $results = array();
        $array = array('mode' => 'غير الدفترية');
        $table = 'discharge';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function descharge_numbers5(){
       // $id=$this->session->userdata('username');
        $results = array();
        $array = array('mode' => 'آخرى');
        $table = 'discharge';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }




    function dealsout_numbers(){
        $results = array();
        $array = array('modest' => 'صادرة', 'dtype!=' => 'سري');
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function dealsout_numbers22(){
        $id='105';
        $results = array();
        $array = array('modest' => 'واردة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function dealsout_numbers82(){
        $id='82';
        $results = array();
        $array = array('modest' => 'صادرة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsout_numbers99(){
        $id='99';
        $results = array();
        $array = array('modest' => 'صادرة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


     function dealsout_numbers100(){
        $id='100';
        $results = array();
        $array = array('modest' => 'صادرة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsout_numbers101(){
        $id='101';
        $results = array();
        $array = array('modest' => 'صادرة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsout_numbers102(){
        $id='102';
        $results = array();
        $array = array('modest' => 'صادرة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsout_numbers103(){
        $id='103';
        $results = array();
        $array = array('modest' => 'صادرة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

     function dealsout_numbers104(){
        $id='104';
        $results = array();
        $array = array('modest' => 'صادرة', 'useridsend' => $id);
        $table = 'transactions_view';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }




    function dealsout_numbers2(){
      $id=$this->session->userdata('username');
        $results = array();
    $array = array('modest' => 'صادرة', 'useridfuture=' => $id);
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    function dealsvip_numbers(){
        $results = array();
//      $array = array('modest' => 'صادرة', 'dtype!=' => 'سري');
        $table = 'transactions';
        $this->db->select("*");
        $this->db->where('dtype','سري');
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }

    function dealsvip_numbers22(){
       $id=$this->session->userdata('username');
        $results = array();
         $array = array('dtype' => 'سري', 'useridfuture=' => $id);
//    $array = array('modest' => 'صادرة', 'dtype!=' => 'سري');
        $table = 'transactions';
        $this->db->select("*");
          
     //   $this->db->where('dtype','سري' ,'useridfuture=' => $id);
         $this->db->where($array);
        $this->db->from($table);
        $query = $this->db->get();
        $num_of_records = $query->num_rows();
        return $num_of_records;
    }


    /*function cuntt44(){
       $sql="SELECT COUNT(id) as num FROM transactions WHERE modest='واردة' AND dtype!='سري'; ";
        $query=$this->db->query($sql);
        $data=$query->row_array();
        
        return $data['num'];
    }*/
    
    public function delete_categories($id){         
       
        $this->db->where('id', $id);
        $this->db->delete('categories');
        return true;
        }
        
        
          public function addimportance(){
            $data = array(
                 'name' => $this->input->post('name'),    
            );
            return $this->db->insert('importance', $data);
        }
        
            public function addaction(){
            $data = array(
                 'name' => $this->input->post('name'), 
                 'color' => $this->input->post('color')    
            );
            return $this->db->insert('action', $data);
        }
        
        
        
         
        
                // Check username exists
     
        
        // Check email exists
     
       
    function get_importance6(){   
        $id='3';
        $sql = "select * from importance;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
        function get_action6(){   
        $id='3';
        $sql = "select * from action;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
    function get_importance55($id){
        $sql = "select * from importance where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    
    function get_action55($id){
        $sql = "select * from action where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    
    
    
    function get_relatedpeople6(){   
        $id='3';
        $sql = "select * from relatedpeople;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
     public function addrelatedpeople(){
            $data = array(
                 'name' => $this->input->post('name'),
                 'mobile' => $this->input->post('mobile'), 
                 'email' => $this->input->post('email'),
                 'transaction' => $this->input->post('transaction'),
                 'office' => $this->input->post('office')
            );
            return $this->db->insert('relatedpeople', $data);
        }
        
    
    
    
      function get_relatedpeople55($id){
        $sql = "select * from relatedpeople where id='$id';";
        $query = $this->db->query($sql);
        return $query->row_array();
    }
    
    
     public function relatedpeople_update($id){
             
           $data = array(
                 'name' =>$this->input->POST('name'),
                 'mobile' =>$this->input->POST('mobile'),
                 'email' =>$this->input->POST('email'), 
                 'transaction' =>$this->input->POST('transaction'),
                 'office' =>$this->input->POST('office')

            );
            $this->db->where('id', $id);
            return $this->db->update('relatedpeople', $data);
         }
         
            public function delete_relatedpeople($id){         
       
        $this->db->where('id', $id);
        $this->db->delete('relatedpeople');
        return true;
        }
    
      public function importance_update($id){
             
           $data = array(
                 'name' =>$this->input->POST('name') 
            );
            $this->db->where('id', $id);
            return $this->db->update('importance', $data);
         }
         
           public function action_update($id){
             
           $data = array(
                 'name' =>$this->input->POST('name') 
            );
            $this->db->where('id', $id);
            return $this->db->update('action', $data);
         }
         
    public function curlTest()
    {
      $testValue = 0;
      if(function_exists("curl_init"))
        ++$testValue;
      if(function_exists("curl_setopt"))
        ++$testValue;
      if(function_exists("curl_exec"))
        ++$testValue;
      if(function_exists("curl_close"))
        ++$testValue;
      if(function_exists("curl_errno"))
        ++$testValue;
      return $testValue;
    }
     
     public function sendMsgSMS($userAccount, $passAccount, $numbers, $sender, $msg, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
     {
  $applicationType = "68";
  $sender = urlencode($sender);
  $domainName = $_SERVER['SERVER_NAME'];
    if(!empty($userAccount) && empty($passAccount)) {
        $stringToPost = "apiKey=".$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    } else {
        $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
    }
  $stringToPostLength = strlen($stringToPost);
  $fsockParameter = "POST /api/msgSend.php HTTP/1.0\r\n";
  $fsockParameter.= "Host: www.mobily.ws \r\n";
  $fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
  $fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
  $fsockParameter.= "$stringToPost";

  $fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 10);
  fputs($fsockConn, $fsockParameter);
    
  $result = ""; 
  $clearResult = false; 
  
  while(!feof($fsockConn))
  {
    $line = fgets($fsockConn, 10240);
    if($line == "\r\n" && !$clearResult)
    $clearResult = true;
    
    if($clearResult)
      $result .= trim($line); 
  }
  return $result;
}
    }
    
    ?>