<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee_leave extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('employee_leave/employee_leave_model');
    $this->load->model('transactions/leave_model');
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

  public function getleavepays_waiting_json(){
    $search = $this->input->post('searchValue');
    $data = $this->employee_leave_model->getLeavePays_waiting_json($search);
    echo json_encode($data);
  }

  public function getleavepays_approved_json(){
    $search = $this->input->post('searchValue');
    $data = $this->employee_leave_model->getLeavePays_approved_json($search);
    echo json_encode($data);
  }

  public function getleavepays_certified_json(){
    $search = $this->input->post('searchValue');
    $data = $this->employee_leave_model->getLeavePays_certified_json($search);
    echo json_encode($data);
  }

  public function getleavepays_rejected_json(){
    $search = $this->input->post('searchValue');
    $data = $this->employee_leave_model->getLeavePays_rejected_json($search);
    echo json_encode($data);
  }

  public function get_employee_leave_json(){
    $search = $this->input->post('searchValue');
    $data = $this->employee_leave_model->get_employee_leave_json($search);
    echo json_encode($data);
  }

  public function get_employee_leave_json_rejected(){
    $search = $this->input->post('searchValue');
    $data = $this->employee_leave_model->get_employee_leave_json_rejected($search);
    echo json_encode($data);
  }

  public function get_remaining_leave_type(){
    $leave_type = $this->input->post('leave_type');

    if(empty($leave_type)){
      $data = array("success" => 0, "message" => "Unable to get any information.");
      generate_json($data);
      exit();
    }

    if($this->employee_leave_model->get_remaining_leave()->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get any information.");
      generate_json($data);
      exit();
    }

    $late_filling = 'no';
    $leave = $this->employee_leave_model->get_leave_type($leave_type);
    $late_filling = ($leave->num_rows() > 0) ? $leave->row()->late_filling: 'no';
    $remaining_leave = $this->employee_leave_model->get_remaining_leave()->row_array();
    $data = array("success" => 1, "remaining_leave" => json_decode($remaining_leave['emp_leave']), "late_filling" => $late_filling);
    generate_json($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('employee_leave/employee_leave',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $emp_idno = $this->session->userdata('emp_idno');
    $leave_type = $this->input->post('leave_type');
    $remaining_leave = $this->input->post('remaining_leave');
    // $remaining_leave = 1;
    $date_from = $this->input->post('date_from');
    $date_to = $this->input->post('date_to');
    $reason = $this->input->post('reason');
    $contact = $this->input->post('contact');
    $paid = $this->input->post('paid');

    ### check pending leave ###
    $pending = $this->employee_leave_model->check_pending_leave($emp_idno,$leave_type);
    if($pending->num_rows() > 0 && $paid == 'with_pay'){
      $pending = $pending->row()->pending;
      if($pending >= $remaining_leave){
        $data = array("success" => 0, "message" => "Insufficient remaining leave(s). You still have ".$pending." pending leave(s) and you only have ".$remaining_leave." remaining  leave left.");
        generate_json($data);
        exit();
      }
    }

    ### check days before filling of leave type ###
		$leave = $this->leave_model->get_leave_type($leave_type);
		if($leave->num_rows() > 0){
			$leave = $leave->row_array();
			$days_before_filling = $leave['days_before_filling'];
			$leave_name = $leave['description'];
			$filling_days = days_between(today(),$date_from);
			if($filling_days < $days_before_filling){
				$data = array("success" => 0, "message" => $leave_name." should be filled ".$days_before_filling." before.");
				generate_json($data);
				exit();
			}

      if($leave['consecutive_filling'] == 'no'){
        $last_leave = $this->employee_leave_model->get_last_filled_leave($emp_idno,$leave_type);
        if($last_leave->num_rows() > 0){
          $last_leave = $last_leave->row()->date_created;
          $separtion_days = days_between(today(),$last_leave);
          if($separtion_days <= 1){
            $data = array("success" => 0, "message" => "Oops! you cannot file consecutive leave under this leave category.");
            generate_json($data);
            exit();
          }
        }
      }
		}

    ### check required fields ###
    if(empty($emp_idno) || empty($leave_type) || empty($date_from) || empty($date_to) || empty($reason) || empty($contact)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    ### check remaining leave ###
    if($remaining_leave == 0 && $paid == 'with_pay'){
      $data = array("success" => 0, "message" => "Insufficient leave remaining. Please try again");
      generate_json($data);
      exit();
    }

    ### check overlaping ###
    $check_filed_leave = $this->employee_leave_model->check_filed_leave($emp_idno,$date_from,$date_to);
    if($check_filed_leave->num_rows() > 0){
      $exist = $check_filed_leave->row();
      $data = array(
        "success" => 0,
        "message" => "Date overlap. You already filed a leave from
        <u>".$exist->date_from."</u> up to <u>".$exist->date_to."</u>. Please try again"
      );
      generate_json($data);
      exit();
    }

    ### check number of days ###
    $num_days = 0;
    $work_sched = (array)json_decode($this->employee_leave_model->get_workschedule()->row()->work_sched);
    $days = array('mon','tue','wed','thu','fri','sat','sun');
    $wdays = array();
    $sdate = new Datetime($date_from);
    $edate = new Datetime($date_to);
    for ($i=$sdate; $i <= $edate; $i->modify('+1 day')) {
      $ldate = $i->format('Y-m-d');
      $date_name = strtolower($i->format('D'));
      $holiday = $this->employee_leave_model->get_holiday($ldate);
      if($holiday->num_rows() == 0){
        for ($x=0; $x < 7; $x++) {
          if($days[$x] == $date_name){
            if($work_sched[$days[$x]][0] != ""){
              $num_days += 1;
            }
          }
        }
      }
    }

    ### check overlaping 2 ###
    if(((int)$num_days > (int)$remaining_leave) && $paid == 'with_pay'){
      $data = array(
        "success" => 0,
        "message" => "Insufficient leave remaining. You only have ".$remaining_leave." remaining leave(s) left with this category. Please try again."
      );
      generate_json($data);
      exit();
    }

    $leave_data = array(
      "leave_type" => $leave_type,
      "employee_idno" => $emp_idno,
      "number_of_days" => $num_days,
      "date_from" => $date_from,
      "date_to" => $date_to,
      "comment" => $reason,
      "contact_number_leave" => $contact,
      "status" => "waiting",
      "paid" => $paid,
      "date_created" => today(),
      "created_by" => $emp_idno
    );

    $inserted = $this->employee_leave_model->set_employee_leave($leave_data);

    if($inserted == false){
      $data = array("success" => 0, "message" => "Unable to save leave. Please try again.");
      generate_json($data);
      exit();
    }

    // $remaining_leave = $this->employee_leave_model->get_remaining_leave()->row()->emp_leave;
    // $new_leave = array();
    //
    // foreach(json_decode($remaining_leave) as $leave){
    //   if($leave->id == $leave_type){
    //     $leave->days = (int)$leave->days - (int)$number_of_days;
    //   }
    //
    //   $new_leave[] = $leave;
    // }
    //
    // $new_leave_json = json_encode($new_leave);
    // $updated = $this->employee_leave_model->update_employee_leave(array($new_leave_json,$this->session->userdata('emp_idno')));
    //
    // if($updated == false){
    //   $data = array("success" => 0, "message" => "Unable to update leave of employee.");
    //   generate_json($data);
    //   exit();
    // }

    $data = array("success" => 1, "message" => "Leave filed successfully.");
    generate_json($data);
  }

  public function update(){
    $this->isLoggedIn();

    $emp_idno = $this->session->userdata('emp_idno');
    $uid = $this->input->post('uid');
    $leave_type = $this->input->post('leave_type');
    $remaining_leave = $this->input->post('remaining_leave');
    $date_from = $this->input->post('date_from');
    $date_to = $this->input->post('date_to');
    $reason = $this->input->post('reason');
    $contact = $this->input->post('contact');
    $edit_paid = $this->input->post('edit_paid');

    ### check required fields ###
    if(empty($leave_type) || empty($date_from) || empty($date_to) || empty($reason) || empty($contact)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    ### check pending leave ###
    $pending = $this->employee_leave_model->check_pending_leave($emp_idno,$leave_type);
    if($pending->num_rows() > 0 && $edit_paid == 'with_pay'){
      $pending = $pending->row()->pending;
      if($pending >= $remaining_leave){
        $data = array("success" => 0, "message" => "Insufficient remaining leave(s). You still have ".$pending." pending leave(s) and you only have ".$remaining_leave." remaining  leave left.");
        generate_json($data);
        exit();
      }
    }

    ### check days before filling of leave type ###
		$leave = $this->leave_model->get_leave_type($leave_type);
		if($leave->num_rows() > 0){
			$leave = $leave->row_array();
			$days_before_filling = $leave['days_before_filling'];
			$leave_name = $leave['description'];
			$filling_days = days_between(today(),$date_from);
			if($filling_days < $days_before_filling){
				$data = array("success" => 0, "message" => $leave_name." should be filled ".$days_before_filling." before.");
				generate_json($data);
				exit();
			}

      if($leave['consecutive_filling'] == 'no'){
        $last_leave = $this->employee_leave_model->get_last_filled_leave($emp_idno,$leave_type,$uid);
        if($last_leave->num_rows() > 0){
          $last_leave = $last_leave->row()->date_created;
          $separtion_days = days_between(today(),$last_leave);
          if($separtion_days <= 1){
            $data = array("success" => 0, "message" => "Oops! you cannot file consecutive leave under this leave category.");
            generate_json($data);
            exit();
          }
        }
      }
		}

    ### check remaing leave ###
    if($remaining_leave == 0 && $edit_paid == 'with_pay'){
      $data = array("success" => 0, "message" => "You dont have any remaining left for this leave category.");
      generate_json($data);
      exit();
    }

    ### check number of days ###
    $num_days = 0;
    $work_sched = (array)json_decode($this->employee_leave_model->get_workschedule()->row()->work_sched);
    $days = array('mon','tue','wed','thu','fri','sat','sun');
    $sdate = new Datetime($date_from);
    $edate = new Datetime($date_to);
    for ($i=$sdate; $i <= $edate; $i->modify('+1 day')) {
      $ldate = $i->format('Y-m-d');
      $date_name = strtolower($i->format('D'));
      $holiday = $this->employee_leave_model->get_holiday($ldate);
      if($holiday->num_rows() == 0){
        for ($x=0; $x < 7; $x++) {
          if($days[$x] == $date_name){
            if($work_sched[$days[$x]][0] != ""){
              $num_days += 1;
            }
          }
        }
      }
    }

    ### check overlapping ###
    if(((int)$num_days > (int)$remaining_leave) && $edit_paid == 'with_pay'){
      $data = array(
        "success" => 0,
        "message" => "Insufficient leave remaining. You only have ".$remaining_leave." remaining leave(s) left with this category. Please try again."
      );
      generate_json($data);
      exit();
    }

    ### check overlapping 2 ###
    $check_filed_leave = $this->employee_leave_model->check_filed_leave($emp_idno,$date_from,$date_to,$uid);
    if($check_filed_leave->num_rows() > 0){
      $exist = $check_filed_leave->row();
      $data = array(
        "success" => 0,
        "message" => "Date overlap. You already filed a leave from
        <u>".$exist->date_from."</u> up to <u>".$exist->date_to."</u>. Please try again"
      );
      generate_json($data);
      exit();
    }

    $update_data = array($leave_type,$num_days,$date_from,$date_to,$reason,$contact,$uid);
    $updated = $this->employee_leave_model->update_leave($update_data);

    if($updated == false){
      $data = array("success" => 0, "message" => "Unable to update. Please try again");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Updated Successfully");
    generate_json($data);
  }

  public function delete(){
    $this->isLoggedIn();

    $delid = $this->input->post('delid');

    if(empty($delid)){
      $data = array("success" => 0, "message" => "Unable to delete. Please try again");
      generate_json($data);
      exit();
    }

    $delete_data = array(0,$delid);
    $deleted = $this->employee_leave_model->update_leave_status($delete_data);
    if($deleted == false){
      $data = array("success" => 0, "message" => "Unable to delete. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Deleted Successfully.");
    generate_json($data);
  }
}
