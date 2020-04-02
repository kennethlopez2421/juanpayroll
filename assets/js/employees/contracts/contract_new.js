$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  $(document).on('click', '#btn_newContract', function(){
    $('#new_contract_modal').modal();
  });
});
