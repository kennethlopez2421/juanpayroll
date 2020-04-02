<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Position_model extends CI_Model {

	public function get_pos_access_lvl(){
		$sql = "SELECT position_id, position FROM hris_position WHERE enabled = 1 AND position_id > 2 ORDER BY hierarchy_lvl ASC, position ASC";
		return $this->db->query($sql);
	}

	public function getDept(){
		$sql = "SELECT * FROM department WHERE enabled = 1 ORDER BY description ASC";
		return $this->db->query($sql);
	}

	public function getSubDept($deptId){
		$sql = "SELECT * FROM subdept WHERE departmentid = ? AND enabled = 1";
		$data = array($deptId);
		return $this->db->query($sql, $data);
	}

	public function getPosition($start,$length,$search) {
		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT hp.pos_access_lvl, hp.positionid as position_id,hp.description as position_desc, d.description as department, sd.description as subdepartment FROM position as hp
				LEFT JOIN department as d
				ON d.departmentid = hp.deptId
				LEFT JOIN subdept as sd
				ON hp.subDeptId = sd.subdeptid
				WHERE hp.enabled = 1 AND hp.description LIKE '".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT hp.pos_access_lvl, hp.positionid as position_id,hp.description as position_desc, d.description as department, sd.description as subdepartment FROM position as hp
				LEFT JOIN department as d
				ON d.departmentid = hp.deptId
				LEFT JOIN subdept as sd
				ON hp.subDeptId = sd.subdeptid
				WHERE hp.enabled = 1 LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT hp.pos_access_lvl, hp.positionid as position_id,hp.description as position_desc, d.description as department, sd.description as subdepartment FROM position as hp
			LEFT JOIN department as d
			ON d.departmentid = hp.deptId
			LEFT JOIN subdept as sd
			ON hp.subDeptId = sd.subdeptid
			WHERE hp.enabled = 1";
		}
		return $this->db->query($sql);
	}

	public function getPosInfo($posId){
		$sql = "SELECT *, c.description as sub_desc FROM position a LEFT JOIN department b ON a.deptId = b.departmentid LEFT JOIN subdept c ON a.subDeptId = c.subdeptid WHERE a.enabled = 1 AND positionid = ?";
		$data = array($posId);

		return $this->db->query($sql, $data);
	}

	public function getPositionByDesc($desc, $dept = false, $subDept = false) {
		$desc = $this->db->escape($desc);
		$sql = "SELECT * FROM position WHERE description = $desc AND enabled = 1";
		if($dept){
			$dept = $this->db->escape($dept);
			$sql .= " AND deptId = $dept";
		}

		if($subDept){
			$subDept = $this->db->escape($subDept);
			$sql .= " AND subDeptId = $subDept";
		}
		$sql .= " ORDER BY description ASC";
		return $this->db->query($sql);
	}

	public function create($data) {
		$this->db->insert('position',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function update($data) {
		$sql = "UPDATE employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 INNER JOIN hris_users c ON a.employee_idno = c.employee_idno
		 INNER JOIN position d ON b.position_id = d.positionid
		 SET b.position_access_lvl = ?, d.department_access = ?, c.position_id = ?,
		 d.description = ?, d.deptId = ?, d.subDeptId = ?, d.pos_access_lvl = ?, d.date_updated = ?,
		 c.deptId = ?, c.subDeptId = ?
		 WHERE b.contract_status = 'active'
		 AND c.enabled = 1 AND b.enabled = 1 AND d.enabled = 1 AND b.position_id = ?";


		// $sql = "UPDATE position SET description = ?, deptId = ?, subDeptId = ?, pos_access_lvl = ?, date_updated = ?
		// WHERE positionid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE position SET enabled = ? WHERE positionid = ?";
		$this->db->query($sql, $data);
	}

}
