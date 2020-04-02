<!--
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->

<div class="content-inner" id="pageActive" data-num="20" data-namecollapse="" data-labelname="HR Assists">
    <div class="bc-icons-2 card mb-4">

        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">HR Assists</li>
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
          <div class="form-group">
              <?php if($hr_assists->num_rows() > 0):?>
                <?php if($hr_assists->row()->body != ""):?>
                  <?=$hr_assists->row()->body?>
                <?php else:?>
                  <div class="form-group text-center" style = "padding-top:15%;padding-bottom:15%;">
                    <i class="fa fa-id-badge d-block mb-2" style = "font-size: 80px;"></i>
                    <h5>HR Assists not available</h5>
                  </div>
                <?php endif;?>

              <?php else:?>
                <div class="form-group text-center" style = "padding-top:15%;padding-bottom:15%;">
                  <i class="fa fa-id-badge d-block mb-2" style = "font-size: 80px;"></i>
                  <h5>HR Assists not available</h5>
                </div>
              <?php endif;?>
          </div>
        </div>
      </div>
    </div>


    <?php $this->load->view('includes/footer'); ?>
    <!-- <script src = "<?=base_url('assets/js/changepass/changepass.js')?>"></script> -->
