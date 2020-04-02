<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transaction_email extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->isLoggedIn();
    $this->load->library('email');
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

  public function partialResponse()
  {
      $response=array();
      ignore_user_abort(true);
      ob_start();
      echo json_encode($response);
      header("Status: 200");
      header($_SERVER["SERVER_PROTOCOL"] . " 200 Ok");
      header("Content-Type: application/json");
      header('Content-Length: '.ob_get_length());
      ob_end_flush();
      ob_flush();
      flush();
  }

  public function email_pump(){
    ini_set('max_execution_time', '0');

    $edata = json_decode(en_dec('dec',$this->input->post('edata')));
    $dept_id = $edata->email_settings->dept_id;
    $nav_id = $edata->email_settings->nav_id;
    $employee = $this->model->get_fullname_by_id($edata->employee_idno)->row()->fullname;
    $type = $edata->email_settings->type;
    $date = $edata->date;
    $cn_name = $edata->cn_name;
    // $created_by = $this->model->get_fullname_by_id($edata->created_by)->row()->fullname;

    $email = $this->model->get_transaction_email($nav_id,$dept_id);
    if($email->num_rows() == 0){
      $data = array("success" => 1);
      generate_json($data);
      exit();
    }

    $receiver = ($edata->email_settings->type == "approver") ? $email->row()->approver_email : $email->row()->certifier_email;
    // $receivers = explode(',',$receiver);
    // foreach ($receivers as $row) {
      // $this->partialResponse();
      // $recipient = $this->model->get_fullname_by_id($row)->row();
      // $data['recipient'] = $recipient->fullname;
      $data['employee'] = $employee;
      $data['date'] = $date;
      $data['cn_name'] = $cn_name;
      // $data['creator'] = $created_by;

  		$msg = $this->load->view('emails/'.$type.'_email',$data,true);

  		$this->email->from('support@cloudpanda.ph', 'Juan Payroll '.$cn_name.' Email');
  		$this->email->to($receiver);

  		$this->email->subject($cn_name);
  		$this->email->message($msg);
  		$email = $this->email->send();
    // }

    $data = array("success" => 1, "message" => $edata);
    generate_json($data);

  }

  public function index($token = "",$email = false){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      "email_data" => $email
    );

    // $this->load->view('includes/header',$data);
    $this->load->view('emails/email_pump',$data);
  }
}
