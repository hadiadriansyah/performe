<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing_page extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $data = [
            'title' => 'Home',
            'js' => [
                '../vendors/orgchart.js/orgchart.js',
                'main/landing.js'
            ]
        ];
        $this->template->load('main/layout', 'main/landing', $data);
    }
}