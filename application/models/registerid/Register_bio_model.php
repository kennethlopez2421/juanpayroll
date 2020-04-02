<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Register_bio_model extends CI_Model {
  public function get_biometrics_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'bio_id',
      1 => 'fullname',
      2 => 'position',
      3 => 'department'
    );

    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      b.bio_id, a.employee_idno, e.description as department, d.description as position,
      b.status, b.id
      FROM employee_record a
      INNER JOIN hris_biometrics_id b ON a.employee_idno = b.employee_idno
      INNER JOIN contract c ON a.id = c.contract_emp_id
      LEFT JOIN position d ON c.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1";

    if($search != ""){
      $search = json_decode($search);
      switch ($search->filter) {
        case 'divBioId':
          $bio_id = $this->db->escape($search->search);
          $sql .= " AND b.bio_id = $bio_id";
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
    $sql .= " GROUP BY a.employee_idno";
    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY b.status ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['bio_id'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['position'];
      $nestedData[] = $row['department'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_edit_modal" style = "width:80px;"
            data-uid = "'.$row['id'].'"
            data-bio_id = "'.$row['bio_id'].'"
          >
            <i class="fa fa-pencil mr-1"></i>Edit
          </button>
          <button class="btn btn-danger btn_del_modal" style = "width:80px;"
            data-delid = "'.$row['id'].'"
            data-bio_id = "'.$row['bio_id'].'"
            data-name = "'.$row['fullname'].'"
          >
            <i class="fa fa-trash mr-1"></i>Delete
          </button>
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

  public function get_emp_id($id){
    $sql = "SELECT * FROM employee_record WHERE enabled = 1 AND employee_idno = ?";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function get_bio_id($id,$prev_id = false){
    $sql = "SELECT * FROM hris_biometrics_id WHERE enabled = 1 AND bio_id = ?";
    $data = array($id);

    if($prev_id){
      $sql .= " AND id !=  ?";
      $data = array($id,$prev_id);
    }
    return $this->db->query($sql,$data);
  }

  public function set_biometrics_id($data){
    $this->db->insert('hris_biometrics_id',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_biometrics_id($data){
    $sql = "UPDATE hris_biometrics_id SET bio_id = ? WHERE id = ? AND enabled = 1";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_biometrics_enabled($data){
    $sql = "UPDATE hris_biometrics_id SET enabled = ? WHERE enabled = 1 AND id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

}
