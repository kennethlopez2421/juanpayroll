<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Workorder_model extends CI_Model{

  public function __construct(){
    parent::__construct();
    if(isset($this->session->content_url)){
      $content_id = $this->model->get_url_content_id($this->session->content_url);
      $content_id = ($content_id->num_rows() > 0) ? $content_id->row()->id : 0;
      if(count((array)$this->session->get_position_access->access_func_nav) > 0){
        $this->access_ids = check_func_access($this->session->get_position_access->access_func_nav,$content_id);
        // $this->access_ids = $content_id;
      }else{
        // $this->access_ids = [];
        $this->access_ids = [];
      }
    }
  }

  public $access_ids;

  public function getWordOrder_json($search){
    $requestData = $_REQUEST;
		$columns = array(
			0 => 'employee_name',
			1 => 'date',
      2 => 'time',
      3 => 'status'
		);

		$sql = "SELECT a.id, a.date as work_order_date, b.employee_idno, a.start_time, a.end_time, a.status,
                  CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as employee_name,
                  DATE(a.updated_at) as date_filed,
                  CONCAT(a.start_time,'-',a.end_time) as work_order_time FROM work_order a
            LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
            LEFT JOIN work_order_itenerary c ON b.id = c.workorder_id
            LEFT JOIN contract d ON b.id = d.contract_emp_id
            LEFT JOIN position e ON d.position_id = e.positionid
            LEFT JOIN department f ON e.deptId = f.departmentid
            WHERE a.enabled = 1 AND b.enabled = 1 AND b.isActive = 1 AND a.status = 'waiting'
            AND d.contract_status = 'active'";

    if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
      $sql .= " AND e.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
    }

    if($this->session->position_lvl > supervisor_and_above() && $this->session->deptId != hr_id()){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }

    if($this->session->login_type != 'admin'){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }

    if($search != ""){
      $sql .= $search;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

		$sql.=" ORDER BY date_filed DESC, last_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
			$nestedData[] = $row['employee_name'];
			$nestedData[] = $row['date_filed'];
			$nestedData[] = $row['work_order_date'];
			$nestedData[] = date("H:i a",strtotime($row['start_time']))."-".date("H:i a",strtotime($row['end_time']));
      $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Waiting for approval</span></center>';

      $buttons = '';
      // approve access
      if(approve_access($this->access_ids)){
        $buttons .=
        '
          <button class="btn_approve_wo btn btn-info btn-sm" style = "width:75px;"
            data-apid = "'.$row['id'].'"
          >
            Approve
          </button>
        ';
      }

      $buttons .=
      '
        <form action = "'.base_url('transactions/Workorder/edit/'.en_dec('en',$this->session->userdata('token_session'))).'" class = "d-inline" method = "post">
          <input type="hidden" name = "wo_id" value = "'.$row['id'].'"/>
          <input type="hidden" name = "emp_idno" value = "'.$row['employee_idno'].'" />
          <button type = "submit" class="btn_view_app btn btn-sm btn-primary" style = "width:75px;"
            data-updateid = "'.$row['id'].'"
          >
            Edit
          </button>
        </form>
      ';
      // reject access
      if(reject_access($this->access_ids)){
        $buttons .=
        '
          <button class="btn_reject_modal btn btn-sm btn-danger" style = "width:75px;"
            data-deleteid = "'.$row['id'].'"
            data-reject_id = "'.en_dec('en', $row['id']).'"
          >
            Reject
          </button>
        ';
      }

      // delete access for the owner of the work order ONLY
      if($this->session->login_type != 'admin' && $this->session->emp_idno = $row['employee_idno']){
        $buttons .=
        '
          <button class="btn_del_wo_modal btn btn-sm btn-danger" style = "width:75px;"
            data-deleteid = "'.$row['id'].'"
            data-reject_id = "'.en_dec('en', $row['id']).'"
          >
            Delete
          </button>
        ';
      }

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

  public function getWordOrder_for_approval_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_name',
			1 => 'date',
      2 => 'time',
      3 => 'status'
		);


		$sql = "SELECT a.id, a.date as work_order_date, b.employee_idno, a.start_time, a.end_time, a.status,
                  CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as employee_name,
                  DATE(a.updated_at) as date_filed,
                  CONCAT(a.start_time,'-',a.end_time) as work_order_time FROM work_order a
            LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
            LEFT JOIN work_order_itenerary c ON b.id = c.workorder_id
            LEFT JOIN contract d ON b.id = d.contract_emp_id
            LEFT JOIN position e ON d.position_id = e.positionid
            LEFT JOIN department f ON e.deptId = f.departmentid
            WHERE a.enabled = 1 AND b.enabled = 1 AND b.isActive = 1 AND a.status = 'approved' AND d.contract_status = 'active'";

    if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
      $sql .= " AND e.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
    }

    if($this->session->position_lvl > supervisor_and_above() && $this->session->deptId != hr_id()){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }

    if($this->session->login_type != 'admin'){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }

    if($search != ""){
      $sql .= $search;
    }

    $query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;

		$sql.=" ORDER BY date_filed DESC, last_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
				$nestedData[] = $row['employee_name'];
				$nestedData[] = $row['date_filed'];
				$nestedData[] = $row['work_order_date'];
				$nestedData[] = date("H:i a",strtotime($row['start_time']))."-".date("H:i a",strtotime($row['end_time']));
        $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-info">Approved</span></center>';

        $buttons = '';
        $buttons .= (certify_access($this->access_ids)) ? '<button type = "button" class="btn_certify_wo btn btn-info btn-sm" data-cid = "'.$row['id'].'" style = "width:75px;">Certify</button>' : '';
        $buttons .=
        '
          <form action = "'.base_url('transactions/Workorder/edit/'.en_dec('en',$this->session->userdata('token_session'))).'" class = "d-inline approve_wo_form" method = "post">
            <input type="hidden" name = "wo_id" value = "'.$row['id'].'"/>
            <input type="hidden" name = "emp_idno" value = "'.$row['employee_idno'].'" />
            <button type = "submit" class="btn_view_app btn_view_wo btn btn-sm btn-primary" style = "width:75px;" data-updateid = "'.$row['id'].'">View</button>
          </form>
        ';

        $buttons .= (reject_access($this->access_ids)) ? '<button class="btn_reject_modal btn btn-sm btn-danger" style = "width:75px;" data-deleteid = "'.$row['id'].'" data-reject_id = "'.en_dec('en',$row['id']).'">Reject</button>' : '';

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

  public function getWordOrder_for_certification_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_name',
			1 => 'date',
      2 => 'time',
      3 => 'status'
		);


		$sql = "SELECT a.id, a.date as work_order_date, b.employee_idno, a.start_time, a.end_time, a.status,
                  CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as employee_name,
                  DATE(a.updated_at) as date_filed,
                  CONCAT(a.start_time,'-',a.end_time) as work_order_time FROM work_order a
            LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
            LEFT JOIN work_order_itenerary c ON b.id = c.workorder_id
            LEFT JOIN contract d ON b.id = d.contract_emp_id
            LEFT JOIN position e ON d.position_id = e.positionid
            LEFT JOIN department f ON e.deptId = f.departmentid
            WHERE a.enabled = 1 AND b.enabled = 1 AND b.isActive = 1 AND a.status = 'certified' AND d.contract_status = 'active'";

    if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
      $sql .= " AND e.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
    }

    if($this->session->position_lvl > supervisor_and_above() && $this->session->deptId != hr_id()){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }

    if($this->session->login_type != 'admin'){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }

    if($search != ""){
      $sql .= $search;
    }

    $query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;

		$sql.=" ORDER BY date_filed DESC, last_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
  	$data = array();

    // return $this->db->last_query();
		foreach( $query->result_array() as $row )
		{
			$nestedData=array();
      if($row['status'] == 'waiting'){
        $status = '<center><h5 class = "text-warning">Waiting for approval</h5></center>';
      }else if($row['status'] == 'approved'){
        $status = '<center><h5 class = "text-info">Approved</h5></center>';
      }else{
        $status = '<center><span class="badge badge-pill badge-sm badge-success">Certified</span></center>';
      }
			$nestedData[] = $row['employee_idno'];
			$nestedData[] = $row['employee_name'];
			$nestedData[] = $row['date_filed'];
			$nestedData[] = $row['work_order_date'];
			$nestedData[] = date("H:i a",strtotime($row['start_time']))."-".date("H:i a",strtotime($row['end_time']));
      $nestedData[] = ($status);

			$nestedData[] =
      '
        <center>
			  <form action = "'.base_url('transactions/Workorder/edit/'.en_dec('en',$this->session->userdata('token_session'))).'" class = "d-inline" method = "post">
          <input type="hidden" name = "wo_id" value = "'.$row['id'].'"/>
          <input type="hidden" name = "emp_idno" value = "'.$row['employee_idno'].'" />
          <button type = "submit" class="btn_view_app btn btn-sm btn-primary" style = "width:75px;" data-updateid = "'.$row['id'].'">View</button>
        </form>
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

  public function getEmployee(){
    $sql = "SELECT * FROM employee_record WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function getWorkOrder($id){
    $sql = "SELECT a.*, a.id as wo_id, d.deptId
            FROM work_order a
            LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
            LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
            WHERE a.enabled = 1 AND b.enabled = 1 AND c.contract_status = 'active' AND a.id = ?";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function getItinerary($wo_id){
    $sql = "SELECT * FROM work_order_itenerary WHERE workorder_id = ? AND enabled = 1";
    $data = array($wo_id);
    return $this->db->query($sql,$data);
  }

  public function setWorkOrder($data){
    $this->db->insert('work_order',$data);
    return ($this->db->affected_rows() != 1) ? false : $this->db->insert_id();
  }

  public function setItinerary($data){
    $this->db->insert('work_order_itenerary', $data);
    return ($this->db->affected_rows() != 1) ? false : true;
  }

  public function updateworkorder_status($data,$update){
    $sql = "UPDATE work_order SET status = ?, ".$update." = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approved') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE work_order SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function updateworkorder($data){
    $sql = "UPDATE work_order SET date = ?, start_time = ?, end_time = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function updateItinerary($data){
    $sql = "UPDATE work_order_itenerary SET location = ?, contact_person = ?, contact_num = ?, purpose = ?, notes = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function endis_workOrder($data){
    $sql = "UPDATE work_order SET enabled = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0)? true : false;
  }

  public function endis_itinerary($data){
    $sql = "UPDATE work_order_itenerary SET enabled = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0)? true : false;
  }

  public function get_workschedule($id){
    $id = $this->db->escape($id);
    $sql = "SELECT work_sched, total_whours, total_bhours
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN work_schedule c ON b.work_sched_id = c.id
      WHERE a.enabled = 1 AND b.contract_status = 'active' AND a.employee_idno = $id";

    return $this->db->query($sql);
  }

  public function get_emp_by_dept($deptId){
    $deptId = $this->db->escape($deptId);
		$sql = "SELECT a.id as emp_id, a.employee_idno, d.work_sched, d.total_whours, d.total_bhours, d.sched_type,
						CONCAT(a.last_name, ',', a.first_name, ' ', a.middle_name) as fullname
						FROM employee_record a
            LEFT JOIN contract b ON a.id = b.contract_emp_id
						LEFT JOIN position c ON b.position_id = c.positionid
						LEFT JOIN work_schedule d ON b.work_sched_id = d.id
						WHERE a.enabled = 1 AND b.contract_status = 'active' AND c.deptId = $deptId AND d.enabled = 1";
		// $data = array($deptId);
    if($this->session->position_lvl >= supervisor_and_above() && $this->session->deptId != hr_id()){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND a.employee_idno = $employee_idno";
    }

    if($this->session->login_type != 'admin'){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND a.employee_idno = $employee_idno";
    }

    $sql .= " ORDER BY fullname ASC, a.last_name ASC, a.middle_name ASC";
		return $this->db->query($sql);
    // return $this->db->last_query();
	}

  public function check_time_overlap($id,$date,$in,$out){
    $id = $this->db->escape($id);
    $date = $this->db->escape($date);
    $in = $this->db->escape($in);
    $out = $this->db->escape($out);

    $sql = "SELECT * FROM work_order WHERE enabled = 1 AND status != 'rejected'
     AND date = $date AND start_time < $out AND end_time > $in AND employee_id = $id";
    return $this->db->query($sql);
  }

  public function reject($data,$id){
    $this->db->update('work_order',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
