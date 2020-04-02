$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var pono = $("#pono_id_sec").data("pono");

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"purchase/PO_historyporeceiveview/table_purchase_order_view", // json datasource
			type: "post",  // method  , by default get
			data: {"pono" : pono},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();
});