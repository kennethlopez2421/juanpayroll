$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang

	searchToday();
	function searchToday(){ //for date today search first
		var dataTable = $('#table-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"bDeferRender": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 9 ], "className": "dt-center" }, { "orderable": false, "targets": [ 4 ], "className": "dt-center" }],
			"ajax":{
				url :base_url+"Main_purchase/tbl_po_payment", // json datasource
				type: "post",  // method  , by default get
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

		var i = $(".searchDateFrom").attr('data-column');  // getting column index
		var v = $(".searchDateFrom").val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	}

		$("#sosearchfilter").change(function() {
		var searchtype = $('#sosearchfilter').val(); // id ng dropdown
		var currentdate = new Date();
		   if(searchtype == "ponodiv")
	       {
	       	 $("#date1").val("");
			 $("#date2").val("");
			 $(".search_status").val("");
			 $('.ponodiv').show('slow');	
			 $('.podatediv').hide('slow');
			 $('.ponostatus').hide('slow');
			 $('.search_po_btn').show('slow');

		   }
		   if(searchtype == "podatediv")
	       {
	       	$(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
			 $(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
			 $('.ponodiv').hide('slow');
			 $(".search_status").val("");	
			 $('.podatediv').show('slow');
			 $(".search_pono").val("");
			 $('.ponostatus').hide('slow');
			 $('.search_po_btn').show('slow');
		   }
		   if(searchtype == "ponostatus")
	       {
	       	$(".searchDateTo").val("");
			 $(".searchDateFrom").val("");
			 $('.ponodiv').hide('slow');	
			 $('.podatediv').hide('slow');
			 $('.ponostatus').show('slow');
			 $('.search_po_btn').show('slow');
		   }
	});
	// searchToday();
	// function searchToday(){ //for date today search first

	// 	var i = $(".searchDateTo").attr('data-column');  // getting column index
	// 	var v = $(".searchDateTo").val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();

	// 	var i = $(".searchDateFrom").attr('data-column');  // getting column index
	// 	var v = $(".searchDateFrom").val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// }

	var oTable = $('#table-grid').dataTable();
		$("#search_order").click(function() {

			var selectedSearchOption = $('#sosearchfilter').find(":selected").val();
			var poNo = $('#ponoField').val();

			if(selectedSearchOption == "ponodiv") {
				 if(poNo == "") {
						$.toast({
						    heading: 'Error',
						    text: 'Check# must not be empty',
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: 'red',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 4000
						});
				 }else {
					oTable.fnFilter( $(".search-input-select1").val(), '0' );
					oTable.fnFilter( $(".search-input-select2").val(), '1' );
					oTable.fnFilter( $(".search_pono").val(), '2' );
					oTable.fnFilter( $("#search_status").val(), '3' );
					//oTable.fnFilter( $("#input3").val(), '4' );				 	
				 }
			}else {

				oTable.fnFilter( $(".search-input-select1").val(), '0' );
				oTable.fnFilter( $(".search-input-select2").val(), '1' );
				oTable.fnFilter( $(".search_pono").val(), '2' );
				oTable.fnFilter( $("#search_status").val(), '3' );
				//oTable.fnFilter( $("#input3").val(), '4' );
			}
		});
});