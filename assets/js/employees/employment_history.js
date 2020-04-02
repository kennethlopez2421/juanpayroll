$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var search_filter = {
    filter: "",
    search: "",
    from: "",
    to: ""
  }

  function gen_employment_history_tbl(filter){
    var employment_history_tbl = $('#employment_history_tbl').DataTable( {
      "processing": true,
      "pageLength": 10,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'employees/Employment_history/get_employment_history_json',
        type: 'post',
        data: {
          searchValue: filter
        },
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
  gen_employment_history_tbl(JSON.stringify(search_filter));
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
  		default:

  	}

  });

  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    search_filter.filter = filter_by;
    // for single search
    if($("#"+filter_by).hasClass('single_search')){
      searchValue.search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      searchValue.from = $('#'+filter_by).children().find('.from').val();
      searchValue.to = $('#'+filter_by).children().find('.to').val();
    }

    gen_employment_history_tbl(JSON.stringify(search_filter));

  });

  //previous Contract
  $(document).on('click', '.btn_contract', function(){
    var previd = $(this).data('c_id');
    // alert(previd);
    $('#prev_sal_cat_ajax').html('');
    $('#prev_leave_tbl_ajax').html('');

    $.ajax({
      url: base_url+'employees/Employment_history/getprevcontract',
      type: 'post',
      data:{ previd },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success:function(data){
        $.LoadingOverlay('hide');
        // console.log(data.pos_id);
        if(data.success == 1){
          var prevData = data.prevData;
          var sched = JSON.parse(prevData.work_sched);

          var mon = sched.mon;
          var tue = sched.tue;
          var wed = sched.wed;
          var thu = sched.thu;
          var fri = sched.fri;
          var sat = sched.sat;
          var sun = sched.sun;
          var cdesc = prevData.contract_desc;
          var ext = cdesc.split('.');


          $('#prev_cWorkSite').val(prevData.workSite);
          $('#prev_cPos').val(prevData.position);
          $('#prev_cEmpLvl').val(prevData.emplvl);
          $('#prev_cStart').val(prevData.contract_start);
          $('#prev_cEnd').val(prevData.contract_end);
          $('#prev_contractStatus').val(prevData.empstatus);
          $('#prev_pay_medium').val(prevData.p_medium);
          // $('#prev_contractDescription').text(prevData.contract_desc);
          $('#prev_contractDescription').html(
            '<a href="'+base_url+prevData.contract_desc+'" download = "'+prevData.fullname+'.'+ext[1]+'">'+
              '<button class="btn btn-info btn-sm"><i class="fa fa-download mr-2"></i>Download Contract File</button>'+
            '</a>'
          )

          var sal_cat = JSON.parse(prevData.sal_cat);
          var total_sal = 0;
          $.each(sal_cat, function(i, val){
            total_sal += parseFloat(val['amount']);
            // replace compensation and salary w/ x if pos_id greater than 3
            if(parseInt(data.pos_id) > 3){
              var amount = replace_txt(numberWithCommas(val['amount']));
            }else{
              var amount = numberWithCommas(val['amount']);
            }
            $('#prev_sal_cat_ajax').append(
              '<tr>'+
                '<td>'+numberWithCommas(val['desc'])+'</td>'+
                '<td>'+prevData.currency+' '+amount+'</td>'+
              '</tr>'
            )
          });

          // replace compensation and salary w/ x if pos_id greater than 3
          if(parseInt(data.pos_id) > 3){
            var total = replace_txt(numberWithCommas(total_sal));
          }else{
            var total  = numberWithCommas(total_sal);
          }

          $('#prev_sal_cat_ajax').append(
            '<tr>'+
              '<td>Total</td>'+
              '<td>'+prevData.currency+' '+total+'</td>'+
            '</tr>'
          )

          var leave = JSON.parse(prevData.emp_leave);
          $.each(leave, function(i, val){
            $('#prev_leave_tbl_ajax').append(
              '<tr>'+
                '<td>'+val['desc']+'</td>'+
                '<td>'+val['days']+'</td>'+
              '</tr>'
            )
          });

          $('#sched_type option[value="'+prevData.sched_type+'"]').prop('selected', true);

          $('#prev_monTimeStart').val(mon[0]);
          $('#prev_monTimeEnd').val(mon[1]);
          $('#prev_monBreakStart').val(mon[3]);
          $('#prev_monBreakEnd').val(mon[4]);
          $('#prev_monTimeTotal').val(mon[2]);

          $('#prev_tueTimeStart').val(tue[0]);
          $('#prev_tueTimeEnd').val(tue[1]);
          $('#prev_tueBreakStart').val(tue[3]);
          $('#prev_tueBreakEnd').val(tue[4]);
          $('#prev_tueTimeTotal').val(tue[2]);

          $('#prev_wedTimeStart').val(wed[0]);
          $('#prev_wedTimeEnd').val(wed[1]);
          $('#prev_wedBreakStart').val(wed[3]);
          $('#prev_wedBreakEnd').val(wed[4]);
          $('#prev_wedTimeTotal').val(wed[2]);

          $('#prev_thuTimeStart').val(thu[0]);
          $('#prev_thuTimeEnd').val(thu[1]);
          $('#prev_thuBreakStart').val(thu[3]);
          $('#prev_thuBreakEnd').val(thu[4]);
          $('#prev_thuTimeTotal').val(thu[2]);

          $('#prev_friTimeStart').val(fri[0]);
          $('#prev_friTimeEnd').val(fri[1]);
          $('#prev_friBreakStart').val(fri[3]);
          $('#prev_friBreakEnd').val(fri[4]);
          $('#prev_friTimeTotal').val(fri[2]);

          $('#prev_satTimeStart').val(sat[0]);
          $('#prev_satTimeEnd').val(sat[1]);
          $('#prev_satBreakStart').val(sat[3]);
          $('#prev_satBreakEnd').val(sat[4]);
          $('#prev_satTimeTotal').val(sat[2]);

          $('#prev_sunTimeStart').val(sun[0]);
          $('#prev_sunTimeEnd').val(sun[1]);
          $('#prev_sunBreakStart').val(sun[3]);
          $('#prev_sunBreakEnd').val(sun[4]);
          $('#prev_sunTimeTotal').val(sun[2]);

          // replace compensation and salary w/ x if pos_id greater than 3
          var sss = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.sss) : prevData.sss;
          var philhealth = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.philhealth) : prevData.philhealth;
          var pagibig = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.pagibig) : prevData.pagibig;
          var tax = (parseInt(data.pos_id) > 3) ? replace_txt(prevData.tax) : prevData.tax;
          $('#prev_compSSS').val(sss);
          $('#prev_compPhilhealth').val(philhealth);
          $('#prev_compPagIbig').val(pagibig);
          $('#prev_compTax').val(tax);
          $('#prev_compPayType').val(prevData.paytype);
          // $('#prev_basic_pay').text("PHP " + numberWithCommas(prevData.basic_pay));
          // $('#prev_trans_pay').text("PHP " + numberWithCommas(prevData.trans_pay));
          // $('#prev_commu_pay').text("PHP " + numberWithCommas(prevData.commu_pay));
          // $('#prev_etc_pay').text("PHP " + numberWithCommas(prevData.etc_pay));
          // var total = parseFloat(prevData.basic_pay) + parseFloat(prevData.trans_pay) + parseFloat(prevData.commu_pay) + parseFloat(prevData.etc_pay);
          // $('#prev_total').text("PHP " + numberWithCommas(total));

          $('#prevContract_modal').modal();
        }else{
          notificationError('Error', data.message);
        }
      }
    })
  });

});
