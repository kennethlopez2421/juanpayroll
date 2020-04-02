$(function(){
var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.drnodiv').show('slow');	
	$('.soSearchdiv').show('slow'); 
	var searchtype = "drnodiv";
	$("#table-grid").prop('hidden',false);
			var dataTable = $('#table-grid').DataTable({
				"destroy": true,
				
				"serverSide": true,
				"ajax":{
					type: "post", 
					url :base_url+"Main_sales/directsales_table_sched", // json datasource
					data:{'searchtype': searchtype},
					error: function(){  // error handling
						$(".table-grid-error").html("");
						$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
						$("#table-grid_processing").css("display","none");
					}
				}
			});

	//start
	$(".btnSearchSO").click(function(e){
		e.preventDefault();
	
		var dateto = $("#dateto").val();
		var datefrom = $("#datefrom").val();
		var sono = $("#sono").val();
		var drno = $("#drno").val();
		var searchtype = $('#sosearchfilter').val();
		var statfilter = $("#statfilter").val();


		var checker = 0;
		if(searchtype == "drnodiv")
		{
			if(drno != "")
			{
				checker=1;
			}
			else
			{
				checker=0;
			}
		}
		else if(searchtype == "none")
		{
			checker=1;
		}
		else
		{
			checker=0;
		}
		
		if(checker == 1)
		{
			$("#table-grid").prop('hidden',false);
			var dataTable = $('#table-grid').DataTable({
				"destroy": true,
				
				"serverSide": true,
				"order": [[ 2, "desc" ]],
				"ajax":{
					type: "post", 
					url :base_url+"Main_sales/directsales_table_sched", // json datasource
					data:{'drno': drno},
					error: function(){  // error handling
						$(".table-grid-error").html("");
						$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
						$("#table-grid_processing").css("display","none");
					}
				}
			});
		}


	});
	//end

	//start
	$('#table-grid').delegate(".btnDRsched", "click", function(){
	  	var drno_id = $(this).data('value');
		$("#drno_value").val(drno_id);
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_sales/display_DRsched_Details',
	  		data:{'drno_id':drno_id},
	  		success:function(data)
	  			{
	  			var res1 = data.result1;
	  			var res2 = data.result2;
	  			var res3 = data.result3;
	  			if (data.success == 1) 
	  			{
	  	            document.getElementById('info_fullname').innerHTML =
	  	            res2[0].lname.toUpperCase() + ", " + res2[0].fname.toUpperCase() +" "+ res2[0].mname.toUpperCase();
	  	            document.getElementById('info_drno').innerHTML = "DR No.:  " + res1;
	  	            document.getElementById('info_date').innerHTML = "Delivery Date:  " + res3;	
	  	            $("#date_value").val(res3);
	  			}
	  		}

	  	});
	});
	//end

	//start
	$(".btnReschedule").click(function(e){
		e.preventDefault();
		var drno_id = $("#drno_value").val();
		var newdate1 = $("#newdate").val();
		var prevdate1 = $("#date_value").val();
		var newdate = formatDate(newdate1);
		var prevdate = formatDate(prevdate1);
		var  checker = 0;
		if(newdate == "")
		{
			$.toast({
			    heading: 'Note',
			    text: "No date selected. Please check your data.",
			    icon: 'info',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#FFA500',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 7000          
				});
				checker = 0;
		}
		else
		{
			checker = 1;
		}
		
		if(checker == 1)
		{
			$.ajax({
		  		type: 'post',
		  		url: base_url+'Main_sales/save_dr_reschedDetails',
		  		data:{'drno_id':drno_id, 
		  		'newdate': newdate,
		  		 'prevdate': prevdate},
		  		beforeSend:function(data){
				},
		  		success:function(data)
		  			{
		  			if (data.success == 1) 
		  			{
							$.toast({
							    heading: 'Success',
							    text: "DR #"+ data.drno +" has been successfully reshedule.",
							    icon: 'success',
							    loader: false,  
							    stack: false,
							    position: 'top-center', 
							    bgColor: '#5cb85c',
								textColor: 'white',
								allowToastClose: false,
								hideAfter: 5000,
							});
							setTimeout(function(){
								$('#DRschedModal').modal('hide');
							},500);
							window.setTimeout(function(){location.reload()},4000)
							$("#newdate").val("");
		  			}
		  		}

		  	});
		}

	});
	//end


});


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

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}


$(document).ready(function(){
    $("#btnSearchSO").trigger("click");
});

