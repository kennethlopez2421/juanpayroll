<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deductions extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/deductions_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/deductions', $data);

	}

	//json result
	public function deductionsjson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
		$column = array('deductionid', 'description', 'amount', 'saldeductstatus');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'caID';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->deductions_model->getDeductions(null,null,null,null)->num_rows(),
			"recordsFiltered" => $this->deductions_model->getDeductions(null,null,null,null)->num_rows(),
			"data" => $this->deductions_model->getDeductions($start,$length,$search,$ordrBy)->result()
		);
		//pass data from php to js
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
		if($description == "") {
			$data = array('success' => 0, 'message' => 'Please Input Deduction Details');
		}
		else {
			$isExist = $this->deductions_model->getDeductionsByDesc($description)->num_rows();
			if($isExist == 0) {
				$this->deductions_model->create($data);
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
			$data = array('success' => 0, 'message' => 'Please Input Deductions Details');
		}
		else{
			$isExist = $this->deductions_model->getDeductionsByDesc($description)->num_rows();
			$this->deductions_model->update($data);
			$data = array('success' => 1, 'message' => "Edited Successfully!");
			// if($isExist == 0){
			// 	$this->deductions_model->update($data);
			// 	$data = array('success' => 1, 'message' => 'Successfully edited');
			// }else{
			// 	$data = array('success' => 0, 'message' => $description. 'already exist');
			// }
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

		$this->deductions_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
