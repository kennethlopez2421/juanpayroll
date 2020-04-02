<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_model extends CI_Model {
  public function get_admin_user($user){
    $user = $this->db->escape($user);
    $sql = "SELECT * FROM hris_admin_user WHERE enabled = 1 AND status = 'active' AND username = $user";
    return $this->db->query($sql);
  }
}
