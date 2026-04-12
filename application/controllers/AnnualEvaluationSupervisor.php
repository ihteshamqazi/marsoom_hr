<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnnualEvaluationSupervisor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('users/login'); return; }

        $this->load->helper(['url','form','security']);
        $this->load->library(['session']);
        $this->load->model('Annual_evaluation_model', 'ev');
        date_default_timezone_set('Asia/Riyadh');
    }

    

     public function index()
{
    $year = (int)$this->input->get('year', true);
    if ($year <= 0) $year = (int)date('Y');

    $super_no = (string)$this->session->userdata('username');
    $team = $this->ev->get_my_team($year, $super_no);

    $data = [
        'title' => 'تقييم الأداء السنوي - تقييم المسؤول المباشر',
        'year'  => $year,
        'team'  => $team,
        'flash' => $this->session->flashdata('msg')
    ];

    // ✅ فقط قائمة الفريق
    $this->load->view('annual_eval/supervisor_list', $data);
}

 
public function form($emp_no = '')
{
    $year = (int)$this->input->get('year', true);
    if ($year <= 0) $year = (int)date('Y');

    $viewer = (string)$this->session->userdata('username');
    if ($viewer === '') { redirect('users/login'); return; }

    // ✅ صلاحيات الأدمن
    $is_admin = in_array($viewer, ['1835','2230'], true);

    $emp_no = trim((string)$emp_no);
    if ($emp_no === '') { show_error('لم يتم تمرير الرقم الوظيفي للموظف.', 400); return; }

    // 1) بيانات الموظف من ملف الموظفين السنوي
    $emp = $this->ev->get_employee_row($year, $emp_no);
    if (!$emp) {
        show_error('لا توجد بيانات مرفوعة لهذا الموظف ضمن ملف الموظفين لهذه السنة.', 404);
        return;
    }

    // 2) صلاحية المشرف يشوف موظفينه فقط (أو الأدمن)
    if (!$is_admin) {
        $sup_no = (string)($emp['supervisor_emp_no'] ?? '');
        if ($sup_no === '' || $sup_no !== $viewer) {
            show_error('غير مصرح لك بعرض/تقييم هذا الموظف.', 403);
            return;
        }
    }

    // 3) درجات CSV
    $discipline   = (float)$this->ev->get_discipline_score($year, $emp_no);
    $courses_base = (float)$this->ev->get_courses_base_score($year, $emp_no);

    // 4) تقييم المشرف (إن وجد)
    $sup_eval = $this->ev->get_supervisor_eval($year, $emp_no);
    if (!is_array($sup_eval)) $sup_eval = [];
    $self_eval = $this->ev->get_self_eval($year, $emp_no);
$has_self  = !empty($self_eval);
$has_sup   = !empty($sup_eval);

    $form_type = (int)($emp['form_type'] ?? 1);
    $total_max = (float)$this->ev->get_form_total_score($form_type);

 // ✅ 5) معايير ببنود تفصيلية + الأوزان الجديدة
  $criteria = $this->ev->get_form_criteria($form_type);

    // ✅ 6) فك أسباب المشرف من JSON (بدون decode_reasons)
    // غيّر أسماء الأعمدة هنا إذا مختلفة عندك:
 
$reasons   = $this->ev->decode_reasons($sup_eval);
$breakdown = $this->ev->decode_breakdown($sup_eval);

    $data = [
    'title'        => 'تقييم الأداء السنوي - تقييم المسؤول المباشر',
    'year'         => $year,
    'emp'          => $emp,
    'sup'          => $sup_eval,
    'discipline'   => $discipline,
    'courses_base' => $courses_base,
    'criteria'     => $criteria,
    'reasons'      => $reasons,
    'breakdown'    => $breakdown,
    'flash'        => $this->session->flashdata('msg'),
    'has_self'     => $has_self,
'has_sup'      => $has_sup,
'show_result'  => ($has_self && $has_sup),
'total_max' => $total_max,
];

    $this->load->view('annual_eval/supervisor_form', $data);
}
    

   public function save()
{
    $year = (int)$this->input->post('year', true);
    if ($year <= 0) $year = (int)date('Y');

    $super_no = (string)$this->session->userdata('username');
    $emp_no   = (string)$this->input->post('emp_no', true);

    $payload = [
        'breakdown' => $this->input->post('breakdown', false),
        'reasons'   => $this->input->post('reasons', false),
        'notes'     => $this->input->post('notes', true),
    ];

    $res = $this->ev->save_supervisor($year, $super_no, $emp_no, $payload);
    $this->session->set_flashdata('msg', $res['msg']);

    if (!empty($res['ok'])) {
        $self = $this->ev->get_self_eval($year, $emp_no);
        if (!empty($self)) {
            redirect('AnnualEvaluation/print_a4/' . rawurlencode($emp_no) . '?year=' . $year);
            return;
        }
    }

    redirect('AnnualEvaluationSupervisor/form/' . $emp_no . '?year=' . $year);
} 
}
