<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worktype_model extends CI_Model {

	public function getWorkType($start, $length,$search) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM worktype WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM worktype WHERE enabled = 1 ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM worktype WHERE enabled = 1";
		}

		return $this->db->query($sql);
	}

	public function getWorkTypeByDesc($data) {
		$sql = "SELECT * FROM worktype WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO worktype(description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE worktype SET description = ?, date_updated = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE worktype SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

}
