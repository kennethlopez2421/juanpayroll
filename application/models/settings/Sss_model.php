<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sss_model extends CI_Model {
  public function get_sss_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $sql = "SELECT *, CONCAT(ROUND(salRange_from,2),' - ',ROUND(salRange_to,2)) as range_comp
      FROM sss WHERE enabled = 1";

    if($search != ""){
      $sql .= $search;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY salRange_from ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $salFrom = (find_match('\-',$row['salRange_from'])) ? "Below" : round($row['salRange_from'],2);
      $salTo = (find_match('\+',$row['salRange_to'])) ? "Above" : round($row['salRange_to'],2);

      // $nestedData[] = $row['range_comp'];
      $nestedData[] = $salFrom.' - '.$salTo;
      $nestedData[] = number_format($row['monthly_sal_cred'],2);
      $nestedData[] = number_format($row['ss_er'],2);
      $nestedData[] = number_format($row['ss_ee'],2);
      $nestedData[] = number_format($row['ss_total'],2);
      $nestedData[] = number_format($row['ec_er'],2);
      $nestedData[] = number_format($row['tc_er'],2);
      $nestedData[] = number_format($row['tc_ee'],2);
      $nestedData[] = number_format($row['tc_total'],2);
      $nestedData[] =
      '
        <button class="btn btn-sm btn-info btn_edit_sss" style = "width:80px;"
          data-update_id = "'.$row['id'].'"
          data-sal_from = "'.$row['salRange_from'].'"
          data-sal_to = "'.$row['salRange_to'].'"
          data-monthly_cred = "'.$row['monthly_sal_cred'].'"
          data-ss_er = "'.$row['ss_er'].'"
          data-ss_ee = "'.$row['ss_ee'].'"
          data-ss_total = "'.$row['ss_total'].'"
          data-ec_er = "'.$row['ec_er'].'"
          data-tc_er = "'.$row['tc_er'].'"
          data-tc_ee = "'.$row['tc_ee'].'"
          data-tc_total = "'.$row['tc_total'].'"
        >
          <i class="fa fa-pencil mr-2"></i>Edit</button>
        <button class="btn btn-sm btn-danger btn_delete_sss" style = "width:80px;"
          data-delete_id = "'.$row['id'].'"
        >
          <i class="fa fa-trash mr-2"></i>Delete
        </button>
      ';
      // $nestedData[] = number_format($row['SV_VM_OFW'],2);

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;

  }

  public function set_sss($data){
    $this->db->insert('sss',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_ss($data,$id){
    $this->db->where('id',$id);
    $this->db->update('sss',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function en_dis_sss($data){
    $sql = "UPDATE sss SET enabled = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function check_sss_sal_range($data){
    $sql = "SELECT salRange_from, salRange_to FROM sss
      WHERE salRange_from = ? AND salRange_to = ? AND enabled = 1";
    return $this->db->query($sql,$data);
  }
}
