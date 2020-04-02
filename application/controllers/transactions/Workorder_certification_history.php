<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Workorder_certification_history extends CI_Controller{
  public function __construct() {
    parent::__construct();
    $this->load->model('transactions/workorder_model');
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

  public function index($token = ""){
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/workorder_for_certification',$data);
  }



}
