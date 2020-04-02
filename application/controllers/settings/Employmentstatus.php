<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employmentstatus extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/employmentstatus_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/employmentstatus', $data);

	}

	//json result
	public function empStatusJSON() {
		$data = $this->employmentstatus_model->getEmploymentStatus()->result();
		echo json_encode($data);
	}

	public function empjson() {

			$draw = $this->input->get('draw');
			$start = $this->input->get('start');
			$length = $this->input->get('length');
			$search = $this->input->get('searchValue');


			$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->employmentstatus_model->getEmploymentStatus(null,null,null)->num_rows(),
			"recordsFiltered" => $this->employmentstatus_model->getEmploymentStatus(null,null,null)->num_rows(),
			"data" => $this->employmentstatus_model->getEmploymentStatus($start,$length,$search)->result()
		);
		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$reg_holiday = $this->input->post('reg_holiday');
		$spec_holiday = $this->input->post('spec_holiday');
		$add_leave = $this->input->post('add_leave');
		$dateUpdated = todaytime();
		$dateCreated = todaytime();
		$userId = $this->session->userdata('user_id');
		$enabled = 1;

		$data = array(
			$description,
			$reg_holiday,
			$spec_holiday,
			$add_leave,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		//$this->department_model->create($data);



		if($description == "") {
			$data = array('success' => 0, 'message' => 'Please input Employment Status');
		}else {
			$isExist = $this->employmentstatus_model->getEmpStatusByDesc($description)->num_rows();
			if($isExist == 0) {

				$this->employmentstatus_model->create($data);
				$data = array('success' => 1, 'message' => 'Successfully Added');
				// echo json_encode($data);
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
		$current_desc = $this->input->post('current_desc');
		$reg_holiday = $this->input->post('reg_holiday');
		$spec_holiday = $this->input->post('spec_holiday');
		$update_leave = $this->input->post('update_leave');
		$dateUpdated = todaytime();

		$data = array(
			$description,
			$reg_holiday,
			$spec_holiday,
			$update_leave,
			$dateUpdated,
			$id
		);

		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please input Employment Status');
		}else{
			if($description != $current_desc){
				$isExist = $this->employmentstatus_model->getEmpStatusByDesc($description)->num_rows();
				if($isExist == 0){
					$this->employmentstatus_model->update($data);
					$data = array('success' => 1, 'message' => "Edited Successfully!");
				}else{
					$data = array('success' => 0, 'message' => $description.' already exist');
				}
			}else{
				$this->employmentstatus_model->update($data);
				$data = array('success' => 1, 'message' => "Edited Successfully!");
			}
		}
		generate_json($data);
	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {

		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->employmentstatus_model->destroy($data);

		$data = array('success' => 1, 'message' => "Edited Successfully!");
		echo json_encode($data);

	}



}
