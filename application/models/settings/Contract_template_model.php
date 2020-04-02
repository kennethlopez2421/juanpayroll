<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contract_template_model extends CI_Model {
  public function get_contract_template_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'id',
      1 => 'template_name',
      2 => 'date_create'
    );

    $sql = "SELECT *, DATE(created_at) as date_created FROM hris_contract_template WHERE enabled = 1";

    ### sub filter ###
    switch ($search->filter) {
      case 'divTemplate':
        $template_name = $this->db->escape('%'.$search->search.'%');
        $sql .= " AND template_name LIKE $template_name";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY template_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $x = 1;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $x;
      $nestedData[] = $row['template_name'];
      $nestedData[] = $row['date_created'];
      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_view" style = "width:90px;"
            data-uid = "'.en_dec('en',$row['id']).'"
            data-template_type = "'.$row['template_type'].'"
          >
            <i class="fa fa-eye mr-1"></i>View
          </button>
          <button class="btn btn-danger btn_delete" style = "width:90px;"
            data-delid = "'.en_dec('en',$row['id']).'"
            data-template_name = "'.$row['template_name'].'"
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

  public function get_template_settings(){
    $sql = "SELECT * FROM hris_template_settings WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function get_contract_template($name = false, $id = false, $self = false){
    $sql = "SELECT * FROM hris_contract_template WHERE enabled = 1 ";
    if($name){
      $name = $this->db->escape($name);
      $sql .= " AND template_name = $name";
    }

    if($id){
      $id = $this->db->escape($id);
      $sql .= " AND id = $id";
    }

    if($self){
      $self = $this->db->escape($self);
      $sql .= " AND id != $self";
    }

    $sql .= " ORDER BY template_name ASC";
    return $this->db->query($sql);
  }

  public function set_template($data){
    $this->db->insert('hris_contract_template',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_template($data,$id){
    $this->db->update('hris_contract_template',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_template_status($id, $status = 0){
    $id = $this->db->escape($id);
    $status = $this->db->escape($status);
    $updated_at = $this->db->escape(todaytime());
    $sql = "UPDATE hris_contract_template SET enabled = $status, updated_at = $updated_at
     WHERE id = $id AND enabled = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_template_contract_id($cid,$id,$cid2,$templates){
    $id = $this->db->escape($id);
    $cid = $this->db->escape($cid);
    $cid2 = $this->db->escape($cid2);
    $templates = $this->db->escape($templates);
    $sql = "UPDATE hris_contract_files SET contract_id = $cid2, content = $templates WHERE template_id = $id AND contract_id = $cid
     AND enabled = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
