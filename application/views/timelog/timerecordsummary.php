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
<div class="content-inner" id="pageActive" data-num="13" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/time_record/'.$token);?>">Time Record</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Time Record Summary</li>
        </ol>
    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
    <input type="hidden" id='all_trs'>
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="">
                            <div class="card-header">
                                <div class = "form-group row ml-1">
                                    <div class = "col-md-3">
                                        <div class="form-group mx-sm-3 mb-2">
                                            <label class = "form-control-label col-form-label-sm active">Filter by:</label>
                                            <select name="" id="filter_by" class = "form-control">
<!--                                                 <option value="by_empid">ID or Name</option>
                                                <option value="by_date_range">Date Range</option>
                                                <option value="by_empid_range">ID or Name and Date Range</option> -->
                                                <option value="by_date_range_only">Date Range Only</option>
                                                <option value="by_empid_date_range">Employee ID and Date Range</option>
                                                <option value="by_name_date_range">Name and Date Range</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class = "col-md-6">
                                    <!--Employee ID-->
<!--                                     <div class="form-group mx-sm-3 mb-2 filter_div active" id = "div_emp_id">
                                        <label class = "form-control-label col-form-label-sm active">Search by ID or Name:</label>
                                        <input type="text" class="form-control search_id" autocomplete="off" placeholder="Search Here">
                                    </div> -->
                                    <!--Date Range-->

<!--                                     <div class="form-group mx-sm-3 mb-2 filter_div" style="display:none;" id = "div_range">
                                        <label class = "form-control-label col-form-label-sm active">Date:</label>
                                        <div class = "row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control date_input date_from_only" autocomplete="off" placeholder="Search Here">
                                                <small class = "form-text">From</small>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control date_input date_to_only" autocomplete="off" placeholder="Search Here">
                                                <small class = "form-text">To</small>
                                            </div>
                                        </div>
                                    </div> -->

                                    <!--Date Range Only-->
                                    <div class="form-group mx-sm-3 mb-2 filter_div active" id = "div_range">
                                        <label class = "form-control-label col-form-label-sm active">Date:</label>
                                        <div class = "row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control datepicker-before date_from_only" autocomplete="off" placeholder="YYYY-MM-DD">
                                                <small class = "form-text">From</small>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control datepicker-before date_to_only" autocomplete="off" placeholder="YYYY-MM-DD">
                                                <small class = "form-text">To</small>
                                            </div>
                                        </div>
                                    </div>


                                    <!--Employee ID and Date Range-->
                                    <div class="form-group mx-sm-3 mb-2 filter_div" style="display:none;" id = "div_id_range">
                                        <label class = "form-control-label col-form-label-sm active">Search by Employee ID and Date Range:</label>
                                        <div class = "row">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control search_id" autocomplete="off" placeholder="Search Here">
                                                <small class = "form-text">Employee ID</small>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control datepicker-before date_from_id" autocomplete="off" placeholder="YYYY-MM-DD">
                                                <small class = "form-text">From</small>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control datepicker-before date_to_id" autocomplete="off" placeholder="YYYY-MM-DD">
                                                <small class = "form-text">To</small>
                                            </div>
                                        </div>
                                    </div>


                                    <!--Employee Name and Date Range-->
                                    <div class="form-group mx-sm-3 mb-2 filter_div" style="display:none;" id = "div_name_range">
                                        <label class = "form-control-label col-form-label-sm active">Search by Name and Date Range:</label>
                                        <div class = "row">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control search_id2" autocomplete="off" placeholder="Search Here">
                                                <small class = "form-text">Name</small>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control datepicker-before date_from_name" autocomplete="off" placeholder="YYYY-MM-DD">
                                                <small class = "form-text">From</small>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control datepicker-before date_to_name" autocomplete="off" placeholder="YYYY-MM-DD">
                                                <small class = "form-text">To</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class = "col-md-3">
                                    <div class = "pull-right">
                                        <button data-toggle="modal" id="searchButton" class="btn btn-primary text-right btnClickAddArea" style=""><i class="fa fa-search"></i>&nbsp;Search</button>
                                        <button style = "display:none;" data-toggle="modal" id="save_toggle" data-target = "#confirm_modal" data-toggle = "modal" class="btn btn-warning text-right btnClickAddArea" style=""><i class="fa fa-save"></i>&nbsp;Save</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                        <div class="card-body" id = "current_trs_div">
                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="currentdate_table"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th>First Time In</th>
                                            <th>Last time Out</th>
                                            <th width="80">Date</th>
                                            <th>ID Number</th>
                                            <th>Name</th>
                                            <th>Man hours</th>
                                            <th>Lates(mins)</th>
                                            <th>Overbreak</th>
                                            <th>Undertime(mins)</th>
                                            <th>Absent</th>
                                            <th>Total Minutes</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="card-body" id = "trs_div" style="display: none;">
                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="timerecord_table"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th>First Time In</th>
                                            <th>Last time Out</th>
                                            <th width="80">Date</th>
                                            <th>ID Number</th>
                                            <th>Name</th>
                                            <th>Man hours</th>
                                            <th>Lates(mins)</th>
                                            <th>Overbreak</th>
                                            <th>Undertime(mins)</th>
                                            <th>Absent</th>
                                            <th>Total Minutes</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div id="confirm_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 id="exampleModalLabel" class="modal-title"><b>Confirm Saving</b></h4>
                                        <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                                    </div>
                                    <form class="form-horizontal personal-info-css" id="deleteCA-form">
                                        <div class="modal-body">
                                            <div class="">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <p><span id = "ca_status_prompt_delete" class="">Some of Data Generated is not yet saved. Do you want to save data?</span></p>
                                                        <input type="hidden" id="deltrsid" class="trsid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <button type="button" id="save_btn" style="float:right" class="btn btn-primary">Save Data</button>
                                                    <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <input type="hidden" id = "arrayctr">

<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/time_record/timerecord.js');?>"></script>
