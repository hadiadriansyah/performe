<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Year_period extends Base_controller {
    
    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('master/Year_period_repository');
        $this->repository = new Year_period_repository();

        $this->is_logged_in();
        $this->is_admin_access();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Year Periods',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.css',
                '../vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.js',
                '../vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
                'admin/master/year-period/list.js',
                'admin/master/year-period/add-edit.js'
            ],
        ]);
        $this->template->load('admin/layout', 'admin/master/year_period/index', $data);
    }

    #####

    public function data_server() {
        $list = $this->repository->get_datatables();
        $data = $this->prepare_datatable($list);
        echo json_encode($this->format_output($data));
    }

    private function prepare_datatable($list) {
        $data = [];
        foreach ($list as $item) {
            $data[] = $this->format_row($item);
        }
        return $data;
    }

    private function format_row($item) {
        return [
            'id' => $item->id,
            'year_period' => $item->year_period,
            'status_appraisal' => $item->status_appraisal,
            'created_at' => format_date($item->created_at),
            'actions' => ''
        ];
    }

    private function format_output($data) {
        return [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->repository->count_all(),
            "recordsFiltered" => $this->repository->count_filtered(),
            "data" => $data,
        ];
    }

    #####

    public function store() {
        if (!$this->validate_input()) {
            $this->handle_validation_error();
            return;
        }

        $data = $this->collect_input_data(true);

        $exists = $this->repository->exists($data);
        if ($exists['is_exists']) {
            $this->json_response->error(
                'Data for the ' . $exists['data']->year_period . 
                ' year period is already available.'
            );
            return;
        }

        $this->save_data($data);
    }

    public function update() {
        $id = $this->input->post('id');

        if (!$id) {
            $this->json_response->error('ID is required.');
            return;
        }

        if (!$this->validate_input()) {
            $this->handle_validation_error();
            return;
        }
        $data = $this->collect_input_data(false, $id);

        $exists = $this->repository->exists($data);
        $unique = $this->repository->unique($data);
        
        if ($exists['is_exists'] && $unique['is_unique']) {
            $this->json_response->error(
                'Data for the ' . $exists['data']->year_period . 
                ' year period is already available.'
            );
            return;
        }

        $this->update_data($data);
    }

    public function delete() {
        $id = $this->input->post('id');
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
    
        $delete = $this->repository->delete($id);
        $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
        $this->json_response->{$delete ? 'success' : 'error'}($message, $delete);
    }

    public function delete_selected() {
        $ids = $this->input->post('ids');

        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
        
        $startTime = microtime(true);
    
        try {
            $delete = $this->repository->delete($id);
            $endTime = microtime(true);
            $timeTaken = ($endTime - $startTime) * 1000;
            $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
            $this->json_response->{$delete ? 'success' : 'error'}($message, ['time' => $timeTaken]);
        } catch (Exception $e) {
            $this->json_response->error('An error occurred while deleting data: ' . $e->getMessage());
        }
    }

    private function validate_input() {
        $this->form_validation->set_rules('year_period', 'Year Period', 'required|trim|min_length[4]|max_length[4]');
        $this->form_validation->set_rules('status_appraisal', 'Status Appraisal', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        return $this->form_validation->run();
    }

    private function collect_input_data($is_store = false, $id = null) {
        $input_data = [
            'year_period' => $this->input->post('year_period'),
            'status_appraisal' => $this->input->post('status_appraisal')
        ];
        if ($is_store) {
            if ($this->global['vendor_id']) {
                $input_data['created_by'] = $this->global['vendor_id'];
                $input_data['updated_by'] = $this->global['vendor_id'];
            }
        } else {
            $input_data['id'] = $id;
            if ($this->global['vendor_id']) {
                $input_data['updated_by'] = $this->global['vendor_id'];
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
            'year_period' => form_error('year_period'),
            'status_appraisal' => form_error('status_appraisal')
        ];
    }

    private function save_data($data) {
        $store = $this->repository->store($data);
        $message = $store ? 'Data Successfully saved.' : 'Failed to save data.';
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_data($data) {
        $update = $this->repository->update($data);
        $message = $update ? 'Data successfully updated.' : 'Failed to update data.';
        $this->json_response->{$update ? 'success' : 'error'}($message, $update);
    }

    #####

    public function get_by_id($id) {
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
        $data = $this->repository->get_by_id($id);
        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }
}