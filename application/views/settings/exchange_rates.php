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
            <li class="breadcrumb-item active">Exchange Rates</li>
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
                    <label for="Filter" class="form-control-label col-form-label-sm">Search</label>
                    <input type="text" class="form-control" id = "searchArea" placeholder = "Description">
                  </div>

                  <div class="col-md-9 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_add">Add</button>
                  </div>
                </div>

              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "exchange_rate_tbl">
                    <thead>
                      <th width = "50">No.</th>
                      <th>Base Currency</th>
                      <th>Currency Code</th>
                      <th>Currency Name</th>
                      <th>Exchange Rate</th>
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
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Exchange Rate</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="ex_rate_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12 mb-2">
                  <label for="Currency Code" class="form-control-label col-form-label-sm">Currency Code: <span class="asterisk"></span></label>
                  <input id = "currency_code" name = "currency_code" type="text" class="form-control rq">
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Currency Name" class="form-control-label col-form-label-sm">Currency Name: <span class="asterisk"></span></label>
                  <input id = "currency_name" name = "currency_name" type="text" class="form-control rq">
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Exchange Rate" class="form-control-label col-form-label-sm">Exchange Rate: <span class="asterisk"></span></label>
                  <input id = "exchange_rate" name = "exchange_rate" type="text" class="form-control currency-input rq">
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
    <!-- EDIT MODAL -->
    <div class="modal fade" id = "edit_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Exchange Rate</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_ex_rate_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="Currency Code" class="form-control-label col-form-label-sm">Currency Code:</label>
                  <input id = "edit_currency_code" name = "edit_currency_code" type="text" class="form-control rq2">
                  <input type="hidden" id = "uid" name = "uid">
                </div>

                <div class="col-md-12">
                  <label for="Currency Name" class="form-control-label col-form-label-sm">Currency Name:</label>
                  <input id = "edit_currency_name" name = "edit_currency_name" type="text" class="form-control rq2">
                </div>

                <div class="col-md-12">
                  <label for="Exchange Rate" class="form-control-label col-form-label-sm">Exchange Rate:</label>
                  <input id = "edit_exchange_rate" name = "edit_exchange_rate" type="text" class="form-control currency-input rq2">
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_update">Update</button>
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
            <h4 class="modal-title">Delete Exchange Rate</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="delete_ex_rate_form">
            <div class="modal-body">
              <h4>Are you sure you want to Delete [<span id = "del_text"></span>]</h4>
              <input type="hidden" id = "delid" name = "delid">
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_delete">Yes</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\exchange_rates.js')?>"></script>
