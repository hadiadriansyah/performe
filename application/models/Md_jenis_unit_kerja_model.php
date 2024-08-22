<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Md_jenis_unit_kerja_model extends CI_Model {
    protected $table = 'md_jenis_unit_kerja';

    public function get_by_id($id) {
        $this->db->select("{$this->table}.id, {$this->table}.jenis")
                ->from($this->table)
                ->where("status", 1)
                ->where("id", $id);
        return $this->db->get()->row_array();
    }

    #####

    public function get_options($search = '', $page = 1) {
        $search = strtolower($search);

        $this->db->select("{$this->table}.id, {$this->table}.jenis")
                    ->where("jenis ILIKE", "%{$search}%")
                    ->where("status", 1)
                    ->limit(10, ($page - 1) * 10);
        $query = $this->db->get($this->table);

        $total_count = $this->db->where("jenis ILIKE", "%{$search}%")->where("status", 1)->count_all_results($this->table);

        return [
            'data' => $query->result(),
            'total' => $total_count
        ];
    }
}