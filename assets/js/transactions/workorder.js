$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var sql = "";

  $('#search_date').datepicker({format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}).datepicker("setDate", new Date());

  function gen_workOrder_tbl(search){
    var workOrder_tbl = $('#workOrder_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Workorder/getworkoder_json',
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

  function workOrder_for_approval(search){
    var workOrder_tbl = $('#workOrder_tbl_for_approval').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Workorder/getworkoder_for_approval_json',
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

  function workOrder_for_certification(search){
    var workOrder_tbl = $('#workOrder_tbl_for_certification').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Workorder/getwordorder_for_certification_json',
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

  gen_workOrder_tbl("");
  // seach work order
  $(document).on('click', '#btn_search_wo', function(){
    var search_wo_type = $('#search_wo_type').val();
    if(search_wo_type == "search_by_name"){
      var searchValue = $('#search_name').val();
    }else{
      var searchValue = $('#search_date').val();
    }
    var tab = $('.nav-link.active').data('stype');
    switch (tab) {
      case "waiting":
        gen_workOrder_tbl(searchValue);
        break;
      case "approved":
        workOrder_for_approval(searchValue);
        break;
      case "certified":
        workOrder_for_certification(searchValue);
        break;
      default:

    }
    // gen_workOrder_tbl(searchValue);
  });
  $(document).on('change', '#search_wo_type', function(){
    // alert();
    switch ($(this).val()) {
      case "search_by_name":
        $('.divSearch').hide("slow");
        $('#divSearchName').show("slow");
        break;
      case "search_by_date":
        $('.divSearch').hide("slow");
        $('#divSearchDate').show("slow");
      default:
    }
  });

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
      case "by_date_filed":
        $('.filter_div').hide("slow");
        $('#divDateRendered').show("slow");
        $('#divDateRendered').addClass('active');
        break;
      // case "by_amount":
      //   $('.filter_div').hide("slow");
      //   $('#divAmount').show("slow");
      //   $('#divAmount').addClass('active');
      //   break;
      default:

    }

  });
  // user access dept
  $(document).on('change', '#dept', function(){
    var deptId = $(this).val();
    $.ajax({
      url: base_url+'transactions/Workorder/get_employee_by_dept',
      type: 'post',
      data:{dept_id: deptId},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#employee_id_no').html('<option value = "">------</option>');
          $.each(data.emp, function(i, val){
            $('#employee_id_no').append('<option value = "'+val['employee_idno']+'">'+val['fullname']+' ('+val['employee_idno']+')</option>');
          });
        }else{
          notificationError('Error', data.message);
          $('#employee_id_no').html('<option value = "">------</option>');
        }
      }
    });
  });
  // search
  $('#searchButton').click(function(){
    var searchText = $('#caTableTB').val();
    var tab = $('.nav-link.active').data('stype');
    var filter = $('.filter_div.active').get(0).id;

    switch (filter) {
      case 'divName':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND (CONCAT(b.last_name,',', b.first_name,' ', b.middle_name) LIKE '"+searchValue+"%'"+
              " OR b.first_name LIKE '"+searchValue+"%'"+
              " OR b.last_name LIKE '"+searchValue+"%')";
        // sql += ex_sql;
        // contract_history_tbl(sql);
        break;
      case 'divEmpID':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND b.employee_idno = '"+searchValue+"'";
        break;
      case 'divDept':
        var searchValue = $('.filter_div.active').children('.searchArea').val();
        sql = " AND e.deptId = "+searchValue;
        break;
      case 'divDate':
        var start = $('#date_from').val();
        var end = $('#date_to').val();
        sql = " AND a.date BETWEEN '"+start+"' AND '"+end+"'";
        // sql += ex_sql;
        // contract_history_tbl(sql);
        break;
      case 'divDateRendered':
        var start = $('#date_from2').val();
        var end = $('#date_to2').val();
        sql = " AND DATE(a.updated_at) BETWEEN '"+start+"' AND '"+end+"'";
        // sql += ex_sql;
        // contract_history_tbl(sql);
        break;
      default:

    }
    switch (tab) {
      case 'waiting':
        // caTable.search(searchText).draw();
        gen_workOrder_tbl(sql);
        break;
      case 'approved':
        workOrder_for_approval(sql);
        break;
      case 'certified':
        workOrder_for_certification(sql)
        break;
      default:

    }
    // caTable.search(searchText).draw();
  });

  // waiting nav
  $(document).on('click', '#waiting_wo_nav', function(){
    $('.nav-link').removeClass('active');
    $('.btn_batch').hide();
    $('.btn_batch_approve').show();
    $(this).addClass('active');
    sql = "";
    gen_workOrder_tbl(sql);
    $('#waiting_wo').tab('show');
  });
  // approved nav
  $(document).on('click', '#approved_wo_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    $('.btn_batch').hide();
    $('.btn_batch_certify').show();
    sql = "";
    workOrder_for_approval(sql);
    $('#approved_wo').tab('show');
  });
  // certified nav
  $(document).on('click', '#certified_wo_nav', function(){
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    $('.btn_batch').hide();
    sql = "";
    workOrder_for_certification(sql);
    $('#certified_wo').tab('show');
  });

  // approved work order
  $(document).on('click', '.btn_approve_wo', function(){
    var wo_id = $(this).data('apid');
    $.ajax({
      url: base_url+'transactions/Workorder/updateworkorder_status',
      type: 'post',
      data:{wo_id: wo_id, status: 'approved', update: 'approved_by'},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          gen_workOrder_tbl(sql);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // certify work order
  $(document).on('click', '.btn_certify_wo', function(){
    var wo_id = $(this).data('cid');
    $.ajax({
      url: base_url+'transactions/Workorder/updateworkorder_status',
      type: 'post',
      data:{wo_id: wo_id, status: 'certified', update: 'certified_by'},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          workOrder_for_approval(sql);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });
  // delete work order
  $(document).on('click', '.btn_del_wo_modal', function(){
    // alert($(this).data('deleteid'));
    // return false;
    var del_id = $(this).data('deleteid');
    var tab = $('.nav-link.active').data('stype');
    // alert(del_id);
    $('#delWorkOrderModal').modal();
    $('#delEmpBtn').click(function(){
      $.ajax({
        url: base_url+'/transactions/Workorder/destroy',
        type: 'post',
        data: { delid: del_id },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $('#delWorkOrderModal').modal('hide');
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            switch (tab) {
              case 'waiting':
                // caTable.search(searchText).draw();
                gen_workOrder_tbl("");
                break;
              case 'approved':
                workOrder_for_approval("");
                break;
              case 'certified':
                workOrder_for_certification("");
                break;
              default:

            }
            gen_workOrder_tbl("");
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    });

  });
  // get work schedule
  $(document).on('change', '#employee_id_no', function(){
    var id = $(this).val();
    var date = $('#wo_date').val();
    if(id != ""){
      $.ajax({
        url: base_url+'transactions/Workorder/get_workschedule',
        type: 'post',
        data:{id, date},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#wo_sTime').val(data.in)
            $('#wo_eTime').val(data.out)
            console.log(data.in);
            console.log(data.out);
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }
  });

  $(document).on('click', '.btn_reject_modal', function(){
    let reject_id = $(this).data('reject_id');
    $('#reject_id').val(reject_id)
    $('#reject_modal').modal();
  });

  $(document).on('click', '#btn_reject_yes', function(){
    let reject_id = $('#reject_id').val();
    let reject_reason = $('#reject_reason').val();
    let tab = $('.nav-link.active').data('stype');
    $.ajax({
      url: base_url+'transactions/Workorder/reject',
      type: 'post',
      data:{reject_id, reject_reason},
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_reject_yes').attr('disabled', true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#btn_reject_yes').prop('disabled', false);
        if(data.success == 1){
          $('#reject_modal').modal('hide');
          notificationSuccess('Success', data.message);
          switch (tab) {
            case 'waiting':
              // caTable.search(searchText).draw();
              gen_workOrder_tbl(sql);
              break;
            case 'approved':
              workOrder_for_approval(sql);
              break;
            case 'certified':
              workOrder_for_certification(sql);
              break;
            default:

          }
          gen_workOrder_tbl("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '.select_all', function(){
    var thiss = $(this);
    var checked = thiss.is(':checked');
    const status = $('.nav-link.active').data('stype')
    // console.log(status, checked);
    if(checked == true){
      $(`.${status}_select`).prop('checked', true).trigger('change');
    }else{
      $(`.${status}_select`).prop('checked', false).trigger('change');
    }

  });

  $(document).on('click', '.btn_batch', function(){
    const batch = [];
    const status = $('.nav-link.active').data('stype');
    const wo_status = (status == 'waiting') ? 'approved' : 'certified';
    const batch_status = (status == 'waiting') ? 'approve' : 'certify'
    $.each($(`.${status}_select:checked`), function(){ batch.push($(this).val())});
    if(batch.length > 0){

      Swal.fire({
        title: 'Are you sure you want to do this batch '+batch_status+'?',
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if(result.value){
          $.ajax({
            url: base_url+'transactions/Workorder/update_batch_status',
            type: 'post',
            data:{status: wo_status, batch: batch, batch_status},
            beforeSend: function(){
              $.LoadingOverlay('show');
            },
            success: function(data){
              $.LoadingOverlay('hide');
              if(data.success == 1){
                notificationSuccess('Success', data.message);
                $('.select_all').prop('checked',false).trigger('change');
                switch (wo_status) {
                  case 'approved':
                    gen_workOrder_tbl(sql);
                    break;
                  case 'certified':
                    workOrder_for_approval(sql);
                    break;
                  default:
                    gen_workOrder_tbl(sql);
                }

              }else{
                notificationError('Error', data.message);
              }
            }
          });
        }
      });

    }else{
      notificationError('Error', "There's nothing to "+batch_status+". Please try again.");
    }
  });

});
