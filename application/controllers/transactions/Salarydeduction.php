<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Salarydeduction extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('transactions/salarydeduction_model');
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
		$this->load->view('transactions/salarydeduction', $data);

	}

	public function getsdpays_waiting_json(){
    $search = $this->input->post('searchValue');
    $data = $this->salarydeduction_model->getSdPays_waiting_json($search);
    echo json_encode($data);
  }

	public function getsdpays_approved_json(){
    $search = $this->input->post('searchValue');
    $data = $this->salarydeduction_model->getSdPays_approved_json($search);
    echo json_encode($data);
  }

	public function getsdpays_certified_json(){
    $search = $this->input->post('searchValue');
    $data = $this->salarydeduction_model->getSdPays_certified_json($search);
    echo json_encode($data);
  }

	public function updatesdstatus(){
		$this->isLoggedIn();

		$sd_id = $this->input->post('sd_id');
		$status = $this->input->post('status');
		$update = $this->input->post('update');

		if(empty($sd_id) || empty($status)){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again");
			generate_json($data);
			exit();
		}

		$emp_id = $this->session->userdata('emp_idno');
		$update_data = array($status,$emp_id,$sd_id);
		$updated = $this->salarydeduction_model->updateSdStatus($update_data,$update);
		if($updated == false){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Successfully ".ucfirst($status));
		generate_json($data);
	}

	//frontend
	public function add($token = "") {
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
		$deductiontype = $this->salarydeduction_model->deductiondesc();
		// $getemployee = $this->salarydeduction_model->getEmpID();
		// $deductiontyperes = $deductiontype->result();
		// $getemployeeres = $getemployee->result();
		$data = array(
			'token' => $token,
			'dropdown' => $deductiontype,
			'department' => $department,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);
		$this->load->view('includes/header_addedit', $data);
		$this->load->view('transactions/salarydeduction_add', $data);
	}

	public function edit($token = "", $id) {
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
		$result = $this->salarydeduction_model->getCAByID($id)->row();
		// print_r($result);
		$getemployee = $this->model->get_emp_by_dept($result->deptId);
		$deduct = $this->salarydeduction_model->deductiondesc();
		// print_r($deduct->result());
		// print_r($getemployee);
		// $getemployee = $this->salarydeduction_model->getEmpID();
		// $getemployeeres = $getemployee->result();

		$data = array(
			'token' => $token,
			'result' => $result,
			'department' => $department,
			'dropdown' => $deduct,
			'employee' => $getemployee,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header_addedit', $data);
		$this->load->view('transactions/salarydeduction_edit', $data);
	}

	public function add_deduction(){
		$this->isLoggedIn();

		$employee_id_no = $this->input->post('employee_id_no');
		$deduction_category = $this->input->post('deduction_category');
		$amount = $this->input->post('amount');
		$created_by = $this->session->userdata('emp_idno');

		// $data = array($employee_id_no,$deduction_category,$amount);
		$sal_deduct_data = array(
			"employee_idno" => $employee_id_no,
			"deduct_category" => $deduction_category,
			"amount" => $amount,
			"status" => "waiting",
			"created_by" => $created_by
		);
		if(empty($employee_id_no) || empty($deduction_category) || empty($amount)){
			$data = array('succeess' => 0, 'message' => "Please input deduction details");
		}
		else{
			$inserted = $this->salarydeduction_model->create($sal_deduct_data);
			if($inserted == false){
				$data = array("success" => 0, "message" => "Unable to save Salary Deduction . Please try again");
			}else{
				$data = array('success' => 1, 'message' => "Data successfully inserted");
			}
		}
		// echo json_encode($data);
		generate_json($data);
	}

	public function update_deduction(){
		$this->isLoggedIn();

		$caID = $this->input->post('caID');
		$employee_id_no = $this->input->post('employee_id_no');
		$deduction_category = $this->input->post('deduction_category');
		$amount = $this->input->post('amount');

		// print_r(array($employee_id_no,$date_of_file,$date_of_effectivity,$amount,$reason,$terms,$rate,$caID));
		// die();
		$data = array($employee_id_no,$deduction_category,$amount,$caID);
		if(empty($employee_id_no) || empty($deduction_category) || empty($caID) || empty($amount)){
			$data = array('success' => 0, 'message' => "Please input deduction details");
		}else{
			$this->salarydeduction_model->update($data);
			$data = array('success' => 1, 'message' => "Data successfully inserted");
		}
		echo json_encode($data);
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
    $updated = $this->salarydeduction_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." salary deduction. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Batch ".ucfirst($batch_status)." Successfull");
    generate_json($data);
  }

	//backend
	public function destroy() {
		$this->isLoggedIn();
		$id = $this->input->post('id');
		// $try123 = 123;
		// print_r($try123. " " .$id);
		// die();

		$data = array(0,$id);
		// print_r($data);

		$this->salarydeduction_model->destroy($data);

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

    $rejected = $this->salarydeduction_model->reject($reject_data,$reject_id);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Unable to reject salary deduction .Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Salary Deduction rejected successfully");
    generate_json($data);

  }

}
