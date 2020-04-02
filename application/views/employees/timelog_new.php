<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=company_name();?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
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
    <link rel="stylesheet" href="<?=base_url('assets\css\MonthPicker.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/flipclock/compiled/flipclock.css')?>">
    <link rel="stylesheet" href="<?=base_url('assets\css\notification\sweetalert2.min.css')?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/timelog.css')?>">
    <link rel="stylesheet" href="<?=base_url('assets\css\css_loader\css-loader.css')?>">
    <!-- Time Picker -->
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/jquery.timepicker.min.css')?>"> -->
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>

<body data-base_url = "<?=base_url()?>" data-sess_id = "<?=en_dec('en',$this->session->user_id)?>">
  <!-- <div class="loader loader-default is-active" data-text = "Logging In" data-blink></div> -->
  <input type="hidden" id = "getaddress">
  <input type = "hidden" id = "getworksite">
  <!-- <input type="" id = "getloginstatus"> -->
  <input type="hidden" id="currentTime">
  <input type = "hidden" id = "confirmlocation">
  <input type="hidden" id = "isAdminLoggedIn" value = "<?=(isset($_SESSION['database_name']) && $_SESSION['database_name'] == true) ? $_SESSION['database_name'] : false?>">

  <div class="container">
    <div id = "time_wrapper" class="row">
      <div class="col-12 text-center">
        <div class="clock" style = "display:inline-block;width: auto;"></div>
      </div>

      <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
        <div  class="img-thumbnail form-control">

          <!-- VIDEO WRAPPER -->
          <div class="col-12 p-0 m-0" id = "video_wrapper">
            <video id="camera-stream" class="avatar" muted="muted" playsinline autoplay></video>
          </div>
          <!-- BRANCH NAME -->
          <?php if(isset($_SESSION['branch_name']) && $_SESSION['branch_name'] != ''):?>
            <!-- <small style = "position:absolute;right:2px;bottom:0;"></small> -->
            <div class="col-12 text-dark p-1 pt-2 text-center mb-2" style = "background-color: #222;color: #fff !important;">
              <h5><?=$_SESSION['branch_name']?> QR Code Scanner</h5>
              <h6>( Please place your qr code infront of the camera. )</h6>
            </div>
          <?php endif;?>
          <!-- NAVIGATION -->
          <!-- <div class="form-group row">
            <div class="col-6">
              <button data-nav = "divEnterId" class="form-control btn-nav active">Enter Employee ID</button>
            </div>
            <div class="col-6">
              <button data-nav = "divScanRf" class="form-control btn-nav">Scan RF Id</button>
            </div>
          </div> -->
          <!-- SCAN RF ID -->
          <div id="divScanRf" class = "nav-div form-group row" style = "display:none;">
            <div class="col-12 text-center">
              <div id="rf_wrapper">
                <small class = "text-left">Tap your ID</small>
                <img src="<?=base_url('assets/gif/rf_scan3.gif')?>" alt="" class = "px-4">
                <small class = "text-right">Tap your ID</small>
              </div>
            </div>
            <div class="qr_code" style = "width: 0px;overflow:hidden;">
              <input type="text" id = "rf_idnumber">
            </div>
          </div>
          <!-- ENTER ID NUMBER -->
          <div id = "divEnterId" class="nav-div form-group row">
            <div class="col-9 col-lg-10">
              <input type="text" class="form-control input-box emp_idno" id="inputPassword2" placeholder="Employee Id Number">
            </div>
            <label for="" class="col-3 col-lg-2 col-form-label p-0">
              <button class="btn btn-camera2" id = "capturebtn"><i class="fa fa-camera"></i></button>
            </label>
            <div class="qr_code2" style = "width: 0px;overflow:hidden;">
              <input type="text" id = "qr_input" readonly>
            </div>
          </div>

        </div>
      </div>
      <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
        <div class="card">
          <div class="card-footer text-center" style = "border-top:0;width:">
            <?php if(!$this->session->isLoggedIn):?>
              <button class="btn btn-log-2" id = "btn_home"><i class="fa fa-home mr-1"></i>Home</button>
              <button class="btn btn-log-2" id = "btn_redirect"><i class="fa fa-camera mr-1"></i>Facial Recog</button>
              <button class="btn btn-log-2" id = "btn_reload"><i class="fa fa-refresh mr-1"></i>Refresh</button>
              <!-- <button class="btn btn-camera" id = "btn_home"><i class="fa fa-home"></i></button> -->
              <!-- <button class="btn btn-camera" id="btn_login_admin"><i class="fa fa-user-secret"></i></button> -->
            <?php endif;?>
            <!-- <button class="btn btn-camera" id = "btn_redirect"><i class="fa fa-video-camera"></i></button> -->
            <!-- <button class="btn btn-camera" id = "btn_reload"><i class="fa fa-refresh"></i></button> -->
            <?php if($this->session->isLoggedIn):?>
              <a href="<?=base_url('Main/logout')?>" style = "text-decoration:none;color:#222;" class="btn btn-camera"><i class="fa fa-sign-out"></i></a>
            <?php endif;?>

            <!-- <button class="btn btn-camera" id = "capturebtn"><i class="fa fa-camera"></i></button> -->
            <!-- <button class="btn btn-camera" id = "btn_rf"><i class="fa fa-id-card-o"></i></button> -->
          </div>
        </div>
      </div>
    </div>
    <br>
    <br>
    <br>
    <br>
  </div>

  <!-- MAP MODAL -->
  <div class="modal fade" id = "location_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Your Current Location</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div id = "mapid"></div>
        </div>
        <div class="modal-footer text-right">
          <!-- <button class="btn btn-sm btn-primary">Save</button> -->
          <button class="btn blue-grey" data-dismiss = "modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- LOG MODAL -->
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
          <div class="form-group row">
            <div class="col-12">
              <div class="img-thumbnail form-control">
                <canvas id = 'mycanvas'></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  <!-- RF MODAL -->
  <div class="modal fade" id = "rf_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Register Rf IdNumber</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <div class="col-12">
                <i class="fa fa-id-card-o" id = "rf_logo"></i>
              </div>
              <div class="col-12">
                <button class="btn btn-primary" id = "btn_scan">Scan</button>
                <div class="rf_wrapper" style = "display:none;">
                  <div class="qr_code" style = "width: 0px;overflow:hidden;">
                    <input type="text" id = "reg_rf_idnumber">
                  </div>
                  <small class = 'd-block'>Waiting for Rf Scan</small>
                  <img src="<?=base_url('assets/img/loader3.gif')?>" alt="" style = "width:100px;height:40px;">
                </div>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <label for="Employee Id" class="form-control-label col-form-label-sm">Employee Id <span class="asterisk"></span></label>
              <input type="text" id = "reg_employee_idno" class="form-control" placeholder="Enter Employee Id number">
            </div>
          </div>
        </div>
        <div class="modal-footer text-right">
          <!-- <button class="btn btn-primary">Scan Rf ID</button> -->
          <button class="btn btn-sm btn-primary" id = "btn_reg_rfid">Save</button>
          <button class="btn blue-grey" data-dismiss = "modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- CONFIRM MODAL -->
  <div class="modal fade" id = "confirm_modal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Oops!</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <input type="hidden" id = "confirm_rfid">
          <input type="hidden" id = "confirm_empid">
        </div>
        <div class="modal-body">
          <h4 id = "confirm_msg"></h4>
        </div>
        <div class="modal-footer text-right">
          <button class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
          <button class="btn blue-grey" data-dismiss = "modal">No</button>
        </div>
      </div>
    </div>
  </div>
  <!-- LOGIN AS ADMIN MODAL -->
  <div class="modal fade" id = "login_admin_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Login as Administrator</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form id="adminlogin-form">
          <div class="modal-body">
            <div class="form-group">
              <label for="Username: " class="form-control-label col-form-label-sm">Username: <span class="asterisk"></span></label>
              <input id="adminlogin-username" type="text" name="loginUsername" required="" class = "form-control">
            </div>
            <div class="form-group">
              <label for="Password" class="form-control-label col-form-label-sm">Password: <span class="asterisk"></span></label>
              <input id="adminlogin-password" type="password" name="loginPassword" required="" class = "form-control">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button type = "submit" class="btnLogin btn btn-sm btn-primary">Login</button>
            <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- ADMIN AUTHENTICA MODAL -->
  <div class="modal fade" id = "admin_auth_modal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Admin Authentication</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form id="admin_auth_form">
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-12 mb-2">
                <small class="form-text">Account Code <span class="asterisk"></span></small>
                <input type="text" id = "account_code" name = "account_code" class="form-control required" required>
              </div>
              <div class="col-md-12 mb-2">
                <small class="form-text">Username <span class="asterisk"></span></small>
                <input type="text" id = "username" name = "username" class="form-control required" required>
              </div>
              <div class="col-md-12 mb-2">
                <small class="form-text">Password <span class="asterisk"></span></small>
                <input type="password" id = "password" name = "password" class="form-control required" required>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button id = "btn_submit" type = "submit" class="btn btn-sm btn-primary">Submit</button>
            <a href="<?=base_url()?>" class = "btn blue-grey">Back</a>
            <!-- <button class="btn blue-grey" data-dismiss = "modal">Back</button> -->

          </div>
        </form>
      </div>
    </div>
  </div>
  <footer class="main-footer">
      <div class="container-fluid">
          <div class="row">
              <div class="col-sm-6">
                  <p><?php echo company_initial(); ?> | <?php echo company_name(); ?> &copy; <?php echo year_only(); ?></p>
              </div>
              <div class="col-sm-6">
                  <p><?=powered_by();?></p>
              </div>
          </div>
      </div>
  </footer>

  <!-- Javascript files-->
  <script src="<?=base_url('assets/js/jquery.min.js');?>"></script>
  <script src="<?=base_url('assets/js/jquery-ui.js');?>"></script>
  <script src="<?=base_url('assets/js/tether.min.js');?>"></script>
  <script src="<?=base_url('assets/js/bootstrap.min.js');?>"></script>
  <script src="<?=base_url('assets/js/mdb.min.js');?>"></script>
  <script src="<?=base_url('assets/js/jquery.cookie.js');?>"> </script>
  <script src="<?=base_url('assets/js/jquery.validate.min.js');?>"></script>
  <script src="<?=base_url('assets/js/bootstrap-datepicker.min.js');?>"></script>
  <!-- custom script for your overall script -->
  <!-- <script src="<?=base_url('assets/js/custom.js');?>"></script> -->
  <!-- <script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script> -->
  <script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>
  <script src="<?=base_url('assets/js/notification.js');?>"></script>
  <script src="<?=base_url('assets\js\marky\custom-loader.js');?>"></script>

  <!-- customs jc marky -->
  <script src="<?= base_url('assets/js/camera.js') ?>"></script>
  <script src = "<?=base_url('assets\js\qr_scanner\instascan.min.js')?>"></script>
  <script src = "<?=base_url('assets/js/employees/map.js') ?>"></script>
  <script src = "<?=base_url('assets/flipclock/compiled/flipclock.js')?>"></script>
  <script src="<?=base_url('assets\js\notification\sweetalert2.min.js');?>"></script>
  <script src="<?= base_url('assets/js/employees/timelog_new.js') ?>"></script>
  <script onload = "check_location()" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCp8esu5bFCZDsr9jzWMW-ZxpgeyywXHVM&libraries=places"
async defer></script>

</body>
