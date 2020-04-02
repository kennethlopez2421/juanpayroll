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
            <li class="breadcrumb-item active">System Users</li>
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
                            <label class="form-control-label col-form-label-sm">System Users</label>
                            <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                          </div>

                          <div class="col-lg-4 offset-lg-5 text-right">
                            <button class="btn btn-sm btn-primary" id="btnSearchSysUser">Search</button>
                            <button id = "btn_add" class="btn btn-primary btnClickAddArea">Add</button>
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
                                                <label class="form-control-label col-form-label-sm">System Users</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <!-- <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addSystemUserModal" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:20px; width: 8%;">Add</button> -->

                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="systemUserTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Status</th>
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

    <!-- ADD MODAL -->
    <div class="modal fade" id = "add_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New System User</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="system_user_addform">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="Position" class="form-control-label col-form-label-sm">Position <span class="asterisk"></span></label>
                  <select name="sys_positon" id="sys_positon" class="form-control select2 rq">
                    <option value="">------</option>
                    <?php if($system_user_positions->num_rows() > 0):?>
                      <?php foreach($system_user_positions->result_array() as $sys_pos):?>
                        <option value="<?=$sys_pos['position_id']?>"><?=$sys_pos['position']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                </div>

                <div class="col-md-12">
                  <label for="First Name:" class="form-control-label col-form-label-sm">First Name: <span class="asterisk"></span></label>
                  <input type="text" name = "sys_fname" id = "sys_fname" class="form-control rq">
                </div>

                <div class="col-md-12">
                  <label for="Middle Name:" class="form-control-label col-form-label-sm">Middle Name:</label>
                  <input type="text" id = "sys_mname" name = "sys_mname" class="form-control">
                </div>

                <div class="col-md-12">
                  <label for="Last Name:" class="form-control-label col-form-label-sm">Last Name: <span class="asterisk"></span></label>
                  <input type="text" id = "sys_lname" name = "sys_lname" class="form-control rq">
                </div>

                <div class="col-md-12">
                  <label for="Username:" class="form-control-label col-form-label-sm">Username: <span class="asterisk"></span></label>
                  <input type="text" id = "sys_username" name = "sys_username" class="form-control rq">
                </div>

                <div class="col-md-12">
                  <label for="Password" class="form-control-label col-form-label-sm">Password: <span class="asterisk"></span></label>
                  <input type="password" id = "sys_password" name = "sys_password" class="form-control rq">
                </div>

                <div class="col-md-12">
                  <label for="Password" class="form-control-label col-form-label-sm">Confirm Password: <span class="asterisk"></span></label>
                  <input type="password" id = "sys_password_cf" name = "sys_password_cf" class="form-control rq">
                </div>


              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_submit_addform">Save</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- EDIT MODAL -->
    <div class="modal fade" id = "edit_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit System User</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="system_user_editform">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="Employee Idno:" class="form-control-label col-form-label-sm">Employee Idno:</label>
                  <input type="text" id = "employee_idno" name = "employee_idno" class="form-control">
                </div>
                <div class="col-md-12">
                  <label for="Username" class="form-control-label col-form-label-sm">Username:  <span class="asterisk"></span></label>
                  <input type="text" id = "edit_sys_username" name = "edit_sys_username" class="form-control edit_rq">
                  <input type="hidden" id = "edit_uid" name = "edit_uid">
                </div>

                <div class="col-md-12">
                  <label for="First Name:" class="form-control-label col-form-label-sm">First Name: <span class="asterisk"></span></label>
                  <input type="text" id = "edit_sys_fname" name = "edit_sys_fname" class="form-control edit_rq">
                </div>

                <div class="col-md-12">
                  <label for="Middle Name:" class="form-control-label col-form-label-sm">Middle Name:</label>
                  <input type="text" id = "edit_sys_mname" name = "edit_sys_mname" class="form-control">
                </div>

                <div class="col-md-12">
                  <label for="Last Name:" class="form-control-label col-form-label-sm">Last Name: <span class="asterisk"></span></label>
                  <input type="text" id = "edit_sys_lname" name = "edit_sys_lname" class="form-control edit_rq">
                </div>

              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" id = "btn_update_form" class="btn btn-sm btn-primary">Update</button>
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
            <h4 class="modal-title">Disable System User</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <h4>Are you sure you want to disable the account of (<span class="user_disable"></span>)</h4>
            <input type="hidden" id = "sys_del_id" value = "">
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_del_sys">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal-->
    <!-- <div id="addSystemUserModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add</h4>
                </div>
                <form class="form-horizontal personal-info-css" id="add_area-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" id="addSystemUserDesc" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="addSystemUserBtn" style="float:right" class="btn btn-success saveBtnArea">Add</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

    <!-- <div id="editSystemUserModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update System Users</h4>
                </div>
                <form class="form-horizontal personal-info-css" id="update_area-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" id="editSystemUserDesc" class="form-control">
                                    <input type="hidden" class="systemuserid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="editSystemUserBtn" style="float:right" class="btn btn-primary updateBtnArea">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

    <!-- <div id="delSystemUserModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete</h4>
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" class="systemuserid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delSystemUserBtn" style="float:right" class="btn btn-primary deleteAreaBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<!-- <script src="<?= base_url('assets/js/settings/systemusers.js') ?>"></script> -->
<script src = "<?=base_url('assets\js\settings\system_user.js')?>"></script>
