<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Incident_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/incident_reports_model');
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

  public function get_incident_reports_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->incident_reports_model->get_incident_reports_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
      'positions' => $this->model->get_user_position()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('reports/incident_reports',$data);
  }

  public function create(){
    $employee = $this->input->post('employee');
    $employee_idno = en_dec('dec',$this->input->post('employee_idno'));
    $reporting_dept_head_id = $this->input->post('reporting_dept_head_id');
    $position = $this->input->post('position');
    $department = $this->input->post('department');
    $date_reported = $this->input->post('date_reported');
    $reported_by = $this->input->post('reported_by');
    $reported_id = en_dec('dec',$this->input->post('reported_id'));
    $concerned_dept_head_id = $this->input->post('concerned_dept_head_id');
    $place_of_incidence = $this->input->post('place_of_incidence');
    $date_happened = $this->input->post('date_happened');
    $time_of_incidence = $this->input->post('time_of_incidence');
    $resulting_damage = $this->input->post('resulting_damage');
    $incident_brief = $this->input->post('incident_brief');

    $inserted_data = array(
      "employee_idno" => $employee_idno,
      "position_id" => $position,
      "dept_id" => $department,
      "date_reported" => $date_reported,
      "date_happened" => $date_happened,
      "time_of_incidence" => $time_of_incidence,
      "place_of_incidence" => $place_of_incidence,
      "resulting_damage" => $resulting_damage,
      "incident_brief" => $incident_brief,
      "reported_by" => $reported_id,
      "reporting_head_id" => $reporting_dept_head_id,
      "concerned_head_id" => $concerned_dept_head_id,
      "status" => 'inactive'
    );

    // print_r($inserted_data);
    // die();

    foreach($inserted_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fieldss.");
        generate_json($data);
        exit();
      }
    }

    $inserted = $this->incident_reports_model->set_incident_reports($inserted_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save incident reports. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Incident Reports Save Successfully");
    generate_json($data);
  }

  public function update(){
    $uid = en_dec('dec',$this->input->post('uid'));
    $edit_employee = $this->input->post('edit_employee');
    $edit_employee_idno = $this->input->post('edit_employee_idno');
    $edit_reporting_dept_head_id = $this->input->post('edit_reporting_dept_head_id');
    $edit_position = $this->input->post('edit_position');
    $edit_department = $this->input->post('edit_department');
    $edit_date_reported = $this->input->post('edit_date_reported');
    $edit_reported_by = $this->input->post('edit_reported_by');
    $edit_reported_id = $this->input->post('edit_reported_id');
    $edit_concerned_dept_head_id = $this->input->post('edit_concerned_dept_head_id');
    $edit_place_of_incidence = $this->input->post('edit_place_of_incidence');
    $edit_date_happened = $this->input->post('edit_date_happened');
    $edit_time_of_incidence = $this->input->post('edit_time_of_incidence');
    $edit_resulting_damage = $this->input->post('edit_resulting_damage');
    $edit_incident_brief = $this->input->post('edit_incident_brief');
    $update_data = array(
      "employee_idno" => $edit_employee_idno,
      "position_id" => $edit_position,
      "dept_id" => $edit_department,
      "date_reported" => $edit_date_reported,
      "date_happened" => $edit_date_happened,
      "time_of_incidence" => $edit_time_of_incidence,
      "place_of_incidence" => $edit_place_of_incidence,
      "resulting_damage" => $edit_resulting_damage,
      "incident_brief" => $edit_incident_brief,
      "reported_by" => $edit_reported_id,
      "reporting_head_id" => $edit_reporting_dept_head_id,
      "concerned_head_id" => $edit_concerned_dept_head_id
    );

    foreach($update_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }
    }

    $updated = $this->incident_reports_model->update_incident_reports($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update incident reports. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Incident Reports Updated Successfully");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec',$this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to delete incident reports. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $deleted = $this->incident_reports_model->update_incident_reports_status($delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete incident reports. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Incident Reports Deleted Successfully");
    generate_json($data);
  }

  public function approve(){
    $id = $this->input->post('id');
    $act = $this->input->post('act');
    $uid = en_dec('dec',$this->input->post('uid'));
    $fields = "";

    switch ($act) {
      case 'rd':
        $fields = 'reporting_dept_head';
        break;
      case 'cd':
        $fields = 'concerned_dept_head';
        break;
      case 'hr':
        $fields = 'hr_dept_head';
        break;
      case 'ac':
        $fields = 'account_dept_head';
        break;
      default:
        // code...
        break;
    }

    $updated = $this->incident_reports_model->update_incident_reports_approve($id,$fields,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to approve incident reports. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Incident Reports Approve Successfully");
    generate_json($data);
  }

  public function search_user(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    if($employee == ""){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item dropdown-emp disabled">No Result Found</a>');
      generate_json($data);
      exit();
    }

    $search = $this->model->search_user($employee);
    if($search->num_rows() == 0){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item dropdown-emp disabled" >No Result Found</a>');
      generate_json($data);
      exit();
    }

    if($search->num_rows() > 0){
      $html = "";
      foreach($search->result_array() as $row){
        $html .= '<a href="#" class="dropdown-item dropdown-emp" data-deptid = "'.$row['deptId'].'" data-emp_idno = "'.en_dec('en',$row['employee_idno']).'">'.$row['fullname'].'</a>';
      }

      $data = array("success" => 1, "message" => $html);
      generate_json($data);
    }
  }

  public function edit_search_user(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    if($employee == ""){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item edit_dropdown-emp disabled">No Result Found</a>');
      generate_json($data);
      exit();
    }

    $search = $this->model->search_user($employee);
    if($search->num_rows() == 0){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item edit_dropdown-emp disabled" >No Result Found</a>');
      generate_json($data);
      exit();
    }

    if($search->num_rows() > 0){
      $html = "";
      foreach($search->result_array() as $row){
        $html .= '<a href="#" class="dropdown-item edit_dropdown-emp" data-deptid = "'.$row['deptId'].'" data-emp_idno = "'.en_dec('en',$row['employee_idno']).'">'.$row['fullname'].'</a>';
      }

      $data = array("success" => 1, "message" => $html);
      generate_json($data);
    }
  }

  public function search_user2(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    if($employee == ""){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item dropdown-reported disabled">No Result Found</a>');
      generate_json($data);
      exit();
    }

    $search = $this->model->search_user($employee);
    if($search->num_rows() == 0){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item dropdown-reported disabled" >No Result Found</a>');
      generate_json($data);
      exit();
    }

    if($search->num_rows() > 0){
      $html = "";
      foreach($search->result_array() as $row){
        $html .= '<a href="#" class="dropdown-item dropdown-reported" data-deptid = "'.$row['deptId'].'" data-emp_idno = "'.en_dec('en',$row['employee_idno']).'">'.$row['fullname'].'</a>';
      }

      $data = array("success" => 1, "message" => $html);
      generate_json($data);
    }
  }

  public function edit_search_user2(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    if($employee == ""){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item edit_dropdown-reported disabled">No Result Found</a>');
      generate_json($data);
      exit();
    }

    $search = $this->model->search_user($employee);
    if($search->num_rows() == 0){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item edit_dropdown-reported disabled" >No Result Found</a>');
      generate_json($data);
      exit();
    }

    if($search->num_rows() > 0){
      $html = "";
      foreach($search->result_array() as $row){
        $html .= '<a href="#" class="dropdown-item edit_dropdown-reported" data-deptid = "'.$row['deptId'].'" data-emp_idno = "'.en_dec('en',$row['employee_idno']).'">'.$row['fullname'].'</a>';
      }

      $data = array("success" => 1, "message" => $html);
      generate_json($data);
    }
  }

  public function get_immediate_head(){
    $deptid = $this->input->post('deptid');
    // die($deptid);
    if(empty($deptid)){
      $data = array("success" => 0, "message" => "Unable to get immediate head1.");
      generate_json($data);
      exit();
    }

    $immediate_head = $this->model->get_immediate_head($deptid);
    if($immediate_head->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get immediate head2.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => $immediate_head->row());
    generate_json($data);

  }
}
