<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payroll_history_new extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('payroll/payroll_history_new_model');
    $this->load->model('payroll/payroll_model_new');
    $this->load->model('payroll/payroll_model');
    $this->load->model('payroll/payroll_history_model');
		$this->load->model('reports/compensation_reports_model');
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

  public function get_payroll_history_json(){
    $this->isLoggedIn();

    $search = json_decode($this->input->post('searchValue'));
    $data = $this->payroll_history_new_model->get_payroll_history_json($search);
    echo json_encode($data);
  }

  public function get_manhours_tbl_json(){
    $this->isLoggedIn();

    $id = en_dec('dec',$this->input->post('searchValue'));
    $data = $this->payroll_history_new_model->get_manhours_tbl_json($id);
    echo json_encode($data);
  }

  public function get_dduction_log_tbl_json(){
    $this->isLoggedIn();

    $id = en_dec('dec',$this->input->post('searchValue'));
    $data = $this->payroll_history_new_model->get_dduction_log_tbl_json($id);
    echo json_encode($data);
  }

  public function get_additional_log_tbl_json(){
    $this->isLoggedIn();

    $id = en_dec('dec',$this->input->post('searchValue'));
    $data = $this->payroll_history_new_model->get_additional_log_tbl_json($id);
    echo json_encode($data);
  }

  public function get_payroll_log_tbl_json(){
    $this->isLoggedIn();

    $id = en_dec('dec',$this->input->post('searchValue'));
    $data = $this->payroll_history_new_model->get_payroll_log_tbl_json($id);
    echo json_encode($data);
  }

  public function get_payslip_data(){
    $this->isLoggedIn();

    $payroll_refno = en_dec('dec', $this->input->post('payroll_refno'));
    $emp_idno = en_dec('dec', $this->input->post('emp_idno'));
    $fromdate = $this->input->post('fromdate');
    $todate = $this->input->post('todate');
    $type = $this->input->post('type');
    $frequency = $this->input->post('frequency');
    $pay_day = $this->input->post('pay_day');
    $company_id = $this->input->post('company_id');

    if(empty($payroll_refno) || empty($emp_idno) || empty($fromdate) || empty($todate) || empty($type) || empty($frequency) || empty($pay_day) || empty($company_id)){
      $data = array("success" => 0, "message" => "Please fill up all required fields and try again.");
      generate_json($data);
      exit();
    }

    $payslip_data = $this->payroll_model_new->get_payslip_data($company_id,$fromdate,$todate,$type,$frequency,$pay_day,$emp_idno,$payroll_refno);
    // print_r($payslip_data);
    // die();
    if(count((array)$payslip_data)== 0){
      $data = array("success" => 0, "message" => "Unable to get any data. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => $payslip_data);
    generate_json($data);

  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'banks' => $this->model->get_bank()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('payroll/payroll_history_new',$data);
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec',$this->input->post('uid'));
    $ref_no = $this->input->post('ref_no');
    $payroll_refno = $ref_no;
    $approved_by = $this->session->emp_idno;
    if(empty($uid) || empty($ref_no)){
      $data = array("success" => 0, "message" => "Unable to update payroll status. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $updated = $this->payroll_history_new_model->update_payroll_history_status($uid,$ref_no,$approved_by,todaytime(),today());
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update payroll status. Please try again.");
      generate_json($data);
      exit();
    }

    ### update employee_record first month ###
    $ids = $this->payroll_history_new_model->get_employee_id_from_payroll_log($payroll_refno);
    if($ids->num_rows() > 0){
      $update_data = array();
      foreach($ids->result_array() as $row){
        $update_month = array(
          'employee_idno' => $row['emp_id'],
          'first_month' => 1
        );

        $update_data[] = $update_month;
      }

      // print_r($update_data);
      // die();
      $this->payroll_history_new_model->update_first_month_employee_record($update_data);
    }

    ### tagging of additionals and deduction on payroll ###
    $tag_data = array($uid);
    $this->payroll_model->tag_additional_on_payroll($tag_data);
    $this->payroll_model->tag_overtime_on_payroll($tag_data);
    $this->payroll_model->tag_salary_deduction_on_payroll($tag_data);

    ### cash advance payment history ###
    $ca_payment = $this->payroll_model->get_ca_pending_deduction($payroll_refno);
    if($ca_payment->num_rows() > 0){
      foreach($ca_payment->result_array() as $ca){
        $insert_data = array(
          "ca_id" => $ca['ca_id'],
          "employee_idno" => $ca['employee_idno'],
          "payroll_ref_no" => $ca['payroll_refno'],
          "ca_payment" => $ca['ca_payment'],
          "ca_balance" => $ca['ca_balance'],
          "cutoff_from" => $ca['ca_from'],
          "cutoff_to" => $ca['ca_to']
        );

        $this->payroll_model->set_cashadvance_payment($insert_data);
      }

    }

    ### update sss loan pending deduction ###
    $this->payroll_model->update_sss_loan_pending_deduction($payroll_refno);

    ### update pagibig loan pending deduction ###
    $this->payroll_model->update_pagibig_loan_pending_deduction($payroll_refno);

    ### tag cashadvance on payroll ###
    // $this->payroll_model->tag_cash_advance_pay_on_payroll($tag_data);

    ### set hris_compesation_reports data ###
    $this->compensation_reports_model->set_compensation_reports();

    ### set payslip data ###

    ### SEND PAYROLL TO EMPLOYEE EMAIL ###
    $emails = $this->payroll_history_model->get_email_w_refno($payroll_refno);
    if($emails->num_rows() > 0){
      // $this->load->library('email');
      $payslip_batch_data = array();

      foreach($emails->result_array() as $email){
        $token_fix = "CloudPandaPHInc";
        $hash_refno = removeSpecialchar(en_dec("en", $payroll_refno));
        $token_email = en_dec('en',$token_fix);
        $date = new Datetime($email['pay_day']);
        $payslip_data = array($email['company_id'],$email['fromdate'],$email['todate'],$email['paytype'],$email['frequency'],$email['pay_day'],$email['employee_idno'],$payroll_refno,$date->format('F d, Y'));
        $hash_payslip_data = en_dec('en', json_encode($payslip_data));
        $payslip = $this->payroll_model_new->get_payslip_data($email['company_id'],$email['fromdate'],$email['todate'],$email['paytype'],$email['frequency'],$email['pay_day'],$email['employee_idno'],$payroll_refno);
        // print_r($payslip);
        // die();
        $p_data = array(
          "employee_idno"            => $email['employee_idno'],
          "payroll_refno"            => $payroll_refno,
          "name"                     => $email['fullname'],
          "paytype_desc"             => $email['paytype_desc'],
          "date_from"                => $email['fromdate'],
          "date_to"                  => $email['todate'],
          "gross_salary"             => number($payslip['gross_pay']),
          "gross_salary_less"        => number($payslip['gross_pay_less']),
          "days_duration"            => number($payslip['wdays']),
          "overtime"                 => number($payslip['ot_pay']),
          "ot_duration"              => number($payslip['ot_min']),
          "additionals"              => number($payslip['add_pay']),
          "regular_holiday"          => number($payslip['reg_holiday_pay']),
          "regular_holiday_duration" => number($payslip['reg_holiday']),
          "special_holiday"          => number($payslip['spl_holiday_pay']),
          "special_holiday_duration" => number($payslip['spl_holiday']),
          "sundays"                  => number($payslip['sunday_pay']),
          "sunday_duration"          => number($payslip['sunday']),
          "nightdiff_hours"          => number($payslip['nightdiff_hrs']),
          "night_diff"               => number($payslip['night_diff']),
          "absent"                   => number($payslip['absent_deduction']),
          "absent_duration"          => number($payslip['absent']),
          "late"                     => number($payslip['late_deduct']),
          "late_duration"            => number($payslip['late']),
          "undertime"                => number($payslip['ut_deduct']),
          "undertime_duration"       => number($payslip['ut']),
          "sss"                      => number($payslip['sss']),
          "philhealth"               => number($payslip['philhealth']),
          "pag_ibig"                 => number($payslip['pagibig']),
          "sss_loan"                 => number($payslip['sss_loan']),
          "pag_ibig_loan"            => number($payslip['pagibig_loan']),
          "cashadvance"              => number($payslip['cashadvance']),
          "salary_deduction"         => number($payslip['sal_deduct']),
          "total_deductions"         => number($payslip['total_deduct']),
          "netpay"                   => number($payslip['net_pay']),
          "date_created"             => todaytime()
        );
        $payslip_batch_data[] = $p_data;

        // $email_data['date'] = $date->format('F d, Y');
        // $email_data['fullname'] = $email['fullname'];
        // $email_data['fromdate'] = $email['fromdate'];
        // $email_data['todate'] = $email['todate'];
        // $email_data['download_link'] = base_url('Main/download_payslip/'.$hash_refno.'/'.$token_email.'/'.removeSpecialchar($hash_payslip_data));
        //
        // $msg = $this->load->view('emails/payslip_email',$email_data,true);
        //
        // $this->email->from('support@cloudpanda.ph', 'One Payroll');
        // $this->email->to($email['email']);
        //
        // $this->email->subject($date->format('F d, Y').' Payslip');
        // $this->email->message($msg);
        // $email = $this->email->send();
      }

      $this->payroll_history_new_model->set_payslip_batch($payslip_batch_data);

    }

    $data = array("success" => 1, "message" => "Payroll approved successfully");
    generate_json($data);

  }
}
