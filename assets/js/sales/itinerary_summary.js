$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	$("#searchfilter").change(function() {
		var searchtype = $('#searchfilter').val(); // id ng dropdown

		if(searchtype == "datediv") {
			$('.datediv').show('slow');
			$('.truckdiv').hide('slow');
			$("#search_shipping").val("").change();
		}
		if(searchtype == "truckdiv") {
			$('.datediv').show('slow');
			$('.truckdiv').show('slow');
		}
	});

	// 102318 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page
	// some functions are found at assets/js/globalfunctions.js

	function fillDatatable(search, datefrom, dateto, truck) {
		var dataTable = $('#table-grid').DataTable({	
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ targets: [3], orderable: false, "className": "dt-center"}, { targets: [2], orderable: false, "className": "dt-center"}],
			"destroy": true,
			"ajax":{
				url :base_url+"sales/Sales_irs/tble_itinerary_summary", // json datasource
				type: "post",  // method  , by default get
				data:{ 'search': search, 'datefrom': datefrom, 'dateto': dateto, 'truck': truck },
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
		truck		= $("#hdnTruck").text();

		$("#searchfilter").val(search).change();
		$("#datefrom").val(datefrom);
		$("#dateto").val(dateto);
		$("#search_shipping").val(truck).change();
	}
	else {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		truck		= $("#search_shipping").val();
	}

	fillDatatable(search, datefrom, dateto, truck);

	$("#search_order").click(function() {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		truck		= $("#search_shipping").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnTruck").text(truck);

		if(search == "datediv"){
			if (dateto == "" || datefrom == "") {
				toastMessage('Note:', 'No date found. Please choose a date.', 'error');
			}
			else {
				fillDatatable(search, datefrom, dateto, truck);
			}
		}

		else if(search == "truckdiv") {
			if (truck == "") {
				toastMessage('Note:', 'No Truck selected. Please select a Truck.', 'error');
			}
			else {
				fillDatatable(search, datefrom, dateto, truck);
			}
		}
	});

	// Storing session data for ease of navigation after clicking Report Button
	$("#table-grid").delegate( "#btnReport", "click", function() {
		// for url
		url_itno = $(this).data("value");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		truck		= $("#hdnTruck").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_irs/storeSearchVariables',
			data:{ 'search': "IRS|" + search, 'datefrom': datefrom, 'dateto': dateto, 'truck': truck },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/itnerary_report/" + token + "/" + url_itno, '_self');
				// href=' . base_url('Main_sales/itnerary_report/' . $token . '/' . $row["itno"]) . ' 
				$.LoadingOverlay("hide");
			}
		});
	});
	
});