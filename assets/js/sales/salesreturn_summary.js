$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	$("#searchfilter").change(function() {
		var searchtype = $('#searchfilter').val(); // id ng dropdown
		var currentdate = new Date();

	   	if(searchtype == "nodiv") {
			$('.nodiv').show('slow');
			$('.datediv').hide('slow');
			$('.statusdiv').hide('slow');
			$(".searchStatus").val("").change();
			$(".searchNo").val("");
	   	}
	   	if(searchtype == "datediv") {	
			$('.datediv').show('slow');
			$('.nodiv').hide('slow');
			$('.statusdiv').hide('slow');
			$(".searchStatus").val("").change();
			$(".searchNo").val("");
	   	}
	   	if(searchtype == "statusdiv") {	
			$('.statusdiv').show('slow');
			$('.nodiv').hide('slow');
			$('.datediv').hide('slow');
			$(".searchStatus").val("").change();
			$(".searchNo").val("");
	   	}
	});

	function fillDatatable(searchtype, datefrom, dateto, drretno, status) {
		var dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			//"columnDefs": [{ "orderable": false, "targets": [ 6 ], "className": "dt-center" }, {"orderable": false, "targets": [ 4 ]}],
			"columnDefs": [{"orderable": false, "targets": [ 4 ]}],
			"ajax":{
				url :base_url + "sales/Sales_salesreturn_history/table_salesreturn_summary", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype':searchtype, 'datefrom':datefrom, 'dateto':dateto, 'drretno':drretno, 'status':status},
				beforeSend:function(data) {
					$("body").LoadingOverlay("show"); 
				},
				complete: function() {
					$("body").LoadingOverlay("hide"); 
				},
				error: function() {  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	search = $("#hdnSearch").text();

	if (search != "") {
		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		drretno		= $("#hdnDrretno").text();
		status		= $("#hdnStatus").text();

		$("#searchfilter").val(search).change();
		$(".dateFrom").val(datefrom);
		$(".dateTo").val(dateto);
		$("#searchNo").val(drretno);
		$("#searchStatus").val(status).change();
	}
	else {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($(".dateFrom").val());
		dateto		= formatDate($(".dateTo").val());
		drretno		= $("#searchNo").val();
		status		= $("#searchStatus").val();
	}

	fillDatatable(search, datefrom, dateto, drretno, status);

	$("#btnSearch").click(function() {
		var searchtype = $('#searchfilter').val();
		var dateto =  formatDate($('.dateTo').val());
		var datefrom =  formatDate($('.dateFrom').val());
		var no =  $('#searchNo').val();
		var status =  $('#searchStatus').val();

		$("#hdnSearch").text(searchtype);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnDrretno").text(no);
		$("#hdnStatus").text(status);

		if (searchtype == "datediv") {
			if (dateto == "" || datefrom == "") {
				toastMessage('Note:', 'Please fill in date field.', 'error');
			}
			else {
				fillDatatable(searchtype, datefrom, dateto, no, status);
			}
		}
		else if (searchtype == "nodiv") {
			if(no == "") {
				toastMessage('Note:', 'No DR Return number found. Please fill in data.', 'error');
			}
			else {
				fillDatatable(searchtype, datefrom, dateto, no, status);
			}
		}
		else if (searchtype == "statusdiv") {
			if(status == "") {
				toastMessage('Note:', 'No status selected. Please select a status.', 'error');
			}
			else {
				fillDatatable(searchtype, datefrom, dateto, no, status);
			}
		}

	});

	// Storing session data for ease of navigation after clicking DRRET Number
	$("#table-grid").delegate( "#btnDrretno", "click", function() {
		// for url
		url_drretno = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		drretno		= $("#hdnDrretno").text();
		status		= $("#hdnStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_salesreturn_history/storeSearchVariables',
			data:{ 'search': "SRtran|" + search, 'datefrom': datefrom, 'dateto': dateto, 'drretno': drretno, 'status': status },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/salesreturn_view/" + token + "/" + url_drretno + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});
	
});

function tofixed(x){
	return numberWithCommas(parseFloat(x).toFixed(2));
}

function numberWithCommas(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}