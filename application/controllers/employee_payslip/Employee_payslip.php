<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee_payslip extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('payroll/Employee_payslip_model');
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
	public function Download_payslip($pono = ""){
		$this->isLoggedIn();
		$data = $this->session->userdata('Payslip_data');
		if($data != null){
			$data = $data;
		}else{
			$data = "no_data";
		}
		
		

	    $header = $this->load->view('includes/print_header' ,$data, true);
		$page = $this->load->view('payroll/employee_payslip' ,$data, true);

		$this->load->library('Pdf');
		//tcpdf();
		$obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);
		$title = "Payslip";
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

		$obj_pdf->Output("PO".$pono.".pdf", 'D');
		
	}

	public function View_payslip($pono = ""){
		$this->isLoggedIn();
		$data = $this->session->userdata('Payslip_data');
		if($data != null){
			$data = $data;
		}else{
			$data = "no_data";
		}
		
		

	    $header = $this->load->view('includes/print_header' ,$data, true);
		$page = $this->load->view('payroll/employee_payslip' ,$data, true);

		$this->load->library('Pdf');
		//tcpdf();
		$obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);
		$title = "Payslip";
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
	public function Generate_payslip(){
		//unset session from first open of tab
		$this->session->unset_userdata('Payslip_data');

		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$employee_idno = $this->input->post('employee_idno');


		$emp_payslip = $this->Employee_payslip_model->generate_payslip($date_from,$date_to,$employee_idno)->row();

		//check data if null
		//

		if($emp_payslip != null){
			if($emp_payslip->employee_idno != null){
				$employee_idno = $emp_payslip->employee_idno;
			}else{
				$employee_idno = "No Data Found";
			}

			if($emp_payslip->name != null){
				$name = $emp_payslip->name;
			}else{
				$name = "No Data Found";
			}

			if($emp_payslip->paytype_desc != null){
				$paytype_desc = $emp_payslip->paytype_desc;
			}else{
				$paytype_desc = "No Data Found";
			}
			
			if($emp_payslip->date_from != null){
				$date_from = $emp_payslip->date_from;
			}else{
				$date_from = '--:--';
			}


			if($emp_payslip->date_to != null){
				$date_to = $emp_payslip->date_to;
			}
			else{
				$date_to = '--:--';
			}

			if($emp_payslip->gross_salary != null){
				$gross_salary = $emp_payslip->gross_salary;
			}else{
				$gross_salary = 0;
			}
			
			if($emp_payslip->days_duration != null){
				$days_duration = $emp_payslip->days_duration;
			}else{
				$days_duration = 0;
			}

			if($emp_payslip->overtime != null){
				$overtime = $emp_payslip->overtime;
			}else{
				$overtime = 0;
			}

			if($emp_payslip->ot_duration != null){
				$ot_duration = $emp_payslip->ot_duration;
			}else{
				$ot_duration =0;
			}

			if($emp_payslip->additionals != null){
				$additionals = $emp_payslip->additionals;
			}else{
				$additionals = 0;
			}

			if($emp_payslip->regular_holiday != null){
				$regular_holiday = $emp_payslip->regular_holiday;
			}else{
				$regular_holiday = 0;
			}

			if($emp_payslip->regular_holiday_duration != null){
				$regular_holiday_duration = $emp_payslip->regular_holiday_duration;
			}else{
				$regular_holiday_duration = 0;
			}

			if($emp_payslip->special_holiday != null){
				$special_holiday = $emp_payslip->special_holiday;
			}else{
				$special_holiday = 0;
			}

			if($emp_payslip->special_holiday_duration != null){
				$special_holiday_duration = $emp_payslip->special_holiday_duration;
			}else{
				$special_holiday_duration = 0;
			}

			if($emp_payslip->sundays != null){
				$sundays = $emp_payslip->sundays;
			}else{
				$sundays = 0;
			}

			if($emp_payslip->sunday_duration != null){
				$sunday_duration = $emp_payslip->sunday_duration;
			}else{
				$sunday_duration = 0;
			}
			
			if($emp_payslip->absent != null){
				$absent = $emp_payslip->absent;
			}else{
				$absent = 0;
			}

			if($emp_payslip->absent_duration != null){
				$absent_duration = $emp_payslip->absent_duration;
			}else{
				$absent_duration = 0;
			}

			if($emp_payslip->late != null){
				$late = $emp_payslip->late;
			}else{
				$late = 0;
			}

			if($emp_payslip->late_duration != null){
				$late_duration = $emp_payslip->late_duration;
			}else{
				$late_duration = 0;
			}

			if($emp_payslip->undertime != null){
				$undertime = $emp_payslip->undertime;
			}else{
				$undertime = 0;
			}

			if($emp_payslip->undertime_duration != null){
				$undertime_duration = $emp_payslip->undertime_duration;
			}else{
				$undertime_duration = 0;
			}

			if($emp_payslip->sss != null){
				$sss = $emp_payslip->sss;
			}else{
				$sss = 0;
			}

			if($emp_payslip->philhealth != null){
				$philhealth = $emp_payslip->philhealth;
			}else{
				$philhealth = 0;
			}

			if($emp_payslip->pag_ibig != null){
				$pag_ibig = $emp_payslip->pag_ibig;
			}else{
				$pag_ibig = 0;
			}

			if($emp_payslip->sss_loan != null){
				$sss_loan = $emp_payslip->sss_loan;
			}else{
				$sss_loan = 0;
			}

			if($emp_payslip->pag_ibig_loan != null){
				$pag_ibig_loan = $emp_payslip->pag_ibig_loan;
			}else{
				$pag_ibig_loan = 0;
			}

			if($emp_payslip->cashadvance != null){
				$cashadvance = $emp_payslip->cashadvance;
			}else{
				$cashadvance = 0;
			}

			if($emp_payslip->salary_deduction != null){
				$salary_deduction = $emp_payslip->salary_deduction;
			}else{
				$salary_deduction = 0;
			}

			if($emp_payslip->total_deductions != null){
				$total_deductions = $emp_payslip->total_deductions;
			}else{
				$total_deductions = 0;
			}
			
			if($emp_payslip->netpay != null){
				$netpay = $emp_payslip->netpay;
			}else{
				$netpay = 0;
			}
				$emp_payslip = array(
				"employee_idno" => $employee_idno,
				"name" => $name,
				"paytype_desc" => $paytype_desc,
				"date_from" => $date_from,
				"date_to" => $date_to,
				"gross_salary" => $gross_salary,
				"days_duration" => $days_duration,
				"overtime" => $overtime,
				"ot_duration" => $ot_duration,
				"additionals" => $additionals,
				"regular_holiday" => $regular_holiday,
				"regular_holiday_duration" => $regular_holiday_duration,
				"special_holiday" => $special_holiday,
				"special_holiday_duration" => $special_holiday_duration,
				"sundays" => $sundays,
				"sunday_duration" => $sunday_duration,
				"absent" => $absent,
				"absent_duration" => $absent_duration,
				"late" => $late,
				"late_duration" => $late_duration,
				"undertime" => $undertime,
				"undertime_duration" => $undertime_duration,
				"sss" => $sss,
				"philhealth" => $philhealth,
				"pag_ibig" => $pag_ibig,
				"sss_loan" => $sss_loan,
				"pag_ibig_loan" => $pag_ibig_loan,
				"cashadvance" => $cashadvance,
				"salary_deduction" => $salary_deduction,
				"total_deductions" => $total_deductions,
				"netpay" => $netpay
				);

			$data = array('success' => 1, 'message' => 'Payslip has been successfully generated', 'output' => $emp_payslip);
			//set data in session
			$payslip_data = array('Payslip_data' => $emp_payslip);
			$this->session->set_userdata($payslip_data);


		}else{
			if($date_from != null || $date_to != null || $employee_idno != null){
				$data = array('success' => 0, 'message' => 'An error has occured on fetching the data. Please wait.', 'output' => 'No Output');
				$this->session->unset_userdata('Payslip_data');
			}else{
				$data = array('success' => 0, 'message' => 'No Payslip Generated. Please wait.', 'output' => 'No Output');
				$this->session->unset_userdata('Payslip_data');
			}
			
		}
		


		// if($emp_payslip != null){
		// 	$data = array('success' => 1, 'message' => 'Payslip has been successfully generated', 'output' => $emp_payslip);
		// 	//set data in session
		// 	$payslip_data = array('Payslip_data' => $emp_payslip);
		// 	$this->session->set_userdata($payslip_data);
		// }else{
		// 	$data = array('success' => 0, 'message' => 'An error has occured on fetching the data', 'output' => 'No Output');
		// }

		// print_r($data);
		echo json_encode($data);
	}






}


?>