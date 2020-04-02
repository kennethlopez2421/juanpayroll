<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;

class Compensation_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/compensation_reports_model');
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

  public function get_compensation_reports_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->compensation_reports_model->get_compensation_reports_json($search);
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
    $this->load->view('reports/compensation_reports',$data);
  }

  public function export_to_excel($token = "", $from = "", $to = "", $filter = "", $keyword = ""){
    $token_dec = $this->session->userdata('token_session');
    $token = en_dec('dec',$token);
    if($token_dec != $token){
      $this->session->sess_destroy();
      $this->load->view('login');
      exit();
    }

    $search = array("from" => $from, "to" => $to, "filter" => $filter, "keyword" => $keyword);
    $search = (object)$search;

    $title = "Compensation Reports from ".$from." to ".$to;
    $header = $title;
    $type2 = "xlsx";
    $type1 = ucfirst($type2);

    $compensation = $this->compensation_reports_model->get_excel_reports($search);
    // die($compensation);
    if($compensation->num_rows() == 0){
      $data = array("success" => 0, "message" => "No results available.");
      generate_json($data);
      exit();
    }

    $last_index = $compensation->num_rows();
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator('HRIS')
        ->setLastModifiedBy('HRIS')
        ->setTitle('Compensation Reports')
        ->setSubject('Compensation Report')
        ->setDescription('Compensation Report');

    $styleArray = array(
        'font' => array('bold' => true,));
    $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A3:I3')->applyFromArray($styleArray);

    foreach(range('A','I') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A1",$title)
        ->setCellValue("A3",'Employee ID')
        ->setCellValue("B3",'Employee Name')
        ->setCellValue("C3",'Payroll Ref No.')
        ->setCellValue("D3",'Date')
        ->setCellValue("E3",'SSS')
        ->setCellValue("F3",'Philhealth')
        ->setCellValue("G3",'Pag Ibig')
        ->setCellValue("H3",'Tax')
        ->setCellValue("I3",'Total');

    $x= 4;
    $j = 1;

    foreach($compensation->result_array() as $sub){
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$x",$sub['employee_idno'])
            ->setCellValue("B$x",$sub['fullname'])
            ->setCellValue("C$x",$sub['payroll_ref_no'])
            ->setCellValue("D$x",$sub['cutoff_from']."-".$sub['cutoff_to'])
            ->setCellValue("E$x",$sub['sss'])
            ->setCellValue("F$x",$sub['philhealth'])
            ->setCellValue("G$x",$sub['pagibig'])
            ->setCellValue("H$x",$sub['tax'])
            ->setCellValue("I$x",$sub['sss'] + $sub['philhealth'] + $sub['pagibig'] + $sub['tax']);
        $x++;
        $j++;
    }

    $spreadsheet->getActiveSheet()->setTitle('Compensation Reports');
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
