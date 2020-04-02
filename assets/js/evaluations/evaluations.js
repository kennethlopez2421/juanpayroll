$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var filter = {
    filter_by: "",
    search: "",
    from: "",
    to: ""
  }

  function gen_pending_eval_tbl(search){
    var pending_eval_tbl = $('#pending_eval_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'evaluations/Evaluations/get_pending_evaluations_json',
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

  function gen_evaluated_eval_tbl(search){
    var pending_eval_tbl = $('#evaluated_eval_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'evaluations/Evaluations/get_evaluated_evaluations_json',
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

  function gen_certified_eval_tbl(search){
    var pending_eval_tbl = $('#certified_eval_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'evaluations/Evaluations/get_certified_evaluations_json',
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

  gen_pending_eval_tbl(JSON.stringify(filter));
  
  gen_evaluated_eval_tbl(JSON.stringify(filter));
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
  		default:

  	}

  });

  $(document).on('click', '.nav-link', function(){
    var status = $(this).data('status');
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    switch (status) {
      case 'ongoing':
        gen_pending_eval_tbl(JSON.stringify(filter));
        break;
      case 'evaluated':
        gen_evaluated_eval_tbl(JSON.stringify(filter));
        break;
      case 'certified':
        gen_certified_eval_tbl(JSON.stringify(filter));
        break;
      default:

    }
  });

  $(document).on('click', '.btn_reassign', function(){
    var eval_id = $(this).data('eval_id');
    $('#eval_id').val(eval_id);
    $('#reassign_modal').modal();
  });

  $(document).on('click', '#btn_reassign_eval', function(){
    var dept = $('#dept').val();
    var pos_lvl = $('#pos_lvl').val();
    var hris_users = $('#hris_users').val();
    var eval_id = $('#eval_id').val();

    if(dept == "" || pos_lvl == "" || hris_users == "" || eval_id == ""){
      notificationError('Error', 'Please fill up all required fields. ');
      return;
    }

    $.ajax({
      url: base_url+'evaluations/Evaluations/reassign',
      type: 'post',
      data:{dept,pos_lvl,hris_users,eval_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#reassign_modal').modal('hide');
          notificationSuccess('Success',data.message);
          gen_pending_eval_tbl(JSON.stringify(filter));
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

  // SELECT DEPARTMENT
  $(document).on('change', '#dept', function(){
    var dept = $(this).val();

    // HIGHER DEPARTMENT
    if(dept == 0){
      $('#hris_users').removeAttr('disabled');
      $.ajax({
        url: base_url+'reports/Contract_expiration_reports/get_admin_to_send_evaluation',
        type: 'post',
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#hris_users').html('<option value="">------</option>');
            $.each(data.message, function(i, val){
              $('#hris_users').append('<option value="'+val['employee_idno']+'">'+val['fullname']+'('+val['position']+')</option>');
            });
          }else{
            notificationError('Error',data.message);
          }
        }
      });
      return;
    }
    // LOWER DEPARTMENT
    if(dept !== "" && dept != 0){
      $('#pos_lvl').removeAttr('disabled');
      var pos_lvl = $('#pos_lvl').val();
      if(pos_lvl !== ""){
        $.ajax({
          url: base_url+'reports/Contract_expiration_reports/get_users_to_send_evaluation',
          type: 'post',
          data:{dept, pos_lvl},
          beforeSend: function(){
            $.LoadingOverlay('show');
          },
          success: function(data){
            $.LoadingOverlay('hide');
            if(data.success == 1){
              $('#hris_users').removeAttr('disabled');
              $('#hris_users').html('<option value="">------</option>');
              $.each(data.pos_lvl, function(i, val){
                $('#hris_users').append(`<option value="${val['employee_idno']}">${val['fullname']}</option>`);
              });
            }else{
              notificationError('Error', data.message);
            }
          }
        });
      }
    }else{
      $('#pos_lvl').prop('disabled',true);
    }
  })
  // SELECT POSITION
  $(document).on('change', '#pos_lvl', function(){
    var pos_lvl = $(this).val();
    if(pos_lvl !== ""){
      var dept = $('#dept').val();
      if(dept !== ""){
        $.ajax({
          url: base_url+'reports/Contract_expiration_reports/get_users_to_send_evaluation',
          type: 'post',
          data:{dept, pos_lvl},
          beforeSend: function(){
            $.LoadingOverlay('show');
          },
          success: function(data){
            $.LoadingOverlay('hide');
            if(data.success == 1){
              $('#hris_users').removeAttr('disabled');
              $('#hris_users').html('<option value="">------</option>');
              $.each(data.pos_lvl, function(i, val){
                $('#hris_users').append(`<option value="${val['employee_idno']}">${val['fullname']}(${val['position_desc']})</option>`);
              });
            }else{
              notificationError('Error', data.message);
            }
          }
        });
      }
    }else{
      $('#pos_lvl').prop('disabled',true);
    }
  })

  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    filter.filter_by = filter_by;
    switch (filter_by) {
      case 'divName':
        filter.search = $('.filter_div.active').children('.searchArea').val();
        break;
      case 'divEmpID':
        filter.search = $('.filter_div.active').children('.searchArea').val();
        break;
      case 'divDept':
        filter.search = $('.filter_div.active').children('.searchArea').val();
        break;
      case 'divDate':
        filter.from = $('#date_from').val();
        filter.to = $('#date_to').val();
        break;
      default:

    }

    var status = $('.nav-link.active').data('status');

    switch (status) {
      case 'ongoing':
        gen_pending_eval_tbl(JSON.stringify(filter));
        break;
      case 'evaluated':
        gen_evaluated_eval_tbl(JSON.stringify(filter));
        break;
      case 'certified':
        gen_certified_eval_tbl(JSON.stringify(filter));
        break;
      default:
    }

  });

  $(document).on('click', '.btn_del_eval', function(){
    var id = $(this).data('id');
    var fullname = $(this).data('fullname');
    $('#eval_del_id').val(id);
    $('.info_desc').html('<span style = "font-weight:bold;">'+fullname+'</span>');
    $('#delete_modal').modal();
  });

  $(document).on('click', '#btn_yes2', function(){
    var eval_del_id = $('#eval_del_id').val();

    if(eval_del_id == ''){
      notificationError('Error', 'Unable to delete evaluation. Please try to reload and try again.');
      return;
    }

    $.ajax({
      url: base_url+'evaluations/Evaluations/delete',
      type: 'post',
      data:{eval_del_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_modal').modal('hide');
          notificationSuccess('Success', data.message);
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });
});
