
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="15" data-namecollapse="" data-labelname="Payroll">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/payroll/'.$token);?>">Payroll</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('payroll/Payroll_history/index/'.$token);?>">Payroll History</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Payroll History Summary</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <input type = "hidden" id = 'manhrsid' value = "<?=$manhours?>">
    <input type = "hidden" id = 'additionalid' value = "<?=$additional?>">
    <input type = "hidden" id = 'deductionid' value = "<?=$deduction?>">
    <input type = "hidden" id = 'payrollid' value = "<?=$payroll?>">
    <input type = "hidden" id = 'date_from' value = "<?=$date_from?>">
    <input type = "hidden" id = 'date_to' value = "<?=$date_to?>">
    <input type = "hidden" id = 'paytype_desc' value = "<?=$paytype_desc?>">
        <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                        <div class = "form-group mt-2 mb-5">
                            <div class = "row">
                                <div class = "col-md-12 mb-2">
                                    <span class = "medium">Payroll History Summary</span>
                                </div>
                            </div>
<!--                             <div class = "row">
                                <div class = col-md-2>
                                    <h3>Department:</h3>
                                </div>
                                <div class = col-md-10>
                                    <h3><?=$department?></h3>
                                </div>
                            </div> -->
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = "medium">Pay Type:</span>
                                </div>
                                <div class = col-md-10>
                                    <?=$paytype_desc?>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = "medium">Date Range:</span>
                                </div>
                                <div class = col-md-10>
                                    <span class = "medium"><?=$date_range?></span>
                                </div>
                            </div>
                        </div>
                        <ul class="nav nav-tabs mt-3">
                            <li class="nav-item">
                                <a class="nav-link active" data-stype = "manhours" data-toggle="tab" id = "mh_nav" href="#manhours_tab" 
                                style="color:#505050;">
                                    <span class = "medium">Man Hours Summary</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-stype = "deductions" data-toggle="tab" id = "ded_nav" href="#deductions_tab" style="color:#505050;" >
                                    <span class = "medium">Deductions Summary</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-stype = "additionals" data-toggle="tab" id = "ad_nav" href="#additionals_tab" style="color:#505050;" >
                                    <span class = "medium">Additionals Summary</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-stype = "payroll" data-toggle="tab" id = "pay_nav" href="#payroll_tab" style="color:#505050;" >
                                    <span class = "medium">Payroll Summary</span></a>
                            </li>
                        </ul>
                        <!--man hours tab-->
                        <div class="tab-content">
                          <div class="tab-pane fade show active" id = "manhours_tab">
                            <div class = "card-header">
                                <div class = "row">
                                    <div class = "col-md-3">
                                        <label class = "form-control-label col-form-label-sm active">Search by ID or Name:</label>
                                        <input type = "text" class="form-control" placeholder="Search Here" id = "manhours_text">
                                    </div>
                                    <div class = "col-md-9 text-right">
                                        <button type="submit" class="btn btn-primary mb-2" id="search_manhours">Search</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                              <div class="table-responsive" style="">
                                <table class="table  table-striped table-hover table-bordered" id="manhourstable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                  <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Days</th>
                                        <th>Man Hours</th>
                                        <th>Absent</th>
                                        <th>Late(min)</th>
                                        <th>Overtime(min)</th>
                                        <th>Undertime(min)</th>
                                        <th>Action</th>
                                    </tr>
                                  </thead>
                                </table>
                              </div>
                            </div>
                          </div>
                          <!--Deductions Tab-->
                          <div class="tab-pane fade show" id = "deductions_tab">
                            <div class = "card-header">
                                <div class = "row">
                                    <div class = "col-md-3">
                                        <label class = "form-control-label col-form-label-sm active">Search by ID or Name:</label>
                                        <input type = "text" class="form-control" placeholder="Search Here" id = "deductions_text">
                                    </div>
                                    <div class = "col-md-9 text-right">
                                        <button type="submit" class="btn btn-primary mb-2" id="search_deductions">Search</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                              <div class="table-responsive" >
                                <table class="table  table-striped table-hover table-bordered" id="deductionstable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                  <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>SSS</th>
                                        <th>Philhealth</th>
                                        <th>Pag Ibig</th>
                                        <th>Salary Deduction</th>
                                        <th>Cash Advance</th>
                                        <th>Action</th>
                                    </tr>
                                  </thead>
                                </table>
                              </div>
                            </div>
                          </div>
                          <!--additionals tab-->
                          <div class="tab-pane fade show" id = "additionals_tab">
                            <div class = "card-header">
                                <div class = "row">
                                    <div class = "col-md-3">
                                        <label class = "form-control-label col-form-label-sm active">Search by ID or Name:</label>
                                        <input type = "text" class="form-control" placeholder="Search Here" id = "additionals_text">
                                    </div>
                                    <div class = "col-md-9 text-right">
                                        <button type="submit" class="btn btn-primary mb-2" id="search_additionals">Search</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                              <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="additionalstable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                  <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Additional Pays</th>
                                        <th>Overtime Pays</th>
                                        <th>Actions</th>
                                    </tr>
                                  </thead>
                                </table>
                              </div>
                            </div>
                          </div>
                        <!--Payroll Tab-->
                          <div class="tab-pane fade show" id = "payroll_tab">
                            <div class = "card-header">
                                <div class = "row">
                                    <div class = "col-md-3">
                                        <label class = "form-control-label col-form-label-sm active">Search by ID or Name:</label>
                                        <input type = "text" class="form-control" placeholder="Search Here" id = "payroll_text">
                                    </div>
                                    <div class = "col-md-9 text-right">
                                        <button type="submit" class="btn btn-primary mb-2" id="search_payroll">Search</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                              <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="payrolltable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                  <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Gross Pay</th>
                                        <th>Deductions</th>
                                        <th>Additionals</th>
                                        <th>Net Pay</th>
                                        <th>Action</th>

                                    </tr>
                                  </thead>
                                  <tfoot>
                                        <th><b>Totals:  </b></th>
                                        <th class = "employees_count"></th>
                                        <th class = "grosspay"></th>
                                        <th class = "deductions"></th>
                                        <th class = "additionals"></th>
                                        <th class = "netpay"></th>
                                        <th class = ""><center>-----------------</center></th>
                                  </tfoot>
                                </table>
                              </div>
                            </div>
                          </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
        <!---manhours log-->

         <div class = "modal fade" id = "viewmanhourslog" role = "dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <input type="hidden" id = "manhours_id">
            <div class = "modal-dialog modal-lg-custom">
               <div class = "modal-content">
                  <div class = "modal-header">      
                        <span id="exampleModalLabel" class="modal-title">Man Hours Log</span>
                  </div>
                  <div class = "modal-body">
                        <div class = "form-group mt-2 mb-5">
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = 'medium'>Name:</span>
                                </div>
                                <div class = col-md-10 >
                                    <span class = "employee_name"></span>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = 'medium'>Pay Type:</span>
                                </div>
                                <div class = col-md-10>
                                    <span class = 'medium'><?=$paytype_desc?></span>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = 'medium'>Date Range:</span>
                                </div>
                                <div class = col-md-10>
                                    <span class = 'medium'><?=$date_range?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class = "col-md-12">
                              <div class="table-responsive" style="">
                                <table class="table  table-striped table-hover table-bordered" id="manhourslogs"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" style="border-top: 1px solid gainsboro;">
                                  <thead>
                                    <tr>
                                        <th width = "12%">Date</th>
                                        <th>Day Type</th>
                                        <th>Time</th>
<!--                                         <th>Employee ID</th>
                                        <th width = "20%">Employee Name</th> -->
                                        <th>Man Hours</th>
<!--                                         <th>Absent</th> -->
                                        <th>Late(min)</th>
                                        <th>Overtime(min)</th>
                                        <th>Undertime(min)</th>
                                    </tr>
                                  </thead>
                                  <tbody id = "manhourslogs_body">
                                  </tbody>
                                </table>
                              </div>
                            </div>
                        </div>
                  </div>
                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
         </div>

<!----------------------deductions log---------->
         <div class = "modal fade" id = "viewdeductionslog" role = "dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <input type="hidden" id = "manhours_id">
            <div class = "modal-dialog modal-lg-custom">
               <div class = "modal-content">
                  <div class = "modal-header">      
                        <span id="exampleModalLabel" class="modal-title">Deductions Log</span>
                  </div>
                  <div class = "modal-body">
                        <div class = "form-group mt-2 mb-5">
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = 'medium'>Name:</span>
                                </div>
                                <div class = col-md-10 >
                                    <span class = "employee_name"></span>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = 'medium'>Pay Type:</span>
                                </div>
                                <div class = col-md-10>
                                    <span class = 'medium'><?=$paytype_desc?></span>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = 'medium'>Date Range:</span>
                                </div>
                                <div class = col-md-10>
                                    <span class = 'medium'><?=$date_range?></span>
                                </div>
                            </div>
                        </div>
                            <ul class="nav nav-tabs mt-3">
                                <li class="nav-item">
                                    <a class="nav-link active" data-stype = "compensation" data-toggle="tab" id = "mh_nav" href="#compensation_tab" style="color:black;"><span class = "medium">Compensation</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-stype = "deductions" data-toggle="tab" id = "ded_nav" href="#salary_deductions_tab" style="color:black;" ><span class = "medium">Deductions</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-stype = "cash_advance" data-toggle="tab" id = "ad_nav" href="#cashadvance_tab" style="color:black;" ><span class = "medium">Cash Advance</span></a>
                                </li>
                            </ul>
                             <!--deduction nav contents-->    
                            <div class="tab-content">
                            <!--compensation-->
                                <div class="tab-pane fade show active" id = "compensation_tab">
                                    <div class="row mt-3">
                                        <div class = "col-md-12">
                                            <div class="table-responsive" style="">
                                                <table class="table  table-striped table-hover table-bordered" id="compensations_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" style="border-top: 1px solid gainsboro;">
                                                    <thead>
                                                        <tr>
                                                            <th>SSS</th>
                                                            <th>Philhealth</th>
                                                            <th>Pag-ibig</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id = "compensations_body_tbl">
                                                            <tr>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!--deductions-->
                                <div class="tab-pane fade show" id = "salary_deductions_tab">
                                    <div class="row mt-3">
                                        <div class = "col-md-12">
                                            <div class="table-responsive" style="">
                                                <table class="table  table-striped table-hover table-bordered" id="compensations_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" style="border-top: 1px solid gainsboro;">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Reason</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id = "sd_body_tbl">
                                                            <tr>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!--cashadvance-->
                                <div class="tab-pane fade show" id = "cashadvance_tab">
                                        <div class = "col-md-12">
                                            <div class="table-responsive" style="">
                                                <table class="table  table-striped table-hover table-bordered" id="compensations_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" style="border-top: 1px solid gainsboro;">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Reason</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id = "ca_body_tbl">
                                                            <tr>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                </div>
                            </div>
                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
         </div>
        </div>
<!----------------------additionals log---------->
         <div class = "modal fade" id = "viewadditionalslog" role = "dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <input type="hidden" id = "additionals_id">
            <div class = "modal-dialog modal-lg-custom">
               <div class = "modal-content">
                  <div class = "modal-header">      
                        <span id="exampleModalLabel" class="modal-title">Additionals Log</span>
                  </div>
                  <div class = "modal-body">
                        <div class = "form-group mt-2 mb-5">
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = "medium">Name:</span>
                                </div>
                                <div class = col-md-10 >
                                    <span class = "employee_name medium"></span>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = "medium">Pay Type:</span>
                                </div>
                                <div class = col-md-10>
                                    <span class = "medium"><?=$paytype_desc?></span>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = col-md-2>
                                    <span class = "medium">Date Range:</span>
                                </div>
                                <div class = col-md-10>
                                    <span class = "medium"><?=$date_range?></span>
                                </div>
                            </div>
                        </div>
                            <ul class="nav nav-tabs mt-3">
                                <li class="nav-item">
                                    <a class="nav-link active" data-stype = "compensation" data-toggle="tab" id = "mh_nav" href="#additionals_pays_tab" style="color:black;"><span class = "medium">Additional Pays</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-stype = "deductions" data-toggle="tab" id = "ded_nav" href="#overtime_pays_tab" style="color:black;" ><span class = "medium">Overtime Pays</span></a>
                                </li>
                            </ul>
                            <div class="tab-content">
                            <!--additionals-->
                                <div class="tab-pane fade show active" id = "additionals_pays_tab">
                                    <div class="row mt-3">
                                        <div class = "col-md-12">
                                            <div class="table-responsive" style="">
                                                <table class="table  table-striped table-hover table-bordered" id="compensations_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" style="border-top: 1px solid gainsboro;">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id = "additionals_pays_tab_body">
                                                            <tr>
                                                                <td>sadfsdadsf</td>
                                                                <td>sadfsdadsf</td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!--overtime pays-->
                                <div class="tab-pane fade show" id = "overtime_pays_tab">
                                    <div class="row mt-3">
                                        <div class = "col-md-12">
                                            <div class="table-responsive" style="">
                                                <table class="table  table-striped table-hover table-bordered" id="compensations_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" style="border-top: 1px solid gainsboro;">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Reason</th>
                                                            <th>Overtime Minutes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id = "overtime_pays_tab_body">
                                                        <tr>
                                                            <td>sadfsdadsf</td>
                                                            <td>sadfsdadsf</td>
                                                            <td>sadfsdadsf</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                  </div>
                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
         </div>




<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/payroll/payroll_history_summary.js');?>"></script>
