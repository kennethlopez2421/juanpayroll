<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Memos_model extends CI_Model {
  public function get_memo_json($search,$status){
    $requestData = $_REQUEST;
    $status = $this->db->escape($status);

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     d.description as department, e.*
     FROM employee_record a
     INNER JOIN contract b ON a.id = b. contract_emp_id
     LEFT JOIN position c ON b.position_id = c.positionid
     LEFT JOIN department d ON c.deptId = d.departmentid
     INNER JOIN hris_memorandum e ON a.employee_idno = e.employee_idno
     WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'
     AND e.enabled = 1 AND e.status = $status";

     ### sub filter ###
     switch ($search->filter) {
       case 'divName':
         $emp_name = $this->db->escape("%".$search->search."%");
         $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                   OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                   OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
         break;
       case 'divDate':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND e.date >= $from AND e.date <= $to";
         break;
       case 'divDept':
         $deptid = $this->db->escape($search->search);
         $sql .= " AND d.departmentid = $deptid";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY date ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['department'];
      $nestedData[] = $row['re'];
      $nestedData[] = $row['date'];
      $nestedData[] = ($row['status'] == 'approved') ? '<center><span class="badge badge-pill badge-sm badge-success">Apporved</span></center>' : '<center><span class="badge badge-pill badge-sm badge-warning">Pending</span></center>';
      $nestedData[] =
      '
        <center>
          <button style = "width:90px;" class="btn btn-primary btn_edit"
           data-uid = "'.$row['id'].'"
           data-name = "'.$row['fullname'].'"
           data-emp_idno = "'.$row['employee_idno'].'"
           data-dept_id = "'.$row['dept_id'].'"
           data-memo_file = "'.$row['memo_file'].'"
           data-re = "'.$row['re'].'"
           data-date = "'.$row['date'].'"
           data-status = "'.$row['status'].'"
          >
           <i class="fa fa-eye mr-1"></i>View
          </button>
          <button style = "width:90px;" class="btn btn-danger"><i class="fa fa-trash mr-1"></i>Delete</button>
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

  public function set_memo($data){
    $this->db->insert('hris_memorandum',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_memo_status($data,$id){
    $this->db->update('hris_memorandum',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_memo($data,$id){
    $this->db->update('hris_memorandum',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
