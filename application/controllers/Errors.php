<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Errors extends Base_controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function page_not_found() {
        $this->output->set_status_header('404');
        $data = [
            'title' => '404 Page Not Found'
        ];
        $this->template->load('main/layout', 'errors/error_404', $data);
    }
    
    public function access_denied() {
        $this->is_logged_in();
        $this->output->set_status_header('403');
        $data = array_merge($this->global, [
            'title' => '403 Access Denied'
        ]);
        $this->template->load('admin/layout', 'errors/error_403', $data);
    }
}