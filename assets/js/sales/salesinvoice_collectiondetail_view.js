$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var collectionid = $("#collection_id_sec").data("id");
	var drno = $(".drno").val();
	var drpayno = $(".drpayno").val();

	var dataTable = $('#table-grid').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/tbl_collectiondetailed", // json datasource
			type: "post",  // method  , by default get
			beforeSend:function(data){
				$.LoadingOverlay("show"); 
			},
			data: {"collectionid" : collectionid},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			},
			complete:function(){
				$.LoadingOverlay("hide");
			}
		},
	});

	var dataTable1 = $('#table-drcollection').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/tbl_sicollection_view", // json datasource
			type: "post",  // method  , by default get
			beforeSend:function(data){
				$('#table-drcollection').LoadingOverlay("show"); 
			},
			data: {"drpayno" : drpayno},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			},
			complete:function(){
				$('#table-drcollection').LoadingOverlay("hide");
			}
		},
	});

	dataTable1.destroy();
	
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

	$(".printSalesOrder").click(function(e){
		e.preventDefault();

		var sono = $(".sono_id_sec").val();

		window.location.href = ''+base_url+'Main/salesorder_exportPDF/'+sono;
	});

	$('.printBtn').click(function(e){

		var currUrl = window.location.href;

		currUrl = currUrl.replace("salesinvoice_collectiondetail_view", "collectiondetail_print");
		window.open (currUrl);
	});

});


