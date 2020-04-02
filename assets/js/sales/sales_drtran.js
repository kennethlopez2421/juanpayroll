$(function(){
var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$("#table-grid").prop('hidden', false);
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

	function fillDatatable(search, datefrom, dateto, drno, drstatus) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"serverSide": true,
			"order": [[ 2, "desc" ]],
			"columnDefs": [{ "orderable": true, "targets": [ 5 ], "className": "dt-center" }],
			"ajax":{
				type: "post", 
				url :base_url+"sales/Sales_drhistory/directsales_table_Trans", // json datasource
				data:{'search': search, 'datefrom': datefrom, 'dateto': dateto, 'drno': drno, 'drstatus': drstatus},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function() {  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	$("#searchFilter").change(function() {
		var searchtype = $('#searchFilter').val();

		$("#datefrom").datepicker("setDate", new Date()); //set today
		$('#dateto').datepicker("setDate", new Date()); //set today

	   	if(searchtype == "nodiv") {
			$('.datediv').hide('slow');
			$('.statusdiv').hide('slow');
			$('.nodiv').show('slow');
			$("#searchNo").val("");
			$("#searchStatus").val("").change();
       	}
       	else if(searchtype == "datediv") {
			$('.datediv').show('slow');
			$('.statusdiv').hide('slow');
			$('.nodiv').hide('slow');
			$("#searchNo").val("");
			$("#searchStatus").val("").change();	  
       	}
       	else if(searchtype == "statusdiv") {
			$('.datediv').show('slow');	
			$('.statusdiv').show('slow');
			$('.nodiv').hide('slow');
			$("#searchNo").val("");
			$("#searchStatus").val("").change();
       	}
	});

	search = $("#hdnSearch").text();

	if (search != "") {
		$("#searchFilter").val(search).change();
		$("#datefrom").val($("#hdnDatefrom").text());
		$("#dateto").val($("#hdnDateto").text());
		$("#searchNo").val($("#hdnDrno").text());
		$("#searchStatus").val($("#hdnDRStatus").text()).change();

		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		drno		= $("#hdnDrno").text();
		drstatus	= $("#hdnDRStatus").text();
	}
	else {
		search 		= $("#searchFilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		drno		= $("#searchNo").val();
		drstatus	= $("#searchStatus").val();
	}

	fillDatatable(search, datefrom, dateto, drno, drstatus);

	$(".btnSearch").click(function(e){
		e.preventDefault();
		
		search 		= $("#searchFilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		drno		= $("#searchNo").val();
		drstatus	= $("#searchStatus").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnDrno").text(drno);
		$("#hdnDRStatus").text(drstatus);

		if(search == "datediv") {
			if(dateto != "" && datefrom != "") {
				fillDatatable(search, datefrom, dateto, drno, drstatus);
			}
			else {
				toastMessage("Note", "No date found. Please choose a date.", "error");
			}
		}
		else if(search == "nodiv") {
			if (drno != "") {
				fillDatatable(search, datefrom, dateto, drno, drstatus);
			}
			else {
				toastMessage("Note", "No DR Number found.", "error");
			}
		}
		else if(search == "statusdiv") {
			if(dateto != "" && datefrom != "" && drstatus != "") {
				fillDatatable(search, datefrom, dateto, drno, drstatus);
			}
			else {
				toastMessage("Note", "No DR Status and date found.", "error");
			}
		}
	});

	// Storing session data for ease of navigation after clicking DR Number
	$("#table-grid").delegate( "#btnDrno", "click", function() {
		// for url
		url_drno = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		drno		= $("#hdnDrno").text();
		drstatus	= $("#hdnDRStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_drhistory/storeSearchVariables',
			data:{ 'search': "DRtran|" + search, 'datefrom': datefrom, 'dateto': dateto, 'drno': drno, 'drstatus': drstatus },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"sales/Sales_drhistory/sales_drhistoryview/" + token + "/" + url_drno + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	/////////

	//start
	$('#table-grid').delegate(".btnDRelease1", "click", function(){
	  	var drno_id = $(this).data('value');
		$(".drno_value").val(drno_id);
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
							error: function() {  // error handling
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
	$('#table-grid').delegate(".btnDRpacking", "click", function(){
	  	var drno_id = $(this).data('value');
	  	$("#drno_id").val(drno_id);
	  	$("#dry1").val("");
		$("#dry2").val("");
		$("#per1").val("");
		$("#per2").val("");
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_sales/check_dr_packing',
	  		data:{'drno_id':drno_id},
	  		success:function(data) {
	  			var res = data.result;
	  			var hasdata = data.hasdata;
	  			if(data.success == 1) {
	  				if(hasdata > 0) {
	  					$("#dry1").val(res[0].drybox);
		  				$("#dry2").val(res[0].drybag);
		  				$("#per1").val(res[0].pershbox);
		  				$("#per2").val(res[0].pershbag);
	  				}
	  			}
	  			else if(data.success == 2) {
	  				$.toast({
					    heading: 'Note',
					    text: "DR does not existed. Please check your data.",
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 5000          
					});
	  			}
	  			else {
	  				$.toast({
					    heading: 'Note',
					    text: "No record found. Please check your data.",
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 5000          
					});
	  			}
	  		}
	  	});
	});
	//end

	$(".cancelBtn").click(function(e){
		e.preventDefault();
		var itemarray=[];
		var qtyarray=[];
		var dataTable2 = $('#table-grid00').DataTable({
			destroy: true,
			
			"serverSide": true,
			"ajax":{
				url :base_url+"Main_sales/empty_barcodeitem_releaseDetails", // json datasource
				type: "post",  // method  , by default get
				data:{'itemarray': itemarray, 'qtyarray': qtyarray},
				error: function(){  // error handling
					$(".table-grid00-error").html("");
					$("#table-grid00").append('<tbody class="table-grid00-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid00_processing").css("display","none");
				}
			}
		});
		dataTable2.destroy();
	});
	
	$(".printDR").click(function(e){
		e.preventDefault();

		var drno_value = $(".drno_value").val();
		if(drno_value > 0) {
			window.location.href = ''+base_url+'Main_sales/dr_exportPDF/'+drno_value;
		}	
	});

	$(".saveBtnPack").click(function(e){
		e.preventDefault();
		var drno_value = $("#drno_id").val();
		var dry1 = $("#dry1").val();
		var dry2 = $("#dry2").val();
		var per1 = $("#per1").val();
		var per2 = $("#per2").val();
		var token = $("#token").val();
		if(drno_value > 0) {
			$.ajax({
		  		type: 'post',
		  		url: base_url+'Main_sales/save_dr_packing',
		  		data:{'drno_id':drno_value,'dry1':dry1,'dry2':dry2,'per1':per1,'per2':per2},
		  		success:function(data) {
		  			$.toast({
					    heading: 'Success',
					    text: "Packing for DR# " + drno_value +	 "has been successfully saved.",
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
					    bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 5000,
					});

		  			window.setTimeout(function(){
                     	window.location.href=base_url+"Main_sales/sales_drtran/" + token;
	              	},500)
		  		}
	  		});
		}
	});

	//allowing numeric with decimal 
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    //allowing numeric without decimal 
    $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
	
});

function dispalyNotif(rowcount) {
	var totalcount = $("#release0").val();
	if(totalcount > 0)
	{
		$('#NotifInvModal').modal({show: true});
	}
	else
	{
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

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}