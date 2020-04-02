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
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Entity">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/employees_home/'.$token);?>">Entity</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Contract History</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="row mb-3">
                  <div class="col-md-3">
                    <label for="Filter" class="form-control-label col-form-label-sm">Filter</label>
                    <select name="filter_by" id="filter_by" class = "form-control">
                      <option value="by_name">By Name</option>
                      <option value="by_dept">By Department</option>
                      <option value="by_pos">By Position</option>
                      <!-- <option value="by_c_date">By Contract Date</option> -->
                      <option value="by_salary">By Salary Range</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <!-- div name -->
                    <div id="divName" class = "filter_div active">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class = "form-control searchArea" placeholder="ex. John Doe">
                    </div>
                    <!-- div deparment -->
                    <div id="divDept" class = "filter_div" style = "display:none">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <div class="row">
                        <div class="col-md-6">
                          <select name="" id="dept" class="form-control searchArea">
                            <option value="">------</option>
                            <?php if($dept->num_rows() > 0):?>
                              <?php foreach($dept->result_array() as $d):?>
                                <option value="<?=$d['departmentid']?>"><?=$d['description']?></option>
                              <?php endforeach;?>
                            <?php endif;?>
                          </select>
                          <small class="form-text">Department</small>
                        </div>
                        <div class="col-md-6">
                          <select name="" id="subDept" class="form-control searchArea">
                            <option value="">------</option>
                          </select>
                          <small class="form-text">Sub Department</small>
                        </div>
                      </div>
                    </div>
                    <!-- div position  -->
                    <div id="divPos" class = "filter_div" style = "display:none">
                      <label for="Position" class="form-control-label col-form-label-sm">Position</label>
                      <select name="" id="search_pos" class="form-control searchArea">
                        <?php if($position->num_rows() > 0):?>
                          <?php foreach($position->result_array() as $pos):?>
                            <option value="<?=$pos['position_id']?>"><?=$pos['position']?>, (<?=$pos['department']?>)</option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                    <!-- div contract date -->
                    <div id="divCdate" class = "filter_div" style = "display:none">
                      <label for="Contract Date" class="form-control-label col-form-label-sm">Contract Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "search_cStart_date" class="form-control searchArea dateInput" placeholder="yyyy-mm-dd">
                          <small class="form-text">Start Date</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id="search_cEnd_date" class="form-control searchArea2 dateInput" placeholder="yyyy-mm-dd">
                          <small class="form-text">End Date</small>
                        </div>
                      </div>
                    </div>
                    <!-- div salary range -->
                    <div id="divSalRange" class = "filter_div" style = "display:none;">
                      <label for="Salary Range" class="form-control-label col-form-label-sm">Salary Range</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="number" id = "search_salRange_from" class = "form-control searchArea">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="number" id = "search_salRange_to" class = "form-control searchArea2">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <button class="btn btn-primary btn-sm" id = "btn_search_cHistory">Search</button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" name = "con_status" class="form-check-input"  value="active">Active
                      </label>
                    </div>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" name = "con_status" class="form-check-input" value="inactive">Inactive
                      </label>
                    </div>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" name = "con_status" class="form-check-input" value="all" checked>All
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id = "contract_history_tbl" class="table table-bordered table-striped table-hover" style = "border-top:1px solid gainsboro;  ">
                    <thead>
                      <th>Name</th>
                      <th>Department</th>
                      <th>Sub Department</th>
                      <th>WorkSite</th>
                      <th>Position</th>
                      <th>Date</th>
                      <th>Status</th>
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
    <!-- view contract Note:: copy from previous contract-->
    <div class="modal fade" id="prevContract_modal">
      <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Previous Contract</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <!-- employee info -->
            <div class="container pt-3">
              <h3>Employee Information</h3>
              <div class="form-group row">
                <div class="col-md-4">
                  <input type="text" class = "form-control" id = "fname" readonly>
                  <small class="form-text">First Name</small>
                </div>
                <div class="col-md-4">
                  <input type="text" class = "form-control" id = "mname" readonly>
                  <small class="form-text">Middle Name</small>
                </div>
                <div class="col-md-4">
                  <input type="text" class = "form-control" id = "lname" readonly>
                  <small class="form-text">Last Name</small>
                </div>
              </div>
            </div>
            <!-- prev contract details -->
            <div class="container pt-3">
              <h3 class = "mb-2">Contract Details</h3>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="Position" class="form-control-label col-form-label-sm">Contract Details</label>
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <input name="current_cWorkSite" id="prev_cWorkSite" class = "form-control" readonly>
                      <small class="form-text">Work Site <span class="asterisk"></span></small>
                    </div>

                    <div class="col-md-4 mb-3">
                      <input name="prev_cPos" id="prev_cPos" class = "form-control" readonly>
                      <small class="form-text">Position <span class="asterisk"></span></small>
                    </div>

                    <div class="col-md-4 mb-3">
                      <input name="prev_contractStatus" id="prev_contractStatus" class="form-control" readonly>
                      <small class="form-text">Contract Status <span class="asterisk"></span></small>
                    </div>

                    <!-- <div class="col-md-4">
                      <input name="prev_cEmpLvl" id="prev_cEmpLvl" class = "form-control" readonly>
                      <small class="form-text">Employee Level <span class="asterisk"></span></small>
                    </div> -->
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" name = "prev_cStart" id = "prev_cStart" placeholder = "yyyy-mm-dd"  readonly>
                  <small class="form-text">Start Date <span class="asterisk"></span></small>
                </div>
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" name = "prev_cEnd" id = "prev_cEnd" placeholder="yyyy-mm-dd" readonly>
                  <small class="form-text">End Date <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-3">
                  <select name="prev_company" id="prev_company" class="form-control" disabled>
                    <option value="">------</option>
                    <?php if($companies->num_rows() > 0):?>
                      <?php foreach($companies->result_array() as $company):?>
                        <option value="<?=$company['id']?>"><?=$company['company']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                  <small class="form-text">Company <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-3">
                  <select name="prev_contract_type" id="prev_contract_type" class="form-control" disabled>
                    <option value="fixed">Fixed</option>
                    <option value="open">Open</option>
                  </select>
                  <small class="form-text">Contract Type <span class="asterisk"></span></small>
                </div>

                <div class="col-md-12 mt-4" id = "prev_contractDescription">
                  <!-- <textarea name="prev_contractDescription" id="prev_contractDescription" cols="30" rows="10" class="form-control" disabled>
                  </textarea>
                  <small class="form-text">Description <span class="asterisk"></span></small> -->
                </div>
              </div>
            </div>
            <!-- prev work schedule -->
            <div class="container pt-3">
              <h3 class = "mb-2">Work Schedule</h3>
              <div class="form-group row">
                <div class="col-md-4">
                  <label for="Type" class="form-control-label col-form-label-sm">Type</label>
                  <select name="" id="sched_type" class="form-control" disabled>
                    <option value="fix">Fixed Time</option>
                    <option value="flexi">Flexible Time</option>
                  </select>
                </div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered text-center" style = "border-top: 1px solid gainsboro;width:100%;">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Work Schedule</th>
                      <th>Break Schedule</th>
                      <th width = "100">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th>Mon</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_monTimeStart" readonly name = "prev_timeStartMon" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_monTimeEnd" readonly name = "prev_timeEndMon" class="form-control col-md-4 py-1 timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_monBreakStart" readonly name = "prev_breakStartMon" class="form-control offset-md-1 col-md-4 py-1 breakStart" >
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_monBreakEnd" readonly name = "prev_breakEndMon" class="form-control col-md-4 py-1 breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_monTimeTotal" readonly name = "prev_timeTotalMon" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly >
                      </td>
                    </tr>
                    <tr>
                      <th>Tue</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_tueTimeStart"readonly  name = "prev_timeStartTue" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart" >
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_tueTimeEnd" readonly name = "prev_timeEndTue" class="form-control col-md-4 py-1 timeWorkEnd" >
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_tueBreakStart" readonly name = "prev_breakStartTue" class="form-control offset-md-1 col-md-4 py-1 breakStart" >
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_tueBreakEnd" readonly name = "prev_breakEndTue" class="form-control col-md-4 py-1 breakEnd" >
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_tueTimeTotal" readonly name = "prev_timeTotalTue"class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly >
                      </td>
                    </tr>
                    <tr>
                      <th>Wed</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_wedTimeStart" readonly name = "prev_timeStartWed" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_wedTimeEnd" readonly name = "prev_timeEndWed" class="form-control col-md-4 py-1 timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_wedBreakStart" readonly name = "prev_breakStartWed" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_wedBreakEnd" readonly name = "prev_breakEndWed" class="form-control col-md-4 py-1 timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_wedTimeTotal" readonly name = "prev_timeTotalWed" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Thu</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_thuTimeStart" readonly name = "prev_timeStartThu" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_thuTimeEnd" readonly name = "prev_timeEndThu" class="form-control col-md-4 py-1 breakEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_thuBreakStart" readonly name = "prev_breakStartThu" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_thuBreakEnd" readonly name = "prev_breakEndThu" class="form-control col-md-4 py-1 breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_thuTimeTotal" readonly name = "prev_timeTotalThu" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Fri</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_friTimeStart" readonly name = "prev_timeStartFri" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_friTimeEnd" readonly name = "prev_timeEndFri" class="form-control col-md-4 py-1 timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_friBreakStart" readonly name = "prev_breakStartFri" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_friBreakEnd" readonly name = "prev_breakEndFri" class="form-control col-md-4 py-1 breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_friTimeTotal" readonly name = "prev_timeTotalFri" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Sat</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_satTimeStart" readonly name = "prev_timeStartSat" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_satTimeEnd" readonly name = "prev_timeEndSat" class="form-control col-md-4 py-1 timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_satBreakStart" readonly name = "prev_breakStartSat" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_satBreakEnd" readonly name = "prev_breakEndSat" class="form-control col-md-4 py-1 breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_satTimeTotal" readonly name= "timeTotalSat" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Sun</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_sunTimeStart" readonly name = "prev_timeStartSun" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_sunTimeEnd" readonly name = "prev_timeEndSun" class="form-control col-md-4 py-1 timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_sunBreakStart" readonly name = "prev_breakStartSun" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_sunBreakEnd" readonly name = "prev_breakEndSun" class="form-control col-md-4 py-1 breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_sunTimeTotal" readonly name = "prev_timeTotalSun" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- prev compensation schedule -->
            <div class="container pt-3">
              <h3 class = "mb-3">Salary</h3>
              <div id = "prevdivSalCat" class="form-group row mb-3">

                <div class="col-md-8">
                  <div class="table-responsive">
                    <table class="table table-bordered text-striped" style = "width:100%;border-top:1px solid gainsboro;">
                      <thead>
                        <th>Salary Category</th>
                        <th>Amount</th>
                      </thead>
                      <tbody id = "prev_sal_cat_ajax">

                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
              <h3>Compensation</h3>
              <div class="form-group row mb-3">
                <div class="col-md-4 mb-2">
                  <label for="sss" class = "form-control-label col-form-label-sm">SSS</label>
                  <input name="compSSS" id="prev_compSSS" class="form-control" readonly>
                  <small class="form-text">Range of Compensation <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Philhealth" class="form-control-label col-form-label-sm">Philhealth</label>
                  <input name="compPhilhealth" id="prev_compPhilhealth" class="form-control" readonly>
                  <small class="form-text">Basic Monthly Salary</small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Pag Ibig" class="form-control-label col-form-label-sm">Pag Ibig</label>
                  <input name="compPagIbig" id="prev_compPagIbig" class="form-control" readonly>
                  <small class="form-text">Monthly Compensation <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Tax" class="form-control-label col-form-label-sm">Tax</label>
                  <input name="compTax" id="prev_compTax" class="form-control" readonly>
                  <small class="form-text">Annual Income Bracket <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Tax" class="form-control-label col-form-label-sm">Pay Type</label>
                  <input name="compPayType" id="prev_compPayType" class="form-control" readonly>
                  <small class="form-text">Description <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Tax" class="form-control-label col-form-label-sm">Payout Medium</label>
                  <input name="compPayType" id="prev_pay_medium" class="form-control" readonly>
                  <small class="form-text">Description <span class="asterisk"></span></small>
                </div>
              </div>
              <h3>Leave</h3>
              <div class="form-group mb-3">
                <div class="col-md-8 px-0">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped" style = "border-top:1px solid gainsboro;">
                      <thead>
                        <th>Leave</th>
                        <th>Days</th>
                      </thead>

                      <tbody id = "prev_leave_tbl_ajax">

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn blue-grey" data-dismiss="modal">Close</button>
          </div>

        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/jquery.alphanum.js')?>"></script>
<script src = "<?=base_url('assets/js/contracts/contract_history.js')?>"></script>
