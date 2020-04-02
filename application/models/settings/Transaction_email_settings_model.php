<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transaction_email_settings_model extends CI_Model {
  public function get_email_settings_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $sql = "SELECT a.*, c.description as department, b.cn_name
     FROM hris_transaction_email_settings a
     INNER JOIN hris_content_navigation b ON a.content_nav_id = b.id
     INNER JOIN department c ON a.department_id = c.departmentid
     WHERE a.enabled = 1 AND b.status = 1 AND c.enabled = 1 AND b.cn_fkey = 14";

     switch ($search->filter) {
       case 'divTran':
         $id = $this->db->escape($search->search);
         $sql .= " AND (a.content_nav_id = $id)";
         break;
       case 'divDept':
         $dept = $this->db->escape($search->search);
         $sql .= " AND (a.department_id = $dept)";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY b.cn_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {

      $nestedData=array();

      $nestedData[] = $row['cn_name'];
      $nestedData[] = $row['department'];
      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_edit_modal" style = "width:70px;"
            data-uid = "'.en_dec('en',$row['id']).'"
            data-nav_id = "'.$row['content_nav_id'].'"
            data-dept_id = "'.$row['department_id'].'"
            data-approver = "'.$row['approver'].'"
            data-certifier = "'.$row['certifier'].'"
          >
            Edit
          </button>
          <button class="btn btn-danger btn_del_modal" style = "width:70px;"
            data-delid = "'.en_dec('en',$row['id']).'"
            data-cn_name = "'.$row['cn_name'].'"
            data-dept_name = "'.$row['department'].'"
          >
            Delete
          </button>
        </center>
      ';

      $nestedData[] = '';

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_content_nav(){
    $sql = "SELECT cn_name, id FROM hris_content_navigation WHERE cn_fkey = 14 AND status = 1 ORDER BY cn_name";
    return $this->db->query($sql);
  }

  public function get_employee_from_dept($id){
    $id = $this->db->escape($id);
    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     a.employee_idno, c.description as position
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN position c ON b.position_id = c.positionid
     INNER JOIN hris_position d ON b.position_access_lvl = d.position_id
     WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1
     AND b.contract_status = 'active' AND d.hierarchy_lvl <= 5 AND d.hierarchy_lvl >= 2 AND FIND_IN_SET($id,c.department_access)
     ORDER BY fullname ASC";
    return $this->db->query($sql);
  }

  public function get_email_settings($nav_id, $dept_id, $self = false){
    $nav_id = $this->db->escape($nav_id);
    $dept_id = $this->db->escape($dept_id);

    $sql = "SELECT * FROM hris_transaction_email_settings
     WHERE content_nav_id = $nav_id AND department_id = $dept_id AND enabled = 1";

    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    return $this->db->query($sql);
  }

  public function set_email_settings($data){
    $this->db->insert('hris_transaction_email_settings',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_email_settings($data,$id){
    $this->db->update('hris_transaction_email_settings',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_email_settings_status($data,$id){
    $this->db->update('hris_transaction_email_settings',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
