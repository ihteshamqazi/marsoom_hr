<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Annual_evaluation_criteria_admin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function clamp($v, $min, $max)
    {
        $v = (float)$v;
        if ($v < $min) $v = $min;
        if ($v > $max) $v = $max;
        return $v;
    }

    public function get_forms()
    {
        return $this->db
            ->order_by('form_type', 'ASC')
            ->get_where('annual_eval_forms', ['is_active' => 1])
            ->result_array();
    }

    public function get_criteria_with_parts($form_type)
    {
        $criteria = $this->db
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get_where('annual_eval_criteria', [
                'form_type' => (int)$form_type,
                'is_active' => 1
            ])->result_array();

        if (empty($criteria)) return [];

        $ids = array_column($criteria, 'id');

        $parts = [];
        if (!empty($ids)) {
            $partsRows = $this->db
                ->where_in('criterion_id', $ids)
                ->where('is_active', 1)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('annual_eval_criteria_parts')
                ->result_array();

            foreach ($partsRows as $p) {
                $parts[$p['criterion_id']][] = $p;
            }
        }

        foreach ($criteria as &$c) {
            $c['parts'] = $parts[$c['id']] ?? [];
        }
        unset($c);

        return $criteria;
    }

    public function save_criteria_bundle($form_type, $criteria)
    {
        $form_type = (int)$form_type;

        $this->db->trans_begin();

        try {
            foreach ($criteria as $criterion_id => $row) {
                $criterion_id = (int)$criterion_id;
                if ($criterion_id <= 0) continue;

                $critData = [
                    'criterion_name'   => trim((string)($row['criterion_name'] ?? '')),
                    'criterion_key'    => trim((string)($row['criterion_key'] ?? '')),
                    'score_column'     => trim((string)($row['score_column'] ?? '')),
                    'criterion_desc'   => trim((string)($row['criterion_desc'] ?? '')),
                    'max_score'        => $this->clamp($row['max_score'] ?? 0, 0, 999),
                    'sort_order'       => (int)($row['sort_order'] ?? 0),
                    'is_readonly'      => !empty($row['is_readonly']) ? 1 : 0,
                    'min_from_courses' => !empty($row['min_from_courses']) ? 1 : 0,
                    'updated_at'       => date('Y-m-d H:i:s'),
                ];

                if ($critData['criterion_key'] === '') {
                    $critData['criterion_key'] = 'criterion_' . $criterion_id;
                }

                if ($critData['score_column'] === '') {
                    $critData['score_column'] = $critData['criterion_key'];
                }

                $this->db->where('id', $criterion_id)
                         ->where('form_type', $form_type)
                         ->update('annual_eval_criteria', $critData);

                $parts = $row['parts'] ?? [];
                if (is_array($parts)) {
                    foreach ($parts as $part_id => $p) {
                        $part_id = (int)$part_id;
                        if ($part_id <= 0) continue;

                        $note_rule = trim((string)($p['note_rule'] ?? 'required_on_less'));
if (!in_array($note_rule, ['optional','required_on_less','required_on_positive'], true)) {
    $note_rule = 'required_on_less';
}

$partData = [
    'part_key'    => trim((string)($p['part_key'] ?? '')),
    'part_name'   => trim((string)($p['part_name'] ?? '')),
    'max_score'   => $this->clamp($p['max_score'] ?? 0, 0, 999),
    'note_rule'   => $note_rule,
    'sort_order'  => (int)($p['sort_order'] ?? 0),
    'updated_at'  => date('Y-m-d H:i:s'),
];

                        if ($partData['part_key'] === '') {
                            $partData['part_key'] = 'part_' . $part_id;
                        }

                        $this->db->where('id', $part_id)
                                 ->where('criterion_id', $criterion_id)
                                 ->update('annual_eval_criteria_parts', $partData);
                    }
                }
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('DB transaction failed');
            }

            $this->db->trans_commit();
            return ['ok' => true, 'msg' => 'تم حفظ جميع التعديلات بنجاح.'];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['ok' => false, 'msg' => 'حدث خطأ أثناء حفظ التعديلات.'];
        }
    }

    public function create_empty_criterion($form_type)
    {
        $form_type = (int)$form_type;

        $maxSort = $this->db->select_max('sort_order')
            ->get_where('annual_eval_criteria', ['form_type' => $form_type])
            ->row_array();

        $sort = (int)($maxSort['sort_order'] ?? 0) + 1;

        $data = [
            'form_type'         => $form_type,
            'criterion_key'     => 'new_criterion_' . time(),
            'score_column'      => 'new_score_' . time(),
            'criterion_name'    => 'معيار جديد',
            'criterion_desc'    => '',
            'max_score'         => 0,
            'is_readonly'       => 0,
            'min_from_courses'  => 0,
            'sort_order'        => $sort,
            'is_active'         => 1,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('annual_eval_criteria', $data);
        return $this->db->insert_id();
    }

    public function delete_criterion($id)
    {
        $id = (int)$id;
        if ($id <= 0) return false;

        $this->db->where('id', $id)->delete('annual_eval_criteria');
        return true;
    }

    public function create_empty_part($criterion_id)
    {
        $criterion_id = (int)$criterion_id;
        if ($criterion_id <= 0) return 0;

        $maxSort = $this->db->select_max('sort_order')
            ->get_where('annual_eval_criteria_parts', ['criterion_id' => $criterion_id])
            ->row_array();

        $sort = (int)($maxSort['sort_order'] ?? 0) + 1;

        $data = [
    'criterion_id' => $criterion_id,
    'part_key'     => 'new_part_' . time(),
    'part_name'    => 'بند جديد',
    'max_score'    => 0,
    'note_rule'    => 'required_on_less',
    'sort_order'   => $sort,
    'is_active'    => 1,
    'created_at'   => date('Y-m-d H:i:s'),
    'updated_at'   => date('Y-m-d H:i:s'),
];

        $this->db->insert('annual_eval_criteria_parts', $data);
        return $this->db->insert_id();
    }

    public function delete_part($id)
    {
        $id = (int)$id;
        if ($id <= 0) return false;

        $this->db->where('id', $id)->delete('annual_eval_criteria_parts');
        return true;
    }
}