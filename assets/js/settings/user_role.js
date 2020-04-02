$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function gen_user_role_tlb(search){
    var user_role_tbl = $('#user_role_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/User_role/get_user_role_json',
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

  gen_user_role_tlb("");

  $(document).on('click', '#btnSearchButton', function(){
    var search = $('#pos_search').val();
    gen_user_role_tlb(search);
  });

  $(document).on('click', '#add_user_role', function(){
    $('#add_position').val("");
    $('#hierarchy_lvl').val("");
    $('#add_user_role_modal').modal();
  });

  $(document).on('submit', '#add_user_role_form', function(e){
    e.preventDefault();
    var form_data = $(this).serialize();

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
        url: base_url+'settings/User_role/create',
        type: 'post',
        data:form_data,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#add_user_role_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_user_role_tlb("");
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_edit', function(){
    var id = $(this).data('id');
    var main_nav = $(this).data('main_nav').toString().split(',');
    var content_nav = $(this).data('content_nav').toString().split(',');
    var func_nav = $(this).data('access_func_nav');
    // console.log(func_nav);
    // return ;

    $('#position').val($(this).data('desc'));
    $('#pos_id').val(id);
    $('.main_nav').prop('checked', false);
    $('.content_nav').prop('checked', false);
    $('.func_access').prop('checked', false);

    // main nav
    $('.main_nav').each(function(){
      var thiss = $(this);
      for (var i = 0; i < main_nav.length; i++) {
        if(parseInt(thiss.val()) == parseInt(main_nav[i])){
          thiss.prop('checked', true);
        }
      }
    });
    // content nav
    $('.content_nav').each(function(){
      var thiss = $(this);
      for (var x = 0; x < content_nav.length; x++) {
        if(parseInt(thiss.val()) == parseInt(content_nav[x])){
          thiss.prop('checked', true);
          if(func_nav.length > 0){
            func_nav.forEach((data) => {
              if(data.hasOwnProperty('id')){
                if(parseInt(data.id) == parseInt(content_nav[x])){
                  data.access_func_nav.forEach((arr) => {
                    $(`.func_nav_${parseInt(content_nav[x])}`).each(function(){
                      let self = $(this);
                      if(self.val() == arr){
                        self.prop('checked', true);
                      }
                    });
                  });
                }
              }
            });
          }
        }
      }
    });

    $('#user_role_modal').modal();

  });

  $(document).on('submit', '#user_role_form', function(e){
    e.preventDefault();
    var form_data = $(this).serialize();
    $.ajax({
      url: base_url+'settings/User_role/update',
      type: 'post',
      data:form_data,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#user_role_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_user_role_tlb("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '.btn_delete', function(){
    var id = $(this).data('id');
    var position = $(this).data('position');

    if(id == "" || position == ""){
      notificationError('Error', "Unable to delete this user role. Try to reload and try again.");
      return ;
    }

    $('#del_text').text(position);
    $('#delid').val(id);
    $('#delete_modal').modal();

  });

  $(document).on('submit', '#delete_user_role_form', function(e){
    e.preventDefault();

    $.ajax({
      url: base_url+'settings/User_role/delete',
      type: 'post',
      data: new FormData(this),
      contentType: false,
      processData: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#delete_modal').modal('hide');
          notificationSuccess('Success', data.message);
          gen_user_role_tlb("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '.parent', function(){
    var id = $(this).data('pid')
    var checked = $(this).is(':checked');
    if(checked === true){
      $(`.${id}_child`).prop('checked',true);
    }else{
      $(`.${id}_child`).prop('checked',false);
    }
  });
});
