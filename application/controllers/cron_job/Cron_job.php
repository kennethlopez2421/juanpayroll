<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron_job extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('cron_job/cron_job_model');
  }

  public function contract_expire_cron(){
    $this->cron_job_model->update_contract_expiration();
  }

}
