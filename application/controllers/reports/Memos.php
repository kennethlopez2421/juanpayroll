<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Memos extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/memos_model');
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

  public function get_memo_json(){
    $search = json_decode($this->input->post('searchValue'));
    $status = $this->input->post('status');

    $data = $this->memos_model->get_memo_json($search,$status);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
    );

    $this->load->view('includes/header',$data);
    $this->load->view('reports/memos',$data);
  }

  public function create(){
    $this->isLoggedIn();

    $employee_idno = en_dec('dec',$this->input->post('employee_idno'));
    $dept = $this->input->post('dept');
    $re = $this->input->post('re');
    $date = $this->input->post('date');
    $memo_file = "";

    $insert_data = array(
      "employee_idno" => $employee_idno,
      "dept_id" => $dept,
      "re" => $re,
      "date" => $date
    );

    foreach($insert_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }
    }

    ### picture file upload ###
    if(isset($_FILES['memo_file']) && !empty($_FILES['memo_file'])){
      $config['upload_path']       = 'assets/memo_file/';
      $config['allowed_types']     = 'doc|docx|pdf';
      $config['max_size']          = 2048;
      $config['encrypt_name']      = true;

      $this->load->library('upload', $config);

      if(!$this->upload->do_upload('memo_file')){
         $error = array('error' => $this->upload->display_errors());
         $data = array("success" => 0, "message" => $error['error']);
         generate_json($data);
         exit();
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $memo_file = $config['upload_path'].$cdata['upload_data']['file_name'];
      }
    }else{
      $memo_file = "";
    }

    if($memo_file == ""){
      $data = array("success" => 0, "message" => "Unable to upload memorandum file. Please try again.");
      generate_json($data);
      exit();
    }

    $insert_data['memo_file'] = $memo_file;

    $inserted = $this->memos_model->set_memo($insert_data);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Unable to save memorandum. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Memorandum Save Successfully.");
    generate_json($data);


  }

  public function update(){
    $this->isLoggedIn();

    $uid = $this->input->post('uid');
    $encrypt_idno = en_dec('dec', $this->input->post('edit_employee_idno'));
    $edit_employee_idno = $this->input->post('edit_employee_idno');
    $edit_dept = $this->input->post('edit_dept');
    $edit_date = $this->input->post('edit_date');
    $edit_re = $this->input->post('edit_re');
    $edit_memo_file = $this->input->post('edit_memo_file');
    $memo_file = "";

    $update_data = array(
      "employee_idno" => ($encrypt_idno == "") ? $edit_employee_idno : $encrypt_idno,
      "dept_id" => $edit_dept,
      "date" => $edit_date,
      "re" => $edit_re
    );

    foreach($update_data as $row){
      if(empty($row)){
        $data = array("success" => 0, "message" => "Please fill up all required fields.".$edit_employee_idno);
        generate_json($data);
        exit();
      }
    }

    ### picture file upload ###
    if(isset($_FILES['memo_file']) && !empty($_FILES['edit_memo_file'])){
      $config['upload_path']       = 'assets/memo_file/';
      $config['allowed_types']     = 'doc|docx|pdf';
      $config['max_size']          = 2048;
      $config['encrypt_name']      = true;

      $this->load->library('upload', $config);

      if(!$this->upload->do_upload('edit_memo_file')){
         $error = array('error' => $this->upload->display_errors());
         $data = array("success" => 0, "message" => $error['error']);
         generate_json($data);
         exit();
      }else{
        $cdata = array('upload_data' => $this->upload->data());
        $memo_file = $config['upload_path'].$cdata['upload_data']['file_name'];
      }
    }else{
      $memo_file = $edit_memo_file;
    }

    if($memo_file == ""){
      $data = array("success" => 0, "message" => "Unable to upload memorandum file. Please try again.");
      generate_json($data);
      exit();
    }

    $update_file['memo_file'] = $memo_file;
    $updated = $this->memos_model->update_memo($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Updating memorandum unsucessfull. Please double check your update before updating.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Memorandum updated Successfully");
    generate_json($data);

  }

  public function approved(){
    $this->isLoggedIn();

    $uid = $this->input->post('uid');
    if(empty($uid)){
      $data = array("success" => 0, "message" => "Unable to approved this memo. Please try to reload and try again");
      generate_json($data);
      exit();
    }

    $update_data = array('status' => 'approved');
    $updated = $this->memos_model->update_memo_status($update_data,$uid);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to approved this memo. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Memorandum Approved Successfully. ");
    generate_json($data);

  }

  public function search_user(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    if($employee == ""){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item dropdown-emp disabled">No Result Found</a>');
      generate_json($data);
      exit();
    }

    $search = $this->model->search_user($employee);
    if($search->num_rows() == 0){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item dropdown-emp disabled" >No Result Found</a>');
      generate_json($data);
      exit();
    }

    if($search->num_rows() > 0){
      $html = "";
      foreach($search->result_array() as $row){
        $html .= '<a href="#" class="dropdown-item dropdown-emp" data-deptid = "'.$row['deptId'].'" data-emp_idno = "'.en_dec('en',$row['employee_idno']).'">'.$row['fullname'].'</a>';
      }

      $data = array("success" => 1, "message" => $html);
      generate_json($data);
    }
  }

  public function edit_search_user(){
    $this->isLoggedIn();

    $employee = $this->input->post('employee');
    if($employee == ""){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item edit_dropdown-emp disabled">No Result Found</a>');
      generate_json($data);
      exit();
    }

    $search = $this->model->search_user($employee);
    if($search->num_rows() == 0){
      $data = array("success" => 0, "message" => '<a href="#" class="dropdown-item edit_dropdown-emp disabled" >No Result Found</a>');
      generate_json($data);
      exit();
    }

    if($search->num_rows() > 0){
      $html = "";
      foreach($search->result_array() as $row){
        $html .= '<a href="#" class="dropdown-item edit_dropdown-emp" data-deptid = "'.$row['deptId'].'" data-emp_idno = "'.en_dec('en',$row['employee_idno']).'">'.$row['fullname'].'</a>';
      }

      $data = array("success" => 1, "message" => $html);
      generate_json($data);
    }
  }

}
