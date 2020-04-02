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
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Entity">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/employees_home/'.$token);?>">Entity</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Issued Items</li>
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
                      <option value="by_cat">Item Category</option>
                      <option value="by_serial">Serial No</option>
                      <option value="by_date_issued">Date Issued</option>
                      <option value="by_date_receive">Date Received</option>
                      <option value="by_date_returned">Date Returned</option>
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

                    <div id="divCat" class="filter_div single_search" style = "display:none;">
                      <label for="Item Category" class="form-control-label col-form-label-sm">Item Category</label>
                      <select class = "form-control searchArea">
                        <option value="">------</option>
                      </select>
                    </div>

                    <div id="divSerial" class="filter_div single_search" style = "display:none;">
                      <label for="Serial No." class="form-control-label col-form-label-sm">Serial No.</label>
                      <input type="text" class="form-control searchArea">
                    </div>

                    <div id="divDateIssued" class = "filter_div dual_search" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" class="form-control from date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control to date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                    <div id="divDateReceived" class = "filter_div dual_search" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" class="form-control from date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control to date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                    <div id="divDateReturned" class = "filter_div dual_search" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" class="form-control from date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control to date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <button class="btn btn-primary" id = "btn_add_modal">Add</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered text-center" id = "issued_items_tbl">
                    <thead>
                      <th>Employee ID</th>
                      <th>Employee Name</th>
                      <th>Item Category</th>
                      <th>Item Name</th>
                      <th>Serial No</th>
                      <th>Condition</th>
                      <th>Date issued</th>
                      <th>Date received</th>
                      <th>Date returned</th>
                      <th>Issued by</th>
                      <th>Action</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ADD MODAL -->
    <div class="modal fade" id = "add_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New Issued Item</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="add_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6 mb-2">
                <label for="Department" class="form-control-label col-form-label-sm">Department: <span class="asterisk"></span></label>
                <select name="dept" id="dept" class="form-control select2 rq">
                  <option value="">------</option>
                  <?php if($department->num_rows() > 0):?>
                    <?php foreach($department->result_array() as $dept):?>
                      <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label for="Employee" class="form-control-label col-form-label-sm">Employee: <span class="asterisk"></span></label>
                <select name="employee" id="employee" class="form-control select2 rq" disabled>
                  <option value="">------</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label for="Item Category" class="form-control-label col-form-label-sm">Item Category: <span class="asterisk"></span></label>
                <select name="item_cat" id="item_cat" class="form-control select2 rq">
                  <option value="">------</option>
                  <?php if($categories->num_rows() > 0):?>
                    <?php foreach($categories->result_array() as $row):?>
                      <option value="<?=$row['id']?>"><?=$row['cat_name']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label for="Condition" class="form-control-label col-form-label-sm">Condition: <span class="asterisk"></span></label>
                <select name="item_condition" id="item_condition" class="form-control select2 rq">
                  <option value="">------</option>
                  <option value="great">Great</option>
                  <option value="good">Good</option>
                  <option value="damage">Damage</option>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label for="Item Name" class="form-control-label col-form-label-sm">Item Name: <span class="asterisk"></span></label>
                <input type="text" class="form-control rq" id="item_name" name = "item_name">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Serial No." class="form-control-label col-form-label-sm">Serial No. <span class="asterisk"></span></label>
                <input type="text" id = "serial_no" name = "serial_no" class="form-control rq">
              </div>

              <div class="col-md-4 mb-2">
                <label for="Date Issued" class="form-control-label col-form-label-sm">Date Issued: <span class="asterisk"></span></label>
                <input type="text" id = "date_issued" name = "date_issued" class="form-control date_input rq">
              </div>
              <div class="col-md-4 mb-2">
                <label for="Date received" class="form-control-label col-form-label-sm">Date received: <span class="asterisk"></span></label>
                <input type="text" id = "date_received" name = "date_received" class="form-control date_input rq">
              </div>
              <!-- <div class="col-md-6 mb-2">
                <label for="Date Returned" class="form-control-label col-form-label-sm">Date Returned: <small>(optional)</small></label>
                <input type="text" id = "date_returned" name = "date_returned" class="form-control date_input_empty ">
              </div> -->

              <div class="col-md-4 mb-2">
                <label for="Price" class="form-control-label col-form-label-sm">Price: <span class="asterisk"></span></label>
                <input type="text" id = "price" name = "price" class="form-control money-input rq">
              </div>

              <div class="col-md-12 mb-2">
                <label for="Note" class="form-control-label col-form-label-sm">Note: <span class="asterisk"></span></label>
                <textarea name="note" id="note" cols="30" rows="3" class="form-control"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" id = "btn_save" class="btn btn-sm btn-primary">Save</button>
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
            <h4 class="modal-title">Update Issued Item</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="edit_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6 mb-2">
                <label for="Employee" class="form-control-label col-form-label-sm">Employee: <span class="asterisk"></span></label>
                <input type="text" id = "emp_name" class = "form-control" readonly>
                <input type="hidden" name = "uid" id = "uid">
              </div>
              <div class="col-md-6"></div>
              <div class="col-md-6 mb-2">
                <label for="Item Category" class="form-control-label col-form-label-sm">Item Category: <span class="asterisk"></span></label>
                <select name="edit_item_cat" id="edit_item_cat" class="form-control select2 rq2">
                  <option value="">------</option>
                  <?php if($categories->num_rows() > 0):?>
                    <?php foreach($categories->result_array() as $row):?>
                      <option value="<?=$row['id']?>"><?=$row['cat_name']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label for="Condition" class="form-control-label col-form-label-sm">Condition: <span class="asterisk"></span></label>
                <select name="edit_item_condition" id="edit_item_condition" class="form-control select2 rq2">
                  <option value="">------</option>
                  <option value="great">Great</option>
                  <option value="good">Good</option>
                  <option value="damage">Damage</option>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label for="Item Name" class="form-control-label col-form-label-sm">Item Name: <span class="asterisk"></span></label>
                <input type="text" class="form-control rq2" id="edit_item_name" name = "edit_item_name">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Serial No." class="form-control-label col-form-label-sm">Serial No. <span class="asterisk"></span></label>
                <input type="text" id = "edit_serial_no" name = "edit_serial_no" class="form-control rq2">
              </div>

              <div class="col-md-6 mb-2">
                <label for="Date Issued" class="form-control-label col-form-label-sm">Date Issued: <span class="asterisk"></span></label>
                <input type="text" id = "edit_date_issued" name = "edit_date_issued" class="form-control date_input_empty rq2">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Date received" class="form-control-label col-form-label-sm">Date received: <span class="asterisk"></span></label>
                <input type="text" id = "edit_date_received" name = "edit_date_received" class="form-control date_input_empty rq2">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Date Returned" class="form-control-label col-form-label-sm">Date Returned: </label>
                <input type="text" id = "edit_date_returned" name = "edit_date_returned" class="form-control date_input_empty ">
              </div>

              <div class="col-md-6 mb-2">
                <label for="Price" class="form-control-label col-form-label-sm">Price: <span class="asterisk"></span></label>
                <input type="text" id = "edit_price" name = "edit_price" class="form-control money-input rq2">
              </div>

              <div class="col-md-12 mb-2">
                <label for="Note" class="form-control-label col-form-label-sm">Note: <span class="asterisk"></span></label>
                <textarea name="edit_note" id="edit_note" cols="30" rows="3" class="form-control"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" id = "btn_update" class="btn btn-sm btn-primary">Update</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- DELETE MODAL -->
    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Confirmation</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="delete_form">
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record <bold class="info_desc"></bold> ?</p>
                <input type="hidden" id = "delid" name = "delid">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" id = "btn_delete" class="btn btn-sm btn-primary">Yes</button>
            <button type = "type" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
          </form>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\employees\issued_items.js')?>"></script>
