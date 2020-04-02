$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/settings_schedule_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	var dataTable2 = $('#table-grid2').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/settings_schedule_limit_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	var holidays = [];

	$.ajax({
		type: 'get',
		url: base_url+'Main/get_holiday',
		success:function(data){

			var res = data.result;
			for(var x = 0; x < res.length; x++){
				holidays.push(res[x].holiday_date.replace(/(^|-)0+/g, "$1"));
			}
  			trigger_datepicker(holidays)
		}
  	});

  	function trigger_datepicker(holidays){
  		$(".datepicker_holiday").datepicker({ 
	        autoclose: true, 
	        todayHighlight: true,
	        format: 'yyyy/mm/dd',
	        beforeShowDay:function(Date){
				var curr_day = Date.getDate();
		        var curr_month = Date.getMonth()+1;
		        var curr_year = Date.getFullYear();        
		        var curr_date = curr_year+'-'+curr_month+'-'+curr_day;  
	        if (holidays.indexOf(curr_date)>-1) return false; 
			}
	  	});
  	}
	

	$(".saveBtnHoliday").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_holiday-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_holiday',
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
					$('#addHolidayModal').modal('toggle'); //close modal
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
		var holiday_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoHolidayUsingID',
	  		data:{'holiday_id':holiday_id},
	  		success:function(data){
	  			
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".del_holiday_id").val(res[0].holiday_id); //user_id pk
	  			}
	  		}
	  	});
		$('#deleteHolidayModal').modal('show');

	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewHolidayModal').modal('show');
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var holiday_id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoHolidayUsingID',
	  		data:{'holiday_id':holiday_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				dataTable.draw(); //refresh table

  					$(".view_hol_desc").val(res[0].holiday_description);
	  				$(".view_hol_date").val(res[0].holiday_date);

	  				$(".edit_hol_desc").val(res[0].holiday_description);
	  				$(".edit_hol_date").val(res[0].holiday_date);
	  				$(".holiday_id").val(res[0].holiday_id);
	  				$(".current_holiday_name").val(res[0].holiday_description);
	  			}
	  		}
	  	});
	});

	$(".goToEditModalHolidayBtn").click(function(e){
		e.preventDefault();
			$('#viewHolidayModal').modal('toggle');
			$('#editHolidayModal').modal('show'); 
	});

	$(".goToEditModalSchedLimitBtn").click(function(e){
		e.preventDefault();
			$('#viewSchedLimitModal').modal('toggle');
			$('#editSchedLimitModal').modal('show'); 
	});

	$("#edit_holiday-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_holiday',
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
					$('#editHolidayModal').modal('toggle'); //close modal
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

	$('.deleteHolidayBtn').click(function(e){
		e.preventDefault();

		var del_holiday_id = $(".del_holiday_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteHoliday',
			data:{'del_holiday_id':del_holiday_id},
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
					$('#deleteHolidayModal').modal('toggle'); //close modal
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