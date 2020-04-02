<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Clockinout_deductions_model extends CI_Model {
  public function get_clockinout_deductions_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM hris_clockinout_deductions WHERE enabled = 1";

    switch ($search->filter) {
      case 'divType':
        $type = $this->db->escape(en_dec('dec',$search->search));
        $sql .= " AND (type = $type)";
        break;
      default:
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY status ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $on = ($row['status'] == "on") ? "SELECTED" : "";
      $off = ($row['status'] == "off") ? "SELECTED" : "";
      $nestedData[] = ucfirst($row['type']);
      $nestedData[] = $row['min_from']."-".$row['min_to'];
      $nestedData[] = $row['min_deduct'];
      $nestedData[] = $row['whours'];
      $nestedData[] =
      '
        <select name="status" class = " status form-control text-center" data-uid = "'.en_dec('en', $row['id']).'">
          <option value="'.en_dec('en','on').'" '.$on.'>On</option>
          <option value="'.en_dec('en','off').'" '.$off.'>Off</option>
        </select>
      ';

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_update_modal" style = "width:80px;"
            data-uid = "'.en_dec('en',$row['id']).'"
            data-type = "'.en_dec('en',$row['type']).'"
            data-min_from = "'.$row['min_from'].'"
            data-min_to = "'.$row['min_to'].'"
            data-min_deduct = "'.$row['min_deduct'].'"
            data-whours = "'.$row['whours'].'"
          >
            Update
          </button>
          <button class="btn btn-danger btn_del_modal" style = "width:80px;"
            data-delid = "'.en_dec('en',$row['id']).'"
          >
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

  public function set_deduct($data){
    $this->db->insert('hris_clockinout_deductions',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function check_type_and_hours($type,$hours,$id = false){
    $type = $this->db->escape($type);
    $hours = $this->db->escape($hours);
    $sql = "SELECT * FROM hris_clockinout_deductions WHERE type = $type AND whours = $hours AND enabled = 1";
    if($id){
      $id = $this->db->escape($id);
      $sql .= " AND id != $id";
    }
    return $this->db->query($sql);
  }

  public function update($data,$id){
    $this->db->update('hris_clockinout_deductions',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_status($data,$id){
    $this->db->update('hris_clockinout_deductions',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_enabled($id,$enabled = 0){
    $id = $this->db->escape($id);
    $enabled = $this->db->escape($enabled);
    $sql = "UPDATE hris_clockinout_deductions SET enabled = $enabled WHERE id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
