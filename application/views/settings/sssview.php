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
            <li class="breadcrumb-item active">SSS</li>
        </ol>
    </div>

    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="row sssTbl_header">
                  <div class="col-12 text-right">
                    <button id="addBtnControls" class="btn btn-primary btnClickAddArea">Controls</button>
                  </div>
                </div>

                <div class="row dataTbl_header" style = "display:none;">
                  <div class="col-md-3">

                  </div>

                  <div class="col-md-4 offset-md-5 text-right">
                    <button id="addBtn" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addContributionModal" class="btn btn-primary btnClickAddArea">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                  <!-- <table class="table table-striped table-hover"> -->
                <div class="sssTable">
                  <div class="table-responsive">
                    <table id="sssViewOnly" class="table table-bordered table-striped table-hover text-center" style ="border-top:1px solid gainsboro;">
                      <thead >
                        <tr>
                            <th style = "vertical-align:middle;" rowspan = "3" class="" width="20%">RANGE OF COMPENSATION</th>
                            <th style = "vertical-align:middle;" rowspan = "3" class="">MONTHLY<br>SALARY<br>CREDIT</th>
                            <th colspan="7" class="">EMPLOYER - EMPLOYEE</th>
                            <th class="">SE/VM/OFW</th>
                        </tr>
                        <tr>
                            <!-- <th class="">&nbsp;</th>
                            <th class="">&nbsp;</th> -->
                            <th colspan="3" class="">SOCIAL SECURITY</th>
                            <th colspan="1" class="">EC</th>
                            <th colspan="3" class="">TOTAL CONTRIBUTION</th>
                            <th style = "vertical-align:middle;" rowspan = "2" class = "">TOTAL CONTRIBUTION</th>
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
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($sssTable as $sss): ?>
                            <tr>
                                <td class="tableContent">
                                    <?= number_format($sss->salRange_from,2) ?> - <?= number_format($sss->salRange_to,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= number_format($sss->monthly_sal_cred,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= number_format($sss->ss_er,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= number_format($sss->ss_ee,2) ?>
                                </td>
                                 <td class="tableContent">
                                    <?= number_format($sss->ss_total,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= number_format($sss->ec_er,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= number_format($sss->tc_er,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= number_format($sss->tc_ee,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= number_format($sss->tc_total,2) ?>
                                </td>
                                <td class="tableContent">
                                    <?= ($sss->SV_VM_OFW == 0) ? number_format((float)$sss->ss_er + (float)$sss->ss_ee,2): number_format($sss->SV_VM_OFW,2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="sssDataTable" style = "display:none;">
                  <!-- <div class="sssDataTableBtnWrapper text-right">
                    <button id="addBtn" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addContributionModal" class="btn btn-primary btnClickAddArea">Add</button>
                  </div> -->
                  <div class="table-responsive">
                    <table id="editableTable" class="table  table-striped table-hover table-bordered" id="sssTable" cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                      <thead>
                          <tr>
                              <th width="60">Salary Credit from</th>
                              <th width="60">Salary Credit to</th>
                              <th width="60">Monthly Salary Credit</th>
                              <th width="60">SS-ER</th>
                              <th width="60">SS-EE</th>
                              <th width="60">SS-Total</th>
                              <th width="60">EC</th>
                              <th width="60">TC-ER</th>
                              <th width="60">TC-EE</th>
                              <th width="60">Contribution-Total</th>
                              <th width="60">SE/VM/OFW</th>
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
      </div>
    </section>

    <!-- SSS Add Modal-->
    <div id="addContributionModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg modal-lg-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add Contribution</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="addContribution-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <div class="card-body">

                                            <div class="form-group row">
                                              <div class="col-md-12">
                                                <label for="" class="form-control-label col-form-label-sm">Range of Compensation <span class="asterisk"></span></label>
                                                <div class="row">
                                                  <div class="col-md-4">
                                                    <input id="addrangefrom_desc"  type="number" step = "0.01" pattern="^\d+(?:\.\d{1,2})?$"  class="form-control sss_required" name="addrangefrom_desc">
                                                    <small class="form-text">From</small>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <input id="addrangeto_desc" type="number" step = "0.01" pattern="^\d+(?:\.\d{1,2})?$"  class="form-control sss_required" name="addrangeto_desc">
                                                    <small class="form-text">To</small>
                                                  </div>

                                                  <div class="col-md-4">
                                                    <input id="addSalCred_desc" type="number" class="form-control sss_required" name="addSalCred_desc">
                                                    <small class="form-text">Monthly Salary Credit</small>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <!-- <div class="form-group row">
                                              <div class="col-md-12 mb-3">
                                                <h4>Range of compensation</h4>
                                                <div class="row">
                                                  <div class="col-md-6">
                                                    <label>From <span class="asterisk"></span></label>
                                                    <input id="addrangefrom_desc"  type="number" step = "0.01" pattern="^\d+(?:\.\d{1,2})?$"  class="form-control sss_required" name="addrangefrom_desc">
                                                  </div>

                                                  <div class="col-md-6">
                                                    <label>To <span class="asterisk"></span></label>
                                                    <input id="addrangeto_desc" type="number" step = "0.01" pattern="^\d+(?:\.\d{1,2})?$"  class="form-control sss_required" name="addrangeto_desc">
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-12 mb-3">
                                                <h4>Monthly Salary Credit <span class="asterisk"></span></h4>
                                                <input id="addSalCred_desc" type="number" class="form-control sss_required" name="addSalCred_desc">
                                              </div>
                                            </div> -->
                                            <!-- <hr style="height: 2px; border:none; color:#333; background-color:#333;"> -->
                                            <div class="form-group row">
                                              <div class="col-md-12">
                                                <label for="" class="form-control-label col-form-label-sm">Social Security <span class="asterisk"></span></label>
                                                <div class="row">
                                                  <div class="col-md-4">
                                                    <input id="addER" type="number" class="form-control sss_required" name="addER">
                                                    <small class="form-text">ER</small>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <input id="addEE" type="number" class="form-control sss_required" name="addEE">
                                                    <small class="form-text">EE</small>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <input id="addTotalSS" type="number" class="form-control sss_required" name="addTotalSS" readonly>
                                                    <small class="form-text">Total</small>
                                                  </div>
                                                </div>
                                              </div>
                                              <!-- <div class="col-md-12 mb-3">
                                                <h4>Social Security</h4>
                                                <div class="form-group row">
                                                  <div class="col-md-4">
                                                    <label for="ER">ER <span class="asterisk"></span></label>
                                                    <input id="addER" type="number" class="form-control sss_required" name="addER">
                                                  </div>
                                                  <div class="col-md-4">
                                                    <label>EE <span class="asterisk"></span></label>
                                                    <input id="addEE" type="number" class="form-control sss_required" name="addEE">
                                                  </div>
                                                  <div class="col-md-4">
                                                    <label>Total <span class="asterisk"></span></label>
                                                    <input id="addTotalSS" type="number" class="form-control sss_required" name="addTotalSS" readonly>
                                                  </div>
                                                </div>
                                              </div> -->
                                            </div>

                                            <!-- ---EC--- -->
                                            <!-- <hr style="height: 2px; border:none; color:#333; background-color:#333;"> -->
                                            <div class="form-group row">
                                              <div class="col-md-12">
                                                <label for="" class="form-control-labe col-form-label-sm">EC <span class="asterisk"></span></label>
                                                <div class="row">
                                                  <div class="col-md-4">
                                                    <input id="EC" type="number" class="form-control sss_required" name="EC">
                                                  </div>
                                                </div>
                                              </div>
                                                <!-- <div class="col-md-12">
                                                <h4>EC <span class="asterisk"></span></h4>
                                                  <input id="EC" type="number" class="form-control sss_required" name="EC">
                                                </div> -->
                                            </div>

                                            <!-- ---Total Contribution--- -->
                                            <!-- <hr style="height: 2px; border:none; color:#333; background-color:#333;"> -->
                                            <div class="form-group row">
                                              <div class="col-md-12">
                                                <label for="" class="form-control-label col-form-label-sm">Total Contribution <span class="asterisk"></span></label>
                                                <div class="row">
                                                  <div class="col-md-4">
                                                    <input id="addContributionER" type="number" class="form-control sss_required" name="addContributionER">
                                                    <small class="form-text">ER</small>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <input id="addContributionEE" type="number" class="form-control sss_required" name="addContributionEE">
                                                    <small class="form-text">EE</small>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <input id="TotalContribution" type="number" class="form-control sss_required" name="TotalContribution" readonly>
                                                    <small class="form-text">Total</small>
                                                  </div>
                                                </div>
                                              </div>
                                              <!-- <div class="col-12">
                                                <h4>Total Contribution</h4>
                                                <div class="form-group">
                                                  <label>ER <span class="asterisk"></span></label>
                                                  <input id="addContributionER" type="number" class="form-control sss_required" name="addContributionER">
                                                </div>

                                                <div class="form-group">
                                                  <label>EE <span class="asterisk"></span></label>
                                                  <input id="addContributionEE" type="number" class="form-control sss_required" name="addContributionEE">
                                                </div>

                                                <div class="form-group">
                                                  <label>Total <span class="asterisk"></span></label>
                                                  <input id="TotalContribution" type="number" class="form-control sss_required" name="TotalContribution" readonly>
                                                </div>
                                              </div> -->
                                            </div>
                                            <!-- <hr style="height: 2px; border:none; color:#333; background-color:#333;"> -->
                                            <div class="form-group row">
                                              <div class="col-md-12">
                                                <label for="" class="form-control-label col-form-label-sm">SE/VM/OFW <span class="asterisk"></span></label>
                                                <div class="row">
                                                  <div class="col-md-4">
                                                    <input type="number" id="SVO_totalContribution" class="form-control" name = "SVO_totalContribution" readonly>
                                                  </div>
                                                </div>
                                              </div>
                                              <!-- <div class="col-12">
                                                <h4>SE/VM/OFW</h4>
                                                <div class="form-group">
                                                  <label for="total_contribution">Total Contribution <span class="asterisk"></span></label>
                                                  <input type="number" id="SVO_totalContribution" class="form-control" name = "SVO_totalContribution" readonly>
                                                </div>
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
                                <button type="button" id="addSSSinfo" style="float:right" class="btn btn-primary addSSSinfo">Add New Contribution</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="updatSSSModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg modal-lg-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update Contribution</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="updateSSS-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                  <div class="card-body">
                                    <div class="form-group row">
                                      <div class="col-md-12">
                                        <label for="" class="form-control-label col-form-label-sm">Range Of Compensation <span class="asterisk"></span></label>
                                        <div class="row">
                                          <div class="col-md-4">
                                            <input id="editrangefrom_desc" type="text"  class="form-control form-control-success" name="editrangefrom_desc">
                                            <small class = "form-text">From</small>
                                            <input type="hidden" id="id" class="info_areaId id" name="id">
                                          </div>

                                          <div class="col-md-4">
                                            <input id="editrangeto_desc" type="text"  class="form-control form-control-success" name="editrangeto_desc">
                                            <small class="form-text">To</small>
                                          </div>

                                          <div class="col-md-4">
                                            <input id="editSalCred_desc" type="text" class="form-control form-control-success" name="editSalCred_desc">
                                            <small class="form-text">Monthly Salary Credit</small>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="form-group row">
                                      <div class="col-md-12">
                                        <label for="" class="form-control-label col-form-label-sm">Social Security</label>
                                        <div class="row">
                                          <div class="col-md-4">
                                            <input id="editER" type="text" class="form-control form-control-success" name="editER">
                                            <small class="form-text">ER</small>
                                          </div>

                                          <div class="col-md-4">
                                            <input id="editEE" type="text" class="form-control form-control-success" name="editEE">
                                            <small class="form-text">EE</small>
                                          </div>

                                          <div class="col-md-4">
                                            <input id="editSSTotal" type="text" class="form-control form-control-success" name="editSSTotal" readonly>
                                            <small class="form-text">Total</small>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="form-group row">
                                      <div class="col-md-12">
                                        <label for="" class="form-control-label col-form-label-sm">EC</label>
                                        <div class="row">
                                          <div class="col-md-4">
                                            <input id="editEC" type="text" class="form-control form-control-success" name="editEC">
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="form-group row">
                                      <div class="col-md-12">
                                        <label for="" class="form-control-label col-form-label-sm">Total Contribution</label>
                                        <div class="row">
                                          <div class="col-md-4">
                                            <input id="editContirbutionER" type="text" class="form-control form-control-success" name="editContirbutionER">
                                            <small class="form-text">ER</small>
                                          </div>

                                          <div class="col-md-4">
                                            <input id="editContributionEE" type="text" class="form-control form-control-success" name="editContributionEE">
                                            <small class="form-text">EE</small>
                                          </div>

                                          <div class="col-md-4">
                                            <input id="editTotalCont" type="text" class="form-control form-control-success" name="editTotalCont" readonly>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="form-group row">
                                      <div class="col-md-4">
                                        <label for="">SE/VM/OFW</label>
                                        <input type="number" id="edit_SVO_totalContribution" class="form-control" name = "edit_SVO_totalContribution" readonly>
                                      </div>
                                    </div>
                                            <!-- <div class="form-group row">
                                              <div class="col-12 form-group">
                                                <h4>Range Of Compensation</h4>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="form-group">
                                                  <label for="editrangefrom_desc">From<span class="asterisk"></span></label>
                                                  <input id="editrangefrom_desc" type="text"  class="form-control form-control-success" name="editrangefrom_desc">
                                                  <input type="hidden" id="id" class="info_areaId id" name="id">
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <label for="editrangeto_desc">To<span class="asterisk"></span></label>
                                                <input id="editrangeto_desc" type="text"  class="form-control form-control-success" name="editrangeto_desc">
                                              </div>
                                              <div class="col-md-12">
                                                <div class="form-group">
                                                  <label for="editSalCred_desc">Monthly Salary Credit <span class="asterisk"></label>
                                                  <input id="editSalCred_desc" type="text" class="form-control form-control-success" name="editSalCred_desc">
                                                </div>
                                              </div>
                                            </div>
                                            <hr style="height: 2px; border:none; color:#333; background-color:#333;">
                                            <div class="form-group row">
                                              <div class="col-12">
                                                <h4>Social Security<span class="asterisk"></span></h4>
                                              </div>
                                              <div class="col-md-4">
                                                  <label class="form-control-label" >ER <span class="asterisk"></span></label>
                                                  <input id="editER" type="text" class="form-control form-control-success" name="editER">
                                              </div>
                                               <div class="col-md-4">
                                                  <label class="form-control-label" >EE <span class="asterisk"></span></label>
                                                  <input id="editEE" type="text" class="form-control form-control-success" name="editEE">
                                              </div>
                                               <div class="col-md-4">
                                                  <label class="form-control-label" >Total <span class="asterisk"></span></label>
                                                  <input id="editSSTotal" type="text" class="form-control form-control-success" name="editSSTotal" readonly>
                                              </div>
                                            </div>
                                            <hr style="height: 2px; border:none; color:#333; background-color:#333;">
                                            <div class="form-group row">
                                              <div class="col-12">
                                                <h5>EC<span class="asterisk"></span></h5>
                                              </div>
                                              <div class="col-md-12">
                                                <input id="editEC" type="text" class="form-control form-control-success" name="editEC">
                                              </div>
                                            </div> -->

                                            <!-- ---Total Contribution--- -->
                                            <!-- <hr style="height: 2px; border:none; color:#333; background-color:#333;">
                                            <div class="form-group row">
                                              <div class="col-12">
                                                <h4>Total Contribution <span class="asterisk"></span></h4>
                                              </div>
                                              <div class="col-md-4">
                                                  <label class="form-control-label" >ER <span class="asterisk"></span></label>
                                                  <input id="editContirbutionER" type="text" class="form-control form-control-success" name="editContirbutionER">
                                              </div>
                                               <div class="col-md-4">
                                                  <label class="form-control-label" >EE <span class="asterisk"></span></label>
                                                  <input id="editContributionEE" type="text" class="form-control form-control-success" name="editContributionEE">
                                              </div>
                                               <div class="col-md-4">
                                                  <label class="form-control-label" >Total <span class="asterisk"></span></label>
                                                  <input id="editTotalCont" type="text" class="form-control form-control-success" name="editTotalCont" readonly>
                                              </div>
                                            </div>

                                            <hr style="height: 2px; border:none; color:#333; background-color:#333;">
                                            <div class="form-group row">
                                              <div class="col-12">
                                                <h4>SE/VM/OFW</h4>
                                                <div class="form-group">
                                                  <label for="total_contribution">Total Contribution <span class="asterisk"></span></label>
                                                  <input type="number" id="edit_SVO_totalContribution" class="form-control" name = "edit_SVO_totalContribution" readonly>
                                                </div>
                                              </div>
                                            </div> -->

                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="updateSSS" style="float:right" class="btn btn-primary updateSSS">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteSSSModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete Contribution</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="deleteSSS-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record <br>(<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" id="id" class="del_areaId id" name="del_areaId" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id ="deleteContriBtn" style="float:right" class="btn btn-primary deleteContriBtn">Delete Contributions</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/settings/sss.js');?>"></script>
