<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_role extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/user_role_model');

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

  public function get_user_role_json(){
    $search = $this->input->post('searchValue');
    $data = $this->user_role_model->get_user_role_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'main_navs' => $this->user_role_model->get_main_nav(),
      'main_navs2' => $this->user_role_model->get_main_nav_w_content(),
      'content_navs' => $this->user_role_model->get_content_nav(),
      'functions' => $this->user_role_model->get_functions()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/user_role',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $add_position = $this->input->post('add_position');
    $add_hierarchy_lvl = $this->input->post('add_hierarchy_lvl');
    $add_main_nav = $this->input->post('add_main_nav');
    $add_content_nav = $this->input->post('add_content_nav');
    $access = array();

    if(empty($add_position) || empty($add_hierarchy_lvl)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    if(count((array)$add_main_nav) < 1){
      $data = array("success" => 0, "message" => "Please fill up atleast 1 Main Navigation Role");
      generate_json($data);
      exit();
    }

    if(count((array)$add_content_nav) > 0){
      foreach($add_content_nav as $nav){
        $func_access = $this->input->post('add_func_access_'.$nav);
        if(count((array)$func_access) > 0){
          $main['id'] = $nav;
          $main['access_func_nav'] = $func_access;
          $access[] = $main;
        }
      }
    }

    $access = json_encode($access);
    $main_nav = (count((array)$add_main_nav) > 0) ? implode(', ',$add_main_nav): "";
    $content_nav = (count((array)$add_content_nav) > 0) ? implode(', ',$add_content_nav) : "";

    $insert_data = array(
      "position" => $add_position,
      "hierarchy_lvl" => (float)$add_hierarchy_lvl,
      "access_nav" => $main_nav,
      "access_content_nav" => $content_nav,
      "access_func_nav" => $access
    );

    $inserted = $this->user_role_model->set_user_role($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save User role. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Save Successfully");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $pos_id = $this->input->post('pos_id');
    $position = $this->input->post('position');
    $main_nav = $this->input->post('main_nav');
    $content_nav = $this->input->post('content_nav');
    $access = array();

    if(empty($pos_id) || empty($position)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    if(count((array)$content_nav) > 0){
      foreach($content_nav as $nav){
        $func_access = $this->input->post('func_access_'.$nav);
        if(count((array)$func_access) > 0){
          $main['id'] = $nav;
          $main['access_func_nav'] = $func_access;
          $access[] = $main;
        }
      }
    }

    $access = json_encode($access);
    $main_nav = (count((array)$main_nav) > 0) ? implode(', ',$main_nav): "";
    $content_nav = (count((array)$content_nav) > 0) ? implode(', ',$content_nav) : "";

    $update_data = array(
      "access_nav" => $main_nav,
      "access_content_nav" => $content_nav,
      "position" => $position,
      "access_func_nav" => $access
    );

    $updated = $this->user_role_model->update_user_role($update_data,$pos_id);
    if($updated === false){
      $data = array("success" => 0, "message" => "There's no changes detected. Please try again.");
      generate_json($data);
      exit();
    }

    // if(count((array)$func_access_batch) > 0){
    //   $this->user_role_model->set_func_access_batch($func_access_batch);
    // }

    // if(count((array)$update_func_access_batch) > 0){
    //   $this->user_role_model->update_function_batch($update_func_access_batch);
    // }

    $data = array("success" => 1, "message" => "Updated Successfully");
    generate_json($data);
  }

  public function destroy(){

  }

  public function delete(){
    $delid = $this->input->post('delid');
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to delete this user role. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $updated = $this->user_role_model->update_user_role_status($delid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to delete this user role. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "User role delete Successfully");
    generate_json($data);
  }
}
