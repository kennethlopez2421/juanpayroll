<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Attendance_graph_analysis_model extends CI_Model {
  public function get_graph_analysis_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT * FROM applicant_record WHERE app_enabled = 1";


    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    if($search != ""){
      $sql .= $search;
    }
    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

          $sql.=" ORDER BY app_lname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

          $data = array();

          foreach( $query->result_array() as $row )
          {
            $nestedData=array();

            $nestedData[] = $row['app_ref_no'];
            $nestedData[] = $row['app_lname'].", ".$row['app_fname']." ".$row['app_mname'];

            $nestedData[] =
      '
        <center>
              <form action = "'.base_url('applicants/Applicant/edit/'.en_dec('en',$this->session->userdata('token_session'))).'" class = "d-inline" method = "post">
          <input type="hidden" name = "appId" value = "'.$row['id'].'"/>
          <input type="hidden" name = "appRefNo" value = "'.$row['app_ref_no'].'" />
          <button type = "submit" class="btn_view_app btn btn-sm btn-primary" style = "width:40%;" data-updateid = "'.$row['id'].'"><i class="fa fa-eye mr-2"></i>View</button>
        </form>
              <button class="btn_del_app_modal btn btn-sm btn-danger" style = "width:40%;" data-deleteid = "'.$row['id'].'" data-name = "'.$row['app_lname'].', '.$row['app_fname'].' '.$row['app_mname'].'"><i class="fa fa-trash mr-2"></i>Delete</button>
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

  public function get_timelog($id,$date){
    $escape_emp_idno = $this->db->escape($id);
    $escape_date = $this->db->escape($date);

    $sql = "SELECT * FROM (
      SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
      a.date as timelog_date, 'timelog' as type
      FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
      UNION
      SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
      b.date as timelog_date, 'work order' as type
      FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
      AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
    ) as timelog ORDER BY timelog_date ASC, time_in ASC";

    return $this->db->query($sql);
  }

}
