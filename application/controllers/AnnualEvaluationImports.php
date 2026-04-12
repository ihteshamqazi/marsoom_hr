<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnnualEvaluationImports extends CI_Controller
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

        $data = [
            'title' => 'استيراد ملفات CSV - التقييم السنوي',
            'year'  => $year,
            'flash' => $this->session->flashdata('msg')
        ];
        $this->load->view('annual_eval/imports_csv', $data);
    }

    /* ========= أدوات CSV ========= */

    private function read_csv_assoc($tmp_path)
    {
        $fh = fopen($tmp_path, 'r');
        if (!$fh) return [[], 'تعذر فتح الملف'];

        $header = fgetcsv($fh);
        if (!$header) { fclose($fh); return [[], 'الملف فارغ']; }

        // تنظيف الهيدر
        $header = array_map(function($h){
            $h = trim((string)$h);
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h); // إزالة BOM
            return $h;
        }, $header);

        $rows = [];
        while (($line = fgetcsv($fh)) !== false) {
            if (count($line) === 1 && trim((string)$line[0]) === '') continue;

            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = isset($line[$i]) ? trim((string)$line[$i]) : '';
            }
            $rows[] = $row;
        }
        fclose($fh);
        return [$rows, null];
    }

    private function ensure_upload()
    {
        if (empty($_FILES['csv_file']['tmp_name'])) {
            return [false, 'لم يتم اختيار ملف CSV'];
        }
        $name = (string)($_FILES['csv_file']['name'] ?? '');
        if (!preg_match('/\.csv$/i', $name)) {
            return [false, 'الملف يجب أن يكون بصيغة CSV فقط'];
        }
        return [true, null];
    }

    /* ========= Master ========= */

    public function upload_master()
    {
        $year = (int)$this->input->post('year', true);
        if ($year <= 0) $year = (int)date('Y');

        [$ok, $err] = $this->ensure_upload();
        if (!$ok) { $this->session->set_flashdata('msg', $err); redirect('AnnualEvaluationImports?year='.$year); }

        [$rows, $e] = $this->read_csv_assoc($_FILES['csv_file']['tmp_name']);
        if ($e) { $this->session->set_flashdata('msg', $e); redirect('AnnualEvaluationImports?year='.$year); }

        // تحقق من الأعمدة الأساسية
        $required = ['emp_no','emp_name','department','job_title','hire_date','supervisor_emp_no','supervisor_name','form_type','role_type'];
        $missing = [];
        if (!empty($rows)) {
            foreach ($required as $r) {
                if (!array_key_exists($r, $rows[0])) $missing[] = $r;
            }
        }
        if ($missing) {
            $this->session->set_flashdata('msg', 'الأعمدة ناقصة في master.csv: '.implode(', ', $missing));
            redirect('AnnualEvaluationImports?year='.$year);
        }

        $count = 0;
        foreach ($rows as $r) {
            if (trim((string)$r['emp_no']) === '') continue;
            $this->ev->upsert_employee_master($year, $r);
            $count++;
        }

        $this->session->set_flashdata('msg', "تم استيراد Master بنجاح. عدد الصفوف: {$count}");
        redirect('AnnualEvaluationImports?year='.$year);
    }

    /* ========= Discipline ========= */

    public function upload_discipline()
    {
        $year = (int)$this->input->post('year', true);
        if ($year <= 0) $year = (int)date('Y');

        [$ok, $err] = $this->ensure_upload();
        if (!$ok) { $this->session->set_flashdata('msg', $err); redirect('AnnualEvaluationImports?year='.$year); }

        [$rows, $e] = $this->read_csv_assoc($_FILES['csv_file']['tmp_name']);
        if ($e) { $this->session->set_flashdata('msg', $e); redirect('AnnualEvaluationImports?year='.$year); }

        $required = ['emp_no','score'];
        $missing = [];
        if (!empty($rows)) {
            foreach ($required as $r) {
                if (!array_key_exists($r, $rows[0])) $missing[] = $r;
            }
        }
        if ($missing) {
            $this->session->set_flashdata('msg', 'الأعمدة ناقصة في discipline.csv: '.implode(', ', $missing));
            redirect('AnnualEvaluationImports?year='.$year);
        }

        $count = 0;
        foreach ($rows as $r) {
            $emp_no = trim((string)$r['emp_no']);
            if ($emp_no === '') continue;

            $emp_name = trim((string)($r['emp_name'] ?? ''));
            $score = (float)($r['score'] ?? 0);

            $this->ev->upsert_discipline($year, $emp_no, $emp_name, $score);
            $count++;
        }

        $this->session->set_flashdata('msg', "تم استيراد الانضباط بنجاح. عدد الصفوف: {$count}");
        redirect('AnnualEvaluationImports?year='.$year);
    }

    /* ========= Courses ========= */

    public function upload_courses()
    {
        $year = (int)$this->input->post('year', true);
        if ($year <= 0) $year = (int)date('Y');

        [$ok, $err] = $this->ensure_upload();
        if (!$ok) { $this->session->set_flashdata('msg', $err); redirect('AnnualEvaluationImports?year='.$year); }

        [$rows, $e] = $this->read_csv_assoc($_FILES['csv_file']['tmp_name']);
        if ($e) { $this->session->set_flashdata('msg', $e); redirect('AnnualEvaluationImports?year='.$year); }

        $required = ['emp_no','base_score'];
        $missing = [];
        if (!empty($rows)) {
            foreach ($required as $r) {
                if (!array_key_exists($r, $rows[0])) $missing[] = $r;
            }
        }
        if ($missing) {
            $this->session->set_flashdata('msg', 'الأعمدة ناقصة في courses.csv: '.implode(', ', $missing));
            redirect('AnnualEvaluationImports?year='.$year);
        }

        $count = 0;
        foreach ($rows as $r) {
            $emp_no = trim((string)$r['emp_no']);
            if ($emp_no === '') continue;

            $emp_name = trim((string)($r['emp_name'] ?? ''));
            $base = (float)($r['base_score'] ?? 0);

            $this->ev->upsert_courses($year, $emp_no, $emp_name, $base);
            $count++;
        }

        $this->session->set_flashdata('msg', "تم استيراد الدورات بنجاح. عدد الصفوف: {$count}");
        redirect('AnnualEvaluationImports?year='.$year);
    }
}
