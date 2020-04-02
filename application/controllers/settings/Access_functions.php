<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Access_functions extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('settings/access_functions_model');

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

  public function get_access_functions_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->access_functions_model->get_access_functions_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'access_functions' => $this->access_functions_model->get_access_functions()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('settings/access_functions',$data);
  }

  public function create(){
    $access = $this->access_functions_model->get_access_functions();
    if($access->num_rows() > 0){
      foreach($access->result_array() as $row){
        $access_ids = explode(', ',$row['main_nav_access']);
        $access_func = $this->input->post("access_".$row['name']);
        if(count((array)$access_func) > 0){
          foreach($access_func as $func){
            $access_ids[] = en_dec('dec',$func);
          }
          $access_data = implode(', ',$access_ids);
          // die($row['id']);
          $this->access_functions_model->update_access_function(array($access_data,$row['id']));
        }
      }
    }

    $data = array("success" => 1, "message" => "Access Functions updated succesfully.");
    generate_json($data);
  }
}
