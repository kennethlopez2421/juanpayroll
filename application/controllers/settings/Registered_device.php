<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Registered_device extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/registered_device_model');
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

  public function get_registered_device_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->registered_device_model->get_registered_device_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/registered_device',$data);
  }

  public function gen_activation_code(){
    $activation_code = generate_player_no();
    $isExist = $this->registered_device_model->get_activation_code($activation_code);
    while($isExist->num_rows() > 0){
      $account_code = generate_player_no();
    }

    $data = array("success" => 1, "code" => $activation_code);
    generate_json($data);
  }

  public function create(){
    $activation_code = $this->input->post('activation_code');
    if(empty($activation_code)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $isExist = $this->registered_device_model->get_activation_code($activation_code);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Activation Code not available");
      generate_json($data);
      exit();
    }

    $insert_data = array("activation_code" => $activation_code);
    $inserted = $this->registered_device_model->set_activation_code($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save activation code. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Activation Code Save Successfully");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec',$this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $updated = $this->registered_device_model->update_status($delid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to delete activation code. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deleted Successfully");
    generate_json($data);
  }
}
