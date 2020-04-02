$(function(){
	var base_url = $("body").data('base_url');
	var token = $("#token").val();
	var manhours_id = $("#manhrsid").val();
	var deduction_id = $("#deductionid").val();
	var additional_id = $("#additionalid").val();
	var payroll_id = $("#payrollid").val();
	var date_from = $("#date_from").val();
	var date_to = $("#date_to").val();
	var paytype_desc = $("#paytype_desc").val();
	console.log(paytype_desc);
	var manhours_data = {
		date_from:date_from,
		date_to:date_to,
		manhours_id:manhours_id
	};
	var deduction_data = {
		date_from:date_from,
		date_to:date_to,
		deduction_id:deduction_id
	};
	var additional_data = {
		date_from:date_from,
		date_to:date_to,
		additional_id:additional_id
	};
	var payroll_data = {
		date_from:date_from,
		date_to:date_to,
		payroll_id:payroll_id
	};
	//manhourstable
		var manhrs_tbl = $("#manhourstable").DataTable({
 		processing:false,
		serverSide:true,
		ajax:{
			url: base_url+'payroll/Payroll_history/manhourstable',
			data:manhours_data,
			beforeSend: function(){
	        	$.LoadingOverlay('show');
	    	},
	    	complete: function(){
	        	$.LoadingOverlay('hide');
	     	},
		},
		columns:[
			{data:"emp_id"},
			{data:function(data,type,dataToSet){
				var name = "";
				name += data.first_name + " " + data.middle_name + " " + data.last_name;
				return name;
			}},
			{data:"days"},
			{data:"hours"},
			{data:"absent"},
			{data:"late"},
			{data:"ot"},
			{data:"ut"}
		],
		columnDefs:[
			{
				"targets": 8,
				"data":null,
				render:function(data,type,row,meta){
					var name = "";
					name += data.first_name + " " + data.middle_name + " " + data.last_name;
					$(document).on('click','#viewmanhoursbtn'+data.id,function(e){
						//stops bubbling of output data
						e.stopImmediatePropagation();
						$(".employee_name").html(name);
						var employee_idno = $(this).data('employee_idno');
						var data = {
							employee_idno:employee_idno,
							fromdate:date_from,
							todate:date_to
						};
						$.ajax({
							url: base_url + "payroll/Payroll_history/getmanhourslogs",
							type: 'POST',
							data:data,
							success:function(data){
								var result = JSON.parse(data);
								var manhoursconcat = "";
								if(result.success == 1){
									var resoutput = result.output;
									var reslength = resoutput.length;
									console.log(resoutput);
									for(var x = 0; x < reslength; x++){
											manhoursconcat += "<tr>";
											manhoursconcat += "<td>"+resoutput[x].date+"</td>";
											manhoursconcat += "<td>"+resoutput[x].day_type+"</td>";
											manhoursconcat += "<td>"+resoutput[x].time_data+"</td>";
											manhoursconcat += "<td>"+resoutput[x].man_hours+"</td>";
											manhoursconcat += "<td>"+resoutput[x].late+"</td>";
											manhoursconcat += "<td>"+resoutput[x].overtime+"</td>";
											manhoursconcat += "<td>"+resoutput[x].undertime+"</td>";
											manhoursconcat += "</tr>";
									}
								}else{
											manhoursconcat += "<tr>";
											manhoursconcat += "<td>No Data Available</td>";
											manhoursconcat += "<td>No Data Available</td>";
											manhoursconcat += "<td>No Data Available</td>";
											manhoursconcat += "<td>No Data Available</td>";
											manhoursconcat += "<td>No Data Available</td>";
											manhoursconcat += "<td>No Data Available</td>";
											manhoursconcat += "<td>No Data Available</td>";
											manhoursconcat += "</tr>";
								}
								$("#manhourslogs_body").html(manhoursconcat);
							}
						});
					});
					var viewbutton = "";
					viewbutton += "<center><button type = 'button' id = 'viewmanhoursbtn"+data.id+"' data-id = '"+data.id+"' data-employee_idno = '"+data.emp_id+"' class = 'btn btn-success' data-toggle='modal' data-target='#viewmanhourslog' style = 'width:90px;'><i class='fa fa-eye'></i>&nbsp;&nbsp;View</button></center>";
					return viewbutton;
				}
			}
		]
	});
		//deductions table
		var deductions_history_tbl = $("#deductionstable").DataTable({
 		processing:false,
		serverSide:true,
		ajax:{
			url: base_url+'payroll/Payroll_history/deductionstable',
			data:deduction_data,
			beforeSend: function(){
	        	$.LoadingOverlay('show');
	    	},
	    	complete: function(){
	        	$.LoadingOverlay('hide');
	     	},
		},
		columns:[
			{data:"employee_idno"},
			{data:function(data,type,dataToSet){
				var name = "";
				name += data.first_name + " " + data.middle_name + " " + data.last_name;
				return name;
			}},
			{data:"sss"},
			{data:"philhealth"},
			{data:"pag_ibig"},
			{data:"salary_deduction"},
			{data:"cashadvance"},
		],
		columnDefs:[
			{
				"targets": 7,
				"data":null,
				render:function(data,type,row,meta){
					var name = "";
					name += data.first_name + " " + data.middle_name + " " + data.last_name;
					$(document).on('click','#viewdeductionsbtn'+data.id,function(e){
						e.stopImmediatePropagation();
						$(".employee_name").html(name);
						var employee_idno = $(this).data('employee_idno');
						var sss = $(this).data('sss');
						var philhealth = $(this).data('philhealth');
						var pag_ibig = $(this).data('pag_ibig');
						//salary deduction
						var total_compensation_deduction = sss+philhealth+pag_ibig;

						var data = {
							employee_idno:employee_idno,
							date_from:date_from,
							date_to:date_to
						};
						// compensations
						var compensations_concat = "";
						compensations_concat += "<tr>";
                        compensations_concat +=     "<td>"+sss+"</td>";
                        compensations_concat +=     "<td>"+philhealth+"</td>";
                        compensations_concat +=     "<td>"+pag_ibig+"</td>";
                        compensations_concat +=     "<td>"+total_compensation_deduction+"</td>";
                        compensations_concat += "</tr>";
                        $("#compensations_body_tbl").html(compensations_concat);
                        //salary deduction table sd_body_tbl

						$.ajax({
							url: base_url + "payroll/Payroll_history/getsalary_deduction",
							type: 'POST',
							data:data,
							success:function(data){
								var result = JSON.parse(data);
								var sd_body_tbl_concat = "";
								if(result.success == 1){
									var resoutput = result.output;
									// console.log(resoutput);
									for(x = 0; x < resoutput.length; x++){
										sd_body_tbl_concat += "<tr>";
                                    	sd_body_tbl_concat +=     "<td>"+moment(resoutput[x].date_created).format("YYYY/MM/DD")+"</td>";
                                    	sd_body_tbl_concat +=     "<td>"+resoutput[x].description+"</td>";
                                    	sd_body_tbl_concat +=     "<td>"+resoutput[x].amount+"</td>";
                                    	sd_body_tbl_concat += "</tr>";
									}
								}else{
										sd_body_tbl_concat += "<tr>";
                                    	sd_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	sd_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	sd_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	sd_body_tbl_concat += "</tr>";
								}
								$("#sd_body_tbl").html(sd_body_tbl_concat);
							}
						});
						//cash advance tran
						$.ajax({
							url: base_url + "payroll/Payroll_history/getcashadvance_tran",
							type: 'POST',
							data:data,
							success:function(data){
								var result = JSON.parse(data);
								var ca_body_tbl_concat = "";
								if(result.success == 1){
									var resoutput = result.output;
									console.log(resoutput);
									// console.log(resoutput);
									for(x = 0; x < resoutput.length; x++){
										ca_body_tbl_concat += "<tr>";
                                    	ca_body_tbl_concat +=     "<td>"+moment(resoutput[x].date_of_file).format("YYYY/MM/DD")+"</td>";
                                    	ca_body_tbl_concat +=     "<td>"+resoutput[x].reason+"</td>";
                                    	ca_body_tbl_concat +=     "<td>"+resoutput[x].amount+"</td>";
                                    	ca_body_tbl_concat += "</tr>";
									}
								}else{
										ca_body_tbl_concat += "<tr>";
                                    	ca_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	ca_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	ca_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	ca_body_tbl_concat += "</tr>";
								}
								$("#ca_body_tbl").html(ca_body_tbl_concat);
							}
						});
					});
					var viewbutton = "";
					viewbutton += "<center><button type = 'button' id = 'viewdeductionsbtn"+data.id+"'";
					viewbutton += "data-id = '"+data.id+"' class = 'btn btn-success' ";
					viewbutton += "data-toggle='modal' data-target='#viewdeductionslog'";
					viewbutton += "data-id = '"+data.id+"' data-name = '"+name+"' data-employee_idno = '"+data.employee_idno+"'";
					viewbutton += "data-sss = '"+data.sss+"' data-philhealth = '"+data.philhealth+"' data-pag_ibig = '"+data.pag_ibig+"'";
					viewbutton += "style = 'width:90px;'>";
					viewbutton += "<i class='fa fa-eye'></i>&nbsp;&nbsp;View</button></center>";
					return viewbutton;
				}
			}
		]
	});
		//additionals table
	var additionals_history_tbl = $("#additionalstable").DataTable({
 		processing:false,
		serverSide:true,
		ajax:{
			url: base_url+'payroll/Payroll_history/additionalstable',
			data:additional_data,
			beforeSend: function(){
	        	$.LoadingOverlay('show');
	    	},
	    	complete: function(){
	        	$.LoadingOverlay('hide');
	     	},
		},
		columns:[
			{data:"emp_id"},
			{data:function(data,type,dataToSet){
				var name = "";
				name += data.first_name + " " + data.middle_name + " " + data.last_name;
				return name;
			}},
			{data:"additionalpay"},
			{data:"overtimepay"},
		],
		columnDefs:[
			{
				"targets": 4,
				"data":null,
				render:function(data,type,row,meta){
					var name = "";
					name += data.first_name + " " + data.middle_name + " " + data.last_name;
					$(document).on('click','#viewadditionalsbtn'+data.id,function(e){
						$(".employee_name").html(name);
						e.stopImmediatePropagation();
						var employee_idno = $(this).data('employee_idno');
						var data = {
							employee_idno:employee_idno,
							date_from:date_from,
							date_to:date_to
						};
						//additionals
						$.ajax({
							url: base_url + "payroll/Payroll_history/getadditonal_pays",
							type: 'POST',
							data:data,
							success:function(data){
								var result = JSON.parse(data);
								var ap_body_tbl_concat = "";
								if(result.success == 1){
									var resoutput = result.output;
									// console.log(resoutput);
									for(x = 0; x < resoutput.length; x++){
										ap_body_tbl_concat += "<tr>";
                                    	ap_body_tbl_concat +=     "<td>"+moment(resoutput[x].date_issued).format("YYYY/MM/DD")+"</td>";
                                    	ap_body_tbl_concat +=     "<td>"+resoutput[x].purpose+"</td>";
                                    	ap_body_tbl_concat += "</tr>";
									}
								}else{
										ap_body_tbl_concat += "<tr>";
                                    	ap_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	ap_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	ap_body_tbl_concat += "</tr>";
								}
								$("#additionals_pays_tab_body").html(ap_body_tbl_concat);
							}
						});
						//overtime pays
						$.ajax({
							url: base_url + "payroll/Payroll_history/getot_pays",
							type: 'POST',
							data:data,
							success:function(data){
								var result = JSON.parse(data);
								var op_body_tbl_concat = "";
								if(result.success == 1){
									var resoutput = result.output;
									// console.log(resoutput);
									for(x = 0; x < resoutput.length; x++){
										op_body_tbl_concat += "<tr>";
                                    	op_body_tbl_concat +=     "<td>"+moment(resoutput[x].date_created).format("YYYY/MM/DD")+"</td>";
                                    	op_body_tbl_concat +=     "<td>"+resoutput[x].purpose+"</td>";
                                    	op_body_tbl_concat +=     "<td>"+resoutput[x].minutes_of_overtime+"</td>";
                                    	op_body_tbl_concat += "</tr>";
									}
								}else{
										op_body_tbl_concat += "<tr>";
                                    	op_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	op_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	op_body_tbl_concat +=     "<td>No Data Available</td>";
                                    	op_body_tbl_concat += "</tr>";
								}
								$("#overtime_pays_tab_body").html(op_body_tbl_concat);
							}
						});
					});
					var viewbutton = "";
					viewbutton += "<center><button id = 'viewadditionalsbtn"+data.id+"' class = 'btn btn-success' ";
					viewbutton += "data-toggle = 'modal' data-target = '#viewadditionalslog'";
					viewbutton += "data-employee_idno = '"+data.emp_id+"'"; 
					viewbutton += "style = 'width:90px;'><i class='fa fa-eye'>";
					viewbutton += "</i>&nbsp;&nbsp;View</button></center>";
					return viewbutton;
				}
			}
		]
	});
		//payroll table
	var payroll_history_tbl = $("#payrolltable").DataTable({
 		processing:false,
		serverSide:true,
		ajax:{
			url: base_url+'payroll/Payroll_history/payroll_summary_table',
			data:payroll_data,
			beforeSend: function(){
	        	$.LoadingOverlay('show');
	    	},
	    	complete: function(){
	        	$.LoadingOverlay('hide');
	        	var grosspay = payroll_history_tbl.column(2).data().sum();
	        	var deductions = payroll_history_tbl.column(3).data().sum();
	        	var additionals = payroll_history_tbl.column(4).data().sum();
	        	var netpay = payroll_history_tbl.column(5).data().sum();
	        	var employees_count = payroll_history_tbl.column(1).data().count();
	        	$(".grosspay").html("<b>"+accounting.formatMoney(grosspay)+"</b>");
	        	$(".deductions").html("<b>"+accounting.formatMoney(deductions)+"</b>");
	        	$(".additionals").html("<b>"+accounting.formatMoney(additionals)+"</b>");
	        	$(".netpay").html("<b>"+accounting.formatMoney(netpay)+"</b>");
	        	$(".employees_count").html("Total Employees: " + "<b>" +employees_count+ "</b>");
	     	},

		},
		fixedHeader: {
			footer: true
		},
		columns:[
			{data:"emp_id"},
			{data:function(data,type,dataToSet){
				var name = "";
				name += data.first_name + " " + data.middle_name + " " + data.last_name;
				return name;
			}},
			{data:function(data,type,dataToSet){
				var num_format = "";
				num_format += accounting.formatMoney(data.grosspay);
				return num_format;
			}},
			{data:function(data,type,dataToSet){
				var num_format = "";
				num_format += accounting.formatMoney(data.deductions);
				return num_format;
			}},
			{data:function(data,type,dataToSet){
				var num_format = "";
				num_format += accounting.formatMoney(data.additionals);
				return num_format;
			}},
			{data:function(data,type,dataToSet){
				var num_format = "";
				num_format += accounting.formatMoney(data.netpay);
				return num_format;
			}}
		],
		columnDefs:[
			{
				"targets": 6,
				"data":null,
				render:function(data,type,row,meta){
					var viewbutton = "";
					viewbutton += "<form method = 'POST' action = '"+base_url+"/payroll/Payroll_history/print_payroll/'"+token+" target = '_blank'>";
					viewbutton += "<center>";
					viewbutton += "<input type = 'hidden' name = 'employee_idno' value= '"+data.emp_id+"'>";
					viewbutton += "<input type = 'hidden' name = 'date_from' value = '"+data.fromdate+"'>";
					viewbutton += "<input type = 'hidden' name = 'date_to' value = '"+data.todate+"'>";
					viewbutton += "<input type = 'hidden' name = 'paytype_desc' value = '"+paytype_desc+"'>";
					viewbutton += "<button type = 'submit' id = 'printbtn' class = 'btn btn-warning' style = 'width:90px;'>";
					viewbutton += "<i class='fa fa-print'></i>&nbsp;&nbsp;Print</button>";
					viewbutton += "</center>";
					viewbutton += "</form>";
					return viewbutton;
				}
			},
			// {
			// 	"targets": 7,
			// 	"data":null,
			// 	render:function(data,type,row,meta){
			// 		$(document).on('click','#trybtn'+data.id,function(e){
			// 			e.stopImmediatePropagation();
			// 			var fromdate = $(this).data('fromdate');
			// 			var todate = $(this).data('todate');
			// 			var employee_idno = $(this).data('employee_idno');
			// 			var data = {
			// 				date_from:fromdate,
			// 				date_to:todate,
			// 				employee_idno:employee_idno
			// 			};

			// 				$.ajax({
			// 					type: 'POST',
			// 					url: base_url + 'payroll/Payroll_history/fetch_payroll_logs',
			// 					data:data,
			// 					success:function(data){

			// 					}
			// 				});
			// 			});
			// 		var buttons = "";
			// 		buttons += " <button type='button' id='trybtn"+data.id+"' data-id='"+data.id+"' data-employee_idno = '"+data.emp_id+"' data-fromdate = '"+data.fromdate+"' data-todate = '"+data.todate+"' class='btn btn-danger' style='width:90%;'> Try</button>";

			// 		return buttons;
			// 	}
			// },
		],

	});
	//----------------------table searches--------------
	// deductions,additionals,payroll
	$("#search_manhours").click(function(){
		var searchmanhours = $("#manhours_text").val();
		manhrs_tbl.search(searchmanhours).draw();
	});
	$("#search_additionals").click(function(){
		var searchadditionals = $("#additionals_text").val();
		additionals_history_tbl.search(searchadditionals).draw();
	});
	$("#search_deductions").click(function(){
		var searchdeductions = $("#deductions_text").val();
		deductions_history_tbl.search(searchdeductions).draw();
	});
	$("#search_payroll").click(function(){
		var searchpayroll = $("#payroll_text").val();
		payroll_history_tbl.search(searchpayroll).draw();
	});
	$('#tryfetch').click(function(){
		$.ajax({
			type: 'POST',
			url: base_url + "payroll/Payroll_history/payroll_log_json",
			success:function(data){

			}
		});
	});
});