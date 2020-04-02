$(function(){

  var base_url = $('body').data('base_url');

  function love(search){
    var pagIbig_tbl = $('#pagIbig_table').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Pagibig/pagibig_json',
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

  love("");

  $('#btnSearchLove').click(function(){
    var search = $('.searchArea').val();
    love(search);
  });

  $('#btn_add_modal').click(function(){
    $('#pagIbig_add_modal').modal();
  });

  $(document).on('click', '#btn_save_pagibig', function(){
    var monthly_compensation = $('#monthly_compensation').val();
    var employee_share = $('#employee_share').val();
    var employer_share = $('#employer_share').val();
    var error = 0;
    var errorMsg = "";

    $('.pRequired').each(function(){
      if($(this).val() == ""){
        error = 1;
        errorMsg = "Please fill up all required fields";
        $(this).css('border','1px solid #ef4131');
      }else{
        $(this).css('border','1px solid gainsboro');
      }
    });

    $('.pRequired').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url + 'settings/Pagibig/create',
        type: 'Post',
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        data: {
          monthly_compensation,
          employee_share,
          employer_share
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#pagIbig_add_modal').modal('hide');

          if(data.success == 1){
            notificationSuccess('Success', data.message);
            love("");
            // pagIbig_tbl.ajax.reload(null,false);
            // $('#pagIbig_add_modal').modal('hide');
            // location.reload();
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_edit_pagibig', function(){
    var updateId = $(this).data('updateid');

    $.ajax({
      url: base_url + 'settings/Pagibig/edit',
      type: 'post',
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      data: { updateId },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#updateId').val(data.pData.id);
          $('#edit_monthly_compensation').val(data.pData.monthly_compensation);
          $('#edit_employee_share').val(data.pData.employee_share);
          $('#edit_employer_share').val(data.pData.employer_share);

          $('#pagIbig_edit_modal').modal();
        }else{
          notificationError('Error', data.message);
        }
      }
    })
  });

  $(document).on('click', '#btn_update_pagibig', function(){
    var monthly_compensation = $('#edit_monthly_compensation').val();
    var employee_share = $('#edit_employee_share').val();
    var employer_share = $('#edit_employer_share').val();
    var updateId = $('#updateId').val();
    var error = 0;
    var errorMsg = "";

    $('.epRequired').each(function(){
      if($(this).val() == ""){
        error = 1;
        errorMsg = "Please fill up all required fields";
        $(this).css('border','1px solid #ef4131');
      }else{
        $(this).css('border','1px solid gainsboro');
      }
    });

    $('.epRequired').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        return false;
      }
    });

    if(updateId == ""){
      error = 1;
      errorMsg = "Update id error.";
    }

    if(error == 0){
      $.ajax({
        url: base_url + 'settings/Pagibig/update',
        type: 'Post',
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        data: {
          updateId,
          monthly_compensation,
          employee_share,
          employer_share
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#pagIbig_edit_modal').modal('hide');

          if(data.success == 1){
            notificationSuccess('Success', data.message);
            love("");
            // pagIbig_tbl.ajax.reload(null,false);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_del_pagibig', function(){
    var deleteId = $(this).data('deleteid');
    $('#pagIbig_del_modal').modal();

    $('#btn_delete_pagibig').click(function(){
      $.ajax({
        url: base_url + 'settings/Pagibig/destroy',
        type: 'post',
        data: { deleteId },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#pagIbig_del_modal').modal('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            love("");
            // pagIbig_tbl.ajax.reload(null,false);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    });
  });

  $(document).on('click', '#btn_pagIbig_ctrls', function(){
    $('.pagIbigTable').hide();
    $('.pagIbigTblHeader_wrapper').hide();
    $('#pagIbig_table').css("width", "100%");
    $('.dataTableContainer').show();
    $('.datatableTblHeader_wrapper').show();
  })

});
