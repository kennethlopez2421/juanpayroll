<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cashadvancepaymentscheme_model extends CI_Model {
  public function getCaps_json($search){
    $requestData = $_REQUEST;

		$columns = array(
			0 => 'monthly_rate',
			1 => 'maximum_loan',
      2 => 'term_of_payment'
		);


		$sql = "SELECT * FROM cash_advance_payment_scheme WHERE enabled = 1";


		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

		// if( !empty($requestData['search']['value']) ){
		// 	$sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
		// }

    if($search != ""){
      // $sql .= " AND (CONCAT(last_name,', ',first_name) LIKE '".$this->db->escape_like_str($search)."%'
      // OR CONCAT(last_name,', ',first_name,' ',middle_name) LIKE '".$this->db->escape_like_str($search)."%'
      // OR last_name LIKE '".$this->db->escape_like_str($search)."%'
      // OR first_name LIKE '".$this->db->escape_like_str($search)."%'
      // OR a.date LIKE '".$this->db->escape_like_str($search)."%')";
    }

		$totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
			$sql.=" ORDER BY monthly_rate ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
			$data = array();

			foreach( $query->result_array() as $row )
			{
				$nestedData=array();

				$nestedData[] = (float)$row['monthly_rate'];
				$nestedData[] = (float)$row['maximum_loan'];
				$nestedData[] = $row['term_of_payment'];
        // $nestedData[] = '<center><h5 class = "text-warning">Waiting for approval</h5></center>';

				$nestedData[] =
        '
          <center>
            <button class="btn btn-sm btn-primary btn_edit_caps" style = "width:90px;"
              data-id = "'.$row['id'].'"
              data-monthly_rate = "'.$row['monthly_rate'].'"
              data-maximum_loan = "'.$row['maximum_loan'].'"
              data-term_of_payment = "'.$row['term_of_payment'].'"><i class="fa fa-pencil pr-1"></i>Edit</button>
            <button class="btn btn-sm btn-danger btn_del_caps" style = "width:90px;" data-id = "'.$row['id'].'"><i class="fa fa-trash pr-1"></i>Delete</button>
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

  public function setPaymentScheme($data){
    $this->db->insert('cash_advance_payment_scheme',$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function updatePaymentScheme($data){
    $sql = "UPDATE cash_advance_payment_scheme SET monthly_rate = ?, maximum_loan = ?, term_of_payment = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function updatePaymentSchemeStatus($data){
    $sql = "UPDATE cash_advance_payment_scheme SET enabled = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }
}
