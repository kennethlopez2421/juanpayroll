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
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="15" data-namecollapse="" data-labelname="Payroll">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/payroll/'.$token);?>">Payroll</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Payroll History</li>
        </ol>
    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
    <input type="hidden" id='pay_id'>
    <input type="hidden" id='payroll_refno'>
    <input type="hidden" id='manhours_id'>
    <input type="hidden" id='deductions_id'>
    <input type="hidden" id='additionals_id'>
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                                <div class = "form-group row ml-1">
                                    <div class = "col-md-3">
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label class = "form-control-label col-form-label-sm active">Filter by:</label>
                                            <select name="" id="filter_by" class = "form-control">
<!--                                                 <option value="by_department">Department</option> -->
                                                <option value="by_date_generated">Date Generated</option>
                                                <option value="by_pay_type">Pay Type</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class = "col-md-6">
                                    <!--Employee ID-->
<!--                                     <div class="form-group mx-sm-3 mb-2 filter_div" id = "div_dept">
                                        <label class = "form-control-label col-form-label-sm active">Search by Department:</label>
                                        <select name="" id="dept_search" class = "form-control col-md-6">
                                            <option value = "">--------</option>
                                            <?php foreach ($get_department as $gd): ?>
                                                <option value = "<?=$gd->departmentid?>"><?=$gd->description?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div> -->
                                    <!--Date-->
                                    <div class="form-group mx-sm-3 mb-2 filter_div active"  id = "div_date">
                                        <label class = "form-control-label col-form-label-sm active"> Search by Date Generated:</label>
                                        <div class = "row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control date_input date_from_only" id = "date_search" autocomplete="off" placeholder="Search Here">
                                            </div>
                                        </div>
                                    </div>
                                    <!--Paytype-->
                                    <div class="form-group mx-sm-3 mb-2 filter_div" style="display:none;" id = "div_pay_type">
                                        <label class = "form-control-label col-form-label-sm active">Search by Paytype:</label>
                                        <select name="" id="paytype_search" class = "form-control col-md-6">
                                            <option value = "">--------</option>
                                            <?php foreach ($get_paytype as $gp): ?>
                                                <option value = "<?=$gp->paytypeid?>"><?=$gp->description?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class = "col-md-3">
                                    <div class = "pull-right">
                                        <button data-toggle="modal" id="searchButton" class="btn btn-primary text-right btnClickAddArea" style="">Search</button>
                                    </div>
                                </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="payroll_history_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Date Generated</th>
<!--                                             <th>Department</th> -->
                                            <th>Paytype</th>
                                            <th>Company</th>
                                            <th>Date Range</th>
                                            <th>Status</th>
                                            <th width = "190px">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="approve_payroll_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Finalize Payroll</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="deleteCA-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p><span id = "ca_status_prompt_delete">It is suggested to view payroll history summary first to avoid further conflicts. Are you sure you want to finish this process?</span></p>
                                    <input type="hidden" id="deltrsid" class="trsid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                                <button type="button" id="reject_payroll_btn" style="float:right" class="btn btn-danger deltrsbtn"><i class = "fa fa-trash mr-1"></i>Reject</button>
                                <button type="button" id="approve_payroll_btn" style="float:right" class="btn btn-primary deltrsbtn"><i class = "fa fa-check mr-1"></i>Approve</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </section>

    <div class="modal fade" id = "bank_file_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Generate Bank File</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6">
                <label for="Bank" class="form-control-label col-form-label-sm">Bank <span class="asterisk"></span></label>
                <select name="bank" id="bank" class="form-control">
                  <option value="">------</option>
                  <?php if($banks->num_rows() > 0):?>
                    <?php foreach($banks->result_array() as $bank):?>
                      <option value="<?=$bank['bank_id']?>"><?=$bank['bank_name']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
                <input type="hidden" id = "payroll_refno" value = "">
              </div>

              <div class="col-md-6">
                <label for="File Type" class="form-control-label col-form-label-sm">File Type:</label>
                <select name="file_type" id="file_type" class="form-control">
                  <option value="xlsx">.xlsx</option>
                  <option value="xls">.xls</option>
                </select>
              </div>
            </div>

            <div class="form-group row template_wrapper">
              <!-- BDO -->
              <div class="col-md-12 div_template" id = "bdo_template" data-id = "1" style = "display:none">
                <div class="row">
                  <div class="col-md-12">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "bdo_company_name" name = "bdo_company_name"type="text" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label for="File Prefix" class="form-control-label col-form-label-sm">File Prefix</label>
                    <input id = "bdo_file_prefix" name = "bdo_file_prefix" type="text" class="form-control rq">
                  </div>

                  <div class="col-md-6">
                    <label for="Virtual Account" class="form-control-label col-form-label-sm">Virtual Account:</label>
                    <input id = "bdo_virtual_account" name = "bdo_virtual_account" type="text" class="form-control rq">
                  </div>

                  <div class="col-md-6">
                    <label for="Credit Date" class="form-control-label col-form-label-sm">Credit Date</label>
                    <input id = "bdo_credit_date" name = "bdo_credit_date" type="text" class="form-control date_input rq">
                  </div>

                  <div class="col-md-6">
                    <label for="Batch No." class="form-control-label col-form-label-sm">Batch No.</label>
                    <input id = "bdo_batch_no" name = "bdo_batch_no" type="text" class="form-control rq">
                  </div>
                </div>

              </div>
              <!-- METRO BANK -->
              <div class="col-md-12 div_template" id = "metro_bank_template" data-id = "2" style = "display:none">
                <div class="row">
                  <div class="col-md-12">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "metro_company_name" name = "bdo_company_name"type="text" class="form-control rq">
                  </div>
                  <div class="col-md-6">
                    <label for="Branch Code" class="form-control-label col-form-label-sm">Branch Code:</label>
                    <input id = "metro_branch_code" name = "metro_branch_code" type="text" class="form-control rq">
                  </div>
                  <div class="col-md-6">
                    <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                    <input id = "metro_date" name = "metro_date" type="text" class="form-control date_input rq">
                  </div>
                </div>
              </div>
              <!-- CTBC -->
              <div class="col-md-12 div_template" id = "ctbc_template" data-id = "5" style = "display:none">
                <div class="row">
                  <div class="col-md-6">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "ctbc_company_name" name = "ctbc_company_name" type="text" class="form-control rq">
                  </div>
                  <div class="col-6">
                    <label for="Date" class="form-control-label col-form-label-sm">Date:</label>
                    <input id = "ctbc_date" name = "ctbc_date" type="text" class="form-control date_input rq">
                  </div>
                </div>
              </div>
              <!-- DEFAULT -->
              <div class="col-md-12 div_template" id = "default_template" style = "display:none;">
                <div class="row">
                  <div class="col-md-6">
                    <label for="Company Name" class="form-control-label col-form-label-sm">Company Name:</label>
                    <input id = "default_company_name" name = "default_company_name" type="text" class="form-control rq">
                  </div>
                  <div class="col-md-6">
                    <label for="Date" class="form-control-label col-form-label-sm">Date:</label>
                    <input id = "default_date" name = "default_date" type="text" class="form-control date_input rq">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_generate">Generate</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/payroll/payroll_history.js');?>"></script>
