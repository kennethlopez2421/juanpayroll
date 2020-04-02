$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_issued_items_tbl(search){
    var issued_items_tbl = $('#issued_items_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7,8,9,10], orderable: false}
      ],
      "ajax":{
        url: base_url+'employees/Issued_items/get_issued_items_json',
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

  gen_issued_items_tbl(JSON.stringify(searchValue));

  // ADD
  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  });

  $(document).on('change', '#dept', function(){
    const thiss = $(this);
    $('#employee').html('<option value="">------</option>');
    if(thiss.val() != ''){
      const dept_id = thiss.val();
      $.ajax({
        url: base_url+'transactions/Offset/get_employee_by_dept',
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

  $(document).on('submit', '#add_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    var add_form = new FormData(this);
    var real_price = $('#price').data('raw');
    add_form.append('real_price', real_price);

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
        url: base_url+'employees/Issued_items/create',
        type: 'post',
        data: add_form,
        processData:false,
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
            gen_issued_items_tbl(JSON.stringify(searchValue));
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
    let employee_idno = $(this).data('employee_idno');
    let emp_name = $(this).data('emp_name');
    let cat_id = $(this).data('cat_id');
    let item_name = $(this).data('item_name');
    let serial_no = $(this).data('serial_no');
    let item_condition = $(this).data('item_condition');
    let date_issued = $(this).data('date_issued');
    let date_received = $(this).data('date_received');
    let date_returned = $(this).data('date_returned');
    let price = $(this).data('price');
    let notes = $(this).data('notes');

    $('#uid').val(uid);
    // $('#edit_employee option[value="'+employee_idno+'"]').prop('selected', true).trigger('change');
    $('#emp_name').val(emp_name);
    $('#edit_item_cat option[value="'+cat_id+'"]').prop('selected', true).trigger('change');
    $('#edit_item_name').val(item_name);
    $('#edit_serial_no').val(serial_no);
    $('#edit_item_condition option[value="'+item_condition+'"]').prop('selected', true).trigger('change');
    $('#edit_date_issued').val(date_issued);
    $('#edit_date_received').val(date_received);
    $('#edit_price').val(price)
    $('#edit_note').val(notes);

    $('.money-input').toArray().forEach(function(field){
      var cleave = new Cleave(field, {
        numeral: true,
        block:[3],
        delimiter: ",",
        onValueChanged: function(e){
          // console.log(e);
          field.setAttribute('data-raw', e.target.rawValue);
        }
      });
    });

    $('#edit_modal').modal()
  });

  $(document).on('change', '#edit_dept', function(){
    const thiss = $(this);
    $('#edit_employee').html('<option value="">------</option>');
    if(thiss.val() != ''){
      const dept_id = thiss.val();
      $.ajax({
        url: base_url+'transactions/Offset/get_employee_by_dept',
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
          }else{
            notificationError('Error', data.message);
            $('#edit_employee option[value=""]').prop('selected', true);
            $('#edit_employee').trigger('change');
            $('#edit_employee').attr('disabled' ,true);
          }
        }
      });
    }else{
      // $('#department').trigger('change');
      $('#edit_employee option[value=""]').prop('selected', true);
      $('#edit_employee').trigger('change');
      $('#edit_employee').attr('disabled' ,true);
    }
  });

  $(document).on('submit', '#edit_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    var edit_form = new FormData(this);
    var real_price = $('#edit_price').data('raw');
    edit_form.append('real_price', real_price);

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
        url: base_url+'employees/Issued_items/update',
        type: 'post',
        data: edit_form,
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_update').attr('disabled', true)
        },
        success: function(data){
          $('#btn_update').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#edit_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_issued_items_tbl(JSON.stringify(searchValue));
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
    let item_name = $(this).data('item_name');
    let serial_no = $(this).data('serial_no');

    $('.info_desc').html( `(${serial_no}) ${item_name}`);
    $('#delid').val(delid);
    $('#delete_modal').modal();
  });

  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'employees/Issued_items/delete',
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
          gen_issued_items_tbl(JSON.stringify(searchValue));
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
  		case "by_cat":
  			$('.filter_div').hide("slow");
  			$('#divCat').show("slow");
  			$('#divCat').addClass('active');
  			break;
  		case "by_serial":
  			$('.filter_div').hide("slow");
  			$('#divSerial').show("slow");
  			$('#divSerial').addClass('active');
  			break;
  		case "by_date_issued":
  			$('.filter_div').hide("slow");
  			$('#divDateIssued').show("slow");
  			$('#divDateIssued').addClass('active');
  			break;
  		case "by_date_receive":
  			$('.filter_div').hide("slow");
  			$('#divDateReceived').show("slow");
  			$('#divDateReceived').addClass('active');
  			break;
  		case "by_date_returned":
  			$('.filter_div').hide("slow");
  			$('#divDateReturned').show("slow");
  			$('#divDateReturned').addClass('active');
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

    gen_issued_items_tbl(JSON.stringify(searchValue));

  });
});
