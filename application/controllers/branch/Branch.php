<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Branch extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('branch/branch_model');
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

  public function get_branch_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->branch_model->get_branch_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('transactions/workOrder',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $cpassword = $this->input->post('cpassword');
    $fname = $this->input->post('fname');
    $mname = $this->input->post('mname');
    $lname = $this->input->post('lname');
    $branch_name = $this->input->post('branch_name');
    $branch_code = $this->input->post('branch_code');
    $db_name = $this->input->post('db_name');
    $timezone = $this->input->post('timezone');
    $country_code = $this->input->post('country_code');
    $loc_status = $this->input->post('loc_status');

    $required = array($username, $password, $cpassword, $fname, $lname, $branch_name, $branch_code, $db_name, $timezone, $country_code, $loc_status);
    for ($i=0; $i < count((array)$required); $i++) {
      if(empty($required[$i])){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }
    }

    if($password != $cpassword){
      $data = array("success" => 0, "message" => "Password and Confirm Password do not match. Please try again.");
      generate_json($data);
      exit();
    }

    $isBranchNameExist = $this->branch_model->get_hris_branch($branch_name, 'branch_name');
    if($isBranchNameExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Branch name already exist. Please try another name.");
      generate_json($data);
      exit();
    }

    $isBranchCodeExist = $this->branch_model->get_hris_branch($branch_code, 'branch_code');
    if($isBranchCodeExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Branch Code already exist. Please try another.");
      generate_json($data);
      exit();
    }

    $isUsernameExist = $this->branch_model->get_hris_branch($username, 'username');
    if($isUsernameExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Username already use by another user. Please try again.");
      generate_json($data);
      exit();
    }

    $isDbExist = $this->branch_model->get_hris_branch($db_name, 'database_name');
    if($isDbExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Database Name already exist. Please try another.");
      generate_json($data);
      exit();
    }

    $created = $this->branch_model->create_database($db_name);
    if($created == false){
      $data = array("success" => 0, "message" => "Unable to Create Database. Please try again.");
      generate_json($data);
      exit();
    }

    $populated = $this->branch_model->populate_database($db_name);
    if($populated == false){
      $data = array("success" => 0, "message" => "Unable to populate database.");
      generate_json($data);
      exit();
    }

    $options = ['cost' => 12];
    $hash_password = password_hash($password, PASSWORD_BCRYPT, $options);

    $insert_data = array(
      "branch_name" => $branch_name,
      "branch_code" => $branch_code,
      "username" => $username,
      "password" => $hash_password,
      "fname" => $fname,
      "mname" => $mname,
      "lname" => $lname,
      "timezone" => $timezone,
      "location" => $loc_status,
      "database_name" => $db_name,
      "country_code" => $country_code
    );
    $inserted = $this->branch_model->set_hris_branch($insert_data);
    if($inserted == false){
      $data = array("success" => 0, "message" => "Unable to save HRIS Branch Information.");
      generate_json($data);
      exit();
    }

    $branch_admin = array(
      "username" => $username,
      "password" => $hash_password,
      "user_fname" => $fname,
      "user_mname" => $mname,
      "user_lname" => $lname,
      "position_id" => 2,
      "employee_idno" => 2,
      "deptId" => 0,
      "subDeptId" => 0,
      "date_activated" => today(),
      "date_created" => today(),
    );
    $insert_branch_admin = $this->branch_model->set_branch_admin($branch_admin,$db_name);
    if($insert_branch_admin == false){
      $data = array("success" => 0, "message" => "Unable to set HRIS ".$branch_name." Administrator");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "HRIS ".$branch_name." Created Successfully");
    generate_json($data);

  }

  public function update(){
    // ADMIN INFO
    $uid = en_dec('dec',$this->input->post('uid'));
    $edit_username = $this->input->post('edit_username');
    $edit_password = $this->input->post('edit_password');
    $curr_password = $this->input->post('curr_password');
    $edit_fname = $this->input->post('edit_fname');
    $edit_lname = $this->input->post('edit_lname');
    $edit_mname = $this->input->post('edit_mname');
    // BRANCH INFO
    $edit_branch_name = $this->input->post('edit_branch_name');
    $edit_branch_code = $this->input->post('edit_branch_code');
    $edit_dbname = $this->input->post('edit_dbname');
    $edit_timezone = $this->input->post('edit_timezone');
    $edit_country_code = $this->input->post('edit_country_code');
    $edit_loc_status = $this->input->post('edit_loc_status');

    $required = array($uid, $edit_username, $edit_password, $edit_fname, $edit_lname, $edit_branch_name, $edit_branch_code, $edit_dbname, $edit_timezone, $edit_country_code,$edit_loc_status);
    for ($i=0; $i < count((array)$required); $i++) {
      if(empty($required[$i])){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }
    }

    $isBranchNameExist = $this->branch_model->get_hris_branch($edit_branch_name, 'branch_name',$uid);
    if($isBranchNameExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Branch name already exist. Please try another name.");
      generate_json($data);
      exit();
    }

    $isBranchCodeExist = $this->branch_model->get_hris_branch($edit_branch_code, 'branch_code',$uid);
    if($isBranchCodeExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Branch Code already exist. Please try another.");
      generate_json($data);
      exit();
    }

    $isUsernameExist = $this->branch_model->get_hris_branch($edit_username, 'username',$uid);
    if($isUsernameExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Username already use by another user. Please try again.");
      generate_json($data);
      exit();
    }

    $isDbExist = $this->branch_model->get_hris_branch($edit_dbname, 'database_name', $uid);
    if($isDbExist->num_rows() > 0){
      $data = array("success" => 0, "message" => "Database Name already exist. Please try another.");
      generate_json($data);
      exit();
    }

    $password = $edit_password;

    if($curr_password != $password){
      $option = ['cost' => 12];
      $hash_password = password_hash($password,PASSWORD_BCRYPT,$option);
      $password = $hash_password;
    }

    $update_data = array(
      "branch_name" => $edit_branch_name,
      "branch_code" => $edit_branch_code,
      "username" => $edit_username,
      "password" => $password,
      "fname" => $edit_fname,
      "mname" => $edit_mname,
      "lname" => $edit_lname,
      "timezone" => $edit_timezone,
      "location" => $edit_loc_status,
      "database_name" => $edit_dbname,
      "country_code" => $edit_country_code
    );

    $updated = $this->branch_model->update_hris_branch($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to update HRIS Branch . Please try again.");
      generate_json($data);
      exit();
    }

    $branch_admin_update = array(
      "username" => $edit_username,
      "password" => $password,
      "user_fname" => $edit_fname,
      "user_mname" => $edit_mname,
      "user_lname" => $edit_lname
    );

    $branch_admin_updated = $this->branch_model->update_hris_branch_admin($branch_admin_update,$uid,$edit_dbname);
    if($branch_admin_updated === false){
      $data = array("success" => 0, "message" => "Unable to update account admin of HRIS Branch ".$edit_branch_name);
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "HRIS Branch updated Successfully");
    generate_json($data);

  }

  public function delete(){
    $delid = en_dec('dec',$this->input->post('delid'));
    if(empty($delid)){
      $data = array("success" => 0, "message" => "Something went wrong in deactivating HRIS Branch. Please try again.");
      generate_json($data);
      exit();
    }

    $deactivated = $this->branch_model->update_hris_branch_status($delid);
    if($deactivated === false){
      $data = array("success" => 0, "message" => "Something went wrong in deactivating HRIS Branch. Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "HRIS Branch Successfully deactivated.");
    generate_json($data);
  }

  public function activate(){
    $activate_id = en_dec('dec',$this->input->post('activate_id'));
    if(empty($activate_id)){
      $data = array("success" => 0, "message" => "Something went wrong in activating HRIS Branch. Try to reload ang try again.");
      generate_json($data);
      exit();
    }

    $activated = $this->branch_model->update_hris_branch_status($activate_id, 1);
    if($activated === false){
      $data = array("success" => 0, "message" => "Something went wrong in activating HRIS Branch. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "HRIS Branch Activated Successfully");
    generate_json($data);
  }
}
