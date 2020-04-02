<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sss_reports_model extends CI_Model {
  public function get_sss_reports_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'sss_no',
      1 => 'employee_idno',
      2 => 'fullname',
      3 => 'dept',
      4 => 'EE',
      5 => 'ER',
      6 => 'EC',
      7 => 'total'
    );

    $month_year = $this->db->escape($search->month);
    $check_sql = "SELECT month FROM hris_sss_reports WHERE month = $month_year AND enabled = 1";
    $check_query = $this->db->query($check_sql);
    if($check_query->num_rows() > 0){
      $sql = "SELECT a.sss_no, a.EE, a.EC, a.ER, a.employee_idno, a.total, i.description as dept, a.company_name,
        CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname, i.departmentid as dept_id
        FROM hris_sss_reports a
        INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
        LEFT JOIN department i ON i.departmentid = a.department
        LEFT JOIN hris_companies j ON a.company_id = j.id
        WHERE month = $month_year AND a.enabled = 1 AND c.enabled = 1";
    }else{
      $sql = "SELECT @EE := SUM(a.sss) as EE, CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname,
      c.sss_no, c.employee_idno, i.description as dept, @ER := e.ss_er as ER, j.id as company_id, j.company as company_name,
      @EC := e.ec_er as EC, (SUM(a.sss) + e.ss_er + e.ec_er) as total, i.departmentid as dept_id, a.sss as sSSS,
      (SELECT GROUP_CONCAT(ref_no SEPARATOR ',') FROM hris_payroll_summary WHERE DATE_FORMAT(b.pay_day, '%b-%Y') = $month_year AND b.status = 'approved') as payroll_ref_no,
      d.currency, k.exchange_rate as ex_rate
      FROM hris_compensation_reports a
      INNER JOIN hris_payroll_summary b ON a.payroll_ref_no = b.ref_no
      INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
      INNER JOIN contract d ON c.id = d.contract_emp_id
      LEFT JOIN sss e ON e.id = d.sss
      LEFT JOIN position h ON h.positionid = d.position_id
      LEFT JOIN department i ON h.deptId = i.departmentid
      LEFT JOIN hris_companies j ON d.company_id = j.id
      LEFT JOIN hris_exchange_rates k ON d.currency = k.currency_code
      WHERE DATE_FORMAT(b.pay_day, '%b-%Y') = $month_year AND b.status = 'approved'
      AND c.enabled = 1 AND b.enabled = 1 AND d.contract_status = 'active'";

      $sql_all = $sql;
      $sql_all .= " GROUP BY a.employee_idno ORDER BY fullname ASC";
      $query = $this->db->query($sql);
      $query_all = $this->db->query($sql_all);
      $insert_arr = array();
      // return $month_year;

    }

    switch ($search->filter_by) {
      case 'divEmpID':
        $emp_id = $this->db->escape($search->keyword);
        $sql .= " AND c.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search->keyword."%");
        $sql .= " AND (CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) LIKE $emp_name
                  OR CONCAT(c.last_name,', ',c.first_name) LIKE $emp_name
                  OR c.last_name LIKE $emp_name OR c.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $dept_id = $this->db->escape($search->keyword);
        $sql .= " AND i.departmentid = $dept_id";
        break;
      case 'divCompany':
        $company_id = $this->db->escape($search->keyword);
        $sql .= " AND j.id = $company_id";
        break;
      default:
        // code...
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" GROUP BY c.employee_idno ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    $data = array();
    ### INSERT DATA ###
    if(!$check_query->num_rows() > 0){ // INSERT
      if($query_all->num_rows() > 0){
        foreach($query_all->result_array() as $row){
          $insert_data = array(
            "sss_no" => $row['sss_no'],
            "month" => $search->month,
            "employee_idno" => $row['employee_idno'],
            "employee_name" => $row['fullname'],
            "company_id" => $row['company_id'],
            "company_name" => $row['company_name'],
            "department" => $row['dept_id'],
            "department_name" => $row['dept'],
            "payroll_ref_no" => $row['payroll_ref_no'],
            "EE" => $row['EE'],
            "ER" => $row['ER'],
            "EC" => $row['EC'],
            "total" => $row['total']
          );

          $insert_arr[] = $insert_data;
        }

        $this->set_sss_reports_batch($insert_arr);

        $data_inserted = array();
        foreach( $insert_arr as $row )
        {
          $nestedData=array();
          // $sss_no = ($row['sss_no'] != "") ? $row['sss_no'] : "";
          $nestedData[] = ($row['sss_no'] != "") ? $row['sss_no'] : '<center>------</center>';
          $nestedData[] = $row['employee_idno'];
          $nestedData[] = $row['employee_name'];
          $nestedData[] = $search->month;
          $nestedData[] = $row['company_name'];
          $nestedData[] = $row['department_name'];
          $nestedData[] =  '<span class="text-right">'.number_format($row['EE'],2).'</span>';
          $nestedData[] =  '<span class="text-right">'.number_format($row['ER'],2).'</span>';
          $nestedData[] =  '<span class="text-right">'.number_format($row['EC'],2).'</span>';
          $nestedData[] =  '<span class="text-right">'.number_format($row['total'],2).'</span>';

          $data_inserted[] = $nestedData;
        }

        $length = $requestData['start'] + $requestData['length'];
        for ($i=$requestData['start']; $i < $length; $i++) {
          if($i < count((array)$data_inserted)){
            $data[] = $data_inserted[$i];
          }else{
            break;
          }
        }

        $totalData = count((array)$data_inserted);
        $totalFiltered = $totalData;
      }
    }else{ // SELECT

      foreach( $query->result_array() as $row )
      {
        $nestedData=array();
        // $sss_no = ($row['sss_no'] != "") ? $row['sss_no'] : "";
        $nestedData[] = ($row['sss_no'] != "") ? $row['sss_no'] : '<center>------</center>';
        $nestedData[] = $row['employee_idno'];
        $nestedData[] = $row['fullname'];
        $nestedData[] = $search->month;
        $nestedData[] = $row['company_name'];
        $nestedData[] = $row['dept'];
        $nestedData[] =  '<span class="text-right">'.number_format($row['EE'],2).'</span>';
        $nestedData[] =  '<span class="text-right">'.number_format($row['ER'],2).'</span>';
        $nestedData[] =  '<span class="text-right">'.number_format($row['EC'],2).'</span>';
        $nestedData[] =  '<span class="text-right">'.number_format($row['total'],2).'</span>';

        $data[] = $nestedData;
      }
    }


    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_emp_name($id){
    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     FROM employee_record a WHERE a.enabled = 1 AND a.employee_idno = ?";
    $data = array($id);
    return $this->db->query($sql,$data)->row()->fullname;
  }

  public function get_dept_name($deptId){
    $sql = "SELECT description as dept_name FROM department
     WHERE departmentid = ?";
    $data = array($deptId);
    return $this->db->query($sql,$data)->row()->dept_name;
  }

  public function check_export_to_excel_sss_reports($search){
    $month_year = $this->db->escape($search->month);
    $sql = "SELECT a.sss_no, a.EE, a.EC, a.ER, a.employee_idno, a.total, i.description as dept,
      a.company_id, a.company_name,
      CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname, i.departmentid as dept_id
      FROM hris_sss_reports a
      INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
      LEFT JOIN department i ON i.departmentid = a.department
      LEFT JOIN hris_companies j ON a.company_id = j.id
      WHERE month = $month_year AND a.enabled = 1 AND c.enabled = 1";

    switch ($search->filter_by) {
      case 'divEmpID':
        $emp_id = $this->db->escape($search->keyword);
        $sql .= " AND c.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search->keyword."%");
        $sql .= " AND (CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) LIKE $emp_name
                  OR CONCAT(c.last_name,', ',c.first_name) LIKE $emp_name
                  OR c.last_name LIKE $emp_name OR c.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $dept_id = $this->db->escape($search->keyword);
        $sql .= " AND i.departmentid = $dept_id";
        break;
      case 'divCompany':
        $company_id = $this->db->escape($search->keyword);
        $sql .= " AND j.id = $company_id";
        break;
      default:
        // code...
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC";

    return $this->db->query($sql);
  }

  public function export_to_excel_sss_reports($month){
    $month_year = $this->db->escape($month);
    $sql = "SELECT a.sss_no, a.EE, a.EC, a.ER, a.employee_idno, a.total, i.description as dept,
      CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname, i.departmentid as dept_id,
      a.company_name
      FROM hris_sss_reports a
      INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
      LEFT JOIN department i ON i.departmentid = a.department
      LEFT JOIN hris_companies j ON a.company_id = j.id
      WHERE month = $month_year AND a.enabled = 1 AND c.enabled = 1";

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC";

    return $this->db->query($sql);
  }

  public function set_sss_reports_batch($data){
    $this->db->insert_batch('hris_sss_reports', $data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
