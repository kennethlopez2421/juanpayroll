$(function(){

	var base_url = $("body").data('base_url');
	var count = 0;
	 var levelTable = $('#levelTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Level/leveljson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'position'},
							{data: 'hierarchy_lvl'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.position_id,function(){

									var levelId = $(this).data('id');
									var description = $(this).data('description');
									var hierarchy = $(this).data('level');

									$('.levelid').val(levelId);
									$('#description').val(description);
									$('#hierarchy').val(hierarchy);
									$('#current_desc').val(description);

								});

								$(document).on('click','#delete-btn'+data.position_id,function(){

									var levelId = $(this).data('id');
									var description = $(this).data('description');
									$('.levelid').val(levelId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.position_id+"' data-id='"+data.position_id+"' data-description='"+data.position+"' data-level = '"+data.hierarchy_lvl+"' data-toggle='modal' data-target='#editLevelModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.position_id+"' data-id='"+data.position_id+"' data-description='"+data.position+"' data-toggle='modal' data-target='#delLevelModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";
								return buttons;
							}
						}]

					});

	$('#btnSearchEmpLvl').click(function(){
		var empLvl = $('.searchArea').val();
		$('#levelTable').DataTable().destroy();

		var levelTable = $('#levelTable').DataTable({
 						processing:false,
 						serverSide:true,
 						searching: false,
 						ajax:{
 							url: base_url+'settings/Level/leveljson',
 							beforeSend:function() {
 								$.LoadingOverlay('show');
 							},
							data: { searchValue: empLvl },
 							complete:function() {
 								$.LoadingOverlay('hide');
 							}
 						},
 						columns:[
							{data:'position'},
							{data: 'hierarchy_lvl'}
 						],
 						columnDefs:[{
 							"targets":2,
 							"data":null,
 							"render":function(data, type, row, meta) {

 								$(document).on('click','#edit-btn'+data.levelid,function(){

 									var levelId = $(this).data('id');
 									var description = $(this).data('description');

 									$('.levelid').val(levelId);
 									$('#description').val(description);

 								});

 								$(document).on('click','#delete-btn'+data.levelid,function(){

 									var levelId = $(this).data('id');
 									var description = $(this).data('description');
 									$('.levelid').val(levelId);
 									$('.info_desc').html(description)
 								});

 								var buttons = "";
 								buttons += "<center>";
 								buttons += "<button type='button' id='edit-btn"+data.levelid+"' data-id='"+data.levelid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editLevelModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 								buttons += " <button type='button' id='delete-btn"+data.levelid+"' data-id='"+data.levelid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delLevelModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
 								buttons += "</center>";
 								return buttons;
 							}
 						}]

 					});

	});

	$('#addLevelBtn').click(function(){

		var description = $('#addLevelDesc').val();
		var hierarchy = $('#addLevelHierarchy').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			hierarchy: hierarchy
		};

		$.ajax({
			url: base_url+'settings/Level/create',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				$('#addLevelBtn').attr('disabled',false);
				$('#addLevelBtn').html('Add');
				$.LoadingOverlay('hide');

				if(data.success == 1) {
					// $('#addLevelDesc').val("");
					$('#addLevelModal').modal('toggle');
					notificationSuccess('Success',data.message);
					levelTable.ajax.reload(null,false);
				}else {
					$('#addLevelDesc').val("");
					notificationError('Error',data.message);
				}
			}
		});
	});

	$('#addLevelModal').on('hidden.bs.modal', function () {
		$('#addLevelDesc').val("");
	});

	$('#editLevelBtn').click(function(){

		var levelId = $('.levelid').val();
		var description = $('#description').val();
		var hierarchy = $('#hierarchy').val();
		var current_desc = $('#description').val();

		// alert(hierarchy);
		// return false;

		var data = {
			id:levelId,
			description:description,
			hierarchy: hierarchy,
			current_desc
		};

		$.ajax({
			url: base_url+'settings/Level/update',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {
				$.LoadingOverlay('hide');
				if(data.success == 1){
					$('#editLevelModal').modal('toggle');
					notificationSuccess('Success',data.message);
					levelTable.ajax.reload(null,false);
				}else{
					notificationError('Error',data.message);
				}
			}
		});

	});

	$('#delLevelBtn').click(function(){

		var levelId = $('.levelid').val();

		var data = {
			id:levelId
		};

		$.ajax({
			url: base_url+'settings/Level/destroy',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');
				$('#delLevelModal').modal('toggle');
				notificationSuccess('Success',result.message);
				levelTable.ajax.reload(null,false);
			}
		});

	});


});
