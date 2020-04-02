<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contract_template extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/contract_template_model');
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

  public function get_contract_template_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->contract_template_model->get_contract_template_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'fields' => $this->contract_template_model->get_template_settings()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/contract_template',$data);
  }

  public function create(){
    $this->isLoggedIn();
    
    $summernote = $this->input->post('summernote');
    $template_name = $this->input->post('template_name');
    $template_type = en_dec('dec',$this->input->post('template_type'));
    if(empty($summernote) || empty($template_name) || empty($template_type)){
      $data = array("success" => 0, "message" => "Please fill up all required fields. Please try again.");
      generate_json($data);
      exit();
    }

    $isExists = $this->contract_template_model->get_contract_template($template_name);
    if($isExists->num_rows() > 0){
      $data = array("success" => 0, "message" => "Template name already exists. Please try again.");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "template_name" => $template_name,
      "template_format" => $summernote,
      "template_type" => $template_type
    );

    $inserted = $this->contract_template_model->set_template($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save template format. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Contract Template save successfully.");
    generate_json($data);
  }

  public function edit(){
    $this->isLoggedIn();

    $id = en_dec('dec',$this->input->post('id'));
    if(empty($id)){
      $data = array("success" => 0, "message" => "Invalid template id");
      generate_json($data);
      exit();
    }

    $template = $this->contract_template_model->get_contract_template(false,$id);
    if($template->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get any template. Please try again.");
      generate_json($data);
      exit();
    }

    $template = $template->row();
    $data = array("success" => 1, "template_name" => $template->template_name, "template_format" => $template->template_format);
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec',$this->input->post('uid'));
    $edit_template_name = $this->input->post('edit_template_name');
    $edit_summernote = $this->input->post('edit_summernote');
    $edit_template_type = $this->input->post('edit_template_type');

    if(empty($edit_template_name) || empty($edit_summernote) || empty($edit_template_type)){
      $data = array("success" => 0, "message" => "Please fill up all required fields. Please try again.");
      generate_json($data);
      exit();
    }

    $isExists = $this->contract_template_model->get_contract_template($edit_template_name,false,$uid);
    if($isExists->num_rows() > 0){
      $data = array("success" => 0, "message" => "Template name already exists. Please try again.");
      generate_json($data);
      exit();
    }

    $update_data = array(
      "template_name" => $edit_template_name,
      "template_format" => $edit_summernote,
      "template_type" => $edit_template_type
    );

    $updated = $this->contract_template_model->update_template($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update contract template. Please check if you change anything before updating .");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Contract Template Updated Succesfully.");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec',$this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to find any ids");
      generate_json($data);
      exit();
    }

    $deleted = $this->contract_template_model->update_template_status($delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete contract template. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Contract template deleted successfully");
    generate_json($data);

  }
}
