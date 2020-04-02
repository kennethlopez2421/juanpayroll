<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Issued_items_model extends CI_Model {
  public function get_issued_items_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT a.*, c.cat_name,
     @fullname := CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname,
     @issued_by := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) FROM hris_users WHERE employee_idno = a.issued_by) as issued_by
     FROM hris_issued_items a
     INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
     INNER JOIN hris_items_category c ON a.cat_id = c.id
     WHERE a.enabled = 1";

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
       case 'divCat':
         $cat = $this->db->escape($search->search);
         $sql .= " AND (a.cat_id = $cat)";
         break;
       case 'divSerial':
         $serial = $this->db->escape($search->search);
         $sql .= " AND (a.serial_no = $serial)";
         break;
       case 'divDateIssued':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (a.date_issued BETWEEN $from AND $to)";
         break;
       case 'divDateReceived':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (a.date_received BETWEEN $from AND $to)";
         break;
       case 'divDateReturned':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (a.date_returned BETWEEN $from AND $to)";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY a.date_issued ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['cat_name'];
      $nestedData[] = $row['item_name'];
      $nestedData[] = $row['serial_no'];
      $nestedData[] = ucfirst($row['item_condition']);
      $nestedData[] = $row['date_issued'];
      $nestedData[] = $row['date_received'];
      $nestedData[] = $row['date_returned'];
      $nestedData[] = $row['issued_by'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_edit" style = "width:75px;"
            data-uid = "'.en_dec('en', $row['id']).'"
            data-employee_idno = "'.$row['employee_idno'].'"
            data-emp_name = "'.$row['fullname'].'"
            data-cat_id = "'.$row['cat_id'].'"
            data-item_name = "'.$row['item_name'].'"
            data-serial_no = "'.$row['serial_no'].'"
            data-item_condition = "'.$row['item_condition'].'"
            data-date_issued = "'.$row['date_issued'].'"
            data-date_received = "'.$row['date_received'].'"
            data-date_returned = "'.$row['date_returned'].'"
            data-price = "'.$row['price'].'"
            data-notes = "'.$row['notes'].'"
          >
            Edit
          </button>
          <button class="btn btn-danger btn_delete" style = "width:75px;"
            data-delid = "'.en_dec('en',$row['id']).'"
            data-item_name = "'.$row['item_name'].'"
            data-serial_no = "'.$row['serial_no'].'"
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

  public function get_item_category(){
    $sql = "SELECT * FROM hris_items_category WHERE enabled = 1 ORDER BY cat_name ASC";
    return $this->db->query($sql);
  }

  public function get_issued_items_by_serial_no($serial,$self = false){
    $serial = $this->db->escape($serial);
    $sql = "SELECT * FROM hris_issued_items WHERE serial_no = $serial AND enabled = 1
     AND date_returned IS NULL";
    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    return $this->db->query($sql);
  }

  public function set_issued_item($data){
    $this->db->insert('hris_issued_items',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_issued_item($data,$id){
    $this->db->update('hris_issued_items', $data, array('id' => $id));
    return ($this->db->affected_rows() > 0 ) ? true : false;
  }

  public function update_issued_item_status($data,$id){
    $this->db->update('hris_issued_items',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
