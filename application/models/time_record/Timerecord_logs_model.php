<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Timerecord_logs_model extends CI_Model {
  public function get_timerecord_logs_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'logs',
      1 => 'created_at'
    );


    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      CONCAT(c.user_lname,', ',c.user_fname,' ',c.user_mname) as admin, b.*
      FROM employee_record a
      INNER JOIN hris_timelog_logs b ON a.employee_idno = b.employee_idno
      INNER JOIN hris_users c ON b.admin_id = c.employee_idno";

    if($search != ""){
      $search = json_decode($search);

      switch ($search->filter) {
        case 'divAdmin':
          $emp_id = $this->db->escape($search->keyword);
          $sql .= " AND c.employee_idno = $emp_id";
          break;
        case 'divName':
          $emp_name = $this->db->escape($search->keyword."%");
          $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                    OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                    OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
          break;
        case 'divLogDate':
          $from = $this->db->escape($search->from);
          $to = $this->db->escape($search->to);
          $sql .= " AND DATE(b.created_at) BETWEEN $from AND $to";
          break;
        case 'divDate':
          $from = $this->db->escape($search->from);
          $to = $this->db->escape($search->to);
          $sql .= " AND b.date BETWEEN $from AND $to";
          break;
        default:
          break;
      }
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY b.created_at DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $logs = explode(',',$row['logs']);
      $logs_html = "";
      for ($i=0; $i < count((array)$logs); $i++) {
        $logs_html .= '<p class = "mb-1">'.$logs[$i].'</p>';
      }
      // $logs_html .= " from the ".$row['type'];
      $nestedData=array();
      $nestedData[] = $row['admin'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $logs_html;
      $nestedData[] = $row['date'];
      $nestedData[] = $row['created_at'];
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
