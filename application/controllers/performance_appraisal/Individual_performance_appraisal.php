<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Individual_performance_appraisal extends Base_controller {

    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('performance_appraisal/Individual_performance_appraisal_repository');
        $this->repository = new Individual_performance_appraisal_repository();

        $this->is_logged_in();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Individual Performance Appraisal',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.css',
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.js',
                'admin/performance-appraisal/individual-performance-appraisal/individual-performance-appraisal.js',
                'admin/performance-appraisal/individual-performance-appraisal/list.js',
                'admin/performance-appraisal/individual-performance-appraisal/target-actual.js',
                'admin/performance-appraisal/individual-performance-appraisal/submit.js',
            ],
        ]);
        $this->template->load('admin/layout', 'admin/performance_appraisal/individual_performance_appraisal/index', $data);
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

    ##### Store Update KPI

    public function store_update_kpi() {
        $mode = $this->input->post('mode');
        if (!$this->validate_input_store_update_kpi()) {
            $this->handle_validation_error_store_update_kpi();
            return;
        }

        $data = $this->collect_input_store_update_kpi();
        if ($mode == 'add') {
            $exists = $this->repository->exists_kpi($data);
            
            if ($exists['is_exists']) {
                $this->json_response->error(
                    'Data for the ' . $exists['data']->kpi . 
                    ' KPI is already in use.'
                );
                return;
            }
            $this->save_kpi($data);
        } else {
            $exists = $this->repository->exists_kpi($data);
            $unique = $this->repository->unique_kpi($data);
            
            if ($exists['is_exists'] && $unique['is_unique']) {
                $this->json_response->error(
                    'Data for the ' . $exists['data']->kpi . 
                    ' KPI is already in use.'
                );
                return;
            }
            $this->update_kpi($data);
        }
    }

    private function validate_input_store_update_kpi() {
        $this->form_validation->set_rules('kpi_id', 'KPI', 'required|trim');
        $this->form_validation->set_rules('weight', 'Weight', 'required|trim');
        return $this->form_validation->run();
    }

    private function handle_validation_error_store_update_kpi() {
        $errors = $this->collect_form_errors_store_update_kpi();
        $this->json_response->error('Failed to collect data.', $errors);
    }

    private function collect_form_errors_store_update_kpi() {
        return [
            'kpi_id' => form_error('kpi_id'),
            'weight' => form_error('weight')
        ];
    }

    private function collect_input_store_update_kpi() {
        $input_data = [
            'id' => $this->input->post('id'),
            'year_period_id' => $this->input->post('year_period_id'),
            'pa_individual_id' => $this->input->post('pa_individual_id'),
            'kpi_id' => $this->input->post('kpi_id'),
            'weight' => $this->input->post('weight'),
        ];

        return $input_data;
    }

    private function save_kpi($data) {
        $store = $this->repository->store_kpi($data);
        $message = $store ? 'Data Successfully Saved.' : 'Failed to save data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_kpi($data) {
        $store = $this->repository->update_kpi($data);
        $message = $store ? 'Data Successfully Updated.' : 'Failed to update data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    public function delete_kpi() {
        $id = $this->input->post('id');
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
    
        $delete = $this->repository->delete_kpi($id);
        $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
        $this->json_response->{$delete ? 'success' : 'error'}($message, $delete);
    }

    public function submit_kpi() {
        $input_data = [
            'pa_individual_id' => $this->input->post('pa_individual_id'),
            'year_period_id' => $this->input->post('year_period_id'),
            'is_submit' => $this->input->post('is_submit'),
        ];
        if (!isset($input_data['pa_individual_id']) || !isset($input_data['year_period_id'])) {
            $this->json_response->error('Unit or Year Period is required.');
            return;
        }
    
        $delete = $this->repository->submit_kpi($input_data);
        $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
        $this->json_response->{$delete ? 'success' : 'error'}($message, $delete);
    }

    ##### End Store Update KPI

    ##### Add Edit Actual

    public function add_edit_actual() {
        $actual_id = $this->input->post('actual_id');
        if (!$this->validate_input_add_edit_actual()) {
            $this->handle_validation_error_add_edit_actual();
            return;
        }

        $data = $this->collect_input_add_edit_actual();
        if (!$actual_id) {
            $this->save_actual($data);
        } else {
            $this->update_actual($data);
        }
    }

    private function validate_input_add_edit_actual() {
        return true;
    }

    private function handle_validation_error_add_edit_actual() {
        $errors = $this->collect_form_errors_add_edit_actual();
        $this->json_response->error('Failed to collect data.', $errors);
    }

    private function collect_form_errors_add_edit_actual() {
        return [];
    }

    private function collect_input_add_edit_actual() {
        $data_actual = [
            '1' => $this->input->post('month_1'),
            '2' => $this->input->post('month_2'),
            '3' => $this->input->post('month_3'),
            '4' => $this->input->post('month_4'),
            '5' => $this->input->post('month_5'),
            '6' => $this->input->post('month_6'),
            '7' => $this->input->post('month_7'),
            '8' => $this->input->post('month_8'),
            '9' => $this->input->post('month_9'),
            '10' => $this->input->post('month_10'),
            '11' => $this->input->post('month_11'),
            '12' => $this->input->post('month_12')
        ];
        
        $actual = json_encode($data_actual);

        $input_data = [
            'kpi_individual_id' => $this->input->post('kpi_individual_id'),
            'actual' => $actual
        ];

        if (empty($this->input->post('actual_id'))) {
            if ($this->global['vendor_id']) {
                $input_data['created_by'] = $this->global['vendor_id'];
                $input_data['updated_by'] = $this->global['vendor_id'];
            }
        } else {
            $input_data['id'] = $this->input->post('actual_id');
            if ($this->global['vendor_id']) {
                $input_data['updated_by'] = $this->global['vendor_id'];
            }
        }
        return $input_data;
    }

    private function save_actual($data) {
        $store = $this->repository->store_actual($data);
        $message = $store ? 'Data Successfully Saved.' : 'Failed to save data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_actual($data) {
        $store = $this->repository->update_actual($data);
        $message = $store ? 'Data Successfully Updated.' : 'Failed to update data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    ##### End Add Edit Actual

    ##### Submit PA

    public function submit_pa() {
        $pa_individual_id = $this->input->post('pa_individual_id');
        $term_and_conditions = $this->input->post('term_and_conditions');
        $id = $this->input->post('id');

        if (!$pa_individual_id) {
            return $this->json_response->error('PA Individual ID is required.');
        }

        if (!$term_and_conditions) {
            return $this->json_response->error('Term and Condition is required.');
        }

        $data = [
            'self_submit' => 1,
            'puk_1_status' => 0,
            'puk_2_status' => 0,
            'month_period' => get_current_month(),
            'month_periods' => json_encode([get_current_month()]),
            'self_submit_time' => date('Y-m-d H:i:s')
        ];

        if (!empty($id)) {
            $data['id'] = $id;
            $this->update_submit_pa($data);
        } else {
            $data = array_merge($data, [
                'pa_individual_id' => $pa_individual_id,
                'term_and_conditions' => $term_and_conditions,
                'puk_1_status' => 0,
                'puk_2_status' => 0,
                'puk_1_unit' => $this->input->post('unit_approval_1_id') ?: null,
                'puk_1_position' => $this->input->post('position_approval_1_id') ?: null,
                'puk_2_unit' => $this->input->post('unit_approval_2_id') ?: null,
                'puk_2_position' => $this->input->post('position_approval_2_id') ?: null,
                'created_by' => $this->global['vendor_id'],
                'updated_by' => $this->global['vendor_id']
            ]);

            if ($this->repository->exists_submit_pa($data)['is_exists']) {
                return $this->json_response->error('Data is already submitted.');
            }

            $this->save_submit_pa($data);
        }
    }

    private function save_submit_pa($data) {
        $store = $this->repository->store_submit_pa($data);
        $message = $store ? 'Data Successfully Submitted.' : 'Failed to submit data.';
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_submit_pa($data) {
        $store = $this->repository->update_submit_pa($data);
        $message = $store ? 'Data Successfully Submitted.' : 'Failed to submit data.';
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    ##### End Submit PA

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

    public function get_kpi_options_by_year_period_id() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $year_period_id = $this->input->get('year_period_id');
        $result = $this->repository->get_kpi_options_by_year_period_id($search, $page, $year_period_id);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->kpi], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
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

    public function get_goals_settings() {
        $year_period_id = $this->input->post('year_period_id');
        $employee_id = $this->input->post('employee_id');
        $data = $this->repository->get_pa_individual($year_period_id, $employee_id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_kpi_individual_by_pa_id($pa_id) {
        $kpi = $this->repository->get_kpi_individual_by_pa_id($pa_id);
        $message = $kpi ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$kpi ? 'success' : 'error'}($message, $kpi);
    }

    public function get_kpi_individual_pa_by_pa_individual_id_year_period_id() {
        $data = $this->input->post();
        $kpi = $this->repository->get_kpi_individual_pa_by_pa_individual_id_year_period_id($data);
        $this->json_response->success('Data successfully fetched.', $kpi);
    }

    public function get_actual_by_id($id) {
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
        
        $data = $this->repository->get_actual_by_id($id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_target_by_id($id) {
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
        
        $data = $this->repository->get_target_by_id($id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    

    public function get_approval_by_goals_settings() {
        $data = $this->input->post();
        $unit_id = $data['placement_unit_id'];
        $position_id = $data['position_id'];

        $approval_1 = $this->get_approval_data($unit_id, $position_id, 'unit_approval_1_id','position_approval_1_id', 'position_approval_1_name', 'approval_1_name', 'approval_1_npp', 'unit_approval_1_name');
        $approval_2 = $this->get_approval_data($unit_id, $position_id, 'unit_approval_2_id', 'position_approval_2_id', 'position_approval_2_name', 'approval_2_name', 'approval_2_npp', 'unit_approval_2_name');

        $approval = [
            'approval_1' => $approval_1,
            'approval_2' => $approval_2
        ];

        $this->json_response->success('Data successfully fetched.', $approval);
    }

    private function get_approval_data($unit_id, $position_id, $approval_unit_id_field, $approval_position_id_field, $position_name_field, $name_field, $npp_field, $unit_name_field) {
        $approval_histories = [
            $this->repository->get_approval_by_unit_id_position_id_in_hist_pelaksana_jabatan($unit_id, $position_id),
            $this->repository->get_approval_by_unit_id_position_id_in_hist_ndpenugasan($unit_id, $position_id),
            $this->repository->get_approval_by_unit_id_position_id_in_hist_jabatan($unit_id, $position_id)
        ];

        $approval_data = [];

        foreach ($approval_histories as $approval_history) {
            foreach ($approval_history as $item) {
                if ($item->{$approval_unit_id_field} && $item->{$approval_position_id_field}) {
                    $approval_data[] = [
                        $approval_unit_id_field => $item->{$approval_unit_id_field},
                        $approval_position_id_field => $item->{$approval_position_id_field},
                        $position_name_field => $item->{$position_name_field},
                        $name_field => $item->{$name_field},
                        $npp_field => $item->{$npp_field},
                        $unit_name_field => $item->{$unit_name_field}
                    ];
                }
            }
            if (!empty($approval_data)) {
                break;
            }
        }

        return $approval_data;
    }

    public function get_approval_pa() {
        $data = $this->input->post();
        $pa_individual_id = $data['pa_individual_id'];
        $month_period = get_current_month();
        $approval = $this->repository->get_approval_pa_by_pa_individual_id($pa_individual_id, $month_period);
        $message = $approval ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$approval ? 'success' : 'error'}($message, $approval);
    }

    public function get_kpi_by_id($id = '') {
        if (!$id) {
            $this->json_response->error('ID is required.');
            return;
        }
        
        $data = $this->repository->get_kpi_by_id($id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_index_scores() {
        $year_period_id = $this->input->post('year_period_id');
        $index_score = $this->repository->get_index_scores($year_period_id);
        echo json_encode($index_score);
    }
    
}