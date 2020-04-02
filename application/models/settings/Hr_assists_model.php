<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Hr_assists_model extends CI_Model {
  // public function ($search){
  //   $requestData = $_REQUEST;
  //
  //   $columns = array(
  //     0 => 'app_ref_no',
  //     1 => 'Name'
  //   );
  //
  //
  //   $sql = "SELECT * FROM applicant_record WHERE app_enabled = 1";
  //
  //
  //   $query = $this->db->query($sql);
  //
  //   $totalData = $query->num_rows();
  //   $totalFiltered = $query->num_rows();
  //
  //   // if( !empty($requestData['search']['value']) ){
  //   //   $sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
  //   // }
  //
  //   if($search != ""){
  //     $sql .= " AND CONCAT(app_lname,', ',app_fname) LIKE '".$this->db->escape_like_str($search)."%'
  //     OR app_lname LIKE '".$this->db->escape_like_str($search)."%'
  //     OR app_fname LIKE '".$this->db->escape_like_str($search)."%'";
  //   }
  //
  //   $totalFiltered = $query->num_rows();
  //   $totalFiltered = $totalData;
  //     // $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
  //     //012819
  //     $sql.=" ORDER BY app_lname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";
  //
  //   $query = $this->db->query($sql);
  //     $data = array();
  //
  //     foreach( $query->result_array() as $row )
  //     {
  //       $nestedData=array();
  //
  //       $nestedData[] = $row['app_ref_no'];
  //       $nestedData[] = $row['app_lname'].", ".$row['app_fname']." ".$row['app_mname'];
  //
  //       $nestedData[] =
  //       '
  //         <center>
  //         <form action = "'.base_url('applicants/Applicant/edit/'.en_dec('en',$this->session->userdata('token_session'))).'" class = "d-inline" method = "post">
  //           <input type="hidden" name = "appId" value = "'.$row['id'].'"/>
  //           <input type="hidden" name = "appRefNo" value = "'.$row['app_ref_no'].'" />
  //           <button type = "submit" class="btn_view_app btn btn-sm btn-primary" style = "width:40%;" data-updateid = "'.$row['id'].'"><i class="fa fa-eye mr-2"></i>View</button>
  //         </form>
  //         <button class="btn_del_app_modal btn btn-sm btn-danger" style = "width:40%;" data-deleteid = "'.$row['id'].'" data-name = "'.$row['app_lname'].', '.$row['app_fname'].' '.$row['app_mname'].'"><i class="fa fa-trash mr-2"></i>Delete</button>
  //         </center>
  //       ';
  //
  //       $data[] = $nestedData;
  //     }
  //     $json_data = array(
  //
  //       "recordsTotal"    => intval( $totalData ),
  //       "recordsFiltered" => intval( $totalFiltered ),
  //       "data"            => $data
  //     );
  //     return $json_data;
  // }

  public function get_hrassists(){
    $sql = "SELECT * FROM hris_hrassists WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function set_hrassists($data){
    $this->db->insert('hris_hrassists', $data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_hrassists($body,$user_id,$id){
    $sql = "UPDATE hris_hrassists SET body = ?, created_by = ? WHERE id = ?";
    $data = array($body,$user_id,$id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
