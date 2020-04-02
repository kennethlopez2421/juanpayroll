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
$this->session->set_userdata('content_url', $content_url);
$access_ids;
if(isset($this->session->content_url)){
  $content_id = $this->model->get_url_content_id($this->session->content_url);
  $content_id = ($content_id->num_rows() > 0) ? $content_id->row()->id : 0;
  if(count((array)$this->session->get_position_access->access_func_nav) > 0){
    $access_ids = check_func_access($this->session->get_position_access->access_func_nav,$content_id);
    // $this->access_ids = $content_id;
  }else{
    $access_ids = [];
  }
}
if (in_array($content_url, $url_content_arr) == false){
    header("location:".base_url('Main/logout'));
}
//071318
?>
<!-- <link href='<?=base_url('assets/js/fullcalendar/core/main.min.css')?>' rel='stylesheet' /> -->
<!-- <link href='<?=base_url('assets/js/fullcalendar/daygrid/main.min.css')?>' rel='stylesheet' /> -->
<link rel="stylesheet" href="<?=base_url('assets/css/custom_checkbox.css')?>">
<link rel="stylesheet" href="<?=base_url('assets\css\notification\sweetalert2.min.css')?>">
<style>
  th, td {
    vertical-align: middle !important;
  }

  .btn_batch{
    width: 153px;
  }
</style>
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Work Schedule</li>
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
                      <option value="by_id">Employee Id</option>
                      <option value="by_dept">Department</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divEmpID" class = "filter_div single_search active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Employee ID</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>
                    <div id="divDept" class="filter_div single_search" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_add_modal">Add</button>
                    <?php if((approve_access($access_ids) && $this->session->login_type == 'admin')):?>
                      <button class="btn btn-primary btn_batch btn_batch_approve">Batch Approve</button>
                    <?php endif;?>
                    <?php if((certify_access($access_ids) && $this->session->login_type == 'admin')):?>
                      <button class="btn btn-primary btn_batch btn_batch_certify" style = "display:none;">Batch Certify</button>
                    <?php endif;?>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="nav nav-tabs mt-3">
                    <li class="nav-item">
                        <a class="nav-link active" id = "waiting_nav" data-stype = 'waiting' data-toggle="tab" href="#waiting_tab" style="color:black;">Waiting for Approval</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id = "approved_nav" data-stype = 'approved' data-toggle="tab" href="#approved_tab" style="color:black;" >Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id = "certified_nav" data-stype = 'certified' data-toggle="tab" href="#certified_tab" style="color:black;" >Certified</a>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                  <div class="tab-pane fade show active" id = "waiting_tab">
                    <div class="table-responsive">
                      <table class="table table-bordered" id = "custom_sched_tbl">
                        <thead>
                          <th width = "50">
                            <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                              <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                              <span class="checkmark"></span>
                            </label>
                          </th>
                          <th>Department</th>
                          <th>Employee</th>
                          <th>Start Date</th>
                          <th>End Date</th>
                          <th>Status</th>
                          <th width = "200">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane fade" id = "approved_tab">
                    <div class="table-responsive">
                      <table class="table table-bordered " id = "approved_ws_tbl">
                        <thead>
                          <th width = "50">
                            <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                              <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                              <span class="checkmark"></span>
                            </label>
                          </th>
                          <th>Department</th>
                          <th>Employee</th>
                          <th>Start Date</th>
                          <th>End Date</th>
                          <th>Status</th>
                          <th width = "240">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane fade" id = "certified_tab">
                    <div class="table-responsive">
                      <table class="table table-bordered " id = "certified_ws_tbl">
                        <thead>
                          <th>Department</th>
                          <th>Employee</th>
                          <th>Start Date</th>
                          <th>End Date</th>
                          <th>Status</th>
                          <th>Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ADD MODAL -->
    <div class="modal fade" id = "add_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create Custom Schedule</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="custom_sched_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6 mb-2">
                <label for="Department" class="form-control-label col-form-label-sm">Department <span class="asterisk"></span></label>
                <select name="department" id="department" class="form-control select2 rq">
                  <option value="">------</option>
                  <?php if($departments->num_rows() > 0):?>
                    <?php foreach($departments->result_array() as $dept):?>
                      <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label for="Employee" class="form-control-label col-form-label-sm">Employee <?=($this->session->login_type != 'admin') ? '<span class="asterisk"></span>' : '<small>( optional )</small>'?></label>
                <select name="employee" id="employee" class="form-control select2 <?=($this->session->login_type != 'admin') ? 'rq' : ''?>" disabled>
                  <option value="">------</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label for="Start Date" class="form-control-label col-form-label-sm">Start Date <span class="asterisk"></span></label>
                <input type="text" name = "start_date" id = "start_date"  class="form-control date_input rq">
              </div>
              <div class="col-md-6 mb-3">
                <label for="End Date" class="form-control-label col-form-label-sm">End Date <span class="asterisk"></span></label>
                <input type="text" name = "end_date" id = "end_date"  class="form-control date_input rq">
              </div>
              <div class="col-md-12">
                <label for="Work Schedule" class="form-control-label col-form-label-sm">Work Schedule <span class="asterisk"></span></label>
                <div class="table-responsive">
                  <table class="table table-bordered" style ="border:1px solid gainsboro;">
                    <thead>
                      <th>Day</th>
                      <th>Time In</th>
                      <th>Time Out</th>
                      <th>Break In</th>
                      <th>Break Out</th>
                      <th>Total</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Mon</td>
                        <td><input type="time" id = "mon_ti" name = "mon_ti" class="form-control in init"></td>
                        <td><input type="time" id = "mon_to" name = "mon_to" class="form-control out init"></td>
                        <td><input type="time" id = "mon_bi" name = "mon_bi" class="form-control bi init"></td>
                        <td><input type="time" id = "mon_bo" name = "mon_bo" class="form-control bout init"></td>
                        <td><input type="text" id = "mon_total" name = "mon_total" class="form-control total" readonly></td>
                      </tr>
                      <tr>
                        <td>Tue</td>
                        <td><input type="time" id = "tue_ti" name = "tue_ti" class="form-control in"></td>
                        <td><input type="time" id = "tue_to" name = "tue_to" class="form-control out"></td>
                        <td><input type="time" id = "tue_bi" name = "tue_bi" class="form-control bi"></td>
                        <td><input type="time" id = "tue_bo" name = "tue_bo" class="form-control bout"></td>
                        <td><input type="text" id = "tue_total" name = "tue_total" class="form-control total" readonly></td>
                      </tr>
                      <tr>
                        <td>Wed</td>
                        <td><input type="time" id = "wed_ti" name = "wed_ti" class="form-control in"></td>
                        <td><input type="time" id = "wed_to" name = "wed_to" class="form-control out"></td>
                        <td><input type="time" id = "wed_bi" name = "wed_bi" class="form-control bi"></td>
                        <td><input type="time" id = "wed_bo" name = "wed_bo" class="form-control bout"></td>
                        <td><input type="text" id = "wed_total" name = "wed_total" class="form-control total" readonly></td>
                      </tr>
                      <tr>
                        <td>Thu</td>
                        <td><input type="time" id = "thu_ti" name = "thu_ti" class="form-control in"></td>
                        <td><input type="time" id = "thu_to" name = "thu_to" class="form-control out"></td>
                        <td><input type="time" id = "thu_bi" name = "thu_bi" class="form-control bi"></td>
                        <td><input type="time" id = "thu_bo" name = "thu_bo" class="form-control bout"></td>
                        <td><input type="text" id = "thu_total" name = "thu_total" class="form-control total" readonly></td>
                      </tr>
                      <tr>
                        <td>Fri</td>
                        <td><input type="time" id = "fri_ti" name = "fri_ti" class="form-control in"></td>
                        <td><input type="time" id = "fri_to" name = "fri_to" class="form-control out"></td>
                        <td><input type="time" id = "fri_bi" name = "fri_bi" class="form-control bi"></td>
                        <td><input type="time" id = "fri_bo" name = "fri_bo" class="form-control bout"></td>
                        <td><input type="text" id = "fri_total" name = "fri_total" class="form-control total" readonly></td>
                      </tr>
                      <tr>
                        <td>Sat</td>
                        <td><input type="time" id = "sat_ti" name = "sat_ti" class="form-control in"></td>
                        <td><input type="time" id = "sat_to" name = "sat_to" class="form-control out"></td>
                        <td><input type="time" id = "sat_bi" name = "sat_bi" class="form-control bi"></td>
                        <td><input type="time" id = "sat_bo" name = "sat_bo" class="form-control bout"></td>
                        <td><input type="text" id = "sat_total" name = "sat_total" class="form-control total" readonly></td>
                      </tr>
                      <tr>
                        <td>Sun</td>
                        <td><input type="time" id = "sun_ti" name = "sun_ti" class="form-control in"></td>
                        <td><input type="time" id = "sun_to" name = "sun_to" class="form-control out"></td>
                        <td><input type="time" id = "sun_bi" name = "sun_bi" class="form-control bi"></td>
                        <td><input type="time" id = "sun_bo" name = "sun_bo" class="form-control bout"></td>
                        <td><input type="text" id = "sun_total" name = "sun_total" class="form-control total" readonly></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary" id = "btn_save_custom_sched">Save</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- UPDATE MODAL -->
    <div class="modal fade" id = "update_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Custom Schedule</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="update_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6 mb-2">
                <label for="Department" class="form-control-label col-form-label-sm">Department <span class="asterisk"></span></label>
                <select name="edit_department" id="edit_department" class="form-control select2 rq2">
                  <option value="">------</option>
                  <?php if($departments->num_rows() > 0):?>
                    <?php foreach($departments->result_array() as $dept):?>
                      <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
                <input type="hidden" id = "uid" name = "uid">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Employee" class="form-control-label col-form-label-sm">Employee <?=($this->session->login_type != 'admin') ? '<span class="asterisk"></span>' : '<small>( optional )</small>'?></label>
                <select name="edit_employee" id="edit_employee" class="form-control select2 <?=($this->session->login_type != 'admin') ? 'rq2' : ''?>" disabled>
                  <option value="">------</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label for="Start Date" class="form-control-label col-form-label-sm">Start Date <span class="asterisk"></span></label>
                <input type="text" name = "edit_start_date" id = "edit_start_date"  class="form-control date_input rq2">
              </div>
              <div class="col-md-6 mb-3">
                <label for="End Date" class="form-control-label col-form-label-sm">End Date <span class="asterisk"></span></label>
                <input type="text" name = "edit_end_date" id = "edit_end_date"  class="form-control date_input rq2">
              </div>
              <div class="col-md-12">
                <label for="Work Schedule" class="form-control-label col-form-label-sm">Work Schedule <span class="asterisk"></span></label>
                <div class="table-responsive">
                  <table class="table table-bordered" style ="border:1px solid gainsboro;">
                    <thead>
                      <th>Day</th>
                      <th>Time In</th>
                      <th>Time Out</th>
                      <th>Break In</th>
                      <th>Break Out</th>
                      <th>Total</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Mon</td>
                        <td><input type="time" id = "edit_mon_ti" name = "edit_mon_ti" class="form-control edit_in edit_init"></td>
                        <td><input type="time" id = "edit_mon_to" name = "edit_mon_to" class="form-control edit_out edit_init"></td>
                        <td><input type="time" id = "edit_mon_bi" name = "edit_mon_bi" class="form-control edit_bi edit_init"></td>
                        <td><input type="time" id = "edit_mon_bo" name = "edit_mon_bo" class="form-control edit_bout edit_init"></td>
                        <td><input type="text" id = "edit_mon_total" name = "edit_mon_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Tue</td>
                        <td><input type="time" id = "edit_tue_ti" name = "edit_tue_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_tue_to" name = "edit_tue_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_tue_bi" name = "edit_tue_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_tue_bo" name = "edit_tue_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_tue_total" name = "edit_tue_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Wed</td>
                        <td><input type="time" id = "edit_wed_ti" name = "edit_wed_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_wed_to" name = "edit_wed_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_wed_bi" name = "edit_wed_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_wed_bo" name = "edit_wed_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_wed_total" name = "edit_wed_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Thu</td>
                        <td><input type="time" id = "edit_thu_ti" name = "edit_thu_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_thu_to" name = "edit_thu_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_thu_bi" name = "edit_thu_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_thu_bo" name = "edit_thu_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_thu_total" name = "edit_thu_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Fri</td>
                        <td><input type="time" id = "edit_fri_ti" name = "edit_fri_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_fri_to" name = "edit_fri_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_fri_bi" name = "edit_fri_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_fri_bo" name = "edit_fri_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_fri_total" name = "edit_fri_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Sat</td>
                        <td><input type="time" id = "edit_sat_ti" name = "edit_sat_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_sat_to" name = "edit_sat_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_sat_bi" name = "edit_sat_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_sat_bo" name = "edit_sat_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_sat_total" name = "edit_sat_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Sun</td>
                        <td><input type="time" id = "edit_sun_ti" name = "edit_sun_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_sun_to" name = "edit_sun_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_sun_bi" name = "edit_sun_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_sun_bo" name = "edit_sun_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_sun_total" name = "edit_sun_total" class="form-control edit_total" readonly></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" id = "btn_update" class="btn btn-sm btn-primary">Update</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- CONFIRM MODAL -->
    <div class="modal fade" id = "confirm_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Confirmation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Do you want to set all schedule like this ?</p>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_confirm_sched">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- DELETE MODAL -->
    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Confirmation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record ?</p>
                <input type="hidden" class="ws_id">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_del_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- REJECT MODAL -->
    <div class="modal fade" id = "reject_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Reject Work Schedule</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="reject_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-12">
                <label for="Reason" class="form-control-label col-form-label-sm">Reason: <span class="asterisk"></span></label>
                <textarea name="reject_reason" id="reject_reason" cols="30" rows="4" class="form-control reject_rq"></textarea>
                <input type="hidden" name="reject_id" id = "reject_id" value="">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button id = "btn_reject_save" type = "submit" class="btn btn-sm btn-primary">Save</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src="<?=base_url('assets\js\notification\sweetalert2.min.js');?>"></script>
<!-- <script src="<?=base_url('assets/js/fullcalendar/core/main.js')?>"></script> -->
<!-- <script src="<?=base_url('assets/js/fullcalendar/daygrid/main.js')?>"></script> -->
<!-- <script src="<?=base_url('assets/js/fullcalendar/interaction/main.js')?>"></script> -->
<script src = "<?=base_url('assets\js\transactions\work_schedule.js')?>"></script>
