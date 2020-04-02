$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var faceapi_data;
  var processing = false;

  const video = document.getElementById('camera-stream');
  const v_wrapper = document.getElementById('video_wrapper');
  const contraints = { video: true, audio: false };

  Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceExpressionNet.loadFromUri(base_url+'assets/faceapi/models')
  ]).then(startVideo);

  function successCallback(stream){
    video.srcObject = stream;
    setInterval(() => {video.play()},5000);
  }

  function errorCallback(error){
    console.log("navigator.mediaDevices.getUserMedia error :", error);
  }

  function startVideo() {
    navigator.mediaDevices.getUserMedia(contraints)
    .then(successCallback)
    .catch(errorCallback)
  }

  function startFacialRecognition(){
    const canvas = faceapi.createCanvasFromMedia(video);
    document.getElementById('video_wrapper').append(canvas)
    const displaySize = { width: video.offsetWidth, height: video.offsetHeight }
    faceapi.matchDimensions(canvas, displaySize)
    let data = setInterval(async () => {
      const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({scoreThreshold:0.6}))
      .withFaceLandmarks().withFaceExpressions().withFaceDescriptors();
      const resizedDetections = faceapi.resizeResults(detections, displaySize)
      try{
        const { happy } = detections[0].expressions;
        if(happy > 0.40 && $('#employee_idno').val() != "" && processing == false){
          // $('#btn_capture').click();
        }
      }catch(err){

      }
      canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
      faceapi.draw.drawDetections(canvas, resizedDetections)
      faceapi.draw.drawFaceLandmarks(canvas, resizedDetections)
      faceapi.draw.drawFaceExpressions(canvas, resizedDetections)
      faceapi_data = detections;
      // console.log(faceapi_data);
    }, 500)
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

  video.addEventListener('play', () => {
    startFacialRecognition();
  });

  $(document).on('click', '#btn_capture', function(){
    if(processing === false){
      const { alignedRect, expressions, landmarks, descriptor } = faceapi_data[0];
      const { _score } = alignedRect;

      const score = Math.round(_score * 100);
      const faceLandMarks = JSON.stringify(landmarks);
      const blob = takeSnapshot();
      const employee_idno = $('#employee_idno').val();

      var formData = new FormData();
      formData.append('picture', blob);
      formData.append('employee_idno', employee_idno);
      formData.append('faceLandMarks', faceLandMarks);
      formData.append('accuracy', score);
      formData.append('descriptor', JSON.stringify(descriptor));

      $.ajax({
        url: base_url+'registerid/Register_facial/create',
        type: 'post',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          processing = true;
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#logModal').modal();
          }else{
            notificationError('Error', data.message);
            processing = false;
          }
        }
      });
    }
  });

  $(document).on('keyup', '#search_emp_fr', function(){
    var keyword = $(this).val();
    if(keyword == ""){
      $('.search_div').html('');
      return false;
    }
    $.ajax({
      url: base_url+'Main/search_user',
      type: 'post',
      data:{keyword},
      beforeSend: function(){
        $('.loader_wrapper').show();
      },
      success: function(data){
        $('.loader_wrapper').hide();
        if(data.success == 1){
          $('.search_div').html(data.message).show('slow');
        }else{
          $('.search_div').html(data.message).show('slow');
        }
      }
    });
  });

  $('#logModal').on('hidden.bs.modal', function(){
    processing = false;
  });
});
