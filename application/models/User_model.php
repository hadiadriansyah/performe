<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    protected $table = 'npm_users';
    protected $column_order = array(null, 'name', 'email', 'profile_picture', 'is_active', 'created_at');
    protected $column_search = array('name', 'email');
    protected $order = array('name' => 'asc');


    public function get_datatables() {
        $this->_get_datatables_query();
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }
    
    private function _get_datatables_query() {
        $this->db->from($this->table);

        $is_active = $this->input->post('is_active');

        if ($is_active !== 'all' && ($is_active === '0' || !empty($is_active))) {
            $this->db->where("is_active", $is_active);
        }

        $search_value = strtolower($_POST['search']['value'] ?? '');
        if ($search_value) {
            $this->db->group_start();
            foreach ($this->column_search as $i => $item) {
                $field = "{$this->table}.$item ILIKE";
                if ($i === 0) {
                    $this->db->where("$field", "%$search_value%");
                } else {
                    $this->db->or_where("$field", "%$search_value%");
                }
            }
            $this->db->group_end();
        }
        
        $order_column = $_POST['order'][0]['column'] ?? null;
        $order_dir = $_POST['order'][0]['dir'] ?? null;

        if (!is_null($order_column) && !is_null($order_dir) && isset($this->column_order[$order_column])) {
            $this->db->order_by($this->column_order[$order_column], $order_dir);
        } else {
            foreach ($this->order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
    }

    public function count_all() {
        return $this->db->from($this->table)->count_all_results();
    }

    public function count_filtered() {
        $this->_get_datatables_query();
        return $this->db->get()->num_rows();
    }

    #####
    
    public function exists(array $data) {
        $this->db->select("{$this->table}.email")
                    ->from($this->table)
                    ->where('email', $data['email']);
        
        $result = $this->db->get();
        return [
            'is_exists' => $result->num_rows() > 0,
            'data' => $result->row()
        ];
    }

    public function unique(array $data) {
        $this->db->select("{$this->table}.email")
                    ->from($this->table)
                    ->where("{$this->table}.id !=", $data['id'])
                    ->where('email', $data['email']);
              
        $result = $this->db->get();
        
        return [
            'is_unique' => $result->num_rows() > 0,
            'data' => $result->row()
        ];
    }

    #####

    public function store($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update(array $data) {
        return $this->db->update($this->table, $data, ['id' => $data['id']]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }
    
    #####

    public function get_by_id($id)
    {
        $this->db->select("id, name, email, profile_picture, is_active")
                    ->from($this->table)
                    ->where("$this->table.id", $id);
        return $this->db->get()->row_array();
    }

    #####

    public function check_credentials($email, $password) {
        $this->db->select("{$this->table}.id, {$this->table}.name, {$this->table}.email, {$this->table}.profile_picture, {$this->table}.is_active");
        $this->db->from($this->table);
        $this->db->where("{$this->table}.email", $email);
        $query = $this->db->get();

        if ($query->num_rows() === 1) {
            $hashed_password = $this->db->select("password")->from($this->table)->where("email", $email)->get()->row()->password;
            if (password_verify($password, $hashed_password)) {
                return $query->row();
            } else {
                return null;
            }
        } else {
            return null;
        }   
    }
}