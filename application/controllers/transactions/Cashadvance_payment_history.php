<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cashadvance_payment_history extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('transactions/cashadvance_payment_history_model');
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

  public function get_cashadvance_payment_history_json(){
    $search = $this->input->post('searchValue');
    $data = $this->cashadvance_payment_history_model->get_cashadvance_payment_history_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/cashadvance_payment_history',$data);
  }

  public function get_ca_payment_breakdown(){
    $this->isLoggedIn();

    $ca_id = $this->input->post('ca_id');
    if($ca_id == ""){
      $data = array('success' => 0, 'message' => "Unable to get any data about this cash advance.");
      generate_json($data);
      exit();
    }

    $ca_payment = $this->cashadvance_payment_history_model->get_ca_payment_breakdown($ca_id);
    if($ca_payment->num_rows() == 0){
      $data = array('success' => 0, 'message' => "Unable to get any data about this cash advance.");
      generate_json($data);
      exit();
    }
    $ca = $ca_payment->row();
    $data = array(
      "success" => 1,
      "ca" => $ca,
      "ca_total" => number_format($ca->total_amount,2),
      "ca_payment" => number_format($ca->ca_payment,2),
      "ca_balance" => number_format($ca->ca_balance,2)
    );
    generate_json($data);
  }
}
