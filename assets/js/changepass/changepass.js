
$(function(){
  var base_url = $('body').data('base_url');
  var token = $('#token').val();
  var status = "";
  var css = "badge-info";
  var pw_strength = 0;

  $(document).on('keypress', '#new_pw', function(e){
    if(e.keyCode == 32){
      return false;
    }
  });

  $(document).on('keyup', '#new_pw', function(){


    $("#pw-status").removeClass(css);

    var strength = 0;
    var pw = $(this).val();

    if(pw === ""){
      $("#pw-status").text("");
      return;
    }

    // check password strength
    if(pw.match("^[a-zA-Z0-9#?@!$%^&*-]{8,}$")){
      strength += 1;
    }

    if(pw.match("^(?=.*[A-Z])(?=.*[a-z])[a-zA-Z0-9#?@!$%^&*-]{8,}$")){
      strength += 1;
    }

    if(pw.match("^(?=.*[0-9])[a-zA-Z0-9#?@!$%^&*-]{8,}$")){
      strength += 1;
    }

    if(pw.match("^(?=.*[#?@!$%^&*-])[a-zA-Z0-9#?@!$%^&*-]{8,}$")){
      strength += 1;
    }

    // assess the strength
    switch (strength) {
      case 1:
        status = "Not Bad";
        css = "badge-warning";
        break;
      case 2:
        status = "Good";
        css = "badge-success";
        break;
      case 3:
        status = "Strong";
        css = "badge-danger";
        break;
      case 4:
        status = "Very Strong";
        css = "badge-danger";
        break;
      default:
        status = "Weak";
        css = "badge-info";
    }


    $("#pw-status").text(status).addClass(css);
    pw_strength = strength;
  });

  $(document).on('click', '#save_btn', function(){
    // var pass_form = $(this).serialize();
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
      if(!pw_strength == 0){
        $.ajax({
          url: base_url + 'Main/change_pass',
          type: 'post',
          data: {
            username: $('#username').val(),
            current_pw: $('#current_pw').val(),
            new_pw: $('#new_pw').val()
          },
          beforeSend: function(){
            $.LoadingOverlay('show');
            $('#save_btn').text('Saving ...').attr('disabled');
            $('#pw-status').removeClass(css);
          },
          success: function(data){
            $.LoadingOverlay('hide');
            $('#pw-status').hide();
            if(data.success == 1){
              notificationSuccess('Success', data.message);
              $('#username').val("");
              $('#current_pw').val("");
              $('#new_pw').val("");
              // $('#pw-status').text(data.message).addClass('badge-success');
            }else{
              notificationError('Error',data.message);
              // $('#pw-status').text(data.message).addClass('badge-danger');
            }
            $('#save_btn').text('Save').removeAttr('disabled');
          }
        });
      }else{
        notificationError('Error', 'Password is too weak');
      }
    }else{
      notificationError('Error', errorMsg);
    }

  });
});
