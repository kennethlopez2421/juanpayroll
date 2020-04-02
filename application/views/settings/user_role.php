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
<link rel="stylesheet" href="<?=base_url('assets/css/custom_checkbox.css')?>">
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">User Role</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-md-3">
                    <label for="Search Filter" class="form-control-label col-form-label-sm">Search Filter</label>
                    <input type="text" id = "pos_search" class="form-control">
                  </div>
                  <div class="col-md-9 text-right">
                    <button class="btn btn-primary" id = "btnSearchButton">Search</button>
                    <button class="btn btn-primary" id = "add_user_role">Add Role</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "user_role_tbl">
                    <thead>
                      <th width = "40">No.</th>
                      <th>Description</th>
                      <th width = "200">Action</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- UPDATE USER ROLE MODAL -->
    <div class="modal fade" id = "user_role_modal">
      <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update User Role</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "user_role_form">
            <div class="modal-body">
              <div class="container">
                <div class="form-group row my-4">
                  <label for="Description:" class="col-sm-1 col-form-label">Position <span class="asterisk"></span>:</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" id = "position" name = "position">
                    <input type="hidden" name = "pos_id" id = "pos_id">
                  </div>
                </div>

                <?php if($main_navs->num_rows() > 0):?>
                  <div class="form-group row">
                    <div class="col-md-12 my-4">
                      <h2>Main Navigation Role</h2>
                    </div>
                    <?php foreach($main_navs->result_array() as $mnav):?>
                      <div class="col-md-6 mb-2">
                        <label class="container_label">
                          <i class="fa <?=$mnav['main_nav_icon']?> mr-1"></i><?=$mnav['main_nav_desc']?>
                          <input type="checkbox" name = "main_nav[]" class = "main_nav" value = "<?=$mnav['main_nav_id']?>">
                          <span class="checkmark"></span>
                        </label>
                      </div>
                    <?php endforeach;?>
                  </div>
                <?php endif;?>

                <?php if($content_navs->num_rows() > 0):?>
                  <div class="form-group row">
                    <div class="col-md-12 mb-3">
                      <h2>Content Navigation Role</h2>
                    </div>
                    <?php foreach($main_navs2->result_array() as $mnav):?>
                      <div class="col-md-12 my-3 ">
                        <h3 style = "text-decoration:underline;">
                          <label class="container_label d-inline mr-2">
                            <input type="checkbox" name = "parent[]" class = "parent" data-pid = "<?=$mnav['main_nav_desc']?>">
                            <span class="checkmark"></span>
                          </label>
                          <?=$mnav['main_nav_desc']?>
                        </h3>
                      </div>
                      <?php foreach($content_navs->result_array() as $cnav):?>
                        <?php if($mnav['main_nav_id'] == $cnav['cn_fkey']):?>
                          <div class="col-md-6 mb-2">
                            <label class="container_label">
                              <span style = "font-weight:bold !important;"><?=$cnav['cn_name']?></span>
                              <input type="checkbox" name = "content_nav[]" class = "content_nav <?=$mnav['main_nav_desc']?>_child" checked="checked" value = "<?=$cnav['content_nav_id']?>">
                              <span class="checkmark"></span>
                            </label>
                            <div class="form-group row">
                              <?php if($functions->num_rows() > 0):?>
                                <?php foreach($functions->result_array() as $func):?>
                                  <?php $main_access = explode(',',$func['main_nav_access']); ?>
                                  <?php if(in_array($cnav['content_nav_id'],$main_access)):?>
                                    <div class="col-md-3 mb-2">
                                      <label class="container_label2">
                                        <?=$func['name']?>
                                        <input type="checkbox" name = "func_access_<?=$cnav['content_nav_id']?>[]" class = "func_access func_nav_<?=$cnav['content_nav_id']?>"  value = "<?=$func['id']?>">
                                        <span class="checkmark2"></span>
                                      </label>
                                    </div>
                                  <?php endif;?>
                                <?php endforeach;?>
                              <?php endif;?>
                            </div>
                          </div>
                        <?php endif;?>
                      <?php endforeach;?>
                    <?php endforeach;?>
                  </div>
                <?php endif;?>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary">Save</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- ADD USER MODAL -->
    <div class="modal fade" id = "add_user_role_modal">
      <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add User Role</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "add_user_role_form">
            <div class="modal-body">
              <div class="container">
                <div class="form-group row my-4">
                  <label for="Description:" class="col-sm-1 col-form-label ">Position <span class="asterisk"></span>:</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control rq" id = "add_position" name = "add_position">
                  </div>

                  <label for="Description:" class="col-sm-1 col-form-label ">Hierarchy Level <span class="asterisk"></span>:</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control decimalNumbers rq" id = "add_hierarchy_lvl" name = "add_hierarchy_lvl">
                  </div>
                </div>

                <?php if($main_navs->num_rows() > 0):?>
                  <div class="form-group row">
                    <div class="col-md-12 my-4">
                      <h2>Main Navigation Role</h2>
                    </div>
                    <?php foreach($main_navs->result_array() as $mnav):?>
                      <div class="col-md-6 mb-2">
                        <label class="container_label">
                          <i class="fa <?=$mnav['main_nav_icon']?> mr-1"></i><?=$mnav['main_nav_desc']?>
                          <input type="checkbox" name = "add_main_nav[]" class = "main_nav" value = "<?=$mnav['main_nav_id']?>">
                          <span class="checkmark"></span>
                        </label>
                      </div>
                    <?php endforeach;?>
                  </div>
                <?php endif;?>

                <?php if($content_navs->num_rows() > 0):?>
                  <div class="form-group row">
                    <div class="col-md-12 mb-3">
                      <h2>Content Navigation Role</h2>
                    </div>
                    <?php foreach($main_navs2->result_array() as $mnav):?>
                      <div class="col-md-12 my-3 ">
                        <h3 style = "text-decoration:underline;">
                          <label class="container_label d-inline mr-2">
                            <input type="checkbox" name = "parent[]" class = "parent" data-pid = "<?=$mnav['main_nav_desc']?>">
                            <span class="checkmark"></span>
                          </label>
                          <?=$mnav['main_nav_desc']?>
                        </h3>
                        <!-- <h5><?=$mnav['main_nav_desc']?></h5> -->
                      </div>
                      <?php foreach($content_navs->result_array() as $cnav):?>
                        <?php if($mnav['main_nav_id'] == $cnav['cn_fkey']):?>
                          <div class="col-md-6 mb-2">
                            <label class="container_label">
                              <span style = "font-weight:bold !important;"><?=$cnav['cn_name']?></span>
                              <input type="checkbox" name = "add_content_nav[]" class = "content_nav content_nav <?=$mnav['main_nav_desc']?>_child" value = "<?=$cnav['content_nav_id']?>">
                              <span class="checkmark"></span>
                            </label>
                            <div class="form-group row">
                              <?php if($functions->num_rows() > 0):?>
                                <?php foreach($functions->result_array() as $func):?>
                                  <?php $main_access = explode(',',$func['main_nav_access']); ?>
                                  <?php if(in_array($cnav['content_nav_id'],$main_access)):?>
                                    <div class="col-md-3 mb-2">
                                      <label class="container_label2">
                                        <?=$func['name']?>
                                        <input type="checkbox" name = "add_func_access_<?=$cnav['content_nav_id']?>[]" class = "func_access add_func_nav_<?=$cnav['content_nav_id']?>"  value = "<?=$func['id']?>">
                                        <span class="checkmark2"></span>
                                      </label>
                                    </div>
                                  <?php endif;?>
                                <?php endforeach;?>
                              <?php endif;?>
                            </div>
                          </div>
                        <?php endif;?>
                      <?php endforeach;?>
                    <?php endforeach;?>
                  </div>
                <?php endif;?>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_save_role">Save</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- DELETE USER MODAL -->
    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Delete User Role</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="delete_user_role_form">
            <div class="modal-body">
              <h4>Are you sure you want to delete this user role ( <span id = "del_text"></span> )</h4>
              <input type="hidden" id = "delid" name = "delid">
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" id = "btn_yes" class="btn btn-sm btn-primary">yes</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\user_role.js')?>"></script>
