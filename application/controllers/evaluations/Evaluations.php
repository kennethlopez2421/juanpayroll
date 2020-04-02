<?php
require 'vendor/autoload.php';
use Carbon\Carbon;

defined('BASEPATH') OR exit('No direct script access allowed');
class Evaluations extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('evaluations/evaluations_model');
    $this->load->model('evaluations/evaluations_settings_model');
    $this->isLoggedIn();
  }

  public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
  }

  public function isLoggedIn() {
    //this will destroy the session if the user not logged in
    if($this->session->userdata('isLoggedIn') == false) {
      if(empty($this->session->userdata('position_id'))) { //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
        exit();
      }
    }else{
      if(empty($this->session->userdata('position_id'))) {  //kapag destroyed na ung session
        header("location:".base_url('Main/logout'));
        exit();
      }
    }
  }

  public function get_pending_evaluations_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->evaluations_model->get_pending_evaluations_json($search);
    echo json_encode($data);
  }

  public function get_evaluated_evaluations_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->evaluations_model->get_evaluated_evaluations_json($search);
    echo json_encode($data);
  }

  public function get_certified_evaluations_json(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->evaluations_model->get_certified_evaluations_json($search);
    echo json_encode($data);
  }

  public function index($token = ""){
    // printf("Now: %s", Carbon::now());
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'departments' => $this->model->getDepartment(),
      'positions' => $this->model->get_user_position(),
      'position_lvl' => $this->model->get_position_lvl(5) // Manager or above position
    );

    $this->load->view('includes/header',$data);
    $this->load->view('evaluations/evaluations',$data);
  }

  public function view($token = "",$id){
    $this->isLoggedIn();
    $raw_id = $id;
    $id = en_dec('dec',$id);
    $emp_id = $this->evaluations_model->get_evaluation($id)->row()->emp_id;
    $contracts = $this->evaluations_model->get_all_contract($emp_id);
    if($contracts->num_rows() > 0){
      $prev = "";
      $contract_start = "";
      foreach($contracts->result_array() as $row){
        $new = $row['position_id'];
        if($new == $prev){
          $contract_start = $row['contract_start'];
        }else{
          $contract_start = $row['contract_start'];
        }
        $prev = $row['position_id'];
      }
    }else{
      $this->logout();
    }
    $eval_date = $this->evaluations_model->get_evaluation($id)->row()->eval_date;
    $date1 = strtotime($contract_start);
    $date2 = strtotime($eval_date);

    $year1 = date('Y',$date1);
    $year2 = date('Y',$date2);

    $month1 = date('m',$date1);
    $month2 = date('m',$date2);

    $current_pos_stay = (($year2 - $year1)).' year(s) '.($month2 - $month1).' month(s)';
    $data = array(
      'token' => $token,
      'raw_id' => $raw_id,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'evaluations' => $this->evaluations_model->get_evaluation($id),
      'eval_ratings' => $this->evaluations_settings_model->get_eval_ratings(),
      'eval_ratings2' => $this->evaluations_settings_model->get_eval_ratings('type_2'),
      'eval_sections' => $this->evaluations_settings_model->get_eval_section(),
      'eval_questions' => $this->evaluations_settings_model->get_eval_questions(),
      'eval_purpose' => $this->evaluations_settings_model->get_eval_purpose(),
      'eval_recommendations' => $this->evaluations_settings_model->get_eval_recommendations(),
      'eval_formula' => $this->evaluations_settings_model->get_eval_formula()->row(),
      'eval_formula2' => $this->evaluations_settings_model->get_eval_formula('type_2')->row(),
      'eval_status' => $this->evaluations_model->get_evaluation($id)->row()->status2,
      'eval_self_assessment' => $this->evaluations_settings_model->get_eval_self_assessment(),
      'current_pos_stay' => $current_pos_stay
    );

    $this->load->view('includes/header',$data);
    $this->load->view('evaluations/evaluations_view',$data);
  }

  public function create(){
    $question = $this->input->post('question');
    $recommend = $this->input->post('recommend');
    $assessment = $this->input->post('assessment');
    $emp_comment = $this->input->post('emp_comment');
    $project = $this->input->post('project');
    $proj_comment = $this->input->post('proj_comment');
    $purpose = $this->input->post('purpose');
    $purpose_value = $this->input->post('purpose_value');
    $eval_id = $this->input->post('eval_id');
    $eval_total_score = $this->input->post('eval_total_score');
    $eval_score_percent = $this->input->post('eval_score_percent');
    $eval_equivalent_rate = $this->input->post('eval_equivalent_rate');

    if(empty($purpose) || empty($purpose_value) || empty($eval_id) || empty($eval_total_score) ||
       empty($eval_total_score) || empty($eval_score_percent) || empty($eval_equivalent_rate)
    ){
      $data = array("success" => 0, "message" => "Please fill up all required fields. ");
      generate_json($data);
      exit();
    }

    $update_data = array(
      "eval_score" => $eval_total_score,
      "eval_score_percent" => $eval_score_percent,
      "eval_equivalent_rate" => $eval_equivalent_rate,
      "eval_remarks" => $question,
      "eval_recommendations" => $recommend,
      "eval_purpose_type" => $purpose,
      "eval_purpose" => $purpose_value,
      "eval_assessment" => $assessment,
      "eval_project" => $project,
      "eval_comments" => $emp_comment,
      "eval_proj_comment" => $proj_comment,
      "status" => "seen",
      "status2" => "evaluated"
    );

    $updated = $this->evaluations_model->update_hris_eval($update_data,$eval_id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to save evaluation. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Evaluations saved successfully.");
    generate_json($data);

  }

  public function calculate(){
    $score = $this->input->post('score');
    $formula = $this->input->post('formula');

    $calculate = str_replace('Total_Points',$score,$formula);
    $result = eval('return '.$calculate.';');
    $rating  = $this->evaluations_model->get_ratings()->result_array();
    $arr = array();
    foreach ($rating as $rate) {
      $score_arr = explode('-', $rate['score']);
      $a = (int)$score_arr[0];
      $b = ($score_arr[1] == 'below') ? 0 : (int)$score_arr[1];
      if(in_range(round($result),$a,$b)){
        $arr['equivalent_rating'] = $rate['equivalent_rating'];
        $arr['score'] = $rate['score'];
      }
    }

    $data = array("success" => 1, "score" => $score, "score_percent" => round($result), "result" => $arr);
    generate_json($data);
  }

  public function certify(){
    $action_hr = $this->input->post('action_hr');
    $eval_id = $this->input->post('eval_id');
    $update_data = array(
      "eval_action_hr" => $action_hr,
      "certify_by" => $this->session->emp_idno,
      "status2" => "certified"
    );

    $updated = $this->evaluations_model->certify($update_data,$eval_id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to certify evaluation. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Evaluation certified successfully.");
    generate_json($data);
  }

  public function print($token = "", $id){
    $id = en_dec('dec',$id);
    $emp_id = $this->evaluations_model->get_evaluation($id)->row()->emp_id;
    $ref_no = $this->evaluations_model->get_evaluation($id)->row()->ref_no;
    $fullname = $this->evaluations_model->get_evaluation($id)->row()->fullname;
    $contracts = $this->evaluations_model->get_all_contract($emp_id);
    if($contracts->num_rows() > 0){
      $prev = "";
      $contract_start = "";
      foreach($contracts->result_array() as $row){
        $new = $row['position_id'];
        if($new == $prev){
          $contract_start = $row['contract_start'];
        }else{
          $contract_start = $row['contract_start'];
        }
        $prev = $row['position_id'];
      }
    }else{
      $this->logout();
    }
    $eval_date = $this->evaluations_model->get_evaluation($id)->row()->eval_date;
    $date1 = strtotime($contract_start);
    $date2 = strtotime($eval_date);

    $year1 = date('Y',$date1);
    $year2 = date('Y',$date2);

    $month1 = date('m',$date1);
    $month2 = date('m',$date2);

    $current_pos_stay = (($year2 - $year1)).' year(s) '.($month2 - $month1).' month(s)';
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'evaluations' => $this->evaluations_model->get_evaluation($id),
      'eval_ratings' => $this->evaluations_settings_model->get_eval_ratings(),
      'eval_sections' => $this->evaluations_settings_model->get_eval_section(),
      'eval_questions' => $this->evaluations_settings_model->get_eval_questions(),
      'eval_purpose' => $this->evaluations_settings_model->get_eval_purpose(),
      'eval_recommendations' => $this->evaluations_settings_model->get_eval_recommendations(),
      'eval_formula' => $this->evaluations_settings_model->get_eval_formula()->row(),
      'eval_status' => $this->evaluations_model->get_evaluation($id)->row()->status2,
      'current_pos_stay' => $current_pos_stay
    );

    $content = "";
    // $content .= $this->load->view('includes/header',$data,true);
    $content .= $this->load->view('evaluations/evaluations_print',$data,true);
    // $eval = $this->evaluations_model->get_evaluation($id)->row();

    $this->load->library('Pdf');
    $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $title = $fullname.' Performance Evaluation';
    $pdf->SetTitle($title);
    $pdf->SetDefaultMonospacedFont('helvetica');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->setFontSubsetting(false);
    $pdf->setPrintHeader(false);
    $pdf->AddPage();
    $pdf->setCellPaddings(0,0,0,0);

    ob_start();
    $style = array(
      'border' => false,
      'padding' => 0,
      'fgcolor' => array(0,0,0),
      'bgcolor' => false,
    );

    $pdf->write2DBarcode($ref_no, 'QRCODE,H', 170, 15, 17, 17, $style, 'N');
    echo $content;
    $content2 = ob_get_contents();
    $pdf->writeHTML($content2, true, false, true, false, '');
    ob_end_clean();
    $pdf->Output($title.".pdf", 'I');
  }

  public function reassign(){
    $dept = $this->input->post('dept');
    $pos_lvl = $this->input->post('pos_lvl');
    $hris_users = $this->input->post('hris_users');
    $eval_id = en_dec('dec',$this->input->post('eval_id'));

    if(empty($dept) || empty($pos_lvl) || empty($hris_users) || empty($eval_id)){
      $data = array("success" => 0, "message" => "Please fill up all required fields.");
      generate_json($data);
      exit();
    }

    $reassign = $this->evaluations_model->reassign_eval($eval_id,$hris_users);
    if($reassign === false){
      $data = array("success" => 0, "message" => "Unable to reassign this evaluation to another user. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Evaluation reassign to another user successfully.");
    generate_json($data);
  }

  public function delete(){
    $eval_del_id = en_dec('dec',$this->input->post('eval_del_id'));
    if(empty($eval_del_id)){
      $data = array("success" => 0, "message" => "Unable to delete evaluation. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $updated = $this->evaluations_model->update_eval_status($eval_del_id);
    if($updated === false){
      $data = array("success" => 0, "message" => "Unable to delete evaluation. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Evaluation successfully deleted");
    generate_json($data);
  }
}
