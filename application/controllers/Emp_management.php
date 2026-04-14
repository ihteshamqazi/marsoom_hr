<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emp_management extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Riyadh');
        $this->load->database();
        $this->load->helper(array('url', 'html', 'form'));
        $this->load->library(array('session', 'form_validation'));
        $this->load->model('Emp_management_model', 'emp_model');
    }

    public function index() {
        $data['employees'] = $this->emp_model->get_all_employees();
         $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('manage_employees', $data);
        $this->load->view('template/new_footer');
    }

    public function get_record() {
        $id = $this->input->post('id', TRUE);
        $data = $this->emp_model->get_employee_by_id($id);
        
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data, 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function save_record() {
        // Automatically grabs EVERY field submitted in the form
        $post_data = $this->input->post(NULL, TRUE);
        
        $id = $post_data['id'];
        unset($post_data['id']); // Remove ID before DB insert/update
        
        // Remove CSRF Token before DB insert
        $csrf_name = $this->security->get_csrf_token_name();
        if(isset($post_data[$csrf_name])) {
            unset($post_data[$csrf_name]);
        }

        $result = $this->emp_model->save_employee_data($id, $post_data);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Employee saved perfectly!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function delete_record() {
        $id = $this->input->post('id', TRUE);
        $result = $this->emp_model->delete_employee($id);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Employee deleted.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    
    }

    public function clear_all() {
        $result = $this->emp_model->empty_employees_table();

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'All records wiped.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to wipe table.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function upload_sheet() {
        if (isset($_FILES['employee_file']['name']) && $_FILES['employee_file']['name'] != '') {
            $path = $_FILES['employee_file']['tmp_name'];
            $file_handle = fopen($path, 'r');
            fgetcsv($file_handle); 
            
            $batch_data = array();
            while (($row = fgetcsv($file_handle)) !== FALSE) {
                $batch_data[] = array(
                    'employee_id'     => isset($row[0]) ? $row[0] : '',
                    'subscriber_name' => isset($row[1]) ? $row[1] : '',
                    'id_number'       => isset($row[2]) ? $row[2] : '',
                    'phone'           => isset($row[3]) ? $row[3] : '',
                    'profession'      => isset($row[4]) ? $row[4] : '',
                    'base_salary'     => isset($row[5]) ? $row[5] : 0,
                    'total_salary'    => isset($row[6]) ? $row[6] : 0,
                    'status'          => 'active'
                );
            }
            fclose($file_handle);

            if (!empty($batch_data)) {
                $this->emp_model->insert_batch_employees($batch_data);
            }
        }
        redirect('emp_management/index');
    }
    // ========================================================
    // CONTROLLER METHODS FOR ORDERS_EMP TABLE
    // ========================================================

    public function orders_management() {
        $data['orders'] = $this->emp_model->get_all_orders();
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('manage_orders', $data); 
        $this->load->view('template/new_footer');
    }

    public function get_order() {
        $id = $this->input->post('id', TRUE);
        $data = $this->emp_model->get_order_by_id($id);
        
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data, 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function save_order() {
        // Automatically fetch all POST data and apply XSS filtering
        $post_data = $this->input->post(NULL, TRUE);
        
        $id = $post_data['id'];
        unset($post_data['id']); // Remove ID from the data array to prevent DB errors
        
        // Remove CSRF token from the data array before inserting
        $csrf_name = $this->security->get_csrf_token_name();
        if(isset($post_data[$csrf_name])) {
            unset($post_data[$csrf_name]);
        }

        $result = $this->emp_model->save_order_data($id, $post_data);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Order saved successfully!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function delete_order() {
        $id = $this->input->post('id', TRUE);
        $result = $this->emp_model->delete_order($id);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Order deleted.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function clear_all_orders() {
        $result = $this->emp_model->empty_orders_table();

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'All orders wiped.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to wipe table.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }
    // ========================================================
    // CONTROLLER METHODS FOR APPROVAL_WORKFLOW TABLE
    // ========================================================

    public function approval_workflow() {
        $data['workflows'] = $this->emp_model->get_all_workflows();
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('manage_approval_workflow', $data);
        $this->load->view('template/new_footer');
    }

    public function get_workflow() {
        $id = $this->input->post('id', TRUE);
        $data = $this->emp_model->get_workflow_by_id($id);
        
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data, 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function save_workflow() {
        $post_data = $this->input->post(NULL, TRUE);
        
        $id = $post_data['id'];
        unset($post_data['id']); 
        
        $csrf_name = $this->security->get_csrf_token_name();
        if(isset($post_data[$csrf_name])) {
            unset($post_data[$csrf_name]);
        }

        $result = $this->emp_model->save_workflow_data($id, $post_data);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Workflow saved successfully!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function delete_workflow() {
        $id = $this->input->post('id', TRUE);
        $result = $this->emp_model->delete_workflow($id);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Workflow deleted.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function clear_all_workflows() {
        $result = $this->emp_model->empty_workflow_table();

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'All workflow records wiped.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to wipe table.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }
    // ========================================================
    // CONTROLLER METHODS FOR ATTENDANCE LOGS (FASTER LOADING)
    // ========================================================

    public function attendance_logs() {
        // We DO NOT fetch data here. The view loads instantly and AJAX fetches the data.
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('manage_attendance');
        $this->load->view('template/new_footer');
    }

    public function fetch_attendance_ajax() {
        $list = $this->emp_model->get_datatables_attendance();
        $data = array();
        $no = $this->input->post('start');
        
        foreach ($list as $log) {
            $no++;
            $row = array();
            
            $row[] = $no;
            $row[] = html_escape($log['emp_code']);
            $row[] = html_escape($log['first_name'] . ' ' . $log['last_name']);
            $row[] = html_escape($log['punch_time']);
            
            // Format Punch State nicely
            $state = html_escape($log['punch_state']);
            if (stripos($state, 'in') !== false) {
                $row[] = '<span class="badge bg-success">'.$state.'</span>';
            } elseif (stripos($state, 'out') !== false) {
                $row[] = '<span class="badge bg-danger">'.$state.'</span>';
            } else {
                $row[] = '<span class="badge bg-secondary">'.$state.'</span>';
            }
            
            $row[] = html_escape($log['area_alias']);
            $row[] = html_escape($log['terminal_alias']);
            $row[] = html_escape($log['upload_time']);
            
            $row[] = '<a class="action-btn text-primary edit-btn" data-id="'.$log['id'].'" title="Edit"><i class="fas fa-edit"></i></a> ' .
                     '<a class="action-btn text-danger delete-btn" data-id="'.$log['id'].'" title="Delete"><i class="fas fa-trash-alt"></i></a>';
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->emp_model->count_all_attendance(),
            "recordsFiltered" => $this->emp_model->count_filtered_attendance(),
            "data" => $data,
            "csrf_hash" => $this->security->get_csrf_hash()
        );
        
        echo json_encode($output);
    }

    public function get_attendance() {
        $id = $this->input->post('id', TRUE);
        $data = $this->emp_model->get_attendance_by_id($id);
        
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data, 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function save_attendance() {
        $post_data = $this->input->post(NULL, TRUE);
        
        $id = isset($post_data['id']) ? $post_data['id'] : null;
        unset($post_data['id']); 
        
        $csrf_name = $this->security->get_csrf_token_name();
        if(isset($post_data[$csrf_name])) {
            unset($post_data[$csrf_name]);
        }

        // ==========================================
        // THE FIX: CLEAN UP DATETIME FIELDS FOR MYSQL
        // ==========================================
        $datetime_fields = ['punch_time', 'upload_time', 'created_at'];
        foreach ($datetime_fields as $field) {
            if (isset($post_data[$field])) {
                if (trim($post_data[$field]) === '') {
                    // If the user left it blank, save it as NULL so MySQL doesn't crash
                    $post_data[$field] = NULL; 
                } else {
                    // Replace the HTML5 'T' with a space
                    $post_data[$field] = str_replace('T', ' ', $post_data[$field]);
                    
                    // Add ':00' for seconds if HTML only sent Hours:Minutes
                    if (strlen($post_data[$field]) == 16) {
                        $post_data[$field] .= ':00';
                    }
                }
            }
        }
        // ==========================================

        $result = $this->emp_model->save_attendance_data($id, $post_data);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Attendance log saved perfectly!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function delete_attendance() {
        $id = $this->input->post('id', TRUE);
        $result = $this->emp_model->delete_attendance($id);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Record deleted.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function clear_all_attendance() {
        $result = $this->emp_model->empty_attendance_table();

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'All attendance logs wiped.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to wipe table.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }
    // ========================================================
    // CONTROLLER METHODS FOR MANDATE REQUESTS
    // ========================================================

    public function mandate_requests() {
       $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('manage_mandate_requests');
        $this->load->view('template/new_footer');
    }

    public function fetch_mandate_ajax() {
        $list = $this->emp_model->get_datatables_mandate();
        $data = array();
        $no = $this->input->post('start');
        
        foreach ($list as $req) {
            $no++;
            $row = array();
            
            $row[] = html_escape($req['id']);
            $row[] = html_escape($req['emp_id']);
            $row[] = html_escape($req['department']);
            $row[] = html_escape($req['request_date']);
            $row[] = html_escape($req['start_date']) . ' to ' . html_escape($req['end_date']);
            $row[] = number_format((float)$req['total_amount'], 2);
            
            // Status Badge
            $status = html_escape($req['status']);
            if (strtolower($status) == 'approved') {
                $row[] = '<span class="badge bg-success">'.$status.'</span>';
            } elseif (strtolower($status) == 'rejected') {
                $row[] = '<span class="badge bg-danger">'.$status.'</span>';
            } else {
                $row[] = '<span class="badge bg-warning text-dark">'.($status ? $status : 'Pending').'</span>';
            }

            // Payment Status Badge
            $pay_status = html_escape($req['payment_status']);
            if (strtolower($pay_status) == 'paid') {
                $row[] = '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Paid</span>';
            } else {
                $row[] = '<span class="badge bg-secondary">'.($pay_status ? $pay_status : 'Unpaid').'</span>';
            }

            $row[] = '<a class="action-btn text-primary edit-btn" data-id="'.$req['id'].'" title="Edit"><i class="fas fa-edit"></i></a> ' .
                     '<a class="action-btn text-danger delete-btn" data-id="'.$req['id'].'" title="Delete"><i class="fas fa-trash-alt"></i></a>';
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->emp_model->count_all_mandate(),
            "recordsFiltered" => $this->emp_model->count_filtered_mandate(),
            "data" => $data,
            "csrf_hash" => $this->security->get_csrf_hash()
        );
        echo json_encode($output);
    }

    public function get_mandate() {
        $id = $this->input->post('id', TRUE);
        $data = $this->emp_model->get_mandate_by_id($id);
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data, 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function save_mandate() {
        $post_data = $this->input->post(NULL, TRUE);
        
        $id = isset($post_data['id']) ? $post_data['id'] : null;
        unset($post_data['id']); 
        
        $csrf_name = $this->security->get_csrf_token_name();
        if(isset($post_data[$csrf_name])) {
            unset($post_data[$csrf_name]);
        }

        // Clean up Date/Time fields for MySQL (Removes 'T', adds seconds, handles empty)
        $datetime_fields = ['request_date', 'start_date', 'end_date', 'payment_date'];
        foreach ($datetime_fields as $field) {
            if (isset($post_data[$field])) {
                if (trim($post_data[$field]) === '') {
                    $post_data[$field] = NULL; 
                } else {
                    $post_data[$field] = str_replace('T', ' ', $post_data[$field]);
                    if (strlen($post_data[$field]) == 16) {
                        $post_data[$field] .= ':00';
                    }
                }
            }
        }

        $result = $this->emp_model->save_mandate_data($id, $post_data);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Mandate saved successfully!', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function delete_mandate() {
        $id = $this->input->post('id', TRUE);
        $result = $this->emp_model->delete_mandate($id);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Record deleted.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }

    public function clear_all_mandate() {
        $result = $this->emp_model->empty_mandate_table();
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'All records wiped.', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }
    // ========================================================
    // CONTROLLER METHODS FOR ALL 6 NEW TABLES
    // ========================================================

    // ---- VIEWS ----
            public function v_attendance_summary() { 
                $this->load->view('template/new_header_and_sidebar', $data ?? []); 
                $this->load->view('v_attendance_summary'); 
                $this->load->view('template/new_footer');
            }

            public function v_payroll_process() { 
                $this->load->view('template/new_header_and_sidebar', $data ?? []); 
                $this->load->view('v_payroll_process');
                $this->load->view('template/new_footer');
            }

            public function v_discounts() { 
                $this->load->view('template/new_header_and_sidebar', $data ?? []); 
                $this->load->view('v_discounts');
                $this->load->view('template/new_footer');
            }

            public function v_reparations() {                
                $this->load->view('template/new_header_and_sidebar', $data ?? []);
                $this->load->view('v_reparations'); 
                $this->load->view('template/new_footer');
            }
            
            public function v_employee_violations() { 
                $this->load->view('template/new_header_and_sidebar', $data ?? []);
                $this->load->view('v_employee_violations'); 
                $this->load->view('template/new_footer');
            }
         
            public function v_employee_leave_balances() {
                
                $this->load->view('template/new_header_and_sidebar', $data ?? []);
                $this->load->view('v_employee_leave_balances');
                $this->load->view('template/new_footer');
            }

    // ---- AJAX DATATABLES FETCH ----
    private function _render_ajax_response($draw, $total, $filtered, $data) {
        echo json_encode(["draw" => $draw, "recordsTotal" => $total, "recordsFiltered" => $filtered, "data" => $data, "csrf_hash" => $this->security->get_csrf_hash()]);
    }

    public function dt_attendance_summary() {
        $list = $this->emp_model->get_dt_attendance_summary();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['emp_id'], $r['emp_name'], $r['absence'], $r['minutes_late'], $r['minutes_early'], $r['id_sheet'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_attendance_summary(), $this->emp_model->count_filtered_attendance_summary(), $data);
    }

    public function dt_payroll_process() {
        $list = $this->emp_model->get_dt_payroll_process();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$r['id'], $r['n1'], $r['n2'], $r['n3'], $r['n4'], $r['n5'], $r['n6'], $r['n7'], $btn]; // Showing a few, rest in modal
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_payroll_process(), $this->emp_model->count_filtered_payroll_process(), $data);
    }

    public function dt_discounts() {
        $list = $this->emp_model->get_dt_discounts();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['type'], $r['emp_id'], $r['emp_name'], number_format((float)$r['amount'], 2), $r['discount_date'], $r['sheet_id'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_discounts(), $this->emp_model->count_filtered_discounts(), $data);
    }

    public function dt_reparations() {
        $list = $this->emp_model->get_dt_reparations();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['type'], $r['emp_id'], $r['emp_name'], number_format((float)$r['amount'], 2), $r['reparation_date'], $r['sheet_id'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_reparations(), $this->emp_model->count_filtered_reparations(), $data);
    }

    public function dt_employee_violations() {
        $list = $this->emp_model->get_dt_employee_violations();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['employee_id'], $r['emp_name'], $r['department'], number_format((float)$r['amount'], 2), $r['violation_date'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_employee_violations(), $this->emp_model->count_filtered_employee_violations(), $data);
    }

    public function dt_employee_leave_balances() {
        $list = $this->emp_model->get_dt_employee_leave_balances();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            // Note: This table has no 'id' column. We will use employee_id as the key for editing.
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['employee_id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['employee_id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['employee_id'], $r['leave_type_slug'], $r['balance_allotted'], $r['balance_consumed'], $r['remaining_balance'], $r['year'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_employee_leave_balances(), $this->emp_model->count_filtered_employee_leave_balances(), $data);
    }

    // ---- GENERIC GET, SAVE, DELETE, CLEAR ----
    public function generic_action() {
        $action = $this->input->post('action', TRUE);
        $table = $this->input->post('table', TRUE);
        $primary_key = $this->input->post('primary_key', TRUE) ?: 'id';
        $id = $this->input->post('id', TRUE);
        
        if ($action == 'get') {
            $data = $this->emp_model->get_generic_record($table, $primary_key, $id);
            echo json_encode(['status' => $data ? 'success' : 'error', 'data' => $data, 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
        elseif ($action == 'save') {
            $post_data = $this->input->post(NULL, TRUE);
            unset($post_data['action'], $post_data['table'], $post_data['primary_key'], $post_data['id']);
            $csrf_name = $this->security->get_csrf_token_name();
            if(isset($post_data[$csrf_name])) unset($post_data[$csrf_name]);

            // Fix Datetimes automatically!
            foreach ($post_data as $key => $val) {
                if (strpos($val, 'T') !== false) {
                    $post_data[$key] = str_replace('T', ' ', $val);
                    if (strlen($post_data[$key]) == 16) $post_data[$key] .= ':00';
                }
                if ($val === '') $post_data[$key] = NULL;
            }

            $res = $this->emp_model->save_generic_record($table, $primary_key, $id, $post_data);
            echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'Saved!' : 'Error', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
        elseif ($action == 'delete') {
            $res = $this->emp_model->delete_generic_record($table, $primary_key, $id);
            echo json_encode(['status' => $res ? 'success' : 'error', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
        elseif ($action == 'clear') {
            $res = $this->emp_model->empty_generic_table($table);
            echo json_encode(['status' => $res ? 'success' : 'error', 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }
    // ========================================================
    // CONTROLLER METHODS FOR END OF SERVICE SETTLEMENTS
    // ========================================================
    public function v_end_of_service_settlements() { 
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_end_of_service_settlements'); 
        $this->load->view('template/new_footer');
    }

    public function dt_eos_settlements() {
        $list = $this->emp_model->get_dt_eos_settlements();
        $data = []; 
        $no = $this->input->post('start');
        
        foreach ($list as $r) {
            $no++;
            
            // Status Badge
            $status = html_escape($r['status']);
            $status_badge = ($status == 'Approved') ? '<span class="badge bg-success">Approved</span>' : (($status == 'Rejected') ? '<span class="badge bg-danger">Rejected</span>' : '<span class="badge bg-warning text-dark">'.($status ?: 'Pending').'</span>');

            // Payment Status Badge
            $pay_status = html_escape($r['payment_status']);
            $pay_badge = (strtolower($pay_status) == 'paid') ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Paid</span>' : '<span class="badge bg-secondary">'.($pay_status ?: 'Unpaid').'</span>';

            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            
            $data[] = [
                $no, 
                $r['employee_id'], 
                $r['resignation_order_id'], 
                number_format((float)$r['final_amount'], 2), 
                $status_badge, 
                $pay_badge, 
                $btn
            ];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_eos_settlements(), $this->emp_model->count_filtered_eos_settlements(), $data);
    }
    // ========================================================
    // CONTROLLER METHODS FOR RESIGNATION CLEARANCES
    // ========================================================
    public function v_resignation_clearances() { 
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_resignation_clearances'); 
        $this->load->view('template/new_footer');
    }

    public function dt_resignation_clearances() {
        $list = $this->emp_model->get_dt_resignation_clearances();
        $data = []; 
        $no = $this->input->post('start');
        
        foreach ($list as $r) {
            $no++;
            
            // Status Badge
            $status = html_escape($r['status']);
            if (strtolower($status) == 'approved' || strtolower($status) == 'cleared') {
                $status_badge = '<span class="badge bg-success">'.$status.'</span>';
            } elseif (strtolower($status) == 'rejected') {
                $status_badge = '<span class="badge bg-danger">'.$status.'</span>';
            } else {
                $status_badge = '<span class="badge bg-warning text-dark">'.($status ?: 'Pending').'</span>';
            }

            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            
            // Limit task description length for the table view
            $short_desc = mb_strimwidth(html_escape($r['task_description']), 0, 30, "...");

            $data[] = [
                $no, 
                $r['resignation_request_id'], 
                $r['clearance_parameter_id'], 
                $short_desc,
                $r['approver_user_id'],
                $status_badge, 
                $btn
            ];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_resignation_clearances(), $this->emp_model->count_filtered_resignation_clearances(), $data);
    }
    // ========================================================
    // CONTROLLER METHODS FOR THE 4 NEW TABLES
    // ========================================================

    // ---- VIEWS ----
    public function v_insurance_discount() { 
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_insurance_discount'); 
        $this->load->view('template/new_footer');
    }
    public function v_new_employees() { 
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_new_employees'); 
        $this->load->view('template/new_footer');
    }
    public function v_stop_salary() { 
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_stop_salary'); 
        $this->load->view('template/new_footer');
    }
    public function v_work_restrictions() { 
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_work_restrictions'); 
        $this->load->view('template/new_footer');
    }

    // ---- AJAX DATATABLES FETCH ----
    public function dt_insurance_discount() {
        $list = $this->emp_model->get_dt_insurance_discount();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['n1'], $r['n2'], $r['n3'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_insurance_discount(), $this->emp_model->count_filtered_insurance_discount(), $data);
    }

    public function dt_new_employees() {
        $list = $this->emp_model->get_dt_new_employees();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['employee_id'], $r['subscriber_name'], $r['nationality'], $r['join_date'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_new_employees(), $this->emp_model->count_filtered_new_employees(), $data);
    }

    public function dt_stop_salary() {
        $list = $this->emp_model->get_dt_stop_salary();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['emp_id'], $r['sheet_id'], $r['reason'], $r['start_date'], $r['end_date'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_stop_salary(), $this->emp_model->count_filtered_stop_salary(), $data);
    }

    public function dt_work_restrictions() {
        $list = $this->emp_model->get_dt_work_restrictions();
        $data = []; $no = $this->input->post('start');
        foreach ($list as $r) {
            $no++;
            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            $data[] = [$no, $r['emp_id'], $r['emp_name'], $r['management'], $r['company'], $r['working_hours'], $r['effective_date'], $btn];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_work_restrictions(), $this->emp_model->count_filtered_work_restrictions(), $data);
    }
    public function v_orders_emp() { 
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_orders_emp'); 
        $this->load->view('template/new_footer');
    }

    public function dt_orders_emp() {
        $list = $this->emp_model->get_dt_orders_emp();
        $data = []; 
        // We still need this for DataTables pagination math, but we won't print it!
        $no = $this->input->post('start'); 
        
        foreach ($list as $r) {
            $no++;
            
            // Format Status nicely
            $status = html_escape($r['status']);
            if (strtolower($status) == 'approved') {
                $status_badge = '<span class="badge bg-success">Approved</span>';
            } elseif (strtolower($status) == 'rejected') {
                $status_badge = '<span class="badge bg-danger">Rejected</span>';
            } else {
                $status_badge = '<span class="badge bg-warning text-dark">'.($status ?: 'Pending').'</span>';
            }

            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';
            
            $data[] = [
                html_escape($r['id']), // <--- CHANGED: Now prints the actual Database ID
                html_escape($r['emp_id']), 
                html_escape($r['emp_name']), 
                '<span class="badge bg-info text-dark">'.html_escape($r['type']).'</span>', 
                html_escape($r['order_name']),
                html_escape($r['date']),
                $status_badge, 
                $btn
            ];
        }
        $this->_render_ajax_response($this->input->post('draw'), $this->emp_model->count_orders_emp(), $this->emp_model->count_filtered_orders_emp(), $data);
    }
    public function v_approval_workflow() { 
         $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('v_approval_workflow'); 
        $this->load->view('template/new_footer');
    }
    public function dt_approval_workflow() {
        $list = $this->emp_model->get_dt_approval_workflow();
        $data = [];
        $no = $this->input->post('start');

        foreach ($list as $r) {
            $no++;
            
            // Status Badge
            $status = html_escape($r['status']);
            if (strtolower($status) == 'approved') {
                $status_badge = '<span class="badge bg-success">approved</span>';
            } elseif (strtolower($status) == 'rejected') {
                $status_badge = '<span class="badge bg-danger">rejected</span>';
            } else {
                $status_badge = '<span class="badge bg-warning text-dark">'.($status ?: 'pending').'</span>';
            }

            // Payment Step Badge
            $pay_badge = ($r['is_payment_step'] == 1) ? '<span class="badge bg-primary">Yes</span>' : '<span class="badge bg-secondary">No</span>';

            $btn = '<a class="action-btn text-primary edit-btn" data-id="'.$r['id'].'"><i class="fas fa-edit"></i></a> 
                    <a class="action-btn text-danger delete-btn" data-id="'.$r['id'].'"><i class="fas fa-trash-alt"></i></a>';

            $data[] = [
                html_escape($r['id']), // Using actual ID as requested
                html_escape($r['order_id']),
                '<span class="badge bg-info text-dark">'.html_escape($r['order_type']).'</span>',
                html_escape($r['approver_id']),
                html_escape($r['approval_level']),
                $status_badge,
                html_escape($r['action_date']),
                $pay_badge,
                $btn
            ];
        }

        $this->_render_ajax_response(
            $this->input->post('draw'),
            $this->emp_model->count_approval_workflow(),
            $this->emp_model->count_filtered_approval_workflow(),
            $data
        );
    }
}
// NO CLOSING PHP TAG HERE!