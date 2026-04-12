<?php defined('BASEPATH') OR exit('No direct script access allowed');

class LeaderSalariesReport extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // if (!$this->session->userdata('logged_in')) { redirect('users/login'); return; }

        $this->load->helper(['url', 'form', 'download']);
        $this->load->library(['session']);
        $this->load->model('Leader_salaries_report_model', 'leaders');

        date_default_timezone_set('Asia/Riyadh');
    }

    public function index()
    {
        $filters = $this->_read_filters();

        // فلتر اختيار قيادي مباشرة
        $manager_id = trim((string)$this->input->get('manager_id', true));
        if ($manager_id !== '') {
            redirect('LeaderSalariesReport/details/' . rawurlencode($manager_id));
            return;
        }

        $data = [];
        $data['title'] = 'تقرير رواتب القياديين';
        $data['filters'] = $filters;

        $data['managers'] = $this->leaders->get_managers_list();
        $data['groups']   = $this->leaders->get_grouped_leaders_report($filters);
        $data['professions'] = $this->leaders->get_professions_list();

        $this->load->view('leader_salaries_report_view', $data);
    }

    /**
     * شاشة تفاصيل القيادي: تعرض التابعين n3 فقط
     * وبزر لكل تابع يفتح شاشة منفصلة لتابعيه (n4)
     */
    public function details($manager_id = null)
    {
        if (!$manager_id) {
            show_error('لم يتم تمرير رقم القيادي (الرقم الوظيفي).', 400);
            return;
        }

        $filters = $this->_read_filters();

        $data = [];
        $data['title'] = 'تفاصيل القيادي - التابعين (n3)';
        $data['filters'] = $filters;
        $data['manager_id'] = $manager_id;

        $data['manager_info'] = $this->leaders->get_employee_basic($manager_id);

        $data['has_n3']    = $this->leaders->has_subordinates_n3($manager_id);
        $data['groups_n3'] = $this->leaders->get_grouped_subordinates_n3_report($manager_id, $filters);

        $this->load->view('leader_salaries_details_view', $data);
    }

    /**
     * ✅ شاشة منفصلة: تفاصيل تابع (n3) وتعرض تابعيه (n4)
     * ترتيب العرض: غير محصل ديون أولاً ثم محصل ديون
     */
    public function sub_details($n3_id = null)
    {
        if (!$n3_id) {
            show_error('لم يتم تمرير رقم التابع (n3).', 400);
            return;
        }

        $filters = $this->_read_filters();

        $data = [];
        $data['title'] = 'تفاصيل التابع - التابعين للتابع (n4)';
        $data['filters'] = $filters;
        $data['n3_id']   = $n3_id;

        $data['n3_info'] = $this->leaders->get_employee_basic($n3_id);

        $data['has_n4'] = $this->leaders->has_followers_n4_by_n3($n3_id);

        // ✅ مطلوب: غير محصل ديون أولاً ثم محصل ديون
        $data['n4_others']     = $this->leaders->get_grouped_n4_by_n3_others_report($n3_id, $filters);
        $data['n4_collectors'] = $this->leaders->get_grouped_n4_by_n3_collectors_report($n3_id, $filters);

        $this->load->view('leader_salaries_subdetails_view', $data);
    }

    /* =============================
       Export CSV (Excel)
       ============================= */

    // تصدير n3 للقيادي
    public function export_n3($manager_id = null)
    {
        if (!$manager_id) { show_error('manager_id required', 400); return; }
        $filters = $this->_read_filters();
        $rows = $this->leaders->export_subordinates_n3_rows($manager_id, $filters);
        $this->_force_csv_download("N3_Subordinates_{$manager_id}.csv", $rows);
    }

    // ✅ تصدير n4 (غير محصل) للتابع n3
    public function export_n4_others_by_n3($n3_id = null)
    {
        if (!$n3_id) { show_error('n3_id required', 400); return; }
        $filters = $this->_read_filters();
        $rows = $this->leaders->export_n4_by_n3_others_rows($n3_id, $filters);
        $this->_force_csv_download("N4_Others_{$n3_id}.csv", $rows);
    }

    // ✅ تصدير n4 (محصل ديون) للتابع n3
    public function export_n4_collectors_by_n3($n3_id = null)
    {
        if (!$n3_id) { show_error('n3_id required', 400); return; }
        $filters = $this->_read_filters();
        $rows = $this->leaders->export_n4_by_n3_collectors_rows($n3_id, $filters);
        $this->_force_csv_download("N4_Collectors_{$n3_id}.csv", $rows);
    }

    /* =============================
       Helpers
       ============================= */

    private function _read_filters()
    {
        $q          = trim((string)$this->input->get('q', true));
        $profession = trim((string)$this->input->get('profession', true));
        $min_salary = trim((string)$this->input->get('min_salary', true));
        $max_salary = trim((string)$this->input->get('max_salary', true));
        $sort = trim((string)$this->input->get('sort', true));
        if ($sort === '') $sort = 'salary_desc';

        return [
            'q'          => $q,
            'profession' => $profession,
            'sort'       => $sort,
            'min_salary' => $min_salary,
            'max_salary' => $max_salary,
        ];
    }

    private function _force_csv_download($filename, array $rows)
    {
        $csv = "\xEF\xBB\xBF"; // BOM UTF-8
        $headers = ['Employee ID','Employee Name','Profession','Total Salary','n13'];
        $csv .= implode(',', $headers) . "\n";

        foreach ($rows as $r) {
            $line = [
                $this->_csv_escape($r['employee_id'] ?? ''),
                $this->_csv_escape($r['subscriber_name'] ?? ''),
                $this->_csv_escape($r['profession'] ?? ''),
                $this->_csv_escape(number_format((float)($r['total_salary_num'] ?? 0), 2, '.', '')),
                $this->_csv_escape((string)($r['n13'] ?? '')),
            ];
            $csv .= implode(',', $line) . "\n";
        }

        force_download($filename, $csv);
    }

    private function _csv_escape($value)
    {
        $value = (string)$value;
        $value = str_replace('"', '""', $value);
        return '"' . $value . '"';
    }
}
