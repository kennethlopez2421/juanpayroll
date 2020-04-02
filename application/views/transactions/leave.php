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
  th, td{
    vertical-align: middle !important;
  }
  .btn_batch{
    width: 153px;
  }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Leave</li>
        </ol>
    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
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
                                <option value="by_leave">Leave Type</option>
                                <option value="by_date">Date</option>
                                <option value="by_date_filed">Date Filed</option>
                                <!-- <option value="by_amount">Number of Days</option> -->
                              </select>
                            </div>

                            <div class="col-md-6">
                              <div id="divEmpID" class = "filter_div active">
                                <label for="Employee ID" class="form-control-label col-form-label-sm">Employee ID</label>
                                <input type="text" class="form-control searchArea" value = "">
                              </div>

                              <div id="divName" class = "filter_div" style = "display:none;">
                                <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                                <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                              </div>

                              <div id="divDept" class="filter_div" style = "display:none;">
                                <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                                <select class = "form-control searchArea">
                                  <?php if($department->num_rows() > 0):?>
                                    <?php foreach($department->result() as $dept):?>
                                      <option value="<?=$dept->departmentid?>"><?=$dept->description?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                              </div>

                              <div id="divLeaveType" class="filter_div" style = "display:none;">
                                <label for="Leave Type" class="form-control-label col-form-label-sm">Leave Type</label>
                                <select class = "form-control searchArea">
                                  <?php if($leaves->num_rows() > 0):?>
                                    <?php foreach($leaves->result() as $leave):?>
                                      <option value="<?=$leave->leaveid?>"><?=$leave->description?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                              </div>

                              <div id="divDate" class = "filter_div" style = "display:none;">
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

                              <div id="divDateFiled" class = "filter_div" style = "display:none;">
                                <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                                <div class="row">
                                  <div class="col-md-6">
                                    <input type="text" id = "date_from2" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                                    <small class="form-text">From</small>
                                  </div>
                                  <div class="col-md-6">
                                    <input type="text" id = "date_to2" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                                    <small class="form-text">To</small>
                                  </div>
                                </div>
                              </div>

                              <div id="divDays" class = "filter_div" style = "display:none;">
                                <label for="Amount" class="form-control-label col-form-label-sm">Amount</label>
                                <div class="row">
                                  <div class="col-md-6">
                                    <input type="number" id = "num_days_from" class="form-control">
                                    <small class="form-text">From</small>
                                  </div>

                                  <div class="col-md-6">
                                    <input type="text" id = "num_days_to" class="form-control">
                                    <small class="form-text">To</small>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-3 text-right">
                              <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                              <a href="<?= base_url('transactions/Leave/add/'.$token) ?>"><button class="btn btn-primary btnClickAddArea">Add</button></a>
                              <?php if((approve_access($access_ids) && $this->session->login_type == 'admin')):?>
                                <button class="btn btn-primary btn_batch btn_batch_approve">Batch Approve</button>
                              <?php endif;?>
                              <?php if((certify_access($access_ids) && $this->session->login_type == 'admin')):?>
                                <button class="btn btn-primary btn_batch btn_batch_certify" style = "display:none;">Batch Certify</button>
                              <?php endif;?>
                            </div>
                          </div>
                          <!-- <div class="form-group mx-sm-3 mb-2">
                              <label class = "form-control-label col-form-label-sm active">Search by Employee ID:</label>
                              <input type="text" class="form-control" autocomplete="off" placeholder="Search Here" id = "caTableTB">
                          </div> -->

                        </div>
                        <ul class="nav nav-tabs mt-3">
                            <li class="nav-item">
                                <a class="nav-link active" data-stype = "waiting" data-toggle="tab" id = "leave_waiting_nav" href="#leave_waiting_tab" style="color:black;">Waiting for Approval</a>
                            </li>
                            <?php if(approve_access($access_ids)):?>
                              <li class="nav-item">
                                  <a class="nav-link" data-stype = "approved" data-toggle="tab" id = "leave_approved_nav" href="#leave_approved_tab" style="color:black;" >Approved</a>
                              </li>
                            <?php endif;?>
                            <?php if(certify_access($access_ids)):?>
                              <li class="nav-item">
                                  <a class="nav-link" data-stype = "certified" data-toggle="tab" id = "leave_certified_nav" href="#leave_certified_tab" style="color:black;" >Certified</a>
                              </li>
                            <?php endif;?>
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane fade show active" id = "leave_waiting_tab">
                            <div class="card-body">
                              <div class="table-responsive">
                                <table class="table table-bordered text-center" id="caTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                  <thead>
                                    <tr>
                                      <th width = "50">
                                        <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                                          <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                                          <span class="checkmark"></span>
                                        </label>
                                      </th>
                                      <th>ID</th>
                                      <th>Name</th>
                                      <th width="100">Leave Type</th>
                                      <th width="90">From</th>
                                      <th width="90">To</th>
                                      <th width="90">Date of Filling</th>
                                      <th>Days</th>
                                      <th>Status</th>
                                      <th width="190">Action</th>
                                    </tr>
                                  </thead>
                                </table>
                              </div>
                            </div>
                          </div>
                          <div class="tab-pane fade" id = "leave_approved_tab">
                            <div class="card-body">
                              <div class="table-responsive">
                                <table class="table table-bordered text-center" id="leave_approved_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                  <thead>
                                    <tr>
                                      <th width = "50">
                                        <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                                          <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                                          <span class="checkmark"></span>
                                        </label>
                                      </th>
                                      <th>ID</th>
                                      <th>Name</th>
                                      <th width="100">Leave Type</th>
                                      <th width="90">From</th>
                                      <th width="90">To</th>
                                      <th width="90">Date of Filling</th>
                                      <th>Days</th>
                                      <th>Status</th>
                                      <th width="190">Action</th>
                                    </tr>
                                  </thead>
                                </table>
                              </div>
                            </div>
                          </div>
                          <div class="tab-pane fade" id = "leave_certified_tab">
                            <div class="card-body">
                              <div class="table-responsive">
                                <table class="table table-bordered text-center" id="leave_certified_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                  <thead>
                                    <tr>
                                      <th>ID</th>
                                      <th>Name</th>
                                      <th width="100">Leave Type</th>
                                      <th width="90">From</th>
                                      <th width="90">To</th>
                                      <th width="90">Date of Filling</th>
                                      <th>Days</th>
                                      <th>Status</th>
                                      <th width="80">Action</th>
                                    </tr>
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

    <!-- Modal-->

    <div id="delCAModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete Cash Advance</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="deleteCA-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p><span id = "ca_status_prompt_delete" class="text-warning">Are you sure you want to delete this data?</span></p>
                                    <input type="hidden" id="delLEid" class="caid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delLEBtn" style="float:right" class="btn btn-primary delLEBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- reject work order -->
    <div class="modal fade" id = "reject_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Reject Work Order</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12">
                <label for="Reason" class="form-control-label col-form-label-sm">Reason <span class="asterisk"></span></label>
                <textarea name="reject_reason" id="reject_reason" cols="30" rows="4" class="form-control"></textarea>
                <input type="hidden" id = "reject_id">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_reject_yes">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets\js\notification\sweetalert2.min.js');?>"></script>
<script src="<?=base_url('assets/js/transactions/leave.js');?>"></script>
