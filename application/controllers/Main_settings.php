<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Main_settings extends CI_Controller {

	public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
    }
    
	public function index() {
		if($this->session->userdata('isLoggedIn') == true) {

			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}

		$this->load->view('login');
	}

	public function isLoggedIn() {
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

	// Admin Settings 012018 - Paul Chua

	// Under Navigation of Settings

	public function settings_home($token = '') {
		$this->isLoggedIn();

		$data_admin = array(
			 // get data using email
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_home', $data_admin);
		}else{
			$this->logout();
		}
	}

	// Start - Area	
	public function area($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_area', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function area_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$area = $this->input->post('area');
			$query = $this->model_settings->area_table($area);
		}
		echo json_encode($query);
	}

	public function insert_area(){
		$info_desc = sanitize($this->input->post('info_desc'));
		$monday_check = sanitize($this->input->post('monday_check'));
		$tuesday_check = sanitize($this->input->post('tuesday_check'));
		$wednesday_check = sanitize($this->input->post('wednesday_check'));
		$thursday_check = sanitize($this->input->post('thursday_check'));
		$friday_check = sanitize($this->input->post('friday_check'));
		$saturday_check = sanitize($this->input->post('saturday_check'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		}
		else {
			if($this->session->userdata('position_id') != ""){ //admin
				$isExists = $this->model_settings->get_area_unique($info_desc);

				if($isExists->num_rows() == 0) {
					$areaId = $this->model_settings->insert_area($info_desc);
					$query = $this->model_settings->insert_areasched($areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else {
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			}
			else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_area() {
		$areaId = sanitize($this->input->post('areaId'));

		$query = $this->model_settings->get_area($areaId);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_area_unique($info_unique) {

		$query = $this->model_settings->get_area_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_area() {

		$info_areaId = sanitize($this->input->post('info_areaId'));
		$info_desc = sanitize($this->input->post('info_desc'));
		$monday_check = sanitize($this->input->post('monday_check'));
		$tuesday_check = sanitize($this->input->post('tuesday_check'));
		$wednesday_check = sanitize($this->input->post('wednesday_check'));
		$thursday_check = sanitize($this->input->post('thursday_check'));
		$friday_check = sanitize($this->input->post('friday_check'));
		$saturday_check = sanitize($this->input->post('saturday_check'));

		if ($info_desc == "" || $info_areaId == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$query1 = $this->model_settings->update_area($info_areaId, $info_desc);

				$checkAreaExist= $this->model_settings->checkAreaExist($info_areaId);

				if($checkAreaExist -> num_rows() > 0) {
				$query2 = $this->model_settings->update_areasched($info_areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check);
				}
				else {
					$query3 = $this->model_settings->insert_areasched($info_areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check);
				}

				$data = array("success" => 1, 'message' => 'Successfully updated');

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_area() {

		$del_areaId = sanitize($this->input->post('del_areaId'));

		if ($del_areaId == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_area($del_areaId);

			$data = array("success" => 1, 'message' => "Area Deleted!" , "del_areaId" => $del_areaId);
		}

		generate_json($data);
	}
	// End - Area

	// Start - Credit Term	
	public function credit_term($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_credit_term', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function credit_term_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$credit = $this->input->post("credit");
			$query = $this->model_settings->credit_term_table($credit);
		}
		echo json_encode($query);
	}

	public function insert_credit_term(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin
				$isExists = $this->model_settings->get_credit_term_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_credit_term($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_credit_term() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_credit_term($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_credit_term_unique($info_unique) {

		$query = $this->model_settings->get_credit_term_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_credit_term() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));
		
		$termChecking = $this->model_sql->selectNow('8_credit','id','description',$info_desc)->row();
		$originalTerm = $this->model_sql->selectNow('8_credit','description','id',$info_id)->row();
		
		if(!is_null($termChecking)) {
		
			 if($originalTerm->description != $info_desc) {
			 	
				 $data = array("success" => 2, 'message' => 'Term Already Exist.');
				 echo json_encode($data);
				 
			 }else {

				if ($info_desc == "" || $info_id == "") {
					
					$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
				} else {

					if($this->session->userdata('position_id') != ""){ //admin

						$query1 = $this->model_settings->update_credit_term($info_id, $info_desc);

						$data = array("success" => 1, 'message' => 'Successfully updated');

					} else {
						$this->logout();
					}
				}

				generate_json($data);

			 }

		}else {

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$query1 = $this->model_settings->update_credit_term($info_id, $info_desc);

					$data = array("success" => 1, 'message' => 'Successfully updated');

				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

	}

	public function delete_credit_term() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_credit_term($del_id);

			$data = array("success" => 1, 'message' => "Credit Term Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Credit Term

	// Start - Delivery Vehicle	
	public function delivery_vehicle($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_delivery_vehicle', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function delivery_vehicle_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$plateno = $this->input->post("plateno");
			$query = $this->model_settings->delivery_vehicle_table($plateno);
		}
		echo json_encode($query);
	}

	public function insert_delivery_vehicle(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_delivery_vehicle_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_delivery_vehicle($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_delivery_vehicle() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_delivery_vehicle($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_delivery_vehicle_unique($info_unique) {

		$query = $this->model_settings->get_delivery_vehicle_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_delivery_vehicle() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));

		$plateNoChecking = $this->model_sql->selectNow('9_delvehicle','plateno','plateno',$info_desc)->row();
		$originalPlateNo = $this->model_sql->selectNow('9_delvehicle','plateno','id',$info_id)->row();

		if( ( !is_null($plateNoChecking)) ) {

			if($originalPlateNo->plateno != $info_desc) {

				$data = array("success" => 2, 'message' => 'Plate# Exist');
				echo json_encode($data);
				
			}else {

				if ($info_desc == "" || $info_id == "") {
					
					$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
				} else {

					if($this->session->userdata('position_id') != ""){ //admin

						$query1 = $this->model_settings->update_delivery_vehicle($info_id, $info_desc);

						$data = array("success" => 1, 'message' => 'Successfully updated');

					} else {
						$this->logout();
					}
				}

				generate_json($data);
			}

		}else {

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$query1 = $this->model_settings->update_delivery_vehicle($info_id, $info_desc);

					$data = array("success" => 1, 'message' => 'Successfully updated');

				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

	}

	public function delete_delivery_vehicle() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_delivery_vehicle($del_id);

			$data = array("success" => 1, 'message' => "Delivery Vehicle Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Delivery Vehicle

	// Start - Employee	
	public function employee($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_emptype' => $this->model_settings->get_emptype(),
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_employee', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function employee_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$id = $this->input->post("id");
			$name = $this->input->post("name");
			$type = $this->input->post("type");
			$query = $this->model_settings->employee_table($id, $name, $type);
		}
		echo json_encode($query);
	}

	public function insert_employee(){
		$info_empid = sanitize($this->input->post('info_empid'));
		$info_fname = sanitize($this->input->post('info_fname'));
		$info_mname = sanitize($this->input->post('info_mname'));
		$info_lname = sanitize($this->input->post('info_lname'));
		$info_type = sanitize($this->input->post('info_type'));

		$empIdChecker = $this->model_sql->selectNow('jcw_employee','id','empid',$info_empid)->row();


		if ($info_empid == "" || $info_fname == "" || $info_lname == "" || $info_type == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_employee_unique($info_fname, $info_mname, $info_lname, $info_empid, $info_type);

				if($isExists->num_rows() == 0)
				{
					if(is_null($empIdChecker)) {
						$id = $this->model_settings->insert_employee($info_empid, $info_fname, $info_mname, $info_lname, $info_type);
						$data = array("success" => 1, 'message' => 'Successfully Added');
					}else {
						 $data = array('success' => 2, 'message' => 'Employee ID already exist.');
					}
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_employee() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_employee($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_employee_unique($info_fname, $info_mname, $info_lname, $info_empid) {

		$query = $this->model_settings->get_employee_unique($info_fname, $info_mname, $info_lname, $info_empid);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_employee() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_empid = sanitize($this->input->post('info_empid'));
		$info_fname = sanitize($this->input->post('info_fname'));
		$info_mname = sanitize($this->input->post('info_mname'));
		$info_lname = sanitize($this->input->post('info_lname'));
		$info_type = sanitize($this->input->post('info_type'));

		$empIdChecker = $this->model_sql->selectNow('jcw_employee','id','empid',$info_empid)->row();
		$originalEmpId = $this->model_sql->selectNow('jcw_employee','empid','id',$info_id)->row();

		if ($info_id == "" || $info_empid == "" || $info_fname == "" || $info_lname == "" || $info_type == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin
				if(!is_null($empIdChecker)) {
					$data = array("success" => 2, 'message' => 'Employee Already Exist.');
				}else {
					if($originalEmpId->empid == $info_empid) {
						$data = array("success" => 2, 'message' => 'Employee Id Already Exist.');
					}else {
						$query1 = $this->model_settings->update_employee($info_id, $info_empid, $info_fname, $info_mname, $info_lname, $info_type);
						$data = array("success" => 1, 'message' => 'Successfully updated');
					}
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_employee() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_employee($del_id);

			$data = array("success" => 1, 'message' => "Employee Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Employee

	// Start - Employee Type
	public function employee_type($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_employee_type', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function employee_type_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$empType = $this->input->post("empType");
			$query = $this->model_settings->employee_type_table($empType);
		}
		echo json_encode($query);
	}

	public function insert_employee_type(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_employee_type_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_employee_type($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_employee_type() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_employee_type($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_employee_type_unique($info_unique) {

		$query = $this->model_settings->get_employee_type_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_employee_type() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));
		$info_desc1 = sanitize($this->input->post('info_desc1'));


		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin


				if($info_desc == $info_desc1)
				{
					$query1 = $this->model_settings->update_employee_type($info_id, $info_desc);
					$data = array("success" => 1, 'message' => 'Successfully updated');
				}
				else
				{
					$isExists = $this->model_settings->get_employee_type_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$query1 = $this->model_settings->update_employee_type($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}
				}

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_employee_type() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_employee_type($del_id);

			$data = array("success" => 1, 'message' => "Employee Type Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Employee Type

	// Start - Franchise
	public function franchise($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_franchise', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function franchise_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$franchise = $this->input->post("franchise");
			$query = $this->model_settings->franchise_table($franchise);
		}
		echo json_encode($query);
	}

	public function insert_franchise(){
		$info_desc = sanitize($this->input->post('info_desc'));
		$info_fee = sanitize($this->input->post('info_fee'));
		$info_cashbond = sanitize($this->input->post('info_cashbond'));
		$info_commission = sanitize($this->input->post('info_commission'));


		if ($info_desc == "" || $info_fee == "" || $info_cashbond == "" || $info_commission == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');

		} 
		else if (filter_var($info_fee, FILTER_VALIDATE_INT) === false || filter_var($info_cashbond, FILTER_VALIDATE_INT) === false || filter_var($info_commission, FILTER_VALIDATE_INT) === false) {
			
			$data = array("success" => 0, 'message' => 'Please make sure to enter proper amount.');
		}
		else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_franchise_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_franchise($info_desc, $info_fee, $info_cashbond, $info_commission);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_franchise() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_franchise($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_franchise_unique($info_unique) {

		$query = $this->model_settings->get_franchise_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_franchise() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));
		$info_fee = sanitize($this->input->post('info_fee'));
		$info_cashbond = sanitize($this->input->post('info_cashbond'));
		$info_commission = sanitize($this->input->post('info_commission'));

		$franchiseChecker = $this->model_sql->selectNow('8_franchises','id','description',$info_desc)->row();
		$originalFranchise = $this->model_sql->selectNow('8_franchises','description','id',$info_id)->row();

		if ($info_desc == "" || $info_id == "" || $info_fee == "" || $info_cashbond == "" || $info_commission == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');

		} 
		else if (filter_var($info_fee, FILTER_VALIDATE_INT) === false || filter_var($info_cashbond, FILTER_VALIDATE_INT) === false || filter_var($info_commission, FILTER_VALIDATE_INT) === false) {
			
			$data = array("success" => 0, 'message' => 'Please make sure to enter proper amount.');
		}
		else {


			if($this->session->userdata('position_id') != ""){ //admin


				if(!is_null($franchiseChecker)) {

					if($originalFranchise->description == $info_desc) {

						$query1 = $this->model_settings->update_franchise($info_id, $info_desc, $info_fee, $info_cashbond, $info_commission);
						$data = array("success" => 1, 'message' => 'Successfully Updated');

					}else {

						$data = array("success" => 2, 'message' => 'Franchise Already Exist');

					}

				}else {

					$query1 = $this->model_settings->update_franchise($info_id, $info_desc, $info_fee, $info_cashbond, $info_commission);
					$data = array("success" => 1, 'message' => 'Successfully Updated');
				
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_franchise() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_franchise($del_id);

			$data = array("success" => 1, 'message' => "Franchise Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Franchise

	// Start - GL Accounts
	public function gl_accounts($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_gl_accounts', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function gl_accounts_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$account = $this->input->post("account");
			$type = $this->input->post("type");
			$query = $this->model_settings->gl_accounts_table($account, $type);
		}
		echo json_encode($query);
	}

	public function insert_gl_accounts(){
		$info_desc = sanitize($this->input->post('info_desc'));
		$info_type = sanitize($this->input->post('info_type'));


		if ($info_desc == "" || $info_type == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {


			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_gl_accounts_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_gl_accounts($info_desc, $info_type);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_gl_accounts() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_gl_accounts($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_gl_accounts_unique($info_unique) {

		$query = $this->model_settings->get_gl_accounts_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_gl_accounts() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));
		$info_type = sanitize($this->input->post('info_type'));

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_gl_accounts_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->update_gl_accounts($info_id, $info_desc, $info_type);

					$data = array("success" => 1, 'message' => 'Successfully Updated');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_gl_accounts() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_gl_accounts($del_id);

			$data = array("success" => 1, 'message' => "GL Accounts Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - GL Accounts

	// Start - Inventory Category
	public function inventory_category($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_inventory_category', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function inventory_category_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$category = $this->input->post("category");
			$query = $this->model_settings->inventory_category_table($category);
		}
		echo json_encode($query);
	}

	public function insert_inventory_category(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {
			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_inventory_category_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_inventory_category($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_inventory_category() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_inventory_category($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_inventory_category_unique($info_unique) {

		$query = $this->model_settings->get_inventory_category_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_inventory_category() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_inventory_category_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->update_inventory_category($info_id,$info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Updated');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_inventory_category() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_inventory_category($del_id);

			$data = array("success" => 1, 'message' => "Inventory Category Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Inventory Category

	// Start - Payment Option
	public function payment_option($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_payment_option', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function payment_option_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$payment = $this->input->post("payment");
			$query = $this->model_settings->payment_option_table($payment);
		}
		echo json_encode($query);
	}

	public function insert_payment_option(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') == "2"){ //admin

				$isExists = $this->model_settings->get_payment_option_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
				$id = $this->model_settings->insert_payment_option($info_desc);

				$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_payment_option() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_payment_option($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_payment_option_unique($info_unique) {

		$query = $this->model_settings->get_payment_option_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}


	public function update_payment_option() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));

		$termChecker = $this->model_sql->selectNow('8_payment','id','description',$info_desc)->row();
		$originalTerm = $this->model_sql->selectNow('8_payment','description','id',$info_id)->row();


		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				if(!is_null($termChecker)) {

					if($originalTerm->description == $info_desc) {

						$query1 = $this->model_settings->update_payment_option($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');

					}else {

						$data = array("success" => 2, 'message' => 'Term Already Exist');

					}

				}else {

					$query1 = $this->model_settings->update_payment_option($info_id, $info_desc);
					$data = array("success" => 1, 'message' => 'Successfully updated');

				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_payment_option() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_payment_option($del_id);

			$data = array("success" => 1, 'message' => "Payment Option Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Payment Option

	// Start - Price Category
	public function price_category($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_price_category', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function price_category_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$category = $this->input->post("description");
			$query = $this->model_settings->price_category_table($category);
		}
		echo json_encode($query);
	}

	public function insert_price_category(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_price_category_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_price_category($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_price_category() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_price_category($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_price_category_unique($info_unique) {

		$query = $this->model_settings->get_price_category_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_price_category() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_price_category_unique($info_desc);

			if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->update_price_category($info_id,$info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Updated');
				}
			else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_price_category() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_price_category($del_id);

			$data = array("success" => 1, 'message' => "Price Category Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Price Category

	// Start - Sales Area
	public function sales_area($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_sales_area', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function sales_area_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$area = $this->input->post("area");
			$query = $this->model_settings->sales_area_table($area);
		}
		echo json_encode($query);
	}

	public function insert_sales_area(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_sales_area_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_sales_area($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_sales_area() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_sales_area($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_sales_area_unique($info_unique) {

		$query = $this->model_settings->get_sales_area_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_sales_area() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_sales_area_unique($info_desc);

				if($isExists->num_rows() == 0)
				{

				$query1 = $this->model_settings->update_sales_area($info_id, $info_desc);

				$data = array("success" => 1, 'message' => 'Successfully updated');

				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}


	public function delete_sales_area() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_sales_area($del_id);

			$data = array("success" => 1, 'message' => "Sales Area Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Sales Area

	// Start - Shipping
	public function shipping($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_shipping', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function shipping_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$query = $this->model_settings->shipping_table();
		}
		echo json_encode($query);
	}

	public function insert_shipping(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_shipping_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_shipping($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_shipping() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_shipping($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_shipping_unique($info_unique) {

		$query = $this->model_settings->get_shipping_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_shipping() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$query1 = $this->model_settings->update_shipping($info_id, $info_desc);

				$data = array("success" => 1, 'message' => 'Successfully updated');

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_shipping() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_shipping($del_id);

			$data = array("success" => 1, 'message' => "Shipping Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Shipping

	// Start - Ticket Status
	public function ticket_status($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_ticket_status', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function ticket_status_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$status = $this->input->post("status");
			$query = $this->model_settings->ticket_status_table($status);
		}
		echo json_encode($query);
	}

	public function insert_ticket_status(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_ticket_status_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_ticket_status($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_ticket_status() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_ticket_status($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_ticket_status_unique($info_unique) {

		$query = $this->model_settings->get_ticket_status_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_ticket_status() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));


		$ticketChecker = $this->model_sql->selectNow('8_ticketstatus','id','description',$info_desc)->row();
		$originalTicket = $this->model_sql->selectNow('8_ticketstatus','description','id',$info_id)->row();

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				if(!is_null($ticketChecker)) {
					if($originalTicket->description == $info_desc) {
						$query1 = $this->model_settings->update_ticket_status($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');
					}else {
						 $data = array("success" => 2, 'message' => 'Ticket Already Exist.');
					}
				}else {
					$data = array("success" => 2, 'message' => 'Ticket Already Exist.');
				}

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_ticket_status() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_ticket_status($del_id);

			$data = array("success" => 1, 'message' => "Ticket Status Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Ticket Status

	// Start - Unit of Measurement
	public function uom($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_uom', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function uom_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$uom = $this->input->post("uom");
			$query = $this->model_settings->uom_table($uom);
		}
		echo json_encode($query);
	}

	public function insert_uom(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_uom_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_uom($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_uom() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_uom($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_uom_unique($info_unique) {

		$query = $this->model_settings->get_uom_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_uom() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));


		$uomChecker = $this->model_sql->selectNow('8_uom','id','description',$info_desc)->row();
		$uomOriginal = $this->model_sql->selectNow('8_uom','description','id',$info_id)->row();

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				if(!is_null($uomChecker)) {
					if($uomOriginal->description == $info_desc) {
						$query1 = $this->model_settings->update_uom($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');
					}else {
						$data = array("success" => 2, 'message' => 'Unit of Measure already exist.');
					}
				}else {
					$data = array("success" => 2, 'message' => 'Unit of Measure already exist.');
				}

			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_uom() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_uom($del_id);

			$data = array("success" => 1, 'message' => "Unit of Measurement Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Unit of Measurement

	// Start - Warehouse Location
	public function warehouse_location($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_warehouse_location', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function warehouse_location_table(){
		if ($this->session->userdata('position_id') != "") { // admin
			$location = $this->input->post("location");
			$query = $this->model_settings->warehouse_location_table($location);
		}
		echo json_encode($query);
	}

	public function insert_warehouse_location(){
		$info_desc = sanitize($this->input->post('info_desc'));


		if ($info_desc == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				$isExists = $this->model_settings->get_warehouse_location_unique($info_desc);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->insert_warehouse_location($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);
	}

	public function get_warehouse_location() {
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_warehouse_location($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_warehouse_location_unique($info_unique) {

		$query = $this->model_settings->get_warehouse_location_unique($info_unique);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function update_warehouse_location() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_desc = sanitize($this->input->post('info_desc'));

		$locationChecker = $this->model_sql->selectNow('8_itemloc','id','description',$info_desc)->row();
		$originalLocation = $this->model_sql->selectNow('8_itemloc','description','id',$info_id)->row();

		if ($info_desc == "" || $info_id == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin

				if(!is_null($locationChecker)) {
					if($originalLocation->description == $info_desc) {
						$query1 = $this->model_settings->update_warehouse_location($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');
					}else {
						$data = array("success" => 2, 'message' => 'Location already exist.');
					}

				}else {
					$data = array("success" => 2, 'message' => 'Location already exist.');
				}


			} else {
				$this->logout();
			}
		}

		generate_json($data);

	}

	public function delete_warehouse_location() {

		$del_id = sanitize($this->input->post('del_id'));

		if ($del_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_warehouse_location($del_id);

			$data = array("success" => 1, 'message' => "Warehouse Location Deleted!" , "del_id" => $del_id);
		}

		generate_json($data);
	}

	// End - Warehouse Location

	//Start - user role
	
	public function user_role($token = ''){
		$this->isLoggedIn();
		$get_main_nav = $this->model_settings->get_main_nav()->result();

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'get_main_nav' => $get_main_nav
		);

		$this->load->view('includes/header', $data);
		$this->load->view('settings/settings_user_role', $data);
	}

	public function user_role_table(){
		$position = $this->input->post("position");
		$query = $this->model_settings->user_role_table($position);
		echo json_encode($query);
	}

	public function add_userrole(){
		$r_position = sanitize($this->input->post('a_position'));
		$acb_sales = sanitize($this->input->post('acb_sales'));
		$acb_purchases = sanitize($this->input->post('acb_purchases'));
		$acb_inventory = sanitize($this->input->post('acb_inventory'));
		$acb_entity = sanitize($this->input->post('acb_entity'));
		$acb_manufacturing = sanitize($this->input->post('acb_manufacturing'));
		$acb_accounts = sanitize($this->input->post('acb_accounts'));
		$acb_settings = sanitize($this->input->post('acb_settings'));
		$acb_packagecart = sanitize($this->input->post('acb_packagecart'));
		$acb_reports = sanitize($this->input->post('acb_reports'));
		$acb_qr = sanitize($this->input->post('acb_qr')); //072318
		$acb_ds = sanitize($this->input->post('acb_ds')); //072618
		$acb_content = $this->input->post('acb_content');  //071618
		
		if (empty($acb_content)) {
			$data = array('success' => 0, 'message' => 'Please choose Content Navigation Role.');
			generate_json($data);
			die();
		}
		$acb_content_str = implode(", ",$acb_content); //071618

		$checkbox_arr = array();
		if (!empty($acb_sales)){
			array_push($checkbox_arr, $acb_sales);
		}

		if (!empty($acb_purchases)){
			array_push($checkbox_arr, $acb_purchases);
		}

		if (!empty($acb_inventory)){
			array_push($checkbox_arr, $acb_inventory);
		}

		if (!empty($acb_entity)){
			array_push($checkbox_arr, $acb_entity);
		}

		if (!empty($acb_manufacturing)){
			array_push($checkbox_arr, $acb_manufacturing);
		}

		if (!empty($acb_accounts)){
			array_push($checkbox_arr, $acb_accounts);
		}

		if (!empty($acb_settings)){
			array_push($checkbox_arr, $acb_settings);
		}

		if (!empty($acb_packagecart)){
			array_push($checkbox_arr, $acb_packagecart);
		}

		if (!empty($acb_reports)){
			array_push($checkbox_arr, $acb_reports);
		}
		if (!empty($acb_qr)){
			array_push($checkbox_arr, $acb_qr);
		}
		if (!empty($acb_ds)){
			array_push($checkbox_arr, $acb_ds);
		}

		$checkbox_str = implode(", ",$checkbox_arr);

		if (!empty($r_position)){
			$res = $this->model_settings->checkunique_userrole($r_position);
			if($res->num_rows() > 0)
			{
				$data = array('success' => 0, 'message' => 'Duplicate user role. Please check your data.');
			}
			else
			{
				$query = $this->model_settings->add_userrole($r_position, $checkbox_str, $acb_content_str); //071618
				$data = array('success' => 1, 'message' => 'Successfully added!');
			}
		}else{
			$data = array('success' => 0, 'message' => 'Please fill up required fields.');
		}
		generate_json($data);
	}

	public function edit_userrole(){
		$r_position_id = sanitize($this->input->post('r_position_id'));
		$r_positionorig = sanitize($this->input->post('r_positionorig'));
		$r_position = sanitize($this->input->post('r_position'));
		$cb_home = sanitize($this->input->post('cb_home'));
		$cb_sales = sanitize($this->input->post('cb_sales'));
		$cb_purchases = sanitize($this->input->post('cb_purchases'));
		$cb_inventory = sanitize($this->input->post('cb_inventory'));
		$cb_entity = sanitize($this->input->post('cb_entity'));
		$cb_manufacturing = sanitize($this->input->post('cb_manufacturing'));
		$cb_accounts = sanitize($this->input->post('cb_accounts'));
		$cb_settings = sanitize($this->input->post('cb_settings'));
		$cb_packagecart = sanitize($this->input->post('cb_packagecart'));
		$cb_reports = sanitize($this->input->post('cb_reports'));
		$cb_qr = sanitize($this->input->post('cb_qr')); //072318
		$cb_ds = sanitize($this->input->post('cb_ds')); //072618
		$cb_content = $this->input->post('cb_content'); //071618

		if (empty($cb_content)) {
			$data = array('success' => 0, 'message' => 'Please choose Content Navigation Role.');
			generate_json($data);
			die();
		}
		$content_checkbox_str = implode(", ",$cb_content);

		$checkbox_arr = array();

		if (!empty($cb_home)){
			array_push($checkbox_arr, $cb_home);
		}

		if (!empty($cb_sales)){
			array_push($checkbox_arr, $cb_sales);
		}

		if (!empty($cb_purchases)){
			array_push($checkbox_arr, $cb_purchases);
		}

		if (!empty($cb_inventory)){
			array_push($checkbox_arr, $cb_inventory);
		}

		if (!empty($cb_entity)){
			array_push($checkbox_arr, $cb_entity);
		}

		if (!empty($cb_manufacturing)){
			array_push($checkbox_arr, $cb_manufacturing);
		}

		if (!empty($cb_accounts)){
			array_push($checkbox_arr, $cb_accounts);
		}

		if (!empty($cb_settings)){
			array_push($checkbox_arr, $cb_settings);
		}

		if (!empty($cb_packagecart)){
			array_push($checkbox_arr, $cb_packagecart);
		}

		if (!empty($cb_reports)){
			array_push($checkbox_arr, $cb_reports);
		}

		if (!empty($cb_qr)){
			array_push($checkbox_arr, $cb_qr);
		}

		if (!empty($cb_ds)){
			array_push($checkbox_arr, $cb_ds);
		}

		$checkbox_str = implode(", ",$checkbox_arr);

		if (!empty($r_position_id)){

			if($r_position == $r_positionorig)
			{
				$query = $this->model_settings->edit_userrole($r_position_id, $r_position, $checkbox_str, $content_checkbox_str);
				$data = array('success' => 1, 'message' => 'Successfully edited!');
			}
			else
			{
				$res = $this->model_settings->checkunique_userrole($r_position);
				if($res->num_rows() > 0)
				{
					$data = array('success' => 0, 'message' => 'Duplicate user role. Please check your data.');
				}
				else
				{
					$query = $this->model_settings->edit_userrole($r_position_id, $r_position, $checkbox_str, $content_checkbox_str);
					$data = array('success' => 1, 'message' => 'Successfully edited!');
				}
			}

			
		}else{
			$data = array('success' => 0, 'message' => 'Something went wrong, please try again.');
		}
		generate_json($data);
	}

	public function delete_userrole(){
		$r_position_id_delete = sanitize($this->input->post('r_position_id_delete'));
		if ($r_position_id_delete > 0) {
			$this->model_settings->delete_userrole($r_position_id_delete);
			$data = array('success' => 1, 'message' => 'User Role Deleted!');
		}else{
			$data = array('success' => 0, 'message' => 'something went wrong, please try again.');
		}

		generate_json($data);
	}

	//End - user role

			// Start - System Users
		public function system_user($token = ''){ //
			$this->isLoggedIn();
			
			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
				'positions' => $this->model_settings->get_positions()->result_array()
			);
			
			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_system_user', $data_admin);
			}else{
				$this->logout();
			}
		}
		
		public function system_user_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$position = $this->input->post("position");
				$query = $this->model_settings->system_user_table($position);
			}
			echo json_encode($query);
		}
		
	public function insert_system_user(){
		$info_position = sanitize($this->input->post('info_position'));
		$info_user_fname = sanitize($this->input->post('info_user_fname'));
		$info_user_mname = sanitize($this->input->post('info_user_mname'));
		$info_user_lname = sanitize($this->input->post('info_user_lname'));
		$info_username = sanitize($this->input->post('info_username'));
		$info_password = sanitize($this->input->post('info_password'));
		$info_re_password = sanitize($this->input->post('info_re_password'));			
		
		if ($info_position == "" || $info_user_fname == "" || $info_user_lname == "" || $info_username == "" || $info_password == "" || $info_re_password == "") {	//Check if required fields are filled up			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {
			if($this->session->userdata('position_id') != ""){ //admin
				$isExists = $this->model_settings->get_system_user_unique($info_username);
				if($isExists == 0){
					if($info_password == $info_re_password){
						$row = array(
							'username' => $info_username, 
							'user_fname' => $info_user_fname, 
							'user_mname' => $info_user_mname, 
							'user_lname' => $info_user_lname, 
							'position_id' => $info_position, 
							'password' => password_hash($info_password,PASSWORD_BCRYPT), 
							'date_activated' => date('Y-m-d H:i:s'), 
							'date_created' => date('Y-m-d H:i:s'), 
							'enabled' => 1, 
						);
						$id = $this->model_settings->insert_system_user($row);
						$data = array("success" => 1, 'message' => 'Successfully Added');
					}else{
						$data = array('success' => 0, 'message' => 'Password and Password Confirmation do not match.');
					}
				}
				else{
					$data = array('success' => 0, 'message' => 'Username is already taken. Please try again.');
				}
				
				
			} else {
				$this->logout();
			}
		}
		generate_json($data);
	}
	
	public function get_system_user() {
		$id = sanitize($this->input->post('user_id'));
		
		$query = $this->model_settings->get_system_user($id);
		
		if ($query->num_rows() > 0) {
			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}
		
		generate_json($data);
		
	}
	
	public function get_system_user_unique($info_unique) {
		
		$query = $this->model_settings->get_warehouse_location_unique($info_unique);
		
		if ($query->num_rows() > 0) {
			
			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}
		
		generate_json($data);
		
	}
	
	public function update_system_user() {
		
		$info_position = sanitize($this->input->post('info_position'));
		$info_user_fname = sanitize($this->input->post('info_user_fname'));
		$info_user_mname = sanitize($this->input->post('info_user_mname'));
		$info_user_lname = sanitize($this->input->post('info_user_lname'));
		$info_username = sanitize($this->input->post('info_username'));
		$info_password = sanitize($this->input->post('info_password'));
		$info_re_password = sanitize($this->input->post('info_re_password'));			
		
		if ($info_position == "" || $info_user_fname == "" || $info_user_lname == "" || $info_username == "") {	//Check if required fields are filled up			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {
			if($this->session->userdata('position_id') != ""){ //admin
				$orig_user = $this->model_settings->get_system_user($this->input->post("info_user_id"));
				$row = array(
					'username' => $info_username, 
					'user_fname' => $info_user_fname, 
					'user_mname' => $info_user_mname, 
					'user_lname' => $info_user_lname, 
					'position_id' => $info_position, 
					'date_updated' => date('Y-m-d H:i:s')
				);
				if($info_password == ""){
					if($info_username == $orig_user->result_array()[0]["username"]){
						$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
						$data = array("success" => 1, 'message' => 'Successfully updated');
					}else{
						$isExists = $this->model_settings->get_system_user_unique($info_username);
						if($isExists == 0){
							$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
							$data = array("success" => 1, 'message' => 'Successfully updated');
						}else{
							$data = array('success' => 0, 'message' => 'Username is already taken. Please try again.');
						}
					}
				}else{
					if($info_password == $info_re_password){
						$row["password"] = password_hash($info_password,PASSWORD_BCRYPT); 		
						if($info_username == $orig_user->result_array()[0]["username"]){
							$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
							$data = array("success" => 1, 'message' => 'Successfully updated');
						}else{
							$isExists = $this->model_settings->get_system_user_unique($info_username);
							if($isExists == 0){
								$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
								$data = array("success" => 1, 'message' => 'Successfully updated');
							}else{
								$data = array('success' => 0, 'message' => 'Username is already taken. Please try again.');
							}
						}
					}else{
						$data = array('success' => 0, 'message' => 'Password and Password Confirmation do not match.');
					}
				}
			} else {
				$this->logout();
			}
		}
		generate_json($data);
		
	}
	
	public function delete_system_user() {
		
		$del_user_id = sanitize($this->input->post('del_user_id'));
		
		if ($del_user_id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_system_user($del_user_id);
			
			$data = array("success" => 1, 'message' => "System User Deleted!" , "del_user_id" => $del_user_id);
		}
		
		generate_json($data);
	}
	
	// End - System Users

	public function void_record($token = '') { 
        $this->isLoggedIn();
        
        $credit_id = sanitize($this->input->post('mode_payment'));
        $idno      = sanitize($this->input->post('idno'));
        $rowrec      = sanitize($this->input->post('rowrec'));
        
        $data_admin = array(
            // get data using email
            'token' => $token,
            'get_position' => $this->model_sales->get_position($this->session->userdata('position_id'))->row(),
            'get_users' => $this->model_sales->get_users($this->session->userdata('user_id'))->row()
        );
        
        if ($this->session->userdata('position_id') != '') { // admin
            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_voidrecord', $data_admin);
        }else{
            $this->logout();
        }
        
    }
}