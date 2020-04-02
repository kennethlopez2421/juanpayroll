<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timelog extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('employees/timelog_model');
		$this->load->model('employees/employee_model');
		$this->load->model('settings/registered_device_model');
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
	//views
	public function index() {
		// $this->session->sess_destroy();
		$this->load->view('employees/timelog_new');
	}

	public function auth_admin(){
		$this->load->model('branch/branch_model');
		$account_code = $this->input->post('account_code');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		if(empty($account_code) || empty($username) || empty($password)){
			$data = array("success" => 0, "message" => "Please fill up all required fields.");
			generate_json($data);
			exit();
		}

		$branch = $this->branch_model->get_hris_branch($account_code,'branch_code');
		if($branch->num_rows() == 0){
			$data = array("success" => 0, "message" => "Invalid Account Code.");
			generate_json($data);
			exit();
		}

		$branch = $branch->row();
		$this->db = switch_database($branch->database_name);
		$validate_username = $this->model->validate_username($username);
		if($validate_username->num_rows() == 0){
      $data = array("success" => 0, "message" => 'The username you\'ve entered doesn\'t match any account.');
      $this->session->sess_destroy();
      generate_json($data);
      exit();
    }

		$user = $validate_username->row();
    $pos_id = $user->pos_lvl;

    $unverified_username = $user->enabled;
    if($unverified_username == 0){
      $data = array("success" => 0, "message" => 'The account you\'ve entered is unverified account.');
      $this->session->sess_destroy();
      generate_json($data);
      exit();
    }

    $hash_password = $user->password;
		if(password_verify($password, $hash_password)){
			$admin_arr = array(
				'database_name' => $branch->database_name,
				'branch_name' => $branch->branch_name,
				'location' => $branch->location,
				'isAdminLoggedIn' => true
			);
			$this->session->set_userdata($admin_arr);
			$data = array("success" => 1, "message" => "Ok");
		}else{
			$data = array(
				'success' => 0,
				'message' => 'The username and password you\'ve entered doesn\'t match any account.'
			);
		}

		generate_json($data);

	}

	public function add() {
		$timezone = $this->input->post('timezone');
		$timeIn = $this->input->post('timeIn');
		$empId = $this->input->post('empId');
		$getLocation = $this->input->post('getlocation');
		$mode = 'auto';
		$login_status = "";
		$multi_worksite = 1;
		// die($getLocation);

		### SET TIMEZONE ###
		date_default_timezone_set($timezone);
		$dateIn = date("Y-m-d");
		$datetime = date("Y-m-d G:i:s");

		### CREATE DIRECTORY FOR NEW ATTENDANCE ###
		if(isset($this->session->database_name) && !is_dir("./assets/attendance/".$this->session->branch_name."/".month_year()."/")) {
			if(!is_dir("./assets/attendance/".$this->session->branch_name."/".month_year()."/")) {
				mkdir("./assets/attendance/".$this->session->branch_name.'/'.month_year().'/');
				// if(!is_dir("./assets/attendance/".$this->session->branch_name."/".month_year()."/")){
				// 	mkdir("./assets/attendance/".$this->session->branch_name.'/'.month_year().'/');
				// }
			}
		}

		### CHECK REQUIRED FIELDS ###
		if($empId == ""){
			$data = array("success" => 0, "message" => "User not recognized. Please try again.");
			generate_json($data);
			exit();
		}

		### CHECK IF EMPLOYEE EXISTS ###
		$isEmployeeExist = $this->timelog_model->get_employee_with_worksched($empId);
		if($isEmployeeExist->num_rows() == 0){
			$data = array("success" => 0, "message" => "Invalid Employee ID. Please try again.");
			generate_json($data);
			exit();
		}

		### CHECK WORKSITES ###
		$worksites_id = $this->timelog_model->get_all_worksite_id($empId);
		if($worksites_id->num_rows() == 0){
			$data = array("success" => 0, "message" => "Unable to find your work location in contract . Please try again.");
			generate_json($data);
			exit();
		}

		$ids = explode(',',$worksites_id->row()->work_site_id);
		if(count((array)$ids) > 1)
		{ // MULTIPLE WORKSITE
			// echo('2');
			$multi_worksite = 2;
			$get_all_worksite = $this->timelog_model->get_all_worksite($ids);
			if($get_all_worksite->num_rows() == 0){
				$data = array("success" => 0, "message" => "Unable to find your work location in contract . Please try again.");
				generate_json($data);
				exit();
			}

			$in_radius = false;
			$worksite = '';
			foreach($get_all_worksite->result() as $ws){
				$worksite_lat = $ws->loc_latitude;
				$worksite_lng = $ws->loc_longitude;
				$getLocation_admin = json_decode($getLocation); // activate only if position lvl is 2 or below
				if($getLocation_admin->lat != "" || $getLocation_admin->lng != ""){
					$distance_from_worksite = getDistance($getLocation,$worksite_lat,$worksite_lng);
					$distance_radius = $ws->distance;
					if($distance_from_worksite > $distance_radius){
						// echo "fuck";
					}else{
						$in_radius = true;
						$worksites_id = $ws->worksiteid;
						$worksite = $ws;
						// echo "tainang to";
					}
				}else{
					$worksites_id = $worksites_id->row()->work_site_id;
					$getLocation = json_encode(array("lat" => $worksite_lat, "lng" => $worksite_lng));
					$worksite = $ws;
					$in_radius = true;
				}
			}

			if($in_radius == false){
				$data = array("success" => 0, "message" => "You are to far away from any of your worksite. Try to get closer.");
				generate_json($data);
				exit();
			}
		}
		else
		{ // SINGLE WORKSITE
			// echo('1');
			$tag_worksite = $this->timelog_model->get_tag_worksite($empId);
			$worksite = ($tag_worksite->row()->worksiteid != 1.1)
			? $this->timelog_model->get_worksite($empId) : $tag_worksite;

			if($worksite->num_rows() == 0){
				$data = array("success" => 0, "message" => "Unable to find your work location in contract . Please try again.");
				generate_json($data);
				exit();
			}

			### CHECK LOCATION IF WITHIN RADIUS ###
			$worksite = $worksite->row();
			if($worksite->worksiteid != 1.1){
				$worksite_lat = $worksite->loc_latitude;
				$worksite_lng = $worksite->loc_longitude;
			}

			$getLocation_admin = json_decode($getLocation); // activate only if position lvl is 2 or below
			if($getLocation_admin->lat != "" || $getLocation_admin->lng != ""){
				if($worksite->worksiteid != 1.1){
					$distance_from_worksite = getDistance($getLocation,$worksite_lat,$worksite_lng);
					$distance_radius = $worksite->distance;
					if($distance_from_worksite > $distance_radius){
						$data = array("success" => 0, "message" => "You are <u>".$distance_from_worksite." meters</u> away from your worksite. Please get close within the <u>".$worksite->distance." meters radius</u>. Thank you ");
						generate_json($data);
						exit();
					}
				}
			}else{
				$getLocation = json_encode(array("lat" => $worksite_lat, "lng" => $worksite_lng));
			}
		}

		$timelogexist = $this->timelog_model->getLastTimeLogPerday($empId,$dateIn);
		### ALREADY HAS TIMEIN FOR THE DAY ###
		if($timelogexist->num_rows() > 0){
			$timelog = $timelogexist->row();
			### CHECK 5 min INTERVAL ###
			$last_timein = strtotime($timelog->date_created);
			$todaytime = strtotime(todaytime());
			$time_diff = $todaytime - $last_timein;
			if(round(abs($time_diff) / 60) <= 5){
				$data = array("success" => 0, "message" => "You just time in. Log out after 5 mins.");
				generate_json($data);
				exit();
			}

			### picture file upload ###
	    if(isset($_FILES['picture'])){
				$config['file_name']         = trim($empId).'_'.$dateIn.'_'.preg_replace('/[:]+/', '-', trim($timeIn)).'.png';
	      $config['upload_path']       = 'assets/attendance/'.$this->session->branch_name."/".month_year()."/";
	      $config['allowed_types']     = '*';
	      $config['max_size']          = 2048;
	      $config['encrypt_name']      = true;

	      $this->load->library('upload', $config);

	      if(!$this->upload->do_upload('picture')){
	         $error = array('error' => $this->upload->display_errors());
					 $data = array("success" => 0, "message" => $error['error']);
					 generate_json($data);
					 exit();
	      }else{
	        $cdata = array('upload_data' => $this->upload->data());
	        $picture = $config['upload_path'].$cdata['upload_data']['file_name'];
	      }
	    }else{
	      $picture = "";
	    }

			if($picture == ""){
				$data = array("success" => 0, "message" => "No image captured. Please try again.");
				generate_json($data);
				exit();
			}

			### WORK SCHEDULE ###
			$work_sched = json_decode($isEmployeeExist->row()->work_sched);
			$work_sched = (array)$work_sched;
			$today = new DateTime($dateIn);
			$day = strtolower($today->format('D'));
			$stime_in = 0;
			$stime_out = 0;

			$stime_in = mins($work_sched[$day][0]);
			$stime_out_raw = mins($work_sched[$day][1]);
			$stime_out = ($stime_in > mins($work_sched[$day][1]))
			? mins($work_sched[$day][1]) + 1440
			: mins($work_sched[$day][1]);


			### CHECK NO TIME OUT ###
			if($timelog->time_out == null){
				$login_status = "Time Out";
				$img_url = implode(',', array($timelog->img_url, $picture));
				$location = implode(',', array($timelog->current_location, $getLocation));

				// NORMAL TIME OUT
				if($dateIn == $timelog->date){
					$updated = $this->timelog_model->update_timelog($mode,$img_url,$location,$timeIn,$datetime,$dateIn,$empId);
					if($updated == false){
						$data = array("success" => 0, "message" => "Unable to Time Out. Please try again.");
						generate_json($data);
						exit();
					}
				}

				// NIGHT DIFF || DATE LAPSE TIME OUT
				if($dateIn != $timelog->date){
					if(((mins($timeIn) + 1440) - $stime_out) < 720)
					{ // IF TIME OUT IS WITHIN 12 HOURS
						// die("test2");
						$data = array(
							"success" => 2,
							"mode" => $mode,
							"worksite" => ($multi_worksite > 1) ? $worksites_id : $worksite->worksiteid,
							"picture" => $picture,
							"img_url" => $img_url,
							"location" => $location,
							"current_location" => $getLocation,
							"ws_location" => ($worksite->worksiteid != 1.1) ? $worksite->location: 'Anywhere',
							"time_in" => $timelog->time_in,
							"time_out" => $timeIn,
							"datetime" => $datetime,
							"date" => $timelog->date,
							"emp_id" => $empId,
							"timezone" => $timezone
						);
						generate_json($data);
						exit();
					}else
					{ // ELSE TREAT IT AS TIME IN
						// die("tes3");
						$login_status = "Time In";
						// if($stime_in > mins($work_sched[$day][1])){
						// 	$dateIn = (mins($timeIn) < $stime_out) ? $timelog->date : $dateIn;
						// }
						$insert_data = array(
							"employee_idno" => $empId,
							"worksite" => ($multi_worksite > 1) ? $worksites_id : $worksite->worksiteid,
							"date" => $dateIn,
							"mode" => $mode,
							"img_url" => $picture,
							"current_location" => $getLocation,
							"time_in" => $timeIn,
							"date_created" => $datetime
						);
						$inserted = $this->timelog_model->set_timelog($insert_data);
						if($inserted == false){
							$data = array("success" => 0, "message" => "Unable to Time In. Please try to reload the page and try again. ");
							generate_json($data);
							exit();
						}
					}
				}
			}
			### CHECK HAS TIME OUT ###
			if($timelog->time_out != null){
				$login_status = "Time In";

				if($stime_in > mins($work_sched[$day][1])){
					$scheduled_in = strtotime($timelog->date." ".$work_sched[$day][0]);
					$scheduled_out = strtotime($dateIn." ".$work_sched[$day][1]);
					$current_in = strtotime($dateIn." ".$timeIn);

					$dateIn = ($current_in >= $scheduled_in && $current_in <= $scheduled_out)
					? $timelog->date : $dateIn;
				}

				$insert_data = array(
					"employee_idno" => $empId,
					"worksite" => ($multi_worksite > 1) ? $worksites_id : $worksite->worksiteid,
					"date" => $dateIn,
					"mode" => $mode,
					"img_url" => $picture,
					"current_location" => $getLocation,
					"time_in" => $timeIn,
					"date_created" => $datetime
				);
				$inserted = $this->timelog_model->set_timelog($insert_data);
				if($inserted == false){
					$data = array("success" => 0, "message" => "Unable to Time In. Please try to reload the page and try again. ");
					generate_json($data);
					exit();
				}
			}
		}

		### FIRST TIME IN FOR THE DAY ###
		if($timelogexist->num_rows() == 0){

			### picture file upload ###
	    if(isset($_FILES['picture'])){
				$config['file_name'] = trim($empId).'_'.$dateIn.'_'.preg_replace('/[:]+/', '-', trim($timeIn)).'.png';
	      $config['upload_path']       = 'assets/attendance/'.$this->session->branch_name."/".month_year()."/";
	      $config['allowed_types']     = '*';
	      $config['max_size']          = 2048;
	      $config['encrypt_name']      = true;

	      $this->load->library('upload', $config);

	      if(!$this->upload->do_upload('picture')){
	         $error = array('error' => $this->upload->display_errors());
					 $data = array("success" => 0, "message" => $error['error']);
					 generate_json($data);
					 exit();
	      }else{
	        $cdata = array('upload_data' => $this->upload->data());
	        $picture = $config['upload_path'].$cdata['upload_data']['file_name'];
	      }
	    }else{
	      $picture = "";
	    }

			if($picture == ""){
				$data = array("success" => 0, "message" => "No image captured. Please try again.");
				generate_json($data);
				exit();
			}

			$login_status = "Time In";
			$insert_data = array(
				"employee_idno" => $empId,
				"worksite" => ($multi_worksite > 1) ? $worksites_id : $worksite->worksiteid,
				"date" => $dateIn,
				"mode" => $mode,
				"img_url" => $picture,
				"current_location" => $getLocation,
				"time_in" => $timeIn,
				"date_created" => $datetime
			);
			$inserted = $this->timelog_model->set_timelog($insert_data);
			if($inserted == false){
				$data = array("success" => 0, "message" => "Unable to Time In. Please try to reload the page and try again. ");
				generate_json($data);
				exit();
			}
		}

		$fname = $this->employee_model->getEmployeeByIdNo($empId)->row()->first_name;
		$lname = $this->employee_model->getEmployeeByIdNo($empId)->row()->last_name;
		$data = array(
			'success' => 1,
			'mode' => "<span class = 'text:center;'>Hello ".$fname." ".$lname."!</span>",
			'message' => $login_status." ".date('H:i A', strtotime($timeIn)),
			'location' => ($worksite->worksiteid != 1.1) ? $worksite->location: 'anywhere'
			// 'location' => 'anywhere'
		);
		generate_json($data);
	}

	public function add_date_lapse(){
		$mode = $this->input->post('mode');
		$picture = $this->input->post('picture');
		$img_url = $this->input->post('img_url');
		$location = $this->input->post('location');
		$current_location = $this->input->post('current_location');
		$ws_location = $this->input->post('ws_location');
		$worksite = $this->input->post('worksite');
		$time_in = $this->input->post('time_in');
		$time_out = $this->input->post('time_out');
		$datetime = $this->input->post('datetime');
		$date = $this->input->post('date');
		$emp_id = $this->input->post('emp_id');
		$timezone = $this->input->post('timezone');
		$status = $this->input->post('status');

		if(empty($mode) || empty($img_url) || empty($location) || empty($datetime) || empty($date) || empty($emp_id)) {
			$data = array("success" => 0, "message" => "One of the required data is missing . Please try again.");
			generate_json($data);
			exit();
		}

		// TIME IN
		if($status == "time_in"){
			date_default_timezone_set($timezone);
			$date = date("Y-m-d");
			$datetime = date("Y-m-d G:i:s");
			$login_status = "Time In";
			$insert_data = array(
				"employee_idno" => $emp_id,
				"worksite" => $worksite,
				"date" => $date,
				"mode" => $mode,
				"img_url" => $picture,
				"current_location" => $current_location,
				"time_in" => $time_out,
				"date_created" => $datetime
			);

			$inserted = $this->timelog_model->set_timelog($insert_data);
			if($inserted == false){
				$data = array("success" => 0, "message" => "Unable to Time In. Please try to reload the page and try again. ");
				generate_json($data);
				exit();
			}

		}

		// TIME OUT
		if($status == "time_out"){
			$login_status = "Time Out";
			$updated = $this->timelog_model->update_timelog($mode,$img_url,$location,$time_out,$datetime,$date,$emp_id);
			if($updated == false){
				$data = array("success" => 0, "message" => "Unable to Time Out. Please try again.");
				generate_json($data);
				exit();
			}
		}


		$fname = $this->employee_model->getEmployeeByIdNo($emp_id)->row()->first_name;
		$lname = $this->employee_model->getEmployeeByIdNo($emp_id)->row()->last_name;

		$data = array(
			'success' => 1,
			'mode' => "<span class = 'text:center;'>Hello ".$fname." ".$lname."!</span>",
			'message' => $login_status." ".date('H:i A', strtotime($time_out)),
			'location' => $ws_location
			// 'location' => 'anywhere'
		);
		generate_json($data);
	}

	public function add_using_rfid(){
		$rf_number = $this->input->post('rf_number');
		$timeIn = $this->input->post('timeIn');
		$getLocation = $this->input->post('getlocation');
		// echo $rf_number;
		// die($getLocation);
		$dateIn = today();
		$datetime = todaytime();
		$mode = 'auto';
		$login_status = "";
		$multi_worksite = 1;


		if(empty($rf_number)){
			$data = array("success" => 0, "message" => "Unable to detect rf id number. Please try again.");
			generate_json($data);
			exit();
		}

		$rf_exist = $this->timelog_model->get_rfid($rf_number,'active');
		if($rf_exist->num_rows() == 0){
			$data = array("success" => 0, "message" => "Invalid rf id number. Please try again.");
			generate_json($data);
			exit();
		}

		if(!is_dir("./assets/attendance/". $dateIn ."/")) {
			mkdir("./assets/attendance/". $dateIn ."/");
		}

		$tag_worksites = $this->timelog_model->get_tag_worksite($rf_exist->row()->employee_idno);
		if($tag_worksites->num_rows() == 0){
			$data = array("success" => 0, "message" => "Unable to find your work location in contract . Please try again.");
			generate_json($data);
			exit();
		}

		$ids = explode(',',$tag_worksites->row()->worksiteid);
		if(count((array)$ids) > 1)
		{ // MULTIPLE WORKSITE
			$multi_worksite = 2;
			$get_all_worksite = $this->timelog_model->get_all_worksite($ids);
			if($get_all_worksite->num_rows() == 0){
				$data = array("success" => 0, "message" => "Unable to find your work location in contract . Please try again.");
				generate_json($data);
				exit();
			}

			$in_radius = false;
			foreach($get_all_worksite->result() as $worksite){
				$worksite_lat = $worksite->loc_latitude;
				$worksite_lng = $worksite->loc_longitude;
				$getLocation_admin = json_decode($getLocation); // activate only if position lvl is 2 or below
				if($getLocation_admin->lat != "" || $getLocation_admin->lng != ""){
					$distance_from_worksite = getDistance($getLocation,$worksite_lat,$worksite_lng);
					$distance_radius = $worksite->distance;
					if($distance_from_worksite > $distance_radius){
						// echo "fuck";
					}else{
						$in_radius = true;
						$tag_worksites = $worksite->worksiteid;
						// echo "tainang to";
					}
				}else{
					$worksites_id = $tag_worksites->row()->worksiteid;
					$getLocation = json_encode(array("lat" => $worksite_lat, "lng" => $worksite_lng));
					$in_radius = true;
				}
			}

			if($in_radius == false){
				$data = array("success" => 0, "message" => "You are to far away from any of your worksite. Try to get closer.");
				generate_json($data);
				exit();
			}
		}
		else
		{ // SINGLE WORKSITE
			$row = ($tag_worksites->row()->worksiteid != 1.1)
			? $this->timelog_model->get_emp_thru_rfid($rf_number)
			: $tag_worksites;
			if($row->num_rows() == 0){
				$data = array("success" => 0, "message" => "Rf id number not registered to any employee. Please try again.");
				generate_json($data);
				exit();
			}

			$user = $row->row_array();
			if($user['distance'] == "" || $user['loc_latitude'] == "" || $user['loc_longitude'] == ""){
				$data = array("success" => 0, "message" => "Unable to find work location on contract. Please try again.");
				generate_json($data);
				exit();
			}

			$getLocation_admin = json_decode($getLocation);
			if($getLocation_admin->lat != "" || $getLocation_admin->lng != ""){
				if($worksite->worksiteid != 1.1){
					$distance_from_worksite = getDistance($getLocation,$user['loc_latitude'],$user['loc_longitude']);
					$distance_radius = $user['distance'];
					if($distance_from_worksite > $distance_radius){
						$data = array("success" => 0, "message" => "You are <u>".$distance_from_worksite." meters</u> away from your worksite. Please get close within the <u>".$worksite->distance." meters radius</u>. Thank you ");
						generate_json($data);
						exit();
					}
				}
			}else{
				$getLocation = json_encode(array("lat" => $user['loc_latitude'], "lng" => $user['loc_latitude']));
			}
		}

		$empId = $user['employee_idno'];
		$timelogexist = $this->timelog_model->getLastTimeLogPerday($empId,$dateIn);
		### ALREADY HAS TIMEIN FOR THE DAY ###
		if($timelogexist->num_rows() > 0){
			$timelog = $timelogexist->row();

			// ### CHECK 5 min INTERVAL ###
			// $last_timein = strtotime($timelog->date_created);
			// $todaytime = strtotime(todaytime());
			// $time_diff = $todaytime - $last_timein;
			// if(round(abs($time_diff) / 60) <= 5){
			// 	$data = array("success" => 0, "message" => "You just time in. Log out after 5 mins.");
			// 	generate_json($data);
			// 	exit();
			// }

			### picture file upload ###
	    if(isset($_FILES['picture'])){
				$config['file_name'] = trim($empId).'_'.$dateIn.'_'.preg_replace('/[:]+/', '-', trim($timeIn)).'.png';
	      $config['upload_path']       = 'assets/attendance/'.$dateIn.'/';
	      $config['allowed_types']     = '*';
	      $config['max_size']          = 2048;
	      $config['encrypt_name']      = true;

	      $this->load->library('upload', $config);

	      if(!$this->upload->do_upload('picture')){
	         $error = array('error' => $this->upload->display_errors());
					 $data = array("success" => 0, "message" => $error['error']);
					 generate_json($data);
					 exit();
	      }else{
	        $cdata = array('upload_data' => $this->upload->data());
	        $picture = $config['upload_path'].$cdata['upload_data']['file_name'];
	      }
	    }else{
	      $picture = "";
	    }

			if($picture == ""){
				$data = array("success" => 0, "message" => "No image captured. Please try again.");
				generate_json($data);
				exit();
			}

			### CHECK NO TIME OUT ###
			if($timelog->time_out == null){
				$login_status = "Time Out";
				$img_url = implode(',', array($timelog->img_url, $picture));
				$location = implode(',', array($timelog->current_location, $getLocation));
				// $update_data = array();
				$updated = $this->timelog_model->update_timelog($mode,$img_url,$location,$timeIn,$datetime,$dateIn,$empId);
				// echo $updated;
				// die();
				if($updated == false){
					$data = array("success" => 0, "message" => "Unable to Time Out. Please try again.");
					generate_json($data);
					exit();
				}
			}
			### CHECK HAS TIME OUT ###
			if($timelog->time_out != null){
				$login_status = "Time In";
				$insert_data = array(
					"employee_idno" => $empId,
					"worksite" => ($multi_worksite > 1) ? $tag_worksites : $user['work_site_id'],
					"date" => $dateIn,
					"mode" => $mode,
					"img_url" => $picture,
					"current_location" => $getLocation,
					"time_in" => $timeIn,
					"date_created" => $datetime
				);
				$inserted = $this->timelog_model->set_timelog($insert_data);
				if($inserted == false){
					$data = array("success" => 0, "message" => "Unable to Time In. Please try to reload the page and try again. ");
					generate_json($data);
					exit();
				}
			}
		}

		### FIRST TIME IN FOR THE DAY ###
		if($timelogexist->num_rows() == 0){

			### picture file upload ###
	    if(isset($_FILES['picture'])){
				$config['file_name'] = trim($empId).'_'.$dateIn.'_'.preg_replace('/[:]+/', '-', trim($timeIn)).'.png';
	      $config['upload_path']       = 'assets/attendance/'.$dateIn.'/';
	      $config['allowed_types']     = '*';
	      $config['max_size']          = 2048;
	      $config['encrypt_name']      = true;

	      $this->load->library('upload', $config);

	      if(!$this->upload->do_upload('picture')){
	         $error = array('error' => $this->upload->display_errors());
					 $data = array("success" => 0, "message" => $error['error']);
					 generate_json($data);
					 exit();
	      }else{
	        $cdata = array('upload_data' => $this->upload->data());
	        $picture = $config['upload_path'].$cdata['upload_data']['file_name'];
	      }
	    }else{
	      $picture = "";
	    }

			if($picture == ""){
				$data = array("success" => 0, "message" => "No image captured. Please try again.");
				generate_json($data);
				exit();
			}

			$login_status = "Time In";
			$insert_data = array(
				"employee_idno" => $empId,
				"worksite" => ($multi_worksite > 1) ? $tag_worksites : $user['work_site_id'],
				"date" => $dateIn,
				"mode" => $mode,
				"img_url" => $picture,
				"current_location" => $getLocation,
				"time_in" => $timeIn,
				"date_created" => $datetime
			);
			$inserted = $this->timelog_model->set_timelog($insert_data);
			if($inserted == false){
				$data = array("success" => 0, "message" => "Unable to Time In. Please try to reload the page and try again. ");
				generate_json($data);
				exit();
			}
		}

		$data = array(
			'success' => 1,
			'mode' => "<span class = 'text:center;'>Hello ".$user['fullname']."!</span>",
			'message' => $login_status." ".date('H:i A', strtotime($timeIn)),
			'location' => ($tag_worksites->row()->worksiteid != 1.1) ? $user['location'] : 'anywhere'
		);
		generate_json($data);
	}

	public function register_rfid(){
		$reg_employee_idno = $this->input->post('reg_employee_idno');
		$reg_rf_idnumber = $this->input->post('reg_rf_idnumber');

		if(empty($reg_employee_idno) || empty($reg_rf_idnumber)){
			$data = array("success" => 0, "message" => "Please fill up all required fields");
			generate_json($data);
			exit();
		}

		### CHECK EMPLOYEE EXISTS ###
		$emp_exist = $this->timelog_model->get_emp_information($reg_employee_idno);
		if($emp_exist->num_rows() === 0){
			$data = array("success" => 0, "message" => "Invalid Employee Id number");
			generate_json($data);
			exit();
		}

		### CHECK EMPLOYEE AND RF EXISTS ###
		$rf_registered_self = $this->timelog_model->get_emp_information($reg_employee_idno,$reg_rf_idnumber);
		if($rf_registered_self->num_rows() > 0){
			$data = array("success" => 0, "message" => "You already registered this Rf Id Number. Please try again.");
			generate_json($data);
			exit();
		}

		### CHECK IF EMPLOYEE HAVE ACTIVE RF ###
		$rf_exist = $this->timelog_model->get_rfid_thru_empid($reg_employee_idno);
		if($rf_exist->num_rows() > 0){
			$data = array("success" => 2, "message" => "It appears that you already have a RF Id Number. Would you like to change it ?.");
			generate_json($data);
			exit();
		}

		### CHECK IF RF STILL ENABLE ###
		$rf_exist2 = $this->timelog_model->get_rfid($reg_rf_idnumber);
		if($rf_exist2->num_rows() > 0){
			$data = array("success" => 0, "message" => "Rf Id Number already use. Please try another");
			generate_json($data);
			exit();
		}

		$insert_data = array(
			"employee_idno" => $reg_employee_idno,
			"rf_number" => $reg_rf_idnumber
		);

		$inserted = $this->timelog_model->set_rf_idnumber($insert_data);
		if($inserted === false){
			$data = array("success" => 0, "message" => "Unable to register Rf Id Number. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Rf Id Number Register Successfully.");
		generate_json($data);

	}

	public function update_rfid(){
		$rf_number = $this->input->post('rf_number');
		$emp_idno = $this->input->post('emp_idno');

		if(empty($rf_number) || empty($emp_idno)){
			$data = array("success" => 0, "message" => "Unable to update rf id number . Please try again.".$rf_number);
			generate_json($data);
			exit();
		}
		### GET LAST RF ID DATA ###
		$last_rfid = $this->timelog_model->get_last_rfid($emp_idno);
		if($last_rfid->num_rows() == 0){
			$data = array("success" => 0, "message" => "Unable to get last Rf Id Number data. Please try again.");
			generate_json($data);
			exit();
		}
		### CHECK IF Rf ID ALREADY EXISTS ###
		$rf_exist = $this->timelog_model->get_rfid($rf_number);
		if($rf_exist->num_rows() > 0){
			$data = array("success" => 0, "message" => "Rf Id Number already use. Please try again.");
			generate_json($data);
			exit();
		}

		$rf_data = $last_rfid->row();
		$update_data = array('inactive',$rf_data->id);
		$updated = $this->timelog_model->update_rf_status($update_data);
		if($updated === false){
			$data = array("success" => 0, "message" => "Unable to deactivate last Rf Id Number. Please try again.");
			generate_json($data);
			exit();
		}

		$insert_data = array(
			"employee_idno" => $emp_idno,
			"rf_number" => $rf_number
		);

		$inserted = $this->timelog_model->set_rf_idnumber($insert_data);
		if($inserted === false){
			$data = array("success" => 0, "message" => "Unable to register new Rf Id Number. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Rf Id Number Successfully Updated. ");
		generate_json($data);
	}

	### FACIAL RECOGNITION ###
	public function facial_recog_index($code = false){
		// $this->session->sess_destroy();
		// $data['facial_recog'] = json_encode($this->timelog_model->get_all_facial_recog()->result_array());
		if($code && isset($this->session->bcode)){
			if($code != en_dec('dec',$this->session->bcode)){
				$this->session->sess_destroy();
				header('location:'.base_url('employees/Timelog/facial_recog_index/'.$code));
				exit();
			}
		}

		$bcode = ($code === false) ? $this->input->post('bcode') : $code;
		if(!empty($bcode) && !isset($this->session->database_name)){
			$this->load->model('branch/branch_model');
			$dbname = $this->branch_model->get_hris_branch($bcode,'branch_code');
			if($dbname->num_rows() == 0){
				$data = array("success" => 0, "facial_recog" => 0, "message" => "No available data");
				generate_json($data);
				exit();
			}

			$this->session->set_userdata('database_name', $dbname->row()->database_name);
			$this->session->set_userdata('branch_name', $dbname->row()->branch_name);
			$this->session->set_userdata('timezone', $dbname->row()->timezone);
			$this->session->set_userdata('location', $dbname->row()->location);
			if($code){
				$this->session->set_userdata('bcode', en_dec('en',$code));
				header('location:'.base_url('employees/Timelog/facial_recog_index'));
			}
			$data = array("success" => 1);
			generate_json($data);
			exit();
		}
		$data['code'] = (!empty($bcode)) ? $bcode : '';
		$this->load->view('employees/facial_recog',$data);
	}

	public function get_all_facial_recog(){
		$facial_recog = $this->timelog_model->get_all_facial_recog();
		if($facial_recog->num_rows() > 0){
			$data = array("success" => 1, "facial_recog" => json_encode($facial_recog->result_array()));
		}else{
			$data = array("success" => 0, "facial_recog" => 0, "message" => "No available data");
		}

		generate_json($data);
	}

	public function timelog($branch_code,$worksitename,$worksitecode){
		$bcode = en_dec('dec',$branch_code);
		$worksite = str_replace('%20', ' ', $worksitename);
		$worksite_code = en_dec('dec',$worksitecode);

		if(empty($bcode)){
			$this->logout();
			exit();
		}

		if(!empty($bcode) && !isset($this->session->database_name)){
			$this->load->model('branch/branch_model');
			$dbname = $this->branch_model->get_hris_branch($bcode,'branch_code');
			if($dbname->num_rows() == 0){
				$data = array("success" => 0, "facial_recog" => 0, "message" => "No available data");
				generate_json($data);
				exit();
			}

			$this->session->set_userdata('database_name', $dbname->row()->database_name);
			$this->session->set_userdata('branch_name', $dbname->row()->branch_name);
			$this->session->set_userdata('timezone', $dbname->row()->timezone);
			$this->session->set_userdata('location',$dbname->row()->location);
			$this->db = switch_database($this->session->database_name);
			// $data = array("success" => 1);
			// generate_json($data);
			// exit();
		}

		$isExist = $this->timelog_model->check_worksite_link($worksite,$worksite_code);
		if($isExist->num_rows() == 0){
			$data = array("success" => 0, "message" => "Invalid link");
			generate_json($data);
			$this->logout();
			exit();
		}

		$data['code'] = (!empty($bcode)) ? $bcode : '';
		$this->load->view('employees/facial_recog_2',$data);


	}

	public function timelog2($branch_code,$worksitename,$worksitecode,$device_id){
		$bcode = en_dec('dec',$branch_code);
		$worksite = str_replace('%20', ' ', $worksitename);
		$worksite_code = en_dec('dec',$worksitecode);
		$activated = 0;

		if(empty($device_id)){
			$data = array("success" => 0, "message" => "Unable to fetch device id");
			generate_json($data);
			exit();
		}

		if(empty($bcode)){
			$this->logout();
			exit();
		}
		// echo('33333');
		if(!empty($bcode) && !isset($this->session->database_name)){
			$this->load->model('branch/branch_model');
			$dbname = $this->branch_model->get_hris_branch($bcode,'branch_code');
			if($dbname->num_rows() == 0){
				$data = array("success" => 0, "facial_recog" => 0, "message" => "No available data");
				generate_json($data);
				exit();
			}

			$this->session->set_userdata('database_name', $dbname->row()->database_name);
			$this->session->set_userdata('branch_name', $dbname->row()->branch_name);
			$this->session->set_userdata('timezone', $dbname->row()->timezone);
			$this->session->set_userdata('location',$dbname->row()->location);
			$this->db = switch_database($this->session->database_name);

			// $data = array("success" => 1);
			// generate_json($data);
			// exit();
		}

		$isActivated = $this->registered_device_model->get_device($device_id);
		if($isActivated->num_rows() > 0){
			$activated = 1;
		}else{
			$this->session->set_userdata('device_id', $device_id);
		}


		$isExist = $this->timelog_model->check_worksite_link($worksite,$worksite_code);
		if($isExist->num_rows() == 0){
			$data = array("success" => 0, "message" => "Invalid link");
			generate_json($data);
			$this->logout();
			exit();
		}

		$data['code'] = (!empty($bcode)) ? $bcode : '';
		$data['activated'] = $activated;
		$this->load->view('employees/facial_recog_2',$data);


	}

	public function register_device(){
		$activation_code = $this->input->post('activation_code');
		$device_id = $this->session->device_id;

		if(empty($activation_code) && empty($device_id)){
			$data = array("success" => 0, "message" => "Device Activation Failed. Please try again.");
			generate_json($data);
			exit();
		}

		$isExist = $this->registered_device_model->get_activation_code($activation_code);
		if($isExist->num_rows() == 0){
			$data = array("success" => 0, "message" => "Invalid Activation Code");
			generate_json($data);
			exit();
		}

		$isActivated = $this->registered_device_model->get_device($device_id);
		if($isActivated->num_rows() > 0){
			$data = array("success" => 0, "message" => "Device already registered");
			generate_json($data);
			exit();
		}

		$update_data = array($device_id,$activation_code);
		$updated = $this->registered_device_model->register_device($update_data);
		if($updated === false){
			$data = array("success" => 0, "message" => "Unable to register device. Please try again.");
			generate_json($data);
			exit();
		}

		$data = array("success" => 1, "message" => "Device Registered Successfully");
		generate_json($data);
	}

	public function redirect_to_clock_in(){
		// $this->session->sess_destroy();
		header("location:".base_url('employees/Timelog'));
		exit();
	}

	public function redirect_to_facial_recog(){
		// $this->session->sess_destroy();
		header('location:'.base_url('employees/Timelog/facial_recog_index'));
		exit();
	}

	public function refresh_time(){
		if(isset($this->session->uptime)){
			$uptime = (int)$this->session->uptime + 10;
			$this->session->set_userdata('uptime', $uptime);
		}else{
			$this->session->set_userdata('uptime',10);
		}

		// echo $this->session->uptime;

	}

}
