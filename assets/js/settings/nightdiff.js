$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };

  function gen_nightdiff_tbl(search){
    var nightdiff_tbl = $('#nightdiff_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Nightdiff/get_nightdiff_json',
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

  gen_nightdiff_tbl(JSON.stringify(searchValue));

  $(document).on('click', '.btn_apply', function(){
    let uid = $(this).data('uid');
    let start = $('#start').val();
    let end = $('#end').val();
    let percent = $('#percent').val();
    let status = $('#status').val();

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
        url: base_url+'settings/Nightdiff/update',
        type: 'post',
        data:{
          uid, start, end, percent, status
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            gen_nightdiff_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
});
