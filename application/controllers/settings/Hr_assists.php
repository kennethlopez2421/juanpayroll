<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Hr_assists extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/hr_assists_model');
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

  // public function (){
  //   $search = $this->input->post('searchValue');
  //   $data = $this->workorder_model->getWordOrder_json($search);
  //   echo json_encode($data);
  // }

  public function index($token = ""){
    $hr_assists = $this->hr_assists_model->get_hrassists()->row()->body;
    $hr_body = ($hr_assists == "") ? "" : $hr_assists;
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'hr_assists' => $hr_body
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/hr_assists',$data);
  }


  public function create(){
    $hrassists_body = $this->input->post('hrassists_body');
    $row = $this->hr_assists_model->get_hrassists();

    if($row->num_rows() > 0 ){ ### update ###

      $id = $row->row()->id;
      $user_id = $this->session->userdata('emp_idno');
      $updated = $this->hr_assists_model->update_hrassists($hrassists_body,$user_id,$id);
      if($updated == false){
        $data = array("success" => 0, "message" => "Unable to update HR Assists. Please try again");
        generate_json($data);
        exit();
      }

      $data = array("success" => 1, "message" => "HR Assists Updated Successfully");
      generate_json($data);

    }else{ ### create ###
      if(empty($hrassists_body)){
        $data = array("success" => 0, "message" => "Please fill up all required fields1.");
        generate_json($data);
        exit();
      }

      $insert_data = array(
        "body" => $hrassists_body,
        "created_by" => $this->session->userdata('emp_idno')
      );

      $inserted = $this->hr_assists_model->set_hrassists($insert_data);
      if($inserted == false){
        $data = array("success" => 0, "message" => "Unable to save HR Assists. Please try again");
        generate_json($data);
        exit();
      }

      $data = array("success" => 1, "message" => "HR Assists Successfully created.");
      generate_json($data);

    }
  }

}
