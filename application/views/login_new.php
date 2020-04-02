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
  <link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>">
	<!-- Google fonts - Roboto -->
	<link rel="stylesheet" href="<?=base_url('assets/css/google_fonts.css');?>">
	<!-- theme stylesheet--> <!-- we change the color theme by changing color.css -->
	<link rel="stylesheet" href="<?= base_url('assets/css/style.blue.css'); ?>" id="theme-stylesheet">
	<!-- Custom stylesheet - for your changes-->
	<link rel="stylesheet" href="<?= base_url('assets/css/custom.css'); ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/mdb.min.css'); ?>">
	<!-- Favicon-->
	<link rel="shortcut icon" href="<?=base_url('assets/img/juanpayroll-logo-05.png');?>">
	<!-- Font Awesome CDN-->
	<!-- you can replace it by local Font Awesome-->
	<!-- <script src="<?//=base_url('assets/js/fontawesome.js');?>"></script> -->
	<!-- Font Icons CSS-->
	<link rel="stylesheet" href="<?=base_url('assets/css/myfontastic.css');?>">
	<link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
	<!-- Tweaks for older IEs--><!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
	<style type="text/css">
		input.input-material ~ label.active {
		    font-size: 0.8em;
		    top: -17px;
		    color: #2b90d9;
		}

    .fa{
      font-size: 30px;
      margin-bottom: 10px;

    }

    .btnLogin, .btnBack{
      margin-bottom: 20px;
      cursor:pointer;
    }

    .login_nav{
      cursor: pointer;
    }

		.box-title{
			font-size: 20px;
		}
	</style>
</head>
<body data-base_url="<?=base_url();?>">
	<div class="page login-page">
		<div class="container d-flex align-items-center">
      <div class="form-holder has-shadow">
        <div class="card">
          <div class="card-body">

            <div class="row" id = "nav_div">
              <div class="col-lg-12">
                <div class="info d-flex align-items-center">

    							<div class="content">
    								<div class="logo" style="width:250px;">
    									<img class="img-fluid" src="<?=base_url('assets/img/juanpayroll-logo-04.png');?>">
    									<!-- <h1>Dashboard</h1> -->
    								</div>
    							</div>
    						</div>
              </div>
							<div class="col-12 text-center">
								<h1>
									<?php if(isset($this->session->branch_name) && $this->session->branch_name != ""):?>
										<?=$this->session->branch_name?>
									<?php endif;?>
								</h1>
							</div>
              <div class="col-lg-4 col-md-3 col-12 mb-4 p-4 login_nav" id = "admin_tab">
                  <!-- <a href="<?=base_url('Main_page/display_page/');?>" class="w-100"> -->
                      <div class="p-5 card card-hover text-center home-card w-100">
                          <span><i class="fa fa-user-secret fa-3x text-white"></i></span>
                          <h6 class="box-title text-white mt-3 primary-bg m-0 py-2">Admin Portal</h6>
                      </div>
                  <!-- </a> -->
              </div>

              <div class="col-lg-4 col-md-3 col-12 mb-4 p-4 login_nav" id = "emp_tab">
                  <!-- <a href="<?=base_url('Main_page/display_page/');?>" class="w-100"> -->
                      <div class="p-5 card card-hover text-center home-card w-100">
                          <span><i class="fa fa-users fa-3x text-white"></i></span>
                          <h6 class="box-title text-white mt-3 primary-bg m-0 py-2">Employee Portal</h6>
                      </div>
                  <!-- </a> -->
              </div>

              <div class="col-lg-4 col-md-3 col-12 mb-4 p-4" >
                  <a href="<?=base_url('employees/Timelog/facial_recog_index');?>" class="w-100">
                      <div class="p-5 card card-hover text-center home-card w-100">
                          <span><i class="fa fa-camera fa-3x text-white"></i></span>
                          <h6 class="box-title text-white mt-3 primary-bg m-0 py-2">Clock In / Out</h6>
                      </div>
                  </a>
              </div>

              <!-- <div class="col-lg-3 col-md-3 col-12 mb-4 p-4" >
                  <a href="<?=base_url('employees/Timelog/facial_recog_index');?>" class="w-100">
                      <div class="p-5 card card-hover text-center home-card w-100">
                          <span><i class="fa fa-video-camera fa-3x text-white"></i></span>
                          <h6 class="box-title text-white mt-3 primary-bg m-0 py-2">Facial Recognition</h6>
                      </div>
                  </a>
              </div> -->

            </div>
						<!-- ADMIN LOGIN -->
            <div class="row login_div" id = "adminlogin_div" style = "display:none;">
              <div class="col-lg-8 offset-lg-2 bg-white">
    						<div class="form d-flex align-items-center">
    							<div class="content">
    								<form id="adminlogin-form" method="post">
                      <div class="form-group text-center">
                        <i class="fa fa-user-secret fa-3x text-white"></i>
                        <h6>Admin Portal</h6>
                      </div>
    									<div class="form-group">
    										<input id="adminlogin-username" type="text" name="loginUsername" required="" class="input-material">
    										<label for="login-username" class="label-material active">Username</label>
    									</div>
    									<div class="form-group">
    										<input id="adminlogin-password" type="password" name="loginPassword" required="" class="input-material">
    										<label for="login-password" class="label-material active">Password</label>
    									</div>
											<!-- <div class="form-group">
												<small><a href=""><u>Forgot Password</u></a></small>
											</div> -->
    									<div class="form-group text-right">
												<small class = "float-left"><a id = "btn_forgot_pw_modal"><u>Forgot Password</u></a></small>
    										<button id="login_admin" href="#"  class="btn btn-primary btnLogin">Login</button>
                        <button type = "button" class="btn btn-blue-grey btnBack">Back</button>
    									</div>
    								</form>
    							</div>
    						</div>
    					</div>
            </div>
						<!-- EMPLOYEE LOGIN  -->
            <div class="row login_div" id = "emplogin_div" style = "display:none;">
              <div class="col-lg-8 offset-lg-2 bg-white">
    						<div class="form d-flex align-items-center">
    							<div class="content">
    								<form id="emplogin-form" method="post">
                      <div class="form-group text-center">
                        <i class="fa fa-users fa-3x text-white"></i>
                        <h6>Employee Portal</h6>
                      </div>
    									<div class="form-group">
    										<input id="login-username" type="text" name="loginUsername" required="" class="input-material">
    										<label for="login-username" class="label-material active">Username</label>
    									</div>
    									<div class="form-group">
    										<input id="login-password" type="password" name="loginPassword" required="" class="input-material">
    										<label for="login-password" class="label-material active">Password</label>
    									</div>
    									<div class="form-group text-right">
												<small class = "float-left"><a id = "btn_forgot_pw_modal"><u>Forgot Password</u></a></small>
    										<button id="login_emp" href="#"  class="btn btn-primary btnLogin">Login</button>
                        <button type = "button" class="btn btn-blue-grey btnBack">Back</button>
    									</div>
    								</form>
    							</div>
    						</div>
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

	<!-- FORGOT PASSWORD MODAL -->
	<div class="modal fade" id = "forgot_pw_modal">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title">Forgot Password</h4>
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
				<form action="" id="reset_passForm">
		      <div class="modal-body">
						<div class="row">
							<div class="col-12 mb-4">
								<h5>CAN'T SIGN IN? FORGOT YOUR PASSWORD?</h5>
							</div>
							<div class="col-12 mb-2">
								<p>Please enter your account`s email address below and click the "Reset My Password" button. You will receive an email that contains a link to set a new password.</p>
							</div>
							<div class="col-12 mb-2">
								<label for="Email Address" class="form-control-label col-form-label-sm">Email Address <span class="asterisk"></span></label>
								<input type="text" id = "forgot_pw_email" name = "forgot_pw_email" class="form-control" placeholder="Enter email">
							</div>
						</div>
		      </div>
		      <div class="modal-footer text-right">
						<button id = "btn_reset_pass" type = "submit" class="btn btn-primary form-control">RESET MY PASSWORD</button>
		      </div>
				</form>
	    </div>
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
<script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script>
<script src="<?=base_url('assets/js/notification.js');?>"></script>
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

<script src="<?=base_url('assets/js/login.js');?>"></script>
</body>
</html>
