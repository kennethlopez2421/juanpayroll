$(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
 	$('.datediv').show('slow');
	var tokken = $("#hdnTokken").val();

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

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
			$('.datediv').show('slow');	
			$('.statusdiv').show('slow');
			$('.nodiv').hide('slow');
			$("#searchNo").val("");
			$("#searchStatus").val("").change();
       	}
	});

	fillDatatable("datediv", date, date, "", "");

	function fillDatatable(searchtype, datefrom, dateto, no, status) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"order": [[ 1, "desc" ]],
			"serverSide": true,
			"columnDefs": [{ "orderable": false, "targets": [ 5 ], "sClass":"text-center" }],
			"ajax":{
				type: "post", 
				url :base_url+"sales/Sales_invoice/salesinvoice_table", // json datasource
				data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'no': no, 'status': status},
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

	//start search
	$(".btnSearch").click(function(e){
		e.preventDefault();
	
		var searchtype = $('#searchfilter').val();
		var dateto = formatDate($("#dateto").val());
		var datefrom = formatDate($("#datefrom").val());
		var no = $("#searchNo").val();
		var status = $("#searchStatus").val();

		var checker = 0;
		if(searchtype == "datediv") {
			if(dateto != "" || datefrom != "") {
				checker=1;
			}
			else {
				checker=0;
			}
		}
		else if(searchtype == "nodiv") {
			if(no != "") {
				checker=1;
			}
			else {
				$.toast({
				    heading: 'Note',
				    text: "No direct sales number found. Please fill in data.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 4000          
				});
				checker = 0;
			}
		}
		else if(searchtype == "statusdiv") {
			if(dateto != "" && datefrom != "" && status != "") {
				checker = 1;
			}
			else {
				$.toast({
				    heading: 'Note',
				    text: "No date or status selected.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 4000          
				});
				checker = 0;
			}
		}
		
		if(checker == 1) {
			$("#table-grid").prop('hidden',false);
			fillDatatable(searchtype, datefrom, dateto, no, status);
		}
	});
	//end

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
