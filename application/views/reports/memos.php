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
<link rel="stylesheet" href="<?=base_url('assets/css/custom_loader2.css')?>">
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Memorandum Reports</li>
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
                      <option value="by_name">Employee Name</option>
                      <option value="by_dept">Department</option>
                      <option value="by_date">Date</option>
                    </select>
                  </div>

                  <div class="col-md-6">

                    <div id="divName" class = "filter_div active single_search" >
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea " placeholder="Ex. John Doe">
                    </div>

                    <div id="divDept" class="filter_div single_search" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0 ):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divDate" class = "filter_div range_date" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "date_from" class="form-control date_input from" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id = "date_to" class="form-control date_input to" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_add">Add</button>
                  </div>
                </div>
              </div>
              <!-- NAVIGATION  -->
              <ul class="nav nav-tabs mt-3">
                  <!-- PENDING NAV -->
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" data-tab = "pending_tab" id = "pending_nav" href="#pending" style="color:black;">Pending</a>
                  </li>
                  <!-- APPROVED NAV -->
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" data-tab = "approved_tab" id = "approved_nav" href="#approved" style="color:black;" >Approved</a>
                  </li>
              </ul>
              <div class="tab-content">
                <!-- PENDING TAB -->
                <div class="tab-pane fade show active" id = "pending_tab">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped" id = "pending_memo_tbl">
                        <thead>
                          <th>To</th>
                          <th>From</th>
                          <th>Re</th>
                          <th>Date</th>
                          <th>Status</th>
                          <th width = "190">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- APPROVED TAB -->
                <div class="tab-pane fade" id = "approved_tab">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped" id = "approved_memo_tbl">
                        <thead>
                          <th>To</th>
                          <th>From</th>
                          <th>Re</th>
                          <th>Date</th>
                          <th>Status</th>
                          <th width = "190">Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ADD MODAL  -->
    <div class="modal fade" id = "add_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Memorandum</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="add_memo_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-6">
                  <label for="To:" class="form-control-label col-form-label-sm">To: <span class="asterisk"></span></label>
                  <div class="dropdown" >
                    <input type="text" id = "employee" name = "employee" class="form-control dropdown-toggle rq" data-toggle = "dropdown">
                    <input type="hidden" id = "employee_idno" name = "employee_idno">
                    <div class="dropdown-menu form-control">
                      <div class="loader_wrapper" style = "display:none;">
                        <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
                          <div class="form-group row">
                            <div class="col-6 text-right p-0">
                              <h6>Searching ...</h6>
                            </div>
                            <div class="col-6 ">
                              <div class="loader-m"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id = "result_wrapper">

                      </div>
                    </div>
                  </div>
                  <!-- <label for="To" class="form-control-label col-form-label-sm">To:<span class="asterisk"></span></label> -->
                  <!-- <input type="text" class="form-control rq"> -->
                </div>

                <div class="col-md-6">
                  <label for="From" class="form-control-label col-form-label-sm">From: <span class="asterisk"></span></label>
                  <select name="dept" id="dept" class="form-control select2 rq">
                    <option value="">------</option>
                    <?php if($departments->num_rows() > 0 ):?>
                      <?php foreach($departments->result_array() as $dept):?>
                        <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="Re" class="form-control-label col-form-label-sm">Re: <span class="asterisk"></span></label>
                  <input type="text" id = "re" name = "re" class="form-control rq">
                </div>

                <div class="col-md-6">
                  <label for="Date" class="form-control-label col-form-label-sm">Date: <span class="asterisk"></span></label>
                  <input type="text" id = "date" name = "date" class="form-control date_input rq">
                </div>

                <div class="col-md-12">
                  <label for="Upload Memorandum File" class="form-control-label col-form-label-sm">Upload Memorandum File (doc, docx, pdf): <span class="asterisk"></span></label>
                  <input type="file" id = "memo_file" name = "memo_file" class="form-control rq" accept=".pdf,.doc,.docx">
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_save">Save</button>
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
            <h4 class="modal-title">Update Memorandum</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_memo_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-md-6">
                  <label for="To" class="form-control-label col-form-label-sm">To</label>
                  <div class="dropdown" >
                    <input type="text" id = "edit_employee" name = "edit_employee" class="form-control edit_dropdown-toggle rq2" data-toggle = "dropdown">
                    <input type="hidden" id = "edit_employee_idno" name = "edit_employee_idno">
                    <input type="hidden" id = "uid" name = "uid" value = "">
                    <div class="dropdown-menu form-control">
                      <div class="edit_loader_wrapper" style = "display:none;">
                        <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
                          <div class="form-group row">
                            <div class="col-6 text-right p-0">
                              <h6>Searching ...</h6>
                            </div>
                            <div class="col-6 ">
                              <div class="loader-m"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id = "edit_result_wrapper">

                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="From" class="form-control-label col-form-label-sm d-block">From</label>
                  <select name="edit_dept" id="edit_dept" class="form-control rq2">
                    <option value="">------</option>
                    <?php if($departments->num_rows() > 0 ):?>
                      <?php foreach($departments->result_array() as $dept):?>
                        <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                </div>

                <div class="col-md-4 mb-3">
                  <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                  <input id = "edit_date" name = "edit_date" type="text" class="form-control date_input_empty rq2">
                </div>

                <div class="col-md-4 mb-3">
                  <label for="Re" class="form-control-label col-form-label-sm">Re</label>
                  <input id = "edit_re" name = "edit_re" type="text" class="form-control rq2">
                </div>

                <div class="col-md-4 mb-3">
                  <label for="" class="form-control-label col-form-label-sm">Memorandum File</label>
                  <input id = "edit_memo_file" name = "edit_memo_file" type="text" class="form-control rq2">
                </div>

                <div class="col-md-12">
                  <iframe id = "memo_file_view"  src="" width="" height="400" class = "form-control"></iframe>
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button id = "btn_approved" type = "button" class="btn btn-info">Approve</button>
              <button id = "btn_update" type = "submit" class="btn btn-sm btn-primary">Update</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\reports\memos.js')?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
