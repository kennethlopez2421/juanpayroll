$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  // alert();

  function gen_companies_tbl(search){
    var gen_companies_tbl = $('#companies_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Companies/get_companies_json',
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
  };

  gen_companies_tbl("");

  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  });

  $(document).on('submit', '#add_form', function(e){
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
        url: base_url+'settings/Companies/create',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#add_modal').modal('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            gen_companies_tbl("");
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_edit_modal', function(){
    var uid = $(this).data('uid');
    var name = $(this).data('name');

    $('#edit_company_name').val(name);
    $('#current_company_name').val(name);
    $('#uid').val(uid);

    $('#edit_modal').modal();
  });

  $(document).on('submit', '#edit_form', function(e){
    e.preventDefault();
    var edit_company_name = $('#edit_company_name').val();
    var current_company_name = $('#current_company_name').val();

    if(edit_company_name == current_company_name){
      $('#edit_modal').modal('hide');
      return;
    }

    var error = 0;
    var errorMsg = "";

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

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Companies/update',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#edit_modal').modal('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            gen_companies_tbl("");
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  })

  $(document).on('click', '.btn_del_modal', function(){
    $('#del_name').text($(this).data('name'));
    $('#delid').val($(this).data('delid'));
    $('#delete_modal').modal();
  });

  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'settings/Companies/delete',
      type: 'post',
      data: new FormData(this),
      processData: false,
      contentType: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#delete_modal').modal('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          gen_companies_tbl("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '#searchButton', function(){
    var searchArea = $('#searchArea').val();
    gen_companies_tbl(searchArea);
  })
});
