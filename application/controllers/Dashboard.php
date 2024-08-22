<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_controller.php';

class Dashboard extends Base_controller {
    public function __construct()
    {
        parent::__construct();
        $this->is_logged_in();
    }

    public function index() {
        $data = array_merge($this->global, [
            'title' => 'Dashboard',
            'css' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css'
            ],
            'js' => [
                '../vendors/metronic-admin/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js',
                '../vendors/metronic-admin/dist/assets/js/custom/widgets.js',
                '../vendors/metronic-admin/dist/assets/js/custom/apps/chat/chat.js',
                '../vendors/metronic-admin/dist/assets/js/custom/modals/create-app.js',
                '../vendors/metronic-admin/dist/assets/js/custom/modals/upgrade-plan.js'
            ]
        ]);
        $this->template->load('admin/layout', 'admin/dashboard', $data);
    }
}