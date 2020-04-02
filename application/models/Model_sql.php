<?php 
class Model_sql extends CI_Model {

	public function selectNow($table,$cols,$condition,$data){ 
		$sql = "SELECT ".$this->db->escape_str($cols)." FROM ".$this->db->escape_str($table)." WHERE ".$this->db->escape_str($condition)." = ?";
		$data = array($data);
		return $this->db->query($sql, $data);
	}

	public function editNow($table,$identifier,$identifierData,$columns,$newData) {
		$sql = "UPDATE ".$this->db->escape_str($table)." SET ".$this->db->escape_str($columns)." = '".$this->db->escape_str($newData)."' WHERE ".$this->db->escape_str($identifier)." = '".$this->db->escape_str($identifierData)."'";		
		return $this->db->query($sql);
	}	

}