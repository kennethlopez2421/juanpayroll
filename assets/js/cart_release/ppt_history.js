$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.ppfdatediv').show('slow');
	var searchtype = "ppfdatediv";
	
	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	var toast = $('#toast').text();

	if (toast == "ppftoast") {
		dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url+"Main_cart/get_ppt_history", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype': 'ppfnodiv', 'ppfno': $('#ppfnoflash').text()},
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
				url:base_url+"Main_cart/get_ppt_history", // json datasource
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

	$("#ppfsearchfilter").change(function() {
		var searchtype = $('#ppfsearchfilter').val();

   		if(searchtype == "ppfdatediv") {
			$('.ppfdatediv').show('slow');  
			$('.ppfnodiv').hide('slow');
			$('.ppffranchiseediv').hide('slow');
   		}
		else if(searchtype == "ppfnodiv") {
			$('.ppfnodiv').show('slow');
			$('.ppfdatediv').hide('slow');
			$('.ppffranchiseediv').hide('slow');
		}
		else if(searchtype == "ppffranchiseediv") {
			$('.ppffranchiseediv').show('slow');
			$('.ppfdatediv').hide('slow');
			$('.ppfnodiv').hide('slow');
		}
	});

	$(".btnSearchPPF").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var ppfno;
		var franchisee;
		var searchtype = $('#ppfsearchfilter').val();
		var checker = 0;

		if(searchtype == "none") {
			checker = 0;
		}
		else if(searchtype == "ppfdatediv") {
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
		else if(searchtype == "ppfnodiv") {
			if($("#ppfno").val() == "") {
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
				ppfno = $("#ppfno").val();
			}
		}
		else if(searchtype == "ppffranchiseediv") {
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
		
		if(checker == 1) {
			dataTable = $('#table-grid').DataTable({
				destroy: true,
				"serverSide": true,
				"ajax":{
					url:base_url+"Main_cart/get_ppt_history", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'ppfno': ppfno, 'franchisee': franchisee},
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