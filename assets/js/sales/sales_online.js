$(function(){
var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	 $('.sodatediv').show('slow');
	 $('.soSearchdiv').show('slow');
	 var searchtype = "none";
	 var dataTable = $('#table-grid').DataTable({
				"destroy": true,
				
				"order": [[ 1, "desc" ]],
				"serverSide": true,
				"columnDefs": [{ "orderable": false, "targets": [ 5 ], "sClass":"text-center" }],
				"ajax":{
					type: "post", 
					url :base_url+"Main_sales/salesonline_table", // json datasource
					data:{'searchtype': searchtype},
					beforeSend:function(data)
					{
						$("#table-grid").LoadingOverlay("show"); 
					},
					complete: function()
					{
						$("#table-grid").LoadingOverlay("hide"); 
					},
					error: function(){  // error handling
						$(".table-grid-error").html("");
						$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
						$("#table-grid_processing").css("display","none");
					}
				}
			});

	$("#sosearchfilter").change(function() {
		var searchtype = $('#sosearchfilter').val();

		   if(searchtype == "none")
	       {
			 $('.sodatediv').hide('slow');	
			 $('.sonodiv').hide('slow');
			
			 $('.statdiv').hide('slow');
			 $('.namediv').hide('slow');	
			 $('.soSearchdiv').hide('slow'); 
			 $('.drnodiv').hide('slow');
			 $("#sono").val("");

	       }
	       else if(searchtype == "sodatediv")
	       {
	         $('.sodatediv').show('slow');
	         $('.sonodiv').hide('slow');
	         $('.statdiv').hide('slow');

	         $('.namediv').hide('slow');	
			 $('.soSearchdiv').show('slow');
			 $('.drnodiv').hide('slow');
			 $("#sono").val("");	  
	       }
	       else if(searchtype == "sonodiv")
	       {
	         $('.sonodiv').show('slow');
	         $('.sodatediv').hide('slow');	
	         $('.statdiv').hide('slow');
	  
	         $('.namediv').hide('slow');
			 $('.soSearchdiv').show('slow');
			 $('.drnodiv').hide('slow');
			 $("#sono").val("");		 
	       }
	       else if(searchtype == "statdiv")
	       {
	       	 $('.statdiv').show('slow');
	       	 $('.sodatediv').show('slow');	
	         
	         $('.sonodiv').hide('slow');	
	         $('.namediv').hide('slow');
	  
			 $('.soSearchdiv').show('slow');
			 $('.drnodiv').hide('slow');
			 $("#sono").val("");  
	       }
	       else if(searchtype == "drnodiv")
	       {
	       	 $('.drnodiv').show('show');
	       	 $('.statdiv').hide('slow');
	         $('.sodatediv').hide('slow');
	 
	         $('.sonodiv').hide('slow');	
	         $('.namediv').hide('slow');
			 $('.soSearchdiv').hide('slow');
			 $("#sono").val("");
	       }

	});

	//start
	$(".btnSearchSO").click(function(e){
		e.preventDefault();
	
		var dateto = $("#dateto").val();
		var datefrom = $("#datefrom").val();
		var sono = $("#sono").val();
		var searchtype = $('#sosearchfilter').val();
		var statfilter = $("#statfilter").val();

		var checker = 0;
		if(searchtype == "sodatediv")
		{
			if(dateto != "" || datefrom != "")
			{
				checker=1;
			}
			else
			{
				checker=0;
			}
		}
		else if(searchtype == "sonodiv")
		{
			if(sono != "")
			{
				checker=1;
			}
			else
			{
				$.toast({
				    heading: 'Note',
				    text: "No online sales order number found. Please fill in data.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 4000          
				});
				checker=0;
			}
		}
		else if(searchtype == "statdiv")
		{
			if(dateto != "" || datefrom != "")
			{
				if(statfilter != "none")
				{
					checker=1;
				}
				else
				{
					checker=0;
					$.toast({
					    heading: 'Note',
					    text: "No status selected. Please select a status.",
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000          
					});
				}
			}
			else
			{
				checker=0;
				$.toast({
					    heading: 'Note',
					    text: "No date found. Please choose a date.",
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000          
					});
			}
		}
		else if(searchtype == "none")
		{
			checker=1;
		}
		else
		{
			checker=0;
		}
		
		
		if(checker == 1)
		{
			var date1 = formatDate(datefrom);
			var date2 = formatDate(dateto);
			$("#table-grid").prop('hidden',false);
			var dataTable = $('#table-grid').DataTable({
				"destroy": true,
				
				"order": [[ 1, "desc" ]],
				"columnDefs": [{ "orderable": false, "targets": [ 5 ], "sClass":"text-center" }],
				"serverSide": true,
				"ajax":{
					type: "post", 
					url :base_url+"Main_sales/salesonline_table", // json datasource
					data:{'dateto':date2, 
					'datefrom': date1, 
					'searchtype': searchtype, 'statfilter': statfilter, 'sono': sono},
					beforeSend:function(data)
					{
						$("#table-grid").LoadingOverlay("show"); 
					},
					complete: function()
					{
						$("#table-grid").LoadingOverlay("hide"); 
					},
					error: function(){  // error handling
						$(".table-grid-error").html("");
						$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
						$("#table-grid_processing").css("display","none");
					}
				}
			});
		}


	});
	//end


	 //start
	$('#table-grid').delegate(".btnDRelease", "click", function(){
	  	var sono_id = $(this).data('value');
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_sales/display_SO_Details',
	  		data:{'sono_id':sono_id},
	  		success:function(data)
	  			{
	  			var res1 = data.result1;
	  			var res2 = data.result2;
	  			var res3 = data.result3;
	  			if (data.success == 1) 
	  			{
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
	  		success:function(data)
	  			{
	  			var res1 = data.result1;
	  			var res2 = data.result2;
	  			var res3 = data.result3;
	  			if (data.success == 1) 
	  			{
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
	$('#table-grid').delegate(".btnRDRelease", "click", function(){
	  	var drno_id = $(this).data('value');
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_sales/display_RDR_Details',
	  		data:{'drno_id':drno_id},
	  		success:function(data)
	  			{
	  			var res1 = data.result1;
	  			var res2 = data.result2;
	  			var res3 = data.result3;
	  			if (data.success == 1) 
	  			{
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
	});
	//end

	

	$('#code-scan').codeScanner();

	$(".cancelBtn").click(function(e){
		e.preventDefault();

	});

	$('#code-scan').codeScanner({
    onScan: function ($element, code) {
        var drno_id = $('#info_drno').val();


        /////
        // still searching for a solution 
        /////
        if(code == "4711421699495")
        {
        	var itemcode = 468;
        	// JC Asado Siopao
        }
        if(code == "4806503870258")
        {
        	var itemcode = 483;
        	// NH Beef Siomai
        }
        if(code == "763649075791")
        {
        	var itemcode = 509;
        	// Chilli Sauce
        }   
        // harcoded itemid 




        var itemarray=[];
		var qtyarray=[];
		var checker = 0;
		itemarray=[];
		qtyarray=[];
		var drno_id = $('#info_drno').val();
		var totalcount = $("#totaldata0").val();
		
		var a = 0;
		if(totalcount > 0)
		{
			for(i=0; i < totalcount; i++ )
			{
				
				var itemid = $("#item"+i).val();
				var qty = $("#qty"+i).val();
				var itemname = $("#itemname"+i).val();

				if(itemid == itemcode)
				{
					var value = parseInt($("#barcode"+i).val(), 10);
				    value = isNaN(value) ? 0 : value;
				    value++;
				    if(value <= qty)
				    {
				    	 var barqty = $("#barcode"+i).val(value);
				    	 checker = 1;
				    }
				    else
				    {
				    	$.toast({
						    heading: 'Note',
						    text: "Note: Quantity exceeded! Please check " + itemname + " details.",
						    icon: 'error',
						    loader: false,   
						    stack: false,
						    position: 'top-center',  
						    bgColor: '#d9534f',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 5000          
						});
						checker = 2;
				    }
				}
				else
				{
					// checker = 3;
					// itemcode = "";
					$.toast({
						    heading: 'Note',
						    text: "Note: No record found in the list. Please check your data.",
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

			if(checker == 3)
			{
					$.toast({
						    heading: 'Note',
						    text: "Note: No record found in the list. Please check your data.",
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
		else
		{
			checker = 2;
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


		if((checker == 1) && (totalcount > 0))
		{
			for(i=0; i < totalcount; i++ )
			{
				var zitemid = $("#item"+i).val();
				var zqty = $("#barcode"+i).val();
				if(zqty > 0)
				{
					itemarray.push(zitemid);
					qtyarray.push(zqty);
				}

			}

						$.ajax({
					  		type: 'post',
					  		url: base_url+'Main_sales/validate_item_releaseDetails',
					  		data:{'drno_id':drno_id},
					  		success:function(data)
					  			{
					  			if (data.success == 1) 
					  			{
					  					
					  				var dataTable2 = $('#table-grid00').DataTable({
										
										"serverSide": true,
										"ajax":{
											url :base_url+"Main_sales/display_barcodeitem_releaseDetails", // json datasource
											type: "post",  // method  , by default get
											data:{'itemarray': itemarray, 'qtyarray': qtyarray},
											error: function(){  // error handling
												$(".table-grid00-error").html("");
												$("#table-grid00").append('<tbody class="table-grid00-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
												$("#table-grid00_processing").css("display","none");
											}
										}
									});

										$.toast({
										    heading: 'Success',
										    text: "Item has been successfully added for release.",
										    icon: 'success',
										    loader: false,  
										    stack: false,
										    position: 'top-center', 
										    bgColor: '#5cb85c',
											textColor: 'white',
											allowToastClose: false,
											hideAfter: 5000,
										});

									dataTable2.destroy();
					  			}
					  		}

					  	});
		}
 
    }	
	});

	
	$(".printDR").click(function(e){
		e.preventDefault();

		var drno_value = $(".drno_value").val();
		if(drno_value > 0)
		{
			window.location.href = ''+base_url+'Main_sales/dr_exportPDF/'+drno_value;
		}
		
	});
	
	
});

function dispalyNotif(rowcount)
{
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

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}


$(document).ready(function(){
    $("#btnSearchSO").trigger("click");
});

