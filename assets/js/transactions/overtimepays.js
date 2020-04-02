$(function(){
	// console.log('overtimePays');
	var base_url = $('body').data('base_url');
	var token = $('#token').val();
	var sql = "";
	var edit_emp_id = $('#employee_id_no').val();
	var edit_date_rendered = $('#date_rendered').val();
	var available_ot = function(){
		var ot_data = null;
		if(edit_emp_id == "" || edit_date_rendered == ""){
			return 0;
		}else{
			$.ajax({
				url: base_url+'transactions/Overtimepays/check_available_ot',
				type: 'post',
				async: false,
				data:{
					employee_id_no: edit_emp_id,
					date_rendered: edit_date_rendered
				},
				beforeSend: function(){
					$.LoadingOverlay('show');
				},
				success: function(data){
					$.LoadingOverlay('hide');
					if(data.success == 1){
						ot_data = data.available_ot;
					}else{
						ot_data =  0;
					}
				}
			});

			return ot_data;
		}
	}();

		function gen_waiting_ot_tbl(search){
	    var workOrder_tbl = $('#caTable').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Overtimepays/getotpays_waiting_json',
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

		gen_waiting_ot_tbl("");

		function gen_approved_ot_tbl(search){
	    var workOrder_tbl = $('#approved_ot_tbl').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Overtimepays/getotpays_approved_json',
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

		function gen_certified_ot_tbl(search){
	    var workOrder_tbl = $('#certified_ot_tbl').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Overtimepays/getotpays_certified_json',
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
		// gen_approved_ot_tbl("");

		// approved ot
		$(document).on('click', '.btn_approved', function(){
			let ot_id = $(this).data('otwaitingid');
			let type = $(this).data('type');
			let status = "approved";
			let update = "approved_by";
			console.log(sql);

			$.ajax({
			  url: base_url+'transactions/Overtimepays/updateotstatus',
			  type: 'post',
			  data:{ot_id, status, update, type},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
						notificationSuccess('Success', data.message);
						gen_waiting_ot_tbl(sql);
			    }else{
						notificationError('Error', data.message);
			    }
			  }
			});
		});
		// certify
		$(document).on('click', '.btn_certify', function(){
			let ot_id = $(this).data('certifyid');
			let type = $(this).data('type');
			let status = "certified";
			let update = "certified_by";
			$.ajax({
			  url: base_url+'transactions/Overtimepays/updateotstatus',
			  type: 'post',
			  data:{ot_id, status, update, type},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
			      notificationSuccess('Success', data.message);
						gen_approved_ot_tbl(sql);
			    }else{
			      notificationError('Error', data.message);
			    }
			  }
			});
		});
		// waiting tab
	  $(document).on('click', '#waiting_ot_tab', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_approve').show();
			sql = "";
	    gen_waiting_ot_tbl(sql);
	    $('#ot_waiting').tab('show');
	  });
		// approved tab
		$(document).on('click', '#approved_ot_tab', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_certify').show();
			sql = "";
	    gen_approved_ot_tbl(sql);
	    $('#ot_approved').tab('show');
	  });
		// certified tab
		$(document).on('click', '#certified_ot_tab', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
			sql = "";
	    gen_certified_ot_tbl(sql);
	    $('#ot_certified').tab('show');
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
				case "by_minutes":
					$('.filter_div').hide("slow");
					$('#divMin').show("slow");
					$('#divMin').addClass('active');
					break;
				default:

			}

		});
		// user access dept
		$(document).on('change', '#dept', function(){
			var deptId = $(this).val();
			$.ajax({
			  url: base_url+'transactions/Overtimepays/get_employee_by_dept',
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
	        sql = " AND DATE(a.date_created) BETWEEN '"+start+"' AND '"+end+"'";
	        // sql += ex_sql;
	        // contract_history_tbl(sql);
	        break;
	      case 'divMin':
	        var sal_from = $('#min_from').val() || 0;
	        var sal_to = $('#min_to').val() || 0;
	        sql = " AND a.minutes_of_overtime BETWEEN "+sal_from+" AND "+sal_to;
	        // sql += ex_sql;
	        // contract_history_tbl(sql);
	      default:

	    }

			switch (tab) {
				case 'waiting':
					gen_waiting_ot_tbl(sql);
					break;
				case 'approved':
					gen_approved_ot_tbl(sql);
					break;
				case 'certified':
					gen_certified_ot_tbl(sql)
					break;
				default:

			}
 			// caTable.search(searchText).draw();
 		});

 		$("#addOPBtn").click(function(){
 			// alert('success!');
			$(this).prop('disabled', true);
 			var employee_id_no	= $("#employee_id_no").val();
 			var purpose = $("#purpose").val();
 			var minutes_of_overtime = $("#minutes_of_overtime").val();
			let type = $('#type').val();
			// console.log(minutes_of_overtime);
			// return;
			var date_rendered = $('#date_rendered').val();
 			var data = {
 				employee_id_no:employee_id_no,
				purpose:purpose,
				minutes_of_overtime:minutes_of_overtime,
				date_rendered,
				type
 			};

			if(minutes_of_overtime > available_ot){
				notificationError('Error', 'Oops! You can\'t file more than you rendered. Please try again.')
				$(this).prop('disabled', false);
				return;
			}

			$.ajax({
				url: base_url+'transactions/Overtimepays/add_otpays',
				type:'POST',
				data:data,
				beforeSend: function(){
					$.LoadingOverlay('show');
				},
				success:function(data) {
					$.LoadingOverlay('hide');
					if(data.success == 1){
						$('#addOPBtn').prop('disabled',true);
						notificationSuccess("Success", data.message);
						setTimeout(function(){window.location.href = base_url+'/transactions/Overtimepays/index/'+token+'/'},1000);
					}else{
						notificationError("Error", data.message);
						$('#amount').val("");
						$('#addOPBtn').prop('disabled',false);
						$("#minutes_of_overtime").val(0);
					}

				}
			});

 		});

 		$("#editOPBtn").click(function(){
			$(this).prop('disabled', true);
 			var caID = $("#caID").val();
 			var employee_id_no	= $("#employee_id_no").val();
 			var purpose = $("#purpose").val();
 			var minutes_of_overtime = $('#minutes_of_overtime').val();
			var date_rendered = $('#date_rendered').val();
			let type = $('#type').val();

 			var data = {
 				caID:caID,
				employee_id_no:employee_id_no,
				purpose:purpose,
				minutes_of_overtime:minutes_of_overtime,
				date_rendered,
				type
 			};

			// console.log(available_ot);

			if(minutes_of_overtime > available_ot){
				notificationError('Error', 'Oops! You can\'t file more than you rendered. Please try again.')
				$(this).prop('disabled', false);
				return;
			}

			// return;
 			// console.log(data)
 			$.ajax({
			url: base_url+'transactions/Overtimepays/update_pays',
			type:'POST',
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data) {
				$.LoadingOverlay('hide');
				if(data.success == 1) {
					$("#editOPBtn").prop('disabled',true);
					notificationSuccess('success',data.message);
					setTimeout(function(){window.location.href = base_url+'/transactions/Overtimepays/index/'+token+'/'},1000);
					//
				}else{
					notificationError('Error', data.message);
					$("#editOPBtn").prop('disabled',false);
					$('#minutes_of_overtime').val(0);
				}
			}
		});

 		});

		$(document).on('click', '.btn_del_ot', function(){
			var del_id = $(this).data('id');
			// alert(del_id);
			var tab = $('.nav-link.active').data('stype');
			$('#delCAModal').modal();
			$('.delCABtn').click(function(){
				$.ajax({
				  url: base_url+'transactions/Overtimepays/destroy',
				  type: 'post',
				  data:{id: del_id},
				  beforeSend: function(){
				    $.LoadingOverlay('show');
				  },
				  success: function(data){
						$('#delCAModal').modal('hide');
				    $.LoadingOverlay('hide');
				    if(data.success == 1){
							notificationSuccess('Success', data.message);

							switch (tab) {
								case 'waiting':
									gen_waiting_ot_tbl("");
									break;
								case 'approved':
									gen_approved_ot_tbl("");
									break;
								case 'certified':
									gen_certified_ot_tbl("")
									break;
								default:

							}
				    }else{
							notificationError('Error',data.message);
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
	      url: base_url+'transactions/Overtimepays/reject',
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
								gen_waiting_ot_tbl(sql);
								break;
							case 'approved':
								gen_approved_ot_tbl(sql);
								break;
							case 'certified':
								gen_certified_ot_tbl(sql)
								break;
							default:

						}
	        }else{
	          notificationError('Error', data.message);
	        }
	      }
	    });
	  });

		$(document).on('change', '#date_rendered', function(){
			let employee_id_no = $('#employee_id_no').val();
			let date_rendered = $(this).val();
			if(employee_id_no != "" && date_rendered != ""){
				$.ajax({
				  url: base_url+'transactions/Overtimepays/check_available_ot',
				  type: 'post',
				  data:{employee_id_no, date_rendered},
				  beforeSend: function(){
				    $.LoadingOverlay('show');
				  },
				  success: function(data){
				    $.LoadingOverlay('hide');
				    if(data.success == 1){
							notificationSuccess('Success', data.message);
							$('#minutes_of_overtime').val(data.available_ot);
							available_ot = data.available_ot;
				    }else{
							notificationError('Error', data.message);
							$('#minutes_of_overtime').val(0);
				    }
				  }
				});
			}
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
	            url: base_url+'transactions/Overtimepays/update_batch_status',
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
	                    gen_waiting_ot_tbl(sql);
	                    break;
	                  case 'certified':
	                    gen_approved_ot_tbl(sql);
	                    break;
	                  default:
	                    gen_waiting_ot_tbl(sql);
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
