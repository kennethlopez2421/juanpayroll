$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.cvdatediv').show('slow');
	var searchtype = "none";
	dataTable = $('#table-grid').DataTable({
		//"processing": true,
		"serverSide": true,
		"columnDefs": [
		    		{ targets: 6, orderable: false, "sClass":"text-center" }
				],
		"ajax":{
			url:base_url+"Main_account/cashvoucher_table", // json datasource
			type: "post",  // method  , by default get
			data:{'searchtype': searchtype},
			beforeSend:function(data)
			{
				$.LoadingOverlay("show"); 
			},
			complete: function()
			{
				$.LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$("#cvsearchfilter").change(function() {
		var searchtype = $('#cvsearchfilter').val();

		   if(searchtype == "cvdatediv")
	       {
	         $('.cvdatediv').show('slow');
	         $('.cvpaydiv').hide('slow');	  
	         $('.cvstatdiv').hide('slow');
	         $('.cvnodiv').hide('slow');
	         $("#payto").val("");
	         $("#cvno").val("");

	       }
	       else if(searchtype == "cvpaydiv")
	       {
	         $('.cvpaydiv').show('slow');
	         $('.cvdatediv').hide('slow');	
	         $('.cvstatdiv').hide('slow');
	         $('.cvnodiv').hide('slow');	
	         $("#payto").val("");
	         $("#cvno").val("");
	       }
	       else if(searchtype == "cvstatdiv")
	       {
	         $('.cvstatdiv').show('slow');
	         $('.cvpaydiv').hide('slow');	
	         $('.cvdatediv').hide('slow');	
	         $('.cvnodiv').hide('slow');
	         $("#payto").val("");
	         $("#cvno").val("");
	       }
	       else if(searchtype == "cvnodiv")
	       {
	         $('.cvnodiv').show('slow');
	         $('.cvpaydiv').hide('slow');	
	         $('.cvdatediv').hide('slow');
	         $('.cvstatdiv').hide('slow');	
	         $("#payto").val("");
	         $("#cvno").val("");

	       }
	});


	//start
	$(".btnSearchCV").click(function(e){
		e.preventDefault();
		var date1 = $("#datefrom").val();
		var date2 = $("#dateto").val();

		var date3 = $("#datefrom1").val();
		var date4 = $("#dateto1").val();
		var payto = $("#payto").val();
		var cvstatus = $("#cvstatus").val();
		var cvno = $("#cvno").val();

		var searchtype = $('#cvsearchfilter').val();
		var checker=0;

		if(searchtype == "none")
		{
			checker=0;
		}
		else if(searchtype == "cvdatediv")
		{
			if(date1 == "" && date2 == "")
			{
				checker=0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill date range field.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 3000          
				});
			}
			else
			{
				checker=1;
			}
		}
		else if(searchtype == "cvpaydiv")
		{
			if(payto == "")
			{
				checker=0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill pay to field.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 3000          
				});
			}
			else
			{
				checker=1;
			}
		}
		else if(searchtype == "cvstatdiv")
		{
			if(date3 == "" && date3 == "")
			{
				checker=0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill date range field.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 3000          
				});
			}
			else
			{
				if(cvstatus == "")
				{
					checker=0;
						$.toast({
					    heading: 'Note:',
					    text: "Please select cv status.",
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 3000          
					});
				}
				else
				{
					checker=1;
				}
				
			}
		}
		else if(searchtype == "cvnodiv")
		{
			if(cvno == "")
			{
				checker=0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill cvno field.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 3000          
				});
			}
			else
			{
				checker=1;
			}
		}


		
		if(checker == 1)
		{
			var datefrom = formatDate(date1);
			var dateto = formatDate(date2);

			var datefrom1 = formatDate(date3);
			var dateto1 = formatDate(date4);


			dataTable = $('#table-grid').DataTable({
				destroy: true,
				//"processing": true,
				"serverSide": true,
				"columnDefs": [
		    		{ targets: 6, orderable: false, "sClass":"text-center" }
				],
				"ajax":{
					url:base_url+"Main_account/cashvoucher_table", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype,
					      'datefrom': datefrom, 
					      'dateto': dateto,
					      'payto': payto,
					      'datefrom1': datefrom1,
					      'dateto1': dateto1,'cvstatus': cvstatus, 'cvno': cvno},
					beforeSend:function(data)
					{
						$.LoadingOverlay("show"); 
					},
					complete: function()
					{
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

	});
	//end

	//start
	$('#table-grid').delegate(".btnbdview", "click", function(){
	  	var cvno = $(this).data('value');
	
	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/display_cashvoucher_Details',
	  		data:{'cvno':cvno},
	  		success:function(data)
	  		{
	  			var res1 = data.result1;
	  			var remarks = res1[0].remarks;
	  			if(remarks == null)
	  			{
	  				remarks = "";
	  			}
	  			if (data.success == 1) 
	  			{
	  	            document.getElementById('info_fullname').innerHTML = 
	  	            res1[0].payto.toUpperCase();
	  	            document.getElementById('info_notes').innerHTML = "Notes: "+ remarks;

	  	            document.getElementById('info_cvno').innerHTML = "CV #: "+res1[0].cvno;
	  	            document.getElementById('info_trandate').innerHTML = res1[0].trandate;

	  	            document.getElementById('info_funds').innerHTML = "Funds Date: "+ res1[0].fundsdate;
	  	            document.getElementById('info_status').innerHTML = "CV Status: "+ res1[0].type;
	  	            document.getElementById('info_create').innerHTML = "Created By: "+ res1[0].username;
	  	            document.getElementById('info_app').innerHTML = "Approved By: "+ res1[0].approver;

	  	            // document.getElementById('info_funds').innerHTML =  res1[0].fundsdate;
	  	            // document.getElementById('info_status').innerHTML =  res1[0].type;
	  	            // document.getElementById('info_create').innerHTML =  res1[0].username;
	  	            // document.getElementById('info_app').innerHTML = res1[0].approver;


	  	            var dataTable1 = $('#table-grid1').DataTable({
	  	            	"destroy": true,
						//"processing": true,
						"serverSide": true,
						"ajax":{
							url :base_url+"Main_account/view_cashvoucher_Details", // json datasource
							type: "post",  // method  , by default get
							data:{'cvno':cvno},
							beforeSend:function(data)
							{
								$.LoadingOverlay("show"); 
							},
							complete: function()
							{
								$.LoadingOverlay("hide"); 
							},
							error: function(){  // error handling
								$(".table-grid1-error").html("");
								$("#table-grid1").append('<tbody class="table-grid1-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
								$("#table-grid1_processing").css("display","none");
							}
						}
					});

					// dataTable1.destroy();
	  	        }
	  		}
	  	});

	});
	//end 


	//start
	$('#table-grid').delegate(".btnPrintCV", "click", function(){
	  	var cvno = $(this).data('value'); 	
	  	var currUrl = window.location.href;
		var token = $('#token').val();

		window.open(
			''+base_url+'Main_account/cashvoucher_print/'+cvno+'/'+token,
			'_blank' // <- This is what makes it open in a new window.
		);


	});
	//end  		


});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
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
