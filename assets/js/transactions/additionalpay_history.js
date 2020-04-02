$(function(){
  var base_url = $("body").data('base_url');
  function additionalpay_history_tbl(search){
    var workOrder_tbl = $('#additionalpay_history').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "columnDefs":[
        {targets: [0], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/AdditionalPays_history/getAdditionalPay_history_json',
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

  additionalpay_history_tbl("");
});
