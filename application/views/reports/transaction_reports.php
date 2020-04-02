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
  th, td{
    vertical-align: middle !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Transaction Reports</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <!-- search filter -->
                <div class="form-group row">

                  <div class="col-md-3">
                    <div class="row">
                      <!-- filter  -->
                      <div class="col-md-12 mb-3">
                        <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                        <select name="" id="filter_by" class="form-control">
                          <option value="by_add_pay">Additional Pays</option>
                          <option value="by_ca">Cash Advance</option>
                          <option value="by_leave">Leave</option>
                          <option value="by_offset">Offset</option>
                          <option value="by_overtimepays">Overtime Pays</option>
                          <option value="by_saldeduct">Salary Deduction</option>
                          <option value="by_wOrder">Work Order</option>
                          <option value="by_worksched">Work Schedule</option>
                        </select>
                      </div>
                      <!-- filter2  -->
                      <div class="col-md-12 mb-3">
                        <select name="" id="filter_by2" class="form-control">
                          <option value="">------</option>
                          <option value="by_id">Employee ID</option>
                          <option value="by_name">Employee Name</option>
                          <option value="by_dept">Department</option>
                          <!-- <option value="by_position">Position</option> -->
                        </select>
                      </div>

                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="row">
                      <!-- filter -->
                      <div class="col-md-12 mb-3">
                        <div id="divAddPay" class = "filter_div active">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text"  class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                            </div>
                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                            </div>
                          </div>
                        </div>

                        <div id="divCa" class = "filter_div" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text"  class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                            </div>
                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                            </div>
                          </div>
                        </div>

                        <div id="divLeave" class="filter_div" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text"  class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                            </div>
                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                            </div>
                          </div>
                        </div>

                        <div id="divOvertimePays" class="filter_div" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text"  class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                            </div>
                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                            </div>
                          </div>
                        </div>

                        <div id="divSalDeduct" class = "filter_div" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text"  class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                            </div>
                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                            </div>
                          </div>
                        </div>

                        <div id="divWorkOrder" class = "filter_div" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="form-control date_input from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to">
                            </div>
                          </div>
                        </div>

                        <div id="divOffset" class = "filter_div" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="form-control date_input from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to">
                            </div>
                          </div>
                        </div>

                        <div id="divWorkSchedule" class = "filter_div" style = "display:none;">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="From" class="form-control-label col-form-label-sm">From</label>
                              <input type="text" class="form-control date_input from">
                            </div>

                            <div class="col-md-6">
                              <label for="To" class="form-control-label col-form-label-sm">To</label>
                              <input type="text" class="form-control date_input to">
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- filter 2 -->
                      <div class="col-md-12 mb-3">
                        <div id="divEmpty" class="filter_div2 active">
                          <!-- <input type="text" class="form-control" readonly> -->
                        </div>

                        <div id="divID" class = "filter_div2" style = "display:none;">
                          <input type="text" class="form-control searchArea2" placeholder="Ex.1010101">
                        </div>

                        <div id="divName" class = "filter_div2" style = "display:none;">
                          <input type="text" class="form-control searchArea2" placeholder="Ex. John Doe">
                        </div>

                        <div id="divDept" class = "filter_div2 searchArea2" style = "display:none;">
                          <select name="" id="" class="form-control searchArea2 select2">
                            <?php if($departments->num_rows() > 0):?>
                              <?php foreach($departments->result_array() as $dept):?>
                                <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                              <?php endforeach;?>
                            <?php endif?>
                          </select>
                        </div>

                        <div id="divPos" class="filter_div2" style = "display:none;">
                          <select name="" id="" class = "form-control searchArea2 select2">
                            <?php if($positions->num_rows() > 0):?>
                              <?php foreach($positions->result_array() as $pos):?>
                                <option value="<?=$pos['positionid']?>">(<?=$pos['dept']?>) <?=$pos['description']?></option>
                              <?php endforeach;?>
                            <?php endif;?>
                          </select>
                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>

                </div>
                <!-- radio button -->
                <div class="form-group row">
                  <div class="col-md-12">
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" name = "tran_status" class="form-check-input tran_status"  value="certified" checked>Certified
                      </label>
                    </div>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" name = "tran_status" class="form-check-input tran_status" value="approved">Approved
                      </label>
                    </div>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" name = "tran_status" class="form-check-input tran_status" value="waiting" >Waiting for Approval
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive"  id = "tbl_ajax">
                  <table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">
                    <thead>
                      <th>Employee ID</th>
                      <th>Employee Name</th>
                      <th>Department</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Created by</th>
                      <th>Approved by</th>
                      <th>Certified by</th>
                      <th>Status</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- UPDATE MODAL -->
    <div class="modal fade" id = "update_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Work Schedule</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="update_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12">
                <label for="Work Schedule" class="form-control-label col-form-label-sm">Work Schedule <span class="asterisk"></span></label>
                <div class="table-responsive">
                  <table class="table table-bordered" style ="border:1px solid gainsboro;">
                    <thead>
                      <th>Day</th>
                      <th>Time In</th>
                      <th>Time Out</th>
                      <th>Break In</th>
                      <th>Break Out</th>
                      <th>Total</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Mon</td>
                        <td><input type="time" id = "edit_mon_ti" name = "edit_mon_ti" class="form-control edit_in edit_init"></td>
                        <td><input type="time" id = "edit_mon_to" name = "edit_mon_to" class="form-control edit_out edit_init"></td>
                        <td><input type="time" id = "edit_mon_bi" name = "edit_mon_bi" class="form-control edit_bi edit_init"></td>
                        <td><input type="time" id = "edit_mon_bo" name = "edit_mon_bo" class="form-control edit_bout edit_init"></td>
                        <td><input type="text" id = "edit_mon_total" name = "edit_mon_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Tue</td>
                        <td><input type="time" id = "edit_tue_ti" name = "edit_tue_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_tue_to" name = "edit_tue_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_tue_bi" name = "edit_tue_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_tue_bo" name = "edit_tue_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_tue_total" name = "edit_tue_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Wed</td>
                        <td><input type="time" id = "edit_wed_ti" name = "edit_wed_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_wed_to" name = "edit_wed_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_wed_bi" name = "edit_wed_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_wed_bo" name = "edit_wed_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_wed_total" name = "edit_wed_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Thu</td>
                        <td><input type="time" id = "edit_thu_ti" name = "edit_thu_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_thu_to" name = "edit_thu_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_thu_bi" name = "edit_thu_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_thu_bo" name = "edit_thu_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_thu_total" name = "edit_thu_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Fri</td>
                        <td><input type="time" id = "edit_fri_ti" name = "edit_fri_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_fri_to" name = "edit_fri_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_fri_bi" name = "edit_fri_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_fri_bo" name = "edit_fri_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_fri_total" name = "edit_fri_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Sat</td>
                        <td><input type="time" id = "edit_sat_ti" name = "edit_sat_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_sat_to" name = "edit_sat_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_sat_bi" name = "edit_sat_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_sat_bo" name = "edit_sat_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_sat_total" name = "edit_sat_total" class="form-control edit_total" readonly></td>
                      </tr>
                      <tr>
                        <td>Sun</td>
                        <td><input type="time" id = "edit_sun_ti" name = "edit_sun_ti" class="form-control edit_in"></td>
                        <td><input type="time" id = "edit_sun_to" name = "edit_sun_to" class="form-control edit_out"></td>
                        <td><input type="time" id = "edit_sun_bi" name = "edit_sun_bi" class="form-control edit_bi"></td>
                        <td><input type="time" id = "edit_sun_bo" name = "edit_sun_bo" class="form-control edit_bout"></td>
                        <td><input type="text" id = "edit_sun_total" name = "edit_sun_total" class="form-control edit_total" readonly></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/reports/transaction_reports.js')?>"></script>
