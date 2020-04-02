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

if (in_array($content_url, $url_content_arr) == false){
    header("location:".base_url('Main/logout'));
}
//071318
?>
<style>
  .dropdown-menu{
    border: 1px solid #999;
    border-top:none;
  }

  img{
    object-fit: 'contain'
  }

  .time_img:hover{
    cursor: pointer;
    border: 1px solid #72716f ;
  }
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/custom_loader2.css')?>">
<div class="content-inner" id="pageActive" data-num="15" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Pagibig Loans</li>
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
                      <option value="by_name">Employee Name</option>
                      <option value="by_deduct_start">Deduction Start</option>
                      <option value="by_period">Payment Period</option>
                      <option value="by_total_loan">Total Loan</option>
                      <option value="by_monthly_amortization">Monthly Amortization</option>
                    </select>
                  </div>

                  <div class="col-md-6">

                    <div id="divName" class = "filter_div active single_date">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divDate" class = "filter_div range_date" style = "display:none;">
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

                    <div id="divDate2" class = "filter_div range_date" style = "display:none;">
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

                    <div id="divLoan" class = "filter_div range_date" style = "display:none;">
                      <label for="Amount" class="form-control-label col-form-label-sm">Amount</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="number" id = "amount_from" class="form-control from">
                          <small class="form-text">From</small>
                        </div>

                        <div class="col-md-6">
                          <input type="text" id = "amount_to" class="form-control to">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                    <div id="divAmortization" class = "filter_div range_date" style = "display:none;">
                      <label for="Amount" class="form-control-label col-form-label-sm">Amount</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="number" id = "amount_from" class="form-control from">
                          <small class="form-text">From</small>
                        </div>

                        <div class="col-md-6">
                          <input type="text" id = "amount_to" class="form-control to">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_add_modal">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "pagibig_loan_tbl">
                    <thead>
                      <th width = "80">Pagibig Voucher</th>
                      <th>Employee</th>
                      <th>Deduction Start</th>
                      <th>Period</th>
                      <th>Total Loan</th>
                      <th>Monthly Amortization</th>
                      <th>Action</th>
                    </thead>
                  </table>
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
            <h4 class="modal-title">Pagibig Loans</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="pagibig_loan_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-12 mb-3">
                  <label for="Employee" class="form-control-label col-form-label-sm">Employee <span class="asterisk"></span></label>
                  <div class="dropdown" >
                    <input type="text" id = "employee" name = "employee" class="form-control dropdown-toggle" data-toggle = "dropdown">
                    <input type="hidden" id = "employee_idno" name = "employee_idno">
                    <div class="dropdown-menu form-control">
                      <div class="loader_wrapper" style = "display:none;">
                        <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
                          <div class="form-group row">
                            <div class="col-6 text-right p-0">
                              <h6>Searching ...</h6>
                            </div>
                            <div class="col-6 ">
                              <div class="loader-m"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id = "result_wrapper">

                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 mb-3">
                  <label for="SSS Voucher" class="form-control-label col-form-label-sm">Upload Pagibig Voucher <span class="asterisk"></span></label>
                  <input type="file" id = "pagibig_voucher" name = "pagibig_voucher" class = "form-control" placeholder=".jpg, jpeg, png">
                  <!-- <input type="text" id = "sss_voucher" name = "sss_voucher" class="form-control"> -->
                </div>

                <div class="col-12 mb-3">
                  <label for="Period" class="form-control-label col-form-label-sm">Period <span class="asterisk"></span></label>
                  <div class="row">
                    <div class="col-md-6">
                      <input type="text" id = "period_from" name = "period_from" class="form-control date_input">
                      <small class="form-text">From </small>
                    </div>
                    <div class="col-md-6">
                      <input type="text" id = "period_to" name = "period_to" class="form-control date_input">
                      <small class="form-text">To </small>
                    </div>
                  </div>
                </div>

                <div class="col-md-12 mb-3">
                  <label for="Deduction Start" class="form-control-label col-form-label-sm">Deduction Start <span class="asterisk"></span></label>
                  <input type="text" id = "deduction_start" name = "deduction_start" class="form-control date_input">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="Total Loan" class="form-control-label col-form-label-sm">Total Loan <span class="asterisk"></span></label>
                  <input type="text" id = "total_loan" name = "total_loan" class="form-control money-input" data-raw-value = "">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="Monthly Amortization" class="form-control-label col-form-label-sm">Monthly Amortization <span class="asterisk"></span></label>
                  <input type="text" id = "monthly_amortization" name = "monthly_amortization" class="form-control money-input" data-raw-value = "">
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary">Save</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- VIEW LOAN MODAL -->
    <div class="modal fade" id = "view_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Pagibig Loan</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6">
                <label for="Total Loan" class="form-control-label col-form-label-sm">Total Loan</label>
                <input type="text" class="form-control" id = "view_total_loan" name = "view_total_loan" readonly>
              </div>

              <div class="col-md-6">
                <label for="Monthly Amortization" class="form-control-label col-form-label-sm">Monthly Amortization</label>
                <input type="text" class="form-control" id = "view_monthly_amortization" name = "view_monthly_amortization" readonly>
              </div>
            </div>

            <div class="form-group">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id = "view_pagibig_loan_tbl" style = "border: 1px solid gainsboro;">
                  <thead>
                    <th>Month</th>
                    <th>Payroll Ref No.</th>
                    <th>Monthly Amortization</th>
                  </thead>
                </table>
              </div>
            </div>


          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- VIEW IMAGE MODAL -->
    <div class="modal fade" id = "view_image_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Pagibig Voucher</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="img-thumbnail view_image" style = "height:350px;width:100%;">

            </div>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\transactions\pagibig_loans.js')?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
