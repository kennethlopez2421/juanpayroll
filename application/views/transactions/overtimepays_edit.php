
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a class = "white-text" href = "<?=base_url('transactions/Overtimepays/index/'.$token)?>">Overtime Pays</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Edit Overtime Pays</li>
        </ol>
    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
    <input type = "hidden" id = "caID" value="<?=$result->id?>">
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                      <div class="card-body">

<!--                           <ul class="nav nav-tabs">
                              <li class="nav-item">
                                  <a class="nav-link active" data-toggle="tab" href="#employeeDetails" style="color:black;">Overtime Pays Edit</a>
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
                                      <select name="dept" id="dept" class="form-control" style = "height:41px;">
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
                                            <option value="<?=$emp['employee_idno']?>" <?=($emp['employee_idno'] == $result->employee_id)? "SELECTED" : ""?>>
                                              <?=$emp['fullname']?> (<?=$emp['employee_idno']?>)
                                            </option>
                                          <?php endforeach;?>
                                        <?php endif;?>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                                <div class = "row">
                                <!--Amount, Reason, Terms, Rate-->
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="Date Rendered" class="form-control-label col-form-label-sm">Ot type <span class="asterisk"></span></label>
                                    <select name="type" id="type" class="form-control">
                                      <option value="<?=en_dec('en','overtime')?>" <?=($result->type == 'overtime') ? "SELECTED": ""?>>Overtime</option>
                                      <option value="<?=en_dec('en', 'offset')?>" <?=($result->type == 'offset') ? "SELECTED": ""?>>Offset</option>
                                    </select>
                                  </div>
                                </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label for="Date Rendered" class="form-control-label col-form-label-sm">Date Rendered <span class="asterisk"></span></label>
                                      <input type="text" class="form-control date_input_empty" id="date_rendered" value = "<?=$result->date_rendered?>">
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Minutes of overtime<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="minutes_of_overtime" class = "form-control number-input-2" autocomplete="off" value = "<?=$result->minutes_of_overtime?>">
                                    </div>
                                  </div>
                                  <div class="col-md-12">
                                    <div class="form-group">
                                      <label class = "form-control-label col-form-label-sm active">Purpose<span class = "ml-2 text-danger" >*</span></label>
                                      <!-- <input type="text" id="purpose" class = "form-control" autocomplete="off" value = "<?=$result->purpose?>"> -->
                                      <textarea name="purpose" id="purpose" cols="30" rows="3" class="form-control"><?php echo $result->purpose;?></textarea>
                                    </div>
                                  </div>
                                </div>


                                <div class="row">
                                  <div class="col-md-12 text-right">
                                    <?php if($result->status == 'waiting'):?>
                                    <button id="editOPBtn" class="btn btn-primary">Update Overtime Pays</button>
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
<script src="<?= base_url('assets/js/transactions/overtimepays.js') ?>"></script>
