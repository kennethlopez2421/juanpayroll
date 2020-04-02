
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

  function gen_custom_sched_tbl(search){
    var custom_sched_tbl = $('#custom_sched_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Work_schedule/get_work_schedule_json',
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

  function gen_approved_ws_tbl(search){
    var custom_sched_tbl = $('#approved_ws_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Work_schedule/get_approved_work_schedule_json',
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

  function gen_certified_ws_tbl(search){
    var custom_sched_tbl = $('#certified_ws_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Work_schedule/get_certified_work_schedule_json',
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

  gen_custom_sched_tbl(JSON.stringify(searchValue));

  // ADD

  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  });

  $(document).on('change', '#department', function(){
    const thiss = $(this);
    $('#employee').html('<option value="">------</option>');
    if(thiss.val() != ''){
      const dept_id = thiss.val();
      $.ajax({
        url: base_url+'transactions/Work_schedule/get_employee_by_dept',
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

  $(document).on('change', '#employee', function(){
    const emp_idno = $(this).val();

  });

  $(document).on('blur', '.in', function(){
    let tin = $(this).val();
    let tout = $(this).parent('td').siblings('td').find('.out').val();
    let total = 0;

    if(tout != ""){
      tin = converToTime(tin);
      tout = converToTime(tout);
      total = (tin > tout) ? ((tout + 86400) - tin) / 3600 : (tout - tin) / 3600 ;
      $(this).parent('td').siblings('td').find('.total').val(total);
    }
  });

  $(document).on('blur', '.out', function(){
    let tout = $(this).val();
    let tin = $(this).parent('td').siblings('td').find('.in').val();

    if(tout != ""){
      tin = converToTime(tin);
      tout = converToTime(tout);
      total = (tin > tout) ? ((tout + 86400) - tin) / 3600 : (tout - tin) / 3600 ;
      $(this).parent('td').siblings('td').find('.total').val(total);
    }
  });

  $(document).on('blur', 'input[type=time]', function(){
    if($(this).val() == ""){
      $(this).parent('td').siblings('td').find('input[type=time]').val('');
      $(this).parent('td').siblings('td').find('.total, .edit_total').val('');
    }
  });

  $(document).on('submit', '#custom_sched_form', function(e){
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
        url: base_url+'transactions/Work_schedule/create',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_custom_sched_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('blur', '.init', function(){
    let error = 0;
    $('.init').each(function(){
      if($(this).val() == ""){
        error += 1;
      }
    });

    if(error == 0){
      Swal.fire({
        title: 'Do you want to set all schedule like this ?',
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if(result.value){
          const ti = $('#mon_ti').val();
          const to = $('#mon_to').val();
          const bi = $('#mon_bi').val();
          const bo = $('#mon_bo').val();
          const total = $('#mon_total').val();

          $('.in').val(ti);
          $('.out').val(to);
          $('.bi').val(bi);
          $('.bout').val(bo);
          $('.total').val(total);
        }
      })
    }
  });

  // UPDATE

  $(document).on('click', '.btn_update', function(){
    let uid = $(this).data('uid');
    let department_id = $(this).data('department_id');
    let employee_idno = $(this).data('employee_idno');
    temp_id = employee_idno;
    let date_from = $(this).data('date_from');
    let date_to = $(this).data('date_to');
    let work_sched = $(this).data('work_sched');
    let status = $(this).data('status');
    let mon = work_sched.mon;
    let tue = work_sched.tue;
    let wed = work_sched.wed;
    let thu = work_sched.thu;
    let fri = work_sched.fri;
    let sat = work_sched.sat;
    let sun = work_sched.sun;

    $('#uid').val(uid);
    $('#edit_department option[value="'+department_id+'"]').prop('selected', true).trigger('change');


    $('#edit_start_date').val(date_from);
    $('#edit_end_date').val(date_to);

    $(`#edit_mon_ti`).val(mon[0]);
    $(`#edit_mon_to`).val(mon[1]);
    $(`#edit_mon_bi`).val(mon[3]);
    $(`#edit_mon_bo`).val(mon[4]);
    $(`#edit_mon_total`).val(mon[2]);

    $(`#edit_tue_ti`).val(tue[0]);
    $(`#edit_tue_to`).val(tue[1]);
    $(`#edit_tue_bi`).val(tue[3]);
    $(`#edit_tue_bo`).val(tue[4]);
    $(`#edit_tue_total`).val(tue[2]);

    $(`#edit_wed_ti`).val(wed[0]);
    $(`#edit_wed_to`).val(wed[1]);
    $(`#edit_wed_bi`).val(wed[3]);
    $(`#edit_wed_bo`).val(wed[4]);
    $(`#edit_wed_total`).val(wed[2]);

    $(`#edit_thu_ti`).val(thu[0]);
    $(`#edit_thu_to`).val(thu[1]);
    $(`#edit_thu_bi`).val(thu[3]);
    $(`#edit_thu_bo`).val(thu[4]);
    $(`#edit_thu_total`).val(thu[2]);

    $(`#edit_fri_ti`).val(fri[0]);
    $(`#edit_fri_to`).val(fri[1]);
    $(`#edit_fri_bi`).val(fri[3]);
    $(`#edit_fri_bo`).val(fri[4]);
    $(`#edit_fri_total`).val(fri[2]);

    $(`#edit_sat_ti`).val(sat[0]);
    $(`#edit_sat_to`).val(sat[1]);
    $(`#edit_sat_bi`).val(sat[3]);
    $(`#edit_sat_bo`).val(sat[4]);
    $(`#edit_sat_total`).val(sat[2]);

    $(`#edit_sun_ti`).val(sun[0]);
    $(`#edit_sun_to`).val(sun[1]);
    $(`#edit_sun_bi`).val(sun[3]);
    $(`#edit_sun_bo`).val(sun[4]);
    $(`#edit_sun_total`).val(sun[2]);

    // return;
    (status == 'certify') ? $('#btn_update').hide() : $('#btn_update').show();
    $('#update_modal').modal();

  });

  $(document).on('change', '#edit_department', function(){
    const thiss = $(this);
    $('#edit_employee').html('<option value="">------</option>');
    if(thiss.val() != ''){
      const dept_id = thiss.val();
      $.ajax({
        url: base_url+'transactions/Work_schedule/get_employee_by_dept',
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
            if(temp_id != ''){
              $('#edit_employee option[value="'+temp_id+'"]').prop('selected', true).trigger('change');
            }
          }else{
            notificationError('Error', data.message);
            $('#edit_employee option[value=""]').prop('selected', true);
            $('#edit_employee').trigger('change');
            $('#edit_employee').attr('disabled' ,true);
          }
        }
      });
    }else{
      // $('#department').trsigger('change');
      $('#edit_employee option[value=""]').prop('selected', true);
      $('#edit_employee').trigger('change');
      $('#edit_employee').attr('disabled' ,true);
    }
  });

  $(document).on('blur', '.edit_in', function(){
    let tin = $(this).val();
    let tout = $(this).parent('td').siblings('td').find('.edit_out').val();
    let total = 0;

    if(tout != ""){
      tin = converToTime(tin);
      tout = converToTime(tout);
      total = (tin > tout) ? ((tout + 86400) - tin) / 3600 : (tout - tin) / 3600 ;
      $(this).parent('td').siblings('td').find('.edit_total').val(total);
    }
  });

  $(document).on('blur', '.edit_out', function(){
    let tout = $(this).val();
    let tin = $(this).parent('td').siblings('td').find('.edit_in').val();

    if(tout != ""){
      tin = converToTime(tin);
      tout = converToTime(tout);
      total = (tin > tout) ? ((tout + 86400) - tin) / 3600 : (tout - tin) / 3600 ;
      $(this).parent('td').siblings('td').find('.edit_total').val(total);
    }
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
        url: base_url+'transactions/Work_schedule/update',
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
            gen_custom_sched_tbl(JSON.stringify(searchValue));
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
  $(document).on('click', '.btn_delete', function(){
    var delid = $(this).data('delid');
    $('.ws_id').val(delid);

    $('#delete_modal').modal();
  });

  $(document).on('click', '#btn_del_yes', function(){
    var delid = $('.ws_id').val();
    $.ajax({
      url: base_url+'transactions/Work_schedule/delete',
      type: 'post',
      data:{delid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_custom_sched_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '.btn_reject', function(){
    let delid = $(this).data('delid');
    $('#reject_id').val(delid);
    $('#reject_modal').modal();
  })

  $(document).on('submit', '#reject_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    let tab = $('.nav-link.active').get(0).id;

    $('.reject_rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.reject_rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'transactions/Work_schedule/reject',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_reject_save').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_reject_save').prop('disabled', false);
          if(data.success == 1){
            $('#reject_modal').modal('hide');
            notificationSuccess('Success', data.message);
            switch (tab) {
              case 'waiting_nav':
                gen_custom_sched_tbl(JSON.stringify(searchValue));
                break;
              case 'approved_nav':
                gen_approved_ws_tbl(JSON.stringify(searchValue));
                break;
              case 'certified_nav':
                gen_certified_ws_tbl(JSON.stringify(searchValue));
                break;
              default:
                gen_custom_sched_tbl(JSON.stringify(searchValue));

            }
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  })

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
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

    switch (tab) {
      case 'waiting_nav':
        gen_custom_sched_tbl(JSON.stringify(searchValue));
        break;
      case 'approved_nav':
        gen_approved_ws_tbl(JSON.stringify(searchValue));
        break;
      case 'certified_nav':
        gen_certified_ws_tbl(JSON.stringify(searchValue));
        break;
      default:
        gen_custom_sched_tbl(JSON.stringify(searchValue));

    }

  });

  // NAVIGATION
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
        gen_custom_sched_tbl(JSON.stringify(searchValue));
        break;
      case 'approved':
        // $(`${id}`).tab('show');
        $('.btn_batch').hide();
        $('.btn_batch_certify').show();
        gen_approved_ws_tbl(JSON.stringify(searchValue));
        break;
      case 'certified':
        // $(`${id}`).tab('show');
        $('.btn_batch').hide();
        gen_certified_ws_tbl(JSON.stringify(searchValue));
        break;
      default:

    }
  });

  $(document).on('click', '.btn_status', function(){
    let status = $(this).data('status');
    let ws_id = $(this).data('ws_id');

    $.ajax({
      url: base_url+'transactions/Work_schedule/update_status',
      type: 'post',
      data:{status, ws_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          switch (status) {
            case 'approve':
              gen_custom_sched_tbl(JSON.stringify(searchValue));
              break;
            case 'certify':
              gen_approved_ws_tbl(JSON.stringify(searchValue));
              break;
            default:
          }
        }else{
          notificationError('Error', data.message);
        }
      }
    });
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
    const wo_status = (status == 'waiting') ? 'approve' : 'certify';
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
            url: base_url+'transactions/Work_schedule/update_batch_status',
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
                  case 'approve':
                    gen_custom_sched_tbl(JSON.stringify(searchValue));
                    break;
                  case 'certify':
                    gen_approved_ws_tbl(JSON.stringify(searchValue));
                    break;
                  default:
                    gen_custom_sched_tbl(JSON.stringify(searchValue));
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
