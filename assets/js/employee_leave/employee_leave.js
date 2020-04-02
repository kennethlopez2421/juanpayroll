$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_waiting_leave_tbl(search){
    var workOrder_tbl = $('#caTable').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7,8], orderable: false}
      ],
      "ajax":{
        url: base_url+'employee_leave/Employee_leave/getleavepays_waiting_json',
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

  function gen_approved_leave_tbl(search){
    var workOrder_tbl = $('#leave_approved_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7,8], orderable: false}
      ],
      "ajax":{
        url: base_url+'employee_leave/Employee_leave/getleavepays_approved_json',
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

  function gen_certified_leave_tbl(search){
    var workOrder_tbl = $('#leave_certified_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7,8], orderable: false}
      ],
      "ajax":{
        url: base_url+'employee_leave/Employee_leave/getleavepays_certified_json',
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

  function gen_rejected_leave_tbl(search){
    var workOrder_tbl = $('#leave_rejected_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7,8], orderable: false}
      ],
      "ajax":{
        url: base_url+'employee_leave/Employee_leave/getleavepays_rejected_json',
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

  function gen_employee_leave_tbl(search){
    var leave_emp_tbl = $('#leave_emp_tbl').DataTable( {
      "processing": true,
      "ordering": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {
          targets: [0,1,2,3,4,5,6],
          orderable: false,
        }
      ],
      "ajax":{
        url: base_url+'employee_leave/Employee_leave/get_employee_leave_json',
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

  function gen_employee_leave_tbl_rejected(search){
    var leave_emp_tbl = $('#leave_emp_tbl').DataTable( {
      "processing": true,
      "ordering": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {
          targets: [0,1,2,3,4,5,6],
          orderable: false,
        }
      ],
      "ajax":{
        url: base_url+'employee_leave/Employee_leave/get_employee_leave_json_rejected',
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

  gen_waiting_leave_tbl(JSON.stringify(searchValue));

  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  });

  $(document).on('change', '#leave_type', function(){
    var leave_type = $(this).val();
    $('.leave_date').datepicker('remove');
    $('.leave_date').datepicker({
      todayBtn: "linked",
      format: 'yyyy-mm-dd',
      todayHighlight: true,
      startDate:'+0d',
      autoclose: true
    }).datepicker("setDate", new Date());
    if(leave_type != ""){
      $.ajax({
        url: base_url+'employee_leave/Employee_leave/get_remaining_leave_type',
        type: 'post',
        data:{leave_type},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            var remaining_leave = data.remaining_leave;
            var filtered = filterArray(parseInt(leave_type),remaining_leave);
            var remaining_leave = (filtered != undefined) ? filtered.days : 0;
            $('#remaining_leave').val(remaining_leave);
            if(data.late_filling == 'yes'){
              $('.leave_date').datepicker('remove');
              $('.leave_date').datepicker(
                {format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
              ).datepicker("setDate", new Date());
            }
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }
  });

  $(document).on('change', '#edit_leave_type', function(){
    var leave_type = $(this).val();
    $('.edit_leave_date').datepicker('remove');
    $('.edit_leave_date').datepicker({
  		todayBtn: "linked",
  		format: 'yyyy-mm-dd',
  		todayHighlight: true,
  		startDate:'+0d',
  		autoclose: true
  	});
    if(leave_type != ""){
      $.ajax({
        url: base_url+'employee_leave/Employee_leave/get_remaining_leave_type',
        type: 'post',
        data:{leave_type},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            var remaining_leave = data.remaining_leave;
            var filtered = filterArray(parseInt(leave_type),remaining_leave);
            var remaining_leave = (filtered != undefined) ? filtered.days : 0;
            $('#edit_remaining_leave').val(remaining_leave);
            if(data.late_filling == 'yes'){
              $('.edit_leave_date').datepicker('remove');
              $('.edit_leave_date').datepicker(
                {format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
              );
            }
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }
  });

  $(document).on('click', '#btn_save_leave_form', function(){
    $('.leave_date').addClass('date_input');
    var error = 0;
    var errorMsg = "";
    var leave_type = $('#leave_type').val();
    var remaining_leave = $('#remaining_leave').val();
    var date_from = $('#date_from2').val();
    var date_to = $('#date_to2').val();
    var reason = $('#reason').val();
    var contact = $('#contact').val();
    var paid = $('#paid').val();
    // var number_of_days = days_between(date_from,date_to);


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
        url: base_url+'employee_leave/Employee_leave/create',
        type: 'post',
        data:{
          leave_type,
          remaining_leave,
          date_from,
          date_to,
          reason,
          contact,
          paid
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success',data.message);
            gen_waiting_leave_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_edit_leave', function(){
    $('#btn_update_leave').show();
    var uid = $(this).data('uid');
    var leave_type = $(this).data('leave_type');
    var remaining = $(this).data('remaining');
    var date_from = $(this).data('date_from');
    var date_to = $(this).data('date_to');
    var reason = $(this).data('reason');
    var contact = $(this).data('contact');
    var paid = $(this).data('paid');
    let status = $(this).data('status');

    $('#uid').val(uid);
    $('#edit_leave_type option[value="'+leave_type+'"]').prop('selected',true);
    $('#edit_remaining_leave').val(remaining);
    $('#edit_date_from2').val(date_from);
    $('#edit_date_to2').val(date_to);
    $('#edit_reason').val(reason);
    $('#edit_contact').val(contact);
    $('#edit_paid option[value="'+paid+'"]').prop('selected',true);
    if(status != 'waiting'){
      $('#btn_update_leave').hide();
    }
    $('#update_modal').modal();
  });

  $(document).on('click', '#btn_update_leave', function(){
    var error = 0;
    var errorMsg = "";
    var uid = $('#uid').val();
    var leave_type = $('#edit_leave_type').val();
    var remaining_leave = $('#edit_remaining_leave').val();
    var date_from = $('#edit_date_from2').val();
    var date_to = $('#edit_date_to2').val();
    var reason = $('#edit_reason').val();
    var contact = $('#edit_contact').val();
    var edit_paid = $('#edit_paid').val();
    // var number_of_days = days_between(date_from,date_to);

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
        url: base_url+'employee_leave/Employee_leave/update',
        type: 'post',
        data:{
          uid,
          leave_type,
          remaining_leave,
          date_from,
          date_to,
          reason,
          contact,
          edit_paid
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#update_modal').modal('hide');
            notificationSuccess('Success',data.message);
            gen_waiting_leave_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_del_leave', function(){
    var delid = $(this).data('delid');
    $('#delete_modal').modal();
    $('#btn_yes').click(function(){
      $.ajax({
        url: base_url+'employee_leave/Employee_leave/delete',
        type: 'post',
        data:{delid},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#delete_modal').modal('hide');
            notificationSuccess('Success',data.message);
            gen_employee_leave_tbl("");
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    })
  });

  $('#searchButton').click(function(){
    let tab = $('.nav-link.active').get(0).id;
    // for date range
    searchValue.from = $('#date_from').val();
    searchValue.to = $('#date_to').val();

    // console.log(tab);
    // return false;

    switch (tab) {
      case 'leave_waiting_nav':
        gen_waiting_leave_tbl(JSON.stringify(searchValue));
        break;
      case 'leave_approved_nav':
        gen_approved_leave_tbl(JSON.stringify(searchValue));
        break;
      case 'leave_certified_nav':
        gen_certified_leave_tbl(JSON.stringify(searchValue));
        break;
      case 'leave_rejected_nav':
        gen_rejected_leave_tbl(JSON.stringify(searchValue));
        break;
      default:
        gen_waiting_leave_tbl(JSON.stringify(searchValue));

    }

  });

  // TABS
  // waiting tab
  $(document).on('click', '#leave_waiting_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    searchValue.from = "";
    searchValue.to = "";
    gen_waiting_leave_tbl(JSON.stringify(searchValue));
    $('#leave_approved_tbl').tab('show');
  });
  // approved tab
  $(document).on('click', '#leave_approved_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    searchValue.from = "";
    searchValue.to = "";
    gen_approved_leave_tbl(JSON.stringify(searchValue));
    $('#leave_approved_tbl').tab('show');
  });
  // certified tab
  $(document).on('click', '#leave_certified_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    searchValue.from = "";
    searchValue.to = "";
    gen_certified_leave_tbl(JSON.stringify(searchValue));
    $('#leave_certified_tab').tab('show');
  });
  // rejected tab
  $(document).on('click', '#leave_rejected_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    searchValue.from = "";
    searchValue.to = "";
    gen_rejected_leave_tbl(JSON.stringify(searchValue));
    $('#leave_rejected_tab').tab('show');
  });

});
