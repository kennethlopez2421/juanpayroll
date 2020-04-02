<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Relationship extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/relationship_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/relationship', $data);

	}

	//json result
	public function relationshipJSON() {
		$data = $this->relationship_model->getRelationship()->result();
		echo json_encode($data);
	}

	public function reljson(){
			$draw = $this->input->get('draw');
			$start = $this->input->get('start');
			$length = $this->input->get('length');
			$search = $this->input->get('searchValue');


			$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->relationship_model->getRelationship(null,null,null)->num_rows(),
			"recordsFiltered" => $this->relationship_model->getRelationship(null,null,null)->num_rows(),
			"data" => $this->relationship_model->getRelationship($start,$length,$search)->result()
		);
		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$dateUpdated = todaytime();
		$dateCreated = todaytime();
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$description,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		//$this->relationship_model->create($data);
		if($description == ""){
			$data = array('success' => 0, 'message' => 'Please input a Relationship');
		}else{
			$isExist = $this->relationship_model->getRelationshipByDesc($description)->num_rows();

			if($isExist == 0) {

				$this->relationship_model->create($data);
				$data = array('success' => 1, 'message' => 'Successfully Added');
			}else {
				$data = array('success' => 0, 'message' => $description.' already exist');
			}
		}
			echo json_encode($data);

	}

	//frontend update by user
	public function edit() {

	}

	//backend update by admin
	public function update() {

		$id = $this->input->post('id');
		$description = sanitize($this->input->post('description'));
		$dateUpdated = todaytime();

		$data = array(
			$description,
			$dateUpdated,
			$id
		);

		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please input Relationship');
		}else{
			$isExist = $this->relationship_model->getRelationshipByDesc($description)->num_rows();
				if($isExist == 0){
					$this->relationship_model->update($data);
					$data = array('success' => 1, 'message' => "Edited Successfully!");
				}else{
					$data = array('success' => 0, 'message' => $description.' already exist');
				}

		}

		generate_json($data);
	}

	//frontend delete data (enable = 0)
	public function delete() {

	}

	//backend delete data(enable = 0)
	public function destroy() {
		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->relationship_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		generate_json($data);

	}



}
