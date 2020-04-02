$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var search_filter = {
    filter: "",
    keyword: "",
    from: "",
    to: ""
  }

  function gen_contract_expiration_reports_tbl(search){
    var contract_expiration_reports_tbl = $('#contract_expiration_reports_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Contract_expiration_reports/get_contract_expiration_json',
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

  gen_contract_expiration_reports_tbl(JSON.stringify(search_filter));

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
  		case "by_pos":
  			$('.filter_div').hide("slow");
  			$('#divPos').show("slow");
  			$('#divPos').addClass('active');
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
    var filter_by = $('.filter_div.active').get(0).id;

    search_filter.filter = filter_by;
    // for single search
    if($("#"+filter_by).hasClass('single_search')){
      search_filter.keyword = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      search_filter.from = $('#'+filter_by).children().find('#date_from').val();
      search_filter.to = $('#'+filter_by).children().find('#date_to').val();
    }

    // console.log(search_filter);
    // return false;
    gen_contract_expiration_reports_tbl(JSON.stringify(search_filter));

  });
  // MODAL
  $(document).on('click', '.btn_create_eval', function(){
    var fullname = $(this).data('fullname');
    $('#dept option[value=""]').prop('selected',true);
    $('#modal_title u').text(fullname);
    $('#employee_idno').val($(this).data('emp_id'));
    $('#dept option[value=""]').prop('selected', true).trigger('change');
    $('#pos_lvl option[value=""]').prop('selected', true).trigger('change');
    $('#hris_user').html('<option value="">------</option>');
    $('#create_evaluation_modal').modal();
  });
  // SELECT DEPARTMENT
  $(document).on('change', '#dept', function(){
    var dept = $(this).val();

    // HIGHER DEPARTMENT
    if(dept == 0){
      // console.log('1');
      $('#hris_users').removeAttr('disabled');
      $('#pos_lvl option[value=""]').prop('selected', true).trigger('change');
      $('#pos_lvl').attr('disabled', true);
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
    if(dept != "" && dept != 0){
      $('#pos_lvl').removeAttr('disabled');
      var pos_lvl = $('#pos_lvl').val();
      // console.log(pos_lvl);
      // return;
      if(pos_lvl != ""){
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
  // SEND EVALUATION
  $(document).on('click', '#btn_send_evaluation', function(){
    var dept = $('#dept').val();
    var emp_idno = $('#employee_idno').val();
    var management_id = $('#hris_users').val();
    var eval_date = $('#eval_date').val();
    var eval_from = $('#eval_from').val();
    var eval_to = $('#eval_to').val();

    if(management_id != ""){
      $.ajax({
        url: base_url+'reports/Contract_expiration_reports/send_evaluation',
        type: 'post',
        data:{
          dept, emp_idno, management_id,
          eval_date, eval_from, eval_to
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#create_evaluation_modal').modal('hide');
            $('#dept').val('').trigger('change');
            $('#pos_lvl').val('').trigger('change');
            $('#pos_lvl').val('').trigger('change');
            // $('#dept option[value=""]').prop('selected', true);
            // $('#pos_lvl option[value=""]').prop('selected', true);
            // $('#pos_lvl option[value=""]').prop('selected', true);
            notificationSuccess('Success', data.message);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', "Please select an employee. ");
    }
  });
});
