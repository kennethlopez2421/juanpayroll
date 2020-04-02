<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Work_schedule_model extends CI_Model {

  public function __construct(){
    parent::__construct();
    if(isset($this->session->content_url)){
      $content_id = $this->model->get_url_content_id($this->session->content_url);
      $content_id = ($content_id->num_rows() > 0) ? $content_id->row()->id : 0;
      if(count((array)$this->session->get_position_access->access_func_nav) > 0){
        $this->access_ids = check_func_access($this->session->get_position_access->access_func_nav,$content_id);
        // $this->access_ids = $content_id;
      }else{
        $this->access_ids = [];
      }
    }
  }

  public $access_ids;

  public function get_work_schedule_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $sql = "SELECT b.description as department, a.date_from, a.date_to, a.type,
     CONCAT(c.last_name,', ', c.first_name,' ', c.middle_name) as fullname, a.status,
     a.id, a.department_id as dept_id, a.employee_idno, a.work_sched
     FROM hris_custom_schedule a
     INNER JOIN department b ON a.department_id = b.departmentid
     LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
     WHERE a.enabled = 1 AND a.status = 'waiting'";

     if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
       $sql .= " AND a.department_id IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
     }

     if($this->session->position_lvl > supervisor_and_above() && $this->session->deptId != hr_id()){
       $employee_idno = $this->db->escape($this->session->emp_idno);
       $sql .= " AND a.employee_idno = $employee_idno";
     }

     if($this->session->login_type != 'admin'){
       $employee_idno = $this->db->escape($this->session->emp_idno);
       $sql .= " AND a.employee_idno = $employee_idno";
     }

    switch ($search->filter) {
      case 'divEmpID':
        $id = $this->db->escape($search->search);
        $sql .= " AND (a.employee_idno = $id)";
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

    $sql.=" ORDER BY a.created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      if($row['status'] == 'waiting'){
        $status = '<span class="badge badge-warning badge-pill">Waiting for approval</span>';
      }

      if($row['status'] == 'approve'){
        $status = '<span class="badge badge-success badge-pill">Approve</span>';
      }

      if($row['status'] == 'certify'){
        $status = '<span class="badge badge-info badge-pill">Certify</span>';
      }
      $nestedData[] =
      '
      <label class="container_label">
        <input type="checkbox" name = "wo_id_batch[]" class = "select waiting_select" value = "'.en_dec('en',$row['id']).'">
        <span class="checkmark"></span>
      </label>
      ';
      $nestedData[] = $row['department'];
      $nestedData[] = ($row['type'] == 'department') ? '<center>------</center>' : $row['fullname'];
      $nestedData[] = $row['date_from'];
      $nestedData[] = $row['date_to'];
      $nestedData[] = '<center>'.$status.'</center>';
      $buttons = "";
      $buttons .= (approve_access($this->access_ids))
      ? '<button class="btn btn-info btn_status" data-status = "approve" data-ws_id = "'.en_dec('en', $row['id']).'" style  = "width:80px;">Approved</button>' : '';
      $buttons .=
      '
        <button class="btn btn-primary btn_update" style = "width:80px;"
          data-uid = "'.en_dec('en',$row['id']).'"
          data-department_id = "'.$row['dept_id'].'"
          data-employee_idno = "'.$row['employee_idno'].'"
          data-date_from = "'.$row['date_from'].'"
          data-date_to = "'.$row['date_to'].'"
          data-work_sched = '.$row['work_sched'].'
          data-status = "'.$row['status'].'"
        >
          Update
        </button>
        <button class="btn btn-danger btn_reject" style = "width:80px;"
         data-delid = "'.en_dec('en', $row['id']).'"
         data-dept_name = "'.$row['department'].'"
         data-fullname = "'.$row['fullname'].'"
         data-type = "'.$row['type'].'"
        >
          Reject
        </button>
      ';

      $nestedData[] = '<center>'.$buttons.'</center>';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_approved_work_schedule_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $sql = "SELECT b.description as department, a.date_from, a.date_to, a.type,
     CONCAT(c.last_name,', ', c.first_name,' ', c.middle_name) as fullname, a.status,
     a.id, a.department_id as dept_id, a.employee_idno, a.work_sched
     FROM hris_custom_schedule a
     INNER JOIN department b ON a.department_id = b.departmentid
     LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
     WHERE a.enabled = 1 AND a.status = 'approve'";

     if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
       $sql .= " AND a.department_id IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
     }

     if($this->session->position_lvl > supervisor_and_above() && $this->session->deptId != hr_id()){
       $employee_idno = $this->db->escape($this->session->emp_idno);
       $sql .= " AND a.employee_idno = $employee_idno";
     }

     if($this->session->login_type != 'admin'){
       $employee_idno = $this->db->escape($this->session->emp_idno);
       $sql .= " AND a.employee_idno = $employee_idno";
     }

    switch ($search->filter) {
      case 'divEmpID':
        $id = $this->db->escape($search->search);
        $sql .= " AND (a.employee_idno = $id)";
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

    $sql.=" ORDER BY a.created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      if($row['status'] == 'waiting'){
        $status = '<span class="badge badge-warning badge-pill">Waiting for approval</span>';
      }

      if($row['status'] == 'approve'){
        $status = '<span class="badge badge-success badge-pill">Approved</span>';
      }

      if($row['status'] == 'certify'){
        $status = '<span class="badge badge-info badge-pill">Certified</span>';
      }
      $nestedData[] =
      '
      <label class="container_label">
        <input type="checkbox" name = "wo_id_batch[]" class = "select approved_select" value = "'.en_dec('en',$row['id']).'">
        <span class="checkmark"></span>
      </label>
      ';
      $nestedData[] = $row['department'];
      $nestedData[] = ($row['type'] == 'department') ? '<center>------</center>' : $row['fullname'];
      $nestedData[] = $row['date_from'];
      $nestedData[] = $row['date_to'];
      $nestedData[] = '<center>'.$status.'</center>';
      $buttons = "";
      $buttons .= (certify_access($this->access_ids))
      ? '<button class="btn btn-info btn_status" data-status = "certify" data-ws_id = "'.en_dec('en',$row['id']).'" style = "width:80px;">Certify</button>' : '';
      $buttons .=
      '
        <button class="btn btn-primary btn_update" style = "width:80px;"
          data-uid = "'.en_dec('en',$row['id']).'"
          data-department_id = "'.$row['dept_id'].'"
          data-employee_idno = "'.$row['employee_idno'].'"
          data-date_from = "'.$row['date_from'].'"
          data-date_to = "'.$row['date_to'].'"
          data-work_sched = '.$row['work_sched'].'
          data-status = "'.$row['status'].'"
        >
          Update
        </button>
        <button class="btn btn-danger btn_reject" style = "width:80px;"
         data-delid = "'.en_dec('en', $row['id']).'"
         data-dept_name = "'.$row['department'].'"
         data-fullname = "'.$row['fullname'].'"
         data-type = "'.$row['type'].'"
        >
          Reject
        </button>
      ';
      $nestedData[] = '<center>'.$buttons.'</center>';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_certified_work_schedule_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $sql = "SELECT b.description as department, a.date_from, a.date_to, a.type,
     CONCAT(c.last_name,', ', c.first_name,' ', c.middle_name) as fullname, a.status,
     a.id, a.department_id as dept_id, a.employee_idno, a.work_sched
     FROM hris_custom_schedule a
     INNER JOIN department b ON a.department_id = b.departmentid
     LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
     WHERE a.enabled = 1 AND a.status = 'certify'";

     if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
       $sql .= " AND a.department_id IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
     }

     if($this->session->position_lvl > supervisor_and_above() && $this->session->deptId != hr_id()){
       $employee_idno = $this->db->escape($this->session->emp_idno);
       $sql .= " AND a.employee_idno = $employee_idno";
     }

     if($this->session->login_type != 'admin'){
       $employee_idno = $this->db->escape($this->session->emp_idno);
       $sql .= " AND a.employee_idno = $employee_idno";
     }

    switch ($search->filter) {
      case 'divEmpID':
        $id = $this->db->escape($search->search);
        $sql .= " AND (a.employee_idno = $id)";
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

    $sql.=" ORDER BY a.created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      if($row['status'] == 'waiting'){
        $status = '<span class="badge badge-warning badge-pill">Waiting for approval</span>';
      }

      if($row['status'] == 'approve'){
        $status = '<span class="badge badge-success badge-pill">Approved</span>';
      }

      if($row['status'] == 'certify'){
        $status = '<span class="badge badge-info badge-pill">Certified</span>';
      }
      $nestedData[] = $row['department'];
      $nestedData[] = ($row['type'] == 'department') ? '<center>------</center>' : $row['fullname'];
      $nestedData[] = $row['date_from'];
      $nestedData[] = $row['date_to'];
      $nestedData[] = '<center>'.$status.'</center>';
      $nestedData[] =
      '
      <center>
        <button class="btn btn-primary btn_update" style = "width:80px;"
          data-uid = "'.en_dec('en',$row['id']).'"
          data-department_id = "'.$row['dept_id'].'"
          data-employee_idno = "'.$row['employee_idno'].'"
          data-date_from = "'.$row['date_from'].'"
          data-date_to = "'.$row['date_to'].'"
          data-work_sched = '.$row['work_sched'].'
          data-status = "'.$row['status'].'"
        >
          View
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

     if($this->session->login_type != 'admin'){
       $employee_idno = $this->db->escape($this->session->emp_idno);
       $sql .= " AND a.employee_idno = $employee_idno";
     }

     $sql .= " ORDER BY fullname ASC";
    return $this->db->query($sql);
  }

  public function get_schedule_overlap($from, $to, $dept, $emp = false, $self = false){
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $dept = $this->db->escape($dept);
    $sql = "SELECT * FROM hris_custom_schedule
     WHERE department_id = $dept AND enabled = 1 AND status != 'rejected'
     AND date_from <= $to AND date_to >= $from";
    if($emp){
      $emp = $this->db->escape($emp);
      $sql .= " AND employee_idno = $emp";
    }

    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }

    return $this->db->query($sql);
  }

  public function set_custom_workschedule($data){
    $this->db->insert('hris_custom_schedule',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_custom_workschedule($data,$id){
    $this->db->update('hris_custom_schedule',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_ws_status($id, $enabled = 0){
    $id = $this->db->escape($id);
    $enabled = $this->db->escape($enabled);

    $sql = "UPDATE hris_custom_schedule SET enabled = $enabled WHERE id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_ws_status2($data,$id){
    $this->db->update('hris_custom_schedule',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approve') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE hris_custom_schedule SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
