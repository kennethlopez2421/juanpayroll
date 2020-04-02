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
            <li class="breadcrumb-item active">Pag Ibig</li>
        </ol>
    </div>

    <setion class="tables">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <div class="row pagIbigTblHeader_wrapper">
              <div class="col-12 text-right">
                <button id = "btn_pagIbig_ctrls" class="btn btn-sm btn-primary float-right">Controls</button>
              </div>
            </div>

            <div class="row datatableTblHeader_wrapper" style = "display:none;">
              <div class="col-md-3">
                <label for="" class="form-control-label col-form-label-sm">Monthly Compensation</label>
                <input type="text" class="form-control searchArea" placeholder="Monthly Compensation..">
              </div>

              <div class="col-md-4 offset-md-5 text-right">
                  <button class="btn btn-sm btn-primary" id="btnSearchLove">Search</button>
                  <button id = "btn_add_modal" class="btn btn-sm btn-primary">Add</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <!-- Pag Ibig Table -->
            <div class="pagIbigTable">
              <!-- <div class="btnWrapper text-right">
                <button id = "btn_pagIbig_ctrls" class="btn btn-sm btn-primary">Controls</button>
              </div> -->
              <div class="table-responsive">
                <table class="table table-bordered table-striped text-center" style = "border-top: 1px solid gainsboro;">
                  <thead>
                    <tr>
                      <th rowspan = "2" style = "vertical-align:middle;">Monthly Compensation</th>
                      <th colspan = "2">Percentage of Monthly Compensation</th>
                    </tr>
                    <tr>
                      <th>Employee Share</th>
                      <th>Employer Share</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(count($pagIbigTable) > 0):?>
                      <?php foreach($pagIbigTable as $love):?>
                        <tr>
                          <td><?=$love['monthly_compensation']?></td>
                          <td><?=(int)$love['employee_share']." %"?></td>
                          <td><?=(int)$love['employer_share']." %"?></td>
                        </tr>
                      <?php endforeach;?>
                    <?php else:?>
                      <tr>
                        <td colspan = "3">No data available</td>
                      </tr>
                    <?php endif;?>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Pag Ibig Data Table -->
            <div class="dataTableContainer col-12" style = "display:none;">
              <!-- <div class="controlsWrapper text-right">
                <button id = "btn_add_modal" class="btn btn-sm btn-primary">Add</button>
              </div> -->
              <div class="table-responsive">
                <table id = "pagIbig_table" class="table table-stripped table-bordered text-center" style = "border-top:1px solid gainsboro;">
                  <thead>
                    <tr>
                      <th rowspan = "2" style = "vertical-align:middle;">Monthly Compensation</th>
                      <th colspan = "2">Percentage of Monthly Compensation</th>
                      <th></th>
                    </tr>
                    <tr>
                      <th>Employee Share</th>
                      <th>Employer Share</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </setion>

    <!-- Add Pag Ibig -->
    <div class="modal fade" id="pagIbig_add_modal">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Add</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="Monthly Compensation">Monthly Compensation <span class="asterisk"></span></label>
                  <input type="text" id = "monthly_compensation" name = "monthly_compensation" class = "form-control pRequired">
                </div>
              </div>

              <div class="col-md-6">
                <label for="Employee Share">Employee Share <span class="asterisk"></span></label>
                <div class="input-group mb-3">
                  <input type="text" id = "employee_share" name = "employee_share" class = "form-control text-right pRequired">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <label for="Employer Share">Employer Share <span class="asterisk"></span></label>
                <div class="input-group mb-3">
                  <input type="text" id = "employer_share" name = "employer_share" class = "form-control text-right pRequired">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn blue-grey" data-dismiss="modal">Close</button>
            <button id = "btn_save_pagibig" class="btn btn-sm btn-primary">Save</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Edit Pag Ibig -->
    <div class="modal fade" id="pagIbig_edit_modal">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Edit</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="Monthly Compensation">Monthly Compensation <span class="asterisk"></span></label>
                  <input type="text" id = "edit_monthly_compensation" name = "edit_monthly_compensation" class = "form-control epRequired">
                  <input type="hidden" id = "updateId" value = "">
                </div>
              </div>

              <div class="col-md-6">
                <label for="Employee Share">Employee Share <span class="asterisk"></span></label>
                <div class="input-group mb-3">
                  <input type="text" id = "edit_employee_share" name = "edit_employee_share" class = "form-control text-right epRequired">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <label for="Employer Share">Employer Share <span class="asterisk"></span></label>
                <div class="input-group mb-3">
                  <input type="text" id = "edit_employer_share" name = "edit_employer_share" class = "form-control text-right epRequired">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn blue-grey" data-dismiss="modal">Close</button>
            <button id = "btn_update_pagibig" class="btn btn-sm btn-info">Update</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Delete Pag Ibig -->
    <div class="modal fade" id="pagIbig_del_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Delete Area</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <div class="col-lg-12">
              <p>Are you sure you want to delete this record <br>(<bold class="info_desc"></bold>) ?</p>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn blue-grey" data-dismiss="modal">Cancel</button>
            <button id = "btn_delete_pagibig" class="btn btn-sm btn-primary">Delete Record</button>
          </div>

        </div>
      </div>
    </div>


<?php $this->load->view('includes/footer');?>
<script src="<?=base_url('assets/js/settings/pagibig.js');?>"></script>
