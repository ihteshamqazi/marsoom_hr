<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leader_salaries_report_model extends CI_Model
{
    public function __construct(){ parent::__construct(); }

    private function salary_num_expr()
    {
        return "CAST(REPLACE(REPLACE(TRIM(e.total_salary), ',', ''), '،', '') AS DECIMAL(18,2))";
    }

    private function apply_filters_sql(&$sql, $salaryExpr, array $filters)
    {
        if (!empty($filters['q'])) {
            $q = $this->db->escape_like_str($filters['q']);
            $sql .= " AND (e.subscriber_name LIKE '%{$q}%' OR e.employee_id LIKE '%{$q}%') ";
        }

        if (!empty($filters['profession'])) {
            $p = $this->db->escape($filters['profession']);
            $sql .= " AND e.profession = {$p} ";
        }

        if (($filters['min_salary'] ?? '') !== '' && is_numeric(str_replace([',','،'], '', $filters['min_salary']))) {
            $min = (float) str_replace([',','،'], '', $filters['min_salary']);
            $sql .= " AND {$salaryExpr} >= {$min} ";
        }
        if (($filters['max_salary'] ?? '') !== '' && is_numeric(str_replace([',','،'], '', $filters['max_salary']))) {
            $max = (float) str_replace([',','،'], '', $filters['max_salary']);
            $sql .= " AND {$salaryExpr} <= {$max} ";
        }
    }

    private function order_sql(&$sql, array $filters)
    {
        $sort = $filters['sort'] ?? 'salary_desc';
        switch ($sort) {
            case 'salary_asc':  $sql .= " ORDER BY t.total_salary_num ASC "; break;
            case 'name_desc':   $sql .= " ORDER BY t.subscriber_name DESC "; break;
            case 'emp_asc':     $sql .= " ORDER BY t.employee_id ASC "; break;
            case 'emp_desc':    $sql .= " ORDER BY t.employee_id DESC "; break;
            case 'name_asc':    $sql .= " ORDER BY t.subscriber_name ASC "; break;
            case 'salary_desc':
            default:            $sql .= " ORDER BY t.total_salary_num DESC "; break;
        }
    }

    /* ========= Basics ========= */

    public function get_employee_basic($employee_id)
    {
        $this->db->select("employee_id, subscriber_name, profession, n13");
        $this->db->from("emp1");
        $this->db->where("employee_id", $employee_id);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function get_professions_list()
    {
        $this->db->select('profession');
        $this->db->from('emp1');
        $this->db->where("profession IS NOT NULL AND profession != ''", null, false);
        $this->db->group_by('profession');
        $this->db->order_by('profession', 'ASC');
        $rows = $this->db->get()->result_array();

        $list = [];
        foreach ($rows as $r) $list[] = $r['profession'];
        return $list;
    }

    public function get_managers_list()
    {
        $sql = "
            SELECT
                os.n2 AS manager_id,
                MAX(e.subscriber_name) AS manager_name,
                MAX(e.profession) AS manager_profession
            FROM organizational_structure os
            LEFT JOIN emp1 e ON e.employee_id = os.n2
            WHERE os.n1 = 1001
            GROUP BY os.n2
            ORDER BY manager_name ASC
        ";
        return $this->db->query($sql)->result_array();
    }

    /* ========= Leaders report (n2) ========= */

    public function get_grouped_leaders_report(array $filters = [])
    {
        $companies = [
            1 => 'شركة مرسوم',
            2 => 'مكتب الدكتور صالح الجربوع للمحاماة',
        ];

        $out = [];
        foreach ($companies as $n13 => $label) {
            $out[] = [
                'n13'   => $n13,
                'label' => $label,
                'stats' => $this->leaders_stats($n13, $filters),
                'rows'  => $this->leaders_rows($n13, $filters),
            ];
        }
        return $out;
    }

    private function leaders_subquery($n13, array $filters = [])
    {
        $salary = $this->salary_num_expr();

        $sql = "
            SELECT
                e.employee_id,
                MAX(e.subscriber_name) AS subscriber_name,
                MAX(e.profession)      AS profession,
                MAX({$salary})         AS total_salary_num,
                MAX(e.n13)             AS n13
            FROM emp1 e
            INNER JOIN organizational_structure os ON os.n2 = e.employee_id
            WHERE e.status = 'active'
              AND os.n1 = 1001
              AND e.n13 = " . (int)$n13 . "
        ";

        $this->apply_filters_sql($sql, $salary, $filters);
        $sql .= " GROUP BY e.employee_id ";
        return $sql;
    }

    private function leaders_rows($n13, array $filters = [])
    {
        $sub = $this->leaders_subquery($n13, $filters);
        $sql = "SELECT employee_id, subscriber_name, profession, total_salary_num, n13 FROM ({$sub}) t ";
        $this->order_sql($sql, $filters);
        return $this->db->query($sql)->result_array();
    }

    private function leaders_stats($n13, array $filters = [])
    {
        $sub = $this->leaders_subquery($n13, $filters);
        $sql = "
            SELECT
                COUNT(*) AS cnt,
                COALESCE(SUM(total_salary_num), 0) AS total,
                COALESCE(AVG(total_salary_num), 0) AS avg_salary,
                COALESCE(MAX(total_salary_num), 0) AS max_salary,
                COALESCE(MIN(total_salary_num), 0) AS min_salary
            FROM ({$sub}) t
        ";
        return $this->db->query($sql)->row_array();
    }

    /* ========= n3 (leader -> subordinates) ========= */

    public function has_subordinates_n3($manager_id)
    {
        $this->db->select('1', false);
        $this->db->from('organizational_structure os');
        $this->db->where('os.n1', 1001);
        $this->db->where('os.n2', $manager_id);
        $this->db->where("os.n3 IS NOT NULL AND os.n3 != ''", null, false);
        $this->db->limit(1);
        return (bool)$this->db->get()->row_array();
    }

    public function get_grouped_subordinates_n3_report($manager_id, array $filters = [])
    {
        $companies = [
            1 => 'شركة مرسوم',
            2 => 'مكتب الدكتور صالح الجربوع للمحاماة',
        ];

        $out = [];
        foreach ($companies as $n13 => $label) {
            $out[] = [
                'n13'   => $n13,
                'label' => $label,
                'stats' => $this->n3_stats($manager_id, $n13, $filters),
                'rows'  => $this->n3_rows($manager_id, $n13, $filters),
            ];
        }
        return $out;
    }

    private function n3_subquery($manager_id, $n13, array $filters = [])
    {
        $salary = $this->salary_num_expr();

        $sql = "
            SELECT
                e.employee_id,
                MAX(e.subscriber_name) AS subscriber_name,
                MAX(e.profession)      AS profession,
                MAX({$salary})         AS total_salary_num,
                MAX(e.n13)             AS n13
            FROM organizational_structure os
            INNER JOIN emp1 e ON e.employee_id = os.n3
            WHERE os.n1 = 1001
              AND os.n2 = " . $this->db->escape($manager_id) . "
              AND e.status = 'active'
              AND e.n13 = " . (int)$n13 . "
        ";

        $this->apply_filters_sql($sql, $salary, $filters);
        $sql .= " GROUP BY e.employee_id ";
        return $sql;
    }

    private function n3_rows($manager_id, $n13, array $filters = [])
    {
        $sub = $this->n3_subquery($manager_id, $n13, $filters);
        $sql = "SELECT employee_id, subscriber_name, profession, total_salary_num, n13 FROM ({$sub}) t ";
        $this->order_sql($sql, $filters);
        return $this->db->query($sql)->result_array();
    }

    private function n3_stats($manager_id, $n13, array $filters = [])
    {
        $sub = $this->n3_subquery($manager_id, $n13, $filters);
        $sql = "
            SELECT
                COUNT(*) AS cnt,
                COALESCE(SUM(total_salary_num), 0) AS total,
                COALESCE(AVG(total_salary_num), 0) AS avg_salary,
                COALESCE(MAX(total_salary_num), 0) AS max_salary,
                COALESCE(MIN(total_salary_num), 0) AS min_salary
            FROM ({$sub}) t
        ";
        return $this->db->query($sql)->row_array();
    }

    public function export_subordinates_n3_rows($manager_id, array $filters = [])
    {
        return array_merge(
            $this->n3_rows($manager_id, 1, $filters),
            $this->n3_rows($manager_id, 2, $filters)
        );
    }

    /* ========= n4 (n3 -> subordinates of subordinate) =========
       ✅ هنا نفترض الربط: organizational_structure.n3 = (تابع) و organizational_structure.n4 = (تابعيه)
    */

    public function has_followers_n4_by_n3($n3_id)
    {
        $this->db->select('1', false);
        $this->db->from('organizational_structure os');
        $this->db->where('os.n1', 1001);
        $this->db->where('os.n3', $n3_id);
        $this->db->where("os.n4 IS NOT NULL AND os.n4 != ''", null, false);
        $this->db->limit(1);
        return (bool)$this->db->get()->row_array();
    }

    public function get_grouped_n4_by_n3_others_report($n3_id, array $filters = [])
    {
        return $this->get_grouped_n4_by_n3($n3_id, $filters, false);
    }

    public function get_grouped_n4_by_n3_collectors_report($n3_id, array $filters = [])
    {
        return $this->get_grouped_n4_by_n3($n3_id, $filters, true);
    }

    private function get_grouped_n4_by_n3($n3_id, array $filters, $isCollectors)
    {
        $companies = [
            1 => 'شركة مرسوم',
            2 => 'مكتب الدكتور صالح الجربوع للمحاماة',
        ];

        $out = [];
        foreach ($companies as $n13 => $label) {
            $out[] = [
                'n13'   => $n13,
                'label' => $label,
                'stats' => $this->n4_by_n3_stats($n3_id, $n13, $filters, $isCollectors),
                'rows'  => $this->n4_by_n3_rows($n3_id, $n13, $filters, $isCollectors),
            ];
        }
        return $out;
    }

    private function n4_by_n3_subquery($n3_id, $n13, array $filters, $isCollectors)
    {
        $salary = $this->salary_num_expr();

        $sql = "
            SELECT
                e.employee_id,
                MAX(e.subscriber_name) AS subscriber_name,
                MAX(e.profession)      AS profession,
                MAX({$salary})         AS total_salary_num,
                MAX(e.n13)             AS n13
            FROM organizational_structure os
            INNER JOIN emp1 e ON e.employee_id = os.n4
            WHERE os.n1 = 1001
              AND os.n3 = " . $this->db->escape($n3_id) . "
              AND e.status = 'active'
              AND e.n13 = " . (int)$n13 . "
        ";

        if ($isCollectors) {
            $sql .= " AND e.profession = " . $this->db->escape('محصل ديون') . " ";
        } else {
            $sql .= " AND (e.profession IS NULL OR e.profession <> " . $this->db->escape('محصل ديون') . ") ";
        }

        $this->apply_filters_sql($sql, $salary, $filters);
        $sql .= " GROUP BY e.employee_id ";
        return $sql;
    }

    private function n4_by_n3_rows($n3_id, $n13, array $filters, $isCollectors)
    {
        $sub = $this->n4_by_n3_subquery($n3_id, $n13, $filters, $isCollectors);
        $sql = "SELECT employee_id, subscriber_name, profession, total_salary_num, n13 FROM ({$sub}) t ";
        $this->order_sql($sql, $filters);
        return $this->db->query($sql)->result_array();
    }

    private function n4_by_n3_stats($n3_id, $n13, array $filters, $isCollectors)
    {
        $sub = $this->n4_by_n3_subquery($n3_id, $n13, $filters, $isCollectors);
        $sql = "
            SELECT
                COUNT(*) AS cnt,
                COALESCE(SUM(total_salary_num), 0) AS total,
                COALESCE(AVG(total_salary_num), 0) AS avg_salary,
                COALESCE(MAX(total_salary_num), 0) AS max_salary,
                COALESCE(MIN(total_salary_num), 0) AS min_salary
            FROM ({$sub}) t
        ";
        return $this->db->query($sql)->row_array();
    }

    public function export_n4_by_n3_others_rows($n3_id, array $filters = [])
    {
        return array_merge(
            $this->n4_by_n3_rows($n3_id, 1, $filters, false),
            $this->n4_by_n3_rows($n3_id, 2, $filters, false)
        );
    }

    public function export_n4_by_n3_collectors_rows($n3_id, array $filters = [])
    {
        return array_merge(
            $this->n4_by_n3_rows($n3_id, 1, $filters, true),
            $this->n4_by_n3_rows($n3_id, 2, $filters, true)
        );
    }
}
