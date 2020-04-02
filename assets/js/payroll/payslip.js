$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_payslip_tbl(search){
    var payslip_tbl = $('#payslip_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payslip/get_payslip_json',
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

  gen_payslip_tbl(JSON.stringify(searchValue));

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_payroll_refno":
  			$('.filter_div').hide("slow");
  			$('#divPayroll_refno').show("slow");
  			$('#divPayroll_refno').addClass('active');
  			break;
  		case "by_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
  			break;
  		default:

  	}

  });

  $(document).on('click', '.payroll_breakdown', function(){
    $('#convertion_wrapper').hide();
    var fullname = $(this).data('fullname');
    var date = $(this).data('date');
    var paytype = $(this).data('paytype');
    var wdays = $(this).data('wdays');
    var currency = $(this).data('currency');
    if(currency != "PHP"){
      $('#convertion_wrapper').show();
    }

    $('#show_peso_chkbox').data('ex_rate', $(this).data('ex_rate'));
    $('#show_peso_chkbox').data('currency', $(this).data('currency'));

    // alert(paytype);
    $('#p_idno').text($(this).data('emp_idno'));
    $('#p_name').text($(this).data('fullname'));
    $('#p_paytype2').text($(this).data('paytype'));
    $('#p_date').text($(this).data('date'));
    // gross salary
    $('#p_wday').text($(this).data('wdays'));
    $('#p_grosspay').text(currency+' '+$(this).data('gross_pay'));
    $('#p_grosspay').data('amount', $(this).data('gross_pay'));

    $('#p_reg_holiday').text($(this).data('reg_holiday'));
    $('#p_reg_holiday_pay').text(currency+' '+$(this).data('reg_holiday_pay'));
    $('#p_reg_holiday_pay').data('amount', $(this).data('reg_holiday_pay'));

    $('#p_spl_holiday').text($(this).data('spl_holiday'));
    $('#p_spl_holiday_pay').text(currency+' '+$(this).data('spl_holiday_pay'));
    $('#p_spl_holiday_pay').data('amount', $(this).data('spl_holiday_pay'));

    $('#p_sunday').text($(this).data('sunday'));
    $('#p_sunday_pay').text(currency+' '+$(this).data('sunday_pay'));
    $('#p_sunday_pay').data('amount', $(this).data('sunday_pay'));
    // less
    $('#p_absent').text($(this).data('absent'));
    $('#p_absent_deduct').text(currency+' '+$(this).data('absent_deduction'));
    $('#p_absent_deduct').data('amount', $(this).data('absent_deduction'));

    $('#p_late').text($(this).data('late'));
    $('#p_late_deduct').text(currency+' '+$(this).data('late_deduct'));
    $('#p_late_deduct').data('amount', $(this).data('late_deduct'));

    $('#p_ut').text($(this).data('ut'));
    $('#p_ut_deduct').text(currency+' '+$(this).data('ut_deduct'));
    $('#p_ut_deduct').data('amount', $(this).data('ut_deduct'));

    $('#p_grosspay_less').text(currency+' '+$(this).data('gross_pay_less'));
    $('#p_grosspay_less').data('amount', $(this).data('gross_pay_less'));
    // deductions
    $('#p_sss').text(currency+' '+$(this).data('sss'));
    $('#p_sss').data('amount', $(this).data('sss'));

    $('#p_sss_loan').text(currency+' '+$(this).data('sss_loan'));
    $('#p_sss_loan').data('amount', $(this).data('sss_loan'));

    $('#p_philhealth').text(currency+' '+$(this).data('philhealth'));
    $('#p_philhealth').data('amount', $(this).data('philhealth'));

    $('#p_pagibig').text(currency+' '+$(this).data('pagibig'));
    $('#p_pagibig').data('amount', $(this).data('pagibig'));

    $('#p_pagibig_loan').text(currency+' '+$(this).data('pagibig_loan'));
    $('#p_pagibig_loan').data('amount', $(this).data('pagibig_loan'));

    $('#p_cashadvance').text(currency+' '+$(this).data('cashadvance'));
    $('#p_cashadvance').data('amount', $(this).data('cashadvance'));

    $('#p_sal_deduct').text(currency+' '+$(this).data('sal_deduct'));
    $('#p_sal_deduct').data('amount', $(this).data('sal_deduct'));

    $('#p_total_deduct').text(currency+' '+$(this).data('total_deduct'));
    $('#p_total_deduct').data('amount', $(this).data('total_deduct'));
    //additionals
    $('#p_add_pay').text(currency+' '+$(this).data('add_pay'));
    $('#p_add_pay').data('amount', $(this).data('add_pay'));

    $('#p_ot_min').text($(this).data('ot_min'));
    $('#p_ot_pay').text(currency+' '+$(this).data('ot_pay'));
    $('#p_ot_pay').data('amount', $(this).data('ot_pay'));

    $('#p_net_pay').text(currency+' '+$(this).data('net_pay'));
    $('#p_net_pay').data('amount', $(this).data('net_pay'));

    $('#payroll_breakdown_modal').modal();
  });

  $(document).on('click', '#btn_print', function(){
    // $('#print_div').css('page-break-after','always');
    var modal = document.getElementById('payroll_breakdown_modal').innerHTML;
    var body = document.body.innerHTML;
    document.body.innerHTML = modal;
    window.print();
    document.body.innerHTML = body;
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

    gen_payslip_tbl(JSON.stringify(searchValue));

  });

});
