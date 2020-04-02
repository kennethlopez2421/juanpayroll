$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	function fillDatatable(name, mainNav) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 5, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				url:base_url+"Main_dev_settings/content_navigation_table", // json datasource
				type: "post",  // method  , by default get
				data: { "name" : name, "mainNav" : mainNav },
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}
	
	fillDatatable("", "");

	$(".btnSearch").on("click", function(){
		name = $(".searchName").val();
		mainNav = $(".searchMainNav").val();

		fillDatatable(name, mainNav);
	});

	$(".saveBtnContentNavigation").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_content_navigation-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main_dev_settings/insert_content_navigation',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnArea").prop('disabled', true); 
				$(".saveBtnArea").text("Please wait...");
				
			},
			success:function(data){
				$(".cancelBtn, .saveBtnArea").prop('disabled', false);
				$(".saveBtnArea").text("Add Area");
				if (data.success == 1) {
                    $("#add_content_navigation-form").trigger("reset");
					fillDatatable("", ""); //refresh datatable
                    $(thiss).find('input').val(""); // clean fields
                    $('#addContentNavigationModal').modal('toggle'); //close modal
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
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white'        
					});
				}
			}
		});
	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$.LoadingOverlay("show");
	  	var id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_dev_settings/get_content_navigation',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$("#update_user_id").val(id);
	  				$("#update_url").val(res[0].cn_url);
	  				$("#update_name").val(res[0].cn_name);
	  				$("#update_description").val(res[0].cn_description);
                    $("#update_main_nav_category").val(res[0].cn_fkey).change();
                }
	  			else {
                    $("#update_url").val('');
	  				$("#update_name").val('');
	  				$("#update_description").val('');
                    $("#update_main_nav_category").val('').change();
	  			}
				$.LoadingOverlay("hide");
				$("#viewContentNavigationModal").modal("show");
	  		}
	  	});
	});

	$("#update_content_navigation-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main_dev_settings/update_content_navigation',
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
                    $("#update_content_navigation-form").trigger("reset");
					fillDatatable("", ""); //refresh table
					$('#viewContentNavigationModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white'        
					});
				}
			}
		});
	});
	
	$('#table-grid').delegate(".btnDelete","click", function(){
		$.LoadingOverlay("show");
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_dev_settings/get_content_navigation',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
  					$(".del_id").val(id);
					$(".del_name").text(res[0].cn_name);
	  			}
				$.LoadingOverlay("hide");
				$('#deleteContentNavigationModal').modal('show');
	  		}
	  	});

	});

	$('.deleteContentNavigationBtn').click(function(e){
		e.preventDefault();

		var del_id = $(".del_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main_dev_settings/delete_content_navigation',
			data:{'del_id':del_id},
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
					fillDatatable("", ""); //refresh table
					$('#deleteContentNavigationModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white'        
					});
				}
			}
		});

	});
	
});




