$(function(){
		var base_url = $('body').data('base_url');


				var serialize = $('#addRelationship_form').serialize();
				var tableGrid = $('#RelTable').DataTable({
					processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Relationship/reljson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'relationshipid'},
							{data:'description'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.relationshipid,function(){

									var relationshipid = $(this).data('id');
									var description = $(this).data('description');

									$('#relationshipid').val(relationshipid);
									$('#viewRel_desc').val(description);

								});


								$(document).on('click','#delete-btn'+data.relationshipid,function(){

									var deptId = $(this).data('id');
									var description = $(this).data('description');
									$('#relationshipid').val(deptId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.relationshipid+"' data-id='"+data.relationshipid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#viewRelationshipModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.relationshipid+"' data-id='"+data.relationshipid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteRelationshipModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}

						}]


				});




		$('#btnSearchRel').click(function(){
			var rel = $('.searchArea').val();
			$('#RelTable').DataTable().destroy();

			var tableGrid = $('#RelTable').DataTable({
				processing:false,
					serverSide:true,
					searching: false,
					ajax:{
						url: base_url+'settings/Relationship/reljson',
						beforeSend:function() {
							$.LoadingOverlay('show');
						},
						data: { searchValue: rel },
						complete:function() {
							$.LoadingOverlay('hide');
						}
					},
					columns:[
						{data:'relationshipid'},
						{data:'description'}
					],
					columnDefs:[{
						"targets":2,
						"data":null,
						"render":function(data, type, row, meta) {

							$(document).on('click','#edit-btn'+data.relationshipid,function(){

								var relationshipid = $(this).data('id');
								var description = $(this).data('description');

								$('#relationshipid').val(relationshipid);
								$('#viewRel_desc').val(description);

							});


							$(document).on('click','#delete-btn'+data.relationshipid,function(){

								var deptId = $(this).data('id');
								var description = $(this).data('description');
								$('#relationshipid').val(deptId);
								$('.info_desc').html(description)
							});

							var buttons = "";
							buttons += "<center>";
							buttons += "<button type='button' id='edit-btn"+data.relationshipid+"' data-id='"+data.relationshipid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#viewRelationshipModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
							buttons += " <button type='button' id='delete-btn"+data.relationshipid+"' data-id='"+data.relationshipid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteRelationshipModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
							buttons += "</center>";

							return buttons;
						}

					}]


			});
		});

		$('#addRelationshipBtn').click(function(){

		var description = $('#addRel_desc').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description
		};

			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Relationship/create',
				data: data,
				success:function(data){

						var result = JSON.parse(data);
						$('#addRelationshipBtn').attr('disabled',false);
						$('#addRelationshipBtn').html('Add');

						if(result.success == 1){
							$('#addRel_desc').val("");
							$('#addRelationshipModal').modal('toggle');
							notificationSuccess('Success',result.message);
							tableGrid.ajax.reload(null,false);
						}
						else{
							$('#addRel_desc').val("");
							notificationError('Error',result.message);
						}
				}



			});
		});

		$('#addRelationshipModal').on('hidden.bs.modal', function () {
			$('#addRel_desc').val("");
		});


		$('.updateRelBtn').click(function(){

		var relationshipid = $('#relationshipid').val();
		var description = $('#viewRel_desc').val();


		var data = {
			id:relationshipid,
			description:description
		};

		$.ajax({
			url: base_url+'settings/Relationship/update',
			type:'POST',
			data:data,
			success:function(data) {

				if(data.success == 0){
					notificationError('Error',data.message);
				}else{
					$('#viewRelationshipModal').modal('toggle');
					notificationSuccess('Success',data.message);
					tableGrid.ajax.reload(null,false);
				}
			}
		});

	});

			$('.deleteRelBtn').click(function(){

		var realtionshipid = $('#relationshipid').val();

		var data = {
			id:realtionshipid
		};

		$.ajax({
			url: base_url+'settings/Relationship/destroy',
			type:'POST',
			data:data,
			success:function(data) {
				console.log(data)
				var result = data;
				$('#deleteRelationshipModal').modal('toggle');
				notificationSuccess('Success',result.message);
				tableGrid.ajax.reload(null,false);
			}
		});

	});
			// $(document).on('hidden.bs.modal', '.modal', function () {
   // 			 $('.modal:visible').length && $(document.body).addClass('modal-open');
});
