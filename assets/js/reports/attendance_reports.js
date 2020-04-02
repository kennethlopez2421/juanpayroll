$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  var searchValue2  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_attendance_reports_tbl(search,search2){
    var attendance_reports_tbl = $('#attendance_reports_tbl').DataTable( {
      "processing": true,
      "ordering": true,
      "serverSide": false,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "order": [[6,'desc']],
      "columnDefs":[
        {
          targets: [0,1,2,3,4,5,6],
          orderable: false,
        }
      ],
      "columnDefs":[
        {
          targets: [4, 5],
          className: 'text-right'
        }
      ],
      "ajax":{
        url: base_url+'reports/Attendance_reports/get_attendance_reports_json',
        type: 'post',
        data: {
          searchValue: search,
          searchValue2: search2
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

    // console.log(attendance_reports_tbl);
  }

  function gen_offday_tbl(search){
    var offday_tbl = $('#offday_tbl').DataTable( {
      "processing": true,
      "serverSide": false,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Attendance_reports/offday_breakdown',
        type: 'post',
        data: { searchValue: search },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(){
          $.LoadingOverlay('hide');
          $('#offday_modal').modal();
        },
        error: function(){

        }
      }
    });
  }

  gen_attendance_reports_tbl(JSON.stringify(searchValue),JSON.stringify(searchValue2));
  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
    // $('.th_append').remove();
  	switch ($(this).val()) {
  		case "by_absent":
  			$('.filter_div').hide("slow");
  			$('#divAbsent').show("slow");
  			$('#divAbsent').addClass('active');
        // $('#tbl_head').append('<th class = "th_append">Minutes</th>');
  			break;
  		case "by_late":
  			$('.filter_div').hide("slow");
  			$('#divLate').show("slow");
  			$('#divLate').addClass('active');
  			break;
  		case "by_overtime":
  			$('.filter_div').hide("slow");
  			$('#divOvertime').show("slow");
  			$('#divOvertime').addClass('active');
  			break;
  		case "by_undertime":
  			$('.filter_div').hide("slow");
  			$('#divUndertime').show("slow");
  			$('#divUndertime').addClass('active');
  			break;
  		case "by_halfday":
  			$('.filter_div').hide("slow");
  			$('#divHalfday').show("slow");
  			$('#divHalfday').addClass('active');
  			break;
  		case "by_offday":
  			$('.filter_div').hide("slow");
  			$('#divOffDay').show("slow");
  			$('#divOffDay').addClass('active');
  			break;
      case "by_most_absent":
  			$('.filter_div').hide("slow");
  			$('#divMostAbsent').show("slow");
  			$('#divMostAbsent').addClass('active');
  			break;
      case "by_most_late":
  			$('.filter_div').hide("slow");
  			$('#divMostLate').show("slow");
  			$('#divMostLate').addClass('active');
  			break;
      case "by_most_overtime":
  			$('.filter_div').hide("slow");
  			$('#divMostOvertime').show("slow");
  			$('#divMostOvertime').addClass('active');
  			break;
      case "by_most_undertime":
  			$('.filter_div').hide("slow");
  			$('#divMostUndertime').show("slow");
  			$('#divMostUndertime').addClass('active');
  			break;
  		default:

  	}

  });

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
    // console.log(filter_by);

    //---- filter by ------

    switch (filter_by) {
      case 'divOffDay':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped" id = "attendance_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Position</th>'+
              '<th>Off Day Attendance</th>'+
              '<th>Status</th>'+
              '<th>Action</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      case 'divHalfday':
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped" id = "attendance_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Position</th>'+
              '<th>Date</th>'+
              '<th>Manhours</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
        break;
      default:
        $('#tbl_ajax').html(
          '<table class="table table-bordered table-striped" id = "attendance_reports_tbl">'+
            '<thead>'+
              '<th>Employee ID</th>'+
              '<th>Employee Name</th>'+
              '<th>Department</th>'+
              '<th>Position</th>'+
              '<th>Absent</th>'+
              '<th>Minutes</th>'+
              '<th>Status</th>'+
            '</thead>'+
          '</table>'
        );
    }

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

    //---- filter by 2 -----

    searchValue2.filter = filter_by2;
    searchValue2.search = $("#"+filter_by2).children('.searchArea2').val();

    // console.log(searchValue);
    // console.log(searchValue2);
    // return;

    gen_attendance_reports_tbl(JSON.stringify(searchValue),JSON.stringify(searchValue2));

  });

  $(document).on('click', '.btn_view_reports', function(){
    $('#view_reports_modal').modal();
  });

  $(document).on('click', '#btn_export', function(){
    var filter_by = $('.filter_div.active').get(0).id;
    var filter_by2 = $('.filter_div2.active').get(0).id;
    var search = undefined;
    var search2 = '';
    var from = undefined;
    var to_date = undefined;

    if($("#"+filter_by).hasClass('single_date')){
      search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      from = $('#'+filter_by).children().find('.from').val();
      to_date = $('#'+filter_by).children().find('.to').val();
    }

    search2 = $("#"+filter_by2).children('.searchArea2').val();

    window.open(base_url+"reports/Attendance_reports/export_to_excel/"+token+"/"+filter_by+"/"+filter_by2+"/"+search+"/"+from+"/"+to_date+"/"+search2);
  });

  $(document).on('click', '.btn_view_offday', function(){
    let view_id = $(this).data('view_id');
    let from = $(this).data('from');
    let to = $(this).data('to');
    let search = {
      view_id, from, to
    }

    gen_offday_tbl(JSON.stringify(search));
  });

});
