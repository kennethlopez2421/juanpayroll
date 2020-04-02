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

<style>
  #rf_logo{
    font-size: 100px !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="24" data-namecollapse="" data-labelname="Register Id">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/registerid_home/'.$token);?>">Register Id</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Rf Id Number</li>
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
                      <option value="by_id">Rf Id Number</option>
                      <option value="by_name">Employee Name</option>
                      <option value="by_pos">Position</option>
                      <option value="by_dept">Department</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divRfId" class = "filter_div active">
                      <label for="Biometrics ID" class="form-control-label col-form-label-sm">Biometrics ID</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divName" class = "filter_div" style = "display:none;">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divPos" class="filter_div" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Position</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($positions->num_rows() > 0):?>
                          <?php foreach($positions->result_array() as $pos):?>
                            <option value="<?=$pos['positionid']?>"><?=$pos['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divDept" class="filter_div" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_rf">Register</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "gen_rf_tbl">
                    <thead>
                      <th>Rf Id Number</th>
                      <th>Employee Name</th>
                      <th>Position</th>
                      <th>Department</th>
                      <th>Status</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- RF MODAL -->
    <div class="modal fade" id = "rf_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Register Rf IdNumber</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12 text-center">
                <div class="col-12">
                  <i class="fa fa-id-card-o" id = "rf_logo"></i>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary" id = "btn_scan">Scan</button>
                  <div class="rf_wrapper" style = "display:none;">
                    <div class="qr_code" style = "width: 0px;overflow:hidden;">
                      <input type="text" id = "reg_rf_idnumber">
                    </div>
                    <small class = 'd-block'>Waiting for Rf Scan</small>
                    <img src="<?=base_url('assets/img/loader3.gif')?>" alt="" style = "width:100px;height:40px;">
                  </div>
                </div>
              </div>
              <div class="col-md-12 mb-3">
                <label for="Employee Id" class="form-control-label col-form-label-sm">Employee Id <span class="asterisk"></span></label>
                <input type="text" id = "reg_employee_idno" class="form-control" placeholder="Enter Employee Id number">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-primary">Scan Rf ID</button> -->
            <button class="btn btn-sm btn-primary" id = "btn_reg_rfid">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- CONFIRM MODAL -->
    <div class="modal fade" id = "confirm_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Oops!</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <input type="hidden" id = "confirm_rfid">
            <input type="hidden" id = "confirm_empid">
          </div>
          <div class="modal-body">
            <h4 id = "confirm_msg"></h4>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">No</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\registerid\register_rf.js')?>"></script>
