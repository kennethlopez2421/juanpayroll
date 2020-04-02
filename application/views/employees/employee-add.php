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
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('employees/Employee/index/'.$token);?>">Employees</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">New Employee</li>
        </ol>
    </div>
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
                                  <a class="nav-link" data-toggle="tab" href="#employeeEducation" style="color:black;" >
                                    Education
                                    <span class="badge badge-danger educError" style = "border-radius:50%;display:none;">
                                      <i class="fa fa-exclamation-circle"></i>
                                    </span>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a class="nav-link" data-toggle="tab" href="#workHistory" style="color:black;">
                                    Work History
                                    <span class="badge badge-danger workError" style = "border-radius:50%;display:none;">
                                      <i class="fa fa-exclamation-circle"></i>
                                    </span>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a class="nav-link" data-toggle="tab" href="#dependents" style="color:black;">
                                    Dependents
                                    <span class="badge badge-danger dependentError" style = "border-radius:50%;display:none;">
                                      <i class="fa fa-exclamation-circle"></i>
                                    </span>
                                  </a>
                              </li>
                          </ul>

                          <div class="tab-content">
                            <div id="employeeDetails" class="tab-pane fade show active">
                              <br>
                              <div class="container">
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Employee ID No.<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="employeeIdNo" class="form-control em_rField" autocomplete="off" value = "<?=$employeeIdNo?>">
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>First Name<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="firstName" class="form-control em_rField" autocomplete="off">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Middle Name</label>
                                      <input type="text" id="middleName" class="form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Last Name<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="lastName" class="form-control em_rField" autocomplete="off">
                                      <span class = "duplicateNameError text-danger d-block"></span>
                                    </div>
                                  </div>
                                </div>

                                <!--Birthday / Gender / Marital Status-->
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Birthdate<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="birthday" class="form-control date_input em_rField" autocomplete="off" readonly>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Gender<span class = "ml-2 text-danger" >*</span></label>
                                      <select id="gender" class="form-control">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Marital Status<span class = "ml-2 text-danger" >*</span></label>
                                      <select id="maritalStatus" class="form-control">
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                        <option value="widowed">Widowed</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>

                                <!--Home Address-->
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Home Address 1<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="homeAddress1" class="form-control em_rField" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Home Address 2</label>
                                      <input type="text" id="homeAddress2" class="form-control" autocomplete="off">
                                    </div>
                                  </div>
                                </div>

                                <!--Contact No / Email / Active-->
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Contact No<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="contactNo" class="form-control" autocomplete="off">
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Email<span class = "ml-2 text-danger" >*</span></label>
                                      <input type="text" id="email" class="form-control em_rField" autocomplete="off">
                                      <span class = "text-danger d-block emailExistError"></span>
                                    </div>
                                  </div>
                                </div>

                                <div class="row mb-5">
                                  <div class="col-md-3">
                                    <label for="SSS" class="active">SSS no.</label>
                                    <input id = "sss_no" name = "sss_no" type="text" class="form-control number-input">
                                  </div>
                                  <div class="col-md-3">
                                    <label for="Philhealth" class="active">Philhealth no.</label>
                                    <input id = "philhealth_no" name = "philhealth_no" type="text" class="form-control number-input">
                                  </div>
                                  <div class="col-md-3">
                                    <label for="Pagibig" class="active">Pagibig no.</label>
                                    <input id = "pagibig_no" name = "pagibig_no" type="text" class="form-control number-input">
                                  </div>
                                  <div class="col-md-3">
                                    <label for="Tin Number" class="active">Tin no.</label>
                                    <input id = "tin_no" name = "tin_no" type="text" class="form-control number-input">
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-md-12 text-right">
                                    <button id="addEmployeeBtn" class="btn btn-primary">Add Employee</button>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div id="employeeEducation" class="tab-pane fade">

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button id="newEducation" class="btn btn-success btn-md"><i class="fa fa-plus"></i></button>
                                        <div id="educationContainer">

                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div id="workHistory" class="tab-pane fade">

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button id="newWorkHis" class="btn btn-success btn-md"><i class="fa fa-plus"></i></button>
                                        <div id="workHisContainer">

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="dependents" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button id="newDependents" class="btn btn-success btn-md"><i class="fa fa-plus"></i></button>
                                        <div id="dependentsContainer">

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
