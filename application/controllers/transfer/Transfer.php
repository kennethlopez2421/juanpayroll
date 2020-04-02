<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('transfer/transfer_model');
  }

  public function get_data_from_database_origin(){
    $database_origin = en_dec('dec',$this->input->post('database_origin'));
    $search = json_decode($this->input->post('searchValue'));
    if(empty($database_origin)){
      $data = array("success" => 0, "message" => "Invalid data origin. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    if($search->search == "employee_record"){
      $data = $this->transfer_model->get_data_from_database($database_origin,$search);
    }

    if($search->search == "applicant_record"){
      $data = $this->transfer_model->get_data_from_database_applicant($database_origin,$search);

    }

    echo json_encode($data);
  }

  public function transfer(){
    $emp_record = $this->input->post('emp_record');
    $contract_record = $this->input->post('contract_record');
    $time_record = $this->input->post('time_record');
    $data_origin = en_dec('dec',$this->input->post('data_origin'));
    $transfer_to = en_dec('dec',$this->input->post('transfer_to'));

    if(empty($emp_record) || empty($contract_record) || empty($time_record)){
      $data = array("success" => 0, "message" => "No data to transfer . Please try again.");
      generate_json($data);
      exit();
    }

    if(empty($transfer_to)){
      $data = array("success" => 0, "message" => "Please select the database you're going to transfer to .");
      generate_json($data);
      exit();
    }

    ///////////////////////////////////////////////////////////////////////////
    //////////  EMPLOYEE RECORDS
    ///////////////////////////////////////////////////////////////////////////
    $emp_ids = implode(',',decrypt_array($emp_record));
    $emp_data = $this->transfer_model->get_emp_record($data_origin,$emp_ids);

    $emp_record_batch = array();
    $emp_dependents_batch = array();
    $emp_education_batch = array();
    $emp_workhistory_batch = array();
    $time_record_batch = array();
    $facial_recog_batch = array();

    if($emp_data['emp_record']->num_rows() > 0){
      $emp_dep = ($emp_data['emp_dependents']->num_rows() > 0) ? $emp_data['emp_dependents']->result_array() : array() ;
      $emp_educ = ($emp_data['emp_education']->num_rows() > 0) ? $emp_data['emp_education']->result_array() : array() ;
      $emp_work = ($emp_data['emp_workhistory']->num_rows() > 0) ? $emp_data['emp_workhistory']->result_array() : array() ;
      $time_rec = ($emp_data['timelog_record']->num_rows() > 0) ? $emp_data['timelog_record']->result_array() : array() ;
      $facial_recog = ($emp_data['facial_recog']->num_rows() > 0) ? $emp_data['facial_recog']->result_array() : array() ;

      foreach($emp_data['emp_record']->result_array() as $row){
        unset($row['id']);
        $row['isActive'] = 0;
        $curr_id = $row['employee_idno'];
        $isExist = $this->transfer_model->get_emp_record_from_transferdb($transfer_to,$row['employee_idno']);
        if($isExist->num_rows() > 0){
          $new_id = generate_player_no();
      		while($this->transfer_model->get_emp_record_from_transferdb($transfer_to,$new_id)->num_rows() > 0){
      			$new_id = generate_player_no();
      		}

          $row['employee_idno'] = $new_id;
        }
        // EMPLOYEE DEPENDENTS
        $dependent = filter_array2($emp_dep,$curr_id);
        if($curr_id != $row['employee_idno'] && count((array)$dependent) > 0){
          foreach($dependent as $dep){
            $dep['employee_idno'] = $row['employee_idno'];
          }
        }
        // EMPLOYEE EDUCATION
        $education = filter_array2($emp_educ,$curr_id);
        if($curr_id != $row['employee_idno'] && count((array)$dependent) > 0){
          foreach($education as $educ){
            $educ['employee_idno'] = $row['employee_idno'];
          }
        }
        // EMPLOYEE WORKHISTORY
        $work = filter_array2($emp_work,$curr_id);
        if($curr_id != $row['employee_idno'] && count((array)$work) > 0){
          foreach($work as $wor){
            $wor['employee_idno'] = $row['employee_idno'];
          }
        }
        // TIME RECORD
        $time = filter_array2($time_rec,$curr_id);
        if($curr_id != $row['employee_idno'] && count((array)$time) > 0){
          foreach($time as $t){
            // $t['current_location'] = json_encode($t['current_location']);
            $t['employee_idno'] = $row['employee_idno'];
          }
        }
        // FACIAL RECOG
        $facial = filter_array2($facial_recog,$curr_id);
        if($curr_id != $row['employee_idno'] && count((array)$facial) > 0){
          foreach($facial as $f){
            $f['employee_idno'] = $row['employee_idno'];
          }
        }

        // DEPENDENT
        foreach($dependent as $d){
          $dependent_data = array(
            "employee_idno" => $d['employee_idno'],
            "first_name" => $d['first_name'],
            "middle_name" => $d['middle_name'],
            "last_name" => $d['last_name'],
            "birthday" => $d['birthday'],
            "relationship" => $d['relationship'],
            "contact_no" => $d['contact_no'],
            "enabled" => $d['enabled']
          );
          $emp_dependents_batch[] = $dependent_data;
        }
        // EDUCATION
        foreach($education as $e){
          $educ_data = array(
            "employee_idno" => $e['employee_idno'],
            "year_from" => $e['year_from'],
            "year_to" => $e['year_to'],
            "school" => $e['school'],
            "course" => $e['course'],
            "level" => $e['level'],
            "enabled" => $e['enabled']
          );

          $emp_education_batch[] = $educ_data;
        }
        // WORK HISTORY
        foreach($work as $w){
          $work_data = array(
            "employee_idno" => $w['employee_idno'],
            "year_from" => $w['year_from'],
            "year_to" => $w['year_to'],
            "stay" => $w['stay'],
            "company_name" => $w['company_name'],
            "position" => $w['position'],
            "level" => $w['level'],
            "contact_no" => $w['contact_no'],
            "responsibility" => $w['responsibility'],
            "enabled" => $w['enabled']
          );

          $emp_workhistory_batch[] = $work_data;
        }
        //  TIME RECORD
        foreach($time as $t){
          $time_data = array(
            "employee_idno" => $t['employee_idno'],
            "worksite" => $t['worksite'],
            "date" => $t['date'],
            "type" => $t['type'],
            "mode" => $t['mode'],
            "img_url" => $t['img_url'],
            "current_location" => $t['current_location'],
            "time_in" => $t['time_in'],
            "time_out" => $t['time_out'],
            "date_created" => $t['date_created'],
            "status_absent" => $t['status_absent'],
            "enabled" => $t['enabled']
          );

          $time_record_batch[] = $time_data;
        }
        //  FACIAL RECOG
        foreach($facial as $f){
          $facial_data = array(
            "employee_idno" => $f['employee_idno'],
            "facial_landmarks" => $f['facial_landmarks'],
            "accuracy" => $f['accuracy'],
            "descriptor" => $f['descriptor'],
            "img_src" => $f['img_src'],
            "enabled" => $f['enabled']
          );

          $facial_recog_batch[] = $facial_data;
        }
        // print_r($time);


        $emp_record_batch[] = $row;
        // $emp_dependents_batch[] = $dependent;
        // $emp_education_batch[] = $education;
        // $emp_workhistory_batch[] = $work;
        // $time_record_batch[] = $time;
        // print_r($emp_record_batch);
        // die();
      }
    }
    // die();
    $this->transfer_model->set_emp_record_batch($transfer_to,$emp_record_batch);
    if(count((array)$emp_dependents_batch) > 0){
      $this->transfer_model->set_emp_dependent_batch($transfer_to,$emp_dependents_batch);
    }

    if(count((array)$emp_education_batch) > 0){
      $this->transfer_model->set_emp_education_batch($transfer_to,$emp_education_batch);
    }

    if(count((array)$emp_workhistory_batch) > 0){
      $this->transfer_model->set_emp_workhistory_batch($transfer_to,$emp_workhistory_batch);
    }

    if(count((array)$time_record_batch) > 0){
      $this->transfer_model->set_time_record_batch($transfer_to,$time_record_batch);
    }

    if(count((array)$facial_recog_batch) > 0){
      $this->transfer_model->set_facial_recog_batch($transfer_to,$facial_recog_batch);
    }


    // print_r($test);
    // die();
    $data = array("success" => 1, "message" => "Transfer Complete");
    generate_json($data);
  }

  public function transfer_applicant(){
    $app_record = $this->input->post('app_record');
    $data_origin = en_dec('dec',$this->input->post('data_origin'));
    $transfer_to = en_dec('dec',$this->input->post('transfer_to'));

    if(empty($app_record)){
      $data = array("success" => 0, "message" => "No applicant to transfer . Please try again.");
      generate_json($data);
      exit();
    }

    if(empty($transfer_to)){
      $data = array("success" => 0, "message" => "Please select the database you're going to transfer to .");
      generate_json($data);
      exit();
    }

    ///////////////////////////////////////////////////////////////////////////
    //////////  APPLICANT RECORDS
    ///////////////////////////////////////////////////////////////////////////
    $app_ids = implode(',',decrypt_array($app_record));
    $app_data = $this->transfer_model->get_app_record($data_origin,$app_ids);

    $app_record_batch = array();
    $app_dependents_batch = array();
    $app_education_batch = array();
    $app_workhistory_batch = array();
    $app_interview_batch = array();
    $app_job_offer_batch = array();
    $app_requirements_batch = array();

    if($app_data['app_record']->num_rows() > 0){
      $app_dep = ($app_data['app_dependents']->num_rows() > 0) ? $app_data['app_dependents']->result_array() : array() ;
      $app_educ = ($app_data['app_education']->num_rows() > 0) ? $app_data['app_education']->result_array() : array() ;
      $app_work = ($app_data['app_workhistory']->num_rows() > 0) ? $app_data['app_workhistory']->result_array() : array() ;
      $app_int = ($app_data['app_interview']->num_rows() > 0) ? $app_data['app_interview']->result_array() : array() ;
      $app_job = ($app_data['app_job_offer']->num_rows() > 0) ? $app_data['app_job_offer']->result_array() : array() ;
      $app_req = ($app_data['app_requirements']->num_rows() > 0) ? $app_data['app_requirements']->result_array() : array() ;

      foreach($app_data['app_record']->result_array() as $row){
        unset($row['id']);
        $curr_id = $row['app_ref_no'];
        $isExist = $this->transfer_model->get_app_record_from_transferdb($transfer_to,$row['app_ref_no']);
        if($isExist->num_rows() > 0){
          $new_id = generate_player_no();
      		while($this->transfer_model->get_app_record_from_transferdb($transfer_to,$new_id)->num_rows() > 0){
      			$new_id = generate_player_no();
      		}

          $row['app_ref_no'] = $new_id;
        }
        // EMPLOYEE DEPENDENTS
        $dependent = filter_array3($app_dep,$curr_id);
        if($curr_id != $row['app_ref_no'] && count((array)$dependent) > 0){
          foreach($dependent as $dep){
            $dep['applicant_ref_no'] = $row['app_ref_no'];
          }
        }
        // EMPLOYEE EDUCATION
        $education = filter_array3($app_educ,$curr_id);
        if($curr_id != $row['app_ref_no'] && count((array)$education) > 0){
          foreach($education as $educ){
            $educ['applicant_ref_no'] = $row['app_ref_no'];
          }
        }
        // EMPLOYEE WORKHISTORY
        $work = filter_array3($app_work,$curr_id);
        if($curr_id != $row['app_ref_no'] && count((array)$work) > 0){
          foreach($work as $wor){
            $wor['applicant_ref_no'] = $row['app_ref_no'];
          }
        }
        // APPLICANT INTERVIEW
        $interview = filter_array3($app_int,$curr_id);
        if($curr_id != $row['app_ref_no'] && count((array)$interview) > 0){
          foreach($interview as $int){
            $int['applicant_ref_no'] = $row['app_ref_no'];
          }
        }
        // APPLICANT JOB OFFER
        $job_offer = filter_array3($app_job,$curr_id);
        if($curr_id != $row['app_ref_no'] && count((array)$job_offer) > 0){
          foreach($job_offer as $jo){
            $jo['applicant_ref_no'] = $row['app_ref_no'];
          }
        }
        // APPLICANT REQUIREMENTS
        $req = filter_array3($app_req,$curr_id);
        if($curr_id != $row['app_ref_no'] && count((array)$req) > 0){
          foreach($req as $rq){
            $rq['applicant_ref_no'] = $row['app_ref_no'];
          }
        }

        // DEPENDENT
        foreach($dependent as $d){
          $dependent_data = array(
            "applicant_ref_no" => $d['applicant_ref_no'],
            "first_name" => $d['first_name'],
            "middle_name" => $d['middle_name'],
            "last_name" => $d['last_name'],
            "birthday" => $d['birthday'],
            "relationship" => $d['relationship'],
            "contact_no" => $d['contact_no'],
            "enabled" => $d['enabled']
          );
          $app_dependents_batch[] = $dependent_data;
        }
        // EDUCATION
        foreach($education as $e){
          $educ_data = array(
            "applicant_ref_no" => $e['applicant_ref_no'],
            "year_from" => $e['year_from'],
            "year_to" => $e['year_to'],
            "school" => $e['school'],
            "course" => $e['course'],
            "level" => $e['level'],
            "enabled" => $e['enabled']
          );

          $app_education_batch[] = $educ_data;
        }
        // WORK HISTORY
        foreach($work as $w){
          $work_data = array(
            "applicant_ref_no" => $w['applicant_ref_no'],
            "year_from" => $w['year_from'],
            "year_to" => $w['year_to'],
            "stay" => $w['stay'],
            "company_name" => $w['company_name'],
            "position" => $w['position'],
            "level" => $w['level'],
            "contact_no" => $w['contact_no'],
            "responsibility" => $w['responsibility'],
            "enabled" => $w['enabled']
          );

          $app_workhistory_batch[] = $work_data;
        }
        // JOB INTERVIEW
        foreach($interview as $i){
          $int_data = array(
            "app_ref_no" => $i['applicant_ref_no'],
            "interviewer" => $i['interviewer'],
            "interview_notes" => $i['interview_notes']
          );

          $app_interview_batch[] = $int_data;
        }
        // JOB OFFER
        foreach($job_offer as $j){
          $jo_data = array(
            "app_ref_no" => $j['applicant_ref_no'],
            "content" => $j['content']
          );

          $app_job_offer_batch[] = $jo_data;
        }
        // REQUIREMENTS
        foreach($req as $r){
          $rq_data = array(
            "employee_idno" => $r['applicant_ref_no'],
            "file_path" => $r['file_path'],
            "req_type" => $r['req_type']
          );

          $app_requirements_batch[] = $rq_data;
        }

        $app_record_batch[] = $row;
      }
    }
    // die();
    $this->transfer_model->set_app_record_batch($transfer_to,$app_record_batch);
    if(count((array)$app_dependents_batch) > 0){
      $this->transfer_model->set_app_dependent_batch($transfer_to,$app_dependents_batch);
    }

    if(count((array)$app_education_batch) > 0){
      $this->transfer_model->set_app_education_batch($transfer_to,$app_education_batch);
    }

    if(count((array)$app_workhistory_batch) > 0){
      $this->transfer_model->set_app_workhistory_batch($transfer_to,$app_workhistory_batch);
    }

    if(count((array)$app_interview_batch) > 0){
      $this->transfer_model->set_app_interview_batch($transfer_to,$app_interview_batch);
    }

    if(count((array)$app_job_offer_batch) > 0){
      $this->transfer_model->set_job_offer_batch($transfer_to,$app_job_offer_batch);
    }

    if(count((array)$app_requirements_batch) > 0){
      $this->transfer_model->set_requirements_batch($transfer_to,$app_requirements_batch);
    }

    $data = array("success" => 1, "message" => "Transfer Complete");
    generate_json($data);
  }

}
