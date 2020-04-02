$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.cohdatediv').show('slow');

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();
	
	dataTable = $('#table-grid').DataTable({
		destroy: true,
		"serverSide": true,
		"ajax":{
			url:base_url+"Main_account/get_coh_history", // json datasource
			type: "post",  // method  , by default get
			data:{'datefrom': date, 'dateto': date},
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

	$("#cohsearchfilter").change(function() {
		var searchtype = $('#cohsearchfilter').val();

		   if(searchtype == "cohdatediv")
	       {
	         $('.cohdatediv').show('slow');
	         $('.cohencodeddiv').hide('slow');	
	         $('.cohencashdiv').hide('slow');  
	         $("#enchashment").val("");
	         $("#encodedby").val("");   
	       }
	       else if(searchtype == "cohencashdiv")
	       {
	         $('.cohdatediv').show('slow');
	         $('.cohencodeddiv').hide('slow');	
	         $('.cohencashdiv').show('slow');
	         $("#enchashment").val("");
	         $("#encodedby").val("");   
	       }
	       else if(searchtype == "cohencodeddiv")
	       {
	         $('.cohdatediv').show('slow');
	         $('.cohencodeddiv').hide('slow');	
	         $('.cohencashdiv').hide('slow');
	         $('.cohencodeddiv').show('slow');   
	         $("#enchashment").val("");
	         $("#encodedby").val("");
	       }
	     
	});


	$(".btnSearchCOH").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var searchtype = $('#cohsearchfilter').val();
		var checker = 0;
		var encash = $('#enchashment').val();
		var encodedby = $('#encodedby').val();

		//alert(encash);

		if(searchtype == "none") {
			checker = 0;
		}
		else if(searchtype == "cohdatediv") {
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

		else if(searchtype == "cohencashdiv") {
			if($("#datefrom").val() == "" || $("#dateto").val() == "" || encash == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill date range and encashment field.",
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
		else if(searchtype == "cohencodeddiv") {
			if($("#datefrom").val() == "" || $("#dateto").val() == "" || encodedby == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill date range and encoded by field.",
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
				"ajax":{
					url:base_url+"Main_account/get_coh_history", // json datasource
					type: "post",  // method  , by default get
					data:{'datefrom': datefrom, 'dateto': dateto, 'encash':encash, 'searchtype':searchtype, 'encodedby':encodedby},
					beforeSend:function(data) {
						$("body").LoadingOverlay("show"); 
					},
					complete: function() {
						$("body").LoadingOverlay("hide"); 
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
