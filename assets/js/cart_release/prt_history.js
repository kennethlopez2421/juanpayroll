$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.prdatediv').show('slow');
	var searchtype = "prdatediv";

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	var toast = $('#toast').text();

	if (toast == "prtoast") {
		dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url+"Main_cart/get_prt_history", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype': 'prnodiv', 'prno': $('#prnoflash').text()},
				beforeSend:function(data){
					$("#table-grid").LoadingOverlay("show");	
				},
				complete: function()
				{
					setTimeout(function(){
						$("#table-grid").LoadingOverlay("hide");
					},500); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});

		$.toast({
		    heading: 'Success',
		    text: 'You have successfully saved the record.',
		    icon: 'success',
		    loader: false,  
		    stack: false,
		    position: 'top-center', 
			allowToastClose: false,
			bgColor: 'yellowgreen',
			textColor: 'white'  
		});
	}
	else {
		dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url+"Main_cart/get_prt_history", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype': searchtype, 'datefrom': date, 'dateto': date},
				beforeSend:function(data){
					$("#table-grid").LoadingOverlay("show");
				},
				complete: function()
				{
					setTimeout(function(){
						$("#table-grid").LoadingOverlay("hide");
					},500); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	$("#prsearchfilter").change(function() {
		var searchtype = $('#prsearchfilter').val();

   		if(searchtype == "prdatediv") {
			$('.prdatediv').show('slow');  
			$('.prnodiv').hide('slow');
			$('.prfranchiseediv').hide('slow');
			$('.prstatdiv').hide('slow');
   		}
		else if(searchtype == "prnodiv") {
			$('.prnodiv').show('slow');
			$('.prdatediv').hide('slow');
			$('.prfranchiseediv').hide('slow');
			$('.prstatdiv').hide('slow');
		}
		else if(searchtype == "prfranchiseediv") {
			$('.prfranchiseediv').show('slow');
			$('.prdatediv').hide('slow');
			$('.prnodiv').hide('slow');
			$('.prstatdiv').hide('slow');
		}
		else if(searchtype == "prstatdiv") {
			$('.prstatdiv').show('slow');
			$('.prdatediv').hide('slow');  
			$('.prnodiv').hide('slow');
			$('.prfranchiseediv').hide('slow');
		}
	});

	$(".btnSearchPR").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var prno;
		var franchisee;
		var searchtype = $('#prsearchfilter').val();
		var prstatus = $("#prstatus").val();
		var checker = 0;

		if(searchtype == "none") {
			checker = 0;
		}
		else if(searchtype == "prdatediv") {
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
		else if(searchtype == "prnodiv") {
			if($("#prno").val() == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please input a RD Number.",
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
				prno = $("#prno").val();
			}
		}
		else if(searchtype == "prfranchiseediv") {
			if($("#franchisee").val() == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select a franchisee.",
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
				franchisee = $("#franchisee").val();
			}
		}
		else if(searchtype == "prstatdiv") {
			if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && prstatus == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and status.",
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
			else if($("#datefrom1").val() != "" && $("#dateto1").val() != "" && prstatus == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select status.",
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
			else if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && prstatus != "none") {
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
		
		if(checker == 1) {
			dataTable = $('#table-grid').DataTable({
				destroy: true,
				"serverSide": true,
				"ajax":{
					url:base_url+"Main_cart/get_prt_history", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'prno': prno, 'franchisee': franchisee, 'prstatus': prstatus},
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

	$('#table-grid').delegate(".btnApprove", "click", function(){
	  	$(".am_franchisee").html($(this).data('name'));
	  	$(".am_date").html($(this).data('date'));
	  	$(".am_prno").val($(this).data('prno'));
	  	$("#approveModal").modal();
	});

	$('#table-grid').delegate(".btnDelete", "click", function(){
	  	$(".dm_franchisee").html($(this).data('name'));
	  	$(".dm_date").html($(this).data('date'));
	  	$(".dm_prno").val($(this).data('prno'));
	  	$("#deleteModal").modal();
	});

	$('#approveForm').submit(function(event) {
		event.preventDefault();

		var form = $(this);

		$.ajax({
	  		url: form.attr('action'),
            type: form.attr('method'),
			data: {'prno':$(".am_prno").val()},
	  		beforeSend:function(data){
				$.LoadingOverlay("show");
			},
	  		success:function(data){
	  			if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: 'Package Release was successfully approved.',
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});
				 	setTimeout(function(){
				 		$.LoadingOverlay("hide");
						$('#approveModal').modal('hide');
						dataTable.draw();
					},500);
	  			}
	  		}
  		});
	});

	$('#deleteForm').submit(function(event) {
		event.preventDefault();

		var form = $(this);

		$.ajax({
	  		url: form.attr('action'),
            type: form.attr('method'),
			data: {'prno':$(".dm_prno").val()},
	  		beforeSend:function(data){
				$.LoadingOverlay("show");
			},
	  		success:function(data){
	  			if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: 'Package Release was successfully deleted.',
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});
				 	setTimeout(function(){
				 		$.LoadingOverlay("hide");
						$('#deleteModal').modal('hide');
						dataTable.draw();
					},500);
	  			}
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

function isNumberKeyOnly(evt) {    
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
 		return false;
	return true;
}