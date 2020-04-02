<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Benefits_settings extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/benefits_settings_model');
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

  public function get_benefits_settings_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->benefits_settings_model->get_benefits_settings_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/benefits_settings',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $benefits_name = $this->input->post('benefits_name');
    if(empty($benefits_name)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->benefits_settings_model->get_benefits($benefits_name);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Benefits already exists. Please try another name");
      generate_json($data);
      exit();
    }

    $insert_data = array("benefits_name" => $benefits_name, "created_at" => todaytime());
    $inserted = $this->benefits_settings_model->set_benefits($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save benefits. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Benefits save successfully.");
    generate_json($data);
  }

  public function update(){
    $edit_benefits_name = $this->input->post('edit_benefits_name');
    $uid = en_dec('dec',$this->input->post('uid'));

    if(empty($uid) || empty($edit_benefits_name)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->benefits_settings_model->get_benefits($edit_benefits_name,$uid);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Benefits already exists. Please try another name");
      generate_json($data);
      exit();
    }

    $update_data = array("benefits_name" => $edit_benefits_name);
    $updated = $this->benefits_settings_model->update_benefits($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update benefits. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Benefits updated successfully.");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec', $this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Something went wrong . Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $delete_data = array("enabled" => 0);
    $deleted = $this->benefits_settings_model->update_benefits_status($delete_data,$delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete benefits. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Benefits deleted successfully");
    generate_json($data);
  }
}
