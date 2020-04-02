$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var pkgid = $('#pkgid').text();
	var type = $('#type').text();

	data = [];

	function reset() {
		$('#am_item').val("").change();
		$('#am_qty').val("");
		$('#am_unit').val("").change();
	}
	
	dataTable = $('#table-grid').DataTable({
		destroy: true,
		"serverSide": true,		
		"ajax":{
			url:base_url+"Main_cart/get_package_items", // json datasource
			type: "post",  // method  , by default get
			data:{"pkgid": pkgid, "view_type": type},
			beforeSend:function(data) {
				$("#table-grid").LoadingOverlay("show");
			},
			complete:function() {
				$("#table-grid").LoadingOverlay("hide"); 
				console.log(type);
			},
			error: function() {  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('.saveItemBtn').click(function(event){
		event.preventDefault();

		itemid = $('#am_item').val();
		qty = $('#am_qty').val();
		unit = $('#am_unit').val();

		if(itemid != "" || qty != "" || unit != "") {
			data = {
				pkgid: pkgid,
				itemid: itemid,
				qty: qty,
				unit: unit
			}

			$.ajax({
		  		url: base_url+"Main_cart/add_item",
	            type: 'post',
				data: {'data':data},
		  		beforeSend:function(data){
					$.LoadingOverlay("show");
				},
		  		success:function(data){
		  			if (data.success == 1) {
						$.toast({
						    heading: 'Success',
						    text: 'You have successfully saved the record.',
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});
					 	setTimeout(function(){
							$.LoadingOverlay("hide");
						},500);

						reset();
						$('#addRowModal').modal('hide');
						dataTable.draw();
		  			}
		  		}
	  		});
		}
		else {
			$.toast({
			    heading: 'Warning',
			    text: 'Please fill out all required fields',
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#ffc107',
				textColor: 'white'  
			});
		}
	});

	$('#table-grid').delegate(".btnEdit", "click", function(){
	  	$("#em_item").val($(this).data('itemname'));
	  	$("#em_id").val($(this).data('id'));
	  	$("#em_qty").val($(this).data('qty'));
	  	$("#em_unit").val($(this).data('unit')).change();
	});

	$('.em_saveEditBtn').click(function(e) {

		id = $("#em_id").val();
		qty = $('#em_qty').val();
		unit = $('#em_unit').val();

		if(qty != "" || unit != "") {
			data = {
				id: id,
				qty: qty,
				unit: unit
			}

			$.ajax({
		  		url: base_url+"Main_cart/update_item",
	            type: 'post',
				data: {'data':data},
		  		beforeSend:function(data){
					$.LoadingOverlay("show");
				},
		  		success:function(data){
		  			if (data.success == 1) {
						$.toast({
						    heading: 'Success',
						    text: 'You have successfully edited the record.',
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#5cb85c',
							textColor: 'white'  
						});
					 	setTimeout(function(){
							$.LoadingOverlay("hide");
						},500);

						$('#editModal').modal('hide');
						dataTable.draw();
		  			}
		  		}
	  		});
		}
		else {
			$.toast({
			    heading: 'Warning',
			    text: 'Please fill out all required fields',
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#ffc107',
				textColor: 'white'  
			});
		}
	});

	$('#table-grid').delegate(".btnDelete", "click", function(){
		$(".dm_item").html($(this).data('itemname'));
	  	$(".dm_id").val($(this).data('id'));
	});

	$('.dm_deleteBtn').click(function(e) {
		var id = $(".dm_id").val();

		$.ajax({
	  		url: base_url+"Main_cart/delete_item",
            type: "post",
			data: {'id':id},
	  		beforeSend:function(data){
				$.LoadingOverlay("show");
			},
	  		success:function(data){
	  			if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: 'You have successfully deleted the package.',
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});
				 	setTimeout(function(){
						$.LoadingOverlay("hide");
						$('#deleteModal').modal("hide");
				 		dataTable.draw();
					},500);
	  			}
	  		}
  		});
	});

});
