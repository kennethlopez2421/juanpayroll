<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Timerecord_summary_model extends CI_Model {
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

  public function get_timerecord_summary_json($search){
    $requestData = $_REQUEST;
    $raw_from = $search->from;
    $raw_to = $search->to;

    $nightdiff_status = 'off';
    $night_diff_sql = "SELECT * FROM hris_nightdiff_settings WHERE enabled = 1";
    $nightdiff = $this->db->query($night_diff_sql);
    if($nightdiff->num_rows() > 0){
      $nightdiff = $nightdiff->row_array();
      $nightdiff_status = $nightdiff['status'];
    }

    $columns = array(
      0 => "first_in",
      1 => "last_out",
      2 => "date",
      3 => "employee_idno",
      4 => "fullname",
      5 => "man_hours",
      6 => "lates",
      7 => "overbreak",
      8 => "undertime",
      9 => "absent",
      10 => "total_minutes",
      11 => "remarks"
    );

    // CHECK IF LAST DAY FROM DATE RANGE IS TODAY
    if($search->to >= today()){
      $new_date = new Datetime(today());
      $new_date2 = $new_date->modify('-1 day');

      $from = $this->db->escape($search->from);
      $to = $this->db->escape($new_date2->format('Y-m-d'));
      $raw_to = $new_date2->format('Y-m-d');
    }else{
      $from = $this->db->escape($search->from);
      $to = $this->db->escape($search->to);
    }

    // CUSTOM WORK SCHEDULE
    $ws_sql = "SELECT * FROM hris_custom_schedule WHERE status = 'certify' AND enabled = 1
     AND $from <= date_to && $to >= date_from";
    $ws_query = $this->db->query($ws_sql);

    // OFFSET
    $offset_sql = "SELECT * FROM hris_offset WHERE status = 'certified' AND enabled = 1
     AND date_rendered BETWEEN $from AND $to";
    $offset_query = $this->db->query($offset_sql);

    // SELECT ALL EMPLOYEE THAT SELECTED FROM DATE RANGE
    $sql = "SELECT DISTINCT(c.employee_idno), d.work_sched, d.break_sched, d.total_whours, d.total_bhours,
     d.sched_type, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, b.contract_start,
     @whours := (d.total_whours - d.total_bhours) as whours, e.deptId
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN time_record_summary_trial c ON a.employee_idno = c.employee_idno
     INNER JOIN work_schedule d ON  b.work_sched_id = d.id
     LEFT JOIN position e ON b.position_id = e.positionid
     WHERE c.date BETWEEN $from AND $to AND a.enabled = 1 AND b.contract_status = 'active'
     AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1";
    // GET ALL SELECTED EMPLOYEE IDNUMBER
    $employee_idnos = array();
    $query = $this->db->query($sql)->result_array();

    if(count($query) == 0){
      $json_data = array(
        "recordsTotal"    => 0,
        "recordsFiltered" => 0,
        "data"            => array()
      );

      return $json_data;
    }

    foreach($query as $row){
      $employee_idnos[] = $this->db->escape($row['employee_idno']);
    }
    $emp_idno = implode(',',$employee_idnos);

    // GET ALL TIMELOG AND WORK ORDER OF ALL SELECTED EMPLOYEE
    $sql2 = "SELECT * FROM (
      SELECT a.id as time_id, a.employee_idno as employee_idno, a.time_in as time_in, a.time_out as time_out,
      a.date as timelog_date, 'timelog' as type
      FROM time_record_summary_trial a WHERE a.date BETWEEN $from AND $to AND a.employee_idno IN ($emp_idno) AND a.enabled = 1 AND a.time_in IS NOT NULL AND a.time_out IS NOT NULL
      UNION
      SELECT b.id as time_id, b.employee_id as employee_idno,  b.start_time as time_in, b.end_time as time_out,
      b.date as timelog_date, 'work order' as type
      FROM work_order b WHERE b.date BETWEEN $from AND $to AND b.employee_id IN ($emp_idno) AND b.status = 'certified'
      AND b.enabled = 1 AND b.start_time IS NOT NULL AND b.end_time IS NOT NULL
    ) as timelog ORDER BY timelog_date ASC, time_in ASC";

    $query2 = $this->db->query($sql2)->result_array();
    // print_r($this->db->last_query());
    $data = array();

    foreach($query as $row){
      $real_row = $row;
      $employee_idno = $row['employee_idno'];
      $deptId = $row['deptId'];
      $fullname = $row['fullname'];
      $total_whours = $row['total_whours'];
      $total_bhours = $row['total_bhours'];
      $min_total_whours = ($total_whours - $total_bhours) * 60;
      $sched_type = $row['sched_type'];
      $worksched = json_decode($row['work_sched']);
      // $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $contract_start = $row['contract_start'];

      $sdate = new Datetime($raw_from);
      $edate = new Datetime($raw_to);
      for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
        $timelog_date = $x->format('Y-m-d');
        $ldate = $this->db->escape($timelog_date);
        $lemp_idnum = $this->db->escape($employee_idno);
        $day = strtolower($x->format('D'));

        if($ws_query->num_rows() > 0){
          $ws = $ws_query->result_array();
          $cond = array('id' => $employee_idno, 'dept' => $deptId, 'date' => $timelog_date);
          $custom_ws = filter_workschedule($ws,$cond);
          if($custom_ws != ''){
            $worksched = json_decode($custom_ws['work_sched']);
          }
        }

        if($offset_query->num_rows() > 0){
          $off = $offset_query->result_array();
          $cond = array('id' => $employee_idno, 'date' => $timelog_date);
          $offset = filter_offset($off,$cond);
        }else{
          $offset = array('late' => 0, 'undertime' => 0, 'wholeday' => 0, 'halfday' => 0);
        }

        $worksched = (array)$worksched;
        $leave_sql = "SELECT * FROM leave_tran WHERE employee_idno = $lemp_idnum
                      AND $ldate BETWEEN date_from AND date_to
                      AND status = 'certified' AND enabled = 1";
        $check_leave = $this->db->query($leave_sql);

        for($i = 0; $i < 7; $i++){
          if($day == $days[$i]){
            if($worksched[$days[$i]][0] != ""){
              $filter_by = array("id" => $employee_idno, "date" => $timelog_date);
              $filter_array = filter_array($query2,$filter_by);
              $inout = array();
              foreach($filter_array as $time){
                $inout[] = strtotime($time['time_in']);
                $inout[] = strtotime($time['time_out']);
              }
              sort($inout);

              $nestedData = array();
              $timelog_data = array(
                "employee_idno" => $employee_idno,
                "total_whours" => $total_whours,
                "total_bhours" => $total_bhours,
                "sched_type" => $sched_type,
                "stime_in" => $worksched[$days[$i]][0],
                "stime_out" => $worksched[$days[$i]][1],
                "sbreak_in" => $worksched[$days[$i]][3],
                "sbreak_out" => $worksched[$days[$i]][4],
                "timelog" => "",
                "first_in" => "",
                "last_out" => ""
              );

              $grace_data = array(
                "late" => $this->late_grace_period,
                "undertime" => $this->undertime_grace_period,
                "overbreak" => $this->overbreak_grace_period,
                "offset_late" => $offset['late'],
                "offset_undertime" => $offset['undertime'],
                "offset_wholeday" => $offset['wholeday'],
                "offset_halfday" => $offset['halfday']
              );
              // print_r($grace_data);
              if(count($filter_array) > 0){

                $count = count($filter_array) - 1;
                $count = count($inout) - 1;
                $first_time_in = date('H:i:s',$inout[0]);
                $last_time_out = date('H:i:s',$inout[$count]);
                $timelog_data['timelog'] = $filter_array;
                $timelog_data['first_in'] = $first_time_in;
                $timelog_data['last_out'] = $last_time_out;
                $timelog = compute_timelog($timelog_data,'all',$grace_data,$nightdiff_status);

                if($timelog['total_minutes'] > 0){
                  $nestedData[] = $first_time_in;
                  $nestedData[] = $last_time_out;
                  $nestedData[] = $timelog_date;
                  $nestedData[] = $employee_idno;
                  $nestedData[] = $fullname;
                  $nestedData[] = $timelog['manhours'];
                  $nestedData[] = $timelog['night_diff'];
                  $nestedData[] = $timelog['late'];
                  $nestedData[] = $timelog['overbreak'];
                  $nestedData[] = $timelog['undertime'];
                  $nestedData[] = 0;
                  $nestedData[] = $timelog['total_minutes'];
                  $nestedData[] = 'Timelog';
                }else{
                  $nestedData[] = $first_time_in;
                  $nestedData[] = $last_time_out;
                  $nestedData[] = $timelog_date;
                  $nestedData[] = $employee_idno;
                  $nestedData[] = $fullname;
                  $nestedData[] = ($check_leave->num_rows() > 0) ? number_format($row['whours'],2) : 0;
                  $nestedData[] = 0;
                  $nestedData[] = 0;
                  $nestedData[] = 0;
                  $nestedData[] = 0;
                  $nestedData[] = ($check_leave->num_rows() > 0) ? 1 : 1;
                  $nestedData[] = $timelog['total_minutes'];
                  $nestedData[] = ($check_leave->num_rows() > 0) ? 'Leave' : 'Absent';
                }
                $data[] = $nestedData;
              }else{
                if($timelog_date >= $contract_start){
                  // print_r($grace_data);
                  if($grace_data['offset_wholeday'] > 0 && $grace_data['offset_wholeday'] >= $min_total_whours){
                    $nestedData[] = '--:--';
                    $nestedData[] = '--:--';
                    $nestedData[] = $timelog_date;
                    $nestedData[] = $employee_idno;
                    $nestedData[] = $fullname;
                    $nestedData[] = $real_row['whours'];
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = $grace_data['offset_wholeday'];
                    $nestedData[] = 'Offset Wholeday';
                  }else if($grace_data['offset_halfday'] > 0 && $grace_data['offset_halfday'] >= (round($real_row['whours'] / 2, 1) * 60)){
                    $nestedData[] = '--:--';
                    $nestedData[] = '--:--';
                    $nestedData[] = $timelog_date;
                    $nestedData[] = $employee_idno;
                    $nestedData[] = $fullname;
                    $nestedData[] = round($real_row['whours'] / 2,1);
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = round($real_row['whours'] / 2,1) * 60;
                    $nestedData[] = 0;
                    $nestedData[] = $grace_data['offset_halfday'];
                    $nestedData[] = 'Offset Halfday';
                  }else{
                    $nestedData[] = '--:--';
                    $nestedData[] = '--:--';
                    $nestedData[] = $timelog_date;
                    $nestedData[] = $employee_idno;
                    $nestedData[] = $fullname;
                    $nestedData[] = ($check_leave->num_rows() > 0) ? number_format($real_row['whours'],2) : 0;
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = 0;
                    $nestedData[] = ($check_leave->num_rows() > 0) ? 1: 1;
                    $nestedData[] = 0;
                    $nestedData[] = ($check_leave->num_rows() > 0) ? 'Leave' : 'Absent';
                  }
                  $data[] = $nestedData;
                }
              }

            }else{
              $filter_by = array("id" => $employee_idno, "date" => $timelog_date);
              $filter_array = filter_array($query2,$filter_by);
              if(count($filter_array) > 0){
                foreach($filter_array as $row){
                  if($row['type'] == 'work order'){
                    $nestedData = array();
                    $timelog_data = array(
                      "employee_idno" => $employee_idno,
                      "total_whours" => $total_whours,
                      "total_bhours" => $total_bhours,
                      "sched_type" => 'flexi',
                      "stime_in" => $row['time_in'],
                      "stime_out" => $row['time_out'],
                      "sbreak_in" => '12:00',
                      "sbreak_out" => '13:00',
                      "timelog" => "",
                      "first_in" => "",
                      "last_out" => ""
                    );

                    $grace_data = array(
                      "late" => $this->late_grace_period,
                      "undertime" => $this->undertime_grace_period,
                      "overbreak" => $this->overbreak_grace_period
                    );

                    $count = count($filter_array) - 1;
                    $first_time_in = $filter_array[0]['time_in'];
                    $last_time_out = $filter_array[$count]['time_out'];
                    $timelog_data['timelog'] = $filter_array;
                    $timelog_data['first_in'] = $first_time_in;
                    $timelog_data['last_out'] = $last_time_out;
                    $timelog = compute_timelog($timelog_data,'all',$grace_data,$nightdiff_status);

                    if($timelog['total_minutes'] > 0){
                      $nestedData[] = $first_time_in;
                      $nestedData[] = $last_time_out;
                      $nestedData[] = $timelog_date;
                      $nestedData[] = $employee_idno;
                      $nestedData[] = $fullname;
                      $nestedData[] = $timelog['manhours'];
                      $nestedData[] = $timelog['night_diff'];
                      $nestedData[] = $timelog['late'];
                      $nestedData[] = $timelog['overbreak'];
                      $nestedData[] = $timelog['undertime'];
                      $nestedData[] = 0;
                      $nestedData[] = $timelog['total_minutes'];
                      $nestedData[] = 'Work Order';
                    }

                    $data[] = $nestedData;
                  }
                }
              }
            }

          }
        }
      }

    }

    $json_data = array(
      "recordsTotal"    => count((array)$data),
      "recordsFiltered" => count((array)$data),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_timerecord_summary($data){
    $from = $this->db->escape($data->from);
    $to = $this->db->escape($data->to);
    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, b.*
      FROM employee_record a INNER JOIN time_record_summary b ON a.employee_idno = b.employee_idno
      WHERE b.enabled = 1";

    if($data->emp_idno != ""){
      $id = $this->db->escape($data->emp_idno);
      $sql .= " AND b.employee_idno = $id";
    }

    if($data->emp_name != ""){
      $emp_name = $this->db->escape('%'.$data->emp_name.'%');
      $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
    }

    return $this->db->query($sql);
  }

  public function get_rules(){
    $sql = "SELECT * FROM hris_clockinout_settings WHERE enabled = 1 AND status = 'on'";
    return $this->db->query($sql);
  }

  public function set_timerecord_summary_batch($data){
    $this->db->insert_batch('time_record_summary',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function truncate(){
    $sql = "TRUNCATE TABLE time_record_summary";
    $this->db->query($sql);
    return true;
    // return ($this->db->affected_rows() > 0) ? true: false;
  }
}
