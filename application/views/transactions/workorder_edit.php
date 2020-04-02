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
            <li class="breadcrumb-item active">Edit Work Order</li>
        </ol>
    </div>

    <section class="tables">
      <div class="container-fluid">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body" id = "work-order-form">
              <div class="form-group row">
                <div class="col-md-6">
                  <div class="form-grouup">
                    <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                    <select name="dept" id="dept" class="form-control select2" style = "height:41px;">
                      <option value="">------</option>
                      <?php if($department->num_rows() > 0):?>
                        <?php foreach($department->result_array() as $dept):?>
                          <option value="<?=$dept['departmentid']?>" <?=($dept['departmentid'] == $workOrder['deptId']) ? "SELECTED": ""?>>
                            <?=$dept['description']?>
                          </option>
                        <?php endforeach;?>
                      <?php endif;?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class = "form-control-label col-form-label-sm active">Employee ID No.<span class = "ml-2 text-danger" >*</span></label>
                    <select id="employee_id_no" class="form-control select2">
                      <?php if($employee->num_rows() > 0):?>
                        <?php foreach($employee->result_array() as $emp):?>
                          <option value="<?=$emp['employee_idno']?>" <?=($emp['employee_idno'] == $workOrder['employee_id'])? "SELECTED" : ""?>>
                            <?=$emp['fullname']?> (<?=$emp['employee_idno']?>)
                          </option>
                        <?php endforeach;?>
                      <?php endif;?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-3">
                  <input type="text" class="form-control <?=($this->session->login_type == 'admin') ? 'date_input_empty' : 'datepicker-after_empty'?> req" id = "wo_date" name = "wo_date" value = "<?=$workOrder['date']?>">
                  <small class="form-text">Date <span class="asterisk"></span></small>
                </div>
                <div class="col-md-3">
                  <input type="time" class="form-control req" id = "wo_sTime" name = "wo_sTime" value = "<?=$workOrder['start_time']?>">
                  <small class="form-text">Start Time <span class="asterisk"></span></small>
                </div>
                <div class="col-md-3">
                  <input type="time" class="form-control req" id = "wo_eTime" name = "wo_eTime" value = "<?=$workOrder['end_time']?>">
                  <small class="form-text">End Time <span class="asterisk"></span></small>
                </div>
                <!-- <div class="col-md-4">
                  <button class="btn btn-sm btn-primary" id = "btnItinerary_modal">Add Itinerary</button>
                </div> -->
                <div class="form-group row div_it p-3">
                  <?php if($itinerary->num_rows() > 0):?>
                    <?php $itCounter = 0;?>
                    <?php foreach($itinerary->result_array() as $it):?>
                      <?php $itCounter++;?>
                      <form class = "itForm">
                        <input type="hidden" name = "updateid" value = "<?=$it['id']?>">
                        <div class="col-12 itContainer mb-3" style = "border-bottom:1px solid gainsboro;">
                          <h4 class = "mb-3">
                            Itinerary <?=$itCounter?>
                            <?php if($workOrder['status'] == 'waiting'):?>
                              <button type = "button" data-delid = "<?=$it['id']?>" class="btn btn-sm btn-danger btn_del_itinerary float-right"><i class="fa fa-trash"></i></button>
                              <button type = "submit" class="btn btn-sm btn-info btn_update_itinerary float-right"><i class="fa fa-pencil"></i></button>
                            <?php endif;?>

                            <?php if($workOrder['status'] != 'waiting' && $this->session->position_lvl <= hr_sup_or_above()):?>
                              <button type = "button" data-delid = "<?=$it['id']?>" class="btn btn-sm btn-danger btn_del_itinerary float-right"><i class="fa fa-trash"></i></button>
                            <?php endif;?>
                          </h4>
                          <div class="row">
                            <div class="col-md-4 mb-3">
                              <input type="text" class="form-control req" id = "location<?=$itCounter?>" name = "location" value = "<?=$it['location']?>">
                              <small class="form-text">Location <span class="asterisk"></span></small>
                            </div>
                            <div class="col-md-4 mb-3">
                              <input type="text" class="form-control req" id = "contact_person<?=$itCounter?>" name = "contact_person" value = "<?=$it['contact_person']?>">
                              <small class="form-text">Contact Person <span class="asterisk"></span></small>
                            </div>
                            <div class="col-md-4 mb-3">
                            <input type="text" class="form-control contactNumber req" id="contact_num<?=$itCounter?>" name = "contact_num" value = "<?=$it['contact_num']?>">
                              <small class="form-text">Contact Number <span class="asterisk"></span></small>
                            </div>
                            <div class="col-md-6 mb-2">
                              <textarea name="purpose" id="purpose<?=$itCounter?>" cols="30" rows="5" class="form-control req"><?php echo $it['purpose'];?></textarea>
                              <small class="form-text">Purpose <span class="asterisk"></span></small>
                            </div>
                            <div class="col-md-6 mb-2">
                              <textarea id = "notes<?=$itCounter?>" name = "notes" cols="30" rows="5" class="form-control req"><?php echo $it['notes'];?></textarea>
                              <small class="form-text">Notes <span class="asterisk"></span></small>
                            </div>
                          </div>
                        </div>
                      </form>
                    <?php endforeach;?>

                  <?php else:?>
                    <div class="col-12 text-center">
                      <h4>No available itinerary</h4>
                    </div>
                  <?php endif;?>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <?php if($workOrder['status'] == 'waiting'):?>
                <button class="btn btn-sm btn-primary" data-update_id = "<?=$workOrder['wo_id']?>" id = "btn_wo_update">Update</button>
              <?php endif;?>

              <?php if($workOrder['status'] != 'waiting'):?>
                <button class="btn btn-primary" id = "print_workorder">Print</button>
              <?php endif;?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="delIt" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record ?</p>
                                    <input type="hidden" class="employeeid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="btn_del_itinerary_yes" style="float:right" class="btn btn-primary deleteAreaBtn">Delete</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/transactions/workorder_edit.js')?>"></script>
