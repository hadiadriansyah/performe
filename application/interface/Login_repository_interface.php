<?php
interface Login_repository_interface {
    // adm_user_model
    public function check_credentials_emp($nrik, $password);
    // last_login_model
    public function last_login($login_info);
    public function last_login_info($user_id);
    // user_model
    public function check_credentials($email, $password);
}