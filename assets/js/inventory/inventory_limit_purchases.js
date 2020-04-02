$(document).ready(function(){


currenSelectedItemId = "";
currentSelectedItemName = "";
currentSelectedUnit  = "";


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
					url:base_url+"Main_inventory/inventory_limit_purchases_list", // json datasource
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
		    		{ targets: 5, orderable: false, "sClass":"text-center" }],
		"ajax":{
			url:base_url+"Main_inventory/inventory_limit_purchases_list", // json datasource
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




  var options = {
  url: function(phrase) {
    return base_url+'Main_inventory/get_inventory'
  },
  getValue: function(element) {
    return element.itemname;
  },

  list: {
        onSelectItemEvent: function() {
            currenSelectedItemId = $("#f_itemname").getSelectedItemData().id;
            currentSelectedItemName = $("#f_itemname").getSelectedItemData().itemname;
            currentSelectedUnit = $("#f_itemname").getSelectedItemData().unit;
        },
    },


  ajaxSettings: {
    dataType: "json",
    method: "POST",
    data: {
      dataType: "json"
    }
  },

  preparePostData: function(data) {
    data.phrase = $("#f_itemname").val();
    return data;
  },

  requestDelay: 400
};

$("#f_itemname").easyAutocomplete(options);

$('.easy-autocomplete').css('width','100%');

$('#f_itemname').css('width', '100%');
$('#f_itemname').css('height', '40px');




$('#add_item_btn').click(function(){
	$('#addItemModal').modal();

	$('#f_itemname').prop("readonly",false);
	$('#f_id').prop("readonly",false);

	$('#add_purchase_limit_form')[0].reset();
});



	$('#table-grid').delegate(".btnUpdate", "click", function(){

	  	var id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_inventory/get_purchase_limit',
	  		data:{'id':id},
	  		success:function(data){
	  			data = JSON.parse(data);
	  			$('#f_itemname').val(data.itemname);
	  			$('#f_id').val(data.id);
				$('#f_start_date').val(data.startdate);
				$('#f_end_date').val(data.enddate);
				$('#f_quantity').val(data.quantity);
				$('#f_itemname').prop("readonly",true);
				$('#f_id').prop("readonly",true);

	  			$('#addItemModal').modal();
	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Note',
				    text: 'Something went wrong. Please try again.',
				    icon: 'info',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
	  		}
	  	});

	  	
	});


	$('#add_purchase_limit_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		form.append();	
		if(currenSelectedItemId > 0)
		{
			formdata = form.serializeArray();

					formdata.push({name: "currenSelectedItemId", value: currenSelectedItemId});

				        $.ajax({
					            url: form.attr('action'),
					            type: form.attr('method'),
								data: formdata,
					        }).done(function(response) {

					            var response = JSON.parse(response);
					            if(response.valid===false)
					            {
					            	$.toast({
									    heading: 'Note',
									    text: response.message,
									    icon: 'info',
									    loader: false,  
									    stack: false,
									    position: 'top-center', 
										allowToastClose: false,
										bgColor: '#FFA500',
										textColor: 'white'  
									});
					            }
					            else
					            {
					            	dataTable.draw();

					            	$('#addItemModal').modal('hide');
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
									
					            }

					    });
		}
		else
		{
			$.toast({
				    heading: 'Note',
				    text: 'Item does not existed in the database. Please check your data.',
				    icon: 'info',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
		}

		
	});

	$('#delete_item_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		

	        $.ajax({
		            url: form.attr('action'),
		            type: form.attr('method'),
					data: form.serialize(),
		        }).done(function(response) {

		            var response = JSON.parse(response);

		            if(response.success===false)
		            {
		            	$.toast({
						    heading: 'Note',
						    text: response.message,
						    icon: 'info',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#FFA500',
							textColor: 'white'  
						});
		            }
		            else
		            {
		            	dataTable.draw();
		            	
		            	$('#deleteItemModal').modal('hide');
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
						
		            }

		    });
	});


	$('#table-grid').delegate(".btnDelete","click", function(){

		var id = $(this).data('value');

		console.log(id);

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_inventory/get_purchase_limit',
	  		data:{'id':id},
	  		success:function(data){

	  			// console.log(data);
	  			
	  			data = JSON.parse(data);

	  			$('#del_item_id').val(data.id);
	  			$('#info_desc').html(data.itemname);
				$('#deleteItemModal').modal();

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Note',
				    text: 'Something went wrong. Please try again.',
				    icon: 'info',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
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
