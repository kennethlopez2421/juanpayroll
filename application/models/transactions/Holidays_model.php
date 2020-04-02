<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Holidays_model extends CI_Model {

  public function get_holidays_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'description',
			1 => 'holiday_type',
      2 => 'date'
		);


		$sql = "SELECT *, a.description as h_desc, b.description as holiday_type FROM holidays_tran a
            LEFT JOIN holidaytype b ON a.holiday_type = b.holidaytypeid
            WHERE a.enabled = 1 AND b.enabled = 1";

		// if($this->session->userdata('deptId') != 2 && $this->session->userdata('deptId') != 0){
		// 	$sql .= " AND d.deptId = ".$this->db->escape_like_str($this->session->userdata('deptId'));
		// }

		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

		// if( !empty($requestData['search']['value']) ){
		// 	$sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
		// }

    if($search != ""){
			$sql .= $search;
    }

		$totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
			$sql.=" ORDER BY a.description ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
			$data = array();

			foreach( $query->result_array() as $row )
			{
				$nestedData=array();

				$nestedData[] = $row['h_desc'];
				$nestedData[] = $row['holiday_type'];
				$nestedData[] = $row['date'];

				$nestedData[] =
        '
          <center>
            <button class="btn btn-sm btn-success btn_edit_holiday_modal"
              data-update_id = "'.$row['id'].'"
              data-h_desc = "'.$row['h_desc'].'"
              data-h_type = "'.$row['holidaytypeid'].'"
              data-h_date = "'.$row['date'].'" style = "width:90px;">
              <i class="fa fa-pencil"></i>Edit
            </button>
						<button class="btn btn-danger btn_del_holiday"
              data-del_desc = "'.$row['h_desc'].'"
              data-del_id="'.$row['id'].'" style = "width:90px;">
							<i class="fa fa-trash"></i>
							Delete
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

  public function get_holiday_type(){
    $sql = "SELECT * FROM holidaytype WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function get_desc_w_date($data){
    $sql = "SELECT * FROM holidays_tran a WHERE a.enabled = 1 AND a.description = ? AND a.date = ?";
    return $this->db->query($sql,$data);
  }

  public function create($data){
    $this->db->insert("holidays_tran", $data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function update($data){
    $sql = "UPDATE holidays_tran SET description = ?, holiday_type = ?, date = ? WHERE id = ?";
    $this->db->query($sql,$data);
    // return $this->db->last_query();
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function delete($id){
    $sql = "UPDATE holidays_tran SET enabled = 0 WHERE id = ?";
    $data = array($id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

}
