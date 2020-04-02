<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timerecordsummary_range_model extends CI_Model {

	public function gettrsrange($start,$length,$search,$ordrBy) {
		if($start != null && $length != null) {

			if($search != null){
				$sql = "SELECT * FROM time_record_summary_range WHERE status = 1 AND description LIKE '%".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM time_record_summary_range WHERE status = 1 ORDER BY ".$ordrBy." LIMIT ".$start.",".$length." ";
			}
		}
		else {
			$sql = "SELECT * FROM time_record_summary_range WHERE status = 1 ";
		}
		return $this->db->query($sql);
	}

	public function create($data) {
		$sql = "INSERT INTO time_record_summary_range (description,range_start,range_end,date_updated,current_date_used,user_id,status) VALUES (?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}
		// $data = array( $start_date, $end_date, $description, $dateUpdated, $id);
	public function update($data) {
		$sql = "UPDATE time_record_summary_range SET range_start = ?, range_end = ?, description = ?, date_updated = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}
	public function remove_active($setzero,$status){
		$sql = "UPDATE time_record_summary_range SET current_date_used = ".$setzero." WHERE status = ".$status."";
		$this->db->query($sql);
	}
			// $data = array($setone,$id,$status);
	public function set_active($data){
		$sql = "UPDATE time_record_summary_range SET current_date_used = ? WHERE id = ? AND status = ?";
		$this->db->query($sql, $data);
	}
	public function destroy($data) {
		$sql = "UPDATE time_record_summary_range SET status = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

}
