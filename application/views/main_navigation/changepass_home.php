<!--
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->

<div class="content-inner" id="pageActive" data-num="19" data-namecollapse="" data-labelname="Change Password">
    <div class="bc-icons-2 card mb-4">

        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Change Password</li>
        </ol>

    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
    <!-- Page Header-->
    <div class="card">
      <!-- <div class="card-header"> -->
        <!-- <h3>Change Password</h3> -->
      <!-- </div> -->
      <div class="card-body">
        <div class="container">
          <div class="form-group row">
            <div class="col-lg-8 offset-lg-2 col-sm-10 offset-sm-1">
              <h3>Change Password</h3>
            </div>
            <div class="col-lg-8 offset-lg-2 col-sm-10 offset-sm-1">
              <label for="Username" class="form-control-label col-form-label-sm">Username: <span class="asterisk"></span></label>
              <input type="text" id = "username" class="form-control rq" placeholder="Enter Username">
            </div>
            <div class="col-lg-8 offset-lg-2 col-sm-10 offset-sm-1">
              <label for="Current Password:" class="form-control-label col-form-label-sm">Current Password: <span class="asterisk"></span></label>
              <input type="password" id = "current_pw" class="form-control rq" placeholder="Enter your password">
            </div>
            <div class="col-lg-8 offset-lg-2 col-sm-10 offset-sm-1">
              <label for="Password" class="form-control-label col-form-label-sm">New Password: <span class="asterisk"></span></label>
              <input type="password" name = "new_pw" id = "new_pw" placeholder="Enter new password" class = "form-control mb-2 rq" maxlength="30">
              <div><span id="pw-status" class="badge badge-pill"></span></div>
            </div>
            <div class="col-lg-8 offset-lg-2 col-sm-10 offset-sm-1 text-right">
              <button class="btn btn-sm btn-primary" id="save_btn">Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>


    <?php $this->load->view('includes/footer'); ?>
    <script src = "<?=base_url('assets/js/changepass/changepass.js')?>"></script>
