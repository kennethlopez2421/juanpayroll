$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function get_contract_audit_trail_tbl(search){
    var registered_device_tbl = $('#contract_audit_trail_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Contract_audit_trail_reports/get_contract_audit_trail_json',
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

  get_contract_audit_trail_tbl(JSON.stringify(searchValue));

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');

  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
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

    // console.log(searchValue);
    // return;

    get_contract_audit_trail_tbl(JSON.stringify(searchValue));

  });

  $(document).on('click', '#btn_export', function(){
    var filter = $('.filter_div.active').get(0).id;
    var search = false;
    var from = false;
    var to = false;

    if($("#"+filter).hasClass('single_search')){
      search = $('#'+filter).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter).hasClass('dual_search')){
      from = $('#'+filter).children().find('.from').val();
      to = $('#'+filter).children().find('.to').val();
    }

    window.open(base_url+"reports/Contract_audit_trail_reports/export_to_excel/"+token+"/"+from+"/"+to+"/"+filter+"/"+search);

  });
});
