<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transaction_reports_model extends CI_Model {

  public function get_transaction_reports_addpay_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = ($search->from == "") ? $this->db->escape(today()) : $this->db->escape($search->from);
    $to = ($search->to == "") ? $this->db->escape(today()) : $this->db->escape($search->to);
    $tran_status = ($search->tran_status == "") ? $this->db->escape('certified') : $this->db->escape($search->tran_status);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      e.description as dept, d.description as position, c.amount as amount, c.status,
      c.date_issued as date,
      @approver_id := (c.approved_by), @certified_id := (c.certified_by), @created_by := (c.created_by),
      @creator_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level,
      @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level,
      @creator_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level_name,
      @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level_name,
      @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level,
      @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level_name,
      @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @approver_id LIMIT 1) as approver,
      @creator := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @created_by LIMIT 1) as creator,
      @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @approver_id LIMIT 1) as approver_pos,
      @creator_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @created_by LIMIT 1) as creator_pos,
      @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @certified_id LIMIT 1) as certifier_pos,
      @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @certified_id) as certifier
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN additional_pays c ON a.employee_idno = c.employee_id
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1 AND b.contract_status = 'active'
      AND c.date_issued BETWEEN $from AND $to AND c.status = $tran_status ";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
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

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY c.date_issued DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'appproved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }
      $creator_pos = ($row['creator_level'] <= 2) ? $row['creator_level_name'] : $row['creator_pos'];
      $approver_pos = ($row['approver_level'] <= 2) ? $row['approver_level_name'] : $row['approver_pos'];
      $certifier_pos = ($row['certifier_level'] <= 2) ? $row['certifier_level_name'] : $row['certifier_pos'];

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['date'];
      $nestedData[] = '<span class="float-right">'.number_format($row['amount'], 2).'</span>';
      $nestedData[] = $row['creator'].'<span class="d-block"><small>('.$creator_pos.')</small></span>';
      $nestedData[] = $row['approver'].'<span class="d-block"><small>('.$approver_pos.')</small></span>';
      $nestedData[] = $row['certifier'].'<span class="d-block"><small>('.$certifier_pos.')</small></span>';
      $nestedData[] = $status;

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_transaction_reports_ca_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);
    $tran_status = ($search->tran_status == "")
      ? $this->db->escape('certified')
      : $this->db->escape($search->tran_status);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      e.description as dept, d.description as position, c.total_amount as amount, c.status,
      c.date_of_effectivity as date, c.total_balance,
      @approver_id := (c.approved_by), @certified_id := (c.certified_by), @created_by := (c.created_by),
      @creator_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level,
      @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level,
      @creator_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level_name,
      @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level_name,
      @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level,
      @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level_name,
      @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @approver_id LIMIT 1) as approver,
      @creator := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @created_by LIMIT 1) as creator,
      @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @approver_id LIMIT 1) as approver_pos,
      @creator_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @created_by LIMIT 1) as creator_pos,
      @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @certified_id LIMIT 1) as certifier_pos,
      @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @certified_id) as certifier
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN cash_advance_tran c ON a.employee_idno = c.employee_id
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1 AND b.contract_status = 'active'
      AND c.date_of_effectivity BETWEEN $from AND $to AND c.status = $tran_status ";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
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

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY date DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'appproved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }
      $creator_pos = ($row['creator_level'] <= 2) ? $row['creator_level_name'] : $row['creator_pos'];
      $approver_pos = ($row['approver_level'] <= 2) ? $row['approver_level_name'] : $row['approver_pos'];
      $certifier_pos = ($row['certifier_level'] <= 2) ? $row['certifier_level_name'] : $row['certifier_pos'];

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['date'];
      $nestedData[] = '<span class="float-right">'.number_format($row['amount'], 2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['total_balance'], 2).'</span>';
      $nestedData[] = $row['creator'].'<span class="d-block"><small>('.$creator_pos.')</small></span>';
      $nestedData[] = $row['approver'].'<span class="d-block"><small>('.$approver_pos.')</small></span>';
      $nestedData[] = $row['certifier'].'<span class="d-block"><small>('.$certifier_pos.')</small></span>';
      $nestedData[] = $status;

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_transaction_reports_leave_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);
    $tran_status = ($search->tran_status == "")
    ? $this->db->escape('certified')
    : $this->db->escape($search->tran_status);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      e.description as dept, d.description as position, c.status, c.date_from, c.date_to,
      DATE(c.date_created) as date, CONCAT(c.date_from,' - - ', c.date_to) as date_of_leave,
      c.number_of_days as days,
      @approver_id := (c.approved_by), @certified_id := (c.certified_by), @created_by := (c.created_by),
      @creator_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level,
      @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level,
      @creator_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level_name,
      @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level_name,
      @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level,
      @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level_name,
      @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @approver_id LIMIT 1) as approver,
      @creator := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @created_by LIMIT 1) as creator,
      @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @approver_id LIMIT 1) as approver_pos,
      @creator_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @created_by LIMIT 1) as creator_pos,
      @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @certified_id LIMIT 1) as certifier_pos,
      @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @certified_id) as certifier
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN leave_tran c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1 AND b.contract_status = 'active'
      AND DATE(c.date_created) BETWEEN $from AND $to AND c.status = $tran_status ";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
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

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY date DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'appproved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }
      $creator_pos = ($row['creator_level'] <= 2) ? $row['creator_level_name'] : $row['creator_pos'];
      $approver_pos = ($row['approver_level'] <= 2) ? $row['approver_level_name'] : $row['approver_pos'];
      $certifier_pos = ($row['certifier_level'] <= 2) ? $row['certifier_level_name'] : $row['certifier_pos'];

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['date'];
      $nestedData[] = $row['date_of_leave'];
      $nestedData[] = '<span class="float-right">'.$row['days'].'</span>';
      $nestedData[] = $row['creator'].'<span class="d-block"><small>('.$creator_pos.')</small></span>';
      $nestedData[] = $row['approver'].'<span class="d-block"><small>('.$approver_pos.')</small></span>';
      $nestedData[] = $row['certifier'].'<span class="d-block"><small>('.$certifier_pos.')</small></span>';
      $nestedData[] = $status;

      $data[] = $nestedData;
    }

    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_transaction_reports_overtimepays_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);
    $tran_status = ($search->tran_status == "")
    ? $this->db->escape('certified')
    : $this->db->escape($search->tran_status);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      e.description as dept, d.description as position, c.status,
      DATE(c.date_created) as date, c.date_rendered, c.type, c.minutes_of_overtime,
      @approver_id := (c.approved_by), @certified_id := (c.certified_by), @created_by := (c.created_by),
      @creator_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level,
      @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level,
      @creator_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level_name,
      @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level_name,
      @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level,
      @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level_name,
      @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @approver_id LIMIT 1) as approver,
      @creator := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @created_by LIMIT 1) as creator,
      @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @approver_id LIMIT 1) as approver_pos,
      @creator_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @created_by LIMIT 1) as creator_pos,
      @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @certified_id LIMIT 1) as certifier_pos,
      @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @certified_id) as certifier
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN overtime_pays c ON a.employee_idno = c.employee_id
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1 AND b.contract_status = 'active'
      AND DATE(c.date_created) BETWEEN $from AND $to AND c.status = $tran_status ";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
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

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY date DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'appproved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }
      $creator_pos = ($row['creator_level'] <= 2) ? $row['creator_level_name'] : $row['creator_pos'];
      $approver_pos = ($row['approver_level'] <= 2) ? $row['approver_level_name'] : $row['approver_pos'];
      $certifier_pos = ($row['certifier_level'] <= 2) ? $row['certifier_level_name'] : $row['certifier_pos'];

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['minutes_of_overtime'];
      $nestedData[] = $row['date_rendered'];
      $nestedData[] = $row['date'];
      $nestedData[] = ucfirst($row['type']);
      $nestedData[] = $row['creator'].'<span class="d-block"><small>('.$creator_pos.')</small></span>';
      $nestedData[] = $row['approver'].'<span class="d-block"><small>('.$approver_pos.')</small></span>';
      $nestedData[] = $row['certifier'].'<span class="d-block"><small>('.$certifier_pos.')</small></span>';
      $nestedData[] = $status;

      $data[] = $nestedData;
    }

    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_transaction_reports_sal_deduct_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);
    $tran_status = ($search->tran_status == "")
      ? $this->db->escape('certified')
      : $this->db->escape($search->tran_status);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      e.description as dept, d.description as position, c.status, c.amount as amount,
      DATE(c.date_created) as date, f.description as deduction
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN salary_deduction c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      INNER JOIN deduction f ON f.deductionid = c.deduct_category
      WHERE a.enabled = 1 AND b.contract_status = 'active' AND f.enabled = 1
      AND DATE(c.date_created) BETWEEN $from AND $to AND c.status = $tran_status ";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
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

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY date DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'appproved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['position'];
      $nestedData[] = $row['date'];
      $nestedData[] = $row['deduction'];
      $nestedData[] = '<span class="float-right">'.number_format($row['amount'], 2).'</span>';
      $nestedData[] = $status;

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_transaction_reports_workorder_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);
    $tran_status = ($search->tran_status == "")
      ? $this->db->escape('certified')
      : $this->db->escape($search->tran_status);

    $sql = "SELECT c.id, a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      e.description as dept, d.description as position, c.status, f.location, f.contact_person,
      c.date as date, CONCAT(c.start_time,' - ',c.end_time) as wtime,
      @approver_id := (c.approved_by), @certified_id := (c.certified_by), @created_by := (c.created_by),
      @creator_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level,
      @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level,
      @creator_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level_name,
      @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level_name,
      @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level,
      @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level_name,
      @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @approver_id LIMIT 1) as approver,
      @creator := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @created_by LIMIT 1) as creator,
      @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @approver_id LIMIT 1) as approver_pos,
      @creator_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @created_by LIMIT 1) as creator_pos,
      @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @certified_id LIMIT 1) as certifier_pos,
      @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @certified_id) as certifier
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN work_order c ON a.employee_idno = c.employee_id
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      INNER JOIN work_order_itenerary f ON c.id = f.workorder_id
      WHERE a.enabled = 1 AND b.contract_status = 'active'
      AND c.date BETWEEN $from AND $to AND c.status = $tran_status ";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
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

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY date DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'approved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }

      $creator_pos = ($row['creator_level'] <= 2) ? $row['creator_level_name'] : $row['creator_pos'];
      $approver_pos = ($row['approver_level'] <= 2) ? $row['approver_level_name'] : $row['approver_pos'];
      $certifier_pos = ($row['certifier_level'] <= 2) ? $row['certifier_level_name'] : $row['certifier_pos'];

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['location'];
      $nestedData[] = $row['contact_person'];
      $nestedData[] = $row['date'];
      $nestedData[] = $row['wtime'];
      $nestedData[] = $row['creator'].'<span class="d-block"><small>('.$creator_pos.')</small></span>';
      $nestedData[] = $row['approver'].'<span class="d-block"><small>('.$approver_pos.')</small></span>';
      $nestedData[] = $row['certifier'].'<span class="d-block"><small>('.$certifier_pos.')</small></span>';
      $nestedData[] = $status;

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_transaction_reports_offset_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);
    $tran_status = ($search->tran_status == "")
      ? $this->db->escape('certified')
      : $this->db->escape($search->tran_status);

    $sql = "SELECT c.id, a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      e.description as dept, d.description as position, c.status, c.offset_type, c.offset_min,
      c.date_rendered as date_to_offset, DATE(c.updated_at) as date_filed,
      @approver_id := (c.approved_by), @certified_id := (c.certified_by), @created_by := (c.created_by),
      @creator_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level,
      @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level,
      @creator_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level_name,
      @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level_name,
      @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level,
      @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level_name,
      @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @approver_id LIMIT 1) as approver,
      @creator := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @created_by LIMIT 1) as creator,
      @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @approver_id LIMIT 1) as approver_pos,
      @creator_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @created_by LIMIT 1) as creator_pos,
      @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @certified_id LIMIT 1) as certifier_pos,
      @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @certified_id) as certifier
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN hris_offset c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1 AND b.contract_status = 'active'
      AND DATE(c.updated_at) BETWEEN $from AND $to AND c.status = $tran_status ";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
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

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY date_filed DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'approved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }

      $creator_pos = ($row['creator_level'] <= 2) ? $row['creator_level_name'] : $row['creator_pos'];
      $approver_pos = ($row['approver_level'] <= 2) ? $row['approver_level_name'] : $row['approver_pos'];
      $certifier_pos = ($row['certifier_level'] <= 2) ? $row['certifier_level_name'] : $row['certifier_pos'];

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['offset_min'];
      $nestedData[] = ucfirst($row['offset_type']);
      $nestedData[] = $row['date_to_offset'];
      $nestedData[] = $row['date_filed'];
      $nestedData[] = $row['creator'].'<span class="d-block"><small>('.$creator_pos.')</small></span>';
      $nestedData[] = $row['approver'].'<span class="d-block"><small>('.$approver_pos.')</small></span>';
      $nestedData[] = $row['certifier'].'<span class="d-block"><small>('.$certifier_pos.')</small></span>';
      $nestedData[] = $status;

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_transaction_reports_worksched_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search->from);
    $to = $this->db->escape($search->to);
    $tran_status = 'certified';
    if($search->tran_status != ""){
      $tran_status = ($search->tran_status == 'certify') ? "certified" : $tran_status;
      $tran_status = ($search->tran_status == 'approve') ? "approved" : $tran_status;
      $tran_status = ($search->tran_status == 'waiting') ? "waiting" : $tran_status;
    }
    $tran_status = $this->db->escape($tran_status);

    $sql = "SELECT c.*, DATE(c.updated_at) as date_filed,
     @department := (SELECT description FROM department WHERE enabled = 1 AND departmentid = c.department_id) as department,
     @fullname := (SELECT CONCAT(last_name,', ',first_name,' ',middle_name) FROM employee_record WHERE enabled = 1 AND employee_idno = c.employee_idno) as fullname,
     @last_firstname := (SELECT CONCAT(last_name,', ',first_name) FROM employee_record WHERE enabled = 1 AND employee_idno = c.employee_idno) as last_firstname,
     @last_name := (SELECT CONCAT(last_name) FROM employee_record WHERE enabled = 1 AND employee_idno = c.employee_idno) as lastname,
     @approver_id := (c.approved_by), @certified_id := (c.certified_by), @created_by := (c.created_by),
     @creator_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level,
     @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level,
     @creator_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @created_by LIMIT 1) as creator_level_name,
     @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @approver_id LIMIT 1) as approver_level_name,
     @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level,
     @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = @certified_id LIMIT 1) as certifier_level_name,
     @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @approver_id LIMIT 1) as approver,
     @creator := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @created_by LIMIT 1) as creator,
     @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @approver_id LIMIT 1) as approver_pos,
     @creator_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @created_by LIMIT 1) as creator_pos,
     @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = @certified_id LIMIT 1) as certifier_pos,
     @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = @certified_id) as certifier
     FROM hris_custom_schedule c
     WHERE c.enabled = 1 AND c.date_from <= $to AND c.date_to >= $from
     AND status = $tran_status";

    ### sub filter ###
    switch ($search->filter2) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND c.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (employee) LIKE $emp_name
                  OR last_firstname LIKE $emp_name
                  OR lastname LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search->search);
        $sql .= " AND c.department_id = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY date_filed DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      switch ($row['status']) {
        case 'certified':
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
        case 'approved':
          $status = '<center><h5 class = "text-info">Approved</h5></center>';
          break;
        case 'waiting':
          $status = '<center><h5 class = "text-warning">Waiting for Approval</h5></center>';
          break;
        default:
          $status = '<center><h5 class = "text-success">Certified</h5></center>';
          break;
      }

      $creator_pos = ($row['creator_level'] <= 2) ? $row['creator_level_name'] : $row['creator_pos'];
      $approver_pos = ($row['approver_level'] <= 2) ? $row['approver_level_name'] : $row['approver_pos'];
      $certifier_pos = ($row['certifier_level'] <= 2) ? $row['certifier_level_name'] : $row['certifier_pos'];

      $nestedData[] = $row['department'];
      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['date_from'];
      $nestedData[] = $row['date_to'];
      $nestedData[] = $row['date_filed'];
      $nestedData[] = $row['creator'].'<span class="d-block"><small>('.$creator_pos.')</small></span>';
      $nestedData[] = $row['approver'].'<span class="d-block"><small>('.$approver_pos.')</small></span>';
      $nestedData[] = $row['certifier'].'<span class="d-block"><small>('.$certifier_pos.')</small></span>';
      $nestedData[] = $status;
      $nestedData[] =
      '
        <button class="btn btn-primary btn_worksched" data-work_sched = '.$row['work_sched'].'>
          Work Schedule
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

}
