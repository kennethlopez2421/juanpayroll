$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function gen_caps_tbl(search){
    var workOrder_tbl = $('#CaPs_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Cashadvancepaymentscheme/getcaps_json',
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

  gen_caps_tbl("");

  $(document).on('click', '#btn_add_caps', function(){
    $('#add_caps_modal').modal();
  });

  $(document).on('submit', '#caps_form', function(e){
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
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Cashadvancepaymentscheme/add',
        type: 'post',
        data: $(this).serialize(),
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_caps_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_caps_tbl("");
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_edit_caps', function(){
    $('#edit_id').val($(this).data('id'));
    $('#edit_monthly_rate').val($(this).data('monthly_rate'));
    $('#edit_maximum_loan').val($(this).data('maximum_loan'));
    $('#edit_term_of_payment').val($(this).data('term_of_payment'));

    $('#edit_caps_modal').modal();
  });

  $(document).on('submit', '#edit_caps_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'settings/Cashadvancepaymentscheme/edit',
      type: 'post',
      data:$(this).serialize(),
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#edit_caps_modal').modal('hide');
          notificationSuccess('Success',data.message);
          gen_caps_tbl("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '.btn_del_caps', function(){
    var thiss = $(this);
    $('#del_caps_modal').modal();
    $('#btn_yes').click(function(){
      $.ajax({
        url: base_url+'settings/Cashadvancepaymentscheme/delete',
        type: 'post',
        data:{del_id: thiss.data('id')},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#del_caps_modal').modal('hide');
            notificationSuccess("Success", data.message);
            gen_caps_tbl("");
          }else{
            notificationError("Error", data.message);
          }
        }
      });
    })
  });

});
