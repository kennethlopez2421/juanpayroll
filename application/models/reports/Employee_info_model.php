<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee_info_model extends CI_Model {
  public function get_employee_info_json($search,$status){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $employment_status = $this->db->escape($status->employment_status);
    $emp_status = $this->db->escape($status->emp_status);
    $con_status = $this->db->escape($status->con_status);

    $sql = "SELECT a.*, b.*, f.*,
     a.employee_idno as emp_idno, b.id as contract_id,
     a.enabled as emp_status, b.enabled as contract_enabled, f.enabled as worksched_status,
     g.enabled as pos_status, h.enabled as dept_status,
     CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     g.description as position, h.description as department
     FROM employee_record a
     LEFT JOIN contract b ON a.id = b.contract_emp_id
     LEFT JOIN work_schedule f ON b.work_sched_id = f.id
     LEFT JOIN position g ON b.position_id = g.positionid
     LEFT JOIN department h ON g.deptId = h.departmentid
     WHERE a.isActive = $employment_status AND a.enabled = $emp_status
     AND b.contract_status = $con_status";

     switch ($search->filter) {
       case 'divEmpID':
         $id = $this->db->escape($search->search);
         $sql .= " AND (a.employee_idno = $id)";
         break;
     case 'divName':
       $emp_name = $this->db->escape("%".$search->search."%");
       $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                 OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                 OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
       break;
       case 'divDept':
         $dept = $this->db->escape($search->search);
         $sql .= " AND (h.departmentid = $dept)";
         break;
       case 'divPos':
         $pos = $this->db->escape($search->search);
         $sql .= " AND (g.positionid = $pos)";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.= " ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $emp_status = ($row['emp_status'] == 1)
      ? '<span class="badge badge-pill badge-success">Active</span>'
      : '<span class="badge badge-pill badge-danger">Inactive</span>';

      $nestedData[] = $row['emp_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['department'];
      $nestedData[] = $row['position'];
      $nestedData[] = $emp_status;
      $nestedData[] =
      '
        <button class="btn btn-primary btn_view" style = "width:75px;"
          data-app_ref_no = "'.$row['app_ref_no'].'"
          data-employee_idno = "'.$row['emp_idno'].'"
          data-first_name = "'.$row['first_name'].'"
          data-middle_name = "'.$row['middle_name'].'"
          data-last_name = "'.$row['last_name'].'"
          data-birthday = "'.$row['birthday'].'"
          data-gender = "'.$row['gender'].'"
          data-marital_status = "'.$row['marital_status'].'"
          data-home_address1 = "'.$row['home_address1'].'"
          data-home_address2 = "'.$row['home_address2'].'"
          data-contact_no = "'.$row['contact_no'].'"
          data-email = "'.$row['email'].'"
          data-sss_no = "'.$row['sss_no'].'"
          data-philhealth_no = "'.$row['philhealth_no'].'"
          data-pagibig_no = "'.$row['pagibig_no'].'"
          data-tin_no = "'.$row['tin_no'].'"
          data-contract_id = "'.en_dec('en', $row['contract_id']).'"
          data-work_sched = '.$row['work_sched'].'
        >
          View
        </button>
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

  public function get_employee_education($id){
    $id = $this->db->escape($id);
    $sql = "SELECT *, b.description as educ_level FROM employee_education a
     INNER JOIN educlevel b ON a.level = b.id
     WHERE a.employee_idno = $id AND b.enabled = 1";
    return $this->db->query($sql);
  }

  public function get_employee_workhistory($id){
    $id = $this->db->escape($id);
    $sql = "SELECT * FROM employee_workhistory WHERE employee_idno = $id AND enabled = 1";
    return $this->db->query($sql);
  }

  public function get_employee_dependents($id){
    $id = $this->db->escape($id);
    $sql = "SELECT a.*, b.description as relation FROM employee_dependents a
     INNER JOIN relationship b ON a.relationship = b.relationshipid
     WHERE a.employee_idno = $id AND b.enabled = 1";
    return $this->db->query($sql);
  }

  public function get_contract($id){
    $id = $this->db->escape($id);
    $sql = "SELECT a.*, b.description as worksite, c.description as position, d.description as department,
     e.company, f.description as emp_status, g.description as payout_medium,
     CONCAT(h.salRange_from,' - ', h.salRange_to) as sss,
     CONCAT(i.basic_mo_sal,' - ',i.basic_mo_sal1) as philhealth,
     j.monthly_compensation as pagibig,
     CONCAT(k.aibLowerLimit,' - ',k.aibUpperLimit) as tax, l.description as paytype
     FROM contract a
     INNER JOIN worksite b ON a.work_site_id = b.worksiteid
     INNER JOIN position c ON a.position_id = c.positionid
     INNER JOIN department d ON c.deptId = d.departmentid
     INNER JOIN hris_companies e ON a.company_id = e.id
     INNER JOIN empstatus f ON a.emp_status = f.empstatusid
     INNER JOIN payoutmedium g ON a.payout_medium = g.payoutmediumid
     INNER JOIN sss h ON a.sss = h.id
     INNER JOIN philhealth i ON a.philhealth = i.phID
     INNER JOIN pagibig j ON a.pagibig = j.id
     INNER JOIN tax k ON a.tax = k.id
     INNER JOIN paytype l ON a.paytype = l.paytypeid
     WHERE a.id = $id AND a.contract_status = 'active'";
    return $this->db->query($sql);
  }
}
