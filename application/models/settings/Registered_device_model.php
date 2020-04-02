<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Registered_device_model extends CI_Model {
  public function get_registered_device_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'No.',
      1 => 'activation_code',
      2 => 'device_id',
      3 => 'status'
    );


    $sql = "SELECT * FROM hris_registered_device WHERE enabled = 1";

    ### sub filter ###
    switch ($search->filter) {
      case 'divActivationCode':
        $activation_code = $this->db->escape($search->search);
        $sql .= " AND activation_code = $activation_code";
        break;
      case 'divDeviceID':
        $device_id = $this->db->escape($search->search);
        $sql .= " AND device_id = $device_id";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $x = 1;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $nestedData[] = $x;
      $nestedData[] = $row['activation_code'];
      $nestedData[] = ($row['device_id'] != '') ? $row['device_id'] : '<center>-----</center>';
      $nestedData[] = ($row['status'] == 'open')
      ? '<center><span class="badge badge-pill badge-success">Available</span></center>'
      : '<center><span class="badge badge-pill badge-danger">Closed</span></center>';

      $nestedData[] =
      '<center>
        <button class="btn btn-danger btn_delete" style = "width:80px;"
          data-delid = "'.en_dec('en',$row['id']).'"
          data-device_id = "'.$row['device_id'].'"
          data-activation_code = "'.$row['activation_code'].'"
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

  public function get_activation_code($code){
    $code = $this->db->escape($code);
    $sql = "SELECT * FROM hris_registered_device WHERE enabled = 1 AND status = 'open' AND activation_code = $code";
    return $this->db->query($sql);
  }

  public function get_device($id){
    $id = $this->db->escape($id);
    $sql = "SELECT * FROM hris_registered_device WHERE enabled = 1 AND device_id = $id";
    return $this->db->query($sql);
  }

  public function set_activation_code($data){
    $this->db->insert('hris_registered_device',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_status($id,$status = '0'){
    $id = $this->db->escape($id);
    $status = $this->db->escape($status);

    $sql = "UPDATE hris_registered_device SET enabled = $status WHERE id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function register_device($data){
    $sql = "UPDATE hris_registered_device SET device_id = ?, status = 'closed' WHERE activation_code = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
