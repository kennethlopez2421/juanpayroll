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
<!-- <link rel="stylesheet" href="<?=base_url('assets\summernote\bootstrap.css')?>"> -->
<link rel="stylesheet" href="<?=base_url('assets\summernote\summernote.min.2.css')?>">
<style>
  .btn-default{
    color: #333 !important;
    background-color: #fff !important;
    border-color: #ccc !important;
  }

  .btn-default.btn-default.dropdown-toggle{
    color: #333 !important;
    background-color: #fff !important;
    border-color: #ccc !important;
  }

  .panel-heading{
    background-color: #f7f7f7;
  }

  .note-editor{
    min-height: 350px !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Contract Template</li>
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
                      <option value="by_template_name">Template Name</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divTemplate" class = "filter_div single_search active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Template Name</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button data-toggle="modal" id="btn_add" class="btn btn-primary">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered" id = "contract_template_tbl">
                    <thead>
                      <th width = "50">No.</th>
                      <th>Template Name</th>
                      <th>Date Created</th>
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
            <h4 class="modal-title">Add New Contract Template<?=$this->session->emp_idno?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="template_form">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-4 mb-2">
                  <label for="Template Type" class="form-control-label col-form-label-sm">Template Type <span class="asterisk"></span></label>
                  <select name="template_type" id="template_type" class="form-control rq">
                    <option value="<?=en_dec('en','default')?>">Default</option>
                    <option value="<?=en_dec('en','job_offer')?>">Job Offer</option>
                    <option value="<?=en_dec('en','agreement')?>">Agreement</option>
                    <option value="<?=en_dec('en','addendum')?>">Addendum</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="Template Name" class="form-control-label col-form-label-sm">Template Name <span class="asterisk"></span></label>
                  <input type="text" name = "template_name" id = "template_name" class="form-control rq">
                </div>
                <div class="col-md-8">
                  <label for="Template Fields" class="form-control-label col-form-label-sm">Template Fields</label>
                  <div class="row">
                    <div class="col-md-8 mb-3">
                      <select name="select_fields" id="select_fields" class="form-control select2">
                        <?php if($fields->num_rows() > 0):?>
                          <?php foreach($fields->result_array() as $field):?>
                            <option value="<?=$field['field_name']?>" data-text = "<?=$field['name']?>"><?=$field['name']?></option>
                          <?php endforeach;?>
                        <?php else:?>
                          <option value="">------</option>
                        <?php endif;?>
                      </select>
                    </div>

                    <div class="col-md-4 mb-3">
                      <button type = "button" class="btn btn-primary" id = "add_fields">Add Fields</button>
                    </div>

                  </div>
                </div>
              </div>
              <textarea class="contractDescription form-control rq" name = "summernote" id="summernote" style = "min-width: 500px !important;">

              </textarea>
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
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Contract Template</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_template_form">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-4 mb-2">
                  <label for="Template Type" class="form-control-label col-form-label-sm">Template Type <span class="asterisk"></span></label>
                  <select name="edit_template_type" id="edit_template_type" class="form-control rq2">
                    <option value="default">Default</option>
                    <option value="job_offer">Job Offer</option>
                    <option value="agreement">Agreement</option>
                    <option value="addendum">Addendum</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="Template Name" class="form-control-label col-form-label-sm">Template Name <span class="asterisk"></span></label>
                  <input type="text" name = "edit_template_name" id = "edit_template_name" class="form-control rq2">
                  <input type="hidden" name = "uid" id = "uid">
                </div>
                <div class="col-8">
                  <label for="Template Fields" class="form-control-label col-form-label-sm">Template Fields</label>
                  <div class="row">
                    <div class="col-md-8 mb-3">
                      <select name="edit_select_fields" id="edit_select_fields" class="form-control select2">
                        <?php if($fields->num_rows() > 0):?>
                          <?php foreach($fields->result_array() as $field):?>
                            <option value="<?=$field['field_name']?>" data-text = "<?=$field['name']?>"><?=$field['name']?></option>
                          <?php endforeach;?>
                        <?php else:?>
                          <option value="">------</option>
                        <?php endif;?>
                      </select>
                    </div>

                    <div class="col-md-4 mb-3">
                      <button type = "button" class="btn btn-primary" id = "add_fields2">Add Fields</button>
                    </div>

                  </div>
                </div>

              </div>
              <textarea class="contractDescription form-control" name = "edit_summernote" id="edit_summernote" style = "min-width: 500px !important;">

              </textarea>
            </div>
            <div class="modal-footer text-right">
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
            <h4 class="modal-title">Delete Contract Template</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "delete_template_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-12">
                  <h4>Are you sure you want to delete this ( <span id = "del_txt"></span> )?</h4>
                  <input type="hidden" name = "delid" id = "delid" class = "rq3">
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" id = "btn_yes" class="btn btn-sm btn-primary">Yes</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<!-- <script src = "<?=base_url('assets\summernote\bootstrap.js')?>"></script> -->
<script src = "<?=base_url('assets\summernote\summernote.min.2.js')?>"></script>
<script src = "<?=base_url('assets\summernote\summernote-ext-checkbox.js')?>"></script>
<script src = "<?=base_url('assets\js\signature-pad\signature_pad.umd.js')?>"></script>
<script src = "<?=base_url('assets\js\settings\contract_template.js')?>"></script>

<!-- <script src = "<?=base_url('assets/ckeditor_full/ckeditor.js')?>"></script>
<script>
  CKEDITOR.replace( 'contractDescription' );
</script> -->
