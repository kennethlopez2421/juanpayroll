<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashadvance extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('settings/cashadvance_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/cashadvance', $data);

	}





}
