<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//this is cash advance for transactions
class Salarydeduction_model extends CI_Model {
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

	public function getSalaryDeduction($start,$length,$search,$ordrBy) {
		if($start != null && $length != null) {

			if($search != null){
				$sql = "SELECT sd.id, sd.employee_idno, sd.deduct_category, sd.amount, sd.status, er.employee_idno, er.first_name,er.middle_name,er.last_name FROM salary_deduction as sd LEFT JOIN employee_record as er ON sd.employee_idno = er.employee_idno WHERE sd.enabled = 1 AND sd.status = 'waiting' AND sd.employee_idno LIKE '%".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT sd.id, sd.employee_idno, sd.deduct_category, sd.amount, sd.status, er.employee_idno, er.first_name,er.middle_name,er.last_name FROM salary_deduction as sd LEFT JOIN employee_record as er ON sd.employee_idno = er.employee_idno WHERE sd.enabled = 1 AND sd.status = 'waiting' ORDER BY ".$ordrBy." LIMIT ".$start.",".$length." ";
			}
		}
		else {
			$sql = "SELECT sd.id, sd.employee_idno, sd.deduct_category, sd.amount, sd.status, er.employee_idno, er.first_name,er.middle_name,er.last_name FROM salary_deduction as sd LEFT JOIN employee_record as er ON sd.employee_idno = er.employee_idno WHERE sd.enabled = 1 AND sd.status = 'waiting'";
		}
		return $this->db->query($sql);
	}

	public function getSdPays_waiting_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
			3 => 'date_created',
      4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_idno, a.deduct_category,
						CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.amount, a.status, e.description as deduct_category, a.date_created
						FROM salary_deduction a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
						LEFT JOIN deduction e ON a.deduct_category = e.deductionid
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
		$sql.=" ORDER BY a.employee_idno ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
			$nestedData[] = $row['deduct_category'];
			$nestedData[] = $row['amount'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Waiting for Approval</span></center>';
			$buttons = '';
			$buttons .= (approve_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_approved" data-sdid = "'.$row['id'].'" style = "width:75px;">Approved</button>'
			: '';
			$buttons .=
			'
				<a href="'.base_url("transactions/Salarydeduction/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
					<button class="btn btn-primary btn-sm" style = "width:75px;">Edit</button>
				</a>

			';

			// reject access
			if(reject_access($this->access_id)){
				$buttons .=
				'
					<button class="btn btn-danger btn_del_sd" style = "width:75px;"
						id="delete-btn'.$row['id'].'"
						data-id="'.$row['id'].'"
						data-reject_id = "'.en_dec('en',$row['id']).'"
					>
						Reject
					</button>
				';
			}

			// delete access for the owner of the sal deduct ONLY
      if($this->session->login_type != 'admin' && $this->session->emp_idno = $row['employee_idno']){
        $buttons .=
        '
					<button class="btn btn-danger btn_reject_modal" style = "width:75px;"
						id="delete-btn'.$row['id'].'"
						data-id="'.$row['id'].'"
						data-reject_id = "'.en_dec('en',$row['id']).'"
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

	public function getSdPays_approved_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
			3 => 'date_created',
      4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_idno, a.deduct_category,
						CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.amount, a.status, e.description as deduct_category, a.date_created
						FROM salary_deduction a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
						LEFT JOIN deduction e ON a.deduct_category = e.deductionid
						WHERE a.status = 'approved' AND a.enabled = 1 AND c.contract_status = 'active'";

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
		$sql.=" ORDER BY a.employee_idno ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
			$nestedData[] = $row['deduct_category'];
			$nestedData[] = $row['amount'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-info">Approved</span></center>';
			$buttons = '';
			$buttons .= (certify_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_certify" data-certifyid = "'.$row['id'].'" style = "width:75px;">Certify</button>'
			: '';
			$buttons .=
			'
				<a href="'.base_url("transactions/Salarydeduction/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
					<button class="btn btn-primary btn-sm" style = "width:75px;">View</button>
				</a>

			';
			
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

	public function getSdPays_certified_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'deduct_category',
      2 => 'amount',
			3 => 'date_created',
      4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_idno, a.deduct_category,
						CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.amount, a.status, e.description as deduct_category, a.date_created
						FROM salary_deduction a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
						LEFT JOIN deduction e ON a.deduct_category = e.deductionid
						WHERE a.status = 'certified' AND a.enabled = 1 AND c.contract_status = 'active'";

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
		$sql.=" ORDER BY a.employee_idno ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();

		foreach( $query->result_array() as $row )
		{
			$nestedData=array();

			$nestedData[] = $row['employee_idno'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['deduct_category'];
			$nestedData[] = $row['amount'];
			$nestedData[] = $row['date_created'];
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-success">Certified</span></center>';

			$nestedData[] =
      '
        <center>
        	<a href="'.base_url("transactions/Salarydeduction/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
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

	public function deductiondesc(){
		$sql = "SELECT * FROM deduction WHERE enabled = 1 ORDER BY description ASC";
		return $this->db->query($sql);
	}

	public function getCAByDesc($data) {
		$sql = "SELECT * FROM cash_advance WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function getCAByID($id) {
		$sql = "SELECT a.*, a.id as sal_deduct_id,
						e.description as deduct_category, d.deptId,
						e.deductionid
						FROM salary_deduction a
						LEFT JOIN employee_record b ON a.employee_idno = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						LEFT JOIN deduction e ON a.deduct_category = e.deductionid
						WHERE a.id = ? AND a.enabled = 1 AND c.contract_status = 'active'";
		$data = array($id);
		return $this->db->query($sql,$data);
	}

	public function getEmpID(){
		$sql = "SELECT * FROM employee_record WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function create($data) {
		$this->db->insert('salary_deduction', $data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function update($data) {
		$sql = "UPDATE salary_deduction SET employee_idno = ?, deduct_category = ?, amount = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function updateSdStatus($data,$update){
		$sql = "UPDATE salary_deduction SET status = ?, ".$update." = ? WHERE id = ?";
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approved') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE salary_deduction SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

	public function destroy($data) {
		$sql = "UPDATE salary_deduction SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function reject($data,$id){
    $this->db->update('salary_deduction',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

}
