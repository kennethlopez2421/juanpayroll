$(document).ready(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();


	$('.divdate').show('slow');
    $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

           if(searchtype == "dividno")
           {
             $('.dividno').show('slow');
             $('.divdate').hide('slow');
             $("#idnosearch").val("");      
           }
           else if(searchtype == "divdate")
           {
             $('.divdate').show('slow');
             $('.dividno').hide('slow');    
             $("#idnosearch").val("");
           }
         
    });

    //start
	$(".searchBtn").click(function(e){
		e.preventDefault();
		var searchtype = $('#divsearchfilter').val();
		var idnosearch = $("#idnosearch").val();
		var date1 = $("#datefrom").val();
		var date2 = $("#dateto").val();

		

		var checker = 0;
		if(searchtype == "dividno")
		{
			if(idnosearch != "")
			{
				checker=1;
			}
			else
			{
				$.toast({
				    heading: 'Note',
				    text: "No ILT number to be search. Please fill in data.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 4000          
				});
				checker=0;
			}
		}
		else if(searchtype == "divdate")
		{
			if(date1 != "" && date2 != "")
			{
				checker=1;
			}
			else
			{
				$.toast({
				    heading: 'Note',
				    text: "No name to be search. Please fill in data.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 4000          
				});
				checker=0;
			}
		}
		
		
		
		if(checker == 1)
		{
			var datefrom = formatDate(date1);
			var dateto = formatDate(date2);


			var dataTable = $('#table-grid').DataTable({
				"destroy": true,
				"processing": true,
				"serverSide": true,
				"columnDefs": [
		    		{ targets: 5, orderable: false, "sClass":"text-center" }
				],
				"ajax":{
					url:base_url+"Main_inventory/inventory_ilt_receive_tables", // json datasource
					type: "post",  // method  , by default get,
					data:{'searchtype':searchtype,'datefrom':datefrom, 'dateto':dateto, 'idno':idnosearch},
					beforeSend:function(data)
		            {
		                $("#table-grid").LoadingOverlay("show"); 
		            },
		            complete: function()
		            {
		                $("#table-grid").LoadingOverlay("hide"); 
		            },
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

	var searchtype = "none";
	dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"columnDefs": [
		    		{ targets: 5, orderable: false, "sClass":"text-center" }
				],
		"ajax":{
			url:base_url+"Main_inventory/inventory_ilt_receive_tables", // json datasource
			type: "post",  // method  , by default get
			data:{'searchtype':searchtype},
			beforeSend:function(data)
            {
                $("#table-grid").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("#table-grid").LoadingOverlay("hide"); 
            },
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});


	$('#table-grid').delegate(".btnView", "click", function(){
	  	var id = $(this).data('value');
	  	$("#iltno").val("");
		$("#iltqty").val("");
		$("#totalqty").val("");

		if(id > 0)
		{
			$("#iltno").val(id);
		  	$.ajax({
			  		type: 'post',
			  		url: base_url+'Main_inventory/inventory_ilt_receive_details',
			  		data:{'iltno':id
			  		},
			  		success:function(data)
			  		{
						$("#totalqty").val(data.result);
			  		}
		  	});
		}
	  	


	});

	//start
	$(".saveILTbtn").click(function(e){
		e.preventDefault();
		var iltno = $('#iltno').val();
		var iltqty = $('#iltqty').val();
		var totalqty = $('#totalqty').val();
		var checker = 0;


		if(iltqty > 0)
		{
			if(totalqty == iltqty)
			{
				checker=1;
			}
			else
			{
				checker=0;
				$.toast({
					    heading: 'Note',
					    text: "Receive quantity must be equal to ILT total quantity. Please check your data.",
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000          
				});
			}
		}	
		else
		{
			$.toast({
			    heading: 'Note',
			    text: "No quantity found. Please check your data.",
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

		

		if(checker > 0)
		{
				$.ajax({
			  		type: 'post',
			  		url: base_url+'Main_inventory/save_inventory_ilt_receive',
			  		data:{'iltno':iltno
			  		},
			  		beforeSend:function(data){
							$(".saveILTbtn").prop("disabled",true);
					},
			  		success:function(data)
			  		{
			  			if(data.success == 1)
			  			{
			  				$.toast({
							    heading: 'Success',
							    text: "You have successfully received inventory location transfer.",
							    icon: 'success',
							    loader: false,  
							    stack: false,
							    position: 'top-center', 
							    bgColor: '#5cb85c',
								textColor: 'white',
								allowToastClose: false,
								hideAfter: 2000,
							});
			  			}
			  			// window.setTimeout(function(){location.reload()},2000)
		  				$(".saveILTbtn").prop("disabled",false);
						$("#iltno").val("");
						$("#iltqty").val("");
						$("#totalqty").val("");
						dataTable.draw();
			  		}
		  		});

		  		setTimeout(function(){
					$('#receiveItemModal').modal('hide');
				},500);
		  		
		}
	});	
	// End

	

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