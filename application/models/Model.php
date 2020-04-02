<?php
class Model extends CI_Model {

	public function validate_username($username){ // validate email if exist and get the info
		$sql = "SELECT a.*, b.hierarchy_lvl as pos_lvl
						FROM hris_users a INNER JOIN hris_position b ON a.position_id = b.position_id
						WHERE a.username = ? AND a.enabled = 1 LIMIT 1";
		$data = array($username);
		return $this->db->query($sql, $data);
	}

	public function validate_sess_id($sess_id){
		$sql = "SELECT b.hierarchy_lvl FROM hris_users a
		 INNER JOIN hris_position b  ON a.position_id = b.position_id
		 WHERE a.enabled = 1 AND b.enabled = 1 AND a.user_id = ?";
		$data = array($sess_id);
		return $this->db->query($sql,$data);
	}

	public function get_transaction_email($nav_id, $dept_id){
		$nav_id = $this->db->escape($nav_id);
		$dept_id= $this->db->escape($dept_id);
		$sql = "SELECT a.approver, a.certifier,
		 @approver_email := (SELECT GROUP_CONCAT(email SEPARATOR ', ') FROM employee_record WHERE FIND_IN_SET(employee_idno,a.approver)) as approver_email,
		 @certifier_email := (SELECT GROUP_CONCAT(email SEPARATOR ', ') FROM employee_record WHERE FIND_IN_SET(employee_idno,a.certifier)) as certifier_email
		 FROM hris_transaction_email_settings a
		 WHERE a.content_nav_id = $nav_id AND a.department_id = $dept_id
		 AND a.enabled = 1";
		return $this->db->query($sql);
		// return $this->db->last_query();
	}

	public function get_fullname_by_id($id){
		$id = $this->db->escape($id);
		$sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, email
		 FROM employee_record a WHERE a.employee_idno = $id AND a.enabled = 1";
		return $this->db->query($sql);
	}

	public function get_emp_by_email($email){
		$email = $this->db->escape($email);
		$sql = "SELECT * FROM employee_record WHERE email = $email AND isActive = 1 AND enabled = 1";
		return $this->db->query($sql);
	}

	public function get_user_w_pos_id($pos_id){
		$pos_id = $this->db->escape($pos_id);
		$sql = "SELECT * FROM hris_users WHERE position_id = $pos_id";
		return $this->db->query($sql);
	}

	public function get_position($email){
		$sql = "SELECT * FROM hris_position WHERE position_id = ? LIMIT 1";
		$data = array($email);
		return $this->db->query($sql, $data);
	}

	public function get_position_by_id($id = false){
		// $sql = "SELECT * FROM position WHERE enabled = 1";
		$sql = "SELECT a.*,
		 @manager_name := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE position_id = 5 AND deptId = a.deptId AND enabled = 1 LIMIT 1) as manager_name,
		 @manager_position_id := (SELECT position_id as fullname FROM hris_users WHERE position_id = 5 AND deptId = a.deptId AND enabled = 1 LIMIT 1) as manager_position_id,
		 @hrmanager_name := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE position_id = 3 AND deptId = a.deptId AND enabled = 1 LIMIT 1) as hrmanager_name,
		 @manager_position := (SELECT description FROM position WHERE pos_access_lvl = @manager_position_id AND enabled = 1) as manager_position,
		 @department_name := (SELECT description FROM department WHERE departmentid = a.deptId AND enabled = 1) as department_name
		 FROM position a
		 WHERE a.enabled = 1";
		if($id){
			$id = $this->db->escape($id);
			$sql .= " AND a.positionid = $id";
		}
		$sql .= " ORDER BY a.description ASC, department_name ASC";
		return $this->db->query($sql);
	}

	public function get_main_page_navigation(){
		$sql = "SELECT * FROM hris_main_navigation WHERE enabled = 1 ORDER BY arrangement ASC";
		return $this->db->query($sql);
	}

	public function get_position_details_access($position_id){
		$sql = "SELECT * FROM hris_position WHERE position_id = ? AND enabled = 1";
		$data = array($position_id);
		return $this->db->query($sql, $data);
	}

	public function get_position_details_access_emp(){
		$sql = "SELECT * FROM hris_position WHERE enabled = 1 ORDER BY hierarchy_lvl DESC LIMIT 1";
		return $this->db->query($sql);
	}

	public function get_immediate_head($deptid){
		$deptid = $this->db->escape($deptid);
		$sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
		 c.description as pos, a.employee_idno
		 FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 INNER JOIN position c ON b.position_id = c.positionid
		 INNER JOIN hris_position d ON b.position_access_lvl = d.position_id
		 WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1
		 AND b.contract_status = 'active' AND d.hierarchy_lvl <= 4 AND c.deptId = $deptid
		 ORDER BY d.hierarchy_lvl DESC LIMIT 1";
		return $this->db->query($sql);
	}

	public function search_user($keyword, $fields = false){
		$keyword = $this->db->escape("%".$keyword."%");
		$sql = "SELECT a.*, CONCAT(last_name,', ',first_name,' ',middle_name) as fullname, c.deptId
			FROM employee_record a
			INNER JOIN contract b ON a.id = b.contract_emp_id
			LEFT JOIN position c ON b.position_id = c.positionid
			WHERE (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $keyword
			OR CONCAT(a.first_name,' ',a.middle_name,'. ',a.last_name) LIKE $keyword
			OR CONCAT(a.first_name,' ',a.middle_name,' ',a.last_name) LIKE $keyword
			OR CONCAT(a.first_name,' ',a.last_name) LIKE $keyword
			OR CONCAT(a.last_name,', ',a.first_name) LIKE $keyword
			OR a.last_name LIKE $keyword OR a.first_name LIKE $keyword)
			AND a.enabled = 1 and a.isActive = 1 AND b.enabled = 1 AND b.contract_status = 'active'
			AND c.enabled = 1";

		if($fields){
			$sql .= " AND $fields != ''";
		}

		$sql .= " ORDER BY fullname ASC LIMIT 5";
		// return $this->db->last_query();
		return $this->db->query($sql);
	}

	// 07/13/18
	public function get_content_navigation($main_nav_id){
		$sql = "SELECT * FROM hris_content_navigation WHERE cn_fkey = ? AND status = 1  ORDER BY cn_name ASC";
		$data = array($main_nav_id);
		return $this->db->query($sql,$data);
	}

	public function get_main_nav_id($labelname){
		$sql = "SELECT * FROM hris_main_navigation WHERE main_nav_desc = ? AND enabled = 1 LIMIT 1 ";
		$data = array($labelname);
		return $this->db->query($sql, $data);

	}

	public function get_url_content_db($arr_){
		$sql = "SELECT cn_url FROM hris_content_navigation WHERE id IN ? AND status = 1";
		$data = array($arr_);
		return $this->db->query($sql, $data);
	}

	public function get_url_content_id($url){
		$url = $this->db->escape($url);
		$sql = "SELECT id, cn_name FROM hris_content_navigation WHERE cn_url = $url AND status = 1 ORDER BY id ASC LIMIT 1";
		return $this->db->query($sql);
	}

	public function get_url_content_hasline_db(){
		$sql = "SELECT id, ch_name FROM  hris_content_hasline WHERE status = 1";
		return $this->db->query($sql);
	}

	public function get_datanum_mainnavigation_using_labelname($labelname){
		$sql = "SELECT main_nav_id FROM hris_main_navigation WHERE main_nav_desc = ? LIMIT 1";
		$data = array($labelname);
		return $this->db->query($sql, $data)->row()->main_nav_id;


	}

	public function get_system_user_pos(){
		$sql = "SELECT * FROM hris_position WHERE enabled = 1 AND hierarchy_lvl < 2 ORDER BY hierarchy_lvl ASC, position ASC";
		return $this->db->query($sql);
	}

	public function get_userInformation($id){
		$sql = "SELECT * FROM hris_users
				WHERE user_id = ? LIMIT 1";
		$data = array($id);
		return $this->db->query($sql, $data);
	}

	public function get_userInformation2($id){
		$sql = "SELECT a.*, b.position FROM hris_users a LEFT JOIN hris_position b ON a.position_id = b.position_id WHERE a.enabled = 1 AND user_id = ?";
		$data = array($id);
		return $this->db->query($sql,$data);
	}

	public function get_employee($emp_idno = false){
		$sql = "SELECT * FROM employee_record WHERE enabled = 1";
		if($emp_idno){
			$emp_idno = $this->db->escape($emp_idno);
			$sql .= " AND id = $emp_idno";
		}
		return $this->db->query($sql);
	}

	// public function get_emp_by_dept($deptId){ // for logs
	// 	$sql = "SELECT CONCAT(a.last_name,',', a.first_name,' ', a.middle_name) as fullname, a.employee_idno
	// 					FROM employee_record a
	// 					LEFT JOIN contract b ON a.id = b.contract_emp_id
	// 					LEFT JOIN hris_position c ON b.position_id
	// 					WHERE c.deptId = ?";
	// 	$data = array($deptId);
	// 	return $this->db->query($sql,$data);
	// }

	public function getDepartment($deptId = false){
		if($deptId == false){
			$sql = "SELECT * FROM department WHERE enabled = 1 ORDER BY description ASC";
			return $this->db->query($sql);
		}else{
			$sql = "SELECT * FROM department WHERE enabled = 1 and departmentid = ? ORDER BY description ASC";
			$data = array($deptId);
			return $this->db->query($sql,$data);
		}
	}

	public function get_user_position($pos_id = false){
		if($pos_id == false){
			$sql = "SELECT a.*, b.description as dept
				FROM position a
				INNER JOIN department b ON a.deptId = b.departmentid
				WHERE a.enabled = 1 ORDER BY b.description ASC";
			return $this->db->query($sql);
		}else{
			$sql = "SELECT a.*, b.description as dept
				FROM position a
				INNER JOIN department b ON a.deptId = b.departmentid
				WHERE a.enabled = 1 AND positionid = ? ORDER BY b.description ASC";
			$data = array($pos_id);
			return $this->db->query($sql,$data);
		}
	}

	public function get_emp_by_dept($deptId){
		$sql = "SELECT a.id as emp_id, a.employee_idno, d.work_sched, d.total_whours, d.total_bhours, d.sched_type,
						CONCAT(a.last_name, ',', a.first_name, ' ', a.middle_name) as fullname
						FROM employee_record a LEFT JOIN contract b ON a.id = b.contract_emp_id
						LEFT JOIN position c ON b.position_id = c.positionid
						LEFT JOIN work_schedule d ON b.work_sched_id = d.id
						WHERE a.enabled = 1 AND b.contract_status = 'active' AND c.deptId = ? AND d.enabled = 1
						GROUP BY a.employee_idno ORDER BY fullname ASC, a.last_name ASC, a.middle_name ASC ";
		$data = array($deptId);
		return $this->db->query($sql,$data);
	}

	public function get_position_lvl($pos_lvl = false){
		if($pos_lvl){
			$sql = "SELECT position, position_id, hierarchy_lvl FROM hris_position
				WHERE enabled = 1 AND hierarchy_lvl BETWEEN 2 AND ?
				ORDER BY hierarchy_lvl ASC";
			$data = array($pos_lvl);
			return $this->db->query($sql,$data);
		}

		$sql = "SELECT position, position_id FROM hris_position WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function get_position_access_lvl($lvl){
		$sql = "SELECT a.employee_idno as admin_id,
			CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname) as fullname,
			b.hierarchy_lvl as access_lvl, b.position
			FROM hris_users a
			INNER JOIN hris_position b ON a.position_id = b.position_id
			WHERE a.enabled = 1 AND b.enabled = 1 AND b.hierarchy_lvl < ?";
		$data = array($lvl);
		return $this->db->query($sql,$data);
	}

	public function get_dept_access($emp_idno){
		$emp_idno = $this->db->escape($emp_idno);
		$sql = "SELECT c.department_access FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 INNER JOIN position c ON b.position_id = c.positionid
		 WHERE a.employee_idno = $emp_idno AND a.enabled = 1
		 AND b.enabled = 1 AND c.enabled = 1 AND b.contract_status = 'active'";
		return $this->db->query($sql);
	}

	public function get_emp_fullname($emp_idno){
		$sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname
			FROM employee_record a WHERE a.employee_idno = ?";
		$data = array($emp_idno);
		return $this->db->query($sql,$data);
	}

	public function get_hris_companies($id = false){
		$sql = "SELECT * FROM hris_companies WHERE enabled = 1";
		if($id){
			$id = $this->db->escape($id);
			$sql .= " AND id = $id";
		}
		$sql .= " ORDER BY company ASC";
		return $this->db->query($sql);
	}

	// update sal
	public function get_all_emp(){
		$sql = "SELECT b.sal_cat, b.emp_leave, a.id, b.id as c_id, b.enabled, a.first_name,
						c.exchange_rate as ex_rate, d.work_sched, d.id as w_id
						FROM employee_record a
						LEFT JOIN contract b ON a.id = b.contract_emp_id
						LEFT JOIN hris_exchange_rates c ON b.currency = c.currency_code
						LEFT JOIN work_schedule d ON b.work_sched_id = d.id
						WHERE b.contract_status = 'active' AND a.enabled = 1";
		return $this->db->query($sql);
	}

	public function get_worksite($id = false){
		$sql = "SELECT * FROM worksite WHERE enabled = 1";

		if($id){
			$id = $this->db->escape($id);
			$sql .= " AND worksiteid = $id";
		}

		$sql .= " ORDER BY description ASC";
		return $this->db->query($sql);
	}

	public function get_attendance_data($id){
		$id = $this->db->escape($id);
		$sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
			c.work_sched, c.total_whours, c.total_bhours, c.sched_type
		 FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 INNER JOIN work_schedule c ON b.work_sched_id = c.id
		 WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND b.contract_status = 'active'
		 AND a.employee_idno = $id";

		return $this->db->query($sql);
	}

	public function get_timelog($id,$date){
    $escape_emp_idno = $this->db->escape($id);
    $escape_date = $this->db->escape($date);

    $sql = "SELECT * FROM (
      SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
      a.date as timelog_date, 'timelog' as type
      FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
      UNION
      SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
      b.date as timelog_date, 'work order' as type
      FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
      AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
    ) as timelog ORDER BY timelog_date ASC, time_in ASC";

    return $this->db->query($sql);
  }

	public function get_bank($id = false){
		$sql = "SELECT * FROM bank WHERE enabled = 1";
		if($id){
			$id = $this->db->escape($id);
			$sql .= " AND bank_id = $id";
		}
		$sql .= " ORDER BY bank_name ASC";
		return $this->db->query($sql);
	}

	public function get_company($id = false){
		$sql = "SELECT * FROM hris_companies WHERE enabled = 1";
		if($id = false){
			$id = $this->db->escape($id);
			$sql .= " AND id = $id";
		}
		return $this->db->query($sql);
	}

	public function get_emp_status($id = false){
		$sql = "SELECT * FROM empstatus WHERE enabled = 1";
		if($id){
			$id = $this->db->escape($id);
			$sql .= " AND empstatusid = $id";
		}
		return $this->db->query($sql);
	}

	public function get_payoutmedium($id = false){
		$sql = "SELECT * FROM payoutmedium WHERE enabled = 1";
		if($id){
			$id = $this->db->escape($id);
			$sql .= " AND payoutmediumid = $id";
		}
		return $this->db->query($sql);
	}

	public function get_paytype($id = false){
		$sql = "SELECT * FROM paytype WHERE enabled = 1";
		if($id){
			$id = $this->db->escape($id);
			$sql .= " AND paytypeid = $id";
		}

		return $this->db->query($sql);
	}

	public function get_department_type(){
		$sql = "SELECT * FROM department_type WHERE enabled = 1
		 ORDER BY id DESC";
		return $this->db->query($sql);
	}

	public function get_dept_by_type($type){
		$type = $this->db->escape($type);
		$sql = "SELECT departmentid FROM department WHERE enabled = 1 AND department_type = $type";
		return $this->db->query($sql);
	}

	public function update_sal_and_leve($data){
		$sql = "UPDATE contract SET total_sal = ? , total_sal_converted = ?, total_leave = ? WHERE id = ?";
		$this->db->query($sql,$data);
	}

	public function update_base_pay($data){
		$sql = "UPDATE contract SET base_pay = ? WHERE id = ?";
		$this->db->query($sql,$data);
	}

	// update worksched
	public function update_worksched($data){
		$sql = "UPDATE work_schedule SET total_whours = ?, total_bhours = ? WHERE id = ?";
		$this->db->query($sql,$data);
		return $this->db->last_query();
	}

	// change_pass 092418
	public function change_pass($email,$pass){
		$email = $this->db->escape($email);
		$pass = $this->db->escape($pass);
		$sql = "UPDATE hris_users SET password = $pass WHERE username = $email AND enabled = 1";
		$this->db->query($sql);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function check_pass_using_id_fk($id){
		$sql = "SELECT password FROM hris_users WHERE user_id = ? LIMIT 1";
		$data = array($id);
		return $this->db->query($sql, $data);
	}

	// public function check_current_pass($pw,$id){
	// 	$sql = "SELECT password FROM hris_users WHERE enabled = 1 AND password = ? AND user_id = ?";
	// 	$data = array($pw,$id);
	// 	return $this->db->query($sql,$data);
	// }

	public function update_password($secNewpass, $id){
		$sql = "UPDATE hris_users SET password = ?, date_updated = ? WHERE user_id = ? ";
		$data = array($secNewpass, todaytime(), $id);
		$this->db->query($sql,$data);
	}
	// change_pass 092418
}
