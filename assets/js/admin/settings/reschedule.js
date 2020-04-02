$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/settings_reschedule_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},"initComplete": function(settings, json) {
			if($(".table_count").val() > 0 ){
				$("#btnClickAddResched").css("display","none");
			} else{
				$("#btnClickAddResched").css("display","block");
			}
		}

	});

	$(".saveBtnResched").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_resched-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_resched_fee',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnResched").prop('disabled', true); 
				$(".saveBtnResched").text("Please wait...");
			},
			success:function(data){
				dataTable.draw(); //refresh table
				$(".cancelBtn, .saveBtnResched").prop('disabled', false);
				$(".saveBtnResched").text("Add");
				if (data.success == 1) {
					$(thiss).find('input').val(""); // clean field
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
						hideAfter: 10000
					});
					$('#addReschedModal').modal('toggle'); //close modal
					$("#btnClickAddResched").css("display","none");
					dataTable.draw(); //refresh table
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
		var resched_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoRescheduleFeeUsingID',
	  		data:{'resched_id':resched_id},
	  		success:function(data){
	  			
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".del_resched_id").val(res[0].resched_id); //user_id pk
	  			}
	  		}
	  	});
		$('#deleteReschedModal').modal('show');

	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewReschedModal').modal('show');
	  	var resched_id = $(this).data('value');
	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoRescheduleFeeUsingID',
	  		data:{'resched_id':resched_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {

  					//for viewing
  					var a = accounting.formatMoney(res[0].resched_fee,"₱",2);
	  				$(".view_resched_fee").val(a);
	  				$(".view_resched_limit").val(res[0].resched_limit);

	  				// for editing
	  				$(".tbe_resched_limit").val(res[0].resched_limit);
	  				$(".tbe_resched_fee").val(res[0].resched_fee);
  					$(".tbe_resched_id").val(res[0].resched_id); //user_id pk
	  			}
	  		}
	  	});
	});


	$(".goToEditModalReschedBtn").click(function(e){
		e.preventDefault();
			$('#viewReschedModal').modal('toggle');
			$('#editReschedModal').modal('show'); 
	});

	$("#edit_resched-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_reschedule_fee',
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
					$('#editReschedModal').modal('toggle'); //close modal
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


	$('.deleteReschedBtn').click(function(e){
		e.preventDefault();

		var del_resched_id = $(".del_resched_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteRescheduleFee',
			data:{'del_resched_id':del_resched_id},
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
					$("#btnClickAddResched").css("display","block");
					$('#deleteReschedModal').modal('toggle'); //close modal
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


	$('.btnClickAddAccount').click(function(){
		$('.info_position').val('').trigger('change');
	});
	

	$("#view_resched-form").submit(function(e){
		e.preventDefault();

		$(this).find('input, select').prop('disabled',false);
		$(this).find('.datepicker').prop('readonly',false);
		$(this).find('input:first').focus();

		$(this).find('input:submit').prop('hidden',true);
		$(this).find('button').prop('hidden',false);
		$(this).find('.asterisk').prop('hidden', false);
		// var amount = $('.view_resched_fee').val();
		// var num = amount.replace(/[^0-9]/g,'') * .01;
		$('.view_resched_fee').prop('hidden', true);
		$('.view_resched_fee2').prop('hidden', false);
		$('.view_resched_id').prop('hidden', true);
		

		
		$.toast({
		    heading: 'Information',
		    text: 'The fields are editable',
		    icon: 'info',
		    loader: false,  
		    stack: false,
		    position: 'top-center', 
		    bgColor: 'rgb(43, 144, 217)',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 4000
		});
	});

	$(".cancelEditBtn").click(function(e){
		
		e.preventDefault();

		var thiss = $("#view_resched-form");
		//SaveEditBtn
		$(thiss).find('input, select').prop('disabled',true);
		$(thiss).find('.datepicker').prop('readonly',false);

		$(thiss).find('input:submit').prop('hidden',false);
		$(thiss).find('input:submit').prop('disabled',false);
		$(thiss).find('button').prop('hidden',true);
		$(thiss).find('.asterisk').prop('hidden', true);

		$('.view_resched_fee').prop('hidden', false);
		$('.view_resched_fee2').prop('hidden', true);

	});

	$(".saveEditBtn").click(function(e){
		
		e.preventDefault();

		var thiss = $("#view_resched-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/edit_reschedule_fee',
			data: serial,
			beforeSend:function(data){
				$(".cancelEditBtn, .saveEditBtn").prop('disabled', true); 
				
			},
			success:function(data){
				$(".cancelEditBtn, .saveEditBtn").prop('disabled', false);
				if (data.success == 1) {

					$(thiss).find('input, select').prop('disabled',true);
					$(thiss).find('.datepicker').prop('readonly',false);

					$(thiss).find('input:submit').prop('hidden',false);
					$(thiss).find('input:submit').prop('disabled',false);
					$(thiss).find('button').prop('hidden',true);
					$(thiss).find('.asterisk').prop('hidden', true);

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
						hideAfter: 10000
					});
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