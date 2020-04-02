<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Timelogreports_model extends CI_Model{

  public function getTimeLogReports_data($search = ""){

    $requestData = $_REQUEST;

    $columns = array(
			0 => 'employee_idno',
			1 => 'description',
      2 => 'location',
			3 => 'time',
      4 => 'date',
      5 => 'type'
		);

    $sql = "SELECT * FROM (SELECT a.time_in as time_in, a.time_out as time_out, a.date as timelog_date, a.id as time_id,
            c.employee_idno as employee_idno, CONCAT(c.last_name, ', ', c.first_name, ' ', c.middle_name) as fullname,
            b.description as worksite, a.img_url as image, 'timelog' as status
            FROM time_record_summary_trial a
            LEFT JOIN worksite b ON a.worksite = b.worksiteid
            LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
            WHERE a.enabled = 1
            UNION
            SELECT a.start_time as time_in, a.end_time as time_out, a.date as timelog_date, a.id as time_id,
            c.employee_idno as employee_idno, CONCAT(c.last_name, ', ', c.first_name, ' ', c.middle_name) as fullname,
            e.description as worksite, '' as image, 'workorder' as status
            FROM work_order a
            LEFT JOIN employee_record c ON a.employee_id = c.employee_idno
            LEFT JOIN contract d ON c.id = d.contract_emp_id
            LEFT JOIN worksite e ON d.work_site_id = e.worksiteid
            WHERE a.enabled = 1 AND a.status = 'certified' AND d.contract_status = 'active' AND c.enabled = 1) as timelog";

    if($search != ""){
			$sql = "SELECT * FROM (SELECT a.time_in as time_in, a.time_out as time_out, a.date as timelog_date, a.id as time_id,
              c.employee_idno as employee_idno, CONCAT(c.last_name, ', ', c.first_name, ' ', c.middle_name) as fullname,
              b.description as worksite, a.img_url as image, 'timelog' as status
              FROM time_record_summary_trial a
              LEFT JOIN worksite b ON a.worksite = b.worksiteid
              LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
              WHERE a.enabled = 1 $search
              UNION
              SELECT a.start_time as time_in, a.end_time as time_out, a.date as timelog_date, a.id as time_id,
              c.employee_idno as employee_idno, CONCAT(c.last_name, ', ', c.first_name, ' ', c.middle_name) as fullname,
              e.description as worksite, '' as image, 'workorder' as status
              FROM work_order a
              LEFT JOIN employee_record c ON a.employee_id = c.employee_idno
              LEFT JOIN contract d ON c.id = d.contract_emp_id
              LEFT JOIN worksite e ON d.work_site_id = e.worksiteid
              WHERE a.enabled = 1 AND a.status = 'certified' AND d.contract_status = 'active' AND
              c.enabled = 1 $search) as timelog";
    }else{
      // $sql .= " AND a.date = ".$this->db->escape(today());
      $today = $this->db->escape(today());
      $sql = "SELECT * FROM (SELECT a.time_in as time_in, a.time_out as time_out, a.date as timelog_date, a.id as time_id,
              c.employee_idno as employee_idno, CONCAT(c.last_name, ', ', c.first_name, ' ', c.middle_name) as fullname,
              b.description as worksite, a.img_url as image, 'timelog' as status
              FROM time_record_summary_trial a
              LEFT JOIN worksite b ON a.worksite = b.worksiteid
              LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
              WHERE a.enabled = 1 AND a.date = $today
              UNION
              SELECT a.start_time as time_in, a.end_time as time_out, a.date as timelog_date, a.id as time_id,
              c.employee_idno as employee_idno, CONCAT(c.last_name, ', ', c.first_name, ' ', c.middle_name) as fullname,
              e.description as worksite, '' as image, 'workorder' as status
              FROM work_order a
              LEFT JOIN employee_record c ON a.employee_id = c.employee_idno
              LEFT JOIN contract d ON c.id = d.contract_emp_id
              LEFT JOIN worksite e ON d.work_site_id = e.worksiteid
              WHERE a.enabled = 1 AND a.status = 'certified' AND d.contract_status = 'active' AND c.enabled = 1
              AND a.date = $today) as timelog";
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY timelog_date DESC, fullname ASC, time_in DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach($query->result_array() as $row){
      $nestedData = array();

      // $remarks = "";
      // if($row['status_absent'] == "1"){
      //   $remarks = "<span class = 'text-danger'>Absent</span>";
      // }else{
      //   $remarks = "<span class = 'text-success'>Present</span>";
      // }
      $img_row = "";
      if($row['status'] == 'timelog'){
        $img = explode(',',$row['image']);
        $count = count((array)$img);
        if($count > 0){
          for ($i=0; $i < $count; $i++) {
            $title = "";
            if($i%2 == 0){
              $title = "Time In";
            }else{
              $title = "Time Out";
            }
            $img_row .= '<div class="img-thumbnail d-inline-block time_img mr-1" data-url = "'.$img[$i].'" data-title = "'.$title.'"><img src="'.base_url($img[$i]).'" alt="" height = "40" width = "40"/></div>';
          }
        }else{
          $img_row = '<div class="img-thumbnail"><h3><i class="fa fa-file-image-o"></i></h3></div>';
        }
      }

      if($row['status'] == 'workorder'){
        $img_row = '<h3><i class="fa fa-file-image-o" style = "font-size:35px;padding:5px 8px;border:1px solid gainsboro;border-radius:3px;backgroun-color:#ffffff;"></i></h3>';
      }

      $nestedData[] = $img_row;
      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row["worksite"];
      $nestedData[] = $row["timelog_date"];
      $nestedData[] = $row["time_in"];
      $nestedData[] = $row['time_out'];
      $nestedData[] =
      '
        <button class="btn btn-primary edit_btn" style = "width:80px;"
          data-uid = "'.en_dec('en',$row['time_id']).'"
          data-timein = "'.convert_time($row['time_in']).'"
          data-timeout = "'.convert_time($row['time_out']).'"
          data-status = "'.$row['status'].'"
          data-emp_id = "'.en_dec('en',$row['employee_idno']).'"
          data-date = "'.$row['timelog_date'].'"
        >
          <i class="fa fa-pencil mr-1"></i>Edit
        </button>
        <button class="btn btn-danger del_btn" style = "width:80px;"
          data-delid = "'.en_dec('en', $row['time_id']).'"
          data-status = "'.$row['status'].'"
          data-fullname = "'.$row['fullname'].'"
          data-emp_id = "'.$row['employee_idno'].'"
          data-del_date = "'.$row['timelog_date'].'"
        >
          <i class="fa fa-trash mr-1"></i>Delete
        </button>
      ';
      // $nestedData[] = $remarks;

      $data[] = $nestedData;

    }

    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );
    // return $sql;

    return $json_data;
  }

  public function getTimeLogReports_dateRange($from, $to){


		$sql = "SELECT * FROM time_record_summary_trial a
            LEFT JOIN worksite b ON a.worksite = b.worksiteid
            LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
            WHERE a.enabled = 1 AND a.date BETWEEN ? AND ?";
    $date_data = array($from, $to);
    $query =  $this->db->query($sql,$date_data);
    $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['first_name']." ".$row['middle_name']." ".$row['last_name'];
      $nestedData[] = $row["description"];
      $nestedData[] = $row["date"];
      $nestedData[] = $row["time_in"];
      $nestedData[] = $row["time_out"];

      // $nestedData[] =
      // '
      //   <button class="btn_edit_pagibig btn btn-sm btn-success" style = "width:30%;" data-updateid = "'.$row['id'].'"><i class="fa fa-pencil mr-2"></i>Edit</button>
      //   <button class="btn_del_pagibig btn btn-sm btn-danger" style = "width:30%;" data-deleteid = "'.$row['id'].'"><i class="fa fa-trash mr-2"></i>Delete</button>
      // ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );
    return $json_data;
    // return $this->db->last_query();
  }

  public function getTimeLogReports_excel($from = false, $to = false, $id = ""){
    $sql = "SELECT * FROM (SELECT CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname,
            a.employee_idno, a.date as date, a.time_in as time_in, a.time_out as time_out,
            b.description as worksite, 'timelog' as status
            FROM time_record_summary_trial a
            INNER JOIN worksite b ON a.worksite = b.worksiteid
            INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
            WHERE a.enabled = 1 AND c.enabled = 1 AND b.enabled = 1
            UNION
            SELECT CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname,
            b.employee_idno, a.date as date, a.start_time as time_in, a.end_time as time_out,
            d.description as worksite, 'workorder' as status
            FROM work_order a
            INNER JOIN employee_record b ON a.employee_id = b.employee_idno
            INNER JOIN contract c ON b.id = c.contract_emp_id
            INNER JOIN worksite d ON c.work_site_id = d.worksiteid
            WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1
              AND a.status = 'certified' AND c.contract_status = 'active') as timelog ORDER BY date ASC, fullname ASC, time_in ASC";

    if($from && $to){
      $from = $this->db->escape($from);
      $to = $this->db->escape($to);
      // $sql .= " AND a.date BETWEEN $from AND $to";
      $sql = "SELECT * FROM (SELECT CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname,
              a.employee_idno, a.date as date, a.time_in as time_in, a.time_out as time_out,
              b.description as worksite, 'timelog' as status
              FROM time_record_summary_trial a
              INNER JOIN worksite b ON a.worksite = b.worksiteid
              INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
              WHERE a.enabled = 1 AND c.enabled = 1 AND b.enabled = 1 AND a.date BETWEEN $from AND $to
              UNION
              SELECT CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname,
              b.employee_idno, a.date as date, a.start_time as time_in, a.end_time as time_out,
              d.description as worksite, 'workorder' as status
              FROM work_order a
              INNER JOIN employee_record b ON a.employee_id = b.employee_idno
              INNER JOIN contract c ON b.id = c.contract_emp_id
              INNER JOIN worksite d ON c.work_site_id = d.worksiteid
              WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1
                AND a.status = 'certified' AND c.contract_status = 'active'
                AND a.date BETWEEN $from AND $to) as timelog ORDER BY date ASC, fullname ASC, time_in ASC";
    }

    if($id != ""){
      $id = $this->db->escape($id);
      // $sql .= " AND a.employee_idno = $id";
      $sql = "SELECT * FROM (SELECT CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name) as fullname,
              a.employee_idno, a.date as date, a.time_in as time_in, a.time_out as time_out,
              b.description as worksite, 'timelog' as status
              FROM time_record_summary_trial a
              INNER JOIN worksite b ON a.worksite = b.worksiteid
              INNER JOIN employee_record c ON a.employee_idno = c.employee_idno
              WHERE a.enabled = 1 AND c.enabled = 1 AND b.enabled = 1 AND a.date BETWEEN $from AND $to
              AND a.employee_idno = $id
              UNION
              SELECT CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname,
              b.employee_idno, a.date as date, a.start_time as time_in, a.end_time as time_out,
              d.description as worksite, 'workorder' as status
              FROM work_order a
              INNER JOIN employee_record b ON a.employee_id = b.employee_idno
              INNER JOIN contract c ON b.id = c.contract_emp_id
              INNER JOIN worksite d ON c.work_site_id = d.worksiteid
              WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1
                AND a.status = 'certified' AND c.contract_status = 'active'
                AND a.date BETWEEN $from AND $to AND b.employee_idno = $id) as timelog ORDER BY date ASC, fullname ASC, time_in ASC";
    }

    return $this->db->query($sql);
  }

  public function get_biometrics_id(){
    $sql = "SELECT a.bio_id, a.employee_idno FROM hris_biometrics_id a
    INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
    WHERE a.enabled = 1 AND b.enabled = 1 AND a.status = 'active'";
    return $this->db->query($sql);
  }

  public function get_emp_w_bio_timelog($data){
    $sql = "SELECT a.* FROM time_record_summary_trial a
     INNER JOIN hris_biometrics_id b ON a.employee_idno = b.employee_idno
     WHERE a.enabled = 1 AND b.enabled = 1 AND a.date = ? AND b.bio_id = ? ORDER BY id ASC";
    return $this->db->query($sql,$data);
  }

  public function countTimeLogReports(){
    $sql = "SELECT COUNT(a.id) as totalLogReports FROM time_record_summary_trial a
            LEFT JOIN worksite b ON a.worksite = b.worksiteid
            LEFT JOIN employee_record c ON a.employee_idno = c.employee_idno
            WHERE a.enabled = 1";
    return $this->db->query($sql);
  }

  public function get_work_site(){
    $sql = "SELECT worksiteid, description FROM worksite WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function set_import_data($data){
    // $this->insert_batch('');
  }

  public function set_timelog_logs($data){
    $this->db->insert('hris_timelog_logs',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_timelog_w_bio($data,$id){
    $this->db->update('time_record_summary_trial', $data, array('id' => $id));
    // return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_first_timein($data){
    $sql = "UPDATE time_record_summary_trial SET time_in = ? WHERE id = ? AND enabled = 1";
    $this->db->query($sql,$data);
  }

  public function update_last_timeout($data){
    $sql = "UPDATE time_record_summary_trial SET time_out = ? WHERE id = ? AND enabled = 1";
    $this->db->query($sql,$data);
  }

  public function update_timerecord($id,$data){
    $this->db->update('time_record_summary_trial', $data, array("id" => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_timerecord_status($id,$status = 0){
    $data = array("enabled" => $status);
    $this->db->update('time_record_summary_trial',$data,array("id" => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_workorder_time($id,$data){
    $this->db->update('work_order', $data, array("id" => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_workorder_status($id,$status = 0){
    $data = array("enabled" => $status);
    $this->db->update('work_order',$data,array("id" => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
