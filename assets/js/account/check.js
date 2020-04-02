$(document).ready(function(){


$('#petty_dates_div').hide();
//$('#details_div').hide();
base_url = $("body").data('base_url');



function tofixed(x){
	return numberWithCommas(parseFloat(x).toFixed(2));
}
function numberWithCommas(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

var h;
$("#classification_submit_btn").click(function(e){
	h = $("#f_classification").val();
	

	if(h == 'Petty Cash Encashment'){

	//$("#classification_submit_btn").click(function(e){
		$('#classification_div').css('overflow',"hidden");
		$('#classification_div').css('position',"absolute");
		$('#classification_div').hide('slide', {direction: 'left'}, 1000);
		$('#petty_dates_div').stop().show('slide', {direction: 'right'}, 1000);

		setTimeout(function(){
			$('#classification_div').css('overflow',"visible");
			$('#petty_dates_div').css('position',"static");

		},2000);

		$('#submitpettydates').click(function(e){
			$('#petty_dates_div').css('overflow',"hidden");
			$('#petty_dates_div').css('position',"absolute");
			$('#petty_dates_div').hide('slide', {direction: 'left'}, 1000);
			$('#details_div').stop().show('slide', {direction: 'right'}, 1000);

			setTimeout(function(){
				$('#classification_div').css('overflow',"visible");
				$('#classification_div').css('position',"static");

			},2000);
		});

	}else{

	//$("#classification_submit_btn").click(function(e){
		$('#classification_div').css('overflow',"hidden");
		$('#classification_div').css('position',"absolute");
		$('#classification_div').hide('slide', {direction: 'left'}, 1000);
		$('#details_div').stop().show('slide', {direction: 'right'}, 1000);

		setTimeout(function(){
			$('#classification_div').css('overflow',"visible");
			$('#classification_div').css('position',"static");

		},2000);


	}
});

CheckClassification = "";

CheckEntries = [];
PettyDates = [];
CheckTotal = 0;
CheckInfoDate = "";
CheckInfoType = "";
CheckInfoSupplier = "";
CheckInfoReference = "";

$('#total_label').html(tofixed(CheckTotal));




resetData = function(){
	CheckClassification = "";

	CheckEntries = [];
	PettyDates = [];
	CheckTotal = 0;
	CheckInfoDate = "";
	CheckInfoType = "";
	CheckInfoSupplier = "";
	CheckInfoReference = "";

	$('#total_label').html(tofixed(CheckTotal));

	$('#details_div').hide();
	$('#petty_dates_div').hide();
	$('#classification_div').show();

	$('#t_body').html('');

	$('#f_date').val('');
	$('#f_type').val('');
	$('#f_supplier').val('');
	$('#f_reference').val('');

}

$('#classification_submit_btn').click(function(event){
	if($('#f_classification').val()==""){
		$.toast({
		    heading: 'Warning',
		    text: 'Please select a classification',
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
		CheckClassification = $('#f_classification').val();

		if(CheckClassification=="Petty Cash Encashment"){
			$('#classification_div').hide();
			$('#petty_dates_div').show();
		}
		else{
			$('#classification_div').hide();
			$('#details_div').show();
		}
		
	}
});


refreshTable = function(){
	var tableBody = "";
	for(var a = 0; a<CheckEntries.length; a++){
		var tableRow = "<tr>"+
	                        "<td>"+CheckEntries[a].date+"</td>"+
	                        "<td>"+CheckEntries[a].description+"</td>"+
	                        "<td>"+tofixed(CheckEntries[a].amount)+"</td>"+
	                        "<td>"+CheckEntries[a].gl_account+"</td>"+
	                        "<td>"+CheckEntries[a].gl_id+"</td>"+
	                        "<td>"+
	                        	"<button class='btn btn-sm btn-danger deletebtn' id='"+a+"'><i class='fa fa-trash'></i> Delete</button>"+
	                        "</td>"+
	                    "</tr>";
	    tableBody+= tableRow;
	}

	
	$('#t_body').html(tableBody);
	set_handler();

}


refreshTable2 = function(){
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



$('#add_check_entry_form').submit(function(event){
	event.preventDefault();

	var valid = true;

	if($('#ff_date').val()==""){
		valid = false;
	}
	if($('#ff_gl_account').val()==""){
		valid = false;
	}
	if($('#ff_description').val()==""){
		valid = false;
	}
	if($('#ff_amount').val()==""){
		valid = false;
	}


	if(valid==true){
		var entry = {
			date: $('#ff_date').val() ,
			description: $('#ff_description').val() ,
			amount: $('#ff_amount').val() ,
			gl_account: $("#ff_gl_account option:selected").text() ,
			gl_id :  $('#ff_gl_account').val()
		}




		CheckEntries.push(entry);


		refreshTable();

		CheckTotal = CheckTotal+parseFloat($('#ff_amount').val());

		$('#total_label').html(tofixed(CheckTotal));

		$('#add_check_entry_form')[0].reset();
		$('#addItemModal').modal('hide');
	}
	else
	{
		$.toast({
		    heading: 'Warning',
		    text: 'Please fill out all required fields',
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


$('#add_petty_date_form').submit(function(event){
	event.preventDefault();

	var entry = {
		date: $('#petty_date').val()
	}

	if($('#petty_date').val()==""){
			
			$.toast({
			    heading: 'Warning',
			    text: 'Date is required',
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
		//check if date already exists

			if(PettyDates.length>0){
				var existing = false;

				for(var a=0; a<PettyDates.length; a++){

					if(PettyDates[a].date==$('#petty_date').val()){

						existing = true;
					}
				}

				if(existing==false){
						PettyDates.push(entry);
						refreshTable2();
					}
				else{
					$.toast({
					    heading: 'Warning',
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


				PettyDates.push(entry);
			
				refreshTable2();
			}

			$('#add_petty_date_form')[0].reset();
			$('#addItemModal2').modal('hide');
	}

	
})


// $('#submitpettydates').click(function(e){
// 	if(CheckClassification=="Petty Cash Encashment"){
// 		if(PettyDates.length==0){
// 			$.toast({
// 			    heading: 'Warning',
// 			    text: "At least 1 date is required.",
// 			    icon: 'info',
// 			    loader: false,  
// 			    stack: false,
// 			    position: 'top-center', 
// 				allowToastClose: false,
// 				bgColor: '#FFA500',
// 				textColor: 'white'  
// 			});
// 		}
// 		else{
// 			$('#petty_dates_div').hide();
// 			$('#details_div').show();
// 		}
// 	}
// 	else{
// 		$('#petty_dates_div').hide();
// 		$('#details_div').show();
// 	}
// })


set_handler = function(){
	$('.deletebtn').click(function(e){

		CheckTotal = CheckTotal-parseFloat(CheckEntries[e.currentTarget.id].amount);
		$('#total_label').html(tofixed(CheckTotal));

		CheckEntries.splice(e.currentTarget.id, 1);
		refreshTable();
	});
}

set_handler2 = function(){
	$('.deletebtnpetty').click(function(e){
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
	CheckTotal = $('#total_check').val();
	//CheckInfoNote = $('#f_notes').val();

	if(CheckInfoDate =="" || CheckInfoType == "" || CheckInfoSupplier == ""|| CheckInfoReference == "" || CheckTotal == ""){
		$.toast({
		    heading: 'Warning',
		    text: 'Please make sure you have completed all check information.',
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
		var data = {
			'CheckClassification': CheckClassification,
			//'CheckEntries': CheckEntries,
			'CheckTotal': CheckTotal,
			'CheckInfoDate': CheckInfoDate,
			'CheckInfoType': CheckInfoType,
			'CheckInfoSupplier': CheckInfoSupplier,
			'CheckInfoReference': CheckInfoReference,
			//'CheckInfoNote': CheckInfoNote,
			'CheckPettyDates': PettyDates
		}


		$.ajax({
			  	type: 'post',
			  	url: base_url+'Main_account/save_check',
			  	data:{'data':data},
			  	beforeSend:function(data){
                $("body").LoadingOverlay("show"); 
	            },
	            complete: function(){
	                $("body").LoadingOverlay("hide"); 
	            },
             //    complete: function() {
             //    		$.LoadingOverlay("show");
            	// },

			  	success:function(data){
			  			
			  		data = JSON.parse(data);

			  		if(data.valid==false){
			  			$.toast({
						    heading: 'Warning',
						    text: data.message,
						    icon: 'info',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#FFA500',
							textColor: 'white'  
						});
                        $('#submitcheckbtn').prop('disabled', false);
                        $("#submitcheckbtn").text("Save Check");
			  		}
			  		else{
                        $("#submitcheckbtn").text("Save Check");
			  			$.toast({
						    heading: 'Success',
						    text: data.message,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});

						resetData();
			  		}
			  		


			  	},
			  	error: function(error){

			  		data = JSON.parse(error);

			  	}
		  });

	}
})



});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}
