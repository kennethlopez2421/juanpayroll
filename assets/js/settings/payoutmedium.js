$(function(){

	var base_url = $("body").data('base_url');

	 var payoutMedTable = $('#payoutMedTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Payoutmedium/payoutmediumjson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'payoutmediumid'},
							{data:'description'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.payoutmediumid,function(){

									var payoutMedId = $(this).data('id');
									var description = $(this).data('description');

									$('.payoutmediumid').val(payoutMedId);
									$('#editPayoutMedDesc').val(description);

								});

								$(document).on('click','#delete-btn'+data.payoutmediumid,function(){

									var payoutMedId = $(this).data('id');
									var description = $(this).data('description');
									$('.payoutmediumid').val(payoutMedId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.payoutmediumid+"' data-id='"+data.payoutmediumid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editPayoutMedModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.payoutmediumid+"' data-id='"+data.payoutmediumid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delPayoutMedModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}
						}]

					});



	$('#btnsearchPmedium').click(function(){
		var pMedium = $('.searchArea').val();
		$('#payoutMedTable').DataTable().destroy();

		var payoutMedTable = $('#payoutMedTable').DataTable({
 						processing:false,
 						serverSide:true,
 						searching: false,
 						ajax:{
 							url: base_url+'settings/Payoutmedium/payoutmediumjson',
 							beforeSend:function() {
 								$.LoadingOverlay('show');
 							},
							data: { searchValue: pMedium },
 							complete:function() {
 								$.LoadingOverlay('hide');
 							}
 						},
 						columns:[
 							{data:'payoutmediumid'},
 							{data:'description'}
 						],
 						columnDefs:[{
 							"targets":2,
 							"data":null,
 							"render":function(data, type, row, meta) {

 								$(document).on('click','#edit-btn'+data.payoutmediumid,function(){

 									var payoutMedId = $(this).data('id');
 									var description = $(this).data('description');

 									$('.payoutmediumid').val(payoutMedId);
 									$('#editPayoutMedDesc').val(description);

 								});

 								$(document).on('click','#delete-btn'+data.payoutmediumid,function(){

 									var payoutMedId = $(this).data('id');
 									var description = $(this).data('description');
 									$('.payoutmediumid').val(payoutMedId);
 									$('.info_desc').html(description)
 								});

 								var buttons = "";
 								buttons += "<center>";
 								buttons += "<button type='button' id='edit-btn"+data.payoutmediumid+"' data-id='"+data.payoutmediumid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editPayoutMedModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 								buttons += " <button type='button' id='delete-btn"+data.payoutmediumid+"' data-id='"+data.payoutmediumid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delPayoutMedModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
 								buttons += "</center>";

 								return buttons;
 							}
 						}]

 					});
	});


	$('#addPayoutMedBtn').click(function(){

		var description = $('#addPayoutMedDesc').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description
		};

		$.ajax({
			url: base_url+'settings/Payoutmedium/create',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#addPayoutMedBtn').attr('disabled',false);
				$('#addPayoutMedBtn').html('Add');

				if(result.success == 1) {
					$('#addPayoutMedDesc').val("");
					$('#addPayoutMedModal').modal('toggle');
					notificationSuccess('Success',result.message);
					payoutMedTable.ajax.reload(null,false);
				}else {
					$('#addPayoutMedDesc').val("");
					notificationError('Error',result.message);
					$('#addPayoutMedModal').modal('toggle');
				}
			}
		});
	});

	$('#addPayoutMedModal').on('hidden.bs.modal', function () {
		$('#addPayoutMedDesc').val("");
	});

	$('#editPayoutMedBtn').click(function(){

		var payoutMedId = $('.payoutmediumid').val();
		var description = $('#editPayoutMedDesc').val();

		var data = {
			id:payoutMedId,
			description:description
		};

		$.ajax({
			url: base_url+'settings/Payoutmedium/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);

				if(result.success == 0){
					notificationError('Error',result.message);
				}else{
					$('#editPayoutMedModal').modal('toggle');
					notificationSuccess('Success',result.message);
					payoutMedTable.ajax.reload();
				}
			}
		});

	});

	$('#delPayoutMedBtn').click(function(){

		var payoutMediumId = $('.payoutmediumid').val();

		var data = {
			id:payoutMediumId
		};

		$.ajax({
			url: base_url+'settings/Payoutmedium/destroy',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#delPayoutMedModal').modal('toggle');
				notificationSuccess('Success',result.message);
				payoutMedTable.ajax.reload(null,false);
			}
		});

	});


});
