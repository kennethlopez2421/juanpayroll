$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var token = $("#token").val();  
	
	$("#searchfilter").change(function() {
		var searchtype = $('#searchfilter').val(); // id ng dropdown

		if(searchtype == "sonodiv") {
			$('.ponodiv').show('slow');	
			$('.podatediv').hide('slow');
		}
		if(searchtype == "datediv") {
			$('.ponodiv').hide('slow');	
			$(".search_pono").val("");	 
			$('.podatediv').show('slow');
		}
	});

	// 102318 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page
	// some functions are found at assets/js/globalfunctions.js

	function fillDatatable(search, datefrom, dateto, sono) {
		var dataTable = $('#table-grid').DataTable({	
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [
				{ "orderable": false, "targets": [ 7 ], "className": "dt-center" }, {"orderable": false, "targets": [ 9 ],"className": "dt-center" },
				{ "orderable": false, "targets": [ 4 ], "className": "dt-center" }, { "orderable": false, "targets": [ 5 ], "className": "dt-center" },
				{ "orderable": false, "targets": [ 6 ], "className": "dt-center" }, { "orderable": false, "targets": [ 3 ], "className": "dt-center" }
			],
			"destroy": true,
			"ajax":{
				url :base_url+"sales/Sales_itinerary/table_sales_itinerary", // json datasource
				type: "post",  // method  , by default get
				data:{ 'search': search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono },
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
		sono		= $("#hdnSono").text();

		$("#searchfilter").val(search).change();
		$("#datefrom").val(datefrom);
		$("#dateto").val(dateto);
		$("#search_colno").val(sono);
	}
	else {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		sono		= $("#search_sono").val();
	}

	fillDatatable(search, datefrom, dateto, sono);


	$("#search_order").click(function() {
		search 		= $("#searchfilter").val();
		datefrom 	= formatDate($("#datefrom").val());
		dateto		= formatDate($("#dateto").val());
		sono		= $("#search_sono").val();

		$("#hdnSearch").text(search);
		$("#hdnDatefrom").text(datefrom);
		$("#hdnDateto").text(dateto);
		$("#hdnSono").text(sono);

		if (search == "datediv") {
			if(dateto == "" || datefrom == "") {
				toastMessage('Note:', 'No date found. Please choose a date.', 'error');
			}
			else {
				fillDatatable(search, datefrom, dateto, sono);
			}
		}
		else if (search == "sonodiv") {
			if(sono == "") {
				toastMessage('Note:', 'No sales order number found. Please fill in data.', 'error');
			}
			else {
				fillDatatable(search, datefrom, dateto, sono);
			}
		}
	});

	// Storing session data for ease of navigation after clicking ID Number
	$("#table-grid").delegate( "#btnSono", "click", function() {
		// for url
		url_sono = $(this).data("value");
		idno = $(this).data("idno");

		// get search variables
		search 		= $("#hdnSearch").text();
		datefrom 	= $("#hdnDatefrom").text();
		dateto		= $("#hdnDateto").text();
		sono		= $("#hdnSono").text();

		$.ajax({
			type: 'post',
			url: base_url+'sales/Sales_itinerary/storeSearchVariables',
			data:{ 'search': "SOI|" + search, 'datefrom': datefrom, 'dateto': dateto, 'sono': sono },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"sales/Sales_itinerary/itinerary_soview/" + token + "/" + url_sono + "/" + idno, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

	$(".BtnSaveItinerary").click(function(e){
		e.preventDefault();
  
		var count = $("#tdata").val(); // validation    

		var sonoarray=[];
		var truckarray=[];
		var datearray=[];
		var userarray=[];
		var idnoarray=[];
		var areaarray=[];

		sonoarray = [];
		truckarray = [];
		datearray = [];
		userarray = [];
		idnoarray = [];
		areaarray = [];

		for(i=0; i < count; i++ ) {
			var sono = $('#sono'+i).val(); 
			var truck = $('#valplateno'+i).val(); 
			var trandate = $('#trandate'+i).val(); 
			var username = $('#uname'+i).val(); 
			var idno = $('#idno'+i).val(); 
			var area = $('#area'+i).val(); 

			if (truck != "" || truck != 0) {  
				sonoarray.push(sono);
				truckarray.push(truck);
				datearray.push(trandate);
				userarray.push(username);
				idnoarray.push(idno);
				areaarray.push(area);
			}		
		}

		$.ajax({
			type:'post',
			url:base_url+'Main_sales/r_salesorderitineraryadd',
			data:{
			"sonoarray": sonoarray,
			"truckarray": truckarray,
			"datearray": datearray,
			"userarray": userarray,
			"idnoarray": idnoarray,
			"areaarray": areaarray,
			},
			success:function(data){
				if(data.success == 1) {     
					$.toast({
						heading: 'Success',
						text: 'You have successfully save Sales Order Itinerary.',
						icon: 'success',
						loader: false,  
						stack: false,
						position: 'top-center', 
						bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 3000
					});
					window.setTimeout(function(){
							window.location.href=base_url+"Main_sales/salesorder_itinerary/" + token;
						},500)
				} 	
				else {
					$.toast({
						heading: 'Note',
						text: 'No record found.',
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
			}   
		});    
	});


});

function SetTruckvalue(count) {
	var plateno = $('#plateno'+count).val();
	$('#valplateno'+count).val(plateno);
}

function isNumberKeyOnly(evt) {    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}

$('.searchDateFrom').datepicker({
	todayBtn: "linked",
	endDate:'+0d'
});	