$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var sino = $("#sino_id").val();
	var idno = $(".idno").val();

	var dataTable = $('#table-grid').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"columnDefs": [{ "orderable": false, "targets": [2, 3, 4, 5]}],
		"ajax":{
			url :base_url+"Main_sales/table_salesinvoice_view", // json datasource
			type: "post",  // method  , by default get
			data: {"sino" : sino},
			beforeSend:function(data){
				$.LoadingOverlay("show"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
			},complete: function(data){
				$.LoadingOverlay("hide"); 
			}
		},
	});

	// dataTable.destroy();

	$('.printBtn').click(function(e){

		var currUrl = window.location.href;

		currUrl = currUrl.replace("sales_invoiceview", "sales_invoiceprint");
		window.open (currUrl, '_blank');
		$('.printBtn').attr("title","This document has already been printed.");
	});
	
});