<?php
interface Kpi_counter_repository_interface
{
    ##### KPI Counter
    public function get_datatables();
    public function count_all();
    public function count_filtered();
    #####
    public function exists(array $data);
    public function unique(array $data);
    #####
    public function store(array $data);
    public function update(array $data);
    public function delete($id);
    #####
    public function get_by_id($id);
    #####
    public function get_year_period_options($search, $page);
}