<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/city_model');
		$this->load->model('settings/country_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'countries' => $this->country_model->getCountry(null,null,null)->result()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/city', $data);

	}

	//json result
	public function cityjson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchData');
		// $search = null;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->city_model->getCity(null,null,null)->num_rows(),
			"recordsFiltered" => $this->city_model->getCity(null,null,null)->num_rows(),
			"data" => $this->city_model->getCity($start,$length,$search)->result()
		);

		echo json_encode($data);
	}

	// public function searchCityJSON($city){
	// 	// $city = $this->input->post('city');
	// 	$data = $this->city_model->searchCity($city);
	// 	echo json_encode($data);
	// }

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$countryId = $this->input->post('countryId');
		$dateUpdated = date('Y-m-d H:i:s');
		$dateCreated = date('Y-m-d H:i:s');
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$description,
			$countryId,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);


		if($description == "") {
			$data = array('success' => 0, 'message' => 'Please Input a City');
		}else {
				$isExist = $this->city_model->getCityByDesc($description,$countryId)->num_rows();

			if($isExist == 0) {
				$this->city_model->create($data);
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
		$countryId = sanitize($this->input->post('countryid'));
		$dateUpdated = date('Y-m-d H:i:s');

		$data = array(
			$description,
			$countryId,
			$dateUpdated,
			$id
		);
		if(empty($description) || empty($countryId)){
			$data = array('success' => 0, 'message' => 'Please fill out all fields');
		}else{

			$isExist = $this->city_model->getCityByDesc($description,$countryId)->num_rows();

			if($isExist == 0){
				$this->city_model->update($data);
				$data = array('success' => 1, 'message' => 'Successfully edited');
			}else{
				$data = array('success' => 0, 'message' => $description.' already exist');
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

		$this->city_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
