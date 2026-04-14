	<?php
	class Users2 extends CI_Controller{
        public function __construct() {
        parent::__construct();
        
        // This function will crash if any of these are missing.
        $this->load->model('hr_model2');
        $this->load->library(['session', 'upload', 'form_validation']);
        $this->load->helper(['url', 'form', 'security']);
    }

    function index1(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{
             $this->load->view('templateo/header');  
             $this->load->view('templateo/index1');   


          }
          
      }
   public function mobile_check_last_payslip_status()
{
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }

    $emp_id = $this->session->userdata('username');
    $this->load->model('hr_model2');
    
    $slip = $this->hr_model2->get_latest_published_payslip($emp_id);
    
    if ($slip) {
        // 1. Safe Encode
        $base64 = base64_encode($slip['month']);
        $safe_url = str_replace(['+', '/', '='], ['-', '_', ''], $base64);

        echo json_encode([
            'status' => 'success', 
            'data' => [
                'month' => $slip['month'],
                // 2. Use the safe URL
                'download_url' => site_url('users2/mobile_view_payslip/' . $safe_url)
            ]
        ]);
    } else {
        echo json_encode(['status' => 'success', 'data' => null]);
    }
}
public function mobile_view_payslip($safe_url = null)
{
    if (!$this->session->userdata('logged_in')) redirect('users/login');

    $this->load->model('hr_model2');
    $emp_id = $this->session->userdata('username');
    
    $month_key = null;

    if ($safe_url) {
        // 1. Safe Decode
        $base64 = str_replace(['-', '_'], ['+', '/'], $safe_url);
        $month_key = base64_decode($base64);
    }

    // Fallback if decode failed or no URL provided (get latest)
    if (!$month_key) {
        $latest = $this->hr_model2->get_latest_published_payslip($emp_id);
        if ($latest) {
             $month_key = $latest['month']; 
        } else { 
             show_error('No payslip found.', 404); 
             return; 
        }
    }

    // Get Data
    $data = $this->hr_model2->get_payslip_data($emp_id, $month_key);
    if (!$data) { show_error('Data not found.', 404); return; }

    // Pass the safe URL to the view so the "Print" button also works
    // We re-encode just in case we fell back to the latest month logic
    $base64_re = base64_encode($month_key);
    $safe_url_re = str_replace(['+', '/', '='], ['-', '_', ''], $base64_re);
    $data['safe_url_param'] = $safe_url_re;

    $this->load->view('templateo/mobile_payslip_view', $data);
}
// 2. Print View (The A4/PDF version)
public function mobile_print_payslip($safe_url = null)
{
    if (!$this->session->userdata('logged_in')) redirect('users/login');

    if (empty($safe_url)) {
         show_error('Invalid request.', 404); return;
    }

    // 1. Safe Decode
    $base64 = str_replace(['-', '_'], ['+', '/'], $safe_url);
    $month_key = base64_decode($base64);

    if (!$month_key) {
         show_error('Invalid ID.', 404); return;
    }

    $this->load->model('hr_model2');
    $emp_id = $this->session->userdata('username');

    $data = $this->hr_model2->get_payslip_data($emp_id, $month_key);
    
    if (!$data) {
         show_error('Payslip data not found.', 404); return;
    }
    
    // Convert Logo to Base64 for Printing
    $logo_path = FCPATH . 'assets/images/logo.png'; 
    $logo_base64 = '';
    if (file_exists($logo_path)) {
        $type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $data_img = file_get_contents($logo_path);
        $logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
    }
    $data['logo_base64'] = $logo_base64;

    $this->load->view('templateo/payslip_print_view', $data);
}
// In Users2.php
public function update_order_status_mobile()
{
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }

    $this->load->model('hr_model2');
    $this->load->model('hr_model'); // 🌟 LOAD DESKTOP MODEL TO REUSE EMAILS

    $id = $this->input->post('id');
    $status = $this->input->post('status'); 
    $user_id = $this->session->userdata('username');
    $reason = $this->input->post('reason');

    if ($status == '2') {
        $result = $this->hr_model2->process_approval_mobile($id, $user_id);
        
        if ($result) {
            $this->hr_model->trigger_next_approval_email($id, $user_id); // Trigger Email
            echo json_encode(['status' => 'success', 'msg' => 'تم الاعتماد بنجاح']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'حدث خطأ أثناء الاعتماد']);
        }
    } 
    elseif ($status == '3') {
        // Rejection Logic
        $this->db->trans_start();
        $this->db->where('id', $id)->update('orders_emp', [
            'status' => '3', 
            'reason_for_rejection' => $reason,
            'responsible_employee' => null
        ]);
        
        $this->db->where('order_id', $id)->where('approver_id', $user_id);
        $this->db->update('approval_workflow', ['status' => 'rejected', 'action_date' => date('Y-m-d H:i:s')]);
        $this->db->trans_complete();
        
        if ($this->db->trans_status()) {
            // SEND REJECTION EMAIL TO APPLICANT
            $order_info = $this->db->get_where('orders_emp', ['id' => $id])->row_array();
            if ($order_info) {
                $applicant = $this->db->select('email, subscriber_name')->where('employee_id', $order_info['emp_id'])->get('emp1')->row();
                if ($applicant && !empty($applicant->email)) {
                    $details = [
                        'رقم الطلب' => '#' . $id,
                        'نوع الطلب' => $order_info['order_name'] ?? 'طلب',
                        'تاريخ التقديم' => $order_info['date'] ?? date('Y-m-d'),
                        '<span style="color:#e74c3c">سبب الرفض</span>' => '<span style="color:#e74c3c">'.htmlspecialchars($reason).'</span>'
                    ];
                    $html = $this->hr_model->build_beautiful_email_template($applicant->subscriber_name ?? 'الموظف', "نعتذر، لقد تم رفض طلبك", "نأسف لإبلاغك بأنه قد تم رفض طلبك.", $details, $id, "مراجعة التفاصيل", "#e74c3c");
                    $this->hr_model->send_html_email($applicant->email, "تم رفض الطلب ❌ - Marsoom HR", $html, $id);
                }
            }
            echo json_encode(['status' => 'success', 'msg' => 'تم رفض الطلب']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'فشل الرفض']);
        }
    }
}
public function approve_request($request_id) {
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $approver_id = $this->session->userdata('username');
    $type = $this->input->post('type'); 

    $this->load->model('hr_model2');
    $this->load->model('hr_model'); // 🌟 LOAD DESKTOP MODEL TO REUSE EMAILS

    // --- 1. HANDLE MANDATE ---
    if ($type === 'Mandate') {
        $res = $this->hr_model2->approve_mandate_mobile($request_id, $approver_id);
        if ($res) { $this->hr_model->trigger_mandate_email($request_id); } // Trigger Email
        
        $response = [
            'status' => $res ? 'success' : 'error',
            'message' => $res ? 'تم اعتماد الانتداب بنجاح' : 'فشل عملية الاعتماد'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // --- 2. HANDLE CLEARANCE ---
   if ($type == 'clearance') {
        $result = $this->hr_model2->approve_clearance_mobile($request_id, $approver_id);
        if ($result) { $this->hr_model->trigger_clearance_email($request_id); } // Trigger Email
        
        $response = [
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'تم اعتماد إخلاء الطرف' : 'فشل الاعتماد'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // --- 3. HANDLE EOS ---
    if ($type == 8) {
        $result = $this->hr_model2->approve_settlement_mobile($request_id, $approver_id);
        if ($result) { $this->hr_model->trigger_eos_email($request_id); } // Trigger Email
        
        $response = [
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'تم اعتماد التسوية' : 'فشل الاعتماد'
        ];
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // --- 4. HANDLE STANDARD REQUESTS (Leaves, Overtime, etc) ---
    $result = $this->hr_model2->process_approval_mobile($request_id, $approver_id);
    if ($result) { $this->hr_model->trigger_next_approval_email($request_id, $approver_id); } // Trigger Email
    
    $response = [
        'status' => $result ? 'success' : 'error',
        'message' => $result ? 'تم اعتماد الطلب' : 'فشل الاعتماد'
    ];
    
    return $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
// 3. Update reject_request to handle type 9 (Clearance)
public function reject_request($request_id) {
    // 1. Security Check
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $approver_id = $this->session->userdata('username');
    $reason = $this->input->post('reason');
    $type = $this->input->post('type');

    $this->load->model('hr_model2');
    $this->load->model('hr_model'); // 🌟 LOAD DESKTOP MODEL TO REUSE EMAILS

    // --- 1. HANDLE MANDATE ---
    if ($type === 'Mandate') {
        $res = $this->hr_model2->reject_mandate_mobile($request_id, $approver_id, $reason);
        if ($res) { $this->hr_model->trigger_mandate_email($request_id); }
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'تم رفض الانتداب' : 'فشل عملية الرفض']));
    }

    // --- 2. HANDLE CLEARANCE ---
    if ($type == 'clearance') {
        $result = $this->hr_model2->reject_clearance_mobile($request_id, $approver_id, $reason);
        if ($result) { $this->hr_model->trigger_clearance_email($request_id); }
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'تم رفض إخلاء الطرف' : 'فشل الرفض']));
    }

    // --- 3. HANDLE EOS ---
    if ($type == 8) {
        $result = $this->hr_model2->reject_settlement_mobile($request_id, $approver_id, $reason);
        if ($result) { $this->hr_model->trigger_eos_email($request_id); }
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'تم رفض التسوية' : 'فشل الرفض']));
    }

    // --- 4. HANDLE STANDARD LOGIC (Leaves, etc) ---
    $this->db->trans_start();
    
    $this->db->where('id', $request_id)->update('orders_emp', [
        'status' => '3', 
        'reason_for_rejection' => $reason,
        'responsible_employee' => null
    ]);

    $this->db->where('order_id', $request_id)->where('approver_id', $approver_id);
    $this->db->group_start()->where('status', 'Pending')->or_where('status', 'pending')->group_end();
    $this->db->update('approval_workflow', ['status' => 'Rejected', 'notes' => $reason, 'action_date' => date('Y-m-d H:i:s')]);
    
    $this->db->trans_complete();
    $update_success = $this->db->trans_status();

    if ($update_success) {
        // SEND REJECTION EMAIL TO APPLICANT
        $order_info = $this->db->get_where('orders_emp', ['id' => $request_id])->row_array();
        if ($order_info) {
            $applicant = $this->db->select('email, subscriber_name')->where('employee_id', $order_info['emp_id'])->get('emp1')->row();
            if ($applicant && !empty($applicant->email)) {
                $details = [
                    'رقم الطلب' => '#' . $request_id,
                    'نوع الطلب' => $order_info['order_name'] ?? 'طلب',
                    'تاريخ التقديم' => $order_info['date'] ?? date('Y-m-d'),
                    '<span style="color:#e74c3c">سبب الرفض</span>' => '<span style="color:#e74c3c">'.htmlspecialchars($reason).'</span>'
                ];
                $html = $this->hr_model->build_beautiful_email_template($applicant->subscriber_name ?? 'الموظف', "نعتذر، لقد تم رفض طلبك", "نأسف لإبلاغك بأنه قد تم رفض طلبك.", $details, $request_id, "مراجعة التفاصيل", "#e74c3c");
                $this->hr_model->send_html_email($applicant->email, "تم رفض الطلب ❌ - Marsoom HR", $html, $request_id);
            }
        }
    }

    return $this->output->set_content_type('application/json')->set_output(json_encode([
        'status' => $update_success ? 'success' : 'error',
        'message' => $update_success ? 'تم رفض الطلب' : 'فشل الرفض'
    ]));
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
      $this->hr_model2->add_watch($name,$op_name);    

      redirect('users/login');
    }

    public function dashboard() {
        $data = [];
        $data['active_page'] = 'dashboard';
        $data['page_title']  = 'لوحة التحكم';
        $this->load->view('templateo/header5', $data);
        $this->load->view('templateo/dashboard_view', $data);
        $this->load->view('templateo/footer5', $data);
    }

        public function task() {
        
        
        $this->load->view('templateo/task');
      
    }

    public function login1() {
        
        
        $this->load->view('templateo/login1');
      
    }

    public function main_hr1() {
        
        
        $this->load->view('templateo/main_hr1');
      
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
        
        
        $this->load->view('templateo/main_salary');
      
    }


    public function get_job_details()
    {
        // Ensure the user is logged in before proceeding
        if (!$this->session->userdata('username')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'User not authenticated.']));
            return;
        }

        $employee_id = $this->session->userdata('username');

        $this->load->model('hr_model2');
        $job_data = $this->hr_model2->get_employee_job_details($employee_id);

        if ($job_data) {
            // Send the data back as a successful JSON response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $job_data]));
        } else {
            // Respond with an error if no data was found
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Job details not found.']));
        }
    }
    public function process_login() {
    if ($this->input->get('pwa') || $this->input->post('is_pwa')) {
        $this->session->set_userdata('prefer_mobile', true);
    }
    
    // After successful login
    if ($this->session->userdata('prefer_mobile')) {
        redirect('users2/mobile_dashboard');
    } else {
        redirect('users1/main_Emp');
    }
}

public function pwa_launch() {
    // Set a flag in the session to remember this is a PWA user.
    $this->session->set_userdata('is_pwa', true);

    // Now, send the user to the regular login page.
    redirect('users/login');
}
    public function get_financial_details()
    {
        // Ensure the user is logged in
        if (!$this->session->userdata('username')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'User not authenticated.']));
            return;
        }

        $employee_id = $this->session->userdata('username');

        $this->load->model('hr_model2');
        $financial_data = $this->hr_model2->get_employee_financial_details($employee_id);

        if ($financial_data) {
            // Send the data back as a successful JSON response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $financial_data]));
        } else {
            // Respond with an error if no data was found
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Financial details not found.']));
        }
    }

    public function get_contract_details()
    {
        if (!$this->session->userdata('username')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'User not authenticated.']));
            return;
        }

        $employee_id = $this->session->userdata('username');

        $this->load->model('hr_model2');
        $contract_data = $this->hr_model2->get_employee_contract_details($employee_id);

        if ($contract_data) {
            // --- SERVER-SIDE CALCULATION FOR REMAINING PERIOD ---
            $remaining_period_message = 'N/A';
            if (!empty($contract_data->contract_end)) {
                $end_date = new DateTime($contract_data->contract_end);
                $now = new DateTime();

                if ($now > $end_date) {
                    $remaining_period_message = "منتهي"; // Expired
                } else {
                    $interval = $now->diff($end_date);
                    $months = ($interval->y * 12) + $interval->m;
                    $days = $interval->d;
                    $remaining_period_message = "$months شهر و $days يوم";
                }
            }
            // Add the calculated value to the data object
            $contract_data->remaining_renewal_period = $remaining_period_message;
            // --- END CALCULATION ---

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $contract_data]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Contract details not found.']));
        }
    }
    // In Users2.php, add these FIVE new functions

public function mobile_get_personal_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model2->mobile_get_employee_profile($employee_id);
    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
}

public function mobile_get_job_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model2->mobile_get_employee_profile($employee_id);
    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
}

public function mobile_get_financial_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model2->mobile_get_employee_profile($employee_id);
    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
}

public function mobile_get_contract_details() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model2->mobile_get_employee_contract($employee_id);
    if ($data && !empty($data['contract_end'])) {
        $end_date = new DateTime($data['contract_end']);
        $now = new DateTime();
        if ($now > $end_date) {
            $data['remaining_renewal_period'] = "منتهي";
        } else {
            $interval = $now->diff($end_date);
            $data['remaining_renewal_period'] = $interval->y . " سنة, " . $interval->m . " شهر و " . $interval->d . " يوم";
        }
    }
    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
}

public function mobile_get_leave_balances() {
    if (!$this->session->userdata('logged_in')) { return $this->output->set_status_header(401); }
    $employee_id = $this->session->userdata('username');
    $data = $this->hr_model2->mobile_get_employee_balances($employee_id);
    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
}
public function get_personal_details()
    {
        // Ensure the user is logged in before proceeding (checks for 'username' now)
        if (!$this->session->userdata('username')) {
            // Respond with an error if no user is logged in
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'User not authenticated.']));
            return;
        }

        // Get the employee ID from the 'username' session key
        $employee_id = $this->session->userdata('username');

        $this->load->model('hr_model2');
        $employee_data = $this->hr_model2->get_employee_personal_details($employee_id);

        if ($employee_data) {
            // Send the data back as a successful JSON response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $employee_data]));
        } else {
            // Respond with an error if no data was found for the user
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Employee details not found.']));
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

        $this->load->view('templateo/header5', $data);
        $this->load->view('templateo/emp_view', $data);
        $this->load->view('templateo/footer5', $data);
    }





       function index1_1(){ 
        

             $this->load->view('templateo/index1_1');   


          
          
      }

      function main1(){ 
        
$data['employee_name'] = $this->session->userdata('name');

            $this->load->view('template/new_header_and_sidebar', $data ?? []);
            $this->load->view('templateo/main1', $data);
            $this->load->view('template/new_footer', $data ?? []);
          
      }
// In Users2.php

public function manual_attendance() {
    // FIX 1: Set the correct timezone to Asia/Riyadh
    date_default_timezone_set('Asia/Riyadh');
    $this->db->query("SET time_zone = '+03:00'");

    if (!$this->session->userdata('user_id')) {
        echo json_encode(['status' => 'error', 'message' => 'غير مسجل دخول']);
        return;
    }

    $emp_code = $this->session->userdata('username');
    $current_date = date('Y-m-d');
    
    $last_punch = $this->db->query("
        SELECT punch_state 
        FROM attendance_logs 
        WHERE emp_code = ? AND DATE(punch_time) = ? 
        ORDER BY punch_time DESC 
        LIMIT 1
    ", [$emp_code, $current_date])->row();
    
    // FIX 2: Use text 'Check In' / 'Check Out' instead of numbers
    $punch_state = 'Check In'; // Default to check-in
    
    if ($last_punch) {
        // This logic handles both old ('0') and new ('Check In') formats
        if ($last_punch->punch_state == 'Check In' || $last_punch->punch_state == '0') {
            $punch_state = 'Check Out';
        } else {
            $punch_state = 'Check In';
        }
    }
    
    $attendance_data = [
        'emp_code' => $emp_code,
        'first_name' => $this->session->userdata('name'),
        'last_name' => '',
        'punch_time' => date('Y-m-d H:i:s'), // This now correctly uses Riyadh time
        'punch_state' => $punch_state, // This now saves the correct text
        'area_alias' => 'MANUAL_ENTRY',
        'terminal_sn' => 'MOBILE_APP_MANUAL',
        'terminal_alias' => 'Mobile App - Manual Entry',
        'upload_time' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $this->db->insert('attendance_logs', $attendance_data);
    $insert_id = $this->db->insert_id();
    
    if ($insert_id) {
        $action = ($punch_state == 'Check In') ? 'الحضور' : 'الانصراف';
        echo json_encode([
            'status' => 'success', 
            'message' => "تم تسجيل $action يدوياً بنجاح",
            'action' => $action
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في حفظ البيانات']);
    }
}
public function toggle_attendance() {
    // Force PHP timezone to match MySQL (Asia/Riyadh = UTC+3)
    date_default_timezone_set('Asia/Riyadh');
    
    // Set MySQL session timezone to ensure consistency
    $this->db->query("SET time_zone = '+03:00'");
    
    // Check if user is logged in
    if (!$this->session->userdata('user_id')) {
        echo json_encode(['status' => 'error', 'message' => 'غير مسجل دخول']);
        return;
    }

    $user_id = $this->session->userdata('user_id');
    $emp_code = $this->session->userdata('username'); // هذا هو رقم الموظف
    $latitude = $this->input->post('latitude');
    $longitude = $this->input->post('longitude');
    
    // Validate location data
    if (!$latitude || !$longitude) {
        echo json_encode(['status' => 'error', 'message' => 'بيانات الموقع غير صالحة']);
        return;
    }
    
    // Check if user is within allowed branch radius (0.5 km = 500 meters)
    $allowed_branch = $this->db->query("
        SELECT *, 
        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
        FROM branches 
        HAVING distance < 0.5 
        ORDER BY distance 
        LIMIT 1
    ", [$latitude, $longitude, $latitude])->row();
    
    if (!$allowed_branch) {
        echo json_encode(['status' => 'error', 'message' => 'أنت خارج نطاق الموقع المسموح به']);
        return;
    }

    // ---------------------------------------------------------
    // بداية التعديل: تقييد فرع "رياض - مكتب 731" للموظف 2230 فقط
    // ---------------------------------------------------------
    if (trim($allowed_branch->branch_name) == 'رياض - مكتب 731') {
        if ($emp_code != '2230' && $emp_code != 'c9990') {
    
            echo json_encode([
                'status' => 'error', 
                'message' => 'عذراً، تسجيل الحضور في هذا الموقع مخصص لموظفين محددين فقط.'
            ]);
            return;
        }
    }
    // ---------------------------------------------------------
    // نهاية التعديل
    // ---------------------------------------------------------
    
    // Get current time - NOW both PHP and MySQL should be in Riyadh time
    $current_datetime = date('Y-m-d H:i:s');
    $current_date = date('Y-m-d');
    
    // Check last punch state for this user today
    $last_punch = $this->db->query("
        SELECT punch_state, punch_time
        FROM attendance_logs 
        WHERE emp_code = ? AND DATE(punch_time) = ? 
        ORDER BY punch_time DESC 
        LIMIT 1
    ", [$emp_code, $current_date])->row();
    
    // Determine next action based on last punch state
    $punch_state = 'Check In'; // Default to check-in
    
    if ($last_punch) {
        if ($last_punch->punch_state == 'Check In' || $last_punch->punch_state == '0') {
            $punch_state = 'Check Out';
        } else {
            $punch_state = 'Check In';
        }
    }
    
    // Insert attendance record
    $attendance_data = [
        'emp_code' => $emp_code,
        'first_name' => $this->session->userdata('name'),
        'last_name' => '',
        'punch_time' => $current_datetime,
        'punch_state' => $punch_state,
        'area_alias' => $allowed_branch->branch_name,
        'terminal_sn' => 'MOBILE_APP',
        'terminal_alias' => 'Mobile App - ' . $this->session->userdata('name'),
        'upload_time' => $current_datetime,
        'created_at' => $current_datetime
    ];
    
    $this->db->insert('attendance_logs', $attendance_data);
    $insert_id = $this->db->insert_id();
    
    if ($insert_id) {
        $action = ($punch_state == 'Check In') ? 'الحضور' : 'الانصراف';
        $display_time = date('H:i:s'); // Current Riyadh time for display
        
        echo json_encode([
            'status' => 'success', 
            'message' => "تم تسجيل $action بنجاح في " . $allowed_branch->branch_name . " - الساعة: $display_time",
            'action' => $action,
            'timestamp' => $current_datetime,
            'server_time' => $current_datetime,
            'display_time' => $display_time
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في حفظ البيانات']);
    }
}
// In Users2.php

public function get_today_attendance_summary() {
    // Set both PHP and MySQL to Riyadh timezone
    date_default_timezone_set('Asia/Riyadh');
    $this->db->query("SET time_zone = '+03:00'");
    
    if (!$this->session->userdata('user_id')) {
        echo json_encode(['status' => 'error', 'message' => 'غير مسجل دخول']);
        return;
    }

    $emp_code = $this->session->userdata('username');
    $today = date('Y-m-d');
    
    $records = $this->hr_model2->get_todays_punches($emp_code, $today);
    
    $isCurrentlyCheckedIn = false;
    $lastCheckInTime = null;
    $totalSeconds = 0;
    
    $checkIns = [];
    $checkOuts = [];
    
    foreach ($records as $record) {
        // Standardize punch state values
        $state = (in_array($record->punch_state, ['Check In', '0'])) ? 'in' : 'out';
        
        if ($state === 'in') {
            $checkIns[] = $record->punch_time;
        } else {
            $checkOuts[] = $record->punch_time;
        }
    }
    
    if (!empty($records)) {
        $last_record = end($records);
        $last_state = (in_array($last_record->punch_state, ['Check In', '0'])) ? 'in' : 'out';
        if ($last_state === 'in') {
            $isCurrentlyCheckedIn = true;
            $lastCheckInTime = $last_record->punch_time;
        }
    }

    // Pair check-ins and check-outs to calculate worked duration
    $paired_count = min(count($checkIns), count($checkOuts));
    for ($i = 0; $i < $paired_count; $i++) {
        $start = new DateTime($checkIns[$i]);
        $end = new DateTime($checkOuts[$i]);
        if ($end > $start) {
            $totalSeconds += $end->getTimestamp() - $start->getTimestamp();
        }
    }
    
    $totalWorkDuration = gmdate("H:i:s", $totalSeconds);
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'hasCheckInToday' => !empty($records),
            'isCurrentlyCheckedIn' => $isCurrentlyCheckedIn,
            'lastCheckInTime' => $lastCheckInTime,
            'totalWorkDuration' => $totalWorkDuration,
            'recordsCount' => count($records),
            'serverTime' => date('Y-m-d H:i:s')
        ]
    ]);
}
public function check_timezone() {
    echo "PHP Timezone: " . date_default_timezone_get() . "<br>";
    echo "PHP Time: " . date('Y-m-d H:i:s') . "<br>";
    
    // Check MySQL timezone
    $query = $this->db->query("SELECT @@global.time_zone, @@session.time_zone");
    $result = $query->row();
    echo "MySQL Global Timezone: " . $result->{'@@global.time_zone'} . "<br>";
    echo "MySQL Session Timezone: " . $result->{'@@session.time_zone'} . "<br>";
    
    // Check MySQL time
    $query = $this->db->query("SELECT NOW() as mysql_time");
    $result = $query->row();
    echo "MySQL Time: " . $result->mysql_time . "<br>";
}
// Add this to your Users2.php controller for testing
public function test_attendance_endpoint() {
    echo json_encode([
        'status' => 'success', 
        'message' => 'Endpoint is accessible',
        'data' => [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username')
        ]
    ]);
}
    public function get_last_requests() {
        if (!$this->session->userdata('logged_in')) {
            $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error', 'message' => 'Not authenticated.']));
            return;
        }

        $emp_id = $this->session->userdata('username');
        
        $this->db->where('emp_id', $emp_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(3);
        $requests = $this->db->get('orders_emp')->result_array(); // Assuming 'orders_emp' is your requests table

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'data' => $requests]));
    }


// In Users2.php, REPLACE the mobile_dashboard() function with this
public function mobile_dashboard() {
    if (!$this->session->userdata('logged_in')) {
        redirect('users/login');
        return;
    }
    
    $this->load->model('hr_model2');
    $logged_in_user_id = $this->session->userdata('username');

    // --- 1. Prepare data for the Home Tab forms ---
    $data['employee_name'] = $this->session->userdata('name');
    $data['is_hr_user'] = false;
    $gender = $this->hr_model2->get_employee_gender($logged_in_user_id);
    $data['employees'] = $this->hr_model2->get_all_employees();
    $data['leave_types'] = $this->hr_model2->get_leave_types($gender);
    $data['balances'] = $this->hr_model2->get_employee_balances($logged_in_user_id);
    $data['public_holidays'] = $this->hr_model2->get_all_holidays_as_dates();
    $data['saturday_assignments'] = $this->hr_model2->get_saturday_assignments_for_employee($logged_in_user_id);

    
    // --- 2. ✅ NEW: Prepare data for the Attendance Tab Calendar ---
    $data['year']  = (int)date('Y');
    $data['month'] = (int)date('n');
    $data['name'] = $this->session->userdata('name');
    $data['target_name'] = $this->session->userdata('name');
    $data['target_username'] = $logged_in_user_id;

    // Fetch calendar data from the model (you will need to add these functions)
    $data['daysMap'] = $this->hr_model2->get_attendance_map_for_month($logged_in_user_id, $data['year'], $data['month']);
    $data['eventsByDay'] = $this->hr_model2->get_events_for_month($logged_in_user_id, $data['year'], $data['month']);
    $data['violationsByDay'] = []; // Keep this simple for now
    $data['holidaysMap'] = $this->hr_model2->get_holidays_for_month($data['year'], $data['month']);

    // Navigation months
    $prev = new DateTime("{$data['year']}-{$data['month']}-01");
    $prev->modify('-1 month');
    $data['prevY'] = $prev->format('Y');
    $data['prevM'] = $prev->format('n');
    
    $next = new DateTime("{$data['year']}-{$data['month']}-01");
    $next->modify('+1 month');
    $data['nextY'] = $next->format('Y');
    $data['nextM'] = $next->format('n');
    
    $this->load->view('templateo/mobile_view', $data);
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

         
          
               // $data['get_emkan'] = $this->hr_model2->get_emkan($id);
                 
                      $data['title'] = 'اضافة مستخدم جديد';
                      $this->form_validation->set_rules('name', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){
                      $this->load->view('templateo/header4');
                      $this->load->view('templateo/add_project', $data);
                      $this->load->view('templateo/footer4');
                } else {
 
                            
                                
                               
                                          $this->hr_model2->add_project();   
                                    
                               
                             
                            
                          

                           

                           


           
            redirect('users/project_index');
          
      }
    }

         function project_index(){

         
          
               // $data['get_emkan'] = $this->hr_model2->get_emkan($id);
                        $id5= $this->hr_model2->max_tran_id();
                 
                      $data['title'] = 'اضافة مستخدم جديد';
                      $this->form_validation->set_rules('name', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){
                      $this->load->view('templateo/header4');
                      $this->load->view('templateo/project_index', $data);
                      $this->load->view('templateo/footer4');
                } else {
 
                            
                                
                               
                                          $this->hr_model2->add_project();   
                                    
          
           
            redirect('users/project_index');
          
      }
    }

      function dashbord_analyses11115588125998369(){ 

   



           

                 $this->hr_model2->export_csv();

          


      
    }



      function main(){

         
          
               // $data['get_emkan'] = $this->hr_model2->get_emkan($id);
                        $id5= $this->hr_model2->max_tran_id();
                 
                      $data['title'] = 'اضافة مستخدم جديد';
                      $this->form_validation->set_rules('name', 'name', 'required'); 
                      if($this->form_validation->run() === FALSE){
                      $this->load->view('templateo/header2');
                      $this->load->view('templateo/main', $data);
                      $this->load->view('templateo/footer2');
                } else {
 
                            
                                
                               
                                          $this->hr_model2->add_project();   
                                    
          
           
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
     // In Users2.php, add these two new functions

/**
 * Fetches all requests submitted BY the logged-in user.
 */
// REPLACE the existing get_my_requests function with this one

public function get_my_requests() {
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error']));
    }
    
    $emp_id = $this->session->userdata('username');
    
    // ✅ Collect filters from the GET request
    $filters = [
        'type'       => $this->input->get('type', TRUE),
        'status'     => $this->input->get('status', TRUE),
        'start_date' => $this->input->get('start_date', TRUE),
        'end_date'   => $this->input->get('end_date', TRUE)
    ];

    // ✅ Debug: Log the filters to see what's being received
    log_message('debug', 'Filters received: ' . print_r($filters, true));

    $this->load->model('hr_model2');
    
    // ✅ Debug: Check what SQL is being generated
    $this->db->save_queries = true;
    $requests = $this->hr_model2->get_orders_for_employee($emp_id, $filters);
    $last_query = $this->db->last_query();
    log_message('debug', 'Last query: ' . $last_query);
    
    $this->output->set_content_type('application/json')->set_output(json_encode([
        'status' => 'success', 
        'data' => $requests,
        'debug' => [ // Optional: include debug info in response for testing
            'filters' => $filters,
            'query' => $last_query
        ]
    ]));
}
// In Users2.php

public function get_branches_ajax()
{
    // ✅ ضع رقمك الوظيفي (username) في هذه القائمة
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901']; // <--- أضف رقمك هنا
    
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }
    
    $this->load->model('hr_model2');
    $branches = $this->hr_model2->get_all_branches();
    
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'branches' => $branches]));
}

/**
 * ✅ استبدل دالة delete_branch_ajax بهذه النسخة
 * تحذف فرع (AJAX)
 */
public function delete_branch_ajax()
{
    // ✅ ضع رقمك الوظيفي (username) في هذه القائمة
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901', 'YOUR_USERNAME_HERE']; // <--- أضف رقمك هنا

    if (!in_array($this->session->userdata('username'), $hr_users)) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $id = $this->input->post('id');
    if (empty($id)) {
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
    }

    $this->load->model('hr_model2');
    if ($this->hr_model2->delete_branch($id)) {
        $response = ['status' => 'success', 'message' => 'تم حذف الفرع بنجاح.'];
    } else {
        $response = ['status' => 'error', 'message' => 'فشل حذف الفرع.'];
    }
    
    // إرجاع CSRF token
    $response['csrf_name'] = $this->security->get_csrf_token_name();
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
// In Users2.php
public function get_request_details() { 
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error']));
    }
    
    // FETCH INPUTS FROM GET REQUEST
    $order_id = $this->input->get('id');
    $type = $this->input->get('type'); 
    
    $this->load->model('hr_model2');

    // --- 1. HANDLE MANDATE ---
    if ($type === 'Mandate') {
        $data = $this->hr_model2->get_mandate_details_mobile($order_id);
        // ... (Keep existing mandate logic) ...
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
    }

    $request_data = null;

    // --- 2. HANDLE CLEARANCE (CHANGE THIS) ---
    // CHANGED: Check for string 'clearance' instead of 9
    if ($type == 'clearance') {
        $request_data = $this->hr_model2->get_clearance_details_mobile((int)$order_id);
        if ($request_data) {
            $request_data['order_name'] = 'إخلاء طرف';
            $request_data['type'] = 'clearance';
            $request_data['note'] = isset($request_data['task_name']) ? $request_data['task_name'] : ''; 
        }
    } 
    // --- 3. HANDLE EOS ---
    elseif ($type == 8) {
        $request_data = $this->hr_model2->get_eos_request_details((int)$order_id);
        if ($request_data) {
            $request_data['order_name'] = 'مستحقات نهاية الخدمة';
            $request_data['type'] = 8;
        }
    } 
    // --- 4. HANDLE STANDARD (Includes Work Mission Type 9) ---
    else {
        // This will now fetch data from 'orders_emp', which contains your mission columns
        $request_data = $this->hr_model2->get_single_request_details((int)$order_id);
    }

    // FINAL OUTPUT
    if ($request_data) {
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $request_data]));
    } else {
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Request not found.']));
    }
}
public function get_my_approvals() {
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error']));
    }
    
    $approver_id = $this->session->userdata('username');
    $this->load->model('hr_model2');
    
    // 1. Get Standard Requests (Leaves, etc)
    $requests = $this->hr_model2->get_pending_approvals_for_user($approver_id);
    if(!$requests) $requests = [];

    // 2. Get Clearance Requests
    $clearance = $this->hr_model2->get_pending_clearances_for_mobile($approver_id);
    if(!$clearance) $clearance = [];

    // 3. Get Mandate Requests (THIS WAS LIKELY MISSING)
    $mandates = $this->hr_model2->get_pending_mandates_for_mobile($approver_id);
    if(!$mandates) $mandates = [];
    
    // 4. Merge All Together
    $all = array_merge($requests, $clearance, $mandates);

    // 5. Sort by Date (Newest first)
    usort($all, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $all]));
}
public function get_calendar_data()
{
    if (!$this->session->userdata('logged_in')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error']));
    }
    $emp_id = $this->session->userdata('username');
    $year = $this->input->get('y');
    $month = $this->input->get('m');

    $this->load->model('hr_model2');
    $data = $this->hr_model2->get_monthly_attendance_summary($emp_id, $year, $month);

    $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $data]));
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
       function extn(){ 
        if(!$this ->session->userdata('logged_in')){
            redirect('users/login');
          }else{
           $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];

            
              $data['get_extension'] =  $this->hr_model2->get_extension($id); 

             





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
              $data['get_users_otp'] =  $this->hr_model2->get_users_otp($username); 
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


    $data['get_salary_vacations'] = $this->hr_model2->get_salary_vacations();
       
 
        $this->load->view('templateo/vacations', $data);
    }

    public function discounts() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_salary_discounts();
       
 
        $this->load->view('templateo/discounts', $data);
    }

    public function discounts101() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_salary_discounts();
       
 
        $this->load->view('templateo/discounts101', $data);
    }

     public function reparations() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_salary_reparations();
       
 
        $this->load->view('templateo/reparations', $data);
    }

    public function reparations101() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_salary_reparations();
       
 
        $this->load->view('templateo/reparations101', $data);
    }

    public function salary_sheet() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_salary_salary_sheet();
       
 
        $this->load->view('templateo/salary_sheet', $data);
    }

    




    public function emp_data() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_emp1();
       
 
        $this->load->view('templateo/emp_data', $data);
    }

     public function work_restrictions() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_work_restrictions();
       
 
        $this->load->view('templateo/work_restrictions', $data);
    }

      public function emp_data101() {

     $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];


    $data['get_salary_vacations'] = $this->hr_model2->get_emp1();
       
 
        $this->load->view('templateo/emp_data101', $data);
    }




    public function get_employee_data() {
        // التأكد من أن الرقم الوظيفي تم تمريره عبر AJAX
        if($this->input->post('employee_id')) {
            $employee_id = $this->input->post('employee_id'); // الحصول على الرقم الوظيفي من POST

            // استدعاء دالة الموديل للبحث عن الموظف
            $employee_data = $this->hr_model2->get_employee_by_id($employee_id);

            // التحقق من وجود الموظف
            if($employee_data) {
                // إرسال النتيجة عبر JSON
                echo json_encode(['status' => 'success', 'employee_name' => $employee_data['subscriber_name']]);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }

    // Example using fetch API
public function submit_correction_request() {
    try {
        // Set headers for CORS and JSON response
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        
        // Only allow POST requests
        if ($this->input->method() !== 'post') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'طريقة الطلب غير مسموحة'
                ]));
            return;
        }
        
        // Get JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Debug: Log the received data
        log_message('debug', 'Correction request received: ' . print_r($data, true));
        
        // Validate JSON input
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'بيانات JSON غير صحيحة'
                ]));
            return;
        }
        
        // Validate input
        if (empty($data)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'لم يتم استلام أي بيانات'
                ]));
            return;
        }
        
        // Check if user is logged in
        $username = $this->session->userdata('username');
        $name = $this->session->userdata('name');
        
        if (!$username) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً'
                ]));
            return;
        }
        
        // Set validation rules
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('correctionDate', 'التاريخ', 'required');
        $this->form_validation->set_rules('checkInTime', 'وقت الدخول', 'required');
        $this->form_validation->set_rules('checkOutTime', 'وقت الخروج', 'required');
        $this->form_validation->set_rules('correctionReason', 'السبب', 'required');
        
        // Additional validation for custom reason if "اخرى" is selected
        if (isset($data['correctionReason']) && $data['correctionReason'] === 'اخرى') {
            $this->form_validation->set_rules('customReason', 'السبب المخصص', 'required|max_length[500]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status' => 'error',
                'message' => strip_tags(validation_errors())
            ];
        } else {
            // Additional business logic validations
            $validation_result = $this->_validate_correction_business_rules($data);
            if (!$validation_result['valid']) {
                $response = [
                    'status' => 'error',
                    'message' => $validation_result['message']
                ];
            } 
            // Check if request already exists
            else if ($this->hr_model2->request_exists($username, $data['correctionDate'])) {
                $response = [
                    'status' => 'error',
                    'message' => 'لديك بالفعل طلب تصحيح لهذا التاريخ'
                ];
            } else {
                // Process the reason (handle custom reason)
                $reason = $this->_process_correction_reason($data);
                
                // Prepare data for insertion
                $insertData = [
                    'username' => $username,
                    'name' => $name,
                    'date' => $data['correctionDate'],
                    'in_time' => $data['checkInTime'],
                    'out_time' => $data['checkOutTime'],
                    'reason' => $reason,
                    'file' => null,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Debug: Log the data being inserted
                log_message('debug', 'Inserting correction request: ' . print_r($insertData, true));
                
                // Insert data with transaction
                $this->db->trans_start();
                $result = $this->hr_model2->insert_fingerprint_request($insertData);
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE || !$result) {
                    // Get database error for debugging
                    $db_error = $this->db->error();
                    log_message('error', 'Database error in correction request: ' . print_r($db_error, true));
                    
                    $response = [
                        'status' => 'error',
                        'message' => 'فشل في إرسال طلب التصحيح. يرجى المحاولة مرة أخرى أو الاتصال بالدعم الفني'
                    ];
                } else {
                    $request_id = $this->db->insert_id();
                    
                    // Log successful insertion
                    log_message('info', "Correction request submitted successfully. ID: {$request_id}, User: {$username}, Date: {$data['correctionDate']}");
                    
                    $response = [
                        'status' => 'success',
                        'message' => 'تم إرسال طلب التصحيح بنجاح. سيتم مراجعته من قبل إدارة الموارد البشرية',
                        'request_id' => $request_id
                    ];
                }
            }
        }
        
    } catch (Exception $e) {
        // Log unexpected errors
        log_message('error', 'Unexpected error in submit_correction_request: ' . $e->getMessage());
        
        $response = [
            'status' => 'error',
            'message' => 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى'
        ];
    }
    
    // Return JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE));
}

// Helper method for business rules validation
private function _validate_correction_business_rules($data) {
    // Check if date is not in the future
    $correction_date = new DateTime($data['correctionDate']);
    $today = new DateTime('today');
    
    if ($correction_date > $today) {
        return [
            'valid' => false,
            'message' => 'لا يمكن تقديم طلب تصحيح لتاريخ في المستقبل'
        ];
    }
    
    // Check if date is not too old (more than 30 days)
    $thirty_days_ago = new DateTime('-30 days');
    if ($correction_date < $thirty_days_ago) {
        return [
            'valid' => false,
            'message' => 'لا يمكن تقديم طلب تصحيح للتواريخ الأقدم من 30 يوماً'
        ];
    }
    
    // Validate time logic
    $in_time = strtotime($data['checkInTime']);
    $out_time = strtotime($data['checkOutTime']);
    
    if ($in_time >= $out_time) {
        return [
            'valid' => false,
            'message' => 'وقت الدخول يجب أن يكون قبل وقت الخروج'
        ];
    }
    
    // Check reasonable working hours (not more than 16 hours)
    $work_duration = ($out_time - $in_time) / 3600; // Convert to hours
    
    if ($work_duration > 16) {
        return [
            'valid' => false,
            'message' => 'مدة العمل لا يمكن أن تتجاوز 16 ساعة'
        ];
    }
    
    if ($work_duration < 1) {
        return [
            'valid' => false,
            'message' => 'مدة العمل يجب أن تكون ساعة واحدة على الأقل'
        ];
    }
    
    // Validate time format
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['checkInTime']) ||
        !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['checkOutTime'])) {
        return [
            'valid' => false,
            'message' => 'تنسيق الوقت غير صحيح'
        ];
    }
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['correctionDate'])) {
        return [
            'valid' => false,
            'message' => 'تنسيق التاريخ غير صحيح'
        ];
    }
    
    return ['valid' => true];
}

// Helper method to process correction reason
private function _process_correction_reason($data) {
    $reason = $data['correctionReason'];
    
    // If "اخرى" is selected and custom reason is provided
    if ($reason === 'اخرى' && !empty($data['customReason'])) {
        $custom_reason = trim($data['customReason']);
        $reason = 'اخرى: ' . $custom_reason;
    }
    
    return $reason;
}
// Add this method to your Users1 controller
public function get_user_correction_requests() {
    // Add debug logging
    log_message('debug', 'get_user_correction_requests method called');
    
    // Check if user is logged in
    $username = $this->session->userdata('username');
    
    log_message('debug', 'Username from session: ' . ($username ?: 'null'));
    
    if (!$username) {
        log_message('debug', 'User not logged in, returning error');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'غير مصرح بالوصول'
            ], JSON_UNESCAPED_UNICODE));
        return;
    }
    
    try {
        // Get user's correction requests
        log_message('debug', 'Fetching requests for user: ' . $username);
        $requests = $this->hr_model2->get_user_correction_requests($username);
        
        log_message('debug', 'Found ' . count($requests) . ' requests');
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data' => $requests
            ], JSON_UNESCAPED_UNICODE));
            
    } catch (Exception $e) {
        log_message('error', 'Error in get_user_correction_requests: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ في الخادم'
            ], JSON_UNESCAPED_UNICODE));
    }
}

// Add this method to your Users1 controller
public function cancel_correction_request() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $username = $this->session->userdata('username');
    $request_id = isset($data['request_id']) ? (int)$data['request_id'] : 0;
    
    if (!$username || !$request_id) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'بيانات غير صحيحة'
            ]));
        return;
    }
    
    // Check if request belongs to user and can be cancelled
    $request = $this->hr_model2->get_correction_request($request_id, $username);
    
    if (!$request) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'الطلب غير موجود'
            ]));
        return;
    }
    
    if ($request['status'] !== 'pending') {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'لا يمكن إلغاء هذا الطلب'
            ]));
        return;
    }
    
    $result = $this->hr_model2->cancel_correction_request($request_id);
    
    if ($result) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'تم إلغاء الطلب بنجاح'
            ]));
    } else {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'فشل في إلغاء الطلب'
            ]));
    }
}
public function add_document() {
    // Use the correct session key 'username' for the employee's ID
    $employee_id = $this->session->userdata('username'); 
    
    // IMPORTANT: Also check and update the key for the employee's name.
    // Replace 'employee_name' with your actual session key for the user's name.
    $employee_name = $this->session->userdata('employee_name'); 

    if (!$employee_id) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
        return;
    }
    
    // Set form validation rules
    $this->form_validation->set_rules('doc_type', 'Document Type', 'required');

    if ($this->form_validation->run() == FALSE) {
        echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        return;
    }

    // File Upload Configuration
    $config['upload_path']   = '.
    /uploads/documents/';
    $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx';
    $config['max_size']      = 2048; // 2MB
    $config['encrypt_name']  = TRUE;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('document_file')) {
        echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]);
        return;
    } 
    
    $upload_data = $this->upload->data();

    $document_data = [
        'employee_id'   => $employee_id,
        'employee_name' => $employee_name, // Make sure this key is also correct
        'doc_type'      => $this->input->post('doc_type'),
        'description'   => $this->input->post('description'),
        'file_name'     => $upload_data['orig_name'],
        'file_path'     => './uploads/documents/' . $upload_data['file_name'],
        'upload_date'   => date('Y-m-d H:i:s')
    ];

    if ($this->hr_model2->insert_document($document_data)) {
        echo json_encode(['status' => 'success', 'message' => 'تم رفع المستند بنجاح!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل حفظ البيانات في قاعدة البيانات.']);
    }
}



// In application/controllers/users1.php
public function attendance() {
    // First, check if a user is logged in
    if (!$this->session->userdata('username')) {
        $response = ['status' => 'error', 'message' => 'User not authenticated. Please log in again.'];
        $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode($response));
        return;
    }

    // Get the employee code securely from the server-side session
    $emp_code = $this->session->userdata('username');

    $this->load->model('hr_model2');

    // Build the data array for the database
    $data = array(
        'emp_code'    => $emp_code, // Use the emp_code from the session
        'punch_time'  => $this->input->post('punch_time'),
        'punch_state' => $this->input->post('punch_state'),
        'area_alias'        => $this->input->post('area_alias')
    );

    // Insert the data and check if it was successful
    if ($this->hr_model2->insert_attendance($data)) {
        $response = ['status' => 'success', 'message' => 'Attendance recorded successfully.'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to save attendance to the database.'];
    }

    // Send the JSON response back to the browser
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

public function get_branch_locations()
{
    // Ensure user is authenticated before providing location data
    if (!$this->session->userdata('username')) {
        return $this->output->set_status_header(401)->set_output(json_encode(['status' => 'error', 'message' => 'Not authenticated.']));
    }

    $this->load->model('hr_model2');
    $branches = $this->hr_model2->get_all_branches();

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($branches));
}
// In Users2.php

public function manage_branches()
{
    // Security check to ensure only HR can access
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901'];
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        show_error('You are not authorized to view this page.', 403);
        return;
    }

    $this->load->model('hr_model2');
    $data['branches'] = $this->hr_model2->get_all_branches();
    
    // The view file remains the same, but it will be loaded by this controller.
    $this->load->view('templateo/branch_management_view', $data);
}

public function save_branch()
{
    // ✅ ضع رقمك الوظيفي (username) في هذه القائمة
    $hr_users = ['2774', '2230', '2784', '1835', '2515', '2901']; // <--- أضف رقمك هنا

    // ✅ تم تعديل الفحص الأمني ليعمل بشكل صحيح
    if (!in_array($this->session->userdata('username'), $hr_users)) {
        // إرجاع خطأ بصيغة JSON بدلاً من 403
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
    }

    $this->load->model('hr_model2');
    $this->load->library('form_validation');

    $this->form_validation->set_rules('branch_name', 'Branch Name', 'required');
    $this->form_validation->set_rules('latitude', 'Latitude', 'required|numeric');
    $this->form_validation->set_rules('longitude', 'Longitude', 'required|numeric');

    if ($this->form_validation->run() === FALSE) {
        $response = ['status' => 'error', 'message' => validation_errors()];
    } else {
        $data = [
            'branch_name' => $this->input->post('branch_name'),
            'latitude'    => $this->input->post('latitude'),
            'longitude'   => $this->input->post('longitude')
        ];

        if ($this->hr_model2->add_branch($data)) {
            $response = ['status' => 'success', 'message' => 'تمت إضافة الفرع بنجاح!'];
        } else {
            $response = ['status' => 'error', 'message' => 'فشل حفظ الفرع في قاعدة البيانات.'];
        }
    }
    
    // إرجاع CSRF token
    $response['csrf_name'] = $this->security->get_csrf_token_name();
    $response['csrf_hash'] = $this->security->get_csrf_hash();
    
    $this->output->set_content_type('application/json')->set_output(json_encode($response));
}
public function print_salary_certificate($employee_id)
{
    $this->load->model('hr_model2');
    $data['employee'] = $this->hr_model2->get_employee_for_document($employee_id);

    if ($data['employee']) {
        // Load the new view file we will create in the next step
        $this->load->view('templateo/salary_certificate_template', $data);
    } else {
        show_error('Employee not found.', 404);
    }
}

public function print_commitment_letter($employee_id)
{
    $this->load->model('hr_model2');
    $data['employee'] = $this->hr_model2->get_employee_for_document($employee_id);

    if ($data['employee']) {
        // Load the new view file we will create in the next step
        $this->load->view('templateo/commitment_letter_template', $data);
    } else {
        show_error('Employee not found.', 404);
    }
}

public function print_commitment_letter_marsoom($employee_id)
{
    $this->load->model('hr_model2');
    $data['employee'] = $this->hr_model2->get_employee_for_document($employee_id);

    if ($data['employee']) {
        // Load the new view file we will create in the next step
        $this->load->view('templateo/commitment_letter_template_marsoom', $data);
    } else {
        show_error('Employee not found.', 404);
    }
}
public function print_eos_certificate($employee_id)
{
    $this->load->model('hr_model2');
    $data['employee'] = $this->hr_model2->get_employee_for_document($employee_id);

    if ($data['employee']) {
        // Load the new view file we will create in the next step
        $this->load->view('templateo/eos_certificate_template', $data);
    } else {
        show_error('Employee not found.', 404);
    }
}
public function print_embassy_letter($employee_id)
{
    $this->load->model('hr_model2');
    $data['employee'] = $this->hr_model2->get_employee_for_document($employee_id);

    if ($data['employee']) {
        // Load the new view file we will create in the next step
        $this->load->view('templateo/embassy_letter_template', $data);
    } else {
        show_error('Employee not found.', 404);
    }
}
    /**
     * Handles the AJAX request to get all documents for the logged-in user.
     */
    public function get_user_documents() {
        $employee_id = $this->session->userdata('username');

        if (!$employee_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
            return;
        }

        $documents = $this->hr_model2->fetch_documents_by_employee($employee_id);

        if ($documents !== false) {
             echo json_encode(['status' => 'success', 'documents' => $documents]);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Could not retrieve documents.']);
        }
    }
    /**
     * Securely serves a document for viewing/downloading.
     * @param int $document_id The ID of the document from the database.
     */
    public function view_document($document_id)
    {
        // Ensure the user is logged in
        $employee_id = $this->session->userdata('username');
        if (!$employee_id) {
            show_error('You are not authorized to view this file.', 403);
            return;
        }

        // Get the document details from the model
        // You might need to create this new model function
        $document = $this->hr_model2->get_document_by_id($document_id);

        // Security Check: Make sure the document exists AND belongs to the logged-in user
        if (!$document || $document->employee_id != $employee_id) {
            show_error('File not found or access denied.', 404);
            return;
        }

        // Load the download helper
        $this->load->helper('download');

        // Get the file path from the database record
        $file_path = FCPATH . $document->file_path; // FCPATH gives the full server path

        // Check if the file actually exists on the server
        // ... inside view_document()
        if (file_exists($file_path)) {
            force_download($file_path, NULL);
        } else {
            // --- TEMPORARY DEBUGGING BLOCK ---
            echo "<h1>File Not Found - Debugging Info</h1>";
            echo "<p><strong>CodeIgniter's FCPATH constant is:</strong><br>" . FCPATH . "</p>";
            echo "<p><strong>Path from Database (doc->file_path) is:</strong><br>" . $document->file_path . "</p>";
            echo "<hr>";
            echo "<p><strong>The FULL path being checked is:</strong><br>" . $file_path . "</p>";
            die(); // Stop the script here to show the debug info
        }
//...
    }
    // ... your other controller methods ...

    public function submit_vacation_request() {
        try {
            // Set headers for CORS and JSON response
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
            
            // Handle preflight OPTIONS request
            if ($this->input->method() === 'options') {
                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'ok']));
                return;
            }
            
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(405)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'طريقة الطلب غير مسموحة'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }
            
            // Get input data from JSON payload
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            // Fallback to form data if JSON is empty
            if (empty($data)) {
                $data = $this->input->post();
            }
            
            // Validate input
            if (empty($data)) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'لم يتم استلام أي بيانات'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }
            
            // Check if user is logged in
            $username = $this->session->userdata('username');
            $name = $this->session->userdata('name');
            
            if (!$username) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'يجب تسجيل الدخول أولاً'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }
            
            // Set validation rules
            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('leaveType', 'نوع الإجازة', 'required');
            $this->form_validation->set_rules('startDate', 'تاريخ البدء', 'required|regex_match[/^\d{4}-\d{2}-\d{2}$/]');
            $this->form_validation->set_rules('endDate', 'تاريخ الانتهاء', 'required|regex_match[/^\d{4}-\d{2}-\d{2}$/]');
            $this->form_validation->set_rules('reentryVisa', 'تأشيرة الخروج والعودة', 'required');
            $this->form_validation->set_rules('reason', 'السبب', 'required|max_length[500]');
            
            if (isset($data['reentryVisa']) && $data['reentryVisa'] === 'نعم') {
                $this->form_validation->set_rules('visaDays', 'فترة التأشيرة', 'required');
            }
            
            if ($this->form_validation->run() == FALSE) {
                $response = [
                    'status' => 'error',
                    'message' => 'خطأ في البيانات: ' . strip_tags(validation_errors())
                ];
            } else {
                // Additional business logic validations
                // Check if end date is before start date
                if (strtotime($data['endDate']) < strtotime($data['startDate'])) {
                     $response = [
                        'status' => 'error',
                        'message' => 'تاريخ الانتهاء لا يمكن أن يكون قبل تاريخ البدء'
                    ];
                }
                // Check if overlapping vacation request exists
                else if ($this->hr_model2->vacation_request_overlaps($username, $data['startDate'], $data['endDate'])) {
                    $response = [
                        'status' => 'error',
                        'message' => 'لديك بالفعل طلب إجازة متداخل مع هذه التواريخ'
                    ];
                } else {
                    // ==========================================================
                    // =        SERVER-SIDE CALCULATION OF WORKING DAYS         =
                    // ==========================================================
                    $start_date_obj = new DateTime($data['startDate']);
                    $end_date_obj = new DateTime($data['endDate']);
                    // We add one day to the end date to make the range inclusive
                    $end_date_obj->modify('+1 day'); 

                    $interval = new DateInterval('P1D');
                    $date_range = new DatePeriod($start_date_obj, $interval, $end_date_obj);

                    $total_days = 0;
                    foreach ($date_range as $date) {
                        // format('N') returns day of week: 1 (Mon) to 7 (Sun). 
                        // Friday is 5, Saturday is 6. We count days that are NOT 5 or 6.
                        if ($date->format('N') != 5 && $date->format('N') != 6) { 
                            $total_days++;
                        }
                    }
                    // ==========================================================
                    // =               END OF SERVER-SIDE CALCULATION           =
                    // ==========================================================

                    // Process visa days
                    $visaDays = ($data['reentryVisa'] === 'نعم' && isset($data['visaDays'])) ? $data['visaDays'] : '';
                    
                    // Prepare data for insertion
                    $insertData = [
                        'username' => $username,
                        'name' => $name ? $name : $username,
                        'start_date' => $data['startDate'],
                        'end_date' => $data['endDate'],
                        'total_days' => $total_days, // <-- ADDED THE CALCULATED VALUE
                        'type' => $data['leaveType'],
                        'reason' => trim($data['reason']),
                        'reentryvisa' => $data['reentryVisa'],
                        'visadays' => $visaDays,
                        'status' => 'pending', // Use a consistent status like 'pending'
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s')
                    ];
                    
                    // Use a transaction for safe database insertion
                    $this->db->trans_start();
                    $result = $this->hr_model2->insert_vacation_request($insertData);
                    $this->db->trans_complete();
                    
                    if ($this->db->trans_status() === FALSE || !$result) {
                        $db_error = $this->db->error();
                        log_message('error', 'Database error on vacation insert: ' . print_r($db_error, true));
                        $response = [
                            'status' => 'error',
                            'message' => 'فشل في إرسال طلب الإجازة بسبب خطأ في قاعدة البيانات'
                        ];
                    } else {
                        $request_id = $this->db->insert_id();
                        log_message('info', "Vacation request submitted successfully. ID: {$request_id}, User: {$username}");
                        
                        $response = [
                            'status' => 'success',
                            'message' => 'تم إرسال طلب الإجازة بنجاح. سيتم مراجعته من قبل إدارة الموارد البشرية',
                            'request_id' => $request_id,
                            'vacation_days' => $total_days // Return the calculated days
                        ];
                    }
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Unexpected error in submit_vacation_request: ' . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()
            ];
        }
        
        // Final JSON response output
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE));
    }
// Helper method for vacation business rules validation
private function _validate_vacation_business_rules($data) {
    try {
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['startDate']) ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['endDate'])) {
            return [
                'valid' => false,
                'message' => 'تنسيق التاريخ غير صحيح. استخدم YYYY-MM-DD'
            ];
        }
        
        // Validate dates
        $start_date = new DateTime($data['startDate']);
        $end_date = new DateTime($data['endDate']);
        $today = new DateTime('today');
        
        // Check if start date is not in the past (allow today)
        if ($start_date < $today) {
            return [
                'valid' => false,
                'message' => 'تاريخ البدء لا يمكن أن يكون في الماضي'
            ];
        }
        
        // Check if end date is after or equal to start date
        if ($end_date < $start_date) {
            return [
                'valid' => false,
                'message' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء أو في نفس اليوم'
            ];
        }
        
        // Calculate vacation duration
        $duration = $start_date->diff($end_date)->days + 1;
        
        // Check maximum vacation duration (adjust as needed)
        if ($duration > 365) {
            return [
                'valid' => false,
                'message' => 'لا يمكن طلب إجازة لأكثر من 365 يوم'
            ];
        }
        
        // Check minimum vacation duration
        if ($duration < 1) {
            return [
                'valid' => false,
                'message' => 'يجب أن تكون الإجازة يوم واحد على الأقل'
            ];
        }
        
        // Validate vacation type
        $valid_types = [
            'سنوية',
            'مرضية',
            'زواج',
            'وفاة زوج، اصول او فروع',
            'مولود جديد'
        ];
        
        if (!in_array($data['leaveType'], $valid_types)) {
            return [
                'valid' => false,
                'message' => 'نوع الإجازة غير صحيح'
            ];
        }
        
        // Validate reentry visa
        if (!in_array($data['reentryVisa'], ['نعم', 'لا'])) {
            return [
                'valid' => false,
                'message' => 'خيار تأشيرة الخروج والعودة غير صحيح'
            ];
        }
        
        // Validate visa days if reentry visa is required
        if ($data['reentryVisa'] === 'نعم') {
            if (empty($data['visaDays'])) {
                return [
                    'valid' => false,
                    'message' => 'يرجى تحديد فترة التأشيرة'
                ];
            }
            
            $valid_visa_periods = [
                '2 شهر/شهور',
                '3 شهر/شهور',
                '4 شهر/شهور',
                '5 شهر/شهور',
                '6 شهر/شهور'
            ];
            
            if (!in_array($data['visaDays'], $valid_visa_periods)) {
                return [
                    'valid' => false,
                    'message' => 'فترة التأشيرة غير صحيحة'
                ];
            }
        }
        
        // Validate reason length
        if (strlen(trim($data['reason'])) < 5) {
            return [
                'valid' => false,
                'message' => 'السبب يجب أن يكون 5 أحرف على الأقل'
            ];
        }
        
        if (strlen(trim($data['reason'])) > 500) {
            return [
                'valid' => false,
                'message' => 'السبب لا يجب أن يتجاوز 500 حرف'
            ];
        }
        
        return ['valid' => true];
        
    } catch (Exception $e) {
        log_message('error', 'Error in vacation validation: ' . $e->getMessage());
        return [
            'valid' => false,
            'message' => 'خطأ في التحقق من صحة البيانات'
        ];
    }
}

// Add this method to your Users1 controller
public function get_user_vacation_requests() {
    try {
        // Check if user is logged in
        $username = $this->session->userdata('username');
        
        if (!$username) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'غير مصرح بالوصول'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Load model if not loaded
        if (!isset($this->hr_model2)) {
            $this->load->model('hr_model2');
        }
        
        // Get user's vacation requests
        $requests = $this->hr_model2->get_user_vacation_requests($username);
        
        // Debug log
        log_message('debug', 'Fetched ' . count($requests) . ' vacation requests for user: ' . $username);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data' => $requests,
                'count' => count($requests)
            ], JSON_UNESCAPED_UNICODE));
            
    } catch (Exception $e) {
        log_message('error', 'Error in get_user_vacation_requests: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ في الخادم'
            ], JSON_UNESCAPED_UNICODE));
    }
}

public function cancel_vacation_request() {
    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $username = $this->session->userdata('username');
        $request_id = isset($data['request_id']) ? (int)$data['request_id'] : 0;
        
        if (!$username || !$request_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'بيانات غير صحيحة'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Load model if not loaded
        if (!isset($this->hr_model2)) {
            $this->load->model('hr_model2');
        }
        
        // Check if request belongs to user and can be cancelled
        $request = $this->hr_model2->get_vacation_request($request_id, $username);
        
        if (!$request) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'طلب الإجازة غير موجود'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        if ($request['status'] !== 'pending') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'لا يمكن إلغاء هذا الطلب - الحالة: ' . $request['status']
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        $result = $this->hr_model2->cancel_vacation_request($request_id);
        
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'تم إلغاء طلب الإجازة بنجاح'
                ], JSON_UNESCAPED_UNICODE));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'فشل في إلغاء الطلب'
                ], JSON_UNESCAPED_UNICODE));
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error in cancel_vacation_request: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ في الخادم'
            ], JSON_UNESCAPED_UNICODE));
    }
}

// Temporary method to test database
public function test_vacation_db() {
    $username = $this->session->userdata('username');
    
    if (!$username) {
        echo "User not logged in<br>";
        return;
    }
    
    echo "Username: {$username}<br>";
    
    // Check if table exists
    if ($this->db->table_exists('vacations')) {
        echo "Vacations table exists<br>";
        
        // Show table structure
        $fields = $this->db->list_fields('vacations');
        echo "Table fields: " . implode(', ', $fields) . "<br>";
        
        // Count total records
        $total = $this->db->count_all('vacations');
        echo "Total vacation records: {$total}<br>";
        
        // Count user records
        $this->db->where('username', $username);
        $user_count = $this->db->count_all_results('vacations');
        echo "User vacation records: {$user_count}<br>";
        
        // Show recent records
        $this->db->limit(5);
        $this->db->order_by('date DESC, time DESC');
        $query = $this->db->get('vacations');
        $recent = $query->result_array();
        
        echo "Recent records:<br>";
        echo "<pre>" . print_r($recent, true) . "</pre>";
        
    } else {
        echo "Vacations table does NOT exist<br>";
        echo "Available tables: " . implode(', ', $this->db->list_tables()) . "<br>";
    }
}
public function submit_overtime_request() {
    try {
        // Get JSON input
        $json = file_get_contents('php://input');
        $input_data = json_decode($json, true);
        
        // Check if user is logged in
        $username = $this->session->userdata('username');
        $name = $this->session->userdata('name');
        
        if (!$username) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'غير مصرح بالوصول'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Validate input data
        if (!$input_data || !isset($input_data['date']) || !isset($input_data['hours']) || !isset($input_data['reason'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'بيانات غير مكتملة'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Extract and validate data
        $date = trim($input_data['date']);
        $hours = floatval($input_data['hours']);
        $reason = trim($input_data['reason']);
        
        // Validation
        if (empty($date) || empty($reason) || $hours <= 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'جميع الحقول مطلوبة ويجب أن تكون صحيحة'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'تنسيق التاريخ غير صحيح'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Validate hours range
        if ($hours < 0.5 || $hours > 12) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'عدد الساعات يجب أن يكون بين 0.5 و 12 ساعة'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Check if date is not in the future
        $request_date = new DateTime($date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        if ($request_date > $today) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'لا يمكن طلب عمل إضافي لتاريخ في المستقبل'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Check if date is not too old (more than 30 days)
        $thirty_days_ago = new DateTime();
        $thirty_days_ago->sub(new DateInterval('P30D'));
        
        if ($request_date < $thirty_days_ago) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'لا يمكن طلب عمل إضافي لتاريخ أقدم من 30 يوم'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Load model
        if (!isset($this->hr_model2)) {
            $this->load->model('hr_model2');
        }
        
        // Check if overtime request already exists for this date
        if ($this->hr_model2->overtime_request_exists($username, $date)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'يوجد طلب عمل إضافي آخر لنفس التاريخ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Prepare data for insertion
        $overtime_data = [
            'date' => $date,
            'hours' => number_format($hours, 1), // Store with 1 decimal place
            'reason' => $reason,
            'username' => $username,
            'name' => $name ?: $username,
            'status' => 'pending'
        ];
        
        // Insert overtime request
        $result = $this->hr_model2->insert_overtime_request($overtime_data);
        
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'تم إرسال طلب العمل الإضافي بنجاح',
                    'request_id' => $result
                ], JSON_UNESCAPED_UNICODE));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'فشل في حفظ طلب العمل الإضافي'
                ], JSON_UNESCAPED_UNICODE));
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error in submit_overtime_request: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ في الخادم'
            ], JSON_UNESCAPED_UNICODE));
    }
}

public function get_user_overtime_requests() {
    try {
        // Check if user is logged in
        $username = $this->session->userdata('username');
        
        if (!$username) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'غير مصرح بالوصول'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Load model if not loaded
        if (!isset($this->hr_model2)) {
            $this->load->model('hr_model2');
        }
        
        // Get user's overtime requests
        $requests = $this->hr_model2->get_user_overtime_requests($username);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data' => $requests,
                'count' => count($requests)
            ], JSON_UNESCAPED_UNICODE));
            
    } catch (Exception $e) {
        log_message('error', 'Error in get_user_overtime_requests: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ في الخادم'
            ], JSON_UNESCAPED_UNICODE));
    }
}

public function cancel_overtime_request() {
    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $username = $this->session->userdata('username');
        $request_id = isset($data['request_id']) ? (int)$data['request_id'] : 0;
        
        if (!$username || !$request_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'بيانات غير صحيحة'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        // Load model if not loaded
        if (!isset($this->hr_model2)) {
            $this->load->model('hr_model2');
        }
        
        // Check if request belongs to user and can be cancelled
        $request = $this->hr_model2->get_overtime_request($request_id, $username);
        
        if (!$request) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'طلب العمل الإضافي غير موجود'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        if ($request['status'] !== 'pending') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'لا يمكن إلغاء هذا الطلب'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }
        
        $result = $this->hr_model2->cancel_overtime_request($request_id);
        
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'تم إلغاء طلب العمل الإضافي بنجاح'
                ], JSON_UNESCAPED_UNICODE));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'فشل في إلغاء الطلب'
                ], JSON_UNESCAPED_UNICODE));
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error in cancel_overtime_request: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ في الخادم'
            ], JSON_UNESCAPED_UNICODE));
    }
}
     

   public function getAttendanceData() {
    try {
        $emp_code = $this->session->userdata('username');
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        if ($data === null || !isset($data['selectedDate'])) {
            throw new Exception('selectedDate is missing or invalid data format.');
        }
        
        $date = $data['selectedDate'];

        if (!$emp_code) {
            throw new Exception('Employee code is missing. Please ensure you are logged in.');
        }

        // Step 1: Get attendance info from the model FIRST
        $attendanceInfo = $this->hr_model2->getAttendanceForDate($emp_code, $date);

        // Initialize default values
        $workDuration = '00:00:00';
        $formattedDifference = '00:00:00';
        $status = 'incomplete'; // Default status if there's no work duration

        // Step 2: Check if we have a valid work duration from the model to calculate
        if (!empty($attendanceInfo['workDuration']) && $attendanceInfo['workDuration'] !== '00:00:00') {
            $workDuration = $attendanceInfo['workDuration'];

            // Convert HH:MM:SS string to total seconds
            $timeParts = explode(':', $workDuration);
            $workDurationSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + (isset($timeParts[2]) ? $timeParts[2] : 0);
            
            // Step 3: Perform the calculation based on the 9-hour policy
            $requiredSeconds = 9 * 3600;
            $differenceSeconds = $workDurationSeconds - $requiredSeconds;

            if ($differenceSeconds < 0) {
                $status = 'incomplete';
                $sign = '-';
            } else {
                $status = 'complete';
                $sign = '+';
            }

            // Format the final difference string with the correct sign
            $absDifferenceSeconds = abs($differenceSeconds);
            $hours = floor($absDifferenceSeconds / 3600);
            $minutes = floor(($absDifferenceSeconds % 3600) / 60);
            $seconds = $absDifferenceSeconds % 60;
            $formattedDifference = sprintf('%s%02d:%02d:%02d', $sign, $hours, $minutes, $seconds);
        }
        
        // Step 4: Build the final response using the CALCULATED values
        $response = [
            'date'           => $attendanceInfo['date'] ?? $date,
            'firstCheckIn'   => $attendanceInfo['firstCheckIn'] ?? 'غير محدد',
            'lastCheckOut'   => $attendanceInfo['lastCheckOut'] ?? 'غير محدد',
            'workDuration'   => $workDuration,
            'timeDifference' => $formattedDifference, // Use the calculated value
            'status'         => $status               // Add the new status field
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));

    } catch (Exception $e) {
        log_message('error', 'Exception in getAttendanceData: ' . $e->getMessage());
        $this->output->set_status_header(500);
        $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'Internal error: ' . $e->getMessage()]));
    }
}

public function getAttendanceEventsForRange() {
    try {
        $emp_code = $this->session->userdata('username');
        $start_date = $this->input->get('start');
        $end_date = $this->input->get('end');

        if (!$emp_code || !$start_date || !$end_date) {
            throw new Exception('Required parameters are missing.');
        }

        // Assumption: Your model returns an array where each item has 'date' and 'has_attendance'.
        $attendanceRecords = $this->hr_model2->getAttendanceInRange($emp_code, $start_date, $end_date);
        $attendanceByDate = [];
        foreach ($attendanceRecords as $record) {
            $attendanceByDate[$record['date']] = $record;
        }

        $events = [];
        $requiredSeconds = 9 * 3600; // 9 hours

        // Create a period to loop through every day in the requested range
        $period = new DatePeriod(
             new DateTime($start_date),
             new DateInterval('P1D'),
             (new DateTime($end_date))->modify('+1 day')
        );

        foreach ($period as $day) {
            $currentDate = $day->format('Y-m-d');
            $status = '';

            // Check if we have a record for this specific day from the database
            if (isset($attendanceByDate[$currentDate])) {
                $record = $attendanceByDate[$currentDate];

                // *** THIS IS THE KEY FIX ***
                // First, check if the employee was actually present ('has_attendance' is true)
                if ($record['has_attendance']) {
                    if (!empty($record['workDuration']) && $record['workDuration'] !== '00:00:00') {
                        $timeParts = explode(':', $record['workDuration']);
                        $workDurationSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + (isset($timeParts[2]) ? $timeParts[2] : 0);
                        
                        // If present, check if work was complete or incomplete
                        $status = ($workDurationSeconds >= $requiredSeconds) ? 'complete' : 'incomplete';
                    } else {
                        // Present but no work duration calculated means incomplete
                        $status = 'incomplete';
                    }
                } else {
                    // Record exists but has_attendance is false, so treat as absent/weekend
                    $dayOfWeek = date('w', strtotime($currentDate));
                    if ($dayOfWeek == 5 || $dayOfWeek == 6) { // 5=Friday, 6=Saturday
                        $status = 'weekend';
                    } else {
                        $status = 'absent';
                    }
                }
            } else {
                // No record found in the database for this day, so it's an absence or weekend
                $dayOfWeek = date('w', strtotime($currentDate));
                if ($dayOfWeek == 5 || $dayOfWeek == 6) {
                    $status = 'weekend';
                } else {
                    $status = 'absent';
                }
            }

            // Add the day to our event list unless it's a weekend
            if ($status !== 'weekend') {
                 $events[] = [
                    'date' => $currentDate,
                    'status' => $status 
                ];
            }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($events));

    } catch (Exception $e) {
        log_message('error', 'Exception in getAttendanceEventsForRange: ' . $e->getMessage());
        $this->output->set_status_header(500);
        $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'Internal error: ' . $e->getMessage()]));
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
        $this->hr_model2->update_attendance_summary($id,$id2);

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



    $data['get_salary_sheet'] = $this->hr_model2->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model2->get_employees();

        
        $attendance_data = $this->hr_model2->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/m1_hr', $data);
    }

    public function payroll_view101() {

    $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];



    $data['get_salary_sheet'] = $this->hr_model2->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model2->get_employees();

   //  $data['get_attendance_summary'] = $this->hr_model2->get_attendance_summary($id);


        
        $attendance_data = $this->hr_model2->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/payroll_view101', $data);
    }

       public function violations() {

    $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];



    $data['get_violations_summary'] = $this->hr_model2->get_violations_summary($id);
      

     

        $this->load->view('templateo/violations', $data);
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



    $data['get_salary_sheet'] = $this->hr_model2->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model2->get_employees();

        
        $attendance_data = $this->hr_model2->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/m4_hr', $data);
    }

  public function save_attendance_summary($sheet_id = null)
{
    // خذ id_sheet من الرابط كما هو (بدون تحويل لنوع) لضمان التطابق حتى لو كانت قيمته نصية مثل "2025/08"
    $route_id = $this->uri->segment(3, null);

    


     


    $id_sheet = ($sheet_id !== null) ? $sheet_id : $route_id;

    if ($id_sheet === null || $id_sheet === '' ) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'msg' => 'id_sheet غير صحيح']));
    }

    // اقرأ JSON
    $raw  = $this->input->raw_input_stream;
    $data = json_decode($raw, true);
    $rows = isset($data['rows']) && is_array($data['rows']) ? $data['rows'] : [];

    $this->db->trans_start();

    // 1) حذف كل السجلات الخاصة بهذا المسير (مطابقة نصية مباشرة)
    $this->db->where('id_sheet', $id_sheet)->delete('attendance_summary');
    $deleted_rows = $this->db->affected_rows();

    // (اختياري/احتياطي) لو id_sheet رقمي وقد تكون السجلات القديمة مخزنة كـ INT
    if (ctype_digit((string)$id_sheet)) {
        $this->db->where('id_sheet', (int)$id_sheet)->delete('attendance_summary');
        $deleted_rows += $this->db->affected_rows();
    }

    // 2) إدخال الدُفعة الجديدة
    $inserted_rows = 0;
    if (!empty($rows)) {
        $batch = [];
        foreach ($rows as $r) {
            if (empty($r['emp_id'])) continue;
$this->hr_model2->delete_attendance_summary($sheet_id);
            $batch[] = [
                'emp_id'        => $r['emp_id'],
                'emp_name'      => $r['emp_name'] ?? '',
                'absence'       => (int)($r['absence'] ?? 0),
                'minutes_late'  => (int)($r['minutes_late'] ?? 0),
                'minutes_early' => (int)($r['minutes_early'] ?? 0),
                'single_thing'  => (int)($r['single_thing'] ?? 0),
                'id_sheet'      => $id_sheet, // احفظ بنفس القيمة التي حذفت بها
            ];
        }
        if (!empty($batch)) {
            $this->db->insert_batch('attendance_summary', $batch);
            if ($this->db->error()['code'] != 0) {
                $err = $this->db->error();
                $this->db->trans_rollback();
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'msg' => $err]));
            }
            $inserted_rows = count($batch);
        }
    }

    $this->db->trans_complete();

    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status'        => 'ok',
            'deleted_rows'  => (int)$deleted_rows,
            'inserted_rows' => (int)$inserted_rows
        ]));
}






      public function attendance_view() {

    $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];



    $data['get_salary_sheet'] = $this->hr_model2->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model2->get_employees();

        
        $attendance_data = $this->hr_model2->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/m44_hr', $data);
    }


     public function payroll_view1() {

    $data['id'] = $this->uri->segment(3,0);
           $id = $data['id'];



    $data['get_salary_sheet'] = $this->hr_model2->get_salary_sheet($id);
      

    

     $data['employees'] = $this->hr_model2->get_employees();

        
        $attendance_data = $this->hr_model2->get_attendance_data();
 
        $data['attendance_data'] = $attendance_data;

        $this->load->view('templateo/m3_hr', $data);
    }





    public function login(){


       // Title of the page // 
      $data['title'] = 'شاشة الدخول';


      // Verify the validity of the entered data username and password// 
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');

      // If the login data is correct// 

      if($this->form_validation->run() === FALSE){    
        $this->load->view('templateo/login1', $data);   
      } else {

         // Define variables to store user data// 
        $username = $this->input->post('username');

         //Password encryption// 
        $password =MD5($this->input->post('password'));
         //Query from the database whether the username and password are correct//
        $user_id = $this->hr_model2->login($username, $password);
       //Entry information query//
        $data = $this->hr_model2->getmydata($user_id);
          //Define an array to hold some user-specific data//
        $type=$data['type'];//type of user//
        $name=$data['name'];//name of user//
      
        $id=$data['id'];//id of user//
        //If the user has validity No is 1//
        if($user_id and $type==1){
                   $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          'type' => $type,     
          'name' => $name
         // 'logged_in' => true
          );              
          $this->session->set_userdata($user_data);  

          $this->hr_model2->my_user_update555();
          

          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in'); 
          $op_name="دخول الى النظام";   
          $this->hr_model2->add_watch($name,$op_name);   

         
          redirect('users/main');


       

          }
          elseif ($user_id and $type==3){
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type, 
          // 'project' => $project, 
          // 'section' => $section,
          'name' => $name
          // 'lang'=>$this->input->post('lang'),  
         // 'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');  
           $op_name="دخول الى النظام";   
          $this->hr_model2->add_watch($name,$op_name);  
          
             
          redirect('users/main');

          } elseif ($user_id and $type==4){
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type, 
          // 'project' => $project, 
          // 'section' => $section,
          'name' => $name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');   
           $op_name="دخول الى النظام";   
          $this->hr_model2->add_watch($name,$op_name);          
          redirect('users/dashbord_analyses');
          }elseif ($user_id and $type==6){
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type, 
          // 'project' => $project, 
          // 'section' => $section,
          'name' => $name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');   
           $op_name="دخول الى النظام";   
          $this->hr_model2->add_watch($name,$op_name);          
          redirect('users/dashbord_analyses');
          }elseif ($user_id and $type==10){
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type, 
          // 'project' => $project, 
          // 'section' => $section,
          'name' => $name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');   
           $op_name="دخول الى النظام";   
          $this->hr_model2->add_watch($name,$op_name);          
          redirect('users/dashbord_analyses105');
          }elseif ($user_id and $type==2){
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type,
          // 'project' => $project, 
          // 'section' => $section,
          'name' => $name,
          // 'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');  
           $op_name="دخول الى النظام";   
          $this->hr_model2->add_watch($name,$op_name);           
          redirect('users/dashbord_analyses');
          }elseif ($user_id and $type==6){
          //create session
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type, 
          'type1' => $type1,      
          'name' => $name,  
          'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');            
          redirect('users/dashbord_in');
          }elseif ($user_id and $type==7){
          //create session
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type, 
          'type1' => $type1,        
          'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');            
          redirect('users/dashbord_in');
          }elseif ($user_id and $type==5){
          //create session
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          //'marid'=> $marid,
          'type' => $type,        
          'type1' => $type1, 
          'lang'=>$this->input->post('lang'),  
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');            
          redirect('users/dashbord');
          }elseif($user_id and $type==3){
          //create session
          $user_data = array(
          'user_id' => $user_id,
          'username' => $username,
          'marid'=> $marid,
          'type' => $type,              
          'type1' => $type1,   
          'logged_in' => true
          );              
          $this->session->set_userdata($user_data);           
          // Set message
          $this->session->set_flashdata('user_loggedin', 'You are now logged in');            
          redirect('users/userindex');
          } 
          else{
          // Set message
          $this->session->set_flashdata('login_failed', 'Login is invalid');
                    redirect('users/login');

          }
        }
    }



            
     




         
        

            
	}
	?>