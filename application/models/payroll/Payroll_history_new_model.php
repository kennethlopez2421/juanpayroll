<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payroll_history_new_model extends CI_Model {
  public function get_payroll_history_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'ref_no',
      1 => 'pay_day',
      2 => 'cut_off',
      3 => 'status'
    );

    $sql = "SELECT a.*, b.company, c.description as paytype, CONCAT(a.fromdate,' - ', a.todate) as cut_off
     FROM hris_payroll_summary a
     INNER JOIN hris_companies b ON a.company_id = b.id
     INNER JOIN paytype c ON a.paytype = c.paytypeid
     WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1";

     ### sub filter ###
     switch ($search->filter) {
       case 'divRefno':
         $ref_no = $this->db->escape($search->search);
         $sql .= " AND a.ref_no = $ref_no";
         break;
       case 'divCompany':
         $company = $this->db->escape($search->search);
         $sql .= " AND a.company_id = $company";
         break;
       case 'divPaytype':
         $paytype = $this->db->escape($search->search);
         $sql .= " AND a.paytype = $paytype";
         break;
       case 'divDate':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND a.pay_day >= $from AND a.pay_day <= $to";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY a.pay_day DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['ref_no'];
      $nestedData[] = $row['company'];
      $nestedData[] = $row['paytype'];
      $nestedData[] = $row['pay_day'];
      $nestedData[] = $row['cut_off'];
      $nestedData[] = ($row['status'] == 'waiting')
      ? '<select data-uid = "'.en_dec('en',$row['id']).'" data-ref_no = "'.$row['ref_no'].'" id="" class="form-control payroll_status payroll_update"><option value="waiting">Open</option> <option value="approved">Closed</option></select>'
      : '<select class = "form-control payroll_status_close payroll_update"><option value="approved">Closed</option></select>';

      if($row['status'] == 'waiting'){
        $buttons =
        '
        <button class="btn btn-primary btn_view" style = "width:90px;"
          data-id = "'.en_dec('en',$row['id']).'"
          data-manhours_id = "'.en_dec('en', $row['manhours_id']).'"
          data-deduction_id = "'.en_dec('en', $row['deduction_id']).'"
          data-additional_id = "'.en_dec('en', $row['additional_id']).'"
        >
          <i class="fa fa-eye mr-1"></i>View
        </button>
        ';
      }else{
        $buttons =
        '
        <button class="btn btn-primary btn_view" style = "width:90px;"
          data-id = "'.en_dec('en',$row['id']).'"
          data-manhours_id = "'.en_dec('en', $row['manhours_id']).'"
          data-deduction_id = "'.en_dec('en', $row['deduction_id']).'"
          data-additional_id = "'.en_dec('en', $row['additional_id']).'"
        >
          <i class="fa fa-eye mr-1"></i>View
        </button>
        <button class="btn btn-primary btn_bank_file" style = "width:90px;"
         data-payroll_refno = "'.$row['ref_no'].'"
        >
          <i class="fa fa-bank mr-1"></i>Bank File
        </button>
        ';
      }

      $nestedData[] =
      '
        <center>
          '.$buttons.'
        </center>
      ';

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_manhours_tbl_json($id){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT a.*, CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname, c.frequency,
     d.pay_day
     FROM hris_manhours_log a
     INNER JOIN employee_record b ON a.emp_id = b.employee_idno
     INNER JOIN paytype c ON a.paytype = c.paytypeid
     INNER JOIN hris_payroll_summary d ON a.manhours_summary_id = d.manhours_id
     WHERE a.enabled = 1 AND d.enabled = 1 AND a.manhours_summary_id = $id";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['emp_id'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['days'];
      $nestedData[] = $row['hours'];
      $nestedData[] = $row['nightdiff'];
      $nestedData[] = $row['absent'];
      $nestedData[] = $row['late'];
      $nestedData[] = $row['ot'];
      $nestedData[] = $row['ut'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_modal_breakdown" style = "width:90px;"
            id = "'.$row['emp_id'].'"
            data-fromdate = "'.$row['fromdate'].'"
            data-todate = "'.$row['todate'].'"
            data-type = "'.$row['paytype'].'"
            data-frequency = "'.$row['frequency'].'"
            data-pay_day = "'.$row['pay_day'].'"
          >
            <i class="fa fa-eye mr-1"></i>View
          </button>
        </center>
      ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_dduction_log_tbl_json($id){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);
    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT a.*, CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname, c.currency,
     a.paytype, d.frequency, e.pay_day
     FROM hris_deduction_log a
     INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
     INNER JOIN contract c ON b.id = c.contract_emp_id
     INNER JOIN paytype d ON a.paytype = d.paytypeid
     INNER JOIN hris_payroll_summary e ON a.deductionsum_id = e.deduction_id
     WHERE a.enabled = 1 AND c.enabled = 1 AND c.contract_status = 'active' AND a.deductionsum_id = $id";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $total = $row['sss'] + $row['sss_loan'] + $row['philhealth'] + $row['pag_ibig'] + $row['pag_ibig_loan'] + $row['salary_deduction'] + $row['cashadvance'];
      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['currency'].' '.number_format($row['sss'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['sss_loan'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['philhealth'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['pag_ibig'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['pag_ibig_loan'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['salary_deduction'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['cashadvance'],2);
      $nestedData[] = $row['currency'].' '.number_format($total,2);
      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_modal_breakdown" style = "width:90px;"
           id = "'.$row['employee_idno'].'"
           data-fromdate = "'.$row['fromdate'].'"
           data-todate = "'.$row['todate'].'"
           data-type = "'.$row['paytype'].'"
           data-frequency = "'.$row['frequency'].'"
           data-pay_day = "'.$row['pay_day'].'"
          >
            <i class="fa fa-eye mr-1"></i>View
          </button>
        </center>
      ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_additional_log_tbl_json($id){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT a.*, CONCAT(b.last_name,',', b.first_name,' ',b.middle_name) as fullname, c.currency,
     a.paytype, d.frequency, e.pay_day
     FROM hris_additional_log a
     INNER JOIN employee_record b ON a.emp_id = b.employee_idno
     INNER JOIN contract c ON b.id = c.contract_emp_id
     INNER JOIN paytype d ON a.paytype = d.paytypeid
     INNER JOIN hris_payroll_summary e ON a.additional_summary_id = e.additional_id
     WHERE a.enabled = 1 AND c.enabled = 1 AND c.contract_status = 'active' AND a.additional_summary_id = $id";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['emp_id'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['currency'].' '.$row['additionalpay'];
      $nestedData[] = $row['currency'].' '.$row['overtimepay'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_modal_breakdown" style = "width:90px;"
            id = "'.$row['emp_id'].'"
            data-fromdate = "'.$row['fromdate'].'"
            data-todate = "'.$row['todate'].'"
            data-type = "'.$row['paytype'].'"
            data-frequency = "'.$row['frequency'].'"
            data-pay_day = "'.$row['pay_day'].'"
          >
            <i class="fa fa-eye mr-1"></i>View
          </button>
        </center>
      ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_payroll_log_tbl_json($id){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );


    $sql = "SELECT a.*, CONCAT(b.last_name,',', b.first_name,' ',b.middle_name) as fullname, c.currency,
     d.frequency, e.company_id, e.ref_no, e.pay_day, c.currency, f.exchange_rate as ex_rate
     FROM hris_payroll_log a
     INNER JOIN employee_record b ON a.emp_id = b.employee_idno
     INNER JOIN contract c ON b.id = c.contract_emp_id
     INNER JOIN paytype d ON a.paytype = d.paytypeid
     INNER JOIN hris_payroll_summary e ON a.payroll_summary_id = e.id
     INNER JOIN hris_exchange_rates f ON c.currency = f.currency_code
     WHERE a.enabled = 1 AND c.enabled = 1 AND c.contract_status = 'active'
     AND e.enabled = 1
     AND a.payroll_summary_id = $id";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['emp_id'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['currency'].' '.number_format($row['grosspay'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['additionals'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['deductions'],2);
      $nestedData[] = $row['currency'].' '.number_format($row['netpay'],2);

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary payroll_breakdown" style = "width:90px;"
           data-emp_idno = "'.en_dec('en',$row['emp_id']).'"
           data-fromdate = "'.$row['fromdate'].'"
           data-todate = "'.$row['todate'].'"
           data-type = "'.$row['paytype'].'"
           data-frequency = "'.$row['frequency'].'"
           data-pay_day = "'.$row['pay_day'].'"
           data-payroll_refno = "'.en_dec('en',$row['ref_no']).'"
           data-company_id = "'.$row['company_id'].'"
           data-currency = "'.$row['currency'].'"
           data-ex_rate = "'.$row['ex_rate'].'"
          >
           <i class="fa fa-eye mr-1"></i>View
          </button>
        </center>
      ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_employee_id_from_payroll_log($ref_no){
    $ref_no = $this->db->escape($ref_no);
    $sql = "SELECT b.emp_id
     FROM hris_payroll_summary a
     INNER JOIN hris_payroll_log b ON a.id = b.payroll_summary_id
     INNER JOIN employee_record c ON b.emp_id = c.employee_idno
     WHERE a.ref_no = $ref_no AND a.enabled = 1 AND a.status = 'approved' AND c.first_month = 0";
    return $this->db->query($sql);
  }

  public function set_payslip_batch($data){
    $this->db->insert_batch('hris_payslip',$data);
  }

  public function update_payroll_history_status($id,$ref_no,$approved_by,$updated_at,$approved_date,$status = "approved"){
    $id = $this->db->escape($id);
    $ref_no = $this->db->escape($ref_no);
    $status = $this->db->escape($status);
    $updated_at = $this->db->escape($updated_at);
    $approved_date = $this->db->escape($approved_date);
    $approved_by = $this->db->escape($approved_by);

    $sql = "UPDATE hris_payroll_summary a
     INNER JOIN hris_payroll_log b ON a.id = b.payroll_summary_id
     INNER JOIN hris_manhours_summary c ON a.manhours_id = c.id
     INNER JOIN hris_deduction_summary d ON a.deduction_id = d.id
     INNER JOIN hris_additional_summary e ON a.additional_id = e.id
     INNER JOIN hris_manhours_log f ON c.id = f.manhours_summary_id
     INNER JOIN hris_deduction_log g ON d.id = g.deductionsum_id
     INNER JOIN hris_additional_log h ON e.id = h.additional_summary_id
     SET a.status = $status, b.status = $status, c.status = $status, d.status = $status, e.status = $status,
     f.status = $status, g.status = $status, h.status = $status, a.approved_date = $approved_date, a.updated_at = $updated_at,
     d.updated_at = $updated_at, d.approved_date = $approved_date, e.approved_date = $approved_date, e.updated_at = $updated_at,
     c.approved_date = $approved_date, c.updated_at = $updated_at, a.approved_by = $approved_by,
     c.approved_by = $approved_by, e.approved_by = $approved_by, d.approved_by = $approved_by, h.approved_by = $approved_by,
     h.updated_at = $updated_at, g.approved_by = $approved_by, g.updated_at = $updated_at,
     f.approved_by = $approved_by, f.updated_at = $updated_at, b.approved_by = $approved_by, b.updated_at = $updated_at
    WHERE a.id = $id AND a.ref_no = $ref_no AND a.enabled = 1 AND a.status = 'waiting'";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_first_month_employee_record($data){
    $this->db->update_batch('employee_record',$data,'employee_idno');
    return ($this->db->affected_rows() > 0) ? true : false;
  }

}
