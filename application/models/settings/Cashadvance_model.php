<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashadvance_model extends CI_Model {
	public function getCashAdvance($start,$length,$search,$ordrBy) {
		if($start != null && $length != null) {

			if($search != null){
				$sql = "SELECT * FROM cash_advance WHERE enabled = 1 AND description LIKE '%".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM cash_advance WHERE enabled = 1 ORDER BY ".$ordrBy." LIMIT ".$start.",".$length." ";
			}
		}
		else {
			$sql = "SELECT * FROM cash_advance WHERE enabled = 1 ";
		}
		return $this->db->query($sql);
	}

	public function getCAByDesc($data) {
		$sql = "SELECT * FROM cash_advance WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}
	public function getCAByID($data) {
		$sql = "SELECT * FROM cash_advance WHERE caID = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO cash_advance (description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE cash_advance SET description = ?, date_updated = ? WHERE caID = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE cash_advance SET enabled = ? WHERE caID = ?";
		$this->db->query($sql, $data);
	}

}
