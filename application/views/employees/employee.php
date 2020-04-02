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
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/employees_home/'.$token);?>">Entity</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Employee</li>
        </ol>
    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
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

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">Employee</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <button id = "btnSearchEmp" class="btn btn-primary btnClickAddArea" style="right:130px; position: absolute; top:20px; width: 8%;">Search</button>
                            <form method="get" action="<?= base_url('employees/Employee/add/'.$token) ?>">
                              <button type="submit" class="btn btn-primary btnClickAddArea" style="right:20px; position: absolute; top:20px; width: 8%;">Add</button>
                            </form>

                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="employeeTable"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th>QR Code</th>
                                            <th width="">ID</th>
                                            <th width="">Description</th>
                                            <th width = "100">Status</th>
                                            <th width = "150">Action</th>
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
    <div id="addCityModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                    <div class="modal-body">

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

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label>Employee ID#:</label>
                                           <input type="text" id="employeeIdNo" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label>First Name</label>
                                           <input type="text" id="firstName" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label>Middle Name</label>
                                           <input type="text" id="middleName" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label>Last Name</label>
                                           <input type="text" id="lastName" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label>Birthday</label>
                                           <input type="text" id="birthday" class="form-control dateField" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Gender</label>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="gender" value="male" checked>
                                          <label class="form-check-label">
                                            Male
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="gender" value="female">
                                          <label class="form-check-label">
                                            Female
                                          </label>
                                        </div>
                                   </div>
                                </div>


                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <label>Marital Status</label>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="maritalStatus" value="single" checked>
                                          <label class="form-check-label">
                                            Single
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="maritalStatus" value="married">
                                          <label class="form-check-label">
                                            Married
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="maritalStatus" value="widowed">
                                          <label class="form-check-label">
                                            Widowed
                                          </label>
                                        </div>
                                   </div>
                                </div>

                                <div class="row pt-4">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                           <label>Home Address 1</label>
                                           <input type="text" id="homeAddress1" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                           <label>Home Address 2</label>
                                           <input type="text" id="homeAddress2" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label>Contact#</label>
                                           <input type="text" id="contactNo" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                           <label>Email</label>
                                           <input type="text" id="email" class="form-control" autocomplete="off">
                                       </div>
                                   </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>is Active</label>
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="isActive" value="true" checked>
                                          <label class="form-check-label">
                                            Yes
                                          </label>
                                        </div>

                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="isActive" value="false">
                                          <label class="form-check-label">
                                            No
                                          </label>
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

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="addEmployeeBtn" style="float:right" class="btn btn-success saveBtnArea">Add</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>


    <div id="delEmployeeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
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
                                    <p>Are you sure you want to <u>End the Employment</u> of (<bold class="info_desc"></bold>) ?</p>
                                    <input type="hidden" class="employeeid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delEmpBtn" style="float:right" class="btn btn-primary deleteAreaBtn">End Employment</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id = "end_employment_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">End of Employment</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <label for="Name of Employee" class="form-control-label col-form-label-sm">Name of Employee</label>
                <input type="text" id = "name_of_emp" class = "form-control" readonly>
              </div>
              <div class="col-md-12">
                <label for="Date of Termination" class="form-control-label col-form-label-sm">End of Employment <span class="asterisk"></span></label>
                <input type="text" id = "date_of_termination" class = "form-control date_input_empty rq3" placeholder="YYYY-MM-DD">
                <input type="hidden" id = "emp_id">
                <input type="hidden" id = "emp_id2">
              </div>
              <div class="col-md-12">
                <label for="Reason" class="form-control-label col-form-label-sm">Reason <span class="asterisk"></span></label>
                <textarea id = "reason" class = "form-control rq3" rows="3" cols="80"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button id = "btn_end_employment" class="btn btn-sm btn-primary" >Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <form id="edit-emp" method="post" action="<?= base_url('employees/Employee/edit') ?>">

    </form>

<div class="modal fade" id = "qr_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Qr Code</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="img-thumbnail view_image" style = "height:200px;width:100%;">

        </div>
      </div>
      <div class="modal-footer text-right">
        <!-- <button class="btn btn-sm btn-primary">Save</button> -->
        <button class="btn blue-grey" data-dismiss = "modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?= base_url('assets/js/employees/employees.js') ?>"></script>
