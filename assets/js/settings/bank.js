$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function gen_bank_tbl(search){
    var bank_tbl = $('#bank_tbl').DataTable( {
      "processing": true,
      "pageLength": 10,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Bank/get_bank_json',
        type: 'post',
        data: {
          searchValue: search
        },
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

  gen_bank_tbl("");

  $(document).on('click', '#btnSearch', function(){
    var search = $('#searchArea').val();
    gen_bank_tbl(search);
  });
  // create modal
  $(document).on('click','#btnAdd', function(){
    $('#add_bank_modal').modal();
  });
  // update modal
  $(document).on('click', '.btn_update_modal', function(){
    $('#update_bank_name').val($(this).data('bank_name'));
    $('#current_bank_name').val($(this).data('bank_name'));
    $('#uid').val($(this).data('uid'))
    $('#edit_bank_modal').modal()
  });
  // delete modal
  $(document).on('click', '.btn_delete_modal', function(){
    $('.info_desc').text($(this).data('bank_name'));
    $('#del_id').val($(this).data('del_id'));
    $('#delete_bank_modal').modal();
  });
  // create bank
  $(document).on('click', '#btn_add_bank', function(){
    var bank_name = $('#add_bank_name').val();
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
        url: base_url+'settings/Bank/create',
        type: 'post',
        data:{bank_name},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            $('#add_bank_modal').modal('hide');
            gen_bank_tbl("");
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // update bank
  $(document).on('click','#btn_update_bank', function(){
    var updated_name = $('#update_bank_name').val();
    var current_bank_name = $('#current_bank_name').val();
    var uid = $('#uid').val();

    if(updated_name == current_bank_name){
      $('#edit_bank_modal').modal('hide');
      return false;
    }

    if(updated_name == ""){
      notificationError('Error', 'Please input a bank name before saving.');
      return false;
    }

    $.ajax({
      url: base_url+'settings/Bank/update',
      type: 'post',
      data:{
        updated_name,
        uid
      },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success',data.message);
          $('#edit_bank_modal').modal('hide');
          gen_bank_tbl("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // delete bank
  $(document).on('click', '#btn_del_bank', function(){
    var del_id = $('#del_id').val();
    $.ajax({
      url: base_url+'settings/Bank/delete',
      type: 'post',
      data:{del_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#delete_bank_modal').modal('hide');
        if(data.success == 1){
          notificationSuccess('Success',data.message);
          $('#delete_bank_modal').modal('hide');
          gen_bank_tbl("");
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  })
});
