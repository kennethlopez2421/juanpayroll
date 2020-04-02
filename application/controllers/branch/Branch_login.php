<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Branch_login extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('branch/branch_login_model');
  }

  public function logout() {
    $this->session->sess_destroy();
    $this->load->view('login');
  }

  public function isLoggedIn() {
    //this will destroy the session if the user not logged in
    if($this->session->userdata('isLoggedIn') == false) {
      if(empty($this->session->userdata('position_id'))) { //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
      }
    }
  }

  public function login(){
    $company_code = $this->input->post('company_code');
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $login_type = $this->input->post('login_type');


    if(empty($company_code) || empty($username) || empty($password)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $company = $this->branch_login_model->get_company_code($company_code);
    if($company->num_rows() == 0){
      $data = array("success" => 0, "message" => "Invalid Company Code");
      generate_json($data);
      exit();
    }

    $company = $company->row_array();
    $this->session->set_userdata('database_name', $company['database_name']);
    $this->db = switch_database($company['database_name']);

    $validate_username = $this->model->validate_username($username);
    if($validate_username->num_rows() == 0){
      $data = array("success" => 0, "message" => 'The username you\'ve entered doesn\'t match any account. <a href="'.base_url('Main/register').'">Sign up for an account.</a>');
      $this->session->sess_destroy();
      generate_json($data);
      exit();
    }

    $user = $validate_username->row();
    $pos_id = $user->pos_lvl;

    $unverified_username = $user->enabled;
    if($unverified_username == 0){
      $data = array("success" => 0, "message" => 'The account you\'ve entered is unverified account.');
      $this->session->sess_destroy();
      generate_json($data);
      exit();
    }

    $userObj = $validate_username->row(); //get the data for fetch
    if($userObj->pos_lvl <= 1 && empty($login_type)){
      $login_type = 'admin';
    }

    $position_access = ($login_type == "")
    ? $this->model->get_position_details_access_emp()->row()
    : $this->model->get_position_details_access($userObj->position_id)->row();

    if($login_type == 'admin'){
      $employee_lvl = $this->model->get_position_details_access_emp()->row()->hierarchy_lvl;
      $user_lvl = $position_access->hierarchy_lvl;
      if($user_lvl >= $employee_lvl){
        $data = array("success" => 0, "message" => "You are not authorized to login in admin portal");
        $this->session->sess_destroy();
        generate_json($data);
        exit();
      }
    }

    $hash_password = $user->password;
    if(password_verify($password, $hash_password)){
      $userData = array( // store in array
          'user_id'	  => $userObj->user_id,
          'emp_idno' => $userObj->employee_idno,
          'username'    => $userObj->username,
          'firstname'	  => $userObj->user_fname,
          'middlename'  => $userObj->user_mname,
          'lastname'	  => $userObj->user_lname,
          'position_id' => $userObj->position_id,
          'position_lvl' => $userObj->pos_lvl,
          'deptId' => $userObj->deptId,
          'get_position_access' => $position_access,
          'enabled'     => $userObj->enabled,
          'company_code' => en_dec('en',$company_code),
          'isLoggedIn'  => true,
          'avatar_file' => $userObj->avatar_file,
      );

      $this->session->set_userdata($userData); // set session

      $token_session = uniqid();
      $token_arr = array( // store token in array
        'token_session'	=> $token_session,
      );

      $this->session->set_userdata($token_arr);

      $token = en_dec('en', $token_session);
      $config['sess_expiration'] = 36000;
      $data = array(
        'success' => 1,
        'message' => 'Login Successfully',
        'token_session' => $token
      );
    }else{
      $data = array(
				'success' => 0,
				'message' => 'The username you\'ve entered doesn\'t match any account. <a href="'.base_url('Main/register').'">Sign up for an account.</a>'
			);
      $this->session->sess_destroy();
    }

    generate_json($data);

  }

  public function login_admin(){
    $this->isLoggedIn();
    $bcode = $this->input->post('bcode');
    $key = $this->input->post('token');
    $timezone = $this->input->post('timezone');

    date_default_timezone_set($timezone);
    $secret_key = secret_key($timezone);
    $given_key = en_dec('dec',$key);
    $given_key = en_dec('en',json_encode(array($given_key,date("Y-m-d"))));

    if($secret_key != $given_key){
      $data = array("success" => 0, "message" => "Invalid Key.");
      generate_json($data);
      exit();
    }

    $company = $this->branch_login_model->get_company_code($bcode);
    if($company->num_rows() == 0){
      $data = array("success" => 0, "message" => "Invalid Company Code");
      generate_json($data);
      exit();
    }

    $company = $company->row_array();
    $this->session->unset_userdata('database_name');
    $this->db = switch_database($company['database_name']);
    $admin = $this->model->get_user_w_pos_id(2);
    $this->session->set_userdata('database_name', $company['database_name']);
    if($admin->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to login in as superuser. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $admin = $admin->row();
    // if($admin->position_id != 1){
    //   $data = array("success" => 0, "message" => "Your not authorized to login as superuser");
    //   generate_json($data);
    //   exit();
    // }
    $username = $admin->username;

    // $validate_username = $this->model->validate_username($username);
    // if($validate_username->num_rows() == 0){
    //   $data = array("success" => 0, "message" => 'The username you\'ve entered doesn\'t match any account. <a href="'.base_url('Main/register').'">Sign up for an account.</a>');
    //   $this->session->sess_destroy();
    //   generate_json($data);
    //   exit();
    // }
    // echo 1;
    // $user = $validate_username->row();
    // $pos_id = $user->pos_lvl;

    // $unverified_username = $user->enabled;
    // if($unverified_username == 0){
    //   $data = array("success" => 0, "message" => 'The account you\'ve entered is unverified account.');
    //   $this->session->sess_destroy();
    //   generate_json($data);
    //   exit();
    // }

    // echo 2;

    // if($validate_username->num_rows() > 0){
      // $userObj = $validate_username->row(); //get the data for fetch
      $this->session->unset_userdata('position_id');
      $this->session->unset_userdata('get_position_access');
      $userData = array( // store in array
          'user_id'	=> $admin->user_id,
          'emp_idno' => $admin->employee_idno,
          'username'    => $admin->username,
          'firstname'	  => $admin->user_fname,
          'middlename'  => $admin->user_mname,
          'lastname'	  => $admin->user_lname,
          'position_id' => $admin->position_id,
          'position_lvl' => 1,
          'deptId' => $admin->deptId,
          'get_position_access' => $this->model->get_position_details_access(1)->row(),
          'enabled'     => $admin->enabled,
          'isLoggedIn'  => true,
          'avatar_file' => $admin->avatar_file,
          'branch_name' => $company['branch_name']
      );

      $this->session->set_userdata($userData); // set session
      $this->session->set_userdata(array('superuser' => true));

      $data = array(
        'success' => 1,
        'message' => 'Login Successfully',
      );
      // echo 3;
    // }else{
    //   $data = array(
		// 		'success' => 0,
		// 		'message' => 'The username you\'ve entered doesn\'t match any account. <a href="'.base_url('Main/register').'">Sign up for an account.</a>'
		// 	);
    //   // echo 4;
    //   $this->session->sess_destroy();
    // }

    generate_json($data);

  }

  public function index($token = ""){
    if($this->session->userdata('isLoggedIn') == true) {
			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}

		$this->load->view('branch_login');
  }
}
