$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  $(document).on('submit', '#admin_login_form', function(e){
    e.preventDefault();
    var admin_form = new FormData(this);
    var error = 0;
    var errorMsg = "";

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'admin/Admin/cp_login',
        type: 'post',
        contentType: false,
        processData: false,
        data: admin_form,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_login').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_login').prop('disabled', false);
          if(data.success == 1){
            var userData = data.userData;
						var token = data.token_session;
						// if(data.visible == 1)
						window.location.href = ''+base_url+'/admin/Admin/home/'+token;
          }else{
            $.toast({
						    heading: 'Note',
						    text: data.message,
						    icon: 'error',
						    loader: false,
						    stack: false,
						    position: 'top-center',
  							allowToastClose: false,
  							bgColor: '#f0ad4e',
  							textColor: 'white'
						});
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
});
