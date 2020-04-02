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
            <li class="breadcrumb-item active">Employee Charges</li>
        </ol>
    </div>

    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="">
                            <div class="card-header d-flex align-items-center">
                                 <div class="form-group mx-sm-3 mb-2">
                                    <input type="text" class="form-control" autocomplete="off" placeholder="Search Here" id = "chargesTableTB">
                                </div>
                                <button type="submit" class="btn btn-primary mb-2" id="searchButton">Search</button>

                                <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addEmployeeChargesModal" class="btn btn-primary btnClickAddArea" style=" position: absolute; right: 50px; width:90px;"><i class = "fa fa-plus"></i>Add</button>

                            </div>
                        </div>
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
<!--                             <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addEmployeeChargesModal" class="btn btn-primary btn-block btnClickAddArea" style=" position: absolute; width: 8%;"><i class='fa fa-plus'></i>Add</button> -->


                            <div class="table-responsive" style="overflow: scroll; overflow-y:hidden;">
                                <table class="table  table-striped table-hover table-bordered" id="EmployeeChargesTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="60">ID</th>
                                            <th>Description</th>
                                            <th>Charge Amount</th>
                                            <th>Charge Status</th>
                                            <th width="190">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody><tr class="rowtry"></tr></tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal-->
    <div id="addEmployeeChargesModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="col-md-5 form-control-label" style="margin-left:-13px;">Employee Charges<span class="asterisk"></span></label>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">�</span></button> -->
                </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <small class="form-text">Description</small>
                                    <input type="text" id="addEmployeeChargesDesc" class="form-control" autocomplete="off">
                                    <small class="form-text">Amount</small>
                                    <input type="text" id="addEmployeeChargesAmount" class="form-control" autocomplete="off">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="addEmployeeChargesBtn" style="float:right" class="btn btn-success saveBtnArea">Add</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div id="editEmployeeChargesModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update Employee Charges</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">�</span></button> -->
                </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <small class="form-text">Description</small>
                                    <input type="text" id="description" class="form-control">
                                    <small class="form-text">Amount</small>
                                    <input type="text" id="editEmployeeChargesAmount" class="form-control">

                                    <input type="hidden" class="EmployeeChargesid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="editEmployeeChargesBtn" style="float:right" class="btn btn-primary updateBtnArea">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div id="delEmployeeChargesModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
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
                                    <p><span id = "charges_status_prompt_delete" class="text-warning"></span> <bold class="info_desc"></bold> <span id = "qmark"></span></p>
                                    <input type="hidden" class="EmployeeChargesid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delEmployeeChargesBtn" style="float:right" class="btn btn-primary deleteAreaBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?= base_url('assets/js/settings/employeecharges.js') ?>"></script>
