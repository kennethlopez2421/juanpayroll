<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employeecharges extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->model('settings/employeecharges_model');

	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/employeecharges', $data);

	}

	//json result
	public function chargesjson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
		$column = array('employee_charges_id', 'description', 'amount', 'charge_status');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'employee_charges_id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;
		// $orderascdesc = $this->input->get('order')[0]['dir'];


		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->employeecharges_model->getEmployee(null,null,null,null)->num_rows(),
			"recordsFiltered" => $this->employeecharges_model->getEmployee(null,null,null,null)->num_rows(),
			"data" => $this->employeecharges_model->getEmployee($start,$length,$search,$ordrBy)->result()
		);

		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$amount = sanitize($this->input->post('amount'));
		$description = sanitize($this->input->post('description'));
		$dateUpdated = date('Y-m-d H:i:s');
		$dateCreated = date('Y-m-d H:i:s');
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$amount,
			$description,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);



		if($description == "") {
			$data = array('success' => 0, 'message' => 'Please input a Country');
		}
		else if (empty($amount) || $amount <= 0){
			$data = array('success' => 0, 'message' => 'Please input amount greater than zero');
		}
		else {
			$isExist = $this->employeecharges_model->getChargesByDesc($description)->num_rows();
			// $this->employeecharges_model->create($data);
			// $data = array('success' => 1, 'message' => 'Successfully Added');
			if($isExist == 0) {
				$this->employeecharges_model->create($data);
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

		$id = sanitize($this->input->post('id'));
		$amount = sanitize($this->input->post('amount'));
		$description = sanitize($this->input->post('description'));
		$dateUpdated = date('Y-m-d H:i:s');

		$data = array(
			$amount,
			$description,
			$dateUpdated,
			$id
		);

		if(empty($description)){
			$data = array('success' => 0, 'message' => 'Please input Description Charges');
		}
		else if (empty($amount) || $amount <= 0){
			$data = array('success' => 0, 'message' => 'Please input amount greater than zero');
		}
		else{
			$isExist = $this->employeecharges_model->getChargesByDesc($description)->num_rows();
			$isExistID = $this->employeecharges_model->getChargesByDesc($description)->num_rows();
			// $this->employeecharges_model->update($data);
			// $data = array('success' => 1, 'message' => 'Successfully edited');
			if($isExist == 0) {
				$this->employeecharges_model->update($data);
				$data = array('success' => 1, 'message' => 'Successfully Edited');
			}else {
				if($isExistID == 1){
					$this->employeecharges_model->update($data);
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

		$this->employeecharges_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
