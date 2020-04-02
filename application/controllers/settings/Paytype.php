<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paytype extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/paytype_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/paytype', $data);

	}

	//json result
	public function paytypejson() {
		$data = $this->paytype_model->getPayType()->result();
		echo json_encode($data);
	}

	//show result on the table-grid
	public function payJSON() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');


		$data = array(
		"draw" => $draw,
		"recordsTotal" => $this->paytype_model->getPayType(null,null,null)->num_rows(),
		"recordsFiltered" => $this->paytype_model->getPayType(null,null,null)->num_rows(),
		"data" => $this->paytype_model->getPayType($start,$length,$search)->result()
		);
		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('pay_desc'));
		$dateUpdated = todaytime();
		$dateCreated = todaytime();
		$frequency = $this->input->post('frequency');
		$date_range_from = $this->input->post('date_range_from');
		$date_range_to = $this->input->post('date_range_to');

		if($date_range_from > $date_range_to){
			$data = array("success" => 0, "message" => "Oops! Invalid date range. Please try again.");
			generate_json($data);
			exit();
		}

		$date_range = $date_range_from.'-'.$date_range_to;
		$userId = $this->session->userdata('user_id');
		$enabled = 1;

		$data = array(
			$description,
			$frequency,
			$date_range,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled,

		);

		if($description == ""){

			$data = array('success' => 0, 'message' => 'Please input a Pay Type');
		}
		else{
			$isExist = $this->paytype_model->getPayTypeByDesc($description)->num_rows();

			if($isExist == 0) {

				$this->paytype_model->create($data);
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
		$description = sanitize($this->input->post('pay_desc'));
		$dateUpdated = date('Y-m-d H:i:s');

		$data = array(
			$description,
			$dateUpdated,
			$id
		);

		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please input a Pay Type');
		}else{
			$isExist = $this->paytype_model->getPayTypeByDesc($description)->num_rows();

			if($isExist == 0 ){
				$this->paytype_model->update($data);
				$data = array('success' => 1, 'message' => 'Successfully edited');
			}else{
				$data = array('success' => 0, 'message' => $description.' already exist.');
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

		$this->paytype_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
