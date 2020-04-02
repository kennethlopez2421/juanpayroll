$(document).ready(function(){


$('#div_2').hide();

base_url = $("body").data('base_url');


//temp
	currentSelectedItemName = "";
	currenSelectedItemId = "";
	currentSelectedUnit = "";
//temp


TransferDate = "";
FromLocation = "";
Type = "";
TransferEntries = [];


resetData = function(){
	
	TransferDate = "";
	FromLocation = "";
	Type = "";
	TransferEntries = [];

	$('#div_2').hide();
	$('#div_1').show();

	$('#lbl_date').html('');
	$('#lbl_from_loc').html('');
	$('#lbl_type').html('');

	$('#add_inventory_entry_modal')[0].reset();

	$('#f1_date').val('');
	$('#f1_from_location').val('');
	$('#f1_type').val('');

}



$('#div_1_submit_button').click(function(event){
	var valid = true;
	var message = "";

	TransferDate = $('#f1_date').val();
	FromLocation = $('#f1_from_location').val();
	Type = $('#f1_type').val();


	if(TransferDate==""){
		valid = false;
		message += "<label>Transfer Date is required</label>";
	}
	if(FromLocation==""){
		valid = false;
		message += "<label>Location is required</label>";
	}
	if(Type==""){
		valid = false;
		message += "<label>Type is required</label>";
	}

	if(valid){

		$('#lbl_date').html('&nbsp;&nbsp;'+TransferDate);
		$('#lbl_from_loc').html('&nbsp;&nbsp;'+$('#f1_from_location option:selected').text());
		$('#lbl_type').html('&nbsp;&nbsp;'+$('#f1_type option:selected').text());

		$('#div_2').show();
		$('#div_1').hide();

		refreshTable();
	}
	else{
		$.toast({
		    heading: 'Note',
		    text: message,
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



var options = {

  url: function(phrase) {
    return base_url+'Main_inventory/get_inventory'
  },

  getValue: function(element) {
    return element.itemname;
  },

  list: {
        onSelectItemEvent: function() {
            currenSelectedItemId = $("#f2_inventory").getSelectedItemData().id;
            currentSelectedItemName = $("#f2_inventory").getSelectedItemData().itemname;
            currentSelectedUnit = $("#f2_inventory").getSelectedItemData().unit;
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
    data.phrase = $("#f2_inventory").val();
    return data;
  },

  requestDelay: 400
};

$("#f2_inventory").easyAutocomplete(options);

$('.easy-autocomplete').css('width','100%');







refreshTable = function(){

	var tableBody = "";

	for(var a = 0; a<TransferEntries.length; a++){
		var tableRow = "<tr>"+
	                        "<td>"+TransferEntries[a].currenSelectedItemId+"</td>"+
	                        "<td>"+TransferEntries[a].currentSelectedItemName+"</td>"+
	                        "<td>"+TransferEntries[a].currentSelectedUnit+"</td>"+
	                        "<td>"+TransferEntries[a].currentItemQuantity+"</td>"+
	                        "<td>"+
	                        	"<button class='btn btn-sm btn-danger deletebtn' id='"+a+"'><i class='fa fa-trash'></i> Delete</button>"+
	                        "</td>"+
	                    "</tr>";
	    tableBody+= tableRow;
	}

	
	$('#t_body').html(tableBody);
	set_handler();

}



$('#add_inventory_entry_modal').submit(function(event){
	event.preventDefault();

	var valid = true;
	var message = "";

	if(currentSelectedItemName=="" || currenSelectedItemId==""){
		valid = false;
		message += "<label>Inventory is required</label>";
	}

	if(isNaN($('#f2_quantity').val()) || $('#f2_quantity').val()=="" ){
		valid = false;
		message += "<label>Quantity field is required and only numbers are allowed.</label>";
	}


	if(valid){

		var entry = {
			currenSelectedItemId: currenSelectedItemId,
			currentSelectedItemName : currentSelectedItemName, 
			currentSelectedUnit: currentSelectedUnit,
			currentItemQuantity: $('#f2_quantity').val()
		}


			if(TransferEntries.length>0){
				var existing = false;

				var sameIndex = 0;

				for(var a=0; a<TransferEntries.length; a++){

					if(TransferEntries[a].currenSelectedItemId==currenSelectedItemId){

						existing = true;
						sameIndex = a;
					}
				}

				if(existing==false){
						TransferEntries.push(entry);
					}
				else{
					TransferEntries[sameIndex].currentItemQuantity = (parseFloat(TransferEntries[sameIndex].currentItemQuantity)+parseFloat($('#f2_quantity').val()));
				}
			}
			else{

				TransferEntries.push(entry);
			}

		refreshTable();

		$('#add_inventory_entry_modal')[0].reset();
		$('#addItemModal').modal('hide');

	}
	else{
		$.toast({
		    heading: 'Note',
		    text: message,
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
			    heading: 'Note',
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
				PettyDates.push(entry);
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
		TransferEntries.splice(e.currentTarget.id, 1);
		refreshTable();
	});
}

set_handler2 = function(){
	$('.deletebtnpetty').click(function(e){
		PettyDates.splice(e, 1);
		refreshTable2();
	});
}



$('#submitbtn').click(function(event){


	var data = {
		'TransferDate': TransferDate,
		'FromLocation': FromLocation,
		'Type': Type,
		'TransferEntries': TransferEntries,
		'Notes': $('#f2_notes').val()
	}

	var checker=0;


	var hasdata = data["TransferEntries"];
	if(hasdata != "")
	{
		checker=1;
	}
	else
	{
		checker=0;
		$.toast({
			    heading: 'Note',
			    text: "No record found. Please check your data.",
			    icon: 'info',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#FFA500',
				textColor: 'white'  
			});
	}



	if(checker==1)
	{
			$.ajax({
			  	type: 'post',
			  	url: base_url+'Main_inventory/save_inventory_adjustment_offset',
			  	data:{'data':data},
			  	success:function(data){
			  		data = JSON.parse(data);
			  		if(data.valid==true){
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
			  		else
			  		{
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
					    text: "Something went wrong. Please try again.",
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
	}
	


})



});
