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


	if(TransferDate==""){
		valid = false;
		message += "<label>Inventory Date is required</label>";
	}
	if(FromLocation==""){
		valid = false;
		message += "<label>Location is required</label>";
	}

	if(valid){

		$('#lbl_date').html('&nbsp;&nbsp;'+TransferDate);
		$('#lbl_loc').html('&nbsp;&nbsp;'+$('#f1_from_location option:selected').text());

		$('#div_2').show();
		$('#div_1').hide();
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
            //var selectedItemValue = $("#f2_inventory").getSelectedItemData().id;

            currenSelectedItemId = $("#f2_inventory").getSelectedItemData().id;
            currentSelectedItemName = $("#f2_inventory").getSelectedItemData().itemname;
            currentSelectedUnit = $("#f2_inventory").getSelectedItemData().unit;
            //console.log(currentSelectedItemName);

            //$("#inputTwo").val(selectedItemValue).trigger("change");
        },
        //onHideListEvent: function() {
        //	$("#inputTwo").val("").trigger("change");
    	//}
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



set_handler = function(){
	$('.deletebtn').click(function(e){
		//console.log(e.currentTarget.id);

		TransferEntries.splice(e.currentTarget.id, 1);
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



$('#submitbtn').click(function(event){


	var data = {
		'TransferDate': TransferDate,
		'FromLocation': FromLocation,
		'TransferEntries': TransferEntries,
		'Notes': $('#f2_notes').val()
	}

	// console.log(data);


	$.ajax({
		  	type: 'post',
		  	url: base_url+'Main_inventory/save_inventory_actual_count',
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


})



});
