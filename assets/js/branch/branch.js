
$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_branch_tbl(search){
    var branch_tbl = $('#branch_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'branch/Branch/get_branch_json',
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

  gen_branch_tbl(JSON.stringify(searchValue));

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_bname":
  			$('.filter_div').hide("slow");
  			$('#divBname').show("slow");
  			$('#divBname').addClass('active');
  			break;
  		case "by_bcode":
  			$('.filter_div').hide("slow");
  			$('#divBcode').show("slow");
  			$('#divBcode').addClass('active');
  			break;
  		case "by_timezone":
  			$('.filter_div').hide("slow");
  			$('#divTimezone').show("slow");
  			$('#divTimezone').addClass('active');
  			break;
  		case "by_country":
  			$('.filter_div').hide("slow");
  			$('#divCountry').show("slow");
  			$('#divCountry').addClass('active');
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
  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  });
  // SUBMIT NEW BRANCH FORM
  $(document).on('submit', '#new_branch_form', function(e){
    e.preventDefault();
    var new_branch_form = new FormData(this);
    var password = $('#password').val();
    var cpassword = $('#cpassword').val();
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

    if(password != cpassword){
      error = 1;
      errorMsg = "Password and Confirm Password do not match. Please try again.";
      $('#password, #cpassword').css('border', '1px solid #ef4131');
      $('#password').focus();
      return false;
    }

    if(error == 0){
      $.ajax({
        url: base_url+'branch/Branch/create',
        type: 'post',
        data: new_branch_form,
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
            notificationSuccess('Success',data.message);
            gen_branch_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // CALL VIEW MODAL
  $(document).on('click', '.btn_view', function(){
    var thiss = $(this);
    $('#uid').val(thiss.data('uid'));
    $('#edit_username').val(thiss.data('username'));
    $('#edit_password').val(thiss.data('password'));
    $('#curr_password').val(thiss.data('password'));
    $('#edit_fname').val(thiss.data('fname'));
    $('#edit_mname').val(thiss.data('mname'));
    $('#edit_lname').val(thiss.data('lname'));

    $('#edit_branch_name').val(thiss.data('bname'));
    $('#login_superuser').data('bcode', thiss.data('bcode'));
    $('#login_superuser').data('timezone', thiss.data('timezone'));
    $('#edit_branch_code').val(thiss.data('bcode'));
    $('#edit_dbname').val(thiss.data('dbname'));
    $('#edit_timezone').val(thiss.data('timezone'));
    $('#edit_country_code').val(thiss.data('country_code'));
    $('#edit_loc_status option[value="'+thiss.data('location')+'"]').prop('selected',true);

    $('#view_modal').modal();
  });
  // SUBMIY UPDATE FORM
  $(document).on('submit', '#update_hris_branch_form', function(e){
    e.preventDefault();
    var update_hris_branch_form = new FormData(this);
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
        url: base_url+'branch/Branch/update',
        type: 'post',
        data: update_hris_branch_form,
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
            $('#view_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_branch_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // CALL DELETE MODAL
  $(document).on('click', '.btn_del', function(){
    var thiss = $(this);
    $('#delid').val(thiss.data('delid'))
    $('#del_txt').text(thiss.data('bname'));

    $('#delete_modal').modal();
  });
  // SUBMIT DELETE FORM
  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    var delete_form = new FormData(this);
    $.ajax({
      url: base_url+'branch/Branch/delete',
      type: 'post',
      data:delete_form,
      contentType: false,
      processData: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_yes').attr('disabled', true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#btn_yes').prop('disabled', true);
        if(data.success == 1){
          $('#delete_modal').modal('toggle');
          notificationSuccess('Success', data.message);
          gen_branch_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // CALL ACTIVATE MODAL
  $(document).on('click', '.btn_activate', function(e){
    var thiss = $(this);
    $('#activate_txt').text(thiss.data('bname'));
    $('#activate_id').val(thiss.data('activateid'));
    $('#activate_modal').modal();
  });
  // SUBMIT ACTIVATE FORM
  $(document).on('submit', '#activate_form', function(e){
    e.preventDefault();
    var activate_form = new FormData(this);
    $.ajax({
      url: base_url+'branch/Branch/activate',
      type: 'post',
      data: activate_form,
      contentType: false,
      processData: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_yes2').attr('disabled', true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#btn_yes2').prop('disabled', false);
        if(data.success == 1){
          $('#activate_modal').modal('toggle');
          notificationSuccess('Success', data.message);
          gen_branch_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // LOGIN AS SUPERUSER
  $(document).on('click', '#login_superuser', function(){
    var bcode = $(this).data('bcode');
    var timezone = $(this).data('timezone');
    $.ajax({
      url: base_url+'branch/Branch_login/login_admin',
      type: 'post',
      data:{bcode: bcode, token: 'S0lVcytzMTZ0REhrMVVYbEZhMllGZz09', timezone},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          window.location.href = base_url+'Main/home/'+token;
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // BACK TO ADMIN HOME
  $(document).on('click', '#btn_admin_home', function(){
    // alert();
    var token = $(this).data('token');
    $.ajax({
      url: base_url+'admin/Admin/back_to_admin',
      type: 'post',
      data:{token},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          // console.log(''+base_url+'/admin/Admin/home/'+data.token);
          window.location.href = ''+base_url+'/admin/Admin/home/'+data.token;
          // window.location.open(base_url+'/admin/Admin/home/'+data.token);
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });
  // SEARCH
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

    gen_branch_tbl(JSON.stringify(searchValue));

  });
});
