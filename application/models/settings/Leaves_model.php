<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaves_model extends CI_Model {

	public function getLeaves($start,$length,$search) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM leaves WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM leaves WHERE enabled = 1 LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM leaves WHERE enabled = 1 ";
		}
		return $this->db->query($sql);
	}

	public function getLeavesByDesc($data,$self = false) {
		$sql = "SELECT * FROM leaves WHERE description = ? AND enabled = 1";
		if($self){
			$self = $this->db->escape($self);
			$sql .= " AND leaveid != $self";
		}
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO leaves(description,days_before_filling,late_filling,consecutive_filling,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE leaves SET description = ?, days_before_filling = ?, late_filling = ?, consecutive_filling = ?, date_updated = ? WHERE leaveid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE leaves SET enabled = ? WHERE leaveid = ?";
		$this->db->query($sql, $data);
	}

}
