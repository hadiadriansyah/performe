<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hist_approval_target_model extends CI_Model {
    protected $table_hist_approval_target = 'npm_hist_approval_target';

    public function get_by_approval_target_id($approval_target_id) {
        $sql = "
            WITH RankedData AS (
                SELECT
                    *,
                    ROW_NUMBER() OVER (PARTITION BY approval_target_id, puk_number ORDER BY updated_at DESC) AS rn
                FROM
                    {$this->table_hist_approval_target}
                WHERE
                    approval_target_id = ?
            )
            SELECT
                approval_target_id,
                MAX(CASE WHEN puk_number = 1 THEN id::text ELSE NULL END)::uuid AS id_puk_1,
                MAX(CASE WHEN puk_number = 1 THEN approval_target_id::text ELSE NULL END)::uuid AS approval_target_id_puk_1,
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
                MAX(CASE WHEN puk_number = 2 THEN approval_target_id::text ELSE NULL END)::uuid AS approval_target_id_puk_2,
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
                approval_target_id;
        ";

        $query = $this->db->query($sql, array($approval_target_id));
        return $query->row_array();
    }

    public function store($data) {
        return $this->db->insert($this->table_hist_approval_target, $data);
    }

}