<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Level_model extends CI_Model {

	// public function getLevel($start,$length,$search) {
	//
	// 	if($start != null && $length != null) {
	// 		if($search != null){
	// 			$sql = "SELECT * FROM level WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY hierarchy_level ASC LIMIT ".$start.",".$length." ";
	// 		}else{
	// 			$sql = "SELECT * FROM level WHERE enabled = 1 ORDER BY hierarchy_level ASC LIMIT ".$start.",".$length." ";
	// 		}
	// 	}else {
	// 		$sql = "SELECT * FROM level WHERE enabled = 1 ";
	// 	}
	//
	// 	return $this->db->query($sql);
	// }

	public function getLevel($start,$length,$search) {
		$pos_id = $this->db->escape($this->session->userdata('position_id'));
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM hris_position
					WHERE enabled = 1
					AND position LIKE '%".$this->db->escape_like_str($search)."%'
					AND hierarchy_lvl > (SELECT hierarchy_lvl FROM hris_position WHERE position_id = $pos_id AND enabled = 1)
					ORDER BY hierarchy_lvl ASC LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM hris_position
				 WHERE enabled = 1
				 AND hierarchy_lvl > (SELECT hierarchy_lvl FROM hris_position WHERE position_id = $pos_id AND enabled = 1)
				 ORDER BY hierarchy_lvl ASC LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM hris_position
			 WHERE enabled = 1
			 AND hierarchy_lvl > (SELECT hierarchy_lvl FROM hris_position WHERE position_id = $pos_id AND enabled = 1)";
		}

		return $this->db->query($sql);
	}


	// public function getLevelByDesc($data) {
	// 	$sql = "SELECT * FROM level WHERE description = ? AND enabled = 1";
	// 	return $this->db->query($sql,$data);
	// }

	public function getLevelByDesc($data) {
		$sql = "SELECT position FROM hris_position WHERE position = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function get_hierarchy_lvl($pos_id){
		$sql = "SELECT hierarchy_lvl FROM hris_position WHERE position_id = ? AND enabled = 1";
		$data = array($pos_id);
		return $this->db->query($sql,$data);
	}

	// public function create($data) {
	// 	$sql = "INSERT INTO level(description,hierarchy_level,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?,?)";
	// 	$this->db->query($sql, $data);
	// }

	public function create($data) {
		$this->db->insert('hris_position',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	// public function update($data) {
	// 	$sql = "UPDATE level SET description = ?, hierarchy_level = ?, date_updated = ? WHERE levelid = ?";
	// 	$this->db->query($sql, $data);
	// 	return ($this->db->affected_rows() > 0)? true: false;
	// }

	public function update($data){
		$sql = "UPDATE hris_position SET position = ?, hierarchy_lvl = ?, date_updated = ? WHERE position_id = ?";
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0);
	}

	public function destroy($data) {
		$sql = "UPDATE level SET enabled = ? WHERE levelid = ?";
		$this->db->query($sql, $data);
	}

}
