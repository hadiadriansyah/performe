<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class : BaseController
 * Base Class to control over all the classes
 * @version : 1.1
 * @since : 23 Mei 2024
 */
#[\AllowDynamicProperties]
class Base_controller extends CI_Controller {
	protected $global = [];
	protected $vendor_id = '';
	protected $name = '';
	protected $is_admin = 0;
	protected $employee_id = '';
	protected $employee_status = 0;
	protected $employee_position = '';
	protected $employee_unit_id = '';
	protected $profile_picture = '';
	protected $last_login = '';
	
	protected $year_period_model;

	public function __construct() {
		parent::__construct();

		$this->load->model('Year_period_model');
        $this->year_period_model = $this->Year_period_model;
	}


	function active_year_period() {
		$current_year_period = $this->year_period_model->get_active_year_period();
		
		return $current_year_period;
	}

	function is_employee_access() {
		$allowed_position_groups = ['218', '201', '222', '140', '166', '167'];
		if (($this->is_admin != SYSTEM_ADMIN) && !in_array($this->session->userdata('employee_position_group'), $allowed_position_groups)) {
			redirect('errors/access_denied');
		}
	}

	#####
	
	function is_logged_in() {
		$is_logged_in = $this->session->userdata('is_logged_in');
		
		if (!isset($is_logged_in) || $is_logged_in != TRUE) {
			redirect();
		} else {
			$this->set_user_session_data();
		}
	}
	
	private function set_user_session_data() {
		$this->vendor_id = $this->session->userdata('user_id');
		$this->name = $this->session->userdata('name');
		$this->last_login = $this->session->userdata('last_login');
		$this->is_admin = $this->session->userdata('is_admin');
		$this->employee_id = $this->session->userdata('employee_id');
		$this->employee_status = $this->session->userdata('employee_status');
		$this->employee_position = $this->session->userdata('employee_position');
		$this->employee_unit_id = $this->session->userdata('employee_unit_id');
		$this->profile_picture = $this->session->userdata('profile_picture');
		
		$this->global = [
			'vendor_id' => $this->vendor_id,
			'name' => $this->name,
			'last_login' => $this->last_login,
			'is_admin' => $this->is_admin,
			'employee_id' => $this->employee_id,
			'employee_status' => $this->employee_status,
			'employee_position' => $this->employee_position,
			'employee_unit_id' => $this->employee_unit_id,
			'profile_picture' => $this->profile_picture
		];
	}
	
	function is_admin() {
		return $this->is_admin == SYSTEM_ADMIN;
	}

	function is_admin_access() {
		if ($this->is_admin != SYSTEM_ADMIN) {
			redirect('errors/access_denied');
		}
	}
	
	function logout() {
		$this->session->sess_destroy();
		redirect();
	}
}
