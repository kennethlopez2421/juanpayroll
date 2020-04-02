
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Cash Advance</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Add Cash Advance</li>
        </ol>
    </div>
<input type="hidden" id='token' value="<?= $token ?>">
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                      <div class="card-body">

                          <ul class="nav nav-tabs">
                              <li class="nav-item">
                                  <a class="nav-link active" data-toggle="tab" href="#employeeDetails" style="color:black;">Cash Advance Details</a>
                              </li>
                          </ul>

                          <div class="tab-content">
                            <div id="employeeDetails" class="tab-pane fade show active">
                              <br>
                              <div class="container">
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Employee ID No.<span class = "ml-2 text-danger" >*</span></label>
                                      <select id="employee_id_no" class="form-control">
                                        <option value="emp001">emp001</option>
                                        <option value="emp001">emp002</option>
                                        <option value="emp001">emp003</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                                <hr>
                              <!--dates-->
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Date of file<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="date" class = "form-control" id="date_of_file">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Date of effectivity<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="date" class = "form-control" id="date_of_effectivity">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>
                                </div>
                                <hr>
                                <!--Amount, Reason, Terms, Rate-->
                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label>Amount<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="number" id="amount" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label>Reason<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="reason" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label>Terms<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="number" id="terms" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label>Rate<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="number" id="rate" class = "form-control" autocomplete="off">
                                    </div>
                                  </div>
                                </div>

                                <hr>

                                <div class="row">
                                  <div class="col-md-12 text-right">
                                    <button id="addCABtn" class="btn btn-primary">Add Cashadvance</button>
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
