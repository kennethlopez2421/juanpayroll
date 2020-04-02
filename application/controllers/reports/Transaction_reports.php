<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transaction_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/transaction_reports_model');
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
        exit();
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
        exit();
      }
    }
  }

  public function get_transaction_reports_json(){
    $search = json_decode($this->input->post('searchValue'));

    switch ($search->filter) {
      case 'divAddPay':
        $data = $this->transaction_reports_model->get_transaction_reports_addpay_json($search);
        break;
      case 'divCa':
        $data = $this->transaction_reports_model->get_transaction_reports_ca_json($search);
        break;
      case 'divLeave':
        $data = $this->transaction_reports_model->get_transaction_reports_leave_json($search);
        break;
      case 'divOvertimePays':
        $data = $this->transaction_reports_model->get_transaction_reports_overtimepays_json($search);
        break;
      case 'divSalDeduct':
        $data = $this->transaction_reports_model->get_transaction_reports_sal_deduct_json($search);
        break;
      case 'divWorkOrder':
        $data = $this->transaction_reports_model->get_transaction_reports_workorder_json($search);
        break;
      case 'divOffset':
        $data = $this->transaction_reports_model->get_transaction_reports_offset_json($search);
        break;
      case 'divWorkSchedule':
        $data = $this->transaction_reports_model->get_transaction_reports_worksched_json($search);
        break;
      default:
        $data = $this->transaction_reports_model->get_transaction_reports_addpay_json($search);
        break;
    }

    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
      'positions' => $this->model->get_user_position()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('reports/transaction_reports',$data);
  }
}
