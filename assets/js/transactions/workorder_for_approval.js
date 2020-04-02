$(function(){
  var base_url = $("body").data('base_url');

  function workOrder_for_approval(search){
    var workOrder_tbl = $('#workOrder_tbl_for_approval').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/WorkOrder/getWorkOder_for_approval_json',
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

  // workOrder_for_approval("");

  $(document).on('click', '#approved_wo_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');

    workOrder_for_approval("");
    $('#approved_wo').tab('show');
  });
  // certify work order
  $(document).on('click', '.btn_certify_wo', function(){
    var wo_id = $(this).data('cid');
    $.ajax({
      url: base_url+'transactions/WorkOrder/updateWorkOrder_status',
      type: 'post',
      data:{wo_id: wo_id, status: 'certified'},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          workOrder_for_approval("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

});
