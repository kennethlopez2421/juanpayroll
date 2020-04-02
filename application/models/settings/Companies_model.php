<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Companies_model extends CI_Model {
  public function get_companies_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'no.',
      1 => 'company'
    );


    $sql = "SELECT * FROM hris_companies WHERE enabled = 1";

    if($search != ""){
      $search = $this->db->escape("%".$search."%");
      $sql .= " AND company LIKE $search";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY company ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $x = 0;
    foreach( $query->result_array() as $row )
    {
      $x++;
      $nestedData=array();

      $nestedData[] = $x;
      $nestedData[] = $row['company'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_edit_modal" style = "width:80px;"
            data-uid = "'.en_dec('en',$row['id']).'"
            data-name = "'.$row['company'].'"
          >
            <i class="fa fa-pencil mr-2"></i>Edit
          </button>
          <button class="btn btn-danger btn_del_modal" style = "width:80px;"
            data-delid = "'.en_dec('en', $row['id']).'"
            data-name = "'.$row['company'].'"
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

  public function get_companies_by_name($name, $id = false){
    $sql = "SELECT * FROM hris_companies WHERE company = ? AND enabled = 1";
    $data = array($name);
    if($id){
      $sql .= " AND id != ?";
      $data = array($name,$id);
    }
    return $this->db->query($sql,$data);
  }

  public function set_company($data){
    $this->db->insert('hris_companies',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_company($data){
    $sql = "UPDATE hris_companies SET company = ? WHERE id = ? AND enabled = 1";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_company_status($data){
    $sql = "UPDATE hris_companies SET enabled = ? WHERE id = ? AND enabled = 1";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
