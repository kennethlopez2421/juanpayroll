<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contract_expiration_reports_model extends CI_Model {

  public function get_contract_expiration_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'employee_idno',
      1 => 'fullname',
      2 => 'dept_desc',
      3 => 'pos_desc',
      4 => 'contract_end'
    );

    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      a.employee_idno, c.description as pos_desc, d.description as dept_desc, b.contract_end
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      LEFT JOIN position c ON b.position_id = c.positionid
      LEFT JOIN department d ON c.deptId = d.departmentid
      WHERE a.enabled = 1 AND b.contract_status = 'active' AND c.enabled = 1 AND d.enabled = 1";

    ### sub filter ###
    switch ($search->filter) {
      case 'divEmpID':
        $emp_id = $this->db->escape($search->keyword);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search->keyword."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search->keyword);
        $sql .= " AND d.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search->keyword);
        $sql .= " AND c.positionid = $pos_id";
        break;
      case 'divDate':
        $from = $this->db->escape($search->from);
        $to = $this->db->escape($search->to);
        $sql .= " AND b.contract_end BETWEEN $from AND $to";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY contract_end ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept_desc'];
      $nestedData[] = $row['pos_desc'];
      $nestedData[] = $row['contract_end'];
      $nestedData[] =
      '
        <center>
          <button class = "btn btn-sm btn-primary btn_create_eval"
            data-emp_id = "'.$row['employee_idno'].'"
            data-fullname = "'.$row['fullname'].'">
            Create Evaluation
          </button>
        </center>
      ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data,
      "seach" => $search
    );

    return $json_data;
  }

  public function get_users_to_send_evaluation($data){
    $sql = "SELECT c.employee_idno, CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname,
      e.description as position_desc, b.hierarchy_lvl
      FROM hris_users a
      INNER JOIN hris_position b ON a.position_id = b.position_id
      LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
      LEFT JOIN contract d ON c.id = d.contract_emp_id
      LEFT JOIN position e ON d.position_id = e.positionid
      WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1 AND e.enabled = 1
      AND d.contract_status = 'active' AND a.deptId = ? AND b.hierarchy_lvl = ?";
    return $this->db->query($sql,$data);

    // return $this->db->last_query();
  }

  public function get_admin_to_send_evaluation(){
    $sql = "SELECT CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname) as fullname,
     a.employee_idno, b.position, b.position_id, b.hierarchy_lvl as pos_lvl
     FROM hris_users a
     INNER JOIN hris_position b ON a.position_id = b.position_id
     WHERE b.hierarchy_lvl <= 1 AND a.enabled = 1 AND b.enabled = 1";

    return $this->db->query($sql);
  }

  public function get_user_evaluation($data){
    $sql = "SELECT employee_idno, eval_from, eval_to
      FROM hris_evaluations
      WHERE enabled = 1 AND employee_idno = ? AND management_id = ? AND eval_from = ? AND eval_to = ?";
    return $this->db->query($sql,$data);
  }

  public function get_evaluation_ref_no($ref_no){
    $sql = "SELECT * FROM hris_evaluations WHERE enabled = 1 AND ref_no = ?";
    $data = array($ref_no);
    return $this->db->query($sql,$data);
  }

  public function get_evaluator_info($id){
    $id = $this->db->escape($id);
    $sql = "SELECT a.email, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     c.hierarchy_lvl as pos_level
     FROM employee_record a
     INNER JOIN hris_users b ON a.employee_idno = b.employee_idno
     INNER JOIN hris_position c ON b.position_id = c.position_id
     WHERE a.employee_idno = $id AND a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1";
    return $this->db->query($sql);
  }

  public function get_evaluatee($id){
    $id = $this->db->escape($id);
    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname
     FROM employee_record a
     WHERE a.employee_idno = $id AND a.enabled = 1";
    return $this->db->query($sql);
  }

  public function get_position_w_access_to_evaluate(){
    $sql = "SELECT position, position_id, hierarchy_lvl, access_func_nav
     FROM hris_position WHERE enabled = 1";
    $positions = $this->db->query($sql)->result_array();
    $evaluate_access = [];
    foreach($positions as $pos){
      if($pos['access_func_nav'] != ''){
        $access = json_decode($pos['access_func_nav']);
        foreach($access as $nav){
          if(in_array(5,$nav->access_func_nav)){
            $evaluate_access[] = $pos;
          }
        }
      }
    }
    return $evaluate_access;
  }

  public function set_evaluation($data){
    $this->db->insert('hris_evaluations',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

}
