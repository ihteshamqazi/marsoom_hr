<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AnnualIncentives extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        // لو عندك حماية دخول فعّلها:
        // if(!$this->session->userdata('logged_in')){ redirect('users/login'); return; }

        $this->load->model('Annual_incentives_model', 'aim');
        $this->load->helper(['url','form']);
        $this->load->library(['session','form_validation']);
        date_default_timezone_set('Asia/Riyadh');
    }

    public function index(){
        $data['title'] = 'نظام الحوافز السنوية';
        $data['rows']  = $this->aim->list_batches();
            $this->load->view('template/new_header_and_sidebar', $data ?? []);
            $this->load->view('annual_incentives/index', $data);
            $this->load->view('template/new_footer');
    }

    public function create(){
        $data['title'] = 'إنشاء دفعة حوافز سنوية';

            $this->load->view('template/new_header_and_sidebar', $data ?? []);
            $this->load->view('annual_incentives/create', $data);
            $this->load->view('template/new_footer');
    }

    public function store_setup(){
        if($this->input->server('REQUEST_METHOD') !== 'POST'){
            redirect('AnnualIncentives/create'); return;
        }

        $this->form_validation->set_rules('batch_name','اسم الحافز','trim|required');
        $this->form_validation->set_rules('batch_year','السنة','trim|required');
        $this->form_validation->set_rules('budget_total','الميزانية','trim|required');

        if($this->form_validation->run() === FALSE){
            $this->session->set_flashdata('err', validation_errors());
            redirect('AnnualIncentives/create'); return;
        }

        $calc_mode = $this->input->post('calc_mode', true); // total/parts

        $data = [
            'batch_name' => $this->input->post('batch_name', true),
            'batch_year' => $this->input->post('batch_year', true),
            'budget_total' => $this->input->post('budget_total', true),
            'calc_mode' => $calc_mode,

            'use_base_salary' => ($calc_mode === 'parts') ? ($this->input->post('use_base_salary') ? '1':'0') : '0',
            'use_housing_allowance' => ($calc_mode === 'parts') ? ($this->input->post('use_housing_allowance') ? '1':'0') : '0',
            'use_transport_allowance' => ($calc_mode === 'parts') ? ($this->input->post('use_transport_allowance') ? '1':'0') : '0',
            'use_other_allowances' => ($calc_mode === 'parts') ? ($this->input->post('use_other_allowances') ? '1':'0') : '0',

            'status' => 'draft',
        ];

        $batch_id = $this->aim->create_batch($data);
        redirect('AnnualIncentives/employees/'.$batch_id);
    }

    public function employees($batch_id){
        $batch = $this->aim->get_batch($batch_id);
        if(!$batch){ show_error('دفعة غير موجودة', 404); return; }

        $data['title'] = 'اختيار الموظفين - '.$batch['batch_name'].' ('.$batch['batch_year'].')';
        $data['batch'] = $batch;
        $data['selected'] = $this->aim->get_batch_employees($batch_id);
       
        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('annual_incentives/employees', $data);
        $this->load->view('template/new_footer');
    }

    public function search_emp1(){
        // AJAX
        $term = $this->input->get('q', true);
        $rows = $this->aim->search_emp1($term);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok'=>true,'rows'=>$rows], JSON_UNESCAPED_UNICODE));
    }

    public function add_employee(){
        // AJAX
        $batch_id    = $this->input->post('batch_id', true);
        $employee_id = $this->input->post('employee_id', true);

        if(!$batch_id || !$employee_id){
            return $this->json_err('بيانات ناقصة');
        }

        if($this->aim->batch_has_employee($batch_id, $employee_id)){
            return $this->json_ok(['msg'=>'الموظف موجود مسبقاً']);
        }

        // جلب الموظف من emp1 بشكل مباشر
        $emp = $this->db->select('employee_id, id_number, subscriber_name, profession, total_salary, base_salary, housing_allowance, other_allowances, n4')

                        ->from('incentives10')
                        ->where('employee_id', $employee_id)
                        ->limit(1)
                        ->get()->row_array();

        if(!$emp){
            return $this->json_err('لم يتم العثور على الموظف في incentives10');
        }

        $this->aim->add_employee_to_batch($batch_id, $emp);
        $selected = $this->aim->get_batch_employees($batch_id);

        return $this->json_ok(['msg'=>'تمت الإضافة','selected'=>$selected]);
    }

    public function remove_employee(){
        // AJAX
        $row_id = (int)$this->input->post('row_id');
        if(!$row_id) return $this->json_err('row_id مفقود');

        $ok = $this->aim->remove_employee($row_id);
        return $this->json_ok(['removed'=>$ok]);
    }

      public function calc($batch_id)
{
    $batch = $this->aim->get_batch($batch_id);
    if (!$batch) { show_error('دفعة غير موجودة', 404); return; }

    $emps = $this->aim->get_batch_employees($batch_id);

    // ✅ السنة الافتراضية (سنة الدفعة) - قد لا تكون سنة التقييم الفعلية لكل الموظفين
    $preferred_year = (int)($batch['batch_year'] ?? date('Y'));

    // ✅ اجمع أرقام الموظفين (الربط عبر employee_id ↔ emp_no)
    $emp_nos = [];
    foreach ($emps as $e) {
        $no = trim((string)($e['employee_id'] ?? ''));
        if ($no !== '') $emp_nos[] = $no;
    }
    $emp_nos = array_values(array_unique($emp_nos));

    // ✅ خريطة سنة التقييم لكل موظف:
    // - لو preferred_year موجودة له يستخدمها
    // - وإلا يستخدم أحدث سنة موجودة في annual_eval_employees
    $emp_year_map = $this->aim->get_eval_years_map($emp_nos, $preferred_year);

    // ✅ خريطة نتائج التقييم (Self/Supervisor) حسب سنة كل موظف
    $eval_map = $this->aim->get_eval_totals_multi_year($emp_year_map);

    $calc_rows = [];
    foreach ($emps as $e) {

        $base = $this->aim->compute_calc_base_amount($batch, $e);

        $emp_no = trim((string)($e['employee_id'] ?? ''));

        // default لو ما لقينا شيء
        $ev = $eval_map[$emp_no] ?? [
            'eval_year'  => $preferred_year,
            'self_total' => null,
            'self_grade' => null,
            'sup_total'  => null,
            'sup_grade'  => null,
        ];

        // ✅ الفرق = (Supervisor - Self) إذا الاثنين موجودين
        $diff = null;
        if ($ev['self_total'] !== null && $ev['sup_total'] !== null) {
            $diff = (float)$ev['sup_total'] - (float)$ev['self_total'];
        }

        // ✅ لا نفرض صفر، نخلي آخر multiplier/incentive من قاعدة البيانات كما هو
        $calc_rows[] = array_merge($e, [
            '_calc_base_amount' => $base,

            // ✅ تمرير بيانات التقييم للفيو
            '_eval_year'  => (int)($ev['eval_year'] ?? $preferred_year),
            '_self_total' => $ev['self_total'],
            '_self_grade' => $ev['self_grade'],
            '_sup_total'  => $ev['sup_total'],
            '_sup_grade'  => $ev['sup_grade'],
            '_eval_diff'  => $diff,
        ]);
    }

    $data['title'] = 'احتساب الحوافز - ' . ($batch['batch_name'] ?? '') . ' (' . ($batch['batch_year'] ?? '') . ')';
    $data['batch'] = $batch;
    $data['rows']  = $calc_rows;

        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('annual_incentives/calc', $data);
        $this->load->view('template/new_footer');
}


    public function update_multiplier(){
        // AJAX: تحديث multiplier لموظف واحد مع منع تجاوز الميزانية
        $row_id   = (int)$this->input->post('row_id');
        $batch_id = $this->input->post('batch_id', true);
        $mult     = $this->input->post('multiplier', true);

        if(!$row_id || !$batch_id) return $this->json_err('بيانات ناقصة');

        $batch = $this->aim->get_batch($batch_id);
        if(!$batch) return $this->json_err('دفعة غير موجودة');

        $row = $this->db->get_where('annual_incentive_batch_employees', ['id'=>$row_id])->row_array();
        if(!$row) return $this->json_err('سجل الموظف غير موجود');

        // calc base amount
        $base = $this->aim->compute_calc_base_amount($batch, $row);

        // multiplier sanitize
        $mult = str_replace(',', '.', (string)$mult);
        $m = is_numeric($mult) ? (float)$mult : 0.0;
        if($m < 0) $m = 0;
        if($m > 7) $m = 7;

        $new_incentive = $base * $m;

        // budget check: total after update
        $budget_total = $this->to_float($batch['budget_total'] ?? 0);

        // total current excluding this row
        $all = $this->aim->get_batch_employees($batch_id);
        $sum_other = 0.0;
        foreach($all as $r){
            if((int)$r['id'] === (int)$row_id) continue;
            $sum_other += $this->to_float($r['incentive_amount'] ?? 0);
        }

        if(($sum_other + $new_incentive) > ($budget_total + 0.00001)){
            return $this->json_err('لا يمكن: الحافز يتجاوز الميزانية المتبقية');
        }

        $this->aim->update_employee_calc($row_id, (string)$base, number_format($m, 2, '.', ''), (string)$new_incentive);

        $sum = $this->aim->batch_totals($batch_id);
        $remaining = $budget_total - $sum;

        return $this->json_ok([
            'base' => $base,
            'incentive' => $new_incentive,
            'sum' => $sum,
            'remaining' => $remaining
        ]);
    }

    public function save_batch(){
        // إنهاء/حفظ نهائي (اختياري)
        $batch_id = (int)$this->input->post('batch_id');
        if(!$batch_id) redirect('AnnualIncentives'); 

        $this->aim->update_batch($batch_id, ['status' => 'final']);
        $this->session->set_flashdata('ok', 'تم حفظ الدفعة كنهائية.');
        redirect('AnnualIncentives/view/'.$batch_id);
    }

      public function view($batch_id)
{
    $batch = $this->aim->get_batch($batch_id);
    if(!$batch){ show_error('دفعة غير موجودة', 404); return; }

    $rows = $this->aim->get_batch_employees_sorted_by_incentive($batch_id);

    // ✅ حساب المصروف والمتبقي
    $budget = (string)($batch['budget_total'] ?? '0');
    $budget = str_replace([',',' '], '', $budget);
    $budget_f = is_numeric($budget) ? (float)$budget : 0.0;

    $spent = 0.0;
    foreach($rows as $r){
        $v = (string)($r['incentive_amount'] ?? '0');
        $v = str_replace([',',' '], '', $v);
        $spent += is_numeric($v) ? (float)$v : 0.0;
    }
    $remaining = $budget_f - $spent;

    $data['title'] = 'استعراض الدفعة';
    $data['batch'] = $batch;
    $data['rows']  = $rows;
    $data['budget_f'] = $budget_f;
    $data['spent_f'] = $spent;
    $data['remaining_f'] = $remaining;

    $this->load->view('template/new_header_and_sidebar', $data ?? []);
    $this->load->view('annual_incentives/view', $data);
    $this->load->view('template/new_footer');
}



    /* ================== local helpers ================== */
    private function json_ok($arr=[]){
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(array_merge(['ok'=>true], $arr), JSON_UNESCAPED_UNICODE));
    }
    private function json_err($msg){
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok'=>false,'msg'=>$msg], JSON_UNESCAPED_UNICODE));
    }
    private function to_float($v){
        $v = (string)$v;
        $v = str_replace([',',' '], '', $v);
        return is_numeric($v) ? (float)$v : 0.0;
    }

    public function edit_settings($batch_id){
    $batch = $this->aim->get_batch($batch_id);
    if(!$batch){ show_error('دفعة غير موجودة', 404); return; }

    $data['title'] = 'تعديل إعدادات الدفعة';
    $data['batch'] = $batch;

    $this->load->view('template/new_header_and_sidebar', $data ?? []);
    $this->load->view('annual_incentives/edit_settings', $data);
    $this->load->view('template/new_footer');
}

 public function update_settings()
{
    if($this->input->server('REQUEST_METHOD') !== 'POST'){
        redirect('AnnualIncentives'); return;
    }

    $batch_id  = (int)$this->input->post('batch_id');
    $batch = $this->aim->get_batch($batch_id);
    if(!$batch){ show_error('دفعة غير موجودة', 404); return; }

    $calc_mode = $this->input->post('calc_mode', true); // total / parts

    // ✅ منطق الفلاتر الصحيح:
    // - total => يلغي كل خيارات الأجزاء
    // - parts => يمكن اختيار واحد أو أكثر
    $new_settings = [
        'budget_total' => $this->input->post('budget_total', true),
        'calc_mode'    => $calc_mode,

        'use_base_salary'         => ($calc_mode === 'parts') ? ($this->input->post('use_base_salary') ? '1':'0') : '0',
        'use_housing_allowance'   => ($calc_mode === 'parts') ? ($this->input->post('use_housing_allowance') ? '1':'0') : '0',
        'use_transport_allowance' => ($calc_mode === 'parts') ? ($this->input->post('use_transport_allowance') ? '1':'0') : '0',
        'use_other_allowances'    => ($calc_mode === 'parts') ? ($this->input->post('use_other_allowances') ? '1':'0') : '0',
    ];

    // ✅ معاينة المصروف بعد التعديل (مع الحفاظ على multipliers)
    $preview = $this->aim->preview_totals_after_settings_change($batch_id, $new_settings);
    if(!$preview['ok']){
        $this->session->set_flashdata('err', 'حدث خطأ أثناء التحقق من المصروف.');
        redirect('AnnualIncentives/edit_settings/'.$batch_id); return;
    }

    if($preview['spent'] > $preview['budget'] + 0.00001){
        // ❌ امنع الحفظ
        $msg = 'لا يمكن تعديل الفلاتر/الميزانية لأن المصروف بعد التعديل سيكون أعلى من الميزانية.'
             . '<br>الميزانية: <b>'.number_format($preview['budget'],2,'.',',').'</b>'
             . ' — المصروف المتوقع: <b>'.number_format($preview['spent'],2,'.',',').'</b>'
             . ' — المتبقي: <b style="color:#b00020;">'.number_format($preview['remaining'],2,'.',',').'</b>'
             . '<br>الحل: زد الميزانية أو قلل تزايد بعض الموظفين ثم أعد المحاولة.';
        $this->session->set_flashdata('err', $msg);
        redirect('AnnualIncentives/edit_settings/'.$batch_id); return;
    }

    // ✅ احفظ الإعدادات
    $this->aim->update_batch($batch_id, array_merge($new_settings, [
        'status' => 'draft' // اختياري: إعادة مسودة بعد تغيير جوهري
    ]));

    // ✅ إعادة احتساب القاعدة والحافز مع الحفاظ على multiplier الحالي
    $this->aim->recompute_all_employees_base_and_amounts_keep_multiplier($batch_id);

    $this->session->set_flashdata('ok', 'تم تحديث الإعدادات وإعادة الاحتساب بنجاح.');
    redirect('AnnualIncentives/view/'.$batch_id);
}


 public function export_excel($batch_id)
{
    $batch_id = (int)$batch_id;

    $batch = $this->aim->get_batch($batch_id);
    if (!$batch) {
        show_error('دفعة غير موجودة', 404);
        return;
    }

    // ✅ مرتبة من الأعلى حافز إلى الأقل
    $rows = $this->aim->get_batch_employees_sorted_by_incentive($batch_id);

    // اسم ملف لطيف
    $safe_name = preg_replace('/[^\p{L}\p{N}\s\-_]+/u', '', (string)($batch['batch_name'] ?? 'batch'));
    $safe_name = trim($safe_name);
    if ($safe_name === '') $safe_name = 'annual_incentives';

    $filename = $safe_name . '_' . ($batch['batch_year'] ?? '') . '_' . date('Ymd_His') . '.csv';

    // Headers
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // BOM ليدعم العربية في Excel
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');
    if ($out === false) {
        show_error('لا يمكن إنشاء ملف التصدير', 500);
        return;
    }

    // رأس الملف
    fputcsv($out, [
        'اسم الحافز',
        'السنة',
        'الميزانية',

        'اسم الموظف',
        'المسمى الوظيفي',
        'الرقم الوظيفي',
        'رقم الهوية',

        'إجمالي الراتب',
        'الراتب الأساسي',
        'بدل السكن',
        'بدل المواصلات',
        'بدلات أخرى',

        'قاعدة الحساب',
        'التزايد (رواتب)',
        'قيمة الحافز'
    ]);

    // البيانات
    foreach ($rows as $r) {
        $emp_name = (string)($r['subscriber_name'] ?? '');
        $profession = (string)($r['profession'] ?? '');

        // اسم مع (مسمى) لو تحب (اختياري) — خليناه عمود منفصل أيضاً
        // if ($profession !== '') $emp_name .= ' (' . $profession . ')';

        fputcsv($out, [
            (string)($batch['batch_name'] ?? ''),
            (string)($batch['batch_year'] ?? ''),
            (string)($batch['budget_total'] ?? ''),

            $emp_name,
            $profession,
            (string)($r['employee_id'] ?? ''),
            (string)($r['id_number'] ?? ''),

            (string)($r['total_salary'] ?? ''),
            (string)($r['base_salary'] ?? ''),
            (string)($r['housing_allowance'] ?? ''),
            (string)($r['transport_allowance'] ?? ''),
            (string)($r['other_allowances'] ?? ''),

            (string)($r['calc_base_amount'] ?? ''),
            (string)($r['multiplier'] ?? ''),
            (string)($r['incentive_amount'] ?? '')
        ]);
    }

    fclose($out);
    exit;
}


public function print_view($batch_id){
    $batch = $this->aim->get_batch($batch_id);
    if(!$batch){ show_error('دفعة غير موجودة', 404); return; }

    $rows = $this->aim->get_batch_employees_sorted_by_incentive($batch_id);

    $data['title'] = 'طباعة الدفعة';
    $data['batch'] = $batch;
    $data['rows']  = $rows;

    $this->load->view('template/new_header_and_sidebar', $data ?? []);
    $this->load->view('annual_incentives/print', $data);
    $this->load->view('template/new_footer');
}

public function reset_calculations()
{
    // AJAX فقط
    $batch_id = (int)$this->input->post('batch_id');
    if(!$batch_id){
        return $this->json_err('batch_id مفقود');
    }

    $batch = $this->aim->get_batch($batch_id);
    if(!$batch){
        return $this->json_err('الدفعة غير موجودة');
    }

    $ok = $this->aim->reset_batch_calculations($batch_id);
    if(!$ok){
        return $this->json_err('تعذر تصفير الاحتساب');
    }

    return $this->json_ok(['msg'=>'تم حذف كل الاحتساب (تصفير التزايد والحوافز) بنجاح.']);
}








}
