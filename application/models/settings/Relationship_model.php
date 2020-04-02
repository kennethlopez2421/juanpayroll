<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Relationship_model extends CI_Model {

	public function getRelationship($start,$length,$search) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM relationship WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM relationship WHERE enabled = 1 LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM relationship WHERE enabled = 1 ";
		}
		return $this->db->query($sql);
	}

		public function getRelationshipByDesc($data) {
		$sql = "SELECT * FROM relationship WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO relationship(description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE relationship SET description = ?, date_updated = ? WHERE relationshipid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE relationship SET enabled = ? WHERE relationshipid = ?";
		$this->db->query($sql, $data);
	}

}
