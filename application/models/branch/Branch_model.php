<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Branch_model extends CI_Model {

  public $app_db;

  public function get_branch_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'company_name',
      1 => 'company_code',
      2 => 'country_code',
      3 => 'timezone',
      4 => 'enabled'
    );

    $sql = "SELECT * FROM hris_branch";

    switch ($search->filter) {
      case 'divBname':
        $branch_name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (branch_name LIKE $branch_name)";
        break;
      case 'divBcode':
        $branch_code = $this->db->escape($search->search);
        $sql .= " AND branch_code = $branch_code";
        break;
      case 'divTimezone':
        $timezone = $this->db->escape($search->search);
        $sql .= " AND timezone = $timezone";
        break;
      case 'divCountry':
        $country = $this->db->escape($search->search);
        $sql .= " AND country_code = $country";
        break;
      default:
        break;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY enabled DESC, branch_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['branch_name'];
      $nestedData[] = $row['branch_code'];
      $nestedData[] = $row['timezone'];
      $nestedData[] = $row['country_code'];
      $nestedData[] = ($row['enabled'] == 1)
      ? '<center><span class="badge badge-pill badge-success">Active</span></center>'
      : '<center><span class="badge badge-pill badge-danger">Inactive</span></center>';
      $nestedData[] = ($row['location'] == 'online')
      ? '<center><span class="badge badge-success badge-pill">Online</span></center>'
      : '<center><span class="badge badge-danger badge-pill">Offline</span></center>';

      if($row['enabled'] == 0){
        $buttons =
        '
        <center>
          <button class="btn btn-info btn_activate" style = "width:90px;"
            data-activateid = "'.en_dec('en',$row['id']).'"
            data-bname = "'.$row['branch_name'].'"
          >
            Activate
          </button>
        </center>
        ';
      }else{
        $buttons =
        '
          <center>
            <button class="btn btn-primary btn_view" style = "width:90px;"
              data-uid = "'.en_dec('en',$row['id']).'"
              data-bname = "'.$row['branch_name'].'"
              data-bcode = "'.$row['branch_code'].'"
              data-dbname = "'.$row['database_name'].'"
              data-username = "'.$row['username'].'"
              data-password = "'.$row['password'].'"
              data-fname = "'.$row['fname'].'"
              data-mname = "'.$row['mname'].'"
              data-lname = "'.$row['lname'].'"
              data-timezone = "'.$row['timezone'].'"
              data-country_code = "'.$row['country_code'].'"
              data-location = "'.$row['location'].'"
            >
              View
            </button>
            <button class="btn btn-danger btn_del" style = "width:90px;"
             data-delid = "'.en_dec('en',$row['id']).'"
             data-bname = "'.$row['branch_name'].'"
            >
              Deactivate
            </button>
          </center>
        ';
      }

      $nestedData[] = $buttons;

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_all_branch(){
    $sql = "SELECT * FROM hris_branch WHERE enabled = 1 ORDER BY branch_name ASC";
    return $this->db->query($sql);
  }

  public function get_hris_branch($key,$fields,$self = false){
    $key = $this->db->escape($key);
    // $fields = $this->db->escape($fields);
    $sql = "SELECT * FROM hris_branch WHERE $fields = $key AND enabled = 1";
    if($self){
      $selft = $this->db->escape($self);
      $sql .= " AND id != $self";
    }
    return $this->db->query($sql);
  }

  public function set_hris_branch($data){
    $this->db->insert('hris_branch',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_branch_admin($data,$dbname){
    $this->db = switch_database($dbname);
    $this->db->insert('hris_users',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_hris_branch($data,$id){
    $this->db->update('hris_branch', $data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_hris_branch_admin($data,$id,$dbname){
    $this->app_db = switch_database($dbname);
    $this->app_db->update('hris_users',$data,array('user_id' => $id));
    return true;
    // return $this->app_db->last_query();
    // return ($this->app_db->affected_rows() > 0) ? true: false;
  }

  public function update_hris_branch_status($id,$status = 0){
    $id = $this->db->escape($id);
    $status = $this->db->escape($status);
    $sql = "UPDATE hris_branch SET enabled = $status WHERE id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function create_database($dbname){
    set_time_limit(300);
    $this->load->dbforge();
    if($this->dbforge->create_database($dbname)){
      $this->app_db = switch_database($dbname);
      $this->myforge = $this->load->dbforge($this->app_db, TRUE);
      // additional_pays
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `payroll_ref_no` varchar(255) NOT NULL DEFAULT 'none',
        `employee_id` varchar(100) NOT NULL,
        `date_issued` date NOT NULL,
        `purpose` varchar(100) NOT NULL,
        `amount` int(100) NOT NULL,
        `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `status` enum('waiting','approved','certified') NOT NULL,
        `created_by` varchar(50) NOT NULL,
        `approved_by` varchar(50) NOT NULL DEFAULT '',
        `certified_by` varchar(50) NOT NULL DEFAULT '',
        `enabled` int(100) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('additional_pays', TRUE);

      // applicant_dependents
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `applicant_ref_no` varchar(100) NOT NULL,
        `first_name` varchar(50) NOT NULL,
        `middle_name` varchar(50) NOT NULL,
        `last_name` varchar(50) NOT NULL,
        `birthday` date DEFAULT '0000-00-00',
        `relationship` varchar(30) NOT NULL,
        `contact_no` varchar(20) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '0'"
      );
      $this->myforge->create_table('applicant_dependents', TRUE);

      // applicant_education
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `applicant_ref_no` varchar(100) NOT NULL,
        `year_from` date NOT NULL,
        `year_to` date NOT NULL,
        `school` varchar(255) NOT NULL,
        `course` varchar(255) NOT NULL,
        `level` varchar(255) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '0'"
      );
      $this->myforge->create_table('applicant_education', TRUE);

      // applicant_form_link
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `app_link` varchar(255) NOT NULL,
        `app_token` varchar(255) NOT NULL,
        `app_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `app_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `app_status` int(11) NOT NULL DEFAULT '0',
        `app_enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('applicant_form_link', TRUE);

      // applicant_record
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `app_ref_no` varchar(50) NOT NULL,
        `app_fname` varchar(50) NOT NULL,
        `app_mname` varchar(50) NOT NULL,
        `app_lname` varchar(50) NOT NULL,
        `app_birthday` date NOT NULL,
        `app_gender` varchar(10) NOT NULL,
        `app_marital_status` varchar(30) NOT NULL,
        `app_home_add1` text NOT NULL,
        `app_home_add2` text NOT NULL,
        `app_city` varchar(50) DEFAULT '',
        `app_country` varchar(50) DEFAULT '',
        `app_contact_no` varchar(15) NOT NULL,
        `app_email` varchar(50) NOT NULL,
        `app_sss_no` varchar(50) NOT NULL DEFAULT '',
        `app_philhealth_no` varchar(50) NOT NULL DEFAULT '',
        `app_pagibig_no` varchar(50) NOT NULL DEFAULT '',
        `app_tin_no` varchar(50) NOT NULL DEFAULT '',
        `app_isActive` tinyint(4) NOT NULL,
        `app_status` enum('in_process','interview','fail_interview','job_offer','reject_joboffer','requirements','hired') NOT NULL DEFAULT 'in_process',
        `app_enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('applicant_record', TRUE);

      // applicant_workhistory
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `applicant_ref_no` varchar(100) NOT NULL,
        `year_from` date NOT NULL,
        `year_to` date NOT NULL,
        `stay` varchar(100) NOT NULL,
        `company_name` varchar(100) NOT NULL,
        `position` varchar(100) NOT NULL,
        `level` varchar(100) NOT NULL,
        `contact_no` varchar(20) NOT NULL,
        `responsibility` text NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '0'"
      );
      $this->myforge->create_table('applicant_workhistory', TRUE);

      // bank
      $this->myforge->add_field("`bank_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `bank_name` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('bank', TRUE);

      // cashadvance_pending_deduction
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `ca_id` int(11) NOT NULL,
        `employee_idno` varchar(100) NOT NULL,
        `payroll_refno` varchar(100) NOT NULL,
        `ca_payment` double NOT NULL,
        `ca_balance` double NOT NULL,
        `ca_from` date NOT NULL,
        `ca_to` date NOT NULL,
        `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('cashadvance_pending_deduction', TRUE);

      // cash_advance
      $this->myforge->add_field("`caID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `idno` int(11) NOT NULL,
        `date_application` datetime NOT NULL,
        `amount` int(20) NOT NULL,
        `description` varchar(100) NOT NULL,
        `ca_status` varchar(100) NOT NULL,
        `approved_by` varchar(100) NOT NULL,
        `date_approved` datetime NOT NULL,
        `approved_amount` int(20) NOT NULL,
        `released_by` varchar(100) NOT NULL,
        `released_date` datetime NOT NULL,
        `released_amount` int(20) NOT NULL,
        `released_charge_amount` int(20) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('cash_advance', TRUE);

      // cash_advance_pay
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `ca_id` int(11) NOT NULL,
        `payroll_ref_no` varchar(255) DEFAULT 'none',
        `employee_idno` varchar(100) NOT NULL,
        `ca_payment` double NOT NULL,
        `ca_balance` double NOT NULL,
        `cutoff_from` date NOT NULL,
        `cutoff_to` date NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `approved_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('cash_advance_pay', TRUE);

      // cash_advance_payment_scheme
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `monthly_rate` decimal(5,2) NOT NULL,
        `maximum_loan` decimal(5,2) NOT NULL,
        `term_of_payment` int(11) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('cash_advance_payment_scheme', TRUE);

      // cash_advance_tran
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_id` varchar(100) NOT NULL,
        `date_of_file` date NOT NULL,
        `amount` double NOT NULL,
        `total_amount` double NOT NULL,
        `total_balance` double NOT NULL,
        `reason` varchar(100) NOT NULL,
        `date_of_effectivity` date NOT NULL,
        `date_end` date NOT NULL,
        `terms` int(100) NOT NULL,
        `rate` int(100) NOT NULL,
        `status` enum('waiting','approved','certified') NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `certified_by` varchar(150) NOT NULL DEFAULT '',
        `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `enabled` int(100) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('cash_advance_tran', TRUE);

      // city
      $this->myforge->add_field("`cityid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `countryid` int(11) NOT NULL COMMENT 'country->countryid',
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('city', TRUE);

      // contract
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `contract_ref_no` varchar(255) NOT NULL,
        `contract_emp_id` int(11) NOT NULL,
        `work_site_id` varchar(50) NOT NULL,
        `position_id` int(11) NOT NULL,
        `position_access_lvl` int(11) NOT NULL,
        `company_id` int(11) NOT NULL,
        `emp_lvl` int(11) NOT NULL DEFAULT '0',
        `contract_start` date NOT NULL,
        `contract_end` date NOT NULL,
        `contract_status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `contract_desc` text NOT NULL,
        `work_sched_id` int(11) NOT NULL,
        `emp_status` int(11) NOT NULL DEFAULT '0',
        `payout_medium` int(11) NOT NULL DEFAULT '0',
        `sss` int(11) DEFAULT '0',
        `philhealth` int(11) DEFAULT '0',
        `pagibig` int(11) DEFAULT '0',
        `tax` int(11) DEFAULT '0',
        `paytype` int(11) NOT NULL,
        `sal_cat` text NOT NULL,
        `base_pay` double NOT NULL DEFAULT '0',
        `total_sal` double NOT NULL,
        `total_sal_converted` double NOT NULL,
        `currency` varchar(5) NOT NULL DEFAULT 'PHP',
        `emp_leave` text NOT NULL,
        `total_leave` double NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `contract_type` enum('fixed','open') NOT NULL DEFAULT 'fixed',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('contract', TRUE);

      // contract_payout_medium
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `contract_id` int(11) NOT NULL,
        `payout_medium_id` int(11) NOT NULL,
        `bank_id` int(11) NOT NULL,
        `card_number` varchar(50) NOT NULL,
        `account_number` varchar(50) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('contract_payout_medium', TRUE);

      // country
      $this->myforge->add_field("`countryid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('country', TRUE);

      // deduction
      $this->myforge->add_field("`deductionid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('deduction', TRUE);

      // department
      $this->myforge->add_field("`departmentid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('department', TRUE);

      // educlevel
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('educlevel', TRUE);

      // employee_charges
      $this->myforge->add_field("`employee_charges_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `idno` varchar(100) NOT NULL,
        `date` datetime NOT NULL,
        `amount` int(20) NOT NULL,
        `description` varchar(100) NOT NULL,
        `charge_status` varchar(100) NOT NULL,
        `approved_by` varchar(100) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `date_approved` datetime NOT NULL,
        `user_id` int(11) NOT NULL,
        `enabled` int(20) NOT NULL"
      );
      $this->myforge->create_table('employee_charges', TRUE);

      // employee_dependents
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(50) NOT NULL,
        `first_name` varchar(255) NOT NULL,
        `middle_name` varchar(255) NOT NULL,
        `last_name` varchar(255) NOT NULL,
        `birthday` date DEFAULT '0000-00-00',
        `relationship` varchar(100) NOT NULL,
        `contact_no` varchar(50) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('employee_dependents', TRUE);

      // employee_details_temp
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `first_name` varchar(100) NOT NULL,
        `middle_name` varchar(100) NOT NULL,
        `last_name` varchar(100) NOT NULL,
        `birthday` varchar(100) NOT NULL,
        `gender` varchar(100) NOT NULL,
        `marital_status` varchar(100) NOT NULL,
        `home_address_1` varchar(100) NOT NULL,
        `home_address_2` varchar(100) NOT NULL,
        `country` varchar(100) NOT NULL,
        `contact_number` varchar(100) NOT NULL,
        `date_created` datetime NOT NULL,
        `enabled` int(10) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('employee_details_temp', TRUE);

      // employee_education
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `year_from` date NOT NULL,
        `year_to` date NOT NULL,
        `school` varchar(255) NOT NULL,
        `course` varchar(255) NOT NULL,
        `level` varchar(255) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('employee_education', TRUE);

      // employee_photos
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `front_photo` varchar(255) NOT NULL,
        `right_photo` varchar(255) NOT NULL,
        `left_photo` varchar(255) NOT NULL,
        `additional` varchar(255) NOT NULL"
      );
      $this->myforge->create_table('employee_photos', TRUE);

      // employee_record
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(50) NOT NULL,
        `app_ref_no` varchar(50) NOT NULL DEFAULT '',
        `first_name` varchar(100) NOT NULL,
        `middle_name` varchar(100) NOT NULL,
        `last_name` varchar(100) NOT NULL,
        `birthday` date NOT NULL,
        `gender` varchar(10) NOT NULL,
        `marital_status` varchar(50) NOT NULL,
        `home_address1` text NOT NULL,
        `home_address2` text NOT NULL,
        `city` varchar(100) DEFAULT '',
        `country` varchar(100) DEFAULT '',
        `contact_no` varchar(15) NOT NULL,
        `email` varchar(50) NOT NULL,
        `sss_no` varchar(50) NOT NULL DEFAULT '',
        `philhealth_no` varchar(50) NOT NULL DEFAULT '',
        `pagibig_no` varchar(50) NOT NULL DEFAULT '',
        `tin_no` varchar(50) NOT NULL DEFAULT '',
        `first_month` int(11) NOT NULL DEFAULT '0',
        `isActive` tinyint(1) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('employee_record', TRUE);

      // employee_workhistory
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(50) NOT NULL,
        `year_from` date NOT NULL,
        `year_to` date NOT NULL,
        `stay` varchar(255) NOT NULL,
        `company_name` varchar(255) NOT NULL,
        `position` varchar(255) NOT NULL,
        `level` varchar(255) NOT NULL,
        `contact_no` varchar(30) NOT NULL,
        `responsibility` text NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('employee_workhistory', TRUE);

      // empstatus
      $this->myforge->add_field("`empstatusid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `regular_holiday` enum('yes','no') NOT NULL DEFAULT 'no',
        `special_non_working_holiday` enum('yes','no') NOT NULL DEFAULT 'no',
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('empstatus', TRUE);

      // holidays_tran
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `holiday_type` int(11) NOT NULL,
        `date` date NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('holidays_tran', TRUE);

      // holidaytype
      $this->myforge->add_field("`holidaytypeid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `payratio` double NOT NULL,
        `payratio2` double NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('holidaytype', TRUE);

      // hris_additional_log
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `additional_summary_id` int(11) NOT NULL,
        `emp_id` varchar(150) NOT NULL,
        `contract_refno` varchar(100) NOT NULL,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `additionalpay` double NOT NULL DEFAULT '0',
        `overtimepay` double NOT NULL DEFAULT '0',
        `currency` varchar(5) NOT NULL DEFAULT 'PHP',
        `ex_rate` double NOT NULL DEFAULT '1',
        `status` enum('waiting','approved') NOT NULL DEFAULT 'waiting',
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_additional_log', TRUE);

      // hris_additional_summary
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `company_id` int(11) NOT NULL,
        `department_id` int(11) DEFAULT NULL,
        `status` enum('waiting','approved') NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_by` varchar(150) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `approved_date` date DEFAULT NULL,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_additional_summary', TRUE);

      // hris_announcement
      $this->myforge->add_field("`announce_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `announce_title` varchar(100) NOT NULL,
        `announce_subject` varchar(100) NOT NULL,
        `announce_body` text NOT NULL,
        `announce_start` date NOT NULL,
        `announce_end` date NOT NULL,
        `created_by` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `status` enum('waiting','approved') NOT NULL DEFAULT 'waiting',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_announcement', TRUE);

      // hris_biometrics_id
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `bio_id` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_biometrics_id', TRUE);

      // hris_companies
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `company` varchar(150) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_companies', TRUE);

      // hris_compensation_reports
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(30) NOT NULL,
        `payroll_ref_no` varchar(30) NOT NULL,
        `sss` double NOT NULL DEFAULT '0',
        `philhealth` double NOT NULL DEFAULT '0',
        `pagibig` double NOT NULL DEFAULT '0',
        `tax` double NOT NULL DEFAULT '0',
        `cutoff_from` date NOT NULL,
        `cutoff_to` date NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_compensation_reports', TRUE);

      // hris_content_navigation
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `cn_url` varchar(255) NOT NULL,
        `cn_name` varchar(255) NOT NULL,
        `cn_description` varchar(255) NOT NULL,
        `cn_hasline` int(11) NOT NULL DEFAULT '0',
        `cn_fkey` int(11) NOT NULL COMMENT 'jcw_main_navigation->id',
        `date_created` datetime NOT NULL,
        `arrangement` int(11) NOT NULL,
        `status` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_content_navigation', TRUE);

      // hris_deduction_log
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(255) NOT NULL,
        `contract_refno` varchar(100) NOT NULL,
        `deductionsum_id` int(11) NOT NULL,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `deduction_total` double NOT NULL DEFAULT '0',
        `sss` double NOT NULL DEFAULT '0',
        `sss_loan` double NOT NULL DEFAULT '0',
        `philhealth` double NOT NULL DEFAULT '0',
        `philhealth_loan` double NOT NULL DEFAULT '0',
        `pag_ibig` double NOT NULL DEFAULT '0',
        `pag_ibig_loan` double NOT NULL DEFAULT '0',
        `cashadvance` double NOT NULL DEFAULT '0',
        `salary_deduction` double NOT NULL DEFAULT '0',
        `currency` varchar(5) NOT NULL DEFAULT 'PHP',
        `ex_rate` double NOT NULL DEFAULT '1',
        `status` enum('waiting','approved') NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_deduction_log', TRUE);

      // hris_deduction_summary
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `company_id` int(11) NOT NULL,
        `department_id` int(11) DEFAULT NULL,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `total_deduction` double NOT NULL DEFAULT '0',
        `status` enum('waiting','approved') NOT NULL DEFAULT 'waiting',
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `approved_date` date DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_deduction_summary', TRUE);

      // hris_employment_history
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(30) NOT NULL,
        `reason` text NOT NULL,
        `termination_date` date NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `created_by` varchar(50) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_employment_history', TRUE);

      // hris_evaluations
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `ref_no` varchar(100) NOT NULL,
        `management_id` varchar(100) NOT NULL,
        `employee_idno` varchar(100) NOT NULL,
        `department_id` int(11) NOT NULL,
        `eval_type` enum('type_1','type_2') NOT NULL DEFAULT 'type_1',
        `eval_score` double NOT NULL DEFAULT '0',
        `eval_score_percent` int(11) NOT NULL DEFAULT '0',
        `eval_equivalent_rate` varchar(100) NOT NULL DEFAULT '',
        `eval_remarks` text,
        `eval_recommendations` text,
        `eval_assessment` text,
        `eval_purpose_type` text,
        `eval_purpose` text,
        `eval_comments` text,
        `eval_project` text,
        `eval_proj_comment` text,
        `eval_date` date NOT NULL,
        `eval_from` date NOT NULL,
        `eval_to` date NOT NULL,
        `eval_action_hr` text,
        `certify_by` varchar(100) NOT NULL DEFAULT '',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `status` enum('delivered','seen') NOT NULL DEFAULT 'delivered',
        `status2` enum('ongoing','evaluated','certified') NOT NULL DEFAULT 'ongoing',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_evaluations', TRUE);

      // hris_eval_formula
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `formula` varchar(255) NOT NULL DEFAULT '(Total_Points / 60) * 100',
        `type` enum('type_1','type_2') NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_eval_formula', TRUE);

      // hris_eval_purpose
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `title` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_eval_purpose', TRUE);

      // hris_eval_questions
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `title` varchar(100) NOT NULL,
        `description` text NOT NULL,
        `section` varchar(10) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_eval_questions', TRUE);

      // hris_eval_ratings
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `eval_type` enum('type_1','type_2') NOT NULL DEFAULT 'type_1',
        `rating` double NOT NULL,
        `description` text NOT NULL,
        `equivalent_rating` varchar(100) NOT NULL,
        `score` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_eval_ratings', TRUE);

      // hris_eval_recommendations
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_eval_recommendations', TRUE);

      // hris_eval_section
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `section` varchar(10) NOT NULL,
        `title` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_eval_section', TRUE);

      // hris_eval_self_assessment
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `question` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_eval_self_assessment', TRUE);

      // hris_exchange_rates
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `base` varchar(5) NOT NULL DEFAULT 'PHP',
        `currency_code` varchar(5) NOT NULL,
        `currency_name` varchar(50) NOT NULL,
        `exchange_rate` double NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_exchange_rates', TRUE);

      // hris_facial_recog
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `facial_landmarks` text NOT NULL,
        `accuracy` int(11) NOT NULL,
        `descriptor` text NOT NULL,
        `img_src` varchar(255) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_facial_recog', TRUE);

      // hris_hrassists
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `body` text NOT NULL,
        `created_by` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_hrassists', TRUE);

      // hris_incident_reports
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(50) NOT NULL,
        `position_id` int(11) NOT NULL,
        `dept_id` int(11) NOT NULL,
        `date_reported` date NOT NULL,
        `date_happened` date NOT NULL,
        `time_of_incidence` time NOT NULL,
        `place_of_incidence` varchar(100) NOT NULL,
        `resulting_damage` text NOT NULL,
        `incident_brief` text NOT NULL,
        `reported_by` varchar(50) NOT NULL,
        `reporting_head_id` varchar(50) NOT NULL,
        `concerned_head_id` varchar(50) NOT NULL,
        `reporting_dept_head` varchar(50) NOT NULL DEFAULT '',
        `concerned_dept_head` varchar(50) NOT NULL DEFAULT '',
        `hr_dept_head` varchar(50) NOT NULL DEFAULT '',
        `account_dept_head` varchar(50) NOT NULL DEFAULT '',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `status` enum('active','inactive') NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_incident_reports', TRUE);

      // hris_main_navigation
      $this->myforge->add_field("`main_nav_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `main_nav_desc` varchar(255) NOT NULL,
        `main_nav_icon` varchar(255) NOT NULL,
        `main_nav_href` varchar(255) NOT NULL COMMENT 'name of function inside of the Main controller',
        `attr_val` varchar(255) NOT NULL COMMENT 'class,id,name attr of checkbox',
        `attr_val_edit` varchar(255) NOT NULL COMMENT 'class,id,name attr of checkbox (edit)',
        `arrangement` int(11) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_main_navigation', TRUE);

      // hris_manhours_log
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `manhours_summary_id` int(11) NOT NULL,
        `emp_id` varchar(255) NOT NULL,
        `contract_refno` varchar(100) NOT NULL,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `days` double NOT NULL DEFAULT '0',
        `hours` double NOT NULL DEFAULT '0',
        `absent` double NOT NULL DEFAULT '0',
        `late` double NOT NULL DEFAULT '0',
        `ut` double NOT NULL DEFAULT '0',
        `ot` double NOT NULL DEFAULT '0',
        `adj1` double NOT NULL DEFAULT '0',
        `adj2` double NOT NULL DEFAULT '0',
        `holiday1` double NOT NULL DEFAULT '0',
        `holiday2` double NOT NULL DEFAULT '0',
        `nightdiff` double NOT NULL DEFAULT '0',
        `sunday` double NOT NULL DEFAULT '0',
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `status` enum('waiting','approved') NOT NULL DEFAULT 'waiting',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_manhours_log', TRUE);

      // hris_manhours_summary
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `company_id` int(11) NOT NULL,
        `department_id` int(11) NOT NULL,
        `status` enum('waiting','approved') NOT NULL DEFAULT 'waiting',
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `approved_date` date DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_manhours_summary', TRUE);

      // hris_memorandum
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(50) NOT NULL,
        `dept_id` varchar(50) NOT NULL,
        `re` varchar(100) NOT NULL,
        `date` date NOT NULL,
        `memo_file` varchar(255) NOT NULL,
        `status` enum('approved','pending') NOT NULL DEFAULT 'pending',
        `createt_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_memorandum', TRUE);

      // hris_pagibig_loans
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `pagibig_loan_voucher` varchar(155) NOT NULL,
        `pagibig_loan_start` date NOT NULL,
        `pagibig_loan_end` date NOT NULL,
        `pagibig_deduction_start` date NOT NULL,
        `pagibig_total_loan` double NOT NULL DEFAULT '0',
        `pagibig_total_balance` double NOT NULL DEFAULT '0',
        `pagibig_total_paid` double DEFAULT '0',
        `monthly_amortization` double NOT NULL,
        `status` enum('active','done') NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_pagibig_loans', TRUE);

      // hris_pagibig_loan_pending_deduction
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `pagibig_loan_id` int(11) NOT NULL,
        `employee_idno` varchar(100) NOT NULL,
        `payroll_refno` varchar(100) NOT NULL,
        `monthly_amortization` double NOT NULL,
        `status` enum('pending','approved') NOT NULL,
        `pagibig_loan_from` date NOT NULL,
        `pagibig_loan_to` date NOT NULL,
        `payday` date NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_pagibig_loan_pending_deduction', TRUE);

      // hris_pagibig_reports
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `pagibig_no` varchar(50) NOT NULL,
        `month` varchar(15) NOT NULL,
        `employee_idno` varchar(50) NOT NULL,
        `employee_name` varchar(50) NOT NULL,
        `payroll_ref_no` varchar(50) NOT NULL,
        `company_id` int(11) NOT NULL,
        `company_name` varchar(100) NOT NULL,
        `department` int(11) NOT NULL,
        `department_name` varchar(100) NOT NULL,
        `EE` double NOT NULL,
        `ER` double NOT NULL,
        `total` double NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_pagibig_reports', TRUE);

      // hris_payroll_log
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `payroll_summary_id` int(11) NOT NULL,
        `emp_id` varchar(150) NOT NULL,
        `contract_refno` varchar(100) NOT NULL,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `grosspay` double NOT NULL DEFAULT '0',
        `deductions` double NOT NULL DEFAULT '0',
        `additionals` double NOT NULL DEFAULT '0',
        `netpay` double NOT NULL DEFAULT '0',
        `others1` double NOT NULL DEFAULT '0',
        `others2` double NOT NULL DEFAULT '0',
        `currency` varchar(5) NOT NULL DEFAULT 'PHP',
        `ex_rate` double NOT NULL DEFAULT '1',
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `status` enum('waiting','approved') NOT NULL"
      );
      $this->myforge->create_table('hris_payroll_log', TRUE);

      // hris_payroll_summary
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `ref_no` varchar(255) NOT NULL,
        `manhours_id` int(11) NOT NULL,
        `deduction_id` int(11) NOT NULL,
        `additional_id` int(11) NOT NULL,
        `company_id` int(11) NOT NULL,
        `department_id` int(11) DEFAULT NULL,
        `pay_day` date NOT NULL,
        `fromdate` date NOT NULL,
        `todate` date NOT NULL,
        `paytype` int(11) NOT NULL,
        `status` enum('waiting','approved') NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_by` varchar(150) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `approved_date` date DEFAULT NULL,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_payroll_summary', TRUE);

      // hris_payslip
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `name` varchar(100) NOT NULL,
        `paytype_desc` varchar(20) NOT NULL,
        `date_from` date NOT NULL,
        `date_to` date NOT NULL,
        `gross_salary` double NOT NULL,
        `days_duration` int(10) NOT NULL,
        `overtime` double NOT NULL,
        `ot_duration` int(10) NOT NULL,
        `additionals` double NOT NULL,
        `regular_holiday` double NOT NULL,
        `regular_holiday_duration` int(10) NOT NULL,
        `special_holiday` double NOT NULL,
        `special_holiday_duration` int(10) NOT NULL,
        `sundays` double NOT NULL,
        `sunday_duration` int(10) NOT NULL,
        `absent` double NOT NULL,
        `absent_duration` int(10) NOT NULL,
        `late` double NOT NULL,
        `late_duration` int(10) NOT NULL,
        `undertime` double NOT NULL,
        `undertime_duration` int(10) NOT NULL,
        `sss` double NOT NULL,
        `philhealth` double NOT NULL,
        `pag_ibig` double NOT NULL,
        `sss_loan` double NOT NULL,
        `pag_ibig_loan` double NOT NULL,
        `cashadvance` double NOT NULL,
        `salary_deduction` double NOT NULL,
        `total_deductions` double NOT NULL,
        `netpay` double NOT NULL,
        `date_created` datetime NOT NULL,
        `enabled` int(10) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_payslip', TRUE);

      // hris_philhealth_reports
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `philhealth_no` varchar(30) NOT NULL,
        `month` varchar(15) NOT NULL,
        `employee_idno` varchar(30) NOT NULL,
        `employee_name` varchar(50) NOT NULL,
        `payroll_ref_no` varchar(30) NOT NULL,
        `company_id` int(11) NOT NULL,
        `company_name` varchar(100) NOT NULL,
        `department` int(11) NOT NULL,
        `department_name` varchar(30) NOT NULL,
        `EE` double NOT NULL,
        `ER` double NOT NULL,
        `total` double NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_philhealth_reports', TRUE);

      // hris_position
      $this->myforge->add_field("`position_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `position` varchar(255) NOT NULL,
        `access_nav` text,
        `access_sub_nav` text,
        `access_content_nav` text COMMENT 'jcw_sub_navigation -> sub_nav_id	',
        `hierarchy_lvl` double NOT NULL,
        `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_position', TRUE);

      // hris_rfid
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(155) NOT NULL,
        `rf_number` varchar(155) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_rfid', TRUE);

      // hris_sss_loans
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `sss_loan_voucher` varchar(155) NOT NULL,
        `sss_loan_start` date NOT NULL,
        `sss_loan_end` date NOT NULL,
        `sss_deduction_start` date NOT NULL,
        `sss_total_loan` double NOT NULL DEFAULT '0',
        `sss_total_balance` double NOT NULL DEFAULT '0',
        `sss_total_paid` double NOT NULL DEFAULT '0',
        `monthly_amortization` double NOT NULL,
        `status` enum('active','done') NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_sss_loans', TRUE);

      // hris_sss_loan_pending_deduction
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `sss_loan_id` int(11) NOT NULL,
        `employee_idno` varchar(100) NOT NULL,
        `payroll_refno` varchar(100) NOT NULL,
        `monthly_amortization` double NOT NULL,
        `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
        `sss_loan_from` date NOT NULL,
        `sss_loan_to` date NOT NULL,
        `payday` date NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_sss_loan_pending_deduction', TRUE);

      // hris_sss_reports
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `sss_no` varchar(30) NOT NULL,
        `month` varchar(15) NOT NULL,
        `employee_idno` varchar(30) NOT NULL,
        `employee_name` varchar(50) NOT NULL,
        `payroll_ref_no` text NOT NULL,
        `company_id` int(11) NOT NULL,
        `company_name` varchar(100) NOT NULL,
        `department` int(11) NOT NULL,
        `department_name` varchar(60) NOT NULL,
        `EE` double NOT NULL,
        `ER` double NOT NULL,
        `EC` double NOT NULL,
        `total` double NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) DEFAULT '1'"
      );
      $this->myforge->create_table('hris_sss_reports', TRUE);

      // hris_timelog_logs
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `admin_id` varchar(100) NOT NULL,
        `logs` text NOT NULL,
        `date` date NOT NULL,
        `status` enum('update','delete') NOT NULL,
        `type` enum('timelog','workorder') NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('hris_timelog_logs', TRUE);

      // hris_users
      $this->myforge->add_field("`user_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `username` varchar(255) NOT NULL,
        `password` varchar(100) NOT NULL,
        `user_fname` varchar(255) NOT NULL,
        `user_mname` varchar(255) DEFAULT NULL,
        `user_lname` varchar(255) NOT NULL,
        `position_id` int(11) NOT NULL COMMENT 'Admin = 1 | Operator = 2 | Player = 3 | Staff = 4 | Staff(Admin) = 5   ',
        `employee_idno` varchar(100) NOT NULL,
        `deptId` int(11) DEFAULT '0',
        `subDeptId` int(11) NOT NULL DEFAULT '0',
        `date_activated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `avatar_file` varchar(255) DEFAULT NULL,
        `enabled` int(1) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_users', TRUE);

      // hris_worksched_settings
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `min_whours` float NOT NULL,
        `max_whours` float NOT NULL,
        `min_bhours` float NOT NULL,
        `max_bhours` float NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_worksched_settings', TRUE);

      // leaves
      $this->myforge->add_field("`leaveid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(100) NOT NULL,
        `days_before_filling` int(11) DEFAULT '0',
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('leaves', TRUE);

      // leave_tran
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `leave_type` int(11) NOT NULL,
        `employee_idno` varchar(100) NOT NULL,
        `date_from` date NOT NULL,
        `date_to` date NOT NULL,
        `number_of_days` int(100) NOT NULL,
        `balance` int(100) NOT NULL DEFAULT '0',
        `hrd` varchar(100) NOT NULL DEFAULT '',
        `comment` varchar(100) NOT NULL,
        `contact_number_leave` varchar(100) NOT NULL,
        `status` enum('waiting','approved','certified') NOT NULL,
        `paid` enum('with_pay','without_pay') DEFAULT 'without_pay',
        `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `certified_by` varchar(150) NOT NULL DEFAULT '',
        `enabled` int(20) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('leave_tran', TRUE);

      // level
      $this->myforge->add_field("`levelid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `hierarchy_level` double NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('level', TRUE);

      // overtime_pays
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `payroll_ref_no` varchar(255) NOT NULL DEFAULT 'none',
        `employee_id` varchar(100) NOT NULL,
        `purpose` varchar(100) NOT NULL,
        `minutes_of_overtime` int(100) NOT NULL,
        `date_rendered` date NOT NULL,
        `status` enum('waiting','approved','certified') NOT NULL,
        `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_by` varchar(50) NOT NULL,
        `approved_by` varchar(50) NOT NULL DEFAULT '',
        `certified_by` varchar(50) NOT NULL DEFAULT '',
        `enabled` int(100) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('overtime_pays', TRUE);

      // pagibig
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `monthly_compensation` varchar(50) NOT NULL,
        `employee_share` decimal(5,2) NOT NULL,
        `employer_share` decimal(5,2) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('pagibig', TRUE);

      // payoutmedium
      $this->myforge->add_field("`payoutmediumid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('payoutmedium', TRUE);

      // paytype
      $this->myforge->add_field("`paytypeid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `frequency` int(11) NOT NULL,
        `date_range` varchar(10) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('paytype', TRUE);

      // pb_company_helper
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `company_name` varchar(255) NOT NULL,
        `company_initial` varchar(255) NOT NULL,
        `company_logo` varchar(255) NOT NULL,
        `company_logo_small` varchar(255) NOT NULL,
        `company_address` varchar(255) NOT NULL,
        `company_website` varchar(255) NOT NULL,
        `company_phone` varchar(255) NOT NULL,
        `company_email` varchar(255) NOT NULL,
        `powered_by` varchar(255) NOT NULL,
        `paypanda_link` varchar(255) NOT NULL"
      );
      $this->myforge->create_table('pb_company_helper', TRUE);

      // pb_userrole_main_nav
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `label_val` varchar(255) NOT NULL,
        `attr_val` varchar(255) NOT NULL,
        `attr_val_edit` varchar(255) NOT NULL,
        `arrangement` int(11) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('pb_userrole_main_nav', TRUE);

      // philhealth
      $this->myforge->add_field("`phID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `basic_mo_sal` double NOT NULL,
        `basic_mo_sal1` double NOT NULL,
        `mo_contribution` double NOT NULL,
        `mo_contribution1` double NOT NULL,
        `employee_share` double NOT NULL,
        `employee_share1` double NOT NULL,
        `employer_share` double NOT NULL,
        `employer_share1` double NOT NULL,
        `enabled` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `date_created` datetime NOT NULL,
        `date_updated` datetime NOT NULL"
      );
      $this->myforge->create_table('philhealth', TRUE);

      // position
      $this->myforge->add_field("`positionid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `deptId` int(11) NOT NULL,
        `subDeptId` int(11) NOT NULL,
        `pos_access_lvl` int(11) NOT NULL,
        `description` varchar(255) NOT NULL,
        `levelid` int(11) NOT NULL DEFAULT '0' COMMENT 'level->levelid',
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('position', TRUE);

      // relationship
      $this->myforge->add_field("`relationshipid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('relationship', TRUE);

      // salarycat
      $this->myforge->add_field("`salarycatid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `type` varchar(255) NOT NULL DEFAULT '',
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('salarycat', TRUE);

      // salary_deduction
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `payroll_ref_no` varchar(255) NOT NULL DEFAULT 'none',
        `employee_idno` varchar(100) NOT NULL,
        `deduct_category` int(11) NOT NULL,
        `amount` double NOT NULL,
        `status` enum('waiting','approved','certified') NOT NULL,
        `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `certified_by` varchar(150) NOT NULL DEFAULT '',
        `enabled` int(20) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('salary_deduction', TRUE);

      // sss
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `salRange_from` double NOT NULL,
        `salRange_to` double NOT NULL,
        `monthly_sal_cred` double NOT NULL,
        `ss_er` double NOT NULL,
        `ss_ee` double NOT NULL,
        `ss_total` double NOT NULL,
        `ec_er` double NOT NULL,
        `tc_er` double NOT NULL,
        `tc_ee` double NOT NULL,
        `tc_total` double NOT NULL,
        `SV_VM_OFW` double NOT NULL DEFAULT '0',
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('sss', TRUE);

      // subdept
      $this->myforge->add_field("`subdeptid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `departmentid` int(11) NOT NULL COMMENT 'department->departmentid',
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('subdept', TRUE);

      // system_users
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('system_users', TRUE);

      // tax
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `aibLowerLimit` double NOT NULL,
        `aibUpperLimit` double NOT NULL,
        `tr1LowerLimit` double NOT NULL,
        `tr1ExcessLimit` double NOT NULL,
        `tr2LowerLimit` double NOT NULL,
        `tr2ExcessLimit` double NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('tax', TRUE);

      // time_record_summary
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `time_in` time DEFAULT NULL,
        `time_out` time DEFAULT NULL,
        `emp_counter` int(11) NOT NULL DEFAULT '1',
        `date_created` date NOT NULL,
        `man_hours` double NOT NULL,
        `late` int(100) NOT NULL DEFAULT '0',
        `overtime` int(100) NOT NULL DEFAULT '0',
        `undertime` int(100) NOT NULL DEFAULT '0',
        `overbreak` int(100) NOT NULL,
        `absent` int(11) NOT NULL DEFAULT '0',
        `total_minutes` int(11) NOT NULL,
        `remarks` int(11) NOT NULL COMMENT '1 = holiday',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('time_record_summary', TRUE);

      // time_record_summary_trial
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `worksite` varchar(100) NOT NULL,
        `date` date NOT NULL,
        `type` varchar(100) NOT NULL DEFAULT 'auto',
        `mode` varchar(100) NOT NULL,
        `img_url` varchar(255) NOT NULL,
        `current_location` text NOT NULL,
        `time_in` time DEFAULT NULL,
        `time_out` time DEFAULT NULL,
        `date_created` datetime NOT NULL,
        `status_absent` int(20) NOT NULL DEFAULT '0',
        `break_trigger` enum('normal','break') NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('time_record_summary_trial', TRUE);

      // valid_id_details
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `valid_id_type` varchar(100) NOT NULL,
        `id_number` varchar(100) NOT NULL,
        `id_value` varchar(45) NOT NULL,
        `upload_date` date NOT NULL,
        `picture_extension` varchar(100) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('valid_id_details', TRUE);

      // worksite
      $this->myforge->add_field("`worksiteid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `city` int(11) DEFAULT '0',
        `location` varchar(255) NOT NULL,
        `loc_latitude` varchar(100) NOT NULL,
        `loc_longitude` varchar(100) NOT NULL,
        `distance` int(100) NOT NULL DEFAULT '1000' COMMENT 'Actual Distance in Meters',
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL COMMENT 'user who create/update',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('worksite', TRUE);

      // worktype
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `description` varchar(255) NOT NULL,
        `date_updated` datetime NOT NULL,
        `date_created` datetime NOT NULL,
        `user_id` int(11) NOT NULL,
        `enabled` int(11) NOT NULL"
      );
      $this->myforge->create_table('worktype', TRUE);

      // work_order
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_id` varchar(100) NOT NULL,
        `date` date NOT NULL,
        `start_time` time NOT NULL,
        `end_time` time NOT NULL,
        `status` enum('waiting','approved','certified') NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `created_by` varchar(150) NOT NULL,
        `approved_by` varchar(150) NOT NULL DEFAULT '',
        `certified_by` varchar(150) NOT NULL DEFAULT ''"
      );
      $this->myforge->create_table('work_order', TRUE);

      // work_order_itenerary
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `workorder_id` int(11) NOT NULL,
        `location` varchar(50) NOT NULL,
        `contact_person` varchar(50) NOT NULL,
        `contact_num` varchar(15) NOT NULL,
        `purpose` varchar(255) NOT NULL,
        `notes` varchar(100) NOT NULL,
        `enabled` int(11) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('work_order_itenerary', TRUE);

      // work_schedule
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `pos_id` int(11) NOT NULL,
        `emp_id` varchar(100) NOT NULL,
        `emp_idno` varchar(50) NOT NULL,
        `work_sched` text NOT NULL,
        `break_sched` text,
        `total_whours` double NOT NULL,
        `total_bhours` double NOT NULL,
        `sched_type` enum('fix','flexi') NOT NULL,
        `sched_type2` enum('specific','default') NOT NULL,
        `enabled` int(11) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'"
      );
      $this->myforge->create_table('work_schedule', TRUE);

      // hris_registered_device
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `activation_code` varchar(100) NOT NULL,
        `device_id` varchar(100) NOT NULL DEFAULT '',
        `status` enum('open','closed') NOT NULL DEFAULT 'open',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_registered_device', TRUE);

      // hris_contract_template
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `template_name` varchar(50) NOT NULL,
        `template_format` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_contract_template', TRUE);

      // hris_template_settings
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(100) NOT NULL,
        `field_name` varchar(100) NOT NULL,
        `table_name` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_template_settings', TRUE);

      // hris_contract_files
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `contract_id` int(11) NOT NULL,
        `template_id` int(11) DEFAULT NULL,
        `content` text,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_contract_files', TRUE);

      // hris_requirements
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `employee_idno` varchar(100) NOT NULL,
        `file_path` varchar(255) NOT NULL,
        `req_type` enum('resume','two_by_two_pic','college_diploma','tor','two_valid_id','tin','sss_e1_form','philhealth_no','pagibig_no','psa_birth_certificate','marriage_certificate','child_birth_certificate','nbi_clearance','police_clearance','brgy_clearance','med_certificate') NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_requirements', TRUE);

      // hris_contract_audit_trail
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `contract_id` varchar(100) NOT NULL,
        `prev_contract_id` varchar(100) NOT NULL,
        `employee_idno` varchar(100) NOT NULL,
        `audit_trail` text NOT NULL,
        `fields` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_contract_audit_trail', TRUE);

      // hris_applicant_interview
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `app_ref_no` varchar(100) NOT NULL,
        `interviewer` varchar(100) NOT NULL,
        `interview_notes` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_applicant_interview', TRUE);

      // hris_applicant_job_offer
      $this->myforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `app_ref_no` varchar(100) NOT NULL,
        `content` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        `enabled` int(11) NOT NULL DEFAULT '1'"
      );
      $this->myforge->create_table('hris_applicant_job_offer', TRUE);

      return true;
    }else{
      return false;
    }
  }

  public function populate_database($dbname){
    $this->app_db = switch_database($dbname);
    // bank
    $this->app_db->query("INSERT INTO `bank` (`bank_id`, `bank_name`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'BDO', '2019-05-02 04:25:47', '0000-00-00 00:00:00', 1),
      (2, 'Metro Bank', '2019-04-06 03:31:15', '0000-00-00 00:00:00', 1),
      (3, 'BPI', '2019-04-06 03:31:15', '0000-00-00 00:00:00', 1),
      (4, 'Union Bank', '2019-05-02 04:46:43', '0000-00-00 00:00:00', 1),
      (5, 'CTBC', '2019-09-13 07:25:00', '0000-00-00 00:00:00', 1)");

    // cash_advance_payment_scheme
    $this->app_db->query("INSERT INTO `cash_advance_payment_scheme` (`id`, `monthly_rate`, `maximum_loan`, `term_of_payment`, `enabled`, `created_at`, `updated_at`) VALUES
      (1, '3.00', '30.00', 3, 1, '2019-02-19 06:28:16', '0000-00-00 00:00:00')");

    // deduction
    $this->app_db->query("INSERT INTO `deduction` (`deductionid`, `description`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Broken facilities', '2018-11-06 09:02:32', '2018-01-01 10:00:00', 1, 1),
      (2, 'Performance issue', '2018-11-08 10:04:08', '2018-01-01 10:00:00', 1, 1)");

    // department
    $this->app_db->query("INSERT INTO `department` (`departmentid`, `description`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Human Resource', '2019-03-26 12:03:06', '2019-03-26 12:03:06', 17, 1),
      (2, 'Operations', '2019-03-26 12:03:13', '2019-03-26 12:03:13', 17, 1),
      (3, 'Accounting', '2019-03-26 12:03:20', '2019-03-26 12:03:20', 17, 1)");

    // educlevel
    $this->app_db->query("INSERT INTO `educlevel` (`id`, `description`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Primary', '2019-03-01 11:32:43', '2019-03-01 11:32:43', 2, 1),
      (2, 'Secondary', '2019-03-01 11:32:50', '2019-03-01 11:32:50', 2, 1),
      (3, 'Tertiary', '2019-03-01 11:32:57', '2019-03-01 11:32:57', 2, 1),
      (4, 'Masteral', '2019-03-01 11:33:03', '2019-03-01 11:33:03', 2, 1),
      (5, 'Doctorate', '2019-03-01 11:33:09', '2019-03-01 11:33:09', 2, 1)");

    // empstatus
    $this->app_db->query("INSERT INTO `empstatus` (`empstatusid`, `description`, `regular_holiday`, `special_non_working_holiday`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Trainee', 'yes', 'no', '2019-03-21 12:00:28', '2019-02-21 14:43:01', 17, 1),
      (2, 'Probationary', 'yes', 'no', '2019-03-21 12:00:33', '2019-02-21 14:43:38', 17, 1),
      (3, 'Contractual', 'yes', 'no', '2019-03-21 12:00:39', '2019-02-21 14:44:01', 17, 1),
      (4, 'Regular', 'yes', 'yes', '2019-03-19 15:18:51', '2019-02-21 14:44:09', 17, 1)");

    // holidaytype
    $this->app_db->query("INSERT INTO `holidaytype` (`holidaytypeid`, `description`, `payratio`, `payratio2`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Regular Holiday', 2, 2.6, '2019-03-16 09:24:17', '2019-03-16 09:24:17', 17, 1),
      (2, 'Special Non-Working  Holiday', 1.3, 1.5, '2019-03-16 09:24:55', '2019-03-16 09:24:55', 17, 1),
      (3, 'Regular Day', 1, 2.5, '2019-03-19 07:12:57', '2019-03-19 06:57:27', 17, 0)");

    // hris_content_navigation
    $this->app_db->query("INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES
      (1, 'settings/Level/index/', 'Level', 'Manage the level of employee', 0, 8, '2018-07-13 00:00:00', 0, 1),
      (2, 'settings/Deductions/index/', 'Deductions', 'Manage employee\'s deductions', 0, 8, '2018-07-13 00:00:00', 0, 1),
      (3, 'settings/Educationlevel/index/', 'Education Level', 'Manage the education level', 0, 8, '2018-07-13 00:00:00', 0, 1),
      (4, 'settings/Paytype/index/', 'Pay Type', 'Manage Pay type ', 0, 8, '2018-07-13 00:00:00', 0, 1),
      (5, 'settings/Relationship/index/', 'Relationship', 'Manage Relationship ', 0, 8, '2018-07-13 00:00:00', 0, 1),
      (6, 'settings/City/index/', 'City', 'Create/Update/Delete City', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (7, 'settings/Country/index/', 'Country', 'Create/Update/Delete Country', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (8, 'settings/Payoutmedium/index/', 'Payout Medium', 'Manage Payout Medium', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (9, 'settings/Systemusers/index/', 'System Users', 'Manage system users', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (10, 'settings/Department/index/', 'Department', 'Manage Departments', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (12, 'settings/Subdepartment/index/', 'Sub Department', 'Manage Sub Department', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (13, 'settings/Worktype/index/', 'Work Type', 'Manage Work Type', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (14, 'settings/Worksite/index/', 'Work Site', 'Manage Work Site', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (15, 'settings/Holidaytype/index/', 'Holiday Type', 'Manage Holiday Type', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (16, 'settings/Salarycategory/index/', 'Salary Category', 'Manage Salary Category', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (17, 'settings/Sss_controller/index/', 'SSS', 'View SSS Table', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (18, 'settings/Philhealth/index/', 'Philhealth', 'View Philhealth Table', 0, 8, '2018-07-17 00:00:00', 0, 1),
      (20, 'settings/Tax/index/', 'Tax', 'View Tax Table', 0, 8, '2018-07-20 07:25:08', 0, 1),
      (21, 'settings/Employmentstatus/index/', 'Employment Status', 'Manage Employment Status', 0, 8, '2018-07-20 07:34:19', 0, 1),
      (22, 'settings/Position/index/', 'Position', 'Manage employee\'s position', 0, 8, '2018-07-20 07:35:15', 0, 1),
      (27, 'employees/Employee/index/', 'Employee', 'Manage Employee', 0, 7, '2018-07-20 09:16:45', 4, 1),
      (43, 'www.google.comX', 'google', 'sasaS', 0, 4, '2018-07-21 11:45:10', 0, 0),
      (132, 'settings/Cashadvance/index/', 'Cash Advance', 'Manage the Cash Advances', 0, 8, '2018-07-13 00:00:00', 0, 1),
      (133, 'settings/Leaves/index/', 'Leaves', 'Manage the Leaves', 0, 8, '2018-07-13 00:00:00', 0, 1),
      (134, 'settings/Employeecharges/index/', 'Employee Charges', 'Manage employee\'s charges', 0, 8, '2018-12-07 05:08:00', 0, 1),
      (135, 'settings/Pagibig/index/', 'Pag Ibig', 'View Pag Ibig Table', 0, 8, '2019-01-25 00:00:00', 0, 1),
      (136, 'time_record/Timelogreports/index/', 'Timelog History', 'View the time in and time out reports', 0, 13, '2019-01-26 00:00:00', 0, 1),
      (139, 'transactions/Cashadvance/index/', 'Cash Advance', 'Manage the cash advance of the employees', 0, 14, '2019-01-26 00:00:00', 0, 1),
      (140, 'transactions/Salarydeduction/index/', 'Salary Deduction', 'Manage the salary deduction of the employees', 0, 14, '2019-01-26 00:00:00', 0, 1),
      (141, 'transactions/Cashadvance_payment_history/index/', 'Cash Advance Payment  History', 'View all the logs of the cash advance transactions.', 0, 14, '2019-01-26 00:00:00', 0, 1),
      (142, 'transactions/Salary_deduction_history/index/', 'Salary Deduction History', 'View all the logs of the salary dedudctions.', 0, 14, '2019-01-26 00:00:00', 0, 0),
      (143, 'transactions/Leave/index/', 'Leave', 'Manage all the leaves of the employees.', 0, 14, '2019-01-26 00:00:00', 0, 1),
      (145, 'applicants/Applicant/index/', 'Applicants', 'Manage Applicant', 0, 7, '2019-01-26 00:00:00', 1, 1),
      (146, 'transactions/Cashadvance/add/', 'Cash Advance Add', 'Manage the cash advance of the employees', 0, 14, '2019-01-26 00:00:00', 0, 0),
      (147, 'transactions/Additionalpays/index/', 'Additional Pays', 'Manage other type of pays', 0, 14, '2019-01-26 00:00:00', 0, 1),
      (148, 'transactions/Overtimepays/index/', 'Overtime Pays', 'Manage overtime pays', 0, 14, '2019-01-26 00:00:00', 0, 1),
      (149, 'time_record/Timerecord_summary/index/', 'Time Record Summary', 'View all of the working hours of employee per day', 0, 13, '2019-01-26 00:00:00', 0, 1),
      (150, 'transactions/Workorder/index/', 'Work Order', 'Manage all Work Order', 0, 14, '2019-02-07 00:00:00', 0, 1),
      (151, 'transactions/Additional_pays_history/index/', 'Additional Pays History', 'History of additional history', 0, 14, '2019-02-07 00:00:00', 0, 0),
      (152, 'transactions/Overtime_pays_history/index/', 'Overtime Pays History', 'View all logs of Overtime Pays', 0, 14, '2019-02-07 00:00:00', 0, 0),
      (153, 'transactions/Work_order_approval_history/index/', 'Work Order for Approval', 'Manage all work order for approval', 0, 14, '2019-02-08 00:00:00', 0, 0),
      (154, 'transactions/Work_order_certification_history/index/', 'Work Order for Certification', 'Manage all Work Order for Certification', 0, 14, '2019-02-08 00:00:00', 0, 0),
      (155, 'contracts/Contract_history/index/', 'Contract History', 'View all contracts history', 0, 7, '2019-02-11 00:00:00', 2, 1),
      (156, 'settings/Cashadvancepaymentscheme/index/', 'Cash Advance Payment Scheme', 'Set default cash advance payment scheme', 0, 8, '2019-02-13 00:00:00', 0, 1),
      (157, 'transactions/Holidays/index/', 'Holidays', 'Manage all holidays this year', 0, 14, '2019-02-27 00:00:00', 0, 1),
      (158, 'payroll/Payroll/index/', 'Create Payroll', 'Create Payroll', 0, 15, '2019-02-28 00:00:00', 0, 1),
      (159, 'payroll/Payroll_history_new/index/', 'Payroll History', 'View all Payroll History', 0, 15, '2019-02-28 00:00:00', 0, 1),
      (160, 'settings/Timerecordsummary_range/index/', 'Time Record Summary Range', 'Control the range of time in/out on time record summary', 0, 8, '2019-03-05 00:00:00', 0, 1),
      (161, 'settings/Announcement/index/', 'Announcement', 'Create, Approve and Delete Announcement', 0, 8, '2019-03-27 00:00:00', 0, 1),
      (162, 'settings/Hr_assists/index/', 'HR Assists', 'Manage HR Assists on employee portal', 0, 8, '2019-03-29 00:00:00', 20, 1),
      (163, 'contracts/Payout/index/', 'Payout Account Information', 'Manage employee payout information', 0, 7, '2019-04-06 00:00:00', 5, 1),
      (164, 'profile/Edit_profile/index/', 'Employee Profile', 'View and manage your personal information here', 0, 16, '2019-04-06 00:00:00', 0, 1),
      (165, 'reports/Attendance_reports/index/', 'Attendance Reports', 'Reports regarding all about the attendance in HRIS', 0, 10, '2019-04-10 00:00:00', 0, 1),
      (166, 'reports/Transaction_reports/index/', 'Transaction Reports', 'Reports regarding all about the trasactions in HRIS', 0, 10, '2019-04-10 00:00:00', 0, 1),
      (167, 'reports/Compensation_reports/index/', 'Compensation Reports', 'Reports regarding all about the compensations in HRIS', 0, 10, '2019-04-26 00:00:00', 0, 1),
      (168, 'settings/Bank/index/', 'Bank', 'Manage Bank Information', 0, 8, '2019-05-02 00:00:00', 0, 1),
      (169, 'profile/View_profile/index/', 'View Profile', 'View your personal information here', 0, 16, '2019-04-06 00:00:00', 0, 0),
      (170, 'profile/Edit_profile/index/', 'Employee Profile', 'View and manage your personal information here', 0, 16, '2019-04-06 00:00:00', 0, 1),
      (171, 'reports/Sss_reports/index/', 'SSS Reports', 'Reports regarding about monthly SSS contribution', 0, 10, '2019-05-21 00:00:00', 0, 1),
      (172, 'reports/Philhealth_reports/index/', 'Philhealth Reports', 'Reports regarding about monthly philhealth contribution', 0, 10, '2019-05-29 00:00:00', 0, 1),
      (173, 'employees/Employment_history/index/', 'Employment History', 'History of all previous employee', 0, 7, '2019-05-31 00:00:00', 3, 1),
      (174, 'reports/Contract_expiration_reports/index/', 'Contract Expiration Reports', 'List of all upcoming contracts that\'s going to expire. ', 0, 10, '2019-05-31 00:00:00', 0, 1),
      (175, 'reports/Attendance_graph_analysis/index/', 'Attendance Graph Analysis', 'Attendance Graph Analysis reports of all employees in hris', 0, 10, '2019-05-31 00:00:00', 0, 1),
      (176, 'evaluations/Evaluations/index/', 'Evaluation History', 'List of all evaluation history', 0, 23, '2019-06-15 00:00:00', 0, 1),
      (177, 'evaluations/Evaluations_settings/index/', 'Evaluations Settings', 'Edit Evaluations Form', 0, 23, '2019-06-15 00:00:00', 0, 1),
      (178, 'settings/User_role/index/', 'User Role', 'Manage user role for each hris position', 0, 8, '2019-06-15 00:00:00', 0, 1),
      (179, 'registerid/Register_rf/index/', 'Rf Id Number ', 'Register or update rf id number', 0, 24, '2019-07-04 00:00:00', 0, 1),
      (180, 'registerid/Register_bio/index/', 'Boimetrics Id', 'Register or update biometrics id', 0, 24, '2019-07-04 00:00:00', 0, 1),
      (181, 'registerid/Register_facial/index/', 'Register Facial Features', 'Register facial features for attendance facial recognition', 0, 24, '2019-07-04 00:00:00', 0, 1),
      (182, 'time_record/Timerecord_logs/index/', 'Time Record Logs', 'List of all activity in time record', 0, 13, '2019-07-04 00:00:00', 0, 1),
      (183, 'settings/Companies/index/', 'Companies', 'Manage the list of companies inside HRIS', 0, 8, '2019-06-15 00:00:00', 0, 1),
      (184, 'reports/Pagibig_reports/index/', 'Pagibig Reports', 'Reports regarding about monthly Pagibig contribution', 0, 10, '2019-05-21 00:00:00', 0, 1),
      (185, 'transactions/Sss_loans/index/', 'SSS Loans', 'Manage all SSS Loans', 0, 14, '2019-02-27 00:00:00', 0, 1),
      (186, 'transactions/Pagibig_loans/index/', 'Pagibig Loans', 'Manage all PagibigLoans', 0, 14, '2019-02-27 00:00:00', 0, 1),
      (187, 'settings/Exchange_rates/index/', 'Exchange Rates', 'Manage Exchange Rates of HRIS', 0, 8, '2019-06-15 00:00:00', 0, 1),
      (188, 'reports/Incident_reports/index/', 'Incident Reports', 'Reports regarding about incidents', 0, 10, '2019-05-21 00:00:00', 0, 1),
      (189, 'reports/Memos/index/', 'Memorandum', 'Reports regarding about all memorandum in hris', 0, 10, '2019-09-20 00:00:00', 0, 1),
      (190, 'settings/Contract_template/index/', 'Contract Template', 'Manage available contract template', 0, 8, '2019-09-20 00:00:00', 0, 1),
      (191, 'settings/Registered_device/index/', 'Registered Device', 'Manage all registered device for time in/out in HRIS', 0, 8, '2019-09-20 00:00:00', 0, 1),
      (192, 'reports/Contract_audit_trail_reports/index/', 'Contract Audit Trail Reports', 'Reports of all the changes happening in contracts.', 0, 10, '2019-12-12 00:00:00', 0, 1)");

    // hris_eval_formula
    $this->app_db->query("INSERT INTO `hris_eval_formula` (`id`, `formula`, `type`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, '(Total_Points / 60) * 100', 'type_1', '2019-09-10 09:09:43', '0000-00-00 00:00:00', 1),
      (2, '(Total_Points / 135) * 100', 'type_2', '2019-09-10 09:09:55', '0000-00-00 00:00:00', 1)");

    // hris_users
    $this->app_db->query("INSERT INTO `hris_users` (`user_id`, `username`, `password`, `user_fname`, `user_mname`, `user_lname`, `position_id`, `employee_idno`, `deptId`, `subDeptId`, `date_activated`, `date_created`, `date_updated`, `avatar_file`, `enabled`) VALUES
      (1, 'superuser', '$2y$12\$pGgcA25nepYs8xjR2Cx5BOzp5ogOW0z/KkTQ3TE8rZ8AYrBv6T51S', 'Superuser', '', '', 1, '1', 0, 0, '2019-03-26 00:00:00', '2019-03-26 00:00:00', '2019-03-26 00:00:00', '', 1)");

    // hris_eval_purpose
    $this->app_db->query("INSERT INTO `hris_eval_purpose` (`id`, `title`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'Promotion', '2019-06-19 04:09:16', '0000-00-00 00:00:00', 1),
      (2, 'Change Position', '2019-06-19 04:09:16', '0000-00-00 00:00:00', 1),
      (3, 'Transfer (Dept., Section, Area)', '2019-06-19 04:09:16', '0000-00-00 00:00:00', 1),
      (4, 'Dismissal / End of Contract ', '2019-06-19 04:09:16', '0000-00-00 00:00:00', 1),
      (5, 'Others (specify)', '2019-06-19 04:09:16', '0000-00-00 00:00:00', 1)");

    // hris_eval_questions
    $this->app_db->query("INSERT INTO `hris_eval_questions` (`id`, `title`, `description`, `section`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'KNOWLEDGE OF WORK', 'Evaluate the employee’s familiarity with all phases and details of the job. Measures effectiveness in keeping knowledgeable in methods, techniques and required skills; \r\nremaining current on new technologies and be able to apply it into the job in a short span of time.', 'A', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (2, 'QUALITY OF WORK', ' Evaluate the employee’s ability to deliver products that conforms to its requirements, work reliably and accurately in its intended manner, delivered on time, and freefrom defects. Take into account clients’ feedbacks ', 'A', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (3, 'QUANTITY OF WORK', 'Evaluate the employee’s ability to produce large amount / volume of work efficiently. Consider the number of projects and tasks accomplished (refer to Appendix page) ', 'A', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (4, 'DECISION MAKING/PROBLEM SOLVING', 'Evaluate the employee’s ability to identify problem areas, gather facts and making timely, practical decisions. ', 'A', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (5, 'ADMINISTRATION', 'Measures employee’s effectiveness in planning, organizing and efficiently handling work hours and eliminating unnecessary activities.', 'A', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (6, 'COMMITMENT', 'Make things happen by  creatively maximizing our resources to ensure each others success. Evaluate the employees’ tendency towards self-initiated actions without waiting for instructions. ', 'B', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (7, 'ACCOUNTABILITY', 'he obligations of an individual or organization to account for its activities, accept responsibilities for  them and to disclose the results  in a transparent manner. Makes carefully weighed decisions and accepts consequences for action and willingness to assume Responsibility. ', 'B', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (8, 'RESPONSIVENESS', 'The quality of reacting quickly and positively. Strive to be the best, not just better or good enough. Have the highest chance of becoming productive. Paying attention, care enough about what he or she is talking about, no waiting time. ', 'B', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (9, 'TRUSTWORTHY ', 'Honesty in everything you do; Take into consideration the employee’s willingness to put company interests above self-interest. Be reliable and keep your word. When you say that you will do something for someone, then do it. Make good friends with Manager/ \r\nSupervisor, Colleagues, Subordinates Clients and Customers. ', 'B', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (10, 'TEAMWORK AND COOPERATION', 'he process of working collaboratively with a group of people in order to achieve the common goal. Always seeks ways to continuously improve existing procedure  or process to prevent recurrence of problems. Actively participates in any problem solving activities with his team and uses these opportunities to coach and guide his staff. Proactively working together and being accountable to each other to achieve  \r\nour goal. Has a positive approach towards work, company policies and people. ', 'B', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (11, 'ADHERENCE TO COMPANY RULES & REGULATIONS', 'Evaluate the employees’ conformity  to company rules and regulations and policies. ', 'C', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (12, 'ATTENDANCE & PUNCTUALITY', 'Consider the employee’s absences and tardiness based on Attendance reports ', 'C', '2019-06-18 12:00:54', '0000-00-00 00:00:00', 1),
      (13, 'Hello World', 'Evaluate the employee’s familiarity with all phases and details of the job. Measures effectiveness in keeping knowledgeable in methods, techniques and required skills; \nremaining current on new technologies and be able to apply it into the job in a short span of time.', 'C', '2019-06-20 05:48:51', '0000-00-00 00:00:00', 0),
      (14, 'Product / Technical Knowledge', 'N/A', 'D', '2019-09-10 07:15:27', '0000-00-00 00:00:00', 0),
      (15, 'Product / Technical Knowledge', 'N/A', 'D', '2019-09-10 07:32:29', '0000-00-00 00:00:00', 1),
      (16, 'Energy, Determination and Work Rate', 'N/A', 'D', '2019-09-10 07:32:29', '0000-00-00 00:00:00', 1),
      (17, 'Problem Solving and Decision Making', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (18, 'Adaptability, Flexibility, and Mobility', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (19, 'Planning, Budgeting and forecasting', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (20, 'Time Management - Meeting deadlines and commitments', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (21, 'Commercial Judgement', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (22, 'Team working and developing others', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (23, 'Delegation skills', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (24, 'Communication skills', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (25, 'Reporting and Administration', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (26, 'Creativity', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (27, 'Steadiness under pressure - Composure', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (28, 'Corporate responsibility and Professional ethics', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1),
      (29, 'Personal appearance and image', 'N/A', 'D', '2019-09-10 07:37:18', '0000-00-00 00:00:00', 1)");

    // hris_eval_ratings
    $this->app_db->query("INSERT INTO `hris_eval_ratings` (`id`, `eval_type`, `rating`, `description`, `equivalent_rating`, `score`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'type_1', 5, 'Contributions have tremendous and consistently positive impact and value to the department and or the organization. May be unique, often one-time achievements that measurably improve progress towards organizational goals. Easily recognized as a top performer compared to peers. Viewed as an excellent resource for providing training, guidance, and support to others. Demonstrates high-level capabilities and proactively takes on higher levels of responsibility. ', 'OUTSTANDING PERFORMANCE (O) ', '95%-100% ', '2019-06-19 11:03:02', '0000-00-00 00:00:00', 1),
      (2, 'type_1', 4, 'Consistently demonstrates high level of performance. Consistently works toward overall objectives of the department and or organization. Viewed as a role model in position. Demonstrates high levels of effort, effectiveness, and judgment with limited or no supervision. ', 'Very Good Performance (VG) ', '86%-94% ', '2019-06-22 03:12:55', '0000-00-00 00:00:00', 1),
      (3, 'type_1', 3, 'Consistently demonstrates effective performance. Performance is reflective of a fully qualified and experienced individual in this position. Viewed as someone who gets the job done and effectively prioritizes work. Contributes to the overall objectives of the department and or the organization. Achieves valuable accomplishments in several critical areas of the job. ', 'Good Performance (G) ', '80%-85% ', '2019-06-22 03:15:30', '0000-00-00 00:00:00', 1),
      (4, 'type_1', 2, 'Working toward gaining proficiency. Demonstrates satisfactory performance inconsistently. Achieves some but not all goals and is acquiring necessary knowledge and skills. ', 'Fair Performance (F) ', '75%-79% ', '2019-06-22 03:15:48', '0000-00-00 00:00:00', 1),
      (5, 'type_1', 1, 'The quality of performance is inadequate and shows little or no improvement. Knowledge, skills, and abilities have not been demonstrated at appropriate levels. ', 'Poor Performance (P) ', '75%-below', '2019-06-22 03:16:02', '0000-00-00 00:00:00', 1),
      (8, 'type_1', 8, 'Contributions have tremendous and consistently positive impact and value to the department and or the organization. May be unique, often one-time achievements that measurably improve progress towards organizational goals. Easily recognized as a top performer compared to peers. Viewed as an excellent resource for providing training, guidance, and support to others. Demonstrates high-level capabilities and proactively takes on higher levels of responsibility.', 'fgsdgs', 'sdgsg', '2019-06-19 10:57:31', '0000-00-00 00:00:00', 0),
      (9, 'type_2', 5, 'Models the way', 'OUTSTANDING PERFORMANCE (O) ', '95%-100%', '2019-09-10 06:10:28', '0000-00-00 00:00:00', 1),
      (10, 'type_2', 4, 'Always exhibits competency', 'Very Good Performance (VG) ', '86%-94% ', '2019-09-10 06:10:28', '0000-00-00 00:00:00', 1),
      (11, 'type_2', 3, 'Exhibits competency most of the time', 'Good Performance (G) ', '80%-85% ', '2019-09-10 06:10:28', '0000-00-00 00:00:00', 1),
      (12, 'type_2', 2, 'Exhibits competency half of the time or occasionally ', 'Fair Performance (F) ', '75%-79% ', '2019-09-10 06:10:28', '0000-00-00 00:00:00', 1),
      (13, 'type_2', 1, 'Does not exhibits competency', 'Poor Performance (P) ', '75%-below', '2019-09-10 06:10:28', '0000-00-00 00:00:00', 1)");

    // hris_eval_recommendations
    $this->app_db->query("INSERT INTO `hris_eval_recommendations` (`id`, `description`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'Identify the employee’s strengths and other areas for improvement ', '2019-06-19 04:28:25', '0000-00-00 00:00:00', 1),
      (2, 'Necessary steps to improve employee’s performance  ', '2019-06-19 04:28:25', '0000-00-00 00:00:00', 1),
      (3, 'Training needs of the employee ', '2019-06-19 04:28:25', '0000-00-00 00:00:00', 1)");

    // hris_eval_section
    $this->app_db->query("INSERT INTO `hris_eval_section` (`id`, `section`, `title`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'A', 'TECHNICAL FACTORS RATINGS', '2019-06-18 11:24:41', '0000-00-00 00:00:00', 1),
      (2, 'B', 'CORE VALUES', '2019-06-18 11:24:41', '0000-00-00 00:00:00', 1),
      (3, 'C', 'POLICY-ORIENTED FACTORS', '2019-06-18 11:24:41', '0000-00-00 00:00:00', 1),
      (4, 'D', 'LEADERSHIP-ORIENTED FACTORS', '2019-09-10 05:53:23', '0000-00-00 00:00:00', 1),
      (5, 'E', 'OVER-ALL ASSESSMENT ', '2019-09-10 05:54:03', '0000-00-00 00:00:00', 1)");

    // hris_exchange_rates
    $this->app_db->query("INSERT INTO `hris_exchange_rates` (`id`, `base`, `currency_code`, `currency_name`, `exchange_rate`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'PHP', 'PHP', 'Philipine Peso', 1, '2019-09-17 05:51:05', '0000-00-00 00:00:00', 1)");

    // hris_main_navigation
    $this->app_db->query("INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES
      (1, 'Home', 'fa-home', 'home', 'acb_home', 'cb_home', 1, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
      (2, 'Sales', 'fa-shopping-cart', 'sales_home', 'acb_sales', 'cb_sales', 2, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
      (3, 'Purchases', 'fa-money', 'purchase_home', 'acb_purchases', 'cb_purchases', 3, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
      (4, 'Inventory', 'fa-tag', 'inventory_home', 'acb_inventory', 'cb_inventory', 4, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
      (5, 'Entity', 'fa-university', 'entity_home', 'acb_entity', 'cb_entity', 5, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
      (6, 'Manufacturing', 'fa-refresh', 'manufacturing_home', 'acb_manufacturing', 'cb_manufacturing', 6, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
      (7, 'Entity', 'fa-users', 'employees_home', 'acb_employees', 'cb_employees', 7, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
      (8, 'Settings', 'fa-cog', 'settings_home', 'acb_settings', 'cb_settings', 99, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
      (9, 'Cart Release', 'fa-square', 'cart_home', 'acb_packagecart', 'cb_packagecart', 9, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
      (10, 'Reports', 'fa-file-text', 'report_home', 'acb_reports', 'cb_reports', 97, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
      (11, 'QR Quick Search', 'fa fa-qrcode', 'qrcode_home', 'acb_qr', 'cb_qr', 11, '2018-07-17 00:00:00', '2018-07-17 00:00:00', 0),
      (12, 'Developer Settings', 'fa-wrench', 'dev_settings_home', 'acb_ds', 'cb_ds', 12, '2018-07-26 00:00:00', '2018-07-26 00:00:00', 1),
      (13, 'Time Record', 'fa fa-clock-o', 'time_record', 'acb_timerecord', 'cb_timerecord', 13, '2019-01-26 00:00:00', '2019-01-26 00:00:00', 1),
      (14, 'Transactions', 'fa fa-money', 'transaction_home', 'acb_tranactions', 'acb_tranactions', 14, '2019-01-26 00:00:00', '2019-01-26 00:00:00', 1),
      (15, 'Payroll', 'fa fa-credit-card', 'payroll', 'acb_payroll', 'cp_payroll', 15, '2019-02-28 00:00:00', '2019-02-28 00:00:00', 1),
      (16, 'Profile', 'fa-user-circle', 'profile', 'acb_profile', 'cb_profile', 16, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
      (17, 'Announcement', 'fa-bullhorn', 'announcement_home', 'acb_announcement', 'cb_announcement', 17, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
      (18, 'Payslip', 'fa fa-credit-card', 'payslip_home', 'acb_payslip', 'cb_payslip', 18, '2019-03-28 00:00:00', '2019-03-28 00:00:00', 1),
      (19, 'Change Password', 'fa-lock', 'changepass_home', 'acb_changepass', 'cb_changepass', 98, '2019-03-28 00:00:00', '2019-03-28 00:00:00', 1),
      (20, 'HR Assist', 'fa-id-badge', 'hrassist_home', 'acb_hrassist', 'cb_hrassist', 20, '2019-03-28 00:00:00', '2019-03-28 00:00:00', 1),
      (21, 'Leave', 'fa-sticky-note', 'leave_home', 'acb_leave', 'cb_leave', 19, '2019-05-04 00:00:00', '2019-05-04 00:00:00', 1),
      (22, 'Attendance', 'fa-bar-chart', 'attendance_home', 'acb_attendance', 'cb_attendance', 21, '2019-05-04 00:00:00', '2019-05-04 00:00:00', 1),
      (23, 'Evaluations', 'fa-id-badge', 'evaluations_home', 'acb_evaluations', 'cb_evaluations', 22, '2019-05-04 00:00:00', '2019-05-04 00:00:00', 1),
      (24, 'Register Id', 'fa-id-card-o', 'registerid_home', 'acb_registerid', 'cb_registerid', 23, '2019-07-04 00:00:00', '2019-07-04 00:00:00', 1)");

    // hris_position
    $this->app_db->query("INSERT INTO `hris_position` (`position_id`, `position`, `access_nav`, `access_sub_nav`, `access_content_nav`, `hierarchy_lvl`, `date_updated`, `date_created`, `enabled`) VALUES
      (1, 'Superuser', '19, 7, 23, 15, 24, 10, 8, 13, 14', '', '145, 155, 27, 173, 163, 161, 168, 132, 156, 6, 183, 7, 2, 10, 3, 134, 21, 187, 15, 162, 133, 1, 135, 4, 8, 18, 22, 5, 16, 17, 12, 9, 20, 160, 178, 14, 13, 175, 165, 167, 174, 188, 189, 184, 172, 171, 166, 182, 149, 136, 147, 139, 141, 157, 143, 148, 186, 140, 185, 150, 158, 159, 176, 177, 180, 181, 179', 0, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
      (2, 'Administrator', '19, 7, 23, 15, 24, 10, 8, 13, 14', '', '145, 155, 27, 173, 163, 161, 168, 132, 156, 6, 183, 7, 2, 10, 3, 134, 21, 187, 15, 162, 133, 1, 135, 4, 8, 18, 22, 5, 16, 17, 12, 9, 20, 160, 178, 14, 13, 175, 165, 167, 174, 188, 189, 184, 172, 171, 166, 182, 149, 136, 147, 139, 141, 157, 143, 148, 186, 140, 185, 150, 158, 159, 176, 177, 180, 181, 179', 1, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
      (3, 'HR Manager', '17, 22, 19, 7, 23, 20, 21, 15, 18, 16, 24, 10, 8, 13, 14', '', '145, 155, 27, 173, 163, 161, 168, 132, 156, 6, 183, 7, 2, 10, 3, 134, 21, 187, 15, 162, 133, 1, 135, 4, 8, 18, 22, 5, 16, 17, 12, 20, 14, 13, 175, 165, 167, 174, 188, 189, 184, 172, 171, 166, 149, 136, 147, 139, 141, 157, 143, 148, 186, 140, 185, 150, 158, 159, 164, 176, 177, 180, 181, 179', 2, '2019-05-15 09:58:35', '2019-03-26 00:00:00', 1),
      (4, 'HR Supervisor', '19', '', '', 3, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
      (5, 'Manager', '19, 23, 10, 14', '', '188, 150, 176', 4, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
      (6, 'Supervisor', '19', '', '', 5, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
      (7, 'Officer', '19', '', '', 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
      (8, 'Staff', '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 19', '', '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162,  163, 165, 166', 7, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
      (9, 'Employee', '17, 22, 19, 20, 21, 18, 16, 14', '', '150, 170', 8, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1)");

    // hris_worksched_settings
    $this->app_db->query("INSERT INTO `hris_worksched_settings` (`id`, `min_whours`, `max_whours`, `min_bhours`, `max_bhours`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 8, 12, 1, 2, '2019-07-30 09:23:07', '0000-00-00 00:00:00', 1)");

    // leaves
    $this->app_db->query("INSERT INTO `leaves` (`leaveid`, `description`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Maternity Leave', '2019-02-21 15:02:31', '2019-02-21 15:02:31', 17, 1),
      (2, 'Paternity Leave', '2019-02-21 15:02:38', '2019-02-21 15:02:38', 17, 1),
      (3, 'Sick Leave', '2019-02-21 15:02:44', '2019-02-21 15:02:44', 17, 1),
      (4, 'Vacation Leave', '2019-02-21 15:02:51', '2019-02-21 15:02:51', 17, 1)");

    // level
    $this->app_db->query("INSERT INTO `level` (`levelid`, `description`, `hierarchy_level`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (2, 'Administrator', 1, '2019-03-26 08:52:42', '2019-03-26 08:52:42', 17, 1),
      (3, 'HR Manager', 2, '2019-03-26 08:52:50', '2019-03-26 08:52:50', 17, 1),
      (4, 'HR Supervisor', 3, '2019-03-26 08:53:01', '2019-03-26 08:53:01', 17, 1),
      (5, 'Officer', 5, '2019-03-26 08:53:31', '2019-03-26 08:53:31', 17, 1),
      (6, 'Staff', 6, '2019-03-26 08:53:41', '2019-03-26 08:53:41', 17, 1),
      (7, 'Employee', 7, '2019-03-26 08:53:48', '2019-03-26 08:53:48', 17, 1)");

    // pagibig
    $this->app_db->query("INSERT INTO `pagibig` (`id`, `monthly_compensation`, `employee_share`, `employer_share`, `enabled`) VALUES
      (1, '1,500 and below', '1.00', '2.00', 1),
      (2, 'Over 1,500', '2.00', '2.00', 1)");

    // payoutmedium
    $this->app_db->query("INSERT INTO `payoutmedium` (`payoutmediumid`, `description`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Cash', '2019-04-06 07:29:18', '2019-04-06 07:29:18', 2, 1),
      (2, 'Cash Card', '2019-04-06 07:29:28', '2019-04-06 07:29:28', 2, 1),
      (3, 'Debit Card', '2019-04-06 07:29:36', '2019-04-06 07:29:36', 2, 1)");

    // paytype
    $this->app_db->query("INSERT INTO `paytype` (`paytypeid`, `description`, `frequency`, `date_range`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (2, 'Weekly', 4, '6-7', '2018-11-21 04:55:29', '2018-01-01 10:00:00', 1, 1),
      (3, 'Monthly', 1, '28-31', '2018-01-01 10:00:00', '2018-01-01 10:00:00', 1, 1),
      (15, 'Semi-Monthly', 2, '12-17', '2019-02-20 17:44:52', '2019-02-20 17:44:52', 17, 1)");

    // pb_company_helper
    $this->app_db->query("INSERT INTO `pb_company_helper` (`id`, `company_name`, `company_initial`, `company_logo`, `company_logo_small`, `company_address`, `company_website`, `company_phone`, `company_email`, `powered_by`, `paypanda_link`) VALUES
      (1, 'HRIS', 'PB', '1payroll4.jpg', 'pandabookslogo.png', '10th Floor Inoza Tower, 40th St., BGC, Taguig City 1634', 'https://www.pandabooks.ph/', '898-1309', 'support@cloudpanda.ph', 'Powered by <a href=\'http://cloudpanda.ph/\' class=\'external\' style=\'text-decoration:underline;\'>Cloud Panda PH</a>', 'www.paypanda.com.ph/bicore')");

    // pb_userrole_main_nav
    $this->app_db->query("INSERT INTO `pb_userrole_main_nav` (`id`, `label_val`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES
      (1, 'Sales', 'acb_sales', 'cb_sales', 1, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (2, 'Purchases', 'acb_purchases', 'cb_purchases', 2, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (3, 'Inventory', 'acb_inventory', 'cb_inventory', 3, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (4, 'Entity', 'acb_entity', 'cb_entity', 4, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (5, 'Manufacturing', 'acb_manufacturing', 'cb_manufacturing', 5, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (6, 'Accounts', 'acb_accounts', 'cb_accounts', 6, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (7, 'Settings', 'acb_settings', 'cb_settings', 7, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (8, 'Package Cart', 'acb_packagecart', 'cb_packagecart', 8, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (9, 'Reports', 'acb_reports', 'cb_reports', 9, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
      (10, 'QR Quick Search', 'acb_qr', 'cb_qr', 10, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1)");

    // philhealth
    $this->app_db->query("INSERT INTO `philhealth` (`phID`, `basic_mo_sal`, `basic_mo_sal1`, `mo_contribution`, `mo_contribution1`, `employee_share`, `employee_share1`, `employer_share`, `employer_share1`, `enabled`, `user_id`, `date_created`, `date_updated`) VALUES
      (1, 0, 10000, 0, 275, 0, 137.5, 0, 137.5, 1, 17, '2019-03-01 17:54:33', '2019-03-01 17:54:33'),
      (2, 10000, 39999.99, 275.02, 1099.99, 137.51, 549.99, 137.51, 549.99, 1, 17, '2019-03-01 17:55:45', '2019-03-01 17:55:45'),
      (3, 40000, 1000000, 1099.99, 1100, 549.99, 550, 549.99, 550, 1, 17, '2019-03-01 18:01:14', '2019-03-01 18:01:14')");

    // position
    $this->app_db->query("INSERT INTO `position` (`positionid`, `deptId`, `subDeptId`, `pos_access_lvl`, `description`, `levelid`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 1, 1, 3, 'HR Manager', 0, '2019-03-26 14:50:18', '2019-03-26 14:28:30', 0, 1),
      (2, 1, 1, 8, 'HR Staff', 0, '2019-03-26 17:31:52', '2019-03-26 17:31:52', 0, 1),
      (3, 1, 1, 5, 'Manager', 0, '2019-06-18 14:44:33', '2019-06-18 14:44:33', 0, 1),
      (4, 1, 1, 6, 'Supervisor', 0, '2019-09-27 15:14:11', '2019-09-27 15:14:11', 0, 1)");

    // relationship
    $this->app_db->query("INSERT INTO `relationship` (`relationshipid`, `description`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Mother', '2019-03-01 18:30:08', '2019-03-01 18:30:08', 2, 1),
      (2, 'Father', '2019-03-01 18:30:14', '2019-03-01 18:30:14', 2, 1),
      (3, 'Son', '2019-03-01 18:30:22', '2019-03-01 18:30:22', 2, 1),
      (4, 'Daughter', '2019-03-01 18:30:29', '2019-03-01 18:30:29', 2, 1),
      (5, 'Aun', '2019-03-01 18:30:38', '2019-03-01 18:30:38', 2, 1),
      (6, 'Uncle', '2019-03-01 18:30:45', '2019-03-01 18:30:45', 2, 1)");

    // salarycat
    $this->app_db->query("INSERT INTO `salarycat` (`salarycatid`, `description`, `type`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Basic Allowance', '', '2019-03-01 18:32:00', '2019-03-01 18:32:00', 2, 1),
      (2, 'Communication Allowance', '', '2019-03-01 18:32:09', '2019-03-01 18:32:09', 2, 1),
      (3, 'Transportation allowance', '', '2019-03-01 18:32:19', '2019-03-01 18:32:19', 2, 1),
      (4, 'Food Allowance', '', '2019-03-01 18:32:27', '2019-03-01 18:32:27', 2, 1)");

    // sss
    $this->app_db->query("INSERT INTO `sss` (`id`, `salRange_from`, `salRange_to`, `monthly_sal_cred`, `ss_er`, `ss_ee`, `ss_total`, `ec_er`, `tc_er`, `tc_ee`, `tc_total`, `SV_VM_OFW`, `enabled`) VALUES
      (4, 1000, 1249.99, 1000, 73.7, 36.3, 110, 10, 83.7, 36.3, 120, 0, 1),
      (5, 1250, 1749.989990234375, 1500, 110.5, 54.5, 165, 10, 120.5, 54.5, 175, 0, 1),
      (6, 1750, 2249.99, 2000, 147.3, 72.7, 220, 10, 157.3, 72.7, 230, 0, 1),
      (7, 2250, 2749.99, 2500, 184.2, 90.8, 275, 10, 194.2, 90.8, 285, 0, 1),
      (8, 2750, 3249.989990234375, 3000, 221, 109, 330, 10, 231, 109, 340, 0, 1),
      (9, 3250, 3749.99, 3500, 257.8, 127.2, 385, 10, 267.8, 127.2, 395, 0, 1),
      (10, 3750, 4249.99, 4000, 294.7, 145.3, 440, 10, 304.7, 145.3, 450, 0, 1),
      (11, 4250, 4749.99, 45000, 331.5, 163.5, 495, 10, 341.5, 163.5, 505, 0, 1),
      (12, 4250, 4749.990234375, 45000, 331.5, 163.5, 495, 10, 378.3, 163.5, 505, 0, 0),
      (13, 4750, 5249.99, 5000, 368.3, 181.7, 550, 10, 378.3, 181.7, 560, 0, 1),
      (14, 5250, 5749.99, 5500, 405.2, 199.8, 605, 10, 415.2, 199.8, 615, 0, 1),
      (15, 5750, 6249.990234375, 6000, 442, 218, 660, 10, 452, 218, 670, 0, 1),
      (16, 6250, 6749.990234375, 6500, 478.8, 236.2, 715, 10, 488.8, 236.2, 725, 0, 1),
      (17, 6750, 7249.990234375, 7000, 515.7, 254.3, 770, 10, 525.7, 254.3, 780, 0, 1),
      (18, 7250, 7249.990234375, 7500, 552.5, 272.5, 825, 10, 562.5, 272.5, 835, 0, 1),
      (19, 7750, 8249.99, 8000, 589.3, 290.7, 880, 10, 599.3, 290.7, 890, 0, 1),
      (20, 8250, 8749.99, 8500, 626.2, 308.8, 935, 10, 636.2, 308.8, 945, 0, 1),
      (21, 8750, 9249.990234375, 9000, 663, 327, 990, 10, 673, 327, 1000, 0, 1),
      (22, 9250, 9749.99, 9500, 699.8, 345.2, 1045, 10, 709.8, 345.2, 1055, 0, 1),
      (23, 9750, 10249.99, 10000, 736.7, 363.3, 1100, 10, 746.7, 363.3, 1110, 0, 1),
      (24, 10250, 10749.99, 10500, 773.5, 381.5, 1155, 10, 783.5, 381.5, 1165, 0, 1),
      (25, 10750, 11249.99, 11000, 810.3, 399.7, 1210, 10, 820.3, 399.7, 1220, 0, 1),
      (26, 11250, 11749.99, 11500, 8847.2, 417.8, 1265, 10, 857.2, 417.8, 1275, 0, 1),
      (27, 11750, 12.249, 12000, 884, 436, 1320, 10, 894, 436, 1330, 0, 1),
      (28, 12250, 12749.99, 12500, 920.8, 454.2, 1375, 10, 930.8, 454.2, 1385, 0, 1),
      (29, 12750, 13249.99, 13000, 957.7, 472.3, 1430, 10, 967.7, 472.3, 1440, 0, 1),
      (30, 13250, 3749.99, 13500, 994.5, 490.5, 1485, 10, 1004.5, 490.5, 1495, 0, 1),
      (31, 13750, 14249.99, 14000, 1031.3, 508.7, 1540, 10, 1041.3, 508.7, 1550, 0, 1),
      (32, 14250, 14749.99, 14500, 1068.2, 526.8, 1595, 10, 1078.2, 526.8, 1605, 0, 1),
      (33, 14750, 15249.99, 15000, 1105, 545, 1650, 30, 1135, 545, 1680, 0, 1),
      (34, 15250, 15749.99, 15500, 1141.8, 563.2, 1705, 30, 1171.8, 563.2, 1735, 0, 1),
      (35, 15750, 1000000, 16000, 1178.7, 581.3, 1760, 30, 1208.7, 581.3, 1790, 0, 0)");

    // subdept
    $this->app_db->query("INSERT INTO `subdept` (`subdeptid`, `description`, `departmentid`, `date_updated`, `date_created`, `user_id`, `enabled`) VALUES
      (1, 'Management', 1, '2019-03-26 06:46:44', '2019-03-26 06:46:44', 17, 1)");

    // tax
    $this->app_db->query("INSERT INTO `tax` (`id`, `aibLowerLimit`, `aibUpperLimit`, `tr1LowerLimit`, `tr1ExcessLimit`, `tr2LowerLimit`, `tr2ExcessLimit`, `enabled`) VALUES
      (8, 0, 250000, 0, 0, 0, 0, 1),
      (9, 250000, 400000, 0, 20, 0, 15, 1),
      (10, 800000, 2000000, 130000, 30, 102500, 25, 1),
      (11, 2000000, 8000000, 490000, 32, 402500, 30, 1),
      (12, 8000000, 8000001, 2410000, 35, 2202500, 35, 1)");

    // hris_template_settings
    $this->app_db->query("INSERT INTO `hris_template_settings` (`id`, `name`, `field_name`, `table_name`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'First Name', 'first_name', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (2, 'Middle Name', 'middle_name', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (3, 'Last Name', 'last_name', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (4, 'Birthday', 'birthday', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (5, 'Gender', 'gender', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (6, 'Marital Status', 'marital_status', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (7, 'Home Address 1', 'home_address1', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (8, 'Home Address 2', 'home_address2', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (9, 'City', 'city', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (10, 'Country', 'country', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (11, 'Contact No.', 'contact_no', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (12, 'Email', 'email', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (13, 'SSS Number', 'sss_no', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (14, 'Philhealth Number', 'philhealth_no', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (15, 'Pagibig Number', 'pagibig_no', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (16, 'Tin number', 'tin_no', 'employee_record', '2019-11-15 08:05:17', '0000-00-00 00:00:00', 1),
      (17, 'Contract Start', 'contract_start', 'contract', '2019-11-15 08:12:20', '0000-00-00 00:00:00', 1),
      (18, 'Salary', 'sal_cat', 'contract', '2019-11-15 08:11:54', '0000-00-00 00:00:00', 1),
      (19, 'Basic Pay', 'base_pay', 'contract', '2019-11-15 08:11:54', '0000-00-00 00:00:00', 1),
      (20, 'Total Salary', 'total_sal', 'contract', '2019-11-15 08:11:54', '0000-00-00 00:00:00', 1),
      (21, 'Total Converted Salary to Peso', 'total_sal_converted', 'contract', '2019-11-15 08:11:54', '0000-00-00 00:00:00', 1),
      (22, 'Leave', 'emp_leave', 'contract', '2019-11-15 08:11:54', '0000-00-00 00:00:00', 1),
      (23, 'Total Leave', 'total_leave', 'contract', '2019-11-15 08:11:54', '0000-00-00 00:00:00', 1),
      (24, 'WorkSite', 'worksite', 'worksite', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (25, 'Position', 'position', 'position', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (26, 'Position Access Level', 'pos_access_lvl', 'hris_position', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (27, 'Company Name', 'company', 'hris_companies', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (28, 'Work Schedule', 'work_sched', 'work_schedule', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (29, 'Employment Status', 'emp_status', 'empstatus', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (30, 'Payout Medium', 'payout_medium', 'payoutmedium', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (31, 'PayType', 'paytype', 'paytype', '2019-11-15 08:31:38', '0000-00-00 00:00:00', 1),
      (32, 'Date Today', 'date_today', 'default', '2019-11-15 09:25:25', '0000-00-00 00:00:00', 1),
      (33, 'Manager Position', 'manager_position', 'default', '2019-11-15 09:49:35', '0000-00-00 00:00:00', 1),
      (34, 'Manager Name', 'manager_name', 'default', '2019-11-15 09:49:35', '0000-00-00 00:00:00', 1),
      (35, 'Total Work Hour (less break)', 'total_whours', 'work_schedule', '2019-11-15 09:49:35', '0000-00-00 00:00:00', 1),
      (36, 'Schedule Type', 'sched_type', 'work_schedule', '2019-11-15 09:49:35', '0000-00-00 00:00:00', 1),
      (37, 'Bank ', 'bank', 'bank', '2019-11-15 09:49:35', '0000-00-00 00:00:00', 1),
      (38, 'Total Break Hours', 'total_bhours', 'work_schedule', '2019-11-15 09:55:22', '0000-00-00 00:00:00', 1),
      (39, 'Total Work Hours', 'total_whours2', 'work_schedule', '2019-11-15 09:55:22', '0000-00-00 00:00:00', 1),
      (40, 'HR Manager', 'hr_manager', 'default', '2019-11-15 10:08:14', '0000-00-00 00:00:00', 1),
      (41, 'Checkbox', 'check_box', 'default', '2019-11-15 10:08:14', '0000-00-00 00:00:00', 1),
      (42, 'Department', 'department', 'department', '2019-11-15 10:08:14', '0000-00-00 00:00:00', 1),
      (43, 'Salary Category', 'sal_cat', 'contract', '2019-11-27 03:15:19', '0000-00-00 00:00:00', 0),
      (44, 'Signature', 'signature', 'default', '2019-11-15 10:08:14', '0000-00-00 00:00:00', 1),
      (45, 'Input Date', 'input_date', 'default', '2019-11-15 10:08:14', '0000-00-00 00:00:00', 1),
      (46, 'Input Text', 'input_text', 'default', '2019-11-15 10:08:14', '0000-00-00 00:00:00', 1),
      (47, 'Contract End', 'contract_end', 'contract', '2019-12-20 09:49:56', '0000-00-00 00:00:00', 1)");

    // hris_contract_template 1
    $this->app_db->query("INSERT INTO `hris_contract_template` (`id`, `template_name`, `template_format`, `created_at`, `updated_at`, `enabled`) VALUES
      (1, 'Job Offer Templates', '<p><span class=\"date_today\"><span style=\"font-weight: bolder;\">Date Today&nbsp;</span></span></p><p><span class=\"date_today\"><span style=\"font-weight: bolder;\"><br></span></span></p><p><span class=\"date_today\"><span style=\"font-weight: bolder;\">MR.&nbsp;</span></span><span class=\"first_name\"><span style=\"font-weight: bolder;\">First Name&nbsp;</span></span><span class=\"middle_name\"><span style=\"font-weight: bolder;\">Middle Name&nbsp;</span></span><span class=\"last_name\"><span style=\"font-weight: bolder;\">Last Name&nbsp;</span></span></p><p><span class=\"home_address1\"><span style=\"font-weight: bolder;\">Home Address 1&nbsp;</span></span></p><p><span class=\"home_address2\"><span style=\"font-weight: bolder;\">Home Address 2</span></span><span class=\"home_address1\"><span style=\"font-weight: bolder;\"><br></span></span></p><p><span class=\"home_address1\"><span style=\"font-weight: bolder;\"><br></span></span></p><p><span class=\"home_address1\">Dear Mr.&nbsp;</span><span class=\"last_name\"><span style=\"font-weight: bolder;\">Last Name&nbsp;</span></span></p><p><span class=\"last_name\">We are writing to formalyy notify you that we are happy to have you on board!&nbsp;</span></p><p><span class=\"last_name\">For your employment with us, we are offering you the following terms and conditions:&nbsp;</span></p><p><span class=\"last_name\"><br></span></p><p><span class=\"last_name\"><span style=\"font-weight: bold;\">CLOUD PANDA PHILS INC.&nbsp;</span>is offering you a full time employement for the position of&nbsp;</span></p><p><span class=\"position\"><span style=\"font-weight: bolder;\">Position.&nbsp;</span>For your employment contract, we are pleased to offer you the following:&nbsp;</span></p><p><span class=\"position\"><br></span></p><p><span class=\"position\">For your&nbsp;<span style=\"font-weight: bold;\">employement contract</span>,&nbsp; we are pleased to offer you the following:&nbsp;</span></p><p></p><div style=\"text-align: justify;\">a. One (1) Month Training&nbsp;</div><div style=\"text-align: justify;\">b. Five (5) Months Contractual&nbsp;</div><div style=\"text-align: justify;\">c. Six (6) Months Probationary</div><div style=\"text-align: justify;\"><br></div><div style=\"text-align: justify;\">You will be directly reporting to&nbsp;<span class=\"manager_name\"><span style=\"font-weight: bolder;\">Manager Name</span></span>&nbsp;our&nbsp;<span class=\"manager_position\"><span style=\"font-weight: bolder;\">Manager Position</span></span>&nbsp;from&nbsp;<span style=\"font-weight: bold;\">Monday to Saturday</span>.&nbsp;You are requested to render to work in 5 days week and&nbsp;<span class=\"total_whours\"><span style=\"font-weight: bolder;\">Total Work Hour (less break)&nbsp;</span>hours of work on a&nbsp;</span><span class=\"sched_type\"><span style=\"font-weight: bolder;\">Schedule Type Time In&nbsp;</span>between&nbsp;<span style=\"font-weight: bold;\">08:00 AM until 11:00 AM</span>&nbsp;only (log in time or start of shift) to 8pm only (log out time). Working time schedule will be based on the time in log and should be complete the&nbsp;</span><span class=\"total_whours2\"><span style=\"font-weight: bolder;\">Total Work Hours</span></span><span class=\"sched_type\">&nbsp;hours a day before time log out. For one (</span><span class=\"total_bhours\"><span style=\"font-weight: bolder;\">Total Break Hours</span></span><span class=\"sched_type\">) hour considered unpaid break time. You agree to render extended work hours when so required by Employer.&nbsp;</span></div><div style=\"text-align: justify;\"><span class=\"sched_type\"><br></span></div><div style=\"text-align: justify;\"><span class=\"sched_type\">For this position, we are pleased to offer you&nbsp;<span style=\"font-weight: bold;\">Php</span>&nbsp;</span><span class=\"total_sal\"><span style=\"font-weight: bolder;\">Total Salary / monthly.&nbsp;&nbsp;</span>Salary will be paid in a&nbsp;</span><span class=\"paytype\"><span style=\"font-weight: bolder;\">PayType</span></span></div><span class=\"last_name\">&nbsp;schedule (every 15th and 30th of the month) and will be released thru&nbsp;</span><span class=\"bank\"><span style=\"font-weight: bolder;\">Bank&nbsp;</span></span><span class=\"payout_medium\"><span style=\"font-weight: bolder;\">Payout Medium&nbsp;</span>provided by the Company.</span><p></p><p><span class=\"payout_medium\"><br></span></p><p><span class=\"payout_medium\">As an employee, your are entitled to the following benefits:&nbsp;</span></p><p><span class=\"payout_medium\" style=\"font-weight: bold;\">Mandatory Benefits:</span></p><p>The employee shall receive all the benefits required by Law which includes SSS, PHIC, HDMF and 13th Month Pay.</p><p>For SSS, PHIC AND HDMF, please take note that the employer will deduct a certain amount from your monthly payroll as part of employee share. This deduction will be based on the given salary bracked from the said government agencies.&nbsp;</p><p><span style=\"font-weight: bold;\">Leave Benefits:</span></p><p>Employee will be entitled to Six (6) days Sick Leave and Vacation Leave with pay after regularization. Unused leave credits are convertible to cash at the end of the calendar year.&nbsp;</p><p><span style=\"font-weight: bold;\">HMO:&nbsp;</span></p><p>The employee shall receive a Health Card with a maximum benefit limit of Php 70,000 per illness per year. This will avail after 2 years of service in the company based on the employment of start date.</p><p><br></p><p>Should you be amenable to the above, please affix your signature on the space provided below as your acceptance of this employment offer.</p><p><br></p><p>Very Truly Yours,</p><p><br></p><p>_________________</p><p><span class=\"hr_manager\"><span style=\"font-weight: bolder;\">HR Manager</span></span></p><p><span class=\"hr_manager\">HR Manager</span></p><p><span class=\"hr_manager\"><br></span></p><p><span class=\"hr_manager\">Conforme:&nbsp;</span></p><p><span class=\"hr_manager\"><br></span></p><p><span class=\"hr_manager\">__________________</span></p><p><span class=\"first_name\"><span style=\"font-weight: bolder;\">First Name&nbsp;</span></span><span class=\"middle_name\"><span style=\"font-weight: bolder;\">Middle Name&nbsp;</span></span><span class=\"last_name\"><span style=\"font-weight: bolder;\">Last Name</span></span></p><p><span class=\"last_name\"><span style=\"font-weight: bolder;\"><br></span></span></p><p><span class=\"last_name\"><span style=\"font-weight: bolder;\">___________________</span></span></p><p><span class=\"date_today\"><span style=\"font-weight: bolder;\">Date Today</span></span><span class=\"last_name\"><span style=\"font-weight: bolder;\"><br></span></span><span class=\"hr_manager\"><br></span></p><p><br></p><p><br></p>\r\n              ', '2019-11-20 11:18:50', '2019-11-20 11:18:50', 0),
      (2, 'asdfas', '<p><span class=\"first_name\"><strong>First Name</strong></span>\r\n              </p>', '2019-11-20 09:39:08', '2019-11-20 09:39:08', 0),
      (3, 'sdfsdfdsf', '<p><span class=\"first_name\"><strong>First Name</strong></span>\r\n              </p>', '2019-11-20 11:18:52', '2019-11-20 11:18:52', 0),
      (4, 'Job Offer Template', '<p><strong>Date Today</strong></p><p><strong><br></strong></p><p><strong>MR.&nbsp;</strong><img class=\"first_name\"><strong>First Name&nbsp;</strong><img class=\"middle_name\"><strong>Middle Name&nbsp;</strong><img class=\"last_name\"><strong>Last Name&nbsp;</strong></p><p><strong>Home Address 1&nbsp;</strong></p><p><strong>Home Address 2</strong><img class=\"home_address2\"><strong><br></strong></p><p><strong><br></strong></p><p>Dear Mr.&nbsp;<img class=\"last_name\"><strong>Last Name&nbsp;</strong></p><p>We are writing to formalyy notifiy you that we are happy to have you on board!&nbsp;</p><p>For your employement with us, we are offering you the following terms and conditions:&nbsp;</p><p><br></p><p><span style=\"font-weight: bold;\">CLOUD PANDA PHILS INC. </span>is offering you a full time employment for the positon of&nbsp;<img class=\"position\"><strong>Position. </strong>For your employment contract, we are pleased to offer you the following:&nbsp;</p><p><br></p><p>For your <span style=\"font-weight: 700;\">employment contract, </span>we are pleased to offer you the follwing:&nbsp;</p><p>a. One (1) Month Training&nbsp;</p><p>b. FIve (5) Months Contractual&nbsp;</p><p>c. Six (6) Months Probationary&nbsp;</p><p><br></p><p>You will be directly reporting to <span style=\"font-weight: bold;\">Mr.&nbsp;</span><img class=\"manager_name\"><strong>Manager Name </strong>our&nbsp;<img class=\"manager_position\"><strong>Manager Position </strong>from <span style=\"font-weight: bold;\">Mondays to Saturday. </span>You are requested to render to work in 5 days week and <span style=\"font-weight: 700;\">atleast eight (8) hours of work </span>on a <span style=\"font-weight: bold;\">flexible Time In</span>&nbsp;between <span style=\"font-weight: bold;\">08:00 AM until 11:00 AM only </span>(log In time or start of shift) to 8pm only (log out time). Working time schedule will be based on the time in log and should be complete the 9 hours a day before time log out. For one (1) hour considered unpaid break time. You agree to render extended work hours when so required by the Employer.</p><p>For this position, we are pleased to offer you <span style=\"font-weight: bold;\">Php&nbsp;</span><img class=\"total_sal\"><strong>Total Salary / monthly. </strong>Salary will be paid in a semi-monthly schedule (every 15th and 30th of the month) and will be released thru Metrobank debit card provided by the Company.&nbsp;<br></p><p>As an employee, you are entitled to the following benefits:&nbsp;</p><p><span style=\"font-weight: bold;\">Mandatory Benefits:&nbsp;</span></p><p>The employee shall receive all the benefits required by Law which includes SSS, PHIC, HDMF and 13th month pay.&nbsp;</p><p>For SSS, PHIC and HDMF, please take note that the employer will deduct a certain amount form your monthly payroll as part of employee share. This deduction will be based on the giver salary bracket from the said government agencies.&nbsp;</p><p><br></p><p><span style=\"font-weight: bold;\">Leave Benefits:&nbsp;</span></p><p>Employee will be entitled to Six (6) days Sick Leave and Vacation Leave with pay after regularization. Unsued leave credits are convertible to cash at the end of the calendar year.&nbsp;</p><p><br></p><p><span style=\"font-weight: bold;\">HMO:&nbsp;</span></p><p>The employee shall receive a Health Card with a maximum benefit limit of Php 70, 000 per illness per year. This will avail after 2 years of service in the company based on the employment of start date.&nbsp;</p><p><br></p><p>Shoud you be amenable to the above, please affix your signature on the space provided below as your acceptance of this employment offer.</p><p><br></p><p>Very Truly Yours,</p><p><br></p><p>_________________</p><p><img class=\"hr_manager\"><strong>HR Manager</strong></p><p>HR Manager&nbsp;</p><p><br></p><p>Conforme:&nbsp;</p><p><br></p><p><span style=\"font-weight: bold;\">_________________</span></p><p><strong>First Name</strong><img class=\"first_name\">&nbsp;<img class=\"middle_name\"><strong>Middle Name&nbsp;</strong><img class=\"last_name\"><strong>Last Name</strong><span style=\"font-weight: bold;\"><br></span><br><br></p><p><br><img class=\"home_address1\"><strong><br></strong><strong><br></strong><img class=\"date_today\">\r\n              </p>', '2019-11-25 07:06:29', '2019-11-25 07:06:29', 0),
      (5, 'Sample Format', '<p><img class=\"date_today\"><strong>Date Today&nbsp;</strong></p><p><strong><br></strong></p><p><strong>MR.&nbsp;</strong><img class=\"first_name\"><strong>First Name&nbsp;</strong><img class=\"middle_name\"><strong>Middle Name&nbsp;</strong><img class=\"last_name\"><strong>Last Name&nbsp;</strong></p><p><strong>Home Address 1&nbsp;</strong></p><p><strong>Home Address 2&nbsp;</strong></p><p><strong><br></strong></p><p>Dear Mr.&nbsp;<img class=\"last_name\"><strong>Last Name&nbsp;</strong></p><p>We are writing to formaly notify you that we are happy to have you on board!&nbsp;</p><p>For your employment with us, we are offering you the following terms and conditions:&nbsp;</p><p><br></p><p><span style=\"font-weight: 700;\">CLOUD PANDA PHILS INC. </span>is offering you a&nbsp; full time employment for the position of&nbsp;&nbsp;<img class=\"position\"><strong>Position. </strong>For your employment contract, we are pleased to offer you the following:&nbsp;</p><p>For your employment contract, we are pleased to offer you the following:&nbsp;</p><p>a. One (1) Month Training&nbsp;</p><p>b. Five (5) Months Contractual&nbsp;</p><p>c. Six (6) Months Probationary&nbsp;<br><br><img class=\"home_address2\"><strong><br></strong><img class=\"home_address1\"><strong><br></strong><strong><br></strong><br></p><p><br></p><p>              </p>', '2019-11-25 07:06:34', '2019-11-25 07:06:34', 0),
      (6, 'Sample Format 2', '<p><br></p><p><img class=\"date_today\"><strong>Date Today&nbsp;</strong></p><p><strong>Mr.&nbsp;</strong><img class=\"first_name\"><strong>First Name&nbsp;&nbsp;</strong><img class=\"middle_name\"><strong>Middle Name&nbsp;&nbsp;</strong><img class=\"last_name\"><strong>Last Name&nbsp;</strong></p><p><strong><br></strong><img class=\"home_address1\"><strong>Home Address 1</strong></p><p><strong>Home Address 2</strong><img class=\"home_address2\"><strong><br></strong></p><p>Dear Mr.&nbsp;<img class=\"last_name\"><strong>Last Name</strong><br><br></p><p>              </p>', '2019-11-25 07:06:37', '2019-11-25 07:06:37', 0),
      (7, 'Sample Format 3', '<p><img><strong class=\"date_today\">Date Today&nbsp;</strong></p><p><strong class=\"date_today\"><br></strong></p><p><strong class=\"date_today\">MR.&nbsp;</strong><img><strong class=\"first_name\">First Name&nbsp;</strong><img><strong class=\"middle_name\">Middle Name&nbsp;</strong><img><strong class=\"last_name\">Last Name&nbsp;</strong></p><p><strong class=\"home_address1\">Home Address 1&nbsp;</strong></p><p><strong class=\"home_address2\">Home Address 2&nbsp;</strong><img>&nbsp;</p><p><strong class=\"home_address1\"><br></strong></p><p><span class=\"home_address1\">Dear Mr.&nbsp;</span><img><strong class=\"last_name\">Last Name&nbsp;</strong></p><p><br></p><p>We are writing to formally notify you that we are happy to have you on board!&nbsp;</p><p>For your employment with us, we are offering you the following terms and conditions.&nbsp;</p><p><br></p><p><span style=\"font-weight: 700;\">CLOUD PANDA PHILS INC. </span>is offering you a full time employment for the position of&nbsp;<img><strong class=\"position\">Position. </strong><span class=\"position\">For your employment contract, we are pleased to offer you the following:&nbsp;</span></p><p><span class=\"position\">For your employement contract, we are pleased to offer you the following:&nbsp;</span></p><p><span class=\"position\">a. One (1) Month Training&nbsp;</span></p><p><span class=\"position\">b. Five (5) Months Contractual&nbsp;</span></p><p><span class=\"position\">c. Six (6) Months Probationary</span></p><p><span class=\"home_address1\"><br></span><img><strong class=\"last_name\"><br></strong><strong class=\"date_today\"><br></strong><br></p>', '2019-11-25 07:06:39', '2019-11-25 07:06:39', 0),
      (8, 'Sample Template 4', '<p><b>Date Today&nbsp;</b></p><p><b><br></b></p><p><b>MR.&nbsp;</b><img class=\"first_name\"><b>First Name&nbsp;</b><img class=\"middle_name\"><b>Middle Name&nbsp;</b><img class=\"last_name\"><b>Last Name&nbsp;</b></p><p><b>Home Address 1</b><img class=\"home_address1\"><b><br></b></p><p><b>Home Address 2</b><img class=\"home_address2\"><b><br></b><b><br></b><img class=\"date_today\">\r\n              </p>', '2019-11-25 07:06:42', '2019-11-25 07:06:42', 0),
      (9, 'Sample 5', '<p><span class=\"date_today\"><b>Date Today</b></span><span>&nbsp;</span></p><p><span><br></span></p><p><span style=\"font-weight: bold;\">MR.&nbsp;</span><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p><p><span class=\"home_address1\"><b>Home Address 1</b></span><span>&nbsp;</span></p><p><span class=\"home_address2\"><b>Home Address 2</b></span><span>&nbsp;</span><span><br></span><span><br></span></p>', '2019-11-25 07:06:32', '2019-11-25 07:06:32', 0),
      (10, 'Job Offer', '<p><span class=\"date_today\"><b>Date Today</b></span><span>&nbsp;</span></p><p><span><br></span></p><p><span style=\"font-weight: bold;\">MR.&nbsp;</span><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p><p><span class=\"home_address1\"><b>Home Address 1</b></span><span>&nbsp;</span></p><p><span class=\"home_address2\"><b>Home Address 2</b></span><span>&nbsp;</span></p><p><span><br></span></p><p><span>Dear Mr.&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p><p><span>We are writing to formally notify you that we are happy to have you on board!&nbsp;</span></p><p><span>For your employment with us, we are offering you the following terms and conditions:&nbsp;</span></p><p><span><br></span></p><p><span><span style=\"font-weight: bold;\">CLOUD PANDA PHILS INC. </span>is offering you a full time employment for the position of&nbsp;</span><input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><span class=\"input-text-container\"></span><span>&nbsp;</span><span class=\"position\">For your employment contract, we are pleased to offer you the following:&nbsp;</span></p><p><span class=\"position\"><br></span></p><p><span class=\"position\">For your employement contract, we are pleased to offer you the following:&nbsp;</span></p><p><span class=\"position\">a. One (1) Month Training</span></p><p><span class=\"position\">b. Five (5) Months Contractual&nbsp;</span></p><p><span class=\"position\">c. Six (6) Months Probationary</span></p><p><br></p><p>You will be directly reporting to <span style=\"font-weight: bold;\">Mr.&nbsp;</span><input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><span class=\"input-text-container\"></span>our <input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><span class=\"input-text-container\"></span>from <span style=\"font-weight: bold;\">Mondays to Saturday. </span>You are requested to render to work in 5 days week and <span style=\"font-weight: bold;\">atleast eight (8) hours of work </span>on a<span style=\"font-weight: bold;\">&nbsp;flexible Time In </span>between <span style=\"font-weight: bold;\">08:00 AM until 11:00 AM only </span>(log In time or start of shift) to 8pm only (log out time). Working time schedule will be based on the time in log and should be complete the 9 hours a day before time log out. For one (1) hour considered unpaid break time. You agree to render extended work hours when so required by the Employer.&nbsp;</p><p>For this position, we are pleased to offer you <span style=\"font-weight: bold;\">Php&nbsp;</span><input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><span class=\"input-text-container\"></span><span>/ monthly. Salary will be paid in a semi-monthly schedule (every 15th and 30th of the month) and will be released thru&nbsp;</span><input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><span class=\"input-text-container\"></span><span class=\"input-text-container\"></span><span>provided by the Company.</span></p><p><span>As an employee, you are entitled to the following benefits:</span></p><p><span style=\"font-weight: bold;\">Mandatory Benefits:&nbsp;</span></p><p>The employee shall receive all the benefits required by Law which includes SSS, PHIC, HDMF and 13th month pay.</p><p>For SSS, PHIC and HDMF, please take note that the employer will deduct a certain amount from you monthly payroll as part of employee share. This deduction will be based on the given salary bracket from the said government agencies.</p><p><br></p><p><span style=\"font-weight: bold;\">HMO:&nbsp;</span></p><p>The employee shall receive a Health Card with a maximum benefit limit This will avail after 2 years of service in the company based on the employment of start date.</p><p><br></p><p>Should you be amenable to the above, please affix your signature on the space provided below as your acceptance of this employment offer.</p><p><br></p><p>Very Truly Yours,</p><p><br></p><p><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span>&nbsp;</span><br></p><p><span style=\"font-weight: 700;\">AIDA G. PADILLA</span></p><p><span>HR Manager</span></p><p><span><br></span></p><p><span>Conforme:</span></p><p><span><br></span></p><p><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span>&nbsp;</span><br></p><p><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><span><br></span><span><br></span><span><br></span><span><br></span><span><br></span></p>', '2019-12-10 02:22:20', '0000-00-00 00:00:00', 1),
      (11, 'Employee Update Form', '<div style=\"text-align: center;\"><span style=\"font-weight: bold; text-decoration-line: underline;\">EMPLOYEE UPDATE FORM</span></div><div style=\"text-align: center;\"><span style=\"font-weight: bold; text-decoration-line: underline;\"><br></span></div><div style=\"text-align: left;\"><span class=\"date_today\"><b>Date Today</b></span><span>&nbsp;</span></div><div style=\"text-align: left;\"><span><br></span></div><div style=\"text-align: left;\"><span><br></span></div><table class=\"table table-bordered\"><tbody><tr><td><p>Name:&nbsp;<span class=\"first_name\"><b>First Name</b></span><span>&nbsp;&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p></td><td><p>Department:&nbsp;<span class=\"department\"><b>Department</b></span><span>&nbsp;</span></p></td></tr><tr><td><p>Position:&nbsp;<span class=\"position\"><b>Position</b></span><span>&nbsp;</span></p></td><td><p>Date of Employment:&nbsp;<span class=\"contract_start\"><b>Contract Start</b></span><span>&nbsp;</span></p></td></tr></tbody></table><div style=\"text-align: left;\"><span><br></span></div><table class=\"table table-bordered\"><tbody><tr><td><p><span style=\"font-weight: bold;\">Nature of Change:</span></p><p><span>&nbsp;</span><input type=\"checkbox\" name=\"checkbox[]\" class=\"check_box\">&nbsp; NEW EMPLOYEE&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span>&nbsp;</span><input type=\"checkbox\" name=\"checkbox[]\" class=\"check_box\">&nbsp;RECLASSIFICATION&nbsp;</p><p><span>&nbsp;</span><input type=\"checkbox\" name=\"checkbox[]\" class=\"check_box\">&nbsp;REGULARIZATION&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input type=\"checkbox\" name=\"checkbox[]\" class=\"check_box\">&nbsp;LATERAL TRANSFER&nbsp;</p><p><span>&nbsp;</span><input type=\"checkbox\" name=\"checkbox[]\" class=\"check_box\">&nbsp;SALARY ADJUSTMENT&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span>&nbsp;</span><input type=\"checkbox\" name=\"checkbox[]\" class=\"check_box\">&nbsp;GRATUITY/LOYALTY PAY</p><p><span>&nbsp;</span><input type=\"checkbox\" name=\"checkbox[]\" class=\"check_box\">&nbsp;PROMOTION<span style=\"font-weight: bold;\"><br></span></p></td></tr></tbody></table><div style=\"text-align: left;\"><span><br></span></div><table class=\"table table-bordered\"><tbody><tr><td>POSITION TITLE</td><td><p><span class=\"position\"><b>Position</b></span><span>&nbsp;</span><br></p></td></tr><tr><td>DEPARTMENT</td><td><p><span class=\"department\"><b>Department</b></span><span>&nbsp;</span><br></p></td></tr><tr><td>EMPLOYMENT STATUS</td><td><p><span class=\"emp_status\"><b>Employment Status</b></span><span>&nbsp;</span><br></p></td></tr><tr><td>SALARY/ WAGE</td><td><p><span class=\"sal_cat\"><b>Salary</b></span><span>&nbsp;</span><br></p></td></tr><tr><td>EFFECTIVITY DATE</td><td><p><span class=\"contract_start\"><b>Contract Start</b></span><span>&nbsp;</span><br></p></td></tr></tbody></table><div style=\"text-align: left;\"><span><br></span></div><div style=\"text-align: left;\"><span><br></span></div><table class=\"table table-bordered\"><tbody><tr><td><p>Prepared BY:</p><p><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span>&nbsp;</span></p><p><span><span style=\"font-weight: bold;\">AIDA G. PADILLA</span><br></span><br></p></td><td><p>Noted By:</p><p><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span>&nbsp;</span></p><p><span><span style=\"font-weight: bold;\">CARLITO B. MACADANGDANG</span><br></span><br></p></td></tr><tr><td style=\"text-align: center;\">HR Manager</td><td style=\"text-align: center;\">Vice President</td></tr><tr><td><p>Date:&nbsp;<span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black\"></p></td><td><p>Date:&nbsp;<span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black\"></p></td></tr></tbody></table><div style=\"text-align: left;\"><span><br></span></div><div style=\"text-align: left;\"><span><br></span></div><table class=\"table table-bordered\"><tbody><tr><td><p>Approved by:&nbsp;</p><p><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span>&nbsp;</span></p><p></p><div style=\"text-align: left;\"><span style=\"font-weight: bold; background-color: transparent;\">JONATHAN P. SO</span></div><br><p></p></td><td><p>Conforme:&nbsp;</p><p><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span>&nbsp;</span></p><p></p><div style=\"text-align: left;\"><span class=\"first_name\" style=\"background-color: transparent;\"><b>First Name</b></span><span style=\"background-color: transparent;\">&nbsp;&nbsp;</span><span class=\"middle_name\" style=\"background-color: transparent;\"><b>Middle Name</b></span><span style=\"background-color: transparent;\">&nbsp;&nbsp;</span><span class=\"last_name\" style=\"background-color: transparent;\"><b>Last Name</b></span><span style=\"background-color: transparent;\">&nbsp;</span></div><br><p></p></td></tr><tr><td style=\"text-align: center;\">President</td><td style=\"text-align: center;\">Employee</td></tr><tr><td><p>Date:<span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black\"></p></td><td><p>Date:&nbsp;<span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black\"></p></td></tr></tbody></table><div style=\"text-align: left;\">CC: ACCOUNTING&nbsp;</div><div style=\"text-align: left;\"><br></div><div style=\"text-align: left;\"><div style=\"text-align: justify;\"><span style=\"font-weight: bold;\">NOTE TO EMPLOYEE:</span> This Contract addendum form part is an integral part of your contract of employment with the company. If you are agreeable to the foregoing terms and conditions, please sign in the space provided as an indication of your conformity and this shall serve as contract between you and the Company.</div><span style=\"font-weight: bold; text-decoration-line: underline;\"><br></span></div>', '2019-11-28 05:50:01', '0000-00-00 00:00:00', 1),
      (12, 'Addendum', '<p class=\"MsoNormal\" align=\"center\" style=\"text-align:center\"><span style=\"font-family: Helvetica;\"><b><u><span style=\"font-size: 15pt;\">ADDENDUM TO EMPLOYMENT AGREEMENT</span></u></b></span></p><p class=\"MsoNormal\" align=\"center\" style=\"text-align:center\"><span style=\"font-family: Helvetica;\"><b><u><span style=\"font-size: 15pt;\"><br></span></u></b><u><span style=\"font-size: 15pt;\"><o:p></o:p></span></u></span></p>\r\n\r\n<p class=\"MsoNormal\" align=\"center\" style=\"text-align:center\"><o:p style=\"font-family: Helvetica;\">&nbsp;</o:p><span style=\"font-family: Helvetica;\">&nbsp;</span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\">This will confirm the understanding you had with\r\nmanagement to the effect that you are employed by Cloud Panda PH Inc. as Software\r\nDeveloper. The terms and conditions of this agreement are as follows:</span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\"><br></span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\"><br></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\">In case of voluntary resignation, it is your\r\nobligation to inform the Company of your resignation (30) thirty days prior to\r\nthe effectively of such resignation to give the Company ample time to audit\r\naccountabilities, process clearances, look and train for replacement, and\r\neffect proper turnover of duties and responsibilities. In case you failed to\r\nfinish the said effectively period, you are authorizing the Company for\r\nautomatic deduction of your un-served days from your last pay.</span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\"><br></span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\"><br></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\">In addition to the said standards, schedule of\r\nrelease of last pay is a month after the completion of clearance.</span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\"><br></span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\"><br></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\">Please signify your conformity hereto by signing\r\non the space provided herein below.</span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span style=\"font-family: Helvetica;\"><br></span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span>&nbsp;</span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span><br></span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><span>&nbsp;</span><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span><br></span><span class=\"first_name\"><b>First Name</b></span>&nbsp;&nbsp;<span class=\"middle_name\"><b>Middle Name</b></span>&nbsp;<span class=\"last_name\"><b>Last Name</b></span>&nbsp;</p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span><span style=\"font-family: Helvetica;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Employee</span></span></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><br></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black\"><br></p><p class=\"MsoNormal\" style=\"text-align: justify; margin-left: 25px;\"><span><span style=\"font-family: Helvetica;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Date<br></span><br></span><span style=\"font-family: Helvetica;\"><br></span><span style=\"font-family: Arial, sans-serif;\"><o:p></o:p></span></p>\r\n              ', '2019-11-28 05:49:12', '0000-00-00 00:00:00', 1)");

    // hris_contract_template 2
    $this->app_db->query("INSERT INTO `hris_contract_template` (`id`, `template_name`, `template_format`, `created_at`, `updated_at`, `enabled`) VALUES
      (13, 'Employee Non-Disclosure Agreement', '<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:.15pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:.15pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;\r\nmso-fareast-font-family:&quot;Times New Roman&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:.45pt;margin-bottom:0in;\r\nmargin-left:0in;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" align=\"center\" style=\"margin-top:0in;margin-right:3.1pt;\r\nmargin-bottom:0in;margin-left:0in;margin-bottom:.0001pt;text-align:center;\r\ntext-indent:0in;line-height:normal\"><b><u><span lang=\"EN-PH\" style=\"font-size:15.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">EMPLOYEE’S NON-DISCLOSURE AGREEMENT</span></u></b><span lang=\"EN-PH\" style=\"font-size:15.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:.75pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:.75pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:-.25pt;margin-bottom:.0001pt;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">FOR\r\nGOOD CONSIDERATION, as an employee of <b>CLOUD\r\nPANDA, INC.</b>, the undersigned employee hereby agrees and acknowledges:<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:-.25pt;margin-bottom:.0001pt;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoListParagraphCxSpFirst\" style=\"margin-top:0in;margin-right:2.45pt;\r\nmargin-bottom:0in;margin-left:.25in;margin-bottom:.0001pt;mso-add-space:auto;\r\ntext-indent:-18.75pt;line-height:normal;mso-list:l3 level1 lfo2\"><!--[if !supportLists]--><b><span lang=\"EN-PH\" style=\"mso-bidi-font-size:\r\n12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;mso-fareast-font-family:Arial\">I.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r\n</span></span></b><!--[endif]--><b><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">CONFIDENTIALITY\r\nOF INFORMATION<o:p></o:p></span></b></p>\r\n\r\n<p class=\"MsoListParagraphCxSpMiddle\" style=\"margin-top:0in;margin-right:2.45pt;\r\nmargin-bottom:0in;margin-left:.5in;margin-bottom:.0001pt;mso-add-space:auto;\r\ntext-indent:-.25in;line-height:normal;mso-list:l1 level1 lfo3\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:Symbol;mso-fareast-font-family:\r\nSymbol;mso-bidi-font-family:Symbol\">·<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">In\r\nthe course of the employment with the Company, the Employee acknowledges that\r\nhe may receive confidential Information from the Employer.&nbsp; \"Information\" as used herein means\r\nall information, written or oral, furnished by the Employer to the Employee, or\r\nacquired by the Employee during the course of his employment with the Employer\r\n(whether such information is prepared by or obtained from the Employer itself,\r\nits clients, partners, advisors or otherwise), the production and manufacturing\r\ntechnology, together with business plans, financial statements, analyses, compilations,\r\nstudies, or technical Information consisting of methods, processes, formulae,\r\ncompositions, systems, techniques, inventions, machines, computer programs and\r\nresearch projects, or business Information consisting of customer lists,\r\npricing data, sources of supply, financial data and marketing, production, or\r\nmerchandising systems or plans, or other documents prepared by and/or received\r\nfrom the Employer its partners, agents, employees or representatives (including\r\nwithout limitation attorneys, accountants, analysts and financial advisors)\r\nwhich contain or otherwise reflect such information.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoListParagraphCxSpMiddle\" style=\"margin-top:0in;margin-right:2.45pt;\r\nmargin-bottom:0in;margin-left:.5in;margin-bottom:.0001pt;mso-add-space:auto;\r\ntext-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:\r\n12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoListParagraphCxSpLast\" style=\"margin-top:0in;margin-right:2.45pt;\r\nmargin-bottom:0in;margin-left:.5in;margin-bottom:.0001pt;mso-add-space:auto;\r\ntext-indent:-.25in;line-height:normal;mso-list:l1 level1 lfo3\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:Symbol;mso-fareast-font-family:\r\nSymbol;mso-bidi-font-family:Symbol\">·<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The\r\nterm \"Information\" does not include any Information which (i) at the\r\ntime of disclosure or thereafter is generally available to and known by the\r\npublic (other than as a result of a disclosure directly or indirectly by the\r\nEmployer or its representatives), or (ii) is or becomes known to one of the\r\nEmployee on a non-confidential basis from a source other than the other\r\nEmployer or its advisors, provided that such source is not and was not either\r\nbound by a confidentiality agreement with either of the Employer or the\r\nEmployee or otherwise prohibited from transmitting such Information to a Party\r\nby a contractual, legal or fiduciary obligation. <o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin:0in;margin-bottom:.0001pt;text-indent:.05in;\r\nline-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoListParagraph\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.5in;margin-bottom:.0001pt;mso-add-space:auto;text-indent:-.25in;\r\nline-height:normal;mso-list:l1 level1 lfo3\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:Symbol;mso-fareast-font-family:\r\nSymbol;mso-bidi-font-family:Symbol\">·<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The\r\nEmployee agrees that it shall not, at any time during or following the\r\ntermination of his employment, directly or indirectly, divulge or disclose to\r\nany third party, for any purpose whatsoever, any of such confidential\r\nInformation which has been obtained by or disclosed to it as a result of its\r\nemployment with the Employer.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin:0in;margin-bottom:.0001pt;text-indent:.05in;\r\nline-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoListParagraph\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.5in;margin-bottom:.0001pt;mso-add-space:auto;text-indent:-.25in;\r\nline-height:normal;mso-list:l1 level1 lfo3\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:Symbol;mso-fareast-font-family:\r\nSymbol;mso-bidi-font-family:Symbol\">·<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The\r\nEmployer agrees that any and all Information furnished to it by the Employer\r\nwill not be reproduced or disclosed to any person not a representative, agent\r\nor employee of the Employer without the expressed written approval of the\r\nEmployer.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin:0in;margin-bottom:.0001pt;text-indent:0in;\r\nline-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin:0in;margin-bottom:.0001pt;text-indent:0in;\r\nline-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin:0in;margin-bottom:.0001pt;text-indent:0in;\r\nline-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.25in;margin-bottom:.0001pt;text-indent:-18.75pt;line-height:\r\nnormal;mso-list:l3 level1 lfo2\"><!--[if !supportLists]--><b><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;\r\nmso-fareast-font-family:Arial\">II.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang=\"EN-PH\" style=\"mso-bidi-font-size:\r\n12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">ACKNOWLEDGEMENT OF CONFIDENTIAL\r\nEMPLOYMENT<o:p></o:p></span></b></p>\r\n\r\n<p class=\"MsoListParagraph\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.5in;margin-bottom:.0001pt;mso-add-space:auto;text-indent:-.25in;\r\nline-height:normal;mso-list:l2 level1 lfo4\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:Symbol;mso-fareast-font-family:\r\nSymbol;mso-bidi-font-family:Symbol\">·<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">I\r\nacknowledge that I am occupying a confidential position, the Management expects\r\nme to protect to the highest level the Company’s trade secrets. I acknowledged\r\nthat I must refrained from joining directly or indirectly any company, which\r\nare engaged in the same or similar line of business as that of Cloud Panda,\r\nInc. within three (3) years from the date of my separation from the Company.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.5pt;margin-bottom:.0001pt;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.5pt;margin-bottom:.0001pt;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.25in;margin-bottom:.0001pt;text-indent:-.25in;line-height:\r\nnormal;mso-list:l3 level1 lfo2\"><!--[if !supportLists]--><b><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;\r\nmso-fareast-font-family:Arial\">III.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp; </span></span></b><!--[endif]--><b><span lang=\"EN-PH\" style=\"mso-bidi-font-size:\r\n12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">OBLIGATIONS UPON TERMINATION OF\r\nEMPLOYMENT<o:p></o:p></span></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:35.25pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Upon\r\nthe termination of my employment from the Company:<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:35.25pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:35.75pt;margin-bottom:.0001pt;text-indent:-17.75pt;line-height:\r\nnormal;mso-list:l0 level2 lfo1\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;mso-fareast-font-family:\r\nArial\">(a)<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;\r\n</span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">I shall return to the Company all documents,\r\nrecords and property belonging to the Company, including but not necessarily\r\nlimited to: drawings, blueprints, reports, manuals, correspondence, customer\r\nlists, computer programs, and all other materials and all copies thereof\r\nrelating in any way to the Company\'s business, or in any way obtained by me\r\nduring the course of employ. I further agree that I shall not retain any copies,\r\nnotes or abstracts of the foregoing.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:35.75pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:35.75pt;margin-bottom:.0001pt;text-indent:-17.75pt;line-height:\r\nnormal;mso-list:l0 level2 lfo1\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;mso-fareast-font-family:\r\nArial\">(b)<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;\r\n</span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The Company may notify any future or\r\nprospective employer or third party of the existence of this agreement and\r\nundertaking, and shall be entitled to full injunctive relief for any breach\r\nthereof.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:0in;margin-bottom:.0001pt;text-indent:0in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:35.75pt;margin-bottom:.0001pt;text-indent:-17.75pt;line-height:\r\nnormal;mso-list:l0 level2 lfo1\"><!--[if !supportLists]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;mso-fareast-font-family:\r\nArial\">(c)<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;\r\n</span></span><!--[endif]--><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">This agreement shall be binding upon me and\r\nmy personal representatives and successors in interest, and shall inure to the\r\nbenefit of the Company, its successors and assigns.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:0in;text-indent:0in\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;line-height:103%;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:0in;text-indent:0in\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;line-height:103%;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:.5pt;margin-bottom:.0001pt;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:-.75pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal;\r\ntab-stops:52.8pt center 71.8pt 125.95pt 2.5in 224.05pt 320.55pt right 454.15pt\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Signed\r\nthis&nbsp;</span><span>&nbsp;</span><input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><span class=\"input-text-container\"></span><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">day of&nbsp; </span><input type=\"text\" class=\"form-control input-text\" style=\"width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span>&nbsp;</span><span class=\"input-text-container\"></span><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">,\r\n<span style=\"background:aqua;mso-highlight:aqua\">2019</span>.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:-.75pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal;\r\ntab-stops:center 71.8pt 125.95pt 2.5in 224.05pt 320.55pt right 454.15pt\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:-.75pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal;\r\ntab-stops:center 71.8pt 125.95pt 2.5in 224.05pt 320.55pt right 454.15pt\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;\r\nmargin-left:-.75pt;margin-bottom:.0001pt;text-indent:0in;line-height:normal;\r\ntab-stops:center 71.8pt 125.95pt 2.5in 224.05pt 320.55pt right 454.15pt\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-top:0in;margin-right:2.45pt;margin-bottom:\r\n0in;margin-left:-.25pt;margin-bottom:.0001pt;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Conforme:<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin:0in;margin-bottom:.0001pt;text-indent:0in;\r\nline-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin:0in;margin-bottom:.0001pt;text-indent:0in;\r\nline-height:normal\"><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><span>&nbsp;</span><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:12.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p><br></o:p></span></p>\r\n\r\n<p class=\"MsoNormalCxSpLast\" style=\"margin-bottom:0in;margin-bottom:.0001pt;\r\nmso-add-space:auto;line-height:normal\"><span>&nbsp;</span><span class=\"first_name\" style=\"text-align: justify;\"><b>First Name</b></span><span style=\"text-align: justify;\">&nbsp;</span><span class=\"middle_name\" style=\"text-align: justify;\"><b>Middle Name</b></span><span style=\"text-align: justify;\">&nbsp;</span><span class=\"last_name\" style=\"text-align: justify;\"><b>Last Name</b></span><span style=\"text-align: justify;\">&nbsp;</span></p>\r\n\r\n<p class=\"MsoNormalCxSpFirst\" style=\"margin:0in;margin-bottom:.0001pt;mso-add-space:\r\nauto;text-indent:.5in;line-height:normal\"><span lang=\"EN-PH\" style=\"mso-bidi-font-size:\r\n12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Employee<o:p></o:p></span></p>\r\n              ', '2019-11-28 09:39:48', '0000-00-00 00:00:00', 1),
      (14, 'Training Agreement', '<p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p><p class=\"MsoNormal\" align=\"center\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:center;line-height:normal\"><b><span lang=\"EN-PH\" style=\"font-size:15.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">CONTRACTOF TRAINING<o:p></o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span class=\"date_today\"><b>Date Today</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt\"><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify\"><span class=\"home_address1\"><b>Home Address 1</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify\"><span class=\"home_address2\"><b>Home Address 2</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify\"><span lang=\"EN-PH\" style=\"font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;color:red\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Dear Mr.&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><span lang=\"EN-PH\" style=\"\"><span style=\"font-family: Arial, sans-serif; font-size: 12pt;\">,<o:p></o:p></span></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">CloudPanda PH Inc.</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"> agrees to train you for the position of </span><span class=\"position\"><b>Position</b></span><span>&nbsp;</span><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">under the following terms and conditions:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Positionand Department</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">You shall be trainedfor the position of&nbsp;</span><span class=\"position\"><b>Position</b></span><span>&nbsp;.</span><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">EffectivityDate and Duration</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The Training shalltake effect on&nbsp;</span><span class=\"contract_start\"><b>Contract Start</b></span><span>&nbsp;</span><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"> and shall end on </span><span class=\"contract_end\"><b>Contract End</b></span><span>&nbsp;</span><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">,subject to extension of not exceeding 3 months, at the discretion of theCompany.<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Salary/Wage</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">You shall receiveyour salary in a semi-monthly no-work-no-pay basis based on the following:<o:p></o:p></span></p><p class=\"MsoListParagraphCxSpFirst\" style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;margin-left:1.5in;margin-bottom:.0001pt;mso-add-space:auto;text-align:justify;text-indent:-.25in;line-height:normal;mso-list:l0 level1 lfo1\"><span class=\"sal_cat\"><b>Salary</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">TrainingHours:</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p></o:p></span></p><p class=\"MsoNoSpacingCxSpFirst\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The Training scheduleis a&nbsp;<b>5 days a week&nbsp;</b>from <b>Mondays to Friday.</b>You are requestedto render to work in 5 days week and <b>atleasteight (8) hours of work </b>on a<b>flexible Time In </b>between <b>08:00 AMuntil 11:00 AM only </b>(log In time or start of shift) to 8pm only(log outtime).Working time schedule will be based on the time in log and should becomplete the 9 hours a day before time log out. For one (1) hour consideredunpaid break time. You agree to render extended work hours when so required bythe Employer.<o:p></o:p></span></p><p class=\"MsoNoSpacingCxSpMiddle\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNoSpacingCxSpMiddle\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNoSpacingCxSpMiddle\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNoSpacingCxSpLast\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Rulesand Regulations</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">It shall be yourobligation to strictly comply with and observe all rules and regulations of theCompany, which you acknowledge to have been made known to you. You are awarethat corresponding disciplinary action will be imposed for each infractions orviolations the said rules and regulations.<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Uniform:<o:p></o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">During the TrainingPeriod, the Trainee may be required to wear Company uniform, in which case, youagree that the requirement of wearing such uniform shall not be construed, inany manner, that you have become an employee of the Company.<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">NoEmployment Guarantee:</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The completion of theTraining Course shall not automatically entitle the Trainee the right to beemployed by the Company. The Company reserves the right to choose whom to hireamong the qualified Trainees taking into consideration the result of theoverall performance during the Training period.<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Terminationof Training Agreement:</span></b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">The Training periodmay be terminated at any time for any causes, whatsoever, including but notlimited to, inability to learn and undertake the duties of the position you arebeing trained for, inefficiency, recession of business, personnel reductionand/or violation of Company Rules and Regulations. In any event, you shall begiven notice of termination at any time, during any working day, in which case,the Training Period shall be terminated at the close of the working hoursstated in the Notice of Termination. The Company undertakes in that event topay your Training Allowance for the days when the Trainee actually reported forTraining. At no instance will the Trainee be entitled to the payment of any amountother than the Training Allowance for the day the Trainee actually reported forTraining.<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">If you are agreeableto the above terms and conditions, please signify your acceptance by signing atthe bottom hereof.<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Respectfully,<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p><br></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal;tab-stops:353.25pt\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span>&nbsp;</span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><b><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">&nbsp; &nbsp;AIDA G. PADILLA</span></b></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HR Manager<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Conforme:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><span>&nbsp;</span><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">&nbsp;</span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span><br></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span><br></span></p><p class=\"MsoNormal\" style=\"margin-bottom:0in;margin-bottom:.0001pt;text-align:justify;line-height:normal\"><span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span><br></span><span lang=\"EN-PH\" style=\"font-size:12.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Date</span></p>', '2019-12-20 10:10:52', '0000-00-00 00:00:00', 1)");

    // hris_contract_template 3
    $this->app_db->query("INSERT INTO `hris_contract_template` (`id`, `template_name`, `template_format`, `created_at`, `updated_at`, `enabled`) VALUES
      (15, 'Regular Agreement', '<p class=\"MsoNormal\"><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span class=\"home_address1\"><b>Home Address 1</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span class=\"home_address2\"><b>Home Address 2</b></span><span>&nbsp;</span><br></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Dear Mr. </span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">,<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nmso-bidi-font-family:Arial\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\"><b><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">CONGRATULATIONS!<o:p></o:p></span></b></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">We found that you have satisfactorily met the\r\nstandard of the Company and have qualified to be a regular employee of our\r\norganization.&nbsp; <o:p></o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">We are therefore, extending this <b>Regular\r\nAppointment</b> to you under the following terms and conditions:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><table class=\"MsoNormalTable\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"730\" style=\"width: 438pt; margin-left: 35.4pt;\">\r\n <tbody><tr>\r\n  <td width=\"290\" valign=\"top\" style=\"width:174.0pt;padding:0in 5.4pt 0in 5.4pt\">\r\n  <h4><span style=\"font-size:11.0pt;mso-bidi-font-family:Arial;text-transform:\r\n  uppercase\">Position:</span><span style=\"font-size:11.0pt;mso-bidi-font-size:\r\n  12.0pt;mso-bidi-font-family:Arial;text-transform:uppercase\"><o:p></o:p></span></h4>\r\n  </td>\r\n  <td width=\"440\" valign=\"top\" style=\"width:264.0pt;padding:0in 5.4pt 0in 5.4pt\">\r\n  <h1 style=\"text-align:justify\"><span class=\"position\"><b>Position</b></span><span>&nbsp;</span><br></h1>\r\n  </td>\r\n </tr>\r\n <tr>\r\n  <td width=\"290\" valign=\"top\" style=\"width:174.0pt;padding:0in 5.4pt 0in 5.4pt\">\r\n  <p class=\"MsoNormal\" style=\"text-align:justify\"><b><span style=\"font-size:11.0pt;\r\n  font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;text-transform:uppercase\">Department/Section:</span></b><b><span style=\"font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;text-transform:uppercase\"><o:p></o:p></span></b></p>\r\n  </td>\r\n  <td width=\"440\" valign=\"top\" style=\"width:264.0pt;padding:0in 5.4pt 0in 5.4pt\">\r\n  <p class=\"MsoNormal\" style=\"text-align:justify\"><span class=\"department\"><b>Department</b></span><span>&nbsp;</span><br></p>\r\n  </td>\r\n </tr>\r\n <tr>\r\n  <td width=\"290\" valign=\"top\" style=\"width:174.0pt;padding:0in 5.4pt 0in 5.4pt\">\r\n  <p class=\"MsoNormal\" style=\"text-align:justify\"><b><span style=\"font-size:11.0pt;\r\n  font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;text-transform:uppercase\">Effectivity:</span></b><b><span style=\"font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;text-transform:uppercase\"><o:p></o:p></span></b></p>\r\n  </td>\r\n  <td width=\"440\" valign=\"top\" style=\"width:264.0pt;padding:0in 5.4pt 0in 5.4pt\">\r\n  <p class=\"MsoNormal\" style=\"text-align:justify\"><span class=\"contract_start\"><b>Contract Start</b></span><span>&nbsp;</span><br></p>\r\n  </td>\r\n </tr>\r\n</tbody></table><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">You shall be entitled to fringe benefits as\r\ngranted by this Company to all regular employees in accordance with the Company\r\nPolicies and Procedures, such as:<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">You\r\nhave to obligation to strictly comply with all Company Rules and\r\nRegulations.&nbsp; You understand that\r\ncorresponding disciplinary action will be imposed for any infractions or\r\nviolations thereof.<o:p></o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Moreover,\r\nsince you are occupying a confidential position, the Company reminds you that\r\nyou are expected to protect to the highest level the Company’s trade secrets,\r\nwhich includes but not limited to its formulas, procedures, and technology of\r\nproduction. Please be advised as well that you are restrained from joining as\r\nemployee, officer, director, stockholder or agent, directly or indirectly, with\r\nany company, which are engaged in the same or similar line of business as that\r\nof Cloud&nbsp; Panda PH Inc. within one (1)\r\nyear from the date your separation from the Company.&nbsp; For a more detailed statement and description\r\nof this obligation, please refer to the attached Non-Disclosure Agreement for\r\nEmployees, which you are required to execute after reading and understanding\r\nthe same.&nbsp;&nbsp;&nbsp; <o:p></o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">For\r\nfurther exposure on your line of duty, and for the enhancement of your skills\r\nand abilities, the Company, as it deems fit, shall have the prerogative to\r\nassign and transfer you to any of its section, departments and work sites\r\nduring the period of your employment.<o:p></o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\"><span style=\"font-size:11.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">May\r\nyou continue to give your best contribution to the continuing growth of the\r\nCompany and its Human Resources requirements by complying all the policy and\r\nprocedure.<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Again, congratulations and we welcome you to\r\nthe growing family of CLOUD PANDA PH INC.,<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Very truly yours,</span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><br></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><br></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><span>&nbsp;</span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: bold;\">MR. JONATHAN P. SO</span></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: bold;\">CEO/ PRESIDENT<br></span></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: bold;\"><br></span></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><span>&nbsp;</span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: 700;\">MR.&nbsp;</span></span><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><span><br></span><img class=\"signature-pad-img\" src=\"\" alt=\"\">Conforme:&nbsp;<span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: bold;\"><br></span></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><span>&nbsp;</span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: bold;\">MR. CARLITO B. MACADANGDANG&nbsp;</span></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: bold;\">CFO/ VICE PRESIDENT</span></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span>Attested by:&nbsp;</span><span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><span><br><br></span><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span><span style=\"font-weight: bold;\"><br></span></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><span style=\"font-weight: bold;\"><br></span><br></span><img class=\"signature-pad-img\" src=\"\" alt=\"\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><br></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:11.0pt;\r\nfont-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><br></span></p>\r\n              ', '2019-12-20 10:03:46', '0000-00-00 00:00:00', 1),
      (16, 'Test', 'Tests', '2019-12-18 03:00:49', '2019-12-18 03:00:49', 0),
      (17, 'Probitionary Agreement', '<p class=\"MsoTitle\" align=\"left\"><span style=\"font-size:\r\n12.0pt\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoTitle\" style=\"text-align: center; \"><span style=\"font-size:12.0pt\">EMPLOYMENT AGREEMENT</span>&nbsp;</p>\r\n\r\n<p class=\"MsoTitle\" style=\"text-align: center;\"><span style=\"font-size: 12pt;\">TERMS AND CONDITION<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\"><b><span style=\"font-size:10.5pt\"><o:p>&nbsp;</o:p></span></b></p>\r\n\r\n<p class=\"MsoNoSpacing\" style=\"text-align:justify\"><b><span lang=\"EN-PH\" style=\"font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></b></p>\r\n\r\n<p class=\"MsoNormal\"><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><br></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span class=\"home_address1\"><b>Home Address 1</b></span><span>&nbsp;</span><br></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span class=\"home_address2\"><b>Home Address 2</b></span><span>&nbsp;</span><br></p>\r\n\r\n<p class=\"MsoNoSpacing\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNoSpacing\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\"><o:p>&nbsp;</o:p></span></p>\r\n\r\n<p class=\"MsoNoSpacing\" style=\"text-align:justify\"><span lang=\"EN-PH\" style=\"font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">Dear Mr. </span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span><span lang=\"EN-PH\" style=\"font-family:&quot;Arial&quot;,&quot;sans-serif&quot;\">,</span>&nbsp;</p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b>WELCOME\r\nTO CLOUD PANDA INC</b>.<span style=\"text-transform:uppercase\">!</span><o:p></o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">We are pleased to confirm your <b>Probationary employment</b> with Cloud\r\nPanda Inc. with a designation of&nbsp;<span class=\"position\"><b>Position</b></span><span>&nbsp;</span>.&nbsp;&nbsp; Your employment shall be\r\nsubject to the following terms and conditions:<b><o:p></o:p></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"1\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">Your probationary employment is commencing on&nbsp;<span class=\"contract_start\"><b>Contract Start</b></span><span>&nbsp; to&nbsp;</span><span class=\"contract_end\"><b>Contract End</b></span><span>&nbsp;</span><b>.</b>During this period, the Employer shall determine\r\n     your qualification and suitability for the job as&nbsp;<span class=\"position\"><b>Position</b></span><span>&nbsp;.</span><o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:12.0pt;text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"2\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You will be directly reporting to Mr. Paul Vincent Chua, our\r\n     Development and Operations Manager from Mondays to Saturdays. You\r\n     requested to render to work in 5 days per week and at least eight (8) hours\r\n     of work on a flexible time starting 8:00 am to 11:00 am (log In time or\r\n     start of shift) to 8pm only(log out time). You agree to render extended\r\n     work hours when so required by the Employer.&nbsp;&nbsp;&nbsp; <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:12.0pt;text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"3\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You acknowledge that the Employer, through its duly authorized\r\n     representative, has notified you, and that you fully understand the\r\n     Company’s reasonable standard to qualify you as regular employee.<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"4\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">On or before the expiration of the Probationary Period, the Company\r\n     shall evaluate your performance based on the Company standard that you\r\n     acknowledge to have been satisfactorily explain to you.&nbsp; The Employer shall notify you in writing\r\n     of the Job Performance Evaluation result, which shall be the basis of\r\n     whether your employment will be retained on a regular basis or terminated\r\n     for failure to meet the Company’s standard and qualify as a regular\r\n     employee.<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoListParagraph\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"5\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">In the event that your probationary employment is transform into a\r\n     regular employment, the terms and conditions contained in this Employment\r\n     Contract shall continue to apply insofar as your obligations as employee\r\n     are concerned.&nbsp; <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:24.0pt;text-align:justify;text-indent:\r\n-12.0pt;tab-stops:list 24.0pt\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"6\" type=\"1\">\r\n <li class=\"MsoNormal\">For giving\r\n     your entire time and attention to the work assigned to you, you shall\r\n     receive a salary of&nbsp;<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\"><o:p>&nbsp;</o:p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"sal_cat\"><b>Salary</b></span><span>&nbsp;</span></p>\r\n\r\n<p class=\"MsoNormal\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoListParagraph\" style=\"text-indent:-.25in;mso-list:l0 level1 lfo1;\r\ntab-stops:list .5in\"><!--[if !supportLists]-->7.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp; </span><!--[endif]-->As\r\nan employee, you are entitled to the following benefits:</p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\"><b>Mandatory Benefits:</b> The employee shall receive all the benefits\r\nrequired by the law which includes SSS, PHIC, HDMF and 13<sup>th</sup> month\r\nPay.</p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\">For SSS, PHIC &amp; HDMF, please\r\ntake note that the Employer will deduct a certain amount from your payroll as\r\npart of your employee share contribution. This deduction will be based on the\r\ngiven salary bracket from the said government agencies.</p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.25in\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\"><b>Leave Benefits:</b> Employee will be entitled to six (6) days for Sick\r\nleave and Vacation Leave with pay upon regularization. Unused leave credits are\r\nconvertible to cash at the end of the calendar year.</p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.25in\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\"><b>HMO:</b> The employee shall receive a Health Card with a maximum\r\nbenefit limit of Php 70,000 per illness per year. This will avail after 2 years\r\nof service in the company based on the employment of start date. </p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in\">Any additional payments or benefits of\r\nwhatever nature, except those mandated by law, are not demandable and rest\r\nentirely at the Employer’s discretion, and will not, even in the case of\r\nrepetition serve to establish any claim against the Employer.<o:p></o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:24.0pt;text-align:justify;text-indent:\r\n-12.0pt;tab-stops:list 24.0pt\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"8\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">Your salary now and in the future is strictly personal and\r\n     confidential between you and the Employer. Hence, you are hereby enjoin\r\n     from discussing this matter, under any circumstances, with anyone except\r\n     with appropriate members of Management and by appropriate procedures.<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"9\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You agree to abide by all Company’s Rules and Regulations.&nbsp; It shall be your duty to study and\r\n     understand such Company rules and regulations.&nbsp; Your employment with the Company is\r\n     exclusive and you agree not be engage, during your employment with the\r\n     Company, in any work with other employer/s whether within or outside your\r\n     working hours with the Company. <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"10\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You agree to give immediate notice to the Company of any claim that\r\n     you believe you are entitled to against the Company.&nbsp; Any claim not demanded within a period\r\n     of the 24 hours from the occurrence of such claim is deem waived and\r\n     abandoned.<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:.5in;text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"11\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">The Company shall not be responsible for any expenses that you may\r\n     incur in the execution of the duties of your position unless the Employer,\r\n     through its duly authorized representative, authorized the same prior to\r\n     the expenditure of such expenses.&nbsp; <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:24.0pt;text-align:justify;text-indent:\r\n-12.0pt;tab-stops:list 24.0pt\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"12\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You agree that all records and documents that belong to the Company\r\n     and all information pertaining to its trade, business or affairs are\r\n     strictly of confidential nature and that you will make no authorized\r\n     disclosure or reproduction of the same at any time during or after your\r\n     employment with the Company.<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:24.0pt;text-align:justify;text-indent:\r\n-12.0pt;tab-stops:list 24.0pt\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"13\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You agree that all Company records, documents and assets in your\r\n     custody or control shall be immediately surrender to the Company if\r\n     requested during the employment period, and at the termination thereof,\r\n     whether or not requested.<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoListParagraph\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"14\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">Since you are occupying a confidential position, the Employer\r\n     expects you to protect to the highest level the Company’s trade secrets,\r\n     which includes but not limited to its formulas, procedures, and technology\r\n     of production.<o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"15\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">During the entire period of your employment (probationary or\r\n     regular), you agree to be assigned to any work or workplace for such\r\n     period as may be determined by the Employer whenever your services is\r\n     required to such new assignment. <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:12.0pt;text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"16\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">The Company reserves the right to terminate your services at any\r\n     time during the probationary period or any time thereafter should the\r\n     Company, exercising reasonable discretion, find that your job performance\r\n     is unsatisfactory based on Company standard that you acknowledge to have\r\n     been satisfactorily explain to you.&nbsp;\r\n     <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left:12.0pt;text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"17\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">In the event that your employment becomes regular, that is after\r\n     you have successfully completed the probationary period to the\r\n     satisfaction of the Employer, you agree to execute the Non-Disclosure\r\n     Agreement for Employees.&nbsp; <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"18\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">During the period of your regular employment, the Employer may\r\n     require you to undergo certain training programs to enhance your\r\n     qualifications or to prepare you to handle other/new work assignment or\r\n     responsibilities (whether for lateral transfer or promotion to a higher\r\n     level).&nbsp; In such eventuality, you\r\n     agree to undergo relevant training and execute the corresponding training\r\n     agreement which shall govern the relationship between the Employee and the\r\n     Employer and may impose additional obligations on the part of the\r\n     Employee.&nbsp;&nbsp;&nbsp; <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"19\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">During the period of your regular employment, the Employer may\r\n     terminate your employment for just or authorized cause pursuant to the\r\n     provisions of the Presidential Decree 442, otherwise known as the Labor\r\n     Code of the Philippines. <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"20\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You acknowledge that in addition to the grounds for dismissal\r\n     provided by the Labor Code of the Philippines, violation of the terms and\r\n     conditions of your employment shall be sufficient ground to terminate your\r\n     employment with the Company.&nbsp;&nbsp; <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<ol style=\"margin-top:0in\" start=\"21\" type=\"1\">\r\n <li class=\"MsoNormal\" style=\"text-align:justify;mso-list:l0 level1 lfo1;\r\n     tab-stops:list .5in\">You may terminate your employment with the Company (resignation) by\r\n     serving a written notice (resignation letter) at least 30-day prior to the\r\n     effectivity of the termination/resignation.&nbsp; During the 30-day transition period, you\r\n     agree to turnover to the Company all the documents, records, assets,\r\n     office supplies and any all Company property in your custody as employee\r\n     of the Company. Should you fail to strictly observed the requirements\r\n     under this Section 19, the Company reserves the right to: (a) withhold the\r\n     issuance of Clearance in your favor; and (b) withhold the payment of your\r\n     last salary until satisfactory compliance with this Section 20.&nbsp;&nbsp;&nbsp;&nbsp; <o:p></o:p></li>\r\n</ol>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><br></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">It is expressly agreed and understood that\r\nthere are no verbal agreements and understandings between you and the Company\r\nor any of its representatives, affecting this agreement and that no amendment\r\nof the terms hereof shall be binding upon either party to this agreement unless\r\nthe same are reduced in writing and signed by you and the Company.<o:p></o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">We welcome you in our Organization and trust\r\nthat your association with us will be mutually beneficial.<o:p></o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">Your signature below the space provided\r\nhereunder will devote your acceptance of the foregoing terms.<o:p></o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">Very truly yours,<o:p></o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b><span style=\"mso-bidi-font-size:\r\n11.0pt;mso-bidi-font-family:Arial;text-transform:uppercase\">cloud pandaPH INC.<o:p></o:p></span></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b><o:p>&nbsp;</o:p></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b><o:p>&nbsp;</o:p></b><span>&nbsp;</span><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b>MR. JONATHAN SO </b><o:p></o:p></p>\r\n\r\n<h3 style=\"margin-right:0in\"><span style=\"font-size:11.0pt;mso-bidi-font-family:\r\nArial\">President<o:p></o:p></span></h3>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><i>I hereby acknowledge receipt of the original\r\nof this letter-agreement and agree to all the terms herein<o:p></o:p></i></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><i><o:p>&nbsp;</o:p></i></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><i><o:p>&nbsp;</o:p></i></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><i><o:p>&nbsp;</o:p></i><span>&nbsp;</span><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">Date:&nbsp;<span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b><o:p>&nbsp;</o:p></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">Attested by:<b><o:p></o:p></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b><o:p>&nbsp;</o:p></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span>&nbsp;</span><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><b><o:p>&nbsp;</o:p></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><b>MR. CARLITO MACADANGDANG JR.<o:p></o:p></b></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">Vice President<o:p></o:p></p>\r\n\r\n<h5 style=\"margin-top:0in;margin-right:0in;margin-bottom:0in;margin-left:4.0in;\r\nmargin-bottom:.0001pt;text-indent:0in\"><span style=\"font-size: 11pt;\"><o:p>&nbsp;</o:p></span></h5>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\">Date:&nbsp;<span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"><o:p></o:p></p>\r\n\r\n<p class=\"MsoTitle\" align=\"left\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p class=\"MsoNoSpacing\" style=\"text-align:justify\"><span lang=\"EN-PH\">&nbsp;</span></p>\r\n              ', '2019-12-20 10:10:41', '0000-00-00 00:00:00', 1),
      (18, 'Contractual Agreement', '<p class=\"MsoTitle\" style=\"text-align: center;\"><span style=\"font-size:15.0pt\">FIXED SERVICE CONTRACT</span></p><p class=\"MsoTitle\" style=\"text-align: center;\"><span style=\"font-size: 12pt;\">TERMS AND CONDITION<o:p></o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:10.0pt\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p><span class=\"date_today\"><b>Date Today</b></span><span>&nbsp;</span></p><p class=\"MsoNormal\"><o:p>&nbsp;</o:p></p><p class=\"MsoNormal\"><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p><p class=\"MsoNormal\"><span class=\"home_address1\"><b>Home Address 1</b></span><span>&nbsp;</span></p><p class=\"MsoNormal\"><span class=\"home_address2\"><b>Home Address 2</b></span><span>&nbsp;</span><span><br></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\">Dear Mr.&nbsp;<span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;,</span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\">We are pleased to confirm your <b>Contractual Employment</b> with Cloud Panda\r\nInc. with a designation of <span class=\"position\"><b>Position</b></span><span>&nbsp;</span>. Your\r\nemployment shall be subject to the following terms and conditions:<o:p></o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p><p class=\"MsoListParagraphCxSpFirst\" style=\"margin-left:.75in;mso-add-space:auto;\r\ntext-align:justify;text-indent:-.25in;mso-list:l0 level1 lfo1\"><!--[if !supportLists]-->1.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp; </span><!--[endif]-->Your engagement will be for a fixed-term period of five (5) months\r\ncommencing on&nbsp;<span class=\"contract_start\"><b>Contract Start</b></span><span>&nbsp;</span><b>&nbsp;to&nbsp;</b><span class=\"contract_end\"><b>Contract End</b></span><span>&nbsp;</span><o:p></o:p></p><p class=\"MsoListParagraphCxSpMiddle\" style=\"margin-left:.75in;mso-add-space:\r\nauto;text-align:justify\"><span style=\"font-size:6.0pt;mso-bidi-font-size:11.0pt;\r\nmso-bidi-font-family:Arial;mso-bidi-font-weight:bold\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoListParagraphCxSpLast\" style=\"margin-left:.75in;mso-add-space:auto;\r\ntext-align:justify;text-indent:-.25in;mso-list:l0 level1 lfo1\"><!--[if !supportLists]-->2.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp; </span><!--[endif]-->You will receive the following monthly compensation in a semi-monthly,\r\n“no work, no pay” basis:<o:p></o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-size:5.0pt;\r\nmso-bidi-font-size:11.0pt;mso-bidi-font-family:Arial;mso-bidi-font-weight:bold\"><o:p>&nbsp;</o:p></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<span class=\"sal_cat\"><b>Salary</b></span><span>&nbsp;</span></p><p class=\"MsoListParagraphCxSpMiddle\"><span style=\"font-size:6.0pt;mso-bidi-font-size:\r\n11.0pt;mso-bidi-font-family:Arial;mso-bidi-font-weight:bold\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoListParagraphCxSpMiddle\" style=\"margin-left:.75in;mso-add-space:\r\nauto;text-align:justify;text-indent:-.25in;mso-list:l0 level1 lfo1\"><!--[if !supportLists]-->3.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp; </span><!--[endif]-->Your benefits and privileges shall be limited to those mandated by law.<o:p></o:p></p><p class=\"MsoListParagraphCxSpMiddle\" style=\"margin-left:.75in;mso-add-space:\r\nauto;text-align:justify;tab-stops:117.0pt\"><span style=\"font-size:6.0pt;\r\nmso-bidi-font-size:11.0pt;mso-bidi-font-family:Arial;mso-bidi-font-weight:bold\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span><span style=\"font-size:7.0pt;mso-bidi-font-size:11.0pt;mso-bidi-font-family:Arial;\r\nmso-bidi-font-weight:bold\"><o:p></o:p></span></p><p class=\"MsoListParagraphCxSpMiddle\" style=\"margin-left:.75in;mso-add-space:\r\nauto;text-align:justify;text-indent:-.25in;mso-list:l0 level1 lfo1\"><!--[if !supportLists]-->4.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp; </span><!--[endif]-->You will report to <b>Mr. Paul Vincent D. Chua</b>, <b>Development and\r\nOperations Manager</b>, who will direct and monitor the performance of your\r\nduties and responsibilities.<o:p></o:p></p><p class=\"MsoListParagraphCxSpMiddle\"><span style=\"font-size:6.0pt;mso-bidi-font-size:\r\n11.0pt;mso-bidi-font-family:Arial;mso-bidi-font-weight:bold\"><o:p>&nbsp;</o:p></span></p><p class=\"MsoListParagraphCxSpMiddle\" style=\"margin-left:.75in;mso-add-space:\r\nauto;text-align:justify;text-indent:-.25in;mso-list:l0 level1 lfo1\"><!--[if !supportLists]-->5.<span style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;\">&nbsp;&nbsp;&nbsp;&nbsp; </span><!--[endif]-->Your service will be dispensed upon the expiration of this contract, on <span class=\"contract_end\"><b>Contract End</b></span><span>&nbsp;</span>without a need of prior\r\nnotice. You understand that your services may be terminated even before the end\r\nof the said period at the discretion of the company.<o:p></o:p></p><p class=\"MsoListParagraphCxSpLast\"><o:p>&nbsp;</o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\">Please indicate\r\nyour understanding and acceptance to the foregoing terms and conditions by\r\nsigning your name on the space provided below.<o:p></o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\"><o:p>&nbsp;</o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span>&nbsp;</span><canvas class=\"signature-pad\" width=\"300\" height=\"100\" style=\"touch-action: none;\"></canvas><o:p>&nbsp;</o:p></p><p class=\"MsoNormal\" style=\"text-align:justify\"><b>CARLITO B. MACADANGDANG<o:p></o:p></b></p><p class=\"MsoNormal\" style=\"text-align:justify\"><i><span style=\"font-size:10.0pt;mso-bidi-font-size:11.0pt;mso-bidi-font-family:\r\nArial;mso-bidi-font-weight:bold\">Vice President, Cloud Panda PH, Inc.</span></i></p><p class=\"MsoNormal\" style=\"text-align:justify\"><i><span style=\"font-size:10.0pt;mso-bidi-font-size:11.0pt;mso-bidi-font-family:\r\nArial;mso-bidi-font-weight:bold\"><o:p><br></o:p></span></i></p><p class=\"MsoNormal\" style=\"text-align:justify\"><i><span style=\"font-size:10.0pt;mso-bidi-font-size:11.0pt;mso-bidi-font-family:\r\nArial;mso-bidi-font-weight:bold\"><o:p>Conforme:</o:p></span></i></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span>&nbsp;</span><canvas class=\"signature-pad\" width=\"300\" height=\"100\"></canvas><i><span style=\"font-size:10.0pt;mso-bidi-font-size:11.0pt;mso-bidi-font-family:\r\nArial;mso-bidi-font-weight:bold\"><o:p><br></o:p></span></i></p><p class=\"MsoNormal\" style=\"text-align:justify\"><b><o:p>&nbsp;</o:p></b><span class=\"first_name\"><b>First Name</b></span><span>&nbsp;</span><span class=\"middle_name\"><b>Middle Name</b></span><span>&nbsp;</span><span class=\"last_name\"><b>Last Name</b></span><span>&nbsp;</span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span>&nbsp;Employee</span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span><br></span></p><p class=\"MsoNormal\" style=\"text-align:justify\"><span>Date:&nbsp;</span><span>&nbsp;</span><input type=\"text\" class=\"form-control date_input_empty\" style=\"width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;\"></p>\r\n              ', '2019-12-20 10:16:15', '0000-00-00 00:00:00', 1)");
    return true;

  }

}
