<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Bank_model extends CI_Model {

  public function get_bank_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM bank WHERE enabled = 1";

    if($search != ""){
      $search = $this->db->escape($search."%");
      $sql .= " AND bank_name LIKE $search";
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $sql.=" ORDER BY bank_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $count = 1;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $count++;
      $nestedData[] = $row['bank_name'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-sm btn-info btn_update_modal"
            data-uid = "'.$row['bank_id'].'"
            data-bank_name = "'.$row['bank_name'].'"
          >
            <i class="fa fa-pencil mr-2"></i>Update
          </button>
          <button class="btn btn-sm btn-danger btn_delete_modal"
            data-del_id = "'.$row['bank_id'].'"
            data-bank_name = "'.$row['bank_name'].'"
          >
            <i class="fa fa-trash mr-2"></i>Delete
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

  public function get_bank_by_name($data){
    $sql = "SELECT bank_name FROM bank WHERE bank_name = ?";
    return $this->db->query($sql,$data);
  }

  public function set_bank($data){
    $this->db->insert('bank',$data);
    return ($this->db->affected_rows() > 0 )? true : false;  
  }

  public function update_bank($data){
    $sql = "UPDATE bank SET bank_name = ? WHERE bank_id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_bank_status($data){
    $sql = "UPDATE bank SET enabled = ? WHERE bank_id = ? AND enabled = 1";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }


}
