$(function(){
	var base_url = $("body").data('base_url');
	 var deductionTable = $('#deductionTable').DataTable({
						processing:false,
						serverSide:true,
						ajax:{
							url: base_url+'settings/Deductions/deductionsjson',
						},
						columns:[
							{data:'deductionid'},
							{data:'description'},
						],
						columnDefs:[
						{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {
								$(document).on('click','#edit-btn'+data.deductionid,function(){

									var deductionId = $(this).data('id');
									var description = $(this).data('description');
									var deduction_status = $(this).data('deduction_status');
									$('.deductionid').val(deductionId);
									$('#description').val(description);
								});

								$(document).on('click','#delete-btn'+data.deductionid,function(){

									var deductionId = $(this).data('id');
									var description = $(this).data('description');
									var deduction_status = $(this).data('deduction_status');
									$('.deductionid').val(deductionId);
								});
								var buttons = "";
								buttons += "<button type='button' id='edit-btn"+data.deductionid+"' data-id='"+data.deductionid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editDeductionModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.deductionid+"' data-id='"+data.deductionid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delDeductionModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";

								return buttons;
							}
						}]

					});


 $('#searchButton').click(function(){
 	var searchText = $('#caTableTB').val();
 	deductionTable.search(searchText).draw();
 });

	$('#addDeductionBtn').click(function(){
		var description = $('#addDeductionDesc').val();
		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
		};

			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Deductions/create',
				data: data,
				success:function(data){


						var result = JSON.parse(data);
						$('#addDeductionBtn').attr('disabled',false);
						$('#addDeductionBtn').html('Add');

						if(result.success == 0){
							$('#addDeductionDesc').val("");
							$('#addDeductionAmount').val("");
							notificationError('Error',result.message);
							deductionTable.ajax.reload(null,false);
						}
						else{
							$.LoadingOverlay('show');
							$('#addDeductionDesc').val("");
							$('#addDeductionAmount').val("");
							$('#addDeductionModal').modal('toggle');
							notificationSuccess('Success',result.message);
							deductionTable.ajax.reload(null,false);
							$.LoadingOverlay('hide');

						}
				}
			});
	});

	$('#addDeductionModal').on('hidden.bs.modal', function () {
		$('#addDeductionDesc').val("");
		$('#addDeductionAmount').val("");
	})


	$('#editDeductionBtn').click(function(){
		var deductionId = $('.deductionid').val();
		var description = $('#description').val();
		var data = {
			id:deductionId,
			description:description,
		};

		$.ajax({
			url: base_url+'settings/Deductions/update',
			type:'POST',
			data:data,

			success:function(data) {

				var result = JSON.parse(data);

				if(result.success == 0){
					$('#editDeductionModal').modal('toggle');
					notificationError('Success',result.message);
					deductionTable.ajax.reload(null,false);
				}else{
					$.LoadingOverlay('show');
					$('#editDeductionModal').modal('toggle');
					notificationSuccess('Success',result.message);
					deductionTable.ajax.reload(null,false);
					$.LoadingOverlay('hide');

				}
			}
		});

	});


	$('#delDeductionBtn').click(function(){

		var deductionId = $('.deductionid').val();

		var data = {
			id:deductionId
		};

		$.ajax({
			url: base_url+'settings/Deductions/destroy',
			type:'POST',
			data:data,
			beforeSend:function(){
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');
				$.LoadingOverlay('show');
				$('#delDeductionModal').modal('toggle');
				notificationSuccess('Success',result.message);
				deductionTable.ajax.reload(null,false);
				$.LoadingOverlay('hide');

			}
		});

	});


});
