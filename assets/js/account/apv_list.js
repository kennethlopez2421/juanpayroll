$(document).ready(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
    var token = $("#hdnToken").val();
	$('.apvdatediv').show('slow');

	// 100218 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

    function fillDatatable(searchtype, datefrom, dateto, apvstatus) {
        dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url+"account/APV_list/get_apv_list", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'apvstatus': apvstatus},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
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

	// reuseable toast call function for easeness and shorter code
	function toastMessage(heading, text, icon, bgcolor) {
		// #5cb85c success
		// #f0ad4e error
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

	function formatDate(date) {
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) month = '0' + month;
		if (day.length < 2) day = '0' + day;

		return [year, month, day].join('-');
	}

	search = $("#hdnSearchtype").text();

	if (search != "") {
		$("#apvsearchfilter").val($("#hdnSearchtype").text()).change();
		$("#datefrom").val($("#hdnDatefrom").text());
		$("#dateto").val($("#hdnDateto").text());
		$("#apvstatus").val($("#hdnStatus").text()).change();

		searchtype 	= $("#hdnSearchtype").text();
		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		apvstatus	= $("#hdnStatus").text();
	}
	else {
		searchtype 	= $("#apvsearchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		apvstatus	= $("#apvstatus").val();
	}
    
    fillDatatable(searchtype, datefrom, dateto, apvstatus);

	$("#apvsearchfilter").change(function() {
		var searchtype = $('#apvsearchfilter').val();

   		if (searchtype == "apvdatediv") {
			$('.apvdatediv').show('slow');  
			$('.apvstatdiv').hide('slow');
   		}
		else if(searchtype == "apvstatdiv") {
			$('.apvstatdiv').show('slow');
		}
	});

	$(".btnSearchAPV").on('click', function(e){
		e.preventDefault();

		searchtype 	= $("#apvsearchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		apvstatus	= $("#apvstatus").val();

		$('#hdnSearchtype').text(searchtype);
		$('#hdnDatefrom').text(datefrom);
		$('#hdnDateto').text(dateto);
		$("#hdnStatus").text(apvstatus);

		if (searchtype == "apvdatediv") {
			if (datefrom != "" && dateto != "") {
				fillDatatable(searchtype, datefrom, dateto, apvstatus);
			}
			else {
				toastMessage('Note', 'Please indicate date range', 'error', '#f0ad4e');
			}
		}
		else if (searchtype == "apvstatdiv") {
			if (datefrom != "" && dateto != "" && apvstatus != "none") {
				fillDatatable(searchtype, datefrom, dateto, apvstatus);
			}
			else {
				toastMessage('Note', 'Please indicate date range and apv status.', 'error', '#f0ad4e');
			}
		}
	});

	// Storing session data for ease of navigation after clicking APV Number
	$("#table-grid").delegate( "#btnApv", "click", function() {
		// for url
		url_apvno = $(this).data("apvno");

		// get search variables
		searchtype 	= $("#hdnSearchtype").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		apvstatus	= $("#hdnStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'account/APV_list/storeSearchVariables',
			data:{'search': "APVL|" + searchtype, 'datefrom': datefrom, 'dateto': dateto, 'apvstatus': apvstatus},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"account/APV_list/apvno_log/" + token + "/" + url_apvno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Edit Button
	$("#table-grid").delegate( "#btnEdit", "click", function() {
		// for url
		url_apvno = $(this).data("apvno");

		// get search variables
		searchtype 	= $("#hdnSearchtype").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		apvstatus	= $("#hdnStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'account/APV_list/storeSearchVariables',
			data:{'search': "APVL|" + searchtype, 'datefrom': datefrom, 'dateto': dateto, 'apvstatus': apvstatus},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_account/apvpo_edit/" + token + "/" + url_apvno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	// Storing session data for ease of navigation after clicking Process Button
	$("#table-grid").delegate( "#btnProcess", "click", function() {
		// for url
		url_apvno = $(this).data("apvno");

		// get search variables
		searchtype 	= $("#hdnSearchtype").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		apvstatus	= $("#hdnStatus").text();

		$.ajax({
			type: 'post',
			url: base_url+'account/APV_list/storeSearchVariables',
			data:{'search': "APVL|" + searchtype, 'datefrom': datefrom, 'dateto': dateto, 'apvstatus': apvstatus},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"account/APV_list/apvpo_process/" + token + "/" + url_apvno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	$('#table-grid').delegate(".btnApprove", "click", function(){
 		var apvno = $(this).data('value');

		$('#hdn_apvno').val(apvno);
		$('#apvno').html(apvno);
		$('#approveApvModal').modal();
	});

	$('#table-grid').delegate(".btnApv", "click", function(){
 		var apvstatus = $(this).data('apvstatus');

 		$.ajax({
            url: base_url+"Main_account/set_apvstatus",
            type: 'post',
			data: {'apvstatus': apvstatus},
        }).done(function(response) {
        	console.log(apvstatus);
	    });
	});

	$('#approveApvForm').submit(function(event){
		event.preventDefault();

		var form = $(this);

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
				$('#approveApvModal').modal('hide');
				
            	$.toast({
				    heading: 'Success',
				    text: response.message,
				    icon: 'success',
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
});
