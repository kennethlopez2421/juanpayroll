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
  $this->session->set_userdata('content_name', $content_id->row()->cn_name);
  $content_id = ($content_id->num_rows() > 0) ? $content_id->row()->id : 0;
  $this->session->set_userdata('content_id', $content_id);
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
            <li class="breadcrumb-item active">Work Order</li>
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
                      <option value="by_date">Date</option>
                      <option value="by_date_filed">Date Filed</option>
                      <!-- <option value="by_amount">Amount</option> -->
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

                    <div id="divDateRendered" class = "filter_div" style = "display:none;">
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

                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <a href="<?= base_url('transactions/Workorder/add/'.$token) ?>"><button class="btn btn-primary btnClickAddArea">Add</button></a>
                    <?php if((approve_access($access_ids) && $this->session->login_type == 'admin')):?>
                      <button class="btn btn-primary btn_batch btn_batch_approve">Batch Approve</button>
                    <?php endif;?>
                    <?php if((certify_access($access_ids) && $this->session->login_type == 'admin')):?>
                      <button class="btn btn-primary btn_batch btn_batch_certify" style = "display:none;">Batch Certify</button>
                    <?php endif;?>
                  </div>
                </div>
              </div>

              <ul class="nav nav-tabs mt-3">
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" id = "waiting_wo_nav" data-stype = "waiting" href="#waiting_wo" style="color:black;">Waiting for Approval</a>
                  </li>
                  <!-- <?php if($this->session->userdata('isLoggedIn')):?> -->
                    <!-- <?php if(($this->session->userdata('deptId') == hr_id() || $this->session->userdata('deptId') == 0) && $this->session->position_lvl <= hr_sup_or_above()):?> -->
                    <!-- <?php endif;?> -->
                  <!-- <?php endif;?> -->
                  <?php if((approve_access($access_ids) && $this->session->login_type == 'admin') || $this->session->login_type != 'admin'):?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" id = "approved_wo_nav" data-stype = "approved" href="#approved_wo" style="color:black;" >Approved</a>
                    </li>
                  <?php endif;?>
                  <?php if((certify_access($access_ids) && $this->session->login_type == 'admin') || $this->session->login_type != 'admin'):?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" id = "certified_wo_nav" data-stype = "certified" href="#certified_wo" style="color:black;" >Certified</a>
                    </li>
                  <?php endif;?>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade show active" id = "waiting_wo">

                  <div class="card-body">
                    <div class="table-responsive">
                      <table id = "workOrder_tbl" class="table table-bordered text-center" style="width: 100%;">
                        <thead>
                          <th width = "50">
                            <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                              <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                              <span class="checkmark"></span>
                            </label>
                          </th>
                          <th>Employee Idno</th>
                          <th>Emloyee Name</th>
                          <th>Date Filed</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Status</th>
                          <th width = "160">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id = "approved_wo">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id = "workOrder_tbl_for_approval" class="table table-bordered text-center" style = "width:100%;">
                        <thead>
                          <th width = "50">
                            <label class="container_label" style = "margin-bottom:0px !important; font-weight:bold !important;top: -10px;">
                              <input type="checkbox" name = "select_all[]" class = "select select_all" value = "">
                              <span class="checkmark"></span>
                            </label>
                          </th>
                          <th>Employee Idno</th>
                          <th>Employee Name</th>
                          <th>Date Filed</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Status</th>
                          <th width = "160" id = "wo_approve_action">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id = "certified_wo">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id = "workOrder_tbl_for_certification" class="table table-bordered text-center" style = "width:100%;">
                        <thead>
                          <th>Employee Idno</th>
                          <th>Employee Name</th>
                          <th>Date Filed</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Status</th>
                          <th width = "120">Action</th>
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

    <!-- add work order -->
    <div class="modal fade" id="workOrder_modal">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Work Order</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <div class="">
                <div class="row">
                    <div class="col-lg-12">
                        <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                    </div>
                </div>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>

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
    <!-- delete work order -->
    <div id="delWorkOrderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Confirmation</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" class="employeeid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delEmpBtn" style="float:right" class="btn btn-primary deleteAreaBtn">Delete</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('includes/footer');?>
    <script src="<?=base_url('assets\js\notification\sweetalert2.min.js');?>"></script>
    <script src = "<?=base_url('assets/js/transactions/workorder.js')?>"></script>
    <!-- <script src = "<?=base_url('assets/js/transactions/workorder_for_approval.js')?>"></script> -->
    <!-- <script src = "<?=base_url('assets/js/transactions/wordorder_for_certification.js')?>"></script> -->
