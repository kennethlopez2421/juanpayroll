$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var contract_id = "";  // contract id
  var contract_pm_id = ""; // contract payout medium id
  var action = 0; // check if action is update or save

  function gen_payout_tbl(search){
    var payout_tbl = $('#payout_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'contracts/Payout/get_payout_json',
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

  gen_payout_tbl("");

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_id":
  			$('.filter_div').hide("slow");
  			$('#divEmpID').show("slow");
  			$('#divEmpID').addClass('active');
  			break;
  		case "by_medium":
  			$('.filter_div').hide("slow");
  			$('#divMedium').show("slow");
  			$('#divMedium').addClass('active');
  			break;
  		case "by_bank":
  			$('.filter_div').hide("slow");
  			$('#divBank').show("slow");
  			$('#divBank').addClass('active');
  			break;
  		default:

  	}

  });

  $('#searchButton').click(function(){
    var filter = $('.filter_div.active').get(0).id;
    var sql = "";

    switch (filter) {
      case 'divName':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND ((CONCAT(a.last_name,',', a.first_name,' ', a.middle_name) LIKE '"+searchValue+"%'"+
              " OR a.first_name LIKE '"+searchValue+"%'"+
              " OR a.last_name LIKE '"+searchValue+"%'))";
        break;
      case 'divEmpID':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND a.employee_idno = '"+searchValue+"'";
        break;
      case 'divMedium':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND b.payout_medium = "+searchValue;
        break;
      case 'divBank':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND d.bank_id = "+searchValue;
      default:

    }

    gen_payout_tbl(sql);

  });

  $(document).on('change', '#p_medium', function(){
    ($(this).val() == 1 || $(this).val() == "")
    ? $('.card_info_div').hide('slow')
    : $('.card_info_div').show('slow');
  });

  $(document).on('click', '.btn_update_payout', function(){
    contract_id = "";
    contract_pm_id = "";

    var c_id = $(this).data('c_id');
    var cpm_id = $(this).data('cpm_id');
    var pm_id = parseInt($(this).data('pm_id'));
    var bank_id = $(this).data('bank_id');
    var card_number = $(this).data('card_number');
    var account_number = $(this).data('account_number');

    // check payout medium
    (pm_id != 1)? $('.card_info_div').show() : $('.card_info_div').hide();
    // check contract id
    (c_id != "") ? contract_id = c_id : notificationError('Error', 'Unable to get any Information about this employee.');
    // check contract payout medium
    (cpm_id != "") ? contract_pm_id = cpm_id: contract_pm_id = "";
    // check action 1 = update 2 = save
    (cpm_id != "") ? action = 1: action = 0;

    $('#p_medium option[value="'+pm_id+'"]').prop('selected',true);
    $('#p_bank option[value="'+bank_id+'"]').prop('selected',true);
    $('#p_card_number').val(card_number);
    $('#p_account_number').val(account_number);

    $('#payout_modal').modal();
  });

  $(document).on('click', '.btn_save_payout', function(){
    var p_medium = $('#p_medium').val();
    var p_bank = $('#p_bank2').val();
    var p_card_number = $('#p_card_number').val();
    var p_account_number = $('#p_account_number').val();
    // console.log(p_bank);
    // return false;

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
      // update
      if(action == 1){
        $.ajax({
          url: base_url+'contracts/Payout/update_payout_information',
          type: 'post',
          data:{
            contract_id,
            contract_pm_id,
            p_medium,
            p_bank,
            p_card_number,
            p_account_number
          },
          beforeSend: function(){
            $.LoadingOverlay('show');
          },
          success: function(data){
            $.LoadingOverlay('hide');
            if(data.success == 1){
              $('#payout_modal').modal('hide');
              notificationSuccess('Success', data.message);
              gen_payout_tbl("");
            }else{
              notificationError('Error', data.message);
            }
          }
        });
      }

      //save
      if(action == 0){
        $.ajax({
          url: base_url+'contracts/Payout/save_payout_information',
          type: 'post',
          data:{
            contract_id,
            p_medium,
            p_bank,
            p_card_number,
            p_account_number
          },
          beforeSend: function(){
            $.LoadingOverlay('show');
          },
          success: function(data){
            $.LoadingOverlay('hide');
            if(data.success == 1){
              $('#payout_modal').modal('hide');
              notificationSuccess('Success',data.message);
              gen_payout_tbl("");
            }else{
              notificationError('Error',data.message);
            }
          }
        });
      }

    }else{
      notificationError('Error', errorMsg);
    }
  });
});
