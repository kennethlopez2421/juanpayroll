<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Compensation_reports_model extends CI_Model {

  public function get_compensation_reports_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);


    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, b.*
      FROM employee_record a
      INNER JOIN hris_compensation_reports b ON a.employee_idno = b.employee_idno
      WHERE b.cutoff_from >= $from AND b.cutoff_to <= $to";
    // $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, b.*
    //   FROM employee_record a
    //   INNER JOIN hris_compensation_reports b ON a.employee_idno = b.employee_idno
    //   INNER JOIN hris_payroll_summary c ON b.payroll_ref_no = c.ref_no
    //   WHERE c.pay_day BETWEEN $from AND $to";

    ### sub filter ###
    switch ($search->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search->keyword);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search->keyword."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divRefNo':
        $ref_no = $this->db->escape($search->keyword);
        $sql .= " AND b.payroll_ref_no = $ref_no";
      default:
        break;
    }

    // $sql .= " ORDER BY cutoff_from ASC, fullname ASC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY b.cutoff_from ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $total = $row['sss'] + $row['philhealth'] + $row['pagibig'] + $row['tax'];
      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['payroll_ref_no'];
      $nestedData[] = $row['cutoff_from']."-".$row['cutoff_to'];
      $nestedData[] = '<span class="float-right">'.number_format($row['sss'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['philhealth'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['pagibig'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['tax'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($total,2).'</span>';

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function set_compensation_reports(){
    $sql = "SELECT
      (CASE WHEN a.currency != 'PHP' THEN a.sss * a.ex_rate ELSE a.sss END) as sss,
      (CASE WHEN a.currency != 'PHP' THEN a.philhealth * a.ex_rate ELSE a.philhealth END) as philhealth,
      (CASE WHEN a.currency != 'PHP' THEN a.pag_ibig * a.ex_rate ELSE a.pag_ibig END) as pag_ibig,
      f.ref_no, b.employee_idno, a.fromdate, a.todate
      FROM hris_deduction_log a
      INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
      INNER JOIN contract c ON b.id = c.contract_emp_id
      LEFT JOIN position d ON c.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      INNER JOIN hris_payroll_summary f ON f.deduction_id =  a.deductionsum_id
      WHERE a.status = 'approved' AND a.enabled = 1 AND b.enabled = 1 AND c.contract_status = 'active'
      AND f.ref_no NOT IN (SELECT payroll_ref_no FROM hris_compensation_reports)";

    $compensation = $this->db->query($sql)->result_array();

    $data = array();
    foreach($compensation as $comp){
      $nestedData = array(
        "employee_idno" => $comp['employee_idno'],
        "payroll_ref_no" => $comp['ref_no'],
        "sss" => $comp['sss'],
        "philhealth" => $comp['philhealth'],
        "pagibig" => $comp['pag_ibig'],
        "cutoff_from" => $comp['fromdate'],
        "cutoff_to" => $comp['todate']
      );

      $data[] = $nestedData;
    }

    $this->db->insert_batch('hris_compensation_reports',$data);
  }

  public function get_excel_reports($search){
    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);


    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, b.*
      FROM employee_record a
      INNER JOIN hris_compensation_reports b ON a.employee_idno = b.employee_idno
      WHERE b.cutoff_from >= $from AND b.cutoff_to <= $to";

    ### sub filter ###
    switch ($search->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search->keyword);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search->keyword."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divRefNo':
        $ref_no = $this->db->escape($search->keyword);
        $sql .= " AND b.payroll_ref_no = $ref_no";
      default:
        break;
    }

    $sql .= " ORDER BY cutoff_from ASC, fullname ASC";

    return $this->db->query($sql);
    // return $this->db->last_query($sql);

  }

}
