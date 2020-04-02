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
  .bordered{
    border: 1px solid gainsboro;
    padding: 3px;
  }

  .dropdown-menu{
    border: 1px solid #999;
    border-top:none;
  }

  .border-bottom{
    border-bottom: 1px solid gainsboro !important;
    padding-bottom: 5px;
    font-weight: bold;
  }

  .signature{
    border: none !important;
    border-bottom: 1px solid gainsboro !important;
  }
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/custom_loader2.css')?>">
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Incident Reports <?=$this->session->emp_idno?></li>
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
                      <option value="by_dept">Department</option>
                      <option value="by_date">Date Reported</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divName" class = "filter_div single_date active" style = "display:;">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divDept" class="filter_div single_date" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divDate" class = "filter_div range_date" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
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
                    <button class="btn btn-primary" id = "btn_add">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "incident_report_tbl">
                    <thead>
                      <th>Subject Employee</th>
                      <th>Date Reported</th>
                      <th>Department</th>
                      <th>Reporting Dept</th>
                      <th>Concerned Dept</th>
                      <th>Hr Dept</th>
                      <th>Accounting Dept</th>
                      <th width = "190">Action</th>
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
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title font-weight-bold"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="incident_form">
            <div class="modal-body">
              <div class="row">
                <div class="col-12 text-center">
                  <h3 class = "font-weight-bold">Incident Report</h3>
                  <hr>
                </div>

                <div class="col-md-7">
                  <div class="col-12">
                    <label for="Subject Employee(s)" class="form-control-label col-form-label-sm">Subject Employee(s): <span class="asterisk"></span></label>
                    <div class="dropdown" >
                      <input type="text" id = "employee" name = "employee" class="form-control dropdown-toggle rq" data-toggle = "dropdown">
                      <input type="hidden" id = "employee_idno" name = "employee_idno">
                      <input type="hidden" id = "reporting_dept_head_id" name = "reporting_dept_head_id" value = "">
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

                  <div class="col-12 row">
                    <div class="col-md-6">
                      <label for="Position" class="form-control-label col-form-label-sm">Position: <span class="asterisk"></span></label>
                      <select name="position" id="position" class="form-control rq">
                        <option value="">------</option>
                        <?php if($positions->num_rows() > 0):?>
                          <?php foreach($positions->result_array() as $row):?>
                            <option value="<?=$row['positionid']?>"><?=$row['description']?> (<?=$row['dept']?>)</option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label for="Department" class="form-control-label col-form-label-sm">Department: <span class="asterisk"></span></label>
                      <select name="department" id="department" class="form-control rq">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="col-12">
                    <label for="Date Reported" class="form-control-label col-form-label-sm">Date Reported: <span class="asterisk"></span></label>
                    <input id = "date_reported" name = "date_reported" type="text" class="form-control date_input rq">
                  </div>

                  <div class="col-12">
                    <label for="Reported By" class="form-control-label col-form-label-sm">Reported By: <span class="asterisk"></span></label>
                    <div class="dropdown" >
                      <input type="text" id = "reported_by" name = "reported_by" class="form-control dropdown-toggle2 rq" data-toggle = "dropdown">
                      <input type="hidden" id = "reported_id" name = "reported_id">
                      <input type="hidden" id = "concerned_dept_head_id" name = "concerned_dept_head_id">
                      <div class="dropdown-menu form-control">
                        <div class="loader_wrapper2" style = "display:none;">
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
                        <div id = "result_wrapper2">

                        </div>
                      </div>
                    </div>
                    <!-- <input type="text" class="form-control"> -->
                  </div>
                </div>

                <div class="col-12">
                  <hr>
                </div>

                <div class="col-md-6">
                  <label for="Place Of Incidence" class="form-control-label col-form-label-sm">Place Of Incidence: <span class="asterisk"></span></label>
                  <input id = "place_of_incidence" name = "place_of_incidence" type="text" class="form-control rq">
                </div>

                <div class="col-md-6 row">
                  <div class="col-md-6">
                    <label for="Date Happened" class="form-control-label col-form-label-sm">Date Happened: <span class="asterisk"></span></label>
                    <input id = "date_happened" name = "date_happened" type="text" class="form-control date_input rq">
                  </div>
                  <div class="col-md-6">
                    <label for="Appox. Time of Incidence" class="form-control-label col-form-label-sm">Appox. Time of Incidence:</label>
                    <input id = "time_of_incidence" name = "time_of_incidence" type="time" class="form-control rq">
                  </div>
                </div>

                <div class="col-12">
                  <hr>
                  <label for="Resulting Damage Or Injuries" class="form-control-label col-form-label-sm">Resulting Damage Or Injuries: <span class="asterisk"></span></label>
                  <input id = "resulting_damage" name = "resulting_damage" type="text" class="form-control rq">
                </div>

                <div class="col-12">
                  <label for="Incident Brief" class="form-control-label col-form-label-sm">Incident Brief: <span class="asterisk"></span></label>
                  <textarea id = "incident_brief" name="incident_brief" rows="4" cols="80" class = "form-control rq"></textarea>
                </div>

              </div>

              <div class="form-group row">
                <div class="col-12">
                  <hr>
                </div>
                <!-- REPORTING DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">Reporting Departments</h5>
                    </div>
                    <div class="col-12 text-center">
                      <input id = "reporting_dept_head" name = "reporting_dept_head" type="text" class="form-control signature">
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                  </div>
                  </div>
                </div>
                <!-- CONCERNED DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">Concerned Departments</h5>
                    </div>
                    <div class="col-12 text-center">
                      <input id = "concerned_dept_head" name = "concerned_dept_head" type="text" class="form-control signature">
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                  </div>
                  </div>
                </div>
                <!-- HR DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">HR Departments</h5>
                    </div>
                    <div class="col-12 text-center">
                      <input id = "hr_dept_head" name = "hr_dept_head" type="text" class="form-control signature">
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                  </div>
                  </div>
                </div>
                <!-- ACCOUNTING DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">Accounting Departments</h5>
                    </div>
                    <div class="col-12 text-center">
                      <input id = "accounting_dept_head" name = "accounting_dept_head" type="text" class="form-control signature">
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_save">Save</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- EDIT MODAL -->
    <div class="modal fade" id = "edit_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title font-weight-bold"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_incident_form">
            <div class="modal-body">
              <div class="row">
                <div class="col-12 text-center">
                  <h3 class = "font-weight-bold">Incident Report</h3>
                  <hr>
                </div>

                <div class="col-md-7">
                  <div class="col-12">
                    <label for="Subject Employee(s)" class="form-control-label col-form-label-sm">Subject Employee(s): <span class="asterisk"></span></label>
                    <div class="dropdown" >
                      <input type="text" id = "edit_employee" name = "edit_employee" class="form-control edit_dropdown-toggle rq2" data-toggle = "dropdown">
                      <input type="hidden" id = "edit_employee_idno" name = "edit_employee_idno">
                      <input type="hidden" id = "edit_reporting_dept_head_id" name = "edit_reporting_dept_head_id" value = "">
                      <input type="hidden" id = "uid" name = "uid" value = "">
                      <div class="dropdown-menu form-control">
                        <div class="edit_loader_wrapper" style = "display:none;">
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
                        <div id = "edit_result_wrapper">

                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 row">
                    <div class="col-md-6">
                      <label for="Position" class="form-control-label col-form-label-sm">Position: <span class="asterisk"></span></label>
                      <select name="edit_position" id="edit_position" class="form-control rq2">
                        <option value="">------</option>
                        <?php if($positions->num_rows() > 0):?>
                          <?php foreach($positions->result_array() as $row):?>
                            <option value="<?=$row['positionid']?>"><?=$row['description']?> (<?=$row['dept']?>)</option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label for="Department" class="form-control-label col-form-label-sm">Department: <span class="asterisk"></span></label>
                      <select name="edit_department" id="edit_department" class="form-control rq2">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="col-12">
                    <label for="Date Reported" class="form-control-label col-form-label-sm">Date Reported: <span class="asterisk"></span></label>
                    <input id = "edit_date_reported" name = "edit_date_reported" type="text" class="form-control date_input_empty rq2">
                  </div>

                  <div class="col-12">
                    <label for="Reported By" class="form-control-label col-form-label-sm">Reported By: <span class="asterisk"></span></label>
                    <div class="dropdown" >
                      <input type="text" id = "edit_reported_by" name = "edit_reported_by" class="form-control edit_dropdown-toggle2 rq2" data-toggle = "dropdown">
                      <input type="hidden" id = "edit_reported_id" name = "edit_reported_id">
                      <input type="hidden" id = "edit_concerned_dept_head_id" name = "edit_concerned_dept_head_id">
                      <div class="dropdown-menu form-control">
                        <div class="edit_loader_wrapper2" style = "display:none;">
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
                        <div id = "edit_result_wrapper2">

                        </div>
                      </div>
                    </div>
                    <!-- <input type="text" class="form-control"> -->
                  </div>
                </div>

                <div class="col-12">
                  <hr>
                </div>

                <div class="col-md-6">
                  <label for="Place Of Incidence" class="form-control-label col-form-label-sm">Place Of Incidence: <span class="asterisk"></span></label>
                  <input id = "edit_place_of_incidence" name = "edit_place_of_incidence" type="text" class="form-control rq2">
                </div>

                <div class="col-md-6 row">
                  <div class="col-md-6">
                    <label for="Date Happened" class="form-control-label col-form-label-sm">Date Happened: <span class="asterisk"></span></label>
                    <input id = "edit_date_happened" name = "edit_date_happened" type="text" class="form-control date_input_empty rq2">
                  </div>
                  <div class="col-md-6">
                    <label for="Appox. Time of Incidence" class="form-control-label col-form-label-sm">Appox. Time of Incidence:</label>
                    <input id = "edit_time_of_incidence" name = "edit_time_of_incidence" type="time" class="form-control rq2">
                  </div>
                </div>

                <div class="col-12">
                  <hr>
                  <label for="Resulting Damage Or Injuries" class="form-control-label col-form-label-sm">Resulting Damage Or Injuries: <span class="asterisk"></span></label>
                  <input id = "edit_resulting_damage" name = "edit_resulting_damage" type="text" class="form-control rq2">
                </div>

                <div class="col-12">
                  <label for="Incident Brief" class="form-control-label col-form-label-sm">Incident Brief: <span class="asterisk"></span></label>
                  <textarea id = "edit_incident_brief" name="edit_incident_brief" rows="4" cols="80" class = "form-control rq2"></textarea>
                </div>

              </div>

              <div class="form-group row">
                <div class="col-12">
                  <hr>
                </div>
                <!-- REPORTING DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">Reporting Departments</h5>
                    </div>
                    <div class="col-12 text-center" >
                      <input id = "edit_reporting_dept_head" name = "edit_reporting_dept_head" type="text" class="form-control signature" readonly>
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                    <div class="col-12 text-center head_wrapper" id = "rd_wrapper" style = "display:none;">
                      <button type = "button" class="btn btn-primary btn_approve" data-id = "" data-act = "rd" id = "btn_approve_rd">Approve</button>
                    </div>
                  </div>
                  </div>
                </div>
                <!-- CONCERNED DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">Concerned Departments</h5>
                    </div>
                    <div class="col-12 text-center">
                      <input id = "edit_concerned_dept_head" name = "edit_concerned_dept_head" type="text" class="form-control signature" readonly>
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                    <div class="col-12 text-center head_wrapper" id = "cd_wrapper" style = "display:none;">
                      <button type = "button" class="btn btn-primary btn_approve" data-id = "" data-act = "cd" id = "btn_approve_cd">Approve</button>
                    </div>
                  </div>
                  </div>
                </div>
                <!-- HR DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">HR Departments</h5>
                    </div>
                    <div class="col-12 text-center">
                      <input id = "edit_hr_dept_head" name = "edit_hr_dept_head" type="text" class="form-control signature" readonly>
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                    <div class="col-12 text-center head_wrapper" id = "hr_wrapper" style = "display:none;">
                      <button type = "button" class="btn btn-primary btn_approve" data-id = "" data-act = "hr" id = "btn_approve_hr">Approve</button>
                    </div>
                  </div>
                  </div>
                </div>
                <!-- ACCOUNTING DEPARTMENT -->
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="col-12 bordered">
                  <div class="form-group row ">
                    <div class="col-12 text-center">
                      <h5 class = "border-bottom">Accounting Departments</h5>
                    </div>
                    <div class="col-12 text-center">
                      <input id = "edit_accounting_dept_head" name = "edit_accounting_dept_head" type="text" class="form-control signature" readonly>
                      <small class="form-text">Immediate Head/Sign/Date</small>
                    </div>
                    <div class="col-12 text-center head_wrapper" id = "ac_wrapper" style = "display:none;">
                      <button type = "button" class="btn btn-primary btn_approve" data-id = "" data-act = "ac" id = "btn_approve_ac">Approve</button>
                    </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_update">Update</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- DELETE MODAL -->
    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Delete Incident Reports</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <h4>Are you sure you want to delete this ?</h4>
            <input type="hidden" id = "delid" name = "delid">
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\reports\incident_reports.js')?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
