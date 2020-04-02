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
<link rel="stylesheet" href="<?=base_url('assets/css/custom_checkbox.css')?>">
<div class="content-inner" id="pageActive" data-num="15" data-namecollapse="" data-labelname="Payroll">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/payroll/'.$token);?>">Payroll</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Create Payroll</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <input type = "hidden" id = "manhours_name">
    <input type = "hidden" id = "manhours_hours">
    <input type = "hidden" id = "manhours_minutes">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <!-- PAYTYPE AND COMPANY -->
                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="Filter" class="form-control-label col-form-label-sm">Pay Type <span class="asterisk"></span></label><br>
                    <select name="p_paytype" id="p_paytype" class="form-control select2 rq medium" mrange = "" mfrequency = "">
                      <option value="" >------</option>
                      <?php if($paytype->num_rows() > 0):?>
                        <?php foreach($paytype->result() as $pt):?>
                          <option value="<?=$pt->paytypeid?>"
                            data-range = "<?=$pt->date_range?>"
                            data-frequency = "<?=$pt->frequency?>">
                            <?=$pt->description?>
                          </option>
                        <?php endforeach;?>
                      <?php endif;?>
                    </select>
                    <!-- <small class="form-text">Pay Type <span class="asterisk"></span></small> -->
                  </div>

                  <div class="col-md-6">
                    <label for="Company" class="form-control-label col-form-label-sm">Company <span class="asterisk"></span></label>
                    <select name="company" id="company" class="form-control select2">
                      <option value="">------</option>
                      <?php if($companies->num_rows() > 0):?>
                        <?php foreach($companies->result_array() as $company):?>
                          <option value="<?=$company['id']?>"><?=$company['company']?></option>
                        <?php endforeach;?>
                      <?php endif;?>
                    </select>
                  </div>
                </div>
                <!-- CUT OFF -->
                <div class="form-group row">
                  <div class="col-md-4 mb-2">
                    <label for="Payment For" class="form-control-label col-form-label-sm">Pay Day <span class="asterisk"></span></label>
                    <input type="text" id = "pay_day" class="form-control date_input rq">
                  </div>

                  <div class="col-md-4 mb-2">
                    <label for="Cut Off From" class="form-control-label col-form-label-sm">Cut Off From <span class="asterisk"></span></label>
                    <input type="text" id = "p_date_from" name = "p_date_from" class="form-control date_input rq medium">
                    <!-- <small class="form-text">From <span class="asterisk"></span></small> -->
                  </div>

                  <div class="col-md-4 mb-2">
                    <label for="Cut Off To" class="form-control-label col-form-label-sm">Cut Off To <span class="asterisk"></span></label>
                    <input type="text" id = "p_date_to" name = "p_date_to" class="form-control date_input rq medium">
                    <!-- <small class="form-text">To <span class="asterisk"></span></small> -->
                  </div>

                  <div class="col-md-12 text-right">
                    <button class="btn btn-sm btn-primary" id = "btn_gen_payroll">Generate Payroll</button>
                  </div>
                </div>

              </div>
              <div class="card-body">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" data-type = "manhours" href="#tab_man_hours" style="color:black;">Man Hours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" data-type = "dduction" href="#tab_deductions" style="color:black;" >Deductions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" data-type = "additional" href="#tab_additionals" style="color:black;" >Additionals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" data-type = "psummary" href="#tab_summary" style="color:black;" >Payroll Summary</a>
                    </li>
                </ul>
                <div class="tab-content">
                  <!-- manhours -->
                  <div class="tab-pane fade show active" id = "tab_man_hours">
                    <div class="container pt-3">
                      <div class="mh_div" style = "display:none;">
                        <div class="form-group row">
                          <div class="col-12">
                            <span class = "medium">Man Hours Summary</span>
                          </div>
                          <div class="col-md-2">
                            <span class = "medium">Date:</span>
                          </div>
                          <div class="col-md-10" id = "mhsum_date">
                            <span class = "medium">March 1 2019 - March 15 2019</span>
                          </div>

                          <div class="col-md-2">
                            <span class = "medium">Pay Type:</span>
                          </div>
                          <div class="col-md-5" id="mhsum_type">
                            <span class = "medium">Weekly</span>
                          </div>

                          <div class="col-5 text-right">
                            <button class="btn btn-sm btn-info btn_verify" data-status = "0">Verify</button>
                          </div>
                        </div>
                        <div class="form-group">
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
                      </div>
                      <div class="mh_div_empty text-center" style = "padding-top:5%;padding-bottom:5%;">
                        <i class="fa fa-credit-card" style = "font-size:80px;"></i>
                        <small class="form-text">No Man Hours Available</small>
                      </div>
                    </div>
                  </div>
                  <!-- deductions -->
                  <div class="tab-pane fade" id = "tab_deductions">
                    <div class="container pt-3">
                      <div class="dduct_div" style = "display:none;">
                        <div class="form-group row">
                          <div class="col-12">
                            <span class = "medium">Deduction Summary</span>
                          </div>
                          <div class="col-md-2">
                            <span class = "medium">Date:</span>
                          </div>
                          <div class="col-md-10" id = "dsum_date">
                            <span class = "medium">March 1 2019 - March 15 2019</span>
                          </div>

                          <div class="col-md-2">
                            <span class = "medium">Pay Type:</span>
                          </div>
                          <div class="col-md-5" id="dsum_type">
                            <span class = "medium">Weekly</span>
                          </div>

                          <div class="col-5 text-right">
                            <button class="btn btn-sm btn-info btn_verify" data-status = "0">Verify</button>
                            <!-- <input type="checkbox" class = "verified"> -->
                            <!-- <label for="Verified" class="form-control-label col-form-label-sm"><strong>Verified</strong></label> -->
                          </div>
                        </div>
                        <div class="form-group">
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
                                <th>Action</th>
                              </thead>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="dduct_div_empty text-center" style = "padding-top:5%;padding-bottom:5%;">
                        <i class="fa fa-credit-card" style = "font-size:80px;"></i>
                        <small class="form-text">No Deductions Available</small>
                      </div>
                    </div>
                  </div>
                  <!-- additionals -->
                  <div class="tab-pane fade" id = "tab_additionals">
                    <div class="container pt-3">
                      <div class="addDiv" style = "display:none;">
                        <div class="form-group row">
                          <div class="col-12">
                            <span class = "medium">Additonals Summary</span>
                          </div>
                          <div class="col-md-2">
                            <span class = "medium">Date:</span>
                          </div>
                          <div class="col-md-10" id = "asum_date">
                            <span class = "medium">March 1 2019 - March 15 2019</span>
                          </div>

                          <div class="col-md-2">
                            <span class = "medium">Pay Type:</span>
                          </div>
                          <div class="col-md-5" id="asum_type">
                            <span class = "medium">Weekly</span>
                          </div>

                          <div class="col-5 text-right">
                            <button class="btn btn-sm btn-info btn_verify" data-status = "0">Verify</button>
                            <!-- <input type="checkbox" class = "verified"> -->
                            <!-- <label for="Verified" class="form-control-label col-form-label-sm"><strong>Verified</strong></label> -->
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="table-responsive">
                            <table class="table table-striped table-bordered" id = "additional_log_tbl">
                              <thead>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Additional Pays</th>
                                <th>Overtime Pays</th>
                                <th>Action</th>
                              </thead>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="addDiv_empty text-center" style = "padding-top:5%;padding-bottom:5%;">
                        <i class="fa fa-credit-card" style = "font-size:80px;"></i>
                        <small class="form-text">No Additional Pays Available</small>
                      </div>
                    </div>
                  </div>
                  <!-- payroll summary -->
                  <div class="tab-pane fade" id = "tab_summary">
                    <div class="container pt-3">
                      <div class="pm_div" style = "display:none;">
                        <div class="form-group row">
                          <div class="col-12">
                            <span class = "medium">Payroll Summary</span>
                          </div>
                          <div class="col-md-2">
                            <span class = "medium">Date:</span>
                          </div>
                          <div class="col-md-10" id = "psum_date">
                            <span class = "medium">March 1 2019 - March 15 2019</span>
                          </div>

                          <div class="col-md-2">
                            <span class = "medium">Pay Type:</span>
                          </div>
                          <div class="col-md-5" id="psum_type">
                            <span class = "medium">Weekly</span>
                          </div>

                          <div class="col-5 text-right">
                            <button class="btn btn-sm btn-info btn_verify" data-status = "0">Verify</button>
                            <!-- <input type="checkbox" class = "verified"> -->
                            <!-- <label for="Verified" class="form-control-label col-form-label-sm"><strong>Verified</strong></label> -->
                          </div>

                        </div>
                        <div class="form-group">
                          <div class="table-responsive">
                            <table class="table table-striped table-bordered" id = "payroll_log_tbl">
                              <thead>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <!-- <th>Rate Per Hour</th> -->
                                <!-- <th>Man Hours</th> -->
                                <th>Gross Pay</th>
                                <th>Additionals</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Action</th>
                              </thead>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="pm_div_empty text-center" style = "padding-top:5%;padding-bottom:5%;">
                        <i class="fa fa-credit-card" style = "font-size:80px;"></i>
                        <small class="form-text">No Summary Available</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button class="btn btn-sm btn-primary" id = "btn_save_payroll">
                  Save
                </button>
                <!-- <button class="btn btn-sm btn-primary" id = "btn_gen_payroll">Generate Payroll</button> -->
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


<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/payroll/payroll.js')?>"></script>
