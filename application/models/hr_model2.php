<?php
    class hr_model2 extends CI_Model{
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

    ////////////////

     public function get_items101(){
        $this->db->order_by('id');
        $query = $this->db->get('items');
        return $query->result_array();
    }
// In application/models/hr_model2.php

// 1. Check if a specific payroll month is published
public function is_payroll_published($month_key)
{
    $query = $this->db->get_where('published_payrolls', ['month_key' => $month_key]);
    return $query->num_rows() > 0;
}
// --- MANDATE MOBILE FUNCTIONS ---

public function get_pending_mandates_for_mobile($approver_id) {
    // Force "Mandate" as type string
    $this->db->select('mr.id, mr.id as order_id, "Mandate" as type, "انتداب عمل" as order_name, e.subscriber_name as emp_name, mr.request_date as date, CONCAT(mr.duration_days, " أيام") as note');
    $this->db->from('approval_workflow aw');
    $this->db->join('mandate_requests mr', 'aw.order_id = mr.id');
    $this->db->join('emp1 e', 'mr.emp_id = e.employee_id');
    $this->db->where('aw.approver_id', $approver_id);
    $this->db->where('aw.status', 'Pending'); 
    $this->db->where('aw.order_type', 'Mandate'); // Make sure DB matches 'Mandate'
    return $this->db->get()->result_array();
}
public function get_mandate_details_mobile($id) {
    $this->db->select('mr.*, e.subscriber_name, e.n1, e.job_tag');
    $this->db->from('mandate_requests mr');
    $this->db->join('emp1 e', 'mr.emp_id = e.employee_id');
    $this->db->where('mr.id', $id);
    $req = $this->db->get()->row_array();
    
    if($req) {
        $req['destinations'] = $this->db->get_where('mandate_destinations', ['request_id' => $id])->result_array();
    }
    return $req;
}

public function approve_mandate_mobile($req_id, $approver_id) {
    $this->db->trans_start();
    
    // 1. Mark current workflow as Approved
    $this->db->where(['order_id'=>$req_id, 'order_type'=>'Mandate', 'approver_id'=>$approver_id, 'status'=>'Pending'])
             ->update('approval_workflow', ['status' => 'Approved', 'action_date' => date('Y-m-d H:i:s')]);

    // 2. Determine Next Step
    $next_approver = ($approver_id == '2774') ? '1693' : (($approver_id == '1693') ? null : '2774');

    if ($next_approver) {
        $this->db->where('id', $req_id)->update('mandate_requests', ['current_approver' => $next_approver]);
        $this->db->insert('approval_workflow', [
            'order_id' => $req_id, 'order_type' => 'Mandate', 'approver_id' => $next_approver, 
            'approval_level' => 2, 'status' => 'Pending'
        ]);
    } else {
        $this->db->where('id', $req_id)->update('mandate_requests', ['status' => 'Approved', 'current_approver' => 'Completed']);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}

public function reject_mandate_mobile($req_id, $approver_id, $reason) {
    $this->db->trans_start();
    $this->db->where(['order_id'=>$req_id, 'order_type'=>'Mandate', 'approver_id'=>$approver_id, 'status'=>'Pending'])
             ->update('approval_workflow', ['status' => 'Rejected', 'notes' => $reason, 'action_date' => date('Y-m-d H:i:s')]);
             
    $this->db->where('id', $req_id)->update('mandate_requests', ['status' => 'Rejected', 'current_approver' => 'Rejected']);
    $this->db->trans_complete();
    return $this->db->trans_status();
}
// 2. Get the latest published payslip for an employee
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

// 3. Fetch detailed data for the payslip view
public function get_payslip_data($employee_id, $month_key)
{
    // A. Get Payroll Calculation Data
    $this->db->where('n1', $employee_id);
    $this->db->where('n13', $month_key);
    $payroll = $this->db->get('payroll_process')->row_array();
    
    if (!$payroll) return null;

    // B. Get Static Employee Data
    $this->db->select('subscriber_name, profession, n2 as iban, n3 as bank_name, total_salary, base_salary, housing_allowance, n4 as transport_allowance, other_allowances');
    $this->db->where('employee_id', $employee_id);
    $emp = $this->db->get('emp1')->row_array();

    // C. Prepare Result Array
    return [
        'month'          => $month_key,
        'emp_name'       => $payroll['n2'], // Use snapshot name
        'emp_id'         => $payroll['n1'],
        'designation'    => $emp['profession'] ?? '',
        'generated_date' => date('Y-m-d'),
        
        // Earnings
        'basic_salary'   => (float)($emp['base_salary'] ?? 0),
        'housing'        => (float)($emp['housing_allowance'] ?? 0),
        'transport'      => (float)($emp['transport_allowance'] ?? 0),
        // n16 is reparations, n15 is backpay.
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

    public function get_pending_clearances_for_mobile($approver_id) {
    $this->db->select('
        rc.id, 
        rc.id as order_id,
        "clearance" as type,  
        COALESCE(cp.parameter_name, rc.task_description) as order_name, 
        e.subscriber_name as emp_name, 
        e.employee_id as emp_id, 
        rc.created_at as date, 
        "0" as status, -- 0 represents pending in the app logic
        CONCAT("مهمة إخلاء طرف: ", COALESCE(cp.parameter_name, "موافقة المدير")) as note,
        rc.id as approval_task_id
    ');
    $this->db->from('resignation_clearances rc');
    $this->db->join('orders_emp oe', 'rc.resignation_request_id = oe.id');
    $this->db->join('emp1 e', 'oe.emp_id = e.employee_id');
    $this->db->join('clearance_parameters cp', 'rc.clearance_parameter_id = cp.id', 'left');
    
    $this->db->where('rc.approver_user_id', $approver_id);
    $this->db->where('rc.status', 'pending');
    
    return $this->db->get()->result_array();
}

/**
 * Approve a clearance task
 */
public function approve_clearance_mobile($task_id, $approver_id) {
    $this->db->trans_start();

    // 1. Update the specific task status
    $data = [
        'status' => 'approved',
        'updated_by_user_id' => $approver_id,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $this->db->where('id', $task_id);
    $this->db->where('approver_user_id', $approver_id);
    $this->db->update('resignation_clearances', $data);

    // 2. Check if this was the last task for the resignation request
    // First get the resignation ID
    $task = $this->db->get_where('resignation_clearances', ['id' => $task_id])->row();
    
    if ($task) {
        $requestId = $task->resignation_request_id;

        // Count remaining pending or rejected tasks
        $this->db->where('resignation_request_id', $requestId);
        $this->db->where_in('status', ['pending', 'rejected']);
        $pendingCount = $this->db->count_all_results('resignation_clearances');

        // If no tasks left, mark the main resignation order as Approved (Status 2)
        if ($pendingCount === 0) {
            $this->db->where('id', $requestId);
            $this->db->update('orders_emp', ['status' => '2']); 
        }
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}

/**
 * Reject a clearance task
 */
public function reject_clearance_mobile($task_id, $approver_id, $reason) {
    $data = [
        'status' => 'rejected',
        'updated_by_user_id' => $approver_id,
        'rejection_reason' => $reason,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $this->db->where('id', $task_id);
    $this->db->where('approver_user_id', $approver_id);
    return $this->db->update('resignation_clearances', $data);
}

/**
 * Get details for the modal view
 */
public function get_clearance_details_mobile($task_id) {
    $this->db->select('
        rc.*,
        COALESCE(cp.parameter_name, rc.task_description) as task_name,
        e.subscriber_name as emp_name,
        e.employee_id as emp_code,
        oe.date_of_the_last_working,
        oe.reason_for_resignation
    ');
    $this->db->from('resignation_clearances rc');
    $this->db->join('orders_emp oe', 'rc.resignation_request_id = oe.id');
    $this->db->join('emp1 e', 'oe.emp_id = e.employee_id');
    $this->db->join('clearance_parameters cp', 'rc.clearance_parameter_id = cp.id', 'left');
    $this->db->where('rc.id', $task_id);
    
    return $this->db->get()->row_array();
}
// Add this inside class hr_model2 extends CI_Model
// ==========================================
// ADD THESE FUNCTIONS TO Users2.php
// ==========================================
public function process_approval_mobile($order_id, $approver_id)
{
    $this->db->trans_start();

    // 1. Get request details
    $request = $this->db->get_where('orders_emp', ['id' => $order_id])->row_array();
    if (!$request) {
        $this->db->trans_complete();
        return false;
    }
    $request_type = (int)$request['type'];

    // 2. Mark the CURRENT approver's task as 'approved'
    $this->db->where('order_id', $order_id);
    $this->db->where('approver_id', $approver_id);
    $this->db->where('status', 'pending');
    $this->db->update('approval_workflow', [
        'status' => 'approved', 
        'action_date' => date('Y-m-d H:i:s')
    ]);

    // 3. Check for ANY remaining steps (Pending OR Waiting)
    // This prevents skipping if the next user is currently 'waiting'
    $next_step_query = $this->db->select('*')
        ->from('approval_workflow')
        ->where('order_id', $order_id)
        ->where('order_type', $request_type)
        ->where_in('status', ['pending', 'waiting']) // <--- CRITICAL FIX
        ->order_by('approval_level', 'ASC')
        ->limit(1)
        ->get();

    if ($next_step_query->num_rows() > 0) {
        // === SCENARIO A: More Approvals Needed (Status 1) ===
        $next_step = $next_step_query->row();

        // A. Activate the next user (change from waiting to pending if needed)
        $this->db->where('id', $next_step->id);
        $this->db->update('approval_workflow', ['status' => 'pending']);

        // B. Update Main Order to Status 1
        $this->db->where('id', $order_id);
        $this->db->update('orders_emp', [
            'status' => '1', 
            'responsible_employee' => $next_step->approver_id
        ]);

    } else {
        // === SCENARIO B: No More Steps (Status 2) ===
        
        // Update Main Order to Status 2
        $this->db->where('id', $order_id);
        $this->db->update('orders_emp', [
            'status' => '2',
            'responsible_employee' => null
        ]);

        // Final Actions
        if ($request_type === 5 && method_exists($this, 'approve_leave_request')) { 
            $this->approve_leave_request($order_id); 
        } 
        elseif ($request_type === 2 && method_exists($this, 'approve_fingerprint_correction')) { 
            $this->approve_fingerprint_correction($order_id); 
        }
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}
public function approve_request($request_id) {
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->model('hr_model2');

    // 1. Direct Update: Set status to '2' (Approved) in the main table
    $this->db->where('id', $request_id);
    $update = $this->db->update('orders_emp', ['status' => '2']);

    // 2. (Optional) If you use the workflow table, update it too
    if ($this->db->table_exists('approval_workflow')) {
        $this->db->where('order_id', $request_id);
        $this->db->where('approver_id', $this->session->userdata('username'));
        $this->db->update('approval_workflow', ['status' => 'approved', 'action_date' => date('Y-m-d H:i:s')]);
    }

    if ($update) {
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'تم اعتماد الطلب بنجاح']));
    } else {
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'فشل الاعتماد']));
    }
}

public function reject_request($request_id) {
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $reason = $this->input->post('reason');
    
    // 1. Direct Update: Set status to '3' (Rejected)
    $this->db->where('id', $request_id);
    $update = $this->db->update('orders_emp', [
        'status' => '3',
        'reason_for_rejection' => $reason
    ]);

    // 2. (Optional) Workflow update
    if ($this->db->table_exists('approval_workflow')) {
        $this->db->where('order_id', $request_id);
        $this->db->where('approver_id', $this->session->userdata('username'));
        $this->db->update('approval_workflow', ['status' => 'rejected', 'notes' => $reason, 'action_date' => date('Y-m-d H:i:s')]);
    }

    if ($update) {
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'تم رفض الطلب']));
    } else {
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'فشل الرفض']));
    }
}
public function process_workflow_decision($order_id, $approver_id, $action, $reason = '') {
    $this->db->trans_start();

    // 1. Update the specific workflow row for this approver
    $status_code = ($action === 'approve') ? 'approved' : 'rejected';
    
    $data = [
        'status' => $status_code,
        'action_date' => date('Y-m-d H:i:s'),
        'notes' => $reason
    ];

    $this->db->where('order_id', $order_id);
    $this->db->where('approver_id', $approver_id);
    $this->db->where('status', 'pending');
    $this->db->update('approval_workflow', $data);

    // 2. Check if this was the final approval or if it was a rejection
    if ($action === 'reject') {
        // If rejected, the whole order is rejected (Status 3)
        $this->db->where('id', $order_id);
        $this->db->update('orders_emp', ['status' => '3', 'reason_for_rejection' => $reason]);
    } elseif ($action === 'approve') {
        // Check if there are any pending approvals left for this order with a HIGHER approval level
        // logic: If no pending steps exist, mark main order as Approved (Status 2)
        
        $this->db->where('order_id', $order_id);
        $this->db->where('status', 'pending');
        $pending_count = $this->db->count_all_results('approval_workflow');

        if ($pending_count == 0) {
            // All approved, update main table
            $this->db->where('id', $order_id);
            $this->db->update('orders_emp', ['status' => '2']);
        }
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
}
public function get_all_employees($include_deleted = false) {
        $this->db->select('employee_id as username, subscriber_name as name, status');
        $this->db->from('emp1');
        if (!$include_deleted) {
            $this->db->where('status !=', 'deleted');
        }
        $this->db->order_by('subscriber_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
// In hr_model2.php
// In hr_model2.php
public function is_mandatory_saturday($employee_id, $date)
{
    $this->db->where('employee_id', $employee_id);
    $this->db->where('saturday_date', $date);
    $query = $this->db->get('saturday_work_assignments'); // Make sure table name is correct
    return $query->num_rows() > 0;
}
public function get_todays_punches($emp_code, $date)
{
    $tables = $this->getAttendanceTables();
    if (empty($tables)) return [];

    $unions = [];
    foreach ($tables as $table) {
        $unions[] = sprintf(
            "(SELECT punch_time, punch_state FROM `%s` WHERE emp_code = %s AND DATE(punch_time) = %s)",
            $table, $this->db->escape($emp_code), $this->db->escape($date)
        );
    }
    $union_sql = implode(" UNION ALL ", $unions);

    $sql = "SELECT punch_time, punch_state FROM ($union_sql) AS U ORDER BY punch_time ASC";
    
    $query = $this->db->query($sql);
    return $query->result();
}


    function add_vacations() {
    date_default_timezone_set('Asia/Riyadh');
    
    $d = date("Y-m-d"); // It's better to use Y-m-d for database consistency
    $time = date("H:i:s"); // Use 24-hour format

    $data = array(
        'username' => $this->input->post('username'),
        'name' => $this->input->post('name'),
        'start_date' => $this->input->post('start_date'),
        'end_date' => $this->input->post('end_date'),
        'total_days' => $this->input->post('total_days'), // <-- ADD THE NEW FIELD
        'status' => '1', // Or 'pending' as used in your controller
        'date' => $d,
        'time' => $time,
        'type' => $this->input->post('type'),
        'reason' => $this->input->post('reason'), // Assuming you have these fields too
        'reentryvisa' => $this->input->post('reentryvisa'),
        'visadays' => $this->input->post('visadays')
    );

    return $this->db->insert('vacations', $data);
}
// In application/models/hr_model.php
public function insert_attendance($data) {
    return $this->db->insert('attendance_logs', $data); // Now returns TRUE or FALSE
}
public function get_all_branches()
{
    $query = $this->db->get('branches');
    return $query->result();
}
// In hr_model2.php

public function add_branch($data)
{
    return $this->db->insert('branches', $data);
}
public function delete_branch($id)
{
    $this->db->where('id', $id);
    return $this->db->delete('branches');
}
public function get_employee_for_document($employee_id)
{
    // Select all fields needed for all documents
    $this->db->select('
        employee_id,
        subscriber_name,
        id_number,
        nationality,
        profession,
        total_salary,
        joining_date,
        n2 
    ');
    $this->db->from('emp1'); 
    $this->db->where('employee_id', $employee_id);
    $query = $this->db->get();
    return $query->row();
}
public function insert_document($data) {
        return $this->db->insert('documents', $data);
    }

    /**
     * Fetches all document records for a specific employee.
     * @param int $employee_id The ID of the employee.
     * @return array|bool An array of document objects on success, FALSE on failure.
     */
    public function fetch_documents_by_employee($employee_id) {
        $this->db->where('employee_id', $employee_id);
        $this->db->order_by('upload_date', 'DESC');
        $query = $this->db->get('documents');

        if ($query) {
            return $query->result(); // Returns an array of objects
        } else {
            return false;
        }
    }

    public function get_document_by_id($id) {
        $query = $this->db->get_where('documents', ['id' => $id], 1);
        return $query->row(); // Returns a single result object
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



     public function insert_fingerprint_request($data) {
    // Start transaction for data integrity
    $this->db->trans_start();
    
    $result = $this->db->insert('fingerprint_request', $data);
    
    $this->db->trans_complete();
    
    // Log any database errors
    if (!$result || $this->db->trans_status() === FALSE) {
        $error = $this->db->error();
        log_message('error', 'Insert failed: ' . print_r($error, true));
        return false;
    }
    
    return true;
}

public function request_exists($username, $date) {
    $this->db->where('username', $username);
    $this->db->where('date', $date);
    $query = $this->db->get('fingerprint_request');
    return $query->num_rows() > 0;
}

// Add this method to your hr_model
public function get_user_correction_requests($username, $limit = null, $offset = 0) {
    $this->db->select('*');
    $this->db->from('fingerprint_request');
    $this->db->where('username', $username);
    $this->db->order_by('created_at', 'DESC');
    
    if ($limit) {
        $this->db->limit($limit, $offset);
    }
    
    $query = $this->db->get();
    return $query->result_array();
}
// Add these methods to your hr_model if not already present
public function get_correction_request($request_id, $username = null) {
    $this->db->select('*');
    $this->db->from('fingerprint_request');
    $this->db->where('id', $request_id);
    
    if ($username) {
        $this->db->where('username', $username);
    }
    
    $query = $this->db->get();
    return $query->row_array();
}

public function cancel_correction_request($request_id) {
    $data = [
        'status' => 'cancelled',
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $this->db->where('id', $request_id);
    $this->db->where('status', 'pending'); // Only allow cancelling pending requests
    
    return $this->db->update('fingerprint_request', $data);
}
/**
 * Insert vacation request
 */


/**
 * Get user's vacation requests
 */

public function get_user_vacation_requests($username, $limit = null, $offset = 0) {
    try {
        $this->db->select('*');
        $this->db->from('vacations');
        $this->db->where('username', $username);
        
        // Try to order by created_at first, then fall back to date/time
        if ($this->db->field_exists('created_at', 'vacations')) {
            $this->db->order_by('created_at', 'DESC');
        } else {
            $this->db->order_by('date DESC, time DESC');
        }
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        log_message('debug', "Found " . count($result) . " vacation requests for user: {$username}");
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in get_user_vacation_requests: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get specific vacation request
 */
public function get_vacation_request($request_id, $username = null) {
    try {
        $this->db->select('*');
        $this->db->from('vacations');
        $this->db->where('id', $request_id);
        
        if ($username) {
            $this->db->where('username', $username);
        }
        
        $query = $this->db->get();
        return $query->row_array();
        
    } catch (Exception $e) {
        log_message('error', 'Exception in get_vacation_request: ' . $e->getMessage());
        return null;
    }
}

/**
 * Cancel vacation request
 */
public function cancel_vacation_request($request_id) {
    try {
        $data = [
            'status' => 'cancelled'
        ];
        
        $this->db->where('id', $request_id);
        $this->db->where('status', 'pending'); // Only allow cancelling pending requests
        
        $result = $this->db->update('vacations', $data);
        
        log_message('debug', "Cancel vacation request {$request_id}: " . ($result ? 'success' : 'failed'));
        
        return $result && $this->db->affected_rows() > 0;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in cancel_vacation_request: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check if vacation request overlaps with existing requests
 */
public function vacation_request_overlaps($username, $start_date, $end_date) {
    try {
        $this->db->where('username', $username);
        $this->db->where('status !=', 'cancelled');
        $this->db->where('status !=', 'rejected');
        
        // Check for overlapping dates
        $this->db->group_start();
        $this->db->where('start_date <=', $end_date);
        $this->db->where('end_date >=', $start_date);
        $this->db->group_end();
        
        $query = $this->db->get('vacations');
        $count = $query->num_rows();
        
        log_message('debug', "Checking overlapping vacation requests for {$username}: found {$count} overlapping requests");
        
        return $count > 0;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in vacation_request_overlaps: ' . $e->getMessage());
        return false;
    }
}

/**
 * Insert vacation request
 */
public function insert_vacation_request($data) {
    try {
        $result = $this->db->insert('vacations', $data);
        
        if ($result) {
            log_message('debug', "Vacation request inserted successfully with ID: " . $this->db->insert_id());
        } else {
            log_message('error', "Failed to insert vacation request: " . print_r($this->db->error(), true));
        }
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in insert_vacation_request: ' . $e->getMessage());
        return false;
    }
}
/**
 * Insert overtime request
 */
public function insert_overtime_request($data) {
    try {
        // Add creation timestamp
        $data['create_date'] = date('Y-m-d H:i:s');
        
        $result = $this->db->insert('overtime_request', $data);
        
        if ($result) {
            $insert_id = $this->db->insert_id();
            log_message('debug', "Overtime request inserted successfully with ID: " . $insert_id);
            return $insert_id;
        } else {
            log_message('error', "Failed to insert overtime request: " . print_r($this->db->error(), true));
            return false;
        }
        
    } catch (Exception $e) {
        log_message('error', 'Exception in insert_overtime_request: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get user's overtime requests
 */
public function get_user_overtime_requests($username, $limit = null, $offset = 0) {
    try {
        $this->db->select('*');
        $this->db->from('overtime_request');
        $this->db->where('username', $username);
        $this->db->order_by('create_date', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        log_message('debug', "Found " . count($result) . " overtime requests for user: {$username}");
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in get_user_overtime_requests: ' . $e->getMessage());
        return [];
    }
}

/**
 * Check if overtime request exists for same date
 */
public function overtime_request_exists($username, $date) {
    try {
        $this->db->where('username', $username);
        $this->db->where('date', $date);
        $this->db->where('status !=', 'cancelled');
        
        $query = $this->db->get('overtime_request');
        $count = $query->num_rows();
        
        log_message('debug', "Checking existing overtime request for {$username} on {$date}: found {$count} requests");
        
        return $count > 0;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in overtime_request_exists: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get specific overtime request
 */
public function get_overtime_request($request_id, $username = null) {
    try {
        $this->db->select('*');
        $this->db->from('overtime_request');
        $this->db->where('id', $request_id);
        
        if ($username) {
            $this->db->where('username', $username);
        }
        
        $query = $this->db->get();
        return $query->row_array();
        
    } catch (Exception $e) {
        log_message('error', 'Exception in get_overtime_request: ' . $e->getMessage());
        return null;
    }
}

/**
 * Cancel overtime request
 */
public function cancel_overtime_request($request_id) {
    try {
        $data = [
            'status' => 'cancelled'
        ];
        
        $this->db->where('id', $request_id);
        $this->db->where('status', 'pending');
        
        $result = $this->db->update('overtime_request', $data);
        
        log_message('debug', "Cancel overtime request {$request_id}: " . ($result ? 'success' : 'failed'));
        
        return $result && $this->db->affected_rows() > 0;
        
    } catch (Exception $e) {
        log_message('error', 'Exception in cancel_overtime_request: ' . $e->getMessage());
        return false;
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

    public function get_violations_summary($id)
{
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
        ORDER BY emp_id
    ";
    return $this->db->query($sql, [$id])->result();
}


     function get_salary_sheet($id){
      
        $sql = "select * from salary_sheet where id=$id;";
        $query = $this->db->query($sql);
        return $query->row_array();
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

    function get_salary_salary_sheet(){
      
        $sql = "select * from salary_sheet;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    


    function get_emp1(){
      
        $sql = "select * from emp1 ;";
        $query = $this->db->query($sql);
        return $query->result_array();
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




public function get_orders_for_employee($emp_id, $filters = []) {
    $this->db->from('orders_emp');
    $this->db->where('emp_id', $emp_id);

    // ✅ FIXED: Use isset() and check for non-empty string for all filters
    if (isset($filters['type']) && $filters['type'] !== '') {
        $this->db->where('type', $filters['type']);
    }
    
    if (isset($filters['status']) && $filters['status'] !== '') {
        $this->db->where('status', $filters['status']);
    }

    if (isset($filters['start_date']) && $filters['start_date'] !== '') {
        $this->db->where('date >=', $filters['start_date']);
    }
    
    if (isset($filters['end_date']) && $filters['end_date'] !== '') {
        $this->db->where('date <=', $filters['end_date']);
    }

    $this->db->order_by('id', 'DESC');
    return $this->db->get()->result_array();
}
// In hr_model2.php
// REPLACE WITH THIS:
public function get_single_request_details($order_id) {
    // 1. Select all columns from orders_emp, plus the name from emp1
    $this->db->select('orders_emp.*, del_emp.subscriber_name as delegation_employee_name');
    $this->db->from('orders_emp');
    
    // 2. Join the emp1 table to get the delegated employee's actual name
    $this->db->join('emp1 del_emp', 'orders_emp.delegation_employee_id = del_emp.employee_id', 'left');
    
    $this->db->where('orders_emp.id', $order_id);
    return $this->db->get()->row_array();
}
// In hr_model2.php, add these three new functions

/**
 * Fetches all necessary profile data for the mobile view from the emp1 table.
 * @param string $employee_id
 * @return array|null
 */
public function mobile_get_employee_profile($employee_id) {
    $this->db->from('emp1');
    $this->db->where('employee_id', $employee_id);
    return $this->db->get()->row_array();
}

/**
 * Fetches contract-specific details.
 * @param string $employee_id
 * @return array|null
 */
public function mobile_get_employee_contract($employee_id) {
    $this->db->select('contract_status, contract_period, contract_start, contract_end');
    $this->db->from('emp1');
    $this->db->where('employee_id', $employee_id);
    return $this->db->get()->row_array();
}

/**
 * Fetches all leave balances for the current year for the mobile view.
 * @param string $employee_id
 * @return array
 */
public function mobile_get_employee_balances($employee_id) {
    $this->db->select("elb.*, lt.name_ar AS leave_type_name");
    $this->db->from('employee_leave_balances AS elb');
    $this->db->join('leave_types AS lt', 'lt.slug = elb.leave_type_slug', 'left');
    $this->db->where('elb.employee_id', $employee_id);
    $this->db->where('elb.year', date('Y'));
    $this->db->order_by('lt.name_ar', 'ASC');
    return $this->db->get()->result_array();
}
public function get_pending_approvals_for_user($approver_id) {
        // 1. Standard Requests
        $this->db->select('
            oe.id, 
            oe.type, 
            oe.order_name, 
            oe.emp_name, 
            oe.emp_id, 
            oe.date, 
            oe.status, 
            oe.note,
            aw.id as approval_task_id
        ');
        $this->db->from('approval_workflow as aw');
        $this->db->join('orders_emp as oe', 'aw.order_id = oe.id AND aw.order_type = oe.type');
        $this->db->where('aw.approver_id', $approver_id);
        $this->db->where('aw.status', 'pending');
        // Filter out if a lower level is still pending (sequential approval)
        $this->db->where("NOT EXISTS (
            SELECT 1 FROM approval_workflow aw2 
            WHERE aw2.order_id = aw.order_id 
            AND aw2.approval_level < aw.approval_level 
            AND aw2.status = 'pending'
        )", NULL, FALSE);
        
        $standard_requests = $this->db->get()->result_array();

        // 2. End of Service Settlements
        $this->db->select('
            eos.id, 
            8 as type, 
            "مستحقات نهاية الخدمة" as order_name, 
            e.subscriber_name as emp_name, 
            eos.employee_id as emp_id, 
            eos.created_at as date, 
            eos.status, 
            CONCAT("المبلغ النهائي: ", eos.final_amount) as note,
            aw.id as approval_task_id
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
        
        $eos_requests = $this->db->get()->result_array();

        return array_merge($standard_requests, $eos_requests);
    }

    // NEW FUNCTION: Get EOS Details for Modal
    public function get_eos_request_details($settlement_id) {
        $this->db->select('
            eos.*, 
            e.subscriber_name as emp_name, 
            e.joining_date, 
            u.name as creator_name
        ');
        $this->db->from('end_of_service_settlements eos');
        $this->db->join('emp1 e', 'eos.employee_id = e.employee_id', 'left');
        $this->db->join('users u', 'eos.created_by_id = u.username', 'left');
        $this->db->where('eos.id', $settlement_id);
        
        return $this->db->get()->row_array();
    }

    // NEW FUNCTION: Approve EOS
    public function approve_settlement_mobile($settlement_id, $approver_id) {
        $this->db->trans_start();

        // 1. Update Workflow
        $this->db->where('order_id', $settlement_id);
        $this->db->where('order_type', 8);
        $this->db->where('approver_id', $approver_id);
        $this->db->where('status', 'pending');
        $this->db->update('approval_workflow', [
            'status' => 'approved',
            'action_date' => date('Y-m-d H:i:s')
        ]);

        // 2. Find Next Approver
        // Get current level first to be safe, or just look for 'waiting'
        $next_task = $this->db->from('approval_workflow')
            ->where('order_id', $settlement_id)
            ->where('order_type', 8)
            ->where('status', 'waiting') // Find waiting tasks
            ->order_by('approval_level', 'ASC')
            ->limit(1)
            ->get()->row();

        if ($next_task) {
            // Activate next
            $this->db->where('id', $next_task->id);
            $this->db->update('approval_workflow', ['status' => 'pending']);

            // Update Main Table
            $this->db->where('id', $settlement_id);
            $this->db->update('end_of_service_settlements', [
                'status' => 'pending_level_' . $next_task->approval_level,
                'current_approver' => $next_task->approver_id
            ]);
        } else {
            // Final Approval
            $this->db->where('id', $settlement_id);
            $this->db->update('end_of_service_settlements', [
                'status' => 'approved',
                'current_approver' => null
            ]);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // NEW FUNCTION: Reject EOS
    public function reject_settlement_mobile($settlement_id, $approver_id, $reason) {
        $this->db->trans_start();

        // 1. Update Workflow
        $this->db->where('order_id', $settlement_id);
        $this->db->where('order_type', 8);
        $this->db->where('approver_id', $approver_id);
        $this->db->where('status', 'pending');
        $this->db->update('approval_workflow', [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'action_date' => date('Y-m-d H:i:s')
        ]);

        // 2. Update Main Table
        $this->db->where('id', $settlement_id);
        $this->db->update('end_of_service_settlements', [
            'status' => 'rejected',
            'current_approver' => null
        ]);
        
        // 3. Skip remaining
        $this->db->where('order_id', $settlement_id)
                 ->where('order_type', 8)
                 ->where('status', 'waiting')
                 ->update('approval_workflow', ['status' => 'skipped']);

        $this->db->trans_complete();
        return $this->db->trans_status();
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
// In hr_model2.php

public function get_leave_types($gender = null) {
    // ✨ FIX: Add default_balance and is_gender_specific to the select statement
    $this->db->select('slug, name_ar, default_balance, is_gender_specific');
    $this->db->from('leave_types');
    $this->db->where('is_active', TRUE);

    // This is the corrected logic to handle gender filtering reliably.
    if ($gender) {
        $this->db->group_start();
        $this->db->where('is_gender_specific IS NULL', null, false); // Add "IS NULL" clause
        $this->db->or_where('is_gender_specific', $gender);
        $this->db->group_end();
    }
    
    $query = $this->db->get();
    return $query->result_array();
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
// In hr_model2.php, add these new functions
private function get_attendance_tables(): array
{
    // This helper function gets all your attendance log table names
    $tables = [];
    $query = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE 'attendance_logs%'");
    foreach ($query->result_array() as $row) {
        $tables[] = $row['TABLE_NAME'];
    }
    return $tables;
}

public function get_attendance_map_for_month($emp_id, $year, $month)
{
    $start_date = "$year-$month-01";
    $end_date = date("Y-m-t", strtotime($start_date));
    $tables = $this->get_attendance_tables();
    if (empty($tables)) return [];

    $unions = [];
    foreach ($tables as $table) {
        $unions[] = sprintf(
            "(SELECT punch_time FROM `%s` WHERE emp_code = %s AND punch_time BETWEEN %s AND %s)",
            $table, $this->db->escape($emp_id), $this->db->escape("$start_date 00:00:00"), $this->db->escape("$end_date 23:59:59")
        );
    }
    $union_sql = implode(" UNION ALL ", $unions);

    $sql = "SELECT 
                DATE(punch_time) AS day,
                MIN(punch_time) AS first_in,
                MAX(punch_time) AS last_out
            FROM ($union_sql) AS U
            GROUP BY DATE(punch_time)";
    
    $query = $this->db->query($sql);
    $map = [];
    foreach ($query->result_array() as $row) {
        $map[$row['day']] = $row;
    }
    return $map;
}

// In hr_model2.php
public function get_events_for_month($emp_id, $year, $month)
{
    $start_date = "$year-$month-01";
    $end_date = date("Y-m-t", strtotime($start_date));
    
    $this->db->from('orders_emp');
    $this->db->where('emp_id', $emp_id);
    $this->db->where_in('type', [2, 5]); // 2=Correction, 5=Leave
    
    // ✅ **THIS IS THE FIX**
    // We ONLY fetch approved requests. status '2' = Approved.
    $this->db->where('status', '2'); 
    
    // This query finds all approved leaves that overlap the month OR approved corrections within the month
    $this->db->where("( (vac_start <= '$end_date' AND vac_end >= '$start_date') OR (correction_date BETWEEN '$start_date' AND '$end_date') )", NULL, FALSE);
    $query = $this->db->get();

    $events = [];
    foreach ($query->result_array() as $row) {
        $event_type = '';
        
        if((int)$row['type'] === 5 && !empty($row['vac_start'])) { // Leave
            $period = new DatePeriod(new DateTime($row['vac_start']), new DateInterval('P1D'), (new DateTime($row['vac_end']))->modify('+1 day'));
            
            foreach($period as $date){
                $dateStr = $date->format('Y-m-d');
                // Only add event if it's within the current month's view
                if ($dateStr >= $start_date && $dateStr <= $end_date) {
                    $type = ($row['vac_half_date'] == $dateStr) ? 'vac_half' : 'vac_full';
                    $events[$dateStr][] = array_merge($row, ['type' => $type, 'title' => $row['order_name']]);
                }
            }
        } elseif ((int)$row['type'] === 2 && !empty($row['correction_date'])) { // Correction
            $correctionDateStr = $row['correction_date'];
            $events[$correctionDateStr][] = array_merge($row, ['type' => 'corr', 'title' => $row['order_name']]);
        }
    }
    return $events;
}

// In hr_model2.php
public function get_monthly_attendance_summary($emp_id, $year, $month)
{
    $start_date = "$year-$month-01";
    $end_date = date("Y-m-t", strtotime($start_date));

    // Fetch all necessary data at once
    $attendance_map = $this->get_attendance_map_for_month($emp_id, $year, $month);
    $events_map = $this->get_events_for_month($emp_id, $year, $month); // Now ONLY contains approved events
    $holidays_map = $this->get_holidays_for_month($year, $month);
    
    $results = [];
    $current = new DateTime($start_date);
    $end = new DateTime($end_date);
    $today = new DateTime('today');

    while($current <= $end) {
        $dateStr = $current->format('Y-m-d');
        $dayOfWeek = $current->format('w'); // 0=Sun, 5=Fri, 6=Sat
        $entry = ['date' => $dateStr];
        
        $is_saturday = ($dayOfWeek == 6);
        $is_mandatory = false;
        if ($is_saturday) {
            if (method_exists($this, 'is_mandatory_saturday')) {
                 $is_mandatory = $this->is_mandatory_saturday($emp_id, $dateStr); 
            }
        }

        // --- START OF LOGIC CHAIN ---

        // 1. Check for Public Holiday.
        if (isset($holidays_map[$dateStr])) {
            $entry['status'] = 'holiday';
            $entry['status_text'] = $holidays_map[$dateStr];
        }
        
        // 2. Check for an approved event (Leave or Correction).
        else if (isset($events_map[$dateStr])) {
            $event_details = $events_map[$dateStr][0]; 
            $entry['event_details'] = $event_details; 

            if ($event_details['type'] == 'vac_half') {
                $entry['status'] = 'leave';
                $entry['status_text'] = 'إجازة نصف يوم';
                $entry['has_leave'] = true;
            } elseif ($event_details['type'] == 'vac_full') {
                $entry['status'] = 'leave';
                $entry['status_text'] = $event_details['title'] ?: 'إجازة';
                $entry['has_leave'] = true;
            } elseif ($event_details['type'] == 'corr') {
                $entry['status'] = 'present'; 
                $entry['status_text'] = 'حاضر (مع تصحيح)';
            }
        }
        
        // 3. Check if it's an ASSIGNED Saturday (and not a holiday/leave).
        else if ($is_mandatory) {
            $entry['status'] = 'saturday_work'; 
            $entry['status_text'] = 'عمل يوم سبت';

            if (isset($attendance_map[$dateStr])) {
                $att = $attendance_map[$dateStr];
                $entry['check_in'] = date('h:i A', strtotime($att['first_in']));
                $entry['check_out'] = ($att['last_out'] != $att['first_in']) ? date('h:i A', strtotime($att['last_out'])) : '--:--';
                
                if ($entry['check_out'] !== '--:--') {
                    $diff = strtotime($att['last_out']) - strtotime($att['first_in']);
                    $entry['worked'] = gmdate("H:i", $diff);
                    $entry['difference'] = gmdate("H:i", $diff); 
                } else {
                    $entry['worked'] = '00:00';
                    $entry['difference'] = '00:00';
                }
            } else {
                if ($current < $today) {
                    $entry['status'] = 'absent';
                    $entry['status_text'] = 'غياب (عن يوم عمل سبت)';
                } else {
                    $entry['check_in'] = '--:--';
                    $entry['check_out'] = '--:--';
                    $entry['worked'] = '--:--';
                }
            }
        }
        
        // 4. Check if it's a REGULAR weekend (Fri/Sat)
        else if ($dayOfWeek == 5 || $dayOfWeek == 6) {
            $entry['status'] = 'weekend';
            $entry['status_text'] = 'نهاية الأسبوع';
        }
        
        // 5. Check for attendance (ONLY if status isn't set by rules above).
        //    This block will now be skipped if $entry['status'] was set to 'leave' in step 2.
        else if (isset($attendance_map[$dateStr])) {
            $att = $attendance_map[$dateStr];
            $entry['status'] = 'present';
            $entry['check_in'] = date('h:i A', strtotime($att['first_in']));
            $entry['check_out'] = ($att['last_out'] != $att['first_in']) ? date('h:i A', strtotime($att['last_out'])) : '--:--';
            
            if ($entry['check_out'] !== '--:--') {
                $diff = strtotime($att['last_out']) - strtotime($att['first_in']);
                $entry['worked'] = gmdate("H:i", $diff);
                $diff_from_9hrs = $diff - (9 * 3600); 
                $sign = $diff_from_9hrs >= 0 ? '+' : '-';
                $entry['difference'] = $sign . ' ' . gmdate("H:i", abs($diff_from_9hrs));
            }
        }
        
        // 6. If it's a past weekday with no other status, mark as absent.
        else if ($current < $today) {
            $entry['status'] = 'absent';
            $entry['status_text'] = 'غياب';
        } 
        
        // 7. Otherwise, it's a future day.
        else {
            $entry['status'] = 'future';
            $entry['status_text'] = '';
        }
        // --- END OF LOGIC CHAIN ---
        
        
        // --- POST-PROCESSING FOR CORRECTIONS ---
        if ($entry['status'] === 'present' && isset($entry['event_details']) && $entry['event_details']['type'] == 'corr') {
            $corrected_in = null;
            $corrected_out = null;
            
            if (!empty($entry['event_details']['attendance_correction'])) {
                 $corrected_in = $entry['event_details']['attendance_correction'];
                 $entry['corrected_check_in'] = date('h:i A', strtotime($corrected_in . ':00'));
                 $entry['check_in'] = $entry['corrected_check_in'];
            }
            if (!empty($entry['event_details']['correction_of_departure'])) {
                 $corrected_out = $entry['event_details']['correction_of_departure'];
                 $entry['corrected_check_out'] = date('h:i A', strtotime($corrected_out . ':00'));
                 $entry['check_out'] = $entry['corrected_check_out'];
            }
            
            if ($corrected_in && $corrected_out) {
                $corr_in_ts = strtotime($corrected_in . ':00');
                $corr_out_ts = strtotime($corrected_out . ':00');
                
                if ($corr_out_ts > $corr_in_ts) {
                    $diff = $corr_out_ts - $corr_in_ts;
                    $entry['worked'] = gmdate("H:i", $diff);
                    $diff_from_9hrs = $diff - (9 * 3600);
                    $sign = $diff_from_9hrs >= 0 ? '+' : '-';
                    $entry['difference'] = $sign . ' ' . gmdate("H:i", abs($diff_from_9hrs));
                } else {
                    $entry['worked'] = '00:00';
                    $entry['difference'] = '- 09:00';
                }
            } else if (!isset($entry['worked'])) {
                 $entry['worked'] = '--:--';
                 $entry['difference'] = '--:--';
            }
        }

        $results[] = $entry;
        $current->modify('+1 day');
    }
    return $results;
}
// In hr_model2.php

public function get_saturday_assignments_for_employee($employee_id) {
    $this->db->select('saturday_date');
    $this->db->where('employee_id', $employee_id);
    // You can add a filter for the current year if you want, e.g.:
    // $this->db->where('YEAR(saturday_date)', date('Y'));
    $query = $this->db->get('saturday_work_assignments');
    
    // Returns a simple array like ['2025-10-18', '2025-10-25']
    return array_column($query->result_array(), 'saturday_date');
}
public function get_holidays_for_month($year, $month)
{
    $start_date = "$year-$month-01";
    $end_date = date("Y-m-t", strtotime($start_date));
    $this->db->select('holiday_date, holiday_name')->from('public_holidays')->where('holiday_date >=', $start_date)->where('holiday_date <=', $end_date);
    $query = $this->db->get();
    $map = [];
    foreach ($query->result_array() as $row) {
        $map[$row['holiday_date']] = $row['holiday_name'];
    }
    return $map;
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
public function get_all_holidays_as_dates()
{
    $query = $this->db->select('holiday_date')->get('public_holidays');
    return array_column($query->result_array(), 'holiday_date');
}
public function getAttendanceForDate($emp_code, $date) {
    
    
    $checkIns = [];
    $checkOuts = [];
    $tables = $this->getAttendanceTables(); // Use a helper method

    // Simplified loop - you might want to fetch both in one query per table
    foreach ($tables as $table) {
        if ($this->db->table_exists($table)) {
            // Check In
            $this->db->select('punch_time');
            $this->db->from($table);
            $this->db->where('emp_code', $emp_code);
            $this->db->where("DATE(punch_time) =", $date);
            $queryIn = $this->db->get();
            if ($queryIn->num_rows() > 0) {
                foreach ($queryIn->result() as $row) {
                    $checkIns[] = $row->punch_time;
                }
            }

            // Check Out (redundant query, better to fetch all and differentiate)
            // For simplicity here, let's assume check-in and check-out are not explicitly marked by a 'punch_state' field in your provided snippets
            // If there's a 'punch_state' or similar, add it to the WHERE clause.
            // If not, you'd rely on sorting by punch_time to differentiate.
            // For now, let's query for all punches and then sort them.

            $this->db->select('punch_time');
            $this->db->from($table);
            $this->db->where('emp_code', $emp_code);
            $this->db->where("DATE(punch_time) =", $date);
            $queryAll = $this->db->get();
            if ($queryAll->num_rows() > 0) {
                foreach ($queryAll->result() as $row) {
                    $checkIns[] = $row->punch_time; // Assume all are check-ins for sorting, we'll sort to find first/last
                }
            }
        }
    }
    
    // Remove duplicates and sort
    $allPunches = array_unique($checkIns); // Assuming checkIns collected all punches
    if (!empty($allPunches)) {
        usort($allPunches, function($a, $b) {
            return strtotime($a) - strtotime($b);
        });
        
        $firstCheckIn = $allPunches[0];
        $lastCheckOut = $allPunches[count($allPunches) - 1]; // Last one is checkout
    } else {
        $firstCheckIn = null;
        $lastCheckOut = null;
    }

    // Calculate durations
    $workedHours = '00:00:00';
    $timeDifference = '00:00:00';

    if ($firstCheckIn && $lastCheckOut) {
        $checkInTime = strtotime($firstCheckIn);
        $checkOutTime = strtotime($lastCheckOut);
        if ($checkInTime !== false && $checkOutTime !== false && $checkOutTime > $checkInTime) {
            $secondsDiff = $checkOutTime - $checkInTime;
            $workedHours = gmdate('H:i:s', $secondsDiff);
            $timeDifference = $workedHours;
        }
    }

    return [
        'date' => $date, // Add the date for context
        'firstCheckIn' => $firstCheckIn,
        'lastCheckOut' => $lastCheckOut,
        'workDuration' => $workedHours,
        'timeDifference' => $timeDifference
    ];
}

// New method to get attendance summary for a range of dates
public function getAttendanceInRange($emp_code, $startDate, $endDate) {
    $attendanceSummary = [];
    $currentDate = new DateTime($startDate);
    $endDateObj = new DateTime($endDate);

    $tables = $this->getAttendanceTables();

    // Loop through each day in the range
    while ($currentDate <= $endDateObj) {
        $dateStr = $currentDate->format('Y-m-d');
        $hasAttendance = false;

        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->select('punch_time');
                $this->db->from($table);
                $this->db->where('emp_code', $emp_code);
                $this->db->where("DATE(punch_time) =", $dateStr);
                $this->db->limit(1); // We only need to know if *any* record exists for the day
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $hasAttendance = true;
                    break; // Found attendance for this day, no need to check other tables
                }
            }
        }

        $attendanceSummary[] = ['date' => $dateStr, 'has_attendance' => $hasAttendance];
        $currentDate->modify('+1 day');
    }

    return $attendanceSummary;
}
public function get_employee_personal_details($employee_id)
    {
        // Adjust the selected column names to match your 'emp1' table
        $this->db->select('
            employee_id,
            subscriber_name,
            gender,
            email,
            marital,
            birth_date,
            phone,
            nationality,
            religion,
            id_number,
            id_expiry,
            address,
            n3,
            n2
        ');
        $this->db->from('emp1');
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row(); // Return a single result object
        }

        return null; // Return null if no employee is found
    }
public function get_employee_job_details($employee_id)
    {
        // Adjust the selected column names to match your 'emp1' table
        $this->db->select('
            joining_date,
            profession,
            type,
            n1,
            company_name,
            location,
            manager
        ');
        $this->db->from('emp1');
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row(); // Return a single result object
        }

        return null; // Return null if no employee is found
    }   
     public function get_employee_financial_details($employee_id)
    {
        // Adjust these column names to match your 'emp1' table
        $this->db->select('
            base_salary,
            housing_allowance,
            other_allowances,
            n4,
            total_salary
        ');
        $this->db->from('emp1');
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return null;
    }

    public function get_employee_contract_details($employee_id)
    {
        // Adjust these column names to match your 'emp1' table
        $this->db->select('
            contract_status,
            contract_period,
            contract_start,
            contract_end
        ');
        $this->db->from('emp1');
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return null;
    }
// Helper to get table list
protected function getAttendanceTables() {
    return [
        'attendance_logs',
        'attendance_logs_af4c194960167',
        'attendance_logs_af4c203360515',
        'attendance_logs_af4c214560921',
        'attendance_logs_af4c223260066',
        'attendance_logs_osa7010056122201235',
        'attendance_logs_rkq4235000038',
        'attendance_logs_rkq4241900202'
    ];
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