$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  var search = {
    from: $('.first_day').val(),
    to: $('.last_day').val(),
    filter: "",
    keyword: "",
  }

  function gen_compensation_reports_tbl(search){
    var compnesation_reports_tbl = $('#compnesation_reports_tbl').DataTable( {
      "processing": true,
      "pageLength": 10,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6,7,8], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Compensation_reports/get_compensation_reports_json',
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

  gen_compensation_reports_tbl(JSON.stringify(search));

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
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
      case 'by_ref_no':
        $('.filter_div2').hide(400);
        $('#divRefNo').show(400);
        $('#divRefNo').addClass('active');
        break;
      default:

    }
  });

  $(document).on('click', '#searchButton', function(){
    var from = $('.first_day').val();
    var to = $('.last_day').val();
    var filter_by = $('.filter_div2.active').get(0).id;
    var keyword = $('#'+filter_by).children('.searchArea2').val() || "";

    search.from = from;
    search.to = to;
    search.filter = filter_by;
    search.keyword = keyword;
    var search2 = JSON.stringify(search);

    gen_compensation_reports_tbl(search2);

  });

  $(document).on('click', '#btn_export', function(){
    var search = undefined;
    var filter_by2 = $('.filter_div2.active').get(0).id;
    var from = $('.from').val();
    var to = $('.to').val();
    search = $("#"+filter_by2).children('.searchArea2').val();

    window.open(base_url+"reports/Compensation_reports/export_to_excel/"+token+"/"+from+"/"+to+"/"+filter_by2+"/"+search);

  });

});
