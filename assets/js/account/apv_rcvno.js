$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var pono = $('.pono').text();

	dataTable = $('#table-grid2').DataTable({
		destroy: true,
		"serverSide": true,	
		"columnDefs": [{ "orderable": false, "targets": [ 7 ], "className": "dt-center" }],	
		"ajax":{
			url:base_url+"account/APV/get_rcvno_log", // json datasource
			type: "post",  // method  , by default get
			data:{"pono": pono},
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

	$('#table-grid2').delegate("#btnView", "click", function(){
	  	var itemid = $(this).data('value');

	  	dataTable2 = $('#table-grid').DataTable({
	  		destroy: true,
			"serverSide": true,		
			"ajax":{
				url:base_url+"account/APV/get_rcvno_breakdown", // json datasource
				type: "post",  // method  , by default get
				data:{"pono": pono, "itemid": itemid},
				beforeSend:function(data) {
				  	$.LoadingOverlay("show"); 
				},
				complete: function() {
				  	$.LoadingOverlay("hide");
					$('#viewModal').modal('show');
				},
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
