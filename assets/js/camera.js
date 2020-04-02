var base_url = $("body").data('base_url');
// const constraint = { video: true, audio: false };

// References to all the element we will need.
var video = document.querySelector('video'),
    image = document.querySelector('#snap'),
    start_camera = document.querySelector('#start-camera'),
    controls = document.querySelector('.controls'),
    take_photo_btn = document.querySelector('#take-photo'),
    delete_photo_btn = document.querySelector('#delete-photo'),
    download_photo_btn = document.querySelector('#download-photo'),
    error_message = document.querySelector('#error-message');


// MARKY
  // var getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
  var constraints = {audio: false, video: true};
  // var video = document.querySelector("video");
  //
  function successCallback(stream) {
    video.srcObject = stream;
    video.play();
  }

  function errorCallback(error) {
    console.log("navigator.getUserMedia error: ", error);
  }

  navigator.mediaDevices.getUserMedia(constraints)
  .then(successCallback)
  .catch(errorCallback);

// MARKY END

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

function base64ToBlob(base64, mime)
{
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
