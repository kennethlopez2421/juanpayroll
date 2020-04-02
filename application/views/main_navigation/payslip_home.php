<?php
  // if(isset($this->session->admin_user_id) && isset($this->session->admin_username)){
  //   $username = en_dec('dec',$this->session->username);
  //   $user = $this->admin_model->get_admin_user($username);
  //   if($user->num_rows() == 0){
  //     header("Location:".base_url('Main/logout'));
  //   }
  // }else{
  //   header("Location:".base_url('Main/logout'));
  // }
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
<div class="content-inner" id="pageActive" data-num="25" data-namecollapse="" data-labelname="HRIS Branch">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token);?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Payslip</li>
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
                      <option value="by_payroll_refno">Payroll Refno</option>
                      <option value="by_date">Date</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divPayroll_refno" class = "filter_div single_search active">
                      <label for="Account Name" class="form-control-label col-form-label-sm">Account Name</label>
                      <input type="text" class="form-control searchArea" value = "">
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
                <div class="table-responsive">
                  <table id = "payslip_tbl" class="table table-bordered table-striped">
                    <thead>
                      <th width = "120">Payroll Refno</th>
                      <th width = "120">Date</th>
                      <th>Gross Pay</th>
                      <th>Net Pay</th>
                      <th width = "90">Action</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- payroll modal -->
    <div class="modal fade" id = "payroll_breakdown_modal">
      <div class="modal-dialog  modal-lg ">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Payslip</h4>
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

              <div class="col-lg-4 mb-4">Overtime(mins)</div>
              <div class="col-lg-4 text-center mb-4" id = "p_ot_min"></div>
              <div class="col-lg-4 text-right mb-4 convert" id = "p_ot_pay"></div>

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
            <!-- <button class="btn btn-sm btn-primary" id = "btn_print">Print</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\payroll\payslip.js')?>"></script>
