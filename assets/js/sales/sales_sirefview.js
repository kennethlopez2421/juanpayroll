$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var drno = $("#drno_id_sec").data("drno");
	var idno = $(".idno").val();

	var dataTable2 = $('#table-grid').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/table_salesinvoice_receipt_view", // json datasource
			type: "post",  // method  , by default get
			data: {"drno" : drno},
			beforeSend: function(){
				$.LoadingOverlay("show");
			},
			complete: function(){
				$.LoadingOverlay("hide");
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable2.destroy();

	var dataTable = $('#table-sales_return').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"language": {
    "zeroRecords": "No Sales Return Records"
  },

		"ajax":{
			url :base_url+"Main_sales/table_salesreturn_for_salesinvoice", // json datasource
			type: "post",  // method  , by default get
			data: {"drno" : drno},
			
			beforeSend: function(){
				$.LoadingOverlay("show");
			},
			complete: function(){
				$.LoadingOverlay("hide");
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				//$("#table-sales_return").append('<tbody class="table-grid-error"><tr><th colspan="3">No sales return has been made</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();

	var dataTable1 = $('#table-grid1').DataTable({
		
		"serverSide": true,
		"ajax":{
			url :base_url+"Main_sales/si_item_CollectionDetails", // json datasource
			type: "post",  // method  , by default get
			data:{'drno':drno},
			beforeSend: function() {
				$.LoadingOverlay("show");
			},
			complete: function() {
				$.LoadingOverlay("hide");
			},
			error: function(){  // error handling
				$(".table-grid1-error").html("");
				$("#table-grid1").append('<tbody class="table-grid1-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid1").css("display","none");
			}
		}
	});
	
	dataTable1.destroy();
	
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

	$(".printSalesOrder").click(function(e){
		e.preventDefault();
			window.location.href = ''+base_url+'Main_sales/salesorder_exportPDF/'+sono+'/'+idno;
	});
});