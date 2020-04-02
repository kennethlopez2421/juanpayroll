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
<link rel="stylesheet" href="<?=base_url('assets/css/custom_checkbox.css')?>">
<link rel="stylesheet" href="<?=base_url('assets\css\notification\sweetalert2.min.css')?>">
<style>
  .btn_batch{
    width: 153px;
  }
  th, td{
    vertical-align: middle !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Offset</li>
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
                      <option value="by_name">Employee Name</option>
                      <option value="by_dept">Department</option>
                      <option value="by_date_filed">Date Filed</option>
                      <option value="by_date_rendered">Date Rendered</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divEmpID" class = "filter_div single_search active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Employee ID</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divName" class = "filter_div single_search" style = "display:none;">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divDept" class="filter_div single_search" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($department->num_rows() > 0):?>
                          <?php foreach($department->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divDateFiled" class = "filter_div dual_search" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                    <div id="divDateRendered" class = "filter_div dual_search" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
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
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id = "waiting_nav" data-toggle="tab" data-stype = "waiting" href="#waiting-tab" style="color:black;">Waiting for Approval</a>
                    </li>
                    <?php if((approve_access($access_ids) && $this->session->login_type == 'admin') || $this->session->login_type != 'admin'):?>
                      <li class="nav-item">
                        <a class="nav-link" id = "approve_nav" data-toggle="tab" data-stype = "approved" href="#approved-tab" style="color:black;" >Approved</a>
                      </li>
                    <?php endif;?>
                    <?php if((certify_access($access_ids) && $this->session->login_type == 'admin') || $this->session->login_type != 'admin'):?>
                      <li class="nav-item">
                        <a class="nav-link" id = "certified_nav" data-toggle="tab" data-stype = "certified" href="#certified-tab" style="color:black;" >Certified</a>
                      </li>
                    <?php endif;?>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id = "waiting-tab">
                    <div class="table-responsive mt-3">
                      <table class="table table-bordered text-center" id = "offset_tbl">
                        <thead>
                          <th width = "50">
                            <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                              <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                              <span class="checkmark"></span>
                            </label>
                          </th>
                          <th>Employee ID</th>
                          <th>Employee Name</th>
                          <th>Employee Department</th>
                          <th>Date Filed</th>
                          <th>Date to Offset</th>
                          <th>Offset Type</th>
                          <th>Status</th>
                          <th width = "180">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane fade" id = "approved-tab">
                    <div class="table-responsive mt-3">
                      <table class="table table-bordered text-center" id = "offset_approved_tbl">
                        <thead>
                          <th width = "50">
                            <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                              <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                              <span class="checkmark"></span>
                            </label>
                          </th>
                          <th>Employee ID</th>
                          <th>Employee Name</th>
                          <th>Employee Department</th>
                          <th>Date Filed</th>
                          <th>Date to Offset</th>
                          <th>Offset Type</th>
                          <th>Status</th>
                          <th width = "180">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane fade" id = "certified-tab">
                    <div class="table-responsive mt-3">
                      <table class="table table-bordered text-center" id = "offset_certified_tbl">
                        <thead>
                          <th>Employee ID</th>
                          <th>Employee Name</th>
                          <th>Employee Department</th>
                          <th>Date Filed</th>
                          <th>Date to Offset</th>
                          <th>Offset Type</th>
                          <th>Status</th>
                          <th width = "180">Action</th>
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
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create New Offset</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="add_offset_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12 mb-2">
                  <label for="Department" class="form-control-label col-form-label-sm">Department: <span class="asterisk"></span></label>
                  <select name="department" id="department" class="form-control select2 rq">
                    <option value="">------</option>
                    <?php if($department->num_rows() > 0):?>
                      <?php foreach($department->result_array() as $dept):?>
                        <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Employee" class="form-control-label col-form-label-sm">Employee: <span class="asterisk"></span></label>
                  <select name="employee" id="employee" class="form-control select2 rq">
                    <option value="">------</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Offset Type" class="form-control-label col-form-label-sm">Offset Type: <span class="asterisk"></span></label>
                  <select name="offset_type" id="offset_type" class="form-control select2 rq">
                    <option value="">------</option>
                    <option value="late">Late</option>
                    <option value="undertime">Undertime</option>
                    <option value="wholeday">Whole Day</option>
                    <option value="halfday">Half Day</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Date Rendered" class="form-control-label col-form-label-sm">Date to Offset: <span class="asterisk"></span></label>
                  <input type="text" name = "date_rendered" id = "date_rendered" class="form-control date_input rq ">
                </div>

                <div class="col-md-6 mb-2">
                  <label for="Offset Balance" class="form-control-label col-form-label-sm">Offset Balance:</label>
                  <input type="text" name = "total_offset_bal" id = "total_offset_bal" class="form-control text-right number-input" value = "0" readonly>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="Offset Balance" class="form-control-label col-form-label-sm">Offset <small>(mins)</small>: <span class="asterisk"></span></label>
                  <input type="text" name = "offset_bal" id = "offset_bal" class="form-control number-input text-right rq" value = "0">
                </div>

              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_save_addform">Save</button>
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
            <h4 class="modal-title">Edit Offset</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_offset_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12 mb-3">
                  <label for="Department" class="form-control-label col-form-label-sm">Department: <span class="asterisk"></span></label>
                  <select name="edit_department" id="edit_department" class="form-control select2 rq2">
                    <option value="">------</option>
                    <?php if($department->num_rows() > 0):?>
                      <?php foreach($department->result_array() as $dept):?>
                        <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                  <input type="hidden" name = "uid" id = "uid" class="">
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Employee" class="form-control-label col-form-label-sm">Employee: <span class="asterisk"></span></label>
                  <select name="edit_employee" id="edit_employee" class="form-control select2 rq2">
                    <option value="">------</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Offset Type" class="form-control-label col-form-label-sm">Offset Type: <span class="asterisk"></span></label>
                  <select name="edit_offset_type" id="edit_offset_type" class="form-control select2 rq2">
                    <option value="">------</option>
                    <option value="late">Late</option>
                    <option value="undertime">Undertime</option>
                    <option value="wholeday">Whole Day</option>
                    <option value="halfday">Half Day</option>
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Date Rendered" class="form-control-label col-form-label-sm">Date to Offset: <span class="asterisk"></span></label>
                  <input type="text" name = "edit_date_rendered" id = "edit_date_rendered" class="form-control date_input_empty rq2 ">
                </div>

                <div class="col-md-6 mb-2">
                  <label for="Offset Balance" class="form-control-label col-form-label-sm">Offset Balance</label>
                  <input type="text" name = "edit_total_offset_bal" id = "edit_total_offset_bal" class="form-control text-right" readonly>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="Offset Balance" class="form-control-label col-form-label-sm">Offset <small>(mins)</small>: <span class="asterisk"></span></label>
                  <input type="text" name = "edit_offset_bal" id = "edit_offset_bal" class="form-control number-input text-right rq2" value = "0">
                </div>

              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" id = "btn_update_offset" class="btn btn-sm btn-primary">Update</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- REJECT MODAL -->
    <div class="modal fade" id = "reject_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Confirmation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="reject_offset_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="Reason" class="form-control-label col-form-label-sm">Reason: <span class="asterisk"></span></label>
                  <textarea name="reject_reason" id="reject_reason" cols="30" rows="5" class="form-control rq3"></textarea>
                  <input type="hidden" name = "delid" id = "delid">
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" id = "btn_reject_offset" class="btn btn-sm btn-primary">Save</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src="<?=base_url('assets\js\notification\sweetalert2.min.js');?>"></script>
<script src = "<?=base_url('assets\js\transactions\offset.js')?>"></script>
