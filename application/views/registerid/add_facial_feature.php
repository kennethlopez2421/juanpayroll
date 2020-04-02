
<link rel="stylesheet" href="<?=base_url('assets/css/timelog.css')?>">
<link rel="stylesheet" href="<?=base_url('assets/css/custom_loader2.css')?>">
<div class="content-inner" id="pageActive" data-num="24" data-namecollapse="" data-labelname="Register Id">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/registerid_home/'.$token);?>">Register Id</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a class = "white-text" href="<?=base_url('registerid/Register_facial/index/'.$token)?>">Register Facial Features </a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Add Facial Recognition Data</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container">
        <div class="card">
          <div class="card-header">
            <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3 mb-2">
              <input type="text" id = "search_emp_fr" class="form-control" placeholder="Search Employee">
            </div>
            <div class="loader_wrapper" style = "display:none;">
              <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
                <div class="form-group row">
                  <div class="col-6 text-right p-0">
                    <h6>Searching ...</h6>
                  </div>
                  <div class="col-6 ">
                    <div class="loader-m"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="search_div">
              <!-- <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3 mb-2">
                <div class="card ">
                  <div class="card-header py-2">
                    <p class = "m-0">RPEX5580371 - Marky Mark Neri</p>
                  </div>
                </div>
              </div>

              <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3 mb-2">
                <div class="card ">
                  <div class="card-header py-2">
                    <p class = "m-0">RPEX5580371 - Marky Mark Neri</p>
                  </div>
                </div>
              </div> -->
            </div>
          </div>
          <div class="card-body">
            <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
              <div  class="img-thumbnail form-control">
                <!-- VIDEO WRAPPER -->
                <div class="col-12 p-0 m-0" id = "video_wrapper">
                  <video id="camera-stream" class="avatar" muted="muted" playsinline autoplay></video>
                </div>
                <!-- ENTER ID NUMBER -->
                <div id = "divEnterId" class="nav-div form-group row">
                  <div class="col-12 mt-2">
                    <input id = "employee_idno" type="text" class="form-control input-box" placeholder="Enter Employee ID">
                  </div>
                </div>

              </div>
            </div>
            <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
              <div class="card">
                <div class="card-footer text-center" style = "border-top:0;width:">
                  <button class="btn btn-camera" id = "btn_capture"><i class="fa fa-camera"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="logModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Registered Facial Recognition Data</h4>
          </div>
          <div class="modal-body">
            <!-- <span id="modalMessage"></span><br> -->
            <!-- <span id ="mapdetails" style="font-size:14px;"></span> -->
            <!-- <br><br> -->
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
<?php $this->load->view('includes/footer');?>
<script defer src = "<?=base_url('assets\faceapi\face-api.min.js')?>"></script>
<script src = "<?=base_url('assets\js\registerid\add_facial_feature.js')?>"></script>
