<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_role_model extends CI_Model {
  public function get_user_role_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'position'
    );


    $sql = "SELECT * FROM hris_position WHERE enabled = 1";

    if($search != ""){
      $search = $this->db->escape("%".$search."%");
      $sql .= " AND position LIKE $search";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY hierarchy_lvl ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $no = 1;
    foreach( $query->result_array() as $row )
    {

      $nestedData=array();
      $nestedData[] = $no;
      $nestedData[] = $row['position'];
      $nestedData[] =
      "<center>
        <button class='btn btn-primary btn_edit' style = 'width:90px;'
          data-id = '".$row['position_id']."'
          data-desc = '".$row['position']."'
          data-main_nav = '".$row['access_nav']."'
          data-content_nav = '".$row['access_content_nav']."'
          data-access_func_nav = '".$row["access_func_nav"]."'
        >
          <i class='fa fa-pencil mr-1'></i>Edit
        </button>
        <button class='btn btn-danger btn_delete' style = 'width:90px;'
          data-id = '".$row['position_id']."'
          data-position = '".$row['position']."'
        >
          <i class='fa fa-trash mr-1'></i>Delete
        </button>
      </center>
      ";
      $no++;
      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_main_nav(){
    $sql = "SELECT main_nav_id, main_nav_desc, main_nav_icon
      FROM hris_main_navigation WHERE enabled = 1 ORDER BY main_nav_desc ASC";
    return $this->db->query($sql);
  }

  public function get_main_nav_w_content(){
    $sql = "SELECT @id := main_nav_id as main_nav_id, main_nav_desc FROM hris_main_navigation
      WHERE (SELECT COUNT(id) FROM hris_content_navigation WHERE cn_fkey = main_nav_id AND status = 1) > 0 AND enabled = 1";
    return $this->db->query($sql);
  }

  public function get_content_nav(){
    $sql = "SELECT id as content_nav_id, cn_name, cn_description, cn_fkey
      FROM hris_content_navigation WHERE status = 1 ORDER BY cn_name ASC";
    return $this->db->query($sql);
  }

  public function get_functions($nav_id = false){
    $sql = "SELECT id, name, main_nav_access FROM hris_functions WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function get_functions_access($nav_id = false){
    $sql = "SELECT id, position_id, content_nav_access FROM hris_functions_access WHERE enabled = 1";
    if($nav_id){
      $nav_id = $this->db->escape($nav_id);
      $sql .= " AND content_nav_access = $nav_id";
    }
    return $this->db->query($sql);
  }

  public function set_user_role($data){
    $this->db->insert('hris_position', $data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_func_access_batch($data){
    $this->db->insert_batch('hris_functions_access',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_user_role($data,$id){
    $this->db->update('hris_position',$data,array('position_id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_user_role_status($id, $status = 0){
    $id = $this->db->escape($id);
    $status = $this->db->escape($status);
    $sql = "UPDATE hris_position SET enabled = $status WHERE position_id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_function_batch($data){
    $this->db->update_batch('hris_functions_access',$data,'content_nav_access');
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function test(){

  }
}
