<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Timerecord_logs extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('time_record/timerecord_logs_model');
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

  public function get_timerecord_logs_json(){
    $search = $this->input->post('searchValue');
    $data = $this->timerecord_logs_model->get_timerecord_logs_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'admins' => $this->model->get_position_access_lvl(2)
    );

    $this->load->view('includes/header',$data);
    $this->load->view('time_record/timerecord_logs',$data);
  }
}
