$(function(){
	var base_url = $('body').data('base_url');
	$("#view_pdf_btn").click(function(){
		window.open(base_url + 'employee_payslip/Employee_payslip/View_payslip');

	});
	function nwc(x) {
  		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	$("#download_pdf_btn").click(function(){
		window.open(base_url + 'employee_payslip/Employee_payslip/Download_payslip');

	});
	$("#generate_payslip").click(function(){
	    var date_from = $("#date_filter").children('option:selected').data('from');
	    var date_to = $("#date_filter").children('option:selected').data('to');
	    var employee_idno =  $("#date_filter").children('option:selected').data('employee_idno');
	    var data = {
	    	date_from:date_from,
	    	date_to:date_to,
	    	employee_idno
	    };

	   $.ajax({
	   		url: base_url + 'employee_payslip/Employee_payslip/Generate_payslip',
	   		type: "POST",
	   		data: data,
	   		beforeSend: function(){
	   			$.LoadingOverlay('show');
	   			$(this).prop('disabled',true);
	   		},
	   		success:function(data){
	   			var result = JSON.parse(data);
	   			if(result.success == 1){
	   				var ep = result.output;
	   				// console.log(ep);


					$("#p_idno").text(ep.employee_idno);
					$("#p_name").text(ep.name);
					$("#p_paytype_desc").text(ep.paytype_desc);
					$("#p_date_from").text(ep.date_from);
					$("#p_date_to").text(ep.date_to);
					$("#p_wday").text(ep.days_duration);
					$("#p_grosspay").text(nwc(ep.gross_salary));
					$("#p_reg_holiday").text(ep.regular_holiday_duration);
					$("#p_reg_holiday_pay").text(nwc(ep.regular_holiday));
					$("#p_spl_holiday").text(ep.special_holiday_duration);
					$("#p_spl_holiday_pay").text(nwc(ep.special_holiday));
					$("#p_sunday").text(ep.sunday_duration);
					$("#p_sunday_pay").text(nwc(ep.sundays));
					$("#p_absent").text(ep.absent_duration);
					$("#p_absent_deduct").text(nwc(ep.absent));
					$("#p_late").text(ep.late_duration);
					$("#p_late_deduct").text(nwc(ep.late));
					$("#p_ut").text(ep.undertime_duration);
					$("#p_ut_deduct").text(nwc(ep.undertime));
					$("#p_grosspay_less").text(nwc(ep.gross_salary));
					$("#p_sss").text(nwc(ep.sss));
					$("#p_philhealth").text(nwc(ep.philhealth));
					$("#p_pagibig").text(nwc(ep.pag_ibig));
					$("#p_sss_loan").text(nwc(ep.pag_ibig));
					$("#p_pag_ibig_loan").text(nwc(ep.pag_ibig));
					$("#p_cashadvance").text(nwc(ep.cashadvance));
					$("#p_sal_deduct").text(nwc(ep.salary_deduction));
					$("#p_total_deduct").text(nwc(ep.total_deductions));
					$("#p_add_pay").text(nwc(ep.additionals));
					$("#p_ot_min").text(ep.ot_duration);
					$("#p_ot_pay").text(nwc(ep.overtime));
					$("#p_net_pay").text(nwc(ep.netpay));
					// console.log(nwc(ep.netpay));

	   				//hide/disable properties
	   				$.LoadingOverlay('hide');
	   				$(this).prop('disabled',false);
	   				notificationSuccess('Success', result.message);
				     //  setTimeout(function(){
				     //   location.reload();
				     // }, 1000);
	   			}else{
	   				$.LoadingOverlay('hide');
	   				$(this).prop('disabled',false);
	   				notificationError('Error',result.message);
				      setTimeout(function(){
				       location.reload();
				     }, 1000);
	   			}

	   		}
	   });

	});
});
