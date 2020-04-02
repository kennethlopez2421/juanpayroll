<!DOCTYPE html>
<html>
<head>
	<?php
		if(isset($this->session->database_name) && $this->session->database_name != ''){
			$this->db = switch_database($this->session->database_name);
		}else{
			header(base_url().'Main/logout');
		}
	?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?=company_name();?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <!-- Jquery Toast CSS-->
  <link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
  <link rel="stylesheet" href="<?=base_url('assets/css/easy-autocomplete.css');?>">
  <link rel="stylesheet" href="<?=base_url('assets/css/mdb.min.css');?>">
  <link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>">
</head>
<body data-base_url="<?=base_url();?>">
	<div class="page">
		<div class="container">
			<br>
      <section class="tables">
          <div class="container-fluid">
              <div class="row">
                  <div class="col-lg-12">
                      <div class="card">
                        <div class="card-header">
                          <h3>Application Form</h3>
                        </div>
                        <div class="card-body">

                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#employeeDetails" style="color:black;">Appicant Record</a>
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
                                        <label>Applicant Ref No.<span class = "ml-2 text-danger" >*</span></label>
                                        <input type="text" id="applicantIdNo" class="form-control em_rField" autocomplete="off" value = "<?=generate_player_no()?>" readonly>
																				<input type="hidden" id = "tDec" value = "<?=$token_dec?>">
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
                                        <input type="text" id="contactNo" class="form-control em_rField" autocomplete="off">
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

																	<div class="row">
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
                        <div class="card-footer">
                          <div class="col-md-12 text-right">
                            <button id="addEmployeeBtn" class="btn btn-primary">Add Employee</button>
                          </div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
      </section>
		</div>

		<div class="copyrights text-center">
			<p>Powered by <a href="http://cloudpanda.cloudpanda.com.ph/" class="external">Cloud Panda PH</a></p>
		</div>
	</div>

<!-- Javascript files-->
<script src="<?=base_url('assets/js/jquery.min.js');?>"></script>
<script src="<?= base_url('assets/js/tether.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.cookie.js'); ?>"> </script>
<script src="<?= base_url('assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/front.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>
<script src="<?=base_url('assets/js/datatables.min.js');?>"></script>
<script src = "<?=base_url('assets/js/jquery.alphanum.js')?>"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script>

<script src="<?=base_url('assets/js/front.js');?>"></script>
<script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>
<script src="<?=base_url('assets/js/jquery-code-scanner.js');?>"></script>
<script src="<?=base_url('assets/js/notification.js');?>"></script>
<script src="<?=base_url('assets/js/bootstrap-datepicker.min.js');?>"></script>
<script src="<?=base_url('assets/js/accounting.min.js');?>"></script>
<script src="<?=base_url('assets/js/custom.js');?>"></script>
<script src = "<?=base_url('assets/js/utility_helper.js')?>"></script>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID.-->
<!---->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
e=o.createElement(i);r=o.getElementsByTagName(i)[0];
e.src='<?=base_url('assets/js/analytics.js');?>';
r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
ga('create','UA-XXXXX-X');ga('send','pageview');
</script>

<script src="<?=base_url('assets/js/applicants/applicant.js');?>"></script>
</body>
</html>
