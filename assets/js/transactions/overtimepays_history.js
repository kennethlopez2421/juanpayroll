$(function(){
  var base_url = $("body").data('base_url');
  function ot_pay_history_tbl(search){
    var otpays_history_tbl = $('#otpays_history_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "columnDefs":[
        {targets: [0], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/OvertimePays_history/getotpays_history_json',
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

  ot_pay_history_tbl("");
});
