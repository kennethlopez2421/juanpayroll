<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salarycategory extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/salarycategory_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/salarycategory', $data);

	}

	//json result
	public function SalaryCategoryJSON() {
		$data = $this->salarycategory_model->getSalCat()->result();
		echo json_encode($data);
	}


	public function salcatjson(){

			$draw = $this->input->get('draw');
			$start = $this->input->get('start');
			$length = $this->input->get('length');
			$search = $this->input->get('searchValue');


			$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->salarycategory_model->getSalCat(null,null,null)->num_rows(),
			"recordsFiltered" => $this->salarycategory_model->getSalCat(null,null,null)->num_rows(),
			"data" => $this->salarycategory_model->getSalCat($start,$length,$search)->result()
		);
		echo json_encode($data);

	}
	//frontend
	public function add() {

	}

	//backend
	//Adding of new data to Database,
	public function create() {
		$description = sanitize($this->input->post('description'));
		$dateUpdated = todaytime();
		$dateCreated = todaytime();
		$userId = $this->session->userdata('user_id');
		$enabled = 1;

		$data = array(
			$description,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		if($description == ""){
			$data = array('success' => 0, 'message' => 'Please input a Salary Category');
		}else{
			$isExist = $this->salarycategory_model->getSalCatByDesc($description)->num_rows();

			if($isExist == 0) {

				$this->salarycategory_model->create($data);
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
		$dateUpdated = todaytime();

		$data = array(
			$description,
			$dateUpdated,
			$id
		);
		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please input Salary Category');
		}else{
			$isExist = $this->salarycategory_model->getSalCatByDesc($description)->num_rows();
			if($isExist == 0){
				$this->salarycategory_model->update($data);
				$data = array('success' => 1, 'message' => "Edited Successfully!");
			}else{
				$data = array('success' => 0, 'message' => $description.' already exist.');
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

		$this->salarycategory_model->destroy($data);

			$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);

	}



}
