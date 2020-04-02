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
		    		{ targets: 4, orderable: false, "sClass":"text-center" }
				],
				"ajax":{
					url:base_url+"Main_inventory/inventory_actual_count_transaction_history_records", // json datasource
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
		    		{ targets: 4, orderable: false, "sClass":"text-center" }],
		"ajax":{
			url:base_url+"Main_inventory/inventory_actual_count_transaction_history_records", // json datasource
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

	// $('.search-input-text').on('keyup blur', function(){   // for text boxes
	// 	var i =$(this).attr('data-column');  // getting column index
	// 	var v =$(this).val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// });

	$('#add_item_btn').click(function(e){
		$('#addItemModal').modal();
	});

	$('#table-grid').delegate(".btnUpdate", "click", function(){

	  	var id = $(this).data('value');

	  	var currUrl = window.location.href;

	  	currUrl = currUrl.replace("inventory_actual_count_transaction_history", "inventory_actual_count_transaction_history_update");
	  	window.location = currUrl+"/"+id;
	  	
	});


	$('#add_inventory_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		console.log(form.serialize());

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

		            	$('#addItemModal').modal('hide');
		            	$('#add_inventory_form')[0].reset();
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
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

	$('#delete_item_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		console.log(form.serialize());

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
		            	
		            	$('#deleteItemModal').modal('hide');
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
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


	$('#table-grid').delegate(".btnDelete","click", function(){
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_inventory/get_item',
	  		data:{'id':id},
	  		success:function(data){

	  			console.log(data);
	  			
	  			data = JSON.parse(data);

	  			$('#del_item_id').val(data.id);
	  			$('#info_desc').html(data.itemname);
				$('#deleteItemModal').modal();

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Error',
				    text: 'Something went wrong. Please try again.',
				    icon: 'error',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#d9534f',
					textColor: 'white'  
				});
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


function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}