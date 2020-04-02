$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_assign_benefits_tbl(search){
    var assign_benefits_tbl = $('#assign_benefits_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3], orderable: false}
      ],
      "ajax":{
        url: base_url+'employees/Assign_benefits/get_assign_benefits_json',
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

  gen_assign_benefits_tbl(JSON.stringify(searchValue));

  // ADD
  $(document).on('change', '#dept', function(){
    const thiss = $(this);
    $('#employee').html('<option value="">------</option>');
    if(thiss.val() != ''){
      const dept_id = thiss.val();
      $.ajax({
        url: base_url+'employees/Assign_benefits/get_employee_by_dept',
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

  $(document).on('click', '#btn_add_modal', function(){
    $('#dept option[value=""]').prop('selected', true).trigger('change');
    $('#benefits option[value=""]').prop('selected', true).trigger('change');
    $('#add_modal').modal();
  });

  $(document).on('submit', '#add_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    let benefits = $('#benefits').val();
    let add_form = new FormData(this);
    add_form.append('real_benefits',benefits);

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
        url: base_url+'employees/Assign_benefits/create',
        type: 'post',
        data: add_form,
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_save').attr('disabled', true);
        },
        success: function(data){
          $('#btn_save').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_assign_benefits_tbl(JSON.stringify(searchValue));
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
  $(document).on('click', '.btn_edit', function(){
    let uid = $(this).data('uid');
    let benefits_id = $(this).data('benefits_id');
    let fullname = $(this).data('fullname');
    let benefit_arr = benefits_id.split(',');
    $('#uid').val(uid);
    $('#emp_name').val(fullname);
    benefit_arr.forEach((id) => {
      $('#edit_benefits option[value="'+id+'"]').prop('selected', true).trigger('change');
    });

    $('#edit_modal').modal();
  });

  $(document).on('submit', '#edit_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    let benefits = $('#edit_benefits').val();
    let edit_form = new FormData(this);
    edit_form.append('real_benefits',benefits);

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
        url: base_url+'employees/Assign_benefits/update',
        type: 'post',
        data: edit_form,
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
            $('#edit_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_assign_benefits_tbl(JSON.stringify(searchValue));
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
    let delid = $(this).data('delid');
    let fullname = $(this).data('fullname');

    $('.info_desc').html( `${fullname}`);
    $('#delid').val(delid);
    $('#delete_modal').modal();
  });

  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'employees/Assign_benefits/delete',
      type: 'post',
      data: new FormData(this),
      processData: false,
      contentType: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_delete').attr('disabled', true);
      },
      success: function(data){
        $('#btn_delete').prop('disabled', false);
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_modal').modal('hide')
          notificationSuccess('Success', data.message);
          gen_assign_benefits_tbl(JSON.stringify(searchValue));
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
  		case "by_benefits":
  			$('.filter_div').hide("slow");
  			$('#divBenefits').show("slow");
  			$('#divBenefits').addClass('active');
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

    gen_assign_benefits_tbl(JSON.stringify(searchValue));

  });
});
