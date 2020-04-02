$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var itCounter = 0;
  var error = 0;

  $('#emp_id').select2();
  $('#wo_date').datepicker({ dateFormat: 'dd-mm-yyyy', autoclose: true, todayHighlight: true});


  $(document).on('click', '#btnItinerary_modal', function(){
    itCounter += 1;
    var div =
    '<div class="col-12 itContainer mb-3" style = "border-bottom:1px solid gainsboro;">'+
      '<h4>Itinerary '+itCounter+'<button class="btn btn-sm btn-danger btn_del_itinerary float-right"><i class="fa fa-trash"></i></button></h4>'+
      '<div class="row">'+
        '<div class="col-md-4 mb-3">'+
          '<input type="text" class="form-control req" id = "location'+itCounter+'" name = "location'+itCounter+'">'+
          '<small class="form-text">Location <span class="asterisk"></span></small>'+
        '</div>'+
        '<div class="col-md-4 mb-3">'+
          '<input type="text" class="form-control req" id = "contact_person'+itCounter+'" name = "contact_person'+itCounter+'">'+
          '<small class="form-text">Contact Person <span class="asterisk"></span></small>'+
        '</div>'+
        '<div class="col-md-4 mb-3">'+
          '<input type="text" class="form-control contactNumber req" id="contact_num'+itCounter+'" name = "contact_num'+itCounter+'">'+
          '<small class="form-text">Contact Number <span class="asterisk"></span></small>'+
      '  </div>'+
        '<div class="col-md-6 mb-2">'+
          '<textarea name="purpose'+itCounter+'" id="purpose'+itCounter+'" cols="30" rows="5" class="form-control req"></textarea>'+
          '<small class="form-text">Purpose <span class="asterisk"></span></small>'+
        '</div>'+
        '<div class="col-md-6 mb-2">'+
          '<textarea id = "notes'+itCounter+'" name = "notes'+itCounter+'" cols="30" rows="5" class="form-control req"></textarea>'+
          '<small class="form-text">Notes <span class="asterisk"></span></small>'+
        '</div>'+
      '</div>'+
    '</div>'

    $('.div_it').append(div);

    $('.contactNumber').numeric({
  		maxPreDecimalPlaces : 11,
  		maxDecimalPlaces: 0,
  		allowMinus: false
  	});
  });

  $(document).on('click', '.btn_del_itinerary', function(){
    $(this).parents('.itContainer').remove();
    itCounter -= 1;
  });

  $(document).on('click', '#btn_saveWorkOrder', function(){
    var thiss = $(this);
    thiss.prop('disabled',true);
    var error = 0;
    var errorMsg = "";
    // highlight any error
    $('.req').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });
    // focus any error
    $('.req').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        erroMsg = "Please fill up all required fields";
        return false;
      }
    });
    // collect work order info
    var data = {
      emp_id: $('#employee_id_no').val(),
      date: $('#wo_date').val(),
      startTime: $('#wo_sTime').val(),
      endTime: $('#wo_eTime').val(),
      dept: $('#dept').val()
    };
    // collect itinerary info
    var itCount = 0;
    var itArray = [];
    $('.itContainer').each(function(){
      itCount++;
      itArray[itCount] =
      [
        $('#location'+[itCount]).val(),
        $('#contact_person'+[itCount]).val(),
        $('#contact_num'+[itCount]).val(),
        $('#purpose'+[itCount]).val(),
        $('#notes'+[itCount]).val()
      ]
    });
    data.itinerary = itArray;
    //submit if no error
    if(error == 0){
      $.ajax({
        url: base_url+'/transactions/Workorder/create',
        type: 'post',
        data: data,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            setTimeout(function(){
              window.location.href = base_url+'/transactions/Workorder/index/'+token;
            },1500);
            if(data.email_status == 1){
              var email_window = window.open(base_url+'emails/Transaction_email/index/'+token+'/'+data.email,'',"width=250,height=200");
              email_window.moveTo(window.outerWidth,0);
            }
          }else{
            thiss.prop('disabled', false);
            notificationError('Error', data.message);
          }
        },
        error: function(){
          $.LoadingOverlay('hide');
          thiss.prop('disabled', false);
        }
      });
    }else{
      notificationError('Error', errorMsg);
      thiss.prop('disabled', false);
    }


  });

});
