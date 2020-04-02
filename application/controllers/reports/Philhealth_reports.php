<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;
class Philhealth_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/philhealth_reports_model');
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

  public function get_philhealth_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->philhealth_reports_model->get_philhealth_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
      "companies" => $this->model->get_hris_companies()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('reports/philhealth_reports',$data);
  }

  public function check_export_to_excel_philhealth_reports(){
    $this->isLoggedIn();
    $search = json_decode($this->input->post('search'));
    $philhealth_reports_count = $this->philhealth_reports_model
                                ->check_export_to_excel_philhealth_reports($search)->num_rows();

    if($philhealth_reports_count > 0){
      $data = array("success" => 1, "message" => "Ok");
    }else{
      $data = array("success" => 0, "message" => "No data to export. Please try again.");
    }

    generate_json($data);

  }

  public function export_to_excel_philhealth_reports($month = ""){
    $this->isLoggedIn();
    if($month == ""){
      header("Refresh:0");
    }
    $title = $month." Philhealth Reports";
    $header = $month." Philhealth Reports";
    $philhealth_reports_data = $this->philhealth_reports_model->export_to_excel_philhealth_reports($month);
    $philhealth_reports_count = $this->philhealth_reports_model->export_to_excel_philhealth_reports($month)->num_rows();
    $last_index = $philhealth_reports_count + 3;
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator('HRIS')
        ->setLastModifiedBy('HRIS')
        ->setTitle($title)
        ->setSubject($title)
        ->setDescription($title);

    $styleArray = array(
        'font' => array('bold' => true,));
    $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$last_index.':'.'F'.$last_index)->applyFromArray($styleArray);

    foreach(range('A','F') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A1",'Philhealth No.')
        ->setCellValue("B1",'Employee ID')
        ->setCellValue("C1",'Employee Name')
        ->setCellValue("D1",'Date')
        ->setCellValue("E1",'Department')
        ->setCellValue("F1",'EE')
        ->setCellValue("G1",'ER')
        ->setCellValue("H1",'Total');

            // to start from the next line after header we set increment variable to 2
    $x= 2;
    $j = 1;
    foreach($philhealth_reports_data->result_array() as $sub){
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$x",$sub['philhealth_no'])
            ->setCellValue("B$x",$sub['employee_idno'])
            ->setCellValue("C$x",$sub['fullname'])
            ->setCellValue("D$x",$month)
            ->setCellValue("E$x",$sub['dept'])
            ->setCellValue("F$x",$sub['EE'])
            ->setCellValue("G$x",$sub['ER'])
            ->setCellValue("H$x",$sub['total']);
        $x++;
        $j++;
    }

    // $spreadsheet->setActiveSheetIndex(0)->setCellValue("E$last_index","Total Sales");
    // $spreadsheet->setActiveSheetIndex(0)->setCellValue("F$last_index",$total_sales);

    $spreadsheet->getActiveSheet()->setTitle($title);
    $spreadsheet->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$header.'.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit();
  }
}
