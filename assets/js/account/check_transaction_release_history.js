$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.chkdatediv').show('slow');
	var searchtype = "none";

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	dataTable = $('#table-grid').DataTable({
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		// "columnDefs": [
		//     		{ targets: 6, orderable: false, "sClass":"text-center" }
		// 		],
		"ajax":{
			url:base_url+"Main_account/get_check_release_transactions", // json datasource
			type: "post",  // method  , by default get
			data:{'searchtype': searchtype, 'datefrom': date, 'dateto': date},
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

	$("#chksearchfilter").change(function() {
		var searchtype = $('#chksearchfilter').val();

   		if(searchtype == "chkdatediv") {
			$('.chkdatediv').show('slow');  
			$('.checknodiv').hide('slow');
			$('.chkstatdiv').hide('slow');
			$('.userdiv').hide('slow');
			$('.paytodiv').hide('slow');

			$('.chkstatus').val('');
			$('.checkno').val('');
			$('.susername').val('');
			$('.payto').val('');

   		}
		else if(searchtype == "chkstatdiv") {
			$('.chkstatdiv').show('slow');
			$('.checknodiv').hide('slow');
			$('.chkdatediv').hide('slow');
			$('.userdiv').hide('slow');
			$('.paytodiv').hide('slow');

			$('.chkstatus').val('');
			$('.checkno').val('');
			$('.susername').val('');
			$('.payto').val('');
		}
		else if(searchtype == "checknodiv") {
			$('.checknodiv').show('slow');
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').show('slow');
			$('.userdiv').hide('slow');
			$('.paytodiv').hide('slow');

			$('.chkstatus').val('');
			$('.checkno').val('');
			$('.susername').val('');
			$('.payto').val('');
		}
		else if(searchtype == "userdiv") {
			$('.checknodiv').hide('slow');
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').show('slow');
			$('.userdiv').show('slow');
			$('.paytodiv').hide('slow');

			$('.chkstatus').val('');
			$('.checkno').val('');
			$('.susername').val('');
			$('.payto').val('');
		}

		else if(searchtype == "paytodiv") {
			$('.checknodiv').hide('slow');
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').show('slow');
			$('.userdiv').hide('slow');
			$('.paytodiv').show('slow');

			$('.chkstatus').val('');
			$('.checkno').val('');
			$('.susername').val('');
			$('.payto').val('');
		}
	});

	$(".btnSearchChk").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var searchtype = $('#chksearchfilter').val();
		var chkstatus = $("#chkstatus").val();
		var checkno = $("#checkno").val();
		var payto = $("#payto").val();
		var susername = $("#susername").val();

		var checker = 0;

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
		else if(searchtype == "chkstatdiv") {
			if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && chkstatus == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and apv status.",
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
				    text: "Please select apv status.",
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
			else if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && chkstatus != "none") {
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
				datefrom = formatDate($("#datefrom1").val());
				dateto = formatDate($("#dateto1").val());
			}
		}
		else if(searchtype == "checknodiv") {
			if($("#datefrom").val() == "" && $("#dateto").val() == "" && checkno == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and check no status.",
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
			else if($("#datefrom").val() != "" && $("#dateto").val() != "" && checkno == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please enter check no.",
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
			else if($("#datefrom").val() == "" && $("#dateto").val() == "" && checkno != "") {
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
		else if(searchtype == "userdiv") {
			if($("#datefrom").val() == "" && $("#dateto").val() == "" && susername == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and username.",
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
			else if($("#datefrom").val() != "" && $("#dateto").val() != "" && susername == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please enter username.",
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
			else if($("#datefrom").val() == "" && $("#dateto").val() == "" && susername != "") {
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

		else if(searchtype == "paytodiv") {
			if($("#datefrom").val() == "" && $("#dateto").val() == "" && payto == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and pay to field.",
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
			else if($("#datefrom").val() != "" && $("#dateto").val() != "" && payto == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please enter pay to field.",
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
			else if($("#datefrom").val() == "" && $("#dateto").val() == "" && payto != "") {
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
		
		if(checker == 1) {
			dataTable = $('#table-grid').DataTable({
				destroy: true,
				"serverSide": true,
				"order": [[ 0, "desc" ]],
				// "columnDefs": [
		  //   		{ targets: 6, orderable: false, "sClass":"text-center" }
				// 	],
				"ajax":{
					url:base_url+"Main_account/get_check_release_transactions", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'chkstatus': chkstatus, 'checkno':checkno, 'susername':susername, 'payto':payto},
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

	  	currUrl = currUrl.replace("check_transaction_release_history", "check_transaction_history_view");
	  	window.open(currUrl+"/"+id,'_self');	
	});

	$('#table-grid').delegate(".btnTag", "click", function(){

	  	var id = $(this).data('value');

	  	var currUrl = window.location.href;

	  	currUrl = currUrl.replace("check_transaction_history", "check_transaction_history_view_tag");
	  	window.open(currUrl+"/"+id,'_self');

	  	window.location = currUrl+"/"+id;  	
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