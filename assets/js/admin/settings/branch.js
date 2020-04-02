$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/settings_branch_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	///	josh

	$(".saveBtnAccounts").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_branchinfo-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_info_branch',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnAccounts").prop('disabled', true); 
				$(".saveBtnAccounts").text("Please wait...");
				
			},
			success:function(data){
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
					dataTable.draw(); //refresh table
					$('#addAccountsModal').modal('toggle'); //hide modal
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
		var branch_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoBranchUsingID',
	  		data:{'branch_id':branch_id},
	  		success:function(data){
	  			
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".del_branch_id").val(res[0].branch_id); //user_id pk
	  			}
	  		}
	  	});
		$('#deleteAccountsModal').modal('show');

	});

	$('#table-grid').delegate(".btnUpdate", "click", function(){
		$('#editAccountsModal').modal('show'); 
	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewAccountsModal').modal('show');
	});

	$('#table-grid').delegate(".btnUpdate, .btnView", "click", function(){


	  	var branch_id = $(this).data('value');
	  	console.log(branch_id)

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoBranchUsingID',
	  		data:{'branch_id':branch_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				dataTable.draw(); //refresh table
	  				$(".branch_id").val(res[0].branch_id); //user_id pk

	  					$(".info_bname").val(res[0].branch_name);
		  				$(".info_bcode").val(res[0].branch_code);
						$(".info_address").val(res[0].branch_address);						
	  			}
	  		}
	  	});
	});

	$(".goToEditModalAccountsBtn").click(function(e){
		e.preventDefault();
			$('#viewAccountsModal').modal('toggle');
			$('#editAccountsModal').modal('show'); 
	});

	$("#edit_view_branchinfo-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_branch',
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


	$('.deleteAccountBtn').click(function(e){
		e.preventDefault();

		var del_branch_id = $(".del_branch_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteBranch',
			data:{'del_branch_id':del_branch_id},
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

	$('.btnClickAddAccount').click(function(){
		$('.info_position').val('').trigger('change');
	});
	
});