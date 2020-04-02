<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaves extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/leaves_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/leaves', $data);

	}

	//data table
	public function leavesjson() {
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');


		$data = array(
		"draw" => $draw,
		"recordsTotal" => $this->leaves_model->getLeaves(null,null,null)->num_rows(),
		"recordsFiltered" => $this->leaves_model->getLeaves(null,null,null)->num_rows(),
		"data" => $this->leaves_model->getLeaves($start,$length,$search)->result()
		);

		echo json_encode($data);


	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {
		$description = sanitize($this->input->post('description'));
		$days_before_filling = $this->input->post('days_before_filling');
		$late_filling = $this->input->post('late_filling');
		$consecutive_filling = $this->input->post('consecutive_filling');
		$dateUpdated = todaytime();
		$dateCreated = todaytime();
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$description,
			$days_before_filling,
			$late_filling,
			$consecutive_filling,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		//$this->relationship_model->create($data);

		if($description == "") {
			$data = array('success' => 0, 'message' => 'Please input a Leaves');
		}else {

			$isExist = $this->leaves_model->getLeavesByDesc($description)->num_rows();

			if($isExist == 0) {

				$this->leaves_model->create($data);
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
		$edit_days_before_filling = $this->input->post('edit_days_before_filling');
		$edit_late_filling = $this->input->post('edit_late_filling');
		$edit_consecutive_filling = $this->input->post('edit_consecutive_filling');
		$dateUpdated = todaytime();

		$data = array(
			$description,
			$edit_days_before_filling,
			$edit_late_filling,
			$edit_consecutive_filling,
			$dateUpdated,
			$id
		);
		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please input a Leaves');
		}else{
			$isExist = $this->leaves_model->getLeavesByDesc($description,$id)->num_rows();
			if($isExist == 0){
				$this->leaves_model->update($data);
				$data = array('success' => 1, 'message' => "Edited Successfully!");
			}else{
				$data = array('success' =>0, 'message' => $description.' already exist.');
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

		$this->leaves_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);

	}



}
