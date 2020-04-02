<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Exchange_rates extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/exchange_rates_model');

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

  public function get_exchange_rates_json(){
    $search = $this->input->post('searchValue');
    $data = $this->exchange_rates_model->get_exchange_rates_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/exchange_rates',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $currency_name = $this->input->post('currency_name');
    $currency_code = $this->input->post('currency_code');
    $exchange_rate = $this->input->post('exchange_rate');

    $insert_data = array(
      "currency_name" => $currency_name,
      "currency_code" => $currency_code,
      "exchange_rate" => $exchange_rate
    );

    foreach($insert_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }
    }

    $isCodeExist = $this->exchange_rates_model->get_exchange_rate($currency_code);
    if($isCodeExist->num_rows() > 0 ){
      $data = array("success" => 0, "message" => "<u>".$currency_code."</u> already exists. Please try again.");
      generate_json($data);
      exit();
    }

    $isNameExists = $this->exchange_rates_model->get_exchange_rate(false,$currency_name);
    if($isNameExists->num_rows() > 0){
      $data = array("success" => 0, "message" => "<u>".$currency_name."</u> already exists. Please try again.");
      generate_json($data);
      exit();
    }

    $inserted = $this->exchange_rates_model->set_exchange_rate($insert_data);
    if($inserted === false){
      $data = array("success" => 1, "message" => "Unable to save exchange rate. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Exchange Rate Save Successfully.");
    generate_json($data);

  }

  public function update(){
    $this->isLoggedIn();

    $uid = $this->input->post('uid');
    $edit_currency_code = $this->input->post('edit_currency_code');
    $edit_currency_name = $this->input->post('edit_currency_name');
    $edit_exchange_rate = $this->input->post('edit_exchange_rate');

    if($uid == "" || $edit_currency_code == "" ||$edit_currency_name == "" || $edit_exchange_rate == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $isCodeExist = $this->exchange_rates_model->get_exchange_rate($edit_currency_code,false,$uid);
    if($isCodeExist->num_rows() > 0 ){
      $data = array("success" => 0, "message" => "<u>".$edit_currency_code."</u> already exists. Please try again.");
      generate_json($data);
      exit();
    }

    $isNameExists = $this->exchange_rates_model->get_exchange_rate(false,$edit_currency_name,$uid);
    if($isNameExists->num_rows() > 0){
      $data = array("success" => 0, "message" => "<u>".$edit_currency_name."</u> already exists. Please try again.");
      generate_json($data);
      exit();
    }

    $updated = $this->exchange_rates_model->update_exchange_rate(array($edit_currency_code, $edit_currency_name, $edit_exchange_rate, $uid));
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update exchange rate. Either you did not change anything or the update was not successfully save. Please try again.");
      generate_json($data);
      exit();
    }

    if($edit_currency_code != "PHP"){
      $contract = $this->exchange_rates_model->get_contract_with_same_currency($edit_currency_code);
      if($contract->num_rows() > 0){
        $update_batch = array();
        foreach($contract->result_array() as $row){
          $update_sal = array(
            "id" => $row['contract_id'],
            "total_sal_converted" => (float)$row['total_sal'] * (float)$row['ex_rate'],
          );
          $update_batch[] = $update_sal;
        }
        if(count((array)$update_batch) > 0){
          $this->exchange_rates_model->update_total_sal_converted($update_batch);
        }
      }
    }


    $data = array("success" => 1, "message" => "Exchange Rate Updated Successfully");
    generate_json($data);
  }

  public function delete(){
    $this->isLoggedIn();

    $delid = $this->input->post('delid');
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to delete this exchange rate. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $deleted = $this->exchange_rates_model->update_exchange_rate_status($delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete exchange rate. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Exchange Rate Deleted Successfully");
    generate_json($data);
  }
}
