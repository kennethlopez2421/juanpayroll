<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_profile extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('profile/Edit_profile_model');
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
	public function index($token = "") {

		$this->isLoggedIn();
		$employee_idno = $this->session->userdata('emp_idno');


		$get_user_details = $this->Edit_profile_model->get_user_details($employee_idno)->row();
		$get_department = $this->Edit_profile_model->get_department($employee_idno)->row();
		$get_worksite = $this->Edit_profile_model->get_worksite($employee_idno)->row();
		//check null data

		if($get_user_details != null){
			if($get_user_details->employee_idno != null){
				$employee_idno = $get_user_details->employee_idno;
			}else{
				$employee_idno = "No Employee Data";
			}
			if($get_user_details->first_name != null){
				$first_name = $get_user_details->first_name;
			}else{
				$first_name = "Empty";
			}
			if($get_user_details->middle_name != null){
				$middle_name = $get_user_details->middle_name;
			}else{
				$middle_name = "";
			}
			if($get_user_details->last_name != null){
				$last_name = $get_user_details->last_name;
			}else{
				$last_name = "Empty";
			}
			if($get_user_details->contact_no != null){
				$contact_no = $get_user_details->contact_no;
			}else{
				$contact_no = "Empty";
			}
			if($get_user_details->email != null){
				$email = $get_user_details->email;
			}else{
				$email = "Empty";
			}
			if($get_user_details->home_address1 != null){
				$home_address1 = $get_user_details->home_address1;
			}else{
				$home_address1 = "Empty";
			}
			if($get_user_details->home_address2 != null){
				$home_address2 = $get_user_details->home_address2;
			}else{
				$home_address2 = "Empty";
			}
			if($get_user_details->country != null){
				$country = $get_user_details->country;
			}else{
				$country = "Empty";
			}
			if($get_user_details->gender != null){
				$gender = $get_user_details->gender;
			}else{
				$gender = "Empty";
			}
			if($get_user_details->birthday != null){
				$birthday = $get_user_details->birthday;
			}else{
				$birthday = "Empty";
			}
			if($get_user_details->marital_status != null){
				$marital_status =$get_user_details->marital_status;
			}else{
				$marital_status = "Empty";
			}
		}else{
				$employee_idno = "No Employee Data";
				$first_name = "Empty";
				$middle_name = "";
				$last_name = "Empty";
				$contact_no = "Empty";
				$email = "Empty";
				$home_address1 = "Empty";
				$home_address2 = "Empty";
				$country = "Empty";
				$gender = "Empty";
				$birthday = "Empty";
				$marital_status = "Empty";

		}

		if($get_department != null){
			if($get_department->description != null){
				$department = $get_department->description;
			}else{
				$department = "Admin";
			}
		}else{
			$department = "Admin";
		}
		if($get_worksite != null){
			if($get_worksite->description != null){
				$worksite = $get_worksite->description;
			}else{
				$worksite = "No Headquarters";
			}
			
		}else{
			$worksite = "No Headquarters";
		}

		$emp_data = array(
			"first_name" => $first_name,
			"middle_name" => $middle_name,
			"last_name" => $last_name,
			"gender" => $gender,
			"birthday" => $birthday,
			"contact_no" => $contact_no,
			"email" => $email,
			"home_address1" => $home_address1,
			"home_address2" => $home_address2,
			"country" => $country,
			"employee_idno" => $employee_idno,
			"department" => $department,
			"worksite" => $worksite,
			"marital_status" => $marital_status
		);
		// $gw = $this->Edit_profile_model->get_worksite($employee_idno);
		// $gp = $this->Edit_profile_model->get_position($employee_idno);

		$pic_dir_jpg = "./assets/employee_photos/".$employee_idno."_active.jpg";
		$pic_dir_jpeg = "./assets/employee_photos/".$employee_idno."_active.jpeg";
		$pic_dir_png = "./assets/employee_photos/".$employee_idno."_active.png";
		if(file_exists($pic_dir_jpg)) {
			$pic_name = $employee_idno."_active.jpg";
		}else if(file_exists($pic_dir_jpeg)){
			$pic_name = $employee_idno."_active.jpeg";
		}else if(file_exists($pic_dir_png)){
			$pic_name = $employee_idno."_active.png";
		}else{
			$pic_name = "default.png";
		}

		$data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'user_details' => $emp_data,
			'picture_extension' => $pic_name
			// 'getposition' => $getposition,
			// 'getworksite' => $getworksite
		);

		$this->load->view('includes/header', $data);
		$this->load->view('profile/edit_profile', $data);

	}
	// public function do_upload(){
	// 	$dateIn = date('Y-m-d');
	// 	$try = $this->input->post('picture');

 //        $config['upload_path']="./assets/attendance";
 //        $config['allowed_types']='gif|jpg|png';
 //        $config['encrypt_name'] = TRUE;

 //        $this->load->library('upload', $config);
 //        $this->upload->initialize($config);
	// 	if(!is_dir("./assets/employee_photos/". $dateIn ."/")) {
	// 					mkdir("./assets/employee_photos/". $dateIn ."/");
	// 				}
 //        if ( ! $this->upload->do_upload('picture'))
 //            {
 //            	print_r('pasok');
 //                $error = array('error' => $this->upload->display_errors());
 //            }
 //        else
 //            {
 //            	$this->upload->data();
 //            	// print_r('pasok');
 //             //  	$data = array('upload_data' => $this->upload->data());
 //            }
 //    }
	public function get_valid_ids() {
		$this->isLoggedIn();
		$employee_idno = $this->input->get('employee_idno');
		$draw = $this->input->get('draw');
		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = $this->input->get('search')['value'];
		$order = $this->input->get('order');
		$column = array('id', 'valid_id_type', 'id_number', 'id_value', 'upload_date','picture_extension');
		$orderColumn = isset($order[0]['column']) ? $column[$order[0]['column']] : 'id';
        $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $ordrBy = $orderColumn . " " . $orderDirection;

		$data = array(
			// "draw" => $draw,
			// "recordsTotal" => $this->Edit_profile_model->get_valid_ids_tbl(null,null,null,null)->num_rows(),
			// "recordsFiltered" => $this->Edit_profile_model->get_valid_ids_tbl(null,null,null,null)->num_rows(),
			"data" => $this->Edit_profile_model->get_valid_ids_tbl($start,$length,$search,$ordrBy,$employee_idno)->result()
		);

		echo json_encode($data);


	}
	public function Add_new_id(){
		$this->isLoggedIn();
		$valid_id_type = $this->input->post('valid_id_type');
		$id_number = $this->input->post('id_number');
		$id_value = $this->input->post('id_value');
		$current_date = today();
		$id_number_concat = str_replace(' ','_',$id_number);
		$valid_id_type_concat = str_replace(' ','_',$valid_id_type);
		$employee_idno = $this->session->userdata('emp_idno');


		if(isset($_FILES['add_image_file']) && $_FILES['add_image_file']['size'] > 0){  
			// print_r($ext);
			// die();

			if($valid_id_type != null && $id_number != null && $id_value != null){
				//insert of picture
				$employee_idno = $this->session->userdata('emp_idno');
		      	$pic_dir_jpg = "./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat."_active.jpg";
				$pic_dir_jpeg = "./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat."_active.jpeg";
				$pic_dir_png = "./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat."_active.png";
				// $file_name = $current_date."_".$employee_idno."_".$valid_id_type."_".$id_number_concat."_active";

		            $config['upload_path'] = './assets/employee_ids';  
		            $config['allowed_types'] = 'jpg|jpeg|png'; 
		            $config['overwrite'] = true; 
		            $file_name = $config['file_name'] = $current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.'_active';

		            //get file extension
					$name = $_FILES["add_image_file"]["name"];
					$x = explode(".", $name);
					$ext = end($x);
		            $with_space = $file_name.".".$ext; 
		            $file_name = str_replace(' ','_',$with_space);

					if(file_exists($pic_dir_jpg)) {
						rename($pic_dir_jpg,"./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.".jpg");
					}else if(file_exists($pic_dir_jpeg)){
						rename($pic_dir_jpeg,"./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.".jpeg");
					}else if(file_exists($pic_dir_png)){
						rename($pic_dir_png,"./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.".png");
					}
		            $this->load->library('upload', $config);  
		            if(!$this->upload->do_upload('add_image_file'))  
		            {  
		                 echo $this->upload->display_errors();  
		            }  
		            else  
		            {  
		                 $this->upload->data();  
		            }  
		       //end of insert of picture

		       //insert of details
		       $insertdata = $this->Edit_profile_model->insert_data($employee_idno,$valid_id_type,$id_number,$id_value,$current_date,$file_name);
		       $data = array('success' => 1, "output" => "ID Successfully Added");
	       	}else{
	       		$data = array('success' => 0, "output" => "Please fill up all details");
	       	}
       }else{
       		$data = array('success' => 0, 'output' => "Please Upload Your ID Image");
       }

      echo json_encode($data);


	}
	public function Edit_id(){
		$this->isLoggedIn();
		$valid_id_id = $this->input->post('edit_valid_id_id');
		$valid_id_type = $this->input->post('edit_valid_id_type');
		$id_number = $this->input->post('edit_id_number');
		$id_value = $this->input->post('edit_id_value');
		$current_date = today();
		$id_number_concat = str_replace(' ','_',$id_number);
		$valid_id_type_concat = str_replace(' ','_',$valid_id_type);
		$employee_idno = $this->session->userdata('emp_idno');

		if($valid_id_type != null && $id_number != null && $id_value != null){
			if(isset($_FILES['edit_image_file']) && $_FILES['edit_image_file']['size'] > 0){
				//insert of picture
			$employee_idno = $this->session->userdata('emp_idno');
	      	$pic_dir_jpg = "./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat."_active.jpg";
			$pic_dir_jpeg = "./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat."_active.jpeg";
			$pic_dir_png = "./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat."_active.png";
			// $file_name = $current_date."_".$employee_idno."_".$valid_id_type."_".$id_number_concat."_active";

	            $config['upload_path'] = './assets/employee_ids';  
	            $config['allowed_types'] = 'jpg|jpeg|png'; 
	            $config['overwrite'] = true; 
	            $file_name = $config['file_name'] = $current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.'_active';

	            //get file extension
				$name = $_FILES["edit_image_file"]["name"];
				$x = explode(".", $name);
				$ext = end($x);
	            $with_space = $file_name.".".$ext; 
	            $file_name = str_replace(' ','_',$with_space);

				if(file_exists($pic_dir_jpg)) {
					rename($pic_dir_jpg,"./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.".jpg");
				}else if(file_exists($pic_dir_jpeg)){
					rename($pic_dir_jpeg,"./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.".jpeg");
				}else if(file_exists($pic_dir_png)){
					rename($pic_dir_png,"./assets/employee_ids/".$current_date."_".$employee_idno."_".$valid_id_type_concat."_".$id_number_concat.".png");
				}
	            $this->load->library('upload', $config);  
	            if(!$this->upload->do_upload('edit_image_file'))  
	            {  
	                 echo $this->upload->display_errors();  
	            }  
	            else  
	            {  
	                 $this->upload->data();  
	            }  
		       //end of insert of picture	
		       	$update_data = $this->Edit_profile_model->edit_data_withpic($valid_id_id,$valid_id_type,$id_number,$id_value,$file_name);
				$data = array('success' => 1, 'output' => "Data successfully edited");			
			}else{
				$update_data = $this->Edit_profile_model->edit_data($valid_id_id,$valid_id_type,$id_number,$id_value);
				$data = array('success' => 1, 'output' => "Data successfully edited");
			}

		}else{
			$data = array('success' => 0, 'output' => "Please fill up all fields");
		}


      echo json_encode($data);

	}
	public function Temp_save_details(){
		// print_r($this->input->post());
		$employee_idno = $this->session->userdata('emp_idno');
		$first_name = $this->input->post('first_name');
		$middle_name = $this->input->post('middle_name');
		$last_name = $this->input->post('last_name');
		$contact_no = $this->input->post('contact_number');
		$marital_status = $this->input->post('marital_status');
		$gender =  $this->input->post('birthdate');
		$email = $this->input->post('email');
		$birthdate = $this->input->post('birthdate');
		$address1 = $this->input->post('address1');
		$address2 = $this->input->post('address2');
		$country = $this->input->post('country');
		$date_updated = todaytime();
		
		if($first_name != null || $middle_name != null || $last_name != null || $gender != null || $marital_status != null || $contact_no != null || $email != null || $birthdate != null || $address1 != null || $address2 != null || $country != null){


			$update_data = $this->Edit_profile_model->insert_temp_emp_details($employee_idno, $first_name, $middle_name, $last_name, $gender, $marital_status, $contact_no, $email, $birthdate, $address1, $address2, $country, $date_updated);
			$data = array('success' => 1, 'output' => "Data Saved. Please wait for admin approval");
		}else{
			$data = array('success' => 0, 'output' => "Please fill up all required fields");
		}

		echo json_encode($data);

	}
	public function Delete_id(){
		$this->isLoggedIn();
		$valid_id_id = $this->input->post('id');

		if($valid_id_id != null){
			$delete = $this->Edit_profile_model->delete_data($valid_id_id);
			$data = array('success' => 1, 'output' => "Data successfully deleted");
		}else{
			$data = array('success' => 0, 'output' => "Failed to delete data");
		}

		echo json_encode($data);
	}
	public function employee_valid_ids(){
		$this->isLoggedIn();
		$employee_idno = $this->input->post('employee_idno');
		$g_v_i = $this->Edit_profile_model->get_employee_ids($employee_idno);
		$ids_num = $g_v_i->num_rows();
		if($ids_num > 0){
			$get_valid_ids = $g_v_i->result();
			$data = array('success' => 1, 'output' =>$get_valid_ids);
		}else{
			$get_valid_ids = "Empty";
			$data = array('success' => 0, 'output' => "No Data Selected");
		}

		echo json_encode($data);
	}
      public function ajax_upload(){  
      	$this->isLoggedIn();
      	$employee_idno = $this->session->userdata('emp_idno');
      	$pic_dir_jpg = "./assets/employee_photos/".$employee_idno."_active.jpg";
		$pic_dir_jpeg = "./assets/employee_photos/".$employee_idno."_active.jpeg";
		$pic_dir_png = "./assets/employee_photos/".$employee_idno."_active.png";
           if(isset($_FILES["image_file"]))  
           {  
                $config['upload_path'] = './assets/employee_photos';  
                $config['allowed_types'] = 'jpg|jpeg|png';  
                $config['overwrite'] = true;
                $config['file_name'] = $employee_idno.'_active';
				if(file_exists($pic_dir_jpg)) {
					rename($pic_dir_jpg,"./assets/employee_photos/".$employee_idno.".jpg");
				}else if(file_exists($pic_dir_jpeg)){
					rename($pic_dir_jpeg,"./assets/employee_photos/".$employee_idno.".jpeg");
				}else if(file_exists($pic_dir_png)){
					rename($pic_dir_png,"./assets/employee_photos/".$employee_idno.".png");
				}
                $this->load->library('upload', $config);  
                if(!$this->upload->do_upload('image_file'))  
                {  
                     echo $this->upload->display_errors();  
                }  
                else  
                {  
                     $this->upload->data();  
                }  
           }  
      }  


}
