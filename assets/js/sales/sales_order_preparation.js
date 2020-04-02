$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var searchtype = $('#sosearchfilter').val(); // id ng dropdown
	var token = $("#hdnToken").val();

	// 102318 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page
	// some functions are found at assets/js/globalfunctions.js

	function fillDatatable(datefrom, dateto, warehouse) {
		var dataTable = $('#table-grid').DataTable({	
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 4 ], "className": "dt-center" }],
			"destroy": true,
			"ajax":{
				url :base_url+"sales/Sales_soprep/table_sales_preparation", // json datasource
				type: "post",  // method  , by default get
				data:{ 'datefrom': datefrom, 'dateto': dateto, 'warehouse': warehouse },
				beforeSend:function(data){
					$.LoadingOverlay("show"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				},
				complete: function(data) {
					$.LoadingOverlay("hide"); 
				}
			}
		});
	}

	search = $("#hdnSearch").text();

	if (search != "") {
		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		warehouse	= $("#hdnWarehouse").text();

		$("#datefrom").val(datefrom);
		$("#dateto").val(dateto);
		$("#search_warehouse").val(warehouse).change();
	}
	else {
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		warehouse	= $("#search_warehouse").val();
	}

	fillDatatable(datefrom, dateto, warehouse);

	$("#search_order").click(function() {
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		warehouse	= $("#search_warehouse").val();

		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnWarehouse").text(warehouse);

		if (datefrom == "" && dateto == "") {
			toasMessage('Note', 'No date found. Please choose a date.', 'error');
		}
		else if (warehouse == "none") {
			toasMessage('Note', 'No data found. Please choose warehouse.', 'error');
		}
		else {
			if ($("#search_warehouse").val() == "all") {
				$("#checkedAll").prop("hidden", true);
			}
			else {
				$("#checkedAll").prop("hidden", false);
			}

			fillDatatable(datefrom, dateto, warehouse);
		}
	});

	// Storing session data for ease of navigation after clicking SO Number
	$("#table-grid").delegate( "#btnSono", "click", function() {
		// for url
		url_sono = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		warehouse	= $("#hdnWarehouse").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_soprep/storeSearchVariables',
			data:{ 'search': "SOprep|" + "nosearchtype", 'datefrom': datefrom, 'dateto': dateto, 'warehouse': warehouse },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"sales/Sales_soprep/soprep_soview/" + token + "/" + url_sono + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	$("#checkedAll").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);

		if ($('input[name="prep_check"]:checked').length > 0) {
			$("#BtnSaveSOPrep").prop("disabled", false);
		}
		else {
			$("#BtnSaveSOPrep").prop("disabled", true);
		}
	});

	$("#table-grid").delegate( ".prep_check", "click", function() {
		if ($('input[name="prep_check"]:checked').length > 0) {
			$("#BtnSaveSOPrep").prop("disabled", false);
		}
		else {
			$("#BtnSaveSOPrep").prop("disabled", true);
		}

		if (($('input[name="prep_check"]:checked').length) == $('input[name="prep_check"]').length) {
			$("#checkedAll").prop("checked", true);
		}
		else {
			$("#checkedAll").prop("checked", false);
		}
	});

	$(".BtnSaveSOPrep").click(function(e){
		e.preventDefault();

		var token = $("#token").val();    
        var count = $("#tdata").val(); // validation    
        
        var checkbox_value = "";
        $("input.prep_check[type=checkbox]").each(function () {
        	var ischecked = $(this).is(":checked");
        	if (ischecked) {
        		checkbox_value +=  $(this).val() + ["|"];
        	}
        });

        $.ajax({
        	type:'post',
        	url:base_url+'Main_sales/table_save_soprep',
        	data:{
        		"checkbox_value":checkbox_value,
        	},
        	beforeSend:function(data)
        	{
        		$("body").LoadingOverlay("show"); 
        		$("#BtnSaveSOPrep").prop("disabled", true);
        	},
        	complete: function(){
        		$("body").LoadingOverlay("hide"); 
        	},
        	success:function(data){
        		if(data.success == 1)
        		{     
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
        				hideAfter: 3000
        			});
        			
        		} else{
        			$.toast({
        				heading: 'Note',
        				text: data.message,
        				icon: 'error',
        				loader: false,  
        				stack: false,
        				position: 'top-center', 
        				bgColor: '#f0ad4e',
        				textColor: 'white',
        				allowToastClose: false,
        				hideAfter: 3000
        			});

        		}
        		window.setTimeout(function(){
        			window.location.href=base_url+"Main_sales/sales_order_preparation/" + token;
        		},1000)
        	}   
        });    
    });
	
});