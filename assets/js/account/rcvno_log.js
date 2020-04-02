$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var apvno = $('.apvno').text();
	var rcvno = $('.rcvno').text();
	var pono = $('.pono').text();
	var item_id = $(".update").data('value');

	dataTable = $('#table-grid2').DataTable({
		"processing": true,
		"serverSide": true,		
		"ajax":{
			url:base_url+"Main_account/get_rcvno_log", // json datasource
			type: "post",  // method  , by default get
			data:{"pono": pono},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('#table-grid2').delegate(".itemid", "click", function(){
	  	var itemid = $(this).data('value');

	  	dataTable2 = $('#table-grid').DataTable({
	  		destroy: true,
			"processing": true,
			"serverSide": true,		
			"ajax":{
				url:base_url+"Main_account/get_rcvno_breakdown", // json datasource
				type: "post",  // method  , by default get
				data:{"pono": pono, "itemid": itemid},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});

		dataTable2.destroy();
	});

});
