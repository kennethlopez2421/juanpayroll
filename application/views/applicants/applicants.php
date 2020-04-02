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
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/employees_home/'.$token);?>">Entity</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Applicant</li>
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
                    <label for="Applicant" class="form-control-labe col-form-label-sm">Applicant</label>
                    <input type="text" class="form-control searchArea" placeholder="Ex. John Doe ...">
                  </div>
                  <div class="col-md-9 text-right">
                    <button class="btn btn-sm btn-primary" id="btnGenLink">Generate Link</button>
                    <button class="btn btn-sm btn-primary" id="btnSearchApp">Search</button>
                    <form method = "post" action = "<?=base_url('applicants/Applicant/add/'.$token)?>" class = "d-inline">
                      <button type = "submit" class="btn btn-sm btn-primary" id="btnAddApplicant">Add</button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id = "applicantTbl" class="table table-striped table-bordered table-hover" style = "border-top:1px solid gainsboro;">
                    <thead>
                      <th width = "190">Applicant Ref No</th>
                      <th>Name</th>
                      <th width = "100">Status</th>
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

    <!-- Generate Link -->
    <div class="modal fade" id="gen_link_modal">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Generate Link</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <div class="input-group mb-3">
              <input type="text" data-token = "" id = "genLink" class="form-control" placeholder="http://..." data-toggle = "tooltip" data-placement = "top" title = "Copied">
              <div class="input-group-append">
                <button class="btn btn-lg btn-primary" id = "btnCopyLink">
                  Copy
                </button>
              </div>
            </div>
          </div>

          <!-- Modal footer -->
          <!-- <div class="modal-footer">
            <button type="button" class="btn blue-grey" data-dismiss="modal">Close</button>
            <button id = "btn_save_pagibig" class="btn btn-sm btn-primary">Save</button>
          </div> -->

        </div>
      </div>
    </div>

    <!-- Delete Applicant -->
    <div id="delEmployeeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <!-- <form class="form-horizontal personal-info-css" id="delete_area-form"> -->
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" class="appId">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delEmpBtn" style="float:right" class="btn btn-primary deleteAreaBtn">Delete</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                <!-- </form> -->
            </div>
        </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/applicants/applicant.js')?>"></script>
