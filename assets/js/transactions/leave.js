$(function(){
	var base_url = $('body').data('base_url');
	var token = $('#token').val();
	var sql = "";

		function gen_waiting_leave_tbl(search){
	    var workOrder_tbl = $('#caTable').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7,8,9], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Leave/getleavepays_waiting_json',
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

		function gen_approved_leave_tbl(search){
	    var workOrder_tbl = $('#leave_approved_tbl').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7,8,9], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Leave/getleavepays_approved_json',
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

		function gen_certified_leave_tbl(search){
	    var workOrder_tbl = $('#leave_certified_tbl').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7,8], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Leave/getleavepays_certified_json',
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

		gen_waiting_leave_tbl("");

		// approved salary deduction
		$(document).on('click', '.btn_approved', function(){
			var leave_id = $(this).data('approvedid');
			var user_id = $(this).data('emp_id');
			var leave_cat = $(this).data('leave_cat');
			var num_days = $(this).data('num_days');
			var status = "approved";
			var update = "approved_by";

			$.ajax({
			  url: base_url+'transactions/Leave/updateleavestatus',
			  type: 'post',
			  data:{leave_id, status, update, user_id,leave_cat,num_days},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
						notificationSuccess('Success', data.message);
						gen_waiting_leave_tbl(sql);
			    }else{
						notificationError('Error', data.message);
			    }
			  }
			});
		});
		// certify salary deduction
		$(document).on('click', '.btn_certify', function(){
			var leave_id = $(this).data('certifyid');
			var user_id = $(this).data('emp_id');
			var leave_cat = $(this).data('leave_cat');
			var num_days = $(this).data('num_days');
			var status = "certified";
			var update = "certified_by";

			$.ajax({
			  url: base_url+'transactions/Leave/updateleavestatus',
			  type: 'post',
			  data:{leave_id, status, update, user_id,leave_cat,num_days},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
						notificationSuccess('Success', data.message);
						gen_approved_leave_tbl(sql);
			    }else{
						notificationError('Error', data.message);
			    }
			  }
			});
		});
		// waiting tab
		$(document).on('click', '#leave_waiting_nav', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_approve').show();
			sql = "";
	    gen_waiting_leave_tbl(sql);
	    $('#leave_approved_tbl').tab('show');
	  });
		// approved tab
		$(document).on('click', '#leave_approved_nav', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_certify').show();
			sql = "";
	    gen_approved_leave_tbl(sql);
	    $('#leave_approved_tbl').tab('show');
	  });
		// certified tab
		$(document).on('click', '#leave_certified_nav', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
			sql = "";
	    gen_certified_leave_tbl(sql);
	    $('#leave_certified_tab').tab('show');
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
					$('#divDateFiled').show("slow");
					$('#divDateFiled').addClass('active');
					break;
				case "by_amount":
					$('.filter_div').hide("slow");
					$('#divDays').show("slow");
					$('#divDays').addClass('active');
					break;
				case "by_leave":
					$('.filter_div').hide("slow");
					$('#divLeaveType').show("slow");
					$('#divLeaveType').addClass('active');
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
							$('#employee_id_no').append('<option value = "'+val['employee_idno']+'">'+val['fullname']+'('+val['employee_idno']+')</option>');
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
	        // sql = " AND a.date_from >= '"+start+"' AND a.date_to <= '"+end+"'";
	        sql = " AND a.date_from <= '"+end+"' AND a.date_to >= '"+start+"'";
	        break;
	      case 'divDateFiled':
	        var start = $('#date_from2').val();
	        var end = $('#date_to2').val();
	        // sql = " AND a.date_from >= '"+start+"' AND a.date_to <= '"+end+"'";
	        sql = " AND DATE(a.date_created) BETWEEN '"+start+"' AND '"+end+"'";
	        break;
	      case 'divDays':
	        var sal_from = $('#num_days_from').val() || 0;
	        var sal_to = $('#num_days_to').val() || 0;
	        sql = " AND a.number_of_days BETWEEN "+sal_from+" AND "+sal_to;
					break;
	      case 'divLeaveType':
	        var searchValue = $('.filter_div.active').children('.searchArea').val();
	        sql = " AND e.leaveid = "+searchValue;
					break;
	      default:

	    }
			switch (tab) {
				case 'waiting':
					// caTable.search(searchText).draw();
					gen_waiting_leave_tbl(sql);
					break;
				case 'approved':
					gen_approved_leave_tbl(sql);
					break;
				case 'certified':
					gen_certified_leave_tbl(sql)
					break;
				default:

			}
 			// caTable.search(searchText).draw();
 		});

 		$("#addLEBtn").click(function(){
			$(this).prop('disabled', true);
			var thiss = $(this);
 			var employee_id_no	= $("#employee_id_no").val();
 			var leave_type = $('#leave_type').val();
 			var date_from = $('#date_from').val();
 			var date_to = $('#date_to').val();
 			var contact_number = $("#contact_number").val();
 			var comment = $("#comment").val();
			var remaining_leave = $('#remaining_leave').val();
			var paid = $('#paid').val();

 			var data = {
				employee_id_no:employee_id_no,
				leave_type:leave_type,
				date_from:date_from,
				date_to:date_to,
				contact_number:contact_number,
				comment:comment,
				remaining_leave,
				paid
 			};

 			$.ajax({
			url: base_url+'transactions/Leave/add_leave',
			type:'POST',
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data) {
				$.LoadingOverlay('hide');
				if(data.success == 1){
					$("#addLEBtn").prop('disabled',true);
					notificationSuccess("Success", data.message);
					setTimeout(function(){
						window.location.href = base_url+'/transactions/Leave/index/'+token+'/';
					},1000);
				}else{
					notificationError("Error", data.message);
					$("#addLEBtn").prop('disabled',false);
				}

			}
		});

 		});

 		$("#editLEBtn").click(function(){
			var thiss = $(this);
			thiss.prop('disabled', true);
 			var caID = $("#caID").val();
 			var employee_id_no	= $("#employee_id_no").val();
 			var leave_type = $('#leave_type').val();
 			var date_from = $('#date_from').val();
 			var date_to = $('#date_to').val();
 			var contact_number = $("#contact_number").val();
 			var comment = $("#comment").val();
			var remaining_leave = $('#remaining_leave').val();
			var paid = $('#paid').val();

			// console.log(paid);
			// return;


 			var data = {
 				caID:caID,
				employee_id_no:employee_id_no,
				leave_type:leave_type,
				date_from:date_from,
				date_to:date_to,
				contact_number:contact_number,
				comment:comment,
				remaining_leave,
				paid
 			};

 			$.ajax({
				url: base_url+'transactions/Leave/update_leave',
				type:'POST',
				data:data,
				beforeSend: function(){
					$.LoadingOverlay('show');
				},
				success:function(data) {
					$.LoadingOverlay('hide');
					if(data.success == 1) {
						thiss.prop('disabled',true);
						notificationSuccess('success',data.message);
						setTimeout(function(){
							window.location.href = base_url+'transactions/Leave/index/'+token+'/';
						},1000);

					}else{
						thiss.prop('disabled', false);
						notificationError('error', data.message);

					}
				}
			});

 		});

		$('.delLEBtn').click(function(){
			var caID = $('#delLEid').val();
			var data = {
				id:caID
			};

			$.ajax({
				url: base_url+'transactions/Leave/destroy',
				type:'POST',
				data:data,
				success:function(data) {
					console.log(data)
					var result = JSON.parse(data);
					console.log(result)
					// $.LoadingOverlay('show');
					$('#delCAModal').modal('toggle');
					notificationSuccess('Success',result.message);
					caTable.ajax.reload(null,false);
					// $.LoadingOverlay('hide');

				}
			});

		});

		$(document).on('click', '.btn_del_leave', function(){
			var thiss = $(thiss)
			thiss.prop('disabled', true);
			var del_id = $(this).data('id');
			var tab = $('.nav-link.active').data('stype');
			$('#delCAModal').modal();

			$('#delLEBtn').click(function(){
				$.ajax({
					url: base_url+'transactions/Leave/destroy',
					type: 'post',
					data:{id:del_id},
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
									gen_waiting_leave_tbl("");
									break;
								case 'approved':
									gen_approved_leave_tbl("");
									break;
								case 'certified':
									gen_certified_leave_tbl("");
									break;
								default:

							}
						}else{
							thiss.prop('disabled', false);
							notificationError('Error', data.message);
						}
					}
				});
			});
		});

		$(document).on('change', '#employee_id_no', function(){
			var emp_idno = $(this).val();
			$('#leave_type option[value=""]').prop('selected',true);
			$('#remaining_leave').val(0);
			if(emp_idno != ""){
				$('#leave_type').removeAttr('disabled');
			}else{
				$('#leave_type').attr('disabled', true);
			}
		});

		$(document).on('change', '#leave_type', function(){
			var leave_type = $(this).val();
			var emp_idno = $('#employee_id_no').val();

			if(leave_type != ""){
				$.ajax({
				  url: base_url+'transactions/Leave/get_remaining_leave_type',
				  type: 'post',
				  data:{
						leave_type,
						emp_idno
					},
				  beforeSend: function(){
				    $.LoadingOverlay('show');
				  },
				  success: function(data){
				    $.LoadingOverlay('hide');
				    if(data.success == 1){
							// notificationSuccess('Success', data.message);
							var remaining_leave = data.remaining_leave;
							var filtered = filterArray(parseInt(leave_type),remaining_leave);
	            var remaining_leave = (filtered != undefined) ? filtered.days : 0;
	            $('#remaining_leave').val(remaining_leave);
							if(data.late_filling == 'yes'){
	              // $('.leave_date').datepicker('remove');
	              // $('.leave_date').datepicker(
	              //   {format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
	              // ).datepicker("setDate", new Date());
	            }
				    }else{
							notificationError('Error', data.message);
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
	      url: base_url+'transactions/Leave/reject',
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
								gen_waiting_leave_tbl(sql);
								break;
							case 'approved':
								gen_approved_leave_tbl(sql);
								break;
							case 'certified':
								gen_certified_leave_tbl(sql);
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
	            url: base_url+'transactions/Leave/update_batch_status',
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
	                    gen_waiting_leave_tbl(sql);
	                    break;
	                  case 'certified':
	                    gen_approved_leave_tbl(sql);
	                    break;
	                  default:
	                    gen_waiting_leave_tbl(sql);
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
