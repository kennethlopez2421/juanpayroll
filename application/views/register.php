<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?=company_name();?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="all,follow">
	<!-- Bootstrap CSS-->
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
	<!-- Google fonts - Roboto -->
	<link rel="stylesheet" href="<?=base_url('assets/css/google_fonts.css');?>">
	<!-- theme stylesheet--> <!-- we change the color theme by changing color.css -->
	<link rel="stylesheet" href="<?= base_url('assets/css/style.blue.css'); ?>" id="theme-stylesheet">
	<link rel="stylesheet" href="<?=base_url('assets/css/select2-materialize.css');?>">
	<!-- Custom stylesheet - for your changes-->
	<link rel="stylesheet" href="<?= base_url('assets/css/custom.css'); ?>">
	<!-- Favicon-->
	<link rel="shortcut icon" href="<?= base_url('assets/img/favicon.ico'); ?>">
	<!-- Font Awesome CDN-->
	<!-- you can replace it by local Font Awesome-->
	<link rel="stylesheet" href="<?=base_url('assets/css/font-awesome.min.css');?>">
	<!-- Font Icons CSS-->
	<link rel="stylesheet" href="<?=base_url('assets/css/myfontastic.css');?>">
	<link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">

	<!-- Bootstrap Datepicker CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/bootstrap-datepicker3.min.css');?>">
	
	<!-- Tweaks for older IEs--><!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<body data-base_url="<?=base_url();?>">
	<div class="page login-page register-page">
		<div class="container d-flex align-items-center">
			<div class="form-holder has-shadow">
				<div class="row">
					<!-- Logo & Information Panel-->
					<!-- <div class="col-lg-6">
						
						<div class="info d-flex align-items-center">

							<div class="content">
								<div class="logo" style="">
									<img class="img-fluid" src="<?=base_url('assets/img/smalltownlottery.png');?>">
								</div>
							</div>
						</div>
					</div> -->
					<!-- Form Panel    -->
					<div class="col-lg-12 bg-white">
						<div class="form d-flex align-items-center">
							<div class="content">
								<h1>Registration Form</h1>
								<br>
								<form id="register-form" method="post">
									<div class="col-lg-12">
										<div class="row row-reg">
											<div class="col-lg-4">
												<div class="form-group">
													<input id="register-firstname" type="text" name="registerFirstname" required="" class="input-material">
													<label for="register-firstname" class="label-material">Firstname <span class="text-danger">*</span></label>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<input id="register-middlename" type="text" name="registerMiddlename" class="input-material">
													<label for="register-middlename" class="label-material">Middle Name</label>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<input id="register-lastname" type="text" name="registerLastname" required="" class="input-material">
													<label for="register-lastname" class="label-material">Last Name <span class="text-danger">*</span></label>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<input id="register-bday" type="text" name="registerBday" required="" value="<?= date_format(date_create(today()),"m/d/Y")?>" class="input-material datepicker">
													<label for="register-bday" class="label-material active">Birthdate <span class="text-danger">*</span></label>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<input id="register-contactno" type="text" name="registerContactno" required="" class="input-material">
													<label for="register-contactno" class="label-material">Contact No. <span class="text-danger">*</span></label>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<div class="i-checks">
														<input id="register-gendermale" type="radio" value="1" name="registerGender" class="radio-template" checked="">
														<label for="register-gendermale">Male</label>
													</div>

													<div class="i-checks">
														<input id="register-genderfemale" type="radio" value="2" name="registerGender" class="radio-template">
														<label for="register-genderfemale">Female</label>
													</div>
												</div>
											</div>

											<div class="col-lg-12">
												<div class="form-group">
													<input id="register-address" type="text" name="registerAddress" required="" class="input-material">
													<label for="register-address" class="label-material">Address <span class="text-danger">*</span></label>
												</div>
											</div>

											<div class="col-lg-6">
												<div class="form-group">
													<label for="register-country" class="form-control-label col-form-label-sm">Country <span class="text-danger">*</span></label>
													<select name="registerCountry" class="form-control" id="register-country" required="">
														<?php
														foreach ($get_country->result() as $gcountry){
														?>
															<option value="<?=$gcountry->country_id?>">
																<?=$gcountry->country?>
															</option>
														<?php
														}
														?>
													</select>
												</div>
											</div>

											<div class="col-lg-6">
												<div class="form-group">
													<label for="register-city" class="form-control-label col-form-label-sm">City <span class="text-danger">*</span></label>
													<br>
													<select name="registerCity" class="" id="register-city" required="">
														<?php
														foreach ($get_city->result() as $gcity) {
														?>
															<option value="<?=$gcity->city_id?>">
																<?=$gcity->city?>
															</option>
														<?php
														}
														?>
														<!-- <option value="">-- Select City --</option> -->
														
													</select>
												</div>
											</div>

											<div class="col-lg-12">
												<div class="col-lg-6">
													<div class="form-group">
														<input id="register-email" type="email" name="registerEmail" required="" class="input-material">
														<label for="register-email" class="label-material">Email <span class="text-danger">*</span></label>
													</div>
												</div>
											</div>

											<div class="col-lg-6">
												<div class="form-group">
													<input id="register-password" type="password" name="registerPassword" required="" class="input-material">
													<label for="register-password" class="label-material">Password <span class="text-danger">*</span></label>
												</div>
											</div>

											<div class="col-lg-6">
												<div class="form-group">
													<input id="register-repassword" type="password" name="registerRepassword" required="" class="input-material">
													<label for="register-repassword" class="label-material">Re-type Password <span class="text-danger">*</span></label>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" style="float: right;">
										<div class="i-checks">
			                            	<input id="checkboxCustom1" type="checkbox" value="" class="checkbox-template termsCheckbox">
			                              	<label for="checkboxCustom1">I agree to the <a href="<?=base_url('Main/termsandcondition');?>" target="_blank">Terms and Conditions.</a></label>
			                            </div>
			                            <small>Already have an account? </small><a href="<?=base_url('Main/index');?>" class="signup">Sign In</a>
		                        	</div>
		                        	<div class="clearfix"></div>
		                        	<div style="float: right;"> 
			                            <button type="button" id="register" class="btn btn-danger btnregCancel">Cancel</button>
										<button id="register" href="#"  class="btn btn-primary btnReg">Register</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
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
<script src="<?= base_url('assets/js/select2.min.js'); ?>"></script>
<script src="<?=base_url('assets/js/bootstrap-datepicker.min.js');?>"></script>
<!-- Google Analytics: change UA-XXXXX-X to be your site's ID.-->
<!---->
<script>
(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
e=o.createElement(i);r=o.getElementsByTagName(i)[0];
e.src='<?=base_url('assets/js/analytics.js');?>';
r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
ga('create','UA-XXXXX-X');ga('send','pageview');
</script>

<script src="<?=base_url('assets/js/register.js');?>"></script>
</body>
</html>