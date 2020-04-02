$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_registered_device_tbl(search){
    var registered_device_tbl = $('#registered_device_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Registered_device/get_registered_device_json',
        type: 'post',
        data: { searchValue: search },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(){
          $.LoadingOverlay('hide');
        },
        error: function(){

        }
      }
    });
  }

  gen_registered_device_tbl(JSON.stringify(searchValue));

  $(document).on('click', '#btn_add', function(){
    $.ajax({
      url: base_url+'settings/Registered_device/gen_activation_code',
      type: 'post',
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#activation_code').val(data.code);
          $('#add_modal').modal();
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

  $(document).on('submit', '#activation_form', function(e){
    e.preventDefault();
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
        url: base_url+'settings/Registered_device/create',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_registered_device_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_delete', function(){
    var thiss = $(this);
    $('#del_code').text(thiss.data('activation_code'));
    $('#del_devid').text(thiss.data('device_id'));
    $('#delid').val(thiss.data('delid'));
    $('#delete_modal').modal();
  });

  $(document).on('click', '#btn_yes', function(){
    var delid = $('#delid').val();
    if(delid == ""){
      notificationError('Error', 'Unable to delete this activation code. Please try again.');
      return;
    }

    $.ajax({
      url: base_url+'settings/Registered_device/delete',
      type: 'post',
      data:{delid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_registered_device_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    searchValue.filter = filter_by;
    // for single date
    if($("#"+filter_by).hasClass('single_search')){
      searchValue.search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      searchValue.from = $('#'+filter_by).children().find('.from').val();
      searchValue.to = $('#'+filter_by).children().find('.to').val();
    }

    gen_registered_device_tbl(JSON.stringify(searchValue));

  });
});
