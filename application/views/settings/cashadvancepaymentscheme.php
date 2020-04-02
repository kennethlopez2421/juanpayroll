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
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Cash Advance Payment Scheme</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="col-md-3 offset-md-9 text-right">
                  <button class="btn btn-sm btn-primary" id = "btn_add_caps">Add</button>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover text-center" id = "CaPs_tbl" style = "width:100%;border-top:1px solid gainsboro;">
                    <thead>
                      <th>
                        <p class = "mb-0">Monthly Rate %</p>
                        <small>(Charge per month)</small>
                      </th>
                      <th>
                        <p class = "mb-0">Maximum Loan %</p>
                        <small>(Based on salary)</small>
                      </th>
                      <th>
                        <p class = "mb-0">Term of Payment</p>
                        <small>(Number of months)</small>
                      </th>
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
    <!-- add payment scheme -->
    <div class="modal fade" id = "add_caps_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Payment Scheme</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "caps_form">
            <div class="modal-body">
              <div class="form-group row">
                <label for="Monthly Rate" class="form-control-label col-form-label-sm col-md-3 text-right">Monthly Rate <span class="asterisk"></span></label>
                <div class="col-md-9">
                  <div class="input-group mb-3">
                    <input type="number" class="form-control rq" name = "monthly_rate" id = "monthly_rate">
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                  <small class="form-text">Charge per month</small>
                </div>
              </div>

              <div class="form-group row">
                <label for="Maximum Loan" class="form-control-label col-form-label-sm col-md-3 text-right">Maximum Loan <span class="asterisk"></span></label>
                <div class="col-md-9">
                  <div class="input-group mb-3">
                    <input type="number" class="form-control rq" name = "maximum_loan" id = "maximum_loan">
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                  <small class="form-text">Based on salary</small>
                </div>
              </div>

              <div class="form-group row">
                <label for="Term of Payment" class="form-control-label col-form-label-sm col-md-3 text-right">Term of Payment <span class="asterisk"></span></label>
                <div class="col-md-9">
                  <input type="number" class="form-control rq" id = "term_of_payment" name = "term_of_payment">
                  <small class="form-text">Number of months</small>
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary">Save</button>
              <button class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- edit payment scheme -->
    <div class="modal fade" id = "edit_caps_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Payment Scheme</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <form id = "edit_caps_form">
              <div class="modal-body">
                <div class="form-group row">
                  <label for="Monthly Rate" class="form-control-label col-form-label-sm col-md-3 text-right">Monthly Rate <span class="asterisk"></span></label>
                  <div class="col-md-9">
                    <div class="input-group mb-3">
                      <input type="number" class="form-control rq" name = "monthly_rate" id = "edit_monthly_rate">
                      <input type="hidden" name = "edit_id" id = "edit_id">
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <small class="form-text">Charge per month</small>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="Maximum Loan" class="form-control-label col-form-label-sm col-md-3 text-right">Maximum Loan <span class="asterisk"></span></label>
                  <div class="col-md-9">
                    <div class="input-group mb-3">
                      <input type="number" class="form-control rq" name = "maximum_loan" id = "edit_maximum_loan">
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <small class="form-text">Based on salary</small>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="Term of Payment" class="form-control-label col-form-label-sm col-md-3 text-right">Term of Payment <span class="asterisk"></span></label>
                  <div class="col-md-9">
                    <input type="number" class="form-control rq" id = "edit_term_of_payment" name = "term_of_payment">
                    <small class="form-text">Number of months</small>
                  </div>
                </div>
              </div>
              <div class="modal-footer text-right">
                <button type = "submit" class="btn btn-sm btn-primary">Save</button>
                <button class="btn blue-grey" data-dismiss = "modal">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- delete payment scheme -->
    <div class="modal fade" id = "del_caps_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Confirmation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record ?</p>
                <!-- <input type="hidden" id="caps_del_id"> -->
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">No</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/settings/cashadvancepaymentscheme.js')?>"></script>
