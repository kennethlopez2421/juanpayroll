$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var search_filter = {
    filter: "",
    search: ""
  };

  function gen_facial_feature_tbl(search){
    var facial_feature_tbl = $('#facial_feature_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'registerid/Register_facial/get_register_facial_json',
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

  gen_facial_feature_tbl('');

  // FILTER
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_id":
  			$('.filter_div').hide("slow");
  			$('#divEmpID').show("slow");
  			$('#divEmpID').addClass('active');
  			break;
  		case "by_dept":
  			$('.filter_div').hide("slow");
  			$('#divDept').show("slow");
  			$('#divDept').addClass('active');
  			break;
  		case "by_pos":
  			$('.filter_div').hide("slow");
  			$('#divPos').show("slow");
  			$('#divPos').addClass('active');
  			break;
  		default:

  	}

  });
  // SEARCH
  $('#searchButton').click(function(){
    search_filter.filter = $('.filter_div.active').get(0).id;
    search_filter.search = $('.filter_div.active').children('.searchArea').val();

    gen_facial_feature_tbl(JSON.stringify(search_filter));

  });
  // CALL VIEW IMG MODAL
  $(document).on('click', '.time_img', function(){
    var url = $(this).data('url');

    $('.view_image').css({
      "background-image": `url(${base_url}${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat"
    });
    $('#view_image_modal').modal();
  });
  // CALL DELETE
  $(document).on('click', '.btn_del_modal', function(){
    var del_id = $(this).data('del_id');
    var del_name = $(this).data('del_name');
    $('#del_id').val(del_id);
    $('#del_name').text(del_name);
    $('#delete_fr_modal').modal();


  });
  // DELETE FR
  $(document).on('click', '#btn_del_fr', function(){
    var del_id = $('#del_id').val();
    $.ajax({
      url: base_url+'registerid/Register_facial/delete',
      type: 'post',
      data:{del_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_fr_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_facial_feature_tbl('');
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  })

});
