$(function(){

	var base_url = $("body").data('base_url');

	 var workTypeTable = $('#workTypeTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Worktype/worktypejson',
							beforeSend:function() {
								$.LoadingOverlay('show')
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

									var workTypeId = $(this).data('id');
									var description = $(this).data('description');

									$('.worktypeid').val(workTypeId);
									$('#editWorkTypeDesc').val(description);

								});

								$(document).on('click','#delete-btn'+data.id,function(){

									var workTypeId = $(this).data('id');
									var description = $(this).data('description');
									$('.worktypeid').val(workTypeId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editWorkTypeModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delWorkTypeModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}
						}]

					});


	$('#btnSearchWorkType').click(function(){
		var workType = $('.searchArea').val();
		$('#workTypeTable').DataTable().destroy();

		var workTypeTable = $('#workTypeTable').DataTable({
 						processing:false,
 						serverSide:true,
 						searching: false,
 						ajax:{
 							url: base_url+'settings/Worktype/worktypejson',
 							beforeSend:function() {
 								$.LoadingOverlay('show')
 							},
							data: {searchValue: workType },
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

 									var workTypeId = $(this).data('id');
 									var description = $(this).data('description');

 									$('.worktypeid').val(workTypeId);
 									$('#editWorkTypeDesc').val(description);

 								});

 								$(document).on('click','#delete-btn'+data.id,function(){

 									var workTypeId = $(this).data('id');
 									var description = $(this).data('description');
 									$('.worktypeid').val(workTypeId);
 									$('.info_desc').html(description)
 								});

 								var buttons = "";
 								buttons += "<center>";
 								buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editWorkTypeModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delWorkTypeModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
 								buttons += "</center>";

 								return buttons;
 							}
 						}]

 					});
	})

	$('#addWorkTypeBtn').click(function(){

		var description = $('#addWorkTypeDesc').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description
		};

		$.ajax({
			url: base_url+'settings/Worktype/create',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#addWorkTypeBtn').attr('disabled',false);
				$('#addWorkTypeBtn').html('Add');

				if(result.success == 1) {
					$('#addWorkTypeDesc').val("");
					$('#addWorkTypeModal').modal('toggle');
					notificationSuccess('Success',result.message);
					workTypeTable.ajax.reload(null,false);
				}else {
					$('#addWorkTypeDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addWorkTypeModal').on('hidden.bs.modal', function () {
		$('#addWorkTypeDesc').val("");
	});

	$('#editWorkTypeBtn').click(function(){

		var workTypeId = $('.worktypeid').val();
		var description = $('#editWorkTypeDesc').val();

		var data = {
			id:workTypeId,
			description:description
		};

		$.ajax({
			url: base_url+'settings/Worktype/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				if(result.success == 0) {
					notificationError('Error',result.message);
				}else{
					$('#editWorkTypeModal').modal('toggle');
					notificationSuccess('Success',result.message);
					workTypeTable.ajax.reload(null,false);
				}
			}
		});

	});

	$('#delWorkTypeBtn').click(function(){

		var workTypeId = $('.worktypeid').val();

		var data = {
			id:workTypeId
		};

		$.ajax({
			url: base_url+'settings/Worktype/destroy',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#delWorkTypeModal').modal('toggle');
				notificationSuccess('Success',result.message);
				workTypeTable.ajax.reload(null,false);
			}
		});

	});


});
