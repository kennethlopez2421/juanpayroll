<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pagibig extends CI_Controller{

  public function __construct() {
		parent::__construct();
		$this->load->model('settings/pagibig_model');
	}

  public function pagibig_json(){
    $search = $this->input->post('searchValue');
    $data = $this->pagibig_model->getPagIbig_data($search);
		echo json_encode($data);
  }

  public function index($token = ""){

    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'pagIbigTable' => $this->pagibig_model->getPagIbig()->result_array()
		);

    $this->load->view('includes/header', $data);
    $this->load->view('settings/pagibig', $data);

  }

  public function create(){
    if(!$this->session->userdata('isLoggedIn')){
      $this->load->view('login');
    }

    $monthly_compensation = $this->input->post('monthly_compensation');
    $employee_share = $this->input->post('employee_share');
    $employer_share = $this->input->post('employer_share');

    if($monthly_compensation == "" || $employee_share == "" || $employer_share == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "monthly_compensation" => $monthly_compensation,
      "employee_share" => $employee_share,
      "employer_share" => $employer_share
    );

    $this->pagibig_model->set_pagibig($insert_data);
    $data = array("success" => 1, "message" => "Successfully added");
    generate_json($data);
  }

  public function edit(){

    if(!$this->session->userdata('isLoggedIn')){
      $this->load->view('login');
    }

    $updateId = $this->input->post('updateId');

    if($updateId == ""){
      $data = array("success" => 0, "message" => "Error. No id thrown.");
      generate_json($data);
      exit();
    }

    $row = $this->pagibig_model->get_pagIbigById($updateId);

    if($row->num_rows() == 0){
      $data = array("success" => 0, "message" => "Did not match any data in the database");
      generate_json($data);
      exit();
    }

    $pData = $row->row();
    $data = array("success" => 1, "pData" => $pData);
    generate_json($data);

  }

  public function update(){
    if(!$this->session->userdata('isLoggedIn')){
      $this->load->view('login');
    }

    $updateId = $this->input->post('updateId');
    $monthly_compensation = $this->input->post('monthly_compensation');
    $employee_share = $this->input->post('employee_share');
    $employer_share = $this->input->post('employer_share');

    if($updateId == ""){
      $data = array("success" => 0, "message" => "Update failed. Please try again");
      generate_json($data);
      exit();
    }

    if($monthly_compensation == "" || $employee_share == "" || $employer_share == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $updateData = array(
      $monthly_compensation,
      $employee_share,
      $employee_share,
      $updateId
    );

    $this->pagibig_model->update_pagIbig($updateData);
    $data = array("success" => 1, "message" => "Updated Successfully");
    generate_json($data);

  }

  public function destroy(){

    $deleteId = $this->input->post('deleteId');

    if($deleteId == ""){
      $data = array("success" => 0, "message" => "Unable to delete this record. Please try again");
      generate_json($data);
      exit();
    }

    $deleteData = array(0, $deleteId);
    $this->pagibig_model->en_dis_pagibig($deleteData);
    $data = array("success" => 1, "message" => "Record Deleted");
    generate_json($data);

  }
}
