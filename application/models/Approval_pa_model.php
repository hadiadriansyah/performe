<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_pa_model extends CI_Model {
    protected $table_approval_pa = 'npm_approval_pa';
    protected $table_hist_approval_pa = 'npm_hist_approval_pa';
    protected $table_pa_individuals = 'npm_pa_individuals';
    protected $table_emp_data_peg = 'emp_data_peg';
    protected $table_md_jabatan = 'md_jabatan';
    protected $table_md_unit_kerja = 'md_unit_kerja';

    // public function get_by_pa_individual_id($pa_individual_id, $month_period) {
    //     $this->db->from($this->table_approval_pa)
    //                 ->where("{$this->table_approval_pa}.pa_individual_id", $pa_individual_id)
    //                 ->where("{$this->table_approval_pa}.month_period", $month_period);
    //     $result = $this->db->get();
    //     return $result->row_array();
    // }

    public function get_by_pa_individual_id($pa_individual_id, $month_period) {
        $sql = "
            WITH RankedData AS (
                SELECT
                    *,
                    ROW_NUMBER() OVER (PARTITION BY approval_pa_id, puk_number ORDER BY updated_at DESC) AS rn
                FROM
                    {$this->table_hist_approval_pa}
            ),
            LatestApprovalData AS (
                SELECT
                    approval_pa_id,
                    MAX(CASE WHEN puk_number = 1 THEN id::text ELSE NULL END)::uuid AS id_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN approval_pa_id::text ELSE NULL END)::uuid AS approval_pa_id_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN puk_number ELSE NULL END) AS puk_number_1,
                    MAX(CASE WHEN puk_number = 1 THEN employee_id ELSE NULL END) AS employee_id_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN unit_id ELSE NULL END) AS unit_id_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN position_id ELSE NULL END) AS position_id_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN comment ELSE NULL END) AS comment_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN status ELSE NULL END) AS status_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN created_at ELSE NULL END) AS created_at_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN updated_at ELSE NULL END) AS updated_at_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN created_by ELSE NULL END) AS created_by_puk_1,
                    MAX(CASE WHEN puk_number = 1 THEN updated_by ELSE NULL END) AS updated_by_puk_1,
                    MAX(CASE WHEN puk_number = 2 THEN id::text ELSE NULL END)::uuid AS id_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN approval_pa_id::text ELSE NULL END)::uuid AS approval_pa_id_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN puk_number ELSE NULL END) AS puk_number_2,
                    MAX(CASE WHEN puk_number = 2 THEN employee_id ELSE NULL END) AS employee_id_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN unit_id ELSE NULL END) AS unit_id_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN position_id ELSE NULL END) AS position_id_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN comment ELSE NULL END) AS comment_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN status ELSE NULL END) AS status_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN created_at ELSE NULL END) AS created_at_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN updated_at ELSE NULL END) AS updated_at_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN created_by ELSE NULL END) AS created_by_puk_2,
                    MAX(CASE WHEN puk_number = 2 THEN updated_by ELSE NULL END) AS updated_by_puk_2
                FROM
                    RankedData
                WHERE
                    rn = 1
                GROUP BY
                    approval_pa_id
            )
            SELECT
                at.*,
                lad.*,
                pa_1.nama as employee_name_puk_1,
                pa_2.nama as employee_name_puk_2
            FROM
                {$this->table_approval_pa} at
            LEFT JOIN
                LatestApprovalData lad ON at.id = lad.approval_pa_id
            LEFT JOIN
                {$this->table_emp_data_peg} pa_1 ON lad.created_by_puk_1 = pa_1.id_peg::varchar
            LEFT JOIN
                {$this->table_emp_data_peg} pa_2 ON lad.created_by_puk_2 = pa_2.id_peg::varchar
            WHERE
                at.pa_individual_id = ?
                AND at.month_period = ?;
        ";
    
        $query = $this->db->query($sql, array($pa_individual_id, $month_period));
        return $query->row_array();
    }

    public function get_approval_employees($year_period_id, $placement_unit_id, $position_id) {
        $this->db->select("{$this->table_approval_pa}.*, {$this->table_emp_data_peg}.nama as employee_name, {$this->table_emp_data_peg}.nrik as employee_npp, {$this->table_pa_individuals}.position_id, {$this->table_pa_individuals}.placement_unit_id, {$this->table_pa_individuals}.from_month, {$this->table_pa_individuals}.to_month, {$this->table_md_jabatan}.nm_jabatan as position_name, muku.nm_unit_kerja as unit_name, mukp.nm_unit_kerja as placement_unit_name")
                    ->from($this->table_approval_pa)
                    ->join($this->table_pa_individuals, "{$this->table_approval_pa}.pa_individual_id = {$this->table_pa_individuals}.id", 'left')
                    ->join($this->table_emp_data_peg, "{$this->table_pa_individuals}.employee_id = {$this->table_emp_data_peg}.id_peg", 'left')
                    ->join($this->table_md_jabatan, "{$this->table_pa_individuals}.position_id = {$this->table_md_jabatan}.id", 'left')
                    ->join("{$this->table_md_unit_kerja} muku", "{$this->table_pa_individuals}.unit_id = muku.id", 'left')
                    ->join("{$this->table_md_unit_kerja} mukp", "{$this->table_pa_individuals}.placement_unit_id = mukp.id", 'left')
                    ->where("{$this->table_pa_individuals}.year_period_id", $year_period_id)
                    ->where("{$this->table_pa_individuals}.status_appraisal", 1)
                    ->group_start()
                        ->where("{$this->table_approval_pa}.puk_1_unit", $placement_unit_id)
                        ->where("{$this->table_approval_pa}.puk_1_position", $position_id)
                    ->group_end()
                    ->or_group_start()
                        ->where("{$this->table_approval_pa}.puk_2_unit", $placement_unit_id)
                        ->where("{$this->table_approval_pa}.puk_2_position", $position_id)
                    ->group_end()
                    ->order_by("{$this->table_approval_pa}.puk_1_status", "ASC")
                    ->order_by("{$this->table_approval_pa}.puk_2_status", "ASC");

        $result = $this->db->get();
        return $result->result();
    }

    #####

    public function exists(array $data) {
        $this->db->from($this->table_approval_pa)
                    ->where("{$this->table_approval_pa}.pa_individual_id", $data['pa_individual_id'])
                    ->where("{$this->table_approval_pa}.month_period", $data['month_period'])
                    ->where("{$this->table_approval_pa}.month_periods", $data['month_periods']);
        $result = $this->db->get();
        return [
            'is_exists' => $result->num_rows() > 0,
            'data' => $result->row()
        ];
    }

    #####

    public function store($data) {
        return $this->db->insert($this->table_approval_pa, $data);
    }

    public function update($data) {
        return $this->db->update($this->table_approval_pa, $data, [
            'id' => $data['id'],
            'month_period' => get_current_month()
        ]);
    }
}