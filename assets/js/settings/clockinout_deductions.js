$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_clock_deductions_tbl(search){
    var clock_deductions_tbl = $('#clock_deductions_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Clockinout_deductions/get_clockinout_deductions_json',
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

  gen_clock_deductions_tbl(JSON.stringify(searchValue));

  // CREATE
  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  })

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
        url: base_url+'settings/Clockinout_deductions/create',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_save').attr('disabled', true);
        },
        success: function(data){
          $('#btn_save').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_clock_deductions_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  })

  // UPDATE

  $(document).on('change', '.status', function(){
    let status = $(this).val();
    let id = $(this).data('uid');
    // console.log(id);
    // return;
    if(status != ""){
      $.ajax({
        url: base_url+'settings/Clockinout_deductions/update_status',
        type: 'post',
        data:{status, id},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            gen_clock_deductions_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }
  });

  $(document).on('click', '.btn_update_modal', function(){
    let uid = $(this).data('uid');
    let type = $(this).data('type');
    let min_from = $(this).data('min_from');
    let min_to = $(this).data('min_to');
    let min_deduct = $(this).data('min_deduct');
    let whours = $(this).data('whours');

    $('#uid').val(uid);
    $('#edit_type option[value="'+type+'"]').prop('selected', true);
    $('#edit_type').trigger('change');
    $('#edit_min_from').val(min_from);
    $('#edit_min_to').val(min_to);
    $('#edit_min_deduct').val(min_deduct);
    $('#edit_whours').val(whours);

    $('#update_modal').modal();
  });

  $(document).on('submit', '#update_form', function(e){
    e.preventDefault();

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
        url: base_url+'settings/Clockinout_deductions/update',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_update').attr('disabled', true);
        },
        success: function(data){
          $('#btn_update').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#update_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_clock_deductions_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  // DELETE

  $(document).on('click', '.btn_del_modal', function(){
    let delid = $(this).data('delid');
    $('#delid').val(delid);
    $('#delete_modal').modal();
  });

  $(document).on('click', '#btn_del_yes', function(){
    let delid = $('#delid').val();
    $.ajax({
      url: base_url+'settings/Clockinout_deductions/delete',
      type: 'post',
      data:{delid},
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_del_yes').attr('disabled', true);
      },
      success: function(data){
        $('#btn_del_yes').prop('disabled', false);
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          gen_clock_deductions_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  // FILTER
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_type":
  			$('.filter_div').hide("slow");
  			$('#divType').show("slow");
  			$('#divType').addClass('active');
  			break;
  		default:
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

    gen_clock_deductions_tbl(JSON.stringify(searchValue));

  });
});
