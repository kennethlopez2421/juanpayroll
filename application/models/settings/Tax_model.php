<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_model extends CI_Model {

	public function getTax($start,$length) {

		if($start != null && $length != null) {
			$sql = "SELECT * FROM tax WHERE enabled = 1 LIMIT ".$start.",".$length." ";
		}else {
			$sql = "SELECT * FROM tax WHERE enabled = 1 ";
		}
		return $this->db->query($sql);
	}

	public function getCityByDesc($data) {
		$sql = "SELECT * FROM city WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO tax(aibLowerLimit,aibUpperLimit,tr1LowerLimit,tr1ExcessLimit,tr2LowerLimit,tr2ExcessLimit,enabled) VALUES (?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE tax SET aibLowerLimit = ?, aibUpperLimit = ?, tr1LowerLimit = ?, tr1ExcessLimit = ?, tr2LowerLimit = ?, tr2ExcessLimit = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE tax SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

}
