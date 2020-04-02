<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Applicant extends CI_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('applicants/applicant_model');
    $this->load->model('employees/employee_model');
    $this->load->model('settings/contract_template_model');
    // $this->isLoggedIn();
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
    $this->isLoggedIn();
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()

		);

    $this->load->view('includes/header', $data);
    $this->load->view('applicants/applicants', $data);
  }

  public function get_educ_level(){
		$educ = $this->employee_model->get_educ_level()->result();
		$data = array("educ" => $educ);
		generate_json($data);
	}

	public function get_relation(){
		$rel = $this->employee_model->get_relation()->result();
		$data = array("rel" => $rel);
		generate_json($data);
	}

  public function get_job_offer_template(){
    $this->isLoggedIn();

    $app_ref_no = $this->input->post('app_ref_no');
    $f_name = $this->input->post('f_name');
    $m_name = $this->input->post('m_name');
    $l_name = $this->input->post('l_name');
    $haddress_1 = $this->input->post('haddress_1');
    $haddress_2 = $this->input->post('haddress_2');
    $select_jo = en_dec('dec',$this->input->post('select_jo'));

    if(empty($app_ref_no)){
      $data = array("success" => 0, "message" => "Unable to verify applicant id");
      generate_json($data);
      exit();
    }

    $job_offer = $this->applicant_model->get_job_offer_template($select_jo);
    if($job_offer->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get job offer template. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $status = $this->applicant_model->getApplicantByIdNo($app_ref_no)->row()->app_status;
    if($status != 'job_offer'){
      $data = array("success" => 0, "message" => "Oops! This applicant is still not available for job offer. Please check applicant status then try again.");
      generate_json($data);
      exit();
    }

    $info = array(
      "first_name" => $f_name,
      "middle_name" => $m_name,
      "last_name" => $l_name,
      "home_address1" => $haddress_1,
      "home_address2" => $haddress_2,
      "date_today" => fulldate()
    );

    $job_offer = $job_offer->row_array();
    $data = array("success" => 1, "template" => $job_offer, "info" => $info);
    generate_json($data);
  }

  public function applicant_json(){
    $search = $this->input->post('searchValue');
    $data = $this->applicant_model->get_appTable_json($search);
    echo json_encode($data);
  }

  public function gen_link(){

    $this->isLoggedIn();

    ### generate token ###
    $token = generate_player_no();
    while($this->applicant_model->get_appToken($token)->num_rows() > 0){
      $token = generate_player_no();
    }

    ### encrypt token and create link ###
    $link = en_dec('en', $token);
    $company_code = $this->session->company_code;
    $href = base_url('applicants/Applicant/application_form/'.$link.'/'.$company_code);

    ### save link ###
    // $link_data = array(
    //   "app_link" => $href,
    //   "app_token" => $token
    // );
    // $this->applicant_model->set_appLink($link_data);
    $data = array("href" => $href, "token" => $token);

    generate_json($data);
  }

  public function copy_link(){
    $href = $this->input->post('href');
    $token = $this->input->post('token');

    if($href == "" || $token == ""){
      $data = array("success" => 0, "message" => "Unable to copy the link. Please try again");
      generate_json($data);
      exit();
    }

    $link_data = array(
      "app_link" => $href,
      "app_token" => $token,
      "app_status" => 0
    );
    $this->applicant_model->set_appLink($link_data);
    $data = array("success" => 1);
    generate_json($data);
  }

  public function application_form($link_token,$company_code){

    // $this->isLoggedIn();
    if(empty($company_code)){
      $this->logout();
      exit();
    }

    $token_dec = en_dec("dec", $link_token);
    $bcode = en_dec('dec', $company_code);
    // CHECK COMPANY CODE
    if(!empty($bcode) && !isset($this->session->database_name)){
			$this->load->model('branch/branch_model');
			$dbname = $this->branch_model->get_hris_branch($bcode,'branch_code');
			if($dbname->num_rows() == 0){
				$data = array("success" => 0, "facial_recog" => 0, "message" => "No available data");
				generate_json($data);
				exit();
			}

			$this->session->set_userdata('database_name', $dbname->row()->database_name);
			$this->session->set_userdata('branch_name', $dbname->row()->branch_name);
			$this->session->set_userdata('timezone', $dbname->row()->timezone);
			$this->session->set_userdata('location',$dbname->row()->location);
			$this->db = switch_database($this->session->database_name);

			// $data = array("success" => 1);
			// generate_json($data);
			// exit();
		}

    $token_data = $this->applicant_model->get_appToken($token_dec);
    ### check if link exist ###
    if(!$token_data->num_rows() > 0){
      $this->logout();
      exit();
    }

    $this->applicant_model->update_link_date($token_dec);
    $link = $token_data->row();

    ### check if link still active ###
    if($link->app_status == 1){ // 1 for already use 0 for still not use
      $this->logout();
      exit();
    }
    ### check if link is expired ###
    if($link->app_status == 0){
      $created_date = strtotime($link->app_created_at);
      $updated_date = strtotime($link->app_updated_at);

      $time_diff = $updated_date - $created_date;
      if($time_diff > 86400){
        $this->logout();
        exit();
      }
    }else{
      $data = array(1,$token_dec);
      $this->applicant_model->update_app_status($data);
    }
    $data['token_dec'] = $token_dec;
    $this->load->view('applicants/applicant_form',$data);
  }

  public function update_link(){
    $token_dec = $this->input->post('token_dec');
    $token_data = array(1,$token_dec);
    $this->applicant_model->update_app_status($token_data);
    $data = array("success" => 1);
    generate_json($data);
  }

  public function update_applicant_status(){
    $this->isLoggedIn();

    $id = en_dec('dec',$this->input->post('id'));
    $action = $this->input->post('action');

    if(empty($id) || empty($action)){
      $data = array("success" => 0, "message" => "Unable to do any action . Please try again.");
      generate_json($data);
      exit();
    }

    $updated = $this->applicant_model->update_applicant_status($id,$action);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update applicant status");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Applicant Status Updated .");
    generate_json($data);

  }

  public function applicant_pass_interview(){
    $this->isLoggedIn();

    $app_ref_no = $this->input->post('app_ref_no');
    $summernote = $this->input->post('summernote');

    if(empty($app_ref_no) || empty($summernote)){
      $data = array("success" => 0, "message" => "Unable to change applicant status");
      generate_json($data);
      exit();
    }

    $insert_data = array(
      "app_ref_no" => $app_ref_no,
      "interviewer" => $this->session->emp_idno,
      "interview_notes" => $summernote
    );

    $inserted = $this->applicant_model->set_interview($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save interview notes. Please try again.");
      generate_json($data);
      exit();
    }

    $updated = $this->applicant_model->update_applicant_status2($app_ref_no,'job_offer');
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update applicant status".$app_ref_no);
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Applicant interview pass.");
    generate_json($data);
  }

  public function applicant_accept_jo(){
    $this->isLoggedIn();

    $app_ref_no = $this->input->post('app_ref_no');
    $job_offer = $this->input->post('job_offer');

    if(empty($app_ref_no) || empty($job_offer)){
      $data = array("success" => 0, "message" => "Unable to change applicant status");
      generate_json($data);
      exit();
    }

    $status = $this->applicant_model->getApplicantByIdNo($app_ref_no)->row()->app_status;
    if($status != 'job_offer'){
      $data = array("success" => 0, "message" => "Oops! This applicant is still not available for job offer. Please check applicant status then try again.");
      generate_json($data);
      exit();
    }

    $job_offer_data = array(
      "app_ref_no" => $app_ref_no,
      "content" => $job_offer
    );

    $inserted = $this->applicant_model->set_job_offer($job_offer_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save job offer contract. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $accept_jo = $this->applicant_model->update_applicant_status2($app_ref_no,'requirements');
    if($accept_jo === false){
      $data = array("success" => 1, "message" => "Unable to change applicant status. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "This applicant is now available for requirements.");
    generate_json($data);
  }

  public function educationjson() {
		$app_ref_no = $this->input->post('app_ref_no');
		$data = $this->applicant_model->getEducations($app_ref_no)->result();
		echo json_encode($data);
	}

  public function workhisjson() {
		$app_ref_no = $this->input->post('app_ref_no');
		$data = $this->applicant_model->getWorkHistory($app_ref_no)->result();
		echo json_encode($data);
	}

  public function dependentsjson() {
		$app_ref_no = $this->input->post('app_ref_no');
		$data = $this->applicant_model->getDependents($app_ref_no)->result();
		echo json_encode($data);
	}

  public function approve(){
    $this->isLoggedIn();

    $appId = $this->input->post('appId');

    if($appId == ""){
      $data = array("success" => 0, "message" => "Approve Error. Please try again.");
      generate_json($data);
      exit();
    }

    $app = $this->applicant_model->getApplicantById(array($appId));
    if($app->num_rows() == 0){
      $data = array("success" => 0, "message" => "Approve Error. Please try again.");
      generate_json($data);
      exit();
    }

    $app = $app->row();
    $app_education = $this->applicant_model->getEducations($app->app_ref_no)->result_array();
    $app_workHistory = $this->applicant_model->getWorkHistory($app->app_ref_no)->result_array();
    $app_dependents = $this->applicant_model->getDependents($app->app_ref_no)->result_array();

    $employeeIdno = generate_player_no();
    while($this->employee_model->getEmployeeByIdNo($employeeIdno)->num_rows > 0){
      $employeeIdNo = generate_player_no();
    }
    $employeeIdNo = $employeeIdno;
    $app_ref_no = $app->app_ref_no;
    $firstName = $app->app_fname;
    $middleName = $app->app_mname;
    $lastName = $app->app_lname;
    $birthday = $app->app_birthday;
    $gender = $app->app_gender;
    $maritalStatus = $app->app_marital_status;
    $homeAddress1 = $app->app_home_add1;
    $homeAddress2 = $app->app_home_add2;
    $contactNo = $app->app_contact_no;
    $email = $app->app_email;
    $isActive = 0;
    $educations = $app_education;
    $workHistory = $app_workHistory;
    $dependents = $app_dependents;

    ### compensation ###
    $sss_no = $app->app_sss_no;
    $philhealth_no = $app->app_philhealth_no;
    $pagibig_no = $app->app_pagibig_no;
    $tin_no = $app->app_tin_no;



    if($employeeIdNo == "" || $firstName == "" || $lastName == "" || $birthday == "" || $gender == "" ||
		 		$maritalStatus == "" || $homeAddress1 == "" || $contactNo == "" || $email == ""){
			$data = array("success" => 0, "message" => "Please fill up all the required fields.");
			generate_json($data);
			exit();
		}
		$employeeIdNoData = array($employeeIdNo);
		if($this->model->employee_model->getEmployeeByIdNo($employeeIdNoData)->num_rows() > 0){
			$data = array("success" => 0, "message" => "Employee ID No. already exists");
			generate_json($data);
			exit();
		}
		$nameData = array($firstName, $lastName);
		if($this->model->employee_model->getEmployeeByFnameLname($nameData)->num_rows() > 0){
			$data = array("success" => 0, "message" => "Name already belong to another employee");
			generate_json($data);
			exit();
		}

		$emailData = array($email);
		if($this->model->employee_model->getEmployeeByEmail($emailData)->num_rows() > 0){
			$data = array("success" => 0, "message" => "Email is already used by another employee");
			generate_json($data);
			exit();
		}

		$dependentsError = 0;
		// dependents
		foreach($dependents as $d) {
			$dependentsFname = $d['first_name'];
		 	$dependentsMname = $d['middle_name'];
		 	$dependentsLname = $d['last_name'];
		 	$dependentsBday = $d['birthday'];
		 	$dependentsRelationship = $d['relationship'];
		 	$dependentsContactNo = $d['contact_no'];

			if($dependentsFname == "" || $dependentsLname == "" || $dependentsContactNo == ""){
				$dependentsError = 1;
			}

			if($dependentsError == 0){
				$data = array(
					$employeeIdNo,
					$dependentsFname,
					$dependentsMname,
					$dependentsLname,
					$dependentsBday,
					$dependentsRelationship,
					$dependentsContactNo,
					1
				);

				$this->employee_model->setDependents($data);
			}
		}

		$educError = 0;
		//education
		foreach($educations as $e) {
			$educationYear = $e['year_from']."-".$e['year_to'];
			$educationSchool = $e['school'];
			$educationCourse = $e['course'];
			$educationLevel = $e['level'];

			$date = preg_split ("/[\-]+/", $educationYear);
			$fromDate1 = $date[0];
			$fromDate2 = $date[1];
			$fromDate3 = $date[2];
			$toDate1 = $date[3];
			$toDate2 = $date[4];
			$toDate3 = $date[5];

			$fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
			$toDate = $toDate1."-".$toDate2."-".$toDate3;

			if($educationYear == "" || $educationSchool == "" || $educationLevel == ""){
				$educError = 1;
			}

			if($educError == 0){
				$data = array(
					$employeeIdNo,
					$fromDate,
					$toDate,
					$educationSchool,
					$educationCourse,
					$educationLevel,
					1
				);

				$this->employee_model->setEducation($data);
			}
		}

		$workHistoryError = 0;
		// work history
		foreach($workHistory as $w) {
			$workHistoryYear = $w['year_from']."-".$w['year_to'];
			$workHistoryStay = $w['stay'];
			$workHistoryCompany = $w['company_name'];
			$workHistoryPosition = $w['position'];
			$workHistoryLevel = $w['level'];
			$workHistoryContact = $w['contact_no'];
			$workHistoryResponsibility = $w['responsibility'];

			$date = preg_split ("/[\-]+/", $workHistoryYear);
			$fromDate1 = $date[0];
			$fromDate2 = $date[1];
			$fromDate3 = $date[2];
			$toDate1 = $date[3];
			$toDate2 = $date[4];
			$toDate3 = $date[5];

			$fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
			$toDate = $toDate1."-".$toDate2."-".$toDate3;

			if($workHistoryYear == "" || $workHistoryStay == "" || $workHistoryCompany == "" || $workHistoryPosition == "" ||
				$workHistoryLevel == "" || $workHistoryResponsibility == ""){
				$workHistoryError = 1;
			}

			if($workHistoryError == 0){
				$data = array(
					$employeeIdNo,
					$fromDate,
					$toDate,
					$workHistoryStay,
					$workHistoryCompany,
					$workHistoryPosition,
					$workHistoryLevel,
					$workHistoryContact,
					$workHistoryResponsibility,
					1
				);

				$this->employee_model->setWorkHistory($data);
			}
		}


		if($educError == 0 && $dependentsError == 0 && $workHistoryError == 0){

      $data = array(
        "employee_idno" => $employeeIdNo,
        "app_ref_no" => $app_ref_no,
        "first_name" => $firstName,
        "middle_name" => $middleName,
        "last_name" => $lastName,
        "birthday" => $birthday,
        "gender" => $gender,
        "marital_status" => $maritalStatus,
        "home_address1" => $homeAddress1,
        "home_address2" => $homeAddress2,
        "contact_no" => $contactNo,
        "email" => $email,
        "sss_no" => $sss_no,
        "philhealth_no" => $philhealth_no,
        "pagibig_no" => $pagibig_no,
        "tin_no" => $tin_no,
        "isActive" => $isActive,
        "enabled" => 1
      );

			// $this->employee_model->create($data);
      $this->applicant_model->create_employee($data);
      $this->applicant_model->destroy(array(0,$appId));
			$data = array('success' => 1, 'message' => 'Successfully Added');
		}else{
			$data = array(
				'success' => 0,
				'message' => 'Please make sure all the required fields are fill up properly',
				'educError' => $educError,
				'dependentsError' => $dependentsError,
				'workHistoryError' => $workHistoryError
			);
		}
		generate_json($data);
  }

  public function create() {
    
    $applicantIdNo = str_replace(' ', '', strtoupper($this->input->post('applicantIdNo')));
    $firstName = $this->input->post('firstName');
    $middleName = $this->input->post('middleName');
    $lastName = $this->input->post('lastName');
    $birthday = $this->input->post('birthday');
    $gender = $this->input->post('gender');
    $maritalStatus = $this->input->post('maritalStatus');
    $homeAddress1 = $this->input->post('homeAddress1');
    $homeAddress2 = $this->input->post('homeAddress2');
    $contactNo = $this->input->post('contactNo');
    $email = $this->input->post('email');
    $isActive = $this->input->post('isActive');
    $educations = $this->input->post('educations');
    $workHistory = $this->input->post('workHistory');
    $dependents = $this->input->post('dependents');

    ### compensation ###
    $sss_no = $this->input->post('sss_no');
    $philhealth_no = $this->input->post('philhealth_no');
    $pagibig_no = $this->input->post('pagibig_no');
    $tin_no = $this->input->post('tin_no');

		if($applicantIdNo == "" || $firstName == "" || $lastName == "" || $birthday == "" || $gender == "" ||
		 		$maritalStatus == "" || $homeAddress1 == "" || $contactNo == "" || $email == ""){
			$data = array("success" => 0, "message" => "Please fill up all the required fields.");
			generate_json($data);
			exit();
		}
		$applicantIdNoData = array($applicantIdNo);
		if($this->model->applicant_model->getApplicantByIdNo($applicantIdNoData)->num_rows() > 0){
			$data = array("success" => 0, "message" => "Applicant Ref No. already exists");
			generate_json($data);
			exit();
		}

		$nameData = array($firstName, $lastName);
		if($this->model->applicant_model->getApplicantByFnameLname($nameData)->num_rows() > 0){
			$data = array("success" => 0, "message" => "Name already belong to another applicant");
			generate_json($data);
			exit();
		}

		$emailData = array($email);
		if($this->model->applicant_model->getApplicantByEmail($emailData)->num_rows() > 0){
			$data = array("success" => 0, "message" => "Email is already used by another applicant");
			generate_json($data);
			exit();
		}

		// dependents
    $dependentsError = 0;
		for($x = 1; $x < count((array)$dependents); $x++) {
			$dependentsFname = $dependents[$x][0];
		 	$dependentsMname = $dependents[$x][1];
		 	$dependentsLname = $dependents[$x][2];
		 	$dependentsBday = $dependents[$x][3];
		 	$dependentsRelationship = $dependents[$x][4];
		 	$dependentsContactNo = $dependents[$x][5];

			if($dependentsFname == "" || $dependentsLname == "" || $dependentsContactNo == ""){
				$dependentsError = 1;
			}

			if($dependentsError == 0){
				$data = array(
					$applicantIdNo,
					$dependentsFname,
					$dependentsMname,
					$dependentsLname,
					$dependentsBday,
					$dependentsRelationship,
					$dependentsContactNo,
					1
				);

				$this->applicant_model->setDependents($data);
			}
		}

		//education
    $educError = 0;
		for($x = 1; $x < count((array)$educations); $x++) {
			// $educationYear = $educations[$x][0];
      $fromDate = $educations[$x][0];
      $toDate = $educations[$x][1];
			$educationSchool = $educations[$x][2];
			$educationCourse = $educations[$x][3];
			$educationLevel = $educations[$x][4];

			// $date = preg_split ("/[\-]+/", $educationYear);
			// $fromDate1 = $date[0];
			// $fromDate2 = $date[1];
			// $fromDate3 = $date[2];
			// $toDate1 = $date[3];
			// $toDate2 = $date[4];
			// $toDate3 = $date[5];
      //
			// $fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
			// $toDate = $toDate1."-".$toDate2."-".$toDate3;

			if($fromDate == "" || $toDate == "" || $educationSchool == "" || $educationLevel == ""){
				$educError = 1;
			}

			if($educError == 0){
				$data = array(
					$applicantIdNo,
					$fromDate,
					$toDate,
					$educationSchool,
					$educationCourse,
					$educationLevel,
					1
				);

				$this->applicant_model->setEducation($data);
			}
		}

		// work history
    $workHistoryError = 0;
		for($x = 1; $x < count((array)$workHistory); $x++) {
			// $workHistoryYear = $workHistory[$x][0];
      $fromDate = $workHistory[$x][0];
      $toDate = $workHistory[$x][1];
			$workHistoryStay = $workHistory[$x][2];
			$workHistoryCompany = $workHistory[$x][3];
			$workHistoryPosition = $workHistory[$x][4];
			$workHistoryLevel = $workHistory[$x][5];
			$workHistoryContact = $workHistory[$x][6];
			$workHistoryResponsibility = $workHistory[$x][7];

			// $date = preg_split ("/[\-]+/", $workHistoryYear);
			// $fromDate1 = $date[0];
			// $fromDate2 = $date[1];
			// $fromDate3 = $date[2];
			// $toDate1 = $date[3];
			// $toDate2 = $date[4];
			// $toDate3 = $date[5];
      //
			// $fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
			// $toDate = $toDate1."-".$toDate2."-".$toDate3;

			if($fromDate == "" || $toDate == "" || $workHistoryStay == "" || $workHistoryCompany == "" || $workHistoryPosition == "" ||
				$workHistoryLevel == "" || $workHistoryResponsibility == ""){
				$workHistoryError = 1;
			}

			if($workHistoryError == 0){
				$data = array(
					$applicantIdNo,
					$fromDate,
					$toDate,
					$workHistoryStay,
					$workHistoryCompany,
					$workHistoryPosition,
					$workHistoryLevel,
					$workHistoryContact,
					$workHistoryResponsibility,
					1
				);

				$this->applicant_model->setWorkHistory($data);
			}
		}

		if($educError == 0 && $dependentsError == 0 && $workHistoryError == 0){
			$data = array(
				"app_ref_no" => $applicantIdNo,
				"app_fname" => $firstName,
				"app_mname" => $middleName,
				"app_lname" => $lastName,
				"app_birthday" => $birthday,
				"app_gender" => $gender,
				"app_marital_status" => $maritalStatus,
				"app_home_add1" => $homeAddress1,
				"app_home_add2" => $homeAddress2,
				"app_contact_no" => $contactNo,
				"app_email" => $email,
        "app_sss_no" => $sss_no,
        "app_philhealth_no" => $philhealth_no,
        "app_pagibig_no" => $pagibig_no,
        "app_tin_no" => $tin_no,
				"app_isActive" => 1,
        "app_status" => 'interview',
				"app_enabled" => 1
			);

			$this->applicant_model->create($data);
			$data = array('success' => 1, 'message' => 'Successfully Added');
		}else{
			$data = array(
				'success' => 0,
				'message' => 'Please make sure all the required fields are fill up properly',
				'educError' => $educError,
				'dependentsError' => $dependentsError,
				'workHistoryError' => $workHistoryError
			);
		}
		generate_json($data);
 	}

  public function add($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('applicants/applicant-add', $data);
	}

  public function edit($token = ""){
    $appId = $this->input->post('appId');
    $appRefNo = $this->input->post('appRefNo');
    $data = array($appId);

    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'appId' => $appId,
			'applicant' => $this->applicant_model->getApplicantById($data)->row(),
			'educations' => $this->applicant_model->getEducations($appRefNo)->result(),
			'workHistory' => $this->applicant_model->getWorkHistory($appRefNo)->result(),
			'dependents' => $this->applicant_model->getDependents($appRefNo)->result(),
      'educ_level' => $this->employee_model->get_educ_level(),
      'relation' => $this->employee_model->get_relation(),
      "requirements" => $this->applicant_model->get_requirements($appRefNo)->result_array(),
      "job_offer" => $this->applicant_model->get_job_offer($appRefNo),
      "interview" => $this->applicant_model->get_interview($appRefNo),
      'app_ref_no' => $appRefNo,
      'job_offers' => $this->applicant_model->get_job_offer_template()
		);

    $this->load->view('includes/header', $data);
		$this->load->view('applicants/applicants-edit', $data);
  }

  public function update() {

		$appId = $this->input->post('appId');
		$editEmployeeNo = $this->input->post('editEmployeeNo');
		$editFirstName = $this->input->post('editFirstName');
		$editMiddleName = $this->input->post('editMiddleName');
		$editLastName = $this->input->post('editLastName');
		$editBirthday = $this->input->post('editBirthday');
		$editGender = $this->input->post('editGender');
		$editMaritalStatus = $this->input->post('editMaritalStatus');
		$editHomeAddress1 = $this->input->post('editHomeAddress1');
		$editHomeAddress2 = $this->input->post('editHomeAddress2');
		$city = $this->input->post('city');
		$country = $this->input->post('country');
		$editContactNo = $this->input->post('editContactNo');
		$editEmail = $this->input->post('editEmail');

    ### compensation ###
    $edit_sss_no = $this->input->post('edit_sss_no');
    $edit_philhealth_no = $this->input->post('edit_philhealth_no');
    $edit_pagibig_no = $this->input->post('edit_pagibig_no');
    $edit_tin_no = $this->input->post('edit_tin_no');

		$data = array(
			$editEmployeeNo,
			$editFirstName,
			$editMiddleName,
			$editLastName,
			$editBirthday,
			$editGender,
			$editMaritalStatus,
			$editHomeAddress1,
			$editHomeAddress2,
			$city,
			$country,
			$editContactNo,
			$editEmail,
      $edit_sss_no,
      $edit_philhealth_no,
      $edit_pagibig_no,
      $edit_tin_no,
			1,
			$appId
		);

		$this->applicant_model->updateApplicantRecord($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);

  }

  public function updateeducation() {

		$id = $this->input->post('id');
		// $educYear = $this->input->post('educYear');
    $educYearFrom = $this->input->post('educYearFrom');
    $educYearTo = $this->input->post('educYearTo');
		$educSchool = $this->input->post('educSchool');
		$educCourse = $this->input->post('educCourse');
		$educLevel = $this->input->post('educLevel');

		// $date = preg_split ("/[\-]+/", $educYear);
		// $fromDate1 = $date[0];
		// $fromDate2 = $date[1];
		// $fromDate3 = $date[2];
		// $toDate1 = $date[3];
		// $toDate2 = $date[4];
		// $toDate3 = $date[5];
    //
		// $fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
		// $toDate = $toDate1."-".$toDate2."-".$toDate3;


		$data = array(
			$educYearFrom,
			$educYearTo,
			$educSchool,
			$educCourse,
			$educLevel,
			$id
		);

		$this->applicant_model->updateApplicantEducation($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);
	}

  public function updateworkhistory() {

		$id = $this->input->post('id');
		// $workYear = $this->input->post('workYear');
    $workYearFrom = $this->input->post('workYearFrom');
    $workYearTo = $this->input->post('workYearTo');
		$workStay = $this->input->post('workStay');
		$workCompany = $this->input->post('workCompany');
		$workPosition = $this->input->post('workPosition');
		$workLevel = $this->input->post('workLevel');
		$workContact = $this->input->post('workContact');
		$workResp = $this->input->post('workResp');

		// $date = preg_split ("/[\-]+/", $workYear);
		// $fromDate1 = $date[0];
		// $fromDate2 = $date[1];
		// $fromDate3 = $date[2];
		// $toDate1 = $date[3];
		// $toDate2 = $date[4];
		// $toDate3 = $date[5];
    //
		// $fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
		// $toDate = $toDate1."-".$toDate2."-".$toDate3;

		$data = array(
			$workYearFrom,
			$workYearTo,
			$workStay,
			$workCompany,
			$workPosition,
			$workLevel,
			$workContact,
			$workResp,
			$id,
		);

		$this->applicant_model->updateApplicantWorkHistory($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);

	}

  public function updatedependents() {

		$id = $this->input->post('id');
		$firstName = $this->input->post('firstName');
		$middleName = $this->input->post('middleName');
		$lastName = $this->input->post('lastName');
		$bday = $this->input->post('bday');
		$relationship = $this->input->post('relationship');
		$contactNo = $this->input->post('contactNo');

		$data = array(
			$firstName,
			$middleName,
			$lastName,
			$bday,
			$relationship,
			$contactNo,
			$id
		);

		$this->applicant_model->updateApplicantDependents($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);

	}

  public function update_requirements(){
    ### UPLOAD REQUIREMENT FILES ###
    if(isset($this->session->database_name) && !is_dir("./assets/requirements/".en_dec('dec',$this->session->company_code)."/")) {
			mkdir("./assets/requirements/".en_dec('dec',$this->session->company_code)."/");
		}

    $app_ref_no = $this->input->post('editEmployeeIdNo');
    $requirements = array();
    $config['upload_path']       = "assets/requirements/".en_dec('dec',$this->session->company_code)."/";
    $config['max_size']          = 10240;
    $config['encrypt_name']      = true;
    $this->load->library('upload', $config);

    ### resume ###
    $resume = false;
    if(isset($_FILES['resume']) && $_FILES['resume'] > 0){
      $config['allowed_types']     = 'docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('resume')){
         $error = array('error' => $this->upload->display_errors());
         // print_r($error);
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $resume = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $resume,
          "req_type" => 'resume'
        );
      }
    }

    ### _2x2_pic ###
    $_2x2_pic = false;
    if(isset($_FILES['_2x2_pic']) && $_FILES['_2x2_pic'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('_2x2_pic')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $_2x2_pic = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $_2x2_pic,
          "req_type" => 'two_by_two_pic'
        );
      }
    }

    ### college_diploma ###
    $college_diploma = false;
    if(isset($_FILES['college_diploma']) && $_FILES['college_diploma'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('college_diploma')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $college_diploma = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $college_diploma,
          "req_type" => 'college_diploma'
        );
      }
    }

    ### trans_record ###
    $trans_record = false;
    if(isset($_FILES['trans_record']) && $_FILES['trans_record'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('trans_record')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $trans_record = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $trans_record,
          "req_type" => 'tor'
        );
      }
    }

    ### _2x2_valid_id ###
    $_2x2_valid_id = false;
    if(isset($_FILES['_2x2_valid_id']) && $_FILES['_2x2_valid_id'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('_2x2_valid_id')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $_2x2_valid_id = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $_2x2_valid_id,
          "req_type" => 'two_valid_id'
        );
      }
    }

    ### tin ###
    $tin = false;
    if(isset($_FILES['tin']) && $_FILES['tin'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('tin')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $tin = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $tin,
          "req_type" => 'tin'
        );
      }
    }

    ### sss_e1_form ###
    $sss_e1_form = false;
    if(isset($_FILES['sss_e1_form']) && $_FILES['sss_e1_form'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('sss_e1_form')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $sss_e1_form = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $sss_e1_form,
          "req_type" => 'sss_e1_form'
        );
      }
    }

    ### philhealth_req ###
    $philhealth_req = false;
    if(isset($_FILES['philhealth_req']) && $_FILES['philhealth_req'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('philhealth_req')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $philhealth_req = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $philhealth_req,
          "req_type" => 'philhealth_no'
        );
      }
    }

    ### pagibig_req ###
    $pagibig_req = false;
    if(isset($_FILES['pagibig_req']) && $_FILES['pagibig_req'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('pagibig_req')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $pagibig_req = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $pagibig_req,
          "req_type" => 'pagibig_no'
        );
      }
    }

    ### psa_birth_certificate ###
    $psa_birth_certificate = false;
    if(isset($_FILES['psa_birth_certificate']) && $_FILES['psa_birth_certificate'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('psa_birth_certificate')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $psa_birth_certificate = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $psa_birth_certificate,
          "req_type" => 'psa_birth_certificate'
        );
      }
    }

    ### nbi_clearance ###
    $nbi_clearance = false;
    if(isset($_FILES['nbi_clearance']) && $_FILES['nbi_clearance'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('nbi_clearance')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $nbi_clearance = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $nbi_clearance,
          "req_type" => 'nbi_clearance'
        );
      }
    }

    ### police_clearance ###
    $police_clearance = false;
    if(isset($_FILES['police_clearance']) && $_FILES['police_clearance'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('police_clearance')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $police_clearance = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $police_clearance,
          "req_type" => 'police_clearance'
        );
      }
    }

    ### brgy_clearance ###
    $brgy_clearance = false;
    if(isset($_FILES['brgy_clearance']) && $_FILES['brgy_clearance'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('brgy_clearance')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $brgy_clearance = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $brgy_clearance,
          "req_type" => 'brgy_clearance'
        );
      }
    }

    ### med_certificate ###
    $med_certificate = false;
    if(isset($_FILES['med_certificate']) && $_FILES['med_certificate'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('med_certificate')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $med_certificate = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $med_certificate,
          "req_type" => 'med_certificate'
        );
      }
    }

    ### marriage_certificate ###
    $marriage_certificate = false;
    if(isset($_FILES['marriage_certificate']) && $_FILES['marriage_certificate'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('marriage_certificate')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $marriage_certificate = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $marriage_certificate,
          "req_type" => 'marriage_certificate'
        );
      }
    }

    ### psa_birth_certificate_2 ###
    $psa_birth_certificate_2 = false;
    if(isset($_FILES['psa_birth_certificate_2']) && $_FILES['psa_birth_certificate_2'] > 0){
      $config['allowed_types']     = 'png|jpg|jpeg|docx|doc|pdf';
      $this->upload->initialize($config);

      if(!$this->upload->do_upload('psa_birth_certificate_2')){
         $error = array('error' => $this->upload->display_errors());
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $psa_birth_certificate_2 = $config['upload_path'].$cdata['upload_data']['file_name'];
        $requirements[] = array(
          "employee_idno" => $app_ref_no,
          "file_path" => $psa_birth_certificate_2,
          "req_type" => 'child_birth_certificate'
        );
      }
    }

    if(count((array)$requirements) == 0){
      $data = array("success" => 0, "message" => "Oops! Theirs nothing to upload. Please check the file size of the things your going to upload.");
      generate_json($data);
      exit();
    }

    // print_r($requirements);
    // die();
    $inserted = $this->applicant_model->set_requirements($requirements);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to upload requirements. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Requirements successfully uploaded.");
    generate_json($data);

  }

  public function destroy() {

		$appId = $this->input->post('appId');

		$data = array(0,$appId);

		$this->applicant_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Deleted Successfully');
		generate_json($data);

	}

  public function destroyeducation() {

		$id = $this->input->post('id');
		$data = array(0,$id);
		$this->applicant_model->destroyEducation($data);
		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}

  public function destroyworkhis() {

		$id = $this->input->post('id');
		$data = array(0,$id);
		$this->applicant_model->destroyWorkHistory($data);
		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}

	public function destroydependent() {

		$id = $this->input->post('id');
		$data = array(0,$id);
		$this->applicant_model->destroyDependent($data);
		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}

  public function addeducation(){
		$appId = $this->input->post('id');
		// $educYear = $this->input->post('educYear');
    $educYearFrom = $this->input->post('educYearFrom');
    $educYearTo = $this->input->post('educYearTo');
		$educSchool = $this->input->post('educSchool');
		$educCourse = $this->input->post('educCourse');
		$educLevel = $this->input->post('educLevel');

		// $date = preg_split ("/[\-]+/", $educYear);
		// $fromDate1 = $date[0];
		// $fromDate2 = $date[1];
		// $fromDate3 = $date[2];
		// $toDate1 = $date[3];
		// $toDate2 = $date[4];
		// $toDate3 = $date[5];
    //
		// $fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
		// $toDate = $toDate1."-".$toDate2."-".$toDate3;

		if($appId == "" || $educYearFrom == "" || $educYearTo == "" || $educSchool == "" || $educLevel == ""){
			$data = array("success" => 0, "message" => "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		if($this->applicant_model->getApplicantByIdNo(array($appId))->num_rows() == 0){
			$data = array("success" => 0, "message" => "Employee does not exist");
			generate_json($data);
			exit();
		}

		$educData = array(
			$appId,
			$educYearFrom,
			$educYearTo,
			$educSchool,
			$educCourse,
			$educLevel,
			1
		);

		$this->applicant_model->setEducation($educData);
		$data = array("success" => 1, "message" => "Successfully add new education");
		generate_json($data);

	}

  public function addworkhistory(){
		$appId = $this->input->post('id');
		// $workYear = $this->input->post('workYear');
    $workYearFrom = $this->input->post('workYearFrom');
    $workYearTo = $this->input->post('workYearTo');
		$workStay = $this->input->post('workStay');
		$workCompany = $this->input->post('workCompany');
		$workPosition = $this->input->post('workPosition');
		$workLevel = $this->input->post('workLevel');
		$workContact = $this->input->post('workContact');
		$workResp = $this->input->post('workResp');

		// $date = preg_split ("/[\-]+/", $workYear);
		// $fromDate1 = $date[0];
		// $fromDate2 = $date[1];
		// $fromDate3 = $date[2];
		// $toDate1 = $date[3];
		// $toDate2 = $date[4];
		// $toDate3 = $date[5];
    //
		// $fromDate = $fromDate1."-".$fromDate2."-".$fromDate3;
		// $toDate = $toDate1."-".$toDate2."-".$toDate3;

		if($appId == "" || $workYearFrom == "" || $workYearTo == "" || $workStay == "" || $workCompany == "" || $workPosition == "" || $workLevel == "" || $workResp == ""){
			$data = array("success" => 0, "message" => "Please fill up all the required fields");
			generate_json($data);
			exit();
		}

		if($this->applicant_model->getApplicantByIdNo(array($appId))->num_rows() == 0){
			$data = array("success" => 0, "message" => "Employee does not exist");
			generate_json($data);
			exit();
		}

		$workHisData = array(
			$appId,
			$workYearFrom,
			$workYearTo,
			$workStay,
			$workCompany,
			$workPosition,
			$workLevel,
			$workContact,
			$workResp,
			1
		);

		$this->applicant_model->setWorkHistory($workHisData);
		$data = array("success" => 1, "message" => "Successfully add new work history");
		generate_json($data);
	}

  public function adddependents(){
		$appId = $this->input->post('id');
		$fname = $this->input->post('firstName');
		$mname = $this->input->post('middleName');
		$lname = $this->input->post('lastName');
		$bday = $this->input->post('bday');
		$relationship = $this->input->post('relationship');
		$contactNo = $this->input->post('contactNo');

		if($appId == "" || $fname == "" || $lname == "" || $relationship == "" || $contactNo == ""){
			$data = array("success" => 0, "message" =>  "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		if($this->applicant_model->getApplicantByIdNo(array($appId))->num_rows() == 0){
			$data = array("success" => 0, "message" => "Employee does not exist");
			generate_json($data);
			exit();
		}

		$deptData = array(
			$appId,
			$fname,
			$mname,
			$lname,
			$bday,
			$relationship,
			$contactNo,
			1
		);

		$this->applicant_model->setDependents($deptData);
		$data = array("success" => 1, "message" => "Successfully added new dependents");
		generate_json($data);
	}

}
