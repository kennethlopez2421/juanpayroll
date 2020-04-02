<!--
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->
<style>
  .badge{
    /* width: 120px; */
  }
  th, td{
    vertical-align: middle !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="21" data-namecollapse="" data-labelname="Attendance">
  <div class="bc-icons-2 card mb-4">

    <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
      <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
      <li class="breadcrumb-item active">Leave</li>
    </ol>

  </div>
  <input type = "hidden" id = "token" value = "<?=$token?>">
  <input type="hidden" id = "emp_idno" value = "<?=en_dec('en',$this->session->userdata('emp_idno'))?>">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <div class="form-group row mb-4">
              <div class="col-md-3">
                <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                <select name="" id="filter_by" class="form-control">
                  <option value="by_date">Date of Filling</option>
                </select>
              </div>

              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-6">
                    <label for="From" class="form-control-label col-form-label-sm">From</label>
                    <input type="text" id = "date_from" class="form-control date_input_empty" placeholder="Ex. yyyy-mm-dd">
                  </div>
                  <div class="col-md-6">
                    <label for="To" class="form-control-label col-form-label-sm">To</label>
                    <input type="text" id = "date_to" class="form-control date_input_empty" placeholder="Ex. yyyy-mm-dd">
                  </div>
                </div>
              </div>
              <div class="col-md-3 text-right">
                <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                <button class="btn btn-primary btn-sm" id = "btn_add_modal">Add</button>
              </div>
            </div>
          </div>

          <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active" data-stype = "waiting" data-toggle="tab" id = "leave_waiting_nav" href="#leave_waiting_tab" style="color:black;">Waiting for Approval</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-stype = "approved" data-toggle="tab" id = "leave_approved_nav" href="#leave_approved_tab" style="color:black;" >Approved</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-stype = "certified" data-toggle="tab" id = "leave_certified_nav" href="#leave_certified_tab" style="color:black;" >Certified</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-stype = "rejected" data-toggle="tab" id = "leave_rejected_nav" href="#leave_rejected_tab" style="color:black;" >Rejected</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id = "leave_waiting_tab">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered text-center" id="caTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
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
            <div class="tab-pane fade" id = "leave_rejected_tab">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered text-center" id="leave_rejected_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                    <thead>
                      <tr>
                        <th width = "100">ID</th>
                        <th width = "100">Name</th>
                        <th width="80">Leave Type</th>
                        <th width="90">From</th>
                        <th width="90">To</th>
                        <th width="90">Date of Filling</th>
                        <th width="60">Days</th>
                        <th width="60">Status</th>
                        <th width="200">Reason</th>
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
  <!-- Add Modal -->
  <div class="modal fade" id = "add_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Leave Form</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-md-12 mb-2">
              <label for="Paid" class="form-control-label col-form-label-sm">Paid <span class="asterisk"></span></label>
              <select name="paid" id="paid" class="form-control">
                <option value="with_pay">With Pay</option>
                <option value="without_pay">Without Pay</option>
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label for="Leave Type" class="form-control-label col-form-label-sm">Leave Type <span class="asterisk"></span></label>
              <select name="" id="leave_type" class="form-control rq">
                <option value="">------</option>
                <?php if($leave_type->num_rows() > 0):?>
                  <?php foreach($leave_type->result_array() as $leave):?>
                    <option value="<?=$leave['leave_id']?>"><?=$leave['leave_type']?></option>
                  <?php endforeach;?>
                <?php endif;?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="Remaining Leave" class="form-control-label col-form-label-sm">Remaining Leave <span class="asterisk"></span></label>
              <input type="text" id = "remaining_leave" class="contactNumber form-control text-right rq" placeholder = "0" readonly>
            </div>

            <div class="col-md-6 mb-2">
              <label for="From" class="form-control-label col-form-label-sm">From <span class="asterisk"></span></label>
              <input type="text" class="form-control leave_date datepicker-after rq" id="date_from2">
            </div>

            <div class="col-md-6 mb-2">
              <label for="To" class="form-control-label col-form-label-sm">To <span class="asterisk"></span></label>
              <input type="text" class="form-control leave_date datepicker-after rq" id="date_to2">
            </div>

            <div class="col-md-12 mb-2">
              <label for="Reason" class="form-control-label col-form-label-sm">Reason <span class="asterisk"></span></label>
              <textarea name="reason" id = "reason" rows="5" cols="3" class = "form-control rq"></textarea>
            </div>

            <div class="col-md-12 mb-2">
              <label for="Contact Number During Leave" class="form-control-label col-form-label-sm">Contact Number During Leave <span class="asterisk"></span></label>
              <input type="text" id = "contact" class="form-control contactNumber rq">
            </div>

          </div>
        </div>
        <div class="modal-footer text-right">
          <button class="btn btn-sm btn-primary" id = "btn_save_leave_form">Save</button>
          <button class="btn blue-grey" data-dismiss = "modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Modal -->
  <div class="modal fade" id = "update_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Leave</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12 mb-2">
                <label for="Paid" class="form-control-label col-form-label-sm">Paid <span class="asterisk"></span></label>
                <select name="edit_paid" id="edit_paid" class="form-control">
                  <option value="with_pay">With Pay</option>
                  <option value="without_pay">Without Pay</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label for="Leave Type" class="form-control-label col-form-label-sm">Leave Type <span class="asterisk"></span></label>
                <select name="" id="edit_leave_type" class="form-control rq2">
                  <option value="">------</option>
                  <?php if($leave_type->num_rows() > 0):?>
                    <?php foreach($leave_type->result_array() as $leave):?>
                      <option value="<?=$leave['leave_id']?>"><?=$leave['leave_type']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>

              <div class="col-md-6">
                <label for="Remaining Leave" class="form-control-label col-form-label-sm">Remaining Leave <span class="asterisk"></span></label>
                <input type="text" id = "edit_remaining_leave" class="contactNumber form-control text-right rq2" placeholder = "0" readonly>
                <input type="hidden" id = "uid">
              </div>

              <div class="col-md-6 mb-2">
                <label for="From" class="form-control-label col-form-label-sm">From <span class="asterisk"></span></label>
                <input type="text" class="form-control edit_leave_date datepicker-after_empty rq2" id="edit_date_from2">
              </div>

              <div class="col-md-6 mb-2">
                <label for="To" class="form-control-label col-form-label-sm">To <span class="asterisk"></span></label>
                <input type="text" class="form-control edit_leave_date datepicker-after_empty rq2" id="edit_date_to2">
              </div>

              <div class="col-md-12 mb-2">
                <label for="Reason" class="form-control-label col-form-label-sm">Reason <span class="asterisk"></span></label>
                <textarea name="reason" id = "edit_reason" rows="5" cols="3" class = "form-control rq2"></textarea>
              </div>

              <div class="col-md-12 mb-2">
                <label for="Contact Number During Leave" class="form-control-label col-form-label-sm">Contact Number During Leave <span class="asterisk"></span></label>
                <input type="text" id = "edit_contact" class="form-control contactNumber rq2">
              </div>

            </div>
          </div>
        </div>
        <div class="modal-footer text-right">
          <button class="btn btn-sm btn-primary" id = "btn_update_leave">Update</button>
          <button class="btn blue-grey" data-dismiss = "modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Modal  -->
  <div class="modal fade" id = "delete_modal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Delete Leave</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="col-lg-12">
              <p>Are you sure you want to delete this record ?</p>
              <input type="hidden" class="delid">
          </div>
        </div>
        <div class="modal-footer text-right">
          <button class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
          <button class="btn blue-grey" data-dismiss = "modal">No</button>
        </div>
      </div>
    </div>
  </div>

<?php $this->load->view('includes/footer'); ?>
<script src = "<?=base_url('assets\js\employee_leave\employee_leave.js')?>"></script>
