<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Issued_items extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('employees/issued_items_model');
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

  public function get_issued_items_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->issued_items_model->get_issued_items_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'department' => $this->model->getDepartment(),
      'categories' => $this->issued_items_model->get_item_category()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('employees/issued_items',$data);
  }

  public function get_employee_by_dept(){
    $dept_id = $this->input->post('dept_id');

    if(empty($dept_id)){
      $data = array("success" => 0, "message" => "Invalid department . Please try again.");
      generate_json($data);
      exit();
    }

    $emps = $this->issued_items_model->get_employee_by_dept($dept_id);
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

    $employee = $this->input->post('employee');
    $item_cat = $this->input->post('item_cat');
    $item_condition = $this->input->post('item_condition');
    $item_name = $this->input->post('item_name');
    $serial_no = $this->input->post('serial_no');
    $date_issued = $this->input->post('date_issued');
    $date_received = $this->input->post('date_received');
    $date_returned = $this->input->post('date_returned');
    $price = $this->input->post('real_price');
    $note = $this->input->post('note');

    if(empty($employee) || empty($item_cat) || empty($item_condition) || empty($item_name) || empty($serial_no) || empty($date_issued) || empty($date_received) ||empty($price) || empty($note)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $issued = $this->issued_items_model->get_issued_items_by_serial_no($serial_no);
    if($issued->num_rows() > 0){
      $data = array("success" => 0, "message" => "Item is still issued to another employee. Please check if it was already returned.");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "employee_idno" => $employee,
      "item_name" => $item_name,
      "cat_id" => $item_cat,
      "serial_no" => $serial_no,
      "item_condition" => $item_condition,
      "price" => $price,
      "date_issued" => $date_issued,
      "date_received" => $date_received,
      "notes" => $note,
      "issued_by" => $this->session->emp_idno,
      "created_at" => todaytime()
    );
    $inserted = $this->issued_items_model->set_issued_item($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save issued item. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Issued item save successfully");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec',$this->input->post('uid'));
    $item_cat = $this->input->post('edit_item_cat');
    $item_condition = $this->input->post('edit_item_condition');
    $item_name = $this->input->post('edit_item_name');
    $serial_no = $this->input->post('edit_serial_no');
    $date_issued = $this->input->post('edit_date_issued');
    $date_received = $this->input->post('edit_date_received');
    $date_returned = $this->input->post('edit_date_returned');
    $price = $this->input->post('real_price');
    $note = $this->input->post('edit_note');

    if(empty($item_cat) || empty($item_condition) || empty($item_name) || empty($serial_no) || empty($date_issued) || empty($date_received) ||empty($price) || empty($note)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $issued = $this->issued_items_model->get_issued_items_by_serial_no($serial_no,$uid);
    if($issued->num_rows() > 0){
      $data = array("success" => 0, "message" => "Item is still issued to another employee. Please check if it was already returned.");
      generate_json($data);
      exit();
    }

    $update_data = array(
      "item_name" => $item_name,
      "cat_id" => $item_cat,
      "serial_no" => $serial_no,
      "item_condition" => $item_condition,
      "price" => $price,
      "date_issued" => $date_issued,
      "date_received" => $date_received,
      "date_returned" => ($date_returned == "") ? NULL : $date_returned,
      "notes" => $note,
    );
    $updated = $this->issued_items_model->update_issued_item($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update issued item. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Issued item updated successfully");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec', $this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Something went wrong. Please try again");
      generate_json($data);
      exit();
    }

    $delete_data = array("enabled" => 0);
    $deleted = $this->issued_items_model->update_issued_item_status($delete_data,$delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete issued items. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Issued item deleted successfully");
    generate_json($data);
  }

}
