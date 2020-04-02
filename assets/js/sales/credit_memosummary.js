$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	// 102318 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page
	// some functions are found at assets/js/globalfunctions.js

	function fillDatatable(search, datefrom, dateto, cmno, status) {
		var dataTable = $('#table-grid').DataTable({
			destroy: true,
			"bServerSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 6 ], "className": "dt-center" }, {"orderable": false, "targets": [ 4 ],"className": "dt-center" }],
			"ajax":{
				url :base_url+"sales/Sales_cmhistory/tbl_creditmemo_summary", // json datasource
				type: "post",  // method  , by default get
				data:{ 'search': search, 'datefrom': datefrom, 'dateto': dateto, 'cmno': cmno, 'status': status },
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				error: function() {  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				},
				complete: function(data) {
					$.LoadingOverlay("hide"); 
				}
			}
		});
	}

	$("#searchfilter").change(function() {
		var searchtype = $('#searchfilter').val(); // id ng dropdown

		if(searchtype == "cmnodiv") {
			$(".search_status").val("").change();
			$('.cmnodiv').show('slow');	
			$('.datediv').hide('slow');
			$('.statusdiv').hide('slow');
			$(".search_cmno").val("");	

		}
		if(searchtype == "datediv") {
			$('.cmnodiv').hide('slow');
			$(".search_status").val("").change();	
			$('.datediv').show('slow');
			$('.statusdiv').hide('slow');
			$(".search_cmno").val("");	
		}
		if(searchtype == "statusdiv") {
			$('.cmnodiv').hide('slow');
			$(".search_cmno").val("");	
			$('.datediv').show('slow');
			$('.statusdiv').show('slow');
		}
	});
	
	search = $("#hdnSearch").text();

	if (search != "") {
		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		cmno		= $("#hdnCmno").text();
		status		= $("#hdnStatus").text();

		$("#searchfilter").val(search).change();
		$("#datefrom").val(datefrom);
		$("#dateto").val(dateto);
		$("#search_cmno").val(cmno);
		$("#search_status").val(status).change();
	}
	else {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		cmno		= $("#search_cmno").val();
		status		= $("#search_status").val();
	}

	fillDatatable(search, datefrom, dateto, cmno, status);

	$("#search_order").click(function() {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		cmno		= $("#search_cmno").val();
		status		= $("#search_status").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnCmno").text(cmno);
		$("#hdnStatus").text(status);

		if(search == "datediv") {
			if(dateto == "" && datefrom == "") {
				toastMessage('Note:', 'No date found. Please choose a date.', 'error');
			}
			else {
				fillDatatable(search, datefrom, dateto, cmno, status);
			}
		}
		else if(search == "cmnodiv") {
			if(cmno == "") {
				toastMessage('Note:', 'No ID number found. Please fill in data.', 'error');
			}
			else {
				fillDatatable(search, datefrom, dateto, cmno, status);
			}
		}
		else if(search == "statusdiv") {
			if(dateto == "" && datefrom == "" && status == "") {
				toastMessage('Note:', 'No date or status selected.', 'error');
			}
			else {
				fillDatatable(search, datefrom, dateto, cmno, status);
			}
		}
	});

	// Storing session data for ease of navigation after clicking ID Number
	$("#table-grid").delegate( "#btnID", "click", function() {
		// for url
		url_cmno = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		cmno		= $("#hdnCmno").text();
		status		= $("#hdnStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_cmhistory/storeSearchVariables',
			data:{ 'search': "CM|" + search, 'datefrom': datefrom, 'dateto': dateto, 'cmno': cmno, 'status': status },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/credit_memoview/" + token + "/" + url_cmno + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Allocate BUtton
	$("#table-grid").delegate( "#btnAllocate", "click", function() {
		// for url
		url_cmno = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		cmno		= $("#hdnCmno").text();
		status		= $("#hdnStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_cmhistory/storeSearchVariables',
			data:{ 'search': "CM|" + search, 'datefrom': datefrom, 'dateto': dateto, 'cmno': cmno, 'status': status },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/credit_memoallocate/" + token + "/" + url_cmno + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});
	
});