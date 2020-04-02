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
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">SSS</li>
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
                  <div class="col-md-12 text-right">
                    <button class="btn btn-primary btn-sm" id = "btn_add_sss">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id = "sss_tbl" class="table table-bordered table-striped text-center" style ="border-top:1px solid gainsboro;">
                    <thead >
                      <tr>
                          <th style = "vertical-align:middle;" rowspan = "3" class="" width="20%">RANGE OF COMPENSATION</th>
                          <th style = "vertical-align:middle;" rowspan = "3" class="">MONTHLY<br>SALARY<br>CREDIT</th>
                          <th colspan="7" class="">EMPLOYER - EMPLOYEE</th>
                          <th></th>
                      </tr>
                      <tr>
                          <!-- <th class="">&nbsp;</th>
                          <th class="">&nbsp;</th> -->
                          <th colspan="3" class="">SOCIAL SECURITY</th>
                          <th colspan="1" class="">EC</th>
                          <th colspan="3" class="">TOTAL CONTRIBUTION</th>
                          <th>Action</th>
                          <!-- <th style = "vertical-align:middle;" class = "">TOTAL CONTRIBUTION</th> -->
                      </tr>
                      <tr>
                          <!-- <th class="">&nbsp;</th>
                          <th class="">&nbsp;</th> -->
                          <th class="">ER</th>
                          <th class="">EE</th>
                          <th class="">Total</th>
                          <th class="">ER</th>
                          <th class="">ER</th>
                          <th class="">EE</th>
                          <th class="">Total</th>
                          <th></th>
                      </tr>
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
    <div class="modal fade" id = "add_sss_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New SSS</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <!-- RANGE OF COMPENSATION -->
            <div class="form-group row">
              <div class="col-12">
                <label for="Range Of Compensation" class="form-control-label col-form-label-sm">Range Of Compensation</label>
              </div>
              <div class="col-md-6">
                <input id = "range_from" type="text" class = "form-control negativeNumbers rq">
                <small class="form-text">From <span class="asterisk"></span></small>
              </div>
              <div class="col-md-6">
                <input id = "range_to" type="text" class = "form-control positiveNumbers rq">
                <small class="form-text">To <span class="asterisk"></span></small>
              </div>
            </div>
            <!-- MONTHLY SALARY CREDIT -->
            <div class="form-group row">
              <div class="col-6">
                <label for="Monthly Salary Credit" class="form-control-label col-form-label-sm">Monthly Salary Credit</label>
                <input id = "monthly_cred" type="number" class="form-control rq">
              </div>
            </div>
            <!-- SOCIAL SECURITY -->
            <div class="form-group row">
              <div class="col-12">
                <label for="Social Security" class="form-control-label col-form-label-sm">Social Security</label>
              </div>
              <div class="col-md-4">
                <input id = "sss_er" type="number" class="form-control rq">
                <small class="form-text">ER <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id = "sss_ee" type="number" class="form-control rq">
                <small class="form-text">EE <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id = "sss_total" type="number" class="form-control rq">
                <small class="form-text">Total <span class="asterisk"></span></small>
              </div>
            </div>
            <!-- EC -->
            <div class="form-group row">
              <div class="col-6">
                <!-- <label for="EC" class="form-control-label col-form-label-sm">EC <span class="asterisk"></span></label> -->
                <input id = "ec" type="number" class="form-control rq">
                <small class="form-text">EC <span class="asterisk"></span></small>
              </div>
            </div>
            <!-- TOTAL CONTRIBUTION -->
            <div class="form-group row">
              <div class="col-12">
                <label for="Total Contribution" class="form-control-label col-form-label-sm">Total Contribution</label>
              </div>
              <div class="col-md-4">
                <input id = "tc_er" type="number" class="form-control rq">
                <small class="form-text">ER <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id = "tc_ee" type="number" class="form-control rq">
                <small class="form-text">EE <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id= "tc_total" type="number" class="form-control rq">
                <small class="form-text">Total <span class="asterisk"></span></small>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button id = "btn_save_sss" class="btn btn-sm btn-primary">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- UPDATE MODAL -->
    <div class="modal fade" id = "update_sss_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit SSS</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <!-- RANGE OF COMPENSATION -->
            <div class="form-group row">
              <div class="col-12">
                <label for="Range Of Compensation" class="form-control-label col-form-label-sm">Range Of Compensation</label>
              </div>
              <div class="col-md-6">
                <input id = "edit_range_from" type="text" class = "form-control negativeNumbers rq2">
                <input type="hidden" id = "uid">
                <input type="hidden" id = "prevFrom">
                <input type="hidden" id = "prevTo">
                <small class="form-text">From <span class="asterisk"></span></small>
              </div>
              <div class="col-md-6">
                <input id = "edit_range_to" type="text" class = "form-control positiveNumbers rq2">
                <small class="form-text">To <span class="asterisk"></span></small>
              </div>
            </div>
            <!-- MONTHLY SALARY CREDIT -->
            <div class="form-group row">
              <div class="col-6">
                <label for="Monthly Salary Credit" class="form-control-label col-form-label-sm">Monthly Salary Credit</label>
                <input id = "edit_monthly_cred" type="number" class="form-control rq2">
              </div>
            </div>
            <!-- SOCIAL SECURITY -->
            <div class="form-group row">
              <div class="col-12">
                <label for="Social Security" class="form-control-label col-form-label-sm">Social Security</label>
              </div>
              <div class="col-md-4">
                <input id = "edit_sss_er" type="number" class="form-control rq2">
                <small class="form-text">ER <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id = "edit_sss_ee" type="number" class="form-control rq2">
                <small class="form-text">EE <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id = "edit_sss_total" type="number" class="form-control rq2">
                <small class="form-text">Total <span class="asterisk"></span></small>
              </div>
            </div>
            <!-- EC -->
            <div class="form-group row">
              <div class="col-6">
                <!-- <label for="EC" class="form-control-label col-form-label-sm">EC <span class="asterisk"></span></label> -->
                <input id = "edit_ec" type="number" class="form-control rq2">
                <small class="form-text">EC <span class="asterisk"></span></small>
              </div>
            </div>
            <!-- TOTAL CONTRIBUTION -->
            <div class="form-group row">
              <div class="col-12">
                <label for="Total Contribution" class="form-control-label col-form-label-sm">Total Contribution</label>
              </div>
              <div class="col-md-4">
                <input id = "edit_tc_er" type="number" class="form-control rq2">
                <small class="form-text">ER <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id = "edit_tc_ee" type="number" class="form-control rq2">
                <small class="form-text">EE <span class="asterisk"></span></small>
              </div>
              <div class="col-md-4">
                <input id= "edit_tc_total" type="number" class="form-control rq2">
                <small class="form-text">Total <span class="asterisk"></span></small>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_update_sss">Update</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- DELETE MODAL -->
    <div id="delete_sss_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
      <div role="document" class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 id="exampleModalLabel" class="modal-title">Delete Area</h4>
              <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
          </div>
          <div class="modal-body">
            <div class="">
              <div class="row">
                <div class="col-lg-12">
                  <p>Are you sure you want to delete the record ?</p>
                  <input type="hidden" id="phID" class="del_areaId phID" name="del_areaId phID" value="">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="form-group row">
              <div class="col-md-12">
                <button type="submit" id="btn_yes_del" style="float:right" class="btn btn-primary deletePhilHealthBtn">Delete Record</button>
                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- <div class="modal fade" id = "delete_sss_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record ?</p>
                <input type="hidden" class="employeeid">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-danger" id = "btn_yes_del">Delete</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div> -->
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\sss2.js')?>"></script>
