<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="<?=base_url('assets/css/bootstrap.min.css');?>">
		<link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">
		<link rel="stylesheet" href="<?= base_url('assets/css/camera.css') ?>">
		<style>


		</style>

	</head>
	<body data-base_url="<?= base_url() ?>">
      <a href="images" id="download-photo" style="top:58%; left:32%; position:absolute; color:#fff;" download="selfie.png" title="Save Photo" class="disabled"><i class="material-icons"></i></a>
		<div class="row">
			<div class="col-md-3">
				<input type="hidden" id="currentTime">
			</div>
			<div class="col-md-6">
				<div id="MyClockDisplay" class="clock"></div>
				<!-- <img src="<?= base_url('assets/img/avatar.jpg') ?>" class="avatar"> -->
				<video id="camera-stream" class="avatar" muted="muted"></video>
				<img id="snap">
				<input type="text" class="form-control input-box" placeholder="Employee No#">

				<!-- Modal -->
				<div id="logModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">

				    <!-- Modal content-->
				    <div class="modal-content">
				      <div class="modal-header">
				        <h4 class="modal-title"><span id="modalTitle"></span></h4>
				      </div>
				      <div class="modal-body">
				        <span id="modalMessage"></span>
				        <br><br>
				        <center>
				        	<canvas id = 'mycanvas'></canvas>
				        </center>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				      </div>
				    </div>

				  </div>
				</div>

			</div>
			<div class="col-md-3">

			</div>
		</div>
	</body>
		<script src="<?= base_url('assets/js/jquery.min.js');?>"></script>
		<script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
		<script src="<?= base_url('assets/js/employees/timelog.js') ?>"></script>
		<script src="<?= base_url('assets/js/camera.js') ?>"></script>
</html>
