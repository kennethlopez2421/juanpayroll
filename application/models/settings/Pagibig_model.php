<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pagibig_model extends CI_Model{

  public function getPagIbig_data($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'monthly_compensation',
			1 => 'employee_share',
			2 => 'employer_share'
		);


		$sql = "SELECT * FROM pagibig WHERE enabled = 1";


		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

		// if( !empty($requestData['search']['value']) ){
		// 	$sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
		// }

    if($search != ""){
      $sql .= " AND monthly_compensation LIKE '".$this->db->escape_like_str($search)."%'";
    }

		$totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
			$sql.=" ORDER BY id ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
			$data = array();

			foreach( $query->result_array() as $row )
			{
				$nestedData=array();

				$nestedData[] = $row['monthly_compensation'];
				$nestedData[] = (int)$row["employee_share"]." %";
				$nestedData[] = (int)$row["employer_share"]." %";

				$nestedData[] =
        '
				  <button class="btn_edit_pagibig btn btn-sm btn-primary" style = "width:80px;" data-updateid = "'.$row['id'].'"><i class="fa fa-pencil mr-2"></i>Edit</button>
				  <button class="btn_del_pagibig btn btn-sm btn-danger" style = "width:80px;" data-deleteid = "'.$row['id'].'"><i class="fa fa-trash mr-2"></i>Delete</button>
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

  public function getPagIbig(){
    $sql = "SELECT * FROM pagibig WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function set_pagibig($data){
    $insert_data = array(
      "monthly_compensation" => $data['monthly_compensation'],
      "employee_share" => $data['employee_share'],
      "employer_share" => $data['employer_share']
    );
    $this->db->insert('pagibig', $insert_data);
  }

  public function get_pagIbigById($id){
    $sql = "SELECT * FROM pagibig WHERE id = ?";
    $data = array($id);

    return $this->db->query($sql,$data);
  }

  public function update_pagIbig($data){
    $sql = "UPDATE pagibig SET monthly_compensation = ?, employee_share = ?, employer_share = ? WHERE id = ?";
    $this->db->query($sql,$data);
  }

  public function en_dis_pagibig($data){
    $sql = "UPDATE pagibig SET enabled = ? WHERE id = ?";
    $this->db->query($sql,$data);
  }
}
