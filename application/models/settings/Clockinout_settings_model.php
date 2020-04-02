<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Clockinout_settings_model extends CI_Model {
  public function get_clockinout_settings_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM hris_clockinout_settings WHERE enabled = 1";

    switch ($search->filter) {
      case 'divRules':
        $name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (rules LIKE $name)";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY rules ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $on = ($row['status'] == 'on') ? "SELECTED" : "";
      $off = ($row['status'] == 'off') ? "SELECTED" : "";
      $desc =
      '
        <div>
          <strong>'.$row['rules'].'</strong>
          <small class = "d-block">'.$row['description'].'</small>
        </div>
      ';

      $select =
      '<center>
        <select class="form-control status" data-uid = "'.en_dec('en',$row['id']).'">
          <option value="on" '.$on.'>On</option>
          <option value="off" '.$off.'>Off</option>
        </select>
       </center>
      ';

      $nestedData[] = $desc;
      $nestedData[] =
      '
        <center>
          <input type="number" class="form-control number-input-2 text-right minutes" value = "'.$row['minutes'].'"
            style = "width:120px;" data-value = "'.$row['minutes'].'" data-uid = "'.en_dec('en',$row['id']).'"
          />
        </center>
      ';
      $nestedData[] = $select;
      $nestedData[] =
      '<center>
        <button class="btn btn-primary btn_update" style = "width:70px;"
          data-uid = "'.en_dec('en',$row['id']).'"
          data-rules = "'.$row['rules'].'"
          data-desc = "'.$row['description'].'"
          data-mins = "'.$row['minutes'].'"
        >
          Update
        </button>
        <button class="btn btn-danger btn_del" style = "width:70px;"
          data-delid = "'.en_dec('en',$row['id']).'"
          data-rules = "'.$row['rules'].'"
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

  public function get_rules($rules,$id = false){
    $rules = $this->db->escape($rules);
    $sql = "SELECT * FROM hris_clockinout_settings WHERE rules = $rules AND enabled = 1";
    if($id){
      $id = $this->db->escape($id);
      $sql .= " AND id != $id";
    }
    return $this->db->query($sql);
  }

  public function set_rules($data){
    $this->db->insert('hris_clockinout_settings',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_status($data,$id){
    $this->db->update('hris_clockinout_settings',$data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_minutes($data,$id){
    $this->db->update('hris_clockinout_settings',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true :false;
  }

  public function update($data,$id){
    $this->db->update('hris_clockinout_settings',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function delete($id, $enabled = 0){
    $enabled = $this->db->escape($enabled);
    $id = $this->db->escape($id);
    $sql = "UPDATE hris_clockinout_settings SET enabled = $enabled WHERE id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
