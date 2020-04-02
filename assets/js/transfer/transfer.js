$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_data_origin_tbl(dbname,search){
    var data_origin_tbl = $('#data_origin_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'transfer/Transfer/get_data_from_database_origin',
        type: 'post',
        data: {
          database_origin: dbname,
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

  $(document).on('change', '#data_origin', function(){
    var thiss = $(this);
    var database_origin = thiss.val();
    searchValue.search = $('#transfer_data').val();

    gen_data_origin_tbl(database_origin,JSON.stringify(searchValue));
  });

  $(document).on('change', '.select', function(){
    var thiss = $(this);
    var checked = thiss.is(':checked');
    if(checked === true){
      thiss.parents('tr').children('td').children('label').find('.emp_record').prop('checked', true);
      thiss.parents('tr').children('td').children('label').find('.contract_record').prop('checked', true);
      thiss.parents('tr').children('td').children('label').find('.time_record').prop('checked', true);
    }else{
      thiss.parents('tr').children('td').children('label').find('.emp_record').prop('checked', false);
      thiss.parents('tr').children('td').children('label').find('.contract_record').prop('checked', false);
      thiss.parents('tr').children('td').children('label').find('.time_record').prop('checked', false);
    }
    // console.log(thiss.val());
    // console.log(thiss.is(':checked'));
  });

  $(document).on('change', '#select_all', function(){
    var thiss = $(this);
    var checked = thiss.is(':checked');
    if(checked === true){
      thiss.parents('thead').siblings('tbody').children('tr').children('td').children('label').find('.select').prop('checked', true);
      $('.select').trigger('change');
    }else{
      thiss.parents('thead').siblings('tbody').children('tr').children('td').children('label').find('.select').prop('checked', true);
      $('.select').trigger('change');
    }
  });

  $(document).on('submit', '#transfer_form', function(e){
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

      var transfer_form = new FormData(this);
      var data_origin = $('#data_origin').val();
      var transfer_to = $('#transfer_to').val();
      var transfer_data = $('#transfer_data').val();
      var url = (transfer_data == 'employee_record')
      ? base_url+'transfer/Transfer/transfer'
      : base_url+'transfer/Transfer/transfer_applicant';

      transfer_form.append('data_origin',data_origin);
      transfer_form.append('transfer_to',transfer_to);

      $.ajax({
        url: url,
        type: 'post',
        data: transfer_form,
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }


  })
});
