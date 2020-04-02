$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var rcvno = $("#rcvno_id_sec").data("rcvno");
	var pono = $("#pono").val();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"purchase/POR_historyreceiveview/tble_rcvpo_usingrcvno", // json datasource
			type: "post",  // method  , by default get
			data: {"rcvno" : rcvno},
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

	$('.printBtn').click(function(e) {
		var currUrl = window.location.href;

		currUrl = currUrl.replace("receivepo_usingrcvno", "receivepo_usingrcvno_print");
		window.open (currUrl, '_blank');

		$('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});

});