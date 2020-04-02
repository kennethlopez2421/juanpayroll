<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Applicant_model extends CI_Model{

  public function get_appTable_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'app_ref_no',
			1 => 'Name'
		);


		$sql = "SELECT * FROM applicant_record WHERE app_enabled = 1";


		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
      $sql .= " AND (CONCAT(app_lname,', ',app_fname,' ',app_mname) LIKE '".$this->db->escape_like_str($search)."%'
      OR app_lname LIKE '".$this->db->escape_like_str($search)."%'
      OR app_fname LIKE '".$this->db->escape_like_str($search)."%')";
    }

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $totalData;

		$sql.=" ORDER BY app_lname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();

		foreach( $query->result_array() as $row )
		{
			$nestedData=array();
      $status = "";
      if($row['app_status'] == 'in_process'){
        $status = '<center><span class="badge badge-success badge-pill" style = "width:90px;">In Process</span></center>';
        $actions = '<a class="btn btn-primary btn_action mb-1 dropdown-item" data-id = "'.en_dec('en',$row['id']).'" data-action = "interview">Interview</a>
        <a class="btn btn-primary btn_action mb-1 dropdown-item" data-id = "'.en_dec('en',$row['id']).'" data-action = "job_offer">Job Offer</a>
        <a class="btn btn-primary btn_action mb-1 dropdown-item" data-id = "'.en_dec('en',$row['id']).'" data-action = "requirements">Requirements</a>';
      }

      if($row['app_status'] == 'interview'){
        $status = '<center><span class="badge badge-success badge-pill" style = "width:90px;">Interview</span></center>';
        $actions = '
        <a class="btn btn-primary btn_action mb-1 dropdown-item" data-id = "'.en_dec('en',$row['id']).'" data-action = "job_offer">Job Offer</a>
        <a class="btn btn-primary btn_action mb-1 dropdown-item" data-id = "'.en_dec('en',$row['id']).'" data-action = "requirements">Requirements</a>';
      }

      if($row['app_status'] == 'job_offer'){
        $status = '<center><span class="badge badge-success badge-pill" style = "width:90px;">Job Offer</span></center>';
        $actions = '
        <a class="btn btn-primary btn_action mb-1 dropdown-item" data-id = "'.en_dec('en',$row['id']).'" data-action = "requirements">Requirements</a>';
      }

      if($row['app_status'] == 'requirements'){
        $status = '<center><span class="badge badge-success badge-pill" style = "width:90px;">Requirements</span></center>';
        $actions = '';
      }

      if($row['app_status'] == 'hired'){
        $status = '<center><span class="badge badge-success badge-pill" style = "width:90px;">Hired</span></center>';
      }

			$nestedData[] = $row['app_ref_no'];
			$nestedData[] = $row['app_lname'].", ".$row['app_fname']." ".$row['app_mname'];
      $nestedData[] = $status;

			$nestedData[] =
      '
        <center>
			  <form action = "'.base_url('applicants/Applicant/edit/'.en_dec('en',$this->session->userdata('token_session'))).'" class = "d-inline" method = "post">
          <input type="hidden" name = "appId" value = "'.$row['id'].'"/>
          <input type="hidden" name = "appRefNo" value = "'.$row['app_ref_no'].'" />
          <button type = "submit" class="btn btn-primary" style = "width:90px;"><i class="fa fa-eye mr-1"></i>View</button>
        </form>
			  <button class="btn_del_app_modal btn btn-sm btn-danger" style = "width:90px;" data-deleteid = "'.$row['id'].'" data-name = "'.$row['app_lname'].', '.$row['app_fname'].' '.$row['app_mname'].'"><i class="fa fa-trash mr-2"></i>Delete</button>
        </center>
			';

			$data[] = $nestedData;
		}

		$json_data = array(

			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $data
		);
		return $json_data;
  }

  public function get_appToken($token){
    $sql = "SELECT * FROM applicant_form_link WHERE app_token = ?";
    $data = array($token);

    return $this->db->query($sql,$data);
  }

  public function get_job_offer_template($id = false){
    $sql = "SELECT * FROM hris_contract_template WHERE template_type = 'job_offer' AND enabled = 1";
    if($id){
      $id = $this->db->escape($id);
      $sql .= " AND id = $id";
    }
    return $this->db->query($sql);
  }

  public function set_appLink($data){
    $this->db->insert('applicant_form_link',$data);
  }

  public function update_app_status($data){
    $sql = "UPDATE applicant_form_link SET app_status = ? WHERE app_token = ?";
    $this->db->query($sql,$data);
  }

  public function update_link_date($token){
    $sql = "UPDATE applicant_form_link SET app_updated_at = ? WHERE app_token = ?";
    $data = array(todaytime(), $token);

    $this->db->query($sql,$data);
  }

  public function getApplicantById($data) {
		$sql = "SELECT * FROM applicant_record WHERE id = ? AND app_enabled = 1 ";
		return $this->db->query($sql,$data);
	}

	public function getApplicantByIdNo($data) {
		$sql = "SELECT * FROM applicant_record WHERE app_ref_no = ? AND app_enabled = 1 ";
		return $this->db->query($sql,$data);
	}

	public function getApplicantByFnameLname($data){
		$sql = "SELECT * FROM applicant_record WHERE app_fname = ? AND app_lname = ? AND app_enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function getApplicantByEmail($data){
		$sql = "SELECT * FROM applicant_record WHERE app_email = ? AND app_enabled = 1";
		return $this->db->query($sql,$data);
	}

  public function get_requirements($app_ref_no){
    $app_ref_no = $this->db->escape($app_ref_no);
    $sql = "SELECT * FROM hris_requirements WHERE enabled = 1 AND employee_idno = $app_ref_no";
    return $this->db->query($sql);
  }

  public function get_interview($app_ref_no){
    $app_ref_no = $this->db->escape($app_ref_no);
    $sql = "SELECT * FROM hris_applicant_interview WHERE app_ref_no = $app_ref_no AND enabled = 1";
    return $this->db->query($sql);
  }

  public function get_job_offer($app_ref_no){
    $app_ref_no = $this->db->escape($app_ref_no);
    $sql = "SELECT * FROM hris_applicant_job_offer WHERE app_ref_no = $app_ref_no AND enabled = 1";
    return $this->db->query($sql);
  }

  public function create($data) {
    $this->db->insert('applicant_record',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
		// $this->db->query($sql, $data);
	}

  public function create_employee($data){
    $this->db->insert('employee_record',$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function set_requirements($data){
    $this->db->insert_batch('hris_requirements',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function setEducation($data) {
		$sql = "INSERT INTO applicant_education(
      applicant_ref_no,
      year_from,
      year_to,
      school,
      course,
      level,
      enabled) VALUES(?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

  public function setWorkHistory($data) {
		$sql = "INSERT INTO applicant_workhistory(
      applicant_ref_no,
      year_from,
      year_to,
      stay,
      company_name,
      position,
      level,
      contact_no,
      responsibility,
      enabled) VALUES(?,?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

  public function setDependents($data) {
		$sql = "INSERT INTO applicant_dependents(
      applicant_ref_no,
      first_name,
      middle_name,
      last_name,
      birthday,
      relationship,
      contact_no,
      enabled) VALUES(?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

  public function set_interview($data){
    $this->db->insert('hris_applicant_interview',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_job_offer($data){
    $this->db->insert('hris_applicant_job_offer',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function getEducations($appRefNo) {
		$sql = "SELECT * FROM applicant_education WHERE applicant_ref_no = ? AND enabled = 1 ";
    $data = array($appRefNo);
		return $this->db->query($sql,$data);
	}

  public function  getWorkHistory($appRefNo) {
		$sql = "SELECT * FROM applicant_workhistory WHERE applicant_ref_no = ? AND enabled = 1 ";
    $data = array($appRefNo);
		return $this->db->query($sql,$data);
	}

  public function getDependents($appRefNo) {
		$sql = "SELECT * FROM applicant_dependents WHERE applicant_ref_no = ? AND enabled = 1 ";
    $data = array($appRefNo);
		return $this->db->query($sql,$data);
	}

  public function updateApplicantRecord($data) {
		$sql = "UPDATE applicant_record SET
    app_ref_no = ?,
    app_fname = ?,
    app_mname = ?,
    app_lname = ?,
    app_birthday = ?,
    app_gender = ?,
    app_marital_status = ?,
    app_home_add1 = ?,
    app_home_add2 = ?,
    app_city = ?,
    app_country = ?,
    app_contact_no = ?,
    app_email = ?,
    app_sss_no = ?,
    app_philhealth_no = ?,
    app_pagibig_no = ?,
    app_tin_no = ?,
    app_isActive = ?  WHERE id = ?";
		$this->db->query($sql, $data);
	}

  public function updateApplicantEducation($data) {
		$sql = "UPDATE applicant_education SET year_from = ?, year_to = ?, school = ?, course = ?, level = ?  WHERE id = ?";
		$this->db->query($sql, $data);
	}

  public function updateApplicantWorkHistory($data) {
		$sql = "UPDATE applicant_workhistory SET year_from = ?, year_to = ?, stay = ?, company_name = ?, position = ?, level = ?, contact_no = ?, responsibility = ?  WHERE id = ?";
		$this->db->query($sql, $data);
	}

  public function updateApplicantDependents($data) {
		$sql = "UPDATE applicant_dependents SET first_name = ?, middle_name = ?, last_name = ?, birthday = ?, relationship = ?, contact_no = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

  public function update_applicant_status($id,$action){
    $id = $this->db->escape($id);
    $action = $this->db->escape($action);
    $sql = "UPDATE applicant_record SET app_status = $action WHERE id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_applicant_status2($id,$action){
    $id = $this->db->escape($id);
    $action = $this->db->escape($action);
    $sql = "UPDATE applicant_record SET app_status = $action WHERE app_ref_no = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function destroy($data) {
		$sql = "UPDATE applicant_record SET app_enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

  public function destroyEducation($data) {
		$sql = "UPDATE applicant_education SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroyWorkHistory($data) {
		$sql = "UPDATE applicant_workhistory SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroyDependent($data) {
		$sql = "UPDATE applicant_dependents SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}
}
