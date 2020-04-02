<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contract_history extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('contracts/contract_history_model');
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

  public function contract_histroy_json(){
    $search = $this->input->post('searchValue');
    $data = $this->contract_history_model->get_contract_history_json($search);
    // die($data);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      "dept" => $this->contract_history_model->getDept(),
      "position" => $this->contract_history_model->getPos(),
      "companies" => $this->model->get_hris_companies()
		);

    $this->load->view('includes/header',$data);
    $this->load->view('contracts/contract_history',$data);
  }

  public function getSubDept(){
    $this->isLoggedIn();

    $dept_id = $this->input->post('dept_id');
    if(empty($dept_id)){
      $data = array("success" => 0, "message" => "Filter error. Please try again");
    }else{
      $subDept = $this->contract_history_model->getSubDept($dept_id);
      if($subDept->num_rows() > 0){
        $data = array("success" => 1, "subDept" => $subDept->result_array());
      }
    }
    generate_json($data);
  }

  public function getPrevContract(){
    $previd = $this->input->post('previd');
    if(empty($previd)){
      $data = array("success" => 0, "message" => "Unable to get information about the previous contract");
      generate_json($data);
      exit();
    }

    $prevContract = $this->contract_history_model->getPrevContractFull($previd);
    if($prevContract->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get information about the previous contract");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "prevData" => $prevContract->row_array(), "pos_id" => $this->session->userdata('position_id'));
    generate_json($data);

  }
}
