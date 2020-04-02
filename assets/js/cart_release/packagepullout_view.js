$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	
	var ppfno = $('.prv_ppfno').text();
	
	dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,		
		"ajax":{
			url:base_url+"Main_cart/get_ppt_history_view", // json datasource
			type: "post",  // method  , by default get
			data:{"ppfno": ppfno},
			beforeSend:function(data){
				$("#table-grid").LoadingOverlay("show");
			},
			complete:function(){
				$("#table-grid").LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});
});
