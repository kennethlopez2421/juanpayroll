<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payoutmed_model extends CI_Model {

	public function getPayoutMed($start,$length,$search) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM payoutmedium WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM payoutmedium WHERE enabled = 1 ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM payoutmedium WHERE enabled = 1";
		}

		return $this->db->query($sql);
	}

	public function getPayoutMedByDesc($data) {
		$sql = "SELECT * FROM payoutmedium WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO payoutmedium(description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE payoutmedium SET description = ?, date_updated = ? WHERE payoutmediumid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE payoutmedium SET enabled = ? WHERE payoutmediumid = ?";
		$this->db->query($sql, $data);
	}

}
