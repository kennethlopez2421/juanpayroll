<?php

 if(!isset($_SESSION['user_id'])) {
   header(base_url('Main/logout'));
 }

?>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/employees_home/'.$token);?>">Entity</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('employees/Employee/index/'.$token);?>">Employee</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Edit Employee</li>
        </ol>
    </div>
    <input type="hidden" id="empID" value="<?= $empID ?>">
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                      <div class="card-body">

                          <ul class="nav nav-tabs">
                              <li class="nav-item">
                                  <a class="nav-link active" data-toggle="tab" href="#employeeDetails" style="color:black;">Employee Record</a>
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
                          </ul>

                          <div class="tab-content">
                            <div id="employeeDetails" class="tab-pane fade show active">
                              <br>
                              <div class="container">
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Employee ID No.</label>
                                      <input type="text" id="editEmployeeIdNo" class="form-control" value="<?= $employee->employee_idno ?>" autocomplete="off" readonly>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>First Name</label>
                                      <input type="text" id="editFirstName" class="form-control" value="<?= $employee->first_name ?>" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Middle Name</label>
                                      <input type="text" id="editMiddleName" class="form-control" value="<?= $employee->middle_name ?>" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Last Name</label>
                                      <input type="text" id="editLastName" class="form-control" value="<?= $employee->last_name ?>" autocomplete="off">
                                    </div>
                                  </div>
                                </div>

                                <!--Birthday / Gender / Marital Status-->
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Birthdate</label>
                                      <input type="text" id="editBirthday" class="form-control date_input_empty" value="<?= $employee->birthday ?>" autocomplete="off" readonly>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Gender</label>
                                      <select id="editGender" class="form-control">
                                        <!-- <option><?= $employee->gender ?></option> -->
                                        <option value="male" <?=($employee->gender == "male") ? "SELECTED": ""?>>Male</option>
                                        <option value="female" <?=($employee->gender == "female") ? "SELECTED": ""?>>Female</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Marital Status</label>
                                      <select id="editMaritalStatus" class="form-control">
                                        <option value="single" <?=( $employee->marital_status == "single")? "SELECTED": ""?>>Single</option>
                                        <option value="married" <?=( $employee->marital_status == "married")? "SELECTED": ""?>>Married</option>
                                        <option value="widowed" <?=( $employee->marital_status == "widowed")? "SELECTED": ""?>>Widowed</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>

                                <!--Home Address-->
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Home Address 1</label>
                                      <input type="text" id="editHomeAddress1" class="form-control" value="<?= $employee->home_address1 ?>" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Home Address 2</label>
                                      <input type="text" id="editHomeAddress2" class="form-control" value="<?= $employee->home_address2 ?>" autocomplete="off">
                                    </div>
                                  </div>
                                </div>
                                <!--Contact No / Email / Active-->
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Contact No</label>
                                      <input type="text" id="editContactNo" class="contactNumber form-control" value="<?= $employee->contact_no ?>" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Email</label>
                                      <input type="text" id="editEmail" class="form-control" value="<?= $employee->email ?>" autocomplete="off">
                                    </div>
                                  </div>

                                </div>

                                <div class="row mb-5">
                                  <div class="col-md-3">
                                    <label for="SSS" class="active">SSS no.</label>
                                    <input id = "sss_no" name = "sss_no" type="text" class="form-control number-input" value = "<?=$employee->sss_no?>">
                                  </div>
                                  <div class="col-md-3">
                                    <label for="Philhealth" class="active">Philhealth no.</label>
                                    <input id = "philhealth_no" name = "philhealth_no" type="text" class="form-control number-input" value = "<?=$employee->philhealth_no?>">
                                  </div>
                                  <div class="col-md-3">
                                    <label for="Pagibig" class="active">Pagibig no.</label>
                                    <input id = "pagibig_no" name = "pagibig_no" type="text" class="form-control number-input" value = "<?=$employee->pagibig_no?>">
                                  </div>
                                  <div class="col-md-3">
                                    <label for="Tin Number" class="active">Tin no.</label>
                                    <input id = "tin_no" name = "tin_no" type="text" class="form-control number-input" value = "<?=$employee->tin_no?>">
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-md-12 text-right">
                                    <button id="editEmployeeBtn" class="btn btn-primary">Update Employee</button>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div id="employeeEducation" class="tab-pane fade">

                                <div class="row">
                                  <div class="col-md-12">
                                    <div id="educationContainer" class = "row p-3">
                                      <div class="col-12 text-right">
                                        <button id = "editnewEducation" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-2"></i>Add Education</button>
                                        <input type="hidden" id="employee_idno" value="<?=$employee_idno?>">
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
                                    </div>
                                  </div>
                                </div>
                            </div>

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
                                                    <textarea name="" id="workResp<?= $workHis->id ?>" cols="30" rows="5" class = "form-control"><?php echo $workHis->responsibility;?></textarea>
                                                    <!-- <input type='text' id='workResp<?= $workHis->id ?>' class='form-control' value='<?= $workHis->responsibility ?>'> -->
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
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src = "<?=base_url('assets/js/jquery.alphanum.js')?>"></script>
<script src="<?= base_url('assets/js/employees/employees.js') ?>"></script>
