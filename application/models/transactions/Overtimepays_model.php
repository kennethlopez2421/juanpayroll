<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//this is cash advance for transactions
class Overtimepays_model extends CI_Model {
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

	public function getOtPays_waiting_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'purpose',
      2 => 'minutes_of_overtime',
			3 => 'date_created',
      4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, a.minutes_of_overtime,
						CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.purpose, a.status, DATE(a.date_created) as date_filed, a.date_rendered, a.type
						FROM overtime_pays a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON c.position_id = d.positionid
						LEFT JOIN department e ON d.deptId = e.departmentid
						WHERE a.status = 'waiting' AND a.enabled = 1 AND c.contract_status = 'active'";
		if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
			$sql .= " AND d.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
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

			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY a.employee_id ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();

		foreach( $query->result_array() as $row )
		{
			$nestedData=array();
			$nestedData[] =
			'
			<label class="container_label">
				<input type="checkbox" name = "ot_id_batch[]" class = "select waiting_select" value = "'.en_dec('en',$row['id']).'">
				<span class="checkmark"></span>
			</label>
			';
			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['minutes_of_overtime'];
			$nestedData[] = ucfirst($row['type']);
			$nestedData[] = $row['date_filed'];
			$nestedData[] = $row['date_rendered'];
			$nestedData[] = '<textarea name="" id="" class = "form-control" cols="30" rows="2" readonly>'.$row['purpose'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Waiting for approval</span></center>';

			$buttons = '';
      $buttons .= (approve_access($this->access_ids)) ? '<button class="btn btn-sm btn-info btn_approved" data-otwaitingid = "'.$row['id'].'" data-type = "'.en_dec('en',$row['type']).'" style = "width:75px;">Approved</button>' : '';
      $buttons .=
      '
				<a href="'.base_url("transactions/Overtimepays/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
					<button class="btn btn-primary btn-sm" style = "width:75px;">Edit</button>
				</a>
      ';
			$buttons .= (reject_access($this->access_ids)) ? '<button class = "btn btn-danger btn_reject_modal" id = "delete-btn'.$row['id'].'" data-id = "'.$row['id'].'" data-reject_id = "'.en_dec('en', $row['id']).'" style = "width:75px;" >Reject</button>' : '';

			// delete access for the owner of the work order ONLY
      if($this->session->login_type != 'admin' && $this->session->emp_idno = $row['employee_id']){
        $buttons .=
        '
          <button class="btn_del_ot btn btn-sm btn-danger" style = "width:75px;"
						id = "delete-btn'.$row['id'].'"
						data-id = "'.$row['id'].'"
          >
            Delete
          </button>
        ';
      }
			$nestedData[] =
      '
        <center>
					'.$buttons.'
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

	public function getOtPays_approved_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'purpose',
      2 => 'minutes_of_overtime',
			3 => 'date_created',
      4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, a.minutes_of_overtime,
						CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.purpose, a.status, DATE(a.date_created) as date_filed, a.date_rendered, a.type
						FROM overtime_pays a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON c.position_id = d.positionid
						LEFT JOIN department e ON d.deptId = e.departmentid
						WHERE a.status = 'approved' AND a.enabled = 1 AND c.contract_status = 'active'";

		if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
			$sql .= " AND d.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
		}

		if($this->session->position_lvl > supervisor_and_above() && $this->session->deptId != hr_id()){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }

		if($this->session->login_type != 'admin'){
      $employee_idno = $this->db->escape($this->session->emp_idno);
      $sql .= " AND b.employee_idno = $employee_idno";
    }



		// $totalData = $query->num_rows();
		// $totalFiltered = $query->num_rows();

		// if( !empty($requestData['search']['value']) ){
		// 	$sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
		// }

    if($search != ""){
			$sql .= $search;
    }

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
		// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
		//012819
		$sql.=" ORDER BY a.employee_id ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();

		foreach( $query->result_array() as $row )
		{
			$nestedData=array();
			$nestedData[] =
			'
			<label class="container_label">
				<input type="checkbox" name = "ot_id_batch[]" class = "select approved_select" value = "'.en_dec('en',$row['id']).'">
				<span class="checkmark"></span>
			</label>
			';
			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['minutes_of_overtime'];
			$nestedData[] = ucfirst($row['type']);
			$nestedData[] = $row['date_filed'];
			$nestedData[] = $row['date_rendered'];
			$nestedData[] = '<textarea cols="30" rows="2" class="form-control" readonly>'.$row['purpose'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-info">Approved</span></center>';
			$buttons = '';
			$buttons .= (certify_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_certify" data-certifyid = "'.$row['id'].'" data-type = "'.en_dec('en', $row['type']).'" style = "width:75px;">Certify</button>'
			: '' ;

			$buttons .=
			'
				<a href="'.base_url("transactions/Overtimepays/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
					<button class="btn btn-primary btn-sm" style = "width:75px;">View</button>
				</a>
			';
			$buttons .= (reject_access($this->access_ids)) ? '<button class = "btn btn-danger btn_reject_modal" id = "delete-btn'.$row['id'].'" data-id = "'.$row['id'].'" data-reject_id = "'.en_dec('en', $row['id']).'" style = "width:75px;" >Reject</button>' : '';
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

	public function getOtPays_certified_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'purpose',
      2 => 'minutes_of_overtime',
			3 => 'date_created',
      4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, a.minutes_of_overtime,
						CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.purpose, a.status, DATE(a.date_created) as date_filed, a.date_rendered, a.type
						FROM overtime_pays a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON c.position_id = d.positionid
						LEFT JOIN department e ON d.deptId = e.departmentid
						WHERE a.status = 'certified' AND a.enabled = 1 AND c.contract_status = 'active'";

		if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
			$sql .= " AND d.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
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
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY a.employee_id ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();

		foreach( $query->result_array() as $row )
		{
			$nestedData=array();

			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['minutes_of_overtime'];
			$nestedData[] = ucfirst($row['type']);
			$nestedData[] = $row['date_filed'];
			$nestedData[] = $row['date_rendered'];
			$nestedData[] = '<textarea cols="30" rows="2" class="form-control" readonly>'.$row['purpose'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-success">Certified</span></center>';

			$nestedData[] =
      '
        <center>
        	<a href="'.base_url("transactions/Overtimepays/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
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

	public function getEmpID(){
		$sql = "SELECT * FROM employee_record WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function getotpays_history_json($search){
		$requestData = $_REQUEST;

		$columns = array(
			0 => 'logs',
			1 => 'created_at',

		);


		$sql = "SELECT * FROM overtime_pays_history";


		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

		// if( !empty($requestData['search']['value']) ){
		// 	$sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
		// }

    if($search != ""){
      $sql .= "";
    }

		$totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
			$sql.=" ORDER BY created_at DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
			$data = array();

			foreach( $query->result_array() as $row )
			{
				$nestedData=array();

				$nestedData[] = $row['logs'];
				$nestedData[] = $row['created_at'];

				$data[] = $nestedData;
			}
			$json_data = array(

				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
			);
			return $json_data;
	}

	public function getOt_empInfo($id){
		$sql = "SELECT first_name, middle_name, last_name, employee_id FROM employee_record a LEFT JOIN overtime_pays b ON a.employee_idno = b.employee_id WHERE b.id = ?";
		$data = array($id);
		return $this->db->query($sql,$data);
	}

	public function getCAByDesc($data) {
		$sql = "SELECT * FROM cash_advance WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function getCAByID($data) {
		$sql = "SELECT a.*, d.deptId
		FROM overtime_pays a
		LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
		LEFT JOIN contract c ON b.id = c.contract_emp_id
		LEFT JOIN position d ON d.positionid = c.position_id
		WHERE a.id = ? AND a.enabled = 1 AND c.contract_status = 'active'";
		return $this->db->query($sql,$data);
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

	public function setOvertimePays_histroy($data){
		$this->db->insert('overtime_pays_history', $data);
	}

	public function create($data) {
		$this->db->insert('overtime_pays',$data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function update($data,$id) {
		$this->db->update('overtime_pays',$data,array('id' => $id));
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function updateOtStatus($data,$update,$type){
		$sql = "UPDATE overtime_pays SET status = ?, ".$update." = ? WHERE id = ?";
		$this->db->query($sql,$data);
		if($type == 'offset' && $data[0] == 'certified'){
			$ot_id = $this->db->escape($data[2]);
			$sql2 = "UPDATE employee_record a
			 INNER JOIN overtime_pays b ON a.employee_idno = b.employee_id
			 SET a.offset_bal = (a.offset_bal + b.minutes_of_overtime)
			 WHERE b.id = $ot_id AND b.status = 'certified'";
			$this->db->query($sql2);
		}
		return ($this->db->affected_rows() > 0)? true : false;
	}

	public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approved') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE overtime_pays SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

	public function destroy($data) {
		$sql = "UPDATE overtime_pays SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function reject($data,$id){
    $this->db->update('overtime_pays',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

	public function check_filed_ot($emp_idno,$date,$self = false){
		$emp_idno = $this->db->escape($emp_idno);
		$date = $this->db->escape($date);

		$sql = "SELECT * FROM overtime_pays WHERE enabled = 1
		 AND employee_id = $emp_idno AND date_rendered = $date
		 AND status != 'rejected'";
		if($self){
			$self = $this->db->escape($self);
			$sql .= " AND id != $self";
		}
		return $this->db->query($sql);
	}

}
