<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Systemusers extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/systemusers_model');
		$this->load->model('employees/employee_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'system_user_positions' => $this->model->get_system_user_pos()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/systemusers', $data);

	}

	//json result
	public function get_systemuser_json(){
		$search = $this->input->post('searchValue');
		$data = $this->systemusers_model->get_systemuser_json($search);
		echo json_encode($data);
	}

	// public function systemusersjson() {
	//
	// 	$draw = $this->input->get('draw');
	// 	$start = $this->input->get('start');
	// 	$length = $this->input->get('length');
	// 	$search = $this->input->get('searchValue');
	//
	// 	$data = array(
	// 		"draw" => $draw,
	// 		"recordsTotal" => $this->systemusers_model->getSystemUsers(null,null,null)->num_rows(),
	// 		"recordsFiltered" => $this->systemusers_model->getSystemUsers(null,null,null)->num_rows(),
	// 		"data" => $this->systemusers_model->getSystemUsers($start,$length,$search)->result()
	// 	);
	//
	// 	echo json_encode($data);
	// }

	public function add() {
		$sys_fname = $this->input->post('sys_fname');
		$sys_mname = $this->input->post('sys_mname');
		$sys_lname = $this->input->post('sys_lname');
		$sys_username = $this->input->post('sys_username');
		$sys_password = $this->input->post('sys_password');
		$sys_password_cf = $this->input->post('sys_password_cf');
		$sys_positon = $this->input->post('sys_positon');

		if(empty($sys_fname) || empty($sys_lname) || empty($sys_lname) || empty($sys_username) || empty($sys_password) || empty($sys_positon)){
			$data = array("success" => 0, "message" => "Please fill up all required fields.");
			generate_json($data);
			exit();
		}

		if($sys_password != $sys_password_cf){
			$data = array("success" => 0, "message" => "Password do not match. Please try again.");
			generate_json($data);
			exit();
		}

		$isExist = $this->systemusers_model->get_systemuser_username($sys_username);
		if($isExist->num_rows() > 0){
			$data = array("success" => 0, "message" => "Username not available. Please try again.");
			generate_json($data);
			exit();
		}

		$employeeIdNo = generate_player_no();
		while($this->employee_model->getEmployeeByIdNo($employeeIdNo)->num_rows() > 0){
			$employeeIdNo = generate_player_no();
		}
		$option = ['cost' => 12];
		$hash_password = password_hash($sys_password, PASSWORD_BCRYPT, $option);
		$insert_data = array(
			"username" => $sys_username,
			"password" => $hash_password,
			"user_fname" => $sys_fname,
			"user_mname" => $sys_mname,
			"user_lname" => $sys_lname,
			"position_id" => $sys_positon,
			"employee_idno" => $employeeIdNo,
			"deptId" => 0,
			"subDeptId" => 0,
			"date_activated" => todaytime(),
			"date_created" => todaytime()
		);

		$inserted = $this->systemusers_model->set_system_user($insert_data);
		if($inserted === false){
			$data = array("success" => 0, "message" => "Unable to save System User. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "System User Save Successfully.");
		generate_json($data);
	}

	public function update_system_user(){
		$edit_uid = $this->input->post('edit_uid');
		$edit_sys_username = $this->input->post('edit_sys_username');
		$edit_sys_fname = $this->input->post('edit_sys_fname');
		$edit_sys_mname = $this->input->post('edit_sys_mname');
		$edit_sys_lname = $this->input->post('edit_sys_lname');
		$employee_idno = $this->input->post('employee_idno');

		if(empty($edit_uid) || empty($edit_sys_username) || empty($edit_sys_fname) || empty($edit_sys_lname) || empty($employee_idno)){
			$data = array("success" => 0, "message" => "Please fill up all required fields. ");
			generate_json($data);
			exit();
		}

		$check_username = $this->systemusers_model->get_systemuser_username($edit_sys_username,$edit_uid);
		if($check_username->num_rows() > 0){
			$data = array("success" => 0, "message" => "Username already exist. Please try again.");
			generate_json($data);
			exit();
		}

		$update_data = array(
			"username" => $edit_sys_username,
			"user_fname" => $edit_sys_fname,
			"user_lname" => $edit_sys_lname,
			"user_mname" => $edit_sys_mname,
			"employee_idno" => $employee_idno
		);

		$this->systemusers_model->update_system_user($update_data,$edit_uid);
		$data = array("success" => 1, "message" => "System User Updated Successfully");
		generate_json($data);
	}

	public function disable_system_user(){
		$del_id = $this->input->post('del_id');
		$del_id = en_dec('dec',$del_id);

		$deleted = $this->systemusers_model->update_system_user_status($del_id);
		if($deleted === false){
			$data = array("success" => 0, "message" => "Unable to disable this account. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Account disable Successfully");
		generate_json($data);
	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$dateUpdated = date('Y-m-d H:i:s');
		$dateCreated = date('Y-m-d H:i:s');
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$description,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);



		$isExist = $this->systemusers_model->getSystemUsersByDesc($description)->num_rows();

		if($description == "") {
			$data = array('success' => 0, 'message' => 'All fields are required');
		}else {

			if($isExist == 0) {
				$this->systemusers_model->create($data);
				$data = array('success' => 1, 'message' => 'Successfully Added');
			}else {
				$data = array('success' => 0, 'message' => $description.' already exist');
			}
		}

			echo json_encode($data);

	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {

		$id = $this->input->post('id');
		$description = $this->input->post('description');
		$dateUpdated = date('Y-m-d H:i:s');

		$data = array(
			$description,
			$dateUpdated,
			$id
		);

 		$this->systemusers_model->update($data);

		$data = array('success' => 1, 'message' => 'Successfully edited');
		echo json_encode($data);
	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {

		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->systemusers_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
