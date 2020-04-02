<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;

class Contract_audit_trail_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/contract_audit_trail_report_model');
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

  public function get_contract_audit_trail_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->contract_audit_trail_report_model->get_contract_audit_trail_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('reports/contract_audit_trail_reports',$data);
  }

  public function export_to_excel($token = "", $from = "", $to = "", $filter = "", $search = ""){
    $token_dec = $this->session->userdata('token_session');
    $token = en_dec('dec',$token);
    if($token_dec != $token){
      $this->session->sess_destroy();
      $this->load->view('login');
      exit();
    }

    $search = array("from" => $from, "to" => $to, "filter" => $filter, "search" => $search);
    $search = (object)$search;

    $title = "";
    if($from != 'false' && $to != 'false'){
      $title = "Contract Audit Trail Reports from ".$from." to ".$to;
    }else{
      $title = "Contract Audit Trail Reports";
    }
    $header = $title;
    $type2 = "xlsx";
    $type1 = ucfirst($type2);

    $audit_trail = $this->contract_audit_trail_report_model->get_excel_reports($search);
    if($audit_trail->num_rows() == 0){
      $data = array("success" => 0, "message" => "No results available.");
      generate_json($data);
      exit();
    }

    $last_index = $audit_trail->num_rows();
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator('HRIS')
        ->setLastModifiedBy('HRIS')
        ->setTitle('Contract Audit Trail Reports')
        ->setSubject('Contract Audit Trail Report')
        ->setDescription('Contract Audit Trail Report');

    $styleArray = array(
        'font' => array('bold' => true,));
    $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A3:B3')->applyFromArray($styleArray);

    foreach(range('A','I') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A1",$title)
        ->setCellValue("A3",'Audit Trail')
        ->setCellValue("B3",'Date');

    $x= 4;
    $j = 1;

    foreach($audit_trail->result_array() as $row){

        $fields = "";
        switch ($row['fields']) {
          case 'contract_start':
            $fields = "contract start";
            break;
          case 'contract_end':
            $fields = "contract end";
            break;
          case 'work_sched':
            $fields = "work schedule";
            break;
          case 'empstatus':
            $fields = "employment status";
            break;
          case 'payoutmedium':
            $fields = "payout medium";
            break;
          case 'base_pay':
            $fields = "basic pay";
            break;
          case 'total_sal_converted':
            $fields = "total salary";
            break;
          default:
            $fields = $row['fields'];
            break;
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$x",strip_tags($row['admin_name']." <u>change the ".$fields." of ".$row['fullname']."</u> from <u>".$row['audit_trail']."</u>"))
            ->setCellValue("B$x",$row['created_at']);
        $x++;
        $j++;
    }

    $spreadsheet->getActiveSheet()->setTitle('Contract Audit Trail Reports');
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
