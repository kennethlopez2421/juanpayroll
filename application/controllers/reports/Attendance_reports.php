<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;

class Attendance_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/attendance_reports_model');
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

  public function get_attendance_reports_json(){
    $search1 = json_decode($this->input->post('searchValue'));
    $search2 = json_decode($this->input->post('searchValue2'));

    ### type ###
    switch ($search1->filter) {
      case 'divAbsent':
        $data = $this->attendance_reports_model->get_attendance_reports_absent($search1,$search2);
        break;
      case 'divLate':
        $data = $this->attendance_reports_model->get_attendance_reports_late($search1,$search2);
        break;
      case 'divOvertime':
        $data = $this->attendance_reports_model->get_attendance_reports_overtime($search1,$search2);
        break;
      case 'divUndertime':
        $data = $this->attendance_reports_model->get_attendance_reports_undertime($search1,$search2);
        break;
      case 'divMostAbsent':
        $data = $this->attendance_reports_model->get_attendance_reports_most_absent($search1,$search2);
        break;
      case 'divMostLate':
        $data = $this->attendance_reports_model->get_attendance_reports_most_late($search1,$search2);
        break;
      case 'divMostOvertime':
        $data = $this->attendance_reports_model->get_attendance_reports_most_overtime($search1,$search2);
        break;
      case 'divMostUndertime':
        $data = $this->attendance_reports_model->get_attendance_reports_most_undertime($search1,$search2);
        break;
      case 'divHalfday':
        $data = $this->attendance_reports_model->get_attendance_reports_halfday($search1,$search2);
        break;
      case 'divOffDay':
        $data = $this->attendance_reports_model->get_offday_reports($search1,$search2);
        break;
      default:
        $data = $this->attendance_reports_model->get_attendance_reports_absent($search1,$search2);
        break;
    }
    echo json_encode($data);
  }

  public function offday_breakdown(){
    $searchValue = json_decode($this->input->post('searchValue'));
    $data = $this->attendance_reports_model->get_offday_reports_breakdown($searchValue);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
      'positions' => $this->model->get_user_position()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('reports/attendance_reports',$data);
  }

  public function export_to_excel($token = "", $filter = "", $filter2 = "", $search = "", $from = "", $to_date = "", $search2 = ""){
    $token_dec = $this->session->userdata('token_session');
    $token = en_dec('dec',$token);
    if($token_dec != $token){
      $this->session->sess_destroy();
      $this->load->view('login');
      exit();
    }

    $title = "";
    switch ($filter) {
      case 'divAbsent':
        $title = "Absent from ".$from;
        break;
      case 'divLate':
        $title = "Late from ".$from;
        break;
      case 'divOvertime':
        $title = "Overtime from ".$from;
        break;
      case 'divUndertime':
        $title = "Undertime from ".$from;
        break;
      case 'divHalfday':
        $title = "Halfday from ".$from." to ".$to_date;
        break;
      case 'divMostAbsent':
        $title = "Most Absent from ".$from." to ".$to_date;
        break;
      case 'divMostLate':
        $title = "Most Late from ".$from." to ".$to_date;
        break;
      case 'divMostOvertime':
        $title = "Most Overtime from ".$from." to ".$to_date;
        break;
      case 'divMostUndertime':
        $title = "Most Undertime from ".$from." to ".$to_date;
        break;
      default:
        // code...
        break;
    }

    $search1 = array(
      "filter" => $filter,
      "search" => $search,
      "from" => $from,
      "to" => $to_date
    );

    $search2 = array(
      "filter" => $filter2,
      "search" => $search2
    );

    // print_r($search1);
    // print_r($search2);
    // die();

    $search1 = (object)$search1;
    $search2 = (object)$search2;

    $header = $title;
    $type2 = "xlsx";
    $type1 = ucfirst($type2);

    $reports = $this->attendance_reports_model->get_excel_reports($search1,$search2);
    if(count((array)$reports) == 0){
      $data = array("success" => 0, "message" => "No results available.");
      generate_json($data);
      exit();
    }

    $last_index = $reports['recordsTotal'];
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator('HRIS')
        ->setLastModifiedBy('HRIS')
        ->setTitle('Timelog Reports')
        ->setSubject('Timelog Report')
        ->setDescription('Timelog Report');

    $styleArray = array(
        'font' => array('bold' => true,));
    $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A3:G3')->applyFromArray($styleArray);
    // $spreadsheet->getActiveSheet()->getStyle('E'.$last_index.':'.'F'.$last_index)->applyFromArray($styleArray);

    foreach(range('A','G') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A1",$title)
        ->setCellValue("A3",'Employee ID')
        ->setCellValue("B3",'Employee Name')
        ->setCellValue("C3",'Department')
        ->setCellValue("D3",'Position')
        ->setCellValue("E3",'Absent')
        ->setCellValue("F3",'Minutes')
        ->setCellValue("G3",'Status');

            // to start from the next line after header we set increment variable to 2
    $x= 4;
    $j = 1;
    for ($a=0; $a < $reports['recordsTotal']; $a++) {
      $spreadsheet->setActiveSheetIndex(0)
          ->setCellValue("A$x",$reports['data'][$a][0])
          ->setCellValue("B$x",$reports['data'][$a][1])
          ->setCellValue("C$x",$reports['data'][$a][2])
          ->setCellValue("D$x",$reports['data'][$a][3])
          ->setCellValue("E$x",strip_tags($reports['data'][$a][4]))
          ->setCellValue("F$x",strip_tags($reports['data'][$a][5]))
          ->setCellValue("G$x",strip_tags($reports['data'][$a][6]));

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
}
