<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employment_history extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('employees/employment_history_model');
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

  public function get_employment_history_json(){
    $search = $this->input->post('searchValue');
    $data = $this->employment_history_model->get_employment_history_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('employees/employment_history',$data);
  }

  public function getprevcontract(){
    $previd = $this->input->post('previd');
    if(empty($previd)){
      $data = array("success" => 0, "message" => "Unable to get information about the previous contract");
      generate_json($data);
      exit();
    }

    $prevContract = $this->employment_history_model->getPrevContractFull($previd);
    if($prevContract->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get information about the previous contract");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "prevData" => $prevContract->row_array(), "pos_id" => $this->session->userdata('position_id'));
    generate_json($data);

  }
}
