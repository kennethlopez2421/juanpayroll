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
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/ju/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-html5-1.6.1/b-print-1.6.1/r-2.2.3/rr-1.2.6/sc-2.0.1/datatables.min.css"/> -->

<div class="content-inner" id="pageActive" data-num="13" data-namecollapse="" data-labelname="Time Record">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/time_record/'.$token);?>">Time Record</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Time Record Summary</li>
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
                      <option value="by_date">Date Range Only</option>
                    </select>
                  </div>

                  <div class="col-md-6">

                    <div id="divDate" class = "filter_div active">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "date_from" class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id = "date_to" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <button class="btn btn-primary" id = "btn_reset_modal">Reset</button>
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button id = "btn_save" class="btn btn-primary" style = "display:none;"><i class="fa fa-save mr-2"></i>Save</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "gen_timerecord_tbl">
                    <thead>
                      <th width = "80">First Time in</th>
                      <th width = "80">Last Time out</th>
                      <th width = "80">Date</th>
                      <th>Employee ID</th>
                      <th>Employee Name</th>
                      <th width = "50">Man Hours</th>
                      <th width = "50">Night Differentials <small>(HRS)</small></th>
                      <th width = "50">Lates <small>(mins)</small></th>
                      <th width = "50">Overbreak</th>
                      <th width = "50">Undertime <small>(mins)</small></th>
                      <th>Absent</th>
                      <th width = "50">Total Minutes</th>
                      <th>Remarks</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- RESEST MODAL -->
    <div class="modal fade" id = "reset_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Confirmation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to reset time record summary ?</p>
                <input type="hidden" class="employeeid">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_reset_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\time_record\datatables.min.with.excel.export.js')?>"></script>
<script src = "<?=base_url('assets\js\time_record\timerecord_summary.js')?>"></script>
