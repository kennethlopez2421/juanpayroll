<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_profile_model extends CI_Model {


	public function get_user_details($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT * FROM employee_record WHERE employee_idno = $empid
		AND isActive = 1 AND enabled = 1";

		return $this->db->query($sql);
	}
	public function get_worksite($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT ws.description FROM contract as c
		LEFT JOIN employee_record as er
		ON c.contract_emp_id = er.id
		LEFT JOIN worksite as ws
		ON ws.worksiteid = c.work_site_id
		WHERE er.employee_idno = $empid
		LIMIT 1";

		return $this->db->query($sql);
	}
	// public function get_position($empid){
	// 	$empid = $this->db->escape($empid);

	// 	$sql = "SELECT w.description as 'description' FROM contract as c
	//  			INNER JOIN  as worksite as w
	//  			ON w.worksiteid = c.work_site_id
	//  			INNER JOIN employee_record as er
	//  			ON c.contract_emp_id = er.id
	//  			WHERE er.employee_idno = $empid
	//  			AND c.contract_status = 'active'
	//  			AND c.enabled = 1";

	// 	return $this->db->query($sql);
	// }
	public function get_department($empid){
		$empid = $this->db->escape($empid);
		$sql = "SELECT d.description FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 LEFT JOIN position c ON b.position_id = c.positionid
		 LEFT JOIN department d ON c.deptId = d.departmentid
		 WHERE b.contract_status = 'active' AND b.enabled = 1 AND a.enabled = 1
		 AND c.enabled = 1 AND d.enabled = 1 AND a.employee_idno = $empid";

		return $this->db->query($sql);
	}
	// public function get_emp_worksite($empid){
	// 	$empid = $this->db->escape($empid);
	// 	$sql = "SELECT w.description as 'description' FROM contract as c
	// 			INNER JOIN  as worksite as w
	// 			ON w.worksiteid = c.work_site_id
	// 			INNER JOIN employee_record as er
	// 			ON c.contract_emp_id = er.id
	// 			WHERE er.employee_idno = $empid
	// 			AND c.contract_status = 'active'
	// 			AND c.enabled = 1";
	// 	return $this->db->query($sql);
	// }
	public function get_valid_ids_tbl($start,$length,$search,$ordrBy,$employee_idno) {
		$employee_idno = $this->db->escape($employee_idno);
		$sql = "SELECT * FROM valid_id_details WHERE employee_idno = $employee_idno AND enabled = 1";
		return $this->db->query($sql);
	}
	public function get_employee_ids($empid){
		$empid = $this->db->escape($empid);

		$sql = "SELECT * FROM valid_id_details WHERE employee_idno = $empid AND enabled = 1 ORDER BY upload_date ASC";

		return $this->db->query($sql);
	}
	public function insert_data($employee_idno,$valid_id_type,$id_number,$id_value,$current_date,$file_name){
		$valid_id_type = $this->db->escape($valid_id_type);
		$id_number = $this->db->escape($id_number);
		$id_value = $this->db->escape($id_value);
		$current_date = $this->db->escape($current_date);
		$file_name = $this->db->escape($file_name);

		// $sql = "INSERT INTO valid_id_details VALUES valid_id_type = $valid_id_type,
		// id_number = $id_number,
		// id_value = $id_value,
		// upload_date = $current_date,
		// picture_extension = $file_name";

		$sql = "INSERT INTO valid_id_details(employee_idno,valid_id_type,id_number,id_value,upload_date,picture_extension) VALUES ($employee_idno,$valid_id_type,$id_number,$id_value,$current_date,$file_name)";

		$this->db->query($sql);
	}
	public function edit_data_withpic($valid_id_id,$valid_id_type,$id_number,$id_value,$file_name){
		$valid_id_id = $this->db->escape($valid_id_id);
		$valid_id_type = $this->db->escape($valid_id_type);
		$id_number = $this->db->escape($id_number);
		$id_value = $this->db->escape($id_value);
		$file_name = $this->db->escape($file_name);

		$sql = "UPDATE valid_id_details SET valid_id_type = $valid_id_type, id_number = $id_number, id_value = $id_value, picture_extension = $file_name WHERE id = $valid_id_id AND enabled = 1";
		$this->db->query($sql);
	}
	public function insert_temp_emp_details($employee_idno, $first_name, $middle_name, $last_name, $gender, $marital_status, $contact_no, $email, $birthdate, $address1, $address2, $country, $date_updated){
		$employee_idno = $this->db->escape($employee_idno);
		$first_name = $this->db->escape($first_name);
		$middle_name = $this->db->escape($middle_name);
		$last_name = $this->db->escape($last_name);
		$gender = $this->db->escape($gender);
		$marital_status = $this->db->escape($marital_status);
		$contact_no = $this->db->escape($contact_no);
		$email = $this->db->escape($email);
		$birthdate = $this->db->escape($birthdate);
		$address1 = $this->db->escape($address1);
		$address2 = $this->db->escape($address2);
		$country = $this->db->escape($country);
		$date_updated = $this->db->escape($date_updated);

		$sql = "INSERT INTO employee_details_temp(employee_idno,first_name,middle_name,last_name,gender,marital_status,contact_number,email,birthday,home_address_1,home_address_2,country,date_created) VALUES ($employee_idno,$first_name,$middle_name,$last_name,$gender,$marital_status,$contact_no,$email,$birthdate,$address1,$address2,$country,$date_updated)";

		$this->db->query($sql);

	}

	public function edit_data($valid_id_id,$valid_id_type,$id_number,$id_value){
		$valid_id_id = $this->db->escape($valid_id_id);
		$valid_id_type = $this->db->escape($valid_id_type);
		$id_number = $this->db->escape($id_number);
		$id_value = $this->db->escape($id_value);

		$sql = "UPDATE valid_id_details SET valid_id_type = $valid_id_type, id_number = $id_number, id_value = $id_value WHERE id = $valid_id_id AND enabled = 1";
		$this->db->query($sql);
	}
	public function delete_data($valid_id_id){
		$valid_id_id = $this->db->escape($valid_id_id);
		$sql = "UPDATE valid_id_details SET enabled = 0 WHERE id = $valid_id_id";
		$this->db->query($sql);
	}


}
