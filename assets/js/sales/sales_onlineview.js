$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var olsono = $("#olsono_id").val();
	var idno = $(".idno").val();

	var dataTable = $('#table-grid').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/table_salesonline_view", // json datasource
			type: "post",  // method  , by default get
			data: {"olsono" : olsono},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();

	$('.printBtn').click(function(e){

		var currUrl = window.location.href;

		currUrl = currUrl.replace("sales_drviews", "sales_drprint");
		window.open (currUrl, '_blank');

		$('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});
	
});