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
<style>
  #accordion .card-header{
    background-color: #607d8b!important;
    padding:5px;

  }

  #accordion .card-header button{
    color:#ffffff !important;
    font-size: 11px !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Employee Information</li>
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
                      <option value="by_dept">Department</option>
                      <option value="by_pos">Position</option>
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

                    <div id="divDept" class="filter_div single_search" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($department->num_rows() > 0):?>
                          <?php foreach($department->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                    <div id="divPos" class="filter_div single_search" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($positions->num_rows() > 0):?>
                          <?php foreach($positions->result_array() as $row):?>
                            <option value="<?=$row['positionid']?>">(<?=$row['department_name']?>) <?=$row['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <!-- STATUS SECTION -->
                <!-- <div class="form-group row">
                  <div class="col-md-3">
                    <label for="Employment Status" class="form-control-label col-form-label-sm">Employment Status</label>
                    <div class="col-12 mb-2">
                      <div class="form-check-inline">
                        <label class="form-check-label">
                          <input type="radio" name = "employment_status" class="form-check-input tran_status"  value="1" checked>Active Employee
                        </label>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <div class="form-check-inline">
                        <label class="form-check-label">
                          <input type="radio" name = "employment_status" class="form-check-input tran_status"  value="0">No Longer Part of Company
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label for="Employment Status" class="form-control-label col-form-label-sm">Employee Status</label>
                    <div class="col-12 mb-2">
                      <div class="form-check-inline">
                        <label class="form-check-label">
                          <input type="radio" name = "emp_status" class="form-check-input tran_status"  value="1" checked>Active
                        </label>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <div class="form-check-inline">
                        <label class="form-check-label">
                          <input type="radio" name = "emp_status" class="form-check-input tran_status"  value="0">Inactive
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label for="Employment Status" class="form-control-label col-form-label-sm">Contract Status</label>
                    <div class="col-12 mb-2">
                      <div class="form-check-inline">
                        <label class="form-check-label">
                          <input type="radio" name = "con_status" class="form-check-input tran_status"  value="active" checked>Active
                        </label>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <div class="form-check-inline">
                        <label class="form-check-label">
                          <input type="radio" name = "con_status" class="form-check-input tran_status"  value="inactive">Inactive
                        </label>
                      </div>
                    </div>
                  </div>

                </div> -->
                <div class="table-responsinve">
                  <table class="table table-bordered text-center" id = "employee_info_tbl">
                    <thead>
                      <th>Employee ID</th>
                      <th>Employee Name</th>
                      <th>Department</th>
                      <th>Position</th>
                      <th>Status</th>
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
    <!-- EMPLOYEE INFORMATION MODAL -->
    <div class="modal fade" id = "employmee_info_modal">
      <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Employee Informations</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div id="accordion">
              <!-- EMPLOYEE INFORMATION -->
              <div class="card">
                <div class="card-header" id="headingOne">
                  <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                      Employee Information
                    </button>
                  </h5>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                  <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" data-stype = "emp_record" data-emp_idno = "" href="#employee_record" style="color:black;">Employee Record</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" data-stype = "emp_educ" data-emp_idno = "" href="#employee_educ" style="color:black;" >Employee Education</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" data-stype = "emp_work" data-emp_idno = "" href="#employee_workhistory" style="color:black;" >Employee Work History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" data-stype = "emp_depend" data-emp_idno = "" href="#employee_dependents" style="color:black;" >Employee Dependents</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane fade show active" id = "employee_record">
                        <div class="form-group row mt-3 px-2">
                          <div class="col-md-4 mb-2">
                            <label for="Employee IDNo:" class="form-control-label col-form-label-sm">Employee IDNo:</label>
                            <input type="text" id = "employee_idno" class="form-control" readonly>
                          </div>
                          <div class="col-md-4 mb-2">
                            <label for="Applicant Refno:" class="form-control-label col-form-label-sm">Applicant Refno:</label>
                            <input type="text" id = "applicant_refno" class="form-control" readonly>
                          </div>
                          <div class="col-md-4 mb-2"></div>
                          <div class="col-md-4 mb-2">
                            <label for="First Name" class="form-control-label col-form-label-sm">First Name:</label>
                            <input type="text" class="form-control" id="first_name" readonly>
                          </div>
                          <div class="col-md-4 mb-2">
                            <label for="Middle Name:" class="form-control-label col-form-label-sm">Middle Name:</label>
                            <input type="text" class="form-control" id="middle_name" readonly>
                          </div>
                          <div class="col-md-4 mb-2">
                            <label for="Last Name:" class="form-control-label col-form-label-sm">Last Name:</label>
                            <input type="text" class="form-control" id = "last_name" readonly>
                          </div>
                          <div class="col-md-4 mb-2">
                            <label for="Birthdate" class="form-control-label col-form-label-sm">Birthdate:</label>
                            <input type="text" class="form-control" id="birthday" readonly>
                          </div>
                          <div class="col-md-4 mb-2">
                            <label for="Gender" class="form-control-label col-form-label-sm">Gender:</label>
                            <input type="text" class="form-control" id = "gender" readonly>
                          </div>
                          <div class="col-md-4 mb-2">
                            <label for="Marital Status:" class="form-control-label col-form-label-sm">Marital Status:</label>
                            <input type="text" class="form-control" id = "marital_status" readonly>
                          </div>
                          <div class="col-md-6 mb-2">
                            <label for="Home Address 1" class="form-control-label col-form-label-sm">Home Address 1:</label>
                            <input type="text" class="form-control" id="home_address1" readonly>
                          </div>
                          <div class="col-md-6 mb-2">
                            <label for="Home Address 2:" class="form-control-label col-form-label-sm">Home Address 2:</label>
                            <input type="text" class="form-control" id="home_address2" readonly>
                          </div>
                          <div class="col-md-6 mb-2">
                            <label for="Contact No." class="form-control-label col-form-label-sm">Contact No:</label>
                            <input type="text" class="form-control" id="contact_no" readonly>
                          </div>
                          <div class="col-md-6 mb-2">
                            <label for="Email" class="form-control-label col-form-label-sm">Email:</label>
                            <input type="text" class="form-control" id="email" readonly>
                          </div>
                          <div class="col-md-3 mb-2">
                            <label for="SSS No:" class="form-control-label col-form-label-sm">SSS No:</label>
                            <input type="text" class="form-control" id="sss_no" readonly>
                          </div>
                          <div class="col-md-3 mb-2">
                            <label for="Philhealth No:" class="form-control-label col-form-label-sm">Philhealth No:</label>
                            <input type="text" class="form-control" id = "philhealth_no" readonly>
                          </div>
                          <div class="col-md-3 mb-2">
                            <label for="Pagibig No:" class="form-control-label col-form-label-sm">Pagibig No:</label>
                            <input type="text" class="form-control" id="pagibig_no" readonly>
                          </div>
                          <div class="col-md-3 mb-2">
                            <label for="Tin No" class="form-control-label col-form-label-sm">Tin No:</label>
                            <input type="text" class="form-control" id="tin_no" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id = "employee_educ">
                        <div class="form-group row mt-3 px-2 educ_ajax">

                        </div>
                      </div>
                      <div class="tab-pane fade" id = "employee_workhistory">
                        <div class="form-group row mt-3 px-2 work_ajax">

                        </div>
                      </div>
                      <div class="tab-pane fade" id = "employee_dependents">
                        <div class="form-group row mt-3 px-2 depend_ajax">

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- CONTRACT -->
              <div class="card">
                <div class="card-header" id="headingTwo">
                  <h5 class="mb-0">
                    <button class="btn btn-link collapsed contract_accordion" data-cid = "" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                      Contract
                    </button>
                  </h5>
                </div>
                <div id="collapseTwo" class="collapse contract_accordion_div" aria-labelledby="headingTwo" data-parent="#accordion">
                  <div class="card-body">
                    <div class="form-group row">
                      <div class="col-md-12 mb-3">
                        <h4>Contract Details</h4>
                      </div>
                      <div class="col-md-4 mb-2">
                        <label for="WorkSite" class="form-control-label col-form-label-sm">WorkSite:</label>
                        <input type="text" id = "worksite" class="form-control" readonly>
                      </div>
                      <div class="col-md-4 mb-2">
                        <label for="Position" class="form-control-label col-form-label-sm">Position:</label>
                        <input type="text" id ="position" class="form-control" readonly>
                      </div>
                      <div class="col-md-4 mb-2">
                        <label for="Employee Status:" class="form-control-label col-form-label-sm">Employee Status:</label>
                        <input type="text" id = "emp_status" class="form-control" readonly>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label for="Start Date" class="form-control-label col-form-label-sm">Start Date:</label>
                        <input type="text" id = "start_date" class="form-control" readonly>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label for="End Date" class="form-control-label col-form-label-sm">End Date:</label>
                        <input type="text" id = "end_date" class="form-control" readonly>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label for="Company" class="form-control-label col-form-label-sm">Company:</label>
                        <input type="text" id = "company" class="form-control" readonly>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label for="Contract Type" class="form-control-label col-form-label-sm">Contract Type:</label>
                        <input type="text" id = "contract_type" class="form-control" readonly>
                      </div>

                      <div class="col-md-12 mb-2">
                        <h4>Salary</h4>
                      </div>
                      <div class="col-md-8 mb-2">
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped text-center" style = "border-top: 1px solid gainsboro;">
                            <thead>
                              <th>Salary Category</th>
                              <th>Amount</th>
                            </thead>
                            <tbody id = "sal_ajax">
                              <tr>
                                <th>Total</th>
                                <td id = "total_sal">PHP 0.00</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-md-12 mb-2">
                        <h4>Compensation</h4>
                      </div>
                      <div class="col-md-4 mb-2">
                        <label for="SSS" class="form-control-label col-form-label-sm">SSS:</label>
                        <input type="text" id = "sss" class="form-control" readonly>
                      </div>
                      <div class="col-md-4 mb-2">
                        <label for="Philhealth" class="form-control-label col-form-label-sm">Philhealth:</label>
                        <input type="text" id="philhealth" class="form-control" readonly>
                      </div>
                      <div class="col-md-4 mb-2">
                        <label for="Pagibig" class="form-control-label col-form-label-sm">Pagibig:</label>
                        <input type="text" id = "pagibig" class="form-control" readonly>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="Tax" class="form-control-label col-form-label-sm">Tax:</label>
                        <input type="text" id = "tax" class="form-control" readonly>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="Pay Type" class="form-control-label col-form-label-sm">Pay Type:</label>
                        <input type="text" id = "paytype" class="form-control" readonly>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="Payout Medium" class="form-control-label col-form-label-sm">Payout Medium:</label>
                        <input type="text" id = "payout_medium" class="form-control" readonly>
                      </div>
                      <div class="col-md-12 mb-2">
                        <h4>Leave</h4>
                      </div>
                      <div class="col-md-8 mb-2">
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped" style = "border-top: 1px solid gainsboro;">
                            <thead>
                              <th>Leave</th>
                              <th>Days</th>
                            </thead>
                            <tbody id = "leave_ajax">

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- WORK SCHEDULE -->
              <div class="card">
                <div class="card-header" id="headingThree">
                  <h5 class="mb-0">
                    <button class="btn btn-link collapsed contract_worksched" data-worksched = "" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                      Work Schedule
                    </button>
                  </h5>
                </div>
                <div id="collapseThree" class="collapse accordion_worksched_div" aria-labelledby="headingThree" data-parent="#accordion">
                  <div class="card-body">
                    <div class="form-group row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-bordered text-center" style ='border-top:1px solid gainsboro;' >
                            <thead>
                              <th>Days</th>
                              <th>Time In</th>
                              <th>Time Out</th>
                              <th>Break In</th>
                              <th>Break Out</th>
                              <th>Total</th>
                            </thead>
                            <tbody id = "work_sched_ajax">

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
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
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\reports\employee_info.js')?>"></script>
