<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Main_page extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('model');
		$this->load->model('admin/admin_model');
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
	//
	//insert all main navigation here //
	//note: make sure name of the function is the same name of main_nav_href column in jcw_main_navigation. see database first
	public function display_page($page_name,$token) {
		//this function is dependent on what inside in main_nav_href column of jcw_main_navigation
		//$page_name is dynamic = main_nav_href
		//see the configuration in config/routes.php
		//$route['main_page/(:any)'] = 'main_page/display_page/$1';

		$this->isLoggedIn();
		$ep_data = $this->session->userdata('Payslip_data');
		if($ep_data != null){
			$this->session->unset_userdata('Payslip_data');
		}
		$data = array(
			 // get data using email
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()

		);

		### for employee announcement ###
		if($page_name == 'announcement_home'){
			$this->load->model('settings/announcement_model');
			$data['announcements'] = $this->announcement_model->get_all_announcement(5);
			$data['total_announce'] = $this->announcement_model->get_all_announcement()->num_rows();
		}

		### for employee hr assists ###
		if($page_name == "hrassist_home"){
			$this->load->model('settings/hr_assists_model');
			$data['hr_assists'] = $this->hr_assists_model->get_hrassists();
		}

		### for attendance chart employee ###
		if($page_name == "attendance_home"){
			date_default_timezone_set('Asia/Manila');
			$this->load->model('attendance_chart/attendance_model');

			$d = new Datetime(today());
			$month = $d->format('m');
			$emp_idno = $this->session->userdata('emp_idno');
			$employee = $this->model->get_attendance_data($emp_idno)->row_array();

			$sdate = new Datetime(date('Y-m-d', strtotime('first day of this month')));
			$edate = new Datetime(today());
			$day_array = array();
			$late_array = array();
			$undertime_array = array();
			$overbreak_array = array();
			$total_minutes_array = array();

			$worksched = json_decode($employee['work_sched']);
			$total_whours = $employee['total_whours'];
			$total_bhours = $employee['total_bhours'];
			$sched_type = $employee['sched_type'];
			$worksched = (array)$worksched;
			$days = array('mon','tue','wed','thu','fri','sat','sun');

			for ($x=$sdate; $x <= $edate ; $x->modify('+1 day')) {
				$date = $x->format('Y-m-d');
				$day = strtolower($x->format('D'));
				for($i = 0; $i < 7; $i++){
					if($day == $days[$i]){
						if($worksched[$days[$i]][0] != ""){
							$timelog = $this->model->get_timelog($emp_idno,$date);
							$timelog = $timelog->result_array();
							if(count($timelog) > 0){

								$timelog_data = array(
									"employee_idno" => $emp_idno,
									"total_whours" => $total_whours,
									"total_bhours" => $total_bhours,
									"sched_type" => $sched_type,
									"stime_in" => $worksched[$days[$i]][0],
									"stime_out" => $worksched[$days[$i]][1],
									"sbreak_in" => $worksched[$days[$i]][3],
									"sbreak_out" => $worksched[$days[$i]][4],
									"timelog" => $timelog,
									"first_in" => $timelog[0]['time_in'],
									"last_out" => end($timelog)['time_out']
								);

								$graph = compute_timelog($timelog_data,'all');
								$late = $graph['late'];
								$undertime = $graph['undertime'];
								$overbreak = $graph['overbreak'];
								$total_min = $graph['total_minutes'];

								$day_array[] = $x->format('M d');
								$late_array[] = $late;
								$undertime_array[] = $undertime;
								$overbreak_array[] = $overbreak;
								$total_minutes_array[] = $total_min;
							}
						}
					}
				}

			}

			$data['month'] = $d->format('M-Y');
			$data['days'] = implode(',',$day_array);
			$data['lates'] = implode(',',$late_array);
			$data['undertimes'] = implode(',',$undertime_array);
			$data['overbreaks'] = implode(',',$overbreak_array);
			$data['total_mins'] = implode(',',$total_minutes_array);

			$data['total_whours'] = $total_whours;
			$data['total_bhours'] = $total_bhours;
			$data['worksched'] = $employee['work_sched'];
			$data['sched_type'] = $sched_type;
		}

		### for employee leave ###
		if($page_name == "leave_home"){
			$this->load->model('employee_leave/employee_leave_model');
			$data['leave_type'] = $this->employee_leave_model->get_leave_type();
		}

		### for payslip home ###
		if($page_name == "payslip_home"){
			$this->load->model('payroll/Employee_payslip_model');
			$employee_idno = $this->session->userdata('emp_idno');
			//get latest payslip
			$latest_payslip = $this->Employee_payslip_model->fetch_latest_payslip($employee_idno)->row();

			//get all date_from
			$alldates_from = $this->Employee_payslip_model->get_alldates_from()->result();
			// if($alldates_from != null){
			// 	$alldates_from = $alldates_from;
			// }else{
			// 	$alldates_from = "--:--";
			// }
			//get all date_to
			$alldates_to = $this->Employee_payslip_model->get_alldates_to()->result();
			// if($alldates_to != null){
			// 	$alldates_to = $alldates_to;
			// }else{
			// 	$alldates_to = "--:--";
			// }

			if($latest_payslip != null){
				//check if data is null
				if($latest_payslip->employee_idno != null){
					$employee_idno = $latest_payslip->employee_idno;
				}else{
					$employee_idno = "No Data Found";
				}

				if($latest_payslip->name != null){
					$name = $latest_payslip->name;
				}else{
					$name = "No Data Found";
				}

				if($latest_payslip->paytype_desc != null){
					$paytype_desc = $latest_payslip->paytype_desc;
				}else{
					$paytype_desc = "No Data Found";
				}

				if($latest_payslip->date_from != null){
					$date_from = $latest_payslip->date_from;
				}else{
					$date_from = '--:--';
				}


				if($latest_payslip->date_to != null){
					$date_to = $latest_payslip->date_to;
				}
				else{
					$date_to = '--:--';
				}

				if($latest_payslip->gross_salary != null){
					$gross_salary = $latest_payslip->gross_salary;
				}else{
					$gross_salary = 0;
				}

				if($latest_payslip->days_duration != null){
					$days_duration = $latest_payslip->days_duration;
				}else{
					$days_duration = 0;
				}

				if($latest_payslip->overtime != null){
					$overtime = $latest_payslip->overtime;
				}else{
					$overtime = 0;
				}

				if($latest_payslip->ot_duration != null){
					$ot_duration = $latest_payslip->ot_duration;
				}else{
					$ot_duration =0;
				}

				if($latest_payslip->additionals != null){
					$additionals = $latest_payslip->additionals;
				}else{
					$additionals = 0;
				}

				if($latest_payslip->regular_holiday != null){
					$regular_holiday = $latest_payslip->regular_holiday;
				}else{
					$regular_holiday = 0;
				}

				if($latest_payslip->regular_holiday_duration != null){
					$regular_holiday_duration = $latest_payslip->regular_holiday_duration;
				}else{
					$regular_holiday_duration = 0;
				}

				if($latest_payslip->special_holiday != null){
					$special_holiday = $latest_payslip->special_holiday;
				}else{
					$special_holiday = 0;
				}

				if($latest_payslip->special_holiday_duration != null){
					$special_holiday_duration = $latest_payslip->special_holiday_duration;
				}else{
					$special_holiday_duration = 0;
				}

				if($latest_payslip->sundays != null){
					$sundays = $latest_payslip->sundays;
				}else{
					$sundays = 0;
				}

				if($latest_payslip->sunday_duration != null){
					$sunday_duration = $latest_payslip->sunday_duration;
				}else{
					$sunday_duration = 0;
				}

				if($latest_payslip->absent != null){
					$absent = $latest_payslip->absent;
				}else{
					$absent = 0;
				}

				if($latest_payslip->absent_duration != null){
					$absent_duration = $latest_payslip->absent_duration;
				}else{
					$absent_duration = 0;
				}

				if($latest_payslip->late != null){
					$late = $latest_payslip->late;
				}else{
					$late = 0;
				}

				if($latest_payslip->late_duration != null){
					$late_duration = $latest_payslip->late_duration;
				}else{
					$late_duration = 0;
				}

				if($latest_payslip->undertime != null){
					$undertime = $latest_payslip->undertime;
				}else{
					$undertime = 0;
				}

				if($latest_payslip->undertime_duration != null){
					$undertime_duration = $latest_payslip->undertime_duration;
				}else{
					$undertime_duration = 0;
				}

				if($latest_payslip->sss != null){
					$sss = $latest_payslip->sss;
				}else{
					$sss = 0;
				}

				if($latest_payslip->philhealth != null){
					$philhealth = $latest_payslip->philhealth;
				}else{
					$philhealth = 0;
				}

				if($latest_payslip->pag_ibig != null){
					$pag_ibig = $latest_payslip->pag_ibig;
				}else{
					$pag_ibig = 0;
				}

				if($latest_payslip->sss_loan != null){
					$sss_loan = $latest_payslip->sss_loan;
				}else{
					$sss_loan = 0;
				}

				if($latest_payslip->pag_ibig_loan != null){
					$pag_ibig_loan = $latest_payslip->pag_ibig_loan;
				}else{
					$pag_ibig_loan = 0;
				}

				if($latest_payslip->cashadvance != null){
					$cashadvance = $latest_payslip->cashadvance;
				}else{
					$cashadvance = 0;
				}

				if($latest_payslip->salary_deduction != null){
					$salary_deduction = $latest_payslip->salary_deduction;
				}else{
					$salary_deduction = 0;
				}

				if($latest_payslip->total_deductions != null){
					$total_deductions = $latest_payslip->total_deductions;
				}else{
					$total_deductions = 0;
				}

				if($latest_payslip->netpay != null){
					$netpay = $latest_payslip->netpay;
				}else{
					$netpay = 0;
				}
			}else{
				$employee_idno = "No Data Found";
				$name = "No Data Found";
				$paytype_desc = "No Data Found";
				$date_from = '--:--';
				$date_to = '--:--';
				$gross_salary = 0;
				$days_duration = 0;
				$overtime = 0;
				$ot_duration =0;
				$additionals = 0;
				$regular_holiday = 0;
				$regular_holiday_duration = 0;
				$special_holiday = 0;
				$special_holiday_duration = 0;
				$sundays = 0;
				$sunday_duration = 0;
				$absent = 0;
				$absent_duration = 0;
				$late = 0;
				$late_duration = 0;
				$undertime = 0;
				$undertime_duration = 0;
				$sss = 0;
				$philhealth = 0;
				$pag_ibig = 0;
				$sss_loan = 0;
				$pag_ibig_loan = 0;
				$cashadvance = 0;
				$salary_deduction = 0;
				$total_deductions = 0;
				$netpay = 0;
			}


			$latest_payslip = array(
				"alldates_from" => $alldates_from,
				"alldates_to" => $alldates_to,
				"employee_idno" => $employee_idno,
				"name" => $name,
				"paytype_desc" => $paytype_desc,
				"date_from" => $date_from,
				"date_to" => $date_to,
				"gross_salary" => $gross_salary,
				"days_duration" => $days_duration,
				"overtime" => $overtime,
				"ot_duration" => $ot_duration,
				"additionals" => $additionals,
				"regular_holiday" => $regular_holiday,
				"regular_holiday_duration" => $regular_holiday_duration,
				"special_holiday" => $special_holiday,
				"special_holiday_duration" => $special_holiday_duration,
				"sundays" => $sundays,
				"sunday_duration" => $sunday_duration,
				"absent" => $absent,
				"absent_duration" => $absent_duration,
				"late" => $late,
				"late_duration" => $late_duration,
				"undertime" => $undertime,
				"undertime_duration" => $undertime_duration,
				"sss" => $sss,
				"philhealth" => $philhealth,
				"pag_ibig" => $pag_ibig,
				"sss_loan" => $sss_loan,
				"pag_ibig_loan" => $pag_ibig_loan,
				"cashadvance" => $cashadvance,
				"salary_deduction" => $salary_deduction,
				"total_deductions" => $total_deductions,
				"netpay" => $netpay
			);
			//stores data in session
			$session_payslip = array("Payslip_data" => $latest_payslip);
       		$this->session->set_userdata($session_payslip);
			$data['payslip_details'] = $latest_payslip;
		}

		### for branch_home ###
		// if($page_name == "branch_home"){
		// 	$this->load->view('includes/header2', $data);
		// 	$this->load->view('main_navigation/'.$page_name, $data);
		// 	// exit();
		// }
		//
		### for transfer_home ###
		if($page_name == "transfer_home"){
			$this->load->model('branch/branch_model');
			$branches = $this->branch_model->get_all_branch();
			$data['branches'] = false;
			if($branches->num_rows() > 0){
				$data['branches'] = $branches->result_array();
			}
		}

		### for user_profile ###


		$this->load->view('includes/header', $data);
		$this->load->view('main_navigation/'.$page_name, $data);
	}


	//end of insert all main navigation here //
}
