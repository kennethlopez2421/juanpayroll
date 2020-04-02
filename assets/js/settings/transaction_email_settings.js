$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };
  let approver_arr;
  let certifier_arr;

  function gen_tran_email_tbl(search){
    var tran_email_tbl = $('#tran_email_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Transaction_email_settings/get_email_settings_json',
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

  gen_tran_email_tbl(JSON.stringify(searchValue));

  // ADD
  $(document).on('click','#btn_add_modal', function(){
    $('#cont_nav option[value = ""]').prop('selected', true).trigger('change');
    $('#dept option[value = ""]').prop('selected', true).trigger('change');
    // $('#approver').html('<option value="">------</option>');
    // $('#certifier').html('<option value="">------</option>');
    $('#add_modal').modal();
  });

  $(document).on('change', '#dept', function(){
    $('#approver').html('');
    $('#certifier').html('');
    let dept = $(this).val();
    if(dept == ""){
      $('#approver').attr('disabled');
      $('#certifier').attr('disabled');
      console.log('pasok');
      return ;
    }

    $.ajax({
      url: base_url+'settings/Transaction_email_settings/get_employees',
      type: 'post',
      data:{dept},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#approver').removeAttr('disabled');
          $('#certifier').removeAttr('disabled');
          $.each(data.employees, function(i, val){
            $('#approver').append(`<option value="${val['employee_idno']}">(${val['position']})${val['fullname']}</option>`);
            $('#certifier').append(`<option value="${val['employee_idno']}">(${val['position']})${val['fullname']}</option>`);
          });
        }else{
          $('#approver').attr('disabled');
          $('#certifier').attr('disabled');
        }
      }
    });
  });

  $(document).on('submit', '#add_form', function(e){
    e.preventDefault();

    var error = 0;
    var errorMsg = "";
    var add_form = new FormData(this);
    add_form.append('approver2', $('#approver').val());
    add_form.append('certifier2', $('#certifier').val());
    // console.log($('#approver').val());

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
        url: base_url+'settings/Transaction_email_settings/create',
        type: 'post',
        data: add_form,
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_save').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_save').prop('disabled', false);
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_tran_email_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  // EDIT
  $(document).on('click', '.btn_edit_modal', function(){
    let uid = $(this).data('uid');
    let nav_id = $(this).data('nav_id');
    let dept_id = $(this).data('dept_id');
    let approver = $(this).data('approver');
    let certifier = $(this).data('certifier');
    approver_arr = approver.split(',');
    certifier_arr = certifier.split(',');
    $('#uid').val(uid);
    $('#edit_cont_nav option[value="'+nav_id+'"]').prop('selected', true).trigger('change');
    $('#edit_dept option[value="'+dept_id+'"]').prop('selected', true).trigger('change');

    $('#edit_modal').modal();
  });

  $(document).on('change', '#edit_dept', function(){
    $('#edit_approver').html('');
    $('#edit_certifier').html('');
    let dept = $(this).val();
    if(dept == ""){
      $('#edit_approver').attr('disabled');
      $('#edit_certifier').attr('disabled');
      return ;
    }

    $.ajax({
      url: base_url+'settings/Transaction_email_settings/get_employees',
      type: 'post',
      data:{dept},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#edit_approver').removeAttr('disabled');
          $('#edit_certifier').removeAttr('disabled');
          $.each(data.employees, function(i, val){
            $('#edit_approver').append(`<option value="${val['employee_idno']}">(${val['position']})${val['fullname']}</option>`);
            $('#edit_certifier').append(`<option value="${val['employee_idno']}">(${val['position']})${val['fullname']}</option>`);
          });

          approver_arr.forEach((data) => {
            $('#edit_approver option[value="'+data+'"]').prop('selected', true).trigger('change');
          });

          certifier_arr.forEach((data) => {
            $('#edit_certifier option[value="'+data+'"]').prop('selected', true).trigger('change');
          });

          approver_arr = [];
          certifier_arr = [];
        }else{
          $('#edit_approver').attr('disabled');
          $('#edit_certifier').attr('disabled');
        }
      }
    });
  });

  $(document).on('submit', '#edit_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    var edit_form = new FormData(this);
    edit_form.append('approver', $('#edit_approver').val())
    edit_form.append('certifier', $('#edit_certifier').val())

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
        url: base_url+'settings/Transaction_email_settings/update',
        type: 'post',
        data: edit_form,
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_update').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_update').prop('disabled', false);
          if(data.success == 1){
            $('#edit_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_tran_email_tbl(JSON.stringify(searchValue));
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
    let cn_name = $(this).data('cn_name');
    let dept_name = $(this).data('dept_name');

    $('.info_desc').html('('+cn_name+')'+dept_name+'');
    $('#delid').val(delid);

    $('#delete_modal').modal();
  });

  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'settings/Transaction_email_settings/delete',
      type: 'post',
      data: new FormData(this),
      contentType: false,
      processData: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_delete').attr('disabled', true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#btn_delete').prop('disabled', false);
        if(data.success == 1){
          $('#delete_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_tran_email_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  // SEARCH FILTER
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_tran":
  			$('.filter_div').hide("slow");
  			$('#divTran').show("slow");
  			$('#divTran').addClass('active');
  			break;
  		case "by_dept":
  			$('.filter_div').hide("slow");
  			$('#divDept').show("slow");
  			$('#divDept').addClass('active');
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

    gen_tran_email_tbl(JSON.stringify(searchValue));
  });
});
