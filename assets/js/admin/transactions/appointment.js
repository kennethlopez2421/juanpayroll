$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$('#datepicker').datepicker();
	$('#datepicker2').datepicker();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/transactions_appointment_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('.filterBtn').click(function(e){
		e.preventDefault();
		if($('.searchAppStatus').val() != "" ||  $('.searchAppNo').val() != "" || $('.searchBCode').val() != "" || $('.searchDate').val != "" || $('.searchDate2').val != ""){

			var a =$('.searchAppStatus').attr('data-column');  // getting column index
			var b =$('.searchAppStatus').val();  // getting search input value
			var c =$('.searchAppNo').attr('data-column');  // getting column index
			var d =$('.searchAppNo').val();  // getting search input value
			var e =$('.searchBCode').attr('data-column');  // getting column index
			var f =$('.searchBCode').val();  // getting search input value
			var g =$('.searchDate').attr('data-column');  
			var h =$('.searchDate').val();  
			var i =$('.searchDate2').attr('data-column');  
			var j =$('.searchDate2').val();

			dataTable.columns(a).search(b);
			dataTable.columns(c).search(d);
			dataTable.columns(e).search(f);	
			dataTable.columns(g).search(h);
			dataTable.columns(i).search(j).draw();
		}else{
			dataTable.columns(a).search(b);
			dataTable.columns(c).search(d);
			dataTable.columns(e).search(f);	
			dataTable.columns(g).search(h);
			dataTable.columns(i).search(j).draw("");
		}
		
	});

	// $('.filterBtn').click(function(e){
	// 	e.preventDefault();
	// 	if($('.searchAppStatus').val() != ""){
	// 		var i =$('.searchAppStatus').attr('data-column');  // getting column index
	// 		var v =$('.searchAppStatus').val();  // getting search input value
	// 		dataTable.columns(i).search(v).draw();
	// 	};	

	// 	if($('.searchAppNo').val() != ""){
	// 		var i =$('.searchAppNo').attr('data-column');  // getting column index
	// 		var v =$('.searchAppNo').val();  // getting search input value
	// 		dataTable.columns(i).search(v).draw();
	// 	};

	// 	if($('.searchBCode').val() != ""){
	// 		var i =$('.searchBCode').attr('data-column');  // getting column index
	// 		var v =$('.searchBCode').val();  // getting search input value
	// 		dataTable.columns(i).search(v).draw();
	// 	};

	// 	if($('.searchDate').val != ""){
	// 		var i =$('.searchDate').attr('data-column');  
	// 		var v =$('.searchDate').val();  
	// 		dataTable.columns(i).search(v).draw();
	// 	}

	// 	if($('.searchDate2').val != ""){
	// 		var i =$('.searchDate2').attr('data-column');  
	// 		var v =$('.searchDate2').val();  
	// 		dataTable.columns(i).search(v).draw();
	// 	}

		// dataTable.draw();
		
	// });
	
	// searchToday();
	// function searchToday(){ //for date today search first
	// 	var i = $(".searchDateTo").attr('data-column');  // getting column index
	// 	var v = $(".searchDateTo").val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// }
	
	// $('.search-input-text').on('keyup click', function(){   // for text boxes
	// 	var i =$(this).attr('data-column');  // getting column index
	// 	var v =$(this).val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// });

	// $('.searchDate').on('change', function(){   // for select box
	// 	var i =$(this).attr('data-column');  
	// 	var v =$(this).val();  
	// 	dataTable.columns(i).search(v).draw();
	// });

	// $('.searchDateTo').on('change', function(){   // for select box
	// 	var i =$(this).attr('data-column');  
	// 	var v =$(this).val();
	// 	dataTable.columns(i).search(v).draw();
	// });


	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewAppointmentModal').modal('show');
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var app_id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoTransactionAppointmentUsingID',
	  		data:{'app_id':app_id },
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
					
  					$(".appr_appointment_id").val(res[0].appointment_id);
  					$(".info_app_ref_no").val(res[0].app_reference_no);
  					$(".info_app_name").val(data.first_name + " " + data.middle_name + " " + data.last_name) ;
	  				$(".info_bcode").val(res[0].branch_code);
					$(".info_app_date").val(res[0].appointment_datesched);
					$(".info_app_time").val(res[0].timesched_description);
					$(".info_app_status").val(res[0].status_description);
					$(".rej_appointment_id").val(res[0].appointment_id);

					if(res[0].appointment_status_id == 1){ //if approved
						$(".goToEditModalAccountsBtn").prop('disabled', 'disabled');
						$(".goToRejectModalAppointmentBtn").prop('disabled', false);
						$(".rejection_reason_div").css('display', 'none');
					} else if(res[0].appointment_status_id == 2){ //if rejected
						$(".goToEditModalAccountsBtn").prop('disabled', false);
						$(".goToRejectModalAppointmentBtn").prop('disabled', 'disabled');
						$(".info_app_rej_reason").val(res[0].rejection_reason);
						$('.rejection_reason_div').css('display', 'block');
					} else if(res[0].appointment_status_id == 0){ //if rejected{
						$(".rejection_reason_div").css('display', 'none');
					}
	  			}
	  		}
	  	});
	});

	$(".goToEditModalAccountsBtn").click(function(e){
		e.preventDefault();
			$('#viewAppointmentModal').modal('toggle');
			$('#approveAppoinmentModal').modal('show'); 
	});

	$(".goToRejectModalAppointmentBtn").click(function(e){
		e.preventDefault();
			$('#viewAppointmentModal').modal('toggle');
			$('#rejectAppointmentModal').modal('show'); 
		
	});

	$("#approve_appointmentinfo-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/approveAdminTransactionsAppointment',
			data:serial,
			success:function(data){
				if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
					    bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000
					});
					dataTable.draw(); //refresh table
					$('#approveAppoinmentModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Error',
					    text: data.message,
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#d9534f',
						textColor: 'white'        
					});
				}
			}
		});
	});

	$("#reject_appointmentinfo-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/rejectAdminTransactionsAppointment',
			data:serial,
			success:function(data){
				if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
					    bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000
					});
					dataTable.draw(); //refresh table
					$('#rejectAppointmentModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Error',
					    text: data.message,
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#d9534f',
						textColor: 'white'        
					});
				}
			}
		});
	});


	$('#table-grid').delegate(".btnDelete","click", function(){
		var app_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoTransactionAppointmentUsingID',
	  		data:{'app_id':app_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  					$(".del_appointment_id").val(app_id);
	  					
	  				}
	  			}
	  		});
		$('#deleteAppointmentModal').modal('show');

	});


	$('.deleteAccountBtn').click(function(e){
		e.preventDefault();

		var del_app_id = $(".del_appointment_id").val();
		console.log(del_app_id);

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteAdminTransactionsAppointment',
			data: {'del_app_id':del_app_id},
			success:function(data){
				if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
					    bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000
					});
					dataTable.draw(); //refresh table
					$('#deleteAppointmentModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Error',
					    text: data.message,
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#d9534f',
						textColor: 'white'        
					});
				}
			}
		});
	});
});