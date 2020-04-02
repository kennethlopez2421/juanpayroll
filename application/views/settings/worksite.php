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
.pac-container, .edit_pac-container {
    z-index: 10000 !important;
}
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Work Site</li>
        </ol>
    </div>

    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="">
                            <div class="card-header d-flex align-items-center">

                                <div class="col-lg-12">
                                    <br>
                                    <div class="row">

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">Work Site</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <button class="btn btn-primary" id = "btnSearchButton" style="right:120px; position: absolute; top:20px; width: 8%;">Search</button>
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addWorkSiteModal" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:20px; width: 8%;">Add</button>

                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="workSiteTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" >
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th >Description</th>
                                            <th>Location</th>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                            <th>Default Distance(meters)</th>
                                            <th width = "100">Timelog In/Out Link</th>
                                            <th>Action</th>
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
    <div id="addWorkSiteModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="add_area-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Description <span class="asterisk"></span></label>
                                    <input type="text" id="addWorkSiteDesc" class="form-control">
                                </div>
                                <div class="form-group">
                                  <label>Default Distance(meters) <span class="asterisk"></span></label>
                                  <input type="number" id="addDistance" class="form-control">
                                </div>
                                <div class="form-group">
                                    <div id="pac-container">
                                        <label for="">Location <span class="asterisk"></span></label>
                                        <input id="pac-input" type="text" placeholder="Enter Address" class = "form-control pr_field" name = "loc_address">
                                        <input type="hidden" name = "loc_latitude" id = "loc_latitude" class = "pr_field" value = "">
                                        <input type="hidden" name = "loc_longitude" id = "loc_longitude" class = "pr_field" value = "">
                                    </div>
                                    <div id="map" style = "height:200px;margin-top:30px;"></div>
                                    <div id="infowindow-content">
                                        <!-- <img src="" width="16" height="16" id="place-icon"> -->
                                        <span id="place-name"  class="title"></span><br>
                                        <span id="place-address"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="addWorkSiteBtn" style="float:right" class="btn btn-success saveBtnArea">Add</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editWorkSiteModal" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Edit</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="update_area-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" id="editWorkSiteDesc" class="form-control">
                                </div>
                                <div class="form-group">
                                  <label>Default Distance</label>
                                  <input type="number" id="editDistance" class="form-control">
                                  <input type="hidden" id="currentWorkSiteDesc" class="form-control">
                                  <input type="hidden" class="worksiteid">
                                </div>
                                <div class="form-group">
                                  <div id="edit_pac-container">
                                      <label for="">Location <span class="asterisk"></span></label>
                                      <input id="edit_pac-input" type="text" placeholder="Enter Address" class = "form-control pr_field" name = "edit_loc_address">
                                      <input type="hidden" name = "edit_loc_latitude" id = "edit_loc_latitude" class = "pr_field" value = "">
                                      <input type="hidden" name = "edit_loc_longitude" id = "edit_loc_longitude" class = "pr_field" value = "">
                                  </div>
                                  <div id="edit_map" style = "height:200px;margin-top:30px;"></div>
                                  <div id="edit_infowindow-content">
                                      <!-- <img src="" width="16" height="16" id="place-icon"> -->
                                      <span id="edit_place-name"  class="title"></span><br>
                                      <span id="edit_place-address"></span>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="editWorkSiteBtn" style="float:right" class="btn btn-primary updateBtnArea">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="delWorkSiteModal" class="modal fade text-left">
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
                                    <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" id="edit_worksiteid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delWorkSiteBtn" style="float:right" class="btn btn-primary deleteAreaBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/settings/worksite.js');?>"></script>
<script src="<?=base_url('assets/js/settings/worksitemap.js');?>"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCp8esu5bFCZDsr9jzWMW-ZxpgeyywXHVM&libraries=places&callback=initMap"
async defer></script>

<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFBsieYzklxXjq2WLa45HKM9_uHmuF6dw&libraries=places&callback=initMap" async defer></script> -->
