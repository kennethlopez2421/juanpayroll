<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worksite extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/worksite_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			"cities" => $this->worksite_model->getCity()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/worksite', $data);

	}


	public function get_worksite_json(){
		$searchValue = $this->input->post('searchValue');
    $data = $this->worksite_model->get_worksite_json($searchValue);
		echo json_encode($data);
	}

	//json result
	public function worksitejson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->worksite_model->getWorkSite(null,null)->num_rows(),
			"recordsFiltered" => $this->worksite_model->getWorkSite(null,null)->num_rows(),
			"data" => $this->worksite_model->getWorkSite($start,$length)->result()
		);
		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$distance = sanitize($this->input->post('distance'));
		$city = $this->input->post('city');
		$loc_address = $this->input->post('loc_address');
		$loc_latitude = $this->input->post('loc_latitude');
		$loc_longitude = $this->input->post('loc_longitude');
		$dateUpdated = date('Y-m-d H:i:s');
		$dateCreated = date('Y-m-d H:i:s');
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$description,
			$loc_address,
			$loc_latitude,
			$loc_longitude,
			$distance,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		if(empty($description) || empty($loc_address) || empty($loc_latitude) || empty($loc_longitude) || empty($distance)){
			$data = array('success' =>0, 'message' => 'Please input a Work site');
		}else{

			$isExist = $this->worksite_model->getWorkSiteByDesc($description)->num_rows();

			if($isExist == 0) {
				$this->worksite_model->create($data);
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
		$city = $this->input->post('city');
		$currentDesc = $this->input->post('currentDesc');
		$location = $this->input->post('edit_location');
		$loc_latitude = $this->input->post('edit_loc_latitude');
		$loc_longitude = $this->input->post('edit_loc_longitude');
		$distance = $this->input->post('distance');
 		$dateUpdated = date('Y-m-d H:i:s');

		$data = array(
			$description,
			$city,
			$location,
			$loc_latitude,
			$loc_longitude,
			$distance,
			$dateUpdated,
			$id
		);

		if($description == "" || $location == "" || $loc_latitude == "" || $loc_longitude == "" || $distance == ""){
			$data = array('success' => 0, 'message' => 'Please input a Work Site');
		}else{
			if($description !== $currentDesc){
				$isExist = $this->worksite_model->getWorkSiteByDesc($description)->num_rows();

				if($isExist == 0){
					$this->worksite_model->update($data);
					$data = array('success' => 1, 'message' => 'Successfully edited');
				}else{
					$data = array('success' =>0, 'message' => $description.' already exist');
				}
			}else{
				$this->worksite_model->update($data);
				$data = array('success' => 1, 'message' => 'Successfully edited');
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

		$this->worksite_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}

}
