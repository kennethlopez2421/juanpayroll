$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var search_filter = {
    filter: "",
    keyword: "",
    from: "",
    to: ""
  }

  function timerecord_logs_tbl(search){
    var timerecord_logs_tbl = $('#timerecord_logs_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1], orderable: false}
      ],
      "ajax":{
        url: base_url+'time_record/Timerecord_logs/get_timerecord_logs_json',
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

  timerecord_logs_tbl("");

  // FILTER
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_admin":
  			$('.filter_div').hide("slow");
  			$('#divAdmin').show("slow");
  			$('#divAdmin').addClass('active');
  			break;
  		case "by_timelog_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
  			break;
  		case "by_log_date":
  			$('.filter_div').hide("slow");
  			$('#divLogDate').show("slow");
  			$('#divLogDate').addClass('active');
  			break;
  		default:

  	}

  });

  // SEARCH
  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;

    search_filter.filter = filter_by;
    // for single search
    if($("#"+filter_by).hasClass('single_search')){
      search_filter.keyword = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      search_filter.from = $('#'+filter_by).children().find('.date_from').val();
      search_filter.to = $('#'+filter_by).children().find('.date_to').val();
    }

    // console.log(search_filter);
    // return;
    timerecord_logs_tbl(JSON.stringify(search_filter));

  });
});
