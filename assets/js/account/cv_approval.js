$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.cvdatediv').show('slow');
	var searchtype = "none";
	// dataTable = $('#table-grid').DataTable({
	// 	//"processing": true,
	// 	"serverSide": true,
	// 	"columnDefs": [
	// 	    		{ targets: 4, orderable: false, "sClass":"text-center" }
	// 				],
	// 	"ajax":{
	// 		url:base_url+"Main_account/cashvoucher_table_approval", // json datasource
	// 		type: "post",  // method  , by default get
	// 		data:{'searchtype': searchtype},
	// 		beforeSend:function(data)
	// 		{
	// 			$("body").LoadingOverlay("show"); 
	// 		},
	// 		complete: function()
	// 		{
	// 			$("body").LoadingOverlay("hide"); 
	// 		},
	// 		error: function(){  // error handling
	// 			$(".table-grid-error").html("");
	// 			$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
	// 			$("#table-grid_processing").css("display","none");
	// 		}
	// 	}
	// });

	//start
	$('#table-grid').delegate(".btnbdview", "click", function(){
	  	var cvno = $(this).data('value');
	
	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/display_cashvoucher_Details',
	  		data:{'cvno':cvno},
	  		success:function(data)
	  		{
	  			var res1 = data.result1;
	  			var remarks = res1[0].remarks;
	  			if(remarks == null)
	  			{
	  				remarks = "";
	  			}
	  			if (data.success == 1) 
	  			{
	  	            document.getElementById('info_fullname').innerHTML = 
	  	            res1[0].payto.toUpperCase();
	  	            document.getElementById('info_notes').innerHTML = "Notes: "+ remarks;

	  	            document.getElementById('info_cvno').innerHTML = "CV #: "+res1[0].cvno;
	  	            document.getElementById('info_trandate').innerHTML = res1[0].trandate;

	  	            document.getElementById('info_funds').innerHTML = "Funds Date: "+ res1[0].fundsdate;
	  	            document.getElementById('info_status').innerHTML = "CV Status: "+ res1[0].type;
	  	            document.getElementById('info_create').innerHTML = "Created By: "+ res1[0].username;
	  	            document.getElementById('info_app').innerHTML = "Approved By: "+ res1[0].approver;


	  	            var dataTable1 = $('#table-grid1').DataTable({
						"processing": true,
						"serverSide": true,
						"ajax":{
							url :base_url+"Main_account/view_cashvoucher_Details", // json datasource
							type: "post",  // method  , by default get
							data:{'cvno':cvno},
							error: function(){  // error handling
								$(".table-grid1-error").html("");
								$("#table-grid1").append('<tbody class="table-grid1-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
								$("#table-grid1_processing").css("display","none");
							}
						}
					});

					dataTable1.destroy();
	  	        }
	  		}
	  	});

	});

	function fillDatatable(search, datefrom, dateto, cvno, cvname) {
		dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"order": [[ 2, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 0, 5 ], "className": "dt-center" }],
			"ajax":{
				url:base_url+"Main_account/cashvoucher_table_approval", // json datasource
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



	$("#cvsearchfilter").change(function() {
		var searchtype = $('#cvsearchfilter').val();

		   if(searchtype == "none")
	       {
			 $('.cvdatediv').hide('slow');	
			 $('.cvnodiv').hide('slow');
			 $('.cvnamediv').hide('slow');
	       }
	       else if(searchtype == "cvdatediv")
	       {
	         $('.cvdatediv').show('slow');
	         $('.cvnodiv').hide('slow');
	         $('.cvnamediv').hide('slow');		  
	       }
	       else if(searchtype == "cvnodiv")
	       {
	         $('.cvnodiv').show('slow');
	         $('.cvdatediv').hide('slow');	
	         $('.cvnamediv').hide('slow');
	         $("#cvno").val("");
	         $("#cvname").val("");
	       }
	       else if(searchtype == "cvnamediv")
	       {
	       	 $('.cvdatediv').hide('slow');	
	         $('.cvnodiv').hide('slow');
	         $('.cvnamediv').show('slow');  
	         $("#cvno").val("");
	         $("#cvname").val("");
	       }

	});

	$("#chkAll").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);

		if ($('input[name="CashVoucher"]:checked').length > 0) {
			$("#btnBatchApprove").prop("disabled", false);
		}
		else {
			$("#btnBatchApprove").prop("disabled", true);
		}
	});

	$("#table-grid").delegate( "#CashVoucher", "click", function() {
		if ($('input[name="CashVoucher"]:checked').length > 0) {
			$("#btnBatchApprove").prop("disabled", false);
		}
		else {
			$("#btnBatchApprove").prop("disabled", true);
		}

		if (($('input[name="CashVoucher"]:checked').length) == $('input[name="CashVoucher"]').length) {
			$("#chkAll").prop("checked", true);
		}
		else {
			$("#chkAll").prop("checked", false);
		}
	});

	$("#btnBatchApprove").on("click", function(){
		cvnoArray = [];
		$( ".CashVoucher:checkbox:checked" ).each(function( index ) {
			cvnoArray.push({"CashVoucher" : $( this ).data("value")});
		});

        if (cvnoArray == "") {
            toastMessage('Note', 'Please select at least one Cash Voucher', 'error', '#f0ad4e');
        }
        else {
			$("#batchApproveConfirmationModal").modal("toggle");
		}
	});


		$(".btnSearchSO").click(function(e){
		e.preventDefault();
		var search = $('#cvsearchfilter').val();
		var datefrom = formatDate($("#datefrom").val());
		var dateto = formatDate($("#dateto").val());
		var cvno = $("#cvno").val();
		var cvname = $("#cvname").val();

		// var date1 = $("#datefrom").val();
		// var date2 = $("#dateto").val();
		// var cvno = $("#cvno").val();
		// var cvname = $("#cvname").val();
		// var searchtype = $('#cvsearchfilter').val();

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

	$("#btnConfirmBatchApprove").on("click", function(){
		$.ajax({
			type: 'post',
			url: base_url+'main_account/batchApproveCashVoucher',
			data:{'cvnoArray': cvnoArray},
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
				}
				else if (data.success == 0) {
					toastMessage('Note', data.message, 'error', '#f0ad4e');
				}
			}
		});
	});


	//start
	// $(".btnSearchSO").click(function(e){
	// 	e.preventDefault();
	// 	var date1 = $("#datefrom").val();
	// 	var date2 = $("#dateto").val();
	// 	var cvno = $("#cvno").val();
	// 	var cvname = $("#cvname").val();
	// 	var searchtype = $('#cvsearchfilter').val();
	// 	var checker=0;
	// 	if(searchtype == "none")
	// 	{
	// 		checker=0;
	// 	}
	// 	else if(searchtype == "cvdatediv")
	// 	{
	// 		if(date1 == "" && date2 == "")
	// 		{
	// 			checker=0;
	// 			$.toast({
	// 			    heading: 'Note:',
	// 			    text: "Please fill date range field.",
	// 			    icon: 'info',
	// 			    loader: false,   
	// 			    stack: false,
	// 			    position: 'top-center',  
	// 			    bgColor: '#FFA500',
	// 				textColor: 'white',
	// 				allowToastClose: false,
	// 				hideAfter: 3000          
	// 			});
	// 		}
	// 		else
	// 		{
	// 			checker=1;
	// 		}
	// 	}
	// 	else if(searchtype == "cvnodiv")
	// 	{
	// 		if(cvno == "")
	// 		{
	// 			checker=0;
	// 			$.toast({
	// 			    heading: 'Note:',
	// 			    text: "Please fill cv number field.",
	// 			    icon: 'info',
	// 			    loader: false,   
	// 			    stack: false,
	// 			    position: 'top-center',  
	// 			    bgColor: '#FFA500',
	// 				textColor: 'white',
	// 				allowToastClose: false,
	// 				hideAfter: 3000          
	// 			});
	// 		}
	// 		else
	// 		{
	// 			checker=1;
	// 		}
	// 	}
	// 	else if(searchtype == "cvnamediv")
	// 	{
	// 		if(cvname == "")
	// 		{
	// 			checker=0;
	// 			$.toast({
	// 			    heading: 'Note:',
	// 			    text: "Please name field.",
	// 			    icon: 'info',
	// 			    loader: false,   
	// 			    stack: false,
	// 			    position: 'top-center',  
	// 			    bgColor: '#FFA500',
	// 				textColor: 'white',
	// 				allowToastClose: false,
	// 				hideAfter: 3000          
	// 			});
	// 		}
	// 		else
	// 		{
	// 			checker=1;
	// 		}
	// 	}

	// 	if(checker == 1)
	// 	{
	// 		var datefrom = formatDate(date1);
	// 		var dateto = formatDate(date2);
	// 		dataTable = $('#table-grid').DataTable({
	// 			destroy: true,
	// 			//"processing": true,
	// 			"serverSide": true,
	// 			"columnDefs": [
	// 	    		{ targets: 4, orderable: false, "sClass":"text-center" }
	// 				],
	// 			"ajax":{
	// 				url:base_url+"Main_account/cashvoucher_table_approval", // json datasource
	// 				type: "post",  // method  , by default get
	// 				data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'cvno': cvno, 'cvname': cvname},
	// 				beforeSend:function(data)
	// 				{
	// 					$.LoadingOverlay("show"); 
	// 				},
	// 				complete: function()
	// 				{
	// 					$.LoadingOverlay("hide"); 
	// 				},
	// 				error: function(){  // error handling
	// 					$(".table-grid-error").html("");
	// 					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
	// 					$("#table-grid_processing").css("display","none");
	// 				}
	// 			}
	// 		});
	// 	}

	// });
	//end

});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
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
