$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };
  var temp_id = "";

  function gen_offset_tbl(search){
    var offset_tbl = $('#offset_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Offset/get_offset_json',
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

  function gen_offset_approved_tbl(search){
    var offset_approved_tbl = $('#offset_approved_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Offset/get_offset_approved_json',
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

  function gen_offset_certified_tbl(search){
    var offset_certified_tbl = $('#offset_certified_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Offset/get_offset_certified_json',
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

  gen_offset_tbl(JSON.stringify(searchValue));

  // ADD

  $(document).on('click', '#btn_add_modal', function(){
    $('#department option[value=""]').prop('selected', true).trigger('change');
    $('#employee option[value=""]').prop('selected', true).trigger('change');
    $('#offset_type option[value=""]').prop('selected', true).trigger('change');
    $('#total_offset_bal').val('0');
    $('#offset_bal').val('0');
    $('#offset_bal').css("border", "1px solid gainsboro");

    $('#add_modal').modal();
  });

  $(document).on('submit', '#add_offset_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    let date_rendered = $('#date_rendered').val();
    let employee = $('#employee').val();
    let offset_type = $('#offset_type').val();
    let offset_bal = $('#offset_bal').val();

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

    if($('#offset_bal').val() == "0"){
      error = 1;
      errorMsg = "Invalid Offset minutes.";
      $('#offset_bal').css('border', '1px solid #ef4131');
    }else{
      $('#offset_bal').css('border', '1px solid gainsboro');
    }

    if(error == 0){
      const addForm = new FormData(this);
      $.ajax({
        url: base_url+'transactions/Offset/check_filed_offset',
        type: 'post',
        data:{offset_type, date_rendered, employee},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            $('#offset_bal').val(data.offset_min);
            if(parseInt(offset_bal) > parseInt(data.offset_min)){
              notificationError('Error', "You're filling an offset more than you're offset type on "+date_rendered);
            }else{
              $.ajax({
                url: base_url+'transactions/Offset/create',
                type: 'post',
                data: addForm,
                processData: false,
                contentType: false,
                beforeSend: function(){
                  $.LoadingOverlay('show');
                  $('#btn_save_addform').attr('disabled', true);
                },
                success: function(data){
                  $.LoadingOverlay('hide');
                  $('#btn_save_addform').prop('disabled', false);
                  if(data.success == 1){
                    $('#add_modal').modal('hide');
                    notificationSuccess('Success', data.message);
                    gen_offset_tbl(JSON.stringify(searchValue));
                  }else{
                    notificationError('Error', data.message);
                  }
                }
              });
            }

          }else if(data.success == 2){
            notificationError('Warning!', data.message);
            $('#offset_bal').val(data.offset_min);
          }else{
            notificationError('Error', data.message);
          }
        }
      });

    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('change', '#department', function(){
    const thiss = $(this);
    $('#employee').html('<option value="">------</option>');
    if(thiss.val() != ''){
      const dept_id = thiss.val();
      $.ajax({
        url: base_url+'transactions/Offset/get_employee_by_dept',
        type: 'post',
        data:{ dept_id },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#employee').removeAttr('disabled');
            $.each(data.emps, function(i, val){
              $('#employee').append('<option value="'+val.employee_idno+'">'+val.fullname+'</option>');
            });
          }else{
            notificationError('Error', data.message);
            $('#employee option[value=""]').prop('selected', true);
            $('#employee').trigger('change');
            $('#employee').attr('disabled' ,true);
          }
        }
      });
    }else{
      // $('#department').trigger('change');
      $('#employee option[value=""]').prop('selected', true);
      $('#employee').trigger('change');
      $('#employee').attr('disabled' ,true);
    }
  });

  $(document).on('change', '#offset_type', function(){
    let offset_type = $('#offset_type').val();
    let employee = $('#employee').val();
    let date = $('#date_rendered').val();

    if(employee != "" && date != ""){
      if(offset_type == "" || employee == ""){
        notificationError('Error', "Please select offset type and Employee");
        return;
      }

      $.ajax({
        url: base_url+'transactions/Offset/check_filed_offset',
        type: 'post',
        data:{offset_type, date_rendered: date, employee},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            // notificationSuccess('Success',data.message);
            $('#offset_bal').val(data.offset_min);
          }else if(data.success == 2){
            notificationError('Warning!', data.message);
            $('#offset_bal').val(data.offset_min);
          }else{
            notificationError('Error', data.message);
          }
        }
      });

    }

  });

  $(document).on('change', '#date_rendered', function(){
    let offset_type = $('#offset_type').val();
    let employee = $('#employee').val();
    let date = $(this).val();

    if(offset_type == "" || employee == ""){
      notificationError('Error', "Please select offset type and Employee");
      return;
    }

    $.ajax({
      url: base_url+'transactions/Offset/check_filed_offset',
      type: 'post',
      data:{offset_type, date_rendered: date, employee},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          // notificationSuccess('Success',data.message);
          $('#offset_bal').val(data.offset_min);
        }else if(data.success == 2){
          notificationError('Warning!', data.message);
          $('#offset_bal').val(data.offset_min);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('change', '#employee', function(){
    if($('#department').val() != ""){
      let emp_idno = $(this).val();
      $.ajax({
        url: base_url+'transactions/Offset/get_offset_bal',
        type: 'post',
        data:{emp_idno},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            $('#total_offset_bal').val(data.offset_bal);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }
  });

  // EDIT

  $(document).on('click', '.btn_edit_modal', function(){
    let uid = $(this).data('uid');
    let date_rendered = $(this).data('date_rendered');
    let offset_min = $(this).data('offset_min');
    let offset_type = $(this).data('offset_type');
    let deptId = $(this).data('deptid');
    let emp_idno = $(this).data('emp_idno');
    temp_id = emp_idno;
    let status = $(this).data('status');
    $('#edit_offset_bal').css("border", "1px solid gainsboro");
    $('#btn_update_offset').show();
    // $('.rq2').each(function(){
    //   $(this).removeAttr('disabled');
    // });
    if(status == 'certified'){
      $('#btn_update_offset').hide();
      // $('.rq2').each(function(){
      //   $(this).attr('disabled');
      // });
    }

    $.ajax({
      url: base_url+'transactions/Offset/get_employee_by_dept',
      type: 'post',
      data:{ dept_id: deptId },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#employee').removeAttr('disabled');
          $.each(data.emps, function(i, val){
            $('#edit_employee').append('<option value="'+val.employee_idno+'">'+val.fullname+'</option>');
          });

          $('#uid').val(uid);
          $('#edit_department option[value="'+deptId+'"]').prop('selected', true);
          $('#edit_employee option[value="'+emp_idno+'"]').prop('selected', true);
          $('#edit_date_rendered').val(date_rendered);
          $('#edit_offset_bal').val(offset_min);
          $('#edit_offset_type option[value="'+offset_type+'"]').prop('selected', true);
          $('#edit_employee').trigger('change');
          $('#edit_offset_type').trigger('change');
          $('#edit_department').trigger('change');

          $('#edit_modal').modal();
        }else{
          notificationError('Error', data.message);
          $('#edit_employee option[value=""]').prop('selected', true);
          $('#edit_employee').trigger('change');
          $('#edit_employee').attr('disabled' ,true);
        }
      }
    });


  });

  $(document).on('submit', '#edit_offset_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    let date_rendered = $('#edit_date_rendered').val();
    let employee = $('#edit_employee').val();
    let offset_type = $('#edit_offset_type').val();
    let offset_bal = $('#edit_offset_bal').val();

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

    if($('#edit_offset_bal').val() == "0"){
      error = 1;
      errorMsg = "Invalid Offset minutes.";
      $('#edit_offset_bal').css('border', '1px solid #ef4131');
    }else{
      $('#edit_offset_bal').css('border', '1px solid gainsboro');
    }

    if(error == 0){
      const update_form = new FormData(this);
      $.ajax({
        url: base_url+'transactions/Offset/check_filed_offset',
        type: 'post',
        data:{offset_type, date_rendered, employee},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            $('#offset_bal').val(data.offset_min);
            if(parseInt(offset_bal) > parseInt(data.offset_min)){
              notificationError('Error', "You're filling an offset more than you're offset type on "+date_rendered);
            }else{
              $.ajax({
                url: base_url+'transactions/Offset/update',
                type: 'post',
                data: update_form,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $.LoadingOverlay('show');
                  $('#btn_update_offset').attr('disabled', true);
                },
                success: function(data){
                  $.LoadingOverlay('hide');
                  $('#btn_update_offset').prop('disabled', false);
                  if(data.success == 1){
                    $('#edit_modal').modal('hide');
                    notificationSuccess('Success', data.message);
                    gen_offset_tbl(JSON.stringify(searchValue));
                  }else{
                    notificationError('Error', data.message);
                  }
                }
              });
            }

          }else if(data.success == 2){
            notificationError('Warning!', data.message);
            $('#offset_bal').val(data.offset_min);
          }else{
            notificationError('Error', data.message);
          }
        }
      });

    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('change', '#edit_department', function(){
    const thiss = $(this);
    $('#edit_employee').html('<option value="">------</option>');
    if(thiss.val() != ''){
      const dept_id = thiss.val();
      $.ajax({
        url: base_url+'transactions/Offset/get_employee_by_dept',
        type: 'post',
        data:{ dept_id },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#edit_employee').removeAttr('disabled');
            $.each(data.emps, function(i, val){
              $('#edit_employee').append('<option value="'+val.employee_idno+'">'+val.fullname+'</option>');
            });
            $('#edit_employee option[value="'+temp_id+'"]').prop('selected', true);
          }else{
            notificationError('Error', data.message);
            $('#edit_employee option[value=""]').prop('selected', true);
            $('#edit_employee').trigger('change');
            $('#edit_employee').attr('disabled' ,true);
            $('#edit_offset_bal').val('0');
          }
        }
      });
    }else{
      // $('#department').trigger('change');
      $('#edit_employee option[value=""]').prop('selected', true);
      $('#edit_employee').trigger('change');
      $('#edit_employee').attr('disabled' ,true);
      $('#edit_offset_bal').val('0');
    }
  });

  $(document).on('change', '#edit_employee', function(){
    let emp_idno = $(this).val();
    $.ajax({
      url: base_url+'transactions/Offset/get_offset_bal',
      type: 'post',
      data:{emp_idno},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          $('#edit_total_offset_bal').val(data.offset_bal);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('change', '#edit_offset_type', function(){
    let offset_type = $(this).val();
    let employee = $('#edit_employee').val();
    let date = $('#edit_date_rendered').val();
    if(employee != "" && date != "" && offset_type != ""){
      if(offset_type == "" || employee == ""){
        notificationError('Error', "Please select offset type and Employee");
        return;
      }

      $.ajax({
        url: base_url+'transactions/Offset/check_filed_offset',
        type: 'post',
        data:{offset_type, date_rendered: date, employee},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            // notificationSuccess('Success',data.message);
            // $('#edit_offset_bal').val(data.offset_min);
          }else if(data.success == 2){
            notificationError('Warning!', data.message);
            $('#edit_offset_bal').val(data.offset_min);
          }else{
            notificationError('Error', data.message);
          }
        }
      });

    }

  });

  $(document).on('change', '#edit_date_rendered', function(){
    let offset_type = $('#edit_offset_type').val();
    let employee = $('#edit_employee').val();
    let date = $(this).val();

    if(offset_type == "" || employee == ""){
      notificationError('Error', "Please select offset type and Employee");
      return;
    }

    $.ajax({
      url: base_url+'transactions/Offset/check_filed_offset',
      type: 'post',
      data:{offset_type, date_rendered: date, employee},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          // notificationSuccess('Success',data.message);
          // $('#edit_offset_bal').val(data.offset_min);
        }else if(data.success == 2){
          notificationError('Warning!', data.message);
          $('#edit_offset_bal').val(data.offset_min);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  // DELETE

  $(document).on('click', '.btn_reject_modal', function(){
    let delid = $(this).data('delid');
    $('#delid').val(delid);

    $('#reject_modal').modal();
  });

  $(document).on('submit', '#reject_offset_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";

    $('.rq3').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq3').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'transactions/Offset/reject',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_reject_offset').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_reject_offset').prop('disabled', false);
          if(data.success == 1){
            $('#reject_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_offset_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  // NAVIGATON

  $(document).on('click', '.nav-link', function(){
    let stype = $(this).data('stype');
    let id = $(this).attr('href');
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    searchValue  = {
      filter: "",
      search: "",
      from: "",
      to: ""
    };

    switch (stype) {
      case 'waiting':
        // $(`${id}`).tab('show');
        $('.btn_batch').hide();
        $('.btn_batch_approve').show();
        gen_offset_tbl(JSON.stringify(searchValue));
        break;
      case 'approved':
        // $(`${id}`).tab('show');
        $('.btn_batch').hide();
        $('.btn_batch_certify').show();
        gen_offset_approved_tbl(JSON.stringify(searchValue));
        break;
      case 'certified':
        // $(`${id}`).tab('show');
        $('.btn_batch').hide();
        gen_offset_certified_tbl(JSON.stringify(searchValue));
        break;
      default:

    }
  });

  $(document).on('click', '.btn_status', function(){
    let status = $(this).data('status');
    let offset_id = $(this).data('offset_id');

    $.ajax({
      url: base_url+'transactions/Offset/update_status',
      type: 'post',
      data:{status, offset_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          switch (status) {
            case 'approved':
              gen_offset_tbl(JSON.stringify(searchValue));
              break;
            case 'certified':
              gen_offset_approved_tbl(JSON.stringify(searchValue));
              break;
            default:
          }
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
  		case "by_id":
  			$('.filter_div').hide("slow");
  			$('#divEmpID').show("slow");
  			$('#divEmpID').addClass('active');
  			break;
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_dept":
  			$('.filter_div').hide("slow");
  			$('#divDept').show("slow");
  			$('#divDept').addClass('active');
  			break;
  		case "by_date_filed":
  			$('.filter_div').hide("slow");
  			$('#divDateFiled').show("slow");
  			$('#divDateFiled').addClass('active');
  			break;
  		case "by_date_rendered":
  			$('.filter_div').hide("slow");
  			$('#divDateRendered').show("slow");
  			$('#divDateRendered').addClass('active');
  			break;
  		default:

  	}

  });

  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    let tab = $('.nav-link.active').get(0).id;
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

    // console.log(tab);
    // return false;

    switch (tab) {
      case 'waiting_nav':
        gen_offset_tbl(JSON.stringify(searchValue));
        break;
      case 'approved_nav':
        gen_offset_approved_tbl(JSON.stringify(searchValue));
        break;
      case 'certified_nav':
        gen_offset_certified_tbl(JSON.stringify(searchValue));
        break;
      default:
        gen_offset_tbl(JSON.stringify(searchValue));

    }

  });

  $(document).on('click', '.select_all', function(){
    var thiss = $(this);
    var checked = thiss.is(':checked');
    const status = $('.nav-link.active').data('stype')
    // console.log(status, checked);
    if(checked == true){
      $(`.${status}_select`).prop('checked', true).trigger('change');
    }else{
      $(`.${status}_select`).prop('checked', false).trigger('change');
    }

  });

  $(document).on('click', '.btn_batch', function(){
    const batch = [];
    const status = $('.nav-link.active').data('stype');
    const wo_status = (status == 'waiting') ? 'approved' : 'certified';
    const batch_status = (status == 'waiting') ? 'approve' : 'certify'
    $.each($(`.${status}_select:checked`), function(){ batch.push($(this).val())});
    if(batch.length > 0){

      Swal.fire({
        title: 'Are you sure you want to do this batch '+batch_status+'?',
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if(result.value){
          $.ajax({
            url: base_url+'transactions/Offset/update_batch_status',
            type: 'post',
            data:{status: wo_status, batch: batch, batch_status},
            beforeSend: function(){
              $.LoadingOverlay('show');
            },
            success: function(data){
              $.LoadingOverlay('hide');
              if(data.success == 1){
                notificationSuccess('Success', data.message);
                $('.select_all').prop('checked',false).trigger('change');
                switch (wo_status) {
                  case 'approved':
                    gen_offset_tbl(JSON.stringify(searchValue));
                    break;
                  case 'certified':
                    gen_offset_approved_tbl(JSON.stringify(searchValue));
                    break;
                  default:
                    gen_offset_tbl(JSON.stringify(searchValue));
                }

              }else{
                notificationError('Error', data.message);
              }
            }
          });
        }
      });

    }else{
      notificationError('Error', "There's nothing to "+batch_status+". Please try again.");
    }
  });
});
