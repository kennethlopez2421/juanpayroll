$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();


  $(document).on('click', '#btn_save_hrassists', function(){
    var error = 0;
    var errorMsg = "";
    var thiss = $(this);
    var hr_body = CKEDITOR.instances['hrassists_body'].getData();

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
        url: base_url+'settings/Hr_assists/create',
        type: 'post',
        data:{hrassists_body: hr_body},
        beforeSend: function(){
          $.LoadingOverlay('show');
          thiss.prop('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          thiss.prop('disabled', false);
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            setTimeout(function(){
              location.reload();
            },1000);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
});
