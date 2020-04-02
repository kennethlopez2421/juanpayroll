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
            <li class="breadcrumb-item active">Companies</li>
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
                    <label for="Search" class="form-control-label col-form-label-sm">Search</label>
                    <input type="text" id = "searchArea" class = "form-control searchArea" placeholder="Company Name">
                  </div>

                  <div class="col-md-9 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button id = "btn_add_modal" class="btn btn-primary">Add</button>
                    <!-- <a href="<?= base_url('transactions/Additionalpays/add/'.$token) ?>"><button class="btn btn-primary btnClickAddArea">Add</button></a> -->
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id = "companies_tbl" class="table table-striped table-bordered">
                    <thead>
                      <th width = "80">No.</th>
                      <th>Company Name</th>
                      <th width = "180">Action</th>
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
            <h4 class="modal-title">Add New Company</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="add_form">
          <div class="modal-body">
            <label for="Company Name" class="form-control-label col-form-label-sm">Company Name</label>
            <input id = "new_company_name" name = "new_company_name" type="text" class = "form-control rq">
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary">Save</button>
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
            <h4 class="modal-title">Edit Company</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_form">
          <div class="modal-body">
            <input id = "edit_company_name" name = "edit_company_name" type="text" class="form-control rq2">
            <input id = "uid" name = "uid" type="hidden">
            <input type="hidden" name="current_company_name" id="current_company_name">
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary">Update</button>
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
            <h4 class="modal-title">Delete Company</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="delete_form">
          <div class="modal-body">
            <h4>Are you sure you want to delete (<span id = "del_name"></span>) ?</h4>
            <input type="hidden" name = "delid" id = "delid">
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary">Yes</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\companies.js')?>"></script>
