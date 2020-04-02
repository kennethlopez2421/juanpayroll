<style>
    .kbw-signature { width: 300px; height: 100px; }

    .backdrop{
        position: fixed;
        top: 0px;
        left: 0px;
        z-index: 999;
        height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, 0.2);
        display: none;
    }
    .scan_barcode{
        height: 250px;
        width: 300px;
        background-color: white;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translateX(-50%) translateY(-50%);
    }
</style>
<!-- 
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
 -->
<div class="content-inner" id="pageActive" data-num="5" data-namecollapse="" data-labelname="Entity">
    <!-- Page Header-->
    <header class="page-header" data-token="<?=$token?>">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Franchise Payment Details</h2>
        </div>
    </header>
    <!-- Breadcrumb-->
    <ul class="breadcrumb">
        <div class="container-fluid"> 
        	<li class="breadcrumb-item"><a href="<?=base_url('Main_page/display_page/entity_home/'.$token);?>">Entity</a></li>      
	        <li class="breadcrumb-item"><a href="<?=base_url('Main_entity/applicant/'.$token);?>">Applicant</a></li>
	       	<li class="breadcrumb-item">Franchise Payment Details</li>
        </div>
    </ul>
    <section class="tables">   
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?php echo base_url();?>Main_entity/export_application_form_report" method="post" target="_blank">
                                <div class="form-group row">       
                                    <div class="col-md-12">
                                       <button type="button" style="float:right;margin-left:7px" class="btn btn-primary filterBtn">Search</button>
                                       <button type="submit" style=" float: right;" class="btn btn-default btn_export_excel" name="btn_export_excel" id="btn_export_excel" hidden>Export To Excel</button>
                                     <!--   <button type="button" style="float:right;" class="btn btn-primary btnScanQrCode">Scan QR Code</button> -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label col-form-label-sm">Application Date</label>
                                            <div class="input-daterange input-group" id="datepicker">
                                                <input type="text" data-column="0" class="input-sm form-control material_josh search-input-select searchDate searchDateFrom" name="start" value="<?=today_date();?>" />
                                                <span class="input-group-addon" style="background-color:#fff0 ; border:none; padding:10px !important;">to</span>
                                                <input type="text" data-column="1" class= "input-sm form-control material_josh search-input-select searchDate2 searchDateTo" name="end" value="<?=today_date();?>" />
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-control-label col-form-label-sm">Applicant Name</label>
                                            <input type="text" data-column="2"  name="searchAppName" class="form-control material_josh form-control-sm search-input-text searchAppName" placeholder="Applicant Name">
                                        </div>
                                    </div>

                                </div>
                            <br><br>
                            <div class="table-responsive table_fis" style="display: none">
                                 <table class="table table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <!-- <th>FIS Applicant ID</th> -->
                                            <th>Applicant Fullname</th>
                                            <th>Contact Number</th>
                                            <th>Application Date</th>
                                            <th>Type of Ocular</th>
                                            <th>Concepts</th>
                                            <th width="80" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                </table>	
                            </div>
                        </form>
                        </div>
                        <div class="backdrop" id="backdrop">
                            <div class="scan_barcode text-center" style="padding:20px;">
                                <i class="fa fa-qrcode" style="font-size:120px;color:lightblue"></i>
                                <h4>Scan QR Code from Application Form</h4>
                                <br>
                                <button class="btn btn-primary btnStopScan">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <br><br><br><br><br><br>
<?php $this->load->view('includes/footer'); ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script> -->
<script src="<?=base_url('assets/js/entity/accounting/franchise_payment_details.js');?>"></script>




