$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	// var pono = $("#pono_id_sec").data("pono");
	var pono = $("#pono").val();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"destroy":true,
		"bDeferRender": true,
		"ajax":{
			url :base_url+"Main_account/table_poreceipt_view", // json datasource
			type: "post",  // method  , by default get
			data: {"pono" : pono},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();
});

