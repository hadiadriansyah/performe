<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'interface/master/Year_period_repository_interface.php');

#[\AllowDynamicProperties]
class Year_period_repository implements Year_period_repository_interface {
    protected $year_period_model;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->model('Year_period_model');
        $this->year_period_model = $CI->Year_period_model;
    }

    ##### Year Period

    public function get_datatables() {
        return $this->year_period_model->get_datatables();
    }

    public function count_all() {
        return $this->year_period_model->count_all();
    }

    public function count_filtered() {
        return $this->year_period_model->count_filtered();
    }

    #####

    public function exists($data) {
        return $this->year_period_model->exists($data);
    }

    public function unique($data) {
        return $this->year_period_model->unique($data);
    }

    #####

    public function store($data) {
        return $this->year_period_model->store($data);
    }

    public function update($data) {
        return $this->year_period_model->update($data);
    }

    public function delete($id) {
        return $this->year_period_model->delete($id);
    }

    #####

    public function get_by_id($id) {
        return $this->year_period_model->get_by_id($id);
    }
    
}