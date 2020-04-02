<?php

 if(!isset($_SESSION['user_id'])) {
   header(base_url('Main/logout'));
 }

?>
<style>
  .modal{
    overflow-y: auto;
  }

  .no-events{
    pointer-events: none;
  }

  .nav-item{
    /* pointer-events: none; */
  }

  .fa-sticky-note-o{
    font-size: 100px;
    cursor:pointer;
    display: block;
  }

  .fa-plus-square{
    font-size: 100px;
    cursor:pointer;
    color: #72716f !important;
  }

  #empty_div{
    height: 50px;
    border: 1px dotted black;
  }

  .curr_template_icon_btn{
    font-size: 6px !important;
    width: 50px;
    display: inline-block;
    padding: 1px;
    padding: 7px !important;
    margin: 5px 0px;
  }

</style>
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('employees/Employee/index/'.$token);?>">Employees</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?=$emp['last_name']?>, <?=$emp['first_name']?> <?=$emp['middle_name']?></li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <!-- <input type="hidden" id = "pid" value = "<??>"> -->
    <section class="contract_wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">

              <div class="card-header">
                <div class="row">
                  <div class="col-12 text-right">
                    <?php if($contract_file->num_rows() > 0):?>
                      <button class="btn btn-primary" id = "btn_edit_contract">Edit Contract</button>
                    <?php endif;?>
                    <button class="btn btn-sm btn-primary" id = "btn_newContract" data-empid = "<?=$emp['id']?>">New Contract</button>
                  </div>
                </div>
              </div>
              <form id="edit_form">
              <div class="card-body">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#currentContract" style="color:black;">Current Contract</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#prevContract" style="color:black;" >Previous Contract</a>
                    </li>
                </ul>
                <div class="tab-content">
                  <!-- CURRENT CONTRACT -->
                  <?php $contract2 = $contract_file;?>
                  <div class="tab-pane fade show active" id="currentContract">
                    <?php if($contract_file->num_rows() > 0):?>
                      <?php $contract = $contract_file->row_array();?>
                      <!-- current contract details -->
                      <div class="container pt-3">
                        <h3 class = "mb-2">Contract Details</h3>
                        <div class="form-group row">
                          <div class="col-md-12">
                            <label for="Credentials" class="form-control-label col-form-label-sm">Credentials</label>
                            <div class="row">
                              <div class="col-md-4">
                                <input type="text" class="form-control" name = "current_emp_username" id = "current_emp_username" value = "<?=$contract['username']?>" readonly>
                                <!-- <input type="hidden" name = "emp_id" id = "emp_id" value = "<?=$emp['id']?>" > -->
                                <small class="form-text">Username <span class="asterisk"></span></small>
                              </div>
                              <div class="col-md-4">
                                <input type="password" class="form-control" name = "current_emp_password" id = "current_emp_password" value = "<?=$contract['password']?>" readonly>
                                <small class="form-text">Password <span class="asterisk"></span></small>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-md-12">
                            <label for="Position" class="form-control-label col-form-label-sm">Contract Details</label>
                            <input type="hidden" name = "edit_emp_id" id = "edit_emp_id" value = "<?=$emp['id']?>">
                            <?php if(count((array)$contract) > 0 ):?>
                              <input type="hidden" id = "edit_contract_id" name = "edit_contract_id" value = "<?=$contract_id?>">
                            <?php endif;?>
                            <div class="row">
                              <div class="col-md-4">
                                <select name="current_cWorkSite[]" id="current_cWorkSite" class = "form-control editable multi-select r4" multiple="multiple" disabled>
                                  <option value="1.1" <?=($contract['work_site_id'] == "1.1") ? "SELECTED" : ""?>>Anywhere</option>
                                  <?php if($workSite->num_rows() > 0):?>
                                    <?php foreach($workSite->result_array() as $w):?>
                                      <?php $cw = explode(',',$contract['work_site_id']);?>
                                      <?php foreach($cw as $c):?>
                                        <option value="<?=$w['worksiteid']?>" <?=($c == $w['worksiteid'])? "SELECTED" : ""?>><?=$w['description']?></option>
                                      <?php endforeach;?>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Work Site <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4">
                                <select name="current_cPos" id="current_cPos" class = "form-control editable r4" disabled>
                                  <option value="">------</option>
                                  <?php if($position->num_rows() > 0):?>
                                    <?php foreach($position->result_array() as $pos):?>
                                      <option value="<?=$pos['position_id']?>"
                                        data-deptId = "<?=$pos['deptId']?>"
                                        data-subDeptId = "<?=$pos['subDeptId']?>"
                                        data-pos_access_lvl = "<?=$pos['pos_access_lvl']?>"
                                        <?=($contract['position_id'] == $pos['position_id'])? "SELECTED" : ""?>
                                      >
                                        (<?=$pos['description']?>) - <?=$pos['position']?>
                                      </option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Position <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4">
                                <select name="current_contractStatus" id="current_contractStatus" class="form-control editable r4" disabled>
                                  <option value="">------</option>
                                  <?php if($emp_status->num_rows() > 0):?>
                                    <?php foreach($emp_status->result() as $stat):?>
                                      <option value="<?=$stat->empstatusid?>" <?=($stat->empstatusid == $contract['emp_status'])? "SELECTED": ""?>><?=$stat->description?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Employee Status <span class="asterisk"></span></small>
                              </div>

                              <!-- <div class="col-md-4">
                                <select name="current_cEmpLvl" id="current_cEmpLvl" class = "form-control" disabled>
                                  <option value="">------</option>
                                  <?php if($empLvl->num_rows() > 0):?>
                                    <?php foreach($empLvl->result_array() as $lvl):?>
                                      <option value="<?=$lvl['levelid']?>" <?=($contract['emp_lvl'] == $lvl['levelid'])? "SELECTED" : ""?>><?=$lvl['description']?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Employee Level <span class="asterisk"></span></small>
                              </div> -->
                            </div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-md-3">
                            <input type="date" class="form-control editable r4" name = "current_cStart" id = "current_cStart" placeholder = "yyyy-mm-dd" value = "<?=$contract['contract_start']?>" readonly>
                            <small class="form-text">Start Date <span class="asterisk"></span></small>
                          </div>
                          <div class="col-md-3">
                            <input type="date" class="form-control editable r4" name = "current_cEnd" id = "current_cEnd" placeholder="yyyy-mm-dd" value = "<?=$contract['contract_end']?>" readonly>
                            <small class="form-text">End Date <span class="asterisk"></span></small>
                          </div>
                          <div class="col-md-3">
                            <select name="current_company" id="current_company" class="form-control editable r4">
                              <option value="">------</option>
                              <?php if($companies->num_rows() > 0):?>
                                <?php foreach($companies->result_array() as $company):?>
                                  <option value="<?=$company['id']?>" <?=($company['id'] == $contract['company_id']) ? "SELECTED" : ""?>><?=$company['company']?></option>
                                <?php endforeach;?>
                              <?php endif;?>
                            </select>
                            <small class="form-text">Company <span class="asterisk"></span></small>
                          </div>
                          <div class="col-md-3">
                            <select name="current_contract_type" id="current_contract_type" class="form-control">
                              <option value="fixed" <?=($contract['contract_type'] == 'fixed') ? "SELECTED" : ""?>>Fixed</option>
                              <option value="open" <?=($contract['contract_type'] == 'open') ? "SELECTED" : ""?>>Open</option>
                            </select>
                          </div>
                        </div>

                        <!-- CURRENT CONTRACT FILES -->
                        <div class="form-group row no-events" id = "current_contract_file_wrapper">
                          <?php if($contract_files->num_rows() > 0):?>
                            <?php foreach($contract_files->result_array() as $file):?>
                              <div  class="col-md-2 text-center cf_wrapper">
                                <div id = "template_<?=$file['template_id']?>" class="img-thumbnail curr_template_icon template_<?=$file['template_id']?>" data-template_id = "<?=$file['template_id']?>" data-name = "<?=$file['template_name']?>">
                                  <i class="fa fa-sticky-note-o"></i>
                                  <small><?=$file['template_name']?></small>
                                  <textarea  class = "curr_templates" style = "display:none" name = "curr_templates"><?=$file['content']?></textarea>
                                  <input class = "curr_template_id" type="hidden" name = "template_id" value = "<?=$file['template_id']?>" />
                                </div>
                                <button type = "button" class="btn btn-primary curr_template_icon_btn btn_print">Print</button>
                                <button data-delid = "<?=$file['template_id']?>" data-template_name = "<?=$file['template_name']?>" type = "button" class="btn btn-danger btn_delete_template curr_template_icon_btn">Delete</button>
                              </div>
                            <?php endforeach;?>
                          <?php else:?>
                            <div class="col-md-2 text-center empty_cf_wrapper">
                              <div class="img-thumbnail">
                                <i class="fa fa-sticky-note-o"></i>
                                <small>No Available Contract File</small>
                              </div>
                            </div>
                          <?php endif;?>
                        </div>
                      </div>
                      <!-- current work schedule -->
                      <div class="container pt-3">
                        <h3 class = "mb-2">Work Schedule</h3>
                        <div class="form-group row">
                          <div class="col-md-4">
                            <label for="Type" class="form-control-label col-form-label-sm">Type</label>
                            <select name="edit_wSchedType" id="edit_wSchedType" class="form-control editable r4" disabled>
                              <option value="fix" <?=($contract['sched_type'] == "fix") ? "SELECTED" : ""?>>Fix Time</option>
                              <option value="flexi" <?=($contract['sched_type'] == "flexi") ? "SELECTED" : ""?>>Flexible Time</option>
                            </select>
                          </div>
                        </div>

                        <?php
                          $sched = json_decode($contract['work_sched']);
                          $mon = $sched->mon;
                          $tue = $sched->tue;
                          $wed = $sched->wed;
                          $thu = $sched->thu;
                          $fri = $sched->fri;
                          $sat = $sched->sat;
                          $sun = $sched->sun;
                        ?>
                        <div class="table-responsive mt-3">
                          <table class="table table-striped table-bordered text-center" style = "border-top: 1px solid gainsboro;width:100%;">
                            <thead>
                              <tr>
                                <th></th>
                                <th>Work Schedule</th>
                                <th>Break Schedule</th>
                                <th width = "100">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th>Mon</th>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "currrent_monTimeStart" readonly name = "current_timeStartMon" class="form-control offset-md-1 col-md-4 py-1 current_timeWorkStart current_initVal editable" value = "<?=$mon[0]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "currrent_monTimeEnd" readonly name = "current_timeEndMon" class="form-control col-md-4 py-1 current_timeWorkEnd current_initVal editable" value = "<?=$mon[1]?>">
                                  </div>
                                </td>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "currrent_monBreakStart" readonly name = "current_breakStartMon" class="form-control offset-md-1 col-md-4 py-1 current_breakStart current_initVal editable" value = "<?=$mon[3]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "currrent_monBreakEnd" readonly name = "current_breakEndMon" class="form-control col-md-4 py-1 current_breakEnd current_initVal editable" value = "<?=$mon[4]?>">
                                  </div>
                                </td>
                                <td>
                                  <input type="text" id = "currrent_monTimeTotal" readonly name = "current_timeTotalMon" class="form-control py-1 current_timeTotal col-md-4 offset-md-4 text-center" readonly value = "<?=$mon[2]?>">
                                </td>
                              </tr>
                              <tr>
                                <th>Tue</th>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "currrent_tueTimeStart"readonly  name = "current_timeStartTue" class="form-control offset-md-1 col-md-4 py-1 current_timeWorkStart editable" value = "<?=$tue[0]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "currrent_tueTimeEnd" readonly name = "current_timeEndTue" class="form-control col-md-4 py-1 current_timeWorkEnd editable" value = "<?=$tue[1]?>">
                                  </div>
                                </td>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_tueBreakStart" readonly name = "current_breakStartTue" class="form-control offset-md-1 col-md-4 py-1 current_breakStart editable" value = "<?=$tue[3]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_tueBreakEnd" readonly name = "current_breakEndTue" class="form-control col-md-4 py-1 current_breakEnd editable" value = "<?=$tue[4]?>">
                                  </div>
                                </td>
                                <td>
                                  <input type="text" id = "current_tueTimeTotal" readonly name = "current_timeTotalTue"class="form-control py-1 current_timeTotal col-md-4 offset-md-4 text-center" readonly value = "<?=$tue[2]?>">
                                </td>
                              </tr>
                              <tr>
                                <th>Wed</th>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_wedTimeStart" readonly name = "current_timeStartWed" class="form-control offset-md-1 col-md-4 py-1 current_timeWorkStart editable" value = "<?=$wed[0]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_wedTimeEnd" readonly name = "current_timeEndWed" class="form-control col-md-4 py-1 current_timeWorkEnd editable" value = "<?=$wed[1]?>">
                                  </div>
                                </td>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_wedBreakStart" readonly name = "current_breakStartWed" class="form-control offset-md-1 col-md-4 py-1 current_breakStart editable" value = "<?=$wed[3]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_wedBreakEnd" readonly name = "current_breakEndWed" class="form-control col-md-4 py-1 current_breakEnd editable" value = "<?=$wed[4]?>">
                                  </div>
                                </td>
                                <td>
                                  <input type="text" id = "current_wedTimeTotal" readonly name = "current_timeTotalWed" class="form-control py-1 current_timeTotal col-md-4 offset-md-4 text-center" readonly value = "<?=$wed[2]?>">
                                </td>
                              </tr>
                              <tr>
                                <th>Thu</th>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_thuTimeStart" readonly name = "current_timeStartThu" class="form-control offset-md-1 col-md-4 py-1 current_timeWorkStart editable" value = "<?=$thu[0]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_thuTimeEnd" readonly name = "current_timeEndThu" class="form-control col-md-4 py-1 current_timeWorkEnd editable" value = "<?=$thu[1]?>">
                                  </div>
                                </td>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_thuBreakStart" readonly name = "current_breakStartThu" class="form-control offset-md-1 col-md-4 py-1 current_breakStart editable" value = "<?=$thu[3]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_thuBreakEnd" readonly name = "current_breakEndThu" class="form-control col-md-4 py-1 current_breakEnd editable" value = "<?=$thu[4]?>">
                                  </div>
                                </td>
                                <td>
                                  <input type="text" id = "current_thuTimeTotal" readonly name = "current_timeTotalThu" class="form-control py-1 current_timeTotal col-md-4 offset-md-4 text-center" readonly value = "<?=$thu[2]?>">
                                </td>
                              </tr>
                              <tr>
                                <th>Fri</th>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_friTimeStart" readonly name = "current_timeStartFri" class="form-control offset-md-1 col-md-4 py-1 current_timeWorkStart editable" value = "<?=$fri[0]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_friTimeEnd" readonly name = "current_timeEndFri" class="form-control col-md-4 py-1 current_timeWorkEnd editable" value = "<?=$fri[1]?>">
                                  </div>
                                </td>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_friBreakStart" readonly name = "current_breakStartFri" class="form-control offset-md-1 col-md-4 py-1 current_breakStart editable" value = "<?=$fri[3]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_friBreakEnd" readonly name = "current_breakEndFri" class="form-control col-md-4 py-1 current_breakEnd editable" value = "<?=$fri[4]?>">
                                  </div>
                                </td>
                                <td>
                                  <input type="text" id = "current_friTimeTotal" readonly name = "current_timeTotalFri" class="form-control py-1 current_timeTotal col-md-4 offset-md-4 text-center" readonly value = "<?=$fri[2]?>">
                                </td>
                              </tr>
                              <tr>
                                <th>Sat</th>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_satTimeStart" readonly name = "current_timeStartSat" class="form-control offset-md-1 col-md-4 py-1 current_timeWorkStart editable" value = "<?=$sat[0]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_satTimeEnd" readonly name = "current_timeEndSat" class="form-control col-md-4 py-1 current_timeWorkEnd editable" value = "<?=$sat[1]?>">
                                  </div>
                                </td>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_satBreakStart" readonly name = "current_breakStartSat" class="form-control offset-md-1 col-md-4 py-1 current_breakStart editable" value = "<?=$sat[3]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_satBreakEnd" readonly name = "current_breakEndSat" class="form-control col-md-4 py-1 current_breakEnd editable" value = "<?=$sat[4]?>">
                                  </div>
                                </td>
                                <td>
                                  <input type="text" id = "current_satTimeTotal" readonly name= "current_timeTotalSat" class="form-control py-1 current_timeTotal col-md-4 offset-md-4 text-center" readonly value = "<?=$sat[2]?>">
                                </td>
                              </tr>
                              <tr>
                                <th>Sun</th>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_sunTimeStart" readonly name = "current_timeStartSun" class="form-control offset-md-1 col-md-4 py-1 current_timeWorkStart editable" value = "<?=$sun[0]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_sunTimeEnd" readonly name = "current_timeEndSun" class="form-control col-md-4 py-1 current_timeWorkEnd editable" value = "<?=$sun[1]?>">
                                  </div>
                                </td>
                                <td>
                                  <div class="row">
                                    <input type="time" id = "current_sunBreakStart" readonly name = "current_breakStartSun" class="form-control offset-md-1 col-md-4 py-1 current_breakStart editable" value = "<?=$sun[3]?>">
                                    <span class="col-md-2 text-center">:</span>
                                    <input type="time" id = "current_sunBreakEnd" readonly name = "current_breakEndSun" class="form-control col-md-4 py-1 current_breakEnd editable" value = "<?=$sun[4]?>">
                                  </div>
                                </td>
                                <td>
                                  <input type="text" id = "currrent_sunTimeTotal" readonly name = "current_timeTotalSun" class="form-control py-1 current_timeTotal col-md-4 offset-md-4 text-center" readonly value = "<?=$sun[2]?>">
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- current compensation schedule -->
                      <div class="container pt-3">
                        <div class="form-group row mb-5">
                          <!-- EDITABLE SALARY CATEGORY -->
                          <div class="col-md-12 mb-4">
                            <h3 class = "mb-3">Salary</h3>
                            <div class="row editable_salary" style = "display:none;">
                              <div class="col-md-4">
                                <select name="edit_compSalaryCat" id="edit_compSalaryCat" class="form-control select2 editable" data-desc = "">
                                  <option value="">------</option>
                                  <?php if($salary_cat->num_rows() > 0):?>
                                    <?php foreach($salary_cat->result_array() as $sal):?>
                                      <option value="<?=$sal['salarycatid']?>" data-desc = "<?=$sal['description']?>"><?=$sal['description']?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <input type="hidden" name = "edit_total_salary" id = "edit_total_salary" value = "<?=$contract['total_sal']?>">
                                <input type="hidden" name = "edit_total_sal_converted" id = "edit_total_sal_converted" value = "<?=$contract['total_sal_converted']?>">
                                <small class="form-text">Salary Category <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4">
                                <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                    <select name="edit_currency" id="edit_currency" data-ex_rate = "<?=$contract['ex_rate']?>" class="form-control" style = "height:40px;cursor:pointer">
                                      <?php if(count((array)$ex_rates) > 0):?>
                                        <?php foreach($ex_rates->result_array() as $rate):?>
                                          <option value="<?=$rate['currency_code']?>" <?=($contract['currency'] == $rate['currency_code']) ? "SELECTED" : ""?> data-rate = "<?=$rate['exchange_rate']?>"><?=$rate['currency_code']?></option>
                                        <?php endforeach;?>
                                      <?php else:?>
                                        <option value="PHP" data-rate = "1">PHP</option>
                                      <?php endif;?>
                                    </select>
                                  </div>
                                  <input type="text" name = "edit_compAmount" id = "edit_compAmount" class="form-control">
                                </div>
                                <small class="form-text">Amount <span class="asterisk"></span></small>
                                <!-- <input type="text" name = "edit_compAmount" id = "edit_compAmount" class="form-control editable">
                                <small class="form-text">Amount <span class="asterisk"></span></small> -->
                              </div>
                              <div class="col-md-2">
                                <input type = "button" class="btn btn-sm btn-primary" id = "edit_btnAddSalCat" value = "Add">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-8">
                            <div class="table-responsive">
                              <table class="table table-bordered table-striped" style = "width:100%;border-top:1px solid gainsboro;">
                                <thead class = "text-center">
                                  <th>Salary Category</th>
                                  <th>Amount</th>
                                </thead>
                                <tbody id = "edit_salary_ajax">
                                  <input id = "sal_arr" type="hidden" value = '<?=$contract['sal_cat']?>'>
                                  <?php
                                    $sal_cat = json_decode($contract['sal_cat']);
                                    $total = 0;
                                  ?>
                                  <?php foreach($sal_cat as $sal):?>
                                  <?php $total += (float)$sal->amount;?>
                                    <tr>
                                      <td>
                                        <?=$sal->desc?>
                                        <input type="hidden" class = "editable_sal_cat" value = "<?=$sal->id?>">
                                      </td>
                                      <td>
                                        <?php
                                          ### replace all number with x when position greater than 3 ###
                                          if($this->session->userdata('position_id') > 3){
                                            echo $contract['currency'].' '.replace(number_format($sal->amount,2));
                                          }else{
                                            echo $contract['currency'].' '.number_format($sal->amount,2);
                                          }
                                        ?>
                                      </td>
                                    </tr>
                                  <?php endforeach;?>
                                  <tr>
                                    <td>Total</td>
                                    <td id = "edit_total_sal">
                                      <?=($this->session->userdata('position_id') > 3)
                                          ? $contract['currency'].' '.replace(number_format($total,2))
                                          : $contract['currency'].' '.number_format($total,2)?>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>

                          <div class="col-md-2" style = "vertical-align:middle;" id = "reset_wrapper" >
                            <button type = "button" class="btn btn-info btn-sm" id = "edit_btn_reset_sal_tbl" style = "display:none;">Reset</button>
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-md-12">
                            <h3>Compensation</h3>
                            <div class="form-group row">
                              <div class="col-md-4 mb-2">
                                <label for="sss" class = "form-control-label col-form-label-sm">SSS</label>
                                <select name="edit_compSSS" id="edit_compSSS" class="form-control editable" disabled>
                                  <option value="0">------</option>
                                  <?php if($sss->num_rows() > 0):?>
                                    <?php foreach($sss->result_array() as $s):?>
                                      <option value="<?=$s['id']?>" <?=($contract['sssID'] == $s['id'])? "SELECTED" : ""?>>
                                        <!-- replace all number with x when position greater than 3 -->
                                        <?=($this->session->userdata('position_lvl') > 3)
                                        ? replace(number_format($s['salRange_From'], 2)."-".number_format($s['salRange_to'],2))
                                        : number_format($s['salRange_From'], 2)."-".number_format($s['salRange_to'],2) ?>
                                      </option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Range of Compensation <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4 mb-2">
                                <label for="Philhealth" class="form-control-label col-form-label-sm">Philhealth</label>
                                <select name="edit_compPhilhealth" id="edit_compPhilhealth" class="form-control editable" disabled>
                                  <option value="0">------</option>
                                  <?php if($philhealth->num_rows() > 0):?>
                                    <?php foreach($philhealth->result_array() as $ph):?>
                                      <option value="<?=$ph['phID']?>" <?=($contract['phID'] == $ph['phID'])? "SELECTED" : ""?>>
                                        <!-- replace all number with x when position greater than 3 -->
                                        <?=
                                          ($this->session->userdata('position_lvl') > 3)
                                          ? replace(number_format($ph['basic_mo_sal'], 2)." - ".number_format($ph['basic_mo_sal1'], 2))
                                          : number_format($ph['basic_mo_sal'], 2)." - ".number_format($ph['basic_mo_sal1'], 2);
                                        ?>
                                      </option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Basic Monthly Salary</small>
                              </div>

                              <div class="col-md-4 mb-2">
                                <label for="Pag Ibig" class="form-control-label col-form-label-sm">Pag Ibig</label>
                                <select name="edit_compPagIbig" id="edit_compPagIbig" class="form-control editable" disabled>
                                  <option value="0">------</option>
                                  <?php if($pagibig->num_rows() > 0):?>
                                    <?php foreach($pagibig->result_array() as $p):?>
                                      <option value="<?=$p['id']?>" <?=($contract['pagibigID'] == $p['id'])? "SELECTED" : ""?>>
                                        <?=
                                          ($this->session->userdata('position_lvl') > 3)
                                          ? replace($p['monthly_compensation'])
                                          : $p['monthly_compensation'];
                                        ?>
                                        <!-- <?=$p['monthly_compensation']?> -->
                                      </option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Monthly Compensation <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4 mb-2">
                                <label for="Tax" class="form-control-label col-form-label-sm">Tax</label>
                                <select name="edit_compTax" id="edit_compTax" class="form-control editable" disabled>
                                  <option value="0">------</option>
                                  <?php if($tax->num_rows() > 0):?>
                                    <?php foreach($tax->result_array() as $t):?>
                                      <option value="<?=$t['id']?>" <?=($contract['taxID'] == $t['id'])? "SELECTED" : ""?>>
                                        <?=
                                          ($this->session->userdata('position_lvl') > 3)
                                          ? replace(number_format($t['aibLowerLimit'],2)." - ".number_format($t['aibUpperLimit'], 2))
                                          : number_format($t['aibLowerLimit'],2)." - ".number_format($t['aibUpperLimit'], 2);
                                        ?>
                                      </option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Annual Income Bracket <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4 mb-2">
                                <label for="Tax" class="form-control-label col-form-label-sm">Pay Type</label>
                                <select name="edit_compPayType" id="edit_compPayType" class="form-control editable r4" disabled>
                                  <option value="">------</option>
                                  <?php if($paytype->num_rows() > 0):?>
                                    <?php foreach($paytype->result_array() as $p):?>
                                      <option value="<?=$p['paytypeid']?>" <?=($contract['paytypeID'] == $p['paytypeid'])? "SELECTED" : ""?>><?=$p['description']?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Description <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4 mb-2">
                                <label for="Tax" class="form-control-label col-form-label-sm">Pay Medium</label>
                                <select name="edit_comp_pay_medium" id="edit_comp_pay_medium" class="form-control editable r4">
                                  <option value="">------</option>
                                  <?php if($pay_medium->num_rows() > 0):?>
                                    <?php foreach($pay_medium->result_array() as $p):?>
                                      <option value="<?=$p['payoutmediumid']?>" <?=($p['payoutmediumid'] == $contract['payout_medium'])? "SELECTED": ""?>><?=$p['description']?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <small class="form-text">Description <span class="asterisk"></span></small>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-group row">
                          <div class="col-md-12">
                            <h3>Leave</h3>
                            <div class="row mb-4 editable_leave_wrapper" style = "display:none;">
                              <div class="col-md-4">
                                <select name="edit_leave_type" id="edit_leave_type" class = "form-control select2 editable" data-desc = "">
                                  <option value="">------</option>
                                  <?php if($emp_leave->num_rows() > 0):?>
                                    <?php foreach($emp_leave->result() as $leave):?>
                                      <option value="<?=$leave->leaveid?>" data-desc = "<?=$leave->description?>"><?=$leave->description?></option>
                                    <?php endforeach;?>
                                  <?php endif;?>
                                </select>
                                <input type="hidden" name = "edit_total_leave" id = "edit_total_leave" value = "<?=$contract['total_leave']?>">
                                <small class="form-text">Leave Category <span class="asterisk"></span></small>
                              </div>

                              <div class="col-md-4">
                                <input type="number" id = "edit_leave_num"  name = "edit_leave_num" class="form-control editable">
                                <small class="form-text">Number of days</small>
                              </div>
                              <div class="col-md-2">
                                <button type = "button" class="btn btn-sm btn-info" id = "edit_btn_add_leave">Add</button>
                              </div>
                            </div>

                            <div class="form-group row">
                              <div class="col-md-8">
                                <div class="table-responsive">
                                  <table class="table table-striped table-bordered" style = "border: 1px solid gainsboro;width:100%;">
                                    <thead>
                                      <th>Leave</th>
                                      <th>Days</th>
                                    </thead>
                                    <tbody id = "edit_leave_ajax">
                                      <input type="hidden" id = "leave_arr" value = '<?=$contract['emp_leave']?>'>
                                      <?php
                                      $leaves = json_decode($contract['emp_leave']);
                                      ?>
                                      <?php foreach($leaves as $sal):?>
                                        <tr>
                                          <td><?=$sal->desc?></td>
                                          <td><?=$sal->days?></td>
                                          <input type="hidden" class = "editable_leave" value = "<?=$sal->id?>">
                                        </tr>

                                      <?php endforeach;?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>

                              <div class="col-md-2">
                                <button type = "button" class="btn btn-sm btn-info" id = "edit_btn_reset_leave_tbl" style = "display:none">Reset</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif;?>

                    <?php if($contract_file->num_rows() == 0):?>
                      <div class="col-12 text-center" style = "padding-top:15%;padding-bottom:30%;">
                        <i class="fa fa-clone d-block mb-2" style = "font-size: 80px;"></i>
                        <h5>No current active contract</h5>
                      </div>
                    <?php endif;?>
                  </div>
                  <!-- PREVIOUS CONTRACT -->
                  <div class="tab-pane fade" id="prevContract">
                    <?php if($prevContract->num_rows() > 0):?>
                      <div class="form-group row mt-5">
                      <?php foreach($prevContract->result_array() as $prev):?>
                        <div class="prevContractForm col-md-2 text-center" style = "cursor:pointer;" data-previd = "<?=$prev['id']?>">
                          <i class="fa fa-clone d-block mb-2" style = "font-size: 80px;"></i>
                          <small class="form-text">Expired on <?=date('Y-m-d', strtotime($prev['updated_at']))?></small>
                        </div>
                      <?php endforeach;?>
                      </div>
                    <?php else:?>
                      <div class="col-12 text-center" style = "padding-top:15%;padding-bottom:30%;">
                        <i class="fa fa-clone d-block mb-2" style = "font-size: 80px;"></i>
                        <h5>No previous contract</h5>
                      </div>
                    <?php endif;?>
                  </div>
                </div>
              </div>

              <div id = "footer_wrapper" class="card-footer text-right" style = "display:none;">
                <button id = "btn_submit_editForm" type = "submit" class="btn btn-primary">Save</button>
              </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- New Contract Modal -->
    <div class="modal fade" id="newContract_modal">
      <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">New Contract</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link nav_new active" id = "nav-cDetails" href="#cDetails" style="color:black;">Contract Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav_new" id = "nav-wschedule" href = "#wSched" style="color:black;" >Work Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav_new" id = "nav-compensation" href = "#compensation" style="color:black;" >Compensation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav_new" id = "nav-leave" href = "#leave" style="color:black;" >Leave</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav_new" id = "nav-contract_file" href = "#contract_file" style="color:black;" >Contract File</a>
                </li>
            </ul>
            <form id="newContract_form">
              <div class="tab-content">
                <!-- CREDENTIALS SECTION -->
                <div class="tab-pane fade show active" id = "cDetails">
                  <div class="container pt-5">
                    <div class="form-group row credSection">
                      <div class="col-md-12">
                        <label for="Credentials" class="form-control-label col-form-label-sm">Credentials</label>
                        <div class="row">
                          <div class="col-md-4">
                            <input type="text" class="form-control" name = "emp_username" id = "emp_username" value = "">
                            <small class="form-text">Email <span class="asterisk"></span></small>
                          </div>
                          <!-- <div class="col-md-4">
                            <input type="password" class="form-control" name = "emp_password" id = "emp_password">
                            <small class="form-text">Password <span class="asterisk"></span></small>
                          </div> -->
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-md-12">
                        <label for="Position" class="form-control-label col-form-label-sm">Contract Details</label>
                        <input type="hidden" name = "emp_id" id = "emp_id" value = "<?=$emp['id']?>">
                        <?php if($contract_file->num_rows() > 0 ):?>
                          <input type="hidden" name = "contract_id" value = "<?=$contract_id?>">
                        <?php endif;?>
                        <div class="row">
                          <div class="col-md-4">
                            <select name="cWorkSite[]" id="cWorkSite" class = "form-control multi-select cdetails_req" multiple = "multiple">
                              <option value="1.1">Anywhere</option>
                              <?php if($workSite->num_rows() > 0):?>
                                <?php foreach($workSite->result_array() as $w):?>
                                  <option value="<?=$w['worksiteid']?>"><?=$w['description']?></option>
                                <?php endforeach;?>
                              <?php endif;?>
                            </select>
                            <small class="form-text">Work Site <span class="asterisk"></span></small>
                          </div>

                          <div class="col-md-4">
                            <select name="cPos" id="cPos" class = "form-control cdetails_req select2">
                              <option value="">------</option>
                              <?php if($position->num_rows() > 0):?>
                                <?php foreach($position->result_array() as $pos):?>
                                  <option
                                    data-deptId = "<?=$pos['deptId']?>"
                                    data-subDeptId = "<?=$pos['subDeptId']?>"
                                    data-pos_access_lvl = "<?=$pos['pos_access_lvl']?>"
                                    value="<?=$pos['position_id']?>">
                                  (<?=$pos['description']?>) - <?=$pos['position']?>
                                  </option>
                                <?php endforeach;?>
                              <?php endif;?>
                            </select>
                            <small class="form-text">Position <span class="asterisk"></span></small>
                          </div>

                          <div class="col-md-4">
                            <select name="contractStatus" id="contractStatus" class="form-control cdetails_req select2">
                              <option value="">------</option>
                              <?php if($emp_status->num_rows() > 0):?>
                                <?php foreach($emp_status->result() as $stat):?>
                                  <option value="<?=$stat->empstatusid?>"><?=$stat->description?></option>
                                <?php endforeach;?>
                              <?php endif;?>
                            </select>
                            <small class="form-text">Employment Status <span class="asterisk"></span></small>
                          </div>

                          <!-- <div class="col-md-4">
                            <select name="cEmpLvl" id="cEmpLvl" class = "form-control select2">
                              <option value="">------</option>
                              <?php if($empLvl->num_rows() > 0):?>
                                <?php foreach($empLvl->result_array() as $lvl):?>
                                  <option value="<?=$lvl['levelid']?>"><?=$lvl['description']?></option>
                                <?php endforeach;?>
                              <?php endif;?>
                            </select>
                            <small class="form-text">Employee Level <span class="asterisk"></span></small>
                          </div> -->
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-md-3">
                        <input type="text" class="form-control cdetails_req" name = "cStart" id = "cStart" placeholder = "yyyy-mm-dd">
                        <small class="form-text">Start Date <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-3">
                        <input type="text" class="form-control cdetails_req" name = "cEnd" id = "cEnd" placeholder="yyyy-mm-dd">
                        <small class="form-text">End Date <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-3">
                        <select name="new_company" id="new_company" class="form-control cdetails_req select2">
                          <option value="">------</option>
                          <?php if($companies->num_rows() > 0):?>
                            <?php foreach($companies->result_array() as $company):?>
                              <option value="<?=$company['id']?>"><?=$company['company']?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Company <span class="asterisk"></span></small>
                      </div>
                      <div class="col-md-3">
                        <select name="contract_type" id="contract_type" class="form-control cdetails_req">
                          <option value="fixed">Fixed</option>
                          <option value="open">Open</option>
                        </select>
                        <small class="form-text">Contract Type <span class="asterisk"></span></small>
                      </div>

                      <!-- <div class="col-md-12 mt-4"> -->
                        <!-- <label for="image-upload" id="image-label">Upload Contract File <span class="asterisk"></span></label> -->
                        <!-- <input type="file" name="contract_info" id="contract_info" class = "form-control"/> -->
                        <!-- <textarea name="contractDescription" id="contractDescription" cols="30" rows="10" class="form-control"></textarea>
                        <small class="form-text">Description <span class="asterisk"></span></small> -->
                      <!-- </div> -->
                    </div>
                  </div>
                </div>
                <!-- WORK SCHEDULE SECTION -->
                <div class="tab-pane fade" id = "wSched">
                  <div class="container pt-5">
                    <div class="form-group row">
                      <div class="col-md-1 text-right">
                        <label for="Type" class="form-control-label col-form-label-sm">Type</label>
                      </div>
                      <div class="col-md-4">
                        <select name="wSchedType" id="wSchedType" class="form-control">
                          <option value="fix">Fixed Time</option>
                          <option value="flexi">Flexible Time</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="col-md-1 text-right">
                        <label for="Schedule" class="form-control-label col-form-label-sm">Schedule</label>
                      </div>
                      <div class="col-md-4">
                        <select name="wSchedType2" id="wSchedType2" class="form-control">
                          <option value="specific">Specific</option>
                          <option value="default">Choose from default</option>
                        </select>
                      </div>
                      <div class="col-md-4 divWorkSChedDefault" style = "display:none;">
                        <select name="wSchedPos" id="wSchedPos" class="form-control">
                          <option value="">------</option>
                          <?php if($position->num_rows() > 0):?>
                            <?php foreach($position->result_array() as $pos):?>
                              <option value="<?=$pos['position_id']?>"><?=$pos['position']?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Position <span class="asterisk"></span></small>
                      </div>
                    </div>

                    <div class="table-responsive mt-3">
                      <table class="workSched_tbl table table-striped table-bordered text-center" style = "border-top: 1px solid gainsboro;width:100%;">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Work Schedule</th>
                            <th>Break Schedule</th>
                            <th width = "100">Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <th>Mon</th>
                            <td>
                              <div class="row">
                                <input type="time" id = "monTimeStart" name = "timeStartMon" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart initVal">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "monTimeEnd" name = "timeEndMon" class="form-control col-md-4 py-1 timeWorkEnd initVal">
                              </div>
                            </td>
                            <td>
                              <div class="row">
                                <input type="time" id = "monBreakStart" name = "breakStartMon" class="form-control offset-md-1 col-md-4 py-1 breakStart initVal">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "monBreakEnd" name = "breakEndMon" class="form-control col-md-4 py-1 breakEnd initVal">
                              </div>
                            </td>
                            <td>
                              <input type="text" id = "monTimeTotal" name = "timeTotalMon" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                            </td>
                          </tr>
                          <tr>
                            <th>Tue</th>
                            <td>
                              <div class="row">
                                <input type="time" id = "tueTimeStart" name = "timeStartTue" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "tueTimeEnd" name = "timeEndTue" class="form-control col-md-4 py-1 timeWorkEnd">
                              </div>
                            </td>
                            <td>
                              <div class="row">
                                <input type="time" id = "tueBreakStart" name = "breakStartTue" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "tueBreakEnd" name = "breakEndTue" class="form-control col-md-4 py-1 breakEnd">
                              </div>
                            </td>
                            <td>
                              <input type="text" id = "tueTimeTotal" name = "timeTotalTue"class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                            </td>
                          </tr>
                          <tr>
                            <th>Wed</th>
                            <td>
                              <div class="row">
                                <input type="time" id = "wedTimeStart" name = "timeStartWed" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "wedTimeEnd" name = "timeEndWed" class="form-control col-md-4 py-1 timeWorkEnd">
                              </div>
                            </td>
                            <td>
                              <div class="row">
                                <input type="time" id = "wedBreakStart" name = "breakStartWed" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "wedBreakEnd" name = "breakEndWed" class="form-control col-md-4 py-1 timeWorkEnd">
                              </div>
                            </td>
                            <td>
                              <input type="text" id = "wedTimeTotal" name = "timeTotalWed" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                            </td>
                          </tr>
                          <tr>
                            <th>Thu</th>
                            <td>
                              <div class="row">
                                <input type="time" id = "thuTimeStart" name = "timeStartThu" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "thuTimeEnd" name = "timeEndThu" class="form-control col-md-4 py-1 breakEnd">
                              </div>
                            </td>
                            <td>
                              <div class="row">
                                <input type="time" id = "thuBreakStart" name = "breakStartThu" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "thuBreakEnd" name = "breakEndThu" class="form-control col-md-4 py-1 breakEnd">
                              </div>
                            </td>
                            <td>
                              <input type="text" id = "thuTimeTotal" name = "timeTotalThu" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                            </td>
                          </tr>
                          <tr>
                            <th>Fri</th>
                            <td>
                              <div class="row">
                                <input type="time" id = "friTimeStart" name = "timeStartFri" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "friTimeEnd" name = "timeEndFri" class="form-control col-md-4 py-1 timeWorkEnd">
                              </div>
                            </td>
                            <td>
                              <div class="row">
                                <input type="time" id = "friBreakStart" name = "breakStartFri" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "friBreakEnd" name = "breakEndFri" class="form-control col-md-4 py-1 breakEnd">
                              </div>
                            </td>
                            <td>
                              <input type="text" id = "friTimeTotal" name = "timeTotalFri" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                            </td>
                          </tr>
                          <tr>
                            <th>Sat</th>
                            <td>
                              <div class="row">
                                <input type="time" id = "satTimeStart" name = "timeStartSat" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "satTimeEnd" name = "timeEndSat" class="form-control col-md-4 py-1 timeWorkEnd">
                              </div>
                            </td>
                            <td>
                              <div class="row">
                                <input type="time" id = "satBreakStart" name = "breakStartSat" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "satBreakEnd" name = "breakEndSat" class="form-control col-md-4 py-1 breakEnd">
                              </div>
                            </td>
                            <td>
                              <input type="text" id = "satTimeTotal" name= "timeTotalSat" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                            </td>
                          </tr>
                          <tr>
                            <th>Sun</th>
                            <td>
                              <div class="row">
                                <input type="time" id = "sunTimeStart" name = "timeStartSun" class="form-control offset-md-1 col-md-4 py-1 timeWorkStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "sunTimeEnd" name = "timeEndSun" class="form-control col-md-4 py-1 timeWorkEnd">
                              </div>
                            </td>
                            <td>
                              <div class="row">
                                <input type="time" id = "sunBreakStart" name = "breakStartSun" class="form-control offset-md-1 col-md-4 py-1 breakStart">
                                <span class="col-md-2 text-center">:</span>
                                <input type="time" id = "sunBreakEnd" name = "breakEndSun" class="form-control col-md-4 py-1 breakEnd">
                              </div>
                            </td>
                            <td>
                              <input type="text" id = "sunTimeTotal" name = "timeTotalSun" class="form-control py-1 timeTotal col-md-4 offset-md-4 text-center" readonly>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- COMPENSATION SCHEDULE -->
                <div class="tab-pane fade" id = "compensation">
                  <div class="container pt-5">

                    <!-- compensation -->
                    <div class="form-group row mb-5">
                      <div class="col-12">
                        <h4>Compensation Schedule</h4>
                      </div>
                      <!-- SSS -->
                      <div class="col-md-4 mb-2">
                        <label for="sss" class = "form-control-label col-form-label-sm">SSS</label>
                        <select name="compSSS" id="compSSS" class="form-control comp_req">
                          <option value="0">------</option>
                          <?php if($sss->num_rows() > 0):?>
                            <?php foreach($sss->result_array() as $s):?>
                              <option value="<?=$s['id']?>"><?=number_format($s['salRange_From'], 2)?> - <?=number_format($s['salRange_to'],2)?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Range of Compensation <span class="asterisk"></span></small>
                      </div>
                      <!-- PHILHEALTH -->
                      <div class="col-md-4 mb-2">
                        <label for="Philhealth" class="form-control-label col-form-label-sm">Philhealth</label>
                        <select name="compPhilhealth" id="compPhilhealth" class="form-control comp_req">
                          <option value="0">------</option>
                          <?php if($philhealth->num_rows() > 0):?>
                            <?php foreach($philhealth->result_array() as $ph):?>
                              <option value="<?=$ph['phID']?>"><?=number_format($ph['basic_mo_sal'], 2)?> - <?=number_format($ph['basic_mo_sal1'], 2)?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Monthly Salary</small>
                      </div>
                      <!-- PAGIBIG -->
                      <div class="col-md-4 mb-2">
                        <label for="Pag Ibig" class="form-control-label col-form-label-sm">Pag Ibig</label>
                        <select name="compPagIbig" id="compPagIbig" class="form-control comp_req">
                          <option value="0">------</option>
                          <?php if($pagibig->num_rows() > 0):?>
                            <?php foreach($pagibig->result_array() as $p):?>
                              <option value="<?=$p['id']?>"><?=$p['monthly_compensation']?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Monthly Compensation <span class="asterisk"></span></small>
                      </div>
                      <!-- TAX -->
                      <div class="col-md-4 mb-2">
                        <label for="Tax" class="form-control-label col-form-label-sm">Tax</label>
                        <select name="compTax" id="compTax" class="form-control comp_req">
                          <option value="0">------</option>
                          <?php if($tax->num_rows() > 0):?>
                            <?php foreach($tax->result_array() as $t):?>
                              <option value="<?=$t['id']?>"><?=number_format($t['aibLowerLimit'],2)?> - <?=number_format($t['aibUpperLimit'], 2)?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Annual Income Bracket <span class="asterisk"></span></small>
                      </div>
                      <!-- PAYTYPE -->
                      <div class="col-md-4 mb-2">
                        <label for="Tax" class="form-control-label col-form-label-sm">Pay Type</label>
                        <select name="compPayType" id="compPayType" class="form-control comp_req" data-mpfreq = "">
                          <option value="">------</option>
                          <?php if($paytype->num_rows() > 0):?>
                            <?php foreach($paytype->result_array() as $p):?>
                              <option value="<?=$p['paytypeid']?>" data-pfreq = "<?=$p['frequency']?>"><?=$p['description']?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Description <span class="asterisk"></span></small>
                      </div>
                      <!-- PAYOUT MEDIUM -->
                      <div class="col-md-4 mb-2">
                        <label for="Tax" class="form-control-label col-form-label-sm">Pay Medium</label>
                        <select name="comp_pay_medium" id="comp_pay_medium" class="form-control comp_req select2">
                          <option value="">------</option>
                          <?php if($pay_medium->num_rows() > 0):?>
                            <?php foreach($pay_medium->result_array() as $p):?>
                              <option value="<?=$p['payoutmediumid']?>"><?=$p['description']?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                        <small class="form-text">Description <span class="asterisk"></span></small>
                      </div>
                    </div>
                      <!-- SALARY -->
                    <div class="form-group row mb-5">
                      <div class="col-md-12 mb-4">
                        <h4>Salary <span class="pt_text_info text-danger"></span></h4>
                        <div class="row">
                          <div class="col-md-4">
                            <select name="compSalaryCat" id="compSalaryCat" class="form-control select2" data-desc = "">
                              <option value="">------</option>
                              <?php if($salary_cat->num_rows() > 0):?>
                                <?php foreach($salary_cat->result_array() as $sal):?>
                                  <option value="<?=$sal['salarycatid']?>" data-desc = "<?=$sal['description']?>"><?=$sal['description']?></option>
                                <?php endforeach;?>
                              <?php endif;?>
                            </select>
                            <input type="hidden" name = "total_sal" id = "total_salary" value = "">
                            <input type="hidden" name = "total_sal_converted" id = "total_sal_converted" value = "">
                            <small class="form-text">Salary Category <span class="asterisk"></span></small>
                          </div>

                          <div class="col-md-4">
                            <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                <?php
                                  if($contract2->num_rows() > 0){
                                    $ex_rate = $contract2->row()->ex_rate;
                                    $currency = $contract2->row()->currency;
                                  }else{
                                    $ex_rate = 1;
                                    $currency = "PHP";
                                  }
                                ?>
                                <select name="currency" id="currency" data-ex_rate = "<?=$ex_rate?>" class="form-control" style = "height:40px;cursor:pointer">
                                  <?php if($ex_rates->num_rows() > 0):?>
                                    <?php foreach($ex_rates->result_array() as $rate):?>
                                      <option value="<?=$rate['currency_code']?>" <?=($currency == $rate['currency_code']) ? "SELECTED" : ""?> data-rate = "<?=$rate['exchange_rate']?>"><?=$rate['currency_code']?></option>
                                    <?php endforeach;?>
                                  <?php else:?>
                                    <option value="PHP" data-ex_rate = "1">PHP</option>
                                  <?php endif;?>
                                </select>
                              </div>
                              <input type="text" name = "compAmount" id = "compAmount" class="form-control money-input" data-raw = "">
                            </div>
                            <small class="form-text">Amount <span class="asterisk"></span></small>
                          </div>
                          <div class="col-md-2">
                            <input type = "button" class="btn btn-sm btn-primary" id = "btnAddSalCat" value = "Add">
                          </div>
                        </div>
                      </div>

                      <div class="col-md-8">
                        <div class="table-responsive">
                          <table class="table table-bordered" style = "width:100%;border-top:1px solid gainsboro;">
                            <thead>
                              <th>Salary Category</th>
                              <th>Amount</th>
                            </thead>
                            <tbody id = "salary_ajax">
                              <td>Total</td>
                              <td id = "total_sal">0</td>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-md-2" style = "vertical-align:middle;">
                        <button type = "button" class="btn btn-info btn-sm" id = "btn_reset_sal_tbl">Reset</button>
                      </div>

                      <div class="col-md-12 mb-3">
                        <div id = "divSalCat" class="form-group row">
                          <input type="hidden" class = "sal" name = "basic_pay" id = "basic_pay">
                          <input type="hidden" class = "sal" name = "transpo_pay" id = "transpo_pay">
                          <input type="hidden" class = "sal" name = "commu_pay" id = "commu_pay">
                          <input type="hidden" class = "sal" name = "etc_pay" id = "etc_pay">
                          <!-- salary category goes here -->
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <!-- LEAVE SECTION -->
                <div class="tab-pane fade" id="leave">
                  <div class="container pt-5">
                    <div class="form-group row">
                      <div class="col-md-12">
                        <h4>Leave</h4>
                        <div class="row">
                          <div class="col-md-4">
                            <select name="leave_type" id="leave_type" class = "form-control select2" data-desc = "">
                              <option value="">------</option>
                              <?php if($emp_leave->num_rows() > 0):?>
                                <?php foreach($emp_leave->result() as $leave):?>
                                  <option value="<?=$leave->leaveid?>" data-desc = "<?=$leave->description?>"><?=$leave->description?></option>
                                <?php endforeach;?>
                              <?php endif;?>
                            </select>
                            <input type="hidden" name = "total_leave" id = "total_leave" value = "0">
                            <small class="form-text">Leave Category <span class="asterisk"></span></small>
                          </div>

                          <div class="col-md-4">
                            <input type="text" id = "leave_num"  name = "leave_num" class="form-control number-input-2">
                            <small class="form-text">Number of days</small>
                          </div>
                          <div class="col-md-2">
                            <button type = "button" class="btn btn-sm btn-info" id = "btn_add_leave">Add</button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group row">
                      <div class="col-md-8">
                        <div class="table-responsive">
                          <table class="table table-striped table-bordered" style = "border: 1px solid gainsboro;">
                            <thead>
                              <th>Leave</th>
                              <th>Days</th>
                            </thead>
                            <tbody id = "leave_ajax">
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-md-2">
                        <button type = "button" class="btn btn-sm btn-info" id = "btn_reset_leave_tbl">Reset</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- CONTRACT FILE -->
                <div class="tab-pane fade" id = "contract_file">
                  <div class="form-group row" id = "template_ajax">
                    <div class="col-md-10 offset-md-1">
                      <div class="row">

                        <div class="col-12 mt-4">
                          <div class="row" id = "template_icon_wrapper">
                            <div class="col-md-2 text-center">
                              <i class="fa fa-plus-square" id = "btn_template_modal"></i>
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                          <hr>
                        </div>
                        <div class="col-12 text-right">
                          <button type = "button" class="btn btn-primary" id = "btn_reset" style = "display:none;">Close</button>
                        </div>
                        <div class="col-md-12" id = "template_container">
                          <div id="template_wrapper" class = "container pt-4">

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn blue-grey" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" id = "btn_back" style = "display:none;">Back</button>
            <button class="btn btn-primary" id = "btn_next">Next</button>
            <button class="btn btn-primary" id = "btn_finish" style = "display:none;">Finish</button>
            <!-- <button id = "btn_submit_newContractForm" class="btn btn-sm btn-primary">Save</button> -->
          </div>

        </div>
      </div>
    </div>
    <!-- previous contract modal -->
    <div class="modal fade" id="prevContract_modal">
      <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Previous Contract</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <!-- prev contract details -->
            <div class="container pt-3">
              <h3 class = "mb-2">Contract Details</h3>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="Position" class="form-control-label col-form-label-sm">Contract Details</label>
                  <div class="row">
                    <div class="col-md-4">
                      <input name="current_cWorkSite" id="prev_cWorkSite" class = "form-control" readonly>
                      <small class="form-text">Work Site <span class="asterisk"></span></small>
                    </div>

                    <div class="col-md-4">
                      <input name="prev_cPos" id="prev_cPos" class = "form-control" readonly>
                      <small class="form-text">Position <span class="asterisk"></span></small>
                    </div>

                    <div class="col-md-4">
                      <input name="prev_contractStatus" id="prev_contractStatus" class="form-control" readonly>
                      <small class="form-text">Employee Status <span class="asterisk"></span></small>
                    </div>

                    <!-- <div class="col-md-4">
                      <input name="prev_cEmpLvl" id="prev_cEmpLvl" class = "form-control" readonly>
                      <small class="form-text">Employee Level <span class="asterisk"></span></small>
                    </div> -->
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-3">
                  <input type="text" class="form-control" name = "prev_cStart" id = "prev_cStart" placeholder = "yyyy-mm-dd"  readonly>
                  <small class="form-text">Start Date <span class="asterisk"></span></small>
                </div>
                <div class="col-md-3">
                  <input type="text" class="form-control" name = "prev_cEnd" id = "prev_cEnd" placeholder="yyyy-mm-dd" readonly>
                  <small class="form-text">End Date <span class="asterisk"></span></small>
                </div>
                <div class="col-md-3">
                  <select name="prev_company" id="prev_company" class="form-control">
                    <option value="">------</option>
                    <?php if($companies->num_rows() > 0):?>
                      <?php foreach($companies->result_array() as $company):?>
                        <option value="<?=$company['id']?>"><?=$company['company']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                  <small class="form-text">Company <span class="asterisk"></span></small>
                </div>
                <div class="col-md-3">
                  <select name="prev_contract_type" id="prev_contract_type" class="form-control">
                    <option value="fixed">Fixed</option>
                    <option value="open">Open</option>
                  </select>
                  <small class="form-text">Contract Type <span class="asterisk"></span></small>
                </div>

                <div class="col-md-12 mt-4" id = "prev_contractDescription">
                  <!-- <textarea name="prev_contractDescription" id="prev_contractDescription" cols="30" rows="10" class="form-control" disabled>
                  </textarea>
                  <small class="form-text">Description <span class="asterisk"></span></small> -->
                </div>
              </div>
            </div>
            <!-- prev work schedule -->
            <div class="container pt-3">
              <h3 class = "mb-2">Work Schedule</h3>
              <div class="form-group row">
                <div class="col-md-4">
                  <label for="Type" class="form-control-label col-form-label-sm">Type</label>
                  <select name="" id="sched_type" class="form-control">
                    <option value="fix">Fixed Time</option>
                    <option value="flexi">Flexible Time</option>
                  </select>
                </div>
              </div>
              <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered text-center" style = "border-top: 1px solid gainsboro;width:100%;">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Work Schedule</th>
                      <th>Break Schedule</th>
                      <th width = "100">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th>Mon</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_monTimeStart" readonly name = "prev_timeStartMon" class="form-control offset-md-1 col-md-4 py-1 prev_timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_monTimeEnd" readonly name = "prev_timeEndMon" class="form-control col-md-4 py-1 prev_timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_monBreakStart" readonly name = "prev_breakStartMon" class="form-control offset-md-1 col-md-4 py-1 prev_breakStart" >
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_monBreakEnd" readonly name = "prev_breakEndMon" class="form-control col-md-4 py-1 prev_breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_monTimeTotal" readonly name = "prev_timeTotalMon" class="form-control py-1 prev_timeTotal col-md-4 offset-md-4 text-center" readonly >
                      </td>
                    </tr>
                    <tr>
                      <th>Tue</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_tueTimeStart"readonly  name = "prev_timeStartTue" class="form-control offset-md-1 col-md-4 py-1 prev_timeWorkStart" >
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_tueTimeEnd" readonly name = "prev_timeEndTue" class="form-control col-md-4 py-1 prev_timeWorkEnd" >
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_tueBreakStart" readonly name = "prev_breakStartTue" class="form-control offset-md-1 col-md-4 py-1 prev_breakStart" >
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_tueBreakEnd" readonly name = "prev_breakEndTue" class="form-control col-md-4 py-1 prev_breakEnd" >
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_tueTimeTotal" readonly name = "prev_timeTotalTue"class="form-control py-1 prev_timeTotal col-md-4 offset-md-4 text-center" readonly >
                      </td>
                    </tr>
                    <tr>
                      <th>Wed</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_wedTimeStart" readonly name = "prev_timeStartWed" class="form-control offset-md-1 col-md-4 py-1 prev_timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_wedTimeEnd" readonly name = "prev_timeEndWed" class="form-control col-md-4 py-1 prev_timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_wedBreakStart" readonly name = "prev_breakStartWed" class="form-control offset-md-1 col-md-4 py-1 prev_breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_wedBreakEnd" readonly name = "prev_breakEndWed" class="form-control col-md-4 py-1 prev_breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_wedTimeTotal" readonly name = "prev_timeTotalWed" class="form-control py-1 prev_timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Thu</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_thuTimeStart" readonly name = "prev_timeStartThu" class="form-control offset-md-1 col-md-4 py-1 prev_timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_thuTimeEnd" readonly name = "prev_timeEndThu" class="form-control col-md-4 py-1 prev_timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_thuBreakStart" readonly name = "prev_breakStartThu" class="form-control offset-md-1 col-md-4 py-1 prev_breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_thuBreakEnd" readonly name = "prev_breakEndThu" class="form-control col-md-4 py-1 prev_breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_thuTimeTotal" readonly name = "prev_timeTotalThu" class="form-control py-1 prev_timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Fri</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_friTimeStart" readonly name = "prev_timeStartFri" class="form-control offset-md-1 col-md-4 py-1 prev_timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_friTimeEnd" readonly name = "prev_timeEndFri" class="form-control col-md-4 py-1 prev_timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_friBreakStart" readonly name = "prev_breakStartFri" class="form-control offset-md-1 col-md-4 py-1 prev_breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_friBreakEnd" readonly name = "prev_breakEndFri" class="form-control col-md-4 py-1 prev_breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_friTimeTotal" readonly name = "prev_timeTotalFri" class="form-control py-1 prev_timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Sat</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_satTimeStart" readonly name = "prev_timeStartSat" class="form-control offset-md-1 col-md-4 py-1 prev_timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_satTimeEnd" readonly name = "prev_timeEndSat" class="form-control col-md-4 py-1 prev_timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_satBreakStart" readonly name = "prev_breakStartSat" class="form-control offset-md-1 col-md-4 py-1 prev_breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_satBreakEnd" readonly name = "prev_breakEndSat" class="form-control col-md-4 py-1 prev_breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_satTimeTotal" readonly name= "timeTotalSat" class="form-control py-1 prev_timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                    <tr>
                      <th>Sun</th>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_sunTimeStart" readonly name = "prev_timeStartSun" class="form-control offset-md-1 col-md-4 py-1 prev_timeWorkStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_sunTimeEnd" readonly name = "prev_timeEndSun" class="form-control col-md-4 py-1 prev_timeWorkEnd">
                        </div>
                      </td>
                      <td>
                        <div class="row">
                          <input type="time" id = "prev_sunBreakStart" readonly name = "prev_breakStartSun" class="form-control offset-md-1 col-md-4 py-1 prev_breakStart">
                          <span class="col-md-2 text-center">:</span>
                          <input type="time" id = "prev_sunBreakEnd" readonly name = "prev_breakEndSun" class="form-control col-md-4 py-1 prev_breakEnd">
                        </div>
                      </td>
                      <td>
                        <input type="text" id = "prev_sunTimeTotal" readonly name = "prev_timeTotalSun" class="form-control py-1 prev_timeTotal col-md-4 offset-md-4 text-center" readonly>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- prev compensation schedule -->
            <div class="container pt-3">
              <div class="form-group row">
                <div class="col-md-8">
                  <h3 class = "mb-3">Salary</h3>
                  <div class="table-responsive">
                    <table class="table table-bordered table-bordered table-striped" style = "width:100%;border-top:1px solid gainsboro;">
                      <thead class="text-center">
                        <th>Salary Category</th>
                        <th>Amount</th>
                      </thead>
                      <tbody id = "prev_sal_cat_ajax">

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <h3>Compesation</h3>
              <div class="form-group row">
                <div class="col-md-4 mb-2">
                  <label for="sss" class = "form-control-label col-form-label-sm">SSS</label>
                  <input name="compSSS" id="prev_compSSS" class="form-control" readonly>
                  <small class="form-text">Range of Compensation <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Philhealth" class="form-control-label col-form-label-sm">Philhealth</label>
                  <input name="compPhilhealth" id="prev_compPhilhealth" class="form-control" readonly>
                  <small class="form-text">Basic Monthly Salary</small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Pag Ibig" class="form-control-label col-form-label-sm">Pag Ibig</label>
                  <input name="compPagIbig" id="prev_compPagIbig" class="form-control" readonly>
                  <small class="form-text">Monthly Compensation <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Tax" class="form-control-label col-form-label-sm">Tax</label>
                  <input name="compTax" id="prev_compTax" class="form-control" readonly>
                  <small class="form-text">Annual Income Bracket <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Tax" class="form-control-label col-form-label-sm">Pay Type</label>
                  <input name="compPayType" id="prev_compPayType" class="form-control" readonly>
                  <small class="form-text">Description <span class="asterisk"></span></small>
                </div>

                <div class="col-md-4 mb-2">
                  <label for="Tax" class="form-control-label col-form-label-sm">Payout Medium</label>
                  <input name="compPayType" id="prev_pay_medium" class="form-control" readonly>
                  <small class="form-text">Description <span class="asterisk"></span></small>
                </div>
              </div>

            </div>
            <!-- pre leave -->
            <div class="container pt-3">
              <div class="form-group row">
                <div class="col-12">
                  <h3>Leave</h3>
                  <div class="form-group row">
                    <div class="col-md-8">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered table-striped" style = "border: 1px solid gainsboro;width:100%;">
                          <thead>
                            <th>Leave</th>
                            <th>Days</th>
                          </thead>
                          <tbody id = "prev_leave_tbl_ajax">

                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn blue-grey" data-dismiss="modal">Close</button>
          </div>

        </div>
      </div>
    </div>
    <!-- Confirmation Modal -->
    <div id="confirmModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Confirmation</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="row">
                            <div class="col-lg-12">
                                <p>Do you want to set all schedule like this ?</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" style="float:right" class="btn btn-primary yesBtn">Yes</button>
                            <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey noBtn" data-dismiss="modal" aria-label="Close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- new active contract modal   -->
    <div id="confirmModal2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Reminder</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_area-form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>This employee still has an active contract. Do you really want to create new one ?</p>
                                    <input type="hidden" class="employeeid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="btnYes" style="float:right" class="btn btn-primary deleteAreaBtn">Yes</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">No</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--new contract create template modal -->
    <div class="modal fade" id = "template_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create Template</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-12">
                <label for="Select Template" class="form-control-label col-form-label-sm">Select Template</label>
                <select name="template" id="template" class="form-control" data-text = "">
                  <option value="">------</option>
                  <?php if($templates->num_rows() > 0):?>
                    <?php foreach($templates->result_array() as $template):?>
                      <option value="<?=$template['id']?>"><?=$template['template_name']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_create">Create</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- current contract create template modal -->
    <div class="modal fade" id = "curr_template_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create Template</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id =  "curr_template_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-12">
                  <label for="Select Template" class="form-control-label col-form-label-sm">Select Template</label>
                  <select name="curr_template" id="curr_template" class="form-control curr_tr" data-text = "">
                    <option value="">------</option>
                    <?php if($templates->num_rows() > 0):?>
                      <?php foreach($templates->result_array() as $template):?>
                        <option value="<?=$template['id']?>"><?=$template['template_name']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                  <input type="hidden" id = "template_contract_id" name = "template_contract_id" value = "<?=$contract_id?>" class = "curr_tr">
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" class="btn btn-sm btn-primary" id = "btn_curr_create">Create</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- view current contract file modal -->
    <div class="modal fade" id = "curr_contract_file_modal">
      <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-10 offset-md-1">
                <div class="row">
                  <div class="col-12 mt-4" id = "curr_contract_file_container">

                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- delete contract files modal -->
    <div class="modal fade" id = "delete_contract_file_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Delete Contract File</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-12">
              <h4>Are you sure you want to delete this contract file (<span id="del_txt_cf"></span>)?</h4>
              <input type="hidden" name = "del_cf_id" id = "del_cf_id">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button id = "btn_del_cf" class="btn btn-sm btn-primary">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script src = "https://printjs-4de6.kxcdn.com/print.min.js"></script>
<script src = "<?=base_url('assets\js\signature-pad\signature_pad.umd.js')?>"></script>
<script src = "<?=base_url('assets\js\contracts\timer.js')?>"></script>
<script src = "<?=base_url('assets/js/employees/contracts/contract.js')?>"></script>
