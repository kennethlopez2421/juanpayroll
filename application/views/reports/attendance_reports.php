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
  .badge-pill{
    min-width: 50px;
  }
</style>
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Attendance Reports</li>
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
                    <div class="form-group row">
                      <div class="col-12 mb-3">
                        <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                        <select name="" id="filter_by" class="form-control">
                          <option value="by_absent">Absent</option>
                          <option value="by_late">Late</option>
                          <option value="by_overtime">Overtime</option>
                          <option value="by_undertime">Undertime</option>
                          <option value="by_halfday">Halfday</option>
                          <option value="by_offday">Off Day Attendance</option>
                          <option value="by_most_absent">Most Absent</option>
                          <option value="by_most_late">Most Late</option>
                          <option value="by_most_overtime">Most Overtime</option>
                          <option value="by_most_undertime">Most Undertime</option>
                        </select>
                      </div>

                      <div class="col-12 mb-3">
                        <select name="" id="filter_by2" class="form-control">
                          <option value="">------</option>
                          <option value="by_id">Employee ID</option>
                          <option value="by_name">Employee Name</option>
                          <option value="by_dept">Department</option>
                          <option value="by_position">Position</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group row">
                      <!-- filter by  -->
                      <div class="col-md-12 mb-3">
                        <div id="divAbsent" class = "filter_div single_date active">
                          <label for="Employee ID" class="form-control-label col-form-label-sm">Date</label>
                          <input type="text" class="datepicker-yesterday form-control searchArea">
                          <!-- <input type="text" class="form-control searchArea" value = ""> -->
                        </div>

                        <div id="divLate" class = "filter_div single_date" style = "display:none;">
                          <label for="Employee Name" class="form-control-label col-form-label-sm">Date</label>
                          <input type="text" class="datepicker-yesterday form-control searchArea">
                        </div>

                        <div id="divOvertime" class="filter_div single_date" style = "display:none;">
                          <label for="Department" class="form-control-label col-form-label-sm">Date</label>
                          <input type="text" class="date_input form-control searchArea">
                        </div>

                        <div id="divUndertime" class = "filter_div single_date" style = "display:none;">
                          <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                          <input type="text" class="datepicker-yesterday form-control searchArea">
                        </div>

                        <div id="divHalfday" class = "filter_div range_date" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="datepicker-yesterday form-control from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="datepicker-yesterday form-control to">
                            </div>
                          </div>
                        </div>

                        <div id="divOffDay" class = "filter_div range_date" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="datepicker-yesterday form-control from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="datepicker-yesterday form-control to">
                            </div>
                          </div>
                        </div>

                        <div id="divMostAbsent" class = "filter_div range_date" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="datepicker-yesterday form-control from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="datepicker-yesterday form-control to">
                            </div>
                          </div>
                        </div>

                        <div id="divMostLate" class = "filter_div range_date" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="datepicker-yesterday form-control from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="datepicker-yesterday form-control to">
                            </div>
                          </div>
                        </div>

                        <div id="divMostOvertime" class = "filter_div range_date" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="datepicker-yesterday form-control from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="datepicker-yesterday form-control to">
                            </div>
                          </div>
                        </div>

                        <div id="divMostUndertime" class = "filter_div range_date" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="datepicker-yesterday form-control from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="datepicker-yesterday form-control to">
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- filter by 2 -->
                      <div class="col-md-12 mb-3">
                        <div id="divEmpty" class="filter_div2 active">
                          <!-- <input type="text" class="form-control" readonly> -->
                        </div>

                        <div id="divID" class = "filter_div2" style = "display:none;">
                          <input type="text" class="form-control searchArea2" placeholder="Ex.1010101">
                        </div>

                        <div id="divName" class = "filter_div2" style = "display:none;">
                          <input type="text" class="form-control searchArea2" placeholder="Ex. John Doe">
                        </div>

                        <div id="divDept" class = "filter_div2 searchArea2" style = "display:none;">
                          <select name="" id="" class="form-control searchArea2 select2">
                            <?php if($departments->num_rows() > 0):?>
                              <?php foreach($departments->result_array() as $dept):?>
                                <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                              <?php endforeach;?>
                            <?php endif?>
                          </select>
                        </div>

                        <div id="divPos" class="filter_div2" style = "display:none;">
                          <select name="" id="" class = "form-control searchArea2 select2">
                            <?php if($positions->num_rows() > 0):?>
                              <?php foreach($positions->result_array() as $pos):?>
                                <option value="<?=$pos['positionid']?>">(<?=$pos['dept']?>) <?=$pos['description']?></option>
                              <?php endforeach;?>
                            <?php endif;?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_export">Export</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive" id = "tbl_ajax">
                  <table class="table table-striped table-bordered" id = "attendance_reports_tbl">
                    <thead id = "tbl_head">
                      <th>Employee ID</th>
                      <th>Employee Name</th>
                      <th>Department</th>
                      <th>Position</th>
                      <th>Absent</th>
                      <th>Minutes</th>
                      <th>Status</th>
                      <!-- <th>Action</th> -->
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id = "view_reports_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Attendance Reports</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-2"><h4>Name:</h4></div>
              <div class="col-md-10"><h4>Marky Neri</h4></div>

              <div class="col-md-2"><h4>Date:</h4></div>
              <div class="col-md-10"><h4>2019-04-25</h4></div>
            </div>

            <div class="form-group">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Date</th>
                  </thead>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "export_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Export Excel</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6">
                <label for="Date From" class="form-control-label col-form-label-sm">Date From:</label>
                <input type="text" class="form-control date_input">
              </div>

              <div class="col-md-6">
                <label for="Date To" class="form-control-label col-form-label-sm">Date To:</label>
                <input type="text" class="form-control date_input">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "offday_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Off Day Breakdown</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-bordered text-center" id = "offday_tbl">
                <thead>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Approved by</th>
                  <th>Certified by</th>
                </thead>
              </table>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/reports/attendance_reports.js')?>"></script>
