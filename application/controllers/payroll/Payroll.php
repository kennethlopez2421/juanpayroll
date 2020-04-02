<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;

class Payroll extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('payroll/payroll_model');
    $this->load->model('payroll/payroll_model_new');
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

  public function index($token = ""){
    $this->session->unset_userdata('deduction');
    $this->session->unset_userdata('additionals');
    $this->session->unset_userdata('man_hours');
    $this->session->unset_userdata('payroll');
    $this->session->unset_userdata('p_date_from');
    $this->session->unset_userdata('p_date_to');
    $this->session->unset_userdata('p_paytype');
    $this->session->unset_userdata('p_dept');
    $this->session->unset_userdata('pay_day');
    // $this->session->unset_userdata('ca_payment');

    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'department' => $this->model->getDepartment(),
      'paytype' => $this->payroll_model->get_paytype(),
      'companies' => $this->model->get_hris_companies()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('payroll/payroll',$data);
  }
  ### deductions ###
  public function dduction_log_json(){
    $this->isLoggedIn();

    $searchValue = $this->input->post('searchValue');
    $p_dept = $this->input->post('p_dept');
    $p_company = $this->input->post('p_company');
    $p_date_from = $this->input->post('p_date_from');
    $p_date_to = $this->input->post('p_date_to');
    $p_paytype = $this->input->post('p_paytype');
    $p_paytype_range = $this->input->post('p_paytype_range');
    $p_paytype_frequency = $this->input->post('p_paytype_frequency');
    $p_pay_day = $this->input->post('p_pay_day');

    $data = $this->payroll_model->get_deduction_log($p_company,$p_date_from,$p_date_to,$p_paytype,$p_paytype_frequency,$p_pay_day);
    // print_r($data['data']);

    $dduct_arr = array(
      "dduction" => $data['data_all'],
      "ca_payment" => $data['ca_payment'], // for cashadvance payment history
      "sss_loan_payment" => $data['sss_loan_payment'], // for sss loan
      "pagibig_loan_payment" => $data['pagibig_loan_payment']
    );

    $this->session->set_userdata($dduct_arr);

    // die();
    echo json_encode($data);
  }
  ### additionals ###
  public function additional_log_json(){
    $this->isLoggedIn();

    $searchValue = $this->input->post('searchValue');
    $p_dept = $this->input->post('p_dept');
    $p_company = $this->input->post('p_company');
    $p_date_from = $this->input->post('p_date_from');
    $p_date_to = $this->input->post('p_date_to');
    $p_paytype = $this->input->post('p_paytype');
    $p_paytype_range = $this->input->post('p_paytype_range');
    $p_paytype_frequency = $this->input->post('p_paytype_frequency');

    $data = $this->payroll_model->get_additionals_log($p_company,$p_date_from,$p_date_to,$p_paytype,$p_paytype_frequency);
    // print_r($data['data']);
    $add_arr = array(
      "additionals" => $data['data_all']
    );
    // print_r($data['data_all']);
    $this->session->set_userdata($add_arr);
    echo json_encode($data);
  }
  ### manhours ###
  public function man_hours_log_json(){
    $this->isLoggedIn();

    $searchValue = $this->input->post('searchValue');
    $p_dept = $this->input->post('p_dept');
    $p_company = $this->input->post('p_company');
    $p_date_from = $this->input->post('p_date_from');
    $p_date_to = $this->input->post('p_date_to');
    $p_paytype = $this->input->post('p_paytype');
    $p_paytype_range = $this->input->post('p_paytype_range');
    $p_paytype_frequency = $this->input->post('p_paytype_frequency');

    $data = $this->payroll_model->get_man_hours_log($p_company,$p_date_from,$p_date_to,$p_paytype,$p_paytype_frequency);
    // print_r($data['data']);
    $mhours_arr = array(
      "man_hours" => $data['data_all']
    );
    $this->session->set_userdata($mhours_arr);
    echo json_encode($data);
  }

  public function get_data_breakdown(){
    $this->isLoggedIn();

    $active = $this->input->post('active');
    $emp_id = $this->input->post('emp_id');
    $from = $this->input->post("from");
    $to = $this->input->post("to");
    $type = $this->input->post('type');
    $frequency = $this->input->post('frequency');
    $pay_day = $this->input->post('pay_day');
    $dept = $this->input->post('dept');

    $dd_type = $this->input->post('dd_type');
    $add_type = $this->input->post('add_type');

    $dduct = $this->input->post('dduct');
    $add = $this->input->post('add');

    // echo $from;
    // echo $to;
    // exit();

    if($active == ""||$emp_id == ""||$from == ""||$to == ""||$type == ""||$frequency == ""){
      $data = array("success" => 0, "message" => "Unable to fetch any data");
      generate_json($data);
      exit();
    }

    switch ($active) {
      case 'manhours':
        $data = $this->payroll_model->get_manhours_breakdown($emp_id,$from,$to,$type,$frequency);
        // print_r($data);
        // die();
        echo json_encode($data);
      break;
      case 'dduction':
        if($dd_type == 'comp'){
          $data = $this->payroll_model->get_deduction_breakdown_comp($emp_id,$from,$to,$type,$frequency,$pay_day);
          echo json_encode($data);
        }

        if($dd_type == 'sd'){
          $data = $this->payroll_model->get_deduction_breakdown_sd($emp_id,$from,$to,$type,$frequency);
          echo json_encode($data);
        }

        if($dd_type == 'ca'){
          $data = $this->payroll_model->get_deduction_breakdown_ca($emp_id,$from,$to,$type,$frequency);
          echo json_encode($data);
        }
      break;
      case 'additional':
        if($add_type == 'add'){
          $data = $this->payroll_model->get_additionals_breakdown_add($emp_id,$from,$to,$type,$frequency);
          echo json_encode($data);
        }

        if($add_type == 'ot'){
          $data = $this->payroll_model->get_additionals_breakdown_ot($emp_id,$from,$to,$type,$frequency);
          echo json_encode($data);
        }
      break;
      case 'psummary':
        $data = $this->payroll_model->get_payroll_breakdown($emp_id,$from,$to,$type,$frequency,$dduct,$add);
        echo json_encode($data);
      break;
      default:
      break;
    }

  }
  ### payroll ###
  public function payroll_log_json(){
    $this->isLoggedIn();

    $searchValue = $this->input->post('searchValue');
    $p_dept = $this->input->post('p_dept');
    $p_company = $this->input->post('p_company');
    $p_date_from = $this->input->post('p_date_from');
    $p_date_to = $this->input->post('p_date_to');
    $p_paytype = $this->input->post('p_paytype');
    $p_paytype_range = $this->input->post('p_paytype_range');
    $p_paytype_frequency = $this->input->post('p_paytype_frequency');
    $p_pay_day = $this->input->post('p_pay_day');

    $data = $this->payroll_model_new->get_payroll_log($p_company,$p_date_from,$p_date_to,$p_paytype,$p_paytype_frequency,$p_pay_day);

    // print_r($data['data']);
    $payroll_arr = array(
      "payroll" => $data['data'],
      "p_dept" => $p_dept,
      "p_date_from" => $p_date_from,
      "p_date_to" => $p_date_to,
      "p_paytype" => $p_paytype,
      "p_company" => $p_company
    );
    $this->session->set_userdata($payroll_arr);
    echo json_encode($data);
  }

  public function gen_payroll(){
    $this->isLoggedIn();

    $p_dept = $this->input->post('p_dept');
    $p_company = $this->input->post('p_company');
    $p_date_from = $this->input->post('p_date_from');
    $p_date_to = $this->input->post('p_date_to');
    $p_paytype = $this->input->post('p_paytype');
    $p_paytype_range = $this->input->post('p_paytype_range');
    $p_paytype_frequency = $this->input->post('p_paytype_frequency');
    $pay_day = $this->input->post('pay_day');

    ### check empty data ###
    if(
      $p_dept == "" ||
      $p_company == "" ||
      $p_paytype == "" ||
      $p_date_from == "" ||
      $p_date_to == "" ||
      $p_paytype_range == "" ||
      $p_paytype_frequency == "" ||
      $pay_day == ""
    ){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }
    ### check if payroll already exist ###
    $check_data = array($p_paytype,$p_date_from,$p_date_to,$p_company);
    $row = $this->payroll_model->check_payroll_summary($check_data)->num_rows();
    if($row > 0){
      $data = array("success" => 0, "message" => "Payroll already exist");
      generate_json($data);
      exit();
    }

    $this->session->set_userdata('pay_day', $pay_day);
    $data = array("success" => 1);
    generate_json($data);

  }

  public function save_payroll(){
    $this->isLoggedIn();

    $p_date_from = $this->session->userdata('p_date_from');
    $p_date_to = $this->session->userdata('p_date_to');
    $p_paytype = $this->session->userdata('p_paytype');
    $p_dept = $this->session->userdata('p_dept');
    $p_company = $this->session->userdata('p_company');

    $check_data = array($p_paytype,$p_date_from,$p_date_to,$p_company);
    $row = $this->payroll_model->check_payroll_summary($check_data)->num_rows();
    if($row > 0){
      $data = array("success" => 0, "message" => "Payroll already exist");
      generate_json($data);
      exit();
    }

    $man_hours = $this->session->userdata('man_hours');
    $dduction = $this->session->userdata('dduction');
    $additionals = $this->session->userdata('additionals');
    $payroll = $this->session->userdata('payroll');

    if($p_date_from == "" || $p_date_to == "" || $p_paytype == ""){
      $data = array("success" => 0, "message" => "Unable to save payroll. Please try aagin1");
      generate_json($data);
      exit();
    }

    if(count((array)$man_hours) == 0 || count((array)$payroll) == 0){
      $data = array("success" => 0, "message" => "No payroll to save. Please try again.");
      generate_json($data);
      exit();
    }

    ### save summary ###
    $summary_data = array(
      "fromdate" => $p_date_from,
      "todate" => $p_date_to,
      "paytype" => $p_paytype,
      "company_id" => $p_company,
      "created_by" => $this->session->userdata('emp_idno')
    );

    $manhours_id = $this->payroll_model->set_man_hours_summary($summary_data);
    $deduction_id = $this->payroll_model->set_deduction_summary($summary_data);
    $additional_id = $this->payroll_model->set_additionals_summary($summary_data);

    $ref_no = generate_player_no();
    while($this->payroll_model->check_payroll_ref_no($ref_no)->num_rows() > 0){
      $ref_no = generate_player_no();
    }
    $payroll_sum_data = array(
      "ref_no" => $ref_no,
      "manhours_id" => $manhours_id,
      "deduction_id" => $deduction_id,
      "additional_id" => $additional_id,
      "company_id" => $p_company,
      "pay_day" => $this->session->userdata('pay_day'),
      "fromdate" => $p_date_from,
      "todate" => $p_date_to,
      "paytype" => $p_paytype,
      "created_by" => $this->session->userdata('emp_idno')
    );
    $payroll_id = $this->payroll_model->set_payroll_summary($payroll_sum_data);

    if($manhours_id == "" || $deduction_id == "" || $additional_id == "" || $payroll_id == ""){
      $data = array("success" => 0, "message" => "Unable to save payroll. Please try aagin2");
      generate_json($data);
      exit();
    }

    ### save logs ###
    $mh_inserted = false;
    $dduct_inserted = false;
    $add_inserted = false;
    $payroll_inserted = false;

    $manhours_data = array();
    $dduction_data = array();
    $additional_data = array();
    $payroll_log_data = array();

    ### manhours log ###
    foreach($man_hours as $mh){
      // $emp = explode('-',$mh[0]);
      $mh_data = array(
        "manhours_summary_id" => $manhours_id,
        "emp_id" => $mh[0],
        "contract_refno" => $mh[10],
        "fromdate" => $p_date_from,
        "todate" => $p_date_to,
        "paytype" => $p_paytype,
        "days" => $mh[2],
        "hours" => $mh[3],
        "nightdiff" => $mh[4],
        "absent" => $mh[5],
        "late" => $mh[6],
        "ot" => $mh[7],
        "ut" => $mh[8],
        "created_by" => $this->session->userdata('emp_idno')
      );
      $manhours_data[] = $mh_data;
      // $mh_inserted = $this->payroll_model->set_man_hours_log($mh_data);
    }
    ### deduction log ###
    foreach($dduction as $dd){
      // $emp = explode('-',$dd[0]);
      $dd_data = array(
        "deductionsum_id" => $deduction_id,
        "employee_idno" => $dd[0],
        "contract_refno" => $dd[11],
        "fromdate" => $p_date_from,
        "todate" => $p_date_to,
        "paytype" => $p_paytype,
        "sss" => number($dd[2]),
        "sss_loan" => number($dd[3]),
        "philhealth" => number($dd[4]),
        "pag_ibig" => number($dd[5]),
        "pag_ibig_loan" => number($dd[6]),
        "salary_deduction" => number($dd[7]),
        "cashadvance" => number($dd[8]),
        "currency" => $dd[12],
        "ex_rate" => number($dd[13]),
        "created_by" => $this->session->userdata('emp_idno')
      );
      // $dduct_inserted = $this->payroll_model->set_deduction_log($dd_data);
      $dduction_data[] = $dd_data;
    }
    ### additional log ###
    foreach($additionals as $add){
      // $emp = explode('-',$add[0]);
      $add_data = array(
        "additional_summary_id" => $additional_id,
        "emp_id" => $add[0],
        "contract_refno" => $add[5],
        "fromdate" => $p_date_from,
        "todate" => $p_date_to,
        "paytype" => $p_paytype,
        "additionalpay" => number($add[2]),
        "overtimepay" => number($add[3]),
        "currency" => $add[6],
        "ex_rate" => number($add[7]),
        "created_by" => $this->session->userdata('emp_idno')
      );
      // $add_inserted = $this->payroll_model->set_additionals_log($add_data);
      $additional_data[] = $add_data;
    }
    ### payroll log ###
    foreach($payroll as $p){
      // $emp = explode('-',$p[0]);
      $payroll_data = array(
        "payroll_summary_id" => $payroll_id,
        "emp_id" => $p[0],
        "contract_refno" => $p[7],
        "fromdate" => $p_date_from,
        "todate" => $p_date_to,
        "paytype" => $p_paytype,
        "grosspay" => number($p[2]),
        "deductions" => number($p[4]),
        "additionals" => number($p[3]),
        "netpay" => number($p[5]),
        "currency" => $p[8],
        "ex_rate" => number($p[9]),
        "created_by" => $this->session->userdata('emp_idno')
      );
      // $payroll_inserted = $this->payroll_model->set_payroll_log($payroll_data);
      $payroll_log_data[] = $payroll_data;
    }

    $mh_inserted = $this->payroll_model->set_man_hours_log_batch($manhours_data);
    $dduct_inserted = $this->payroll_model->set_deduction_log_batch($dduction_data);
    $add_inserted = $this->payroll_model->set_additionals_log_batch($additional_data);
    $payroll_inserted = $this->payroll_model->set_payroll_log_batch($payroll_log_data);

    if($mh_inserted == false || $dduct_inserted == false || $add_inserted == false || $payroll_inserted == false){
      $data = array("success" => 0, "message" => "Unable to save payroll. Please try again3");
      generate_json($data);
      exit();
    }

    ### deduction log history ###
    $ca_data = array();
    $sss_loan_data = array();
    $pagibig_loan_data = array();

    foreach($this->session->ca_payment as $ca){
      $ca_insert_data = array(
        "ca_id" => $ca['ca_id'],
        "employee_idno" => $ca['id'],
        "payroll_refno" => $ref_no,
        "ca_payment" => $ca['ca_payment'],
        "ca_balance" => $ca['ca_balance'],
        "ca_from" => $ca['from'],
        "ca_to" => $ca['to']
      );
      $ca_data[] = $ca_insert_data;
      // $this->payroll_model->set_cashadvance_pending_deduction($ca_insert_data);
    }

    foreach($this->session->sss_loan_payment as $sss){
      $sss_arr = array(
        "sss_loan_id" => $sss['sss_loan_id'],
        "employee_idno" => $sss['employee_idno'],
        "payroll_refno" => $ref_no,
         "monthly_amortization" => $sss['monthly_amortization'],
        "sss_loan_from" => $sss['sss_loan_from'],
        "sss_loan_to" => $sss['sss_loan_to'],
        "payday" => $sss['payday']
      );
      $sss_loan_data[] =$sss_arr;
    }

    foreach($this->session->pagibig_loan_payment as $love){
      $love_arr = array(
        "pagibig_loan_id" => $love['pagibig_loan_id'],
        "employee_idno" => $love['employee_idno'],
        "payroll_refno" => $ref_no,
         "monthly_amortization" => $love['monthly_amortization'],
        "pagibig_loan_from" => $love['pagibig_loan_from'],
        "pagibig_loan_to" => $love['pagibig_loan_to'],
        "payday" => $love['payday']
      );
      $pagibig_loan_data[] =$love_arr;
    }
    // CASH ADVANCE
    if(count((array)$ca_data) > 0){
      $this->payroll_model->set_cashadvance_pending_deduction_batch($ca_data);
    }
    // SSS LOAN
    if(count((array)$sss_loan_data) > 0){
      $this->payroll_model->set_sss_loan_pending_deduction_batch($sss_loan_data);
    }
    // PHILHEALTH LOAN
    if(count((array)$pagibig_loan_data) > 0){
      $this->payroll_model->set_pagibig_loan_pending_deduction_batch($pagibig_loan_data);
    }

    $data = array("success" => 1, "message" => "Payroll save successfully");
    generate_json($data);
  }

  public function generate_bank_file($token = "", $template = "", $payroll_refno = "", $bank = "", $file_type = "", $data = ""){
    $token_dec = $this->session->userdata('token_session');
    $token = en_dec('dec',$token);
    if($token_dec != $token){
      $this->session->sess_destroy();
      $this->load->view('login');
      exit();
    }

    $type1 = ucfirst($file_type);
    $type2 = $file_type;

    // echo $payroll_refno;
    // echo $bank;
    // die();

    $bank_file_data = $this->payroll_model->get_bank_file_data($payroll_refno,$bank);
    // die($bank_file_data);
    if($bank_file_data->num_rows() == 0){
      $data = array("success" => 0, "message" => "No bank file data available.w");
      generate_json($data);
      exit();
    }

    switch ($template) {
      case 'bdo_template':
        $header = "BDO Bank File";
        $bdo_file_prefix = $this->input->get('bdo_file_prefix');
        $bdo_company_name = $this->input->get('bdo_company_name');
        $bdo_virtual_account = $this->input->get('bdo_virtual_account');
        $bdo_credit_date = $this->input->get('bdo_credit_date');
        $bdo_batch_no = $this->input->get('bdo_batch_no');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('HRIS')
            ->setLastModifiedBy('HRIS')
            ->setTitle('BDO Bank File')
            ->setSubject('BDO Bank File')
            ->setDescription('BDO Bank File');

        $styleArray = array('font' => array('bold' => true,));
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A6:D6')->applyFromArray($styleArray);

        foreach(range('A','D') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A1",$bdo_company_name)
            ->setCellValue("A2",'File Prefix:')
            ->setCellValue("A3",'Virtual Account:')
            ->setCellValue("A4",'Credit Date:')
            ->setCellValue("A5",'Batch No.:')
            ->setCellValue("B2",$bdo_file_prefix)
            ->setCellValue("B3",$bdo_virtual_account)
            ->setCellValue("B4",$bdo_credit_date)
            ->setCellValue("B5",$bdo_batch_no)
            ->setCellValue("A6",'Account #')
            ->setCellValue("B6",'Amount')
            ->setCellValue("C6",'Name')
            ->setCellValue("D6",'Remarks');


        $x= 7;
        $j = 6;
        foreach($bank_file_data->result_array() as $sub){
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A$x",$sub['account_number'])
                ->setCellValue("B$x",number_format((float)$sub['netpay'] * (float)$sub['ex_rate'],2))
                ->setCellValue("C$x",$sub['fullname'])
                ->setCellValue("D$x",'');
            $x++;
            $j++;
        }

        $spreadsheet->getActiveSheet()->setTitle('BDO Bank File');
        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$header.'.'.$type2.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $type1);
        $writer->save('php://output');

        break;
      case 'metro_bank_template':
        $header = "Metro Bank File";
        $metro_company_name = $this->input->get('metro_company_name');
        $metro_branch_code = $this->input->get('metro_branch_code');
        $metro_date = $this->input->get('metro_date');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('HRIS')
            ->setLastModifiedBy('HRIS')
            ->setTitle('Metro Bank File')
            ->setSubject('Metro Bank File')
            ->setDescription('Metro Bank File');

        $styleArray = array('font' => array('bold' => true,));
        $spreadsheet->getActiveSheet()->getStyle('A1:A2')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:E3')->applyFromArray($styleArray);

        foreach(range('A','E') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A1",$metro_company_name)
            ->setCellValue("A2",'Date:')
            ->setCellValue("B2",$metro_date)
            ->setCellValue("A3",'Employee Code')
            ->setCellValue("B3",'Employee Name')
            ->setCellValue("C3",'Branch Code')
            ->setCellValue("D3",'Payroll Account Number')
            ->setCellValue("E3",'Amount');


        $x= 4;
        $j = 3;
        foreach($bank_file_data->result_array() as $sub){
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A$x",$sub['employee_idno'])
                ->setCellValue("B$x",$sub['fullname'])
                ->setCellValue("C$x",$metro_branch_code)
                ->setCellValue("D$x",$sub['account_number'])
                ->setCellValue("E$x",number_format((float)$sub['netpay'] * (float)$sub['ex_rate'],2));
            $x++;
            $j++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Metro Bank File');
        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$header.'.'.$type2.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $type1);
        $writer->save('php://output');
        break;
      case 'ctbc_template':
        $header = "CTBC Bank File";
        $ctbc_company_name = $this->input->get('ctbc_company_name');
        $ctbc_date = $this->input->get('ctbc_date');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('HRIS')
            ->setLastModifiedBy('HRIS')
            ->setTitle('CTBC Bank File')
            ->setSubject('CTBC Bank File')
            ->setDescription('CTBC Bank File');

        $styleArray = array('font' => array('bold' => true,));
        $spreadsheet->getActiveSheet()->getStyle('A1:A2')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:D3')->applyFromArray($styleArray);

        foreach(range('A','D') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A1",$ctbc_company_name)
            ->setCellValue("A2",'Date:')
            ->setCellValue("B2",$ctbc_date)
            ->setCellValue("A3",'Target Account')
            ->setCellValue("B3",'Amount')
            ->setCellValue("C3",'Email')
            ->setCellValue("D3",'Particulars');


        $x= 4;
        $j = 3;
        foreach($bank_file_data->result_array() as $sub){
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A$x",$sub['account_number'])
                ->setCellValue("B$x",number_format((float)$sub['netpay'] * (float)$sub['ex_rate'],2))
                ->setCellValue("C$x",$sub['email'])
                ->setCellValue("D$x",'');
            $x++;
            $j++;
        }

        $spreadsheet->getActiveSheet()->setTitle('CTBC Bank File');
        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$header.'.'.$type2.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $type1);
        $writer->save('php://output');
        break;
      default:
        $header = "HRIS Bank File";
        $default_company_name = $this->input->post('default_company_name');
        $default_date = $this->input->post('default_date');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('HRIS')
            ->setLastModifiedBy('HRIS')
            ->setTitle('HRIS Bank File')
            ->setSubject('HRIS Bank File')
            ->setDescription('HRIS Bank File');

        $styleArray = array('font' => array('bold' => true,));
        $spreadsheet->getActiveSheet()->getStyle('A1:A2')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:D3')->applyFromArray($styleArray);

        foreach(range('A','D') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A1",$default_company_name)
            ->setCellValue("A2",'Date:')
            ->setCellValue("B2",$default_date)
            ->setCellValue("A3",'Target Account')
            ->setCellValue("B3",'Amount')
            ->setCellValue("C3",'Email')
            ->setCellValue("D3",'Particulars');


        $x= 4;
        $j = 3;
        foreach($bank_file_data->result_array() as $sub){
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A$x",$sub['account_number'])
                ->setCellValue("B$x",number_format((float)$sub['netpay'] * (float)$sub['ex_rate'],2))
                ->setCellValue("C$x",$sub['email'])
                ->setCellValue("D$x",'');
            $x++;
            $j++;
        }

        $spreadsheet->getActiveSheet()->setTitle('CTBC Bank File');
        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$header.'.'.$type2.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $type1);
        $writer->save('php://output');
        break;
    }
  }
}
