<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('admin/admin_model');
  }

  public function logout() {
        $this->session->sess_destroy();
        $this->load->view('admin/cp_login');
  }

  public function isLoggedIn() {
    //this will destroy the session if the user not logged in
    if($this->session->userdata('isLoggedIn') == false) {
      if(empty($this->session->userdata('position_id'))) { //kapag destroyed na ung session
        header("location:".base_url('Admin/logout'));
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Admin/logout'));
      }
    }
  }

  public function get_branch_json(){
    $search = $this->input->post('searchValue');
    $data = $this->admin_model->get_branch_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    if($this->session->userdata('isLoggedIn') == true) {

			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Admin/home/'.$token));
		}
    $this->session->sess_destroy();
		$this->load->view('admin/cp_login');
  }

  public function cp_login(){
    $username = $this->input->post('username');
    $password = $this->input->post('password');

    if(empty($username) || empty($password)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $validate_user = $this->admin_model->get_admin_user($username);
    if($validate_user->num_rows() == 0){
      $data = array(
				'success' => 0,
				'message' => 'The username you\'ve entered doesn\'t match any account. <a href="'.base_url('Main/register').'">Sign up for an account.</a>'
			);
    }

    $user = $validate_user->row_array();
    $hash_password = $user['password'];
    if(password_verify($password,$hash_password)){

      $userData = array( // store in array
        'admin_user_id' => en_dec('en',$user['id']),
        'admin_username' => en_dec('en',$user['username']),
        'get_position_access' => $this->model->get_position_details_access($user['position_id'])->row(),
        'position_id' => $user['position_id'],
        'firstname' => $user['fname'],
        'middlename' => $user['mname'],
        'lastname' => $user['lname'],
        'isLoggedIn' => true
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
        'message' => 'The password you\'ve entered is not correct. Please try again.'
      );
    }
    generate_json($data);
  }

  public function back_to_admin(){
    $this->isLoggedIn();

    $token = $this->input->post('token');
    // die(en_dec('en',$token));

    $this->db = switch_database('cloudpanda-hris_main');
    $this->session->unset_userdata('database_name');
    $this->session->unset_userdata('branch_name');
    // $this->session->unset_userdata('superuser');
    // $this->session->unset_userdata('get_position_access');
    // unset($_SESSION['get_position_access']);
    // unset($_SESSION['database_name']);
    // print_r($_SESSION['get_position_access']);
    $_SESSION['database_name'] = 'cloudpanda-hris_main';
    // $_SESSION['get_position_access'] = $this->model->get_position_details_access($this->session->position_id)->row();
    // print_r($_SESSION['get_position_access']);

    // $this->session->set_userdata(array('database_name' => 'cloudpanda-hris_main'));
    // $this->session->set_userdata('get_position_access', $this->model->get_position_details_access($this->session->position_id)->row());
    $data = array("success" => 1, "token" => en_dec('en',$token));
    generate_json($data);
  }

  public function home($token = ''){
    $this->isLoggedIn();
    if(isset($this->session->superuser) && $this->session->superuser == true){
      $_SESSION['get_position_access'] = $this->model->get_position_details_access($this->session->position_id)->row();
    }

    $data_user = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

		$this->load->view('includes/header2', $data_user);
		$this->load->view('main_navigation/home', $data_user);
  }
}
