$(function(){
		var base_url = $('body').data('base_url');


		var serialize = $('#addPosition-form').serialize();
		var positionTable = $('#positionTable').DataTable({
			processing:"true",
				serverSide:true,
				searching: false,
				ajax:{
					url: base_url+'settings/Position/posjson',
					dataSrc:'data',
					beforeSend: function(){
						$.LoadingOverlay('show');
					},
					complete: function(){
						$.LoadingOverlay('hide');
					}
				},
				columns:[
					{data:'position_id'},
					{data:'position_desc'},
					{data:'department'},
					{data:'subdepartment'}
				],
				columnDefs:[{
					"targets":4,
					"data":null,
					"render":function(data, type, row, meta) {

						$(document).on('click','#edit-btn'+data.position_id,function(){
							var thiss = $(this);
							$.ajax({
								url: base_url + 'settings/Position/get_pos_info',
								type: 'post',
								data: {
									posId: $(this).data('id')
								},
								success: function(data){
									$('#editPos_deptDesc').html('<option>-----------</option>');
									$('#edit_dept_access').html('<option>-----------</option>');
									$('#editPos_subDeptDesc').html('<option>-----------</option>');
									const dept_access = data.posData['department_access'].split(',');
									// console.log(dept_access);
									$.each(data.dept, function(i, val) {
										$('#editPos_deptDesc').append('<option value = "'+val['departmentid']+'">'+val['description']+'</option>');
										$('#edit_dept_access').append('<option value = "'+val['departmentid']+'">'+val['description']+'</option>');
									});

									$('#editPos_deptDesc option[value = "'+data.posData['deptId']+'"]').prop('selected', true);
									// console.log(data.subDept);
									$.each(data.subDept, function(i, val){
										$('#editPos_subDeptDesc').append('<option value = "'+val['subdeptid']+'">'+val['description']+'</option>');
									});

									$('#editPos_subDeptDesc option[value = "'+data.posData['subDeptId']+'"]').prop('selected', true);
									$('#edit_pos_access_lvl option[value = "'+thiss.data('pos_access_lvl')+'"]').prop('selected',true);

									dept_access.forEach((access) => {
										$('#edit_dept_access option[value="'+access+'"]').prop('selected', true);
									});

								}
							})

							var positionid = $(this).data('id');
							var description = $(this).data('description');

							$('.positionid').val(positionid);
							$('#editPos_desc').val(description);

						});


						$(document).on('click','#delete-btn'+data.position_id,function(){

							var deptId = $(this).data('id');
							var description = $(this).data('description');
							$('.positionid').val(deptId);
							$('#delPosid').html(description);
						});

						var buttons = "";
						buttons += "<center>";
						buttons += "<button type='button' id='edit-btn"+data.position_id+"' data-id='"+data.position_id+"' data-description='"+data.position_desc+"' data-pos_access_lvl  = '"+data.pos_access_lvl+"' data-toggle='modal' data-target='#editPosModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
						buttons += " <button type='button' id='delete-btn"+data.position_id+"' data-id='"+data.position_id+"' data-description='"+data.position_desc+"' data-toggle='modal' data-target='#deletePosModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
						buttons += "</center>";

						return buttons;
					}

				}]


		});

		$('#btnSearchPos').click(function(){
			var pos = $('.searchArea').val();
			$('#positionTable').DataTable().destroy();

			var positionTable = $('#positionTable').DataTable({
				processing:"true",
					serverSide:true,
					searching: false,
					ajax:{
						url: base_url+'settings/Position/posjson',
						dataSrc:'data',
						data: { searchValue: pos },
						beforeSend: function(){
							$.LoadingOverlay('show');
						},
						complete: function(){
							$.LoadingOverlay('hide');
						}
					},
					columns:[
						{data:'positionid'},
						{data:'description'}
					],
					columnDefs:[{
						"targets":2,
						"data":null,
						"render":function(data, type, row, meta) {

							$(document).on('click','#edit-btn'+data.position_id,function(){
								var thiss = $(this);
								$.ajax({
									url: base_url + 'settings/Position/get_pos_info',
									type: 'post',
									data: {
										posId: $(this).data('id')
									},
									success: function(data){
										$('#editPos_deptDesc').html('<option>-----------</option>');
										$('#editPos_subDeptDesc').html('<option>-----------</option>');
										$.each(data.dept, function(i, val) {
											$('#editPos_deptDesc').append('<option value = "'+val['departmentid']+'">'+val['description']+'</option>');
										});

										$('#editPos_deptDesc option[value = "'+data.posData['deptId']+'"]').prop('selected', true);
										console.log(data.subDept);
										$.each(data.subDept, function(i, val){
											$('#editPos_subDeptDesc').append('<option value = "'+val['subdeptid']+'">'+val['description']+'</option>');
										});

										$('#editPos_subDeptDesc option[value = "'+data.posData['subDeptId']+'"]').prop('selected', true);
									}
								})

								var positionid = $(this).data('id');
								var description = $(this).data('description');

								$('.positionid').val(positionid);
								$('#editPos_desc').val(description);

							});


							$(document).on('click','#delete-btn'+data.position_id,function(){

								var deptId = $(this).data('id');
								var description = $(this).data('description');
								$('.positionid').val(deptId);
								$('#delPosid').html(description);
							});

							var buttons = "";
							buttons += "<center>";
							buttons += "<button type='button' id='edit-btn"+data.position_id+"' data-id='"+data.position_id+"' data-description='"+data.position+"' data-toggle='modal' data-target='#editPosModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
							buttons += " <button type='button' id='delete-btn"+data.position_id+"' data-id='"+data.position_id+"' data-description='"+data.position+"' data-toggle='modal' data-target='#deletePosModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
							buttons += "</center>";

							return buttons;
						}

					}]


			});
		});

		$(document).on('change', '#editPos_deptDesc', function(){
			$('#editPos_subDeptDesc').html('<option value = "">-----------</option>');
			if($(this).val() != ""){
				$('#edit_dept_access').val(null).trigger('change');
				$('#edit_dept_access').removeAttr('disabled');
				$('#edit_dept_access option[value="'+$(this).val()+'"]').prop('selected', true).trigger('change');
				$.ajax({
					url: base_url + 'settings/Position/get_sub_dept',
					type: 'Post',
					beforeSend: $.LoadingOverlay('show'),
					data: {deptId: $(this).val() },
					success: function(data){
						$.LoadingOverlay('hide');
						$.each(data.subDept, function(i, val) {
							$('#editPos_subDeptDesc').append('<option value = "'+val['subdeptid']+'">'+val['description']+'</option>');
						});
					}
				});
			}else{
				$('#editPos_SubDept').val('').trigger('change');
				$('#edit-pos_access_lvl').val('').trigger('change');
				$('#edit-dept_access').val(null).trigger('change');
			}
		});

		$(document).on('click', '#btn_add_modal', function(){
			$('#addPos_Dept').html('<option value = "">-----------</option>').trigger('change');
			$.ajax({
				url: base_url + 'settings/Position/get_dept',
				type: 'Post',
				beforeSend: $.LoadingOverlay('show'),
				success: function(data){
					$.LoadingOverlay('hide');
					$.each(data.dept, function(i, val){
						$('#addPos_Dept').append('<option value = "'+val['departmentid']+'">'+val['description']+'</option>');
					});

					$.each(data.dept, function(i, val){
						$('#dept_access').append('<option value = "'+val['departmentid']+'">'+val['description']+'</option>');
					});


					$('#addPositionModal').modal();
				}
			});

			$('#addPositionModal').modal();
		});

		$(document).on('change', '#addPos_Dept', function(){
			$('#addPos_SubDept').html('<option value = "">-----------</option>');
			if($(this).val() != ""){
				$('#dept_access').val(null).trigger('change');
				$('#dept_access').removeAttr('disabled');
				$('#dept_access option[value="'+$(this).val()+'"]').prop('selected', true).trigger('change');
				$.ajax({
					url: base_url + 'settings/Position/get_sub_dept',
					type: 'Post',
					beforeSend: $.LoadingOverlay('show'),
					data: {deptId: $(this).val() },
					success: function(data){
						$.LoadingOverlay('hide');
						$.each(data.subDept, function(i, val) {
							$('#addPos_SubDept').append('<option value = "'+val['subdeptid']+'">'+val['description']+'</option>');
						});
					}
				});
			}else{
				$('#addPos_SubDept').val('').trigger('change');
				$('#pos_access_lvl').val('').trigger('change');
				$('#dept_access').val(null).trigger('change');
			}
		});
		//add new Position to DB
		$('#addPosBtn').click(function(){
			var dept = $('#addPos_Dept').val();
			var subDept = $('#addPos_SubDept').val();
			var description = $('#addPos_desc').val();
			var pos_access_lvl = $('#pos_access_lvl').val();
			let dept_access = $('#dept_access').val();

			var data = {
				description:description,
				dept: dept,
				subDept: subDept,
				pos_access_lvl,
				dept_access
			};

			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Position/create',
				data: data,
				success:function(data){
						if(data.success == 1){
							$('#addPos_desc').val("");
							$('#addPositionModal').modal('toggle');
							notificationSuccess('Success',data.message);
							positionTable.ajax.reload(null,false);
						}
						else{
							notificationError('Error',data.message);
							// $('#addPos_desc').val("");
							// $('#addPositionModal').modal('toggle');

						}
				}



			});
		});

		$('#editPosBtn').click(function(){

			var positionid = $('.positionid').val();
			var description = $('#editPos_desc').val();
			var editPos_deptDesc = $('#editPos_deptDesc').val();
			var editPos_subDeptDesc = $('#editPos_subDeptDesc').val();
			var edit_pos_access_lvl = $('#edit_pos_access_lvl').val();
			let edit_dept_access = $('#edit_dept_access').val();
			// alert(editPos_deptDesc);
			// alert(editPos_subDeptDesc);
			var data = {
				id:positionid,
				description:description,
				editPos_deptDesc: editPos_deptDesc,
				editPos_subDeptDesc: editPos_subDeptDesc,
				edit_pos_access_lvl,
				edit_dept_access
			};

			$.ajax({
				url: base_url+'settings/Position/update',
				type:'POST',
				data:data,
				success:function(data) {

					if(data.success == 0){
						notificationError('Error',data.message);
					}else{
						$('#editPosModal').modal('toggle');
						notificationSuccess('Success',data.message);
						positionTable.ajax.reload(null,false);
					}
				}
			});

		});

		$('.deletePosBtn').click(function(){

		var positionid = $('.positionid').val();

		var data = {
			id:positionid
		};

		$.ajax({
			url: base_url+'settings/Position/destroy',
			type:'POST',
			data:data,
			success:function(data) {
				console.log(data)
				var result = data;
				console.log(result)
				$('#deletePosModal').modal('toggle');
				notificationSuccess('Success',result.message);
				positionTable.ajax.reload(null,false);
			}
		});

	});
});
