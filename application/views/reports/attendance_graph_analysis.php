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
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Attendance Reports Analysis</li>
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
                  <div class="col-md-5">
                    <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                    <select name="dept" id="dept" class="form-control select2 rq">
                      <option value="">------</option>
                      <?php if($departments->num_rows() > 0):?>
                        <?php foreach($departments->result_array() as $dept):?>
                          <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                        <?php endforeach;?>
                      <?php endif?>
                    </select>
                  </div>

                  <div class="col-md-5">
                    <label for="Employee" class="form-control-label col-form-label-sm">Employee</label>
                    <select name="emp_id" id="emp_id" data-worksched = "" class="form-control select2 rq" disabled>
                      <option value="">------</option>
                    </select>
                  </div>

                  <div class="col-md-2 text-right">
                    <button id = "btnSearchButton" class="btn btn-sm btn-primary">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                    <select name="" id="filter_by" class="form-control" disabled>
                      <option value="this_month">This Month</option>
                      <option value="last_3months">Last 3 Months</option>
                      <option value="last_6months">Last 6 Months</option>
                      <!-- <option value="by_date">Date Issued</option> -->
                      <!-- <option value="by_amount">Amount</option> -->
                    </select>
                  </div>
                  <div class="col-md-9 text-right">
                    <input type="hidden" id = "month" value = "">
                    <input type="hidden" id = "days" value = "">
                    <input type="hidden" id = "lates" value = "">
                    <input type="hidden" id = "undertimes" value = "">
                    <input type="hidden" id = "overbreaks" value = "">
                    <input type="hidden" id = "total_mins" value = "">
                    <button class="btn btn-primary btn-sm" id = "btn_back" style = "display:none;">Back</button>
                    <!-- <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button> -->
                  </div>
                </div>
                <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\highchart\highcharts.js')?>"></script>
<script src = "<?=base_url('assets\js\highchart\export-data.js')?>"></script>
<script src = "<?=base_url('assets\js\highchart\exporting.js')?>"></script>
<script src = "<?=base_url('assets\js\reports\attendance_graph_analysis.js')?>"></script>
