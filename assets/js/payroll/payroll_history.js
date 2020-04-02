$(function(){
	var base_url = $('body').data('base_url');
	var token = $('#token').val();
	var payroll_history_tbl = $("#payroll_history_tbl").DataTable({
 		processing:false,
		serverSide:true,
		ordering:false,
		ajax:{
				url: base_url+'payroll/Payroll_history/get_payroll',
				beforeSend:function(){
					$.LoadingOverlay('show');
				},
				complete:function(){
					$.LoadingOverlay('hide');
				}
			},
		columns:[
			{data:function(data,type,dataToSet){
				var dc = new Date(data.created_at);
				date_created = moment(dc).format("YYYY-MM-DD");
				return date_created;
			}},
			{data:"paytype_desc"},
			{data:"company"},
			{data:function(data,type,dataToSet){
				var date_range = moment(data.fromdate).format("YYYY-MM-DD")+ " <b>-</b> "+moment(data.todate).format("YYYY-MM-DD");
				return date_range;
			}},
			{data: function(data){
				return (data.status == 'pending')
				? '<h5 class="text-warning text-center">Pending</h5>'
				: '<h5 class="text-success text-center">Approved</h5>';
			}}
		],
		columnDefs:[
			{
				"targets": 5,
				"data":null,
				render:function(data,type,row,meta){
					var date_range = moment(data.fromdate).format("YYYY-MM-DD")+ " <b>-</b> "+moment(data.todate).format("YYYY-MM-DD");

					$(document).on("click","#approve_btn"+data.pay_id,function(e){
						e.stopImmediatePropagation();
						var payroll_id = $(this).data('id');
						var manhours_id = $(this).data('manhours');
						var additionals_id = $(this).data('additionals');
						var deductions_id = $(this).data('deductions');
						var payroll_refno = $(this).data('payroll_refno');
						$("#pay_id").val(payroll_id);
						$("#manhours_id").val(manhours_id);
						$("#additionals_id").val(additionals_id);
						$("#deductions_id").val(deductions_id);
						$("#payroll_refno").val(payroll_refno);
					});

					var viewbutton = "";
					viewbutton += "<form class = 'd-inline' method='post' action='"+base_url+"payroll/Payroll_history/open_payroll_summary/"+token+"' >";
					viewbutton += "<input type='hidden' name='get_id' value='"+data.pay_id+"'>";
					viewbutton += "<input type='hidden' name='manhours' value='"+data.manhours_id+"'>";
					viewbutton += "<input type='hidden' name='deduction' value='"+data.deduction_id+"'>";
					viewbutton += "<input type='hidden' name='additional' value='"+data.additional_id+"'>";
					viewbutton += "<input type='hidden' name='department' value='"+data.dept_name+"'>";
					viewbutton += "<input type='hidden' name='date_range' value='"+date_range+"'>";
					viewbutton += "<input type='hidden' name='date_from' value='"+moment(data.fromdate).format("YYYY/MM/DD")
					+"'>";
					viewbutton += "<input type='hidden' name='date_to' value='"+moment(data.todate).format("YYYY/MM/DD")+"'>";
					viewbutton += "<input type='hidden' name='paytype_desc' value='"+data.paytype_desc+"'>";
					viewbutton += "<center><button type = 'submit' class = 'btn btn-primary mr-1 ' style = 'width:90px;'>";
					viewbutton += "<i class='fa fa-eye'></i>&nbsp;&nbsp;View</buttton><button data-payroll_refno = '"+data.payroll_refno+"' type = 'button' class = 'btn btn-primary btn_bank_file' style = 'width:90px;'>";
					viewbutton += "<i class='fa fa-bank'></i>&nbsp;&nbsp;Bank File</buttton></center>";
					viewbutton += "</form>";
					if(data.status != "approved"){
						viewbutton += "<button type = 'button' class = 'btn btn-info  d-inline ml-1' ";
						viewbutton += "id = 'approve_btn"+data.pay_id+"' data-id = '"+data.pay_id+"' data-payroll_refno = '"+data.payroll_refno+"'  data-manhours = '"+data.manhours_id+"' data-additionals = '"+data.additional_id+"' data-deductions = '"+data.deduction_id+"'";
						viewbutton += "data-target = '#approve_payroll_modal' data-toggle = 'modal'";
						viewbutton += "style = 'width:90px;'><i class='fa fa-check' aria-hidden='true'></i> Finalize </button>";
					}else{
					}
					// viewbutton += "<button type = 'button' class = 'btn btn-info  d-inline ml-1' ";
					// viewbutton += "id = 'approve_btn"+data.pay_id+"' data-id = '"+data.pay_id+"'  data-manhours = '"+data.manhours_id+"' data-additionals = '"+data.additional_id+"' data-deductions = '"+data.deduction_id+"'";
					// viewbutton += "data-target = '#approve_payroll_modal' data-toggle = 'modal'";
					// viewbutton += "style = 'width:90px;'><i class='fa fa-check' aria-hidden='true'></i> Finalize </button>";
					// viewbutton += "</center>";

					return viewbutton;
				}
			}
		]
	});
	//dept
	//date
	//paytype
	$("#filter_by").change(function(){
			// $.LoadingOverlay('show');
			payroll_history_tbl.columns(0).search("");
			payroll_history_tbl.columns(1).search("");
			payroll_history_tbl.columns(2).search("");
			payroll_history_tbl.draw();
		//active class will be hidden slowly
			$('.filter_div').removeClass('active');
			$('#dept_search').val("");
			// $('#date_search').val("");
			$('#paytype_search').val("");
			switch ($(this).val()) {
				case "by_department":
					$('.filter_div').hide("slow");
					$('#div_dept').show("slow");
					$('#div_dept').addClass('active');
					break;
				case "by_date_generated":
					$('.filter_div').hide("slow");
					$('#div_date').show("slow");
					$('#div_date').addClass('active');
					break;
				case "by_pay_type":
					$('.filter_div').hide("slow");
					$('#div_pay_type').show("slow");
					$('#div_pay_type').addClass('active');
					break;
			default:
			}
			// $.LoadingOverlay('hide');
	});

	$("#searchButton").click(function(){
		var filter = $("#filter_by").val();
		var dept_search = $("#dept_search").val();
		var date_search = $("#date_search").val();
		var paytype_search = $("#paytype_search").val();

		if(filter == "by_department"){
			payroll_history_tbl.columns(0).search(dept_search);
			payroll_history_tbl.draw();
		}
		else if(filter == "by_date_generated"){
			payroll_history_tbl.columns(1).search(date_search);
			payroll_history_tbl.draw();
		}
		else if(filter == "by_pay_type"){
			payroll_history_tbl.columns(2).search(paytype_search);
			payroll_history_tbl.draw();

		}
		// payroll_history_tbl.draw(false);
	});
	$("#approve_payroll_btn").click(function(){
		var payroll_history_summary = $("#pay_id").val();
		var payroll_refno = $('#payroll_refno').val();
		var additionals_history_summary = $("#additionals_id").val();
		var deductions_history_summary = $("#deductions_id").val();
		var manhours_history_summary = $("#manhours_id").val();

		var data = {
			payroll_id:payroll_history_summary,
			additionals_id:additionals_history_summary,
			deductions_id:deductions_history_summary,
			manhours_id:manhours_history_summary,
			payroll_refno
		};
		//lipat to sa baba after
		var data_payslip = {
			payroll_id:payroll_history_summary
		};
		approve_payroll(data,function(status){
			if(status == 'success'){
				save_approved_payslip(data_payslip);
			}
		});

		//function approve payroll
	});

	function approve_payroll(data,callback){
		$.ajax({
			url: base_url+"payroll/Payroll_history/approve_payroll",
			type: "POST",
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data){
				var result = JSON.parse(data);
				var resoutput = result.output;
				if(result.success == 1){
					notificationSuccess('Success',resoutput);
					$("#approve_payroll_modal").modal('toggle');
					payroll_history_tbl.draw();
					$.LoadingOverlay('hide');
					callback('success');

				}else{
					notificationError('Error', resoutput);
					$("#approve_payroll_modal").modal('toggle');
					$.LoadingOverlay('hide');
					callback('error');

				}
			}
		});
	}
	function save_approved_payslip(data_payslip){
		$.ajax({
			url: base_url + "payroll/Payroll_history/save_approved_payslip",
			type: 'POST',
			data:data_payslip,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data){
				$.LoadingOverlay('hide');
			}
		});
	}
	$("#reject_payroll_btn").click(function(){
		var payroll_history_summary = $("#pay_id").val();
		var additionals_history_summary = $("#additionals_id").val();
		var deductions_history_summary = $("#deductions_id").val();
		var manhours_history_summary = $("#manhours_id").val();

		var data = {
			payroll_id:payroll_history_summary,
			additionals_id:additionals_history_summary,
			deductions_id:deductions_history_summary,
			manhours_id:manhours_history_summary
		};

		console.log(data);

		$.ajax({
			url: base_url+"payroll/Payroll_history/reject_payroll",
			type: "POST",
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data){
				var result = JSON.parse(data);
				var resoutput = result.output;
				if(result.success == 1){
					notificationSuccess('Success',resoutput);
					$("#approve_payroll_modal").modal('toggle');
					payroll_history_tbl.draw();
				$.LoadingOverlay('hide');
				}else{
					notificationError('Error', resoutput);
					$("#approve_payroll_modal").modal('toggle');
				$.LoadingOverlay('hide');
				}
			}
		});
	});

	$(document).on('click', '.btn_bank_file', function(){
		$('#bank_file_modal').modal();
		$('#payroll_refno').val($(this).data('payroll_refno'));
	});

	$(document).on('change', '#bank', function(){
		var bank = $(this).val();
		try{
			var template = $(`[data-id = "${bank}"]`).get(0).id;
		}catch(err){
			var template = "default_template";
		}
		// var template = $(`[data-id = "${bank}"]`).get(0).id;
		var payrol_refno = $('#payroll_refno').val();
		var file_type = $('#file_type').val();
		$('.div_template').hide('slow');
		$('.div_template').removeClass('active');

		switch (template) {
			case 'bdo_template':
				$('#bdo_template').show('slow');
				$('#bdo_template').addClass('active');
				break;
			case 'metro_bank_template':
				$('#metro_bank_template').show('slow');
				$('#metro_bank_template').addClass('active');
				break;
			case 'ctbc_template':
				$('#ctbc_template').show('slow');
				$('#ctbc_template').addClass('active');
				break;
			default:
				$('#default_template').show('slow');
				$('#default_template').addClass('active');
		}

	});

	$(document).on('click', '#btn_generate', function(){
		var bank = $('#bank').val();
		try{
			var template = $(`[data-id = "${bank}"]`).get(0).id;
		}catch(err){
			var template = "default_template";
		}
		var payrol_refno = $('#payroll_refno').val();
		var file_type = $('#file_type').val();

		switch (template) {
			case 'bdo_template':
				var bdo_company_name = $('#bdo_company_name').val();
				var bdo_file_prefix = $('#bdo_file_prefix').val();
				var bdo_virtual_account = $('#bdo_virtual_account').val();
				var bdo_credit_date = $('#bdo_credit_date').val();
				var bdo_batch_no = $('#bdo_batch_no').val();

				var error = 0;
				var errorMsg = "";

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).css("border", "1px solid #ef4131");
				  }else{
				    $(this).css("border", "1px solid gainsboro");
				  }
				});

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).focus();
				    error = 1;
				    errorMsg = "Please fill up all required fields.";
				    return false;
				  }
				});

				if(error == 0){
					window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?bdo_company_name='+bdo_company_name+'&bdo_file_prefix='+bdo_file_prefix+'&bdo_virtual_account='+bdo_virtual_account+'&bdo_credit_date='+bdo_credit_date+'&bdo_batch_no='+bdo_batch_no);
				}else{
				  notificationError('Error', errorMsg);
				}
				break;
			case 'metro_bank_template':
				var metro_company_name = $('#metro_company_name').val();
				var metro_branch_code = $('#metro_branch_code').val();
				var metro_date = $('#metro_date').val();

				var error = 0;
				var errorMsg = "";

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).css("border", "1px solid #ef4131");
				  }else{
				    $(this).css("border", "1px solid gainsboro");
				  }
				});

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).focus();
				    error = 1;
				    errorMsg = "Please fill up all required fields.";
				    return false;
				  }
				});

				if(error == 0){
					window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?metro_company_name='+metro_company_name+'&metro_branch_code='+metro_branch_code+'&metro_date='+metro_date);
				}else{
				  notificationError('Error', errorMsg);
				}
				break;
			case 'ctbc_template':
				var ctbc_company_name = $('#ctbc_company_name').val();
				var ctbc_date = $('#ctbc_date').val();

				var error = 0;
				var errorMsg = "";

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).css("border", "1px solid #ef4131");
				  }else{
				    $(this).css("border", "1px solid gainsboro");
				  }
				});

				$('.div_template .active .rq').each(function(){
				  if($(this).val() == ""){
				    $(this).focus();
				    error = 1;
				    errorMsg = "Please fill up all required fields.";
				    return false;
				  }
				});

				if(error == 0){
					window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?ctbc_company_name='+ctbc_company_name+'&ctbc_date='+ctbc_date);
				}else{
				  notificationError('Error', errorMsg);
				}
				break;
			default:
				var default_company_name = $('#default_company_name').val();
				var default_date = $('#default_date').val();
				window.open(base_url+'payroll/Payroll/generate_bank_file/'+token+'/'+template+'/'+payrol_refno+'/'+bank+'/'+file_type+'/?default_company_name='+default_company_name+'&default_date='+default_date);
		}
	});


});
