<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Approval extends Base_controller {
    
    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('setting/Approval_repository');
        $this->repository = new Approval_repository();

        $this->is_logged_in();
        $this->is_admin_access();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Approval',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.css',
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.js',
                'admin/setting/approval/list.js',
                'admin/setting/approval/add-edit.js'
            ],
        ]);
        $this->template->load('admin/layout', 'admin/setting/approval/index', $data);
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
            'no' => $no,
            'temp_id' => $item->temp_id,
            'temp_name' => $item->temp_name,
            'position_id' => $item->position_id,
            'position_name' => $item->position_name,
            'unit_approval_1_id' => $item->unit_approval_1_id,
            'unit_approval_1_name' => $item->unit_approval_1_name,
            'position_approval_1_ids' => $item->position_approval_1_ids,
            'position_approval_1_names' => $item->position_approval_1_names,
            'unit_approval_2_id' => $item->unit_approval_2_id,
            'unit_approval_2_name' => $item->unit_approval_2_name,
            'position_approval_2_ids' => $item->position_approval_2_ids,
            'position_approval_2_names' => $item->position_approval_2_names,
            'unit_type_1' => $item->unit_type_1,
            'unit_type_1_name' => $item->unit_type_1_name,
            'unit_type_2' => $item->unit_type_2,
            'unit_type_2_name' => $item->unit_type_2_name,
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

    ##### Add Edit Approval

    public function add_edit_approval() {
        if (!$this->validate_input_add_edit_approval()) {
            $this->handle_validation_error_add_edit_approval();
            return;
        }

        $data = $this->collect_input_data_add_edit_approval();
        $exists = $this->repository->exists($data);
        if ($exists['is_exists']) {
            $this->update_approval($data);
        } else {
            $this->save_approval($data);
        }
    }

    private function validate_input_add_edit_approval() {
        $this->form_validation->set_rules('type', 'Type', 'required|trim');
        $this->form_validation->set_rules('unit_id', 'Unit', 'required|trim');
        $this->form_validation->set_rules('position_id', 'Position', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        return $this->form_validation->run();
    }

    private function handle_validation_error_add_edit_approval() {
        $errors = $this->collect_form_errors_add_edit_approval();
        $this->json_response->error('Failed to collect data.', $errors);
    }

    private function collect_form_errors_add_edit_approval() {
        return [
            'type' => form_error('type'),
            'unit_id' => form_error('unit_id'),
            'position_id' => form_error('position_id'),
        ];
    }

    private function collect_input_data_add_edit_approval() {
        $type = $this->input->post('type');
        $unit_id = $this->input->post('unit_id');
        $position_id = $this->input->post('position_id');
        $approval_unit_type_1 = $this->input->post('approval_unit_type_1');
        $custom_approval_unit_1 = json_decode($this->input->post('custom_approval_unit_1'), true);
        $position_approval_1 = json_decode($this->input->post('position_approval_1'), true);
        $approval_unit_type_2 = $this->input->post('approval_unit_type_2');
        $custom_approval_unit_2 = json_decode($this->input->post('custom_approval_unit_2'), true);
        $position_approval_2 = json_decode($this->input->post('position_approval_2'), true);
        
        $unit_approval_1_id = null;
        $position_approval_1_id = null;
        $custom_unit_1 = null;
        
        $unit_approval_2_id = null;
        $position_approval_2_id = null;
        $custom_unit_2 = null;

        if ($approval_unit_type_1 != '0') {
            $position_approval_1_id = !empty($position_approval_1) ? '[' . implode(',', array_map(fn($item) => $item['value'], $position_approval_1)) . ']' : null;
            if ($approval_unit_type_1 == '3') {
                $unit_approval_1_id = $custom_approval_unit_1;
                $custom_unit_1 = 1;
            } else if ($approval_unit_type_1 == '1') {
                $unit_approval_1_id = $unit_id;
                $custom_unit_1 = 0;
            } else {
                $unit_approval_1_id = $this->get_unit_parent($unit_id);
                $custom_unit_1 = 0;
            }
        }

        if ($approval_unit_type_2 != '0') {
            $position_approval_2_id = !empty($position_approval_2) ? '[' . implode(',', array_map(fn($item) => $item['value'], $position_approval_2)) . ']' : null;

            if ($approval_unit_type_2 == '3') {
                $unit_approval_2_id = $custom_approval_unit_2;
                $custom_unit_2 = 1;
            } else if ($approval_unit_type_2 == '1') {
                $unit_approval_2_id = $unit_id;
                $custom_unit_2 = 0;
            } else {
                $unit_approval_2_id = $this->get_unit_parent($unit_id);
                $custom_unit_2 = 0;
            }
        }

        $input_data = [
            'unit_id' => $unit_id,
            'position_id' => $position_id,
            'unit_approval_1_id' => $unit_approval_1_id,
            'position_approval_1_id' => $position_approval_1_id,
            'custom_unit_1' => $custom_unit_1,
            'unit_approval_2_id' => $unit_approval_2_id,
            'position_approval_2_id' => $position_approval_2_id,
            'custom_unit_2' => $custom_unit_2,
        ];


        return $input_data;
    }

    private function get_unit_parent($unit_id) {
        $unit = $this->repository->get_unit_by_id($unit_id);
        if ($unit) {
            return $unit['id_parent'];
        }
        return null;
    }

    private function save_approval($data) {
        $store = $this->repository->store_approval($data);
        $message = $store ? 'Data Successfully Saved.' : 'Failed to save data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    private function update_approval($data) {
        $store = $this->repository->update_approval($data);
        $message = $store ? 'Data Successfully Updated.' : 'Failed to update data.';
        
        $this->json_response->{$store ? 'success' : 'error'}($message, $store);
    }

    public function delete_unit_approval() {
        $unit_id = $this->input->post('unit_id');
        $position_id = $this->input->post('position_id');
        if (!isset($unit_id) || !isset($position_id)) {
            $this->json_response->error('Unit and Position is not available.');
            return;
        }
        
        $delete = $this->repository->delete_unit_approval($unit_id, $position_id);
        $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
        $this->json_response->{$delete ? 'success' : 'error'}($message, $delete);
    }

    
    public function delete_unit_type_approval() {
        $unit_type_id = $this->input->post('unit_type_id');
        $position_id = $this->input->post('position_id');
        if (!isset($unit_type_id) || !isset($position_id)) {
            $this->json_response->error('Unit Type and Position is not available.');
            return;
        }
        
        $delete = $this->repository->delete_unit_type_approval($unit_type_id, $position_id);
        $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
        $this->json_response->{$delete ? 'success' : 'error'}($message, $delete);
    }


    ##### End Add Edit Approval

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
                'Data for the ' . $exists['data']->index_value . 
                ' index value and the ' . $exists['data']->year_period . 
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
                'Data for the ' . $exists['data']->index_value . 
                ' index value and the ' . $exists['data']->year_period . 
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
        $this->form_validation->set_rules('index_value', 'Index Value', 'required|trim|in_list[1,2,3,4,5]');
        $this->form_validation->set_rules('operator_1', 'Operator 1', 'trim|in_list[>,>=]');
        $this->form_validation->set_rules('value_1', 'Value 1', 'trim');
        $this->form_validation->set_rules('operator_2', 'Operator 2', 'trim');
        $this->form_validation->set_rules('value_2', 'Value 2', 'trim');
        $this->form_validation->set_rules('order', 'Order', 'required|trim|numeric|greater_than_equal_to[1]');
        $this->form_validation->set_rules('year_period_id', 'Year Period', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        return $this->form_validation->run();
    }

    private function collect_input_data($is_store = false, $id = null) {
        $input_data = [
            'index_value' => $this->input->post('index_value'),
            'description' => $this->input->post('description'),
            'color' => $this->input->post('color'),
            'order' => $this->input->post('order'),
            'year_period_id' => $this->input->post('year_period_id')
        ];

        if (!empty($this->input->post('operator_1')) && !empty($this->input->post('value_1'))) {
            $input_data['operator_1'] = $this->input->post('operator_1');
            $input_data['value_1'] = $this->input->post('value_1');
        } else {
            $input_data['operator_1'] = null;
            $input_data['value_1'] = null;
        }

        if (!empty($this->input->post('operator_2')) && !empty($this->input->post('value_2'))) {
            $input_data['operator_2'] = $this->input->post('operator_2');
            $input_data['value_2'] = $this->input->post('value_2');
        } else {
            $input_data['operator_2'] = null;
            $input_data['value_2'] = null;
        }

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
            'index_value' => form_error('index_value'),
            'operator_1' => form_error('operator_1'),
            'value_1' => form_error('value_1'),
            'operator_2' => form_error('operator_2'),
            'value_2' => form_error('value_2'),
            'order' => form_error('order'),
            'year_period_id' => form_error('year_period_id')
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

    public function get_by_temp_position($type_filter, $temp_id, $position_id) {
        if (!isset($type_filter) || !isset($temp_id) || !isset($position_id)) {
            $this->json_response->error('Type Filter, Unit and Position is required.');
            return;
        }
        if ($type_filter == 'unit_type') {
            $data = $this->repository->get_by_unit_type_id_position_id($temp_id, $position_id);
        } else {
            $data = $this->repository->get_by_unit_id_position_id($temp_id, $position_id);
        }

        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }

    public function get_units_by_unit_type_id() {
        $unit_type_id = $this->input->post('unit_type_id');
        $data = $this->repository->get_units_by_unit_type_id($unit_type_id);
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_unit_type_options() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $result = $this->repository->get_unit_type_options($search, $page);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->jenis], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_unit_options() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $result = $this->repository->get_unit_options($search, $page);
        $data = [
            'items' => array_map(fn($item) => ['id' => $item->id, 'text' => $item->nm_unit_kerja], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_unit_list() {
        $result = $this->repository->get_unit_list();
        $data = [
            'items' => array_map(fn($item) => ['value' => $item->id, 'name' => $item->nm_unit_kerja], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }

    public function get_position_list() {
        $result = $this->repository->get_position_list();
        $data = [
            'items' => array_map(fn($item) => ['value' => $item->id, 'name' => $item->nm_jabatan], $result['data']),
            'total_count' => $result['total']
        ];
        $this->json_response->success('Data successfully fetched.', $data);
    }
}