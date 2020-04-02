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
  .bg-secondary{
    background-color: gainsboro !important;
  }

  .medium, .nav li a{
    font-size: 12.5px;
    font-color: #505050;
  }
</style>
<div class="content-inner" id="pageActive" data-num="15" data-namecollapse="" data-labelname="Payroll">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/payroll/'.$token);?>">Payroll</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Payroll History</li>
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
                      <option value="by_refno">Reference No.</option>
                      <option value="by_company">Company</option>
                      <option value="by_paytype">Paytype</option>
                      <option value="by_date">PayDay</option>
                      <!-- <option value="by_amount">CutOff</option> -->
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divRefno" class = "filter_div single_search active">
                      <label for="Reference Number" class="form-control-label col-form-label-sm">Reference Number</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divCompany" class = "filter_div single_search" style = "display:none;">
                      <label for="Company" class="form-control-label col-form-label-sm">Company</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. Cloud Panda">
                    </div>

                    <div id="divPaytype" class="filter_div single_search" style = "display:none;">
                      <label for="Paytype" class="form-control-label col-form-label-sm">PayType</label>
                      <select class = "form-control searchArea">
                        <option value="">------</option>
                      </select>
                    </div>

                    <div id="divDate" class = "filter_div dual_search" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Pay Day</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "date_from" class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id = "date_to" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive" id = "summary_wrapper">
                  <table class="table table-bordered table-striped" id = "payroll_history_tbl">
                    <thead>
                      <th>Ref Number</th>
                      <th>Company</th>
                      <th>Paytype</th>
                      <th>PayDay</th>
                      <th>Cut Off</th>
                      <th>Status</th>
                      <th>Action</th>
                    </thead>
                  </table>
                </div>

                <div class="table-responsive" id = "breakdown_wrapper" style = "display:none;">
                  <div class="col-md-12 text-right">
                    <button class="btn btn-primary" id = "btn_back">Back</button>
                  </div>
                  <ul class="nav nav-tabs">
                      <li class="nav-item">
                          <a class="nav-link active" data-type = "manhours" data-toggle="tab" href="#manhours" style="color:black;">Man Hours</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" data-type = "dduction" data-toggle="tab" href="#deduction" style="color:black;" >Deductions</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" data-type = "additional" data-toggle="tab" href="#additionals" style="color:black;" >Additionals</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" data-psummary = "psummary" data-toggle="tab" href="#payroll" style="color:black;" >Payroll Summary</a>
                      </li>
                  </ul>
                  <div class="tab-content pt-3">
                    <div class="tab-pane fade show active" id = "manhours">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered" id = "manHours_log_tbl">
                          <thead>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Days</th>
                            <th>Man Hours</th>
                            <th>Night Differentials(hrs)</th>
                            <th>Absent</th>
                            <th>Late(min)</th>
                            <th>Overtime(min)</th>
                            <th>Undertime(min)</th>
                            <th>Action</th>
                          </thead>
                        </table>
                      </div>
                    </div>
                    <div class="tab-pane fade" id = "deduction">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered" id = "dduction_log_tbl">
                          <thead>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>SSS</th>
                            <th>SSS Loan</th>
                            <th>Philhealth</th>
                            <th>Pag Ibig</th>
                            <th>Pag Ibig Loan</th>
                            <th>Salary Deduction</th>
                            <th>Cash Advance</th>
                            <th>Total</th>
                            <th width = "100">Action</th>
                          </thead>
                        </table>
                      </div>
                    </div>
                    <div class="tab-pane fade" id = "additionals">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered" id = "additional_log_tbl">
                          <thead>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Additional Pays</th>
                            <th>Overtime Pays</th>
                            <th width = "100">Action</th>
                          </thead>
                        </table>
                      </div>
                    </div>
                    <div class="tab-pane fade" id = "payroll">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered" id = "payroll_log_tbl">
                          <thead>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Gross Pay</th>
                            <th>Additionals</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th width = "100">Action</th>
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
      </div>
    </section>

    <!-- modal -->
    <div class="modal fade" id = "modal_breakdown">
      <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id = "breakdown_cat"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="container">
            <div class="form-group row">
              <div class="col-2"><span class = "medium">Name:</span></div>
              <div class="col-10" id = "breakdown_name"><span class = "medium"></span></div>
              <div class="col-2"><span class = "medium">Date:</span></div>
              <div class="col-10" id = "breakdown_date"><span class = "medium"></span></div>
              <div class="col-2"><span class = "medium">Pay Type:</span></div>
              <div class="col-10" id = "breakdown_type"><span class = "medium"></span></div>
              <div class="col-md-12">
                <div class="table-responsive" id = "table_ajax">

                </div>
              </div>
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
    <!-- payroll modal -->
    <div class="modal fade" id = "payroll_breakdown_modal">
      <div class="modal-dialog  modal-lg ">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Payroll BreakDown</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <div class="card-body p-5">
            <div class="form-group row">
              <div class="col-lg-2">ID No:</div>
              <div class="col-lg-10" id = "p_idno"></div>

              <div class="col-lg-2">Name:</div>
              <div class="col-lg-10" id = "p_name"></div>

              <div class="col-lg-2 mb-4">Date:</div>
              <div class="col-lg-10 mb-4" id = "p_date"></div>

              <div class="col-lg-12 mb-1" id = "convertion_wrapper" style = "display:none;">
                <label class="container_label">
                  <input id = "show_peso_chkbox" data-ex_rate = "" type="checkbox" name = "content_nav[]" class = "content_nav">
                  <span class="checkmark"></span>
                  <span class="text-info">(NOTE: Check this if you want to view the conversion in PESO)</span>
                </label>
              </div>

              <div class="col-lg-4 bg-secondary"><strong>Gross Salary</strong></div>
              <div class="col-lg-4 bg-secondary text-center"><strong>Time Value</strong></div>
              <div class="col-lg-4 bg-secondary text-right"><strong>Amount</strong></div>

              <div class="col-lg-4">Days(days)</div>
              <div class="col-lg-4 text-center" id = "p_wday"></div>
              <div class="col-lg-4 text-right convert" id = "p_grosspay"></div>

              <div class="col-lg-4">Regular Holidays(days)</div>
              <div class="col-lg-4 text-center" id = "p_reg_holiday"></div>
              <div class="col-lg-4 text-right convert" id = "p_reg_holiday_pay"></div>

              <div class="col-lg-4">Special Non-Working Holiday(days)</div>
              <div class="col-lg-4 text-center" id = "p_spl_holiday"></div>
              <div class="col-lg-4 text-right convert" id = "p_spl_holiday_pay"></div>

              <div class="col-lg-4 mb-4">Sunday</div>
              <div class="col-lg-4 text-center mb-4" id = "p_sunday"></div>
              <div class="col-lg-4 text-right mb-4 convert" id = "p_sunday_pay"></div>

              <div class="col-lg-4 bg-secondary"><strong>Penalties</strong></div>
              <div class="col-lg-4 bg-secondary text-center"><strong>Time Value</strong></div>
              <div class="col-lg-4 bg-secondary text-right"><strong>Amount</strong></div>

              <div class="col-lg-4">Absent(days)</div>
              <div class="col-lg-4 text-center" id = "p_absent"></div>
              <div class="col-lg-4 text-right convert" id = "p_absent_deduct"></div>

              <div class="col-lg-4">Late(min)</div>
              <div class="col-lg-4 text-center" id = "p_late"></div>
              <div class="col-lg-4 text-right convert" id = "p_late_deduct"></div>

              <div class="col-lg-4 mb-4">Undertime(min)</div>
              <div class="col-lg-4 text-center mb-4" id = "p_ut"></div>
              <div class="col-lg-4 text-right mb-4 convert" id = "p_ut_deduct"></div>

              <div class="col-lg-4 mb-4"><strong>Gross Pay:</strong></div>
              <div class="col-lg-4 text-center mb-4" id = ""></div>
              <div class="col-lg-4 text-right mb-4"><strong class = "convert" id = "p_grosspay_less"></strong></div>

              <div class="col-lg-4 bg-secondary"><strong>Deductions</strong></div>
              <div class="col-lg-4 bg-secondary text-center"></div>
              <div class="col-lg-4 bg-secondary text-right"><strong>Amount</strong></div>

              <div class="col-lg-4">SSS</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_sss"></div>

              <div class="col-lg-4">SSS Loan</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_sss_loan"></div>

              <div class="col-lg-4">Philhealth</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_philhealth"></div>

              <div class="col-lg-4">Pagibig</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_pagibig"></div>

              <div class="col-lg-4">Pagibig Loan</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_pagibig_loan"></div>

              <div class="col-lg-4">Cash Advance</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_cashadvance"></div>

              <div class="col-lg-4">Salary Deductions</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_sal_deduct"></div>

              <div class="col-lg-4 mb-4">Total Deductions</div>
              <div class="col-lg-4 text-center mb-4"></div>
              <div class="col-lg-4 text-right mb-4 convert" id = "p_total_deduct"></div>


              <div class="col-lg-4 bg-secondary"><strong>Additionals</strong></div>
              <div class="col-lg-4 bg-secondary text-center"><strong>Time Value</strong></div>
              <div class="col-lg-4 bg-secondary text-right"><strong>Amount</strong></div>

              <div class="col-lg-4">Additional Pay</div>
              <div class="col-lg-4 text-center"></div>
              <div class="col-lg-4 text-right convert" id = "p_add_pay"></div>

              <div class="col-lg-4">Overtime(mins)</div>
              <div class="col-lg-4 text-center" id = "p_ot_min"></div>
              <div class="col-lg-4 text-right convert" id = "p_ot_pay"></div>

              <div class="col-lg-4 mb-4">Night Differentials(hrs)</div>
              <div class="col-lg-4 text-center mb-4" id = "nightdiff_hrs"></div>
              <div class="col-lg-4 text-right mb-4 convert" id = "night_diff"></div>

              <div class="col-lg-4"><strong>Net Pay:</strong></div>
              <div class="col-lg-4 text-center" id = ""></div>
              <div class="col-lg-4 text-right"><strong class = "convert" id = "p_net_pay"></strong></div>


            </div>
          </div>
          <!-- <div class="modal-body">
            <div class="form-group row">
              <div class="col-4"><h4>Name:</h4></div>
              <div class="col-8"><h4 id = "p_name"></h4></div>

              <div class="col-4">Pay Type:</div>
              <div class="col-8"><h4 id = "p_paytype2"></h4></div>

              <div class="col-4">Date:</div>
              <div class="col-8"><h4 id = "p_date"></h4></div>
            </div>

            <div class="form-group row">
              <div class="col-12">
                <h4><u>Gross Salary</u></h4>
              </div>

              <div class="col-4"><h4>Days(day)</h4></div>
              <div class="col-2"><h4 id="p_wday"></h4></div>
              <div class="col-6"><h4 id="p_grosspay"></h4></div>

              <div class="col-4"><h4>Reg Holiday(day)</h4></div>
              <div class="col-2"><h4 id="p_reg_holiday"></h4></div>
              <div class="col-6"><h4 id="p_reg_holiday_pay"></h4></div>

              <div class="col-4"><h4>Spl Holiday(day)</h4></div>
              <div class="col-2"><h4 id="p_spl_holiday"></h4></div>
              <div class="col-6"><h4 id="p_spl_holiday_pay"></h4></div>

              <div class="col-4"><h4>Sunday(day)</h4></div>
              <div class="col-2"><h4 id="p_sunday"></h4></div>
              <div class="col-6"><h4 id="p_sunday_pay"></h4></div>

              <div class="col-12"><h4><u>Less</u></h4></div>

              <div class="col-4"><h4>Absent(day)</h4></div>
              <div class="col-2"><h4 id = "p_absent"></h4></div>
              <div class="col-6"><h4 id = "p_absent_deduct"></h4></div>

              <div class="col-4"><h4>Late(min)</h4></div>
              <div class="col-2"><h4 id="p_late"></h4></div>
              <div class="col-6"><h4 id="p_late_deduct"></h4></div>

              <div class="col-4"><h4>UT(min)</h4></div>
              <div class="col-2"><h4 id="p_ut"></h4></div>
              <div class="col-6"><h4 id="p_ut_deduct"></h4></div>

              <div class="col-6"><h4>Gross Salary</h4></div>
              <div class="col-6"><h4 id="p_grosspay_less"></h4></div>

            </div>

            <div class="form-group row">
              <div class="col-12"><h4><u>Deductions</u></h4></div>

              <div class="col-6"><h4>SSS</h4></div>
              <div class="col-6"><h4 id="p_sss"></h4></div>

              <div class="col-6"><h4>Philhealth</h4></div>
              <div class="col-6"><h4 id="p_philhealth"></h4></div>

              <div class="col-6"><h4>Pagibig</h4></div>
              <div class="col-6"><h4 id="p_pagibig"></h4></div>

              <div class="col-6"><h4>Cash Advance</h4></div>
              <div class="col-6"><h4 id="p_cashadvance"></h4></div>

              <div class="col-6"><h4>Salary Deduction</h4></div>
              <div class="col-6"><h4 id="p_sal_deduct"></h4></div>

              <div class="col-6"><h4>Total Deduction</h4></div>
              <div class="col-6"><h4 id="p_total_deduct"></h4></div>
            </div>

            <div class="form-group row">
              <div class="col-12"><h4><u>Additionals</u></h4></div>
              <div class="col-6"><h4>Additional Pays</h4></div>
              <div class="col-6"><h4 id="p_add_pay"></h4></div>

              <div class="col-4"><h4>Ot Pay(min)</h4></div>
              <div class="col-2"><h4 id="p_ot_min"></h4></div>
              <div class="col-6"><h4 id="p_ot_pay"></h4></div>
            </div>

            <div class="form-group row">
              <div class="col-6"><h4><u>Net Pay</u></h4></div>
              <div class="col-6"><h4 id="p_net_pay"></h4></div>
            </div>
          </div> -->
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- confirm modal -->
    <div class="modal fade" id = "confirm_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <h4>Are you sure you want to approve this ? Payroll Refno: (<span id="confirm_refno"></span>)</h4>
            <h5></h5>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- bank modal -->
    <div class="modal fade" id = "bank_file_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Generate Bank File</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6">
                <label for="Bank" class="form-control-label col-form-label-sm">Bank <span class="asterisk"></span></label>
                <select name="bank" id="bank" class="form-control">
                  <option value="">------</option>
                  <?php if($banks->num_rows() > 0):?>
                    <?php foreach($banks->result_array() as $bank):?>
                      <option value="<?=$bank['bank_id']?>"><?=$bank['bank_name']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
                <input type="hidden" id = "payroll_refno" value = "">
              </div>

              <div class="col-md-6">
                <label for="File Type" class="form-control-label col-form-label-sm">File Type:</label>
                <select name="file_type" id="file_type" class="form-control">
                  <option value="xlsx">.xlsx</option>
                  <option value="xls">.xls</option>
                </select>
              </div>
            </div>

            <div class="form-group row template_wrapper">
              <!-- BDO -->
              <div class="col-md-12 div_template" id = "bdo_template" data-id = "1" style = "display:none">
                <div class="row">
                  <div class="col-md-12">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "bdo_company_name" name = "bdo_company_name"type="text" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label for="File Prefix" class="form-control-label col-form-label-sm">File Prefix</label>
                    <input id = "bdo_file_prefix" name = "bdo_file_prefix" type="text" class="form-control rq">
                  </div>

                  <div class="col-md-6">
                    <label for="Virtual Account" class="form-control-label col-form-label-sm">Virtual Account:</label>
                    <input id = "bdo_virtual_account" name = "bdo_virtual_account" type="text" class="form-control rq">
                  </div>

                  <div class="col-md-6">
                    <label for="Credit Date" class="form-control-label col-form-label-sm">Credit Date</label>
                    <input id = "bdo_credit_date" name = "bdo_credit_date" type="text" class="form-control date_input rq">
                  </div>

                  <div class="col-md-6">
                    <label for="Batch No." class="form-control-label col-form-label-sm">Batch No.</label>
                    <input id = "bdo_batch_no" name = "bdo_batch_no" type="text" class="form-control rq">
                  </div>
                </div>

              </div>
              <!-- METRO BANK -->
              <div class="col-md-12 div_template" id = "metro_bank_template" data-id = "2" style = "display:none">
                <div class="row">
                  <div class="col-md-12">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "metro_company_name" name = "bdo_company_name"type="text" class="form-control rq">
                  </div>
                  <div class="col-md-6">
                    <label for="Branch Code" class="form-control-label col-form-label-sm">Branch Code:</label>
                    <input id = "metro_branch_code" name = "metro_branch_code" type="text" class="form-control rq">
                  </div>
                  <div class="col-md-6">
                    <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                    <input id = "metro_date" name = "metro_date" type="text" class="form-control date_input rq">
                  </div>
                </div>
              </div>
              <!-- CTBC -->
              <div class="col-md-12 div_template" id = "ctbc_template" data-id = "5" style = "display:none">
                <div class="row">
                  <div class="col-md-6">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "ctbc_company_name" name = "ctbc_company_name" type="text" class="form-control rq">
                  </div>
                  <div class="col-6">
                    <label for="Date" class="form-control-label col-form-label-sm">Date:</label>
                    <input id = "ctbc_date" name = "ctbc_date" type="text" class="form-control date_input rq">
                  </div>
                </div>
              </div>
              <!-- DEFAULT -->
              <div class="col-md-12 div_template" id = "default_template" style = "display:none;">
                <div class="row">
                  <div class="col-md-6">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "default_company_name" name = "default_company_name" type="text" class="form-control rq">
                  </div>
                  <div class="col-md-6">
                    <label for="Date" class="form-control-label col-form-label-sm">Date:</label>
                    <input id = "default_date" name = "default_date" type="text" class="form-control date_input rq">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_generate">Generate</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\payroll\payroll_history_new.js')?>"></script>
