<?php
  if(isset($this->session->admin_user_id) && isset($this->session->admin_username)){
    $username = en_dec('dec',$this->session->username);
    $user = $this->admin_model->get_admin_user($username);
    if($user->num_rows() == 0){
      header("Location:".base_url('Main/logout'));
    }
  }else{
    header("Location:".base_url('Main/logout'));
  }
?>
<div class="content-inner" id="pageActive" data-num="25" data-namecollapse="" data-labelname="HRIS Branch">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token);?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">HRIS Branch</li>
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
                      <option value="by_bname">Account Name</option>
                      <option value="by_bcode">Account Code</option>
                      <option value="by_timezone">Timezone</option>
                      <option value="by_country">Country</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divBname" class = "filter_div single_search active">
                      <label for="Account Name" class="form-control-label col-form-label-sm">Account Name</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divBcode" class = "filter_div single_search" style = "display:none;">
                      <label for="Account Code" class="form-control-label col-form-label-sm">Account Code</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divTimezone" class="filter_div single_search" style = "display:none;">
                      <label for="Timezone" class="form-control-label col-form-label-sm">Timezone</label>
                      <select class = "form-control searchArea">
                        <option value="">------</option>
                      </select>
                    </div>

                    <div id="divCountry" class = "filter_div single_search" style = "display:none;">
                      <label for="Country" class="form-control-label col-form-label-sm">Country</label>
                      <select class = "form-control searchArea">
                        <option value="">------</option>
                      </select>
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_add_modal">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id = "branch_tbl" class="table table-bordered table-striped">
                    <thead>
                      <th>Account Name</th>
                      <th>Account Code</th>
                      <th>Timezone</th>
                      <th width = "90">Country Code</th>
                      <th>Status</th>
                      <th>Location</th>
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
            <h4 class="modal-title">Create New HRIS Branch</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="new_branch_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-12 mb-3">
                      <h4>Account Admin Info</h4>
                    </div>
                    <div class="col-md-12 mb-1">
                      <label for="Username" class="form-control-label col-form-label-sm">Username: <span class="asterisk"></span></label>
                      <input type="text" id = "username" name = "username" class="form-control rq">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="Password:" class="form-control-label col-form-label-sm">Password: <span class="asterisk"></span></label>
                      <input type="text" id = "password" name = "password" class="form-control rq">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="Confirm Password" class="form-control-label col-form-label-sm">Confirm Password: <span class="asterisk"></span></label>
                      <input type="text" id = "cpassword" name = "cpassword" class="form-control rq">
                    </div>

                    <div class="col-md-12 mb-1">
                      <label for="First Name:" class="form-control-label col-form-label-sm">First Name: <span class="asterisk"></span></label>
                      <input type="text" id = "fname" name = "fname" class="form-control rq">
                    </div>

                    <div class="col-md-12 mb-1">
                      <label for="Middle Name:" class="form-control-label col-form-label-sm">Middle Name: </label>
                      <input type="text" id = "mname" name = "mname" class="form-control">
                    </div>

                    <div class="col-md-12 mb-1">
                      <label for="Last Name:" class="form-control-label col-form-label-sm">Last Name: <span class="asterisk"></span></label>
                      <input type="text" id = "lname" name = "lname" class="form-control rq">
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-12 mb-3">
                      <h4>Account Information</h4>
                    </div>
                    <div class="col-md-12 mb-1">
                      <label for="Branch Name" class="form-control-label col-form-label-sm">Account Name: <span class="asterisk"></span></label>
                      <input type="text" id = "branch_name" name = "branch_name" class="form-control rq">
                    </div>

                    <div class="col-md-12 mb-1">
                      <label for="Branch Code" class="form-control-label col-form-label-sm">Account Code: <span class="asterisk"></span></label>
                      <input type="text" id = "branch_code" name = "branch_code" class="form-control rq">
                    </div>

                    <div class="col-md-12 mb-1">
                      <label for="Database Name" class="form-control-label col-form-label-sm">Database Name: <span class="asterisk"></span></label>
                      <input type="text" id = "db_name" name = "db_name" class="form-control rq">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="Timezone" class="form-control-label col-form-label-sm">Timezone: <span class="asterisk"></span></label>
                      <input type="text" id = "timezone" name = "timezone" class="form-control rq">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="Country Code" class="form-control-label col-form-label-sm">Country Code: <span class="asterisk"></span></label>
                      <input type="text" id = "country_code" name = "country_code" class="form-control rq">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="Location Status" class="form-control-label col-form-label-sm">Location Status: <span class="asterisk"></span></label>
                      <select name="loc_status" id="loc_status" class="form-control">
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                      </select>
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
    <!-- VIEW MODAL -->
    <div class="modal fade" id = "view_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">HRIS Branch</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="update_hris_branch_form">
            <div class="modal-body">
              <div class="form-group row">
                <!-- ACCOUNT ADMIN INFO -->
                <div class="col-md-6">
                  <div class="col-12 mb-3">
                    <h4>Account Admin Info</h4>
                  </div>
                  <div class="col-12 mb-1">
                    <label for="Username" class="form-control-label col-form-label-sm">Username</label>
                    <input type="text"id = "edit_username" name = "edit_username" class="form-control">
                    <input type="hidden" id = "uid" name = "uid" class="form-control rq2">
                  </div>
                  <div class="col-md-12 mb-1">
                    <label for="Password" class="form-control-label col-form-label-sm">Password</label>
                    <input type="password" id = "edit_password" name = "edit_password" class="form-control rq2">
                    <input type="hidden" id = "curr_password" name = "curr_passswod">
                  </div>
                  <div class="col-md-12 mb-1">
                    <label for="First Name" class="form-control-label col-form-label-sm">First Name</label>
                    <input type="text" id = "edit_fname" name = "edit_fname" class="form-control rq2">
                  </div>
                  <div class="col-md-12 mb-1">
                    <label for="Middle Name" class="form-control-label col-form-label-sm">Middle Name</label>
                    <input type="text" id = "edit_mname" name = "edit_mname" class="form-control">
                  </div>
                  <div class="col-md-12 mb-1">
                    <label for="Last Name" class="form-control-label col-form-label-sm">Last Name</label>
                    <input type="text" id = "edit_lname" name = "edit_lname" class="form-control rq2">
                  </div>
                </div>
                <!-- ACCOUNT INFO  -->
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-12 mb-3">
                      <h4>Account Information</h4>
                    </div>
                    <div class="col-12 mb-1">
                      <label for="Account Name" class="form-control-label col-form-label-sm">Account Name</label>
                      <input type="text" id = "edit_branch_name" name = "edit_branch_name" class="form-control rq2">
                    </div>
                    <div class="col-12 mb-1">
                      <label for="Account Code" class="form-control-label col-form-label-sm">Account Code</label>
                      <input type="text" id = "edit_branch_code" name = "edit_branch_code" class="form-control rq2">
                    </div>
                    <div class="col-12 mb-1">
                      <label for="Database Name" class="form-control-label col-form-label-sm">Database Name</label>
                      <input type="text" id = "edit_dbname" name = "edit_dbname" class="form-control rq2" readonly>
                    </div>
                    <div class="col-md-6 mb-1">
                      <label for="Timezone" class="form-control-label col-form-label-sm">Timezone</label>
                      <input type="text" id = "edit_timezone" name = "edit_timezone" class="form-control rq2">
                    </div>
                    <div class="col-md-6 mb-1">
                      <label for="Contry Code" class="form-control-label col-form-label-sm">Contry Code</label>
                      <input type="text" id = "edit_country_code" name = "edit_country_code" class="form-control rq2">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="Location Status" class="form-control-label col-form-label-sm">Location Status: <span class="asterisk"></span></label>
                      <select name="edit_loc_status" id="edit_loc_status" class="form-control">
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "button" class="btn btn-info" id = "login_superuser" data-bcode = "" data-timezone = "">Login as Superuser</button>
              <button type = "submit" id = "btn_update" class="btn btn-sm btn-primary">Update</button>
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
            <h4 class="modal-title">Deactivate HRIS Branch</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="delete_form">
            <div class="modal-body">
              <h4>Are you sure you want to delete this HRIS Branch? (<span id="del_txt"></span>)</h4>
              <input type="hidden" id = "delid" name = "delid">
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- ACTIVATE MODAL -->
    <div class="modal fade" id = "activate_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Activate HRIS Branch</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="activate_form">
            <div class="modal-body">
              <h4>Are you sure you want to activate this HRIS Branch (<span id="activate_txt"></span>)</h4>
              <input type="hidden" id = "activate_id" name = "activate_id">
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_yes2">Yes</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\branch\branch.js')?>"></script>
