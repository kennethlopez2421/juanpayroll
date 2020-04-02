<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Assign_benefits_model extends CI_Model {
  public function get_assign_benefits_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $sql = "SELECT a.*, c.benefits_name,
     CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname,
     (SELECT GROUP_CONCAT(benefits_name SEPARATOR ', ') FROM hris_benefits_setting WHERE enabled = 1 AND FIND_IN_SET(id,a.benefits_id)) as benefits
     FROM hris_assign_benefits a
     INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
     INNER JOIN hris_benefits_setting c ON a.benefits_id = c.id
     WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1";

     switch ($search->filter) {
       case 'divEmpID':
         $id = $this->db->escape($search->search);
         $sql .= " AND (a.employee_idno = $id)";
         break;
     case 'divName':
       $emp_name = $this->db->escape("%".$search->search."%");
       $sql .= " AND (CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) LIKE $emp_name
                 OR CONCAT(b.last_name,', ',b.first_name) LIKE $emp_name
                 OR b.last_name LIKE $emp_name OR b.first_name LIKE $emp_name)";
       break;
       case 'divBenefits':
         $benefits_id = $this->db->escape($search->search);
         $sql .= " AND (FIND_IN_SET($benefits_id, a.benefits_id))";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['benefits'];

      $nestedData[] =
      '
      <center>
        <button class="btn btn-primary btn_edit" style = "width:75px;"
          data-uid = "'.en_dec('en', $row['id']).'"
          data-fullname = "'.$row['fullname'].'"
          data-benefits_id = "'.$row['benefits_id'].'"
        >
          Edit
        </button>
        <button class="btn btn-danger btn_delete" style = "width:75px;"
          data-delid = "'.en_dec('en', $row['id']).'"
          data-fullname = "'.$row['fullname'].'"

        >
          Delete
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

  public function get_employee_by_dept($id){
    $id = $this->db->escape($id);
    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     d.departmentid, d.description
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN position c ON b.position_id = c.positionid
     INNER JOIN department d ON c.deptId = d.departmentid
     WHERE d.departmentid = $id AND a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'";

     $sql .= " ORDER BY fullname ASC";
    return $this->db->query($sql);
  }

  public function get_benefits(){
    $sql = "SELECT * FROM hris_benefits_setting WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function get_assign_benefits($emp_id, $self = false){
    $emp_id = $this->db->escape($emp_id);
    $sql = "SELECT * FROM hris_assign_benefits WHERE enabled = 1
     AND employee_idno = $emp_id";
    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }

    return $this->db->query($sql);
  }

  public function set_assign_benefits($data){
    $this->db->insert('hris_assign_benefits',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_assign_benefits($data,$id){
    $this->db->update('hris_assign_benefits',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_assign_benefits_status($data,$id){
    $this->db->update('hris_assign_benefits',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
