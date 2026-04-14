<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrgStructureEditor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('users/login'); return; }

        $this->load->model('Organizational_structure_model', 'org');
        $this->load->helper(['url', 'form']);
        $this->load->library(['session']);
        date_default_timezone_set('Asia/Riyadh');
    }

    public function index()
    {
        $data['title'] = 'تعديل الهيكل التنظيمي (سحب وإفلات)';

            $this->load->view('template/new_header_and_sidebar', $data ?? []);
            $this->load->view('org_structure_editor_view', $data);
            $this->load->view('template/new_footer');
    }

    // تحميل بيانات الشجرة
    public function api_tree()
    {
        $tree = $this->org->get_tree_for_jstree(); // يشمل غير مرتبطين من emp1
        $this->output
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($tree, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    // تنفيذ نقل موظف تحت مدير جديد (بعد التأكيد من الواجهة)
    public function api_move()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            show_error('Method Not Allowed', 405);
        }

        $employee_id    = trim($this->input->post('employee_id', true));
        $new_manager_id = trim($this->input->post('new_manager_id', true)); // قد تكون '#'

        $res = $this->org->move_employee($employee_id, $new_manager_id);

        $this->output
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    // تصدير Excel (CSV)
    public function export_excel()
    {
        $rows = $this->org->get_structure_for_export(); // مع أسماء الموظفين

        $filename = 'organizational_structure_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        // BOM لعرض العربي صحيح في Excel
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');

        // Header
        fputcsv($out, [
            'ID',
            'Level 1 EmpID','Level 1 Name','Level 1 Job',
            'Level 2 EmpID','Level 2 Name','Level 2 Job',
            'Level 3 EmpID','Level 3 Name','Level 3 Job',
            'Level 4 EmpID','Level 4 Name','Level 4 Job',
            'Level 5 EmpID','Level 5 Name','Level 5 Job',
            'Level 6 EmpID','Level 6 Name','Level 6 Job',
            'Level 7 EmpID','Level 7 Name','Level 7 Job'
        ]);

        foreach ($rows as $r) {
            fputcsv($out, $r);
        }

        fclose($out);
        exit;
    }

    // صفحة طباعة (عرض شجري + طباعة)
    public function print_view()
    {
        $data['title'] = 'طباعة الهيكل التنظيمي';
        $data['tree_json'] = json_encode($this->org->get_tree_for_jstree(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
         
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('org_structure_print_view', $data);
        $this->load->view('template/new_footer');
    }

    // جلب قائمة المدراء/المشرفين للاختيار (بحث)
public function api_managers()
{
    $q = trim($this->input->get('q', true));
    $list = $this->org->search_possible_managers($q);

    $this->output
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($list, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}

// نقل موظف عبر اختيار مدير (بدون سحب)
public function api_move_by_picker()
{
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
        show_error('Method Not Allowed', 405);
    }

    $employee_id    = trim($this->input->post('employee_id', true));
    $new_manager_id = trim($this->input->post('new_manager_id', true)); // يمكن '#'

    $res = $this->org->move_employee($employee_id, $new_manager_id);

    $this->output
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}


public function api_conflicts()
{
    $rows = $this->org->get_multi_manager_employees();
    $this->output->set_content_type('application/json','utf-8')
        ->set_output(json_encode(['ok'=>true,'rows'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}

public function api_occurrences()
{
    $employee_id = trim($this->input->get('employee_id', true));
    $rows = $this->org->get_employee_occurrences($employee_id);
    $this->output->set_content_type('application/json','utf-8')
        ->set_output(json_encode(['ok'=>true,'rows'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}

public function api_move_specific()
{
    if ($this->input->server('REQUEST_METHOD') !== 'POST') show_error('Method Not Allowed',405);

    $employee_id = trim($this->input->post('employee_id', true));
    $old_manager_id = trim($this->input->post('old_manager_id', true)); // المدير القديم الذي تريد حذف الربط منه
    $new_manager_id = trim($this->input->post('new_manager_id', true)); // المدير الجديد

    $res = $this->org->move_employee_specific($employee_id, $old_manager_id, $new_manager_id);

    $this->output->set_content_type('application/json','utf-8')
        ->set_output(json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}

public function api_resigned_linked()
{
    // اختياري: حماية دخول
    // if(!$this->session->userdata('logged_in')) { show_error('Unauthorized', 401); }

    $q = $this->input->get('q', true);
    $rows = $this->OrgStructureEditor_model->get_resigned_linked($q);

    $out = [];
    foreach($rows as $r){
        $out[] = [
            'id'            => (string)$r['id'],
            'text'          => $r['text'],         // اسم + رقم
            'manager_id'    => (string)$r['manager_id'],
            'manager_text'  => $r['manager_text'], // اسم المدير
        ];
    }
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($out, JSON_UNESCAPED_UNICODE));
}





}
