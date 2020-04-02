$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	$("#sosearchfilter").change(function() {
		var searchtype = $('#sosearchfilter').val(); // id ng dropdown

		if(searchtype == "ponodiv") {
			$('.searchDateFrom').val("");
			$('.searchDateTo').val("");
			$(".search_status").val("");
			$('.ponodiv').show('slow');	
			$('.podatediv').hide('slow');
			$('.ponostatus').hide('slow');
			$('.search_po_btn').show('slow');
		}
		if(searchtype == "podatediv") {
			$(".searchDateTo").val(date);
        	$(".searchDateFrom").val(date);
			$('.ponodiv').hide('slow');
			$(".search_status").val("");	
			$('.podatediv').show('slow');
			$('.ponostatus').hide('slow');
			$('.search_po_btn').show('slow');
			$(".search_pono").val("");
		}
	});

	// 092418 - nick
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

	function fillDatatable(search, datefrom, dateto, pono) {
		dataTable = $('#table-grid').DataTable({
			"serverSide": true,
			"order": [[ 2, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 0, 6 ], "className": "dt-center" }],
			"destroy": true,
			"ajax":{
				url :base_url+"purchase/PO_approval/table_purchase_summary", // json datasource
				type: "post",
				data:{'search': search, 'datefrom': datefrom, 'dateto': dateto, 'pono': pono},
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
		$("#ponoField").val($("#hdnPono").text());

		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		pono		= $("#hdnPono").text();
	}
	else {
		search 		= $("#sosearchfilter").val();
		datefrom 	= formatDate($("#date1").val());
		dateto		= formatDate($("#date2").val());
		pono		= $("#ponoField").val();
	}

	fillDatatable(search, datefrom, dateto, pono);

	$("#searchBtn").click(function(){
		search 		= $("#sosearchfilter").val();
		datefrom 	= formatDate($("#date1").val());
		dateto		= formatDate($("#date2").val());
		pono		= $("#ponoField").val();
		checker		= 0;

		$('#hdnSearch').text(search);
		$('#hdnDatefrom').text(datefrom);
		$('#hdnDateto').text(dateto);
		$('#hdnPono').val(pono);
		$('#hdnStatus').val(status);

		if (search == "podatediv") {
			if (datefrom != "" && dateto != "") {
				checker = 1;
			}
			else {
				toastMessage('Note', 'Please indicate date range', 'error', '#f0ad4e');
			}
        }
        else if (search == "ponodiv") {
			if (pono != "") {
				checker = 1;
			}
			else {
				toastMessage('Note', 'Please indicate PO Number', 'error', '#f0ad4e');
			}
        }
		
		if (checker == 1) {
			fillDatatable(search, datefrom, dateto, pono);
		}
	});

	// Storing session data for ease of navigation after clicking PO Number
	$("#table-grid").delegate( "#btnPono", "click", function() {
		// for url
		url_pono = $(this).data("value");
		supid = $(this).data("supid");

		// get search variables
		search = $('#hdnSearch').text();
		datefrom = $('#hdnDatefrom').text();
		dateto = $('#hdnDateto').text();
		pono = $('#hdnPono').val();

		$.ajax({
			type: 'post',
			url: base_url+'purchase/PO_approval/storeSearchVariables',
			data:{'search': "POA|" + search, 'datefrom': datefrom, 'dateto': dateto, 'pono': pono},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_purchase/purchaseorder_view/" + token + "/" + url_pono + "/" + supid, '_self');
				$.LoadingOverlay("hide"); 
			}
		});
	});

	// Storing session data for ease of navigation after clicking Edit Button
	$("#table-grid").delegate( "#btnEdit", "click", function() {
		// for url
		url_pono = $(this).data("value");
		supid = $(this).data("supid");

		// get search variables
		search = $('#hdnSearch').text();
		datefrom = $('#hdnDatefrom').text();
		dateto = $('#hdnDateto').text();
		pono = $('#hdnPono').val();

		$.ajax({
			type: 'post',
			url: base_url+'purchase/PO_approval/storeSearchVariables',
			data:{'search': "POA|" + search, 'datefrom': datefrom, 'dateto': dateto, 'pono': pono},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"purchase/PO_approvalview/poapprovalview/" + token + "/" + url_pono + "/" + supid, '_self');
				$.LoadingOverlay("hide"); 
			}
		});
	});

	$("#chkAll").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);

		if ($('input[name="chkSino"]:checked').length > 0) {
			$("#btnBatchApprove").prop("disabled", false);
		}
		else {
			$("#btnBatchApprove").prop("disabled", true);
		}
	});

	$("#table-grid").delegate( "#chkSino", "click", function() {
		if ($('input[name="chkSino"]:checked').length > 0) {
			$("#btnBatchApprove").prop("disabled", false);
		}
		else {
			$("#btnBatchApprove").prop("disabled", true);
		}

		if (($('input[name="chkSino"]:checked').length) == $('input[name="chkSino"]').length) {
			$("#chkAll").prop("checked", true);
		}
		else {
			$("#chkAll").prop("checked", false);
		}
	});

	$("#btnBatchApprove").on("click", function(){
		ponoArray = [];
		$( ".chkSino:checkbox:checked" ).each(function( index ) {
			ponoArray.push({"pono" : $( this ).data("value")});
		});

		$("#batchApproveConfirmationModal").modal("toggle");
	});

	$("#btnConfirmBatchApprove").on("click", function(){
        if (ponoArray == "") {
            toastMessage('Note', 'Please select atleast one Purchase Order', 'error', '#f0ad4e');
        }
        else {
            $.ajax({
				type: 'post',
				url: base_url+'purchase/PO_approval/batchApprove',
				data:{'ponoArray': ponoArray},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				success:function(data) {
					$.LoadingOverlay("hide");
					
					if (data.success == 1) {
						toastMessage('Success', data.message, 'success', '#5cb85c');
	
						search 		= $("#sosearchfilter").val();
						datefrom 	= formatDate($("#date1").val());
						dateto		= formatDate($("#date2").val());
						pono		= $("#ponoField").val();
	
						fillDatatable(search, datefrom, dateto, pono);
						$("#chkAll").prop("checked", false);
						$("#batchApproveConfirmationModal").modal("hide");
					}
					else if (data.success == 0) {
						toastMessage('Note', data.message, 'error', '#f0ad4e');
					}
				}
			});
        }
	});

});

function isNumberKeyOnly(evt) {    
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}