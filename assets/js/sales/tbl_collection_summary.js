$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	$("#searchfilter").change(function() {
		var search = $('#searchfilter').val(); // id ng dropdown
		var currentdate = new Date();

		if(search == "colnodiv"){
			$(".search_status").val("");
			$('.colnodiv').show('slow');	
			$('.datediv').hide('slow');
			$('.statusdiv').hide('slow');
			$(".search_colno").val("");	
		}
		else if(search == "datediv"){
			$('.colnodiv').hide('slow');
			$(".search_status").val("");	
			$('.datediv').show('slow');
			$('.statusdiv').hide('slow');
			$(".search_colno").val("");	
		}
		else if(search == "statusdiv"){
			$('.colnodiv').hide('slow');
			$(".search_colno").val("");	
			$('.datediv').show('slow');
			$('.statusdiv').show('slow');
		}
	});

	// 102318 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page
	// some functions are found at assets/js/globalfunctions.js

	function fillDatatable(search, datefrom, dateto, colno, paystatus) {
		var dataTable = $('#table-grid').DataTable({	
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 5 ], "className": "dt-center" }, { "orderable": false, "targets": [ 7 ], "className": "dt-center" }],
			"destroy": true,
			"ajax":{
				url :base_url+"sales/Sales_colhistory/tbl_collection_summary", // json datasource
				type: "post",  // method  , by default get
				data:{ 'search': search, 'datefrom': datefrom, 'dateto': dateto, 'colno': colno, 'paystatus': paystatus },
				beforeSend:function(data){
					$.LoadingOverlay("show"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				},
				complete: function(data) {
					$.LoadingOverlay("hide"); 
				}
			}
		});
	}

	search = $("#hdnSearch").text();

	if (search != "") {
		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		colno		= $("#hdnColno").text();
		paystatus	= $("#hdnPayStatus").text();

		$("#searchfilter").val(search).change();
		$("#date_from").val(datefrom);
		$("#date_to").val(dateto);
		$("#search_colno").val(colno);
		$("#search_status").val(paystatus).change();
	}
	else {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#date_from").val());
		dateto		= formatDate($("#date_to").val());
		colno		= $("#search_colno").val();
		paystatus	= $("#search_status").val();
	}

	fillDatatable(search, datefrom, dateto, colno, paystatus);

	$('#search_order').on('click', function (e) {
		e.preventDefault();
		
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#date_from").val());
		dateto		= formatDate($("#date_to").val());
		colno		= $("#search_colno").val();
		paystatus	= $("#search_status").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnColno").text(colno);
		$("#hdnPayStatus").text(paystatus);

		if(search == "datediv") {
			if(dateto != "" && datefrom != "") {
				fillDatatable(search, datefrom, dateto, colno, paystatus);
			}
			else {
				toastMessage("Note", "No date found. Please choose a date.", "error");
			}
		}
		else if(search == "colnodiv") {
			if (colno != "") {
				fillDatatable(search, datefrom, dateto, colno, paystatus);
			}
			else {
				toastMessage("Note", "No Collection Number found.", "error");
			}
		}
		else if(search == "statusdiv") {
			if(dateto != "" && datefrom != "" && paystatus != "") {
				fillDatatable(search, datefrom, dateto, colno, paystatus);
			}
			else {
				toastMessage("Note", "No Collection Status and date found.", "error");
			}
		}
	});

	// Storing session data for ease of navigation after clicking ID Number
	$("#table-grid").delegate( "#btnID", "click", function() {
		// for url
		url_colno = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		colno		= $("#hdnColno").text();
		paystatus	= $("#hdnPayStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_colhistory/storeSearchVariables',
			data:{ 'search': "Col|" + search, 'datefrom': datefrom, 'dateto': dateto, 'colno': colno, 'paystatus': paystatus },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/salesinvoice_collectiondetail_view/" + token + "/" + url_colno + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Allocate BUtton
	$("#table-grid").delegate( "#btnAllocate", "click", function() {
		// for url
		url_colno = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		colno		= $("#hdnColno").text();
		paystatus	= $("#hdnPayStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_colhistory/storeSearchVariables',
			data:{ 'search': "Col|" + search, 'datefrom': datefrom, 'dateto': dateto, 'colno': colno, 'paystatus': paystatus },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/collection_si_allocate/" + token + "/" + url_colno + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});
	
});