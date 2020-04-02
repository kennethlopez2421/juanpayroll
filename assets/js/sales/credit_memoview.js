$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var collectionid = $("#collection_id_sec").data("id");
	var drno = $(".drno").val();
	var drpayno = $(".drpayno").val();

	var dataTable = $('#table-grid').DataTable({	
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/tbl_creditcollectiondetailed", // json datasource
			type: "post",  // method  , by default get
			data: {"collectionid" : collectionid},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();
		var dataTable1 = $('#table-drcollection').DataTable({
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/tbl_creditcollection_view", // json datasource
			type: "post",  // method  , by default get
			data: {"drpayno" : drpayno},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
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

	currUrl = currUrl.replace("collectiondetail_view", "collectiondetail_print");
	window.open (currUrl, '_blank');

	$('.printBtn').attr("disabled","true");
	$('.printBtn').attr("title","This document has already been printed.");
});

});


