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
            <li class="breadcrumb-item active">Sub Department</li>
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
                            <label class="form-control-label col-form-label-sm">Sub Department</label>
                            <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                          </div>

                          <div class="col-lg-4 offset-lg-5 text-right">
                            <button class="btn btn-sm btn-primary" id="btnSearchSubDept">Search</button>
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addSubDeptModal" class="btn btn-primary btnClickAddArea">Add</button>
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
                                                <label class="form-control-label col-form-label-sm">Sub Department</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <!-- <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addSubDeptModal" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:20px; width: 8%;">Add</button> -->

                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="subDeptTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="60">ID</th>
                                            <th>Description</th>
                                            <th>Department</th>
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
    <div id="addSubDeptModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">�</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="add_area-form">
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Department <span class="asterisk"></span></label>
                                    <select id="addDeptId" class="form-control">
                                        <option>------</option>
                                        <?php foreach($departments as $department): ?>
                                            <option value="<?= $department->departmentid ?>"><?= $department->description ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Description <span class="asterisk"></span></label>
                                    <input type="text" id="addSubDeptDesc" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="addSubDeptBtn" style="float:right" class="btn btn-success saveBtnArea">Add</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editSubDeptModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Edit</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">�</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="update_area-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" id="editSubDeptDesc" class="form-control">
                                     <input type="hidden" class="subdeptid">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select id="editDepartment" class="form-control">
                                        <option></option>
                                        <?php foreach($departments as $department): ?>
                                            <option value="<?= $department->departmentid ?>"><?= $department->description ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="editSubDeptBtn" style="float:right" class="btn btn-primary updateBtnArea">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="delSubDeptModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">�</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" class="subdeptid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delSubDeptBtn" style="float:right" class="btn btn-primary deleteAreaBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?= base_url('assets/js/settings/subdept.js') ?>"></script>
