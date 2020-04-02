$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  function ca_payment_tbl(search){
    var ca_payment_tbl = $('#ca_payment_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Cashadvance_payment_history/get_cashadvance_payment_history_json',
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
  ca_payment_tbl('');

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
      case "by_dept":
        $('.filter_div').hide("slow");
        $('#divDept').show("slow");
        $('#divDept').addClass('active');
        break;
      case "by_date":
        $('.filter_div').hide("slow");
        $('#divDate').show("slow");
        $('#divDate').addClass('active');
        break;
      case "by_amount":
        $('.filter_div').hide("slow");
        $('#divAmount').show("slow");
        $('#divAmount').addClass('active');
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
        sql = " AND (CONCAT(c.last_name,',', c.first_name,' ', c.middle_name) LIKE '"+searchValue+"%'"+
              " OR c.first_name LIKE '"+searchValue+"%'"+
              " OR c.last_name LIKE '"+searchValue+"%')";
        // sql += ex_sql;
        // contract_history_tbl(sql);
        break;
      case 'divEmpID':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND c.employee_idno = '"+searchValue+"'";
        break;
      case 'divDate':
        var start = $('#date_from').val();
        var end = $('#date_to').val();
        sql = " AND a.cutoff_from = '"+start+"' AND a.cutoff_to = '"+end+"'";
        break;
      case 'divAmount':
        var sal_from = $('#amount_from').val() || 0;
        var sal_to = $('#amount_to').val() || 0;
        sql = " AND a.ca_payment BETWEEN "+sal_from+" AND "+sal_to;
      default:

    }

    ca_payment_tbl(sql);

  });

  $(document).on('click', '.btn_view_ca', function(){
    var thiss = $(this);

    $.ajax({
      url: base_url+'transactions/Cashadvance_payment_history/get_ca_payment_breakdown',
      type: 'post',
      data:{ca_id: thiss.get(0).id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#ca_cutoff').val(data.ca.cutoff_from+" - "+data.ca.cutoff_to);
          $('#ca_emp_name').val(data.ca.fullname);
          $('#ca_reason').val(data.ca.reason);
          $('#ca_total').val(data.ca_total);
          $('#ca_payment').val(data.ca_payment);
          $('#ca_balance').val(data.ca_balance);
          $('#ca_payment_modal').modal();
        }else{

        }
      }
    });
  });

});
