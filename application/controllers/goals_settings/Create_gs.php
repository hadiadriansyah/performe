<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Create_gs extends Base_controller {

    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('goals_settings/Create_gs_repository');
        $this->repository = new Create_gs_repository();
        
        $this->is_logged_in();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Create Goals Setting',
            'js' => [
                'admin/goals-settings/create-gs/create-gs.js',
            ],
        ]);
        $this->template->load('admin/layout', 'admin/goals_settings/create_gs/index', $data);
    }
    
    public function get_employee() {
        $data = $this->global;

        $employee = false;

        if ($data['employee_id']) {
            $employee = $this->repository->get_employee_by_employee_id($data['employee_id']);
        }

        $message = $employee ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$employee ? 'success' : 'error'}($message, $employee);
    }

    public function create_gs() {
        if (!$this->validate_input()) {
            $this->handle_validation_error();
            return;
        }
        $data_pa = $this->collect_input_data(true);
        $data_hist_pa = array_diff_key($data_pa, array_flip(['position_id', 'unit_id', 'placement_unit_id', 'from_month', 'to_month', 'status_appraisal']));
        
        $exists = $this->repository->exists($data_hist_pa);
        if ($exists['is_exists']) {
            $this->json_response->error(
                'Data for the ' . $exists['data']->employee_name . 
                ' employee and the ' . $exists['data']->year_period . 
                ' period is already create goals setting.'
            );
            return;
        }

        $this->db->trans_begin();
    
        try {
            $this->repository->store_pa($data_pa);
            $this->repository->store_hist_pa($data_hist_pa);
    
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->json_response->error('Failed to save data.');
            } else {
                $this->db->trans_commit();
                $this->json_response->success('Data successfully saved.', null);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->json_response->error('Failed to save data.', ['error' => $e->getMessage()]);
        }
    }

    public function get_position_unit_placement_unit_by_employee_id($employee_id) {
        $data = $this->get_position_unit_placement_unit_employee($employee_id);
        $position = $this->repository->get_position_by_id($data['position_id']);
        $unit = $this->repository->get_unit_by_id($data['unit_id']);
        $placement_unit = $this->repository->get_unit_by_id($data['placement_unit_id']);
        $this->json_response->success('Data successfully fetched.', [
            'npp' => $data['npp'],
            'position' => $position,
            'unit' => $unit,
            'placement_unit' => $placement_unit,
            'description' => $data['description']
        ]);
    }

    private function get_position_unit_placement_unit_employee($employee_id) {
        $npp = NULL;
        $position_id = NULL;
        $unit_id = NULL;
        $placement_unit_id = NULL;
        $description = NULL;

        $temp_position_hist = $this->repository->get_temp_position_hist_by_employee_id($employee_id);

        if ($temp_position_hist) {
            $npp = $temp_position_hist->nrik;
            $position_id = $temp_position_hist->id_jabatan_diganti;
            $unit_id = $temp_position_hist->id_unit_kerja;
            $placement_unit_id = $temp_position_hist->ditempatkan_diganti;
            $description = 'Pelaksana Jabatan s/d ' . date('d F Y', strtotime($temp_position_hist->tgl_selesai));
        } else if (($temp_assignment_hist = $this->repository->get_temp_assignment_hist_by_employee_id($employee_id))) {
            $npp = $temp_assignment_hist->nrik;
            $position_id = $temp_assignment_hist->tmp_jabatan;
            $unit_id = $temp_assignment_hist->id_uker_penempatan;
            $placement_unit_id = $temp_assignment_hist->id_uker_ndpenugasan;
            $description = 'Nota Dinas Penugasan s/d ' . date('d F Y', strtotime($temp_assignment_hist->tgl_selesai));
        } else if (($position_hist = $this->repository->get_position_hist_by_employee_id($employee_id))) {
            $npp = $position_hist->nrik;
            $position_id = $position_hist->id_jabatan;
            $unit_id = $position_hist->id_unit_kerja;
            $placement_unit_id = $position_hist->ditempatkan_di;
        }

        return [
            'npp' => $npp,
            'position_id' => $position_id,
            'unit_id' => $unit_id,
            'placement_unit_id' => $placement_unit_id,
            'description' => $description
        ];
    }


    private function validate_input() {
        $this->form_validation->set_rules('year_period_id', 'Year Period', 'required|trim');
        $this->form_validation->set_rules('employee_id', 'Employee', 'required|trim');
        $this->form_validation->set_rules('position_id', 'Position', 'required|trim');
        $this->form_validation->set_rules('unit_id', 'Unit', 'required|trim');
        $this->form_validation->set_rules('placement_unit_id', 'Placement Unit', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        return $this->form_validation->run();
    }

    private function collect_input_data($is_store = false, $id = null) {
        $input_data = [
            'year_period_id' => $this->input->post('year_period_id'),
            'employee_id' => $this->input->post('employee_id'),
            'position_id' => $this->input->post('position_id'),
            'unit_id' => $this->input->post('unit_id'),
            'placement_unit_id' => $this->input->post('placement_unit_id'),
            'from_month' => 1,
            'to_month' => 12,
            'status_appraisal' => 1,
        ];

        if ($is_store) {
            if ($this->global['vendor_id']) {
                $input_data['created_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
                $input_data['updated_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
            }
        } else {
            $input_data['id'] = $id;
            if ($this->global['vendor_id']) {
                $input_data['updated_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
            }
        }

        return $input_data;
    }

    private function handle_validation_error() {
        $errors = $this->collect_form_errors();
        $this->json_response->error('Failed to save data.', $errors);
    }

    private function collect_form_errors() {
        return [
            'year_period_id' => form_error('year_period_id'),
            'employee_id' => form_error('employee_id'),
            'position_id' => form_error('position_id'),
            'unit_id' => form_error('unit_id'),
            'placement_unit_id' => form_error('placement_unit_id'),
        ];
    }

    public function get_employee_options() {
        $data = $this->global;
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        if ($data['employee_id']) {
            $individual = $this->repository->get_employee_options_by_employee_id($search, $page, $data['employee_id']);
        } else {
            $individual = $this->repository->get_employee_options($search, $page);
        }
        $result = $individual;
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id_peg, 'text' => $item->nama], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_year_period_options() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $result = $this->repository->get_year_period_options($search, $page);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->year_period], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }
}