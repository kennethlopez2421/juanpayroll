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
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">TAX</li>
        </ol>
    </div>
    
    <section class="tables">   
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="">
                            <div class="card-header d-flex align-items-center">

                                <div class="col-lg-12">
                                    <br>
                                    <div class="row">

 <!--                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">TAX</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <button data-toggle="modal" id="newTaxBtn" data-backdrop="static" data-keyboard="false" data-target="#addTaxModal" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:10px; width: 100px;"><i class = "fa fa-plus"></i>&nbsp;Add</button>

                            <button id="ctrlBtn" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:5px; width: 140px;"><i class = "fa fa-gamepad"></i>&nbsp;Modify Table</button>                            
        
                            <div class="table-responsive">
                                <table id="taxTableView" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Annual Income Bracket</th>
                                            <th colspan="2">Tax Rate ( 2018 - 2022 )</th>
                                            <th colspan="2">Tax Rate ( 2023 Onwards )</th>
                                        </tr>
                                        <tr>
                                            <th>Lower Limit</th>
                                            <th>Upper Limit</th>
                                            <th>Tax on lower limit</th>
                                            <th>Tax on excess over lower limit</th>
                                            <th>Tax on lower limit</th>
                                            <th>Tax on excess over lower limit</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($datas as $data): ?>
                                            <tr>

                                                <td><?= ($data->aibLowerLimit == 0) ? "-" : number_format($data->aibLowerLimit,2) ?></td>

                                                <?php if($data->aibUpperLimit == 8000001): ?>
                                                    <td>Above</td>
                                                <?php else: ?>
                                                     <td><?= ($data->aibUpperLimit == 0) ? "-" : number_format($data->aibUpperLimit,2) ?></td>
                                                <?php endif; ?>
                                                <td><?= ($data->tr1LowerLimit == 0) ? "-" : number_format($data->tr1LowerLimit,2) ?></td>
                                                <td><?= $data->tr1ExcessLimit."%" ?></td>
                                                <td><?= ($data->tr2LowerLimit == 0) ? "-" : number_format($data->tr2LowerLimit,2) ?></td>
                                                <td><?= $data->tr2ExcessLimit."%" ?></td>
     
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                    
                                </table>

                                <table id="taxTable" style="border-collapse: collapse;" class="table table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%" style="overflow-y:auto;">
<!--                                     <colgroup>
                                        <col span="2" style="background-color:teal; ">
                                        <col span="2" style="background-color:teal; ">
                                        <col span="2" style="background-color:teal; ">
                                        <col>
                                    </colgroup> -->
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="th_top" style=" border-bottom:1px solid #c8c8c8;"><b>Annual Income Bracket</b></th>
                                            <th colspan="2" class="th_top" style=" border-bottom:1px solid #c8c8c8;"><b>Tax Rate ( 2018 - 2022 )</b></th>
                                            <th colspan="2" class="th_top" style=" border-bottom:1px solid #c8c8c8;"><b>Tax Rate ( 2023 Onwards )</b></th>
                                            <th style="border-bottom: 1px solid #c8c8c8;"><b>Actions</b></th>
                                        </tr>
                                        <tr>
                                            <th style="border-bottom: 1px solid #c8c8c8;">Lower Limit</th>
                                            <th>Upper Limit</th>
                                            <th style="border-bottom: 1px solid #c8c8c8;">Tax Rate Lower Limit</th>
                                            <th>Tax Rate Excess Limit</th>
                                            <th style="border-bottom: 1px solid #c8c8c8;">Tax Rate Lower Limit</th>
                                            <th>Tax Rate Excess Limit</th>
                                            <th style="border-bottom: 1px solid #c8c8c8;"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal-->
    <div id="addTaxModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add</h4>
                </div>
                <form class="form-horizontal personal-info-css" id="add_area-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <h3>Annual Income Bracket</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Lower Limit</label>
                                <input type="text" id="aibLowerLimit" class="form-control" autocomplete="off"> 
                            </div>
                            <div class="col-md-6">
                                <label>Upper Limit</label>
                                <input type="text" id="aibUpperLimit" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <hr style="height: 2px;">
                        <div class="row">
                            <div class="col-md">
                                <h3>Tax Rate (2018-2022)</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tax on lower limit</label>
                                <input type="text" id="tr1LowerLimit" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label>Tax on excess over lower limit</label>  
                                <div class="form-inline">
                                    <input type="text" id="tr1ExcessLimit" class="form-control" autocomplete="off" style="width: 50%; margin-right: 2%;"> %
                                </div>                            
                            </div>
                        </div>
                        <hr style="height: 2px;">
                        <div class="row">
                            <div class="col-md">
                                <h3>Tax Rate (2023 Onwards)</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tax on lower limit</label>
                                <input type="text" id="tr2LowerLimit" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label>Tax on excess over lower limit</label>  
                                <div class="form-inline">
                                    <input type="text" id="tr2ExcessLimit" class="form-control" autocomplete="off" style="width: 50%; margin-right: 2%;"> %
                                </div>                            
                            </div>
                        </div>                        
                    </div>
                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="button" id="addTaxModalBtn" style="float:right" class="btn btn-success">Add</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editTaxModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update Tax</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="update_area-form">
                                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <h3>Annual Income Bracket</h3>
                                <input type='hidden' class='taxid'>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Lower Limit</label>
                                <input type="text" id="editAibLowerLimit" class="form-control" autocomplete="off"> 
                            </div>
                            <div class="col-md-6">
                                <label>Upper Limit</label>
                                <input type="text" id="editAibUpperLimit" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md">
                                <h3>Tax Rate (2018-2022)</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tax on lower limit</label>
                                <input type="text" id="editTr1LowerLimit" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label>Tax on excess over lower limit</label>  
                                <div class="form-inline">
                                    <input type="text" id="editTr1ExcessLimit" class="form-control" autocomplete="off" style="width: 50%; margin-right: 2%;"> %
                                </div>                            
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md">
                                <h3>Tax Rate (2023 Onwards)</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tax on lower limit</label>
                                <input type="text" id="editTr2LowerLimit" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label>Tax on excess over lower limit</label>  
                                <div class="form-inline">
                                    <input type="text" id="editTr2ExcessLimit" class="form-control" autocomplete="off" style="width: 50%; margin-right: 2%;"> %
                                </div>                            
                            </div>
                        </div>                        
                    </div>
                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="button" id="editTaxBtn" style="float:right" class="btn btn-primary">Save Changes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="delTaxModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete Area</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete record ?</p>
                                    <input type='hidden' class='taxid'>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="button" id="delTaxBtn" style="float:right" class="btn btn-primary deleteAreaBtn">Delete Record</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?= base_url("assets/js/settings/tax.js") ?>"></script>