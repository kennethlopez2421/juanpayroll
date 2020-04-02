<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends CI_Controller {

	 public function __construct() {
	 	parent::__construct();
	 	$this->load->model('settings/department_model');
		$this->isLoggedIn();
	 }

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'dept_types' => $this->model->get_department_type()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/department', $data);

	}

	//json result
	public function departmentJSON() {
		$data = $this->department_model->getDepartment()->result();
		echo json_encode($data);
	}

	//show result on the table-grid
	public function deptjson() {

			$draw = $this->input->get('draw');
			$start = $this->input->get('start');
			$length = $this->input->get('length');
			$search = $this->input->get('searchValue');


			$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->department_model->getDepartment(null,null,null)->num_rows(),
			"recordsFiltered" => $this->department_model->getDepartment(null,null,null)->num_rows(),
			"data" => $this->department_model->getDepartment($start,$length,$search)->result()
		);
		echo json_encode($data);
	}


	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$dept_type = $this->input->post('dept_type');
		$dateUpdated = todaytime();
		$dateCreated = todaytime();
		$userId = $this->session->userdata('user_id');
		$enabled = 1;

		$data = array(
			$description,
			$dept_type,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		//$this->department_model->create($data);
		if($description == "" || $dept_type == "") {
			$data = array('success' => 0, 'message' => 'Please Input a City');
		}else {

			$isExist = $this->department_model->getDepartmentByDesc($description)->num_rows();

			if($isExist == 0) {

				$this->department_model->create($data);
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
		$edit_dept_type = $this->input->post('edit_dept_type');
		$dateUpdated = todaytime();

		$data = array(
			$description,
			$edit_dept_type,
			$dateUpdated,
			$id
		);

		$this->department_model->update($data);

		$data = array('success' => 1, 'message' => "Edited Successfully!");
		generate_json($data);

	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {
		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->department_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);

	}



}
