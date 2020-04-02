<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Overtimepays extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('transactions/Overtimepays_model');
		$this->isLoggedIn();
	}

	public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
	}

	public function isLoggedIn() {
	  //this will destroy the session if the user not logged in
		if($this->session->userdata('isLoggedIn') == false) {
			if(empty($this->session->userdata('position_id'))) { //kapag destroyed na ung session
				header("location:".base_url('Main/logout'));
				exit();
			}
		}else{
			if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
				header("location:".base_url('Main/logout'));
				exit();
			}
		}
	}

	//views
	public function index($token = "") {
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)? $this->model->getDepartment($user_dept): $this->model->getDepartment();
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/overtimepays', $data);

	}

	//json result
	// public function cashAdJSON() {
	// 	$data = $this->position_model->getPosition()->result();
	// 	echo json_encode($data);
	// }

	//get data for data table
	public function opjson() {
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
		//for sorting
		$column = array('op.employee_id', 'op.purpose', 'op.minutes_of_overtime', 'op.status');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->Overtimepays_model->getotpays(null,null,null,null)->num_rows(),
			"recordsFiltered" => $this->Overtimepays_model->getotpays(null,null,null,null)->num_rows(),
			"data" => $this->Overtimepays_model->getotpays($start,$length,$search,$ordrBy)->result()
		);

		echo json_encode($data);


	}

	public function getotpays_waiting_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Overtimepays_model->getOtPays_waiting_json($search);
    echo json_encode($data);
  }

	public function getotpays_approved_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Overtimepays_model->getOtPays_approved_json($search);
    echo json_encode($data);
  }

	public function getotpays_certified_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Overtimepays_model->getOtPays_certified_json($search);
    echo json_encode($data);
  }

	public function updateotstatus(){
		$this->isLoggedIn();

		$ot_id = $this->input->post('ot_id');
		$status = $this->input->post('status');
		$update = $this->input->post('update');
		$type = en_dec('dec', $this->input->post('type'));

		if(empty($ot_id) || empty($status) || empty($update)){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again");
			generate_json($data);
			exit();
		}

		$emp_id = $this->session->userdata('emp_idno');
		$update_data = array($status,$emp_id,$ot_id);
		$updated = $this->Overtimepays_model->updateOtStatus($update_data,$update,$type);

		if($updated == false){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Successfully ".ucfirst($status));
		generate_json($data);
	}

	public function get_employee_by_dept(){
		$dept_id = $this->input->post('dept_id');
		if($dept_id == ""){
			$data = array("success" => 0, "message" => "Unable to find any employee");
			generate_json($data);
			exit();
		}

		$emp = $this->Overtimepays_model->get_emp_by_dept($dept_id);
		if($emp->num_rows() == 0){
			$data = array("success" => 0, "message" => "No available employee under this department");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "emp" => $emp->result_array());
		generate_json($data);

	}
	//frontend
	public function add($token = "") {
		$user_dept = $this->session->userdata('deptId');
		$department = (($this->session->login_type != 'admin' || $user_dept != hr_id()) && $user_dept != 0)? $this->model->getDepartment($user_dept): $this->model->getDepartment();
		$getemployee = $this->Overtimepays_model->getEmpID();
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department,
			'employee' => $getemployee->result()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/overtimepays_add', $data);
	}
	public function edit($token = "", $id) {
		$user_dept = $this->session->userdata('deptId');
		$department = (($this->session->login_type != 'admin' || $user_dept != hr_id()) && $user_dept != 0)? $this->model->getDepartment($user_dept): $this->model->getDepartment();
		$result = $this->Overtimepays_model->getCAByID($id)->row();
		$getemployee = $this->Overtimepays_model->get_emp_by_dept($result->deptId);
		$caID = $id;
		$data = array(
			'token' => $token,
			'result' => $result,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department,
			'employee' => $getemployee
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/overtimepays_edit', $data);
	}



	//backend
	// public function create() {
	// 	$description = sanitize($this->input->post('description'));
	// 	$dateUpdated = todaytime();
	// 	$dateCreated = todaytime();
	// 	$userId = $this->session->user_id;
	// 	$enabled = 1;

	// 	$data = array(
	// 		$description,
	// 		$dateUpdated,
	// 		$dateCreated,
	// 		$userId,
	// 		$enabled
	// 	);
	// 	if(empty($description)){
	// 		$data = array('success' => 0, 'message' => 'Please Input Deductions Details');
	// 	}
	// 	else{
	// 		$isExist = $this->cashadvance_model->getCAByDesc($description)->num_rows();
	// 		if($isExist == 0){
	// 			$this->cashadvance_model->create($data);
	// 			$data = array('success' => 1, 'message' => 'Successfully edited');
	// 		}else{
	// 			$data = array('success' => 0, 'message' => $description. 'already exist');
	// 		}
	// 	}
	// echo json_encode($data);
	// }
	public function add_otpays(){
		$employee_id_no = $this->input->post('employee_id_no');
		$purpose = $this->input->post('purpose');
		$minutes_of_overtime = $this->input->post('minutes_of_overtime');
		$date_rendered = $this->input->post('date_rendered');
		$created_by = $this->session->userdata('emp_idno');
		$type = en_dec('dec',$this->input->post('type'));
		$data = array($employee_id_no,$purpose,$minutes_of_overtime);

		if(empty($employee_id_no) || empty($purpose) || empty($date_rendered)){
			$data = array('succeess' => 0, 'message' => "Please input Overtime pays details");
			generate_json($data);
			exit();
		}

		if($minutes_of_overtime == 0){
			$data = array("success" => 0, "message" => "Insufficient minutes. Please try again.");
			generate_json($data);
			exit();
		}

		$isExist = $this->Overtimepays_model->check_filed_ot($employee_id_no,$date_rendered);
		if($isExist->num_rows() > 0){
			$data = array("success" => 0, "message" => "You already had an existing overtime filed on this date. Please try again.");
			generate_json($data);
			exit();
		}

		$ot_data = array(
			"employee_id" => $employee_id_no,
			"purpose" => $purpose,
			"minutes_of_overtime" => $minutes_of_overtime,
			"date_rendered" => $date_rendered,
			"status" => "waiting",
			"type" => $type,
			"created_by" => $created_by
		);

		$inserted = $this->Overtimepays_model->create($ot_data);
		if($inserted == false){
			$data = array("success" => 0, "message" => "Unable to save Ovetime Pay");
			generate_json($data);
			exit();
		}

		$data = array('success' => 1, 'message' => "Data successfully inserted");

		generate_json($data);
	}

	public function update_pays(){
		$caID = $this->input->post('caID');
		$employee_id_no = $this->input->post('employee_id_no');
		$purpose = $this->input->post('purpose');
		$minutes_of_overtime = $this->input->post('minutes_of_overtime');
		$date_rendered = $this->input->post('date_rendered');
		$type = en_dec('dec',$this->input->post('type'));
		// die($type);

		if(empty($caID) || empty($employee_id_no) || empty($purpose) || empty($minutes_of_overtime)){
			$data = array('success' => 0, 'message' => "Please input overtime pays details");
			generate_json($data);
			exit();
		}

		if($minutes_of_overtime == 0){
			$data = array("success" => 0, "message" => "Insufficient minutes. Please try again.");
			generate_json($data);
			exit();
		}

		$isExist = $this->Overtimepays_model->check_filed_ot($employee_id_no,$date_rendered,$caID);
		if($isExist->num_rows() > 0){
			$data = array("success" => 0, "message" => "You already had an existing overtime filed on this date. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array($employee_id_no,$purpose,$minutes_of_overtime,$date_rendered,$caID);
		$update_data = array(
			"employee_id" => $employee_id_no,
			"purpose" => $purpose,
			"minutes_of_overtime" => $minutes_of_overtime,
			"date_rendered" => $date_rendered,
			"type" => $type
		);

		$updated = $this->Overtimepays_model->update($update_data,$caID);
		if($updated === false){
			$data = array("success" => 0, "message" => "Unable to update Overtime. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Data successfully updated successfully");
		generate_json($data);
	}

	public function update_batch_status(){
    $this->isLoggedIn();

    $status = $this->input->post('status');
    $batch_status = $this->input->post('batch_status');
    $batch = $this->input->post('batch');

    if(empty($status) || count((array)$batch) == 0){
      $data = array("success" => 0, "message" => "Something went wrong. Please try again.");
      generate_json($data);
      exit();
    }

    $batch_ = array();
    $batch_decrypt[] = decrypt_array($batch);
    $batch_serialize = implode(',',$batch_decrypt[0]);
    $updated = $this->Overtimepays_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." overtime. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Batch ".ucfirst($batch_status)." Successfull");
    generate_json($data);
  }

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {
		$this->isLoggedIn();
		$id = $this->input->post('id');
		$data = array(0,$id);

		$this->Overtimepays_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		generate_json($data);

	}

	public function reject(){
    $this->isLoggedIn();

    $reject_id = en_dec('dec',$this->input->post('reject_id'));
    $reject_reason = $this->input->post('reject_reason');

    if(empty($reject_id) || empty($reject_reason)){
      $data = array("success" => 0, "message" => "Please fill up all required fields and try again.");
      generate_json($data);
      exit();
    }

    $reject_data = array(
      "rejected_by" => $this->session->emp_idno,
      "reject_reason" => $reject_reason,
      "status" => "rejected",
      "date_updated" => todaytime()
    );

    $rejected = $this->Overtimepays_model->reject($reject_data,$reject_id);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Unable to reject overtime pays .Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Overtime pays rejected successfully");
    generate_json($data);

  }

	public function check_available_ot(){
		$employee_id_no = $this->input->post('employee_id_no');
		$date_rendered = $this->input->post('date_rendered');

		if(empty($employee_id_no) || empty($date_rendered)){
			$data = array("success" => 0, "message" => "No available minutes of overtime.");
			generate_json($data);
			exit();
		}

		$timelog = $this->Overtimepays_model->get_timelog_history($employee_id_no,$date_rendered);
		if($timelog->num_rows() == 0){
			$data = array("success" => 0, "message" => "No available minutes of overtime");
			generate_json($data);
			exit();
		}

		$worksched = $this->Overtimepays_model->get_worksched($employee_id_no);
		if($worksched->num_rows() == 0){
			$data = array("success" => 0, "message" => "Unable to get any work schedule for this employee.");
			generate_json($data);
			exit();
		}

		$worksched = $worksched->row_array();
		$timelog = $timelog->result_array();
		$count = count((array)$timelog) - 1;
		$ws = (array)json_decode($worksched['work_sched']);
		$days = array('mon','tue','wed','thu','fri','sat','sun');
		$date = new DateTime($date_rendered);
		$day = strtolower($date->format('D'));
		$return_data = 0;

		// print_r($ws);
		// die();

		for ($i=0; $i < 7; $i++) {
			if($day == $days[$i]){
				if($ws[$days[$i]][0] != ""){
					$timelog_data = array(
						"employee_idno" => $employee_id_no,
						"total_whours" => $worksched['total_whours'],
						"total_bhours" => $worksched['total_bhours'],
						"sched_type" => $worksched['sched_type'],
						"stime_in" => $ws[$days[$i]][0],
						"stime_out" => $ws[$days[$i]][1],
						"sbreak_in" => $ws[$days[$i]][3],
						"sbreak_out" => $ws[$days[$i]][4],
						"timelog" => $timelog,
						"first_in" => $timelog[0]['time_in'],
						"last_out" => $timelog[$count]['time_out']
					);

					$return_data = compute_timelog($timelog_data,'overtime');
				}
			}
		}

		$data = array("success" => 1, "available_ot" => $return_data);
		generate_json($data);
	}
}
