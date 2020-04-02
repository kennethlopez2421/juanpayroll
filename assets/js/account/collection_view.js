$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var drpayno = $('.drpayno').val();

	dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,		
		"ajax":{
			url:base_url+"Main_account/collection_view_table", // json datasource
			type: "post",  // method  , by default get
			data:{"drpayno": drpayno},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	dataTable2 = $('#table-grid2').DataTable({
		"processing": true,
		"serverSide": true,		
		"ajax":{
			url:base_url+"Main_account/collection_view_table2", // json datasource
			type: "post",  // method  , by default get
			data:{"drpayno": drpayno},
			error: function(){  // error handling
				$(".table-grid2-error").html("");
				$("#table-grid2").append('<tbody class="table-grid2-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid2_processing").css("display","none");
			}
		}
	});
	dataTable.destroy();
	dataTable2.destroy();


});
