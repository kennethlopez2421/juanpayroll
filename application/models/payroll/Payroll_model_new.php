<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payroll_model_new extends CI_Model {

  public function get_payroll_log($company,$from,$to,$type,$frequency,$pay_day,$search = ""){
    // date_default_timezone_set('Asia/Manial');
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

    $nightdiff_status = 'off';
    $night_diff_sql = "SELECT * FROM hris_nightdiff_settings WHERE enabled = 1";
    $nightdiff = $this->db->query($night_diff_sql);
    if($nightdiff->num_rows() > 0){
      $nightdiff = $nightdiff->row_array();
      $nightdiff_status = $nightdiff['status'];
    }

    $affected_user_sql = "SELECT a.id, b.contract_start, b.contract_end, b.id as contract_id,
     CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, a.first_month, a.tin_no,
     @year_diff := (SELECT (CASE WHEN DATEDIFF('2020-01-01', $pay_day) < 365 THEN 0 ELSE DATEDIFF('2020-01-01', $pay_day) / 365 END)) as year_diff,
     @ph_contri := (SELECT (CASE WHEN ((.5 * year_diff) + 3.0) > 5.0 THEN 5.0 / 100 ELSE ((.5 * year_diff) + 3.0) / 100 END)) as ph_contri,
     @emp_id := a.employee_idno as emp_idnum,
     @whours := (h.total_whours - h.total_bhours) as whours,
     @daily_rate := (CASE WHEN e.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
     @hourly_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours),2) as hourly_rate,
     @min_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours) / 60,2) as min_rate,
     @man_hours := (SELECT SUM(man_hours) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as man_hours,
     @night_diff := SUM(d.night_diff) as night_diff,
     @late := (SELECT SUM(late) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as late,
     @late_deduct := (SELECT SUM( (SELECT (CASE WHEN late >= min_from AND late <= min_to THEN min_deduct * min_rate ELSE late * min_rate END) FROM hris_clockinout_deductions WHERE status = 'on' AND type = 'late' AND whours = whours )) as lates FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as late_deduct,
     @undertime := (SELECT SUM(undertime) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as undertime,
     @salary_deduction := (SELECT SUM(amount) FROM salary_deduction WHERE employee_idno = a.employee_idno AND status = 'certified' AND DATE(date_created) BETWEEN $from AND $to) as salary_deduction,
     @cash_advance := (SELECT SUM(((amount + rate) / terms) / $frequency) FROM cash_advance_tran WHERE employee_id = a.employee_idno AND status = 'certified' AND (date_of_effectivity >= date_of_effectivity AND date_of_effectivity <= $to) AND date_end >= $to) as cash_advance,
     @sss := (SELECT (ss_ee / $frequency) FROM sss WHERE id = b.sss AND enabled = 1 AND a.sss_no != '') as sss,
     @sss_loan := (SELECT (monthly_amortization / $frequency) FROM hris_sss_loans WHERE status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno AND $pay_day BETWEEN sss_loan_start AND sss_loan_end) as sss_loan,
     @pagibig_loan := (SELECT (monthly_amortization / $frequency) FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan,
     @sss_loan_id := (SELECT id as sss_loan_id FROM hris_sss_loans WHERE $pay_day BETWEEN sss_loan_start AND sss_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as sss_loan_id,
     @pagibig_loan_id := (SELECT id as pagibig_loan_id FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan_id,
     @philhealth := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN (((b.base_pay * ph_contri) / 2) / $frequency) WHEN basic_mo_sal1 > 40000 THEN (employee_share1 / $frequency) ELSE ((employee_share1 / 2) / $frequency) END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1 AND a.philhealth_no != '') as philhealth,
     @pagibig := (SELECT (CASE WHEN employee_share > 1.00 THEN (5000 * (employee_share / 100)) / $frequency ELSE (b.base_pay * (employee_share / 100)) / $frequency END) FROM pagibig WHERE id = b.pagibig AND enabled = 1 AND a.pagibig_no != '') as pagibig,
     @ot_pays := (SELECT (CASE WHEN e.frequency >= 4 THEN ROUND(SUM((b.total_sal / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60)),2) ELSE ROUND(SUM((((daily_rate) / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60))), 2) END) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_pays,
     @ot_min := (SELECT SUM(minutes_of_overtime) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_min,
     @additional_pays := (SELECT SUM(amount) FROM additional_pays WHERE employee_id = a.employee_idno AND status = 'certified' AND date_issued BETWEEN $from AND $to) as additional_pays,
     @sss_year_contri := (SELECT (ss_ee * 12) FROM sss WHERE id = b.sss AND enabled = 1) as sss_year_contri,
     @philhealth_year_contri := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN ((b.base_pay * ph_contri) / 2) * 12 ELSE (employee_share1 / 2) * 12 END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1) as philhealth_year_contri,
     @pagibig_year_contri := (SELECT (CASE WHEN b.pagibig > 1 THEN (5000 * (employee_share / 100)) * 12 ELSE (b.base_pay * (employee_share / 100)) * 12 END) FROM pagibig WHERE id = b.pagibig AND enabled = 1) as pagibig_year_contri,
     k.aibLowerLimit as lower_limit, k.aibUpperLimit as upper_limit, k.tr1LowerLimit as tax_lower_limit,
     (k.tr1ExcessLimit / 100) as excess_limit, j.empstatusid as emp_status, b.total_sal_converted,
     e.description as pay_type, b.currency, f.exchange_rate as ex_rate, b.contract_ref_no, g.id as ca_id,
     h.work_sched, j.regular_holiday, j.special_non_working_holiday, j.leave_pay, e.description as pay_type, e.frequency, b.total_sal
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN hris_companies c ON b.company_id = c.id
     INNER JOIN time_record_summary d ON a.employee_idno = d.employee_idno
     LEFT JOIN paytype e ON b.paytype = e.paytypeid
     LEFT JOIN hris_exchange_rates f ON b.currency = f.currency_code
     LEFT JOIN cash_advance_tran g ON a.employee_idno = g.employee_id
     LEFT JOIN work_schedule h ON b.work_sched_id = h.id
     LEFT JOIN work_schedule i ON a.employee_idno = i.emp_idno
     LEFT JOIN empstatus j ON b.emp_status = j.empstatusid
     LEFT JOIN tax k ON b.tax = k.id
     WHERE d.date_created >= $from AND d.date_created <= $to AND b.paytype = $type AND c.id = $company
     AND b.contract_status = 'active' AND a.enabled = 1 AND b.enabled = 1
     AND c.enabled = 1 AND d.enabled = 1 AND e.enabled = 1 AND f.enabled = 1
     GROUP BY a.employee_idno ORDER BY fullname ASC";

    $affected_user_query = $this->db->query($affected_user_sql);
    // return $this->db->last_query();

    $totalData = $affected_user_query->num_rows();
		$totalFiltered = $affected_user_query->num_rows();

    $data = array();
    $data_main = array();

    $d1 = new Datetime($cfrom);
    $d2 = new Datetime($cto);
    $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
    $p_type = "";

    if($affected_user_query->num_rows() > 0){
      foreach($affected_user_query->result_array() as $row){
        $nestedData = array();
        $curr_contract = $row;
        $prev_contract = '';
        $emp_idnum = $this->db->escape($row['emp_idnum']);

        // GET ALL TIMERECORD OF THIS USER
        $timelog_sql = "SELECT c.man_hours, e.regular_holiday, e.special_non_working_holiday,
          c.date_created as date, e.empstatusid as employee_type
          FROM employee_record a
          LEFT JOIN contract b ON a.id = b.contract_emp_id
          LEFT JOIN time_record_summary c ON a.employee_idno = c.employee_idno
          LEFT JOIN empstatus e ON b.emp_status = e.empstatusid
          WHERE c.date_created BETWEEN $from AND $to AND a.employee_idno = $emp_idnum
          AND b.contract_status = 'active' AND a.enabled = 1 AND c.absent = 0 ORDER BY c.date_created ASC";

        $holiday_sql = "SELECT a.date, b.type as holiday_type, payratio, payratio2
          FROM holidays_tran a
          LEFT JOIN holidaytype b ON a.holiday_type = b.holidaytypeid
          WHERE a.date BETWEEN $from AND $to AND a.enabled = 1";

        $timelog_query = $this->db->query($timelog_sql);
        $holiday_query = $this->db->query($holiday_sql);

        ### CHECK CONTRACT OVERLAP ###
        if(($row['contract_start'] > $cfrom && $row['contract_start'] < $cto) || ($row['contract_start'] > $cto)){
          // $curr_contract = $row;
          $id = $this->db->escape($row['emp_idnum']);
          $prev_con_sql = "SELECT a.id, b.contract_start, b.contract_end, b.id as contract_id,
           CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, a.first_month, a.tin_no,
           @year_diff := (SELECT (CASE WHEN DATEDIFF('2020-01-01', $pay_day) < 365 THEN 0 ELSE DATEDIFF('2020-01-01', $pay_day) / 365 END)) as year_diff,
           @ph_contri := (SELECT (CASE WHEN ((.5 * year_diff) + 3.0) > 5.0 THEN 5.0 / 100 ELSE ((.5 * year_diff) + 3.0) / 100 END)) as ph_contri,
           @emp_id := a.employee_idno as emp_idnum,
           @whours := (h.total_whours - h.total_bhours) as whours,
           @daily_rate := (CASE WHEN e.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
           @hourly_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours),2) as hourly_rate,
           @min_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours) / 60,2) as min_rate,
           @man_hours := (SELECT SUM(man_hours) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as man_hours,
           @night_diff := (SELECT SUM(night_diff) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as night_diff,
           @late := (SELECT SUM(late) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as late,
           @late_deduct := (SELECT SUM( (SELECT (CASE WHEN late >= min_from AND late <= min_to THEN min_deduct * min_rate ELSE late * min_rate END) FROM hris_clockinout_deductions WHERE status = 'on' AND type = 'late' AND whours = whours )) as lates FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as late_deduct,
           @undertime := (SELECT SUM(undertime) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as undertime,
           @salary_deduction := (SELECT SUM(amount) FROM salary_deduction WHERE employee_idno = a.employee_idno AND status = 'certified' AND DATE(date_created) BETWEEN $from AND $to) as salary_deduction,
           @cash_advance := (SELECT SUM(((amount + rate) / terms) / $frequency) FROM cash_advance_tran WHERE employee_id = a.employee_idno AND status = 'certified' AND (date_of_effectivity >= date_of_effectivity AND date_of_effectivity <= $to) AND date_end >= $to) as cash_advance,
           @sss := (SELECT (ss_ee / $frequency) FROM sss WHERE id = b.sss AND enabled = 1 AND a.sss_no != '') as sss,
           @sss_loan := (SELECT (monthly_amortization / $frequency) FROM hris_sss_loans WHERE status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno AND $pay_day BETWEEN sss_loan_start AND sss_loan_end) as sss_loan,
           @pagibig_loan := (SELECT (monthly_amortization / $frequency) FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan,
           @sss_loan_id := (SELECT id as sss_loan_id FROM hris_sss_loans WHERE $pay_day BETWEEN sss_loan_start AND sss_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as sss_loan_id,
           @pagibig_loan_id := (SELECT id as pagibig_loan_id FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan_id,
           @philhealth := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN (((b.base_pay * ph_contri) / 2) / $frequency) WHEN basic_mo_sal1 > 40000 THEN (employee_share1 / $frequency) ELSE ((employee_share1 / 2) / $frequency) END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1 AND a.philhealth_no != '') as philhealth,
           @pagibig := (SELECT (CASE WHEN employee_share > 1.00 THEN (5000 * (employee_share / 100)) / $frequency ELSE (b.base_pay * (employee_share / 100)) / $frequency END) FROM pagibig WHERE id = b.pagibig AND enabled = 1 AND a.pagibig_no != '') as pagibig,
           @ot_pays := (SELECT (CASE WHEN e.frequency >= 4 THEN ROUND(SUM((b.total_sal / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60)),2) ELSE ROUND(SUM((((daily_rate) / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60))), 2) END) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_pays,
           @ot_min := (SELECT SUM(minutes_of_overtime) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_min,
           @additional_pays := (SELECT SUM(amount) FROM additional_pays WHERE employee_id = a.employee_idno AND status = 'certified' AND date_issued BETWEEN $from AND $to) as additional_pays,
           @sss_year_contri := (SELECT (ss_ee * 12) FROM sss WHERE id = b.sss AND enabled = 1) as sss_year_contri,
           @philhealth_year_contri := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN ((b.base_pay * ph_contri) / 2) * 12 ELSE (employee_share1 / 2) * 12 END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1) as philhealth_year_contri,
           @pagibig_year_contri := (SELECT (CASE WHEN b.pagibig > 1 THEN (5000 * (employee_share / 100)) * 12 ELSE (b.base_pay * (employee_share / 100)) * 12 END) FROM pagibig WHERE id = b.pagibig AND enabled = 1) as pagibig_year_contri,
           k.aibLowerLimit as lower_limit, k.aibUpperLimit as upper_limit, k.tr1LowerLimit as tax_lower_limit,
           (k.tr1ExcessLimit / 100) as excess_limit, j.empstatusid as emp_status, b.total_sal_converted,
           e.description as pay_type, b.currency, f.exchange_rate as ex_rate, b.contract_ref_no, g.id as ca_id,
           h.work_sched, j.regular_holiday, j.special_non_working_holiday, j.leave_pay, e.description as pay_type, e.frequency, b.total_sal
           FROM employee_record a
           INNER JOIN contract b ON a.id = b.contract_emp_id
           INNER JOIN hris_companies c ON b.company_id = c.id
           LEFT JOIN paytype e ON b.paytype = e.paytypeid
           LEFT JOIN hris_exchange_rates f ON b.currency = f.currency_code
           LEFT JOIN cash_advance_tran g ON a.employee_idno = g.employee_id
           LEFT JOIN work_schedule h ON b.work_sched_id = h.id
           LEFT JOIN work_schedule i ON a.employee_idno = i.emp_idno
           LEFT JOIN empstatus j ON b.emp_status = j.empstatusid
           LEFT JOIN tax k ON b.tax = k.id
           WHERE b.paytype = $type AND c.id = $company AND a.employee_idno = $id
           AND b.contract_status = 'inactive' AND a.enabled = 1 AND b.enabled = 1
           AND c.enabled = 1 AND e.enabled = 1 AND f.enabled = 1
           ORDER BY b.created_at DESC LIMIT 1";

          $prev_con_query = $this->db->query($prev_con_sql);
          // die($this->db->last_query());

          if($prev_con_query->num_rows() > 0){ //
            $prev_contract = $prev_con_query->row_array();
          }

        }

        ### CONTRACT OVERLAP ON CUTOFF ### => PREVIOUS CONTRACT
        if($prev_contract != ''){
          $row = ($prev_contract == '') ? $curr_contract : $prev_contract;
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
          // NIGHT DIFFERENTIALS
          $night_diffhours = 0;
          $night_differentials = 0;
          if($nightdiff_status == 'on'){
            $nightdiff_rate = ($row['hourly_rate'] * ($nightdiff['percent'] / 100));
            $night_differentials = $nightdiff_rate * $row['night_diff'];
            $night_diffhours = $row['night_diff'];
          }

          $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
          $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

          // $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];
          $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
          $cash_advance     = ($row['currency'] == "PHP") ? $row['cash_advance'] : (float)$row['cash_advance'] / (float)$row['ex_rate'];
          $salary_deduction = ($row['currency'] == "PHP") ? $row['salary_deduction'] : (float)$row['salary_deduction'] / (float)$row['ex_rate'];

          $sss_year_contri        = ($row['currency'] == "PHP") ? $row['sss_year_contri'] : (float)$row['sss_year_contri'] / (float)$row['ex_rate'];
          $philhealth_year_contri = ($row['currency'] == "PHP") ? $row['philhealth_year_contri'] : (float)$row['philhealth_year_contri'] / (float)$row['ex_rate'];
          $pagibig_year_contri    = ($row['currency'] == "PHP") ? $row['pagibig_year_contri'] : (float)$row['pagibig_year_contri'] / (float)$row['ex_rate'];

          $additionals = $row['ot_pays'] + $additional_pays + $night_differentials;
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
          $total_tax = ($row['tin_no'] != '') ? $total_tax : 0;

          $cut_off_days = 0;
          $wdays = 0;
          $absent = 0;
          $absent_deduction = 0;
          $late_deduct = ($row['late_deduct'] == null) ? $min_rate * $row['late'] :$row['late_deduct'];
          $ut_deduct = $min_rate * $row['undertime'];
          $total_deduct = $compensation + $deductions + $late_deduct + $ut_deduct;
          $total_deduct2 = $compensation + $deductions;

          $reg_holiday = 0;
          $reg_holiday_pay = 0;
          $spl_holiday = 0;
          $spl_holiday_pay = 0;
          $sunday = 0;
          $sunday_pay = 0;
          // echo $cfrom;
          // echo $cto;

          $sdate = new DateTime($cfrom);
          $edate = new DateTime($cto);
          $fdate = new DateTime($row['contract_start']);
          // $sdate = ($sdate < $fdate) ? $fdate : $sdate;
          // $curr_contract_start = new Datetime($curr_contract['contract_start']);

          $worksched = json_decode($row['work_sched']);
          $worksched = (array)$worksched;
          $days = array('mon','tue','wed','thu','fri','sat','sun');

          // CURRENT CONTRACT COMPUTATION
          for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
            $ldate_raw = $x->format('Y-m-d');
            $ldate = $this->db->escape($ldate_raw);

            ### cut off days ###
            $d_main = new Datetime($ldate_raw);
            $wdate_main = strtolower($d_main->format('D'));
            for ($a=0; $a < 7; $a++) {
              if($wdate_main == $days[$a]){
                if($worksched[$days[$a]][0] != ""){
                  $cut_off_days += 1;
                }else{
                }
              }
            }

            $timelog = filter_array_payroll($timelog_query->result(), array('date' => $ldate_raw));
            // print_r($timelog);
            $holiday = filter_array_payroll($holiday_query->result(), array('date' => $ldate_raw));

            ### if the day is holiday and has man hours
            if(count((array)$timelog) > 0 && count((array)$holiday) > 0){

              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              if($timelog[0]->man_hours > 0){
                ### regular holiday
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;

                  // if($timelog[0]->regular_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){
                        ### weekly ###
                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;
                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }
                        ### monthly || semi-monthly ###
                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;
                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }
                      }
                    }
                  // }
                }

                ### special non working holiday
                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;
                  // if($timelog[0]->special_non_working_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){

                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }

                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }

                      }
                    }
                  // }
                }

              }else{
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $reg_holiday_pay += $daily_rate;
                  }
                }

                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $spl_holiday_pay += $daily_rate;
                  }
                }
              }

            }

            ### if has man_hours only
            if(count((array)$timelog) > 0 && count((array)$holiday) == 0){
              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              for ($i=0; $i < 7; $i++) {
                if($wdate == $days[$i]){
                  if($worksched[$days[$i]][0] != ""){
                    if($row['frequency'] > 2){
                      $gross_pay += $daily_rate;
                    }
                    $wdays += 1;
                  }else{ // for sunday or off day
                    $gross_pay += ($row['frequency'] > 2)
                    ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                    : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly

                    if($wdate == 'sun'){
                      $sunday += 1;
                      $sunday_pay += ($row['frequency'] > 2)
                      ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                      : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly
                    }
                  }
                }
              }
            }

            ### holiday only
            if(count((array)$timelog) == 0 && count((array)$holiday) > 0){
              // $holiday = $holiday;
              if($holiday[0]->holiday_type == 'regular'){
                $reg_holiday += 1;
                if($row['regular_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $reg_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $reg_holiday_pay += $daily_rate;
                }
              }

              if($holiday[0]->holiday_type == 'special'){
                $spl_holiday += 1;
                if($row['special_non_working_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $spl_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $spl_holiday_pay += $daily_rate;
                }
              }
            }

            ### absent
            if(count((array)$timelog) == 0 && count((array)$holiday) == 0){
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
                    if($row['leave_pay'] == 'yes'){
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($check_leave->row()->paid == 'with_pay'){
                          if($row['frequency'] > 2){
                            $gross_pay += $daily_rate;
                          }
                          $wdays += 1;
                        }else{
                          if($row['frequency'] <= 2){
                            $gross_pay -= $daily_rate;
                            $absent_deduction += $daily_rate;
                          }
                          $absent += 1;
                        }
                      }
                    }else{
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }
                    }
                  }
                }
              }
            }
          }

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
              data-nightdiff_hrs = "'.$night_diffhours.'"
              data-night_diff = "'.number_format($night_differentials,2).'"
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

        ### NO CONTRACT OVERLAP ON CUTOFF ### => CURRENT CONTRACT
        if($prev_contract == '' && $curr_contract != ''){
          // GET AVAILABLE CONTRACT
          $row = $curr_contract;
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

          // NIGHT DIFFERENTIALS
          $night_diffhours = 0;
          $night_differentials = 0;
          if($nightdiff_status == 'on'){
            $nightdiff_rate = ($row['hourly_rate'] * ($nightdiff['percent'] / 100));
            $night_differentials = $nightdiff_rate * $row['night_diff'];
            $night_diffhours = $row['night_diff'];
          }

          $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
          $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

          // $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];
          $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
          $cash_advance     = ($row['currency'] == "PHP") ? $row['cash_advance'] : (float)$row['cash_advance'] / (float)$row['ex_rate'];
          $salary_deduction = ($row['currency'] == "PHP") ? $row['salary_deduction'] : (float)$row['salary_deduction'] / (float)$row['ex_rate'];

          $sss_year_contri        = ($row['currency'] == "PHP") ? $row['sss_year_contri'] : (float)$row['sss_year_contri'] / (float)$row['ex_rate'];
          $philhealth_year_contri = ($row['currency'] == "PHP") ? $row['philhealth_year_contri'] : (float)$row['philhealth_year_contri'] / (float)$row['ex_rate'];
          $pagibig_year_contri    = ($row['currency'] == "PHP") ? $row['pagibig_year_contri'] : (float)$row['pagibig_year_contri'] / (float)$row['ex_rate'];

          $additionals = $row['ot_pays'] + $additional_pays + $night_differentials;
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
          $total_tax = ($row['tin_no'] != '') ? $total_tax : 0;

          $cut_off_days = 0;
          $wdays = 0;
          $absent = 0;
          $absent_deduction = 0;
          $late_deduct = ($row['late_deduct'] == null) ? $min_rate * $row['late'] :$row['late_deduct'];
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
          $fdate = new Datetime($row['contract_start']);
          // $sdate = ($sdate < $fdate) ? $fdate : $sdate;

          $worksched = json_decode($row['work_sched']);
          $worksched = (array)$worksched;
          $days = array('mon','tue','wed','thu','fri','sat','sun');

          for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
            $ldate_raw = $x->format('Y-m-d');
            $ldate = $this->db->escape($ldate_raw);

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

            $timelog = filter_array_payroll($timelog_query->result(), array('date' => $ldate_raw));
            // print_r($timelog);
            $holiday = filter_array_payroll($holiday_query->result(), array('date' => $ldate_raw));

            ### if the day is holiday and has man hours
            if(count((array)$timelog) > 0 && count((array)$holiday) > 0){

              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              if($timelog[0]->man_hours > 0){
                ### regular holiday
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;

                  // if($timelog[0]->regular_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){
                        ### weekly ###
                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;
                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }
                        ### monthly || semi-monthly ###
                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;
                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }
                      }
                    }
                  // }
                }

                ### special non working holiday
                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;

                  // if($timelog[0]->special_non_working_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){

                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }

                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }

                      }
                    }
                  // }
                }

              }else{
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $reg_holiday_pay += $daily_rate;
                  }
                }

                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $spl_holiday_pay += $daily_rate;
                  }
                }
              }

            }

            ### if has man_hours only
            if(count((array)$timelog) > 0 && count((array)$holiday) == 0){
              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              for ($i=0; $i < 7; $i++) {
                if($wdate == $days[$i]){
                  if($worksched[$days[$i]][0] != ""){
                    if($row['frequency'] > 2){
                      $gross_pay += $daily_rate;
                    }
                    $wdays += 1;
                  }else{ // for sunday or off day
                    $gross_pay += ($row['frequency'] > 2)
                    ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                    : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly

                    if($wdate == 'sun'){
                      $sunday += 1;
                      $sunday_pay += ($row['frequency'] > 2)
                      ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                      : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly
                    }
                  }
                }
              }
            }

            ### holiday only
            if(count((array)$timelog) == 0 && count((array)$holiday) > 0){
              // $holiday = $holiday;
              if($holiday[0]->holiday_type == 'regular'){
                $reg_holiday += 1;
                if($row['regular_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $reg_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $reg_holiday_pay += $daily_rate;
                }
              }

              if($holiday[0]->holiday_type == 'special'){
                $spl_holiday += 1;
                if($row['special_non_working_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $spl_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $spl_holiday_pay += $daily_rate;
                }
              }
            }

            ### absent
            if(count((array)$timelog) == 0 && count((array)$holiday) == 0){
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
                    if($row['leave_pay'] == 'yes'){
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($check_leave->row()->paid == 'with_pay'){
                          if($row['frequency'] > 2){
                            $gross_pay += $daily_rate;
                          }
                          $wdays += 1;
                        }else{
                          if($row['frequency'] <= 2){
                            $gross_pay -= $daily_rate;
                            $absent_deduction += $daily_rate;
                          }
                          $absent += 1;
                        }
                      }
                    }else{
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }
                    }
                  }
                }
              }
            }

          }

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
              data-nightdiff_hrs = "'.$night_diffhours.'"
              data-night_diff = "'.number_format($night_differentials,2).'"
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

      }

    }

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

  public function get_payslip_data($company,$from,$to,$type,$frequency,$pay_day,$id,$ref_no){
    $cfrom = $from;
    $cto = $to;
    $company = $this->db->escape($company);
    $from = $this->db->escape($from);
    $to = $this->db->escape($to);
    $type = $this->db->escape($type);
    $frequency = $this->db->escape($frequency);
    $pay_day = $this->db->escape($pay_day);
    $id = $this->db->escape($id);

    $requestData = $_REQUEST;

    $columns = array(
			0 => 'emp_id',
      1 => 'fullname',
			2 => 'gross_pay',
      3 => 'deduction',
      4 => 'additionals',
      5 => 'net_pay'
		);

    $nightdiff_status = 'off';
    $night_diff_sql = "SELECT * FROM hris_nightdiff_settings WHERE enabled = 1";
    $nightdiff = $this->db->query($night_diff_sql);
    if($nightdiff->num_rows() > 0){
      $nightdiff = $nightdiff->row_array();
      $nightdiff_status = $nightdiff['status'];
    }


    $affected_user_sql = "SELECT a.id, b.contract_start, b.contract_end, b.id as contract_id,
     CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, a.first_month, a.tin_no,
     @year_diff := (SELECT (CASE WHEN DATEDIFF('2020-01-01', $pay_day) < 365 THEN 0 ELSE DATEDIFF('2020-01-01', $pay_day) / 365 END)) as year_diff,
     @ph_contri := (SELECT (CASE WHEN ((.5 * year_diff) + 3.0) > 5.0 THEN 5.0 / 100 ELSE ((.5 * year_diff) + 3.0) / 100 END)) as ph_contri,
     @emp_id := a.employee_idno as emp_idnum,
     @daily_rate := (CASE WHEN e.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
     @hourly_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours),2) as hourly_rate,
     @min_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours) / 60,2) as min_rate,
     @man_hours := (SELECT SUM(man_hours) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as man_hours,
     @night_diff := (SELECT SUM(night_diff) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as night_diff,
     @late := (SELECT SUM(late) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as late,
     @undertime := (SELECT SUM(undertime) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as undertime,
     @salary_deduction := (SELECT SUM(amount) FROM salary_deduction WHERE employee_idno = a.employee_idno AND status = 'certified' AND DATE(date_created) BETWEEN $from AND $to) as salary_deduction,
     @cash_advance := (SELECT SUM(((amount + rate) / terms) / $frequency) FROM cash_advance_tran WHERE employee_id = a.employee_idno AND status = 'certified' AND (date_of_effectivity >= date_of_effectivity AND date_of_effectivity <= $to) AND date_end >= $to) as cash_advance,
     @sss := (SELECT (ss_ee / $frequency) FROM sss WHERE id = b.sss AND enabled = 1 AND a.sss_no != '') as sss,
     @sss_loan := (SELECT (monthly_amortization / $frequency) FROM hris_sss_loans WHERE status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno AND $pay_day BETWEEN sss_loan_start AND sss_loan_end) as sss_loan,
     @pagibig_loan := (SELECT (monthly_amortization / $frequency) FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan,
     @sss_loan_id := (SELECT id as sss_loan_id FROM hris_sss_loans WHERE $pay_day BETWEEN sss_loan_start AND sss_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as sss_loan_id,
     @pagibig_loan_id := (SELECT id as pagibig_loan_id FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan_id,
     @philhealth := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN (((b.base_pay * ph_contri) / 2) / $frequency) WHEN basic_mo_sal1 > 40000 THEN (employee_share1 / $frequency) ELSE ((employee_share1 / 2) / $frequency) END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1 AND a.philhealth_no != '') as philhealth,
     @pagibig := (SELECT (CASE WHEN employee_share > 1.00 THEN (5000 * (employee_share / 100)) / $frequency ELSE (b.base_pay * (employee_share / 100)) / $frequency END) FROM pagibig WHERE id = b.pagibig AND enabled = 1 AND pagibig_no != '') as pagibig,
     @ot_pays := (SELECT (CASE WHEN e.frequency >= 4 THEN ROUND(SUM((b.total_sal / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60)),2) ELSE ROUND(SUM((((daily_rate) / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60))), 2) END) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_pays,
     @ot_min := (SELECT SUM(minutes_of_overtime) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_min,
     @additional_pays := (SELECT SUM(amount) FROM additional_pays WHERE employee_id = a.employee_idno AND status = 'certified' AND date_issued BETWEEN $from AND $to) as additional_pays,
     @sss_year_contri := (SELECT (ss_ee * 12) FROM sss WHERE id = b.sss AND enabled = 1) as sss_year_contri,
     @philhealth_year_contri := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN ((b.base_pay * ph_contri) / 2) * 12 ELSE (employee_share1 / 2) * 12 END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1) as philhealth_year_contri,
     @pagibig_year_contri := (SELECT (CASE WHEN b.pagibig > 1 THEN (5000 * (employee_share / 100)) * 12 ELSE (b.base_pay * (employee_share / 100)) * 12 END) FROM pagibig WHERE id = b.pagibig AND enabled = 1) as pagibig_year_contri,
     k.aibLowerLimit as lower_limit, k.aibUpperLimit as upper_limit, k.tr1LowerLimit as tax_lower_limit,
     (k.tr1ExcessLimit / 100) as excess_limit, j.empstatusid as emp_status, b.total_sal_converted,
     e.description as pay_type, b.currency, f.exchange_rate as ex_rate, b.contract_ref_no, g.id as ca_id,
     h.work_sched, j.regular_holiday, j.special_non_working_holiday, j.leave_pay, e.description as pay_type, e.frequency, b.total_sal
     FROM employee_record a
     INNER JOIN contract b ON a.id = b.contract_emp_id
     INNER JOIN hris_companies c ON b.company_id = c.id
     INNER JOIN time_record_summary d ON a.employee_idno = d.employee_idno
     LEFT JOIN paytype e ON b.paytype = e.paytypeid
     LEFT JOIN hris_exchange_rates f ON b.currency = f.currency_code
     LEFT JOIN cash_advance_tran g ON a.employee_idno = g.employee_id
     LEFT JOIN work_schedule h ON b.work_sched_id = h.id
     LEFT JOIN work_schedule i ON a.employee_idno = i.emp_idno
     LEFT JOIN empstatus j ON b.emp_status = j.empstatusid
     LEFT JOIN tax k ON b.tax = k.id
     WHERE d.date_created >= $from AND d.date_created <= $to AND b.paytype = $type AND c.id = $company
     AND b.contract_status = 'active' AND a.enabled = 1 AND b.enabled = 1
     AND c.enabled = 1 AND d.enabled = 1 AND e.enabled = 1 AND f.enabled = 1
     AND a.employee_idno = $id";

    $affected_user_query = $this->db->query($affected_user_sql);
    // return $this->db->last_query();

    $totalData = $affected_user_query->num_rows();
		$totalFiltered = $affected_user_query->num_rows();

    $data = array();
    $data_main = array();

    $d1 = new Datetime($cfrom);
    $d2 = new Datetime($cto);
    $date = $d1->format('M d, Y')."-".$d2->format('M d, Y');
    $p_type = "";

    if($affected_user_query->num_rows() > 0){
      foreach($affected_user_query->result_array() as $row){
        $nestedData = array();
        $curr_contract = $row;
        $prev_contract = '';
        $emp_idnum = $this->db->escape($row['emp_idnum']);

        // GET ALL TIMERECORD OF THIS USER
        $timelog_sql = "SELECT c.man_hours, e.regular_holiday, e.special_non_working_holiday,
          c.date_created as date, e.empstatusid as employee_type
          FROM employee_record a
          LEFT JOIN contract b ON a.id = b.contract_emp_id
          LEFT JOIN time_record_summary c ON a.employee_idno = c.employee_idno
          LEFT JOIN empstatus e ON b.emp_status = e.empstatusid
          WHERE c.date_created BETWEEN $from AND $to AND a.employee_idno = $emp_idnum
          AND b.contract_status = 'active' AND a.enabled = 1 AND c.absent = 0 ORDER BY c.date_created ASC";

        $holiday_sql = "SELECT a.date, b.type as holiday_type, payratio, payratio2
          FROM holidays_tran a
          LEFT JOIN holidaytype b ON a.holiday_type = b.holidaytypeid
          WHERE a.date BETWEEN $from AND $to AND a.enabled = 1";

        $timelog_query = $this->db->query($timelog_sql);
        $holiday_query = $this->db->query($holiday_sql);

        ### CHECK CONTRACT OVERLAP ###
        if(($row['contract_start'] > $cfrom && $row['contract_start'] < $cto) || ($row['contract_start'] > $cto)){
          // $curr_contract = $row;
          $id = $this->db->escape($row['emp_idnum']);
          $prev_con_sql = "SELECT a.id, b.contract_start, b.contract_end, b.id as contract_id,
           CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, a.first_month, a.tin_no,
           @year_diff := (SELECT (CASE WHEN DATEDIFF('2020-01-01', $pay_day) < 365 THEN 0 ELSE DATEDIFF('2020-01-01', $pay_day) / 365 END)) as year_diff,
           @ph_contri := (SELECT (CASE WHEN ((.5 * year_diff) + 3.0) > 5.0 THEN 5.0 / 100 ELSE ((.5 * year_diff) + 3.0) / 100 END)) as ph_contri,
           @emp_id := a.employee_idno as emp_idnum,
           @daily_rate := (CASE WHEN e.frequency >= 4 THEN (b.total_sal) ELSE ROUND((b.total_sal * 12) / 313,2) END) as daily_rate,
           @hourly_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours),2) as hourly_rate,
           @min_rate := ROUND(@daily_rate / (h.total_whours - h.total_bhours) / 60,2) as min_rate,
           @man_hours := (SELECT SUM(man_hours) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as man_hours,
           @night_diff := (SELECT SUM(night_diff) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as night_diff,
           @late := (SELECT SUM(late) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as late,
           @undertime := (SELECT SUM(undertime) FROM time_record_summary WHERE employee_idno = a.employee_idno AND date_created BETWEEN $from AND $to) as undertime,
           @salary_deduction := (SELECT SUM(amount) FROM salary_deduction WHERE employee_idno = a.employee_idno AND status = 'certified' AND DATE(date_created) BETWEEN $from AND $to) as salary_deduction,
           @cash_advance := (SELECT SUM(((amount + rate) / terms) / $frequency) FROM cash_advance_tran WHERE employee_id = a.employee_idno AND status = 'certified' AND (date_of_effectivity >= date_of_effectivity AND date_of_effectivity <= $to) AND date_end >= $to) as cash_advance,
           @sss := (SELECT (ss_ee / $frequency) FROM sss WHERE id = b.sss AND enabled = 1 AND a.sss_no != '') as sss,
           @sss_loan := (SELECT (monthly_amortization / $frequency) FROM hris_sss_loans WHERE status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno AND $pay_day BETWEEN sss_loan_start AND sss_loan_end) as sss_loan,
           @pagibig_loan := (SELECT (monthly_amortization / $frequency) FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan,
           @sss_loan_id := (SELECT id as sss_loan_id FROM hris_sss_loans WHERE $pay_day BETWEEN sss_loan_start AND sss_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as sss_loan_id,
           @pagibig_loan_id := (SELECT id as pagibig_loan_id FROM hris_pagibig_loans WHERE $pay_day BETWEEN pagibig_loan_start AND pagibig_loan_end AND status = 'active' AND enabled = 1 AND employee_idno = a.employee_idno) as pagibig_loan_id,
           @philhealth := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN (((b.base_pay * ph_contri) / 2) / $frequency) WHEN basic_mo_sal1 > 40000 THEN (employee_share1 / $frequency) ELSE ((employee_share1 / 2) / $frequency) END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1 AND a.philhealth_no != '') as philhealth,
           @pagibig := (SELECT (CASE WHEN employee_share > 1.00 THEN (5000 * (employee_share / 100)) / $frequency ELSE (b.base_pay * (employee_share / 100)) / $frequency END) FROM pagibig WHERE id = b.pagibig AND enabled = 1 AND pagibig_no != '') as pagibig,
           @ot_pays := (SELECT (CASE WHEN e.frequency >= 4 THEN ROUND(SUM((b.total_sal / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60)),2) ELSE ROUND(SUM((((daily_rate) / (h.total_whours - h.total_bhours)) * 1.25 * (minutes_of_overtime / 60))), 2) END) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_pays,
           @ot_min := (SELECT SUM(minutes_of_overtime) FROM overtime_pays WHERE type = 'overtime' AND status = 'certified' AND employee_id = a.employee_idno AND date_rendered BETWEEN $from AND $to) as ot_min,
           @additional_pays := (SELECT SUM(amount) FROM additional_pays WHERE employee_id = a.employee_idno AND status = 'certified' AND date_issued BETWEEN $from AND $to) as additional_pays,
           @sss_year_contri := (SELECT (ss_ee * 12) FROM sss WHERE id = b.sss AND enabled = 1) as sss_year_contri,
           @philhealth_year_contri := (SELECT (CASE WHEN basic_mo_sal1 > 10000 THEN ((b.base_pay * ph_contri) / 2) * 12 ELSE (employee_share1 / 2) * 12 END) FROM philhealth WHERE phID = b.philhealth AND enabled = 1) as philhealth_year_contri,
           @pagibig_year_contri := (SELECT (CASE WHEN b.pagibig > 1 THEN (5000 * (employee_share / 100)) * 12 ELSE (b.base_pay * (employee_share / 100)) * 12 END) FROM pagibig WHERE id = b.pagibig AND enabled = 1) as pagibig_year_contri,
           k.aibLowerLimit as lower_limit, k.aibUpperLimit as upper_limit, k.tr1LowerLimit as tax_lower_limit,
           (k.tr1ExcessLimit / 100) as excess_limit, j.empstatusid as emp_status, b.total_sal_converted,
           e.description as pay_type, b.currency, f.exchange_rate as ex_rate, b.contract_ref_no, g.id as ca_id,
           h.work_sched, j.regular_holiday, j.special_non_working_holiday, j.leave_pay, e.description as pay_type, e.frequency, b.total_sal
           FROM employee_record a
           INNER JOIN contract b ON a.id = b.contract_emp_id
           INNER JOIN hris_companies c ON b.company_id = c.id
           LEFT JOIN paytype e ON b.paytype = e.paytypeid
           LEFT JOIN hris_exchange_rates f ON b.currency = f.currency_code
           LEFT JOIN cash_advance_tran g ON a.employee_idno = g.employee_id
           LEFT JOIN work_schedule h ON b.work_sched_id = h.id
           LEFT JOIN work_schedule i ON a.employee_idno = i.emp_idno
           LEFT JOIN empstatus j ON b.emp_status = j.empstatusid
           LEFT JOIN tax k ON b.tax = k.id
           WHERE b.paytype = $type AND c.id = $company AND a.employee_idno = $id
           AND b.contract_status = 'inactive' AND a.enabled = 1 AND b.enabled = 1
           AND c.enabled = 1 AND e.enabled = 1 AND f.enabled = 1
           ORDER BY b.created_at DESC LIMIT 1";

          $prev_con_query = $this->db->query($prev_con_sql);
          // die($this->db->last_query());

          if($prev_con_query->num_rows() > 0){ //
            $prev_contract = $prev_con_query->row_array();
          }

        }

        ### CONTRACT OVERLAP ON CUTOFF ### => PREVIOUS CONTRACT
        if($prev_contract != ''){

          $row = ($prev_contract == '') ? $curr_contract : $prev_contract;
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

          // NIGHT DIFFERENTIALS
          $night_diffhours = 0;
          $night_differentials = 0;
          if($nightdiff_status == 'on'){
            $nightdiff_rate = ($row['hourly_rate'] * ($nightdiff['percent'] / 100));
            $night_differentials = $nightdiff_rate * $row['night_diff'];
            $night_diffhours = $row['night_diff'];
          }
          // $sss          = ($row['currency'] == "PHP") ? $row['sss'] : (float)$row['sss'] / (float)$row['ex_rate'];
          // $philhealth   = ($row['currency'] == "PHP") ? $row['philhealth'] : (float)$row['philhealth'] / (float)$row['ex_rate'];
          // $pagibig      = ($row['currency'] == "PHP") ? $row['pagibig'] : (float)$row['pagibig'] / (float)$row['ex_rate'];
          $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
          $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

          // $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];
          $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
          $cash_advance     = ($row['currency'] == "PHP") ? $row['cash_advance'] : (float)$row['cash_advance'] / (float)$row['ex_rate'];
          $salary_deduction = ($row['currency'] == "PHP") ? $row['salary_deduction'] : (float)$row['salary_deduction'] / (float)$row['ex_rate'];

          $sss_year_contri        = ($row['currency'] == "PHP") ? $row['sss_year_contri'] : (float)$row['sss_year_contri'] / (float)$row['ex_rate'];
          $philhealth_year_contri = ($row['currency'] == "PHP") ? $row['philhealth_year_contri'] : (float)$row['philhealth_year_contri'] / (float)$row['ex_rate'];
          $pagibig_year_contri    = ($row['currency'] == "PHP") ? $row['pagibig_year_contri'] : (float)$row['pagibig_year_contri'] / (float)$row['ex_rate'];

          $additionals = $row['ot_pays'] + $additional_pays + $night_differentials;
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
          $total_tax = ($row['tin_no'] != '') ? $total_tax : 0;
          
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
          $fdate = new Datetime($row['contract_start']);
          // $sdate = ($sdate < $fdate) ? $fdate : $sdate;
          // $curr_contract_start = new Datetime($curr_contract['contract_start']);


          $worksched = json_decode($row['work_sched']);
          $worksched = (array)$worksched;
          $days = array('mon','tue','wed','thu','fri','sat','sun');

          // CURRENT CONTRACT COMPUTATION
          for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
            $ldate_raw = $x->format('Y-m-d');
            $ldate = $this->db->escape($ldate_raw);

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

            $timelog = filter_array_payroll($timelog_query->result(), array('date' => $ldate_raw));
            // print_r($timelog);
            $holiday = filter_array_payroll($holiday_query->result(), array('date' => $ldate_raw));

            ### if the day is holiday and has man hours
            if(count((array)$timelog) > 0 && count((array)$holiday) > 0){

              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              if($timelog[0]->man_hours > 0){
                ### regular holiday
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;

                  // if($timelog[0]->regular_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){
                        ### weekly ###
                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;
                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }
                        ### monthly || semi-monthly ###
                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;
                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }
                      }
                    }
                  // }
                }

                ### special non working holiday
                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;

                  // if($timelog[0]->special_non_working_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){

                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }

                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }

                      }
                    }
                  // }
                }

              }else{
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $reg_holiday_pay += $daily_rate;
                  }
                }

                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $spl_holiday_pay += $daily_rate;
                  }
                }
              }

            }

            ### if has man_hours only
            if(count((array)$timelog) > 0 && count((array)$holiday) == 0){
              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              for ($i=0; $i < 7; $i++) {
                if($wdate == $days[$i]){
                  if($worksched[$days[$i]][0] != ""){
                    if($row['frequency'] > 2){
                      $gross_pay += $daily_rate;
                    }
                    $wdays += 1;
                  }else{ // for sunday or off day
                    $gross_pay += ($row['frequency'] > 2)
                    ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                    : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly

                    if($wdate == 'sun'){
                      $sunday += 1;
                      $sunday_pay += ($row['frequency'] > 2)
                      ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                      : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly
                    }
                  }
                }
              }
            }

            ### holiday only
            if(count((array)$timelog) == 0 && count((array)$holiday) > 0){
              // $holiday = $holiday;
              if($holiday[0]->holiday_type == 'regular'){
                $reg_holiday += 1;
                if($row['regular_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $reg_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $reg_holiday_pay += $daily_rate;
                }
              }

              if($holiday[0]->holiday_type == 'special'){
                $spl_holiday += 1;
                if($row['special_non_working_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $spl_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $spl_holiday_pay += $daily_rate;
                }
              }
            }

            ### absent
            if(count((array)$timelog) == 0 && count((array)$holiday) == 0){
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
                    if($row['leave_pay'] == 'yes'){
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($check_leave->row()->paid == 'with_pay'){
                          if($row['frequency'] > 2){
                            $gross_pay += $daily_rate;
                          }
                          $wdays += 1;
                        }else{
                          if($row['frequency'] <= 2){
                            $gross_pay -= $daily_rate;
                            $absent_deduction += $daily_rate;
                          }
                          $absent += 1;
                        }
                      }
                    }else{
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }
                    }
                  }
                }
              }
            }
          }

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
            "ex_rate" => $row['ex_rate'],
            "nightdiff_hrs" => $night_diffhours,
            "night_diff" => number_format($night_differentials,2)
          );
        }

        ### NO CONTRACT OVERLAP ON CUTOFF ### => CURRENT CONTRACT
        if($prev_contract == '' && $curr_contract != ''){
          // GET AVAILABLE CONTRACT
          $row = $curr_contract;
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

          // NIGHT DIFFERENTIALS
          $night_diffhours = 0;
          $night_differentials = 0;
          if($nightdiff_status == 'on'){
            $nightdiff_rate = ($row['hourly_rate'] * ($nightdiff['percent'] / 100));
            $night_differentials = $nightdiff_rate * $row['night_diff'];
            $night_diffhours = $row['night_diff'];
          }

          // $sss          = ($row['currency'] == "PHP") ? $row['sss'] : (float)$row['sss'] / (float)$row['ex_rate'];
          // $philhealth   = ($row['currency'] == "PHP") ? $row['philhealth'] : (float)$row['philhealth'] / (float)$row['ex_rate'];
          // $pagibig      = ($row['currency'] == "PHP") ? $row['pagibig'] : (float)$row['pagibig'] / (float)$row['ex_rate'];
          $sss_loan     = ($row['currency'] == "PHP") ? $row['sss_loan'] : (float)$row['sss_loan'] / (float)$row['ex_rate'];
          $pagibig_loan = ($row['currency'] == "PHP") ? $row['pagibig_loan'] : (float)$row['pagibig_loan'] / (float)$row['ex_rate'];

          // $ot_pays  = ($row['currency'] == "PHP") ? $row['ot_pays'] : (float)$row['ot_pays'] / (float)$row['ex_rate'];
          $additional_pays  = ($row['currency'] == "PHP") ? $row['additional_pays'] : (float)$row['additional_pays'] / (float)$row['ex_rate'];
          $cash_advance     = ($row['currency'] == "PHP") ? $row['cash_advance'] : (float)$row['cash_advance'] / (float)$row['ex_rate'];
          $salary_deduction = ($row['currency'] == "PHP") ? $row['salary_deduction'] : (float)$row['salary_deduction'] / (float)$row['ex_rate'];

          $sss_year_contri        = ($row['currency'] == "PHP") ? $row['sss_year_contri'] : (float)$row['sss_year_contri'] / (float)$row['ex_rate'];
          $philhealth_year_contri = ($row['currency'] == "PHP") ? $row['philhealth_year_contri'] : (float)$row['philhealth_year_contri'] / (float)$row['ex_rate'];
          $pagibig_year_contri    = ($row['currency'] == "PHP") ? $row['pagibig_year_contri'] : (float)$row['pagibig_year_contri'] / (float)$row['ex_rate'];

          $additionals = $row['ot_pays'] + $additional_pays + $night_differentials;
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
          $total_tax = ($row['tin_no'] != '') ? $total_tax : 0;

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
          $fdate = new Datetime($row['contract_start']);
          // $sdate = ($sdate < $fdate) ? $fdate : $sdate;

          $worksched = json_decode($row['work_sched']);
          $worksched = (array)$worksched;
          $days = array('mon','tue','wed','thu','fri','sat','sun');

          for($x = $sdate; $x <= $edate; $x->modify('+1 day')){
            $ldate_raw = $x->format('Y-m-d');
            $ldate = $this->db->escape($ldate_raw);

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

            $timelog = filter_array_payroll($timelog_query->result(), array('date' => $ldate_raw));
            // print_r($timelog);
            $holiday = filter_array_payroll($holiday_query->result(), array('date' => $ldate_raw));

            ### if the day is holiday and has man hours
            if(count((array)$timelog) > 0 && count((array)$holiday) > 0){

              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              if($timelog[0]->man_hours > 0){
                ### regular holiday
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;

                  // if($timelog[0]->regular_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){
                        ### weekly ###
                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;
                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }
                        ### monthly || semi-monthly ###
                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $reg_holiday_pay += $daily_rate * $holiday[0]->payratio2;
                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $reg_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }
                      }
                    }
                  // }
                }

                ### special non working holiday
                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;

                  // if($timelog[0]->special_non_working_holiday == 'yes'){
                    for ($i=0; $i < 7; $i++) {
                      if($wdate == $days[$i]){

                        if($row['frequency'] > 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * $holiday[0]->payratio;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * $holiday[0]->payratio2;
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * $holiday[0]->payratio2 *  $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * $holiday[0]->payratio2 * $timelog[0]->man_hours;
                          }
                        }

                        if($row['frequency'] <= 2){
                          if($worksched[$days[$i]][0] != ""){
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio - 1) * $timelog[0]->man_hours;
                          }else{
                            // $gross_pay += $daily_rate * ($holiday[0]->payratio2 / 2);
                            // $spl_holiday_pay += $daily_rate * $holiday[0]->payratio2;

                            $gross_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                            $spl_holiday_pay += $hourly_rate * ($holiday[0]->payratio2 - 1) * $timelog[0]->man_hours;
                          }
                        }

                      }
                    }
                  // }
                }

              }else{
                if($holiday[0]->holiday_type == 'regular'){
                  $reg_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $reg_holiday_pay += $daily_rate;
                  }
                }

                if($holiday[0]->holiday_type == 'special'){
                  $spl_holiday += 1;
                  if($timelog[0]->regular_holiday == 'yes'){
                    $gross_pay += ($row['frequency'] > 2)? $daily_rate : 0;
                    $spl_holiday_pay += $daily_rate;
                  }
                }
              }

            }

            ### if has man_hours only
            if(count((array)$timelog) > 0 && count((array)$holiday) == 0){
              $d = new Datetime($timelog[0]->date);
              $wdate = strtolower($d->format('D'));

              for ($i=0; $i < 7; $i++) {
                if($wdate == $days[$i]){
                  if($worksched[$days[$i]][0] != ""){
                    if($row['frequency'] > 2){
                      $gross_pay += $daily_rate;
                    }
                    $wdays += 1;
                  }else{ // for sunday or off day
                    $gross_pay += ($row['frequency'] > 2)
                    ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                    : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly

                    if($wdate == 'sun'){
                      $sunday += 1;
                      $sunday_pay += ($row['frequency'] > 2)
                      ? $hourly_rate * 1.3 * $timelog[0]->man_hours // daily rate
                      : $hourly_rate * 1.3 * $timelog[0]->man_hours; // semi-monthly
                    }
                  }
                }
              }
            }

            ### holiday only
            if(count((array)$timelog) == 0 && count((array)$holiday) > 0){
              // $holiday = $holiday;
              if($holiday[0]->holiday_type == 'regular'){
                $reg_holiday += 1;
                if($row['regular_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $reg_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $reg_holiday_pay += $daily_rate;
                }
              }

              if($holiday[0]->holiday_type == 'special'){
                $spl_holiday += 1;
                if($row['special_non_working_holiday'] == 'yes'){
                  $gross_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  $spl_holiday_pay += ($row['frequency'] > 2) ? $daily_rate : 0;
                  // $spl_holiday_pay += $daily_rate;
                }
              }
            }

            ### absent
            if(count((array)$timelog) == 0 && count((array)$holiday) == 0){
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
                    if($row['leave_pay'] == 'yes'){
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($check_leave->row()->paid == 'with_pay'){
                          if($row['frequency'] > 2){
                            $gross_pay += $daily_rate;
                          }
                          $wdays += 1;
                        }else{
                          if($row['frequency'] <= 2){
                            $gross_pay -= $daily_rate;
                            $absent_deduction += $daily_rate;
                          }
                          $absent += 1;
                        }
                      }
                    }else{
                      if($check_leave->num_rows() == 0){ // NO LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }else{ // HAS LEAVE
                        if($row['frequency'] <= 2){
                          $gross_pay -= $daily_rate;
                          $absent_deduction += $daily_rate;
                        }
                        $absent += 1;
                      }
                    }
                  }
                }
              }
            }

          }

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
            "ex_rate" => $row['ex_rate'],
            "nightdiff_hrs" => $night_diffhours,
            "night_diff" => number_format($night_differentials,2)
          );
        }

      }

    }
    // print_r($data);
    // die();
    return $data;

  }

}
