<?php

 if(!isset($_SESSION['user_id'])) {
   header(base_url('Main/logout'));
 }

?>
<link rel="stylesheet" href="<?=base_url('assets\summernote\summernote.min.2.css')?>">
<link rel="stylesheet" href="<?=base_url('assets/css/custom_checkbox.css')?>">
<style>
  .checkmark{
    margin-top: 50px;
    margin-left: 30px;
  }

  .container_label{
    pointer-events: none;
  }

  .btn-default{
    color: #333 !important;
    background-color: #fff !important;
    border-color: #ccc !important;
  }

  .btn-default.btn-default.dropdown-toggle{
    color: #333 !important;
    background-color: #fff !important;
    border-color: #ccc !important;
  }

  .panel-heading{
    background-color: #f7f7f7;
  }

  .note-editor{
    min-height: 350px !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/employees_home/'.$token);?>">Entity</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('applicants/Applicant/index/'.$token);?>">Applicant</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">View Applicant</li>
        </ol>
    </div>
    <input type="hidden" id="appId" value="<?= $appId ?>">
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">

                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#employeeDetails" style="color:black;">Applicant Record</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#employeeEducation" style="color:black;" >Education</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#workHistory" style="color:black;">Work History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#dependents" style="color:black;">Dependents</a>
                        </li>
                        <!-- INTERVIEW -->
                        <?php if($applicant->app_status != "in_process"):?>
                          <li class="nav-item">
                              <a class="nav-link" data-toggle="tab" href="#interview" style="color:black;">
                                Interview
                                <span class="badge badge-danger reqError" style = "border-radius:50%;display:none;">
                                  <i class="fa fa-exclamation-circle"></i>
                                </span>
                              </a>
                          </li>
                        <?php endif;?>
                        <!-- JOB OFFER -->
                        <?php if($applicant->app_status == "job_offer" || $applicant->app_status == "reject_joboffer" || $applicant->app_status == "requirements"):?>
                          <li class="nav-item">
                              <a class="nav-link" data-toggle="tab" href="#job_offer" style="color:black;">
                                Job Offer
                                <span class="badge badge-danger reqError" style = "border-radius:50%;display:none;">
                                  <i class="fa fa-exclamation-circle"></i>
                                </span>
                              </a>
                          </li>
                        <?php endif;?>
                        <!-- REQUIREMENTS -->
                        <?php if($applicant->app_status == "requirements"):?>
                          <li class="nav-item">
                              <a class="nav-link" data-toggle="tab" href="#requirements" style="color:black;">
                                Requirements
                                <span class="badge badge-danger reqError" style = "border-radius:50%;display:none;">
                                  <i class="fa fa-exclamation-circle"></i>
                                </span>
                              </a>
                          </li>
                        <?php endif;?>
                    </ul>

                    <div class="tab-content">
                      <!-- EMPLOYEE DETAILS -->
                      <div id="employeeDetails" class="tab-pane fade show active">
                        <br>
                        <div class="container">
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Employee ID No.</label>
                                <input type="text" id="editEmployeeIdNo" class="form-control" value="<?= $applicant->app_ref_no ?>" autocomplete="off">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>First Name</label>
                                <input type="text" id="editFirstName" class="form-control" value="<?= $applicant->app_fname ?>" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" id="editMiddleName" class="form-control" value="<?= $applicant->app_mname ?>" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" id="editLastName" class="form-control" value="<?= $applicant->app_lname ?>" autocomplete="off">
                              </div>
                            </div>
                          </div>

                          <!--Birthday / Gender / Marital Status-->
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Birthdate</label>
                                <input type="text" id="editBirthday" class="form-control date_input_empty" value="<?= $applicant->app_birthday ?>" autocomplete="off" readonly>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Gender</label>
                                <select id="editGender" class="form-control">
                                  <option value="male" <?=($applicant->app_gender == "male")? "SELECTED": ""?>>Male</option>
                                  <option value="female" <?=($applicant->app_gender == "female")? "SELECTED": ""?>>Female</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Marital Status</label>
                                <select id="editMaritalStatus" class="form-control">
                                  <option value="single" <?=($applicant->app_marital_status == "single")? "SELECTED": ""?>>Single</option>
                                  <option value="married" <?=($applicant->app_marital_status == "married")? "SELECTED": ""?>>Married</option>
                                  <option value="widowed" <?=($applicant->app_marital_status == "widowed")? "SELECTED": ""?>>Widowed</option>
                                </select>
                              </div>
                            </div>
                          </div>

                          <!--Home Address-->
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Home Address 1</label>
                                <input type="text" id="editHomeAddress1" class="form-control" value="<?= $applicant->app_home_add1 ?>" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Home Address 2</label>
                                <input type="text" id="editHomeAddress2" class="form-control" value="<?= $applicant->app_home_add2 ?>" autocomplete="off">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                          </div>

                          <!--Contact No / Email / Active-->
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Contact No</label>
                                <input type="text" id="editContactNo" class="contactNumber form-control" value="<?= $applicant->app_contact_no ?>" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Email</label>
                                <input type="text" id="editEmail" class="form-control" value="<?= $applicant->app_email ?>" autocomplete="off">
                              </div>
                            </div>
                          </div>

                          <div class="row mb-5">
                            <div class="col-md-3">
                              <label for="SSS no" class="active">SSS no.</label>
                              <input id = "edit_sss_no" name = "edit_sss_no" type="text" class="form-control number-input" value = "<?=$applicant->app_sss_no?>">
                            </div>
                            <div class="col-md-3">
                              <label for="Philhealth no." class="active">Philhealth no.</label>
                              <input id = "edit_philhealth_no" name = "edit_philhealth_no" type="text" class="form-control number-input" value = "<?=$applicant->app_philhealth_no?>">
                            </div>
                            <div class="col-md-3">
                              <label for="Pagibig no." class="active">Pagibig no.</label>
                              <input id = "edit_pagibig_no" name = "edit_pagibig_no" type="text" class="form-control number-input" value = "<?=$applicant->app_pagibig_no?>">
                            </div>
                            <div class="col-md-3">
                              <label for="Tin no." class="active">Tin no.</label>
                              <input id = "edit_tin_no" name = "edit_tin_no" type="text" class="form-control number-input" value = "<?=$applicant->app_tin_no?>">
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-12 text-right">
                              <button id="editEmployeeBtn" class="btn btn-primary">Update Applicant Details</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- EDUCATION -->
                      <div id="employeeEducation" class="tab-pane fade">

                          <div class="row">
                              <div class="col-md-12">
                                  <!-- <button id="newEducation" class="btn btn-success btn-md"><i class="fa fa-plus"></i></button>   -->
                                  <div id="educationContainer" class = "row p-3">
                                    <div class="col-12 text-right">
                                      <button id = "editnewEducation" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-2"></i>Add Education</button>
                                      <input type="hidden" id="app_ref_no" value="<?=$app_ref_no?>">
                                    </div>
                                    <?php $i = 0 ?>
                                    <?php foreach($educations as $education): ?>
                                      <div class="educs col-12 educationHandler<?= $education->id ?>" id = "<?=$education->id?>">
                                        <?php $i++ ?>
                                        <div class="row">
                                          <div class="col-md-4 mb-1">
                                            <div class="form-group text-left">
                                              <h3>Education <?= $i ?></h3>
                                            </div>
                                          </div>
                                          <div class="col-md text-right">
                                            <button id="editEducBtn<?= $education->id ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil mr-2"></i>Update</button>
                                            <button id="delEducBtn<?= $education->id ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>

                                          </div>
                                        </div>
                                        <div class="educContainer row">
                                            <div class="col-md-6 mb-1">
                                              <div class="form-group text-left">
                                                <label>Year</label>
                                                <div class="form-group row">
                                                  <div class="col-md-6">
                                                    <input type="text" id = "empEducYearFrom<?= $education->id ?>" name = 'empEducYearFrom['.<?=$i?>.']' class="form-control date_input_empty" value='<?= $education->year_from ?>' readonly>
                                                    <small class="form-text">From <span class="asterisk"></span></small>
                                                  </div>

                                                  <div class="col-md-6">
                                                    <input type="text" id = "empEducYearTo<?= $education->id ?>" name = 'empEducYearTo['.<?=$i?>.']' class="form-control date_input_empty" value='<?= $education->year_to ?>' readonly>
                                                    <small class="form-text">To <span class="asterisk"></span></small>
                                                  </div>
                                                </div>
                                                <!-- <input type='text' id='empEducYear<?= $education->id ?>' name = 'empEducYear['.<?=$i?>.']' class='form-control editDateFieldRange' value='<?= $education->year_from ?>-<?= $education->year_to ?>' readonly> -->
                                              </div>
                                            </div>

                                            <div class="col-md-6 mb-1">
                                              <div class="form-group text-left">
                                                <label>School</label>
                                                <input type='text' id='empEducSchool<?= $education->id ?>' name = 'empEducSchool['.<?=$i?>.']' value='<?= $education->school ?>' class='form-control'>
                                              </div>
                                            </div>

                                            <div class="col-md-6 mb-1">
                                              <div class="form-group text-left">
                                                <label>Course</label>
                                                <input type='text' id='empEducCourse<?= $education->id ?>' name = 'empEducCourse['.<?=$i?>.']' value='<?= $education->course ?>' class='form-control'>
                                              </div>
                                            </div>

                                            <div class="col-md-6 mb-1">
                                              <div class="form-group text-left">
                                                <label>Level</label>
                                                <select id='empEducLevel<?= $education->id ?>' name = 'empEducLevel['.<?=$i?>.']' class='form-control'>
                                                  <?php if($educ_level->num_rows() > 0):?>
                                                    <?php foreach($educ_level->result_array() as $lvl):?>
                                                      <option value="<?=$lvl['id']?>" <?=($lvl['id'] == $education->level)? "SELECTED": ""?>><?=$lvl['description']?></option>
                                                    <?php endforeach;?>
                                                  <?php endif;?>

                                                </select>
                                              </div>
                                            </div>

                                        </div>
                                      </div>
                                      <hr>
                                    <?php endforeach; ?>
                                    <!-- <div class="col-12 text-right">
                                      <button id = "btn_update_educ" class="btn btn-success btn-sm">Update Education</button>
                                    </div> -->
                                  </div>
                              </div>
                          </div>

                      </div>
                      <!-- WORK HISTORY -->
                      <div id="workHistory" class="tab-pane fade">

                          <div class="row">
                              <div class="col-md-12 text-right">
                                  <!-- <button id="newWorkHis" class="btn btn-success btn-md"><i class="fa fa-plus"></i></button>   -->
                                  <div id="workHisContainer" class = "p-3">
                                    <div class="col-12 text-right">
                                      <button id = "editNewWorkHistory" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-2"></i>Add Work History</button>
                                    </div>
                                    <?php $i = 0 ?>
                                    <?php foreach($workHistory as $workHis): ?>
                                      <?php $i++ ?>
                                      <div class = "workHis col-12 workHisHandler<?= $workHis->id ?>" id="<?=$workHis->id?>">
                                        <div class="row">
                                          <div class='col-md-4 mb-1'>
                                            <div class='form-group text-left'>
                                              <h3>Work <?= $i ?></h3>
                                            </div>
                                          </div>
                                          <div class="col-md text-right">
                                            <button id="editWorkHis<?= $workHis->id ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil mr-2"></i>Update</button>
                                            <button id="delWorkHis<?= $workHis->id ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class='col-md-6 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Year</label>
                                              <div class="form-group row">
                                                <div class="col-md-6">
                                                  <input type="text" class="form-control date_input_empty" id='workYearFrom<?= $workHis->id ?>' value='<?= $workHis->year_from ?>' readonly>
                                                  <small class="form-text">From <span class="asterisk"></span></small>
                                                </div>

                                                <div class="col-md-6">
                                                  <input type="text" class="form-control date_input_empty" id='workYearTo<?= $workHis->id ?>' value='<?= $workHis->year_to ?>' readonly>
                                                  <small class="form-text">To <span class="asterisk"></span></small>
                                                </div>
                                              </div>
                                              <!-- <input type='text' id='workYear<?= $workHis->id ?>' class='form-control editDateFieldRange' value='<?= $workHis->year_from ?> - <?= $workHis->year_to ?>' readonly> -->
                                            </div>
                                          </div>

                                          <div class='col-md-6 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Stay</label>
                                              <input type='text' id='workStay<?= $workHis->id ?>' class='form-control' value='<?= $workHis->stay ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-6 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Company Name</label>
                                              <input type='text' id='workCompany<?= $workHis->id ?>' class='form-control' value='<?= $workHis->company_name ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-6 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Position</label>
                                              <input type='text' id='workPosition<?= $workHis->id ?>' class='form-control' value='<?= $workHis->position ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-6 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Level</label>
                                              <input type='text' id='workLevel<?= $workHis->id ?>' class='form-control' value='<?= $workHis->level ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-6 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Contact#</label>
                                              <input type='text' id='workContact<?= $workHis->id ?>' class='contactNumber form-control' value='<?= $workHis->contact_no ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-6 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Responsibility</label>
                                              <textarea name="" id='workResp<?= $workHis->id ?>' cols="30" rows="5" class = "form-control"><?php echo $workHis->responsibility;?></textarea>
                                              <!-- <input type='text' id='workResp<?= $workHis->id ?>' class='form-control' value=''> -->
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <hr>
                                    <?php endforeach; ?>
                                  </div>
                              </div>
                          </div>

                      </div>
                      <!-- DEPENDENTS -->
                      <div id="dependents" class="tab-pane fade">
                          <div class="row">
                              <div class="col-md-12 text-right">
                                  <!-- <button id="newDependents" class="btn btn-success btn-md"><i class="fa fa-plus"></i></button>   -->
                                  <div id="dependentsContainer" class = "p-3">
                                    <div class="col-12 text-right">
                                      <button id = "editNewDependents" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-2"></i>Add Dependents</button>
                                    </div>
                                    <?php $i = 0 ?>
                                    <?php foreach($dependents as $dependent): ?>
                                      <?php $i++ ?>
                                    <div class = "depts col-md-12 dependentHandler<?= $dependent->id ?>" id="<?= $dependent->id ?>">
                                      <div class='row'>
                                        <div class='col-md-4 mb-1'>
                                          <div class='form-group text-left'>
                                            <h3>Dependents <?= $i ?></h3>
                                          </div>
                                        </div>
                                        <div class="col-md text-right">
                                          <button id="editDependent<?= $dependent->id ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil mr-2"></i>Update</button>
                                          <button id="delDependent<?= $dependent->id ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </div>
                                      </div>

                                      <div class='dependentsContainer row'>

                                          <div class='col-md-4 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>First Name</label>
                                              <input type='text' id='dependFname<?= $dependent->id ?>' class='form-control' value='<?= $dependent->first_name ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-4 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Middle Name</label>
                                              <input type='text' id='dependMname<?= $dependent->id ?>' class='form-control' value='<?= $dependent->middle_name ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-4 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Last Name</label>
                                              <input type='text' id='dependLname<?= $dependent->id ?>' class='form-control' value='<?= $dependent->last_name ?>'>
                                            </div>
                                          </div>

                                          <div class='col-md-4 mb-1'>
                                            <div class='form-group text-left'>
                                              <label>Birthday</label>
                                              <input type='text' id='bday<?= $dependent->id ?>' class='form-control date_input_empty' value='<?= $dependent->birthday ?>'>
                                            </div>
                                          </div>


                                          <div class='col-md-4'>
                                            <div class='form-group text-left'>
                                              <label>Relationship</label>
                                              <!-- <input type='text' id='relationship<?= $dependent->id ?>' class='form-control' value='<?= $dependent->relationship ?>'> -->
                                              <select name="" id="relationship<?= $dependent->id ?>" class="form-control">
                                                <?php if($relation->num_rows() > 0):?>
                                                  <?php foreach($relation->result() as $rel):?>
                                                    <option value="<?=$rel->relationshipid?>" <?=($rel->relationshipid == $dependent->relationship)? "SELECTED" : ""?>><?=$rel->description?></option>
                                                  <?php endforeach;?>
                                                <?php endif;?>
                                              </select>
                                            </div>
                                          </div>


                                          <div class='col-md-4'>
                                            <div class='form-group text-left'>
                                              <label>Contact#</label>
                                              <input type='text' id='contactNo<?= $dependent->id ?>' class='contactNumber form-control' value='<?= $dependent->contact_no ?>'>
                                            </div>
                                          </div>

                                        <hr>
                                      </div>
                                    </div>
                                    <?php endforeach; ?>

                                  </div>
                              </div>
                          </div>
                      </div>
                      <!-- INTERVIEW -->
                      <div id="interview" class="tab-pane fade">
                        <div class="card">
                          <div class="card-header">
                            <h4>Interview Notes</h4>
                          </div>
                          <div class="card-body">
                            <textarea name="" id="summernote" cols="30" rows="10" class="form-control">
                              <?php if($interview->num_rows() > 0):?>
                                <?php echo $interview->row()->interview_notes;?>
                              <?php endif;?>
                            </textarea>
                          </div>
                          <div class="card-footer text-right">
                            <?php if($applicant->app_status == 'job_offer' || $applicant->app_status == 'requirements' || $applicant->app_status == 'hired'):?>
                              <div class="col-md-4 offset-md-8 pt-2 text-center" style = "border:1px solid #72716f !important;border-radius:3px;">
                                <h4 class="text-success">Passed</h4>
                              </div>
                            <?php endif;?>

                            <?php if($applicant->app_status == 'fail_interview'):?>
                              <div class="col-md-4 offset-md-8 pt-2 text-center" style = "border:1px solid red !important;border-radius:3px;">
                                <h4 class="text-danger">Failed</h4>
                              </div>
                            <?php endif;?>

                            <?php if($applicant->app_status == 'interview'):?>
                              <button id = "btn_int_pass" class="btn btn-primary">Pass</button>
                              <button id = "btn_int_fail" data-id = "<?= $applicant->app_ref_no ?>" data-action = "fail_interview" class="btn btn-danger btn_action">Failed</button>
                            <?php endif;?>

                            <!-- <button id = "btn_int_pass" class="btn btn-primary">Pass</button> -->
                            <!-- <button id = "btn_int_fail" data-id = "<?= $applicant->app_ref_no ?>" data-action = "fail_interview" class="btn btn-danger btn_action">Failed</button> -->
                          </div>
                        </div>
                      </div>
                      <!-- JOB OFFER -->
                      <div id="job_offer" class="tab-pane fade">
                        <div class="form-group row">
                          <div class="col-12 mt-3 mb-3 text-right">
                            <?php if($applicant->app_status == "job_offer"):?>
                              <button class="btn btn-primary" id = "btn_job_offer_modal">Create Job Offer</button>
                            <?php endif;?>
                          </div>
                          <div class="col-12 mb-3" id = "job_offer_wrapper">
                            <?php if($job_offer->num_rows() > 0):?>
                              <?php echo $job_offer->row()->content;?>
                            <?php endif;?>
                          </div>
                          <div id = "jo_footer" class="col-12 text-right" style = "display:<?=($applicant->app_status == 'job_offer') ? 'none': 'block'?>;">
                            <button id = "btn_print_job_offer" class="btn btn-primary">Print</button>
                            <?php if($applicant->app_status != "requirements" && $applicant->app_status != 'reject_joboffer'):?>
                              <button id = "btn_accept_jo" class="btn btn-primary">Accept</button>
                              <button id = "btn_decline_jo" data-id = "<?= $applicant->app_ref_no ?>" data-action = "reject_joboffer" class="btn btn-danger btn_action">Decline</button>
                            <?php endif;?>
                          </div>
                        </div>
                      </div>
                      <!-- REQUIREMENTS -->
                      <div id="requirements" class="tab-pane fade">
                        <form id="req_form">
                          <div class="row">
                            <input type="hidden" name = "editEmployeeIdNo" id="editEmployeeIdNo" class="form-control" value="<?= $applicant->app_ref_no ?>" autocomplete="off">
                            <!-- <?php print_r($requirements);?> -->
                            <!-- RESUME -->
                            <div class="col-md-6">
                              <div class="form-group row mt-3">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'resume')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Resume" class="form-control-label col-form-label-sm d-block">Resume (docx| doc | pdf) <span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'resume')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'resume');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" data-title = "Resume" class="btn btn-primary">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                  <?php else:?>
                                    <input type="file" name = "resume" id = "resume" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- 2 X 2 PICTURE -->
                            <div class="col-md-6">
                              <div class="form-group row mt-3">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'two_by_two_pic')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="2 X 2 Picture" class="form-control-label col-form-label-sm d-block">2 X 2 Picture (png |jpg |jpeg) <span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'two_by_two_pic')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'two_by_two_pic');?>
                                    <!-- <?php print_r($filter);?> -->
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "2x2 Picture">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <!-- <?php print_r($requirements);?> -->
                                    <input type="file" name = "_2x2_pic" id = "_2x2_pic" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- COLLEGE DIPLOMA -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'college_diploma')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="College Diploma" class="form-control-label col-form-label-sm d-block">College Diploma (png |jpg |jpeg) <span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'college_diploma')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'college_diploma');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "College Diploma">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "college_diploma" id = "college_diploma" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- TRANSCRIPT OF RECORDS -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'tor')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Transcript of Records" class="form-control-label col-form-label-sm d-block">Transcript of Records (docx| doc | pdf | png |jpg |jpeg) <span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'tor')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'tor');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Transcript of Records">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "trans_record" id = "trans_record" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- 2 X 2 VALID ID'S WITH 3 SIGNATURE -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'two_valid_id')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="2 X 2 VALID ID'S WITH 3 SIGNATURE" class="form-control-label col-form-label-sm d-block">2 VALID ID'S WITH 3 SIGNATURE (docx| doc | pdf | png |jpg |jpeg) <span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'two_valid_id')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'two_valid_id');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "2 Valid ID">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "_2x2_valid_id" id = "_2x2_valid_id" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- TIN -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'tin')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Tin" class="form-control-label col-form-label-sm d-block">Tin (docx| doc | pdf | png |jpg |jpeg) <span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'tin')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'tin');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Tin">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "tin" id = "tin" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- SSS E1 FORM  -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'sss_e1_form')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="SSS E1 FORM" class="form-control-label col-form-label-sm d-block">SSS E1 FORM (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'sss_e1_form')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'sss_e1_form');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "SSS E1 Form">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "sss_e1_form" id = "sss_e1_form" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- PHILHEALTH NUMBER  -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'philhealth_no')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Philhealth Number" class="form-control-label col-form-label-sm d-block">Philhealth Number (docx| doc | pdf | png |jpg |jpeg) <span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'philhealth_no')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'philhealth_no');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Philhealth Number">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "philhealth_req" id = "philhealth_req" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- PAG IBIG NUMBER -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'pagibig_no')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Pagibig Number" class="form-control-label col-form-label-sm d-block">Pagibig Number (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'pagibig_no')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'pagibig_no');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Pagibig Number">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "pagibig_req" id = "pagibig_req" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- PSA BIRTH CERTIFICATE -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'psa_birth_certificate')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="PSA Birth Certificate" class="form-control-label col-form-label-sm d-block">PSA Birth Certificate (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'psa_birth_certificate')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'psa_birth_certificate');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "PSA Birth Certificate">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "psa_birth_certificate" id = "psa_birth_certificate" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- NBI CLEARANCE -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'nbi_clearance')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="NBI Clearance" class="form-control-label col-form-label-sm d-block">NBI Clearance (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'nbi_clearance')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'nbi_clearance');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "NBI Clearance">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "nbi_clearance" id = "nbi_clearance" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- POLICE CLEARANCE ORIGINAL  -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'police_clearance')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Police Clearance (Original)" class="form-control-label col-form-label-sm d-block">Police Clearance (Original) (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'police_clearance')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'police_clearance');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Police Clearance">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "police_clearance" id = "police_clearance" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- BARANGAY CLEARANCE ORIGINAL -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'brgy_clearance')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Barangay Clearance (Original)" class="form-control-label col-form-label-sm d-block">Barangay Clearance (Original) (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'brgy_clearance')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'brgy_clearance');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Brgy Clearance">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "brgy_clearance" id = "brgy_clearance" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- MEDICAL CERTIFICATE AND RESULTS -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'med_certificate')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Medical Certificates and Results" class="form-control-label col-form-label-sm d-block">Medical Exam and Results (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'med_certificate')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'med_certificate');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Medical Certificate">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "med_certificate" id = "med_certificate" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>

                            <!-- -CONDITIONAL REQUIREMENTS -->

                            <!-- MARRIAGE CERTIFICATE -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'marriage_certificate')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Marriage Certificate" class="form-control-label col-form-label-sm d-block">Marriage Certificate (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'marriage_certificate')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'marriage_certificate');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Marriage Certificate">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "marriage_certificate" id = "marriage_certificate" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>
                            <!-- CHILDREN PSA BIRTH CERTIFICATE -->
                            <div class="col-md-6">
                              <div class="form-group row">
                                <div class="col-md-2 mb-2">
                                  <label class="container_label">
                                    <input type="checkbox" class = "content_nav" value = "" <?=(count((array)filter_requirements($requirements,'child_birth_certificate')) > 0) ? 'checked' : ''?>>
                                    <span class="checkmark"></span>
                                  </label>
                                </div>
                                <div class="col-md-10 mb-2">
                                  <label for="Children PSA Birth Certificate" class="form-control-label col-form-label-sm d-block">Children PSA Birth Certificate (docx| doc | pdf | png |jpg |jpeg)<span class="asterisk"></span></label>
                                  <?php if(count((array)filter_requirements($requirements,'child_birth_certificate')) > 0):?>
                                    <?php $filter = filter_requirements($requirements,'child_birth_certificate');?>
                                    <a href="<?=base_url($filter[0]['file_path'])?>" class="btn btn-primary" data-title = "Child Birth Certificate">
                                      <i class="fa fa-download mr-1"></i>Download
                                    </a>
                                    <button type = "button" class="btn btn-primary btn_view_req">View</button>
                                  <?php else:?>
                                    <input type="file" name = "psa_birth_certificate_2" id = "psa_birth_certificate_2" class="form-control requirement">
                                  <?php endif;?>
                                </div>
                              </div>
                            </div>

                            <div class="col-12 text-right">
                              <button type = "submit" class="btn btn-primary" id = "btn_save_req">
                                Save Requirements
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>

                    </div>
                </div>
                <div class="card-footer text-right">
                  <button id="approveApplicant" class="btn btn-primary">Hire Applicant</button>
                  <!-- <div class="dropup">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                      Action Button
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="#">Requirements</a>
                      <a class="dropdown-item" href="#">Hired Applicant</a>
                    </div>
                  </div> -->
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>

    <div class="modal fade" id = "view_req_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body" id = "view_req_ajax">
            <img src="" alt="" class = "req_image" style = "object-fit:contain !important;width:100%;object-position: 50% 50%;">
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "jo_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Job Offers</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12">
                <label for="Select Job Offer" class="form-control-label col-form-label-sm">Select Job Offer</label>
                <select name="select_jo" id="select_jo" class="form-control">
                  <option value="">------</option>
                  <?php if($job_offers->num_rows() > 0):?>
                    <?php foreach($job_offers->result_array() as $jo):?>
                      <option value="<?=en_dec('en',$jo['id'])?>"><?=$jo['template_name']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_job_offer">Create Job Offer</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\summernote\summernote.min.2.js')?>"></script>
<script src = "<?=base_url('assets/js/jquery.alphanum.js')?>"></script>
<script src = "<?=base_url('assets\js\signature-pad\signature_pad.umd.js')?>"></script>
<script src = "<?=base_url('assets\js\contracts\timer.js')?>"></script>
<script src="<?=base_url('assets/js/applicants/applicant.js');?>"></script>
