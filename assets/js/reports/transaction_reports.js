$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  var searchValue  = {
    filter: "",
    filter2: "",
    search: "",
    tran_status: "",
    from: "",
    to: ""
  };

  function gen_transaction_reports_tbl(search){
    var attendance_reports_tbl = $('#transaction_reports_tbl').DataTable( {
      "processing": true,
      "pageLength": 10,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Transaction_reports/get_transaction_reports_json',
        type: 'post',
        data: {
          searchValue: search
        },
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
  gen_transaction_reports_tbl(JSON.stringify(searchValue));

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_add_pay":
  			$('.filter_div').hide("slow");
  			$('#divAddPay').show("slow");
  			$('#divAddPay').addClass('active');
  			break;
  		case "by_ca":
  			$('.filter_div').hide("slow");
  			$('#divCa').show("slow");
  			$('#divCa').addClass('active');
  			break;
  		case "by_leave":
  			$('.filter_div').hide("slow");
  			$('#divLeave').show("slow");
  			$('#divLeave').addClass('active');
  			break;
  		case "by_overtimepays":
  			$('.filter_div').hide("slow");
  			$('#divOvertimePays').show("slow");
  			$('#divOvertimePays').addClass('active');
  			break;
  		case "by_saldeduct":
  			$('.filter_div').hide("slow");
  			$('#divSalDeduct').show("slow");
  			$('#divSalDeduct').addClass('active');
  			break;
  		case "by_wOrder":
  			$('.filter_div').hide("slow");
  			$('#divWorkOrder').show("slow");
  			$('#divWorkOrder').addClass('active');
  			break;
  		case "by_offset":
  			$('.filter_div').hide("slow");
  			$('#divOffset').show("slow");
  			$('#divOffset').addClass('active');
  			break;
  		case "by_worksched":
  			$('.filter_div').hide("slow");
  			$('#divWorkSchedule').show("slow");
  			$('#divWorkSchedule').addClass('active');
  			break;
  		default:

  	}

  });
  // filter 2
  $(document).on('change', '#filter_by2', function(){
    $('.filter_div2').removeClass('active');

    switch ($(this).val()) {
      case '':
        $('.filter_div2').hide(400);
        $('#divEmpty').show(400);
        $('#divEmpty').addClass('active');
        break;
      case 'by_id':
        $('.filter_div2').hide(400);
        $('#divID').show(400);
        $('#divID').addClass('active');
        break;
      case 'by_name':
        $('.filter_div2').hide(400);
        $('#divName').show(400);
        $('#divName').addClass('active');
        break;
      case 'by_dept':
        $('.filter_div2').hide(400);
        $('#divDept').show(400);
        $('#divDept').addClass('active');
        break;
      case 'by_position':
        $('.filter_div2').hide(400);
        $('#divPos').show(400);
        $('#divPos').addClass('active');
        break;
      default:

    }
  });

  $(document).on('click', '#searchButton', function(){
    var filter_by = $('.filter_div.active').get(0).id;
    var filter_by2 = $('.filter_div2.active').get(0).id;
    var tran_status = $('input[name=tran_status]:checked').val();
    $('#tbl_ajax').html('');

    switch (filter_by) {
      case 'divAddPay':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">'+
            '<thead>'+
            '<th>Employee ID</th>'+
            '<th>Employee Name</th>'+
            '<th>Department</th>'+
            '<th>Date</th>'+
            '<th>Amount</th>'+
            '<th>Created by</th>'+
            '<th>Approved by</th>'+
            '<th>Certified by</th>'+
            '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divCa':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Date of Effectivity</th>'+
              '<th>Cash Advance</th>'+
              '<th>Remaining Balance</th>'+
              '<th>Created by</th>'+
              '<th>Approved by</th>'+
              '<th>Certified by</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divOvertimePays':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Overtime <small>(mins)</small></th>'+
              '<th>Date Rendered</th>'+
              '<th>Date Filed</th>'+
              '<th>Type</th>'+
              '<th>Created By</th>'+
              '<th>Approved By</th>'+
              '<th>Certified By</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divLeave':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Date of Filling</th>'+
              '<th>Date of Leave</th>'+
              '<th>Days</th>'+
              '<th>Created By</th>'+
              '<th>Approved By</th>'+
              '<th>Certified By</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divSalDeduct':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped" id = "transaction_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Departmen</th>'+
              '<th>Position</th>'+
              '<th>Date</th>'+
              '<th>Deduction</th>'+
              '<th>Amount</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divWorkOrder':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Location</th>'+
              '<th>Contact Person</th>'+
              '<th>Date</th>'+
              '<th>Time</th>'+
              '<th>Created By</th>'+
              '<th>Approved By</th>'+
              '<th>Certified By</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divOffset':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Offset <smal>(mins)</smal></th>'+
              '<th>Offset Type</th>'+
              '<th>Date to Offset</th>'+
              '<th>Date Filed</th>'+
              '<th>Created By</th>'+
              '<th>Approved By</th>'+
              '<th>Certified By</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divWorkSchedule':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped text-center" id = "transaction_reports_tbl">'+
            '<thead>'+
              '<th>Department</th>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Date From</th>'+
              '<th>Date To</th>'+
              '<th>Date Filed</th>'+
              '<th>Created By</th>'+
              '<th>Approved By</th>'+
              '<th>Certified By</th>'+
              '<th>Status</th>'+
              '<th>Action</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      default:

    }

    //---- filter by ------
    searchValue.filter = filter_by;
    searchValue.filter2 = filter_by2;
    searchValue.search = $("#"+filter_by2).children('.searchArea2').val();
    searchValue.tran_status = tran_status;
    searchValue.from = $('#'+filter_by).children().find('.from').val();
    searchValue.to = $('#'+filter_by).children().find('.to').val();

    var json_searchValue = JSON.stringify(searchValue);
    gen_transaction_reports_tbl(json_searchValue);

  });

  $(document).on('click', '.btn_worksched', function(){
    let work_sched = $(this).data('work_sched');
    let mon = work_sched.mon;
    let tue = work_sched.tue;
    let wed = work_sched.wed;
    let thu = work_sched.thu;
    let fri = work_sched.fri;
    let sat = work_sched.sat;
    let sun = work_sched.sun;

    $(`#edit_mon_ti`).val(mon[0]);
    $(`#edit_mon_to`).val(mon[1]);
    $(`#edit_mon_bi`).val(mon[3]);
    $(`#edit_mon_bo`).val(mon[4]);
    $(`#edit_mon_total`).val(mon[2]);

    $(`#edit_tue_ti`).val(tue[0]);
    $(`#edit_tue_to`).val(tue[1]);
    $(`#edit_tue_bi`).val(tue[3]);
    $(`#edit_tue_bo`).val(tue[4]);
    $(`#edit_tue_total`).val(tue[2]);

    $(`#edit_wed_ti`).val(wed[0]);
    $(`#edit_wed_to`).val(wed[1]);
    $(`#edit_wed_bi`).val(wed[3]);
    $(`#edit_wed_bo`).val(wed[4]);
    $(`#edit_wed_total`).val(wed[2]);

    $(`#edit_thu_ti`).val(thu[0]);
    $(`#edit_thu_to`).val(thu[1]);
    $(`#edit_thu_bi`).val(thu[3]);
    $(`#edit_thu_bo`).val(thu[4]);
    $(`#edit_thu_total`).val(thu[2]);

    $(`#edit_fri_ti`).val(fri[0]);
    $(`#edit_fri_to`).val(fri[1]);
    $(`#edit_fri_bi`).val(fri[3]);
    $(`#edit_fri_bo`).val(fri[4]);
    $(`#edit_fri_total`).val(fri[2]);

    $(`#edit_sat_ti`).val(sat[0]);
    $(`#edit_sat_to`).val(sat[1]);
    $(`#edit_sat_bi`).val(sat[3]);
    $(`#edit_sat_bo`).val(sat[4]);
    $(`#edit_sat_total`).val(sat[2]);

    $(`#edit_sun_ti`).val(sun[0]);
    $(`#edit_sun_to`).val(sun[1]);
    $(`#edit_sun_bi`).val(sun[3]);
    $(`#edit_sun_bo`).val(sun[4]);
    $(`#edit_sun_total`).val(sun[2]);
    $('#update_modal').modal();

  });

});
