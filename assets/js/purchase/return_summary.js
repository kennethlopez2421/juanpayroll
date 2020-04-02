$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	// 092718 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	function formatDate(date) {
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) month = '0' + month;
		if (day.length < 2) day = '0' + day;

		return [year, month, day].join('-');
	}

	$("#sosearchfilter").change(function() {
		var searchtype = $('#sosearchfilter').val(); // id ng dropdown
		if (searchtype == "poretnodiv") {
			$('#poretnoField').val('');
			$('.poretnodiv').show('slow');	
			$('.podatediv').hide('slow');
		}
		if (searchtype == "podatediv") {
			$('#poretnoField').val('');
			$('.poretnodiv').hide('slow');	
			$('.podatediv').show('slow');
		}
	});

	function fillDatatable(search, datefrom, dateto, poretno) {
		dataTable = $('#table-grid').DataTable({
			"serverSide": true,
			"order": [[ 2, "desc" ]],
			"destroy": true,
			"ajax":{
				url :base_url+"purchase/PR_history/table_return_summary", // json datasource
				type: "post",
				data:{'search': search, 'datefrom': datefrom, 'dateto': dateto, 'poretno': poretno},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				error: function() {  // error handling
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
	
	// reuseable toast call function for easeness and shorter code
	function toastMessage(heading, text, icon, bgcolor) {
		// #5cb85c success
		// #f0ad4e error
		$.toast({
			heading: heading,
			text: text,
			icon: icon,
			loader: false,  
			stack: false,
			position: 'top-center', 
			allowToastClose: false,
			bgColor: bgcolor,
			textColor: 'white'  
		});
	}

	search = $("#hdnSearch").text();

	if (search != "") {
		$("#sosearchfilter").val(search).change();
		$("#date1").val($("#hdnDatefrom").text());
		$("#date2").val($("#hdnDateto").text());
		$("#poretnoField").val($("#hdnPoretno").text());

		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		poretno		= $("#hdnPoretno").text();
	}
	else {
		search 		= $("#sosearchfilter").val();
		datefrom 	= formatDate($("#date1").val());
		dateto		= formatDate($("#date2").val());
		poretno		= $("#poretnoField").val();
	}

	fillDatatable(search, datefrom, dateto, poretno);

	$("#searchBtn").click(function(){
		search 		= $("#sosearchfilter").val();
		datefrom 	= formatDate($("#date1").val());
		dateto		= formatDate($("#date2").val());
		poretno		= $("#poretnoField").val();
		checker		= 0;

		$('#hdnSearch').text(search);
		$('#hdnDatefrom').text(datefrom);
		$('#hdnDateto').text(dateto);
		$('#hdnPoretno').val(poretno);

		if (search == "podatediv") {
			if (datefrom != "" && dateto != "") {
				checker = 1;
			}
			else {
				toastMessage('Note', 'Please indicate date range', 'error', '#f0ad4e');
			}
        }
        else if (search == "poretnodiv") {
			if (poretno != "") {
				checker = 1;
			}
			else {
				toastMessage('Note', 'Please indicate PO Number', 'error', '#f0ad4e');
			}
        }
		
		if (checker == 1) {
			fillDatatable(search, datefrom, dateto, poretno);
		}
	});

	// Storing session data for ease of navigation after clicking PO Number
	$("#table-grid").delegate( "#btnPoretno", "click", function() {
		// for url
		url_poretno = $(this).data("value");
		supid = $(this).data("supid");

		// get search variables
		search = $('#hdnSearch').text();
		datefrom = $('#hdnDatefrom').text();
		dateto = $('#hdnDateto').text();
		poretno = $('#hdnPoretno').val();

		$.ajax({
			type: 'post',
			url: base_url+'purchase/PR_history/storeSearchVariables',
			data:{'search': "PORet|" + search, 'datefrom': datefrom, 'dateto': dateto, 'poretno': poretno},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"purchase/PR_historyview/poreturn_view/" + token + "/" + url_poretno + "/" + supid, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Allocate Button
	$("#table-grid").delegate( "#btnAllocate", "click", function() {
		// for url
		url_poretno = $(this).data("value");
		supid = $(this).data("supid");

		// get search variables
		search = $('#hdnSearch').text();
		datefrom = $('#hdnDatefrom').text();
		dateto = $('#hdnDateto').text();
		poretno = $('#hdnPoretno').val();

		$.ajax({
			type: 'post',
			url: base_url+'purchase/PR_history/storeSearchVariables',
			data:{'search': "PORet|" + search, 'datefrom': datefrom, 'dateto': dateto, 'poretno': poretno},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_purchase/poreturn_allocate/" + token + "/" + url_poretno + "/" + supid, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});
});

function isNumberKeyOnly(evt) { 
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}