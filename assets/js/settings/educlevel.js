$(function(){

	var base_url = $("body").data('base_url');

	 var educLevelTable = $('#educLevelTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Educationlevel/educationleveljson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'id'},
							{data:'description'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.id,function(){

									var id = $(this).data('id');
									var description = $(this).data('description');

									$('.educlevelid').val(id);
									$('#editDeductionDesc').val(description);

								});

								$(document).on('click','#delete-btn'+data.id,function(){

									var id = $(this).data('id');
									var description = $(this).data('description');

									$('.educlevelid').val(id);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editEducLevelModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delEducLevelModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";
								return buttons;
							}
						}]

					});



	$('#btnSearchEducLvl').click(function(){
		var educLvl = $('.searchArea').val();
		$('#educLevelTable').DataTable().destroy();
		var educLevelTable = $('#educLevelTable').DataTable({
 						processing:false,
 						serverSide:true,
 						searching: false,
 						ajax:{
 							url: base_url+'settings/Educationlevel/educationleveljson',
 							beforeSend:function() {
 								$.LoadingOverlay('show');
 							},
							data: { searchValue: educLvl},
 							complete:function() {
 								$.LoadingOverlay('hide');
 							}
 						},
 						columns:[
 							{data:'id'},
 							{data:'description'}
 						],
 						columnDefs:[{
 							"targets":2,
 							"data":null,
 							"render":function(data, type, row, meta) {

 								$(document).on('click','#edit-btn'+data.id,function(){

 									var id = $(this).data('id');
 									var description = $(this).data('description');

 									$('.educlevelid').val(id);
 									$('#editDeductionDesc').val(description);

 								});

 								$(document).on('click','#delete-btn'+data.id,function(){

 									var id = $(this).data('id');
 									var description = $(this).data('description');

 									$('.educlevelid').val(id);
 									$('.info_desc').html(description)
 								});

 								var buttons = "";
 								buttons += "<center>";
 								buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editEducLevelModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delEducLevelModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
 								buttons += "</center>";
 								return buttons;
 							}
 						}]

 					});

	});

	$('#addEducLevelBtn').click(function(){

		var description = $('#addEducLevelDesc').val();
		$(this).attr('disabled',true);
		$(this).html('Please Wait');


		var data = {
			description:description
		};

		$.ajax({
			url: base_url+'settings/Educationlevel/create',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');
				$('#addEducLevelBtn').attr('disabled',false);
				$('#addEducLevelBtn').html('Add');

				if(result.success == 1) {
					$('#addEducLevelDesc').val("");
					$('#addEducLevelModal').modal('toggle');
					notificationSuccess('Success',result.message);
					educLevelTable.ajax.reload(null,false);
				}else {
					$('#addEducLevelDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addEducLevelModal').on('hidden.bs.modal', function () {
		$('#addEducLevelDesc').val("");
	});

	$('#editEducLevelBtn').click(function(){

		var id = $('.educlevelid').val();
		var description = $('#editDeductionDesc').val();

		var data = {
			id:id,
			description:description
		};

		$.ajax({
			url: base_url+'settings/Educationlevel/update',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');

				$('#editEducLevelModal').modal('toggle');
				notificationSuccess('Success',result.message);
				educLevelTable.ajax.reload(null,false);
			}
		});

	});

	$('#delEducLevelBtn').click(function(){

		var educLevelId = $('.educlevelid').val();
		$.LoadingOverlay('show');

		var data = {
			id:educLevelId
		};

		$.ajax({
			url: base_url+'settings/Educationlevel/destroy',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('hide');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$('#delEducLevelModal').modal('toggle');
				notificationSuccess('Success',result.message);
				educLevelTable.ajax.reload(null,false);
			}
		});

	});


});
