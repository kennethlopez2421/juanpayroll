<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payroll_history_model extends CI_Model {

	public function get_email_w_refno($refno){
		$refno = $this->db->escape($refno);
		$sql = "SELECT a.email, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
		 c.pay_day, c.fromdate, c.todate, c.company_id, c.paytype, a.employee_idno,
		 d.description as paytype_desc, d.frequency
		 FROM employee_record a
		 INNER JOIN hris_payroll_log b ON a.employee_idno = b.emp_id
		 INNER JOIN hris_payroll_summary c ON b.payroll_summary_id = c.id
		 LEFT JOIN paytype d ON c.paytype = d.paytypeid
		 WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND c.status = 'approved'
		 AND c.ref_no = $refno";
		return $this->db->query($sql);
	}

	public function getpayrollsummary($start,$length,$search,$ordrBy){
		if($start != null && $length != null){
			if($search != null){
				 if(sizeof($search) > 0){
					if(array_key_exists('department',$search) && $search['department'] != ""){
							$sql = "SELECT hc.company, hps.id as pay_id, hps.ref_no as payroll_refno, hps.manhours_id,hps.deduction_id,hps.additional_id,hps.created_at,hps.department_id,hps.fromdate,hps.todate,hps.status, d.description as dept_name, p.description as paytype_desc FROM hris_payroll_summary as hps
							LEFT JOIN department as d
							ON d.departmentid = hps.department_id
							LEFT JOIN hris_companies hc ON hps.company_id = hc.id
							LEFT JOIN paytype as p
							ON hps.paytype = p.paytypeid
							WHERE hps.department_id = ".$this->db->escape_like_str($search['department'])." AND hps.enabled = 1";
						}
					else if(array_key_exists('dategenerated',$search) && $search['dategenerated'] != ""){
							$sql = "SELECT hc.company, hps.id as pay_id, hps.ref_no as payroll_refno, hps.manhours_id,hps.deduction_id,hps.additional_id,hps.created_at,hps.department_id,hps.fromdate,hps.todate,hps.status, d.description as dept_name, p.description as paytype_desc FROM hris_payroll_summary as hps
							LEFT JOIN department as d
							ON d.departmentid = hps.department_id
							LEFT JOIN hris_companies hc ON hps.company_id = hc.id
							LEFT JOIN paytype as p
							ON hps.paytype = p.paytypeid
							WHERE hps.created_at LIKE '%".$this->db->escape_like_str($search['dategenerated'])."%' AND hps.enabled = 1";
							// print_r('xxxx');
						}
					else if(array_key_exists('paytype',$search) && $search['paytype'] != ""){
							$sql = "SELECT hc.company, hps.id as pay_id, hps.ref_no as payroll_refno, hps.manhours_id,hps.deduction_id,hps.additional_id,hps.created_at,hps.department_id,hps.fromdate,hps.todate,hps.status, d.description as dept_name, p.description as paytype_desc FROM hris_payroll_summary as hps
							LEFT JOIN department as d
							ON d.departmentid = hps.department_id
							LEFT JOIN hris_companies hc ON hps.company_id = hc.id
							LEFT JOIN paytype as p
							ON p.paytypeid = hps.paytype
							WHERE hps.paytype = ".$this->db->escape_like_str($search['paytype'])." AND hps.enabled = 1";
						}
				}
				//end if
			}else{
				$sql = "SELECT hc.company, hps.id as pay_id, hps.ref_no as payroll_refno, hps.manhours_id,hps.deduction_id,hps.additional_id,hps.created_at,hps.department_id,hps.fromdate,hps.todate,hps.status, d.description as dept_name, p.description as paytype_desc FROM hris_payroll_summary as hps
					LEFT JOIN department as d
					ON d.departmentid = hps.department_id
					LEFT JOIN hris_companies hc ON hps.company_id = hc.id
					LEFT JOIN paytype as p
					ON hps.paytype = p.paytypeid
					WHERE hps.enabled = 1";
			}
		}else{
			$sql = "SELECT hc.company, hps.id as pay_id, hps.ref_no as payroll_refno, hps.manhours_id,hps.deduction_id,hps.additional_id,hps.created_at,hps.department_id,hps.fromdate,hps.todate,hps.status, d.description as dept_name, p.description as paytype_desc FROM hris_payroll_summary as hps
				LEFT JOIN department as d
				ON d.departmentid = hps.department_id
				LEFT JOIN hris_companies hc ON hps.company_id = hc.id
				LEFT JOIN paytype as p
				ON hps.paytype = p.paytypeid
				";
		}
		return $this->db->query($sql);
	}

	public function getDept(){
		$sql = "SELECT * FROM department WHERE enabled = 1";
		return $this->db->query($sql);
	}
	public function getpaytype(){
		$sql = "SELECT * FROM paytype WHERE enabled = 1";
		return $this->db->query($sql);
	}
	// hml.emp_id,hml.days,hml.hours,hml.absent,hml.ot,hml.ut

	public function getmanhours($start,$length,$search,$ordrBy,$manhours_id,$date_from,$date_to){
		if($start != null && $length != null){
			if($search != null){
				$sql = "SELECT hml.id,hml.emp_id,hml.days,hml.hours,hml.absent,hml.late,hml.ot,hml.ut, er.first_name,er.middle_name, er.last_name FROM
				hris_manhours_summary as hms
				LEFT JOIN hris_manhours_log as hml
				ON hml.manhours_summary_id = hms.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hml.emp_id
				WHERE concat(hml.emp_id,er.first_name,er.middle_name,er.last_name)
				LIKE '%".$this->db->escape_like_str($search)."%'
				AND hml.fromdate = '".$date_from."'
				AND hml.todate  = '".$date_to."'
				AND hml.manhours_summary_id = ".$manhours_id."
				AND hml.enabled = 1
				LIMIT ".$start.",".$length."
				";
			}else{
				$sql = "SELECT hml.id,hml.emp_id,hml.days,hml.hours,hml.absent,hml.late,hml.ot,hml.ut, er.first_name,er.middle_name, er.last_name FROM
				hris_manhours_summary as hms
				LEFT JOIN hris_manhours_log as hml
				ON hml.manhours_summary_id = hms.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hml.emp_id
				WHERE hml.manhours_summary_id = ".$manhours_id."
				AND hml.fromdate = '".$date_from."'
				AND hml.todate  = '".$date_to."'
				AND hml.enabled = 1
				ORDER BY ".$ordrBy." LIMIT ".$start.",".$length."";
			}				//print_r(array($manhours_id,$date_from,$date_to));
		}
		else{
				$sql = "SELECT hml.id,hml.emp_id,hml.days,hml.hours,hml.absent,hml.late,hml.ot,hml.ut, er.first_name,er.middle_name, er.last_name FROM
				hris_manhours_summary as hms
				LEFT JOIN hris_manhours_log as hml
				ON hml.manhours_summary_id = hms.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hml.emp_id
				WHERE hml.manhours_summary_id = ".$manhours_id."
				AND hml.fromdate = '".$date_from."'
				AND hml.todate = '".$date_to."'
				AND hml.enabled = 1
			";

		}
		return $this->db->query($sql);
	}
	//id,name,sss,phil,pagibig,sd,ca
	public function getdeductions($start,$length,$search,$ordrBy,$deduction_id,$date_from,$date_to){
		if($start != null && $length != null){
			if($search != null){
				$sql = "SELECT hdl.id,hdl.employee_idno,hdl.sss,hdl.philhealth,hdl.pag_ibig,hdl.cashadvance,hdl.salary_deduction,er.first_name,er.middle_name,er.last_name
				FROM hris_deduction_summary as hds
				LEFT JOIN hris_deduction_log as hdl
				ON hdl.deductionsum_id = hds.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hdl.employee_idno
				WHERE concat(hdl.employee_idno,er.first_name,er.middle_name,er.last_name)
				LIKE '%".$this->db->escape_like_str($search)."%' and hdl.enabled = 1
				AND hdl.deductionsum_id = ".$deduction_id."
				AND hdl.fromdate = '".$date_from."'
				AND hdl.todate = '".$date_to."'
				LIMIT ".$start.",".$length."";
			}else{
				$sql = "SELECT hdl.id,hdl.employee_idno,hdl.sss,hdl.philhealth,hdl.pag_ibig,hdl.cashadvance,hdl.salary_deduction,er.first_name,er.middle_name,er.last_name
				FROM hris_deduction_summary as hds
				LEFT JOIN hris_deduction_log as hdl
				ON hdl.deductionsum_id = hds.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hdl.employee_idno
				WHERE hdl.deductionsum_id = ".$deduction_id."
				AND hdl.fromdate = '".$date_from."'
				AND hdl.todate = '".$date_to."'
				AND hdl.enabled = 1
				LIMIT ".$start.",".$length."";
			}
		}else{
			$sql = "SELECT hdl.id,hdl.employee_idno,hdl.sss,hdl.philhealth,hdl.pag_ibig,hdl.cashadvance,hdl.salary_deduction,er.first_name,er.middle_name,er.last_name
				FROM hris_deduction_summary as hds
				LEFT JOIN hris_deduction_log as hdl
				ON hdl.deductionsum_id = hds.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hdl.employee_idno
				WHERE hdl.deductionsum_id = ".$deduction_id."
				AND hdl.fromdate = '".$date_from."'
				AND hdl.todate = '".$date_to."'
				AND hdl.enabled = 1
				";
		}
		return $this->db->query($sql);
	}

	public function getadditionals($start,$length,$search,$ordrBy,$additional_id,$date_from,$date_to){
		if($start != null && $length != null){
			if($search != null){
				$sql = "SELECT hal.id,hal.emp_id,hal.additionalpay,hal.overtimepay,er.first_name,er.middle_name,er.last_name
				FROM hris_additional_summary as has
				LEFT JOIN hris_additional_log as hal
				ON hal.additional_summary_id = has.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hal.emp_id
				WHERE concat(hal.emp_id,er.first_name,er.middle_name,er.last_name)
				LIKE '%".$this->db->escape_like_str($search)."%'
				AND hal.additional_summary_id = ".$additional_id."
				AND hal.fromdate = '".$date_from."'
				AND hal.todate = '".$date_to."'
				AND hal.enabled = 1
				LIMIT ".$start.",".$length."";

			}else{
				$sql = "SELECT hal.id,hal.emp_id,hal.additionalpay,hal.overtimepay,er.first_name,er.middle_name,er.last_name
				FROM hris_additional_summary as has
				LEFT JOIN hris_additional_log as hal
				ON hal.additional_summary_id = has.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hal.emp_id
				WHERE hal.additional_summary_id = ".$additional_id."
				AND hal.fromdate = '".$date_from."'
				AND hal.todate = '".$date_to."'
				AND hal.enabled = 1
				ORDER BY ".$ordrBy." LIMIT ".$start.",".$length."";
			}
		}else{
			$sql = "SELECT hal.id,hal.emp_id,hal.additionalpay,hal.overtimepay,er.first_name,er.middle_name,er.last_name
			FROM hris_additional_summary as has
			LEFT JOIN hris_additional_log as hal
			ON hal.additional_summary_id = has.id
			LEFT JOIN employee_record as er
			ON er.employee_idno = hal.emp_id
			WHERE hal.additional_summary_id = ".$additional_id."
			AND hal.fromdate = '".$date_from."'
			AND hal.todate = '".$date_to."'
			AND hal.enabled = 1
			";
			}
		return $this->db->query($sql);
	}
	public function getpayroll_logs($start,$length,$search,$ordrBy,$payroll_id,$date_from,$date_to){
		if($start != null && $length != null){
			if($search != null){
				$sql = "SELECT hpl.id,hpl.fromdate,hpl.todate,
				hpl.emp_id,er.first_name,er.middle_name,er.last_name,
				hpl.grosspay,hpl.additionals,hpl.deductions,hpl.netpay,
				hpl.paytype, hpl.fromdate,hpl.todate
				FROM hris_payroll_summary as hps
				LEFT JOIN hris_payroll_log as hpl
				ON hpl.payroll_summary_id = hps.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hpl.emp_id
				WHERE hpl.payroll_summary_id = ".$payroll_id."
				AND concat(hpl.emp_id,er.first_name,er.middle_name,er.last_name)
				LIKE '%".$this->db->escape_like_str($search)."%'
				AND hpl.fromdate = '".$date_from."'
				AND hpl.todate = '".$date_to."'
				AND hpl.enabled = 1
				LIMIT ".$start.",".$length."";
			}
			else{
				$sql = "SELECT hpl.id,hpl.fromdate,hpl.todate
				,hpl.emp_id,er.first_name,er.middle_name,er.last_name,
				hpl.grosspay,hpl.additionals,hpl.deductions,hpl.netpay,
				hpl.paytype, hpl.fromdate,hpl.todate
				FROM hris_payroll_summary as hps
				LEFT JOIN hris_payroll_log as hpl
				ON hpl.payroll_summary_id = hps.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hpl.emp_id
				WHERE hpl.payroll_summary_id = ".$payroll_id."
				AND hpl.fromdate = '".$date_from."'
				AND hpl.todate = '".$date_to."'
				AND hpl.enabled = 1
				LIMIT ".$start.",".$length."";
			}
		}else{
				$sql = "SELECT hpl.id,hpl.fromdate,hpl.todate,
				hpl.emp_id,er.first_name,er.middle_name,er.last_name,
				hpl.grosspay,hpl.additionals,hpl.deductions,hpl.netpay,
				hpl.paytype, hpl.fromdate,hpl.todate
				FROM hris_payroll_summary as hps
				LEFT JOIN hris_payroll_log as hpl
				ON hpl.payroll_summary_id = hps.id
				LEFT JOIN employee_record as er
				ON er.employee_idno = hpl.emp_id
				WHERE hpl.payroll_summary_id = ".$payroll_id."
				AND hpl.fromdate = '".$date_from."'
				AND hpl.todate = '".$date_to."'
				AND hpl.enabled = 1
				";
		}
		return $this->db->query($sql);
	}
	//--------------------------This will get all logs of tables-------------------
	public function getmanhours_logs($emp_id,$date_from,$date_to){

		$wsquery = "SELECT work_sched FROM work_schedule as ws
		LEFT JOIN employee_record as er
		ON er.employee_idno = ws.emp_idno
		LEFT JOIN contract as c
		ON c.contract_emp_id = er.id
		WHERE ws.emp_idno = '".$emp_id."'
		AND ws.enabled = 1
		AND c.contract_status = 'active'
		";
		$worksched = $this->db->query($wsquery)->row();

		$get_sched_day = json_decode($worksched->work_sched);
		//lalagyan pa ng else if pag wala sched tas may WO
		$mh_log_array = array();
		while (strtotime($date_from) <= strtotime($date_to)) {
		$date_timelog_day = date('w', strtotime($date_from));
		if($date_timelog_day == 0){

				if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
				{
					//check if there is holiday
				$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON er.employee_idno = trs.employee_idno
				WHERE trs.employee_idno = '".$emp_id."'
				AND trs.date_created ='".$date_from."'";

					$trs_row = $this->db->query($trs_query)->row();
					if($trs_row != null){ // will trigger if not in trs. will be defaulted as absent. medyo malabo mangyari kapag naka up na. mark as absent na lang
						$date = $trs_row->date_created;
						$day_type = "";
						if($trs_row->absent == 1){
							$day_type = "Absent";
						}else{
							$day_type = "Regular Day";
						}
						if($day_type == "Absent"){
							$time_details = '--:-- - --:--';
						}else{
							$time_details = $trs_row->time_in.' - '.$trs_row->time_out;
						}
						$man_hours = $trs_row->man_hours;
						$late = $trs_row->late;
						$overtime = $trs_row->overtime;
						$undertime = $trs_row->undertime;
					}else{
						$date = $date_from;
						$day_type = 'Absent';
						$time_details = '--:-- - --:--';
						$man_hours = 0;
						$late = 0;
						$overtime = 0;
						$undertime = 0;
					}
				}else{
					$date = $date_from;
					$day_type = 'Day Off';
					$time_details = '--:-- - --:--';
					$man_hours = 0;
					$late = 0;
					$overtime = 0;
					$undertime = 0;
				}
				$mh_log_array[] = array('date' => $date,
										'day_type' => $day_type,
										'time_data' => $time_details,
										'man_hours' => $man_hours,
										'late' => $late,
										'overtime' => $overtime,
										'undertime' => $undertime);
			}
			else if($date_timelog_day == 1){
				if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
				{
				$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON er.employee_idno = trs.employee_idno
				WHERE trs.employee_idno = '".$emp_id."'
				AND trs.date_created ='".$date_from."'";

					$trs_row = $this->db->query($trs_query)->row();
					if($trs_row != null){ // will trigger if not in trs. will be defaulted as absent. medyo malabo mangyari kapag naka up na. mark as absent na lang
						$date = $trs_row->date_created;
						$day_type = "";
						if($trs_row->absent == 1){
							$day_type = "Absent";
						}else{
							$day_type = "Regular Day";
						}
						if($day_type == "Absent"){
							$time_details = '--:-- - --:--';
						}else{
							$time_details = $trs_row->time_in.' - '.$trs_row->time_out;
						}
						$man_hours = $trs_row->man_hours;
						$late = $trs_row->late;
						$overtime = $trs_row->overtime;
						$undertime = $trs_row->undertime;
					}else{
						$date = $date_from;
						$day_type = 'Absent';
						$time_details = '--:-- - --:--';
						$man_hours = 0;
						$late = 0;
						$overtime = 0;
						$undertime = 0;
					}
				}else{
					$date = $date_from;
					$day_type = 'Day Off';
					$time_details = '--:-- - --:--';
					$man_hours = 0;
					$late = 0;
					$overtime = 0;
					$undertime = 0;
				}
				$mh_log_array[] = array('date' => $date,
										'day_type' => $day_type,
										'time_data' => $time_details,
										'man_hours' => $man_hours,
										'late' => $late,
										'overtime' => $overtime,
										'undertime' => $undertime);

			}
			else if($date_timelog_day == 2){
				if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
				{
				$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON er.employee_idno = trs.employee_idno
				WHERE trs.employee_idno = '".$emp_id."'
				AND trs.date_created ='".$date_from."'";

					$trs_row = $this->db->query($trs_query)->row();
					if($trs_row != null){ // will trigger if not in trs. will be defaulted as absent. medyo malabo mangyari kapag naka up na. mark as absent na lang
						$date = $trs_row->date_created;
						$day_type = "";
						if($trs_row->absent == 1){
							$day_type = "Absent";
						}else{
							$day_type = "Regular Day";
						}
						if($day_type == "Absent"){
							$time_details = '--:-- - --:--';
						}else{
							$time_details = $trs_row->time_in.' - '.$trs_row->time_out;
						}
						$man_hours = $trs_row->man_hours;
						$late = $trs_row->late;
						$overtime = $trs_row->overtime;
						$undertime = $trs_row->undertime;
					}else{
						$date = $date_from;
						$day_type = 'Absent';
						$time_details = '--:-- - --:--';
						$man_hours = 0;
						$late = 0;
						$overtime = 0;
						$undertime = 0;
					}
				}else{
					$date = $date_from;
					$day_type = 'Day Off';
					$time_details = '--:-- - --:--';
					$man_hours = 0;
					$late = 0;
					$overtime = 0;
					$undertime = 0;
				}
				$mh_log_array[] = array('date' => $date,
										'day_type' => $day_type,
										'time_data' => $time_details,
										'man_hours' => $man_hours,
										'late' => $late,
										'overtime' => $overtime,
										'undertime' => $undertime);

			}
			else if($date_timelog_day == 3){
				if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
				{
				$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON er.employee_idno = trs.employee_idno
				WHERE trs.employee_idno = '".$emp_id."'
				AND trs.date_created ='".$date_from."'";

					$trs_row = $this->db->query($trs_query)->row();
					if($trs_row != null){ // will trigger if not in trs. will be defaulted as absent. medyo malabo mangyari kapag naka up na. mark as absent na lang
						$date = $trs_row->date_created;
						$day_type = "";
						if($trs_row->absent == 1){
							$day_type = "Absent";
						}else{
							$day_type = "Regular Day";
						}
						if($day_type == "Absent"){
							$time_details = '--:-- - --:--';
						}else{
							$time_details = $trs_row->time_in.' - '.$trs_row->time_out;
						}
						$man_hours = $trs_row->man_hours;
						$late = $trs_row->late;
						$overtime = $trs_row->overtime;
						$undertime = $trs_row->undertime;
					}else{
						$date = $date_from;
						$day_type = 'Absent';
						$time_details = '--:-- - --:--';
						$man_hours = 0;
						$late = 0;
						$overtime = 0;
						$undertime = 0;
					}
				}else{
					$date = $date_from;
					$day_type = 'Day Off';
					$time_details = '--:-- - --:--';
					$man_hours = 0;
					$late = 0;
					$overtime = 0;
					$undertime = 0;
				}
				$mh_log_array[] = array('date' => $date,
										'day_type' => $day_type,
										'time_data' => $time_details,
										'man_hours' => $man_hours,
										'late' => $late,
										'overtime' => $overtime,
										'undertime' => $undertime);

			}
			else if($date_timelog_day == 4){
				if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
				{
				$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON er.employee_idno = trs.employee_idno
				WHERE trs.employee_idno = '".$emp_id."'
				AND trs.date_created ='".$date_from."'";

					$trs_row = $this->db->query($trs_query)->row();
					if($trs_row != null){ // will trigger if not in trs. will be defaulted as absent. medyo malabo mangyari kapag naka up na. mark as absent na lang
						$date = $trs_row->date_created;
						$day_type = "";
						if($trs_row->absent == 1){
							$day_type = "Absent";
						}else{
							$day_type = "Regular Day";
						}
						if($day_type == "Absent"){
							$time_details = '--:-- - --:--';
						}else{
							$time_details = $trs_row->time_in.' - '.$trs_row->time_out;
						}
						$man_hours = $trs_row->man_hours;
						$late = $trs_row->late;
						$overtime = $trs_row->overtime;
						$undertime = $trs_row->undertime;
					}else{
						$date = $date_from;
						$day_type = 'Absent';
						$time_details = '--:-- - --:--';
						$man_hours = 0;
						$late = 0;
						$overtime = 0;
						$undertime = 0;
					}
				}else{
					$date = $date_from;
					$day_type = 'Day Off';
					$time_details = '--:-- - --:--';
					$man_hours = 0;
					$late = 0;
					$overtime = 0;
					$undertime = 0;
				}
				$mh_log_array[] = array('date' => $date,
										'day_type' => $day_type,
										'time_data' => $time_details,
										'man_hours' => $man_hours,
										'late' => $late,
										'overtime' => $overtime,
										'undertime' => $undertime);

			}
			else if($date_timelog_day == 5){
				if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
				{
				$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON er.employee_idno = trs.employee_idno
				WHERE trs.employee_idno = '".$emp_id."'
				AND trs.date_created ='".$date_from."'";

					$trs_row = $this->db->query($trs_query)->row();
					if($trs_row != null){ // will trigger if not in trs. will be defaulted as absent. medyo malabo mangyari kapag naka up na. mark as absent na lang
						$date = $trs_row->date_created;
						$day_type = "";
						if($trs_row->absent == 1){
							$day_type = "Absent";
						}else{
							$day_type = "Regular Day";
						}
						if($day_type == "Absent"){
							$time_details = '--:-- - --:--';
						}else{
							$time_details = $trs_row->time_in.' - '.$trs_row->time_out;
						}
						$man_hours = $trs_row->man_hours;
						$late = $trs_row->late;
						$overtime = $trs_row->overtime;
						$undertime = $trs_row->undertime;
					}else{
						$date = $date_from;
						$day_type = 'Absent';
						$time_details = '--:-- - --:--';
						$man_hours = 0;
						$late = 0;
						$overtime = 0;
						$undertime = 0;
					}

				}else{
					$date = $date_from;
					$day_type = 'Day Off';
					$time_details = '--:-- - --:--';
					$man_hours = 0;
					$late = 0;
					$overtime = 0;
					$undertime = 0;
				}
				$mh_log_array[] = array('date' => $date,
										'day_type' => $day_type,
										'time_data' => $time_details,
										'man_hours' => $man_hours,
										'late' => $late,
										'overtime' => $overtime,
										'undertime' => $undertime);

			}
			else if($date_timelog_day == 6){
				if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
				{
				$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON er.employee_idno = trs.employee_idno
				WHERE trs.employee_idno = '".$emp_id."'
				AND trs.date_created ='".$date_from."'";

					$trs_row = $this->db->query($trs_query)->row();
					if($trs_row != null){ // will trigger if not in trs. will be defaulted as absent. medyo malabo mangyari kapag naka up na. mark as absent na lang
						$date = $trs_row->date_created;
						$day_type = "";
						if($trs_row->absent == 1){
							$day_type = "Absent";
						}else{
							$day_type = "Regular Day";
						}
						if($day_type == "Absent"){
							$time_details = '--:-- - --:--';
						}else{
							$time_details = $trs_row->time_in.' - '.$trs_row->time_out;
						}
						$man_hours = $trs_row->man_hours;
						$late = $trs_row->late;
						$overtime = $trs_row->overtime;
						$undertime = $trs_row->undertime;
					}else{
						$date = $date_from;
						$day_type = 'Absent';
						$time_details = '--:-- - --:--';
						$man_hours = 0;
						$late = 0;
						$overtime = 0;
						$undertime = 0;
					}
				}else{
					$date = $date_from;
					$day_type = 'Day Off';
					$time_details = '--:-- - --:--';
					$man_hours = 0;
					$late = 0;
					$overtime = 0;
					$undertime = 0;
				}
				$mh_log_array[] = array('date' => $date,
										'day_type' => $day_type,
										'time_data' => $time_details,
										'man_hours' => $man_hours,
										'late' => $late,
										'overtime' => $overtime,
										'undertime' => $undertime);

			}

			$date_from = date ("Y-m-d", strtotime("+1 day", strtotime($date_from)));
		}

		return $mh_log_array;
	}
	public function getsalary_deduction($emp_id,$date_from,$date_to){
		$sql = "SELECT sd.date_created,ded.description,sd.amount FROM salary_deduction as sd
		LEFT JOIN deduction as ded
		ON ded.deductionid = sd.deduct_category
		WHERE sd.employee_idno = '".$emp_id."'
		AND sd.date_created BETWEEN '".$date_from."' AND '".$date_to."'
		AND sd.enabled = 1";

		return $this->db->query($sql);
	}
	public function getadditionalpays($emp_id,$date_from,$date_to){
		$sql = "SELECT date_issued,purpose FROM additional_pays
		WHERE employee_id = '".$emp_id."'
		AND date_issued BETWEEN '".$date_from."' AND '".$date_to."'
		AND enabled = 1";
		return $this->db->query($sql);
	}
	public function getovertimepays($emp_id,$date_from,$date_to){
		$sql = "SELECT date_created,purpose,minutes_of_overtime FROM overtime_pays
		WHERE employee_id = '".$emp_id."'
		AND date_created BETWEEN '".$date_from."' AND '".$date_to."'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function getcashadvance_tran($emp_id,$date_from,$date_to){
		$sql = "SELECT date_of_file,amount,reason FROM cash_advance_tran
		WHERE employee_id = '".$emp_id."'
		AND date_of_file BETWEEN '".$date_from."' AND '".$date_to."'
		AND enabled = 1";

		return $this->db->query($sql);

	}
	//----------------------------------------------------------
	//---------Queries for approving---------------

	//---------approve-----------------------------
	public function approve_payroll_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_payroll_summary SET status = 'approved'
		WHERE id = ".$id."
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function approve_manhours_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_manhours_summary SET status = 'approved'
		WHERE id = ".$id."
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function approve_additionals_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_additional_summary SET status = 'approved'
		WHERE id = ".$id."
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function approve_deductions_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_deduction_summary SET status = 'approved'
		WHERE id = ".$id."
		AND enabled = 1";

		return $this->db->query($sql);
	}

	public function approve_manhours_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_manhours_log SET status = 'approved'
		WHERE manhours_summary_id = (SELECT id FROM hris_manhours_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function approve_additionals_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_additional_log SET status = 'approved'
		WHERE additional_summary_id = (SELECT id FROM hris_additional_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function approve_deductions_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_deduction_log SET status = 'approved'
		WHERE deductionsum_id = (SELECT id FROM hris_deduction_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function approve_payroll_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_payroll_log SET status = 'approved'
		WHERE payroll_summary_id = (SELECT id FROM hris_payroll_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function get_approved_payroll_emp($payroll_summary_id){
		$payroll_summary_id = $this->db->escape($payroll_summary_id);
		$sql = "SELECT hpl.emp_id as emp_id,hpl.fromdate as fromdate,hpl.todate as todate,p.description as paytype
		FROM hris_payroll_log as hpl
		LEFT JOIN paytype as p
		ON p.paytypeid = hpl.paytype
		WHERE hpl.status = 'approved'
		AND hpl.enabled = 1
		AND hpl.payroll_summary_id = $payroll_summary_id";

		return $this->db->query($sql);
	}

	//---------------------remove----------
	public function remove_payroll_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_payroll_log SET enabled = 0
		WHERE payroll_summary_id = (SELECT id FROM hris_payroll_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function remove_deductions_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_deduction_log SET enabled = 0
		WHERE deductionsum_id = (SELECT id FROM hris_deduction_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function remove_additionals_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_additional_log SET enabled = 0
		WHERE additional_summary_id = (SELECT id FROM hris_additional_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function remove_manhours_log($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_manhours_log SET enabled = 0
		WHERE manhours_summary_id = (SELECT id FROM hris_manhours_summary WHERE id = ".$id." and enabled = 1)
		AND enabled = 1";

		return $this->db->query($sql);
	}


	public function remove_payroll_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_payroll_summary SET enabled = 0
		WHERE id = ".$id."";
		return $this->db->query($sql);
	}
	public function remove_additionals_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_additional_summary SET enabled = 0
		WHERE id = ".$id."";
		return $this->db->query($sql);
	}
	public function remove_deductions_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_deduction_summary SET enabled = 0
		WHERE id = ".$id."";
		return $this->db->query($sql);
	}
	public function remove_manhours_summary($id){
		$id = $this->db->escape($id);
		$sql = "UPDATE hris_manhours_summary SET enabled = 0
		WHERE id = ".$id."";
		return $this->db->query($sql);
	}
	//----------------------------------------------------------------
	//----------------------Queries for printing--------------------
	public function print_payroll_log($emp_id,$date_from,$date_to){
		$sql = "SELECT * FROM hris_payroll_log
		WHERE emp_id = '".$emp_id."'
		AND fromdate = '".$date_from."'
		AND todate = '".$date_to."'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function print_manhours_log($emp_id,$date_from,$date_to){
		$sql = "SELECT * FROM hris_manhours_log
		WHERE  emp_id = '".$emp_id."'
		AND fromdate = '".$date_from."'
		AND todate = '".$date_to."'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function print_additionals_log($emp_id,$date_from,$date_to){
		$sql = "SELECT * FROM hris_additional_log
		WHERE emp_id = '".$emp_id."'
		AND fromdate = '".$date_from."'
		AND todate = '".$date_to."'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function print_deductions_log($emp_id,$date_from,$date_to){
		$sql = "SELECT * FROM hris_deduction_log
		WHERE employee_idno = '".$emp_id."'
		AND fromdate = '".$date_from."'
		AND todate = '".$date_to."'";

		return $this->db->query($sql);
	}
	public function getname($emp_id){
		$sql = "SELECT * FROM employee_record
		WHERE employee_idno = '".$emp_id."'";
		return $this->db->query($sql);
	}
	//----------------others---------------------
	public function gettotal_salary($emp_id){
		$sql = "SELECT con.total_sal FROM employee_record as er
		LEFT JOIN contract as con
		ON con.contract_emp_id = er.id
		WHERE employee_idno = '".$emp_id."'
		AND con.contract_status = 'active'
		AND con.enabled = 1";

		return $this->db->query($sql);
	}


  public function print_manhours($date_from,$date_to,$employee_idno){
  	$sql = "SELECT SUM(man_hours) FROM time_record_summary
          WHERE employee_idno = '".$employee_idno."'
          AND date_created BETWEEN '".$date_from."' AND '".$date_to."'";

    return $this->db->query($sql);

  }
  public function print_late($date_from,$date_to,$employee_idno){
  	$sql = "SELECT SUM(late) as late FROM time_record_summary
          WHERE employee_idno = '".$employee_idno."'
          AND date_created BETWEEN '".$date_from."' AND '".$date_to."'";

    return $this->db->query($sql);
  }
  public function print_undertime($date_from,$date_to,$employee_idno){
  	$sql = "SELECT SUM(undertime) as undertime FROM time_record_summary
          WHERE employee_idno = '".$employee_idno."'
          AND date_created BETWEEN '".$date_from."' AND '".$date_to."'";

    return $this->db->query($sql);

  }
  public function get_contract_details($employee_idno){
  		$sql = "SELECT c.total_sal,p.frequency,c.emp_status
  		FROM contract as c
  		LEFT JOIN employee_record as er
  		ON c.contract_emp_id = er.id
  		LEFT JOIN paytype as p
  		ON p.paytypeid = c.paytype
  		WHERE er.employee_idno = '".$employee_idno."'
  		AND c.contract_status = 'active'";

  		return $this->db->query($sql);
  }

	public function getschedule($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT c.work_sched FROM employee_record a
				INNER JOIN contract b ON a.id = b.contract_emp_id
				INNER JOIN work_schedule c ON b.work_sched_id = c.id
				WHERE a.employee_idno = $empid AND b.contract_status = 'active'";
		return $this->db->query($sql);
	}
	// public function get_holidays($empid,$date_created){
	// 	$sql = "SELECT ht.holiday_type,ht.date
	// 	FROM holiday_tran as ht
	// 	LEFT JOIN time_record_summary as trs
	// 	ON ht.date = trs.date_created
	// 	WHERE trs.employee_idno = '".$empid."'
	// 	AND trs.date_created = '".$date_created."'";

	// 	return $this->db->query($sql);
	// }
	public function get_trs($empid,$date_from,$date_to){
		$empid = $this->db->escape($empid);
		$date_from = $this->db->escape($date_from);
		$date_to = $this->db->escape($date_to);

		$sql = "SELECT date_created FROM time_record_summary
		WHERE employee_idno = '".$empid."'
		AND date_created BETWEEN '".$date_from."' AND '".$date_to."'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function get_holidays($date_created){
		$date_created = $this->db->escape($date_created);
		$sql = "SELECT * FROM holidays_tran
		WHERE `date` = '".$date_created."'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function get_regular_holiday($date_from,$date_to){
		$date_from = $this->db->escape($date_from);
		$date_to = $this->db->escape($date_to);

		$sql = "SELECT * FROM holidays_tran
		WHERE `date` BETWEEN ".$date_from." AND ".$date_to."
		AND holiday_type = 1";

		return $this->db->query($sql);
	}

	public function get_special_holiday($date_from,$date_to){
		$date_from = $this->db->escape($date_from);
		$date_to = $this->db->escape($date_to);

		$sql = "SELECT * FROM holidays_tran
		WHERE `date` BETWEEN ".$date_from." AND ".$date_to."
		AND holiday_type = 2";

		return $this->db->query($sql);
	}
	public function get_timerecord_summary($empid,$date_from,$date_to){
		// $empid = $this->db->escape($empid);
		// $date_from = $this->db->escape($date_from);
		// $date_to = $this->db->escape($date_to);

		$sql = "SELECT * FROM time_record_summary
		WHERE employee_idno = '".$empid."'
		AND date_created BETWEEN '".$date_from."' AND '".$date_to."'
		AND absent = 0
		AND enabled = 1";

	return $this->db->query($sql);
	}
	//if employee is present
	public function get_regular_payratio1(){
		$sql = "SELECT payratio FROM holidaytype
		WHERE holidaytypeid = 1
		AND enabled = 1";

		return $this->db->query($sql);
	}
	//if employee is absent
	public function get_regular_payratio2(){
		$sql = "SELECT payratio2 FROM holidaytype
		WHERE holidaytypeid = 1
		AND enabled = 1";

		return $this->db->query($sql);
	}

	//if employee is present
	public function get_special_payratio1(){
		$sql = "SELECT payratio FROM holidaytype
		WHERE holidaytypeid = 2
		AND enabled = 1";

		return $this->db->query($sql);
	}
	//if employee is absent
	public function get_special_payratio2(){
		$sql = "SELECT payratio2 FROM holidaytype
		WHERE holidaytypeid = 2
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function insert_approved_payslip(
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
										){

		$employee_idno = $this->db->escape($employee_idno);
		$name = $this->db->escape($name);
		$paytype_desc = $this->db->escape($paytype_desc);
		$date_from = $this->db->escape($date_from);
		$date_to = $this->db->escape($date_to);
		$approved_gross_salary = $this->db->escape($approved_gross_salary);
		$days_duration = $this->db->escape($days_duration);
		$approved_overtime = $this->db->escape($approved_overtime);
		$ot_duration = $this->db->escape($ot_duration);
		$approved_additionals = $this->db->escape($approved_additionals);
		$approved_regular_holiday = $this->db->escape($approved_regular_holiday);
		$regular_holiday_duration = $this->db->escape($regular_holiday_duration);
		$approved_special_holiday = $this->db->escape($approved_special_holiday);
		$special_holiday_duration = $this->db->escape($special_holiday_duration);
		$approved_sundays = $this->db->escape($approved_sundays);
		$sunday_duration = $this->db->escape($sunday_duration);
		$approved_absent = $this->db->escape($approved_absent);
		$absent_duration = $this->db->escape($absent_duration);
		$approved_late = $this->db->escape($approved_late);
		$late_duration = $this->db->escape($late_duration);
		$approved_undertime = $this->db->escape($approved_undertime);
		$undertime_duration = $this->db->escape($undertime_duration);
		$approved_sss = $this->db->escape($approved_sss);
		$approved_philhealth = $this->db->escape($approved_philhealth);
		$approved_pag_ibig = $this->db->escape($approved_pag_ibig);
		$approved_sss_loan = $this->db->escape($approved_sss_loan);
		$approved_pag_ibig_loan = $this->db->escape($approved_pag_ibig_loan);
		$approved_cashadvance = $this->db->escape($approved_cashadvance);
		$approved_salary_deduction = $this->db->escape($approved_salary_deduction);
		$approved_total_deductions = $this->db->escape($approved_total_deductions);
		$netpay = $this->db->escape($netpay);

		$sql = "INSERT INTO hris_payslip(
			employee_idno,
			name,
			paytype_desc,
			date_from,
			date_to,
			gross_salary,
			days_duration,
			overtime,
			ot_duration,
			additionals,
			regular_holiday,
			regular_holiday_duration,
			special_holiday,
			special_holiday_duration,
			sundays,
			sunday_duration,
			absent,
			absent_duration,
			late,
			late_duration,
			undertime,
			undertime_duration,
			sss,
			philhealth,
			pag_ibig,
			sss_loan,
			pag_ibig_loan,
			cashadvance,
			salary_deduction,
			total_deductions,
			netpay
		)
		VALUES(
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
		)";

		$this->db->query($sql);
	}

}
