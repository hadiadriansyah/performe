<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hist_ndpenugasan_model extends CI_Model {
    protected $table = 'hist_ndpenugasan';

    public function get_by_employee_id($employee_id) {
        $this->db->select("{$this->table}.tmp_jabatan, {$this->table}.id_uker_penempatan, {$this->table}.id_uker_ndpenugasan, {$this->table}.tgl_selesai, emp_data_peg.nrik")
                    ->from($this->table)
                    ->join('emp_data_peg', 'emp_data_peg.id_peg = ' . $this->table . '.id_peg')
                    ->where($this->table . '.id_peg', $employee_id)
                    ->where($this->table . '.status', 1)
                    ->where($this->table . '.tgl_selesai >', 'CURRENT_DATE', FALSE);
        $query = $this->db->get();

        if ($query->num_rows() === 1) {
            return $query->row();
        } else {
            return null;
        }   
    }
}