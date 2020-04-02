<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cashadvancepaymentscheme extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/cashadvancepaymentscheme_model');

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

  public function getcaps_json(){
    $search = $this->input->post('searchValue');
    $data = $this->cashadvancepaymentscheme_model->getCaps_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/cashadvancepaymentscheme',$data);
  }

  public function add(){
    $this->isLoggedIn();

    $monthly_rate = $this->input->post('monthly_rate');
    $maximum_loan = $this->input->post('maximum_loan');
    $term_of_payment = $this->input->post('term_of_payment');

    if(empty($monthly_rate) || empty($maximum_loan) || empty($term_of_payment)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "monthly_rate" => $monthly_rate,
      "maximum_loan" => $maximum_loan,
      "term_of_payment" => $term_of_payment
    );

    $inserted = $this->cashadvancepaymentscheme_model->setPaymentScheme($insert_data);
    if($inserted == false){
      $data = array("success" => 0, "message" => "Failed to save Cash Advance Payment Scheme. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Save Successful");
    generate_json($data);
  }

  public function edit(){
    $this->isLoggedIn();
    $edit_id = $this->input->post('edit_id');
    $monthly_rate = $this->input->post('monthly_rate');
    $maximum_loan = $this->input->post('maximum_loan');
    $term_of_payment = $this->input->post('term_of_payment');

    if(empty($monthly_rate) || empty($maximum_loan) || empty($term_of_payment)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $update_data = array($monthly_rate,$maximum_loan,$term_of_payment,$edit_id);

    $updated = $this->cashadvancepaymentscheme_model->updatePaymentScheme($update_data);
    if($updated == false){
      $data = array("success" => 0, "message" => "Failed to update Cash Advance Payment Scheme. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Update Successful");
    generate_json($data);
  }

  public function delete(){
    $del_id = $this->input->post('del_id');

    if(empty($del_id)){
      $data = array("success" => 0, "message" => "Failed to delete. Please try again1");
      generate_json($data);
      exit();
    }

    $delete_data = array(0,$del_id);
    $deleted = $this->cashadvancepaymentscheme_model->updatePaymentSchemeStatus($delete_data);
    if($deleted == false){
      $data = array("success" => 0, "message" => "Failed to delete. Please try again2");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deleted Successfully");
    generate_json($data);
  }
}
