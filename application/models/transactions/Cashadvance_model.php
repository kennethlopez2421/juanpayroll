<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//this is cash advance for transactions
class Cashadvance_model extends CI_Model {
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

	public function getCaPays_waiting_json($search){
    $requestData = $_REQUEST;
		// return $this->access_ids;
		$columns = array(
			0 => 'employee_id',
			1 => 'date_of_file',
      2 => 'date_of_effectivity',
      3 => 'reason',
			4 => 'amount',
			5 => 'rate',
			6 => 'term',
			7 => 'status'
		);

		$sql = "SELECT a.id, a.employee_id, a.date_of_file,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.amount, a.status, a.reason, a.date_of_effectivity, a.terms, a.rate
						FROM cash_advance_tran a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
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
		$sql.=" ORDER BY a.date_of_file DESC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['date_of_file'];
			$nestedData[] = $row['date_of_effectivity'];
			$nestedData[] = number_format($row['amount'],2);
			$nestedData[] = '<textarea cols="30" rows="2" class = "form-control" readonly>'.$row['reason'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Waiting for Approval</span></center>';
			$buttons = '';
			$buttons .= (approve_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_approved" data-caid = "'.$row['id'].'" style = "width:75px;">Approved</button>'
			: '';
			$buttons .=
			'
				<a href="'.base_url("transactions/Cashadvance/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
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
						data-reject_id = "'.en_dec('en',$row['id']).'"
					>
						Reject
					</button>
				';
			}

			// delete access for the owner of the cashadvance ONLY
      if($this->session->login_type != 'admin' && $this->session->emp_idno = $row['employee_idno']){
        $buttons .=
        '
					<button class="btn btn-danger btn_del_ca" style = "width:75px;"
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

	public function getCaPays_approved_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'date_of_file',
      2 => 'date_of_effectivity',
      3 => 'reason',
			4 => 'amount',
			5 => 'rate',
			6 => 'terms',
			7 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, a.date_of_file,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.amount, a.status, a.reason, a.date_of_effectivity, a.terms, a.rate
						FROM cash_advance_tran a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
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
		$sql.=" ORDER BY a.date_of_file DESC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

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
			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['date_of_effectivity'];
			$nestedData[] = $row['date_of_file'];
			$nestedData[] = number_format($row['amount'],2);
			$nestedData[] = '<textarea cols="30" rows="2" readonly class="form-control">'.$row['reason'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge sm badge-info">Approved</span></center>';
			$buttons = '';
			$buttons .= (certify_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_certify" data-certifyid = "'.$row['id'].'" style = "width:75px;">Certify</button>'
			: '';

			$buttons .=
			'
				<a href="'.base_url("transactions/Cashadvance/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
					<button class="btn btn-primary btn-sm" style = "width:75px;">View</button>
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

	public function getCaPays_certified_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'date_of_file',
      2 => 'date_of_effectivity',
      3 => 'reason',
			4 => 'amount',
			5 => 'rate',
			6 => 'terms',
			7 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, a.date_of_file,
						CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as emp_name,
						a.amount, a.status, a.reason, a.date_of_effectivity, a.terms, a.rate
						FROM cash_advance_tran a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
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
		$sql.=" ORDER BY a.date_of_file DESC, emp_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
		$data = array();

		foreach( $query->result_array() as $row )
		{
			$nestedData=array();

			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['date_of_file'];
			$nestedData[] = $row['date_of_effectivity'];
			$nestedData[] = number_format($row['amount'],2);
			$nestedData[] = '<textarea readonly cols="30" rows="2" class="form-control">'.$row['reason'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-success">Certified</span></center>';

			$nestedData[] =
      '
        <center>
        	<a href="'.base_url("transactions/Cashadvance/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
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

	public function getCAByDesc($data) {
		$sql = "SELECT * FROM cash_advance WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function get_employee_w_active_contract(){
		$sql = "SELECT a.id, a.employee_idno,
						CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as emp_name
						FROM employee_record a LEFT JOIN contract b ON a.id = b.contract_emp_id
						WHERE a.enabled = 1 AND b.contract_status = 'active' ORDER BY a.last_name ASC, a.first_name ASC";
		return $this->db->query($sql);
	}

	public function get_employee($id){
		$sql = "SELECT b.sal_cat,
						CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as emp_name,
						a.id, b.position_id
						FROM employee_record a
						LEFT JOIN contract b ON a.id = b.contract_emp_id
						WHERE b.contract_status = 'active' AND a.enabled = 1 AND a.employee_idno = ?";
		$data = array($id);
		return $this->db->query($sql,$data);
	}

	public function get_basic_pay(){
		$sql = "SELECT * FROM salarycat WHERE enabled = 1 AND salarycatid = 1";
		return $this->db->query($sql);
	}

	public function getPaymentScheme(){
		$sql = "SELECT * FROM cash_advance_payment_scheme WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function set_cadvance($data){
		$this->db->insert("cash_advance_tran",$data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function getEmpID(){
		$sql = "SELECT * FROM employee_record WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function getCAByID($id) {
		$sql = "SELECT *, a.id as ca_id FROM cash_advance_tran a
						LEFT JOIN employee_record b
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						ON a.employee_id = b.employee_idno  WHERE a.id = ? AND a.enabled = 1";
		$data = array($id);
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO cash_advance_tran (employee_id,date_of_file,date_of_effectivity,amount,reason,terms,rate,status) VALUES (?,?,?,?,?,?,?,'waiting')";
		// $data = array($employee_id_no,$date_of_file,$date_of_effectivity,$amount,$reason,$terms,$rate);
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE cash_advance_tran
						SET employee_id = ?, date_of_file = ?, date_of_effectivity = ?, amount = ? ,
						reason = ?, terms = ?, rate = ? WHERE id = ?";
		$this->db->query($sql, $data);
		// return $this->db->last_query();
		return ($this->db->affected_rows() > 0)? true : false;

	}

	public function updateCaStatus($data,$update){
		$sql = "UPDATE cash_advance_tran SET status = ?, ".$update." = ? WHERE  id = ?";
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approved') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE cash_advance_tran SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

	public function destroy($data) {
		$sql = "UPDATE cash_advance_tran SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function reject($data,$id){
    $this->db->update('cash_advance_tran',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

}
