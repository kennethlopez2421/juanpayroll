$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var sono = $("#sono_id_sec").data("sono");
	var idno = $(".idno").val();

	var dataTable = $('#table-grid').DataTable({
		// 
		"serverSide": true,
		"destroy":true,
		"columnDefs": [
			{ targets: [2, 3, 4, 5], orderable: false}
		],
		"ajax":{
			url :base_url+"Main_sales/table_salesorder_view_j", // json datasource
			type: "post",  // method  , by default get
			data: {"sono" : sono},
			beforeSend:function(data){
				$.LoadingOverlay("show"); 
			},
			complete: function(data)
			{
				$.LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
			}
		},
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

	$('.printBtn').click(function(e){

		var currUrl = window.location.href;

		currUrl = currUrl.replace("salesorder_view", "salesorder_print");
		window.open (currUrl, '_blank');

		// $('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});

	$('.printBtn1').click(function(e){

		var currUrl = window.location.href;

		currUrl = currUrl.replace("salesorder_view_in_DR", "salesorder_print");
		window.open (currUrl, '_blank');

		// $('.printBtn1').attr("disabled","true");
		$('.printBtn1').attr("title","This document has already been printed.");
	});
});