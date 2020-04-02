<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Additionalpays extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('transactions/Additionalpays_model');
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
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/additionalpays', $data);

	}

	//get data for data table
	public function getadditionalpays_waiting_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Additionalpays_model->getAdditionalPays_waiting_json($search);
    echo json_encode($data);
  }

	public function getadditionalpays_approved_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Additionalpays_model->getAdditionalPays_approved_json($search);
    echo json_encode($data);
  }

	public function getadditionalpays_certified_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Additionalpays_model->getAdditionalPays_certified_json($search);
    echo json_encode($data);
  }

	public function updateadditionalpays_status(){
		$this->isLoggedIn();

		$ap_id = $this->input->post('ap_id');
		$status = $this->input->post('status');
		$update = $this->input->post('update');

		if(empty($ap_id) || empty($status) || empty($update)){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again".$ap_id.$status);
			generate_json($data);
			exit();
		}

		$emp_id = $this->session->userdata('emp_idno');
		$update_data = array($status,$emp_id,$ap_id);
		$updated = $this->Additionalpays_model->updateStatus($update_data,$update);
		if($updated == false){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again".$ap_id.$status);
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Successfully ".ucfirst($status));
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
    $updated = $this->Additionalpays_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." additional pays. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Batch ".ucfirst($batch_status)." Successfull");
    generate_json($data);
  }

	//frontend
	public function add($token = "") {
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department
		);

		$this->load->view('includes/header_addedit', $data);
		$this->load->view('transactions/additionalpays_add', $data);
	}

	public function edit($token = "", $id) {
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
		$result = $this->Additionalpays_model->getCAByID($id)->row();
		$getemployee = $this->model->get_emp_by_dept($result->deptId);
		$caID = $id;
		$data = array(
			'token' => $token,
			'result' => $result,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department,
			'employee' => $getemployee
		);

		$this->load->view('includes/header_addedit', $data);
		$this->load->view('transactions/additionalpays_edit', $data);
	}

	public function add_pays(){
		$employee_id_no = $this->input->post('employee_id_no');
		$date_issued = $this->input->post('date_issued');
		$purpose = $this->input->post('purpose');
		$amount = $this->input->post('amount');
		$created_by = $this->session->userdata('emp_idno');

		if($employee_id_no == "" || $date_issued == "" || $purpose == "" || $amount == "" || $created_by == ""){
			$data = array("success" => 0, "message" => "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		$add_data = array(
			"employee_id" => $employee_id_no,
			"date_issued" => $date_issued,
			"purpose" => $purpose,
			"amount" => $amount,
			"created_by" => $created_by,
			"status" => "waiting"
		);
		$inserted = $this->Additionalpays_model->create($add_data);
		if($inserted == false){
			$data = array("success" => 0, "message" => "Unable to save additional pay. Please try again");
			generate_json($data);
			exit();
 		}

		$data = array("success" => 1, "message" => "Added Successfully");
		generate_json($data);
	}

	public function update_pays(){
		$caID = $this->input->post('caID');
		$employee_id_no = $this->input->post('employee_id_no');
		$date_issued = $this->input->post('date_issued');
		$purpose = $this->input->post('purpose');
		$amount = $this->input->post('amount');

		// print_r(array($employee_id_no,$date_of_file,$date_of_effectivity,$amount,$reason,$terms,$rate,$caID));
		// die();
		$data = array($employee_id_no,$date_issued, $purpose, $amount,$caID);
		if(empty($caID) || empty($employee_id_no) || empty($date_issued) || empty($purpose) || empty($amount)){
			$data = array('success' => 0, 'message' => "Please input additional pays details");
		}else{
			$this->Additionalpays_model->update($data);
			$data = array('success' => 1, 'message' => "Data successfully inserted");
		}
		echo json_encode($data);
	}

	//backend
	public function destroy() {
		$id = $this->input->post('del_id');

		$data = array(0,$id);
		$deleted = $this->Additionalpays_model->destroy($data);
		// if($deleted == false){
		// 	$data = array("success" => 0, "message" => "Unable to delete this Additional Pay. Please try again");
		// 	generate_json($data);
		// 	exit();
		// }

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

    $rejected = $this->Additionalpays_model->reject($reject_data,$reject_id);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Unable to reject additional pays .Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Additional Pays rejected successfully");
    generate_json($data);

  }

}
