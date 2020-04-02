<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//this is cash advance for transactions
class Timerecordsummary_model extends CI_Model {

		public function Gettimerecord($start,$length,$search,$ordrBy) {
		$current_date_minus = date('Y-m-d',strtotime('-1 day'));
		if($start != null && $length != null) {
			if(sizeof($search) > 0){
				if(array_key_exists('start_date',$search) && $search['start_date'] != ""){
					$sql = "SELECT
					trs.*,
					er.first_name,
					er.middle_name,
					er.last_name
					FROM time_record_summary as trs
					LEFT JOIN employee_record as er
					ON trs.employee_idno = er.employee_idno
					WHERE trs.enabled = 1
					AND trs.date_created BETWEEN '".$search['start_date']."' AND '".$search['end_date']."'
					AND er.isActive = 1
					ORDER BY date_created ASC
					LIMIT ".$start.", ".$length."
					";
				}
				else if(array_key_exists('datestart_id',$search) && $search['datestart_id'] != ""){
					if(array_key_exists('search_id',$search) && $search['search_id'] != ""){
							$sql = "SELECT
							trs.*,
							er.first_name,
							er.middle_name,
							er.last_name
							FROM time_record_summary as trs
							LEFT JOIN employee_record as er
							ON trs.employee_idno = er.employee_idno
							WHERE trs.enabled = 1
							AND trs.employee_idno LIKE '%".$this->db->escape_like_str($search['search_id'])."%'
							AND trs.date_created BETWEEN '".$search['datestart_id']."' AND '".$search['dateend_id']."'
							AND er.isActive = 1
							ORDER BY date_created ASC
							LIMIT ".$start.",".$length."
							";
						}else{
							$sql = "SELECT
							trs.*,
							er.first_name,
							er.middle_name,
							er.last_name
							FROM time_record_summary as trs
							LEFT JOIN employee_record as er
							ON trs.employee_idno = er.employee_idno
							WHERE trs.enabled = 1
							AND trs.date_created BETWEEN '".$search['datestart_id']."' AND '".$search['dateend_id']."'
							AND er.isActive = 1
							ORDER BY date_created ASC
							LIMIT ".$start.",".$length."
							";
						}
				}
				else if(array_key_exists('datestart_name',$search) && $search['datestart_name'] != ""){
					// print_r($search['search_name']);
					if(array_key_exists('search_name',$search) && $search['search_name'] != ""){
							$search_var = preg_replace('/\s+/', '', $search['search_name']);
							$sql = "SELECT
							trs.*,
							er.first_name,
							er.middle_name,
							er.last_name
							FROM time_record_summary as trs
							LEFT JOIN employee_record as er
							ON trs.employee_idno = er.employee_idno
							WHERE trs.enabled = 1
							AND REPLACE(concat(er.first_name,er.middle_name,er.last_name),' ','') LIKE '%".$this->db->escape_like_str($search_var)."%'
							AND trs.date_created BETWEEN '".$search['datestart_name']."' AND '".$search['dateend_name']."'
							AND er.isActive = 1
							ORDER BY date_created ASC
							LIMIT ".$start.",".$length."
							";
						}
						else{
							$sql = "SELECT
							trs.*,
							er.first_name,
							er.middle_name,
							er.last_name
							FROM time_record_summary as trs
							LEFT JOIN employee_record as er
							ON trs.employee_idno = er.employee_idno
							WHERE trs.enabled = 1
							AND trs.date_created BETWEEN '".$search['datestart_name']."' AND '".$search['dateend_name']."'
							AND er.isActive = 1
							ORDER BY date_created ASC
							LIMIT ".$start.",".$length."
							";
						}
				}
			}else
				{
					$sql = "SELECT
					trs.*,
					er.first_name,
					er.middle_name,
					er.last_name
					FROM time_record_summary as trs
					LEFT JOIN employee_record as er
					ON trs.employee_idno = er.employee_idno
					WHERE trs.enabled = 1
					AND er.isActive = 1
					AND date_created = '".$current_date_minus."'
					LIMIT ".$start.",".$length."
					";
				}
		}
		else{
				$sql = "SELECT
				trs.*,
				er.first_name,
				er.middle_name,
				er.last_name
				FROM time_record_summary as trs
				LEFT JOIN employee_record as er
				ON trs.employee_idno = er.employee_idno
				WHERE trs.enabled = 1
				AND er.isActive = 1
				";
		}



		return $this->db->query($sql);
	}

	public function get_default_date(){
		$sql = "SELECT range_start,range_end FROM time_record_summary_range WHERE current_date_used = 1 AND status = 1";
		return $this->db->query($sql);
	}
	public function countworkorder($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT ws.id FROM work_schedule as ws
			LEFT JOIN employee_record as er
			ON er.employee_idno = ws.emp_idno
			LEFT JOIN contract as c
			ON c.contract_emp_id = er.id
			WHERE ws.emp_idno = $empid
			AND ws.enabled = 1
			AND c.contract_status = 'active'
			";
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
	public function getschedtype($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT c.sched_type FROM employee_record a
				INNER JOIN contract b ON a.id = b.contract_emp_id
				INNER JOIN work_schedule c ON b.work_sched_id = c.id
				WHERE a.employee_idno = $empid AND b.contract_status = 'active'";
		return $this->db->query($sql);

	}
	public function get_work_hours($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT c.total_whours FROM employee_record a
				INNER JOIN contract b ON a.id = b.contract_emp_id
				INNER JOIN work_schedule c ON b.work_sched_id = c.id
				WHERE a.employee_idno = $empid AND b.contract_status = 'active'";
		return $this->db->query($sql);

	}
	//-----basic ti to elements
	public function get_first_time_in($emp_id,$date){
		$emp_id = $this->db->escape($emp_id);
		$date = $this->db->escape($date);
		//this will get the first timelog per day
		$sql = "
			SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_id
			AND `date` = $date
			ORDER BY time_in ASC LIMIT 1
		";

		return $this->db->query($sql);
	}
	public function get_last_time_out($emp_id,$date){
		$emp_id = $this->db->escape($emp_id);
		$date = $this->db->escape($date);
		$sql = "
			SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_id
			AND `date` = $date
			ORDER BY time_in DESC LIMIT 1
		";
		return $this->db->query($sql);
	}
	//------
	//---for multiple timelog
	public function get_first_time_out($emp_id,$date){
		$emp_id = $this->db->escape($emp_id);
		$date = $this->db->escape($date);
		$sql = "
			SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_id
			AND `date` = $date
			ORDER BY time_in ASC LIMIT 1
		";
		return $this->db->query($sql);
	}
	public function get_last_time_in($emp_id,$date){
		$emp_id = $this->db->escape($emp_id);
		$date = $this->db->escape($date);
		$sql = "
			SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_id
			AND `date` = $date
			ORDER BY time_in DESC LIMIT 1
		";
		return $this->db->query($sql);
	}
	//for checking of multiple timelog
	public function count_timelog($emp_id,$date){
		$emp_id = $this->db->escape($emp_id);
		$date = $this->db->escape($date);
		$sql = "SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_id
			AND `date` = $date
			ORDER BY time_in ASC";
		return $this->db->query($sql);
	}
	//this will find all not inside the time record summary
	public function get_all_dates_timelog($date){
		$date = $this->db->escape($date);
		$sql = "SELECT `date`,`employee_idno` FROM time_record_summary_trial WHERE concat(`date`,`employee_idno`) NOT IN
    		(SELECT concat(`date_created`,`employee_idno`) FROM time_record_summary WHERE enabled = 1)
    		AND NOT(`time_out` is null)
    		AND `date` = $date
    		AND enabled = 1
     		GROUP by concat(`date`,`employee_idno`)";
		return $this->db->query($sql);
	}
	//for time record summary
	public function get_all_dates_employees_timelog($empid,$date){
		$date = $this->db->escape($date);
		$empid = $this->db->escape($empid);
		$sql = "SELECT `date`,`employee_idno` FROM time_record_summary_trial WHERE concat(`date`,`employee_idno`) NOT IN
    		(SELECT concat(`date_created`,`employee_idno`) FROM time_record_summary WHERE enabled = 1)
    		AND NOT(`time_out` is null)
    		AND  employee_idno = $empid
    		AND `date` = $date
    		AND enabled = 1
     		GROUP by concat(`date`,`employee_idno`)";
		return $this->db->query($sql);
	}
	//for viewing on other modules
	public function get_all_dates_employees_timelog_others($empid,$date){
		$date = $this->db->escape($date);
		$empid = $this->db->escape($empid);
		$sql = "SELECT `date`,`employee_idno` FROM time_record_summary_trial
    		WHERE NOT(`time_out` is null)
    		AND  employee_idno = $empid
    		AND `date` = $date
    		AND enabled = 1
     		GROUP by concat(`date`,`employee_idno`)";
		return $this->db->query($sql);
	}
	public function check_lunch_workorder($employee_idno,$date_timelog,$breakout){
		$employee_idno = $this->db->escape($employee_idno);
		$date_timelog = $this->db->escape($date_timelog);
		$breakout = $this->db->escape($breakout);

		$sql = "SELECT end_time FROM work_order
		WHERE employee_id = $employee_idno
		AND `date` = $date_timelog
		AND end_time = $breakout
		AND enabled = 1
		ORDER BY end_time ASC LIMIT 1
		";

		return $this->db->query($sql);
	}
	public function select_dates_timelog($start_date,$end_date){
		$start_date = $this->db->escape($start_date);
		$end_date = $this->db->escape($end_date);

		$sql = "SELECT `date` FROM time_record_summary_trial WHERE `date` NOT IN
    		(SELECT `date_created` FROM time_record_summary WHERE enabled = 1)
    		AND NOT(`time_out` is null)
    		AND `date` BETWEEN $start_date AND $end_date
    		AND enabled = 1
     		GROUP by `date`";
		return $this->db->query($sql);
	}
	public function insertdata($empid,$date,$time_in,$time_out,$late,$overtime,$undertime,$absent,$totalminutes,$manhours,$overbreak){
		$empid = $this->db->escape($empid);
		$date = $this->db->escape($date);
		$time_in = $this->db->escape($time_in);
		$time_out = $this->db->escape($time_out);
		$late = $this->db->escape($late);
		$overtime = $this->db->escape($overtime);
		$undertime = $this->db->escape($undertime);
		$absent = $this->db->escape($absent);
		$totalminutes = $this->db->escape($totalminutes);
		$manhours = $this->db->escape($manhours);
		$overbreak = $this->db->escape($overbreak);

		// print_r(array($empid,$date,$time_in,$time_out,$late,$overtime,$undertime,$absent,$totalminutes,$manhours));
		$sql = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,overbreak) VALUES ($empid,$date,$time_in,$time_out,$late,$overtime,$undertime,$absent,$totalminutes,$manhours,$overbreak)";
		return $this->db->query($sql);
	}

	public function insertdata_workorder($empid,$date,$time_in,$time_out,$late,$overtime,$undertime,$absent,$totalminutes,$manhours,$overbreak,$remarks){
		$empid = $this->db->escape($empid);
		$date = $this->db->escape($date);
		$time_in = $this->db->escape($time_in);
		$time_out = $this->db->escape($time_out);
		$late = $this->db->escape($late);
		$overtime = $this->db->escape($overtime);
		$undertime = $this->db->escape($undertime);
		$absent = $this->db->escape($absent);
		$totalminutes = $this->db->escape($totalminutes);
		$manhours = $this->db->escape($manhours);
		$overbreak = $this->db->escape($overbreak);
		$remarks = $this->db->escape($remarks);

		// print_r(array($empid,$date,$time_in,$time_out,$late,$overtime,$undertime,$absent,$totalminutes,$manhours));
		$sql = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,overbreak,remarks) VALUES ($empid,$date,$time_in,$time_out,$late,$overtime,$undertime,$absent,$totalminutes,$manhours,$overbreak,$remarks)";
		return $this->db->query($sql);
	}
	//this query will trigger if multiple timelog
	// public function get_timelog_multiple($emp_id,$date){
	// 	$emp_id = $this->db->escape($emp_id);
	// 	$date = $this->db->escape($date);
	// 	$sql = "SELECT employee_idno,time_in,time_out FROM time_record_summary_trial
	// 	WHERE  NOT(`time_out` is null) AND employee_idno = $emp_id AND `date` = $date
	// 	AND enabled = 1
	// 	ORDER BY time_in ASC";

	// 	return $this->db->query($sql);
	// }

	public function get_timelog_multiple($emp_id,$date){
		$emp_id = $this->db->escape($emp_id);
		$date = $this->db->escape($date);

		$sql = "
			SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_id
			AND `date` = $date
			ORDER BY `date` ASC, `time_in` ASC
		";

		return $this->db->query($sql);
	}
	//----workorder queries-------------------------------------------------------------------------------------------------------
	public function get_work_order($emp_id,$work_date){
		$emp_id = $this->db->escape($emp_id);
		$work_date = $this->db->escape($work_date);
		$sql = "SELECT start_time,end_time FROM work_order
		WHERE employee_id = $emp_id
		AND `date` = $work_date
		AND status = 'certified'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	//will get all workorders after last time out
	public function check_post_workorder($employee_idno,$date_timelog,$actual_timeout){
		$employee_idno = $this->db->escape($employee_idno);
		$date_timelog = $this->db->escape($date_timelog);
		$actual_timeout = $this->db->escape($actual_timeout);

		$postworkorder = "SELECT start_time FROM work_order
		WHERE employee_id = $employee_idno
		AND `date` = $date_timelog
		AND status = 'certified'
		AND start_time >= $actual_timeout
		AND enabled = 1
		LIMIT 1";

		return $this->db->query($postworkorder);
	}
	//workorder that will trigger if employee has late
	public function get_pre_workorder($emp_id,$work_date,$fti){
		$emp_id = $this->db->escape($emp_id);
		$work_date = $this->db->escape($work_date);
		$fti = $this->db->escape($fti);
		$totalminutes_workorder = 0;

		$preworkorder = "SELECT start_time,end_time FROM work_order
		WHERE employee_id = $emp_id
		AND `date` = $work_date
		AND status = 'certified'
		AND end_time >= $fti
		AND enabled = 1";

		$preworkorder_result = $this->db->query($preworkorder)->result();

		if($preworkorder_result != null){
			foreach($preworkorder_result as $pr){
				$start_time = convert_to_minutes($pr->start_time);
				$end_time = convert_to_minutes($pr->end_time);
				$totalminutes_workorder = $totalminutes_workorder + ($end_time - $start_time);
			}
		}else{
			$totalminutes_workorder = 0;
		}
		return $totalminutes_workorder;
	}
	//workorder that will trigger if employee has undertime
	public function get_post_workorder($emp_id,$work_date,$lto){
		$emp_id = $this->db->escape($emp_id);
		$work_date = $this->db->escape($work_date);
		$lto = $this->db->escape($lto);
		$totalminutes_workorder = 0;

		$preworkorder = "SELECT start_time,end_time FROM work_order
		WHERE employee_id = $emp_id
		AND `date` = $work_date
		AND status = 'certified'
		AND start_time >= $lto
		AND enabled = 1";

		$preworkorder_result = $this->db->query($preworkorder)->result();

		if($preworkorder_result != null){
			foreach($preworkorder_result as $pr){
				$start_time = convert_to_minutes($pr->start_time);
				$end_time = convert_to_minutes($pr->end_time);
				$totalminutes_workorder = $totalminutes_workorder + ($end_time - $start_time);
			}
		}else{
			$totalminutes_workorder = 0;
		}
		return $totalminutes_workorder;
	}
	public function tryquery(){
		$sql = "SELECT * FROM paytype";
		return $this->db->query($sql);
	}

	//this will generate workorder that is not in the trs
	public function get_all_workorders(){
		$sql = "SELECT * FROM work_order WHERE concat(`date`,`employee_id`) NOT IN
    		(SELECT concat(`date_created`,`employee_idno`) FROM time_record_summary where enabled = 1)
    		AND enabled = 1
     		GROUP by concat(`date`,`employee_id`)";
		return $this->db->query($sql);
	}

	public function get_all_workorders_others($employee_idno,$date_created){
		$employee_idno = $this->db->escape($employee_idno);
		$date_created = $this->db->escape($date_created);
		$purpose = "others";
		$check = check_minutes($employee_idno,$date_created,$purpose);
	//if null, then proceed
		if($check == ""){

			$sql = "SELECT * FROM work_order
			WHERE employee_id = $employee_idno
			AND `date` = $date_created
			AND enabled = 1";

		}else{

			$sql = "SELECT * FROM work_order WHERE concat(`date`,`employee_id`) NOT IN
    		(SELECT concat(`date_created`,`employee_idno`) FROM time_record_summary where enabled = 1)
    		AND employee_id = $employee_idno
    		AND `date` = $date_created
    		AND enabled = 1
     		GROUP by concat(`date`,`employee_id`)";
		}


		return $this->db->query($sql);
	}
	// public function check_absent_record_v2($emp_id,$date_from){
	// 	// $current_date_minus = date('Y-m-d',strtotime('-1 day'));
	// 	$current_date_minus = date('Y-m-d');
	// 	$wsquery = "SELECT work_sched FROM work_schedule as ws
	// 	LEFT JOIN employee_record as er
	// 	ON er.employee_idno = ws.emp_idno
	// 	LEFT JOIN contract as c
	// 	ON c.contract_emp_id = er.id
	// 	WHERE ws.emp_idno = '".$emp_id."'
	// 	AND ws.enabled = 1
	// 	AND c.contract_status = 'active'
	// 	";

	// 	//first timelog of employee
	// 	$dfs = "SELECT * FROM
	// 		(
	// 		SELECT trs.employee_idno as  employee_idno,
	// 		trs.time_in as time_in, trs.time_out as time_out,
	// 		trs.date as date,
	// 		'timelog' as type,
	// 		'timelog' as status
	// 		FROM time_record_summary_trial as trs

	// 		UNION ALL

	// 		SELECT wo.employee_id as  employee_idno,
	// 		wo.start_time as time_in, wo.end_time as time_out,
	// 		wo.date as date,
	// 		'work_order' as type,
	// 		wo.status as status
	// 		FROM work_order as wo
	// 		WHERE status = 'certified'
	// 		)
	// 		all_time_logs
	// 		WHERE NOT(time_out is null)
	// 		ORDER BY `date` ASC LIMIT 1";
	// 	$dfs_check = $this->db->query($dfs)->row();
	// 	$date_filter_start = "";

	// 	$dfe = "SELECT * FROM
	// 		(
	// 		SELECT trs.employee_idno as  employee_idno,
	// 		trs.time_in as time_in, trs.time_out as time_out,
	// 		trs.date as date,
	// 		'timelog' as type,
	// 		'timelog' as status
	// 		FROM time_record_summary_trial as trs

	// 		UNION ALL

	// 		SELECT wo.employee_id as  employee_idno,
	// 		wo.start_time as time_in, wo.end_time as time_out,
	// 		wo.date as date,
	// 		'work_order' as type,
	// 		wo.status as status
	// 		FROM work_order as wo
	// 		WHERE status = 'certified'
	// 		)
	// 		all_time_logs
	// 		WHERE NOT(time_out is null)
	// 		ORDER BY `date` DESC LIMIT 1";
	// 	$dfe_check = $this->db->query($dfe)->row();
	// 	$date_filter_end = "";

	// 	if($dfs_check != null){
	// 		$date_filter_start = $this->db->query($dfs)->row()->date;
	// 		$date_filter_start = $date_filter_start;
	// 	}else{
	// 		$date_filter_start = $current_date_minus;
	// 	}
	// 	if($dfe_check != null){
	// 		$date_filter_end = $this->db->query($dfe)->row()->date;
	// 		$date_filter_end = $date_filter_start;
	// 	}else{
	// 		$date_filter_end = $current_date_minus;
	// 	}
	// 	// will return current day -1 if there is no record

	// 	//will check date to prevent mishandling of absent behaviors
	// 	if($date_filter_start > $date_from){
	// 		$date_from = $date_filter_start;
	// 	}
	// 	if($date_from >= $current_date_minus){
	// 		$date_from = $current_date_minus;
	// 	}

	// 	$worksched = $this->db->query($wsquery)->row();
	// 	if($worksched != null){
	// 		$mh_log_array = array();
	// 		$date_timelog_day = date('w', strtotime($date_from));
	// 		$get_worksched = get_worksched($emp_id,$date_timelog_day);
	// 		$sched_availability = $get_worksched['sched_availability'];
	// 		if($sched_availability = "with_worksched"){
	// 			$purpose = "others";
	// 			$check_minutes = check_minutes($emp_id,$date_from,$purpose);
	// 			if($check_minutes == ""){
	// 				//check first timelog of employee
	// 				if($dfs_check != null){
	// 					//check holiday
	// 					$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
	// 					$check_holidays = $this->db->query($ch)->row();
	// 					if($check_holidays != null){
	// 						//employee is not absent - return 0;
	// 						return 0;
	// 					}else{
	// 						//employee is absent - return 1;
	// 						return 1;
	// 					}
	// 				}else{
	// 					//pumasok
	// 					return 0;
	// 				}
	// 			}else{
	// 				//may pasok
	// 				return 0;
	// 			}
	// 		}
	// 	}else{
	// 		return 0;
	// 	}
	// }
	public function check_absent_record_v2($emp_id,$date_from){
		// $current_date_minus = date('Y-m-d',strtotime('-1 day'));
		$current_date_minus = date('Y-m-d');
		$emp_idnum = $this->db->escape($emp_id);
		$date_1 = $this->db->escape($date_from);

		$wsquery = "SELECT work_sched FROM work_schedule as ws
		LEFT JOIN employee_record as er
		ON er.employee_idno = ws.emp_idno
		LEFT JOIN contract as c
		ON c.contract_emp_id = er.id
		WHERE ws.emp_idno = '".$emp_id."'
		AND ws.enabled = 1
		AND c.contract_status = 'active'
		";
		//first timelog of employee
		$dfs = "SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_idnum
			ORDER BY `date` ASC LIMIT 1";
		$dfs_check = $this->db->query($dfs)->row();
		$date_filter_start = "";

		$dfe = "SELECT * FROM
			(
			SELECT trs.employee_idno as  employee_idno,
			trs.time_in as time_in, trs.time_out as time_out,
			trs.date as date,
			'timelog' as type,
			'timelog' as status
			FROM time_record_summary_trial as trs

			UNION ALL

			SELECT wo.employee_id as  employee_idno,
			wo.start_time as time_in, wo.end_time as time_out,
			wo.date as date,
			'work_order' as type,
			wo.status as status
			FROM work_order as wo
			WHERE status = 'certified'
			)
			all_time_logs
			WHERE NOT(time_out is null)
			AND employee_idno = $emp_idnum
			ORDER BY `date` DESC LIMIT 1";
		$dfe_check = $this->db->query($dfe)->row();
		$date_filter_end = "";

		if($dfs_check != null){
			$date_filter_start = $this->db->query($dfs)->row()->date;
			$date_filter_start = $date_filter_start;
		}else{
			$date_filter_start = $current_date_minus;
		}
		if($dfe_check != null){
			$date_filter_end = $this->db->query($dfe)->row()->date;
			$date_filter_end = $date_filter_start;
		}else{
			$date_filter_end = $current_date_minus;
		}
		// will return current day -1 if there is no record

		//will check date to prevent mishandling of absent behaviors
		if($date_filter_start > $date_from){
			$date_from = $date_filter_start;
		}
		if($date_from >= $current_date_minus){
			$date_from = $current_date_minus;
		}

		$worksched = $this->db->query($wsquery)->row();
		if($worksched != null){
			$mh_log_array = array();
			$date_timelog_day = date('w', strtotime($date_from));
			$get_worksched = get_worksched($emp_id,$date_timelog_day);
			$sched_availability = $get_worksched['sched_availability'];
			if($sched_availability == "with_worksched"){
				$purpose = "others";
				$check_minutes = check_minutes($emp_id,$date_from,$purpose);
				if($check_minutes == ""){
					//check first timelog of employee
					if($dfs_check != null){
						$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
						$check_workorder_timelog = "SELECT * FROM work_order WHERE employee_id = $emp_idnum AND `date` = $date_1";
						$leave = "SELECT * FROM leave_tran WHERE employee_idno = $emp_idnum AND '".$date_from."' BETWEEN date_from AND date_to AND status = 'certified' AND enabled = 1";

						// CHECK HOLIDAY
						if($this->db->query($ch)->num_rows() > 0){
							return 0;
						}

						// CHECK WORK ORDER
						if($this->db->query($check_workorder_timelog)->num_rows() > 0){
							return 0;
						}

						// CHECK LEAVE
						if($this->db->query($leave)->num_rows() > 0 ){
							return 0;
						}

						// RETURN 1 if does not match any of the above filter
						return 1;

					}else{
						//pumasok
						return 0;
					}
				}else{
					//may pasok
					return 0;
				}
			}
		}else{
			return 0;
		}

	}

	public function get_leave($date,$emp_idno){
		$sql = "SELECT * FROM leave_tran WHERE employee_idno = ? AND ? BETWEEN date_from AND date_to AND status = 'certified' AND enabled = 1";
		$data = array($emp_idno,$date);
		return $this->db->query($sql,$data);
	}
	public function get_all_workorders_current_date(){
		$date = today();
		$sql = "SELECT * FROM work_order WHERE
			`date` = $date
			AND concat(`date`,`employee_id`) NOT IN
    		(SELECT concat(`date_created`,`employee_idno`) FROM time_record_summary where enabled = 1)
    		AND enabled = 1
     		GROUP by concat(`date`,`employee_id`)";
		return $this->db->query($sql);
	}
	//this will overwrite database based on workorder
	// time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours
	public function update_trs_to_workorder($time_in,$time_out,$late,$overtime,$undertime,$absent,$total_minutes,$man_hours,$emp_id,$date_created){
		$time_in = $this->db->escape($time_in);
		$time_out = $this->db->escape($time_out);
		$late = $this->db->escape($late);
		$overtime = $this->db->escape($overtime);
		$undertime = $this->db->escape($undertime);
		$absent = $this->db->escape($absent);
		$total_minutes = $this->db->escape($total_minutes);
		$man_hours = $this->db->escape($man_hours);
		$emp_id = $this->db->escape($emp_id);
		$date_created = $this->db->escape($date_created);

		$sql = "UPDATE time_record_summary SET time_in = $time_in
		,time_out = $time_out
		,late = $late
		,overtime = $overtime
		,undertime = $undertime
		,absent = $absent
		,total_minutes = $total_minutes
		,man_hours = $man_hours
		WHERE employee_idno = $emp_id
		AND date_created = $date_created
		and enabled = 1";

		return $this->db->query($sql);
	}
	//-----------------------------------------------------
	public function check_trs($emp_id,$date){
		$emp_id = $this->db->escape($emp_id);
		$date = $this->db->escape($date);

		$sql = "SELECT * FROM time_record_summary WHERE employee_idno = $emp_id AND `date_created` = $date AND enabled = 1";
		return $this->db->query($sql);
	}
	//find absent

	//get from last timelog up to current date with work schedule
	//yung mga na fetch ko na data ico convert ko sa days per employee ID na present sa may trs
	public function get_absent($empid,$date){
		$empid = $this->db->escape($empid);
		$date = $this->db->escape($date);

		$sql = "SELECT date_created FROM time_record_summary
		WHERE employeee_idno = $empid
		AND date_created = $date
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function last_date($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT date_created FROM time_record_summary_trial
		WHERE employee_idno = $empid
		AND NOT(time_out = null)
		ORDER by date_created DESC LIMIT 1";

		return $this->db->query($sql);
	}
	public function get_absent_dates($empid){
		$current_date_minus = date('Y-m-d',strtotime('-1 day'));
		$sql = "SELECT * from
 		(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
 		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
		WHERE selected_date >
		(SELECT `date` FROM time_record_summary_trial
		WHERE `employee_idno` = '".$empid."'
		GROUP BY concat(`date`,`employee_idno`)
		ORDER BY `date` DESC LIMIT 1,1)
		AND selected_date <= '".$current_date_minus."'";

		return $this->db->query($sql);
	}
	public function get_employee_timelog(){
		$sql = "SELECT `employee_idno` FROM time_record_summary_trial
		GROUP BY `employee_idno`";

		return $this->db->query($sql);
	}

	public function get_employee_timelog_v2($emp_id){
		$search_var = preg_replace('/\s+/', '', $emp_id);
		// $emp_id = $this->db->escape_like_str($emp_id);
		// REPLACE(concat(er.first_name,er.middle_name,er.last_name),' ','')
		$sql = "SELECT er.employee_idno,er.first_name,er.middle_name,er.last_name FROM time_record_summary_trial as trs
		LEFT JOIN employee_record as er
		ON er.employee_idno = trs.employee_idno
		INNER JOIN contract as c
		ON er.id = c.contract_emp_id
		WHERE REPLACE(concat(er.employee_idno,er.first_name,er.middle_name,er.last_name),' ','')
		 LIKE '%".$this->db->escape_like_str($search_var)."%'
		 AND c.contract_status = 'active'
		GROUP BY `employee_idno`";

		return $this->db->query($sql);
	}
	public function check_holidays($date){
		$date = $this->db->escape($date);
		$sql = "SELECT id, holiday_type FROM holidays_tran WHERE `date` = $date AND enabled = 1";
		return $this->db->query($sql);

	}
	public function get_employee_name($employee_idno){
		$employee_idno = $this->db->escape($employee_idno);

		$sql = "SELECT first_name,middle_name,last_name FROM employee_record
		WHERE employee_idno = $employee_idno";

		$name = $this->db->query($sql)->row();

		if($name != null){
			$complete_name = $name->first_name." ".$name->middle_name." ".$name->last_name;
		}else{
			$complete_name = "No Name Found";
		}
		return $complete_name;
	}
	// public function get_reg_emp_holiday_restrictions($id){
	// 	$sql = "SELECT regular_holiday,special_non_working_holiday
	// 	FROM empsta";
	// }
	public function get_emp_status($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT c.emp_status, es.regular_holiday,es.special_non_working_holiday FROM contract as c
		LEFT JOIN employee_record as er
		ON c.contract_emp_id = er.id
		LEFT JOIN empstatus as es
		ON es.empstatusid = c.emp_status
		WHERE er.employee_idno = $empid
		AND c.contract_status = 'active'";

		return $this->db->query($sql);

	}
	public function check_first_timelog($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT `date` FROM time_record_summary_trial WHERE `employee_idno` = $empid
			AND NOT(`time_out` is null) AND enabled = 1
			GROUP BY `employee_idno` ORDER BY `date` ASC LIMIT 1";

		return $this->db->query($sql);
	}
	public function check_first_timelog_date($empid,$date_created){
		$empid = $this->db->escape($empid);
		$date_created = $this->db->escape($date_created);

		$sql = "SELECT `date` FROM time_record_summary_trial WHERE `employee_idno` = $empid
			 AND `date` = $date_created
			AND NOT(`time_out` is null) AND enabled = 1
			ORDER BY `date` ASC LIMIT 1";

		return $this->db->query($sql);

	}
	public function check_overtime($empid,$date_created){
		$empid =$this->db->escape($empid);
		$date_created =$this->db->escape($date_created);

		$sql = "SELECT `employee_id`,`status`,`minutes_of_overtime` FROM `overtime_pays`
		WHERE `status` = 'certified'
		AND `employee_id` = $empid
		AND `date_rendered` = $date_created
		AND `enabled` = 1";

		return $this->db->query($sql);
	}
	public function check_workorder_timelog(){

		$sql = "SELECT * FROM (SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, f.time_in, f.time_out, g.work_sched, f.date,
            g.sched_type, g.total_whours, 'timelog' as status,
            TIME_TO_SEC(TIMEDIFF(f.time_out,f.time_in)) / 60 as time_diff
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            LEFT JOIN time_record_summary_trial f ON f.employee_idno = a.employee_idno
            INNER JOIN work_schedule g ON b.work_sched_id = g.id
            WHERE a.enabled = 1 AND b.contract_status = 'active' AND f.date = '2019-04-13'
            UNION
            SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, f.start_time as time_in, f.end_time as time_out,
            g.work_sched, f.date, g.sched_type, g.total_whours, 'workorder' as status,
            TIME_TO_SEC(TIMEDIFF(f.end_time,f.start_time)) / 60 as time_diff
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            LEFT JOIN work_order f ON  f.employee_id = a.employee_idno
            INNER JOIN work_schedule g ON b.work_sched_id = g.id
            WHERE a.enabled = 1 AND b.contract_status = 'active'
            AND f.status = 'certified' AND f.date = '2019-04-13') timelog ORDER BY employee_idno ASC, time_out DESC";


            return $this->db->query($sql);
	}
		public function getworksite(){
			$sql = "SELECT loc_latitude,loc_longitude FROM worksite
			WHERE worksiteid = 1
			AND enabled = 1";

			return $this->db->query($sql);
		}

		//for checking of absent on utilities

		//for absent computation
		public function check_absent_record($emp_id,$date_from,$date_to){
			$current_date_minus = date('Y-m-d',strtotime('-1 day'));
			$wsquery = "SELECT work_sched FROM work_schedule as ws
			LEFT JOIN employee_record as er
			ON er.employee_idno = ws.emp_idno
			LEFT JOIN contract as c
			ON c.contract_emp_id = er.id
			WHERE ws.emp_idno = '".$emp_id."'
			AND ws.enabled = 1
			AND c.contract_status = 'active'
			";
			//first timelog of employee
			$dfs = "SELECT `date` FROM time_record_summary_trial WHERE `employee_idno` = '".$emp_id."'
			AND NOT(`time_out` is null) AND enabled = 1
			GROUP BY `employee_idno` ORDER BY `date` ASC LIMIT 1";
			$dfs_check = $this->db->query($dfs)->row();
			$date_filter_start = "";
			//last timelog of employee
			$dfe = "SELECT `date` FROM time_record_summary_trial WHERE `employee_idno` = '".$emp_id."'
			AND enabled = 1
			GROUP BY `employee_idno`
			ORDER BY `date` DESC LIMIT 1";
			$dfe_check = $this->db->query($dfe)->row();
			$date_filter_end = "";
			// will return current day -1 if there is no record
			if($dfs_check != null){
				$date_filter_start = $this->db->query($dfs)->row()->date;
				$date_filter_start = $date_filter_start;
			}else{
				$date_filter_start = $current_date_minus;
			}
			if($dfe_check != null){
				$date_filter_end = $this->db->query($dfe)->row()->date;
				$date_filter_end = $date_filter_start;
			}else{
				$date_filter_end = $current_date_minus;
			}

			//will check date to prevent mishandling of absent behaviors
			if($date_filter_start > $date_from){
				$date_from = $date_filter_start;
			}
			if($date_to >= $current_date_minus){
				$date_to = $current_date_minus;
			}


			$worksched = $this->db->query($wsquery)->row();
			if($worksched != null){
				$get_sched_day = json_decode($worksched->work_sched);
				//lalagyan pa ng else if pag wala sched tas may WO
				$mh_log_array = array();
				while (strtotime($date_from) <= strtotime($date_to)) {
				$date_timelog_day = date('w', strtotime($date_from));
					if($date_timelog_day == 0){

						if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
						{
							$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
							LEFT JOIN employee_record as er
							ON er.employee_idno = trs.employee_idno
							WHERE trs.employee_idno = '".$emp_id."'
							AND trs.date_created ='".$date_from."'";

							$trs_row = $this->db->query($trs_query)->row();
							if($trs_row == null){
									//check holiday
								$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
								$check_holidays = $this->db->query($ch)->row();
									if($check_holidays	!= null ){
										//EMPLOYEE IS PRESENT DUE TO HOLIDAY
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,remarks)
									VALUES ('".$emp_id."','".$date_from."','".$get_sched_day->sun[0]."','".$get_sched_day->sun[1]."',0,0,0,0,0,0,1)";
									}
										//------EMPLOYEE IS ABSENT
									else{
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours) VALUES ('".$emp_id."','".$date_from."',0,0,0,0,0,1,0,0)";
									}

								$this->db->query($insert_absent);
							}
						}
					}
					else if($date_timelog_day == 1){

						if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
						{
							//check if there is holiday
							$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
							LEFT JOIN employee_record as er
							ON er.employee_idno = trs.employee_idno
							WHERE trs.employee_idno = '".$emp_id."'
							AND trs.date_created ='".$date_from."'";

							$trs_row = $this->db->query($trs_query)->row();
							if($trs_row == null){
									//check holiday
								$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
								$check_holidays = $this->db->query($ch)->row();
									if($check_holidays	!= null ){
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,remarks)
									VALUES ('".$emp_id."','".$date_from."','".$get_sched_day->mon[0]."','".$get_sched_day->mon[1]."',0,0,0,0,0,0,1)";
									}else{ // employee is absent
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours) VALUES ('".$emp_id."','".$date_from."',0,0,0,0,0,1,0,0)";
									}
									$this->db->query($insert_absent);
							}
						}
					}
					else if($date_timelog_day == 2){

						if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
						{
							//check if there is holiday
						$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
						LEFT JOIN employee_record as er
						ON er.employee_idno = trs.employee_idno
						WHERE trs.employee_idno = '".$emp_id."'
						AND trs.date_created ='".$date_from."'";

							$trs_row = $this->db->query($trs_query)->row();
							if($trs_row == null){
									//check holiday
								$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
								$check_holidays = $this->db->query($ch)->row();
									if($check_holidays	!= null ){
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,remarks)
									VALUES ('".$emp_id."','".$date_from."','".$get_sched_day->tue[0]."','".$get_sched_day->tue[1]."',0,0,0,0,0,0,1)";
									}else{ // employee is absent
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours) VALUES ('".$emp_id."','".$date_from."',0,0,0,0,0,1,0,0)";
									}
									$this->db->query($insert_absent);
							}
						}
					}
					else if($date_timelog_day == 3){

						if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
						{
							//check if there is holiday
						$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
						LEFT JOIN employee_record as er
						ON er.employee_idno = trs.employee_idno
						WHERE trs.employee_idno = '".$emp_id."'
						AND trs.date_created ='".$date_from."'";

							$trs_row = $this->db->query($trs_query)->row();
							if($trs_row == null){
									//check holiday
								$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
								$check_holidays = $this->db->query($ch)->row();
									if($check_holidays	!= null ){
										//EMPLOYEE IS PRESENT DUE TO HOLIDAY
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,remarks)
									VALUES ('".$emp_id."','".$date_from."','".$get_sched_day->wed[0]."','".$get_sched_day->wed[1]."',0,0,0,0,0,0,1)";
									}
										//------EMPLOYEE IS ABSENT
									else{
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours) VALUES ('".$emp_id."','".$date_from."',0,0,0,0,0,1,0,0)";
									}

								$this->db->query($insert_absent);
							}
						}
					}
					else if($date_timelog_day == 4){

						if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
						{
							//check if there is holiday
						$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
						LEFT JOIN employee_record as er
						ON er.employee_idno = trs.employee_idno
						WHERE trs.employee_idno = '".$emp_id."'
						AND trs.date_created ='".$date_from."'";

							$trs_row = $this->db->query($trs_query)->row();
							if($trs_row == null){
									//check holiday
								$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
								$check_holidays = $this->db->query($ch)->row();
									if($check_holidays	!= null ){
										//EMPLOYEE IS PRESENT DUE TO HOLIDAY
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,remarks)
									VALUES ('".$emp_id."','".$date_from."','".$get_sched_day->thu[0]."','".$get_sched_day->thu[1]."',0,0,0,0,0,0,1)";
									}
										//------EMPLOYEE IS ABSENT
									else{
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours) VALUES ('".$emp_id."','".$date_from."',0,0,0,0,0,1,0,0)";
									}

								$this->db->query($insert_absent);
							}
						}
					}
					else if($date_timelog_day == 5){

						if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
						{
							//check if there is holiday
						$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
						LEFT JOIN employee_record as er
						ON er.employee_idno = trs.employee_idno
						WHERE trs.employee_idno = '".$emp_id."'
						AND trs.date_created ='".$date_from."'";

							$trs_row = $this->db->query($trs_query)->row();
							if($trs_row == null){
									//check holiday
								$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
								$check_holidays = $this->db->query($ch)->row();
									if($check_holidays	!= null ){
										//EMPLOYEE IS PRESENT DUE TO HOLIDAY
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,remarks)
									VALUES ('".$emp_id."','".$date_from."','".$get_sched_day->fri[0]."','".$get_sched_day->fri[1]."',0,0,0,0,0,0,1)";
									}
										//------EMPLOYEE IS ABSENT
									else{
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours) VALUES ('".$emp_id."','".$date_from."',0,0,0,0,0,1,0,0)";
									}

								$this->db->query($insert_absent);
							}
						}
					}
					else if($date_timelog_day == 6){

						if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
						{
							//check if there is holiday
						$trs_query = "SELECT trs.date_created,trs.employee_idno,trs.time_in,trs.time_out,er.first_name,er.middle_name,er.last_name,trs.man_hours,trs.absent,trs.late,trs.overtime,trs.undertime FROM time_record_summary as trs
						LEFT JOIN employee_record as er
						ON er.employee_idno = trs.employee_idno
						WHERE trs.employee_idno = '".$emp_id."'
						AND trs.date_created ='".$date_from."'";

							$trs_row = $this->db->query($trs_query)->row();
							if($trs_row == null){
									//check holiday
								$ch = "SELECT * FROM holidays_tran WHERE `date` = '".$date_from."' AND enabled = 1";
								$check_holidays = $this->db->query($ch)->row();
									if($check_holidays	!= null ){
										//EMPLOYEE IS PRESENT DUE TO HOLIDAY
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours,remarks)
									VALUES ('".$emp_id."','".$date_from."','".$get_sched_day->wed[0]."','".$get_sched_day->wed[1]."',0,0,0,0,0,0,1)";
									}
										//------EMPLOYEE IS ABSENT
									else{
									$insert_absent = "INSERT INTO time_record_summary(employee_idno,date_created,time_in,time_out,late,overtime,undertime,absent,total_minutes,man_hours) VALUES ('".$emp_id."','".$date_from."',0,0,0,0,0,1,0,0)";
									}

								$this->db->query($insert_absent);
							}
						}
				}
				$date_from = date ("Y-m-d", strtotime("+1 day", strtotime($date_from)));
			}
			}
		}

}

					// {
					// 	//may pasok - payratio 1
					// 	$getpayratio1_regular = $this->payroll_history_model->get_regular_payratio1()->row();
					// 		if($getpayratio1_regular != null){
					// 			$p1 = $getpayratio1_regular->payratio;
					// 		}else{
					// 			$p1 = 2;
					// 		}
					// 	$x = ($dr * $p1) - $dr;
					// 	$regular_holiday_pays = $regular_holiday_pays + $x;

					// }
					// else if(($get_sched_day->tue[0] == "") || ($get_sched_day->tue[1] == "") || ($get_sched_day->tue[4] == "") || ($get_sched_day->tue[3]))
					// {
					// 	$getpayratio2_regular = $this->payroll_history_model->get_regular_payratio2()->row();
					// 		if($getpayratio2_regular != null){
					// 			$p2 = $getpayratio2_regular->payratio2;
					// 		}else{
					// 			$p2 = 2;
					// 		}
					// 	$x = ($dr * $p2) - $dr;
					// 	$regular_holiday_pays = $regular_holiday_pays + $x;

					// }
					// else{
					// 	$regular_holiday_pays = $regular_holiday_pays + 0;
					// }
