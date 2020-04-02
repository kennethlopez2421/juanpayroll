<?php

 if(!isset($_SESSION['user_id'])) {
   header(base_url('Main/logout'));
 }

?>

<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href = "<?=base_url('transactions/Workorder/index/'.$token)?>">Work Order</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">New Work Order</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4>Create Work Order</h4>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select name="dept" id="dept" class="form-control select2" style = "height:41px;">
                        <option value="">------</option>
                        <?php if($department->num_rows() > 0):?>
                          <?php foreach($department->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class = "form-control-label col-form-label-sm active">Employee ID No.<span class = "ml-2 text-danger" >*</span></label>
                      <select id="employee_id_no" class="form-control select2 req" style = "width:100%;">
                        <option value="">------</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-md-3">
                    <input type="text" class="form-control <?=($this->session->login_type == 'admin') ? 'date_input' : 'datepicker-after'?> req" id = "wo_date" name = "wo_date">
                    <small class="form-text">Date <span class="asterisk"></span></small>
                  </div>
                  <div class="col-md-3">
                    <input type="time" class="form-control req" id = "wo_sTime" name = "wo_sTime">
                    <small class="form-text">Start Time <span class="asterisk"></span></small>
                  </div>
                  <div class="col-md-3">
                    <input type="time" class="form-control req" id = "wo_eTime" name = "wo_eTime">
                    <small class="form-text">End Time <span class="asterisk"></span></small>
                  </div>
                  <div class="col-md-3">
                    <button class="btn btn-sm btn-primary" id = "btnItinerary_modal">Add Itinerary</button>
                  </div>
                </div>
                <div class="form-group row div_it">
                  <!-- <div class="col-12">
                    <h4>Itinerary</h4>
                    <div class="row">
                      <div class="col-md-4 mb-3">
                        <input type="text" class="form-control req" name = "location" id = "location">
                        <small class="form-text">Location <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-4 mb-3">
                        <input type="text" class="form-control req" name = "contact_person" id = "contact_person">
                        <small class="form-text">Contact Person <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-4 mb-3">
                        <input type="text" class="form-control req" name = "contact_num" id = "contact_num">
                        <small class="form-text">Contact Number <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-4 mb-2">
                        <textarea name="purpose" id="purpose" cols="30" rows="5" class="form-control req"></textarea>
                        <small class="form-text">Purpose <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-4 mb-2">
                        <textarea name="notes" id="notes" cols="30" rows="5" class="form-control req"></textarea>
                        <small class="form-text">Notes <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-4">
                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                      </div>
                    </div>
                  </div> -->
                </div>
              </div>
              <div class="card-footer text-right">
                <button class="btn btn-sm btn-primary" id = "btn_saveWorkOrder">Save</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- itinerary modal -->

<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/transactions/workorder_add.js')?>"></script>
<script src = "<?=base_url('assets/js/transactions/workorder.js')?>"></script>
