<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class hist_jabatan_model extends CI_Model {
    protected $table = 'hist_jabatan';

    public function get_by_employee_id($employee_id) {
        $this->db->select("{$this->table}.id_peg, {$this->table}.id_jabatan, {$this->table}.id_unit_kerja, hist_unit_kerja.ditempatkan_di, emp_data_peg.nrik")
                    ->from($this->table)
                    ->join('emp_data_peg', 'emp_data_peg.id_peg = ' . $this->table . '.id_peg')
                    ->join('hist_unit_kerja', 'hist_unit_kerja.id_peg = ' . $this->table . '.id_peg')
                    ->where("emp_data_peg.status_peg", 1)
                    ->where("hist_unit_kerja.status", 1)
                    ->where("{$this->table}.status", 1)
                    ->where("{$this->table}.id_peg", $employee_id);
        $query = $this->db->get();

        if ($query->num_rows() === 1) {
            return $query->row();
        } else {
            return null;
        }   
    }
}