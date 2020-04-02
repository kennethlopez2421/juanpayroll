<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contract extends CI_Controller{
  public function __construct(){
    parent::__construct();
    $this->load->model('employees/contracts/contract_model');
    $this->load->model('settings/department_model');
    $this->load->model('settings/subdepartment_model');
    $this->load->model('settings/exchange_rates_model');
    $this->load->model('settings/contract_template_model');

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
    $empID = $this->input->post('empID');
    $contract_id = ($this->contract_model->getContract($empID)->num_rows() > 0)
      ? $this->contract_model->getContract($empID)->row()->contract_id : "";
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      "contract_file" => $this->contract_model->getContract($empID),
      "contract_id" => $contract_id,
      "prevContract" => $this->contract_model->getPrevContract($empID),
      "sss" => $this->contract_model->getSSS(),
      "philhealth" => $this->contract_model->getPhilhealth(),
      "pagibig" => $this->contract_model->getPagIbig(),
      "tax" => $this->contract_model->getTax(),
      "paytype" => $this->contract_model->getPayType(),
      "empLvl" => $this->contract_model->getEmployeeLvl(),
      "workSite" => $this->contract_model->getWorkSite(),
      "position" => $this->contract_model->getPosition(),
      "emp_status" => $this->contract_model->get_emp_status(),
      "emp_leave" => $this->contract_model->get_emp_leave(),
      "pay_medium" => $this->contract_model->get_pay_medium(),
      "salary_cat" => $this->contract_model->getSalCat(),
      "emp" => $this->contract_model->getEmployee($empID)->row_array(),
      "companies" => $this->model->get_hris_companies(),
      "ex_rates" => $this->exchange_rates_model->get_exchange_rate(),
      "templates" => $this->contract_template_model->get_contract_template(),
      "contract_files" => $this->contract_model->get_contract_files($contract_id)
      // "emp_lvl" => $this->contract_model->getEmpLevel()->result_array(),
		);

    $this->load->view('includes/header', $data);
		$this->load->view('employees/contracts/contract', $data);
  }

  public function getprevcontract(){
    $previd = $this->input->post('previd');
    if(empty($previd)){
      $data = array("success" => 0, "message" => "Unable to get information about the previous contract");
      generate_json($data);
      exit();
    }

    $prevContract = $this->contract_model->getPrevContractFull($previd);
    if($prevContract->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get information about the previous contract");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "prevData" => $prevContract->row_array(), "pos_id" => $this->session->userdata('position_id'));
    generate_json($data);

  }

  public function getpossched(){
    $pos_id = $this->input->post('pos_id');
    if(empty($pos_id)){
      $data = array("success" => 0, "message" => "Unable to find default schedule for this position".$pos_id);
      generate_json($data);
      exit();
    }

    $posSched = $this->contract_model->getPositionSchedule($pos_id);
    if($posSched->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to find default schedule for this position");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "pos_sched" => $posSched->row_array());
    generate_json($data);
  }

  public function getSubDeptBy_deptId(){
		$deptId = $this->input->post('deptId');
		if($deptId == ""){
			$data = array("success" => 0, "message" => "Error encounter on getting sub department");
		}else{
			$subDept = $this->contract_model->getSubDeptBy_deptId($deptId);
			if($subDept->num_rows() > 0){
				$subDept = $subDept->result_array();
				$data = array("success" => 1, "message" => $subDept);
			}else{
				$data = array("success" => 0, "message" => "No available Sub Department.");
			}
		}
		generate_json($data);
	}

  public function getPosBy_subId(){
    $subDeptId = $this->input->post('subDeptId');
    if($subDeptId == ""){
      $data = array("success" => 0, "message" => "Error encounter on getting position.");
    }else{
      $position = $this->contract_model->getPosBy_subId($subDeptId);
      if($position->num_rows() > 0){
        $data = array("success" => 1, "message" => $position->result_array());
      }else{
        $data = array("success" => 0, "message" => "No available position");
      }
    }
    generate_json($data);
  }

  public function checkcontract(){
    $emp_id = $this->input->post('emp_id');
    $email = $this->contract_model->getEmployee($emp_id)->row()->email;
    if($this->contract_model->getContractByEmpID($emp_id)->num_rows() > 0){
      $cred = $this->contract_model->getContractByEmpID($emp_id)->row();
      $data = array("success" => 1, "username" => $cred->username, "password" => $cred->password, "email" => $cred->email);
    }else{
      $data = array("success" => 0, "email" => $email);
    }
    generate_json($data);
  }

  public function create(){
    $this->isLoggedIn();
    ### Contract Details ###
    $emp_id = (int)$this->input->post('emp_id');
    $contract = $this->contract_model->getContractByEmpID($emp_id);

    $emp_username = $this->input->post('emp_username');
    // $emp_password = $this->input->post('emp_password');
    if(!empty($emp_username)){
      if(!filter_var($emp_username, FILTER_VALIDATE_EMAIL)){
        $data = array("success" => 0, "message" => "Invalid email format. Please try again.");
        generate_json($data);
        exit();
      }
    }

    $cWorkSite = $this->input->post('cWorkSite');
    $cWorkSite = implode(',', $cWorkSite);
    $cPos = $this->input->post('cPos');
    $pos_access_lvl = $this->input->post('pos_access_lvl');
    $deptId = $this->input->post('deptId');
    $subDeptId = $this->input->post('subDeptId');
    $company = $this->input->post('new_company');
    $contract_type = $this->input->post('contract_type');
    $cStart = date('Y-m-d', strtotime($this->input->post('cStart')));
    $cEnd = date('Y-m-d', strtotime($this->input->post('cEnd')));
    $contractStatus = $this->input->post('contractStatus'); // employement status nato
    // $cEmpLvl = $this->input->post('cEmpLvl');
    // $cDesc = $this->input->post('cDesc');

    ### CHECK CONTRACT START AND END IF VALID ###
    if($cStart > $cEnd){
      $data = array("success" => 0, "message" => "Invalid Contract Date. Start Date cannot be greater than End Date.");
      generate_json($data);
      exit();
    }
    ### CHECK TOTAL SALARY ###
    if($this->input->post('total_sal') == 0){
      $data = array("success" => 0, "message" => "Please add Employee Salary.");
      generate_json($data);
      exit();
    }
    ### CHECK LEAVE ###
    // if($this->input->post('total_leave') == 0){
    //   $data = array("success" => 0, "message" => "Please add Employee Leave.");
    //   generate_json($data);
    //   exit();
    // }

    ### contract file upload ###
    // if(isset($_FILES['contract_info'])){
    //   $config['upload_path']       = 'assets/contract_file/';
    //   $config['allowed_types']     = 'docx|doc|pdf';
    //   $config['max_size']          = 10240;
    //   $config['encrypt_name']      = true;
    //
    //   $this->load->library('upload', $config);
    //
    //   if(!$this->upload->do_upload('contract_info')){
    //      $error = array('error' => $this->upload->display_errors());
    //   }else{
    //     $cdata = array('upload_data' => $this->upload->data());
    //     $cDesc = $config['upload_path'].$cdata['upload_data']['file_name'];
    //   }
    // }else{
    //   $cDesc = "";
    // }

    ### Work Schedule ###
    $workSched = array('mon' => array(), 'tue' => array(), 'wed' => array(), 'thu' => array(), 'fri' => array(), 'sat' => array(), 'sun' => array());
    $wSchedType = $this->input->post('wSchedType');
    $wSchedType2 = $this->input->post('wSchedType2');
    $wSchedPos = ($wSchedType2 == 'specific' || $wSchedType2 == "")? 0 : $this->input->post('wSchedPos');
    $day = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    $day2 = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
    $total_hours = 0;
    $total_break = 0;
    $total_day = 0;
    $worksched_error = 0;
    for ($i=0; $i < 7; $i++) {
      $timeStart = $this->input->post('timeStart'.$day[$i]);
      $timeEnd = $this->input->post('timeEnd'.$day[$i]);
      $timeTotal = $this->input->post('timeTotal'.$day[$i]);
      $breakStart = $this->input->post('breakStart'.$day[$i]);
      $breakEnd = $this->input->post('breakEnd'.$day[$i]);
      if($timeTotal != ""){
        $total_day += 1;
        $total_break += (converToTime($breakStart) > converToTime($breakEnd))
        ? (((converToTime($breakEnd) + 86400) - converToTime($breakStart)) / 3600)
        : ((converToTime($breakEnd) - converToTime($breakStart)) / 3600);
        $total_hours += $timeTotal;
      }

      if($timeStart == "" && $timeEnd == "" && $timeTotal == "" && $breakStart == "" && $breakEnd == ""){
        $worksched_error += 1;
      }

      if($worksched_error > 2){
        $data = array("success" => 0, "message" => "You need to put atleast 5 days of work schedule");
        generate_json($data);
        exit();
      }
      array_push($workSched[$day2[$i]],$timeStart, $timeEnd, $timeTotal, $breakStart, $breakEnd);
    }
    $total_hours = $total_hours / $total_day;
    $total_break = $total_break / $total_day;
    $worksched_settings = $this->contract_model->get_worksched_settigns()->row();
    if($total_hours > $worksched_settings->max_whours){
      $data = array("success" => 0, "message" => "Total working hours exceed 12 hours. Please try again.");
      generate_json($data);
      exit();
    }

    if($total_break > $worksched_settings->max_bhours){
      $data = array("success" => 0, "message" => "Break hours exceed the maximum limit for break. Please try again.");
      generate_json($data);
      exit();
    }

    $workSched_json = json_encode($workSched);

    ### Compensation Schedule ###
    $compSSS = $this->input->post('compSSS');
    $compPhilhealth = $this->input->post('compPhilhealth');
    $compPagIbig = $this->input->post('compPagIbig');
    $compTax = $this->input->post('compTax');
    $compPayType = $this->input->post('compPayType');
    $comp_pay_medium = $this->input->post('comp_pay_medium');

    $salCat = $this->input->post('salCat');
    $convert = json_decode($salCat);
    $base_pay = $convert[0]->amount_php;
    // $base_pay = (isset($convert[0]->amount_php)) ? $convert[0]->amount_php : $convert[0]->amount;

    $total_sal = $this->input->post('total_sal');
    $total_sal_converted = $this->input->post('total_sal_converted');
    $currency = $this->input->post('currency');
    $leave = $this->input->post('leave');
    $total_leave = $this->input->post('total_leave');
    $templates = 0;
    $template_id = 0;
    if(isset($_POST['templates']) && isset($_POST['template_id'])){
      $templates = $this->input->post('templates');
      $template_id = $this->input->post('template_id');
    }

    ### CHECK CONTRACT TEMPLATE ###
    if(count((array)$templates) == 0 || count((array)$template_id) == 0){
      $data = array("success" => 0, "message" => "Please add atleast 1 contract file.");
      generate_json($data);
      exit();
    }

    ### filter contract information ###
    if($contract->num_rows() > 0 && ($contract->row()->username != "" || $contract->row()->password != "")){
      if(
          empty($cWorkSite) ||
          empty($cPos) ||
          empty($pos_access_lvl) ||
          empty($deptId) ||
          empty($subDeptId) ||
          empty($cStart) ||
          empty($cEnd) ||
          empty($contractStatus) ||
          empty($compPayType) ||
          empty($comp_pay_medium) ||
          empty($company)
          // empty($cDesc) ||
          // empty($compSSS) ||
          // empty($compPhilhealth) ||
          // empty($compPagIbig) ||
          // empty($compTax) ||
        ){
          $data = array("success" => 0, "message" => "Please fill up all required fields");
          generate_json($data);
          exit();
        }
    }else{
      if(
          empty($emp_username) ||
          empty($cWorkSite) ||
          empty($cPos) ||
          empty($pos_access_lvl) ||
          empty($deptId) ||
          empty($subDeptId) ||
          empty($cStart) ||
          empty($cEnd) ||
          empty($contractStatus) ||
          empty($compPayType) ||
          empty($comp_pay_medium) ||
          empty($company)
          // empty($emp_password) ||
          // empty($cDesc) ||
          // empty($compSSS) ||
          // empty($compPhilhealth) ||
          // empty($compPagIbig) ||
          // empty($compTax) ||
        ){
          $data = array("success" => 0, "message" => "Please fill up all required fields");
          generate_json($data);
          exit();
        }
    }

    ### CHECK IF EMPLOYEE ID IS VALID ###
    if($this->contract_model->getEmployee($emp_id)->num_rows() == 0){
      $data = array("success" => 0, "message" => "Invalid employee!.");
      generate_json($data);
      exit();
    }
    ### CHECK IF EMAIL IS STILL AVAILABLE ###
    if($this->contract_model->getContractByEmail($emp_username)->num_rows() > 0){
      $data = array("success" => 0, "message" => "Username not available. Please try another.");
      generate_json($data);
      exit();
    }
    ### GENERATE CONTRACT REFERENCE NO. ###
    $cRef_no = generate_player_no();
    while($this->contract_model->getContractRefNo($cRef_no)->num_rows() > 0){
      $cRef_no = generate_player_no();
    }

    ### update previous contract ###
    if($contract->num_rows() > 0){
      $contract_id = $this->input->post('contract_id');
      $updated = $this->contract_model->update_all_prevContract($emp_id);
      $updated = $this->contract_model->updatePreviousContract($contract_id,true);
      if($updated == false){
        $data = array("success" => 0, "message" => "Contract creation failed. Please try again");
        generate_json($data);
        exit();
      }
    }
    ### insert work sched ###
    $emp = $this->contract_model->getEmployee($emp_id);
    if($emp->num_rows() == 0){
      $data = array("success" => 0, "message" => "No such employee exist");
      generate_json($data);
      exit();
    }
    $emp = $emp->row();
    $workSched_data = array(
      "pos_id" => $cPos,
      "emp_id" => $emp_id,
      "emp_idno" => $emp->employee_idno,
      "work_sched" => $workSched_json,
      "total_whours" => round($total_hours,2),
      "total_bhours" => round($total_break,2),
      "sched_type" => $wSchedType,
      "sched_type2" => $wSchedType2,
      "enabled" => 1
    );

    $work_sched_id = $this->contract_model->setWorkSchedule($workSched_data);
    if($work_sched_id === false){
      $data = array("success" => 0, "message" => "Unable to insert work schedule. Please try again.");
      generate_json($data);
      exit();
    }

    ### insert contract details ###
    $contractDetails_data = array(
      "contract_ref_no" => $cRef_no,
      "contract_emp_id" => $emp_id,
      "work_site_id" => $cWorkSite,
      "position_id" => $cPos,
      "position_access_lvl" => $pos_access_lvl,
      "company_id" => $company,
      "contract_start" => $cStart,
      "contract_end" => $cEnd,
      "emp_status" => $contractStatus,
      "payout_medium" => $comp_pay_medium,
      "contract_desc" => base_url(),
      "work_sched_id" => $work_sched_id,
      "sss" => $compSSS,
      "philhealth" => $compPhilhealth,
      "pagibig" => $compPagIbig,
      "tax" => $compTax,
      "paytype" => $compPayType,
      "sal_cat" => $salCat,
      "total_sal" => $total_sal,
      "total_sal_converted" => $total_sal_converted,
      "base_pay" => $base_pay,
      "currency" => $currency,
      "emp_leave" => $leave,
      "total_leave" => $total_leave,
      "contract_type" => $contract_type
    );
    $inserted = $this->contract_model->setContract($contractDetails_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to insert Contract Details. Please try again.");
      generate_json($data);
      exit();
    }

    ### CONTRACT FILE ###
    $insert_batch = array();
    if(count((array)$templates) > 0){
      for ($i=0; $i < count((array)$templates); $i++) {
        $insert_data = array(
          "employee_idno" => $emp->employee_idno,
          "contract_id" => $inserted,
          "template_id" => $template_id[$i],
          "content" => $templates[$i]
        );
        $insert_batch[] = $insert_data;
      }
    }
    $inserted_template = $this->contract_model->set_template_batch($insert_batch);
    if($inserted_template === false){
      $data = array("success" => 0, "message" => "Unable to save contract file");
      generate_json($data);
    }

    ### EMAIL RANDOM GENERATE PASSWORD ###
    $this->load->library('email');
    $emp_password = Generate_random_password();
    $email_data['username'] = $emp_username;
    $email_data['password'] = $emp_password;
    $email_data['fullname'] = $emp->last_name.', '.$emp->first_name.' '.$emp->middle_name;
    $msg = $this->load->view('emails/password_email',$email_data,true);

    $this->email->from('support@cloudpanda.ph', 'One Payroll');
    $this->email->to($emp_username);

    $this->email->subject('One Payroll Credentials');
    $this->email->message($msg);
    $email = $this->email->send();

    ### UPDATE POSITION ACCESS LEVEL ###
    $this->contract_model->update_hris_user_pos($contractDetails_data['position_access_lvl'],$deptId,$subDeptId,$contractDetails_data['contract_emp_id']);

    if($contract->num_rows() == 0 || $contract->row()->username == "" || $contract->row()->password == ""){
      ### update employee credentials ###
      $options = ['cost' => 12];
			$emp_password = password_hash($emp_password, PASSWORD_BCRYPT, $options);

      $credentials_data = array(
        "username" => $emp_username,
        "password" => $emp_password,
        "user_fname" => $emp->first_name,
        "user_mname" => $emp->middle_name,
        "user_lname" => $emp->last_name,
        "position_id" => $pos_access_lvl,
        "employee_idno" => $emp->employee_idno,
        "deptId" => $deptId,
        "subDeptId" => $subDeptId,
        "date_activated" => todaytime(),
        "date_created" => todaytime(),
        "date_updated" => todaytime(),
        "enabled" => 1
      );
      $inserted = $this->contract_model->set_user_credentials($credentials_data);
      if($inserted == false){
        $data = array("success" => 0, "message" => "Unable to create user credentials");
        generate_json($data);
        exit();
      }
      // $updated = $this->contract_model->updateEmployeeCredentials($credentials_data);
      // if($updated == false){
      //   $data = array("success" => 0, "message" => "Unable to update employee credentials. Please try again");
      //   generate_json($data);
      //   exit();
      // }
      ### change employee status to active ###
      $empStat_data = array(1, $emp_id);
      $updated = $this->contract_model->updateEmployeeStatus($empStat_data);
    }

    if($contract->num_rows() >= 1){
      $audit_data = array();
      $prev_contract = $contract->row_array();
      $active_contract = $this->contract_model->getContractByEmpID($emp_id)->row_array();

      foreach($prev_contract as $key => $value){
        foreach($active_contract as $key2 => $value2){
          if($key == $key2){
            if($key != 'id' && $key != 'contract_ref_no' && $key != 'total_sal'){
              if($value != $value2){
                $audit = array(
                  "contract_id" => $active_contract['id'],
                  "prev_contract_id" => $prev_contract['id'],
                  "employee_idno" => $this->session->emp_idno,
                  "audit_trail" => $value." to ".$value2,
                  "fields" => $key
                );
                $audit_data[] = $audit;
              }
            }
          }
        }
      }

      if(count((array)$audit_data) > 0){
        $this->contract_model->set_audit_trail_batch($audit_data);
      }
    }


    $data = array("success" => 1, "message" => "Contract successfully created.");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();
    ### Contract Details ###
    $edit_emp_id = (int)$this->input->post('edit_emp_id');
    $contract = $this->contract_model->getContractByEmpID($edit_emp_id);

    // $emp_username = $this->input->post('emp_username');
    // $emp_password = $this->input->post('emp_password');
    $current_cWorkSite = $this->input->post('current_cWorkSite');
    $current_cWorkSite = implode(',',$current_cWorkSite);
    $current_cPos = $this->input->post('current_cPos');
    $pos_access_lvl = $this->input->post('pos_access_lvl');
    $deptId = $this->input->post('deptId');
    $subDeptId = $this->input->post('subDeptId');
    $current_company = $this->input->post('current_company');
    $current_contract_type = $this->input->post('current_contract_type');
    // $cEmpLvl = $this->input->post('cEmpLvl');
    $current_cStart = date('Y-m-d', strtotime($this->input->post('current_cStart')));
    $current_cEnd = date('Y-m-d', strtotime($this->input->post('current_cEnd')));
    $current_contractStatus = $this->input->post('current_contractStatus'); // employement status nato
    // $cDesc = $this->input->post('cDesc');
    if($current_cStart > $current_cEnd){
      $data = array("success" => 0, "message" => "Invalid Contract Date. Start Date cannot be greater than End Date.");
      generate_json($data);
      exit();
    }

    if($this->input->post('edit_total_salary') == 0){
      $data = array("success" => 0, "message" => "Please add Employee Salary.");
      generate_json($data);
      exit();
    }

    // if($this->input->post('edit_total_leave') == 0){
    //   $data = array("success" => 0, "message" => "Please add Employee Leave.");
    //   generate_json($data);
    //   exit();
    // }

    ### contract file upload ###
    // if(isset($_FILES['edit_contract_info']) && $_FILES['edit_contract_info']['size'] > 0){
    //   $config['upload_path']       = 'assets/contract_file/';
    //   $config['allowed_types']     = 'docx|doc|pdf';
    //   $config['max_size']          = 10240;
    //   $config['encrypt_name']      = true;
    //
    //   $this->load->library('upload', $config);
    //
    //   if(!$this->upload->do_upload('edit_contract_info')){
    //      $error = array('error' => $this->upload->display_errors());
    //   }else{
    //     $cdata = array('upload_data' => $this->upload->data());
    //     $cDesc = $config['upload_path'].$cdata['upload_data']['file_name'];
    //   }
    // }else{
    //   $cDesc = $this->input->post('current_contract_desc');
    // }

    ### Work Schedule ###
    $workSched = array('mon' => array(), 'tue' => array(), 'wed' => array(), 'thu' => array(), 'fri' => array(), 'sat' => array(), 'sun' => array());
    $edit_wSchedType = $this->input->post('edit_wSchedType');
    // $edit_wSchedType = $this->input->post('edit_wSchedType');
    $wSchedPos = ($edit_wSchedType == 'specific' || $edit_wSchedType == "")? 0 : $this->input->post('wSchedPos');
    $day = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    $day2 = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
    $total_hours = 0;
    $total_break = 0;
    $total_day = 0;
    $worksched_error = 0;
    for ($i=0; $i < 7; $i++) {
      $timeStart = $this->input->post('current_timeStart'.$day[$i]);
      $timeEnd = $this->input->post('current_timeEnd'.$day[$i]);
      $timeTotal = $this->input->post('current_timeTotal'.$day[$i]);
      $breakStart = $this->input->post('current_breakStart'.$day[$i]);
      $breakEnd = $this->input->post('current_breakEnd'.$day[$i]);
      if($timeTotal != ""){
        $total_day += 1;
        $total_break += (converToTime($breakStart) > converToTime($breakEnd))
        ? (((converToTime($breakEnd) + 86400) - converToTime($breakStart)) / 3600)
        : ((converToTime($breakEnd) - converToTime($breakStart)) / 3600);;
        $total_hours += $timeTotal;
      }

      if($timeStart == "" && $timeEnd == "" && $timeTotal == "" && $breakStart == "" && $breakEnd == ""){
        $worksched_error += 1;
        // $data = array("success" => 0, "message" => $day[$i]);
        // generate_json($data);
        // exit();
      }

      if($worksched_error > 2){
        $data = array("success" => 0, "message" => "You need to put atleast 5 days of work schedule".$worksched_error);
        generate_json($data);
        exit();
      }
      array_push($workSched[$day2[$i]],$timeStart, $timeEnd, $timeTotal, $breakStart, $breakEnd);
    }
    $total_hours = $total_hours / $total_day;
    $total_break = $total_break / $total_day;
    $worksched_settings = $this->contract_model->get_worksched_settigns()->row();
    if($total_hours > $worksched_settings->max_whours){
      $data = array("success" => 0, "message" => "Total working hours exceed 12 hours. Please try again.");
      generate_json($data);
      exit();
    }

    if($total_break > $worksched_settings->max_bhours){
      $data = array("success" => 0, "message" => "Break hours exceed the maximum limit for break. Please try again.");
      generate_json($data);
      exit();
    }

    $workSched_json = json_encode($workSched);

    ### Compensation Schedule ###
    $edit_compSSS = $this->input->post('edit_compSSS');
    $edit_compPhilhealth = $this->input->post('edit_compPhilhealth');
    $edit_compPagIbig = $this->input->post('edit_compPagIbig');
    $edit_compTax = $this->input->post('edit_compTax');
    $edit_compPayType = $this->input->post('edit_compPayType');
    $edit_comp_pay_medium = $this->input->post('edit_comp_pay_medium');

    $salCat = $this->input->post('salCat');
    $convert = json_decode($salCat);
    $base_pay = (isset($convert[0]->amount_php)) ? $convert[0]->amount_php : $convert[0]->amount;

    $edit_total_salary = $this->input->post('edit_total_salary');
    $edit_total_sal_converted = $this->input->post('edit_total_sal_converted');
    $edit_currency = $this->input->post('edit_currency');
    $leave = $this->input->post('leave');
    $edit_total_leave = $this->input->post('edit_total_leave');
    $edit_contract_id = $this->input->post('edit_contract_id');

    $templates = 0;
    $template_id = 0;
    if(isset($_POST['templates']) && isset($_POST['template_id'])){
      $templates = $this->input->post('templates');
      $template_id = $this->input->post('template_id');
    }

    ### CHECK CONTRACT TEMPLATE ###
    if(count((array)$templates) == 0 || count((array)$template_id) == 0){
      $data = array("success" => 0, "message" => "Please add atleast 1 contract file.");
      generate_json($data);
      exit();
    }

    ### filter contract information ###
    if(
        empty($current_cWorkSite) ||
        empty($current_cPos) ||
        empty($pos_access_lvl) ||
        empty($deptId) ||
        empty($subDeptId) ||
        empty($current_cStart) ||
        empty($current_cEnd) ||
        empty($current_contractStatus) ||
        // empty($cDesc) ||
        // empty($edit_compSSS) ||
        // empty($edit_compPhilhealth) ||
        // empty($edit_compPagIbig) ||
        // empty($edit_compTax) ||
        empty($edit_compPayType) ||
        empty($edit_comp_pay_medium) ||
        empty($current_company)
      ){
        $data = array("success" => 0, "message" => "Please fill up all required fieldss");
        generate_json($data);
        exit();
      }

      if($this->contract_model->getEmployee($edit_emp_id)->num_rows() == 0){
        $data = array("success" => 0, "message" => "Invalid employee!.");
        generate_json($data);
        exit();
      }

      // if($this->contract_model->getContractByEmail($emp_username)->num_rows() > 0){
      //   $data = array("success" => 0, "message" => "Username not available. Please try another.");
      //   generate_json($data);
      //   exit();
      // }

      $cRef_no = generate_player_no();
      while($this->contract_model->getContractRefNo($cRef_no)->num_rows() > 0){
        $cRef_no = generate_player_no();
      }
      ### update previous contract ###
      if($contract->num_rows() > 0){
        // $edit_contract_id = $this->input->post('edit_contract_id');
        $updated = $this->contract_model->update_all_prevContract($edit_emp_id);
        $this->contract_model->updatePreviousContract($edit_contract_id,true);
        if($updated == false){
          $data = array("success" => 0, "message" => "Contract creation failed. Please try again");
          generate_json($data);
          exit();
        }
      }
      ### insert work sched ###
      $emp = $this->contract_model->getEmployee($edit_emp_id);
      if($emp->num_rows() == 0){
        $data = array("success" => 0, "message" => "No such employee exist");
        generate_json($data);
        exit();
      }
      $emp = $emp->row();
      $workSched_data = array(
        "pos_id" => $current_cPos,
        "emp_id" => $edit_emp_id,
        "emp_idno" => $emp->employee_idno,
        "work_sched" => $workSched_json,
        "total_whours" => round($total_hours,2),
        "total_bhours" => round($total_break,2),
        "sched_type" => $edit_wSchedType,
        "sched_type2" => 'specific',
        "enabled" => 1
      );
      $work_sched_id = $this->contract_model->setWorkSchedule($workSched_data);
      if(!$work_sched_id > 0){
        $data = array("success" => 0, "message" => "Unable to insert work schedule. Please try again.");
        generate_json($data);
        exit();
      }

      ### insert contract details ###
      $contractDetails_data = array(
        "contract_ref_no" => $cRef_no,
        "contract_emp_id" => $edit_emp_id,
        "work_site_id" => $current_cWorkSite,
        "position_id" => $current_cPos,
        "position_access_lvl" => $pos_access_lvl,
        "company_id" => $current_company,
        "contract_start" => $current_cStart,
        "contract_end" => $current_cEnd,
        "emp_status" => $current_contractStatus,
        "payout_medium" => $edit_comp_pay_medium,
        "contract_desc" => base_url(),
        "work_sched_id" => $work_sched_id,
        "sss" => $edit_compSSS,
        "philhealth" => $edit_compPhilhealth,
        "pagibig" => $edit_compPagIbig,
        "tax" => $edit_compTax,
        "paytype" => $edit_compPayType,
        "sal_cat" => $salCat,
        "total_sal" => $edit_total_salary,
        "total_sal_converted" => $edit_total_sal_converted,
        "base_pay" => $base_pay,
        "emp_leave" => $leave,
        "total_leave" => $edit_total_leave,
        "currency" => $edit_currency,
        "contract_type" => $current_contract_type
      );
      $inserted = $this->contract_model->setContract($contractDetails_data);
      if($inserted == false){
        $data = array("success" => 0, "message" => "Unable to insert Contract Details. Please try again.");
        generate_json($data);
        exit();
      }

      ### CONTRACT FILE ###
      $in = 0;
      $insert_batch = array();
      if(count((array)$templates) > 0){
        for ($i=0; $i < count((array)$templates); $i++) {
          $exist = $this->contract_model->get_contract_file_by_template_id($template_id[$i],$edit_contract_id);
          if($exist->num_rows() > 0){
            $this->contract_template_model->update_template_contract_id($edit_contract_id,$template_id[$i],$inserted,$templates[$i]);
          }else{
            $insert_data = array(
              "employee_idno" => $emp->employee_idno,
              "contract_id" => $inserted,
              "template_id" => $template_id[$i],
              "content" => $templates[$i]
            );
            $insert_batch[] = $insert_data;
            $in++;
          }
        }
      }

      if($in > 0){
        $inserted_template = $this->contract_model->set_template_batch($insert_batch);
        if($inserted_template === false){
          $data = array("success" => 0, "message" => "Unable to save contract file");
          generate_json($data);
        }
      }

      if($contract->num_rows() > 0){
        $prev_contract = $contract->row_array();
        $active_contract = $this->contract_model->getContractByEmpID($edit_emp_id)->row_array();

        $audit_data = array();
        foreach($prev_contract as $key => $value){
          foreach($active_contract as $key2 => $value2){
            if($key == $key2){
              if($key != 'id' && $key != 'contract_ref_no' && $key != 'total_sal'){
                if($value != $value2){
                  $audit = array(
                    "contract_id" => $active_contract['id'],
                    "prev_contract_id" => $prev_contract['id'],
                    "employee_idno" => $this->session->emp_idno,
                    "audit_trail" => $value." to ".$value2,
                    "fields" => $key
                  );
                  $audit_data[] = $audit;
                }
              }
            }
          }
        }

      }

      if(count((array)$audit_data) > 0){
        $this->contract_model->set_audit_trail_batch($audit_data);
      }

      $this->contract_model->update_hris_user_pos($contractDetails_data['position_access_lvl'],$deptId,$subDeptId,$contractDetails_data['contract_emp_id']);
      $data = array("success" => 1, "message" => "Contract successfully created.");
      generate_json($data);
  }

  public function create_template(){
    $employee_idno = $this->input->post('employee_idno');
    $employee = $this->model->get_employee($employee_idno)->row_array();
    $cWorkSite = $this->input->post('cWorkSite');
    $pos_access_lvl = $this->input->post('pos_access_lvl');
    $cWorkSite = implode(',', $cWorkSite);
    $cPos = $this->input->post('cPos');
    $pos_access_lvl = $this->input->post('pos_access_lvl');
    $deptId = $this->input->post('deptId');
    $subDeptId = $this->input->post('subDeptId');
    $company = $this->input->post('new_company');
    $contract_type = $this->input->post('contract_type');
    $cStart = date('Y-m-d', strtotime($this->input->post('cStart')));
    $cEnd = date('Y-m-d', strtotime($this->input->post('cEnd')));
    $contractStatus = $this->input->post('contractStatus'); // employement status nato

    $wSchedType = $this->input->post('wSchedType');
    $wSchedType2 = $this->input->post('wSchedType2');
    $wSchedPos = ($wSchedType2 == 'specific' || $wSchedType2 == "")? 0 : $this->input->post('wSchedPos');

    $workSched = array('mon' => array(), 'tue' => array(), 'wed' => array(), 'thu' => array(), 'fri' => array(), 'sat' => array(), 'sun' => array());
    $day = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    $day2 = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
    $total_hours = 0;
    $total_break = 0;
    $total_day = 0;
    $worksched_error = 0;
    for ($i=0; $i < 7; $i++) {
      $timeStart = $this->input->post('timeStart'.$day[$i]);
      $timeEnd = $this->input->post('timeEnd'.$day[$i]);
      $timeTotal = $this->input->post('timeTotal'.$day[$i]);
      $breakStart = $this->input->post('breakStart'.$day[$i]);
      $breakEnd = $this->input->post('breakEnd'.$day[$i]);
      if($timeTotal != ""){
        $total_day += 1;
        $total_break += ((converToTime($breakEnd) - converToTime($breakStart)) / 3600);
        $total_hours += $timeTotal;
      }

      if($timeStart == "" && $timeEnd == "" && $timeTotal == "" && $breakStart == "" && $breakEnd == ""){
        $worksched_error += 1;
      }

      if($worksched_error > 2){
        $data = array("success" => 0, "message" => "You need to put atleast 5 days of work schedule");
        generate_json($data);
        exit();
      }
      array_push($workSched[$day2[$i]],$timeStart, $timeEnd, $timeTotal, $breakStart, $breakEnd);
    }

    $workSched_json = json_encode($workSched);
    $compSSS = $this->input->post('compSSS');
    $compPhilhealth = $this->input->post('compPhilhealth');
    $compPagIbig = $this->input->post('compPagIbig');
    $compTax = $this->input->post('compTax');
    $compPayType = $this->input->post('compPayType');
    $comp_pay_medium = $this->input->post('comp_pay_medium');

    $salCat = $this->input->post('salCat');
    $convert = json_decode($salCat);
    $base_pay = $convert[0]->amount_php;

    $total_sal = $this->input->post('total_sal');
    $total_sal_converted = $this->input->post('total_sal_converted');
    $currency = $this->input->post('currency');
    $leave = $this->input->post('leave');
    $total_leave = $this->input->post('total_leave');
    $template = $this->input->post('template');

    $worksite = $this->model->get_worksite($cWorkSite)->row_array();
    $position = $this->model->get_position_by_id($cPos)->row_array();
    $position_access_level = $this->model->get_position_details_access($pos_access_lvl)->row_array();
    $companies = $this->model->get_company($company)->row_array();
    $empstatus = $this->model->get_emp_status($contractStatus)->row_array();
    $payout_medium = $this->model->get_payoutmedium($comp_pay_medium)->row_array();
    $paytype = $this->model->get_paytype($compPayType)->row_array();
    $contract_template = $this->contract_template_model->get_contract_template(false,$template)->row_array();

    $info = array(
      "first_name" => $employee['first_name'],
      "middle_name" => $employee['middle_name'],
      "last_name" => $employee['last_name'],
      "birthday" => $employee['birthday'],
      "marital_status" => $employee['marital_status'],
      "home_address1" => $employee['home_address1'],
      "home_address2" => $employee['home_address2'],
      "city" => $employee['city'],
      "country" => $employee['country'],
      "email" => $employee['email'],
      "sss_no" => $employee['sss_no'],
      "philhealth_no" => $employee['philhealth_no'],
      "pagibig_no" => $employee['pagibig_no'],
      "tin_no" => $employee['tin_no'],
      "contract_start" => $cStart,
      "contract_end" => $cEnd,
      "sal_cat" => $salCat,
      "base_pay" => $base_pay,
      "total_sal" => number_format($total_sal,2),
      "total_sal_converted" => number_format($total_sal_converted,2),
      "emp_leave" => $leave,
      "total_leave" => $total_leave,
      "worksite" => $worksite['description'],
      "position" => $position['description'],
      "pos_access_lvl" => $position_access_level['position'],
      "company_name" => $companies['company'],
      "work_schedule" => $workSched_json,
      "empstatus" => $empstatus['description'],
      "payout_medium" => $payout_medium['description'],
      "paytype" => $paytype['description'],
      "date_today" => fulldate(),
      "total_whours" => $total_hours - $total_break,
      "total_bhours" => $total_break,
      "total_whours2" => $total_hours,
      "manager_name" => $position['manager_name'],
      "manager_position" => $position['manager_position'],
      "hr_manager" => $position['hrmanager_name']
    );

    $data = array("success" => 1, "template_info" => $info, "template_format" => $contract_template['template_format'], "template_id" => $template);
    generate_json($data);
  }

  public function get_template_format(){
    $template = $this->input->post('template');
    if(empty($template)){
      $data = array("success" => 0, "message" => "Please select template format");
      generate_json($data);
      exit();
    }

    $contract_template = $this->contract_template_model->get_contract_template(false,$template);
    if($contract_template->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get template format");
      generate_json($data);
      exit();
    }

    $template = $contract_template->row_array();
    $data = array("success" => 1, "template_format" => $template['template_format']);
    generate_json($data);
  }

  public function get_template_format_with_contract(){
    $curr_template = $this->input->post('curr_template');
    $template_contract_id = $this->input->post('template_contract_id');

    if(empty($curr_template) || empty($template_contract_id)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $contract = $this->contract_model->get_contract_for_template($template_contract_id);
    $template = $this->contract_template_model->get_contract_template(false,$curr_template);
    if($contract->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get any contract details. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    if($template->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get template format. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $template = $template->row_array();
    $contract = $contract->row_array();
    $info = array(
      "first_name" => $contract['first_name'],
      "middle_name" => $contract['middle_name'],
      "last_name" => $contract['last_name'],
      "birthday" => $contract['birthday'],
      "marital_status" => $contract['marital_status'],
      "home_address1" => $contract['home_address1'],
      "home_address2" => $contract['home_address2'],
      "city" => $contract['city'],
      "country" => $contract['country'],
      "email" => $contract['email'],
      "sss_no" => $contract['sss_no'],
      "philhealth_no" => $contract['philhealth_no'],
      "pagibig_no" => $contract['pagibig_no'],
      "tin_no" => $contract['tin_no'],
      "contract_start" => $contract['contract_start'],
      "contract_end" => $contract['contract_end'],
      "sal_cat" => $contract['sal_cat'],
      "base_pay" => $contract['base_pay'],
      "total_sal" => number_format($contract['total_sal'],2),
      "total_sal_converted" => number_format($contract['total_sal_converted'],2),
      "emp_leave" => $contract['emp_leave'],
      "total_leave" => $contract['total_leave'],
      "worksite" => $contract['work_site'],
      "position" => $contract['position'],
      // "pos_access_lvl" => $position_access_level['position'],
      "company_name" => $contract['company'],
      "work_schedule" => $contract['work_sched'],
      "emp_status" => $contract['emp_status'],
      "payout_medium" => $contract['payout_medium'],
      "paytype" => $contract['paytype'],
      "date_today" => fulldate(),
      "total_whours" => $contract['total_whours'],
      "total_bhours" => $contract['total_bhours'],
      "total_whours2" => $contract['total_whours2'],
      "manager_name" => $contract['manager_name'],
      "manager_position" => $contract['manager_position'],
      "hr_manager" => $contract['hr_manager']
    );

    $data = array("success" => 1, "template_info" => $info, "template_format" => $template['template_format'], "template" => $template);
    generate_json($data);
  }

  public function delete_contract_file(){
    $delid = $this->input->post('delid');
    $edit_contract_id = $this->input->post('edit_contract_id');
    // echo 'id'.$delid;
    // echo '<br>';
    // echo 'cid'.$edit_contract_id;
    // die();
    if(empty($delid) || empty($edit_contract_id)){
      $data = array("success" => 0, "message" => "Unable to delete contract file. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $deleted = $this->contract_model->update_contract_file_status($delid,$edit_contract_id);
    if($deleted === false){
      $data = array("success" => 0, "message" => "Unaable to delete contract file. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Contract file deleted successfully");
    generate_json($data);
  }

}
