<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transaction_email_settings extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/transaction_email_settings_model');
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
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
      }
    }
  }

  public function get_email_settings_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->transaction_email_settings_model->get_email_settings_json($search);
    echo json_encode($data);
  }

  public function get_employees(){
    $this->isLoggedIn();

    $dept = $this->input->post('dept');
    if(empty($dept)){
      $data = array("success" => 0, "message" => "Please select a department first. ");
      generate_json($data);
      exit();
    }

    $employees = $this->transaction_email_settings_model->get_employee_from_dept($dept);
    if($employees->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to find andy employees under this department.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "employees" => $employees->result_array());
    generate_json($data);

  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'content_navs' => $this->transaction_email_settings_model->get_content_nav(),
      'departments' => $this->model->getDepartment()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/transaction_email_settings',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $cont_nav = $this->input->post('cont_nav');
    $dept = $this->input->post('dept');
    $approver = $this->input->post('approver2');
    $certifier = $this->input->post('certifier2');

    if(empty($cont_nav) || empty($dept) || empty($approver) || empty($certifier)){
      $data = array("success" => 0, "message" => "Please fill up all required fieldss.");
      generate_json($data);
      exit();
    }

    $isExist = $this->transaction_email_settings_model->get_email_settings($cont_nav,$dept);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "This transaction email settings already exist.");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "content_nav_id" => $cont_nav,
      "department_id" => $dept,
      "approver" => $approver,
      "certifier" => $certifier
    );

    $inserted = $this->transaction_email_settings_model->set_email_settings($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save transaction email settings. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Transaction email settings save successfully");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec', $this->input->post('uid'));
    $edit_cont_nav = $this->input->post('edit_cont_nav');
    $edit_dept = $this->input->post('edit_dept');
    $edit_approver = $this->input->post('approver');
    $edit_certifier = $this->input->post('certifier');

    if(empty($uid) || empty($edit_cont_nav) || empty($edit_dept) || empty($edit_approver) || empty($edit_certifier)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->transaction_email_settings_model->get_email_settings($edit_cont_nav,$edit_dept,$uid);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "This transaction email settings already exist.");
      generate_json($data);
      exit();
    }

    $update_data = array(
      "content_nav_id" => $edit_cont_nav,
      "department_id" => $edit_dept,
      "approver" => $edit_approver,
      "certifier" => $edit_certifier
    );

    $updated = $this->transaction_email_settings_model->update_email_settings($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update transaction email settings.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Transaction email settings updated successfully");
    generate_json($data);
  }

  public function delete(){
    $this->isLoggedIn();

    $delid = en_dec('dec',$this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Something went wrong. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $delete_data = array("enabled" => 0);
    $deleted = $this->transaction_email_settings_model->update_email_settings_status($delete_data,$delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete transaction email settings");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Transaction email settings deleted successfully");
    generate_json($data);
  }
}
