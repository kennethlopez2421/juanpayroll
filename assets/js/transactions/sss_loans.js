$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_sss_loan_tbl(search){
    var sss_loan_tbl = $('#sss_loan_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Sss_loans/get_sss_loans_json',
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

  function gen_view_sss_loan_tbl(search){
    var view_sss_loan_tbl = $('#view_sss_loan_tbl').DataTable( {
      "processing": true,
      "serverSide": false,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Sss_loans/get_sss_loan',
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

  gen_sss_loan_tbl(JSON.stringify(searchValue));

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_voucher":
  			$('.filter_div').hide("slow");
  			$('#divVoucher').show("slow");
  			$('#divVoucher').addClass('active');
  			break;
  		case "by_deduct_start":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
  			break;
  		case "by_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
  			break;
  		case "by_period":
  			$('.filter_div').hide("slow");
  			$('#divDate2').show("slow");
  			$('#divDate2').addClass('active');
  			break;
  		case "by_total_loan":
  			$('.filter_div').hide("slow");
  			$('#divLoan').show("slow");
  			$('#divLoan').addClass('active');
  			break;
  		case "by_monthly_amortization":
  			$('.filter_div').hide("slow");
  			$('#divAmortization').show("slow");
  			$('#divAmortization').addClass('active');
  			break;
  		default:

  	}

  });

  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal('show');
  });
  // SUBMIT SSS LOAN FORM
  $(document).on('submit', '#sss_loan_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    var loan_start = $('#loan_start').val();
    var loan_end = $('#loan_end').val();
    var formData = new FormData(this);
    var total_loan = $('#total_loan').attr('data-raw');
    var monthly_amortization = $('#monthly_amortization').attr('data-raw');
    formData.append('total_loan_raw', total_loan);
    formData.append('monthly_amortization_raw', monthly_amortization);
    // console.log("loan",total_loan);
    // return ;

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
        url: base_url+'transactions/Sss_loans/create',
        type: 'post',
        contentType: false,
        processData: false,
        data: formData,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success',data.message);
            gen_sss_loan_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // SEARCH EMPLOYEE
  $(document).on('keyup', '#employee', function(){
    var employee = $('#employee').val();
    // console.log(employee);
    // console.log($('#employee_idno').val());
    if(employee != ""){
      $.ajax({
        url: base_url+'transactions/Sss_loans/search_user_w_sss',
        type: 'post',
        data:{ employee },
        beforeSend: function(){
          // $.LoadingOverlay('show');
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
      $('#result_wrapper').html('<a href="#" class="dropdown-item disabled" >No Result Found</a>');
    }
  });
  // SELECT EMPLOYEE
  $(document).on('click', '.dropdown-item', function(e){
    $('#employee').val($(this).text());
    $('#employee_idno').val($(this).data('emp_idno'));
  });

  $(document).on('click', '.time_img', function(){
    var title = $(this).data('title');
    var url = $(this).data('url');
    // console.log(title);

    // $('.view_image').css('background-image', `url(${base_url}${url})`);
    $('.modal-title').text(title);
    $('.view_image').css({
      "background-image": `url(${base_url}${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat"
    });
    $('#view_image_modal').modal();
  });

  $(document).on('click', '.btn_view_modal', function(){
    var sss_loan_id = $(this).data('sss_loan_id');
    var sss_total_loan = $(this).data('sss_total_loan');
    var monthly_amortization = $(this).data('monthly_amortization');

    $('#view_total_loan').val(sss_total_loan);
    $('#view_monthly_amortization').val(monthly_amortization);
    gen_view_sss_loan_tbl(sss_loan_id);
    $('#view_modal').modal();
  })

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

    gen_sss_loan_tbl(JSON.stringify(searchValue));

  });

  $('.dropdown-toggle').dropdown();

  $('#employee').click(function(){
    $('.loader_wrapper').show();
    $('#result_wrapper').html('');
  })
});
