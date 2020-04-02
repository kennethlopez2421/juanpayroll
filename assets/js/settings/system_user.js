$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function gen_systemUserTable(search){
    var systemUserTable = $('#systemUserTable').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Systemusers/get_systemuser_json',
        type: 'post',
        data: { searchValue: search },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(){
          $.LoadingOverlay('hide');
        },
        error: function(){
					$.LoadingOverlay('hide');
        }
      }
    });
  }
  gen_systemUserTable('');

  // SUBMIT ADD FORM
  $(document).on('submit', '#system_user_addform', function(e){
    e.preventDefault();

    var error = 0;
    var errorMsg = "";
    var form_data = $(this).serialize();
    var sys_password = $('#sys_password').val();
    var sys_password_cf = $('#sys_password_cf').val();

    if(sys_password != sys_password_cf){
      error = 1;
      errorMsg = "Password do not match. Please try again.";
    }

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
        url: base_url+'settings/Systemusers/add',
        type: 'post',
        data:form_data,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_submit_addform').prop('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success',data.message);
            $('#btn_submit_addform').prop('disabled', false);
            gen_systemUserTable('');
          }else{
            notificationError('Error', data.message);
            $('#btn_submit_addform').prop('disabled', false);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // SUBMIT EDIT FORM
  $(document).on('submit', '#system_user_editform', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    var edit_formdata = $(this).serialize();

    $('.edit_rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.edit_rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Systemusers/update_system_user',
        type: 'post',
        data:edit_formdata,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_update_form').prop('disabled',true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#edit_modal').modal('hide');
            notificationSuccess('Success',data.message);
            $('#btn_update_form').prop('disabled',false);
            gen_systemUserTable('');
          }else{
            notificationError('Error',data.message);
            $('#btn_update_form').prop('disabled',false);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // DISABLE ACCOUNT
  $(document).on('click', '#btn_del_sys', function(){
    var del_id = $('#sys_del_id').val();
    $.ajax({
      url: base_url+'settings/Systemusers/disable_system_user',
      type: 'post',
      data:{del_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_modal').modal('hide');
          notificationSuccess('Success',data.message);
          gen_systemUserTable('');
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });
  // SEARCH SYSTEM USER
  $(document).on('click', '#btnSearchSysUser', function(){
    var search = $('.searchArea').val();
    gen_systemUserTable(search)
  });

  // CALL ADD MODAL
  $(document).on('click', '#btn_add', function(){
    $('#add_modal').modal();
  });
  // CALL EDIT MODAL
  $(document).on('click', '.btn_edit_sys', function(){
    var thiss = $(this);
    $('#edit_uid').val(thiss.data('uid'));
    $('#edit_sys_username').val(thiss.data('username'));
    $('#edit_sys_fname').val(thiss.data('fname'));
    $('#edit_sys_lname').val(thiss.data('lname'));
    $('#edit_sys_mname').val(thiss.data('mname'));
    $('#employee_idno').val(thiss.data('employee_idno'));
    $('#edit_modal').modal();
  });
  // CALL DELETE MODAL
  $(document).on('click', '.btn_del_sys', function(){
    $('.user_disable').text($(this).data('fullname'));
    $('#sys_del_id').val($(this).data('del_id'));
    $('#delete_modal').modal();
  });


});
