<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class User extends Base_controller {
    
    protected $repository;

    public function __construct() {
        parent::__construct();
        $this->load->repository('user_management/User_repository');
        $this->repository = new User_repository();

        $this->is_logged_in();
        $this->is_admin_access();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Users',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.css',
                '../vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/datatables/datatables.bundle.js',
                'admin/user-management/user/list.js',
                'admin/user-management/user/add-edit.js'
            ],
        ]);
        $this->template->load('admin/layout', 'admin/user_management/user/index', $data);
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
            'name' => $item->name,
            'email' => $item->email,
            'profile_picture' => $item->profile_picture,
            'is_active' => $item->is_active,
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
        if (!$this->validate_input(true)) {
            $this->handle_validation_error();
            return;
        }

        $data = $this->collect_input_data(true);

        if (!empty($_FILES['profile_picture']['name'])) {
            $upload = $this->do_upload('profile_picture');
            if ($upload['status']) {
                $data['profile_picture'] = $upload['file_name'];
            } else {
                $this->json_response->error($upload['error']);
                return;
            }
        }

        $exists = $this->repository->exists($data);
        if ($exists['is_exists']) {
            $this->json_response->error(
                'Data for the ' . $exists['data']->email . 
                ' is already available.'
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
        
        if (!empty($_FILES['profile_picture']['name'])) {
            $old_data = $this->repository->get_by_id($id);

            if ($old_data && !empty($old_data['profile_picture'])) {
                $this->delete_old_file($old_data['profile_picture']);
            }

            $upload = $this->do_upload('profile_picture');
            if ($upload['status']) {
                $data['profile_picture'] = $upload['file_name'];
            } else {
                $this->json_response->error($upload['error']);
                return;
            }
        }

        $exists = $this->repository->exists($data);
        $unique = $this->repository->unique($data);
        
        if ($exists['is_exists'] && $unique['is_unique']) {
            $this->json_response->error(
                'Data for the ' . $exists['data']->email . 
                ' is already available.'
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

        $old_data = $this->repository->get_by_id($id);
        if ($old_data && !empty($old_data['profile_picture'])) {
            $this->delete_old_file($old_data['profile_picture']);
        }
    
        $delete = $this->repository->delete($id);
        $message = $delete ? 'Data successfully deleted.' : 'Failed to delete data.';
        $this->json_response->{$delete ? 'success' : 'error'}($message, $delete);
    }

    private function validate_input($is_store = false) {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim');
        if ($is_store || (!$is_store && $this->input->post('password') !== '' && $this->input->post('password') !== null)) {
            $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|trim|matches[password]');
        }
        $this->form_validation->set_rules('is_active', 'Is Active', 'required|trim');
        $this->form_validation->set_error_delimiters('', '');
        return $this->form_validation->run();
    }

    private function collect_input_data($is_store = false, $id = null) {
        $input_data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'is_active' => $this->input->post('is_active')
        ];
        if ($is_store || (!$is_store && $this->input->post('password') !== '' && $this->input->post('password') !== null)) {
            $input_data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        }

        if (!$is_store) {
            $input_data['id'] = $id;
        }
        return $input_data;
    }

    private function handle_validation_error() {
        $errors = $this->collect_form_errors();
        $this->json_response->error('Failed to save data.', $errors);
    }

    private function collect_form_errors() {
        return [
            'name' => form_error('name'),
            'email' => form_error('email'),
            'password' => form_error('password'),
            'confirm_password' => form_error('confirm_password'),
            'is_active' => form_error('is_active')
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

    private function do_upload($field_name) {
        $config['upload_path'] = './uploads/profile_pictures/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field_name)) {
            return ['status' => false, 'error' => $this->upload->display_errors()];
        } else {
            return ['status' => true, 'file_name' => $this->upload->data('file_name')];
        }
    }
    
    private function delete_old_file($file_name) {
        $file_path = './uploads/profile_pictures/' . $file_name;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    #####
    
    public function get_by_id($id) {
        if (!isset($id)) {
            $this->json_response->error('ID is required.');
            return;
        }
        $data = $this->repository->get_by_id($id);
        
        if (!empty($data['profile_picture'])) {
            $is_file_exists = 'uploads/profile_pictures/' . $data['profile_picture'];
            $data['is_file_exists'] = file_exists($is_file_exists) ? true : false;
        } else {
            $data['is_file_exists'] = false;
        }
        
        $message = $data ? 'Data successfully fetched.' : 'Failed to fetch data.';
        $this->json_response->{$data ? 'success' : 'error'}($message, $data);
    }
}