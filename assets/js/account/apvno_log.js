$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	
	var apvno = $('.apvno').text();
	
	dataTable = $('#table-grid2').DataTable({
		destroy: true,
		"serverSide": true,
		"columnDefs": [{ "orderable": false, "targets": [ 0, 1, 2, 3, 4 ], "className": "text-left" }],
		"ajax":{
			url:base_url+"account/APV_list/get_apv_log", // json datasource
			type: "post",  // method  , by default get
			data:{"apvno": apvno},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('.printBtn').click(function(e){
		var currUrl = window.location.href;

		currUrl = currUrl.replace("apvno_log", "apv_print");
		window.open (currUrl, '_blank');

		$('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});

});
