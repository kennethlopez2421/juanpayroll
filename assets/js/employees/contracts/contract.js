$(function(){
  // alert();
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function converToTime(time){
		time = time.split(":");
		return time[0] * 3600 + time[1] * 60;
	}

  function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  $('#cStart').datepicker({dateFormat: 'yy-mm-dd', autoclose: true, todayHighlight: true});
  $('#cEnd').datepicker({dateFormat: 'yyyy-mm-dd', autoclose: true, todayHighlight: true});

  $(document).on('click', '#btn_newContract', function(){
    var emp_id = $(this).data('empid');
    $('#btn_reset').hide();
    $.ajax({
      url: base_url+'/employees/contracts/Contract/checkcontract',
      type: 'post',
      data: { emp_id },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          // console.log('1');
          $('#confirmModal2').modal();
          $('#btnYes').click(function(){
            if(data.username != "" || data.password != ""){
              $('.credSection').remove();
            }
            $('#confirmModal2').modal('hide');
            $('#newContract_modal').modal();
          });
        }else{
          // console.log('2');
          $('#emp_username').val(data.email);
          $('#newContract_modal').modal();
        }
      }
    });
  });

  $(document).on('click', '#wSchedType2', function(){
    if($(this).val() == "default"){
      $('.divWorkSChedDefault').show('slow');
      $('.workSched_tbl').css("pointer-events", "none");
    }else{
      $('.divWorkSChedDefault').hide('slow');
      $('.workSched_tbl').css("pointer-events", "auto");
      var days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
      for(var x = 0; x < 7; x++){
        $('#'+days[x]+'TimeStart').val('');
        $('#'+days[x]+'TimeEnd').val('');
        $('#'+days[x]+'BreakStart').val('');
        $('#'+days[x]+'BreakEnd').val('');
        $('#'+days[x]+'TimeTotal').val('');
      }
    }
  });

  $(document).on('change', '#wSchedPos', function(){
    // alert($(this).val());
    $.ajax({
      url: base_url+'employees/contracts/Contract/getpossched',
      type: 'post',
      data: {pos_id: $(this).val()},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          var wSched_id = data.pos_sched['id'];
          var wSched = JSON.parse(data.pos_sched['work_sched']);
          var mon = wSched.mon;
          var tue = wSched.tue;
          var wed = wSched.wed;
          var thu = wSched.thu;
          var fri = wSched.fri;
          var sat = wSched.sat;
          var sun = wSched.sun;
          // console.log(wSched);
          var days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
          var days2 = [mon, tue, wed, thu, fri, sat, sun];
          for (var i = 0; i < 7; i++) {
            $('#'+days[i]+'TimeStart').val(days2[i][0]);
            $('#'+days[i]+'TimeEnd').val(days2[i][1]);
            $('#'+days[i]+'BreakStart').val(days2[i][3]);
            $('#'+days[i]+'BreakEnd').val(days2[i][4]);
            $('#'+days[i]+'TimeTotal').val(days2[i][2]);
          }
        }else{
          var days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
          for(var x = 0; x < 7; x++){
            $('#'+days[x]+'TimeStart').val('');
            $('#'+days[x]+'TimeEnd').val('');
            $('#'+days[x]+'BreakStart').val('');
            $('#'+days[x]+'BreakEnd').val('');
            $('#'+days[x]+'TimeTotal').val('');
          }
          notificationError('Error', data.message);
        }
      }
    })
  });

  // prevent workTime lapse
  $(document).on('change', '.timeWorkEnd', function(){
    var timeStart = $(this).parent('.row').children('.timeWorkStart').val();
    var timeEnd = $(this).val();

    // console.log(timeStart);
    // console.log(timeEnd);

    if(timeStart != "" && timeEnd != ""){
      timeStart = converToTime(timeStart);
      timeEnd = converToTime(timeEnd);

      if(timeStart > timeEnd){
        // notificationError('Error', 'Invalid Time Format. Please try again');
        // $(this).val('');
        var timeTotal = ((timeEnd + 86400) - timeStart) / 3600;
        $(this).parent('.row').parent('td').parent('tr').find('.timeTotal').val(timeTotal);
      }else{
        var timeTotal = (timeEnd - timeStart) / 3600 // sec in 1 hour;
        $(this).parent('.row').parent('td').parent('tr').find('.timeTotal').val(timeTotal);
      }
    }else{
      $(this).parent('.row').parent('td').parent('tr').find('.timeTotal').val('');
    }

  });
  $(document).on('change', '.timeWorkStart', function(){
    var timeEnd = $(this).parent('.row').children('.timeWorkEnd').val();
    var timeStart = $(this).val();

    // console.log(timeStart);
    // console.log(timeEnd);

    if(timeStart != "" && timeEnd != ""){
      timeStart = converToTime(timeStart);
      timeEnd = converToTime(timeEnd);

      if(timeStart > timeEnd){
        var timeTotal = ((timeEnd + 86400) - timeStart) / 3600;
        $(this).parent('.row').parent('td').parent('tr').find('.timeTotal').val(timeTotal);
      }else{
        var timeTotal = (timeEnd - timeStart) / 3600 // sec in 1 hour;
        $(this).parent('.row').parent('td').parent('tr').find('.timeTotal').val(timeTotal);
      }
    }else{
      $(this).parent('.row').parent('td').parent('tr').find('.timeTotal').val('');
    }

  });
  // prevent breakTime lapse
  $(document).on('change', '.breakEnd', function(){
    var breakStart = $(this).parent('.row').children('.breakStart').val();
    var breakEnd = $(this).val();

    // console.log(breakStart);
    // console.log(breakEnd);

    if(breakStart != "" && breakEnd != ""){
      breakStart = converToTime(breakStart);
      breakEnd = converToTime(breakEnd);

      // if(breakStart > breakEnd){
      //   notificationError('Error', 'Invalid Time Format. Please try again');
      //   $(this).val('');
      // }
    }

  });
  $(document).on('change', '.breakStart', function(){
    var breakEnd = $(this).parent('.row').children('.breakEnd').val();
    var breakStart = $(this).val();

    // console.log(breakStart);
    // console.log(breakEnd);

    if(breakStart != "" && breakEnd != ""){
      breakStart = converToTime(breakStart);
      breakEnd = converToTime(breakEnd);

      // if(breakStart > breakEnd){
      //   notificationError('Error', 'Invalid Time Format. Please try again');
      //   $(this).val('');
      // }
    }

  });
  // set init value for all days
  $(document).on('blur', '.initVal', function(){
    var error = 0;
    // console.log($('#monTimeStart').val());
    $('.initVal').each(function(){
      if($(this).val() == ""){
        error = 1;
      }
    });

    if(error == 0){
      var timeStart = $('#monTimeStart').val();
      var timeEnd = $('#monTimeEnd').val();
      var breakStart = $('#monBreakStart').val();
      var breakEnd = $('#monBreakEnd').val();
      var timeTotal = (converToTime(timeStart) > converToTime(timeEnd))
      ? ((converToTime(timeEnd) + 86400) - converToTime(timeStart)) / 3600
      : (converToTime(timeEnd) - converToTime(timeStart)) / 3600;
      $('#monTimeTotal').val(timeTotal);

      $('#confirmModal').modal();
      $('.yesBtn').click(function(){
        var day = ['tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        for(var x = 0; x < 6; x++){
          $('#'+day[x]+'TimeStart').val(timeStart);
          $('#'+day[x]+'TimeEnd').val(timeEnd);
          $('#'+day[x]+'BreakStart').val(breakStart);
          $('#'+day[x]+'BreakEnd').val(breakEnd);
          $('#'+day[x]+'TimeTotal').val(timeTotal);
        }
        $('#confirmModal').modal('hide');
      });
    }


  });

  // ADD SALARY
  var id_arr = [];
  var sal_arr = [];
  var total = 0;
  $(document).on('change', '#compSalaryCat', function(){
    var selected = $(this).find('option:selected');
    var data_desc = selected.data('desc');
    $(this).data('desc',data_desc);
    // alert(data_desc);
  });
  $(document).on('click', '#btnAddSalCat', function(){
    var currency = $('#currency').val();
    var ex_rate = parseFloat($('#currency').data('ex_rate'));
    var sal_obj = {'id':'', 'desc':'', 'amount':'', 'amount_php': ''};
    var sal_cat = parseInt($('#compSalaryCat').val());
    var sal_cat_dec = $('#compSalaryCat').data('desc');
    var amount = parseFloat($('#compAmount').attr('data-raw')) || 0;

    if(amount != "" && sal_cat_dec != ""){
      if($.inArray(sal_cat, id_arr) != -1){
        notificationError('Error', 'Salary Category already exist');
      }else{
        $('#salary_ajax').prepend(
          '<tr>'+
          '<td><small>'+sal_cat_dec+'</small></td>'+
          '<td>'+currency+' '+numberWithCommas(amount)+'</td>'+
          '<input type="hidden" name = "salary['+sal_cat+']" value = "'+amount+'"/>'+
          '</tr>'
        );
        id_arr.push(sal_cat);
        sal_obj.id = sal_cat;
        sal_obj.desc = sal_cat_dec;
        sal_obj.amount = amount;
        sal_obj.amount_php = amount * ex_rate;
        sal_arr.push(sal_obj);

        total += amount;
        $('#total_sal').text(currency+' '+numberWithCommas(total));
        $('#total_salary').val(total);
        $('#total_sal_converted').val(total * ex_rate);
        // console.log(sal_arr);
      }
    }

  });
  $(document).on('click', '#btn_reset_sal_tbl', function(){
    $('#salary_ajax').html(
      '<tr>'+
        '<td>Total</td>'+
        '<td id = "total_sal">0</td>'+
      '</tr>'
    );
    total = 0;
    id_arr = [];
    sal_arr = [];
  });
  $(document).on('change', '#currency', function(){
    var selected = $(this).find('option:selected');
    var ex_rate = selected.data('rate');
    $(this).data('ex_rate',ex_rate);
  });

  // LEAVE
  var leave_arr = [];
  var leave_arr2 = [];
  var total_days = 0;
  $(document).on('change', '#leave_type', function(){
    var selected = $(this).find('option:selected');
    var data_desc = selected.data('desc');
    $(this).data('desc',data_desc);
  });
  $(document).on('click', '#btn_add_leave', function(){
    var leave_obj = {'id':'', 'desc':'', 'days':''};
    var leave = parseInt($('#leave_type').val());
    var leave_desc = $('#leave_type').data('desc');
    var days = parseFloat($('#leave_num').val()) || 0;

    if(days >= 0){
      if($.inArray(leave, leave_arr) != -1){
        notificationError('Error', 'Leave already exist');
      }else{
        $('#leave_ajax').prepend(
          '<tr>'+
          '<td><small>'+leave_desc+'</small></td>'+
          '<td>'+days+'</td>'+
          '<input type="hidden" name = "salary['+leave+']" value = "'+days+'"/>'+
          '</tr>'
        );
        total_days += days;
        leave_obj.id = leave;
        leave_obj.desc = leave_desc;
        leave_obj.days = days;
        leave_arr.push(leave);
        leave_arr2.push(leave_obj);
        $('#total_leave').val(total_days);
        // console.log(leave_arr2);
      }
    }
  });
  $(document).on('click', '#btn_reset_leave_tbl', function(){
    $('#leave_ajax').html('');
    leave_arr = [];
    leave_arr2 = [];
  });

  // WORKSITE MULTI SELECTION
  $(document).on('change', '#cWorkSite, #current_cWorkSite', function(){
    if($(this).val() == '1.1'){
      $(this).prop('disabled', true);
    }
  });
  $(document).on('click', '.select2-selection__clear', function(){
    $('#cWorkSite').prop('disabled', false);
    $('#cWorkSite').select2('val', '');
  });

  // SUBMIT NEW CONTRACT FORM
  $('#btn_finish').click(function(){
    // $(this).prop('disabled',true);
    $('#newContract_form').submit();
  });
  $(document).on('submit', '#newContract_form', function(e){

    // var desc = CKEDITOR.instances['contractDescription'].getData();
    // var desc = $('#contractDescription').val();
    // cform.append('cDesc', desc);
    e.preventDefault();
    var cWorkSite = $('#cWorkSite').val();
    var pos_access_lvl = $('#cPos').find('option:selected').data('pos_access_lvl');
    var deptId = $('#cPos').find('option:selected').data('deptid')
    var subDeptId = $('#cPos').find('option:selected').data('subdeptid')
    var cform = new FormData(this);

    $('.templates').each(function(){
      cform.append('templates[]', $(this).val());
    });

    $('.template_id').each(function(){
      cform.append('template_id[]', $(this).val());
    });

    cform.append('salCat', JSON.stringify(sal_arr));
    cform.append('leave', JSON.stringify(leave_arr2));
    cform.append('pos_access_lvl', pos_access_lvl);
    cform.append('deptId', deptId);
    cform.append('subDeptId', subDeptId);
    if(cWorkSite == 1.1){
      cform.append('cWorkSite[]', cWorkSite);
    }

    $.ajax({
      url: base_url + 'employees/contracts/Contract/create',
      type: 'post',
      data: cform,
      contentType: false,
      processData:false,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          setTimeout(function (){
            window.location.href = base_url+'/employees/Employee/index/'+token;
          },2000);
        }else{
          $('#btn_submit_newContractForm').prop('disabled',false);
          notificationError('Error', data.message);
        }
      }
    })
  });

  // EDIT CONTRACT
  var editable_salcat_id = [];
  var edit_sal_arr = $('#sal_arr').val();
  edit_sal_arr = (edit_sal_arr != undefined) ? JSON.parse(edit_sal_arr) : '';
  var edit_total = parseFloat($('#edit_total_salary').val());
  $(document).on('change', '#edit_compSalaryCat', function(){
    var selected = $(this).find('option:selected');
    var data_desc = selected.data('desc');
    $(this).data('desc',data_desc);
    // alert(data_desc);
  });
  $(document).on('click', '#edit_btnAddSalCat', function(){
    var edit_currency = $('#edit_currency').val();
    var ex_rate = parseFloat($('#edit_currency').data('ex_rate'));
    var sal_obj = {'id':'', 'desc':'', 'amount':'', 'amount_php': ''};
    var sal_cat = parseInt($('#edit_compSalaryCat').val());
    var sal_cat_dec = $('#edit_compSalaryCat').data('desc');
    var amount = parseFloat($('#edit_compAmount').val()) || 0;

    if(amount != "" && sal_cat_dec != ""){
      if($.inArray(sal_cat, editable_salcat_id) != -1){
        notificationError('Error', 'Salary Category already exist');
      }else{
        $('#edit_salary_ajax').prepend(
          '<tr>'+
          '<td>'+sal_cat_dec+'</td>'+
          '<td>'+edit_currency+' '+numberWithCommas(amount)+'</td>'+
          '<input type="hidden" name = "edit_salary['+sal_cat+']" value = "'+amount+'"/>'+
          '</tr>'
        );
        editable_salcat_id.push(sal_cat);
        sal_obj.id = sal_cat;
        sal_obj.desc = sal_cat_dec;
        sal_obj.amount = amount;
        sal_obj.amount_php = amount * ex_rate;
        edit_sal_arr.push(sal_obj);

        edit_total += amount;
        $('#edit_total_sal').text(edit_currency+' '+numberWithCommas(edit_total));
        $('#edit_total_salary').val(edit_total);
        $('#edit_total_sal_converted').val(edit_total * ex_rate);
        // console.log(edit_sal_arr);
      }
    }

    // console.log(edit_sal_arr);

  });
  $(document).on('click', '#edit_btn_reset_sal_tbl', function(){
    // console.log(edit_sal_arr);
    $('#edit_salary_ajax').html(
      '<tr>'+
        '<td>Total</td>'+
        '<td id = "edit_total_sal">0</td>'+
      '</tr>'
    );
    edit_total = 0;
    editable_salcat_id = [];
    edit_sal_arr = [];
  });
  $(document).on('change', '#edit_currency', function(){
    var selected = $(this).find('option:selected');
    var ex_rate = selected.data('rate');
    $(this).data('ex_rate',ex_rate);
  });

  // PREVENT TIMELAPSE FOR EDIT CONTRACT
  $(document).on('change', '.current_timeWorkEnd', function(){
    var timeStart = $(this).parent('.row').children('.current_timeWorkStart').val() + '';
    var timeEnd = $(this).val() + '';


    if(timeStart != "" && timeEnd != ""){
      timeStart = converToTime(timeStart);
      timeEnd = converToTime(timeEnd);

      if(timeStart > timeEnd){
        // notificationError('Error', 'Invalid Time Format. Please try again');
        // $(this).val('');
        var timeTotal = ((timeEnd + 86400) - timeStart) / 3600;
        $(this).parent('.row').parent('td').parent('tr').find('.current_timeTotal').val(timeTotal);
      }else{
        var timeTotal = (timeEnd - timeStart) / 3600 // sec in 1 hour;
        $(this).parent('.row').parent('td').parent('tr').find('.current_timeTotal').val(timeTotal);
      }
    }else{
      $(this).parent('.row').parent('td').parent('tr').find('.current_timeTotal').val('');
    }

  });
  $(document).on('change', '.current_timeWorkStart', function(){
    var timeEnd = $(this).parent('.row').children('.current_timeWorkEnd').val();
    var timeStart = $(this).val();

    // console.log(timeStart);
    // console.log(timeEnd);

    if(timeStart != "" && timeEnd != ""){
      timeStart = converToTime(timeStart);
      timeEnd = converToTime(timeEnd);

      if(timeStart > timeEnd){
        // notificationError('Error', 'Invalid Time Format. Please try again');
        // $(this).val('');
        var timeTotal = ((timeEnd + 86400) - timeStart) / 3600;
        $(this).parent('.row').parent('td').parent('tr').find('.current_timeTotal').val(timeTotal);
      }
    }else{
      $(this).parent('.row').parent('td').parent('tr').find('.current_timeTotal').val('');
    }

  });
  $(document).on('change', '.current_breakEnd', function(){
    var breakStart = $(this).parent('.row').children('.current_breakStart').val();
    var breakEnd = $(this).val();

    // console.log(breakStart);
    // console.log(breakEnd);

    if(breakStart != "" && breakEnd != ""){
      breakStart = converToTime(breakStart);
      breakEnd = converToTime(breakEnd);

      // if(breakStart > breakEnd){
      //   notificationError('Error', 'Invalid Time Format. Please try again');
      //   $(this).val('');
      // }
    }

  });
  $(document).on('change', '.current_breakStart', function(){
    var breakEnd = $(this).parent('.row').children('.current_breakEnd').val();
    var breakStart = $(this).val();

    // console.log(breakStart);
    // console.log(breakEnd);

    if(breakStart != "" && breakEnd != ""){
      breakStart = converToTime(breakStart);
      breakEnd = converToTime(breakEnd);

      // if(breakStart > breakEnd){
      //   notificationError('Error', 'Invalid Time Format. Please try again');
      //   $(this).val('');
      // }
    }

  });
  $(document).on('blur', '.current_initVal', function(){
    var error = 0;
    // console.log($('#monTimeStart').val());
    $('.initVal').each(function(){
      if($(this).val() == ""){
        error = 1;
      }
    });

    if(error == 0){
      var timeStart = $('#currrent_monTimeStart').val();
      var timeEnd = $('#currrent_monTimeEnd').val();
      var breakStart = $('#currrent_monBreakStart').val();
      var breakEnd = $('#currrent_monBreakEnd').val();
      var timeTotal = (converToTime(timeStart) > converToTime(timeEnd))
      ? ((converToTime(timeEnd) + 86400) - converToTime(timeStart)) / 3600
      : (converToTime(timeEnd) - converToTime(timeStart)) / 3600;
      $('#currrent_monTimeTotal').val(timeTotal);

      $('#confirmModal').modal();
      $('.yesBtn').click(function(){
        var day = ['tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        for(var x = 0; x < 6; x++){
          $('#current_'+day[x]+'TimeStart').val(timeStart);
          $('#current_'+day[x]+'TimeEnd').val(timeEnd);
          $('#current_'+day[x]+'BreakStart').val(breakStart);
          $('#current_'+day[x]+'BreakEnd').val(breakEnd);
          $('#current_'+day[x]+'TimeTotal').val(timeTotal);
        }
        $('#confirmModal').modal('hide');
      });
    }


  })

  var edit_leave_arr = [];
  var edit_leave_arr2 = $('#leave_arr').val();
  edit_leave_arr2 = (edit_leave_arr2 != undefined) ? JSON.parse(edit_leave_arr2) : '';
  var edit_total_days = parseFloat($('#edit_total_leave').val());;
  $(document).on('change', '#edit_leave_type', function(){
    var selected = $(this).find('option:selected');
    var data_desc = selected.data('desc');
    $(this).data('desc',data_desc);
  });
  $(document).on('click', '#edit_btn_add_leave', function(){
    var leave_obj = {'id':'', 'desc':'', 'days':''};
    var leave = parseInt($('#edit_leave_type').val());
    var leave_desc = $('#edit_leave_type').data('desc');
    var days = parseFloat($('#edit_leave_num').val()) || 0;

    if(days >= 0){
      if($.inArray(leave, edit_leave_arr) != -1){
        notificationError('Error', 'Leave already exist');
      }else{
        $('#edit_leave_ajax').prepend(
          '<tr>'+
          '<td>'+leave_desc+'</td>'+
          '<td>'+days+'</td>'+
          '<input type="hidden" name = "edit_leave['+leave+']" value = "'+days+'"/>'+
          '</tr>'
        );
        edit_total_days += days;
        leave_obj.id = leave;
        leave_obj.desc = leave_desc;
        leave_obj.days = days;
        edit_leave_arr.push(leave);
        edit_leave_arr2.push(leave_obj);
        $('#edit_total_leave').val(edit_total_days);
        // console.log(edit_leave_arr);
        // console.log(edit_leave_arr2);
      }
    }
  });
  $(document).on('click', '#edit_btn_reset_leave_tbl', function(){
    $('#edit_leave_ajax').html('');
    edit_leave_arr = [];
    edit_leave_arr2 = [];
  });

  $(document).on('click', '#btn_edit_contract', function(){
    $('#change_cf_wrapper').show();
    $('.editable').removeAttr('disabled');
    $('.editable').removeAttr('readonly');
    $('.editable').css('border','1px solid #5AB733');
    $('.editable_salary').show();
    $('.editable_leave_wrapper').show();
    $('#edit_btn_reset_sal_tbl').show();
    $('#edit_btn_reset_leave_tbl').show();
    $('.editable_sal_cat').each(function(){
      editable_salcat_id.push(parseInt($(this).val()));
    });
    $('.editable_leave').each(function(){
      edit_leave_arr.push(parseInt($(this).val()));
    });
    $('#current_contract_file_wrapper').append(
      `<div class="col-md-2 text-center">
        <i class="fa fa-plus-square" id = "btn_curr_template_modal"></i>
      </div>`
    );
    $('#current_contract_file_wrapper').removeClass('no-events');
    $('#footer_wrapper').show();
  });
  $(document).on('click', '#btn_change_contract_file', function(){
    $('#current_upload_wrapper').hide();
    $('#editable_upload_wrapper').show();
  });
  $(document).on('submit', '#edit_form', function(e){
    e.preventDefault();
    var current_cWorkSite = $('#current_cWorkSite').val();
    // console.log(current_cWorkSite);
    // return ;

    var error = 0;
    var errorMsg = "";
    var pos_access_lvl = $('#current_cPos').find('option:selected').data('pos_access_lvl');
    var deptId = $('#current_cPos').find('option:selected').data('deptid')
    var subDeptId = $('#current_cPos').find('option:selected').data('subdeptid')
    var cform_update = new FormData(this);
    // console.log(edit_sal_arr);
    // console.log(edit_leave_arr2);
    // return false;
    $('.curr_templates').each(function(){
      cform_update.append('templates[]', $(this).val());
    });

    $('.curr_template_id').each(function(){
      cform_update.append('template_id[]', $(this).val());
    });

    cform_update.append('salCat', JSON.stringify(edit_sal_arr));
    cform_update.append('leave', JSON.stringify(edit_leave_arr2));
    cform_update.append('pos_access_lvl', pos_access_lvl);
    cform_update.append('deptId', deptId);
    cform_update.append('subDeptId', subDeptId);
    if(current_cWorkSite == "1.1"){
      cform_update.append('current_cWorkSite[]', current_cWorkSite);
    }

    $('.r4').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.r4').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'employees/contracts/Contract/update',
        type: 'post',
        contentType: false,
        processData: false,
        data:cform_update,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_submit_editForm').prop('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success',data.message);
            setTimeout(() => {location.reload()},1500);
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  // PREVIOUS CONTRACT
  $(document).on('click', '.prevContractForm', function(){
    var previd = $(this).data('previd');
    $('#prev_sal_cat_ajax').html('');
    $('#prev_leave_tbl_ajax').html('');
    $('#prev_company option[value=""]').prop('selected', true);
    $('#prev_contract_type option[value="fixed"]').prop('selected', true);

    $.ajax({
      url: base_url+'employees/contracts/Contract/getprevcontract',
      type: 'post',
      data:{ previd },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success:function(data){
        $.LoadingOverlay('hide');
        console.log(data.pos_id);
        if(data.success == 1){
          var prevData = data.prevData;
          var sched = JSON.parse(prevData.work_sched);

          var mon = sched.mon;
          var tue = sched.tue;
          var wed = sched.wed;
          var thu = sched.thu;
          var fri = sched.fri;
          var sat = sched.sat;
          var sun = sched.sun;
          var cdesc = prevData.contract_desc;
          var ext = cdesc.split('.');


          $('#prev_cWorkSite').val(prevData.workSite);
          $('#prev_cPos').val(prevData.position);
          $('#prev_cEmpLvl').val(prevData.emplvl);
          $('#prev_cStart').val(prevData.contract_start);
          $('#prev_cEnd').val(prevData.contract_end);
          $('#prev_contractStatus').val(prevData.empstatus);
          $('#prev_pay_medium').val(prevData.p_medium);
          $('#prev_company option[value="'+prevData.company_id+'"]').prop('selected', true);
          $('#prev_contract_type option[value="'+prevData.contract_type+'"]').prop('selected', true);
          // $('#prev_contractDescription').text(prevData.contract_desc);
          $('#prev_contractDescription').html(
            '<a href="'+base_url+prevData.contract_desc+'" download = "'+prevData.fullname+'.'+ext[1]+'">'+
              '<button class="btn btn-info btn-sm"><i class="fa fa-download mr-2"></i>Download Contract File</button>'+
            '</a>'
          )

          var sal_cat = JSON.parse(prevData.sal_cat);
          var total_sal = 0;
          $.each(sal_cat, function(i, val){
            total_sal += parseFloat(val['amount']);
            // replace compensation and salary w/ x if pos_id greater than 3
            if(parseInt(data.pos_id) > 3){
              var amount = replace_txt(numberWithCommas(val['amount']));
            }else{
              var amount = numberWithCommas(val['amount']);
            }
            $('#prev_sal_cat_ajax').append(
              '<tr>'+
                '<td>'+numberWithCommas(val['desc'])+'</td>'+
                '<td>'+prevData.currency+' '+amount+'</td>'+
              '</tr>'
            )
          });

          // replace compensation and salary w/ x if pos_id greater than 3
          if(parseInt(data.pos_id) > 3){
            var total = replace_txt(numberWithCommas(total_sal));
          }else{
            var total  = numberWithCommas(total_sal);
          }

          $('#prev_sal_cat_ajax').append(
            '<tr>'+
              '<td>Total</td>'+
              '<td>'+prevData.currency+' '+total+'</td>'+
            '</tr>'
          )

          var leave = JSON.parse(prevData.emp_leave);
          $.each(leave, function(i, val){
            $('#prev_leave_tbl_ajax').append(
              '<tr>'+
                '<td>'+val['desc']+'</td>'+
                '<td>'+val['days']+'</td>'+
              '</tr>'
            )
          });

          $('#sched_type option[value="'+prevData.sched_type+'"]').prop('selected', true);

          $('#prev_monTimeStart').val(mon[0]);
          $('#prev_monTimeEnd').val(mon[1]);
          $('#prev_monBreakStart').val(mon[3]);
          $('#prev_monBreakEnd').val(mon[4]);
          $('#prev_monTimeTotal').val(mon[2]);

          $('#prev_tueTimeStart').val(tue[0]);
          $('#prev_tueTimeEnd').val(tue[1]);
          $('#prev_tueBreakStart').val(tue[3]);
          $('#prev_tueBreakEnd').val(tue[4]);
          $('#prev_tueTimeTotal').val(tue[2]);

          $('#prev_wedTimeStart').val(wed[0]);
          $('#prev_wedTimeEnd').val(wed[1]);
          $('#prev_wedBreakStart').val(wed[3]);
          $('#prev_wedBreakEnd').val(wed[4]);
          $('#prev_wedTimeTotal').val(wed[2]);

          $('#prev_thuTimeStart').val(thu[0]);
          $('#prev_thuTimeEnd').val(thu[1]);
          $('#prev_thuBreakStart').val(thu[3]);
          $('#prev_thuBreakEnd').val(thu[4]);
          $('#prev_thuTimeTotal').val(thu[2]);

          $('#prev_friTimeStart').val(fri[0]);
          $('#prev_friTimeEnd').val(fri[1]);
          $('#prev_friBreakStart').val(fri[3]);
          $('#prev_friBreakEnd').val(fri[4]);
          $('#prev_friTimeTotal').val(fri[2]);

          $('#prev_satTimeStart').val(sat[0]);
          $('#prev_satTimeEnd').val(sat[1]);
          $('#prev_satBreakStart').val(sat[3]);
          $('#prev_satBreakEnd').val(sat[4]);
          $('#prev_satTimeTotal').val(sat[2]);

          $('#prev_sunTimeStart').val(sun[0]);
          $('#prev_sunTimeEnd').val(sun[1]);
          $('#prev_sunBreakStart').val(sun[3]);
          $('#prev_sunBreakEnd').val(sun[4]);
          $('#prev_sunTimeTotal').val(sun[2]);

          // replace compensation and salary w/ x if pos_id greater than 3
          var sss = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.sss) : prevData.sss;
          var philhealth = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.philhealth) : prevData.philhealth;
          var pagibig = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.pagibig) : prevData.pagibig;
          var tax = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.tax) : prevData.tax;
          $('#prev_compSSS').val(sss);
          $('#prev_compPhilhealth').val(philhealth);
          $('#prev_compPagIbig').val(pagibig);
          $('#prev_compTax').val(tax);
          $('#prev_compPayType').val(prevData.paytype);
          // $('#prev_basic_pay').text("PHP " + numberWithCommas(prevData.basic_pay));
          // $('#prev_trans_pay').text("PHP " + numberWithCommas(prevData.trans_pay));
          // $('#prev_commu_pay').text("PHP " + numberWithCommas(prevData.commu_pay));
          // $('#prev_etc_pay').text("PHP " + numberWithCommas(prevData.etc_pay));
          // var total = parseFloat(prevData.basic_pay) + parseFloat(prevData.trans_pay) + parseFloat(prevData.commu_pay) + parseFloat(prevData.etc_pay);
          // $('#prev_total').text("PHP " + numberWithCommas(total));

          $('#prevContract_modal').modal();
        }else{
          notificationError('Error', data.message);
        }
      }
    })
  });
  $(document).on('change', '#compPayType', function(){
    var freq = $(this).children('option:selected').data('pfreq');
    if(freq >= 4){
      $('.pt_text_info').html('<strong>Note:</strong> Please input employee <u>Daily Rate</u>');
    }else{
      $('.pt_text_info').html('<strong>Note:</strong> Please input employee <u>Monthly Rate</u>');
    }
  });

  // NEXT BUTTON
  $(document).on('click', '#btn_next', function(){
    var tab = $('.nav_new.active').get(0).id;
    var error = 0;
    var errorMsg = "";
    $('#btn_back').show();
    $('#btn_next').show();
    $('#btn_finish').hide();

    switch (tab) {
      case "nav-cDetails":
        $('.cdetails_req').each(function(){
          if($(this).val() == ""){
            $(this).css("border", "1px solid #ef4131");
          }else{
            $(this).css("border", "1px solid gainsboro");
          }
        });
        $('.cdetails_req').each(function(){
          if($(this).val() == ""){
            $(this).focus();
            error = 1;
            errorMsg = "Please fill up all required fields.";
            return false;
          }
        });

        (error != 0)
        ? notificationError('Error', errorMsg)
        : $('#nav-wschedule').tab('show');
        break;
      case "nav-wschedule":
        $('.initVal').each(function(){
          if($(this).val() == ""){
            $(this).css("border", "1px solid #ef4131");
          }else{
            $(this).css("border", "1px solid gainsboro");
          }
        });
        $('.initVal').each(function(){
          if($(this).val() == ""){
            $(this).focus();
            error = 1;
            errorMsg = "Please set employee work schedule.";
            return false;
          }
        });

        (error != 0)
        ? notificationError('Error', errorMsg)
        : $('#nav-compensation').tab('show');

        break;
      case "nav-compensation":
        $('#compSalaryCat').css('border', '1px solid gainsboro');
        $('.comp_req').each(function(){
          if($(this).val() == ""){
            $(this).css("border", "1px solid #ef4131");
          }else{
            $(this).css("border", "1px solid gainsboro");
          }
        });
        $('.comp_req').each(function(){
          if($(this).val() == ""){
            $(this).focus();
            error = 1;
            errorMsg = "Please fill up all required fields.";
            return false;
          }
        });

        if(total == 0){
          $('#compSalaryCat .select2-container--focus').css('border', '1px solid #ef4131');
          notificationError('Error', "Please add employee salary.");
          return false;
        }

        (error != 0)
        ? notificationError('Error', errorMsg)
        : $('#nav-leave').tab('show');

        break;
      case "nav-leave":
        $('#btn_next').hide();
        $('#btn_finish').show();
        $('#nav-contract_file').tab('show');
        break;
      default:

    }
  });
  // BACK BUTTON
  $(document).on('click', '#btn_back', function(){
    var tab = $('.nav_new.active').get(0).id;
    $('#btn_next').show();
    $('#btn_back').show();
    $('#btn_finish').hide();

    switch (tab) {
      case "nav-wschedule":
        $('#btn_back').hide();
        $('#nav-cDetails').tab('show');
        break;
      case "nav-compensation":
        $('#nav-wschedule').tab('show');
        break;
      case "nav-leave":
        $('#nav-compensation').tab('show');
        break;
      case "nav-contract_file":
        $('#btn_next').hide();
        $('#btn_finish').show();
        $('#nav-leave').tab('show');
        break;
      default:

    }
  });
  // CALL CREATE CONTRACT FILE MODAL
  $(document).on('click', '#btn_template_modal', function(){
    $('#template_modal').modal();
  });
  // CALL CURRENT CREATE CONTRACT FILE MODAL
  $(document).on('click', '#btn_curr_template_modal', function(){
    $('#curr_template_modal').modal();
  });
  // CREATE CONTRACT FILE
  $(document).on('click', '#btn_create', function(){
    var template = $('#template').val();
    var template_text = $('#template').data('text');
    var exists = 0;

    var cWorkSite = $('#cWorkSite').val();
    var pos_access_lvl = $('#cPos').find('option:selected').data('pos_access_lvl');
    var deptId = $('#cPos').find('option:selected').data('deptid')
    var subDeptId = $('#cPos').find('option:selected').data('subdeptid');
    var template = $('#template').val();
    var template_text = $('#template').data('text');
    var template_format = $(this).children('textarea').val();
    var contract = $('#newContract_form').serialize()+'&'+$.param({
      'salCat':JSON.stringify(sal_arr),
      'leave':JSON.stringify(leave_arr2),
      'pos_access_lvl':pos_access_lvl,
      'deptId':deptId,
      'subDeptId':subDeptId,
      'template':template,
      'employee_idno': $('#btn_newContract').data('empid')
    });
    // $('#btn_reset').hide();

    if(cWorkSite == 1.1){
      contract+'&cWorkSite[]='+cWorkSite;
      // contract+'&'+$.param({'cWorkSite':cWorkSite});
    }

    if($('#template').val() == ""){
      notificationError('Error', 'Please select template format');
      return;
    }

    $('.template_icon').each(function(){
      var id = $(this).data('template_id');
      if(id == template){
        exists = 1;
      }
    });

    if(exists == 1){
      notificationError('Error', 'Template already exists.');
      return ;
    }

    $.ajax({
      url: base_url+'employees/contracts/Contract/create_template',
      type: 'post',
      data:contract,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#template_modal').modal('hide');
          $('#template_wrapper').html(data.template_format);
          var template_info = data.template_info;
          Object.keys(template_info).forEach(key => {
            switch (key) {
              case 'sal_cat':
                var sal_cat = JSON.parse(template_info.sal_cat);
                $(`#template_wrapper .${key} b`).text('');
                sal_cat.forEach((data) => {
                  $(`#template_wrapper .${key} b`).append(
                    `<p>${data.desc}:   ${data.amount} / month</p>`
                  );
                })
                break;
              default:
                $(`#template_wrapper .${key} b`).text(template_info[key]);

            }
            // $(`#template_wrapper .${key} b`).text(template_info[key]);
          });

          var updated_format = $('#template_wrapper').html();
          $('#template_wrapper').html('');
          // thiss.children('textarea').val(updated_format);

          $('#template_icon_wrapper').prepend(
            `<div class="col-md-2 text-center new_cf_wrapper">
              <div id = "new_template_${template}" class="img-thumbnail template_icon template_${template}" data-template_id = "${template}">
                <i class="fa fa-sticky-note-o"></i>
                <small>${template_text}</small>
                <textarea class = "templates" style = "display:none" name = "templates">${updated_format}</textarea>
                <input class = "template_id" type="hidden" name = "template_id" value = "${template}" />
              </div>
              <button type = "button" class="btn btn-danger btn_new_delete_template">delete</button>
            </div>`
          );
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // CURRENT CREATE CONTRACT FILE
  $(document).on('submit', '#curr_template_form', function(e){
    var error = 0;
    var errorMsg = "";
    var exists = 0;
    var curr_template = $('#curr_template').val();
    e.preventDefault();

    $('.curr_template_icon').each(function(){
      var id = $(this).data('template_id');
      if(id == curr_template){
        exists = 1;
      }
    });

    if(exists == 1){
      notificationError('Error', 'Template already exists.');
      return ;
    }

    $('.curr_tr').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.curr_tr').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'employees/contracts/Contract/get_template_format_with_contract',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_curr_create').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_curr_create').prop('disabled', false);
          if(data.success == 1){
            $('.empty_cf_wrapper').remove();
            $('#curr_template_modal').modal('hide');
            $('#curr_contract_file_container').html(data.template_format);
            var canvas = document.querySelector('.signature-pad');
            var signature_pad = new SignaturePad(canvas);
            var template_info = data.template_info;
            Object.keys(template_info).forEach(key => {
              switch (key) {
                case 'sal_cat':
                  var sal_cat = JSON.parse(template_info.sal_cat);
                  $(`#curr_contract_file_container .${key} b`).text('');
                  sal_cat.forEach((data) => {
                    $(`#curr_contract_file_container .${key} b`).append(
                      `<p>${data.desc}:   ${data.amount} / month</p>`
                    );
                  })
                  break;
                default:
                  $(`#curr_contract_file_container .${key} b`).text(template_info[key]);

              }
            });

            var updated_format = $('#curr_contract_file_container').html();

            $('#current_contract_file_wrapper').prepend(
              `<div  class="col-md-2 text-center cf_wrapper">
                <div id = "template_${data.template['id']}" class="img-thumbnail curr_template_icon template_${data.template['id']}" data-template_id = "${data.template['id']}" data-name = "${data.template['template_name']}">
                  <i class="fa fa-sticky-note-o"></i>
                  <small>${data.template['template_name']}</small>
                  <textarea class = "curr_templates" style = "display:none" name = "curr_templates">${updated_format}</textarea>
                  <input class = "curr_template_id" type="hidden" name = "curr_template_id" value = "${data.template['id']}" />
                </div>
                <button type = "button" class="btn btn-primary curr_template_icon_btn btn_print">Print</button>
                <button data-delid = "${data.template['id']}" data-template_name = "${data.template['template_name']}" type = "button" class="btn btn-danger btn_delete_template2 curr_template_icon_btn">Delete</button>
              </div>`
            );

            $('#curr_contract_file_container').html('');
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.template_icon', function(){
    var template = $(this).children('.templates').val();
    var id = $(this).get(0).id;
    $('#template_wrapper').html(template);
    $('#btn_reset').show();
    $('#btn_reset').data('active_file', id);
    var canvas_count = $('.signature-pad').length;
    if(canvas_count > 0){
      $('.signature-pad').toArray().forEach((field, ind, el) => {
        var signature_pad = new SignaturePad(field, {
          onEnd: function(data){
            var timer = new Timer(3000, function(){
              var img = signature_pad.toDataURL();
              field.remove();
              $(`#template_wrapper .signature-pad-img:eq(${ind})`).attr('src', img);
              // $(`${field} .signature-pad-img`).attr('src', img);
            })

            $('.signature-pad').mousedown(function(){ timer.addTime(6000)});
            $('.signature-pad').dblclick(function(){
              var canvas = $(this)[0];
              canvas.width = canvas.width;
              timer.stop();
              // timer.addTime(3000);
            });
            // console.log(img);
          }
        })
      })
      $('.date_input_empty').datepicker(
        {format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
      );
      // var signature_pad = new SignaturePad(document.querySelector('.signature-pad'),{
      //   onEnd: function(data){
      //     // console.log(data);
      //     var timer = new Timer(3000, function(){
      //       var img = signature_pad.toDataURL();
      //       $('#template_wrapper .signature-pad').remove();
      //       $('#template_wrapper .signature-pad-img').attr('src', img);
      //     })
      //
      //     $('.signature-pad').mousedown(function(){ timer.addTime(2000)});
      //     // console.log(img);
      //   }
      // });
    }
  });

  $(document).on('change', '#template', function(){
    var selected = $(this).find('option:selected');
    var text = selected.text();
    $(this).data('text', text);
  });

  $(document).on('click', '.btn_new_delete_template', function(){
    $(this).parents('.new_cf_wrapper').remove();
  });

  $(document).on('click', '#btn_reset', function(){
    var active_file = $(this).data('active_file');
    var html = $('#template_wrapper').html();
    $(`#${active_file}`).children('.templates').val(html);
    $('#template_wrapper').html('');
    $(this).hide();
  });

  $(document).on('click', '.curr_template_icon', function(){
    var thiss = $(this);
    var template = thiss.children('.curr_templates').val();
    var name = thiss.data('name');
    var id = thiss.get(0).id;
    $('#curr_contract_file_modal #curr_contract_file_container').html(template);
    $('#curr_contract_file_modal .modal-title').text(name);
    $('#curr_contract_file_modal').data('active_file', id);
    var canvas_count = $('.signature-pad').length;
    if(canvas_count > 0){
      $('.signature-pad').toArray().forEach((field, ind, el) => {
        var signature_pad = new SignaturePad(field, {
          onEnd: function(data){
            var timer = new Timer(3000, function(){
              var img = signature_pad.toDataURL();
              field.remove();
              $(`#curr_contract_file_container .signature-pad-img:eq(${ind})`).attr('src', img);
              // $(`${field} .signature-pad-img`).attr('src', img);
            })

            $('.signature-pad').mousedown(function(){ timer.addTime(6000)});
            $('.signature-pad').dblclick(function(){
              var canvas = $(this)[0];
              canvas.width = canvas.width;
              timer.stop();
              // timer.addTime(3000);
            });
            // console.log(img);
          }
        })


      })
    }
    $('.date_input_empty').datepicker(
      {format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
    );
    $('#curr_contract_file_modal').modal();
  });

  $(document).on('change', '.check_box', function(){
    var check = $(this).is(':checked');
    if(check){
      $(this).attr('checked', 'checked');
    }else{
      $(this).removeAttr('checked');
    }
  });

  $('#curr_contract_file_modal').on('hidden.bs.modal', function(){
    var active_file = $(this).data('active_file');
    var html = $('#curr_contract_file_container').html();
    $(`#${active_file}`).children('.curr_templates').val(html);
    $('#curr_contract_file_container').html('');
  });

  $(document).on('click', '.btn_print', function(){
    // printJS('edit_form', 'html');
    var modal = $(this).siblings('.curr_template_icon').children('.curr_templates').val();
	  var body = document.body.innerHTML;
	  document.body.innerHTML = modal;
	  window.print();
	  document.body.innerHTML = body;
  });

  $(document).on('click', '.btn_delete_template', function(){
    var template_name = $(this).data('template_name');
    var delid = $(this).data('delid');
    var edit_contract_id = $('#edit_contract_id').val();
    $('#del_txt_cf').text(template_name);
    $('#del_cf_id').val(delid);
    $('#delete_contract_file_modal').modal();

    $('#btn_del_cf').click(function(){
      $.ajax({
        url: base_url+'employees/contracts/Contract/delete_contract_file',
        type: 'post',
        data:{delid, edit_contract_id},
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_del_cf').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_del_cf').prop('disabled', false);
          if(data.success == 1){
            $('#delete_contract_file_modal').modal('hide');
            notificationSuccess('Success', data.message);
            $(`.template_${delid}`).parent('.cf_wrapper').remove();
            // setTimeout(() => {location.reload(true);}, 1500);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
      // $('#delete_contract_file_modal').modal('hide');
      // $(`#${delid}`).parents('.cf_wrapper').remove();
    })
  });

  $(document).on('click', '.btn_delete_template2', function(){
    var delid = $(this).data('delid');
    $(`.template_${delid}`).parent('.cf_wrapper').remove();
  });

  $(document).on('blur', '.input-text', function(){
    var text = $(this).val();
    // console.log(text);
    if(text != ''){
      // $(this).closest('span').find('.input-text-container').text(text);
      $(this).next().append('<span>'+text+'</span> ')
      $(this).remove();
    }
  })

});
