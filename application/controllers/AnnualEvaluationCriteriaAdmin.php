<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnnualEvaluationCriteriaAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('users/login');
            return;
        }

        $this->load->helper(['url', 'form', 'security']);
        $this->load->library(['session']);
        $this->load->model('Annual_evaluation_model', 'ev');
        $this->load->model('Annual_evaluation_criteria_admin_model', 'cam');

        date_default_timezone_set('Asia/Riyadh');

        if (!$this->ev->is_admin()) {
            show_error('غير مصرح لك.', 403);
            return;
        }
    }

    public function index()
    {
        $form_type = (int)$this->input->get('form_type', true);
        if ($form_type <= 0) $form_type = 1;

        $forms = $this->cam->get_forms();
        $criteria = $this->cam->get_criteria_with_parts($form_type);

        $data = [
            'title'     => 'إدارة معايير التقييم الديناميكية',
            'form_type' => $form_type,
            'forms'     => $forms,
            'criteria'  => $criteria,
            'flash'     => $this->session->flashdata('msg'),
        ];

        $this->load->view('template/new_header_and_sidebar', $data ?? []);
        $this->load->view('annual_eval/criteria_admin', $data);
        $this->load->view('template/new_footer');
    }

    public function save()
    {
        $form_type = (int)$this->input->post('form_type', true);
        if ($form_type <= 0) $form_type = 1;

        $criteria = $this->input->post('criteria', false);
        if (!is_array($criteria)) $criteria = [];

        $res = $this->cam->save_criteria_bundle($form_type, $criteria);

        $this->session->set_flashdata('msg', $res['msg']);
        redirect('AnnualEvaluationCriteriaAdmin?form_type=' . $form_type);
    }

    public function add_criterion()
    {
        $form_type = (int)$this->input->get('form_type', true);
        if ($form_type <= 0) $form_type = 1;

        $this->cam->create_empty_criterion($form_type);

        $this->session->set_flashdata('msg', 'تمت إضافة معيار جديد.');
        redirect('AnnualEvaluationCriteriaAdmin?form_type=' . $form_type);
    }

    public function delete_criterion($id = 0)
    {
        $id = (int)$id;
        $form_type = (int)$this->input->get('form_type', true);
        if ($form_type <= 0) $form_type = 1;

        if ($id > 0) {
            $this->cam->delete_criterion($id);
            $this->session->set_flashdata('msg', 'تم حذف المعيار بنجاح.');
        }

        redirect('AnnualEvaluationCriteriaAdmin?form_type=' . $form_type);
    }

    public function add_part($criterion_id = 0)
    {
        $criterion_id = (int)$criterion_id;
        $form_type = (int)$this->input->get('form_type', true);
        if ($form_type <= 0) $form_type = 1;

        if ($criterion_id > 0) {
            $this->cam->create_empty_part($criterion_id);
            $this->session->set_flashdata('msg', 'تمت إضافة بند فرعي جديد.');
        }

        redirect('AnnualEvaluationCriteriaAdmin?form_type=' . $form_type);
    }

    public function delete_part($id = 0)
    {
        $id = (int)$id;
        $form_type = (int)$this->input->get('form_type', true);
        if ($form_type <= 0) $form_type = 1;

        if ($id > 0) {
            $this->cam->delete_part($id);
            $this->session->set_flashdata('msg', 'تم حذف البند الفرعي بنجاح.');
        }

        redirect('AnnualEvaluationCriteriaAdmin?form_type=' . $form_type);
    }
}