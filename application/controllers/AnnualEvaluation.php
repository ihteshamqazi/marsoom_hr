<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnnualEvaluation extends CI_Controller
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

    $viewer = (string)$this->session->userdata('username');
    if ($viewer === '') {
        redirect('users/login'); return;
    }

    // ✅ صلاحيات الأدمن المطلقة
    $is_admin = in_array($viewer, ['1835','2230','1001'], true);

    // الموظف نفسه (صف السجل الرئيسي لهذا الرقم)
    $emp = $this->ev->get_employee_row($year, $viewer);
    if (!$emp) {
        show_error('لا توجد بيانات مرفوعة لك ضمن ملف الموظفين لهذه السنة.', 404);
        return;
    }

    // درجات CSV
    $discipline   = (float)$this->ev->get_discipline_score($year, $viewer);   // من CSV الانضباط
    $courses_base = (float)$this->ev->get_courses_base_score($year, $viewer); // من CSV الدورات (قاعدة تطوير الذات/الاستثنائي)

    // تقييم الموظف الذاتي (إن وجد)
    $self = $this->ev->get_self_eval($year, $viewer);
    $has_self = !empty($self);
$has_sup  = !empty($this->ev->get_supervisor_eval($year, $viewer));

    // تحديد النموذج
    $form_type = (int)($emp['form_type'] ?? 1);
    $total_max = (float)$this->ev->get_form_total_score($form_type);

    // ✅ تعريف معايير التقييم + الشرح (مهم عشان تظهر في الفيو)
    // ✅ تعريف معايير التقييم + الشرح (مهم عشان تظهر في الفيو)
  $criteria = $this->ev->get_form_criteria($form_type);

    // عنوان الصفحة
    $data = [];
    $data['title']        = 'تقييم الأداء السنوي - تقييم ذاتي';
    $data['year']         = $year;

    // بيانات الموظف
    $data['emp']          = $emp;

    // درجات ثابتة
    $data['discipline']   = $discipline;
    $data['courses_base'] = $courses_base;

    // تقييمات
    $data['self']         = $self ?: [];

    // المعايير (سبب اختفاء النموذج كان عدم تمريرها)
    $data['criteria']     = $criteria;

    // فلاش (اختياري)
    $data['flash'] = $this->session->flashdata('msg');
    $data['reasons'] = $this->ev->decode_reasons($data['self']);
    $data['breakdown'] = $this->ev->decode_breakdown($data['self']);
    $data['has_self']     = $has_self;
$data['has_sup']      = $has_sup;
$data['show_result']  = ($has_self && $has_sup);
$data['total_max'] = $total_max;



    // ✅ تحميل الفيو
           
    $this->load->view('template/new_header_and_sidebar', $data ?? []);
    $this->load->view('annual_eval/self_form', $data);
    $this->load->view('template/new_footer');
}

public function save()
{
    $year = (int)$this->input->post('year', true);
    if ($year <= 0) $year = (int)date('Y');

    $emp_no = (string)$this->session->userdata('username');

    $payload = [
        'breakdown' => $this->input->post('breakdown', false),
        'reasons'   => $this->input->post('reasons', false),
        'notes'     => $this->input->post('notes', true),
    ];

    $res = $this->ev->save_self($year, $emp_no, $payload);
    $this->session->set_flashdata('msg', $res['msg']);

    if (!empty($res['ok'])) {
        $sup = $this->ev->get_supervisor_eval($year, $emp_no);
        if (!empty($sup)) {
            redirect('AnnualEvaluation/print_a4/' . rawurlencode($emp_no) . '?year=' . $year);
            return;
        }
    }

    redirect('AnnualEvaluation?year=' . $year);
}
    

    public function print_a4($emp_no = '')
{
    $year = (int)$this->input->get('year', true);
    if ($year <= 0) $year = (int)date('Y');

    $viewer = (string)$this->session->userdata('username');

    // 1) صلاحيات العرض
    if ($this->ev->is_admin()) {
        // الأدمن يشوف أي أحد
    } else {
        // الموظف يشوف نفسه
        if ($emp_no === $viewer) {
            // ok
        } else {
            // المشرف يشوف موظفينه فقط
            $empRow = $this->ev->get_employee_row($year, $emp_no);
            if (!$empRow) { show_error('الموظف غير موجود.', 404); return; }
            if ((string)$empRow['supervisor_emp_no'] !== $viewer) { show_error('غير مصرح لك بعرض هذا التقييم.', 403); return; }
        }
    }

    // 2) جلب التفاصيل (بيانات الموظف + تقييمه + تقييم المشرف + درجات CSV)
    $d = $this->ev->admin_detail($year, $emp_no); // يرجّع ['emp'=>..,'self'=>..,'sup'=>..,'discipline'=>..,'courses_base'=>..]
    if (!$d || empty($d['emp'])) { show_error('لا توجد بيانات لهذا الموظف.', 404); return; }

    $emp  = $d['emp'];
    $self = $d['self'] ?? null;
    $sup  = $d['sup']  ?? null;

    $form_type = (int)($emp['form_type'] ?? 1);
    $total_max = (float)$this->ev->get_form_total_score($form_type);

    // 3) تعريف المعايير + الشرح (يظهر في الطباعة)
     // 3) تعريف المعايير + الشرح (يظهر في الطباعة)
  
$criteria = $this->ev->get_form_criteria($form_type);
    // 4) تجهيز بيانات العرض للـ View
    $data = [
        'title'        => 'التقييم السنوي للموظف',
        'year'         => $year,
        'emp'          => $emp,
        'self'         => $self,
        'sup'          => $sup,
        'criteria'     => $criteria,
        'discipline'   => (float)($d['discipline'] ?? 0),
        'courses_base' => (float)($d['courses_base'] ?? 0),
        'total_max' => $total_max,
    ];

    // 5) عرض قالب الطباعة
           
    $this->load->view('template/new_header_and_sidebar', $data ?? []);
    $this->load->view('annual_eval/employee_eval_a4_v3_new', $data);
    $this->load->view('template/new_footer');
}


}
