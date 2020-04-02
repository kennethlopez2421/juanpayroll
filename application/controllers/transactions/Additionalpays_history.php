<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Additionalpays_history extends CI_Controller {

	public function __construct() {
		parent::__construct();
	  $this->load->model('transactions/Additionalpays_model');
	}

  public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/additionalpays_history', $data);

	}

  public function getAdditionalPay_history_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Additionalpays_model->getAdditional_history_json($search);
    echo json_encode($data);
  }
}
