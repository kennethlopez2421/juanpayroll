$(document).ready(function(){
base_url = $("body").data('base_url');


//temp
	currentSelectedItemName = "";
	currenSelectedItemId = "";
	currentSelectedUnit = "";
//temp


TransferDate = "";
FromLocation = "";
ToLocation = "";
TransferEntries = [];



ReloadData = function(){
	$.ajax({
		  	type: 'post',
		  	url: base_url+'Main_inventory/get_location_transfer_items',
		  	data:{'data': $('#iltnumberli').val() },
		  	success:function(data){
		  			
		  		data = JSON.parse(data);

		  		if(data.valid==false){
		  			
		  		}
		  		else{
		  			//assign values:
		  			TransferDate = data[0].trandate;
					FromLocation = data[0].itemlocid1;
					ToLocation = data[0].itemlocid2;

					$('#lbl_date').html('&nbsp;&nbsp;'+data[0].trandate);
					$('#lbl_from_loc').html('&nbsp;&nbsp;'+data[0].locfrom);
					$('#lbl_to_loc').html('&nbsp;&nbsp;'+data[0].locto);

					for(var a = 0; a<data.length; a++){
						var entry = {
										currenSelectedItemId: data[a].itemid,
										currentSelectedItemName : data[a].itemname, 
										currentSelectedUnit: data[a].description,
										currentItemQuantity: data[a].tranqty
									}

						TransferEntries.push(entry);

						refreshTable();
					}

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


// DO this on page loaf
ReloadData();
//


resetData = function(){

	TransferEntries = [];

	$('#add_inventory_entry_modal')[0].reset();

}


$('.printBtn').click(function(e){

	var currUrl = window.location.href;

	currUrl = currUrl.replace("inventory_location_transfer_transaction_history_items", "inventory_location_transfer_transaction_history_items_print");
	window.open (currUrl, '_blank');
})

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


function tofixed(x){
	return numberWithCommas(parseFloat(x).toFixed(2));
}
numberWithCommas = function(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}




refreshTable = function(){

	var tableBody = "";

	for(var a = 0; a<TransferEntries.length; a++){
		var tableRow = "<tr>"+
	                        "<td>"+TransferEntries[a].currenSelectedItemId+"</td>"+
	                        "<td>"+TransferEntries[a].currentSelectedItemName+"</td>"+
	                        "<td>"+TransferEntries[a].currentSelectedUnit+"</td>"+
	                        "<td>"+tofixed(TransferEntries[a].currentItemQuantity)+"</td>"+
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

		TransferEntries.splice(e, 1);
		refreshTable();
	});
}


$('#submitbtn').click(function(event){


	var data = {
		'TransferDate': TransferDate,
		'FromLocation': FromLocation,
		'ToLocation': ToLocation,
		'TransferEntries': TransferEntries,
		'Notes': $('#f2_notes').val(),
		'ILTNo': $('#iltnumberli').html()
	}

	// console.log(data);s


	$.ajax({
		  	type: 'post',
		  	url: base_url+'Main_inventory/save_inventory_trasnfer_location_edit',
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
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});

					resetData();

					ReloadData();
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
