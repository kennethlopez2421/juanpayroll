<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Main extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('model');
	}

	public function index($bcode = 'hris_cp') {
		$config['sess_expiration'] = 36000;
		if($this->session->userdata('isLoggedIn') == true) {

			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}

		if(isset($this->session->bcode)){
			if($bcode != en_dec('dec', $this->session->bcode)){
				$this->session->sess_destroy();
				header('location:'.base_url(''.$bcode.''));
				exit();
			}
		}

		if(!empty($bcode) && !isset($this->session->database_name)){
			$this->load->model('branch/branch_model');
			$dbname = $this->branch_model->get_hris_branch($bcode,'branch_code');

			if($dbname->num_rows() == 0){
				$data = array("success" => 0, "facial_recog" => 0, "message" => "No available data");
				generate_json($data);
				exit();
			}
			// die($dbname->row()->branch_name);
			$this->session->set_userdata('database_name', $dbname->row()->database_name);
			$this->session->set_userdata('branch_name', $dbname->row()->branch_name);
			$this->session->set_userdata('timezone', $dbname->row()->timezone);
			$this->session->set_userdata('location', $dbname->row()->location);
			$this->session->set_userdata('company_code', en_dec('en',$bcode));
			$this->session->set_userdata('bcode', en_dec('en',$bcode));
		}

		// $this->session->sess_destroy();
		$this->load->view('login_new');
	}

	//for pdf
	public function pdf() {
		$this->load->library('fpdf_gen');

		$this->fpdf->SetFont('Arial','B',16);
		$this->fpdf->Cell(40,10,'Hello World!');

		echo $this->fpdf->Output('hello_world.pdf','D');
	}

	public function login(){
		$config['sess_expiration'] = 36000;
		$username 	= sanitize($this->input->post('loginUsername'));
		$password 	= sanitize($this->input->post('loginPassword'));
		$login_type = $this->input->post('login_type');

		// CHECK REQUIRED FIELDS
		if($username == "" || $password == ""){
			$data = array("success" => 0, "message" => "Please enter your username and password");
			generate_json($data);
			exit();
		}
		// CHECK IF VALID USERNAME
		$validate_username = $this->model->validate_username($username);
		if($validate_username->num_rows() == 0){
			$data = array(
				'success' => 0,
				'message' => 'The username you\'ve entered doesn\'t match any account. <a href="'.base_url('Main/register').'">Sign up for an account.</a>'
			);
			generate_json($data);
			exit();
		}
		// CHECK UNVERIFIED USER
		$unverified_username = $validate_username->row()->enabled;
		if($unverified_username == 0){
			$data = array(
				'success' => 0,
				'message' => 'The account you\'ve entered is unverified account.'
			);
			generate_json($data);
			exit();
		}
		// CHECK PASSWORD
		$hash_password = $validate_username->row()->password;
		if(!password_verify($password,$hash_password)){
			$data = array(
				'success' => 0,
				'message' => 'The password you\'ve entered is not correct. Please try again.'
			);
			generate_json($data);
			exit();
		}

		$userObj = $validate_username->row(); //get the data for fetch
		$login_type = ($userObj->pos_lvl <= 1) ? 'admin' : $login_type;
		$dept_access = ($this->model->get_dept_access($userObj->employee_idno)->num_rows() > 0)
		? $dept_access = $this->model->get_dept_access($userObj->employee_idno)->row()->department_access : 0;

		$userData = array( // store in array
			'user_id'	  => $userObj->user_id,
			'emp_idno' => $userObj->employee_idno,
			'username'    => $userObj->username,
			'firstname'	  => $userObj->user_fname,
			'middlename'  => $userObj->user_mname,
			'lastname'	  => $userObj->user_lname,
			'position_id' => $userObj->position_id,
			'position_lvl' => ($login_type != 'admin')
				? $this->model->get_position_details_access_emp()->row()->hierarchy_lvl
				: $this->model->get_position_details_access($userObj->position_id)->row()->hierarchy_lvl,
			'deptId' => $userObj->deptId,
			'dept_access' => $dept_access,
			'get_position_access' => ($login_type != 'admin')
				? $this->model->get_position_details_access_emp()->row()
				: $this->model->get_position_details_access($userObj->position_id)->row(),
			// $this->model->get_position_details_access($userObj->position_id)->row(),
			'enabled'     => $userObj->enabled,
			'login_type'  => $login_type,
			'isLoggedIn'  => true,
			'avatar_file' => $userObj->avatar_file,
		);

		$this->session->set_userdata($userData); // set session

		$token_session = uniqid();
		$token_arr = array( // store token in array
			'token_session'	=> $token_session,
		);

		$this->session->set_userdata($token_arr);

		$token = en_dec('en', $token_session);
		if($login_type == 'admin'){
			$config['sess_expiration'] = 36000;
		}

		$data = array(
			'success' => 1,
			'message' => 'Login Successfully',
			'token_session' => $token
		);

		generate_json($data);
	}

	public function isLoggedIn() {
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

	public function home($token = '') {
		$this->isLoggedIn();
		if(isset($this->session->superuser) && $this->session->superuser == true){
			$_SESSION['get_position_access'] = $this->model->get_position_details_access($this->session->position_id)->row();
		}

		$data_user = array(
			 // get data using email
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);
		$this->load->view('includes/header', $data_user);
		$this->load->view('main_navigation/user_dashboard', $data_user);

	}

	public function logout() {
		$bcode = en_dec('dec',$this->session->bcode);
		$this->session->sess_destroy();
		header('location:'.base_url(''.$bcode.''));
		exit();
		// $this->load->view('branch_login');
	}
	### CHECK USER SESS ID ###
	public function check_sess_id(){
		$sess_id = $this->input->post('sess_id');
		### FOR THOSE BRANCH THAT DOES NOT NEED LOCATION ###
		if(isset($this->session->location) && $this->session->location == 'offline'){
			$data = array("success" => 1, "status" => true);
			generate_json($data);
			exit();
		}

		if(empty($sess_id)){
			$data = array("success" => 0, "status" => false);
			generate_json($data);
			exit();
		}

		$sess_id = en_dec('dec',$sess_id);

		$validate_sess_id = $this->model->validate_sess_id($sess_id);
		if($validate_sess_id->num_rows() == 0){
			$data = array("success" => 0, "status" => false);
			generate_json($data);
			exit();
		}

		if($validate_sess_id->row()->hierarchy_lvl >= 2){
			$data = array("success" => 0, "status" => false);
			generate_json($data);
			exit();
		}

		if($validate_sess_id->row()->hierarchy_lvl <= 2){
			$data = array("success" => 1, "status" => true);
			generate_json($data);
		}
	}
	### user access ###
	public function get_employee_by_dept(){
		$dept_id = $this->input->post('dept_id');
		if($dept_id == ""){
			$data = array("success" => 0, "message" => "Unable to find any employee");
			generate_json($data);
			exit();
		}

		$emp = $this->model->get_emp_by_dept($dept_id);
		if($emp->num_rows() == 0){
			$data = array("success" => 0, "message" => "No available employee under this department");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "emp" => $emp->result_array());
		generate_json($data);

	}
	### SEARCH USER ###
	public function search_user(){
		$keyword = $this->input->post('keyword');
		if(empty($keyword)){
			$data = array("success" => 2);
			generate_json($data);
			exit();
		}

		$row = $this->model->search_user($keyword);
		if($row->num_rows() == 0){
			$data = array(
				"success" => 0,
				"message" => '<div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3 mb-2">
												<div class="card ">
													<div class="card-header py-2">
														<p class = "m-0">No Result Found</p>
													</div>
												</div>
											</div>'
			);
			generate_json($data);
			exit();
		}

		$html = "";
		foreach($row->result_array() as $user){
			$html .= '<div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3 mb-2">
									<div class="card ">
										<div class="card-header py-2">
											<p class = "m-0">'.$user['fullname'].' - '.$user['employee_idno'].'</p>
										</div>
									</div>
								</div>';
		}

		$data = array("success" => 1, "message" => $html);
		generate_json($data);
	}
	### update_sal ###
	public function update_sal(){
		$this->load->view('update_sal');
	}

	public function update_sal_form(){
		$c_code = $this->input->post('c_code');
		$this->load->model('branch/branch_login_model');
		if(empty($c_code)){
			$data = array("success" => 0);
			generate_json($data);
			exit();
		}

		$branch = $this->branch_login_model->get_company_code($c_code);
		if($branch->num_rows() == 0){
			$data = array("success" => 0);
			generate_json($data);
			exit();
		}

		$this->db = switch_database($branch->row()->database_name);
		$emps = $this->model->get_all_emp()->result();
		foreach($emps as $emp){
			$total_sal = 0;
			$total_leave = 0;
			$sal = json_decode($emp->sal_cat);
			$leave = json_decode($emp->emp_leave);
			$ex_rate = $emp->ex_rate;
			$base_pay = $sal[0]->amount * (float)$ex_rate;
			foreach($sal as $s){
				$total_sal += (float)$s->amount;
			}

			foreach($leave as $l){
				$total_leave += (int)$l->days;
			}
			// echo $total_sal."</br>";
			$total_sal_converted = $total_sal * (float)$ex_rate;
			$this->model->update_base_pay(array((float)$base_pay,$emp->c_id));
			$this->model->update_sal_and_leve(array((float)$total_sal,(float)$total_sal_converted,(float)$total_leave,$emp->c_id));
		}

		$data = array("success" => 1);
		generate_json($data);
	}
	### update total_hours ###
	public function update_worksched(){
		$emps = $this->model->get_all_emp()->result();
		$total_hours = 0;
		$total_break =0;

		foreach($emps as $emp){
			$wsched = json_decode($emp->work_sched);
			$working_days = 0;
			$working_hours = 0;
			$working_break = 0;
			if($wsched->mon[2] != ""){
				$bs = converToTime($wsched->mon[3]);
				$be = converToTime($wsched->mon[4]);
				$total = $be - $bs;
				$working_days += 1;
				$working_break += $total;
				$working_hours += $wsched->mon[2];
			}

			if($wsched->tue[2] != ""){
				$bs = converToTime($wsched->tue[3]);
				$be = converToTime($wsched->tue[4]);
				$total = $be - $bs;
				$working_days += 1;
				$working_break += $total;
				$working_hours += $wsched->tue[2];
			}

			if($wsched->wed[2] != ""){
				$bs = converToTime($wsched->wed[3]);
				$be = converToTime($wsched->wed[4]);
				$total = $be - $bs;
				$working_days += 1;
				$working_break += $total;
				$working_hours += $wsched->wed[2];
			}

			if($wsched->thu[2] != ""){
				$bs = converToTime($wsched->thu[3]);
				$be = converToTime($wsched->thu[4]);
				$total = $be - $bs;
				$working_days += 1;
				$working_break += $total;
				$working_hours += $wsched->thu[2];
			}

			if($wsched->fri[2] != ""){
				$bs = converToTime($wsched->fri[3]);
				$be = converToTime($wsched->fri[4]);
				$total = $be - $bs;
				$working_days += 1;
				$working_break += $total;
				$working_hours += $wsched->fri[2];
			}

			if($wsched->sat[2] != ""){
				$bs = converToTime($wsched->sat[3]);
				$be = converToTime($wsched->sat[4]);
				$total = $be - $bs;
				$working_days += 1;
				$working_break += $total;
				$working_hours += $wsched->sat[2];
			}


			if($wsched->sun[2] != ""){
				$bs = converToTime($wsched->sun[3]);
				$be = converToTime($wsched->sun[4]);
				$total = $be - $bs;
				$working_days += 1;
				$working_break += $total;
				$working_hours += $wsched->sun[2];
			}

			$total_hours = $working_hours / $working_days;
			$total_break = ($working_break / $working_days) / 3600;

			$wdata = array(round($total_hours,2),round($total_break,2),(int)$emp->w_id);
			$sql = $this->model->update_worksched($wdata);
			echo $sql;
		}
	}
	### change pass ###
	public function change_pass(){
		$this->isLoggedIn();

		$username = $this->input->post('username');
		$current_pw = $this->input->post('current_pw');
		$new_pw = $this->input->post('new_pw');

		if(empty($username) || empty($current_pw) || empty($new_pw)){
			$data = array("success" => 0, "message" => "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		$validate = $this->model->validate_username($username);
		if($validate->num_rows() == 0){
			$data = array("success" => 0, "message" => "Username invalid. Please try again");
			generate_json($data);
			exit();
		}

		$user = $validate->row();
		if($user->user_id != $this->session->userdata('user_id')){
			$this->logout();
		}

		if(!password_verify($current_pw,$user->password)){
			$data = array("success" => 0, "message" => "Invalid current password. Please try again.");
			generate_json($data);
			exit();
		}

		$option = ['cost' => 12];
		$new_pw = password_hash($new_pw,PASSWORD_BCRYPT,$option);

		$this->model->update_password($new_pw,$user->user_id);
		$data = array("success" => 1, "message" => "Password Successfully Change.");
		generate_json($data);
	}
	### forgot pass ###
	public function forgot_pass(){
		$forgot_pw_email = $this->input->post('forgot_pw_email');
		$token_fix = "CloudPandaPHInc";

		if(!filter_var($forgot_pw_email, FILTER_VALIDATE_EMAIL)){
			$data = array("success" => 0, "message" => "Invalid email format. Please try again.");
			generate_json($data);
			exit();
		}

		$isExist = $this->model->get_emp_by_email($forgot_pw_email);
		if($isExist->num_rows() == 0){
			$data = array("success" => 0, "message" => "The email you provided is not link to any of our users. Please try again.");
			generate_json($data);
			exit();
		}
		$this->load->library('email');
		$hash_email = removeSpecialchar(en_dec('en',$forgot_pw_email));
		$token_email = en_dec('en',$token_fix);

		$data['password_reset_link'] = base_url('main/reset_pass/'.$hash_email.'/'.$token_email.'/'.$this->session->bcode);
    $data['subject'] = "Password Reset";

    $msg = $this->load->view('emails/reset_email',$data,true);

    $this->email->from('support@cloudpanda.ph', 'One Payroll');
    $this->email->to($forgot_pw_email);

    $this->email->subject('Password Reset');
    $this->email->message($msg);
    $email = $this->email->send();

		if($email){
        $data = array("success" => 1, "message" => "The link to your password reset is sent to the email you provided. Thank You");
    }else{
        $data = array("success" => 0, "message" => "Email was not sent. Please try again");
    }

		generate_json($data);
	}
	### reset pass ###
	public function reset_pass($email,$token,$code){
		$token_fix = "CloudPandaPHInc";

    $data['email'] = $email;
		$data['bcode'] = $code;
    $token = en_dec('dec',$token);
    $email = en_dec('dec',$email);

		if($token_fix != $token){
			header("Location:".base_url());
			exit();
		}

		// $isExist = $this->model->get_emp_by_email($email);
		// if($isExist->num_rows() == 0){
		// 	header("Location:".base_url());
		// 	exit();
		// }

		$this->load->view('forgot_pass',$data);
	}

	public function reset_change_pass(){
		$this->load->model('branch/branch_login_model');
		$new_pw = $this->input->post('new_pw');
		$confirm_new_pw = $this->input->post('confirm_new_pw');
		$email = en_dec('dec',$this->input->post('email'));
		$bcode = en_dec('dec',$this->input->post('bcode'));

		if(empty($new_pw) || empty($confirm_new_pw)){
			$data = array("success" => 0, "message" => "Please fill up all required fields.");
			generate_json($data);
			exit();
		}

		if(!isset($this->session->database_name) && empty($this->session->database_name)){
			$company = $this->branch_login_model->get_company_code($bcode);
	    if($company->num_rows() == 0){
	      $data = array("success" => 0, "message" => "Invalid Company Code");
	      generate_json($data);
	      exit();
	    }

			$company = $company->row_array();
			// $this->session->set_userdata('database_name', $company['database_name']);
	    $this->db = switch_database($company['database_name']);
		}

		if($new_pw != $confirm_new_pw){
			$data = array("success" => 0, "message" => "Password do not match. Please try again.");
			generate_json($data);
			exit();
		}

		$isExist = $this->model->get_emp_by_email($email);
		if($isExist->num_rows() == 0){
			$data = array("success" => 0, "message" => "Invalid Email. Please try again");
			generate_json($data);
			exit();;
		}

		$options = ['cost' => 12];
    $new_pass = password_hash($new_pw, PASSWORD_BCRYPT, $options);
    $changed = $this->model->change_pass($email,$new_pass);
		if($changed === false){
			$data = array("success" => 0, "message" => "Unable to change password. Please try again.");
			generate_json($data);
			exit();
		}


    $data = array("success" => 1, "message" => "Password Change Successful", "bcode" => $bcode);
		generate_json($data);
	}

	public function email_test(){

		// $this->load->library('Pdf');
		// $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		// print_r($pdf);
		// die();
		$this->load->view('emails/email_pump');
	}

	public function email_pump(){
		ini_set('max_execution_time', '0');
		$this->load->library('email');
		// $email_data['date'] = today();
		// $email_data['fullname'] = "Marky Neri";
		// $email_data['fromdate'] = $email['fromdate'];
		// $email_data['todate'] = $email['todate'];
		// $email_data['download_link'] = base_url('Main/download_payslip/'.$hash_refno.'/'.$token_email.'/'.removeSpecialchar($hash_payslip_data));
		for ($i=0; $i < 3; $i++) {
			$data['name'] = "Marky";
			$msg = $this->load->view('emails/email_test',$data,true);

			$this->email->from('support@cloudpanda.ph', 'Juan Payroll Email Pump');
			$this->email->to('nerimarky@gmail.com');

			$this->email->subject('Email Pump');
			$this->email->message($msg);
			$email = $this->email->send();
		}

		// $data = array("success" => 1, "message" => "Payroll approved successfully");
    // generate_json($data);
	}

	public function download_payslip($refno,$token,$payslip){
		$token_fix = "CloudPandaPHInc";
		$refno = en_dec('dec',$refno);
		$this->load->model('payroll/payroll_history_model');
		$this->load->model('payroll/payroll_model_new');

		if($token_fix != en_dec('dec', $token)){
			$this->logout();
		}

		if($this->payroll_history_model->get_email_w_refno($refno)->num_rows() == 0){
			$this->logout();
		}
		// $payslip_data = json_decode(en_dec('dec',$payslip));
		// print_r(json_decode(en_dec('dec',$payslip)));
		// die();
		$payslip = json_decode(en_dec('dec', $payslip));
		// print_r($payslip);
		// die();
		$payslip_data = (object)$this->payroll_model_new->get_payslip_data($payslip[0],$payslip[1],$payslip[2],$payslip[3],$payslip[4],$payslip[5],$payslip[6],$payslip[7]);
		$data = array(
			"employee_idno" => $payslip_data->emp_idno,
			"name" => $payslip_data->fullname,
			"date" => $payslip_data->date,
			"wdays" => $payslip_data->wdays,
			"gross_pay" => $payslip_data->gross_pay,
			"gross_pay_less" => $payslip_data->gross_pay_less,
			"reg_holiday" => $payslip_data->reg_holiday,
			"reg_holiday_pay" => $payslip_data->reg_holiday_pay,
			"spl_holiday" => $payslip_data->spl_holiday,
			"spl_holiday_pay" => $payslip_data->spl_holiday_pay,
			"sunday" => $payslip_data->sunday,
			"sunday_pay" => $payslip_data->sunday_pay,
			"absent" => $payslip_data->absent,
			"absent_deduction" => $payslip_data->absent_deduction,
			"late" => $payslip_data->late,
			"late_deduct" => $payslip_data->late_deduct,
			"ut" => $payslip_data->ut,
			"ut_deduct" => $payslip_data->ut_deduct,
			"total_deduct" => $payslip_data->total_deduct,
			"sss" => $payslip_data->sss,
			"sss_loan" => $payslip_data->sss_loan,
			"philhealth" => $payslip_data->philhealth,
			"pagibig" => $payslip_data->pagibig,
			"pagibig_loan" => $payslip_data->pagibig_loan,
			"cashadvance"=> $payslip_data->cashadvance,
			"sal_deduct" => $payslip_data->sal_deduct,
			"add_pay" => $payslip_data->add_pay,
			"ot_min" => $payslip_data->ot_min,
			"ot_pay" => $payslip_data->ot_pay,
			"net_pay" => $payslip_data->net_pay,
			"full_date" => $payslip[8],
			"currency" => $payslip_data->currency
		);

		$this->load->library('Pdf');
		$page = $this->load->view('payroll/print_payslip',$data,true);
		$style = array(
			'border' => false,
			'padding' => 0,
			'fgcolor' => array(0,0,0),
			'bgcolor' => false,
		);

		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('Payslip');
		// $pdf->SetHeaderMargin(30);
		$pdf->SetTopMargin(20);
		// $pdf->setFooterMargin(20);
		// $pdf->SetAutoPageBreak(true);
		$pdf->SetDefaultMonospacedFont('helvetica');
		$pdf->SetFont('helvetica', '', 9);
		$pdf->setFontSubsetting(false);
		$pdf->setPrintHeader(false);
		$pdf->SetAuthor('One Payroll');
		$pdf->SetDisplayMode('real', 'default');
		$pdf->SetProtection(array('print', 'copy'), $payslip[6], null, 0, null);

		$pdf->AddPage();
		$pdf->setCellPaddings(0,0,0,0);

		$pdf->writeHTML($page, true, false, true, false, '');
		$pdf->write2DBarcode('PO_'.$payslip_data->ref_no, 'QRCODE,H', 170, 15, 17, 17, $style, 'N');
		$pdf->Output($payslip_data->fullname.' Payslip.pdf', 'I');

	}


}
