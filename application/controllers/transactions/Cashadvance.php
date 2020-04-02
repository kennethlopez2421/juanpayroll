<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Cashadvance extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('transactions/cashadvance_model');
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
		$this->load->view('transactions/cashadvance', $data);

	}

	public function getcapays_waiting_json(){
    $search = $this->input->post('searchValue');
    $data = $this->cashadvance_model->getCaPays_waiting_json($search);
    echo json_encode($data);
  }

	public function getcapays_approved_json(){
    $search = $this->input->post('searchValue');
    $data = $this->cashadvance_model->getCaPays_approved_json($search);
    echo json_encode($data);
  }

	public function getcapays_certified_json(){
    $search = $this->input->post('searchValue');
    $data = $this->cashadvance_model->getCaPays_certified_json($search);
    echo json_encode($data);
  }

	public function updatecastatus(){
		$ca_id = $this->input->post('ca_id');
		$status = $this->input->post('status');
		$update = $this->input->post('update');

		if(empty($ca_id) || empty($status)){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again");
			generate_json($data);
			exit();
		}

		$emp_id = $this->session->userdata('emp_idno');
		$update_data = array($status,$emp_id,$ca_id);
		$updated = $this->cashadvance_model->updateCaStatus($update_data, $update);
		if($updated == false){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Successfully ".ucfirst($status));
		generate_json($data);
	}

	public function get_emp_contract_info(){
		$emp_id = $this->input->post('emp_id');

		if($emp_id == ""){
			$data = array("success" => 0, "message" => "Unable to get any information about this employee");
			generate_json($data);
			exit();
		}

		$ec_info = $this->cashadvance_model->get_employee($emp_id);
		if($ec_info->num_rows() == 0){
			$data = array("success" => 0, "message" => "Unable to get any information about this employee");
			generate_json($data);
			exit();
		}

		$pscheme = $this->cashadvance_model->getPaymentScheme();
		if($pscheme->num_rows() == 0){
			$data = array("success" => 0, "No available payment scheme. Please set it up first");
			generate_json($data);
			exit();
		}

		$basic_pay = $this->cashadvance_model->get_basic_pay()->row()->salarycatid;
		$contract = $ec_info->row();
		$pscheme = $pscheme->row();
		$salary = 0;
		foreach(json_decode($contract->sal_cat) as $sal){
			$salary += $sal->amount;
		}

		$max_loan = (float)$salary * ((float)$pscheme->maximum_loan / 100);
		$monthly_rate = $max_loan * (((float)$pscheme->monthly_rate / 100) * $pscheme->term_of_payment);
		$term = $pscheme->term_of_payment;
		$total = $max_loan - $monthly_rate;
		$data = array(
			"success" => 1,
			"max_per" => $pscheme->maximum_loan,
			"rate_per" => $pscheme->monthly_rate,
			"term_per" => $pscheme->term_of_payment,
			"max_loan" => $max_loan,
			"monthly_rate" => $monthly_rate,
			"term" => $term,
			"total" => $total
		);
		generate_json($data);
	}

	public function create(){
		$emp_id = $this->input->post('emp_id');
		$ca_dof = $this->input->post('ca_dof');
		$ca_doe = $this->input->post('ca_doe');
		$ca_reason = $this->input->post('ca_reason');
		$ca_max_loan = $this->input->post('ca_max_loan');
		$ca_num_days = $this->input->post('ca_num_days');
		$ca_monthly_rate = $this->input->post('ca_monthly_rate');
		$ca_total = $this->input->post('ca_total');
		$created_by = $this->session->userdata('emp_idno');
		// echo $ca_total;
		// die();

		if(empty($emp_id) || empty($ca_dof) || empty($ca_doe) || empty($ca_reason) || empty($ca_num_days)
		|| empty($ca_monthly_rate) || empty($ca_total) || empty($ca_max_loan)){
			$data = array("success" => 0, "message" => "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		$date_end = date('Y-m-d', strtotime('+'.$ca_num_days.' months', strtotime($ca_doe)));

		$insert_data = array(
			"employee_id" => $emp_id,
			"date_of_file" => $ca_dof,
			"amount" => $ca_total,
			"total_amount" => $ca_max_loan,
			"total_balance" => $ca_max_loan,
			"reason" => $ca_reason,
			"date_of_effectivity" => $ca_doe,
			"date_end" => $date_end,
			"terms" => $ca_num_days,
			"rate" => $ca_monthly_rate,
			"created_by" => $created_by
		);

		$inserted = $this->cashadvance_model->set_cadvance($insert_data);
		if($inserted == false){
			$data = array("success" => 0, "message" => "Unable to add Cash Advance. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Cash Advance Added Successfully");
		generate_json($data);
	}

	//frontend
	public function add($token = "") {
		// $getemployee = $this->cashadvance_model->get_employee_w_active_contract();
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)? $this->model->getDepartment($user_dept): $this->model->getDepartment();
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department,
		);

		$this->load->view('includes/header_addedit', $data);
		$this->load->view('transactions/cashadvance_add', $data);
	}

	public function edit($token = "", $id) {
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)? $this->model->getDepartment($user_dept): $this->model->getDepartment();
		$result = $this->cashadvance_model->getCAByID($id)->row();
		$getemployee = $this->model->get_emp_by_dept($result->deptId);
		// $getemployee = $this->cashadvance_model->get_employee_w_active_contract();
		$pscheme = $this->cashadvance_model->getPaymentScheme()->row();
		$caID = $id;
		$data = array(
			'token' => $token,
			'result' => $result,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department,
			'employee' => $getemployee,
			'per_rate' => $pscheme->monthly_rate,
			'per_loan' => $pscheme->maximum_loan,
			'per_term' => $pscheme->term_of_payment
		);

		$this->load->view('includes/header_addedit', $data);
		$this->load->view('transactions/cashadvance_edit', $data);
	}

	public function add_cashadvance(){
		$employee_id_no = $this->input->post('employee_id_no');
		$date_of_file = $this->input->post('date_of_file');
		$date_of_effectivity = $this->input->post('date_of_effectivity');
		$amount = $this->input->post('amount');
		$reason = $this->input->post('reason');
		$terms = $this->input->post('terms');
		$rate = $this->input->post('rate');

		$data = array($employee_id_no,$date_of_file,$date_of_effectivity,$amount,$reason,$terms,$rate);
		if(empty($employee_id_no) || empty($date_of_file) || empty($date_of_effectivity) || empty($amount) || empty($reason) || empty($terms) ||empty($rate)){
			$data = array('succeess' => 0, 'message' => "Please input Cash advance details");
		}
		else{
			$this->cashadvance_model->create($data);
			$data = array('success' => 1, 'message' => "Data successfully inserted");
		}
		echo json_encode($data);
	}

	public function update_cashadvance(){
		$caID = $this->input->post('caID');
		$employee_id_no = $this->input->post('employee_id_no');
		$date_of_file = $this->input->post('date_of_file');
		$date_of_effectivity = $this->input->post('date_of_effectivity');
		$amount = $this->input->post('amount');
		$reason = $this->input->post('reason');
		$terms = $this->input->post('terms');
		$rate = $this->input->post('rate');

		// print_r(array($employee_id_no,$date_of_file,$date_of_effectivity,$amount,$reason,$terms,$rate,$caID));
		// die();
		$data = array(
			$employee_id_no,
			$date_of_file,
			$date_of_effectivity,
			$amount,$reason,
			$terms,
			$rate,
			$caID
		);
		if(empty($employee_id_no) || empty($date_of_file) || empty($date_of_effectivity) || empty($amount) || empty($reason) || empty($terms) ||empty($rate) || empty($caID)){
			$data = array('success' => 0, 'message' => "Please input cash advance details");
		}else{
			$updated = $this->cashadvance_model->update($data);
			if($updated == false){
				$data = array("success" => 0, "message" => "Unable to update Cash Advance. Please try again");
			}else{
				$data = array('success' => 1, 'message' => "Data successfully inserted");
			}
		}
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
    $updated = $this->cashadvance_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." cash advance. Please try again.");
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
		$data = array(0,$id);

		$this->cashadvance_model->destroy($data);

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

    $rejected = $this->cashadvance_model->reject($reject_data,$reject_id);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Unable to reject cash advance .Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Cash advance rejected successfully");
    generate_json($data);

  }

}
