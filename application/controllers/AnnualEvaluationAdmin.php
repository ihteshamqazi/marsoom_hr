<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnnualEvaluationAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('users/login'); return; }

        $this->load->helper(['url','form','security']);
        $this->load->library(['session']);
        $this->load->model('Annual_evaluation_model', 'ev');
        date_default_timezone_set('Asia/Riyadh');

        if (!$this->ev->is_admin()) { show_error('غير مصرح لك.', 403); return; }
    }

    public function index()
    {
        $year = (int)$this->input->get('year', true);
        if ($year <= 0) $year = (int)date('Y');

        $dept = trim((string)$this->input->get('department', true));

        $rows = $this->ev->admin_list($year, $dept);

        $data = [
            'title'=>'الإدارة - التقييم السنوي',
            'year'=>$year,
            'department'=>$dept,
            'rows'=>$rows,
            'flash'=>$this->session->flashdata('msg')
        ];
        $this->load->view('annual_eval/admin_list', $data);
    }

    public function detail($emp_no = '')
    {
        $year = (int)$this->input->get('year', true);
        if ($year <= 0) $year = (int)date('Y');

        $d = $this->ev->admin_detail($year, $emp_no);
        if (!$d) { show_error('غير موجود.', 404); return; }

         $form_type = (int)($d['emp']['form_type'] ?? 1);
$total_max = (float)$this->ev->get_form_total_score($form_type);

$data = [
    'title'=>'تفاصيل تقييم الموظف',
    'year'=>$year,
    'emp'=>$d['emp'],
    'self'=>$d['self'],
    'sup'=>$d['sup'],
    'discipline'=>$d['discipline'],
    'courses_base'=>$d['courses_base'],
    'total_max'=>$total_max,
];

        $this->load->view('annual_eval/admin_detail', $data);
    }
}
