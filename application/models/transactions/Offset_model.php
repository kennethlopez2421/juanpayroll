<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Offset_model extends CI_Model {
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

  public function get_offset_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT c.*, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     e.description as dept, e.departmentid as deptId, DATE(c.updated_at) as date_filled
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN hris_offset c ON a.employee_idno = c.employee_idno
     INNER JOIN position d ON b.position_id = d.positionid
     INNER JOIN department e ON d.deptId = e.departmentid
     WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'
     AND c.enabled = 1 AND c.status = 'waiting'";

     if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
       $sql .= " AND e.departmentid IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
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
         $sql .= " AND (e.departmentid = $dept)";
         break;
       case 'divDateFiled':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (DATE(c.updated_at) BETWEEN $from AND $to)";
         break;
       case 'divDateRendered':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (DATE(c.date_rendered) BETWEEN $from AND $to)";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY c.updated_at ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $nestedData[] =
      '
      <label class="container_label">
        <input type="checkbox" name = "wo_id_batch[]" class = "select waiting_select" value = "'.en_dec('en',$row['id']).'">
        <span class="checkmark"></span>
      </label>
      ';
      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['date_filled'];
      $nestedData[] = $row['date_rendered'];
      $nestedData[] = ucfirst($row['offset_type']);
      $nestedData[] = '<span class="badge badge-pill badge-small badge-warning">'.ucfirst($row['status']).'</span>';
      $buttons = '';
      $buttons .= (approve_access($this->access_ids))
      ? '<button class="btn btn-info btn_status" data-status = "approved" data-offset_id = "'.en_dec('en', $row['id']).'" style  = "width:75px;">Approved</button>'
      : '';
      $buttons .=
      '
        <button class="btn btn-primary btn_edit_modal" style = "width:75px;"
          data-uid = "'.en_dec('en',$row['id']).'"
          data-deptId = "'.$row['deptId'].'"
          data-emp_idno = "'.$row['employee_idno'].'"
          data-date_rendered = "'.$row['date_rendered'].'"
          data-offset_min = "'.$row['offset_min'].'"
          data-offset_type = "'.$row['offset_type'].'"
          data-status = "'.$row['status'].'"
        >
          Edit
        </button>
        <button class="btn btn-danger btn_reject_modal" style = "width:75px;"
          data-delid = "'.en_dec('en',$row['id']).'"
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

  public function get_offset_approved_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT c.*, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     e.description as dept, e.departmentid as deptId, DATE(c.updated_at) as date_filled
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN hris_offset c ON a.employee_idno = c.employee_idno
     INNER JOIN position d ON b.position_id = d.positionid
     INNER JOIN department e ON d.deptId = e.departmentid
     WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'
     AND c.enabled = 1 AND c.status = 'approved'";

     if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
       $sql .= " AND e.departmentid IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
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
         $sql .= " AND (e.departmentid = $dept)";
         break;
       case 'divDateFiled':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (DATE(c.updated_at) BETWEEN $from AND $to)";
         break;
       case 'divDateRendered':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (DATE(c.date_rendered) BETWEEN $from AND $to)";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY c.updated_at ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $nestedData[] =
      '
      <label class="container_label">
        <input type="checkbox" name = "wo_id_batch[]" class = "select approved_select" value = "'.en_dec('en',$row['id']).'">
        <span class="checkmark"></span>
      </label>
      ';
      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['date_filled'];
      $nestedData[] = $row['date_rendered'];
      $nestedData[] = ucfirst($row['offset_type']);
      $nestedData[] = '<span class="badge badge-pill badge-small badge-info">'.ucfirst($row['status']).'</span>';
      $buttons = '';
      $buttons .= (certify_access($this->access_ids))
      ? '<button class="btn btn-info btn_status" data-status = "certified" data-offset_id = "'.en_dec('en', $row['id']).'" style  = "width:75px;">Certify</button>'
      : '';
      $buttons .=
      '
        <button class="btn btn-primary btn_edit_modal" style = "width:75px;"
          data-uid = "'.en_dec('en',$row['id']).'"
          data-deptId = "'.$row['deptId'].'"
          data-emp_idno = "'.$row['employee_idno'].'"
          data-date_rendered = "'.$row['date_rendered'].'"
          data-offset_min = "'.$row['offset_min'].'"
          data-offset_type = "'.$row['offset_type'].'"
          data-status = "'.$row['status'].'"
        >
          Edit
        </button>
        <button class="btn btn-danger btn_reject_modal" style = "width:75px;"
          data-delid = "'.en_dec('en',$row['id']).'"
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

  public function get_offset_certified_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT c.*, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     e.description as dept, e.departmentid as deptId, DATE(c.updated_at) as date_filled
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN hris_offset c ON a.employee_idno = c.employee_idno
     INNER JOIN position d ON b.position_id = d.positionid
     INNER JOIN department e ON d.deptId = e.departmentid
     WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'
     AND c.enabled = 1 AND c.status = 'certified'";

     if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
       $sql .= " AND e.departmentid IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
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
         $sql .= " AND (e.departmentid = $dept)";
         break;
       case 'divDateFiled':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (DATE(c.updated_at) BETWEEN $from AND $to)";
         break;
       case 'divDateRendered':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND (DATE(c.date_rendered) BETWEEN $from AND $to)";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY c.updated_at ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['date_filled'];
      $nestedData[] = $row['date_rendered'];
      $nestedData[] = ucfirst($row['offset_type']);
      $nestedData[] = '<span class="badge badge-pill badge-small badge-success">'.ucfirst($row['status']).'</span>';
      $buttons = '';
      $buttons .=
      '
        <button class="btn btn-primary btn_edit_modal" style = "width:75px;"
          data-uid = "'.en_dec('en',$row['id']).'"
          data-deptId = "'.$row['deptId'].'"
          data-emp_idno = "'.$row['employee_idno'].'"
          data-date_rendered = "'.$row['date_rendered'].'"
          data-offset_min = "'.$row['offset_min'].'"
          data-offset_type = "'.$row['offset_type'].'"
          data-status = "'.$row['status'].'"
        >
          View
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

  public function get_offset_bal($emp_idno){
    $emp_idno = $this->db->escape($emp_idno);
    $sql = "SELECT offset_bal FROM employee_record WHERE employee_idno = $emp_idno AND enabled = 1";
    return $this->db->query($sql);
  }

  public function get_all_pending_offset($emp_idno, $self = false){
    $emp_idno = $this->db->escape($emp_idno);
    $sql = "SELECT SUM(offset_min) as pending_offset FROM hris_offset WHERE employee_idno = $emp_idno
     AND (status = 'waiting' || status = 'approved') AND enabled = 1";

    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    return $this->db->query($sql);
  }

  public function get_worksched($emp_idno){
		$emp_idno = $this->db->escape($emp_idno);
		$sql = "SELECT c.work_sched, c.total_whours, c.total_bhours, c.sched_type
		 FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 INNER JOIN work_schedule c ON b.work_sched_id = c.id
		 WHERE b.contract_status = 'active' AND c.enabled = 1 AND b.enabled = 1
		 AND a.employee_idno = $emp_idno";
		return $this->db->query($sql);
	}

  public function get_timelog_history($emp_idno, $date){
		$date = $this->db->escape($date);
		$emp_idno = $this->db->escape($emp_idno);
		$sql = "SELECT * FROM (
      SELECT a.id as time_id, a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
      a.date as timelog_date, 'timelog' as type
      FROM time_record_summary_trial a WHERE a.date = $date AND a.employee_idno IN ($emp_idno) AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
      UNION
      SELECT b.id as time_id, b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
      b.date as timelog_date, 'work order' as type
      FROM work_order b WHERE b.date = $date AND b.employee_id IN ($emp_idno) AND b.status = 'certified'
      AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
    ) as timelog ORDER BY timelog_date ASC, time_in ASC, time_id ASC";
		return $this->db->query($sql);
	}

  public function get_graceperiod($type){
    $type = $this->db->escape($type);
    $sql = "SELECT minutes FROM hris_clockinout_settings
     WHERE type = $type AND status = 'on' AND enabled = 1";
    return $this->db->query($sql);
  }

  public function check_filed_offset($date,$type,$id,$self = false){
    $date = $this->db->escape($date);
    $type = $this->db->escape($type);
    $id = $this->db->escape($id);
    $sql = "SELECT * FROM hris_offset WHERE date_rendered = $date AND offset_type = $type
     AND employee_idno = $id AND enabled = 1 AND status != 'rejected'";
    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    return $this->db->query($sql);
  }

  public function set_offset($data){
    $this->db->insert('hris_offset',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_offset($data,$id){
    $this->db->update('hris_offset',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_offset_status($data,$id){
    $this->db->update('hris_offset',$data, array('id' => $id));
    $updated = ($this->db->affected_rows() > 0) ? true : false;
    if($updated && $data['status'] == 'certified'){
      $id = $this->db->escape($id);
      $sql = "UPDATE employee_record a INNER JOIN hris_offset b ON a.employee_idno = b.employee_idno
       SET a.offset_bal = (a.offset_bal - b.offset_min) WHERE b.status = 'certified' AND b.enabled = 1
       AND b.id = $id";
      $this->db->query($sql);
    }
    return $updated;
  }

  public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approved') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE hris_offset SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function reject_offset($data,$id){
    $this->db->update('hris_offset',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
