$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var pono = $('.pono').text();
	var apvno = $('.apvno').text();
	var token = $('#hdnToken').val();

	function tofixed(x){
		return numberWithCommas(parseFloat(x).toFixed(2));
	}

	function numberWithCommas(x){
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	dataTable = $('#table-grid2').DataTable({
		"serverSide": true,
		"ajax":{
			url:base_url+"account/APV_list/get_apvpo_process", // json datasource
			type: "post",  // method  , by default get
			data:{"apvno": apvno},
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

	dataTable3 = $('#table-grid3').DataTable({
		"serverSide": true,
		"ajax":{
			url: base_url + "Main_account/get_apvpo_payment_log", // json datasource
			type: "post",  // method  , by default get
			data:{"apvno": apvno},
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
	dataTable3.destroy();

	$('#table-grid2').delegate(".btnViewRcv", "click", function(){
 		var pono = $(this).data('pono');
 		var rcvno = $(this).data('value');
 		var printed = $(this).data('printed');
 		var suprefno = $(this).data('suprefno');
 		var potrandate = $(this).data('potrandate');
 		var itemtrandate = $(this).data('itemtrandate');
 		$('.m_pono').text(pono);
 		$('.m_rcvno').text(rcvno);
 		$('.m_suprefno').text(suprefno);
 		$('.m_potrandate').text(potrandate);
 		$('.m_itemtrandate').text(itemtrandate);

 		if (printed > 0){
 			$("#printedText").show();
 			$(".printBtn").hide();
 		}
 		else {
 			$("#printedText").hide();
 			$(".printBtn").show();
 		}

		dataTable2 = $('#table-grid').DataTable({
			"serverSide": true,
			"ajax":{
				url:base_url+"account/APV_list/get_rcvno_edit_breakdown", // json datasource
				type: "post",  // method  , by default get
				data:{"apvno": apvno, "rcvno": rcvno},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});

		$('#poReceiveModal').modal('show');
		dataTable2.destroy();
	});

	$('#apvpo_process_form').submit(function(event){
		event.preventDefault();

		// check the number of checks
		if ($('#hdn_chk_count').val() == 1) {
			if ($('#hdn_amount').val() == ""){
				$.toast({
				    heading: 'Error',
				    text: 'Please check the amount.',
				    icon: 'error',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#d9534f',
					textColor: 'white'  
				});
			}
			else if ($('#chk_number').val() == ""){
				$.toast({
				    heading: 'Error',
				    text: 'Please enter a valid check number.',
				    icon: 'error',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#d9534f',
					textColor: 'white'  
				});
			}
			else{
				$('#hdn_chk_number1').val($('#chk_number').val());
				$('#confirmProcessModal').modal();
			}
		}
		else {
			for (var i = 1; i <= $('#hdn_chk_count').val(); i++) {
				if ($('#chk_number' + i).val() == "") {
					$.toast({
					    heading: 'Error',
					    text: 'Fill up all required fields.',
					    icon: 'error',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#d9534f',
						textColor: 'white'  
					});
				}
				else {
					$('#hdn_chk_number' + i).val($('#chk_number' + i).val());
				}
			}
			$('#confirmProcessModal').modal();
		}
	});

	$('#apvpo_process_form2').submit(function(event){
		event.preventDefault();

		var form = $(this);

        $.ajax({
	            url: form.attr('action'),
	            type: form.attr('method'),
				data: form.serialize(),
	        }).done(function(response) {

            var response = JSON.parse(response);

            if(response.success === false)
            {
            	dataTable.draw();
				$('#confirmProcessModal').modal('hide');

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
            else {
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

				window.location.href = base_url + 'Main_account/apv_list/' + token;
				location(function(){
					$('#confirmProcessModal').modal('hide');
				}, 500);
            }
	    });
	});

	$('.printBtn').click(function(e){
		var currUrl = window.location.href;

		rcvno = $('.m_rcvno').text();

		window.open(
			'' + base_url + 'Main_account/rcv_print/' + token + '/' + rcvno,
			'_blank' // <- This is what makes it open in a new window.
		);

		$('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});

});