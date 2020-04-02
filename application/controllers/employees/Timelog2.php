<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timelog extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('employees/timelog_model');
		$this->load->model('employees/employee_model');
	}
 
	//views
	public function index() {
		$this->load->view('employees/timelog2');		
	}

	//json result	
	public function cityJSON() {

	}
	
	//frontend
	public function add() {

	}

	//backend
	public function create() {
		
		$empId = $this->input->post('empId');
		$timeIn = $this->input->post('timeIn');
		$dateIn = date('Y-m-d');
		$workSite = 1;
		$type = "";
		$mode = "auto";		

		$typeWord = "";

		$timeInArr = array(
			$empId,
			date('Y-m-d')
		);


		$isEmployeeExist = $this->employee_model->getEmployeeByIdNo($empId)->num_rows();

		$lastLog = $this->timelog_model->getLastLog($timeInArr)->row();

 		if($isEmployeeExist > 0) {
			if($lastLog->type == "out") {
				$type = "in";
				$typeWord = "Time In";
			}else {
				$type = "out";
				$typeWord = "Time Out";
			}
		}
		 
		$data = array(
			$empId,
			$workSite,
			$timeIn,
			$dateIn,
			$type,
			$mode,
			1
		);
	
		$this->timelog_model->setTime($data);

		if($empId == "" || $empId == null) {
			$data = array('success' => 0, 'message' => 'Please Enter Employee ID#');
		}else {
			if($timeIn == "" || $timeIn == null) {
				$data = array('success' => 0, 'message' => 'Time is null. Server Error Maybe');
			}else {
				if($isEmployeeExist == 1) {
					// $data = array('success' => 0, 'message' => 'Employee ID# does not exist');
					$fname = $this->employee_model->getEmployeeByIdNo($empId)->row()->first_name;
					$lname = $this->employee_model->getEmployeeByIdNo($empId)->row()->last_name;					
					$data = array('success' => 1, 'mode' => $typeWord.": ".$timeIn,'message' => "Good Day!<br>Employee: ".$lname.", ".$fname."<br />".$typeWord.": ".$timeIn."<br />Date: ".formatDate($dateIn));
				} else {
					$data = array('success' => 0, 'message' => 'Employee ID# does not exist');
				}
			}
		}

		echo json_encode($data);

	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {
	
	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {
		
	}



}