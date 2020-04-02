<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Philhealth_model extends CI_Model {

	public function getPhil ($start,$length) {
		if($start != null && $length != null) {
			$sql = "SELECT * FROM philhealth WHERE enabled = 1 LIMIT ".$start.",".$length." ";
		}else {
			$sql = "SELECT * FROM philhealth WHERE enabled = 1 ";
		}
		return $this->db->query($sql);
	}

	public function getphID($data) {
		$sql = "SELECT * FROM philhealth WHERE phID = ?";
		$data = array($data);
		return $this->db->query($sql,$data);
	}

	public function checkSalRange($data){
		$sql = "SELECT * FROM philhealth WHERE basic_mo_sal = ? AND basic_mo_sal1 = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO philhealth(basic_mo_sal, basic_mo_sal1, mo_contribution, mo_contribution1, employee_share, employee_share1, employer_share, employer_share1, enabled, user_id, date_created, date_updated) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE philhealth SET basic_mo_sal = ?, basic_mo_sal1 = ?, mo_contribution = ?, mo_contribution1 = ?, employee_share = ?, employee_share1 = ?, employer_share = ?, employer_share1 = ?,enabled = ?, user_id = ?, date_updated = ? WHERE phID = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE philhealth SET enabled = ? WHERE phID = ?";
		$this->db->query($sql, $data);
	}

}
