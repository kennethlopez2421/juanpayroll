<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Nightdiff_model extends CI_Model {
  public function get_nightdiff_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM hris_nightdiff_settings WHERE enabled = 1";

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

    $sql.=" ORDER BY start ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $on = ($row['status'] == "on") ? "SELECTED" : "";
      $off = ($row['status'] == "off") ? "SELECTED" : "";
      $nestedData[] = '<input name = "start" id = "start" type="time" class="form-control rq" value = "'.$row['start'].'"/>';
      $nestedData[] = '<input name = "end" id = "end" type="time" class="form-control rq" value = "'.$row['end'].'"/>';
      $nestedData[] = '<input name = "percent" id = "percent" type="input" class="form-control rq text-right number-input-2" style = "width:120px;" value = "'.$row['percent'].'"/>';
      $nestedData[] =
      '
        <select name="status" id="status" class="form-control rq" >
          <option value="'.en_dec('en', 'on').'" '.$on.'>On</option>
          <option value="'.en_dec('en', 'off').'" '.$off.'>Off</option>
        </select>
      ';

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_apply" style = "width:70px;" data-uid = "'.en_dec('en',$row['id']).'">
            Apply
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

  public function update($data,$id){
    $this->db->update('hris_nightdiff_settings',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
