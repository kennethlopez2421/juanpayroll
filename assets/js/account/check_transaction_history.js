$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.chkdatediv').show('slow');
	var searchtype = "chkdatediv";

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	dataTable = $('#table-grid').DataTable({
		"serverSide": true,
		"columnDefs": [
		    		{ targets: 3, orderable: false, "sClass":"text-center" }
		],
		"ajax":{
			url:base_url+"Main_account/get_check_transactions", // json datasource
			type: "post",  // method  , by default get
			data:{'searchtype': searchtype, 'datefrom': date, 'dateto': date},
			beforeSend:function(data) {
				$("body").LoadingOverlay("show"); 
			},
			complete: function() {
				$("body").LoadingOverlay("hide"); 
			},
			error: function() {  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$("#chksearchfilter").change(function() {
		var searchtype = $('#chksearchfilter').val();

   		if(searchtype == "chkdatediv") {
			$('.chkdatediv').show('slow');  
			$('.chkstatdiv').hide('slow');
			$('.chkchecknodiv').hide('slow');	
			$('.chkpaytodiv').hide('slow');
			$('#payto').val("");
			$('.chkprintdiv').hide('slow');
			$('#chkprint').val("");
			$('.chkcleardiv').hide('slow');
			$('#chkclear').val("");
			$('#checkno').val("");
   		}
		else if(searchtype == "chkstatdiv") {
			$('.chkstatdiv').show('slow');
			$('.chkdatediv').hide('slow');
			$('.chkchecknodiv').hide('slow');			
			$('.chkpaytodiv').hide('slow');			
			$('.chkprintdiv').hide('slow');			
			$('.chkcleardiv').hide('slow');
			$('#chkclear').val("");
			$('#checkno').val("");
			$('#chkprint').val("");
			$('#payto').val("");
		}
		else if(searchtype == "chkpaytodiv") {
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').show('slow');
			$('.chkpaytodiv').show('slow');
			$('.chkchecknodiv').hide('slow');
			$('.chkprintdiv').hide('slow');
			$('#checkno').val("");
			$('#payto').val("");
			$('.chkprintdiv').hide('slow');
			$('#chkprint').val("");
			$('.chkcleardiv').hide('slow');
			$('#chkclear').val("");
		}
		else if(searchtype == "chkprintdiv") {
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').show('slow');
			$('.chkpaytodiv').hide('slow');
			$('.chkchecknodiv').hide('slow');
			$('#checkno').val("");
			$('#payto').val("");
			$('.chkchecknodiv').hide('slow');

			$('.chkprintdiv').show('slow');
			$('.chkcleardiv').hide('slow');
			$('#chkclear').val("");
		}
		else if(searchtype == "chkchecknodiv") {
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').hide('slow');
			$('.chkpaytodiv').hide('slow');
			$('#checkno').val("");
			$('#payto').val("");
			$('.chkchecknodiv').show('slow');
			$('.chkprintdiv').hide('slow');
			$('#checkno').val("");
			$('.chkcleardiv').hide('slow');
			$('#chkclear').val("");
		}
		else if(searchtype == "chkcleardiv") {

			//alert('hi');
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').show('slow');
			$('.chkpaytodiv').hide('slow');
			$('#checkno').val("");
			$('#payto').val("");
			$('.chkchecknodiv').hide('slow');
			$('.chkprintdiv').hide('slow');
			$('#checkno').val("");
			$('.chkcleardiv').show('slow');
			//$('#chkclear').val("");
			//$('#checkno').val("");
		}
		else
		{
			$('.chkchecknodiv').show('slow');
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').hide('slow');
		}
	});

	$(".btnSearchChk").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var searchtype = $('#chksearchfilter').val();
		var chkstatus = $("#chkstatus").val();
		var checker = 0;
		var checkno = $("#checkno").val();
		var payto = $("#payto").val();
		var print = $("#chkprint").val();
		var clear = $("#chkclear").val();

		if(searchtype == "none") {
			checker = 0;
		}
		else if(searchtype == "chkdatediv") {
			if($("#datefrom").val() == "" || $("#dateto").val() == "") {
				checker = 0;
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
			else {
				checker = 1;
				datefrom = formatDate($("#datefrom").val());
				dateto = formatDate($("#dateto").val());
			}
		}
		else if(searchtype == "chkpaytodiv") {
			if($("#datefrom").val() == "" && $("#dateto").val() == "" && payto == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and check status.",
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
			else if($("#datefrom").val() != "" && $("#dateto").val() != "" && payto == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select pay to.",
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
			
			else {
				checker = 1;
				datefrom = formatDate($("#datefrom").val());
				dateto = formatDate($("#dateto").val());
			}
		}
		else if(searchtype == "chkprintdiv") {
			if($("#datefrom").val() == "" && $("#dateto").val() == "" && print == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and check status.",
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
			else if($("#datefrom").val() != "" && $("#dateto").val() != "" && print == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select print.",
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
			
			else {
				checker = 1;
				datefrom = formatDate($("#datefrom").val());
				dateto = formatDate($("#dateto").val());
			}
		}
		else if(searchtype == "chkcleardiv") {
			if($("#datefrom").val() == "" && $("#dateto").val() == "" && clear == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and clear status.",
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
			else if($("#datefrom").val() != "" && $("#dateto").val() != "" && clear == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select clear status.",
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
			
			else {
				checker = 1;
				datefrom = formatDate($("#datefrom").val());
				dateto = formatDate($("#dateto").val());
			}
		}
		else if(searchtype == "chkstatdiv") {
			if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && chkstatus == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and check status.",
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
			else if($("#datefrom1").val() != "" && $("#dateto1").val() != "" && chkstatus == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select check status.",
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
			
			else {
				checker = 1;
				datefrom = formatDate($("#datefrom1").val());
				dateto = formatDate($("#dateto1").val());
			}
		}
		else if(searchtype == "chkchecknodiv")
		{
			if(checkno == "")
			{
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill check number field.",
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
				checker = 1;
			}
		}
		
		if(checker == 1) {
			dataTable = $('#table-grid').DataTable({
				destroy: true,
				"serverSide": true,
				"columnDefs": [
		    		{ targets: 8, orderable: false, "sClass":"text-center" }
				],
				"ajax":{
					url:base_url+"Main_account/get_check_transactions", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'chkstatus': chkstatus, 'checkno': checkno, 'payto':payto, 'print':print, 'clear':clear},
					beforeSend:function(data) {
						$("body").LoadingOverlay("show"); 
					},
					complete: function() {
						$("body").LoadingOverlay("hide"); 
					},
					error: function() {  // error handling
						$(".table-grid-error").html("");
						$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
						$("#table-grid_processing").css("display","none");
					}
				}
			});
		}
	});

	$('#add_item_btn').click(function(e){
		$('#addItemModal').modal();
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var id = $(this).data('value');

	  	var currUrl = window.location.href;

	  	currUrl = currUrl.replace("check_transaction_history", "check_transaction_history_view");
	  	window.location = currUrl+"/"+id;
	});

	$('#markascleared').click(function(){
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/clear_check',
	  		data:{'chkno': $('#checknum').val() },
	  		beforeSend:function(data) {
				$("body").LoadingOverlay("show"); 
			},
			complete: function() {
				$("body").LoadingOverlay("hide"); 
			},
	  		success:function(data){

	  			data = JSON.parse(data);

	  			if(data.valid == true){
		  				$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#5cb85c',
						textColor: 'white'  
					});

		  		//setTimeout(function(){ window.close() }, 1000);

		  		setTimeout(function(){ 
		  			var currUrl = window.location.href;

		  			currUrl = currUrl.replace("check_transaction_history_view_tag", "check_transaction_history");
	  				//window.open(currUrl+"/"+id,'_blank');

	  				currUrl = currUrl.split('/');

	  				currUrl = currUrl[0]+'/'+currUrl[1]+'/'+currUrl[2]+'/'+currUrl[3]+'/'+currUrl[4]+'/'+currUrl[5]+'/'+currUrl[6];

	  				console.log(currUrl);
		  			window.location = currUrl;

		  		}, 1000);
		  		
	  			}
	  			else{
	  				$.toast({
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#FFA500',
						textColor: 'white'  
					});
	  			}

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Note',
				    text: 'Something went wrong. Please try again.',
				    icon: 'info',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
	  		}
	  	});
	});


	$('#table-grid').delegate(".btnTag", "click", function(){

	  	var id = $(this).data('value');

	  	var currUrl = window.location.href;

	  	currUrl = currUrl.replace("check_transaction_history", "check_transaction_history_view_tag");
	  	//window.open(currUrl+"/"+id,'_blank');

	  	window.location = currUrl+"/"+id;
	});

	//start
	$('#table-grid').delegate("#btntagapprove", "click", function(){
	  	var chkno = $(this).data('value');
		$(".checknum").val(chkno);
		$("p.p_check").html('Are you sure you want to clear check #'+chkno+'?');
	});

	$('#add_inventory_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		console.log(form.serialize());

	        $.ajax({
		            url: form.attr('action'),
		            type: form.attr('method'),
					data: form.serialize(),
		        }).done(function(response) {

		            var response = JSON.parse(response);

		            if(response.success===false)
		            {
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#d9534f',
							textColor: 'white'  
						});
		            }
		            else
		            {
		            	dataTable.draw();

		            	$('#addItemModal').modal('hide');
		            	$('#add_inventory_form')[0].reset();
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});
						
		            }

		    });
	});

	$('#delete_item_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		console.log(form.serialize());

	        $.ajax({
		            url: form.attr('action'),
		            type: form.attr('method'),
					data: form.serialize(),
		        }).done(function(response) {

		            var response = JSON.parse(response);

		            if(response.success===false)
		            {
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#d9534f',
							textColor: 'white'  
						});
		            }
		            else
		            {
		            	dataTable.draw();
		            	
		            	$('#deleteItemModal').modal('hide');
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});
						
		            }

		    });
	});

	$('#table-grid').delegate(".btnDelete","click", function(){
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_inventory/get_item',
	  		data:{'id':id},
	  		success:function(data){

	  			console.log(data);
	  			
	  			data = JSON.parse(data);

	  			$('#del_item_id').val(data.id);
	  			$('#info_desc').html(data.itemname);
				$('#deleteItemModal').modal();

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Error',
				    text: 'Something went wrong. Please try again.',
				    icon: 'error',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#d9534f',
					textColor: 'white'  
				});
	  		}
	  	});
	});

	$('#table-grid').delegate(".btnPrint","click", function(){
		var id = $(this).data('value');

		var currUrl = window.location.href;

	  	currUrl = currUrl.replace("check_transaction_history", "check_transaction_history_print");
	  	window.location = currUrl+"/"+id;
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