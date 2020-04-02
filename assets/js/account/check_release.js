$(document).ready(function(){


$('#details_div').hide();
base_url = $("body").data('base_url');




CheckEntries = [];
CheckNo = "";
CheckRemarks = "";


resetData = function(){

	CheckEntries = [];
	CheckNo = "";
	CheckRemarks = "";


	$('#details_div').hide();
	$('#classification_div').show();

}


function tofixed(x){
	return numberWithCommas(parseFloat(x).toFixed(2));
}
numberWithCommas = function(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


$('#classification_submit_btn').click(function(event){
	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/validate_check',
	  		data:{'chkno': $('#check_release_number').val() },
	  		success:function(data){

	  			data = JSON.parse(data);

	  			if(data.valid == true){
		  			
		  			$('#details_div').show();
					$('#classification_div').hide();
		  			
		  			var row = data.message
		  			//draw the table
		  			if(row.suppliername == null){
	                        	var tableRow = "<tr>"+
	                        "<td>"+row.id+"</td>"+
	                        "<td>"+row.chkdate+"</td>"+
	                        "<td>"+row.chkno+"</td>"+
	                        "<td>"+tofixed(row.amount)+"</td>"+
	                        "<td>"+row.username+"</td>"+
	                        "<td>"+row.checkstatus+"</td>"+
	                        "<td>"+row.idno+"</td>"+
	                        "<td><button class='btn modalButton btn-success'><i class='fa fa-arrow-right'></i> Release</button></td>"+
	                    "</tr>";
	                        }else{
	                        	var tableRow = "<tr>"+
	                        "<td>"+row.id+"</td>"+
	                        "<td>"+row.chkdate+"</td>"+
	                        "<td>"+row.chkno+"</td>"+
	                        "<td>"+tofixed(row.amount)+"</td>"+
	                        "<td>"+row.username+"</td>"+
	                        "<td>"+row.checkstatus+"</td>"+
	                        "<td>"+row.suppliername+"</td>"+
	                        "<td><button class='btn modalButton btn-success'><i class='fa fa-arrow-right'></i> Release</button></td>"+
	                    "</tr>";

	                        }
		  				
	                    
	                    $('#t_body').append(tableRow);

	                    $('#ff_id').val(row.chkno);

	                   set_handler_modal();
	  			}
	  			else{
	  				$.toast({
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#FFA500',
						textColor: 'white'  
					});
	  			}

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

set_handler_modal = function(){
	$('.modalButton').click(function(){
		$('#addItemModal').modal();
	})
}




refreshTable = function(){
	//$('#t_body').html("");


	var tableBody = "";

	for(var a = 0; a<CheckEntries.length; a++){
		var tableRow = "<tr>"+
	                        "<td>"+CheckEntries[a].date+"</td>"+
	                        "<td>"+CheckEntries[a].description+"</td>"+
	                        "<td>"+CheckEntries[a].amount+"</td>"+
	                        "<td>"+CheckEntries[a].gl_account+"</td>"+
	                        "<td>"+CheckEntries[a].gl_id+"</td>"+
	                        "<td>"+
	                        	"<button class='btn btn-sm btn-secondary deletebtn' id='"+a+"'><i class='fa fa-trash'></i></button>"+
	                        "</td>"+
	                    "</tr>";
	    tableBody+= tableRow;
	}

	
	$('#t_body').html(tableBody);
	set_handler();

}


refreshTable2 = function(){
	//$('#t_body').html("");


	var tableBody = "";

	for(var a = 0; a<PettyDates.length; a++){
		var tableRow = "<tr>"+
	                        "<td>"+PettyDates[a].date+"</td>"+
	                        "<td>"+
	                        	"<button class='btn btn-sm btn-secondary deletebtnpetty' id='"+a+"'><i class='fa fa-trash'></i></button>"+
	                        "</td>"+
	                    "</tr>";
	    tableBody+= tableRow;
	}

	
	$('#t_body_petty').html(tableBody);
	set_handler2();

}



$('#release_check_form').submit(function(event){
	event.preventDefault();

	var form = $(this);
	        $.ajax({
		            url: form.attr('action'),
		            type: form.attr('method'),
					data: form.serialize(),
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

		            	$('#release_check_form')[0].reset();
		            	$('#addItemModal').modal('hide');
		            	resetData();
		
		            	$.toast({
						    heading: 'Success',
						    text: response.message,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#5cb85c',
							textColor: 'white'  
						});
						$('#check_release_number').val("");

						
		            }

		    });
	

});


$('#add_petty_date_form').submit(function(event){
	event.preventDefault();

	var entry = {
		date: $('#petty_date').val()
	}

	if($('#petty_date').val()==""){
			
			$.toast({
			    heading: 'Note',
			    text: 'Date is required',
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#FFA500',
				textColor: 'white'  
			});

	}
	else{
		//check if date already exists

			if(PettyDates.length>0){
				var existing = false;

				for(var a=0; a<PettyDates.length; a++){

					if(PettyDates[a].date==$('#petty_date').val()){

						existing = true;
					}
				}

				if(existing==false){
						// console.log("do something");
						PettyDates.push(entry);
						// console.log(PettyDates);

						refreshTable2();
					}
				else{
					$.toast({
					    heading: 'Note',
					    text: 'Date already exists in the list.',
					    icon: 'info',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#FFA500',
						textColor: 'white'  
					});
				}
			}
			else{

				// console.log("do something 2");

				PettyDates.push(entry);
				// console.log(PettyDates);

				refreshTable2();
			}


			$('#add_petty_date_form')[0].reset();
			$('#addItemModal2').modal('hide');
	}

	
})


$('#submitpettydates').click(function(e){
	if(CheckClassification=="Petty Cash Encashment"){
		if(PettyDates.length==0){
			$.toast({
			    heading: 'Note',
			    text: "At least 1 date is required.",
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#FFA500',
				textColor: 'white'  
			});
		}
		else{
			$('#petty_dates_div').hide();
			$('#details_div').show();
		}
	}
	else{
		$('#petty_dates_div').hide();
		$('#details_div').show();
	}
})


set_handler = function(){
	$('.deletebtn').click(function(e){
		//console.log(e.currentTarget.id);

		CheckTotal = CheckTotal+parseFloat(CheckEntries[e.currentTarget.id].amount);
		$('#total_label').html(CheckTotal);

		CheckEntries.splice(e, 1);
		refreshTable();
	});
}

set_handler2 = function(){
	$('.deletebtnpetty').click(function(e){
		//console.log(e.currentTarget.id);
		PettyDates.splice(e, 1);
		refreshTable2();
	});
}



$('#submitcheckbtn').click(function(event){

	//CheckTotal
	CheckInfoDate = $('#f_date').val();
	CheckInfoType = $('#f_type').val();
	CheckInfoSupplier = $('#f_supplier').val();
	CheckInfoReference = $('#f_reference').val();
	CheckInfoNote = $('#f_notes').val();

	var data = {
		'CheckClassification': CheckClassification,
		'CheckEntries': CheckEntries,
		'CheckTotal': CheckTotal,
		'CheckInfoDate': CheckInfoDate,
		'CheckInfoType': CheckInfoType,
		'CheckInfoSupplier': CheckInfoSupplier,
		'CheckInfoReference': CheckInfoReference,
		'CheckInfoNote': CheckInfoNote,
		'CheckPettyDates': PettyDates
	}


	$.ajax({
		  	type: 'post',
		  	url: base_url+'Main_account/save_check',
		  	data:{'data':data},
		  	success:function(data){
		  			
		  		data = JSON.parse(data);

		  		// console.log(data);

		  		if(data.valid==false){
		  			$.toast({
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#FFA500',
						textColor: 'white'  
					});
		  		}
		  		else{
		  			$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#5cb85c',
						textColor: 'white'  
					});

					resetData();
		  		}
		  		


		  	},
		  	error: function(error){

		  		data = JSON.parse(error);

		  		// console.log(data);
		  		//$.toast({
				//    heading: 'Error',
				//    text: 'Something went wrong. Please try again.',
				//    icon: 'error',
				//    loader: false,  
				//    stack: false,
				//    position: 'top-center', 
				//	allowToastClose: false,
				//	bgColor: '#d9534f',
				//	textColor: 'white'  
				//});
		  	}
	  });


})



});
