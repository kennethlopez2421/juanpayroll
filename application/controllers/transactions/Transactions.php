<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transactions extends CI_Controller{
  public function __construct() {
    parent::__construct();
    $this->load->model('time_record/timelogreports_model');
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'timeLogTable' => ""
    );

    $this->load->view('includes/header', $data);
    $this->load->view('transactions/tran_cashadvance', $data);
  }

  public function timeLogReports_json(){
    $data = $this->timelogreports_model->getTimeLogReports_data();
    echo json_encode($data);
  }

  public function timeLogReports_dateRange($date){
    $date = explode('---', $date);
    $date_from = $date[0];
    $date2_to = $date[1];
    // print_r($date);
    // die();

    $data = $this->timelogreports_model->getTimeLogReports_dateRange($date_from, $date2_to);
    echo json_encode($data);
    // generate_json($data);
  }

}
