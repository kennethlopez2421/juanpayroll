$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var filter = {
    month: "",
    filter_by: "",
    keyword: ""
  };

  function gen_sss_reports_tbl(filter){
    var sss_reports_tbl = $('#sss_reports_tbl').DataTable( {
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
        url: base_url+'reports/Sss_reports/get_sss_reports_json',
        type: 'post',
        data: {
          searchValue: filter
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

  gen_sss_reports_tbl(JSON.stringify(filter));

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div2').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div2').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_id":
  			$('.filter_div2').hide("slow");
  			$('#divEmpID').show("slow");
  			$('#divEmpID').addClass('active');
  			break;
  		case "by_dept":
  			$('.filter_div2').hide("slow");
  			$('#divDept').show("slow");
  			$('#divDept').addClass('active');
  			break;
      case "by_company":
  			$('.filter_div2').hide("slow");
  			$('#divCompany').show("slow");
  			$('#divCompany').addClass('active');
  			break;
  		default:
        $('.filter_div2').hide("slow");
        $('#divEmpty').show("slow");
        $('#divEmpty').addClass('active');
        break;
  	}

  });

  $('#searchButton').click(function(){
    var month = $('#month').val();
    var filter_by = $('.filter_div2.active').get(0).id;
    var keyword = $(`#${filter_by}`).children('.searchArea').val();

    filter.month = month;
    filter.filter_by = filter_by;
    filter.keyword = keyword;

    gen_sss_reports_tbl(JSON.stringify(filter));

  });

  $(document).on('click', '#btnExport', function(){
    var month = $('#month').val();
    var filter_by = $('.filter_div2.active').get(0).id;
    var keyword = $(`#${filter_by}`).children('.searchArea').val();

    filter.month = month;
    filter.filter_by = filter_by;
    filter.keyword = keyword;

    if(month == ""){
      notificationError('Error','You need to select the month of report you want to export');
      return false;
    }

    $.ajax({
      url: base_url+'reports/Sss_reports/check_export_to_excel_sss_reports',
      type: 'post',
      data:{search: JSON.stringify(filter)},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          window.open(base_url + 'reports/Sss_reports/export_to_excel_sss_reports/'+month);
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

});
