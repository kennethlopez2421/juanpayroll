$(function(){
  var base_url = $("body").data('base_url');
  $('.dateInput').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true
  }).datepicker("setDate", new Date());
  // var token = $('#token').val();
  function contract_history_tbl(search){
    var workOrder_tbl = $('#contract_history_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "columnDefs":[
        {targets: [1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'contracts/Contract_history/contract_histroy_json',
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

  contract_history_tbl("");
  // select filter by
  $(document).on('change', '#filter_by', function(){
    $('.filter_div').removeClass('active');
    switch ($(this).val()) {
      case "by_name":
        $('.filter_div').hide("slow");
        $('#divName').show("slow");
        $('#divName').addClass('active');
        break;
      case "by_dept":
        $('.filter_div').hide("slow");
        $('#divDept').show("slow");
        $('#divDept').addClass('active');
        break;
      case "by_pos":
        $('.filter_div').hide("slow");
        $('#divPos').show("slow");
        $('#divPos').addClass('active');
        break;
      case "by_c_date":
        $('.filter_div').hide("slow");
        $('#divCdate').show("slow");
        $('#divCdate').addClass('active');
        break;
      case "by_salary":
        $('.filter_div').hide("slow");
        $('#divSalRange').show("slow");
        $('#divSalRange').addClass('active');
        break;
      default:

    }

  });
  // get sub departmentid
  $(document).on('change', '#dept', function(){
    if($(this).val() != ""){
      $.ajax({
        url: base_url+'contracts/Contract_history/getSubDept',
        type: 'post',
        data:{dept_id: $(this).val()},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#subDept').html('<option value="">-------</option>');
            $.each(data.subDept, function(i, val){
              $('#subDept').append('<option value="'+val['subdeptid']+'">'+val['description']+'</option>');
            });
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }
  });
  // search btn
  $(document).on('click', '#btn_search_cHistory', function(){
    var filter = $('.filter_div.active').get(0).id;
    var status_arr = [];
    // var arr = [1, 2, 3, 4, 5];
    var ex_sql = "";
    $('.form-check-input:checked').each(function(){
      status_arr.push($(this).val());
    });
    // alert(arr);
    if($.inArray('active', status_arr) != -1){
      ex_sql += " AND contract_status = 'active'";
    }

    if($.inArray('inactive', status_arr) != -1){
      ex_sql += " AND contract_status = 'inactive'";
    }

    if($.inArray('all', status_arr) != -1){
      ex_sql = "";
    }
    // console.log(status_arr);
    // return false;
    var sql = "";
    switch (filter) {
      case 'divName':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND (CONCAT(f.last_name,',', f.first_name,' ', f.middle_name) LIKE '%"+searchValue+"%'"+
              " OR f.first_name LIKE '%"+searchValue+"%'"+
              " OR f.last_name LIKE '%"+searchValue+"%')";
        sql += ex_sql;
        contract_history_tbl(sql);
        break;
      case 'divDept':
        var dept = $('#dept').val();
        var subDept = $('#subDept').val();
        if(subDept == ""){ // department only
          sql = " AND b.deptId = "+dept;
        }else{ // with subDepartment
          sql = " AND b.deptId = "+dept+" AND b.subDeptId = "+subDept;
        }
        sql += ex_sql;
        contract_history_tbl(sql);
        break;
      case 'divPos':
        var pos_id = $('#search_pos').val();
        sql = " AND a.positionid = "+pos_id;
        sql += ex_sql;
        contract_history_tbl(sql);
        break;
      case 'divCdate':
        var start = $('#search_cStart_date').val();
        var end = $('#search_cEnd_date').val();
        sql = " AND contract_start BETWEEN "+start+" AND "+end;
        sql += ex_sql;
        contract_history_tbl(sql);
        break;
      case 'divSalRange':
        var sal_from = $('#search_salRange_from').val();
        var sal_to = $('#search_salRange_to').val();
        var sal_range_arr = [];
        // sql = " AND (a.basic_pay + a.trans_pay + a.commu_pay + a.etc_pay) BETWEEN "+sal_from+" AND "+sal_to;
        sal_range_arr.push(sal_from);
        sal_range_arr.push(sal_to);
        sal_range_arr.push(ex_sql);
        // sql += ex_sql;
        contract_history_tbl(sal_range_arr);
      default:

    }

    // alert(searchValue);
  });

  function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
  // view contract
  $(document).on('click', '.btn_view_contract', function(){
    var previd = $(this).data('cid');
    $('#prev_sal_cat_ajax').html('');
    $('#prev_leave_tbl_ajax').html('');
    $('#prev_company option[value=""]').prop('selected', true);
    $('#prev_contract_type option[value="fixed"]').prop('selected', true);

    $.ajax({
      url: base_url+'contracts/Contract_history/getPrevContract',
      type: 'post',
      data:{ previd },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success:function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          var prevData = data.prevData;
          // console.log(prevData);
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

          $('#fname').val(prevData.first_name);
          $('#mname').val(prevData.middle_name);
          $('#lname').val(prevData.last_name);
          $('#prev_cWorkSite').val(prevData.workSite);
          $('#prev_cPos').val(prevData.position);
          $('#prev_cEmpLvl').val(prevData.emplvl);
          $('#prev_cStart').val(prevData.contract_start);
          $('#prev_cEnd').val(prevData.contract_end);
          $('#prev_contractStatus').val(prevData.empstatus);
          $('#prev_pay_medium').val(prevData.p_medium);
          $('#prev_company option[value="'+prevData.company_id+'"]').prop('selected', true);
          $('#prev_contract_type option[value="'+prevData.contract_type+'"]').prop('selected', true);
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
          );

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
          // $('#prev_basic_pay').text(prevData.basic_pay);
          // $('#prev_trans_pay').text(prevData.trans_pay);
          // $('#prev_commu_pay').text(prevData.commu_pay);
          // $('#prev_etc_pay').text(prevData.etc_pay);

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
