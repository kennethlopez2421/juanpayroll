$(function(){
  var base_url = $('body').data('base_url');
  var token = $('#token').val();

  function time_log_reports_tbl(search){
    var time_log_reports_tbl = $('#time_log_reports_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'time_record/Timelogreports/timelogreports_json',
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

  time_log_reports_tbl("");

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
      case "by_worksite":
        $('.filter_div').hide("slow");
        $('#divWorksite').show("slow");
        $('#divWorksite').addClass('active');
        break;
      case "by_date":
        $('.filter_div').hide("slow");
        $('#divDate').show("slow");
        $('#divDate').addClass('active');
        break;
      default:

    }

  });

  $('#searchButton').click(function(){
    var filter = $('.filter_div.active').get(0).id;
    var sql = "";

    switch (filter) {
      case 'divName':
        var searchValue = $('.filter_div.active').children().find('.searchArea').val();
        var date = $('.filter_div.active').children().find('.filter_date_from').val();
        var date2 = $('.filter_div.active').children().find('.filter_date_to').val();
        sql = " AND ((CONCAT(c.last_name,',', c.first_name,' ', c.middle_name) LIKE '%"+searchValue+"%'"+
              " OR c.first_name LIKE '%"+searchValue+"%'"+
              " OR c.last_name LIKE '%"+searchValue+"%')) AND a.date BETWEEN '"+date+"' AND '"+date2+"'";
        break;
      case 'divEmpID':
        var searchValue = $('.filter_div.active').children().find('.searchArea').val();
        // var date = $('.filter_div.active').children().find('.filter_date').val();
        var date = $('.filter_div.active').children().find('.filter_date_from').val();
        var date2 = $('.filter_div.active').children().find('.filter_date_to').val();
        sql = " AND c.employee_idno = '"+searchValue+"' AND a.date BETWEEN '"+date+"' AND '"+date2+"'";
        break;
      case 'divWorksite':
        var searchValue = $('.filter_div.active').children().find('.searchArea').val();
        var date = $('.filter_div.active').children().find('.filter_date').val();
        sql = " AND a.worksite = "+searchValue+" AND a.date = '"+date+"'";
        // console.log(searchValue);
        // console.log(date);
        // return false
        break;
      case 'divDate':
        var start = $('#date_from').val();
        var end = $('#date_to').val();
        sql = " AND a.date BETWEEN '"+start+"' AND '"+end+"'";
        break;
      default:

    }
    // console.log('hello');
    // return;
    time_log_reports_tbl(sql);
    // caTable.search(searchText).draw();
  });

  $(document).on('click', '.time_img', function(){
    var title = $(this).data('title');
    var url = $(this).data('url');
    url = url.replace(/ /g,'%20');
    console.log(url);

    $('.view_image').css('background-image', `url(${base_url}${url})`);
    $('.modal-title').text(title);
    $('.view_image').css({
      "background-image": `url(${base_url}${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat"
    });
    $('#view_image_modal').modal();
  });

  $(document).on('click', '#btn_import_modal', function(){
    $('#import_modal').modal();
  });

  $(document).on('submit', '#import_form', function(e){
    e.preventDefault();
    var lat = $('#import_worksite option:selected').data('lat');
    var lng = $('#import_worksite option:selected').data('lng');
    var form = new FormData(this);

    form.append('lat', lat);
    form.append('lng', lng);
    $.ajax({
      url: base_url+'time_record/Timelogreports/import_excel',
      type: 'post',
      data: form,
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#import_modal').modal('hide');
          notificationSuccess('Success', data.message);
          time_log_reports_tbl("");
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

  $(document).on('click', '.edit_btn', function(){
    var uid = $(this).data('uid');
    var status = $(this).data('status');
    var timein = $(this).data('timein');
    var timeout = $(this).data('timeout');
    var emp_id = $(this).data('emp_id');
    var date = $(this).data('date');

    // console.log(uid);

    $('#time_in').val(timein)
    $('#current_timein').val(timein)
    $('#time_out').val(timeout)
    $('#current_timeout').val(timeout)
    $('#uid').val(uid);
    $('#status').val(status);
    $('#emp_id').val(emp_id);
    $('#date').val(date);
    $('#edit_modal').modal();
  });

  $(document).on('click', '.del_btn', function(){
    var delid = $(this).data('delid');
    var status = $(this).data('status');
    var fullname = $(this).data('fullname');
    var emp_id = $(this).data('emp_id');
    var del_date = $(this).data('del_date');

    $('#delid').val(delid);
    $('#del_status').val(status);
    $('#del_name').text(fullname);
    $('#del_emp_id').val(emp_id)
    $('#del_date').val(del_date);
    $('#delete_modal').modal();
  });

  $(document).on('click', '#btn_export_excel', function(){
    $('#export_modal').modal();
  });

  $(document).on('submit', '#update_timelog_form', function(e){
    e.preventDefault();
    var form = new FormData(this);
    var time_in = $('#time_in').val();
    var time_out = $('#time_out').val();
    var current_timein = $('#current_timein').val();
    var current_timeout = $('#current_timeout').val();

    // console.log('time_in', time_in);
    // console.log('time_out', time_out);
    // return;
    if(time_in == current_timein && time_out == current_timeout){
      $('#edit_modal').modal('hide');
      return;
    }
    $.ajax({
      url: base_url+'time_record/Timelogreports/update',
      type: 'post',
      data: form,
      processData: false,
      contentType: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#edit_modal').modal('hide');
          notificationSuccess('Success', data.message);
          time_log_reports_tbl("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  })

  $(document).on('submit', '#del_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'time_record/Timelogreports/delete',
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
          $('#delete_modal').modal('hide');
          notificationSuccess('Success', data.message);
          time_log_reports_tbl("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('submit', '#export_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    var export_from_date = $('#export_from_date').val();
    var export_to_date = $('#export_to_date').val();
    var export_emp_id = $('#export_emp_id').val();
    var export_type = $('#export_type').val();

    $('.export_rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.export_rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      window.open(base_url+'time_record/Timelogreports/export_to_excel/'+token+'/'+export_from_date+'/'+export_to_date+'/'+export_type+'/'+export_emp_id)
    }else{
      notificationError('Error', errorMsg);
    }

  })

  $(document).on('click', '#sample_import_format', function(){
    $('#sample_import_format_modal').modal();
  })
});
