$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	table_bank("", "");
	function table_bank(i,v){

		var dataTable = $('#table-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"destroy": true,
			"ajax":{
				url:base_url+"Main_purchases/receive_po_table", // json datasource
				type: "post",  // method  , by default get
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
		setTimeout(function(){
			dataTable.columns(i).search(v).draw();	

		},200);
	}
	function table_bank2(i,v){

		var dataTable = $('#table-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"destroy": true,
			"ajax":{
				url:base_url+"Main_purchases/receive_po_table", // json datasource
				type: "post",  // method  , by default get
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}
	$("#searchBtn").click(function(){
		var searchFrom = $(".searchFrom").attr('data-column'); 
		var searchFromVal = $(".searchFrom").val();
		// var searchTo = $(".searchTo").attr('data-column'); 
		// var searchToVal = $(".searchTo").val();
		table_bank(searchFrom, searchFromVal);
	});

	$('.search-input-text').on('keyup click', function(){   // for text boxes
		var i =$(this).attr('data-column');  // getting column index
		var v =$(this).val();  // getting search input value
		// dataTable.columns(i).search(v).draw();
		table_bank2(i, v);
	});

	$('.search-input-select').on('change', function(){   // for select box
		alert("change");
		var i =$(this).attr('data-column');  
		var v =$(this).val();  
		// dataTable.columns(i).search(v).draw();
		// table_bank(i, v);
	});

	$(".btnClickAddReceivePO").click(function(e){
		var thiss = $('#add_receive_po-form'); // target form
		$(thiss).find('input').val(""); // clean fields 
		$(thiss).find("input:checkbox").prop("checked", false); //unchecked checkbox

	});

	$(".saveBtnReceivePO").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_receive_po-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main_purchases/insert_receive_po',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnReceivePO").prop('disabled', true); 
				$(".saveBtnReceivePO").text("Please wait...");
				
			},
			success:function(data){
				$(".cancelBtn, .saveBtnReceivePO").prop('disabled', false);
				$(".saveBtnReceivePO").text("Add Receive Purchase Order");
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
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_purchases/get_receive_po',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".info_id").val(id);
	  				$(".info_desc").val(res[0].description);
	  			}
	  			else {
	  				$(".info_desc").val('');
	  			}
	  		}
	  	});
	});

	$("#update_receive_po-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main_purchases/update_receive_po',
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
					$('#viewReceivePOModal').modal('toggle'); //close modal
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
	  		url: base_url+'Main_purchases/get_receive_po',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
  					$(".del_id").val(id);
					$(".info_desc").text(res[0].description);
	  			}
	  		}
	  	});
		$('#deleteReceivePOModal').modal('show');

	});

	$('.deleteReceivePOBtn').click(function(e){
		e.preventDefault();

		var del_id = $(".del_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main_purchases/delete_receive_po',
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
					$('#deleteReceivePOModal').modal('toggle'); //close modal
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