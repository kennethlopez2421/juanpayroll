<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Level extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/level_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/level', $data);

	}

	//json result
	public function leveljson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->level_model->getLevel($start,$length,$search)->num_rows(),
			"recordsFiltered" => $this->level_model->getLevel($start,$length,$search)->num_rows(),
			"data" => $this->level_model->getLevel($start,$length,$search)->result()
		);
		echo json_encode($data);
	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {

		$description = sanitize($this->input->post('description'));
		$hierarchy = $this->input->post('hierarchy');
		$dateUpdated = date('Y-m-d H:i:s');
		$dateCreated = date('Y-m-d H:i:s');
		$userId = $this->session->user_id;
		$enabled = 1;

		$level_data = array(
			"position" => $description,
			"hierarchy_lvl" => $hierarchy,
			"date_updated" => $dateUpdated,
			"date_created" => $dateCreated,
			"enabled" => $enabled
		);

		foreach($level_data as $d){
			if(empty($d)){
				$data = array("success" => 0, "message" => "Please fill up all required fields");
				generate_json($data);
				exit();
			}
		}

		if($this->level_model->getLevelByDesc($description)->num_rows() > 0){
			$data = array("success" => 0, "message" => "Employee Level already exists. Please try again.");
			generate_json($data);
			exit();
		}

		$current_hierarchy_lvl = $this->level_model->get_hierarchy_lvl($this->session->userdata('position_id'))->row()->hierarchy_lvl;
		if($hierarchy <= $current_hierarchy_lvl){
			$data = array("success" => 0, "message" => "Invalid Hierarchy Level. You cannot input a hierarchy level lower than or equal to your position, which is <u>".$current_hierarchy_lvl."</u>");
			generate_json($data);
			exit();
		}

		$inserted = $this->level_model->create($level_data);
		if($inserted == false){
			$data = array("success" => 0, "message" => "Unable to save Employee Level. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Employee Level Save Successfully");
		generate_json($data);
	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {

		$id = $this->input->post('id');
		$description = sanitize($this->input->post('description'));
		$current_desc = $this->input->post('current_desc');
		$hierarchy = $this->input->post('hierarchy');
		$dateUpdated = date('Y-m-d H:i:s');

		$update_data = array(
			$description,
			$hierarchy,
			$dateUpdated,
			$id
		);

		foreach($update_data as $d){
			if(empty($d)){
				$data = array("success" => 0, "message" => "Please fill up all required fields.");
				generate_json($data);
				exit();
			}
		}

		if($description != $current_desc){
			if($this->level_model->getLevelByDesc($description)->num_rows() > 0){
				$data = array("success" => 0, "message" => "Employee Level already exists. Please try again");
				generate_json($data);
				exit();
			}
		}

		$current_hierarchy_lvl = $this->level_model->get_hierarchy_lvl($this->session->userdata('position_id'))->row()->hierarchy_lvl;
		if($hierarchy <= $current_hierarchy_lvl){
			$data = array("success" => 0, "message" => "Invalid Hierarchy Level. You cannot input a hierarchy level lower than or equal to your position, which is <u>".$current_hierarchy_lvl."</u>");
			generate_json($data);
			exit();
		}

		$updated = $this->level_model->update($update_data);
		if($updated == false){
			$data = array("success" => 0, "message" => "Update Failed. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Updated Succesfully");
		generate_json($data);

	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {
		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->level_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}



}
