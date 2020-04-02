<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashadvance extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/cashadvance_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/cashadvance', $data);

	}

	//json result
	// public function cashAdJSON() {
	// 	$data = $this->position_model->getPosition()->result();
	// 	echo json_encode($data);
	// }

	//get data for data table
	public function cajson() {
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
		$column = array('caID', 'description', 'amount', 'ca_status');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'caID';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->cashadvance_model->getCashAdvance(null,null,null,null)->num_rows(),
			"recordsFiltered" => $this->cashadvance_model->getCashAdvance(null,null,null,null)->num_rows(),
			"data" => $this->cashadvance_model->getCashAdvance($start,$length,$search,$ordrBy)->result()
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
		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please Input Deductions Details');
		}
		else{
			$isExist = $this->cashadvance_model->getCAByDesc($description)->num_rows();
			if($isExist == 0){
				$this->cashadvance_model->create($data);
				$data = array('success' => 1, 'message' => 'Successfully edited');
			}else{
				$data = array('success' => 0, 'message' => $description. 'already exist');
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
		if($description == "") {
			$data = array('success' => 0, 'message' => 'Please input Cash Advance Details');
		}
		else {
			$isExist = $this->cashadvance_model->getCAByDesc($description)->num_rows();
			$isExistID = $this->cashadvance_model->getCAByID($id)->num_rows();
			if($isExist == 0) {
				$this->cashadvance_model->update($data);
				$data = array('success' => 1, 'message' => 'Successfully Edited');
			}else {
				if($isExistID == 1){
					$this->cashadvance_model->update($data);
					$data = array('success' => 1, 'message' => 'Successfully Edited');
				}else{
					$data = array('success' => 0, 'message' => $description.' already exist');
				}
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

		$this->cashadvance_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);

	}



}
