$.LoadingOverlay = function(action, msg = "Processing..."){
  if(!$('.loader').length > 0 ){
    $('body').prepend(`<div style = "display:none;opacity:0.5 !important;" class="loader loader-default is-active blur" data-half data-text = "${msg}" data-blink></div>`)
  }
  switch (action) {
    case 'show':
      $('.loader').show();
      break;
    case 'hide':
      $('.loader').fadeOut('slow');
      break;
    default:

  }
}
