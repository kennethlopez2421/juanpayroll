<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Timelog_model extends CI_Model {

	public function get_emp_information($id,$rf_id = false){
		$id = $this->db->escape($id);
		$sql = "SELECT a.id, a.employee_idno,
			CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, b.rf_number, b.status
			FROM employee_record a LEFT JOIN hris_rfid b ON a.employee_idno = b.employee_idno
			WHERE a.enabled = 1 AND a.employee_idno = $id";

		if($rf_id !== false){
			$rf_id = $this->db->escape($rf_id);
			$sql .= " AND b.rf_number = $rf_id AND b.status = 'active' AND b.enabled = 1";
		}

		return $this->db->query($sql);
	}

	public function get_last_rfid($emp_id,$status = 'active'){
		$sql = "SELECT * FROM hris_rfid WHERE employee_idno = ? AND status = ? AND enabled = 1
			ORDER BY id DESC LIMIT 1";
		$data = array($emp_id,$status);
		return $this->db->query($sql,$data);
	}

	public function get_emp_thru_rfid($id){
		$sql = "SELECT a.employee_idno, b.work_site_id, d.loc_latitude, d.loc_longitude, d.distance,
			CONCAT(a.first_name,', ',a.middle_name,' ',a.last_name) as fullname, d.location
			FROM employee_record a
			INNER JOIN contract b ON a.id = b.contract_emp_id
			INNER JOIN hris_rfid c ON a.employee_idno = c.employee_idno
			LEFT JOIN worksite d ON b.work_site_id = d.worksiteid
			WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active' AND c.status = 'active'
			AND c.enabled = 1 AND d.enabled = 1 AND c.rf_number = ?";
		$data = array($id);
		return $this->db->query($sql,$data);
	}

	public function get_rfid($rfid,$status = false){
		$rfid = $this->db->escape($rfid);
		$sql = "SELECT * FROM hris_rfid WHERE enabled = 1 AND rf_number = $rfid";
		if(!$status == false){
			$status = $this->db->escape($status);
			$sql .= " AND status = $status";
		}
		return $this->db->query($sql);
	}

	public function get_rfid_thru_empid($emp_id){
		$sql = "SELECT * FROM hris_rfid WHERE enabled = 1 AND employee_idno = ? AND status = 'active'";
		$data = array($emp_id);
		return $this->db->query($sql,$data);
	}

	public function getTimeIn($data) {
		// $sql = "SELECT * FROM timelog WHERE employee_idno = ? AND `date` = ? AND type = 'in' AND enabled = 1 ";
		$sql = "SELECT * FROM timelog WHERE employee_idno = ? AND enabled = 1 ";

		return $this->db->query($sql,$data);
	}

	public function getLastLog($data) {
		$sql = "SELECT * FROM timelog WHERE employee_idno = ? AND `date` = ? AND enabled = 1 ORDER BY id DESC LIMIT 1";
		return $this->db->query($sql,$data);
	}

	public function get_worksite($empid){
		$sql = "SELECT c.* FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 LEFT JOIN worksite c ON b.work_site_id = c.worksiteid
		 WHERE b.contract_type = 'fixed' AND b.contract_status = 'active'
		 AND a.employee_idno = ?";
		$data = array($empid);
		$result = $this->db->query($sql,$data);

		if($result->num_rows() > 0){
			return $result;
		}else{
			$sql = "SELECT c.* FROM employee_record a
			 INNER JOIN contract b ON a.id = b.contract_emp_id
			 LEFT JOIN worksite c ON b.work_site_id = c.worksiteid
			 WHERE b.contract_type = 'open' AND a.employee_idno = ?
			 ORDER BY b.created_at DESC LIMIT 1";
			$data = array($empid);
			return $this->db->query($sql,$data);
		}
		// $sql = "SELECT c.* FROM employee_record a
		//  INNER JOIN contract b ON a.id = b.contract_emp_id
		//  LEFT JOIN worksite c ON b.work_site_id = worksiteid
		//  WHERE (CASE WHEN b.contract_type = 'fixed' THEN b.contract_status = 'active' ELSE b.contract_type = 'open' END)
		//  AND a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND
		//  a.employee_idno = ?";
		// return $this->db->query($sql,$data);
	}

	public function get_worksite_byname($name){
		$name = $this->db->escape($name);
		$sql = "SELECT * FROM worksite WHERE description = $name WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function get_worksite_byid($id){
		$id = $this->db->query($id);
		$sql = "SELECT * FROM worksite WHERE worksiteid = $id AND enabled = 1";
		return $this->db->query($sql);
	}

	public function get_tag_worksite($empid){
		$empid = $this->db->escape($empid);
		// $sql = "SELECT b.work_site_id as worksiteid FROM employee_record a
		//  INNER JOIN contract b ON a.id = b.contract_emp_id
		//  WHERE a.enabled = 1 AND b.enabled = 1 AND
		//  (CASE WHEN b.contract_type = 'fixed' THEN b.contract_status = 'active' ELSE b.contract_type = 'open' END)
		//  AND a.employee_idno = $empid";
		// return $this->db->query($sql);
		$sql = "SELECT b.work_site_id as worksiteid FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_type = 'fixed'
		 AND b.contract_status = 'active' AND a.employee_idno = $empid";
		$result = $this->db->query($sql);

		if($result->num_rows() > 0){
			return $result;
		}else{
			$sql = "SELECT b.work_site_id as worksiteid FROM employee_record a
			 INNER JOIN contract b ON a.id = b.contract_emp_id
			 WHERE b.contract_type = 'open' AND a.employee_idno = $empid
			 ORDER BY b.created_at DESC LIMIT 1";
			return $this->db->query($sql);
		}
	}

	public function get_all_worksite_id($empid){
		$empid = $this->db->escape($empid);

		$sql = "SELECT b.work_site_id FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_type = 'fixed'
		 AND b.contract_status = 'active' AND a.employee_idno = $empid";
		$result = $this->db->query($sql);

		if($result->num_rows() > 0){
			return $result;
		}else{
			$sql = "SELECT b.work_site_id FROM employee_record a
			 INNER JOIN contract b ON a.id = b.contract_emp_id
			 WHERE b.contract_type = 'open' AND a.employee_idno = $empid
			 ORDER BY b.created_at DESC LIMIT 1";
			return $this->db->query($sql);
		}
		// $sql = "SELECT b.work_site_id FROM employee_record a
		//  INNER JOIN contract b ON a.id = b.contract_emp_id
		//  WHERE a.enabled = 1 AND b.enabled = 1 AND
		//  (CASE WHEN b.contract_type = 'fixed' THEN b.contract_status = 'active' ELSE b.contract_type = 'open' END)
		//  AND a.employee_idno = $empid";
		//
		// return $this->db->query($sql);
	}

	public function get_all_worksite($ids){
		$this->db->select('*');
		$this->db->where_in('worksiteid', $ids);
		return $this->db->get('worksite');
		// $sql = "SELECT * FROM worksite WHERE worksiteid IN(?) AND enabled = 1";
		// $data = array($ids);
		// return $this->db->query($sql,$data);
	}

	public function get_all_facial_recog(){
    $sql = "SELECT CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname,
      a.descriptor, a.employee_idno
      FROM hris_facial_recog a
      INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
      WHERE a.enabled = 1 AND b.enabled = 1";
    return $this->db->query($sql);
  }

	public function get_employee_with_worksched($id){
		$id = $this->db->escape($id);
		$sql = "SELECT a.*, c.work_sched FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 INNER JOIN work_schedule c ON b.work_sched_id = c.id
		 WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'
		 AND a.employee_idno = $id";

		return $this->db->query($sql);
	}

	public function check_worksite_link($name,$id){
		$name = $this->db->escape($name);
		$id = $this->db->escape($id);
		$sql = "SELECT * FROM worksite WHERE worksiteid = $id
		 AND description = $name AND enabled = 1";
		return $this->db->query($sql);
	}

	public function set_rf_idnumber($data){
		$this->db->insert('hris_rfid',$data);
		return ($this->db->affected_rows() > 0) ? true: false;
	}

	public function set_timelog($data){
		$this->db->insert('time_record_summary_trial', $data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function update_rf_status($data){
		$sql = "UPDATE hris_rfid SET status = ? WHERE id = ? AND status = 'active' AND enabled = 1";
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function setTime($data) {
		$sql = "INSERT INTO time_record_summary_trial(employee_idno,worksite,`time_in`,`date`,type,mode,img_url,enabled) VALUES(?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function getworksite($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT ws.loc_latitude,ws.loc_longitude FROM contract as c
		LEFT JOIN employee_record as er
		ON c.contract_emp_id = er.id
		LEFT JOIN worksite as ws
		ON ws.worksiteid = c.work_site_id
		WHERE er.employee_idno = $empid";

		return $this->db->query($sql);
	}
	//---------------Time Record Summary-----------------
	public function getTimeLogPerDay($empid,$date){
	//this will look for the timelog of the employees per date
		// $sql = "SELECT * FROM trs_try WHERE `date` = $date AND employee_idno = '".$empid."' AND enabled = 1";
		$sql = "SELECT * FROM time_record_summary_trial WHERE employee_idno = '".$empid."' AND date = '".$date."' AND enabled = 1";
		return $this->db->query($sql);
	}
	public function getLastTimeLogPerday($empid,$date){
		$empid = $this->db->escape($empid);
		$date = $this->db->escape($date);
		$sql = "SELECT * FROM time_record_summary_trial
		 WHERE employee_idno = $empid
		 AND (date = $date OR date = DATE_ADD($date, INTERVAL -1 DAY))
		 AND enabled = 1 ORDER BY id DESC LIMIT 1";
		return $this->db->query($sql);
	}
	public function insertTimein($data){
		//this will insert the time in of the employees per date
		$sql = "INSERT INTO time_record_summary_trial(employee_idno,worksite,`time_in`,`date`,type,mode,img_url,enabled,current_location,date_created) VALUES(?,?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}
	public function updateTimeout($timeout,$getlocation,$date,$empid,$date_created){
		$timeout = $this->db->escape($timeout);
		$getlocation = $this->db->escape($getlocation);
		$date = $this->db->escape($date);
		$empid = $this->db->escape($empid);
		//this will update the timeout of the last timein of employees per date
		$sql = "UPDATE `time_record_summary_trial` SET  `time_out` = $timeout,`current_location` = $getlocation, `date_created` = '$date_created'  WHERE id =
			(SELECT id FROM (SELECT `id` FROM `time_record_summary_trial` WHERE employee_idno = $empid ORDER BY id DESC LIMIT 1) AS m2)
			AND `date`= $date
			AND enabled = 1";
		return $this->db->query($sql);
	}

	public function update_timelog($mode,$img_url,$location,$time,$datetime,$date,$empid){
		$mode = $this->db->escape($mode);
		$img_url = $this->db->escape($img_url);
		$location = $this->db->escape($location);
		$time = $this->db->escape($time);
		$datetime = $this->db->escape($datetime);
		$date = $this->db->escape($date);
		$empid = $this->db->escape($empid);
		$sql = "UPDATE time_record_summary_trial
			SET `mode` = $mode, `img_url` = $img_url, `current_location` = $location, `time_out` = $time, `date_created` = $datetime
			WHERE enabled = 1 AND id =
			(SELECT * FROM (SELECT id FROM time_record_summary_trial WHERE `date` = $date AND `employee_idno` = $empid
				ORDER BY id DESC LIMIT 1) as time2)";
		$this->db->query($sql);
		// return $this->db->last_query();
		return ($this->db->affected_rows() > 0) ? true: false;
	}
	//this function will update total minutes on every timeout of employee

	//will check last timelog to check if emp is absent
	public function check_time_record_summary_trial($empid,$date_created){
		$sql = "SELECT `employee_idno`,`date`,time_in,time_out,status_absent FROM time_record_summary_trial
		WHERE employee_idno = '".$empid."'
		AND `date` < '".$date_created."'
		AND enabled = 1
		ORDER BY `date` DESC LIMIT 1";

		return $this->db->query($sql);
	}
	public function get_worksite_distance($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT w.distance FROM contract as c
				INNER JOIN employee_record as er
				ON c.contract_emp_id = er.id
				INNER JOIN worksite as w
				ON w.worksiteid = c.work_site_id
				WHERE er.employee_idno = $empid
				AND c.enabled = 1";

		return $this->db->query($sql);
	}
	public function update_absent($empid,$date_created){
		$sql = "UPDATE time_record_summary_trial SET status_absent = 1
		WHERE employee_idno = '".$empid."'
		AND `date` = '".$date_created."'
		AND enabled = 1";

		return $this->db->query($sql);
	}
	public function check_timelog_interval($empid,$date){
		$empid = $this->db->escape($empid);
		$date = $this->db->escape($date);
		$sql = "SELECT `date_created` FROM `time_record_summary_trial`
		WHERE employee_idno = $empid
		AND `date` = $date ORDER BY `id` DESC LIMIT 1";

		return $this->db->query($sql);
	}

}
