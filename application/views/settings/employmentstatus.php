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
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Employment Status</li>
        </ol>
    </div>

    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-header">
                        <div class="row">
                          <div class="col-lg-3">
                            <label class="form-control-label col-form-label-sm">Employment Status</label>
                            <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                          </div>

                          <div class="col-lg-4 offset-lg-5 text-right">
                            <button class="btn btn-primary btn-sm" id="btnSearchEmpStatus">Search</button>
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addEmpStatusModal" class="btn btn-primary btnClickAddArea">Add</button>
                          </div>
                        </div>
                      </div>
                        <!-- <div class="">
                            <div class="card-header d-flex align-items-center">

                                <div class="col-lg-12">
                                    <br>
                                    <div class="row">

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">Employment Status</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <!-- <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addEmpStatusModal" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:20px; width:8%">Add</button> -->
                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="EmpStatTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="60">ID</th>
                                            <th>Description</th>
                                            <th width = "120">Regular Holiday</th>
                                            <th width = "120">Special Non Working Holiday</th>
                                            <th width = "120">Leave</th>
                                            <th width="190">Action</th>
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

    <!-- Modal-->
    <div id="addEmpStatusModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add Employment Status</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="addEmpStatus-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="form-group row">
                                              <div class="col-md-12 mb-3">
                                                <label for="Employement Status" class="form-control-label col-form-label-sm">Employement Status</label>
                                                <input type="text" id = "addempStatus_desc" class="form-control req">
                                                <small class="form-text">Description <span class="asterisk"></span></small>
                                              </div>

                                              <div class="col-md-12 mb-3">
                                                <label for="Holiday Payment" class="form-control-label col-form-label-sm">Holiday Payment</label>
                                                <div class="row">
                                                  <div class="col-md-6">
                                                    <select name="reg_holiday" id="reg_holiday" class="form-control req">
                                                      <option value="no">No</option>
                                                      <option value="yes">Yes</option>
                                                    </select>
                                                    <small class="form-text">Regular Holiday <span class="asterisk"></span></small>
                                                  </div>

                                                  <div class="col-md-6">
                                                    <select name="reg_holiday" id="spec_holiday" class="form-control req">
                                                      <option value="no">No</option>
                                                      <option value="yes">Yes</option>
                                                    </select>
                                                    <small class="form-text">Special Non Working Holiday <span class="asterisk"></span></small>
                                                  </div>
                                                </div>

                                              </div>

                                              <div class="col-md-12">
                                                <label for="Leave" class="form-control-label col-form-label-sm">Leave Payment</label>
                                                <select name="add_leave" id="add_leave" class="form-control">
                                                  <option value="yes">Yes</option>
                                                  <option value="no">No</option>
                                                </select>
                                              </div>

                                              <!-- <div class="col-md-12"> -->
                                                <!-- <label for="Special Non Working Holiday" class="form-control-label col-form-label-sm">Special Non Working Holiday</label> -->

                                              <!-- </div> -->

                                                <!-- <label class="col-md-2 form-control-label">Employment Status <span class="asterisk"></span></label>
                                                <div class="col-md-10">
                                                    <input id="addempStatus_desc" type="text" class="form-control form-control-success" name="addempStatus_desc"><small class="form-text">Description</small>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="addEmpStatusBtn" style="float:right" class="btn btn-success addEmpStatusBtn">Add Employment Status</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="updateEmpStatusModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update Employment Status</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="updateEmpStatus-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="form-group row">
                                              <div class="col-md-12 mb-3">
                                                <label for="Employement Status" class="form-control-label col-form-label-sm">Employement Status</label>
                                                <input type="text" class="form-control" id="updateEmpStatus_desc">
                                                <input type="hidden" id = "current_desc">
                                                <small class="form-text">Description <span class="asterisk"></span></small>
                                              </div>

                                              <div class="col-md-12 mb-3">
                                                <label for="Holiday Payment" class="form-control-label col-form-label-sm">Holiday Payment</label>
                                                <div class="row">
                                                  <div class="col-md-6">
                                                    <select name="update_reg_holiday" id="update_reg_holiday" class="form-control">
                                                      <option value="no">No</option>
                                                      <option value="yes">Yes</option>
                                                    </select>
                                                    <small class="form-text">Regular Holiday <span class="asterisk"></span></small>
                                                  </div>

                                                  <div class="col-md-6">
                                                    <select name="update_spec_holiday" id="update_spec_holiday" class="form-control">
                                                      <option value="no">No</option>
                                                      <option value="yes">Yes</option>
                                                    </select>
                                                    <small class="form-text">Special Non Working Holiday <span class="asterisk"></span></small>
                                                  </div>
                                                </div>
                                              </div>

                                              <div class="col-md-12">
                                                <label for="Leave" class="form-control-label col-form-label-sm">Leave Payment</label>
                                                <select name="update_leave" id="update_leave" class="form-control">
                                                  <option value="yes">Yes</option>
                                                  <option value="no">No</option>
                                                </select>
                                              </div>
                                                <!-- <label class="col-md-2 form-control-label">Update Employment Status <span class="asterisk"></span></label>
                                                <div class="col-md-10">
                                                    <input type="hidden" name="info_areaId" class="info_areaId empstatusid" >
                                                    <input id="updateEmpStatus_desc" type="text" class="form-control form-control-success info_desc" name="updateEmpStatus_desc"><small class="form-text">Description</small>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="updateEmpStatusBtn" style="float:right" class="btn btn-primary updateEmpStatusBtn">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteEmpStatusModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete Employment Status</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record <br>(<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" id="delEmpStatus"class="del_areaId empstatusid" name="del_areaId" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" id="deleteEmpStatusBtn" style="float:right" class="btn btn-primary deleteEmpStatusBtn">Delete Employment Status</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/settings/employmentstatus.js');?>"></script>
