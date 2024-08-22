<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'interface/master/Index_score_repository_interface.php');

#[\AllowDynamicProperties]
class Index_score_repository implements Index_score_repository_interface {
    protected $index_score_model;
    protected $year_period_model;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->model('Index_score_model');
        $this->index_score_model = $CI->Index_score_model;
        $CI->load->model('Year_period_model');
        $this->year_period_model = $CI->Year_period_model;
    }

    ##### Index Score

    public function get_datatables() {
        return $this->index_score_model->get_datatables();
    }

    public function count_all() {
        return $this->index_score_model->count_all();
    }

    public function count_filtered() {
        return $this->index_score_model->count_filtered();
    }

    #####

    public function exists(array $data) {
        return $this->index_score_model->exists($data);
    }

    public function unique(array $data) {
        return $this->index_score_model->unique($data);
    }

    #####

    public function store(array $data) {
        return $this->index_score_model->store($data);
    }

    public function update(array $data) {
        return $this->index_score_model->update($data);
    }

    public function delete($id) {
        return $this->index_score_model->delete($id);
    }

    #####

    public function get_by_id($id) {
        return $this->index_score_model->get_by_id($id);
    }

    ##### Year Period

    public function get_year_period_options($search, $page) {
        return $this->year_period_model->get_options($search, $page);
    }
    
}