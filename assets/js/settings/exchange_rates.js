$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function gen_exchange_rate_tbl(search){
    var exchange_rate_tbl = $('#exchange_rate_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Exchange_rates/get_exchange_rates_json',
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

  gen_exchange_rate_tbl('');

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_id":
  			$('.filter_div').hide("slow");
  			$('#divEmpID').show("slow");
  			$('#divEmpID').addClass('active');
  			break;
  		case "by_dept":
  			$('.filter_div').hide("slow");
  			$('#divDept').show("slow");
  			$('#divDept').addClass('active');
  			break;
  		case "by_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
  			break;
  		case "by_amount":
  			$('.filter_div').hide("slow");
  			$('#divAmount').show("slow");
  			$('#divAmount').addClass('active');
  			break;
  		default:

  	}

  });
  // CALL ADD MODAL
  $(document).on('click', '#btn_add', function(){
    $('#add_modal').modal();
  });
  // CALL EDIT MODAL
  $(document).on('click', '.btn_edit', function(){
    var uid = $(this).data('uid');
    var code = $(this).data('code');
    var name = $(this).data('name');
    var rate = $(this).data('rate');

    $('#uid').val(uid);
    $('#edit_currency_code').val(code);
    $('#edit_currency_name').val(name);
    $('#edit_exchange_rate').val(rate);

    $('#edit_modal').modal();
  });
  // CALL DELETE MODAL
  $(document).on('click', '.btn_delete', function(){
    var delid = $(this).data('delid');
    var code = $(this).data('code');
    var name = $(this).data('name');
    $('#del_text').text(`${code} ( ${name} )`);
    $('#delid').val(delid);

    $('#delete_modal').modal();
  });
  // SUBMIT ADD MODAL
  $(document).on('submit', '#ex_rate_form', function(e){
    e.preventDefault();
    var ex_rate_form = new FormData(this);
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
        url: base_url+'settings/Exchange_rates/create',
        type: 'post',
        data: ex_rate_form,
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
            notificationSuccess('Success',data.message);
            gen_exchange_rate_tbl('');
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // SUBMIT EDIT MODAL
  $(document).on('submit', '#edit_ex_rate_form', function(e){
    e.preventDefault();
    var edit_ex_rate_form = new FormData(this);
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
        url: base_url+'settings/Exchange_rates/update',
        type: 'post',
        data: edit_ex_rate_form,
        processData: false,
        contentType: false,
        beforeSend: function(){
          $('#btn_update').attr('disabled', true);
          $.LoadingOverlay('show');
        },
        success: function(data){
          $('#btn_update').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#edit_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_exchange_rate_tbl('');
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // SUBMIT DELETE MODAL
  $(document).on('submit', '#delete_ex_rate_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'settings/Exchange_rates/delete',
      type: 'post',
      data: new FormData(this),
      processData: false,
      contentType: false,
      beforeSend: function(){
        $('#delete_modal').attr('disabled', true);
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#delete_modal').prop('disabled', false);
        if(data.success == 1){
          $('#delete_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_exchange_rate_tbl('');
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

  $(document).on('click', '#searchButton', function(){
    var searchArea = $('#searchArea').val();
    gen_exchange_rate_tbl(searchArea);
  });
});
