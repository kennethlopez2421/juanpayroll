$(function(){
	console.log("cashAdvance");
	var base_url = $('body').data('base_url');
	var token = $('#token').val();
	var sql = "";
	  $('.date_input').datepicker({format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}).datepicker("setDate", new Date());

		function gen_waiting_ca_tbl(search){
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
	        url: base_url+'transactions/Cashadvance/getcapays_waiting_json',
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

		function gen_approved_ca_tbl(search){
	    var workOrder_tbl = $('#ca_approve_tbl').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Cashadvance/getcapays_approved_json',
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

		function gen_certified_ca_tbl(search){
	    var workOrder_tbl = $('#ca_certified_tbl').DataTable( {
	      "processing": true,
	      "serverSide": true,
	      "searching": false,
	      "destroy": true,
	      "autoWidth": false,
	      "columnDefs":[
	        {targets: [0,1,2,3,4,5,6,7], orderable: false}
	      ],
	      "ajax":{
	        url: base_url+'transactions/Cashadvance/getcapays_certified_json',
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

		gen_waiting_ca_tbl("");

		// approved cash advance
		$(document).on('click', '.btn_approved', function(){
			var ca_id = $(this).data('caid');
			var status = "approved";
			var update = "approved_by"

			$.ajax({
			  url: base_url+'transactions/Cashadvance/updatecastatus',
			  type: 'post',
			  data:{ca_id, status, update},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
						notificationSuccess('Success', data.message);
						gen_waiting_ca_tbl(sql);
			    }else{
						notificationError('Error', data.message);
			    }
			  }
			});
		});
		// certify cash advance
		$(document).on('click', '.btn_certify', function(){
			var ca_id = $(this).data('certifyid');
			var status = "certified";
			var update = "certified_by";
			$.ajax({
			  url: base_url+'transactions/Cashadvance/updatecastatus',
			  type: 'post',
			  data:{ca_id, status, update},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
			      notificationSuccess('Success', data.message);
						gen_approved_ca_tbl(sql);
			    }else{
			      notificationError('Error', data.message);
			    }
			  }
			});
		});
		// watinng tab
		$(document).on('click', '#ca_waiting_nav', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_approve').show();
			sql = ""
	    gen_waiting_ca_tbl(sql);
	    $('#ca_waiting_tab').tab('show');
	  });
		// approved tab
		$(document).on('click', '#ca_approved_nav', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
	    $('.btn_batch_certify').show();
			sql = ""
	    gen_approved_ca_tbl(sql);
	    $('#ca_approved_tab').tab('show');
	  });
		// certified tab
		$(document).on('click', '#ca_certified_nav', function(){
	    $('.nav-link').removeClass('active');
	    $(this).addClass('active');
			$('.btn_batch').hide();
			sql = ""
	    gen_certified_ca_tbl(sql);
	    $('#ca_certified_tab').tab('show');
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
				case "by_date2":
					$('.filter_div').hide("slow");
					$('#divDate2').show("slow");
					$('#divDate2').addClass('active');
					break;
				case "by_amount":
					$('.filter_div').hide("slow");
					$('#divAmount').show("slow");
					$('#divAmount').addClass('active');
					break;
				case "by_fee":
					$('.filter_div').hide("slow");
					$('#divFee').show("slow");
					$('#divFee').addClass('active');
					break;
				case "by_term":
					$('.filter_div').hide("slow");
					$('#divTerm').show("slow");
					$('#divTerm').addClass('active');
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
			     	$('#ca_emp_id').html('<option value = "">------</option>');
						$.each(data.emp, function(i, val){
							$('#ca_emp_id').append('<option value = "'+val['employee_idno']+'">'+val['fullname']+' ('+val['employee_idno']+')</option>');
						});
			    }else{
			      notificationError('Error', data.message);
						$('#ca_emp_id').html('<option value = "">------</option>');
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
	        var start = $('#dof_from').val();
	        var end = $('#dof_to').val();
	        sql = " AND a.date_of_file BETWEEN '"+start+"' AND '"+end+"'";
	        // sql += ex_sql;
	        // contract_history_tbl(sql);
	        break;
				case 'divDate2':
	        var start = $('#doe_from').val();
	        var end = $('#doe_to').val();
	        sql = " AND a.date_of_effectivity BETWEEN '"+start+"' AND '"+end+"'";
	        // sql += ex_sql;
	        // contract_history_tbl(sql);
	        break;
	      case 'divAmount':
	        var sal_from = $('#amount_from').val() || 0;
	        var sal_to = $('#amount_to').val() || 0;
	        sql = " AND a.amount BETWEEN "+sal_from+" AND "+sal_to;
					break;
				case 'divFee':
	        var sal_from = $('#fee_from').val() || 0;
	        var sal_to = $('#fee_to').val() || 0;
	        sql = " AND a.rate BETWEEN "+sal_from+" AND "+sal_to;
					break;
				case 'divTerm':
	        var sal_from = $('#term_from').val() || 0;
	        var sal_to = $('#term_to').val() || 0;
	        sql = " AND a.terms BETWEEN "+sal_from+" AND "+sal_to;
					break;
	      default:

	    }
			switch (tab) {
				case 'waiting':
					gen_waiting_ca_tbl(sql);
					break;
				case 'approved':
					gen_approved_ca_tbl(sql);
					break;
				case 'certified':
					gen_certified_ca_tbl(sql)
					break;
				default:

			}
 			// caTable.search(searchText).draw();
 		});

 		$("#editCABtn").click(function(){
 			var caID = $("#caID").val();
 			var employee_id_no	= $("#ca_emp_id").val();
 			var date_of_file = $("#ca_dof").val();
 			var date_of_effectivity	= $("#ca_doe").val();
 			var amount = $("#ca_total").val();
			var reason = $("#ca_reason").val();
 			var terms = $("#ca_num_days").val();
 			var rate =$("#ca_monthly_rate").val();
 			var data = {
 				caID:caID,
				employee_id_no:employee_id_no,
				date_of_file:date_of_file,
				date_of_effectivity:date_of_effectivity,
				amount:amount,
				reason:reason,
				terms:terms,
				rate:rate
 			};
 			// console.log(data);
 			$.ajax({
				url: base_url+'transactions/Cashadvance/update_cashadvance',
				type:'POST',
				data:data,
				success:function(data) {
					// var result = JSON.parse(data);
					// console.log(result);
					if(data.success == 1) {
						$("#editCABtn").prop('attr',true);
						notificationSuccess('success',data.message);
						window.location.href = base_url+'/transactions/Cashadvance/index/'+token+'/';
						//
					}else{
						$("#editCABtn").prop('attr',false);
						notificationError('error', data.message);
					}
				}
			});

 		});

		$(document).on('change', '#ca_emp_id', function(){
			if($(this).val() != ""){
				$.ajax({
				  url: base_url+'transactions/Cashadvance/get_emp_contract_info',
				  type: 'post',
				  data:{emp_id: $(this).val()},
				  beforeSend: function(){
				    $.LoadingOverlay('show');
				  },
				  success: function(data){
				    $.LoadingOverlay('hide');
				    if(data.success == 1){
							// $('.p_scheme').removeAttr('readonly');
							$('#ca_max_loan').val(data.max_loan);
							$('#ca_max_loan').data('max', data.max_per);
							$('#ca_max_loan').attr('max', data.max_loan);

							$('#ca_num_days').val(data.term);
							$('#ca_num_days').data('term', data.term_per);

							$('#ca_monthly_rate').val(data.monthly_rate);
							$('#ca_monthly_rate').data('rate', data.rate_per);

							$('#ca_total').val(data.total);
				    }else{
							notificationError('Error',data.message);
				    }
				  }
				});
			}
		});

		$(document).on('change', '#ca_max_loan', function(){
			var max = $(this).data('max');
			var term = $('#ca_num_days').data('term');
			var rate = $('#ca_monthly_rate').data('rate');

			var max_loan = $(this).val();
			var term_of_payment = $('#ca_monthly_rate').val();
			var monthly_rate = parseFloat(max_loan) * ((parseFloat(rate) / 100 ) * term);
			var total = max_loan - monthly_rate;


			$('#ca_monthly_rate').val(monthly_rate);
			$('#ca_total').val(total);
		});

		$(document).on('change', '#ca_num_days', function(){
			var max = $('#ca_max_loan').data('max');
			var term = $('#ca_num_days').val();
			var rate = $('#ca_monthly_rate').data('rate');

			var max_loan = $('#ca_max_loan').val();
			var monthly_rate = parseFloat(max_loan) * ((parseFloat(rate) / 100 ) * term);
			var total = max_loan - monthly_rate;

			$('#ca_monthly_rate').val(monthly_rate);
			$('#ca_total').val(total);
		});

		$(document).on('click', '#addCABtn', function(){
			$(this).prop('disabled', true);
			// alert($('#ca_total').val());
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
					errorMsg = "Please fill up all required fields";
					return false;
				}
			});

			if(error == 0){
				$.ajax({
				  url: base_url+'transactions/Cashadvance/create',
				  type: 'post',
				  data:{
						emp_id: $('#ca_emp_id').val(),
						ca_dof: $('#ca_dof').val(),
						ca_doe: $('#ca_doe').val(),
						ca_reason: $('#ca_reason').val(),
						ca_max_loan: $('#ca_max_loan').val(),
						ca_num_days: $('#ca_num_days').val(),
						ca_monthly_rate: $('#ca_monthly_rate').val(),
						ca_total: $('#ca_total').val()
					},
				  beforeSend: function(){
				    $.LoadingOverlay('show');
				  },
				  success: function(data){
				    $.LoadingOverlay('hide');
				    if(data.success == 1){
							notificationSuccess('Success', data.message);
							$("#addCABtn").prop('disabled',true);
							setTimeout(function(){
								window.location.href = base_url+'transactions/Cashadvance/index/'+token;
							},1500);
				    }else{
							notificationError('Error', data.message);
							$("#addCABtn").prop('disabled',false);

				    }
				  }
				});
			}else{
				notificationError('Error', errorMsg);
			}
		});

		$(document).on('click', '.btn_del_ca', function(){
			var del_id = $(this).data('id');
			var tab = $('.nav-link.active').data('stype');
			$('#delCAModal').modal();
			$('#delCABtn').click(function(){
				$.ajax({
				  url: base_url+'transactions/Cashadvance/destroy',
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
									gen_waiting_ca_tbl("");
									break;
								case 'approved':
									gen_approved_ca_tbl("");
									break;
								case 'certified':
									gen_certified_ca_tbl("");
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
	      url: base_url+'transactions/Cashadvance/reject',
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
								gen_waiting_ca_tbl("");
								break;
							case 'approved':
								gen_approved_ca_tbl("");
								break;
							case 'certified':
								gen_certified_ca_tbl("");
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
	            url: base_url+'transactions/Cashadvance/update_batch_status',
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
	                    gen_waiting_ca_tbl(sql);
	                    break;
	                  case 'certified':
	                    gen_approved_ca_tbl(sql);
	                    break;
	                  default:
	                    gen_waiting_ca_tbl(sql);
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
