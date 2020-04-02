<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subdepartment_model extends CI_Model {

	public function getSubDept($start, $length,$search) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT sd.subdeptid as subdeptid, sd.description as description, d.departmentid as departmentid, d.description as department FROM subdept sd LEFT JOIN department d ON sd.departmentid = d.departmentid WHERE sd.enabled = 1 AND sd.description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY sd.description ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT sd.subdeptid as subdeptid, sd.description as description, d.departmentid as departmentid, d.description as department FROM subdept sd LEFT JOIN department d ON sd.departmentid = d.departmentid WHERE sd.enabled = 1 ORDER BY sd.description ASC LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT sd.subdeptid as subdeptid, sd.description as description, d.departmentid as departmentid, d.description as department FROM subdept sd LEFT JOIN department d ON sd.departmentid = d.departmentid WHERE sd.enabled = 1";
		}

		return $this->db->query($sql);
	}

	public function getSubDeptByDesc($data) {
		$sql = "SELECT * FROM subdept WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO subdept(description,departmentid,date_updated,date_created,user_id,enabled) VALUES(?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE subdept SET description = ?, departmentid = ? ,date_updated = ? WHERE subdeptid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE subdept SET enabled = ? WHERE subdeptid = ?";
		$this->db->query($sql, $data);
	}

}
