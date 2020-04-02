$(function(){
  var base_url = $("body").data('base_url');

  function workOrder_for_certification(search){
    var workOrder_tbl = $('#workOrder_tbl_for_certification').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/WorkOrder/getWordOrder_for_certification_json',
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

  // workOrder_for_certification("");

  $(document).on('click', '#certified_wo_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');

    workOrder_for_certification("");
    $('#certified_wo').tab('show');
  });

});
