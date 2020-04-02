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
            <li class="breadcrumb-item active">PhilHealth</li>
        </ol>
    </div>

    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="row philHealthTbl_header">
                  <div class="col-md-12 text-right">
                    <button id="ctrlBtn" class="btn btn-primary">Controls</button>
                  </div>
                </div>

                <div class="row dataTable_header" style = "display:none;">
                  <div class="col-md-3">

                  </div>

                  <div class="col-md-4 offset-md-5 text-right">
                    <button id="addPHICbtn" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addPhilhealthModal" class="btn btn-primary">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                  <!-- <table class="table table-striped table-hover"> -->
                <div class="philHealthTbl">
                  <div class="text-right">
                    <!-- <button id="ctrlBtn" class="btn btn-primary">Controls</button> -->
                  </div>
                  <div class="table-responsive">
                    <table id="viewPHICtable" class="table table-striped table-hover table-bordered" style = "border-top:1px solid gainsboro;">
                      <thead>
                          <tr>
                              <th>Basic Monthly Salary</th>
                              <th>Monthly Contribution</th>
                              <th>Employee Share</th>
                              <th>Employer Share</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php foreach($philhealth as $phic): ?>
                              <tr>
                                  <td>
                                       <?php

                                          $sal1 = null;
                                          if($phic->basic_mo_sal1 > 40000) {
                                            $sal1 = "above";
                                          }else {
                                            $sal1 = $phic->basic_mo_sal1;
                                          }

                                       ?>

                                      <?= number_format($phic->basic_mo_sal,2) ?> - <?= (is_numeric($sal1)) ? number_format($sal1,2) : $sal1; ?>

                                  </td>
                                  <td>
                                      <?php if($phic->mo_contribution > 0 ): ?>
                                          <?= number_format($phic->mo_contribution,2) ?> - <?= number_format($phic->mo_contribution1,2) ?>
                                      <?php else: ?>
                                          <?= number_format($phic->mo_contribution1,2) ?>
                                      <?php endif; ?>

                                  </td>
                                  <td>
                                      <?php if($phic->employee_share > 0): ?>
                                          <?= number_format($phic->employee_share,2) ?> - <?= number_format($phic->employee_share1,2) ?>
                                      <?php else: ?>
                                          <?= number_format($phic->employee_share1,2) ?>
                                      <?php endif; ?>
                                  </td>
                                  <td>
                                      <?php if($phic->employer_share > 9): ?>
                                          <?= number_format($phic->employer_share,2) ?> - <?= number_format($phic->employer_share1,2) ?>
                                      <?php else: ?>
                                          <?= number_format($phic->employer_share1,2) ?>
                                      <?php endif; ?>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="philHealthDataTbl" style = "display:none;">
                  <!-- <div class="text-right">
                    <button id="addPHICbtn" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addPhilhealthModal" class="btn btn-primary">Add</button>
                  </div> -->
                  <div class="table-responsive">
                    <table class="table  table-striped table-hover table-bordered" id="PHtable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th width="80">Basic Monthly Salary From</th>
                                <th width="80">Basic Monthly Salary To</th>
                                <th width="80">Monthly Contribution From</th>
                                <th width="80">Monthly Contribution To</th>
                                <th width="80">Employee Share From</th>
                                <th width="80">Employee Share To</th>
                                <th width="80">Employer Share From</th>
                                <th width="80">Employer Share To</th>
                                <th width="130">Action</th>
                            </tr>
                        </thead>
                    </table>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Modal-->
    <div id="addPhilhealthModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="addPhilhealth-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body">
                                    <!-- <div class="row">
                                      <div class="col-6 text-center">
                                        <h4>From</h4>
                                      </div>

                                      <div class="col-6 text-center">
                                        <h4>To</h4>
                                      </div>
                                    </div> -->
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Basic Monthly Salary<span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="addBasicSal1" type="number" class="form-control form-control-success addBasicDesc" name="addBasicDesc">
                                            <small class="form-text">From</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="addBasicSal2" type="number" class="form-control form-control-success addBasicDesc" name="addBasicDesc">
                                            <small class="form-text">To</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Monthly Contribution<span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="addMonthlyCon1" type="number" class="form-control form-control-success addMonthlyDesc" name="addMonthlyDesc">
                                            <small class="form-text">From</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="addMonthlyCon2" type="number" class="form-control form-control-success addMonthlyDesc" name="addMonthlyDesc">
                                            <small class="form-text">To</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Employee Share<span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="addEmployeeShare1" type="number" class="form-control form-control-success addEmployeeShare" name="addEmployeeShare">
                                            <small class="form-text">From</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="addEmployeeShare2" type="number" class="form-control form-control-success addEmployeeShare" name="addEmployeeShare">
                                            <small class="form-text">To</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Employer Share <span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="addEmployerShare1" type="number" class="form-control form-control-success addEmployerShare" name="addEmployerShare">
                                        </div>
                                        <div class="col-md-6">
                                            <input id="addEmployerShare2" type="number" class="form-control form-control-success addEmployerShare" name="addEmployerShare">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="addPhilHealth" style="float:right" class="btn btn-primary addPhilHealth">Add PhilHealth</button>
                                <!-- <button type="button" id="addPhilHealth" style="float:right" class="btn btn-success addPhilHealth">Add</button> -->
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editPhilhealthModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update PhilHealth</h4>
                    <h4 id="exampleModalLabel" class="modal-title">Update Philhealth</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="editPhilHealth-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Basic Monthly Salary <span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="editBasicSal1" type="number" class="form-control form-control-success editBasicDesc" name="addBasicDesc">
                                        </div>
                                        <div class="col-md-6">
                                            <input id="editBasicSal2" type="number" class="form-control form-control-success editBasicDesc" name="addBasicDesc">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Monthly Contribution <span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="editMonthlyCon1" type="number" class="form-control form-control-success editMonthlyDesc" name="addMonthlyDesc">
                                        </div>
                                        <div class="col-md-6">
                                            <input id="editMonthlyCon2" type="number" class="form-control form-control-success editMonthlyDesc" name="addMonthlyDesc">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Employee Share <span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="editEmployeeShare1" type="number" class="form-control form-control-success editEmployeeShare" name="addEmployeeShare">
                                        </div>
                                        <div class="col-md-6">
                                             <input id="editEmployeeShare2" type="number" class="form-control form-control-success editEmployeeShare" name="addEmployeeShare">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <label>Employer Share <span class="asterisk"></span></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <input id="editEmployerShare1" type="number" class="form-control form-control-success editEmployerShare" name="addEmployerShare">
                                            <input type="hidden" class="philhealthid">
                                        </div>
                                        <div class="col-md-6">
                                            <input id="editEmployerShare2" type="number" class="form-control form-control-success editEmployerShare" name="addEmployerShare">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="editPhilhealthBtn" style="float:right" class="btn btn-primary editPhilhealthBtn">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deletePhilHealtModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete Area</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="deletePhilhealth-form">
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
                                <button type="submit" id="deletePhilHealthBtn" style="float:right" class="btn btn-primary deletePhilHealthBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/settings/philhealth.js');?>"></script>
