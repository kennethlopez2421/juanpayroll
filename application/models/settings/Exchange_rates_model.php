<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Exchange_rates_model extends CI_Model {
  public function get_exchange_rates_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM hris_exchange_rates WHERE enabled = 1";

    if($search != ""){
      $search = $this->db->escape('%'.$search.'%');
      $sql .= " AND (currency_code LIKE $search OR currency_code LIKE $search)";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY currency_code ASC, currency_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $x = 1;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $x;
      $nestedData[] = $row['base'];
      $nestedData[] = $row['currency_code'];
      $nestedData[] = $row['currency_name'];
      $nestedData[] = '<span class="float-right">'.number_format($row['exchange_rate'],2).'</span>';
      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_edit" style = "width:90px;"
            data-uid = "'.$row['id'].'"
            data-code = "'.$row['currency_code'].'"
            data-name = "'.$row['currency_name'].'"
            data-rate = "'.$row['exchange_rate'].'"
          >
            <i class="fa fa-pencil mr-1"></i>Edit
          </button>
          <button class="btn btn-danger btn_delete" style = "width:90px;"
            data-delid = "'.$row['id'].'"
            data-code = "'.$row['currency_code'].'"
            data-name = "'.$row['currency_name'].'"
          >
            <i class="fa fa-trash mr-1"></i>Delete
          </button>
        </center>
      ';


      $data[] = $nestedData;
      $x++;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_exchange_rate($code = false,$name = false, $self = false){
    $sql = "SELECT * FROM hris_exchange_rates WHERE enabled = 1";
    if($code){
      $code = $this->db->escape($code);
      $sql .= " AND currency_code = $code";
    }

    if($name){
      $name = $this->db->escape($name);
      $sql .= " AND currency_name = $name";
    }

    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }

    $sql .= " ORDER BY id ASC, currency_code ASC, currency_name ASC";
    return $this->db->query($sql);
  }

  public function get_contract_with_same_currency($currency_code){
    $code = $this->db->escape($currency_code);
    $sql = "SELECT a.total_sal, b.exchange_rate as ex_rate, a.id as contract_id
     FROM contract a
     INNER JOIN hris_exchange_rates b ON a.currency = b.currency_code
     WHERE a.enabled = 1 AND b.enabled = 1 AND a.contract_status = 'active'
     AND a.currency = $code";
    return $this->db->query($sql);
  }

  public function set_exchange_rate($data){
    $this->db->insert('hris_exchange_rates', $data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_exchange_rate($data){
    $sql = "UPDATE hris_exchange_rates SET currency_code = ?, currency_name = ?, exchange_rate = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_exchange_rate_status($id,$status = 0){
    $sql = "UPDATE hris_exchange_rates SET enabled = ? WHERE id = ? AND enabled = 1";
    $data = array($status,$id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_total_sal_converted($data){
    $this->db->update_batch('contract',$data,'id');
    return ($this->db->affected_rows() >0) ? true: false;
  }

}
