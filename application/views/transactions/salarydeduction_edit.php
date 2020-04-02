
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a class = "white-text" href= "<?=base_url('transactions/Salarydeduction/index/'.$token)?>">Salary Deduction</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Edit Salary Deduction</li>
        </ol>
    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
    <input type = "hidden" id = "caID" value="<?=$result->sal_deduct_id?>">
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                      <div class="card-body">

<!--                           <ul class="nav nav-tabs">
                              <li class="nav-item">
                                  <a class="nav-link active" data-toggle="tab" href="#employeeDetails" style="color:black;">Salary Deduction Edit</a>
                              </li>
                          </ul> -->

                          <div class="tab-content">
                            <div id="employeeDetails" class="tab-pane fade show active">
                              <br>
                              <div class="container">
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-grouup">
                                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
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
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Employee ID No.<span class = "ml-2 text-danger" >*</span></label>
                                      <select id="employee_id_no" class="form-control select2">
                                        <?php if($employee->num_rows() > 0):?>
                                          <?php foreach($employee->result_array() as $emp):?>
                                            <option value="<?=$emp['employee_idno']?>" <?=($emp['employee_idno'] == $result->employee_idno)? "SELECTED" : ""?>>
                                              <?=$emp['fullname']?> (<?=$emp['employee_idno']?>)
                                            </option>
                                          <?php endforeach;?>
                                        <?php endif;?>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                                <div class = "row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Deduction Category<span class = "ml-2 text-danger" >*</span></label>
                                      <select id = "deduction_category" class = "form-control select2">
                                        <?php if($dropdown->num_rows() > 0):?>
                                          <<?php foreach ($dropdown->result() as $dd): ?>
                                            <option value = "<?=$dd->deductionid?>" <?=($dd->deductionid == $result->deductionid)? "SELECTED" : ""?>><?=$dd->description?></option>
                                          <?php endforeach ?>
                                        <?php endif;?>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Amount<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="amount" class = "form-control money-input" autocomplete="off" value = "<?=$result->amount?>">
                                    </div>
                                  </div>
                                </div>


                                <div class="row">
                                  <div class="col-md-12 text-right">
                                    <?php if($result->status == 'waiting'):?>
                                    <button id="editSalDecBtn" class="btn btn-primary">Update Salary Deduction</button>
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
<script src="<?= base_url('assets/js/transactions/salarydeduction.js') ?>"></script>
