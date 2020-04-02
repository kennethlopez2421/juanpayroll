<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Evaluations_settings extends CI_Controller {
  public function __construct(){
    parent::__construct();
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

  public function assessment_tbl_settings(){
    $section = $this->input->post('section');
    $action = $this->input->post('action');
    $id = $this->input->post('id');
    $rating = $this->input->post('rating');
    $desc = $this->input->post('desc');
    $equivalent_rating = $this->input->post('equivalent_rating');
    $score = $this->input->post('score');

    switch ($action) {
      case 'add':
        $check_rate = $this->evaluations_settings_model->check_eval_ratings($rating,false,false);
        if($check_rate->num_rows() > 0){
          $data = array("success" => 0, "message" => "Rating <u>".$rating."</u> already exists. Please try again");
          generate_json($data);
          exit();
        }

        $check_equivalent_rating = $this->evaluations_settings_model->check_eval_ratings(false,$equivalent_rating,false);
        if($check_equivalent_rating->num_rows() > 0){
          $data = array("success" => 0, "message" => "Equivalent Rating <u>".$equivalent_rating."</u> already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $check_score = $this->evaluations_settings_model->check_eval_ratings(false,false,$score);
        if($check_score->num_rows() > 0){
          $data = array("success" => 0, "message" => "Score <u>".$score."</u> already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $insert_data = array(
          "rating" => $rating,
          "description" => $desc,
          "equivalent_rating" => $equivalent_rating,
          "score" => $score
        );

        $inserted = $this->evaluations_settings_model->set_eval_ratings($insert_data);
        if($inserted === false){
          $data = array("success" => 0, "message" => "Unable to save Performance Evaluation. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Save Successfully. ");
        generate_json($data);
        break;
      case 'edit':
        $update_data = array($rating, $equivalent_rating, $score, $desc, $id);
        foreach($update_data as $row){
          if(empty($row)){
            $data = array("success" => 1, "message" => "Please fill up all required fields.");
            generate_json($data);
            exit();
          }
        }

        $rows = $this->evaluations_settings_model->check_eval_ratings2($update_data);
        if($rows->num_rows() > 0){
          $data = array("success" => 0, "message" => "Update failed. Nothing change. Please try again.");
          generate_json($data);
          exit();
        }

        $updated = $this->evaluations_settings_model->update_eval_ratings($update_data);
        if($updated === false){
          $data = array("success" => 0, "message" => "Update Failed. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Updated Successfully");
        generate_json($data);
        break;
      case 'delete':
        $deleted = $this->evaluations_settings_model->update_eval_status($id);
        if($deleted === false){
          $data = array("success" => 0, "message" => "Unable to delete data. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Deleted Successfully. ");
        generate_json($data);
        break;
      default:
        // code...
        break;
    }
  }

  public function assessment_question_settings(){
    $id = $this->input->post('id');
    $action = $this->input->post('action');
    $title = $this->input->post('title');
    $section = $this->input->post('section');
    $desc = $this->input->post('desc');

    switch ($action) {
      case 'add':
        $insert_data = array(
          "title" => $title,
          "description" => $desc,
          "section" => $section
        );

        foreach($insert_data as $data){
          if(empty($data)){
            $data = array("success" => 0, "message" => "Please fill up all required fields");
            generate_json($data);
            exit();
          }
        }

        $check_title = $this->evaluations_settings_model->get_eval_questions($title);
        if($check_title->num_rows() > 0){
          $data = array("success" => 0, "message" => "Title already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $check_desc = $this->evaluations_settings_model->get_eval_questions(false,false,$desc);
        if($check_desc->num_rows() > 0){
          $data = array("success" => 0, "message" => "This Description already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $inserted = $this->evaluations_settings_model->set_eval_questions($insert_data);
        if($inserted === false){
          $data = array("success" => 0, "message" => "Unable to save assessment question. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Save Successfully.");
        generate_json($data);
        break;
        break;
      case 'edit':
        $update_data = array($title,$section,$desc,$id);
        foreach($update_data as $data){
          if(empty($data)){
            $data = array("success" => 0, "message" => "Please fill up all required fields.");
            generate_json($data);
            exit();
          }
        }

        $check_self = $this->evaluations_settings_model->check_eval_questions($update_data,"self");
        if($check_self->num_rows() > 0){
          $data = array("success" => 0, "message" => "Update failed. Nothing change. Please try again.");
          generate_json($data);
          exit();
        }

        $check_others = $this->evaluations_settings_model->check_eval_questions($update_data);
        if($check_others->num_rows() > 0){
          $data = array("success" => 1, "message" => "Some of the data you put already exist. Please try again.");
          generate_json($data);
          exit();
        }

        $check_title = $this->evaluations_settings_model->check_eval_questions2($id,$title);
        if($check_title === false){
          $data = array("success" => 0, "message" => "Title already exists.Please try again.");
          generate_json($data);
          exit();
        }

        $check_desc = $this->evaluations_settings_model->check_eval_questions2($id,false,$desc);
        if($check_desc->num_rows() > 0){
          $data = array("success" => 0, "message" => "This description already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $updated = $this->evaluations_settings_model->update_eval_questions($update_data);
        if($updated === false){
          $data = array("success" => 0, "message" => "Unable to update assessment question. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Updated Successfully");
        generate_json($data);
        break;
      case 'delete':
        $deleted = $this->evaluations_settings_model->update_eval_questions_status($id);
        if($deleted === false){
          $data = array("success" => 0, "message" => "Unable to  delete assessment question. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Deleted Successfully");
        generate_json($data);
        break;
      default:
        // code...
        break;
    }
  }

  public function recommendation_settings(){
    $action = $this->input->post('action');
    $id = $this->input->post('id');
    $desc = $this->input->post('desc');

    switch ($action) {
      case 'add':
        $insert_data = array(
          "description" => $desc
        );

        foreach($insert_data as $data){
          if(empty($data)){
            $data = array("success" => 0, "message" => "Please fill up all required fields.");
            generate_json($data);
            exit();
          }
        }

        $check_desc = $this->evaluations_settings_model->get_eval_recommendations($desc);
        if($check_desc === false){
          $data = array("success" => 0, "message" => "This recommendation already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $inserted = $this->evaluations_settings_model->set_eval_recommendations($insert_data);
        if($inserted === false){
          $data = array("success" => 0, "message" => "Unable to save recommendation. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Save Successfully.");
        generate_json($data);
        break;
      case 'edit':
        $update_data = array($desc,$id);
        foreach($update_data as $data){
          if(empty($data)){
            $data = array("success" => 0, "message" => "Please fill up all required fieldss.");
            generate_json($data);
            exit();
          }
        }

        $check_self = $this->evaluations_settings_model->check_eval_recommendations($update_data,"self");
        if($check_self->num_rows() > 0){
          $data = array("success" => 0, "message" => "Update failed. Nothing changed. Please try again.");
          generate_json($data);
          exit();
        }

        $check_others = $this->evaluations_settings_model->check_eval_recommendations($update_data,"others");
        if($check_others->num_rows() > 0){
          $data = array("success" => 0, "message" => "Recommendation already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $updated = $this->evaluations_settings_model->update_eval_recommendations($update_data);
        if($updated === false){
          $data = array("success" => 0, "message" => "Unable to update recommendation. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Updated Successfully");
        generate_json($data);
        break;
      case 'delete':
        $deleted = $this->evaluations_settings_model->update_eval_recommendations_status($id);
        if($deleted === false){
          $data = array("success" => 0, "message" => "Unable to delete recommendation. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Deleted Successfully");
        generate_json($data);
        break;
      default:
        // code...
        break;
    }
  }

  public function self_assessment_settings(){
    $action = $this->input->post('action');
    $id = $this->input->post('id');
    $desc = $this->input->post('desc');

    switch ($action) {
      case 'add':
        $insert_data = array(
          "question" => $desc
        );

        foreach($insert_data as $data){
          if(empty($data)){
            $data = array("success" => 0, "message" => "Please fill up all required fields.");
            generate_json($data);
            exit();
          }
        }

        $check_desc = $this->evaluations_settings_model->get_eval_self_assessment($desc);
        if($check_desc === false){
          $data = array("success" => 0, "message" => "This self assessment question already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $inserted = $this->evaluations_settings_model->set_eval_self_assessment($insert_data);
        if($inserted === false){
          $data = array("success" => 0, "message" => "Unable to save self assessment question. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Save Successfully.");
        generate_json($data);
        break;
      case 'edit':
        $update_data = array($desc,$id);
        foreach($update_data as $data){
          if(empty($data)){
            $data = array("success" => 0, "message" => "Please fill up all required fieldss.");
            generate_json($data);
            exit();
          }
        }

        $check_self = $this->evaluations_settings_model->check_eval_self_assessment($update_data,"self");
        if($check_self->num_rows() > 0){
          $data = array("success" => 0, "message" => "Update failed. Nothing changed. Please try again.");
          generate_json($data);
          exit();
        }

        $check_others = $this->evaluations_settings_model->check_eval_self_assessment($update_data,"others");
        if($check_others->num_rows() > 0){
          $data = array("success" => 0, "message" => "Self Assessment already exists. Please try again.");
          generate_json($data);
          exit();
        }

        $updated = $this->evaluations_settings_model->update_eval_self_assessment($update_data);
        if($updated === false){
          $data = array("success" => 0, "message" => "Unable to update self assessment. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Updated Successfully");
        generate_json($data);
        break;
      case 'delete':
        $deleted = $this->evaluations_settings_model->update_eval_self_assessment_status($id);
        if($deleted === false){
          $data = array("success" => 0, "message" => "Unable to delete self assessment question. Please try again.");
          generate_json($data);
          exit();
        }

        $data = array("success" => 1, "message" => "Deleted Successfully");
        generate_json($data);
        break;
      default:
        // code...
        break;
    }
  }

  public function eval_formula_settings(){
    $id = $this->input->post('id');
    $formula = $this->input->post('formula');
    $update_data = array($formula,$id);

    foreach($update_data as $data){
      if(empty($data)){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }
    }

    $updated = $this->evaluations_settings_model->update_formula($update_data);
    if($updated === false){
      $data = array("success" => 0, "message" => "Update failed. Nothing change. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Updated Successfully");
    generate_json($data);
  }

  public function index($token = ""){
    $data = array(
      'token' => $token,
      'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
      'eval_ratings' => $this->evaluations_settings_model->get_eval_ratings(),
      'eval_ratings2' => $this->evaluations_settings_model->get_eval_ratings('type_2'),
      'eval_sections' => $this->evaluations_settings_model->get_eval_section(),
      'eval_questions' => $this->evaluations_settings_model->get_eval_questions(),
      'eval_purpose' => $this->evaluations_settings_model->get_eval_purpose(),
      'eval_recommendations' => $this->evaluations_settings_model->get_eval_recommendations(),
      'eval_formula' => $this->evaluations_settings_model->get_eval_formula('type_1')->row(), // below manager
      'eval_formula2' => $this->evaluations_settings_model->get_eval_formula('type_2')->row(), // manager and above position
      'eval_self_assessment' => $this->evaluations_settings_model->get_eval_self_assessment()
    );

    $this->load->view('includes/header',$data);
    $this->load->view('evaluations/evaluations_settings',$data);
  }
}
