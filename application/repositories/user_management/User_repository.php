<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'interface/user_management/User_repository_interface.php');

#[\AllowDynamicProperties]
class User_repository implements User_repository_interface {
    protected $user_model;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->model('User_model');
        $this->user_model = $CI->User_model;
    }

    ##### User

    public function get_datatables() {
        return $this->user_model->get_datatables();
    }

    public function count_all() {
        return $this->user_model->count_all();
    }

    public function count_filtered() {
        return $this->user_model->count_filtered();
    }

    #####

    public function exists($data) {
        return $this->user_model->exists($data);
    }

    public function unique($data) {
        return $this->user_model->unique($data);
    }

    #####

    public function store($data) {
        return $this->user_model->store($data);
    }

    public function update($data) {
        return $this->user_model->update($data);
    }

    public function delete($id) {
        return $this->user_model->delete($id);
    }

    #####

    public function get_by_id($id) {
        return $this->user_model->get_by_id($id);
    }
    
}