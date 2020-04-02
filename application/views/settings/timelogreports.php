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
  img{
    object-fit: 'contain'
  }

  .time_img:hover{
    cursor: pointer;
    border: 1px solid #72716f ;
  }
</style>
<div class="content-inner" id="pageActive" data-num="13" data-namecollapse="" data-labelname="Time Record">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/time_record/'.$token);?>">Time Record</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Time History</li>
        </ol>
    </div>
    <input type="hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <div class="form-group row">
              <div class="col-md-2">
                <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                <select name="" id="filter_by" class="form-control">
                  <option value="by_date">Date Range</option>
                  <option value="by_id">Employee Id</option>
                  <option value="by_name">Employee Name</option>
                  <!-- <option value="by_worksite">Work Site</option> -->
                </select>
              </div>

              <div class="col-md-6">
                <div id="divEmpID" class = "filter_div" style = "display:none;">
                  <div class="form-group row">
                    <div class="col-md-4">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Employee ID</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div class="col-md-4">
                      <label for="Date" class="form-control-label col-form-label-sm">From</label>
                      <input type="text" class="form-control date_input filter_date_from">
                    </div>

                    <div class="col-md-4">
                      <label for="Date" class="form-control-label col-form-label-sm">To</label>
                      <input type="text" class="form-control date_input filter_date_to">
                    </div>
                  </div>

                </div>

                <div id="divName" class = "filter_div" style = "display:none;">
                  <div class="form-group row">
                    <div class="col-md-4">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div class="col-md-4">
                      <label for="Date" class="form-control-label col-form-label-sm">From</label>
                      <input type="text" class="form-control date_input filter_date_from">
                    </div>

                    <div class="col-md-4">
                      <label for="Date" class="form-control-label col-form-label-sm">To</label>
                      <input type="text" class="form-control date_input filter_date_to">
                    </div>
                  </div>

                </div>

                <div id="divWorksite" class="filter_div" style = "display:none;">
                  <div class="form-group row">
                    <div class="col-md-6">
                      <label for="Department" class="form-control-label col-form-label-sm">Work Site</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($worksite->num_rows() > 0):?>
                          <?php foreach($worksite->result_array() as $wk):?>
                            <option value="<?=$wk['worksiteid']?>"><?=$wk['description']?></option>
                          <?php endforeach;?>
                        <?php endif?>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <input type="text" class="form-control date_input filter_date">
                    </div>
                  </div>

                </div>

                <div id="divDate" class = "filter_div active" >
                  <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                  <div class="row">
                    <div class="col-md-6">
                      <input type="text" id = "date_from" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                      <small class="form-text">From</small>
                    </div>
                    <div class="col-md-6">
                      <input type="text" id = "date_to" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                      <small class="form-text">To</small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-right">
                <div class="form-group">
                  <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  <button style = "width:140px;" class = "btn btn-primary" id = "btn_import_modal">Import Excel</button>
                  <!-- <a href="<?=base_url('time_record/Timelogreports/export_to_excel/'.$token)?>" class="btn btn-sm btn-primary" style = "width:140px;">Export to Excel</a> -->
                  <button class="btn btn-primary" id = "btn_export_excel" style = "width:140px;">Export to Excel</button>
                </div>
                <div class="form-group">
                </div>
              </div>
            </div>
            <!-- <div class="row">
              <div class="col-lg-3">
                <label for="" class="form-control-label col-form-label-sm">Date Range:</label>
                <input type="text" id = "time_log_filter_date" class = "form-control time_log_filter_date">
                <input type="hidden" id = "time_log_filter_val">
              </div>
              <div class="col-lg-9 text-right">
              </div>
            </div> -->
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id = "time_log_reports_tbl" class="table table-striped table-bordered text-center" style = "border-top:1px solid gainsboro;">
                <thead>
                  <th>Image</th>
                  <th>Id Number</th>
                  <th>Name</th>
                  <th>Work Site</th>
                  <th>Date</th>
                  <th>Time in</th>
                  <th>Time out</th>
                  <th>Action</th>
                  <!-- <th>Remarks</th> -->
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- VIEW IMAGE MODAL -->
    <div class="modal fade" id = "view_image_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="img-thumbnail view_image" style = "height:350px;width:100%;">

            </div>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- EXPORT MODAL -->
    <div class="modal fade" id = "export_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Export Excel</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form action="" id="export_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6 mb-2">
                <label for="Date From" class="form-control-label col-form-label-sm">Date From:</label>
                <!-- <small class="form-text">Date From <span class="asterisk"></span></small> -->
                <input type="text" name = "export_from_date" id = "export_from_date" class="form-control export_rq date_input">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Date To:" class="form-control-label col-form-label-sm">Date To:</label>
                <!-- <small class="form-text">Date To <span class="asterisk"></span></small> -->
                <input type="text" name = "export_to_date" id = "export_to_date" class="form-control export_rq date_input">
              </div>
              <div class="col-md-12 mb-2">
                <label for="File Type:" class="form-control-label col-form-label-sm">File Type:</label>
                <select name="export_type" id="export_type" class="form-control">
                  <option value="xlsx">.xlsx</option>
                  <option value="xls">.xls</option>
                </select>
              </div>
              <div class="col-12 mb-2">
                <label for="Employee Id:" class="form-control-label col-form-label-sm">Employee Id:</label>
                <input type="text" name = "export_emp_id" id = "export_emp_id" class="form-control">
                <small class="form-text text-danger">Note: (Fill this up if you want to export the timelog of specific user. Otherwise you can leave it empty)</small>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary">Export</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- IMPORT MODAL -->
    <div class="modal fade" id = "import_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Import Excel</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form action="" id="import_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="Work Site: " class="form-control-label col-form-label-sm">Work Site: <span class="asterisk"></span></label>
                  <select name="import_worksite" id="import_worksite" class="form-control">
                    <option value="">------</option>
                    <?php if($worksite->num_rows() > 0):?>
                      <?php foreach($worksite->result_array() as $w):?>
                        <option value="<?=$w['worksiteid']?>" data-lat = "<?=$w['loc_latitude']?>" data-lng = "<?=$w['loc_longitude']?>">
                          <?=$w['description']?>
                        </option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <label for="Select Excel File" class="form-control-label col-form-label-sm">Select Excel File <span class="asterisk"></span></label>
                  <input type="file" name = "import_excel" id = "import_excel" class="form-control" accept = ".xls, .xlsx" required>
                </div>
                <div class="col-md-12 text-right" >
                  <u id = "sample_import_format" style = "cursor:pointer;font-size:10px !important;">Sample excel import format</u>
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary">Import</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- EDIT MODAL -->
    <div class="modal fade" id = "edit_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Timelog</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "update_timelog_form">
          <div class="modal-body">
            <div class="form-group row">
              <label for="Time In" class = "col-md-3 col-form-label mb-2">Time In</label>
              <div class="col-md-9 mb-2">
                <input type="time" id = "time_in" name = "time_in" class = "form-control">
                <input type="hidden" id = "current_timein" name = "current_timein">
                <input type="hidden" id = "uid" name = "uid">
                <input type="hidden" id = "status" name = "status">
                <input type="hidden" id = "emp_id" name = "emp_id">
                <input type="hidden" id = "date" name = "date">
              </div>
              <label for="Time Out" class = "col-md-3 col-form-label mb-2">Time Out</label>
              <div class="col-md-9 mb-2">
                <input type="time" id = "time_out" name = "time_out" class = "form-control">
                <input type="hidden" id = "current_timeout" name = "current_timeout">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary">Save</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- DELETE MODAL -->
    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "del_form">
          <div class="modal-body">
            <h4>Are you sure you want to delete the timelog of (<span id = "del_name"></span>)</h4>
            <input type="hidden" id = "delid" name = "delid">
            <input type="hidden" id = "del_status" name = "del_status">
            <input type="hidden" id = "del_emp_id" name = "del_emp_id">
            <input type="hidden" id = "del_date" name = "del_date">
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary">Yes</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- SAMPLE IMPORT FORMAT MODAL -->
    <div class="modal fade" id = "sample_import_format_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Sample Excel Import Format</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-bordered text-center">
                    <thead>
                      <th>Bio Id</th>
                      <th>Employee Name</th>
                      <th>Date</th>
                      <th>On Duty</th>
                      <th>Off Duty</th>
                      <th>Clock In</th>
                      <th>Clock Out</th>
                      <th>No C/Out</th>
                      <th>No C/Out</th>
                      <th>Late</th>
                      <th>Early</th>
                      <th>Absent</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1001</td>
                        <td>John Doe</td>
                        <td>30/01/2020</td>
                        <td>08:00</td>
                        <td>17:00</td>
                        <td>08:30</td>
                        <td>18:30</td>
                        <td></td>
                        <td></td>
                        <td>00:33</td>
                        <td>00:10</td>
                        <td>False</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script src="<?=base_url('assets/js/time_record/timelogreports.js');?>"></script>
