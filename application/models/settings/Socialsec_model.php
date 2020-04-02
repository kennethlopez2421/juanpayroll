<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Socialsec_model extends CI_Model {

	public function getSSS($start,$length) {

		if($start != null && $length != null) {
			$sql = "SELECT *, (ss_ee + ss_er) as ofw FROM sss  WHERE enabled = 1 LIMIT ".$start.",".$length." " ;
		}else {
			$sql = "SELECT * FROM sss ";
		}
		return $this->db->query($sql);
	}

	public function getssID($data) {
		$sql = "SELECT * FROM sss WHERE id = ?";
		$data = array($data);
		return $this->db->query($sql,$data);

	}

	public function create($data) {
		$sql = "INSERT
		INTO sss (salRange_from,salRange_to,monthly_sal_cred,ss_er,ss_ee,ss_total,ec_er,tc_er,tc_ee,tc_total,SV_VM_OFW, enabled)
		VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE sss SET salRange_from = ?, salRange_to = ?, monthly_sal_cred = ?, ss_er = ?, ss_ee = ?, ss_total = ?, ec_er = ?, tc_er = ?, tc_ee = ?, tc_total = ?, SV_VM_OFW = ?, enabled = 1 WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE sss SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

}
