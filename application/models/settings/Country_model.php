<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Country_model extends CI_Model {

	public function getCountry($start,$length,$search) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM country WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY description LIMIT ".$start.",".$length;
			}else{
				$sql = "SELECT * FROM country WHERE enabled = 1 ORDER BY description LIMIT ".$start.",".$length;
			}
		}else {
			$sql = "SELECT * FROM country WHERE enabled = 1 ";
		}
		return $this->db->query($sql);
	}

	public function getCountryByDesc($data) {
		$sql = "SELECT * FROM country WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO country(description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE country SET description = ?, date_updated = ? WHERE countryid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE country SET enabled = ? WHERE countryid = ?";
		$this->db->query($sql, $data);
	}

}
