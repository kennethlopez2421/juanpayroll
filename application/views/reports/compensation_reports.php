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

<style>
  .ui-datepicker-calendar {
      display: none;
  }
</style>
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
          <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
          <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
          <li class="breadcrumb-item active">Compensation Reports</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <!-- search filter -->
                <div class="form-group row">

                  <div class="col-md-3">
                    <div class="row">
                      <!-- filter  -->
                      <div class="col-md-12 mb-3">
                        <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                        <select name="" id="filter_by" class="form-control">
                          <option value="by_date">Date</option>
                        </select>
                      </div>
                      <!-- filter2  -->
                      <div class="col-md-12 mb-3">
                        <select name="" id="filter_by2" class="form-control">
                          <option value="">------</option>
                          <option value="by_id">Employee ID</option>
                          <option value="by_name">Employee Name</option>
                          <option value="by_ref_no">Payroll Reference No.</option>
                        </select>
                      </div>

                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="row">
                      <!-- filter -->
                      <div class="col-md-12 mb-3">
                        <div id="divDate" class = "filter_div active">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text"  class="form-control first_day from" placeholder="Ex. yyyy-mm-dd">
                            </div>
                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control last_day to" placeholder="Ex. yyyy-mm-dd">
                            </div>
                          </div>
                        </div>

                      </div>
                      <!-- filter 2 -->
                      <div class="col-md-12 mb-3">
                        <div id="divEmpty" class="filter_div2 active">
                          <!-- <input type="text" class="form-control" readonly> -->
                        </div>

                        <div id="divID" class = "filter_div2" style = "display:none;">
                          <input type="text" class="form-control searchArea2" placeholder="Employee ID">
                        </div>

                        <div id="divName" class = "filter_div2" style = "display:none;">
                          <input type="text" class="form-control searchArea2" placeholder="Ex. John Doe">
                        </div>

                        <div id="divRefNo" class="filter_div2" style = "display:none;">
                          <input type="text" class="form-control searchArea2" placeholder="Payroll Reference No.">
                        </div>

                      </div>
                    </div>

                  </div>

                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_export">Export to Excel</button>
                  </div>

                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive" id = "test">
                  <table class="table table-bordered table-striped" style = "border-top:1px solid gainsboro;" id = "compnesation_reports_tbl">
                    <thead>
                      <th>Employee ID</th>
                      <th>Employee Name</th>
                      <th>Payroll Ref No.</th>
                      <th>Date</th>
                      <th>SSS</th>
                      <th>Philhealth</th>
                      <th>Pag Ibig</th>
                      <th>Tax</th>
                      <th>Total</th>
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
<script src = "<?=base_url('assets/js/reports/compensation_reports.js')?>"></script>
