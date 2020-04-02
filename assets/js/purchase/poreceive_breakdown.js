$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var pono = $("#pono_id_sec").data("pono");
	var itemid = $("#itemid").val();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_purchase/tbl_poreceive_breakdown", // json datasource
			type: "post",  // method  , by default get
			data: {"pono" : pono, "itemid": itemid},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();
	
	$('.search-input-text').on('keyup click', function(){   // for text boxes
		var i =$(this).attr('data-column');  // getting column index
		var v =$(this).val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	$('.search-input-select').on('change', function(){   // for select box
		var i =$(this).attr('data-column');  
		var v =$(this).val();  
		dataTable.columns(i).search(v).draw();
	});
});