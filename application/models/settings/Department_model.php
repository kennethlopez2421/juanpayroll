<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model {

	public function getDepartment($start,$length,$search) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT a.*, b.type as dept_type FROM department a INNER JOIN department_type b ON a.department_type = b.id WHERE a.enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT a.*, b.type as dept_type FROM department a INNER JOIN department_type b ON a.department_type = b.id WHERE a.enabled = 1 ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT a.*, b.type as dept_type FROM department a INNER JOIN department_type b ON a.department_type = b.id WHERE a.enabled = 1 ";
		}


		return $this->db->query($sql);
	}

	public function getDepartmentByDesc($data) {
		$sql = "SELECT * FROM department WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO department(description,department_type,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE department SET description = ?, department_type = ?, date_updated = ? WHERE departmentid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE department SET enabled = ? WHERE departmentid = ?";
		$this->db->query($sql, $data);
	}

}
