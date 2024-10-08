<?php
interface User_repository_interface
{
    ##### User
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
}