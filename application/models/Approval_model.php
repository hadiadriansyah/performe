<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_model extends CI_Model {
    protected $table = 'npm_approvals';
    protected $emp_data_peg_table = 'emp_data_peg';
    protected $hist_ndpenugasan_table = 'hist_ndpenugasan';
    protected $hist_jabatan_table = 'hist_jabatan';
    protected $hist_pelaksana_jabatan_table = 'hist_pelaksana_jabatan';
    protected $hist_unit_kerja_table = 'hist_unit_kerja';
    protected $md_jabatan_table = 'md_jabatan';
    protected $md_jenis_unit_kerja_table = 'md_jenis_unit_kerja';
    protected $md_unit_kerja_table = 'md_unit_kerja';

    protected $column_order_unit_type = array(null, 'position_name', 'temp_name', null, null, null);
    protected $column_search_unit_type = array('p.nm_jabatan', 'j.jenis');
    protected $order_unit_type = array('position_name' => 'asc', 'temp_name' => 'asc');

    protected $column_order_unit = array(null, 'p.nm_jabatan', 'u.nm_unit_kerja', null, null, null);
    protected $column_search_unit = array('p.nm_jabatan', 'u.nm_unit_kerja');
    protected $order_unit = array('p.nm_jabatan' => 'asc', 'u.nm_unit_kerja' => 'asc');

    public function get_datatables() {
        $query = $this->_get_datatables_query();
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $query .= " LIMIT {$_POST['length']} OFFSET {$_POST['start']}";
        }
        return $this->db->query($query)->result();
    }
    
    private function _get_datatables_query() {
        $type = $this->input->post('type');
        $unit_type_id = $this->input->post('unit_type_id');
        $unit_id = $this->input->post('unit_id');

        if ($type == 'unit_type' && $unit_type_id) {
            // Subquery for filtered_unit_counts
            $subquery_filtered_unit_counts = "(SELECT u.position_id, u.unit_id, u.unit_approval_1_id, u.position_approval_1_id, u.unit_approval_2_id, u.position_approval_2_id, u.custom_unit_1, u.custom_unit_2
                FROM (
                    SELECT a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2, COUNT(*) AS count
                    FROM {$this->table} a
                    JOIN {$this->md_unit_kerja_table} u ON a.unit_id = u.id
                    JOIN {$this->md_jenis_unit_kerja_table} j ON u.id_jenis_unit_kerja = j.id
                    WHERE j.id = $unit_type_id 
                        AND a.unit_id IS NOT NULL
                        AND a.position_id IS NOT NULL
                        AND (a.unit_approval_1_id IS NOT NULL OR a.unit_approval_2_id IS NOT NULL)
                        AND (a.custom_unit_1 IS NOT NULL OR a.custom_unit_2 IS NOT NULL)
                    GROUP BY a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2
                ) u
                JOIN (
                    SELECT position_id, MAX(count) AS max_count
                    FROM (
                        SELECT a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_2, COUNT(*) AS count
                        FROM {$this->table} a
                        JOIN {$this->md_unit_kerja_table} u ON a.unit_id = u.id
                        JOIN {$this->md_jenis_unit_kerja_table} j ON u.id_jenis_unit_kerja = j.id
                        WHERE j.id = $unit_type_id 
                            AND a.unit_id IS NOT NULL
                            AND a.position_id IS NOT NULL
                            AND (a.unit_approval_1_id IS NOT NULL OR a.unit_approval_2_id IS NOT NULL)
                            AND (a.custom_unit_1 IS NOT NULL OR a.custom_unit_2 IS NOT NULL)
                        GROUP BY a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2
                    ) uc
                    GROUP BY position_id
                ) m ON u.position_id = m.position_id AND u.count = m.max_count)";

            // Subquery for filtered_approval_counts_1
            $subquery_filtered_approval_counts_1 = "(SELECT a.position_id, a.position_approval_1_id, a.count, a.array_length
            FROM (
                SELECT a.position_id, a.position_approval_1_id, COUNT(*) AS count,
                    array_length(string_to_array(replace(replace(a.position_approval_1_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                FROM {$subquery_filtered_unit_counts} a
                JOIN md_unit_kerja u ON a.unit_id = u.id
                JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                WHERE j.id = $unit_type_id 
                    AND a.position_approval_1_id IS NOT NULL
                GROUP BY a.position_id, a.position_approval_1_id
            ) a
            JOIN (
                SELECT position_id, MAX(count) AS max_count
                FROM (
                    SELECT a.position_id, a.position_approval_1_id, COUNT(*) AS count,
                        array_length(string_to_array(replace(replace(a.position_approval_1_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                    FROM {$subquery_filtered_unit_counts} a
                    JOIN md_unit_kerja u ON a.unit_id = u.id
                    JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                    WHERE j.id = $unit_type_id 
                        AND a.position_approval_1_id IS NOT NULL
                    GROUP BY a.position_id, a.position_approval_1_id
                ) ac1
                GROUP BY position_id
            ) m ON a.position_id = m.position_id AND a.count = m.max_count
            ORDER BY a.position_id, a.array_length DESC)";

            // Subquery for filtered_approval_counts_2
            $subquery_filtered_approval_counts_2 = "(SELECT a.position_id, a.position_approval_2_id, a.count, a.array_length
            FROM (
                SELECT a.position_id, a.position_approval_2_id, COUNT(*) AS count,
                    array_length(string_to_array(replace(replace(a.position_approval_2_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                FROM {$subquery_filtered_unit_counts} a
                JOIN md_unit_kerja u ON a.unit_id = u.id
                JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                WHERE j.id = $unit_type_id 
                    AND a.position_approval_2_id IS NOT NULL
                GROUP BY a.position_id, a.position_approval_2_id
            ) a
            JOIN (
                SELECT position_id, MAX(count) AS max_count
                FROM (
                    SELECT a.position_id, a.position_approval_2_id, COUNT(*) AS count,
                        array_length(string_to_array(replace(replace(a.position_approval_2_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                    FROM {$subquery_filtered_unit_counts} a
                    JOIN md_unit_kerja u ON a.unit_id = u.id
                    JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                    WHERE j.id = $unit_type_id 
                        AND a.position_approval_2_id IS NOT NULL
                    GROUP BY a.position_id, a.position_approval_2_id
                ) ac2
                GROUP BY position_id
            ) m ON a.position_id = m.position_id AND a.count = m.max_count
            ORDER BY a.position_id, a.array_length DESC)";

            // unnest_approval_1
            $unnest_approval_1 = "SELECT fa1.position_id, 
                unnest(string_to_array(replace(replace(fa1.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                FROM ($subquery_filtered_approval_counts_1) fa1";
            
            // unnest_approval_2
            $unnest_approval_2 = "SELECT fa2.position_id, 
                unnest(string_to_array(replace(replace(fa2.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                FROM ($subquery_filtered_approval_counts_2) fa2";

            // approval_1_names
            $approval_1_names = "SELECT ua1.position_id, array_agg(ua1.position_approval_1_id) AS position_approval_1_ids, array_agg(p1.nm_jabatan) AS position_approval_1_names 
                FROM ({$unnest_approval_1}) ua1 
                JOIN {$this->md_jabatan_table} p1 ON ua1.position_approval_1_id = p1.id 
                WHERE p1.status = 1
                GROUP BY ua1.position_id";

            // approval_2_names
            $approval_2_names = "SELECT ua2.position_id, array_agg(ua2.position_approval_2_id) AS position_approval_2_ids, array_agg(p2.nm_jabatan) AS position_approval_2_names 
                FROM ({$unnest_approval_2}) ua2 
                JOIN {$this->md_jabatan_table} p2 ON ua2.position_approval_2_id = p2.id 
                WHERE p2.status = 1
                GROUP BY ua2.position_id";

            $subquery = "SELECT DISTINCT ON (p.id, j.id)
                    j.id AS temp_id,
                    j.jenis AS temp_name,
                    p.id AS position_id,
                    p.nm_jabatan AS position_name,
                    f.custom_unit_1,
                    f.custom_unit_2,
                    f.unit_approval_1_id,
                    ua1.nm_unit_kerja as unit_approval_1_name,
                    f.unit_approval_2_id,
                    ua2.nm_unit_kerja as unit_approval_2_name,
                    array_to_string(a1.position_approval_1_ids, ', ') as position_approval_1_ids,
                    array_to_string(a1.position_approval_1_names, ', ') as position_approval_1_names,
                    array_to_string(a2.position_approval_2_ids, ', ') as position_approval_2_ids,
                    array_to_string(a2.position_approval_2_names, ', ') as position_approval_2_names,
                    CASE 
                        WHEN f.custom_unit_1 != 0 THEN '3'
                        WHEN f.unit_approval_1_id IS NULL THEN '0'
                        WHEN f.unit_id = f.unit_approval_1_id THEN '1'
                        ELSE '2'
                    END AS unit_type_1,
                    CASE 
                        WHEN f.custom_unit_2 != 0 THEN '3'
                        WHEN f.unit_approval_2_id IS NULL THEN '0'
                        WHEN f.unit_id = f.unit_approval_2_id THEN '1'
                        ELSE '2'
                    END AS unit_type_2,
                    CASE 
                        WHEN f.custom_unit_1 != 0 THEN 'Unit Custom'
                        WHEN f.unit_approval_1_id IS NULL THEN ''
                        WHEN f.unit_id = f.unit_approval_1_id THEN 'Unit Pemohon'
                        ELSE 'Unit Induk'
                    END AS unit_type_1_name,
                    CASE 
                        WHEN f.custom_unit_2 != 0 THEN 'Unit Custom'
                        WHEN f.unit_approval_2_id IS NULL THEN ''
                        WHEN f.unit_id = f.unit_approval_2_id THEN 'Unit Pemohon'
                        ELSE 'Unit Induk'
                    END AS unit_type_2_name
                FROM {$this->md_jabatan_table} p
                CROSS JOIN {$this->md_jenis_unit_kerja_table} j
                LEFT JOIN ($subquery_filtered_unit_counts) f ON p.id = f.position_id
                LEFT JOIN {$this->md_unit_kerja_table} ua1 ON f.unit_approval_1_id = ua1.id
                LEFT JOIN {$this->md_unit_kerja_table} ua2 ON f.unit_approval_2_id = ua2.id
                LEFT JOIN ($approval_1_names) a1 ON p.id = a1.position_id
                LEFT JOIN ($approval_2_names) a2 ON p.id = a2.position_id
                WHERE j.id = $unit_type_id
                AND p.status = 1
                AND j.status = 1";

            // Search
            $search_value = strtolower($_POST['search']['value'] ?? '');
            if ($search_value) {
                $subquery .= " AND (";
                foreach ($this->column_search_unit_type as $i => $item) {
                    if ($i > 0) {
                        $subquery .= " OR LOWER($item) ILIKE '%" . $this->db->escape_like_str($search_value) . "%'";
                    } else {
                        $subquery .= " LOWER($item) ILIKE '%" . $this->db->escape_like_str($search_value) . "%'";
                    }
                }
                $subquery .= ")";
            }

            // Column search
            foreach ($this->column_search_unit_type as $i => $item) {
                $column_search_value = $_POST['columns'][$i + 1]['search']['value'] ?? '';
                if ($column_search_value) {
                    $subquery .= " AND LOWER($item) ILIKE '%" . $this->db->escape_like_str(strtolower($column_search_value)) . "%'";
                }
            }

            // Group by and order by
            $subquery .= " GROUP BY
                                j.id, j.jenis, p.id, p.nm_jabatan,
                                f.unit_id, f.unit_approval_1_id, ua1.nm_unit_kerja, f.unit_approval_2_id, ua2.nm_unit_kerja, f.custom_unit_1, f.custom_unit_2,
                                a1.position_approval_1_ids, a1.position_approval_1_names, 
                                a2.position_approval_2_ids, a2.position_approval_2_names ORDER BY p.id ASC, j.id ASC";

            $query = "SELECT * FROM ($subquery) subquery ORDER BY position_approval_1_ids IS NOT NULL DESC";
            // Order
            if (isset($_POST['order'])) {
                $order_column = $this->column_order_unit_type[$_POST['order'][0]['column']];
                $order_dir = $_POST['order'][0]['dir'];
                $query .= ", $order_column $order_dir";
            } else {
                $order_clauses = [];
                foreach ($this->order_unit_type as $key => $value) {
                    $order_clauses[] = "$key $value";
                }
                if (!empty($order_clauses)) {
                    $query .= ", " . implode(', ', $order_clauses);
                }
            }
            return $query;
            
        } else {
            // unnest_approval_1
            $unnest_approval_1 = "SELECT a.unit_id, a.position_id, a.unit_approval_1_id, 
                                unnest(string_to_array(replace(replace(a.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                                FROM {$this->table} a";
            
            // unnest_approval_2
            $unnest_approval_2 = "SELECT a.unit_id, a.position_id, a.unit_approval_2_id, 
                                unnest(string_to_array(replace(replace(a.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                                FROM {$this->table} a";
            
            // approval_1_names
            $approval_1_names = "SELECT ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id, array_agg(ua1.position_approval_1_id) AS position_approval_1_id, array_agg(p1.nm_jabatan) AS position_approval_1_names 
                                FROM ({$unnest_approval_1}) ua1 
                                JOIN {$this->md_jabatan_table} p1 ON ua1.position_approval_1_id = p1.id 
                                WHERE p1.status = 1
                                GROUP BY ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id";
            // approval_2_names
            $approval_2_names = "SELECT ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id, array_agg(ua2.position_approval_2_id) AS position_approval_2_id, array_agg(p2.nm_jabatan) AS position_approval_2_names 
                                FROM ({$unnest_approval_2}) ua2 
                                JOIN {$this->md_jabatan_table} p2 ON ua2.position_approval_2_id = p2.id 
                                WHERE p2.status = 1
                                GROUP BY ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id";
            
            $this->db->select("
                p.id as position_id,
                p.nm_jabatan as position_name,
                u.id as temp_id,
                u.nm_unit_kerja as temp_name,
                a.custom_unit_1,
                a.custom_unit_2,
                a.unit_approval_1_id,
                ua1.nm_unit_kerja as unit_approval_1_name,
                a.unit_approval_2_id,
                ua2.nm_unit_kerja as unit_approval_2_name,
                array_to_string(a1.position_approval_1_id, ', ') as position_approval_1_ids,
                array_to_string(a1.position_approval_1_names, ', ') as position_approval_1_names,
                array_to_string(a2.position_approval_2_id, ', ') as position_approval_2_ids,
                array_to_string(a2.position_approval_2_names, ', ') as position_approval_2_names,
                CASE 
                    WHEN a.custom_unit_1 != 0 THEN '3'
                    WHEN a.unit_approval_1_id IS NULL THEN '0'
                    WHEN a.unit_id = a.unit_approval_1_id THEN '1'
                    ELSE '2'
                END AS unit_type_1,
                CASE 
                    WHEN a.custom_unit_2 != 0 THEN '3'
                    WHEN a.unit_approval_2_id IS NULL THEN '0'
                    WHEN a.unit_id = a.unit_approval_2_id THEN '1'
                    ELSE '2'
                END AS unit_type_2,
                CASE 
                    WHEN a.custom_unit_1 != 0 THEN 'Unit Custom'
                    WHEN a.unit_approval_1_id IS NULL THEN ''
                    WHEN a.unit_id = a.unit_approval_1_id THEN 'Unit Pemohon'
                    ELSE 'Unit Induk'
                END AS unit_type_1_name,
                CASE 
                    WHEN a.custom_unit_2 != 0 THEN 'Unit Custom'
                    WHEN a.unit_approval_2_id IS NULL THEN ''
                    WHEN a.unit_id = a.unit_approval_2_id THEN 'Unit Pemohon'
                    ELSE 'Unit Induk'
                END AS unit_type_2_name", false);
            $this->db->from("{$this->md_jabatan_table} p");
            $this->db->join("{$this->md_unit_kerja_table} u", '1=1', 'CROSS');
            $this->db->join("{$this->table} a", 'a.position_id = p.id and a.unit_id = u.id', 'left');
            $this->db->join("{$this->md_unit_kerja_table} ua1", 'ua1.id = a.unit_approval_1_id', 'left');
            $this->db->join("{$this->md_unit_kerja_table} ua2", 'ua2.id = a.unit_approval_2_id', 'left');
            $this->db->join("({$approval_1_names}) a1", 'a.unit_id = a1.unit_id AND a.position_id = a1.position_id AND a.unit_approval_1_id = a1.unit_approval_1_id', 'left', false);
            $this->db->join("({$approval_2_names}) a2", 'a.unit_id = a2.unit_id AND a.position_id = a2.position_id AND a.unit_approval_2_id = a2.unit_approval_2_id', 'left', false);
            $this->db->where('p.status', 1);
            $this->db->where('u.status', 1);
            $this->db->order_by('a1.position_approval_1_id IS NOT NULL', 'DESC', false);

            if ($unit_id) {
                $this->db->where('a.unit_id', $unit_id);
            }
            
            // Search
            $search_value = strtolower($_POST['search']['value'] ?? '');
            if ($search_value) {
                $this->db->group_start();
                foreach ($this->column_search_unit as $i => $item) {
                    if ($i > 0) {
                        $this->db->or_where("$item ILIKE", "%{$this->db->escape_like_str($search_value)}%");
                    } else {
                        $this->db->where("$item ILIKE", "%{$this->db->escape_like_str($search_value)}%");
                    }
                }
                $this->db->group_end();
            }
        
            // Column search
            foreach ($this->column_search_unit as $i => $item) {
                $column_search_unit_value = $_POST['columns'][$i + 1]['search']['value'] ?? '';
                if ($column_search_unit_value) {
                    $this->db->where("$item ILIKE", "%{$column_search_unit_value}%");
                }
            }
        
            // Order
            if (isset($_POST['order'])) {
                $order_column = $this->column_order_unit[$_POST['order'][0]['column']];
                $order_dir = $_POST['order'][0]['dir'];
                $this->db->order_by($order_column, $order_dir);
            } else {
                foreach ($this->order_unit as $key => $value) {
                    $this->db->order_by($key, $value);
                }
            }
            return $this->db->get_compiled_select();
        }
    }

    public function count_all() {
        $type = $this->input->post('type');
        $unit_type_id = $this->input->post('unit_type_id');
        $unit_id = $this->input->post('unit_id');

        if ($type == 'unit_type' && $unit_type_id) {
            // Subquery for filtered_unit_counts
            $subquery_filtered_unit_counts = "(SELECT u.position_id, u.unit_id, u.unit_approval_1_id, u.position_approval_1_id, u.unit_approval_2_id, u.position_approval_2_id, u.custom_unit_1, u.custom_unit_2
                FROM (
                    SELECT a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2, COUNT(*) AS count
                    FROM {$this->table} a
                    JOIN {$this->md_unit_kerja_table} u ON a.unit_id = u.id
                    JOIN {$this->md_jenis_unit_kerja_table} j ON u.id_jenis_unit_kerja = j.id
                    WHERE j.id = $unit_type_id 
                        AND a.unit_id IS NOT NULL
                        AND a.position_id IS NOT NULL
                        AND (a.unit_approval_1_id IS NOT NULL OR a.unit_approval_2_id IS NOT NULL)
                        AND (a.custom_unit_1 IS NOT NULL OR a.custom_unit_2 IS NOT NULL)
                    GROUP BY a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2
                ) u
                JOIN (
                    SELECT position_id, MAX(count) AS max_count
                    FROM (
                        SELECT a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_2, COUNT(*) AS count
                        FROM {$this->table} a
                        JOIN {$this->md_unit_kerja_table} u ON a.unit_id = u.id
                        JOIN {$this->md_jenis_unit_kerja_table} j ON u.id_jenis_unit_kerja = j.id
                        WHERE j.id = $unit_type_id 
                            AND a.unit_id IS NOT NULL
                            AND a.position_id IS NOT NULL
                            AND (a.unit_approval_1_id IS NOT NULL OR a.unit_approval_2_id IS NOT NULL)
                            AND (a.custom_unit_1 IS NOT NULL OR a.custom_unit_2 IS NOT NULL)
                        GROUP BY a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2
                    ) uc
                    GROUP BY position_id
                ) m ON u.position_id = m.position_id AND u.count = m.max_count)";

            // Subquery for filtered_approval_counts_1
            $subquery_filtered_approval_counts_1 = "(SELECT a.position_id, a.position_approval_1_id, a.count, a.array_length
            FROM (
                SELECT a.position_id, a.position_approval_1_id, COUNT(*) AS count,
                    array_length(string_to_array(replace(replace(a.position_approval_1_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                FROM {$subquery_filtered_unit_counts} a
                JOIN md_unit_kerja u ON a.unit_id = u.id
                JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                WHERE j.id = $unit_type_id 
                    AND a.position_approval_1_id IS NOT NULL
                GROUP BY a.position_id, a.position_approval_1_id
            ) a
            JOIN (
                SELECT position_id, MAX(count) AS max_count
                FROM (
                    SELECT a.position_id, a.position_approval_1_id, COUNT(*) AS count,
                        array_length(string_to_array(replace(replace(a.position_approval_1_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                    FROM {$subquery_filtered_unit_counts} a
                    JOIN md_unit_kerja u ON a.unit_id = u.id
                    JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                    WHERE j.id = $unit_type_id 
                        AND a.position_approval_1_id IS NOT NULL
                    GROUP BY a.position_id, a.position_approval_1_id
                ) ac1
                GROUP BY position_id
            ) m ON a.position_id = m.position_id AND a.count = m.max_count
            ORDER BY a.position_id, a.array_length DESC)";

            // Subquery for filtered_approval_counts_2
            $subquery_filtered_approval_counts_2 = "(SELECT a.position_id, a.position_approval_2_id, a.count, a.array_length
            FROM (
                SELECT a.position_id, a.position_approval_2_id, COUNT(*) AS count,
                    array_length(string_to_array(replace(replace(a.position_approval_2_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                FROM {$subquery_filtered_unit_counts} a
                JOIN md_unit_kerja u ON a.unit_id = u.id
                JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                WHERE j.id = $unit_type_id 
                    AND a.position_approval_2_id IS NOT NULL
                GROUP BY a.position_id, a.position_approval_2_id
            ) a
            JOIN (
                SELECT position_id, MAX(count) AS max_count
                FROM (
                    SELECT a.position_id, a.position_approval_2_id, COUNT(*) AS count,
                        array_length(string_to_array(replace(replace(a.position_approval_2_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                    FROM {$subquery_filtered_unit_counts} a
                    JOIN md_unit_kerja u ON a.unit_id = u.id
                    JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                    WHERE j.id = $unit_type_id 
                        AND a.position_approval_2_id IS NOT NULL
                    GROUP BY a.position_id, a.position_approval_2_id
                ) ac2
                GROUP BY position_id
            ) m ON a.position_id = m.position_id AND a.count = m.max_count
            ORDER BY a.position_id, a.array_length DESC)";

            // unnest_approval_1
            $unnest_approval_1 = "SELECT fa1.position_id, 
                unnest(string_to_array(replace(replace(fa1.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                FROM ($subquery_filtered_approval_counts_1) fa1";
            
            // unnest_approval_2
            $unnest_approval_2 = "SELECT fa2.position_id, 
                unnest(string_to_array(replace(replace(fa2.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                FROM ($subquery_filtered_approval_counts_2) fa2";

            // approval_1_names
            $approval_1_names = "SELECT ua1.position_id, array_agg(ua1.position_approval_1_id) AS position_approval_1_ids, array_agg(p1.nm_jabatan) AS position_approval_1_names 
                FROM ({$unnest_approval_1}) ua1 
                JOIN {$this->md_jabatan_table} p1 ON ua1.position_approval_1_id = p1.id 
                WHERE p1.status = 1
                GROUP BY ua1.position_id";

            // approval_2_names
            $approval_2_names = "SELECT ua2.position_id, array_agg(ua2.position_approval_2_id) AS position_approval_2_ids, array_agg(p2.nm_jabatan) AS position_approval_2_names 
                FROM ({$unnest_approval_2}) ua2 
                JOIN {$this->md_jabatan_table} p2 ON ua2.position_approval_2_id = p2.id 
                WHERE p2.status = 1
                GROUP BY ua2.position_id";

            $subquery = "SELECT DISTINCT ON (p.id, j.id)
                    j.id AS temp_id,
                    j.jenis AS temp_name,
                    p.id AS position_id,
                    p.nm_jabatan AS position_name,
                    f.custom_unit_1,
                    f.custom_unit_2,
                    f.unit_approval_1_id,
                    ua1.nm_unit_kerja as unit_approval_1_name,
                    f.unit_approval_2_id,
                    ua2.nm_unit_kerja as unit_approval_2_name,
                    array_to_string(a1.position_approval_1_ids, ', ') as position_approval_1_ids,
                    array_to_string(a1.position_approval_1_names, ', ') as position_approval_1_names,
                    array_to_string(a2.position_approval_2_ids, ', ') as position_approval_2_ids,
                    array_to_string(a2.position_approval_2_names, ', ') as position_approval_2_names,
                    CASE 
                        WHEN f.custom_unit_1 != 0 THEN '3'
                        WHEN f.unit_approval_1_id IS NULL THEN '0'
                        WHEN f.unit_id = f.unit_approval_1_id THEN '1'
                        ELSE '2'
                    END AS unit_type_1,
                    CASE 
                        WHEN f.custom_unit_2 != 0 THEN '3'
                        WHEN f.unit_approval_2_id IS NULL THEN '0'
                        WHEN f.unit_id = f.unit_approval_2_id THEN '1'
                        ELSE '2'
                    END AS unit_type_2,
                    CASE 
                        WHEN f.custom_unit_1 != 0 THEN 'Unit Custom'
                        WHEN f.unit_approval_1_id IS NULL THEN ''
                        WHEN f.unit_id = f.unit_approval_1_id THEN 'Unit Pemohon'
                        ELSE 'Unit Induk'
                    END AS unit_type_1_name,
                    CASE 
                        WHEN f.custom_unit_2 != 0 THEN 'Unit Custom'
                        WHEN f.unit_approval_2_id IS NULL THEN ''
                        WHEN f.unit_id = f.unit_approval_2_id THEN 'Unit Pemohon'
                        ELSE 'Unit Induk'
                    END AS unit_type_2_name
                FROM {$this->md_jabatan_table} p
                CROSS JOIN {$this->md_jenis_unit_kerja_table} j
                LEFT JOIN ($subquery_filtered_unit_counts) f ON p.id = f.position_id
                LEFT JOIN {$this->md_unit_kerja_table} ua1 ON f.unit_approval_1_id = ua1.id
                LEFT JOIN {$this->md_unit_kerja_table} ua2 ON f.unit_approval_2_id = ua2.id
                LEFT JOIN ($approval_1_names) a1 ON p.id = a1.position_id
                LEFT JOIN ($approval_2_names) a2 ON p.id = a2.position_id
                WHERE j.id = $unit_type_id
                AND p.status = 1
                AND j.status = 1";

            // Group by and order by
            $subquery .= " GROUP BY
                                j.id, j.jenis, p.id, p.nm_jabatan,
                                f.unit_id, f.unit_approval_1_id, ua1.nm_unit_kerja, f.unit_approval_2_id, ua2.nm_unit_kerja, f.custom_unit_1, f.custom_unit_2,
                                a1.position_approval_1_ids, a1.position_approval_1_names, 
                                a2.position_approval_2_ids, a2.position_approval_2_names ORDER BY p.id ASC, j.id ASC";

            $query = "SELECT * FROM ($subquery) subquery ORDER BY position_approval_1_ids IS NOT NULL DESC";
            
            return $this->db->query($query)->num_rows();
            
        } else {
            // unnest_approval_1
            $unnest_approval_1 = "SELECT a.unit_id, a.position_id, a.unit_approval_1_id, 
                                unnest(string_to_array(replace(replace(a.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                                FROM {$this->table} a";
            
            // unnest_approval_2
            $unnest_approval_2 = "SELECT a.unit_id, a.position_id, a.unit_approval_2_id, 
                                unnest(string_to_array(replace(replace(a.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                                FROM {$this->table} a";
            
            // approval_1_names
            $approval_1_names = "SELECT ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id, array_agg(ua1.position_approval_1_id) AS position_approval_1_id, array_agg(p1.nm_jabatan) AS position_approval_1_names 
                                FROM ({$unnest_approval_1}) ua1 
                                JOIN {$this->md_jabatan_table} p1 ON ua1.position_approval_1_id = p1.id 
                                WHERE p1.status = 1
                                GROUP BY ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id";
            // approval_2_names
            $approval_2_names = "SELECT ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id, array_agg(ua2.position_approval_2_id) AS position_approval_2_id, array_agg(p2.nm_jabatan) AS position_approval_2_names 
                                FROM ({$unnest_approval_2}) ua2 
                                JOIN {$this->md_jabatan_table} p2 ON ua2.position_approval_2_id = p2.id 
                                WHERE p2.status = 1
                                GROUP BY ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id";
            
            $this->db->select("
                p.id as position_id,
                p.nm_jabatan as position_name,
                u.id as temp_id,
                u.nm_unit_kerja as temp_name,
                a.custom_unit_1,
                a.custom_unit_2,
                a.unit_approval_1_id,
                ua1.nm_unit_kerja as unit_approval_1_name,
                a.unit_approval_2_id,
                ua2.nm_unit_kerja as unit_approval_2_name,
                array_to_string(a1.position_approval_1_id, ', ') as position_approval_1_ids,
                array_to_string(a1.position_approval_1_names, ', ') as position_approval_1_names,
                array_to_string(a2.position_approval_2_id, ', ') as position_approval_2_ids,
                array_to_string(a2.position_approval_2_names, ', ') as position_approval_2_names,
                CASE 
                    WHEN a.custom_unit_1 != 0 THEN '3'
                    WHEN a.unit_approval_1_id IS NULL THEN '0'
                    WHEN a.unit_id = a.unit_approval_1_id THEN '1'
                    ELSE '2'
                END AS unit_type_1,
                CASE 
                    WHEN a.custom_unit_2 != 0 THEN '3'
                    WHEN a.unit_approval_2_id IS NULL THEN '0'
                    WHEN a.unit_id = a.unit_approval_2_id THEN '1'
                    ELSE '2'
                END AS unit_type_2,
                CASE 
                    WHEN a.custom_unit_1 != 0 THEN 'Unit Custom'
                    WHEN a.unit_approval_1_id IS NULL THEN ''
                    WHEN a.unit_id = a.unit_approval_1_id THEN 'Unit Pemohon'
                    ELSE 'Unit Induk'
                END AS unit_type_1_name,
                CASE 
                    WHEN a.custom_unit_2 != 0 THEN 'Unit Custom'
                    WHEN a.unit_approval_2_id IS NULL THEN ''
                    WHEN a.unit_id = a.unit_approval_2_id THEN 'Unit Pemohon'
                    ELSE 'Unit Induk'
                END AS unit_type_2_name", false);
            $this->db->from("{$this->md_jabatan_table} p");
            $this->db->join("{$this->md_unit_kerja_table} u", '1=1', 'CROSS');
            $this->db->join("{$this->table} a", 'a.position_id = p.id and a.unit_id = u.id', 'left');
            $this->db->join("{$this->md_unit_kerja_table} ua1", 'ua1.id = a.unit_approval_1_id', 'left');
            $this->db->join("{$this->md_unit_kerja_table} ua2", 'ua2.id = a.unit_approval_2_id', 'left');
            $this->db->join("({$approval_1_names}) a1", 'a.unit_id = a1.unit_id AND a.position_id = a1.position_id AND a.unit_approval_1_id = a1.unit_approval_1_id', 'left', false);
            $this->db->join("({$approval_2_names}) a2", 'a.unit_id = a2.unit_id AND a.position_id = a2.position_id AND a.unit_approval_2_id = a2.unit_approval_2_id', 'left', false);
            $this->db->where('p.status', 1);
            $this->db->where('u.status', 1);

            return $this->db->count_all_results();
        }
    }

    public function count_filtered() {
        $query = $this->_get_datatables_query();
        return $this->db->query($query)->num_rows();
    }

    public function exists($data) {
        $this->db->select("{$this->table}.unit_id, {$this->table}.position_id, {$this->md_unit_kerja_table}.nm_unit_kerja as unit_name, {$this->md_jabatan_table}.nm_jabatan as position_name")
                 ->from($this->table)
                 ->join($this->md_unit_kerja_table, "{$this->table}.unit_id = {$this->md_unit_kerja_table}.id", 'left')
                 ->join($this->md_jabatan_table, "{$this->table}.position_id = {$this->md_jabatan_table}.id", 'left')
                 ->where("{$this->table}.unit_id", $data['unit_id'])
                 ->where("{$this->table}.position_id", $data['position_id']);
        $result = $this->db->get();
        return [
            'is_exists' => $result->num_rows() > 0,
            'data' => $result->row()
        ];
    }

    public function store($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($data) {
        return $this->db->update($this->table, $data, ['unit_id' => $data['unit_id'], 'position_id' => $data['position_id']]);
    }

    public function delete_unit_approval($unit_id, $position_id) {
        return $this->db->delete($this->table, ['unit_id' => $unit_id, 'position_id' => $position_id]);
    }

    public function delete_unit_type_approval($unit_type_id, $position_id) {
        $this->db->select("{$this->table}.unit_id, {$this->table}.position_id");
        $this->db->from($this->table);
        $this->db->join($this->md_unit_kerja_table, "{$this->md_unit_kerja_table}.id = {$this->table}.unit_id", 'left');
        $this->db->join($this->md_jenis_unit_kerja_table, "{$this->md_jenis_unit_kerja_table}.id = {$this->md_unit_kerja_table}.id_jenis_unit_kerja", 'left');
        $this->db->where("{$this->md_jenis_unit_kerja_table}.id", $unit_type_id);
        $this->db->where("{$this->table}.position_id", $position_id);
        $subquery = $this->db->get_compiled_select();

        $this->db->where("($this->table.unit_id, $this->table.position_id) IN ($subquery)", NULL, FALSE);
        return $this->db->delete($this->table);
    }

    public function get_by_unit_type_id_position_id($unit_type_id, $position_id) {
        // Subquery for filtered_unit_counts
        $subquery_filtered_unit_counts = "(SELECT u.position_id, u.unit_id, u.unit_approval_1_id, u.position_approval_1_id, u.unit_approval_2_id, u.position_approval_2_id, u.custom_unit_1, u.custom_unit_2
        FROM (
            SELECT a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2, COUNT(*) AS count
            FROM {$this->table} a
            JOIN {$this->md_unit_kerja_table} u ON a.unit_id = u.id
            JOIN {$this->md_jenis_unit_kerja_table} j ON u.id_jenis_unit_kerja = j.id
            WHERE j.id = $unit_type_id 
                AND a.position_id = $position_id
                AND a.unit_id IS NOT NULL
                AND a.unit_approval_1_id IS NOT NULL
                AND a.unit_approval_2_id IS NOT NULL
                AND a.custom_unit_1 IS NOT NULL
                AND a.custom_unit_2 IS NOT NULL
            GROUP BY a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2
        ) u
        JOIN (
            SELECT position_id, MAX(count) AS max_count
            FROM (
                SELECT a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_2, COUNT(*) AS count
                FROM {$this->table} a
                JOIN {$this->md_unit_kerja_table} u ON a.unit_id = u.id
                JOIN {$this->md_jenis_unit_kerja_table} j ON u.id_jenis_unit_kerja = j.id
                WHERE j.id = $unit_type_id 
                    AND a.position_id = $position_id
                    AND a.unit_id IS NOT NULL
                    AND a.unit_approval_1_id IS NOT NULL
                    AND a.unit_approval_2_id IS NOT NULL
                    AND a.custom_unit_1 IS NOT NULL
                    AND a.custom_unit_2 IS NOT NULL
                GROUP BY a.unit_id, a.position_id, a.unit_approval_1_id, a.position_approval_1_id, a.unit_approval_2_id, a.position_approval_2_id, a.custom_unit_1, a.custom_unit_2
            ) uc
            GROUP BY position_id
        ) m ON u.position_id = m.position_id AND u.count = m.max_count)";

        // Subquery for filtered_approval_counts_1
        $subquery_filtered_approval_counts_1 = "(SELECT a.position_id, a.position_approval_1_id, a.count, a.array_length
        FROM (
            SELECT a.position_id, a.position_approval_1_id, COUNT(*) AS count,
                array_length(string_to_array(replace(replace(a.position_approval_1_id::text, '[', ''), ']', ''), ','), 1) AS array_length
            FROM {$subquery_filtered_unit_counts} a
            JOIN md_unit_kerja u ON a.unit_id = u.id
            JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
            WHERE j.id = $unit_type_id 
                AND a.position_id = $position_id
                AND a.position_approval_1_id IS NOT NULL
            GROUP BY a.position_id, a.position_approval_1_id
        ) a
        JOIN (
            SELECT position_id, MAX(count) AS max_count
            FROM (
                SELECT a.position_id, a.position_approval_1_id, COUNT(*) AS count,
                    array_length(string_to_array(replace(replace(a.position_approval_1_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                FROM {$subquery_filtered_unit_counts} a
                JOIN md_unit_kerja u ON a.unit_id = u.id
                JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                WHERE j.id = $unit_type_id 
                    AND a.position_id = $position_id
                    AND a.position_approval_1_id IS NOT NULL
                GROUP BY a.position_id, a.position_approval_1_id
            ) ac1
            GROUP BY position_id
        ) m ON a.position_id = m.position_id AND a.count = m.max_count
        ORDER BY a.position_id, a.array_length DESC)";

        // Subquery for filtered_approval_counts_2
        $subquery_filtered_approval_counts_2 = "(SELECT a.position_id, a.position_approval_2_id, a.count, a.array_length
        FROM (
            SELECT a.position_id, a.position_approval_2_id, COUNT(*) AS count,
                array_length(string_to_array(replace(replace(a.position_approval_2_id::text, '[', ''), ']', ''), ','), 1) AS array_length
            FROM {$subquery_filtered_unit_counts} a
            JOIN md_unit_kerja u ON a.unit_id = u.id
            JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
            WHERE j.id = $unit_type_id 
                AND a.position_id = $position_id
                AND a.position_approval_2_id IS NOT NULL
            GROUP BY a.position_id, a.position_approval_2_id
        ) a
        JOIN (
            SELECT position_id, MAX(count) AS max_count
            FROM (
                SELECT a.position_id, a.position_approval_2_id, COUNT(*) AS count,
                    array_length(string_to_array(replace(replace(a.position_approval_2_id::text, '[', ''), ']', ''), ','), 1) AS array_length
                FROM {$subquery_filtered_unit_counts} a
                JOIN md_unit_kerja u ON a.unit_id = u.id
                JOIN md_jenis_unit_kerja j ON u.id_jenis_unit_kerja = j.id
                WHERE j.id = $unit_type_id 
                    AND a.position_id = $position_id
                    AND a.position_approval_2_id IS NOT NULL
                GROUP BY a.position_id, a.position_approval_2_id
            ) ac2
            GROUP BY position_id
        ) m ON a.position_id = m.position_id AND a.count = m.max_count
        ORDER BY a.position_id, a.array_length DESC)";

        // unnest_approval_1
        $unnest_approval_1 = "SELECT fa1.position_id, 
            unnest(string_to_array(replace(replace(fa1.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
            FROM ($subquery_filtered_approval_counts_1) fa1";
        
        // unnest_approval_2
        $unnest_approval_2 = "SELECT fa2.position_id, 
            unnest(string_to_array(replace(replace(fa2.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
            FROM ($subquery_filtered_approval_counts_2) fa2";

        // approval_1_names
        $approval_1_names = "SELECT ua1.position_id, array_agg(ua1.position_approval_1_id) AS position_approval_1_ids, array_agg(p1.nm_jabatan) AS position_approval_1_names 
            FROM ({$unnest_approval_1}) ua1 
            JOIN {$this->md_jabatan_table} p1 ON ua1.position_approval_1_id = p1.id 
            WHERE p1.status = 1
            GROUP BY ua1.position_id";

        // approval_2_names
        $approval_2_names = "SELECT ua2.position_id, array_agg(ua2.position_approval_2_id) AS position_approval_2_ids, array_agg(p2.nm_jabatan) AS position_approval_2_names 
            FROM ({$unnest_approval_2}) ua2 
            JOIN {$this->md_jabatan_table} p2 ON ua2.position_approval_2_id = p2.id 
            WHERE p2.status = 1
            GROUP BY ua2.position_id";

        $this->db->select("
            DISTINCT ON (p.id, j.id)
            j.id AS temp_id,
            j.jenis AS temp_name,
            p.id AS position_id,
            p.nm_jabatan AS position_name,
            f.custom_unit_1,
            f.custom_unit_2,
            f.unit_approval_1_id,
            f.unit_approval_2_id,
            array_to_string(a1.position_approval_1_ids, ', ') as position_approval_1_ids,
            array_to_string(a1.position_approval_1_names, ', ') as position_approval_1_names,
            array_to_string(a2.position_approval_2_ids, ', ') as position_approval_2_ids,
            array_to_string(a2.position_approval_2_names, ', ') as position_approval_2_names,
            CASE 
                WHEN f.custom_unit_1 != 0 THEN '3'
                WHEN f.unit_approval_1_id IS NULL THEN '0'
                WHEN f.unit_id = f.unit_approval_1_id THEN '1'
                ELSE '2'
            END AS unit_type_1,
            CASE 
                WHEN f.custom_unit_2 != 0 THEN '3'
                WHEN f.unit_approval_2_id IS NULL THEN '0'
                WHEN f.unit_id = f.unit_approval_2_id THEN '1'
                ELSE '2'
            END AS unit_type_2,
            CASE 
                WHEN f.custom_unit_1 != 0 THEN 'Unit Custom'
                WHEN f.unit_approval_1_id IS NULL THEN ''
                WHEN f.unit_id = f.unit_approval_1_id THEN 'Unit Pemohon'
                ELSE 'Unit Induk'
            END AS unit_type_1_name,
            CASE 
                WHEN f.custom_unit_2 != 0 THEN 'Unit Custom'
                WHEN f.unit_approval_2_id IS NULL THEN ''
                WHEN f.unit_id = f.unit_approval_2_id THEN 'Unit Pemohon'
                ELSE 'Unit Induk'
            END AS unit_type_2_name
        ", false);
        $this->db->from("{$this->md_jabatan_table} p");
        $this->db->join("{$this->md_jenis_unit_kerja_table} j", '1=1', 'CROSS');
        $this->db->join("($subquery_filtered_unit_counts) f", 'p.id = f.position_id', 'left', false);
        $this->db->join("($approval_1_names) a1", 'p.id = a1.position_id', 'left', false);
        $this->db->join("($approval_2_names) a2", 'p.id = a2.position_id', 'left', false);
        $this->db->where('j.id', $unit_type_id);
        $this->db->where('p.id', $position_id);
        $this->db->where('p.status', 1);
        $this->db->where('j.status', 1);
        $this->db->group_by([
            'j.id', 'j.jenis', 'p.id', 'p.nm_jabatan', 'f.unit_id', 'f.unit_approval_1_id', 
            'f.unit_approval_2_id', 'f.custom_unit_1', 'f.custom_unit_2', 
            'a1.position_approval_1_ids', 'a1.position_approval_1_names', 
            'a2.position_approval_2_ids', 'a2.position_approval_2_names'
        ]);
        
        $this->db->order_by('p.id', 'ASC');
        $this->db->order_by('j.id', 'ASC');
        return $this->db->get()->row_array();
    }

    public function get_by_unit_id_position_id($unit_id, $position_id) {
        // unnest_approval_1
        $unnest_approval_1 = "SELECT a.unit_id, a.position_id, a.unit_approval_1_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                            FROM {$this->table} a";
        
        // unnest_approval_2
        $unnest_approval_2 = "SELECT a.unit_id, a.position_id, a.unit_approval_2_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                            FROM {$this->table} a";
        
        // approval_1_names
        $approval_1_names = "SELECT ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id, array_agg(ua1.position_approval_1_id) AS position_approval_1_id, array_agg(p1.nm_jabatan) AS position_approval_1_names 
                            FROM ({$unnest_approval_1}) ua1 
                            JOIN {$this->md_jabatan_table} p1 ON ua1.position_approval_1_id = p1.id 
                            WHERE p1.status = 1
                            GROUP BY ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id";
        // approval_2_names
        $approval_2_names = "SELECT ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id, array_agg(ua2.position_approval_2_id) AS position_approval_2_id, array_agg(p2.nm_jabatan) AS position_approval_2_names 
                            FROM ({$unnest_approval_2}) ua2 
                            JOIN {$this->md_jabatan_table} p2 ON ua2.position_approval_2_id = p2.id 
                            WHERE p2.status = 1
                            GROUP BY ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id";
        
        $this->db->select("
            a.position_id,
            p.nm_jabatan as position_name,
            a.unit_id as temp_id,
            u.nm_unit_kerja as temp_name,
            a.custom_unit_1,
            a.custom_unit_2,
            a.unit_approval_1_id,
            a.unit_approval_2_id, 
            array_to_string(a1.position_approval_1_id, ', ') as position_approval_1_ids,
            array_to_string(a1.position_approval_1_names, ', ') as position_approval_1_names,
            array_to_string(a2.position_approval_2_id, ', ') as position_approval_2_ids,
            array_to_string(a2.position_approval_2_names, ', ') as position_approval_2_names,
            CASE 
                WHEN a.custom_unit_1 != 0 THEN '3'
                WHEN a.unit_approval_1_id IS NULL THEN '0'
                WHEN a.unit_id = a.unit_approval_1_id THEN '1'
                ELSE '2'
            END AS unit_type_1,
            CASE 
                WHEN a.custom_unit_2 != 0 THEN '3'
                WHEN a.unit_approval_2_id IS NULL THEN '0'
                WHEN a.unit_id = a.unit_approval_2_id THEN '1'
                ELSE '2'
            END AS unit_type_2,
            CASE 
                WHEN a.custom_unit_1 != 0 THEN 'Unit Custom'
                WHEN a.unit_approval_1_id IS NULL THEN ''
                WHEN a.unit_id = a.unit_approval_1_id THEN 'Unit Pemohon'
                ELSE 'Unit Induk'
            END AS unit_type_1_name,
            CASE 
                WHEN a.custom_unit_2 != 0 THEN 'Unit Custom'
                WHEN a.unit_approval_2_id IS NULL THEN ''
                WHEN a.unit_id = a.unit_approval_2_id THEN 'Unit Pemohon'
                ELSE 'Unit Induk'
            END AS unit_type_2_name", false);
        $this->db->from("{$this->md_jabatan_table} p");
        $this->db->join("{$this->md_unit_kerja_table} u", '1=1', 'CROSS');
        $this->db->join("{$this->table} a", 'a.position_id = p.id and a.unit_id = u.id', 'left');
        $this->db->join("({$approval_1_names}) a1", 'a.unit_id = a1.unit_id AND a.position_id = a1.position_id AND a.unit_approval_1_id = a1.unit_approval_1_id', 'left', false);
        $this->db->join("({$approval_2_names}) a2", 'a.unit_id = a2.unit_id AND a.position_id = a2.position_id AND a.unit_approval_2_id = a2.unit_approval_2_id', 'left', false);
        $this->db->where('p.status', 1);
        $this->db->where('u.status', 1);
        $this->db->where("u.id", $unit_id);
        $this->db->where("p.id", $position_id);
        return $this->db->get()->row_array();
    }

    public function get_by_unit_id_position_id_in_hist_pelaksana_jabatan($unit_id, $position_id) {
        // unnest_approval_1
        $unnest_approval_1 = "SELECT a.unit_id, a.position_id, a.unit_approval_1_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                            FROM {$this->table} a";

        // unnest_approval_2
        $unnest_approval_2 = "SELECT a.unit_id, a.position_id, a.unit_approval_2_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                            FROM {$this->table} a";

        // approval_1_emp
        $approval_1_emp = "SELECT ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id, ua1.position_approval_1_id, 
                            j.nm_jabatan as position_approval_1_name, e.nama as employee_name, e.nrik as employee_npp 
                            FROM ({$unnest_approval_1}) ua1 
                            LEFT JOIN {$this->hist_pelaksana_jabatan_table} h ON ua1.unit_approval_1_id = h.ditempatkan_diganti AND ua1.position_approval_1_id = h.id_jabatan_diganti 
                            LEFT JOIN {$this->md_jabatan_table} j ON j.id = ua1.position_approval_1_id 
                            LEFT JOIN {$this->emp_data_peg_table} e ON e.id_peg = h.id_peg 
                            WHERE h.status = 1 AND e.status = 1 AND h.tgl_selesai > CURRENT_DATE";

        // approval_2_emp
        $approval_2_emp = "SELECT ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id, ua2.position_approval_2_id, 
                            j.nm_jabatan as position_approval_2_name, e.nama as employee_name, e.nrik as employee_npp 
                            FROM ({$unnest_approval_2}) ua2 
                            LEFT JOIN {$this->hist_pelaksana_jabatan_table} h ON ua2.unit_approval_2_id = h.ditempatkan_diganti AND ua2.position_approval_2_id = h.id_jabatan_diganti 
                            LEFT JOIN {$this->md_jabatan_table} j ON j.id = ua2.position_approval_2_id 
                            LEFT JOIN {$this->emp_data_peg_table} e ON e.id_peg = h.id_peg 
                            WHERE h.status = 1 AND e.status = 1 AND h.tgl_selesai > CURRENT_DATE";

        $this->db->select("a1.unit_approval_1_id as unit_approval_1_id, a1.position_approval_1_id as position_approval_1_id, 
                            a1.position_approval_1_name as position_approval_1_name, a1.employee_name as approval_1_name, 
                            a1.employee_npp as approval_1_npp, u1.nm_unit_kerja as unit_approval_1_name, 
                            a2.unit_approval_2_id as unit_approval_2_id, a2.position_approval_2_id as position_approval_2_id, 
                            a2.position_approval_2_name as position_approval_2_name, a2.employee_name as approval_2_name, 
                            a2.employee_npp as approval_2_npp, u2.nm_unit_kerja as unit_approval_2_name", false);
        $this->db->from("{$this->table} a", false);
        $this->db->join("({$approval_1_emp}) a1", 'a.unit_id = a1.unit_id AND a.position_id = a1.position_id AND a.unit_approval_1_id = a1.unit_approval_1_id', 'left', false);
        $this->db->join("{$this->md_unit_kerja_table} u1", 'a1.unit_approval_1_id = u1.id', 'left');
        $this->db->join("({$approval_2_emp}) a2", 'a.unit_id = a2.unit_id AND a.position_id = a2.position_id AND a.unit_approval_2_id = a2.unit_approval_2_id', 'left', false);
        $this->db->join("{$this->md_unit_kerja_table} u2", 'a2.unit_approval_2_id = u2.id', 'left');
        $this->db->where('a.unit_id', $unit_id);
        $this->db->where('a.position_id', $position_id);

        return $this->db->get()->result();
    }

    public function get_by_unit_id_position_id_in_hist_ndpenugasan($unit_id, $position_id) {
        // unnest_approval_1
        $unnest_approval_1 = "SELECT a.unit_id, a.position_id, a.unit_approval_1_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                            FROM {$this->table} a";

        // unnest_approval_2
        $unnest_approval_2 = "SELECT a.unit_id, a.position_id, a.unit_approval_2_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                            FROM {$this->table} a";

        // approval_1_emp
        $approval_1_emp = "SELECT ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id, ua1.position_approval_1_id, 
                            j.nm_jabatan as position_approval_1_name, e.nama as employee_name, e.nrik as employee_npp 
                            FROM ({$unnest_approval_1}) ua1 
                            LEFT JOIN {$this->hist_ndpenugasan_table} h ON ua1.unit_approval_1_id = h.id_uker_ndpenugasan AND ua1.position_approval_1_id = h.tmp_jabatan 
                            LEFT JOIN {$this->md_jabatan_table} j ON j.id = ua1.position_approval_1_id 
                            LEFT JOIN {$this->emp_data_peg_table} e ON e.id_peg = h.id_peg 
                            WHERE h.status = 1 AND e.status = 1 AND h.tgl_selesai > CURRENT_DATE";

        // approval_2_emp
        $approval_2_emp = "SELECT ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id, ua2.position_approval_2_id, 
                            j.nm_jabatan as position_approval_2_name, e.nama as employee_name, e.nrik as employee_npp 
                            FROM ({$unnest_approval_2}) ua2 
                            LEFT JOIN {$this->hist_ndpenugasan_table} h ON ua2.unit_approval_2_id = h.id_uker_ndpenugasan AND ua2.position_approval_2_id = h.tmp_jabatan 
                            LEFT JOIN {$this->md_jabatan_table} j ON j.id = ua2.position_approval_2_id 
                            LEFT JOIN {$this->emp_data_peg_table} e ON e.id_peg = h.id_peg 
                            WHERE h.status = 1 AND e.status = 1 AND h.tgl_selesai > CURRENT_DATE";

        $this->db->select("a1.unit_approval_1_id as unit_approval_1_id, a1.position_approval_1_id as position_approval_1_id, 
                            a1.position_approval_1_name as position_approval_1_name, a1.employee_name as approval_1_name, 
                            a1.employee_npp as approval_1_npp, u1.nm_unit_kerja as unit_approval_1_name, 
                            a2.unit_approval_2_id as unit_approval_2_id, a2.position_approval_2_id as position_approval_2_id, 
                            a2.position_approval_2_name as position_approval_2_name, a2.employee_name as approval_2_name, 
                            a2.employee_npp as approval_2_npp, u2.nm_unit_kerja as unit_approval_2_name", false);
        $this->db->from("{$this->table} a", false);
        $this->db->join("({$approval_1_emp}) a1", 'a.unit_id = a1.unit_id AND a.position_id = a1.position_id AND a.unit_approval_1_id = a1.unit_approval_1_id', 'left', false);
        $this->db->join("{$this->md_unit_kerja_table} u1", 'a1.unit_approval_1_id = u1.id', 'left');
        $this->db->join("({$approval_2_emp}) a2", 'a.unit_id = a2.unit_id AND a.position_id = a2.position_id AND a.unit_approval_2_id = a2.unit_approval_2_id', 'left', false);
        $this->db->join("{$this->md_unit_kerja_table} u2", 'a2.unit_approval_2_id = u2.id', 'left');
        $this->db->where('a.unit_id', $unit_id);
        $this->db->where('a.position_id', $position_id);

        return $this->db->get()->result();
    }

    public function get_by_unit_id_position_id_in_hist_jabatan($unit_id, $position_id) {
        // unnest_approval_1
        $unnest_approval_1 = "SELECT a.unit_id, a.position_id, a.unit_approval_1_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_1_id, '[', ''), ']', ''), ','))::int AS position_approval_1_id 
                            FROM {$this->table} a";

        // unnest_approval_2
        $unnest_approval_2 = "SELECT a.unit_id, a.position_id, a.unit_approval_2_id, 
                            unnest(string_to_array(replace(replace(a.position_approval_2_id, '[', ''), ']', ''), ','))::int AS position_approval_2_id 
                            FROM {$this->table} a";

        // approval_1_emp
        $approval_1_emp = "SELECT ua1.unit_id, ua1.position_id, ua1.unit_approval_1_id, ua1.position_approval_1_id, 
                            j.nm_jabatan as position_approval_1_name, e.nama as employee_name, e.nrik as employee_npp 
                            FROM ({$unnest_approval_1}) ua1 
                            LEFT JOIN {$this->hist_jabatan_table} hj ON ua1.position_approval_1_id = hj.id_jabatan 
                            LEFT JOIN {$this->md_jabatan_table} j ON j.id = ua1.position_approval_1_id 
                            LEFT JOIN {$this->hist_unit_kerja_table} h ON ua1.unit_approval_1_id = h.ditempatkan_di AND hj.id_peg = h.id_peg 
                            LEFT JOIN {$this->emp_data_peg_table} e ON e.id_peg = hj.id_peg 
                            WHERE hj.status = 1 AND h.status = 1 AND e.status = 1";

        $approval_2_emp = "SELECT ua2.unit_id, ua2.position_id, ua2.unit_approval_2_id, ua2.position_approval_2_id, 
                            j.nm_jabatan as position_approval_2_name, e.nama as employee_name, e.nrik as employee_npp 
                            FROM ({$unnest_approval_2}) ua2 
                            LEFT JOIN {$this->hist_jabatan_table} hj ON ua2.position_approval_2_id = hj.id_jabatan 
                            LEFT JOIN {$this->md_jabatan_table} j ON j.id = ua2.position_approval_2_id 
                            LEFT JOIN {$this->hist_unit_kerja_table} h ON ua2.unit_approval_2_id = h.ditempatkan_di AND hj.id_peg = h.id_peg 
                            LEFT JOIN {$this->emp_data_peg_table} e ON e.id_peg = hj.id_peg 
                            WHERE hj.status = 1 AND h.status = 1 AND e.status = 1";

        // semua bagian
        $this->db->select("a1.unit_approval_1_id as unit_approval_1_id, a1.position_approval_1_id as position_approval_1_id, 
                            a1.position_approval_1_name as position_approval_1_name, a1.employee_name as approval_1_name, 
                            a1.employee_npp as approval_1_npp, u1.nm_unit_kerja as unit_approval_1_name, 
                            a2.unit_approval_2_id as unit_approval_2_id, a2.position_approval_2_id as position_approval_2_id, 
                            a2.position_approval_2_name as position_approval_2_name, a2.employee_name as approval_2_name, 
                            a2.employee_npp as approval_2_npp, u2.nm_unit_kerja as unit_approval_2_name", false);
        $this->db->from("{$this->table} a", false);
        $this->db->join("({$approval_1_emp}) a1", 'a.unit_id = a1.unit_id AND a.position_id = a1.position_id AND a.unit_approval_1_id = a1.unit_approval_1_id', 'left', false);
        $this->db->join("{$this->md_unit_kerja_table} u1", 'a1.unit_approval_1_id = u1.id', 'left');
        $this->db->join("({$approval_2_emp}) a2", 'a.unit_id = a2.unit_id AND a.position_id = a2.position_id AND a.unit_approval_2_id = a2.unit_approval_2_id', 'left', false);
        $this->db->join("{$this->md_unit_kerja_table} u2", 'a2.unit_approval_2_id = u2.id', 'left');
        $this->db->where('a.unit_id', $unit_id);
        $this->db->where('a.position_id', $position_id);

        return $this->db->get()->result();
    }
}