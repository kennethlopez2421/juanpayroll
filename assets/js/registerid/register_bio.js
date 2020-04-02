$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var search_filter = {
    filter: "",
    search: ""
  };

  function gen_bio_tbl(search){
    var gen_bio_tbl = $('#gen_bio_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'registerid/Register_bio/get_biometrics_json',
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

  gen_bio_tbl('');
  // filter
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
  			$('#divBioId').show("slow");
  			$('#divBioId').addClass('active');
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
  // FILTER SEND
  $('#searchButton').click(function(){
    search_filter.filter = $('.filter_div.active').get(0).id;
    search_filter.search = $('.filter_div.active').children('.searchArea').val();

    gen_bio_tbl(JSON.stringify(search_filter));

  });
  // ADD BIOMETRICS MODAL
  $(document).on('click', '#btn_add_modal', function(){
    $('#add_bio_modal').modal();
  });
  // ADD BIOMETRICS ID
  $(document).on('click', '#btn_save_bio', function(){
    var add_empid = $('#add_empid').val();
    var add_bio_id = $('#add_bio_id').val();

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
        url: base_url+'registerid/Register_bio/create',
        type: 'post',
        data:{add_empid, add_bio_id},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_bio_modal').modal('hide');
            notificationSuccess('Success',data.message);
            gen_bio_tbl('');
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // EDIT BIOMETRICS MODAL
  $(document).on('click', '.btn_edit_modal', function(){
    $('#edit_bio_id').val($(this).data('bio_id'));
    $('#prev_bio_id').val($(this).data('bio_id'));
    $('#uid').val($(this).data('uid'));
    $('#edit_bio_modal').modal();
  });
  // EDIT BIOMETRICS ID
  $(document).on('click', '#btn_update_bio', function(){
    var edit_bio_id = $('#edit_bio_id').val();
    var prev_bio_id = $('#prev_bio_id').val();
    var uid = $('#uid').val();

    if(edit_bio_id == prev_bio_id){
      $('#edit_bio_modal').modal('hide');
      return false;
    }

    $.ajax({
      url: base_url+'registerid/Register_bio/update',
      type: 'post',
      data:{edit_bio_id, prev_bio_id, uid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#edit_bio_modal').modal('hide');
          notificationSuccess('Success',data.message);
          gen_bio_tbl('');
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // DELETE BIOMETRICS MODAL
  $(document).on('click', '.btn_del_modal', function(){
    $('#del_name').text($(this).data('name'));
    $('#del_id').val($(this).data('delid'));
    $('#delete_bio_modal').modal();
  });
  // DELETE BIOMETRICS ID
  $(document).on('click', '#btn_del_bio', function(){
    var del_id = $('#del_id').val();
    $.ajax({
      url: base_url+'registerid/Register_bio/delete',
      type: 'post',
      data:{del_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_bio_modal').modal('hide');
          notificationSuccess('Success',data.message);
          gen_bio_tbl('');
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });
});
