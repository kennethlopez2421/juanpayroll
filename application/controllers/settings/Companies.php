<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Companies extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/companies_model');

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

  public function get_companies_json(){
    $search = $this->input->post('searchValue');
    $data = $this->companies_model->get_companies_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/companies',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $new_company_name = $this->input->post('new_company_name');
    if($new_company_name == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->companies_model->get_companies_by_name($new_company_name);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "<u>".$new_company_name."</u> already exist. Please try another.");
      generate_json($data);
      exit();
    }

    $insert_data = array("company" => $new_company_name);
    $inserted = $this->companies_model->set_company($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "messag" => "Unable to save new company. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "New Company Save Successfully");
    generate_json($data);

  }

  public function update(){
    $edit_company_name = $this->input->post('edit_company_name');
    $uid = en_dec('dec',$this->input->post('uid'));

    if($edit_company_name == "" || $uid == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->companies_model->get_companies_by_name($edit_company_name,$uid);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "<u>".$edit_company_name."<u> already exist. Please try another.");
      generate_json($data);
      exit();
    }

    $update_data = array($edit_company_name,$uid);
    $updated = $this->companies_model->update_company($update_data);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update company. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Successfully Updated Company");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec',$this->input->post('delid'));
    if($delid == ""){
      $data = array("success" => 0, "message" => "Oops! Something went wrong. Please reload and try again.");
      generate_json($data);
      exit();
    }

    $delete_data = array(0,$delid);
    $deleted = $this->companies_model->update_company_status($delete_data);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to Delete Company. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Company Deleted Successfully");
    generate_json($data);

  }
}
