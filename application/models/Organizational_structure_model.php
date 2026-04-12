<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organizational_structure_model extends CI_Model
{
    private $table = 'organizational_structure';
    private $cols  = ['n1','n2','n3','n4','n5','n6','n7'];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /* =========================
       Helpers
    ========================= */

    private function clean($v)
    {
        $v = trim((string)$v);
        return ($v === '' || $v === '0') ? '' : $v;
    }

    private function path_to_cols(array $path)
    {
        $data = [];
        for ($i=0; $i<7; $i++) {
            $data[$this->cols[$i]] = isset($path[$i]) ? (string)$path[$i] : null;
        }
        return $data;
    }

    private function is_prefix(array $prefix, array $full)
    {
        if (count($prefix) > count($full)) return false;
        for ($i=0; $i<count($prefix); $i++) {
            if ((string)$prefix[$i] !== (string)$full[$i]) return false;
        }
        return true;
    }

    private function normalize_path_from_row(array $row)
    {
        $path = [];
        foreach ($this->cols as $c) {
            $v = $this->clean($row[$c] ?? '');
            if ($v !== '') $path[] = $v;
        }
        return $path;
    }

    /* =========================
       جلب المسارات من أي مكان يظهر فيه الموظف
       (هنا الفرق المهم عن النسخة السابقة)
    ========================= */

    private function get_any_rows_containing_employee($employee_id)
    {
        $employee_id = (string)$employee_id;

        $w = [];
        $params = [];
        foreach ($this->cols as $c) {
            $w[] = "$c = ?";
            $params[] = $employee_id;
        }

        $sql = "SELECT id,n1,n2,n3,n4,n5,n6,n7
                FROM {$this->table}
                WHERE " . implode(' OR ', $w);

        return $this->db->query($sql, $params)->result_array();
    }

    /**
     * يرجع "أقصر مسار" للموظف:
     * إذا الموظف ظهر في n4 في أي صف -> المسار يكون [n1,n2,n3,n4]
     * وإذا ظهر في n6 -> [n1..n6]
     */
    private function find_best_path_for_employee($employee_id)
    {
        $rows = $this->get_any_rows_containing_employee($employee_id);
        if (empty($rows)) return null;

        $best = null;
        $bestDepth = 999;

        foreach ($rows as $r) {
            // ابحث بأي عمود ظهر
            $path = [];
            foreach ($this->cols as $idx => $c) {
                $v = $this->clean($r[$c] ?? '');
                if ($v === '') break;
                $path[] = $v;

                if ($v === (string)$employee_id) {
                    $depth = $idx + 1; // n1=1 ... n7=7
                    if ($depth < $bestDepth) {
                        $bestDepth = $depth;
                        $best = array_slice($path, 0, $depth);
                    }
                    break;
                }
            }
        }

        return $best; // ممكن null
    }

    /**
     * يجيب كل الصفوف التابعة لمسار prefix (مثلاً prefix= [.., عبدالباري])
     * أي صف يبدأ بنفس القيم في الأعمدة حتى العمق
     */
    private function get_rows_by_prefix(array $prefix)
    {
        $depth = count($prefix);
        if ($depth < 1) return [];

        $w = [];
        $params = [];
        for ($i=0; $i<$depth; $i++) {
            $col = $this->cols[$i];
            $w[] = "{$col} = ?";
            $params[] = (string)$prefix[$i];
        }

        $sql = "SELECT id,n1,n2,n3,n4,n5,n6,n7
                FROM {$this->table}
                WHERE " . implode(' AND ', $w);

        return $this->db->query($sql, $params)->result_array();
    }

    /**
     * يتحقق هل يوجد صف يمثل الموظف نفسه على نفس depth (ينتهي عنده)
     * مثال: لو المسار [.., عبدالباري] لازم n(depth)=عبدالباري والباقي NULL/فارغ
     */
    private function find_exact_employee_row(array $employeePath)
    {
        $depth = count($employeePath);
        if ($depth < 1) return null;

        $w = [];
        $params = [];

        // نفس prefix
        for ($i=0; $i<$depth; $i++) {
            $col = $this->cols[$i];
            $w[] = "{$col} = ?";
            $params[] = (string)$employeePath[$i];
        }

        // بقية الأعمدة فارغة/NULL/0
        for ($i=$depth; $i<7; $i++) {
            $col = $this->cols[$i];
            $w[] = "({$col} IS NULL OR {$col} = '' OR {$col} = '0')";
        }

        $sql = "SELECT id,n1,n2,n3,n4,n5,n6,n7
                FROM {$this->table}
                WHERE " . implode(' AND ', $w) . "
                LIMIT 1";

        $row = $this->db->query($sql, $params)->row_array();
        return $row ?: null;
    }

    /* =========================
       MOVE (نقل شجرة كاملة)
    ========================= */

    public function move_employee($employee_id, $new_manager_id)
    {
        $employee_id = $this->clean($employee_id);
        $new_manager_id = $this->clean($new_manager_id);

        if ($employee_id === '' || $employee_id === '__unlinked__') {
            return ['ok'=>false, 'msg'=>'رقم الموظف غير صحيح'];
        }

        // جذر
        $move_to_root = ($new_manager_id === '' || $new_manager_id === '#' || $new_manager_id === '0' || $new_manager_id === '__unlinked__');

        // 1) مسار الموظف (عبدالباري) من أي ظهور له
        $oldEmpPath = $this->find_best_path_for_employee($employee_id);
        if (!$oldEmpPath) {
            // الموظف غير موجود بالهيكل (من emp1 فقط)
            $oldEmpPath = [$employee_id];
        }

        // 2) مسار المدير الجديد (فهد) من أي ظهور له
        $mgrPath = [];
        if (!$move_to_root) {
            if ($new_manager_id === $employee_id) {
                return ['ok'=>false, 'msg'=>'لا يمكن جعل الموظف مديرًا لنفسه'];
            }

            $mgrPath = $this->find_best_path_for_employee($new_manager_id);
            if (!$mgrPath) {
                return ['ok'=>false, 'msg'=>'المدير المحدد غير موجود في الهيكل (لم يتم العثور عليه في n1..n7)'];
            }

            // منع الحلقة: لا تنقل الموظف تحت أحد أبنائه
            if ($this->is_prefix($oldEmpPath, $mgrPath)) {
                return ['ok'=>false, 'msg'=>'رفض: لا يمكن نقل الموظف تحت أحد تابعيه (حلقة)'];
            }
        }

        // 3) المسار الجديد للموظف
        $newEmpPath = $move_to_root ? [$employee_id] : array_merge($mgrPath, [$employee_id]);

        if (count($newEmpPath) > 7) {
            return ['ok'=>false, 'msg'=>'رفض: العمق الجديد يتجاوز 7 مستويات'];
        }

        // 4) كل الصفوف التابعة لعبدالباري (prefix القديم)
        $subtreeRows = $this->get_rows_by_prefix($oldEmpPath);

        // لو ما طلع شيء (نادر)، اعتبره فقط الموظف نفسه
        if (empty($subtreeRows)) {
            $subtreeRows = [];
        }

        $this->db->trans_begin();

        try {
            // 5) تحديث كل صف في الشجرة: استبدال prefix
            foreach ($subtreeRows as $r) {
                $p = $this->normalize_path_from_row($r);
                if (empty($p)) continue;

                if (!$this->is_prefix($oldEmpPath, $p)) continue;

                $suffix = array_slice($p, count($oldEmpPath)); // تابعين بعد الموظف
                $newPath = array_merge($newEmpPath, $suffix);

                if (count($newPath) > 7) {
                    throw new Exception('رفض: أحد التابعين سيتجاوز 7 مستويات بعد النقل');
                }

                $upd = $this->path_to_cols($newPath);
                $this->db->where('id', (int)$r['id'])->update($this->table, $upd);
            }

            // 6) تأكد أن للموظف نفسه صف يمثل موقعه (حتى لو ما كان موجود سابقاً)
            $existingRow = $this->find_exact_employee_row($oldEmpPath);
            $newRowData  = $this->path_to_cols($newEmpPath);

            if ($existingRow && !empty($existingRow['id'])) {
                $this->db->where('id', (int)$existingRow['id'])->update($this->table, $newRowData);
            } else {
                // إدراج صف جديد يمثل الموظف في موقعه الجديد
                $this->db->insert($this->table, $newRowData);
            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('DB Error');
            }

            $this->db->trans_commit();
            return ['ok'=>true, 'msg'=>'تم حفظ التعديل ونقل الموظف مع كامل تابعيه بنجاح'];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['ok'=>false, 'msg'=>'فشل الحفظ: '.$e->getMessage()];
        }
    }

    /* =========================
       TREE + EXPORT (كما هي)
       إذا عندك النسخ السابقة اتركها، أو استخدم هذه
    ========================= */

    public function get_emp_map()
    {
        $rows = $this->db->select('employee_id, subscriber_name, profession')
                         ->from('emp1')
                         ->get()->result_array();

        $map = [];
        foreach ($rows as $r) {
            $id = (string)$r['employee_id'];
            $map[$id] = [
                'name' => $r['subscriber_name'] ?? '',
                'job'  => $r['profession'] ?? ''
            ];
        }
        return $map;
    }

    public function get_structure_rows()
    {
        return $this->db->select('id,n1,n2,n3,n4,n5,n6,n7')
                        ->from($this->table)
                        ->get()->result_array();
    }

    public function get_tree_for_jstree()
    {
        $emp  = $this->get_emp_map();
        $rows = $this->get_structure_rows();

        $nodes = [];
        $edges = [];

        foreach ($rows as $r) {
            $path = $this->normalize_path_from_row($r);
            $cnt  = count($path);
            if ($cnt < 1) continue;

            for ($i=0; $i<$cnt; $i++) {
                $id     = (string)$path[$i];
                $parent = ($i === 0) ? '#' : (string)$path[$i-1];

                $edges[$id] = $parent;

                if (!isset($nodes[$id])) {
                    $name = $emp[$id]['name'] ?? ('موظف #' . $id);
                    $job  = $emp[$id]['job'] ?? '';
                    $text = $name . ' — (' . $id . ')' . ($job !== '' ? ' — ' . $job : '');

                    $nodes[$id] = [
                        'id'     => $id,
                        'parent' => $parent,
                        'text'   => $text,
                        'state'  => ['opened' => true]
                    ];
                }
            }
        }

        foreach ($nodes as $id => $n) {
            $nodes[$id]['parent'] = $edges[$id] ?? '#';
        }

        // غير مرتبطين
        $orphansRoot = '__unlinked__';
        $nodes[$orphansRoot] = [
            'id' => $orphansRoot,
            'parent' => '#',
            'text' => 'غير مرتبطين بالهيكل (من emp1)',
            'state' => ['opened' => false]
        ];

        foreach ($emp as $id => $info) {
            if (!isset($nodes[$id])) {
                $text = ($info['name'] ?: ('موظف #' . $id)) . ' — (' . $id . ')'
                      . (!empty($info['job']) ? ' — '.$info['job'] : '');
                $nodes[$id] = [
                    'id' => $id,
                    'parent' => $orphansRoot,
                    'text' => $text,
                    'state' => ['opened' => false]
                ];
            }
        }

        return array_values($nodes);
    }

    public function get_structure_for_export()
    {
        $empMap = $this->get_emp_map();
        $rows   = $this->get_structure_rows();

        $out = [];
        foreach ($rows as $r) {
            $line = [];
            $line[] = $r['id'];

            for ($i=1; $i<=7; $i++) {
                $col = 'n'.$i;
                $eid = $this->clean($r[$col] ?? '');
                $line[] = $eid;

                $name = ($eid !== '' && isset($empMap[$eid])) ? ($empMap[$eid]['name'] ?? '') : '';
                $job  = ($eid !== '' && isset($empMap[$eid])) ? ($empMap[$eid]['job'] ?? '') : '';

                $line[] = $name;
                $line[] = $job;
            }

            $out[] = $line;
        }
        return $out;
    }

    public function search_possible_managers($q = '')
{
    $q = trim((string)$q);

    // نجيب من emp1 (المدراء ممكن يكونون أي موظف)
    $this->db->select('employee_id, subscriber_name, profession');
    $this->db->from('emp1');

    if ($q !== '') {
        $this->db->group_start()
                 ->like('subscriber_name', $q)
                 ->or_like('employee_id', $q)
                 ->or_like('profession', $q)
                 ->group_end();
    }

    $this->db->limit(30);
    $rows = $this->db->get()->result_array();

    $out = [];
    foreach ($rows as $r) {
        $id = (string)$r['employee_id'];
        $out[] = [
            'id' => $id,
            'text' => ($r['subscriber_name'] ?? '---') . ' — (' . $id . ')' . (!empty($r['profession']) ? ' — ' . $r['profession'] : '')
        ];
    }
    return $out;
}


/* =========================
   1) تقرير: موظف تحت أكثر من مدير
========================= */
public function get_multi_manager_employees()
{
    $sql = "
    SELECT x.employee_id,
           MAX(e.subscriber_name) AS subscriber_name,
           MAX(e.profession) AS profession,
           COUNT(DISTINCT x.parent_id) AS parents_count,
           GROUP_CONCAT(DISTINCT CONCAT(x.parent_id) ORDER BY x.parent_id SEPARATOR ',') AS parent_ids
    FROM (
      SELECT n1 AS employee_id, '#'  AS parent_id FROM {$this->table} WHERE n1 IS NOT NULL AND n1<>'' AND n1<>'0'
      UNION ALL SELECT n2, n1 FROM {$this->table} WHERE n2 IS NOT NULL AND n2<>'' AND n2<>'0'
      UNION ALL SELECT n3, n2 FROM {$this->table} WHERE n3 IS NOT NULL AND n3<>'' AND n3<>'0'
      UNION ALL SELECT n4, n3 FROM {$this->table} WHERE n4 IS NOT NULL AND n4<>'' AND n4<>'0'
      UNION ALL SELECT n5, n4 FROM {$this->table} WHERE n5 IS NOT NULL AND n5<>'' AND n5<>'0'
      UNION ALL SELECT n6, n5 FROM {$this->table} WHERE n6 IS NOT NULL AND n6<>'' AND n6<>'0'
      UNION ALL SELECT n7, n6 FROM {$this->table} WHERE n7 IS NOT NULL AND n7<>'' AND n7<>'0'
    ) x
    LEFT JOIN emp1 e ON e.employee_id = x.employee_id
    GROUP BY x.employee_id
    HAVING COUNT(DISTINCT x.parent_id) > 1
    ORDER BY parents_count DESC, x.employee_id ASC
    ";
    return $this->db->query($sql)->result_array();
}

/* يرجّع كل “ظهورات” الموظف: في أي مستوى وتحت أي parent */
public function get_employee_occurrences($employee_id)
{
    $employee_id = $this->clean($employee_id);
    if ($employee_id === '') return [];

    // نجيب الصفوف اللي يظهر فيها
    $rows = $this->get_any_rows_containing_employee($employee_id);
    if (empty($rows)) return [];

    $out = [];
    foreach ($rows as $r) {
        $path = [];
        $parent = '#';
        foreach ($this->cols as $idx => $c) {
            $v = $this->clean($r[$c] ?? '');
            if ($v === '') break;

            if ($v === $employee_id) {
                // parent هو اللي قبله
                $parent = ($idx === 0) ? '#' : $this->clean($r[$this->cols[$idx-1]] ?? '#');
                $out[] = [
                    'row_id' => (int)$r['id'],
                    'depth'  => $idx+1,
                    'parent_id' => ($parent === '' ? '#' : $parent),
                    'path'   => array_slice($path, 0, $idx), // مسار المدير
                    'full_path' => array_merge(array_slice($path, 0, $idx), [$employee_id])
                ];
                break;
            }

            $path[] = $v;
        }
    }

    // إزالة التكرار (لو نفس الظهور تكرر)
    $uniq = [];
    foreach ($out as $o) {
        $k = $o['parent_id'].'|'.implode('-', $o['full_path']);
        $uniq[$k] = $o;
    }
    return array_values($uniq);
}

/* =========================
   2) نقل محدد: من مدير معين إلى مدير معين فقط
   بشرط: لا يوجد تابعين تحت الموظف في الفرع القديم
========================= */
public function move_employee_specific($employee_id, $old_manager_id, $new_manager_id)
{
    $employee_id   = $this->clean($employee_id);
    $old_manager_id = $this->clean($old_manager_id);
    $new_manager_id = $this->clean($new_manager_id);

    if ($employee_id === '' || $employee_id === '__unlinked__') {
        return ['ok'=>false,'msg'=>'رقم الموظف غير صحيح'];
    }

    // 1) اعرف “الظهور” المطلوب تعديلُه (الفرع القديم) حسب old_manager_id
    $occ = null;
    $occurrences = $this->get_employee_occurrences($employee_id);
    foreach ($occurrences as $o) {
        // old_manager_id قد يكون '#'
        $p = ($o['parent_id'] === '' ? '#' : $o['parent_id']);
        $want = ($old_manager_id === '' ? '#' : $old_manager_id);
        if ($p === $want) { $occ = $o; break; }
    }
    if (!$occ) {
        return ['ok'=>false,'msg'=>'لم يتم العثور على هذا الموظف تحت المدير المحدد (الفرع القديم غير موجود)'];
    }

    $oldEmpPath = $occ['full_path'];     // prefix القديم (مدير + موظف)
    $oldDepth   = count($oldEmpPath);

    // 2) تحقق من الشرط: لا يوجد تابعين تحت الموظف في هذا الفرع
    // أي صف يبدأ بـ oldEmpPath ويكون عنده suffix بعده = تابعين
    $subtree = $this->get_rows_by_prefix($oldEmpPath);
    foreach ($subtree as $r) {
        $p = $this->normalize_path_from_row($r);
        if ($this->is_prefix($oldEmpPath, $p) && count($p) > $oldDepth) {
            return ['ok'=>false,'msg'=>'رفض: يوجد تابعين تحت الموظف في الفرع القديم، لا يمكن حذفه من هذا المكان'];
        }
    }

    // 3) مسار المدير الجديد
    $move_to_root = ($new_manager_id === '' || $new_manager_id === '#' || $new_manager_id === '0');
    $mgrPath = [];
    if (!$move_to_root) {
        $mgrPath = $this->find_best_path_for_employee($new_manager_id);
        if (!$mgrPath) {
            return ['ok'=>false,'msg'=>'المدير الجديد غير موجود في الهيكل'];
        }
        // منع حلقة
        if ($this->is_prefix($oldEmpPath, $mgrPath)) {
            return ['ok'=>false,'msg'=>'رفض: لا يمكن نقله تحت أحد تابعيه (حلقة)'];
        }
    }

    $newEmpPath = $move_to_root ? [$employee_id] : array_merge($mgrPath, [$employee_id]);
    if (count($newEmpPath) > 7) {
        return ['ok'=>false,'msg'=>'رفض: العمق الجديد يتجاوز 7 مستويات'];
    }

    // 4) نعدّل “فقط” الصف/الصفوف اللي تمثل هذا الظهور (بدون تابعين)
    $this->db->trans_begin();
    try {
        // نحدث كل صف يطابق prefix القديم ويكون طوله = oldDepth فقط
        foreach ($subtree as $r) {
            $p = $this->normalize_path_from_row($r);
            if ($this->is_prefix($oldEmpPath, $p) && count($p) == $oldDepth) {
                $upd = $this->path_to_cols($newEmpPath);
                $this->db->where('id', (int)$r['id'])->update($this->table, $upd);
            }
        }

        if ($this->db->trans_status() === FALSE) throw new Exception('DB Error');
        $this->db->trans_commit();

        return ['ok'=>true,'msg'=>'تم تعديل الربط لهذا المدير فقط بنجاح'];
    } catch (Exception $e) {
        $this->db->trans_rollback();
        return ['ok'=>false,'msg'=>'فشل الحفظ: '.$e->getMessage()];
    }
}

public function get_resigned_linked($q = '')
{
    // عدّل أسماء الأعمدة حسب جدولك:
    // emp1: id, name, emp_no, status, manager_id
    // الربط: manager_id يشير لموظف آخر داخل emp1 (مدير/مشرف)

    $params = [];
    $sql = "
        SELECT 
            e.id,
            CONCAT(e.name, ' — ', e.emp_no) AS text,
            e.manager_id,
            COALESCE(CONCAT(m.name, ' — ', m.emp_no), 'غير معروف') AS manager_text
        FROM emp1 e
        LEFT JOIN emp1 m ON m.id = e.manager_id
        WHERE e.status = 'resigned'
          AND e.manager_id IS NOT NULL
          AND e.manager_id <> ''
          AND e.manager_id <> '0'
    ";

    if($q !== ''){
        $sql .= " AND (e.name LIKE ? OR e.emp_no LIKE ? OR m.name LIKE ? OR m.emp_no LIKE ?)";
        $like = "%{$q}%";
        $params = [$like, $like, $like, $like];
    }

    $sql .= " ORDER BY e.name ASC LIMIT 500";
    return $this->db->query($sql, $params)->result_array();
}







}
