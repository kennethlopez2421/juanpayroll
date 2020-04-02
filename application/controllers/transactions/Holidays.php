<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Holidays extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('transactions/holidays_model');
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

  public function get_holiday_json(){
    $search = $this->input->post('searchValue');
    $data = $this->holidays_model->get_holidays_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'holiday_type' => $this->holidays_model->get_holiday_type()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/holidays',$data);
  }

  public function add(){
    $h_desc = $this->input->post('h_desc');
    $h_type = $this->input->post('h_type');
    $h_date = $this->input->post('h_date');

    if($h_desc == "" || $h_type == "" || $h_date == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $hdata = array($h_desc,$h_date);
    $rows = $this->holidays_model->get_desc_w_date($hdata)->num_rows();
    if($rows > 0){
      $data = array("success" => 0, "message" => "This holiday already exist");
      generate_json($data);
      exit();
    }

    $h_data = array(
      "description" => $h_desc,
      "holiday_type" => $h_type,
      "date" => $h_date
    );

    $inserted = $this->holidays_model->create($h_data);
    if($inserted == false){
      $data = array("success" => 0, "message" => "Unable to save holiday. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Holiday save successfully");
    generate_json($data);
  }

  public function update(){
    $update_id = $this->input->post('update_id');
    $h_desc = $this->input->post('h_desc');
    $h_type = $this->input->post('h_type');
    $h_date = $this->input->post('h_date');
    $c_hdesc = $this->input->post('c_hdesc');
    $c_hdate = $this->input->post('c_hdate');


    if($h_desc == "" || $h_type == "" || $h_date == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    if($c_hdesc != $h_desc && $c_hdate != $h_date){
      $rows = $this->holidays_model->get_desc_w_date(array($h_desc,$h_date))->num_rows();
      if($rows > 0){
        $data = array("success" => 0, "message" => "This holiday already exist");
        generate_json($data);
        exit();
      }
    }

    $update_data = array($h_desc, (int)$h_type, $h_date, (int)$update_id);
    $updated = $this->holidays_model->update($update_data);
    // echo $updated;
    // die();
    if($updated == false){
      $data = array("success" => 0, "message" => "Holiday update failed. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Holiday successfully updated.");
    generate_json($data);
  }

  public function delete(){
    $del_id = $this->input->post('del_id');
    $deleted = $this->holidays_model->delete($del_id);
    if($deleted == false){
      $data = array("success" => 0, "message" => "Unable to delete holiday. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Holiday deleted successfully");
    generate_json($data);
  }
}
