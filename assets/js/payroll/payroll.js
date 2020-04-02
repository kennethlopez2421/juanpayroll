$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var total_sal = 0;

  $('#btn_save_payroll').prop('disabled', true);

  function toggle_generate_btn(){
    //this will check if all the details are filled
      var dept_dropdown = $("#dept").val();
      var emp_dropdown = $("#employee_id_no").val();
      var paytype_dropdown = $("#p_paytype").val();
      var date_from = $("#range_from").val();
      var date_to = $("#range_to").val();
    if((dept_dropdown == "") || (emp_dropdown == "") || (paytype_dropdown == "") || (date_from == "") || (date_to == "")){
      $("#gen_payroll").prop("disabled",true);
    }else{
      $("#gen_payroll").prop("disabled",false);
    }
  }

  function days_between(date1, date2) {

    var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
    var firstDate = new Date(date1);
    var secondDate = new Date(date2);

    var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
    return diffDays + 1;
  }

  function gen_dduction_log_tbl(search, data){
    var dduction_log_tbl = $('#dduction_log_tbl').DataTable( {
      "processing": true,
      "serverSide": false,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "order": [[1, 'asc']],
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/dduction_log_json',
        type: 'post',
        data: {
          searchValue: search,
          p_dept: data.p_dept,
          p_company: data.p_company,
          p_date_from: data.p_date_from,
          p_date_to: data.p_date_to,
          p_paytype: data.p_paytype,
          p_paytype_frequency: data.p_paytype_frequency,
          p_pay_day: data.pay_day
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          // console.log(data);
          // console.log(data.responseJSON.total_sss);
          var sum = data.responseJSON;
          $('#dsum_date').children('span').text(sum.date);
          $('#dsum_type').children('span').text(sum.p_type);
          // $('#dsum_sss').children('h5').text(sum.total_sss);
          // $('#dsum_philhealth').children('h5').text(sum.total_philhealth);
          // $('#dsum_pagibig').children('h5').text(sum.total_pagibig);
          // $('#dsum_deduction').children('h5').text(sum.total_sal_dduction);
          // $('#dsum_cadvance').children('h5').text(sum.total_cash_advance);
          $('.dduct_div_empty').hide();
          $('.dduct_div').show();
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

  function gen_additional_log_tbl(search, data){
    var additional_log_tbl = $('#additional_log_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "order": [[2, 'asc']],
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/additional_log_json',
        type: 'post',
        data: {
          searchValue: search,
          p_dept: data.p_dept,
          p_company: data.p_company,
          p_date_from: data.p_date_from,
          p_date_to: data.p_date_to,
          p_paytype: data.p_paytype,
          p_paytype_frequency: data.p_paytype_frequency
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          // console.log(data);
          // console.log(data.responseJSON.total_sss);
          var sum = data.responseJSON;
          $('#asum_date').children('span').text(sum.date);
          $('#asum_type').children('span').text(sum.p_type);
          // $('#asum_add_pay').children('h5').text(sum.total_add_pays);
          // $('#asum_ot_pay').children('h5').text(sum.total_ot_pay);
          $('.addDiv_empty').hide();
          $('.addDiv').show();
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
          from: data.from,
          to: data.to,
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

  function gen_manHours_log_tbl(search, data){
    var manHours_log_tbl = $('#manHours_log_tbl').DataTable( {
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
        url: base_url+'payroll/Payroll/man_hours_log_json',
        type: 'post',
        data: {
          searchValue: search,
          p_dept: data.p_dept,
          p_company: data.p_company,
          p_date_from: data.p_date_from,
          p_date_to: data.p_date_to,
          p_paytype: data.p_paytype,
          p_paytype_frequency: data.p_paytype_frequency
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          // console.log(data);
          // console.log(data.responseJSON.total_sss);
          var sum = data.responseJSON;
          $('#mhsum_date').children('span').text(sum.date);
          $('#mhsum_type').children('span').text(sum.p_type);
          // $('#asum_add_pay').children('h5').text(sum.total_add_pays);
          // $('#asum_ot_pay').children('h5').text(sum.total_ot_pay);
          $('.mh_div_empty').hide();
          $('.mh_div').show();
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
        {targets: [0,1,2,3,4,5,6,7], orderable: false}
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

  function gen_payroll_log_tbl(search, data){
    var payroll_log_tbl = $('#payroll_log_tbl').DataTable( {
      "processing": true,
      "serverSide": false,
      "searching": false,
      "lengthChange": false,
      "destroy": true,
      "autoWidth": false,
      "order": [[1, 'asc']],
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'payroll/Payroll/payroll_log_json',
        type: 'post',
        data: {
          searchValue: search,
          p_company: data.p_company,
          p_dept: data.p_dept,
          p_date_from: data.p_date_from,
          p_date_to: data.p_date_to,
          p_paytype: data.p_paytype,
          p_paytype_frequency: data.p_paytype_frequency,
          p_pay_day: data.pay_day
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');

          var sum = data.responseJSON;
          $('#psum_date').children('span').text(sum.date);
          $('#psum_type').children('span').text(sum.p_type);;
          $('.pm_div_empty').hide();
          $('.pm_div').show();
        },
        error: function(){

        }
      }
    });
  }

  function gen_psummary_breakdown_tbl(search, data){
    var psummary_breakdown_tbl = $('#psummary_breakdown_tbl').DataTable( {
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
          dduct: data.dduct,
          add: data.add
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          var breakdown = data.responseJSON;
          $('#breakdown_cat').text('Payroll');
          $('#breakdown_name').children('span').text(breakdown.fullname);
          $('#breakdown_date').children('span').text(breakdown.date);
          $('#total_gross').val(breakdown.grosspay);
          // console.log(breakdown.grosspay);
          $('#total_add').val(breakdown.add);
          $('#total_dduct').val(breakdown.dduct);
          $('#total_net').val(breakdown.netpay);
          $('#breakdown_type').children('span').text(breakdown.paytype);
          $('#modal_breakdown').modal();
        },
        error: function(){

        }
      }
    });
  }

  $(document).on('change', '#p_paytype', function(){
    var range = $(this).children('option:selected').data('range');
    var frequency = $(this).children('option:selected').data('frequency');
    $(this).data('mrange', range);
    $(this).data('mfrequency', frequency);
    // alert(range);
  });

  $(document).on('click', '#btn_gen_payroll', function(){
    var error = 0;
    var errorMsg = "";
    var company = $('#company').val();
    var pay_day = $('#pay_day').val();
    var diff = days_between($('#p_date_from').val(), $('#p_date_to').val());
    var range = $('#p_paytype').data('mrange') || "";
    var p_diff = range.toString().split('-');
    // console.log(diff);
    // console.log(p_diff);

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

    if(diff >= p_diff[0] && diff <= p_diff[1]){
      error = 0;
    }else{
      errorMsg = "Invalid date range. The Date difference should be "+range+" days";
      error = 1;
    }

    var d_data = {
      p_dept: 0,
      p_company: company,
      p_date_from: $('#p_date_from').val(),
      p_date_to: $('#p_date_to').val(),
      p_paytype: $('#p_paytype').val(),
      p_paytype_range: $('#p_paytype').data('mrange'),
      p_paytype_frequency: $('#p_paytype').data('mfrequency'),
      pay_day: pay_day
    };

    // console.log(d_data);
    // return;

    if(error == 0){
      $.ajax({
        url: base_url+'payroll/Payroll/gen_payroll',
        type: 'post',
        data: d_data,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            gen_dduction_log_tbl("",d_data);
            gen_additional_log_tbl("",d_data);
            gen_manHours_log_tbl("",d_data);
            gen_payroll_log_tbl("",d_data);
            $('#btn_save_payroll').prop('disabled', false);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '#btn_save_payroll', function(){
    var thiss = $(this);
    var error = 0;
    var errorMsg = "";

    $('.btn_verify').each(function(){
      if(parseInt($(this).data('status')) == 0){
        error = 1;
        errorMsg = "Please vefify all payroll transaction before saving";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'payroll/Payroll/save_payroll',
        type: 'post',
        beforeSend: function(){
          $.LoadingOverlay('show');
          thiss.prop('disbled', true);
          thiss.prop('text', 'Saving ... ');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          thiss.prop('disbled', false);
          thiss.prop('text', 'Save');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            setTimeout(function(){
              location.reload();
            },1000);
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }

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

    $('#nightdiff_hrs').text($(this).data('nightdiff_hrs'));
    $('#night_diff').text(currency+' '+$(this).data('night_diff'));
    $('#night_diff').data('amount', $(this).data('night_diff'));

    $('#p_net_pay').text(currency+' '+$(this).data('net_pay'));
    $('#p_net_pay').data('amount', $(this).data('net_pay'));

    $('#payroll_breakdown_modal').modal();
  });

  $(document).on('change', '#show_peso_chkbox', function(){
    var thiss = $(this);
    var ex_rate = $(this).data('ex_rate');
    console.log(thiss.prop('checked'));
    if(thiss.prop('checked') === true){
      $('.convert').each(function(){
        var amount = $(this).data('amount');
        var peso_convertion = parseFloat(amount) * parseFloat(ex_rate);
        peso_convertion = removeCommas(peso_convertion.toFixed(2));
        $(this).prepend(`<span class = "peso_convertion">(PHP ${numberWithCommas(peso_convertion)})</span> `);
      })
    }else{
      $('.peso_convertion').remove();
    }
  });

  $(document).on('click', '.btn_verify', function(){
    if(parseInt($(this).data('status')) == 0 ){
      $(this).html('<i class="fa fa-check mr-2"></i>Verified');
      $(this).removeClass('btn-info');
      $(this).addClass('btn-success');
      $(this).data('status', 1);
    }else{
      $(this).html('<i class="fa fa-eye mr-2"></i>Verify');
      $(this).removeClass('btn-success');
      $(this).addClass('btn-info');
      $(this).data('status', 0);
    }
  });

  $('#payroll_breakdown_modal').on('hidden.bs.modal', function(){
    $('#show_peso_chkbox').prop('checked', false);
  });

});
