<?php
//071318
//this code is for destroying session and page if they access restricted page

$position_access = $this->session->userdata('get_position_access');
$access_content_nav = $position_access->access_content_nav;
$arr_ = explode(', ', $access_content_nav); //string comma separated to array
$get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();

$url_content_arr = array();
foreach ($get_url_content_db as $cun) {
    $url_content_arr[] = $cun['cn_url'];
}
$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/';

if (in_array($content_url, $url_content_arr) == false){
    header("location:".base_url('Main/logout'));
}
//071318
?>
<div class="content-inner" id="pageActive" data-num="13" data-namecollapse="" data-labelname="Time Record">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/time_record/'.$token);?>">Time Record</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Time Record Logs</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                    <select name="" id="filter_by" class="form-control">
                      <option value="by_admin">Admin</option>
                      <option value="by_name">Employee Name</option>
                      <option value="by_timelog_date">Timelog Date</option>
                      <option value="by_log_date">Log Date</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divAdmin" class = "filter_div single_search active">
                      <label for="Select Admin" class="form-control-label col-form-label-sm">Select Admin</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($admins->num_rows() > 0):?>
                          <?php foreach($admins->result_array() as $admin):?>
                            <option value="<?=$admin['admin_id']?>"><?=$admin['fullname']?> (<?=$admin['position']?>)</option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divName" class = "filter_div single_search" style = "display:none;">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divDate" class = "filter_div range_date" style = "display:none;">
                      <label for="Timelog Date" class="form-control-label col-form-label-sm">Timelog Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "timelog_from" class="form-control date_from date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id = "timelog_to" class="form-control date_to date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                    <div id="divLogDate" class = "filter_div range_date" style = "display:none;">
                      <label for="Log Date" class="form-control-label col-form-label-sm">Log Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "logdate_from" class="form-control date_from date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id = "logdate_to" class="form-control date_to date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <!-- <a href="<?= base_url('transactions/Additionalpays/add/'.$token) ?>"><button class="btn btn-primary btnClickAddArea">Add</button></a> -->
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "timerecord_logs_tbl">
                    <thead>
                      <th>Admin</th>
                      <th>Employee</th>
                      <th>Logs</th>
                      <th>Timelog Date</th>
                      <th width = "180">Log Date</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\time_record\timerecord_logs.js')?>"></script>
