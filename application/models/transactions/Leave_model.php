<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//this is cash advance for transactions
class Leave_model extends CI_Model {
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

	public function getLeavePays_waiting_json($search){
    $requestData = $_REQUEST;
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
						DATE(a.date_created) as date_created
						FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.status = 'waiting' AND a.enabled = 1 AND c.contract_status = 'active'";

		if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
      $sql .= " AND d.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
    }

    if($search != ""){
			$sql .= $search;
    }

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY a.date_from ASC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['leave_type'];
			$nestedData[] = $row['date_from'];
			$nestedData[] = $row['date_to'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = $row['number_of_days'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Waiting for Approval</span></center>';
			$buttons = '';
			$buttons .= (approve_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_approved" data-num_days = "'.$row['number_of_days'].'" data-leave_cat = "'.$row['leave_cat'].'" data-emp_id = "'.$row['employee_idno'].'" data-approvedid = "'.$row['id'].'" style = "width:75px;">Approved</button>'
			: '';

			$buttons .=
			'
				<a href="'.base_url("transactions/Leave/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
					<button class="btn btn-primary btn-sm" style = "width:75px;">Edit</button>
				</a>
			';
			// reject access
			if(reject_access($this->access_ids)){
				$buttons .=
				'
					<button class="btn btn-danger btn_reject_modal" style = "width:75px;"
						id="delete-btn'.$row['id'].'"
						data-id="'.$row['id'].'"
						data-reject_id = "'.en_dec('en', $row['id']).'"
					>
						Reject
					</button>
				';
			}

			// delete for leave owner only
			if($this->session->login_type != 'admin' && $this->session->emp_idno = $row['employee_idno']){
        $buttons .=
        '
					<button class="btn btn-danger btn_del_leave" style = "width:75px;"
						id="delete-btn'.$row['id'].'"
						data-id="'.$row['id'].'"
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

	public function getLeavePays_approved_json($search){
    $requestData = $_REQUEST;

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
						DATE(a.date_created) as date_created
						FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.status = 'approved' AND a.enabled = 1 AND c.contract_status = 'active'";

		if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
      $sql .= " AND d.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
    }

		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
			$sql.=" ORDER BY a.date_from ASC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
				$nestedData[] = $row['emp_name'];
				$nestedData[] = $row['leave_type'];
				$nestedData[] = $row['date_from'];
				$nestedData[] = $row['date_to'];
				$nestedData[] = $row['date_created'];
				$nestedData[] = $row['number_of_days'];
				$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-info">Approved</span></center>';
				$buttons = '';
				$buttons .= (certify_access($this->access_ids))
				? '<button class="btn btn-sm btn-info btn_certify"
					data-num_days = "'.$row['number_of_days'].'"
					data-leave_cat = "'.$row['leave_cat'].'"
					data-emp_id = "'.$row['employee_idno'].'"
					data-certifyid = "'.$row['id'].'" style = "width:75px;">Certify</button>'
				: '';

				$buttons .=
				'
					<a href="'.base_url("transactions/Leave/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
						<button class="btn btn-primary btn-sm" style = "width:75px;">View</button>
					</a>

				';

				if(reject_access($this->access_ids)){
					$buttons .=
					'
						<button class="btn btn-danger btn_reject_modal" style = "width:75px;"
							id="delete-btn'.$row['id'].'"
							data-id="'.$row['id'].'"
							data-reject_id = "'.en_dec('en',$row['id']).'"
						>
							Reject
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

	public function getLeavePays_certified_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
      3 => 'status'
		);


		$sql = "SELECT a.id, a.employee_idno, a.leave_type,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.date_from, a.date_to, a.contact_number_leave, a.status,
						e.description as leave_type, a.number_of_days,
						DATE(a.date_created) as date_created
						FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.status = 'certified' AND a.enabled = 1 AND c.contract_status = 'active'";

		if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
      $sql .= " AND d.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
    }

		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
			$sql.=" ORDER BY a.date_from ASC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
				$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-success">Certified</span></center>';

				$nestedData[] =
        '
          <center>
          	<a href="'.base_url("transactions/Leave/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
							<button class="btn btn-primary btn-sm" style = "width:75px;">View</button>
						</a>
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

	public function getCAByID($id) {
		$sql = "SELECT a.*, a.id as leave_id, d.deptId FROM leave_tran a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN leaves e ON a.leave_type = e.leaveid
						WHERE a.id = ? AND a.enabled = 1 AND c.contract_status = 'active'";
		$data = array($id);
		return $this->db->query($sql,$data);
	}

	public function getEmpID(){
		$sql = "SELECT * FROM employee_record WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function get_leave_type($id = false){
		$sql = "SELECT * FROM leaves WHERE enabled = 1";
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

	public function get_remaining_leave($id){
		$emp_id = $this->db->escape($id);
    $sql = "SELECT b.emp_leave FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      WHERE contract_status = 'active' AND a.enabled = 1 AND a.employee_idno = $emp_id";

    return $this->db->query($sql);
  }

	public function get_workschedule($id){
		$emp_id = $this->db->escape($id);
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

	public function create($data) {
		$this->db->insert('leave_tran',$data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function update($data) {
			$sql = "UPDATE leave_tran SET employee_idno = ?,leave_type = ?,date_from = ?,date_to = ?,number_of_days = ?,contact_number_leave = ?,comment = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function update_new($id,$data){
		$this->db->update('leave_tran', $data, array('id' => $id));
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function updateLeaveStatus($data,$update){
		if($update == "certified_by"){
			$sql = "UPDATE employee_record a
			INNER JOIN contract b ON a.id = b.contract_emp_id
			INNER JOIN leave_tran c ON a.employee_idno = c.employee_idno
			SET c.status = ?, b.emp_leave = ?, ".$update." = ? WHERE c.id = ?
			AND contract_status = 'active' AND c.enabled = 1 AND a.enabled = 1";
		}

		if($update == "approved_by"){
			$sql = "UPDATE leave_tran SET status = ?, ".$update." = ? WHERE id = ?";
		}
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approved') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE leave_tran SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

	public function destroy($data) {
		$sql = "UPDATE leave_tran SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
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

	public function reject($data,$id){
    $this->db->update('leave_tran',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

}
