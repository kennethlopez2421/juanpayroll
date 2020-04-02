<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Nightdiff extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/nightdiff_model');

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

  public function get_nightdiff_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->nightdiff_model->get_nightdiff_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/nightdiff',$data);
  }

  public function update(){
    $uid = en_dec('dec',$this->input->post('uid'));
    $start = $this->input->post('start');
    $end = $this->input->post('end');
    $percent = $this->input->post('percent');
    $status = en_dec('dec',$this->input->post('status'));

    if(empty($start)|| empty($end) || empty($percent) || empty($status)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $update_data = array(
      "start" => $start,
      "end" => $end,
      "percent" => $percent,
      "status" => $status
    );

    $updated = $this->nightdiff_model->update($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update night differentials.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Updated successfully");
    generate_json($data);
    exit();
  }
}
