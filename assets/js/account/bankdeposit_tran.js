$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.bddatediv').show('slow');
	
	// 101018 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

    function fillDatatable(searchtype, datefrom, dateto, depno, bdtype, bdtype2) {

    	//alert(bdtype);
        dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url+"account/Bankdeposit_history/bankdeposit_table", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'depno': depno, 'bdtype':bdtype, 'bdtype2':bdtype2},
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

	$("#bdsearchfilter").change(function() {
		var searchtype = $('#bdsearchfilter').val();

		if(searchtype == "bddatediv") {
			$('.bddatediv').show('slow');
			$('.bdtypediv').hide('slow');
			$('.bdtype2div').hide('slow');
			$("#depno").val("");
			$("#bdtype").val("");
			$("#bdtype2").val("");		  
		}
		else if(searchtype == "bdnodiv") {
			$('.bdnodiv').show('slow');
			$('.bddatediv').hide('slow');
			$('.bdtype2div').hide('slow');
			$('.bdtypediv').hide('slow');	
			$("#depno").val("");
			$("#bdtype").val("");
			$("#bdtype2").val("");
		}
		else if(searchtype == "bdtypediv") {
			$('.bdnodiv').hide('slow');
			$('.bddatediv').show('slow');
			$('.bdtypediv').show('slow');	
			$('.bdtype2div').hide('slow');
			$("#depno").val("");
			$("#bdtype").val("");
			$("#bdtype2").val("");
		}
		else if(searchtype == "bdtype2div") {
			$('.bdnodiv').hide('slow');
			$('.bddatediv').show('slow');
			$('.bdtypediv').hide('slow');
			$('.bdtype2div').show('slow');	
			$("#depno").val("");
			$("#bdtype").val("");
			$("#bdtype2").val("");
		}
	});

	fillDatatable($('#bdsearchfilter').val(), date, date, "");

	$(".btnSearchSO").click(function(e){
		e.preventDefault();
		var searchtype = $('#bdsearchfilter').val();
		var datefrom = formatDate($("#datefrom").val());
		var dateto = formatDate($("#dateto").val());
		var depno = $("#depno").val();
		var bdtype = $("#bdtype").val();
		var bdtype2 = $("#bdtype2").val();
		//alert(bdtype);
		var checker = 0;

		if (searchtype == "bddatediv") {
			if(datefrom == "" && dateto == "") {
				checker = 0;
				toastMessage('Note:', "Please fill date range field.", 'error', '#FFA500');
			}
			else {
				checker = 1;
			}
		}
		else if (searchtype == "bdnodiv") {
			if(depno == "") {
				checker = 0;
				toastMessage('Note:', "Please fill deposit number field.", 'error', '#FFA500');
			}
			else {
				checker=1;
			}
		}
		else if (searchtype == "bdtypediv") {
			if(bdtype == "") {
				checker = 0;
				toastMessage('Note:', "Please fill account field.", 'error', '#FFA500');
			}else if(datefrom == "" && dateto == "") {
				checker = 0;
				toastMessage('Note:', "Please fill date range field.", 'error', '#FFA500');
			}
			else {
				checker=1;
			}
		}
		else if (searchtype == "bdtype2div") {
			if(bdtype2 == "") {
				checker = 0;
				toastMessage('Note:', "Please fill type field.", 'error', '#FFA500');
			}else if(datefrom == "" && dateto == "") {
				checker = 0;
				toastMessage('Note:', "Please fill date range field.", 'error', '#FFA500');
			}
			else {
				checker=1;
			}
		}
		
		if(checker == 1) {
			fillDatatable(searchtype, datefrom, dateto, depno, bdtype, bdtype2);
		}
	});

	$('#table-grid').delegate(".btnbdview", "click", function(){
	  	var depno = $(this).data('value');
	  	$('#table-grid1').DataTable().clear().destroy();
	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'account/Bankdeposit_history/display_bankdeposit_Details',
	  		data:{'depno':depno},
	  		success:function(data) {
	  			var res1 = data.result1;
	  			if (data.success == 1) {
	  	            document.getElementById('info_fullname').innerHTML = "Encoded By: "+ res1[0].encodedby.toUpperCase();
	  	            document.getElementById('info_notes').innerHTML = "Notes: "+ res1[0].notes;
	  	            document.getElementById('info_depno').innerHTML = "Dep No.: "+ res1[0].depno;
	  	            document.getElementById('info_trandate').innerHTML = res1[0].depdate;

	  	            var dataTable1 = $('#table-grid1').DataTable({
						destroy: true,
						"serverSide": true,
						"ajax":{
							url :base_url+"Main_account/view_bankdepo_Details", // json datasource
							type: "post",  // method  , by default get
							data:{'depno':depno},
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

});

function isNumberKeyOnly(evt) {    
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}