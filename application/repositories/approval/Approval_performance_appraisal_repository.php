<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'interface/approval/Approval_performance_appraisal_repository_interface.php');

#[\AllowDynamicProperties]
class Approval_performance_appraisal_repository implements Approval_performance_appraisal_repository_interface {
    protected $approval_model;
    protected $approval_pa_model;
    protected $emp_data_peg_model;
    protected $hist_approval_pa_model;
    protected $hist_ndpenugasan_model;
    protected $hist_jabatan_model;
    protected $hist_pelaksana_jabatan_model;
    protected $index_score_model;
    protected $kpi_model;
    protected $kpi_individual_model;
    protected $kpi_individual_actual_model;
    protected $md_jabatan_model;
    protected $md_unit_kerja_model;
    protected $pa_individual_model;
    protected $year_period_model;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->model('Approval_model');
        $this->approval_model = $CI->Approval_model;
        $CI->load->model('Approval_pa_model');
        $this->approval_pa_model = $CI->Approval_pa_model;
        $CI->load->model('Emp_data_peg_model');
        $this->emp_data_peg_model = $CI->Emp_data_peg_model;
        $CI->load->model('Hist_approval_pa_model');
        $this->hist_approval_pa_model = $CI->Hist_approval_pa_model;
        $CI->load->model('Hist_ndpenugasan_model');
        $this->hist_ndpenugasan_model = $CI->Hist_ndpenugasan_model;
        $CI->load->model('Hist_jabatan_model');
        $this->hist_jabatan_model = $CI->Hist_jabatan_model;
        $CI->load->model('Hist_pelaksana_jabatan_model');
        $this->hist_pelaksana_jabatan_model = $CI->Hist_pelaksana_jabatan_model;
        $CI->load->model('Index_score_model');
        $this->index_score_model = $CI->Index_score_model;
        $CI->load->model('Kpi_model');
        $this->kpi_model = $CI->Kpi_model;
        $CI->load->model('Kpi_individual_model');
        $this->kpi_individual_model = $CI->Kpi_individual_model;
        $CI->load->model('Kpi_individual_actual_model');
        $this->kpi_individual_actual_model = $CI->Kpi_individual_actual_model;
        $CI->load->model('Md_jabatan_model');
        $this->md_jabatan_model = $CI->Md_jabatan_model;
        $CI->load->model('Md_unit_kerja_model');
        $this->md_unit_kerja_model = $CI->Md_unit_kerja_model;
        $CI->load->model('Pa_individual_model');
        $this->pa_individual_model = $CI->Pa_individual_model;
        $CI->load->model('Year_period_model');
        $this->year_period_model = $CI->Year_period_model;
    }

    #####

    public function get_approval_by_unit_id_position_id_in_hist_ndpenugasan($unit_id, $position_id) {
        return $this->approval_model->get_by_unit_id_position_id_in_hist_ndpenugasan($unit_id, $position_id);
    }

    public function get_approval_by_unit_id_position_id_in_hist_jabatan($unit_id, $position_id) {
        return $this->approval_model->get_by_unit_id_position_id_in_hist_jabatan($unit_id, $position_id);
    }

    public function get_approval_by_unit_id_position_id_in_hist_pelaksana_jabatan($unit_id, $position_id) {
        return $this->approval_model->get_by_unit_id_position_id_in_hist_pelaksana_jabatan($unit_id, $position_id);
    }

    #####

    public function exists_submit_pa($data) {
        return $this->approval_pa_model->exists($data);
    }

    public function get_approval_employees($year_period_id, $unit_id, $position_id) {
        return $this->approval_pa_model->get_approval_employees($year_period_id, $unit_id, $position_id);
    }

    public function get_approval_pa_by_pa_individual_id($pa_individual_id, $month_period) {
        return $this->approval_pa_model->get_by_pa_individual_id($pa_individual_id, $month_period);
    }
    
    public function update_submit_pa($data) {
        return $this->approval_pa_model->update($data);
    }

    #####

    public function get_employee_by_employee_id($employee_id) {
        return $this->emp_data_peg_model->get_by_employee_id($employee_id);
    }
    
    public function get_employee_options($search, $page) {
        return $this->emp_data_peg_model->get_options($search, $page);
    }

    public function get_employee_options_by_employee_id($search, $page, $data) {
        return $this->emp_data_peg_model->get_options_by_employee_id($search, $page, $data);
    }
    
    #####

    public function get_hist_approval_pa($approval_pa_id) {
        return $this->hist_approval_pa_model->get_by_approval_pa_id($approval_pa_id);
    }
    
    public function store_hist_approval_pa($data) {
        return $this->hist_approval_pa_model->store($data);
    }

    #####

    public function get_temp_assignment_hist_by_employee_id($employee_id) {
        return $this->hist_ndpenugasan_model->get_by_employee_id($employee_id);
    }

    #####

    public function get_position_hist_by_employee_id($employee_id) {
        return $this->hist_jabatan_model->get_by_employee_id($employee_id);
    }

    #####

    public function get_temp_position_hist_by_employee_id($employee_id) {
        return $this->hist_pelaksana_jabatan_model->get_by_employee_id($employee_id);
    }
    
    #####
    
    public function get_index_scores($year_period_id) {
        return $this->index_score_model->get_by_year_period_id($year_period_id);
    }

    ####

    public function get_kpi_by_id($id) {
        return $this->kpi_model->get_by_id($id);
    }
    
    public function get_kpi_options_by_year_period_id($search, $page, $year_period_id)
    {
        return $this->kpi_model->get_options_by_year_period_id($search, $page, $year_period_id);
    }
    
    #####

    public function exists_kpi($data) {
        return $this->kpi_individual_model->exists($data);
    }

    public function delete_kpi($id) {
        return $this->kpi_individual_model->delete($id);
    }

    public function get_kpi_individual_by_pa_id($id) {
        return $this->kpi_individual_model->get_by_pa_id($id);
    }

    public function get_kpi_individual_pa_by_pa_individual_id_year_period_id($data) {
        return $this->kpi_individual_model->get_kpi_individual_pa_by_pa_individual_id_year_period_id($data);
    }

    public function store_kpi($data) {
        return $this->kpi_individual_model->store($data);
    }
    
    public function submit_kpi($data) {
        return $this->kpi_individual_model->submit_kpi_pa($data);
    }

    public function unique_kpi($data) {
        return $this->kpi_individual_model->unique($data);
    }

    public function update_kpi($data) {
        return $this->kpi_individual_model->update($data);
    }

    #####
    
    public function get_pa_by_id($id) {
        return $this->kpi_individual_actual_model->get_by_id($id);
    }

    public function store_pa($data) {
        return $this->kpi_individual_actual_model->store($data);
    }

    public function update_pa($data) {
        return $this->kpi_individual_actual_model->update($data);
    }

    #####

    public function get_position_by_id($id) {
        return $this->md_jabatan_model->get_by_id($id);
    }

    #####

    public function get_unit_by_id($id) {
        return $this->md_unit_kerja_model->get_by_id($id);
    }

    #####

    public function get_year_period_options($search, $page) {
        return $this->year_period_model->get_options($search, $page);
    }
}