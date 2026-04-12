<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Annual_incentives_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /* ================== Helpers ================== */
    private function now_text(){
        return date('Y-m-d H:i:s');
    }

    private function to_float($v){
        // يدعم "1,000,000" و "1000000" ونصوص
        $v = (string)$v;
        $v = str_replace([',', ' '], '', $v);
        return is_numeric($v) ? (float)$v : 0.0;
    }

    /* ================== Batches ================== */
    public function list_batches(){
        return $this->db->order_by('id','DESC')->get('annual_incentive_batches')->result_array();
    }

    public function get_batch($id){
        return $this->db->get_where('annual_incentive_batches', ['id' => (int)$id])->row_array();
    }

    public function create_batch($data){
        $data['status']     = $data['status'] ?? 'draft';
        $data['created_at'] = $this->now_text();
        $data['updated_at'] = $this->now_text();

        $this->db->insert('annual_incentive_batches', $data);
        return (int)$this->db->insert_id();
    }

    public function update_batch($id, $data){
        $data['updated_at'] = $this->now_text();
        $this->db->where('id', (int)$id)->update('annual_incentive_batches', $data);
        return $this->db->affected_rows() >= 0;
    }

    /* ================== Employees (emp1 search) ================== */
    public function search_emp1($term){
        $term = trim($term);
        if($term === '') return [];

        // بحث بالرقم الوظيفي أو الاسم
        $this->db->select('employee_id, id_number, subscriber_name, profession, total_salary, base_salary, housing_allowance, other_allowances, n4');

        $this->db->from('incentives10');
        $this->db->group_start()
                 ->like('employee_id', $term)
                 ->or_like('subscriber_name', $term)
                 ->group_end();
        $this->db->limit(30);
        return $this->db->get()->result_array();
    }

    /* ================== Batch employees ================== */
    public function get_batch_employees($batch_id){
    return $this->db
                ->order_by('id', 'ASC') // أو DESC إذا تبي العكس
                ->get_where(
                    'annual_incentive_batch_employees',
                    ['batch_id' => (string)$batch_id]
                )
                ->result_array();
}


    public function batch_has_employee($batch_id, $employee_id){
        return $this->db->where('batch_id', (string)$batch_id)
                        ->where('employee_id', (string)$employee_id)
                        ->count_all_results('annual_incentive_batch_employees') > 0;
    }

    public function add_employee_to_batch($batch_id, $emp_row){
        // emp_row قادم من incentives10
        $data = [
            'batch_id'           => (string)$batch_id,
            'employee_id'        => (string)($emp_row['employee_id'] ?? ''),
            'id_number'          => (string)($emp_row['id_number'] ?? ''),
            'subscriber_name'    => (string)($emp_row['subscriber_name'] ?? ''),
            'profession'         => (string)($emp_row['profession'] ?? ''),
            'total_salary'       => (string)($emp_row['total_salary'] ?? ''),
            'base_salary'        => (string)($emp_row['base_salary'] ?? ''),
            'housing_allowance'  => (string)($emp_row['housing_allowance'] ?? ''),
            'other_allowances'   => (string)($emp_row['other_allowances'] ?? ''),
            'transport_allowance'=> (string)($emp_row['n4'] ?? ''),
            'calc_base_amount'   => '0',
            'multiplier'         => '0.00',
            'incentive_amount'   => '0',
            'created_at'         => $this->now_text(),
            'updated_at'         => $this->now_text(),
        ];

        $this->db->insert('annual_incentive_batch_employees', $data);
        return (int)$this->db->insert_id();
    }

    public function remove_employee($row_id){
        $this->db->where('id', (int)$row_id)->delete('annual_incentive_batch_employees');
        return $this->db->affected_rows() > 0;
    }

    /* ================== Calculation ================== */
    public function compute_calc_base_amount($batch, $emp){
        $mode = (string)($batch['calc_mode'] ?? 'total');

        if($mode === 'total'){
            return $this->to_float($emp['total_salary'] ?? 0);
        }

        $sum = 0.0;
        if(($batch['use_base_salary'] ?? '0') == '1')         $sum += $this->to_float($emp['base_salary'] ?? 0);
        if(($batch['use_housing_allowance'] ?? '0') == '1')   $sum += $this->to_float($emp['housing_allowance'] ?? 0);
        if(($batch['use_transport_allowance'] ?? '0') == '1') $sum += $this->to_float($emp['transport_allowance'] ?? 0);
        if(($batch['use_other_allowances'] ?? '0') == '1')    $sum += $this->to_float($emp['other_allowances'] ?? 0);

        return $sum;
    }

    public function update_employee_calc($row_id, $calc_base_amount, $multiplier, $incentive_amount){
        $data = [
            'calc_base_amount' => (string)$calc_base_amount,
            'multiplier'       => (string)$multiplier,
            'incentive_amount' => (string)$incentive_amount,
            'updated_at'       => $this->now_text(),
        ];
        $this->db->where('id', (int)$row_id)->update('annual_incentive_batch_employees', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function batch_totals($batch_id){
        $rows = $this->get_batch_employees($batch_id);
        $sum = 0.0;
        foreach($rows as $r){
            $sum += $this->to_float($r['incentive_amount'] ?? 0);
        }
        return $sum;
    }

    public function get_batch_employees_sorted_by_incentive($batch_id){
    return $this->db->query("
        SELECT *
        FROM annual_incentive_batch_employees
        WHERE batch_id = ?
        ORDER BY id ASC
    ", [(string)$batch_id])->result_array();
}


public function get_batch_employee_row($row_id){
    return $this->db->get_where('annual_incentive_batch_employees', ['id'=>(int)$row_id])->row_array();
}

public function recompute_all_employees_base_and_amounts_keep_multiplier($batch_id){
    $batch = $this->get_batch($batch_id);
    if(!$batch) return false;

    $rows = $this->get_batch_employees($batch_id);

    foreach($rows as $r){
        $base = $this->compute_calc_base_amount($batch, $r);

        $mult = str_replace(',', '.', (string)($r['multiplier'] ?? '0'));
        $m = is_numeric($mult) ? (float)$mult : 0.0;
        if($m < 0) $m = 0;
        if($m > 7) $m = 7;

        $inc = $base * $m;

        $this->update_employee_calc((int)$r['id'], (string)$base, number_format($m,2,'.',''), (string)$inc);
    }

    return true;
}


public function preview_totals_after_settings_change($batch_id, $new_batch_settings)
{
    $batch = $this->get_batch($batch_id);
    if (!$batch) return ['ok'=>false,'msg'=>'Batch not found'];

    // دمج الإعدادات الجديدة مع القديمة (عشان اللي ما انرسل يحتفظ بقيمته)
    $merged = array_merge($batch, $new_batch_settings);

    $rows = $this->get_batch_employees($batch_id);

    $sum = 0.0;
    foreach($rows as $r){
        $base = $this->compute_calc_base_amount($merged, $r);

        $mult = str_replace(',', '.', (string)($r['multiplier'] ?? '0'));
        $m = is_numeric($mult) ? (float)$mult : 0.0;
        if($m < 0) $m = 0;
        if($m > 7) $m = 7;

        $sum += ($base * $m);
    }

    // الميزانية الجديدة
    $budget = (string)($merged['budget_total'] ?? '0');
    $budget = str_replace([',',' '], '', $budget);
    $budget_f = is_numeric($budget) ? (float)$budget : 0.0;

    return [
        'ok' => true,
        'budget' => $budget_f,
        'spent' => $sum,
        'remaining' => ($budget_f - $sum),
    ];
}

public function reset_batch_calculations($batch_id)
{
    $data = [
        'calc_base_amount' => '0',
        'multiplier'       => '0.00',
        'incentive_amount' => '0',
        'updated_at'       => date('Y-m-d H:i:s'),
    ];

    $this->db->where('batch_id', (string)$batch_id)
             ->update('annual_incentive_batch_employees', $data);

    return $this->db->affected_rows() >= 0;
}

public function get_eval_totals_map($year, $emp_nos = [])
{
    $year = (int)$year;
    $emp_nos = array_values(array_filter(array_map('strval', (array)$emp_nos)));

    if (empty($emp_nos)) return [];

    // ✅ Self
    $self_rows = $this->db->select('emp_no, total_score, grade_label')
        ->from('annual_eval_self')
        ->where('eval_year', $year)
        ->where_in('emp_no', $emp_nos)
        ->get()->result_array();

    // ✅ Supervisor
    // (لو عندك أكثر من سجل للمشرف، نأخذ الأحدث لكل موظف)
    $sup_rows = $this->db->select('emp_no, total_score, grade_label, updated_at, id')
        ->from('annual_eval_supervisor')
        ->where('eval_year', $year)
        ->where_in('emp_no', $emp_nos)
        ->order_by('updated_at', 'DESC')
        ->order_by('id', 'DESC')
        ->get()->result_array();

    $map = [];

    foreach ($emp_nos as $no) {
        $map[$no] = [
            'self_total' => null,
            'self_grade' => null,
            'sup_total'  => null,
            'sup_grade'  => null,
        ];
    }

    foreach ($self_rows as $r) {
        $no = (string)$r['emp_no'];
        $map[$no]['self_total'] = is_null($r['total_score']) ? null : (float)$r['total_score'];
        $map[$no]['self_grade'] = (string)($r['grade_label'] ?? null);
    }

    // خذ أول سجل (لأنه مرتب بالأحدث)
    foreach ($sup_rows as $r) {
        $no = (string)$r['emp_no'];
        if (!isset($map[$no])) continue;
        if ($map[$no]['sup_total'] !== null) continue;

        $map[$no]['sup_total']  = is_null($r['total_score']) ? null : (float)$r['total_score'];
        $map[$no]['sup_grade']  = (string)($r['grade_label'] ?? null);
    }

    return $map;
}

public function get_eval_years_map($emp_nos = [], $preferred_year = null)
{
    $emp_nos = array_values(array_filter(array_map('strval', (array)$emp_nos)));
    if (empty($emp_nos)) return [];

    $preferred_year = $preferred_year ? (int)$preferred_year : null;

    // كل السنوات المتاحة من master
    $rows = $this->db->select('emp_no, eval_year')
        ->from('annual_eval_employees')
        ->where_in('emp_no', $emp_nos)
        ->where('is_active', 1)
        ->order_by('eval_year', 'DESC')
        ->get()->result_array();

    $years_by_emp = [];
    foreach ($rows as $r) {
        $no = (string)$r['emp_no'];
        $y  = (int)$r['eval_year'];
        if (!isset($years_by_emp[$no])) $years_by_emp[$no] = [];
        $years_by_emp[$no][] = $y;
    }

    $map = [];
    foreach ($emp_nos as $no) {
        $list = $years_by_emp[$no] ?? [];
        if (empty($list)) { $map[$no] = null; continue; }

        // لو السنة المطلوبة موجودة استخدمها، وإلا خذ آخر سنة (أحدث واحدة)
        if ($preferred_year && in_array($preferred_year, $list, true)) {
            $map[$no] = $preferred_year;
        } else {
            $map[$no] = (int)$list[0];
        }
    }
    return $map;
}

public function get_eval_totals_multi_year($emp_year_map = [])
{
    if (empty($emp_year_map)) return [];

    // اجمع emp_nos + years
    $emp_nos = array_keys($emp_year_map);
    $years = array_values(array_unique(array_filter(array_map('intval', array_values($emp_year_map)))));

    if (empty($emp_nos) || empty($years)) return [];

    // Self rows
    $self_rows = $this->db->select('emp_no, eval_year, total_score, grade_label')
        ->from('annual_eval_self')
        ->where_in('emp_no', $emp_nos)
        ->where_in('eval_year', $years)
        ->get()->result_array();

    // Supervisor rows (الأحدث)
    $sup_rows = $this->db->select('emp_no, eval_year, total_score, grade_label, updated_at, id')
        ->from('annual_eval_supervisor')
        ->where_in('emp_no', $emp_nos)
        ->where_in('eval_year', $years)
        ->order_by('updated_at', 'DESC')
        ->order_by('id', 'DESC')
        ->get()->result_array();

    // جهّز map
    $map = [];
    foreach ($emp_year_map as $emp_no => $y) {
        $map[$emp_no] = [
            'eval_year'  => $y,
            'self_total' => null,
            'self_grade' => null,
            'sup_total'  => null,
            'sup_grade'  => null,
        ];
    }

    foreach ($self_rows as $r) {
        $no = (string)$r['emp_no'];
        $y  = (int)$r['eval_year'];
        if (!isset($map[$no]) || (int)$map[$no]['eval_year'] !== $y) continue;

        $map[$no]['self_total'] = is_null($r['total_score']) ? null : (float)$r['total_score'];
        $map[$no]['self_grade'] = (string)($r['grade_label'] ?? null);
    }

    // Supervisor: أول سجل لكل موظف/سنة (لأنه مرتب بالأحدث)
    $seen = [];
    foreach ($sup_rows as $r) {
        $no = (string)$r['emp_no'];
        $y  = (int)$r['eval_year'];
        $key = $no.'|'.$y;
        if (isset($seen[$key])) continue;
        $seen[$key] = true;

        if (!isset($map[$no]) || (int)$map[$no]['eval_year'] !== $y) continue;

        $map[$no]['sup_total'] = is_null($r['total_score']) ? null : (float)$r['total_score'];
        $map[$no]['sup_grade'] = (string)($r['grade_label'] ?? null);
    }

    return $map;
}











}
