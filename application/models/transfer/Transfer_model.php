<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer_model extends CI_Model {
  public $app_db;

  public function get_data_from_database($dbname,$search){
    $this->app_db = switch_database($dbname);

    $requestData = $_REQUEST;

    $columns = array(
      0 => 'select',
      1 => 'fullname',
      2 => '',
      3 => '',
      4 => ''
    );


    $sql = "SELECT a.id as emp_id, a.employee_idno,
     (SELECT GROUP_CONCAT(employee_idno SEPARATOR ',') FROM employee_record WHERE enabled = 1) as ids,
     CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname
     FROM employee_record a WHERE enabled = 1";

     switch ($search->filter) {
       case 'divBname':
         $branch_name = $this->db->escape("%".$search->search."%");
         $sql .= " AND (branch_name LIKE $branch_name)";
         break;
       default:
         break;
     }

    $query = $this->app_db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->app_db->query($sql);
    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $checkbox1 =
      '
        <label class="container_label">
          <input type="checkbox" name = "select[]" class = "select" value = "'.en_dec('en',$row['employee_idno']).'">
          <span class="checkmark"></span>
        </label>
      ';
      $checkbox2 =
      '
        <label class="container_label">
          <input type="checkbox" name = "emp_record[]" class = "emp_record" value = "'.en_dec('en',$row['employee_idno']).'">
          <span class="checkmark"></span>
        </label>
      ';
      $checkbox3 =
      '
        <label class="container_label">
          <input type="checkbox" name = "contract_record[]" class = "contract_record" value = "'.en_dec('en',$row['employee_idno']).'">
          <span class="checkmark"></span>
        </label>
      ';
      $checkbox4 =
      '
        <label class="container_label">
          <input type="checkbox" name = "time_record[]" class = "time_record" value = "'.en_dec('en',$row['employee_idno']).'">
          <span class="checkmark"></span>
        </label>
      ';

      $nestedData[] = $checkbox1;
      $nestedData[] = $row['fullname'];
      $nestedData[] = $checkbox2;
      $nestedData[] = $checkbox3;
      $nestedData[] = $checkbox4;

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_data_from_database_applicant($dbname,$search){
    $this->app_db = switch_database($dbname);

    $requestData = $_REQUEST;

    $columns = array(
      0 => 'select',
      1 => 'fullname',
      2 => '',
      3 => '',
      4 => ''
    );


    $sql = "SELECT a.id as app_id, a.app_ref_no,
     (SELECT GROUP_CONCAT(id SEPARATOR ',') FROM applicant_record WHERE app_enabled = 1) as ids,
     CONCAT(a.app_lname,', ',a.app_fname,' ',a.app_mname) as fullname
     FROM applicant_record a WHERE app_enabled = 1";

     switch ($search->filter) {
       case 'divBname':
         $branch_name = $this->db->escape("%".$search->search."%");
         $sql .= " AND (branch_name LIKE $branch_name)";
         break;
       default:
         break;
     }

    $query = $this->app_db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->app_db->query($sql);
    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $checkbox1 =
      '
        <label class="container_label">
          <input type="checkbox" name = "select[]" class = "select" value = "'.en_dec('en',$row['app_ref_no']).'">
          <span class="checkmark"></span>
        </label>
      ';
      $checkbox2 =
      '
        <label class="container_label">
          <input type="checkbox" name = "app_record[]" class = "emp_record" value = "'.en_dec('en',$row['app_ref_no']).'">
          <span class="checkmark"></span>
        </label>
      ';
      $checkbox3 =
      '
        <label class="container_label">
          <input type="checkbox" name = "contract_record[]" class = "contract_record" value = "'.en_dec('en',$row['app_ref_no']).'">
          <span class="checkmark"></span>
        </label>
      ';
      $checkbox4 =
      '
        <label class="container_label">
          <input type="checkbox" name = "time_record[]" class = "time_record" value = "'.en_dec('en',$row['app_ref_no']).'">
          <span class="checkmark"></span>
        </label>
      ';

      $nestedData[] = $checkbox1;
      $nestedData[] = $row['fullname'];
      $nestedData[] = $checkbox2;
      $nestedData[] = $checkbox3;
      $nestedData[] = $checkbox4;

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_emp_record($dbname,$ids){
    $this->app_db = switch_database($dbname);
    // $ids = $this->db->escape($ids);
    $emp_record = "SELECT * FROM employee_record WHERE enabled = 1 AND employee_idno IN ($ids)";
    $emp_dependents = "SELECT * FROM employee_dependents WHERE enabled = 1 AND employee_idno IN ($ids)";
    $emp_education = "SELECT * FROM employee_education WHERE enabled = 1 AND employee_idno IN ($ids)";
    $emp_workhistory = "SELECT * FROM employee_workhistory WHERE enabled = 1 AND employee_idno IN ($ids)";
    $time_record = "SELECT * FROM time_record_summary_trial WHERE enabled = 1 AND employee_idno IN($ids)";
    $facial_recog = "SELECT * FROM hris_facial_recog WHERE enabled = 1 AND employee_idno IN($ids)";

    $data = array(
      "emp_record" => $this->app_db->query($emp_record),
      "emp_dependents" => $this->app_db->query($emp_dependents),
      "emp_education" => $this->app_db->query($emp_education),
      "emp_workhistory" => $this->app_db->query($emp_workhistory),
      "timelog_record" => $this->app_db->query($time_record),
      "facial_recog" => $this->app_db->query($facial_recog)
    );

    return $data;
  }

  public function get_app_record($dbname,$ids){
    $this->app_db = switch_database($dbname);
    // $ids = $this->db->escape($ids);
    $app_record = "SELECT * FROM applicant_record WHERE app_enabled = 1 AND app_ref_no IN ($ids)";
    $app_dependents = "SELECT * FROM applicant_dependents WHERE enabled = 1 AND applicant_ref_no IN ($ids)";
    $app_education = "SELECT * FROM applicant_education WHERE enabled = 1 AND applicant_ref_no IN ($ids)";
    $app_workhistory = "SELECT * FROM applicant_workhistory WHERE enabled = 1 AND applicant_ref_no IN ($ids)";
    $app_interview = "SELECT *, app_ref_no as applicant_ref_no FROM hris_applicant_interview WHERE enabled = 1 AND app_ref_no IN ($ids)";
    $app_job_offer = "SELECT *, app_ref_no as applicant_ref_no FROM hris_applicant_job_offer WHERE enabled = 1 AND app_ref_no IN ($ids)";
    $app_requirements = "SELECT *, employee_idno as applicant_ref_no FROM hris_requirements WHERE enabled = 1 AND employee_idno IN ($ids)";

    $data = array(
      "app_record" => $this->app_db->query($app_record),
      "app_dependents" => $this->app_db->query($app_dependents),
      "app_education" => $this->app_db->query($app_education),
      "app_workhistory" => $this->app_db->query($app_workhistory),
      "app_interview" => $this->app_db->query($app_interview),
      "app_job_offer" => $this->app_db->query($app_job_offer),
      "app_requirements" => $this->app_db->query($app_requirements)
    );

    return $data;
  }

  public function get_emp_record_from_transferdb($dbname,$id){
    $this->app_db = switch_database($dbname);
    $id = $this->db->escape($id);
    $sql = "SELECT employee_idno FROM employee_record WHERE enabled = 1 AND employee_idno = $id";
    return $this->app_db->query($sql);
  }

  public function get_app_record_from_transferdb($dbname,$id){
    $this->app_db = switch_database($dbname);
    $id = $this->db->escape($id);
    $sql = "SELECT app_ref_no FROM applicant_record WHERE app_enabled = 1 AND app_ref_no = $id";
    return $this->app_db->query($sql);
  }

  public function set_emp_record_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('employee_record',$data);
  }

  public function set_app_record_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('applicant_record',$data);
  }

  public function set_emp_dependent_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('employee_dependents',$data);
  }

  public function set_app_dependent_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('applicant_dependents',$data);
  }

  public function set_emp_education_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('employee_education', $data);
  }

  public function set_app_education_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('applicant_education', $data);
  }

  public function set_app_interview_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('hris_applicant_interview', $data);
  }

  public function set_job_offer_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('hris_applicant_job_offer', $data);
  }

  public function set_requirements_batch($dbname,$data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('hris_requirements', $data);
  }

  public function set_emp_workhistory_batch($dbname, $data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('employee_workhistory',$data);
  }

  public function set_app_workhistory_batch($dbname, $data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('applicant_workhistory',$data);
  }

  public function set_time_record_batch($dbname, $data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('time_record_summary_trial',$data);
  }

  public function set_facial_recog_batch($dbname, $data){
    $this->app_db = switch_database($dbname);
    $this->app_db->insert_batch('hris_facial_recog',$data);
  }

}
