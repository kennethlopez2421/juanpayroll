$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var drno = $('#drno').val();
	

	dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,		
		"ajax":{
			url:base_url+"Main_account/bct_history_reference_table2", // json datasource
			type: "post",  // method  , by default get
			data:{"drno": drno},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});
	dataTable.destroy();
    
    $('.printBtn').click(function(e){

		var currUrl = window.location.href;

		currUrl = currUrl.replace("bct_history_view", "bct_history_print");
		window.open (currUrl, '_blank');

		$('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});

});
