<?php
    class Users1 extends CI_Controller{
        public function __construct()
        {
            parent::__construct();

        date_default_timezone_set('Asia/Riyadh');
        $this->load->database();
        $this->load->helper(['url','html','form']);
        $this->load->library('session');


        }
// In Users1.php

public function export_all_orders()
{
    // Security check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    $this->load->dbutil();
    $this->load->helper('download');

    // Determine user role (same logic as in fetch_orders)
    $logged_in_user_id = $this->session->userdata('username');
    $hr_users = ['2230', '2515', '2774', '2784', '1835','2901'];
    $is_hr_user = in_array($logged_in_user_id, $hr_users);

    // This is a clever way to simulate the $_POST array from a GET request for the model
    $_POST['filter_request_id'] = $this->input->get('id');
    $_POST['filter_creator'] = $this->input->get('creator');
    $_POST['filter_name'] = $this->input->get('emp_name');
    $_POST['filter_company_name'] = $this->input->get('company_name');
    $_POST['filter_employee_id'] = $this->input->get('emp_id');
    $_POST['filter_date'] = $this->input->get('date');
    $_POST['filter_type'] = $this->input->get('type');
    $_POST['filter_status'] = $this->input->get('status');

    $all_data = $this->hr_model->get_all_filtered_requests_for_export($is_hr_user, $logged_in_user_id);

    // Manually create CSV content to match the table structure precisely
    $delimiter = ",";
    $newline = "\r\n";
    $filename = "employee_requests_export.csv";
    
    // Start with the UTF-8 BOM for Excel compatibility with Arabic
    $csv_data = "\xEF\xBB\xBF"; 

    // Add headers
    $csv_data .= 'رقم الطلب,مقدم الطلب,اسم الموظف,اسم الشركة,رقم الموظف,تاريخ الطلب,نوع الطلب,حالة الطلب,بانتظار موافقة' . $newline;
    
    // Add data rows
    foreach ($all_data as $row) {
        $status_text = 'غير معروف';
        if (isset($orderStatusMap[$row['status']])) {
            $status_text = $orderStatusMap[$row['status']]['label'];
        }
        
        $line = [
            $row['id'],
            '"' . str_replace('"', '""', $row['creator_name']) . '"',
            '"' . str_replace('"', '""', $row['emp_name']) . '"',
            '"' . str_replace('"', '""', $row['company_name']) . '"',
            $row['emp_id'],
            $row['date'],
            '"' . str_replace('"', '""', $row['order_name']) . '"',
            '"' . str_replace('"', '""', $status_text) . '"',
            '"' . str_replace('"', '""', $row['responsible_employee']) . '"',
        ];
        $csv_data .= implode($delimiter, $line) . $newline;
    }

    force_download($filename, $csv_data);
}
      


// In Users1.php

// In Users1.php
// In Users1.php, add this new function

// In Users1.php, REPLACE the ajax_add_punch function with this one

public function ajax_add_punch()
{
    // Security checks
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
    }
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->model('hr_model');
    
    $emp_id = $this->input->post('employee_id');
    $punch_date = $this->input->post('punch_date');
    $check_in_time = $this->input->post('check_in_time');
    $check_out_time = $this->input->post('check_out_time');

    // Validate that required fields are present and at least one time is entered
    if (empty($emp_id) || empty($punch_date) || (empty($check_in_time) && empty($check_out_time))) {
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'بيانات غير مكتملة. الرجاء التأكد من اختيار الموظف والتاريخ وإدخال وقت واحد على الأقل.']));
    }

    $success_count = 0;
    $error_count = 0;

    // Insert Check-in record if provided
    if (!empty($check_in_time)) {
        $data_to_insert = [
            'emp_code'       => $emp_id,
            'punch_time'     => $punch_date . ' ' . $check_in_time . ':00',
            'punch_state'    => 'Check In', // Set specific state
            'terminal_alias' => 'HR System',
            'area_alias'     => 'Manual Entry'
        ];
        if ($this->hr_model->add_manual_punch($data_to_insert)) {
            $success_count++;
        } else {
            $error_count++;
        }
    }

    // Insert Check-out record if provided
    if (!empty($check_out_time)) {
         $data_to_insert = [
            'emp_code'       => $emp_id,
            'punch_time'     => $punch_date . ' ' . $check_out_time . ':00',
            'punch_state'    => 'Check Out', // Set specific state
            'terminal_alias' => 'HR System',
            'area_alias'     => 'Manual Entry'
        ];
        if ($this->hr_model->add_manual_punch($data_to_insert)) {
            $success_count++;
        } else {
            $error_count++;
        }
    }

    // Provide a more detailed response
    if ($success_count > 0 && $error_count == 0) {
        $response = ['status' => 'success', 'message' => 'تمت إضافة البصمات بنجاح.'];
    } elseif ($success_count > 0 && $error_count > 0) {
        $response = ['status' => 'warning', 'message' => 'تمت إضافة بعض البصمات بنجاح، وفشل البعض الآخر.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل إضافة البصمات في قاعدة البيانات.'];
    }

    $response['csrf_name'] = $this->security->get_csrf_token_name();
    $response['csrf_hash'] = $this->security->get_csrf_hash();

    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function manual_attendance_edit()
{
    // Security check for HR users
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $this->load->model('hr_model');

    // Get filters from URL or use defaults
    $date = $this->input->get('date', TRUE) ?: date('Y-m-d');
    $employee_id = $this->input->get('employee_id', TRUE); // <-- Get the employee ID

    $data['selected_date'] = $date;
    $data['selected_employee_id'] = $employee_id; // <-- Pass it to the view
    $data['attendance_data'] = $this->hr_model->get_daily_first_last_punches($date, $employee_id); // <-- Pass it to the model
    $data['csrf_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();
    $data['all_employees'] = $this->hr_model->get_all_employees();
    
 
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/manual_attendance_edit_view', $data ?? []);
$this->load->view('template/new_footer');
 
}
// In Users1.php
// --- SEND CLEARANCE VIA EMAIL ---
    public function send_clearance_email_ajax() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Session Expired']); return;
        }

        $resignation_id = $this->input->post('resignation_id');
        $this->load->model('hr_model');

        // 1. Fetch Data (Same logic as print_clearance_form)
        $this->db->select('r.id, r.emp_id, r.date_of_the_last_working, e.subscriber_name as emp_name, e.employee_id as emp_code, e.job_tag as job_title, e.n1 as department_name, e.email as work_email, e.personal_email');
        $this->db->from('orders_emp r');
        $this->db->join('emp1 e', 'e.employee_id = r.emp_id', 'left');
        $this->db->where('r.id', $resignation_id);
        $data['info'] = $this->db->get()->row_array();

        if (!$data['info']) {
            echo json_encode(['status' => 'error', 'message' => 'Request not found']); return;
        }

        // Determine Email Address (Prefer Personal, Fallback to Work)
        $recipient_email = !empty($data['info']['personal_email']) ? $data['info']['personal_email'] : $data['info']['work_email'];

        if (empty($recipient_email)) {
            echo json_encode(['status' => 'error', 'message' => 'لم يتم العثور على بريد إلكتروني للموظف (No Email Found)']); return;
        }

        // 2. Fetch Tasks Data
        $this->db->select('rc.status, rc.updated_at, cp.parameter_name, cp.department_id, e_app.subscriber_name as approver_name');
        $this->db->from('resignation_clearances rc');
        $this->db->join('clearance_parameters cp', 'cp.id = rc.clearance_parameter_id');
        $this->db->join('emp1 e_app', 'e_app.id = rc.approver_user_id', 'left');
        $this->db->where('rc.resignation_request_id', $resignation_id);
        $query = $this->db->get()->result_array();

        $data['departments'] = [];
        foreach ($query as $row) {
            $dept_id = $row['department_id'];
            $data['departments'][$dept_id]['tasks'][] = $row;
            if ($row['status'] == 'approved') {
                $data['departments'][$dept_id]['approver'] = $row['approver_name'];
                $data['departments'][$dept_id]['date'] = $row['updated_at'];
            }
        }

        // 3. Generate HTML Content
        // We pass a flag 'is_email_mode' to hide the buttons inside the view
        $data['is_email_mode'] = true; 
        $email_body = $this->load->view('templateo/print_clearance_form', $data, TRUE);

        // 4. Configure Email
        $this->load->library('email');
        
        $config = array();
        $config['protocol']    = 'smtp';
        $config['smtp_host']   = 'MAR-PRD-EXH01.MARSOOM.NET';
        $config['smtp_user']   = 'itsystem@marsoom.net';
        $config['smtp_pass']   = 'Asd@123123';
        $config['smtp_port']   = 587;
        $config['smtp_crypto'] = 'tls';
        $config['mailtype']    = 'html';
        $config['charset']     = 'utf-8';
        $config['newline']     = "\r\n";
        $config['crlf']        = "\r\n";
        $config['wordwrap']    = TRUE;
        $config['smtp_timeout'] = 20;

        $this->email->initialize($config);

        $this->email->from('IT.systems@marsoom.net', 'Marsoom HR System');
        $this->email->to($recipient_email);
        $this->email->subject('نموذج إخلاء الطرف - Clearance Certificate - ' . $data['info']['emp_name']);
        $this->email->message($email_body);

        // 5. Send
        if ($this->email->send()) {
            echo json_encode(['status' => 'success', 'message' => 'تم إرسال النموذج بنجاح إلى: ' . $recipient_email]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'فشل الإرسال: ' . $this->email->print_debugger()]);
        }
    }
public function saturday_work_management()
{
    // Security check for HR users
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('You are not authorized.', 403);
        return;
    }
    $this->load->model('hr_model');
    $data['all_employees'] = $this->hr_model->get_all_employees(); // For the dropdown
    $data['csrf_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/saturday_work_view', $data ?? []);
$this->load->view('template/new_footer'); // We will create this view next
}

public function get_saturday_assignments_ajax()
{
    if (!$this->session->userdata('logged_in')) return;
    $this->load->model('hr_model');
    $start = $this->input->get('start');
    $end = $this->input->get('end');
    $assignments = $this->hr_model->get_saturday_assignments_for_month($start, $end);

    $events = [];
    foreach ($assignments as $assignment) {
        $events[] = [
            'id'    => $assignment['employee_id'] . '_' . $assignment['saturday_date'],
            'title' => $assignment['subscriber_name'],
            'start' => $assignment['saturday_date'],
            'allDay' => true,
            'extendedProps' => [
                'employee_id' => $assignment['employee_id']
            ]
        ];
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($events));
}

public function assign_saturday_work_ajax()
{
    if (!$this->input->is_ajax_request()) show_404();
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }
    
    $this->load->model('hr_model');
    $employee_ids = $this->input->post('employee_ids');
    $saturday_date = $this->input->post('saturday_date');
    
    if (empty($employee_ids) || empty($saturday_date)) {
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Missing data.']));
    }
    
    $assigner_id = $this->session->userdata('username');
    $this->hr_model->add_saturday_assignments($employee_ids, $saturday_date, $assigner_id);
    
    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success']));
}

public function remove_saturday_assignment_ajax()
{
    if (!$this->input->is_ajax_request()) show_404();
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->model('hr_model');
    $employee_id = $this->input->post('employee_id');
    $saturday_date = $this->input->post('saturday_date');

    if ($this->hr_model->remove_saturday_assignment($employee_id, $saturday_date)) {
        $response = ['status' => 'success'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to delete assignment.'];
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}

public function ajax_update_punch()
{
    // Security checks
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
    }
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->model('hr_model');
    
    $record_id = (int)$this->input->post('record_id');
    $new_time = $this->input->post('new_time');

    if (empty($record_id) || empty($new_time)) {
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Invalid data provided.']));
    }

    if ($this->hr_model->update_punch_record($record_id, $new_time)) {
        $response = ['status' => 'success', 'message' => 'تم تحديث وقت البصمة بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل تحديث البصمة. لم يتم العثور على السجل.'];
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
        // In Users1.php
// In Users1.php

public function clearance_form()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    $this->load->model('hr_model');

    $resignation_id = $this->input->get('resignation_id', true);

    $data = [
        'row'                   => null,
        'err'                   => null,
        'resignations'          => [], // Initialize as empty array
        'departments'           => [],
        'direct_manager'        => null,
        'clearance_in_progress' => false // Initialize flag
    ];

    if ($resignation_id) {
        // --- Logic when a specific resignation IS selected ---
        $row = $this->hr_model->get_request_details($resignation_id); // Assuming this gets details
        if ($row && isset($row['type']) && $row['type'] == 1 && in_array($row['status'], ['2', '10', '11'])) {
            $data['row'] = $row;
            $data['departments'] = $this->hr_model->get_all_departments();
            $data['direct_manager'] = $this->hr_model->get_direct_manager($row['emp_id']);
            // Check if clearance is already in progress for THIS specific ID
            $data['clearance_in_progress'] = $this->hr_model->check_existing_clearance_tasks($resignation_id);
        } else {
            $data['err'] = 'لم يتم العثور على طلب الاستقالة المعتمد المحدد.';
            // Still fetch the list for the search view in case of error
            $data['resignations'] = $this->hr_model->get_approved_resignations();
        }
    } else {
        // --- Logic when NO resignation is selected (showing the search dropdown) ---
        // <<< THIS LINE MUST CALL THE CORRECT MODEL FUNCTION >>>
        $data['resignations'] = $this->hr_model->get_approved_resignations();
        // <<< END CHECK >>>
        if (empty($data['resignations'])) {
             $data['err'] = 'لا توجد طلبات استقالة معتمدة متاحة حالياً.';
        }
    }

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/clearance_form_a4', $data ?? []);
$this->load->view('template/new_footer');
}
public function resignation_process_report()
{
    // Security check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    $data = [
        'report_data' => null,
        'resignations_list' => []
    ];

    $resignation_id = $this->input->get('resignation_id', true);

    if ($resignation_id) {
        // If an ID is selected, get the full report data
        $data['report_data'] = $this->hr_model->get_full_resignation_process_details($resignation_id);

        // --- Add the creator to the top of the approval log ---
        if ($data['report_data'] && !empty($data['report_data']['resignation'])) {
            $resignation_details = $data['report_data']['resignation'];

            $creator_log_entry = [
                'approval_level' => 'إنشاء', // "Creation"
                'approver_name'  => $resignation_details['creator_name'],
                'status'         => 'created', // A custom status for display
                'action_date'    => $resignation_details['date'] . ' ' . $resignation_details['time'],
                'rejection_reason' => '' 
            ];
            
            // Prepend the creator's action to the beginning of the approval log array
            array_unshift($data['report_data']['approval_log'], $creator_log_entry);
        }
        $grouped_clearances = [];
        if (!empty($data['report_data']['clearances'])) {
            foreach ($data['report_data']['clearances'] as $task) {
                $dept_name = $task['department_name'] ?? 'غير محدد'; // Fallback department name
                if (!isset($grouped_clearances[$dept_name])) {
                    $grouped_clearances[$dept_name] = []; // Initialize array for this department
                }
                $grouped_clearances[$dept_name][] = $task; // Add the task to its department group
            }
        }
        // Replace the original flat list with the grouped list
        $data['report_data']['grouped_clearances'] = $grouped_clearances;
        // *** END NEW GROUPING ***

    } else {
        // Otherwise, get the list of employees for the search dropdown
        $data['resignations_list'] = $this->hr_model->get_employees_with_approved_resignations();
    }
    
    // Load the view file
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/resignation_process_report_view', $data ?? []);
$this->load->view('template/new_footer');
}
// In application/controllers/Users1.php

public function mandate_action_ajax() 
{
    if (!$this->session->userdata('logged_in')) return;

    $req_id = $this->input->post('req_id');
    $action = $this->input->post('action'); // 'approve' or 'reject'
    $reason = $this->input->post('reason');
    $approver_id = $this->session->userdata('username');

    $this->load->model('hr_model');

    if ($action == 'reject') {
        // --- NEW LOGIC: Return to 2784 instead of killing the request ---
        if (empty($reason)) {
            echo json_encode(['status' => 'error', 'message' => 'Please provide a rejection reason.']);
            return;
        }

        $success = $this->hr_model->reject_mandate_return_to_specialist($req_id, $approver_id, $reason);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Returned to Specialist (2784) for revision.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database Error']);
        }
    } 
    elseif ($action == 'approve') {
        // Your existing standard approval logic here...
        // $this->hr_model->approve_mandate(...)
        echo json_encode(['status' => 'success', 'message' => 'Approved successfully']);
    }
}
public function export_resignation_report_excel($resignation_id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    $this->load->model('hr_model');
    $data = $this->hr_model->get_full_resignation_process_details($resignation_id);

    if (!$data) { show_error('Report data not found.', 404); return; }

    $filename = 'resignation_report_' . $data['resignation']['emp_id'] . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF"; // BOM for Arabic in Excel

    $output = fopen('php://output', 'w');
    
    // Section 1: Resignation Details
    fputcsv($output, ['تفاصيل طلب الاستقالة']);
    fputcsv($output, ['اسم الموظف', $data['resignation']['emp_name']]);
    fputcsv($output, ['الرقم الوظيفي', $data['resignation']['emp_id']]);
    fputcsv($output, ['تاريخ آخر يوم عمل', $data['resignation']['date_of_the_last_working']]);
    fputcsv($output, ['مقدم الطلب', $data['resignation']['creator_name']]);
    fputcsv($output, []);

    // Section 2: Settlement Details
    if ($data['settlement']) {
        fputcsv($output, ['تفاصيل التسوية المالية']);
        fputcsv($output, ['مكافأة نهاية الخدمة', $data['settlement']['gratuity_amount']]);
        fputcsv($output, ['تعويض الإجازات', $data['settlement']['compensation']]);
        fputcsv($output, ['إجمالي المستحقات', $data['settlement']['gratuity_amount'] + $data['settlement']['compensation']]);
        fputcsv($output, ['خصم التأمينات', $data['settlement']['insurance_deduction']]);
        fputcsv($output, ['خصومات أخرى', $data['settlement']['leave_balance_deduction'] + $data['settlement']['absence_deduction'] + $data['settlement']['lateness_deduction'] + $data['settlement']['penalty_clause_deduction']]);
        fputcsv($output, ['المبلغ النهائي', $data['settlement']['final_amount']]);
        fputcsv($output, []);
    }

    // Section 3: Clearance Tasks
    if (!empty($data['clearances'])) {
        fputcsv($output, ['حالة إخلاء الطرف']);
        fputcsv($output, ['الإدارة', 'المهمة', 'المسؤول', 'الحالة']);
        foreach ($data['clearances'] as $task) {
            fputcsv($output, [$task['department_name'], $task['parameter_name'], $task['approver_name'], $task['status']]);
        }
    }
    
    fclose($output);
    exit;
}

public function export_resignation_report_pdf($resignation_id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    $this->load->model('hr_model');
    $data = $this->hr_model->get_full_resignation_process_details($resignation_id);

    if (!$data) { show_error('Report data not found.', 404); return; }

    require_once(APPPATH . 'third_party/tcpdf/tcpdf.php');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('HR System');
    $pdf->SetTitle('تقرير إنهاء الخدمة - ' . $data['resignation']['emp_name']);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->setRTL(true);
    $pdf->AddPage();

    // --- Build HTML for PDF ---
    $html = '<h1>تقرير إنهاء الخدمة الشامل</h1>';
    
    // Resignation Info
    $html .= '<h3>1. تفاصيل طلب الاستقالة</h3>';
    $html .= '<table border="1" cellpadding="4">
                <tr><td><b>اسم الموظف</b></td><td>' . $data['resignation']['emp_name'] . '</td><td><b>الرقم الوظيفي</b></td><td>' . $data['resignation']['emp_id'] . '</td></tr>
                <tr><td><b>آخر يوم عمل</b></td><td>' . $data['resignation']['date_of_the_last_working'] . '</td><td><b>مقدم الطلب</b></td><td>' . $data['resignation']['creator_name'] . '</td></tr>
             </table>';
    
    // Settlement Info
    if ($data['settlement']) {
        $html .= '<h3>2. التسوية المالية النهائية</h3>';
        $html .= '<table border="1" cellpadding="4">
                    <tr><td><b>مكافأة نهاية الخدمة</b></td><td>' . number_format($data['settlement']['gratuity_amount'], 2) . '</td></tr>
                    <tr><td><b>تعويض رصيد الإجازات</b></td><td>' . number_format($data['settlement']['compensation'], 2) . '</td></tr>
                    <tr><td><b>خصم التأمينات</b></td><td>' . number_format($data['settlement']['insurance_deduction'], 2) . '</td></tr>
                    <tr><td style="background-color:#f0f0f0;"><b>المبلغ النهائي المستحق</b></td><td style="background-color:#f0f0f0;"><b>' . number_format($data['settlement']['final_amount'], 2) . '</b></td></tr>
                 </table>';
    }

    // Clearance Info
    if (!empty($data['clearances'])) {
        $html .= '<h3>3. حالة إخلاء الطرف</h3>';
        $html .= '<table border="1" cellpadding="4">
                    <tr style="background-color:#001f3f; color:white;">
                        <th>الإدارة</th><th>المهمة</th><th>المسؤول</th><th>الحالة</th>
                    </tr>';
        foreach ($data['clearances'] as $task) {
            $html .= '<tr><td>' . $task['department_name'] . '</td><td>' . $task['parameter_name'] . '</td><td>' . $task['approver_name'] . '</td><td>' . $task['status'] . '</td></tr>';
        }
        $html .= '</table>';
    }

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('resignation_report_' . $data['resignation']['emp_id'] . '.pdf', 'D');
    exit;
}
// In application/controllers/Users1.php
public function initiate_or_resubmit_clearance() // Renamed from submit_clearance_tasks
{
    // --- Keep initial checks ---
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
        redirect('users1/clearance_form');
        return; // Added return
    }
     if (!$this->session->userdata('logged_in')) { // Added login check
        redirect('users/login');
        return;
    }

    $this->load->model('hr_model');

    $resignation_id = $this->input->post('resignation_id');
    $employee_id = $this->input->post('employee_id'); // Get employee ID for manager lookup
    $department_ids = $this->input->post('department_ids') ?? [];
    $include_direct_manager = $this->input->post('include_direct_manager');
    $direct_manager_id = $this->input->post('direct_manager_id');
    $resubmit_confirmation = $this->input->post('resubmit_confirmation'); // Get confirmation value
    $finance_approver_id = $this->input->post('finance_approver');

    if (empty($resignation_id) || empty($employee_id)) {
        $this->session->set_flashdata('error', 'بيانات الطلب غير مكتملة.');
        redirect('users1/clearance_form?resignation_id=' . $resignation_id); // Redirect back to the form
        return;
    }
    // Basic check: at least manager or one department must be selected
    if (empty($include_direct_manager) && empty($department_ids)) {
         $this->session->set_flashdata('error', 'يجب اختيار المدير المباشر أو قسم واحد على الأقل.');
         redirect('users1/clearance_form?resignation_id=' . $resignation_id);
         return;
    }


    $this->db->trans_start(); // Start transaction

    $clearance_in_progress = $this->hr_model->check_existing_clearance_tasks($resignation_id);
    $cancel_success = true; // Assume success if not needed

    // === NEW: Check if resubmitting and confirmed ===
    if ($clearance_in_progress) {
        if ($resubmit_confirmation === 'yes') {
            // Cancel existing PENDING tasks
            $cancelled_count = $this->hr_model->cancel_existing_clearance_tasks($resignation_id);
            log_message('info', "Cancelled {$cancelled_count} pending clearance tasks for resignation ID {$resignation_id}.");
        } else {
            // User did not confirm resubmission via JS prompt (shouldn't normally happen if JS works, but acts as safeguard)
            $this->db->trans_rollback(); // Rollback any potential changes
            $this->session->set_flashdata('error', 'تم إلغاء العملية. لم يتم تأكيد استبدال المهام الحالية.');
            redirect('users1/clearance_form?resignation_id=' . $resignation_id);
            return;
        }
    }
    // === END NEW CHECK ===


    // Proceed with creating new tasks (this function now also deletes ALL old ones first)
    $manager_id_to_pass = ($include_direct_manager && !empty($direct_manager_id)) ? $direct_manager_id : null;
    $create_success = $this->hr_model->create_clearance_tasks_from_departments($resignation_id, $department_ids, $manager_id_to_pass, $finance_approver_id);


    $this->db->trans_complete(); // Complete transaction

    if ($this->db->trans_status() && $create_success) {
         if ($clearance_in_progress && $resubmit_confirmation === 'yes') {
             $this->session->set_flashdata('success', 'تم إلغاء المهام السابقة وإنشاء مهام إخلاء الطرف الجديدة بنجاح.');
         } else {
             $this->session->set_flashdata('success', 'تم إنشاء مهام إخلاء الطرف بنجاح.');
         }
       //  $this->hr_model->trigger_clearance_initiation_emails($resignation_id);
    } else {
        log_message('error', 'Transaction failed or task creation failed for resignation ID ' . $resignation_id);
        $this->session->set_flashdata('error', 'فشل في تحديث أو إنشاء مهام إخلاء الطرف.');
    }

    // Redirect to a relevant page, maybe the main HR dashboard or requests list
    redirect('users1/main_hr1');
}

public function submit_clearance_initiation()
{
    // This function handles the form submission from the HR user
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
        redirect('users1/clearance_form');
    }

    $resignation_id = $this->input->post('resignation_id');
    $employee_id = $this->input->post('employee_id');
    $department_ids = $this->input->post('department_ids') ?? [];

    if (empty($resignation_id) || empty($employee_id) || empty($department_ids)) {
        $this->session->set_flashdata('error', 'بيانات غير مكتملة. الرجاء اختيار موظف وقسم واحد على الأقل.');
        redirect('users1/clearance_form');
        return;
    }

    $success = $this->hr_model->initiate_clearance_process($resignation_id, $employee_id, $department_ids);

    if ($success) {
        $this->session->set_flashdata('success', 'تم بدء عملية إخلاء الطرف بنجاح وتم إرسالها للمدير المباشر للاعتماد.');
     //   $this->hr_model->trigger_clearance_initiation_emails($resignation_id);
    } else {
        $this->session->set_flashdata('error', 'فشل في بدء عملية إخلاء الطرف.');
    }

    redirect('users1/orders_emp'); // Redirect to a relevant page
}
// In Users1.php
// In Users1.php -> Replace the entire end_of_service() function

// In Users1.php
// This is the only version of the function you should have.

// In Users1.php
// This is the only version of the function you should have.

// In Users1.php
// REPLACE the entire old end_of_service() function with this new one

public function end_of_service()
{
    $this->load->model('hr_model');
    $emp_id = $this->input->get('emp', true);
    $replace_existing = $this->input->get('replace') === 'true';
    
    // --- CAPTURE THE CUSTOM DATE FROM THE FORM ---
    $custom_deduction_start_date = $this->input->get_post('deduction_start_date', true);

    // Handle POST Request (Form Submission)
    if ($this->input->server('REQUEST_METHOD') === 'POST' && $emp_id) {
    
        if ($this->input->post('replace_flag') === 'true') {
            $this->hr_model->delete_existing_settlement($emp_id);
        }

        $new_reason = $this->input->post('reason_for_resignation');
        $resignation_order_id_for_reason = $this->input->post('resignation_order_id');
        if (!empty($new_reason) && !empty($resignation_order_id_for_reason)) {
            $this->hr_model->update_resignation_reason($resignation_order_id_for_reason, $new_reason);
        }

        $items = $this->input->post('items');
        $final_amount = $this->input->post('settlement')['final_amount'] ?? 0;

        $data_to_save = [
            'employee_id'          => $emp_id,
            'resignation_order_id' => $this->input->post('resignation_order_id'),
            'created_by_id'        => $this->session->userdata('username'),
            'status'               => 'pending_review',
            'final_amount'         => (float)$final_amount,
            'items'                => $items,
            'settlement'           => $this->input->post('settlement'),
            'deduction_start_date' => $custom_deduction_start_date // <-- SAVES DATE
        ];

        $settlement_id = $this->hr_model->save_end_of_service_settlement($data_to_save);

        if ($settlement_id) {
            $approver_ids = $this->input->post('approvers');
            $filtered_approvers_with_old_keys = $approver_ids ? array_filter($approver_ids) : [];
            $filtered_approvers = array_values($filtered_approvers_with_old_keys);

            if (empty($filtered_approvers)) {
                $this->hr_model->delete_existing_settlement($emp_id);
                $this->session->set_flashdata('error_message', 'فشل الحفظ. يجب تحديد معتمد واحد على الأقل لمسار الاعتماد.');
                redirect('users1/end_of_service?emp=' . $emp_id);
                return;
            }

            $first_approver = null;
            foreach ($filtered_approvers as $index => $approver_id) {
                $level = $index + 1;
                $status = ($level === 1) ? 'pending' : 'waiting';
                $this->hr_model->add_sequential_approval_step($settlement_id, 8, $approver_id, $level, $status);
                if ($level === 1) {
                    $first_approver = $approver_id;
                }
            }
            $this->hr_model->update_settlement_first_approver($settlement_id, $first_approver);
            // ==========================================
            // [NEW] TRIGGER EOS EMAIL FOR FIRST APPROVER
            // ==========================================
            $this->hr_model->trigger_eos_email($settlement_id);
            // ==========================================
            $this->session->set_flashdata('success_message', 'تم حفظ مستحقات نهاية الخدمة وتقديمها للاعتماد بنجاح.');
        } else {
            $this->session->set_flashdata('error_message', 'حدث خطأ أثناء حفظ البيانات.');
        }
        redirect('users1/orders_emp_app');
        return;
    }

    // Handle GET Request (Displaying the page)
    $data = [
        'emp'                => $emp_id,
        'row'                => null,
        'err'                => null,
        'employees'          => [],
        'is_approval_mode'   => false,
        'settlement_details' => null,
        'approval_task_id'   => null,
        'replace_existing'   => $replace_existing,
        'all_approvers'      => []
    ];

    $approval_task_id = $this->input->get('task_id', true);

    if ($approval_task_id) {
        $data['is_approval_mode'] = true;
        $settlement_details = $this->hr_model->get_settlement_details_for_approval($approval_task_id, $this->session->userdata('username'));
        
        if ($settlement_details) {
            
            // --- PASS THE SAVED DATE TO THE MODEL ---
            $saved_deduction_date = $settlement_details['deduction_start_date'] ?? null;
            $row_data = $this->hr_model->get_end_of_service_data($settlement_details['employee_id'], true, $saved_deduction_date);
            
            if (!$row_data) {
                $data['err'] = 'لا يمكن العثور على بيانات الموظف الأساسية.';
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/end_of_service_a4', $data ?? []);
$this->load->view('template/new_footer');
                return;
            }

            // Extract saved financial items from JSON
            $items_json = $settlement_details['items_json'] ?? '[]';
            $saved_items = json_decode($items_json, true);
            $saved_values = [];
            if (is_array($saved_items)) {
                foreach ($saved_items as $item) {
                    if (isset($item['key'])) {
                        $saved_values[$item['key']] = (float)($item['amount'] ?? 0);
                    }
                }
            }

            // Calculate rates
            $total_salary = (float)($row_data['total_salary'] ?? 0);
            $daily_rate = $total_salary > 0 ? $total_salary / 30.0 : 0;
            $minute_rate = $daily_rate > 0 ? $daily_rate / 8.0 / 60.0 : 0; 

            // REVERSE-CALCULATE and OVERWRITE the summary values in $row_data
            $compensation = $saved_values['compensation'] ?? 0;
            $negative_deduction = $saved_values['leave_balance_deduction'] ?? 0;
            if ($daily_rate > 0) {
                if ($compensation > 0) {
                    $row_data['leave_balance'] = $compensation / $daily_rate;
                } elseif ($negative_deduction > 0) {
                    $row_data['leave_balance'] = -($negative_deduction / $daily_rate);
                } else {
                    $row_data['leave_balance'] = 0; 
                }
            } else {
                $row_data['leave_balance'] = 0; 
            }

            $absence_deduction = $saved_values['absence_deduction'] ?? 0;
            if ($daily_rate > 0) {
                $row_data['total_absences'] = round($absence_deduction / $daily_rate);
            } else {
                $row_data['total_absences'] = 0;
            }

            $lateness_deduction = $saved_values['lateness_deduction'] ?? 0;
            if ($minute_rate > 0) {
                $row_data['total_lateness'] = round($lateness_deduction / $minute_rate);
                $row_data['total_early'] = 0; 
            } else {
                $row_data['total_lateness'] = 0;
                $row_data['total_early'] = 0;
            }

            $data['row'] = $row_data;
            $data['settlement_details'] = $settlement_details;
            $data['approval_task_id'] = $approval_task_id;

        } else {
            $data['err'] = 'المهمة غير موجودة أو ليست من صلاحياتك.';
        }

    } elseif ($emp_id) {
        // --- PASS THE CUSTOM DATE FROM THE FORM TO THE MODEL ---
        $row = $this->hr_model->get_end_of_service_data($emp_id, true, $custom_deduction_start_date);
        
        if ($row) {
            $existing_settlement = $this->hr_model->get_existing_settlement($emp_id);
            
            if ($existing_settlement && !$replace_existing) {
                
                $settlement_details = (array)$existing_settlement;
                
                $items_json = $settlement_details['items_json'] ?? '[]';
                $saved_items = json_decode($items_json, true);
                $saved_values = [];
                if (is_array($saved_items)) {
                    foreach ($saved_items as $item) {
                        if (isset($item['key'])) {
                            $saved_values[$item['key']] = (float)($item['amount'] ?? 0);
                        }
                    }
                }

                $total_salary = (float)($row['total_salary'] ?? 0);
                $daily_rate = $total_salary > 0 ? $total_salary / 30.0 : 0;
                $minute_rate = $daily_rate > 0 ? $daily_rate / 8.0 / 60.0 : 0; 

                $compensation = $saved_values['compensation'] ?? 0;
                $negative_deduction = $saved_values['leave_balance_deduction'] ?? 0;
                if ($daily_rate > 0) {
                    if ($compensation > 0) {
                        $row['leave_balance'] = $compensation / $daily_rate;
                    } elseif ($negative_deduction > 0) {
                        $row['leave_balance'] = -($negative_deduction / $daily_rate);
                    } else {
                        $row['leave_balance'] = 0;
                    }
                } else {
                    $row['leave_balance'] = 0;
                }

                $absence_deduction = $saved_values['absence_deduction'] ?? 0;
                if ($daily_rate > 0) {
                    $row['total_absences'] = round($absence_deduction / $daily_rate);
                } else {
                    $row['total_absences'] = 0;
                }

                $lateness_deduction = $saved_values['lateness_deduction'] ?? 0;
                if ($minute_rate > 0) {
                    $row['total_lateness'] = round($lateness_deduction / $minute_rate);
                    $row['total_early'] = 0; 
                } else {
                    $row['total_lateness'] = 0;
                    $row['total_early'] = 0;
                }

                $data['row'] = $row;
                $data['settlement_details'] = $settlement_details;
                $data['is_approval_mode'] = true; 
              } else {
                $data['row'] = $row;
                $default_approver_ids = ['2774', '2230', '2833', '2909', '1693', '2784', '1001']; 
                $data['all_approvers'] = $this->hr_model->get_employee_details_bulk($default_approver_ids);
            }
        } else {
            $data['err'] = 'لا توجد بيانات موظف بهذا الرقم، أو لا يوجد طلب استقالة معتمد له.';
        }
    } else {
        $data['employees'] = $this->hr_model->get_all_employees(true);
    }

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/end_of_service_a4', $data ?? []);
$this->load->view('template/new_footer');
}
public function update_adjustment_reason_ajax() {
    if (!$this->session->userdata('logged_in')) return;

    $emp_id = $this->input->post('emp_id');
    $sheet_id = $this->input->post('sheet_id');
    $new_reason = $this->input->post('reason');
    $adj_type = $this->input->post('adj_type'); // 'addition' or 'deduction'

    $table = ($adj_type == 'addition') ? 'reparations' : 'discounts';

    // Update the 'type' column for records matching this employee and sheet
    $this->db->where('emp_id', $emp_id);
    $this->db->where('sheet_id', $sheet_id);
    $this->db->update($table, ['type' => $new_reason]);

    echo json_encode([
        'status' => 'success',
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
// In application/controllers/Users1.php
// In application/controllers/Users1.php
public function get_adjustment_reasons_ajax() {
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error']); return;
    }
    
    $adj_type = $this->input->post('adj_type'); // 'addition' or 'deduction'
    $this->load->model('hr_model');
    
    if ($adj_type == 'addition') {
        $reasons = $this->hr_model->get_all_unique_reparation_types();
    } else {
        $reasons = $this->hr_model->get_all_unique_discount_types();
    }
    
    echo json_encode([
        'status' => 'success', 
        'reasons' => $reasons, 
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
public function export_processed_payroll() {
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    // 2. Load Resources
    $this->load->dbutil();
    $this->load->helper('download');
    $this->load->model('hr_model'); // Assuming your logic is here

    // 3. Get Filters (Same as your report view)
    $month = $this->input->get('month');
    $company_code = $this->input->get('company_code');
    $search = $this->input->get('employee_search');

    // 4. Fetch Data (Reuse your existing model logic to get the raw data)
    // Note: Adjust 'get_payroll_data' to whatever function you use to populate the view
    $data = $this->hr_model->get_payroll_data($month, $company_code, $search);

    // 5. Generate CSV Content
    // We explicitly map columns to ensure ALL fields are there with Arabic headers
    $csv_content = "\xEF\xBB\xBF"; // BOM for Arabic support in Excel

    // Define Headers matching your DB columns (n1, n2... etc)
    $headers = [
        'ID (n1)', 'Name (n2)', 'Nationality (n3)', 'Bank (n4)', 'IBAN (n5)',
        'Gross Salary (n6)', 'Late Min (n7)', 'Early Min (n8)', 'Absence Days (n9)',
        'GOSI (n10)', 'Total Deductions (n11)', 'Net Salary (n12)', 'Month (nf)',
        'Company (n14)', 'Hiring Diff (n15)', 'Previous Balance (n16)', 'Sanctions (n17)',
        'Loan (n18)', 'n19', 'n20', 
        'Basic (n21)', 'Housing (n22)', 'Transport (n23)', 'Other (n24)',
        'n25', 'n26', 'n27', 'n28', 'Absence Ded (n29)', 'n30'
    ];
    
    $csv_content .= implode(",", $headers) . "\n";

    foreach ($data as $row) {
        $line = [
            $row['n1'], $row['n2'], $row['n3'], $row['n4'], $row['n5'],
            $row['n6'], $row['n7'], $row['n8'], $row['n9'],
            $row['n10'], $row['n11'], $row['n12'], $row['n13'],
            $row['n14'], $row['n15'], $row['n16'], $row['n17'],
            $row['n18'], $row['n19'], $row['n20'],
            $row['n21'], $row['n22'], $row['n23'], $row['n24'],
            $row['n25'], $row['n26'], $row['n27'], $row['n28'], $row['n29'], $row['n30']
        ];
        
        // Escape for CSV
        $line = array_map(function($field) {
            return '"' . str_replace('"', '""', $field) . '"';
        }, $line);

        $csv_content .= implode(",", $line) . "\n";
    }

    // 6. Force Download
    $filename = 'payroll_full_export_' . date('Ymd_His') . '.csv';
    force_download($filename, $csv_content);
}
public function export_payroll_sheet_csv($sheet_id)
{
    // 1. Security Checks
    if (!$this->session->userdata('logged_in')) { 
        redirect('users/login'); 
    }
    
    $hr_users = ['1835', '2230', '2515', '2774', '2784', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('Unauthorized', 403);
    }

    $this->load->model('hr_model');
    $this->load->helper('download');

    // 2. Fetch Data
    $sheet_info = $this->hr_model->get_salary_sheet($sheet_id);
    if (!$sheet_info) { 
        show_error('Sheet not found', 404);
    }

    $sheet_start_date = $sheet_info['start_date'];
    $sheet_end_date   = $sheet_info['end_date'];
    
    // Get employees with company filter
    $selected_company = $this->input->get('company', TRUE);
    $employees = $this->hr_model->get_active_employees();
    
    if (!empty($selected_company)) {
        $selected_company = trim($selected_company);
        $employees = array_filter($employees, function($row) use ($selected_company) {
            $db_company_normalized = trim(preg_replace('/\s+/', ' ', $row->company_name));
            return $db_company_normalized === $selected_company;
        });
        
        // Re-index array
        $employees = array_values($employees);
    }

    if (empty($employees)) {
        show_error('No employees found for the selected criteria', 404);
    }

    // 3. Get employee IDs and load data maps
    $employee_ids = array_map(function($e) { 
        return $e->employee_id; 
    }, $employees);

    // Load all required data in batches to avoid memory issues
    $data_map = $this->hr_model->get_payroll_data_maps($employee_ids, $sheet_id, $sheet_start_date, $sheet_end_date);

    // 4. Prepare Dates
    try {
        $sheet_start_dt = new DateTime($sheet_start_date);
        $sheet_end_dt   = new DateTime($sheet_end_date);
        $payroll_start_day = (int)$sheet_start_dt->format('j');
    } catch (Exception $e) { 
        show_error('Invalid date format in sheet data', 500);
    }

    // 5. Start CSV Output
    $filename = "payroll_sheet_" . $sheet_id . "_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Output UTF-8 BOM for Excel compatibility
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers - Match your view columns
    fputcsv($output, [
        'الرقم الوظيفي', 'اسم الموظف', 'رقم الهوية', 'اسم البنك', 'الايبان', 'حالة الموظف',
        'المسمى الوظيفي', 'الادارة', 'الجنسية', 'إجمالي الأجر', 'الراتب الأساسي', 'بدل السكن',
        'بدل النقل', 'بدلات أخرى', 'بدل الاتصال', 'اسم الشركة', 'أيام الغياب', 'أيام إجازة غير مدفوعة',
        'إجمالي الدقائق المتأخرة', 'إجمالي الدقائق المبكرة', 'أيام بصمة منفردة', 'فروقات التوظيف',
        'راتب الشهر السابق', 'التعويضات', 'خصم الغياب', 'خصم إجازة غير مدفوعة', 'خصم بصمة منفردة',
        'خصم دقائق التأخير والخروج المبكر', 'خصم الجزاءات', 'إجمالي خصم الحضور والانصراف',
        'اجمالي الراتب ما قبل خصم التأمينات', 'اجمالي خصم التامينات', 'صافي الراتب'
    ]);

    // 6. Process each employee
    foreach ($employees as $row) {
        $emp_id = $row->employee_id;
        
        // Initialize all variables with defaults
        $full_time_salary = (float)($row->total_salary ?? 0);
        $salary_for_this_period = $full_time_salary;
        $employee_note = $data_map['notes'][$emp_id] ?? '—';
        $reparations_amount = $data_map['reparations'][$emp_id] ?? 0.0;
        $is_new_employee = false;
        $skip_this_month = false;
        $previous_month_comp = 0.0;
        $hiring_day_difference = 0.0;
        $actual_worked_days = 30;
        
        $is_stopped = isset($data_map['stopped'][$emp_id]);
        $is_exempt = isset($data_map['exemptions'][$emp_id]);

        // New employee logic
        $new_emp_details = $data_map['new_employees'][$emp_id] ?? null;
        if ($new_emp_details && !empty($new_emp_details->join_date) && $sheet_start_dt) {
            $is_new_employee = true;
            try {
                $join_dt = new DateTime(str_replace('/', '-', trim($new_emp_details->join_date)));
                $join_day = (int)$join_dt->format('j');

                if ($join_dt >= $sheet_start_dt && $join_dt <= $sheet_end_dt) {
                    if ($join_day >= $payroll_start_day) {
                        // Policy 2: Joined >= 16th
                        $days_in_current_month = max(1, 30 - $join_day + 1);
                        $actual_worked_days = $days_in_current_month + 30;
                        $salary_for_this_period = ($full_time_salary / 30) * $actual_worked_days;
                        $employee_note = "موظف جديد (سياسة >= 16) - راتب $actual_worked_days يوم";
                        $hiring_day_difference = $full_time_salary - $salary_for_this_period;
                    } else {
                        // Policy 1: Joined < 16th
                        $actual_worked_days = max(1, 30 - $join_day + 1);
                        $salary_for_this_period = ($full_time_salary / 30) * $actual_worked_days;
                        $hiring_day_difference = $full_time_salary - $salary_for_this_period;
                    }
                } elseif ($join_dt > $sheet_end_dt) {
                    $skip_this_month = true;
                    $employee_note = 'موظف لم يبدأ العمل بعد';
                    $salary_for_this_period = 0;
                    $hiring_day_difference = $full_time_salary;
                }
            } catch (Exception $e) {
                $employee_note = 'خطأ في قراءة تاريخ الانضمام';
            }
        }

        // Calculate working hours and rates
        $rule_row = $data_map['rules'][$emp_id] ?? null;
        $default_working_hours = 8.0;
        if ($rule_row && !empty($rule_row->working_hours)) {
            $wh_raw = trim((string)$rule_row->working_hours);
            if (strpos($wh_raw, ':') !== false) {
                $parts = array_map('intval', explode(':', $wh_raw));
                $h = $parts[0] ?? 0; 
                $m = $parts[1] ?? 0; 
                $default_working_hours = $h + ($m/60);
            } else {
                $default_working_hours = floatval($wh_raw);
            }
            if ($default_working_hours <= 0) { 
                $default_working_hours = 8.0; 
            }
        }

        $full_time_daily_salary = $full_time_salary > 0 ? $full_time_salary / 30 : 0;
        $full_time_minute_salary = ($default_working_hours > 0 && $full_time_daily_salary > 0) 
            ? ($full_time_daily_salary / $default_working_hours / 60) 
            : 0;

        // Previous period deductions
        $total_previous_attendance_deductions = 0.0;
        if ($previous_month_comp > 0 && isset($data_map['prev_attendance'][$emp_id])) {
            $prev_summary = $data_map['prev_attendance'][$emp_id];
            $prev_absence_days = $prev_summary ? (int)$prev_summary->absence : 0;
            $prev_minutes_late = $prev_summary ? (int)$prev_summary->minutes_late : 0;
            $prev_minutes_early = $prev_summary ? (int)$prev_summary->minutes_early : 0;
            $prev_single_thing = $prev_summary ? (int)$prev_summary->single_thing : 0;

            $total_previous_attendance_deductions = 
                ($full_time_daily_salary * $prev_absence_days) +
                ($full_time_minute_salary * $prev_minutes_late) +
                ($full_time_minute_salary * $prev_minutes_early) +
                ($full_time_daily_salary * $prev_single_thing);
        }

        // Current period deductions
        $summary = $data_map['attendance'][$emp_id] ?? null;
        $absence_from_summary = $summary ? (int)$summary->absence : 0;
        $unpaid_leave_days = $data_map['unpaid_leave'][$emp_id] ?? 0;
        $half_day_vacations = $data_map['half_day'][$emp_id] ?? 0;

        $pure_unpaid_days = $unpaid_leave_days;
        $pure_absence_days = max(0, $absence_from_summary - $pure_unpaid_days);

        $minutes_late = $summary ? (int)$summary->minutes_late : 0;
        $minutes_early = $summary ? (int)$summary->minutes_early : 0;
        $single_thing = $summary ? (int)$summary->single_thing : 0;

        // Fix for new employees
        if ($is_new_employee && !$skip_this_month && $previous_month_comp == 0 && $hiring_day_difference > 0) {
            $pure_absence_days = 0; 
            $single_thing = 0;
        }

        $absence_deduct = $full_time_daily_salary * $pure_absence_days;
        $unpaid_leave_deduction = $full_time_daily_salary * $pure_unpaid_days;
        $single_thing_deduct = $full_time_daily_salary * $single_thing;
        $late_deduct = $full_time_minute_salary * $minutes_late;
        $early_deduct = $full_time_minute_salary * $minutes_early;
        $half_day_vacation_deduct = ($full_time_daily_salary / 2) * $half_day_vacations;

        $attendance_total_deduct = $absence_deduct + $unpaid_leave_deduction + $single_thing_deduct + 
                                 $late_deduct + $early_deduct + $half_day_vacation_deduct;

        $discount_amount = $data_map['discounts'][$emp_id] ?? 0.0;

        // Insurance deduction
        $insurance_deduction = 0.0;
        $discount_rate = $data_map['insurance'][$emp_id] ?? 0.0;
        
        if (!$skip_this_month && $discount_rate > 0) {
            $is_saudi = ($row->nationality === 'سعودي');
            if ($is_saudi) {
                $base_plus_house = (float)($row->base_salary ?? 0) + (float)($row->housing_allowance ?? 0);
                $base_plus_house_capped = min($base_plus_house, 45000);
                $insurance_deduction = $base_plus_house_capped * $discount_rate;
            }
        }

        // Final calculations
        $total_deductions = $attendance_total_deduct + $total_previous_attendance_deductions + $discount_amount;
        $salary_before_insurance = ($salary_for_this_period + $reparations_amount + $previous_month_comp) - $total_deductions;
        $net_salary = $salary_before_insurance - $insurance_deduction;

        // Handle stopped or skipped employees
        if ($is_stopped || $skip_this_month) {
            $net_salary = 0.0;
            $salary_before_insurance = 0.0;
            if ($is_stopped) {
                $employee_note = 'الراتب موقف';
            }
        }

        // Write the row to CSV
        fputcsv($output, [
            $row->employee_id,
            $row->subscriber_name,
            $row->id_number,
            $row->n3,
            $row->n2,
            $employee_note,
            $row->profession,
            $row->n1,
            $row->nationality,
            number_format($full_time_salary, 2),
            number_format((float)($row->base_salary ?? 0), 2),
            number_format((float)($row->housing_allowance ?? 0), 2),
            number_format((float)($row->n4 ?? 0), 2),
            number_format((float)($row->other_allowances ?? 0), 2),
            number_format((float)($row->n7 ?? 0), 2),
            $row->company_name,
            $pure_absence_days,
            $pure_unpaid_days,
            $minutes_late,
            $minutes_early,
            $single_thing,
            number_format($hiring_day_difference, 2),
            number_format($previous_month_comp, 2),
            number_format($reparations_amount, 2),
            number_format($absence_deduct, 2),
            number_format($unpaid_leave_deduction, 2),
            number_format($single_thing_deduct, 2),
            number_format($late_deduct + $early_deduct, 2),
            number_format($discount_amount, 2),
            number_format($attendance_total_deduct + $total_previous_attendance_deductions, 2),
            number_format($salary_before_insurance, 2),
            number_format($insurance_deduction, 2),
            number_format($net_salary, 2)
        ]);
    }

    fclose($output);
    exit;
}
public function process_settlement_approval()
{
    $this->load->model('hr_model');
    
    $task_id = $this->input->post('task_id');
    $action = $this->input->post('action'); // 'approve' or 'reject'
    $comments = $this->input->post('comments', true);
    $approver_id = $this->session->userdata('username');
    if ($approver_id == '2784' && $action === 'approve') {
        $verification_data = [
            'signed_by_employee' => $this->input->post('ver_signed') ? 1 : 0,
            'verified_mol'       => $this->input->post('ver_mol') ? 1 : 0,
            'verified_other'     => $this->input->post('ver_other') ? 1 : 0,
        ];
        // We need to fetch the Order ID to update it
        // We can get it from the task or pass it hidden. Let's assume the model handles finding the order from task_id or we query it.
        // Ideally, pass order_id in the form, but looking at your model, we can fetch it via the task.
        $this->hr_model->update_settlement_verification_data($task_id, $verification_data);
    }
    // --- END NEW LOGIC ---

    if ($action === 'approve') {
        $result = $this->hr_model->approve_settlement($task_id, $approver_id, $comments);
        if ($result) {
            $this->session->set_flashdata('success_message', 'تم اعتماد التسوية بنجاح.');
        } else {
            $this->session->set_flashdata('error_message', 'حدث خطأ أثناء الاعتماد.');
        }
    } elseif ($action === 'reject') {
        $result = $this->hr_model->reject_settlement($task_id, $approver_id, $comments);
        if ($result) {
            $this->session->set_flashdata('success_message', 'تم رفض التسوية بنجاح.');
        } else {
            $this->session->set_flashdata('error_message', 'حدث خطأ أثناء الرفض.');
        }
    }

    redirect('users1/orders_emp_app'); // Redirect to approval tasks list
}
public function employee_daily_log_ramadan($employee_id = null, $sheet_id = null)
{
    // Security Check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }

    if (empty($employee_id) || empty($sheet_id)) {
        show_error('Employee ID or Sheet ID is missing.', 400);
        return;
    }

    $this->load->model('hr_model');

    // 1. Get the combined result from the model safely
    $result = $this->hr_model->get_daily_attendance_log_ramadan($employee_id, $sheet_id);    
    
    // 2. Split the result into the specific view variables
    $data['daily_log']    = $result['daily_log'] ?? []; 
    $data['summary_data'] = $result['summary'] ?? false; 

    $employee_info = $this->hr_model->get_employee_info($employee_id);
    $sheet_info    = $this->hr_model->get_salary_sheet($sheet_id);

    $data['employee_name'] = $employee_info['name'] ?? $employee_id;
    $data['employee_id']   = $employee_id;
    $data['sheet_period']  = ($sheet_info['start_date'] ?? 'N/A') . ' - ' . ($sheet_info['end_date'] ?? 'N/A');
    $data['sheet_id']      = $sheet_id;

    // Get salary calculation details
    $data['salary_details'] = $this->hr_model->get_salary_calculation_details($employee_id, $sheet_id);
    $data['deductions'] = $this->hr_model->get_deductions($employee_id, $sheet_id);
    $data['additions'] = $this->hr_model->get_additions($employee_id, $sheet_id);

    // --- RAMADAN SPECIFIC ADDITION (CRASH-PROOF) ---
    $data['working_hours'] = 9.0; // Default fallback
    if (isset($this->db)) {
        $rules_query = $this->db->get_where('work_restrictions', ['emp_id' => $employee_id]);
        
        // Ensure the query didn't fail before calling row_array()
        if ($rules_query !== false) {
            $rules = $rules_query->row_array();
            if ($rules && isset($rules['working_hours'])) {
                $data['working_hours'] = (float)$rules['working_hours'];
            }
        }
    }

    // Load the NEW RAMADAN view file
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/employee_daily_log_ramadan_view', $data ?? []);
$this->load->view('template/new_footer');
}
public function employee_daily_log($employee_id = null, $sheet_id = null)
{
    // Security Check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }

    if (empty($employee_id) || empty($sheet_id)) {
        show_error('Employee ID or Sheet ID is missing.', 400);
        return;
    }

    $this->load->model('hr_model');

    // --- FIX STARTS HERE ---
    
    // 1. Get the combined result from the model
    $result = $this->hr_model->get_daily_attendance_log($employee_id, $sheet_id);
    
    // 2. Split the result into the specific view variables
    // Use empty array fallback to prevent errors if model returns null
    $data['daily_log']    = $result['daily_log'] ?? []; 
    $data['summary_data'] = $result['summary'] ?? false; 
    
    // --- FIX ENDS HERE ---

    $employee_info = $this->hr_model->get_employee_info($employee_id);
    $sheet_info    = $this->hr_model->get_salary_sheet($sheet_id);

    $data['employee_name'] = $employee_info['name'] ?? $employee_id;
    $data['employee_id']   = $employee_id;
    $data['sheet_period']  = ($sheet_info['start_date'] ?? 'N/A') . ' - ' . ($sheet_info['end_date'] ?? 'N/A');
    $data['sheet_id']      = $sheet_id;

    // Get salary calculation details
    $data['salary_details'] = $this->hr_model->get_salary_calculation_details($employee_id, $sheet_id);
    
    // Get deductions from discounts table
    $data['deductions'] = $this->hr_model->get_deductions($employee_id, $sheet_id);
    
    // Get additions from reparations table
    $data['additions'] = $this->hr_model->get_additions($employee_id, $sheet_id);

    // Load the view file
    // Make sure this path is correct. Previously we just used 'employee_daily_log_view'
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/employee_daily_log_view', $data ?? []);
$this->load->view('template/new_footer');
}
// In Users1.php
public function ajax_update_leave_balance()
{
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
    }

    $this->load->model('hr_model');

    $employee_id = $this->input->post('employee_id');
    $new_balance = $this->input->post('new_balance');

    if (empty($employee_id) || !is_numeric($new_balance) || $new_balance < 0) {
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Invalid data provided.']));
    }

    $success = $this->hr_model->update_employee_leave_balance($employee_id, $new_balance);

    if ($success) {
        $response = ['status' => 'success', 'message' => 'تم تحديث رصيد الإجازات بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل تحديث الرصيد. قد لا يوجد سجل لهذا الموظف.'];
    }

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}
// Add this new function to Users1.php
public function update_settlement_status()
{
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
    }

    $this->load->model('hr_model');
    $task_id = (int)$this->input->post('task_id');
    $action = $this->input->post('action'); // 'approve' or 'reject'
    $reason = $this->input->post('rejection_reason');
    $approver_id = $this->session->userdata('username');

    if (empty($task_id) || !in_array($action, ['approve', 'reject'])) {
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Invalid data.']));
    }
    
    if ($action === 'reject' && empty($reason)) {
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'Rejection reason is required.']));
    }

    $success = $this->hr_model->process_settlement_action($task_id, $action, $approver_id, $reason);

    if ($success) {
        $response = ['status' => 'success', 'message' => 'تم تسجيل الإجراء بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل تسجيل الإجراء. قد تكون المهمة تمت معالجتها مسبقًا.'];
    }
    
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}

// In Users1.php

// In Users1.php

// In Users1.php

public function attendance()
{
    $logged_in_username = $this->session->userdata('username');
    if (!$logged_in_username) { 
        redirect('users/login'); 
        return; 
    }

    $this->load->model('hr_model');
    
    // 1. Authorize user
    $hr_users = ['2774', '2230', '2784', '1835', '2515','2901'];
    $is_hr_user = in_array($logged_in_username, $hr_users);

    // --- EXISTING: ABHA SUPERVISOR ---
    $is_abha_supervisor = ($logged_in_username == '2694');
    $abha_employees = [];

    // --- NEW: COMPANY 2 SUPERVISOR (1136) ---
    $is_company_2_supervisor = ($logged_in_username == '1136');
    $company_2_employees = [];

    $subordinates = [];
    $is_manager = false;
    
    if ($is_hr_user) {
        // HR logic (sees everyone via search)
    } elseif ($is_abha_supervisor) {
        $abha_employees = $this->hr_model->get_employees_by_location('أبها');
    } elseif ($is_company_2_supervisor) { 
        $company_2_employees = $this->hr_model->get_employees_by_company(2);
    } elseif (!$is_hr_user) {
        $subordinate_ids = $this->hr_model->get_all_subordinates_ids($logged_in_username);
        if (!empty($subordinate_ids)) {
            $is_manager = true;
            $subordinates = $this->hr_model->get_employee_details_bulk($subordinate_ids);
        }
    }

    // 2. Determine target employee
    $target_username = $logged_in_username; // Default to self
    $searched_emp_id = $this->input->get('emp_id', true);

    if (!empty($searched_emp_id)) {
        if ($is_hr_user) {
            $target_username = $searched_emp_id;
        } elseif ($is_abha_supervisor) {
            $is_allowed = false;
            if ($searched_emp_id == $logged_in_username) {
                $is_allowed = true;
            } else {
                foreach ($abha_employees as $emp) {
                    if ($emp['username'] == $searched_emp_id) {
                        $is_allowed = true; break;
                    }
                }
            }
            if ($is_allowed) $target_username = $searched_emp_id;
            
        } elseif ($is_company_2_supervisor) {
            $is_allowed = false;
            if ($searched_emp_id == $logged_in_username) {
                $is_allowed = true; 
            } else {
                foreach ($company_2_employees as $emp) {
                    if ($emp['username'] == $searched_emp_id) {
                        $is_allowed = true; break;
                    }
                }
            }
            if ($is_allowed) $target_username = $searched_emp_id;

        } elseif ($is_manager) {
            $is_allowed = false;
            if ($searched_emp_id == $logged_in_username) {
                $is_allowed = true;
            } else {
                foreach ($subordinates as $sub) {
                    if ($sub['username'] == $searched_emp_id) {
                        $is_allowed = true; break;
                    }
                }
            }
            if ($is_allowed) $target_username = $searched_emp_id;
        }
    }
    
    $new_year_holiday_status = $this->hr_model->get_new_year_holiday_status($target_username);
    
    // 3. Set up date range for the report
    $year  = (int)($this->input->get('y') ?: date('Y'));
    $month = (int)($this->input->get('m') ?: date('n'));
    $holidaysMap = $this->hr_model->get_holidays_for_month($year, $month);

    $firstDay  = new DateTime(sprintf('%04d-%02d-01 00:00:00', $year, $month));
    $lastDay   = (clone $firstDay)->modify('last day of this month')->setTime(23,59,59);
    $firstDate = $firstDay->format('Y-m-d');
    $lastDate  = $lastDay->format('Y-m-d');

    // 4. Load rules
    $this->db->from('work_restrictions');
    $this->db->where('emp_id', $target_username);

    if ($this->db->field_exists('effective_date', 'work_restrictions')) {
        $this->db->where('effective_date <=', $firstDate);
        $this->db->order_by('effective_date', 'DESC'); 
    } else {
        $this->db->order_by('id', 'DESC');
    }

    $this->db->limit(1);
    $rules = $this->db->get()->row_array();

    if (!$rules && $this->db->field_exists('effective_date', 'work_restrictions')) {
         $rules = $this->db->where('emp_id', $target_username)
                           ->order_by('effective_date', 'ASC')
                           ->limit(1)
                           ->get('work_restrictions')
                           ->row_array();
    }

    $flexStart     = $rules['first_punch']   ?? '06:30';
    $flexEnd       = $rules['last_punch']    ?? '08:30';
    $workingHours  = (float)($rules['working_hours'] ?? 8.0);

    // 5. Fetch all attendance punches for the target employee (ORIGINAL SQL PRESERVED)
    $tables  = $this->get_attendance_tables();
    $daysMap = [];
    if (!empty($tables)) {
        $union = $this->build_attendance_union_sql(
            $tables, 
            $target_username,
            $firstDay->format('Y-m-d H:i:s'),
            $lastDay->format('Y-m-d H:i:s')
        );

        $sql = "SELECT 
                    DATE(punch_time) AS day,
                    MIN(CASE WHEN TIME(punch_time) BETWEEN '06:30:00' AND '12:59:59' THEN punch_time END) AS first_in,
                    MAX(CASE WHEN TIME(punch_time) BETWEEN '13:00:00' AND '20:59:59' THEN punch_time END) AS last_out,
                    SUM(CASE WHEN TIME(punch_time) BETWEEN '06:30:00' AND '12:59:59' THEN 1 ELSE 0 END) AS in_cnt,
                    SUM(CASE WHEN TIME(punch_time) BETWEEN '13:00:00' AND '20:59:59' THEN 1 ELSE 0 END) AS out_cnt
                FROM ($union) U
                GROUP BY DATE(punch_time)";
        $q = $this->db->query($sql);
        foreach ($q->result_array() as $r) {
            $daysMap[$r['day']] = [
                'first_in'  => $r['first_in'],
                'last_out'  => $r['last_out'],
                'in_cnt'    => (int)$r['in_cnt'],
                'out_cnt'   => (int)$r['out_cnt'],
                'valid_cnt' => (int)$r['in_cnt'] + (int)$r['out_cnt'],
            ];
        }
    }

    // 6. Fetch all requests (Vacations/Corrections)
    $eventsByDay = [];
    $correctionsByDay = [];
    $approvedLeaveDates = []; 
    $approvedEventsByDay = []; 

    $ordersSql = "
        SELECT id, type, status, vac_days_count,
               correction_date, attendance_correction, correction_of_departure,
               vac_start, vac_end, vac_half_date, vac_half_period
        FROM orders_emp
        WHERE emp_id = ? AND (
            (type = 2 AND correction_date BETWEEN ? AND ?) OR 
            (type = 5 AND (
                (vac_half_date BETWEEN ? AND ?) OR 
                (vac_start IS NOT NULL AND vac_end IS NOT NULL AND vac_start <= ? AND vac_end >= ?)
            ))
        )";
    $orders = $this->db->query($ordersSql, [
        $target_username,
        $firstDate, $lastDate,
        $firstDate, $lastDate,
        $lastDate, $firstDate
    ])->result_array();

    foreach ($orders as $o) {
        $status = (int)$o['status'];
        $vac_days_count = $o['vac_days_count'] ?? '';
        
        if ((int)$o['type'] === 2 && !empty($o['correction_date'])) {
            $d = $o['correction_date'];
            $in  = trim((string)$o['attendance_correction']);
            $out = trim((string)$o['correction_of_departure']);
            
            $eventsByDay[$d][] = [
                'type' => 'correction', 'status' => $status, 
                'title'  => 'تصحيح بصمة', 'in' => $in ?: null, 'out' => $out ?: null, 'id' => (int)$o['id']
            ];
            
            if ($status === 2) {
                if (!isset($correctionsByDay[$d])) $correctionsByDay[$d] = ['in'=>null,'out'=>null];
                if ($in)  $correctionsByDay[$d]['in']  = $in;
                if ($out) $correctionsByDay[$d]['out'] = $out;
                $approvedEventsByDay[$d] = true;
            }
        }
        
        if ((int)$o['type'] === 5) {
            if (!empty($o['vac_half_date']) && $vac_days_count == '0.5') {
                $d = $o['vac_half_date'];
                $period = trim((string)$o['vac_half_period']);
                $period_ar = ($period === 'am') ? 'صباحي' : (($period === 'pm') ? 'مسائي' : $period);
                
                $eventsByDay[$d][] = [
                    'type'    => 'vac_half', 'status' => $status, 
                    'title'  => 'إجازة نصف يوم', 'period' => $period_ar ?: '—', 'id' => (int)$o['id']
                ];
                
                if ($status === 2) {
                    $approvedLeaveDates[$d] = true;
                    $approvedEventsByDay[$d] = true;
                }
            }
            
            if (!empty($o['vac_start']) && !empty($o['vac_end']) && ($vac_days_count == '1' || empty($o['vac_half_date']))) {
                $start = max($o['vac_start'], $firstDate);
                $end   = min($o['vac_end'],   $lastDate);
                $cur   = new DateTime($start);
                $endDt = new DateTime($end);
                while ($cur <= $endDt) {
                    $d = $cur->format('Y-m-d');
                    $eventsByDay[$d][] = [
                        'type' => 'vac_full', 'status' => $status, 'title' => 'إجازة', 'id' => (int)$o['id']
                    ];
                    if ($status === 2) {
                        $approvedLeaveDates[$d] = true;
                        $approvedEventsByDay[$d] = true;
                    }
                    $cur->modify('+1 day');
                }
            }
        }
    }

    foreach ($correctionsByDay as $date => $correction) {
        if (!isset($daysMap[$date])) {
            $daysMap[$date] = ['first_in' => null, 'last_out' => null, 'in_cnt' => 0, 'out_cnt' => 0, 'valid_cnt' => 0];
        }
        if (!empty($correction['in'])) {
            $daysMap[$date]['first_in'] = $date . ' ' . $correction['in'] . ':00';
        }
        if (!empty($correction['out'])) {
            $daysMap[$date]['last_out'] = $date . ' ' . $correction['out'] . ':00';
        }
    }

    // =========================================================================
    // [START] RAMADAN INJECTION (Safely fetches required data without breaking original SQL)
    // =========================================================================
    $attendance_data = []; // Shared to view
    $ramadan_start = '2026-02-18';
    $ramadan_end   = '2026-03-19';

    if ($lastDate >= $ramadan_start && $firstDate <= $ramadan_end && !empty($tables)) {
        $extended_lastDay = date('Y-m-d 05:59:59', strtotime($lastDate . ' +2 days'));
        $union_raw = $this->build_attendance_union_sql($tables, $target_username, $firstDate . ' 00:00:00', $extended_lastDay);
        $raw_sql = "SELECT emp_code, punch_time FROM ($union_raw) U ORDER BY punch_time ASC";
        $raw_q = $this->db->query($raw_sql);
        
        $shift_punches = [];
        foreach ($raw_q->result_array() as $r) {
            $ts = strtotime($r['punch_time']);
            $hour = (int)date('H', $ts);
            // Group punches belonging to the 9AM-5AM Ramadan Shift safely
            $shift_date = ($hour < 6) ? date('Y-m-d', strtotime('-1 day', $ts)) : date('Y-m-d', $ts);
            $shift_punches[$shift_date][] = $ts;
            
            // Format for the UI View Blocks
            $obj = new stdClass();
            $obj->emp_code = $r['emp_code'];
            $obj->punch_time = $r['punch_time'];
            $attendance_data[] = $obj;
        }
        
        $emp_details = $this->db->select('profession')->where('employee_id', $target_username)->get('emp1')->row();
        $is_collector = ($emp_details && strpos($emp_details->profession, 'محصل') !== false);
        
        // Push raw punches safely into daysMap ONLY for Ramadan dates
        $curr_ts = strtotime($firstDate);
        $end_ts  = strtotime($lastDate);
        while ($curr_ts <= $end_ts) {
            $date = date('Y-m-d', $curr_ts);
            $curr_ts = strtotime('+1 day', $curr_ts);
            
            if ($date >= $ramadan_start && $date <= $ramadan_end) {
                $punches_today = $shift_punches[$date] ?? [];
                
                if (isset($correctionsByDay[$date])) {
                    if (!empty($correctionsByDay[$date]['in'])) $punches_today[] = strtotime($date . ' ' . $correctionsByDay[$date]['in']);
                    if (!empty($correctionsByDay[$date]['out'])) {
                        $corr_out = $correctionsByDay[$date]['out'];
                        if (strpos($corr_out, '00:') === 0 || strpos($corr_out, '01:') === 0 || strpos($corr_out, '02:') === 0 || strpos($corr_out, '03:') === 0 || strpos($corr_out, '04:') === 0 || strpos($corr_out, '05:') === 0) {
                            $punches_today[] = strtotime(date('Y-m-d', strtotime($date . ' +1 day')) . ' ' . $corr_out);
                        } else {
                            $punches_today[] = strtotime($date . ' ' . $corr_out);
                        }
                    }
                }
                
                $punches_today = array_unique($punches_today);
                sort($punches_today);
                
                if (!empty($punches_today)) {
                    if (!isset($daysMap[$date])) {
                        $daysMap[$date] = ['first_in' => null, 'last_out' => null, 'in_cnt' => 0, 'out_cnt' => 0, 'valid_cnt' => 0];
                    }
                    $daysMap[$date]['ram_punches'] = $punches_today;
                    $daysMap[$date]['is_collector'] = $is_collector;
                }
            }
        }
    }
    // =========================================================================
    // [END] RAMADAN INJECTION
    // =========================================================================


    // 7. Process all fetched data to apply rules and calculate violations
    $violationsByDay = [];
    $toDt = function($date, $hm){
        $hm = strlen($hm) === 5 ? $hm.':00' : $hm;
        return new DateTime($date.' '.$hm);
    };
    $minutesDiff = function(DateTime $a, DateTime $b){
        return max(0, (int)floor(($a->getTimestamp() - $b->getTimestamp())/60));
    };

    foreach ($daysMap as $date => $row) {
        if (isset($approvedLeaveDates[$date])) continue;

        $v = [];
        $is_ramadan_day = ($date >= $ramadan_start && $date <= $ramadan_end);

        if ($is_ramadan_day && isset($row['ram_punches'])) {
            // ==========================================
            // RAMADAN CALCULATIONS (Replaces Normal Logic safely)
            // ==========================================
            $punches_today = $row['ram_punches'];
            
            $is_half_day = false;
            if (isset($eventsByDay[$date])) {
                foreach ($eventsByDay[$date] as $ev) {
                    if ($ev['type'] === 'vac_half' && $ev['status'] == 2) {
                        $is_half_day = true; break;
                    }
                }
            }
            
            // Check Explicit Breastfeeding Rule from DB
            $is_breastfeeding_shift = (isset($rules['working_hours']) && (float)$rules['working_hours'] == 8.0);
            $base_ramadan_hours = $is_breastfeeding_shift ? 5.0 : 6.0;
            $working_hours_for_day = $is_half_day ? ($base_ramadan_hours / 2) : $base_ramadan_hours;
            
            $worked_hours = 0;
            $first_in = null;
            $last_out = null;
            
            if ($row['is_collector']) {
                $s1_punches = []; $s2_punches = [];
                
                foreach ($punches_today as $p) {
                    // Day Shift Window (06:00 to 17:59)
                    if ($p >= strtotime($date . ' 06:00:00') && $p <= strtotime($date . ' 17:59:59')) $s1_punches[] = $p;
                    // Night Shift Window (18:00 to 05:59 next day)
                    if ($p >= strtotime($date . ' 18:00:00') && $p <= strtotime($date . ' +1 day 05:59:59')) $s2_punches[] = $p;
                }

                $worked_s1 = count($s1_punches) >= 2 ? (max($s1_punches) - min($s1_punches)) / 3600 : 0;
                $worked_s2 = count($s2_punches) >= 2 ? (max($s2_punches) - min($s2_punches)) / 3600 : 0;
                
                // Total worked is simply the sum of both shifts
                $worked_hours = $worked_s1 + $worked_s2;

                $first_in = !empty($s1_punches) ? min($s1_punches) : (!empty($s2_punches) ? min($s2_punches) : null);
                $last_out = !empty($s2_punches) ? max($s2_punches) : (!empty($s1_punches) ? max($s1_punches) : null);
                $daysMap[$date]['first_in'] = $first_in ? date('Y-m-d H:i:s', $first_in) : date('Y-m-d H:i:s', $punches_today[0]);
                $daysMap[$date]['last_out'] = $last_out ? date('Y-m-d H:i:s', $last_out) : date('Y-m-d H:i:s', end($punches_today));
                $daysMap[$date]['valid_cnt'] = count($punches_today);
            
            } else {
                $shift_start = strtotime($date . ' 09:00:00');
                $shift_end   = strtotime($date . ' +1 day 02:00:00');
                
                $valid_pts = [];
                foreach ($punches_today as $p) {
                    if ($p >= strtotime($date . ' 06:00:00') && $p <= strtotime($date . ' +1 day 05:00:00')) $valid_pts[] = $p;
                }
                
                if (count($valid_pts) >= 2) {
                    $first_in = min($valid_pts);
                    $last_out = max($valid_pts);
                    $worked_hours = max(0, min($last_out, $shift_end) - max($first_in, $shift_start)) / 3600;
                } elseif (count($valid_pts) == 1) {
                    $first_in = min($valid_pts);
                }
                
                $daysMap[$date]['first_in'] = $first_in ? date('Y-m-d H:i:s', $first_in) : null;
                $daysMap[$date]['last_out'] = $last_out ? date('Y-m-d H:i:s', $last_out) : null;
                $daysMap[$date]['valid_cnt'] = count($valid_pts);
            }
            
            $vc = count($punches_today);
            if ($vc > 0) {
                if ($vc == 1 || ($first_in && $last_out && ($last_out - $first_in) < 60)) {
                    $v[] = ['type'=>'single', 'minutes'=>0, 'label'=>'بصمة منفردة'];
                } else {
                    $shortage_hours = $working_hours_for_day - $worked_hours;
                    if ($shortage_hours > 0.033) { // 2 mins tolerance
                        $v[] = ['type'=>'late_in', 'minutes'=>round($shortage_hours * 60), 'label'=>'نقص ساعات']; 
                    }
                }
            }
            if (!empty($v)) $violationsByDay[$date] = $v;
            continue; // Skip the old code to prevent double calculating!
        }
        
        // ==========================================
        // ORIGINAL NORMAL LOGIC (100% UNTOUCHED)
        // ==========================================
        $firstIn = $row['first_in'] ? new DateTime($row['first_in']) : null;
        $lastOut = $row['last_out'] ? new DateTime($row['last_out']) : null;

        $day_of_week = date('w', strtotime($date));
        if (!isset($this->hr_model)) {
            $this->load->model('hr_model');
        }
        // Check if this specific date is a mandatory Saturday for this user
        $is_saturday_work = ($day_of_week == 6 && $this->hr_model->is_mandatory_saturday($target_username, $date));

        $current_flexEnd = $is_saturday_work ? '11:00' : $flexEnd;
        $current_workingHours = $is_saturday_work ? 6.0 : $workingHours;
        
        // Allow entry from 11 AM for Saturdays, 6:30 AM for others
        $valid_in_start_time = $is_saturday_work ? (11*60) : (6*60+30);

        $toMin = function(DateTime $dt){ return (int)$dt->format('H')*60 + (int)$dt->format('i'); };
        
        $validIn  = $firstIn && $toMin($firstIn)  >= $valid_in_start_time && $toMin($firstIn)  <= (12*60+59);
        $validOut = $lastOut && $toMin($lastOut)  >= (13*60)   && $toMin($lastOut)  <= (20*60+59);

        // 1. Single Punch Check
        if (($validIn xor $validOut) || ($row['valid_cnt'] === 1)) {
            $v[] = ['type'=>'single', 'minutes'=>0, 'label'=>'بصمة منفردة'];
        }

        // 2. Lateness Check
        $flexEndDt = null;
        try { 
            $flexEndDt = $toDt($date, $current_flexEnd); 
        } catch (\Exception $e) {}
        
        if (!$is_saturday_work && $validIn && $flexEndDt && $firstIn > $flexEndDt) {
            $mins = $minutesDiff($firstIn, $flexEndDt);
            if ($mins > 0) $v[] = ['type'=>'late_in', 'minutes'=>$mins, 'label'=>'دخول متأخر'];
        }

        // 3. Early Out Check
        if (!$is_saturday_work && $validIn) {
            $h = (int)floor($current_workingHours);
            $m = (int)round(($current_workingHours - $h) * 60);
            $requiredEnd = (clone $firstIn)->modify("+{$h} hours")->modify("+{$m} minutes");
            
            if ($validOut && $lastOut < $requiredEnd) {
                $mins = $minutesDiff($requiredEnd, $lastOut);
                if ($mins > 0) $v[] = ['type'=>'early_out', 'minutes'=>$mins, 'label'=>'خروج مبكر'];
            }
        }

        if (!empty($v)) $violationsByDay[$date] = $v;
    }

    // 8. Prepare final data array to be sent to the view
    $prev = (clone $firstDay)->modify('-1 month');
    $next = (clone $firstDay)->modify('+1 month');
    $target_user_details = $this->db->get_where('users', ['username' => $target_username])->row();
    $target_name = $target_user_details ? $target_user_details->name : $target_username;

    $data = [
        'is_hr_user'      => $is_hr_user,
        'is_manager'      => $is_manager, 
        'subordinates'    => $subordinates, 
        'is_abha_supervisor' => $is_abha_supervisor,
        'abha_employees'  => $abha_employees,
        'target_username' => $target_username,
        'target_name'     => $target_name,
        'year'            => $year,
        'month'           => $month,
        'daysMap'         => $daysMap,
        'eventsByDay'     => $eventsByDay,
        'approvedEventsByDay' => $approvedEventsByDay,
        'is_company_2_supervisor' => $is_company_2_supervisor,
        'company_2_employees'     => $company_2_employees,
        'violationsByDay' => $violationsByDay,
        'holidaysMap'     => $holidaysMap,
        'prevY'           => (int)$prev->format('Y'),
        'prevM'           => (int)$prev->format('n'),
        'nextY'           => (int)$next->format('Y'),
        'nextM'           => (int)$next->format('n'),
        'name'            => html_escape($this->session->userdata('name') ?? ''),
        'username'        => html_escape($logged_in_username),
        'csrf_token_name' => $this->security->get_csrf_token_name(),
        'csrf_hash'       => $this->security->get_csrf_hash(),
        'flexStart'       => $flexStart,
        'flexEnd'         => $flexEnd,
        'workingHours'    => $workingHours,
        'new_year_holiday_status' => $new_year_holiday_status,
        'attendance_data' => $attendance_data ?? []
    ];

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/attendance_calendar', $data ?? []);
$this->load->view('template/new_footer');
}
public function process_past_fingerprints() {
    // --- SECURITY CHECK ---
    $secretKey = "mySecret123"; // CHANGE THIS to a strong, random key
    $urlKey = $this->input->get('key');
    $loggedInUser = $this->session->userdata('username');
    // Allow only specific users OR if the correct key is provided
     $allowed_users = ['2774']; // Add specific HR admin IDs allowed to run this
     if ($urlKey !== $secretKey && !in_array($loggedInUser, $allowed_users)) {
       show_error('Unauthorized access.', 403);
       return;
     }
    // --- END SECURITY CHECK ---

    $startDate = $this->input->get('start');
    $endDate = $this->input->get('end');

    // Validate dates (simple check)
    if (empty($startDate) || empty($endDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        echo "<h1>Error</h1><p>Please provide valid start and end dates in YYYY-MM-DD format in the URL.</p>";
        echo "<p>Example: ?start=2025-09-01&end=2025-09-30&key=YourSecretKey</p>";
        return;
    }

    $this->load->model('hr_model');

    echo "<h1>Processing Past Approved Fingerprint Corrections</h1>";
    echo "<p>Date Range: " . html_escape($startDate) . " to " . html_escape($endDate) . "</p>";
    echo "<hr>";

    $requestsToProcess = $this->hr_model->get_past_approved_fingerprint_requests($startDate, $endDate);

    if (empty($requestsToProcess)) {
        echo "<p style='color: green;'>No approved fingerprint correction requests found in the specified date range that need processing.</p>";
        return;
    }

    echo "<p>Found " . count($requestsToProcess) . " requests to process...</p>";
    echo "<ul>";

    $processedCount = 0;
    $errorCount = 0;

    foreach ($requestsToProcess as $request) {
        echo "<li>Processing Request ID: " . $request['id'] . " (Emp: " . $request['emp_id'] . ", Date: " . $request['correction_date'] . ") ... ";
        // Call the model function to handle insertion for this single request
        if ($this->hr_model->process_single_past_request_logs($request)) {
            echo "<span style='color: green;'>OK</span></li>";
            $processedCount++;
        } else {
            echo "<span style='color: red;'>ERROR</span></li>";
            $errorCount++;
        }
        // Optional: Add a small delay if processing many records
        // usleep(50000); // 50 milliseconds
    }

    echo "</ul>";
    echo "<hr>";
    echo "<p><strong>Processing Complete.</strong></p>";
    echo "<p style='color: green;'>Successfully processed (logs inserted): " . $processedCount . "</p>";
    if ($errorCount > 0) {
        echo "<p style='color: red;'>Errors encountered: " . $errorCount . " (Check application logs for details)</p>";
    }
}
public function public_holidays()
{
    // Optional: Add security check for HR users
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }
    
    $this->load->model('hr_model');
    $data['holidays'] = $this->hr_model->get_all_holidays();
    
    // Load a new view file we will create next
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/public_holidays_view', $data ?? []);
$this->load->view('template/new_footer');
}

// Handles the form submission for adding a new holiday
// In Users1.php
// In Users1.php

public function add_holiday()
{
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
        redirect('users1/public_holidays');
    }

    $this->load->model('hr_model');
    $data = [
        'holiday_name' => $this->input->post('holiday_name'),
        'holiday_date' => $this->input->post('holiday_date') // Only one date field now
    ];

    if ($this->hr_model->add_new_holiday($data)) {
        $this->session->set_flashdata('success', 'تمت إضافة العطلة الرسمية بنجاح.');
    } else {
        $this->session->set_flashdata('error', 'فشلت الإضافة. قد يكون هذا التاريخ مضاف مسبقاً.');
    }
    redirect('users1/public_holidays');
}
// Deletes a holiday
public function delete_holiday($id)
{
    $this->load->model('hr_model');
    if ($this->hr_model->delete_holiday_by_id($id)) {
        $this->session->set_flashdata('success', 'تم حذف العطلة بنجاح.');
    } else {
        $this->session->set_flashdata('error', 'فشل الحذف.');
    }
    redirect('users1/public_holidays');
}

        
// In Users1.php
// In Users1.php

// In Users1.php

public function attendance_day()
{
    // 1. Ensure it's an AJAX request
    if (!$this->input->is_ajax_request()) { 
        show_404(); 
        return; 
    }

    // 2. Check login
    $logged_in_username = $this->session->userdata('username');
    if (!$logged_in_username) {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok'=>false,'msg'=>'لم يتم تسجيل الدخول']));
        return;
    }
    
    $this->load->model('hr_model'); 

    // 3. Determine Target Employee
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    $is_hr_user = in_array($logged_in_username, $hr_users);
    $is_abha_supervisor = ($logged_in_username == '2694');
    $is_company_2_supervisor = ($logged_in_username == '1136');
    
    $employee_to_check = $this->input->post('emp_id');
    $target_username = $logged_in_username; // Default to self

    if (!empty($employee_to_check)) {
        if ($is_hr_user) {
            // HR can check anyone
            $target_username = $employee_to_check;

        } elseif ($is_abha_supervisor) {
            // Abha supervisor logic (2694)
            if ($employee_to_check == $logged_in_username || $this->hr_model->is_employee_in_location($employee_to_check, 'أبها')) {
                $target_username = $employee_to_check;
            }

        } elseif ($is_company_2_supervisor) { 
            // --- NEW: Logic for 1136 (Company 2) ---
            if ($employee_to_check == $logged_in_username || $this->hr_model->is_employee_in_company($employee_to_check, 2)) {
                $target_username = $employee_to_check;
            }

        } elseif ($employee_to_check != $logged_in_username) {
            // Standard manager logic
            $subordinate_ids = $this->hr_model->get_all_subordinates_ids($logged_in_username);
            if (!empty($subordinate_ids) && in_array($employee_to_check, $subordinate_ids)) {
                $target_username = $employee_to_check;
            } 
        }
    }

    // 4. Validate Date
    $date = $this->input->post('date');
    if (!preg_match('#^\d{4}-\d{2}-\d{2}$#', (string)$date)) {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok'=>false,'msg'=>'تنسيق تاريخ غير صحيح']));
        return;
    }

    // =========================================================
    // RAMADAN TIME WINDOW LOGIC
    // =========================================================
    $is_ramadan = (strtotime($date) >= strtotime('2026-02-18') && strtotime($date) <= strtotime('2026-03-19'));
    
    if ($is_ramadan) {
        $start_time = $date . ' 06:00:00';
        $end_time   = date('Y-m-d', strtotime($date . ' +1 day')) . ' 05:59:59';
    } else {
        $start_time = $date . ' 00:00:00';
        $end_time   = $date . ' 23:59:59';
    }

    // 5. Fetch Detailed Punches (Device/Mobile)
    $tables = [];
    $query = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'attendance_logs%'");
    if ($query) {
        foreach ($query->result_array() as $r) { $tables[] = $r['TABLE_NAME']; }
    }
    if (!in_array('attendance_logs', $tables)) { array_unshift($tables, 'attendance_logs'); }
    $tables = array_unique($tables);

    $punches = [];
    
    if (!empty($tables)) {
        $unions = [];
        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                // FIXED: Changed DATE() matching to BETWEEN using the dynamic Ramadan window
                $unions[] = sprintf(
                    "SELECT punch_time, punch_state, terminal_alias, area_alias, 'device' as source 
                     FROM `%s` 
                     WHERE emp_code = %s AND punch_time BETWEEN %s AND %s",
                    $table,
                    $this->db->escape($target_username),
                    $this->db->escape($start_time),
                    $this->db->escape($end_time)
                );
            }
        }
        
        if (!empty($unions)) {
            $union_sql = implode(" UNION ALL ", $unions);
            // Order by time to show sequence correctly
            $query = $this->db->query("$union_sql ORDER BY punch_time ASC");
            $punches = $query->result_array();
        }
    }

    // 6. Fetch Approved Correction Requests (Manual)
    $this->db->select('attendance_correction, correction_of_departure');
    $this->db->from('orders_emp');
    $this->db->where('emp_id', $target_username);
    $this->db->where('type', 2); // Fingerprint Correction
    $this->db->where('status', '2'); // Approved
    $this->db->where('correction_date', $date);
    $correction = $this->db->get()->row_array();

    // 7. Build the Response Data
    $details_list = [];

    // Process Device/Mobile Punches
    foreach ($punches as $p) {
        // Show full date and time format (e.g. 2026-02-26 02:33 AM) for clarity on cross-midnight punches
        $time = date('Y-m-d h:i A', strtotime($p['punch_time']));
        
        // Determine Source Name
        $device_name = $p['terminal_alias'];
        $area_alias = $p['area_alias'] ?? '';
        
        // Logic to detect Mobile vs Device
        if (empty($device_name) || stripos($device_name, 'Mobile') !== false || stripos($area_alias, 'Mobile') !== false) {
            $source_label = '<span class="badge bg-purple" style="background-color: #6f42c1;">تطبيق الجوال</span>';
            $device_text = $device_name ?: 'تطبيق الجوال';
        } else {
            $source_label = '<span class="badge bg-secondary">جهاز بصمة</span>';
            $device_text = $device_name;
        }

        // Determine State (Check In/Out)
        $state_val = $p['punch_state'];
        if ($state_val == 'Check In' || $state_val == '0' || $state_val == 'دخول') {
            $state_trans = 'دخول';
        } elseif ($state_val == 'Check Out' || $state_val == '1' || $state_val == 'خروج') {
            $state_trans = 'خروج';
        } else {
            $state_trans = $state_val; 
        }

        $details_list[] = [
            'time' => $time,
            'type' => $state_trans,
            'source_html' => $source_label,
            'device_name' => $device_text
        ];
    }

    // Process Correction Data (if exists)
    if ($correction) {
        if (!empty($correction['attendance_correction'])) {
            $details_list[] = [
                'time' => $date . ' ' . $correction['attendance_correction'],
                'type' => 'دخول',
                'source_html' => '<span class="badge bg-warning text-dark">طلب تصحيح</span>',
                'device_name' => 'تعديل يدوي (معتمد)'
            ];
        }
        if (!empty($correction['correction_of_departure'])) {
            // Handle cross-midnight correction of departure intelligently
            $corr_out = $correction['correction_of_departure'];
            if ($is_ramadan && (strpos($corr_out, '00:') === 0 || strpos($corr_out, '01:') === 0 || strpos($corr_out, '02:') === 0 || strpos($corr_out, '03:') === 0 || strpos($corr_out, '04:') === 0 || strpos($corr_out, '05:') === 0)) {
                $corr_out_time = date('Y-m-d', strtotime($date . ' +1 day')) . ' ' . $corr_out;
            } else {
                $corr_out_time = $date . ' ' . $corr_out;
            }

            $details_list[] = [
                'time' => $corr_out_time,
                'type' => 'خروج',
                'source_html' => '<span class="badge bg-warning text-dark">طلب تصحيح</span>',
                'device_name' => 'تعديل يدوي (معتمد)'
            ];
        }
    }

    // Sort everything by actual timestamp so the list is strictly chronological
    usort($details_list, function($a, $b) {
        return strtotime($a['time']) - strtotime($b['time']);
    });

    // 8. Generate Top Summary Note
    $firstDisp = '—';
    $lastDisp = '—';
    
    if (!empty($details_list)) {
        // Extract just the H:i A component for the short summary header
        $firstDisp = date('h:i A', strtotime($details_list[0]['time']));
        if (count($details_list) > 1) {
            $lastDisp = date('h:i A', strtotime($details_list[count($details_list) - 1]['time']));
        }
    }

    $note = "<div>وقت الدخول: <strong>{$firstDisp}</strong> &nbsp;•&nbsp; وقت الخروج: <strong>{$lastDisp}</strong></div>";
    
    if (count($details_list) === 1 && !$correction) {
        $note .= " <small class='text-warning'>(بصمة واحدة)</small>";
    }

    // 9. Final Response
    $resp = [
        'ok' => true,
        'date' => $date,
        'has' => !empty($details_list),
        'note' => $note,
        'punch_details' => $details_list 
    ];

    $this->output->set_content_type('application/json')->set_output(json_encode($resp));
}
// In application/controllers/Users1.php
// In application/controllers/Users1.php

// --- MANAGER SIDE: ASSIGN TASKS ---
public function task_manager_dashboard()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $manager_id = $this->session->userdata('username');
    $this->load->model('hr_model');

    // 1. Get Subordinates for the Dropdown
    $subordinate_ids = $this->hr_model->get_all_subordinates_ids($manager_id);
    
    // Also allow assigning to self? Optional. 
    // If HR, get everyone.
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    if (in_array($manager_id, $hr_users)) {
        $data['subordinates'] = $this->hr_model->get_all_employees();
    } else {
        $data['subordinates'] = !empty($subordinate_ids) ? $this->hr_model->get_employee_details_bulk($subordinate_ids) : [];
    }

    // 2. Get Created Tasks List
    $data['my_created_tasks'] = $this->hr_model->get_tasks_created_by_manager($manager_id);
    
    $data['csrf_token_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/task_manager_view', $data ?? []);
$this->load->view('template/new_footer');
}

public function ajax_create_task()
{
    if (!$this->input->is_ajax_request()) { exit('No access'); }
    $this->load->model('hr_model');

    $data = [
        'manager_id'       => $this->session->userdata('username'),
        'employee_id'      => $this->input->post('employee_id'),
        'task_title'       => $this->input->post('task_title'),
        'task_description' => $this->input->post('task_description'),
        'start_date'       => $this->input->post('start_date'),
        'due_date'         => $this->input->post('due_date'),
        'status'           => 'pending'
    ];

    if ($this->hr_model->create_task($data)) {
        echo json_encode(['status' => 'success', 'message' => 'تم إرسال المهمة للموظف بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل الحفظ']);
    }
}

// --- EMPLOYEE SIDE: MY TASKS ---
public function my_tasks_dashboard()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $employee_id = $this->session->userdata('username');
    $this->load->model('hr_model');

    $data['my_tasks'] = $this->hr_model->get_tasks_assigned_to_employee($employee_id);
    
    $data['csrf_token_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/my_tasks_view', $data ?? []);
$this->load->view('template/new_footer');
}
// In application/controllers/Users1.php

public function ajax_task_action()
{
    if (!$this->input->is_ajax_request()) { exit('No access'); }
    $this->load->model('hr_model');

    $task_id = $this->input->post('task_id');
    $action_type = $this->input->post('action_type');
    $update_data = [];
    $message = '';

    switch ($action_type) {
        case 'update_status':
            $new_status = $this->input->post('status');
            $update_data = ['status' => $new_status];
            $message = 'تم تحديث حالة المهمة بنجاح.';
            break;

        case 'add_note':
            $note = $this->input->post('note');
            $update_data = ['employee_notes' => $note];
            $message = 'تم حفظ الملاحظات.';
            break;

        case 'reject_task':
            $reason = $this->input->post('reason');
            $update_data = [
                'status' => 'rejected',
                'rejection_reason' => $reason
            ];
            $message = 'تم رفض المهمة.';
            break;

        case 'request_extension':
            $new_date = $this->input->post('new_date');
            $reason = $this->input->post('reason');
            $update_data = [
                'extension_requested_date' => $new_date,
                'extension_reason' => $reason,
                'is_extension_requested' => 1
            ];
            $message = 'تم إرسال طلب تمديد الموعد للمشرف.';
            break;
    }

    if (!empty($update_data) && $this->hr_model->update_task_attributes($task_id, $update_data)) {
        echo json_encode(['status' => 'success', 'message' => $message]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء الحفظ.']);
    }
}

// In application/controllers/Users1.php

// In application/controllers/Users1.php

public function export_bank_payroll_csv($sheet_id)
{
    // 1. Security Checks
    if (!$this->session->userdata('logged_in')) { 
        redirect('users/login'); 
    }
    
    $hr_users = ['1835', '2230', '2515', '2774', '2784', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('Unauthorized', 403);
    }

    $this->load->model('hr_model');
    $this->load->helper('download');

    // 2. Fetch Data
    $sheet_info = $this->hr_model->get_salary_sheet($sheet_id);
    if (!$sheet_info) { show_error('Sheet not found', 404); }

    $sheet_start_date = $sheet_info['start_date'];
    $sheet_end_date   = $sheet_info['end_date'];
    
    // Prepare Dates for Proration Logic
    try {
        $sheet_start_dt = new DateTime($sheet_start_date);
        $sheet_end_dt   = new DateTime($sheet_end_date);
        $payroll_start_day = (int)$sheet_start_dt->format('j');
    } catch (Exception $e) { 
        $payroll_start_day = 16; // Default fallback
    }
    
    // Filter by Company
    $selected_company = $this->input->get('company', TRUE);
    $employees = $this->hr_model->get_active_employees();
    
    if (!empty($selected_company)) {
        $selected_company = trim($selected_company);
        $employees = array_filter($employees, function($row) use ($selected_company) {
            $db_company_normalized = trim(preg_replace('/\s+/', ' ', $row->company_name));
            return $db_company_normalized === $selected_company;
        });
        $employees = array_values($employees);
    }

    if (empty($employees)) {
        show_error('No employees found', 404);
    }

    $employee_ids = array_map(function($e) { return $e->employee_id; }, $employees);
    $data_map = $this->hr_model->get_payroll_data_maps($employee_ids, $sheet_id, $sheet_start_date, $sheet_end_date);

    // 3. Prepare CSV
    $filename = "Bank_Payroll_" . $sheet_id . "_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    
    $output = fopen('php://output', 'w');
    
    // Bank Format Headers
    fputcsv($output, [
        'Bank Name', 
        'Account Number(34N)', // IBAN
        'Employee Name',
        'Employee Number',
        'National ID Number',
        'Salary (15N)', // Basic + Housing
        'Basic Salary',
        'Housing Allowance',
        'Other Earnings', // Reparations + Prev Month + Transport + Other Allow
        'Deductions'      // Insurance + All Attendance + All Discounts
    ]);

    // 4. Process Rows
    foreach ($employees as $row) {
        $emp_id = $row->employee_id;
        
        // --- A. Initialize Base Values ---
        $full_time_salary = (float)($row->total_salary ?? 0);
        $base_salary = (float)($row->base_salary ?? 0);
        $housing_allowance = (float)($row->housing_allowance ?? 0);
        
        // Fixed Allowances (Transport, etc.)
        $transport = (float)($row->n4 ?? 0);
        $other_fixed_allow = (float)($row->other_allowances ?? 0) + (float)($row->n7 ?? 0); // n7 is communication often

        // Dynamic Data
        $reparations_amount = $data_map['reparations'][$emp_id] ?? 0.0;
        $discount_amount = $data_map['discounts'][$emp_id] ?? 0.0;
        $previous_month_comp = 0.0; 
        $hiring_day_difference = 0.0;
        $is_new_employee = false;
        $skip_this_month = false;
        
        // --- B. New Employee Logic (Proration) ---
        $new_emp_details = $data_map['new_employees'][$emp_id] ?? null;
        if ($new_emp_details && !empty($new_emp_details->join_date)) {
            try {
                $join_dt = new DateTime(str_replace('/', '-', trim($new_emp_details->join_date)));
                $join_day = (int)$join_dt->format('j');

                if ($join_dt >= $sheet_start_dt && $join_dt <= $sheet_end_dt) {
                    $is_new_employee = true;
                    if ($join_day >= $payroll_start_day) {
                        // Policy 2: >= 16th (Paid extra next month or calculated differently)
                        $days_in_current_month = max(1, 30 - $join_day + 1);
                        $actual_worked_days = $days_in_current_month + 30;
                        $salary_for_this_period = ($full_time_salary / 30) * $actual_worked_days;
                    } else {
                        // Policy 1: < 16th
                        $actual_worked_days = max(1, 30 - $join_day + 1);
                        $salary_for_this_period = ($full_time_salary / 30) * $actual_worked_days;
                    }
                    $hiring_day_difference = $full_time_salary - $salary_for_this_period;
                } elseif ($join_dt > $sheet_end_dt) {
                    $skip_this_month = true;
                    $hiring_day_difference = $full_time_salary; // Deduct full amount
                }
            } catch (Exception $e) {}
        }

        // --- C. Attendance Deductions Calculation ---
        // Recalculate rates
        $rule_row = $data_map['rules'][$emp_id] ?? null;
        $default_working_hours = 8.0; 
        if ($rule_row && !empty($rule_row->working_hours)) {
             $wh_raw = trim((string)$rule_row->working_hours);
             $default_working_hours = (float)$wh_raw > 0 ? (float)$wh_raw : 8.0;
        }

        $daily_salary = $full_time_salary > 0 ? $full_time_salary / 30 : 0;
        $minute_salary = ($default_working_hours > 0) ? ($daily_salary / $default_working_hours / 60) : 0;

        // Get Attendance Data
        $summary = $data_map['attendance'][$emp_id] ?? null;
        $unpaid_leave_days = $data_map['unpaid_leave'][$emp_id] ?? 0;
        $half_day_vacations = $data_map['half_day'][$emp_id] ?? 0;
        
        $absence_total = $summary ? (int)$summary->absence : 0;
        $pure_absence_days = max(0, $absence_total - $unpaid_leave_days);
        $minutes_late = $summary ? (int)$summary->minutes_late : 0;
        $minutes_early = $summary ? (int)$summary->minutes_early : 0;
        $single_punch = $summary ? (int)$summary->single_thing : 0;

        // Calculate Money Values
        $deduct_absence = $daily_salary * $pure_absence_days;
        $deduct_unpaid  = $daily_salary * $unpaid_leave_days;
        $deduct_single  = $daily_salary * $single_punch;
        $deduct_late    = $minute_salary * $minutes_late;
        $deduct_early   = $minute_salary * $minutes_early;
        $deduct_half_day= ($daily_salary / 2) * $half_day_vacations;

        $total_attendance_deductions = $deduct_absence + $deduct_unpaid + $deduct_single + $deduct_late + $deduct_early + $deduct_half_day;

        // Previous Month Deductions (if any logic exists for it, usually 0 unless specified)
        $prev_deductions = 0.0; // Assuming 0 for now unless you have specific logic to pass it here

        // Insurance (GOSI)
        $insurance_deduction = 0.0;
        $discount_rate = $data_map['insurance'][$emp_id] ?? 0.0;
        if (!$skip_this_month && $discount_rate > 0 && $row->nationality === 'سعودي') {
            $gosi_base = $base_salary + $housing_allowance;
            $insurance_deduction = min($gosi_base, 45000) * $discount_rate;
        }

        // --- D. CONSOLIDATION (THE FIX) ---
        
        // 1. Hiring Diff Split
        $hiring_pos = ($hiring_day_difference < 0) ? abs($hiring_day_difference) : 0; // If we owe them money (e.g. Policy 2)
        $hiring_neg = ($hiring_day_difference > 0) ? $hiring_day_difference : 0; // If they worked less than full month

        // 2. Other Earnings Column
        // Must include Fixed Allowances + Reparations + Backpay + Hiring Bonus
        $other_earnings_total = $transport 
                              + $other_fixed_allow 
                              + $reparations_amount 
                              + $previous_month_comp 
                              + $hiring_pos;

        // 3. Deductions Column
        // Must include Insurance + ALL Attendance + Dynamic Discounts + Hiring Deduction
        $deductions_total = $insurance_deduction 
                          + $total_attendance_deductions 
                          + $prev_deductions 
                          + $discount_amount 
                          + $hiring_neg;

        // Skip row if salary stopped
        if (isset($data_map['stopped'][$emp_id]) || $skip_this_month) {
            $base_salary = 0; $housing_allowance = 0; $other_earnings_total = 0; $deductions_total = 0;
        }

        // --- E. Write Row ---
        // --- E. Write Row ---
fputcsv($output, [
    $row->n3, // Bank Name
    $row->n2, // IBAN
    $row->subscriber_name,
    $row->employee_id,
    $row->id_number,
    
    // FIX: Use full_time_salary (Total Contractual Salary) without any changes
    number_format($full_time_salary, 2, '.', ''), 

    number_format($base_salary, 2, '.', ''),
    number_format($housing_allowance, 2, '.', ''),
    number_format($other_earnings_total, 2, '.', ''), // REPARATIONS & ADDITIONS
    number_format($deductions_total, 2, '.', '')      // DEDUCTIONS & DISCOUNTS
]);
    }

    fclose($output);
    exit;
}
// In application/controllers/Users1.php
// In application/controllers/Users1.php

public function team_balances_dashboard()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $logged_in_user = $this->session->userdata('username');
    $this->load->model('hr_model');

    // 1. Determine Permissions
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    $is_hr = in_array($logged_in_user, $hr_users);
    $is_abha_supervisor = ($logged_in_user == '2694');

    $target_ids = [];

    if ($is_hr) {
        // HR sees EVERYONE
        $all_emps = $this->hr_model->get_active_employees();
        $target_ids = array_column($all_emps, 'employee_id');
    } elseif ($is_abha_supervisor) {
        // Abha Logic (Self + Abha Location)
        $target_ids = [$logged_in_user]; 
        // Add logic to fetch abha employees IDs here if needed
        // $abha_ids = ...
        // $target_ids = array_merge($target_ids, $abha_ids);
    } else {
        // Manager Logic (Self + Subordinates)
        $subordinates = $this->hr_model->get_all_subordinates_ids($logged_in_user);
        $target_ids = array_merge([$logged_in_user], $subordinates);
    }

    // 2. Fetch Data
    $data['team_balances'] = $this->hr_model->get_balances_for_list(array_unique($target_ids));
    
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/team_balances_view', $data ?? []);
$this->load->view('template/new_footer');
}
public function leave_capacity_dashboard()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $manager_id = $this->session->userdata('username');
    $this->load->model('hr_model');
    
    // Check if HR (can see all) or Manager (specific view)
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    
    if (in_array($manager_id, $hr_users)) {
        // HR sees Global Stats (using the previous function I gave you, or create a global version)
        // For simplicity, let's assume get_department_leave_stats handles "All" if no ID passed or different function
        $data['dept_stats'] = $this->hr_model->get_department_leave_stats(25); 
    } else {
        // Manager sees only their team's stats
        $data['dept_stats'] = $this->hr_model->get_subordinate_leave_stats($manager_id, 25);
    }
    
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/leave_capacity_view', $data ?? []);
$this->load->view('template/new_footer');
}
public function ajax_update_task_status()
{
    if (!$this->input->is_ajax_request()) { exit('No access'); }
    $this->load->model('hr_model');

    $task_id = $this->input->post('task_id');
    $new_status = $this->input->post('status'); // 'in_progress' or 'completed'

    if ($this->hr_model->update_task_status($task_id, $new_status)) {
        echo json_encode(['status' => 'success', 'message' => 'تم تحديث الحالة']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل التحديث']);
    }
}
public function check_resigned_employees()
{
    // Security Check
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->model('hr_model');
    
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $resigned_list = $this->hr_model->get_past_due_resignations($start_date, $end_date);

    echo json_encode([
        'status' => 'success',
        'data' => $resigned_list,
        'count' => count($resigned_list),
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
public function export_employees_excel()
{
    if (!$this->session->userdata('logged_in')) redirect('users/login');
    $this->load->model('hr_model');
    $this->load->dbutil();
    $this->load->helper('download'); 

    // Capture GET params into POST for model compatibility
    $_POST['filter_employee_id'] = $this->input->get('filter_employee_id');
    $_POST['filter_id_number']   = $this->input->get('filter_id_number');
    $_POST['filter_name']        = $this->input->get('filter_name');
    $_POST['filter_status']      = $this->input->get('filter_status');
    
    // NEW FILTERS
    $_POST['filter_department']  = $this->input->get('filter_department');
    $_POST['filter_company']     = $this->input->get('filter_company');
    $_POST['filter_manager']     = $this->input->get('filter_manager');
    $_POST['filter_location']    = $this->input->get('filter_location');
    $_POST['filter_position']    = $this->input->get('filter_position');
    $_POST['filter_nationality'] = $this->input->get('filter_nationality');
    $_POST['filter_gender']      = $this->input->get('filter_gender');
    
    $_POST['length'] = -1; 
    $_POST['start'] = 0;

    $query = $this->hr_model->get_all_employees_for_export();
    $csv_data = $this->dbutil->csv_from_result($query, ",", "\r\n");
    force_download("employees_export_" . date('Y-m-d') . ".csv", "\xEF\xBB\xBF" . $csv_data);
}
private function get_attendance_tables(): array
{
    $sql = "SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME LIKE 'attendance_logs%'";
    $rows = $this->db->query($sql, [$this->db->database])->result_array();

    $tables = [];
    foreach ($rows as $r) {
        $t = $r['TABLE_NAME'];
        $chk = $this->db->query(
            "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_SCHEMA=? AND TABLE_NAME=? 
               AND COLUMN_NAME IN ('emp_code','punch_time')",
            [$this->db->database, $t]
        )->row()->c ?? 0;
        if ($chk >= 2) $tables[] = $t;
    }
    return $tables;
}

private function build_attendance_union_sql(array $tables, string $username, string $from, string $to): string
{
    $parts = [];
    foreach ($tables as $t) {
        $parts[] = sprintf(
            "SELECT emp_code, punch_time FROM `%s`
             WHERE emp_code = %s AND punch_time BETWEEN %s AND %s",
            str_replace('`','``',$t),
            $this->db->escape($username),
            $this->db->escape($from),
            $this->db->escape($to)
        );
    }
    return implode(" UNION ALL ", $parts);
}

// In application/controllers/Users1.php

/**
 * Displays the details page for a single employee request.
 * @param int $request_id The primary key of the request to view.
 */
// In application/controllers/Users1.php

public function view_request($request_id)
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');

    $data['request'] = $this->hr_model->get_request_details($request_id);

    if (empty($data['request'])) {
        show_404();
    }
// =========================================================
    // START: Calculate Estimated Overtime Amount
    // =========================================================
$data['ot_estimated_amount'] = 0;

    // 1. First, try to use the stored amount from the database (The 532)
    if (!empty($data['request']['ot_amount']) && $data['request']['ot_amount'] > 0) {
        $data['ot_estimated_amount'] = (float)$data['request']['ot_amount'];
    } 
    // 2. If no stored amount, calculate it (Legacy support)
    elseif (isset($data['request']['type']) && $data['request']['type'] == 3) {
        $emp_id = $data['request']['emp_id'];
        $hours = (float)($data['request']['ot_hours'] ?? 0);
        
        $salary_details = $this->hr_model->get_salary_calculation_details($emp_id, 0);
        
        if (!empty($salary_details['actual_hourly_salary'])) {
            $total_salary = $salary_details['total_salary'] ?? 0;
            $basic_salary = $salary_details['base_salary'] ?? 0;
            
            $overtime_minutes = $hours * 60;
            $divisor = 14400; // 30 * 8 * 60
            
            $A = ($total_salary / $divisor) * $overtime_minutes;
            $B = (($basic_salary / 2) / $divisor) * $overtime_minutes;
            
            $data['ot_estimated_amount'] = round($A + $B, 2);
        }
    }
    // =========================================================
    // END: Calculation
    // =========================================================
    // --- NEW LOGIC TO CHECK IF USER CAN ACT ---
    $is_pending = in_array((int)($data['request']['status'] ?? -1), [0, 1]);
    $is_responsible = ($data['request']['responsible_employee'] ?? '') == $this->session->userdata('username');
    
    // The buttons should only appear if the request is pending AND assigned to the current user
    $data['can_act_on_request'] = ($is_pending && $is_responsible);
    // --- END OF NEW LOGIC ---
    $data['delegate_name'] = null; // Initialize as null
    if (isset($data['request']['type']) && $data['request']['type'] == 5 && !empty($data['request']['delegation_employee_id'])) {
        // It's a vacation request with a delegate. Get the delegate's name.
        $delegate_info = $this->hr_model->get_employee_info($data['request']['delegation_employee_id']);
        
        if ($delegate_info) {
            $data['delegate_name'] = $delegate_info['name'];
        } else {
            // Fallback if the employee name isn't found
            $data['delegate_name'] = $data['request']['delegation_employee_id']; 
        }
    }
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/view_request_details', $data ?? []);
$this->load->view('template/new_footer');
}
    function index1(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{
               
             $this->load->view('templateo/index1');   


          }
          
      }

public function store_employee()
{
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->library('form_validation');
    $this->load->model('hr_model');

    // 2. Validation Rules
    $this->form_validation->set_rules('employee_code', 'الرقم الوظيفي', 'required|trim|is_unique[emp1.employee_id]');
    
    // Name: At least 4 words
    $this->form_validation->set_rules('full_name_ar', 'الاسم الرباعي', 'required|trim|regex_match[/^(\S+\s+){3,}\S+.*$/u]', [
        'regex_match' => 'يجب أن يكون الاسم رباعياً على الأقل (4 كلمات).'
    ]);
    $this->form_validation->set_rules('join_date', 'تاريخ الانضمام', 'required');
    $this->form_validation->set_rules('company', 'الشركة', 'required');
    $this->form_validation->set_rules('department', 'القسم', 'required'); // Optional based on your need
    $this->form_validation->set_rules('direct_manager', 'المدير المباشر', 'required|trim');

    // 3. NEW: Financial Validation (Must be numbers and >= 0)
    $this->form_validation->set_rules('basic_salary', 'الراتب الأساسي', 'required|numeric|greater_than_equal_to[0]');
    $this->form_validation->set_rules('housing_allowance', 'بدل السكن', 'numeric|greater_than_equal_to[0]');
    $this->form_validation->set_rules('transportation_allowance', 'بدل النقل', 'numeric|greater_than_equal_to[0]');
    $this->form_validation->set_rules('communication_allowance', 'بدل الاتصال', 'numeric|greater_than_equal_to[0]');
    $this->form_validation->set_rules('other_allowance', 'بدلات أخرى', 'numeric|greater_than_equal_to[0]');
    $this->form_validation->set_rules('total_salary', 'إجمالي الراتب', 'required|numeric|greater_than_equal_to[0]');
    // ID: Exactly 10 digits
    $this->form_validation->set_rules('id_number', 'رقم الهوية', 'required|trim|numeric|exact_length[10]');

    // Mobile: Starts with 966 and is 12 digits total
    $this->form_validation->set_rules('mobile', 'رقم الجوال', 'required|trim|numeric|exact_length[12]|regex_match[/^966\d{9}$/]', [
        'regex_match' => 'يجب أن يبدأ الجوال بـ 966 ويتبعه 9 أرقام.'
    ]);

    // IBAN: Exactly 24 chars
    $this->form_validation->set_rules('iban', 'الآيبان', 'required|trim|exact_length[24]');

    if ($this->form_validation->run() === FALSE) {
        // Validation Failed
        $this->session->set_flashdata('error', validation_errors());
        $data['page_title'] = 'إضافة موظف جديد';
        $data['employee'] = null;
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/employee_form_view', $data ?? []);
$this->load->view('template/new_footer');
    } else {
        // 3. MAP INPUTS TO DATABASE COLUMNS
        $data = [
            'employee_id'     => $this->input->post('employee_code'),
            'subscriber_name' => $this->input->post('full_name_ar'),
            'id_number'       => $this->input->post('id_number'),
            'nationality'     => $this->input->post('nationality'),
            'joining_date'    => $this->input->post('join_date'),
            'n1'              => $this->input->post('department'),
            'birth_date'      => $this->input->post('birth_date'),
            'profession'      => $this->input->post('job_title'),
            'gender'          => $this->input->post('gender'),
            'marital'         => $this->input->post('marital_status'),
            'religion'        => $this->input->post('religion'),
            'id_expiry'       => $this->input->post('id_expiry'),
            'phone'           => $this->input->post('mobile'),
            'email'           => $this->input->post('personal_email'), // Personal Email
            'n13'             => $this->input->post('company_email'),  // Company Email (mapped to n13 based on logic)
            'address'         => $this->input->post('address'),
            'type'            => $this->input->post('employment_type'),
            'company_name'    => $this->input->post('company'),
            'location'        => $this->input->post('location'),
            'manager'         => $this->input->post('direct_manager'),
            
            // Banking Mapping
            'n2'              => $this->input->post('iban'),       // IBAN -> n2
            'n3'              => $this->input->post('bank_name'),  // Bank -> n3

            // Salary Mapping
            'base_salary'       => $this->input->post('base_salary'),
            'housing_allowance' => $this->input->post('housing_allowance'),
            'commissions'       => $this->input->post('transportation_allowance'), // Map transport to commissions or n4
            'n4'                => $this->input->post('transportation_allowance'), // Saving to n4 as well to be safe
            'n7'                => $this->input->post('communication_allowance'),
            'n5'                => $this->input->post('work_nature_allowance'),
            'n6'                => $this->input->post('headphone_allowance'),
            'other_allowances'  => $this->input->post('other_allowance'),
            'n8'                => $this->input->post('fuel_allowance'),
            'n9'                => $this->input->post('extra_transport_allowance'),
            'n10'               => $this->input->post('supervision_allowance'),
            'n11'               => $this->input->post('subsistence_allowance'),
            'total_salary'      => $this->input->post('total_salary'),
            
            'status'            => 'active',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        if ($this->hr_model->add_employee($data)) {
            $this->session->set_flashdata('success', 'تمت إضافة الموظف بنجاح.');
            redirect('users1/emp_data101');
        } else {
            $this->session->set_flashdata('error', 'حدث خطأ في قاعدة البيانات.');
            redirect('users1/add_employee');
        }
    }
}
      function add_new_emp(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{
             
             $this->load->view('templateo/add_new_emp');   


          }
          
      }

    // In application/controllers/Users2.php

// In your controller (e.g., Users2.php)
// REPLACE the entire view_emp function with this corrected version.

// In application/controllers/Users2.php
// REPLACE your entire existing view_emp() function with this corrected one.

// In application/controllers/Users2.php
// Replace the entire view_emp function with this more robust version.

// In application/controllers/Users2.php
// REPLACE the entire view_emp function with this corrected version.

// In your controller (Users1.php or Users2.php)
// In Users1.php

// In Users1.php

public function update_employee_availability_cron($token = '')
{
    if ($token !== 'YourSecretTokenHere') { // REMEMBER to use your actual secret token
        show_404();
        return;
    }
    
    $this->load->model('hr_model');

    // First, let's see who the script thinks should be on vacation
    $employees_on_vacation = $this->hr_model->get_currently_vacationing_employees();
    
    // Next, run the update process as before
    $result = $this->hr_model->update_availability_statuses();
    
    // --- NEW DEBUGGING AND VERIFICATION REPORT ---
    echo "<h2>Availability Status Sync Report</h2>";
    echo "<p><b>Action Summary:</b></p>";
    echo "<ul>";
    echo "<li>Employees Reset to 'available': " . $result['reset'] . "</li>";
    echo "<li>Employees updated to 'unavailable': " . $result['made_unavailable'] . "</li>";
    echo "</ul><hr>";

    echo "<p><b>Verification Step:</b></p>";
    if (empty($employees_on_vacation)) {
        echo "<p>No employees were found on an approved vacation for today, so no status change was expected.</p>";
    } else {
        echo "<p>The script attempted to update the following employee(s). Here is their status read directly from the database AFTER the update:</p>";
        echo "<table border='1' cellpadding='5'><tr><th>Employee ID</th><th>Status in Database</th></tr>";
        foreach ($employees_on_vacation as $emp) {
            // Read the status back from the database immediately after the update
            $status_after_update = $this->hr_model->get_employee_status($emp['emp_id']);
            echo "<tr><td><b>" . $emp['emp_id'] . "</b></td><td style='color:red;'><b>" . $status_after_update . "</b></td></tr>";
        }
        echo "</table>";
    }
    die("--- End of Report ---");
}
// In Users1.php

// In Users1.php
// In Users1.php
// In Users1.php

public function export_balances_excel_all()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    $this->load->model('hr_model');

    // Get all data for the export
    $all_balances = $this->hr_model->get_all_balances_for_export();
    
    // 1. Set the filename with .xls extension
    $filename = 'all_leave_balances_' . date('Y-m-d') . '.xls';

    // 2. Set the correct headers for an Excel file
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    
    // 3. Start building the HTML table string
    // The meta tag is crucial for correct Arabic character display in Excel
    $excel_data = '<html dir="rtl" lang="ar"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
    $excel_data .= '<table border="1">';
    
    // Add table headers with some basic styling
    $excel_data .= '<thead style="background-color:#001f3f; color:#ffffff;"><tr>';
    $excel_data .= '<th>الرقم الوظيفي</th>';
    $excel_data .= '<th>اسم الموظف</th>';
    $excel_data .= '<th>نوع الإجازة</th>';
    $excel_data .= '<th>المخصص</th>';
    $excel_data .= '<th>المستهلك</th>';
    $excel_data .= '<th>المتبقي</th>';
    $excel_data .= '<th>السنة</th>';
    $excel_data .= '</tr></thead>';
    
    // Add table rows
    $excel_data .= '<tbody>';
    foreach ($all_balances as $balance) {
        $excel_data .= '<tr>';
        $excel_data .= '<td>' . html_escape($balance->employee_id) . '</td>';
        $excel_data .= '<td>' . html_escape($balance->subscriber_name) . '</td>';
        $excel_data .= '<td>' . html_escape($balance->leave_type_name) . '</td>';
        $excel_data .= '<td>' . html_escape($balance->balance_allotted) . '</td>';
        $excel_data .= '<td>' . html_escape($balance->balance_consumed) . '</td>';
        $excel_data .= '<td>' . html_escape($balance->remaining_balance) . '</td>';
        $excel_data .= '<td>' . html_escape($balance->year) . '</td>';
        $excel_data .= '</tr>';
    }
    $excel_data .= '</tbody>';
    
    // Close the table and body/html tags
    $excel_data .= '</table>';
    $excel_data .= '</body></html>';

    // 4. Echo the final HTML string
    echo $excel_data;
    exit;
}

public function export_balances_pdf_all()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    $this->load->model('hr_model');

    $all_balances = $this->hr_model->get_all_balances_for_export();

    // Add a check to ensure there is data to export
    if (empty($all_balances)) {
        show_error('لا توجد بيانات لتصديرها.', 404, 'خطأ في التصدير');
        return;
    }

    // Make sure the TCPDF library file exists
    $tcpdf_path = APPPATH . 'third_party/tcpdf/tcpdf.php';
    if (!file_exists($tcpdf_path)) {
        show_error('ملف مكتبة TCPDF غير موجود. الرجاء التحقق من المسار: ' . $tcpdf_path, 500, 'خطأ في النظام');
        return;
    }
    require_once($tcpdf_path);

    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); // 'L' for Landscape

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('HR System');
    $pdf->SetTitle('تقرير أرصدة الإجازات الكامل');
    
    // --- IMPORTANT CHANGE: Use a font that reliably supports Arabic ---
    // 'aealarabiya' is a common font bundled with TCPDF for this purpose.
    $pdf->SetFont('aealarabiya', '', 10);

    $pdf->setRTL(true);
    $pdf->AddPage();

    // Build the HTML for the PDF
    $html = '<h1>تقرير أرصدة إجازات الموظفين (كامل)</h1>';
    $html .= '<table border="1" cellpadding="4" cellspacing="0" style="width:100%;">';
    $html .= '<tr style="background-color:#001f3f; color:white; text-align:center;">
                    <th style="width:10%;">الرقم الوظيفي</th>
                    <th style="width:25%;">اسم الموظف</th>
                    <th style="width:25%;">نوع الإجازة</th>
                    <th style="width:10%;">المخصص</th>
                    <th style="width:10%;">المستهلك</th>
                    <th style="width:10%;">المتبقي</th>
                    <th style="width:10%;">السنة</th>
                </tr>';

    foreach ($all_balances as $balance) {
        $html .= '<tr>
                    <td style="text-align:center;">' . $balance->employee_id . '</td>
                    <td>' . $balance->subscriber_name . '</td>
                    <td>' . $balance->leave_type_name . '</td>
                    <td style="text-align:center;">' . number_format((float)$balance->balance_allotted, 2) . '</td>
                    <td style="text-align:center;">' . number_format((float)$balance->balance_consumed, 2) . '</td>
                    <td style="text-align:center;">' . number_format((float)$balance->remaining_balance, 2) . '</td>
                    <td style="text-align:center;">' . $balance->year . '</td>
                  </tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('all_leave_balances_' . date('Y-m-d') . '.pdf', 'D');
    exit;
}
public function employee_balances_report()
{
    // Security check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    
    // Fetch all balance records from the database
    $data['balances'] = $this->hr_model->get_all_employee_balances();
    
    // Load the view and pass the data to it
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/employee_balances_report_view', $data ?? []);
$this->load->view('template/new_footer');
}

public function fetch_all_balances()
{
    $this->load->model('hr_model');
    $list = $this->hr_model->get_datatables_balances();
    
    $data = [];
    foreach ($list as $balance) {
        $row = [];
        $row['employee_id'] = $balance->employee_id;
        $row['subscriber_name'] = $balance->subscriber_name;
        $row['leave_type_name'] = $balance->leave_type_name;
        // Format numbers to 2 decimal places for consistent display
        $row['balance_allotted'] = number_format((float)$balance->balance_allotted, 2);
        $row['balance_consumed'] = number_format((float)$balance->balance_consumed, 2);
        $row['remaining_balance'] = number_format((float)$balance->remaining_balance, 2);
        $row['year'] = $balance->year;
        $data[] = $row;
    }

    $output = [
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->hr_model->count_all_balances(),
        "recordsFiltered" => $this->hr_model->count_filtered_balances(),
        "data" => $data,
    ];
    
    // Output to JSON format
    echo json_encode($output);
}
// --- POPUP: DISCOUNT DETAILS ---
public function employee_discounts_log($employee_id, $sheet_id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); return; }

    $this->load->model('hr_model');

    // Fetch Data
    $data['discounts'] = $this->hr_model->get_employee_discounts_details($employee_id, $sheet_id);
    
    // Employee Info
    $emp = $this->hr_model->get_employee_info($employee_id);
    $data['employee_name'] = $emp['name'] ?? $employee_id;
    
    // Sheet Info
    $sheet = $this->hr_model->get_salary_sheet($sheet_id);
    $data['sheet_period'] = ($sheet['start_date'] ?? '-') . ' / ' . ($sheet['end_date'] ?? '-');

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/employee_discounts_popup', $data ?? []);
$this->load->view('template/new_footer');
}

// --- POPUP: REPARATION DETAILS ---
public function employee_reparations_log($employee_id, $sheet_id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); return; }

    $this->load->model('hr_model');

    // Fetch Data
    $data['reparations'] = $this->hr_model->get_employee_reparations_details($employee_id, $sheet_id);

    // Employee Info
    $emp = $this->hr_model->get_employee_info($employee_id);
    $data['employee_name'] = $emp['name'] ?? $employee_id;
    
    // Sheet Info
    $sheet = $this->hr_model->get_salary_sheet($sheet_id);
    $data['sheet_period'] = ($sheet['start_date'] ?? '-') . ' / ' . ($sheet['end_date'] ?? '-');

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/employee_reparations_popup', $data ?? []);
$this->load->view('template/new_footer');
}
// --- ADD TO Users1.php ---
// --- ADD TO Users1.php ---

    public function fetch_approved_expenses() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['data' => []]); return;
        }
        
        $this->load->model('hr_model');
        $expenses = $this->hr_model->get_approved_expenses();
        $data = [];
        
        // Check if the logged-in user is Finance
        $current_user = $this->session->userdata('username');
        $is_finance = ($current_user == '1693' || $current_user == '2909' || $current_user == '1936' || $current_user == '2833');
        
        foreach ($expenses as $exp) {
            $row = [];
            $row['id'] = $exp['id'];
            $row['emp_name'] = $exp['emp_name'];
            $row['exp_item_name'] = $exp['exp_item_name'] ?? '-';
            $row['exp_amount'] = number_format((float)$exp['exp_amount'], 2) . ' ريال';
            $row['exp_date'] = $exp['exp_date'] ?? '-';
            $row['exp_reason'] = $exp['exp_reason'] ?? '-';
            
            // Generate Status Badge
            $status_badge = '<span class="badge badge-soft-secondary">غير مدفوع</span>';
            if ($exp['ot_payment_status'] === 'paid' || $exp['ot_payment_status'] === '1') {
                $status_badge = '<span class="badge badge-soft-success">مدفوع</span>';
            } elseif ($exp['ot_payment_status'] === 'requested') {
                $status_badge = '<span class="badge badge-soft-info">مطلوب من المالية</span>';
            }
            
            // Show Bank Receipt if uploaded
            if (!empty($exp['bank_receipt_file'])) {
                $status_badge .= ' <a href="'.base_url($exp['bank_receipt_file']).'" target="_blank" class="btn btn-sm btn-link text-primary p-0 ms-2" title="عرض الإيصال"><i class="fas fa-file-invoice"></i> إيصال</a>';
            }
            $row['payment_status'] = $status_badge;
            
            // Actions (Only show Payment button to Finance if not paid yet)
            $actions = '<div class="d-flex justify-content-center">';
            if ($is_finance && $exp['ot_payment_status'] !== 'paid' && $exp['ot_payment_status'] !== '1') {
                // We pass 'expense' to tell the frontend modal which URL to hit
                $actions .= '<button type="button" class="btn btn-sm btn-success shadow-sm" onclick="openPaymentModal('.$exp['id'].', \'expense\')" data-bs-toggle="tooltip" title="إرفاق إيصال الدفع"><i class="fas fa-money-check-alt"></i> دفع</button>';
            }
            $actions .= '</div>';
            
            $row['actions'] = $actions;
            $data[] = $row;
        }
        
        echo json_encode(['data' => $data]);
    }

    public function submit_expense_payment() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); return;
        }
        
        $order_id = $this->input->post('order_id');
        $payment_date = $this->input->post('payment_date') ?: date('Y-m-d');
        $receipt_path = null;

        // Upload Bank Receipt logic
        if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] === UPLOAD_ERR_OK) {
            $uploadPath = FCPATH . 'uploads/receipts/';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, TRUE); }
            
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['max_size'] = 5120; // 5MB max
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('receipt_file')) {
                $uploadData = $this->upload->data();
                $receipt_path = 'uploads/receipts/' . $uploadData['file_name'];
            } else {
                echo json_encode(['status' => 'error', 'message' => strip_tags($this->upload->display_errors())]);
                return;
            }
        }
        
        $this->load->model('hr_model');
        // Set payment status to "paid" and save the receipt + date
        $success = $this->hr_model->update_order_payment_status($order_id, 'paid', $receipt_path, $payment_date);
        
        if ($success) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'تم رفع الإيصال وتحديث حالة الدفع بنجاح.',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ البيانات.']);
        }
    }
// 1. Load the Overtime Dashboard View
public function overtime_dashboard()
{
    // Security: Check Login
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $current_user = $this->session->userdata('username');
    
    // Allowed users list
    $allowed_users = ['2901', '2774', '1001', '2230', '1835', '1693', '2784','2909','1936','2833'];

    if (!in_array($current_user, $allowed_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $data['is_finance'] = ($current_user == '1693' || $current_user == '2909' || $current_user == '1936' || $current_user == '2833'); // Flag for Finance Manager
    $data['title'] = 'لوحة متابعة العمل الإضافي';
    
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/overtime_dashboard', $data ?? []);
$this->load->view('template/new_footer');
}

// 2. Fetch Data for DataTables (AJAX)
// In Users1.php

// --- UPDATE/ADD IN Users1.php ---

// 1. UPDATE fetch_overtime_orders (Logic Change)
public function fetch_overtime_orders()
    {
        if (!$this->session->userdata('logged_in')) return;
        $this->load->model('hr_model');
        
        $list = $this->hr_model->get_overtime_datatables();
        $data = [];
        $no = $_POST['start'];
        
        $current_user = $this->session->userdata('username');
        $finance_ids = ['1693', '2909', '1936', '2833'];
        $is_finance = in_array($current_user, $finance_ids);
    
        foreach ($list as $order) {
            $no++;
            
            // Status Logic
            $status_text = 'تحت الإجراء';
            $status_class = 'badge-soft-warning';
            if ($order->status == 2) {
                $status_text = 'معتمد';
                $status_class = 'badge-soft-success';
            } elseif ($order->status == 3) {
                $status_text = 'مرفوض';
                $status_class = 'badge-soft-danger';
            }
    
            // Payment Status
            $pay_status = '<span class="status-badge badge-soft-secondary">غير مدفوع</span>';
            if ($order->ot_payment_status == 'paid' || $order->ot_payment_status == 1) {
                $pay_status = '<span class="status-badge badge-soft-success">تم الدفع</span>';
                if(!empty($order->payment_date)) $pay_status .= '<br><small class="text-muted" style="font-size:0.7rem">'.$order->payment_date.'</small>';
            } elseif ($order->ot_payment_status == 'requested') {
                $pay_status = '<span class="status-badge badge-soft-info">بانتظار المالية</span>';
            }
    
            // Actions
            $actions = '<div class="d-flex justify-content-center">';
            $actions .= '<a href="'.base_url('users1/view_request/'.$order->id).'" class="btn-action btn-view" target="_blank" data-bs-toggle="tooltip" title="التفاصيل"><i class="fas fa-eye"></i></a>';
    
            if ($order->status == 2) {
                if (!$is_finance && $order->ot_payment_status != 'requested' && $order->ot_payment_status != 'paid' && $order->ot_payment_status != 1) {
                    $actions .= '<button type="button" class="btn-action btn-request ms-1" onclick="sendToFinance('.$order->id.', this)" data-bs-toggle="tooltip" title="طلب صرف"><i class="fas fa-paper-plane"></i></button>';
                }
                if ($is_finance && $order->ot_payment_status == 'requested') {
                    $actions .= '<button type="button" class="btn-action btn-pay ms-1" onclick="openPaymentModal('.$order->id.', \'overtime\')" data-bs-toggle="tooltip" title="صرف"><i class="fas fa-check"></i></button>';
                }
            }
            $actions .= '</div>';

            // Approver Button (Type 3 = Overtime)
            $approver_btn = '<button type="button" class="btn btn-sm btn-light border" onclick="showApprovers('.$order->id.', 3)">
                                <i class="fas fa-sitemap text-muted"></i> <span class="small">عرض</span>
                             </button>';
    
            // Associative Array Row
            $row = [
                'id' => $order->id,
                'emp_id' => $order->emp_id,
                'emp_name' => '<strong>' . $order->emp_name . '</strong>',
                'company_name' => $order->company_name ?? '-',
                'approvers' => $approver_btn, // Button
                'ot_date' => $order->ot_date,
                'ot_hours' => $order->ot_hours,
                'ot_amount' => number_format($order->ot_amount, 2),
                'status' => '<span class="badge ' . $status_class . '">' . $status_text . '</span>',
                'pay_status' => $pay_status,
                'bank_receipt_file' => $order->bank_receipt_file,
                'actions' => $actions
            ];
            $data[] = $row;
        }
    
        echo json_encode([
            "draw" => intval($_POST['draw']),
            "recordsTotal" => $this->hr_model->count_all_overtime(),
            "recordsFiltered" => $this->hr_model->count_filtered_overtime(),
            "data" => $data,
            "csrf_hash" => $this->security->get_csrf_hash() 
        ]);
    }
    public function get_approvers_ajax()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        echo $this->get_approvers_train($id, $type);
    }
// --- ADD TO Users1.php ---

// 1. Fetch Mandates for DataTable
// --- UPDATE THIS FUNCTION IN Users1.php ---

public function fetch_mandate_requests()
    {
        if (!$this->session->userdata('logged_in')) return;
        $this->load->model('hr_model');
        
        $list = $this->hr_model->get_mandate_datatables();
        $data = [];
        $no = $_POST['start'];
        $current_user = $this->session->userdata('username');
        $finance_ids = ['1693', '2909', '1936', '2833'];
        $is_finance = in_array($current_user, $finance_ids);
    
        foreach ($list as $r) {
            $no++;
            $row = []; // Numbered array initialization
            
            // Status Logic
            $status_html = '<span class="status-badge badge-soft-warning">تحت الإجراء</span>';
            if ($r->status == 'Approved' || $r->status == 'Completed') {
                $status_html = '<span class="status-badge badge-soft-success">معتمد</span>';
            } elseif ($r->status == 'Rejected') {
                $status_html = '<span class="status-badge badge-soft-danger">مرفوض</span>';
            }
    
            // Payment Status
            $pay_status = '<span class="status-badge badge-soft-secondary">غير مدفوع</span>';
            if ($r->payment_status == 'paid') {
                $pay_status = '<span class="status-badge badge-soft-success">تم الدفع</span>';
                if(!empty($r->payment_date)) $pay_status .= '<br><small class="text-muted" style="font-size:0.7rem">'.$r->payment_date.'</small>';
            } elseif ($r->payment_status == 'requested') {
                $pay_status = '<span class="status-badge badge-soft-info">بانتظار المالية</span>';
            }
    
            // Actions
            $actions = '<div class="d-flex justify-content-center gap-1">';
            if (($r->status == 'Approved' || $r->status == 'Completed') && !$is_finance && $r->payment_status != 'requested' && $r->payment_status != 'paid') {
                 $actions .= '<button type="button" class="btn-action btn-request" onclick="sendMandateToFinance('.$r->id.', this)" data-bs-toggle="tooltip" title="طلب صرف"><i class="fas fa-paper-plane"></i></button>';
            }
            if (($r->status == 'Approved' || $r->status == 'Completed') && $is_finance && $r->payment_status == 'requested') {
                $actions .= '<button type="button" class="btn-action btn-pay" onclick="openPaymentModal('.$r->id.', \'mandate\')" data-bs-toggle="tooltip" title="صرف"><i class="fas fa-check"></i></button>';
            }
            $actions .= '</div>';

            // Approver Button (Type 9 = Mandate)
            $approver_btn = '<button type="button" class="btn btn-sm btn-light border" onclick="showApprovers('.$r->id.', 9)">
                                <i class="fas fa-sitemap text-muted"></i> <span class="small">عرض</span>
                             </button>';

            // Receipt HTML
            $receipt_html = '<span class="text-muted small">-</span>';
            if (!empty($r->payment_receipt)) {
                $file_url = base_url('uploads/receipts/' . $r->payment_receipt);
                $receipt_html = '<a href="'.$file_url.'" class="btn btn-sm btn-outline-info" target="_blank"><i class="fas fa-file-pdf"></i></a>';
            }

            // --- FILL ROW DATA (Order MUST match HTML Table Headers) ---
            $row[] = $r->id;                                       // 0. #
            $row[] = $r->emp_id;                                   // 1. Emp ID
            $row[] = '<strong>' . $r->subscriber_name . '</strong>'; // 2. Name (Added)
            $row[] = $r->company_name ?? '-';                      // 3. Company (Added)
            $row[] = $approver_btn;                                // 4. Approvers (Added)
            $row[] = date('Y-m-d', strtotime($r->start_date));     // 5. Date
            $row[] = $r->duration_days . ' يوم';                   // 6. Duration
            $row[] = number_format($r->total_amount, 2);           // 7. Amount
            $row[] = $status_html;                                 // 8. Status
            $row[] = $pay_status;                                  // 9. Pay Status
            $row[] = $receipt_html;                                // 10. Receipt
            $row[] = $actions;                                     // 11. Actions

            $data[] = $row;
        }
    
        echo json_encode([
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->hr_model->count_all_mandate(),
            "recordsFiltered" => $this->hr_model->count_filtered_mandate(),
            "data" => $data,
            "csrf_hash" => $this->security->get_csrf_hash()
        ]);
    }


    // --- 4. APPROVER HTML GENERATOR ---
   // --- 4. APPROVER HTML GENERATOR (FIXED TO SHOW CHAIN) ---
    public function get_approvers_train($order_id, $order_type)
    {
        $this->db->select('aw.status, aw.approval_level, e.subscriber_name');
        $this->db->from('approval_workflow aw');
        $this->db->join('emp1 e', 'e.employee_id = aw.approver_id', 'left');
        
        // 1. Match the Order ID
        $this->db->where('aw.order_id', $order_id);
        
        // 2. TEMPORARY FIX: Ignore the Order Type to force data to show
        // Once you see the data, you can check your DB to find the real type number (likely 4, 12, etc)
        // $this->db->where('aw.order_type', $order_type); 
        
        $this->db->order_by('aw.approval_level', 'ASC');
        $query = $this->db->get();
        
        $approvers = $query->result();
        
        if(empty($approvers)) {
            return '<div class="alert alert-light text-center small text-muted p-2">
                        <i class="fas fa-exclamation-circle"></i> لا توجد سلسلة اعتمادات لهذا الطلب
                    </div>';
        }
    
        $html = '<div class="d-flex flex-column align-items-center gap-2">';
        foreach($approvers as $app) {
            $color = '#6c757d'; 
            $icon = 'fa-circle';
            $status_text = 'غير محدد';
            $border_color = '#dee2e6';
            
            if($app->status == 'approved' || $app->status == '1') {
                $color = '#28a745'; $icon = 'fa-check-circle'; $status_text = 'معتمد'; $border_color = '#28a745';
            } elseif($app->status == 'rejected' || $app->status == '2') {
                $color = '#dc3545'; $icon = 'fa-times-circle'; $status_text = 'مرفوض'; $border_color = '#dc3545';
            } elseif($app->status == 'pending' || $app->status == '0') {
                $color = '#ffc107'; $icon = 'fa-clock'; $status_text = 'قيد الانتظار'; $border_color = '#ffc107';
            }
    
            $html .= '<div class="card w-100 border-0 shadow-sm mb-1" style="border-right: 4px solid '.$border_color.' !important; background:#f8f9fa;">
                        <div class="card-body p-2 d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block" style="font-size:10px;">المستوى '.$app->approval_level.'</small>
                                <span class="fw-bold small text-dark">'.$app->subscriber_name.'</span>
                            </div>
                            <div class="text-end">
                                <span class="badge" style="background:'.$color.'; font-weight:normal; font-size:10px;">'.$status_text.'</span>
                            </div>
                        </div>
                      </div>';
            
            if ($app !== end($approvers)) {
                $html .= '<i class="fas fa-arrow-down text-muted small my-1" style="opacity:0.5"></i>';
            }
        }
        $html .= '</div>';
        return $html;
    }
// 2. HR Submit Action (Mandate)
public function submit_mandate_payment() {
    // Security checks...
    $id = $this->input->post('order_id');
    $this->load->model('hr_model');
    if($this->hr_model->request_mandate_payment($id)) {
        echo json_encode(['status'=>'success', 'message'=>'تم إرسال الانتداب للمالية', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'DB Error', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    }
}
// --- FETCH EOS DATA ---
public function fetch_eos_data()
{
    if (!$this->session->userdata('logged_in')) return;
    $this->load->model('hr_model');
    
    $list = $this->hr_model->get_eos_datatables();
    $data = [];
    $no = $_POST['start'];
    
    $current_user = $this->session->userdata('username');
    $finance_ids = ['1693', '2909', '1936', '2833'];
    $is_finance = in_array($current_user, $finance_ids);

    foreach ($list as $r) {
        $no++;
        
        // Status Badge
        $status_html = '<span class="status-badge badge-soft-success">معتمد</span>'; // Filter only gets approved ones
        
        // Payment Status Badge
        $pay_status = '<span class="status-badge badge-soft-secondary">غير مدفوع</span>';
        if ($r->payment_status == 'paid') {
            $pay_status = '<span class="status-badge badge-soft-success">تم الدفع</span>';
            if(!empty($r->payment_date)) $pay_status .= '<br><small class="text-muted" style="font-size:0.7rem">'.$r->payment_date.'</small>';
        } elseif ($r->payment_status == 'requested') {
            $pay_status = '<span class="status-badge badge-soft-info">بانتظار المالية</span>';
        }

        // Actions Logic
        $actions = '<div class="d-flex justify-content-center">';
        
        // HR Request Button
        if (!$is_finance && $r->payment_status != 'requested' && $r->payment_status != 'paid') {
             $actions .= '<button type="button" class="btn-action btn-request" onclick="sendEosToFinance('.$r->id.', this)" data-bs-toggle="tooltip" title="طلب صرف"><i class="fas fa-paper-plane"></i></button>';
        }

        // Finance Confirm Button
        if ($is_finance && $r->payment_status == 'requested') {
            $actions .= '<button type="button" class="btn-action btn-pay ms-1" onclick="openPaymentModal('.$r->id.', \'eos\')" data-bs-toggle="tooltip" title="صرف"><i class="fas fa-check"></i></button>';
        }
        $actions .= '</div>';

        // Approver Button (Using type 10 for EOS - Generic placeholder)
        $approver_btn = '<button type="button" class="btn btn-sm btn-light border" onclick="showApprovers('.$r->id.', 10)">
                            <i class="fas fa-sitemap text-muted"></i> <span class="small">عرض</span>
                         </button>';

        // Receipt Link
        $receipt_html = '<span class="text-muted small">-</span>';
        if (!empty($r->payment_receipt)) {
            $file_url = base_url('uploads/receipts/' . $r->payment_receipt);
            $receipt_html = '<a href="'.$file_url.'" class="btn btn-sm btn-outline-info" target="_blank"><i class="fas fa-file-pdf"></i></a>';
        }

        // Build Row (Matches HTML Table Order)
        $row = [];
        $row[] = $r->id;                                       // 0. #
        $row[] = $r->employee_id;                              // 1. Emp ID
        $row[] = '<strong>' . $r->subscriber_name . '</strong>'; // 2. Name
        $row[] = $r->company_name ?? '-';                      // 3. Company
        $row[] = $approver_btn;                                // 4. Approvers
        $row[] = number_format($r->final_amount, 2);           // 5. Final Amount
        $row[] = $status_html;                                 // 6. Status
        $row[] = $pay_status;                                  // 7. Payment Status
        $row[] = $receipt_html;                                // 8. Receipt File
        $row[] = $actions;                                     // 9. Actions

        $data[] = $row;
    }

    echo json_encode([
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $this->hr_model->count_all_eos(),
        "recordsFiltered" => $this->hr_model->count_filtered_eos(),
        "data" => $data,
        "csrf_hash" => $this->security->get_csrf_hash()
    ]);
}

// --- CONFIRM EOS PAYMENT (FINANCE) ---
public function confirm_eos_payment() {
    $id = $this->input->post('order_id');
    $date = $this->input->post('payment_date');
    
    // Upload Logic
    $file_name = null;
    if (!empty($_FILES['receipt_file']['name'])) {
        $config['upload_path'] = './uploads/receipts/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['encrypt_name'] = TRUE;
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
        
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('receipt_file')) {
            $d = $this->upload->data();
            $file_name = $d['file_name'];
        }
    }

    $this->load->model('hr_model');
    if($this->hr_model->update_eos_payment($id, $date, $file_name)) {
         echo json_encode(['status'=>'success', 'message'=>'تم صرف المستحقات بنجاح', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    } else {
         echo json_encode(['status'=>'error', 'message'=>'Database Error', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    }
}

// --- SUBMIT EOS TO FINANCE (HR) ---
public function submit_eos_payment() {
    $id = $this->input->post('order_id');
    $this->load->model('hr_model');
    if($this->hr_model->request_eos_payment($id)) {
        echo json_encode(['status'=>'success', 'message'=>'تم إرسال الطلب للمالية', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'DB Error', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    }
}
// 3. Finance Confirm Action (Mandate)
public function confirm_mandate_payment() {
    // Security checks...
    $id = $this->input->post('order_id');
    $date = $this->input->post('payment_date');
    
    // File Upload (Reusable logic recommended, but kept inline for simplicity)
    $file_name = null;
    if (!empty($_FILES['receipt_file']['name'])) {
        $config['upload_path'] = './uploads/receipts/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['encrypt_name'] = TRUE;
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('receipt_file')) {
            $d = $this->upload->data();
            $file_name = $d['file_name'];
        }
    }

    $this->load->model('hr_model');
    if($this->hr_model->update_mandate_payment($id, $date, $file_name)) {
         echo json_encode(['status'=>'success', 'message'=>'تم صرف الانتداب', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    } else {
         echo json_encode(['status'=>'error', 'message'=>'DB Error', 'csrf_hash'=>$this->security->get_csrf_hash()]);
    }
}
// 2. NEW FUNCTION: HR Sends to Finance
public function submit_payment_request()
{
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired', 'csrf_hash' => $this->security->get_csrf_hash()]); return;
    }

    $order_id = $this->input->post('order_id');
    
    $this->load->model('hr_model');
    // Set status to 'requested'
    $success = $this->hr_model->request_ot_payment($order_id);

    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'تم إرسال طلب الصرف للمالية بنجاح', 'csrf_hash' => $this->security->get_csrf_hash()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database Error', 'csrf_hash' => $this->security->get_csrf_hash()]);
    }
}

public function confirm_ot_payment()
{
    // Force Error Logging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Response Array
    $response = ['status' => 'error', 'message' => 'Unknown Error', 'csrf_hash' => $this->security->get_csrf_hash()];

    if (!$this->session->userdata('logged_in')) {
        $response['message'] = 'User not logged in';
        echo json_encode($response); return;
    }

    $order_id = $this->input->post('order_id');
    $payment_date = $this->input->post('payment_date');

    // DEBUG: Check if data arrived
    if (empty($order_id)) {
        $response['message'] = 'Error: Order ID is missing from the request.';
        echo json_encode($response); return;
    }

    // 1. Handle File Upload (If exists)
    $file_name = null;
    if (!empty($_FILES['receipt_file']['name'])) {
        $config['upload_path'] = './uploads/receipts/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['encrypt_name'] = TRUE;
        
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('receipt_file')) {
            $response['message'] = 'Upload Error: ' . $this->upload->display_errors('', '');
            echo json_encode($response); return;
        } else {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];
        }
    }

    // 2. Database Update
    $this->load->model('hr_model');
    
    // Check if the order actually exists first
    $exists = $this->db->where('id', $order_id)->count_all_results('orders_emp');
    if($exists == 0) {
        $response['message'] = 'Error: Order ID ' . $order_id . ' not found in database.';
        echo json_encode($response); return;
    }

    // Perform Update
    $result = $this->hr_model->update_ot_payment_status($order_id, $payment_date, $file_name);

    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Payment Confirmed! Order ID: ' . $order_id;
    } else {
        $response['message'] = 'Database Error: Could not update the row.';
    }

    // Send back the new token
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    echo json_encode($response);
}
public function accrue_leave_balances_cron($token = '', $employee_id = null)
{
    // IMPORTANT: Make sure this token matches the one in your Task Scheduler.
    if ($token !== 'LeaveAccrual') {
        show_404();
        return;
    }
    
    $this->load->model('hr_model');
    
    // Pass the optional employee_id to the model function
    $result = $this->hr_model->process_weekly_leave_accrual($employee_id);
    
    if ($employee_id) {
        echo "<h3>Test Run for Employee ID: " . html_escape($employee_id) . "</h3>";
    } else {
        echo "<h3>Running for ALL Active Employees</h3>";
    }

    echo "<p>Weekly Leave Accrual Completed.</p>";
    echo "<ul>";
    echo "<li>Records Updated: " . $result['updated'] . "</li>";
    echo "<li>New Year Records Created (with carry-over): " . $result['created'] . "</li>";
    echo "</ul>";
}
// In Users1.php
public function delete_salary_suspension() {
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired']); return;
    }

    $id = $this->input->post('id');
    
    // Security check: Ensure ID exists
    if(!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']); return;
    }

    $this->db->where('id', $id);
    $this->db->delete('stop_salary');

    if ($this->db->affected_rows() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'تم حذف الإيقاف بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل الحذف أو السجل غير موجود']);
    }
}
public function view_emp($employee_id)
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }

    $this->load->model('hr_model');

    $data['suspensions'] = $this->hr_model->get_employee_suspensions($id);
    // Get the main employee data
    $data['employee'] = $this->hr_model->get_employee_details($employee_id);

    if (empty($data['employee'])) {
        show_404();
        return;
    }

    // Safely fetch and process leave balances
    $balances_detailed = $this->hr_model->get_employee_balances($employee_id);
    $simple_balances = [];
    if (is_array($balances_detailed)) {
        foreach ($balances_detailed as $slug => $details) {
            $simple_balances[$slug] = $details['remaining'] ?? 0;
        }
    }
    $data['leave_balance'] = $simple_balances;

    // *** NEW: Get Direct Manager Details ***
    $data['direct_manager_details'] = $this->hr_model->get_direct_manager_details($employee_id);
    // *** END NEW ***


    // (Optional) You can fetch attachments here
    // $data['attachments'] = $this->hr_model->get_attachments($employee_id);

     // Include CSRF tokens for the form
     $data['csrf_token_name'] = $this->security->get_csrf_token_name();
     $data['csrf_hash'] = $this->security->get_csrf_hash();


    // Load the view with all the data
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/view_emp', $data ?? []);
$this->load->view('template/new_footer'); // Assuming your view file name is view_emp.php
}
// In Users1.php

public function list_eos_settlements()
{
    // Security Check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }
    $hr_users = ['1835', '2230', '2515', '2774', '2784', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('Unauthorized access.', 403);
        return;
    }

    $this->load->model('hr_model');

    // 1. GET THE INPUTS
    $status_filter = $this->input->get('status', TRUE);
    $employee_id_filter = $this->input->get('employee_id', TRUE); // <--- NEW LINE

    // 2. PASS BOTH VARIABLES TO THE MODEL
    // We update the model function call to include the employee_id
    $data['settlements'] = $this->hr_model->get_all_eos_settlements($status_filter, $employee_id_filter);
    
    // Pass filters back to view (so the search box keeps the value)
    $data['current_filter'] = $status_filter ?? 'all';
    // We assume you handle $_GET['employee_id'] in the view directly, 
    // or you can pass it here like: $data['current_employee_id'] = $employee_id_filter;

    $data['status_labels'] = [
        'pending_review' => 'بانتظار المراجعة الأولية',
        'pending_level_1' => 'بانتظار الاعتماد الأول',
        'pending_level_2' => 'بانتظار الاعتماد الثاني',
        'pending_level_3' => 'بانتظار الاعتماد الثالث',
        'pending_level_4' => 'بانتظار الاعتماد الرابع',
        'pending_level_5' => 'بانتظار الاعتماد الخامس',
        'approved' => 'معتمد نهائي',
        'rejected' => 'مرفوض',
        'all'      => 'الكل'
    ];

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/list_eos_settlements_view', $data ?? []);
$this->load->view('template/new_footer');
}
public function insurance_discounts()
{
    // Security Check: Only allow HR users
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $this->load->model('hr_model');
    
    // Fetch all discounts for the table
    $data['discounts'] = $this->hr_model->get_all_insurance_discounts();
    
    // Fetch all employees for the "Add New" dropdown
    $data['employees'] = $this->hr_model->get_all_employees();
    
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/insurance_discounts_view', $data ?? []);
$this->load->view('template/new_footer');
}

// This function handles updating the discount percentage via AJAX
public function ajax_update_insurance_discount()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $id = $this->input->post('id');
    $percentage = $this->input->post('n3');

    if (empty($id) || !is_numeric($percentage) || $percentage < 0 || $percentage > 100) {
        $response = ['status' => 'error', 'message' => 'Invalid data provided.'];
    } else {
        $this->load->model('hr_model');
        $data = ['n3' => $percentage];
        $success = $this->hr_model->update_insurance_discount($id, $data);
        
        if ($success) {
            $response = ['status' => 'success', 'message' => 'Discount updated successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to update the discount.'];
        }
    }
    
    // Add CSRF token to response for security
    $response['csrf_name'] = $this->security->get_csrf_token_name();
    $response['csrf_hash'] = $this->security->get_csrf_hash();

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}
// In application/controllers/Users1.php

// In Users1.php
// In Users1.php, replace the new_employees_list function

// In Users1.php
public function new_employees_list()
{
    // Security Check: Only allow HR users to view this page
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $this->load->model('hr_model');
    
    // Get the date range from GET parameters
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');

    // Debug: Log what we received
    log_message('debug', "=== NEW EMPLOYEES LIST CONTROLLER ===");
    log_message('debug', "GET Parameters: " . print_r($_GET, TRUE));
    log_message('debug', "Start Date: " . $start_date . " | End Date: " . $end_date);

    // Pass the date range to the model function
    $data['new_employees'] = $this->hr_model->get_new_employees($start_date, $end_date);
    
    // Debug: Log how many records we got
    log_message('debug', "Records found: " . count($data['new_employees']));
    
    // Add debug info to pass to view
    $data['debug_info'] = [
        'start_date' => $start_date,
        'end_date' => $end_date,
        'record_count' => count($data['new_employees'])
    ];
    
    // Load the view and pass the data to it
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/new_employees_view', $data ?? []);
$this->load->view('template/new_footer');
}
// You can add functions for ajax_add and ajax_delete here later if needed
public function update_employee_data()
{
    // 1. Security Check
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $this->load->model('hr_model');

    // 2. Get Data
    $data = $this->input->post();
    $employeeId = $this->input->post('id');

    if (empty($employeeId)) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Error: Employee ID is missing.']));
        return;
    }

    // 3. Remove Security Tokens & ID
    $csrf_token_name = $this->security->get_csrf_token_name();
    unset($data[$csrf_token_name]);
    unset($data['id']);

    // 4. --- FIX: MAP FIELDS TO DATABASE COLUMNS ---
    // تحويل الأسماء من الشاشة إلى أسماء الأعمدة في قاعدة البيانات

    // الآيبان (الشاشة: iban -> قاعدة البيانات: n2)
    if (isset($data['iban'])) {
        $data['n2'] = $data['iban'];
        unset($data['iban']); // حذف الاسم القديم لمنع الخطأ
    }

    // اسم البنك (الشاشة: bank_name -> قاعدة البيانات: n3)
    if (isset($data['bank_name'])) {
        $data['n3'] = $data['bank_name'];
        unset($data['bank_name']);
    }
    
    // المسمى الوظيفي (الشاشة: job_title -> قاعدة البيانات: profession)
    if (isset($data['job_title'])) {
        $data['profession'] = $data['job_title'];
        unset($data['job_title']);
    }
    
    // ========================================================
    // --- 5. NEW: AUTO-CALCULATE TOTAL SALARY IN BACKEND -----
    // ========================================================
    // ضمان حساب الراتب الإجمالي بدقة وحفظه في جدول emp1
    
    $base    = isset($data['base_salary']) ? (float)$data['base_salary'] : 0;
    $housing = isset($data['housing_allowance']) ? (float)$data['housing_allowance'] : 0;
    $other   = isset($data['other_allowances']) ? (float)$data['other_allowances'] : 0;
    $n4      = isset($data['n4']) ? (float)$data['n4'] : 0;
    $n5      = isset($data['n5']) ? (float)$data['n5'] : 0;
    $n6      = isset($data['n6']) ? (float)$data['n6'] : 0;
    $n7      = isset($data['n7']) ? (float)$data['n7'] : 0;
    $n8      = isset($data['n8']) ? (float)$data['n8'] : 0;
    $n9      = isset($data['n9']) ? (float)$data['n9'] : 0;
    $n10     = isset($data['n10']) ? (float)$data['n10'] : 0;
    $n11     = isset($data['n11']) ? (float)$data['n11'] : 0;

    // جمع الراتب الأساسي مع كل البدلات وإسنادها لعمود total_salary
    $data['total_salary'] = $base + $housing + $other + $n4 + $n5 + $n6 + $n7 + $n8 + $n9 + $n10 + $n11;
    // ========================================================

    // 6. CLEAN DATA (Handle Empty Values)
    foreach ($data as $key => $value) {
        if ($value === '') {
            $data[$key] = null;
        }
    }

    // 7. Update Database
    $success = $this->hr_model->update_employee($employeeId, $data);
    
    // 8. Return Response
    if ($success) {
        $response = ['status' => 'success', 'message' => 'تم تحديث بيانات الموظف بنجاح.'];
    } else {
        $db_error = $this->db->error();
        $msg = 'فشل تحديث البيانات.';
        if (!empty($db_error['message'])) {
            $msg .= ' (DB Error: ' . $db_error['message'] . ')';
        }
        $response = ['status' => 'error', 'message' => $msg];
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    $response['csrf_name'] = $csrf_token_name;

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}


       function export_emp_data(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{
             
             $this->load->view('templateo/export_emp_data');   


          }
          
      }





    public function logout(){
      // Unset user data
      $this->session->unset_userdata('logged_in');
      $this->session->unset_userdata('user_id');
      $this->session->unset_userdata('username');
      // Set message
      $this->session->set_flashdata('user_loggedout', 'You are now logged out');
      $name=$this->session->userdata('name');
      $op_name="خروج من النظام";
      $this->hr_model->add_watch($name,$op_name);    

      redirect('users/login');
    }

    public function dashboard() {
        $data = [];
        $data['active_page'] = 'dashboard';
        $data['page_title']  = 'لوحة التحكم';
$this->load->view('template/new_header_and_sidebar', $data ?? []); 
$this->load->view('templateo/dashboard_view', $data ?? []);
$this->load->view('template/new_footer');
 
    }

        public function task() {
        
        
        $this->load->view('templateo/task');
      
    }


    // In Users1.php

/**
 * Displays the GOSI data upload page.
 */
public function upload_gosi_csv_page()
{
    // Security Check: Only allow HR users
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $data['csrf_token_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();
    
    // We will create this view file next
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/gosi_upload_view', $data ?? []);
$this->load->view('template/new_footer');
}

/**
 * Handles the processing of the uploaded GOSI CSV file.
 */
public function process_gosi_upload()
{
    // Security Check
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('You are not authorized for this action.', 403);
        return;
    }

    $this->load->model('hr_model');
    $this->load->library('upload');

    $company_code = $this->input->post('company_code');
    $upload_mode = $this->input->post('upload_mode');
    $branch = $this->input->post('branch');

    // --- 1. Validation ---
    if (empty($company_code) || !in_array($upload_mode, ['add', 'replace'])) {
        $this->session->set_flashdata('error', 'بيانات غير مكتملة. الرجاء اختيار الشركة ونوع العملية.');
        redirect('users1/upload_gosi_csv_page');
    }

    if (empty($_FILES['gosi_file']['name'])) {
        $this->session->set_flashdata('error', 'الرجاء اختيار ملف CSV لرفعه.');
        redirect('users1/upload_gosi_csv_page');
    }

    // --- 2. File Upload ---
    $config['upload_path']   = './uploads/csv/gosi/';
    $config['allowed_types'] = 'csv';
    $config['max_size']      = 5120; // 5MB
    $config['encrypt_name']  = true;

    if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0777, true);
    }

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('gosi_file')) {
        $this->session->set_flashdata('error', 'فشل رفع الملف: ' . $this->upload->display_errors());
        redirect('users1/upload_gosi_csv_page');
        return;
    }

    $file_data = $this->upload->data();
    $file_path = $file_data['full_path'];

    // --- 3. Parse CSV and Process Data ---
    try {
        $gosi_data = $this->_parse_gosi_csv($file_path, $company_code);
        
        if (empty($gosi_data)) {
            throw new Exception('الملف فارغ أو أن رؤوس الأعمدة غير صحيحة. يجب أن تحتوي على: n1, n2, n3, n4, n5, n6');
        }

        // Call the model to process the batch
        // Call the model to process the batch
        $success = $this->hr_model->process_gosi_batch($gosi_data, $company_code, $upload_mode, $branch);

        if ($success) {
            $this->session->set_flashdata('success', 'تمت معالجة الملف بنجاح. تم إضافة ' . count($gosi_data) . ' سجل.');
        } else {
            throw new Exception('فشل في معاملة قاعدة البيانات.');
        }

    } catch (Exception $e) {
        $this->session->set_flashdata('error', 'خطأ في معالجة الملف: ' . $e->getMessage());
    }

    // Clean up uploaded file
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    redirect('users1/upload_gosi_csv_page');
}

// In Users1.php

/**
 * (READ) Lists all clearance parameters.
 */
public function clearance_parameters_list()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    $data['parameters'] = $this->hr_model->get_all_clearance_parameters();
    
    // We will create this view file
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/clearance_parameters_list_view', $data ?? []);
$this->load->view('template/new_footer');
}

/**
 * (CREATE - Form) Shows the form to add a new parameter.
 */
public function add_clearance_parameter()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    
    // Get data needed for the form dropdowns
    $data['parameter'] = null; // No data for add mode
    $data['departments'] = $this->hr_model->get_all_departments();
    $data['employees'] = $this->hr_model->get_all_employees_for_dropdown();
    $data['page_title'] = 'إضافة مهمة مخالصة جديدة';
    
    // We will create this view file
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/clearance_parameter_form_view', $data ?? []);
$this->load->view('template/new_footer');
}

/**
 * (UPDATE - Form) Shows the form to edit an existing parameter.
 */
public function edit_clearance_parameter($id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    
    // Get data for the form
    $data['parameter'] = $this->hr_model->get_parameter_by_id($id);
    if (empty($data['parameter'])) {
        show_404();
    }
    
    $data['departments'] = $this->hr_model->get_all_departments();
    $data['employees'] = $this->hr_model->get_all_employees_for_dropdown();
    $data['page_title'] = 'تعديل مهمة المخالصة';
    
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/clearance_parameter_form_view', $data ?? []);
$this->load->view('template/new_footer');
}

/**
 * (SAVE - Logic) Saves a new or updated parameter.
 */
public function save_clearance_parameter()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $this->load->model('hr_model');

    $id = $this->input->post('id');
    
    $data = [
        'department_id'      => $this->input->post('department_id'),
        'parameter_name'     => $this->input->post('parameter_name'),
        'approver_user_id'   => $this->input->post('approver_user_id'),
        'is_active'          => $this->input->post('is_active')
    ];

    if ($this->hr_model->save_clearance_parameter($id, $data)) {
        if (empty($id)) {
            $this->session->set_flashdata('success', 'تمت إضافة المهمة بنجاح.');
        } else {
            $this->session->set_flashdata('success', 'تم تحديث المهمة بنجاح.');
        }
    } else {
        $this->session->set_flashdata('error', 'حدث خطأ أثناء حفظ البيانات.');
    }

    redirect('users1/clearance_parameters_list');
}

/**
 * (DELETE - Logic) Deletes a parameter.
 */
public function delete_clearance_parameter($id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    
    if ($this->hr_model->delete_clearance_parameter($id)) {
        $this->session->set_flashdata('success', 'تم حذف المهمة بنجاح.');
    } else {
        $this->session->set_flashdata('error', 'فشل حذف المهمة.');
    }
    
    redirect('users1/clearance_parameters_list');
}
private function _parse_gosi_csv($file_path, $company_code)
{
    $gosi_records = [];
    $required_headers = ['n1', 'n2', 'n3', 'n4', 'n5', 'n6'];

    if (($handle = fopen($file_path, "r")) !== FALSE) {
        // Read and validate header
        $header = fgetcsv($handle);
        if ($header === FALSE) {
            throw new Exception('لا يمكن قراءة ملف CSV.');
        }

        // Clean header (remove BOM, trim spaces)
        $header = array_map(function($value) {
            return trim(preg_replace('/^\xEF\xBB\xBF/', '', $value));
        }, $header);
        
        // Check if all required headers are present
        if (count(array_intersect($required_headers, $header)) != count($required_headers)) {
            throw new Exception('رؤوس الأعمدة غير صحيحة. يجب أن تحتوي على: ' . implode(', ', $required_headers));
        }

        // Read data rows
        while (($row = fgetcsv($handle)) !== FALSE) {
            // Skip empty rows
            if (empty($row) || (count($row) == 1 && empty(trim($row[0])))) {
                continue;
            }

            $record = array_combine($header, $row);
            
            // Build the final array for the database
            $db_row = [];
            foreach ($required_headers as $col) {
                // Trim value and set empty strings to NULL
                $value = trim($record[$col]);
                $db_row[$col] = ($value === '') ? null : $value;
            }
            
            // Add the company code from the form
            $db_row['n7'] = $company_code; 
            
            // Add to batch if n2 (identity) is present
            if (!empty($db_row['n2'])) {
                $gosi_records[] = $db_row;
            }
        }
        fclose($handle);
    } else {
        throw new Exception('لا يمكن فتح ملف CSV.');
    }
    
    return $gosi_records;
}

   public function gosi_emp_compare()
{
    // اختيار الشركة اختياري: c=1 مرسوم, c=2 مكتب الدكتور, فارغة = الكل
    $company = $this->input->get('c', true);
    // ⭐️ NEW: Get the branch filter
    $branch = $this->input->get('branch', true);

    $this->load->model('hr_model');
    // ⭐️ MODIFIED: Pass the branch to the model
    $data = $this->hr_model->build_gosi_emp_compare($company, $branch);

    $data['company'] = $company; // لإظهار الفلتر الحالي
    $data['branch'] = $branch; // ⭐️ NEW: Pass branch to the view
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/gosi_emp_compare_a4', $data ?? []);
$this->load->view('template/new_footer');
}


     function ex_emp1(){ 

     if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{ 
            if($this->session->userdata('type') == 10){
              redirect('users/login');
            }else{

                 $this->hr_model->ex_emp1();

            }
        }



      
    }

    function ex_vacations(){ 

     if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{ 
            if($this->session->userdata('type') == 10){
              redirect('users/login');
            }else{

                 $this->hr_model->ex_vacations();

            }
        }



      
    }

     function ex_fingerprint_correction(){ 

     if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{ 
            if($this->session->userdata('type') == 10){
              redirect('users/login');
            }else{

                 $this->hr_model->ex_fingerprint_correction();

            }
        }



      
    }


        public function create()
    {
        // القوائم المطلوبة
        $data['categories'] = [
            'نظام الموارد البشرية',
            'نظام اودوو',
            'نظام التحصيل',
            'نظام الجودة',
            'نظام وحدة العناية بالعملاء',
            'متطلبات امن المعلومات',
            'برنامج التوظيف',
            'الراجحي عقار',
            'القانونية',
            '__other__' => 'أخرى'
        ];

        $data['requesters'] = [
            'لجنة التطوير التقني',
            'الادارة المالية',
            'ادارة تقنية المعلومات',
            'ادارة التحصيل',
            'مدير التوظيف',
            'مشرف الجودة',
            'مدير العمليات',
            'مدير الادارة القانونية',
            'ادارة العمليات',
            'الادارة القانونية',
            'مدير عام التحصيل',
            'نائب مدير عام التحصيل',
            'ادارة الجودة',
            '__other__' => 'أخرى'
        ];

        $data['assignees'] = [
            'صالح السفياني',
            'صهيب خطيب',
        ];

        $data['statuses'] = [
            'تم الانجاز',
            'جاري التنفيذ',
            'طلب مستقبلي',
            'جاري دراسة الطلب',
            'طلب مرفوض',
        ];

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/create', $data ?? []);
$this->load->view('template/new_footer');
    }

    // حفظ المهمة
    public function store()
    {
        // قواعد التحقق
        $this->form_validation->set_rules('category', 'التصنيف', 'required');
        $this->form_validation->set_rules('requester', 'جهة الطلب', 'required');
        $this->form_validation->set_rules('assignee', 'الموظف المنفذ', 'required');
        $this->form_validation->set_rules('due_date', 'تاريخ الانتهاء', 'required');
        $this->form_validation->set_rules('status', 'حالة الطلب', 'required');
        $this->form_validation->set_rules('details', 'تفاصيل المهمة', 'required|min_length[3]');

        if ($this->input->post('category') === '__other__') {
            $this->form_validation->set_rules('category_other', 'تصنيف آخر', 'required|min_length[2]');
        }
        if ($this->input->post('requester') === '__other__') {
            $this->form_validation->set_rules('requester_other', 'جهة طلب أخرى', 'required|min_length[2]');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            return redirect('users1/create');
        }

        // استخدام نص "أخرى" داخل نفس الحقل النهائي
        $category  = $this->input->post('category', true);
        if ($category === '__other__') {
            $category = $this->input->post('category_other', true);
        }

        $requester = $this->input->post('requester', true);
        if ($requester === '__other__') {
            $requester = $this->input->post('requester_other', true);
        }

        $data = [
            'category'  => $category,
            'requester' => $requester,
            'assignee'  => $this->input->post('assignee', true),
            'due_date'  => $this->input->post('due_date', true), // كنص حسب طلبك
            'status'    => $this->input->post('status', true),
            'details'   => $this->input->post('details', true),
        ];

        $ok = $this->hr_model->insert($data);

        if ($ok) {
            $this->session->set_flashdata('success', 'تم إضافة المهمة بنجاح ✅');
        } else {
            $this->session->set_flashdata('error', 'تعذر حفظ المهمة، حاول مرة أخرى.');
        }
        return redirect('users1/create');
    }

    public function index11()
    {
        // فلاتر من GET
        $filters = [
            'category' => $this->input->get('category', true),
            'statuses' => $this->input->get('statuses') ?: [],
            'date_from'=> $this->input->get('date_from', true),
            'date_to'  => $this->input->get('date_to', true),
        ];

        $data['filters']   = $filters;
        $data['statuses']  = ['تم الانجاز','جاري التنفيذ','طلب مستقبلي','جاري دراسة الطلب','طلب مرفوض'];

        // التصنيفات: ثابتة + من قاعدة البيانات (لإظهار أي تصنيفات أُدخلت نصيًا)
        $fixed = [
            'نظام الموارد البشرية','نظام اودوو','نظام التحصيل','نظام الجودة','نظام وحدة العناية بالعملاء',
            'متطلبات امن المعلومات','برنامج التوظيف','الراجحي عقار','القانونية'
        ];
        $dynamic = $this->hr_model->get_distinct_categories();
        $data['categories'] = array_values(array_unique(array_merge($fixed, $dynamic)));

        // البيانات
        $data['stats'] = $this->hr_model->get_stats_by_category($filters);
        $data['tasks'] = $this->hr_model->get_tasks1($filters);

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/index1', $data ?? []);
$this->load->view('template/new_footer');
    }

    public function update_status()
{
    $id     = (int) $this->input->post('id');
    $status = $this->input->post('status', true);
    $redirect = $this->input->post('redirect', true);

    if (!$id || !$status) {
        $this->session->set_flashdata('error', 'بيانات غير مكتملة لتحديث الحالة.');
        return redirect($redirect ?: 'users1/index11');
    }

    // المنطقة الزمنية + التاريخ/الوقت
    date_default_timezone_set('Asia/Riyadh');
    $meta = [
        'date'     => date('Y/m/d'),
        'time'     => date('h:i:s'), // حسب طلبك
        'username' => $this->session->userdata('username'),
        'name'     => $this->session->userdata('name'),
    ];

    $ok = $this->hr_model->update_status($id, $status, $meta);

    $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'تم تحديث حالة المهمة.' : 'تعذر تحديث الحالة.');
    return redirect($redirect ?: 'users1/index11');
}

public function update_due_date()
{
    $id  = (int) $this->input->post('id');
    $raw = $this->input->post('due_date_picker', true); // صيغة المتصفح: yyyy-mm-dd
    $redirect = $this->input->post('redirect', true);

    if (!$id || !$raw) {
        $this->session->set_flashdata('error', 'بيانات غير مكتملة لتحديث التاريخ.');
        return redirect($redirect ?: 'users1/index11');
    }

    // تحويل إلى YYYY/MM/DD
    $parts = explode('-', $raw); // [yyyy, mm, dd]
    $due_date = (count($parts) === 3) ? ($parts[0].'/'.$parts[1].'/'.$parts[2]) : '';

    date_default_timezone_set('Asia/Riyadh');
    $meta = [
        'date'     => date('Y/m/d'),
        'time'     => date('h:i:s'),
        'username' => $this->session->userdata('username'),
        'name'     => $this->session->userdata('name'),
    ];

    $ok = $this->hr_model->update_due_date($id, $due_date, $meta);
    $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'تم تحديث تاريخ الإنجاز.' : 'تعذر تحديث التاريخ.');
    return redirect($redirect ?: 'users1/index11');
}

public function update_assignee()
{
    $id       = (int) $this->input->post('id');
    $assignee = $this->input->post('assignee', true);
    $redirect = $this->input->post('redirect', true);

    if (!$id || !$assignee) {
        $this->session->set_flashdata('error', 'بيانات غير مكتملة لتحديث الموظف.');
        return redirect($redirect ?: 'users1/index11');
    }

    date_default_timezone_set('Asia/Riyadh');
    $meta = [
        'date'     => date('Y/m/d'),
        'time'     => date('h:i:s'),
        'username' => $this->session->userdata('username'),
        'name'     => $this->session->userdata('name'),
    ];

    $ok = $this->hr_model->update_assignee($id, $assignee, $meta);
    $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'تم تحديث الموظف المسؤول.' : 'تعذر تحديث الموظف.');
    return redirect($redirect ?: 'users1/index11');
}




    // === تصدير CSV (Excel-friendly) ===
    public function export()
    {
        $filters = [
            'category' => $this->input->get('category', true),
            'statuses' => $this->input->get('statuses') ?: [],
            'date_from'=> $this->input->get('date_from', true),
            'date_to'  => $this->input->get('date_to', true),
        ];
        $rows = $this->hr_model->get_tasks1($filters);

        $filename = 'tasks_report_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        // BOM للغة العربية
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        // العناوين
        fputcsv($out, ['#','التصنيف','جهة الطلب','الموظف المنفذ','تاريخ الانجاز (YYYY/MM/DD)','حالة الطلب','تفاصيل المهمة']);

        $i = 1;
        foreach ($rows as $r) {
            fputcsv($out, [
                $i++,
                $r['category'],
                $r['requester'],
                $r['assignee'],
                $r['due_date'],
                $r['status'],
                preg_replace("/\r|\n/", ' ', $r['details']),
            ]);
        }
        fclose($out);
        exit;
    }

    function ex_work_restrictions(){ 

     if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{ 
            if($this->session->userdata('type') == 10){
              redirect('users/login');
            }else{

                 $this->hr_model->ex_work_restrictions();

            }
        }



      
    }

    



    public function login1() {
        
        
        $this->load->view('templateo/login1');
      
    }

    

// In application/controllers/Users1.php

public function delete_employee()
{
    // Check if the request is an AJAX request for security
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $id = $this->input->post('id');
    
    $this->load->model('hr_model');

    // Call the model to perform the soft delete
    if ($this->hr_model->delete_employee_by_id($id)) {
        // On success, return a success message and the new CSRF token
        $response = [
            'status' => 'success',
            'message' => 'تم نقل الموظف إلى الأرشيف بنجاح.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ];
        echo json_encode($response);
    } else {
        // On failure, return an error message
        echo json_encode(['status' => 'error', 'message' => 'فشل حذف الموظف.']);
    }
}

     

   // In users1.php
function payroll_view101(){ 
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
$this->load->view('templateo/payroll_view101', $data ?? []);
$this->load->view('template/new_footer');
            } else {
                redirect('users/login');
            }
        }
    }
}

public function exemption_management()
    {
        // Security Check: Only allow HR users
        $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
        if (!in_array($this->session->userdata('username'), $hr_users)) {
            show_error('You are not authorized to view this page.', 403);
            return;
        }

        $this->load->model('hr_model');
        
        // Fetch all exemptions for the main table
        $data['exemptions'] = $this->hr_model->get_all_exemptions();
        
        // Fetch all employees for the "Add New" dropdown
        // We use the function that returns 'username' and 'name'
        $data['all_employees'] = $this->hr_model->get_all_employees(); 
        
        // Pass CSRF tokens for the forms
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        // Load the new view file
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/exemption_management_view', $data ?? []);
$this->load->view('template/new_footer');
    }
public function update_resigned_employee_status_cron($token = '')
    {
        // IMPORTANT: Set a strong, secret token here for security
        $secret_token = 'YourSecretKey123'; 
        
        if ($token !== $secret_token) {
            log_message('error', 'Unauthorized attempt to run update_resigned_employee_status_cron');
            show_error('Unauthorized', 403);
            return;
        }

        $this->load->model('hr_model');
        $updated_count = $this->hr_model->update_resigned_employee_statuses();
        
        $message = "Resigned Employee Status Sync Completed. Users updated: " . $updated_count;
        log_message('info', $message);
        echo $message;
    }
    /**
     * (AJAX) Fetches a single exemption record for editing.
     */
    public function ajax_get_exemption()
    {
        if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
        }

        $this->load->model('hr_model');
        $id = $this->input->post('id');
        $data = $this->hr_model->get_exemption_by_id($id);

        if ($data) {
            $response = ['status' => 'success', 'data' => $data];
        } else {
            $response = ['status' => 'error', 'message' => 'Record not found.'];
        }
        
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * (AJAX) Saves (Adds or Updates) an exemption record.
     */
    public function ajax_save_exemption()
    {
        if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
        }

        $this->load->model('hr_model');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('n1', 'Employee ID', 'required|trim');
        $this->form_validation->set_rules('name', 'Employee Name', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $response = ['status' => 'error', 'message' => validation_errors()];
        } else {
            $id = $this->input->post('id'); // Hidden field for ID
            $employee_id = $this->input->post('n1');

            $data = [
                'n1' => $employee_id,
                'name' => $this->input->post('name')
            ];

            if (empty($id)) {
                // ADD Mode: Check for duplicates
                if ($this->hr_model->check_exemption_exists($employee_id)) {
                    $response = ['status' => 'error', 'message' => 'An exemption record already exists for this employee.'];
                } else {
                    if ($this->hr_model->add_exemption($data)) {
                        $response = ['status' => 'success', 'message' => 'Exemption added successfully.'];
                    } else {
                        $response = ['status' => 'error', 'message' => 'Failed to save data to database.'];
                    }
                }
            } else {
                // EDIT Mode
                if ($this->hr_model->update_exemption($id, $data)) {
                    $response = ['status' => 'success', 'message' => 'Exemption updated successfully.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Failed to update data in database.'];
                }
            }
        }
        
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * (AJAX) Deletes an exemption record.
     */
    public function ajax_delete_exemption()
    {
        if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
        }

        $this->load->model('hr_model');
        $id = $this->input->post('id');

        if (empty($id)) {
            $response = ['status' => 'error', 'message' => 'Invalid ID.'];
        } else {
            if ($this->hr_model->delete_exemption($id)) {
                $response = ['status' => 'success', 'message' => 'Record deleted successfully.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to delete record.'];
            }
        }
        
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    // In hr_model.php

public function get_all_saturday_assignments_mapped()
{
    $this->db->select('employee_id, saturday_date');
    $query = $this->db->get('saturday_work_assignments');
    
    $mapped_data = [];
    foreach ($query->result_array() as $row) {
        $mapped_data[$row['employee_id']][] = $row['saturday_date'];
    }
    return $mapped_data;
}

// In controllers/Users1.php

public function emp_data101()
{ 
    if(!$this->session->userdata('logged_in')){
        redirect('users/login');
    } else { 
        $username = $this->session->userdata('username');
        $allowed_users = array('1835', '2230', '2515', '2774', '2784','2901');
       
        if(in_array($username, $allowed_users)){
            $this->load->model('hr_model');
            
            // --- Fetch Dropdown Data ---
            $data['departments'] = $this->hr_model->get_unique_values('n1');
            $data['companies']   = $this->hr_model->get_unique_values('company_name');
            $data['managers']    = $this->hr_model->get_unique_values('manager');
            $data['locations']   = $this->hr_model->get_unique_values('location');
            $data['positions']   = $this->hr_model->get_unique_values('profession');
            $data['nationalities'] = $this->hr_model->get_unique_values('nationality');
            
            $data['csrf_token_name'] = $this->security->get_csrf_token_name();
            $data['csrf_hash'] = $this->security->get_csrf_hash();
            
$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/emp_data101', $data ?? []);
$this->load->view('template/new_footer');
        } else {
            redirect('users/login');
        }
    }
}




   

   function main_hr1(){ 

    if(!$this->session->userdata('logged_in')){
        redirect('users/login');
    } else { 
        if($this->session->userdata('type') == 10){
            redirect('users/login');
        } else {
          
            $username = $this->session->userdata('username'); 


            $data['get_users']=$this->user_model->get_users101();
            $ee=$data['get_users']['status3'];
            if ($ee !='2') {
                redirect('users/re_pass');
            }else



          
            $allowed_users = array('1835', '2230', '2515', '2774', '2784','2901');

           
            if(in_array($username, $allowed_users)){
                $this->load->view('templateo/main_hr1');
            } else {
                 
                redirect('users/login');
              
            }
        }
    }
}





    function main_emp(){ 
    if(!$this->session->userdata('logged_in')){
        // Check if this is a PWA request
        $is_pwa = $this->input->get('pwa') || $this->session->userdata('is_pwa');
        if ($is_pwa) {
            $this->session->set_userdata('is_pwa', true);
        }
        redirect('users/login');
    } else { 
        if($this->session->userdata('type') == 10){
            redirect('users/login');
        } else {
            $data['get_users'] = $this->user_model->get_users101();
            $ee = $data['get_users']['status3'];
            
            if ($ee != '2') {
                redirect('users1/re_pass');
            } else {
                // ✅ PWA DETECTION AND REDIRECTION
                // Check if this is a PWA request
                $is_pwa = $this->input->get('pwa') || $this->session->userdata('is_pwa');
                
                if ($is_pwa) {
                    // Redirect PWA users to mobile dashboard
                    redirect('users2/mobile_dashboard');
                } else {
                    // Load normal desktop view for non-PWA users
                    $this->load->view('templateo/main_emp');
                }
            }
        }
    }
}

// =============================================================
    // ALL-IN-ONE RENEWAL SYSTEM (Users1.php)
    // =============================================================

// =============================================================
    // CONTROLLER: Matches your "Single View" exactly
    // =============================================================

   // =============================================================
    // FIXED CONTROLLER: RENEWAL SYSTEM (Self-Contained)
    // =============================================================

    
public function renewal_system()
    {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        $user_id = $this->session->userdata('username');
        $this->load->model('hr_model');
        
        $data = [];
        $data['user_id'] = $user_id;
        $data['managers'] = []; 

        // 0. GET STATIC APPROVER NAMES
        $approver_ids = ['1127', '2230', '1001'];
        $app_query = $this->db->select('employee_id, subscriber_name')
                              ->where_in('employee_id', $approver_ids)
                              ->get('emp1')
                              ->result_array();
        $data['app_names'] = [];
        foreach($app_query as $row) {
            $data['app_names'][$row['employee_id']] = $row['subscriber_name'];
        }

        // 1. GET MANAGERS (For HR Admin 1127)
        if ($user_id == '1127') {
             $data['managers'] = $this->hr_model->get_employeess();
             $data['all_employees'] = $this->db->select('employee_id, subscriber_name')
                                               ->get('emp1')
                                               ->result_array();
        }

        // 2. GET PENDING TASKS (FIXED LOGIC FOR 1127)
        $this->db->select('r.*, e.subscriber_name, e.n1 as department, e.job_tag, e.joining_date, e.Iqama_expiry_date, eval.subscriber_name as evaluator_name');
        $this->db->from('id_renewal_requests r');
        $this->db->join('emp1 e', 'e.employee_id = r.emp_id', 'left'); 
        $this->db->join('emp1 eval', 'eval.employee_id = r.evaluator_id', 'left'); 

        if ($user_id == '1127') {
            // FIX: Use group_start() to combine OR conditions
            $this->db->group_start();
                // Role 1: HR Admin duties
                $this->db->where_in('r.status', ['pending_hr_action', 'pending_renewal']);
                
                // Role 2: Evaluator duties (OR condition)
                $this->db->or_group_start();
                    $this->db->where('r.status', 'pending_evaluation');
                    $this->db->where('r.evaluator_id', '1127');
                $this->db->group_end();
            $this->db->group_end();
            
        } elseif ($user_id == '2230') {
            $this->db->where('r.status', 'pending_hr_manager');
        } elseif ($user_id == '1001') {
            $this->db->where('r.status', 'pending_ceo');
        } else {
            $this->db->where('r.evaluator_id', $user_id);
            $this->db->where('r.status', 'pending_evaluation');
        }
        $data['my_tasks'] = $this->db->get()->result_array();

        // 3. GET MY REQUESTS
        $this->db->select('r.*, eval.subscriber_name as evaluator_name');
        $this->db->from('id_renewal_requests r');
        $this->db->join('emp1 eval', 'eval.employee_id = r.evaluator_id', 'left');
        $this->db->where('r.emp_id', $user_id);
        $this->db->order_by('r.id', 'DESC');
        $data['my_requests'] = $this->db->get()->result_array();

        // 4. GET ALL HISTORY
        $data['all_history'] = [];
        if (in_array($user_id, ['1127', '2230', '1001'])) {
            $this->db->select('r.*, e.subscriber_name, eval.subscriber_name as evaluator_name');
            $this->db->from('id_renewal_requests r');
            $this->db->join('emp1 e', 'e.employee_id = r.emp_id', 'left');
            $this->db->join('emp1 eval', 'eval.employee_id = r.evaluator_id', 'left');
            $this->db->order_by('r.id', 'DESC');
            $data['all_history'] = $this->db->get()->result_array();
        }

        // 5. ATTACH WORKFLOW LOGS
        foreach(['my_tasks', 'my_requests', 'all_history'] as $key) {
            if(!empty($data[$key])) {
                foreach($data[$key] as &$item) {
                    $item['history_logs'] = $this->hr_model->get_request_history($item['id']);
                }
            }
        }

        $data['my_info'] = $this->db->where('employee_id', $user_id)->get('emp1')->row_array();

$this->load->view('template/new_header_and_sidebar', $data ?? []);
$this->load->view('templateo/renewal_system_view', $data ?? []);
$this->load->view('template/new_footer');
    }

    public function process_renewal_system()
    {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        $action = $this->input->post('action_type');
        $id = $this->input->post('req_id');
        $user_id = $this->session->userdata('username');
        $this->load->model('hr_model');
        
        // ACTION: CREATE
        if ($action == 'create') {
            
            // --- [NEW] Determine who the request is for ---
            $target_emp_id = $user_id; // Default to self
            
            // If User is 1127 AND they selected someone from the list
            if ($user_id == '1127' && $this->input->post('emp_id_manual')) {
                $target_emp_id = $this->input->post('emp_id_manual');
            }
            // ----------------------------------------------

            $this->db->insert('id_renewal_requests', [
                'emp_id' => $target_emp_id, // Use the determined ID
                'request_date' => date('Y-m-d'),
                'current_expiry_date' => $this->input->post('expiry_date'),
                'status' => 'pending_hr_action',
                'created_by' => $user_id
            ]);
            $req_id = $this->db->insert_id();
            
            // Log differently if created on behalf
            $log_msg = ($target_emp_id == $user_id) ? 'Created Request' : "Created Request on behalf of $target_emp_id";
            $this->hr_model->log_history($req_id, $user_id, $log_msg); 
            
            $this->session->set_flashdata('success', 'Request Created Successfully');
        }
        
        // ... [Rest of the actions (assign, evaluate, approve, complete) remain unchanged] ...
        elseif ($action == 'assign') {
            $eval_id = $this->input->post('evaluator_id');
            $this->db->where('id', $id)->update('id_renewal_requests', [
                'evaluator_id' => $eval_id, 
                'status' => 'pending_evaluation'
            ]);
            $this->hr_model->log_history($id, $user_id, 'Assigned Evaluator: ' . $eval_id);
            $this->session->set_flashdata('success', 'Evaluator Assigned');
        }
        elseif ($action == 'evaluate') {
            $total = $this->input->post('attendance') + $this->input->post('behaviour') + $this->input->post('tasks');
            $this->db->where('id', $id)->update('id_renewal_requests', [
                'score_attendance' => $this->input->post('attendance'),
                'score_behaviour' => $this->input->post('behaviour'),
                'score_tasks' => $this->input->post('tasks'),
                'total_score' => $total,
                'status' => 'pending_hr_manager'
            ]);
            $this->hr_model->log_history($id, $user_id, "Submitted Evaluation (Score: $total%)");
            $this->session->set_flashdata('success', 'Evaluation Submitted');
        }
        elseif ($action == 'approve') {
            $next = ($user_id == '2230') ? 'pending_ceo' : 'pending_renewal';
            $role = ($user_id == '2230') ? 'HR Manager' : 'CEO';
            $this->db->where('id', $id)->update('id_renewal_requests', ['status' => $next]);
            $this->hr_model->log_history($id, $user_id, "Approved by $role");
            $this->session->set_flashdata('success', 'Request Approved');
        }
        elseif ($action == 'complete') {
            $this->db->where('id', $id)->update('id_renewal_requests', ['status' => 'renewed']);
            $req = $this->hr_model->get_renewal_request_by_id($id);
            $this->hr_model->update_employee_iqama_expiry($req['emp_id']);
            $this->hr_model->log_history($id, $user_id, 'Completed Renewal Process');
            $this->session->set_flashdata('success', 'Renewal Completed');
        }

        redirect('users1/renewal_system');
    }
    

    public function main_ev() {
        
        
        $this->load->view('templateo/main_ev');
      
    }

    public function main_seitting() {
        
        
        $this->load->view('templateo/main_seitting');
      
    }

    public function main_users() {
        
        
        $this->load->view('templateo/main_users');
      
    }

    public function main_model() {
        
        
        $this->load->view('templateo/main_model');
      
    }

    public function mm() {
        
        
        $this->load->view('templateo/mm');
      
    }



  public function main_salary() {

    // 1) التأكد من تسجيل الدخول
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }

    // 2) تحديد المستخدم الحالي وقائمة المصرح لهم
    $username = $this->session->userdata('username');
    $allowed_users = array('1835', '2230', '2515', '2774', '2784', '2901');

    // 3) التحقق من الصلاحية
    if (in_array($username, $allowed_users)) {
        $this->load->view('templateo/main_salary');
    } else {
        // إعادة توجيه أو منع
        redirect('users/login');
        // أو تقدر تستخدم:
        // show_error("غير مصرح لك بالدخول إلى هذه الصفحة", 403);
    }
}



    public function emp_view() {
        $data = [];
        $data['active_page'] = 'employees';
        $data['page_title']  = 'قائمة الموظفين';

        // اجلب باراميترات البحث إن وجدت (GET)
        $data['search_field'] = $this->input->get('field', true);
        $data['search_q']     = $this->input->get('q', true);

        // مثال بيانات (بدّلها ببياناتك من DB)
        $data['employees'] = [
            [
                'employee_no' => '1',
                'national_id' => '1234567890',
                'name'        => 'صالح السالمي',
                'title'       => 'اخصائي موارد بشرية',
                'department'  => 'الإدارة العامة',
                'project'     => 'المالية - ERP',
                'branch'      => 'الرياض',
                'join_date'   => '01-12-2019',
                'status'      => 'نشط',
            ],
            [
                'employee_no' => '2',
                'national_id' => '2345678901',
                'name'        => 'أحمد محمد',
                'title'       => 'محاسب',
                'department'  => 'المالية',
                'project'     => 'المحاسبة - الفروع',
                'branch'      => 'جدة',
                'join_date'   => '15-05-2020',
                'status'      => 'متوقف',
            ],
        ];

$this->load->view('template/new_header_and_sidebar', $data ?? []); 
$this->load->view('templateo/emp_view', $data ?? []);
$this->load->view('template/new_footer');
 
    }





       function index1_1(){ 
        

             $this->load->view('templateo/index1_1');   


          
          
      }

       

       function main2(){ 
        

             $this->load->view('templateo/main2');   


          
          
      }

      function main_hr(){ 
        

             $this->load->view('templateo/main_hr');   


          
          
      }

       function test(){ 
        

             $this->load->view('templateo/test');   


          
          
      }

      function test1(){ 
        

             $this->load->view('templateo/test1');   


          
          
      }


        function add_project(){

         
          
               // $data['get_emkan'] = $this->hr_model->get_emkan($id);
                 
                      $data['title'] = 'اضافة مستخدم جديد';
                      $this->form_validation->set_rules('name', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){
                     
                      $this->load->view('template/new_header_and_sidebar', $data ?? []);
                      $this->load->view('templateo/add_project', $data ?? []);
                      $this->load->view('template/new_footer'); 
                } else {
 
                            
                                
                               
                                          $this->hr_model->add_project();   
                                    
                               
                             
                            
                          

                           

                           


           
            redirect('users/project_index');
          
      }
    }

         function project_index(){

         
          
               // $data['get_emkan'] = $this->hr_model->get_emkan($id);
                        $id5= $this->hr_model->max_tran_id();
                 
                      $data['title'] = 'اضافة مستخدم جديد';
                      $this->form_validation->set_rules('name', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){ 
                      $this->load->view('template/new_header_and_sidebar', $data ?? []);
                      $this->load->view('templateo/project_index', $data ?? []);
                      $this->load->view('template/new_footer'); 
                } else {
 
                            
                                
                               
                                          $this->hr_model->add_project();   
                                    
          
           
            redirect('users/project_index');
          
      }
    }

      function dashbord_analyses11115588125998369(){ 

   



           

                 $this->hr_model->export_csv();

          


      
    }



      function main(){

         
          
               // $data['get_emkan'] = $this->hr_model->get_emkan($id);
                        $id5= $this->hr_model->max_tran_id();
                 
                      $data['title'] = 'اضافة مستخدم جديد';
                      $this->form_validation->set_rules('name', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){
                      $this->load->view('templateo/header2');
                      $this->load->view('templateo/main', $data);
                      $this->load->view('templateo/footer2');
                } else {
 
                            
                                
                               
                                          $this->hr_model->add_project();   
                                    
          
           
            redirect('users/project_index');
          
      }
    }



        function dashbord_analyses(){ 
          
                  
                    if ($this->session->userdata('type') == 1) {
                      
                    }elseif ($this->session->userdata('type') == 2) {
                     
                    }elseif ($this->session->userdata('type') == 3) {
                        
                    }elseif ($this->session->userdata('type') == 6) {
                     
                    }elseif ($this->session->userdata('type') == 4) {
                     
                    }elseif ($this->session->userdata('type') == 5) {
                     
                    }
 
        
          
         $this->load->view('templateo/header2'); 
         $this->load->view('templateo/dashbord_analyses',$data);
         $this->load->view('templateo/footer2');  
      
    }

       function index1_2(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_2');   


          }
          
      }

       function index1_3(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_3');   


          }
          
      }

       function index1_4(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_4');   


          }
          
      }
       function index1_5(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_5');   


          }
          
      }
       function index1_6(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_6');   


          }
          
      }
       function index1_7(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_7');   


          }
          
      }
       function index1_8(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_8');   


          }
          
      }
       function index1_9(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_9');   


          }
          
      }
       function index1_10(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_10');   


          }
          
      }
       function index1_11(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_11');   


          }
          
      }

       function index1_12(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_12');   


          }
          
      }

       function index1_13(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_13');   


          }
          
      }

       function index1_14(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/index1_14');   


          }
          
      }


      // ضعها داخل كلاس Users1 (برا الدوال الأخرى)
private function build_tree($rootId, $edges, $uMap, $pMap) {
    $visited = [];
    $walk = function($id) use (&$walk, &$visited, $edges, $uMap, $pMap) {
        if (!$id || isset($visited[$id])) return null; // حماية من الدورات
        $visited[$id] = true;

        $node = [
            'id'         => $id,
            'name'       => $uMap[$id] ?? $id,         // اسم الموظف (أو الرقم لو غير موجود)
            'profession' => $pMap[$id] ?? '',          // المسمى الوظيفي
            'children'   => []
        ];

        if (isset($edges[$id])) {
            foreach (array_keys($edges[$id]) as $child) {
                if ($child === $id) continue;
                $childNode = $walk($child);
                if ($childNode) $node['children'][] = $childNode;
            }
        }
        return $node;
    };
    return $walk($rootId);
}

public function payroll_compare()
{
    $this->load->model('hr_model', 'payroll');

    // الأشهر من GET بصيغة YYYY-MM
    $m1 = $this->input->get('m1') ?: date('Y-m', strtotime('first day of -1 month'));
    $m2 = $this->input->get('m2') ?: date('Y-m');

    $companies = [1, 2]; // 1 مرسوم، 2 مكتب الدكتور
    $out = [];

    foreach ($companies as $c) {
        $name = $this->payroll->company_name($c);

        $leftOnly  = $this->payroll->left_only($c, $m1, $m2);   // موجود في الأول فقط
        $rightOnly = $this->payroll->right_only($c, $m1, $m2);  // موجود في الثاني فقط
        $sum1      = $this->payroll->month_summary($c, $m1);
        $sum2      = $this->payroll->month_summary($c, $m2);

        // ملخص المفقود/المضاف
        $leftTotals = [
            'count' => count($leftOnly),
            'salary'=> array_sum(array_column($leftOnly, 'total_salary')),
        ];
        $rightTotals = [
            'count' => count($rightOnly),
            'salary'=> array_sum(array_column($rightOnly, 'total_salary')),
        ];

        // تجهيز مؤشرات الزيادة/النقص لكل بند
        $metrics = [
            'emp_count'           => 'عدد الموظفين',
            'total_salary'        => 'إجمالي الرواتب (n6)',
            'late_penalties'      => 'مخالفات التأخير (n7)',
            'early_leave_penalties'=> 'الخروج المبكر (n8)',
            'absences_deductions' => 'خصومات الغياب (n9)',
            'social_insurance'    => 'التأمينات الاجتماعية (n10)',
            'total_deductions'    => 'إجمالي الخصومات (n11)',
            'net_salary'          => 'صافي الرواتب (n12)',
        ];
        $changes = [];
        foreach ($metrics as $k => $label) {
            $v1 = (float)($sum1[$k] ?? 0);
            $v2 = (float)($sum2[$k] ?? 0);
            $delta = $v2 - $v1;
            $pct = ($v1 != 0) ? ($delta / $v1 * 100) : ($v2 != 0 ? 100 : 0);
            $trend = ($delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat'));
            $changes[] = compact('k','label','v1','v2','delta','pct','trend');
        }

        $out[$c] = [
            'name'        => $name,
            'leftOnly'    => $leftOnly,
            'rightOnly'   => $rightOnly,
            'leftTotals'  => $leftTotals,
            'rightTotals' => $rightTotals,
            'sum1'        => $sum1,
            'sum2'        => $sum2,
            'changes'     => $changes,
        ];
    }

    $data = [
        'm1' => $m1,
        'm2' => $m2,
        'companies' => $out,
    ];

    $this->load->view('templateo/payroll_compare_a4', $data);
}


public function leave_balances_up()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    $this->load->model('hr_model');
    
    // Data for the "View Balances" tab
    $data['balances'] = $this->hr_model->get_all_employee_balances();
    
    // Data for the "Manual Entry" tab
    $data['leave_types'] = $this->hr_model->get_leave_types(); 
    $data['all_employees'] = $this->hr_model->get_all_employees(); // Added this
    
    // Pass CSRF tokens
    $data['csrf_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();
    
    // Assuming your view file is named 'leave_balances_view.php'
    $this->load->view('templateo/leave_balances_view', $data);
}

// ACTION 2: ADD this new function anywhere in Users1.php
public function export_balances_excel()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    $this->load->dbutil(); // Load the Database Utility
    $this->load->helper('download'); 

    // Re-run the query from the model to get all data
    $this->db->select("
        elb.employee_id AS 'الرقم الوظيفي',
        emp.subscriber_name AS 'اسم الموظف',
        lt.name_ar AS 'نوع الإجازة',
        elb.balance_allotted AS 'المخصص',
        elb.balance_consumed AS 'المستهلك',
        elb.remaining_balance AS 'المتبقي',
        elb.year AS 'السنة'
    ");
    $this->db->from('employee_leave_balances AS elb');
    $this->db->join('emp1 AS emp', 'emp.employee_id = elb.employee_id', 'left');
    $this->db->join('leave_types AS lt', 'lt.slug = elb.leave_type_slug', 'left');
    $this->db->order_by('emp.subscriber_name', 'ASC');
    $this->db->order_by('elb.year', 'DESC');
    $query = $this->db->get();

    // Define export parameters
    $delimiter = ",";
    $newline = "\r\n";
    $filename = "leave_balances_export_" . date('Y-m-d') . ".csv";

    // Use dbutil to create CSV data
    $csv_data = $this->dbutil->csv_from_result($query, $delimiter, $newline);

    // Force download with UTF-8 BOM for Excel/Arabic compatibility
    force_download($filename, "\xEF\xBB\xBF" . $csv_data);
}

/**
 * AJAX endpoint for the Manual Entry form.
 */
public function add_manual_balance()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $this->load->model('hr_model');
    
    $data = [
        'employee_id'       => $this->input->post('employee_id'),
        'leave_type_slug'   => $this->input->post('leave_type_slug'),
        'balance_allotted'  => (int)$this->input->post('balance_allotted'),
        'year'              => (int)$this->input->post('year')
    ];

    $success = $this->hr_model->upsert_employee_balance($data);

    if ($success) {
        $response = ['status' => 'success', 'message' => 'تم حفظ الرصيد بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل حفظ الرصيد.'];
    }

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

/**
 * AJAX endpoint for the Excel Upload form.
 */
// In application/controllers/Users1.php

public function upload_balance_sheet()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $this->load->model('hr_model');

    // --- Standard CodeIgniter File Upload ---
    $config['upload_path'] = './uploads/documents/';
    $config['allowed_types'] = 'xlsx';
    if (!is_dir($config['upload_path'])) { mkdir($config['upload_path'], 0777, true); }
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('balance_file')) {
        $response = ['status' => 'error', 'message' => $this->upload->display_errors()];
    } else {
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];

        // --- NEW LOGIC USING SimpleXLSX ---
        // 1. Load the library
        $this->load->library('SimpleXLSX');
        
        // 2. Parse the Excel file
        if ($xlsx = SimpleXLSX::parse($file_path)) {
            $balances_to_upsert = [];
            
            // 3. Loop through the rows (skip the header row)
            foreach ($xlsx->rows(0, true) as $row) {
                // The key of each item will be the header from the Excel file
                if (!empty($row['employee_id']) && !empty($row['leave_type_slug'])) {
                    $balances_to_upsert[] = [
                        'employee_id'      => trim($row['employee_id']),
                        'leave_type_slug'  => trim($row['leave_type_slug']),
                        'balance_allotted' => (int)trim($row['balance_allotted']),
                        'year'             => (int)trim($row['year'])
                    ];
                }
            }

            if (empty($balances_to_upsert)) {
                 $response = ['status' => 'error', 'message' => 'الملف فارغ أو أن رؤوس الأعمدة غير صحيحة.'];
            } else {
                $success = $this->hr_model->upsert_batch_balances($balances_to_upsert);
                if ($success) {
                    $response = ['status' => 'success', 'message' => 'تمت معالجة ' . count($balances_to_upsert) . ' سجل بنجاح.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ البيانات في قاعدة البيانات.'];
                }
            }
        } else {
            // If the library fails to parse the file
            $response = ['status' => 'error', 'message' => 'فشل في قراءة ملف الإكسل. تأكد من أن الملف غير تالف.'];
        }
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
// In Users1.php, add this new function

public function ajax_get_balances_for_employee($employee_id = '')
{
    // Security check: ensure this is an AJAX request and user is logged in
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output('Forbidden');
    }

    $target_employee_id = trim((string)$employee_id);

    // If no employee ID is provided, default to the logged-in user
    if (empty($target_employee_id)) {
        $target_employee_id = $this->session->userdata('username');
    }
    
    $this->load->model('hr_model');
    $balances = $this->hr_model->get_employee_balances($target_employee_id);

    // Return the balances as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'balances' => $balances]));
}
public function update_leave_balance()
{
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }
    
    if (!$this->session->userdata('logged_in')) {
        $this->output->set_status_header(403);
        echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
        return;
    }

    $this->load->model('hr_model');

    // ✅ **FIX:** Get the composite key from the POST data.
    $keys = [
        'employee_id'       => $this->input->post('employee_id'),
        'leave_type_slug'   => $this->input->post('leave_type_slug'),
      //  'year'              => $this->input->post('year')
    ];
    
    $data = [
        'balance_allotted' => $this->input->post('balance_allotted'),
        'balance_consumed' => $this->input->post('balance_consumed')
    ];

    if (empty($keys['employee_id']) || empty($keys['leave_type_slug']) || empty($keys['year'])) {
        $response = ['status' => 'error', 'message' => 'Balance record key is missing.'];
    } else {
        $success = $this->hr_model->update_balance_record($keys, $data);
        if ($success) {
            $response = ['status' => 'success', 'message' => 'تم تحديث الرصيد بنجاح.'];
        } else {
            $response = ['status' => 'error', 'message' => 'فشل تحديث الرصيد في قاعدة البيانات.'];
        }
    }
    
    // Add new CSRF hash to the response for security
    $response['csrf_name'] = $this->security->get_csrf_token_name();
    $response['csrf_hash'] = $this->security->get_csrf_hash();

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}



      // Users1.php (اختصر/استبدل الكود الذي يبني الشجرة)
  public function org_pyramid()
{
    $this->load->model('hr_model');

    // رأس الهرم (الرقم الوظيفي) من GET ?head=1001 مثلاً
    $head = trim($this->input->get('head', TRUE) ?? '');

    // 1) نبني العلاقات من كل الصفوف
    $rows = $this->hr_model->get_all_org_paths();
    if (!$rows) {
        $data = ['head' => $head, 'levels' => [], 'edges' => [], 'map' => [], 'error' => 'لا توجد بيانات في organizational_structure.'];
        return $this->load->view('templateo/org_pyramid', $data);
    }

    // لو ما انرسل head نختار أول n1 موجود
    if ($head === '') {
        foreach ($rows as $r) { if (!empty($r['n1'])) { $head = (string)$r['n1']; break; } }
    }

    // adjacency: parent → set(children)
    $adj = []; 
    foreach ($rows as $r) {
        $path = [];
        for ($i = 1; $i <= 7; $i++) {
            $v = isset($r["n{$i}"]) ? trim((string)$r["n{$i}"]) : '';
            if ($v !== '') $path[] = $v;
        }
        for ($i = 0; $i + 1 < count($path); $i++) {
            $p = $path[$i]; $c = $path[$i+1];
            if (!isset($adj[$p])) $adj[$p] = [];
            $adj[$p][$c] = true; // de-duplicate
        }
    }

    // 2) BFS من الرأس لالتقاط العقد القابلة للوصول
    $levels = [];          // level => [nodes]
    $edges  = [];          // [[parent, child], ...] (للرسم)
    $seen   = [];          // visited set
    $queue  = [];

    if ($head !== '') { $queue[] = [$head, 0]; $seen[$head] = true; }

    while (!empty($queue)) {
        [$node, $lvl] = array_shift($queue);
        $levels[$lvl][] = $node;

        $children = isset($adj[$node]) ? array_keys($adj[$node]) : [];
        foreach ($children as $ch) {
            $edges[] = [$node, $ch];
            if (!isset($seen[$ch])) {
                $seen[$ch] = true;
                $queue[] = [$ch, $lvl + 1];
            }
        }
    }

    // 3) أسماء + مسميات وظيفية
    $ids  = array_keys($seen);
    $info = $this->hr_model->get_people_bulk($ids);
    $map  = [];
    foreach ($ids as $id) {
        $map[$id] = (object)($info[$id] ?? ['name' => $id, 'title' => '—']);
    }

    // ترتيب المستويات
    ksort($levels);
    foreach ($levels as &$arr) { $arr = array_values(array_unique($arr)); }

    $data = compact('head','levels','edges','map');
    $this->load->view('templateo/org_pyramid', $data);
}



       function extn(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{
           $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];

            
              $data['get_extension'] =  $this->hr_model->get_extension($id); 

             





             $this->load->view('templateo/extn', $data);  


          }
          
      }


       function forgotpassword(){ 
        // if(!$this ->session->userdata('logged_in')){
        //     redirect('users/login');
        //   }else{

             $this->load->view('templateo/forgotpassword');   


          // }
          
      }


       function news(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/news');   


          }
          
      }

      function splash(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/splash');   


          }
          
      }

       function twa(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{

             $this->load->view('templateo/twa');   


          }
          
      }






      




     function chekotp_false(){ 

             if(!$this ->session->userdata('logged_in')){
                redirect('users/login');
            }elseif($this->session->userdata('type')==6){
            
             $this->load->view('templateo/chekotp_false'); 

            }else
      redirect('users/login');


            
      }

      public function check_otp(){

       
              $data['title'] = 'شاشة الدخول';
              $username=$this->session->userdata('username');
              $data['get_users_otp'] =  $this->hr_model->get_users_otp($username); 
              $otp1= $data['get_users_otp']['otp'];
              $otp2 = $this->input->post('otp');
              $otp = hash('sha256', $otp2);
              $this->form_validation->set_rules('otp', 'otp', 'required');
              if($this->form_validation->run() === FALSE){    
                $this->load->view('templateo/check_otp1', $data);   
              } else {
        
               if ($otp == $otp1) {
                  $user_data = array(
         
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);  

          if ($this->session->userdata('type') == 1) {
               redirect('users/index1');
          }elseif($this->session->userdata('type') == 3){
             redirect('users/index1');

          }


               

               }else{
                 redirect('users/chekotp_false'); 
              

                 }
              if ($this->session->userdata('type') == 1) {
               redirect('users/main361');
          }elseif($this->session->userdata('type') == 3){
             redirect('users/main366all1');

          }
        }

            


     
     
    }


    public function sync_attendance_logs_cron($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_improved();
}

public function fetch_attendance_logs_improved() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'AF4C232560102',
        'max_pages'    => 100, // Safety limit
        'batch_size'   => 50,  // Records to process at once
    ];

    // Get last sync time from database
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from('attendance_logs')
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page <= $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            // No more data
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    continue;
                }

                // Create unique hash for record
                $record_hash = md5(
                    ($record['emp_code'] ?? '') . 
                    ($record['punch_time'] ?? '') . 
                    ($record['terminal_sn'] ?? '')
                );

                // Check if record exists
                $exists = $this->db->select('id')
                    ->from('attendance_logs')
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->where('terminal_sn', $record['terminal_sn'] ?? null)
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $record['terminal_sn'] ?? null,
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch('attendance_logs', $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record: ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch('attendance_logs', $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            break;
        }
    }

    // Log results
    $message = "Sync completed. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

protected function make_api_request($url, $username, $password) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERPWD        => "$username:$password",
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        CURLOPT_FAILONERROR    => true,
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        log_message('error', 'cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code != 200) {
        log_message('error', "API returned HTTP code: $http_code");
        return false;
    }

    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        log_message('error', 'JSON decode error: ' . json_last_error_msg());
        return false;
    }

    return $data;
}
public function live_attendance_sync($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour (adjust as needed)
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=AF4C232560102";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode($last_sync_time);
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            foreach ($response['data'] as $record) {
                // Your existing record processing logic here
                // ...
                $inserted++;
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check
        sleep(60); // Check every minute
    }
}


public function sync_attendance_logs_rkq4241900202_cron($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_rkq4241900202();
}

public function fetch_attendance_logs_rkq4241900202() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration for RKQ4241900202 device
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'RKQ4241900202',
        'max_pages'    => 2600, // Safety limit
        'batch_size'   => 50,  // Records to process at once
    ];

    // Get last sync time from database
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from('attendance_logs_rkq4241900202')
                          ->where('terminal_sn', $config['terminal_sn'])
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page <= $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            // No more data
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    continue;
                }

                // Check if record exists
                $exists = $this->db->select('id')
                    ->from('attendance_logs_rkq4241900202')
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->where('terminal_sn', $config['terminal_sn'])
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $config['terminal_sn'],
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch('attendance_logs_rkq4241900202', $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record: ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch('attendance_logs_rkq4241900202', $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            break;
        }
    }

    // Log results
    $message = "Sync completed for device RKQ4241900202. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

public function live_attendance_sync_rkq4241900202($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour (adjust as needed)
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time for this specific device
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs_rkq4241900202')
                              ->where('terminal_sn', 'RKQ4241900202')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=RKQ4241900202";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode($last_sync_time);
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            $batch = [];
            
            foreach ($response['data'] as $record) {
                try {
                    // Check if record exists
                    $exists = $this->db->select('id')
                        ->from('attendance_logs_rkq4241900202')
                        ->where('emp_code', $record['emp_code'] ?? null)
                        ->where('punch_time', $record['punch_time'] ?? null)
                        ->where('terminal_sn', 'RKQ4241900202')
                        ->get()
                        ->row();

                    if (!$exists) {
                        $batch[] = [
                            'emp_code'       => $record['emp_code'] ?? null,
                            'first_name'     => $record['first_name'] ?? null,
                            'last_name'      => $record['last_name'] ?? null,
                            'punch_time'     => $record['punch_time'] ?? null,
                            'punch_state'    => $record['punch_state_display'] ?? null,
                            'area_alias'     => $record['area_alias'] ?? null,
                            'terminal_sn'    => 'RKQ4241900202',
                            'terminal_alias' => $record['terminal_alias'] ?? null,
                            'upload_time'    => $record['upload_time'] ?? null
                        ];
                        $inserted++;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing record: ' . $e->getMessage());
                }
            }
            
            // Insert records if any
            if (!empty($batch)) {
                $this->db->insert_batch('attendance_logs_rkq4241900202', $batch);
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'time' => date('Y-m-d H:i:s'),
                'device' => 'RKQ4241900202'
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'time' => date('Y-m-d H:i:s'),
                'device' => 'RKQ4241900202'
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check
        sleep(60); // Check every minute
    }
}


public function sync_attendance_logs_osa7010056122201235_cron($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_osa7010056122201235();
}



public function fetch_attendance_logs_osa7010056122201235() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration for OSA7010056122201235 device
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'OSA7010056122201235',
        'max_pages'    => 7600, // Safety limit
        'batch_size'   => 50,  // Records to process at once
    ];

    // Get last sync time from dedicated table
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from('attendance_logs_osa7010056122201235')
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page = $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            // No more data
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    continue;
                }

                // Check if record exists in dedicated table
                $exists = $this->db->select('id')
                    ->from('attendance_logs_osa7010056122201235')
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $config['terminal_sn'],
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch('attendance_logs_osa7010056122201235', $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record: ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch('attendance_logs_osa7010056122201235', $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            break;
        }
    }

    // Log results
    $message = "Sync completed for device OSA7010056122201235. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

public function live_attendance_sync_osa7010056122201235($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour (adjust as needed)
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time from dedicated table
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs_osa7010056122201235')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=OSA7010056122201235";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode($last_sync_time);
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            $batch = [];
            
            foreach ($response['data'] as $record) {
                try {
                    // Check if record exists in dedicated table
                    $exists = $this->db->select('id')
                        ->from('attendance_logs_osa7010056122201235')
                        ->where('emp_code', $record['emp_code'] ?? null)
                        ->where('punch_time', $record['punch_time'] ?? null)
                        ->get()
                        ->row();

                    if (!$exists) {
                        $batch[] = [
                            'emp_code'       => $record['emp_code'] ?? null,
                            'first_name'     => $record['first_name'] ?? null,
                            'last_name'      => $record['last_name'] ?? null,
                            'punch_time'     => $record['punch_time'] ?? null,
                            'punch_state'    => $record['punch_state_display'] ?? null,
                            'area_alias'     => $record['area_alias'] ?? null,
                            'terminal_sn'    => 'OSA7010056122201235',
                            'terminal_alias' => $record['terminal_alias'] ?? null,
                            'upload_time'    => $record['upload_time'] ?? null
                        ];
                        $inserted++;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing record: ' . $e->getMessage());
                }
            }
            
            // Insert records if any
            if (!empty($batch)) {
                $this->db->insert_batch('attendance_logs_osa7010056122201235', $batch);
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'time' => date('Y-m-d H:i:s'),
                'device' => 'OSA7010056122201235'
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'time' => date('Y-m-d H:i:s'),
                'device' => 'OSA7010056122201235'
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check
        sleep(60); // Check every minute
    }
}



public function sync_attendance_logs_cron_af4c194960167($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_improved_af4c194960167();
}

public function fetch_attendance_logs_improved_af4c194960167() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration for this specific device
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'AF4C194960167',
        'max_pages'    => 10400, // Safety limit
        'batch_size'   => 50,  // Records to process at once
        'table_name'   => 'attendance_logs_af4c194960167',
    ];

    // Get last sync time from database
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from($config['table_name'])
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page <= $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            // No more data
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    continue;
                }

                // Check if record exists
                $exists = $this->db->select('id')
                    ->from($config['table_name'])
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->where('terminal_sn', $record['terminal_sn'] ?? null)
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $record['terminal_sn'] ?? null,
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch($config['table_name'], $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record: ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch($config['table_name'], $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            break;
        }
    }

    // Log results
    $message = "Sync completed for device AF4C194960167. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

public function live_attendance_sync_af4c194960167($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour (adjust as needed)
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs_af4c194960167')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=AF4C194960167";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode($last_sync_time);
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            $batch = [];
            
            foreach ($response['data'] as $record) {
                try {
                    // Check if record exists
                    $exists = $this->db->select('id')
                        ->from('attendance_logs_af4c194960167')
                        ->where('emp_code', $record['emp_code'] ?? null)
                        ->where('punch_time', $record['punch_time'] ?? null)
                        ->where('terminal_sn', $record['terminal_sn'] ?? null)
                        ->get()
                        ->row();

                    if (!$exists) {
                        $batch[] = [
                            'emp_code'       => $record['emp_code'] ?? null,
                            'first_name'     => $record['first_name'] ?? null,
                            'last_name'      => $record['last_name'] ?? null,
                            'punch_time'     => $record['punch_time'] ?? null,
                            'punch_state'    => $record['punch_state_display'] ?? null,
                            'area_alias'     => $record['area_alias'] ?? null,
                            'terminal_sn'    => $record['terminal_sn'] ?? null,
                            'terminal_alias' => $record['terminal_alias'] ?? null,
                            'upload_time'    => $record['upload_time'] ?? null
                        ];
                        
                        $inserted++;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing record: ' . $e->getMessage());
                }
            }
            
            // Insert new records
            if (!empty($batch)) {
                $this->db->insert_batch('attendance_logs_af4c194960167', $batch);
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check
        sleep(60); // Check every minute
    }
}




public function sync_attendance_logs_cron_af4c203360515($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_improved_af4c203360515();
}

public function fetch_attendance_logs_improved_af4c203360515() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration for this specific device
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'AF4C203360515',
        'max_pages'    => 12000, // Safety limit
        'batch_size'   => 50,  // Records to process at once
        'table_name'   => 'attendance_logs_af4c203360515',
    ];

    // Get last sync time from database
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from($config['table_name'])
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page <= $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            // No more data
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    continue;
                }

                // Check if record exists
                $exists = $this->db->select('id')
                    ->from($config['table_name'])
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->where('terminal_sn', $record['terminal_sn'] ?? null)
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $record['terminal_sn'] ?? null,
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch($config['table_name'], $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record: ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch($config['table_name'], $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            break;
        }
    }

    // Log results
    $message = "Sync completed for device AF4C203360515. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

public function live_attendance_sync_af4c203360515($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour (adjust as needed)
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs_af4c203360515')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=AF4C203360515";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode($last_sync_time);
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            $batch = [];
            
            foreach ($response['data'] as $record) {
                try {
                    // Check if record exists
                    $exists = $this->db->select('id')
                        ->from('attendance_logs_af4c203360515')
                        ->where('emp_code', $record['emp_code'] ?? null)
                        ->where('punch_time', $record['punch_time'] ?? null)
                        ->where('terminal_sn', $record['terminal_sn'] ?? null)
                        ->get()
                        ->row();

                    if (!$exists) {
                        $batch[] = [
                            'emp_code'       => $record['emp_code'] ?? null,
                            'first_name'     => $record['first_name'] ?? null,
                            'last_name'      => $record['last_name'] ?? null,
                            'punch_time'     => $record['punch_time'] ?? null,
                            'punch_state'    => $record['punch_state_display'] ?? null,
                            'area_alias'     => $record['area_alias'] ?? null,
                            'terminal_sn'    => $record['terminal_sn'] ?? null,
                            'terminal_alias' => $record['terminal_alias'] ?? null,
                            'upload_time'    => $record['upload_time'] ?? null
                        ];
                        
                        $inserted++;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing record: ' . $e->getMessage());
                }
            }
            
            // Insert new records
            if (!empty($batch)) {
                $this->db->insert_batch('attendance_logs_af4c203360515', $batch);
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check
        sleep(60); // Check every minute
    }
}


public function sync_attendance_logs_cron_af4c214560921($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_improved_af4c214560921();
}

public function fetch_attendance_logs_improved_af4c214560921() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration for this specific device
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'AF4C214560921',
        'max_pages'    => 13000, // Safety limit
        'batch_size'   => 50,  // Records to process at once
        'table_name'   => 'attendance_logs_af4c214560921',
    ];

    // Get last sync time from database
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from($config['table_name'])
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page <= $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            // No more data
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    continue;
                }

                // Check if record exists
                $exists = $this->db->select('id')
                    ->from($config['table_name'])
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->where('terminal_sn', $record['terminal_sn'] ?? null)
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $record['terminal_sn'] ?? null,
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch($config['table_name'], $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record: ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch($config['table_name'], $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            break;
        }
    }

    // Log results
    $message = "Sync completed for device AF4C214560921. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

public function live_attendance_sync_af4c214560921($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour (adjust as needed)
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs_af4c214560921')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=AF4C214560921";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode($last_sync_time);
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            $batch = [];
            
            foreach ($response['data'] as $record) {
                try {
                    // Check if record exists
                    $exists = $this->db->select('id')
                        ->from('attendance_logs_af4c214560921')
                        ->where('emp_code', $record['emp_code'] ?? null)
                        ->where('punch_time', $record['punch_time'] ?? null)
                        ->where('terminal_sn', $record['terminal_sn'] ?? null)
                        ->get()
                        ->row();

                    if (!$exists) {
                        $batch[] = [
                            'emp_code'       => $record['emp_code'] ?? null,
                            'first_name'     => $record['first_name'] ?? null,
                            'last_name'      => $record['last_name'] ?? null,
                            'punch_time'     => $record['punch_time'] ?? null,
                            'punch_state'    => $record['punch_state_display'] ?? null,
                            'area_alias'     => $record['area_alias'] ?? null,
                            'terminal_sn'    => $record['terminal_sn'] ?? null,
                            'terminal_alias' => $record['terminal_alias'] ?? null,
                            'upload_time'    => $record['upload_time'] ?? null
                        ];
                        
                        $inserted++;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing record: ' . $e->getMessage());
                }
            }
            
            // Insert new records
            if (!empty($batch)) {
                $this->db->insert_batch('attendance_logs_af4c214560921', $batch);
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check
        sleep(60); // Check every minute
    }
}


public function sync_attendance_logs_cron_af4c223260066($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_improved_af4c223260066();
}

public function fetch_attendance_logs_improved_af4c223260066() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration for AF4C223260066 device
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'AF4C223260066',
        'max_pages'    => 15000, // Safety limit
        'batch_size'   => 50,  // Records to process at once
        'table_name'   => 'attendance_logs_af4c223260066',
    ];

    // Get last sync time from database
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from($config['table_name'])
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page <= $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page (Device: AF4C223260066)");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            // No more data
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    log_message('error', 'Empty punch_time in record (Device: AF4C223260066)');
                    continue;
                }

                // Check if record exists
                $exists = $this->db->select('id')
                    ->from($config['table_name'])
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->where('terminal_sn', $record['terminal_sn'] ?? null)
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $record['terminal_sn'] ?? null,
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch($config['table_name'], $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record (Device: AF4C223260066): ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch($config['table_name'], $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            log_message('info', 'No new records found, stopping sync (Device: AF4C223260066)');
            break;
        }
    }

    // Log results
    $message = "Sync completed for device AF4C223260066. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

public function live_attendance_sync_af4c223260066($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour (adjust as needed)
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs_af4c223260066')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=AF4C223260066";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode(date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second')));
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            $batch = [];
            
            foreach ($response['data'] as $record) {
                try {
                    // Check if record exists
                    $exists = $this->db->select('id')
                        ->from('attendance_logs_af4c223260066')
                        ->where('emp_code', $record['emp_code'] ?? null)
                        ->where('punch_time', $record['punch_time'] ?? null)
                        ->where('terminal_sn', $record['terminal_sn'] ?? null)
                        ->get()
                        ->row();

                    if (!$exists) {
                        $batch[] = [
                            'emp_code'       => $record['emp_code'] ?? null,
                            'first_name'     => $record['first_name'] ?? null,
                            'last_name'      => $record['last_name'] ?? null,
                            'punch_time'     => $record['punch_time'] ?? null,
                            'punch_state'    => $record['punch_state_display'] ?? null,
                            'area_alias'     => $record['area_alias'] ?? null,
                            'terminal_sn'    => $record['terminal_sn'] ?? null,
                            'terminal_alias' => $record['terminal_alias'] ?? null,
                            'upload_time'    => $record['upload_time'] ?? null
                        ];
                        
                        $inserted++;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing live record (Device: AF4C223260066): ' . $e->getMessage());
                }
            }
            
            // Insert new records
            if (!empty($batch)) {
                $this->db->insert_batch('attendance_logs_af4c223260066', $batch);
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'device' => 'AF4C223260066',
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'device' => 'AF4C223260066',
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check (30 seconds for live sync)
        sleep(30);
    }
}

public function sync_attendance_logs_cron_rkq4235000038($token = '') {
    // Optional security token check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    $this->fetch_attendance_logs_improved_rkq4235000038();
}

public function fetch_attendance_logs_improved_rkq4235000038() {
    set_time_limit(0);
    ini_set('memory_limit', '256M');
    
    // Configuration for RKQ4235000038 device
    $config = [
        'api_base_url' => 'http://sadad01.dyndns.org:8081/iclock/api/transactions/',
        'username'     => 'admin',
        'password'     => 'admin123456',
        'terminal_sn'  => 'RKQ4235000038',
        'max_pages'    => 2300,
        'batch_size'   => 50,
        'table_name'   => 'attendance_logs_rkq4235000038',
    ];

    // Get last sync time from database
    $last_sync = $this->db->select('MAX(punch_time) as last_time')
                          ->from($config['table_name'])
                          ->get()
                          ->row();
    $last_sync_time = $last_sync ? $last_sync->last_time : null;

    // Initialize counters
    $stats = [
        'inserted'      => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'processed'     => 0,
        'error_records' => 0,
        'pages'         => 0,
    ];

    // Main processing loop
    for ($page = 1; $page <= $config['max_pages']; $page++) {
        $stats['pages']++;
        
        // Build API URL with parameters
        $params = [
            'page' => $page,
            'terminal_sn' => $config['terminal_sn'],
        ];
        
        if ($last_sync_time) {
            $params['start_time'] = date('Y-m-d H:i:s', strtotime($last_sync_time . ' +1 second'));
        }
        
        $api_url = $config['api_base_url'] . '?' . http_build_query($params);
        
        // Make API request
        $response = $this->make_api_request($api_url, $config['username'], $config['password']);
        
        if ($response === false) {
            log_message('error', "API request failed for page $page (Device: RKQ4235000038)");
            break;
        }

        // Process response
        if (empty($response['data'])) {
            break;
        }

        // Batch processing
        $batch = [];
        foreach ($response['data'] as $record) {
            try {
                $stats['processed']++;
                
                if (empty($record['punch_time'])) {
                    $stats['error_records']++;
                    continue;
                }

                // Check if record exists
                $exists = $this->db->select('id')
                    ->from($config['table_name'])
                    ->where('emp_code', $record['emp_code'] ?? null)
                    ->where('punch_time', $record['punch_time'] ?? null)
                    ->where('terminal_sn', $record['terminal_sn'] ?? null)
                    ->get()
                    ->row();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Prepare batch insert
                $batch[] = [
                    'emp_code'       => $record['emp_code'] ?? null,
                    'first_name'     => $record['first_name'] ?? null,
                    'last_name'      => $record['last_name'] ?? null,
                    'punch_time'     => $record['punch_time'] ?? null,
                    'punch_state'    => $record['punch_state_display'] ?? null,
                    'area_alias'     => $record['area_alias'] ?? null,
                    'terminal_sn'    => $record['terminal_sn'] ?? null,
                    'terminal_alias' => $record['terminal_alias'] ?? null,
                    'upload_time'    => $record['upload_time'] ?? null
                ];

                // Insert in batches
                if (count($batch) >= $config['batch_size']) {
                    $this->db->insert_batch($config['table_name'], $batch);
                    $stats['inserted'] += count($batch);
                    $batch = [];
                }

            } catch (Exception $e) {
                $stats['error_records']++;
                log_message('error', 'Error processing record (Device: RKQ4235000038): ' . $e->getMessage());
            }
        }

        // Insert any remaining records in batch
        if (!empty($batch)) {
            $this->db->insert_batch($config['table_name'], $batch);
            $stats['inserted'] += count($batch);
        }

        // Stop if we're not getting new records
        if ($page > 1 && $stats['inserted'] == 0) {
            break;
        }
    }

    // Log results
    $message = "Sync completed for device RKQ4235000038. " . 
               "Pages: {$stats['pages']}, " .
               "Processed: {$stats['processed']}, " .
               "Inserted: {$stats['inserted']}, " .
               "Skipped: {$stats['skipped']}, " .
               "Errors: {$stats['error_records']}";
    
    log_message('info', $message);
    echo "<div style='color: green;'>$message</div>";
}

public function live_attendance_sync_rkq4235000038($token = '') {
    // Security check
    if ($token !== 'mySecret123') {
        show_404();
        return;
    }

    // Set headers for SSE
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Disable time limit
    set_time_limit(0);
    
    // Run for 1 hour
    $end_time = time() + 3600;
    
    while (time() < $end_time) {
        // Get last sync time
        $last_sync = $this->db->select('MAX(punch_time) as last_time')
                              ->from('attendance_logs_rkq4235000038')
                              ->get()
                              ->row();
        $last_sync_time = $last_sync ? $last_sync->last_time : null;
        
        // Make API call with last sync time
        $api_url = "http://sadad01.dyndns.org:8081/iclock/api/transactions/?terminal_sn=RKQ4235000038";
        if ($last_sync_time) {
            $api_url .= "&start_time=" . urlencode($last_sync_time);
        }
        
        $response = $this->make_api_request($api_url, 'admin', 'admin123456');
        
        if ($response && !empty($response['data'])) {
            // Process new records
            $inserted = 0;
            $batch = [];
            
            foreach ($response['data'] as $record) {
                try {
                    // Check if record exists
                    $exists = $this->db->select('id')
                        ->from('attendance_logs_rkq4235000038')
                        ->where('emp_code', $record['emp_code'] ?? null)
                        ->where('punch_time', $record['punch_time'] ?? null)
                        ->where('terminal_sn', $record['terminal_sn'] ?? null)
                        ->get()
                        ->row();

                    if (!$exists) {
                        $batch[] = [
                            'emp_code'       => $record['emp_code'] ?? null,
                            'first_name'     => $record['first_name'] ?? null,
                            'last_name'      => $record['last_name'] ?? null,
                            'punch_time'     => $record['punch_time'] ?? null,
                            'punch_state'    => $record['punch_state_display'] ?? null,
                            'area_alias'     => $record['area_alias'] ?? null,
                            'terminal_sn'    => $record['terminal_sn'] ?? null,
                            'terminal_alias' => $record['terminal_alias'] ?? null,
                            'upload_time'    => $record['upload_time'] ?? null
                        ];
                        
                        $inserted++;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error processing record (Device: RKQ4235000038): ' . $e->getMessage());
                }
            }
            
            // Insert new records
            if (!empty($batch)) {
                $this->db->insert_batch('attendance_logs_rkq4235000038', $batch);
            }
            
            // Send update to browser
            echo "event: update\n";
            echo "data: " . json_encode([
                'status' => 'success',
                'inserted' => $inserted,
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        } else {
            echo "event: heartbeat\n";
            echo "data: " . json_encode([
                'status' => 'no_update',
                'time' => date('Y-m-d H:i:s')
            ]) . "\n\n";
        }
        
        // Flush output
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        
        // Wait before next check
        sleep(60);
    }
}

public function vacations() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model->get_salary_vacations();
       
 
        $this->load->view('templateo/vacations', $data);
    }

    public function discounts() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model->get_salary_discounts();
       
 
        $this->load->view('templateo/discounts', $data);
    }

  // In application/controllers/Users1.php

public function discounts101() {
    // Security Check
    if(!$this->session->userdata('logged_in')){
        redirect('users/login');
        return;
    }

    $this->load->model('hr_model'); // Ensure model is loaded

    $data['id'] = $this->uri->segment(3,0);
    
    // Existing data load
    $data['get_salary_discounts'] = $this->hr_model->get_salary_discounts();
    
    // --- NEW ---: Fetch salary sheets for the dropdown
    $data['salary_sheets'] = $this->hr_model->get_salary_salary_sheet();
    // --- END NEW ---

    // Pass CSRF tokens to the view for secure AJAX requests
    $data['csrf_token_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();
       
    $this->load->view('templateo/discounts101', $data);
}
// 2. ADD these FOUR new functions below it

// In Users1.php, REPLACE the old save_discount() function with this one

// In application/controllers/Users1.php
public function save_discount()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $this->load->model('hr_model');
    $this->load->library('form_validation');

    // 1. Validation Rules
   $this->form_validation->set_rules('emp_id', 'الرقم الوظيفي', 'required|alpha_numeric');
    $this->form_validation->set_rules('type', 'سبب الخصم', 'required|trim');
    $this->form_validation->set_rules('amount', 'المبلغ', 'required|numeric');
    $this->form_validation->set_rules('discount_date', 'تاريخ الخصم', 'required');
    
    // Sheet ID is optional (We rely on dates for retrieval)
    $this->form_validation->set_rules('sheet_id', 'مسير الرواتب', 'trim'); 

    if ($this->form_validation->run() == FALSE) {
        $response = ['status' => 'error', 'message' => validation_errors()];
    } else {
        $discount_id = $this->input->post('id');
        
        // 2. Prepare Data
        $data = [
            'emp_id'        => $this->input->post('emp_id'),
            'emp_name'      => $this->input->post('emp_name'),
            'type'          => $this->input->post('type'),
            'amount'        => $this->input->post('amount'),
            'discount_date' => $this->input->post('discount_date'),
            'notes'         => $this->input->post('notes'),
            'is_recurring'  => $this->input->post('is_recurring') ? $this->input->post('is_recurring') : 0,
            // Save sheet_id if provided, otherwise default to 0
            'sheet_id'      => $this->input->post('sheet_id') ? $this->input->post('sheet_id') : 0
        ];

        // 3. Add Audit Info for New Records
        if (empty($discount_id)) {
            $data['username'] = $this->session->userdata('username');
            $data['name']     = $this->session->userdata('name');
            $data['date']     = date('Y-m-d');
            $data['time']     = date('H:i:s');
        }

        // 4. Save (Insert or Update)
        if ($this->hr_model->save_discount($discount_id, $data)) {
            $message = empty($discount_id) ? 'تمت إضافة الخصم بنجاح.' : 'تم تحديث الخصم بنجاح.';
            $response = ['status' => 'success', 'message' => $message];
        } else {
            $response = ['status' => 'error', 'message' => 'فشل حفظ البيانات في قاعدة البيانات.'];
        }
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
// 1. PAGE: The Kanban View
public function salary_sheets_list()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $this->load->model('hr_model');
    
    // Fetch all sheets ordered by start date (Newest first)
    $data['sheets'] = $this->hr_model->get_all_salary_sheets_full(); 
    $data['page_title'] = 'أرشيف مسيرات الرواتب';
    
    $this->load->view('templateo/salary_sheets_kanban', $data);
}
public function salary_sheets_list_ramadan()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $this->load->model('hr_model');
    
    // Fetch all sheets ordered by start date (Newest first)
    $data['sheets'] = $this->hr_model->get_all_salary_sheets_full(); 
    $data['page_title'] = 'أرشيف مسيرات الرواتب';
    
    $this->load->view('ramadan/salary_sheets_kanban_ramadan', $data);
}

public function delete_salary_sheet()
{
    // Set header for JSON response
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
        return;
    }

    // Get POST data
    $id = $this->input->post('id');
    
    // Validate ID
    if (empty($id) || !is_numeric($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    // Load model - CORRECTED: lowercase 'hr_model' not 'Hr_model'
    $this->load->model('hr_model');
    
    // Attempt deletion - CORRECTED: lowercase 'hr_model' not 'Hr_model'
    $result = $this->hr_model->delete_sheet_by_id($id);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'تم الحذف بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في حذف البيانات']);
    }
}
// 2. ACTION: Create New Sheet (Insert)
public function create_new_salary_sheet()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $this->load->model('hr_model');
    $this->load->library('form_validation');
    
    $this->form_validation->set_rules('type', 'اسم المسير', 'required|trim');
    $this->form_validation->set_rules('start_date', 'تاريخ البداية', 'required');
    $this->form_validation->set_rules('end_date', 'تاريخ النهاية', 'required');

    if ($this->form_validation->run() == FALSE) {
        $response = ['status' => 'error', 'message' => validation_errors()];
    } else {
        $data = [
            'type'       => $this->input->post('type'),
            'start_date' => $this->input->post('start_date'),
            'end_date'   => $this->input->post('end_date'),
            'username'   => $this->session->userdata('username'),
            'name'       => $this->session->userdata('name'),
            'date'       => date('Y-m-d'),
            'time'       => date('H:i:s'),
        ];

        // CALL INSERT FUNCTION
        if ($this->hr_model->insert_salary_sheet($data)) {
            $response = ['status' => 'success', 'message' => 'تم إنشاء مسير رواتب جديد بنجاح.'];
        } else {
            $response = ['status' => 'error', 'message' => 'فشل في قاعدة البيانات.'];
        }
    }
    
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
// --- ADD THESE FUNCTIONS TO Users1.php ---

// --- ADD TO Users1.php ---

// --- PASTE INTO application/controllers/Users1.php ---

public function get_emp_discounts_manager()
{
    if (!$this->session->userdata('logged_in')) { echo json_encode(['status'=>'error']); return; }
    
    $emp_id = $this->input->post('emp_id');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    
    // Query: Get discounts for this employee within the sheet's date range
    $this->db->select('*');
    $this->db->from('orders.discounts');
    $this->db->where('emp_id', $emp_id);
    $this->db->group_start();
        $this->db->where('discount_date >=', $start_date);
        $this->db->where('discount_date <=', $end_date);
    $this->db->group_end();
    $this->db->order_by('discount_date', 'ASC');
    
    $query = $this->db->get();
    
    echo json_encode([
        'status' => 'success', 
        'data' => $query->result(), 
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

public function get_emp_reparations_manager()
{
    if (!$this->session->userdata('logged_in')) { echo json_encode(['status'=>'error']); return; }
    
    $emp_id = $this->input->post('emp_id');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    
    // Query: Get reparations for this employee within the sheet's date range
    $this->db->select('*');
    $this->db->from('orders.reparations');
    $this->db->where('emp_id', $emp_id);
    $this->db->group_start();
        $this->db->where('reparation_date >=', $start_date);
        $this->db->where('reparation_date <=', $end_date);
    $this->db->group_end();
    $this->db->order_by('reparation_date', 'ASC');
    
    $query = $this->db->get();
    
    echo json_encode([
        'status' => 'success', 
        'data' => $query->result(), 
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
public function delete_discount11()
{
    if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
    
    $id = $this->input->post('id');
    $this->load->model('hr_model');
    $this->hr_model->delete_discount($id); // Ensure this function exists in model
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'تم الحذف بنجاح',
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

public function delete_reparation11()
{
    if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
    
    $id = $this->input->post('id');
    $this->load->model('hr_model');
    $this->hr_model->delete_reparation($id); // Ensure this function exists in model
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'تم الحذف بنجاح',
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
public function delete_discount()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }
    
    $this->load->model('hr_model');
    $id = $this->input->post('id');

    if ($this->hr_model->delete_discount($id)) {
        $response = ['status' => 'success', 'message' => 'تم حذف السجل بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل حذف السجل.'];
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}

public function get_discount_data()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $this->load->model('hr_model');
    $id = $this->input->post('id');
    $data = $this->hr_model->get_discount_by_id($id);

    if ($data) {
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response = ['status' => 'error', 'message' => 'لم يتم العثور على السجل.'];
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}

public function upload_discounts_sheet()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $config['upload_path']   = './uploads/temp/';
    $config['allowed_types'] = 'xlsx';
    if (!is_dir($config['upload_path'])) { mkdir($config['upload_path'], 0777, true); }
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('discount_file')) {
        $this->session->set_flashdata('error', $this->upload->display_errors());
    } else {
        $file_path = $this->upload->data('full_path');
        
        require_once APPPATH . 'libraries/SimpleXLSX.php';
        
        if ($xlsx = SimpleXLSX::parse($file_path)) {
            $discounts_to_insert = [];
            $header = $xlsx->rows()[0]; 
            
            foreach (array_slice($xlsx->rows(), 1) as $row) {
                if (!empty($row[0])) { 
                    $record = array_combine($header, $row);
                    $record['username'] = $this->session->userdata('username');
                    $record['name']     = $this->session->userdata('name');
                    $record['date']     = date('Y-m-d');
                    $record['time']     = date('H:i:s');
                    $discounts_to_insert[] = $record;
                }
            }

            if (!empty($discounts_to_insert)) {
                $this->load->model('hr_model');
                $this->hr_model->insert_discounts_batch($discounts_to_insert);
                $this->session->set_flashdata('success', 'تم استيراد ' . count($discounts_to_insert) . ' سجل بنجاح.');
            } else {
                $this->session->set_flashdata('error', 'الملف فارغ أو البيانات غير صالحة.');
            }
        } else {
            $this->session->set_flashdata('error', 'فشل في قراءة ملف الإكسل: ' . SimpleXLSX::parseError());
        }
        unlink($file_path);
    }
    redirect('users1/discounts101');
}

     public function reparations() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model->get_salary_reparations();
       
 
        $this->load->view('templateo/reparations', $data);
    }
// In controllers/Users1.php

// Add these functions to Users1.php

// In Users1.php, REPLACE the old save_reparation() function with this one

// In application/controllers/Users1.php

public function save_reparation()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $this->load->model('hr_model');
    $this->load->library('form_validation');

    // 1. Validation Rules
    $this->form_validation->set_rules('emp_id', 'الرقم الوظيفي', 'required|trim|numeric');
    $this->form_validation->set_rules('type', 'سبب التعويض', 'required|trim');
    $this->form_validation->set_rules('amount', 'المبلغ', 'required|numeric');
    $this->form_validation->set_rules('reparation_date', 'تاريخ التعويض', 'required');
    $this->form_validation->set_rules('notes', 'ملاحظات', 'trim');
    
    // Sheet ID is optional
    $this->form_validation->set_rules('sheet_id', 'مسير الرواتب', 'trim');

    if ($this->form_validation->run() == FALSE) {
        $response = ['status' => 'error', 'message' => validation_errors()];
    } else {
        $reparation_id = $this->input->post('id');
        
        // 2. Prepare Data
        $data = [
            'emp_id'          => $this->input->post('emp_id'),
            'emp_name'        => $this->input->post('emp_name'),
            'type'            => $this->input->post('type'),
            'amount'          => $this->input->post('amount'),
            'reparation_date' => $this->input->post('reparation_date'),
            'notes'           => $this->input->post('notes'),
            // Save sheet_id if provided, otherwise default to 0
            'sheet_id'        => $this->input->post('sheet_id') ? $this->input->post('sheet_id') : 0
        ];

        // 3. Add Audit Info for New Records
        if (empty($reparation_id)) {
            $data['username'] = $this->session->userdata('username');
            $data['name']     = $this->session->userdata('name');
            $data['date']     = date('Y-m-d');
            $data['time']     = date('H:i:s');
        }

        // 4. Save (Insert or Update)
        if ($this->hr_model->save_reparation($reparation_id, $data)) {
            $message = empty($reparation_id) ? 'تمت إضافة التعويض بنجاح.' : 'تم تحديث التعويض بنجاح.';
            $response = ['status' => 'success', 'message' => $message];
        } else {
            $response = ['status' => 'error', 'message' => 'فشل حفظ البيانات في قاعدة البيانات.'];
        }
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function delete_reparation()
{
    if (!$this->input->is_ajax_request()) {
       exit('No direct script access allowed');
    }
    
    $this->load->model('hr_model');
    $id = $this->input->post('id');

    if ($this->hr_model->delete_reparation($id)) {
        $response = ['status' => 'success', 'message' => 'تم حذف السجل بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل حذف السجل.'];
    }
    
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

public function get_reparation_data()
{
    if (!$this->input->is_ajax_request()) {
       exit('No direct script access allowed');
    }
    $this->load->model('hr_model');
    $id = $this->input->post('id');
    $data = $this->hr_model->get_reparation_by_id($id);
    if ($data) {
        $response = ['status' => 'success', 'data' => $data];
    } else {
        $response = ['status' => 'error', 'message' => 'لم يتم العثور على السجل.'];
    }
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

public function upload_reparations_sheet()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $config['upload_path']   = './uploads/temp/';
    $config['allowed_types'] = 'xlsx';
    if (!is_dir($config['upload_path'])) { mkdir($config['upload_path'], 0777, true); }
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('reparation_file')) {
        $this->session->set_flashdata('error', $this->upload->display_errors());
    } else {
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];
        
        // Make sure the SimpleXLSX library is available
        require_once APPPATH . 'libraries/SimpleXLSX.php';
        
        if ($xlsx = SimpleXLSX::parse($file_path)) {
            $reparations_to_insert = [];
            $header = $xlsx->rows()[0]; 
            
            foreach (array_slice($xlsx->rows(), 1) as $row) {
                if (!empty($row[0])) { 
                    $record = array_combine($header, $row);
                    $record['username'] = $this->session->userdata('username');
                    $record['name']     = $this->session->userdata('name');
                    $record['date']     = date('Y-m-d');
                    $record['time']     = date('H:i:s');
                    $reparations_to_insert[] = $record;
                }
            }

            if (!empty($reparations_to_insert)) {
                $this->load->model('hr_model');
                $this->hr_model->insert_reparations_batch($reparations_to_insert);
                $this->session->set_flashdata('success', 'تم استيراد ' . count($reparations_to_insert) . ' سجل بنجاح.');
            } else {
                $this->session->set_flashdata('error', 'الملف فارغ أو البيانات غير صالحة.');
            }
        } else {
            $this->session->set_flashdata('error', 'فشل في قراءة ملف الإكسل: ' . SimpleXLSX::parseError());
        }
        unlink($file_path);
    }
    redirect('users1/reparations101');
}
// In application/controllers/Users1.php
// **ADD THIS NEW FUNCTION**
// In application/controllers/Users1.php
// ADD THESE TWO NEW FUNCTIONS

public function clear_all_discounts()
{
    // Security checks
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
    }
    
    $this->load->model('hr_model');
    if ($this->hr_model->truncate_discounts_table()) {
        $response = ['status' => 'success', 'message' => 'تم حذف كافة سجلات الخصومات السابقة بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل حذف السجلات.'];
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}

public function clear_all_reparations()
{
    // Security checks
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
    }
    
    $this->load->model('hr_model');
    if ($this->hr_model->truncate_reparations_table()) {
        $response = ['status' => 'success', 'message' => 'تم حذف كافة سجلات التعويضات السابقة بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل حذف السجلات.'];
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function reparations101() {
    // Security Check
    if(!$this->session->userdata('logged_in')){
        redirect('users/login');
        return;
    }
    
    $this->load->model('hr_model'); // Ensure hr_model is loaded

    $data['id'] = $this->uri->segment(3,0);

    // Load reparations data
    $data['get_salary_reparations'] = $this->hr_model->get_salary_reparations();
       
    // --- NEW ---: Fetch salary sheets for the dropdown
    $data['salary_sheets'] = $this->hr_model->get_salary_salary_sheet();
    // --- END NEW ---
 
    $this->load->view('templateo/reparations101', $data);
}

    public function salary_sheet() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model->get_salary_salary_sheet();
       
 
        $this->load->view('templateo/salary_sheet', $data);
    }

    




    public function emp_data() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model->get_emp1();
       
 
        $this->load->view('templateo/emp_data', $data);
    }

     // In Users1.php

    /**
     * Displays the Work Restrictions page.
     * We modify this to also pass the employee list for the "Add/Edit" modal.
     */
    public function work_restrictions() {
        if(!$this->session->userdata('logged_in')){
            redirect('users/login');
        }

        $data['id'] = $this->uri->segment(3,0);
        
        // This is the data for the main table
        $data['get_salary_vacations'] = $this->hr_model->get_work_restrictions();
        
        // --- NEW ---
        // Get all employees for the modal dropdown
        $data['all_employees'] = $this->hr_model->get_employees_for_restrictions_dropdown(); // <-- MODIFIED LINE
        
        // Pass CSRF tokens for secure form submission
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        // --- END NEW ---
        
        $this->load->view('templateo/work_restrictions', $data);
    }

    /**
     * AJAX: Fetches a single work restriction record for editing.
     */
    public function ajax_get_work_restriction()
    {
        if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
        }

        $id = $this->input->post('id');
        $data = $this->hr_model->get_work_restriction_by_id($id);

        if ($data) {
            $response = ['status' => 'success', 'data' => $data];
        } else {
            $response = ['status' => 'error', 'message' => 'Record not found.'];
        }
        
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * AJAX: Saves (Adds or Updates) a work restriction record.
     */
    public function ajax_save_work_restriction()
    {
        if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('emp_id', 'Employee ID', 'required|trim');
        $this->form_validation->set_rules('emp_name', 'Employee Name', 'required|trim');
        $this->form_validation->set_rules('first_punch', 'First Punch', 'required|trim');
        $this->form_validation->set_rules('last_punch', 'Last Punch', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $response = ['status' => 'error', 'message' => validation_errors()];
        } else {
            $id = $this->input->post('id'); // Hidden field for ID
            
            $data = [
                'emp_id' => $this->input->post('emp_id'),
                'emp_name' => $this->input->post('emp_name'),
                'management' => $this->input->post('management'),
                'company' => $this->input->post('company'),
                'first_punch' => $this->input->post('first_punch'),
                'last_punch' => $this->input->post('last_punch'),
                'maximum_departure_date' => $this->input->post('maximum_departure_date'),
                'sheet_id' => $this->input->post('sheet_id'),
                'working_hours' => $this->input->post('working_hours')
            ];

            if ($this->hr_model->save_work_restriction($id, $data)) {
                $message = empty($id) ? 'Record added successfully.' : 'Record updated successfully.';
                $response = ['status' => 'success', 'message' => $message];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to save data to database.'];
            }
        }
        
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * AJAX: Deletes a work restriction record.
     */
    public function ajax_delete_work_restriction()
    {
        if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Forbidden']));
        }

        $id = $this->input->post('id');
        if ($this->hr_model->delete_work_restriction($id)) {
            $response = ['status' => 'success', 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to delete record.'];
        }
        
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * Exports the work_restrictions table to Excel (CSV).
     */
    public function export_work_restrictions()
    {
        if(!$this->session->userdata('logged_in')){
            redirect('users/login');
        }

        $this->load->dbutil();
        $this->load->helper('download');

        $query = $this->db->get('work_restrictions');
        
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "work_restrictions.csv";

        $data = $this->dbutil->csv_from_result($query, $delimiter, $newline);
        force_download($filename, "\xEF\xBB\xBF" . $data); // Add BOM for Arabic in Excel
    }
    

/**
 * NEW AJAX function for DataTables server-side processing.
 */
// In Users1.php
// In Users1.php

/**
 * Handles the AJAX request to re-activate an employee.
 */
public function activate_employee()
{
    // Check if the request is an AJAX request for security
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        exit('No direct script access allowed');
    }

    $id = $this->input->post('id');
    
    $this->load->model('hr_model');

    // Call the model function to set status back to 'active'
    // We re-use the update_employee_status function that already exists
    if ($this->hr_model->update_employee_status($id, 'active')) {
        // On success, return a success message and the new CSRF token
        $response = [
            'status' => 'success',
            'message' => 'تم استعادة الموظف بنجاح.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ];
        echo json_encode($response);
    } else {
        // On failure, return an error message
        echo json_encode(['status' => 'error', 'message' => 'فشل في استعادة الموظف.']);
    }
}
public function fetch_employees()
{
    $this->load->model('hr_model');
    $list = $this->hr_model->get_datatables_employees();
    
    $data = array();
    foreach ($list as $employee) {
        $row = array();
        $row['id'] = $employee->id; // Primary key for edit links
        $row['employee_id'] = $employee->employee_id;
        $row['id_number'] = $employee->id_number;
        $row['subscriber_name'] = $employee->subscriber_name;
        $row['nationality'] = $employee->nationality;
        $row['gender'] = $employee->gender;
        $row['total_salary'] = $employee->total_salary;
        $row['profession'] = $employee->profession;
        $row['status'] = $employee->status; 
        
        // --- ADD THIS LINE ---
        $row['joining_date'] = $employee->joining_date; 
        // ---------------------

        $row['actions'] = ''; 
        $data[] = $row;
    }

    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->hr_model->count_all_employees(),
        "recordsFiltered" => $this->hr_model->count_filtered_employees(),
        "data" => $data,
        "csrf_hash" => $this->security->get_csrf_hash()
    );
    
    // Output to JSON format
    echo json_encode($output);
}
    public function models_emp() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model->get_emp1();
       
 
        $this->load->view('templateo/models_emp', $data);
    }

    public function residents() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model->get_emp_residents();
       
 
        $this->load->view('templateo/residents', $data);
    }


     // In Users1.php
// In application/controllers/Users1.php

// In application/controllers/Users1.php

// In application/controllers/Users1.php

public function orders_emp() 
{
    $logged_in_user_id = $this->session->userdata('username');
    if (!$logged_in_user_id) {
        redirect('users/login');
        return;
    }

    $this->load->model('hr_model');

    // 1. Get Salary Sheets
    $data['salary_sheets'] = $this->hr_model->get_all_salary_sheets_simple();

    // 2. Permissions
    $hr_cancel_permissions = ['2774', '2230', '2784','1835','2515','2901'];
    $data['can_cancel_requests'] = in_array($logged_in_user_id, $hr_cancel_permissions);

    // 3. ✅ NEW: Get Current User's Department Info for the button label
    $user_info = $this->hr_model->get_employee_details($logged_in_user_id);
    $data['my_department'] = $user_info['department'] ?? ''; 
    $data['current_user_id'] = $logged_in_user_id;

     
     $this->load->view('templateo/orders_emp', $data);
     
 }
// In Users1.php

public function fetch_orders()
{
    $logged_in_user_id = $this->session->userdata('username');
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    $is_hr_user = in_array($logged_in_user_id, $hr_users);
    
    // 1. Get Filter Parameters
    $filter_my_requests = $this->input->post('filter_my_requests') === 'true';
    $filter_pending_ceo = $this->input->post('filter_pending_ceo') === 'true';
    
    // --- NEW ABHA SUPERVISOR LOGIC ---
    $is_abha_supervisor = ($logged_in_user_id == '2694');

    // 2. Get Data from Model
    $list = $this->hr_model->get_datatables_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor, $filter_my_requests, $filter_pending_ceo);
    
    // Bulk fetch names
    $responsible_ids = array_map(function($order){ 
        return isset($order->responsible_employee) ? $order->responsible_employee : null; 
    }, $list);
    
    $responsible_names = !empty($responsible_ids) ? $this->hr_model->get_employee_names_bulk(array_unique($responsible_ids)) : [];
    
    // Leave Map
    $leave_type_map = [
        'annual' => 'سنوية', 'sick' => 'مرضية', 'maternity' => 'أمومة', 'newborn' => 'مولود',
        'hajj' => 'حج', 'marriage' => 'زواج', 'death' => 'وفاة', 'death_brother' => 'وفاة أخ/أخت',
        'unpaid' => 'غير مدفوعة', 'paternity' => 'أبوة', 'exam' => 'اختبارات'
    ];
    
    // Calculate badge count
    $myRequestsCount = 0;
    if (!$filter_my_requests && !$filter_pending_ceo) {
        $myRequestsCount = $this->hr_model->count_my_requests($logged_in_user_id);
    }
    
    $data = [];
    foreach ($list as $order) {
        $row = [];
        $row['id'] = $order->id;
        $row['creator_name'] = $order->creator_name;
        $row['emp_name'] = $order->emp_name;
        $row['emp_id'] = $order->emp_id;
        $row['date'] = $order->date;
        $row['time'] = $order->time;
        $row['event_date'] = isset($order->event_date) ? $order->event_date : $order->date;
        
        // Fix Order Name Display
        $order_name_display = $order->order_name;
        if (($order->type == 5 || $order->order_name == 'إجازة') && !empty($order->vac_main_type)) {
            $ar_type = $leave_type_map[$order->vac_main_type] ?? $order->vac_main_type;
            $order_name_display .= ' - ' . $ar_type;
        }
        $row['order_name'] = $order_name_display;
        
        // ---------------------------------------------------------
        // <--- NEW LOGIC: Payment Status Column (Only for Overtime)
        // ---------------------------------------------------------
        $payment_status_html = '-'; // Default
        
        if ($order->type == 3) { // 3 = Overtime
            // Check ot_payment_status column
            if ($order->ot_payment_status == 'paid' || $order->ot_payment_status == 1) {
                $payment_status_html = '<span class="badge bg-success">تم الدفع</span>';
                if(!empty($order->payment_date)) {
                    $payment_status_html .= '<br><small class="text-muted" style="font-size:10px">'.$order->payment_date.'</small>';
                }
            } elseif ($order->ot_payment_status == 'requested') {
                $payment_status_html = '<span class="badge bg-info text-dark">بانتظار المالية</span>';
            } elseif ($order->status == 2) {
                // If Approved (2) but no payment status yet
                $payment_status_html = '<span class="badge bg-secondary">غير مدفوع</span>';
            }
        }
        
        $row['payment_status'] = $payment_status_html; 
        // ---------------------------------------------------------

        $row['status'] = $order->status;
        $row['responsible_employee_name'] = isset($order->responsible_employee) ? ($responsible_names[$order->responsible_employee] ?? '—') : '—';
        $row['company_name'] = $order->company_name;
        $row['type'] = $order->type;
        $row['file'] = $order->file;
        $data[] = $row;
    }

    $output = [
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->hr_model->count_all_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor, $filter_my_requests, $filter_pending_ceo),
        "recordsFiltered" => $this->hr_model->count_filtered_orders($is_hr_user, $logged_in_user_id, $is_abha_supervisor, $filter_my_requests, $filter_pending_ceo),
        "data" => $data,
        "myRequestsCount" => $myRequestsCount
    ];

    echo json_encode($output);
}
// --- ADD TO Users1.php ---

// --- In Users1.php ---

public function save_salary_suspension() {
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired']); return;
    }

    // 2. Get Input
    $emp_id = $this->input->post('emp_id');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    $reason = $this->input->post('reason');
    
    // Checkbox returns '1' if checked, or NULL if not
    $stop_leaves = $this->input->post('stop_leaves') ? 1 : 0;

    // 3. Validation
    if (!$emp_id || !$start_date || !$reason) {
        echo json_encode(['status' => 'error', 'message' => 'يرجى تعبئة الحقول الإجبارية (الموظف، التاريخ، السبب)']); return;
    }

    // 4. Prepare Data (Matching your Table Columns exactly)
    $data = [
        'emp_id'             => $emp_id,
        'sheet_id'           => 0, // Default to 0 for manual stops
        'start_date'         => $start_date,
        'end_date'           => !empty($end_date) ? $end_date : NULL, // Handle empty date as NULL
        'reason'             => $reason,
        'stop_leave_accrual' => $stop_leaves,
        'date'               => date('Y-m-d'),
        'time'               => date('H:i:s')
    ];

    // 5. Load Model & Insert
    $this->load->model('hr_model');
    
    // Try to insert and catch errors
    if ($this->hr_model->add_salary_suspension($data)) {
        echo json_encode(['status' => 'success', 'message' => 'تم حفظ إيقاف الراتب بنجاح']);
    } else {
        // --- DEBUGGING: Get the exact DB error ---
        $db_error = $this->db->error(); 
        $msg = 'Database Error: ' . $db_error['message'] . ' (Code: ' . $db_error['code'] . ')';
        echo json_encode(['status' => 'error', 'message' => $msg]);
    }
}
// --- ADD TO Users1.php ---

// --- UPDATE in Users1.php ---

public function duplicate_punches_report() {
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : date('Y-m-01');
    $end_date   = $this->input->get('end_date')   ? $this->input->get('end_date')   : date('Y-m-d');
    $threshold  = $this->input->get('threshold')  ? (int)$this->input->get('threshold') : 2;

    $this->load->model('hr_model');
    
    // 1. Get Raw Logs for Duplicates
    $raw_logs = $this->hr_model->get_raw_attendance_logs($start_date, $end_date);
    
    // 2. Get Single Punches (NEW)
    $single_punches = $this->hr_model->get_single_punches($start_date, $end_date);

    // --- Process Duplicates (Existing Logic) ---
    $duplicates = [];
    $dup_stats = ['total' => 0];
    
    if (!empty($raw_logs)) {
        $last_log = null;
        foreach ($raw_logs as $current) {
            if ($last_log && $last_log['emp_code'] == $current['emp_code']) {
                $time1 = strtotime($last_log['punch_time']);
                $time2 = strtotime($current['punch_time']);
                $diff_minutes = abs($time2 - $time1) / 60;

                if ($diff_minutes <= $threshold) {
                    $key_prev = $last_log['id'];
                    if (!isset($duplicates[$key_prev])) {
                        $last_log['is_duplicate'] = false;
                        $duplicates[$key_prev] = $last_log;
                    }
                    $current['is_duplicate'] = true;
                    $current['time_diff'] = round($diff_minutes * 60) . ' ثانية';
                    $duplicates[$current['id']] = $current;
                    $dup_stats['total']++;
                }
            }
            $last_log = $current;
        }
    }

    $data = [
        'logs' => $duplicates,
        'single_punches' => $single_punches, // <--- Pass to View
        'start_date' => $start_date,
        'end_date' => $end_date,
        'threshold' => $threshold,
        'dup_stats' => $dup_stats
    ];

    $this->load->view('templateo/duplicate_punches_view', $data);
}
public function delete_duplicate_logs() {
    if (!$this->session->userdata('logged_in')) return;
    
    $ids = $this->input->post('ids');
    if (empty($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم تحديد أي سجلات']); return;
    }

    $this->load->model('hr_model');
    if ($this->hr_model->delete_attendance_logs($ids)) {
        echo json_encode(['status' => 'success', 'message' => 'تم حذف ' . count($ids) . ' سجل مكرر بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء الحذف']);
    }
}
// In application/controllers/Users1.php
public function print_eos_settlement_view($id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $this->load->model('hr_model');
    
    // 1. Get Data
    $data = $this->hr_model->get_eos_settlement_print_data($id);

    if (empty($data)) {
        show_error('Settlement Record Not Found (ID: ' . $id . ')', 404);
        return;
    }

    // 2. Format Data for the View
    $data['print_date'] = date('l, F d, Y'); 
    $data['final_amount'] = number_format((float)($data['final_amount'] ?? 0), 2);

    // 3. Determine Company based on n13 from emp1 table
    // Assuming 'emp_code' from settlement data matches 'employee_id' in emp1
    $n13 = 1; // Default to Marsoom
    if (isset($data['n13'])) {
        $n13 = $data['n13'];
    } elseif (isset($data['emp_code'])) {
        $emp_query = $this->db->select('n13')->where('employee_id', $data['emp_code'])->get('emp1');
        if ($emp_query->num_rows() > 0) {
            $n13 = $emp_query->row()->n13;
        }
    }

    // 4. Set Logo and View based on Company (n13)
    if ($n13 == 2) {
        // Office of Dr. Saleh Al-Jarboua
        $logo_path = 'C:\laragon\www\hr\assets\imeges\saleh.png';
        $view_file = 'templateo/eos_print_template_saleh';
        $data['company_name'] = 'مكتب الدكتور صالح الجربوع';
    } else {
        // Marsoom (Default)
        $logo_path = 'C:\laragon\www\hr\ico.png';
        $view_file = 'templateo/eos_print_template';
        $data['company_name'] = 'شركة مرسوم';
    }

    // 5. HANDLE LOGO (Convert local path to Base64)
    $logo_data = '';
    if (file_exists($logo_path)) {
        $type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $data_img = file_get_contents($logo_path);
        $logo_data = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
    }
    $data['logo_base64'] = $logo_data;

    // 6. Load the Exact Design View
    $this->load->view($view_file, $data);
}
public function print_eos_settlement_pdf($id)
{
    // 1. Enable Error Reporting (Temporary, to see why it fails)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // 2. Security Check
    if (!$this->session->userdata('logged_in')) {
        die("Error: User not logged in.");
    }

    // 3. Load Model & Data
    $this->load->model('hr_model');
    $data = $this->hr_model->get_eos_settlement_print_data($id);

    // 4. Data Verification
    if (empty($data)) {
        die("<h1>Error: Data Not Found</h1><p>Could not find a settlement with ID or Resignation ID: <strong>$id</strong></p>");
    }

    // 5. Clean Output Buffer (Fixes "White Screen" issues)
    if (ob_get_length()) ob_end_clean();

    // 6. Load TCPDF

    require_once(APPPATH . 'third_party/vendor/src/PhpSpreadsheet/Writer/Pdf/Tcpdf.php');
    

    // 7. Create PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->SetFont('dejavusans', '', 10); // Use this font, it is safe for Arabic
    $pdf->AddPage();
    $pdf->setRTL(true);

    // 8. Helper for numbers
    $fmt = function($val) { 
        return number_format((float)($val ?? 0), 2); 
    };

    // 9. Prepare Data
    $name = $data['subscriber_name'] ?? 'Unknown';
    $emp_id = $data['emp_code'] ?? '---';
    $id_num = $data['id_number'] ?? '---';
    $job = $data['profession'] ?? '---';
    $join = $data['joining_date'] ?? '---';
    $last = $data['date_of_the_last_working'] ?? '---';

    // Calculate Duration
    $duration = '---';
    if($join != '---' && $last != '---') {
        try {
            $d1 = new DateTime($join);
            $d2 = new DateTime($last);
            $diff = $d1->diff($d2);
            $duration = $diff->y." سنة, ".$diff->m." شهر, ".$diff->d." يوم";
        } catch(Exception $e) {}
    }

    // 10. HTML Content
    $html = '
    <table border="0" cellpadding="5">
        <tr><td align="center"><h2>مخالصة نهائية وتسوية مستحقات</h2><h3>End of Service Settlement</h3></td></tr>
    </table>
    <br><br>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <tr style="background-color:#f0f0f0;">
            <th colspan="4" align="center"><b>بيانات الموظف / Employee Details</b></th>
        </tr>
        <tr>
            <td width="20%"><b>الاسم</b></td><td width="30%">'.$name.'</td>
            <td width="20%"><b>الرقم الوظيفي</b></td><td width="30%">'.$emp_id.'</td>
        </tr>
        <tr>
            <td><b>رقم الهوية</b></td><td>'.$id_num.'</td>
            <td><b>المسمى الوظيفي</b></td><td>'.$job.'</td>
        </tr>
        <tr>
            <td><b>تاريخ التعيين</b></td><td>'.$join.'</td>
            <td><b>آخر يوم عمل</b></td><td>'.$last.'</td>
        </tr>
        <tr>
            <td><b>مدة الخدمة</b></td><td colspan="3">'.$duration.'</td>
        </tr>
    </table>
    <br><br>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr style="background-color:#001f3f; color:white;">
            <th width="60%" align="center"><b>البيان (Description)</b></th>
            <th width="20%" align="center"><b>استحقاق (+)</b></th>
            <th width="20%" align="center"><b>خصم (-)</b></th>
        </tr>
        <tr>
            <td>مكافأة نهاية الخدمة (Gratuity)</td>
            <td align="center">'.$fmt($data['gratuity_amount']).'</td>
            <td></td>
        </tr>
        <tr>
            <td>تعويض رصيد الإجازات (Vacation Comp)</td>
            <td align="center">'.$fmt($data['compensation']).'</td>
            <td></td>
        </tr>
        <tr>
            <td>راتب الشهر الحالي (Prorated Salary)</td>
            <td align="center">'.$fmt($data['prorated_salary_amount']).'</td>
            <td></td>
        </tr>
        <tr>
            <td>إضافات أخرى (Other Additions)</td>
            <td align="center">'.$fmt($data['insurance_compensation']).'</td>
            <td></td>
        </tr>
        <tr>
            <td>خصم التأمينات (GOSI Deduction)</td>
            <td></td>
            <td align="center">'.$fmt($data['insurance_deduction']).'</td>
        </tr>
        <tr>
            <td>تجاوز رصيد الإجازات (Negative Leave)</td>
            <td></td>
            <td align="center">'.$fmt($data['leave_balance_deduction']).'</td>
        </tr>
        <tr>
            <td>خصم الغياب (Absence)</td>
            <td></td>
            <td align="center">'.$fmt($data['absence_deduction']).'</td>
        </tr>';

    // Group other penalties
    $other_deduct = ($data['lateness_deduction'] ?? 0) + 
                    ($data['penalty_clause_deduction'] ?? 0) + 
                    ($data['absence_penalty_deduction'] ?? 0);
    
    if ($other_deduct > 0) {
        $html .= '<tr><td>خصم تأخير / جزاءات (Penalties)</td><td></td><td align="center">'.$fmt($other_deduct).'</td></tr>';
    }

    $html .= '
        <tr style="background-color:#e9ecef;">
            <td><b>صافي المستحق (Net Payable)</b></td>
            <td colspan="2" align="center"><b style="font-size:12pt">'.$fmt($data['final_amount']).' SAR</b></td>
        </tr>
    </table>
    <br><br>
    
    <div style="font-size:10px;">
    أقر أنا الموقع أدناه بأنني قد استلمت كافة مستحقاتي النظامية من رواتب وبدلات ومكافأة نهاية خدمة وأي حقوق أخرى، وبهذا أبرئ ذمة الشركة إبراءً تاماً وشاملاً لا رجوع فيه.
    <br>I acknowledge that I have received all my statutory dues and fully release the company from any further liabilities regarding my employment.
    </div>
    <br><br><br>
    
    <table border="0" cellpadding="5">
        <tr>
            <td align="center"><b>الموظف / Employee</b><br><br>......................</td>
            <td align="center"><b>الموارد البشرية / HR</b><br><br>......................</td>
            <td align="center"><b>المدير المالي / Finance</b><br><br>......................</td>
        </tr>
    </table>';

    // 11. Output PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('EOS_Settlement_'.$id.'.pdf', 'D');
}
// In application/controllers/Users1.php

// Handles adding a new discount record via AJAX
public function ajax_add_insurance_discount()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    // Security Check: Only allow HR users
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->library('form_validation');
    $this->form_validation->set_rules('n1', 'Employee ID', 'required|trim|numeric');
    $this->form_validation->set_rules('n3', 'Discount Percentage', 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');

    if ($this->form_validation->run() == FALSE) {
        $response = ['status' => 'error', 'message' => validation_errors()];
    } else {
        $this->load->model('hr_model');
        $employee_id = $this->input->post('n1');

        // Check if a discount already exists for this employee
        if ($this->hr_model->check_discount_exists_for_employee($employee_id)) {
            $response = ['status' => 'error', 'message' => 'A discount record already exists for this employee. Please edit the existing record.'];
        } else {
            // Fetch employee name based on ID
            $employee_info = $this->hr_model->get_employee_info1($employee_id);
            $employee_name = $employee_info ? $employee_info['name'] : 'Unknown Employee';

            $data = [
                'n1' => $employee_id,
                'n2' => $employee_name,
                'n3' => $this->input->post('n3')
            ];

            if ($this->hr_model->add_insurance_discount1($data)) {
                $response = ['status' => 'success', 'message' => 'Discount added successfully.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to add the discount.'];
            }
        }
    }

    // Add CSRF token to response
    $response['csrf_name'] = $this->security->get_csrf_token_name();
    $response['csrf_hash'] = $this->security->get_csrf_hash();

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

// Handles deleting a discount record via AJAX
public function ajax_delete_insurance_discount()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    // Security Check: Only allow HR users
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $id = $this->input->post('id');

    if (empty($id)) {
        $response = ['status' => 'error', 'message' => 'Invalid ID provided.'];
    } else {
        $this->load->model('hr_model');
        if ($this->hr_model->delete_insurance_discount1($id)) {
            $response = ['status' => 'success', 'message' => 'Discount deleted successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to delete the discount.'];
        }
    }

    // Add CSRF token to response
    $response['csrf_name'] = $this->security->get_csrf_token_name();
    $response['csrf_hash'] = $this->security->get_csrf_hash();

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}


// In application/controllers/Users1.php

// In application/controllers/Users1.php

// In application/controllers/Users1.php

public function hr_override_approve()
{
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['ok' => false, 'error' => 'Forbidden']));
    }
    
    // Security: Double-check that the user is an HR user
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    $logged_in_user_id = $this->session->userdata('username');
    if (!in_array($logged_in_user_id, $hr_users)) {
        return $this->output->set_status_header(403)->set_output(json_encode(['ok' => false, 'error' => 'Unauthorized action.']));
    }
    
    $this->load->model('hr_model');
    $order_id = (int)$this->input->post('id');
    
    // This calls the new function in the model
    $result = $this->hr_model->process_hr_override_approval($order_id, $logged_in_user_id);
   // =========================================================
    // START: Auto-Log Attendance for Work Mission (9) & Permission (12)
    // =========================================================
    if ($result['success']) {
        // Check the order details to see if it is Status 2 (Approved)
        $order_check = $this->db->select('type, status')
            ->get_where('orders_emp', ['id' => $order_id])
            ->row();

        if ($order_check && $order_check->status == 2) {
            if ($order_check->type == 9) {
                $this->hr_model->insert_mission_attendance_logs($order_id);
            } elseif ($order_check->type == 12) {
                // NEW: Automatically insert attendance logs for Permission requests!
                $this->hr_model->auto_log_permission_attendance($order_id);
            }
        }
    }
    // =========================================================
    // END: Auto-Log Attendance
    // =========================================================
    $response = [
        'ok' => $result['success'],
        'error' => $result['success'] ? '' : $result['message'],
        'new_status' => $result['new_status'] ?? '',
        'csrfHash' => $this->security->get_csrf_hash()
    ];
    
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

// In Users1.php
public function cancel_request($order_id)
{
    // 1. Security Check (remains the same)
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }
    $logged_in_user_id = $this->session->userdata('username');
    $hr_cancel_permissions = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($logged_in_user_id, $hr_cancel_permissions)) {
        $this->session->set_flashdata('error', 'You do not have permission to perform this action.');
        redirect('users1/orders_emp');
        return;
    }

    $this->db->trans_start();
    
    // First, get the request details BEFORE changing its status
    $request_to_cancel = $this->hr_model->get_request_by_id($order_id);

    // Cancel the order by updating its status
    $cancellation_success = $this->hr_model->cancel_order_by_hr($order_id, $logged_in_user_id);

    // If the request was a previously APPROVED leave, refund the balance
    if ($cancellation_success && $request_to_cancel && $request_to_cancel['type'] == 5 && $request_to_cancel['status'] == '2') {
        
        // ✅ FIX: Pass the entire $request_to_cancel array, not just the ID.
        $this->hr_model->refund_leave_balance_on_cancellation($request_to_cancel);
    }
    
    $this->db->trans_complete();

    if ($this->db->trans_status()) {
        $this->session->set_flashdata('success', 'تم إلغاء الطلب بنجاح (وأي رصيد إجازة مستحق قد تم إرجاعه).');
    } else {
        $this->session->set_flashdata('error', 'فشل إلغاء الطلب.');
    }

    redirect('users1/orders_emp');
}
// In Users1.php

public function ajax_check_pending_requests()
{
    // Security check
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403);
    }

    $sheet_id = $this->input->post('sheet_id');
    
    if(!$sheet_id) {
        echo json_encode(['status' => 'error', 'message' => 'Sheet ID missing']);
        return;
    }

    $this->load->model('hr_model');
    
    // 1. Get Sheet Dates
    $sheet = $this->hr_model->get_salary_sheet($sheet_id);
    
    if (!$sheet) {
        echo json_encode(['status' => 'error', 'message' => 'Sheet not found']);
        return;
    }

    // 2. Get Pending Requests
    $requests = $this->hr_model->get_pending_requests_by_date_range($sheet['start_date'], $sheet['end_date']);

    echo json_encode([
        'status' => 'success', 
        'data' => $requests,
        'count' => count($requests),
        'period' => $sheet['start_date'] . ' إلى ' . $sheet['end_date']
    ]);
}
public function get_clearance_data($requestId) {
    // Ensure this is an AJAX request
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    // Make sure the model is loaded
    $this->load->model('hr_model'); 

    // Fetch both sets of data
    $all_departments = $this->hr_model->get_all_departments();
    $selected_departments = $this->hr_model->get_selected_clearance_departments($requestId);

    $response = [
        'status' => 'success',
        'all_departments' => $all_departments,
        'selected_departments' => $selected_departments
    ];

    // Set the correct headers and send the JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}
    public function save_clearance_data() {
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $this->load->model('hr_model');

    $requestId = $this->input->post('request_id');
    $departmentIds = $this->input->post('department_ids') ?? [];

    if (empty($requestId)) {
        $response = ['status' => 'error', 'message' => 'Request ID is missing.'];
    } else {
        // Call the new model function that handles the logic
        $success = $this->hr_model->create_clearance_tasks_from_departments($requestId, $departmentIds);
        
        if ($success) {
            $response = ['status' => 'success', 'message' => 'تم إنشاء مهام المخالصة بنجاح.'];
        } else {
            $response = ['status' => 'error', 'message' => 'حدث خطأ أثناء إنشاء المهام.'];
        }
    }
    
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}


public function my_clearance_tasks() {
    $this->load->model('hr_model');
    
    // CORRECTED: Get the logged-in user's ID from the 'username' session key
    $approverId = $this->session->userdata('username');

    $data['tasks'] = $this->hr_model->get_pending_clearances_for_user($approverId);
    $this->load->view('templateo/clearance_tasks_view', $data);
}

public function submit_clearance_decision() {
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $this->load->model('hr_model');

    $taskId = $this->input->post('task_id');
    $action = $this->input->post('action');
    $reason = $this->input->post('rejection_reason');
    
    // CORRECTED: Get the logged-in user's ID from the 'username' session key
    $approverId = $this->session->userdata('username');

    $attachmentPath = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $config['upload_path']   = './uploads/clearances/';
        $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx';
        $config['encrypt_name']  = TRUE;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('attachment')) {
            $attachmentPath = $config['upload_path'] . $this->upload->data('file_name');
        } else {
            $response = ['status' => 'error', 'message' => $this->upload->display_errors('', '')];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }
    }

    $success = $this->hr_model->update_clearance_task($taskId, $action, $approverId, $reason, $attachmentPath);

    if ($success) {
        $this->hr_model->trigger_clearance_email($taskId);
        $this->hr_model->check_and_update_overall_status($taskId);
        $response = ['status' => 'success', 'message' => 'تم تسجيل الإجراء بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل في تسجيل الإجراء.'];
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}





public function orders_emp_res() {



      


    $data['get_salary_vacations'] = $this->hr_model->get_orders_emp_res();


       
 
        $this->load->view('templateo/orders_emp_res', $data);
    }



 function profile(){ 

     if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{ 
            if($this->session->userdata('type') == 10){
              redirect('users/login');
            }else{

                 $this->load->view('templateo/profile_view');

                  

            }
        }



      
    
    }






 function letter_mng(){ 

     if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{ 
            if($this->session->userdata('type') == 10){
              redirect('users/login');
            }else{

                 $this->load->view('templateo/letters_management');

                  

            }
        }



      
    
    }
    public function save_letter_ajax()
{
    if (!$this->input->is_ajax_request()) { 
        show_404(); 
    }

    $this->load->model('hr_model');
    
    $order_id = $this->input->post('order_id');
    $letter_html_content = $this->input->post('letter_html_content');
    
    // Debug logging
    log_message('debug', 'Saving letter for order_id: ' . $order_id);
    log_message('debug', 'HTML content length: ' . strlen($letter_html_content));
    log_message('debug', 'HTML content preview: ' . substr($letter_html_content, 0, 200) . '...');
    
    $data = [
        'order_id'            => $order_id,
        'employee_id'         => $this->input->post('employee_id'),
        'letter_slug'         => $this->input->post('letter_slug'),
        'letter_html_content' => $letter_html_content,
        'created_by_id'       => $this->session->userdata('username'),
        'created_at'          => date('Y-m-d H:i:s')
    ];
    
    if ($this->hr_model->save_generated_letter($data)) {
        $response = ['status' => 'success', 'message' => 'تم حفظ الخطاب بنجاح.'];
        log_message('debug', 'Letter saved successfully for order_id: ' . $order_id);
    } else {
        $response = ['status' => 'error', 'message' => 'فشل حفظ الخطاب.'];
        log_message('error', 'Failed to save letter for order_id: ' . $order_id);
    }
    
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function test_save_endpoint()
{
    // Temporary function to test if save works
    if (!$this->input->is_ajax_request()) {
        // Allow direct access for testing
        $test_data = [
            'status' => 'test',
            'message' => 'Endpoint is accessible',
            'post_data' => $this->input->post()
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($test_data));
        return;
    }

    $this->load->model('hr_model');
    
    $data = [
        'order_id'            => $this->input->post('order_id') ?? 'test_' . time(),
        'employee_id'         => $this->input->post('employee_id') ?? 'test_emp',
        'letter_slug'         => $this->input->post('letter_slug') ?? 'test-slug',
        'letter_html_content' => $this->input->post('letter_html_content') ?? '<div>Test content</div>',
        'created_by_id'       => $this->session->userdata('username') ?? 'test_user',
        'created_at'          => date('Y-m-d H:i:s')
    ];
    
    log_message('debug', 'Test save data: ' . print_r($data, true));
    
    if ($this->hr_model->save_generated_letter($data)) {
        $response = ['status' => 'success', 'message' => 'Test save successful'];
    } else {
        $response = ['status' => 'error', 'message' => 'Test save failed'];
    }
    
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function download_letter()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    
    $letter_slug = $this->input->post('letter_slug');
    $employee_id = $this->input->post('employee_id');
    $order_id = $this->input->post('order_id');

    // Get employee data
    $employee = (object) $this->hr_model->get_employee_details($employee_id);
    
    if (!$employee) {
        show_error('Employee not found.', 404);
        return;
    }

    // Generate the letter content
    $data['employee'] = $employee;
    $data['letter_slug'] = $letter_slug;
    $data['order_id'] = $order_id;
    
    // Get the letter HTML content
    $letter_content = $this->load->view('templateo/salary_certificate_template', $data, TRUE);
    
    // Generate PDF using dompdf (you'll need to install dompdf)
    $this->load->library('pdf');
    
    $filename = "letter_" . $employee->subscriber_name . "_" . date('Y-m-d') . ".pdf";
    
    // Save to generated_letters table
    $save_data = [
        'order_id' => $order_id,
        'employee_id' => $employee_id,
        'letter_slug' => $letter_slug,
        'letter_html_content' => $letter_content,
        'created_by_id' => $this->session->userdata('username'),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $this->hr_model->save_generated_letter($save_data);
    
    // For now, we'll create a simple HTML file download
    // You can implement PDF generation later
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($letter_content));
    
    echo $letter_content;
    exit;
}

public function letter_management()
{
    // Security check: only user 1127 can access this page
    if ($this->session->userdata('username') != '1127') {
        show_error('You are not authorized to view this page.', 403);
        return;
    }
    $this->load->view('templateo/letters_management');
}

public function get_requesting_employees()
{
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $this->load->model('hr_model');
    $approver_id = $this->session->userdata('username');
    
    // Call a new model function to get employees with pending letter requests
    $employees = $this->hr_model->get_employees_with_pending_letter_requests($approver_id);

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'employees' => $employees]));
}

// Add this new function to Users1.php to generate and display the letter
// In Users1.php controller
// Simple function to show letter and auto-save
public function generate_letter($letter_slug = '', $employee_id = '', $order_id = '')
{
    if ($this->session->userdata('username') != '1127') {
        show_error('Unauthorized', 403);
        return;
    }
    
    $this->load->model('hr_model');
    
    // Get employee data
    $data['employee'] = (object) $this->hr_model->get_employee_details($employee_id);
    $data['letter_slug'] = $letter_slug;
    $data['order_id'] = $order_id;
    $data['employee_id'] = $employee_id;

    if (!$data['employee']) {
        show_error('Employee not found', 404);
        return;
    }

    // Auto-save the letter when generated
    $this->auto_save_letter($order_id, $employee_id, $letter_slug, $data);

    // Load the appropriate template
    $template_map = [
        'salary-certificate'        => 'templateo/salary_certificate_template',
        'salary-commitment'         => 'templateo/commitment_letter_template',
        'salary-commitment-marsoom' => 'templateo/commitment_letter_template_marsoom',
        'embassy-letter'            => 'templateo/embassy_letter_template',
        'eos-certificate'           => 'templateo/eos_certificate_template',
        'marsoom-definition'          => 'templateo/marsoom_definition_template',
    'office-definition'           => 'templateo/office_definition_template'
    ];

    $template_view = $template_map[$letter_slug] ?? null;

    if ($template_view) {
        $this->load->view($template_view, $data);
    } else {
        show_404();
    }
}

// Auto-save function
private function auto_save_letter($order_id, $employee_id, $letter_slug, $data)
{
    $this->load->model('hr_model');
    
    // Check if already saved
    $existing_letter = $this->hr_model->get_generated_letter_by_order_id($order_id);
    
    if (!$existing_letter) {
        // Generate letter content for saving
        $template_map = [
            'salary-certificate'        => 'templateo/salary_certificate_template',
            'salary-commitment'         => 'templateo/commitment_letter_template',
            'salary-commitment-marsoom' => 'templateo/commitment_letter_template_marsoom',
            'embassy-letter'            => 'templateo/embassy_letter_template',
            'eos-certificate'           => 'templateo/eos_certificate_template'
        ];
        
        $template_view = $template_map[$letter_slug] ?? 'templateo/salary_certificate_template';
        $letter_content = $this->load->view($template_view, $data, TRUE);
        
        // Save to database
        $save_data = [
            'order_id' => $order_id,
            'employee_id' => $employee_id,
            'letter_slug' => $letter_slug,
            'letter_html_content' => $letter_content,
            'created_by_id' => $this->session->userdata('username'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->hr_model->save_generated_letter($save_data);
    }
}

// Function to view saved letter
public function view_saved_letter($order_id)
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    
    $this->load->model('hr_model');
    $letter = $this->hr_model->get_generated_letter_by_order_id($order_id);
    
    if ($letter) {
        // Simply output the saved HTML
        echo $letter['letter_html_content'];
    } else {
        show_404();
    }
}
public function create_letter($letter_slug, $employee_id, $order_id)
{
    if ($this->session->userdata('username') != '1127') {
        show_error('Unauthorized', 403);
        return;
    }

    $this->load->model('hr_model');
    
    $data['employee'] = (object) $this->hr_model->get_employee_details($employee_id);
    $data['letter_slug'] = $letter_slug;
    $data['order_id'] = $order_id;
    $data['employee_id'] = $employee_id;

    if (!$data['employee']) {
        show_error('Employee not found', 404);
        return;
    }

    // Check if letter already exists
    $data['existing_letter'] = $this->hr_model->get_letter_data($order_id, $letter_slug);

    $this->load->view('templateo/letter_form', $data);
}

// Function to save letter data
public function save_letter_data()
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }

    $this->load->model('hr_model');

    $data = [
        'order_id' => $this->input->post('order_id'),
        'employee_id' => $this->input->post('employee_id'),
        'letter_type' => $this->input->post('letter_slug'),
        'recipient_name' => $this->input->post('recipient_name'),
        'salary_amount' => $this->input->post('salary_amount'),
        'additional_notes' => $this->input->post('additional_notes'),
        'created_by_id' => $this->session->userdata('username')
    ];

    if ($this->hr_model->save_letter_data($data)) {
        $response = ['status' => 'success', 'message' => 'تم حفظ بيانات الخطاب بنجاح'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل في حفظ البيانات'];
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}

// Function to generate and print letter
public function generate_letter_print($order_id, $letter_slug)
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    
    // Get letter data
    $letter_data = $this->hr_model->get_letter_data($order_id, $letter_slug);
    
    if (!$letter_data) {
        show_error('Letter data not found', 404);
        return;
    }

    // Get employee data
    $employee = (object) $this->hr_model->get_employee_details($letter_data['employee_id']);
    
    $data = [
        'employee' => $employee,
        'letter_data' => $letter_data,
        'letter_slug' => $letter_slug
    ];

    $this->load->view('templateo/letter_print', $data);
}
public function get_order_log()
{
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Not logged in']));
    }

    $order_id = $this->input->post('order_id');
    $order_type = $this->input->post('order_type');

    if (empty($order_id) || empty($order_type)) {
        return $this->output->set_status_header(400)->set_output(json_encode([
            'status' => 'error', 
            'message' => 'Missing parameters',
            'order_id' => $order_id,
            'order_type' => $order_type
        ]));
    }

    $this->load->model('hr_model');
    $log_data = $this->hr_model->get_approval_log((int)$order_id, (int)$order_type);

    // Debug: Check what data is returned
    error_log("Order Log Query - ID: $order_id, Type: $order_type, Results: " . count($log_data));

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => 'success', 
            'log' => $log_data,
            'debug' => ['order_id' => $order_id, 'order_type' => $order_type] // Remove this in production
        ]));
}

// PASTE THIS NEW FUNCTION INSIDE YOUR Users1 CONTROLLER (application/controllers/Users1.php)

// In Users1.php, REPLACE the entire attendance_overview() function with this new version.

public function attendance_overview()
{
    // 1. Security Check
    $logged_in_username = $this->session->userdata('username');
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($logged_in_username, $hr_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $this->load->model('hr_model');

    // 2. Get Filters from URL
    $filters = [
        'start_date'  => $this->input->get('start_date', TRUE) ?: date('Y-m-01'),
        'end_date'    => $this->input->get('end_date', TRUE) ?: date('Y-m-t'),
        'employee_id' => $this->input->get('employee_id', TRUE),
        'department'  => $this->input->get('department', TRUE), // n1
        'profession'  => $this->input->get('profession', TRUE), // profession
        'company'     => $this->input->get('company', TRUE),    // company_name
        'location'    => $this->input->get('location', TRUE),
        'device'      => $this->input->get('device', TRUE),
        'job_type'    => $this->input->get('job_type', TRUE)    
    ];

    // 3. Fetch Dropdown Data for Filters
    // Note: Ensure get_distinct_employees_field exists in your model
    $data['departments'] = $this->hr_model->get_distinct_employees_field('n1'); 
    $data['professions'] = $this->hr_model->get_distinct_employees_field('profession');
    $data['companies']   = $this->hr_model->get_distinct_employees_field('company_name');
    $data['locations']   = $this->hr_model->get_distinct_employees_field('location');
    $data['devices']     = $this->hr_model->get_distinct_devices();
    $data['job_types']   = $this->hr_model->get_distinct_employees_field('job_type');

    // 4. Fetch Raw Data from Model
    $overview_data = $this->hr_model->get_attendance_overview_data($filters);

    // 5. Pivot Data (Transform Raw Records into Employee => Date Structure)
    $pivoted_data = [];
    foreach ($overview_data['records'] as $record) {
        $emp_id = $record['employee_id'];
        if (!isset($pivoted_data[$emp_id])) {
            $pivoted_data[$emp_id] = [
                'employee_name' => $record['employee_name'],
                'department'    => $record['department'],
                'profession'    => $record['profession'] ?? '',
                'company'       => $record['company'] ?? '',
                'location'      => $record['location'] ?? '',
                'dates'         => []
            ];
        }
        $pivoted_data[$emp_id]['dates'][$record['date']] = $record;
    }

    // 6. Calculate Totals per Employee
    foreach ($pivoted_data as $emp_id => &$emp_details) {
        $absence_count = 0; 
        $vacation_count = 0; 
        $violation_count = 0; 
        $correction_count = 0;

        if (isset($emp_details['dates'])) {
            foreach ($emp_details['dates'] as $date_data) {
                $status = $date_data['day_status'] ?? '';
                $violation = $date_data['violation'] ?? null;

                if ($status === 'غياب') {
                    $absence_count++;
                }
                if (strpos($status, 'إجازة') !== false || $status === 'عطلة رسمية') {
                     // Count half-day leaves as 0.5 if needed, currently counting as 1
                     $vacation_count += ($status === 'إجازة نصف يوم' ? 0.5 : 1);
                }
                if ($status === 'تصحيح بصمة') {
                    $correction_count++;
                }
                if (!empty($violation)) {
                    $violation_count++;
                }
            }
        }
        $emp_details['total_absences'] = $absence_count;
        $emp_details['total_vacations'] = $vacation_count;
        $emp_details['total_violations'] = $violation_count;
        $emp_details['total_corrections'] = $correction_count;
    }
    unset($emp_details); // Break reference

    // 7. Generate Date Headers
    $date_headers = [];
    $period = new DatePeriod(
        new DateTime($filters['start_date']),
        new DateInterval('P1D'),
        (new DateTime($filters['end_date']))->modify('+1 day')
    );
    foreach ($period as $date) {
        $date_headers[] = $date->format('Y-m-d');
    }

    // 8. Prepare View Data
    $data['kpis'] = $overview_data['kpis'];
    $data['pivoted_data'] = $pivoted_data;
    $data['date_headers'] = $date_headers;
    $data['filters'] = $filters;
    $data['page_title'] = 'تقرير الحضور الشامل';

    $this->load->view('templateo/hr_attendance_overview', $data);
}
// In application/controllers/Users1.php

public function check_missing_data()
{
    // Security Check
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->model('hr_model');
    $missing_data_employees = $this->hr_model->get_employees_with_missing_data();

    // Process the result to identify specifically WHICH fields are missing for each employee
    $processed_list = [];
    $critical_columns = [
        'employee_id' => 'الرقم الوظيفي', 
        'id_number' => 'رقم الهوية', 
        'email' => 'البريد الإلكتروني',
        'id_expiry' => 'تاريخ انتهاء الهوية', 
        'subscriber_name' => 'الاسم', 
        'nationality' => 'الجنسية',
        'gender' => 'الجنس', 
        'birth_date' => 'تاريخ الميلاد', 
        'base_salary' => 'الراتب الأساسي',
        'housing_allowance' => 'بدل السكن', 
        'total_salary' => 'إجمالي الراتب', 
        'profession' => 'المسمى الوظيفي',
        'joining_date' => 'تاريخ المباشرة', 
        'company_name' => 'الشركة', 
        'n1' => 'الإدارة', 
        'n2' => 'الآيبان', 
        'n3' => 'البنك', 
        'n4' => 'بدلات أخرى',
        'location' => 'الموقع', 
        'manager' => 'المدير المباشر', 
        'contract_period' => 'مدة العقد', 
        'contract_start' => 'بداية العقد', 
        'contract_end' => 'نهاية العقد'
    ];

    foreach ($missing_data_employees as $emp) {
        $missing_fields = [];
        foreach ($critical_columns as $col_key => $col_name) {
            if (empty($emp[$col_key])) {
                $missing_fields[] = $col_name;
            }
        }
        
        if (!empty($missing_fields)) {
            $processed_list[] = [
                'emp_id' => $emp['employee_id'],
                'name' => $emp['subscriber_name'],
                'missing' => $missing_fields
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $processed_list,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
// --- ADD TO application/controllers/Users1.php ---
// --- OPEN: application/controllers/Users1.php ---

// --- In application/controllers/Users1.php ---

public function fetch_overtime_data() {
    if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
    $this->load->model('hr_model');

    $list = $this->hr_model->get_overtime_datatables();
    $data = array();
    
    foreach ($list as $item) {
        $row = array();
        $row['id'] = $item->id;
        $row['emp_id'] = $item->emp_id;
        $row['emp_name'] = !empty($item->subscriber_name) ? $item->subscriber_name : '-';
        $row['company_name'] = !empty($item->company_name) ? $item->company_name : '-';
        $row['approvers'] = $this->hr_model->get_approvers_train($item->id, 3);
        $row['ot_date'] = $item->ot_date;
        $row['ot_hours'] = $item->ot_hours;
        $row['ot_amount'] = number_format($item->ot_amount, 2);

        // 1. Approval Status
        $badge = 'warning'; $text = 'Pending';
        if ($item->status == 2) { $badge = 'success'; $text = 'Approved'; }
        if ($item->status == 3) { $badge = 'danger'; $text = 'Rejected'; }
        $row['status'] = '<span class="badge bg-'.$badge.'">'.$text.'</span>';

        // 2. NEW: Payment Status (ot_payment_status)
        $pay_badge = 'secondary'; $pay_text = 'Unpaid';
        if ($item->ot_payment_status == 'paid' || $item->ot_payment_status == 1) { 
            $pay_badge = 'success'; $pay_text = 'Paid'; 
        } elseif ($item->ot_payment_status == 'requested') { 
            $pay_badge = 'info'; $pay_text = 'Requested'; 
        }
        $row['pay_status'] = '<span class="badge bg-'.$pay_badge.'">'.$pay_text.'</span>';

        // Actions
        $btn = '<div class="btn-group">';
        if(($this->session->userdata('username') == '1693' 
    || $this->session->userdata('username') == '2909' 
    || $this->session->userdata('username') == '1936' 
    || $this->session->userdata('username') == '2833') 
   && $item->ot_payment_status == 'requested'){
             $btn .= '<button class="btn btn-sm btn-success btn-action" onclick="openPaymentModal('.$item->id.', \'overtime\')" title="صرف"><i class="fas fa-money-bill-wave"></i></button>';
        }
        if(($item->status == 2) && ($item->ot_payment_status != 'requested' && $item->ot_payment_status != 'paid')) {
             $btn .= '<button class="btn btn-sm btn-warning btn-action" onclick="sendToFinance('.$item->id.', this)" title="إرسال للمالية"><i class="fas fa-paper-plane"></i></button>';
        }
        $btn .= '<a href="'.base_url('users1/view_request/'.$item->id).'" class="btn btn-sm btn-primary btn-action"><i class="fas fa-eye"></i></a>';
        $btn .= '</div>';
        $row['actions'] = $btn;

        $data[] = $row;
    }

    echo json_encode([
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->hr_model->count_all_overtime(),
        "recordsFiltered" => $this->hr_model->count_filtered_overtime(),
        "data" => $data,
        "csrf_hash" => $this->security->get_csrf_hash()
    ]);
}

// MATCH THE NAME TO YOUR JAVASCRIPT URL: "fetch_mandate_data"
public function fetch_mandate_data()
{
    if (!$this->session->userdata('logged_in')) return;
    $this->load->model('hr_model');
    
    // 1. Get Data
    $list = $this->hr_model->get_mandate_datatables();
    $data = [];
    $no = $_POST['start'];
    
    $current_user = $this->session->userdata('username');
    $finance_ids = ['1693', '2909', '1936', '2833'];
    $is_finance = in_array($current_user, $finance_ids);

    foreach ($list as $r) {
        $no++;
        
        // --- Status Logic ---
        $status_html = '<span class="status-badge badge-soft-warning">تحت الإجراء</span>';
        if ($r->status == 'Approved' || $r->status == 'Completed') {
            $status_html = '<span class="status-badge badge-soft-success">معتمد</span>';
        } elseif ($r->status == 'Rejected') {
            $status_html = '<span class="status-badge badge-soft-danger">مرفوض</span>';
        }

        // --- Payment Status Logic ---
        $pay_status = '<span class="status-badge badge-soft-secondary">غير مدفوع</span>';
        if ($r->payment_status == 'paid') {
            $pay_status = '<span class="status-badge badge-soft-success">تم الدفع</span>';
            if(!empty($r->payment_date)) {
                $pay_status .= '<br><small class="text-muted" style="font-size:0.7rem">'.$r->payment_date.'</small>';
            }
        } elseif ($r->payment_status == 'requested') {
            $pay_status = '<span class="status-badge badge-soft-info">بانتظار المالية</span>';
        }

        // --- Actions Buttons ---
        $actions = '<div class="d-flex justify-content-center">';
        
        // HR Request Button
        if (($r->status == 'Approved' || $r->status == 'Completed') && !$is_finance && $r->payment_status != 'requested' && $r->payment_status != 'paid') {
             $actions .= '<button type="button" class="btn-action btn-request" onclick="sendMandateToFinance('.$r->id.', this)" data-bs-toggle="tooltip" title="طلب صرف"><i class="fas fa-paper-plane"></i></button>';
        }

        // Finance Confirm Button
        if (($r->status == 'Approved' || $r->status == 'Completed') && $is_finance && $r->payment_status == 'requested') {
            $actions .= '<button type="button" class="btn-action btn-pay ms-1" onclick="openPaymentModal('.$r->id.', \'mandate\')" data-bs-toggle="tooltip" title="صرف"><i class="fas fa-check"></i></button>';
        }
        $actions .= '</div>';

        // --- FIX: APPROVER BUTTON (Solution 2) ---
        // We use type '9' for Mandates. 
        $approver_btn = '<button type="button" class="btn btn-sm btn-light border" onclick="showApprovers('.$r->id.', 9)">
                            <i class="fas fa-sitemap text-muted"></i> <span class="small">عرض</span>
                         </button>';

        // --- BUILD ROW ---
        $row = [
            'id' => $r->id,
            'emp_id' => $r->emp_id,
            'emp_name' => '<strong>' . $r->subscriber_name . '</strong>',
            'company_name' => $r->company_name ?? '-',
            
            // --- UPDATED THIS LINE ---
            'approvers' => $approver_btn, 
            // -------------------------

            'start_date' => $r->start_date,
            'duration' => $r->duration_days . ' يوم',
            'amount' => number_format($r->total_amount, 2),
            'status' => $status_html,
            'pay_status' => $pay_status,
            'bank_receipt_file' => $r->payment_receipt, 
            'actions' => $actions
        ];

        $data[] = $row;
    }

    $output = [
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $this->hr_model->count_all_mandate(),
        "recordsFiltered" => $this->hr_model->count_filtered_mandate(),
        "data" => $data,
        "csrf_hash" => $this->security->get_csrf_hash()
    ];

    echo json_encode($output);
}


// Payment Actions


public function export_dashboard_data() {
    $this->load->model('hr_model');
    $this->load->dbutil();
    $this->load->helper('download');

    $type = $this->input->get('type');
    
    // Simulate POST for the model query
    $_POST['filter_company'] = $this->input->get('filter_company');
    $_POST['filter_emp_name_text'] = $this->input->get('filter_emp_name_text');
    $_POST['filter_emp_id_text'] = $this->input->get('filter_emp_id_text');
    $_POST['filter_date_from'] = $this->input->get('filter_date_from');
    $_POST['filter_date_to'] = $this->input->get('filter_date_to');

    $query = $this->hr_model->get_export_query($type);
    
    $csv = $this->dbutil->csv_from_result($query, ",", "\r\n");
    force_download($type.'_report_'.date('Y-m-d').'.csv', "\xEF\xBB\xBF" . $csv);
}
// Find public function attendance_report() in Users1.php and modify it:

public function attendance_report() {
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    
    $this->load->model('hr_model');
    
    $data['departments'] = $this->hr_model->get_distinct_employees_field('n1');
    $data['companies'] = $this->hr_model->get_distinct_employees_field('company_name');
    
    // --- CHANGE: Use 'address' to fetch the unique locations ---
    $data['locations'] = $this->hr_model->get_distinct_employees_field('address'); 
    
    $this->load->view('templateo/attendance_report_view', $data);
}

public function attendance_report_json()
{
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        return;
    }

    $this->load->model('hr_model');

    // 2. Get Filters from POST
    $start_date = $this->input->post('start_date');
    $end_date   = $this->input->post('end_date');
    
    $filters = [
        'department' => $this->input->post('department'),
        'company'    => $this->input->post('company'),
        // Capture the location input from the view
        'location'   => $this->input->post('location') 
    ];

    // 3. Get Data from Model
    $report_data = $this->hr_model->get_comprehensive_attendance_report($start_date, $end_date, $filters);

    // 4. Return JSON
    $response = [
        'data' => $report_data,
        'csrf_hash' => $this->security->get_csrf_hash()
    ];

    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function export_attendance_overview()
{
    // 1. Security Check
    $logged_in_username = $this->session->userdata('username');
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($logged_in_username, $hr_users)) {
        show_error('Unauthorized access.', 403);
        return;
    }

    $this->load->model('hr_model');
    $this->load->helper('download');

    // 2. Get Filters (Including the new ones)
    $filters = [
        'start_date'  => $this->input->get('start_date', TRUE) ?: date('Y-m-01'),
        'end_date'    => $this->input->get('end_date', TRUE) ?: date('Y-m-t'),
        'employee_id' => $this->input->get('employee_id', TRUE),
        'department'  => $this->input->get('department', TRUE),
        'profession'  => $this->input->get('profession', TRUE), // New
        'company'     => $this->input->get('company', TRUE),    // New
        'location'    => $this->input->get('location', TRUE),
        'device' => $this->input->get('device', TRUE)    // New
    ];

    // 3. Fetch Data
    $overview_data = $this->hr_model->get_attendance_overview_data($filters);

    // 4. Pivot Data (Transform into Emp => Date structure)
    $pivoted_data = [];
    foreach ($overview_data['records'] as $record) {
        $emp_id = $record['employee_id'];
        if (!isset($pivoted_data[$emp_id])) {
            $pivoted_data[$emp_id] = [
                'employee_name' => $record['employee_name'],
                'department'    => $record['department'],
                'profession'    => $record['profession'] ?? '',
                'company'       => $record['company'] ?? '',
                'location'      => $record['location'] ?? '',
                'dates'         => []
            ];
        }
        $pivoted_data[$emp_id]['dates'][$record['date']] = $record;
    }

    // 5. Calculate Totals
    foreach ($pivoted_data as $emp_id => &$emp_details) {
        $absence_cnt = 0; $vacation_cnt = 0; $violation_cnt = 0; $correction_cnt = 0;
        if (isset($emp_details['dates'])) {
            foreach ($emp_details['dates'] as $date_data) {
                $st = $date_data['day_status'];
                $vio = $date_data['violation'];
                
                if ($st === 'غياب') $absence_cnt++;
                if (strpos($st, 'إجازة') !== false || $st === 'عطلة رسمية') $vacation_cnt++;
                if ($st === 'تصحيح بصمة') $correction_cnt++;
                if (!empty($vio)) $violation_cnt++;
            }
        }
        $emp_details['stats'] = [$absence_cnt, $vacation_cnt, $violation_cnt, $correction_cnt];
    }
    unset($emp_details);

    // 6. Prepare CSV
    $filename = "Attendance_Report_" . date('Y-m-d') . ".csv";
    
    // Set headers for download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel Arabic support
    fwrite($output, "\xEF\xBB\xBF");

    // --- Build Header Row ---
    $csv_headers = [
        'الرقم الوظيفي', 
        'اسم الموظف', 
        'الإدارة', 
        'المسمى الوظيفي', 
        'الشركة', 
        'الموقع'
    ];
    
    // Add Date Columns
    $period = new DatePeriod(
        new DateTime($filters['start_date']), 
        new DateInterval('P1D'), 
        (new DateTime($filters['end_date']))->modify('+1 day')
    );
    
    $date_keys = [];
    foreach ($period as $date) {
        $d_str = $date->format('Y-m-d');
        $date_keys[] = $d_str;
        $csv_headers[] = $d_str . ' (' . $date->format('D') . ')';
    }
    
    // Add Summary Columns
    $csv_headers[] = 'إجمالي الغياب';
    $csv_headers[] = 'أيام الإجازات';
    $csv_headers[] = 'المخالفات';
    $csv_headers[] = 'التصحيحات';
    
    fputcsv($output, $csv_headers);

    // --- Build Data Rows ---
    foreach ($pivoted_data as $emp_id => $emp) {
        $row = [
            $emp_id,
            $emp['employee_name'],
            $emp['department'],
            $emp['profession'],
            $emp['company'],
            $emp['location']
        ];

        // Loop Dates
        foreach ($date_keys as $date) {
            $cell = $emp['dates'][$date] ?? null;
            $cell_text = '';

            if ($cell) {
                $st = $cell['day_status'];
                if ($st === 'حاضر') {
                    $in = $cell['check_in'] ?? '-';
                    $out = $cell['check_out'] ?? '-';
                    $cell_text = "$in - $out";
                    if ($cell['violation']) $cell_text .= " (" . $cell['violation'] . ")";
                } else {
                    $cell_text = $st;
                }
            } else {
                // Check if weekend
                $dw = date('w', strtotime($date));
                if ($dw == 5 || $dw == 6) $cell_text = 'عطلة نهاية أسبوع';
            }
            $row[] = $cell_text;
        }

        // Append Stats
        $row[] = $emp['stats'][0]; // Absent
        $row[] = $emp['stats'][1]; // Vacation
        $row[] = $emp['stats'][2]; // Violation
        $row[] = $emp['stats'][3]; // Correction

        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
public function orders_emp_app() 
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }

    $this->load->model('hr_model');
    $approver_id = $this->session->userdata('username');

    // 1. Get Requests
    $data['get_salary_vacations'] = $this->hr_model->get_pending_requests_for_approver($approver_id);
    
    // 2. Get Salary Sheets
    $data['salary_sheets'] = $this->hr_model->get_all_salary_sheets_simple();

    // 3. ✅ NEW: Get Current User's Department (n1)
    // We fetch the full details of the logged-in user to get their department 'n1'
    $current_user_info = $this->hr_model->get_employee_details($approver_id);
    // 'department' alias comes from get_employee_details select 'emp1.n1 AS department'
    $data['my_department'] = $current_user_info['department'] ?? ''; 
    $data['current_user_id'] = $approver_id;

    $this->load->view('templateo/orders_emp_app', $data);
}

// In Users1.php, REPLACE the eos_approvals function
public function eos_approvals()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    $this->load->model('hr_model');
    
    // Use the new model function to get ALL tasks for the logged-in user
    $user_id = $this->session->userdata('username');
    $data['all_eos_tasks'] = $this->hr_model->get_all_eos_tasks_for_user($user_id);

    $this->load->view('templateo/eos_approvals_view', $data);
}
public function update_order_status()
    {
        if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
            $this->output->set_status_header(403);
            echo json_encode(['ok' => false, 'error' => 'Forbidden']);
            return;
        }

        $this->load->model('hr_model');

        $order_id = (int)$this->input->post('id');
        $new_status = (int)$this->input->post('status'); // 2 for approve, 3 for reject
        $reason = $this->input->post('reason');
        $approver_id = $this->session->userdata('username');

        if (!$order_id || !in_array($new_status, [2, 3])) {
            echo json_encode(['ok' => false, 'error' => 'Invalid data submitted.']);
            return;
        }

        $success = false;
        
        if ($new_status === 2) { // Approval Action
            $success = $this->hr_model->process_approval($order_id, $approver_id);
            
            // =========================================================
            // START: Auto-Log Attendance & Email Triggers
            // =========================================================
            // Only run this if the approval step was successful
            if ($success) {
                // Check the current status of the order in the database
                // We need to know if this specific approval made the order "Fully Approved" (status 2)
                $order_check = $this->db->select('status, type')
                    ->get_where('orders_emp', ['id' => $order_id])
                    ->row();

                // If found and is fully approved (2)
                // If found and is fully approved (2)
                if ($order_check && $order_check->status == 2) {
                    if ($order_check->type == 9) {
                        $this->hr_model->insert_mission_attendance_logs($order_id);
                    } elseif ($order_check->type == 12) {
                        // Automatically insert attendance logs for Permission requests!
                        $this->hr_model->auto_log_permission_attendance($order_id);
                    } elseif ($order_check->type == 1) {
                        // === [NEW] AUTO-RESIGN & CORRECT LEAVE BALANCE ===
                        // Automatically change emp1 status and fix leave balance 
                        // ONLY if the approver is 2784 or 2901
                        $this->hr_model->execute_final_resignation_approval($order_id, $approver_id);
                        // ==================================================
                    }
                }

                // === [NEW] TRIGGER EMAIL FOR NEXT APPROVER OR FINAL APPROVAL ===
                // This checks the hierarchy and emails the next manager, HR, or the employee
                $this->hr_model->trigger_next_approval_email($order_id, $approver_id);
                // ===============================================================
            }
            // =========================================================
            // END: Auto-Log Attendance & Email Triggers
            // =========================================================

        } elseif ($new_status === 3) { // Rejection Action
            if (empty($reason) || mb_strlen($reason) < 3) {
                echo json_encode(['ok' => false, 'error' => 'Rejection reason is required.']);
                return;
            }
            
            $success = $this->hr_model->process_rejection($order_id, $approver_id, $reason);

           // =========================================================
            // START: [NEW] BEAUTIFUL REJECTION EMAIL NOTIFICATION
            // =========================================================
            if ($success) {
                $order_info = $this->db->get_where('orders_emp', ['id' => $order_id])->row_array();
                if ($order_info) {
                    $applicant_data = $this->db->select('email, subscriber_name')->where('employee_id', $order_info['emp_id'])->get('emp1')->row();
                    
                    if ($applicant_data && !empty($applicant_data->email)) {
                        $req_name_ar = $order_info['order_name'] ?? 'طلب الموارد البشرية';
                        $applicant_name = $applicant_data->subscriber_name ?? 'الموظف';
                        $order_date = $order_info['date'] ?? date('Y-m-d');
                        
                        $details = [
                            'رقم الطلب' => '#' . $order_id,
                            'نوع الطلب' => $req_name_ar,
                            'تاريخ التقديم' => $order_date,
                            '<span style="color:#e74c3c">سبب الرفض</span>' => '<span style="color:#e74c3c">'.htmlspecialchars($reason).'</span>'
                        ];

                        // Use the new beautiful template generator from the model, using RED (#e74c3c) as the main color
                        $html = $this->hr_model->build_beautiful_email_template(
                            $applicant_name, 
                            "نعتذر، لقد تم رفض طلبك", 
                            "نأسف لإبلاغك بأنه قد تم رفض طلبك من قبل المسؤول المختص. يرجى الاطلاع على سبب الرفض أدناه.", 
                            $details, 
                            base_url('users/login'), 
                            "مراجعة تفاصيل الطلب", 
                            "#e74c3c" // Red color for rejection
                        );

                        $this->hr_model->send_html_email($applicant_data->email, "تم رفض الطلب ❌ - Marsoom HR", $html, $order_id);
                    }
                }
            }
            // =========================================================
            // END: REJECTION EMAIL NOTIFICATION
            // =========================================================
        }

        if ($success) {
            $response = ['ok' => true];
        } else {
            $response = ['ok' => false, 'error' => 'Failed to process the request. The request may have already been actioned or you may not have permission.'];
        }
        
        // Refresh CSRF token for the next request
        $response['csrfHash'] = $this->security->get_csrf_hash();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


private function _json($arr){
    $this->output
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))
        ->_display();
    exit;
}
// In Users1.php

public function add_employee()
{
    // Security Check
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $data['page_title'] = 'إضافة موظف جديد';
    $data['employee'] = null; // No employee data for add mode
    $this->load->view('templateo/employee_form_view', $data);
}

public function edit_employee($id)
{
    // Security Check
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $this->load->model('hr_model');
    $data['employee'] = $this->hr_model->get_employee_by_id($id);

    if (empty($data['employee'])) {
        show_404();
    }

    $data['page_title'] = 'تعديل بيانات الموظف';
    $this->load->view('templateo/employee_form_view', $data);
}

// In controllers/Users1.php

// In controllers/Users1.php

public function save_employee()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }

    $this->load->model('hr_model');
    
    $id = $this->input->post('id');
    $employee_data = $this->input->post();
    unset($employee_data['id']);

    // --- LOGIC FOR A NEW EMPLOYEE ---
    if (empty($id)) {
        $employee_id = $this->input->post('employee_id');
        
        if ($this->hr_model->check_employee_id_exists($employee_id)) {
            $this->session->set_flashdata('error', 'فشل الإضافة: الرقم الوظيفي "' . html_escape($employee_id) . '" مسجل لموظف آخر.');
            redirect('users1/add_employee');
            return;
        }

        // ✅ ADD THIS LINE: Automatically set the status for new employees
        $employee_data['status'] = 'active';

        if ($this->hr_model->add_employee($employee_data)) {
            $this->session->set_flashdata('success', 'تمت إضافة الموظف بنجاح.');
        } else {
            $this->session->set_flashdata('error', 'حدث خطأ أثناء إضافة الموظف.');
        }
    } 
    // --- LOGIC FOR AN EXISTING EMPLOYEE ---
    else {
        if ($this->hr_model->update_employee($id, $employee_data)) {
            $this->session->set_flashdata('success', 'تم تحديث بيانات الموظف بنجاح.');
        } else {
            $this->session->set_flashdata('error', 'حدث خطأ أثناء تحديث البيانات.');
        }
    }
    
    redirect('users1/emp_data101');
}

// In Users1.php

public function org_structure_management()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    $data['structures'] = $this->hr_model->get_all_org_structures();
    
    // Load the new list view we will create
    $this->load->view('templateo/org_structure_list_view', $data);
}

public function edit_org_structure($id = 0)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    
    // Fetch all employees for the dropdown menus
    $data['employees'] = $this->hr_model->get_all_employees_for_dropdown();
    $data['structure'] = null;

    if ($id > 0) {
        // Edit mode: fetch the existing data
        $data['page_title'] = 'تعديل الهيكل التنظيمي';
        $data['structure'] = $this->hr_model->get_org_structure_by_id($id);
    } else {
        // Add mode
        $data['page_title'] = 'إضافة سلسلة جديدة للهيكل التنظيمي';
    }
    
    // Load the new form view we will create
    $this->load->view('templateo/org_structure_form_view', $data);
}

public function save_org_structure()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    
    $id = $this->input->post('id');
    $data = [
        'n1' => $this->input->post('n1'),
        'n2' => $this->input->post('n2'),
        'n3' => $this->input->post('n3'),
        'n4' => $this->input->post('n4'),
        'n5' => $this->input->post('n5'),
        'n6' => $this->input->post('n6'),
        'n7' => $this->input->post('n7'),
    ];
    
    if (empty($id)) {
        // Add new record
        $this->hr_model->add_org_structure($data);
        $this->session->set_flashdata('success', 'تم إضافة الهيكل بنجاح.');
    } else {
        // Update existing record
        $this->hr_model->update_org_structure($id, $data);
        $this->session->set_flashdata('success', 'تم تحديث الهيكل بنجاح.');
    }
    
    redirect('users1/org_structure_management');
}

public function delete_org_structure($id)
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    
    $this->load->model('hr_model');
    $this->hr_model->delete_org_structure_by_id($id);
    $this->session->set_flashdata('success', 'تم حذف الهيكل بنجاح.');
    redirect('users1/org_structure_management');
}
public function toggle_employee_status()
{
    if (!$this->input->is_ajax_request()) { exit('No direct script access allowed'); }

    $id = $this->input->post('id');
    $current_status = $this->input->post('status');
    $new_status = ($current_status === 'active') ? 'inactive' : 'active';
    
    $this->load->model('hr_model');
    if ($this->hr_model->update_employee_status($id, $new_status)) {
        echo json_encode(['status' => 'success', 'new_status' => $new_status]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
    }
}

public function upload_employees_page()
{
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    $this->load->view('templateo/employee_upload_view');
}
// Download CSV template
public function download_insurance_discounts_template()
{
    $filename = "insurance_discounts_template.csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['n1', 'n2', 'n3']); // Headers
    fputcsv($output, ['5001', 'أحمد محمد', '5.5']); // Example row
    fputcsv($output, ['5002', 'فاطمة عبدالله', '7.25']); // Example row
    fclose($output);
    exit;
}

// Process CSV upload
public function process_insurance_discounts_csv()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $config['upload_path'] = './uploads/csv/insurance_discounts/';
    $config['allowed_types'] = 'csv';
    $config['max_size'] = 5120; // 5MB
    $config['encrypt_name'] = true;
    
    // Create upload directory if it doesn't exist
    if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0777, true);
    }
    
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('csv_file')) {
        $response = [
            'status' => 'error',
            'message' => 'فشل في رفع الملف: ' . $this->upload->display_errors(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];
        echo json_encode($response);
        return;
    }

    $file_data = $this->upload->data();
    $file_path = $file_data['full_path'];
    $update_existing = $this->input->post('update_existing') == '1';

    try {
        $discounts_data = $this->parse_insurance_discounts_csv($file_path);
        
        if (empty($discounts_data)) {
            throw new Exception('الملف فارغ أو أن رؤوس الأعمدة غير صحيحة.');
        }

        $this->load->model('hr_model');
        $result = $this->hr_model->process_insurance_discounts_batch($discounts_data, $update_existing);
        
        $response = [
            'status' => 'success',
            'message' => 'تمت معالجة الملف بنجاح',
            'inserted' => $result['inserted'],
            'updated' => $result['updated'],
            'errors' => $result['errors'],
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];
    }

    // Clean up uploaded file
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    echo json_encode($response);
}

private function parse_insurance_discounts_csv($file_path)
{
    $discounts = [];
    
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        // Read and validate header
        $header = fgetcsv($handle, 1000, ",");
        
        if ($header === FALSE) {
            throw new Exception('لا يمكن قراءة رؤوس الأعمدة من الملف.');
        }
        
        // Clean header names
        $header = array_map('trim', $header);
        $header = array_map(function($value) {
            return preg_replace('/^\xEF\xBB\xBF/', '', $value); // Remove BOM
        }, $header);
        
        $expected_headers = ['n1', 'n2', 'n3'];
        if (count(array_intersect($expected_headers, $header)) != count($expected_headers)) {
            throw new Exception('رؤوس الأعمدة غير صحيحة. يجب أن تكون: n1, n2, n3');
        }
        
        $row_count = 1;
        
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_count++;
            
            // Skip empty rows
            if (empty($row) || (count($row) == 1 && empty(trim($row[0])))) {
                continue;
            }
            
            // Ensure row has same number of columns as header
            $padded_row = array_pad($row, count($header), '');
            $discount_data = array_combine($header, $padded_row);
            
            // Clean and validate data
            $discount_data = $this->clean_discount_data($discount_data);
            
            // Only add if required fields exist
            if (!empty($discount_data['n1']) && !empty($discount_data['n2'])) {
                $discounts[] = $discount_data;
            }
        }
        
        fclose($handle);
    } else {
        throw new Exception('لا يمكن فتح ملف CSV.');
    }
    
    return $discounts;
}

private function clean_discount_data($data)
{
    foreach ($data as $key => $value) {
        // Trim whitespace
        if (is_string($value)) {
            $data[$key] = trim($value);
        }
        
        // Convert empty strings to NULL
        if ($value === '') {
            $data[$key] = null;
        }
        
        // Validate percentage (n3)
        if ($key === 'n3' && $value !== null && $value !== '') {
            $percentage = floatval($value);
            if ($percentage < 0 || $percentage > 100) {
                $data[$key] = null; // Invalid percentage
            } else {
                $data[$key] = $percentage;
            }
        }
    }
    
    return $data;
}

public function process_employee_upload_csv()
{
    if (!$this->session->userdata('logged_in')) { 
        redirect('users/login'); 
    }

    // Upload configuration for CSV
    $config['upload_path'] = './uploads/employees/';
    $config['allowed_types'] = 'csv';
    $config['max_size'] = 5120; // 5MB
    $config['encrypt_name'] = true;
    
    // Create upload directory if it doesn't exist
    if (!is_dir($config['upload_path'])) { 
        mkdir($config['upload_path'], 0777, true); 
    }
    
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('employee_file')) {
        $error = $this->upload->display_errors();
        $this->session->set_flashdata('error', 'فشل في رفع الملف: ' . $error);
    } else {
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];
        
        try {
            // Process CSV file
            $employees_to_upsert = $this->parse_csv_file($file_path);
            
            if (empty($employees_to_upsert)) {
                $this->session->set_flashdata('error', 'الملف فارغ أو أن رؤوس الأعمدة غير صحيحة.');
            } else {
                $this->load->model('hr_model');
                $result = $this->hr_model->upsert_batch_employees($employees_to_upsert);
                
                $message = "تمت معالجة الملف بنجاح. السجلات المضافة: {$result['inserted']}, السجلات المحدثة: {$result['updated']}.";
                
                if (!empty($result['errors'])) {
                    $message .= " عدد الأخطاء: " . count($result['errors']);
                    // Log errors for debugging
                    log_message('error', 'CSV upload errors: ' . implode('; ', $result['errors']));
                }
                
                $this->session->set_flashdata('success', $message);
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'خطأ في معالجة الملف: ' . $e->getMessage());
        }
        
        // Clean up uploaded file
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    redirect('users1/emp_data101');
}

private function parse_csv_file($file_path)
{
    $employees = [];
    
    // Define only the valid columns that exist in your emp1 table
    $valid_columns = [
        'status', 'availability_status', 'employee_id', 'id_number', 'email', 
        'marital', 'phone', 'religion', 'id_expiry', 'address', 'subscriber_name', 
        'nationality', 'gender', 'birth_date', 'base_salary', 'housing_allowance', 
        'commissions', 'other_allowances', 'total_salary', 'salary_subject_to_contribution', 
        'profession', 'joining_date', 'company_name', 'n1', 'n2', 'n3', 'n4', 'n5', 
        'n6', 'n7', 'n8', 'n9', 'n10', 'n11', 'n12', 'location', 'manager', 'type', 
        'contract_status', 'contract_period', 'contract_start', 'contract_end', 'n13', 
        'iqama_expiry_date'
    ];
    
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        // Read the header row
        $header = fgetcsv($handle, 1000, ",");
        
        if ($header === FALSE) {
            throw new Exception('لا يمكن قراءة رؤوس الأعمدة من الملف.');
        }
        
        // Clean header names (remove BOM and trim spaces)
        $header = array_map(function($value) {
            // Remove UTF-8 BOM if exists
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);
            return trim($value);
        }, $header);
        
        // Filter header to only include valid columns
        $filtered_header = [];
        foreach ($header as $column) {
            if (in_array($column, $valid_columns)) {
                $filtered_header[] = $column;
            }
        }
        
        // If no valid columns found, throw error
        if (empty($filtered_header)) {
            throw new Exception('لم يتم العثور على أعمدة صحيحة في الملف. تأكد من أن رؤوس الأعمدة مطابقة لقاعدة البيانات.');
        }
        
        $row_count = 1; // Start from 1 since we already read the header
        
        // Read data rows
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_count++;
            
            // Skip empty rows
            if (empty($row) || (count($row) == 1 && empty(trim($row[0])))) {
                continue;
            }
            
            // Create employee data array with only valid columns
            $employee_data = [];
            foreach ($header as $index => $column_name) {
                // Only include if it's a valid column and we have data for this index
                if (in_array($column_name, $valid_columns) && isset($row[$index])) {
                    $employee_data[$column_name] = $row[$index];
                }
            }
            
            // Fill missing valid columns with empty values
            foreach ($valid_columns as $column) {
                if (!isset($employee_data[$column])) {
                    $employee_data[$column] = '';
                }
            }
            
            // Clean the data
            $employee_data = $this->clean_csv_data($employee_data);
            
            // Only add if employee_id exists
            if (!empty($employee_data['employee_id'])) {
                $employees[] = $employee_data;
            }
        }
        
        fclose($handle);
    } else {
        throw new Exception('لا يمكن فتح ملف CSV.');
    }
    
    return $employees;
}

private function clean_csv_data($data)
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
            'n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7', 'n8', 'n9', 'n10', 'n11', 'n12', 'n13',
        ];
        
        if (in_array($key, $numeric_fields) && $value !== null && $value !== '') {
            // Remove any non-numeric characters except decimal point and minus sign
            $cleaned_value = preg_replace('/[^\d.-]/', '', $value);
            $data[$key] = is_numeric($cleaned_value) ? $cleaned_value : null;
        }
        
        // Handle date fields
        $date_fields = ['birth_date', 'joining_date', 'contract_start', 'contract_end', 'iqama_expiry_date', 'id_expiry'];
        if (in_array($key, $date_fields) && !empty($value)) {
            $data[$key] = $this->parse_csv_date($value);
        }
        
        // Handle phone numbers - remove any non-digit characters
        if ($key === 'phone' && !empty($value)) {
            $data[$key] = preg_replace('/\D/', '', $value);
        }
    }
    
    // Set default values for required fields that cannot be NULL
    if (empty($data['type'])) {
        $data['type'] = 'employee'; // or whatever default value your database expects
    }
    
    // You might want to add other required fields here as well
    if (empty($data['contract_status'])) {
        $data['contract_status'] = 'active';
    }
    
    return $data;
}
private function parse_csv_date($value)
{
    if (empty($value)) return null;
    
    // Try different date formats
    $formats = [
        
        'Y-m-d',
        'd/m/Y',
        'd-m-Y',
        'm/d/Y',
        'm-d-Y'
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $value);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }
    }
    
    // If all else fails, try strtotime
    $timestamp = strtotime($value);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    
    return null;
}
// In Users1.php

public function stop_salary_management()
{
    // Security Check
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }

    $this->load->model('hr_model');
    
    // Fetch data
    $data['stop_requests'] = $this->hr_model->get_all_stop_salary_requests();
    $data['employees'] = $this->hr_model->get_all_employees();
    $data['salary_sheets'] = $this->hr_model->get_salary_salary_sheet(); // Get dropdown list
    
    $data['csrf_name'] = $this->security->get_csrf_token_name();
    $data['csrf_hash'] = $this->security->get_csrf_hash();
    
    $this->load->view('templateo/stop_salary_management', $data);
}

public function save_stop_salary()
{
    if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

    $this->load->model('hr_model');
    
    $id = $this->input->post('id');
    $raw_emp_ids = $this->input->post('emp_ids');
    $emp_ids = [];
    if(is_array($raw_emp_ids)){
        $emp_ids = array_filter($raw_emp_ids, function($value) { return !empty($value); });
    } elseif(!empty($raw_emp_ids)) {
        $emp_ids = [$raw_emp_ids];
    }
    $sheet_id = $this->input->post('sheet_id');
    $stop_date = $this->input->post('stop_date');
    
    // 1. Validation: Ensure at least one option is selected
    if (empty($sheet_id) && empty($stop_date)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'يجب اختيار "تاريخ الإيقاف" أو "فترة المسير" على الأقل.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
        return;
    }

    // 2. "Date Only" Scenario: Auto-detect the Sheet ID
    if (!empty($stop_date) && empty($sheet_id)) {
        $found_sheet = $this->hr_model->get_salary_sheet_by_date($stop_date);
        
        if ($found_sheet) {
            $sheet_id = $found_sheet['id']; // Auto-assign the correct sheet
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'لا يوجد مسير رواتب معرف لهذا التاريخ (' . $stop_date . '). يرجى اختيار المسير يدوياً أو إنشاء مسير جديد.',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
    }

    // 3. "Both Selected" Scenario: Validate Range
    if (!empty($stop_date) && !empty($sheet_id)) {
        $sheet = $this->hr_model->get_salary_sheet($sheet_id);
        if ($sheet) {
            if ($stop_date < $sheet['start_date'] || $stop_date > $sheet['end_date']) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'تاريخ الإيقاف (' . $stop_date . ') يقع خارج نطاق المسير المختار (' . $sheet['start_date'] . ' - ' . $sheet['end_date'] . ').',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]);
                return;
            }
        }
    }

    // 4. Prepare Data (Handling NULL correctly for Database)
    $common_data = [
        'sheet_id'  => $sheet_id,
        // FIX: If empty, send NULL explicitly, otherwise send the date string
        'start_date' => !empty($stop_date) ? $stop_date : NULL,
        'reason'    => $this->input->post('reason'),
        'date'      => date('Y-m-d'), 
        'time'      => date('H:i:s') 
    ];

    $success_count = 0;

    if (!empty($id)) {
        // Edit Mode
        $single_emp_id = is_array($emp_ids) ? $emp_ids[0] : $emp_ids;
        $data = array_merge($common_data, ['emp_id' => $single_emp_id]);
        if ($this->hr_model->save_stop_salary_request($id, $data)) {
            $success_count++;
        }
    } else {
        // Add Mode
        if (is_array($emp_ids)) {
            foreach ($emp_ids as $emp_id) {
                $data = array_merge($common_data, ['emp_id' => $emp_id]);
                if ($this->hr_model->save_stop_salary_request(null, $data)) {
                    $success_count++;
                }
            }
        }
    }

    if ($success_count > 0) {
        $response = ['status' => 'success', 'message' => 'تم حفظ البيانات بنجاح'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل الحفظ'];
    } 
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    echo json_encode($response);
}
public function delete_stop_salary()
{
    if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
    
    $this->load->model('hr_model');
    $id = $this->input->post('id');
    
    if ($this->hr_model->delete_stop_salary($id)) {
        $response = ['status' => 'success', 'message' => 'تم الحذف بنجاح'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل الحذف'];
    }
    
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    echo json_encode($response);
}

public function get_stop_salary_details()
{
    $this->load->model('hr_model');
    $id = $this->input->post('id');
    $data = $this->hr_model->get_stop_salary_by_id($id);
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

public function add_new_order()
    {
        // --- [1] Keep Existing Login/Type Checks ---
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
            return; // Added return for clarity
        }
        // ============================================================
    // START: BLOCK EMPLOYEE 2824 FROM MAKING REQUESTS
    // ============================================================
    if ($this->session->userdata('username') == '2824') {
        // Optional: Set an error message to display on the redirect page
        $this->session->set_flashdata('error', 'عفواً، لا تملك صلاحية لتقديم طلب جديد.');
        
        // Redirect them back to the main page or requests list
        redirect('users1/orders_emp'); 
        return; // Stop execution immediately
    }
    // ============================================================
    // END BLOCK
    // ============================================================
// === START CALCULATION LOGIC ===

// === END CALCULATION LOGIC ===
        if ($this->session->userdata('type') == 10) {
            redirect('users/login');
            return; // Added return for clarity
        }

        $this->load->model('hr_model');
        $this->load->library('form_validation'); // Make sure form validation is loaded
        $data['title'] = 'طلب جديد';

        // --- [2] Load ALL data needed for the view BEFORE checking the form ---
        $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
        $logged_in_user_id = $this->session->userdata('username');
        $data['is_hr_user'] = in_array($logged_in_user_id, $hr_users);
        $data['public_holidays'] = $this->hr_model->get_all_holidays_as_dates();
        $current_emp_details = $this->hr_model->get_employee_profile_details($logged_in_user_id);
$data['current_user_joining_date'] = $current_emp_details['joining_date'] ?? '';

        // --- [3] Determine the target employee ID for initial view rendering ---
        $target_employee_id_for_view = $logged_in_user_id;
        // Check GET first (for HR browsing directly to a user's request page)
        if ($this->input->method() === 'get' && $data['is_hr_user'] && $this->input->get('employee_id')) {
            $target_employee_id_for_view = $this->input->get('employee_id');
        }
        // Check POST next (for reloading the form after a validation error)
        elseif ($this->input->method() === 'post' && $data['is_hr_user'] && $this->input->post('employee_id')) {
             $target_employee_id_for_view = $this->input->post('employee_id');
        }
        // If neither GET nor POST specifies an employee_id, HR defaults to themselves
        elseif ($data['is_hr_user'] && !$this->input->post('employee_id') && !$this->input->get('employee_id')) {
             $target_employee_id_for_view = $logged_in_user_id;
        }


        // --- [4] Fetch Last Working Day for the view ---
        // Fetch based on the employee ID determined for the *current view rendering*
        $data['last_working_day'] = $this->hr_model->get_approved_resignation_last_day($target_employee_id_for_view);
        log_message('debug', "Last working day for view (Employee ID: {$target_employee_id_for_view}): " . ($data['last_working_day'] ?: 'N/A'));

        // --- [4.5] التحقق من القسم لظهور طلب الاستئذان ---
        $emp_dept_info = $this->hr_model->get_employee_details($target_employee_id_for_view);
        $raw_dept = $emp_dept_info['department'] ?? '';
        
        // تنظيف النص: إزالة كل المسافات وتوحيد (أ، إ، آ -> ا) و (ة -> ه) لضمان التطابق
        $normalized_dept = str_replace(['أ', 'إ', 'آ', 'ة', ' '], ['ا', 'ا', 'ا', 'ه', ''], $raw_dept);
        
        $allowed_depts = [
            'إدارة الموارد البشرية', 
            'ادارة العمليات والتحليل', 
            'ادارة تقنية المعلومات', 
            'الإدارة المالية والمشتريات', 
            'امن المعلومات', 
            'قسم التوظيف', 
            'قسم التوظيفب', 
            'مكتب الدكتور صالح الجربوع', 
            'وحدة الجودة', 
            'وحدة حوكمة البيانات'
        ];
        
        $allowed_depts_normalized = array_map(function($d) {
            return str_replace(['أ', 'إ', 'آ', 'ة', ' '], ['ا', 'ا', 'ا', 'ه', ''], $d);
        }, $allowed_depts);
        
        $data['show_permission_request'] = in_array($normalized_dept, $allowed_depts_normalized);
        // --- [5] Keep existing data loading for view ---
        if ($data['is_hr_user']) {
            $data['employees'] = $this->hr_model->get_all_employees();
        }

        if (!isset($data['employees'])) {
             $data['employees'] = $this->hr_model->get_all_employees();
        }

        $gender_for_leave_types = $this->hr_model->get_employee_gender($target_employee_id_for_view); // Use target employee's gender
        $data['allowed_codes'] = array_column($this->db->get_where('series_of_approvals', ['status' => 1])->result_array(), 'code');
        $data['leave_types'] = $this->hr_model->get_leave_types($gender_for_leave_types); // Use target employee's gender
        $data['saturday_assignments'] = $this->hr_model->get_all_saturday_assignments_mapped();

        if ($data['is_hr_user']) {
            $data['all_balances'] = $this->hr_model->get_all_employee_balances_for_hr();
            $data['balances'] = $data['all_balances'][$target_employee_id_for_view] ?? [];
            if (!isset($data['employees'])) {
                $data['employees'] = $this->hr_model->get_all_employees();
            }
        } else {
            $data['balances'] = $this->hr_model->get_employee_balances($logged_in_user_id); // Non-HR always sees their own balance
            $data['all_balances'] = [];
        }

        // --- [6] Set Form Validation Rules ---
        $this->form_validation->set_rules('request_type', 'request_type', 'required');
        // Add more specific rules based on type inside the 'else' block if needed

        // --- [7] Check if form is submitted AND passes basic CI validation ---
        if ($this->form_validation->run() === FALSE) {
            // --- Form not submitted OR basic validation failed (e.g., CSRF, missing request_type) ---
            log_message('debug', 'Form validation FALSE - Loading view.');
            // $data['last_working_day'] is already set correctly for the view
            $this->load->view('templateo/add_new_order', $data); // Load view with potential validation errors from CI
        } else {
            // --- [8] FORM HAS BEEN SUBMITTED AND PASSED BASIC CI VALIDATION ---
            log_message('debug', '=== FORM SUBMISSION PASSED CI VALIDATION ===');
            // ... (keep debug logs) ...
            log_message('debug', 'ALL POST DATA: ' . print_r($this->input->post(), true));

            // --- [9] Determine target employee ID and name FOR SUBMISSION ---
            $target_employee_id_submit = $logged_in_user_id;
            $target_employee_name_submit = $this->session->userdata('name');
            if ($data['is_hr_user'] && $this->input->post('employee_id')) {
                $target_employee_id_submit = $this->input->post('employee_id');
                $emp_details = $this->hr_model->get_employee_info($target_employee_id_submit); // Use model function
                $target_employee_name_submit = $emp_details ? $emp_details['name'] : ('Unknown Employee (' . $target_employee_id_submit . ')');
            }
             log_message('debug', "Target employee for submission: ID={$target_employee_id_submit}, Name={$target_employee_name_submit}");
            // Set target employee in the model for saving the request
            $this->hr_model->set_target_employee($target_employee_id_submit, $target_employee_name_submit);

            $request_type = $this->input->post('request_type');
            $post_data = $this->input->post(); // All submitted data


            // --- [10] Fetch last working day specifically for SERVER-SIDE validation ---
            $last_working_day_submit = $this->hr_model->get_approved_resignation_last_day($target_employee_id_submit);
             log_message('debug', "Last working day for server validation (Employee ID: {$target_employee_id_submit}): " . ($last_working_day_submit ?: 'N/A'));


            // --- [11] SERVER-SIDE VALIDATION CHECKS (Leave Limits, Resignation Date, Duplicates) ---

            // --- Vacation Specific Validation ---
            if ($request_type === 'vacation') {
                log_message('debug', '=== VACATION REQUEST SERVER VALIDATION STARTED ===');

                $vac_data = $this->input->post('vac'); // Get vacation specific post data
                $vacation_type = $vac_data['main_type'] ?? null;
                $day_type = $vac_data['day_type'] ?? 'full';
                $vac_start_date_str = null;
                $vac_end_date_str = null; // Needed for calculation

                if ($day_type === 'half' && isset($vac_data['half_date'])) {
                     $vac_start_date_str = $vac_data['half_date'];
                     $vac_end_date_str = $vac_data['half_date']; // End date is same for half day
                } elseif ($day_type === 'full' && isset($vac_data['start']) && isset($vac_data['end'])) {
                     $vac_start_date_str = $vac_data['start'];
                     $vac_end_date_str = $vac_data['end'];
                }
                // --- EXECUTE CHECK ---
                // --- NEW: SERVER-SIDE PAST DATE CHECK --- //future
                $today_date = date('Y-m-d');
                if ($vacation_type !== 'sick' && $vacation_type !== 'maternity' && $vacation_type !== 'death' && $vacation_type !== 'death_brother') {
                    if (($vac_start_date_str && $vac_start_date_str < $today_date) || 
                        ($vac_end_date_str && $vac_end_date_str < $today_date)) {
                        
                        $this->session->set_flashdata('error_message', 'عفواً، لا يمكن اختيار تاريخ في الماضي لهذا النوع من الإجازات.');
                        $data['last_working_day'] = $last_working_day_submit;
                        $this->load->view('templateo/add_new_order', $data);
                        return;
                    }
                }
                // --- END PAST DATE CHECK ---
    if (!empty($vac_start_date_str) && !empty($vac_end_date_str)) {
        
        $target_id = $this->session->userdata('username');
        if ($this->input->post('employee_id')) {
            $target_id = $this->input->post('employee_id');
        }

        $capacity_check = $this->hr_model->check_department_capacity_conflict(
            $target_id, 
            $vac_start_date_str, 
            $vac_end_date_str
        );

        if ($capacity_check && $capacity_check['is_blocked']) {
            
            // Show the specific error message generated by the model
            $this->session->set_flashdata('error_message', "عفواً، تم رفض الطلب لتجاوز نسبة القسم (10%). " . $capacity_check['msg']);
            
            $data['last_working_day'] = $last_working_day_submit;
            $this->load->view('templateo/add_new_order', $data);
            return; 
        }
    }
    // --- END CHECK ---
                // Check 1: Resignation Date
                if ($last_working_day_submit && $vac_start_date_str && $vac_start_date_str > $last_working_day_submit) {
                    $error_msg = "لا يمكن طلب إجازة تبدأ ({$vac_start_date_str}) بعد تاريخ آخر يوم عمل ({$last_working_day_submit}) المحدد في طلب الاستقالة المعتمد.";
                    log_message('error', 'SERVER VALIDATION FAILED (Resignation Date): ' . $error_msg); // Log as error
                    $this->session->set_flashdata('error_message', $error_msg);
                    $data['last_working_day'] = $last_working_day_submit; // Pass date back
                    $this->load->view('templateo/add_new_order', $data); // Reload view
                    return; // Stop processing
                } else {
                     log_message('debug', 'Server validation passed (Resignation Date Check). Last Day: ' . ($last_working_day_submit ?: 'N/A') . ', Vac Start: ' . ($vac_start_date_str ?: 'N/A'));
                }

                // Check 2: Mandatory Fields
                if (empty($vacation_type)) {
                    $this->session->set_flashdata('error_message', 'نوع الإجازة مطلوب.');
                     $data['last_working_day'] = $last_working_day_submit;
                     $this->load->view('templateo/add_new_order', $data);
                    return;
                }
                if (empty($vac_start_date_str) || ($day_type === 'full' && empty($vac_end_date_str))) {
                    $this->session->set_flashdata('error_message', 'تاريخ بداية ونهاية الإجازة مطلوب.');
                     $data['last_working_day'] = $last_working_day_submit;
                     $this->load->view('templateo/add_new_order', $data);
                    return;
                }
                 if ($day_type === 'full' && $vac_end_date_str < $vac_start_date_str) {
                    $this->session->set_flashdata('error_message', 'تاريخ نهاية الإجازة يجب أن يكون بعد تاريخ البداية.');
                     $data['last_working_day'] = $last_working_day_submit;
                     $this->load->view('templateo/add_new_order', $data);
                    return;
                }


                // Check 3: Calculate requested days (using the model function)
                $requested_days = 0;
                if ($day_type === 'half') {
                    $requested_days = 0.5;
                } else {
                    // Pass the correct employee ID here!
                    $requested_days = $this->hr_model->calculate_business_days($vac_start_date_str, $vac_end_date_str, $target_employee_id_submit);
                     if ($requested_days <= 0 && $vacation_type !== 'maternity') { // Added check for maternity
                         $this->session->set_flashdata('error_message', 'الفترة المحددة لا تحتوي على أيام عمل صالحة.');
                         $data['last_working_day'] = $last_working_day_submit;
                         $this->load->view('templateo/add_new_order', $data);
                         return;
                     }
                }
                 log_message('debug', "Server calculated requested days: {$requested_days}");


                // Check 4: Balance / Limits
                $leave_type_details = $this->hr_model->get_leave_type_by_slug($vacation_type);
                if ($leave_type_details) {
                    // Annual balance check (only if requesting for self or target is self)
                    $is_creating_for_self = !$data['is_hr_user'] || ($data['is_hr_user'] && $target_employee_id_submit === $logged_in_user_id);
                    if ($vacation_type === 'annual' && $is_creating_for_self) {
                        $employee_balances = $this->hr_model->get_employee_balances($target_employee_id_submit);
                        $available_balance = $employee_balances['annual']['remaining'] ?? 0;
                        if ($requested_days > $available_balance) {
                             $error_msg = "رصيد الإجازة السنوية غير كافٍ - الطلب {$requested_days} يوم بينما الرصيد المتاح {$available_balance} يوم فقط";
                             $this->session->set_flashdata('error_message', $error_msg);
                             $data['last_working_day'] = $last_working_day_submit;
                             $this->load->view('templateo/add_new_order', $data);
                             return;
                        }
                    }
                    // Other leave types default limit check
                    // Make sure 'default_balance' column exists and is numeric
                    else if (isset($leave_type_details['default_balance']) && is_numeric($leave_type_details['default_balance'])) {
                        $max_allowed = (float)$leave_type_details['default_balance'];
                        // Only check limit if max_allowed is greater than 0
                        if ($max_allowed > 0 && $requested_days > $max_allowed) {
                             $error_msg = "لا يمكن طلب أكثر من {$max_allowed} يوم لإجازة {$leave_type_details['name_ar']} - المطلوب: {$requested_days} يوم";
                             $this->session->set_flashdata('error_message', $error_msg);
                             $data['last_working_day'] = $last_working_day_submit;
                             $this->load->view('templateo/add_new_order', $data);
                             return;
                         }
                    }
                } else {
                     $error_msg = 'نوع الإجازة المحدد غير صحيح أو غير فعال: ' . htmlspecialchars($vacation_type);
                     $this->session->set_flashdata('error_message', $error_msg);
                     $data['last_working_day'] = $last_working_day_submit;
                     $this->load->view('templateo/add_new_order', $data);
                     return;
                }
                 log_message('debug', 'Server-side vacation balance/limit checks passed.');
                 // Check 5: Attachment for Sick Leave
                 if ($vacation_type === 'sick' && (empty($_FILES['vac']['name']['file']) || $_FILES['vac']['error']['file'] !== UPLOAD_ERR_OK) ) {
                    $this->session->set_flashdata('error_message', 'يجب إرفاق تقرير طبي للإجازة المرضية.');
                    $data['last_working_day'] = $last_working_day_submit;
                    $this->load->view('templateo/add_new_order', $data);
                    return;
                 }


            } else {
                log_message('debug', 'Not a vacation request - skipping specific vacation server validation.');
            }
            // --- End Vacation Validation ---
           // ==================================================================
            // ** START: PERMISSION (الاستئذان) SERVER VALIDATION **
            // ==================================================================
            if ($request_type === 'permission') {
                
                // 1. التحقق من القسم (لضمان عدم التلاعب)
                $submit_emp_info = $this->hr_model->get_employee_details($target_employee_id_submit);
                $submit_raw_dept = $submit_emp_info['department'] ?? '';
                $submit_norm_dept = str_replace(['أ', 'إ', 'آ', 'ة', ' '], ['ا', 'ا', 'ا', 'ه', ''], $submit_raw_dept);
                
                $allowed_depts_normalized = array_map(function($d) {
                    return str_replace(['أ', 'إ', 'آ', 'ة', ' '], ['ا', 'ا', 'ا', 'ه', ''], $d);
                }, ['إدارة الموارد البشرية', 'ادارة العمليات والتحليل', 'ادارة تقنية المعلومات', 'الإدارة المالية والمشتريات', 'امن المعلومات', 'قسم التوظيف', 'قسم التوظيفب', 'مكتب الدكتور صالح الجربوع', 'وحدة الجودة', 'وحدة حوكمة البيانات','وحدة الالتزام والحوكمة']);

                if (!in_array($submit_norm_dept, $allowed_depts_normalized)) {
                    $this->session->set_flashdata('error_message', 'عفواً، قسمك غير مصرح له بتقديم طلب استئذان.');
                    $data['last_working_day'] = $last_working_day_submit;
                    $this->load->view('templateo/add_new_order', $data);
                    return;
                }

                // 2. التحقق من الوقت و الساعات
                $perm_date   = $this->input->post('perm_date');
                $perm_start  = $this->input->post('perm_start');
                $perm_end    = $this->input->post('perm_end');

                $start_ts = strtotime($perm_start);
                $end_ts   = strtotime($perm_end);
                $hours    = ($end_ts - $start_ts) / 3600;

                if ($hours < 1 || $hours > 2) {
                    $this->session->set_flashdata('error_message', 'يجب أن تكون مدة الاستئذان بين ساعة وساعتين فقط.');
                    $data['last_working_day'] = $last_working_day_submit;
                    $this->load->view('templateo/add_new_order', $data);
                    return;
                }

                // 3. التحقق من الرصيد الشهري (الحد الأقصى 6 ساعات)
                $month = date('m', strtotime($perm_date));
                $year  = date('Y', strtotime($perm_date));
                $used_hours = $this->hr_model->get_monthly_permission_hours($target_employee_id_submit, $month, $year);

                if (($used_hours + $hours) > 6) {
                    $this->session->set_flashdata('error_message', 'لقد تجاوزت الحد الأقصى لرصيد الاستئذان هذا الشهر (6 ساعات). المتاح لك: ' . (6 - $used_hours) . ' ساعة.');
                    $data['last_working_day'] = $last_working_day_submit;
                    $this->load->view('templateo/add_new_order', $data);
                    return;
                }
            }
            // ==================================================================
            // ** END: PERMISSION VALIDATION **
            // ==================================================================
// ==================================================================    //future
            // **VALIDATION 1: DELEGATION CONFLICT (APPLICANT)**
            // Check if the person *applying* ($target_employee_id_submit) is already 
            // a delegate for someone else on an APPROVED (status=2) leave.
            // ==================================================================
            if ($vacation_type === 'annual') { 
    $conflict_applicant = $this->hr_model->check_delegation_conflict($target_employee_id_submit, $vac_start_date_str, $vac_end_date_str);

    if ($conflict_applicant) {
        $error_msg = sprintf(
            "لا يمكن تقديم طلب إجازة سنوية. أنت مفوض بمهام الموظف (%s) من تاريخ %s إلى %s، وطلبك يتداخل مع هذه الفترة.",
            htmlspecialchars($conflict_applicant['emp_name']),
            htmlspecialchars($conflict_applicant['vac_start']),
            htmlspecialchars($conflict_applicant['vac_end'])
        );
        log_message('error', 'SERVER VALIDATION FAILED (Delegation Conflict): ' . $error_msg);
        $this->session->set_flashdata('error_message', $error_msg);
        $data['last_working_day'] = $last_working_day_submit;
        $this->load->view('templateo/add_new_order', $data); // Reload view
        return; // Stop processing
    } else {
        log_message('debug', 'Server validation passed (Delegation Conflict Check 1).');
    }
}

            // ==================================================================
            // **VALIDATION 2: DELEGATE AVAILABILITY (SELECTED DELEGATE)**
            // If a delegate was selected, check if *they* are on an approved leave.
            // ==================================================================
            $delegation_id = $vac_data['delegation_employee_id'] ?? null;
            
            if (!empty($delegation_id)) {
                $conflict_delegate = $this->hr_model->check_delegate_availability_conflict($delegation_id, $vac_start_date_str, $vac_end_date_str);
                
                if ($conflict_delegate) {
                    // Get the delegate's name
                    $delegate_details = $this->hr_model->get_employee_info($delegation_id);
                    $delegate_name = $delegate_details ? $delegate_details['name'] : $delegation_id;
                    
                    $error_msg = sprintf(
                        "لا يمكن اختيار (%s) كمفوض. لديه إجازة معتمدة (طلب رقم %s) تتداخل مع هذه الفترة.",
                        htmlspecialchars($delegate_name),
                        htmlspecialchars($conflict_delegate['id'])
                    );
                    
                    log_message('error', 'SERVER VALIDATION FAILED (Delegate Availability Conflict): ' . $error_msg);
                    $this->session->set_flashdata('error_message', $error_msg);
                    $data['last_working_day'] = $last_working_day_submit;
                    $this->load->view('templateo/add_new_order', $data); // Reload view
                    return; // Stop processing
                } else {
                     log_message('debug', 'Server validation passed (Delegate Availability Check 2).');
                }
            }
            // ==================================================================
            // **END NEW CHECKS**
            // ==================================================================
            // --- [14] Keep Duplicate Check ---
            // Use the ID determined for submission
            $error_message_dup = $this->hr_model->check_for_duplicate_request($target_employee_id_submit, $request_type, $post_data);
            if ($error_message_dup) {
                log_message('error', 'Duplicate request detected: ' . $error_message_dup); // Log as error
                $this->session->set_flashdata('error_message', $error_message_dup);
                $data['last_working_day'] = $last_working_day_submit; // Pass date back
                $this->load->view('templateo/add_new_order', $data); // Reload view
                return;
            } else {
                log_message('debug', 'No duplicate requests found');
            }

            // --- [15] Keep File Upload Logic ---
            $relativePath = null;
            $fileInputMap = [
                'resign'      => 'resign', 'fingerprint' => 'fp', 'overtime' => 'ot',
                'asset'       => 'asset',  'letter'      => 'letter', 'vacation' => 'vac', 'work_mission' => 'work_mission','expenses'     => 'exp'
            ];
            $fileInputName = $fileInputMap[$request_type] ?? null;

             // Check if file is required for sick leave, even if not explicitly checked before
             $isFileRequired = ($request_type === 'vacation' && ($this->input->post('vac[main_type]') ?? null) === 'sick');

             if ($fileInputName && (!empty($_FILES[$fileInputName]['name']['file']) || $isFileRequired))
             {
                if (empty($_FILES[$fileInputName]['name']['file']) || $_FILES[$fileInputName]['error']['file'] !== UPLOAD_ERR_OK) {
                     // If file is required but missing or has error
                     if ($isFileRequired) {
                         $this->session->set_flashdata('error_message', 'مرفق الإجازة المرضية مطلوب.');
                         $data['last_working_day'] = $last_working_day_submit;
                         $this->load->view('templateo/add_new_order', $data);
                         return;
                     }
                     // If optional file has an error (other than no file)
                     elseif ($_FILES[$fileInputName]['error']['file'] !== UPLOAD_ERR_NO_FILE) {
                         $this->session->set_flashdata('error_message', 'حدث خطأ أثناء رفع الملف المرفق.');
                         $data['last_working_day'] = $last_working_day_submit;
                         $this->load->view('templateo/add_new_order', $data);
                         return;
                     }
                     // Else: Optional file just wasn't uploaded, which is fine.
                      log_message('debug', 'Optional file not uploaded or empty for request type: ' . $request_type);

                 } else {
                     // --- File was uploaded, proceed with validation/saving ---
                     $uploadPath = './uploads/orders/';
                     if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, TRUE); }
                     $config['upload_path']   = $uploadPath; $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                     $config['max_size']      = 2048; $config['encrypt_name']  = TRUE;
                     $this->load->library('upload', $config);

                     $_FILES['userfile']['name']     = $_FILES[$fileInputName]['name']['file'];
                     $_FILES['userfile']['type']     = $_FILES[$fileInputName]['type']['file'];
                     $_FILES['userfile']['tmp_name'] = $_FILES[$fileInputName]['tmp_name']['file'];
                     $_FILES['userfile']['error']    = $_FILES[$fileInputName]['error']['file'];
                     $_FILES['userfile']['size']     = $_FILES[$fileInputName]['size']['file'];

                     if ($this->upload->do_upload('userfile')) {
                         $uploadData = $this->upload->data(); $relativePath = 'uploads/orders/' . $uploadData['file_name'];
                         log_message('debug', 'File uploaded successfully: ' . $relativePath);
                     } else {
                         $upload_error = 'File upload failed: ' . $this->upload->display_errors('', '');
                         log_message('error', $upload_error); // Log as error
                         $this->session->set_flashdata('error_message', $upload_error);
                         $data['last_working_day'] = $last_working_day_submit;
                         $this->load->view('templateo/add_new_order', $data); // Reload view
                         return;
                     }
                 } // End else block for file uploaded check
            } else {
                log_message('debug', 'No file input relevant or file not uploaded for request type: ' . $request_type);
            }
            // --- End file upload logic ---
// ✅ NEW CORRECT CODE
if ($request_type == 'overtime') {
    $ot_estimated_amount = 0;
    $ot_data = $this->input->post('ot');

    if (isset($ot_data['paid']) && $ot_data['paid'] == 1 && !empty($ot_data['hours'])) {
        
        // 1. Get the correct Employee ID (From form if HR, otherwise Session)
        $posted_emp_id = $this->input->post('employee_id'); 
        $emp_id = !empty($posted_emp_id) ? $posted_emp_id : $this->session->userdata('username');

        $hours = (float)$ot_data['hours'];

        // 2. Get Salary Details for THAT employee
        $salary_details = $this->hr_model->get_salary_calculation_details($emp_id, 0);

        if (!empty($salary_details['actual_hourly_salary'])) {
            $total_salary = $salary_details['total_salary'] ?? 0;
            $basic_salary = $salary_details['base_salary'] ?? 0;
            
            // 3. Calculate Minutes
            // If hours is "09:50", it might come as a string. ensure format is handled:
            // If your input is "9.83" decimal:
            $overtime_minutes = $hours * 60; 
            // OR if your input is "09:50" string, you might need to convert it. 
            // Assuming your front-end sends decimal hours or you convert it.
            // If the front-end sends "09:50", use this:
            if (strpos($ot_data['hours'], ':') !== false) {
                list($h, $m) = explode(':', $ot_data['hours']);
                $overtime_minutes = ($h * 60) + $m;
            }

            $divisor = 14400; // 30 * 8 * 60

            // 4. Formula A (Total Salary Portion)
            $amount_A = ($total_salary / $divisor) * $overtime_minutes;

            // 5. Formula B (Basic Salary Portion)
            $amount_B = (($basic_salary / 2) / $divisor) * $overtime_minutes;

            // 6. Total
            $ot_estimated_amount = round($amount_A + $amount_B, 2);
        }
    }
    
    // Inject calculated amount into POST so Model saves the CORRECT value
    $_POST['ot_amount_calculated'] = $ot_estimated_amount;
}
            // --- [16] Keep Model Saving Logic ---
            // --- [16] Keep Model Saving Logic ---
            $methodMap = [
                'resign'      => 'add_new_order1', 'fingerprint' => 'add_new_order2', 'overtime' => 'add_new_order3',
                'expenses'    => 'add_new_order4', 'vacation'    => 'add_new_order5', 'asset' => 'add_new_order6',
                'letter'      => 'add_new_order7', 'work_mission' => 'add_new_order9', 
                'permission'  => 'add_new_order12' // <--- ADDED THIS LINE
             ];
            $method = $methodMap[$request_type] ?? null;

            $save_success = false; // Flag to check if saving was successful
            if ($method && method_exists($this->hr_model, $method)) {
                log_message('debug', 'Calling model method: ' . $method);
                // The model method should return true on success, false on failure
                if ($this->hr_model->{$method}($relativePath)) { // Pass file path
     log_message('debug', 'Model method executed successfully');
     $this->session->set_flashdata('success_message', 'تم إرسال الطلب بنجاح.');
     $save_success = true;
     
     // === [NEW] TRIGGER EMAIL TO THE FIRST APPROVER ===
     // We pass the applicant ID, the request type, and the form data (to check for delegation)
     $this->hr_model->trigger_new_request_email($target_employee_id_submit, $request_type, $post_data);
     // =================================================

// =================================================
} else {
                    log_message('error', 'Model method ' . $method . ' returned false.');
                    $this->session->set_flashdata('error_message', 'حدث خطأ أثناء حفظ الطلب في قاعدة البيانات.');
                }
            } else {
                log_message('error', 'Unknown request_type or model method does not exist: ' . $request_type);
                $this->session->set_flashdata('error_message', 'نوع الطلب غير معروف أو غير صحيح.');
            }

            // --- [17] Keep Redirect Logic (Only redirect on successful save) ---
            if ($save_success) {
                if ($this->session->userdata('is_pwa')) {
                    redirect('users2/mobile_dashboard');
                } else {
                    redirect('users1/orders_emp');
                }
            } else {
                 // If saving failed, reload the form with the error message
                 $data['last_working_day'] = $last_working_day_submit; // Pass date back
                 $this->load->view('templateo/add_new_order', $data);
                 return;
            }

        } // End else block for form submission processing

    } 
    
private function _send_json_response($status, $data = [], $message = '') {
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => $status, 'data' => $data, 'message' => $message]));
}
// Add this inside Users1.php class

public function ajax_calculate_ot_hours() {
    if (!$this->session->userdata('logged_in')) exit;

    $emp_id = $this->input->post('emp_id');
    $date_from = $this->input->post('date_from');
    $date_to = $this->input->post('date_to');

    // 1. Get Plan
    $plan_query = $this->db->get_where('work_restrictions', ['emp_id' => $emp_id]);
    $plan = $plan_query->row_array();
    $standard_hours = (float)($plan['working_hours'] ?? 8);
    if ($standard_hours <= 0) $standard_hours = 8;

    $total_ot_decimal = 0;
    $daily_logs = []; 

    $current_date = strtotime($date_from);
    $end_date = strtotime($date_to);

    while ($current_date <= $end_date) {
        $check_date = date('Y-m-d', $current_date);
        
        // --- SATURDAY CHECK ---
        $day_required = $standard_hours; 
        $this->db->where('employee_id', $emp_id);
        $this->db->where('saturday_date', $check_date);
        $assignment = $this->db->get('saturday_work_assignments')->row();
        
        // If assigned Saturday, required hours = 0 (Full OT)
        if ($assignment) { $day_required = 0; }

        // --- ATTENDANCE ---
        $this->db->select_min('punch_time', 'check_in');
        $this->db->select_max('punch_time', 'check_out');
        $this->db->where('emp_code', $emp_id);
        $this->db->where('DATE(punch_time)', $check_date);
        $log = $this->db->get('attendance_logs')->row();

        // Variables for display
        $in_time = '-';
        $out_time = '-';
        $worked_str = '00:00'; // Display string (HH:MM)
        $ot_str = '00:00';     // Display string (HH:MM)
        $status_class = 'table-light text-muted'; 

        if ($log && $log->check_in && $log->check_out && $log->check_in != $log->check_out) {
            $in = strtotime($log->check_in);
            $out = strtotime($log->check_out);
            $in_time = date('H:i', $in);
            $out_time = date('H:i', $out);
            
            // 1. Calculate Exact Worked Time
            $worked_seconds = $out - $in;
            
            // Format Worked Time as HH:MM
            $w_h = floor($worked_seconds / 3600);
            $w_m = floor(($worked_seconds % 3600) / 60);
            $worked_str = sprintf("%02d:%02d", $w_h, $w_m); // e.g. "08:59"

            // 2. Calculate Required Seconds
            $required_seconds = $day_required * 3600;

            // 3. Calculate Overtime
            if ($worked_seconds > $required_seconds) {
                $ot_seconds = $worked_seconds - $required_seconds;
                
                // Format OT as HH:MM
                $ot_h = floor($ot_seconds / 3600);
                $ot_m = floor(($ot_seconds % 3600) / 60);
                $ot_str = sprintf("%02d:%02d", $ot_h, $ot_m); // e.g. "01:30"

                // Add to Total Decimal (for Payroll Amount Calculation)
                // We divide minutes by 60 to get decimal (e.g. 30 min = 0.5)
                $ot_decimal_day = $ot_h + ($ot_m / 60);
                $total_ot_decimal += $ot_decimal_day;
                
                $status_class = 'table-success';
            }
        }

        $daily_logs[] = [
            'date' => $check_date,
            'day_name' => date('D', $current_date),
            'in' => $in_time,
            'out' => $out_time,
            'worked' => $worked_str, // Sends "05:58" instead of 5.97
            'required' => $day_required,
            'ot_display' => $ot_str, // Sends "01:30"
            'class' => $status_class
        ];
        
        $current_date = strtotime('+1 day', $current_date);
    }

    echo json_encode([
        'status' => 'success',
        'hours' => round($total_ot_decimal, 2), // Keeps decimal for the total input (e.g. 1.5)
        'details' => $daily_logs,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
// In application/controllers/Users1.php

public function hr_comprehensive_report()
{
    if (!$this->session->userdata('logged_in')) redirect('users/login');
    
    $this->load->model('hr_model');
    
    // Data for Filters
    $data['departments'] = $this->hr_model->get_distinct_list('emp1', 'n1');
    $data['companies']   = $this->hr_model->get_distinct_list('emp1', 'company_name');
    
    // Data for Top Cards
    $data['stats'] = $this->hr_model->get_dashboard_stats();
    
    //
    $this->load->view('templateo/hr_comprehensive_report_view', $data);
  //  
}
// =============================================================
    // CONTROLLER UPDATES
    // =============================================================

    public function edit_request($request_id)
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }

        $this->load->model('hr_model');
        
        // 1. Fetch Request Data
        $data['request'] = $this->hr_model->get_request_details($request_id); // Assuming this exists from previous steps
        if (empty($data['request'])) { show_404(); }

        // 2. NEW: Fetch Workflow/Approvers Data
        $data['workflow_steps'] = $this->hr_model->get_order_workflow($request_id);

        // 3. Load Dropdowns
        $data['employees'] = $this->hr_model->get_all_employees(); 
        $data['leave_types'] = $this->hr_model->get_leave_types();
        
        // CSRF
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        $this->load->view('templateo/edit_request_view', $data);
    }

    public function update_request_submission()
    {
        if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
        
        $this->load->model('hr_model');
        
        // --- 1. Get POST Data ---
        $id = $this->input->post('request_id');
        $type = $this->input->post('type');
        $emp_id = $this->input->post('emp_id');
        $workflow_updates = $this->input->post('workflow'); // The array of new approvers

        // --- 2. Update Request Details (Standard Data) ---
        $update_data = ['note' => $this->input->post('note')];

        // [Add your Switch Case for specific types here (Vacation, Resignation etc) as provided previously]
        if($type == '5') {
             $vac_start = $this->input->post('vac_start');
             $vac_end   = $this->input->post('vac_end');
             $update_data['vac_main_type'] = $this->input->post('vac_main_type');
             $update_data['vac_start'] = $vac_start;
             $update_data['vac_end'] = $vac_end;
             $update_data['delegation_employee_id'] = $this->input->post('delegation_employee_id');
             
             if($vac_start && $vac_end) {
                 $days = $this->hr_model->calculate_vacation_days($vac_start, $vac_end, $emp_id);
                 $update_data['vac_days_count'] = $days;
             }
        }
        // ... (Add other cases 1, 2, 3, 7 here)

        // Save Main Data
        $this->hr_model->update_order_data($id, $update_data);

        // --- 3. Update Workflow Approvers ---
        if (!empty($workflow_updates)) {
            foreach ($workflow_updates as $wf_id => $new_approver) {
                if (!empty($new_approver)) {
                    // Update the specific workflow row
                    $this->hr_model->update_workflow_approver($wf_id, $new_approver);
                }
            }
        }

        // --- 4. CRITICAL: SYNC RESPONSIBLE EMPLOYEE ---
        // This ensures orders_emp.responsible_employee reflects the new workflow
        $this->hr_model->sync_order_responsibility($id);

        $this->session->set_flashdata('success', 'تم تحديث البيانات وتغيير المسؤولين بنجاح');
        redirect('users1/orders_emp');
    }



    // --- MANDATE PAGES ---

    public function mandate_request() {
    if (!$this->session->userdata('logged_in')) redirect('users/login');

    $data['title'] = 'طلب انتداب جديد';

    // --- HR CUSTOMIZATION START ---
    $current_user = $this->session->userdata('username');
    // List your HR Admin IDs here
    $hr_ids = ['2230', '2515', '2774', '2784', '1835', '2901']; 
    
    $data['is_hr'] = in_array($current_user, $hr_ids);
    $data['departments_list'] = $this->hr_model->get_departments_list();

    // If user is HR, fetch all employees for the dropdown
    if ($data['is_hr']) {
        $this->load->model('hr_model');
        // Ensure this function exists in hr_model (see step 3 below)
        $data['all_employees'] = $this->hr_model->get_all_active_employees();
    }
    // --- HR CUSTOMIZATION END ---

    $this->load->view('templateo/mandate_form', $data); 
}

    public function mandate_settings() {
        // Only for HR/Admin
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        $this->load->model('hr_model');
        $data['policies'] = $this->hr_model->get_policies();
        $this->load->view('templateo/mandate_settings', $data);
    }

    // --- AJAX HANDLERS ---

public function submit_mandate_ajax() {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Session Timeout. Please login again.']);
        return;
    }

    try {
        $this->load->model('hr_model');
        $this->load->database();

        // 2. Setup Upload Path
        if (!is_dir('./uploads/documents/')) {
            mkdir('./uploads/documents/', 0777, true);
        }

        // 3. Gather POST Data
        $mandate_id = $this->input->post('mandate_id');
        $on_behalf_id = $this->input->post('on_behalf_emp_id');
        $target_emp_id = !empty($on_behalf_id) ? $on_behalf_id : $this->session->userdata('username');
        $current_user_id = $this->session->userdata('username');

        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $reason     = $this->input->post('reason');
        $department = $this->input->post('department');

        if(empty($start_date) || empty($end_date)) {
            throw new Exception("تواريخ البداية والنهاية مطلوبة");
        }
        // ==========================================================
        // NEW: OVERLAP / DUPLICATE CHECK
        // ==========================================================
        $has_overlap = $this->hr_model->check_mandate_overlap($target_emp_id, $start_date, $end_date, $mandate_id);
        
        if ($has_overlap) {
            throw new Exception("عفواً، لا يمكن تقديم الطلب. يوجد لديك انتداب سابق (قيد الإجراء أو معتمد) يتقاطع مع نفس التواريخ المحددة.");
        }
        // ==========================================================
        // 4. Prepare Legs
        $from_cities   = $this->input->post('from_city');
        $from_manuals  = $this->input->post('from_city_manual');
        $to_cities     = $this->input->post('to_city');
        $to_manuals    = $this->input->post('to_city_manual');
        $distances     = $this->input->post('dist_km'); 
        $modes         = $this->input->post('leg_mode');

        $legs_data = [];
        if (!empty($from_cities)) {
            for($i=0; $i < count($from_cities); $i++) {
                $final_from = !empty($from_manuals[$i]) ? $from_manuals[$i] : $from_cities[$i];
                $final_to   = !empty($to_manuals[$i])   ? $to_manuals[$i]   : $to_cities[$i];
                $dist_val   = isset($distances[$i]) ? (float)$distances[$i] : 0;
                $mode_val   = isset($modes[$i]) ? $modes[$i] : 'road';

                if(empty($final_from) || empty($final_to)) continue;

                $legs_data[] = ['from' => $final_from, 'to' => $final_to, 'dist' => $dist_val, 'mode' => $mode_val];
            }
        }

        // 5. Calculate Duration & Amounts
        $start_dt = new DateTime($start_date);
        $end_dt   = new DateTime($end_date);
        $days     = $end_dt->diff($start_dt)->days + 1;

        // Get ticket amount from form
        $ticket_amount = (float)$this->input->post('ticket_amount') ?: 0;
        
        // OPTION 1A: Use the model method if it exists
        // $calc = $this->hr_model->calculate_mandate_estimate($target_emp_id, $legs_data, $days);
        
        // OPTION 1B: Or use calculate_submission_totals which should be in controller
        $calc = $this->calculate_submission_totals($target_emp_id, $legs_data, $days, $ticket_amount);
        
        $allowance_amount = $calc['total_allowance'];
        $fuel_amount      = $calc['fuel_cost'];
        $total_km         = $calc['total_km'];
        $transport_mode   = ($ticket_amount > 0) ? 'air' : 'road';
        $grand_total      = $calc['grand_total'];

        // 6. Handle File Uploads
        $attachments = [];
        for ($i = 1; $i <= 3; $i++) {
            $file_name = $this->_upload_file_safe('attachment'.$i);
            if (!empty($file_name)) $attachments['attachment'.$i] = $file_name;
        }

        $this->db->trans_start();

        // =======================================================
        // SCENARIO A: EDITING / RESUBMITTING (Has ID)
        // =======================================================
        if (!empty($mandate_id)) {
            $data = [
                'start_date'       => $start_date,
                'end_date'         => $end_date,
                'duration_days'    => $days,
                'reason'           => $reason,
                'transport_mode'   => $transport_mode,
                'allowance_amount' => $allowance_amount,
                'road_fuel_amount' => $fuel_amount,
                'road_total_km'    => $total_km,
                'ticket_amount'    => $ticket_amount,
                'total_amount'     => $grand_total,
            ];
            
            if(isset($attachments['attachment1'])) $data['attachment1'] = $attachments['attachment1'];
            if(isset($attachments['attachment2'])) $data['attachment2'] = $attachments['attachment2'];
            if(isset($attachments['attachment3'])) $data['attachment3'] = $attachments['attachment3'];

            $this->db->where('id', $mandate_id);
            $this->db->update('mandate_requests', $data);

            // Re-insert Destinations
            $this->db->delete('mandate_destinations', ['request_id' => $mandate_id]);
            $insert_id = $mandate_id;

            if ($current_user_id == '2784') {
                $req = $this->db->get_where('mandate_requests', ['id' => $mandate_id])->row();
                
                if ($req && $req->status == 'Returned' && !empty($req->rejected_from_id)) {
                    $target_approver = $req->rejected_from_id;
                    
                    $this->db->update('mandate_requests', [
                        'current_approver' => $target_approver,
                        'status'           => 'Pending',
                        'rejection_reason' => NULL
                    ], ['id' => $mandate_id]);

                    $this->db->where('order_id', $mandate_id);
                    $this->db->where('order_type', 'Mandate');
                    $this->db->where('approver_id', $target_approver);
                    $this->db->update('approval_workflow', [
                        'status'      => 'Pending',
                        'action_date' => NULL,
                        'notes'       => NULL
                    ]);
                }
            }
            $msg_text = 'تم تحديث الطلب وإعادة إرساله';
        }
        // =======================================================
        // SCENARIO B: NEW REQUEST (No ID)
        // =======================================================
        else {
            $manager_id = $this->hr_model->get_direct_managerssssss($target_emp_id); 
            if (is_array($manager_id)) {
                $manager_id = isset($manager_id['manager_id']) ? $manager_id['manager_id'] : (isset($manager_id['employee_id']) ? $manager_id['employee_id'] : null);
            }
            if (!$manager_id || !is_string($manager_id) || empty($manager_id)) $manager_id = '2774';

            $request_data = [
                'emp_id'           => $target_emp_id,
                'department'       => $department,
                'request_date'     => date('Y-m-d H:i:s'),
                'start_date'       => $start_date,
                'end_date'         => $end_date,
                'duration_days'    => $days,
                'reason'           => $reason,
                'transport_mode'   => $transport_mode,
                'allowance_amount' => $allowance_amount,
                'road_fuel_amount' => $fuel_amount,
                'road_total_km'    => $total_km,
                'ticket_amount'    => $ticket_amount,
                'total_amount'     => $grand_total,
                'status'           => 'Pending',
                'current_approver' => $manager_id,
                'attachment1'      => isset($attachments['attachment1']) ? $attachments['attachment1'] : null,
                'attachment2'      => isset($attachments['attachment2']) ? $attachments['attachment2'] : null,
                'attachment3'      => isset($attachments['attachment3']) ? $attachments['attachment3'] : null
            ];

            $this->db->insert('mandate_requests', $request_data);
            $insert_id = $this->db->insert_id();

            // Workflow
            $this->db->insert('approval_workflow', [
                'order_id'    => $insert_id,
                'order_type'  => 'Mandate',
                'approver_id' => $manager_id,
                'status'      => 'Pending'
            ]);
            $msg_text = 'تم رفع الطلب بنجاح (Request #'.$insert_id.')';
        }

        // Insert Destinations
        foreach($legs_data as $leg) {
            $this->db->insert('mandate_destinations', [
                'request_id'  => $insert_id,
                'from_city'   => $leg['from'],
                'to_city'     => $leg['to'],
                'distance_km' => $leg['dist'],
                'leg_mode'    => $leg['mode']
            ]);
        }

        // Insert Goals
        $this->db->delete('mandate_goals', ['request_id' => $insert_id]);
        $goals = $this->input->post('goals');
        if(!empty($goals)) {
            foreach($goals as $g) {
                if(!empty(trim($g))) {
                    $this->db->insert('mandate_goals', [
                        'request_id' => $insert_id,
                        'goal_text'  => trim($g),
                        'is_achieved'=> 0
                    ]);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception("Database Transaction Failed");
        }

        echo json_encode(['status'=>'success', 'message'=> $msg_text]);

    } catch (Exception $e) {
        echo json_encode(['status'=>'error', 'message'=> $e->getMessage()]);
    }
}
private function calculate_submission_totals($emp_id, $legs_data, $days, $ticket_amount = 0) {
    $total_km = 0;
    $longest_one_way_distance = 0;
    $road_km_total = 0;
    
    if (is_array($legs_data)) {
        foreach ($legs_data as $leg) {
            if (isset($leg['dist']) && $leg['dist'] > 0) {
                $one_way_distance = (float)$leg['dist'];
                $total_km += $one_way_distance;
                $mode = isset($leg['mode']) ? $leg['mode'] : 'road';
                
                // Track the longest leg found in the trip
                if ($one_way_distance > $longest_one_way_distance) {
                    $longest_one_way_distance = $one_way_distance;
                }
                
                if ($mode === 'road') {
                    $road_km_total += $one_way_distance;
                }
            }
        }
    }

    // Get employee's base rate
    $this->db->select('job_tag');
    $this->db->where('employee_id', $emp_id);
    $emp = $this->db->get('emp1')->row();
    
    $job_tag = $emp ? trim($emp->job_tag) : 'Employee';
    $tag_normalized = strtolower(str_replace(' ', '_', $job_tag));
    
    // Determine Base Daily Rate
    $base_rate = 275;
    if (strpos($tag_normalized, 'ceo') !== false) {
        $base_rate = 800;
    } elseif (strpos($tag_normalized, 'project_manager') !== false) {
        $base_rate = 550;
    } elseif (strpos($tag_normalized, 'department_manager') !== false) {
        $base_rate = 450;
    }

    // Determine Multiplier
    $multiplier = 0;
    
    if ($longest_one_way_distance > 250) {
        // If the furthest destination is > 250km, full allowance (x2)
        $multiplier = 2;
    } elseif ($longest_one_way_distance >= 150) {
        // If the furthest destination is between 150km and 250km, half allowance (x1)
        $multiplier = 1;
    } else {
        // If the longest leg is less than 150km, no allowance.
        $multiplier = 0;
    }

    // Calculate totals
    $daily_allowance = $base_rate * $multiplier;
    $total_allowance = $daily_allowance * $days;
    
    // =========================================================
    // NEW CONDITION: Remote Employee (1-Day Mandate)
    // If employee is remote and duration is 1 day, zero the allowance
    // =========================================================
    $is_remote = (strpos($tag_normalized, 'remote') !== false);
    
    if ($is_remote && $days == 1) {
        $daily_allowance = 0;
        $total_allowance = 0;
        $multiplier = 0; // Reset multiplier for consistency
    }
    // =========================================================
    
    // Calculate FUEL cost (70 SAR per 100km for ROAD travel only)
    $fuel_cost = 0;
    if ($road_km_total > 0) {
        $fuel_cost = ($road_km_total / 100) * 70;
        $fuel_cost = round($fuel_cost, 2);
    }
    
    // Calculate grand total
    $grand_total = $total_allowance + $ticket_amount + $fuel_cost;

    return [
        'total_km'        => $total_km,
        'road_km'         => $road_km_total,
        'daily_allowance' => $daily_allowance,
        'total_allowance' => $total_allowance,
        'fuel_cost'       => $fuel_cost,
        'grand_total'     => $grand_total,
        'multiplier'      => $multiplier,
        'base_rate'       => $base_rate,
        'is_remote'       => $is_remote // Added for consistency
    ];
}
// Helper Function for File Uploads
private function _upload_file_safe($field_name) {
    if (empty($_FILES[$field_name]['name'])) return null;

    $config['upload_path']   = './uploads/documents/';
    $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx|xls|xlsx';
    $config['encrypt_name']  = TRUE;
    $config['max_size']      = 10240; // 10MB

    $this->load->library('upload', $config);
    $this->upload->initialize($config);

    if ($this->upload->do_upload($field_name)) {
        return $this->upload->data('file_name');
    }
    return null;
}
    // --- 1. VIEW DETAILS PAGE ---
// In application/controllers/Users1.php
// In application/controllers/Users1.php

public function admin_update_mandate() {
    // 1. Auth Check
    $my_id = $this->session->userdata('username');
    $hr_admins = ['2230', '2515', '2774', '2784', '1835', '2901'];
    
    if (!$this->session->userdata('logged_in') || !in_array($my_id, $hr_admins)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }

    $id = $this->input->post('req_id');
    if(!$id) return;

    $this->load->database();
    $this->db->trans_start();

    // 2. Update Main Request Data
    $data = [
        'start_date'       => $this->input->post('start_date'),
        'end_date'         => $this->input->post('end_date'),
        'duration_days'    => $this->input->post('duration_days'),
        'allowance_amount' => $this->input->post('allowance_amount'),
        'road_fuel_amount' => $this->input->post('road_fuel_amount'),
        'ticket_amount'    => $this->input->post('ticket_amount'),
        'total_amount'     => $this->input->post('total_amount'),
        'status'           => $this->input->post('status'),
        'current_approver' => $this->input->post('current_approver')
    ];
    
    // Recalculate Total Road KM from the legs submitted
    $legs = $this->input->post('legs');
    $total_km = 0;
    
    if (!empty($legs)) {
        // 3. Clear Old Destinations
        $this->db->delete('mandate_destinations', ['request_id' => $id]);

        // 4. Insert New Destinations
        foreach ($legs as $leg) {
            if(!empty($leg['from']) && !empty($leg['to'])) {
                $km = floatval($leg['km']);
                $mode = $leg['mode'];
                
                $this->db->insert('mandate_destinations', [
                    'request_id'  => $id,
                    'from_city'   => $leg['from'],
                    'to_city'     => $leg['to'],
                    'distance_km' => $km,
                    'leg_mode'    => $mode
                ]);

                if($mode == 'road') {
                    $total_km += $km;
                }
            }
        }
        // Update total KM in main record
        $data['road_total_km'] = $total_km;
    }

    $this->db->where('id', $id);
    $this->db->update('mandate_requests', $data);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Database Error']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Updated Successfully']);
    }
}
public function mandate_details($id)
{
    if (!$this->session->userdata('logged_in')) redirect('users/login');

    $this->load->model('hr_model');
    $this->load->database();

    // 1. GET MAIN REQUEST
    $this->db->select('r.*, e.subscriber_name, e.job_tag, e.n1, cur.subscriber_name as current_approver_name');
    $this->db->from('mandate_requests r');
    // Schema Fix: r.emp_id -> e.employee_id
    $this->db->join('emp1 e', 'e.employee_id = r.emp_id', 'left'); 
    $this->db->join('emp1 cur', 'cur.employee_id = r.current_approver', 'left');
    $this->db->where('r.id', $id);
    $req = $this->db->get()->row_array();

    if (!$req) show_404();

    // 2. FETCH HISTORY (Timeline)
    $this->db->select('w.*, e.subscriber_name as approver_name, e.job_tag as approver_role');
    $this->db->from('approval_workflow w');
    $this->db->join('emp1 e', 'e.employee_id = w.approver_id', 'left');
    $this->db->where('w.order_id', $id);
    $this->db->where('w.order_type', 'Mandate');
    $this->db->order_by('w.id', 'ASC');
    $data['timeline'] = $this->db->get()->result_array();

    // 3. FETCH OTHER DATA
    $data['destinations'] = $this->db->get_where('mandate_destinations', ['request_id' => $id])->result_array();
    $data['goals'] = $this->db->get_where('mandate_goals', ['request_id' => $id])->result_array();
    $data['req'] = $req;

    // 4. ADMIN PERMISSIONS
    $my_id = $this->session->userdata('username');
    $data['is_admin'] = in_array($my_id, ['2230', '2515', '2774', '2784', '1835', '2901']);

//   
    $this->load->view('templateo/mandate_details', $data);
 //   
}
// In application/controllers/Users1.php

public function print_mandate($id)
{
    if (!$this->session->userdata('logged_in')) redirect('users/login');

    $this->load->database();

    // 1. Fetch Request + Employee Details
    // REMOVED 'e.emp_code' because it causes an error. 
    // We will use e.employee_id instead.
    $this->db->select('r.*, e.subscriber_name, e.employee_id as emp_code, e.job_tag, e.n1, e.n13');
    $this->db->from('mandate_requests r');
    $this->db->join('emp1 e', 'e.employee_id = r.emp_id', 'left');
    $this->db->where('r.id', $id);
    $data['req'] = $this->db->get()->row_array();

    if (!$data['req']) show_404();

    // 2. Fetch Workflow History
    $this->db->select('w.*, e.subscriber_name as approver_name');
    $this->db->from('approval_workflow w');
    $this->db->join('emp1 e', 'e.employee_id = w.approver_id', 'left');
    $this->db->where('w.order_id', $id);
    $this->db->where('w.order_type', 'Mandate');
    $this->db->order_by('w.id', 'ASC');
    $data['timeline'] = $this->db->get()->result_array();

    // 3. Fetch Destinations
    $data['destinations'] = $this->db->get_where('mandate_destinations', ['request_id' => $id])->result_array();

    $this->load->view('templateo/print_mandate', $data);
}
    public function labor_case_request() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
     //   
        $this->load->view('templateo/labor_case_form');
    //    
    }

    public function submit_labor_case_ajax() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status'=>'error', 'message'=>'Session Expired']); return;
        }

        // 1. File Upload (Evidence is crucial for Labor Cases)
        $uploaded_files = [];
        if (!empty($_FILES['attachments']['name'][0])) {
            $config['upload_path'] = './uploads/documents/';
            if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
            $config['allowed_types'] = 'pdf|jpg|png|doc|docx|mp3|wav'; // Audio allowed for verbal evidence
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);

            $count = count($_FILES['attachments']['name']);
            for ($i = 0; $i < $count; $i++) {
                $_FILES['file']['name'] = $_FILES['attachments']['name'][$i];
                $_FILES['file']['type'] = $_FILES['attachments']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['attachments']['error'][$i];
                $_FILES['file']['size'] = $_FILES['attachments']['size'][$i];

                $this->upload->initialize($config);
                if ($this->upload->do_upload('file')) {
                    $uploaded_files[] = $this->upload->data('file_name');
                }
            }
        }

        // 2. Save Data
        $data = [
            'emp_id' => $this->session->userdata('username'),
            'case_type' => $this->input->post('case_type'),
            'incident_date' => $this->input->post('incident_date'),
            'against_whom' => $this->input->post('against_whom'),
            'description' => $this->input->post('description'),
            'desired_outcome' => $this->input->post('desired_outcome'),
            'attachments' => implode(',', $uploaded_files),
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('labor_case_requests', $data);
        $req_id = $this->db->insert_id();

        // 3. Workflow: Labor cases go directly to HR Specialist (2774), skipping Direct Manager for privacy
        $first_approver = '2774'; 
        
        $this->db->insert('approval_workflow', [
            'order_id' => $req_id, 'order_type' => 'LaborCase',
            'approver_id' => $first_approver, 'approval_level' => 1,
            'status' => 'Pending', 'action_date' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->where('id', $req_id)->update('labor_case_requests', ['current_approver' => $first_approver]);

        echo json_encode(['status'=>'success', 'message'=>'تم رفع القضية العمالية برقم #' . $req_id]);
    }

    // --- APPROVALS DASHBOARD ---
    public function labor_case_approvals() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        $my_id = $this->session->userdata('username');

        // Fetch pending cases
        $this->db->select('labor_case_requests.*, emp1.subscriber_name, emp1.employee_id as emp_code, emp1.n1 as department');
        $this->db->from('approval_workflow');
        $this->db->join('labor_case_requests', 'labor_case_requests.id = approval_workflow.order_id');
        $this->db->join('emp1', 'emp1.employee_id = labor_case_requests.emp_id', 'left');
        $this->db->where('approval_workflow.approver_id', $my_id);
        $this->db->where('approval_workflow.status', 'Pending');
        $this->db->where('approval_workflow.order_type', 'LaborCase');
        
        $data['requests'] = $this->db->get()->result_array();
        
    //    
        $this->load->view('templateo/labor_case_approvals', $data);
    //    
    }
// In your Users1 controller
public function labor_cases_list() {
    if (!$this->session->userdata('logged_in')) redirect('users/login');
    
    $user_id = $this->session->userdata('user_id');
    
    // Check user role/position to determine what to show
    $this->db->select('profession');
    $this->db->where('employee_id', $user_id);
    $user_data = $this->db->get('emp1')->row_array();
    
    // Build query
    $this->db->select('labor_case_requests.*, emp1.subscriber_name');
    $this->db->from('labor_case_requests');
    $this->db->join('emp1', 'emp1.employee_id = labor_case_requests.emp_id', 'left');
    
    // Filter based on user role
    $user_role = $this->session->userdata('role'); // Assuming you have role in session
    if($user_role == 'employee') {
        // Employees can only see their own cases
        $this->db->where('labor_case_requests.emp_id', $user_id);
    } 
    // Add more conditions for HR, managers, etc.
    
    $this->db->order_by('created_at', 'DESC');
    $data['cases'] = $this->db->get()->result_array();
    
    $this->load->view('templateo/labor_cases_list', $data);
}
// In application/controllers/Users1.php

public function get_employee_requests_ajax()
{
    // 1. Security Checks
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        exit('No direct script access allowed');
    }

    $emp_id = $this->input->post('employee_id');
    $this->load->model('hr_model');

    // 2. Fetch Requests
    // We use the existing model function. Passing false as 2nd arg filters by this specific emp_id
    $requests = $this->hr_model->get_orders_emp($emp_id, false);

    $html = '';
    if (!empty($requests)) {
        foreach ($requests as $req) {
            // -- Status Logic --
            $status = '';
            switch ($req['status']) {
                case '0': $status = '<span class="badge bg-warning text-dark">بالانتظار</span>'; break;
                case '1': $status = '<span class="badge bg-info text-dark">جاري المراجعة</span>'; break;
                case '2': $status = '<span class="badge bg-success">معتمد</span>'; break;
                case '3': $status = '<span class="badge bg-danger">مرفوض</span>'; break;
                case '-1': $status = '<span class="badge bg-secondary">ملغى</span>'; break;
                default: $status = $req['status'];
            }

            // -- File Attachment Logic --
            $file_link = '<span class="text-muted">-</span>';
            if(!empty($req['file'])) {
                 $file_link = '<a href="'.base_url($req['file']).'" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa fa-paperclip"></i></a>';
            }

            // -- View Details Button Logic --
            // This links to the view_request page for this specific order
            $view_details_btn = '<a href="'.site_url('users1/view_request/'.$req['id']).'" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-eye"></i> عرض</a>';

            // -- Build Table Row --
            $html .= '<tr>';
            $html .= '<td>' . $req['id'] . '</td>';
            $html .= '<td>' . $req['order_name'] . '</td>';
            $html .= '<td dir="ltr">' . $req['date'] . '</td>';
            $html .= '<td>' . $status . '</td>';
            $html .= '<td>' . $file_link . '</td>';
            $html .= '<td>' . $view_details_btn . '</td>'; // The new button
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="6" class="text-center text-muted p-3">لا توجد طلبات سابقة لهذا الموظف.</td></tr>';
    }

    echo $html;
}
    // --- DETAILS & REPORT GENERATION ---
    public function labor_case_details($id) {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        // Fetch Details
        $this->db->select('labor_case_requests.*, emp1.subscriber_name, emp1.employee_id as emp_code, emp1.n1 as department, emp1.joining_date');
        $this->db->from('labor_case_requests');
        $this->db->join('emp1', 'emp1.employee_id = labor_case_requests.emp_id', 'left');
        $this->db->where('labor_case_requests.id', $id);
        $data['req'] = $this->db->get()->row_array();

        if(!$data['req']) show_404();

        // Fetch Timeline
        $this->db->select('approval_workflow.*, emp1.subscriber_name as approver_name');
        $this->db->from('approval_workflow');
        $this->db->join('emp1', 'emp1.employee_id = approval_workflow.approver_id', 'left');
        $this->db->where('order_id', $id)->where('order_type', 'LaborCase')->order_by('id', 'ASC');
        $data['timeline'] = $this->db->get()->result_array();

     //   
        $this->load->view('templateo/labor_case_details', $data);
    //    
    }
public function print_clearance_form($resignation_id) {
    if (!$this->session->userdata('logged_in')) redirect('users/login');
    
    $this->load->model('hr_model'); 
    
    // 1. Fetch Employee & Resignation Info (CORRECTED TABLE: orders_emp)
    $this->db->select('
        r.id, 
        r.emp_id, 
        r.date_of_the_last_working, 
        e.subscriber_name as emp_name, 
        e.employee_id as emp_code, 
        e.job_tag as job_title, 
        e.n1 as department_name
    ');
    $this->db->from('orders_emp r'); // Correct Table Name
    $this->db->join('emp1 e', 'e.employee_id = r.emp_id', 'left');
    $this->db->where('r.id', $resignation_id);
    
    // Optional: Filter only resignation types if needed
    // $this->db->where('r.type', 'Resignation'); 
    
    $data['info'] = $this->db->get()->row_array();

    if (!$data['info']) {
        show_error('Resignation request not found or invalid ID.', 404);
    }

    // 2. Fetch Clearance Items & Approvals
    // Joining clearances with parameters to get task names and departments
    $this->db->select('
        rc.status, rc.updated_at, rc.approver_user_id,
        cp.parameter_name, cp.department_id,
        e_app.subscriber_name as approver_name
    ');
    $this->db->from('resignation_clearances rc');
    $this->db->join('clearance_parameters cp', 'cp.id = rc.clearance_parameter_id');
    $this->db->join('emp1 e_app', 'e_app.id = rc.approver_user_id', 'left'); // Get approver name
    $this->db->where('rc.resignation_request_id', $resignation_id);
    $query = $this->db->get()->result_array();

    // 3. Group by Department
    $data['departments'] = [];
    foreach ($query as $row) {
        $dept_id = $row['department_id']; 
        $data['departments'][$dept_id]['tasks'][] = $row;
        
        // Store the last approver info for the footer signature
        if ($row['status'] == 'approved') {
            $data['departments'][$dept_id]['approver'] = $row['approver_name'];
            $data['departments'][$dept_id]['date'] = $row['updated_at'];
        }
    }

    $this->load->view('templateo/print_clearance_form', $data);
}
// In application/controllers/Users1.php

public function send_experience_certificate() {
    // 1. Get the Employee ID (assuming it is passed via POST or URI)
    // Adjust this line if you get the ID from the URL (e.g. $this->uri->segment(3))
    $id = $this->input->post('employee_id') ? $this->input->post('employee_id') : $this->uri->segment(3);

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID is missing']);
        return;
    }

    // 2. Load Database & Fetch Data (Using the CORRECT column 'employee_id')
    $this->load->database();
    
    // We select specific fields to avoid fetching too much data
    $this->db->select('subscriber_name as full_name, subscriber_name as full_name_ar, joining_date as join_date, job_tag as job_title, email, personal_email');
    $this->db->from('emp1');
    $this->db->where('employee_id', $id); // FIX: Changed 'employee_code' to 'employee_id'
    $query = $this->db->get();
    $user_data = $query->row_array();

    // 3. Check if user exists
    if (empty($user_data)) {
        show_error("Employee not found in database with ID: $id", 404);
        return;
    }

    // 4. Prepare Email Data
    $data['full_name']    = $user_data['full_name'];
    $data['full_name_ar'] = $user_data['full_name_ar']; // Ensure this column exists or map correctly
    $data['join_date']    = $user_data['join_date'];
    $data['job_title']    = $user_data['job_title'];

    // Determine Recipient (Prefer Personal, Fallback to Work)
    $recipient_email = !empty($user_data['personal_email']) ? $user_data['personal_email'] : $user_data['email'];

    if (empty($recipient_email)) {
        echo json_encode(['status' => 'error', 'message' => 'No email found for this employee.']);
        return;
    }

    // 5. Generate PDF/HTML Content (Assuming you have a view for this)
    $email_body = $this->load->view('templateo/certificate_view', $data, TRUE); 
    // ^ NOTE: Make sure 'certificate_view' is the correct name of your certificate HTML file

    // 6. Configure Email (EXACTLY like the working function) 
    $this->load->library('email');
    
    $config = array();
    $config['protocol']    = 'smtp';
    $config['smtp_host']   = 'MAR-PRD-EXH01.MARSOOM.NET';
    $config['smtp_user']   = 'itsystem@marsoom.net';
    $config['smtp_pass']   = 'Asd@123123';
    $config['smtp_port']   = 587;
    $config['smtp_crypto'] = 'tls';
    $config['mailtype']    = 'html';
    $config['charset']     = 'utf-8';
    $config['newline']  = "\r\n";
    $config['crlf']     = "\r\n";
    $config['wordwrap']    = TRUE;
    $config['smtp_timeout'] = 20;

    $this->email->initialize($config);

    // FIX: Using the 'From' address that works in your clearance function
    $this->email->from('IT.systems@marsoom.net', 'Marsoom HR System');
    $this->email->to($recipient_email);
    $this->email->subject('Experience Certificate - شهادة خبرة');
    $this->email->message($email_body);

    // 7. Send
    if ($this->email->send()) {
        echo json_encode(['status' => 'success', 'message' => 'Email sent successfully to ' . $recipient_email]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send: ' . $this->email->print_debugger()]);
    }
}
// --- GENERAL EMPLOYEE SURVEY ---
    public function employee_survey() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        // Optional: Check if they filled it this month/year already to prevent spam
        $emp_id = $this->session->userdata('username');
        $current_month = date('Y-m');
        
        $this->db->like('created_at', $current_month);
        $exists = $this->db->get_where('general_surveys', ['emp_id' => $emp_id])->row();
        
        $data['already_submitted'] = $exists ? true : false;

     //   
        $this->load->view('templateo/general_survey', $data);
     //   
    }
// --- HAPPINESS INDEX ---
    public function happiness_index() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        // Prevent spamming (e.g., once a month)
        $emp_id = $this->session->userdata('username');
        $this->db->like('created_at', date('Y-m')); // Current Month
        $exists = $this->db->get_where('happiness_surveys', ['emp_id' => $emp_id])->row();
        
        $data['already_done'] = $exists ? true : false;
        
   //     
        $this->load->view('templateo/happiness_index', $data);
   //     
    }

    public function submit_happiness_ajax() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status'=>'error', 'message'=>'Session Expired']); return;
        }

        // 1. Gather Scores (1-5 Scale)
        $scores = [
            'q_comfort'      => $this->input->post('q_comfort'),
            'q_tools'        => $this->input->post('q_tools'),
            'q_atmosphere'   => $this->input->post('q_atmosphere'),
            'q_support'      => $this->input->post('q_support'),
            'q_recognition'  => $this->input->post('q_recognition'),
            'q_transparency' => $this->input->post('q_transparency'),
            'q_stress'       => $this->input->post('q_stress'),
            'q_balance'      => $this->input->post('q_balance'),
            'q_safety'       => $this->input->post('q_safety'),
            'q_learning'     => $this->input->post('q_learning'),
            'q_purpose'      => $this->input->post('q_purpose'),
            'q_pride'        => $this->input->post('q_pride'),
        ];

        // 2. Calculate Index (0 - 100)
        // Max possible score = 12 questions * 5 points = 60
        $sum = array_sum($scores);
        $total_index = ($sum / 60) * 100;
        
        // Determine Mood Label
        $mood = 'Neutral';
        if($total_index >= 85) $mood = 'Thriving (مبتهج)';
        elseif($total_index >= 70) $mood = 'Happy (سعيد)';
        elseif($total_index >= 50) $mood = 'Content (راضٍ)';
        elseif($total_index >= 30) $mood = 'Struggling (يعاني)';
        else $mood = 'Unhappy (غير سعيد)';

        // 3. Save
        $data = array_merge($scores, [
            'emp_id' => $this->session->userdata('username'),
            'total_score' => $total_index,
            'mood_text' => $mood,
            'feedback_text' => $this->input->post('feedback_text'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->insert('happiness_surveys', $data);
        
        echo json_encode([
            'status' => 'success', 
            'score' => number_format($total_index, 1),
            'message' => 'تم حفظ مؤشر السعادة بنجاح!'
        ]);
    }
    public function submit_general_survey_ajax() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status'=>'error', 'message'=>'Session Expired']); return;
        }

        $data = [
            'emp_id' => $this->session->userdata('username'),
            'job_satisfaction' => $this->input->post('job_satisfaction'),
            'work_life_balance' => $this->input->post('work_life_balance'),
            'management_rating' => $this->input->post('management_rating'),
            'communication_rating' => $this->input->post('communication_rating'),
            'nps_score' => $this->input->post('nps_score'),
            'most_valued' => $this->input->post('most_valued'),
            'suggestions' => $this->input->post('suggestions'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('general_surveys', $data);
        echo json_encode(['status'=>'success', 'message'=>'شكراً لمشاركتك! تم حفظ الاستبيان.']);
    }
    // --- ACTION HANDLER ---
    public function do_labor_case_action() {
        $my_id = $this->session->userdata('username');
        $req_id = $this->input->post('req_id');
        $action = $this->input->post('action'); // approve, reject, escalate
        $notes = $this->input->post('notes');

        // Update current workflow
        $this->db->where(['order_id'=>$req_id, 'approver_id'=>$my_id, 'order_type'=>'LaborCase'])
                 ->update('approval_workflow', ['status'=>ucfirst($action), 'action_date'=>date('Y-m-d H:i:s'), 'rejection_reason'=>$notes]);

        // Save Notes to main table
        if($my_id == '2774') { // HR
            $this->db->where('id', $req_id)->update('labor_case_requests', ['hr_notes' => $notes]);
        } elseif ($my_id == '2230') { // Legal/Finance
            $this->db->where('id', $req_id)->update('labor_case_requests', ['legal_notes' => $notes, 'final_verdict' => $action]);
        }

        if ($action == 'approve') {
            // HR Approved -> Send to Legal (1693)
            if ($my_id == '2774') {
                $this->db->insert('approval_workflow', [
                    'order_id' => $req_id, 'order_type' => 'LaborCase',
                    'approver_id' => '1693', 'approval_level' => 2,
                    'status' => 'Pending', 'action_date' => date('Y-m-d H:i:s')
                ]);
                $this->db->where('id', $req_id)->update('labor_case_requests', ['current_approver' => '1693']);
            } else {
                // Final Approval
                $this->db->where('id', $req_id)->update('labor_case_requests', ['status' => 'Closed', 'current_approver' => 'Completed']);
            }
        } elseif ($action == 'reject') {
            $this->db->where('id', $req_id)->update('labor_case_requests', ['status' => 'Rejected', 'current_approver' => 'Rejected']);
        }

        echo json_encode(['status'=>'success']);
    }
    public function new_insurance_request() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
    //    
        $this->load->view('templateo/insurance_form'); // The Attractive View
   //     
    }
public function modify_staff_record($id) {
        // التحقق من تسجيل الدخول
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }
        $allowed_users = array('1835', '2230', '2515', '2774', '2784','2901','1127');
        $this->load->model('hr_model');

        // التحقق من إرسال الفورم
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            // ربط الحقول
            $update_data = [
                'employee_id'       => $this->input->post('employee_id'),
                'id_number'         => $this->input->post('id_number'),
                'email'             => $this->input->post('email'),
                'personal_email'    => $this->input->post('personal_email'),
                'marital'           => $this->input->post('marital'),
                'phone'             => $this->input->post('phone'),
                'religion'          => $this->input->post('religion'),
                'id_expiry'         => $this->input->post('id_expiry'),
                'address'           => $this->input->post('address'),
                'subscriber_name'   => $this->input->post('subscriber_name'),
                'nationality'       => $this->input->post('nationality'),
                'gender'            => $this->input->post('gender'),
                'birth_date'        => $this->input->post('birth_date'),
                'manager'           => $this->input->post('manager'),
                'Iqama_expiry_date' => $this->input->post('Iqama_expiry_date'),
                'joining_date'      => $this->input->post('joining_date'),
                'n2'      => $this->input->post('n2'),
                'n3'      => $this->input->post('n3'),
                'profession'        => $this->input->post('profession')
            ];

            // تنفيذ التحديث باستخدام اسم الدالة الجديد في الموديل
            $this->hr_model->save_staff_modifications($id, $update_data);
            
            $this->session->set_flashdata('success', 'تم تحديث بيانات الموظف بنجاح!');
            // إعادة التوجيه لاسم الدالة الجديد
            redirect('users1/modify_staff_record/' . $id);
        }

        // جلب البيانات باستخدام اسم الدالة الجديد في الموديل
        $data['employee'] = $this->hr_model->fetch_staff_details($id);
        
   //     
        // تأكد من أن اسم ملف العرض (View) مطابق لاسم الملف الذي أنشأته
        $this->load->view('templateo/modify_staff_record', $data); 
    //    
    }
    /**
     * عرض قائمة الموظفين للبحث والتعديل
     */
    public function manage_employees_list() {
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }

        $this->load->model('hr_model');
        
        // جلب جميع الموظفين من الموديل
        $data['employees'] = $this->hr_model->get_all_employees_for_list();
        
      //  
        $this->load->view('templateo/manage_employees_list', $data);
     //   
    }
    // In application/controllers/Users1.php
public function submit_insurance_ajax() {
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status'=>'error', 'message'=>'Session timeout']); 
        return;
    }

    $emp_id = $this->session->userdata('username');
    $type = $this->input->post('request_type');

    // --- 2. HANDLE FILE UPLOADS ---
    $uploaded_files = [];
    $upload_error = '';

    if (!empty($_FILES['attachments']['name'][0])) {
        $filesCount = count($_FILES['attachments']['name']);
        $uploadPath = './uploads/insurance/'; 
        
        // Ensure folder exists with permissions
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        $config['upload_path']   = $uploadPath;
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['max_size']      = 5120; // 5MB
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        for ($i = 0; $i < $filesCount; $i++) {
            $_FILES['file']['name']     = $_FILES['attachments']['name'][$i];
            $_FILES['file']['type']     = $_FILES['attachments']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i];
            $_FILES['file']['error']    = $_FILES['attachments']['error'][$i];
            $_FILES['file']['size']     = $_FILES['attachments']['size'][$i];

            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
                $fileData = $this->upload->data();
                $uploaded_files[] = $fileData['file_name'];
            } else {
                $upload_error = $this->upload->display_errors('', '');
            }
        }
    }

    // Stop if there was an error and no files were uploaded
    if (!empty($upload_error) && empty($uploaded_files)) {
        echo json_encode(['status'=>'error', 'message'=>'فشل رفع الملفات: ' . $upload_error]); 
        return;
    }

    $attachments_str = !empty($uploaded_files) ? implode(',', $uploaded_files) : null;

    // --- 3. PREPARE MAIN REQUEST DATA ---
    $data = [
        'emp_id'           => $emp_id,
        'request_type'     => $type,
        'reason'           => $this->input->post('reason'),
        'attachments'      => $attachments_str,
        'status'           => '0',      // 0 = Pending
        'current_approver' => '2784',   // Initial Approver (HR Specialist)
        'created_at'       => date('Y-m-d H:i:s')
    ];

    $this->load->model('hr_model');
    
    // --- 4. PREPARE FAMILY DATA ---
    $family_data = [];
    if($type == 'family') {
        $fam_names    = $this->input->post('fam_name');
        $fam_names_en = $this->input->post('fam_name_en');
        $fam_rels     = $this->input->post('fam_rel');
        $fam_dobs     = $this->input->post('fam_dob');
        $fam_nids     = $this->input->post('fam_nid');

        if (!empty($fam_names)) {
            for ($i = 0; $i < count($fam_names); $i++) {
                if (!empty($fam_names[$i])) {
                    
                    // CALCULATE AGE
                    $age = 0;
                    if(!empty($fam_dobs[$i])) {
                        $dob_date = new DateTime($fam_dobs[$i]);
                        $today    = new DateTime();
                        $age      = $today->diff($dob_date)->y;
                    }

                    $family_data[] = [
                        'full_name'    => $fam_names[$i],
                        'full_name_en' => $fam_names_en[$i],
                        'relationship' => $fam_rels[$i],
                        'dob'          => $fam_dobs[$i],
                        'age'          => $age,             // Added Age Calculation
                        'national_id'  => $fam_nids[$i]
                        // 'request_id' is NOT added here; the Model does it.
                    ];
                }
            }
        }
    }

    // --- 5. CALL MODEL ---
    // Make sure your model function looks like the one I provided previously
    $req_id = $this->hr_model->create_insurance_request($data, $family_data);

    if ($req_id) {
        // --- 6. START WORKFLOW (Level 1: 2784) ---
        $first_approver = '2784';

        $this->db->insert('approval_workflow', [
            'order_id'       => $req_id, 
            'order_type'     => 'Insurance',
            'approver_id'    => $first_approver, 
            'approval_level' => 1, 
            'status'         => 'Pending', 
            'action_date'    => date('Y-m-d H:i:s')
        ]);
        
        echo json_encode(['status'=>'success', 'message'=>'تم رفع طلب التأمين بنجاح']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'فشل حفظ البيانات في قاعدة البيانات']);
    }
}
public function insurance_details($req_id) {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        $this->load->model('hr_model');
        
        // Fetch Main Request + Employee Info
        $this->db->select('
            insurance_requests.*, 
            emp1.subscriber_name, 
            emp1.employee_id as emp_code, 
            emp1.n1 as department, 
            emp1.job_tag as job_title
        ');
        $this->db->from('insurance_requests');
        $this->db->join('emp1', 'emp1.employee_id = insurance_requests.emp_id', 'left');
        $this->db->where('insurance_requests.id', $req_id);
        $data['req'] = $this->db->get()->row_array();

        if(!$data['req']) show_404();

        // Fetch Family Members
        $data['family_members'] = $this->db->get_where('insurance_family_members', ['request_id' => $req_id])->result_array();

        // Fetch Workflow Timeline
        $this->db->select('approval_workflow.*, emp1.subscriber_name as approver_name');
        $this->db->from('approval_workflow');
        $this->db->join('emp1', 'emp1.employee_id = approval_workflow.approver_id', 'left');
        $this->db->where('order_id', $req_id);
        $this->db->where('order_type', 'Insurance');
        $this->db->order_by('id', 'ASC');
        $data['timeline'] = $this->db->get()->result_array();

        $data['title'] = 'تفاصيل طلب التأمين';
    //    
        $this->load->view('templateo/insurance_details', $data);
    //    
    }
    // --- APPROVALS PAGE ---
    public function insurance_approvals() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        $my_id = $this->session->userdata('username');

        $this->db->select('
            insurance_requests.*, 
            approval_workflow.approval_level, 
            emp1.subscriber_name, 
            emp1.employee_id as emp_code,
            emp1.n1 as department
        ');
        $this->db->from('approval_workflow');
        $this->db->join('insurance_requests', 'insurance_requests.id = approval_workflow.order_id');
        $this->db->join('emp1', 'emp1.employee_id = insurance_requests.emp_id', 'left');
        
        $this->db->where('approval_workflow.approver_id', $my_id);
        $this->db->where('approval_workflow.status', 'Pending');
        $this->db->where('approval_workflow.order_type', 'Insurance');
        
        $data['requests'] = $this->db->get()->result_array();
        
        $data['title'] = 'اعتماد التأمين الطبي';
     //   
        $this->load->view('templateo/insurance_approvals', $data);
      //  
    }

    // --- PROCESS APPROVAL (The Workflow Logic) ---
    public function do_insurance_approval() {
    $my_id = $this->session->userdata('username');
    $req_id = $this->input->post('req_id');
    $action = $this->input->post('action'); // approve/reject
    $level = $this->input->post('level'); // Sent from the view (1 or 2)

    $this->db->trans_start();

    // 1. Update My Status in Workflow Table
    $this->db->where([
        'order_id'    => $req_id, 
        'approver_id' => $my_id, 
        'order_type'  => 'Insurance'
    ])->update('approval_workflow', [
        'status'      => ucfirst($action), 
        'action_date' => date('Y-m-d H:i:s')
    ]);

    if($action == 'reject') {
        // Stop workflow immediately
        $this->db->where('id', $req_id)->update('orders.insurance_requests', [
            'status' => 'Rejected', 
            'current_approver' => 'Rejected by ' . $my_id
        ]);
    } else {
        // 2. Move to Next Level
        $next_approver = null;
        $next_level = 0;

        if($level == 1) {
            // HR Specialist (2784) Done -> Send to Final Manager (2200)
            $next_approver = '2200'; 
            $next_level = 2;
            
            // Update main status to Processing
            $this->db->where('id', $req_id)->update('orders.insurance_requests', ['status' => 'Processing']);
        } 
        elseif($level == 2) {
            // Final Manager (2200) Done -> Workflow Complete
            $this->db->where('id', $req_id)->update('orders.insurance_requests', [
                'status' => 'Approved', 
                'current_approver' => 'Completed'
            ]);
        }

        // 3. If there is a next approver, insert into workflow
        if($next_approver) {
            $this->db->insert('approval_workflow', [
                'order_id'       => $req_id, 
                'order_type'     => 'Insurance',
                'approver_id'    => $next_approver, 
                'approval_level' => $next_level,
                'status'         => 'Pending', 
                'action_date'    => date('Y-m-d H:i:s')
            ]);
            
            $this->db->where('id', $req_id)->update('orders.insurance_requests', [
                'current_approver' => $next_approver
            ]);
        }
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status'=>'error', 'message'=>'Database Error']);
    } else {
        echo json_encode(['status'=>'success']);
    }
}
    public function update_mandate_goal() {
        $goal_id = $this->input->post('goal_id');
        $status  = $this->input->post('is_achieved'); // 1 or 0
        
        $this->db->where('id', $goal_id);
        $this->db->update('mandate_goals', ['is_achieved' => $status]);
        
        echo json_encode(['status' => 'success']);
    }
// In Users1.php

    // In application/controllers/Users1.php

// In application/controllers/Users1.php

public function check_policy_ajax() {
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Session Timeout']);
        return;
    }

    $emp_id = $this->input->post('on_behalf_emp_id') ?: $this->session->userdata('username');
    $days = (int)$this->input->post('days');
    
    if ($days < 1) {
        $days = 1;
    }

    $ticket_amount = (float)$this->input->post('ticket_amount') ?: 0;
    $total_km = 0;
    $legs = $this->input->post('legs');
    $legs_data = [];
    
    // Check ONE-WAY distances
    $longest_one_way_distance = 0;
    $road_km_total = 0;
    $air_km_total = 0;
    
    if (is_array($legs)) {
        foreach ($legs as $leg) {
            if (isset($leg['dist']) && $leg['dist'] > 0) {
                $one_way_distance = (float)$leg['dist'];
                $total_km += $one_way_distance;
                $mode = isset($leg['mode']) ? $leg['mode'] : 'road';
                
                // Track the longest one-way distance (for multiplier)
                if ($one_way_distance > $longest_one_way_distance) {
                    $longest_one_way_distance = $one_way_distance;
                }
                
                // Track distance by mode for fuel calculation
                if ($mode === 'road') {
                    $road_km_total += $one_way_distance;
                } else {
                    $air_km_total += $one_way_distance;
                }
                
                $legs_data[] = [
                    'from' => isset($leg['from']) ? $leg['from'] : '',
                    'to' => isset($leg['to']) ? $leg['to'] : '',
                    'km' => $leg['dist'],
                    'mode' => $mode
                ];
            }
        }
    }

    // Get employee's base rate
    $this->db->select('job_tag');
    $this->db->where('employee_id', $emp_id);
    $emp = $this->db->get('emp1')->row();
    
    $job_tag = $emp ? trim($emp->job_tag) : 'Employee';
    $tag_normalized = strtolower(str_replace(' ', '_', $job_tag));
    
    // Determine Base Daily Rate
    $base_rate = 275;
    if (strpos($tag_normalized, 'ceo') !== false) {
        $base_rate = 800;
    } elseif (strpos($tag_normalized, 'project_manager') !== false) {
        $base_rate = 550;
    } elseif (strpos($tag_normalized, 'department_manager') !== false) {
        $base_rate = 450;
    }

    // Determine Multiplier - BASED ON LONGEST ONE-WAY DISTANCE ONLY
    $multiplier = 0;
    $policy_note = "";
    
    // FIXED LOGIC: Base the multiplier strictly on the longest leg of the journey.
    if ($longest_one_way_distance > 250) {
        $multiplier = 2;
        $policy_note = "مسافة الذهاب $longest_one_way_distance كم (أكثر من 250 كم): ضعف الانتداب (x2)";
    } elseif ($longest_one_way_distance >= 150) {
        $multiplier = 1;
        $policy_note = "مسافة الذهاب $longest_one_way_distance كم (150-250 كم): انتداب يوم واحد (x1)";
    } else {
        // Only if the longest leg of the ENTIRE trip is < 150km do we give 0
        $multiplier = 0;
        $policy_note = "المسافة الأساسية أقل من 150 كم: لا يوجد بدل انتداب";
    }

    // Calculate totals
    $daily_allowance = $base_rate * $multiplier;
    $total_allowance = $daily_allowance * $days;
    
    // =========================================================
    // NEW CONDITION: Remote Employee (1-Day Mandate)
    // If employee is remote and duration is 1 day, zero the allowance
    // =========================================================
    if ($tag_normalized === 'remote' && $days == 1) {
        $daily_allowance = 0;
        $total_allowance = 0;
        $multiplier = 0; // Resetting multiplier for UI clarity
        $policy_note = "موظف عن بعد: لا يتم احتساب بدل انتداب لمهام العمل التي تستغرق يوماً واحداً، يقتصر التعويض على الوقود والتذاكر.";
    }
    // =========================================================
    
    // Calculate FUEL cost (70 SAR per 100km for ROAD travel only)
    $fuel_cost = 0;
    if ($road_km_total > 0) {
        $fuel_cost = ($road_km_total / 100) * 70;
        $fuel_cost = round($fuel_cost, 2);
    }
    
    // Calculate grand total
    $grand_total = $total_allowance + $ticket_amount + $fuel_cost;

    $response = [
        'status'          => 'success',
        'base_rate'       => $base_rate,
        'days'            => $days,
        'multiplier'      => $multiplier,
        'daily_allowance' => number_format($daily_allowance, 2, '.', ''), // Removed commas for safer parsing
        'total_allowance' => number_format($total_allowance, 2, '.', ''),
        'ticket_amount'   => number_format($ticket_amount, 2, '.', ''),
        'fuel_cost'       => number_format($fuel_cost, 2, '.', ''),
        'grand_total'     => number_format($grand_total, 2, '.', ''),
        'breakdown'       => $legs_data,
        'total_km'        => $total_km,
        'road_km'         => $road_km_total,
        'one_way_distance' => $longest_one_way_distance,
        'policy_note'     => $policy_note,
        'is_remote'       => ($tag_normalized === 'remote'), // Optional helpful flag for the frontend
        'csrf_hash'       => $this->security->get_csrf_hash()
    ];

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}
// In application/controllers/Users1.php

// 1. View to Submit a Note (HR Only)
public function add_violation_note()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $current_user = $this->session->userdata('username');
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];

    // Strict HR Check
    if (!in_array($current_user, $hr_users)) {
        show_error('ليس لديك صلاحية للوصول لهذه الصفحة', 403);
    }

    $this->load->model('hr_model');
    // Fetch all employees for the search dropdown
    $data['employees'] = $this->hr_model->get_all_employees_simple(); 
    $data['page_title'] = 'تسجيل مخالفة / ملاحظة إدارية';

    $this->load->view('templateo/add_violation_view', $data);
}

// 2. Action to Save the Note
public function submit_violation_ajax()
{
    if (!$this->session->userdata('logged_in')) return;
    
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        echo json_encode(['status'=>'error', 'message'=>'Unauthorized']); return;
    }

    $this->load->model('hr_model');

    // Get Employee Name based on ID selected
    $emp_id = $this->input->post('employee_id');
    $emp_details = $this->hr_model->get_employee_name_by_id($emp_id);

    $data = [
        'employee_id' => $emp_id,
        'emp_name'    => $emp_details ? $emp_details['subscriber_name'] : '',
        'department'  => $this->input->post('department'),
        'amount'      => $this->input->post('amount'),
        'supervisor_name' => $this->input->post('supervisor_name'),
        'violation_date'  => $this->input->post('violation_date'),
        'hr_note'     => $this->input->post('hr_note'),
        'created_by'  => $this->session->userdata('username'),
        'created_at'  => date('Y-m-d H:i:s')
    ];

    $insert_id = $this->hr_model->insert_violation($data);

    if($insert_id) {
        echo json_encode(['status'=>'success', 'message'=>'تم حفظ الملاحظة بنجاح']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'حدث خطأ أثناء الحفظ']);
    }
}
// In application/controllers/Users1.php

public function submit_violation_feedback()
{
    // 1. Check Login
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status'=>'error', 'message'=>'Session timeout']); 
        return;
    }

    $id = $this->input->post('violation_id');
    $feedback = $this->input->post('employee_feedback');
    $current_user = $this->session->userdata('username');

    // 2. Security: Ensure the user submitting is actually the employee on the violation
    // (Optional but recommended security step)
    $this->load->model('hr_model');
    // You could fetch the violation first to check permissions, 
    // but for now, we will trust the ID and proceed.

    // 3. Update Database
    $result = $this->hr_model->update_violation_feedback($id, $feedback);

    if($result) {
        echo json_encode(['status'=>'success', 'message'=>'تم إرسال ردك بنجاح']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'حدث خطأ أثناء الحفظ']);
    }
}
// 3. View List (HR sees all, Employee sees own)
public function violations_list()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $current_user = $this->session->userdata('username');
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    $is_hr = in_array($current_user, $hr_users);

    $this->load->model('hr_model');
    $data['violations'] = $this->hr_model->get_violations_list($current_user, $is_hr);
    $data['is_hr'] = $is_hr;
    $data['current_user'] = $current_user;
    $data['page_title'] = 'سجل الملاحظات والمخالفات';

    $this->load->view('templateo/violation_list_view', $data);
}

// 4. Action for Employee Feedback

public function my_mandates() {
    if (!$this->session->userdata('logged_in')) redirect('users/login');

    $emp_id = $this->session->userdata('username');
    // HR Users List
    $hr_users = ['2230', '2515', '2774', '2784', '1835', '2901'];
    $is_hr = in_array($emp_id, $hr_users);

    // 1. Get Filter Inputs (XSS Cleaned)
    $filter_emp_code = $this->input->get('emp_code', TRUE);
    $filter_emp_name = $this->input->get('emp_name', TRUE);
    $filter_date_from = $this->input->get('date_from', TRUE);
    $filter_date_to = $this->input->get('date_to', TRUE);
    $filter_status = $this->input->get('status', TRUE);
    
    // --- NEW: Current Approver Filter ---
    $filter_approver_role = $this->input->get('approver_role', TRUE); 

    $this->db->select('
        mandate_requests.*, 
        emp1.subscriber_name as emp_name, 
        emp1.employee_id as emp_code, 
        (SELECT GROUP_CONCAT(CONCAT(from_city, " ➝ ", to_city) SEPARATOR " | ") FROM mandate_destinations WHERE request_id = mandate_requests.id) as itinerary, 
        (SELECT GROUP_CONCAT(goal_text SEPARATOR " • ") FROM mandate_goals WHERE request_id = mandate_requests.id) as goals_summary
    ');
    $this->db->from('mandate_requests');
    // Join on employee_id
    $this->db->join('emp1', 'emp1.employee_id = mandate_requests.emp_id', 'left');

    // ---------------------------------------------------------
    // 2. Apply Filters
    // ---------------------------------------------------------
    // A. Role Based Logic
    if ($is_hr) {
        // HR can filter by specific Employee ID or Name
        if (!empty($filter_emp_code)) {
            $this->db->where('emp1.employee_id', $filter_emp_code);
        }
        if (!empty($filter_emp_name)) {
            $this->db->like('emp1.subscriber_name', $filter_emp_name);
        }
    } else {
        // Non-HR: RESTRICT to their own data only
        $this->db->where('mandate_requests.emp_id', $emp_id);
    }
    
    // B. Date Range Filters (Applies to everyone)
    if (!empty($filter_date_from)) {
        $this->db->where('mandate_requests.start_date >=', $filter_date_from);
    }
    if (!empty($filter_date_to)) {
        $this->db->where('mandate_requests.start_date <=', $filter_date_to);
    }
    // C. Status Filter (Applies to everyone)
    if (!empty($filter_status)) {
        $this->db->where('mandate_requests.status', $filter_status);
    }
    
    // --- NEW: D. Approver Role Filter ---
    if (!empty($filter_approver_role)) {
        if ($filter_approver_role == 'manager') {
            // Exclude known HR, Finance, and Completed IDs. The rest are direct managers.
            $this->db->where_not_in('mandate_requests.current_approver', ['2784', '2833', '2909', '1693', '1936', 'Completed']);
            $this->db->where('mandate_requests.current_approver IS NOT NULL');
            $this->db->where('mandate_requests.current_approver !=', '');
            $this->db->where('mandate_requests.status !=', 'Approved'); // Managers only hold pending requests
        } elseif ($filter_approver_role == 'hr') {
            $this->db->where_in('mandate_requests.current_approver', ['2784', '2833', '2909']);
        } elseif ($filter_approver_role == 'finance') {
            $this->db->where_in('mandate_requests.current_approver', ['1693', '1936']);
        }
    }
    // ---------------------------------------------------------

    $this->db->order_by('mandate_requests.id', 'DESC');
    
    // Execute Query
    $data['requests'] = $this->db->get()->result_array();
    
    $data['is_hr'] = $is_hr;
    $data['title'] = 'سجل الانتدابات';

    // 
    $this->load->view('templateo/my_mandates', $data);
    // 
}
// --- ADD TO Users1.php ---

// 1. عرض قائمة المستندات
public function documents_list() {
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    $this->load->model('hr_model');
    $data['documents'] = $this->hr_model->get_all_documents();
    
    // قم بتحميل العرض (تأكد من مسار واسم ملف الـ View الخاص بك)
    $this->load->view('templateo/document_list_view', $data);
}

// 2. عرض شاشة إضافة مستند (نافذة منبثقة أو صفحة عادية)
public function add_document() {
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    $this->load->model('hr_model');
    $data['employees'] = $this->hr_model->get_employees_list();
    $this->load->view('templateo/document_form_view', $data);
}

// 3. معالجة حفظ المستندات المتعددة
public function save_document() {
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    $this->load->model('hr_model');

    $employee_id = $this->input->post('employee_id');
    $document_type = $this->input->post('document_type');
    $expiry_date = $this->input->post('expiry_date');

    // إعدادات الرفع
    $config['upload_path']   = './uploads/documents/'; // تأكد من إنشاء هذا المجلد وإعطائه تصاريح الكتابة
    $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
    $config['max_size']      = 5120; // 5MB
    $config['encrypt_name']  = TRUE; // تشفير اسم الملف لمنع التعارض

    $this->load->library('upload', $config);

    // حساب عدد الملفات المرفوعة
    $filesCount = count($_FILES['documents']['name']);
    
    for ($i = 0; $i < $filesCount; $i++) {
        if (!empty($_FILES['documents']['name'][$i])) {
            $_FILES['file']['name']     = $_FILES['documents']['name'][$i];
            $_FILES['file']['type']     = $_FILES['documents']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['documents']['tmp_name'][$i];
            $_FILES['file']['error']    = $_FILES['documents']['error'][$i];
            $_FILES['file']['size']     = $_FILES['documents']['size'][$i];

            if ($this->upload->do_upload('file')) {
                $fileData = $this->upload->data();
                $insertData = array(
                    'employee_id'   => $employee_id,
                    'document_type' => $document_type,
                    'file_name'     => $fileData['orig_name'],
                    'file_path'     => $fileData['file_name'],
                    'expiry_date'   => $expiry_date
                );
                $this->hr_model->insert_document($insertData);
            } else {
                // يمكنك معالجة الأخطاء هنا إن أردت
                $error = $this->upload->display_errors();
            }
        }
    }
    
    // إغلاق النافذة المنبثقة وتحديث الصفحة الأم بعد الحفظ
    echo "<script>window.opener.location.reload(); window.close();</script>";
}

// 4. تنزيل المستند
public function download_document($id) {
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    $this->load->model('hr_model');
    $this->load->helper('download');
    
    $doc = $this->hr_model->get_document_by_id($id);
    if ($doc) {
        $file = './uploads/documents/' . $doc['file_path'];
        if (file_exists($file)) {
            force_download($doc['file_name'], file_get_contents($file));
        } else {
            show_error('الملف غير موجود.');
        }
    }
}// --- أضف هذه الدوال في Users1.php ---

public function manage_documents() {
    if (!$this->session->userdata('logged_in')) { redirect('users/login'); }
    $this->load->model('hr_model');
    // جلب المستندات لعرضها في الجدول
    $data['documents'] = $this->hr_model->get_all_documents();
    $this->load->view('templateo/document_manage_view', $data);
}

// دالة لحفظ مستند جديد بملفات متعددة عبر AJAX
// --- REPLACE YOUR save_document_ajax FUNCTION WITH THIS ---

public function save_document_ajax() {
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired']); return;
    }
    $this->load->model('hr_model');

    $employee_id = $this->input->post('emp_id');
    $notes = $this->input->post('notes');
    
    // Arrays from the dynamic form
    $doc_types = $this->input->post('doc_type'); 
    $expiry_dates = $this->input->post('expiry_date');

    $documents_array = array();

    $config['upload_path']   = './uploads/documents/';
    $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
    $config['max_size']      = 10240; // 10MB
    $config['encrypt_name']  = TRUE;

    $this->load->library('upload', $config);

    // Loop through the uploaded files based on the dynamic rows
    $count = count($doc_types);
    for($i = 0; $i < $count; $i++) {
        if(!empty($_FILES['doc_file']['name'][$i])) {
            $_FILES['file']['name']     = $_FILES['doc_file']['name'][$i];
            $_FILES['file']['type']     = $_FILES['doc_file']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['doc_file']['tmp_name'][$i];
            $_FILES['file']['error']    = $_FILES['doc_file']['error'][$i];
            $_FILES['file']['size']     = $_FILES['doc_file']['size'][$i];

            if($this->upload->do_upload('file')) {
                $fileData = $this->upload->data();
                
                // Bundle the specific type, date, and file together
                $documents_array[] = array(
                    'type'          => $doc_types[$i],
                    'expiry_date'   => $expiry_dates[$i],
                    'original_name' => $fileData['client_name'],
                    'file_name'     => $fileData['file_name']
                );
            }
        }
    }

    if (empty($documents_array)) {
        echo json_encode(['status' => 'error', 'message' => 'Please attach at least one document.']);
        return;
    }

    $insertData = array(
        'employee_id'    => $employee_id,
        'notes'          => $notes,
        'documents_data' => json_encode($documents_array) // Save everything in one record
    );

    if ($this->hr_model->insert_document($insertData)) {
        echo json_encode(['status' => 'success', 'message' => 'Record saved successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }
}
 public function mandate_approvals() {
    if (!$this->session->userdata('logged_in')) redirect('users/login');

    $my_id = $this->session->userdata('username');
    $this->load->database();

    // 1. Get PENDING approvals from workflow
    $this->db->select('
        aw.*, 
        mr.id as req_id, 
        mr.request_date, 
        mr.start_date, 
        mr.duration_days, 
        mr.total_amount, 
        mr.status as final_status,
        mr.current_approver,
        e.subscriber_name, 
        e.n1 as department
    ');
    $this->db->from('approval_workflow aw');
    $this->db->join('mandate_requests mr', 'mr.id = aw.order_id');
    $this->db->join('emp1 e', 'e.employee_id = mr.emp_id');
    $this->db->where('aw.approver_id', $my_id);
    $this->db->where('aw.order_type', 'Mandate');
    $this->db->where('aw.status', 'Pending');
    $this->db->order_by('aw.id', 'DESC');
    
    $pending_query = $this->db->get()->result_array();
    
    // 2. Get RETURNED orders (direct from mandate_requests)
    $this->db->select('
        mr.*,
        mr.id as req_id,
        e.subscriber_name,
        e.n1 as department
    ');
    $this->db->from('mandate_requests mr');
    $this->db->join('emp1 e', 'e.employee_id = mr.emp_id');
    $this->db->where('mr.status', 'Returned');
    $this->db->where('mr.current_approver', $my_id);
    $this->db->order_by('mr.id', 'DESC');
    
    $returned_query = $this->db->get()->result_array();
    
    // Add rejector info to returned orders
    foreach($returned_query as &$row) {
        if(!empty($row['rejected_from_id'])) {
            $this->db->select('subscriber_name, n1 as department');
            $rejector = $this->db->get_where('emp1', 
                ['employee_id' => $row['rejected_from_id']]
            )->row_array();
            
            $row['rejected_by_name'] = $rejector['subscriber_name'] ?? 'غير معروف';
            $row['rejected_by_department'] = $rejector['department'] ?? '-';
            $row['return_date'] = $row['updated_at'] ?? date('Y-m-d H:i:s');
        }
    }
    
    // 3. Get HISTORY (Approved/Rejected workflow entries)
    $this->db->select('
        aw.*, 
        mr.id as req_id, 
        mr.request_date, 
        mr.start_date, 
        mr.duration_days, 
        mr.total_amount, 
        e.subscriber_name, 
        e.n1 as department
    ');
    $this->db->from('approval_workflow aw');
    $this->db->join('mandate_requests mr', 'mr.id = aw.order_id');
    $this->db->join('emp1 e', 'e.employee_id = mr.emp_id');
    $this->db->where('aw.approver_id', $my_id);
    $this->db->where('aw.order_type', 'Mandate');
    $this->db->where_in('aw.status', ['Approved', 'Rejected', 'Returned']);
    $this->db->order_by('aw.id', 'DESC');
    
    $history_query = $this->db->get()->result_array();

    // 4. Prepare data for view
    $data = [
        'pending' => $pending_query,
        'returned' => $returned_query,
        'history' => $history_query
    ];

    // 5. Load view
    $this->load->view('templateo/mandate_approvals', $data);
}
    // =========================================================
    // 4. WORKFLOW LOGIC: Approve/Reject Action
    // =========================================================
    // In application/controllers/Users1.php

public function do_mandate_approval() {
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Session Timeout']);
        return;
    }

    $req_id = $this->input->post('req_id');
    $action = $this->input->post('action'); 
    $reason = $this->input->post('reason');
    $my_id  = $this->session->userdata('username');

    if (empty($req_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Request ID missing']);
        return;
    }

    $this->load->database();
    
    // 1. Get Request
    $req = $this->db->get_where('mandate_requests', ['id' => $req_id])->row();
    if (!$req) {
        echo json_encode(['status' => 'error', 'message' => 'Request not found']);
        return;
    }

    // 2. Permission Check
    $hr_admins = ['2230', '2515', '2774', '2784', '1835', '2901', '1693', '2909', '2833', '1936'];
    
    if ($req->current_approver != $my_id && !in_array($my_id, $hr_admins)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }

    // --- DETERMINE STATUS STRING ---
    if ($action == 'approve') {
        $status_str = 'Approved';
    } else {
        // If anyone other than 2784 rejects, we call it 'Returned'
        $status_str = ($my_id != '2784') ? 'Returned' : 'Rejected'; //instead
    }

    $this->db->trans_start();

    // 3. Update Workflow History for the current user
    $this->db->where('order_id', $req_id);
    $this->db->where('order_type', 'Mandate');
    $this->db->where('approver_id', $my_id);
    $this->db->where('status', 'Pending');
    $this->db->update('approval_workflow', [
        'status'      => $status_str,
        'action_date' => date('Y-m-d H:i:s'),
        'notes'       => $reason
    ]);

    // 4. Handle Decision
    if ($action == 'reject') {
        
        // --- REJECTION LOGIC: RETURN TO 2784 IF NOT 2784 ---
        if ($my_id != '2784') { //instead
            $this->db->where('id', $req_id);
            $this->db->update('mandate_requests', [
                'status'           => 'Returned', // Special status
                'current_approver' => '2784',    // Send to Specialist instead
                'rejected_from_id' => $my_id,    // REMEMBER WHO REJECTED IT
                'rejection_reason' => $reason
            ]);
            $msg = 'تم إرجاع الطلب إلى الأخصائي (2784) للمراجعة'; //instead
        } 
        else {
            // If 2784 herself rejects, it is a Final Rejection
            $this->db->where('id', $req_id);
            $this->db->update('mandate_requests', [
                'status'           => 'Rejected',
                'current_approver' => null,
                'rejection_reason' => $reason
            ]);
            $msg = 'تم رفض الطلب نهائياً';
        }

    } else {
        // --- APPROVE LOGIC (CORRECTED) ---
        
        $emp_info = $this->db->select('n13')->get_where('emp1', ['employee_id' => $req->emp_id])->row();
        $raw_code = ($emp_info && isset($emp_info->n13)) ? $emp_info->n13 : '';
        $company_code = trim((string)$raw_code); 
        $is_ceo = ($req->emp_id == '1001');

        $next_approver = null;

        // --- CORRECTED ROUTING TREE ---
        // Note: 'direct_manager' step is handled before this function
        // Requests come to this function with current_approver already set
        
        if ($my_id == '2784') { //instead
            // From Specialist (2784)
            if ($is_ceo) {
                // CEO special path: 2784 → 2833 → 1693
                $next_approver = '2833';
            } elseif ($company_code == '2') {
                // Company Code 2: 2784 → 2909 → 1936
                $next_approver = '2909';
            } else {
                // Company Code 1 (default): 2784 → 2833 → 1693
                $next_approver = '2833';
            }
        }
        elseif ($my_id == '2909' && $company_code == '2') {
            // From 2909 (only for company code 2)
            $next_approver = '1936';
        }
        elseif ($my_id == '1936' && $company_code == '2') {
            // From 1936 (final approver for company code 2)
            $next_approver = 'Completed';
        }
        elseif ($my_id == '2833' && $company_code != '2') {
            // From 2833 (only for company code 1 and CEO)
            $next_approver = '1693';
        }
        elseif ($my_id == '1693' && $company_code != '2') {
            // From 1693 (final approver for company code 1 and CEO)
            $next_approver = 'Completed';
        }
        else {
            // Default case (for direct manager or unexpected cases)
            $next_approver = '2784';  //instead
         }

        // Apply Updates
        if ($next_approver == 'Completed') {
            $this->db->where('id', $req_id);
            $this->db->update('mandate_requests', [
                'status' => 'Approved', 
                'current_approver' => 'Completed'
            ]);
            $msg = 'تم اعتماد الطلب نهائياً';
        } else {
            $this->db->where('id', $req_id);
            $this->db->update('mandate_requests', [
                'current_approver' => $next_approver
            ]);
            
            // Add next approver to workflow
            $wf_data = [
                'order_id'    => $req_id,
                'order_type'  => 'Mandate',
                'approver_id' => $next_approver,
                'status'      => 'Pending',
                'action_date' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('approval_workflow', $wf_data);
            
            $msg = 'تم الاعتماد. المحطة القادمة: ' . $next_approver;
        }
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        $error = $this->db->error();
        echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $error['message']]);
    } else {
        
        // ==========================================
        // [NEW] TRIGGER MANDATE EMAIL NOTIFICATION
        // ==========================================
        $this->load->model('hr_model');
        $this->hr_model->trigger_mandate_email($req_id);
        // ==========================================

        echo json_encode(['status' => 'success', 'message' => $msg]);
    }
}
// In application/controllers/Users1.php
// In application/controllers/Users1.php

public function export_insurance_requests()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    $this->load->helper('download');
    
    $emp_id = $this->session->userdata('username');
    $data = $this->hr_model->get_all_insurance_details_for_export($emp_id);

    // BOM for Arabic Excel Support
    $content = "\xEF\xBB\xBF"; 
    
    // Headers
    $headers = [
        'رقم الطلب', 
        'الرقم الوظيفي', 
        'اسم الموظف', 
        'اسم الموظف (En)', // Will be empty as per table structure
        'تاريخ الطلب', 
        'نوع التغطية', 
        'اسم التابع (عربي)', 
        'اسم التابع (إنجليزي)', 
        'صلة القرابة', 
        'رقم هوية التابع', 
        'تاريخ ميلاد التابع', 
        'الحالة'
    ];
    
    $content .= implode(",", $headers) . "\n";

    foreach ($data as $row) {
        $line = [];
        $line[] = $row['request_id'];
        $line[] = $row['emp_id'];
        $line[] = $row['emp_name_ar']; // subscriber_name
        $line[] = $row['emp_name_en']; // Empty
        $line[] = $row['created_at'];
        $line[] = ($row['request_type'] == 'family') ? 'عائلة' : 'موظف';
        
        $line[] = $row['fam_name_ar'] ? $row['fam_name_ar'] : '-';
        $line[] = $row['fam_name_en'] ? $row['fam_name_en'] : '-';
        $line[] = $row['relationship'] ? $row['relationship'] : '-';
        $line[] = $row['national_id'] ? $row['national_id'] : '-';
        $line[] = $row['dob'] ? $row['dob'] : '-';
        
        $status_map = [
            '0' => 'Pending', 'Pending' => 'Pending',
            '1' => 'Processing', 'Processing' => 'Processing',
            '2' => 'Approved', 'Approved' => 'Approved',
            '3' => 'Rejected', 'Rejected' => 'Rejected'
        ];
        $line[] = isset($status_map[$row['status']]) ? $status_map[$row['status']] : $row['status'];

        // Escape for CSV
        $line = array_map(function($field) {
            return '"' . str_replace('"', '""', $field) . '"';
        }, $line);

        $content .= implode(",", $line) . "\n";
    }

    $filename = 'insurance_requests_' . date('Ymd_His') . '.csv';
    force_download($filename, $content);
}
public function my_insurance_requests()
{
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    $this->load->model('hr_model');
    $employee_id = $this->session->userdata('username');

    // 2. Fetch Requests
    // We will create this function in the model next
    $data['requests'] = $this->hr_model->get_employee_insurance_requests($employee_id);

    // 3. Load View
    $data['page_title'] = 'طلبات التأمين الطبي';
  //   // Assuming standard header
    $this->load->view('templateo/my_insurance_requests_view', $data);
 //    // Assuming standard footer
}
   public function save_policy_ajax() {
        if (!$this->session->userdata('logged_in')) redirect('users/login');
        
        $data = [
            'employee_tag' => $this->input->post('employee_tag'),
            'min_km' => $this->input->post('min_km'),
            'max_km' => $this->input->post('max_km'),
            'transport_type' => $this->input->post('transport_type'),
            'salary_multiplier' => 1, // Default, since we use Min/Max now
            'min_daily_amount' => $this->input->post('min_daily_amount'),
            'max_daily_amount' => $this->input->post('max_daily_amount'),
            'max_cap_amount' => 99999 // Global cap if needed
        ];
        
        $this->db->insert('mandate_policies', $data);
        redirect('users1/mandate_settings');
    }
public function debug_my_hierarchy() {
        if (!$this->session->userdata('logged_in')) die("Please login first");
        
        $this->load->model('hr_model');
        $emp_id = $this->session->userdata('username');
        
        // 1. Get Raw Data
        $emp_info = $this->hr_model->get_employee_mandate_info($emp_id);
        
        echo "<pre style='background:#f4f4f4; padding:20px; font-size:16px;'>";
        echo "<h1>Hierarchy Debugger</h1>";
        echo "<strong>Logged in as:</strong> " . $emp_id . "<br>";
        
        if(!$emp_info) {
            echo "<h2 style='color:red'>ERROR: Could not find employee row in DB!</h2>";
            die();
        }

        echo "<strong>Found DB Row ID:</strong> " . $emp_info->id . "<br>";
        echo "<strong>Found Employee ID:</strong> " . $emp_info->employee_id . "<br><br>";
        
        echo "<h3>Checking Columns (N1 - N10):</h3>";
        
        // Match Logic Simulation
        $search_id = !empty($emp_info->employee_id) ? $emp_info->employee_id : $emp_info->id;
        $found_in_col = false;
        
        for($i=1; $i<=10; $i++) {
            $col = 'n'.$i;
            $val = isset($emp_info->$col) ? $emp_info->$col : '[EMPTY]';
            
            // Highlight match
            $style = "";
            if(trim($val) == $search_id) {
                $style = "background:lightgreen; font-weight:bold;";
                $found_in_col = $col;
            }
            
            echo "<div style='border-bottom:1px solid #ccc; padding:5px; $style'>";
            echo "Column <strong>$col</strong>: '$val'";
            if($style) echo " <--- YOU ARE HERE";
            echo "</div>";
        }
        
        echo "<br><h3>Result:</h3>";
        if($found_in_col) {
            $level = (int)str_replace('n', '', $found_in_col);
            $manager_col = 'n'.($level - 1);
            $manager_id = isset($emp_info->$manager_col) ? $emp_info->$manager_col : 'None';
            
            echo "You were found in <strong>$found_in_col</strong>.<br>";
            echo "Your Manager should be in <strong>$manager_col</strong>.<br>";
            echo "Manager Value found: <span style='color:blue; font-size:20px'>'$manager_id'</span>";
        } else {
            echo "<h3 style='color:red'>FAILED: Your ID ($search_id) was NOT found in any column (n1-n10).</h3>";
            echo "The system defaulted to 1001.";
        }
        echo "</pre>";
    }
    public function delete_policy($id) {
        $this->db->where('id', $id)->delete('mandate_policies');
        redirect('users1/mandate_settings');
    }
public function hr_report_ajax()
{
    if (!$this->session->userdata('logged_in')) exit;
    $this->load->model('hr_model');
    
    $type = $this->input->post('report_type');
    $filters = [
        'dept'  => $this->input->post('department'),
        'comp'  => $this->input->post('company'),
        'start' => $this->input->post('start_date'),
        'end'   => $this->input->post('end_date')
    ];
    
    $data = $this->hr_model->get_report_data($type, $filters);
    
    echo json_encode([
        'data' => $data,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

public function fetch_comprehensive_data()
{
    if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
    
    $this->load->model('hr_model');
    
    $report_type = $this->input->post('report_type');
    $filters = [
        'department' => $this->input->post('department'),
        'company'    => $this->input->post('company'),
        'location'   => $this->input->post('location'),
        'start_date' => $this->input->post('start_date'),
        'end_date'   => $this->input->post('end_date')
    ];

    $data = $this->hr_model->get_comprehensive_report_data($report_type, $filters);
    
    // Format data for DataTables
    $formatted_data = [];
    foreach ($data as $row) {
        $item = [
            'emp_id' => $row['employee_id'],
            'name'   => $row['subscriber_name'],
            'dept'   => $row['department'],
            'job'    => $row['profession'],
            'join'   => $row['joining_date']
        ];

        // Add dynamic columns based on type
        switch ($report_type) {
            case 'probation':
                $item['extra_1'] = $row['days_employed'] . ' يوم';
                $item['extra_2'] = '<span class="badge bg-info">تحت التجربة</span>';
                break;
            case 'resigned':
                $item['extra_1'] = $row['date_of_the_last_working'];
                $item['extra_2'] = $row['reason_for_resignation'];
                break;
            case 'on_vacation':
            case 'sick_leave':
                $item['extra_1'] = $row['vac_start'] . ' إلى ' . $row['vac_end'];
                $item['extra_2'] = ($report_type == 'sick_leave') ? 'إجازة مرضية' : $row['vac_main_type'];
                break;
            case 'pending_requests':
                $item['extra_1'] = $row['request_type'];
                $item['extra_2'] = '<span class="badge bg-warning">معلق</span> ' . $row['request_date'];
                break;
            case 'balances':
                $item['extra_1'] = $row['leave_type_slug'];
                $item['extra_2'] = 'المتبقي: ' . number_format((float)$row['remaining_balance'], 2);
                break;
        }
        $formatted_data[] = $item;
    }

    echo json_encode(['data' => $formatted_data, 'csrf_hash' => $this->security->get_csrf_hash()]);
}

public function export_comprehensive_excel()
{
    // Basic Excel Export logic using CSV helper
    if (!$this->session->userdata('logged_in')) redirect('users/login');
    
    $this->load->model('hr_model');
    $this->load->dbutil();
    $this->load->helper('download');

    $report_type = $this->input->get('type');
    $filters = [
        'department' => $this->input->get('dept'),
        'company'    => $this->input->get('comp'),
        'location'   => $this->input->get('loc')
    ];

    $data = $this->hr_model->get_comprehensive_report_data($report_type, $filters);
    
    // Convert Array to CSV compatible structure
    $csv_output = "\xEF\xBB\xBF"; // BOM
    $headers = ['الرقم الوظيفي', 'الاسم', 'الإدارة', 'الوظيفة', 'تاريخ المباشرة', 'تفاصيل 1', 'تفاصيل 2'];
    $csv_output .= implode(',', $headers) . "\r\n";

    foreach ($data as $row) {
        $extra1 = ''; $extra2 = '';
        if(isset($row['days_employed'])) $extra1 = $row['days_employed'] . ' Days';
        if(isset($row['date_of_the_last_working'])) $extra1 = $row['date_of_the_last_working'];
        if(isset($row['vac_start'])) $extra1 = $row['vac_start'] . ' - ' . $row['vac_end'];
        if(isset($row['remaining_balance'])) $extra2 = $row['remaining_balance'];
        
        $line = [
            $row['employee_id'],
            '"' . str_replace('"', '""', $row['subscriber_name']) . '"',
            '"' . str_replace('"', '""', $row['department']) . '"',
            '"' . str_replace('"', '""', $row['profession']) . '"',
            $row['joining_date'],
            '"' . str_replace('"', '""', $extra1) . '"',
            '"' . str_replace('"', '""', $extra2) . '"',
        ];
        $csv_output .= implode(',', $line) . "\r\n";
    }

    force_download("HR_Report_{$report_type}_" . date('Y-m-d') . ".csv", $csv_output);
}
// Endpoint for Personal, Job, and Financial Details
public function get_personal_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model->get_employee_profile_details($employee_id);
    if ($data) { $this->_send_json_response('success', $data); } 
    else { $this->_send_json_response('error', null, 'Employee not found.'); }
}

public function get_job_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model->get_employee_profile_details($employee_id);
    if ($data) { $this->_send_json_response('success', $data); }
    else { $this->_send_json_response('error', null, 'Employee not found.'); }
}

public function get_financial_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model->get_employee_profile_details($employee_id);
    if ($data) { $this->_send_json_response('success', $data); }
    else { $this->_send_json_response('error', null, 'Employee not found.'); }
}

// Endpoint for Contract Details
public function get_contract_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model->get_employee_contract_details($employee_id);
    if ($data) { $this->_send_json_response('success', $data); }
    else { $this->_send_json_response('error', null, 'Contract details not found.'); }
}

// Endpoint for Leave Balances
public function get_leave_balances() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $balances = $this->hr_model->get_leave_balances_by_employee($employee_id);
    if (!empty($balances)) { $this->_send_json_response('success', $balances); }
    else { $this->_send_json_response('success', [], 'No leave balance data found.'); }
}

// Endpoint for Last Salary Slip
public function get_last_salary_slip() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $payslip = $this->hr_model->get_last_payroll_for_employee($employee_id);
    if (!empty($payslip)) { $this->_send_json_response('success', $payslip); }
    else { $this->_send_json_response('error', null, 'No previous salary slip found.'); }
}

// PASTE THIS NEW FUNCTION INSIDE your application/controllers/Users1.php file
// PASTE THIS NEW FUNCTION INSIDE your application/controllers/Users1.php
// In application/controllers/Users1.php

// Action triggered by HR Manager (2230) to "Send" slips
public function publish_payroll_action()
{
    if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
    
    // 1. Define Authorized Users (Same list used elsewhere)
    $hr_users = ['1835', '2230', '2515', '2774', '2784', '2901'];
    $current_user = $this->session->userdata('username');

    // 2. Security Check
    if (!in_array($current_user, $hr_users)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Your ID (' . $current_user . ') does not have permission.']);
        return;
    }

    $month_key = $this->input->post('month_key');
    
    if (empty($month_key)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing Sheet ID']);
        return;
    }

    $this->load->model('hr_model');
    
    if ($this->hr_model->publish_payroll_sheet($month_key)) {
        echo json_encode(['status' => 'success', 'message' => 'تم إرسال مسيرات الرواتب للموظفين بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل العملية في قاعدة البيانات']);
    }
}

// PDF Generation
// In Users1.php

// In application/controllers/Users1.php

public function download_payslip($safe_url = null)
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }

    if (empty($safe_url)) {
        show_error('Invalid Link', 404);
        return;
    }

    // 1. Restore Base64 Format (Replace - with +, _ with /)
    $base64 = str_replace(['-', '_'], ['+', '/'], $safe_url);
    
    // 2. Decode
    $month_key = base64_decode($base64);

    if (empty($month_key)) {
        show_error('Cannot decode payslip ID', 404);
        return;
    }

    $this->load->model('hr_model');
    $emp_id = $this->session->userdata('username');
    
    // DEBUG: Log the parameters
    log_message('debug', 'Download payslip - Employee: ' . $emp_id . ', Month: ' . $month_key);
    
    // Check what tables exist in database
    $tables = $this->db->list_tables();
    log_message('debug', 'Available tables: ' . implode(', ', $tables));

    // 3. Fetch Data
    $data = $this->hr_model->get_payslip_data($emp_id, $month_key);
    
    if (!$data) {
        show_error('Payslip data not found for: ' . html_escape($month_key), 404);
        return;
    }

    // 4. Handle Logo
    $logo_path = FCPATH . 'assets/imeges/m1.png'; 
    $logo_base64 = '';
    if (file_exists($logo_path)) {
        $type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $data_img = file_get_contents($logo_path);
        $logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
    }
    $data['logo_base64'] = $logo_base64;

    $this->load->view('templateo/payslip_print_view', $data);
}
/* ---------------------------------------------------
   PASTE THIS INSIDE: application/controllers/Users1.php
--------------------------------------------------- */
   public function ajax_calculate_ot() {
    if (!$this->session->userdata('logged_in')) exit;

    $emp_id = $this->input->post('emp_id');
    if (!$emp_id) $emp_id = $this->session->userdata('username');

    // 1. Determine Dates
    $type = $this->input->post('type'); 
    if ($type === 'range') {
        $date_from = $this->input->post('date_from');
        $date_to   = $this->input->post('date_to');
    } else {
        $d = $this->input->post('date');
        $date_from = $d;
        $date_to   = $d;
    }

    if (!$date_from || !$date_to) {
        echo json_encode(['status' => 'error', 'message' => 'Dates missing', 'csrf_hash' => $this->security->get_csrf_hash()]);
        return;
    }

    // 2. Get Standard Plan
    $plan_query = $this->db->get_where('work_restrictions', ['emp_id' => $emp_id]);
    $plan = $plan_query->row_array();
    $standard_hours = (float)($plan['working_hours'] ?? 8);
    if ($standard_hours <= 0) $standard_hours = 8;

    $total_ot_decimal = 0;
    $daily_logs = []; 

    $current_time = strtotime($date_from);
    $end_time     = strtotime($date_to);

    // 3. Loop through days
    while ($current_time <= $end_time) {
        $check_date = date('Y-m-d', $current_time);
        
        // --- SATURDAY LOGIC ---
        $day_required = $standard_hours; 
        
        $this->db->where('employee_id', $emp_id);
        $this->db->where('saturday_date', $check_date);
        $assignment = $this->db->get('saturday_work_assignments')->row();
        
        // If assigned, Required = 0 (Full OT)
        if ($assignment) { $day_required = 0; }

        // --- FETCH ATTENDANCE ---
        $this->db->select_min('punch_time', 'check_in');
        $this->db->select_max('punch_time', 'check_out');
        $this->db->where('emp_code', $emp_id);
        $this->db->where('DATE(punch_time)', $check_date);
        $log = $this->db->get('attendance_logs')->row();

        // Display Variables
        $in_time = '-';
        $out_time = '-';
        $worked_str = '00:00'; 
        $ot_str = '00:00';
        $status_class = 'table-light text-muted'; 

        if ($log && $log->check_in && $log->check_out && $log->check_in != $log->check_out) {
            $in = strtotime($log->check_in);
            $out = strtotime($log->check_out);
            $in_time = date('H:i', $in);
            $out_time = date('H:i', $out);
            
            // Calc Seconds
            $worked_seconds = $out - $in;
            
            // [FIX] Format Worked Time as String "HH:MM"
            $w_h = floor($worked_seconds / 3600);
            $w_m = floor(($worked_seconds % 3600) / 60);
            $worked_str = sprintf("%02d:%02d", $w_h, $w_m); // This sends "09:50"

            // Calc Required Seconds
            $required_seconds = $day_required * 3600;

            // Calc Overtime
            if ($worked_seconds > $required_seconds) {
                $ot_seconds = $worked_seconds - $required_seconds;
                
                // [FIX] Format OT as String "HH:MM"
                $ot_h = floor($ot_seconds / 3600);
                $ot_m = floor(($ot_seconds % 3600) / 60);
                $ot_str = sprintf("%02d:%02d", $ot_h, $ot_m); // This sends "01:30"

                // Add decimal to total (for the payroll calculation)
                $ot_decimal_day = $ot_h + ($ot_m / 60);
                $total_ot_decimal += $ot_decimal_day;
                
                $status_class = 'table-success';
            }
        }

        $daily_logs[] = [
            'date' => $check_date,
            'day_name' => date('D', $current_time),
            'in' => $in_time,
            'out' => $out_time,
            'worked' => $worked_str,   // Sends String "09:50"
            'required' => $day_required,
            'ot_display' => $ot_str,   // Sends String "01:30"
            'class' => $status_class
        ];
        
        $current_time = strtotime('+1 day', $current_time);
    }

    echo json_encode([
        'status' => 'success',
        'hours' => round($total_ot_decimal, 2), // Total Decimal (e.g., 9.83) for Input Field
        'details' => $daily_logs,               // Formatted Strings for Table
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}
// API for Profile to check status
public function check_last_payslip_status()
{
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        return;
    }

    $emp_id = $this->session->userdata('username');
    $this->load->model('hr_model');
    
    // Get current month in YYYY-MM format
    $current_month = date('Y-m');
    log_message('debug', 'Current month: ' . $current_month);
    
    // Check if current month's payroll is published
    $is_published = $this->hr_model->is_payroll_published($current_month);
    log_message('debug', 'Current month published: ' . ($is_published ? 'Yes' : 'No'));
    
    if ($is_published) {
        // 1. Encode to Base64
        $base64 = base64_encode($current_month);
        
        // 2. Make it URL-Safe (Replace + with -, / with _, remove =)
        $safe_url = str_replace(['+', '/', '='], ['-', '_', ''], $base64);

        echo json_encode([
            'status' => 'success', 
            'month' => $current_month,
            'download_url' => site_url('users1/download_payslip/' . $safe_url)
        ]);
    } else {
        // Get the latest published month (regardless of which month)
        $latest_month = $this->hr_model->get_latest_published_month();
        log_message('debug', 'Latest published month: ' . ($latest_month ?: 'None'));
        
        if ($latest_month) {
            $base64 = base64_encode($latest_month);
            $safe_url = str_replace(['+', '/', '='], ['-', '_', ''], $base64);
            
            echo json_encode([
                'status' => 'success', 
                'month' => $latest_month,
                'download_url' => site_url('users1/download_payslip/' . $safe_url),
                'note' => 'هذا مسير راتب سابق'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No payslip available']);
        }
    }
}

// In Users1.php

public function ajax_get_eos_violation_details()
{
    if (!$this->input->is_ajax_request() || !$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(403);
    }

    $employee_id = $this->input->post('employee_id');
    $last_working_day = $this->input->post('last_working_day');

    $this->load->model('hr_model');
    
    // Get the detailed breakdown
    $details = $this->hr_model->get_eos_violation_details($employee_id, $last_working_day);

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'data' => $details]));
}
// --- OPEN: application/controllers/Users1.php ---

public function processed_payroll_report()
{
    // Security Check for HR Users
    $username = $this->session->userdata('username');
    $allowed_users = ['1835', '2230', '2515', '2774', '2784', '2901'];
    if (!in_array($username, $allowed_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $this->load->model('hr_model');

    // Get filters from URL
    $filters = [
        'month'           => $this->input->get('month', TRUE),
        'company_code'    => $this->input->get('company_code', TRUE),
        'employee_search' => $this->input->get('employee_search', TRUE)
    ];

    // Prepare data for the view
    $data = [
        'filters'       => $filters,
        'payroll_data'  => $this->hr_model->get_processed_payroll_data($filters),
        'month_list'    => $this->hr_model->get_distinct_payroll_periods(),
        'company_list'  => [ // You can expand this list as needed
            '1' => 'شركة مرسوم لتحصيل الديون',
            '2' => 'مكتب د. صالح الجربوع للمحاماة'
        ],
        'page_title'    => 'تقرير مسير الرواتب المحفوظة'
    ];
    
    // Load the new view
    $this->load->view('templateo/processed_payroll_report_view', $data);
}
// --- Add to Users1.php ---

    public function delegation_report() {
        // Security check
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }
        
        $this->load->model('hr_model');
        $data['delegates'] = $this->hr_model->get_delegation_list();
        $data['title'] = 'تقرير الموظفين المفوضين';
        
        // Adjust the header/footer paths if yours are named differently
     //   $this->load->view('templateo/header', $data);
        $this->load->view('templateo/delegation_list_view', $data);
   //     
    }

    public function delegation_details($delegate_id) {
        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
        }
        
        if(empty($delegate_id)) {
            show_404();
        }

        $this->load->model('hr_model');
        $data['delegate_info'] = $this->hr_model->get_delegate_info($delegate_id);
        $data['delegation_records'] = $this->hr_model->get_delegation_details($delegate_id);
        $data['title'] = 'تفاصيل المهام المفوضة';
        
      //  $this->load->view('templateo/header', $data);
        $this->load->view('templateo/delegation_details_view', $data);
     //   
    }
// PASTE THIS NEW FUNCTION INSIDE your application/controllers/Users1.php

public function payroll_summary()
{
    // Security Check for HR Users
    $username = $this->session->userdata('username');
    $allowed_users = ['1835', '2230', '2515', '2774', '2784', '2901'];
    if (!in_array($username, $allowed_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $this->load->model('hr_model');

    $data = [
        'payroll_summaries' => $this->hr_model->get_payroll_period_summaries(),
        'page_title'        => 'ملخص مسيرات الرواتب'
    ];
    
    // Load the new view we will create in the next step
    $this->load->view('templateo/payroll_summary_view', $data);
}
public function process_payroll()
{
    // Security Check: Only allow authorized HR users and AJAX POST requests
    $username = $this->session->userdata('username');
    $allowed_users = array('1835', '2230', '2515', '2774', '2784', '2901');
    if (!in_array($username, $allowed_users) || !$this->input->is_ajax_request()) {
        return $this->output->set_status_header(403)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized access.']));
    }

    $this->load->model('hr_model');

    // Get the JSON data sent from the view
    $payroll_data = $this->input->post('payroll_data');
    $sheet_id = $this->input->post('sheet_id');

    if (empty($payroll_data) || !is_array($payroll_data) || empty($sheet_id)) {
        return $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'No data received.']));
    }

    // Call the model function to save the data
    $success = $this->hr_model->save_processed_payroll($payroll_data, $sheet_id);

    if ($success) {
        $response = ['status' => 'success', 'message' => 'تمت معالجة وحفظ مسير الرواتب بنجاح!'];
    } else {
        $response = ['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ البيانات في قاعدة البيانات.'];
    }
    
    // Send the response back to the JavaScript
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
    public function leave_balances() {

      


    $data['get_leave_balance'] = $this->hr_model->get_leave_balance();
       
 
        $this->load->view('templateo/leave_balances', $data);
    }

     public function series_of_approvals() {

      


    $data['get_series_of_approvals'] = $this->hr_model->get_series_of_approvals();
       
 
        $this->load->view('templateo/series_of_approvals', $data);
    }





    public function get_employee_data() {
        // التأكد من أن الرقم الوظيفي تم تمريره عبر AJAX
        if($this->input->post('employee_id')) {
            $employee_id = $this->input->post('employee_id'); // الحصول على الرقم الوظيفي من POST

            // استدعاء دالة الموديل للبحث عن الموظف
            $employee_data = $this->hr_model->get_employee_by_id($employee_id);

            // التحقق من وجود الموظف
            if($employee_data) {
                // إرسال النتيجة عبر JSON
                echo json_encode(['status' => 'success', 'employee_name' => $employee_data['subscriber_name']]);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }

    public function add_vacations() {
    $data['title'] = 'اضافة إجازة جديدة';

    $this->form_validation->set_rules('name', 'name', 'required');

    if ($this->form_validation->run() === FALSE) {
        // عرض نموذج إضافة الإجازة
        $this->load->view('templateo/add_vacations', $data);
    } else {
        // إضافة الإجازة باستخدام الموديل
        $this->hr_model->add_vacations();

        // عرض رسالة ثم إغلاق النافذة وتحديث النافذة السابقة
        echo "<script>
                window.close();  // إغلاق النافذة الحالية
                window.opener.location.reload();  // تحديث النافذة الأصلية
              </script>";
    }
}

 public function exemption() {
    $data['title'] = 'اضافة إجازة جديدة';

      $data['id'] = $this->uri->segment(3,0);
      $id = $data['id'];

       $data['id2'] = $this->uri->segment(4,0);
      $id2 = $data['id2'];




    $this->form_validation->set_rules('type', 'type', 'required');

    if ($this->form_validation->run() === FALSE) {
        // عرض نموذج إضافة الإجازة
        $this->load->view('templateo/exemption', $data);
    } else {
        // إضافة الإجازة باستخدام الموديل
        $this->hr_model->update_attendance_summary($id,$id2);

        // عرض رسالة ثم إغلاق النافذة وتحديث النافذة السابقة
        echo "<script>
                window.close();  // إغلاق النافذة الحالية
                window.opener.location.reload();  // تحديث النافذة الأصلية
              </script>";
    }
}



 public function payroll_view2() {

    $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];



    $data['get_salary_sheet'] = $this->hr_model->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model->get_employees();

        
        $attendance_data = $this->hr_model->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/m1_hr', $data);
    }

    private function _collect_opts_from_get()
    {
        // اجمع الخيارات من GET لاستخدامها في الموديل والتصدير
        $opts = [];
        $opts['only_mismatched'] = (bool)$this->input->get('only_mismatched');
        $opts['tolerance']       = is_null($this->input->get('tolerance')) ? 0.0 : (float)$this->input->get('tolerance');
        $opts['q']               = $this->input->get('q', true);
        $opts['sort']            = $this->input->get('sort', true) ?: 'id_asc';
        return $opts;
    }

    public function gosi_emp1_compare()
    {
        $opts          = $this->_collect_opts_from_get();
        $data['rows']  = $this->hr_model->get_gosi_emp1_comparison(100000, 0, $opts);
        $data['stats'] = $this->hr_model->get_summary_stats($opts);
        $data['title'] = 'تقرير مقارنة التأمينات (GOSI) مع بيانات الموظفين (EMP1)';
        $data['opts']  = $opts; // نمررها للفيو لاستخدامها في شريط الأدوات ورابط التصدير

        $this->load->view('templateo/compare_gosi_emp1', $data);
    }

    /**
     * تصدير CSV (يشمل جميع الأعمدة الظاهرة في الفيو)
     * يستخدم نفس فلاتر GET المفعلة في شاشة العرض
     * مثال: /index.php/reports/gosi_emp1_compare_export?only_mismatched=1&tolerance=0.5&q=سعود&sort=diff_total_desc
     */
    public function gosi_emp1_compare_export()
    {
        $opts = $this->_collect_opts_from_get();
        $rows = $this->hr_model->get_gosi_emp1_comparison(100000, 0, $opts);

        $filename = 'gosi_emp1_compare_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        // BOM لدعم العربية في Excel
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');

        // رؤوس الأعمدة (تطابق الفيو)
        fputcsv($out, [
            'رقم الهوية',
            'اسم الموظف',

            'GOSI - الراتب الأساسي','EMP1 - الراتب الأساسي','فرق الراتب الأساسي',
            'GOSI - بدل السكن','EMP1 - بدل السكن','فرق بدل السكن',
            'GOSI - Other (n5+n6)','EMP1 - Other (n4..n12)','فرق Other',

            'GOSI - الإجمالي (محسوب)','EMP1 - الإجمالي (total_salary)','فرق الإجمالي',

            'الفرق بالإجمالي', // يوجد فرق / لا يوجد
            'وجود السجل',      // موجود في الجدولين / غير موجود في GOSI / غير موجود في ملف الموظفين

            'الحالة',          // مطابق / غير مطابق
            'تفاصيل الفروقات'
        ]);

        foreach ($rows as $r) {
            // وجود السجل (نفس منطق الفيو)
            if (!empty($r['e_id_number']) && !empty($r['emp_name_gosi'])) {
                $presence_label = 'موجود في الجدولين';
            } elseif (empty($r['emp_name_gosi'])) {
                $presence_label = 'غير موجود في GOSI';
            } elseif (empty($r['e_id_number'])) {
                $presence_label = 'غير موجود في ملف الموظفين';
            } else {
                $presence_label = '—';
            }

            // الفرق بالإجمالي (نصي)
            $has_total_diff = ((float)$r['diff_total'] != 0.0) ? 'يوجد فرق' : 'لا يوجد';

            fputcsv($out, [
                $r['id_number'],
                isset($r['emp_name_final']) ? $r['emp_name_final'] : ($r['emp_name_gosi'] ?: ''),

                $r['g_base'],     $r['e_base'],     $r['diff_base'],
                $r['g_housing'],  $r['e_housing'],  $r['diff_housing'],
                $r['g_other'],    $r['e_other'],    $r['diff_other'],

                $r['g_total'],    $r['e_total'],    $r['diff_total'],

                $has_total_diff,
                $presence_label,

                $r['status'],
                $r['diff_details']
            ]);
        }

        fclose($out);
        exit;
    }




   
// In controllers/Users1.php
// In Users1.php

// In Users1.php

public function employee_requests_report()
{
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
    }
    
    // This data is for the filter dropdowns on the page
    $data['request_types'] = [
        '1' => 'استقالة', '2' => 'تصحيح بصمة', '3' => 'عمل إضافي',
        '4' => 'مصاريف مالية', '5' => 'إجازة', '6' => 'طلب عهدة',
        '7' => 'طلب خطاب'
    ];
    
    // ✅ ADDED: Pass the status list to the view
    $data['statuses'] = [
        '0' => 'بالانتظار', '1' => 'جاري المراجعة', '2' => 'معتمد',
        '3' => 'مرفوض', '-1' => 'ملغى', '-2' => 'ملغى من الموارد البشرية'
    ];

    $this->load->view('templateo/employee_requests_report_view', $data);
}

public function fetch_all_requests()
{
    // 1. Security Check
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }
    
    $this->load->model('hr_model');
    
    // 2. Fetch Data from Model
    $list = $this->hr_model->get_datatables_requests();
    
    // 3. Status Mapping
    $status_map = [
        '0' => '<span class="badge bg-warning">بالانتظار</span>',
        '1' => '<span class="badge bg-info">جاري المراجعة</span>',
        '2' => '<span class="badge bg-success">معتمد</span>',
        '3' => '<span class="badge bg-danger">مرفوض</span>',
        '-1' => '<span class="badge bg-secondary">ملغى</span>',
        '-2' => '<span class="badge bg-dark">ملغى من الموارد البشرية</span>'
    ];

    // Type mapping for order_name
    $type_map = [
        '1' => 'استقالة', '2' => 'تصحيح بصمة', '3' => 'عمل إضافي',
        '4' => 'مصاريف مالية', '5' => 'إجازة', '6' => 'طلب عهدة',
        '7' => 'طلب خطاب'
    ];

    $data = [];
    foreach ($list as $request) {
        $row = [];
        
        // --- Basic Info ---
        $row['id'] = $request->id;
        $row['emp_id'] = $request->emp_id;
        $row['emp_name'] = $request->emp_name;
        $row['order_name'] = $type_map[$request->type] ?? $request->order_name; // Use type to get the name
        $row['profession'] = $request->profession ?? '—';
        
        // --- Vac Main Type / Details ---
        $row['vac_main_type'] = '';
        if ($request->type == '5') { // Vacation
            $row['vac_main_type'] = $request->vac_main_type ?? '';
            if ($request->vac_type) {
                $row['vac_main_type'] .= ' - ' . $request->vac_type;
            }
        } elseif ($request->type == '9') { // Mission
            $row['vac_main_type'] = $request->mission_type ?? 'مأمورية';
        } else {
            $row['vac_main_type'] = '—';
        }
        
        // --- Effective Start Date ---
        $row['effective_start_date'] = '';
        if ($request->type == '5' && $request->vac_start) {
            $row['effective_start_date'] = $request->vac_start;
        } elseif ($request->type == '1' && $request->date_of_the_last_working) {
            $row['effective_start_date'] = $request->date_of_the_last_working;
        } elseif ($request->type == '9' && $request->mission_date) {
            $row['effective_start_date'] = $request->mission_date;
        } elseif ($request->type == '2' && $request->correction_date) {
            $row['effective_start_date'] = $request->correction_date;
        } else {
            $row['effective_start_date'] = $request->date ?? '—';
        }
        
        // --- Effective End Date ---
        $row['effective_end_date'] = '';
        if ($request->type == '5' && $request->vac_end) {
            $row['effective_end_date'] = $request->vac_end;
        } elseif ($request->type == '1' && $request->date_of_the_last_working) {
            $row['effective_end_date'] = $request->date_of_the_last_working; // Resignation typically same day
        } elseif ($request->type == '9' && $request->mission_date) {
            $row['effective_end_date'] = $request->mission_date; // Mission might be single day
        } elseif ($request->type == '2' && $request->correction_date) {
            $row['effective_end_date'] = $request->correction_date;
        } else {
            $row['effective_end_date'] = '—';
        }
        
        // --- Details Info (Duration) ---
        $row['details_info'] = '';
        if ($request->type == '5' && $request->vac_days_count) {
            $row['details_info'] = $request->vac_days_count . ' يوم';
            if ($request->vac_half_date && $request->vac_half_period) {
                $row['details_info'] .= ' (نصف يوم - ' . $request->vac_half_period . ')';
            }
        } elseif ($request->type == '2') {
            $row['details_info'] = 'تصحيح بصمة';
        } elseif ($request->type == '3') {
            $row['details_info'] = 'عمل إضافي';
        } elseif ($request->type == '4') {
            $row['details_info'] = 'مصاريف مالية';
        } else {
            $row['details_info'] = '—';
        }
        
        // --- Creation Date ---
        $row['date'] = $request->date;
        
        // --- Status ---
        $row['status'] = $status_map[$request->status] ?? $request->status;

        $data[] = $row;
    }

    // 4. Output JSON
    $output = [
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->hr_model->count_all_requests(),
        "recordsFiltered" => $this->hr_model->count_filtered_requests(),
        "data" => $data,
    ];
    
    echo json_encode($output);
}
// In Users1.php

public function delete_violation_ajax()
{
    // Security check (Ensure only HR can delete)
    if (!$this->session->userdata('logged_in')) {
         echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
         return;
    }

    $id = $this->input->post('id');
    $this->load->model('hr_model');
    
    // Call the model function that handles both tables
    if ($this->hr_model->delete_violation($id)) {
        echo json_encode(['status' => 'success', 'message' => 'تم حذف المخالفة والخصم بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل الحذف أو السجل غير موجود']);
    }
}

public function update_violation_ajax()
{
    if (!$this->session->userdata('logged_in')) return;

    $id = $this->input->post('id');
    $data = [
        'amount' => $this->input->post('amount'),
        'violation_date' => $this->input->post('violation_date'),
        'hr_note' => $this->input->post('hr_note')
    ];

    $this->load->model('hr_model');
    
    // Call the update function
    if ($this->hr_model->update_violation_with_discount($id, $data)) {
        echo json_encode(['status' => 'success', 'message' => 'تم التحديث بنجاح']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء التحديث']);
    }
}
public function violations()
{
    // Check if the user is logged in
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }

    $this->load->model('hr_model');

    // Get the selected sheet ID and employee ID from the URL
    $sheet_id = $this->input->get('sheet_id', TRUE);
    $employee_id = $this->input->get('emp_id', TRUE);

    // Prepare the data array for the view
    $data = [
        'get_violations_summary' => [], // Start with an empty array
        'all_salary_sheets'      => $this->hr_model->get_salary_salary_sheet(),
        'selected_sheet_id'      => $sheet_id,
        'selected_emp_id'        => $employee_id,
        // ✅ ADDED: Pass CSRF security tokens to the view
        'csrf_token_name'        => $this->security->get_csrf_token_name(),
        'csrf_hash'              => $this->security->get_csrf_hash()
    ];

    // If a sheet ID was selected, then fetch the violation data
    if (!empty($sheet_id)) {
        // Pass both the sheet_id and the optional employee_id to the model
        $data['get_violations_summary'] = $this->hr_model->get_violations_summary($sheet_id, $employee_id);
    }
 
    // Assuming the view file is named 'violations_view.php'
    $this->load->view('templateo/violations', $data);
}

public function get_employee_violation_details()
{
    // Ensure this is an AJAX request
    if (!$this->input->is_ajax_request()) {
        show_404();
        return;
    }

    $this->load->model('hr_model');

    $employee_id = $this->input->post('emp_id');
    $sheet_id = $this->input->post('sheet_id');

    if (empty($employee_id) || empty($sheet_id)) {
        return $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'بيانات ناقصة.']));
    }

    // Fetch the detailed daily log using our new model function
    $daily_log = $this->hr_model->get_daily_attendance_log($employee_id, $sheet_id);

    // Send the response back to the JavaScript
    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'data' => $daily_log]));
}


    public function bulk_exempt_attendance_summary($sheet_id = null)
{
    // خذ id_sheet من الرابط
    $id_sheet = ($sheet_id !== null) ? $sheet_id : $this->uri->segment(3, 0);
    if ($id_sheet === null || $id_sheet === '') {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'msg' => 'id_sheet مفقود']));
    }

    $type = (int)$this->input->post('type');
    if (!in_array($type, [1,2,3,4], true)) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'msg' => 'نوع العملية غير مدعوم']));
    }

    $this->db->trans_start();

    // طبّق التحديث حسب النوع، والشرط على id_sheet
    $this->db->where('id_sheet', $id_sheet);

    switch ($type) {
        case 1: // single_thing فقط
            $this->db->set('single_thing', 0);
            break;

        case 2: // minutes_late + minutes_early
            $this->db->set('minutes_late', 0);
            $this->db->set('minutes_early', 0);
            break;

        case 3: // absence فقط
            $this->db->set('absence', 0);
            break;

        case 4: // الكل
            $this->db->set('absence', 0);
            $this->db->set('minutes_late', 0);
            $this->db->set('minutes_early', 0);
            $this->db->set('single_thing', 0);
            break;
    }

    $this->db->update('attendance_summary');
    $affected = $this->db->affected_rows(); // قد تكون 0 إذا القيم أصلًا صفر

    $this->db->trans_complete();

    if ($this->db->trans_status() === false) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'msg' => 'DB transaction failed']));
    }

    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'ok', 'affected' => (int)$affected]));
}








     





   public function payroll_view() {

    $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];



    $data['get_salary_sheet'] = $this->hr_model->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model->get_employees();

        
        $attendance_data = $this->hr_model->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/m4_hr', $data);
    }

public function save_attendance_summary($sheet_id = null)
{
    // Get Sheet ID from URL if not passed
    $route_id = $this->uri->segment(3, null);
    $id_sheet = ($sheet_id !== null) ? $sheet_id : $route_id;

    if ($id_sheet === null || $id_sheet === '') {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'msg' => 'id_sheet مفقود']));
    }

    // Read JSON Input
    $raw  = $this->input->raw_input_stream;
    $data = json_decode($raw, true);
    $rows = isset($data['rows']) && is_array($data['rows']) ? $data['rows'] : [];

    if (empty($rows)) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'msg' => 'لا توجد بيانات مرسلة للحفظ']));
    }

    $this->db->trans_start();

    // 1. Prepare Data & Extract IDs
    $batch = [];
    $ids_to_update = [];

    foreach ($rows as $r) {
        if (empty($r['emp_id'])) continue;
        
        $ids_to_update[] = $r['emp_id']; // Collect ID
        
        $batch[] = [
            'emp_id'        => $r['emp_id'],
            'emp_name'      => $r['emp_name'] ?? '',
            'absence'       => (int)($r['absence'] ?? 0),
            'minutes_late'  => (int)($r['minutes_late'] ?? 0),
            'minutes_early' => (int)($r['minutes_early'] ?? 0),
            'single_thing'  => (int)($r['single_thing'] ?? 0),
            'id_sheet'      => $id_sheet,
        ];
    }

    // 2. Targeted Delete (The Fix)
    // Only delete records for the employees we are about to update.
    // This leaves the OTHER company's data untouched.
    if (!empty($ids_to_update)) {
        $this->db->where('id_sheet', $id_sheet);
        $this->db->where_in('emp_id', $ids_to_update);
        $this->db->delete('attendance_summary');
    }

    // 3. Insert New Data
    $inserted_rows = 0;
    if (!empty($batch)) {
        $this->db->insert_batch('attendance_summary', $batch);
        $inserted_rows = $this->db->affected_rows();
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === false) {
        $err = $this->db->error();
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'msg' => 'Database Error: ' . $err['message']]));
    }

    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status'        => 'ok',
            'updated_count' => count($ids_to_update),
            'inserted_rows' => $inserted_rows
        ]));
}




// In application/controllers/Users1.php

public function update_salary_sheet()
{
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }

    $this->load->model('hr_model');
    
    // Server-side validation
    $this->form_validation->set_rules('type', 'اسم المسير', 'required|trim');
    $this->form_validation->set_rules('start_date', 'تاريخ البداية', 'required');
    $this->form_validation->set_rules('end_date', 'تاريخ النهاية', 'required');

    if ($this->form_validation->run() == FALSE) {
        $response = ['status' => 'error', 'message' => validation_errors()];
    } else {
        $data = [
            'type' => $this->input->post('type'),
            'start_date' => $this->input->post('start_date'),
            'end_date'   => $this->input->post('end_date'),
            'username'   => $this->session->userdata('username'), // HR's username
            'name'       => $this->session->userdata('name'),     // HR's name
            'date'       => date('Y-m-d'),
            'time'       => date('H:i:s'),
            
        ];

        // Debug: Log the data being sent
        log_message('debug', 'Salary Sheet Update Data: ' . print_r($data, true));
        log_message('debug', 'Session Data: ' . print_r($this->session->userdata(), true));

        // Call the model function to update the database
        $success = $this->hr_model->update_current_salary_sheet($data);

        if ($success) {
            $response = ['status' => 'success', 'message' => 'تم تحديث فترة مسير الرواتب بنجاح.'];
        } else {
            // Get the last database error
            $db_error = $this->db->error();
            log_message('error', 'Database Error: ' . print_r($db_error, true));
            $response = ['status' => 'error', 'message' => 'فشل تحديث البيانات في قاعدة البيانات. Error: ' . $db_error['message']];
        }
    }
    
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}
public function attendance_view() {
    // Get the salary sheet ID from the URL
    $id = $this->uri->segment(3, 0);
    $data['id'] = $id;

    // Get the salary sheet details (start/end dates)
    $data['get_salary_sheet'] = $this->hr_model->get_salary_sheet($id);

    // Get the list of employees for the report
    $employees = $this->hr_model->get_employees();
    $data['employees'] = $employees;
    
    // If there are no employees, stop here to avoid errors
    if (empty($employees)) {
        $this->load->view('templateo/m44_hr', $data);
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
        'vacations'       => [],
        'corrections'     => [],
        'rules'           => [],
        'leave_requests'  => [],
        'fp_corrections'  => [],
        'mandates'        => [], // <--- ADDED THIS
        'public_holidays' => [],
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
$this->db->where('status', 'Approved'); // Status = 'Approved' (not '2')
$all_mandate_requests = $this->db->get()->result_array();
    // --- [START] NEW CODE: Fetch Approved Mandates ---
    $this->db->select('emp_id, start_date, end_date, status');
    $this->db->from('mandate_requests');
    $this->db->where_in('emp_id', $employee_ids);
    $this->db->where('status', 'Approved'); // Ensure this matches your DB status (e.g., 'Approved' or '2')
    // Check for date overlap
    $this->db->group_start();
        $this->db->where('start_date <=', $end_date);
        $this->db->where('end_date >=', $start_date);
    $this->db->group_end();
    $all_mandates = $this->db->get()->result_array();
    // --- [END] NEW CODE ---

    // Get all work restrictions for these employees in ONE query
    $this->db->select('*');
    $this->db->from('work_restrictions');
    $this->db->where_in('emp_id', $employee_ids);
    $all_rules = $this->db->get()->result();

    // GET PUBLIC HOLIDAYS (Your existing logic)
    $holiday_dates = [];
    try {
        $this->db->select('holiday_name, holiday_date, start_date, end_date');
        $this->db->from('public_holidays');
        $public_holidays_result = $this->db->get();
        if ($public_holidays_result && $public_holidays_result->num_rows() > 0) {
            $all_holidays = $public_holidays_result->result_array();
            foreach ($all_holidays as $holiday) {
                // ... (Your existing holiday logic remains exactly the same) ...
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
    
    // --- [START] NEW CODE: Map Mandates ---
    $data_map['mandate_requests'] = [];
foreach ($all_mandate_requests as $mandate) {
    $data_map['mandate_requests'][$mandate['emp_id']][] = $mandate;
}
    // --- [END] NEW CODE ---

    foreach ($all_rules as $rule) {
        $data_map['rules'][$rule->emp_id] = $rule;
    }
    
    $data_map['exemptions'] = $this->hr_model->get_exemptions_for_employees($employee_ids);
    $data['new_year_holiday_data'] = $this->hr_model->get_new_year_holiday_data($employee_ids);
    
    // 4. Pass all the data to the view
    $data['attendance_data'] = $this->hr_model->get_attendance_data();
    $data['data_map'] = $data_map;
    
    $this->load->view('templateo/m44_hr', $data);
}
     public function payroll_view1() {

    $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];



    $data['get_salary_sheet'] = $this->hr_model->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model->get_employees();

        
        $attendance_data = $this->hr_model->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/m3_hr', $data);
    }

     public function login(){
     
      $data['title'] = 'شاشة الدخول';
   
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');
      if($this->form_validation->run() === FALSE){    
        $this->load->view('templateo/login1', $data);   
      } else {
        $username = $this->input->post('username');
        $password =MD5($this->input->post('password'));
        $user_id = $this->hr_model->login($username, $password);
        $data101 = $this->hr_model->getmydata101($username);
        $email101=$data101['email'];
        $data = $this->hr_model->getmydata($user_id);       
         $type=$data['type'];
         $status3=$data['status3'];
         $name=$data['name'];
         $suberviser=$data['suberviser'];
         $sub_id=$data['sub_id'];
         $project=$data['project'];
         $status2=$data['status2'];
         $project_name=$data['project_name'];
         $employee_classification=$data['employee_classification'];
         $id=$data['id'];
        if($user_id and $type==1){
            $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'name' => $name,
           'status3' => $status3,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name,$username); 
          $pass= rand(100000, 999999);
          $pass1 = hash('sha256', $pass);
          $this->hr_model->users_update_otp($username,$pass1);
          $data['get_users_otp'] =  $this->hr_model->get_users_otp($username); 
          $otp= $data['get_users_otp']['otp'];
          $mobile= $data['get_users_otp']['mobile'];
          $email11= $data['get_users_otp']['email']; 
          $from_email = "IT.systems@marsoom.net";
          $to_email =$email11;
          $this->load->library('email');
          $config = array();
          $config['protocol'] = 'smtp';
          $config['smtp_host'] = '10.214.25.220';
          $config['smtp_user'] = 'IT.systems@marsoom.net';
          $config['smtp_pass'] = 'Asd@123123';
          $config['smtp_port'] = 587;
          $this->email->initialize($config);
          $this->email->set_newline("\r\n");
          $this->email->from($from_email, 'COLLECTION NOTIFICATION');
          $this->email->to($to_email);
          $this->email->subject(' LOGIN NOTIFICATION ');
          $g2='  your verification code to login to marsoom Online services';  
          $this->email->message($pass.$g2);
          $this->email->send();
          if ($email11 == "saleh.alsofiany@marsoom.net") {

             redirect('users1/update_email/'.$username);

          }else
          redirect('users1/main_emp');
          }
          elseif ($user_id and ($type==2)){
             
           $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
           'name' => $name,
           'employee_classification' => $employee_classification,
           'project_name' => $project_name,
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name,$username); 
          $pass= rand(100000, 999999);
          $pass1 = hash('sha256', $pass);
          $this->hr_model->users_update_otp($username,$pass1);
          $data['get_users_otp'] =  $this->hr_model->get_users_otp($username); 
          $otp= $data['get_users_otp']['otp'];
          $mobile= $data['get_users_otp']['mobile'];
          $email11= $data['get_users_otp']['email']; 
          $from_email = "IT.systems@marsoom.net";
          $to_email =$email11;
          $this->load->library('email');
          $config = array();
          $config['protocol'] = 'smtp';
          $config['smtp_host'] = '10.214.25.220';
          $config['smtp_user'] = 'IT.systems@marsoom.net';
          $config['smtp_pass'] = 'Asd@123123';
          $config['smtp_port'] = 587;
          $this->email->initialize($config);
          $this->email->set_newline("\r\n");
          $this->email->from($from_email, 'COLLECTION NOTIFICATION');
          $this->email->to($to_email);
          $this->email->subject(' LOGIN NOTIFICATION ');
          $g2='is your verification code to login to marsoom Online services';  
          $this->email->message($pass.$g2);
          $this->email->send();
           if ($email11 == "saleh.alsofiany@marsoom.net") {

             redirect('users/update_email/'.$username);

          }else
          redirect('users1/main_emp');
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users/check_otp');
          }
          elseif ($user_id and ($type==1001)){
             
           $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
           'name' => $name,
           'employee_classification' => $employee_classification,
           'project_name' => $project_name,
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name,$username); 
          $pass= rand(100000, 999999);
          $pass1 = hash('sha256', $pass);
          $this->hr_model->users_update_otp($username,$pass1);
          $data['get_users_otp'] =  $this->hr_model->get_users_otp($username); 
          $otp= $data['get_users_otp']['otp'];
          $mobile= $data['get_users_otp']['mobile'];
          $email11= $data['get_users_otp']['email']; 
          $from_email = "IT.systems@marsoom.net";
          $to_email =$email11;
          $this->load->library('email');
          $config = array();
          $config['protocol'] = 'smtp';
          $config['smtp_host'] = '10.214.25.220';
          $config['smtp_user'] = 'IT.systems@marsoom.net';
          $config['smtp_pass'] = 'Asd@123123';
          $config['smtp_port'] = 587;
          $this->email->initialize($config);
          $this->email->set_newline("\r\n");
          $this->email->from($from_email, 'COLLECTION NOTIFICATION');
          $this->email->to($to_email);
          $this->email->subject(' LOGIN NOTIFICATION ');
          $g2='is your verification code to login to marsoom Online services';  
          $this->email->message($pass.$g2);
          $this->email->send();
           if ($email11 == "saleh.alsofiany@marsoom.net") {

             redirect('users/update_email/'.$username);

          }else
          redirect('users1/main_emp');
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users/check_otp');
          }
          elseif ($user_id and ($type==22)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name
          // 'lang'=>$this->input->post('lang'),  
        //  'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);  
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users1/main_emp');
          }elseif ($user_id and ($type==200)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name
          // 'lang'=>$this->input->post('lang'),  
         // 'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);  
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users/dashbord_analyses102');
          }
          elseif ($user_id and ($type==90)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name
          // 'lang'=>$this->input->post('lang'),  
         // 'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);  
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users1/main_emp');
          }  elseif ($user_id and ($type==8)){
            
            
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);   
           if ($status3 == 0) {
              redirect('users/re_password');
         }else  
          redirect('users1/main_emp');
          } elseif ($user_id and ($type==33)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
          'name' => $name,
          'status3' => $status3,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);  
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users1/main_emp');
          } elseif ($user_id and ($type==11)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'status3' => $status3,
           'sub_id' => $sub_id,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);   
           if ($status3 == 0) {
             redirect('users/re_password');
        }else  
          redirect('users1/main_emp');
          } elseif ($user_id and ($type==22)){
            
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
           $this->hr_model->add_watch($name,$op_name,$username); 
          $pass= rand(100000, 999999);
          $pass1 = hash('sha256', $pass);
          $this->hr_model->users_update_otp($username,$pass1);
          $data['get_users_otp'] =  $this->hr_model->get_users_otp($username); 
          $otp= $data['get_users_otp']['otp'];
          $mobile= $data['get_users_otp']['mobile'];
          $email11= $data['get_users_otp']['email']; 
          $from_email = "IT.systems@marsoom.net";
          $to_email =$email11;
          $this->load->library('email');
          $config = array();
          $config['protocol'] = 'smtp';
          $config['smtp_host'] = '10.214.25.220';
          $config['smtp_user'] = 'IT.systems@marsoom.net';
          $config['smtp_pass'] = 'Asd@123123';
          $config['smtp_port'] = 587;
          $this->email->initialize($config);
          $this->email->set_newline("\r\n");
          $this->email->from($from_email, 'COLLECTION NOTIFICATION');
          $this->email->to($to_email);
          $this->email->subject(' LOGIN NOTIFICATION ');
          $g2='  your verification code to login to marsoom Online services';  
          $this->email->message($pass.$g2);
          $this->email->send();
          redirect('users/dashbord_analyses1088'); 
           if ($status3 == 0) {
             redirect('users/re_password');
        }else    
          redirect('users1/main_emp');
          } elseif ($user_id and ($type==55)){
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'status3' => $status3,
           'sub_id' => $sub_id,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name); 
           if ($status3 == 0) {
             redirect('users/re_password');
        }else    
          redirect('users1/main_emp');
          } elseif ($user_id and ($type==66)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);
           if ($status3 == 0) {
             redirect('users/re_password');
        }else     
          redirect('users1/main_emp');
          }elseif ($user_id and ($type==5)){
              
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name); 
           if ($status3 == 0) {
             redirect('users/re_password');
        }else    
          redirect('users1/main_emp');
          }elseif ($user_id and ($type==6)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);  
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users1/main_emp');
          }elseif ($user_id and ($type==3)){
             
                  $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
           'sub_id' => $sub_id,
           'status3' => $status3,
           'name' => $name,
           'project_name' => $project_name,
           'status2' => $status2,
           // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
            $this->hr_model->add_watch($name,$op_name,$username); 
          $pass= rand(100000, 999999);
          $pass1 = hash('sha256', $pass);
          $this->hr_model->users_update_otp($username,$pass1);
          $data['get_users_otp'] =  $this->hr_model->get_users_otp($username); 
          $otp= $data['get_users_otp']['otp'];
          $mobile= $data['get_users_otp']['mobile'];
          $email11= $data['get_users_otp']['email']; 
          $from_email = "IT.systems@marsoom.net";
          $to_email =$email11;
          $this->load->library('email');
          $config = array();
          $config['protocol'] = 'smtp';
          $config['smtp_host'] = '10.214.25.220';
          $config['smtp_user'] = 'IT.systems@marsoom.net';
          $config['smtp_pass'] = 'Asd@123123';
          $config['smtp_port'] = 587;
          $this->email->initialize($config);
          $this->email->set_newline("\r\n");
          $this->email->from($from_email, 'COLLECTION NOTIFICATION');
          $this->email->to($to_email);
          $this->email->subject(' LOGIN NOTIFICATION ');
          $g2='  your verification code to login to marsoom Online services';  
          $this->email->message($pass.$g2);
          $this->email->send();
          redirect('users1/main_emp');
           if ($status3 == 0) {
             redirect('users/main33333');
        }else   

        if ($username!= 2218 ) {
           redirect('users/main33333');
        }else{
             redirect('users/dashbord_analyses103');

        }
         
          } elseif ($user_id and $type==4){
              
                   $user_data = array(
           'user_id' => $user_id,
           'username' => $username,
           'suberviser'=> $suberviser,
           'type' => $type, 
           'project' => $project, 
            'status3' => $status3,
           'sub_id' => $sub_id,
          'name' => $name,
           'project_name' => $project_name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model->add_watch($name,$op_name);  
           if ($status3 == 0) {
             redirect('users/re_password');
        }else   
          redirect('users1/main_emp');
          } 
          else{
          // Set message
          $this->session->set_flashdata('login_failed', 'Login is invalid');
                    redirect('users/login');

          }
        }
    }


    function re_pass(){
           if(!$this ->session->userdata('logged_in')){
             redirect('users/login');
                   }else{ 
                      $data['title'] = 'اضافة مستخدم جديد';

                     

 
                      $data['customers'] = $this->user_model->get_userdata_star_update();
                      $this->form_validation->set_rules('password', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){

                    
                      $this->load->view('templateo/re_pass', $data);
                   

                   
                } else {




                              $password =MD5($this->input->post('password'));
                              // $old_pass= $data['customers']['password'];
                              // if ($password == $old_pass) {
                              //      redirect('users/re_password1');
                              // }
                             // $password1 = $this->input->post('password');

                               $password =MD5($this->input->post('password'));

                            //  $password = md5('sha256', $password1);

                              


                              //echo $password;        
                              $this->user_model->my_password_update($password);
      
            redirect('users/logout');
          }
      }
    }



    function re_password(){
           if(!$this ->session->userdata('logged_in')){
             redirect('users/login');
                   }else{ 
                      $data['title'] = 'اضافة مستخدم جديد';

                     

 
                      $data['customers'] = $this->user_model->get_userdata_star_update();
                      $this->form_validation->set_rules('password', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){

                      $this->load->view('templateo/header2');
                      $this->load->view('templateo/re_password', $data);
                      $this->load->view('templateo/footer2');

                   
                } else {


                              $password =MD5($this->input->post('password'));
                              $old_pass= $data['customers']['password'];
                              if ($password == $old_pass) {
                                   redirect('users/re_password1');
                              }
                              $password1 = $this->input->post('password');
                              $password = md5('sha256', $password1);
                              //echo $password;        
                              $this->user_model->my_password_update($password);
      
            redirect('users/logout');
          }
      }
    }

     function re_password1(){
           if(!$this ->session->userdata('logged_in')){
             redirect('users/login');
                   }else{ 
                      $data['title'] = 'اضافة مستخدم جديد';

                     

 
                      $data['customers'] = $this->user_model->get_userdata_star_update();
                      $this->form_validation->set_rules('password', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){
                      
                      $this->load->view('templateo/re_password1', $data);
                      
                } else {


                              
      
            redirect('users/re_password');
          }
      }
    }




          

     







     


            
     




         
        

            
    }
    ?>