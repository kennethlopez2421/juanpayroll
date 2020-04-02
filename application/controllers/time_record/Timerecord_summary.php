<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;

class Timerecord_summary extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('time_record/timerecord_summary_model');
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

  public function get_timerecord_summary_json(){
    $search = json_decode($this->input->post('searchValue'));
    // DEFAULT RECORD TODAY
    if($search->from == "" && $search->to == ""){
      $data = array(
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => array(),
        "success" => 0
      );
      echo json_encode($data);
      exit();
    }

    $data = $this->timerecord_summary_model->get_timerecord_summary_json($search);
    $this->session->set_userdata('trs_data', $data['data']);
    $data['success'] = (isset($this->session->trs_data) && count((array)$this->session->trs_data) > 0) ? 1 : 0;
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    if(isset($this->session->trs_data) && count((array)$this->session->trs_data) > 0){
      $this->session->unset_userdata('trs_data');
    }
    $this->load->view('includes/header',$data);
    $this->load->view('time_record/timerecord_summary',$data);
  }

  public function create(){
    $this->isLoggedIn();
    $save_data = json_decode($this->input->post('save_data'));
    if(isset($this->session->trs_data) && count((array)$this->session->trs_data)){
      $trs_data = $this->session->trs_data;
      $trs_record = $this->timerecord_summary_model->get_timerecord_summary($save_data);

      $insert_data = array();
      $trs_arr = $trs_record->result_array();
      if($trs_record->num_rows() > 0){

        for ($i=0; $i < count($trs_arr); $i++) {
          for ($x=0; $x < count($trs_data); $x++) {
            if($trs_arr[$i]['date_created'] == $trs_data[$x][2] && $trs_arr[$i]['employee_idno'] == $trs_data[$x][3]){
              unset($trs_data[$x]);
            }
          }
          $trs_data = array_values($trs_data);
        }

        foreach($trs_data as $arr2){
          $remarks = 1;
          $remarks = ($row[12] == "Leave") ? 4 : $remarks;
          $remarks = ($row[12] == "Work Order") ? 2 : $remarks;
          $remarks = ($row[12] == "Offset Wholeday") ? 5 : $remarks;
          $remarks = ($row[12] == "Offset Halfday") ? 6 : $remarks;
          $insert = array(
            "time_in" => ($arr2[0] == '--:--') ? '00:00' : $arr2[0],
            "time_out" => ($arr2[1] == '--:--') ? '00:00' : $arr2[1],
            "date_created" => $arr2[2],
            "employee_idno" => $arr2[3],
            "man_hours" => $arr2[5],
            "night_diff" => $arr2[6],
            "late" => $arr2[7],
            "overbreak" => $arr2[8],
            "undertime" => $arr2[9],
            "absent" => $arr2[10],
            "total_minutes" => $arr2[11],
            "remarks" => $remarks
          );

          $insert_data[] = $insert;
        }

      }else{
        foreach($trs_data as $row){
          $remarks = 1;
          $remarks = ($row[12] == "Leave") ? 4 : $remarks;
          $remarks = ($row[12] == "Work Order") ? 2 : $remarks;
          $remarks = ($row[12] == "Offset Wholeday") ? 5 : $remarks;
          $remarks = ($row[12] == "Offset Halfday") ? 6 : $remarks;
          $insert = array(
            "time_in" => ($row[0] == '--:--') ? '00:00' : $row[0],
            "time_out" => ($row[1] == '--:--') ? '00:00' : $row[1],
            "date_created" => $row[2],
            "employee_idno" => $row[3],
            "man_hours" => $row[5],
            "night_diff" => $row[6],
            "late" => $row[7],
            "overbreak" => $row[8],
            "undertime" => $row[9],
            "absent" => $row[10],
            "total_minutes" => $row[11],
            "remarks" => $remarks
          );

          $insert_data[] = $insert;
        }
      }

      if(count($insert_data) == 0){
        $data = array("success" => 0, "message" => "Oops! It looks like you already save all the Time Record data inside this date range.");
        generate_json($data);
        exit();
      }

      $inserted = $this->timerecord_summary_model->set_timerecord_summary_batch($insert_data);
      if($inserted === false){
        $data = array("success" => 0, "message" => "Unable to save Time Record Summary. Please try again.");
        generate_json($data);
        exit();
      }

      $data = array("success" => 1, "message" => "Time Record Summary Save Successfully.");
      generate_json($data);
    }
  }

  public function reset(){
    $this->isLoggedIn();
    $truncated = $this->timerecord_summary_model->truncate();
    if($truncated === false){
      $data = array("success" => 0, "message" => "Unable to reset timerecord summary. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Timerecord summary reset Successfully");
    generate_json($data);

  }
}
