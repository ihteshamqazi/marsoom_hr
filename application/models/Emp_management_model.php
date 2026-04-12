<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emp_management_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_employees() {
        $this->db->order_by('id', 'DESC');
        return $this->db->get('orders.emp1')->result_array();
    }

    public function get_employee_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('orders.emp1')->row_array();
    }

    public function save_employee_data($id = null, $data) {
        if (!empty($id)) {
            $this->db->where('id', $id);
            return $this->db->update('orders.emp1', $data);
        } else {
            return $this->db->insert('orders.emp1', $data);
        }
    }

    public function delete_employee($id) {
        $this->db->where('id', $id);
        return $this->db->delete('orders.emp1');
    }

    public function empty_employees_table() {
        return $this->db->empty_table('orders.emp1');
    }

    public function insert_batch_employees($data) {
        return $this->db->insert_batch('orders.emp1', $data);
    }
    // ========================================================
    // FUNCTIONS FOR ORDERS_EMP TABLE
    // ========================================================

    public function get_all_orders() {
        $this->db->order_by('id', 'DESC');
        return $this->db->get('orders.orders_emp')->result_array();
    }

    public function get_order_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('orders.orders_emp')->row_array();
    }

    public function save_order_data($id = null, $data) {
        if (!empty($id)) {
            $this->db->where('id', $id);
            return $this->db->update('orders.orders_emp', $data);
        } else {
            return $this->db->insert('orders.orders_emp', $data);
        }
    }

    public function delete_order($id) {
        $this->db->where('id', $id);
        return $this->db->delete('orders.orders_emp');
    }

    public function empty_orders_table() {
        return $this->db->empty_table('orders.orders_emp');
    }

    public function insert_batch_orders($data) {
        return $this->db->insert_batch('orders.orders_emp', $data);
    }
    // ========================================================
    // FUNCTIONS FOR APPROVAL_WORKFLOW TABLE
    // ========================================================

    public function get_all_workflows() {
        $this->db->order_by('id', 'DESC');
        return $this->db->get('orders.approval_workflow')->result_array();
    }

    public function get_workflow_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('orders.approval_workflow')->row_array();
    }

    public function save_workflow_data($id = null, $data) {
        if (!empty($id)) {
            $this->db->where('id', $id);
            return $this->db->update('orders.approval_workflow', $data);
        } else {
            return $this->db->insert('orders.approval_workflow', $data);
        }
    }

    public function delete_workflow($id) {
        $this->db->where('id', $id);
        return $this->db->delete('orders.approval_workflow');
    }

    public function empty_workflow_table() {
        return $this->db->empty_table('orders.approval_workflow');
    }
    // ========================================================
    // SERVER-SIDE LOGIC FOR ATTENDANCE LOGS
    // ========================================================
    var $att_column_order = array('id', 'emp_code', 'first_name', 'last_name', 'punch_time', 'punch_state', 'area_alias', 'terminal_alias', 'upload_time', null);
    var $att_column_search = array('emp_code', 'first_name', 'last_name', 'punch_time', 'punch_state', 'area_alias', 'terminal_alias');
    
    private function _get_datatables_query_attendance() {
        $this->db->from('orders.attendance_logs');
        
        $search = $this->input->post('search');
        $search_val = isset($search['value']) ? $search['value'] : null;
        
        $i = 0;
        foreach ($this->att_column_search as $item) {
            if ($search_val) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search_val);
                } else {
                    $this->db->or_like($item, $search_val);
                }
                if (count($this->att_column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }
        
        $order = $this->input->post('order');
        if (isset($order)) {
            $this->db->order_by($this->att_column_order[$order['0']['column']], $order['0']['dir']);
        } else {
            $this->db->order_by('id', 'DESC'); // Latest punches first
        }
    }

    public function get_datatables_attendance() {
        $this->_get_datatables_query_attendance();
        $length = $this->input->post('length');
        $start = $this->input->post('start');
        
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_filtered_attendance() {
        $this->_get_datatables_query_attendance();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_attendance() {
        $this->db->from('orders.attendance_logs');
        return $this->db->count_all_results();
    }

    // CRUD Functions
    public function get_attendance_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('orders.attendance_logs')->row_array();
    }

    public function save_attendance_data($id = null, $data) {
        if (!empty($id)) {
            $this->db->where('id', $id);
            return $this->db->update('orders.attendance_logs', $data);
        } else {
            return $this->db->insert('orders.attendance_logs', $data);
        }
    }

    public function delete_attendance($id) {
        $this->db->where('id', $id);
        return $this->db->delete('orders.attendance_logs');
    }

    public function empty_attendance_table() {
        return $this->db->empty_table('orders.attendance_logs');
    }
    // ========================================================
    // SERVER-SIDE LOGIC FOR MANDATE REQUESTS
    // ========================================================
    var $man_column_order = array('id', 'emp_id', 'department', 'request_date', 'start_date', 'end_date', 'total_amount', 'status', 'payment_status', null);
    var $man_column_search = array('emp_id', 'department', 'transport_mode', 'status', 'payment_status', 'reason');
    
    private function _get_datatables_query_mandate() {
        $this->db->from('orders.mandate_requests');
        
        $search = $this->input->post('search');
        $search_val = isset($search['value']) ? $search['value'] : null;
        
        $i = 0;
        foreach ($this->man_column_search as $item) {
            if ($search_val) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search_val);
                } else {
                    $this->db->or_like($item, $search_val);
                }
                if (count($this->man_column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }
        
        $order = $this->input->post('order');
        if (isset($order)) {
            $this->db->order_by($this->man_column_order[$order['0']['column']], $order['0']['dir']);
        } else {
            $this->db->order_by('id', 'DESC');
        }
    }

    public function get_datatables_mandate() {
        $this->_get_datatables_query_mandate();
        $length = $this->input->post('length');
        $start = $this->input->post('start');
        
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_filtered_mandate() {
        $this->_get_datatables_query_mandate();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_mandate() {
        $this->db->from('orders.mandate_requests');
        return $this->db->count_all_results();
    }

    public function get_mandate_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('orders.mandate_requests')->row_array();
    }

    public function save_mandate_data($id = null, $data) {
        if (!empty($id)) {
            $this->db->where('id', $id);
            return $this->db->update('orders.mandate_requests', $data);
        } else {
            return $this->db->insert('orders.mandate_requests', $data);
        }
    }

    public function delete_mandate($id) {
        $this->db->where('id', $id);
        return $this->db->delete('orders.mandate_requests');
    }

    public function empty_mandate_table() {
        return $this->db->empty_table('orders.mandate_requests');
    }
    // ========================================================
    // SERVER-SIDE LOGIC FOR 6 NEW TABLES
    // ========================================================
    
    // Helper function to keep code clean and small
    private function _shared_datatables_query($table, $column_order, $column_search) {
        $this->db->from("orders.{$table}");
        $search = $this->input->post('search');
        $search_val = isset($search['value']) ? $search['value'] : null;
        
        $i = 0;
        foreach ($column_search as $item) {
            if ($search_val) {
                if ($i === 0) { $this->db->group_start(); $this->db->like($item, $search_val); } 
                else { $this->db->or_like($item, $search_val); }
                if (count($column_search) - 1 == $i) { $this->db->group_end(); }
            }
            $i++;
        }
        $order = $this->input->post('order');
        if (isset($order)) {
            $this->db->order_by($column_order[$order['0']['column']], $order['0']['dir']);
        } else {
            // Check if 'id' exists, otherwise order by the first column
            if (in_array('id', $column_order)) { $this->db->order_by('id', 'DESC'); } 
            else { $this->db->order_by($column_order[0], 'DESC'); }
        }
    }

    // 1. Attendance Summary
    public function get_dt_attendance_summary() {
        $cols = ['id', 'emp_id', 'emp_name', 'absence', 'minutes_late', 'minutes_early', 'single_thing', 'id_sheet'];
        $this->_shared_datatables_query('attendance_summary', $cols, ['emp_id', 'emp_name']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_attendance_summary() { return $this->db->count_all('orders.attendance_summary'); }
    public function count_filtered_attendance_summary() { $cols = ['id', 'emp_id', 'emp_name', 'absence', 'minutes_late', 'minutes_early', 'single_thing', 'id_sheet']; $this->_shared_datatables_query('attendance_summary', $cols, ['emp_id', 'emp_name']); return $this->db->get()->num_rows(); }

    // 2. Payroll Process
    public function get_dt_payroll_process() {
        $cols = ['id', 'n1', 'n2']; // Truncated for order speed, UI will show all
        $this->_shared_datatables_query('payroll_process', $cols, ['id']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_payroll_process() { return $this->db->count_all('orders.payroll_process'); }
    public function count_filtered_payroll_process() { $this->_shared_datatables_query('payroll_process', ['id'], ['id']); return $this->db->get()->num_rows(); }

    // 3. Discounts
    public function get_dt_discounts() {
        $cols = ['id', 'type', 'emp_id', 'emp_name', 'amount', 'date', 'discount_date'];
        $this->_shared_datatables_query('discounts', $cols, ['emp_id', 'emp_name', 'type']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_discounts() { return $this->db->count_all('orders.discounts'); }
    public function count_filtered_discounts() { $cols = ['id', 'type', 'emp_id', 'emp_name', 'amount', 'date', 'discount_date']; $this->_shared_datatables_query('discounts', $cols, ['emp_id', 'emp_name', 'type']); return $this->db->get()->num_rows(); }

    // 4. Reparations
    public function get_dt_reparations() {
        $cols = ['id', 'type', 'emp_id', 'emp_name', 'amount', 'date', 'reparation_date'];
        $this->_shared_datatables_query('reparations', $cols, ['emp_id', 'emp_name', 'type']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_reparations() { return $this->db->count_all('orders.reparations'); }
    public function count_filtered_reparations() { $cols = ['id', 'type', 'emp_id', 'emp_name', 'amount', 'date', 'reparation_date']; $this->_shared_datatables_query('reparations', $cols, ['emp_id', 'emp_name', 'type']); return $this->db->get()->num_rows(); }

    // 5. Employee Violations
    public function get_dt_employee_violations() {
        $cols = ['id', 'employee_id', 'emp_name', 'department', 'amount', 'supervisor_name', 'violation_date'];
        $this->_shared_datatables_query('employee_violations', $cols, ['employee_id', 'emp_name', 'department']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_employee_violations() { return $this->db->count_all('orders.employee_violations'); }
    public function count_filtered_employee_violations() { $cols = ['id', 'employee_id', 'emp_name', 'department', 'amount', 'supervisor_name', 'violation_date']; $this->_shared_datatables_query('employee_violations', $cols, ['employee_id', 'emp_name', 'department']); return $this->db->get()->num_rows(); }

    // 6. Employee Leave Balances
    public function get_dt_employee_leave_balances() {
        $cols = ['employee_id', 'leave_type_slug', 'balance_allotted', 'balance_consumed', 'remaining_balance', 'year'];
        $this->_shared_datatables_query('employee_leave_balances', $cols, ['employee_id', 'leave_type_slug', 'year']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_employee_leave_balances() { return $this->db->count_all('orders.employee_leave_balances'); }
    public function count_filtered_employee_leave_balances() { $cols = ['employee_id', 'leave_type_slug', 'balance_allotted', 'balance_consumed', 'remaining_balance', 'year']; $this->_shared_datatables_query('employee_leave_balances', $cols, ['employee_id', 'leave_type_slug', 'year']); return $this->db->get()->num_rows(); }

    // --- GENERIC CRUD FOR ALL 6 TABLES ---
    public function get_generic_record($table, $where_col, $id) {
        $this->db->where($where_col, $id);
        return $this->db->get("orders.{$table}")->row_array();
    }
    public function save_generic_record($table, $where_col, $id, $data) {
        if (!empty($id)) {
            $this->db->where($where_col, $id);
            return $this->db->update("orders.{$table}", $data);
        } else {
            return $this->db->insert("orders.{$table}", $data);
        }
    }
    public function delete_generic_record($table, $where_col, $id) {
        $this->db->where($where_col, $id);
        return $this->db->delete("orders.{$table}");
    }
    public function empty_generic_table($table) {
        return $this->db->empty_table("orders.{$table}");
    }
    // ========================================================
    // SERVER-SIDE LOGIC FOR END OF SERVICE SETTLEMENTS
    // ========================================================
    public function get_dt_eos_settlements() {
        $cols = ['id', 'employee_id', 'resignation_order_id', 'status', 'final_amount', 'payment_status', 'created_at'];
        $this->_shared_datatables_query('end_of_service_settlements', $cols, ['employee_id', 'resignation_order_id', 'status', 'payment_status']);
        
        if ($this->input->post('length') != -1) {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }
        return $this->db->get()->result_array();
    }
    
    public function count_eos_settlements() { 
        return $this->db->count_all('orders.end_of_service_settlements'); 
    }
    
    public function count_filtered_eos_settlements() { 
        $cols = ['id', 'employee_id', 'resignation_order_id', 'status', 'final_amount', 'payment_status', 'created_at'];
        $this->_shared_datatables_query('end_of_service_settlements', $cols, ['employee_id', 'resignation_order_id', 'status', 'payment_status']); 
        return $this->db->get()->num_rows(); 
    }
    // ========================================================
    // SERVER-SIDE LOGIC FOR RESIGNATION CLEARANCES
    // ========================================================
    public function get_dt_resignation_clearances() {
        $cols = ['id', 'resignation_request_id', 'clearance_parameter_id', 'task_description', 'approver_user_id', 'status', 'created_at'];
        $this->_shared_datatables_query('resignation_clearances', $cols, ['resignation_request_id', 'task_description', 'status', 'approver_user_id']);
        
        if ($this->input->post('length') != -1) {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }
        return $this->db->get()->result_array();
    }
    
    public function count_resignation_clearances() { 
        return $this->db->count_all('orders.resignation_clearances'); 
    }
    
    public function count_filtered_resignation_clearances() { 
        $cols = ['id', 'resignation_request_id', 'clearance_parameter_id', 'task_description', 'approver_user_id', 'status', 'created_at'];
        $this->_shared_datatables_query('resignation_clearances', $cols, ['resignation_request_id', 'task_description', 'status', 'approver_user_id']); 
        return $this->db->get()->num_rows(); 
    }
    // ========================================================
    // SERVER-SIDE LOGIC FOR THE 4 NEW TABLES
    // ========================================================
    // ========================================================
    // SERVER-SIDE LOGIC FOR ORDERS_EMP (THE MASSIVE TABLE)
    // ========================================================
    public function get_dt_orders_emp() {
        $cols = ['id', 'emp_id', 'emp_name', 'type', 'status', 'date', 'order_name'];
        $this->_shared_datatables_query('orders_emp', $cols, ['emp_id', 'emp_name', 'type', 'status', 'order_name']);
        
        if ($this->input->post('length') != -1) {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }
        return $this->db->get()->result_array();
    }
    
    public function count_orders_emp() { 
        return $this->db->count_all('orders.orders_emp'); 
    }
    
    public function count_filtered_orders_emp() { 
        $cols = ['id', 'emp_id', 'emp_name', 'type', 'status', 'date', 'order_name'];
        $this->_shared_datatables_query('orders_emp', $cols, ['emp_id', 'emp_name', 'type', 'status', 'order_name']); 
        return $this->db->get()->num_rows(); 
    }
    // 1. Insurance Discount
    public function get_dt_insurance_discount() {
        $cols = ['id', 'n1', 'n2', 'n3'];
        $this->_shared_datatables_query('insurance_discount', $cols, ['n1', 'n2', 'n3']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_insurance_discount() { return $this->db->count_all('orders.insurance_discount'); }
    public function count_filtered_insurance_discount() { $cols = ['id', 'n1', 'n2', 'n3']; $this->_shared_datatables_query('insurance_discount', $cols, ['n1', 'n2', 'n3']); return $this->db->get()->num_rows(); }

    // 2. New Employees
    public function get_dt_new_employees() {
        $cols = ['id', 'employee_id', 'subscriber_name', 'nationality', 'join_date'];
        $this->_shared_datatables_query('new_employees', $cols, ['employee_id', 'subscriber_name', 'nationality']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_new_employees() { return $this->db->count_all('orders.new_employees'); }
    public function count_filtered_new_employees() { $cols = ['id', 'employee_id', 'subscriber_name', 'nationality', 'join_date']; $this->_shared_datatables_query('new_employees', $cols, ['employee_id', 'subscriber_name', 'nationality']); return $this->db->get()->num_rows(); }

    // 3. Stop Salary
    public function get_dt_stop_salary() {
        $cols = ['id', 'emp_id', 'sheet_id', 'start_date', 'reason', 'date', 'end_date', 'stop_leave_accrual'];
        $this->_shared_datatables_query('stop_salary', $cols, ['emp_id', 'sheet_id', 'reason']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_stop_salary() { return $this->db->count_all('orders.stop_salary'); }
    public function count_filtered_stop_salary() { $cols = ['id', 'emp_id', 'sheet_id', 'start_date', 'reason', 'date', 'end_date', 'stop_leave_accrual']; $this->_shared_datatables_query('stop_salary', $cols, ['emp_id', 'sheet_id', 'reason']); return $this->db->get()->num_rows(); }

    // 4. Work Restrictions
    public function get_dt_work_restrictions() {
        $cols = ['id', 'emp_id', 'emp_name', 'management', 'company', 'working_hours', 'effective_date'];
        $this->_shared_datatables_query('work_restrictions', $cols, ['emp_id', 'emp_name', 'management', 'company']);
        if ($this->input->post('length') != -1) $this->db->limit($this->input->post('length'), $this->input->post('start'));
        return $this->db->get()->result_array();
    }
    public function count_work_restrictions() { return $this->db->count_all('orders.work_restrictions'); }
    public function count_filtered_work_restrictions() { $cols = ['id', 'emp_id', 'emp_name', 'management', 'company', 'working_hours', 'effective_date']; $this->_shared_datatables_query('work_restrictions', $cols, ['emp_id', 'emp_name', 'management', 'company']); return $this->db->get()->num_rows(); }
    public function get_dt_approval_workflow() {
        $cols = ['id', 'order_id', 'order_type', 'approver_id', 'approval_level', 'status', 'action_date', 'is_payment_step'];
        // Use our shared query helper (make sure this helper exists in your model from previous steps)
        $this->_shared_datatables_query('approval_workflow', $cols, ['order_id', 'order_type', 'status', 'approver_id']);
        
        if ($this->input->post('length') != -1) {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }
        return $this->db->get()->result_array();
    }

    public function count_approval_workflow() {
        return $this->db->count_all('orders.approval_workflow');
    }

    public function count_filtered_approval_workflow() {
        $cols = ['id', 'order_id', 'order_type', 'approver_id', 'approval_level', 'status', 'action_date', 'is_payment_step'];
        $this->_shared_datatables_query('approval_workflow', $cols, ['order_id', 'order_type', 'status', 'approver_id']);
        return $this->db->get()->num_rows();
    }
}
// NO CLOSING PHP TAG HERE!