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
            <li class="breadcrumb-item active">Position</li>
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
                            <label class="form-control-label col-form-label-sm">Position</label>
                            <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                          </div>

                          <div class="col-lg-4 offset-lg-5 text-right">
                            <button class="btn btn-sm btn-primary" id="btnSearchPos">Search</button>
                            <button id = "btn_add_modal" data-backdrop="static" data-keyboard="false" data-target="#addPositionModal" class="btn btn-primary btnClickAddArea">Add</button>
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
                                                <label class="form-control-label col-form-label-sm">Position</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <!-- <button id = "btn_add_modal" data-backdrop="static" data-keyboard="false" data-target="#addPositionModal" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:20px; width:8%">Add</button> -->

                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="positionTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="60">ID</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                            <th>Sub Department</th>
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

    <!-- ADD MODAL-->
    <div id="addPositionModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add New Position</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="addPosition-form">
                    <div class="modal-body">
                      <div class="row">
                          <div class="col-lg-12">
                            <div class="">
                              <div class="card-body">
                                <div class="form-group row">
                                  <div class="col-md-12 mb-2">
                                    <label for="addPos_Dept">Department <span class="asterisk ml-2"></span></label>
                                    <select id = "addPos_Dept" name = "position_dept" class = "form-control">
                                      <option value = "">-----------</option>
                                    </select>
                                    <!-- <input id="addPos_Dept" type="text" class="form-control form-control-success" name="position_dept"> -->
                                  </div>

                                  <div class="col-md-12 mb-2">
                                    <label for="addPos_SubDept">Sub Department <span class="asterisk ml-2"></span></label>
                                    <select id = "addPos_SubDept" name = "position_subDept" class = "form-control">
                                      <option value = "">-----------</option>
                                    </select>
                                    <!-- <input id="addPos_SubDept" type="text" class="form-control form-control-success" name="position_subDept"> -->
                                  </div>

                                  <div class="col-md-12 mb-2">
                                    <label for="Position Access Level">Position Access Level <span class="asterisk"></span></label>
                                    <select name="pos_access_lvl" id="pos_access_lvl" class="form-control">
                                      <option value="">-----------</option>
                                      <?php if($pos_access_lvl->num_rows() > 0):?>
                                        <?php foreach($pos_access_lvl->result_array() as $access):?>
                                          <option value="<?=$access['position_id']?>"><?=$access['position']?></option>
                                        <?php endforeach;?>
                                      <?php endif;?>
                                    </select>
                                  </div>

                                  <div class="col-md-12 mb-2">
                                    <label for="addPos_desc">Description <span class="asterisk ml-2"></span></label>
                                    <input id="addPos_desc" type="text" class="form-control form-control-success" name="position_desc">
                                  </div>

                                  <div class="col-md-12 mb-2">
                                    <label for="Department Access" class="form-control-label col-form-label-sm">Department Access <span class="asterisk"></span></label>
                                    <select name="dept_access" id="dept_access" class="form-control multi-select" multiple = "multiple" disabled>
                                      <!-- <option value="" disabled>------</option> -->
                                    </select>
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
                                <button type="button" id="addPosBtn" style="float:right" class="btn btn-success addPosBtn">Add Positon</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ---EDIT MODAL--- -->
    <div id="editPosModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update Position</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="upadtePos-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                  <div class="card-body">
                                      <div class="form-group row">
                                          <!-- <label class="col-md-2 form-control-label">Update Position <span class="asterisk"></span></label> -->
                                          <div class="col-md-12 mb-2">
                                            <label for="editPos_deptDesc">Department</label>
                                            <select name = "editPos_deptDesc" id = "editPos_deptDesc" class = "form-control">
                                              <option value = "">-----------</option>
                                            </select>
                                            <!-- <input id="editPos_deptDesc" type="text" class="form-control form-control-success" name="editPos_deptDesc"> -->
                                          </div>

                                          <div class="col-md-12 mb-2">
                                            <label for="editPos_subDeptDesc">Sub Department</label>
                                            <select name = "editPos_subDeptDesc" id = "editPos_subDeptDesc" class = "form-control">
                                              <option value = "">-----------</option>
                                            </select>
                                            <!-- <input id="editPos_deptDesc" type="text" class="form-control form-control-success" name="editPos_deptDesc"> -->
                                          </div>

                                          <div class="col-md-12 mb-2">
                                            <label for="Position Access Level">Position Access Level <span class="asterisk"></span></label>
                                            <select name="edit_pos_access_lvl" id="edit_pos_access_lvl" class="form-control">
                                              <option value="">-----------</option>
                                              <?php if($pos_access_lvl->num_rows() > 0):?>
                                                <?php foreach($pos_access_lvl->result_array() as $access):?>
                                                  <option value="<?=$access['position_id']?>"><?=$access['position']?></option>
                                                <?php endforeach;?>
                                              <?php endif;?>
                                            </select>
                                          </div>

                                          <div class="col-md-12 mb-2">
                                              <label for="editPos_desc">Description</label>
                                              <input type="hidden" name="info_areaId" class="info_areaId positionid">
                                              <input id="editPos_desc" type="text" class="form-control form-control-success editPos_desc" name="editPos_desc">
                                          </div>

                                          <div class="col-md-12 mb-2">
                                            <label for="Department Access" class="form-control-label col-form-label-sm">Department Access <span class="asterisk"></span></label>
                                            <select name="dept_access" id="edit_dept_access" class="form-control multi-select" multiple = "multiple">
                                              <!-- <option value="" disabled>------</option> -->
                                            </select>
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
                                <button type="button" id="editPosBtn" style="float:right" class="btn btn-primary editPosBtn">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- ---DELETE MODAL--- -->
    <div id="deletePosModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete Area</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record <br>(<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" class="del_areaId" name="del_areaId" id="delPosid" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" style="float:right" class="btn btn-primary deletePosBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/settings/position.js');?>"></script>
