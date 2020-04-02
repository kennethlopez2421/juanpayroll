<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Register_rf extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('registerid/register_rf_model');
    $this->isLoggedIn();
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
        exit();
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
        exit();
      }
    }
  }

  public function get_rf_json(){
    $search = $this->input->post('searchValue');
    $data = $this->register_rf_model->get_rf_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
      'positions' => $this->model->get_user_position()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('registerid/register_rf',$data);
  }
}
