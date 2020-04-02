$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url:base_url+"Main_entity/supplier_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('.search-input-text').on('keyup click', function(){   // for text boxes
		var i =$(this).attr('data-column');  // getting column index
		var v =$(this).val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	$('.search-input-select').on('change', function(){   // for select box
		var i =$(this).attr('data-column');  
		var v =$(this).val();  
		dataTable.columns(i).search(v).draw();
	});

	$(".btnClickAddSupplier").click(function(e){
		var thiss = $('#add_supplier-form'); // target form
		$(thiss).find('input').val(""); // clean fields 
		$(thiss).find("input:checkbox").prop("checked", false); //unchecked checkbox

	});

	$("#add_supplier-form").submit(function(e){
		e.preventDefault();

		//var thiss = $("#add_supplier-form");

		//var serial = thiss.serialize();

		var form = $('#add_supplier-form');
		//requestUrlRoot = form.attr('action');
		
		postdata = new FormData(form[0]);

		$.ajax({				
			url: base_url+'Main_entity/insert_supplier',
			type: 'post',
			data: postdata,
			contentType: false,   
			cache: false,      
			processData:false,
			success : function(data){
				data = JSON.parse(data);

				if(data.valid == true){
					$('#add_supplier-form')[0].reset();
					$('#addSupplierModal').modal('hide');

					$.toast({
						heading: 'Success',
						text: data.message,
						icon: 'success',
						loader: false,  
						stack: false,
						position: 'top-center', 
						allowToastClose: false,
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});
				}
				else{
					$.toast({
						heading: 'Error',
						text: data.message,
						icon: 'error',
						loader: false,  
						stack: false,
						position: 'top-center', 
						allowToastClose: false,
						bgColor: '#d9534f',
						textColor: 'white'  
					});
				}	
			},
			error : function(error){
				$.toast({
					heading: 'Error',
					text: data.message,
					icon: 'error',
					loader: false,  
					stack: false,
					position: 'top-center', 
					allowToastClose: false,
					bgColor: '#d9534f',
					textColor: 'white'  
				});
			}
		});
	});

/*	$(".saveBtnSupplier").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_supplier-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main_entity/insert_supplier',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnSupplier").prop('disabled', true); 
				$(".saveBtnSupplier").text("Please wait...");
				
			},
			success:function(data){
				$(".cancelBtn, .saveBtnSupplier").prop('disabled', false);
				$(".saveBtnSupplier").text("Add Supplier");
				if (data.success == 1) {
					dataTable.draw(); //refresh datatable
					$(thiss).find('input').val(""); // clean fields

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
	});*/

	$('#table-grid').delegate(".btnView", "click", function(){
	  	var id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_entity/get_supplier',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".info_id").val(id);
	  				$(".info_name").val(res[0].suppliername);
	  				$(".info_contactno").val(res[0].contactno);
	  				$(".info_contactperson").val(res[0].contactperson);
	  				$(".info_address").val(res[0].address);
	  				$(".info_other").val(res[0].otherinf);
	  				$(".info_term").val(res[0].credit).trigger('change');
	  			}
	  			else {
	  				$(".info_desc").val('');
	  				$(".info_name").val('');
	  				$(".info_contactno").val('');
	  				$(".info_contactperson").val('');
	  				$(".info_address").val('');
	  				$(".info_other").val('');
	  				$(".info_term").val('').trigger('change');
	  			}
	  		}
	  	});
	});

	$("#update_supplier-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main_entity/update_supplier',
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
					$('#viewSupplierModal').modal('toggle'); //close modal
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
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_entity/get_supplier',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
  					$(".del_id").val(id);
					$(".info_name").text(res[0].suppliername);
	  			}
	  		}
	  	});
		$('#deleteSupplierModal').modal('show');

	});

	$('.deleteSupplierBtn').click(function(e){
		e.preventDefault();

		var del_id = $(".del_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main_entity/delete_supplier',
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
					dataTable.draw(); //refresh table
					$('#deleteSupplierModal').modal('toggle'); //close modal
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