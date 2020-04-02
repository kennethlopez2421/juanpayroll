<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="<?=base_url('assets/css/bootstrap.min.css');?>">
		<link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">
		<link rel="stylesheet" href="<?= base_url('assets/css/camera.css') ?>">
  		<meta charset="UTF-8">
  		<meta name="description" content="Free Web tutorials">
  		<meta name="keywords" content="HTML,CSS,XML,JavaScript">
  		<meta name="author" content="John Doe">
  		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
  		<style type="text/css">
  			#mapid{
  				height:300px;
  				width: 100%;
  			}
  		</style>
	</head>
	<body data-base_url="<?= base_url() ?>">
			<input type="hidden" id = "getaddress">
			<input type = "hidden" id = "getworksite">
			<!-- <input type="" id = "getloginstatus"> -->
			<input type="hidden" id="currentTime">
			<input type = "hidden" id = "confirmlocation">
			<!-- <input type="hidden" id="error-message" value="error handler">			 -->
			<div>
				<a href="<?= base_url()?>"><button type="button" class="btn btn-link btn-lg">
						<p class = "text-success"><i class = "fa fa-home"></i><span class = "timelogbtns">&nbsp;Back to Login</span></p>
					</button>
				</a>
			</div>
				<div id="MyClockDisplay" class="clock"></div>
				<div class="holder">
					<video id="camera-stream"  class="avatar" muted="muted" playsinline autoplay></video>
					<img id="snap">
					  <div class="form-group row mb-2">
					    <input type="text" class="form-control input-box emp_idno" style="border-radius:20px; width: 300px; margin: 0 auto;" id="inputPassword2" placeholder="Enter your Employee ID:">
					  </div>
					  <div class="row">
					  	<div class = "btns">
						  	<button id = "capturebtn" class="btn btn-success mb-2 pull-left"><i class = "fa fa-camera"></i>&nbsp;<span class = "timelogbtns">Capture</span></button>&nbsp;&nbsp;
					      <button id = "refreshbtn" class="btn btn-danger mb-2 ml-2 pull-left"><i class = "fa fa-refresh"></i>&nbsp;<span class = "timelogbtns">Refresh</span></button>
								<button type="button" class="btn btn-info mb-2 pull-right" id = "locationbtn" data-target = "#mapmodal" data-toggle = "modal"><i class = "fa fa-map-marker"></i>&nbsp;</button>
							</div>
					  </div>
				</div>
				<div class = "beforefooter">
					<button type="button" class="btn btn-info" data-target = "#mapmodal" data-toggle = "modal">
						<i class = "fa fa-map-marker"></i>&nbsp;View your location
					</button>
				</div>
				<!-- Modal -->
				<div id="logModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <div class="modal-content">
				      <div class="modal-header">
				        <h4 class="modal-title"><span id="modalTitle"></span></h4>
				      </div>
				      <div class="modal-body">
				        <span id="modalMessage"></span><br>
				        <span id ="mapdetails" style="font-size:14px;"></span>
				        <br><br>
				        <center>
				        	<canvas id = 'mycanvas'></canvas>
				        </center>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>
				    </div>

				  </div>
				</div>

				<div class="modal fade" id="mapmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Your Current Location</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				        <div id = "mapid"></div>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				      </div>
				    </div>
				  </div>
				</div>
				<footer class="fixed-bottom" style="background-color:#313131;">
				  <!-- <div class = "container"> -->
				  	<div class="footer-copyright text-center py-3" style="color:#e2e2e2;">Powered by:
				  	<a href="http://www.cloudpanda.ph/cp/" style="color:#e2e2e2;"> <u>Cloud Panda PH</u></a>
				  	<!-- </div> -->
				 </div>
				</footer>
<!-- Footer -->
	</body>
		<script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
		<script src="<?= base_url('assets/js/camera.js') ?>"></script>
		<script src = "<?=base_url('assets/js/employees/map.js') ?>"></script>
		<script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script>
		<script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>
		<script src="<?=base_url('assets/js/jquery-code-scanner.js');?>"></script>
		<script src="<?=base_url('assets/js/notification.js');?>"></script>
		<script src="<?= base_url('assets/js/employees/timelog2.js') ?>"></script>
<script type="text/javascript">

</script>
		<script onload = "check_location()" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCp8esu5bFCZDsr9jzWMW-ZxpgeyywXHVM&libraries=places&callback=initMap"
async defer></script>
		<script src="<?=base_url('assets/js/moment.js');?>"></script>
</html>
