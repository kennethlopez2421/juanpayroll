<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Register_bio extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('registerid/register_bio_model');
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

  public function get_biometrics_json(){
    $search = $this->input->post('searchValue');
    $data = $this->register_bio_model->get_biometrics_json($search);
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
    $this->load->view('registerid/register_bio',$data);
  }

  public function create(){
    $add_empid = $this->input->post('add_empid');
    $add_bio_id = $this->input->post('add_bio_id');

    if(empty($add_empid) || empty($add_bio_id)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $emp_exist = $this->register_bio_model->get_emp_id($add_empid);
    if($emp_exist->num_rows() == 0){
      $data = array("success" => 0, "message" => "Invalid Employee Id. Please try again.");
      generate_json($data);
      exit();
    }

    $bio_exist = $this->register_bio_model->get_bio_id($add_bio_id);
    if($bio_exist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Biometrics Id already exist. Please try again");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "employee_idno" => $add_empid,
      "bio_id" => $add_bio_id
    );
    $inserted = $this->register_bio_model->set_biometrics_id($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save biometrics id. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Biometrics Id Successfully Saved. ");
    generate_json($data);
  }

  public function update(){
    $edit_bio_id = $this->input->post('edit_bio_id');
    $prev_bio_id = $this->input->post('prev_bio_id');
    $uid = $this->input->post('uid');

    if(empty($edit_bio_id) || empty($prev_bio_id) || empty($uid)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    if($edit_bio_id == $prev_bio_id){
      $data = array("success" => 0, "message" => "Nothing change. Please try again.");
      generate_json($data);
      exit();
    }

    $bio_exist = $this->register_bio_model->get_bio_id($edit_bio_id,$uid);
    if($bio_exist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Biometrics Id already exist. Please try again.");
      generate_json($data);
      exit();
    }

    $update_data = array($edit_bio_id,$uid);
    $updated = $this->register_bio_model->update_biometrics_id($update_data);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update biometrics id. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Biometrics Id updated Successfully");
    generate_json($data);
  }

  public function delete(){
    $del_id = $this->input->post('del_id');
    if(empty($del_id)){
      $data = array("success" => 0, "message" => "Unable to delete Biometrics Id. Please try again.");
      generate_json($data);
      exit();
    }

    $delete_data = array(0,$del_id);
    $deleted = $this->register_bio_model->update_biometrics_enabled($delete_data);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete Biometrics Id. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Biometrics Id Successfully Deleted. ");
    generate_json($data);
  }
}
