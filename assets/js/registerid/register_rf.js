$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var search_filter = {
    filter: "",
    search: ""
  };

  function gen_rf_tbl(search){
    var gen_rf_tbl = $('#gen_rf_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'registerid/Register_rf/get_rf_json',
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

  gen_rf_tbl('');

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
  			$('#divRfId').show("slow");
  			$('#divRfId').addClass('active');
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
  		default:

  	}

  });
  // FILTER SEND
  $('#searchButton').click(function(){
    search_filter.filter = $('.filter_div.active').get(0).id;
    search_filter.search = $('.filter_div.active').children('.searchArea').val();

    gen_rf_tbl(JSON.stringify(search_filter));

  });


  // REGISTER RF ID MODAL
  $(document).on('click', '#btn_rf', function(){
    $('#rf_modal').modal();

    $('#btn_scan').click(function(){
      $('#reg_employee_idno').css('border', '1px solid gainsboro');
      if(!$('#reg_employee_idno').val() == ""){
        $(this).hide();
        $('.rf_wrapper').show();
        $('#reg_rf_idnumber').val('');
        $('#reg_rf_idnumber').focus();
      }else{
        notificationError('Error', 'Please enter your employee id number');
        $('#reg_employee_idno').css('border', '1px solid #ef4131');
        // $('#reg_employee_idno').focus();
      }
    });

    $('#reg_rf_idnumber').keyup(function(e){
      if(e.keyCode === 13){
        $('#btn_reg_rfid').click();
      }
    })
  });
  // REGISTER RF ID
  $(document).on('click', '#btn_reg_rfid', function(){
    var reg_employee_idno = $('#reg_employee_idno').val();
    var reg_rf_idnumber = $('#reg_rf_idnumber').val();
    var thiss = $(this);

    if(reg_employee_idno == "" || reg_rf_idnumber == ""){
      notificationError('Error', 'Please fill up all required fields');
      return false;
    }

    $.ajax({
      url: base_url+'employees/Timelog/register_rfid',
      type: 'post',
      data:{ reg_employee_idno, reg_rf_idnumber},
      beforeSend: function(){
        $.LoadingOverlay('show');
        thiss.prop('disabled',true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        thiss.prop('disabled', false);
        if(data.success == 1){
          $('#rf_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_rf_tbl('');
        }else if(data.success == 2){
          $('#confirm_rfid').val(reg_rf_idnumber);
          $('#confirm_empid').val(reg_employee_idno);
          $('#rf_modal').modal('hide');
          $('#confirm_msg').text(data.message);
          $('#confirm_modal').modal();
        }else{
          $('#reg_employee_idno').val('');
          $('.rf_wrapper').hide();
          $('#btn_scan').show();

          notificationError('Error', data.message);
        }
      }
    });
  });
  // UPDATE RF NUMBER
  $(document).on('click', '#btn_yes', function(){
    var rf_number = $('#confirm_rfid').val();
    var emp_idno = $('#confirm_empid').val();
    console.log(rf_number);

    $.ajax({
      url: base_url+'employees/Timelog/update_rfid',
      type: 'post',
      data:{rf_number, emp_idno},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#confirm_modal').modal('hide');
        if(data.success == 1){
          notificationSuccess('Success',data.message);
          gen_rf_tbl('');
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });
  // RF MODAL CLOSE
  $('#rf_modal').on('hidden.bs.modal', function(){
    $('#reg_employee_idno').val('');
    $('#reg_rf_idnumber').val('');
    $('#btn_scan').show();
    $('.rf_wrapper').hide();
    $('#rf_idnumber').focus();
  });
  //CONFIRM MODAL CLOSE
  $('#confirm_modal').on('hidden.bs.modal', function(){
    $('#confirm_rfid').val('');
    // $('#rf_idnumber').focus();
  });
});
