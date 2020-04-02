<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Branch_login_model extends CI_Model {
  public function get_company_code($code){
    $code = $this->db->escape($code);
    $sql = "SELECT * FROM hris_branch WHERE branch_code = $code AND enabled = 1";
    return $this->db->query($sql);
  }
}
