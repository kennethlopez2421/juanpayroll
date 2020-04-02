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
            <li class="breadcrumb-item active">Benefits</li>
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
                      <option value="by_name">Benefits Name</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divName" class = "filter_div single_search active">
                      <label for="Benefits name" class="form-control-label col-form-label-sm">Benefits name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
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
                  <table class="table table-striped table-bordered" id = "benefits_settings_tbl">
                    <thead>
                      <th width = "100">No</th>
                      <th>Benefits Name</th>
                      <th width = "160">Action</th>
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
            <h4 class="modal-title">Add New Benefits</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "add_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12 mb-2">
                <label for="Benefits Name" class="form-control-label col-form-label-sm">Benefits Name: <span class="asterisk"></span></label>
                <input type="text" id = "benefits_name" name = "benefits_name" class="form-control rq">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" id = "btn_save" class="btn btn-sm btn-primary">Save</button>
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
            <h4 class="modal-title">Edit Benefits</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12 mb-2">
                <label for="Benefits Name" class="form-control-label col-form-label-sm">Benefits Name: <span class="asterisk"></span></label>
                <input type="text" id = "edit_benefits_name" name = "edit_benefits_name" class="form-control rq2">
                <input type="hidden" id = "uid" name = "uid">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" id = "btn_update" class="btn btn-sm btn-primary">Save</button>
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
            <h4 class="modal-title">Confirmation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="delete_form">
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                <input type="hidden" id = "delid" name = "delid">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" id = "btn_delete" class="btn btn-sm btn-primary">Yes</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\benefits_settings.js')?>"></script>
