<?php
class Ramadan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }
        $this->load->model('Ramadan_model');
        $this->load->model('hr_model');
    }

    // 1. Ramadan Attendance Tracker
   // 1. Ramadan Attendance Tracker
    // 1. Ramadan Attendance Tracker
    public function m44_hr_ramadan() {
        // Get the salary sheet ID from the URL
        $id = $this->uri->segment(3, 0);
        $data['id'] = $id;

        // Get the salary sheet details (start/end dates)
        $data['get_salary_sheet'] = $this->hr_model->get_salary_sheet($id);

        if (empty($data['get_salary_sheet'])) {
            show_error('Salary sheet not found.', 404);
        }

        // Get the list of employees for the report
        $employees = $this->hr_model->get_employees();
        $data['employees'] = $employees;
        
        // If there are no employees, stop here to avoid errors
        if (empty($employees)) {
            $this->load->view('template/new_header_and_sidebar', $data ?? []);
            $this->load->view('ramadan/m44_hr_ramadan', $data);
            $this->load->view('template/new_footer');
            return;
        }

        // Get Saturday assignments in database
        $start_date = $data['get_salary_sheet']['start_date'];
        $end_date = $data['get_salary_sheet']['end_date'];
        
        $this->db->select('*');
        $this->db->from('saturday_work_assignments');
        $this->db->where('saturday_date >=', $start_date);
        $this->db->where('saturday_date <=', $end_date);
        $saturday_assignments = $this->db->get()->result_array();

        // 1. Create a simple array of just the employee IDs for efficient querying
        $employee_ids = array_map(function($emp) {
            return $emp->employee_id;
        }, $employees);

        // 2. Pre-load ALL related data in single, efficient queries
        
        // Initialize data_map
        $data_map = [
            'vacations'        => [],
            'corrections'      => [],
            'rules'            => [],
            'leave_requests'   => [],
            'fp_corrections'   => [],
            'mandates'         => [], 
            'public_holidays'  => [],
            'saturday_assignments' => $saturday_assignments,
            'mandate_requests' => []
        ];

        // Get all approved leave requests from orders_emp table
        $this->db->select('emp_id, vac_start, vac_end, vac_main_type, status');
        $this->db->from('orders_emp');
        $this->db->where_in('emp_id', $employee_ids);
        $this->db->where('type', 5); // Type 5 = Leave requests
        $this->db->where('status', '2'); // Status 2 = Approved
        $all_leave_requests = $this->db->get()->result_array();

        // FIX: Get complete fingerprint correction data with time fields
        $this->db->select('emp_id, correction_date, attendance_correction, correction_of_departure, type, status');
        $this->db->from('orders_emp');
        $this->db->where_in('emp_id', $employee_ids);
        $this->db->where('type', 2); // Type 2 = Fingerprint corrections
        $this->db->where('status', '2'); // Status 2 = Approved
        $all_fp_corrections = $this->db->get()->result_array();

        // Get all approved mandate requests (business trips) in ONE query
        $this->db->select('emp_id, start_date, end_date, status');
        $this->db->from('mandate_requests');
        $this->db->where_in('emp_id', $employee_ids);
        $this->db->where('status', 'Approved'); // Status = 'Approved'
        $all_mandate_requests = $this->db->get()->result_array();

        // Get all work restrictions for these employees in ONE query
        $this->db->select('*');
        $this->db->from('work_restrictions');
        $this->db->where_in('emp_id', $employee_ids);
        $all_rules = $this->db->get()->result();

        // GET PUBLIC HOLIDAYS
        $holiday_dates = [];
        try {
            $this->db->select('holiday_name, holiday_date, start_date, end_date');
            $this->db->from('public_holidays');
            $public_holidays_result = $this->db->get();
            if ($public_holidays_result && $public_holidays_result->num_rows() > 0) {
                $all_holidays = $public_holidays_result->result_array();
                foreach ($all_holidays as $holiday) {
                    if (!empty($holiday['holiday_date'])) {
                        $holiday_date = $holiday['holiday_date'];
                        if ($holiday_date >= $start_date && $holiday_date <= $end_date) {
                            $holiday_dates[] = $holiday_date;
                        }
                    }
                    if (!empty($holiday['start_date']) && !empty($holiday['end_date'])) {
                         $overlap_start = max(strtotime($start_date), strtotime($holiday['start_date']));
                         $overlap_end = min(strtotime($end_date), strtotime($holiday['end_date']));
                         if ($overlap_start <= $overlap_end) {
                             $current_date = $overlap_start;
                             while ($current_date <= $overlap_end) {
                                 $holiday_dates[] = date('Y-m-d', $current_date);
                                 $current_date = strtotime('+1 day', $current_date);
                             }
                         }
                    }
                }
                $data_map['public_holidays'] = array_unique($holiday_dates);
            }
        } catch (Exception $e) {
            $data_map['public_holidays'] = [];
        }

        // 3. Organize the pre-loaded data into a fast "lookup map"
        foreach ($all_leave_requests as $leave) {
            $data_map['leave_requests'][$leave['emp_id']][] = $leave;
        }

        foreach ($all_fp_corrections as $fp_corr) {
            $data_map['fp_corrections'][$fp_corr['emp_id']][] = $fp_corr;
        }
        
        $data_map['mandate_requests'] = [];
        foreach ($all_mandate_requests as $mandate) {
            $data_map['mandate_requests'][$mandate['emp_id']][] = $mandate;
        }

        foreach ($all_rules as $rule) {
            $data_map['rules'][$rule->emp_id] = $rule;
        }
        
        $data_map['exemptions'] = $this->hr_model->get_exemptions_for_employees($employee_ids);
        $data['new_year_holiday_data'] = $this->hr_model->get_new_year_holiday_data($employee_ids);
        
        // 4. Pass all the data to the view
        // =========================================================
        // RAMADAN FIX: FETCH RAW PUNCHES TO SUPPORT SPLIT SHIFTS (4 PUNCHES/DAY)
        // =========================================================
        $this->db->select('emp_code, punch_time');
        $this->db->from('attendance_logs');
        $this->db->where('punch_time >=', $start_date . ' 00:00:00');
        // Add 2 days to ensure we catch the cross-midnight checkouts for the final day
        $this->db->where('punch_time <=', date('Y-m-d', strtotime($end_date . ' +2 days')) . ' 05:00:00');
        $raw_punches = $this->db->get()->result();

        $formatted_attendance = [];
        foreach ($raw_punches as $p) {
            $obj = new stdClass();
            $obj->emp_code = $p->emp_code;
            $obj->punch_date = date('Y-m-d', strtotime($p->punch_time));
            // We map both variables to the raw time. The view will automatically deduplicate and sort them!
            $obj->first_punch = $p->punch_time;
            $obj->last_punch = $p->punch_time; 
            $formatted_attendance[] = $obj;
        }
        $data['attendance_data'] = $formatted_attendance;
        // =========================================================
        $data['data_map'] = $data_map;
        
        // === THIS LINE IS THE ONLY DIFFERENCE! IT LOADS THE RAMADAN VIEW INSTEAD OF THE STANDARD ONE ===

          $this->load->view('template/new_header_and_sidebar', $data ?? []);
          $this->load->view('ramadan/m44_hr_ramadan', $data);
          $this->load->view('template/new_footer');
    }
    // --- Handle Bulk Action (Approve/Reject Multiple) ---
    public function bulk_action_remote_request() {
        $request_ids = $this->input->post('request_ids'); // Array of IDs
        $action = $this->input->post('action'); // 'approve' or 'reject'
        $manager_id = $this->session->userdata('username');

        if (empty($request_ids) || !is_array($request_ids)) {
            echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد أي طلبات.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // Check if user is HR
        $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
        $is_hr = in_array($manager_id, $hr_users);

        $success_count = 0;
        $error_count = 0;

        // Loop through each ID and process it using our existing robust model function
        foreach ($request_ids as $id) {
            $result = $this->Ramadan_model->process_remote_approval($id, $action, $manager_id, $is_hr);
            if ($result['status'] == 'success') {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        $msg = "تم تنفيذ الإجراء بنجاح لـ $success_count طلبات.";
        if ($error_count > 0) {
            $msg .= " وفشل الإجراء لـ $error_count طلبات (قد تكون معالجة مسبقاً).";
        }

        echo json_encode([
            'status' => 'success', 
            'message' => $msg, 
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
    // --- Submit Remote Request ---
    // --- Load the Dynamic View ---
    public function remote_work() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');

        $emp_id = $this->session->userdata('username');
        $hr_users = ['2774', '2784', '2515', '2230', '1835', '2901'];
        
        $data['is_hr'] = in_array($emp_id, $hr_users);
        $data['is_manager'] = $this->Ramadan_model->is_manager($emp_id);
        $data['current_user'] = $emp_id;

        $data['requests'] = $this->Ramadan_model->get_remote_requests($emp_id, $data['is_hr']);

        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('ramadan/remote_work_view', $data ?? []);
        $this->load->view('template/new_footer');

    }

    // --- Submit New Request ---
    // --- Submit New Request ---
    // --- Submit New Request ---
    public function submit_remote_request() {
        $emp_id = $this->session->userdata('username');
        $emp_name = $this->session->userdata('name');
        
        $date = $this->input->post('request_date');
        $start_time = $this->input->post('start_time');
        $end_time = $this->input->post('end_time');

        // 1. Validate Date Range
        if ($date < '2026-02-18' || $date > '2026-03-17') {
            echo json_encode(['status' => 'error', 'message' => 'لا يمكنك تقديم طلبات إلا للفترة من 18/02/2026 حتى 17/03/2026.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // 2. Verify maximum duration is 2 hours (Across Midnight Support)
        $start_ts = strtotime($start_time);
        $end_ts = strtotime($end_time);
        
        // If end time is before start time, add 24 hours (86400 seconds) to the end time
        if ($end_ts < $start_ts) {
            $end_ts += 86400;
        }

        if (($end_ts - $start_ts) > 7200) { // 7200 seconds = 2 hours
            echo json_encode(['status' => 'error', 'message' => 'لا يمكن أن تتجاوز مدة العمل عن بعد ساعتين.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $manager_id = $this->Ramadan_model->get_direct_manager($emp_id);

        $this->db->insert('remote_work_requests', [
            'emp_id' => $emp_id, 'emp_name' => $emp_name,
            'request_date' => $date, 'start_time' => $start_time,
            'end_time' => $end_time, 'manager_id' => $manager_id,
            'status' => 0
        ]);

        echo json_encode(['status' => 'success', 'message' => 'تم إرسال الطلب لمديرك المباشر بنجاح.', 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    // --- Handle Action (Approve/Reject) ---
    public function action_remote_request() {
        $request_id = $this->input->post('request_id');
        $action = $this->input->post('action'); // 'approve' or 'reject'
        $manager_id = $this->session->userdata('username');

        // Check if user is HR
        $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
        $is_hr = in_array($manager_id, $hr_users);

        $result = $this->Ramadan_model->process_remote_approval($request_id, $action, $manager_id, $is_hr);
        $result['csrf_hash'] = $this->security->get_csrf_hash();
        echo json_encode($result);
    }

    // --- Manager Approves Request ---
    public function approve_remote($request_id) {
        $success = $this->Ramadan_model->approve_remote_request($request_id);
        if ($success) {
            $this->session->set_flashdata('success', 'تم الاعتماد وتسجيل البصمات في النظام بنجاح.');
        } else {
            $this->session->set_flashdata('error', 'حدث خطأ أو تم اعتماد الطلب مسبقاً.');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    // 2. Fetch Ramadan Attendance AJAX 
    public function fetch_ramadan_attendance() {
        $start_date = $this->input->post('start_date') ?: '2026-02-18';
        $end_date = $this->input->post('end_date') ?: '2026-03-19';
        
        $data = $this->Ramadan_model->get_ramadan_attendance_data($start_date, $end_date);
        
        echo json_encode([
            'status' => 'success',
            'data' => $data,
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    // 3. Ramadan Payroll Engine
    function payroll_view101_ramadan(){ 
    if(!$this->session->userdata('logged_in')){
        redirect('users/login');
    } else { 
        if($this->session->userdata('type') == 10){
            redirect('users/login');
        } else {
            $username = $this->session->userdata('username');
            $allowed_users = array('1835', '2230', '2515', '2774', '2784','2901');

            if(in_array($username, $allowed_users)){
                $sheet_id = $this->uri->segment(3,0);
                if (empty($sheet_id)) {
                    show_error('Sheet ID is missing.', 400);
                    return;
                }

                $data['id'] = $sheet_id;
$data['dynamic_additions_headers'] = $this->hr_model->get_unique_reparation_types($sheet_id);
$data['dynamic_deductions_headers'] = $this->hr_model->get_unique_discount_types($sheet_id);

$data['dynamic_additions_data'] = $this->hr_model->get_reparations_detailed_map($sheet_id);
$data['dynamic_deductions_data'] = $this->hr_model->get_discounts_detailed_map($sheet_id);
                // 1. Get base employee and sheet data
                $data['get_salary_sheet'] = $this->hr_model->get_salary_sheet($sheet_id);
                $sheet_start_date = $data['get_salary_sheet']['start_date'];
                $sheet_end_date   = $data['get_salary_sheet']['end_date'];
                $employees = $this->hr_model->get_active_employees();
                $data['employees'] = $employees;

                if (!empty($employees)) {
                    $employee_ids = array_map(function($emp) { return $emp->employee_id; }, $employees);

                    // DEBUG: Check if we have employee IDs
                    echo "<!-- DEBUG: Employee IDs: " . implode(', ', $employee_ids) . " -->";
                    
                    // 2. Pre-load all related data
                    $attendance_map = $this->hr_model->get_attendance_summary_for_employees($employee_ids, $sheet_id);
                    
                    // DEBUG: Check attendance summary data
                   foreach($attendance_map as $emp_id => $summary) {
    echo "<!-- DEBUG RAW DATA: Emp $emp_id - Late: {$summary->minutes_late}, Early: {$summary->minutes_early}, Single: {$summary->single_thing} -->";
}
$data['attendance_map'] = $attendance_map;
                    
                    $data['attendance_map'] = $attendance_map;
                    $data['discounts_map'] = $this->hr_model->get_discounts_for_employees($employee_ids, $sheet_id, $sheet_start_date, $sheet_end_date);
                    $data['half_day_vacations_map'] = $this->hr_model->get_half_day_vacations_for_employees($employee_ids, $sheet_start_date, $sheet_end_date);
                    $prev_sheet_id = $sheet_id - 1; // Assumes sheet IDs are sequential integers (e.g., 1, 2, 3)
                $data['prev_attendance_map'] = $this->hr_model->get_attendance_summary_for_employees($employee_ids, $prev_sheet_id);
                    $data['reparations_map'] = $this->hr_model->get_reparations_for_employees($employee_ids, $sheet_id, $sheet_start_date, $sheet_end_date);
                    $data['new_employee_map'] = $this->hr_model->get_new_employee_data_for_employees($employee_ids);
                    $data['insurance_map'] = $this->hr_model->get_insurance_discounts_for_employees($employee_ids);
                    $data['stopped_salary_map'] = $this->hr_model->get_stopped_salaries_for_employees($employee_ids, $sheet_id);
                    $data['exemption_map'] = $this->hr_model->get_exemptions_for_employees($employee_ids);  
                    $sheet_end_dt_obj = new DateTime($sheet_end_date);
$calendar_month_start = $sheet_end_dt_obj->format('Y-m-01');
$calendar_month_end = $sheet_end_dt_obj->format('Y-m-t');

$data['unpaid_leave_map'] = $this->hr_model->get_unpaid_leave_days_for_employees($employee_ids, $calendar_month_start, $calendar_month_end);
                    $data['notes_map'] = $this->hr_model->get_employee_notes_for_payroll($employee_ids, $sheet_start_date, $sheet_end_date);

                } else {
                    $data['attendance_map'] = [];
                    $data['discounts_map'] = [];
                    $data['reparations_map'] = [];
                    $data['new_employee_map'] = [];
                    $data['insurance_map'] = [];
                    $data['stopped_salary_map'] = [];
                    $data['exemption_map'] = [];
                    $data['notes_map'] = [];
                } 
             
                $this->load->view('template/new_header_and_sidebar', $data ?? []);
                $this->load->view('ramadan/payroll_view101_ramadan', $data);
                $this->load->view('template/new_footer');

            } else {
                redirect('users/login');
            }
        }
    }
}
}