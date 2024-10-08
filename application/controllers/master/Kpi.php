<?php
defined('BASEPATH') OR exit('No direct script access allowed');


require APPPATH . '/libraries/Base_controller.php';

class Kpi extends Base_controller {
    
    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('master/Kpi_repository');
        $this->repository = new Kpi_repository();

        $this->is_logged_in();
        $this->is_admin_access();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'KPIs',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.css'
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.js',
                'admin/master/kpi/kpi/list.js',
                'admin/master/kpi/kpi/add-edit.js'
            ],
        ]);
        $this->template->load('admin/layout', 'admin/master/kpi/index', $data);
    }

    #####

    public function data_server() {
        $list = $this->repository->get_datatables();
        $data = $this->prepare_datatable($list);
        echo json_encode($this->format_output($data));
    }

    private function prepare_datatable($list) {
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $data[] = $this->format_row($no, $item);
        }
        return $data;
    }

    private function format_row($no, $item) {
        return [
            'id' => $item->id,
            'kpi' => $item->kpi,
            'measurement' => $item->measurement,
            'counter' => $item->counter,
            'polarization' => $item->polarization,
            'description' => $item->description,
            'year_period' => $item->year_period,
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
                'Data for the ' . $exists['data']->kpi . 
                ' KPI and the ' . $exists['data']->year_period . 
                ' period is already available.'
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
                'Data for the ' . $exists['data']->kpi . 
                ' KPI and the ' . $exists['data']->year_period . 
                ' period is already available.'
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

    private function validate_input() {
        $this->form_validation->set_rules('kpi', 'KPI', 'required');
        $this->form_validation->set_rules('kpi_counter_id', 'KPI Counter', 'required');
        $this->form_validation->set_rules('measurement', 'Measurement', 'required');
        $this->form_validation->set_rules('kpi_polarization_id', 'KPI Polarization', 'required');
        $this->form_validation->set_rules('year_period_id', 'Year Period', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        return $this->form_validation->run();
    }

    private function collect_input_data($is_store = false, $id = null) {
        $input_data = [
            'kpi' => $this->input->post('kpi'),
            'kpi_counter_id' => $this->input->post('kpi_counter_id'),
            'measurement' => $this->input->post('measurement'),
            'kpi_polarization_id' => $this->input->post('kpi_polarization_id'),
            'description' => $this->input->post('description'),
            'year_period_id' => $this->input->post('year_period_id'),
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
            'kpi' => form_error('kpi'),
            'kpi_counter_id' => form_error('kpi_counter_id'),
            'measurement' => form_error('measurement'),
            'kpi_polarization_id' => form_error('kpi_polarization_id'),
            'year_period_id' => form_error('year_period_id'),
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

    ##### KPI COUNTER OPTIONS #####

    public function get_kpi_counter_options_by_year_period_id() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $yearPeriodId = $this->input->get('year_period_id');
        $result = $this->repository->get_kpi_counter_options_by_year_period_id($search, $page, $yearPeriodId);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->counter], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    ##### KPI POLARIZATION OPTIONS #####

    public function get_kpi_polarization_options_by_year_period_id() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $yearPeriodId = $this->input->get('year_period_id');
        $result = $this->repository->get_kpi_polarization_options_by_year_period_id($search, $page, $yearPeriodId);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->polarization], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    ##### YEAR PERIOD OPTIONS #####

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