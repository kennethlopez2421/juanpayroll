<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//this is cash advance for transactions
class Additionalpays_model extends CI_Model {
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

	public function getAdditionalPays($start,$length,$search,$ordrBy) {
		if($start != null && $length != null) {

			if($search != null){
				$sql = "SELECT ap.id,ap.status,ap.employee_id,ap.date_issued,
											ap.purpose,ap.amount,er.employee_idno,er.first_name,er.last_name
											FROM additional_pays as ap LEFT JOIN employee_record as er ON er.employee_idno = ap.employee_id
											LEFT JOIN contract c ON er.id = contract_emp_id
											LEFT JOIN hris_position d ON d.position_id = c.position_id
											LEFT JOIN department e ON d.deptId = e.departmentid
											WHERE ap.enabled = 1 AND ap.status = 'waiting' AND ap.purpose LIKE '%".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT ap.id,ap.status,ap.employee_id,ap.date_issued,ap.purpose,ap.amount,er.employee_idno,er.first_name,er.last_name FROM additional_pays as ap LEFT JOIN employee_record as er ON er.employee_idno = ap.employee_id WHERE ap.enabled = 1 AND ap.status = 'waiting' ORDER BY ".$ordrBy." LIMIT ".$start.",".$length." ";
			}
		}
		else {
			$sql = "SELECT ap.id,ap.status,ap.employee_id,ap.date_issued,ap.purpose,ap.amount,er.employee_idno,er.first_name,er.last_name FROM additional_pays as ap LEFT JOIN employee_record as er ON er.employee_idno = ap.employee_id WHERE ap.enabled = 1 AND ap.status = 'waiting' ";
		}

		return $this->db->query($sql);
	}

	public function getAdditionalPays_waiting_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'date_issued',
      2 => 'purpose',
      3 => 'amount',
			4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.date_issued, a.purpose, a.amount, a.status
						FROM additional_pays a
						INNER JOIN employee_record b ON a.employee_id = b.employee_idno
						INNER JOIN contract c ON b.id = c.contract_emp_id
						INNER JOIN hris_position d ON c.position_id = d.position_id
						WHERE a.status = 'waiting' AND a.enabled = 1 AND c.contract_status = 'active'
						AND b.enabled = 1 AND c.enabled = 1";

		// if($this->session->userdata('deptId') != hr_id() && $this->session->userdata('deptId') != 0){
    //   $sql .= " AND d.deptId IN (".$this->db->escape_like_str($this->session->userdata('dept_access')).")";
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
        <input type="checkbox" name = "wo_id_batch[]" class = "select waiting_select" value = "'.en_dec('en',$row['id']).'">
        <span class="checkmark"></span>
      </label>
      ';
			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['date_issued'];
			$nestedData[] = number_format($row['amount'],2);
			$nestedData[] = '<textarea cols="30" rows="2" readonly class="form-control">'.$row['purpose'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Waiting for Approval</span></center>';
			$buttons = '';
			$buttons .= (approve_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_approved mr-1" data-approvedid = "'.$row['id'].'" style = "width:75px;">Aprroved</button>'
			: '';
			$buttons .=
			'
				<a href="'.base_url("transactions/Additionalpays/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
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

			// delete access for the owner of the additional pay ONLY
      if($this->session->login_type != 'admin' && $this->session->emp_idno = $row['employee_idno']){
        $buttons .=
        '
					<button class="btn btn-danger btn_reject_modal" style = "width:75px;"
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

	public function getAdditionalPays_approved_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'date_issued',
      2 => 'purpose',
      3 => 'amount',
			4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.date_issued, a.purpose, a.amount, a.status
						FROM additional_pays a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
						WHERE a.status = 'approved' AND a.enabled = 1 AND c.contract_status = 'active'";

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
        <input type="checkbox" name = "wo_id_batch[]" class = "select approved_select" value = "'.en_dec('en',$row['id']).'">
        <span class="checkmark"></span>
      </label>
      ';
			$nestedData[] = $row['employee_id'];
			$nestedData[] = $row['emp_name'];
			$nestedData[] = $row['date_issued'];
			$nestedData[] = $row['amount'];
			$nestedData[] = '<textarea cols="30" rows="2" class="form-control" readonly>'.$row['purpose'].'</textarea>';
			$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-info">Approved</span></center>';
			$buttons = '';
			$buttons .= (certify_access($this->access_ids))
			? '<button class="btn btn-sm btn-info btn_certify" data-certifyid = "'.$row['id'].'" style = "width:75px;">Certify</button>'
			: '';
			$buttons .=
			'
				<a href="'.base_url("transactions/Additionalpays/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
					<button class="btn btn-primary btn-sm" style = "width:75px;">View</button>
				</a>
				<button class="btn btn-danger btn_reject_modal" style = "width:75px;"
				 id="delete-btn'.$row['id'].'"
				 data-id="'.$row['id'].'"
				 data-reject_id = "'.en_dec('en',$row['id']).'"
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

	public function getAdditionalPays_certified_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'employee_id',
			1 => 'date_issued',
      2 => 'purpose',
      3 => 'amount',
			4 => 'status'
		);


		$sql = "SELECT a.id, a.employee_id, CONCAT(b.first_name,' ',b.last_name) as emp_name,
						a.date_issued, a.purpose, a.amount, a.status
						FROM additional_pays a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN hris_position d ON c.position_id = d.position_id
						WHERE a.status = 'certified' AND a.enabled = 1 AND c.contract_status = 'active'";



		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

		// if( !empty($requestData['search']['value']) ){
		// 	$sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
		// }

    if($search != ""){
			$sql .= $search;
    }

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
				$nestedData[] = $row['date_issued'];
				$nestedData[] = $row['amount'];
				$nestedData[] = '<textarea cols="30" rows="2" readonly class="form-control">'.$row['purpose'].'</textarea>';
				$nestedData[] = '<center><span class="badge badge-pill badge-sm badge-success">Certified</span></center>';

				$nestedData[] =
        '
          <center>
          	<a href="'.base_url("transactions/AdditionalPays/edit/".en_dec("en",$this->session->userdata("token_session"))."/".$row['id']).'">
							<button class="btn btn-primary btn-sm" style = "width:75px;">View</button>
						</a>
          </center>
				';
				$nestedData[] = "";

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

	public function getCAByID($data) {
		$sql = "SELECT a.*, d.deptId
						FROM additional_pays a
						LEFT JOIN employee_record b ON a.employee_id = b.employee_idno
						LEFT JOIN contract c ON b.id = c.contract_emp_id
						LEFT JOIN position d ON d.positionid = c.position_id
						WHERE a.id = ? AND a.enabled = 1 AND c.contract_status = 'active'";
		return $this->db->query($sql,$data);
	}

	public function setAdditionalPay_history($data){
		$this->db->insert('additional_pays_history',$data);
	}

	public function create($data) {
		$this->db->insert("additional_pays", $data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function update($data) {
		$sql = "UPDATE additional_pays SET employee_id = ?, date_issued = ?, purpose = ?, amount = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function updateStatus($data,$update_col){
		$sql = "UPDATE additional_pays SET status = ?, ".$update_col." = ? WHERE id = ?";
	 	$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0)? true: false;
	}

	public function updateworkorder_batch_status($status,$data){
    $field = ($status == 'approved') ? 'approved_by' : 'certified_by';
    $status = $this->db->escape($status);
    $approver = $this->db->escape($this->session->emp_idno);
    // $data = $this->db->escape($data);
    $sql = "UPDATE additional_pays SET status = $status, $field = $approver WHERE id IN ($data)";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

	public function destroy($data) {
		$sql = "UPDATE additional_pays SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
		// return ($this->db->affected_rows() > 0)? true: false;
	}

	public function reject($data,$id){
    $this->db->update('additional_pays',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }


}
