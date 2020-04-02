<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sss_loans_model extends CI_Model {
  public function get_sss_loans_json($search){
    $requestData = $_REQUEST;
    $search = json_decode($search);

    $columns = array(
      0 => 'sss_loan_voucher',
      1 => 'fullname',
      2 => 'sss_deduction_start',
      3 => 'sss_loan_start',
      4 => 'sss_total_loan',
      5 => 'monthly_amortization'
    );


    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, c.*,
      DATE_FORMAT(c.sss_deduction_start, '%b-%Y') as deduct_start,
      CONCAT(DATE_FORMAT(c.sss_loan_start, '%b-%Y'),' - ',DATE_FORMAT(c.sss_loan_end, '%b-%Y')) as period
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN hris_sss_loans c ON a.employee_idno = c.employee_idno
      WHERE a.enabled = 1 AND b.enabled = 1 AND b.contract_status = 'active'
      AND c.enabled = 1 AND c.status = 'active'";

      ### sub filter ###
      switch ($search->filter) {
        case 'divName':
          $emp_name = $this->db->escape("%".$search->search."%");
          $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                    OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                    OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
          break;
        case 'divDate':
          $from = $this->db->escape($search->from);
          $to = $this->db->escape($search->to);
          $sql .= " AND c.sss_deduction_start >= $from AND c.sss_deduction_end <= $to";
          break;
        case 'divLoan':
          $from = $this->db->escape($search->from);
          $to = $this->db->escape($search->to);
          $sql .= " AND c.sss_total_loan >= $from AND c.sss_total_loan <= $to";
          break;
        case 'divAmortization':
          $from = $this->db->escape($search->from);
          $to = $this->db->escape($search->to);
          $sql .= " AND c.monthly_amortization >= $from AND c.monthly_amortization <= $to";
          break;
        default:
          break;
      }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY sss_deduction_start ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $img_row = '<div class="img-thumbnail d-inline-block time_img mr-1" data-url = "'.$row['sss_loan_voucher'].'" data-title = "SSS Voucher"><img src="'.base_url($row['sss_loan_voucher']).'" alt="" height = "40" width = "40"/></div>';

      $nestedData[] = '<center>'.$img_row.'</center>';
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['deduct_start'];
      $nestedData[] = $row['period'];
      $nestedData[] = '<span class="float-right">'.number_format($row['sss_total_loan'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['monthly_amortization'],2).'</span>';
      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_view_modal"
            data-sss_loan_id = "'.$row['id'].'"
            data-sss_total_loan = "'.$row['sss_total_loan'].'"
            data-monthly_amortization = "'.$row['monthly_amortization'].'"
            style = "width:80px;"
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

  public function get_employee_w_sss_no($id = false){
    $sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
     a.employee_idno
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     WHERE a.enabled = 1 AND b.contract_status = 'active' AND a.sss_no != ''";

    if($id){
      $id = $this->db->escape($id);
      $sql .= " AND a.employee_idno = $id";
    }

    return $this->db->query($sql);
  }

  public function get_sss_loan($id){
    $id = $this->db->escape($id);
    $sql = "SELECT SUM(b.monthly_amortization) as monthly_payment, @month := DATE_FORMAT(b.payday, '%b-%Y') as month,
      (SELECT SUM(monthly_amortization) as total FROM hris_sss_loan_pending_deduction WHERE status = 'approved' AND enabled = 1 AND sss_loan_id = $id) as total,
      (SELECT GROUP_CONCAT(payroll_refno SEPARATOR ',') as payroll_refno FROM hris_sss_loan_pending_deduction WHERE status = 'approved' AND enabled = 1 AND sss_loan_id = $id AND DATE_FORMAT(payday, '%b-%Y') = @month) as payroll_refno
      FROM hris_sss_loans a
      INNER JOIN hris_sss_loan_pending_deduction b ON a.id = b.sss_loan_id
      WHERE b.status = 'approved' AND b.enabled = 1 AND a.status = 'active' AND a.enabled = 1
      AND a.id = $id GROUP BY DATE_FORMAT(b.payday, '%b-%Y') ORDER BY b.payday DESC";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    // $sql.=" ORDER BY payday ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    // $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['month'];
      $nestedData[] = $row['payroll_refno'];
      $nestedData[] = '<span class="float-right">'.$row['monthly_payment'].'</span>';

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;

  }

  public function set_sss_loan($data){
    $this->db->insert('hris_sss_loans',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
