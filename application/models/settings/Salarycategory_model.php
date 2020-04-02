<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salarycategory_model extends CI_Model {

	public function getSalCat($start,$length,$search) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM salarycat WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM salarycat WHERE enabled = 1 LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM salarycat WHERE enabled = 1 ";
		}


		return $this->db->query($sql);
	}

	public function getSalCatByDesc($data) {
		$sql = "SELECT * FROM salarycat WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO salarycat (description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE salarycat SET description = ?, date_updated = ? WHERE salarycatid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE salarycat SET enabled = ? WHERE salarycatid = ?";
		$this->db->query($sql, $data);
	}

}
