$(document).ready(function(){


//$('#div_2').hide();

base_url = $("body").data('base_url');


//temp
	currentSelectedItemName = "";
	currenSelectedItemId = "";
	currentSelectedUnit = "";
//temp


TransferDate = "";
Location = "";
Type = "";
Classificatio = "";
Entries = [];

function tofixed(x){
	return numberWithCommas(parseFloat(x).toFixed(2));
}
numberWithCommas = function(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


ReloadData = function(){
	$.ajax({
		  	type: 'post',
		  	url: base_url+'Main_inventory/get_adjustment_items',
		  	data:{'data': $('#iltnumberli').val() },
		  	success:function(data){
		  			
		  		data = JSON.parse(data);

		  		// console.log(data);

		  		if(data.valid==false){
		  			
		  		}
		  		else{

		  			// console.log(data);
		  			//assign values:
		  			TransferDate = data[0].trandate;
					FromLocation = data[0].itemlocid1;
					ToLocation = data[0].itemlocid2;

					console.log(data[0].trandate);

					
					$('#lbl_date').html('&nbsp;&nbsp;'+data[0].trandate);
					$('#lbl_loc').html('&nbsp;&nbsp;'+data[0].item_loc);
					if(data[0].adjtype=="plus"){
						$('#lbl_type').html('Positive Adjustment');
					}
					else{
						$('#lbl_type').html('Negative Adjustment');
					}
					
					$('#lbl_class').html('&nbsp;&nbsp;'+data[0].classification);
					$('#f2_notes').val(data[0].notes);

					for(var a = 0; a<data.length; a++){
						var entry = {
										currenSelectedItemId: data[a].itemid,
										currentSelectedItemName : data[a].itemname, 
										currentSelectedUnit: data[a].item_uom,
										currentItemQuantity: data[a].adjqty
									}

						Entries.push(entry);

						refreshTable();
					}

		  		}
		  		
		  	},
		  	error: function(error){

		  		$.toast({
				    heading: 'Error',
				    text: "Something went wrong. Please try again.",
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
}


// DO this on page loaf
ReloadData();
//







refreshTable = function(){

	var tableBody = "";

	for(var a = 0; a<Entries.length; a++){
		var tableRow = "<tr>"+
	                        "<td>"+Entries[a].currenSelectedItemId+"</td>"+
	                        "<td>"+Entries[a].currentSelectedItemName+"</td>"+
	                        "<td>"+tofixed(Entries[a].currentItemQuantity)+"</td>"+
	                        "<td>"+Entries[a].currentSelectedUnit+"</td>"+
	                    "</tr>";
	    tableBody+= tableRow;
	}

	
	$('#t_body').html(tableBody);

}


$('.printBtn').click(function(e){

	var currUrl = window.location.href;

	currUrl = currUrl.replace("inventory_adjustment_history_items_review", "inventory_adjustment_history_items_review_print");
	window.open (currUrl, '_blank');

	//$('.printBtn').attr("disabled","true");
	//$('.printBtn').attr("title","This document has already been printed.");
})




});
