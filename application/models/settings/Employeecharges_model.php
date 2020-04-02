<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employeecharges_model extends CI_Model {

	public function getEmployee($start,$length,$search,$ordrBy) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM employee_charges WHERE enabled = 1 AND description LIKE '%".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}
			else{
			$sql = "SELECT * FROM employee_charges WHERE enabled = 1 ORDER BY ".$ordrBy." LIMIT ".$start.",".$length;
			}
		}else {
			$sql = "SELECT * FROM employee_charges WHERE enabled = 1 ";
		}
		return $this->db->query($sql);
	}

	public function getChargesByDesc($data) {
		$sql = "SELECT * FROM employee_charges WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}
	public function getChargesByID($data) {
		$sql = "SELECT * FROM employee_charges WHERE employee_charges_id = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO employee_charges(charge_status,amount,description,date_updated,date_created,user_id,enabled) VALUES ('waiting for approval',?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE employee_charges SET amount = ?, description = ?, date_updated = ? WHERE employee_charges_id = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE employee_charges SET enabled = ? WHERE employee_charges_id = ?";
		$this->db->query($sql, $data);
	}

}
