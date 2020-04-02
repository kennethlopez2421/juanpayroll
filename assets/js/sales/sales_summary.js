$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#hdnToken").val();

	$("#sosearchfilter").change(function() {
		
		var searchtype = $('#sosearchfilter').val(); // id ng dropdown

		$("#date_to").datepicker("setDate", new Date());
		$("#date_from").datepicker("setDate", new Date());
		
		if(searchtype == "sonodiv")
		{
			$('.podatediv').hide('slow');
			$('.search_po_btn').show('slow');
			$('.ponodiv').show('slow');	
			$('.searchbyName').hide('slow');
			$('.poshipping').hide('slow');
			$('.ponostatus').hide('slow');

			$("#search_status").val("");
			$("#search_customer").val("");	
			$("#search_shipping").val("");	
		}
		if(searchtype == "sodatediv")
		{
			$('.ponodiv').hide('slow');
			$('.podatediv').show('slow');
			$('.poshipping').hide('slow');
			$('.searchbyName').hide('slow');
			$('.ponostatus').hide('slow');
			$('.search_po_btn').show('slow');

			$("#search_shipping").val("");	
			$("#search_customer").val("");	
			$("#search_status").val("");	
			$("#search_sono").val("");	
		}
		if(searchtype == "sostatus")
		{
			$('.podatediv').show('slow');
			$('.ponostatus').show('slow');
			$('.ponodiv').hide('slow');
			$('.searchbyName').hide('slow');
			$('.poshipping').hide('slow');
			$('.search_po_btn').show('slow');

			$("#search_shipping").val("");	
			$("#search_sono").val("");	
			$("#search_customer").val("");	
		}
		if(searchtype == "searchbyName")
		{
			$('.ponodiv').hide('slow');
			
			$('.podatediv').show('slow');
			$('.ponostatus').hide('slow');
			
			$('.search_po_btn').show('slow');
			$('.poshipping').hide('slow');
			$('.searchbyName').show('slow');

			$("#search_sono").val("");	
			$("#search_shipping").val("");	
			$("#search_status").val("");
		}
		if(searchtype == "soshipping")
		{
			$('.ponodiv').hide('slow');	
			$('.podatediv').show('slow');
			$('.searchbyName').hide('slow');
			$('.ponostatus').hide('slow');
			$('.poshipping').show('slow');
			$('.search_po_btn').show('slow');

			$("#search_sono").val("");	
			$(".search_status").val("");
			$(".search_customer").val("");	
		}
	});

	// 102218 - nick
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

	function fillDatatable(search, datefrom, dateto, sono, status, name, shipping) {
		var dataTable = $('#table-grid').DataTable({
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 6 ], "className": "dt-center" }],
			"destroy": true,
			"ajax":{
				url :base_url+"sales/Sales_salesorder_history/table_sales_summary", // json datasource
				type: "post",
				data:{'search': search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono, 'status': status, 'name': name, 'shipping': shipping},
				beforeSend:function(data){
					$.LoadingOverlay("show"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
					$("#btn_export_excel").prop('hidden',true);
				},
				complete: function(data)
				{
					$.LoadingOverlay("hide"); 
				}
			},
			"fnDrawCallback": function(){
				var api = this.api()
				var json = api.ajax.json();
				// console.log(json);
				$(".loader").hide();
				$("#table_salesorder").show();
			}
		});
	}
	
	// reuseable toast call function for easeness and shorter code
	function toastMessage(heading, text, icon) {
		if (icon == "info") {
			bgcolor = "#5cb85c";
		}
		else if (icon == "error") {
			bgcolor = "#f0ad4e";
		}

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
		$("#date_from").val($("#hdnDatefrom").text());
		$("#date_to").val($("#hdnDateto").text());
		$("#search_sono").val($("#hdnSono").text());
		$("#search_status").val($("#hdnStatus").text()).change();
		$("#search_customer").val($("#hdnName").text());
		$("#search_shipping").val($("#hdnShipping").text()).change();

		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		sono		= $("#hdnSono").text();
		status		= $("#hdnStatus").text();
		name		= $("#hdnName").text();
		shipping	= $("#hdnShipping").text();
	}
	else {
		search 		= $("#sosearchfilter").val();
		datefrom 	= formatDate($("#date_from").val());
		dateto		= formatDate($("#date_to").val());
		sono		= $("#search_sono").val();
		status		= $("#search_status").val();
		name		= $("#search_customer").val();
		shipping	= $("#search_shipping").val();
	}

	fillDatatable(search, datefrom, dateto, sono, status, name, shipping);

	$("#searchBtn").click(function(){
		search 		= $("#sosearchfilter").val();
		datefrom 	= formatDate($("#date_from").val());
		dateto		= formatDate($("#date_to").val());
		sono		= $("#search_sono").val();
		status		= $("#search_status").val();
		name		= $("#search_customer").val();
		shipping	= $("#search_shipping").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnSono").text(sono);
		$("#hdnStatus").text(status);
		$("#hdnName").text(name);
		$("#hdnShipping").text(shipping);

		if (search == "sodatediv") {
			if (datefrom != "" && dateto != "") {
				fillDatatable(search, datefrom, dateto, sono, status, name, shipping);
			}
			else {
				toastMessage('Note', 'Please indicate date range', 'error');
			}
        }
        else if (search == "sonodiv") {
			if (sono != "") {
				fillDatatable(search, datefrom, dateto, sono, status, name, shipping);
			}
			else {
				toastMessage('Note', 'Please indicate PO Number', 'error');
			}
        }
        else if (search == "sostatus") {
			if (datefrom != "" && dateto != "" && status != "") {
				fillDatatable(search, datefrom, dateto, sono, status, name, shipping);
			}
			else {
				toastMessage('Note', 'Please indicate PO Number', 'error');
			}
        }
        else if (search == "searchbyName") {
			if (datefrom != "" && dateto != "" && name != "") {
				fillDatatable(search, datefrom, dateto, sono, status, name, shipping);
			}
			else {
				toastMessage('Note', 'Please indicate PO Number', 'error');
			}
        }
        else if (search == "soshipping") {
			if (datefrom != "" && dateto != "" && shipping != "") {
				fillDatatable(search, datefrom, dateto, sono, status, name, shipping);
			}
			else {
				toastMessage('Note', 'Please indicate PO Number', 'error');
			}
        }

	});

	// Storing session data for ease of navigation after clicking SO Number
	$("#table-grid").delegate( "#btnSono", "click", function() {
		// for url
		url_sono = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		sono		= $("#hdnSono").text();
		status		= $("#hdnStatus").text();
		name		= $("#hdnName").text();
		shipping	= $("#hdnShipping").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_salesorder_history/storeSearchVariables',
			data:{'search': "SO|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono, 'status': status, 'name': name, 'shipping': shipping},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/salesorder_view/" + token + "/" + url_sono + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Edit Button
	$("#table-grid").delegate( "#btnEdit", "click", function() {
		// for url
		url_sono = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		sono		= $("#hdnSono").text();
		status		= $("#hdnStatus").text();
		name		= $("#hdnName").text();
		shipping	= $("#hdnShipping").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_salesorder_history/storeSearchVariables',
			data:{'search': "SO|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono, 'status': status, 'name': name, 'shipping': shipping},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_sales/salesorder_edit/" + token + "/" + url_sono + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

});