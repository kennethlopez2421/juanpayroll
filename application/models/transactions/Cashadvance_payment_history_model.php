<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cashadvance_payment_history_model extends CI_Model {

  public function get_cashadvance_payment_history_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'employee_idno',
      1 => 'name',
      2 => 'date',
      3 => 'payment'
    );


    $sql = "SELECT a.*, CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname
      FROM cash_advance_pay a
      INNER JOIN cash_advance_tran b ON a.ca_id = b.id
      INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
      LEFT JOIN contract d ON c.id = d.contract_emp_id
      WHERE d.contract_status = 'active' AND c.enabled = 1 AND b.status = 'certified'
      AND a.enabled = 1";


    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // if( !empty($requestData['search']['value']) ){
    //   $sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
    // }

    if($search != ""){
      $sql .= $search;
    }

    $sql.=" ORDER BY a.created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

      $data = array();

      foreach( $query->result_array() as $row )
      {
        $nestedData=array();

        $nestedData[] = $row['employee_idno'];
        $nestedData[] = $row['fullname'];
        $nestedData[] = $row['cutoff_from']." - ".$row['cutoff_to'];
        $nestedData[] = '<span class = "float-right">'.number_format($row['ca_payment'],2).'</span class = "float-right">';
        $nestedData[] =
          '<center>
            <button class="btn btn-sm btn-primary btn_view_ca" id = "'.$row['id'].'" style = "width:80px;">
              <i class="fa fa-eye mr-2"></i>View
            </button>
          </center>';

        $data[] = $nestedData;
      }
      $json_data = array(

        "recordsTotal"    => intval( $totalData ),
        "recordsFiltered" => intval( $totalFiltered ),
        "data"            => $data
      );
      return $json_data;
  }

  public function get_ca_payment_breakdown($id){
    $sql = "SELECT a.*, b.*, CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname
      FROM cash_advance_pay a
      INNER JOIN cash_advance_tran b ON a.ca_id = b.id
      INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
      LEFT JOIN contract d ON c.id = d.contract_emp_id
      WHERE d.contract_status = 'active' AND c.enabled = 1 AND b.status = 'certified'
      AND a.enabled = 1 AND a.id = ?";
    $data = array($id);
    return $this->db->query($sql,$data);
  }
}
