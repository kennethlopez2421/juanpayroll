$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var drretno = $("#drretno_id_sec").data("drretno");
	var drno = $(".drno").val();

	var dataTable = $('#table-grid').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/table_salesreturn_view", // json datasource
			type: "post",  // method  , by default get
			data: {"drretno" : drretno},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();

	var dataTable1 = $('#table-drsales').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/tbl_drsalesretun_view", // json datasource
			type: "post",  // method  , by default get
			data: {"drretno" : drretno},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable1.destroy();

	$('.printBtn').click(function(e){
	 	e.preventDefault();
	    $('#printDivsRet1').attr('class','hidden'); 
	    var currUrl = window.location.href;

	    $.ajax({
	        type: "POST",
	        url:base_url+'Main_sales/print_salesreturn',
	        data: {"drretno" : drretno},
	        cache: false,
	        beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
	        success:function(data){
	            if(data.success == 1) {
					currUrl = currUrl.replace("salesreturn_view", "salesreturn_print");
					window.open (currUrl, '_blank');
	                $(document).ready(function() {
	                    location.reload();
	                }); 
	            }

	            $('#printDivsret2').removeAttr('class','hidden');   
	        }
	    });  
	});

});