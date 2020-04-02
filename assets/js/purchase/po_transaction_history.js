$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	
	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	function fillDatatable(searchtype, datefrom, dateto, pono, status, supplier) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"serverSide": true,
			"bDeferRender": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 6 ], "className": "dt-center" }],
			"ajax":{
				url :base_url+"purchase/PO_history/tbl_po_tranHistory", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype':searchtype, 'datefrom':datefrom, 'dateto':dateto, 'pono':pono, 'status':status, 'supplier':supplier},
				beforeSend:function(data) {
	                $.LoadingOverlay("show"); 
	            },
	            complete: function() {
	                $.LoadingOverlay("hide"); 
	            },
				error: function() {  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	function toastMessage(heading, text, icon, color) {
		$.toast({
			heading: heading,
			text: text,
			icon: icon,
			loader: false,  
			stack: false,
			position: 'top-center', 
			allowToastClose: false,
			bgColor: color,
			textColor: 'white'  
		});
	}

	fillDatatable("podatediv", date, date, "", "", "");

	$("#sosearchfilter").change(function() {
		var searchtype = $('#sosearchfilter').val(); // id ng dropdown
		var currentdate = new Date();

		if(searchtype == "ponodiv") {
			$('#date1').val("");
			$('#date2').val("");
			$(".search_status").val("");
			$('.ponodiv').show('slow');	
			$('.ponosupplier').hide('slow');
			$(".search_supplier").text("");	
			$(".search_supplier").val("");
			$('.podatediv').hide('slow');
			$('.ponostatus').hide('slow');
			$('.search_po_btn').show('slow');
		}
		if(searchtype == "podatediv") {
			$('.ponodiv').hide('slow');
			$('.ponosupplier').hide('slow');	
			$(".search_supplier").val("");
			$(".search_supplier").text("");
			$(".search_status").val("");	
			$(".search_pono").val("");	
			$('.podatediv').show('slow');
			$('.ponostatus').hide('slow');
			$('.search_po_btn').show('slow');
		}
		if(searchtype == "ponostatus") {
			$('.ponosupplier').hide('slow');	
			$(".search_supplier").val("");
			$('.ponodiv').hide('slow');	
			$('.podatediv').show('slow');
			$(".search_supplier").text("");
			$(".search_pono").val("");	
			$('.ponostatus').show('slow');
			$('.search_po_btn').show('slow');
		}
		if(searchtype == "ponosupplier") {
			$('.ponosupplier').show('slow');	
			$(".search_supplier").val("");
			$(".search_pono").val("");	
			$('.ponodiv').hide('slow');	
			$('.podatediv').show('slow');
			$('.ponostatus').hide('slow');
			$('.search_po_btn').hide('slow');
		}
	});

	$("#search_order").click(function() {
		searchtype = $('#sosearchfilter').val();
		datefrom = formatDate($("#datefrom").val());
		dateto = formatDate($("#dateto").val());
		pono = $("#search_pono").val();
		status = $("#search_status").val();
		supplier = $("#search_supplier").val();

		if (searchtype == "podatediv") {
			if (datefrom == "" || dateto == "") {
				toastMessage('Note', 'Please fill in the Date fields.', 'info', '#FFA500');
				checker = 0;
			}
			else {
				checker = 1;
			}
		}
		else if (searchtype == "ponodiv") {
			if (pono == "") {
				toastMessage('Note', 'Please fill in PO Number field.', 'info', '#FFA500');
				checker = 0;
			}
			else {
				checker = 1;
			}
		}
		else if (searchtype == "ponostatus") {
			if (status == "" || datefrom == "" || dateto == "") {
				toastMessage('Note', 'Please fill in PO Status and Date fields.', 'info', '#FFA500');
				checker = 0;
			}
			else {
				checker = 1;
			}
		}
		else if (searchtype == "ponosupplier") {
			if (supplier == "" || datefrom == "" || dateto == "") {
				toastMessage('Note', 'Please fill in Supplier and Date fields.', 'info', '#FFA500');
				checker = 0;
			}
			else {
				checker = 1;
			}
		}

		if (checker == 1) {
			fillDatatable(searchtype, datefrom, dateto, pono, status, supplier);
		}
	});
});

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function isNumberKeyOnly(evt) {    
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}