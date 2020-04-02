
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a class = "white-text" href = "<?=base_url('transactions/Leave/index/'.$token)?>">Leave</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Add Leave</li>
        </ol>
    </div>
<input type="hidden" id='token' value="<?= $token ?>">
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                      <div class="card-body">

                          <div class="tab-content">
                            <div id="employeeDetails" class="tab-pane fade show active">
                              <br>
                              <div class="container">
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                                      <select name="dept" id="dept" class="form-control select2" style = "height:41px;">
                                        <option value="">------</option>
                                        <?php if($department->num_rows() > 0):?>
                                          <?php foreach($department->result_array() as $dept):?>
                                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                                          <?php endforeach;?>
                                        <?php endif;?>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Employee ID No.<span class = "ml-2 text-danger" >*</span></label>
                                      <select id="employee_id_no" class="form-control select2" style = "width:100%;">
                                        <option value="">------</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                                <div class = "row">

                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">From<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" class = "form-control leave_date<?=($this->session->login_type == 'admin') ? 'date_input' : 'datepicker-after'?>" id="date_from" placeholder="yyyy-mm-dd">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>

                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">To<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" class = "form-control leave_date<?=($this->session->login_type == 'admin') ? 'date_input' : 'datepicker-after'?>" id="date_to" placeholder="yyyy-mm-dd">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>

                                <!--Number of Days, Balance, HRD-->
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Contact number during leave<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="contact_number" class = "form-control contactNumber" autocomplete="off">
                                    </div>
                                  </div>

                                  <div class="col-md-3">
                                    <label for="Leave Type" class="form-control-label col-form-label-sm">Paid <span class="asterisk"></span></label>
                                    <select name="paid" id="paid" class="form-control">
                                      <option value="with_pay">With Pay</option>
                                      <option value="without_pay">Without Pay</option>
                                    </select>
                                  </div>

                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Leave Type<span class = "ml-2 text-danger" >*</span></label>
                                      <select id="leave_type" class="form-control" disabled>
                                        <option value="">------</option>
                                        <?php if($leaves->num_rows() > 0):?>
                                          <?php foreach($leaves->result_array() as $leave):?>
                                            <option value="<?=$leave['leaveid']?>"><?=$leave['description']?></option>
                                          <?php endforeach;?>
                                        <?php endif;?>
                                      </select>
                                    </div>
                                  </div>

                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="Remaining Leave" class="form-control-label col-form-label-sm">Remaining Leave <span class="asterisk"></span></label>
                                      <input type="text" id = "remaining_leave" class="form-control contactNumber text-right" placeholder="0" readonly>
                                    </div>
                                  </div>

                                  <div class="col-md-12">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Comment/Reason<span class = "ml-2 text-danger" >*</span></label>
                                      <textarea name="name" id = "comment" rows="3" cols="80" class = "form-control"></textarea>
                                      <!-- <input type="text" id="comment" class = "form-control" autocomplete="off"> -->
                                    </div>
                                  </div>


                                </div>
                                <div class="row">
                                  <div class="col-md-12 text-right">
                                    <button id="addLEBtn" class="btn btn-primary">Add Leave</button>
                                  </div>
                                </div>
                              </div>
                            </div>

                          </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<!-- <script src = "<?=base_url('assets/js/jquery.alphanum.js')?>"></script> -->
<script src="<?= base_url('assets/js/transactions/leave.js') ?>"></script>
