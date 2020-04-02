$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_incident_report_tbl(search){
    var incident_report_tbl = $('#incident_report_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Incident_reports/get_incident_reports_json',
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

  gen_incident_report_tbl(JSON.stringify(searchValue));

  // FILTER
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
  // SEARCH EMPLOYEE
  $(document).on('keyup', '#employee', function(){
    var employee = $('#employee').val();
    // console.log(employee);
    if(employee != ""){
      $.ajax({
        url: base_url+'reports/Incident_reports/search_user',
        type: 'post',
        data:{ employee },
        beforeSend: function(){
          $('.loader_wrapper').show();
        },
        success: function(data){
          // $.LoadingOverlay('hide');
          $('.loader_wrapper').hide();
          if(data.success == 1){
            $('#result_wrapper').html(data.message);
          }else{
            $('#result_wrapper').html(data.message);
          }
        }
      });
    }else{
      $('#employee_idno').val('');
      $('#result_wrapper').html('<a href="#" class="dropdown-item dropdown-emp disabled" >No Result Found</a>');
    }
  });
  // SEARCH EDIT EMPLOYEE
  $(document).on('keyup', '#edit_employee', function(){
    var employee = $('#edit_employee').val();
    // console.log(employee);
    if(employee != ""){
      $.ajax({
        url: base_url+'reports/Incident_reports/edit_search_user',
        type: 'post',
        data:{ employee },
        beforeSend: function(){
          $('.edit_loader_wrapper').show();
        },
        success: function(data){
          // $.LoadingOverlay('hide');
          $('.edit_loader_wrapper').hide();
          if(data.success == 1){
            $('#edit_result_wrapper').html(data.message);
          }else{
            $('#edit_result_wrapper').html(data.message);
          }
        }
      });
    }else{
      $('#edit_employee_idno').val('');
      $('#edit_result_wrapper').html('<a href="#" class="dropdown-item edit_dropdown-emp disabled" >No Result Found</a>');
    }
  });
  // SEARCH REPORTED
  $(document).on('keyup', '#reported_by', function(){
    var employee = $('#reported_by').val();
    if(employee != ""){
      $.ajax({
        url: base_url+'reports/Incident_reports/search_user2',
        type: 'post',
        data:{ employee },
        beforeSend: function(){
          $('.loader_wrapper2').show();
        },
        success: function(data){
          // $.LoadingOverlay('hide');
          $('.loader_wrapper2').hide();
          if(data.success == 1){
            $('#result_wrapper2').html(data.message);
          }else{
            $('#result_wrapper2').html(data.message);
          }
        }
      });
    }else{
      $('#reported_id').val('');
      $('#result_wrapper2').html('<a href="#" class="dropdown-item dropdown-reported disabled" >No Result Found</a>');
    }
  });
  // SEARCH EDIT REPORTED
  $(document).on('keyup', '#edit_reported_by', function(){
    var employee = $('#edit_reported_by').val();
    if(employee != ""){
      $.ajax({
        url: base_url+'reports/Incident_reports/edit_search_user2',
        type: 'post',
        data:{ employee },
        beforeSend: function(){
          $('.edit_loader_wrapper2').show();
        },
        success: function(data){
          // $.LoadingOverlay('hide');
          $('.edit_loader_wrapper2').hide();
          if(data.success == 1){
            $('#edit_result_wrapper2').html(data.message);
          }else{
            $('#edit_result_wrapper2').html(data.message);
          }
        }
      });
    }else{
      $('#edit_reported_id').val('');
      $('#edit_result_wrapper2').html('<a href="#" class="dropdown-item edit_dropdown-reported disabled" >No Result Found</a>');
    }
  });
  // SELECT EMPLOYEE
  $(document).on('click', '.dropdown-emp', function(e){
    var deptid = $(this).data('deptid');
    $('#employee').val($(this).text());
    $('#employee_idno').val($(this).data('emp_idno'));
    $.ajax({
      url: base_url+'reports/Incident_reports/get_immediate_head',
      type: 'post',
      data:{deptid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#reporting_dept_head_id').val(data.message.employee_idno);
        }else{
          notificationError('Error', data.message);
          setTimeout(() => { window.location.reload(true)},2000);
        }
      }
    });
  });
  // SELECT EDIT EMPLOYEE
  $(document).on('click', '.edit_dropdown-emp', function(e){
    var deptid = $(this).data('deptid');
    $('#edit_employee').val($(this).text());
    $('#edit_employee_idno').val($(this).data('emp_idno'));
    $.ajax({
      url: base_url+'reports/Incident_reports/get_immediate_head',
      type: 'post',
      data:{deptid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#edit_reporting_dept_head_id').val(data.message.employee_idno);
        }else{
          notificationError('Error', data.message);
          setTimeout(() => { window.location.reload(true)},2000);
        }
      }
    });
  });
  // SELECT REPORTED
  $(document).on('click', '.dropdown-reported', function(e){
    var deptid = $(this).data('deptid');
    $('#reported_by').val($(this).text());
    $('#reported_id').val($(this).data('emp_idno'));

    $.ajax({
      url: base_url+'reports/Incident_reports/get_immediate_head',
      type: 'post',
      data:{deptid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#concerned_dept_head_id').val(data.message.employee_idno);
        }else{
          notificationError('Error', data.message);
          setTimeout(() => { window.location.reload(true)},2000);
        }
      }
    });
  });
  // SELECT REPORTED
  $(document).on('click', '.edit_dropdown-reported', function(e){
    var deptid = $(this).data('deptid');
    $('#edit_reported_by').val($(this).text());
    $('#edit_reported_id').val($(this).data('emp_idno'));

    $.ajax({
      url: base_url+'reports/Incident_reports/get_immediate_head',
      type: 'post',
      data:{deptid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#edit_concerned_dept_head_id').val(data.message.employee_idno);
        }else{
          notificationError('Error', data.message);
          setTimeout(() => { window.location.reload(true)},2000);
        }
      }
    });
  });
  // SUBMIT INCIDENT FORM
  $(document).on('submit', '#incident_form', function(e){
    e.preventDefault();
    var incident_form = new FormData(this);
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
        url: base_url+'reports/Incident_reports/Create',
        type: 'post',
        data: incident_form,
        processData: false,
        contentType: false,
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
            gen_incident_report_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // CALL EDIT MODAL
  $(document).on('click', '.btn_edit', function(){
    var thiss = $(this);
    var user_id = $(this).data('user_id');
    var user_dept = $(this).data('user_dept');
    var user_lvl = $(this).data('user_lvl');

    console.log(user_id);
    console.log(thiss.data('reporting_head_id'));
    // console.log(thiss.data('cd_head'));
    $('.head_wrapper').hide();
    $('#uid').val(thiss.data('uid'));
    $('#edit_employee').val(thiss.data('emp_name'));
    $('#edit_employee_idno').val(thiss.data('emp_idno'));
    $('#edit_reporting_dept_head_id').val(thiss.data('reporting_head_id'));
    $('#edit_position option[value="'+thiss.data('pos_id')+'"]').prop('selected', true);
    $('#edit_department option[value="'+thiss.data('dept_id')+'"]').prop('selected', true);
    $('#edit_date_reported').val(thiss.data('date_reported'));
    $('#edit_reported_by').val(thiss.data('reporter_name'));
    $('#edit_reported_id').val(thiss.data('reported_by'));
    $('#edit_concerned_dept_head_id').val(thiss.data('concerned_head_id'));
    $('#edit_place_of_incidence').val(thiss.data('place_of_incidence'));
    $('#edit_date_happened').val(thiss.data('date_happened'));
    $('#edit_time_of_incidence').val(thiss.data('time_of_incidence'));
    $('#edit_resulting_damage').val(thiss.data('resulting_damage'));
    $('#edit_incident_brief').val(thiss.data('incident_brief'));
    $('#edit_reporting_dept_head').val(thiss.data('rd_head'));
    $('#edit_concerned_dept_head').val(thiss.data('cd_head'));
    $('#edit_hr_dept_head').val(thiss.data('hr_head'));
    $('#edit_accounting_dept_head').val(thiss.data('ac_head'));
    // REPORTING DEPT
    if(user_id == thiss.data('reporting_head_id') && thiss.data('rd_head') == ""){
      $('#btn_approve_rd').data('id', thiss.data('reporting_head_id'));
      $('#btn_approve_rd').data('uid', thiss.data('uid'));
      $('#rd_wrapper').show();
    }
    // CONCERNED DEPT
    if(user_id == thiss.data('concerned_head_id') && thiss.data('cd_head') == ""){
      $('#btn_approve_cd').data('id', thiss.data('concerned_head_id'));
      $('#btn_approve_cd').data('uid', thiss.data('uid'));
      $('#cd_wrapper').show();
    }
    // HR DEPT
    if(user_dept == hr_id() && user_lvl <= hr_or_above() && thiss.data('hr_head') == ""){
      $('#btn_approve_hr').data('id', user_id);
      $('#btn_approve_hr').data('uid', thiss.data('uid'));
      $('#hr_wrapper').show();
    }
    // ACCOUNTING DEPT
    if(user_dept == accounting_id() && user_lvl <= accounting_manager_or_above() && thiss.data('ac_head') == ""){
      $('#btn_approve_ac').data('id', user_id);
      $('#btn_approve_ac').data('uid', thiss.data('uid'));
      $('#ac_wrapper').show();
    }
    $('#edit_modal').modal();
  });
  // SUBMIT EDIT INCIDENT FORM
  $(document).on('submit', '#edit_incident_form', function(e){
    e.preventDefault();
    var edit_incident_form = new FormData(this);
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

    // console.log(edit_incident_form);
    // return ;

    if(error == 0){
      $.ajax({
        url: base_url+'reports/Incident_reports/update',
        type: 'post',
        data:edit_incident_form,
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
            notificationSuccess('Success',data.message);
            gen_incident_report_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // CALL DELETE FORM AND SUBMIT
  $(document).on('click', '.btn_delete', function(){
    var delid = $(this).data('delid');
    $('#delid').val(delid);
    $('#delete_modal').modal();

    $("#btn_yes").click(function(){
      $.ajax({
        url: base_url+'reports/Incident_reports/delete',
        type: 'post',
        data:{delid},
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_yes').attr('disabled', true);
        },
        success: function(data){
          $('#btn_yes').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#delete_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_incident_report_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    });
  });
  // APPROVE BUTTON
  $(document).on('click', '.btn_approve', function(){
    var id = $(this).data('id');
    var act = $(this).data('act');
    var uid = $(this).data('uid');
    console.log(id);

    if(id != "" && act != ""){
      $.ajax({
        url: base_url+'reports/Incident_reports/approve',
        type: 'post',
        data:{id, act, uid},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#edit_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_incident_report_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }
  });
  // SEARCH
  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    searchValue.filter = filter_by;
    // for single date
    if($("#"+filter_by).hasClass('single_date')){
      searchValue.search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      searchValue.from = $('#'+filter_by).children().find('.from').val();
      searchValue.to = $('#'+filter_by).children().find('.to').val();
    }

    gen_incident_report_tbl(JSON.stringify(searchValue));

  });

  $('.dropdown-toggle').dropdown();
  $('.dropdown-toggle2').dropdown();

  $('.edit_dropdown-toggle').dropdown();
  $('.edit_dropdown-toggle2').dropdown();

  $('#employee').click(function(){
    $('.loader_wrapper').show();
    $('#result_wrapper').html('');
  });

  $('#edit_employee').click(function(){
    $('.edit_loader_wrapper').show();
    $('#edit_result_wrapper').html('');
  });

  $('#edit_reported_by').click(function(){
    $('.edit_loader_wrapper2').show();
    $('#edit_result_wrapper2').html('');
  });

});
