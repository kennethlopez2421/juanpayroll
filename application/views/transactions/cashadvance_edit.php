
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a class="white-text" href="<?=base_url('transactions/Cashadvance/index/'.$token);?>">Cash Advance</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Edit Cash Advance</li>
        </ol>
    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
    <input type = "hidden" id = "caID" value="<?=$result->ca_id?>">
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                      <div class="card-body">
<!--
                          <ul class="nav nav-tabs">
                              <li class="nav-item">
                                  <a class="nav-link active" data-toggle="tab" href="#employeeDetails" style="color:black;">Cash Advance Edit</a>
                              </li>
                          </ul> -->

                          <div class="tab-content">
                            <div id="employeeDetails" class="tab-pane fade show active">
                              <br>
                              <div class="container">
                                <div class="form-group row">
                                  <div class="col-12">
                                    <label for="Cash Advance Information" class="mb-3 form-control-label col-form-label-sm">Cash Advance Information</label>
                                    <div class="row">
                                      <div class="col-md-6">
                                        <select name="dept" id="dept" class="form-control select2" style = "height:41px;">
                                          <option value="">------</option>
                                          <?php if($department->num_rows() > 0):?>
                                            <?php foreach($department->result_array() as $dept):?>
                                              <option value="<?=$dept['departmentid']?>" <?=($dept['departmentid'] == $result->deptId) ? "SELECTED": ""?>>
                                                <?=$dept['description']?>
                                              </option>
                                            <?php endforeach;?>
                                          <?php endif;?>
                                        </select>
                                        <small class="form-text">Deparment</small>
                                      </div>
                                      <div class="col-md-6 mb-3">
                                        <select id="ca_emp_id" class="form-control select2">
                                          <?php if($employee->num_rows() > 0):?>
                                            <?php foreach($employee->result_array() as $emp):?>
                                              <option value="<?=$emp['employee_idno']?>" <?=($emp['employee_idno'] == $result->employee_id)? "SELECTED" : ""?>>
                                                <?=$emp['fullname']?> (<?=$emp['employee_idno']?>)
                                              </option>
                                            <?php endforeach;?>
                                          <?php endif;?>
                                        </select>
                                        <small class="form-text">Employee <span class="asterisk"></span></small>
                                      </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <input type="text" id = "ca_dof" class="form-control date_input_empty rq" value = "<?=$result->date_of_file?>">
                                        <small class="form-text">Date of File <span class="asterisk"></span></small>
                                      </div>
                                      <div class="col-md-6 mb-3">
                                        <input type="text" id = "ca_doe" class="form-control date_input_empty rq" value = "<?=$result->date_of_effectivity?>">
                                        <small class="form-text">Date of Effectivity <span class="asterisk"></span></small>
                                      </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-md-12 mb-3">
                                        <textarea name="" id="ca_reason" cols="30" rows="5" class="form-control rq"><?php echo $result->reason;?></textarea>
                                        <small class="form-text">Reason <span class="asterisk"></span></small>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <div class="form-group row">
                                  <div class="col-12">
                                    <label for="Payment Scheme" class="mb-3 form-control-label col-form-label-sm">Payment Scheme</label>
                                    <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <input type="number" id = "ca_max_loan" data-max = "<?=$per_loan?>" value = "<?=(float)$result->amount + (float)$result->rate?>" class="form-control p_scheme rq">
                                        <small class="form-text">Max Loan (30% of salary)</small>
                                      </div>

                                      <div class="col-md-6 mb-3">
                                        <input type="number" data-term = "<?=$per_term?>" id = "ca_num_days" value = "<?=$result->terms?>" class = "form-control p_scheme rq">
                                        <small class="form-text">Number of Months</small>
                                      </div>
                                    </div>


                                    <div class="form-group row mt-3">
                                      <div class="col-md-6 mb-3">
                                        <input type="number" data-rate = "<?=$per_rate?>" id = "ca_monthly_rate" value = "<?=$result->rate?>" data-max = "0" class="form-control p_scheme rq" readonly>
                                        <small class="form-text">Processing Fee</small>
                                      </div>

                                      <div class="col-md-6 mb-3">
                                        <input type="text" id = "ca_total" value = "<?=$result->amount?>" class="form-control rq" readonly>
                                        <small class="form-text">Total Cash Advance to be Released</small>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <!-- <div class="row"> -->
                                  <!-- <div class="col-md-4">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Employee ID No.<span class = "ml-2 text-danger" >*</span></label>
                                      <select id="employee_id_no" class="form-control">
                                          <<?php foreach ($employee as $emp): ?>
                                            <option value = "<?=$emp->employee_idno?>"><?=$emp->employee_idno?> -<?=$emp->first_name?> <?=$emp->last_name?></option>
                                          <?php endforeach ?>
                                      </select>
                                    </div>
                                  </div> -->
                                  <!--dates-->
                                  <!-- <div class="col-md-4">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Date of file<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="date" class = "form-control" id="date_of_file">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Date of effectivity<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="date" class = "form-control" id="date_of_effectivity">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>
                                </div> -->
                                <!--Amount, Reason, Terms, Rate-->
                                <!-- <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Amount<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="number" id="amount" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Reason<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="reason" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Terms<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="number" id="terms" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Rate<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="number" id="rate" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                </div> -->
                                <div class="row">
                                  <div class="col-md-12 text-right">
                                    <?php if($result->status == 'waiting'):?>
                                      <button id="editCABtn" class="btn btn-primary">Update Cashadvance</button>
                                    <?php endif;?>
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
<script src="<?= base_url('assets/js/transactions/cashadvance.js') ?>"></script>
