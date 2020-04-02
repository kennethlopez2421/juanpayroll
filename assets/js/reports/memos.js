$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_memo_tbl(search){
    var pending_memo_tbl = $('#pending_memo_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Memos/get_memo_json',
        type: 'post',
        data: { searchValue: search, status: 'pending' },
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

  function approved_gen_memo_tbl(search){
    var approved_memo_tbl = $('#approved_memo_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Memos/get_memo_json',
        type: 'post',
        data: { searchValue: search, status: 'approved' },
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

  gen_memo_tbl(JSON.stringify(searchValue));
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
        url: base_url+'reports/Memos/search_user',
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
  // SELECT EMPLOYEE
  $(document).on('click', '.dropdown-emp', function(e){
    $('#employee').val($(this).text());
    $('#employee_idno').val($(this).data('emp_idno'));
  });
  // SUMBIT MEMO FORM
  $(document).on('submit', '#add_memo_form', function(e){
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
        url: base_url+'reports/Memos/create',
        type: 'post',
        data: new FormData(this),
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
            gen_memo_tbl(JSON.stringify(searchValue));
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
    var memo_file = $(this).data('memo_file');
    var dept_id = $(this).data('dept_id');
    var status = $(this).data('status');

    $('#edit_employee').val(thiss.data('name'));
    $('#edit_employee_idno').val(thiss.data('emp_idno'));
    $('#uid').val(thiss.data('uid'));
    $('#edit_re').val(thiss.data('re'));
    $('#edit_memo_file').val(thiss.data('memo_file'));
    $('#edit_dept option[value="'+dept_id+'"]').prop('selected',true);
    $('#edit_date').val(thiss.data('date'));
    $('#memo_file_view').prop('src', `https://docs.google.com/gview?url=${base_url}${memo_file}&embedded=true`);

    if(status == 'approved'){
      $('#btn_approved').hide();
      $('#btn_update ').hide();
    }else{
      $('#btn_approved').show();
      $('#btn_update ').show();
    }

    $('#edit_modal').modal();
  });
  // SEARCH EDIT EMPLOYEE
  $(document).on('keyup', '#edit_employee', function(){
    var employee = $('#edit_employee').val();
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
  // SUBMIT EDIT MEMO FORM
  $(document).on('submit', '#edit_memo_form', function(e){
    e.preventDefault();
    var edit_form = new FormData(this);
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
        url: base_url+'reports/Memos/update',
        type: 'post',
        data:edit_form,
        processData: false,
        contentType: false,
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
            gen_memo_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  })
  // SELECT EDIT EMPLOYEE
  $(document).on('click', '.edit_dropdown-emp', function(e){
    var deptid = $(this).data('deptid');
    $('#edit_employee').val($(this).text());
    $('#edit_employee_idno').val($(this).data('emp_idno'));
  });
  // APPROVED MEMO
  $(document).on('click', '#btn_approved', function(e){
    var uid = $('#uid').val();
    $.ajax({
      url: base_url+'reports/Memos/approved',
      type: 'post',
      data:{uid},
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_approved').attr('disabled', true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#btn_approved').prop('disabled', false);
        if(data.success == 1){
          $('#edit_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_memo_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });
  // CHANGE INPUT TEXT TO FILE
  $(document).on('click', '#edit_memo_file', function(){
    $(this).prop({type:"file"});
  });
  // NAVIGATION
  $(document).on('click', '.nav-link', function(){
    var tab = $(this).data('tab');
    $('.nav-link').removeClass('active');
    $('.tab-pane').removeClass('active');
    $(this).addClass('active');
    $(`#${tab}`).tab('show');
    // console.log(tab);

    switch (tab) {
      case 'pending_tab':
        gen_memo_tbl(JSON.stringify(searchValue));
        break;
      case 'approved_tab':
        approved_gen_memo_tbl(JSON.stringify(searchValue));
        break;
      default:

    }
  });
  // SEARCH
  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    var tab = $('.nav-link.active').get(0).id;
    searchValue.filter = filter_by;
    // for single date
    if($("#"+filter_by).hasClass('single_search')){
      searchValue.search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      searchValue.from = $('#'+filter_by).children().find('.from').val();
      searchValue.to = $('#'+filter_by).children().find('.to').val();
    }

    switch (tab) {
      case 'pending_nav':
        gen_memo_tbl(JSON.stringify(searchValue));
        break;
      case 'approved_nav':
        approved_gen_memo_tbl(JSON.stringify(searchValue));
        break;
      default:

    }

    gen_incident_report_tbl(JSON.stringify(searchValue));

  });

  $('.dropdown-toggle').dropdown();
  $('#employee').click(function(){
    $('.loader_wrapper').show();
    $('#result_wrapper').html('');
  });

  $('.edit_dropdown-toggle').dropdown();
  $('#edit_employee').click(function(){
    $('.edit_loader_wrapper').show();
    $('#edit_result_wrapper').html('');
  });

});
