$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  const image = document.getElementById('image');
  const cropper = new Cropper(image, {
    aspectRatio: 16 / 9,
    crop(event) {
      console.log(event.detail.x);
      console.log(event.detail.y);
      console.log(event.detail.width);
      console.log(event.detail.height);
      console.log(event.detail.rotate);
      console.log(event.detail.scaleX);
      console.log(event.detail.scaleY);
    },
  });
});
