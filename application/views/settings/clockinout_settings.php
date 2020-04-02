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
            <li class="breadcrumb-item active">Clock In / Out</li>
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
                      <option value="by_rules">Rules</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divRules" class = "filter_div single_search active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Employee Rules</label>
                      <input type="text" class="form-control searchArea" value = "">
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
                  <table class="table table-bordered" id = "clockinout_tbl">
                    <thead>
                      <th>Rules</th>
                      <th width = "120">Minutes</th>
                      <th width = "120">Status</th>
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

    <!-- DELETE MODAL -->
    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Delete Clock In / Out Rules</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "delete_form">
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record (<strong class="info_desc"></strong>) ?</p>
                <input type="hidden" name = "delid" class="delid">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- UPDATE MODAL -->
    <div class="modal fade" id = "update_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Clock In/Out Rules</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="update_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12">
                <label for="Name" class="form-control-label col-form-label-sm">Name <span class="asterisk"></span></label>
                <input type="text" id = "rules" name = "rules" class="form-control rq">
                <input type="hidden" id = "update_id" name="update_id" value="">
              </div>
              <div class="col-md-12">
                <label for="Description" class="form-control-label col-form-label-sm">Description <span class="asterisk"></span></label>
                <textarea name="description" id="description" cols="30" rows="3" class="form-control rq"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btn btn-sm btn-primary" id = "btn_update_save">Save</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- ADD MODAL -->
    <div class="modal fade" id = "add_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New Rules</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="add_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12 mb-2">
                <label for="Rules" class="form-control-label col-form-label-sm">Rules <span class="asterisk"></span></label>
                <input type="text" id = "add_rules" name = "add_rules" class="form-control add_rq">
              </div>
              <div class="col-md-4 mb-2">
                <label for="Minutes" class="form-control-label col-form-label-sm">Minutes <span class="asterisk"></span></label>
                <input type="text" id = "add_minutes" name = "add_minutes" class="form-control number-input-2 add_rq">
              </div>
              <div class="col-md-4 mb-2">
                <label for="Type" class="form-control-label col-form-label-sm">Type <span class="asterisk"></span></label>
                <select name="add_type" id="add_type" class="form-control add_rq">
                  <option value="">------</option>
                  <option value="<?=en_dec('en', 'late')?>">Late</option>
                  <option value="<?=en_dec('en', 'undertime')?>">Undertime</option>
                  <option value="<?=en_dec('en', 'overtime')?>">Overtime</option>
                  <option value="<?=en_dec('en', 'half_day')?>">Half Day</option>
                  <option value="<?=en_dec('en', 'over_break')?>">Over Break</option>
                  <option value="<?=en_dec('en', 'default')?>">Default</option>
                </select>
              </div>
              <div class="col-md-4 mb-2">
                <label for="Status" class="form-control-label col-form-label-sm">Status <span class="asterisk"></span></label>
                <select name="add_status" id="add_status" class="form-control add_rq">
                  <option value="<?=en_dec('en','off')?>">Offline</option>
                  <option value="<?=en_dec('en','on')?>">Online</option>
                </select>
              </div>
              <div class="col-md-12 mb-2">
                <label for="Descriptions" class="form-control-label col-form-label-sm">Description <span class="asterisk"></span></label>
                <textarea name="add_desc" id="add_desc" cols="30" rows="5" class="form-control add_rq"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_add_save">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\clockinout_settings.js')?>"></script>
