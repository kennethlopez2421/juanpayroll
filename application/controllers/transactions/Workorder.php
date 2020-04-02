<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Workorder extends CI_Controller{
  public function __construct() {
    parent::__construct();
    $this->load->model('transactions/workorder_model');
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

  public function getworkoder_json(){
    $search = $this->input->post('searchValue');
    $data = $this->workorder_model->getWordOrder_json($search);
    echo json_encode($data);
  }

  public function getworkoder_for_approval_json(){
    $search = $this->input->post('searchValue');
    $data = $this->workorder_model->getWordOrder_for_approval_json($search);
    echo json_encode($data);
  }

  public function getwordorder_for_certification_json(){
    $search = $this->input->post('searchValue');
    $data = $this->workorder_model->getWordOrder_for_certification_json($search);
    echo json_encode($data);
  }

  public function get_workschedule(){
    $id = $this->input->post('id');
    $date = $this->input->post('date');
    if(empty($id) || empty($date)){
      $data = array("success" => 0, "message" => "Unable to get any work schedule. Please try again.");
      generate_json($data);
      exit();
    }

    $ws_data = $this->workorder_model->get_workschedule($id)->row_array();
    if(count((array)$ws_data) > 0){
      $d = new Datetime($date);
      $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
      $wday = strtolower($d->format('D'));
      $worksched = (array)json_decode($ws_data['work_sched']);
      $in = "";
      $out = "";

      for ($i=0; $i < 7; $i++) {
        if($wdays = $days[$i]){
          if($worksched[$days[$i]][0] != ""){
            $in = $worksched[$days[$i]][0];
            $out = $worksched[$days[$i]][1];
          }
        }
      }

      $data = array("success" => 1, "in" => $in, "out" => $out);
    }else{
      $data = array("success" => 0, "message" => "Unable to get any work schedule. Please try again.");
    }

    generate_json($data);
  }

  public function get_employee_by_dept(){
		$dept_id = $this->input->post('dept_id');
		if($dept_id == ""){
			$data = array("success" => 0, "message" => "Unable to find any employee");
			generate_json($data);
			exit();
		}

		$emp = $this->workorder_model->get_emp_by_dept($dept_id);
		if($emp->num_rows() == 0){
			$data = array("success" => 0, "message" => "No available employee under this department");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "emp" => $emp->result_array());
		generate_json($data);

	}

  public function index($token = ""){
    $user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'department' => $department
		);

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/workorder',$data);
  }

  public function add($token = ""){
    $user_dept = $this->session->userdata('deptId');
		$department = (($this->session->login_type != 'admin' || $user_dept != hr_id()) && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'department' => $department
		);

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/workorder_add',$data);
  }

  public function edit($token = ""){
    $user_dept = $this->session->userdata('deptId');
		$department = (($this->session->login_type != 'admin' || $user_dept != hr_id()) && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
    $wo_id = $this->input->post('wo_id');
    $emp_idno = $this->input->post('emp_idno');
    $work_order = $this->workorder_model->getWorkOrder($wo_id)->row_array();
    $getemployee = $this->workorder_model->get_emp_by_dept($work_order['deptId']);
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'workOrder' => $work_order,
      'itinerary' => $this->workorder_model->getItinerary($wo_id),
      'department' => $department,
      'employee' => $getemployee
		);

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/workorder_edit',$data);
  }

  public function create(){
    $emp_id = $this->input->post('emp_id');
    $date = date('Y-m-d', strtotime($this->input->post('date')));
    $startTime = $this->input->post('startTime');
    $endTime = $this->input->post('endTime');
    $itArray = $this->input->post('itinerary');
    $dept = $this->input->post('dept');
    $it_error = 0;
    $created_by = $this->session->userdata('emp_idno');

    if(empty($emp_id) || empty($date) || empty($startTime) || empty($endTime) || empty($itArray)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $work_data = array(
      "employee_id" => $emp_id,
      "date" => $date,
      "start_time" => $startTime,
      "end_time" => $endTime,
      "status" => 'waiting',
      "updated_at" => todaytime(),
      "created_by" => $created_by
    );

    $overlap = $this->workorder_model->check_time_overlap($emp_id,$date,$startTime,$endTime);
    if($overlap->num_rows() > 0){
      $data = array("success" => 0, "message" => "Oops! You already had a work order that overlaps to this one.");
      generate_json($data);
      exit();
    }

    // print_r($dept);
    // die();
    $inserted = $this->workorder_model->setWorkOrder($work_data);
    if($inserted == false){
      $data = array("success" => 0, "message" => "Failed to save work order. Please try again");
      generate_json($data);
      exit();
    }

    for ($i=1; $i < count($itArray); $i++) {
      $it_location = $itArray[$i][0];
      $it_contact_person = $itArray[$i][1];
      $it_contact_num = $itArray[$i][2];
      $it_purpose = $itArray[$i][3];
      $it_notes = $itArray[$i][4];

      if(empty($it_location) || empty($it_contact_person) || empty($it_contact_num) || empty($it_purpose) || empty($it_notes)){
        $it_error = 1;
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }

      if($it_error == 0){
        $it_data = array(
          "workorder_id" => $inserted,
          "location" => $it_location,
          "contact_person" => $it_contact_person,
          "contact_num" => $it_contact_num,
          "purpose" => $it_purpose,
          "notes" => $it_notes
        );
        $inserted2 = $this->workorder_model->setItinerary($it_data);
        if($inserted2 == false){
          $data = array("success" => 0, "message" => "Failed to insert itinerary. Please try again");
          generate_json($data);
          exit();
        }
      }
    }

    // FOR EMAIL
    $email_data = array(
      "cn_name" => $this->session->content_name,
      "employee_idno" => $emp_id,
      "date" => $date,
      "email_settings" => array("dept_id" => $dept, "nav_id" => $this->session->content_id, "type" => "approver")
    );
    $email_data = en_dec('en',json_encode($email_data));
    $email = $this->model->get_transaction_email($this->session->content_id,$dept);
    // $email = $this->model->get_transaction_email($this->session->content_id,$dept);
    // if($email->num_rows() > 0){
    //   ini_set('max_execution_time', '0');
    //   $this->load->library('email');
    //   $type = "approver";
    //   $receiver = ($type == "approver") ? $email->row()->approver : $email->row()->certifier;
    //   $receivers = explode(',',$receiver);
    //   $employee = $this->model->get_fullname_by_id($emp_id)->row()->fullname;
    //   $cn_name = $this->session->content_name;
    //   foreach ($receivers as $row) {
    //     $recipient = $this->model->get_fullname_by_id($row)->row();
    //     $data['recipient'] = $recipient->fullname;
    //     $data['employee'] = $employee;
    //     $data['date'] = $date;
    //     $data['cn_name'] = $this->session->content_name;
    //     // $data['creator'] = $created_by;
    //
    // 		$msg = $this->load->view('emails/'.$type.'_email',$data,true);
    //
    // 		$this->email->from('support@cloudpanda.ph', 'Juan Payroll '.$cn_name.' Email');
    // 		$this->email->to($recipient->email);
    //
    // 		$this->email->subject($cn_name);
    // 		$this->email->message($msg);
    // 		$email = $this->email->send();
    //   }
    // }

    $data = array(
      "success" => 1,
      "message" => "Work Order save Successfully",
      "email" => $email_data,
      "email_status" => ($email->num_rows() > 0) ? 1 : 0
     );
    generate_json($data);

    // foreach($itArray as )
  }

  public function update(){
    $wo_id = $this->input->post('wo_id');
    $date = date('Y-m-d', strtotime($this->input->post('date')));
    $sTime = $this->input->post('sTime');
    $eTime = $this->input->post('eTime');
    if($wo_id == "" || $date == "" || $sTime == "" || $eTime == ""){
      $data = array("success" => 0, "message" => "Please fill up all the required fields");
      generate_json($data);
      exit();
    }

    $work_order_data = array($date,$sTime,$eTime,$wo_id);

    $updated = $this->workorder_model->updateworkorder($work_order_data);
    if($updated == false){
      $data = array("success" => 0, "message" => "Updating work order failed. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Update Successfull");
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
      "updated_at" => todaytime()
    );

    $rejected = $this->workorder_model->reject($reject_data,$reject_id);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Unable to reject work order .Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Work order rejected successfully");
    generate_json($data);

  }

  public function updateit(){
    $updateid = $this->input->post('updateid');
    $location = $this->input->post('location');
    $contact_person = $this->input->post('contact_person');
    $contact_num = $this->input->post('contact_num');
    $purpose = $this->input->post('purpose');
    $notes = $this->input->post('notes');

    if(empty($updateid) || empty($location) || empty($contact_person) || empty($contact_num) || empty($purpose) || empty($notes)){
      $data = array("success" => 0, "message" => "Please fill up all required fields for itinerary");
      generate_json($data);
      exit();
    }

    $itData = array($location, $contact_person, $contact_num, $purpose, $notes, $updateid);
    $updated = $this->workorder_model->updateItinerary($itData);
    if($updated == false){
      $data = array("success" => 0, "message" => "Updating itinerary failed. Please try again");
    }else{
      $data = array("success" => 1, "message" => "Itinerary Successfully Updated!");
    }

    generate_json($data);

  }

  public function updateworkorder_status(){
    $wo_id = $this->input->post('wo_id');
    $status = $this->input->post('status');
    $update = $this->input->post('update');

    if($wo_id == "" || $status == ""){
      $data = array("success" => 0, "message" => ucfirst($status)." failed. Please try again");
      generate_json($data);
      exit();
    }

    $emp_id = $this->session->userdata('emp_idno');
    $update_data = array($status, $emp_id, $wo_id);
    $updated = $this->workorder_model->updateworkorder_status($update_data,$update);
    if($updated == false){
      $data = array("success" => 0, "message" => ucfirst($status)." failed. Please try again");
    }else{
      $data = array("success" => 1, "message" => ucfirst($status)." Successfull");
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
    $updated = $this->workorder_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." workorder. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Batch ".ucfirst($batch_status)." Successfull");
    generate_json($data);
  }

  public function destroy(){
    $delid = $this->input->post('delid');
    $del_data = array(0, $delid);
    $deleted = $this->workorder_model->endis_workOrder($del_data);
    if($deleted == false){
      $data = array("success" => 0, "message" => "Delete Failed. Please try again".$delid);
    }else{
      $data = array("success" => 1, "message" => "Delete Successfull");
    }
    generate_json($data);
  }

  public function destroy_it(){
    $del_id = $this->input->post('del_id');
    if(empty($del_id)){
      $data = array("success" => 0, "message" => "Nothing to delete");
      generate_json($data);
      exit();
    }

    $del_data = array(0, $del_id);
    $deleted = $this->workorder_model->endis_itinerary($del_data);
    if($deleted == false){
      $data = array("success" => 0, "message" => "Unable to delete this itinerary. Please try again");
    }else{
      $data = array("success" => 1, "message" => "Itinerary deleted successfully");
    }

    generate_json($data);
  }
}
