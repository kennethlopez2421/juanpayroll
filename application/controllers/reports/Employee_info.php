<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee_info extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/employee_info_model');
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
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
      }
    }
  }

  public function get_employee_info_json(){
    $search = json_decode($this->input->post('searchValue'));
    $status = json_decode($this->input->post('status'));
    $data = $this->employee_info_model->get_employee_info_json($search,$status);
    echo json_encode($data);
  }

  public function get_emp_info(){
    $stype = $this->input->post('stype');
    $emp_idno = $this->input->post('emp_idno');

    if(empty($stype) || empty($emp_idno)){
      $data = array("success" => 0, "message" => "Something went wrong please try again.");
      generate_json($data);
      exit();
    }

    switch ($stype) {
      case 'emp_record':
        break;
      case 'emp_educ':
        $education = $this->employee_info_model->get_employee_education($emp_idno);
        if($education->num_rows() == 0){
          $data = array("success" => 1, "record" => array());
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "record" => $education->result_array());
        generate_json($data);
        break;
      case 'emp_work':
        $workhistory = $this->employee_info_model->get_employee_workhistory($emp_idno);
        if($workhistory->num_rows() == 0){
          $data = array("success" => 1, "record" => array());
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "record" => $workhistory->result_array());
        generate_json($data);
        break;
      case 'emp_depend':
        $dependents = $this->employee_info_model->get_employee_dependents($emp_idno);
        if($dependents->num_rows() == 0){
          $data = array("success" => 1, "record" => array());
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "record" => $dependents->result_array());
        generate_json($data);
        break;
      default:
        // code...
        break;
    }
  }

  public function get_contract(){
    $cid = en_dec('dec',$this->input->post('cid'));

    if(empty($cid)){
      $data = array("success" => 0, "message" => "Something went wrong. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $contract = $this->employee_info_model->get_contract($cid);
    if($contract->num_rows() == 0){
      $data = array("success" => 1, 'record' => array());
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "record" => $contract->result_array());
    generate_json($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'department' => $this->model->getDepartment(),
      'positions' => $this->model->get_position_by_id()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('reports/employee_info',$data);
  }
}
