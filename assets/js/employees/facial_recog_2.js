$(function(){
  $('#rf_idnumber').focus();
  var base_url = $('body').data('base_url');
  var sess_status = false;
  var processing = false;
  var base = 1;
  let limit = 36;

  var base_url = $("body").data('base_url');
  var location = {
    lat: "",
    lng: ""
  }
  // var facial_recog = $('#fr').val();
  var facial_recog = function (){
		var facial_data = null;
    var isloggein = $('#isActivated').val();
    if(isloggein == 0){
      // SUBMIT ACCOUNT CODE FORM
      $(document).on('submit', '#activation_code_form', function(e){
        e.preventDefault();
        var error = 0;
        var errorMsg = "";
        var account_code_form = new FormData(this);

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
            url: base_url+'employees/Timelog/register_device',
            type: 'post',
            data: account_code_form,
            processData: false,
            contentType: false,
            beforeSend: function(){
              $.LoadingOverlay('show');
              $('#submit_code').attr('disabled', true);
            },
            success: function(data){
              $.LoadingOverlay('hide');
              $('#submit_code').prop('disabled', false);
              if(data.success == 1){
                $('#activation_code_modal').modal('hide');
                notificationSuccess('Success',data.message);
                setTimeout(() => { window.location.reload();},1500);
              }else{
                notificationError('Error', data.message);
              }
            }
          });
        }else{
          notificationError('Error', errorMsg);
        }
      });
      // CALL ACCOUNT CODE MODAL
      $('#activation_code_modal').modal({ backdrop: 'static', keyboard: false });
      return ;
    }

    // var code = $('#code').val();
    var test_url = 'https://cloudpandainc.com/hris_cp/'+code;
		$.ajax({
			url: base_url+'employees/Timelog/get_all_facial_recog',
			type: 'post',
      // data:{code},
			async: false,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success: function(data){
				$.LoadingOverlay('hide');
        if(data.success == 1){
          facial_data = data.facial_recog;
        }else{
          notificationError('Error',data.message);
        }
			}
		});

		return facial_data;
	}();
  var parse_fr = JSON.parse(facial_recog);

  // SCRIPT.js VARIABLES
  const video = document.getElementById('camera-stream')
  const v_wrapper = document.getElementById('video_wrapper');
  const constraint = { video: true, audio: false };

  Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceExpressionNet.loadFromUri(base_url+'assets/faceapi/models')
  ]).then(startVideo);

  // SCRIPT VARIABLES FUNCTIONS
  function successCallback(stream) {
    video.srcObject = stream;
    setInterval(() => {  video.play();}, 5000);
  }

  function errorCallback(error) {
    console.log("navigator.getUserMedia error: ", error);
  }

  function takeSnapshot(){
    // Here we're using a trick that involves a hidden canvas element.

    var hidden_canvas = document.getElementById('mycanvas');
    var context = hidden_canvas.getContext('2d');

    var width = video.videoWidth,
        height = video.videoHeight;

    if (width && height) {

      // Setup a canvas with the same dimensions as the video.
      hidden_canvas.width = width;
      hidden_canvas.height = height;

      // Make a copy of the current frame in the video on the canvas.
      context.drawImage(video, 0, 0, width, height);

      // Turn the canvas image into a dataURL that can be used as a src for our photo.
      image = hidden_canvas.toDataURL("image/png");
      var base64ImageContent = image.replace(/^data:image\/(png|jpg);base64,/, "");
      var blob = base64ToBlob(base64ImageContent, 'image/png');
      return blob;
    }
  }

  function base64ToBlob(base64, mime){
      mime = mime || '';
      var sliceSize = 1024;
      var byteChars = window.atob(base64);
      var byteArrays = [];

      for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
          var slice = byteChars.slice(offset, offset + sliceSize);

          var byteNumbers = new Array(slice.length);
          for (var i = 0; i < slice.length; i++) {
              byteNumbers[i] = slice.charCodeAt(i);
          }

          var byteArray = new Uint8Array(byteNumbers);

          byteArrays.push(byteArray);
      }

      return new Blob(byteArrays, {type: mime});
  }

  function displayErrorMessage(error_msg, error){
    error = error || "";
    if(error){
      console.log(error);
    }

    error_message.innerText = error_msg;

    hideUI();
    error_message.classList.add("visible");
  }

  function hideUI(){
    // Helper function for clearing the app UI.

    controls.classList.remove("visible");
    start_camera.classList.remove("visible");
    video.classList.remove("visible");
    snap.classList.remove("visible");
    error_message.classList.remove("visible");
  }

  function getBestMatch(arr, descriptor){
    // arr[0].dist = faceapi.euclideanDistance(new Float32Array(Object.values(JSON.parse(arr[0].descriptor))),descriptor);
    arr[0].dist = 0.35;

    // return arr;
    let best = arr[0];
    arr.map((data) => {
      let d1 = new Float32Array(Object.values(JSON.parse(data.descriptor)))
      let d2 = descriptor
      let dist = faceapi.euclideanDistance(d1, d2);
      data.dist = dist;
      if(best.dist > data.dist){
        best = data;
      }
    });

    if(best.dist > 0.35){
      best = {
        fullname: "",
        employee_idno: "",
        dist: 0.35

      }
    }
    return best;
  }

  function getBestMatch2(arr, descriptor){
    let best = [];
    let faceMatcher = new faceapi.FaceMatcher(descriptor,0.60);
    arr.map((data) => {
      let d1 = new Float32Array(Object.values(JSON.parse(data.descriptor)));
      data.dist = faceMatcher.findBestMatch(d1).distance;
      best.push(data);
    });

    result = best.reduce(function(prev, curr){
      // console.log(x++);
      return prev.dist < curr.dist ? prev : curr;
    });
    // return result;
    // result.dist > 0.36
    let dist = result.dist * 100;
    // console.log(dist);
    (dist > 37.60) ? result = {fullname: '', employee_idno: ''} : result;
    return result;
  }

  function startVideo() {
    navigator.mediaDevices.getUserMedia(constraint)
    .then(successCallback)
    .catch(errorCallback)
  }

  function toast(name, callback = false, pic = false){
    let open = false;
    // console.log(open);
    if(open === false){
      let id = $('#employee_idno').val();
      Swal.fire({
        title: 'Are you '+name+' ?',
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        onOpen: function(){
          open = true
        },
        onClose: function(){
          open = false
          // processing = false;
        }
      }).then((result) => {
        if (result.value) {
          if(pic){
            callback(pic,id);
          }
        }else{
          processing = false;
          $('#emp_name').val('');
          $('#employee_idno').val('');
          limit = 36;
        }
      })
    }
  }

  function startFacialRecognition(){
    // const facial_recog = $('#fr').val();
    const parse_fr = JSON.parse(facial_recog);

    const canvas = faceapi.createCanvasFromMedia(video);
    document.getElementById('video_wrapper').append(canvas)
    const displaySize = { width: video.offsetWidth, height: video.offsetHeight }
    faceapi.matchDimensions(canvas, displaySize)

    setInterval(async () => {
      const options = {
        scoreThreshold: 0.60,
        inputSize: 160
      }
      const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions(options))
      .withFaceLandmarks().withFaceExpressions().withFaceDescriptor() || [];
      // console.log(detections);

      const resizedDetections = faceapi.resizeResults(detections, displaySize)
      try{
        const { happy } = detections.expressions;
        const { score } = detections.detection;

        // CONTINOUS CHECKING
        // console.log('initial',limit);
        if($('#emp_name').val() == "" && $('#employee_idno').val() == "" && limit > 35.99){
          let bestMatch = getBestMatch2(parse_fr,detections.descriptor);
          // console.log('pre',limit);
          limit = (bestMatch.dist) ? bestMatch.dist * 100 : 36;
          // console.log('post',limit);
          // console.log(bestMatch);
          $('#emp_name').val(bestMatch.fullname);
          $('#employee_idno').val(bestMatch.employee_idno);
        }

        if(happy > 0.45 && $('#employee_idno').val() != "" && processing === false){
          processing = true;
          let pic = takeSnapshot();
          let name = $('#emp_name').val();
          let id = $('#employee_idno').val();
          let bestMatch2 = getBestMatch2(parse_fr,detections.descriptor);
          if(bestMatch2.employee_idno == id && name != "" && id != ""){
            toast(name,loginMe,pic);
          }else{
            processing = false;
            $('#emp_name').val('');
            $('#employee_idno').val('');
            limit = 36;
            // toast(name,loginMe,pic);
          }

        }

        // ONE TIME CHECKING
        // if(happy > 0.60){
        //   // processing = true;
        //   let bestMatch = getBestMatch2(parse_fr,detections.descriptor);
        //   if(processing === false && bestMatch.fullname != "" && bestMatch.employee_idno != ""){
        //     loginMe(bestMatch.fullname,bestMatch.employee_idno);
        //   }
        // }

      }catch(err){
        $('#emp_name').val('');
        $('#employee_idno').val('');
        limit = 36;
      }

      canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
      faceapi.draw.drawDetections(canvas, resizedDetections)
      // faceapi.draw.drawFaceLandmarks(canvas, resizedDetections)
      // faceapi.draw.drawFaceExpressions(canvas, resizedDetections)
    }, 500)

  }

  // FUNCTIONS
  var xmlHttp;

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

              var latlng = {lat:getlatitude, lng:getlongitude};
              $('#getaddress').val(JSON.stringify(latlng));
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
          $('#emp_name').val('');
          $('#employee_idno').val('');
          $('#modalTitle').html(data.mode);
          $('#modalMessage').html(data.message);
          $('#logModal').modal('toggle');
          setTimeout(() => {
            $('#logModal').modal('hide');
            processing = false;
            limit = 36;
          },3000);
        }else{
          $('#emp_name').val('');
          $('#employee_idno').val('');
          notificationError('Error', data.message);
          setTimeout(() => {
            processing = false;
            limit = 36;
          },3000);
        }
      }
    });
  }

  function loginMe(pic,id = false){
    // if(processing === false){
      // processing = true;

      get_location((status,lat,lng) => {
        if(status === true){
          // var timezone = get_timezone(lat,lng);
          var timezone = $('#timezone').val();
          var time = flipClock();

          var getTime = showTime(time);
          var empId = (id === false) ? $('#employee_idno').val() : id;
          var location2 = JSON.stringify(location);

          var blob = pic;
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
              // $('#capturebtn').attr('disabled','disabled');
            },
            success: function(data){
              $.LoadingOverlay('hide');

              switch (data.success) {
                case 1:
                  check_sess_id((admin) => {
                    if(admin == true){
                      $('#mapdetails').html(data.location);
                    }
                  });
                  // obj.play();
                  $('#emp_name').val('');
                  $('#employee_idno').val('');
                  $('#modalTitle').html(data.mode);
                  $('#modalMessage').html(data.message);
                  $('#logModal').modal('toggle');
                  setTimeout(() => {
                    $('#logModal').modal('hide');
                    processing = false;
                    limit = 36;
                  },3000);
                  break;
                case 2:
                  Swal.fire({
                    title: 'Oops! It looks like you don\'t have a timeout from your last timein. Will this be your ? ',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Time In ',
                    cancelButtonText: 'Time Out',
                    onOpen: function(){
                      open = true
                    },
                    onClose: function(){
                      open = false
                      // processing = false;
                    }
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
                  $('#emp_name').val('');
                  $('#employee_idno').val('');
                  notificationError('Error', data.message);
                  setTimeout(() => {
                    processing = false;
                    limit = 36;
                  },3000);
              }
              // if(data.success == 1){
              //   // console.log(data.location);
              //   check_sess_id((admin) => {
              //     if(admin == true){
              //       $('#mapdetails').html(data.location);
              //     }
              //   });
              //   // obj.play();
              //   $('#emp_name').val('');
              //   $('#employee_idno').val('');
              //   $('#modalTitle').html(data.mode);
              //   $('#modalMessage').html(data.message);
              //   $('#logModal').modal('toggle');
              //   setTimeout(() => {
              //     $('#logModal').modal('hide');
              //     processing = false;
              //     limit = 36;
              //   },3000);
              // }else{
              //   $('#emp_name').val('');
              //   $('#employee_idno').val('');
              //   notificationError('Error', data.message);
              //   setTimeout(() => {
              //     processing = false;
              //     limit = 36;
              //   },3000);
              // }
            }
          })
        }else{
          $('#emp_name').val('');
          $('#employee_idno').val('');
          setTimeout(() => {
            processing = false;
            limit = 36;
          }, 2000);
        }
      });
    // }else{
      // console.log(processing);
    // }
  }

  flipClock();

  // ON VIDEO PLAY
  video.addEventListener('play', () => {
    startFacialRecognition();
  });

  // TAKE SNAPSHOT
  $(document).on('click', '#capturebtn', function(){

  });
  // RF NUMBER
  $(document).on('keyup', '.emp_idno', function(e){
    if(e.keyCode === 13){
      $('#capturebtn').click();
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
  });
  // GO TO CLOCK IN  AND OUT
  $(document).on('click', '#btn_clock_in', function(){
    // console.log('hello');
    // return;
    window.location.href = base_url + 'employees/Timelog/redirect_to_clock_in';
  });
  // RELOAD
  $(document).on('click', '#btn_reload', function(){
    window.location.reload(true);
  });
  // GO HOME
  $(document).on('click', '#btn_home', function(){
    window.location.href = base_url;
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
					notificationError('Error',data.message);
          $('.btnLogin').prop('disabled',false);
		    }
		  }
		});
	});

  // MODAL CLOSE
  $('#logModal').on('hidden.bs.modal', function(){
    // processing = false;
    // window.location.reload(true);
    var div = $('.btn-nav.active').data('nav');
    $('#rf_idnumber').val('');
    $('.emp_idno').val('');
    (div == 'divScanRf') ? $('#rf_idnumber').focus() : $('.emp_idno').focus();
    // $('#rf_idnumber').focus();
  });
  // MAP MODAL
  $('#location_modal').on('hidden.bs.modal', function(){
    $('#rf_idnumber').val('');
    $('.emp_idno').val('');
    $('#rf_idnumber').focus();
  });


});
