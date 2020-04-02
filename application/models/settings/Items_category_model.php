<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items_category_model extends CI_Model {
  public function get_items_category_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM hris_items_category WHERE enabled = 1";

    switch ($search->filter) {
      case 'divName':
        $cat_name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (cat_name LIKE $cat_name)";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY cat_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $count = 1;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $count;
      $nestedData[] = $row['cat_name'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_edit" style = "width:75px;"
            data-uid = "'.en_dec('en', $row['id']).'"
            data-cat_name = "'.$row['cat_name'].'"
          >
            Edit
          </button>
          <button class="btn btn-danger btn_del" style = "width:75px;"
            data-delid = "'.en_dec('en', $row['id']).'"
            data-cat_name = "'.$row['cat_name'].'"
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

  public function get_category($name, $self = false){
    $name = $this->db->escape($name);
    $sql = "SELECT * FROM hris_items_category WHERE enabled = 1 AND cat_name = $name";
    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    return $this->db->query($sql);
  }

  public function set_category($data){
    $this->db->insert('hris_items_category',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_category($data,$id){
    $this->db->update('hris_items_category',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
    // return $this->db->last_query();
  }

  public function update_category_status($data,$id){
    $this->db->update('hris_items_category',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
