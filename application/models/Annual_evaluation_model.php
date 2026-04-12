<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Annual_evaluation_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /* =========================
     * Helpers
     * ========================= */

     public function is_admin()
{
    $u = (string)$this->session->userdata('username'); // الرقم الوظيفي
    if (in_array($u, ['1835','2230','1001'], true)) return true;

    // احتياط لو عندك نظام صلاحيات قديم
    return (int)$this->session->userdata('is_admin') === 1;
}


    public function get_grade_label($total)
    {
        $t = (float)$total;
        if ($t < 60) return 'ضعيف';
        if ($t < 75) return 'جيد';
        if ($t < 85) return 'جيد جدًا';
        if ($t < 95) return 'ممتاز';
        if ($t < 105) return 'ممتاز جدًا';
        return 'استثنائي';
    }

    private function clamp($v, $min, $max)
    {
        $v = (float)$v;
        if ($v < $min) $v = $min;
        if ($v > $max) $v = $max;
        return $v;
    }

    /* =========================
     * Base data (employee master)
     * ========================= */

    public function get_employee_row($year, $emp_no)
    {
        return $this->db->get_where('annual_eval_employees', [
            'eval_year' => (int)$year,
            'emp_no'    => (string)$emp_no,
            'is_active' => 1
        ])->row_array();
    }

    public function get_my_team($year, $supervisor_emp_no)
    {
        return $this->db
            ->order_by('emp_name', 'ASC')
            ->get_where('annual_eval_employees', [
                'eval_year' => (int)$year,
                'supervisor_emp_no' => (string)$supervisor_emp_no,
                'is_active' => 1
            ])->result_array();
    }

    /* =========================
     * Imports base scores
     * ========================= */

    public function upsert_discipline($year, $emp_no, $emp_name, $score)
    {
        $year = (int)$year;
        $emp_no = (string)$emp_no;
        $score = $this->clamp($score, 0, 20);

        $exists = $this->db->get_where('annual_eval_discipline', [
            'eval_year'=>$year, 'emp_no'=>$emp_no
        ])->row_array();

        $data = [
            'eval_year'=>$year,
            'emp_no'=>$emp_no,
            'emp_name'=>$emp_name,
            'score'=>$score
        ];

        if ($exists) {
            $this->db->where('id', $exists['id'])->update('annual_eval_discipline', $data);
        } else {
            $this->db->insert('annual_eval_discipline', $data);
        }
    }

    public function upsert_courses($year, $emp_no, $emp_name, $base_score)
    {
        $year = (int)$year;
        $emp_no = (string)$emp_no;
        $base_score = $this->clamp($base_score, 0, 20);

        $exists = $this->db->get_where('annual_eval_courses', [
            'eval_year'=>$year, 'emp_no'=>$emp_no
        ])->row_array();

        $data = [
            'eval_year'=>$year,
            'emp_no'=>$emp_no,
            'emp_name'=>$emp_name,
            'base_score'=>$base_score
        ];

        if ($exists) {
            $this->db->where('id', $exists['id'])->update('annual_eval_courses', $data);
        } else {
            $this->db->insert('annual_eval_courses', $data);
        }
    }

    public function get_discipline_score($year, $emp_no)
    {
        $row = $this->db->get_where('annual_eval_discipline', [
            'eval_year'=>(int)$year,
            'emp_no'=>(string)$emp_no
        ])->row_array();
        return $row ? (float)$row['score'] : 0.0;
    }

    public function get_courses_base_score($year, $emp_no)
    {
        $row = $this->db->get_where('annual_eval_courses', [
            'eval_year'=>(int)$year,
            'emp_no'=>(string)$emp_no
        ])->row_array();
        return $row ? (float)$row['base_score'] : 0.0;
    }

    /* =========================
     * Master employees import (Excel)
     * ========================= */

    public function upsert_employee_master($year, $row)
{
    $year = (int)$year;

    $emp_no = trim((string)($row['emp_no'] ?? ''));
    if ($emp_no === '') return;

    $data = [
        'eval_year'          => $year,
        'emp_no'             => $emp_no,
        'emp_name'           => trim((string)($row['emp_name'] ?? '')),
        'department'         => trim((string)($row['department'] ?? '')),
        'job_title'          => trim((string)($row['job_title'] ?? '')),
        'hire_date'          => trim((string)($row['hire_date'] ?? '')),

        'supervisor_emp_no'  => trim((string)($row['supervisor_emp_no'] ?? '')),
        'supervisor_name'    => trim((string)($row['supervisor_name'] ?? '')),
        'form_type'          => (int)($row['form_type'] ?? 1),
        'role_type'          => in_array(($row['role_type'] ?? 'employee'), ['employee','supervisor'], true)
                                  ? $row['role_type'] : 'employee',
        'is_active'          => 1
    ];

    $exists = $this->db->get_where('annual_eval_employees', [
        'eval_year'=>$year, 'emp_no'=>$emp_no
    ])->row_array();

    if ($exists) {
        $this->db->where('id', $exists['id'])->update('annual_eval_employees', $data);
    } else {
        $this->db->insert('annual_eval_employees', $data);
    }
}


    /* =========================
     * Save Self evaluation
     * ========================= */

     public function save_self($year, $emp_no, $payload)
{
    $year   = (int)$year;
    $emp_no = (string)$emp_no;

    $emp = $this->get_employee_row($year, $emp_no);
    if (!$emp) {
        return ['ok'=>false, 'msg'=>'الموظف غير موجود ضمن قائمة التقييم لهذا العام.'];
    }

    // منع إعادة التقييم
    $exists = $this->db->get_where('annual_eval_self', [
        'eval_year' => $year,
        'emp_no'    => $emp_no
    ])->row_array();

    if ($exists) {
        return [
            'ok' => false,
            'msg' => 'شكراً لك، تم التقييم مسبقاً ولا يمكن التقييم مرة أخرى.',
            'already_submitted' => true
        ];
    }

    $form_type  = (int)$emp['form_type'];
    $discipline = $this->get_discipline_score($year, $emp_no);

    $data = [
        'eval_year'   => $year,
        'emp_no'      => $emp_no,
        'form_type'   => $form_type,
        'notes'       => trim((string)($payload['notes'] ?? '')),
        'updated_at'  => date('Y-m-d H:i:s'),
        'submitted_at'=> date('Y-m-d H:i:s'),
    ];

    $calc = $this->build_scores_from_dynamic_criteria($year, $emp_no, $form_type, $payload, $discipline);
    $data = array_merge($data, $calc);

    $this->db->insert('annual_eval_self', $data);

    return ['ok'=>true, 'msg'=>'تم حفظ تقييمك بنجاح.'];
}

    /* =========================
     * Save Supervisor evaluation
     * ========================= */

      public function save_supervisor($year, $supervisor_emp_no, $emp_no, $payload)
{
    $year = (int)$year;
    $supervisor_emp_no = (string)$supervisor_emp_no;
    $emp_no = (string)$emp_no;

    $emp = $this->get_employee_row($year, $emp_no);
    if (!$emp) {
        return ['ok'=>false, 'msg'=>'الموظف غير موجود ضمن قائمة التقييم.'];
    }

    if ((string)$emp['supervisor_emp_no'] !== $supervisor_emp_no && !$this->is_admin()) {
        return ['ok'=>false, 'msg'=>'غير مصرح لك بتقييم هذا الموظف.'];
    }

    // منع إعادة التقييم
    $exists = $this->db->get_where('annual_eval_supervisor', [
        'eval_year'         => $year,
        'emp_no'            => $emp_no,
        'supervisor_emp_no' => $supervisor_emp_no
    ])->row_array();

    if ($exists) {
        return [
            'ok' => false,
            'msg' => 'شكراً لك، تم تقييم الموظف مسبقاً ولا يمكن التقييم مرة أخرى.',
            'already_submitted' => true
        ];
    }

    $form_type  = (int)$emp['form_type'];
    $discipline = $this->get_discipline_score($year, $emp_no);

    $data = [
        'eval_year'          => $year,
        'emp_no'             => $emp_no,
        'supervisor_emp_no'  => $supervisor_emp_no,
        'form_type'          => $form_type,
        'notes'              => trim((string)($payload['notes'] ?? '')),
        'updated_at'         => date('Y-m-d H:i:s'),
        'submitted_at'       => date('Y-m-d H:i:s'),
    ];

    $calc = $this->build_scores_from_dynamic_criteria($year, $emp_no, $form_type, $payload, $discipline);
    $data = array_merge($data, $calc);

    $this->db->insert('annual_eval_supervisor', $data);

    return ['ok'=>true, 'msg'=>'تم حفظ تقييم المشرف بنجاح.'];
}

    /* =========================
     * Reads for forms and admin view
     * ========================= */

    public function get_self_eval($year, $emp_no)
    {
        return $this->db->get_where('annual_eval_self', [
            'eval_year'=>(int)$year,
            'emp_no'=>(string)$emp_no
        ])->row_array();
    }

   public function get_supervisor_eval($year, $emp_no, $supervisor_emp_no = '')
{
    $this->db->from('annual_eval_supervisor');
    $this->db->where('eval_year', (int)$year);
    $this->db->where('emp_no', (string)$emp_no);

    if ($supervisor_emp_no !== '') {
        $this->db->where('supervisor_emp_no', (string)$supervisor_emp_no);
    }

    $this->db->order_by('updated_at', 'DESC');
    $this->db->order_by('id', 'DESC');
    return $this->db->get()->row_array();
}

    public function admin_list($year, $dept = '')
    {
        $this->db->select("e.*, 
            s.total_score AS self_total, s.grade_label AS self_grade, s.submitted_at AS self_submitted,
            sp.total_score AS sup_total, sp.grade_label AS sup_grade, sp.submitted_at AS sup_submitted
        ");
        $this->db->from('annual_eval_employees e');
        $this->db->join('annual_eval_self s', 's.eval_year=e.eval_year AND s.emp_no=e.emp_no', 'left');
        $this->db->join('annual_eval_supervisor sp', 'sp.eval_year=e.eval_year AND sp.emp_no=e.emp_no', 'left');
        $this->db->where('e.eval_year', (int)$year);
        $this->db->where('e.is_active', 1);

        if ($dept !== '') $this->db->where('e.department', $dept);

        $this->db->order_by('e.department','ASC')->order_by('e.emp_name','ASC');
        return $this->db->get()->result_array();
    }

    public function admin_detail($year, $emp_no)
    {
        $emp = $this->get_employee_row($year, $emp_no);
        if (!$emp) return null;

        $self = $this->get_self_eval($year, $emp_no);
        $sup  = $this->get_supervisor_eval($year, $emp_no);

        return [
            'emp'=>$emp,
            'self'=>$self,
            'sup'=>$sup,
            'discipline'=>$this->get_discipline_score($year, $emp_no),
            'courses_base'=>$this->get_courses_base_score($year, $emp_no)
        ];
    }

    public function diff_row($self, $sup, $key)
    {
        $a = $self ? (float)($self[$key] ?? 0) : 0.0;
        $b = $sup  ? (float)($sup[$key]  ?? 0) : 0.0;
        return $b - $a; // فرق المشرف - الموظف
    }

    public function decode_reasons($row)
{
    if (!is_array($row)) return [];
    $raw = $row['reasons_json'] ?? '';
    if ($raw === '' || $raw === null) return [];
    $tmp = json_decode($raw, true);
    return is_array($tmp) ? $tmp : [];
}

public function decode_breakdown($row)
{
    if (!is_array($row)) return [];
    $raw = $row['breakdown_json'] ?? '';
    if ($raw === '' || $raw === null) return [];
    $tmp = json_decode($raw, true);
    return is_array($tmp) ? $tmp : [];
}


public function get_form_criteria($form_type)
{
    $criteria = $this->db
        ->order_by('sort_order', 'ASC')
        ->get_where('annual_eval_criteria', [
            'form_type' => (int)$form_type,
            'is_active' => 1
        ])->result_array();

    if (empty($criteria)) return [];

    $ids = array_column($criteria, 'id');
    $partsRows = [];
    if (!empty($ids)) {
        $partsRows = $this->db
            ->where_in('criterion_id', $ids)
            ->where('is_active', 1)
            ->order_by('sort_order', 'ASC')
            ->get('annual_eval_criteria_parts')
            ->result_array();
    }

    $partsMap = [];
    foreach ($partsRows as $p) {
        $partsMap[$p['criterion_id']][] = [
    'k'         => $p['part_key'],
    'name'      => $p['part_name'],
    'max'       => (float)$p['max_score'],
    'note_rule' => (string)($p['note_rule'] ?? 'required_on_less'),
];
    }

    $out = [];
    foreach ($criteria as $c) {
        $out[] = [
            'id'               => (int)$c['id'],
            'key'              => $c['criterion_key'],
            'score_column'     => $c['score_column'],
            'name'             => $c['criterion_name'],
            'max'              => (float)$c['max_score'],
            'readonly'         => (bool)$c['is_readonly'],
            'min_from_courses' => (bool)$c['min_from_courses'],
            'desc'             => (string)$c['criterion_desc'],
            'parts'            => $partsMap[$c['id']] ?? [],
        ];
    }

    return $out;
}


private function build_scores_from_dynamic_criteria($year, $emp_no, $form_type, $payload, $discipline_score = 0)
{
    $criteria = $this->get_form_criteria($form_type);

    $breakdown = $payload['breakdown'] ?? [];
    $reasons   = $payload['reasons'] ?? [];

    if (!is_array($breakdown)) $breakdown = [];
    if (!is_array($reasons))   $reasons   = [];

    $data = [
        'discipline_score' => (float)$discipline_score,
        'reasons_json'     => json_encode($reasons, JSON_UNESCAPED_UNICODE),
        'breakdown_json'   => json_encode($breakdown, JSON_UNESCAPED_UNICODE),
    ];

    $total = 0.0;

    foreach ($criteria as $c) {
        $key         = (string)$c['key'];
        $scoreColumn = (string)$c['score_column'];
        $max         = (float)$c['max'];
        $readonly    = !empty($c['readonly']);
        $parts       = $c['parts'] ?? [];

        if ($readonly) {
            $score = (float)$discipline_score;
        } else {
            $score = 0.0;
            $vals = $breakdown[$key] ?? [];
            if (!is_array($vals)) $vals = [];

            foreach ($parts as $p) {
                $pk   = (string)$p['k'];
                $pmax = (float)$p['max'];
                $v    = isset($vals[$pk]) ? (float)$vals[$pk] : 0;
                $score += $this->clamp($v, 0, $pmax);
            }

            if (!empty($c['min_from_courses'])) {
                $base = $this->get_courses_base_score($year, $emp_no);
                $score = $this->clamp(max($score, $base), $base, $max);
            } else {
                $score = $this->clamp($score, 0, $max);
            }
        }

        $data[$scoreColumn] = $score;
        $total += $score;
    }

    $data['total_score'] = $total;
    $data['grade_label'] = $this->get_grade_label($total);

    return $data;
}

public function has_submitted_self($year, $emp_no)
{
    $row = $this->db->get_where('annual_eval_self', [
        'eval_year' => (int)$year,
        'emp_no'    => (string)$emp_no
    ])->row_array();

    return !empty($row);
}

public function has_submitted_supervisor($year, $emp_no, $supervisor_emp_no = '')
{
    $this->db->from('annual_eval_supervisor');
    $this->db->where('eval_year', (int)$year);
    $this->db->where('emp_no', (string)$emp_no);

    if ($supervisor_emp_no !== '') {
        $this->db->where('supervisor_emp_no', (string)$supervisor_emp_no);
    }

    $row = $this->db->get()->row_array();
    return !empty($row);
}



public function get_form_total_score($form_type)
{
    $row = $this->db->get_where('annual_eval_forms', [
        'form_type' => (int)$form_type,
        'is_active' => 1
    ])->row_array();

    return $row ? (float)$row['total_score'] : 120.0;
}







}
