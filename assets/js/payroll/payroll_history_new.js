$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_payroll_history_tbl(search){
    var payroll_history_tbl = $('#payroll_history_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll_history_new/get_payroll_history_json',
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

  // MANHOURS
  function gen_manHours_log_tbl(id){
    var manHours_log_tbl = $('#manHours_log_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll_history_new/get_manhours_tbl_json',
        type: 'post',
        data: { searchValue: id },
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

  function gen_manHours_breakdown_tbl(search, data){
    var manHours_breakdown_tbl = $('#manHours_breakdown_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "bPaginate": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/get_data_breakdown',
        type: 'post',
        data: {
          searchValue: search,
          active: data.active,
          emp_id: data.emp_id,
          from: data.from,
          to: data.to,
          type: data.type,
          frequency: data.frequency,
          dept: data.dept
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          var breakdown = data.responseJSON;
          $('#breakdown_cat').text(breakdown.category);
          $('#breakdown_name').children('span').text(breakdown.name);
          $('#breakdown_date').children('span').text(breakdown.date);
          $('#breakdown_type').children('span').text(breakdown.p_type);
          $('#modal_breakdown').modal();
        },
        error: function(){

        }
      }
    });
  }

  // DEDUCTIONS
  function gen_dduction_log_tbl(id){
    var dduction_log_tbl = $('#dduction_log_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll_history_new/get_dduction_log_tbl_json',
        type: 'post',
        data: { searchValue: id },
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

  function gen_deduction_breakdown_comp_tbl(search, data){
    var deduction_breakdown_comp_tbl = $('#deduction_breakdown_comp_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/get_data_breakdown',
        type: 'post',
        data: {
          searchValue: search,
          active: data.active,
          emp_id: data.emp_id,
          from: data.from,
          to: data.to,
          type: data.type,
          frequency: data.frequency,
          dept: data.dept,
          dd_type: 'comp',
          pay_day: data.pay_day
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          var breakdown = data.responseJSON;
          $('#breakdown_cat').text('Deductions');
          $('#breakdown_name').children('span').text(breakdown.fullname);
          $('#breakdown_date').children('span').text(breakdown.date);
          $('#breakdown_type').children('span').text(breakdown.paytype);
          $('#modal_breakdown').modal();
        },
        error: function(){

        }
      }
    });
  }

  function gen_deduction_breakdown_sd_tbl(search, data){
    var deduction_breakdown_sd_tbl = $('#deduction_breakdown_sd_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/get_data_breakdown',
        type: 'post',
        data: {
          searchValue: search,
          active: data.active,
          emp_id: data.emp_id,
          from: data.from,
          to: data.to,
          type: data.type,
          frequency: data.frequency,
          dept: data.dept,
          dd_type: 'sd'
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          var breakdown = data.responseJSON;
          $('#breakdown_cat').text('Deductions');
          // $('#breakdown_name').children('span').text(breakdown.fullname);
          $('#breakdown_date').children('span').text(breakdown.date);
          // $('#breakdown_type').children('span').text(breakdown.paytype);
          $('#modal_breakdown').modal();
        },
        error: function(){

        }
      }
    });
  }

  function gen_deduction_breakdown_ca_tbl(search, data){
    var deduction_breakdown_ca_tbl = $('#deduction_breakdown_ca_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/get_data_breakdown',
        type: 'post',
        data: {
          searchValue: search,
          active: data.active,
          emp_id: data.emp_id,
          from: data.from,
          to: data.to,
          type: data.type,
          frequency: data.frequency,
          dept: data.dept,
          dd_type: 'ca'
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          var breakdown = data.responseJSON;
          $('#breakdown_cat').text('Deductions');
          // $('#breakdown_name').children('span').text(breakdown.fullname);
          $('#breakdown_date').children('span').text(breakdown.date);
          // $('#breakdown_type').children('span').text(breakdown.paytype);
          $('#modal_breakdown').modal();
        },
        error: function(){

        }
      }
    });
  }

  // ADDITIONALS
  function gen_additional_log_tbl(id){
    var additional_log_tbl = $('#additional_log_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll_history_new/get_additional_log_tbl_json',
        type: 'post',
        data: { searchValue: id },
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

  function gen_payroll_log_tbl(id){
    var payroll_log_tbl = $('#payroll_log_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll_history_new/get_payroll_log_tbl_json',
        type: 'post',
        data: { searchValue: id },
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

  function gen_additionals_breakdown_add_tbl(search, data){
    var additionals_breakdown_add_tbl = $('#additionals_breakdown_add_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/get_data_breakdown',
        type: 'post',
        data: {
          searchValue: search,
          active: data.active,
          emp_id: data.emp_id,
          from: data.from,
          to: data.to,
          type: data.type,
          frequency: data.frequency,
          dept: data.dept,
          add_type: 'add'
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          var breakdown = data.responseJSON;
          $('#breakdown_cat').text('Additionals');
          $('#breakdown_name').children('span').text(breakdown.fullname);
          $('#breakdown_date').children('span').text(breakdown.date);
          $('#breakdown_type').children('span').text(breakdown.paytype);
          $('#modal_breakdown').modal();
        },
        error: function(){

        }
      }
    });
  }

  function gen_additionals_breakdown_ot_tbl(search, data){
    var additionals_breakdown_ot_tbl = $('#additionals_breakdown_ot_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/get_data_breakdown',
        type: 'post',
        data: {
          searchValue: search,
          active: data.active,
          emp_id: data.emp_id,
          from: "'"+data.from+"'",
          to: "'"+data.to+"'",
          type: data.type,
          frequency: data.frequency,
          dept: data.dept,
          add_type: 'ot'
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          var breakdown = data.responseJSON;
          $('#breakdown_cat').text('Additionals');
          $('#breakdown_name').children('span').text(breakdown.fullname);
          $('#breakdown_date').children('span').text(breakdown.date);
          $('#breakdown_type').children('span').text(breakdown.paytype);
          $('#modal_breakdown').modal();
        },
        error: function(){

        }
      }
    });
  }

  gen_payroll_history_tbl(JSON.stringify(searchValue));
  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_refno":
  			$('.filter_div').hide("slow");
  			$('#divRefno').show("slow");
  			$('#divRefno').addClass('active');
  			break;
  		case "by_company":
  			$('.filter_div').hide("slow");
  			$('#divCompany').show("slow");
  			$('#divCompany').addClass('active');
  			break;
  		case "by_paytype":
  			$('.filter_div').hide("slow");
  			$('#divPaytype').show("slow");
  			$('#divPaytype').addClass('active');
  			break;
  		case "by_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
  			break;
  		default:

  	}

  });

  $(document).on('click', '.btn_view', function(){
    var id = $(this).data('id');
    var manhours_id = $(this).data('manhours_id');
    var deduction_id = $(this).data('deduction_id');
    var additional_id = $(this).data('additional_id');
    $('#summary_wrapper').hide();
    gen_manHours_log_tbl(manhours_id);
    gen_dduction_log_tbl(deduction_id);
    gen_additional_log_tbl(additional_id);
    gen_payroll_log_tbl(id);
    $('#breakdown_wrapper').show();
  });

  $(document).on('click', '#btn_back', function(){
    $('#summary_wrapper').show();
    $('#breakdown_wrapper').hide();
  });

  $(document).on('click', '.btn_modal_breakdown', function(){
    var error = 0;
    var errorMsg = "";
    var active = $('.nav-link.active').data('type');
    var emp_id = $(this).get(0).id;
    var from = $(this).data('fromdate');
    var to = $(this).data('todate');
    var type = $(this).data('type');
    var frequency = $(this).data('frequency');
    var pay_day = $(this).data('pay_day');
    var dept = $(this).data('dept');
    var dduct = $(this).data('dduct');
    var add = $(this).data('add');

    var data = {
      active: active,
      emp_id: emp_id,
      from: from,
      to: to,
      type: type,
      frequency: frequency,
      dept: dept,
      dduct: dduct,
      add: add,
      pay_day: pay_day
    };

    // console.log(data);
    // return;

    var req = [emp_id, from, to, type, frequency];
    for (var i = 0; i < req.length; i++) {
      if(req[i] == ""){
        error = 1;
        errorMsg = "Unable to fetch any data";
      }
    }

    if(error == 0){
      switch (active) {
        case 'manhours':
          $('#table_ajax').html(
            '<table class="table table-striped table-bordered" id = "manHours_breakdown_tbl">'+
              '<thead>'+
                '<th>Date</th>'+
                '<th>Day Type</th>'+
                '<th>Time</th>'+
                '<th>Man Hours</th>'+
                '<th>Night Differentials(hrs)</th>'+
                '<th>Late(min)</th>'+
                '<th>Overtime(min)</th>'+
                '<th>Undertime(min)</th>'+
              '</thead>'+
            '</table>'
          );
          gen_manHours_breakdown_tbl("",data);
        break;
        case 'dduction':
          $('#table_ajax').html(
            '<ul class="nav nav-tabs mt-3">'+
                '<li class="nav-item">'+
                    '<a class="nav-link active" data-toggle="tab" href="#comp_tab" style="color:black;">Compensation</a>'+
                '</li>'+
                '<li class="nav-item">'+
                    '<a class="nav-link" data-toggle="tab" href="#sd_tab" style="color:black;" >Salary Deductions</a>'+
                '</li>'+
                '<li class="nav-item">'+
                    '<a class="nav-link" data-toggle="tab" href="#ca_tab" style="color:black;" >Cash Advance</a>'+
                '</li>'+
            '</ul>'+
            '<div class="tab-content">'+
              '<div class="tab-pane fade show active" id = "comp_tab">'+
                '<div class="table-responsive mt-3">'+
                  '<table class="table table-striped table-bordered" id = "deduction_breakdown_comp_tbl">'+
                    '<thead>'+
                      '<th>Date</th>'+
                      '<th>SSS</th>'+
                      '<th>SSS Loans</th>'+
                      '<th>Philhealth</th>'+
                      '<th>Pagibig</th>'+
                      '<th>Pagibig Loans</th>'+
                      '<th>Total</th>'+
                    '</thead>'+
                  '</table>'+
                '</div>'+
              '</div>'+
              '<div class="tab-pane fade" id = "sd_tab">'+
                '<div class="table-responsive mt-3">'+
                  '<table class="table table-striped table-bordered" id = "deduction_breakdown_sd_tbl">'+
                    '<thead>'+
                      '<th>Date</th>'+
                      '<th>Reason</th>'+
                      '<th>Amount</th>'+
                    '</thead>'+
                  '</table>'+
                '</div>'+
              '</div>'+
              '<div class="tab-pane fade" id = "ca_tab">'+
                '<div class="table-responsive mt-3">'+
                  '<table class="table table-striped table-bordered" id = "deduction_breakdown_ca_tbl">'+
                    '<thead>'+
                      '<th>Date</th>'+
                      '<th>Reason</th>'+
                      '<th>Amount</th>'+
                    '</thead>'+
                  '</table>'+
                '</div>'+
              '</div>'+
            '</div>'
          );
          gen_deduction_breakdown_sd_tbl("",data);
          gen_deduction_breakdown_ca_tbl("",data);
          gen_deduction_breakdown_comp_tbl("",data);
        break;
        case 'additional':
          $('#table_ajax').html(
            '<ul class="nav nav-tabs mt-3">'+
                '<li class="nav-item">'+
                    '<a class="nav-link active" data-toggle="tab" href="#add_tab" style="color:black;">Additional Pays</a>'+
                '</li>'+
                '<li class="nav-item">'+
                    '<a class="nav-link" data-toggle="tab" href="#ot_tab" style="color:black;" >Overtime Pays</a>'+
                '</li>'+
            '</ul>'+
            '<div class="tab-content">'+
              '<div class="tab-pane fade show active" id = "add_tab">'+
                '<div class="table-responsive mt-3">'+
                  '<table class="table table-striped table-bordered" id = "additionals_breakdown_add_tbl">'+
                    '<thead>'+
                      '<th>Date</th>'+
                      '<th>Reason</th>'+
                      '<th>Amount</th>'+
                    '</thead>'+
                  '</table>'+
                '</div>'+
              '</div>'+
              '<div class="tab-pane fade" id = "ot_tab">'+
                '<div class="table-responsive mt-3">'+
                  '<table class="table table-striped table-bordered" id = "additionals_breakdown_ot_tbl">'+
                    '<thead>'+
                      '<th>Date</th>'+
                      '<th>Reason</th>'+
                      '<th>Overtime Minutes</th>'+
                      '<th>Amount</th>'+
                    '</thead>'+
                  '</table>'+
                '</div>'+
              '</div>'+
            '</div>'
          );
          gen_additionals_breakdown_add_tbl("",data);
          gen_additionals_breakdown_ot_tbl("",data);
        break;
        case 'psummary':
          $('#table_ajax').html(
            '<table class="table table-striped table-bordered" id = "psummary_breakdown_tbl">'+
              '<thead>'+
                '<th>Date</th>'+
                '<th>Time</th>'+
                '<th>Hours</th>'+
                '<th>Day Type</th>'+
                '<th>Gross Pay</th>'+
                '<th>Additionals</th>'+
                '<th>Deductions</th>'+
                '<th>Net Pay</th>'+
              '</thead>'+
            '</table>'+
            '<div class="form-group mt-4">'+
              '<div class="col-md-3 offset-md-9 p-0">'+
                '<div class="input-group mb-3">'+
                  '<div class="input-group-prepend">'+
                    '<span class="input-group-text">Total Gross Pay</span>'+
                  '</div>'+
                  '<input type="text" id = "total_gross" class="form-control text-right" value = "">'+
                '</div>'+
              '</div>'+
            '</div>'+
            '<div class="form-group mt-4">'+
              '<div class="col-md-3 offset-md-9 p-0">'+
                '<div class="input-group mb-3">'+
                  '<div class="input-group-prepend">'+
                    '<span class="input-group-text">Total Additionals</span>'+
                  '</div>'+
                  '<input type="text" id = "total_add" class="form-control text-right" value = "">'+
                '</div>'+
              '</div>'+
            '</div>'+
            '<div class="form-group mt-4">'+
              '<div class="col-md-3 offset-md-9 p-0">'+
                '<div class="input-group mb-3">'+
                  '<div class="input-group-prepend">'+
                    '<span class="input-group-text">Total Deductions</span>'+
                  '</div>'+
                  '<input type="text" id = "total_dduct" class="form-control text-right" value = "">'+
                '</div>'+
              '</div>'+
            '</div>'+
            '<div class="form-group mt-4">'+
              '<div class="col-md-3 offset-md-9 p-0">'+
                '<div class="input-group mb-3">'+
                  '<div class="input-group-prepend">'+
                    '<span class="input-group-text">Total Net Pay</span>'+
                  '</div>'+
                  '<input type="text" id = "total_net" class="form-control text-right" value = "">'+
                '</div>'+
              '</div>'+
            '</div>'
          );
          gen_psummary_breakdown_tbl("",data);
        break;
        default:
      }
    }else{
      notificationError('Error',errorMsg);
    }
  });

  $(document).on('click', '.payroll_breakdown', function(){
    $('#convertion_wrapper').hide();

    var payroll_refno = $(this).data('payroll_refno');
    var emp_idno = $(this).data('emp_idno');
    var fromdate = $(this).data('fromdate');
    var todate = $(this).data('todate');
    var type = $(this).data('type');
    var frequency = $(this).data('frequency');
    var pay_day = $(this).data('pay_day');
    var company_id = $(this).data('company_id');
    var currency = $(this).data('currency');
    var ex_rate = $(this).data('ex_rate');

    // console.log(currency);
    // console.log(ex_rate);

    var payslip_data = {
      payroll_refno,
      emp_idno,
      fromdate,
      todate,
      type,
      frequency,
      pay_day,
      company_id
    };

    $.ajax({
      url: base_url+'payroll/Payroll_history_new/get_payslip_data',
      type: 'post',
      data:payslip_data,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          var payslip = data.message;
          var fullname = payslip.fullname;
          var date = payslip.date;
          var paytype = payslip.paytype;
          var wdays = payslip.wdays;
          // var currency = $(this).data('currency');
          if(currency != "PHP"){
            $('#convertion_wrapper').show();
          }

          $('#show_peso_chkbox').data('ex_rate', ex_rate);
          $('#show_peso_chkbox').data('currency', currency);

          // alert(paytype);
          $('#p_idno').text(payslip.emp_idno);
          $('#p_name').text(payslip.fullname);
          $('#p_paytype2').text(payslip.paytype);
          $('#p_date').text(payslip.date);
          // gross salary
          $('#p_wday').text(payslip.wdays);
          $('#p_grosspay').text(currency+' '+payslip.gross_pay);
          $('#p_grosspay').data('amount', payslip.gross_pay);

          $('#p_reg_holiday').text(payslip.reg_holiday);
          $('#p_reg_holiday_pay').text(currency+' '+payslip.reg_holiday_pay);
          $('#p_reg_holiday_pay').data('amount', payslip.reg_holiday_pay);

          $('#p_spl_holiday').text(payslip.spl_holiday);
          $('#p_spl_holiday_pay').text(currency+' '+payslip.spl_holiday_pay);
          $('#p_spl_holiday_pay').data('amount', payslip.spl_holiday_pay);

          $('#p_sunday').text(payslip.sunday);
          $('#p_sunday_pay').text(currency+' '+payslip.sunday_pay);
          $('#p_sunday_pay').data('amount', payslip.sunday_pay);
          // less
          $('#p_absent').text(payslip.absent);
          $('#p_absent_deduct').text(currency+' '+payslip.absent_deduction);
          $('#p_absent_deduct').data('amount', payslip.absent_deduction);

          $('#p_late').text(payslip.late);
          $('#p_late_deduct').text(currency+' '+payslip.late_deduct);
          $('#p_late_deduct').data('amount', payslip.late_deduct);

          $('#p_ut').text(payslip.ut);
          $('#p_ut_deduct').text(currency+' '+payslip.ut_deduct);
          $('#p_ut_deduct').data('amount', payslip.ut_deduct);

          $('#p_grosspay_less').text(currency+' '+payslip.gross_pay_less);
          $('#p_grosspay_less').data('amount', payslip.gross_pay_less);
          // deductions
          $('#p_sss').text(currency+' '+payslip.sss);
          $('#p_sss').data('amount', payslip.sss);

          $('#p_sss_loan').text(currency+' '+payslip.sss_loan);
          $('#p_sss_loan').data('amount', payslip.sss_loan);

          $('#p_philhealth').text(currency+' '+payslip.philhealth);
          $('#p_philhealth').data('amount', payslip.philhealth);

          $('#p_pagibig').text(currency+' '+payslip.pagibig);
          $('#p_pagibig').data('amount', payslip.pagibig);

          $('#p_pagibig_loan').text(currency+' '+payslip.pagibig_loan);
          $('#p_pagibig_loan').data('amount', payslip.pagibig_loan);

          $('#p_cashadvance').text(currency+' '+payslip.cashadvance);
          $('#p_cashadvance').data('amount', payslip.cashadvance);

          $('#p_sal_deduct').text(currency+' '+payslip.sal_deduct);
          $('#p_sal_deduct').data('amount', payslip.sal_deduct);

          $('#p_total_deduct').text(currency+' '+payslip.total_deduct);
          $('#p_total_deduct').data('amount', payslip.total_deduct);
          //additionals
          $('#p_add_pay').text(currency+' '+payslip.add_pay);
          $('#p_add_pay').data('amount', payslip.add_pay);

          $('#p_ot_min').text(payslip.ot_min);
          $('#p_ot_pay').text(currency+' '+payslip.ot_pay);
          $('#p_ot_pay').data('amount', payslip.ot_pay);

          $('#nightdiff_hrs').text(payslip.nightdiff_hrs);
          $('#night_diff').text(currency+' '+payslip.night_diff);
          $('#night_diff').data('amount', payslip.night_diff);

          $('#p_net_pay').text(currency+' '+payslip.net_pay);
          $('#p_net_pay').data('amount', payslip.net_pay);

          $('#payroll_breakdown_modal').modal();
        }else{
          notificationError('Error',data.message);
        }
      }
    });

  });

  $(document).on('change', '#show_peso_chkbox', function(){
    var thiss = $(this);
    var ex_rate = $(this).data('ex_rate');
    // console.log(thiss.prop('checked'));
    if(thiss.prop('checked') === true){
      $('.convert').each(function(){
        var amount = $(this).data('amount');
        var peso_convertion = parseFloat(amount) * parseFloat(ex_rate);
        peso_convertion = removeCommas(peso_convertion.toFixed(2));
        $(this).prepend(`<span class = "peso_convertion">(${numberWithCommas(peso_convertion)})</span> `);
      })
    }else{
      $('.peso_convertion').remove();
    }
  });

  $(document).on('change', '.payroll_update', function(){
    var uid = $(this).data('uid');
    var ref_no = $(this).data('ref_no');

    if(uid == ""){
      notificationError('Error', "Unable to update payroll status");
      return;
    }
    $('#confirm_refno').text(ref_no);
    $('#confirm_modal').modal();

    $('#btn_yes').click(function(){
      $.ajax({
        url: base_url+'payroll/Payroll_history_new/update',
        type: 'post',
        data:{uid, ref_no},
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_yes').attr('disabled', true);
        },
        success: function(data){
          $('#btn_yes').prop('disabled', false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#confirm_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_payroll_history_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    });
  });

  $(document).on('click', '.btn_bank_file', function(){
		$('#bank_file_modal').modal();
		$('#payroll_refno').val($(this).data('payroll_refno'));
    // console.log($(this).data('payroll_refno'));
	});

	$(document).on('change', '#bank', function(){
		var bank = $(this).val();
		try{
			var template = $(`[data-id = "${bank}"]`).get(0).id;
		}catch(err){
			var template = "default_template";
		}
		// var template = $(`[data-id = "${bank}"]`).get(0).id;
		var payrol_refno = $('#payroll_refno').val();
		var file_type = $('#file_type').val();
		$('.div_template').hide('slow');
		$('.div_template').removeClass('active');

		switch (template) {
			case 'bdo_template':
				$('#bdo_template').show('slow');
				$('#bdo_template').addClass('active');
				break;
			case 'metro_bank_template':
				$('#metro_bank_template').show('slow');
				$('#metro_bank_template').addClass('active');
				break;
			case 'ctbc_template':
				$('#ctbc_template').show('slow');
				$('#ctbc_template').addClass('active');
				break;
			default:
				$('#default_template').show('slow');
				$('#default_template').addClass('active');
		}

	});

	$(document).on('click', '#btn_generate', function(){
		var bank = $('#bank').val();
		try{
			var template = $(`[data-id = "${bank}"]`).get(0).id;
		}catch(err){
			var template = "default_template";
		}
		var payrol_refno = $('#payroll_refno').val();
		var file_type = $('#file_type').val();

		switch (template) {
			case 'bdo_template':
				var bdo_company_name = $('#bdo_company_name').val();
				var bdo_file_prefix = $('#bdo_file_prefix').val();
				var bdo_virtual_account = $('#bdo_virtual_account').val();
				var bdo_credit_date = $('#bdo_credit_date').val();
				var bdo_batch_no = $('#bdo_batch_no').val();

				var error = 0;
				var errorMsg = "";

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).css("border", "1px solid #ef4131");
				  }else{
				    $(this).css("border", "1px solid gainsboro");
				  }
				});

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).focus();
				    error = 1;
				    errorMsg = "Please fill up all required fields.";
				    return false;
				  }
				});

				if(error == 0){
					window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?bdo_company_name='+bdo_company_name+'&bdo_file_prefix='+bdo_file_prefix+'&bdo_virtual_account='+bdo_virtual_account+'&bdo_credit_date='+bdo_credit_date+'&bdo_batch_no='+bdo_batch_no);
				}else{
				  notificationError('Error', errorMsg);
				}
				break;
			case 'metro_bank_template':
				var metro_company_name = $('#metro_company_name').val();
				var metro_branch_code = $('#metro_branch_code').val();
				var metro_date = $('#metro_date').val();

				var error = 0;
				var errorMsg = "";

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).css("border", "1px solid #ef4131");
				  }else{
				    $(this).css("border", "1px solid gainsboro");
				  }
				});

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).focus();
				    error = 1;
				    errorMsg = "Please fill up all required fields.";
				    return false;
				  }
				});

				if(error == 0){
					window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?metro_company_name='+metro_company_name+'&metro_branch_code='+metro_branch_code+'&metro_date='+metro_date);
				}else{
				  notificationError('Error', errorMsg);
				}
				break;
			case 'ctbc_template':
				var ctbc_company_name = $('#ctbc_company_name').val();
				var ctbc_date = $('#ctbc_date').val();

				var error = 0;
				var errorMsg = "";

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).css("border", "1px solid #ef4131");
				  }else{
				    $(this).css("border", "1px solid gainsboro");
				  }
				});

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).focus();
				    error = 1;
				    errorMsg = "Please fill up all required fields.";
				    return false;
				  }
				});

				if(error == 0){
					window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?ctbc_company_name='+ctbc_company_name+'&ctbc_date='+ctbc_date);
				}else{
				  notificationError('Error', errorMsg);
				}
				break;
			default:
				var default_company_name = $('#default_company_name').val();
				var default_date = $('#default_date').val();
				window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?default_company_name='+default_company_name+'&default_date='+default_date);
		}
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

    gen_payroll_history_tbl(JSON.stringify(searchValue));

  });
});
