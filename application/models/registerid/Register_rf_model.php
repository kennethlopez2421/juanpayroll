<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Register_rf_model extends CI_Model {
  public function get_rf_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'rf_number',
      1 => 'fullname',
      2 => 'position',
      3 => 'department'
    );


    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      b.rf_number, a.employee_idno, e.description as department, d.description as position,
      b.status, b.id
      FROM employee_record a
      INNER JOIN hris_rfid b ON a.employee_idno = b.employee_idno
      INNER JOIN contract c ON a.id = c.contract_emp_id
      LEFT JOIN position d ON c.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND c.contract_status = 'active'";


      if($search != ""){
        $search = json_decode($search);
        switch ($search->filter) {
          case 'divRfId':
            $rf_number = $this->db->escape($search->search);
            $sql .= " AND b.rf_number = $rf_number";
            break;
          case 'divName':
            $emp_name = $this->db->escape("%".$search->search."%");
            $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                      OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                      OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
            break;
          case 'divDept':
            $deptId = $this->db->escape((int)$search->search);
            $sql .= " AND e.departmentid = $deptId";
            break;
          case 'divPos':
            $pos_id = $this->db->escape($search->search);
            $sql .= " AND d.positionid = $pos_id";
            break;
          default:
            break;
        }
      }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY status ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['rf_number'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['position'];
      $nestedData[] = $row['department'];
      $nestedData[] = ($row['status'] == 'active')
        ? '<center><span class="badge badge-pill badge-success">Active</span></center>'
        : '<center><span class="badge badge-pill badge-danger">Inactive</span></center>';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }
}
