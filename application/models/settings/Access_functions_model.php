<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Access_functions_model extends CI_Model {
  public function get_access_functions_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM hris_content_navigation WHERE status = 1";

    switch ($search->filter) {
      case 'divName':
        $name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (cn_name LIKE $name)";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY cn_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $sql_access = "SELECT * FROM hris_functions WHERE enabled = 1";
    $query2 = $this->db->query($sql_access);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] =
      '
        <label class="container_label">
          <input type="checkbox" name = "select_all[]" class = "select_all" value = "">
          <span class="checkmark"></span>
        </label>
      ';
      $nestedData[] = $row['cn_name'];
      foreach($query2->result_array() as $row2){
        $access_ids = explode(',',$row2['main_nav_access']);
        $checked = (in_array($row['id'], $access_ids)) ? 'checked' : '';
        $nestedData[] =
        '
          <label class="container_label">
            <input type="checkbox" name = "access_'.$row2['name'].'[]" class = "access_'.$row2['name'].' access_func" value = "'.en_dec('en',$row['id']).'" '.$checked.'>
            <span class="checkmark"></span>
          </label>
        ';
      }

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_access_functions($id = false){
    $sql = "SELECT * FROM hris_functions WHERE enabled = 1";
    if($id){
      $id = $this->db->escape($id);
      $sql .= " AND id = $id";
    }

    return $this->db->query($sql);
  }

  public function update_access_function($data){
    $sql = "UPDATE hris_functions SET main_nav_access = ? WHERE enabled = 1 AND id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
