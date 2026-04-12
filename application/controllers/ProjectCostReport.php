<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectCostReport extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('users/login'); return; }

        $this->load->model('Project_cost_report_model', 'cost');
        $this->load->helper(['url', 'form']);
        date_default_timezone_set('Asia/Riyadh');
    }

    /** ملخص المشاريع */
    public function index()
    {
        $month = $this->input->get('month'); // yyyy/mm اختياري
        if (!$month) $month = date('Y/m');

        $data = [];
        $data['title'] = 'تقرير تكلفة المشاريع (شهري)';
        $data['month'] = $month;

        $data['projects'] = $this->cost->get_projects_summary($month);

        $this->load->view('project_cost_summary_view', $data);
    }

    /** تفاصيل مشروع */
    public function project($project_id = null)
    {
        if (!$project_id) { show_error('لم يتم تمرير رقم المشروع.', 400); return; }

        $month = $this->input->get('month');
        if (!$month) $month = date('Y/m');

        $data = [];
        $data['title']      = 'تفاصيل تكلفة المشروع';
        $data['month']      = $month;
        $data['project_id'] = (int)$project_id;

        $data['project']    = $this->cost->get_project_header($project_id);
        $data['rows']       = $this->cost->get_project_employees_cost($project_id, $month);
        $data['totals']     = $this->cost->calc_totals($data['rows'], $data['project']['commission_rate'] ?? 0);

        $this->load->view('project_cost_project_view', $data);
    }

    /** تصدير CSV للملخص */
    public function export_summary_csv()
    {
        $month = $this->input->get('month');
        if (!$month) $month = date('Y/m');

        $rows = $this->cost->get_projects_summary($month);

        $filename = "projects_cost_summary_{$month}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        echo "\xEF\xBB\xBF"; // BOM for Excel Arabic
        $out = fopen('php://output', 'w');

        fputcsv($out, ['رقم المشروع','اسم المشروع','عدد الأشخاص','إجمالي الرواتب','إجمالي الإنتاجية','نسبة العمولة','عمولة الشركة المتوقعة','إجمالي التكلفة التشغيلية','هامش تقريبي']);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['project_id'],
                $r['project_name'],
                $r['people_count'],
                $r['total_salary'],
                $r['total_productivity'],
                $r['commission_rate'],
                $r['company_commission'],
                $r['total_operating_cost'],
                $r['approx_margin'],
            ]);
        }
        fclose($out);
        exit;
    }

    /** تصدير CSV لتفاصيل مشروع */
    public function export_project_csv($project_id = null)
    {
        if (!$project_id) { show_error('لم يتم تمرير رقم المشروع.', 400); return; }

        $month = $this->input->get('month');
        if (!$month) $month = date('Y/m');

        $project = $this->cost->get_project_header($project_id);
        $rows    = $this->cost->get_project_employees_cost($project_id, $month);

        $filename = "project_cost_{$project_id}_{$month}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');

        fputcsv($out, ['رقم المشروع','اسم المشروع','الشهر','الرقم الوظيفي','اسم الموظف','النوع','اسم المشرف','اسم المدير','الراتب','إنتاجية الشهر','نسبة العمولة','عمولة الشركة المتوقعة','التكلفة التشغيلية','هامش تقريبي']);

        $rate = (float)($project['commission_rate'] ?? 0);

        foreach ($rows as $r) {
            $company_comm = round(((float)$r['productivity'] * $rate / 100), 2);
            $margin = round(($company_comm - (float)$r['salary'] - (float)$r['operating_cost']), 2);

            fputcsv($out, [
                $project['project_id'],
                $project['project_name'],
                $month,
                $r['emp_no'],
                $r['name'],
                $r['type_label'],
                $r['supervisor_name'],
                $r['manager_name'],
                $r['salary'],
                $r['productivity'],
                $rate,
                $company_comm,
                $r['operating_cost'],
                $margin,
            ]);
        }

        fclose($out);
        exit;
    }
}
