<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'interface/goals_settings/Create_gs_repository_interface.php');

#[\AllowDynamicProperties]
class Create_gs_repository implements Create_gs_repository_interface {
    protected $md_jabatan_model;
    protected $md_unit_kerja_model;
    protected $emp_data_peg_model;
    protected $hist_jabatan_model;
    protected $hist_pa_individual_model;
    protected $hist_pelaksana_jabatan_model;
    protected $pa_individual_model;
    protected $year_period_model;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->model('Md_jabatan_model');
        $this->md_jabatan_model = $CI->Md_jabatan_model;
        $CI->load->model('Md_unit_kerja_model');
        $this->md_unit_kerja_model = $CI->Md_unit_kerja_model;
        $CI->load->model('Emp_data_peg_model');
        $this->emp_data_peg_model = $CI->Emp_data_peg_model;
        $CI->load->model('Hist_ndpenugasan_model');
        $this->hist_ndpenugasan_model = $CI->Hist_ndpenugasan_model;
        $CI->load->model('Hist_jabatan_model');
        $this->hist_jabatan_model = $CI->Hist_jabatan_model;
        $CI->load->model('Hist_pa_individual_model');
        $this->hist_pa_individual_model = $CI->Hist_pa_individual_model;
        $CI->load->model('Hist_pelaksana_jabatan_model');
        $this->hist_pelaksana_jabatan_model = $CI->Hist_pelaksana_jabatan_model;
        $CI->load->model('Pa_individual_model');
        $this->pa_individual_model = $CI->Pa_individual_model;
        $CI->load->model('Year_period_model');
        $this->year_period_model = $CI->Year_period_model;
    }

    ##### MD JABATAN #####

    public function get_position_by_id($id) {
        return $this->md_jabatan_model->get_by_id($id);
    }

    ##### MD UNIT KERJA #####

    public function get_unit_by_id($id) {
        return $this->md_unit_kerja_model->get_by_id($id);
    }

    ##### EMP DATA PEG #####

    public function get_employee_by_employee_id($employee_id) {
        return $this->emp_data_peg_model->get_by_employee_id($employee_id);
    }

    public function get_employee_options($search, $page) {
        return $this->emp_data_peg_model->get_options($search, $page);
    }

    public function get_employee_options_by_employee_id($search, $page, $employee_id) {
        return $this->emp_data_peg_model->get_options_by_employee_id($search, $page, $employee_id);
    }

    ##### HIST NDPEGUNGASAN #####

    public function get_temp_assignment_hist_by_employee_id($employee_id) {
        return $this->hist_ndpenugasan_model->get_by_employee_id($employee_id);
    }

    #####

    ##### HIST JABATAN #####

    public function get_position_hist_by_employee_id($employee_id) {
        return $this->hist_jabatan_model->get_by_employee_id($employee_id);
    }

    ##### HIST PA INDIVIDUAL #####

    public function store_hist_pa(array $data) {
        return $this->hist_pa_individual_model->store($data);
    }

    ##### HIST PELANKASIJAJAN #####

    public function get_temp_position_hist_by_employee_id($employee_id) {
        return $this->hist_pelaksana_jabatan_model->get_by_employee_id($employee_id);
    }

    ##### PA INDIVIDUAL #####

    public function exists(array $data) {
        return $this->pa_individual_model->exists($data);
    }

    public function store_pa(array $data) {
        return $this->pa_individual_model->store($data);
    }

    ##### YEAR PERIOD #####

    public function get_year_period_options($search, $page) {
        return $this->year_period_model->get_options($search, $page);
    }
}