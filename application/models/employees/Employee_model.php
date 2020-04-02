<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model {

	public function getEmployee($start,$length,$search) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT *, CONCAT(last_name,', ',first_name) AS name FROM employee_record WHERE enabled = 1
				AND (CONCAT(last_name,', ',first_name) LIKE '".$this->db->escape_like_str($search)."%'
				OR last_name LIKE '".$this->db->escape_like_str($search)."%'
				OR first_name LIKE '".$this->db->escape_like_str($search)."%') ORDER BY last_name LIMIT ".$start.",".$length;
			}else{
				$sql = "SELECT *, CONCAT(last_name,', ',first_name) AS name FROM employee_record WHERE enabled = 1 ORDER BY last_name LIMIT ".$start.",".$length;
			}
		}else {
			$sql = "SELECT * FROM employee_record WHERE enabled = 1 ";
		}

		return $this->db->query($sql);
	}

	public function getEmployeeById($data) {
		$sql = "SELECT * FROM employee_record WHERE id = ? AND enabled = 1 ";
		return $this->db->query($sql,$data);
	}

	public function getEmployeeByIdNo($data) {
		$sql = "SELECT * FROM employee_record WHERE employee_idno = ? AND enabled = 1 ";
		return $this->db->query($sql,$data);
	}

	public function getEmployeeByFnameLname($data){
		$sql = "SELECT * FROM employee_record WHERE first_name = ? AND last_name = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function getEmployeeByEmail($data){
		$sql = "SELECT * FROM employee_record WHERE email = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function get_educ_level(){
		$sql = "SELECT * FROM educlevel WHERE enabled = 1 ORDER BY description ASC";
		return $this->db->query($sql);
	}

	public function get_relation(){
		$sql = "SELECT * FROM relationship WHERE enabled = 1 ORDER BY description ASC";
		return $this->db->query($sql);
	}

	public function create($data) {

		$sql = "INSERT INTO employee_record(
			employee_idno,
			first_name,
			middle_name,
			last_name,
			birthday,
			gender,
			marital_status,
			home_address1,
			home_address2,
			contact_no,
			email,
			sss_no,
			philhealth_no,
			pagibig_no,
			tin_no,
			isActive,
			enabled) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function setEducation($data) {

		$sql = "INSERT INTO employee_education(employee_idno,year_from,year_to,school,course,level,enabled) VALUES(?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function getEducations($data) {
		$sql = "SELECT * FROM employee_education WHERE employee_idno = ? AND enabled = 1 ";
		return $this->db->query($sql,$data);
	}

	public function setWorkHistory($data) {

		$sql = "INSERT INTO employee_workhistory(employee_idno,year_from,year_to,stay,company_name,position,level,contact_no,responsibility,enabled) VALUES(?,?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function  getWorkHistory($data) {
		$sql = "SELECT * FROM employee_workhistory WHERE employee_idno = ? AND enabled = 1 ";
		return $this->db->query($sql,$data);
	}

	public function setDependents($data) {

		$sql = "INSERT INTO employee_dependents(employee_idno,first_name,middle_name,last_name,birthday,relationship,contact_no,enabled) VALUES(?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function set_hris_employment_history($data){
		$this->db->insert('hris_employment_history',$data);
		return ($this->db->affected_rows() > 0) ? true: false;
	}

	public function getDependents($data) {
		$sql = "SELECT * FROM employee_dependents WHERE employee_idno = ? AND enabled = 1 ";
		return $this->db->query($sql,$data);
	}

	public function updateEmployeeRecord($data) {
		$sql = "UPDATE employee_record
			SET employee_idno = ?, first_name = ?,
				middle_name = ?, last_name = ?,
				birthday = ?, gender = ?,
				marital_status = ?, home_address1 = ?,
				home_address2 = ?, city = ?,
				country = ?, contact_no = ?,
				email = ?, sss_no = ?,
				philhealth_no = ?, pagibig_no = ?,
				tin_no = ?
				WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function updateEmployeeEducation($data) {
		$sql = "UPDATE employee_education SET year_from = ?, year_to = ?, school = ?, course = ?, level = ?  WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function updateEmployeeWorkHistory($data) {
		$sql = "UPDATE employee_workhistory SET year_from = ?, year_to = ?, stay = ?, company_name = ?, position = ?, level = ?, contact_no = ?, responsibility = ?  WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function updateEmployeeDependents($data) {
		$sql = "UPDATE employee_dependents SET first_name = ?, middle_name = ?, last_name = ?, birthday = ?, relationship = ?, contact_no = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE employee_record a
			LEFT JOIN hris_users b ON a.employee_idno = b.employee_idno
			SET a.enabled = ?, b.enabled = 0
			WHERE a.id = ?";
		$this->db->query($sql, $data);
		$deleted  = ($this->db->affected_rows() > 0) ? true: false;
		if($deleted == true){
			$emp_id = $this->db->escape($data[1]);
			$sql = "UPDATE contract
							SET contract_status = 'inactive'
							WHERE contract_emp_id = $emp_id AND contract_status = 'active'";
			$this->db->query($sql);
		}

		return $deleted;
	}

	public function destroyEducation($data) {
		$sql = "UPDATE employee_education SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroyWorkHistory($data) {
		$sql = "UPDATE employee_workhistory SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroyDependent($data) {
		$sql = "UPDATE employee_dependents SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}
}
