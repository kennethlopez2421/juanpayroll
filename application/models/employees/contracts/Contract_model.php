<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contract_model extends CI_Model{

  public function getDepartment_for_select(){
		$sql = "SELECT departmentid, description FROM department WHERE enabled = 1";
		return $this->db->query($sql);
	}

  public function getSubDeptBy_deptId($id){
		$sql = "SELECT *, a.description as sub_desc FROM subdept a LEFT JOIN department b ON a.departmentid = b.departmentid WHERE a.departmentid = ? AND a.enabled = 1";
		$data = array($id);

		return $this->db->query($sql,$data);
	}

  public function getPosBy_subId($id){
    $sql = "SELECT a.description, a.positionid FROM position a LEFT JOIN subdept b ON a.subDeptId = b.subdeptid WHERE a.subdeptid = ?";
    $data = array($id);

    return $this->db->query($sql,$data);
  }

  public function getSSS(){
    $sql = "SELECT id, salRange_From, salRange_to FROM sss WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function getPhilhealth(){
    $sql = "SELECT phID, basic_mo_sal, basic_mo_sal1 FROM philhealth WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function getPagIbig(){
    $sql = "SELECT id, monthly_compensation FROM pagibig WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function getTax(){
    $sql = "SELECT id, aibLowerLimit, aibUpperLimit FROM tax WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function getPayType(){
    $sql = "SELECT paytypeid, description, frequency FROM paytype WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function getEmployeeLvl(){
    $sql = "SELECT levelid, description FROM level WHERE enabled = 1 AND hierarchy_level >= 2 ORDER BY hierarchy_level ASC";
    return $this->db->query($sql);
  }

  public function getWorkSite(){
    $sql = "SELECT worksiteid, description FROM worksite WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function getPosition(){
    $sql = "SELECT position_id as pos_access_lvl, positionid as position_id,
            a.deptId, a.subDeptId,
            a.description as position, b.description
            FROM position a
            LEFT JOIN department b ON a.deptId = b.departmentid
            LEFT JOIN hris_position c ON a.pos_access_lvl = c.position_id
            WHERE a.enabled = 1 AND a.deptId > 0 ORDER BY b.description ASC, a.description ASC";
    return $this->db->query($sql);
  }

  public function getEmpLevel(){
    $sql = "SELECT levelid, description FROM level WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function getSalCat(){
    $sql = "SELECT salarycatid, description FROM salarycat WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function getEmployee($id){
    $sql = "SELECT * FROM employee_record WHERE id = ? AND enabled = 1";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function get_emp_status(){
    $sql = "SELECT * FROM empstatus WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function get_pay_medium(){
    $sql = "SELECT * FROM payoutmedium WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function get_emp_leave(){
    $sql = "SELECT * FROM leaves WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function getContract($id){
    $sql = "SELECT a.*, b.*, b.id as contract_id, c.work_sched, c.sched_type, c.sched_type2,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.positionid as posID, f.levelid as levelID, g.id as sssID,
            h.phID as phID, i.id as pagibigID, j.id as taxID, k.paytypeid as paytypeID,
            m.username, m.password, n.description, p.exchange_rate as ex_rate
            FROM employee_record a
            LEFT JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN work_schedule c ON b.work_sched_id = c.id
            LEFT JOIN worksite d ON b.work_site_id = d.worksiteid
            LEFT JOIN position e ON b.position_id = e.positionid
            LEFT JOIN level f ON b.emp_lvl = f.levelid
            LEFT JOIN sss g ON b.sss = g.id
            LEFT JOIN philhealth h ON b.philhealth = h.phID
            LEFT JOIN pagibig i ON b.pagibig = i.id
            LEFT JOIN tax j ON b.tax = j.id
            LEFT JOIN paytype k ON b.paytype = k.paytypeid
            LEFT JOIN hris_users m ON m.employee_idno = a.employee_idno
            LEFT JOIN empstatus n ON n.empstatusid = b.emp_status
            LEFT JOIN payoutmedium o ON o.payoutmediumid = b.payout_medium
            LEFT JOIN hris_exchange_rates p ON b.currency = p.currency_code
            WHERE a.enabled = 1 AND b.contract_status = 'active' AND a.id = ? AND m.enabled = 1 AND p.enabled = 1";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function getPrevContract($id){
    $sql = "SELECT b.id, b.updated_at
      FROM employee_record a
      LEFT JOIN contract b ON a.id = b.contract_emp_id
      WHERE b.contract_status = 'inactive' AND a.id = ?";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function getPrevContractFull($cid){
    $sql = "SELECT a.*, b.*, c.work_sched, c.sched_type, c.sched_type2,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.positionid as posID, f.levelid as levelID, CONCAT(g.salRange_From,'-',g.salRange_to) as sss,
            d.description as workSite, e.description as position, f.description as emplvl,
            CONCAT(h.basic_mo_sal,'-',h.basic_mo_sal1) as philhealth,
            i.monthly_compensation as pagibig, CONCAT(j.aibLowerLimit,'-',j.aibUpperLimit) as tax,
            k.description as paytype, l.description as empstatus, m.description as p_medium
            FROM employee_record a
            LEFT JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN work_schedule c ON a.id = c.emp_id
            LEFT JOIN worksite d ON b.work_site_id = d.worksiteid
            LEFT JOIN position e ON b.position_id = e.positionid
            LEFT JOIN level f ON b.emp_lvl = f.levelid
            LEFT JOIN sss g ON b.sss = g.id
            LEFT JOIN philhealth h ON b.philhealth = h.phID
            LEFT JOIN pagibig i ON b.pagibig = i.id
            LEFT JOIN tax j ON b.tax = j.id
            LEFT JOIN paytype k ON b.paytype = k.paytypeid
            LEFT JOIN empstatus l ON b.emp_status = l.empstatusid
            LEFT JOIN payoutmedium m ON b.payout_medium = m.payoutmediumid
            WHERE a.enabled = 1 AND b.contract_status = 'inactive' AND b.id = ?";
    $data = array($cid);
    return $this->db->query($sql,$data);
  }

  public function getContractByEmail($email){
    $sql = "SELECT username FROM hris_users WHERE username = ? AND enabled = 1";
    $data = array($email);
    return $this->db->query($sql,$data);
  }

  public function getContractRefNo($ref_no){
    $sql = "SELECT contract_ref_no FROM contract WHERE contract_ref_no = ?";
    $data = array($ref_no);
    return $this->db->query($sql,$data);
  }

  public function getContractByEmpID($emp_id){
    // $sql = "SELECT id FROM contract WHERE contract_emp_id = ? AND contract_status = 'active'";
    $sql = "SELECT a.id, c.username, c.password, b.email,
      a.contract_ref_no, d.description as worksite, e.description as position, f.company, a.contract_start,
      a.contract_end, g.work_sched, h.description as empstatus, i.description as payoutmedium,
      j.description as paytype, a.base_pay, a.total_sal, a.total_sal_converted, a.currency
      FROM contract a
      INNER JOIN employee_record b ON a.contract_emp_id = b.id
      LEFT JOIN hris_users c ON b.employee_idno = c.employee_idno
      LEFT JOIN worksite d ON a.work_site_id = d.worksiteid
      LEFT JOIN position e ON a.position_id = e.positionid
      LEFT JOIN hris_companies f ON a.company_id = f.id
      LEFT JOIN work_schedule g ON a.work_sched_id = g.id
      LEFT JOIN empstatus h ON a.emp_status = h.empstatusid
      LEFT JOIN payoutmedium i ON a.payout_medium = i.payoutmediumid
      LEFT JOIN paytype j ON a.paytype = j.paytypeid
      WHERE a.contract_emp_id = ? AND a.contract_status = 'active' AND c.enabled = 1";
    $data = array($emp_id);
    return $this->db->query($sql,$data);
  }

  public function getPositionSchedule($pos_id){
    $sql = "SELECT * FROM work_schedule WHERE pos_id = ? AND sched_type2 = 'default' AND enabled = 1";
    $data = array($pos_id);
    return $this->db->query($sql,$data);
  }

  public function get_worksched_settigns(){
    $sql = "SELECT * FROM hris_worksched_settings WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function get_contract_files($contract_id){
    $contract_id = $this->db->escape($contract_id);
    $sql = "SELECT a.*, c.template_name FROM hris_contract_files a
     INNER JOIN contract b ON a.contract_id = b.id
     INNER JOIN hris_contract_template c ON a.template_id = c.id
     WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1
     AND b.id = $contract_id ORDER BY c.template_name ASC";
    return $this->db->query($sql);
  }

  public function get_contract_file_by_template_id($id,$cid){
    $id = $this->db->escape($id);
    $cid = $this->db->escape($cid);
    $sql = "SELECT * FROM hris_contract_files WHERE template_id = $id AND contract_id = $cid AND enabled = 1";
    return $this->db->query($sql);
  }

  public function get_contract_for_template($contract_id){
    $contract_id = $this->db->escape($contract_id);
    $sql = "SELECT CONCAT(a.first_name,', ',a.middle_name,' ',a.last_name) as fullname, a.*, b.*,
     d.description as work_site, e.description as position, f.company as company, g.work_sched,
     g.total_whours, (g.total_whours - g.total_bhours) as total_whours2, g.total_bhours, g.sched_type, h.description as emp_status,
     i.description as payout_medium, j.description as paytype,
     @hr_manager := (SELECT CONCAT(hu.user_fname,' ',hu.user_mname,' ',hu.user_lname) as fullname FROM hris_users hu INNER JOIN hris_position hp ON hu.position_id = hp.position_id WHERE hp.hierarchy_lvl = 2 AND hu.enabled = 1 AND hp.enabled = 1 LIMIT 1) as hr_manager,
     @manager_name := (SELECT CONCAT(hu.user_fname,' ',hu.user_mname,' ',hu.user_lname) as fullname FROM hris_users hu INNER JOIN hris_position hp ON hu.position_id = hp.position_id WHERE hp.hierarchy_lvl = 4 AND hu.deptId = e.deptId AND hp.enabled = 1 AND hu.enabled = 1 LIMIT 1) as manager_name,
     @manager_id := (SELECT hu.employee_idno FROM hris_users hu INNER JOIN hris_position hp ON hu.position_id = hp.position_id WHERE hp.hierarchy_lvl = 4 AND hu.deptId = e.deptId AND hp.enabled = 1 AND hu.enabled = 1 LIMIT 1) as manager_id,
     @manager_positon := (SELECT c.description as position FROM employee_record a INNER JOIN contract b ON a.id = b.contract_emp_id INNER JOIN position c ON b.position_id = c.positionid WHERE a.employee_idno = @manager_id AND a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 LIMIT 1) as manager_position
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     LEFT JOIN worksite d ON b.work_site_id = d.worksiteid
     LEFT JOIN position e ON b.position_id = e.positionid
     LEFT JOIN hris_companies f ON b.company_id = f.id
     LEFT JOIN work_schedule g ON b.work_sched_id = g.id
     LEFT JOIN empstatus h ON b.emp_status = h.empstatusid
     LEFT JOIN payoutmedium i ON b.payout_medium = i.payoutmediumid
     LEFT JOIN paytype j ON b.paytype = j.paytypeid
     LEFT JOIN hris_position k ON b.position_access_lvl = k.position_id
     WHERE a.enabled = 1 AND b.enabled = 1 AND d.enabled = 1 AND e.enabled = 1 AND f.enabled = 1
     AND j.enabled = 1 AND k.enabled = 1
     AND g.enabled = 1 AND h.enabled = 1 AND i.enabled = 1 AND b.id = $contract_id";

    return $this->db->query($sql);
  }

  public function setWorkSchedule($data){
    $this->db->insert("work_schedule", $data);
    return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : false;
  }

  public function setContract($data){
    $this->db->insert("contract", $data);
    return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : false;
  }

  public function set_template_batch($data){
    $this->db->insert_batch('hris_contract_files',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_user_credentials($data){
    $this->db->insert('hris_users', $data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function set_audit_trail_batch($data){
    $this->db->insert_batch('hris_contract_audit_trail',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_hris_user_pos($pos_id,$deptId,$subDeptId,$id){
    $sql = "UPDATE hris_users a INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
      SET a.position_id = ?, a.deptId = ?, a.subDeptId = ? WHERE a.employee_idno = b.employee_idno AND b.id = ?";
    $data = array($pos_id,$deptId, $subDeptId,$id);
    $this->db->query($sql,$data);
  }

  public function updateEmployeeCredentials($update){
    $sql = "UPDATE hris_users SET username = ?, password = ? WHERE id = ?";
    $this->db->query($sql,$update);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function updateEmployeeStatus($data){
    $sql = "UPDATE employee_record SET isActive = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0)? true : false;
  }

  public function updatePreviousContract($id,$update = false){
    if($update == false){
      $sql = "UPDATE contract SET contract_status = 'inactive', updated_at = ? WHERE id = ? AND contract_status = 'active'";
      $data = array(todaytime(),$id);
    }else{
      $sql = "UPDATE contract SET updated_at = ? WHERE id = ?";
      $data = array(todaytime(),$id);
    }
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_all_prevContract($empid){
    $empid = $this->db->escape($empid);
    $sql = "UPDATE employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     SET b.contract_status = 'inactive'
     WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'
     AND a.id = $empid";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_contract_file_status($id,$cid,$status = 0){
    $status = $this->db->escape($status);
    $id = $this->db->escape($id);
    $cid = $this->db->escape($cid);
    $sql = "UPDATE hris_contract_files SET enabled = $status WHERE template_id = $id AND contract_id = $cid
     AND enabled = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

}
