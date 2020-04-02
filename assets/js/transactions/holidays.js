$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function gen_holiday_tbl(search){
    var workOrder_tbl = $('#holiday_tran_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Holidays/get_holiday_json',
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

  gen_holiday_tbl("");

  $(document).on('click', '#btn_add_holiday_modal', function(){
    $('#add_holiday_modal').modal();
  });

  $(document).on('click', '#btn_save_holiday', function(){
    var h_desc = $('#holiday_desc').val();
    var h_type = $('#holiday_type').val();
    var h_date = $('#holiday_date').val();
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
        errorMsg = "Please fill up all required fieldss.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'transactions/Holidays/add',
        type: 'post',
        data:{ h_desc, h_type, h_date},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $('#add_holiday_modal').modal('hide');
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            gen_holiday_tbl("");
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_edit_holiday_modal', function(){
    $('#edit_holiday_modal').modal();
    var update_id = $(this).data('update_id');
    var h_desc = $(this).data('h_desc');
    var h_type = $(this).data('h_type');
    var h_date = $(this).data('h_date');

    $('#edit_holiday_desc').val(h_desc);
    $('#edit_holiday_type option[value = "'+h_type+'"]').prop('selected', true);
    $('#edit_holiday_date').val(h_date);
    $('#current_h_desc').val(h_desc);
    $('#current_h_date').val(h_date);

    $('#btn_update_holiday').click(function(){
      $.ajax({
        url: base_url+'transactions/Holidays/update',
        type: 'post',
        data:{
          update_id,
          h_desc: $('#edit_holiday_desc').val(),
          h_type: $('#edit_holiday_type').val(),
          h_date: $('#edit_holiday_date').val(),
          c_hdesc: $('#current_h_desc').val(),
          c_hdate:   $('#current_h_date').val()
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $('#edit_holiday_modal').modal('hide');
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            gen_holiday_tbl("");
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    });
  });

  $(document).on('click', '.btn_del_holiday', function(){
    $('#del_holiday_modal').modal();
    var del_id = $(this).data('del_id');
    var del_desc = $(this).data('del_desc');

    $('.info_desc').text(del_desc);

    $('#btn_yes').click(function(){
      $('#del_holiday_modal').modal('hide');
      $.ajax({
        url: base_url+'transactions/Holidays/delete',
        type: 'post',
        data:{del_id},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            gen_holiday_tbl("");
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    });
  });
  // filter
  $(document).on('change', '#filter_by', function(){
    $('.filter_div').removeClass('active');
    switch ($(this).val()) {
      case "by_desc":
        $('.filter_div').hide("slow");
        $('#divDesc').show("slow");
        $('#divDesc').addClass('active');
        break;
      case "by_htype":
        $('.filter_div').hide("slow");
        $('#divHtype').show("slow");
        $('#divHtype').addClass('active');
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
    var searchText = $('#caTableTB').val();
    var filter = $('.filter_div.active').get(0).id;
    var sql = "";

    switch (filter) {
      case 'divDesc':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND a.description LIKE '"+searchValue+"%'";
        break;
      case 'divHtype':
        var searchValue = $('#h_type_filter').val();
        sql = " AND b.holidaytypeid = "+searchValue;
        break;
      case 'divDate':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        // alert(searchValue);
        sql = " AND a.date = '"+searchValue+"'";
        break;
      default:

    }

      gen_holiday_tbl(sql);
  });

});
