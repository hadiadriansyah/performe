<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Md_unit_kerja_model extends CI_Model {
    protected $table = 'md_unit_kerja';

    public function get_by_id($id) {
        $this->db->select("{$this->table}.id, {$this->table}.nm_unit_kerja, {$this->table}.id_parent")
                ->from($this->table)
                ->where("status", 1)
                ->where("id", $id);
        return $this->db->get()->row_array();
    }

    public function get_by_unit_type_id($unit_type_id) {
        $this->db->select("{$this->table}.id, {$this->table}.nm_unit_kerja")
                    ->where("id_jenis_unit_kerja", $unit_type_id)
                    ->where("status", 1);
        return $this->db->get($this->table)->result();
    }

    #####

    public function get_list() {
        $this->db->select("{$this->table}.id, {$this->table}.nm_unit_kerja")
                    ->where("status", 1);
        $query = $this->db->get($this->table);

        $total_count = $this->db->where("status", 1)->count_all_results($this->table);

        return [
            'data' => $query->result(),
            'total' => $total_count
        ];
    }

    public function get_options($search = '', $page = 1) {
        $search = strtolower($search);

        $this->db->select("{$this->table}.id, {$this->table}.nm_unit_kerja")
                    ->where("nm_unit_kerja ILIKE", "%{$search}%")
                    ->where("status", 1)
                    ->limit(10, ($page - 1) * 10);
        $query = $this->db->get($this->table);

        $total_count = $this->db->where("nm_unit_kerja ILIKE", "%{$search}%")->where("status", 1)->count_all_results($this->table);

        return [
            'data' => $query->result(),
            'total' => $total_count
        ];
    }
}