<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('employees/employee_model');
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
	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('employees/employee', $data);

	}

	//json result
	public function employeejson() {

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('searchValue');

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->employee_model->getEmployee(null,null,null)->num_rows(),
			"recordsFiltered" => $this->employee_model->getEmployee(null,null,null)->num_rows(),
			"data" => $this->employee_model->getEmployee($start,$length,$search)->result()
		);

		echo json_encode($data);
	}

	public function educationjson() {
		$employee_idno = $this->input->post('employee_idno');
		$data = $this->employee_model->getEducations($employee_idno)->result();
		echo json_encode($data);
	}

	public function workhisjson() {
		$employee_idno = $this->input->post('employee_idno');
		$data = $this->employee_model->getWorkHistory($employee_idno)->result();
		echo json_encode($data);
	}

	public function dependentsjson() {
		$employee_idno = $this->input->post('employee_idno');
		$data = $this->employee_model->getDependents($employee_idno)->result();
		echo json_encode($data);
	}

	//frontend
	public function add($token = "") {
		$employeeIdNo = generate_player_no();
		while($this->employee_model->getEmployeeByIdNo($employeeIdNo)->num_rows() > 0){
			$employeeIdNo = generate_player_no();
		}
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'employeeIdNo' => $employeeIdNo
		);


		$this->load->view('includes/header', $data);
		$this->load->view('employees/employee-add', $data);
	}

	//backend
	public function create() {

		$employeeIdNo = str_replace(' ', '', strtoupper($this->input->post('employeeIdNo')));
		$firstName = $this->input->post('firstName');
		$middleName = $this->input->post('middleName');
		$lastName = $this->input->post('lastName');
		$birthday = $this->input->post('birthday');
		// die($birthday);
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

		if($employeeIdNo == "" || $firstName == "" || $lastName == "" || $birthday == "" || $gender == "" ||
		 		$maritalStatus == "" || $homeAddress1 == "" || $contactNo == "" || $email == "" || $isActive == ""){
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
		for($x = 1; $x < count((array)$educations); $x++) {
			$fromDate = $educations[$x][0];
			$toDate = $educations[$x][1];
			// die($fromDate."-".$toDate);
			$educationSchool = $educations[$x][2];
			$educationCourse = $educations[$x][3];
			$educationLevel = $educations[$x][4];

			if($fromDate ==  "" || $toDate == "" || $educationSchool == "" || $educationLevel == ""){
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
		for($x = 1; $x < count((array)$workHistory); $x++) {

			$fromDate = $workHistory[$x][0];
			$toDate = $workHistory[$x][1];
			$workHistoryStay = $workHistory[$x][2];
			$workHistoryCompany = $workHistory[$x][3];
			$workHistoryPosition = $workHistory[$x][4];
			$workHistoryLevel = $workHistory[$x][5];
			$workHistoryContact = $workHistory[$x][6];
			$workHistoryResponsibility = $workHistory[$x][7];

			if($fromDate == "" || $toDate == "" || $workHistoryStay == "" || $workHistoryCompany == "" || $workHistoryPosition == "" ||
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
				$employeeIdNo,
				$firstName,
				$middleName,
				$lastName,
				$birthday,
				$gender,
				$maritalStatus,
				$homeAddress1,
				$homeAddress2,
				$contactNo,
				$email,
				$sss_no,
				$philhealth_no,
				$pagibig_no,
				$tin_no,
				$isActive,
				1
			);

			$this->employee_model->create($data);
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

	//backend
	public function check_duplicate_name(){
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$data_array = array($fname, $lname);
		$res = $this->model->employee_model->getEmployeeByFnameLname($data_array)->num_rows();
		if($res > 0){
			$data = array("duplicate" => 1, "message" => "Name already belong to another employee");
		}else{
			$data = array("duplicate" => 0, "message" => "");
		}

		generate_json($data);
	}

	// backend
	public function check_email_exist(){
		$email = $this->input->post('email');
		$data_array = array($email);
		$res = $this->model->employee_model->getEmployeeByEmail($data_array)->num_rows();
		if($res > 0){
			$data = array("emailExist" => 1, "message" => "Email is already used by another employee");
		}else{
			$data = array("emailExist" => 0, "message" => "");
		}

		generate_json($data);
	}
	//frontend
	public function edit($token) {

		$empID = $this->input->post('empID');
		$data = array($empID);
		$employeeID = $this->employee_model->getEmployeeById($data)->row()->employee_idno;

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'empID' => $empID,
			'employee' => $this->employee_model->getEmployeeById($data)->row(),
			'educations' => $this->employee_model->getEducations($employeeID)->result(),
			'workHistory' => $this->employee_model->getWorkHistory($employeeID)->result(),
			'dependents' => $this->employee_model->getDependents($employeeID)->result(),
			'educ_level' => $this->employee_model->get_educ_level(),
      'relation' => $this->employee_model->get_relation(),
			'employee_idno' => $employeeID
		);

		$this->load->view('includes/header', $data);
		$this->load->view('employees/employee-edit', $data);

	}

	//backend
	public function update() {

		$empID = $this->input->post('empID');
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

		$sss_no = $this->input->post('sss_no');
		$philhealth_no = $this->input->post('philhealth_no');
		$pagibig_no = $this->input->post('pagibig_no');
		$tin_no = $this->input->post('tin_no');

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
			$sss_no,
			$philhealth_no,
			$pagibig_no,
			$tin_no,
			$empID
		);

		$this->employee_model->updateEmployeeRecord($data);

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

		$this->employee_model->updateEmployeeEducation($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);
	}

	public function addeducation(){
		$empId = $this->input->post('id');
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

		if($empId == "" || $educYearFrom == "" || $educYearTo == "" || $educSchool == "" || $educLevel == ""){
			$data = array("success" => 0, "message" => "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		if($this->employee_model->getEmployeeByIdNo(array($empId))->num_rows() == 0){
			$data = array("success" => 0, "message" => "Employee does not exist");
			generate_json($data);
			exit();
		}

		$educData = array(
			$empId,
			$educYearFrom,
			$educYearTo,
			$educSchool,
			$educCourse,
			$educLevel,
			1
		);

		$this->employee_model->setEducation($educData);
		$data = array("success" => 1, "message" => "Successfully add new education");
		generate_json($data);

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

		$this->employee_model->updateEmployeeWorkHistory($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);

	}

	public function addworkhistory(){
		$empId = $this->input->post('id');
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

		if($empId == "" || $workYearFrom == "" || $workYearTo == "" || $workStay == "" || $workCompany == "" || $workPosition == "" || $workLevel == "" || $workResp == ""){
			$data = array("success" => 0, "message" => "Please fill up all the required fields");
			generate_json($data);
			exit();
		}

		if($this->employee_model->getEmployeeByIdNo(array($empId))->num_rows() == 0){
			$data = array("success" => 0, "message" => "Employee does not exist");
			generate_json($data);
			exit();
		}

		$workHisData = array(
			$empId,
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

		$this->employee_model->setWorkHistory($workHisData);
		$data = array("success" => 1, "message" => "Successfully add new work history");
		generate_json($data);
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

		$this->employee_model->updateEmployeeDependents($data);

		$data = array('success' => 1, 'message' => 'Successfully Edited');
		echo json_encode($data);

	}

	public function adddependents(){
		$empId = $this->input->post('id');
		$fname = $this->input->post('firstName');
		$mname = $this->input->post('middleName');
		$lname = $this->input->post('lastName');
		$bday = $this->input->post('bday');
		$relationship = $this->input->post('relationship');
		$contactNo = $this->input->post('contactNo');

		if($empId == "" || $fname == "" || $lname == "" || $relationship == "" || $contactNo == ""){
			$data = array("success" => 0, "message" =>  "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		if($this->employee_model->getEmployeeByIdNo(array($empId))->num_rows() == 0){
			$data = array("success" => 0, "message" => "Employee does not exist");
			generate_json($data);
			exit();
		}

		$deptData = array(
			$empId,
			$fname,
			$mname,
			$lname,
			$bday,
			$relationship,
			$contactNo,
			1
		);

		$this->employee_model->setDependents($deptData);
		$data = array("success" => 1, "message" => "Successfully added new dependents");
		generate_json($data);
	}

	//frontend
	public function delete() {

	}

	//backend
	public function destroy() {

		$employeeId = $this->input->post('employeeId');
		// echo $employeeId;
		// die();

		$data = array(0,$employeeId);

		$this->employee_model->destroy($data);

		$data = array('success' => 1, 'message' => 'Employee is now Deactivated');
		echo json_encode($data);

	}

	public function destroyeducation() {

		$id = $this->input->post('id');
		$data = array(0,$id);
		$this->employee_model->destroyEducation($data);
		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}

	public function destroyworkhis() {

		$id = $this->input->post('id');
		$data = array(0,$id);
		$this->employee_model->destroyWorkHistory($data);
		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}

	public function destroydependent() {

		$id = $this->input->post('id');
		$data = array(0,$id);
		$this->employee_model->destroyDependent($data);
		$data = array('success' => 1, 'message' => 'Successfully Deleted');
		echo json_encode($data);
	}

	public function end_employment(){
		$this->isLoggedIn();

		$emp_id = $this->input->post('emp_id'); // emp id
		$emp_id2 = $this->input->post('emp_id2'); // employee idno
		$date_of_termination = $this->input->post('date_of_termination');
		$reason = $this->input->post('reason');

		$insert_data = array(
			"employee_idno" => $emp_id2,
			"reason" =>$reason,
			"termination_date" => $date_of_termination,
			"created_by" => $this->session->userdata('emp_idno')
		);

		// print_r($insert_data);
		// die();

		if(empty($emp_id2) || empty($reason) || empty($date_of_termination) || $this->session->userdata('emp_idno') == ""){
			$data = array("success" => 0, "message" => "Please fill up all required fields.");
			generate_json($data);
			exit();
		}

		// foreach($insert_data as $row){
		// 	if(empty($row)){
		//
		// 	}
		// }

		$deleted = $this->employee_model->destroy(array(0,$emp_id));
		if($deleted == false){
			$data = array("success" => 0, "message" => "End of Employment Failed. Please try again.");
			generate_json($data);
			exit();
		}

		$inserted = $this->employee_model->set_hris_employment_history($insert_data);
		if($inserted == false){
			$data = array("success" => 0, "message" => "Unable to create Employment History");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => 'Employee is now Deactivated');
		generate_json($data);

	}

}
