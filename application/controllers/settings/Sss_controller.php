<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sss_controller extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/sss_model');
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

  public function get_sss_json(){
    $search = $this->input->post('searchValue');
    $data = $this->sss_model->get_sss_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/sss',$data);
  }

  public function create(){
    $range_from  = $this->input->post('range_from');
    $range_to = $this->input->post('range_to');
    $monthly_cred = $this->input->post('monthly_cred');
    $sss_er = $this->input->post('sss_er');
    $sss_ee = $this->input->post('sss_ee');
    $sss_total = $this->input->post('sss_total');
    $ec = $this->input->post('ec');
    $tc_er = $this->input->post('tc_er');
    $tc_ee = $this->input->post('tc_ee');
    $tc_total = $this->input->post('tc_total');

    $insert_data = array(
      "salRange_from" => $range_from,
      "salRange_to" => $range_to,
      "monthly_sal_cred" => $monthly_cred,
      "ss_er" => $sss_er,
      "ss_ee" => $sss_ee,
      "ss_total" => $sss_total,
      "ec_er" => $ec,
      "tc_er" => $tc_er,
      "tc_ee" => $tc_ee,
      "tc_total" => $tc_total,
      "enabled" => 1
    );

    foreach($insert_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fieldss");
        generate_json($data);
        exit();
      }
    }

    if($range_from > $range_to){
      $data = array("success" => 0, "message" => "Invalid Range of Compensation");
      generate_json($data);
      exit();
    }

    $check_sal_range = $this->sss_model->check_sss_sal_range(array($range_from,$range_to));
    if($check_sal_range->num_rows() > 0){
      $data = array("success" => 0, "message" => "Range of Compensation already exists.Please try again.");
      generate_json($data);
      exit();
    }

    $inserted = $this->sss_model->set_sss($insert_data);
    if($inserted == false){
      $data = array("success" => 0, "messag" => "Unable to save SSS. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Save Successfully.");
    generate_json($data);
  }

  public function update(){
    $update_id = $this->input->post('update_id');
    $range_from  = $this->input->post('range_from');
    $range_to = $this->input->post('range_to');
    $monthly_cred = $this->input->post('monthly_cred');
    $sss_er = $this->input->post('sss_er');
    $sss_ee = $this->input->post('sss_ee');
    $sss_total = $this->input->post('sss_total');
    $ec = $this->input->post('ec');
    $tc_er = $this->input->post('tc_er');
    $tc_ee = $this->input->post('tc_ee');
    $tc_total = $this->input->post('tc_total');

    $prevFrom = $this->input->post('prevFrom');
    $prevTo = $this->input->post('prevTo');

    $update_data = array(
      "salRange_from" => $range_from,
      "salRange_to" => $range_to,
      "monthly_sal_cred" => $monthly_cred,
      "ss_er" => $sss_er,
      "ss_ee" => $sss_ee,
      "ss_total" => $sss_total,
      "ec_er" => $ec,
      "tc_er" => $tc_er,
      "tc_ee" => $tc_ee,
      "tc_total" => $tc_total
    );

    foreach($update_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fields");
        generate_json($data);
        exit();
      }
    }

    if((float)$range_from > (float)$range_to){
      $data = array("success" => 0, "message" => "Invalid Range of Compensation");
      generate_json($data);
      exit();
    }

    if($range_from != $prevFrom && $range_to != $prevTo){
      $check_sal_range = $this->sss_model->check_sss_sal_range(array($range_from,$range_to));
      if($check_sal_range->num_rows() > 0){
        $data = array("success" => 0, "message" => "Range of Compensation already exists.Please try again.");
        generate_json($data);
        exit();
      }
    }

    $updated = $this->sss_model->update_ss($update_data,$update_id);
    if($updated == false){
      $data = array("success" => 0, "messag" => "Unable to update SSS. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Updated Successfully.");
    generate_json($data);
  }

  public function delete(){
    $delete_id = $this->input->post('del_id');
    $deleted = $this->sss_model->en_dis_sss(array(0,$delete_id));
    if($deleted  == false){
      $data = array("success" => 0, "message" => "Unable to delete SSS. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deleted Successfully");
    generate_json($data);
  }
}
