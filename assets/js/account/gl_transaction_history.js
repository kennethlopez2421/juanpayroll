$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.gldatediv').show('slow');
	var searchtype = "none";

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	dataTable = $('#table-grid').DataTable({
		destroy: true,
		"serverSide": true,
		"ajax":{
			url:base_url+"Main_account/get_gl_transactions", // json datasource
			type: "post",  // method  , by default get
			data:{'searchtype': searchtype, 'datefrom': date, 'dateto': date},
			beforeSend:function(data) {
				$("body").LoadingOverlay("show"); 
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

	$("#glsearchfilter").change(function() {
		var searchtype = $('#glsearchfilter').val();

   		if(searchtype == "gldatediv") {
			$('.gldatediv').show('slow');  
			$('.glaccdiv').hide('slow');

   		}
		else if(searchtype == "glaccdiv") {
			$('.glaccdiv').show('slow');
			$('.gldatediv').hide('slow');
		}
	});

	$('#table-grid').delegate(".btnView", "click", function(){
	  	var id = $(this).data('value');
	  	var currUrl = window.location.href;
	  	currUrl = currUrl.replace("gl_transaction_history", "gl_transaction_history_view");
	  	window.open(currUrl+"/"+id,'_blank');
	});

	$(".btnSearchGL").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var searchtype = $('#glsearchfilter').val();
		var glaccount = $("#glaccount").val();
		var checker = 0;

		if(searchtype == "none") {
			checker = 0;
		}
		else if(searchtype == "gldatediv") {
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
		else if(searchtype == "glaccdiv") {
			if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && glaccount == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and account.",
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
			else if($("#datefrom1").val() != "" && $("#dateto1").val() != "" && glaccount == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select account.",
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
			else if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && glaccount != "none") {
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
					url:base_url+"Main_account/get_gl_transactions", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'glaccount': glaccount},
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
