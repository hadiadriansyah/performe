<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emp_data_peg_model extends CI_Model {
    protected $table = 'emp_data_peg';

    public function get_by_employee_id($employee_id) {
        $this->db->select("{$this->table}.id_peg, {$this->table}.nama")
                 ->from($this->table)
                 ->where("{$this->table}.status_peg", 1)
                 ->where("{$this->table}.id_peg", $employee_id);
        return $this->db->get()->row_array();
    }

    public function get_options($search, $page) {
        $search = strtolower($search);

        $this->db->select("{$this->table}.id_peg, {$this->table}.nama")
                ->like("LOWER(CAST(nama AS TEXT))", $search)
                ->where("{$this->table}.status_peg", 1)
                ->order_by("nama", "asc")
                ->limit(10, ($page - 1) * 10);
        $query = $this->db->get($this->table);

        $total_count = $this->db->like("LOWER(CAST(nama AS TEXT))", $search)->where("{$this->table}.status_peg", 1)->count_all_results($this->table);

        return [
            'data' => $query->result(),
            'total' => $total_count
        ];
    }

    public function get_options_by_employee_id($search, $page, $employee_id) {
        $data = [];
        $total_count = 0;
        $search = strtolower($search);

        if ($employee_id) {
            $this->db->select("{$this->table}.id_peg, {$this->table}.nama")
                        ->like("LOWER(CAST(nama AS TEXT))", $search)
                        ->where("{$this->table}.status_peg", 1)
                        ->where("{$this->table}.id_peg", $employee_id)
                        ->limit(10, ($page - 1) * 10);
            $query = $this->db->get($this->table);

            $data = $query->result();

            $total_count = $this->db->like("LOWER(CAST(nama AS TEXT))", $search)
                                        ->where("{$this->table}.status_peg", 1)
                                        ->where("{$this->table}.id_peg", $employee_id)
                                        ->count_all_results($this->table);
        }

        return [
            'data' => $data,
            'total' => $total_count
        ];
    }

    public function get_options_by_parent_id($search, $page, $parent_id) {
        $data = [];
        $total_count = 0;
        $search = strtolower($search);

        if ($parent_id) {
            $this->db->select('emp_data_peg.id_peg, emp_data_peg.nama')
                        ->join('emp_data_peg', 'emp_data_peg.id_peg = hist_pelaksana_jabatan.id_peg', 'left')
                        ->join('hist_pelaksana_jabatan', 'md_jabatan.id = hist_pelaksana_jabatan.id_jabatan_diganti', 'left')
                        ->join('npm_approval_target', 'npm_approval_target.puk_1 = md_jabatan.id_parent OR npm_approval_target.puk_2 = md_jabatan.id_parent', 'left')
                        ->join('npm_pa_individuals', 'npm_pa_individuals.employee_id = emp_data_peg.id_peg', 'left')
                        ->where('md_jabatan.id_parent', $parent_id)
                        ->group_start()
                            ->where('hist_pelaksana_jabatan.tgl_selesai >', 'CURRENT_DATE', FALSE)
                            ->where('hist_pelaksana_jabatan.status', 1)
                            ->where('hist_pelaksana_jabatan.id_jabatan_diganti IS NOT NULL', NULL, FALSE)
                        ->group_end()
                        ->or_group_start()
                            ->join('hist_jabatan', 'emp_data_peg.id_peg = hist_jabatan.id_peg', 'left')
                            ->where('hist_jabatan.status', 1)
                        ->group_end()
                        ->like("LOWER(CAST(nama AS TEXT))", $search)
                        ->limit(10, ($page - 1) * 10);
            $query = $this->db->get($this->table);

            $data = $query->result();

            $total_count = $this->db->join('hist_pelaksana_jabatan', 'md_jabatan.id = hist_pelaksana_jabatan.id_jabatan_diganti', 'left')
                                        ->join('emp_data_peg', 'emp_data_peg.id_peg = hist_pelaksana_jabatan.id_peg', 'left')
                                        ->join('npm_approval_target', 'npm_approval_target.puk_1 = md_jabatan.id_parent OR npm_approval_target.puk_2 = md_jabatan.id_parent', 'left')
                                        ->join('npm_pa_individuals', 'npm_pa_individuals.employee_id = emp_data_peg.id_peg', 'left')
                                        ->where('md_jabatan.id_parent', $parent_id)
                                        ->group_start()
                                            ->where('hist_pelaksana_jabatan.tgl_selesai >', 'CURRENT_DATE', FALSE)
                                            ->where('hist_pelaksana_jabatan.status', 1)
                                            ->where('hist_pelaksana_jabatan.id_jabatan_diganti IS NOT NULL', NULL, FALSE)
                                        ->group_end()
                                        ->or_group_start()
                                            ->join('hist_jabatan', 'emp_data_peg.id_peg = hist_jabatan.id_peg', 'left')
                                            ->where('hist_jabatan.status', 1)
                                        ->group_end()
                                        ->like("LOWER(CAST(nama AS TEXT))", $search)
                                        ->count_all_results($this->table);
        }

        return [
            'data' => $data,
            'total' => $total_count
        ];
    }
}