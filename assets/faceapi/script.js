$(function(){
  const video = document.getElementById('camera-stream')
  const v_wrapper = document.getElementById('video_wrapper');
  const base_url = $("body").data('base_url');
  const constraint = { video: true, audio: false };
  // var processing = false;
  // console.log("width", v_wrapper.offsetWidth);
  // console.log("height", v_wrapper.offsetHeight);

  Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri(base_url+'assets/faceapi/models'),
    faceapi.nets.faceExpressionNet.loadFromUri(base_url+'assets/faceapi/models')
  ]).then(startVideo);

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
    arr[0].dist = faceapi.euclideanDistance(new Float32Array(Object.values(JSON.parse(arr[0].descriptor))),descriptor);

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

    if(best.dist > 0.40){
      best = {
        fullname: "",
        employee_idno: ""
      }
    }
    return best;
  }

  function startVideo() {
    navigator.mediaDevices.getUserMedia(constraint)
    .then(successCallback)
    .catch(errorCallback)
  }

  function startFacialRecognition(){
    const facial_recog = $('#fr').val();
    const parse_fr = JSON.parse(facial_recog);
    // console.log(parse_fr);
    // return;
    const test = new Float32Array(Object.values(JSON.parse(parse_fr[0].descriptor)));

    const canvas = faceapi.createCanvasFromMedia(video);
    document.getElementById('video_wrapper').append(canvas)
    const displaySize = { width: video.offsetWidth, height: video.offsetHeight }
    faceapi.matchDimensions(canvas, displaySize)

    setInterval(async () => {
      const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
      .withFaceLandmarks().withFaceExpressions().withFaceDescriptors();
      const resizedDetections = faceapi.resizeResults(detections, displaySize)
      try{
        const { happy } = detections[0].expressions;
        const dist = faceapi.euclideanDistance(test, detections[0].descriptor);
        let bestMatch = getBestMatch(parse_fr,detections[0].descriptor);
        $('#emp_name').val(bestMatch.fullname);
        $('#employee_idno').val(bestMatch.employee_idno)
        if(happy > 0.10 && $('#employee_idno').val() != ""){
          console.log("snapshot");
          console.log($('#employee_idno').val());
          // console.log(processing);
          // loginMe()
        }
        // console.log(bestMatch);
      }catch(err){
        $('#emp_name').val('');
        $('#employee_idno').val('');
      }

      canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
      faceapi.draw.drawDetections(canvas, resizedDetections)
      faceapi.draw.drawFaceLandmarks(canvas, resizedDetections)
      faceapi.draw.drawFaceExpressions(canvas, resizedDetections)
    }, 500)

  }

  video.addEventListener('play', () => {

    startFacialRecognition();
  });


})
