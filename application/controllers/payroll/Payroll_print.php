<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payroll_print extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('payroll/payroll_history_model');
	}

	// public function index(){ 
	// 	$this->load->library('Pdf');

	// 	$this->load->view("payroll/print_payroll");

		// $obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		// $obj_pdf->SetCreator(PDF_CREATOR);
		// $title = "Purchase Order Report";
		// $obj_pdf->SetTitle($title);
		// $obj_pdf->SetDefaultMonospacedFont('helvetica');
		// $obj_pdf->SetFont('helvetica', '', 9);
		// $obj_pdf->setFontSubsetting(false);
		// $obj_pdf->setPrintHeader(false);
		// $obj_pdf->AddPage();

		// ob_start();
		// we can have any view part here like HTML, PHP etc


	// }
public function index($token = '', $pono = ''){ //view specific summary
		// $this->isLoggedIn();
            $header = $this->load->view('includes/print_header', '', true);
			$page = $this->load->view('payroll/print_payroll','', true);

			$this->load->library('Pdf');
			//tcpdf();
			$obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
			$obj_pdf->SetCreator(PDF_CREATOR);
			$title = "Purchase Order Report";
			$obj_pdf->SetTitle($title);
			$obj_pdf->SetDefaultMonospacedFont('helvetica');
			$obj_pdf->SetFont('helvetica', '', 9);
			$obj_pdf->setFontSubsetting(false);
			$obj_pdf->setPrintHeader(false);
			$obj_pdf->AddPage();
			$obj_pdf->setCellPaddings(0,0,0,0);


			ob_start();
			// we can have any view part here like HTML, PHP etc

            $obj_pdf->writeHTML($header, true, false, true, false, '');
            // $obj_pdf->writeHTML($page, true, false, true, false, '');


			$style = array(
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
			);

			//QRCODE,H : QR-CODE Best error correction
			$obj_pdf->write2DBarcode('PO_'.$pono, 'QRCODE,H', 170, 15, 17, 17, $style, 'N');

			echo $page;
			$content = ob_get_contents();
			ob_end_clean();

			$obj_pdf->writeHTML($content, true, false, true, false, '');
			
			$obj_pdf->Output("PO".$pono.".pdf", 'I');

        }
	}

?>