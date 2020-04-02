<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Attendance extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('attendance_chart/attendance_model');

  }

  public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
  }

  public function isLoggedIn() {
    //this will destroy the session if the user not logged in
    if($this->session->userdata('isLoggedIn') == false) {
      if(empty($this->session->userdata('position_id'))) { //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
      }
    }
  }

  public function get_attendance_json(){
    date_default_timezone_set('Asia/Manila');
    $filter_by = $this->input->post('filter_by');
    $emp_idno = $this->session->userdata('emp_idno');
    $total_whours = $this->input->post('total_whours');
    $total_bhours = $this->input->post('total_bhours');
    $sched_type = $this->input->post('sched_type');
    $worksched = json_decode($this->input->post('worksched'));
    $worksched = (array)$worksched;
    $days = array('mon','tue','wed','thu','fri','sat','sun');

    switch ($filter_by) {
      case 'this_month':
    			$d = new Datetime(today());
    			$month = $d->format('M-Y');

    			$sdate = new Datetime(date('Y-m-d', strtotime('first day of this month')));
    			$edate = new Datetime(today());
    			$day_array = array();
    			$late_array = array();
    			$undertime_array = array();
    			$overbreak_array = array();
    			$total_minutes_array = array();

    			for ($x=$sdate; $x <= $edate ; $x->modify('+1 day')) {
    				$date = $x->format('Y-m-d');
            $day = strtolower($x->format('D'));
            for($i = 0; $i < 7; $i++){
              if($day == $days[$i]){
                if($worksched[$days[$i]][0] != ""){
                  $timelog = $this->model->get_timelog($emp_idno,$date);
                  $timelog = $timelog->result_array();
                  if(count($timelog) > 0){

                    $timelog_data = array(
                      "employee_idno" => $emp_idno,
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

                    $graph = compute_timelog($timelog_data,'all');
                    $late = $graph['late'];
            				$undertime = $graph['undertime'];
            				$overbreak = $graph['overbreak'];
            				$total_min = $graph['total_minutes'];

                    $day_array[] = $x->format('M d');
            				$late_array[] = $late;
            				$undertime_array[] = $undertime;
            				$overbreak_array[] = $overbreak;
            				$total_minutes_array[] = $total_min;
                  }
                }
              }
            }
    			}

    			$days = implode(',',$day_array);
    			$lates = implode(',',$late_array);
    			$undertimes = implode(',',$undertime_array);
    			$overbreaks= implode(',',$overbreak_array);
    			$total_mins = implode(',',$total_minutes_array);

          $data = array
          (
            "success" => 1, "days" => $days, "lates" => $lates,
            "undertimes" => $undertimes, "overbreaks" => $overbreaks,
            "total_mins" => $total_mins, "month" => $month
          );
        break;
      case 'last_3months':
        $month1 = (new DateTime())->modify('first day of this month')->modify('-3 months')->format('M');
        $monthyear1 = (new DateTime())->modify('first day of this month')->modify('-3 months')->format('M-Y');
        $month1_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $month2 = (new DateTime())->modify('first day of this month')->modify('-2 months')->format('M');
        $monthyear2 = (new DateTime())->modify('first day of this month')->modify('-2 months')->format('M-Y');
        $month2_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $month3 = (new DateTime())->modify('first day of this month')->modify('-1 months')->format('M');
        $monthyear3 = (new DateTime())->modify('first day of this month')->modify('-1 months')->format('M-Y');
        $month3_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $last_3months = (new DateTime())->modify('first day of this month')->modify('-3 months')->format('Y-m-d');
        $last_day_of_previous_month = date('Y-m-d',strtotime('last day of previous month'));

        ### arrays ###
        $month_array = array();
        $monthly_late = array();
        $monthly_undertime = array();
        $monthly_overbreak = array();
        $monthly_total_min = array();

        $tm1_arr = array();
        $tm2_arr = array();
        $tm3_arr = array();


        $month_array[] = $monthyear1;
        $month_array[] = $monthyear2;
        $month_array[] = $monthyear3;

        $sdate = new Datetime($last_3months);
        $edate = new Datetime($last_day_of_previous_month);
        $late = 0;
        $undertime = 0;
        $overbreak = 0;
        $total_min = 0;

        for ($x=$sdate; $x < $edate; $x->modify('+1 days')) {
          $date = $x->format('Y-m-d');
          $this_month = $x->format('M');
          $day = strtolower($x->format('D'));
          for($i = 0; $i < 7; $i++){
            if($day == $days[$i]){
              if($worksched[$days[$i]][0] != ""){
                $timelog = $this->model->get_timelog($emp_idno,$date);
                $timelog = $timelog->result_array();
                if(count($timelog) > 0){

                  $timelog_data = array(
                    "employee_idno" => $emp_idno,
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

                  $graph = compute_timelog($timelog_data,'all');
                  $late = $graph['late'];
                  $undertime = $graph['undertime'];
                  $overbreak = $graph['overbreak'];
                  $total_min = $graph['total_minutes'];

                }
              }
            }
          }

          ### 3rd previous month ###
          if($month1 == $this_month){
            $month1_data['late'] += ($late > 0) ? $late : 0;
            $month1_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month1_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month1_data['total_min'] += ($total_min > 0) ? $total_min : 0;
            $tm1_arr[] = $total_min;
          }
          ### 2nd previous month ###
          if($month2 == $this_month){
            $month2_data['late'] += ($late > 0) ? $late : 0;
            $month2_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month2_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month2_data['total_min'] += ($total_min > 0) ? $total_min : 0;
            $tm2_arr[] = $total_min;
          }
          ### 1st previous month ###
          if($month3 == $this_month){
            $month3_data['late'] += ($late > 0) ? $late : 0;
            $month3_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month3_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month3_data['total_min'] += ($total_min > 0) ? $total_min : 0;
            $tm3_arr[] = $total_min;
          }

        }

        $monthly_late[] = $month1_data['late'];
        $monthly_late[] = $month2_data['late'];
        $monthly_late[] = $month3_data['late'];

        $monthly_undertime[] = $month1_data['undertime'];
        $monthly_undertime[] = $month2_data['undertime'];
        $monthly_undertime[] = $month3_data['undertime'];

        $monthly_overbreak[] = $month1_data['overbreak'];
        $monthly_overbreak[] = $month2_data['overbreak'];
        $monthly_overbreak[] = $month3_data['overbreak'];

        $monthly_total_min[] = $month1_data['total_min'];
        $monthly_total_min[] = $month2_data['total_min'];
        $monthly_total_min[] = $month3_data['total_min'];

        // print_r($monthly_late);

        $data = array(
          "success" => 1,
          "months" => implode(',',$month_array),
          "monthly_late" => implode(',',$monthly_late),
          "monthly_overbreak" => implode(',', $monthly_overbreak),
          "monthly_undertime" => implode(',', $monthly_undertime),
          "monthly_total_min" => implode(',', $monthly_total_min),
          "tm1_arr" => $tm1_arr,
          "tm2_arr" => $tm2_arr,
          "tm3_arr" => $tm3_arr
        );
        break;
      case 'last_6months':
        $month1 = (new DateTime())->modify('first day of this month')->modify('-6 months')->format('M');
        $monthyear1 = (new DateTime())->modify('first day of this month')->modify('-6 months')->format('M-Y');
        $month1_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $month2 = (new DateTime())->modify('first day of this month')->modify('-5 months')->format('M');
        $monthyear2 = (new DateTime())->modify('first day of this month')->modify('-5 months')->format('M-Y');
        $month2_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $month3 = (new DateTime())->modify('first day of this month')->modify('-4 months')->format('M');
        $monthyear3 = (new DateTime())->modify('first day of this month')->modify('-4 months')->format('M-Y');
        $month3_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $month4 = (new DateTime())->modify('first day of this month')->modify('-3 months')->format('M');
        $monthyear4 = (new DateTime())->modify('first day of this month')->modify('-3 months')->format('M-Y');
        $month4_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $month5 = (new DateTime())->modify('first day of this month')->modify('-2 months')->format('M');
        $monthyear5 = (new DateTime())->modify('first day of this month')->modify('-2 months')->format('M-Y');
        $month5_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);
        $month6 = (new DateTime())->modify('first day of this month')->modify('-1 months')->format('M');
        $monthyear6 = (new DateTime())->modify('first day of this month')->modify('-1 months')->format('M-Y');
        $month6_data = array("late" => 0, "undertime" => 0, "overbreak" => 0, "total_min" => 0);

        $last_6months = (new DateTime())->modify('first day of this month')->modify('-6 months')->format('Y-m-d');
        $last_day_of_previous_month = date('Y-m-d',strtotime('last day of previous month'));

        ### arrays ###
        $month_array = array();
        $monthly_late = array();
        $monthly_undertime = array();
        $monthly_overbreak = array();
        $monthly_total_min = array();

        $tm1_arr = array();
        $tm2_arr = array();
        $tm3_arr = array();


        $month_array[] = $monthyear1;
        $month_array[] = $monthyear2;
        $month_array[] = $monthyear3;
        $month_array[] = $monthyear4;
        $month_array[] = $monthyear5;
        $month_array[] = $monthyear6;

        $sdate = new Datetime($last_6months);
        $edate = new Datetime($last_day_of_previous_month);
        $late = 0;
        $undertime = 0;
        $overbreak = 0;
        $total_min = 0;

        for ($x=$sdate; $x < $edate; $x->modify('+1 days')) {
          $date = $x->format('Y-m-d');
          $this_month = $x->format('M');
          $day = strtolower($x->format('D'));
          for($i = 0; $i < 7; $i++){
            if($day == $days[$i]){
              if($worksched[$days[$i]][0] != ""){
                $timelog = $this->model->get_timelog($emp_idno,$date);
                $timelog = $timelog->result_array();
                if(count($timelog) > 0){

                  $timelog_data = array(
                    "employee_idno" => $emp_idno,
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

                  $graph = compute_timelog($timelog_data,'all');
                  $late = $graph['late'];
                  $undertime = $graph['undertime'];
                  $overbreak = $graph['overbreak'];
                  $total_min = $graph['total_minutes'];

                }
              }
            }
          }

          ### 6th previous month ###
          if($month1 == $this_month){
            $month1_data['late'] += ($late > 0) ? $late : 0;
            $month1_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month1_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month1_data['total_min'] += ($total_min > 0) ? $total_min : 0;
            $tm1_arr[] = $total_min;
          }
          ### 5th previous month ###
          if($month2 == $this_month){
            $month2_data['late'] += ($late > 0) ? $late : 0;
            $month2_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month2_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month2_data['total_min'] += ($total_min > 0) ? $total_min : 0;
            $tm2_arr[] = $total_min;
          }
          ### 4th previous month ###
          if($month3 == $this_month){
            $month3_data['late'] += ($late > 0) ? $late : 0;
            $month3_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month3_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month3_data['total_min'] += ($total_min > 0) ? $total_min : 0;
            $tm3_arr[] = $total_min;
          }
          ### 3rd previous month ###
          if($month4 == $this_month){
            $month4_data['late'] += ($late > 0) ? $late : 0;
            $month4_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month4_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month4_data['total_min'] += ($total_min > 0) ? $total_min : 0;
          }
          ### 2nd previous month ###
          if($month5 == $this_month){
            $month5_data['late'] += ($late > 0) ? $late : 0;
            $month5_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month5_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month5_data['total_min'] += ($total_min > 0) ? $total_min : 0;
          }
          ### 1st previous month ###
          if($month6 == $this_month){
            $month6_data['late'] += ($late > 0) ? $late : 0;
            $month6_data['undertime'] += ($undertime > 0) ? $undertime : 0;
            $month6_data['overbreak'] += ($overbreak > 0) ? $overbreak : 0;
            $month6_data['total_min'] += ($total_min > 0) ? $total_min : 0;
          }

        }

        $monthly_late[] = $month1_data['late'];
        $monthly_late[] = $month2_data['late'];
        $monthly_late[] = $month3_data['late'];
        $monthly_late[] = $month4_data['late'];
        $monthly_late[] = $month5_data['late'];
        $monthly_late[] = $month6_data['late'];

        $monthly_undertime[] = $month1_data['undertime'];
        $monthly_undertime[] = $month2_data['undertime'];
        $monthly_undertime[] = $month3_data['undertime'];
        $monthly_undertime[] = $month4_data['undertime'];
        $monthly_undertime[] = $month5_data['undertime'];
        $monthly_undertime[] = $month6_data['undertime'];

        $monthly_overbreak[] = $month1_data['overbreak'];
        $monthly_overbreak[] = $month2_data['overbreak'];
        $monthly_overbreak[] = $month3_data['overbreak'];
        $monthly_overbreak[] = $month4_data['overbreak'];
        $monthly_overbreak[] = $month5_data['overbreak'];
        $monthly_overbreak[] = $month6_data['overbreak'];

        $monthly_total_min[] = $month1_data['total_min'];
        $monthly_total_min[] = $month2_data['total_min'];
        $monthly_total_min[] = $month3_data['total_min'];
        $monthly_total_min[] = $month4_data['total_min'];
        $monthly_total_min[] = $month5_data['total_min'];
        $monthly_total_min[] = $month6_data['total_min'];

        // print_r($monthly_late);

        $data = array(
          "success" => 1,
          "months" => implode(',',$month_array),
          "monthly_late" => implode(',',$monthly_late),
          "monthly_overbreak" => implode(',', $monthly_overbreak),
          "monthly_undertime" => implode(',', $monthly_undertime),
          "monthly_total_min" => implode(',', $monthly_total_min),
          "tm1_arr" => $tm1_arr,
          "tm2_arr" => $tm2_arr,
          "tm3_arr" => $tm3_arr
        );
        break;

      default:
        // code...
        break;


    }

    generate_json($data);
  }

  public function get_attendance_breakdown_monthly(){
    date_default_timezone_set('Asia/Manila');
    $emp_idno = $this->session->userdata('emp_idno');
    $month = $this->input->post('month');
    $first_day = (new Datetime($month))->modify('first day of this month')->format('Y-m-d');
    $last_day = (new Datetime($month))->modify('last day of this month')->format('Y-m-d');

    $total_whours = $this->input->post('total_whours');
    $total_bhours = $this->input->post('total_bhours');
    $sched_type = $this->input->post('sched_type');
    $worksched = json_decode($this->input->post('worksched'));
    $worksched = (array)$worksched;
    $days = array('mon','tue','wed','thu','fri','sat','sun');

    $sdate = new Datetime($first_day);
    $edate = new Datetime($last_day);

    $day_array = array();
    $late_array = array();
    $undertime_array = array();
    $overbreak_array = array();
    $total_minutes_array = array();

    for ($x=$sdate; $x <= $edate ; $x->modify('+1 day')) {
      $date = $x->format('Y-m-d');
      $day = strtolower($x->format('D'));
      for($i = 0; $i < 7; $i++){
        if($day == $days[$i]){
          if($worksched[$days[$i]][0] != ""){
            $timelog = $this->model->get_timelog($emp_idno,$date);
            $timelog = $timelog->result_array();
            if(count($timelog) > 0){

              $timelog_data = array(
                "employee_idno" => $emp_idno,
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

              $graph = compute_timelog($timelog_data,'all');
              $late = $graph['late'];
              $undertime = $graph['undertime'];
              $overbreak = $graph['overbreak'];
              $total_min = $graph['total_minutes'];

              $day_array[] = $x->format('M d');
              $late_array[] = $late;
              $undertime_array[] = $undertime;
              $overbreak_array[] = $overbreak;
              $total_minutes_array[] = $total_min;
            }
          }
        }
      }

    }

    $days = implode(',',$day_array);
    $lates = implode(',',$late_array);
    $undertimes = implode(',',$undertime_array);
    $overbreaks= implode(',',$overbreak_array);
    $total_mins = implode(',',$total_minutes_array);

    $data = array
    (
      "success" => 1, "days" => $days, "lates" => $lates,
      "undertimes" => $undertimes, "overbreaks" => $overbreaks,
      "total_mins" => $total_mins, "month" => $month
    );

    generate_json($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/workOrder',$data);
  }
}
