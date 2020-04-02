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
            <li class="breadcrumb-item active">Content Navigation Functions</li>
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
                      <option value="by_name">Content Navigation name</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divName" class = "filter_div single_search active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Content Navigation name</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <form id="access_func_form">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered" id = "access_func_tbl">
                      <thead>
                        <th width = "50"></th>
                        <th>Content Navigation</th>
                        <?php if($access_functions->num_rows() > 0):?>
                          <?php $access_header = $access_functions;?>
                          <?php foreach($access_header->result_array() as $header):?>
                            <th><?=$header['name']?></th>
                          <?php endforeach;?>
                        <?php endif;?>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type = "submit" class="btn btn-primary" id = "btn_apply">Apply</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\settings\access_functions.js')?>"></script>
