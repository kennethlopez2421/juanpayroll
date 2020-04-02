$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	
	var prno = $('.prv_prno').text();

	data = [];

	function reset() {
		data = [];
	}
	
	dataTable = $('#table-grid').DataTable({
		destroy: true,
		"serverSide": true,		
		"ajax":{
			url:base_url+"Main_cart/get_prt_history_view", // json datasource
			type: "post",  // method  , by default get
			data:{"prno": prno},
			beforeSend:function(data){
				$("#table-grid").LoadingOverlay("show");
			},
			complete:function(){
				$("#table-grid").LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('#addRowForm').submit(function(event){
		event.preventDefault();

		var form = $(this);

		itemid = $('#am_item').val();
		qty = $('#am_qty').val();
		unit = $('#am_uomid').val();
		remarks = $('#am_remarks').val();

		if(itemid != "" || qty != "") {
			data = {
				prno: prno,
				itemid: itemid,
				qty: qty,
				unit: unit,
				remarks: remarks
			}

			$.ajax({
		  		url: form.attr('action'),
	            type: form.attr('method'),
				data: {'data':data},
		  		beforeSend:function(data){
					$.LoadingOverlay("show");
				},
		  		success:function(data){
		  			if (data.success == 1) {
						$.toast({
						    heading: 'Success',
						    text: 'You have successfully saved the record.',
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});
					 	setTimeout(function(){
							$.LoadingOverlay("hide");
						},500);

						$('#addRowForm')[0].reset();
						$('#addRowModal').modal('hide');
						dataTable.draw();
						reset();
		  			}
		  		}
	  		});
		}
		else {
			$.toast({
			    heading: 'Warning',
			    text: 'Please fill out all required fields',
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#ffc107',
				textColor: 'white'  
			});
		}
	});

	$("#am_item").change(function () {
	    var itemid = $('#am_item').val();

	    $.ajax({
	  		url: base_url+"Main_cart/get_item_details",
            type: 'post',
			data: {'itemid':itemid},
	  		success:function(data){
  				$('#am_unit').val(data.unit);
  				$('#am_uomid').val(data.uomid);
	  		}
  		});
	});

});
