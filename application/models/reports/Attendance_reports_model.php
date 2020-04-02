<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Attendance_reports_model extends CI_Model {
  public function __construct(){
    parent::__construct();
    $rules = $this->get_rules();
    if($rules->num_rows() > 0){
      foreach($rules->result() as $rule){
        switch ($rule->type) {
          case 'late':
            $this->late_grace_period = $rule->minutes;
            break;
          case 'undertime':
            $this->undertime_grace_period = $rule->minutes;
            break;
          case 'over_break':
            $this->overbreak_grace_period = $rule->minutes;
            break;
          default:
            $this->late_grace_period = 0;
            $this->undertime_grace_period = 0;
            $this->overbreak_grace_period = 0;
            break;
        }
      }
    }else{
      $this->late_grace_period = 0;
      $this->undertime_grace_period = 0;
      $this->overbreak_grace_period = 0;
    }
  }

  public $late_grace_period;

  public $undertime_grace_period;

  public $overbreak_grace_period;

  public function get_attendance_reports_absent($search1,$search2){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $date = ($search1->search == "")
    ? today()
    : $search1->search;
    $d = $this->db->escape($date);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name, b.contract_start,
            b.contract_end,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            LEFT JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND $d BETWEEN b.contract_start AND b.contract_end";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();
    // return $totalData;

    // $sql.=" ORDER BY last_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    // $query = $this->db->query($sql);
    $data_main = array();
    $data = array();

    foreach( $query->result_array() as $row )
    {
      $escape_date = $this->db->escape($date);
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();
      // $emp_id = $this->db->escape($row['employee_idno']);

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];
      $day = new Datetime($date);
      $day = strtolower($day->format('D'));

      for($i = 0; $i < 7; $i++){
        if($day == $days[$i]){
          if($worksched[$days[$i]][0] != ""){
            $sql2 = "SELECT * FROM (
              SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
              a.date as timelog_date, 'timelog' as type
              FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
              UNION
              SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
              b.date as timelog_date, 'work order' as type
              FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
              AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
            ) as timelog ORDER BY timelog_date ASC, time_in ASC";

            $query2 = $this->db->query($sql2);

            if($query2->num_rows() == 0){
              $nestedData[] = $row['employee_idno'];
              $nestedData[] = $row['fullname'];
              $nestedData[] = $row['dept'];
              $nestedData[] = $row['position'];
              $nestedData[] = '<span class="float-right">1</span>';
              $nestedData[] = '<span class="float-right">0.00</span>';
              $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Absent</span></center>';

              $data[] = $nestedData;
            }
          }
        }
      }

      // $absent = fetch_absent($row['employee_idno'],$date,'others');
      // if($absent != ""){
      //   $nestedData[] = $row['employee_idno'];
      //   $nestedData[] = $row['fullname'];
      //   $nestedData[] = $row['dept'];
      //   $nestedData[] = $row['position'];
      //   $nestedData[] = '<span class="float-right">1</span>';
      //   $nestedData[] = '<span class="float-right">0.00</span>';
      //   $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Absent</span></center>';
      //
      //   $data[] = $nestedData;
      // }
    }
    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(

      // "recordsTotal"    => intval( $totalData ),
      "recordsTotal"    => count((array)$data),
      // "recordsFiltered" => intval( $totalFiltered ),
      "recordsFiltered" => count((array)$data),
      "data"            => $data
      // "start" => $requestData['start'],
      // "end" => $requestData['length']
      // "request" => $requestData
    );

    // return $data_main;
    return $json_data;
  }

  public function get_attendance_reports_late($search1,$search2){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $date = ($search1->search == "")
    ? today()
    : $search1->search;
    $d = $this->db->escape($date);
    // return $date;
    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name, b.contract_start,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND $d BETWEEN b.contract_start AND b.contract_end";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $data = array();
    $data_main = array();

    $grace_data = array(
      "late" => $this->late_grace_period,
      "undertime" => $this->undertime_grace_period,
      "overbreak" => $this->overbreak_grace_period
    );

    foreach( $query->result_array() as $row )
    {
      // $late = compute_late($row['employee_idno'],$date,'others');
      $escape_date = $this->db->escape($date);
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];
      $day = new Datetime($date);
      $day = strtolower($day->format('D'));

      for($i = 0; $i < 7; $i++){
        if($day == $days[$i]){
          if($worksched[$days[$i]][0] != ""){
            $sql2 = "SELECT * FROM (
              SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
              a.date as timelog_date, 'timelog' as type
              FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
              UNION
              SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
              b.date as timelog_date, 'work order' as type
              FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
              AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
            ) as timelog ORDER BY timelog_date ASC, time_in ASC";

            $query2 = $this->db->query($sql2);

            if($query2->num_rows() > 0){
              $timelog = $query2->result_array();

              // print_r($timelog);
              $timelog_data = array(
                "employee_idno" => $row['employee_idno'],
                "total_whours" => $total_whours,
                "total_bhours" => $total_bhours,
                "sched_type" => $sched_type,
                "stime_in" => $worksched[$days[$i]][0],
                "stime_out" => $worksched[$days[$i]][1],
                "sbreak_in" => $worksched[$days[$i]][3],
                "sbreak_out" => $worksched[$days[$i]][4],
                "timelog" => $timelog,
                "first_in" => $timelog[0]['time_in'],
                "last_out" => end($timelog)['time_out']
              );

              $late = compute_timelog($timelog_data,'late',$grace_data);
              if($late > 0){
                $nestedData[] = $row['employee_idno'];
                $nestedData[] = $row['fullname'];
                $nestedData[] = $row['dept'];
                $nestedData[] = $row['position'];
                $nestedData[] = '<span class="float-right">0</span>';
                $nestedData[] = '<span class="float-right">'.$late.'</span>';
                $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Late</span></center>';

                $data[] = $nestedData;
              }
            }
          }
        }
      }


    }

    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(

      "recordsTotal"    => /*intval( $totalData )*/ count((array)$data),
      "recordsFiltered" => /*intval( $totalFiltered )*/ count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_attendance_reports_overtime($search1,$search2){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $date = $this->db->escape($search1->search);
    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.minutes_of_overtime as ot_min
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN overtime_pays f ON a.employee_idno = f.employee_id
            WHERE a.enabled = 1 AND b.contract_status = 'active'
            AND f.date_rendered = $date AND f.status = 'certified'
            AND f.type = 'overtime'";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // $sql.=" ORDER BY f.date_rendered DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";
    $sql.=" ORDER BY f.date_rendered DESC";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['position'];
      $nestedData[] = '<span class="float-right">0</span>';
      $nestedData[] = '<span class="float-right">'.$row['ot_min'].'</span>';
      $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-warning">Overtime</span></center>';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_attendance_reports_undertime($search1,$search2){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $date = ($search1->search == "")
    ? today()
    : $search1->search;
    $d = $this->db->escape($date);


    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name, b.contract_start,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND $d BETWEEN b.contract_start AND b.contract_end";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // $sql.=" ORDER BY last_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";
    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    // $query = $this->db->query($sql);

    // return $this->db->last_query();

    $data = array();
    $data_main = array();

    $grace_data = array(
      "late" => $this->late_grace_period,
      "undertime" => $this->undertime_grace_period,
      "overbreak" => $this->overbreak_grace_period
    );

    foreach( $query->result_array() as $row )
    {
      // return $undertime;
      // $undertime = compute_undertime($row['employee_idno'],$date,'others');
      $escape_date = $this->db->escape($date);
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];
      $day = new Datetime($date);
      $day = strtolower($day->format('D'));

      for($i = 0; $i < 7; $i++){
        if($day == $days[$i]){
          if($worksched[$days[$i]][0] != ""){
            $sql2 = "SELECT * FROM (
              SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
              a.date as timelog_date, 'timelog' as type
              FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
              UNION
              SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
              b.date as timelog_date, 'work order' as type
              FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
              AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
            ) as timelog ORDER BY timelog_date ASC, time_in ASC";

            $query2 = $this->db->query($sql2);

            if($query2->num_rows() > 0){
              $timelog = $query2->result_array();

              // print_r($timelog);
              $timelog_data = array(
                "employee_idno" => $row['employee_idno'],
                "total_whours" => $total_whours,
                "total_bhours" => $total_bhours,
                "sched_type" => $sched_type,
                "stime_in" => $worksched[$days[$i]][0],
                "stime_out" => $worksched[$days[$i]][1],
                "sbreak_in" => $worksched[$days[$i]][3],
                "sbreak_out" => $worksched[$days[$i]][4],
                "timelog" => $timelog,
                "first_in" => $timelog[0]['time_in'],
                "last_out" => end($timelog)['time_out']
              );

              $undertime = compute_timelog($timelog_data,'undertime',$grace_data);
              if($undertime > 0){
                $nestedData[] = $row['employee_idno'];
                $nestedData[] = $row['fullname'];
                $nestedData[] = $row['dept'];
                $nestedData[] = $row['position'];
                $nestedData[] = '<span class="float-right">0</span>';
                $nestedData[] = '<span class="float-right">'.$undertime.'</span>';
                $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Undertime</span></center>';

                $data[] = $nestedData;
              }
            }
          }
        }
      }

    }

    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(

      "recordsTotal"    => /*intval( $totalData )*/ count((array)$data),
      "recordsFiltered" => /*intval( $totalFiltered )*/ count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_attendance_reports_most_absent($search1,$search2){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $search1->from;
    $to = $search1->to;
    $f = $this->db->escape($from);
    $t = $this->db->escape($to);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type, b.contract_start
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND b.contract_start < $t AND b.contract_end > $f AND f.enabled = 1";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // $sql.=" ORDER BY a.last_name DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    // $query = $this->db->query($sql);

    $data = array();
    $data_main = array();

    foreach( $query->result_array() as $row )
    {

      $total_absent = 0;
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];
      $sdate = new Datetime($from);
      $edate = new Datetime($to);

      for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
        $timelog_date = $x->format('Y-m-d');
        $escape_date = $this->db->escape($timelog_date);
        $day = strtolower($x->format('D'));
        for($i = 0; $i < 7; $i++){
          if($day == $days[$i]){
            if($worksched[$days[$i]][0] != ""){
              $sql2 = "SELECT * FROM (
                SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
                a.date as timelog_date, 'timelog' as type
                FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
                UNION
                SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
                b.date as timelog_date, 'work order' as type
                FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
                AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
              ) as timelog ORDER BY timelog_date ASC, time_in ASC";

              $query2 = $this->db->query($sql2);

              if($query2->num_rows() == 0){
                $total_absent += 1;
              }
            }
          }
        }
      }
      // return $total_absent;
      if($total_absent > 0){
        $nestedData[] = $row['employee_idno'];
        $nestedData[] = $row['fullname'];
        $nestedData[] = $row['dept'];
        $nestedData[] = $row['position'];
        $nestedData[] = $total_absent;
        $nestedData[] = '<span class="float-right">0</span>';
        $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Most Absent</span></center>';

        $data[] = $nestedData;
      }
      // $sdate = new Datetime($from);
      // $edate = new Datetime($to);
      // $purpose = "others";


    }
    array_multisort( array_column($data, 4), SORT_DESC, $data );
    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    // return $date_array;

    $json_data = array(

      "recordsTotal"    => /*intval( $totalData )*/ count((array)$data),
      "recordsFiltered" => /*intval( $totalFiltered )*/ count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_attendance_reports_most_overtime($search1,$search2){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $this->db->escape($search1->from);
    $to = $this->db->escape($search1->to);

    $date = $this->db->escape($search1->search);
    $sql = "SELECT SUM(f.minutes_of_overtime) as total_overtime, a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.minutes_of_overtime as ot_min
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN overtime_pays f ON a.employee_idno = f.employee_id
            WHERE a.enabled = 1 AND b.contract_status = 'active'
            AND f.date_rendered BETWEEN $from AND $to AND f.status = 'certified'
            AND f.type = 'overtime'";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND ((CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name))";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // $sql.=" GROUP BY a.employee_idno ORDER BY total_overtime DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";
    $sql.=" GROUP BY a.employee_idno ORDER BY total_overtime DESC";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['dept'];
      $nestedData[] = $row['position'];
      $nestedData[] = '<span class="float-right">0</span>';
      $nestedData[] = '<span class="float-right">'.$row['total_overtime'].'</span>';
      $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Overtime</span></center>';

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_attendance_reports_most_late($search1,$search2){
    $requestData = $_REQUEST;

    $from = $search1->from;
    $to = $search1->to;

    $f = $this->db->escape($from);
    $t = $this->db->escape($to);

    $sql = "SELECT a.employee_idno,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type, b.contract_start
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND b.contract_start < $t AND b.contract_end > $f AND f.enabled = 1";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $data = array();
    $data_main = array();

    $grace_data = array(
      "late" => $this->late_grace_period,
      "undertime" => $this->undertime_grace_period,
      "overbreak" => $this->overbreak_grace_period
    );

    foreach( $query->result_array() as $row )
    {

      $total_late = 0;
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];
      $sdate = new Datetime($from);
      $edate = new Datetime($to);
      for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
        $timelog_date = $x->format('Y-m-d');
        $escape_date = $this->db->escape($timelog_date);
        $day = strtolower($x->format('D'));
        for($i = 0; $i < 7; $i++){
          if($day == $days[$i]){
            if($worksched[$days[$i]][0] != ""){
              $sql2 = "SELECT * FROM (
                SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
                a.date as timelog_date, 'timelog' as type
                FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
                UNION
                SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
                b.date as timelog_date, 'work order' as type
                FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
                AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
              ) as timelog ORDER BY timelog_date ASC, time_in ASC";

              $query2 = $this->db->query($sql2);

              if($query2->num_rows() > 0){
                $timelog = $query2->result_array();

                // print_r($timelog);
                $timelog_data = array(
                  "employee_idno" => $row['employee_idno'],
                  "total_whours" => $total_whours,
                  "total_bhours" => $total_bhours,
                  "sched_type" => $sched_type,
                  "stime_in" => $worksched[$days[$i]][0],
                  "stime_out" => $worksched[$days[$i]][1],
                  "sbreak_in" => $worksched[$days[$i]][3],
                  "sbreak_out" => $worksched[$days[$i]][4],
                  "timelog" => $timelog,
                  "first_in" => $timelog[0]['time_in'],
                  "last_out" => end($timelog)['time_out']
                );

                $total_late += compute_timelog($timelog_data,'late',$grace_data);
              }
            }
          }
        }
      }

      if($total_late > 0){
        $nestedData[] = $row['employee_idno'];
        $nestedData[] = $row['fullname'];
        $nestedData[] = $row['dept'];
        $nestedData[] = $row['position'];
        $nestedData[] = '<span class="float-right">0</span>';
        $nestedData[] = $total_late;
        $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Late</span></center>';

        $data[] = $nestedData;
      }
    }

    array_multisort( array_column($data, (int)5), SORT_DESC, $data );
    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(

      "recordsTotal"    => /*intval( $totalData )*/ count((array)$data),
      "recordsFiltered" => /*intval( $totalFiltered )*/ count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_attendance_reports_most_undertime($search1,$search2){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );

    $from = $search1->from;
    $to = $search1->to;
    $f = $this->db->escape($from);
    $t = $this->db->escape($to);

    $sql = "SELECT a.employee_idno,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type, b.contract_start
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND b.contract_start < $t AND b.contract_end > $f AND f.enabled = 1";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $data = array();
    $data_main = array();

    $grace_data = array(
      "late" => $this->late_grace_period,
      "undertime" => $this->undertime_grace_period,
      "overbreak" => $this->overbreak_grace_period
    );

    foreach( $query->result_array() as $row )
    {

      $total_undertime = 0;
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];
      $sdate = new Datetime($from);
      $edate = new Datetime($to);

      for($x = $sdate; $x <= $edate; $x->modify('+1 day')) {
        $timelog_date = $x->format('Y-m-d');
        $escape_date = $this->db->escape($timelog_date);
        $day = strtolower($x->format('D'));
        for($i = 0; $i < 7; $i++){
          if($day == $days[$i]){
            if($worksched[$days[$i]][0] != ""){
              $sql2 = "SELECT * FROM (
                SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
                a.date as timelog_date, 'timelog' as type
                FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
                UNION
                SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
                b.date as timelog_date, 'work order' as type
                FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
                AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
              ) as timelog ORDER BY timelog_date ASC, time_in ASC";

              $query2 = $this->db->query($sql2);

              if($query2->num_rows() > 0){
                $timelog = $query2->result_array();

                // print_r($timelog);
                $timelog_data = array(
                  "employee_idno" => $row['employee_idno'],
                  "total_whours" => $total_whours,
                  "total_bhours" => $total_bhours,
                  "sched_type" => $sched_type,
                  "stime_in" => $worksched[$days[$i]][0],
                  "stime_out" => $worksched[$days[$i]][1],
                  "sbreak_in" => $worksched[$days[$i]][3],
                  "sbreak_out" => $worksched[$days[$i]][4],
                  "timelog" => $timelog,
                  "first_in" => $timelog[0]['time_in'],
                  "last_out" => end($timelog)['time_out']
                );

                $total_undertime += compute_timelog($timelog_data,'undertime',$grace_data);
              }
            }
          }
        }
      }

      if($total_undertime > 0){
        $nestedData[] = $row['employee_idno'];
        $nestedData[] = $row['fullname'];
        $nestedData[] = $row['dept'];
        $nestedData[] = $row['position'];
        $nestedData[] = '<span class="float-right">0</span>';
        $nestedData[] = $total_undertime;
        $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Undertime</span></center>';

        $data[] = $nestedData;
      }
    }

    array_multisort( array_column($data, 5), SORT_DESC, $data );
    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(

      "recordsTotal"    => /*intval( $totalData )*/ count((array)$data),
      "recordsFiltered" => /*intval( $totalFiltered )*/ count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_attendance_reports_halfday($search1,$search2){
    $requestData = $_REQUEST;

    $from = $search1->from;
    $to = $search1->to;
    $f = $this->db->escape($from);
    $t = $this->db->escape($to);

    $sql = "SELECT a.employee_idno,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type, b.contract_start
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND b.contract_start < $t AND b.contract_end > $f AND f.enabled = 1";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    $data = array();
    $data_main = array();

    foreach( $query->result_array() as $row )
    {

      $sdate = new Datetime($from);
      $edate = new Datetime($to);
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];

      for ($x=$sdate; $x <= $edate ; $x->modify('+1 day')) {
        $timelog_date = $x->format('Y-m-d');
        $escape_date = $this->db->escape($timelog_date);
        $day = strtolower($x->format('D'));

        for($i = 0; $i < 7; $i++){
          if($day == $days[$i]){
            if($worksched[$days[$i]][0] != ""){
              $sql2 = "SELECT * FROM (
                SELECT a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
                a.date as timelog_date, 'timelog' as type
                FROM time_record_summary_trial a WHERE a.date = $escape_date AND a.employee_idno = $escape_emp_idno AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
                UNION
                SELECT b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
                b.date as timelog_date, 'work order' as type
                FROM work_order b WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
                AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
              ) as timelog ORDER BY timelog_date ASC, time_in ASC";

              $query2 = $this->db->query($sql2);

              if($query2->num_rows() > 0){
                $timelog = $query2->result_array();

                // print_r($timelog);
                $timelog_data = array(
                  "employee_idno" => $row['employee_idno'],
                  "total_whours" => $total_whours,
                  "total_bhours" => $total_bhours,
                  "sched_type" => $sched_type,
                  "stime_in" => $worksched[$days[$i]][0],
                  "stime_out" => $worksched[$days[$i]][1],
                  "sbreak_in" => $worksched[$days[$i]][3],
                  "sbreak_out" => $worksched[$days[$i]][4],
                  "timelog" => $timelog,
                  "first_in" => $timelog[0]['time_in'],
                  "last_out" => end($timelog)['time_out']
                );

                $total_minutes = compute_timelog($timelog_data,'total_minutes');
                if($total_minutes <= (($total_whours - $total_bhours) * 60) / 2){
                  $nestedData[] = $row['employee_idno'];
                  $nestedData[] = $row['fullname'];
                  $nestedData[] = $row['dept'];
                  $nestedData[] = $row['position'];
                  $nestedData[] = $timelog_date;
                  $nestedData[] = round($total_minutes / 60, 2);
                  $nestedData[] = '<center><span class="badge badge-pill badge-sm badge-danger">Halfday</span></center>';

                  $data[] = $nestedData;
                }
              }
            }
          }
        }
      }


    }

    array_multisort( array_column($data, (int)4), SORT_ASC, $data );
    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(

      "recordsTotal"    => /*intval( $totalData )*/ count((array)$data),
      "recordsFiltered" => /*intval( $totalFiltered )*/ count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_offday_reports($search1,$search2){
    $requestData = $_REQUEST;

    $from = $search1->from;
    $to = $search1->to;
    $f = $this->db->escape($from);
    $t = $this->db->escape($to);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type, b.contract_start
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND b.contract_start < $t AND b.contract_end > $f AND f.enabled = 1";

    ### sub filter ###
    switch ($search2->filter) {
      case 'divID':
        $emp_id = $this->db->escape($search2->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape($search2->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search2->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divPos':
        $pos_id = $this->db->escape($search2->search);
        $sql .= " AND d.positionid = $pos_id";
        break;
      default:
        break;
    }

    $sql .= " GROUP BY a.employee_idno ORDER BY fullname ASC, b.id DESC";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // $sql.=" ORDER BY a.last_name DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    // $query = $this->db->query($sql);

    $data = array();
    $data_main = array();

    foreach( $query->result_array() as $row )
    {

      $off_day = 0;
      $escape_emp_idno = $this->db->escape($row['employee_idno']);
      $nestedData=array();

      $employee_idno = $row['employee_idno'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];
      $sdate = new Datetime($from);
      $edate = new Datetime($to);

      for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
        $timelog_date = $x->format('Y-m-d');
        $escape_date = $this->db->escape($timelog_date);
        $day = strtolower($x->format('D'));
        for($i = 0; $i < 7; $i++){
          if($day == $days[$i]){
            if($worksched[$days[$i]][0] == ""){
              $sql2 = "SELECT * FROM work_order b
              WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
              AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL";

              $query2 = $this->db->query($sql2);

              if($query2->num_rows() > 0){
                $off_day += 1;
              }
            }
          }
        }
      }
      // return $total_absent;
      if($off_day > 0){
        $nestedData[] = $row['employee_idno'];
        $nestedData[] = $row['fullname'];
        $nestedData[] = $row['dept'];
        $nestedData[] = $row['position'];
        $nestedData[] = $off_day;
        $nestedData[] = '<span class="badge badge-pill badge-danger">Off Day</span>';
        $nestedData[] =
        '
          <center>
            <button class="btn btn-primary btn_view_offday" style = "width:80px;"
              data-view_id = "'.en_dec('en',$row['employee_idno']).'"
              data-from = "'.$from.'"
              data-to = "'.$to.'"
            >
              View
            </button>
          </center>
        ';

        $data[] = $nestedData;
      }
      // $sdate = new Datetime($from);
      // $edate = new Datetime($to);
      // $purpose = "others";


    }
    array_multisort( array_column($data, 5), SORT_DESC, $data );
    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    // return $date_array;

    $json_data = array(

      "recordsTotal"    => /*intval( $totalData )*/ count((array)$data),
      "recordsFiltered" => /*intval( $totalFiltered )*/ count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_offday_reports_breakdown($search){
    $emp_idno = en_dec('dec',$search->view_id);
    $from = $search->from;
    $to = $search->to;

    $escape_emp_idno = $this->db->escape($emp_idno);
    $escape_from = $this->db->escape($from);
    $escape_to = $this->db->escape($to);

    $sql = "SELECT a.employee_idno, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as dept, d.description as position, a.last_name as last_name,
            f.work_sched, f.total_whours, f.total_bhours, f.sched_type, b.contract_start,
            e.departmentid as deptId
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN position d ON b.position_id = d.positionid
            LEFT JOIN department e ON d.deptId = e.departmentid
            INNER JOIN work_schedule f ON b.work_sched_id = f.id
            WHERE a.enabled = 1 AND b.contract_start < $escape_from AND b.contract_end > $escape_to
            AND f.enabled = 1 AND a.employee_idno = $escape_emp_idno";
    $query = $this->db->query($sql)->row_array();
    $worksched = json_decode($query['work_sched']);

    // CHECK FOR CUSTOM WORK SCHEDULE
    $ws_sql = "SELECT * FROM hris_custom_schedule WHERE status = 'certify' AND enabled = 1
     AND $from <= date_to && $to >= date_from";
    $ws_query = $this->db->query($ws_sql);

    $days = array('mon','tue','wed','thu','fri','sat','sun');
    $data = array();
    $sdate = new Datetime($from);
    $edate = new Datetime($to);
    for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
      $timelog_date = $x->format('Y-m-d');
      $escape_date = $this->db->escape($timelog_date);
      $day = strtolower($x->format('D'));
      $nestedData = array();

      if($ws_query->num_rows() > 0){
        $ws = $ws_query->result_array();
        $cond = array('id' => $emp_idno, 'dept' => $row['deptId'], 'date' => $timelog_date);
        $custom_ws = filter_workschedule($ws,$cond);
        if($custom_ws != ''){
          $worksched = json_decode($custom_ws['work_sched']);
        }
      }

      $worksched = (array)$worksched;
      for($i = 0; $i < 7; $i++){
        if($day == $days[$i]){
          if($worksched[$days[$i]][0] == ""){
            $sql2 = "SELECT *,
            @approver_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = b.approved_by) as approver_level,
            @approver_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = b.approved_by) as approver_level_name,
            @certifier_level := (SELECT a.position_id FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = b.certified_by) as certifier_level,
            @certifier_level_name := (SELECT c.position FROM hris_users a INNER JOIN hris_position c ON a.position_id = c.position_id WHERE a.employee_idno = b.certified_by) as certifier_level_name,
            @approver := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = b.approved_by) as approver,
            @approver_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = b.approved_by) as approver_pos,
            @certifier_pos := (SELECT c.description as position FROM employee_record d INNER JOIN contract a ON d.id = a.contract_emp_id INNER JOIN position c ON a.position_id = c.positionid WHERE d.employee_idno = b.certified_by) as certifier_pos,
            @certifier := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as fullname FROM hris_users WHERE employee_idno = b.certified_by) as certifier
            FROM work_order b
            WHERE b.date = $escape_date AND b.employee_id = $escape_emp_idno AND b.status = 'certified'
            AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL";

            $query2 = $this->db->query($sql2);

            if($query2->num_rows() > 0){
              $wo = $query2->row_array();
              $approver_pos = ($wo['approver_level'] <= 2) ? $wo['approver_level_name'] : $wo['approver_pos'];
              $certifier_pos = ($wo['certifier_level'] <= 2) ? $wo['certifier_level_name'] : $wo['certifier_pos'];
              $nestedData[] = $wo['date'];
              $nestedData[] = $wo['start_time']." -- ".$wo['end_time'];
              $nestedData[] = "(".$approver_pos.") ".$wo['approver'];
              $nestedData[] = ".(".$certifier_pos."). ".$wo['certifier'];

              $data[] = $nestedData;
            }
          }
        }
      }
    }

    $totalData = count((array)$data);
    $totalFiltered = count((array)$data);

    $json_data = array(

      "recordsTotal"    => $totalData,
      "recordsFiltered" => $totalFiltered,
      "data"            => $data
    );

    return $json_data;
  }

  public function get_excel_reports($search1,$search2){

    switch ($search1->filter) {
      case 'divAbsent':
        $data = $this->get_attendance_reports_absent($search1,$search2);
        break;
      case 'divLate':
        $data = $this->get_attendance_reports_late($search1,$search2);
        break;
      case 'divOvertime':
        $data = $this->get_attendance_reports_overtime($search1,$search2);
        break;
      case 'divUndertime':
        $data = $this->get_attendance_reports_undertime($search1,$search2);
        break;
      case 'divMostAbsent':
        $data = $this->get_attendance_reports_most_absent($search1,$search2);
        break;
      case 'divMostLate':
        $data = $this->get_attendance_reports_most_late($search1,$search2);
        break;
      case 'divMostOvertime':
        $data = $this->get_attendance_reports_most_overtime($search1,$search2);
        break;
      case 'divMostUndertime':
        $data = $this->get_attendance_reports_most_undertime($search1,$search2);
        break;
      case 'divHalfday':
        $data = $this->get_attendance_reports_halfday($search1,$search2);
        break;
      default:
        $data = $this->get_attendance_reports_absent($search1,$search2);
        break;
    }

    return $data;
  }

  public function get_rules(){
    $sql = "SELECT * FROM hris_clockinout_settings WHERE enabled = 1 AND status = 'on'";
    return $this->db->query($sql);
  }
}
