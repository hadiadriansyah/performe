<?php
interface Approval_performance_appraisal_repository_interface {
    ####
    public function get_approval_by_unit_id_position_id_in_hist_ndpenugasan($unit_id, $position_id);
    public function get_approval_by_unit_id_position_id_in_hist_jabatan($unit_id, $position_id);
    public function get_approval_by_unit_id_position_id_in_hist_pelaksana_jabatan($unit_id, $position_id);
    #####
    public function exists_submit_pa($data);
    public function get_approval_employees($year_period_id, $unit_id, $position_id);
    public function get_approval_pa_by_pa_individual_id($pa_individual_id, $month_period);
    #####
    public function get_hist_approval_pa($approval_pa_id);
    public function store_hist_approval_pa($data);
    public function update_submit_pa($data);
    #####
    public function get_employee_by_employee_id($employee_id);
    public function get_employee_options($search, $page);
    public function get_employee_options_by_employee_id($search, $page, $data);
    #####
    public function get_temp_assignment_hist_by_employee_id($employee_id);
    #####
    public function get_position_hist_by_employee_id($employee_id);
    #####
    public function get_temp_position_hist_by_employee_id($employee_id);
    #####
    public function get_index_scores($year_period_id);
    #####
    public function get_kpi_by_id($id);
    public function get_kpi_options_by_year_period_id($search, $page, $year_period_id);
    #####
    public function exists_kpi($data);
    public function delete_kpi($id);
    public function get_kpi_individual_by_pa_id($id);
    public function get_kpi_individual_pa_by_pa_individual_id_year_period_id($data);
    public function store_kpi($data);
    public function submit_kpi($data);
    public function unique_kpi($data);
    public function update_kpi($data);
    #####
    public function get_pa_by_id($id);
    public function store_pa($data);
    public function update_pa($data);
    #####
    public function get_position_by_id($id);
    #####
    public function get_unit_by_id($id);
    #####
    public function get_year_period_options($search, $page);
}