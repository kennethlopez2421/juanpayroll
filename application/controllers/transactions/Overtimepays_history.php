<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Overtimepays_history extends CI_Controller {

	public function __construct() {
		parent::__construct();
	$this->load->model('transactions/Overtimepays_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/overtimepays_history', $data);

	}

  public function getotpays_history_json(){
    $search = $this->input->post('searchValue');
    $data = $this->Overtimepays_model->getotpays_history_json($search);
    echo json_encode($data);
  }
}
