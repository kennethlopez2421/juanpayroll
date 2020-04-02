$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var token = $("#hdnToken").val();

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

	function fillDatatable(search, datefrom, dateto, sono, drstatus) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"order": [[ 1, "desc" ]],
			"serverSide": true,
			"columnDefs": [{ "orderable": false, "targets": [ 5 ], "sClass":"text-center" }],
			"ajax":{
				type: "post", 
				url :base_url+"sales/Sales_directsales/directsales_table", // json datasource
				data:{'search': search, "datefrom": datefrom, "dateto": dateto, "sono": sono, "drstatus": drstatus},
				beforeSend:function(data) {
					$.LoadingOverlay("show");
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
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

	$("#searchfilter").change(function() {
		var searchtype = $('#searchfilter').val();

	   	if(searchtype == "datediv") {
			$('.datediv').show('slow');
			$('.nodiv').hide('slow');
			$('.statusdiv').hide('slow');
			$("#searchNo").val("");
			$("#searchStatus").val("").change();
       	}
       	else if(searchtype == "nodiv") {
			$('.nodiv').show('slow');
			$('.datediv').hide('slow');	
			$('.statusdiv').hide('slow');
			$("#searchNo").val("");
			$("#searchStatus").val("").change(); 
       	}
       	else if(searchtype == "statusdiv") {
			$('.statusdiv').show('slow');
			$('.datediv').show('slow');	
			$('.nodiv').hide('slow');
			$("#searchNo").val("");
			$("#searchStatus").val("").change();
       	}
	});

	search = $("#hdnSearch").text();

	if (search != "") {
		$("#searchfilter").val(search).change();
		$("#datefrom").val($("#hdnDatefrom").text());
		$("#dateto").val($("#hdnDateto").text());
		$("#searchNo").val($("#hdnSono").text());
		$("#searchStatus").val($("#hdnDRStatus").text()).change();

		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		sono		= $("#hdnSono").text();
		drstatus	= $("#hdnDRStatus").text();
	}
	else {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		sono		= $("#searchNo").val();
		drstatus	= $("#searchStatus").val();
	}

	fillDatatable(search, datefrom, dateto, sono, drstatus);
	
	$(".btnSearch").click(function(e){
		e.preventDefault();
		
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		sono		= $("#searchNo").val();
		drstatus	= $("#searchStatus").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnSono").text(sono);
		$("#hdnDRStatus").text(drstatus);

		if(search == "datediv") {
			if(dateto != "" || datefrom != "") {
				fillDatatable(search, datefrom, dateto, sono, drstatus);
			}
			else {
				toastMessage('Note', 'No date found. Please choose a date.', 'error');
			}
		}
		else if(search == "nodiv") {
			if(sono != "") {
				fillDatatable(search, datefrom, dateto, sono, drstatus);
			}
			else {
				toastMessage('Note', 'No sales order number found. Please fill in data.', 'error');
			}
		}
		else if(search == "statusdiv") {
			if(dateto != "" || datefrom != "") {
				if(drstatus != "") {
					fillDatatable(search, datefrom, dateto, sono, drstatus);
				}
				else {
					toastMessage('Note', 'No status selected. Please select a status.', 'error');
				}
			}
			else {
				toastMessage('Note', 'No date found. Please choose a date.', 'error');
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
		drstatus	= $("#hdnDRStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_directsales/storeSearchVariables',
			data:{'search': "DR|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono, 'drstatus': drstatus},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"sales/Sales_drsoview/dr_soview/" + token + "/" + url_sono + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Convert Button
	$("#table-grid").delegate( "#btnConvert", "click", function() {
		// for url
		url_sono = $(this).data("value");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		sono		= $("#hdnSono").text();
		drstatus	= $("#hdnDRStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_directsales/storeSearchVariables',
			data:{'search': "DR|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono, 'drstatus': drstatus},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"sales/Sales_drconvert/salesorder_drconvertview/" + token + "/" + url_sono, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking View Button
	$("#table-grid").delegate( "#btnView", "click", function() {
		// for url
		url_drno = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		sono		= $("#hdnSono").text();
		drstatus	= $("#hdnDRStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_directsales/storeSearchVariables',
			data:{'search': "DR|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono, 'drstatus': drstatus},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"sales/Sales_drview/sales_drviews/" + token + "/" + url_drno + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	/////////

	 //start
	$('#table-grid').delegate(".btnDRelease", "click", function(){
	  	var sono_id = $(this).data('value');
		$.ajax({
	  		type: 'post',
	  		url: base_url + 'Sales_directsales/display_SO_Details',
	  		data:{'sono_id':sono_id},
	  		success:function(data) {
	  			var res1 = data.result1;
	  			var res2 = data.result2;
	  			var res3 = data.result3;
	  			if (data.success == 1) {
	  	            document.getElementById('info_fullname').innerHTML =
	  	            res2[0].lname.toUpperCase() + ", " + res2[0].fname.toUpperCase() +" "+ res2[0].mname.toUpperCase();
	  	            document.getElementById('info_branch').innerHTML = "Branch Name:  " + res2[0].branchname;
	  	            document.getElementById('info_cont').innerHTML = "Contact No.:  " + res2[0].conno;
	  	            document.getElementById('info_address').innerHTML = "Outlet Address:  " + res2[0].address;
    				document.getElementById('info_sono').innerHTML = "SO #:  " + sono_id;
    				document.getElementById('info_trandate').innerHTML = "Date:  " + res1[0].trandate;

	  				var dataTable1 = $('#table-grid1').DataTable({
						"serverSide": true,
						"ajax":{
							url :base_url+"Main_sales/so_item_Details", // json datasource
							type: "post",  // method  , by default get
							data:{'sono_id':sono_id},
							error: function(){  // error handling
								$(".table-grid1-error").html("");
								$("#table-grid1").append('<tbody class="table-grid1-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
								$("#table-grid1_processing").css("display","none");
							}
						},
						"initComplete": function(settings, json) {
						  	var subtotal = 0;
						  	var totalval = 0;
						  	var grandtotal = 0;
						  	$(".totalDatatable2").each(function(){
						  		totalval = parseInt($(this).val());
						  		subtotal = (subtotal*1)+(totalval*1);
						  	});
							freight = parseFloat(res3).toFixed(2);
							subtotal = parseFloat(subtotal).toFixed(2);
							gtotal = parseFloat(subtotal) + parseFloat(res3);
							grandtotal = parseFloat(gtotal).toFixed(2);

						  	$('.subtotalspan').text(addCommas(subtotal));
							$('.freightspan').text(addCommas(freight));							
							$('.gtotalspan').text(addCommas(grandtotal));
						}
					});

					dataTable1.destroy();
	  			}
	  		}

	  	});
	});
	//end

	//start
	$('#table-grid').delegate(".btnDRelease1", "click", function(){
	  	var drno_id = $(this).data('value');
		$("#drno_value").val(drno_id);
	
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_sales/display_DR_Details',
	  		data:{'drno_id':drno_id},
	  		success:function(data) {
	  			var res1 = data.result1;
	  			var res2 = data.result2;
	  			var res3 = data.result3;
	  			if (data.success == 1) {
	  	            document.getElementById('uinfo_fullname').innerHTML =
	  	            res2[0].lname.toUpperCase() + ", " + res2[0].fname.toUpperCase() +" "+ res2[0].mname.toUpperCase();
	  	            document.getElementById('uinfo_branch').innerHTML = "Branch Name:  " + res2[0].branchname;
	  	            document.getElementById('uinfo_cont').innerHTML = "Contact No.:  " + res2[0].conno;
	  	            document.getElementById('uinfo_address').innerHTML = "Outlet Address:  " + res2[0].address;
    				document.getElementById('uinfo_sono').innerHTML = "DR #:  " + drno_id;
    				document.getElementById('uinfo_trandate').innerHTML = "Date:  " + res1[0].trandate;		


	  				var dataTable1 = $('#table-grid3').DataTable({
						
						"serverSide": true,
						"ajax":{
							url :base_url+"Main_sales/dr_item_Details", // json datasource
							type: "post",  // method  , by default get
							data:{'drno_id':drno_id},
							error: function(){  // error handling
								$(".table-grid3-error").html("");
								$("#table-grid3").append('<tbody class="table-grid3-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
								$("#table-grid3").css("display","none");
							}
						},
						"initComplete": function(settings, json) {
						  	var subtotal = 0;
						  	var totalval = 0;
						  	var grandtotal = 0;
						  	$(".totalDatatable3").each(function(){
						  		totalval = parseFloat($(this).val());
						  		subtotal = (subtotal*1)+(totalval*1);
						  	});
							freight = parseFloat(res3).toFixed(2);
							subtotal = parseFloat(subtotal).toFixed(2);
							gtotal = parseFloat(subtotal) + parseFloat(res3);
							grandtotal = parseFloat(gtotal).toFixed(2);

						  	$('.usubtotalspan').text(addCommas(subtotal));
							$('.ufreightspan').text(addCommas(freight));							
							$('.ugtotalspan').text(addCommas(grandtotal));
						}
					});

					dataTable1.destroy();
	  			}
	  		}

	  	});
	});
	//end

	//start
	/*$('#table-grid').delegate(".btnRDRelease", "click", function(){
	  	var drno_id = $(this).data('value');
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_sales/display_RDR_Details',
	  		data:{'drno_id':drno_id},
	  		success:function(data){
	  			var res1 = data.result1;
	  			var res2 = data.result2;
	  			var res3 = data.result3;

	  			if (data.success == 1) {
	  	            document.getElementById('uinfo_fullname').innerHTML =
	  	            res2[0].lname.toUpperCase() + ", " + res2[0].fname.toUpperCase() +" "+ res2[0].mname.toUpperCase();
	  	            document.getElementById('uinfo_branch').innerHTML = "Branch Name:  " + res2[0].branchname;
	  	            document.getElementById('uinfo_cont').innerHTML = "Contact No.:  " + res2[0].conno;
	  	            document.getElementById('uinfo_address').innerHTML = "Outlet Address:  " + res2[0].address;
    				document.getElementById('uinfo_sono').innerHTML = "DR #:  " + drno_id;
    				document.getElementById('uinfo_trandate').innerHTML = "Date:  " + res1[0].trandate;
    				$('#info_drno').val(drno_id);
    			    			
	  				var dataTable1 = $('#table-grid0').DataTable({
						
						"serverSide": true,
						"ajax":{
							url :base_url+"Main_sales/rdr_item_releaseDetails", // json datasource
							type: "post",  // method  , by default get
							data:{'drno_id':drno_id},
							error: function(){  // error handling
								$(".table-grid1-error").html("");
								$("#table-grid1").append('<tbody class="table-grid1-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
								$("#table-grid1_processing").css("display","none");
							}
						}
					});

					dataTable1.destroy();
	  			}
	  		}

	  	});
	});*/
	//end

	

	$('#code-scan').codeScanner();

	$(".cancelBtn").click(function(e){
		e.preventDefault();

	});

	// $('#code-scan').codeScanner({
	//     onScan: function ($element, code) {
	//         console.log(code);
	//     }
	// });
	
	$(".printDR").click(function(e){
		e.preventDefault();

		var drno_value = $(".drno_value").val();
		if(drno_value > 0) {
			window.location.href = ''+base_url+'Main_sales/dr_exportPDF/'+drno_value;
		}
	});	
	
});

function dispalyNotif(rowcount) {
	var totalcount = $("#release0").val();
	if(totalcount > 0) {
		$('#NotifInvModal').modal({show: true});
	}
	else {
		$.toast({
		    heading: 'Note',
		    text: "Note: No record found. Please check your data.",
		    icon: 'error',
		    loader: false,   
		    stack: false,
		    position: 'top-center',  
		    bgColor: '#d9534f',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 5000          
		});
	}
}

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}