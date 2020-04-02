$(document).ready(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.cvdatediv').show('slow');
	var token = $("#hdnToken").val();
	//var searchtype = $('#cvsearchfilter').val();

	$("#cvsearchfilter").change(function() {
		var searchtype = $('#cvsearchfilter').val();

		if(searchtype == "none") {
			$('.cvdatediv').hide('slow');	
			$('.cvnodiv').hide('slow');
			$('.cvnamediv').hide('slow');
		}
		else if(searchtype == "cvdatediv") {
			$('.cvdatediv').show('slow');
			$('.cvnodiv').hide('slow');
			$('.cvnamediv').hide('slow');		  
		}
		else if(searchtype == "cvnodiv") {
			$('.cvnodiv').show('slow');
			$('.cvdatediv').hide('slow');	
			$('.cvnamediv').hide('slow');
			$("#cvno").val("");
			$("#cvname").val("");
		}
		else if(searchtype == "cvnamediv") {
			$('.cvdatediv').show('slow');	
			$('.cvnodiv').hide('slow');
			$('.cvnamediv').show('slow');  
			$("#cvno").val("");
			$("#cvname").val("");
		}
	});

	// 101118 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	function formatDate(date) {
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) month = '0' + month;
		if (day.length < 2) day = '0' + day;

		return [year, month, day].join('-');
	}

	function fillDatatable(search, datefrom, dateto, cvno, cvname) {
		dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"order": [[ 2, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 0, 5 ], "className": "dt-center" }],
			"ajax":{
				url:base_url+"account/Check_approval/get_checks_for_approval", // json datasource
				type: "post",  // method  , by default get
				data:{'search': search, 'datefrom': datefrom, 'dateto': dateto, 'cvno': cvno, 'cvname': cvname},
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

	search = $("#hdnSearch").text();

	if (search != "") {
		$("#sosearchfilter").val(search).change();
		$("#datefrom").val($("#hdnDatefrom").text());
		$("#dateto").val($("#hdnDateto").text());
		$("#cvno").val($("#hdnCvno").text());
		$("#cvname").val($("#hdnCvname").text());

		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		cvno		= $("#hdnCvno").text();
		cvname		= $("#hdnCvname").text();
	}
	else {
		search 		= $("#cvsearchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		cvno		= $("#cvno").val();
		cvname		= $("#cvname").val();
	}

	fillDatatable(search, datefrom, dateto, cvno, cvname);

	$(".btnSearchSO").click(function(e){
		e.preventDefault();
		var search = $('#cvsearchfilter').val();
		var datefrom = formatDate($("#datefrom").val());
		var dateto = formatDate($("#dateto").val());
		var cvno = $("#cvno").val();
		var cvname = $("#cvname").val();

		$('#hdnSearch').text(search);
		$('#hdnDatefrom').text(datefrom);
		$('#hdnDateto').text(dateto);
		$('#hdnCvno').val(cvno);
		$('#hdnCvname').val(cvname);

		var checker = 0;
		
		if (search == "cvdatediv") {
			if(datefrom == "" && dateto == "") {
				checker = 0;
				toastMessage('Note', 'Please fill date range field.', 'error', '#f0ad4e');
			}
			else {
				checker=1;
			}
		}
		else if(search == "cvnodiv") {
			if(cvno == "") {
				checker = 0;
				toastMessage('Note', 'Please fill cv number field.', 'error', '#f0ad4e');
			}
			else {
				checker = 1;
			}
		}
		else if(search == "cvnamediv") {
			if(cvname == "") {
				checker = 0;
				toastMessage('Note', 'Please name field.', 'error', '#f0ad4e');
			}
			else {
				checker = 1;
			}
		}

		if (checker == 1) {
			fillDatatable(search, datefrom, dateto, cvno, cvname);
		}
	});

	$("#chkAll").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);

		if ($('input[name="chkChkno"]:checked').length > 0) {
			$("#btnBatchApprove").prop("disabled", false);
		}
		else {
			$("#btnBatchApprove").prop("disabled", true);
		}
	});

	$("#table-grid").delegate( "#chkChkno", "click", function() {
		if ($('input[name="chkChkno"]:checked').length > 0) {
			$("#btnBatchApprove").prop("disabled", false);
		}
		else {
			$("#btnBatchApprove").prop("disabled", true);
		}

		if (($('input[name="chkChkno"]:checked').length) == $('input[name="chkChkno"]').length) {
			$("#chkAll").prop("checked", true);
		}
		else {
			$("#chkAll").prop("checked", false);
		}
	});

	$("#btnBatchApprove").on("click", function(){
		chknoArray = [];
		$( ".chkChkno:checkbox:checked" ).each(function( index ) {
			chknoArray.push({"chkno" : $( this ).data("value")});
		});

        if (chknoArray == "") {
            toastMessage('Note', 'Please select atleast one Check', 'error', '#f0ad4e');
        }
        else {
			$("#batchApproveConfirmationModal").modal("toggle");
		}
	});

	$("#btnConfirmBatchApprove").on("click", function(){
		$.ajax({
			type: 'post',
			url: base_url+'account/Check_approval/batchApprove',
			data:{'chknoArray': chknoArray},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				$.LoadingOverlay("hide");
				
				if (data.success == 1) {
					toastMessage('Success', data.message, 'success', '#5cb85c');

					search 		= $("#hdnSearch").text();
					datefrom 	= formatDate($("#hdnDatefrom").text());
					dateto		= formatDate($("#hdnDateto").text());
					cvno		= $("#hdnCvno").text();
					cvname		= $("#hdnCvname").text();

					fillDatatable(search, datefrom, dateto, cvno, cvname);
					$("#chkAll").prop("checked", false);
					$("#batchApproveConfirmationModal").modal("hide");

					location.reload();
				}
				else if (data.success == 0) {
					toastMessage('Note', data.message, 'error', '#f0ad4e');
				}
			}
		});
	});

	// Storing session data for ease of navigation after clicking Approve Button
	$("#table-grid").delegate( "#btnApprove", "click", function() {
		// for url
		url_id = $(this).data("value");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		cvno		= $("#hdnCvno").text();
		cvname		= $("#hdnCvname").text();

		$.ajax({
			type: 'post',
			url: base_url+'account/Check_approval/storeSearchVariables',
			data:{'search': "CA|" + search, 'datefrom': datefrom, 'dateto': dateto, 'cvno': cvno, 'cvname': cvname},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"Main_account/check_view_approval/" + token + "/" + url_id, '_self');
				$.LoadingOverlay("hide"); 
			}
		});
	});

});
