<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worktype extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/worktype_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/worktype', $data);

	}

	//json result
	public function worktypejson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->worktype_model->getWorkType(null,null,null)->num_rows(),
			"recordsFiltered" => $this->worktype_model->getWorkType(null,null,null)->num_rows(),
			"data" => $this->worktype_model->getWorkType($start,$length,$search)->result()
		);

		echo json_encode($data);
	}

	//frontend
	public function add() {

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

		if(empty($description)) {
			$data = array('success' => 0, 'message' => 'Please input a Work Type');
		}else{

			$isExist = $this->worktype_model->getWorkTypeByDesc($description)->num_rows();

			if($isExist == 0) {
				$this->worktype_model->create($data);
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
		$dateUpdated = date('Y-m-d H:i:s');

		$data = array(
			$description,
			$dateUpdated,
			$id
		);

		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please input a Work Type');
		}else{
			$isExist = $this->worktype_model->getWorkTypeByDesc($description)->num_rows();
			if($isExist == 0){
				$this->worktype_model->update($data);
				$data = array('success' => 1, 'message' => 'Successfully edited');
			}else{
				$data = array('success' => 0, 'message' => $description.'  already exist');
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

		$this->worktype_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
