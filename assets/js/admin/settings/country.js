$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/settings_country", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});
	

	$(".saveBtnCountry").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_country-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_country',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnCountry").prop('disabled', true); 
				$(".saveBtnCountry").text("Please wait...");
				
			},
			success:function(data){
				$(".cancelBtn, .saveBtnCountry").prop('disabled', false);
				$(".saveBtnCountry").text("Add");
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
					$('#addCountryModal').modal('toggle'); //close modal
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
		var country_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoCountryUsingID',
	  		data:{'country_id':country_id},
	  		success:function(data){
	  			
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".del_country_id").val(res[0].country_id); //user_id pk
	  			}
	  		}
	  	});
		$('#deleteCountryModal').modal('show');

	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewCountryModal').modal('show');
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var country_id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoCountryUsingID',
	  		data:{'country_id':country_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				dataTable.draw(); //refresh table

  					$(".view_country_id").val(res[0].country_id);
	  				$(".view_country_name").val(res[0].country);

	  				$(".edit_country_name").val(res[0].country);
	  				$(".country_id").val(res[0].country_id);
	  				$(".current_country_name").val(res[0].country);
	  			}
	  		}
	  	});
	});

	$(".goToEditModalCountryBtn").click(function(e){
		e.preventDefault();
			$('#viewCountryModal').modal('toggle');
			$('#editCountryModal').modal('show'); 
	});

	$("#edit_country-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_country',
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
					$('#editCountryModal').modal('toggle'); //close modal
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

	$('.deleteCountryBtn').click(function(e){
		e.preventDefault();

		var del_country_id = $(".del_country_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteCountry',
			data:{'del_country_id':del_country_id},
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
					$('#deleteCountryModal').modal('toggle'); //close modal
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

	$('.info_day_limit').keyup(function(){
		var max = $('.info_day_limit').val();
		// alert(max);
		$('.info_hour_limit').attr('max',max);
	});

	$('.edit_day_limit').keyup(function(){
		var max = $('.edit_day_limit').val();
		// alert(max);
		$('.edit_hour_limit').attr('max',max);
	});

	$('.info_hour_limit').keyup(function(){
		var x = parseInt($('.info_day_limit').val());
		var y = parseInt($('.info_hour_limit').val());
		
		if( x < y ){
			$.toast({
			    heading: 'Error',
			    text: "Hour limit should not exceed day limit",
			    icon: 'error',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#d9534f',
				textColor: 'white'        
			});

			$('.saveBtnSchedLimit').prop('disabled','disabled');
		} else{
			$('.saveBtnSchedLimit').prop('disabled',false);
		}

	});

	$('.edit_hour_limit').keyup(function(){
		var x = parseInt($('.edit_day_limit').val());
		var y = parseInt($('.edit_hour_limit').val());
		
		if( x < y ){
			$.toast({
			    heading: 'Error',
			    text: "Hour limit should not exceed day limit",
			    icon: 'error',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#d9534f',
				textColor: 'white'        
			});

			$('.EditSchedLimitModalBtn').prop('disabled','disabled');
		} else{
			$('.EditSchedLimitModalBtn').prop('disabled',false);
		}

	});

	$(".saveBtnSchedLimit").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_schedlimit-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_sched_limit',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnHoliday").prop('disabled', true); 
				$(".saveBtnHoliday").text("Please wait...");
				
			},
			success:function(data){
				$(".cancelBtn, .saveBtnHoliday").prop('disabled', false);
				$(".saveBtnHoliday").text("Add");
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
					$('#addScheduleLimitModal').modal('toggle'); //close modal
					dataTable2.draw(); //refresh table
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

	$('#table-grid2').delegate(".btnView", "click", function(){
		$('#viewSchedLimitModal').modal('show');
	});

	$('#table-grid2').delegate(".btnView", "click", function(){

	  	var sched_limit_id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoSchedLimitUsingID',
	  		data:{'sched_limit_id':sched_limit_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				dataTable.draw(); //refresh table

  					$(".view_day_limit").val(res[0].day_limit);
	  				$(".view_hour_limit").val(res[0].hour_limit);
	  				$(".view_branch_code").val(res[0].sched_limit_branch_id).trigger('change');//for select2
	  				$(".view_start_date").val(res[0].start_date);
	  				$(".view_end_date").val(res[0].end_date);

	  				$(".edit_day_limit").val(res[0].day_limit);
	  				$(".edit_hour_limit").val(res[0].hour_limit);
	  				$(".edit_branch_code").val(res[0].sched_limit_branch_id).trigger('change');//for select2
	  				$(".edit_start_date").val(res[0].start_date);
	  				$(".edit_end_date").val(res[0].end_date);
	  				$(".sched_limit_id").val(res[0].sched_limit_id);
	  			}
	  		}
	  	});
	});

	$("#edit_schedlimit-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_sched_limit',
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
					dataTable2.draw(); //refresh table
					$('#editSchedLimitModal').modal('toggle'); //close modal
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

	$('#table-grid2').delegate(".btnDelete","click", function(){
		var sched_limit_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoSchedLimitUsingID',
	  		data:{'sched_limit_id':sched_limit_id},
	  		success:function(data){
	  			
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".del_sched_limit_id").val(res[0].sched_limit_id); //user_id pk
	  			}
	  		}
	  	});
		$('#deleteSchedLimitModal').modal('show');

	});

	$('.deleteSchedLimitBtn').click(function(e){
		e.preventDefault();

		var del_sched_limit_id = $(".del_sched_limit_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main/delete_sched_limit',
			data:{'del_sched_limit_id':del_sched_limit_id},
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
					dataTable2.draw(); //refresh table
					$('#deleteSchedLimitModal').modal('toggle'); //close modal
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