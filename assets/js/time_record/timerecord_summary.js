$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var search_filter = {
    emp_idno: '',
    emp_name: '',
    from: '',
    to: ''
  }

  function gen_timerecord_tbl(search){
    var gen_timerecord_tbl = $('#gen_timerecord_tbl').DataTable( {
      "processing": true,
      "serverSide": false,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "dom": 'Bfrtip',
      "buttons": [
          { extend: 'excel', text: 'Export to excel'}
      ],
      "columnDefs":[
        {targets: [0,1,3,4,5,6,7,8,9,10,11,12], orderable: false}
        // {targets: "_all", "className" : 'text-center'}
      ],
      "order": [[2, "asc"]],
      "ajax":{
        url: base_url+'time_record/Timerecord_summary/get_timerecord_summary_json',
        type: 'post',
        data: { searchValue: search },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          if(data.responseJSON.success == 1){
            $('#btn_save').show();
          }else{
            $('#btn_save').hide();
          }
        },
        error: function(){

        }
      }
    });
  }

  gen_timerecord_tbl(JSON.stringify(search_filter));

  $(document).on('click', '#searchButton', function(){
    var filter_by = $('.filter_div.active').get(0).id;
    search_filter.from = $('#'+filter_by).children().find('.from').val();
    search_filter.to = $('#'+filter_by).children().find('.to').val();
    gen_timerecord_tbl(JSON.stringify(search_filter));
  });

  $(document).on('click', '#btn_save', function(){
    var filter_by = $('.filter_div.active').get(0).id;
    search_filter.from = $('#'+filter_by).children().find('.from').val();
    search_filter.to = $('#'+filter_by).children().find('.to').val();

    $.ajax({
      url: base_url+'time_record/Timerecord_summary/create',
      type: 'post',
      data: {save_data: JSON.stringify(search_filter)},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          setTimeout(() => {location.reload()},2000);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '#btn_reset_modal', function(){
    $('#reset_modal').modal();
  });

  $(document).on('click', '#btn_reset_yes', function(){
    $.ajax({
      url: base_url+'time_record/Timerecord_summary/reset',
      type: 'post',
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_reset_yes').attr('disabled', true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#btn_reset_yes').prop('disabled',false);
        if(data.success == 1){
          $('#reset_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_timerecord_tbl(JSON.stringify(search_filter));
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

});
