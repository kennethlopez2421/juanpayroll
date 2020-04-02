<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contract_audit_trail_report_model extends CI_Model {
  public function get_contract_audit_trail_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'audit_trail',
      1 => 'created_at'
    );


    $sql = "SELECT a.*, CONCAT(b.user_fname,' ',b.user_mname,' ',b.user_lname) as admin_name,
     @fullname := (SELECT CONCAT(em.last_name,', ',em.first_name,' ',em.middle_name) as fullname1 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as fullname,
     @fullname1 := (SELECT CONCAT(em.last_name,', ',em.first_name) as fullname2 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as fullname1,
     @lastname := (SELECT CONCAT(em.last_name) as fullname3 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as lastname,
     @firstname := (SELECT CONCAT(em.first_name) as fullname4 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as firstname
     FROM hris_contract_audit_trail a
     INNER JOIN hris_users b ON a.employee_idno = b.employee_idno
     WHERE a.enabled = 1";

    ### sub filter ###
    switch ($search->filter) {
      case 'divName':
        $emp_name = $this->db->escape($search->search.'%');
        $sql .= " HAVING (fullname LIKE $emp_name OR fullname1 LIKE $emp_name
          OR lastname LIKE $emp_name OR firstname LIKE $emp_name)";
        break;
      case 'divDate':
        $from = $this->db->escape($search->from);
        $to = $this->db->escape($search->to);
        $sql .= " AND DATE(a.created_at) BETWEEN $from AND $to";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    // return $this->db->last_query();

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $fields = "";
      switch ($row['fields']) {
        case 'contract_start':
          $fields = "contract start";
          break;
        case 'contract_end':
          $fields = "contract end";
          break;
        case 'work_sched':
          $fields = "work schedule";
          break;
        case 'empstatus':
          $fields = "employment status";
          break;
        case 'payoutmedium':
          $fields = "payout medium";
          break;
        case 'base_pay':
          $fields = "basic pay";
          break;
        case 'total_sal_converted':
          $fields = "total salary";
          break;
        default:
          $fields = $row['fields'];
          break;
      }

      $nestedData[] = $row['admin_name']." <u>change the ".$fields." of ".$row['fullname']."</u> from <u>".$row['audit_trail']."</u>";
      $nestedData[] = '<center>'.$row['created_at'].'</center>';

      $data[] = $nestedData;
    }

    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_excel_reports($search){
    $sql = "SELECT a.*, CONCAT(b.user_fname,' ',b.user_mname,' ',b.user_lname) as admin_name,
     @fullname := (SELECT CONCAT(em.last_name,', ',em.first_name,' ',em.middle_name) as fullname1 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as fullname,
     @fullname1 := (SELECT CONCAT(em.last_name,', ',em.first_name) as fullname2 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as fullname1,
     @lastname := (SELECT CONCAT(em.last_name) as fullname3 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as lastname,
     @firstname := (SELECT CONCAT(em.first_name) as fullname4 FROM employee_record em INNER JOIN contract con ON em.id = con.contract_emp_id WHERE con.id = a.contract_id) as firstname
     FROM hris_contract_audit_trail a
     INNER JOIN hris_users b ON a.employee_idno = b.employee_idno
     WHERE a.enabled = 1";

    ### sub filter ###
    switch ($search->filter) {
      case 'divName':
        $emp_name = $this->db->escape($search->search.'%');
        $sql .= " HAVING (fullname LIKE $emp_name OR fullname1 LIKE $emp_name
          OR lastname LIKE $emp_name OR firstname LIKE $emp_name)";
        break;
      case 'divDate':
        $from = $this->db->escape($search->from);
        $to = $this->db->escape($search->to);
        $sql .= " AND DATE(a.created_at) BETWEEN $from AND $to";
        break;
      default:
        break;
    }
    $sql .= " ORDER BY created_at ASC";
    return $this->db->query($sql);
  }
}
