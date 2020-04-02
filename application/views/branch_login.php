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
	<link rel="shortcut icon" href="<?= base_url('assets/img/pandabookslogo.png'); ?>">
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
      font-size: 22px;
      margin-bottom: 10px;
			color: #72716f !important;

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

		.timelog_link{
			font-size: 11px;
			text-decoration: none;
			color:#222;
			text-decoration: underline;
		}
	</style>
</head>
<link rel="stylesheet" href="<?=base_url('assets/css/custom_checkbox.css')?>">
<body data-base_url="<?=base_url();?>">
	<div class="page login-page container-fluid">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card pb-5 mt-5">
					<form id = "branch_login_form">
	          <div class="card-body">
	            <div class="row">
	              <div class="col-12">
	                <div class="content pt-5 pb-3">
	                  <div class="logo" style="width:180px;margin:auto;">
	                    <img class="img-fluid" src="<?=base_url('assets/img/1payroll3.jpg');?>">
	                  </div>
	                </div>
	              </div>
	            </div>
	            <div class="row">
	              <div class="col-md-8 offset-md-2">
	                <label for="Company Code" class="form-control-label col-form-label-sm">Account Code <span class="asterisk"></span></label>
	                <input type="text" id = "company_code" name = "company_code" class="form-control rq">
	              </div>
	              <div class="col-md-8 offset-md-2">
	                <label for="Username" class="form-control-label col-form-label-sm">Username <span class="asterisk"></span></label>
	                <input type="text" id = "username" name = "username" class="form-control rq">
	              </div>
	              <div class="col-md-8 offset-md-2 mb-4">
	                <label for="Password" class="form-control-label col-form-label-sm">Password <span class="asterisk"></span></label>
	                <input type="password" id = "password" name = "password" class="form-control rq">
	              </div>
	              <div class="col-md-8 offset-md-2 text-right pb-4">
									<div class="row">
										<div class="col-md-6 p-0">
											<div class="col-12 text-left">
												<label class="container_label">
													Login in to admin portal
													<input type="checkbox" id = "login_type" name = "login_type" class = "content_nav" value = "admin">
													<span class="checkmark"></span>
												</label>
											</div>
											<div class="col-12 text-left text-dark">
												<a class = "timelog_link mr-2 mb-1 d-block" href="<?=base_url('employees/Timelog/facial_recog_index')?>">HRIS Facial Recognition</a>
												<a class = "timelog_link mr-2 mb-1 d-block" href="<?=base_url('employees/Timelog/')?>">HRIS Clock In / Out</a>
											</div>
										</div>
										<div class="col-md-6">
											<button id = "btn_login" type = "submit" class="btn btn-primary">Login</button>
										</div>
									</div>
	              </div>
	            </div>
	          </div>
					</form>
        </div>
      </div>

      <div class="col-12">

      </div>
    </div>

    <div class="form-holder">
      <div class="copyrights text-center" style = "background:transparent;">
  			<p>Powered by <a href="http://cloudpanda.cloudpanda.com.ph/" class="external">Cloud Panda PH</a></p>
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

<script src="<?=base_url('assets\js\branch_login.js');?>"></script>
</body>
</html>
