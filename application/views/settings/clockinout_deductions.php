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
            <li class="breadcrumb-item active">Clock in/out Deductions</li>
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
                      <option value="by_type">Deduction Type</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divType" class = "filter_div active single_search">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Deduction Type</label>
                      <select name="" id="" class="form-control searchArea select2">
                        <option value="<?=en_dec('en', 'late')?>">Late</option>
                        <option value="<?=en_dec('en', 'undertime')?>">Undertime</option>
                        <option value="<?=en_dec('en', 'overbreak')?>">Overbreak</option>
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
                  <table class="table table-bordered text-center" id = "clock_deductions_tbl">
                    <thead>
                      <th width = "190">Deduction Type</th>
                      <th width = "190">Range <small>(mins)</small></th>
                      <th width = "190">Deduction <small>(mins)</small></th>
                      <th width = "190">Working Hours <small>(hrs)</small></th>
                      <th width = "190">Status</th>
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
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Clock In / Out Deduction</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="add_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12">
                <label for="Deduction Type" class="form-control-label col-form-label-sm">Deduction Type <span class="asterisk"></span></label>
                <select name="type" id="type" class="form-control select2 rq">
                  <option value="<?=en_dec('en', 'late')?>">Late</option>
                  <option value="<?=en_dec('en', 'undertime')?>">Undertime</option>
                  <option value="<?=en_dec('en', 'overbreak')?>">Overbreak</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="Range from" class="form-control-label col-form-label-sm">Range from <small>(mins)</small> <span class="asterisk"></span></label>
                <input type="text" id = "min_from" name = "min_from" class="form-control number-input-2 rq">
              </div>
              <div class="col-md-6">
                <label for="Range to" class="form-control-label col-form-label-sm">Range to <small>(mins)</small> <span class="asterisk"></span></label>
                <input type="text" id = "min_to" name = "min_to" class="form-control number-input-2 rq">
              </div>
              <div class="col-md-6">
                <label for="Minutes Deductions" class="form-control-label col-form-label-sm">Minutes Deductions <small>(mins)</small> <span class="asterisk"></span></label>
                <input type="text" id = "min_deduct" name = "min_deduct" class="form-control number-input-2 rq">
              </div>
              <div class="col-md-6">
                <label for="Working Hours" class="form-control-label col-form-label-sm">Working Hours <small>(Hrs)</small> <span class="asterisk"></span></label>
                <input type="text" id = "whours" name = "whours" class="form-control number-input-2 rq">
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
    <!-- UPDATE MODAL -->
    <div class="modal fade" id = "update_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Clock In / Out Deductions</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="update_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12">
                <label for="Deduction Type" class="form-control-label col-form-label-sm">Deduction Type <span class="asterisk"></span></label>
                <select name="edit_type" id="edit_type" class="form-control select2 rq2">
                  <option value="<?=en_dec('en', 'late')?>">Late</option>
                  <option value="<?=en_dec('en', 'undertime')?>">Undertime</option>
                  <option value="<?=en_dec('en', 'overbreak')?>">Overbreak</option>
                </select>
                <input type="hidden" id = "uid" name = "uid">
              </div>
              <div class="col-md-6">
                <label for="Range from" class="form-control-label col-form-label-sm">Range from <small>(mins)</small> <span class="asterisk"></span></label>
                <input type="text" id = "edit_min_from" name = "edit_min_from" class="form-control number-input-2 rq2">
              </div>
              <div class="col-md-6">
                <label for="Range to" class="form-control-label col-form-label-sm">Range to <small>(mins)</small> <span class="asterisk"></span></label>
                <input type="text" id = "edit_min_to" name = "edit_min_to" class="form-control number-input-2 rq2">
              </div>
              <div class="col-md-6">
                <label for="Minutes Deductions" class="form-control-label col-form-label-sm">Minutes Deductions <small>(mins)</small> <span class="asterisk"></span></label>
                <input type="text" id = "edit_min_deduct" name = "edit_min_deduct" class="form-control number-input-2 rq2">
              </div>
              <div class="col-md-6">
                <label for="Working Hours" class="form-control-label col-form-label-sm">Working Hours <small>(Hrs)</small> <span class="asterisk"></span></label>
                <input type="text" id = "edit_whours" name = "edit_whours" class="form-control number-input-2 rq2">
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
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record ?</p>
                <input type="hidden" id = "delid" class="employeeid">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_del_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\clockinout_deductions.js')?>"></script>
