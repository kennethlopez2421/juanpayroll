<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sss extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/socialsec_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'sssTable' => $this->socialsec_model->getSSS(null,null)->result()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/sssview', $data);

	}

	//json result
	public function socialSecurityJSON() {
		$data = $this->SocialSec_model->getSSS()->result();
		echo json_encode($data);

	}

	public function sssjson(){
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');


		$data = array(
		"draw" => $draw,
		"recordsTotal" => $this->socialsec_model->getSSS(null,null)->num_rows(),
		"recordsFiltered" => $this->socialsec_model->getSSS(null,null)->num_rows(),
		"data" => $this->socialsec_model->getSSS($start,$length)->result()
		);

		echo json_encode($data);
	}

	public function get_ss_by_id(){
		$id = $this->input->post('id');
		$data = $this->socialsec_model->getssID($id)->result();

		$res = array("result" => $data);
		generate_json($res);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		//$id = $this->input->post('id');
		$rangeFrom = sanitize($this->input->post('addrangefrom_desc'));
		$rangeTo =  sanitize($this->input->post('addrangeto_desc'));
		$monthSalCred = sanitize($this->input->post('addSalCred_desc'));
		$ssER = sanitize($this->input->post('addER'));
		$ssEE = sanitize($this->input->post('addEE'));
		$ssTotal = sanitize($this->input->post('addTotalSS'));
		$ecER = sanitize($this->input->post('EC'));
		$tcER = sanitize($this->input->post('addContributionER'));
		$tcEE = sanitize($this->input->post('addContributionEE'));
		$tcTotal = sanitize($this->input->post('TotalContribution'));
		$SVO_totalContribution = sanitize($this->input->post('SVO_totalContribution'));
		$enabled = 1;
		//$userId = $this->session->user_id;
		//$enabled = 1;

		$data = array(
			$rangeFrom,
			$rangeTo,
			$monthSalCred,
			$ssER,
			$ssEE,
			$ssTotal,
			$ecER,
			$tcER,
			$tcEE,
			$tcTotal,
			$SVO_totalContribution,
			$enabled
		);


		if(empty($rangeFrom) || empty($rangeTo) || empty($monthSalCred) || empty($ssER) || empty($ssEE) || empty($ssTotal) || ($ecER == "") ||
			($tcER == "") || ($tcEE == "") || ($tcTotal == "") || $SVO_totalContribution == ""){
				$data = array('success' => 0, 'message' => 'Please input all required fields');
		}else{
			$this->socialsec_model->create($data);
			$data = array('success' => 1, 'message' => 'Successfully Added');
		}
		echo json_encode($data);
		// $isExist = $this->SocialSec_model->get_ss_by_id($description)->num_rows();

		// if($isExist == 0) {

		// 	$this->position_model->create($data);
		// 	$data = array('success' => 1, 'message' => 'Successfully Added');
		// 	echo json_encode($data);
		// }else {
		// 	$data = array('success' => 0, 'message' => $description.' already exist');

		// }
	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {
		$id = $this->input->post('id');
		$rangeFrom = sanitize($this->input->post('editrangefrom_desc'));
		$rangeTo =  sanitize($this->input->post('editrangeto_desc'));
		$monthSalCred = sanitize($this->input->post('editSalCred_desc'));
		$ssER = sanitize($this->input->post('editER'));
		$ssEE = sanitize($this->input->post('editEE'));
		$ssTotal = sanitize($this->input->post('editSSTotal'));
		$ecER = sanitize($this->input->post('editEC'));
		$tcER = sanitize($this->input->post('editContirbutionER'));
		$tcEE = sanitize($this->input->post('editContributionEE'));
		$tcTotal = sanitize($this->input->post('editTotalCont'));
		$SV_VM_OFW = sanitize($this->input->post('edit_SVO_totalContribution'));
		//$dateUpdated = todaytime();

		$data = array(

			$rangeFrom,
			$rangeTo,
			$monthSalCred,
			$ssER,
			$ssEE,
			$ssTotal,
			$ecER,
			$tcER,
			$tcEE,
			$tcTotal,
			$SV_VM_OFW,
			$id,
		);

		if(empty($rangeFrom) || empty($rangeTo) || empty($monthSalCred) || empty($ssER) || empty($ssEE) || empty($ssTotal) || ($ecER == "") ||
			($tcER == "") || ($tcEE == "") || ($tcTotal == "")){
				$data = array('success' => 0, 'message' => 'Please input all required fields');
		}else{
			$this->socialsec_model->update($data);
			$data = array('success' => 1, 'message' => "Edited Successfully!");
		}
		//echo json_encode($data);
		generate_json($data);

	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {
		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->socialsec_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);
	}



}
