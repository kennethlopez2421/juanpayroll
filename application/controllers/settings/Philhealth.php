<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Philhealth extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/philhealth_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'philhealth' => $this->philhealth_model->getPhil(null,null)->result()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/philhealth', $data);

	}

	//json result
	public function philhealthjson() {
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');


		$data = array(
		"draw" => $draw,
		"recordsTotal" => $this->philhealth_model->getPhil(null,null)->num_rows(),
		"recordsFiltered" => $this->philhealth_model->getPhil(null,null)->num_rows(),
		"data" => $this->philhealth_model->getPhil($start,$length)->result()
		);

		echo json_encode($data);

	}

	public function getByPhilID(){
		$id = $this->input->post('id');
		$data = $this->philhealth_model->getphID($id)->result();

		$res = array("IDresult" => $data);
		generate_json($res);
	}

	public function checkSalRange(){
		$basicSal1 = $this->input->post('basicSal1');
		$basicSal2 = $this->input->post('basicSal2');

		if($basicSal1 == "" || $basicSal2 == ""){
			$data = array("success" => 0, "message" => "System Error.");
		}else{
			$salRange_data = array($basicSal1,$basicSal2);
			// print_r($salRange_data);
			// die();
			if($this->philhealth_model->checkSalRange($salRange_data)->num_rows() > 0){
				$data = array("success" => 0, "salRangeExist" => 1, "message" => "Salary Range Already Exist");
			}else{
				$data = array("success" => 1, "salRangeExist" => 0, "message" => "");
			}
		}

		generate_json($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$basicSal1 = $this->input->post('basicSal1');
		$basicSal2 = $this->input->post('basicSal2');
		$monthlyCon1 = $this->input->post('monthlyCon1');
		$monthlyCon2 = $this->input->post('monthlyCon2');
		$employeeShare1 = $this->input->post('employeeShare1');
		$employeeShare2 = $this->input->post('employeeShare2');
		$employerShare1 = $this->input->post('employerShare1');
		$employerShare2 = $this->input->post('employerShare2');

		$enabled = 1;
		$userID	= $this->session->user_id;
		$date_created = todaytime();
		$date_updated = todaytime();


		$data = array(
			$basicSal1,
			$basicSal2,
			$monthlyCon1,
			$monthlyCon2,
			$employeeShare1,
			$employeeShare2,
			$employerShare1,
			$employerShare2,
			$enabled,
			$userID,
			$date_created,
			$date_updated
		);

		$salRange_data = array($basicSal1, $basicSal2);
		if($this->philhealth_model->checkSalRange($salRange_data)->num_rows() > 0){
			$data = array("success" => 0, "salRangeExist" => 1, "message" => "Salaray Range Already Exist");
			echo json_encode($data);
			exit();
		}

		if(($basicSal1 == "") || $basicSal2 == "" || ($monthlyCon1 == "") || $monthlyCon2 == "" || ($employeeShare1 == "") || $employerShare1 == "" ){
			$data = array('success' => 0, 'message' => 'Please fill out all fields');
		}else{
			$this->philhealth_model->create($data);
			$data = array('success' => 1, 'message' => 'Successfully Added');
		}
			echo json_encode($data);
	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {

		$id = $this->input->post('id');
		$basicSal1 = $this->input->post('editBasicSal1');
		$basicSal2 = $this->input->post('editBasicSal2');
		$monthlyCon1 = $this->input->post('editMonthlyCon1');
		$monthlyCon2 = $this->input->post('editMonthlyCon2');
		$employeeShare1 = $this->input->post('editEmployeeShare1');
		$employeeShare2 = $this->input->post('editEmployeeShare2');
		$employerShare1 = $this->input->post('editEmployerShare1');
		$employerShare2 = $this->input->post('editEmployerShare2');
		$enabled = 1;
		$userID	= $this->session->user_id;
		$date_created = todaytime();
		$date_updated = todaytime();

		$data = array(
			$basicSal1,
			$basicSal2,
			$monthlyCon1,
			$monthlyCon2,
			$employeeShare1,
			$employeeShare2,
			$employerShare1,
			$employerShare2,
			$enabled,
			$userID,
			$date_updated,
			$id
		);

		$this->philhealth_model->update($data);
		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);
	}

	//frontend
	public function delete() {

		}

	//backend
	public function destroy() {

		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->philhealth_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);

	}



}
