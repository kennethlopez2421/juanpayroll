<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;

class Timelogreports extends CI_Controller{
  public function __construct() {
		parent::__construct();
		$this->load->model('time_record/timelogreports_model');
    $this->load->library('excel');
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

  public function index($token = ""){
    $data = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'worksite' => $this->model->get_worksite(),
			'timeLogTable' => ""
		);

    $this->load->view('includes/header', $data);
    $this->load->view('settings/timelogreports', $data);
  }

  public function timelogreports_json(){
    $searchValue = $this->input->post('searchValue');
    $data = $this->timelogreports_model->getTimeLogReports_data($searchValue);
		echo json_encode($data);
  }

  public function timelogreports_daterange($date){
    $date = explode('---', $date);
    $date_from = $date[0];
    $date2_to = $date[1];
    // print_r($date);
    // die();

    $data = $this->timelogreports_model->getTimeLogReports_dateRange($date_from, $date2_to);
		echo json_encode($data);
    // generate_json($data);
  }

  public function export_to_excel($token = "", $from = "", $to = "", $type = "", $id = ""){

    $token_dec = $this->session->userdata('token_session');
    $token = en_dec('dec',$token);
    if($token_dec != $token){
      $this->session->sess_destroy();
      $this->load->view('login');
      exit();
    }

    $export_emp_id = $id;
    $export_from_date = $from;
    $export_to_date = $to;
    $export_type = $type;
    $type1 = ucfirst($export_type);
    $type2 = $export_type;

    if($export_from_date == "" || $export_to_date == ""){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }



    $header = "HRIS Timelog ".$export_from_date." - ".$export_to_date;
    $timeLog = $this->timelogreports_model->getTimeLogReports_excel($export_from_date,$export_to_date,$export_emp_id);
    $total_row = $this->timelogreports_model->getTimeLogReports_excel($export_from_date,$export_to_date,$export_emp_id)->num_rows();
    $last_index = $total_row + 3;
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator('HRIS')
        ->setLastModifiedBy('HRIS')
        ->setTitle('Timelog Reports')
        ->setSubject('Timelog Report')
        ->setDescription('Timelog Report');

    $styleArray = array(
        'font' => array('bold' => true,));
    $spreadsheet->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$last_index.':'.'F'.$last_index)->applyFromArray($styleArray);

    foreach(range('A','F') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A1",'Id Number')
        ->setCellValue("B1",'Name')
        ->setCellValue("C1",'Work Site')
        ->setCellValue("D1",'Date')
        ->setCellValue("E1",'Time in')
        ->setCellValue("F1",'Time out');

            // to start from the next line after header we set increment variable to 2
    $x= 2;
    $j = 1;
    foreach($timeLog->result_array() as $sub){
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$x",$sub['employee_idno'])
            ->setCellValue("B$x",$sub['fullname'])
            ->setCellValue("C$x",$sub['worksite'])
            ->setCellValue("D$x",$sub['date'])
            ->setCellValue("E$x",$sub['time_in'])
            ->setCellValue("F$x",$sub['time_out']);
        $x++;
        $j++;
    }

    // $spreadsheet->setActiveSheetIndex(0)->setCellValue("E$last_index","Total Sales");
    // $spreadsheet->setActiveSheetIndex(0)->setCellValue("F$last_index",$total_sales);

    $spreadsheet->getActiveSheet()->setTitle('Users Information');
    $spreadsheet->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$header.'.'.$type2.'"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $type1);
    $writer->save('php://output');
    exit;
  }

  public function import_excel(){
    $this->isLoggedIn();

    if(isset($_FILES['import_excel'])){
      $path = $_FILES['import_excel']['tmp_name'];
      $import_worksite = $this->input->post('import_worksite');
      $lat = $this->input->post('lat');
      $lng = $this->input->post('lng');

      $object = PHPExcel_IOFACTORY::load($path);
      $import_data = array();

      foreach($object->getWorksheetIterator() as $row){
        $highestRow = $row->getHighestRow();
        $highestColumn = $row->getHighestColumn();

        for ($i=2; $i <= $highestRow ; $i++) {
          $id = $row->getCellByColumnAndRow(0, $i)->getValue();
          $date = $row->getCellByColumnAndRow(2, $i)->getValue();
          $time_in = $row->getCellByColumnAndRow(5, $i)->getValue();
          $time_out = $row->getCellByColumnAndRow(6, $i)->getValue();
          $absent = $row->getCellByColumnAndRow(11, $i)->getValue();
          $import_data[] = array(
            'bio_id' => $id,
            'worksite' => $import_worksite,
            'lat' => $lat,
            'lng' => $lng,
            'date' => $date,
            'time_in' => $time_in,
            'time_out' => $time_out,
            'absent' => ($absent != "") ? 'True' : "False"
          );
        }
      }

      $bio_ids = $this->timelogreports_model->get_biometrics_id();
      if($bio_ids->num_rows() == 0){
        $data = array("success" => 0, "message" => "No registered biometrics id. Please try again.");
        generate_json($data);
        exit();
      }

      $emp_w_bio = array();
      $bio_ids = $bio_ids->result_array();
      foreach($import_data as $import){
        foreach($bio_ids  as $bio){
          if($import['bio_id'] == $bio['bio_id'] && $import['absent'] == "False"){
            $import['employee_idno'] = $bio['employee_idno'];
            $emp_w_bio[] = $import;
          }
        }
      }

      if(count((array)$emp_w_bio) == 0){
        $data = array("success" => 0, "message" => "Unable to find any employee that match with the biometrics id. Please try again.");
        generate_json($data);
        exit();
      }

      // print_r($emp_w_bio);
      // die(json_encode($emp_w_bio));
      $insert_batch_data = array();
      foreach($emp_w_bio as $row){
        $d = explode('/',$row['date']);
        $bio_id = $row['bio_id'];
        $emp_id = $row['employee_idno'];
        $worksite = $row['worksite'];
        $date = $d[2]."-".$d[1]."-".$d[0];
        $time_in = $row['time_in'];
        $time_out = $row['time_out'];
        $location = json_encode(array("lat" => $row['lat'], "lng" => $row['lng']));

        $select_data = array($date,$bio_id);
        $selected = $this->timelogreports_model->get_emp_w_bio_timelog($select_data);
        ### UPDATE TIMELOG ###
        if($selected->num_rows() > 0){
          // $update_data = array();
          $last = $selected->num_rows() - 1;
          $timelog = $selected->result_array();
          $time_in_id = $timelog[0]['id'];
          $time_out_id = $timelog[$last]['id'];
          $first_in = "";
          $last_out = "";

          ### TIME IN ###
          if($timelog[0]['time_in'] != ""){
            $time_in_diff = (strtotime($timelog[0]['time_in']) > strtotime($time_in))
              ? time_diff($timelog[0]['time_in'],$time_in) : time_diff($time_in,$timelog[0]['time_in']);

            if($time_in_diff > 5){
              $bio_time = strtotime($time_in);
              $hris_time = strtotime($timelog[0]['time_in']);
              $first_in = ($bio_time > $hris_time) ? $timelog[0]['time_in'] : $time_in;
            }else{
              $first_in = $timelog[0]['time_in'];
            }
          }else{
            $first_in = $time_in;
          }

          ### TIME OUT ###
          if($timelog[$last]['time_out'] != ""){
            $time_out_diff = (strtotime($timelog[$last]['time_out']) > strtotime($time_out))
              ? time_diff($timelog[$last]['time_out'],$time_out) : time_diff($time_out,$timelog[$last]['time_out']);

            if($time_out_diff > 5){
              $bio_time = strtotime($time_out);
              $hris_time = strtotime($timelog[$last]['time_out']);
              $last_out = ($bio_time > $hris_time) ? $time_out : $timelog[$last]['time_out'];
              // $last_out = $time_out;
            }else{
              $last_out = $timelog[$last]['time_out'];
            }
          }else{
            $last_out = $time_out;
          }

          $this->timelogreports_model->update_first_timein(array($first_in,$time_in_id));
          $this->timelogreports_model->update_last_timeout(array($last_out,$time_out_id));
        }
        ### INSERT TIMELOG ###
        if($selected->num_rows() == 0){
          $insert_data = array(
            "employee_idno" => $emp_id,
            "worksite" => $worksite,
            "date" => $date,
            "mode" => "auto",
            "img_url" => "assets\img\avatar.jpg",
            "current_location" => $location,
            "time_in" => $time_in,
            "time_out" => $time_out,
            "date_created" => todaytime()
          );

          $insert_batch_data[] = $insert_data;
        }
      }

      if(count((array)$insert_batch_data) > 0){
        $inserted = $this->timelogreports_model->set_import_data($insert_batch_data);
        if($inserted === false){
          $data = array("success" => 0, "message" => "Failed to import some of the new Data.");
          generate_json($data);
          exit();
        }
      }

      $data = array("success" => 1, "message" => "Data Successfully Imported.");
      generate_json($data);
      exit();


    }else {
      $data = array("success" => 0, "message" => "Nothing to import. Please try again.");
      generate_json($data);
      exit();
    }
  }

  public function update(){
    $this->isLoggedIn();

    $uid = en_dec('dec',$this->input->post('uid'));
    $emp_id = en_dec('dec',$this->input->post('emp_id'));
    $status = $this->input->post('status');
    $time_in = $this->input->post('time_in');
    $current_timein = $this->input->post('current_timein');
    $time_out = $this->input->post('time_out');
    $current_timeout = $this->input->post('current_timeout');
    $date = $this->input->post('date');
    $logs = array();

    if($time_in != $current_timein){
      $time_in = ($time_in == "") ? "--:-- --" : $time_in;
      $current_timein = ($current_timein == "") ? "--:-- --" : $current_timein;
      $logs[] = "Change time in from".$current_timein." to ".$time_in;
    }

    if($time_out != $current_timeout){
      $time_out = ($time_out == "") ? "--:-- --" : $time_out;
      $current_timeout = ($current_timeout == "") ? "--:-- --" : $current_timeout;
      $logs[] = "Change time out from ".$current_timeout." to ".$time_out;
    }


    ### TIMELOG ###
    if($status == "timelog"){
      $update_data = array(
        "time_in" => ($time_in == "") ? null : $time_in,
        "time_out" => ($time_out == "") ? null : $time_out,
        "date_created" => todaytime()
      );

      $updated = $this->timelogreports_model->update_timerecord($uid,$update_data);
      if($updated === false){
        $data = array("success" => 0, "message" => "Unable to update timelog. Please try again.");
        generate_json($data);
        exit();
      }
    }
    ### WORK ORDER ###
    if($status == "workorder"){
      $update_data = array(
        "start_time" => $time_in,
        "end_time" => $time_out,
        "created_at" => todaytime()
      );

      $updated = $this->timelogreports_model->update_workorder_time($uid,$update_data);
      if($updated === false){
        $data = array("success" => 0, "message" => "Unable to update timelog. Please try again.".$uid);
        generate_json($data);
        exit();
      }
    }

    $log_data = array(
      "employee_idno" => $emp_id,
      "admin_id" => $this->session->userdata('emp_idno'),
      "logs" => implode(',',$logs),
      "date" => $date,
      "status" => "update",
      "type" => $status,
      "created_at" => todaytime()
    );

    $inserted = $this->timelogreports_model->set_timelog_logs($log_data);

    $data = array("success" => 1, "message" => "Timelog Updated Successfully!");
    generate_json($data);
  }

  public function delete(){
    $delid = en_dec('dec',$this->input->post('delid'));
    $del_status = $this->input->post('del_status');
    $del_emp_id = $this->input->post('del_emp_id');
    $del_date = $this->input->post('del_date');

    if($delid == "" || $del_status == ""){
      $data = array("success" => 0, "message" => "Oops!. Something went wrong. Please try again.");
      generate_json($data);
      exit();
    }

    if($del_status == "timelog"){
      $deleted = $this->timelogreports_model->update_timerecord_status($delid);
      if($deleted === false){
        $data = array("success" => 0, "message" => "Unable to delete timelog . Please try again.");
        generate_json($data);
        exit();
      }
    }

    if($del_status == "workorder"){
      $deleted = $this->timelogreports_model->update_workorder_status($delid);
      if($deleted === false){
        $data = array("success" => 0, "message" => "Unable to delete timelog . Please try again.");
        generate_json($data);
        exit();
      }
    }

    $log_data = array(
      "employee_idno" => $del_emp_id,
      "admin_id" => $this->session->userdata('emp_idno'),
      "logs" => "Deleted timelog",
      "date" => $del_date,
      "status" => "delete",
      "type" => $del_status,
      "created_at" => todaytime()
    );

    $inserted = $this->timelogreports_model->set_timelog_logs($log_data);
    $data = array("success" => 1, "message" => "Timelog Successfully Deleted. ");
    generate_json($data);
  }

}
