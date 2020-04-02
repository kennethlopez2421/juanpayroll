
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a class = "white-text" href="<?=base_url('transactions/Additionalpays/index/'.$token)?>">Additional Pays</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Add Additional Pays</li>
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
                          <div class="row">
                          <!--dates-->
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class = "form-control-label col-form-label-sm active">Date Issued<span class = "ml-2 text-danger" >*</span></label>
                                <input type="text" class = "form-control date_input" id="date_issued">
                                <span class = "duplicateNameError text-danger d-block"></span>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group">
                                <label class = "form-control-label col-form-label-sm active">Amount<span class = "ml-2 text-danger" >*</span></label>
                                <input type="text" id="amount" class = "form-control money-input" autocomplete="off">
                              </div>
                            </div>
                          <!--Amount, Reason, Terms, Rate-->
                            <div class="col-md-12">
                              <div class="form-group">
                                <label class = "form-control-label col-form-label-sm active">Purpose<span class = "ml-2 text-danger" >*</span></label>
                                <input type="text" id="purpose" class = "form-control" autocomplete="off">
                              </div>
                            </div>


                          </div>

                          <div class="row">
                            <div class="col-md-12 text-right">
                              <button id="addAPBtn" class="btn btn-primary">Add Additional Pay</button>
                            </div>
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
<script src="<?= base_url('assets/js/transactions/additionalpays.js') ?>"></script>
