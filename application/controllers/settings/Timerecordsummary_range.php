<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timerecordsummary_range extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/timerecordsummary_range_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/timerecordsummary_range', $data);

	}
	//get data for data table
	public function trs_range() {
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
		$column = array('id','range_start','range_end','description','current_date_used');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->timerecordsummary_range_model->gettrsrange(null,null,null,null)->num_rows(),
			"recordsFiltered" => $this->timerecordsummary_range_model->gettrsrange(null,null,null,null)->num_rows(),
			"data" => $this->timerecordsummary_range_model->gettrsrange($start,$length,$search,$ordrBy)->result()
		);

		echo json_encode($data);


	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {
		$description = sanitize($this->input->post('description'));
		$start_date = sanitize($this->input->post('start_date'));
		$end_date = sanitize($this->input->post('end_date'));
		$dateUpdated = todaytime();
		$userId = $this->session->user_id;
		$date_range_status = 0;
		$isactive = 1;

		$data = array( $description, $start_date, $end_date, $dateUpdated, $date_range_status, $userId, $isactive
		);
		if(empty($description) || empty($start_date) || empty($end_date)){
			$data = array('success' => 0, 'message' => 'Please Input Details');
		}
		else{
			$this->timerecordsummary_range_model->create($data);
			$data = array('success' => 1, 'message' => 'Data Successfully Added');
		}
	echo json_encode($data);
	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$id = $this->input->post('id');
		$description = sanitize($this->input->post('description'));
		$dateUpdated = todaytime();

		$data = array(
			$start_date,
			$end_date,
			$description,
			$dateUpdated,
			$id
		);
		if(empty($start_date) || empty($end_date) || empty($description)){
			$date = array('success' => 0, 'message' => 'Please input Details');
		}else{
			$this->timerecordsummary_range_model->update($data);
			$data = array('success' => 1, 'message' => 'Data successfully edited');
		}
			echo json_encode($data);
		
	}
	//this will setup the default date range of the time record summary
	public function set_active(){
		$id = $this->input->post('id');
		$setzero = 0;
		$setone = 1;
		$status = 1;
		$data = array($setone,$id,$status);
		if(!(empty($id))){
			//this will remove all 1 in current_date_used and set to zero
			$this->timerecordsummary_range_model->remove_active($setzero,$status);
			$this->timerecordsummary_range_model->set_active($data);
			$data = array("success" => 1, "message" => "Successfully set to default time record summary range");
		}else{
			$data = array("success" => 0, "message" => "There is an error on the process");
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

		$this->timerecordsummary_range_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);

	}



}
