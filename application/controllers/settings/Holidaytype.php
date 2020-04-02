<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Holidaytype extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/holidaytype_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/holidaytype', $data);

	}

	//json result
	public function holidaytypejson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->holidaytype_model->getHolidayType(null,null,null)->num_rows(),
			"recordsFiltered" => $this->holidaytype_model->getHolidayType(null,null,null)->num_rows(),
			"data" => $this->holidaytype_model->getHolidayType($start,$length,$search)->result()
		);
		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$add_type = en_dec('dec',$this->input->post('add_type'));
		$payRatio = $this->input->post('payRatio');
		$payRatio2 = $this->input->post('payRatio2');
		$dateUpdated = date('Y-m-d H:i:s');
		$dateCreated = date('Y-m-d H:i:s');
		$userId = $this->session->user_id;
		$enabled = 1;

		$data = array(
			$description,
			$add_type,
			$payRatio,
			$payRatio2,
			$dateUpdated,
			$dateCreated,
			$userId,
			$enabled
		);

		if($description == "" || $payRatio == "" || $payRatio2 == "") {
			$data = array('success' => 0, 'message' => 'Please input a Holiday Type');
		}else {

			$isExist = $this->holidaytype_model->getHolidayTypeByDesc($description)->num_rows();

			if($isExist == 0) {

				$this->holidaytype_model->create($data);
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
		$currentDescription = $this->input->post('currentDescription');
		$payRatio = $this->input->post('payRatio');
		$payRatio2 = $this->input->post('payRatio2');
		$dateUpdated = date('Y-m-d H:i:s');
		$edit_type = $this->input->post('edit_type');

		$data = array(
			$description,
			$edit_type,
			$payRatio,
			$payRatio2,
			$dateUpdated,
			$id
		);

		if(empty($description) || empty($payRatio) || empty($payRatio2)){
			$data = array('success' => 0, 'message' => 'Please input a Holiday Type');
		}else{
			if($description !== $currentDescription){
				$isExist = $this->holidaytype_model->getHolidayTypeByDesc($description)->num_rows();

				if($isExist == 0){
					$this->holidaytype_model->update($data);
					$data = array('success' => 1, 'message' => 'Successfully edited');
				}else{
					$data = array('success' =>0, 'message' => $description.' already exist.');
				}
			}else{
				$this->holidaytype_model->update($data);
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

		$this->holidaytype_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
