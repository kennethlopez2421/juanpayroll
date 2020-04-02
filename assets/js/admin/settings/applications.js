$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/settings_applications_table", // json datasource
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
			url :base_url+"Main/settings_applications_table2", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid2").append('<tbody class="table-grid2-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$(".saveBtnAccounts").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_applicationnfo-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_info_application',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnAccounts").prop('disabled', true); 
				$(".saveBtnAccounts").text("Please wait...");
				
			},
			success:function(data){
				dataTable.draw(); //refresh table
				$(".cancelBtn, .saveBtnAccounts").prop('disabled', false);
				$(".saveBtnAccounts").text("Add");
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
					$('#addAccountsModal').modal('toggle'); //close modal
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

	$(".saveBtnReq").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_reqinfo-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_info_requirement',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnReq").prop('disabled', true); 
				$(".saveBtnReq").text("Please wait...");
				
			},
			success:function(data){
				dataTable.draw(); //refresh table
				$(".cancelBtn, .saveBtnAccounts").prop('disabled', false);
				$(".saveBtnAccounts").text("Add");
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
					$('#addRequirementModal').modal('toggle'); //close modal
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
	
	$('#table-grid').delegate(".btnDelete","click", function(){
		var application_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoApplicationUsingID',
	  		data:{'application_id':application_id},
	  		success:function(data){
	  			
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".del_application_id").val(res[0].application_id); //user_id pk
	  			}
	  		}
	  	});
		$('#deleteAccountsModal').modal('show');

	});

	$('#table-grid2').delegate(".btnDelete2","click", function(){

		var requirement_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoRequirementUsingID',
	  		data:{'requirement_id':requirement_id},
	  		success:function(data){
	  			
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".del_requirement_id").val(res[0].requirement_id); //user_id pk
	  			}
	  		}
	  	});
		$('#deleteRequirementModal').modal('show');

	});

	$('#table-grid').delegate(".btnView", "click", function(e){

		$('#viewAccountsModal').modal('show');
	});

	$('#table-grid').delegate(".btnUpdate, .btnView", "click", function(){

	  	var application_id = $(this).data('value');
	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoApplicationUsingID',
	  		data:{'application_id':application_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				dataTable.draw(); //refresh table
	  				$(".application_id").val(res[0].application_id); //user_id pk

	  					var a = accounting.formatMoney(res[0].process_fee,"₱",2);
	  					var b = accounting.formatMoney(res[0].bid_fee,"₱",2);

	  					$(".info_aname").val(res[0].description);
						$(".info_pfee").val(res[0].process_fee);
		  				$(".info_bfee").val(res[0].bid_fee);
		  				$(".view_info_pfee").val(a);
		  				$(".view_info_bfee").val(b);
		  				

	  			}
	  		}
	  	});
	});

	$('#table-grid2').delegate(".btnUpdate, .btnView2", "click", function(){

	  	var requirement_id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoRequirementUsingID',
	  		data:{'requirement_id':requirement_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				dataTable2.draw(); //refresh table

	  					$(".info_req_id").val(res[0].requirement_id);
		  				$(".info_req_desc").val(res[0].requirement_description);
					
	  			}
	  		}
	  	});
	});

	$(".goToEditModalAccountsBtn").click(function(e){
		e.preventDefault();
			$('#viewAccountsModal').modal('toggle');
			$('#editAccountsModal').modal('show'); 
	});

	$(".goToEditModalReqBtn").click(function(e){
		e.preventDefault();
			$('#viewRequirementModal').modal('toggle');
			$('#editRequirementModal').modal('show'); 
	});

	$("#edit_view_applicationinfo-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_application',
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
					$('#editAccountsModal').modal('toggle'); //close modal
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

	$("#edit_view_requirementinfo-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_requirement',
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
					$('#editRequirementModal').modal('toggle'); //close modal
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



	$('.deleteAccountBtn').click(function(e){
		e.preventDefault();

		var del_application_id = $(".del_application_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteApplication',
			data:{'del_application_id':del_application_id},
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
					$('#deleteAccountsModal').modal('toggle'); //close modal
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

	$('.deleteReqBtn').click(function(e){
		e.preventDefault();

		var del_requirement_id = $(".del_requirement_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteRequirement',
			data:{'del_requirement_id':del_requirement_id},
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
					$('#deleteRequirementModal').modal('toggle'); //close modal
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
	
});