<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items_category extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/items_category_model');
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

  public function get_items_category_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->items_category_model->get_items_category_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/items_category',$data);
  }

  public function create(){
    $cat_name = $this->input->post('cat_name');
    if(empty($cat_name)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    $isExist = $this->items_category_model->get_category($cat_name);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Category name already exists. ");
      generate_json($data);
      exit();
    }

    $insert_data  = array('cat_name' => $cat_name, "created_at" => todaytime());
    $inserted = $this->items_category_model->set_category($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save category. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Item Category save successfully");
    generate_json($data);
  }

  public function update(){
    $uid = en_dec('dec',$this->input->post('uid'));
    $edit_cat_name = $this->input->post('edit_cat_name');

    if(empty($uid) || empty($edit_cat_name)){
      $data = array("success" => 0, "message" => "Something went wrong. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $isExist = $this->items_category_model->get_category($edit_cat_name,$uid);
    if($isExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Category name already exists. ");
      generate_json($data);
      exit();
    }

    $update_data = array("cat_name" => $edit_cat_name);
    $updated = $this->items_category_model->update_category($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update item category. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Item Category updated successfully");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec',$this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Something went wrong . Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $delete_data = array("enabled" => 0);
    $deleted = $this->items_category_model->update_category_status($delete_data,$delid);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete item category. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Item Category deleted successfully");
    generate_json($data);
  }
}
