<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Overview extends Base_controller {
    
    protected $repository;

    public function __construct() {
        parent::__construct();
        // $this->load->repository('profile/Overview_repository');
        // $this->repository = new Overview_repository();

        $this->is_logged_in();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Profile | Overview'
        ]);
        $this->template->load('admin/layout', 'admin/profile/overview', $data);
    }
}