$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function gen_sss_tbl(search){
    var sss_tbl = $('#sss_tbl').DataTable( {
      "processing": true,
      "pageLength": 10,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Sss_controller/get_sss_json',
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

  gen_sss_tbl("");

  $(document).on('click', '#btn_add_sss', function(){
    $('#add_sss_modal').modal();
  });

  $(document).on('click', '#btn_save_sss', function(){
    var error = 0;
    var errorMsg = "";
    var range_from = $('#range_from').val();
    var range_to = $('#range_to').val();
    var monthly_cred = $('#monthly_cred').val();
    var sss_er = $('#sss_er').val();
    var sss_ee = $('#sss_ee').val();
    var sss_total = $('#sss_total').val();
    var ec = $('#ec').val();
    var tc_er = $('#tc_er').val();
    var tc_ee = $('#tc_ee').val();
    var tc_total = $('#tc_total').val();

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

    if(parseFloat(range_from) >= parseFloat(range_to)){
      $('#range_from').css("border", "1px solid #ef4131");
      $('#range_to').css("border", "1px solid #ef4131");
      error = 1;
      errorMsg = "Invalid Range of Compensation. Please try again";
    }else{
      $('#range_from').css("border", "1px solid gainsboro");
      $('#range_to').css("border", "1px solid gainsboro");
      error = 0;
    }
    // console.log($('#range_to').val());
    // return false;

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Sss_controller/create',
        type: 'post',
        data:{
          range_from, range_to, monthly_cred, sss_er, sss_ee,
          sss_total, ec, tc_er, tc_ee, tc_total
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
              $('#add_sss_modal').modal('hide');
              notificationSuccess('Success', data.message);
              gen_sss_tbl("");
          }else{
              notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_edit_sss', function(){
    $('#uid').val($(this).data('update_id'));
    $('#edit_range_from').val($(this).data('sal_from'));
    $('#edit_range_to').val($(this).data('sal_to'));
    $('#prevFrom').val($(this).data('sal_from'));
    $('#prevTo').val($(this).data('sal_to'));
    $('#edit_monthly_cred').val($(this).data('monthly_cred'));
    $('#edit_sss_er').val($(this).data('ss_er'));
    $('#edit_sss_ee').val($(this).data('ss_ee'));
    $('#edit_sss_total').val($(this).data('ss_total'));
    $('#edit_ec').val($(this).data('ec_er'));
    $('#edit_tc_er').val($(this).data('tc_er'));
    $('#edit_tc_ee').val($(this).data('tc_ee'));
    $('#edit_tc_total').val($(this).data('tc_total'));
    $('#update_sss_modal').modal();
  });

  $(document).on('click', '#btn_update_sss', function(){
    var error = 0;
    var errorMsg = "";
    var update_id = $('#uid').val();
    var prevFrom = $('#prevFrom').val();
    var prevTo = $('#prevTo').val();
    var range_from = $('#edit_range_from').val();
    var range_to = $('#edit_range_to').val();
    var monthly_cred = $('#edit_monthly_cred').val();
    var sss_er = $('#edit_sss_er').val();
    var sss_ee = $('#edit_sss_ee').val();
    var sss_total = $('#edit_sss_total').val();
    var ec = $('#edit_ec').val();
    var tc_er = $('#edit_tc_er').val();
    var tc_ee = $('#edit_tc_ee').val();
    var tc_total = $('#edit_tc_total').val();

    $('.rq2').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq2').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(parseFloat(range_from) >= parseFloat(range_to)){
      $('#range_from').css("border", "1px solid #ef4131");
      $('#range_to').css("border", "1px solid #ef4131");
      error = 1;
      errorMsg = "Invalid Range of Compensation. Please try again";
    }else{
      $('#range_from').css("border", "1px solid gainsboro");
      $('#range_to').css("border", "1px solid gainsboro");
      error = 0;
    }
    // console.log($('#range_to').val());
    // return false;

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Sss_controller/update',
        type: 'post',
        data:{
          update_id, range_from, range_to, monthly_cred, sss_er, sss_ee,
          sss_total, ec, tc_er, tc_ee, tc_total, prevFrom, prevTo
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
              $('#update_sss_modal').modal('hide');
              notificationSuccess('Success', data.message);
              gen_sss_tbl("");
          }else{
              notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_delete_sss', function(){
      var thiss = $(this);
    $('#delete_sss_modal').modal();
    // console.log(thiss.data('delete_id'));
    // return false;
    $('#btn_yes_del').click(function(){
      $.ajax({
        url: base_url+'settings/Sss_controller/delete',
        type: 'post',
        data:{del_id: thiss.data('delete_id')},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#delete_sss_modal').modal('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            gen_sss_tbl("");
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    });
  });

});
