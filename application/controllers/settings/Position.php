<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Position extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/position_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'pos_access_lvl' => $this->position_model->get_pos_access_lvl()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/position', $data);

	}

	public function get_dept(){
		$dept = $this->position_model->getDept()->result_array();

		$data = array("dept" => $dept);
		generate_json($data);
	}

	public function get_sub_dept(){
		$deptId = $this->input->post('deptId');
		$subDept = $this->position_model->getSubDept($deptId)->result_array();
		$data = array("subDept" => $subDept);
		generate_json($data);
	}

	public function get_pos_info(){
		$posId = $this->input->post('posId');
		$deptId = $this->input->post('deptId');
		$posData = $this->position_model->getPosInfo($posId)->row_array();
		$dept = $this->position_model->getDept()->result_array();
		$subDept = $this->position_model->getSubDept($posData['deptId'])->result_array();

		$data = array("posData" => $posData, "dept" => $dept, "subDept" => $subDept);
		generate_json($data);
	}

	public function positionJSON() {
		$data = $this->position_model->getPosition()->result();
		echo json_encode($data);
	}

	//json result
	public function posjson() {
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');


		$data = array(
		"draw" => $draw,
		"recordsTotal" => $this->position_model->getPosition(null,null,null)->num_rows(),
		"recordsFiltered" => $this->position_model->getPosition(null,null,null)->num_rows(),
		"data" => $this->position_model->getPosition($start,$length,$search)->result()
		);

		echo json_encode($data);

	}

	//frontend
	public function add() {

	}

	//backend
	public function create() {
		$dept = sanitize($this->input->post('dept'));
		$subDept = sanitize($this->input->post('subDept'));
		$description = sanitize($this->input->post('description'));
		$pos_access_lvl = $this->input->post('pos_access_lvl');
		$dept_access = implode(',',$this->input->post('dept_access'));
		$dateUpdated = todaytime();
		$dateCreated = todaytime();
		$userId = $this->session->user_id;
		$enabled = 1;

		$insert_data = array(
			"description" => $description,
			"deptId" => $dept,
			"subDeptId" => $subDept,
			"pos_access_lvl" => $pos_access_lvl,
			"date_created" => $dateCreated,
			"enabled" => $enabled,
			"department_access" => $dept_access
		);

		foreach($insert_data as $row){
			if(empty($row)){
				$data = array("success" => 0, "message" => "Please fill up all required fields.");
				generate_json($data);
				exit();
			}
		}

		$isExist = $this->position_model->getPositionByDesc($description,$dept,$subDept)->num_rows();
		if($isExist > 0) {
			$data = array('success' => 0, 'message' => $description.' already exist');
			generate_json($data);
			exit();
		}

		$inserted = $this->position_model->create($insert_data);
		if($inserted === false){
			$data = array("success" => 0, "message" => "Unable to add new position. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array('success' => 1, 'message' => 'Successfully Added');
		generate_json($data);
	}

	//frontend
	public function edit() {

	}

	//backend
	public function update() {

		$id = $this->input->post('id');
		$description = sanitize($this->input->post('description'));
		$editPos_deptDesc = sanitize($this->input->post('editPos_deptDesc'));
		$editPos_subDeptDesc = sanitize($this->input->post('editPos_subDeptDesc'));
		$edit_pos_access_lvl = $this->input->post('edit_pos_access_lvl');
		$edit_dept_access = implode(',',$this->input->post('edit_dept_access'));
		$dateUpdated = todaytime();

		$data = array(
			$edit_pos_access_lvl,
			$edit_dept_access,
			$edit_pos_access_lvl,
			$description,
			$editPos_deptDesc,
			$editPos_subDeptDesc,
			$edit_pos_access_lvl,
			$dateUpdated,
			$editPos_deptDesc,
			$editPos_subDeptDesc,
			$id
		);
		
		if($description == "" || $editPos_deptDesc == "" || $editPos_subDeptDesc == "" || $edit_pos_access_lvl == ""){
			$data = array('success' => 0, 'message' => 'Please input a Position');
		}else{
			// $isExist = $this->position_model->getPositionByDesc($description)->num_rows();
			// if($isExist == 0){
				$this->position_model->update($data);
				$data = array('success' => 1, 'message' => "Edited Successfully!");
			// }else{
				// $data = array('success' => 0, 'message' => $description.' already exist.');
			// }
		}
			generate_json($data);


	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {
		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->position_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);
	}



}
