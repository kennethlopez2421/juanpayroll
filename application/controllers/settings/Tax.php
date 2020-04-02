<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tax extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/tax_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'datas' => $this->tax_model->getTax(null,null)->result()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/tax', $data);

	}

	//json result
	public function taxjson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->tax_model->getTax(null,null)->num_rows(),
			"recordsFiltered" => $this->tax_model->getTax(null,null)->num_rows(),
			"data" => $this->tax_model->getTax($start,$length)->result()
		);
		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$aibLowerLimit = $this->input->post('aibLowerLimit');
		$aibUpperLimit = $this->input->post('aibUpperLimit');
		$tr1LowerLimit = $this->input->post('tr1LowerLimit');
		$tr1ExcessLimit = $this->input->post('tr1ExcessLimit');
		$tr2LowerLimit = $this->input->post('tr2LowerLimit');
		$tr2ExcessLimit = $this->input->post('tr2ExcessLimit');

		$data = array(
			$aibLowerLimit,
			$aibUpperLimit,
			$tr1LowerLimit,
			$tr1ExcessLimit,
			$tr2LowerLimit,
			$tr2ExcessLimit,
			1
		);


		$this->tax_model->create($data);

		$data = array('success' => 1, 'message' => 'Successfully Added');

		echo json_encode($data);

	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {

		$id = $this->input->post('taxId');
		$aibLowerLimit = $this->input->post('aibLowerLimit');
		$aibUpperLimit = $this->input->post('aibUpperLimit');
		$tr1LowerLimit = $this->input->post('tr1LowerLimit');
		$tr1ExcessLimit = $this->input->post('tr1ExcessLimit');
		$tr2LowerLimit = $this->input->post('tr2LowerLimit');
		$tr2ExcessLimit = $this->input->post('tr2ExcessLimit');

		$data = array(
			$aibLowerLimit,
			$aibUpperLimit,
			$tr1LowerLimit,
			$tr1ExcessLimit,
			$tr2LowerLimit,
			$tr2ExcessLimit,
			$id
		);

		$this->tax_model->update($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);
	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {

		$id = $this->input->post('id');

 		$data = array(0,$id);

		$this->tax_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
