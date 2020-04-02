<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Systemusers_model extends CI_Model {

	public function get_systemuser_json($search = ""){
		$requestData = $_REQUEST;

    $columns = array(
      0 => 'fullname',
      1 => 'position'
    );

		$sql = "SELECT CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname) as fullname,  b.position,
			a.enabled, a.user_id, a.position_id, a.username, a.user_fname, a.user_lname, a.user_mname, a.employee_idno
			FROM hris_users a
			INNER JOIN hris_position b ON a.position_id = b.position_id
			WHERE a.enabled = 1 AND b.enabled = 1";

		if($search != ""){
			$search = $this->db->escape('%'.$search.'%');
			$sql .= " AND (CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname) LIKE $search
											OR CONCAT(a.user_fname,' ',a.user_mname,'. ',a.user_lname) LIKE $search
											OR CONCAT(a.user_lname,' ',a.user_fname) LIKE $search
											OR CONCAT(a.user_fname,' ',a.user_lname) LIKE $search
											OR a.user_fname LIKE $search OR a.user_lname LIKE $search
											OR b.position LIKE $search)";
		}

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
    $totalFiltered = $totalData;

		$sql.=" ORDER BY b.hierarchy_lvl ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);

		$data = array();
		foreach($query->result_array() as $row){
			$nestedData = array();

			$nestedData[] = $row['fullname'];
			$nestedData[] = $row['position'];
			$nestedData[] = ($row['enabled'] == 1)
				? '<center><span class="badge badge-pill badge-success">Active</span></center>'
				: '<center><span class="badge badge-pill badge-danger">Inactive</span></center>';
			$nestedData[] =
			'
				<center>
					<button class="btn btn-primary btn_edit_sys" style = "width:80px;"
						data-uid = "'.$row['user_id'].'"
						data-username = "'.$row['username'].'"
						data-fname = "'.$row['user_fname'].'"
						data-lname = "'.$row['user_lname'].'"
						data-mname = "'.$row['user_mname'].'"
						data-position_id = "'.$row['position_id'].'"
						data-employee_idno = "'.$row['employee_idno'].'"
					>
						<i class="fa fa-pencil mr-1"></i>Edit
					</button>
					<button class="btn btn-danger btn_del_sys" style = "width:80px;"
						data-del_id = "'.en_dec('en',$row['user_id']).'"
						data-fullname = "'.$row['fullname'].'"
					>
						<i class="fa fa-trash mr-1"></i>Disable
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

	public function get_systemuser_username($username, $user_id = false){
		$sql = "SELECT * FROM hris_users WHERE enabled = 1 AND username = ?";
		$data = array($username);
		if($user_id){
			$sql .= " AND user_id != ?";
			$data = array($username,$user_id);
		}
		return $this->db->query($sql,$data);
	}

	public function set_system_user($data){
		$this->db->insert('hris_users',$data);
		return ($this->db->affected_rows() > 0) ? true: false;
	}

	public function update_system_user($data,$id){
		$this->db->update('hris_users',$data,array('user_id' => $id));
	}

	public function update_system_user_status($id,$status = 0){
		$sql = "UPDATE hris_users SET enabled = ? WHERE user_id = ?";
		$data = array($status,$id);
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0) ? true: false;
	}

	public function getSystemUsers($start,$length,$search) {

		if($start != null && $length != null) {
			if($search != null){
				$sql = "SELECT * FROM system_users WHERE enabled = 1 AND description LIKE '".$this->db->escape_like_str($search)."%' LIMIT ".$start.",".$length." ";
			}else{
				$sql = "SELECT * FROM system_users WHERE enabled = 1 LIMIT ".$start.",".$length." ";
			}
		}else {
			$sql = "SELECT * FROM system_users WHERE enabled = 1";
		}

		return $this->db->query($sql);
	}

	public function getSystemUsersByDesc($data) {
		$sql = "SELECT * FROM system_users WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO system_users(description,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE system_users SET description = ?, date_updated = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE system_users SET enabled = ? WHERE id = ?";
		$this->db->query($sql, $data);
	}

}
