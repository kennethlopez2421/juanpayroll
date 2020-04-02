<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee_payslip_model extends CI_Model {
  public function try_fetch($employee_idno){
    $employee_idno = $this->db->escape($employee_idno);
    $sql = "SELECT * FROM time_record_summary_trial WHERE employee_idno = $employee_idno
    ORDER BY id ASC LIMIT 1";
    return $this->db->query($sql);
  }
  public function fetch_latest_payslip($employee_idno){
    $employee_idno = $this->db->escape($employee_idno);
    //latest payslip
    $sql = "SELECT * FROM hris_payslip WHERE employee_idno = $employee_idno AND enabled = 1
    ORDER BY date_to DESC LIMIT 1";

    return $this->db->query($sql);
  }
  public function get_alldates_from(){
    $sql = "SELECT date_from FROM hris_payslip WHERE enabled = 1 GROUP BY date_from ORDER BY date_from DESC";

    return $this->db->query($sql);
  }
  public function get_alldates_to(){
    $sql = "SELECT date_to FROM hris_payslip WHERE enabled = 1 GROUP BY date_to ORDER BY date_to DESC";

    return $this->db->query($sql);
  }
  public function generate_payslip($date_from,$date_to,$employee_idno){
    $date_from = $this->db->escape($date_from);
    $date_to = $this->db->escape($date_to);
    $employee_idno = $this->db->escape($employee_idno);

    $sql = "SELECT * FROM hris_payslip
    WHERE employee_idno = $employee_idno
    AND date_from = $date_from
    AND date_to = $date_to
    AND enabled = 1";

    return $this->db->query($sql);
  }
//
}
?>