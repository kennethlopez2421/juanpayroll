<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Evaluations_settings_model extends CI_Model {
  public function get_eval_ratings($type = false){
    $sql = "SELECT * FROM hris_eval_ratings WHERE enabled = 1";
    if($type){
      $type = $this->db->escape($type);
      $sql .= " AND eval_type = $type";
    }
    $sql .= "  ORDER BY rating DESC";
    return $this->db->query($sql);
  }

  public function get_eval_section(){
    $sql = "SELECT * FROM hris_eval_section WHERE enabled = 1 ORDER BY section ASC";
    return $this->db->query($sql);
  }

  public function get_eval_questions($title = false, $section = false, $desc = false){
    if($title){
      $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1 AND title = ? ORDER BY id ASC";
      $data = array($title);
      return $this->db->query($sql,$data);
    }

    if($desc){
      $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1 AND description = ? ORDER BY id ASC";
      $data = array($desc);
      return $this->db->query($sql,$data);
    }

    if($section){
      $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1 AND section = ? ORDER BY id ASC";
      $data = array($section);
      return $this->db->query($sql,$data);
    }

    $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1 ORDER BY id ASC";
    return $this->db->query($sql);
  }

  public function get_eval_purpose(){
    $sql = "SELECT * FROM hris_eval_purpose WHERE enabled = 1 ORDER BY id ASC";
    return $this->db->query($sql);
  }

  public function get_eval_recommendations($desc = false){
    if($desc){
      $sql = "SELECT * FROM hris_eval_recommendations WHERE enabled = 1 AND description = ?";
      $data = array($desc);
      return $this->db->query($sql,$data);
    }

    $sql = "SELECT * FROM hris_eval_recommendations WHERE enabled = 1 ORDER BY id ASC";
    return $this->db->query($sql);
  }

  public function get_eval_formula($type = false){
    $sql = "SELECT * FROM hris_eval_formula WHERE enabled = 1";
    if($type){
      $type = $this->db->escape($type);
      $sql .= " AND type = $type";
    }
    $sql .= " ORDER BY id ASC";
    return $this->db->query($sql);
  }

  public function get_eval_self_assessment($question = false){
    $sql = "SELECT * FROM hris_eval_self_assessment WHERE enabled = 1";
    if($question){
      $question = $this->db->escape($question);
      $sql .= " AND question = $question";
    }
    $sql .= " ORDER BY id ASC";
    return $this->db->query($sql);
  }

  public function update_eval_ratings($data){
    $sql = "UPDATE hris_eval_ratings SET rating = ?, equivalent_rating = ?, score = ?, description = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_eval_status($id, $status = 0){
    $sql = "UPDATE hris_eval_ratings SET enabled = ? WHERE id = ?";
    $data = array($status, $id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_eval_questions($data){
    $sql = "UPDATE hris_eval_questions SET title = ?, section = ?, description = ? WHERE id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_eval_questions_status($id,$status = 0){
    $sql = "UPDATE hris_eval_questions SET enabled = ? WHERE id = ?";
    $data = array($status,$id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_eval_recommendations($data){
    $sql = "UPDATE hris_eval_recommendations SET description = ? WHERE enabled = 1 AND id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_eval_self_assessment($data){
    $sql = "UPDATE hris_eval_self_assessment SET question = ? WHERE enabled = 1 AND id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_eval_recommendations_status($id,$status = 0){
    $sql = "UPDATE hris_eval_recommendations SET enabled = ? WHERE id = ?";
    $data = array($status,$id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_eval_self_assessment_status($id,$status = 0){
    $sql = "UPDATE hris_eval_self_assessment SET enabled = ? WHERE id = ?";
    $data = array($status,$id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_formula($data){
    $sql = "UPDATE hris_eval_formula SET formula = ? WHERE enabled = 1 AND id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0 )? true: false;
  }

  public function check_eval_ratings($rate = false, $rate2 = false, $score = false){
    if($rate){
      $sql = "SELECT * FROM hris_eval_ratings WHERE enabled = 1 AND rating = ?";
      $data = array($rate);
    }

    if($rate2){
      $sql = "SELECT * FROM hris_eval_ratings WHERE enabled = 1 AND equivalent_rating = ?";
      $data = array($rate2);
    }

    if($score){
      $sql = "SELECT * FROM hris_eval_ratings WHERE enabled = 1 AND score = ?";
      $data = array($score);
    }
    return $this->db->query($sql,$data);
  }

  public function check_eval_ratings2($data){
    $sql = "SELECT * FROM hris_eval_ratings WHERE enabled = 1 AND rating = ? AND equivalent_rating = ?
      AND score = ? AND description = ? AND id = ?";
    return $this->db->query($sql,$data);
  }

  public function check_eval_questions($data,$search = "others"){
    if($search === "others"){
      $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1
        AND title = ? AND section = ? AND description = ? AND id != ?";
      return $this->db->query($sql,$data);
    }

    if($search === "self"){
      $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1
        AND title = ? AND section = ? AND description = ? AND id = ?";
      return $this->db->query($sql,$data);
    }
  }

  public function check_eval_questions2($id,$title = false, $desc = false){
    if($title){
      $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1 AND title = ? AND id != ?";
      $data = array($title,$id);
      return $this->db-query($sql,$data);
    }

    if($desc){
      $sql = "SELECT * FROM hris_eval_questions WHERE enabled = 1 AND description = ? AND id != ?";
      $data = array($desc,$id);
      return $this->db->query($sql,$data);
    }
  }

  public function check_eval_recommendations($data,$search = "others"){
    if($search == "others"){
      $sql = "SELECT * FROM hris_eval_recommendations WHERE enabled = 1 AND description = ? AND id != ?";
      return $this->db->query($sql,$data);
    }

    if($search == "self"){
      $sql = "SELECT * FROM hris_eval_recommendations WHERE enabled = 1 AND description = ? AND id = ?";
      return $this->db->query($sql,$data);
    }
  }

  public function check_eval_self_assessment($data,$search = "others"){
    if($search == "others"){
      $sql = "SELECT * FROM hris_eval_self_assessment WHERE enabled = 1 AND question = ? AND id != ?";
      return $this->db->query($sql,$data);
    }

    if($search == "self"){
      $sql = "SELECT * FROM hris_eval_self_assessment WHERE enabled = 1 AND question = ? AND id = ?";
      return $this->db->query($sql,$data);
    }
  }

  public function set_eval_ratings($data){
    $this->db->insert('hris_eval_ratings', $data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_eval_questions($data){
    $this->db->insert('hris_eval_questions',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_eval_recommendations($data){
    $this->db->insert('hris_eval_recommendations', $data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function set_eval_self_assessment($data){
    $this->db->insert('hris_eval_self_assessment', $data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

}
