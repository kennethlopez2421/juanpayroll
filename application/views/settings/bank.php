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
            <li class="breadcrumb-item active">Bank</li>
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
                    <label for="Bank" class="form-control-label col-form-label-sm">Bank</label>
                    <input type="text" class="form-control" placeholder="Bank Name" id = "searchArea">
                  </div>
                  <div class="col-md-9 text-right">
                    <button class="btn btn-primary btn-sm" id = "btnSearch">Search</button>
                    <button class="btn btn-sm btn-primary" id = "btnAdd">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered" id = "bank_tbl">
                    <thead>
                      <th width = "190">No.</th>
                      <th>Bank</th>
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
    <!-- create -->
    <div class="modal fade" id = "add_bank_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Bank</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <label for="Bank Name" class="form-control-label col-form-label-sm">Bank Name <span class="asterisk"></span></label>
            <input type="text" class="form-control rq" id = "add_bank_name">
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_add_bank">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- update -->
    <div class="modal fade" id = "edit_bank_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Bank Information</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <label for="Bank Name:" class="form-control-label col-form-label-sm">Bank Name:</label>
            <input type="text" class="form-control" id = "update_bank_name">
            <input type="hidden" id = "current_bank_name">
            <input type="hidden" id = "uid">
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_update_bank">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- delete -->
    <div class="modal fade" id = "delete_bank_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                <input type="hidden" class="del_id" id = "del_id">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_del_bank">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\bank.js')?>"></script>
