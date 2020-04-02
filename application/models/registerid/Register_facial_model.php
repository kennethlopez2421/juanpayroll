<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Register_facial_model extends CI_Model {
  public function get_register_facial_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'img_src',
      1 => 'employee_idno',
      2 => 'fullname',
      3 => 'department',
      4 => 'position'
    );

    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      a.employee_idno, e.description as department, d.description as position, c.img_src,
      c.id as fr_id, (SELECT GROUP_CONCAT(img_src SEPARATOR ',') FROM hris_facial_recog WHERE employee_idno = a.employee_idno AND enabled = 1) as images
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN hris_facial_recog c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON d.positionid = b.position_id
      LEFT JOIN department e ON e.departmentid = d.deptId
      WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active' AND c.enabled = 1";

    if($search != ""){
      $search = json_decode($search);
      switch ($search->filter) {
        case 'divEmpID':
          $employee_id = $this->db->escape($search->search);
          $sql .= " AND a.employee_idno = $employee_id";
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

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $img_row = "";
      $images = explode(',',$row['images']);
      for ($i=0; $i < count((array)$images); $i++) {
        $img_row .= '<div class="img-thumbnail d-inline-block time_img mr-1" data-url = "'.$images[$i].'"><img src="'.base_url($images[$i]).'" alt="" height = "40" width = "40"/></div>';
      }

      // $img_row = '<center><div class="img-thumbnail d-inline-block time_img mr-1" data-url = "'.$row['img_src'].'"><img src="'.base_url($row['img_src']).'" alt="" height = "40" width = "40"/></div></center>';
      $nestedData[] = $img_row;
      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['department'];
      $nestedData[] = $row['position'];
      $nestedData[] =
      '
      <center>
        <button data-del_id = "'.$row['employee_idno'].'" data-del_name = "'.$row['fullname'].'" class="btn btn-danger btn_del_modal" stype = "width:90px;">
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

  public function get_facial_recog($id){
    $sql = "SELECT * FROM hris_facial_recog WHERE employee_idno = ? AND enabled = 1";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function get_employee($id, $contract = false){
    $sql = "SELECT * FROM employee_record a LEFT JOIN contract b ON a.id = b.contract_emp_id
      WHERE a.enabled = 1 AND a.employee_idno = ?";

    if($contract){
      $sql .= " AND b.contract_status = 'active' AND b.enabled = 1";
    }
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function set_facial_recog($data){
    $this->db->insert('hris_facial_recog',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_facial_recog_status($id, $status = 0){
    $id = $this->db->escape($id);
    $status = $this->db->escape($status);
    $todaytime = $this->db->escape(todaytime());
    $sql = "UPDATE hris_facial_recog SET enabled = $status, updated_at = $todaytime WHERE employee_idno = $id AND enabled = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

}
