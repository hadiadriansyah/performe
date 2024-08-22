<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Approval_target extends Base_controller {

    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('approval/Approval_target_repository');
        $this->repository = new Approval_target_repository();

        $this->is_logged_in();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Approval Target',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.css',
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.js',
                'admin/approval/approval-target/approval-target.js',
                'admin/approval/approval-target/kpi-list.js',
                'admin/approval/approval-target/kpi-add.js',
                'admin/approval/approval-target/target-actual.js',
                'admin/approval/approval-target/submit.js',
            ],
        ]);
        $this->template->load('admin/layout', 'admin/approval/approval_target/index', $data);
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
    
        $submit = $this->repository->submit_kpi($input_data);
        $message = $submit ? 'Data successfully submitted.' : 'Failed to submit data.';
        $this->json_response->{$submit ? 'success' : 'error'}($message, $submit);
    }

    ##### End Store Update KPI

    ##### Add Edit Target

    public function add_edit_target() {
        $target_id = $this->input->post('target_id');
        if (!$this->validate_input_add_edit_target()) {
            $this->handle_validation_error_add_edit_target();
            return;
        }

        $data = $this->collect_input_add_edit_target();
        if (!$target_id) {
            $this->save_target($data);
        } else {
            $this->update_target($data);
        }
    }

    private function validate_input_add_edit_target() {
        return true;
    }

    private function handle_validation_error_add_edit_target() {
        $errors = $this->collect_form_errors_add_edit_target();
        $this->json_response->error('Failed to collect data.', $errors);
    }

    private function collect_form_errors_add_edit_target() {
        return [];
    }

    private function collect_input_add_edit_target() {
        $data_target = [
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
        
        $target = json_encode($data_target);

        $input_data = [
            'kpi_individual_id' => $this->input->post('kpi_individual_id'),
            'target' => $target
        ];

        if (empty($this->input->post('target_id'))) {
            if ($this->global['vendor_id']) {
                $input_data['created_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
                $input_data['updated_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
            }
        } else {
            $input_data['id'] = $this->input->post('target_id');
            if ($this->global['vendor_id']) {
                $input_data['updated_by'] = $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'];
            }
        }
        return $input_data;
    }

    private function save_target($data) {
        $store = $this->repository->store_target($data);
        $message = $store ? 'Data Successfully Saved.' : 'Failed to save data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_target($data) {
        $store = $this->repository->update_target($data);
        $message = $store ? 'Data Successfully Updated.' : 'Failed to update data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    ##### End Add Edit Target

    ##### Submit Target

    public function submit_target() {
        $id = $this->input->post('id');
        if (!isset($id)) {
            $this->json_response->error('ID Not found');
            return;
        }

        if (!$this->validate_input_submit_target()) {
            $this->handle_validation_error_submit_target();
            return;
        }

        $this->db->trans_start();

        $data_submit = $this->collect_input_submit_target($id);
        $data_hist = $this->collect_input_hist_approval_target($id);

        $this->repository->update_submit_target($data_submit);
        $this->repository->store_hist_approval_target($data_hist);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->json_response->error('Submit Target Failed');
        } else {
            $this->json_response->success('Submit Target Success');
        }

    }

    private function validate_input_submit_target() {
        $this->form_validation->set_rules('status', 'Status', 'required|trim');
        $this->form_validation->set_rules('comment', 'Comment', 'required|min_length[15]');
        $this->form_validation->set_error_delimiters('', '');
        return $this->form_validation->run();
    }

    private function collect_input_submit_target($id) {
        $status = $this->input->post('status');
        $comment = $this->input->post('comment');
        $puk_number = $this->input->post('puk_number');
        $number_of_puks = $this->input->post('number_of_puks');
        $input_data = [
            'id' => $id,
            'updated_by' => $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id']
        ];
        if ($puk_number == '1') {
            if ($number_of_puks <= 1) {
                if ($status == '2') {
                    $input_data['self_submit'] = 2;
                    $input_data['puk_2_status'] = 0;
                } else {
                    $input_data['self_submit'] = 0;
                    $input_data['puk_2_status'] = 0;
                }
            } else {
                if ($status == '2') {
                    $input_data['puk_2_status'] = 0;
                } else {
                    $input_data['self_submit'] = 0;
                    $input_data['puk_2_status'] = 0;
                }
            }
            $input_data['puk_1_status'] = $status;
            $input_data['puk_1_comment'] = $comment;
            $input_data['puk_1_submit_time'] = date('Y-m-d H:i:s');
        } else {
            if ($status == '2') {
                $input_data['self_submit'] = 2;
            } else {
                $input_data['puk_1_status'] = 0;
            }
            $input_data['puk_2_status'] = $status;
            $input_data['puk_2_comment'] = $comment;
            $input_data['puk_2_submit_time'] = date('Y-m-d H:i:s');
        }
        
        return $input_data;
    }

    private function collect_input_hist_approval_target($id) {
        $input_data = [
            'approval_target_id' => $id,
            'puk_number' => $this->input->post('puk_number'),
            'employee_id' => $this->input->post('employee_id'),
            'unit_id' => $this->input->post('unit_id'),
            'position_id' => $this->input->post('position_id'),
            'comment' => $this->input->post('comment'),
            'status' => $this->input->post('status'),
            'created_by' => $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id'],
            'updated_by' => $this->is_admin ? $this->global['vendor_id'] : $this->global['employee_id']
        ];
        
        return $input_data;
    }

    private function handle_validation_error_submit_target() {
        $errors = $this->collect_form_errors_submit_target();
        $this->json_response->error('Failed to save data.', $errors);
    }

    private function collect_form_errors_submit_target() {
        return [
            'status' => form_error('status'),
            'comment' => form_error('comment')
        ];
    }

    // private function save_submit_target($data) {
    //     $store = $this->repository->update_submit_target($data);
    //     $message = $store ? 'Data Successfully Submitted.' : 'Failed to submit data.';
        
    //     $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    // }

    // private function save_hist_approval_target($data) {
    //     $store = $this->repository->save_hist_approval_target($data);
    //     $message = $store ? 'Data Successfully Submitted.' : 'Failed to submit data.';
        
    //     $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    // }
    ##### End Submit Target

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

    #####

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

    public function get_approval_employees() {
        $year_period_id = $this->input->post('year_period_id');
        $unit_id = $this->input->post('unit_id');
        $placement_unit_id = $this->input->post('placement_unit_id');
        $position_id = $this->input->post('position_id');
        $data = $this->repository->get_approval_employees($year_period_id, $placement_unit_id, $position_id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_approval_by_approval_target() {
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

    public function get_target_by_id($id) {
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
        
        $data = $this->repository->get_target_by_id($id);

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_approval_target() {
        $data = $this->input->post();
        $pa_individual_id = $data['pa_individual_id'];
        $month_period = get_current_month();
        $approval = $this->repository->get_approval_target_by_pa_individual_id($pa_individual_id, $month_period);
        $message = $approval ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$approval ? 'success' : 'error'}($message, $approval);
    }

    public function get_hist_approval_target() {
        $approval_target_id = $this->input->post('approval_target_id');
        $approval = $this->repository->get_hist_approval_target($approval_target_id);
        $message = $approval ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$approval ? 'success' : 'error'}($message, $approval);
    }

    public function get_kpi_individual_by_pa_id($pa_id) {
        $kpi = $this->repository->get_kpi_individual_by_pa_id($pa_id);
        $message = $kpi ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$kpi ? 'success' : 'error'}($message, $kpi);
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

    public function get_kpi_individual_target_by_pa_individual_id_year_period_id() {
        $data = $this->input->post();
        $kpi = $this->repository->get_kpi_individual_target_by_pa_individual_id_year_period_id($data);
        $this->json_response->success('Data successfully fetched.', $kpi);
    }
}