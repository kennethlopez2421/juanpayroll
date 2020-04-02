<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deductions_model extends CI_Model {

	public function getDeductions($start,$length,$search,$ordrBy) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM deduction WHERE enabled = 1 AND description LIKE '%".$this->db->escape_like_str($search)."%' ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM deduction WHERE enabled = 1 ORDER BY ".$ordrBy." LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM deduction WHERE enabled = 1";
		}

		return $this->db->query($sql);
	}

	public function getDeductionsByDesc($data) {
		$sql = "SELECT * FROM deduction WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}
	public function getDeductionsByID($data) {
		$sql = "SELECT * FROM deduction WHERE deductionid = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}
	public function create($data) {
		$sql = "INSERT INTO deduction(description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE deduction SET description = ?, date_updated = ? WHERE deductionid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE deduction SET enabled = ? WHERE deductionid = ?";
		$this->db->query($sql, $data);
	}

}
