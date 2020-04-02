$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	$("#sisearchfilter").change(function() {
		
		var searchtype = $('#sisearchfilter').val(); // id ng dropdown

		$("#date_to").datepicker("setDate", new Date());
		$("#date_from").datepicker("setDate", new Date());

		if(searchtype == "sinodiv") {
			$('.sidatediv').hide('slow');
			$('.sinodiv').show('slow');	
			$('.searchbyName').hide('slow');
			$('.sistatus').hide('slow');

			$("#search_status").val("");
			$("#search_customer").val("");	
		}
		if(searchtype == "sidatediv") {
			$('.sinodiv').hide('slow');
			$('.sidatediv').show('slow');
			$('.searchbyName').hide('slow');
			$('.sistatus').hide('slow');

			$("#search_customer").val("");	
			$("#search_status").val("");	
			$("#search_sino").val("");	
		}
		if(searchtype == "sistatus") {
			$('.sidatediv').show('slow');
			$('.sistatus').show('slow');
			$('.sinodiv').hide('slow');
			$('.searchbyName').hide('slow');
	
			$("#search_sino").val("");	
			$("#search_customer").val("");	
		}
		if(searchtype == "searchbyName") {
			$('.sinodiv').hide('slow');
			$('.sidatediv').show('slow');
			$('.sistatus').hide('slow');
			$('.searchbyName').show('slow');

			$("#search_sino").val("");
			$("#search_status").val("");
		}
	});

	// 102318 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page
	// some functions are found at assets/js/globalfunctions.js

	function fillDatatable(search, datefrom, dateto, sino, paystatus, name) {
		var dataTable = $('#table-grid').DataTable({
		
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 2 ], "className": "dt-center" }, { "orderable": false, "targets": [ 3 ], "className": "dt-center" }],
			"destroy": true,
			"ajax":{
				url :base_url+"sales/Sales_sihistory/salesinvoice_table_Trans", // json datasource
				type: "post",
				data:{'search': search, 'datefrom': datefrom, 'dateto': dateto, 'sino': sino, 'paystatus': paystatus, 'name': name},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
					$("#btn_export_excel").prop('hidden',true);
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
		sino		= $("#hdnSino").text();
		paystatus	= $("#hdnPayStatus").text();
		name		= $("#hdnName").text();

		$("#sisearchfilter").val(search).change();
		$("#date_from").val(datefrom);
		$("#date_to").val(dateto);
		$("#search_sino").val(sino);
		$("#search_status").val(paystatus).change();
		$("#search_customer").val(name);
	}
	else {
		search 		= $("#sisearchfilter").val();
		datefrom 	= formatDate($("#date_from").val());
		dateto		= formatDate($("#date_to").val());
		sino		= $("#search_sino").val();
		paystatus	= $("#search_status").val();
		name		= $("#search_customer").val();
	}

	fillDatatable(search, datefrom, dateto, sino, paystatus, name);

	$("#searchBtn").click(function(e){
		e.preventDefault();
		
		search 		= $("#sisearchfilter").val();
		datefrom 	= formatDate($("#date_from").val());
		dateto		= formatDate($("#date_to").val());
		sino		= $("#search_sino").val();
		paystatus	= $("#search_status").val();
		name		= $("#search_customer").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnSino").text(sino);
		$("#hdnPayStatus").text(paystatus);
		$("#hdnName").text(name);

		if(search == "sidatediv") {
			if(datefrom != "" && dateto != "") {
				fillDatatable(search, datefrom, dateto, sino, paystatus, name);
			}
			else {
				toastMessage("Note", "No date found. Please choose a date.", "error");
			}
		}
		else if(search == "sinodiv") {
			if (sino != "") {
				fillDatatable(search, datefrom, dateto, sino, paystatus, name);
			}
			else {
				toastMessage("Note", "No SI Number found.", "error");
			}
		}
		else if(search == "sistatus") {
			if(datefrom != "" && dateto != "" && paystatus != "") {
				fillDatatable(search, datefrom, dateto, sino, paystatus, name);
			}
			else {
				toastMessage("Note", "No SI Status or date found.", "error");
			}
		}
		else if(search == "searchbyName") {
			if(datefrom != "" && dateto != "" && name != "") {
				fillDatatable(search, datefrom, dateto, sino, paystatus, name);
			}
			else {
				toastMessage("Note", "No Customer or date found.", "error");
			}
		}
	});

	$('.btnAddSales').click(function(e) {
		window.open(base_url+"sales/Sales_invoice_form/salesinvoice_form/"+token, '_self');
	});

	// Storing session data for ease of navigation after clicking SI Number
	$("#table-grid").delegate( "#btnSino", "click", function() {
		// for url
		url_sino = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		sino		= $("#hdnSino").text();
		paystatus	= $("#hdnPayStatus").text();
		name		= $("#hdnName").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_sihistory/storeSearchVariables',
			data:{ 'search': "SItran|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sino': sino, 'paystatus': paystatus, 'name': name },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"sales/Sales_siview/sales_invoiceview/" + token + "/" + url_sino + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Reference Button
	$("#table-grid").delegate( "#btnReference", "click", function() {
		// for url
		url_sino = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		sino		= $("#hdnSino").text();
		paystatus	= $("#hdnPayStatus").text();
		name		= $("#hdnName").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_sihistory/storeSearchVariables',
			data:{ 'search': "SItran|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sino': sino, 'paystatus': paystatus, 'name': name },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/sales_sirefview/" + token + "/" + url_sino + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

});