<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Bank extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/bank_model');

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

  public function get_bank_json(){
    $search = $this->input->post('searchValue');
    $data = $this->bank_model->get_bank_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/bank',$data);
  }

  public function create(){
    $bank_name = $this->input->post('bank_name');
    if(empty($bank_name)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $row = $this->bank_model->get_bank_by_name($bank_name)->num_rows();
    if($row > 0){
      $data = array("success" => 0, "message" => "Bank name already exist. Please try again");
      generate_json($data);
      exit();
    }

    $add_data = array(
      "bank_name" => $bank_name
    );

    $inserted = $this->bank_model->set_bank($add_data);
    if($inserted == false){
      $data = array("success" => 0, "message" => "Unabled to save. Please try again.");
    }else{
      $data = array("success" => 1, "message" => "Save Successfully");
    }

    generate_json($data);


  }

  public function update(){
    $updated_name = $this->input->post('updated_name');
    $uid = $this->input->post('uid');

    if(empty($updated_name) || empty($uid)){
      $data = array("success" => 0, "message" => "Unable to update. Please try again.");
      generate_json($data);
      exit();
    }

    $row = $this->bank_model->get_bank_by_name($updated_name)->num_rows();
    if($row > 0){
      $data = array("success" => 0, "message" => "Bank name already exist. Please try again");
      generate_json($data);
      exit();
    }

    $bank_data = array($updated_name,$uid);
    $updated = $this->bank_model->update_bank($bank_data);
    if($updated == false){
      $data = array("success" => 0, "message" => "Unable to update. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Updated successfully.");
    generate_json($data);
  }

  public function delete(){
    $del_id = $this->input->post('del_id');

    if(empty($del_id)){
      $data = array("success" => 0, "message" => "Unable to delete. Please try again.");
      generate_json($data);
      exit();
    }

    $delete_data = array(0,$del_id);
    $deleted = $this->bank_model->update_bank_status($delete_data);
    if($deleted == false){
      $data = array("success" => 0, "message" => "Unable to delete. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deleted Successfully");
    generate_json($data);
  }
}
