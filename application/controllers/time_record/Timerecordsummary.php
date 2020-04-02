<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Timerecordsummary extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('time_record/Timerecordsummary_model');
		$this->load->model('time_record/timerecord_logs_model');
	}

	//views
	public function index($token = "") {

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);
		//unset data for saving trs
		$trs_data = $this->session->userdata('Timerecord_array');
		if($trs_data != null){
			$this->session->unset_userdata('Timerecord_array');
		}
		$this->load->view('includes/header', $data);
		$this->load->view('timelog/timerecordsummary', $data);

	}
	public function try_util(){
		$employee_idno = "11538";
		$date_created = '2019-02-26';
		$purpose = "others";
		$val = fetch_absent($employee_idno,$date_created,$purpose);
		print_r($val);
	}
	//this will compare the date that will be filetered from the default trs date
	public function compare_default_date(){
		$getdates = $this->Timerecordsummary_model->get_default_date()->row();
		if(!(empty($getdates))){
			$data = array("success" => 1, "date_ranges" => $getdates);
		}else{
			$data = array("success" => 0, "date_ranges" => "Please input valid date range from Time Record Summary Range in settings");
		}
		echo json_encode($data);

	}

	//process
	//*get all timerecords that is not yet in time record summary
	//*get work schedule of employee. on workorder also will be determined if the employee is fixed or flexi
	//if employee has work schedule, do the computations
	//if the employee does not have, check if the employee has work order. if there is workorder, override the current timelog of employee else
	//after generating all timelogs using the NOT IN query, also generate the workorder using NOT IN formula on workorder

	public function Get_timerecord(){
		//unset data for saving trs
		$trs_data = $this->session->userdata('Timerecord_array');
		if($trs_data != null){
			$this->session->unset_userdata('Timerecord_array');
		}
		$current_day = $this->input->get('day');
		$current_date = today();
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$search_id = $this->input->get('search');
		// $start_date = '2019-02-22';
		// $end_date = '2019-02-26';

		//prevents on generating current date
		if($start_date == $current_date){
			$start_date = date('Y-m-d',strtotime('-1 day'));
		}
		if($end_date == $current_date){
			$end_date = date('Y-m-d',strtotime('-1 day'));
		}
		$trs_array = array();
		while(strtotime($start_date) <= strtotime($end_date)){
			$getemp = $this->Timerecordsummary_model->get_employee_timelog_v2($search_id);
			if($getemp->num_rows() > 0){
				foreach($getemp->result() as $ge){
					$employee_idno = $ge->employee_idno;
					$date_timelog = $start_date;
					// echo "<pre>";
					// print_r($date_timelog);
					$purpose = "others";
					$absent = 0;
					$remarks = 0;
					$fti_data = $this->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
					if($fti_data != null){
						$fti = $fti_data->time_in;
					}else{
						$fti = 0;
					}
					$lto_data = $this->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
					if($lto_data != null){
						$lto = $lto_data->time_out;
					}else{
						$lto = 0;
					}
					//check if absent
					$check_absent = fetch_absent($employee_idno,$date_timelog,$purpose);
					if($check_absent == 0){
						$check_trs = $this->Timerecordsummary_model->check_trs($employee_idno,$date_timelog)->row();
						if($check_trs != null){
							$late = $check_trs->late;
							$overtime = $check_trs->overtime;
							$undertime = $check_trs->undertime;
							$overbreak = $check_trs->overbreak;
							$manhours = $check_trs->man_hours;
							$totalminutes = $check_trs->total_minutes;
							$remarks = $check_trs->remarks;
						}else{
							$late = compute_late($employee_idno,$date_timelog,$purpose);
							$overtime = compute_overtime($employee_idno,$date_timelog,$purpose);
							$undertime = compute_undertime($employee_idno,$date_timelog,$purpose);
							$overbreak = compute_overbreak($employee_idno,$date_timelog,$purpose);
							$manhours = compute_manhours($employee_idno,$date_timelog,$purpose);
							$totalminutes = compute_totalminutes($employee_idno,$date_timelog,$purpose);
							$remarks = get_remarks($employee_idno,$date_timelog,$purpose);
						}
						//checks the major values. if null, employee is marked absent due to timelog misbehaviors
						if($late != "" || $overtime != "" || $undertime != "" || $totalminutes != "" || $manhours != "" || $overbreak != ""){
							$insertdata =
							array(
								'employee_idno' => $employee_idno,
								'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
								'date_created' => $date_timelog,
								'time_in' => $fti,
								'time_out' => $lto,
								'late' => $late,
								'overtime' =>$overtime,
								'undertime' =>$undertime,
								'absent' => $check_absent,
								'totalminutes' => $totalminutes,
								'manhours' => $manhours,
								'overbreak' => $overbreak,
								'remarks' => $remarks
								);
							array_push($trs_array,$insertdata);
						}else{
							//Employee did not timelog but there is holiday, so employee is present
							//check if holiday is 1. if 1, then employee is present, else, employee is on his DAY OFF
							$remarks = get_remarks($employee_idno,$date_timelog,$purpose);
							//check employee's first timelog to avoid generating holidays with no employee attendance
							$validate_timelog = validate_holidays($remarks,$employee_idno,$date_timelog);
							if($validate_timelog == "counted"){
							$insertdata =
							array(
								'employee_idno' => $employee_idno,
								'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
								'date_created' => $date_timelog,
								'time_in' => 0,
								'time_out' => 0,
								'late' => 0,
								'overtime' => 0,
								'undertime' => 0,
								'absent' => 0,
								'totalminutes' => 0,
								'manhours' => 0,
								'overbreak' => 0,
								'remarks' => $remarks
								);
							array_push($trs_array,$insertdata);
							}

							// check leave
							$check_leave = $this->Timerecordsummary_model->get_leave($date_timelog,$employee_idno);
							if($check_leave->num_rows() > 0){
								$insertdata =
								array(
									'employee_idno' => $employee_idno,
									'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
									'date_created' => $date_timelog,
									'time_in' => 0,
									'time_out' => 0,
									'late' => 0,
									'overtime' => 0,
									'undertime' => 0,
									'absent' => 0,
									'totalminutes' => 0,
									'manhours' => 0,
									'overbreak' => 0,
									'remarks' => $remarks
									);
								array_push($trs_array,$insertdata);
							}
						}
					}else if($check_absent == 1){
						// employee is absent
						//check remarks if holiday. 1 is equivalent to holiday. if holiday, employee is not absent
						$remarks = get_remarks($employee_idno,$date_timelog,$purpose);
						if($remarks == 1){
							$check_absent = 0;
						}
						$absentval = 0;
							$insertdata =
							array(
								'employee_idno' => $employee_idno,
								'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
								'date_created' => $date_timelog,
								'time_in' => 0,
								'time_out' => 0,
								'late' => $absentval,
								'overtime' =>$absentval,
								'undertime' => $absentval,
								'absent' => $check_absent,
								'totalminutes' => $absentval,
								'manhours' => $absentval,
								'overbreak' => $absentval,
								'remarks' => $remarks
								);
							array_push($trs_array,$insertdata);
					}
				}
			}
				$start_date = date ("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		}// end date loop
		//}

		//-----------DATATABLE--------------------
		//---------------------------------------

		// foreach ($trs_array as $key => $part) {
  //      		$sort[$key] = strtotime($part['date_created']);
  // 		}
  // 		array_multisort($sort, SORT_ASC, $trs_array);

		// echo "<pre>";
		// print_r($trs_array);
		// echo "</pre>";

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$order = $this->input->get('order');
		$column = array('trs.date_created','trs.employee_idno');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'date_created';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

        $trs_data = array("Timerecord_array" => $trs_array);
        //stores data in session
       $this->session->set_userdata($trs_data);
		$data = array(
			"draw" => $draw,
			"recordsTotal" => count($trs_array),
			"recordsFiltered" => count($trs_array),
			"data" => $trs_array,
		);
		echo json_encode($data);

	}
	public function save_data(){
		// $this->session->unset_userdata('Timerecord_array');
		$trs_data = $this->session->userdata('Timerecord_array');

		$checker = 0;
		$trs_checker = 0;
		if($trs_data != null){
			foreach($trs_data as $trs){
				$trs_checker++;
				$employee_idno = $trs['employee_idno'];
				$date_created = $trs['date_created'];
				$time_in = $trs['time_in'];
				$time_out = $trs['time_out'];
				$late = $trs['late'];
				$overtime = $trs['overtime'];
				$undertime = $trs['undertime'];
				$absent = $trs['absent'];
				$totalminutes = $trs['totalminutes'];
				$manhours = $trs['manhours'];
				$overbreak = $trs['overbreak'];
				$remarks = $trs['remarks'];

				//check time record summary to avoid data redundancy
				$check_trs = $this->Timerecordsummary_model->check_trs($employee_idno,$date_created)->row();

				if($check_trs != null){
					//do not insert
				}else{
					$inserdata = $this->Timerecordsummary_model->insertdata_workorder($employee_idno,$date_created,$time_in,$time_out,$late,$overtime,$undertime,$absent,$totalminutes,$manhours,$overbreak,$remarks);
					$checker++;
				}
			}
			if($checker > 0){
				if($checker == $trs_checker){
					$data = array('success' => 1,  'message' => 'All data has been successfully added!');
				}else{
					$data = array('success' => 1,  'message' => 'New Generated Data Saved!');
				}
			}else{
				$data = array('success' => 1,  'message' => 'All data has already added');
			}
		}else{
			$data = array('success' => 0,  'message' => 'Please Generate data before saving');
		}

		//unset session
			$this->session->unset_userdata('Timerecord_array');
			echo json_encode($data);

	}
	public function check_absent_record(){
		//wala pang validations
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$getemp = $this->Timerecordsummary_model->get_employee_timelog();
		if($getemp->num_rows() > 0){
			foreach($getemp->result() as $ge){
				$employee_idno = $ge->employee_idno;
				$check_absent_record = $this->Timerecordsummary_model->check_absent_record($employee_idno,$start_date,$end_date);
			}
		}

	}
	public function display_current_timerecord(){
		$current_day = $this->input->get('day');
		$current_date = today();
		$start_date = today();
		$end_date = today();
		$search_id = $this->input->get('search');
		// $start_date = '2019-02-22';
		// $end_date = '2019-02-26';

		//prevents on generating current date
		$trs_array = array();
		while(strtotime($start_date) <= strtotime($end_date)){
			$getemp = $this->Timerecordsummary_model->get_employee_timelog_v2($search_id);
			if($getemp->num_rows() > 0){
				foreach($getemp->result() as $ge){
					$employee_idno = $ge->employee_idno;
					$date_timelog = $start_date;
					// echo "<pre>";
					// print_r($date_timelog);
					$purpose = "others";
					$absent = 0;
					$remarks = 0;
					$fti_data = $this->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
					if($fti_data != null){
						$fti = $fti_data->time_in;
					}else{
						$fti = 0;
					}
					$lto_data = $this->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
					if($lto_data != null){
						$lto = $lto_data->time_out;
					}else{
						$lto = 0;
					}
					//check if absent
					$check_absent = fetch_absent($employee_idno,$date_timelog,$purpose);
					if($check_absent == 0){
						$late = compute_late($employee_idno,$date_timelog,$purpose);
						$overtime = compute_overtime($employee_idno,$date_timelog,$purpose);
						$undertime = compute_undertime($employee_idno,$date_timelog,$purpose);
						$overbreak = compute_overbreak($employee_idno,$date_timelog,$purpose);
						$manhours = compute_manhours($employee_idno,$date_timelog,$purpose);
						$totalminutes = compute_totalminutes($employee_idno,$date_timelog,$purpose);
						$remarks = get_remarks($employee_idno,$date_timelog,$purpose);
						//checks the major values. if null, employee is marked absent due to timelog misbehaviors
						if($late != "" || $overtime != "" || $undertime != "" || $totalminutes != "" || $manhours != "" || $overbreak != ""){
							$insertdata =
							array(
								'employee_idno' => $employee_idno,
								'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
								'date_created' => $date_timelog,
								'time_in' => $fti,
								'time_out' => $lto,
								'late' => $late,
								'overtime' =>$overtime,
								'undertime' =>$undertime,
								'absent' => $check_absent,
								'totalminutes' => $totalminutes,
								'manhours' => $manhours,
								'overbreak' => $overbreak,
								'remarks' => $remarks
								);
							array_push($trs_array,$insertdata);
						}else{
							//Employee did not timelog but there is holiday, so employee is present
							$remarks = get_remarks($employee_idno,$date_timelog,$purpose);
							$validate_timelog = validate_holidays($remarks,$employee_idno,$date_timelog);
							if($validate_timelog == "counted"){
								$insertdata =
								array(
									'employee_idno' => $employee_idno,
									'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
									'date_created' => $date_timelog,
									'time_in' => 0,
									'time_out' => 0,
									'late' => 0,
									'overtime' => 0,
									'undertime' => 0,
									'absent' => 0,
									'totalminutes' => 0,
									'manhours' => 0,
									'overbreak' => 0,
									'remarks' => $remarks
									);
								array_push($trs_array,$insertdata);
							}

							// check leave
							$check_leave = $this->Timerecordsummary_model->get_leave($date_timelog,$employee_idno);
							if($check_leave->num_rows() > 0){
								$insertdata =
								array(
									'employee_idno' => $employee_idno,
									'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
									'date_created' => $date_timelog,
									'time_in' => 0,
									'time_out' => 0,
									'late' => 0,
									'overtime' => 0,
									'undertime' => 0,
									'absent' => 0,
									'totalminutes' => 0,
									'manhours' => 0,
									'overbreak' => 0,
									'remarks' => $remarks
									);
								array_push($trs_array,$insertdata);
							}
						}
					}else if($check_absent == 1){
						// employee is absent
						//check remarks if holiday. 1 is equivalent to holiday. if holiday, employee is not absent
						$remarks = get_remarks($employee_idno,$date_timelog,$purpose);
						if($remarks == 1){
							$check_absent = 0;
						}
						$absentval = 0;
							$insertdata =
							array(
								'employee_idno' => $employee_idno,
								'employee_name' => $this->Timerecordsummary_model->get_employee_name($employee_idno),
								'date_created' => $date_timelog,
								'time_in' => 0,
								'time_out' => 0,
								'late' => $absentval,
								'overtime' =>$absentval,
								'undertime' => $absentval,
								'absent' => $check_absent,
								'totalminutes' => $absentval,
								'manhours' => $absentval,
								'overbreak' => $absentval,
								'remarks' => $absentval
								);
							array_push($trs_array,$insertdata);
					}
				}
			}
				$start_date = date ("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		}// end date loop
		//}

		//-----------DATATABLE--------------------
		//---------------------------------------

		// foreach ($trs_array as $key => $part) {
  //      		$sort[$key] = strtotime($part['date_created']);
  // 		}
  // 		array_multisort($sort, SORT_ASC, $trs_array);

		// echo "<pre>";
		// print_r($trs_array);
		// echo "</pre>";

		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$order = $this->input->get('order');
		$column = array('trs.date_created','trs.employee_idno');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'date_created';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => count($trs_array),
			"recordsFiltered" => count($trs_array),
			"data" => $trs_array,
		);
		echo json_encode($data);

	}
}
