<?php
interface Approval_repository_interface
{
    ##### Approval
    public function get_datatables();
    public function count_all();
    public function count_filtered();
    #####
    public function exists($data);
    public function store_approval($data);
    public function update_approval($data);
    public function delete_unit_approval($unit_id, $position_id);
    public function delete_unit_type_approval($unit_type_id, $position_id);
    #####
    public function get_by_unit_id_position_id($unit_id, $position_id);
    public function get_by_unit_type_id_position_id($unit_type_id, $position_id);
    #####
    public function get_position_list();
    #####
    public function get_unit_type_options($search, $page);
    #####
    public function get_units_by_unit_type_id($unit_type_id);
    public function get_unit_by_id($id);
    public function get_unit_list();
    public function get_unit_options($search, $page);
}