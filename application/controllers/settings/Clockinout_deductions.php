<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Clockinout_deductions extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/clockinout_deductions_model');

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

  public function get_clockinout_deductions_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->clockinout_deductions_model->get_clockinout_deductions_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/clockinout_deductions',$data);
  }

  public function update_status(){
    $this->isLoggedIn();

    $status = en_dec('dec',$this->input->post('status'));
    $id = en_dec('dec',$this->input->post('id'));
    if(empty($status) || empty($id)){
      $data = array("success" => 0, "message" => "Unable to changed status. Please try again.");
      generate_json($data);
      exit();
    }

    $update_data = array("status" => $status);
    $updated = $this->clockinout_deductions_model->update_status($update_data,$id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update rules status. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Status updated successfully");
    generate_json($data);
  }

  public function create(){
    $this->isLoggedIn();

    $type = en_dec('dec',$this->input->post('type'));
    $min_from = $this->input->post('min_from');
    $min_to = $this->input->post('min_to');
    $min_deduct = $this->input->post('min_deduct');
    $whours = $this->input->post('whours');

    if(empty($type) || empty($min_from) || empty($min_to) || empty($min_deduct) || empty($whours)){
      $data = array("success" => 0, "messag" => "Please fill up all required fields. ");
      generate_json($data);
      exit();
    }

    // $isExist = $this->clockinout_deductions_model->check_type_and_hours($type,$whours);
    // if($isExist->num_rows() > 0){
    //   $data = array("success" => 0, "message" => "Deduction type already exist. Please try another.");
    //   generate_json($data);
    //   exit();
    // }

    $insert_data = array(
      "type" => $type,
      "min_from" => $min_from,
      "min_to" => $min_to,
      "min_deduct" => $min_deduct,
      "whours" => $whours
    );

    $inserted = $this->clockinout_deductions_model->set_deduct($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save deduction type. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deduction type saved successfully");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec',$this->input->post('uid'));
    $edit_type = en_dec('dec', $this->input->post('edit_type'));
    $edit_min_from = $this->input->post('edit_min_from');
    $edit_min_to = $this->input->post('edit_min_to');
    $edit_min_deduct = $this->input->post('edit_min_deduct');
    $edit_whours = $this->input->post('edit_whours');

    if(empty($uid) || empty($edit_type) || empty($edit_min_from) || empty($edit_min_to) || empty($edit_min_deduct) || empty($edit_whours)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    // $isExist = $this->clockinout_deductions_model->check_type_and_hours($edit_type,$edit_whours,$uid);
    // if($isExist->num_rows() > 0){
    //   $data = array("success" => 0, "message" => "Deduction type already exist. Please try another.");
    //   generate_json($data);
    //   exit();
    // }

    $update_data = array(
      "type" => $edit_type,
      "min_from" => $edit_min_from,
      "min_to" => $edit_min_to,
      "min_deduct" => $edit_min_deduct,
      "whours" => $edit_whours
    );
    $updated = $this->clockinout_deductions_model->update($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update deduction type. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deduction type successfully updated.");
    generate_json($data);

  }

  public function delete(){
    $delid = en_dec('dec', $this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to delete this data. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $deleted = $this->clockinout_deductions_model->update_enabled($delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete this data. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deduction type deleted successfully");
    generate_json($data);
  }
}
