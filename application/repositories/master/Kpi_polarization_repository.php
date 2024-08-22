<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'interface/master/Kpi_polarization_repository_interface.php');

#[\AllowDynamicProperties]
class Kpi_polarization_repository implements Kpi_polarization_repository_interface {
    protected $kpi_polarization_model;
    protected $year_period_model;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->model('Kpi_polarization_model');
        $this->kpi_polarization_model = $CI->Kpi_polarization_model;
        $CI->load->model('Year_period_model');
        $this->year_period_model = $CI->Year_period_model;
    }

    #####

    public function get_datatables() {
        return $this->kpi_polarization_model->get_datatables();
    }

    public function count_all() {
        return $this->kpi_polarization_model->count_all();
    }

    public function count_filtered() {
        return $this->kpi_polarization_model->count_filtered();
    }

    #####

    public function exists(array $data) {
        return $this->kpi_polarization_model->exists($data);
    }

    public function unique(array $data) {
        return $this->kpi_polarization_model->unique($data);
    }

    #####

    public function store(array $data) {
        return $this->kpi_polarization_model->store($data);
    }

    public function update(array $data) {
        return $this->kpi_polarization_model->update($data);
    }

    public function delete($id) {
        return $this->kpi_polarization_model->delete($id);
    }

    #####

    public function get_by_id($id) {
        return $this->kpi_polarization_model->get_by_id($id);
    }

    #####

    public function get_year_period_options($search, $page) {
        return $this->year_period_model->get_options($search, $page);
    }
}