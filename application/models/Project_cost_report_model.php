<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Project_cost_report_model extends CI_Model
{
    protected $users_table = 'users';
    protected $emp_table   = 'emp1';
    protected $prod_table  = 'employee_reimbursements';
    protected $comm_table  = 'project_commission';
    protected $op_table    = 'operating_cost';

    // users (حسب وصفك)
    private $U_EMP_NO        = 'username';
    private $U_NAME          = 'name';
    private $U_TYPE          = 'type';
    private $U_SUP_NAME      = 'suberviser';
    private $U_SUP_ID        = 'sub_id';
    private $U_PROJECT_ID    = 'project';
    private $U_PROJECT_NAME  = 'project_name';
    private $U_MGR_ID        = 'mang_id';
    private $U_MGR_NAME      = 'manag_name';

    // emp1
    private $E_EMP_ID   = 'employee_id';
    private $E_STATUS   = 'status';
    private $E_SALARY   = 'total_salary';

    // reimbursements
    private $P_EMP_NO     = 'n6';
    private $P_AMOUNT     = 'n2';
    private $P_PROJECT_ID = 'project_id';
    private $P_STATUS     = 'status';
    private $P_DATE       = 'n8'; // ✅ تاريخ السداد yyyy/mm/dd
    private $P_SUP_NO     = 'x3';

    // commission
    private $C_PROJECT_ID = 'n1';
    private $C_RATE       = 'n3';
    private $C_MONTH      = 'date'; // yyyy/mm

    // operating cost (عندك n1 فقط – ثابت لكل موظف)
    private $O_MONTH      = 'month'; // قد لا يكون موجود
    private $O_COST       = 'n1';

    // ==========================
    // Helpers
    // ==========================
    private function month_like_prefix($month)
    {
        return $this->db->escape_like_str($month) . '/'; // yyyy/mm/
    }

    private function type_label($type)
    {
        $t = (int)$type;
        if ($t === 1) return 'محصل ديون';
        if ($t === 2) return 'مشرف تحصيل';
        if ($t === 3) return 'مدير مشروع';
        return 'غير محدد';
    }

    // ✅ تكلفة تشغيل ثابتة لكل موظف
    public function get_fixed_operating_cost_per_person($month)
    {
        // إذا يوجد عمود month في جدول operating_cost خذ تكلفة الشهر
        if ($this->db->field_exists($this->O_MONTH, $this->op_table)) {
            $r = $this->db->select("o.`{$this->O_COST}` AS cost", false)
                ->from($this->op_table.' o')
                ->where("o.`{$this->O_MONTH}`", $month)
                ->order_by("o.id", "DESC")
                ->limit(1)
                ->get()->row_array();

            if ($r && $r['cost'] !== null && $r['cost'] !== '') return (float)$r['cost'];

            // fallback: آخر سجل
            $r2 = $this->db->select("o.`{$this->O_COST}` AS cost", false)
                ->from($this->op_table.' o')
                ->order_by("o.id", "DESC")
                ->limit(1)
                ->get()->row_array();

            return $r2 ? (float)$r2['cost'] : 0.0;
        }

        // إذا ما فيه month: خذ آخر قيمة
        $r = $this->db->select("o.`{$this->O_COST}` AS cost", false)
            ->from($this->op_table.' o')
            ->order_by("o.id", "DESC")
            ->limit(1)
            ->get()->row_array();

        return $r ? (float)$r['cost'] : 0.0;
    }

    // ==========================
    // Project header
    // ==========================
    public function get_project_header($project_id)
    {
        $project_id = (int)$project_id;

        $row = $this->db->select("
                u.`{$this->U_PROJECT_ID}` AS project_id,
                MAX(u.`{$this->U_PROJECT_NAME}`) AS project_name
            ", false)
            ->from($this->users_table.' u')
            ->where("u.`{$this->U_PROJECT_ID}`", $project_id)
            ->get()->row_array();

        if (!$row) {
            $row = ['project_id' => $project_id, 'project_name' => '—'];
        }
        return $row;
    }

    // ==========================
    // Commission rate
    // ==========================
    public function get_commission_rate($project_id, $month)
    {
        $project_id = (int)$project_id;

        $r = $this->db->select("c.`{$this->C_RATE}` AS rate", false)
            ->from($this->comm_table.' c')
            ->where("c.`{$this->C_PROJECT_ID}`", $project_id)
            ->where("c.`{$this->C_MONTH}`", $month)
            ->order_by("c.id", "DESC")
            ->limit(1)
            ->get()->row_array();

        if ($r && $r['rate'] !== null && $r['rate'] !== '') return (float)$r['rate'];

        $r2 = $this->db->select("c.`{$this->C_RATE}` AS rate", false)
            ->from($this->comm_table.' c')
            ->where("c.`{$this->C_PROJECT_ID}`", $project_id)
            ->order_by("c.`{$this->C_MONTH}`", "DESC")
            ->order_by("c.id", "DESC")
            ->limit(1)
            ->get()->row_array();

        return $r2 ? (float)$r2['rate'] : 0.0;
    }

    // ==========================
    // Summary: Projects list
    // ==========================
    public function get_projects_summary($month)
    {
        $fixed_cost = $this->get_fixed_operating_cost_per_person($month);

        $projects = $this->db->select("
                u.`{$this->U_PROJECT_ID}` AS project_id,
                MAX(u.`{$this->U_PROJECT_NAME}`) AS project_name,
                COUNT(*) AS people_count
            ", false)
            ->from($this->users_table.' u')
            ->where("u.`{$this->U_PROJECT_ID}` IS NOT NULL", null, false)
            ->where("u.`{$this->U_PROJECT_ID}` <> ''", null, false)
            ->group_by("u.`{$this->U_PROJECT_ID}`")
            ->order_by("people_count", "DESC")          // ✅ ترتيب حسب عدد الموظفين
            ->order_by("project_name", "ASC")
            ->get()->result_array();

        foreach ($projects as &$p) {
            $pid  = (int)$p['project_id'];
            $rate = $this->get_commission_rate($pid, $month);

            $total_salary = $this->get_project_total_salary($pid);
            $total_prod   = $this->get_project_total_productivity($pid, $month);

            $company_comm = round(($total_prod * $rate / 100), 2);

            // ✅ تكلفة تشغيل ثابتة لكل شخص
            $total_operating = round($fixed_cost * (int)$p['people_count'], 2);

            $p['commission_rate']       = $rate;
            $p['total_salary']          = round($total_salary, 2);
            $p['total_productivity']    = round($total_prod, 2);
            $p['company_commission']    = $company_comm;
            $p['fixed_operating_person']= round($fixed_cost, 2);
            $p['total_operating_cost']  = $total_operating;
            $p['approx_margin']         = round(($company_comm - $total_salary - $total_operating), 2);
        }
        unset($p);

        return $projects;
    }

    // ==========================
    // Totals helpers
    // ==========================
    private function get_project_total_salary($project_id)
    {
        $project_id = (int)$project_id;

        $r = $this->db->select("SUM(e.`{$this->E_SALARY}`) AS total", false)
            ->from($this->users_table.' u')
            ->join($this->emp_table.' e', "e.`{$this->E_EMP_ID}` = u.`{$this->U_EMP_NO}`", 'left')
            ->where("u.`{$this->U_PROJECT_ID}`", $project_id)
            ->where("e.`{$this->E_STATUS}`", 'active')
            ->get()->row_array();

        return (float)($r['total'] ?? 0);
    }

    private function get_project_total_productivity($project_id, $month)
    {
        $project_id = (int)$project_id;
        $like = $this->month_like_prefix($month);

        $r = $this->db->select("SUM(p.`{$this->P_AMOUNT}`) AS total", false)
            ->from($this->prod_table.' p')
            ->where("p.`{$this->P_PROJECT_ID}`", $project_id)
            ->where("p.`{$this->P_STATUS}`", 1)
            ->like("p.`{$this->P_DATE}`", $like, 'after') // ✅ n8
            ->get()->row_array();

        return (float)($r['total'] ?? 0);
    }

    // ==========================
    // Details: Employees cost in project
    // ==========================
    public function get_project_employees_cost($project_id, $month)
    {
        $project_id = (int)$project_id;
        $rate       = $this->get_commission_rate($project_id, $month);
        $fixed_cost = $this->get_fixed_operating_cost_per_person($month);

        $users = $this->db->select("
                u.`{$this->U_EMP_NO}`       AS emp_no,
                u.`{$this->U_NAME}`         AS name,
                u.`{$this->U_TYPE}`         AS type,
                u.`{$this->U_SUP_ID}`       AS supervisor_id,
                u.`{$this->U_SUP_NAME}`     AS supervisor_name,
                u.`{$this->U_MGR_ID}`       AS manager_id,
                u.`{$this->U_MGR_NAME}`     AS manager_name
            ", false)
            ->from($this->users_table.' u')
            ->where("u.`{$this->U_PROJECT_ID}`", $project_id)
            // غير محصل أولاً ثم محصل
            ->order_by("CASE WHEN u.`{$this->U_TYPE}`=1 THEN 2 ELSE 1 END", "ASC", false)
            ->order_by("u.`{$this->U_TYPE}`", "ASC")
            ->order_by("u.`{$this->U_NAME}`", "ASC")
            ->get()->result_array();

        if (empty($users)) return [];

        $empNos = array_values(array_filter(array_map(function($x){
            return $x['emp_no'] ?? null;
        }, $users)));

        if (empty($empNos)) return [];

        // رواتب
        $salaryMap = [];
        $salRows = $this->db->select("e.`{$this->E_EMP_ID}` AS emp_no, e.`{$this->E_SALARY}` AS salary", false)
            ->from($this->emp_table.' e')
            ->where("e.`{$this->E_STATUS}`", 'active')
            ->where_in("e.`{$this->E_EMP_ID}`", $empNos)
            ->get()->result_array();

        foreach ($salRows as $s) $salaryMap[$s['emp_no']] = (float)$s['salary'];

        // إنتاجية الشهر
        $prodMap = [];
        $like = $this->month_like_prefix($month);

        $prodRows = $this->db->select("p.`{$this->P_EMP_NO}` AS emp_no, SUM(p.`{$this->P_AMOUNT}`) AS total_prod", false)
            ->from($this->prod_table.' p')
            ->where("p.`{$this->P_PROJECT_ID}`", $project_id)
            ->where("p.`{$this->P_STATUS}`", 1)
            ->like("p.`{$this->P_DATE}`", $like, 'after')
            ->where_in("p.`{$this->P_EMP_NO}`", $empNos)
            ->group_by("p.`{$this->P_EMP_NO}`")
            ->get()->result_array();

        foreach ($prodRows as $pr) $prodMap[$pr['emp_no']] = (float)$pr['total_prod'];

        // دمج + حسابات
        $rows = [];
        foreach ($users as $u) {
            $empNo = $u['emp_no'];

            $salary = $salaryMap[$empNo] ?? 0.0;
            $prod   = $prodMap[$empNo] ?? 0.0;

            $companyComm = round(($prod * $rate / 100), 2);
            $opCost      = round($fixed_cost, 2); // ✅ ثابت لكل موظف
            $margin      = round(($companyComm - $salary - $opCost), 2);

            $rows[] = [
                'emp_no'          => $empNo,
                'name'            => $u['name'],
                'type'            => (int)$u['type'],
                'type_label'      => $this->type_label($u['type']),
                'supervisor_id'   => $u['supervisor_id'] ?: '—',
                'supervisor_name' => $u['supervisor_name'] ?: '—',
                'manager_id'      => $u['manager_id'] ?: '—',
                'manager_name'    => $u['manager_name'] ?: '—',
                'salary'          => round($salary, 2),
                'productivity'    => round($prod, 2),
                'commission_rate' => (float)$rate,
                'company_commission' => $companyComm,
                'operating_cost'  => $opCost,
                'approx_margin'   => $margin,
            ];
        }

        return $rows;
    }

    // ==========================
    // Totals for project page
    // ==========================
    public function calc_totals($rows, $rate)
    {
        $sumSalary = 0; $sumProd = 0; $sumOp = 0; $sumCompanyComm = 0;

        foreach ($rows as $r) {
            $sumSalary      += (float)$r['salary'];
            $sumProd        += (float)$r['productivity'];
            $sumOp          += (float)$r['operating_cost'];
            $sumCompanyComm += (float)$r['company_commission'];
        }

        return [
            'total_salary'       => round($sumSalary, 2),
            'total_productivity' => round($sumProd, 2),
            'total_operating'    => round($sumOp, 2),
            'company_commission' => round($sumCompanyComm, 2),
            'approx_margin'      => round(($sumCompanyComm - $sumSalary - $sumOp), 2),
        ];
    }
}
