$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#token").val();
	
	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();
	
	function fillDatatable(searchtype, datefrom, dateto, pono, rcvno) {
		var dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"bDeferRender": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 6 ], "className": "dt-center" }],
			"ajax":{
				url :base_url+"purchase/POR_history/tbl_receivepo_tranHistory", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype':searchtype, 'datefrom':datefrom, 'dateto':dateto, 'pono':pono, 'rcvno':rcvno},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}
	
	function toastMessage(heading, text, icon, color) {
		$.toast({
			heading: heading,
			text: text,
			icon: icon,
			loader: false,  
			stack: false,
			position: 'top-center', 
			allowToastClose: false,
			bgColor: color,
			textColor: 'white'  
		});
	}

	fillDatatable('podatediv', date, date, "");

	$("#sosearchfilter").change(function() {
        var searchtype = $('#sosearchfilter').val(); // id ng dropdown
        var currentdate = new Date();
        
        if(searchtype == "ponodiv") {
        	$('.ponodiv').show('slow');	
        	$('.podatediv').hide('slow');
        	$(".search_pono").val("");
        	$('.porcvnodiv').hide('slow');
        	$(".search_rcvno").val("");
        	$(".searchDateTo").val("");
		 	$(".searchDateFrom").val("");
        }
        if(searchtype == "podatediv") {
        	$('.ponodiv').hide('slow');
        	$(".search_pono").val("");
        	$('.podatediv').show('slow');
        	$('.porcvnodiv').hide('slow');
        	$(".search_rcvno").val("");
        	$(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
		 	$(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
        }
        if(searchtype == "porcvnodiv"){
        	$('.ponodiv').hide('slow');
        	$(".search_pono").val("");
        	$('.podatediv').hide('slow');
        	$('.porcvnodiv').show('slow');
        	$(".search_rcvno").val("");
        	$(".searchDateTo").val("");
		 	$(".searchDateFrom").val("");

        }
    });

	$("#search_order").click(function() {
		searchtype = $('#sosearchfilter').val();
		datefrom = formatDate($("#datefrom").val());
		dateto = formatDate($("#dateto").val());
		pono = $('#search_pono').val();
		rcvno = $('#search_rcvno').val();
		checker = 0;

		if (searchtype == "podatediv") {
			if (datefrom == "" || dateto == "") {
				toastMessage('Note', 'Please fill in the Date fields.', 'info', '#FFA500');
				checker = 0;
			}
			else {
				checker = 1;
			}
		}
		else if (searchtype == "ponodiv") {
			if (pono == "") {
				toastMessage('Note', 'Please fill in the PO Number field.', 'info', '#FFA500');
				checker = 0;
			}
			else {
				checker = 1;
			}
		}else if (searchtype == "porcvnodiv") {
			if (rcvno == "") {
				toastMessage('Note', 'Please fill in the PO Receive Number field.', 'info', '#FFA500');
				checker = 0;
			}
			else {
				checker = 1;
			}
		}


		if (checker == 1) {
			fillDatatable(searchtype, datefrom, dateto, pono, rcvno);
		}
	});

	$('#table-grid').delegate(".btnDelete","click", function(){
		var rcvno = $(this).data('value');

		$(".recvno").val(rcvno); //user_id pk
		$(".del_rcvno").val(rcvno);
		$(".del_rcvno").text(rcvno);
		$('#deletePOReceiveModal').modal('show');
	});

	$('.deletePOReceiveBtn').click(function(e){
		e.preventDefault();

		var del_rcvno = $(".del_rcvno").val();

		$.ajax({
			type:'post',
			url:base_url+'purchase/POR_history/deletePOReceive',
			data:{'del_rcvno':del_rcvno},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			success:function(data){
				if (data.success == 1) {
					$.toast({
						heading: 'Success',
						text: data.message,
						icon: 'success',
						loader: false,  
						stack: false,
						position: 'top-center', 
						bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000
					});

					fillDatatable(
						$('#sosearchfilter').val(),
						formatDate($("#datefrom").val()),
						formatDate($("#dateto").val()),
						$('#search_pono').val()
						);
					$('#deletePOReceiveModal').modal('toggle'); //close modal
				}
				else {
					$.toast({
						heading: 'Note',
						text: data.message,
						icon: 'error',
						loader: false,   
						stack: false,
						position: 'top-center',  
						bgColor: '#f0ad4e',
						textColor: 'white'        
					});
				}
			}
		});

	});

	$('#table-grid').delegate(".btnApprove","click", function(){
		var rcvno = $(this).data('value');
		var id = $(this).data('id');
		var apprcvno = $(this).data('apprcvno');

		$(".rcvno").val(rcvno); //user_id pk
		$(".app_rcvno").val(apprcvno);
		$(".app_rcvno").text(rcvno);
		$(".poreceive_id").val(id);

		$('#approvePOReceiveModal').modal('show');
	});

	$('.approvePOReceiveBtn').click(function(e){
		e.preventDefault();

		var app_rcvno = $(".app_rcvno").val();
		var poreceive_id = $(".poreceive_id").val();

		$.ajax({
			type:'post',
			url:base_url+'purchase/POR_history/approvePOReceive',
			data:{'app_rcvno':app_rcvno, 'poreceive_id' : poreceive_id},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			success:function(data) {
				if (data.success == 1) {
					$.toast({
						heading: 'Success',
						text: data.message,
						icon: 'success',
						loader: false,  
						stack: false,
						position: 'top-center', 
						bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000
					});

					fillDatatable(
						$('#sosearchfilter').val(),
						formatDate($("#datefrom").val()),
						formatDate($("#dateto").val()),
						$('#search_pono').val()
						);
					//$(".processBtn").prop("disabled",true);
					window.setTimeout(function(){
						window.location.href=base_url+"Main_purchase/receivepo_tranHistory/" + token;
					},500)
				}
				else {
					$.toast({
						heading: 'Note',
						text: data.message,
						icon: 'error',
						loader: false,   
						stack: false,
						position: 'top-center',  
						bgColor: '#f0ad4e',
						textColor: 'white'        
					});
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
