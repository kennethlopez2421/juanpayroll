$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var itCounter = $('.itContainer').length;
  var error = 0;

  $('#emp_id').select2();
  $('#wo_date').datepicker({autoclose: true, todayHighlight: true});

  $(document).on('click', '#btn_wo_update', function(){
    var wo_id = $(this).data('update_id');
    var date = $('#wo_date').val();
    var sTime = $('#wo_sTime').val();
    var eTime = $('#wo_eTime').val();

    $.ajax({
      url: base_url+'/transactions/Workorder/update',
      type: 'post',
      data: {
        wo_id,
        date,
        sTime,
        eTime
      },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          setTimeout(function(){
            window.location.href = base_url+'/transactions/Workorder/index/'+token;
          },1500);
        }else{
          notificationError('Error',data.message);
        }
      }
    })
  });

  $(document).on('submit', '.itForm', function(e){
    e.preventDefault();
    var itForm = $(this).serialize();
    $.ajax({
      url: base_url+'transactions/Workorder/updateit',
      type: 'post',
      data: itForm,
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
    })
  });

  $(document).on('click', '.btn_del_itinerary', function(){
    var del_id = $(this).data('delid');
    var thiss = $(this);
    $('#delIt').modal();
    $('#btn_del_itinerary_yes').click(function(){
      $.ajax({
        url: base_url+'transactions/Workorder/destroy_it',
        type: 'post',
        data: {del_id},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            thiss.hide();
            notificationSuccess('Success',data.message);
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    });
  })

  $(document).on('click', '#print_workorder', function(){
    // alert();
    var modal = document.getElementById('work-order-form').innerHTML;
	  var body = document.body.innerHTML;
	  document.body.innerHTML = modal;
	  window.print();
	  document.body.innerHTML = body;
  });

});
