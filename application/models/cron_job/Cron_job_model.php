<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron_job_model extends CI_Model {
  public function update_contract_expiration(){
    $date = $this->db->escape(today());
    $datetime = $this->db->escape(todaytime());
    $sql = "UPDATE contract SET contract_status = 'inactive', updated_at = $datetime
      WHERE contract_end < DATE_ADD($date, INTERVAL 2 MONTH) AND contract_status = 'active' AND enabled = 1";
    $this->db->query($sql);
  }
}
