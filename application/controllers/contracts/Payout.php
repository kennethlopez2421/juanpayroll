<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payout extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('contracts/payout_model');
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

  public function get_payout_json(){
    $search = $this->input->post('searchValue');
    $data = $this->payout_model->get_payout_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'banks' => $this->payout_model->get_bank(),
      'payout_mediums' => $this->payout_model->get_payoutmedium()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('contracts/payout',$data);
  }

  public function save_payout_information(){
    $contract_id = $this->input->post('contract_id');
    $p_medium = $this->input->post('p_medium');
    $p_bank = $this->input->post('p_bank');
    $p_card_number = $this->input->post('p_card_number');
    $p_account_number = $this->input->post('p_account_number');

    if(empty($contract_id) || empty($p_medium) || empty($p_bank) || empty($p_card_number) || empty($p_account_number)){
      $data = array("success" => 0, "message" => "Please fill up all required fields".$p_bank);
      generate_json($data);
      exit();
    }

    $active = $this->payout_model->check_contract($contract_id)->num_rows();
    if($active == 0){
      $data = array("success" => 0, "messag" => "The contract of this employee is no longer active.");
      generate_json($data);
      exit();
    }

    $payout_data = array(
      "contract_id" => $contract_id,
      "payout_medium_id" => $p_medium,
      "bank_id" => $p_bank,
      "card_number" => $p_card_number,
      "account_number" => $p_account_number
    );

    $inserted = $this->payout_model->set_payout_information($payout_data);
    if($inserted == false){
      $data = array("success" => 0, "message" => "Unable to save payout information. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Payout successfully updated");
    generate_json($data);
  }

  public function update_payout_information(){
    $contract_id = $this->input->post('contract_id');
    $contract_pm_id = $this->input->post('contract_pm_id');
    $p_medium = $this->input->post('p_medium');
    $p_bank = $this->input->post('p_bank');
    $p_card_number = $this->input->post('p_card_number');
    $p_account_number = $this->input->post('p_account_number');

    if(
      empty($contract_id) ||
      empty($contract_pm_id) ||
      empty($p_medium) ||
      empty($p_bank) ||
      empty($p_card_number) ||
      empty($p_account_number)
    ){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $active = $this->payout_model->check_contract($contract_id)->num_rows();
    if($active == 0){
      $data = array("success" => 0, "messag" => "The contract of this employee is no longer active.");
      generate_json($data);
      exit();
    }

    ### check duplicate card number ###
    $duplicate = $this->payout_model->check_card_number($p_card_number,$contract_id,$contract_pm_id)->num_rows();
    if($duplicate > 0){
      $data = array("success" => 0, "message" => "Card number already exist");
      generate_json($data);
      exit();
    }

    ### check duplicate account number ###
    $duplicate2 = $this->payout_model->check_account_number($p_account_number,$contract_id,$contract_pm_id)->num_rows();
    if($duplicate2 > 0){
      $data = array("success" => 0, "message" => "Account number already exist");
      generate_json($data);
      exit();
    }

    $payout_data = array(
      $p_medium,
      $p_bank,
      $p_card_number,
      $p_account_number,
      todaytime(),
      $p_medium,
      $contract_id,
      $contract_pm_id
    );
    $this->payout_model->update_payout_information($payout_data);
    $data = array("success" => 1, "message" => "Payout information successfully updated.");
    generate_json($data);

  }
}
