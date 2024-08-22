<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'interface/setting/Approval_repository_interface.php');

#[\AllowDynamicProperties]
class Approval_repository implements Approval_repository_interface {
    protected $approval_model;
    protected $md_unit_kerja_model;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->model('Approval_model');
        $this->approval_model = $CI->Approval_model;
        $CI->load->model('Md_jabatan_model');
        $this->md_jabatan_model = $CI->Md_jabatan_model;
        $CI->load->model('Md_jenis_unit_kerja_model');
        $this->md_jenis_unit_kerja_model = $CI->Md_jenis_unit_kerja_model;
        $CI->load->model('Md_unit_kerja_model');
        $this->md_unit_kerja_model = $CI->Md_unit_kerja_model;
    }

    ##### Index Score

    public function get_datatables() {
        return $this->approval_model->get_datatables();
    }

    public function count_all() {
        return $this->approval_model->count_all();
    }

    public function count_filtered() {
        return $this->approval_model->count_filtered();
    }

    #####

    public function exists($data) {
        return $this->approval_model->exists($data);
    }

    public function store_approval($data) {
        return $this->approval_model->store($data);
    }

    public function update_approval($data) {
        return $this->approval_model->update($data);
    }

    public function delete_unit_approval($unit_id, $position_id) {
        return $this->approval_model->delete_unit_approval($unit_id, $position_id);
    }

    public function delete_unit_type_approval($unit_type_id, $position_id) {
        return $this->approval_model->delete_unit_type_approval($unit_type_id, $position_id);
    }

    #####

    public function get_by_unit_id_position_id($unit_id, $position_id) {
        return $this->approval_model->get_by_unit_id_position_id($unit_id, $position_id);
    }

    public function get_by_unit_type_id_position_id($unit_type_id, $position_id) {
        return $this->approval_model->get_by_unit_type_id_position_id($unit_type_id, $position_id);
    }

    #####

    public function get_position_list() {
        return $this->md_jabatan_model->get_list();
    }

    #####

    public function get_unit_type_options($search, $page) {
        return $this->md_jenis_unit_kerja_model->get_options($search, $page);
    }

    #####

    public function get_units_by_unit_type_id($unit_type_id) {
        return $this->md_unit_kerja_model->get_by_unit_type_id($unit_type_id);
    }

    public function get_unit_by_id($id) {
        return $this->md_unit_kerja_model->get_by_id($id);
    }

    public function get_unit_list() {
        return $this->md_unit_kerja_model->get_list();
    }

    public function get_unit_options($search, $page) {
        return $this->md_unit_kerja_model->get_options($search, $page);
    }
}