$(function(){
	// console.log('additionalPays');
	var base_url = $('body').data('base_url');
	var token = $('#token').val();
	var sql = "";

	function gen_ap_waiting_tbl_tbl(search){
    var workOrder_tbl = $('#caTable').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Additionalpays/getadditionalpays_waiting_json',
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

	function gen_ap_approved_tbl_tbl(search){
    var workOrder_tbl = $('#ap_approved_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5,6], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Additionalpays/getadditionalpays_approved_json',
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

	function gen_ap_certified_tbl_tbl(search){
    var workOrder_tbl = $('#ap_certified_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'transactions/Additionalpays/getadditionalpays_certified_json',
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

	gen_ap_waiting_tbl_tbl("");

		// approved
		$(document).on('click', '.btn_approved', function(){
			var ap_id = $(this).data('approvedid');
			var status = 'approved';
			var update = 'approved_by';
			$.ajax({
			  url: base_url+'transactions/Additionalpays/updateadditionalpays_status',
			  type: 'post',
			  data:{ ap_id, status, update },
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
						notificationSuccess('Success', data.message);
						gen_ap_waiting_tbl_tbl(sql);
			    }else{
						notificationError('Error', data.message);
			    }
			  }
			});
		});
		// certify
		$(document).on('click', '.btn_certify', function(){
			var ap_id = $(this).data('certifyid');
			var status = "certified";
			var update = "certified_by";
			$.ajax({
			  url: base_url+'transactions/Additionalpays/updateadditionalpays_status',
			  type: 'post',
			  data:{ap_id, status, update},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
			      notificationSuccess('Success', data.message);
						gen_ap_approved_tbl_tbl(sql);
			    }else{
			      notificationError('Error', data.message);
			    }
			  }
			});
		});
		// waiting tab
		$(document).on('click', '#waiting_ap', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_approve').show();
			sql = "";
	    gen_ap_waiting_tbl_tbl(sql);
	    $('#ap_waiting_for_approval').tab('show');
	  });
		// approved tab
		$(document).on('click', '#approved_ap', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_certify').show();
			sql = "";
	    gen_ap_approved_tbl_tbl(sql);
	    $('#ap_approved').tab('show');
	  });
		// certified tab
		$(document).on('click', '#certified_ap', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
			sql = "";
	    gen_ap_certified_tbl_tbl(sql);
	    $('#ap_certified').tab('show');
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
				case "by_amount":
					$('.filter_div').hide("slow");
					$('#divAmount').show("slow");
					$('#divAmount').addClass('active');
					break;
				default:

			}

		});
		// user access dept
		$(document).on('change', '#dept', function(){
			var deptId = $(this).val();
			$.ajax({
			  url: base_url+'Main/get_employee_by_dept',
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
					sql = " AND d.deptId = "+searchValue;
					break;
	      case 'divDate':
	        var start = $('#date_from').val();
	        var end = $('#date_to').val();
	        sql = " AND a.date_issued BETWEEN '"+start+"' AND '"+end+"'";
	        // sql += ex_sql;
	        // contract_history_tbl(sql);
	        break;
	      case 'divAmount':
	        var sal_from = $('#amount_from').val() || 0;
	        var sal_to = $('#amount_to').val() || 0;
	        sql = " AND a.amount BETWEEN "+sal_from+" AND "+sal_to;
	        // sql += ex_sql;
	        // contract_history_tbl(sql);
	      default:

	    }
			switch (tab) {
				case 'waiting':
					// caTable.search(searchText).draw();
					gen_ap_waiting_tbl_tbl(sql);
					break;
				case 'approved':
					gen_ap_approved_tbl_tbl(sql);
					break;
				case 'certified':
					gen_ap_certified_tbl_tbl(sql)
					break;
				default:

			}
 			// caTable.search(searchText).draw();
 		});

 		$("#addAPBtn").click(function(){
			$(this).prop('disabled', true);
 			// alert('success!');
 			var employee_id_no	= $("#employee_id_no").val();
 			var date_issued = $("#date_issued").val();
 			var purpose = $("#purpose").val();
 			var amount = $("#amount").data('raw');
			// console.log(amount);
			// return;
 			var data = {
 				employee_id_no:employee_id_no,
				date_issued:date_issued,
				purpose:purpose,
				amount:amount
 			};
 			$.ajax({
			url: base_url+'transactions/Additionalpays/add_pays',
			type:'POST',
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data) {
				// var result = JSON.parse(data);
				$.LoadingOverlay('hide');
				if(data.success == 1){
					$("#addAPBtn").prop('disabled',true);
					notificationSuccess("Success", data.message);
					setTimeout(function(){window.location.href = base_url+'/transactions/Additionalpays/index/'+token+'/'},1000);
				}else{
					notificationError("error", data.message);
					$("#addAPBtn").prop('disabled',false);
					$('#date_issued').val("");
					$('#amount').val("");
					$("#purpose").val("");
				}

			}
		});

 		});

 		$("#editAPBtn").click(function(){
			$(this).prop('disabled', true);
 			var caID = $("#caID").val();
 			var employee_id_no	= $("#employee_id_no").val();
 			var date_issued = $("#date_issued").val();
 			var purpose = $("#purpose").val();
 			var amount = $("#amount").data('raw');
			// console.log(amount);
			// return;
 			var data = {
 				caID:caID,
				employee_id_no:employee_id_no,
				date_issued:date_issued,
				purpose:purpose,
				amount:amount
 			};
 			// console.log(data)
 			$.ajax({
			url: base_url+'transactions/Additionalpays/update_pays',
			type:'POST',
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data) {
				$.LoadingOverlay('hide');
				var result = JSON.parse(data);
				// console.log(result);
				if(result.success == 1) {
					$("#editAPBtn").prop('disabled',true);
					notificationSuccess('success',result.message);
					setTimeout(function(){window.location.href = base_url+'/transactions/Additionalpays/index/'+token+'/'},1000);
					//
				}else{
					$("#editAPBtn").prop('disabled',false);
					notificationError('error', result.message);
				}
			}
		});

 		});

		$(document).on('click', '.btn_del_ap', function(){
			var del_id = $(this).data('id');
			var tab = $('.nav-link.active').data('stype');
			$('#delCAModal').modal();
			$('#delAPBtn').click(function(){
				$.ajax({
				  url: base_url+'transactions/Additionalpays/destroy',
				  type: 'post',
				  data:{del_id},
				  beforeSend: function(){
				    $.LoadingOverlay('show');
				  },
				  success: function(data){
						$('#delCAModal').modal('hide');
				    $.LoadingOverlay('hide');
				    if(data.success == 1){
							notificationSuccess('Success',data.message);
							switch (tab) {
								case 'waiting':
									// caTable.search(searchText).draw();
									gen_ap_waiting_tbl_tbl("");
									break;
								case 'approved':
									gen_ap_approved_tbl_tbl("");
									break;
								case 'certified':
									gen_ap_certified_tbl_tbl("");
									break;
								default:

							}
				    }else{
							notificationError('Error', data.message);
				    }
				  }
				});
			});
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
	      url: base_url+'transactions/Additionalpays/reject',
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
								gen_ap_waiting_tbl_tbl("");
								break;
							case 'approved':
								gen_ap_approved_tbl_tbl("");
								break;
							case 'certified':
								gen_ap_certified_tbl_tbl("");
								break;
							default:

						}
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
	            url: base_url+'transactions/Additionalpays/update_batch_status',
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
	                    gen_ap_waiting_tbl_tbl(sql);
	                    break;
	                  case 'certified':
	                    gen_ap_approved_tbl_tbl(sql);
	                    break;
	                  default:
	                    gen_ap_waiting_tbl_tbl(sql);
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
