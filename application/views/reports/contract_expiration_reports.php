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
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Contract Expiration Reports</li>
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
                      <option value="by_id">Employee Id</option>
                      <option value="by_name">Employee Name</option>
                      <option value="by_dept">Department</option>
                      <option value="by_pos">Position</option>
                      <option value="by_date">Date of Separation</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divEmpID" class = "filter_div single_search active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Employee ID</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divName" class = "filter_div single_search" style = "display:none;">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divDept" class="filter_div single_search" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif?>
                      </select>
                    </div>

                    <div id="divPos" class="filter_div single_search" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($positions->num_rows() > 0):?>
                          <?php foreach($positions->result_array() as $pos):?>
                            <option value="<?=$pos['positionid']?>">(<?=$pos['dept']?>) <?=$pos['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divDate" class = "filter_div range_date" style = "display:none;">
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
                    <!-- <a href="<?= base_url('transactions/Additionalpays/add/'.$token) ?>"><button class="btn btn-primary btnClickAddArea">Add</button></a> -->
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-reponsive">
                  <table class="table table-bordered table-striped" id = "contract_expiration_reports_tbl">
                    <thead>
                      <th>Employee Id</th>
                      <th>Employee Name</th>
                      <th>Department</th>
                      <th>Position</th>
                      <th>Date of Expiration</th>
                      <th>Actions</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id = "create_evaluation_modal">
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
                <label for="Evaluation Date:" class="form-control-label col-form-label-sm">Evaluation Date:</label>
                <input type="text" id = "eval_date" class="form-control datepicker-after">
                <div class="col-md-12 mb-3">
              </div>
                <label for="Date Covered:" class="form-control-label col-form-label-sm">Date Covered:</label>
                <div class="row">
                  <div class="col-md-6">
                    <input type="text" id = "eval_from" class="form-control date_input">
                    <small class="form-text">From <span class="asterisk"></span></small>
                  </div>

                  <div class="col-md-6">
                    <input type="text" id = "eval_to" class="form-control date_input">
                    <small class="form-text">To <span class="asterisk"></span></small>
                  </div>
                </div>
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
                  <?php if(count((array)$position_lvl) > 0):?>
                    <?php foreach($position_lvl as $row):?>
                      <option value="<?=$row['hierarchy_lvl']?>"><?=$row['position']?></option>
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
              </div>

            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_send_evaluation">Send</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\reports\contract_expiration_reports.js')?>"></script>
