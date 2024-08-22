<?php
interface Kpi_repository_interface
{
    ##### KPI
    public function get_datatables();
    public function count_all();
    public function count_filtered();
    #####
    public function exists($data);
    public function unique($data);
    #####
    public function store($data);
    public function update($data);
    public function delete($id);
    #####
    public function get_by_id($id);
    ##### KPI Counter
    public function get_kpi_counter_options_by_year_period_id($search, $page, $year_period_id);
    ##### KPI Polarization
    public function get_kpi_polarization_options_by_year_period_id($search, $page, $year_period_id);
    ##### Year Period
    public function get_year_period_options($search, $page);
}