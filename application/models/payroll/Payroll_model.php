<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payroll_model extends CI_Model {
  public function get_paytype(){
    $sql = "SELECT * FROM paytype WHERE enabled = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  ### Man Hours ###
  public function get_man_hours_log($company,$from,$to,$type,$frequency,$search = ""){
    $cfrom = $from;
    $cto = $to;
    $company = $this->db->escape($company);
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $type = $this->db->escape($type);
    $frequency = $this->db->escape($frequency);

    $requestData = $_REQUEST;

		$columns = array(
			0 => 'emp_id',
      1 => 'fullname',
			2 => 'days',
      3 => 'man_hours',
      4 => 'absent',
			5 => 'late',
      6 => 'ut',
      7 => 'ot'
		);

    $sql = "SELECT a.employee_idno as emp_idno,
            (SUM(d.man_hours)) as man_hours,
            (SUM(d.late)) as late,
            (SELECT SUM(minutes_of_overtime) FROM overtime_pays a
              WHERE a.employee_id = emp_idno
              AND a.date_rendered BETWEEN $from AND $to AND a.type = 'overtime' AND a.status = 'certified'
              AND a.enabled = 1
            ) as overtime,
            (SUM(d.undertime)) as undertime,
            (SELECT COUNT(absent) FROM time_record_summary a
              WHERE a.employee_idno = emp_idno
              AND a.date_created BETWEEN $from AND $to AND absent = 1 AND a.enabled = 1
            ) as absent,
            (SUM(d.total_minutes)) as total_minutes,
            (SELECT COUNT(id) FROM time_record_summary a
              WHERE a.employee_idno = emp_idno
              AND a.date_created BETWEEN $from AND $to AND (d.absent = 0 || (d.absent = 1 && d.remarks = 4)) AND a.enabled = 1
            ) as days,
            (SUM(d.night_diff)) as night_diff,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            c.description as pay_type, b.contract_ref_no
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN paytype c ON b.paytype = c.paytypeid
            LEFT JOIN time_record_summary d ON a.employee_idno = d.employee_idno
            WHERE a.enabled = 1 AND b.contract_status = 'active' AND d.absent = 0
            AND c.paytypeid = $type AND b.company_id = $company
            AND d.date_created BETWEEN $from AND $to GROUP BY a.employee_idno";

    $query = $this->db->query($sql);
    // return $this->db->last_query();

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;

    $data_main = array();
    foreach($query->result_array() as $row){
      $nestedData = array();
      $total_man_hours = $row['man_hours'];

      $nestedData[] = $row['emp_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['days'];
      $nestedData[] = number_format($total_man_hours,2);
      $nestedData[] = number_format($row['night_diff'],2);
      $nestedData[] = $row['absent'];
      $nestedData[] = number_format($row['late']);
      $nestedData[] = number_format($row['overtime']);
      $nestedData[] = number_format($row['undertime']);
      $nestedData[] =
      '
        <center>
          <button class="btn btn-sm btn-primary btn_modal_breakdown" style = "width:90px;"
            id = "'.$row['emp_idno'].'"
            data-fromdate = "'.$from.'"
            data-todate = "'.$to.'"
            data-type = "'.$type.'"
            data-frequency = "'.$frequency.'"
          >
            <i class="fa fa-eye"></i>&nbsp;View
          </button>
        </center>
      ';
      $nestedData[] = $row['contract_ref_no'];

      $data_main[] = $nestedData;
    }

    // $query = $this->db->query($sql);

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $d1 = new Datetime($cfrom);
    $d2 = new Datetime($cto);
    $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
    $p_type = "";

    foreach($query->result_array() as $row){
      $nestedData = array();
      $total_man_hours = $row['man_hours'];

      $nestedData[] = $row['emp_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['days'];
      $nestedData[] = number_format($total_man_hours,2);
      $nestedData[] = number_format($row['night_diff'],2);
      $nestedData[] = $row['absent'];
      $nestedData[] = number_format($row['late']);
      $nestedData[] = number_format($row['overtime']);
      $nestedData[] = number_format($row['undertime']);
      $nestedData[] =
      '
        <center>
          <button class="btn btn-sm btn-primary btn_modal_breakdown" style = "width:90px;"
            id = "'.$row['emp_idno'].'"
            data-fromdate = "'.$from.'"
            data-todate = "'.$to.'"
            data-type = "'.$type.'"
            data-frequency = "'.$frequency.'"
          >
            <i class="fa fa-eye"></i>&nbsp;View
          </button>
        </center>
      ';

      $data[] = $nestedData;
      $p_type = $row['pay_type'];

    }

    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(
      "date" => $date,
      "p_type" => $p_type,
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      // "recordsTotal"    => count((array)$data),
      // "recordsFiltered" => count((array)$data),
      "data"            => $data,
      "data_all"        => $data_main
    );

    return $json_data;
  }

  public function get_manhours_breakdown($emp_id,$from,$to,$type,$frequency, $search = ""){
    $cfrom = $from;
    $cto = $to;
    $emp_id = $this->db->escape($emp_id);

    $requestData = $_REQUEST;

		$columns = array(
			0 => 'emp_id',
      1 => 'fullname',
			2 => 'date',
      3 => 'man_hours',
      4 => 'absent',
			5 => 'late',
      6 => 'ut',
      7 => 'ot'
		);

    $user_sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
        d.work_sched, c.description as paytype
        FROM employee_record a
        INNER JOIN contract b ON a.id = b.contract_emp_id
        LEFT JOIN paytype c ON b.paytype = c.paytypeid
        LEFT JOIN work_schedule d ON b.work_sched_id = d.id
        WHERE b.contract_status = 'active' AND a.enabled = 1 AND a.employee_idno = $emp_id";
    $user = $this->db->query($user_sql)->row();

    $sdate = new Datetime(clean_string($cfrom));
    $edate = new Datetime(clean_string($cto));

    $date = $cfrom."-".$cto;
    $name = $user->fullname;
    $paytype = $user->paytype;
    $ddate = array();

    $data = array();

    for ($i= $sdate; $i <= $edate; $i->modify('+1 day')) {
      $ldate_clean = $i->format('Y-m-d');
      $ldate = $this->db->escape($ldate_clean);

      $sql = "SELECT d.*, c.description as paytype,
          CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
          a.employee_idno as emp_id, d.remarks,
          @ot_min := (SELECT minutes_of_overtime FROM overtime_pays WHERE enabled = 1 AND type = 'overtime' AND status = 'certified' AND employee_id = $emp_id AND date_rendered = $ldate) as ot_min
          FROM employee_record a
          LEFT JOIN contract b ON a.id = b.contract_emp_id
          LEFT JOIN paytype c ON b.paytype = c.paytypeid
          LEFT JOIN time_record_summary d ON a.employee_idno = d.employee_idno
          WHERE b.contract_status = 'active' AND a.enabled = 1 AND (d.absent = 0 || (d.absent = 1 && d.remarks = 4))
          AND a.employee_idno = $emp_id
          AND d.date_created = $ldate";

      $holiday_sql = "SELECT
          a.date, b.payratio, b.payratio2, b.description as holiday, b.holidaytypeid as h_type
          FROM holidays_tran a
          LEFT JOIN holidaytype b ON a.holiday_type = b.holidaytypeid
          WHERE a.date = $ldate AND a.enabled = 1";

      $timelog = $this->db->query($sql);
      $holiday = $this->db->query($holiday_sql);

      $days = array('mon','tue','wed','thu','fri','sat','sun');
      $d = new Datetime($ldate_clean);
      $wdate = strtolower($d->format('D'));
      $worksched = (array)json_decode($user->work_sched);
      $nestedData = array();
      ### timelog and holiday
      if($timelog->num_rows() > 0 && $holiday->num_rows() > 0){
        $timelog_data = $timelog->row();
        $holiday_data = $holiday->row();

        $t1 = new Datetime($timelog_data->time_in);
        $t2 = new Datetime($timelog_data->time_out);

        $nestedData[] = $d->format('l M d, Y');
        $nestedData[] = $holiday_data->holiday;
        $nestedData[] = $t1->format('h:i a')." - ".$t2->format('h:i a');
        $nestedData[] = $timelog_data->man_hours;
        $nestedData[] = $timelog_data->night_diff;
        $nestedData[] = $timelog_data->late;
        $nestedData[] = $timelog_data->ot_min;
        $nestedData[] = $timelog_data->undertime;

      }
      ### timelog
      if($timelog->num_rows() > 0 && $holiday->num_rows() == 0){
        $timelog_data = $timelog->row();

        $t1 = new Datetime($timelog_data->time_in);
        $t2 = new Datetime($timelog_data->time_out);

        $nestedData[] = $d->format('l M d, Y');
        $nestedData[] = ($timelog_data->remarks == 4) ? "Leave" : "Regular Day";
        $nestedData[] = ($timelog_data->remarks == 4)
          ? '--:--'." - ".'--:--'
          : $t1->format('h:i a')." - ".$t2->format('h:i a');
        $nestedData[] = $timelog_data->man_hours;
        $nestedData[] = $timelog_data->night_diff;
        $nestedData[] = $timelog_data->late;
        $nestedData[] = $timelog_data->ot_min;
        $nestedData[] = $timelog_data->undertime;
      }
      ### holiday only
      if($timelog->num_rows() == 0 && $holiday->num_rows() > 0){
        $holiday_data = $holiday->row();

        $nestedData[] = $d->format('l M d, Y');
        $nestedData[] = $holiday_data->holiday;
        $nestedData[] = '--:--'." - ".'--:--';
        $nestedData[] = 0;
        $nestedData[] = 0;
        $nestedData[] = 0;
        $nestedData[] = 0;
        $nestedData[] = 0;
      }
      ### absent
      if($timelog->num_rows() == 0 && $holiday->num_rows() == 0){
        for ($x=0; $x < 7; $x++) {
          if($wdate == $days[$x]){
            if($worksched[$days[$x]][0] != ""){
              $day_type = "Absent";
            }else{
              $day_type = "Day Off";
            }

            $nestedData[] = $d->format('l M d, Y');
            $nestedData[] = $day_type;
            $nestedData[] = '--:--'." - ".'--:--';
            $nestedData[] = 0;
            $nestedData[] = 0;
            $nestedData[] = 0;
            $nestedData[] = 0;
            $nestedData[] = 0;
          }
        }
      }

      $data[] = $nestedData;
    }

    $totalData = count((array)$data);
		$totalFiltered = count((array)$data);

    if($search != ""){
			$sql .= $search;
    }

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $json_data = array(
      "date" => clean_string($date),
      "name" => $name,
      "p_type" => $paytype,
      "category" => "Man Hours",
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;

  }

  public function set_man_hours_summary($data){
    $this->db->insert('hris_manhours_summary',$data);
    return $this->db->insert_id();
  }

  public function set_man_hours_log($data){
    $this->db->insert('hris_manhours_log',$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function set_man_hours_log_batch($data){
    $this->db->insert_batch('hris_manhours_log',$data);
    return ($this->db->affected_rows() > 0) ? true :false;
  }

  ### Deduction ###
  public function get_deduction_log($company,$from,$to,$type,$frequency,$pay_day,$search = ""){
    $cfrom = $from;
    $cto = $to;
    $raw_payday = $pay_day;
    $company = $this->db->escape($company);
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $type = $this->db->escape($type);
    $frequency = $this->db->escape($frequency);
    $pay_day = $this->db->escape($pay_day);

    $requestData = $_REQUEST;

		$columns = array(
			0 => 'emp_id',
      1 => 'fullname',
			2 => 'sss',
      3 => 'philhealth',
      4 => 'pagibig',
			5 => 'salary_deduction',
      6 => 'cash_advance'
		);

    $sql = "SELECT @emp_id := a.employee_idno as emp_id, a.first_month,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            a.sss_no, a.philhealth_no, a.pagibig_no, a.tin_no,
            @year_diff := (SELECT (CASE WHEN DATEDIFF('2020-01-01', $pay_day) < 365 THEN 0 ELSE DATEDIFF('2020-01-01', $pay_day) / 365 END)) as year_diff,
            @ph_contri := (SELECT (CASE WHEN ((.5 * year_diff) + 3.0) > 5.0 THEN 5.0 / 100 ELSE ((.5 * year_diff) + 3.0) / 100 END)) as ph_contri,
            (SELECT SUM(e.amount)
              FROM salary_deduction e
              WHERE e.employee_idno = emp_id
              AND e.status = 'certified'
              AND DATE(e.date_created)
              BETWEEN $from AND $to) as salary_deduction,
            (SELECT SUM(((f.amount + f.rate) / f.terms) / $frequency)
              FROM cash_advance_tran f
              WHERE f.employee_id = emp_id
              AND f.status = 'certified'
              AND (f.date_of_effectivity >= f.date_of_effectivity AND f.date_of_effectivity <= $to)
              AND f.date_end >= $to) as cash_advance,
              (ss_ee / $frequency) as sss,
            (SELECT (ss.monthly_amortization / $frequency) FROM hris_sss_loans ss
              WHERE $pay_day BETWEEN ss.sss_loan_start AND ss.sss_loan_end
              AND ss.status = 'active' AND ss.enabled = 1 AND ss.employee_idno = @emp_id
            ) as sss_loan,
            (SELECT (love.monthly_amortization / $frequency) FROM hris_pagibig_loans love
              WHERE $pay_day BETWEEN love.pagibig_loan_start AND love.pagibig_loan_end
              AND love.status = 'active' AND love.enabled = 1 AND love.employee_idno = @emp_id
            ) as pagibig_loan,
            (SELECT ss.id as sss_loan_id FROM hris_sss_loans ss
              WHERE $pay_day BETWEEN ss.sss_loan_start AND ss.sss_loan_end
              AND ss.status = 'active' AND ss.enabled = 1 AND ss.employee_idno = @emp_id
            ) as sss_loan_id,
            (SELECT love.id as pagibig_loan_id FROM hris_pagibig_loans love
              WHERE $pay_day BETWEEN love.pagibig_loan_start AND love.pagibig_loan_end
              AND love.status = 'active' AND love.enabled = 1 AND love.employee_idno = @emp_id
            ) as pagibig_loan_id,
            (CASE WHEN h.basic_mo_sal1 > 10000
              THEN (((b.base_pay * @ph_contri) / 2) / $frequency)
              WHEN h.basic_mo_sal1 > 40000 THEN (h.employee_share1 / $frequency)
              ELSE ((h.employee_share1 / 2) / $frequency) END) as philhealth,
            (CASE WHEN b.pagibig > 1
              THEN (5000 * (i.employee_share / 100)) / $frequency
              ELSE (b.base_pay * (i.employee_share / 100)) / $frequency END) as pagibig,
            j.description as pay_type, f.id as ca_id, b.currency, m.exchange_rate as ex_rate,
            b.contract_ref_no
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN hris_position c ON b.position_id = c.position_id
            LEFT JOIN salary_deduction e ON a.employee_idno = e.employee_idno
            LEFT JOIN cash_advance_tran f ON a.employee_idno = f.employee_id
            LEFT JOIN sss g ON b.sss = g.id
            LEFT JOIN philhealth h ON b.philhealth = h.phID
            LEFT JOIN pagibig i ON b.pagibig = i.id
            LEFT JOIN paytype j ON b.paytype = j.paytypeid
            INNER JOIN time_record_summary k ON a.employee_idno = k.employee_idno
            LEFT JOIN hris_exchange_rates m ON b.currency = m.currency_code
            WHERE b.contract_status = 'active'
                  AND a.enabled = 1 AND k.absent = 0
                  AND b.paytype = $type AND b.company_id = $company
                  AND m.enabled = 1
                  AND k.date_created BETWEEN $from AND $to GROUP BY a.employee_idno
                  ORDER BY fullname ASC";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;

    // $query = $this->db->query($sql);
		// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
		//012819
		// $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

		$query = $this->db->query($sql);
    $data_main = array();
		$data = array();
    $ca_payment = array();
    $sss_loan_payment = array();
    $pagibig_loan_payment = array();
    $total_sss = 0;
    $total_philhealth = 0;
    $total_pagibig = 0;
    $total_sal_dduction = 0;
    $total_cash_advance = 0;
    $d1 = new Datetime($cfrom);
    $d2 = new Datetime($cto);
    $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
    $p_type = "";

		foreach( $query->result_array() as $row )
		{
      if($row['first_month'] == 0){ // FIRST SALARY
        $sss        = 0;
        $philhealth = 0;
        $pagibig    = 0;
      }

      if($row['first_month'] == 1){ // AFTER FIRST SALARY
        $sss          = ($row['currency'] == "PHP") ? $row['sss'] : (float)$row['sss'] / (float)$row['ex_rate'];
        $philhealth   = ($row['currency'] == "PHP") ? $row['philhealth'] : (float)$row['philhealth'] / (float)$row['ex_rate'];
        $pagibig      = ($row['currency'] == "PHP") ? $row['pagibig'] : (float)$row['pagibig'] / (float)$row['ex_rate'];

      }

      $sss          = ($row['sss_no'] == '') ? 0 : $sss;
      $philhealth   = ($row['philhealth_no'] == '') ? 0 : $philhealth;
      $pagibig      = ($row['pagibig_no'] == '') ? 0 : $pagibig;

      // $sss          = ($row['currency'] == "PHP") ? $row['sss'] : (float)$row['sss'] / (float)$row['ex_rate'];
      // $philhealth   = ($row['currency'] == "PHP") ? $row['philhealth'] : (float)$row['philhealth'] / (float)$row['ex_rate'];
      // $pagibig      = ($row['currency'] == "PHP") ? $row['pagibig'] : (float)$row['pagibig'] / (float)$row['ex_rate'];
      $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
      $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

      $cash_advance     = ($row['currency'] == "PHP") ? $row['cash_advance'] : (float)$row['cash_advance'] / (float)$row['ex_rate'];
      $salary_deduction = ($row['currency'] == "PHP") ? $row['salary_deduction'] : (float)$row['salary_deduction'] / (float)$row['ex_rate'];

			$nestedData=array();

      $total = $sss + $philhealth + $pagibig + $salary_deduction + $salary_deduction + $salary_deduction + $pagibig_loan;
			$nestedData[] = $row['emp_id'];
			$nestedData[] = $row['fullname'];
			$nestedData[] = $row['currency'].' '.number_format($sss, 2);
			$nestedData[] = $row['currency'].' '.number_format($sss_loan, 2);
			$nestedData[] = $row['currency'].' '.number_format($philhealth, 2);
			$nestedData[] = $row['currency'].' '.number_format($pagibig, 2);
      $nestedData[] = $row['currency'].' '.number_format($pagibig_loan, 2);
			$nestedData[] = $row['currency'].' '.number_format($salary_deduction, 2);
			$nestedData[] = $row['currency'].' '.number_format($cash_advance, 2);
			$nestedData[] = $row['currency'].' '.number_format(round($total,2), 2);
      $nestedData[] =
      '
      <center>
        <button class="btn btn-sm btn-primary btn_modal_breakdown" style = "width:90px;"
          id = "'.$row['emp_id'].'"
          data-fromdate = "'.$from.'"
          data-todate = "'.$to.'"
          data-type = "'.$type.'"
          data-frequency = "'.$frequency.'"
          data-pay_day = "'.$pay_day.'"
        >
          <i class="fa fa-eye"></i>&nbsp;View
        </button>
      </center>
      ';

      $nestedData[] = $row['contract_ref_no'];
      $nestedData[] = $row['currency'];
      $nestedData[] = $row['ex_rate'];

      ### sss loan payment ###
      if($row['sss_loan_id'] != ""){
        $sss_loan_nested = array(
          "sss_loan_id" => $row['sss_loan_id'],
          "employee_idno" => $row['emp_id'],
          "payroll_refno" => "",
          "monthly_amortization" => $row['sss_loan'],
          "sss_loan_from" => $cfrom,
          "sss_loan_to" => $cto,
          "payday" => $raw_payday
        );
        $sss_loan_payment[] = $sss_loan_nested;
      }

      ### pagibig loan payment ###
      if($row['pagibig_loan_id'] != ""){
        $pagibig_loan_nested = array(
          "pagibig_loan_id" => $row['pagibig_loan_id'],
          "employee_idno" => $row['emp_id'],
          "payroll_refno" => "",
          "monthly_amortization" => $row['sss_loan'],
          "pagibig_loan_from" => $cfrom,
          "pagibig_loan_to" => $cto,
          "payday" => $raw_payday
        );
        $pagibig_loan_payment[] = $pagibig_loan_nested;
      }

      ### cash advance payment history ###
      $emp_id = $this->db->escape($row['emp_id']);
      $ca_sql = "SELECT ((c.amount + c.rate) / c.terms) / $frequency as cashadvance,
                CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
                d.description as pay_type,
                c.reason as reason, c.date_of_effectivity as ca_date, c.date_end as ca_date2,
                c.id as ca_id, c.total_balance
                FROM employee_record a
                INNER JOIN contract b ON a.id = b.contract_emp_id
                LEFT JOIN cash_advance_tran c ON a.employee_idno = c.employee_id
                LEFT JOIN paytype d ON b.paytype = d.paytypeid
                WHERE b.contract_status = 'active' AND a.enabled = 1 AND c.status = 'certified'
                AND (date_of_effectivity >= date_of_effectivity AND date_of_effectivity <= $to)
                AND date_end >= $to AND a.employee_idno = $emp_id AND d.paytypeid = $type
                AND b.company_id = $company";
      $ca_query = $this->db->query($ca_sql);

      if($ca_query->num_rows() > 0){
        foreach($ca_query->result_array() as $ca){
          $ca_bal = round($ca['total_balance'] - $ca['cashadvance'],2);
          $ca_nested = array(
            "id" => $row['emp_id'],
            'ca_id' => $ca['ca_id'],
            "payroll_refno" => "",
            "ca_payment" => round($ca['cashadvance'],2),
            "ca_balance" => ($ca_bal < 0) ? 0.00 : $ca_bal,
            "from" => $cfrom,
            "to" => $cto
          );

          $ca_payment[] = $ca_nested;
        }
      }


      $data[] = $nestedData;
      $total_sss += $row['sss'];
      $total_philhealth += $row['philhealth'];
      $total_pagibig += $row['pagibig'];
      $total_sal_dduction += $row['salary_deduction'];
      $total_cash_advance += $row['cash_advance'];
      $p_type = $row['pay_type'];
		}

    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

		$json_data = array(
      "date" => $date,
      "p_type" => $p_type,
      "total_sss" => number_format($total_sss, 2),
      "total_philhealth" => number_format($total_philhealth, 2),
      "total_pagibig" => number_format($total_pagibig, 2),
      "total_sal_dduction" => number_format($total_sal_dduction, 2),
      "total_cash_advance" => number_format($total_cash_advance, 2),
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $data,
      "data_all"        => $data,
      "ca_payment"      => $ca_payment,
      "sss_loan_payment" => $sss_loan_payment,
      "pagibig_loan_payment" => $pagibig_loan_payment
		);
		return $json_data;

    // return $this->db->query($sql,$data);

  }

  public function get_deduction_breakdown_comp($emp_id,$from,$to,$type,$frequency,$pay_day, $search = ""){
    $cfrom = $from;
    $cto = $to;
    $emp_id = $this->db->escape($emp_id);
    // $frequency = (int)$frequency;
    $requestData = $_REQUEST;

    $columns = array(
			0 => 'date',
      1 => 'sss',
			2 => 'philhealth',
      3 => 'pagibig',
      4 => 'total'
		);

    $sql = "SELECT (c.ss_ee / $frequency) as sss, a.id as emp_id,
                CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
                f.description as pay_type, a.sss_no, a.pagibig_no, a.philhealth_no, a.tin_no,
                (SELECT (ss.monthly_amortization / $frequency) FROM hris_sss_loans ss
                  WHERE ss.sss_deduction_start BETWEEN $from AND $to
                  AND $pay_day BETWEEN ss.sss_loan_start AND ss.sss_loan_end
                  AND ss.status = 'active' AND ss.enabled = 1 AND ss.employee_idno = $emp_id
                ) as sss_loan,
                (SELECT (love.monthly_amortization / $frequency) FROM hris_pagibig_loans love
                  WHERE love.pagibig_deduction_start BETWEEN $from AND $to
                  AND $pay_day BETWEEN love.pagibig_loan_start AND love.pagibig_loan_end
                  AND love.status = 'active' AND love.enabled = 1 AND love.employee_idno = $emp_id
                ) as pagibig_loan,
                (CASE WHEN d.basic_mo_sal1 > 10000
                  THEN (((b.base_pay * 0.0275) / 2) / $frequency)
                  WHEN d.basic_mo_sal1 > 40000 THEN (d.employee_share1 / $frequency)
                  ELSE ((d.employee_share1 / 2) / $frequency) END) as philhealth,
                (CASE WHEN b.pagibig > 1
                  THEN (5000 * (e.employee_share / 100)) / $frequency
                  ELSE (b.base_pay * (e.employee_share / 100)) / $frequency END) as pagibig,
                b.currency, i.exchange_rate as ex_rate
                FROM employee_record a
                INNER JOIN contract b ON a.id = b.contract_emp_id
                LEFT JOIN sss c ON b.sss = c.id
                LEFT JOIN philhealth d ON b.philhealth = d.phID
                LEFT JOIN pagibig e ON b.pagibig = e.id
                LEFT JOIN paytype f ON b.paytype = f.paytypeid
                LEFT JOIN hris_exchange_rates i ON b.currency = i.currency_code
                WHERE b.contract_status = 'active' AND a.enabled = 1 AND d.enabled = 1
                AND a.employee_idno = $emp_id AND f.paytypeid = $type AND i.enabled = 1";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY emp_id ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    $data = array();

    $fullname = "";
    $paytype = "";

    foreach( $query->result_array() as $row )
    {
      $sss          = ($row['currency'] == "PHP") ? $row['sss'] : (float)$row['sss'] / (float)$row['ex_rate'];
      $philhealth   = ($row['currency'] == "PHP") ? $row['philhealth'] : (float)$row['philhealth'] / (float)$row['ex_rate'];
      $pagibig      = ($row['currency'] == "PHP") ? $row['pagibig'] : (float)$row['pagibig'] / (float)$row['ex_rate'];
      $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
      $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

      $sss          = ($row['sss_no'] == '') ? 0 : $sss;
      $philhealth   = ($row['philhealth_no'] == '') ? 0 : $philhealth;
      $pagibig      = ($row['pagibig_no'] == '') ? 0 : $pagibig;

      $nestedData=array();

      $total_comp = $sss + $philhealth + $pagibig + $sss_loan + $pagibig_loan;
      $nestedData[] = clean_string($from)." - ".clean_string($to);
      $nestedData[] = $row['currency'].' '.number_format($sss, 2);
      $nestedData[] = $row['currency'].' '.number_format($sss_loan, 2);
      $nestedData[] = $row['currency'].' '.number_format($philhealth, 2);
      $nestedData[] = $row['currency'].' '.number_format($pagibig, 2);
      $nestedData[] = $row['currency'].' '.number_format($pagibig_loan, 2);
      $nestedData[] = $row['currency'].' '.number_format($total_comp, 2);

      $data[] = $nestedData;
      $fullname = $row['fullname'];
      $paytype = $row['pay_type'];
    }

    $json_data = array(
      "fullname" => $fullname,
      "paytype" => $paytype,
      "date" => clean_string($from)." - ".clean_string($to),
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );
    return $json_data;

  }

  public function get_deduction_breakdown_sd($emp_id,$from,$to,$type,$frequency, $search = ""){
    $cfrom = $from;
    $cto = $to;
    $emp_id = $this->db->escape($emp_id);

    $requestData = $_REQUEST;

    $columns = array(
			0 => 'sal_date',
      1 => 'reason',
			2 => 'sal_deduct'
		);

    $sql = "SELECT e.description as reason, c.amount as sal_deduct,
              CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
              d.description as pay_type,
              DATE(c.date_created) as sal_date, b.currency, f.exchange_rate as ex_rate
              FROM employee_record a
              INNER JOIN contract b ON a.id = b.contract_emp_id
              LEFT JOIN salary_deduction c ON a.employee_idno = c.employee_idno
              LEFT JOIN paytype d ON b.paytype = d.paytypeid
              LEFT JOIN deduction e ON c.deduct_category = e.deductionid
              LEFT JOIN hris_exchange_rates f ON b.currency = f.currency_code
              WHERE b.contract_status = 'active' AND a.enabled = 1 AND c.status = 'certified'
              AND DATE(c.date_created) BETWEEN $from AND $to AND a.employee_idno = $emp_id
              AND d.paytypeid = $type AND f.enabled = 1";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY sal_date ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    $data = array();

    $fullname = "";
    $paytype = "";

    foreach( $query->result_array() as $row )
    {
      $salary_deduction = ($row['currency'] == "PHP") ? $row['sal_deduct'] : (float)$row['sal_deduct'] / (float)$row['ex_rate'];

      $nestedData=array();

      $nestedData[] = $row['sal_date'];
      $nestedData[] = $row['reason'];
      $nestedData[] = $row['currency'].' '.number_format($salary_deduction, 2);

      $data[] = $nestedData;

      $fullname = $row['fullname'];
      $paytype = $row['pay_type'];
    }

    $json_data = array(
      "fullname" => $fullname,
      "paytype" => $paytype,
      "date" => clean_string($from)." - ".clean_string($to),
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );
    return $json_data;
  }

  public function get_deduction_breakdown_ca($emp_id,$from,$to,$type,$frequency, $search = ""){
    $cfrom = $from;
    $cto = $to;
    $emp_id = $this->db->escape($emp_id);

    $requestData = $_REQUEST;

    $columns = array(
			0 => 'ca_date',
      1 => 'reason',
			2 => 'cashadvance'
		);

    $sql = "SELECT ((c.amount + c.rate) / c.terms) / $frequency as cashadvance,
              CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
              d.description as pay_type, b.currency, e.exchange_rate as ex_rate,
              c.reason as reason, c.date_of_effectivity as ca_date, c.date_end as ca_date2
              FROM employee_record a
              INNER JOIN contract b ON a.id = b.contract_emp_id
              LEFT JOIN cash_advance_tran c ON a.employee_idno = c.employee_id
              LEFT JOIN paytype d ON b.paytype = d.paytypeid
              LEFT JOIN hris_exchange_rates e ON b.currency = e.currency_code
              WHERE b.contract_status = 'active' AND a.enabled = 1 AND c.status = 'certified'
              AND (date_of_effectivity >= date_of_effectivity AND date_of_effectivity <= $to)
              AND date_end >= $to AND a.employee_idno = $emp_id AND d.paytypeid = $type
              AND e.enabled = 1";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY ca_date ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    $data = array();

    $fullname = "";
    $paytype = "";

    foreach( $query->result_array() as $row )
    {
      $cashadvance     = ($row['currency'] == "PHP") ? $row['cashadvance'] : (float)$row['cashadvance'] / (float)$row['ex_rate'];

      $nestedData=array();

      $nestedData[] = $row['ca_date']." - ".$row['ca_date2'];
      $nestedData[] = $row['reason'];
      $nestedData[] = $row['currency'].' '.number_format($cashadvance, 2);

      $data[] = $nestedData;
      $fullname = $row['fullname'];
      $paytype = $row['pay_type'];
    }

    $json_data = array(
      "fullname" => $fullname,
      "paytype" => $paytype,
      "date" => clean_string($from)." - ".clean_string($to),
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );
    return $json_data;
  }

  public function get_ca_pending_deduction($payroll_refno){
    $sql = "SELECT * FROM cashadvance_pending_deduction WHERE payroll_refno = ?
     AND status = 'pending' AND enabled = 1";
    $data = array($payroll_refno);
    return $this->db->query($sql,$data);
  }

  public function get_sss_loan_pending_deduction($payroll_refno){
    $payroll_refno =$this->db->escape($payroll_refno);
    $sql = "SELECT * FROM hris_sss_loan_pending_deduction WHERE enabled = 1 AND status = 'pending'
     AND payroll_refno = $payroll_refno";
    return $this->db->query($sql);
  }

  public function set_deduction_summary($data){
    $this->db->insert('hris_deduction_summary',$data);
    return $this->db->insert_id();
  }

  public function set_deduction_log($data){
    $this->db->insert('hris_deduction_log',$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function set_deduction_log_batch($data){
    $this->db->insert_batch('hris_deduction_log',$data);
    return ($this->db->affected_rows() > 0) ? true :false;
  }

  public function set_cashadvance_payment($data){
    $this->db->insert('cash_advance_pay',$data);
    $inserted = ($this->db->affected_rows() > 0)? true: false;
    if($inserted == true){
      $payment = $this->db->escape($data['ca_payment']);
      $ca_id = $this->db->escape($data['ca_id']);
      $payroll_refno = $this->db->escape($data['payroll_ref_no']);
      $sql = "UPDATE cash_advance_tran
              SET total_balance = (round(total_balance - $payment,2))
              WHERE id = $ca_id";
      $sql2 = "UPDATE cashadvance_pending_deduction
               SET status = 'approved' WHERE payroll_refno = $payroll_refno AND status = 'pending'";
      $this->db->query($sql);
      $this->db->query($sql2);
    }
  }

  public function set_cashadvance_pending_deduction($data){
    $this->db->insert('cashadvance_pending_deduction',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_cashadvance_pending_deduction_batch($data){
    $this->db->insert_batch('cashadvance_pending_deduction',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_sss_loan_pending_deduction_batch($data){
    $this->db->insert_batch('hris_sss_loan_pending_deduction', $data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_pagibig_loan_pending_deduction_batch($data){
    $this->db->insert_batch('hris_pagibig_loan_pending_deduction', $data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_sss_loan_pending_deduction($payroll_refno){
    $sql = "UPDATE hris_sss_loan_pending_deduction SET status = 'approved'
     WHERE status = 'pending' AND enabled = 1 AND payroll_refno = ?";
    $data = array($payroll_refno);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_pagibig_loan_pending_deduction($payroll_refno){
    $sql = "UPDATE hris_pagibig_loan_pending_deduction SET status = 'approved'
     WHERE status = 'pending' AND enabled = 1 AND payroll_refno = ?";
    $data = array($payroll_refno);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  ### Additionals ###
  public function get_additionals_log($company,$from,$to,$type,$frequency,$search = ""){
    $cfrom = $from;
    $cto = $to;
    $company = $this->db->escape($company);
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $type = $this->db->escape($type);
    $frequency = $this->db->escape($frequency);

    $requestData = $_REQUEST;

		$columns = array(
			0 => 'emp_id',
      1 => 'fullname',
			2 => 'additional_pays',
      3 => 'ot_pays'
		);

    $sql = "SELECT a.employee_idno as emp_idnum,
            (SELECT SUM(c.amount) FROM additional_pays c
              WHERE c.employee_id = emp_idnum AND c.status = 'certified'
              AND c.date_issued BETWEEN $from AND $to) as additional_pays,
            (SELECT (CASE WHEN e.frequency >= 4
              THEN ROUND(SUM(b.total_sal / (g.total_whours - g.total_bhours) * 1.25 * (d.minutes_of_overtime / 60)),2)
              ELSE ROUND(SUM(((((b.total_sal * 12) / 313) / (g.total_whours - g.total_bhours)) * 1.25 * (d.minutes_of_overtime / 60))), 2) END)
              FROM overtime_pays d WHERE d.type = 'overtime' AND d.status = 'certified' AND d.employee_id = emp_idnum
              AND date_rendered BETWEEN $from AND $to) as ot_pays,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.description as p_type, b.currency, j.exchange_rate as ex_rate,
            b.contract_ref_no
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN additional_pays c ON a.employee_idno = c.employee_id
            LEFT JOIN overtime_pays d ON a.employee_idno = d.employee_id
            LEFT JOIN paytype e ON b.paytype = e.paytypeid
            LEFT JOIN hris_position f ON b.position_id = f.position_id
            LEFT JOIN work_schedule g ON b.work_sched_id = g.id
            LEFT JOIN work_schedule h ON a.employee_idno = h.emp_id
            INNER JOIN time_record_summary i ON a.employee_idno = i.employee_idno
            LEFT JOIN hris_exchange_rates j ON b.currency = j.currency_code
            WHERE b.contract_status = 'active' AND a.enabled = 1 AND i.absent = 0
                  AND b.paytype = $type AND b.company_id = $company
                  AND j.enabled = 1
                  AND i.date_created BETWEEN $from AND $to GROUP BY a.employee_idno
                  ";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
		// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
		//012819
    $query = $this->db->query($sql);
    $data_main = array();
    foreach( $query->result_array() as $row )
    {
      $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
      $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];
      $nestedData=array();

      $nestedData[] = $row['emp_idnum'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['currency'].' '.number_format($additional_pays, 2);
      $nestedData[] = $row['currency'].' '.number_format($ot_pays, 2);
      $nestedData[] =
      '
        <center>
          <button class="btn btn-sm btn-primary btn_modal_breakdown" style = "width:90px;"
            id = "'.$row['emp_idnum'].'"
            data-fromdate = "'.$from.'"
            data-todate = "'.$to.'"
            data-type = "'.$type.'"
            data-frequency = "'.$frequency.'"
          >
            <i class="fa fa-eye"></i>&nbsp;View
          </button>
        </center>
      ';

      $nestedData[] = $row['contract_ref_no'];
      $nestedData[] = $row['currency'];
      $nestedData[] = $row['ex_rate'];
      $data_main[] = $nestedData;
    }

		$sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
			$data = array();
      $total_add_pay = 0;
      $total_ot_pay = 0;
      $d1 = new Datetime($cfrom);
      $d2 = new Datetime($cto);
      $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
      $p_type = "";

			foreach( $query->result_array() as $row )
			{
        $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
        $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];

				$nestedData=array();

				$nestedData[] = $row['emp_idnum'];
				$nestedData[] = $row['fullname'];
				$nestedData[] = $row['currency'].' '.number_format($additional_pays, 2);
				$nestedData[] = $row['currency'].' '.number_format($ot_pays, 2);
        $nestedData[] =
        '
          <center>
            <button class="btn btn-sm btn-primary btn_modal_breakdown" style = "width:90px;"
              id = "'.$row['emp_idnum'].'"
              data-fromdate = "'.$from.'"
              data-todate = "'.$to.'"
              data-type = "'.$type.'"
              data-frequency = "'.$frequency.'"
            >
              <i class="fa fa-eye"></i>&nbsp;View
            </button>
          </center>
        ';

        $nestedData[] = $row['contract_ref_no'];
        $nestedData[] = $row['currency'];
        $nestedData[] = $row['ex_rate'];
				$data[] = $nestedData;
        $total_add_pay += $row['additional_pays'];
        $total_ot_pay += $row['ot_pays'];
        $p_type = $row['p_type'];
			}

      // $length = $requestData['start'] + $requestData['length'];
      // for ($i=$requestData['start']; $i < $length; $i++) {
      //   if($i < count((array)$data)){
      //     $data_main[] = $data[$i];
      //   }else{
      //     break;
      //   }
      // }

			$json_data = array(
        "date" => $date,
        "p_type" => $p_type,
        "total_add_pays" => number_format($total_add_pay, 2),
        "total_ot_pay" => number_format($total_ot_pay, 2),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data,
        "data_all"        => $data_main
			);
			return $json_data;
  }

  public function get_additionals_breakdown_add($emp_id,$from,$to,$type,$frequency, $search = ""){
    $cfrom = $from;
    $cto = $to;
    $emp_id = $this->db->escape($emp_id);
    // $frequency = (int)$frequency;
    $requestData = $_REQUEST;

    $columns = array(
			0 => 'date',
      1 => 'reason',
			2 => 'amount'
		);

    $user_sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
                c.description as paytype
                FROM employee_record a
                INNER JOIN contract b ON a.id = b.contract_emp_id
                LEFT JOIN paytype c ON b.paytype = c.paytypeid
                WHERE a.employee_idno = $emp_id AND b.paytype = $type";
    $user = $this->db->query($user_sql)->row();

    $sql = "SELECT d.date_issued as add_date, d.purpose as reason, d.amount as add_amount,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            c.description as pay_type, b.currency, e.exchange_rate as ex_rate
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN paytype c ON b.paytype = c.paytypeid
            LEFT JOIN additional_pays d ON a.employee_idno = d.employee_id
            LEFT JOIN hris_exchange_rates e ON b.currency = e.currency_code
            WHERE b.contract_status = 'active' AND a.enabled = 1 AND d.status = 'certified'
            AND c.paytypeid = $type AND a.employee_idno = $emp_id AND e.enabled = 1
            AND d.date_issued BETWEEN $from AND $to";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY add_date ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
    $data = array();

    $fullname = "";
    $paytype = "";

    foreach( $query->result_array() as $row )
    {
      $additional_pays  = ($row['currency'] == "PHP") ? $row['add_amount'] : (float)$row['add_amount'] / (float)$row['ex_rate'];
      $nestedData=array();

      $nestedData[] = $row['add_date'];
      $nestedData[] = $row['reason'];
      $nestedData[] = $row['currency'].' '.number_format($additional_pays, 2);

      $data[] = $nestedData;
    }

    $json_data = array(
      "fullname" => $user->fullname,
      "paytype" => $user->paytype,
      "date" => clean_string($from)." - ".clean_string($to),
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );
    return $json_data;
  }

  public function get_additionals_breakdown_ot($emp_id,$from,$to,$type,$frequency, $search = ""){
    $cfrom = $from;
    $cto = $to;
    $emp_id = $this->db->escape($emp_id);
    // $frequency = (int)$frequency;
    $requestData = $_REQUEST;

    $columns = array(
			0 => 'ot_date',
      1 => 'reason',
			2 => 'ot_min',
      3 => 'amount'
		);

    $user_sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
                c.description as paytype
                FROM employee_record a
                INNER JOIN contract b ON a.id = b.contract_emp_id
                LEFT JOIN paytype c ON b.paytype = c.paytypeid
                WHERE a.employee_idno = $emp_id AND b.paytype = $type";
    $user = $this->db->query($user_sql)->row();

    $sql = "SELECT
            ROUND(SUM(((((b.total_sal * 12) / 313) / 8) * 1.25 * (d.minutes_of_overtime / 60))), 2) as ot_pay,
            DATE(d.date_created) as ot_date, d.purpose as reason, d.minutes_of_overtime as ot_min,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            c.description as pay_type, b.currency, e.exchange_rate as ex_rate
            FROM employee_record a
            INNER JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN paytype c ON b.paytype = c.paytypeid
            LEFT JOIN overtime_pays d ON a.employee_idno = d.employee_id
            LEFT JOIN hris_exchange_rates e ON b.currency = e.currency_code
            WHERE b.contract_status = 'active' AND a.enabled = 1 AND d.type = 'overtime'
            AND d.status = 'certified'
            AND c.paytypeid = $type AND a.employee_idno = $emp_id AND e.enabled = 1
            AND d.date_rendered BETWEEN $from AND $to";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;
			// $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
			//012819
		$sql.=" ORDER BY ot_date ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    // return $this->db->last_query();
    $data = array();

    $fullname = "";
    $paytype = "";

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();


      $nestedData[] = $row['ot_date'];
      $nestedData[] = $row['reason'];
      $nestedData[] = $row['ot_min'];
      $nestedData[] = $row['currency'].' '.number_format($row['ot_pay'], 2);

      if($row['ot_pay'] > 0){
        $data[] = $nestedData;
      }
    }

    $json_data = array(
      "fullname" => $user->fullname,
      "paytype" => $user->paytype,
      "date" => clean_string($from)." - ".clean_string($to),
      "recordsTotal"    => (count((array)$data) > 0) ? intval( $totalData ) : 0,
      "recordsFiltered" => (count((array)$data) > 0) ? intval( $totalFiltered ) : 0,
      "data"            => $data
    );
    return $json_data;

  }

  public function set_additionals_summary($data){
    $this->db->insert('hris_additional_summary',$data);
    return $this->db->insert_id();
  }

  public function set_additionals_log($data){
    $this->db->insert('hris_additional_log',$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function set_additionals_log_batch($data){
    $this->db->insert_batch('hris_additional_log',$data);
    return ($this->db->affected_rows() > 0) ? true :false;
  }

  ### Payroll ###
  public function get_payroll_log($company,$from,$to,$type,$frequency,$pay_day,$search = ""){
    $cfrom = $from;
    $cto = $to;
    $company = $this->db->escape($company);
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $type = $this->db->escape($type);
    $frequency = $this->db->escape($frequency);
    $pay_day = $this->db->escape($pay_day);

    $requestData = $_REQUEST;

    $columns = array(
			0 => 'emp_id',
      1 => 'fullname',
			2 => 'gross_pay',
      3 => 'deduction',
      4 => 'additionals',
      5 => 'net_pay'
		);

    $sql = "SELECT
        CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
        @emp_idnum := a.employee_idno as emp_idnum,
        @daily_rate := (CASE WHEN c.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
        ROUND(@daily_rate / (e.total_whours - e.total_bhours),2) as hourly_rate,
        ROUND(@daily_rate / (e.total_whours - e.total_bhours) / 60,2) as min_rate,
        (SELECT SUM(man_hours) FROM time_record_summary a
          WHERE a.employee_idno = emp_idnum
          AND a.date_created BETWEEN $from AND $to
        ) as man_hours,
        (SELECT SUM(late) FROM time_record_summary a
          WHERE a.employee_idno = emp_idnum
          AND a.date_created BETWEEN $from AND $to
        ) as late,
        (SELECT SUM(undertime) FROM time_record_summary a
          WHERE a.employee_idno = emp_idnum
          AND a.date_created BETWEEN $from AND $to
        ) as undertime,
        (SELECT SUM(g.amount)
         FROM salary_deduction g
         WHERE g.employee_idno = emp_idnum
         AND g.status = 'certified'
         AND DATE(g.date_created)
         BETWEEN $from AND $to
        ) as salary_deduction,
        (SELECT SUM(((f.amount + f.rate) / f.terms) / c.frequency)
         FROM cash_advance_tran f
         WHERE f.employee_id = emp_idnum
         AND f.status = 'certified'
         AND (f.date_of_effectivity >= f.date_of_effectivity AND f.date_of_effectivity <= $to)
         AND f.date_end >= $to
        ) as cash_advance,
        (CASE WHEN k.basic_mo_sal1 > 10000
          THEN (((b.base_pay * 0.0275) / 2) / c.frequency)
          WHEN k.basic_mo_sal1 > 40000 THEN (k.employee_share1 / c.frequency)
          ELSE ((k.employee_share1 / 2) / c.frequency) END) as philhealth,
        (CASE WHEN b.pagibig > 1
        THEN (5000 * (l.employee_share / 100)) / c.frequency
        ELSE (b.base_pay * (l.employee_share / 100)) / c.frequency
        END) as pagibig,
        (ss_ee / c.frequency) as sss,
        (SELECT (ss.monthly_amortization / $frequency) FROM hris_sss_loans ss
          WHERE ss.sss_deduction_start BETWEEN $from AND $to
          AND $pay_day BETWEEN ss.sss_loan_start AND ss.sss_loan_end
          AND ss.status = 'active' AND ss.enabled = 1 AND ss.employee_idno = @emp_idnum
        ) as sss_loan,
        (SELECT (love.monthly_amortization / $frequency) FROM hris_pagibig_loans love
          WHERE love.pagibig_deduction_start BETWEEN $from AND $to
          AND $pay_day BETWEEN love.pagibig_loan_start AND love.pagibig_loan_end
          AND love.status = 'active' AND love.enabled = 1 AND love.employee_idno = @emp_idnum
        ) as pagibig_loan,
        (SELECT
        (CASE WHEN c.frequency >= 4
         THEN ROUND(SUM((b.total_sal / (e.total_whours - e.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60)),2)
         ELSE ROUND(SUM((((daily_rate) / (e.total_whours - e.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60))), 2)
        END)
        FROM overtime_pays m WHERE m.status = 'certified' AND m.employee_id = emp_idnum
        AND date_rendered BETWEEN $from AND $to
        ) as ot_pays,
        (SELECT SUM(m.minutes_of_overtime) FROM overtime_pays m WHERE m.status = 'certified' AND m.employee_id = emp_idnum
        AND m.date_rendered BETWEEN $from AND $to)
        as ot_min,
        (SELECT SUM(n.amount) FROM additional_pays n
         WHERE n.employee_id = emp_idnum AND n.status = 'certified'
         AND n.date_issued BETWEEN $from AND $to
        ) as additional_pays, e.work_sched, f.regular_holiday, f.special_non_working_holiday,
        c.description as pay_type, c.frequency, b.total_sal, (ss_ee * 12) as sss_year_contri,
        (CASE WHEN b.philhealth > 1 THEN ((b.base_pay * 0.0275) / 2) * 12
        ELSE (k.employee_share1 / 2) * 12 END) as philhealth_year_contri,
        (CASE WHEN b.pagibig > 1 THEN (5000 * (l.employee_share / 100)) * 12
        ELSE (b.base_pay * (l.employee_share / 100)) * 12 END) as pagibig_year_contri,
        p.aibLowerLimit as lower_limit, p.aibUpperLimit as upper_limit, p.tr1LowerLimit as tax_lower_limit,
        (p.tr1ExcessLimit / 100) as excess_limit, f.empstatusid as emp_status, q.exchange_rate as ex_rate,
        b.total_sal_converted, b.currency, b.contract_ref_no
        FROM employee_record a
        LEFT JOIN contract b ON a.id = b.contract_emp_id
        LEFT JOIN paytype c ON b.paytype = c.paytypeid
        INNER JOIN time_record_summary d ON a.employee_idno = d.employee_idno
        LEFT JOIN work_schedule e ON b.work_sched_id = e.id
        LEFT JOIN empstatus f ON b.emp_status = f.empstatusid
        LEFT JOIN salary_deduction g ON a.employee_idno = g.employee_idno
        LEFT JOIN work_schedule h ON a.employee_idno = h.emp_idno
        LEFT JOIN cash_advance_tran i ON a.employee_idno = i.employee_id
        LEFT JOIN sss j ON b.sss = j.id
        LEFT JOIN philhealth k ON b.philhealth = k.phID
        LEFT JOIN pagibig l ON b.pagibig = l.id
        LEFT JOIN overtime_pays m ON a.employee_idno = m.employee_id
        LEFT JOIN additional_pays n ON a.employee_idno = n.employee_id
        INNER JOIN time_record_summary o ON a.employee_idno = o.employee_idno
        LEFT JOIN tax p ON b.tax = p.id
        LEFT JOIN hris_exchange_rates q ON b.currency = q.currency_code
        WHERE b.contract_status = 'active' AND a.enabled = 1 AND q.enabled = 1
        AND b.paytype = $type
        AND b.company_id = $company
        AND o.date_created BETWEEN $from AND $to AND o.absent = 0
        GROUP BY a.employee_idno ORDER BY fullname ASC";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    if($search != ""){
			$sql .= $search;
    }

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;

		// $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $data_main = array();

    $d1 = new Datetime($cfrom);
    $d2 = new Datetime($cto);
    $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
    $p_type = "";

    foreach($query->result_array() as $row){
      $sss          = ($row['currency'] == "PHP") ? $row['sss'] : (float)$row['sss'] / (float)$row['ex_rate'];
      $philhealth   = ($row['currency'] == "PHP") ? $row['philhealth'] : (float)$row['philhealth'] / (float)$row['ex_rate'];
      $pagibig      = ($row['currency'] == "PHP") ? $row['pagibig'] : (float)$row['pagibig'] / (float)$row['ex_rate'];
      $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
      $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

      // $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];
      $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
      $cash_advance     = ($row['currency'] == "PHP") ? $row['cash_advance'] : (float)$row['cash_advance'] / (float)$row['ex_rate'];
      $salary_deduction = ($row['currency'] == "PHP") ? $row['salary_deduction'] : (float)$row['salary_deduction'] / (float)$row['ex_rate'];

      $sss_year_contri        = ($row['currency'] == "PHP") ? $row['sss_year_contri'] : (float)$row['sss_year_contri'] / (float)$row['ex_rate'];
      $philhealth_year_contri = ($row['currency'] == "PHP") ? $row['philhealth_year_contri'] : (float)$row['philhealth_year_contri'] / (float)$row['ex_rate'];
      $pagibig_year_contri    = ($row['currency'] == "PHP") ? $row['pagibig_year_contri'] : (float)$row['pagibig_year_contri'] / (float)$row['ex_rate'];

      // $compensation = $row['sss'] + $row['philhealth'] + $row['pagibig'] + $row['sss_loan'] + $row['pagibig_loan'];
      // $additionals = $row['ot_pays'] + $row['additional_pays'];
      // $deductions = $row['cash_advance'] + $row['salary_deduction'];

      $additionals = $row['ot_pays'] + $additional_pays;
      $compensation = $sss + $philhealth + $pagibig + $sss_loan + $pagibig_loan;
      $deductions = $cash_advance + $salary_deduction;
      $manhours = $row['man_hours'];

      $daily_rate = $row['daily_rate'];
      $hourly_rate = $row['hourly_rate'];
      $min_rate = $row['min_rate'];

      $emp_idnum = $this->db->escape($row['emp_idnum']);
      $emp_status = $this->db->escape($row['emp_status']);
      $gross_pay = ($row['frequency'] > 2) ? 0 : $row['total_sal'] / $row['frequency'];
      $gross_pay_raw = ($row['frequency'] > 2) ? 0 : $row['total_sal'] / $row['frequency'];
      $taxable_income = ($row['total_sal']) - ($sss_year_contri + $philhealth_year_contri + $pagibig_year_contri);
      $total_tax = round((($taxable_income - $row['lower_limit']) * $row['excess_limit']) + $row['tax_lower_limit'],2) / 12 / $row['frequency'];
      $total_tax = ($row['currency'] == "PHP") ? $total_tax : $total_tax / (float)$row['ex_rate'];

      $cut_off_days = 0;
      $wdays = 0;
      $absent = 0;
      $absent_deduction = 0;
      $late_deduct = $min_rate * $row['late'];
      $ut_deduct = $min_rate * $row['undertime'];
      $total_deduct = $compensation + $deductions + $late_deduct + $ut_deduct;
      $total_deduct2 = $compensation + $deductions;

      $reg_holiday = 0;
      $reg_holiday_pay = 0;
      $spl_holiday = 0;
      $spl_holiday_pay = 0;
      $sunday = 0;
      $sunday_pay = 0;

      $sdate = new Datetime($cfrom);
      $edate = new Datetime($cto);

      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');

      for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
        $ldate_raw = $x->format('Y-m-d');
        $ldate = $this->db->escape($ldate_raw);

        $sql2 = "SELECT c.man_hours, e.regular_holiday, e.special_non_working_holiday,
                c.date_created as date, e.empstatusid as employee_type
                FROM employee_record a
                LEFT JOIN contract b ON a.id = b.contract_emp_id
                LEFT JOIN time_record_summary c ON a.employee_idno = c.employee_idno
                LEFT JOIN empstatus e ON b.emp_status = e.empstatusid
                WHERE c.date_created = $ldate AND a.employee_idno = $emp_idnum
                AND b.contract_status = 'active' AND a.enabled = 1 AND c.absent = 0
                -- UNION
                -- SELECT TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600 as man_hours,
                -- d.regular_holiday, d.special_non_working_holiday, c.date as date
                -- FROM employee_record a
                -- LEFT JOIN contract b ON a.id = b.contract_emp_id
                -- LEFT JOIN work_order c ON a.employee_idno = c.employee_id
                -- LEFT JOIN empstatus d ON b.emp_status = d.empstatusid
                -- WHERE c.date = $ldate AND a.employee_idno = $emp_idnum AND c.enabled = 1
                -- AND b.contract_status = 'active' AND a.enabled = 1 AND c.status = 'certified'";

        $sql3 = "SELECT a.date, b.holidaytypeid as holiday_type, payratio, payratio2
                FROM holidays_tran a
                LEFT JOIN holidaytype b ON a.holiday_type = b.holidaytypeid
                WHERE a.date = $ldate AND a.enabled = 1";

        $query2 = $this->db->query($sql2);
        $query3 = $this->db->query($sql3);

        ### cut off days ###
        $d_main = new Datetime($ldate_raw);
        $wdate_main = strtolower($d_main->format('D'));
        for ($a=0; $a < 7; $a++) {
          if($wdate_main == $days[$a]){
            if($worksched[$days[$a]][0] != ""){
              $cut_off_days += 1;
            }
          }
        }

        ### if the day is holiday and has man_hours
        if($query2->num_rows() > 0 && $query3->num_rows() > 0){
          $timelog = $query2->row();
          $holiday = $query3->row();
          $d = new Datetime($timelog->date);
          $wdate = strtolower($d->format('D'));

          if($timelog->man_hours > 0){
            ### regular holiday
            if($holiday->holiday_type == 1){
              $reg_holiday += 1;

              if($timelog->regular_holiday == 'yes'){
                for ($i=0; $i < 7; $i++) {
                  if($wdate == $days[$i]){
                    ### weekly ###
                    if($row['frequency'] > 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * $holiday->payratio;
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio;
                        $gross_pay += $hourly_rate * $holiday->payratio *  $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * $holiday->payratio * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * $holiday->payratio2;
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio2;

                        $gross_pay += $hourly_rate * $holiday->payratio2 *  $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * $holiday->payratio2 * $timelog->man_hours;
                      }
                    }
                    ### monthly || semi-monthly ###
                    if($row['frequency'] <= 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * ($holiday->payratio / 2);
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio;

                        $gross_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * ($holiday->payratio2 / 2);
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio2;
                        $gross_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                      }
                    }
                  }
                }
              }
            }

            ### special non working holiday
            if($holiday->holiday_type == 2){
              $spl_holiday += 1;

              if($timelog->special_non_working_holiday == 'yes'){
                for ($i=0; $i < 7; $i++) {
                  if($wdate == $days[$i]){

                    if($row['frequency'] > 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * $holiday->payratio;
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio;

                        $gross_pay += $hourly_rate * $holiday->payratio *  $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * $holiday->payratio * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * $holiday->payratio2;
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio2;

                        $gross_pay += $hourly_rate * $holiday->payratio2 *  $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * $holiday->payratio2 * $timelog->man_hours;
                      }
                    }

                    if($row['frequency'] <= 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * ($holiday->payratio / 2);
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio;

                        $gross_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * ($holiday->payratio2 / 2);
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio2;

                        $gross_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                      }
                    }

                  }
                }
              }
            }

          }else{
            if($holiday->holiday_type == 1){
              $reg_holiday += 1;
              if($timelog->regular_holiday == 'yes'){
                $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                $reg_holiday_pay += $daily_rate;
              }
            }

            if($holiday->holiday_type == 2){
              $spl_holiday += 1;
              if($timelog->regular_holiday == 'yes'){
                $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                $spl_holiday_pay += $daily_rate;
              }
            }
          }

        }

        ### if has man_hours only
        if($query2->num_rows() > 0 && $query3->num_rows() == 0){
          $timelog = $query2->row();
          $d = new Datetime($timelog->date);
          $wdate = strtolower($d->format('D'));
          // $g[] = 3;
          // $g[] = $timelog->date;
          for ($i=0; $i < 7; $i++) {
            if($wdate == $days[$i]){
              if($worksched[$days[$i]][0] != ""){
                if($row['frequency'] > 2){
                  $gross_pay += $daily_rate;
                }
                $wdays += 1;
              }else{ // for sunday or off day
                $gross_pay += ($row['frequency'] > 2)
                ? $hourly_rate * 1.3 * $timelog->man_hours // daily rate
                : $hourly_rate * .3 * $timelog->man_hours; // semi-monthly

                if($wdate == 'sun'){
                  $sunday += 1;
                  $sunday_pay += ($row['frequency'] > 2)
                  ? $hourly_rate * 1.3 * $timelog->man_hours // daily rate
                  : $hourly_rate * .3 * $timelog->man_hours; // semi-monthly
                }
              }
            }
          }
        }

        ### holiday only ###
        if($query3->num_rows() > 0 && $query2->num_rows() == 0){
          $holiday = $query3->row();
          if($holiday->holiday_type == 1){
            $reg_holiday += 1;
            if($row['regular_holiday'] == 'yes'){
              $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              $reg_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              // $reg_holiday_pay += $daily_rate;
            }
          }

          if($holiday->holiday_type == 2){
            $spl_holiday += 1;
            if($row['special_non_working_holiday'] == 'yes'){
              $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              $spl_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              // $spl_holiday_pay += $daily_rate;
            }
          }
        }

        ### absent
        if($query2->num_rows() == 0 && $query3->num_rows() == 0){
          $d = new Datetime($ldate_raw);
          $wdate = strtolower($d->format('D'));
          for ($i=0; $i < 7; $i++) {
            if($wdate == $days[$i]){
              if($worksched[$days[$i]][0] != ""){
                $leave_sql = "SELECT * FROM leave_tran WHERE employee_idno = $emp_idnum
                              AND $ldate BETWEEN date_from AND date_to
                              AND status = 'certified' AND enabled = 1";
                $check_leave = $this->db->query($leave_sql);
                ### CHECK EMPLOYEE STATUS AND LEAVE ###
                if($emp_status == regular_employee()){
                  if($check_leave->num_rows() == 0){ // NO LEAVE
                    if($row['frequency'] <= 2){
                      $gross_pay -= $daily_rate;
                      $absent_deduction += $daily_rate;
                    }
                    $absent += 1;
                  }else{ // HAS LEAVE
                    if($row['frequency'] > 2){
                      $gross_pay += $daily_rate;
                    }
                    $wdays += 1;
                  }
                }else{
                  if($check_leave->num_rows() == 0){ // NO LEAVE
                    if($row['frequency'] <= 2){
                      $gross_pay -= $daily_rate;
                      $absent_deduction += $daily_rate;
                    }
                    $absent += 1;
                  }else{ // HAS LEAVE
                    $wdays += 1;
                  }
                }
              }
            }
          }
        }

      }

      // $gross_pay = $gross_pay - $late_deduct - $ut_deduct;
      $net_pay = ($gross_pay + $additionals) - $total_deduct;
      $gross_pay_less = ($gross_pay - $late_deduct) - $ut_deduct;

      $nestedData = array();
      $nestedData[] = $row['emp_idnum'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['currency'].' '.number_format($gross_pay_less,2);
      $nestedData[] = $row['currency'].' '.number_format($additionals,2);
      $nestedData[] = $row['currency'].' '.number_format($total_deduct,2);
      $nestedData[] = $row['currency'].' '.number_format($net_pay,2);
      ### semi monthly || weekly ###
      $gross_pay = ($row['frequency'] > 2) ? number_format($gross_pay,2) : number_format($gross_pay_raw,2);
      $nestedData[] =
      '
      <center>
        <button class="btn btn-sm btn-primary payroll_breakdown" style = "width:90px;"
          data-emp_idno = "'.$row['emp_idnum'].'"
          data-fullname = "'.$row['fullname'].'"
          data-date = "'.$date.'"
          data-paytype = "'.$row['pay_type'].'"
          data-wdays = "'.number_format($cut_off_days,2).'"
          data-gross_pay = "'.$gross_pay.'"
          data-gross_pay_less = "'.number_format($gross_pay_less,2).'"
          data-reg_holiday = "'.number_format($reg_holiday,2).'"
          data-reg_holiday_pay = "'.number_format($reg_holiday_pay,2).'"
          data-spl_holiday = "'.number_format($spl_holiday,2).'"
          data-spl_holiday_pay = "'.number_format($spl_holiday_pay,2).'"
          data-spl_holiday_pay = "'.number_format($spl_holiday_pay,2).'"
          data-sunday = "'.number_format($sunday,2).'"
          data-sunday_pay = "'.number_format($sunday_pay,2).'"
          data-absent = "'.number_format($absent,2).'"
          data-absent_deduction = "'.number_format($absent_deduction,2).'"
          data-late = "'.number_format($row['late']).'"
          data-late_deduct = "'.number_format(($late_deduct),2).'"
          data-ut = "'.number_format($row['undertime']).'"
          data-ut_deduct = "'.number_format($ut_deduct,2).'"
          data-total_deduct = "'.number_format($total_deduct2,2).'"
          data-sss = "'.number_format($sss,2).'"
          data-sss_loan = "'.number_format($sss_loan,2).'"
          data-pagibig_loan = "'.number_format($pagibig_loan,2).'"
          data-philhealth = "'.number_format($philhealth,2).'"
          data-pagibig = "'.number_format($pagibig,2).'"
          data-cashadvance = "'.number_format($cash_advance,2).'"
          data-sal_deduct = "'.number_format($salary_deduction,2).'"
          data-add_pay = "'.number_format($additional_pays,2).'"
          data-ot_min = "'.number_format($row['ot_min']).'"
          data-ot_pay = "'.number_format($row['ot_pays'],2).'"
          data-net_pay = "'.number_format($net_pay,2).'"
          data-currency = "'.$row['currency'].'"
          data-ex_rate = "'.$row['ex_rate'].'"
        >
          <i class="fa fa-eye"></i>&nbsp;View
        </button>
      </center>
      ';
      $nestedData[] = $row['contract_ref_no'];
      $nestedData[] = $row['currency'];
      $nestedData[] = $row['ex_rate'];
      $data[] = $nestedData;
      $p_type = $row['pay_type'];
    }

    // $length = $requestData['start'] + $requestData['length'];
    // for ($i=$requestData['start']; $i < $length; $i++) {
    //   if($i < count((array)$data)){
    //     $data_main[] = $data[$i];
    //   }else{
    //     break;
    //   }
    // }

    $json_data = array(
      "date" => $date,
      "p_type" => $p_type,
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data,
      "data_all"        => $data
    );
    return $json_data;
  }

  public function get_payroll_breakdown($emp_id,$from,$to,$type,$frequency,$dduct,$add,$search = ""){
    $cfrom = $from;
    $cto = $to;
    $emp_id = $this->db->escape($emp_id);
    $frequency = (int)$frequency;
    $requestData = $_REQUEST;

    $columns = array(
			0 => 'date',
      1 => 'reason',
			2 => 'amount'
		);

    $totalData = 0;
		$totalFiltered = 0;

    $sdate = new Datetime($cfrom);
    $edate = new Datetime($cto);

    $user_sql = "SELECT a.employee_idno as emp_idnum,
      CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      @daily_rate := (CASE WHEN c.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
      ROUND(@daily_rate / (d.total_whours - d.total_bhours),2) as hourly_rate,
      ROUND(@daily_rate / (d.total_whours - d.total_bhours) / 60,2) as min_rate,
      d.total_whours, d.total_bhours, b.total_sal, d.work_sched,
      e.regular_holiday as reg_holiday, c.description as paytype, c.frequency,
      e.special_non_working_holiday as spec_holiday,
      (SELECT SUM(((f.amount + f.rate) / f.terms) / c.frequency)
      FROM cash_advance_tran f
      WHERE f.employee_id = emp_idnum
      AND f.status = 'certified'
      AND (f.date_of_effectivity >= f.date_of_effectivity AND f.date_of_effectivity <= $to)
      AND f.date_end >= $to
      ) as cash_advance,
      (CASE WHEN k.basic_mo_sal1 > 10000
        THEN (((b.total_sal * 0.0275) / 2) / c.frequency)
        WHEN k.basic_mo_sal1 > 40000 THEN (k.employee_share1 / c.frequency)
        ELSE ((k.employee_share1 / 2) / c.frequency) END) as philhealth,
      (CASE WHEN b.pagibig > 1
      THEN (5000 * (l.employee_share / 100)) / c.frequency
      ELSE (b.total_sal * (l.employee_share / 100)) / c.frequency
      END) as pagibig,
      (ss_ee / c.frequency) as sss
      FROM employee_record a
      LEFT JOIN contract b ON a.id = b.contract_emp_id
      LEFT JOIN paytype c ON b.paytype = c.paytypeid
      LEFT JOIN work_schedule d ON b.work_sched_id = d.id
      LEFT JOIN empstatus e ON b.emp_status = e.empstatusid
      LEFT JOIN cash_advance_tran i ON a.employee_idno = i.employee_id
      LEFT JOIN sss j ON b.sss = j.id
      LEFT JOIN philhealth k ON b.philhealth = k.phID
      LEFT JOIN pagibig l ON b.pagibig = l.id
      WHERE a.employee_idno = $emp_id AND b.contract_status = 'active' AND a.enabled = 1
      AND c.paytypeid = $type";

    $user = $this->db->query($user_sql)->row();
    $worksched = json_decode($user->work_sched);
    $worksched = (array)$worksched;
    $days = array('mon','tue','wed','thu','fri','sat','sun');

    $total_gross = 0;
    // $total_gross = array();
    $total_late = 0;
    $total_undertime = 0;
    $total_net = 0;
    $compensation = round($user->sss + $user->philhealth + $user->pagibig,2);
    $cashadvance = round($user->cash_advance,2);
    $data = array();

    for ($a = $sdate; $a <= $edate ; $a->modify('+1 day')) {
      $date_clean = $a->format('Y-m-d');
      $ldate = $this->db->escape($date_clean);

      $sql = "SELECT a.employee_idno as emp_idnum,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            d.work_sched, e.time_in, e.time_out, e.date_created as date, e.man_hours,
            @daily_rate := (CASE WHEN c.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
            ROUND(@daily_rate / (d.total_whours - d.total_bhours),2) as hourly_rate,
            ROUND(@daily_rate / (d.total_whours - d.total_bhours) / 60,2) as min_rate,
            e.late, e.overtime, e.undertime,
            d.total_whours, d.total_bhours,
            b.total_sal, f.regular_holiday as reg_holiday,
            f.special_non_working_holiday as spec_holiday,
            c.frequency,
            (SELECT SUM(g.amount) FROM salary_deduction g WHERE g.employee_idno = emp_idnum
             AND g.status = 'certified' AND DATE(g.date_created) = $ldate
            ) as sal_deduct,
            (SELECT
            (CASE WHEN c.frequency >= 4
             THEN ROUND(SUM((b.total_sal / (d.total_whours - d.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60)),2)
             ELSE ROUND(SUM((((daily_rate) / (d.total_whours - d.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60))), 2)
            END)
            FROM overtime_pays m WHERE m.status = 'certified' AND m.employee_id = emp_idnum
            AND date_rendered = $ldate
            ) as ot_pays,
            (SELECT SUM(n.amount) FROM additional_pays n
            WHERE n.employee_id = emp_idnum AND n.status = 'certified'
            AND n.date_issued = $ldate
            ) as additional_pays
            FROM employee_record a
            LEFT JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN paytype c ON b.paytype = c.paytypeid
            LEFT JOIN work_schedule d ON b.work_sched_id = d.id
            LEFT JOIN time_record_summary e ON a.employee_idno = e.employee_idno
            LEFT JOIN empstatus f ON b.emp_status = f.empstatusid
            LEFT JOIN salary_deduction g ON a.employee_idno = g.employee_idno
            LEFT JOIN work_schedule h ON a.employee_idno = h.emp_idno
            LEFT JOIN overtime_pays m ON a.employee_idno = m.employee_id
            LEFT JOIN additional_pays n ON a.employee_idno = n.employee_id
            WHERE b.contract_status = 'active' AND a.enabled = 1
            AND e.date_created = $ldate AND c.paytypeid = $type
            AND a.employee_idno = $emp_id";

      // $sql2 = "SELECT a.employee_idno as emp_idnum,
      //       CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
      //       @daily_rate := (CASE WHEN c.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
      //       ROUND(@daily_rate / (d.total_whours - d.total_bhours),2) as hourly_rate,
      //       ROUND(@daily_rate / (d.total_whours - d.total_bhours) / 60,2) as min_rate,
      //       d.work_sched, e.start_time as time_in, e.end_time as time_out, e.date,
      //       TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600 as man_hours,
      //       d.total_whours, d.total_bhours, b.total_sal, f.regular_holiday as reg_holiday,
      //       f.special_non_working_holiday as spec_holiday,
      //       c.frequency,
      //       (SELECT SUM(g.amount) FROM salary_deduction g WHERE g.employee_idno = emp_idnum
      //        AND g.status = 'certified' AND DATE(g.date_created) = $ldate
      //       ) as sal_deduct,
      //       (SELECT
      //       (CASE WHEN c.frequency >= 4
      //        THEN ROUND(SUM((b.total_sal / (d.total_whours - d.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60)),2)
      //        ELSE ROUND(SUM((((daily_rate) / (d.total_whours - d.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60))), 2)
      //       END)
      //       FROM overtime_pays m WHERE m.status = 'certified' AND m.employee_id = emp_idnum
      //       AND DATE(date_created) = $ldate
      //       ) as ot_pays,
      //       (SELECT SUM(n.amount) FROM additional_pays n
      //       WHERE n.employee_id = emp_idnum AND n.status = 'certified'
      //       AND n.date_issued = $ldate
      //       ) as additional_pays
      //       FROM employee_record a
      //       LEFT JOIN contract b ON a.id = b.contract_emp_id
      //       LEFT JOIN paytype c ON b.paytype = c.paytypeid
      //       LEFT JOIN work_schedule d ON b.work_sched_id = d.id
      //       INNER JOIN work_order e ON a.employee_idno = e.employee_id
      //       LEFT JOIN empstatus f ON b.emp_status = f.empstatusid
      //       LEFT JOIN salary_deduction g ON a.employee_idno = g.employee_idno
      //       LEFT JOIN work_schedule h ON a.employee_idno = h.emp_idno
      //       LEFT JOIN overtime_pays m ON a.employee_idno = m.employee_id
      //       LEFT JOIN additional_pays n ON a.employee_idno = n.employee_id
      //       WHERE b.contract_status = 'active' AND a.enabled = 1 AND e.status = 'certified'
      //       AND e.date = $ldate AND c.paytypeid = $type
      //       AND a.employee_idno = $emp_id";

      $holiday_sql = "SELECT
            a.date, b.payratio, b.payratio2, b.description as holiday, b.holidaytypeid as h_type
            FROM holidays_tran a
            LEFT JOIN holidaytype b ON a.holiday_type = b.holidaytypeid
            WHERE a.date = $ldate AND a.enabled = 1";

      $query1 = $this->db->query($sql);
      // $query2= $this->db->query($sql2);
      $query3 = $this->db->query($holiday_sql);

      // $totalData += $timelog->num_rows() + $holiday->num_rows();
      // $totalFiltered += $timelog->num_rows() + $holiday->num_rows();

      $nestedData = array();
      $late = 0;
      $undertime = 0;
      $absent = 0;
      // $gross_pay = ($user->frequency > 2) ? 0 : $user->total_sal / $user->frequency;
      $gross_pay = 0;
      $netpay = 0;
      $additional = 0;
      $deduction = 0;

      ### if has workorder and holiday
      // if($query2->num_rows() > 0 && $query3->num_rows() > 0){
      //   $workorder = $query2->row();
      //   $holiday = $query3->row();
      //   $d = new Datetime($workorder->date);
      //   $wdate = strtolower($d->format('D'));
      //
      //
      //   if($workorder->man_hours > 0){
      //     if($holiday->h_type == 1){
      //       if($workorder->reg_holiday == 'yes'){
      //         for ($i=0; $i < 7; $i++) {
      //           if($wdate == $days[$i]){
      //             $earnings = ($worksched[$days[$i]][0] != "")
      //               ? round($workorder->daily_rate * $holiday->payratio,2)
      //               : round($workorder->daily_rate * $holiday->payratio2,2);
      //           }
      //         }
      //       }
      //     }
      //
      //     if($holiday->h_type == 2){
      //       if($workorder->spec_holiday == 'yes'){
      //         for ($i=0; $i < 7; $i++) {
      //           if($wdate == $days[$i]){
      //             $earnings = ($worksched[$days[$i]][0] != "")
      //               ? round($workorder->daily_rate * $holiday->payratio,2)
      //               : round($workorder->daily_rate * $holiday->payratio2,2);
      //           }
      //         }
      //       }
      //     }
      //
      //
      //     $total_gross += $earnings;
      //     $netpay = $earnings;
      //     $total_net += $netpay;
      //
      //     $nestedData[] = $workorder->date;
      //     $nestedData[] = $workorder->time_in." to ".$workorder->time_out;
      //     $nestedData[] = round($workorder->man_hours,2);
      //     $nestedData[] = $holiday->holiday;
      //     $nestedData[] = number_format($earnings,2);
      //     $nestedData[] = number_format(0,2);
      //     $nestedData[] = number_format(0,2);
      //     $nestedData[] = number_format($netpay,2);
      //
      //   }else{
      //     $d = new Datetime($holiday->date);
      //     $wdate = strtolower($d->format('D'));
      //     for ($i=0; $i < 7; $i++) {
      //       if($wdate == $days[$i]){
      //         if($worksched[$days[$i]][0] != ""){
      //           $nestedData[] = $holiday->date;
      //           $nestedData[] = '--:-- to --:--';
      //           $nestedData[] = 0;
      //           $nestedData[] = $holiday->holiday;
      //           $nestedData[] = $user->daily_rate;
      //           $nestedData[] = number_format(0,2);
      //           $nestedData[] = number_format(0,2);
      //           $nestedData[] = number_format($user->daily_rate,2);
      //         }
      //       }
      //     }
      //   }
      //
      // }

      ### if has workorder only
      // if($query2->num_rows() > 0 && $query3->num_rows() == 0){
      //   $workorder = $query2->row();
      //   $daily_rate = $workorder->daily_rate;
      //
      //       $gross_pay = $daily_rate;
      //       $netpay = $gross_pay;
      //
      //       $total_gross += $gross_pay;
      //       $total_net += $netpay;
      //
      //       $nestedData[] = $workorder->date;
      //       $nestedData[] = $workorder->time_in." to ".$workorder->time_out;
      //       $nestedData[] = $workorder->man_hours;
      //       $nestedData[] = 'Regular Day';
      //       $nestedData[] = number_format($gross_pay,2);
      //       $nestedData[] = number_format($late,2);
      //       $nestedData[] = number_format($undertime,2);
      //       $nestedData[] = number_format($netpay,2);
      // }

      ### if has timelog and holiday
      if($query1->num_rows() > 0 && $query3->num_rows() > 0){
        $timelog = $query1->row();
        $holiday = $query3->row();
        $d = new Datetime($timelog->date);
        $wdate = strtolower($d->format('D'));


        if($timelog->man_hours > 0){
          if($holiday->h_type == 1){
            if($timelog->reg_holiday == 'yes'){
              for ($i=0; $i < 7; $i++) {
                if($wdate == $days[$i]){

                  $gross_pay = ($worksched[$days[$i]][0] != "")
                    ? round($timelog->daily_rate * $holiday->payratio,2)
                    : round($timelog->daily_rate * $holiday->payratio2,2);

                  if($worksched[$days[$i]][0] != ""){
                    $late = round($timelog->min_rate * $timelog->late,2);
                    $undertime = round($timelog->min_rate * $timelog->undertime,2);
                    $additional = round($timelog->ot_pays + $timelog->additional_pays,2);
                    $deduction = round($timelog->sal_deduct + $late + $undertime,2);
                    $netpay = $gross_pay;

                    $total_gross += $gross_pay;
                    $total_late += $late;
                    $total_undertime += $undertime;
                    $total_net += $netpay;

                    $nestedData[] = $timelog->date;
                    $nestedData[] = $timelog->time_in." to ".$timelog->time_out;
                    $nestedData[] = $timelog->man_hours;
                    $nestedData[] = $holiday->holiday;
                    $nestedData[] = number_format($gross_pay,2);
                    $nestedData[] = number_format($additional,2);
                    $nestedData[] = number_format($deduction,2);
                    $nestedData[] = number_format($netpay,2);
                  }

                }
              }
            }
          }

          if($holiday->h_type == 2){
            if($timelog->spec_holiday == 'yes'){
              for ($i=0; $i < 7; $i++) {
                if($wdate == $days[$i]){

                  $gross_pay = ($worksched[$days[$i]][0] != "")
                    ? round($timelog->daily_rate * $holiday->payratio,2)
                    : round($timelog->daily_rate * $holiday->payratio2,2);

                  if($worksched[$days[$i]][0] != ""){
                    $late = round($timelog->min_rate * $timelog->late,2);
                    $undertime = round($timelog->min_rate * $timelog->undertime,2);
                    $additional = round($timelog->ot_pays + $timelog->additional_pays,2);
                    $deduction = round($timelog->sal_deduct + $late + $undertime,2);
                    $netpay = $gross_pay;

                    $total_gross += $gross_pay;
                    $total_late += $late;
                    $total_undertime += $undertime;
                    $total_net += $netpay;

                    $nestedData[] = $timelog->date;
                    $nestedData[] = $timelog->time_in." to ".$timelog->time_out;
                    $nestedData[] = $timelog->man_hours;
                    $nestedData[] = $holiday->holiday;
                    $nestedData[] = number_format($gross_pay,2);
                    $nestedData[] = number_format($additional,2);
                    $nestedData[] = number_format($deduction,2);
                    $nestedData[] = number_format($netpay,2);
                  }

                }
              }
            }
          }

        }else{

          $d = new Datetime($holiday->date);
          $wdate = strtolower($d->format('D'));
          for ($i=0; $i < 7; $i++) {
            if($wdate == $days[$i]){
              if($worksched[$days[$i]][0] != ""){
                $nestedData[] = $holiday->date;
                $nestedData[] = '--:-- to --:--';
                $nestedData[] = 0;
                $nestedData[] = $holiday->holiday;
                $nestedData[] = $user->daily_rate;
                $nestedData[] = number_format(0,2);
                $nestedData[] = number_format(0,2);
                $nestedData[] = number_format($user->daily_rate,2);
              }
            }
          }

        }
      }

      ### if has man_hours only
      if($query1->num_rows() > 0 && $query3->num_rows() == 0){
        $timelog = $query1->row();
        $d = new Datetime($timelog->date);
        $wdate = strtolower($d->format('D'));
        $daily_rate = $timelog->daily_rate;
        for ($i=0; $i < 7; $i++) {
          if($wdate == $days[$i]){
            if($worksched[$days[$i]][0] != ""){
              $gross_pay = $daily_rate;

              $late = $timelog->min_rate * $timelog->late;
              $undertime = $timelog->min_rate * $timelog->undertime;
              $additional = round($timelog->ot_pays + $timelog->additional_pays,2);
              $deduction = round($timelog->sal_deduct + $late + $undertime,2);
              $netpay = $gross_pay;

              $total_gross += $gross_pay;
              $total_late += $late;
              $total_undertime += $undertime;
              $total_net += $netpay;

              $nestedData[] = $timelog->date;
              $nestedData[] = $timelog->time_in." to ".$timelog->time_out;
              $nestedData[] = $timelog->man_hours;
              $nestedData[] = 'Regular Day';
              $nestedData[] = number_format($gross_pay,2);
              $nestedData[] = number_format($additional,2);
              $nestedData[] = number_format($deduction,2);
              $nestedData[] = number_format($netpay,2);
            }

          }
        }
      }

      ### if holiday only
      if($query3->num_rows() > 0 && $query1->num_rows() == 0){
        $holiday = $query3->row();
        if($holiday->h_type == 1){
          if($user->reg_holiday == 'yes'){
            $gross_pay = $user->daily_rate;
            $netpay = $user->daily_rate;
            $total_gross += $gross_pay;
            $total_net += $netpay;

            $nestedData[] = $holiday->date;
            $nestedData[] = '--:-- to --:--';
            $nestedData[] = 0;
            $nestedData[] = $holiday->holiday;
            $nestedData[] = number_format($gross_pay,2);
            $nestedData[] = number_format(0,2);
            $nestedData[] = number_format(0,2);
            $nestedData[] = number_format($netpay,2);
          }
        }

        if($holiday->h_type == 2){
          if($user->spec_holiday == 'yes'){
            $gross_pay = $user->daily_rate;
            $netpay = $user->daily_rate;
            $total_gross += $gross_pay;
            $total_net += $netpay;

            $nestedData[] = $holiday->date;
            $nestedData[] = '--:-- to --:--';
            $nestedData[] = 0;
            $nestedData[] = $holiday->holiday;
            $nestedData[] = number_format($gross_pay,2);
            $nestedData[] = number_format(0,2);
            $nestedData[] = number_format(0,2);
            $nestedData[] = number_format($netpay,2);
          }
        }

      }

      if($a == (int)$edate[2]){
        if(count((array)$nestedData) > 0){
          $nestedData[6] = $compensation + $cashadvance;
        }else{
          $nestedData[] = $ldate;
          $nestedData[] = '--:-- to --:--';
          $nestedData[] = 0;
          $nestedData[] = '';
          $nestedData[] = number_format(0,2);
          $nestedData[] = number_format(0,2);
          $nestedData[] = number_format($compensation + $cashadvance,2);
          $nestedData[] = number_format(0,2);
        }
        $data[] = $nestedData;
      }else{
        if(count((array)$nestedData) > 0){
          $data[] = $nestedData;
        }
      }
    }
    $totalData = count((array)$data);
    $totalFiltered = count((array)$data);
    $total_net = ($total_net + $add) - $dduct;

    $json_data = array(
      "fullname" => $user->fullname,
      "grosspay" => number_format(round($total_gross,2),2),
      // "grosspay" => $total_gross,
      "late" => number_format(round($late,2)),
      "undertime" => number_format(round($undertime,2)),
      "netpay" => number_format(round($total_net,2),2),
      "add" => number_format(round($add,2),2),
      "dduct" => number_format(round($dduct,2),2),
      "date" => $cfrom." - ". $cto,
      "paytype" => $user->paytype,
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );
    return $json_data;
  }

  public function set_payroll_summary($data){
    $this->db->insert('hris_payroll_summary',$data);
    return $this->db->insert_id();
  }

  public function set_payroll_log($data){
    $this->db->insert('hris_payroll_log',$data);
    return ($this->db->affected_rows() > 0)? true: false;
  }

  public function set_payroll_log_batch($data){
    $this->db->insert_batch('hris_payroll_log',$data);
    return ($this->db->affected_rows() > 0) ? true :false;
  }

  public function check_payroll_summary($data){
    $sql = "SELECT *
            FROM hris_payroll_summary
            WHERE paytype = ? AND
            ((? BETWEEN fromdate AND todate) OR (? BETWEEN fromdate AND todate)) AND enabled = 1
            AND company_id = ?";
    return $this->db->query($sql,$data);
  }

  public function check_payroll_ref_no($ref_no){
    $sql = "SELECT ref_no FROM hris_payroll_summary WHERE ref_no = ?";
    $data = array($ref_no);
    return $this->db->query($sql,$data);
  }

  ### tagging ###

  public function tag_additional_on_payroll($tag_data){
    $tag_sql = "UPDATE additional_pays a
      INNER JOIN hris_additional_log b ON a.employee_id = b.emp_id
      INNER JOIN hris_additional_summary c ON b.additional_summary_id = c.id
      INNER JOIN hris_payroll_summary d ON c.id = d.additional_id
      SET payroll_ref_no = d.ref_no
      WHERE a.date_issued BETWEEN d.fromdate AND d.todate
      AND d.status = 'approved'
      AND a.payroll_ref_no = 'none'
      AND a.status = 'certified'
      AND a.enabled = 1 AND d.id = ?";
    $this->db->query($tag_sql,$tag_data);

  }

  public function tag_overtime_on_payroll($tag_data){
    $tag_sql2 = "UPDATE overtime_pays a
      INNER JOIN hris_additional_log b ON a.employee_id = b.emp_id
      INNER JOIN hris_additional_summary c ON b.additional_summary_id = c.id
      INNER JOIN hris_payroll_summary d ON c.id = d.additional_id
      SET payroll_ref_no = d.ref_no
      WHERE a.date_rendered BETWEEN d.fromdate AND d.todate
      AND d.status = 'approved'
      AND a.payroll_ref_no = 'none'
      AND a.status = 'certified'
      AND a.type = 'overtime'
      AND a.enabled = 1";
    $this->db->query($tag_sql2,$tag_data);
  }

  public function tag_salary_deduction_on_payroll($tag_data){
    $tag_sql3 = "UPDATE salary_deduction a
      INNER JOIN hris_deduction_log b ON a.employee_idno = b.employee_idno
      INNER JOIN hris_deduction_summary c ON b.deductionsum_id = c.id
      INNER JOIN hris_payroll_summary d ON c.id = d.deduction_id
      SET payroll_ref_no = d.ref_no
      WHERE DATE(a.date_created) BETWEEN d.fromdate AND d.todate
      AND d.status = 'approved'
      AND a.payroll_ref_no = 'none'
      AND a.status = 'certified'
      AND a.enabled = 1";
    $this->db->query($tag_sql3,$tag_data);
  }

  public function tag_cash_advance_pay_on_payroll($tag_data){
    $tag_sql3 = "UPDATE cash_advance_pay a
      INNER JOIN hris_deduction_log b ON a.employee_idno = b.employee_idno
      INNER JOIN hris_deduction_summary c ON b.deductionsum_id = c.id
      INNER JOIN hris_payroll_summary d ON c.id = d.deduction_id
      INNER JOIN cash_advance_tran e ON a.ca_id = e.id
      SET payroll_ref_no = d.ref_no
      WHERE a.cutoff_from = d.fromdate AND a.cutoff_to = d.todate
      AND d.status = 'approved'
      AND a.payroll_ref_no = 'none'
      AND e.status = 'certified'
      AND a.enabled = 1";
    $this->db->query($tag_sql3,$tag_data);
  }

  ### payslip ###

  public function get_payslip($company,$from,$to,$type,$frequency,$pay_day,$id,$ref_no){
    $cfrom = $from;
    $cto = $to;
    $company = $this->db->escape($company);
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $type = $this->db->escape($type);
    $frequency = $this->db->escape($frequency);
    $pay_day = $this->db->escape($pay_day);
    $id = $this->db->escape($id);

    $sql = "SELECT
        CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
        @emp_idnum := a.employee_idno as emp_idnum,
        @daily_rate := (CASE WHEN c.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
        ROUND(@daily_rate / (e.total_whours - e.total_bhours),2) as hourly_rate,
        ROUND(@daily_rate / (e.total_whours - e.total_bhours) / 60,2) as min_rate,
        (SELECT SUM(man_hours) FROM time_record_summary a
          WHERE a.employee_idno = emp_idnum
          AND a.date_created BETWEEN $from AND $to
        ) as man_hours,
        (SELECT SUM(late) FROM time_record_summary a
          WHERE a.employee_idno = emp_idnum
          AND a.date_created BETWEEN $from AND $to
        ) as late,
        (SELECT SUM(undertime) FROM time_record_summary a
          WHERE a.employee_idno = emp_idnum
          AND a.date_created BETWEEN $from AND $to
        ) as undertime,
        (SELECT SUM(g.amount)
         FROM salary_deduction g
         WHERE g.employee_idno = emp_idnum
         AND g.status = 'certified'
         AND DATE(g.date_created)
         BETWEEN $from AND $to
        ) as salary_deduction,
        (SELECT SUM(((f.amount + f.rate) / f.terms) / c.frequency)
         FROM cash_advance_tran f
         WHERE f.employee_id = emp_idnum
         AND f.status = 'certified'
         AND (f.date_of_effectivity >= f.date_of_effectivity AND f.date_of_effectivity <= $to)
         AND f.date_end >= $to
        ) as cash_advance,
        (CASE WHEN k.basic_mo_sal1 > 10000
          THEN (((b.total_sal_converted * 0.0275) / 2) / c.frequency)
          WHEN k.basic_mo_sal1 > 40000 THEN (k.employee_share1 / c.frequency)
          ELSE ((k.employee_share1 / 2) / c.frequency) END) as philhealth,
        (CASE WHEN b.pagibig > 1
        THEN (5000 * (l.employee_share / 100)) / c.frequency
        ELSE (b.total_sal_converted * (l.employee_share / 100)) / c.frequency
        END) as pagibig,
        (ss_ee / c.frequency) as sss,
        (SELECT (ss.monthly_amortization / $frequency) FROM hris_sss_loans ss
          WHERE ss.sss_deduction_start BETWEEN $from AND $to
          AND $pay_day BETWEEN ss.sss_loan_start AND ss.sss_loan_end
          AND ss.status = 'active' AND ss.enabled = 1 AND ss.employee_idno = @emp_idnum
        ) as sss_loan,
        (SELECT (love.monthly_amortization / $frequency) FROM hris_pagibig_loans love
          WHERE love.pagibig_deduction_start BETWEEN $from AND $to
          AND $pay_day BETWEEN love.pagibig_loan_start AND love.pagibig_loan_end
          AND love.status = 'active' AND love.enabled = 1 AND love.employee_idno = @emp_idnum
        ) as pagibig_loan,
        (SELECT
        (CASE WHEN c.frequency >= 4
         THEN ROUND(SUM((b.total_sal / (e.total_whours - e.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60)),2)
         ELSE ROUND(SUM((((daily_rate) / (e.total_whours - e.total_bhours)) * 1.25 * (m.minutes_of_overtime / 60))), 2)
        END)
        FROM overtime_pays m WHERE m.type = 'overtime' AND m.status = 'certified' AND m.employee_id = emp_idnum
        AND date_rendered BETWEEN $from AND $to
        ) as ot_pays,
        (SELECT SUM(m.minutes_of_overtime) FROM overtime_pays m WHERE m.type = 'overtime' AND m.status = 'certified' AND m.employee_id = emp_idnum
        AND m.date_rendered BETWEEN $from AND $to)
        as ot_min,
        (SELECT SUM(n.amount) FROM additional_pays n
         WHERE n.employee_id = emp_idnum AND n.status = 'certified'
         AND n.date_issued BETWEEN $from AND $to
        ) as additional_pays, e.work_sched, f.regular_holiday, f.special_non_working_holiday,
        c.description as pay_type, c.frequency, b.total_sal, (ss_ee * 12) as sss_year_contri,
        (CASE WHEN b.philhealth > 1 THEN ((b.total_sal_converted * 0.0275) / 2) * 12
        ELSE (k.employee_share1 / 2) * 12 END) as philhealth_year_contri,
        (CASE WHEN b.pagibig > 1 THEN (5000 * (l.employee_share / 100)) * 12
        ELSE (b.total_sal_converted * (l.employee_share / 100)) * 12 END) as pagibig_year_contri,
        p.aibLowerLimit as lower_limit, p.aibUpperLimit as upper_limit, p.tr1LowerLimit as tax_lower_limit,
        (p.tr1ExcessLimit / 100) as excess_limit, f.empstatusid as emp_status, q.exchange_rate as ex_rate,
        b.total_sal_converted, b.currency
        FROM employee_record a
        LEFT JOIN contract b ON a.id = b.contract_emp_id
        LEFT JOIN paytype c ON b.paytype = c.paytypeid
        INNER JOIN time_record_summary d ON a.employee_idno = d.employee_idno
        LEFT JOIN work_schedule e ON b.work_sched_id = e.id
        LEFT JOIN empstatus f ON b.emp_status = f.empstatusid
        LEFT JOIN salary_deduction g ON a.employee_idno = g.employee_idno
        LEFT JOIN work_schedule h ON a.employee_idno = h.emp_idno
        LEFT JOIN cash_advance_tran i ON a.employee_idno = i.employee_id
        LEFT JOIN sss j ON b.sss = j.id
        LEFT JOIN philhealth k ON b.philhealth = k.phID
        LEFT JOIN pagibig l ON b.pagibig = l.id
        LEFT JOIN overtime_pays m ON a.employee_idno = m.employee_id
        LEFT JOIN additional_pays n ON a.employee_idno = n.employee_id
        INNER JOIN time_record_summary o ON a.employee_idno = o.employee_idno
        LEFT JOIN tax p ON b.tax = p.id
        LEFT JOIN hris_exchange_rates q ON b.currency = q.currency_code
        WHERE b.contract_status = 'active' AND a.enabled = 1 AND b.paytype = $type
        AND b.company_id = $company
        AND o.date_created BETWEEN $from AND $to AND o.absent = 0 AND k.enabled = 1
        AND a.employee_idno = $id ";

    $query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows();

    $totalFiltered = $query->num_rows();
		$totalFiltered = $totalData;

		// $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    // $data_main = array();

    $d1 = new Datetime($cfrom);
    $d2 = new Datetime($cto);
    $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
    $p_type = "";

    foreach($query->result_array() as $row){
      $sss          = ($row['currency'] == "PHP") ? $row['sss'] : (float)$row['sss'] / (float)$row['ex_rate'];
      $philhealth   = ($row['currency'] == "PHP") ? $row['philhealth'] : (float)$row['philhealth'] / (float)$row['ex_rate'];
      $pagibig      = ($row['currency'] == "PHP") ? $row['pagibig'] : (float)$row['pagibig'] / (float)$row['ex_rate'];
      $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
      $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

      // $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];
      $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
      $cash_advance     = ($row['currency'] == "PHP") ? $row['cash_advance'] : (float)$row['cash_advance'] / (float)$row['ex_rate'];
      $salary_deduction = ($row['currency'] == "PHP") ? $row['salary_deduction'] : (float)$row['salary_deduction'] / (float)$row['ex_rate'];

      $sss_year_contri        = ($row['currency'] == "PHP") ? $row['sss_year_contri'] : (float)$row['sss_year_contri'] / (float)$row['ex_rate'];
      $philhealth_year_contri = ($row['currency'] == "PHP") ? $row['philhealth_year_contri'] : (float)$row['philhealth_year_contri'] / (float)$row['ex_rate'];
      $pagibig_year_contri    = ($row['currency'] == "PHP") ? $row['pagibig_year_contri'] : (float)$row['pagibig_year_contri'] / (float)$row['ex_rate'];

      // $compensation = $row['sss'] + $row['philhealth'] + $row['pagibig'] + $row['sss_loan'] + $row['pagibig_loan'];
      // $additionals = $row['ot_pays'] + $row['additional_pays'];
      // $deductions = $row['cash_advance'] + $row['salary_deduction'];

      $additionals = $row['ot_pays'] + $additional_pays;
      $compensation = $sss + $philhealth + $pagibig + $sss_loan + $pagibig_loan;
      $deductions = $cash_advance + $salary_deduction;
      $manhours = $row['man_hours'];

      $daily_rate = $row['daily_rate'];
      $hourly_rate = $row['hourly_rate'];
      $min_rate = $row['min_rate'];

      $emp_idnum = $this->db->escape($row['emp_idnum']);
      $emp_status = $this->db->escape($row['emp_status']);
      $gross_pay = ($row['frequency'] > 2) ? 0 : $row['total_sal'] / $row['frequency'];
      $gross_pay_raw = ($row['frequency'] > 2) ? 0 : $row['total_sal'] / $row['frequency'];
      $taxable_income = ($row['total_sal']) - ($sss_year_contri + $philhealth_year_contri + $pagibig_year_contri);
      $total_tax = round((($taxable_income - $row['lower_limit']) * $row['excess_limit']) + $row['tax_lower_limit'],2) / 12 / $row['frequency'];
      $total_tax = ($row['currency'] == "PHP") ? $total_tax : $total_tax / (float)$row['ex_rate'];

      $cut_off_days = 0;
      $wdays = 0;
      $absent = 0;
      $absent_deduction = 0;
      $late_deduct = $min_rate * $row['late'];
      $ut_deduct = $min_rate * $row['undertime'];
      $total_deduct = $compensation + $deductions + $late_deduct + $ut_deduct;
      $total_deduct2 = $compensation + $deductions;

      $reg_holiday = 0;
      $reg_holiday_pay = 0;
      $spl_holiday = 0;
      $spl_holiday_pay = 0;
      $sunday = 0;
      $sunday_pay = 0;

      $sdate = new Datetime($cfrom);
      $edate = new Datetime($cto);

      $worksched = json_decode($row['work_sched']);
      $worksched = (array)$worksched;
      $days = array('mon','tue','wed','thu','fri','sat','sun');

      for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
        $ldate_raw = $x->format('Y-m-d');
        $ldate = $this->db->escape($ldate_raw);

        $sql2 = "SELECT c.man_hours, e.regular_holiday, e.special_non_working_holiday,
                c.date_created as date, e.empstatusid as employee_type
                FROM employee_record a
                LEFT JOIN contract b ON a.id = b.contract_emp_id
                LEFT JOIN time_record_summary c ON a.employee_idno = c.employee_idno
                LEFT JOIN empstatus e ON b.emp_status = e.empstatusid
                WHERE c.date_created = $ldate AND a.employee_idno = $emp_idnum
                AND b.contract_status = 'active' AND a.enabled = 1 AND c.absent = 0
                -- UNION
                -- SELECT TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600 as man_hours,
                -- d.regular_holiday, d.special_non_working_holiday, c.date as date
                -- FROM employee_record a
                -- LEFT JOIN contract b ON a.id = b.contract_emp_id
                -- LEFT JOIN work_order c ON a.employee_idno = c.employee_id
                -- LEFT JOIN empstatus d ON b.emp_status = d.empstatusid
                -- WHERE c.date = $ldate AND a.employee_idno = $emp_idnum AND c.enabled = 1
                -- AND b.contract_status = 'active' AND a.enabled = 1 AND c.status = 'certified'";

        $sql3 = "SELECT a.date, b.holidaytypeid as holiday_type, payratio, payratio2
                FROM holidays_tran a
                LEFT JOIN holidaytype b ON a.holiday_type = b.holidaytypeid
                WHERE a.date = $ldate AND a.enabled = 1";

        $query2 = $this->db->query($sql2);
        $query3 = $this->db->query($sql3);

        ### cut off days ###
        $d_main = new Datetime($ldate_raw);
        $wdate_main = strtolower($d_main->format('D'));
        for ($a=0; $a < 7; $a++) {
          if($wdate_main == $days[$a]){
            if($worksched[$days[$a]][0] != ""){
              $cut_off_days += 1;
            }
          }
        }

        ### if the day is holiday and has man_hours
        if($query2->num_rows() > 0 && $query3->num_rows() > 0){
          $timelog = $query2->row();
          $holiday = $query3->row();
          $d = new Datetime($timelog->date);
          $wdate = strtolower($d->format('D'));

          if($timelog->man_hours > 0){
            ### regular holiday
            if($holiday->holiday_type == 1){
              $reg_holiday += 1;

              if($timelog->regular_holiday == 'yes'){
                for ($i=0; $i < 7; $i++) {
                  if($wdate == $days[$i]){
                    ### weekly ###
                    if($row['frequency'] > 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * $holiday->payratio;
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio;
                        $gross_pay += $hourly_rate * $holiday->payratio *  $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * $holiday->payratio * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * $holiday->payratio2;
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio2;

                        $gross_pay += $hourly_rate * $holiday->payratio2 *  $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * $holiday->payratio2 * $timelog->man_hours;
                      }
                    }
                    ### monthly || semi-monthly ###
                    if($row['frequency'] <= 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * ($holiday->payratio / 2);
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio;

                        $gross_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * ($holiday->payratio2 / 2);
                        // $reg_holiday_pay += $daily_rate * $holiday->payratio2;
                        $gross_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                        $reg_holiday_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                      }
                    }
                  }
                }
              }
            }

            ### special non working holiday
            if($holiday->holiday_type == 2){
              $spl_holiday += 1;

              if($timelog->special_non_working_holiday == 'yes'){
                for ($i=0; $i < 7; $i++) {
                  if($wdate == $days[$i]){

                    if($row['frequency'] > 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * $holiday->payratio;
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio;

                        $gross_pay += $hourly_rate * $holiday->payratio *  $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * $holiday->payratio * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * $holiday->payratio2;
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio2;

                        $gross_pay += $hourly_rate * $holiday->payratio2 *  $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * $holiday->payratio2 * $timelog->man_hours;
                      }
                    }

                    if($row['frequency'] <= 2){
                      if($worksched[$days[$i]][0] != ""){
                        // $gross_pay += $daily_rate * ($holiday->payratio / 2);
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio;

                        $gross_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * ($holiday->payratio - 1) * $timelog->man_hours;
                      }else{
                        // $gross_pay += $daily_rate * ($holiday->payratio2 / 2);
                        // $spl_holiday_pay += $daily_rate * $holiday->payratio2;

                        $gross_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                        $spl_holiday_pay += $hourly_rate * ($holiday->payratio2 - 1) * $timelog->man_hours;
                      }
                    }

                  }
                }
              }
            }

          }else{
            if($holiday->holiday_type == 1){
              $reg_holiday += 1;
              if($timelog->regular_holiday == 'yes'){
                $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                $reg_holiday_pay += $daily_rate;
              }
            }

            if($holiday->holiday_type == 2){
              $spl_holiday += 1;
              if($timelog->regular_holiday == 'yes'){
                $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                $spl_holiday_pay += $daily_rate;
              }
            }
          }

        }

        ### if has man_hours only
        if($query2->num_rows() > 0 && $query3->num_rows() == 0){
          $timelog = $query2->row();
          $d = new Datetime($timelog->date);
          $wdate = strtolower($d->format('D'));
          // $g[] = 3;
          // $g[] = $timelog->date;
          for ($i=0; $i < 7; $i++) {
            if($wdate == $days[$i]){
              if($worksched[$days[$i]][0] != ""){
                if($row['frequency'] > 2){
                  $gross_pay += $daily_rate;
                }
                $wdays += 1;
              }else{ // for sunday or off day
                $gross_pay += ($row['frequency'] > 2)
                ? $hourly_rate * 1.3 * $timelog->man_hours // daily rate
                : $hourly_rate * .3 * $timelog->man_hours; // semi-monthly

                if($wdate == 'sun'){
                  $sunday += 1;
                  $sunday_pay += ($row['frequency'] > 2)
                  ? $hourly_rate * 1.3 * $timelog->man_hours // daily rate
                  : $hourly_rate * .3 * $timelog->man_hours; // semi-monthly
                }
              }
            }
          }
        }

        ### holiday only ###
        if($query3->num_rows() > 0 && $query2->num_rows() == 0){
          $holiday = $query3->row();
          if($holiday->holiday_type == 1){
            $reg_holiday += 1;
            if($row['regular_holiday'] == 'yes'){
              $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              $reg_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              // $reg_holiday_pay += $daily_rate;
            }
          }

          if($holiday->holiday_type == 2){
            $spl_holiday += 1;
            if($row['special_non_working_holiday'] == 'yes'){
              $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              $spl_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
              // $spl_holiday_pay += $daily_rate;
            }
          }
        }

        ### absent
        if($query2->num_rows() == 0 && $query3->num_rows() == 0){
          $d = new Datetime($ldate_raw);
          $wdate = strtolower($d->format('D'));
          for ($i=0; $i < 7; $i++) {
            if($wdate == $days[$i]){
              if($worksched[$days[$i]][0] != ""){
                $leave_sql = "SELECT * FROM leave_tran WHERE employee_idno = $emp_idnum
                              AND $ldate BETWEEN date_from AND date_to
                              AND status = 'certified' AND enabled = 1";
                $check_leave = $this->db->query($leave_sql);
                ### CHECK EMPLOYEE STATUS AND LEAVE ###
                if($emp_status == regular_employee()){
                  if($check_leave->num_rows() == 0){ // NO LEAVE
                    if($row['frequency'] <= 2){
                      $gross_pay -= $daily_rate;
                      $absent_deduction += $daily_rate;
                    }
                    $absent += 1;
                  }else{ // HAS LEAVE
                    if($row['frequency'] > 2){
                      $gross_pay += $daily_rate;
                    }
                    $wdays += 1;
                  }
                }else{
                  if($check_leave->num_rows() == 0){ // NO LEAVE
                    if($row['frequency'] <= 2){
                      $gross_pay -= $daily_rate;
                      $absent_deduction += $daily_rate;
                    }
                    $absent += 1;
                  }else{ // HAS LEAVE
                    $wdays += 1;
                  }
                }
              }
            }
          }
        }

      }

      // $gross_pay = $gross_pay - $late_deduct - $ut_deduct;
      $net_pay = ($gross_pay + $additionals) - $total_deduct;
      $gross_pay_less = ($gross_pay - $late_deduct) - $ut_deduct;

      ### semi monthly || weekly ###
      $gross_pay = ($row['frequency'] > 2) ? number_format($gross_pay,2) : number_format($gross_pay_raw,2);
      $data = array(
        "emp_idno" => $row['emp_idnum'],
        "fullname" => $row['fullname'],
        "ref_no" => $ref_no,
        "date" => $date,
        "wdays" => number_format($cut_off_days,2),
        "gross_pay" => $gross_pay,
        "gross_pay_less" => number_format($gross_pay_less,2),
        "reg_holiday" => number_format($reg_holiday,2),
        "reg_holiday_pay" => number_format($reg_holiday_pay,2),
        "spl_holiday" => number_format($spl_holiday,2),
        "spl_holiday_pay" => number_format($spl_holiday_pay,2),
        "sunday" => number_format($sunday,2),
        "sunday_pay" => number_format($sunday_pay,2),
        "absent" => number_format($absent,2),
        "absent_deduction" => number_format($absent_deduction,2),
        "late" => number_format($row['late']),
        "late_deduct" => number_format(($late_deduct),2),
        "ut" => number_format($row['undertime']),
        "ut_deduct" => number_format($ut_deduct,2),
        "total_deduct" => number_format($total_deduct2,2),
        "sss" => number_format($sss,2),
        "sss_loan" => number_format($sss_loan,2),
        "pagibig_loan" => number_format($pagibig_loan,2),
        "philhealth" => number_format($philhealth,2),
        "pagibig" => number_format($pagibig,2),
        "cashadvance" => number_format($cash_advance,2),
        "sal_deduct" => number_format($salary_deduction,2),
        "add_pay" => number_format($additional_pays,2),
        "ot_min" => number_format($row['ot_min']),
        "ot_pay" => number_format($row['ot_pays'],2),
        "net_pay" => number_format($net_pay,2),
        "currency" => $row['currency'],
        "ex_rate" => $row['ex_rate']
      );
    }

    return $data;
  }

  ### bank file ###

  public function get_bank_file_data($payroll_refno, $bank){
		$payroll_refno = $this->db->escape($payroll_refno);
		$bank = $this->db->escape($bank);
		$sql = "SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
		 e.card_number, e.account_number, c.netpay, a.employee_idno, a.email, b.currency,
     f.exchange_rate as ex_rate
		 FROM employee_record a
		 INNER JOIN contract b ON a.id = b.contract_emp_id
		 INNER JOIN hris_payroll_log c ON a.employee_idno = c.emp_id
		 INNER JOIN hris_payroll_summary d ON c.payroll_summary_id = d.id
		 INNER JOIN contract_payout_medium e ON b.id = e.contract_id
     INNER JOIN hris_exchange_rates f ON b.currency = f.currency_code
		 WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1 AND e.enabled = 1
		 AND b.contract_status = 'active' AND c.status = 'approved' AND d.status = 'approved'
		 AND d.ref_no = $payroll_refno AND e.bank_id = $bank AND e.payout_medium_id IN (2,3)
     ORDER BY fullname ASC";
		return $this->db->query($sql);
	}
}
