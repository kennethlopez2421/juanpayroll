<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Benefits_settings_model extends CI_Model {
  public function get_benefits_settings_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM hris_benefits_setting WHERE enabled = 1";

    switch ($search->filter) {
      case 'divName':
        $benefits_name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (benefits_name LIKE $benefits_name)";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY benefits_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $count = 1;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $count;
      $nestedData[] = $row['benefits_name'];

      $nestedData[] =
      '
      <center>
        <button class="btn btn-primary btn_edit" style = "width:75px;"
          data-uid = "'.en_dec('en', $row['id']).'"
          data-benefits_name = "'.$row['benefits_name'].'"
        >
          Edit
        </button>
        <button class="btn btn-danger btn_delete" style = "width:75px;"
          data-delid = "'.en_dec('en', $row['id']).'"
          data-benefits_name = "'.$row['benefits_name'].'"
        >
          Delete
        </button>
      </center>
      ';
      $count++;
      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_benefits($name,$self = false){
    $name = $this->db->escape($name);
    $sql = "SELECT * FROM hris_benefits_setting WHERE enabled = 1 AND benefits_name = $name";
    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    return $this->db->query($sql);
  }

  public function set_benefits($data){
    $this->db->insert('hris_benefits_setting',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_benefits($data,$id){
    $this->db->update('hris_benefits_setting',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_benefits_status($data,$id){
    $this->db->update('hris_benefits_setting',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
