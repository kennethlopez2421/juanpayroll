<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Register_facial extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('registerid/register_facial_model');

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

  public function get_register_facial_json(){
    $search = $this->input->post('searchValue');
    $data = $this->register_facial_model->get_register_facial_json($search);
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
    $this->load->view('registerid/register_facial',$data);
  }

  public function add($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('registerid/add_facial_feature',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $employee_idno = $this->input->post('employee_idno');
    $faceLandMarks = $this->input->post('faceLandMarks');
    $accuracy = $this->input->post('accuracy');
    $descriptor = $this->input->post('descriptor');

    if(empty($employee_idno) || empty($faceLandMarks) || empty($accuracy) || empty($descriptor)){
      $data = array("success" => 0, "message" => "Oops! Something went wrong. Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $check_employee = $this->register_facial_model->get_employee($employee_idno);
    if($check_employee->num_rows() == 0){
      $data = array("success" => 0, "message" => "Invalid Employee Id Number. Please try again");
      generate_json($data);
      exit();
    }

    $check_employee_contract = $this->register_facial_model->get_employee($employee_idno,true);
    if($check_employee_contract->num_rows() == 0){
      $data = array("success" => 0, "message" => "Employee contract invalid. Please try again.");
      generate_json($data);
      exit();
    }

    ### picture file upload ###
    if(isset($_FILES['picture'])){
      // $config['file_name'] = trim($empId).'_'.$dateIn.'_'.preg_replace('/[:]+/', '-', trim($timeIn)).'.png';
      $config['upload_path']       = 'assets/facial_recog_img/';
      $config['allowed_types']     = '*';
      $config['max_size']          = 2048;
      $config['encrypt_name']      = true;

      $this->load->library('upload', $config);

      if(!$this->upload->do_upload('picture')){
         $error = array('error' => $this->upload->display_errors());
         $data = array("success" => 0, "message" => $error['error']);
         generate_json($data);
         exit();
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $picture = $config['upload_path'].$cdata['upload_data']['file_name'];
      }
    }else{
      $picture = "";
    }

    if($picture == ""){
      $data = array("success" => 0, "message" => "Oops! Unable to capture image. Please try again.");
      generate_json($data);
      exit();
    }

    $isExists = $this->register_facial_model->get_facial_recog($employee_idno)->num_rows();
    if($isExists >= 5){
      $data = array("success" => 0, "message" => "Employee already have 5 Facial Recognition Data.");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "employee_idno" => $employee_idno,
      "facial_landmarks" => $faceLandMarks,
      "accuracy" => $accuracy,
      "descriptor" => $descriptor,
      "img_src" => $picture
    );

    $inserted = $this->register_facial_model->set_facial_recog($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save facial recognition data. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Facial Recognition Data Successfully Saved.");
    generate_json($data);

  }

  public function delete(){
    $del_id = $this->input->post('del_id');
    if(empty($del_id)){
      $data = array("success" => 0, "message" => "Unable to delete this data. Please try again.");
      generate_json($data);
      exit();
    }

    $deleted = $this->register_facial_model->update_facial_recog_status($del_id);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete Facial Recognition Data. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Facial Recognition Data Successfully Deleted");
    generate_json($data);
  }
}
