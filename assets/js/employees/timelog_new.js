$(function(){
  $('#rf_idnumber').focus();
  var base_url = $('body').data('base_url');
  var sess_status = false;
  var isAdminLoggedIn = $('#isAdminLoggedIn').val();
  if(isAdminLoggedIn == false){
    // SUBMIT ADMIN AUTHENTICATION FORM
    $(document).on('submit', '#admin_auth_form', function(e){
      e.preventDefault();
      var error = 0;
      var errorMsg = "";
      var admin_auth_form = new FormData(this);

      $('.required').each(function(){
        if($(this).val() == ""){
          $(this).css("border", "1px solid #ef4131");
        }else{
          $(this).css("border", "1px solid gainsboro");
        }
      });

      $('.required').each(function(){
        if($(this).val() == ""){
          $(this).focus();
          error = 1;
          errorMsg = "Please fill up all required fields.";
          return false;
        }
      });

      if(error == 0){
        $.ajax({
          url: base_url+'employees/Timelog/auth_admin',
          type: 'post',
          data: admin_auth_form,
          processData: false,
          contentType: false,
          beforeSend: function(){
            $.LoadingOverlay('show');
            $('.btnLogin').prop('disabled',true);
          },
          success: function(data){
            $.LoadingOverlay('hide');
            $('#btn_submit').prop('disabled', false);
            if(data.success == 1){
              $('#admin_auth_modal').modal('hide');
              window.location.href = base_url+'employees/Timelog';
            }else{
              notificationError('Error', data.message);
            }
          }
        });
      }else{
        notificationError('Error', errorMsg);
      }
    });
    // CALL ADMIN AUTH MODAL
    $('#admin_auth_modal').modal({ backdrop: 'static', keyboard: false });
    return;
  }

  function check_sess_id(admin) {
    var sess_id = $('body').data('sess_id');
    // console.log(sess_id);
    $.ajax({
      url: base_url+'Main/check_sess_id',
      type: 'post',
      data:{sess_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          // console.log("true");
          admin(data.status);
        }else{
          // console.log("false");
          admin(data.status);
        }
      }
    });
  }

  var base_url = $("body").data('base_url');
  var location = {
    lat: "",
    lng: ""
  }

  // FUNCTIONS
  var xmlHttp;
  function srvTime(){
      try {
          //FF, Opera, Safari, Chrome
          xmlHttp = new XMLHttpRequest();
      }
      catch (err1) {
          //IE
          try {
              xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
          }
          catch (err2) {
              try {
                  xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
              }
              catch (eerr3) {
                  //AJAX not supported, use CPU time.
                  alert("AJAX not supported");
              }
          }
      }
      xmlHttp.open('HEAD',window.location.href.toString(),false);
      xmlHttp.setRequestHeader("Content-Type", "text/html");
      xmlHttp.send('');
      return xmlHttp.getResponseHeader("Date");
  }

  function showTime(stime){
      var st = srvTime();
      var date = new Date(stime);
      var h = date.getHours(); // 0 - 23
      var m = date.getMinutes(); // 0 - 59
      var s = date.getSeconds(); // 0 - 59

      h = (h < 10) ? "0" + h : h;
      m = (m < 10) ? "0" + m : m;
      s = (s < 10) ? "0" + s : s;

      var time = h + ":" + m + ":" + s;
      // document.getElementById("MyClockDisplay").innerText = time;
      // document.getElementById("MyClockDisplay").textContent = time;
      // $('#currentTime').val(time);
      // $('.emp_idno').focus();
      // setInterval(showTime, 1000);
      return time;
  }

  function errorHandler(err) {
    if(err.code == 1) {
       console.log("Error: Access is denied!");
    }
    else if( err.code == 2) {
       console.log("Error: Position is unavailable!");
    }
  }

  function get_timezone(lat,lng){
    var timezone = 'Asia/Manila';
    var url = `https://maps.googleapis.com/maps/api/timezone/json?location=${lat},${lng}&timestamp=1458000000&key=AIzaSyCp8esu5bFCZDsr9jzWMW-ZxpgeyywXHVM`;
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      async: false,
      success: function(data){
        timezone = data.timeZoneId;
      }
    });

    return timezone;
  }

  function get_location(callback){
    check_sess_id((admin) => {
      if(admin == false){
        $.LoadingOverlay('show');
        if (navigator.geolocation) {
          var options = {maximumAge: 600000,timeout:60000};
          navigator.geolocation.getCurrentPosition(function(position,errorHandler,options) {
            // console.log(position);
            if(position.coords.accuracy < 1000){
              location.lat = position.coords.latitude;
              location.lng = position.coords.longitude;

              var getlatitude = position.coords.latitude;
              var getlongitude = position.coords.longitude;
              var map = new google.maps.Map(document.getElementById("mapid"),{
                zoom: 15,
                center: location,
                gestureHandling: 'cooperative'
              });

              var geocoder = new google.maps.Geocoder;
              var infowindow = new google.maps.InfoWindow;
              geocodelatlang(geocoder,map,infowindow,getlatitude,getlongitude);

              $.LoadingOverlay('hide');
              callback(true,getlatitude,getlongitude);
            }else{
              $.LoadingOverlay('hide');
              // console.log('Error', 'Unable to locate accurate location. Please turn on the location on your pc or cellphone and try again. Thank you.');
              console.log(position.coords.accuracy);
              notificationError('Error', 'Unable to locate accurate location. <u>Please turn on the location on your pc or cellphone and try again</u>. Thank you.');
              callback(false);
            }
          }, function() {
            $.LoadingOverlay('hide');
            // console.log('Error', 'Unable to get user current location. Please try again.');
            notificationError('Error', 'Unable to get user current location. Please try again.');
            callback(false)
          }, {enableHighAccuracy: true});
        } else {
          $.LoadingOverlay('hide');
          console.log('Error', 'Geolocation not available. Please allow access for your location.');
          notificationError('Error', 'Geolocation not available. Please allow access for your location.');
          callback(false)
        }
      }else{
        callback(true);
      }
    });
  }

  function flipClock(){
    // FLIP CLOCK
    var server_time = srvTime();
    var date = new Date(server_time);
    var clock;

    clock = $('.clock').FlipClock(date, {
      clockFace: 'TwentyFourHourClock'
    });
    // console.log(clock);
    // console.log(clock.face.factory.time);
    // console.log(clock.face.factory.getTime());
    return clock.face.factory.getTime().time;
  }

  function submit_datelapse(url,data){
    $.ajax({
      url: base_url+url,
      type: 'post',
      data: data,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          check_sess_id((admin) => {
            if(admin == true){
              $('#mapdetails').html(data.location);
            }
          });
          // obj.play();
          $('.input-box').val("");
          $('#modalTitle').html(data.mode);
          $('#modalMessage').html(data.message);
          $('#logModal').modal('toggle');
          setTimeout(() => {$('#logModal').modal('hide')},4000);
        }else{
          notificationError('Error', data.message);
          $('.emp_idno').val('');
        }
      }
    });
  }

  function qr_scanner(){
    let scanner = new Instascan.Scanner({ video: document.getElementById('camera-stream') });
    scanner.addListener('scan', function (content) {
      var qr_audio = new Audio(base_url+'assets/sound/beep-2.mp3');
      qr_audio.play();
      get_location((status,lat,lng) => {
        if(status === true){
          var timezone = get_timezone(lat,lng);
          var time = flipClock();
          console.log(timezone);
          // var getTime = $('#currentTime').val();
          var getTime = showTime(time);
          var empId = content;
          var location2 = JSON.stringify(location);
          // console.log(location2);

          var blob = takeSnapshot();
          // console.log(blob);

          var formData = new FormData();
          formData.append('picture', blob);
          formData.append('timeIn', getTime);
          formData.append('empId', empId);
          formData.append('getlocation', location2);
          formData.append('timezone', timezone);

          $.ajax({
            url: base_url + 'employees/Timelog/add',
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function(){
              $.LoadingOverlay('show');
              $('#capturebtn').attr('disabled','disabled');
            },
            success: function(data){
              $.LoadingOverlay('hide');
              $('#capturebtn').removeAttr('disabled');

              switch (data.success) {
                case 1:
                  // console.log(data.location);
                  check_sess_id((admin) => {
                    if(admin == true){
                      $('#mapdetails').html(data.location);
                    }
                  });
                  // obj.play();
                  $('.input-box').val("");
                  Swal.fire(
                    data.mode,
                    data.message,
                    'success'
                  );
                  // $('#modalTitle').html(data.mode);
                  // $('#modalMessage').html(data.message);
                  // $('#logModal').modal('toggle');
                  // setTimeout(() => {$('#logModal').modal('hide')},4000);
                  break;
                case 2:
                  Swal.fire({
                    title: 'Oops! It looks like you don\'t have a timeout from your last timein. Will this be your ? ',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Time In ',
                    cancelButtonText: 'Time Out'
                  }).then((result) => {
                    if (result.value) {
                      // console.log("time_in");
                      data.status = "time_in";
                      const url = 'employees/Timelog/add_date_lapse';
                      submit_datelapse(url,data);
                    }else{
                      data.status = "time_out";
                      const url = 'employees/Timelog/add_date_lapse';
                      submit_datelapse(url,data);
                    }
                  });
                  break;
                default:
                  notificationError('Error', data.message);
                  $('.emp_idno').val('');

              }
              // if(data.success == 1){
              //   // console.log(data.location);
              //   check_sess_id((admin) => {
              //     if(admin == true){
              //       $('#mapdetails').html(data.location);
              //     }
              //   });
              //   // obj.play();
              //   $('.input-box').val("");
              //   Swal.fire(
              //     data.mode,
              //     data.message,
              //     'success'
              //   );
              //   // $('#modalTitle').html(data.mode);
              //   // $('#modalMessage').html(data.message);
              //   // $('#logModal').modal('toggle');
              //   // setTimeout(() => {$('#logModal').modal('hide')},4000);
              // }else{
              //   notificationError('Error', data.message);
              //   $('.emp_idno').val('');
              // }
            }
          })
        }else{
          $('.emp_idno').val('');
        }
      });

      // console.log(content);
    });
    Instascan.Camera.getCameras().then(function (cameras) {
      if (cameras.length > 0) {
        scanner.start(cameras[0]);
      } else {
        console.log('No cameras found.');
      }
    }).catch(function (e) {
      console.log(e);
    });
  }

  flipClock();

  qr_scanner();

  // TAKE SNAPSHOT
  $(document).on('click', '#capturebtn', function(){
    get_location((status,lat,lng) => {
      if(status === true){
        var timezone = get_timezone(lat,lng);
        var time = flipClock();

        // var getTime = $('#currentTime').val();
        var getTime = showTime(time);
        var empId = $('.input-box').val();
        var location2 = JSON.stringify(location);
        // console.log(location2);

        var blob = takeSnapshot();
        // console.log(blob);

        var formData = new FormData();
        formData.append('picture', blob);
        formData.append('timeIn', getTime);
        formData.append('empId', empId);
        formData.append('getlocation', location2);
        formData.append('timezone',timezone);

        $.ajax({
          url: base_url + 'employees/Timelog/add',
          type: 'post',
          data: formData,
          contentType: false,
          processData: false,
          beforeSend: function(){
            $.LoadingOverlay('show');
            $('#capturebtn').attr('disabled','disabled');
          },
          success: function(data){
            $.LoadingOverlay('hide');
            $('#capturebtn').removeAttr('disabled');
            if(data.success == 1){
              // console.log(data.location);
              check_sess_id((admin) => {
                if(admin == true){
                  $('#mapdetails').html(data.location);
                }
              });
              // obj.play();
              $('.input-box').val("");
              $('#modalTitle').html(data.mode);
              $('#modalMessage').html(data.message);
              $('#logModal').modal('toggle');
              setTimeout(() => {$('#logModal').modal('hide')},4000);
            }else{
              notificationError('Error', data.message);
              $('.emp_idno').val('');
            }
          }
        })
      }else{
        $('.emp_idno').val('');
      }
    });
  });
  // RF NUMBER
  $(document).on('keyup', '.emp_idno', function(e){
    if(e.keyCode === 13){
      $('#capturebtn').click();
    }
  });
  // QR INPUT
  $(document).on('keyup', '#qr_input', function(e){
    if(e.keyCode === 13){
      get_location((status,lat,lng) => {
        if(status === true){
          var timezone = get_timezone(lat,lng);
          var time = flipClock();

          // var getTime = $('#currentTime').val();
          var getTime = showTime(time);
          var empId = $('#qr_input').val();
          var location2 = JSON.stringify(location);
          // console.log(location2);

          var blob = takeSnapshot();
          // console.log(blob);

          var formData = new FormData();
          formData.append('picture', blob);
          formData.append('timeIn', getTime);
          formData.append('empId', empId);
          formData.append('getlocation', location2);
          formData.append('timezone',timezone);

          $.ajax({
            url: base_url + 'employees/Timelog/add',
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function(){
              $.LoadingOverlay('show');
              $('#capturebtn').attr('disabled','disabled');
            },
            success: function(data){
              $.LoadingOverlay('hide');
              $('#capturebtn').removeAttr('disabled');

              switch (data.success) {
                case 1:
                  check_sess_id((admin) => {
                    if(admin == true){
                      $('#mapdetails').html(data.location);
                    }
                  });
                  // obj.play();
                  $('.input-box').val("");
                  $('#modalTitle').html(data.mode);
                  $('#modalMessage').html(data.message);
                  $('#logModal').modal('toggle');
                  setTimeout(() => {$('#logModal').modal('hide')},4000);
                  break;
                case 2:
                  Swal.fire({
                    title: 'Oops! It looks like you don\'t have a timeout from your last timein. Will this be you\'re ? ',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Time In ',
                    cancelButtonText: 'Time Out'
                  }).then((result) => {
                    if (result.value) {
                      // console.log("time_in");
                      data.status = "time_in";
                      const url = 'employees/Timelog/add_date_lapse';
                      submit_datelapse(url,data);
                    }else{
                      data.status = "time_out";
                      const url = 'employees/Timelog/add_date_lapse';
                      submit_datelapse(url,data);
                    }
                  });
                  break;
                default:
                  notificationError('Error', data.message);
                  $('.emp_idno').val('');
              }

              // if(data.success == 1){
              //   // console.log(data.location);
              //   check_sess_id((admin) => {
              //     if(admin == true){
              //       $('#mapdetails').html(data.location);
              //     }
              //   });
              //   // obj.play();
              //   $('.input-box').val("");
              //   $('#modalTitle').html(data.mode);
              //   $('#modalMessage').html(data.message);
              //   $('#logModal').modal('toggle');
              //   setTimeout(() => {$('#logModal').modal('hide')},4000);
              // }else{
              //   notificationError('Error', data.message);
              //   $('.emp_idno').val('');
              // }
            }
          })
        }else{
          $('.emp_idno').val('');
        }
      });
    }
  });
  // LOCATION
  $(document).on('click', '#btn_location', function(){
    if($('#getaddress').val() != ''){
      $('#location_modal').modal('show');
    }else{
      $('#location_modal').modal('show');
      initMap();
    }
    // $('#location_modal').modal('show');
  });
  // REDIRECT
  $(document).on('click', '#btn_redirect', function(){
    window.location.href = base_url + 'employees/Timelog/redirect_to_facial_recog';
  });
  // RELOAD
  $(document).on('click', '#btn_reload', function(){
    window.location.reload(true);
  });
  // GO HOME
  $(document).on('click', '#btn_home', function(){
    window.location.href = base_url + 'main/logout';
  });
  // REGISTER RF ID MODAL
  $(document).on('click', '#btn_rf', function(){
    $('#rf_modal').modal();

    $('#btn_scan').click(function(){
      $('#reg_employee_idno').css('border', '1px solid gainsboro');
      if(!$('#reg_employee_idno').val() == ""){
        $(this).hide();
        $('.rf_wrapper').show();
        $('#reg_rf_idnumber').val('');
        $('#reg_rf_idnumber').focus();
      }else{
        notificationError('Error', 'Please enter your employee id number');
        $('#reg_employee_idno').css('border', '1px solid #ef4131');
        // $('#reg_employee_idno').focus();
      }
    });

    $('#reg_rf_idnumber').keyup(function(e){
      if(e.keyCode === 13){
        $('#btn_reg_rfid').click();
      }
    })
  });
  // REGISTER RF ID
  $(document).on('click', '#btn_reg_rfid', function(){
    var reg_employee_idno = $('#reg_employee_idno').val();
    var reg_rf_idnumber = $('#reg_rf_idnumber').val();
    var thiss = $(this);

    if(reg_employee_idno == "" || reg_rf_idnumber == ""){
      notificationError('Error', 'Please fill up all required fields');
      return false;
    }

    $.ajax({
      url: base_url+'employees/Timelog/register_rfid',
      type: 'post',
      data:{ reg_employee_idno, reg_rf_idnumber},
      beforeSend: function(){
        $.LoadingOverlay('show');
        thiss.prop('disabled',true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        thiss.prop('disabled', false);
        if(data.success == 1){
          $('#rf_modal').modal('hide');
          notificationSuccess('Success', data.message);
        }else if(data.success == 2){
          $('#confirm_rfid').val(reg_rf_idnumber);
          $('#confirm_empid').val(reg_employee_idno);
          $('#rf_modal').modal('hide');
          $('#confirm_msg').text(data.message);
          $('#confirm_modal').modal();
        }else{
          $('#reg_employee_idno').val('');
          $('.rf_wrapper').hide();
          $('#btn_scan').show();

          notificationError('Error', data.message);
        }
      }
    });
  });
  // LOGIN USING RF ID
  $(document).on('keyup', '#rf_idnumber', function(e){
    if(e.keyCode === 13){
      var rfid = $(this).val();
      get_location((status,lat,lng) => {
        if(status === true){
          var timezone = get_timezone(lat,lng);
          var time = flipClock();
          var getTime = showTime(time);
          var location2 = JSON.stringify(location);

          var blob = takeSnapshot();
          // console.log(blob);

          var formData = new FormData();
          formData.append('picture', blob);
          formData.append('timeIn', getTime);
          formData.append('rf_number', rfid);
          formData.append('getlocation', location2);
          formData.append('timezone', timezone);

          $.ajax({
            url: base_url + 'employees/Timelog/add_using_rfid',
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function(){
              $.LoadingOverlay('show');
            },
            success: function(data){
              $.LoadingOverlay('hide');
              if(data.success == 1){
                // obj.play();
                check_sess_id((admin) => {
                  if(admin == true){
                    $('#mapdetails').html(data.location);
                  }
                });
                $('#rf_idnumber').val("");
                $('#modalTitle').html(data.mode);
                $('#modalMessage').html(data.message);
                $('#logModal').modal('toggle');
                setTimeout(() => {$('#logModal').modal('hide')},4000);
                // $("#getloginstatus").val(data.loginstatus);
              }else{
                notificationError('Error', data.message);
                $('#rf_idnumber').val('');
              }
            }
          })
        }else{
          $('#rf_idnumber').val('');
        }
      });
    }
  });
  // UPDATE RF NUMBER
  $(document).on('click', '#btn_yes', function(){
    var rf_number = $('#confirm_rfid').val();
    var emp_idno = $('#confirm_empid').val();
    // console.log(rf_number);540015c48d


    $.ajax({
      url: base_url+'employees/Timelog/update_rfid',
      type: 'post',
      data:{rf_number, emp_idno},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#confirm_modal').modal('hide');
        if(data.success == 1){
          notificationSuccess('Success',data.message);
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });
  // LOGIN ADMIN MODAL
  $(document).on('click', '#btn_login_admin', function(){
    $('#login_admin_modal').modal();
  });
  // LOGIN ADMIN
  $(document).on('submit', '#adminlogin-form', function(e){
		e.preventDefault();

		$.ajax({
		  url: base_url+'Main/login',
		  type: 'post',
		  data:{
				loginUsername: $('#adminlogin-username').val(),
				loginPassword: $('#adminlogin-password').val(),
				login_type: 'admin'
			},
		  beforeSend: function(){
		    $.LoadingOverlay('show');
				$('.btnLogin').prop('disabled',true);
		  },
		  success: function(data){
		    $.LoadingOverlay('hide');
		    if(data.success == 1){
          $('#login_admin_modal').modal('hide');
					var userData = data.userData;
					var token = data.token_session;
          notificationSuccess('Success',data.message);
					setTimeout(() => {window.location.reload()},2000);
		    }else{
					$('.btnLogin').prop('disabled',false);
					notificationError('Error',data.message);
		    }
		  }
		});
	});
  // MODAL CLOSE
  $('#logModal').on('hidden.bs.modal', function(){
    // window.location.reload(true);
    var div = $('.btn-nav.active').data('nav');
    $('#rf_idnumber').val('');
    $('.emp_idno').val('');
    $('#qr_input').val('');
    (div == 'divScanRf') ? $('#rf_idnumber').focus() : $('#qr_input').focus();
    // $('#rf_idnumber').focus();
  });
  // RF MODAL CLOSE
  $('#rf_modal').on('hidden.bs.modal', function(){
    $('#reg_employee_idno').val('');
    $('#reg_rf_idnumber').val('');
    $('#btn_scan').show();
    $('.rf_wrapper').hide();
    $('#rf_idnumber').focus();
  });
  // CONFIRM MODAL CLOSE
  $('#confirm_modal').on('hidden.bs.modal', function(){
    $('#confirm_rfid').val('');
    $('#rf_idnumber').focus();
  });
  // MAP MODAL
  $('#location_modal').on('hidden.bs.modal', function(){
    $('#rf_idnumber').val('');
    $('.emp_idno').val('');
    $('#qr_input').val('');
    $('#rf_idnumber').focus();
  });
  // TIMELOG NAV
  $('.btn-nav').click(function(){
    var div = $(this).data('nav');
    $('.btn-nav').removeClass('active');
    $('.nav-div').hide('slow');
    $(this).addClass('active');
    $(`#${div}`).show('slow');
    (div == 'divScanRf') ? $('#rf_idnumber').focus() : $('#qr_input').focus();

  });

});
