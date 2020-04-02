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
	<!-- Tweaks for older IEs--><!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
	<style type="text/css">
		input.input-material ~ label.active {
		    font-size: 0.8em;
		    top: -17px;
		    color: #2b90d9;
		}
	</style>
</head>
<body data-base_url="<?=base_url();?>">
	<div class="page login-page">
		<div class="container d-flex align-items-center">
			<div class="form-holder has-shadow">
				<div class="row">
					<!-- Logo & Information Panel-->
					<div class="col-lg-6">

						<div class="info d-flex align-items-center">

							<div class="content">
								<div class="logo" style="">
									<img class="img-fluid" src="<?=base_url('assets/img/pandabooks.png');?>">
									<!-- <h1>Dashboard</h1> -->
								</div>
								<!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p> -->
							</div>
						</div>
					</div>
					<!-- Form Panel    -->
					<div class="col-lg-6 bg-white">
						<div class="form d-flex align-items-center">
							<div class="content">
								<form id="login-form" method="post">

									<div class="form-group">
										<input id="login-username" type="text" name="loginUsername" required="" class="input-material">
										<label for="login-username" class="label-material active">Username</label>
									</div>
									<div class="form-group">
										<input id="login-password" type="password" name="loginPassword" required="" class="input-material">
										<label for="login-password" class="label-material active">Password</label>
									</div>
									<div class="form-group text-right">
										<button id="login" href="#"  class="btn btn-primary btnLogin">Login</button>
										<a href ="<?=base_url('employees/Timelog');?>"><button  id="login" type = "button" class="btn btn-success"><i class = "fa fa-camera"></i>&nbsp;Timelog</button></a>
									</div>
									<!-- This should be submit button but I replaced it with <a> for demo purposes-->
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
