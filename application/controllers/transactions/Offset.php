<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Offset extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('transactions/offset_model');
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

  public function get_offset_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->offset_model->get_offset_json($search);
    echo json_encode($data);
  }

  public function get_offset_approved_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->offset_model->get_offset_approved_json($search);
    echo json_encode($data);
  }

  public function get_offset_certified_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->offset_model->get_offset_certified_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
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
    $this->load->view('transactions/offset',$data);
  }

  public function get_employee_by_dept(){
    $dept_id = $this->input->post('dept_id');

    if(empty($dept_id)){
      $data = array("success" => 0, "message" => "Invalid department . Please try again.");
      generate_json($data);
      exit();
    }

    $emps = $this->offset_model->get_employee_by_dept($dept_id);
    if($emps->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to fetch any employee for this department.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "emps" => $emps->result());
    generate_json($data);
  }

  public function get_offset_bal(){
    $emp_idno = $this->input->post('emp_idno');

    if(empty($emp_idno)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isExist = $this->offset_model->get_offset_bal($emp_idno);
    if($isExist->num_rows() == 0){
      $data = array("success" => 0, "message" => "Invalid Employee ID . Please try again.");
      generate_json($data);
      exit();
    }

    $offset_bal = $isExist->row()->offset_bal;
    if((int)$offset_bal == 0){
      $data = array("success" => 1, "message" => "Insufficient offset balance.", "offset_bal" => $offset_bal);
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "offset_bal" => $offset_bal);
    generate_json($data);
  }

  public function check_filed_offset(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    $offset_type = $this->input->post('offset_type');
    $date = $this->input->post('date_rendered');

    if(empty($offset_type) || empty($date) || empty($employee)){
      $data = array("success" => 0, "message" => "Please select Employee, OffsetType and Date".$offset_type." || ".$date." || ".$employee);
      generate_json($data);
      exit();
    }

    if($offset_type == "undertime" || $offset_type == "late"){
      $timelog = $this->offset_model->get_timelog_history($employee,$date);
      if($timelog->num_rows() == 0){
        $data = array("success" => 0, "message" => "Unable to check employee ".ucfirst($offset_type)." on ".$date);
        generate_json($data);
        exit();
      }

      $worksched = $this->offset_model->get_worksched($employee);
      if($worksched->num_rows() == 0){
        $data = array("success" => 0, "message" => "Unable to get any work schedule for this employee.");
        generate_json($data);
        exit();
      }

      $grace = $this->offset_model->get_graceperiod($offset_type);
      $grace_data = array(
        "late" => 0,
        "undertime" => 0,
        "overbreak" => 0,
        "offset_late" => 0,
        "offset_undertime" => 0,
        "offset_wholeday" => 0,
        "offset_halfday" => 0
      );
      if($grace->num_rows() > 0){
        $grace_data["'.$offset_type.'"] = $grace->row()->minutes;
      }

      $worksched = $worksched->row_array();
      $timelog = $timelog->result_array();
      $count = count((array)$timelog) - 1;
      $ws = (array)json_decode($worksched['work_sched']);
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $real_date = $date;
      $date = new DateTime($date);
      $day = strtolower($date->format('D'));
      $return_data = 0;

      for ($i=0; $i < 7; $i++) {
  			if($day == $days[$i]){
  				if($ws[$days[$i]][0] != ""){
  					$timelog_data = array(
  						"employee_idno" => $employee,
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

  					$return_data = compute_timelog($timelog_data,$offset_type,$grace_data);
  				}
  			}
  		}

      if($return_data == 0){
        $data = array("success" => 2, "message" => "You don't have any ".ucfirst($offset_type)." on ".$real_date, "offset_min" => $return_data);
        generate_json($data);
        exit();
      }

  		$data = array("success" => 1, "offset_min" => $return_data);
  		generate_json($data);

    }
  }

  public function update_status(){
    $this->isLoggedIn();

    $status = $this->input->post('status');
    $offset_id = en_dec('dec', $this->input->post('offset_id'));

    // die($status." ".$offset_id);

    if(empty($status) || empty($offset_id)){
      $data = array("success" => 0, "message" => "Unable to ".$status." Offset. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $update_data = array("status" => $status);
    ($status == 'approved')
    ? $update_data['approved_by'] = $this->session->emp_idno
    : $update_data['certified_by'] = $this->session->emp_idno;

    $updated = $this->offset_model->update_offset_status($update_data,$offset_id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".$status." offset. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Offset ".$status." successfully");
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
    $updated = $this->offset_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." offset. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Batch ".ucfirst($batch_status)." Successfull");
    generate_json($data);
  }

  public function create(){
    $this->isLoggedIn();

    $department = $this->input->post('department');
    $employee = $this->input->post('employee');
    $offset_type = $this->input->post('offset_type');
    $date_rendered = $this->input->post('date_rendered');
    $offset_bal = $this->input->post('offset_bal');

    if(empty($department) || empty($employee) || empty($offset_type) || empty($date_rendered) || empty($offset_bal)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $total_offset_bal = $this->offset_model->get_offset_bal($employee)->row()->offset_bal;
    if((int)$offset_bal > (int)$total_offset_bal){
      $data = array("success" => 0, "message" => "Insufficient offset balance. Please try again.");
      generate_json($data);
      exit();
    }

    $pending = $this->offset_model->get_all_pending_offset($employee);
    if($pending->num_rows() > 0){
      $pending_offset = $pending->row()->pending_offset;
      if(((int)$offset_bal + (int)$pending_offset) > $total_offset_bal){
        $data = array("success" => 0, "message" => "Insufficient offset balance. You still have some pending offset.");
        generate_json($data);
        exit();
      }
    }

    $filed_offset = $this->offset_model->check_filed_offset($date_rendered,$offset_type,$employee);
    if($filed_offset->num_rows() > 0){
      $data = array("success" => 0, "message" => "You already filed an offset on this date with same offset type");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "employee_idno" => $employee,
      "date_rendered" => $date_rendered,
      "offset_min" => $offset_bal,
      "offset_type" => $offset_type,
      "created_by" => $this->session->emp_idno,
      "updated_at" => todaytime()
    );

    $inserted = $this->offset_model->set_offset($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save offset. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Offset save successfully");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec', $this->input->post('uid'));
    $edit_department = $this->input->post('edit_department');
    $edit_employee = $this->input->post('edit_employee');
    $edit_offset_type = $this->input->post('edit_offset_type');
    $edit_date_rendered = $this->input->post('edit_date_rendered');
    $edit_offset_bal = $this->input->post('edit_offset_bal');

    if(empty($edit_department) || empty($edit_employee) || empty($edit_offset_type) || empty($edit_date_rendered) || empty($edit_offset_bal)){
      $data = array("success" => 0, "message" => "Please fill up all required fields. ");
      generate_json($data);
      exit();
    }

    $total_offset_bal = $this->offset_model->get_offset_bal($edit_employee)->row()->offset_bal;
    if((int)$edit_offset_bal > (int)$total_offset_bal){
      $data = array("success" => 0, "message" => "Insufficient offset balance. Please try again.");
      generate_json($data);
      exit();
    }

    $pending = $this->offset_model->get_all_pending_offset($edit_employee,$uid);
    if($pending->num_rows() > 0){
      $pending_offset = $pending->row()->pending_offset;
      if(((int)$edit_offset_bal + (int)$pending_offset) > $total_offset_bal){
        $data = array("success" => 0, "message" => "Insufficient offset balance. You still have some pending offset.");
        generate_json($data);
        exit();
      }
    }

    $filed_offset = $this->offset_model->check_filed_offset($edit_date_rendered,$edit_offset_type,$edit_employee,$uid);
    if($filed_offset->num_rows() > 0){
      $data = array("success" => 0, "message" => "You already filed an offset on this date with same offset type");
      generate_json($data);
      exit();
    }

    $update_data = array(
      "employee_idno" => $edit_employee,
      "date_rendered" => $edit_date_rendered,
      "offset_min" => $edit_offset_bal,
      "offset_type" => $edit_offset_type
    );
    $updated = $this->offset_model->update_offset($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update offset. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Offset updated successfully");
    generate_json($data);
  }

  public function reject(){
    $this->isLoggedIn();

    $delid = en_dec('dec', $this->input->post('delid'));
    $reject_reason = $this->input->post('reject_reason');

    if(empty($delid) || empty($reject_reason)){
      $data = array("success" => 0, "message" => "Please fill up all required fields. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $reject_data = array(
      "reject_reason" => $reject_reason,
      "rejected_by" => $this->session->emp_idno,
      "status" => "rejected"
    );
    $rejected = $this->offset_model->reject_offset($reject_data,$delid);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Failed to reject offset. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Offset rejected successfully");
    generate_json($data);
  }
}
