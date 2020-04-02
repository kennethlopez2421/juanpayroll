$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_item_cat_tbl(search){
    var item_cat_tbl = $('#item_cat_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Items_category/get_items_category_json',
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

  gen_item_cat_tbl(JSON.stringify(searchValue));

  // ADD
  $(document).on('click', '#btn_add_modal', function(){
    $('#add_modal').modal();
  });

  $(document).on('submit', '#add_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";

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

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Items_category/create',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_save').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_save').prop('disabled', false);
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_item_cat_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  // EDIT
  $(document).on('click', '.btn_edit', function(){
    let uid = $(this).data('uid');
    let cat_name = $(this).data('cat_name');

    $('#edit_cat_name').val(cat_name);
    $('#uid').val(uid);
    $('#edit_modal').modal();
  });

  $(document).on('submit', '#edit_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";

    $('.rq2').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq2').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Items_category/update',
        type: 'post',
        data:new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_update').attr('disabled',true)
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_update').prop('disabled', false);
          if(data.success == 1){
            $('#edit_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_item_cat_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  // DELETE
  $(document).on('click', '.btn_del', function(){
    let cat_name = $(this).data('cat_name');
    let delid = $(this).data('delid');
    $('.info_desc').html(cat_name);
    $('#delid').val(delid);

    $('#delete_modal').modal();
  });

  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";

    $('.rq3').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq3').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Items_category/delete',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_delete').attr('disabled', true);
        },
        success: function(data){
          $('#btn_delete').prop('disabled' ,false);
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#delete_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_item_cat_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  // SEARCH FILTER
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

    gen_item_cat_tbl(JSON.stringify(searchValue));
  });
});
