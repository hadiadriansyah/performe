<?php
interface Create_gs_repository_interface {
    ##### MD JABATAN #####
    public function get_position_by_id($id);
    ##### MD UNIT KERJA #####
    public function get_unit_by_id($id);
    ##### EMP DATA PEG #####
    public function get_employee_by_employee_id($employee_id);
    public function get_employee_options($search, $page);
    public function get_employee_options_by_employee_id($search, $page, $data);
    ##### HIST NDPEGUNGASAN #####
    public function get_temp_assignment_hist_by_employee_id($employee_id);
    ##### HIST JABATAN #####
    public function get_position_hist_by_employee_id($employee_id);
    ##### HIST PA INDIVIDUAL #####
    public function store_hist_pa(array $data);
    ##### HIST PELANKASIJAJAN #####
    public function get_temp_position_hist_by_employee_id($employee_id);
    ##### PA INDIVIDUAL #####
    public function exists(array $data);
    public function store_pa(array $data);
    ##### YEAR PERIOD #####
    public function get_year_period_options($search, $page);
}