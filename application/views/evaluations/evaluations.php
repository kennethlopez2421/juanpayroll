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
<div class="content-inner" id="pageActive" data-num="23" data-namecollapse="" data-labelname="Evaluations">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/evaluations_home/'.$token);?>">Evaluations</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Evaluations History</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <!-- <?php echo en_dec('dec', 'SEkvOTVzTWZWQ0NyNjhmM1pBWlk3d');?> -->
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
                      <option value="by_id">Employee Id</option>
                      <option value="by_name">Employee Name</option>
                      <option value="by_dept">Department</option>
                      <option value="by_date">Evaluation Date</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divEmpID" class = "filter_div active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Employee ID</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divName" class = "filter_div" style = "display:none;">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divDept" class="filter_div" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea">
                        <option value="">------</option>
                      </select>
                    </div>

                    <div id="divDate" class = "filter_div" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "date_from" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id = "date_to" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="nav nav-tabs">
                  <?php if($this->session->login_type == 'admin'):?>
                    <li class="nav-item">
                      <a class="nav-link active" data-status = "ongoing" data-toggle="tab" href="#pending_tab" style="color:black;">Ongoing</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-status = "evaluated" data-toggle="tab" href="#approved_tab" style="color:black;" >Evaluated</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-status = "certified" data-toggle="tab" href="#certified_tab" style="color:black;" >Certified</a>
                    </li>
                  <?php else:?>
                    <li class="nav-item">
                      <a class="nav-link active" data-status = "evaluated" data-toggle="tab" href="#approved_tab" style="color:black;" >Evaluated</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-status = "certified" data-toggle="tab" href="#certified_tab" style="color:black;" >Certified</a>
                    </li>
                  <?php endif;?>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane fade <?=($this->session->login_type == 'admin') ? 'show active' : ''?>" id = "pending_tab">
                    <div class="table-responsive mt-4">
                      <table class="table table-bordered table-striped" id = "pending_eval_tbl" style = "border-top: 1px solid gainsboro;">
                        <thead>
                          <th>Employee Name</th>
                          <th>Evaluator</th>
                          <th>Department</th>
                          <th>Evaluation Date</th>
                          <th>Covered Period</th>
                          <th>Status</th>
                          <th>Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane fade <?=($this->session->login_type != 'admin') ? 'show active' : ''?>" id = "approved_tab">
                    <div class="table-responsive mt-4">
                      <table class="table table-bordered table-striped" id = "evaluated_eval_tbl" style = "border-top: 1px solid gainsboro;">
                        <thead>
                          <th>Employee Name</th>
                          <th>Evaluator</th>
                          <th>Department</th>
                          <th>Evaluation Date</th>
                          <th>Covered Period</th>
                          <th>Status</th>
                          <th>Action</th>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane fade" id = "certified_tab">
                    <div class="table-responsive mt-4">
                      <table class="table table-bordered table-striped" id = "certified_eval_tbl" style = "border-top: 1px solid gainsboro;">
                        <thead>
                          <th>Employee Name</th>
                          <th>Evaluator</th>
                          <th>Department</th>
                          <th>Evaluation Date</th>
                          <th>Covered Period</th>
                          <th>Status</th>
                          <th>Action</th>
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
    <!-- REASSIGN MODAL -->
    <div class="modal fade" id = "reassign_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create Evaluation for <span id="modal_title"><u></u></span></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">

            <div class="form-group row">
              <div class="col-md-12 mb-3">
                <label for="Department" class="form-control-label col-form-label-sm">Send To</label>
              </div>

              <div class="col-md-12 mb-3">
                <input type="hidden" id = "employee_idno">
                <select name="dept" id="dept" class="form-control select2">
                  <option value="">------</option>
                  <option value="0">Higher Department</option>
                  <?php if($departments->num_rows() > 0):?>
                    <?php foreach($departments->result_array() as $dept):?>
                      <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                    <?php endforeach;?>
                  <?php endif?>
                </select>
                <small class="form-text">Department <span class="asterisk"></span></small>
              </div>
              <div class="col-md-12 mb-3">
                <select name="pos_lvl" id="pos_lvl" class="form-control select2" disabled>
                  <option value="">------</option>
                  <?php if($position_lvl->num_rows() > 0):?>
                    <?php foreach($position_lvl->result() as $row):?>
                      <option value="<?=$row->hierarchy_lvl?>"><?=$row->position?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
                <small class="form-text">Position <span class="asterisk"></span></small>
              </div>
              <div class="col-md-12">
                <select name="hris_users" id="hris_users" class = "form-control select2" disabled>
                  <option value="">------</option>
                </select>
                <small class="form-text">Employee <span class="asterisk"></span></small>
                <input type="hidden" id = "eval_id">
              </div>

            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_reassign_eval">Reassign</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Delete Evaluation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete the evaluation for (<bold class="info_desc"></bold>) ?</p>
                <input type="hidden" class="employeeid" id = "eval_del_id">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button id = "btn_yes2" class="btn btn-sm btn-primary">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\evaluations\evaluations.js')?>"></script>
