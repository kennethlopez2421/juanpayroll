<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Holidaytype_model extends CI_Model {

	public function getHolidayType($start,$length,$search) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM holidaytype WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM holidaytype WHERE enabled = 1 ORDER BY description ASC LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM holidaytype WHERE enabled = 1 ";
		}

		return $this->db->query($sql);
	}


	public function getHolidayTypeByDesc($data) {
		$sql = "SELECT * FROM holidaytype WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO holidaytype(description,type,payratio,payratio2,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE holidaytype SET description = ?, type = ?, payratio = ?, payratio2 = ?, date_updated = ? WHERE holidaytypeid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE holidaytype SET enabled = ? WHERE holidaytypeid = ?";
		$this->db->query($sql, $data);
	}

}
