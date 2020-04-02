<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Assign_benefits extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('employees/assign_benefits_model');
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

  public function get_assign_benefits_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->assign_benefits_model->get_assign_benefits_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'department' => $this->model->getDepartment(),
      'benefits' => $this->assign_benefits_model->get_benefits()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('employees/assign_benefits',$data);
  }

  public function create(){
    $this->isLoggedIn();
    $employee = $this->input->post('employee');
    $benefits = $this->input->post('real_benefits');

    if(empty($employee) || empty($benefits)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->assign_benefits_model->get_assign_benefits($employee);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "This employee has already assign benefits.");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "employee_idno" => $employee,
      "benefits_id" => $benefits,
      "created_at" => todaytime(),
      "assign_by" => $this->session->emp_idno
    );
    $inserted = $this->assign_benefits_model->set_assign_benefits($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to assign benefits. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Assign benefits save successfully");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();
    $uid = en_dec('dec', $this->input->post('uid'));
    $benefits = $this->input->post('real_benefits');

    if(empty($benefits)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $update_data = array("benefits_id" => $benefits);
    $updated = $this->assign_benefits_model->update_assign_benefits($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update assign benefits. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Assign benefits updated successfully");
    generate_json($data);

  }

  public function delete(){
    $delid = en_dec('dec', $this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Something went wrong. Please try again");
      generate_json($data);
      exit();
    }

    $delete_data = array("enabled" => 0);
    $deleted = $this->assign_benefits_model->update_assign_benefits_status($delete_data,$delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete issued items. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Issued item deleted successfully");
    generate_json($data);
  }

  public function get_employee_by_dept(){
    $this->isLoggedIn();
    $dept_id = $this->input->post('dept_id');

    if(empty($dept_id)){
      $data = array("success" => 0, "message" => "Invalid department . Please try again.");
      generate_json($data);
      exit();
    }

    $emps = $this->assign_benefits_model->get_employee_by_dept($dept_id);
    if($emps->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to fetch any employee for this department.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "emps" => $emps->result());
    generate_json($data);
  }
}
