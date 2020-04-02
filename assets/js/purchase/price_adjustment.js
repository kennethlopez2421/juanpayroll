$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"bDeferRender": true,
		"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 5 ], "className": "dt-center" }],
		"ajax":{
			url :base_url+"Main_purchase/table_priceadjustment_summary", // json datasource
			type: "post",  // method  , by default get
			data:{'username':username},
			beforeSend:function(data)
            {
                $("#table-grid").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("#table-grid").LoadingOverlay("hide"); 
            },
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	var oTable = $('#table-grid').dataTable();
		$("#search_order").click(function() {
			oTable.fnFilter( $(".search-input-select1").val(), '0' );
		});


});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}