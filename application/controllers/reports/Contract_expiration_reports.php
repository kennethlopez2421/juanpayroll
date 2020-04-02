<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contract_expiration_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/contract_expiration_reports_model');
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

  public function get_contract_expiration_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->contract_expiration_reports_model->get_contract_expiration_json($search);
    echo json_encode($data);
  }

  public function get_users_to_send_evaluation(){
    $dept = $this->input->post('dept');
    $pos_lvl = $this->input->post('pos_lvl');

    if(empty($dept) || empty($pos_lvl)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $pos_lvl = $this->contract_expiration_reports_model->get_users_to_send_evaluation(array($dept,$pos_lvl));
    // die($pos_lvl)
    if($pos_lvl->num_rows() === 0){
      $data = array("success" => 0, "message" => "Unable to get any employee. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "pos_lvl" => $pos_lvl->result_array());
    generate_json($data);

  }

  public function get_admin_to_send_evaluation(){
    $admins = $this->contract_expiration_reports_model->get_admin_to_send_evaluation();
    if($admins->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get any users. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => $admins->result_array());
    generate_json($data);
  }

  public function get_position_lvl(){
    $dept = $this->input->post('dept');
    if($dept === ""){
      $data = array("success" => 0, "message" => "Invalid department. Please try again.");
      generate_json($data);
      exit();
    }

    $pos_lvl = $this->contract_expiration_reports_model->get_position_lvl();
  }

  public function send_evaluation(){
    $dept = $this->input->post('dept');
    $emp_idno = $this->input->post('emp_idno');
    $management_id = $this->input->post('management_id');
    $eval_date = $this->input->post('eval_date');
    $eval_from = $this->input->post('eval_from');
    $eval_to = $this->input->post('eval_to');

    if($dept === "" || empty($emp_idno) || empty($management_id) || empty($eval_date) || empty($eval_to) || empty($eval_from)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.".$dept);
      generate_json($data);
      exit();
    }

    $check_evaluation = $this->contract_expiration_reports_model->get_user_evaluation(array($emp_idno,$management_id,$eval_from,$eval_to));
    if($check_evaluation->num_rows() > 0){
      $data = array("success" => 0, "message" => "This user already have evaluation for this employee");
      generate_json($data);
      exit();
    }

    $ref_no = generate_player_no();
    while($this->contract_expiration_reports_model->get_evaluation_ref_no($ref_no)->num_rows() > 0){
      $ref_no = generate_player_no();
    }

    $insert_data = array(
      "ref_no" => $ref_no,
      "management_id" => $management_id,
      "employee_idno" => $emp_idno,
      "department_id" => $dept,
      "eval_type" => ($dept == 0) ? 'type_2' : 'type_1',
      "eval_date" => $eval_date,
      "eval_from" => $eval_from,
      "eval_to" => $eval_to
    );

    $inserted = $this->contract_expiration_reports_model->set_evaluation($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to send evaluation. Please try again.");
      generate_json($data);
      exit();
    }

    $evaluator = $this->contract_expiration_reports_model->get_evaluator_info($management_id);
    if($evaluator->num_rows() > 0){
      $eval = $evaluator->row_array();
      // print_r($eval);
      // print_r($eval['pos_level']);
      // die();
      $evaluatee = $this->contract_expiration_reports_model->get_evaluatee($emp_idno)->row()->fullname;
      if($eval['pos_level'] >= 2){
        $this->load->library('email');
        $email_data['username'] = $eval['email'];
        $email_data['fullname'] = $eval['fullname'];
        $email_data['evaluatee'] = $evaluatee;
        $email_data['code'] = en_dec('dec',$this->session->bcode);
        $email_data['from'] = $eval_from;
        $email_data['to'] = $eval_to;
        $msg = $this->load->view('emails/evaluation_email',$email_data,true);

        $this->email->from('support@cloudpanda.ph', 'One Payroll');
        $this->email->to($eval['email']);

        $this->email->subject('One Payroll Evaluation');
        $this->email->message($msg);
        $email = $this->email->send();
      }
    }

    $data = array("success" => 1, "message" => "Evaluation Sent Successfully");
    generate_json($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
      'positions' => $this->model->get_user_position(),
      'position_lvl' => $this->contract_expiration_reports_model->get_position_w_access_to_evaluate() // Manager or above position
    );

    // print_r($data['position_lvl']);
    // die();

    $this->load->view('includes/header',$data);
    $this->load->view('reports/contract_expiration_reports',$data);
  }
}
