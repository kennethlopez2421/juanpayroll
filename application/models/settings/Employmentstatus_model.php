<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employmentstatus_model extends CI_Model {

	public function getEmploymentStatus($start,$length,$search) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM empstatus WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM empstatus WHERE enabled = 1 LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM empstatus WHERE enabled = 1 ";
		}


		return $this->db->query($sql);
	}

	public function getEmpStatusByDesc($data) {
		$sql = "SELECT * FROM empstatus WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO empstatus (description,regular_holiday,special_non_working_holiday,leave_pay,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE empstatus SET description = ?, regular_holiday = ?, special_non_working_holiday = ?, leave_pay = ?, date_updated = ? WHERE empstatusid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE empstatus SET enabled = ? WHERE empstatusid = ?";
		$this->db->query($sql, $data);
	}

}
