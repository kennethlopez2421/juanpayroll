<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subdepartment extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/subdepartment_model');
		$this->load->model('settings/department_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'departments' => $this->department_model->getDepartment(null,null,null)->result()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/subdepartment', $data);

	}

	//json result
	public function subdeptjson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->subdepartment_model->getSubDept(null,null,null)->num_rows(),
			"recordsFiltered" => $this->subdepartment_model->getSubDept(null,null,null)->num_rows(),
			"data" => $this->subdepartment_model->getSubDept($start,$length,$search)->result()
		);

		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$subdept = sanitize($this->input->post('subdept'));
		$dateUpdated = date('Y-m-d H:i:s');
		$dateCreated = date('Y-m-d H:i:s');
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$description,
			$subdept,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		if(empty($description) || empty($subdept)){
			$data = array('success' => 0, 'message' => 'Please fill out all required fields');
		}else{

			$isExist = $this->subdepartment_model->getSubDeptByDesc($description,$subdept)->num_rows();

			if($isExist == 0) {
				$this->subdepartment_model->create($data);
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
		$description = sanitize($this->input->post('description'));
		$department = sanitize($this->input->post('department'));
		$dateUpdated = date('Y-m-d H:i:s');

		$data = array(
			$description,
			$department,
			$dateUpdated,
			$id
		);

			if(($description == "") || ($department == "")) {
				$data = array('success' => 0, 'message' => 'Please input requrired fields');
			}else{
				$isExist = $this->subdepartment_model->getSubDeptByDesc($description,$department)->num_rows();
				if($isExist == 0){
					$this->subdepartment_model->update($data);
					$data = array('success' => 1, 'message' => 'Edited Successfully');
				}else{
					$data = array('success' => 0, 'message' => $description.' already exsits');
				}
			}

			echo json_encode($data);
	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {
		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->subdepartment_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
