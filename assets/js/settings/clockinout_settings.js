$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_clockinout_tbl(search){
    var data_origin_tbl = $('#clockinout_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Clockinout_settings/get_clockinout_settings_json',
        type: 'post',
        data: {
          searchValue: search
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 0){
            notificationError('Error', data.message);
          }
        },
        error: function(){

        }
      }
    });
  };

  gen_clockinout_tbl(JSON.stringify(searchValue));

  $(document).on('change', '.status', function(){
    let status = $(this).val();
    let id = $(this).data('uid');
    // console.log(id);
    // return;
    if(status != ""){
      $.ajax({
        url: base_url+'settings/Clockinout_settings/update_status',
        type: 'post',
        data:{status, id},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            gen_clockinout_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }
  });

  $(document).on('blur', '.minutes', function(){
    let minutes = $(this).val();
    let data_min = $(this).data('value');
    let uid = $(this).data('uid');
    // console.log(minutes);
    // console.log(data_min);
    // alert();
    // return;

    if(minutes != data_min){
      $.ajax({
        url: base_url+'settings/Clockinout_settings/update_minutes',
        type: 'post',
        data:{minutes, data_min, uid},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            gen_clockinout_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }
  });

  $(document).on('click', '.btn_del', function(){
    var delid = $(this).data('delid');
    var rules = $(this).data('rules');

    $('#delete_modal').modal();
    $('.delid').val(delid);
    $('.info_desc').html(rules);
  });

  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    if($('.delid').val() != ""){
      $.ajax({
        url: base_url+'settings/Clockinout_settings/delete',
        type: 'post',
        contentType: false,
        processData: false,
        data: new FormData(this),
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_yes').attr('disabled');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_yes').prop('disabled', false);
          if(data.success == 1){
            $('#delete_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_clockinout_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }
  });

  $(document).on('click', '.btn_update', function(){
    var rules = $(this).data('rules');
    var desc = $(this).data('desc');
    var uid = $(this).data('uid');

    $('#rules').val(rules);
    $('#description').val(desc);
    $('#update_id').val(uid);
    $('#update_modal').modal();
  });

  $(document).on('submit', '#update_form', function(e){
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
        url: base_url+'settings/Clockinout_settings/update',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_update_save').attr('disabled');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_update_save').prop('disabled', false);
          if(data.success == 1){
            $('#update_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_clockinout_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  });

  $(document).on('submit', '#add_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";

    $('.add_rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.add_rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Clockinout_settings/create',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_add_save').attr('disabled');
        },
        success: function(data){
          $('#btn_add_save').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_clockinout_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    searchValue.filter = filter_by;
    // for single date
    if($("#"+filter_by).hasClass('single_search')){
      searchValue.search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('dual_search')){
      searchValue.from = $('#'+filter_by).children().find('.from').val();
      searchValue.to = $('#'+filter_by).children().find('.to').val();
    }

    // console.log(searchValue);
    // return;

    gen_clockinout_tbl(JSON.stringify(searchValue));

  });
});
