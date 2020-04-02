$(function(){
  $('#rf_idnumber').focus();
  var base_url = $("body").data('base_url');
  var location = {
    lat: "",
    lng: ""
  }
  var rf_id_const = 0;

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

  function get_location(callback){
    $.LoadingOverlay('show');
    if (navigator.geolocation) {
        var options = {maximumAge: 600000,timeout:60000};
        navigator.geolocation.getCurrentPosition(function(position,errorHandler,options) {
          console.log(position);
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
            callback(true);
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

  flipClock();

  // $('#refreshbtn').click(function(){
	// 	window.location.reload(true);
	// });

  // TAKE SNAPSHOT
  $(document).on('click', '#capturebtn', function(){
    get_location((status) => {
      if(status === true){
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
              $('.input-box').val("");
							$('#modalTitle').html(data.mode);
							$('#modalMessage').html(data.message);
              $('#logModal').modal('toggle');
							// $("#getloginstatus").val(data.loginstatus);
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
  // LOCATION
  $(document).on('click', '#btn_location', function(){
    $('#location_modal').modal('show');
  });
  // RELOAD
  $(document).on('click', '#btn_reload', function(){
    window.location.reload(true);
  });
  // GO HOME
  $(document).on('click', '#btn_home', function(){
    window.location.href = base_url;
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
      },
      success: function(data){
        $.LoadingOverlay('hide');
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
      // $('#rf_idnumber').val("");
      var rfid = $(this).val();
      get_location((status) => {
        if(status === true){

          var time = flipClock();
          // var getTime = $('#currentTime').val();
          var getTime = showTime(time);
          var location2 = JSON.stringify(location);
          console.log(location2);
          // return false;
          // console.log(location2);

          var blob = takeSnapshot();
          // console.log(blob);

          var formData = new FormData();
          formData.append('picture', blob);
          formData.append('timeIn', getTime);
          formData.append('rf_number', rfid);
          formData.append('getlocation', location2);

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
                $('#rf_idnumber').val("");
  							$('#modalTitle').html(data.mode);
  							$('#modalMessage').html(data.message);
                $('#logModal').modal('toggle');
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
    console.log(rf_number);

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
  // MODAL CLOSE
  $('#logModal').on('hidden.bs.modal', function(){
    // window.location.reload(true);
    $('#rf_idnumber').val('');
    $('.emp_idno').val('');
    $('#rf_idnumber').focus();
  });
  // RF MODAL CLOSE
  $('#rf_modal').on('hidden.bs.modal', function(){
    $('#reg_employee_idno').val('');
    $('#reg_rf_idnumber').val('');
    $('#btn_scan').show();
    $('.rf_wrapper').hide();
    $('#rf_idnumber').focus();
  });
  //CONFIRM MODAL CLOSE
  $('#confirm_modal').on('hidden.bs.modal', function(){
    $('#confirm_rfid').val('');
    $('#rf_idnumber').focus();
  })

});
