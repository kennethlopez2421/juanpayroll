
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=company_name();?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/bootstrap.min.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/jquery-ui.css');?>">
    <!-- Google fonts - Roboto -->
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/google_fonts.css');?>"> -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <!-- theme stylesheet--><!-- we change the color theme by changing color.css -->
    <link rel="stylesheet" href="<?=base_url('assets/css/style.blue.css');?>" id="theme-stylesheet">
    <link rel="stylesheet" href="<?=base_url('assets/css/select2-materialize.css');?>">
    <!-- Custom stylesheet - for your changes--> 
    <link rel="stylesheet" href="<?=base_url('assets/css/custom.css');?>">
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?=base_url('assets/img/pandabookslogo.png');?>">
    <!-- Font Awesome CDN-->
    <!-- you can replace it by local Font Awesome-->
   <link rel="stylesheet" href="<?=base_url('assets/css/font-awesome.min.css');?>">
    <!-- Font Icons CSS-->
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/myfontastic.css');?>"> -->
    <!-- Jquery Datatable CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/datatables.min.css');?>">
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/jquery.dataTables.css');?>"> -->
    <!-- Jquery Select2 CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css');?>">
    <!-- Bootstrap Datepicker CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/bootstrap-datepicker3.min.css');?>">
    <!-- Jquery Toast CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/easy-autocomplete.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/mdb.min.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>">
    
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>


<body data-base_url="<?=base_url();?>" style="">
<div style="">
<div class="form-group-material pull-right" >
            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#viewAddrowModal" id="add_item_btn" class="btn btn-primary btnUpdate btnTable " name="update">
                Add Item
            </button>
        </div>

        <div class="table-responsive">
                
            <table class="table  table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th width="50">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
   
       <div class="col-md-4 pr-0" style="float: right;">
            <button type="button" class="btn btn-block ml-0 mb-2 border-deco-left blue-grey pd-0" data-toggle="modal" data-target="#addAShippingModal" style="width: 100%; font-size: 15px; line-height: 20px;" id="" ><i class="fa fa-truck white-text fa-lg" style="float: left; margin-left: -18px; margin-top: 10px;"></i><a class="btnShipping">Shipping: 0.00</a></button>

            <input type="text" class="ship_hide" id="ship_hide" value="0" hidden>

            <button type="button" class="btn btn-md btn-block ml-0 mt-0 pr-0 border-deco-left blue-grey" id="grand_total" style="width: 100%;font-size: 15px; line-height: 20px;" disabled><i class="fa fa-cart-plus white-text fa-lg" style="float: left; margin-left: -18px; margin-top: 10px;"></i><a class="grand_total">Total: 0.00</a></button>

            <input type="text" class="grandtotal_hide" id="grandtotal_hide" value="0" hidden>
        </div>

        <div class="form-group" style="margin-top: 10px">
            <textarea class="form-control txtarea" rows="3" style="resize: none;" placeholder="Notes..." id="txtareanotes"></textarea>
        </div>
</div> <!-- tbl-details -->
</body>
    <div id="viewAddrowModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left" name="viewDeformModal">
        <div role="document" class="modal-dialog modal-md modal-md-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add item</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-body">
                                        
                                <div class="col-lg-12">     
                                    <form id="addRow">
                                        <div class="col-lg-12">

                                            <!-- Itemname -->
                                            <div class="form-group row">
                                              <label class="col-md-4 form-control-label">Item<span class="" style="color:red">*</span></label>
                                              <div class="col-md-8">
                                                 <input type="text" class="form-control required_fields form-control-sm col-md-12 searchSalesorder loading" name="searchSalesorder" id="searchSalesorder" placeholder="Item Name"/>
                                                 <input type="hidden" class="searchSalesorderCode_id w-100"  name="searchSalesorderCode_id" id="searchSalesorderCode_id" required>
                                              </div>
                                            </div>

                                            <input type="text" class="form-control allownumericwithdecimal price required_fields form-control-sm col-md-12 price" name="price" id="price" oninput="validity.valid||(value='');" min="" placeholder="Price" value="100" hidden>
                                                        
                                            <!-- QTY -->
                                            <div class="form-group row">
                                              <label class="col-md-4 form-control-label">Quantity<span class="" style="color:red">*</span></label>
                                              <div class="col-md-8">
                                                 <input type="text" class="form-control allownumericwithdecimal material_josh required_fields form-control-sm col-md-12 qty" name="qty" id="qty" oninput="validity.valid||(value='');" min="" placeholder="Quantity">
                                              </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-4 form-control-label">Discount Type</label>
                                                <div class="col-md-8">
                                                    <select class="form-control form-control-success discount_type_select" id="discount_type_select">   
                                                        <option value="">Select Discount Type</option>
                                                        <option value="1">Amount</option>
                                                        <option value="2">Percentage</option>
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="form-group row percentage_div" style="display: none;">
                                              <label class="col-md-4 form-control-label">Discount Percentage<span class="" style="color:red">*</span></label>
                                              <div class="col-md-8">
                                                 <input class="form-control form-control-success disc w-100 allownumericwithdecimal" min="" type="number" name="disc_percent" id="disc_percent" required="" required="" placeholder="Discount Percent.." max="100" value="0">
                                              </div>
                                            </div>

                                            <div class="form-group row amount_div" style="display: none;">
                                              <label class="col-md-4 form-control-label">Discount Amount<span class="" style="color:red">*</span></label>
                                              <div class="col-md-8">
                                                 <input class="form-control form-control-success disc w-100 allownumericwithdecimal" min="" type="number" name="disc_amt" id="disc_amt" required="" required="" placeholder="Discount Amount.." value="0">
                                              </div>
                                            </div>                                                  
                                                                                         
                                        </div>
                                    </form>
                                </div>                                       

                                <div class="modal-footer">
                                    <div class="form-group row float-right">       
                                        <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                                        <span style="margin: 5px;"></span>
                                        <button type="button" class="btn btn-primary add_inventory_modal">Add Inventory</button>
                                    </div>
                                </div>
                            </div> <!-- card body -->   
                        </div>
                    </div>
                </div> <!-- modal body -->
            </div> <!-- modal content -->
        </div>
    </div>

<div id="addAShippingModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Shipping</h4>
                </div>
                <form class="form-horizontal personal-info-css" data-toggle="validator" role="form" id="forminv">
                    <div class="modal-body">
                        <!-- <div class="card-header d-flex align-items-center"> -->
                            <div class="col-lg-12">
                                <div class="row">

                                    <div class="form-group row col-md-12">
                                        <div class="col-12">
                                            <label class="form-control-label " type="text">Shipping Amount<span class="" style="color:red">*</span></label>
                                        </div>
                                        <div class="col-12">
                                            <input class="form-control valid_number allownumericwithdecimal" min="0" type="text" id="shipping" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- </div> -->

                            <div class="modal-footer">
                                <div class="form-group row">
                                    <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close" style="margin-right: 10px;">Cancel</button>
                                    <button type="button" aria-label="Close" data-dismiss="modal" class="btn btn-primary btnassignShip">Add</button>     
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

</html>

<!-- footer -->
<?php $this->load->view('includes/footer'); ?>
<!-- footer -->

<!-- javascript -->
<script src="<?=base_url('assets/js/dynamic_table.js');?>"></script>
<!-- javascript -->