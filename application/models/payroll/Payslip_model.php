<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payslip_model extends CI_Model {
  public function get_payslip_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'app_ref_no',
      1 => 'Name'
    );
    $emp_idno = $this->db->escape($this->session->emp_idno);
    $sql = "SELECT * FROM hris_payslip WHERE enabled = 1 AND employee_idno =$emp_idno";

    ### sub filter ###
    switch ($search->filter) {
      case 'divPayroll_refno':
        $ref_no = $this->db->escape($search->search);
        $sql .= " AND payroll_refno = $ref_no";
        break;
      case 'divDate':
        $from = $this->db->escape($search->from);
        $to = $this->db->escape($search->to);
        $sql .= " AND date_from >= $from AND date_to <= $to";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY date_created DESC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['payroll_refno'];
      $nestedData[] = $row['date_from'].' - '.$row['date_to'];
      $nestedData[] = '<span class="float-right">'.number_format($row['gross_salary'],2).'</span>';
      $nestedData[] = '<span class = "float-right">'.number_format($row['netpay'],2).'</span>';

      $d1 = new Datetime($row['date_from']);
      $d2 = new Datetime($row['date_to']);
      $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
      $nestedData[] =
      '
      <center>
        <button class="btn btn-sm btn-primary payroll_breakdown" style = "width:90px;"
          data-emp_idno = "'.$row['employee_idno'].'"
          data-fullname = "'.$row['name'].'"
          data-date = "'.$date.'"
          data-wdays = "'.number_format($row['days_duration'],2).'"
          data-gross_pay = "'.number_format($row['gross_salary'],2).'"
          data-gross_pay_less = "'.number_format($row['gross_salary_less'],2).'"
          data-reg_holiday = "'.number_format($row['regular_holiday'],2).'"
          data-reg_holiday_pay = "'.number_format($row['regular_holiday_duration'],2).'"
          data-spl_holiday = "'.number_format($row['special_holiday'],2).'"
          data-spl_holiday_pay = "'.number_format($row['special_holiday_duration'],2).'"
          data-sunday = "'.number_format($row['sundays'],2).'"
          data-sunday_pay = "'.number_format($row['sunday_duration'],2).'"
          data-absent = "'.number_format($row['absent_duration'],2).'"
          data-absent_deduction = "'.number_format($row['absent'],2).'"
          data-late = "'.number_format($row['late_duration']).'"
          data-late_deduct = "'.number_format(($row['late']),2).'"
          data-ut = "'.number_format($row['undertime_duration']).'"
          data-ut_deduct = "'.number_format($row['undertime'],2).'"
          data-total_deduct = "'.number_format($row['total_deductions'],2).'"
          data-sss = "'.number_format($row['sss'],2).'"
          data-sss_loan = "'.number_format($row['sss_loan'],2).'"
          data-pagibig_loan = "'.number_format($row['pag_ibig_loan'],2).'"
          data-philhealth = "'.number_format($row['philhealth'],2).'"
          data-pagibig = "'.number_format($row['pag_ibig'],2).'"
          data-cashadvance = "'.number_format($row['cashadvance'],2).'"
          data-sal_deduct = "'.number_format($row['salary_deduction'],2).'"
          data-add_pay = "'.number_format($row['additionals'],2).'"
          data-ot_min = "'.number_format($row['ot_duration']).'"
          data-ot_pay = "'.number_format($row['overtime'],2).'"
          data-net_pay = "'.number_format($row['netpay'],2).'"
          data-currency = "PHP"
          data-ex_rate = "1.00"
        >
          <i class="fa fa-eye"></i>&nbsp;View
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
}
