$(function(){
		var base_url = $('body').data('base_url');
				//var serialize = $('#addCA-form').serialize();
				var caTable = $('#caTable').DataTable({
						processing:false,
						serverSide:true,
						ajax:{
							url: base_url+'settings/Cashadvance/cajson',
						},
						columns:[
							{data:'caID'},
							{data:'description'},
						],
						columnDefs:[
						{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.caID,function(){

									var caID = $(this).data('id');
									var description = $(this).data('description');

									//will pass the data from var caID, description, etc
									$('.caID').val(caID);
									$('#editCADesc').val(description);

								});


								$(document).on('click','#delete-btn'+data.caID,function(){

									var caID = $(this).data('id');
									var description = $(this).data('description');
									$('.caID').val(caID);
								});

								var buttons = "";
								buttons += "<button type='button' id='edit-btn"+data.caID+"' data-id='"+data.caID+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editCAModal' class='btn btn-primary' style='width:40%;'> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.caID+"' data-id='"+data.caID+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delCAModal' class='btn btn-danger' style='width:40%;'> Delete</button>";

								return buttons;

							}

						}]


				});
 $('#searchButton').click(function(){
 	var searchText = $('#caTableTB').val();
 	caTable.search(searchText).draw();
 });
//add new Position to DB
		$('#addCABtn').click(function(){
		var description = $('#addCA_desc').val();
		console.log(description);
		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			//user_id:id
		};
			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Cashadvance/create',
				data: data,
				success:function(data){
						var result = JSON.parse(data);
						$('#addCABtn').attr('disabled',false);
						$('#addCABtn').html('Add');

						if(result.success == 0){
							$('#addCA_desc').val("");
							notificationError('Error',result.message);
							caTable.ajax.reload(null,false);
						}
						else{
							$.LoadingOverlay('show');
							$('#addCA_desc').val("");
							$('#addCAModal').modal('toggle');
							notificationSuccess('Success',result.message);
							caTable.ajax.reload(null,false);
							$.LoadingOverlay('hide');

						}
				}
			});
		});

		$('#addCAModal').on('hidden.bs.modal', function () {
			$('#addCA_desc').val("");
			$('#addCA_amount').val("");
		});

		//Update position selected from table
		//show the current position on the input field

		$('#editCABtn').click(function(){

		var caID = $('.caID').val();
		var description = $('#editCADesc').val();
		var data = {
			id:caID,
			description:description,
		};
		$.ajax({
			url: base_url+'settings/Cashadvance/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);

				if(result.success == 0){
					$('#editCAModal').modal('toggle');
					notificationError('Success',result.message);
					caTable.ajax.reload(null,false);
				}else{
					$.LoadingOverlay('show');
					$('#editCAModal').modal('toggle');
					notificationSuccess('Success',result.message);
					caTable.ajax.reload(null,false);
					$.LoadingOverlay('hide');
				}
			}


		});

	});
		$('.delCABtn').click(function(){
		var caID = $('.caID').val();
		var data = {
			id:caID
		};

		$.ajax({
			url: base_url+'settings/Cashadvance/destroy',
			type:'POST',
			data:data,
			success:function(data) {
				console.log(data)
				var result = JSON.parse(data);
				console.log(result)
				$.LoadingOverlay('show');
				$('#delCAModal').modal('toggle');
				notificationSuccess('Success',result.message);
				caTable.ajax.reload(null,false);
				$.LoadingOverlay('hide');

			}
		});

	});
});
