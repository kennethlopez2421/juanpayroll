<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee_leave_model extends CI_Model {

  public function getLeavePays_waiting_json($search){
    $requestData = $_REQUEST;
    $emp_idno = $this->db->escape($this->session->emp_idno);
    $search = json_decode($search);
		// return $this->access_ids;
		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
      3 => 'status'
		);

		$sql = "SELECT a.id, a.employee_idno, a.leave_type,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.date_from, a.date_to, a.contact_number_leave, a.status,
						e.description as leave_type, a.leave_type as leave_cat, a.number_of_days,
						DATE(a.date_created) as date_created, a.comment as reason, a.paid
						FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.status = 'waiting' AND a.enabled = 1 AND c.contract_status = 'active'
            AND b.employee_idno = $emp_idno";

    if($search->from != "" && $search->to != ""){
      $date_from = $this->db->escape($search->from);
      $date_to = $this->db->escape($search->to);

      if($search->from != "" && $search->to != ""){
        $sql .= " AND DATE(a.date_created) BETWEEN $date_from AND $date_to";
      }
    }

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY a.date_created DESC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();
    $remaining_leaves = json_decode($this->get_remaining_leave()->row()->emp_leave);

		foreach( $query->result_array() as $row )
		{

      ### get remaining leave ###
      $leave_type = array();
      foreach($remaining_leaves as $leave){
        if($leave->id == $row['leave_cat']){
          $leave_type[] = $leave;
        }
      }
      $remaining = (count((array)$leave_type) > 0) ? $leave_type[0]->days : 0 ;

			$nestedData=array();
			$nestedData[] = $row['employee_idno'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['leave_type'];
			$nestedData[] = $row['date_from'];
			$nestedData[] = $row['date_to'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = $row['number_of_days'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Waiting for Approval</span></center>';
			$buttons = '';

			$buttons .=
			'
        <button class="btn btn-primary btn-sm btn_edit_leave" style = "width:75px;"
          data-uid = "'.$row['id'].'"
          data-leave_type = "'.$row['leave_cat'].'"
          data-remaining = "'.$remaining.'"
          data-date_from = "'.$row['date_from'].'"
          data-date_to = "'.$row['date_to'].'"
          data-reason = "'.$row['reason'].'"
          data-contact = "'.$row['contact_number_leave'].'"
          data-paid = "'.$row['paid'].'"
          data-status = "'.$row['status'].'"
        >
          Update
        </button>
        <button class="btn btn-danger btn-sm btn_del_leave" style = "width:75px;"
          data-delid = "'.$row['id'].'"
        >
          Delete
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

	public function getLeavePays_approved_json($search){
    $requestData = $_REQUEST;
    $emp_idno = $this->db->escape($this->session->emp_idno);
    $search = json_decode($search);
		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
      3 => 'status'
		);

		$sql = "SELECT a.id, a.employee_idno, a.leave_type,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.date_from, a.date_to, a.contact_number_leave, a.status,
						e.description as leave_type, a.leave_type as leave_cat, a.number_of_days,
						DATE(a.date_created) as date_created, a.comment as reason, a.paid
						FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.status = 'approved' AND a.enabled = 1 AND c.contract_status = 'active'
            AND b.employee_idno = $emp_idno";

    if($search->from != "" && $search->to != ""){
      $date_from = $this->db->escape($search->from);
      $date_to = $this->db->escape($search->to);

      if($search->from != "" && $search->to != ""){
        $sql .= " AND DATE(a.date_created) BETWEEN $date_from AND $date_to";
      }
    }

    $query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY a.date_created DESC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();
    $remaining_leaves = json_decode($this->get_remaining_leave()->row()->emp_leave);
		foreach( $query->result_array() as $row )
		{

      ### get remaining leave ###
      $leave_type = array();
      foreach($remaining_leaves as $leave){
        if($leave->id == $row['leave_cat']){
          $leave_type[] = $leave;
        }
      }
      $remaining = (count((array)$leave_type) > 0) ? $leave_type[0]->days : 0 ;

			$nestedData=array();
			$nestedData[] = $row['employee_idno'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['leave_type'];
			$nestedData[] = $row['date_from'];
			$nestedData[] = $row['date_to'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = $row['number_of_days'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-info">Approved</span></center>';
			$buttons = '';

			$buttons .=
			'
        <button class="btn btn-primary btn-sm btn_edit_leave" style = "width:75px;"
          data-uid = "'.$row['id'].'"
          data-leave_type = "'.$row['leave_cat'].'"
          data-remaining = "'.$remaining.'"
          data-date_from = "'.$row['date_from'].'"
          data-date_to = "'.$row['date_to'].'"
          data-reason = "'.$row['reason'].'"
          data-contact = "'.$row['contact_number_leave'].'"
          data-paid = "'.$row['paid'].'"
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

	public function getLeavePays_certified_json($search){
    $requestData = $_REQUEST;
    $emp_idno = $this->db->escape($this->session->emp_idno);
    $search = json_decode($search);
		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
      3 => 'status'
		);


		$sql = "SELECT a.id, a.employee_idno, a.leave_type,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.date_from, a.date_to, a.contact_number_leave, a.status,
						e.description as leave_type, a.leave_type as leave_cat, a.number_of_days,
						DATE(a.date_created) as date_created, a.comment as reason, a.paid
						FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.status = 'certified' AND a.enabled = 1 AND c.contract_status = 'active'
            AND b.employee_idno = $emp_idno";

    if($search->from != "" && $search->to != ""){
      $date_from = $this->db->escape($search->from);
      $date_to = $this->db->escape($search->to);

      if($search->from != "" && $search->to != ""){
        $sql .= " AND DATE(a.date_created) BETWEEN $date_from AND $date_to";
      }
    }

    $query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY a.date_created DESC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();
    $remaining_leaves = json_decode($this->get_remaining_leave()->row()->emp_leave);
		foreach( $query->result_array() as $row )
		{
      ### get remaining leave ###
      $leave_type = array();
      foreach($remaining_leaves as $leave){
        if($leave->id == $row['leave_cat']){
          $leave_type[] = $leave;
        }
      }
      $remaining = (count((array)$leave_type) > 0) ? $leave_type[0]->days : 0 ;
			$nestedData=array();

			$nestedData[] = $row['employee_idno'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['leave_type'];
			$nestedData[] = $row['date_from'];
			$nestedData[] = $row['date_to'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = $row['number_of_days'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-success">Certified</span></center>';

			$nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn-sm btn_edit_leave" style = "width:75px;"
            data-uid = "'.$row['id'].'"
            data-leave_type = "'.$row['leave_cat'].'"
            data-remaining = "'.$remaining.'"
            data-date_from = "'.$row['date_from'].'"
            data-date_to = "'.$row['date_to'].'"
            data-reason = "'.$row['reason'].'"
            data-contact = "'.$row['contact_number_leave'].'"
            data-paid = "'.$row['paid'].'"
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

  public function getLeavePays_rejected_json($search){
    $requestData = $_REQUEST;
    $emp_idno = $this->db->escape($this->session->emp_idno);
    $search = json_decode($search);
		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
      3 => 'status'
		);


		$sql = "SELECT a.id, a.employee_idno, a.leave_type,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.date_from, a.date_to, a.contact_number_leave, a.status,
						e.description as leave_type, a.leave_type as leave_cat, a.number_of_days,
						DATE(a.date_created) as date_created, a.comment as reason, a.paid, a.reject_reason
						FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.status = 'rejected' AND a.enabled = 1 AND c.contract_status = 'active'
            AND b.employee_idno = $emp_idno";

    if($search->from != "" && $search->to != ""){
      $date_from = $this->db->escape($search->from);
      $date_to = $this->db->escape($search->to);

      if($search->from != "" && $search->to != ""){
        $sql .= " AND DATE(a.date_created) BETWEEN $date_from AND $date_to";
      }
    }

    $query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY a.date_created DESC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();
		foreach( $query->result_array() as $row )
		{
			$nestedData=array();

			$nestedData[] = $row['employee_idno'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['leave_type'];
			$nestedData[] = $row['date_from'];
			$nestedData[] = $row['date_to'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = $row['number_of_days'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Rejected</span></center>';

			$nestedData[] =
      '
        <textarea cols="30" rows="3" class = "form-control">'.$row['reject_reason'].'</textarea>
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

  public function get_employee_leave_json($search){
    $requestData = $_REQUEST;
    $emp_id  = $this->db->escape($this->session->userdata('emp_idno'));
    $search = json_decode($search);

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      c.description as leave_type, b.date_from, b.date_to, b.contact_number_leave as contact,
      b.status, b.comment as reason, b.number_of_days, b.id, b.leave_type as leave_cat, b.paid,
      b.contact_number_leave, b.date_created
      FROM employee_record a
      INNER JOIN leave_tran b ON a.employee_idno = b.employee_idno
      LEFT JOIN leaves c ON b.leave_type = c.leaveid
      WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND a.employee_idno = $emp_id";

    if($search != ""){
      $date_from = $this->db->escape($search->from);
      $date_to = $this->db->escape($search->to);
      $status = $this->db->escape($search->status);

      if($search->from != "" && $search->to != ""){
        $sql .= " AND b.date_from >= $date_from AND b.date_to <= $date_to";
      }

      if($search->status != "all"){
        $sql .= " AND status = $status";
      }
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY b.date_from DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $status = "";

      ### status ###
      if($row['status'] == 'waiting'){
        $status = '<span class= "badge badge-pill badge-warning">Waiting for Approval</span>';
      }

      if($row['status'] == 'approved'){
        $status = '<span class="badge badge-pill badge-info">Approved</span>';
      }

      if($row['status'] == 'certified'){
        $status = '<span class="badge badge-pill badge-success">Certified</span>';
      }

      if($row['status'] == 'rejected'){
        $status = '<span class="badge badge-pill badge-danger">Rejected</span>';
      }
      ### get remaining leave ###
      $remaining_leaves = json_decode($this->get_remaining_leave()->row()->emp_leave);
      $leave_type = array();
      foreach($remaining_leaves as $leave){
        if($leave->id == $row['leave_cat']){
          $leave_type[] = $leave;
        }
      }
      $remaining = (count((array)$leave_type) > 0) ? $leave_type[0]->days : 0 ;

      ### buttons ###
      $buttons =
      '<center>
        <button class="btn btn-info btn-sm btn_edit_leave" style = "width:80px;"
          data-uid = "'.$row['id'].'"
          data-leave_type = "'.$row['leave_cat'].'"
          data-remaining = "'.$remaining.'"
          data-date_from = "'.$row['date_from'].'"
          data-date_to = "'.$row['date_to'].'"
          data-reason = "'.$row['reason'].'"
          data-contact = "'.$row['contact_number_leave'].'"
          data-paid = "'.$row['paid'].'"
        >
          <i class="fa fa-pencil mr-2"></i>Update
        </button>
        <button class="btn btn-danger btn-sm btn_del_leave" style = "width:80px;"
          data-delid = "'.$row['id'].'"
        >
          <i class="fa fa-trash mr-2"></i>Delete
        </button>
      </center>
      ';

      $nestedData[] = $row['leave_type'];
      $nestedData[] = $row['date_from']." - ".$row['date_to'];
      $nestedData[] = $row['date_created'];
      $nestedData[] = '<span class="float-right">'.$row['number_of_days'].'</span>';
      $nestedData[] = '<span class="float-right">'.$row['contact'].'</span>';
      $nestedData[] = '<center>'.$status.'</center>';
      $nestedData[] =  ($row['status'] == 'waiting') ? $buttons : "";


      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_employee_leave_json_rejected($search){
    $requestData = $_REQUEST;
    $emp_id  = $this->db->escape($this->session->userdata('emp_idno'));
    $search = json_decode($search);

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      c.description as leave_type, b.date_from, b.date_to, b.contact_number_leave as contact,
      b.status, b.comment as reason, b.number_of_days, b.id, b.leave_type as leave_cat, b.paid,
      b.contact_number_leave, b.reject_reason, b.date_created
      FROM employee_record a
      INNER JOIN leave_tran b ON a.employee_idno = b.employee_idno
      LEFT JOIN leaves c ON b.leave_type = c.leaveid
      WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND a.employee_idno = $emp_id";

    if($search != ""){
      $date_from = $this->db->escape($search->from);
      $date_to = $this->db->escape($search->to);
      $status = $this->db->escape($search->status);

      if($search->from != "" && $search->to != ""){
        $sql .= " AND b.date_from >= $date_from AND b.date_to <= $date_to";
      }

      if($search->status != "all"){
        $sql .= " AND status = $status";
      }
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY b.date_from DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $status = "";

      ### status ###
      if($row['status'] == 'rejected'){
        $status = '<span class="badge badge-pill badge-danger">Rejected</span>';
      }

      ### buttons ###

      $nestedData[] = $row['leave_type'];
      $nestedData[] = $row['date_from']." - ".$row['date_to'];
      $nestedData[] = $row['date_created'];
      $nestedData[] = '<span class="float-right">'.$row['number_of_days'].'</span>';
      $nestedData[] = '<span>'.$row['contact'].'</span>';
      $nestedData[] = '<center>'.$status.'</center>';
      $nestedData[] =  '<textarea name="" id="" cols="30" rows="3" class="form-control">'.$row['reject_reason'].'</textarea>';


      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_leave_type($id = false){
    $sql = "SELECT *, leaveid as leave_id, description as leave_type
    FROM leaves WHERE enabled = 1";
    if($id){
      $id = $this->db->escape($id);
      $sql .= " AND leaveid = $id";
    }
    $sql .= " ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function get_last_filled_leave($emp_idno, $type, $self = false){
    $emp_idno = $this->db->escape($emp_idno);
    $type = $this->db->escape($type);
    $sql = "SELECT DATE(date_created) as date_created FROM leave_tran
     WHERE employee_idno = $emp_idno AND leave_type = $type AND status != 'rejected'";

    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    $sql .= " ORDER BY date_to DESC LIMIT 1";
    return $this->db->query($sql);
  }

  public function get_remaining_leave(){
    $emp_id = $this->db->escape($this->session->userdata('emp_idno'));
    $sql = "SELECT b.emp_leave FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      WHERE contract_status = 'active' AND a.enabled = 1 AND a.employee_idno = $emp_id";

    return $this->db->query($sql);
  }

  public function get_workschedule(){
    $emp_id = $this->db->escape($this->session->userdata('emp_idno'));
    $sql = "SELECT c.work_sched FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN work_schedule c ON b.work_sched_id = c.id
      WHERE contract_status = 'active' AND a.enabled = 1 AND a.employee_idno = $emp_id";

    return $this->db->query($sql);
  }

  public function get_holiday($date){
    $date = $this->db->escape($date);
    $sql = "SELECT a.date FROM holidays_tran a WHERE enabled = 1 AND a.date = $date";
    return $this->db->query($sql);
  }

  public function set_employee_leave($data){
    $this->db->insert('leave_tran',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_employee_leave($data){
    $sql = "UPDATE contract a
      INNER JOIN employee_record b ON a.contract_emp_id = b.id
      SET emp_leave = ? WHERE a.employee_idno = ?";
    $this->db->query($sql,$data);

    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_leave($data){
    $sql = "UPDATE leave_tran SET leave_type = ?, number_of_days = ?, date_from = ?,
            date_to = ?, comment = ?, contact_number_leave = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_leave_status($data){
    $sql = "UPDATE leave_tran SET enabled = ? WHERE id = ?";
    $this->db->query($sql,$data);

    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function check_filed_leave($id,$from,$to,$update_id = false){
    $id = $this->db->escape($id);
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $update_id = $this->db->escape($update_id);

    $sql = "SELECT * FROM `leave_tran`
      WHERE (date_from <= $to)
      AND (date_to >= $from)
      AND employee_idno = $id AND enabled = 1
      AND status != 'rejected'";

    if($update_id){
      $sql .= " AND id != $update_id";
    }

    return $this->db->query($sql);
  }

  public function check_pending_leave($emp_id,$leave_type){
    $emp_id = $this->db->escape($emp_id);
    $leave_type = $this->db->escape($leave_type);
    $sql = "SELECT SUM(number_of_days) as pending FROM leave_tran
     WHERE employee_idno = $emp_id AND leave_type = $leave_type
     AND (status = 'waiting' || status = 'approved')
     AND enabled = 1 AND paid = 'with_pay'";
    return $this->db->query($sql);
  }

}
