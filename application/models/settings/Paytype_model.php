<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paytype_model extends CI_Model {

	public function getPayType($start, $length,$search) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM paytype WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM paytype WHERE enabled = 1 ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM paytype WHERE enabled = 1 ";
		}


		return $this->db->query($sql);
	}

		public function getPayTypeByDesc($data) {
		$sql = "SELECT * FROM paytype WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO paytype(description,frequency,date_range,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE paytype SET description = ?, date_updated = ? WHERE paytypeid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE paytype SET enabled = ? WHERE paytypeid = ?";
		$this->db->query($sql, $data);
	}

}
