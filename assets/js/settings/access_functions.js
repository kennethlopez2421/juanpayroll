$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_data_origin_tbl(search){
    var data_origin_tbl = $('#access_func_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Access_functions/get_access_functions_json',
        type: 'post',
        data: {
          searchValue: search
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 0){
            notificationError('Error', data.message);
          }
        },
        error: function(){

        }
      }
    });
  };

  gen_data_origin_tbl(JSON.stringify(searchValue));

  $(document).on('change', '.select_all', function(){
    var thiss = $(this);
    var checked = thiss.is(':checked');
    if(checked === true){
      thiss.parents('tr').children('td').children('label').find('.access_func').prop('checked', true);
    }else{
      thiss.parents('tr').children('td').children('label').find('.access_func').prop('checked', false);
    }
  });

  $(document).on('submit', '#access_func_form', function(e){
    e.preventDefault();
    $.ajax({
      url: base_url+'settings/Access_functions/create',
      type: 'post',
      data: new FormData(this),
      processData: false,
      contentType: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          gen_data_origin_tbl(JSON.stringify(searchValue));
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
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

    gen_data_origin_tbl(JSON.stringify(searchValue));

  });
});
