<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Md_jabatan_model extends CI_Model {
    protected $table = 'md_jabatan';

    public function get_by_id($id) {
        $this->db->select("{$this->table}.id, {$this->table}.nm_jabatan")
                ->from($this->table)
                ->where("status", 1)
                ->where("id", $id);
        return $this->db->get()->row_array();
    }

    public function get_employee_options_by_id_parent($search, $page, $parent_id) {
        $data = [];
        $total_count = 0;
        $search = strtolower($search);

        if ($parent_id) {
            $this->db->select("edp.id_peg, edp.nama")
                        ->join("hist_pelaksana_jabatan hpj", "hpj.id_jabatan_diganti = {$this->table}.id", "left")
                        ->join("hist_jabatan hj", "hj.id_jabatan = {$this->table}.id", "left")
                        ->join("emp_data_peg edp", "edp.id_peg = COALESCE(hpj.id_peg, hj.id_peg)", "left")
                        ->where("{$this->table}.id_parent", $parent_id)
                        ->like("LOWER(CAST(edp.nama AS TEXT))", $search)
                        ->limit(10, ($page - 1) * 10);
            $query = $this->db->get($this->table);

            $data = $query->result();

            $total_count = $this->db->join("hist_pelaksana_jabatan hpj", "hpj.id_jabatan_diganti = {$this->table}.id", "left")
                                        ->join("hist_jabatan hj", "hj.id_jabatan = {$this->table}.id", "left")
                                        ->join("emp_data_peg edp", "edp.id_peg = COALESCE(hpj.id_peg, hj.id_peg)", "left")
                                        ->where("{$this->table}.id_parent", $parent_id)
                                        ->like("LOWER(CAST(edp.nama AS TEXT))", $search)
                                        ->count_all_results($this->table);
        }

        return [
            'data' => $data,
            'total' => $total_count
        ];
    }

    public function get_list() {
        $this->db->select("{$this->table}.id, {$this->table}.nm_jabatan")
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

        $this->db->select("{$this->table}.id, {$this->table}.nm_jabatan")
                    ->where("nm_jabatan ILIKE", "%{$search}%")
                    ->where("status", 1)
                    ->limit(10, ($page - 1) * 10);
        $query = $this->db->get($this->table);

        $total_count = $this->db->where("nm_jabatan ILIKE", "%{$search}%")->where("status", 1)->count_all_results($this->table);

        return [
            'data' => $query->result(),
            'total' => $total_count
        ];
    }
}