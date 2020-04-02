<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payout_model extends CI_Model {

  public function get_payout_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT a.employee_idno,
      CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      b.payout_medium as payout_medium, c.description as pm_desc, e.bank_name, b.id as c_id,
      d.bank_id, d.card_number, d.account_number, d.id as cpm_id
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      LEFT JOIN payoutmedium c ON b.payout_medium = c.payoutmediumid
      LEFT JOIN contract_payout_medium d ON b.id = d.contract_id
      LEFT JOIN bank e ON d.bank_id = e.bank_id
      WHERE a.enabled = 1 AND b.contract_status = 'active' AND c.enabled = 1";


    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    if($search != ""){
      $sql .= $search;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['pm_desc'];
      $nestedData[] = $row['bank_name'];


      $nestedData[] =
      '
        <center>
        <button class="btn btn-sm btn-primary btn_update_payout"
          data-c_id = "'.$row['c_id'].'"
          data-cpm_id = "'.$row['cpm_id'].'"
          data-pm_id = "'.$row['payout_medium'].'"
          data-bank_id = "'.$row['bank_id'].'"
          data-card_number = "'.$row['card_number'].'"
          data-account_number = "'.$row['account_number'].'"
          style = "width:80px;"><i class="fa fa-pencil mr-2"></i>Update</button>
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

  public function get_bank(){
    $sql = "SELECT * FROM bank WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function get_payoutmedium(){
    $sql = "SELECT * FROM payoutmedium WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function set_payout_information($data){
    $this->db->insert('contract_payout_medium',$data);
    $inserted = ($this->db->affected_rows() > 0)? true: false;
    if($inserted == true){
      $sql = "UPDATE contract SET payout_medium = ? WHERE id = ? AND contract_status = 'active'";
      $update_data = array($data['payout_medium_id'],$data['contract_id']);
      $this->db->query($sql,$update_data);
      return true;
      // return ($this->db->affected_rows() > 0) ? true: false;

      // return $this->db->last_query();
    }else{
      return false;
    }
  }

  public function check_contract($id){
    $sql = "SELECT id FROM contract WHERE id = ? ";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function check_card_number($card_number,$c_id,$cpm_id){
    $sql = "SELECT * FROM contract_payout_medium
      WHERE card_number = ?
      AND contract_id != ? AND id != ?";
    $data = array($card_number,$c_id,$cpm_id);
    return $this->db->query($sql,$data);
  }

  public function check_account_number($account_number,$c_id,$cpm_id){
    $sql = "SELECT * FROM contract_payout_medium
      WHERE account_number = ?
      AND contract_id != ? AND id != ?";
    $data = array($account_number,$c_id,$cpm_id);
    return $this->db->query($sql,$data);
  }

  public function update_payout_information($data){
    $sql = "UPDATE contract_payout_medium a
            INNER JOIN contract b ON a.contract_id = b.id
            SET a.payout_medium_id = ?,
            a.bank_id = ?,
            a.card_number = ?,
            a.account_number = ?,
            a.updated_at = ?,
            b.payout_medium = ?
            WHERE a.contract_id = ? AND a.id = ? AND b.contract_status = 'active'";
    $this->db->query($sql,$data);
  }
}
