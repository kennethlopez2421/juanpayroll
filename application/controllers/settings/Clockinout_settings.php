<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Clockinout_settings extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/clockinout_settings_model');
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

  public function get_clockinout_settings_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->clockinout_settings_model->get_clockinout_settings_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/clockinout_settings',$data);
  }

  public function update_status(){
    $this->isLoggedIn();

    $status = $this->input->post('status');
    $id = en_dec('dec',$this->input->post('id'));
    if(empty($status) || empty($id)){
      $data = array("success" => 0, "message" => "Unable to changed status. Please try again.");
      generate_json($data);
      exit();
    }

    $update_data = array("status" => $status);
    $updated = $this->clockinout_settings_model->update_status($update_data,$id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update rules status. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Status updated successfully");
    generate_json($data);
  }

  public function update_minutes(){
    $this->isLoggedIn();

    $minutes = $this->input->post('minutes');
    $data_min = $this->input->post('data_min');
    $uid = en_dec('dec',$this->input->post('uid'));

    if(empty($minutes) || empty($data_min) || empty($uid)){
      $data = array("success" => 0, "message" => "Unable to change minutes. Please try again.");
      generate_json($data);
      exit();
    }

    if($minutes == $data_min){
      $data = array("success" => 0, "message" => "Minutes was not changed. ");
      generate_json($data);
      exit();
    }

    $update_data = array("minutes" => $minutes);
    $updated = $this->clockinout_settings_model->update_minutes($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update minutes. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Minutes updated successfully");
    generate_json($data);
  }

  public function create(){
    $this->isLoggedIn();

    $add_rules = $this->input->post('add_rules');
    $add_minutes = $this->input->post('add_minutes');
    $add_type = en_dec('dec',$this->input->post('add_type'));
    $add_status = en_dec('dec',$this->input->post('add_status'));
    $add_desc = $this->input->post('add_desc');

    if(empty($add_rules) || empty($add_minutes) || empty($add_type) || empty($add_status) || empty($add_desc)){
      $data = array("success" => 0, "message" => "Please fill up all the required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->clockinout_settings_model->get_rules($add_rules);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Rule name already exists. Please try another.");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "rules" => $add_rules,
      "minutes" => $add_minutes,
      "status" => $add_status,
      "type" => $add_type,
      "description" => $add_desc
    );

    $inserted = $this->clockinout_settings_model->set_rules($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save new rules. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Rules save successfully");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $rules = $this->input->post('rules');
    $update_id = en_dec('dec',$this->input->post('update_id'));
    $description = $this->input->post('description');

    if(empty($rules) || empty($update_id) || empty($description)){
      $data = array("success" => 0, "message" => "Please fill up all required fields".$update_id);
      generate_json($data);
      exit();
    }

    $isExist = $this->clockinout_settings_model->get_rules($rules,$update_id);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Name rules already taken. Please try another");
      generate_json($data);
      exit();
    }

    $update_data = array(
      "rules" => $rules,
      "description" => $description
    );
    $updated = $this->clockinout_settings_model->update($update_data,$update_id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unble to update rules name and description. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Rules name and description successfully updated.");
    generate_json($data);


  }

  public function delete(){
    $this->isLoggedIn();
    
    $delid = en_dec('dec', $this->input->post('delid'));

    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to delete rules. Please try again.");
      generate_json($data);
      exit();
    }

    $deleted = $this->clockinout_settings_model->delete($delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete rules. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Rules deleted successfully");
    generate_json($data);
  }
}
