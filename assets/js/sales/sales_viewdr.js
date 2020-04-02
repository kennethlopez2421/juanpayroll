$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var drno = $("#drno_id_sec").data("drno");
	var idno = $(".idno").val();
	var tokken = $("#hdnTokken").val();

	var dataTable = $('#table-grid').DataTable({
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"sales/Sales_drview/table_deliveryreceipt_view", // json datasource
			type: "post",  // method  , by default get
			data: {"drno" : drno},
			beforeSend: function() {
				$.LoadingOverlay("show");
			},
			complete: function() {
				$.LoadingOverlay("hide");
			},
			error: function() {  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	// dataTable.destroy();

	$('.printBtn').click(function(e) {
		window.open(base_url+"sales/Sales_drview/sales_drprint/"+tokken+"/"+drno+"/"+idno, '_blank');
	});
	
});