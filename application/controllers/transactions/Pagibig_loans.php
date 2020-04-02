<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pagibig_loans extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('transactions/pagibig_loans_model');
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

  public function get_pagibig_loans_json(){
    $search = $this->input->post('searchValue');
    $data = $this->pagibig_loans_model->get_pagibig_loans_json($search);
    echo json_encode($data);
  }

  public function search_user_w_sss(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    // echo $employee;
    if($employee == ""){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item disabled">No Result Found</a>');
      generate_json($data);
      exit();
    }

    $search = $this->model->search_user($employee,'pagibig_no');
    // die($search);
    if($search->num_rows() == 0){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item disabled" >No Result Found</a>');
      generate_json($data);
      exit();
    }

    if($search->num_rows() > 0){
      $html = "";
      foreach($search->result_array() as $row){
        $html .= '<a href="#" class="dropdown-item" data-emp_idno = "'.en_dec('en',$row['employee_idno']).'">'.$row['fullname'].'</a>';
      }

      $data = array("success" => 1, "message" => $html);
      generate_json($data);
    }
  }

  public function get_pagibig_loan(){
    // $sss_loan_id = $this->input->post('sss_loan_id');
    $search = $this->input->post('searchValue');
    $data = $this->pagibig_loans_model->get_pagibig_loan($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/pagibig_loans',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $employee_idno = en_dec('dec', $this->input->post('employee_idno'));
    $pagibig_voucher = $this->input->post('pagibig_voucher');
    $period_from = $this->input->post('period_from');
    $period_to = $this->input->post('period_to');
    $deduction_start = $this->input->post('deduction_start');
    $total_loan = $this->input->post('total_loan_raw');
    $monthly_amortization = $this->input->post('monthly_amortization_raw');
    $sss_voucher = "";

    $insert_data = array(
      "employee_idno" => $employee_idno,
      "pagibig_loan_start" => $period_from,
      "pagibig_loan_end" => $period_to,
      "pagibig_deduction_start" => $deduction_start,
      "pagibig_total_loan" => $total_loan,
      "monthly_amortization" => $monthly_amortization,
      "status" => "active"
    );



    ### picture file upload ###
    if(isset($_FILES['pagibig_voucher']) && !empty($_FILES['pagibig_voucher'])){
      $config['upload_path']       = 'assets/pagibig_loan_voucher/';
      $config['allowed_types']     = 'jpg|jpeg|png';
      $config['max_size']          = 2048;
      $config['encrypt_name']      = true;

      $this->load->library('upload', $config);

      if(!$this->upload->do_upload('pagibig_voucher')){
         $error = array('error' => $this->upload->display_errors());
         $data = array("success" => 0, "message" => $error['error']);
         generate_json($data);
         exit();
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $sss_voucher = $config['upload_path'].$cdata['upload_data']['file_name'];
      }
    }else{
      $sss_voucher = "";
    }

    $insert_data['pagibig_loan_voucher'] = $sss_voucher;

    foreach($insert_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }
    }

    $isExist = $this->pagibig_loans_model->get_employee_w_pagibig_no($employee_idno);
    if($isExist->num_rows() == 0){
      $data = array("success" => 0, "message" => "Oops! This employee does not exist. Please try again.");
      generate_json($data);
      exit();
    }

    $inserted = $this->pagibig_loans_model->set_pagibig_loan($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save Pagibig Loan. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Pagibig Loan Save Successfully. ");
    generate_json($data);

  }
}
