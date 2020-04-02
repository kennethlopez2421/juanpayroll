<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_history extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('payroll/payroll_history_model');
		$this->load->model('payroll/payroll_model');
		$this->load->model('reports/compensation_reports_model');
	}

	//views
	public function index($token = "") {
		$getdept = $this->payroll_history_model->getDept()->result();
		$getpaytype = $this->payroll_history_model->getpaytype()->result();
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'get_department' => $getdept,
			'get_paytype' => $getpaytype,
			'banks' => $this->model->get_bank()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('payroll/payroll_history', $data);
	}

	//get data for data table
	public function get_payroll() {
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = array();
		$order = $this->input->get('order');
		$by_dept = $this->input->get('columns')[0]['search']['value'];
		$by_date = $this->input->get('columns')[1]['search']['value'];
		$by_paytype = $this->input->get('columns')[2]['search']['value'];
		if($by_dept != ""){
			$search['department'] = $by_dept;
		}
		if($by_date != ""){
			$search['dategenerated'] = $by_date;
		}
		if($by_paytype != ""){
			$search['paytype'] = $by_paytype;
		}

		$column = array('hps.created_at', 'hps.department', 'paytype_desc');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'hps.created_at';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->payroll_history_model->getpayrollsummary(null,null,null,null)->num_rows(),
			"recordsFiltered" => $this->payroll_history_model->getpayrollsummary(null,null,null,null)->num_rows(),
			"data" => $this->payroll_history_model->getpayrollsummary($start,$length,$search,$ordrBy)->result()
		);

		echo json_encode($data);


	}
							// 	manhours:manhours,
							// deduction:deduction,
							// additional:additional
	public function open_payroll_summary($token){
		$id = $this->input->post('get_id');
		$deduction = $this->input->post('deduction');
		$manhours = $this->input->post('manhours');
		$additional = $this->input->post('additional');
		$department = $this->input->post('department');
		$date_range = $this->input->post('date_range');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$paytype_desc = $this->input->post('paytype_desc');
		$idarray = array('manhours' => $manhours,'deduction' => $deduction,'additional' => $additional);
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'manhours' => $manhours,
			'additional' => $additional,
			'deduction' => $deduction,
			'payroll' => $id,
			'department' => $department,
			'date_range' => $date_range,
			'paytype_desc' => $paytype_desc,
			'date_from' => $date_from,
			'date_to' => $date_to
		);
		// print_r($token);
		$this->load->view('includes/header', $data);
		$this->load->view('payroll/payroll_history_summary', $data);

	}
	public function manhourstable(){
		$manhours_id = $this->input->get("manhours_id");
		$date_from = $this->input->get("date_from");
		$date_to = $this->input->get("date_to");
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
		$column = array("hml.emp_id","er.first_name","hml.days","hml.hours","hml.absent","hml.late","hml.ot","hml.ut");
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->payroll_history_model->getmanhours(null,null,null,null,$manhours_id,$date_from,$date_to)->num_rows(),
			"recordsFiltered" => $this->payroll_history_model->getmanhours(null,null,null,null,$manhours_id,$date_from,$date_to)->num_rows(),
			"data" => $this->payroll_history_model->getmanhours($start,$length,$search,$ordrBy,$manhours_id,$date_from,$date_to)->result()
		);

		echo json_encode($data);

	}
	public function deductionstable(){
		$deduction_id = $this->input->get("deduction_id");
		$date_from = $this->input->get("date_from");
		$date_to = $this->input->get("date_to");
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
			//id,name,sss,phil,pagibig,sd,ca
		$column = array("hdl.employee_idno","er.first_name","hdl.sss","hdl.philhealth","hdl.pag_ibig","hdl.cashadvance","hdl.salary_deduction");
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->payroll_history_model->getdeductions(null,null,null,null,$deduction_id,$date_from,$date_to)->num_rows(),
			"recordsFiltered" => $this->payroll_history_model->getdeductions(null,null,null,null,$deduction_id,$date_from,$date_to)->num_rows(),
			"data" => $this->payroll_history_model->getdeductions($start,$length,$search,$ordrBy,$deduction_id,$date_from,$date_to)->result()
		);

		echo json_encode($data);

	}
	public function additionalstable(){
		$additional_id = $this->input->get("additional_id");
		$date_from = $this->input->get("date_from");
		$date_to = $this->input->get("date_to");
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
			//id,name,sss,phil,pagibig,sd,ca
		$column = array("hal.emp_id","er.first_name","hal.overtime","hal.additionalpay");
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->payroll_history_model->getadditionals(null,null,null,null,$additional_id,$date_from,$date_to)->num_rows(),
			"recordsFiltered" => $this->payroll_history_model->getadditionals(null,null,null,null,$additional_id,$date_from,$date_to)->num_rows(),
			"data" => $this->payroll_history_model->getadditionals($start,$length,$search,$ordrBy,$additional_id,$date_from,$date_to)->result()
		);

		echo json_encode($data);

	}
	public function payroll_summary_table(){
		$payroll_id = $this->input->get("payroll_id");
		$date_from = $this->input->get("date_from");
		$date_to = $this->input->get("date_to");
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
			//id,name,sss,phil,pagibig,sd,ca
		$column = array("hpl.emp_id","er.first_name","hpl.gross_pay","hpl.deductions","hpl.additionals","hpl.net_pay");
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $this->payroll_history_model->getpayroll_logs(null,null,null,null,$payroll_id,$date_from,$date_to)->num_rows(),
			"recordsFiltered" => $this->payroll_history_model->getpayroll_logs(null,null,null,null,$payroll_id,$date_from,$date_to)->num_rows(),
			"data" => $this->payroll_history_model->getpayroll_logs($start,$length,$search,$ordrBy,$payroll_id,$date_from,$date_to)->result()
		);

		echo json_encode($data);

	}
	//--------------------------This will get all logs of tables-------------------
	public function getmanhourslogs(){
		//will get the values from time_record_summary
		$employee_idno = $this->input->post("employee_idno");
		$date_from = $this->input->post('fromdate');
		$date_to = $this->input->post('todate');
		$getmhl = $this->payroll_history_model->getmanhours_logs($employee_idno,$date_from,$date_to);
		// $getmhlres = $getmhl->result();
		if($getmhl > 0){
			$data = array('success' => 1, 'output' => $getmhl);
		}else{
			$data = array('success' => 1, 'output' => $getmhl);
		}

		// if($getmhl_num > 0){
		// 	$data = array('success' => 1, 'output' => $getmhlres);
		// }else{
		// 	$data = array('success' => 0, 'output' => "Data is empty.");
		// }
		echo json_encode($data);
	}
	public function getsalary_deduction(){
		$employee_idno = $this->input->post('employee_idno');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');

		$getsaldec = $this->payroll_history_model->getsalary_deduction($employee_idno,$date_from,$date_to);
		$saldecres = $getsaldec->result();
		$saldecnum = $getsaldec->num_rows();
		if($saldecnum > 0){
			$data = array('success' => 1, 'output' => $saldecres);
		}else{
			$data = array('success' => 0, 'output' => "Data is empty.");
		}
		echo json_encode($data);
	}
	public function getadditonal_pays(){
		$employee_idno = $this->input->post('employee_idno');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');

		$getadditionalpays = $this->payroll_history_model->getadditionalpays($employee_idno,$date_from,$date_to);
		$getadditionalpaysres = $getadditionalpays->result();
		$getadditionalpaysnum = $getadditionalpays->num_rows();

		if($getadditionalpaysnum > 0){
			$data = array('success' => 1, 'output' => $getadditionalpaysres);
		}else{
			$data = array('success' => 0, 'output' => 'Data is empty.');
		}
		echo json_encode($data);
	}
	public function getot_pays(){
		$employee_idno = $this->input->post('employee_idno');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');

		$getotpays = $this->payroll_history_model->getovertimepays($employee_idno,$date_from,$date_to);
		$getotpaysres = $getotpays->result();
		$getotpaysnum = $getotpays->num_rows();

		if($getotpaysnum > 0){
			$data = array('success' => 1, 'output' => $getotpaysres);
		}else{
			$data = array('success' => 0, 'output' => 'Data is empty');
		}
		echo json_encode($data);
	}

	public function getcashadvance_tran(){
		$employee_idno = $this->input->post('employee_idno');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');


		$getcashadvancetran = $this->payroll_history_model->getcashadvance_tran($employee_idno,$date_from,$date_to);
		$getcashadvancetranres = $getcashadvancetran->result();
		$getcashadvancetrannum = $getcashadvancetran->num_rows();

		if($getcashadvancetrannum > 0){
			$data = array('success' => 1, 'output' => $getcashadvancetranres);
		}else{
			$data = array('success' => 0, 'output' => 'Data is empty');
		}
		echo json_encode($data);
	}
	//-------------------------------end logs---------------------------
	//print payroll
public function print_payroll($token = '', $pono = ''){ //view specific summary
			$employee_idno = $this->input->post('employee_idno');
			$date_from = $this->input->post('date_from');
			$date_to = $this->input->post('date_to');
			$paytype_desc = $this->input->post('paytype_desc');

			$getpayroll_log = $this->payroll_history_model->print_payroll_log($employee_idno,$date_from,$date_to)->row();
			$getmanhours_log = $this->payroll_history_model->print_manhours_log($employee_idno,$date_from,$date_to)->row();
			$getadditional_log = $this->payroll_history_model->print_additionals_log($employee_idno,$date_from,$date_to)->row();
			$getdeduction_log = $this->payroll_history_model->print_deductions_log($employee_idno,$date_from,$date_to)->row();
			$getname = $this->payroll_history_model->getname($employee_idno)->row();
			$getlate = $this->payroll_history_model->print_late($date_from,$date_to,$employee_idno)->row();
			$getundertime = $this->payroll_history_model->print_undertime($date_from,$date_to,$employee_idno)->row();

			$getsalary = $this->payroll_history_model->gettotal_salary($employee_idno)->row();
			$getcontractdetails = $this->payroll_history_model->get_contract_details($employee_idno)->row();
			$getworkschedule = $this->payroll_history_model->getschedule($employee_idno)->row();
			//employee rates
			if($getcontractdetails != null){
				$frequency = $getcontractdetails->frequency;
			}else{
				$frequency = 4;
			}
			//getting work schedule FOR employee rates
			if($getworkschedule != null){
				$get_sched_day = json_decode($getworkschedule->work_sched);
				$days = array(($get_sched_day->sun[2]),
					($get_sched_day->mon[2]),
					($get_sched_day->tue[2]),
					($get_sched_day->wed[2]),
					($get_sched_day->thu[2]),
					($get_sched_day->fri[2]),
					($get_sched_day->sat[2]));

				for($x = 0; $x < 7; $x++){
					if($days[$x] != ""){
						if($x == 0){
							$totalhours = convert_to_hours($get_sched_day->sun[1]) - convert_to_hours($get_sched_day->sun[0]);
							$totalbreak = convert_to_hours($get_sched_day->sun[4]) - convert_to_hours($get_sched_day->sun[3]);
							break;
						}else if($x == 1){
							$totalhours = convert_to_hours($get_sched_day->mon[1]) - convert_to_hours($get_sched_day->mon[0]);
							$totalbreak = convert_to_hours($get_sched_day->mon[4]) - convert_to_hours($get_sched_day->mon[3]);
							break;
						}
						else if($x == 2){
							$totalhours = convert_to_hours($get_sched_day->tue[1]) - convert_to_hours($get_sched_day->tue[0]);
							$totalbreak = convert_to_hours($get_sched_day->tue[4]) - convert_to_hours($get_sched_day->tue[3]);
							break;
						}
						else if($x == 3){
							$totalhours = convert_to_hours($get_sched_day->wed[1]) - convert_to_hours($get_sched_day->wed[0]);
							$totalbreak = convert_to_hours($get_sched_day->wed[4]) - convert_to_hours($get_sched_day->wed[3]);
							break;
						}
						else if($x == 4){
							$totalhours = convert_to_hours($get_sched_day->thu[1]) - convert_to_hours($get_sched_day->thu[0]);
							$totalbreak = convert_to_hours($get_sched_day->thu[4]) - convert_to_hours($get_sched_day->thu[3]);
							break;
						}
						else if($x == 5){
							$totalhours = convert_to_hours($get_sched_day->fri[1]) - convert_to_hours($get_sched_day->fri[0]);
							$totalbreak = convert_to_hours($get_sched_day->fri[4]) - convert_to_hours($get_sched_day->fri[3]);

							break;
						}
						else if($x == 6){
							$totalhours = convert_to_hours($get_sched_day->sat[1]) - convert_to_hours($get_sched_day->sat[0]);
							$totalbreak = convert_to_hours($get_sched_day->sat[4]) - convert_to_hours($get_sched_day->sat[3]);

							break;
						}

					}
				}

			}else{
				$totalhours = 9;
				$totalbreak = 1;
			}
			//get minute and hourly rate
			if($getsalary != null){
				$totalsal = $getsalary->total_sal;
				$dr = compute_dailyrate($totalsal,$frequency);
				$minute_rate = compute_minute_rate($dr,$totalhours,$totalbreak);
				$hourly_rate = compute_hourlyrate($dr,$totalhours,$totalbreak);


			}else{
				$totalsal = 0;
				$dr = compute_dailyrate($totalsal,$frequency);
				$minute_rate = compute_minute_rate($dr,$totalhours,$totalbreak);
				$hourly_rate = compute_hourlyrate($dr,$totalhours,$totalbreak);


			}
			//get employee ws FOR holiday pays
			//count_holidays
			//count regular holidays
			if($getcontractdetails->emp_status == 4){
				$get_regular_holiday = $this->payroll_history_model->get_regular_holiday($date_from,$date_to);
				$grh_num = $get_regular_holiday->num_rows();
				//count special holidays
				$get_special_holiday = $this->payroll_history_model->get_special_holiday($date_from,$date_to);
				$gsh_num = $get_special_holiday->num_rows();
			}else{
				$grh_num = 0;
				$gsh_num = 0;
			}
			//---------------------------------regular holiday adder-----------------------------------------
			//-----------------------------------------------------------------------------------------------
			$regular_holiday_result = $this->payroll_history_model->get_regular_holiday($date_from,$date_to)->result();
			// print_r($timerecord_result);
			$regular_holiday_pays = 0;
			foreach($regular_holiday_result as $tr):
				$date_timelog = $tr->date;
				$date_timelog_day = date('w', strtotime($date_timelog));
				if($date_timelog_day == 0){
					if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->sun[0] == "") || ($get_sched_day->sun[1] == "") || ($get_sched_day->sun[4] == "") || ($get_sched_day->sun[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 1){
					if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->mon[0] == "") || ($get_sched_day->mon[1] == "") || ($get_sched_day->mon[4] == "") || ($get_sched_day->mon[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 2){
					if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->tue[0] == "") || ($get_sched_day->tue[1] == "") || ($get_sched_day->tue[4] == "") || ($get_sched_day->tue[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 3){
					if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->wed[0] == "") || ($get_sched_day->wed[1] == "") || ($get_sched_day->wed[4] == "") || ($get_sched_day->wed[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 4){
					if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->thu[0] == "") || ($get_sched_day->thu[1] == "") || ($get_sched_day->thu[4] == "") || ($get_sched_day->thu[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 5){
					if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->fri[0] == "") || ($get_sched_day->fri[1] == "") || ($get_sched_day->fri[4] == "") || ($get_sched_day->fri[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 6){
					if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->sat[0] == "") || ($get_sched_day->sat[1] == "") || ($get_sched_day->sat[4] == "") || ($get_sched_day->sat[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
			endforeach;
			//---------------------------------special holiday adder-----------------------------------------
			//-----------------------------------------------------------------------------------------------
			$special_holiday_result = $this->payroll_history_model->get_special_holiday($date_from,$date_to)->result();
			// print_r($timerecord_result);
			$special_holiday_pays = 0;
			foreach($special_holiday_result as $tr):
				$date_timelog_special = $tr->date;
				$date_timelog_day = date('w', strtotime($date_timelog_special));
				if($date_timelog_day == 0){
					if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->sun[0] == "") || ($get_sched_day->sun[1] == "") || ($get_sched_day->sun[4] == "") || ($get_sched_day->sun[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 1){
					if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->mon[0] == "") || ($get_sched_day->mon[1] == "") || ($get_sched_day->mon[4] == "") || ($get_sched_day->mon[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 2){
					if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->tue[0] == "") || ($get_sched_day->tue[1] == "") || ($get_sched_day->tue[4] == "") || ($get_sched_day->tue[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 3){
					if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->wed[0] == "") || ($get_sched_day->wed[1] == "") || ($get_sched_day->wed[4] == "") || ($get_sched_day->wed[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 4){
					if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->thu[0] == "") || ($get_sched_day->thu[1] == "") || ($get_sched_day->thu[4] == "") || ($get_sched_day->thu[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 5){
					if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->fri[0] == "") || ($get_sched_day->fri[1] == "") || ($get_sched_day->fri[4] == "") || ($get_sched_day->fri[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 6){
					if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->sat[0] == "") || ($get_sched_day->sat[1] == "") || ($get_sched_day->sat[4] == "") || ($get_sched_day->sat[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
			endforeach;
			//get_employee_holidays

			// print_r($minute_rate);
			// print_r($hourly_rate);
			if($getsalary != null){
				$salary_emp = $getsalary->total_sal;
			}else{
				$salary_emp = 0;
			}
			if($getname != null){
				$name = $getname->first_name." ".$getname->middle_name." ".$getname->last_name;
			}else{
				$name = "No name found";
			}
			$data = array(
				'token' =>$token,
				'getmanhours_log' => $getmanhours_log,
				'getpayroll_log' => $getpayroll_log,
				'getadditional_log' => $getadditional_log,
				'getdeduction_log' => $getdeduction_log,
				'employee_idno' => $employee_idno,
				'name' => $name,
				'late' => $getlate,
				'undertime' => $getundertime,
				'date_from' => $date_from,
				'date_to' => $date_to,
				'paytype_desc' => $paytype_desc,
				'salary_emp' => $salary_emp,
				'minute_rate' =>$minute_rate,
				'hourly_rate' =>$hourly_rate,
				'daily_rate' =>$dr,
				'regular_holiday' => $grh_num,
				'special_holiday' => $gsh_num,
				'regular_holiday_pays' => $regular_holiday_pays,
				'special_holiday_pays' => $special_holiday_pays
			);


            $header = $this->load->view('includes/print_header' ,$data, true);
			$page = $this->load->view('payroll/print_payroll' ,$data, true);

			$this->load->library('Pdf');
			//tcpdf();
			$obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
			$obj_pdf->SetCreator(PDF_CREATOR);
			$title = "Payslip";
			$obj_pdf->SetTitle($title);
			$obj_pdf->SetDefaultMonospacedFont('helvetica');
			$obj_pdf->SetFont('helvetica', '', 9);
			$obj_pdf->setFontSubsetting(false);
			$obj_pdf->setPrintHeader(false);
			$obj_pdf->AddPage();
			$obj_pdf->setCellPaddings(0,0,0,0);


			ob_start();
			// we can have any view part here like HTML, PHP etc

            $obj_pdf->writeHTML($header, true, false, true, false, '');
            // $obj_pdf->writeHTML($page, true, false, true, false, '');


			$style = array(
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
			);

			//QRCODE,H : QR-CODE Best error correction
			$obj_pdf->write2DBarcode('PO_'.$pono, 'QRCODE,H', 170, 15, 17, 17, $style, 'N');

			echo $page;
			$content = ob_get_contents();

			$obj_pdf->writeHTML($content, true, false, true, false, '');

			ob_end_clean();
			$obj_pdf->Output("PO".$pono.".pdf", 'I');

        }
      public function fetch_payroll_logs(){
    // 	$p_dept = $this->input->post('p_dept');
    	$date_from = $this->input->post('date_from');
    	$date_to = $this->input->post('date_to');
    	$employee_idno = $this->input->post('employee_idno');

    	print_r($date_from);
    	print_r($date_to);
    	print_r($employee_idno);


    // 	$p_paytype = $this->input->post('p_paytype');
    // 	$p_paytype_range = $this->input->post('p_paytype_range');
    // 	$p_paytype_frequency = $this->input->post('p_paytype_frequency');

    // 	$data = $this->payroll_model->get_payroll_log($p_dept,$p_date_from,$p_date_to,$p_paytype,$p_paytype_frequency);
    // // print_r($data['data']);
    // 	$payroll_arr = array(
    //   	"payroll" => $data['data'],
    //   	"p_dept" => $p_dept,
    //   	"p_date_from" => $p_date_from,
    //   	"p_date_to" => $p_date_to,
    //   	"p_paytype" => $p_paytype
    // 	);
    // 	echo json_encode($data);
  	}

  	public function approve_payroll(){
  		$payroll_id = $this->input->post("payroll_id");
			$payroll_refno = $this->input->post('payroll_refno');
  		$additionals_id = $this->input->post("additionals_id");
  		$deductions_id = $this->input->post("deductions_id");
  		$manhours_id = $this->input->post("manhours_id");

			// die($payroll_refno);

  		if($payroll_id != "" || $additionals_id != "" || $deductions_id != "" || $manhours_id != ""){
  			$app_p_summary = $this->payroll_history_model->approve_payroll_summary($payroll_id);
  			$app_m_summary = $this->payroll_history_model->approve_manhours_summary($manhours_id);
  			$app_d_summary = $this->payroll_history_model->approve_deductions_summary($deductions_id);
  			$app_a_summary = $this->payroll_history_model->approve_additionals_summary($additionals_id);

  			$app_p_log = $this->payroll_history_model->approve_payroll_log($payroll_id);
  			$app_m_log = $this->payroll_history_model->approve_manhours_log($manhours_id);
  			$app_a_log = $this->payroll_history_model->approve_additionals_log($additionals_id);
  			$app_d_log = $this->payroll_history_model->approve_deductions_log($deductions_id);
  			$data = array('success' => 1, 'output' => "Payroll Successfully Approved");

				### tagging of additionals and deduction on payroll ###
				$tag_data = array($payroll_id);
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

				### SEND PAYROLL TO EMPLOYEE EMAIL ###
				$emails = $this->payroll_history_model->get_email_w_refno($payroll_refno);
				if($emails->num_rows() > 0){
					$this->load->library('email');
					foreach($emails->result_array() as $email){
						// die($email['email']);
						$token_fix = "CloudPandaPHInc";
						$hash_refno = removeSpecialchar(en_dec("en", $payroll_refno));
						$token_email = en_dec('en',$token_fix);
						$date = new Datetime($email['pay_day']);
						$payslip_data = array($email['company_id'],$email['fromdate'],$email['todate'],$email['paytype'],$email['paytype'],$email['pay_day'],$email['employee_idno'],$payroll_refno,$date->format('F d, Y'));
						$hash_payslip_data = en_dec('en', json_encode($payslip_data));
						// $payslip_data = $this->payroll_model->get_payslip();
						// print_r($payslip_data);
						// die();

						$email_data['date'] = $date->format('F d, Y');
						$email_data['fullname'] = $email['fullname'];
						$email_data['fromdate'] = $email['fromdate'];
						$email_data['todate'] = $email['todate'];
						$email_data['download_link'] = base_url('Main/download_payslip/'.$hash_refno.'/'.$token_email.'/'.removeSpecialchar($hash_payslip_data));

						$msg = $this->load->view('emails/payslip_email',$email_data,true);

						$this->email->from('support@cloudpanda.ph', 'One Payroll');
						$this->email->to($email['email']);

						$this->email->subject($date->format('F d, Y').' Payslip');
						$this->email->message($msg);
						$email = $this->email->send();
					}
				}

  		}else{
  			$data = array('success' => 0, 'output' => "An error has occured during the process");
  		}
  		echo json_encode($data);
  	}
	public function save_approved_payslip(){

		$payroll_summary_id = $this->input->post('payroll_id');
		$get_approved_payroll_employee = $this->payroll_history_model->get_approved_payroll_emp($payroll_summary_id);
		$allemp_res = $get_approved_payroll_employee->result();
		foreach($allemp_res as $ar):
			$employee_idno = $ar->emp_id;
			$date_from = $ar->fromdate;
			$date_to = $ar->todate;
			$paytype_desc = $ar->paytype;
			// print_r(array($employee_idno,$date_from,$date_to,$paytype_desc));
			// $employee_idno = $this->input->post('employee_idno');
			// $date_from = $this->input->post('date_from');
			// $date_to = $this->input->post('date_to');
			// $paytype_desc = $this->input->post('paytype_desc');

			$getpayroll_log = $this->payroll_history_model->print_payroll_log($employee_idno,$date_from,$date_to)->row();
			$getmanhours_log = $this->payroll_history_model->print_manhours_log($employee_idno,$date_from,$date_to)->row();
			// if($getmanhours_log != null){
			// 	$getmanhours =$getmanhours_log;
			// }else{
			// 	print_r('xxxxxxxx'.$employee_idno.'--');
			// }

			$getadditional_log = $this->payroll_history_model->print_additionals_log($employee_idno,$date_from,$date_to)->row();
			$getdeduction_log = $this->payroll_history_model->print_deductions_log($employee_idno,$date_from,$date_to)->row();
			$getname = $this->payroll_history_model->getname($employee_idno)->row();
			$getlate = $this->payroll_history_model->print_late($date_from,$date_to,$employee_idno)->row();
			$getundertime = $this->payroll_history_model->print_undertime($date_from,$date_to,$employee_idno)->row();

			$getsalary = $this->payroll_history_model->gettotal_salary($employee_idno)->row();
			$getcontractdetails = $this->payroll_history_model->get_contract_details($employee_idno)->row();
			$getworkschedule = $this->payroll_history_model->getschedule($employee_idno)->row();
			//employee rates
			if($getcontractdetails != null){
				$frequency = $getcontractdetails->frequency;
			}else{
				$frequency = 4;
			}
			//getting work schedule FOR employee rates
			if($getworkschedule != null){
				$get_sched_day = json_decode($getworkschedule->work_sched);
				$days = array(($get_sched_day->sun[2]),
					($get_sched_day->mon[2]),
					($get_sched_day->tue[2]),
					($get_sched_day->wed[2]),
					($get_sched_day->thu[2]),
					($get_sched_day->fri[2]),
					($get_sched_day->sat[2]));

				for($x = 0; $x < 7; $x++){
					if($days[$x] != ""){
						if($x == 0){
							$totalhours = convert_to_hours($get_sched_day->sun[1]) - convert_to_hours($get_sched_day->sun[0]);
							$totalbreak = convert_to_hours($get_sched_day->sun[4]) - convert_to_hours($get_sched_day->sun[3]);
							break;
						}else if($x == 1){
							$totalhours = convert_to_hours($get_sched_day->mon[1]) - convert_to_hours($get_sched_day->mon[0]);
							$totalbreak = convert_to_hours($get_sched_day->mon[4]) - convert_to_hours($get_sched_day->mon[3]);
							break;
						}
						else if($x == 2){
							$totalhours = convert_to_hours($get_sched_day->tue[1]) - convert_to_hours($get_sched_day->tue[0]);
							$totalbreak = convert_to_hours($get_sched_day->tue[4]) - convert_to_hours($get_sched_day->tue[3]);
							break;
						}
						else if($x == 3){
							$totalhours = convert_to_hours($get_sched_day->wed[1]) - convert_to_hours($get_sched_day->wed[0]);
							$totalbreak = convert_to_hours($get_sched_day->wed[4]) - convert_to_hours($get_sched_day->wed[3]);
							break;
						}
						else if($x == 4){
							$totalhours = convert_to_hours($get_sched_day->thu[1]) - convert_to_hours($get_sched_day->thu[0]);
							$totalbreak = convert_to_hours($get_sched_day->thu[4]) - convert_to_hours($get_sched_day->thu[3]);
							break;
						}
						else if($x == 5){
							$totalhours = convert_to_hours($get_sched_day->fri[1]) - convert_to_hours($get_sched_day->fri[0]);
							$totalbreak = convert_to_hours($get_sched_day->fri[4]) - convert_to_hours($get_sched_day->fri[3]);

							break;
						}
						else if($x == 6){
							$totalhours = convert_to_hours($get_sched_day->sat[1]) - convert_to_hours($get_sched_day->sat[0]);
							$totalbreak = convert_to_hours($get_sched_day->sat[4]) - convert_to_hours($get_sched_day->sat[3]);

							break;
						}
					}
				}

			}else{
				$totalhours = 9;
				$totalbreak = 1;
			}
			//get minute and hourly rate
			if($getsalary != null){
				$totalsal = $getsalary->total_sal;
				$dr = compute_dailyrate($totalsal,$frequency);
				$minute_rate = compute_minute_rate($dr,$totalhours,$totalbreak);
				$hourly_rate = compute_hourlyrate($dr,$totalhours,$totalbreak);


			}else{
				$totalsal = 0;
				$dr = compute_dailyrate($totalsal,$frequency);
				$minute_rate = compute_minute_rate($dr,$totalhours,$totalbreak);
				$hourly_rate = compute_hourlyrate($dr,$totalhours,$totalbreak);


			}
			//get employee ws FOR holiday pays
			//count_holidays
			//count regular holidays
			if($getcontractdetails->emp_status == 4){
				$get_regular_holiday = $this->payroll_history_model->get_regular_holiday($date_from,$date_to);
				$grh_num = $get_regular_holiday->num_rows();
				//count special holidays
				$get_special_holiday = $this->payroll_history_model->get_special_holiday($date_from,$date_to);
				$gsh_num = $get_special_holiday->num_rows();
			}else{
				$grh_num = 0;
				$gsh_num = 0;
			}
			//---------------------------------regular holiday adder-----------------------------------------
			//-----------------------------------------------------------------------------------------------
			$regular_holiday_result = $this->payroll_history_model->get_regular_holiday($date_from,$date_to)->result();
			// print_r($timerecord_result);
			$regular_holiday_pays = 0;
			foreach($regular_holiday_result as $tr):
				$date_timelog = $tr->date;
				$date_timelog_day = date('w', strtotime($date_timelog));
				if($date_timelog_day == 0){
					if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->sun[0] == "") || ($get_sched_day->sun[1] == "") || ($get_sched_day->sun[4] == "") || ($get_sched_day->sun[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 1){
					if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->mon[0] == "") || ($get_sched_day->mon[1] == "") || ($get_sched_day->mon[4] == "") || ($get_sched_day->mon[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 2){
					if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->tue[0] == "") || ($get_sched_day->tue[1] == "") || ($get_sched_day->tue[4] == "") || ($get_sched_day->tue[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 3){
					if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->wed[0] == "") || ($get_sched_day->wed[1] == "") || ($get_sched_day->wed[4] == "") || ($get_sched_day->wed[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 4){
					if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->thu[0] == "") || ($get_sched_day->thu[1] == "") || ($get_sched_day->thu[4] == "") || ($get_sched_day->thu[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 5){
					if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->fri[0] == "") || ($get_sched_day->fri[1] == "") || ($get_sched_day->fri[4] == "") || ($get_sched_day->fri[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 6){
					if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_regular->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else if(($get_sched_day->sat[0] == "") || ($get_sched_day->sat[1] == "") || ($get_sched_day->sat[4] == "") || ($get_sched_day->sat[3]))
					{
						$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
							if($getpayratio2_regular != null){
								$p2 = $getpayratio2_regular->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$regular_holiday_pays = $regular_holiday_pays + $x;

					}
					else{
						$regular_holiday_pays = $regular_holiday_pays + 0;
					}
				}
			endforeach;
			//---------------------------------special holiday adder-----------------------------------------
			//-----------------------------------------------------------------------------------------------
			$special_holiday_result = $this->payroll_history_model->get_special_holiday($date_from,$date_to)->result();
			// print_r($timerecord_result);
			$special_holiday_pays = 0;
			foreach($special_holiday_result as $tr):
				$date_timelog_special = $tr->date;
				$date_timelog_day = date('w', strtotime($date_timelog_special));
				if($date_timelog_day == 0){
					if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->sun[0] == "") || ($get_sched_day->sun[1] == "") || ($get_sched_day->sun[4] == "") || ($get_sched_day->sun[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 1){
					if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->mon[0] == "") || ($get_sched_day->mon[1] == "") || ($get_sched_day->mon[4] == "") || ($get_sched_day->mon[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 2){
					if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->tue[0] == "") || ($get_sched_day->tue[1] == "") || ($get_sched_day->tue[4] == "") || ($get_sched_day->tue[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 3){
					if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->wed[0] == "") || ($get_sched_day->wed[1] == "") || ($get_sched_day->wed[4] == "") || ($get_sched_day->wed[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 4){
					if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->thu[0] == "") || ($get_sched_day->thu[1] == "") || ($get_sched_day->thu[4] == "") || ($get_sched_day->thu[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 5){
					if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->fri[0] == "") || ($get_sched_day->fri[1] == "") || ($get_sched_day->fri[4] == "") || ($get_sched_day->fri[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
				else if($date_timelog_day == 6){
					if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
					{
						//may pasok - payratio 1
						$getpayratio1_special = $this->payroll_history_model->get_special_payratio1()->row();
							if($getpayratio1_regular != null){
								$p1 = $getpayratio1_special->payratio;
							}else{
								$p1 = 2;
							}
						$x = ($dr * $p1) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else if(($get_sched_day->sat[0] == "") || ($get_sched_day->sat[1] == "") || ($get_sched_day->sat[4] == "") || ($get_sched_day->sat[3]))
					{
						$getpayratio2_special = $this->payroll_history_model->get_special_payratio2()->row();
							if($getpayratio2_special != null){
								$p2 = $getpayratio2_special->payratio2;
							}else{
								$p2 = 2;
							}
						$x = ($dr * $p2) - $dr;
						$special_holiday_pays = $special_holiday_pays + $x;

					}
					else{
						$special_holiday_pays = $special_holiday_pays + 0;
					}
				}
			endforeach;
			//get_employee_holidays
			if($getsalary != null){
				$salary_emp = $getsalary->total_sal;
			}else{
				$salary_emp = 0;
			}
			if($getname != null){
				$name = $getname->first_name." ".$getname->middle_name." ".$getname->last_name;
			}else{
				$name = "No name found";
			}

				// $getmanhours_log = $getmanhours_log;
				// $getpayroll_log = $getpayroll_log;
				// $getadditional_log = $getadditional_log;
				// $getdeduction_log = $getdeduction_log;
				// $employee_idno = $employee_idno;
				// $name = $name;
				$late = $getlate;
				$undertime = $getundertime;
				// $date_from = $date_from;
				// $date_to = $date_to;
				// $paytype_desc = $paytype_desc;
				// $salary_emp = $salary_emp;
				// $minute_rate = $minute_rate;
				// $hourly_rate = $hourly_rate;
				$daily_rate = $dr;
				$regular_holiday = $grh_num;
				$special_holiday = $gsh_num;
				// $regular_holiday_pays = $regular_holiday_pays;
				// $special_holiday_pays = $special_holiday_pay;


				//----------------------------Payroll Computations------------------
				//-------------------------------------------------------------

				//Gross Salary
				//Days
				if($getpayroll_log != null){
					$approved_gross_salary = $getpayroll_log->grosspay;
				}else{
					$approved_gross_salary = 0;
				}
				if($getmanhours_log != null){
					$days_duration = $getmanhours_log->days;
				}else{
					$days_duration = 0;
				}

				// //Overtime
				if($getadditional_log != null){
					$approved_overtime = $getadditional_log->overtimepay;
				}else{
					$approved_overtime = 0;
				}
				if($getmanhours_log != null){
					$ot_duration = $getmanhours_log->ot;
				}else{
					$ot_duration = 0;
				}
				// //Approved additionals
				if($getpayroll_log != null){
					$approved_additionals = $getpayroll_log->additionals;
				}else{
					$approved_additionals = 0;
				}
				// //Regular Holiday
				$regular_minutes = $regular_holiday;
				if($paytype_desc == 'Weekly'){
					$regular_rate = ($regular_minutes * $daily_rate) + $regular_holiday_pays;
				}else{
					$regular_rate = $regular_holiday_pays;
				}
				$approved_regular_holiday = $regular_rate;
				$regular_holiday_duration = $regular_holiday;
				// //Special Holiday
				$special_mins = $special_holiday;
				if($paytype_desc == 'Weekly'){
					$special_rate = ($special_mins * $daily_rate) + $special_holiday_pays;
				}else{
					$special_rate = $special_holiday_pays;
				}
				$approved_special_holiday =  $special_rate;
				$special_holiday_duration = $special_holiday;
				// //Sundays(days)
				if($getmanhours_log != null){
					$sunday_days = $getmanhours_log->sunday;
					$sunday_rate = $daily_rate * $sunday_days;
					$approved_sundays = $sunday_rate;
					$sunday_duration = $getmanhours_log->sunday;
				}else{
					$approved_sundays = 0;
					$sunday_duration = 0;
				}
				// //Penalties
				// //Absent
				if($getmanhours_log != null){
					$absent_days = $getmanhours_log->absent;
					$absent_rate = $daily_rate * $absent_days;
					$approved_absent = $absent_rate;
					$absent_duration = $getmanhours_log->absent;
				}else{
					$approved_absent = 0;
					$absent_duration = 0;
				}

				// //Late
				if($getmanhours_log != null){
					$late_mins = $getmanhours_log->late;
					$late_rate = $minute_rate * $late_mins;
					$approved_late = $late_rate;
					$late_duration = $getmanhours_log->late;
				}else{
					$approved_late = 0;
					$late_duration = 0;
				}

				// //Undertime
				if($getmanhours_log != null){
					$ut_mins = $getmanhours_log->ut;
					$ut_rate = $minute_rate * $ut_mins;
					$approved_undertime = $ut_rate;
					$undertime_duration = $getmanhours_log->ut;
				}else{
					$approved_undertime = 0;
					$undertime_duration = 0;
				}

				// //Salary Deductions
				if($getdeduction_log != null){
					$approved_sss = $getdeduction_log->sss;
					$approved_philhealth = $getdeduction_log->philhealth;
					$approved_pag_ibig = $getdeduction_log->pag_ibig;
					$approved_sss_loan = $getdeduction_log->sss_loan;
					$approved_pag_ibig_loan = $getdeduction_log->pag_ibig_loan;
					$approved_cashadvance = $getdeduction_log->cashadvance;
					$approved_salary_deduction = $getdeduction_log->salary_deduction;
					//Total Deductions
					$total = $approved_sss + $approved_philhealth + $approved_pag_ibig + $approved_sss_loan + $approved_pag_ibig_loan + $approved_cashadvance + $approved_salary_deduction;
					$approved_total_deductions = $total;
					//net pay
					$netpay = $getpayroll_log->netpay;
				}else{
					$approved_total_deductions = 0;
					$netpay = 0;
				}

				//insert data to database
				// $insertdata =array(
				// 	'employee_idno' =>$employee_idno,
				// 	'name' =>$name,
				// 	'paytype_desc' =>$paytype_desc,
				// 	'date_from' =>$date_from,
				// 	'date_to' =>$date_to,
				// 	'approved_gross_salary' =>$approved_gross_salary,
				// 	'days_duration' =>$days_duration,
				// 	'approved_overtime' =>$approved_overtime,
				// 	'ot_duration' =>$ot_duration,
				// 	'approved_additionals' =>$approved_additionals,
				// 	'approved_regular_holiday' =>$approved_regular_holiday,
				// 	'regular_holiday_duration' =>$regular_holiday_duration,
				// 	'approved_special_holiday' =>$approved_special_holiday,
				// 	'special_holiday_duration' =>$special_holiday_duration,
				// 	'approved_sundays' =>$approved_sundays,
				// 	'sunday_duration' =>$sunday_duration,
				// 	'approved_absent' =>$approved_absent,
				// 	'absent_duration' =>$absent_duration,
				// 	'approved_late' =>$approved_late,
				// 	'late_duration' =>$late_duration,
				// 	'approved_undertime' =>$approved_undertime,
				// 	'undertime_duration' =>$undertime_duration,
				// 	'approved_sss' =>$approved_sss,
				// 	'approved_philhealth' =>$approved_philhealth,
				// 	'approved_pag_ibig' =>$approved_pag_ibig,
				// 	'approved_sss_loan' =>$approved_sss_loan,
				// 	'approved_pag_ibig_loan' =>$approved_pag_ibig_loan,
				// 	'approved_cashadvance' =>$approved_cashadvance,
				// 	'approved_salary_deduction' =>$approved_salary_deduction,
				// 	'approved_total_deductions' =>$approved_total_deductions,
				// 	'netpay' =>$netpay
				// );

				$insertdata =$this->payroll_history_model->insert_approved_payslip(
					$employee_idno,
					$name,
					$paytype_desc,
					$date_from,
					$date_to,
					$approved_gross_salary,
					$days_duration,
					$approved_overtime,
					$ot_duration,
					$approved_additionals,
					$approved_regular_holiday,
					$regular_holiday_duration,
					$approved_special_holiday,
					$special_holiday_duration,
					$approved_sundays,
					$sunday_duration,
					$approved_absent,
					$absent_duration,
					$approved_late,
					$late_duration,
					$approved_undertime,
					$undertime_duration,
					$approved_sss,
					$approved_philhealth,
					$approved_pag_ibig,
					$approved_sss_loan,
					$approved_pag_ibig_loan,
					$approved_cashadvance,
					$approved_salary_deduction,
					$approved_total_deductions,
					$netpay
				);


				// print_r($insertdata);
			endforeach;
    }
   	public function reject_payroll(){
  		$payroll_id = $this->input->post("payroll_id");
  		$additionals_id = $this->input->post("additionals_id");
  		$deductions_id = $this->input->post("deductions_id");
  		$manhours_id = $this->input->post("manhours_id");


  		if($payroll_id != "" || $additionals_id != "" || $deductions_id != "" || $manhours_id != ""){

  			$app_p_log = $this->payroll_history_model->remove_payroll_log($payroll_id);
  			$app_m_log = $this->payroll_history_model->remove_manhours_log($manhours_id);
  			$app_a_log = $this->payroll_history_model->remove_additionals_log($additionals_id);
  			$app_d_log = $this->payroll_history_model->remove_deductions_log($deductions_id);

   			$app_p_summary = $this->payroll_history_model->remove_payroll_summary($payroll_id);
  			$app_m_summary = $this->payroll_history_model->remove_manhours_summary($manhours_id);
  			$app_d_summary = $this->payroll_history_model->remove_deductions_summary($deductions_id);
  			$app_a_summary = $this->payroll_history_model->remove_additionals_summary($additionals_id);
  			$data = array('success' => 1, 'output' => "Payroll Successfully Rejected");
				$this->session->unset_userdata('ca_payment');
  		}else{
  			$data = array('success' => 0, 'output' => "An error has occured during the process");
  		}
  		echo json_encode($data);
  	}

	public function destroy() {
		$id = $this->input->post('id');

		$data = array(0,$id);

		$this->cashadvance_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		echo json_encode($data);

	}



}
