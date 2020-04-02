<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//this is the cash advance for transactions
class Leave extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('transactions/leave_model');
		$this->load->model('employee_leave/employee_leave_model');
		$this->isLoggedIn();
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
				exit();
			}
		}else{
			if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
				header("location:".base_url('Main/logout'));
				exit();
			}
		}
	}

	//views
	public function index($token = "") {

		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department,
			'leaves' => $this->leave_model->get_leave_type()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/leave', $data);
	}

	public function getleavepays_waiting_json(){
    $search = $this->input->post('searchValue');
    $data = $this->leave_model->getLeavePays_waiting_json($search);
    echo json_encode($data);
  }

	public function getleavepays_approved_json(){
    $search = $this->input->post('searchValue');
    $data = $this->leave_model->getLeavePays_approved_json($search);
    echo json_encode($data);
  }

	public function getleavepays_certified_json(){
    $search = $this->input->post('searchValue');
    $data = $this->leave_model->getLeavePays_certified_json($search);
    echo json_encode($data);
  }

	public function updateleavestatus(){
		$leave_id = $this->input->post('leave_id');
		$status = $this->input->post('status');
		$update = $this->input->post('update');
		$user_id = $this->input->post('user_id'); // id of the employee who has leave
		$leave_cat = $this->input->post('leave_cat'); // leave type id
		$num_days = $this->input->post('num_days');

		if(empty($leave_id) || empty($status)){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try againss");
			generate_json($data);
			exit();
		}

		$remaining_leave = $this->leave_model->get_remaining_leave($user_id)->row()->emp_leave;
    $new_leave = array();

    foreach(json_decode($remaining_leave) as $leave){
      if($leave->id == $leave_cat){
        $leave->days = (int)$leave->days - (int)$num_days;
      }

      $new_leave[] = $leave;
    }

    $new_leave_json = json_encode($new_leave);
    // $updated = $this->leave_model->update_employee_leave(array($new_leave_json,$this->session->userdata('emp_idno')));

		$emp_id = $this->session->userdata('emp_idno');
		if($update == "approved_by"){
			$update_data = array($status,$emp_id,$leave_id);
		}

		if($update == "certified_by"){
			$update_data = array($status,$new_leave_json,$emp_id,$leave_id);
		}

		$updated = $this->leave_model->updateLeaveStatus($update_data,$update);
		if($updated == false){
			$data = array("success" => 0, "message" => ucfirst($status)." Failed. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Successfully ".ucfirst($status));
		generate_json($data);
	}

	public function get_remaining_leave_type(){
    $leave_type = $this->input->post('leave_type');
		$emp_idno = $this->input->post('emp_idno');

    if(empty($leave_type) || empty($emp_idno)){
      $data = array("success" => 0, "message" => "Unable to get any information.");
      generate_json($data);
      exit();
    }

    if($this->leave_model->get_remaining_leave($emp_idno)->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to get any information.");
      generate_json($data);
      exit();
    }

		$late_filling = 'no';
    $leave = $this->employee_leave_model->get_leave_type($leave_type);
    $late_filling = ($leave->num_rows() > 0) ? $leave->row()->late_filling: 'no';
    $remaining_leave = $this->leave_model->get_remaining_leave($emp_idno)->row_array();
    $data = array("success" => 1, "remaining_leave" => json_decode($remaining_leave['emp_leave']), "late_filling" => $late_filling);
    generate_json($data);
  }

	//frontend
	public function add($token = "") {
		// $getemployee = $this->leave_model->getEmpID();
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'leaves' => $this->leave_model->get_leave_type(),
			'department' => $department
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/leave_add', $data);
	}

	public function edit($token = "", $id) {
		$user_dept = $this->session->userdata('deptId');
		$department = ($user_dept != hr_id() && $user_dept != 0)
									? $this->model->getDepartment($user_dept)
									: $this->model->getDepartment();
		$result = $this->leave_model->getCAByID($id)->row();
		$getemployee = $this->model->get_emp_by_dept($result->deptId);
		// $getemployee = $this->leave_model->getEmpID();
		$caID = $id;

		### get remaining leave ###
		$remaining_leaves = json_decode($this->leave_model->get_remaining_leave($result->employee_idno)->row()->emp_leave);
		$leave_type = array();
		foreach($remaining_leaves as $leave){
			if($leave->id == $result->leave_type){
				$leave_type[] = $leave;
			}
		}

		$remaining = (count((array)$leave_type) > 0) ? $leave_type[0]->days : 0 ;
		$data = array(
			'token' => $token,
			'result' => $result,
			'leaves' => $this->leave_model->get_leave_type(),
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'department' => $department,
			'employee' => $getemployee,
			'remaining' => $remaining
		);

		$this->load->view('includes/header', $data);
		$this->load->view('transactions/leave_edit', $data);
	}

	public function add_leave(){
		$employee_id_no = $this->input->post('employee_id_no');
		$leave_type = $this->input->post('leave_type');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$contact_number = $this->input->post('contact_number');
		$comment = $this->input->post('comment');
		$created_by = $this->session->userdata('emp_idno');
		$remaining_leave = $this->input->post('remaining_leave');
		// $remaining_leave = 1;
		$paid = $this->input->post('paid');

		// $data = array($employee_id_no,$leave_type,$date_from,$date_to,$number_of_days,$balance,$hrd,$contact_number,$comment);
		$leave_data = array(
			"employee_idno" => $employee_id_no,
			"leave_type" => $leave_type,
			"date_from" => $date_from,
			"date_to" => $date_to,
			"contact_number_leave" => $contact_number,
			"comment" => $comment,
			"created_by" => $created_by,
			"paid" => $paid
		);

		if(empty($employee_id_no) || empty($leave_type) || empty($date_from) || empty($date_to) || empty($date_to) ||  empty($contact_number) || empty($comment)){
			$data = array('succeess' => 0, 'message' => "Please input Leave type details");
			generate_json($data);
			exit();
		}

		### check pending leave ###
    $pending = $this->employee_leave_model->check_pending_leave($employee_id_no,$leave_type);
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
        $last_leave = $this->leave_model->get_last_filled_leave($employee_id_no,$leave_type);
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

		### check remaining leave ###
    if($remaining_leave == 0 && $paid == 'with_pay'){
      $data = array("success" => 0, "message" => "Insufficient leave remaining. Please try again");
      generate_json($data);
      exit();
    }

		### check overlaping ###
    $check_filed_leave = $this->leave_model->check_filed_leave($employee_id_no,$date_from,$date_to);
    if($check_filed_leave->num_rows() > 0){
      $exist = $check_filed_leave->row();
      $data = array(
        "success" => 0,
        "message" => "Date overlap. This user already had a leave filed from
        <u>".$exist->date_from."</u> up to <u>".$exist->date_to."</u>. Please try again"
      );
      generate_json($data);
      exit();
    }

		### check number of days ###
    $num_days = 0;
    $work_sched = (array)json_decode($this->leave_model->get_workschedule($employee_id_no)->row()->work_sched);
    $days = array('mon','tue','wed','thu','fri','sat','sun');
    $sdate = new Datetime($date_from);
    $edate = new Datetime($date_to);
    for ($i=$sdate; $i <= $edate; $i->modify('+1 day')) {
      $ldate = $i->format('Y-m-d');
      $date_name = strtolower($i->format('D'));
      $holiday = $this->leave_model->get_holiday($ldate);
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
        "message" => "Insufficient leave remaining. This user only have ".$remaining_leave." remaining leave(s) left with this category. Please try again."
      );
      generate_json($data);
      exit();
    }

		$leave_data['number_of_days'] = $num_days;

		$inserted = $this->leave_model->create($leave_data);
		if($inserted == false){
			$data = array("success" => 0, "message" => "Unable to save leave. Please try again");
		}else{
			$data = array('success' => 1, 'message' => "Data successfully inserted");
		}

		generate_json($data);
	}

	public function update_leave(){
		$caID = $this->input->post('caID');
		$employee_id_no = $this->input->post('employee_id_no');
		$leave_type = $this->input->post('leave_type');
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$contact_number = $this->input->post('contact_number');
		$comment = $this->input->post('comment');
		$remaining_leave = $this->input->post('remaining_leave');
		$paid = $this->input->post('paid');

		$update_data = array(
			"employee_idno" => $employee_id_no,
			"leave_type" => $leave_type,
			"date_from" => $date_from,
			"date_to" => $date_to,
			"contact_number_leave" => $contact_number,
			"comment" => $comment,
			"paid" => $paid,
		);

		### check required fields ###
		foreach($update_data as $rq){
			if(empty($rq)){
				$data = array("success" => 0, "message" => "Please fill up all required fields.");
				generate_json($data);
				exit();
			}
		}

		### check days before filling of leave type ###
		$leave = $this->leave_model->get_leave_type($leave_type);
		if($leave->num_rows() > 0){
			$leave = $leave->row_array();
			// $days_before_filling = $leave['days_before_filling'];
			// $leave_name = $leave['description'];
			// $filling_days = days_between(today(),$date_from);
			// if($filling_days < $days_before_filling){
			// 	$data = array("success" => 0, "message" => $leave_name." should be filled ".$days_before_filling." before.");
			// 	generate_json($data);
			// 	exit();
			// }

			if($leave['consecutive_filling'] == 'no'){
        $last_leave = $this->leave_model->get_last_filled_leave($employee_id_no,$leave_type,$caID);
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

		### check pending leave ###
    $pending = $this->employee_leave_model->check_pending_leave($employee_id_no,$leave_type);
    if($pending->num_rows() > 0 && $paid == 'with_pay'){
      $pending = $pending->row()->pending;
      if($pending >= $remaining_leave){
        $data = array("success" => 0, "message" => "Insufficient remaining leave(s). You still have ".$pending." pending leave(s) and you only have ".$remaining_leave." remaining  leave left.");
        generate_json($data);
        exit();
      }
    }

		### check remaining leave ###
    if($remaining_leave == 0 && $paid == 'with_pay'){
      $data = array("success" => 0, "message" => "Insufficient leave remaining. Please try again");
      generate_json($data);
      exit();
    }

		### check overlaping ###
    $check_filed_leave = $this->leave_model->check_filed_leave($employee_id_no,$date_from,$date_to,$caID);
    if($check_filed_leave->num_rows() > 0){
      $exist = $check_filed_leave->row();
      $data = array(
        "success" => 0,
        "message" => "Date overlap. This user already had a leave filed from
        <u>".$exist->date_from."</u> up to <u>".$exist->date_to."</u>. Please try again"
      );
      generate_json($data);
      exit();
    }

		### check number of days ###
    $num_days = 0;
    $work_sched = (array)json_decode($this->leave_model->get_workschedule($employee_id_no)->row()->work_sched);
    $days = array('mon','tue','wed','thu','fri','sat','sun');
    $sdate = new Datetime($date_from);
    $edate = new Datetime($date_to);
    for ($i=$sdate; $i <= $edate; $i->modify('+1 day')) {
      $ldate = $i->format('Y-m-d');
      $date_name = strtolower($i->format('D'));
      $holiday = $this->leave_model->get_holiday($ldate);
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
        "message" => "Insufficient leave remaining. This user only have ".$remaining_leave." remaining leave(s) left with this category. Please try again."
      );
      generate_json($data);
      exit();
    }

		$update_data['number_of_days'] = $num_days;
		$updated = $this->leave_model->update_new($caID,$update_data);
		if($updated == false){
			$data = array("success" => 0, "message" => "Update failed. Please try again");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Updated Successfully");
		generate_json($data);

	}

	public function update_batch_status(){
    $this->isLoggedIn();

    $status = $this->input->post('status');
    $batch_status = $this->input->post('batch_status');
    $batch = $this->input->post('batch');

    if(empty($status) || count((array)$batch) == 0){
      $data = array("success" => 0, "message" => "Something went wrong. Please try again.");
      generate_json($data);
      exit();
    }

    $batch_ = array();
    $batch_decrypt[] = decrypt_array($batch);
    $batch_serialize = implode(',',$batch_decrypt[0]);
    $updated = $this->leave_model->updateworkorder_batch_status($status,$batch_serialize);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to ".ucfirst($batch_status)." leave. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Batch ".ucfirst($batch_status)." Successfull");
    generate_json($data);
  }

	//backend
	public function destroy() {
		$id = $this->input->post('id');
		$data = array(0,$id);

		$this->leave_model->destroy($data);

		$data = array('success' => 1, 'message' => "Deleted Successfully!");
		generate_json($data);

	}

	public function reject(){
    $this->isLoggedIn();

    $reject_id = en_dec('dec',$this->input->post('reject_id'));
    $reject_reason = $this->input->post('reject_reason');

    if(empty($reject_id) || empty($reject_reason)){
      $data = array("success" => 0, "message" => "Please fill up all required fields and try again.");
      generate_json($data);
      exit();
    }

    $reject_data = array(
      "rejected_by" => $this->session->emp_idno,
      "reject_reason" => $reject_reason,
      "status" => "rejected",
      "date_updated" => todaytime()
    );

    $rejected = $this->leave_model->reject($reject_data,$reject_id);
    if($rejected === false){
      $data = array("success" => 0, "message" => "Unable to reject leave .Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Leave rejected successfully");
    generate_json($data);

  }

}
