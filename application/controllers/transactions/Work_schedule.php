<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Work_schedule extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('transactions/work_schedule_model');
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

  public function get_work_schedule_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->work_schedule_model->get_work_schedule_json($search);
    echo json_encode($data);
  }

  public function get_approved_work_schedule_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->work_schedule_model->get_approved_work_schedule_json($search);
    echo json_encode($data);
  }

  public function get_certified_work_schedule_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->work_schedule_model->get_certified_work_schedule_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $department
    );

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/work_schedule',$data);
  }

  public function get_employee_by_dept(){
    $dept_id = $this->input->post('dept_id');

    if(empty($dept_id)){
      $data = array("success" => 0, "message" => "Invalid department . Please try again.");
      generate_json($data);
      exit();
    }

    $emps = $this->work_schedule_model->get_employee_by_dept($dept_id);
    if($emps->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to fetch any employee for this department.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "emps" => $emps->result());
    generate_json($data);
  }

  public function create(){
    $this->isLoggedIn();

    $department = $this->input->post('department');
    $employee = $this->input->post('employee');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    $type = (empty($employee)) ? "department" : "employee";
    $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
    $work_sched = array('mon' => array(), 'tue' => array(), 'wed' => array(), 'thu' => array(), 'fri' => array(), 'sat' => array(), 'sun' => array());
    $total_whours = 0;
    $total_bhours = 0;
    $total_day = 0;

    if(empty($department) || empty($start_date) || empty($end_date)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    if($this->session->login_type != 'admin' && empty($employee)){
      $data = array("success" => 0, "message" => "Invalid employee ID. Please try again.");
      generate_json($data);
      exit();
    }

    for ($i=0; $i < 7; $i++) {
      $ti = $this->input->post($days[$i]."_ti");
      $to = $this->input->post($days[$i]."_to");
      $bi = $this->input->post($days[$i]."_bi");
      $bo = $this->input->post($days[$i]."_bo");
      $total = $this->input->post($days[$i]."_total");
      $total_whours += (int)$total;
      $total_bhours += (mins($bi) > mins($bo)) ? ((mins($bo) + 2440) - mins($bi)) / 60 : (mins($bo) - mins($bi)) / 60;
      $total_day += ($total != '') ? 1 : 0;

      array_push($work_sched[$days[$i]],$ti, $to, $total, $bi, $bo);
    }

    // die($total_whours." ".$total_bhours." ".$total_day);

    if($total_whours < 32){
      $data = array("success" => 0, "message" => "Work schedule should be atleast 32 hours.");
      generate_json($data);
      exit();
    }

    $isExist = ($type == "department")
    ? $this->work_schedule_model->get_schedule_overlap($start_date,$end_date,$department)
    : $this->work_schedule_model->get_schedule_overlap($start_date,$end_date,$department,$employee);

    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Custom schedule already exist for this deparment/employee.");
      generate_json($data);
      exit();
    }

    $total_whours_per_day = $total_whours / $total_day;
    $total_bhours_per_day = $total_bhours / $total_day;
    $insert_data = array(
      "department_id" => $department,
      "date_from" => $start_date,
      "date_to" => $end_date,
      "work_sched" => json_encode($work_sched),
      "total_whours" => $total_whours_per_day,
      "total_bhours" => $total_bhours_per_day,
      "type" => $type,
      "created_by" => $this->session->emp_idno,
      "updated_at" => todaytime()
    );

    if($type == "employee"){
      $insert_data['employee_idno'] = $employee;
    }

    $inserted = $this->work_schedule_model->set_custom_workschedule($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save custom work schedule. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Custom work schedule saved successfully.");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec',$this->input->post('uid'));
    $department = $this->input->post('edit_department');
    $employee = $this->input->post('edit_employee');
    $start_date = $this->input->post('edit_start_date');
    $end_date = $this->input->post('edit_end_date');
    $type = (empty($employee)) ? "department" : "employee";
    $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
    $work_sched = array('mon' => array(), 'tue' => array(), 'wed' => array(), 'thu' => array(), 'fri' => array(), 'sat' => array(), 'sun' => array());
    $total_whours = 0;
    $total_bhours = 0;
    $total_day = 0;

    if(empty($department) || empty($start_date) || empty($end_date)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    if($this->session->login_type != 'admin' && empty($employee)){
      $data = array("success" => 0, "message" => "Invalid employee ID. Please try again.");
      generate_json($data);
      exit();
    }

    for ($i=0; $i < 7; $i++) {
      $ti = $this->input->post("edit_".$days[$i]."_ti");
      $to = $this->input->post("edit_".$days[$i]."_to");
      $bi = $this->input->post("edit_".$days[$i]."_bi");
      $bo = $this->input->post("edit_".$days[$i]."_bo");
      $total = $this->input->post("edit_".$days[$i]."_total");
      $total_whours += (int)$total;
      $total_bhours += (mins($bi) > mins($bo)) ? ((mins($bo) + 2440) - mins($bi)) / 60 : (mins($bo) - mins($bi)) / 60;
      $total_day += ($total != '') ? 1 : 0;

      array_push($work_sched[$days[$i]],$ti, $to, $total, $bi, $bo);
    }

    if($total_whours < 32){
      $data = array("success" => 0, "message" => "Work schedule should be atleast 32 hours.");
      generate_json($data);
      exit();
    }

    $isExist = ($type == "department")
    ? $this->work_schedule_model->get_schedule_overlap($start_date,$end_date,$department,false,$uid)
    : $this->work_schedule_model->get_schedule_overlap($start_date,$end_date,$department,$employee,$uid);

    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Custom schedule already exist for this deparment/employee.");
      generate_json($data);
      exit();
    }

    $total_whours_per_day = $total_whours / $total_day;
    $total_bhours_per_day = $total_bhours / $total_day;
    $update_data = array(
      "department_id" => $department,
      "date_from" => $start_date,
      "date_to" => $end_date,
      "work_sched" => json_encode($work_sched),
      "total_whours" => $total_whours_per_day,
      "total_bhours" => $total_bhours_per_day,
      "type" => $type
    );

    if($type == "employee"){
      $update_data['employee_idno'] = $employee;
    }

    $updated = $this->work_schedule_model->update_custom_workschedule($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update work schedule. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Custom work schedule updated successfully");
    generate_json($data);
  }

  public function delete(){
    $this->isLoggedIn();
    $delid = en_dec('dec',$this->input->post('delid'));

    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to delete custom work schedule. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $deleted = $this->work_schedule_model->update_ws_status($delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete custom workschedule. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Work schedule successfully deleted.");
    generate_json($data);
  }

  public function reject(){
    $reject_reason = $this->input->post('reject_reason');
    $reject_id = en_dec('dec',$this->input->post('reject_id'));

    if(empty($reject_reason) || empty($reject_id)){
      $data = array("success" => "Please fill up all required fields. ");
      generate_json($data);
      exit();
    }

    $reject_data = array(
      "reject_reason" => $reject_reason,
      "rejected_by" => $this->session->emp_idno,
      "updated_at" => todaytime(),
      "status" => "rejected"
    );

    $rejected = $this->work_schedule_model->update_ws_status2($reject_data,$reject_id);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Unable to reject work schedule");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Work Schedule rejected successfully");
    generate_json($data);
    exit();
  }

  public function update_status(){
    $this->isLoggedIn();

    $status = $this->input->post('status');
    $ws_id = en_dec('dec', $this->input->post('ws_id'));

    // die($status." ".$ws_id);

    if(empty($status) || empty($ws_id)){
      $data = array("success" => 0, "message" => "Unable to ".$status." work schedule. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $update_data = array("status" => $status, "updated_at" => todaytime());
    ($status == 'approve')
    ? $update_data['approved_by'] = $this->session->emp_idno
    : $update_data['certified_by'] = $this->session->emp_idno;

    $updated = $this->work_schedule_model->update_ws_status2($update_data,$ws_id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".$status." work schedule. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Work schedule ".$status." successfully");
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
    $updated = $this->work_schedule_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." work schedule. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Batch ".ucfirst($batch_status)." Successfull");
    generate_json($data);
  }
}
