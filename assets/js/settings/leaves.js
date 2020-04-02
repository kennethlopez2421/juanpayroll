$(function(){
		var base_url = $('body').data('base_url');


				//var serialize = $('#addCA-form').serialize();
				var leavesTable = $('#leavesTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Leaves/leavesjson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'leaveid'},
							{data:'description'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.leaveid,function(){

									var leaveid = $(this).data('id');
									var description = $(this).data('description');
									var days_before_filling = $(this).data('dbf');
									let late_filling = $(this).data('late_filling');
									let consecutive_filling = $(this).data('consecutive_filling');
										//show the current leaves on the input field from data table
									$('.leaveid').val(leaveid);
									$('#updateLeave_info').val(description);
									$('#edit_days_before_filling').val(days_before_filling)
									$('#edit_late_filling option[value="'+late_filling+'"]').prop('selected', true);
									$('#edit_consecutive_filling option[value="'+consecutive_filling+'"]').prop('selected', true);
								});


								$(document).on('click','#delete-btn'+data.leaveid,function(){

									var leaveid = $(this).data('id');
									var description = $(this).data('description');
									$('.leaveid').val(leaveid);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.leaveid+"' data-id='"+data.leaveid+"' data-description='"+data.description+"' data-dbf = '"+data.days_before_filling+"' data-late_filling = '"+data.late_filling+"' data-consecutive_filling = '"+data.consecutive_filling+"' data-toggle='modal' data-target='#editLeavesModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.leaveid+"' data-id='"+data.leaveid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteLeavesModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}

						}]


				});




		$('#btnSearchLeave').click(function(){
			var leave = $('.searchArea').val();
			$('#leavesTable').DataTable().destroy();

			var leavesTable = $('#leavesTable').DataTable({
					processing:false,
					serverSide:true,
					searching: false,
					ajax:{
						url: base_url+'settings/Leaves/leavesjson',
						beforeSend:function() {
							$.LoadingOverlay('show');
						},
						data: { searchValue: leave },
						complete:function() {
							$.LoadingOverlay('hide');
						}
					},
					columns:[
						{data:'leaveid'},
						{data:'description'}
					],
					columnDefs:[{
						"targets":2,
						"data":null,
						"render":function(data, type, row, meta) {

							$(document).on('click','#edit-btn'+data.leaveid,function(){

								var leaveid = $(this).data('id');
								var description = $(this).data('description');
									//show the current leaves on the input field from data table
								$('.leaveid').val(leaveid);
								$('#updateLeave_info').val(description);
								$('#edit_days_before_filling').val(days_before_filling)
								$('#edit_late_filling option[value="'+late_filling+'"]').prop('selected', true);
								$('#edit_consecutive_filling option[value="'+consecutive_filling+'"]').prop('selected', true);
							});


							$(document).on('click','#delete-btn'+data.leaveid,function(){

								var leaveid = $(this).data('id');
								var description = $(this).data('description');
								$('.leaveid').val(leaveid);
								$('.info_desc').html(description)
							});

							var buttons = "";
							buttons += "<center>";
							buttons += "<button type='button' id='edit-btn"+data.leaveid+"' data-id='"+data.leaveid+"' data-description='"+data.description+"' data-dbf = '"+data.days_before_filling+"' data-late_filling = '"+data.late_filling+"' data-consecutive_filling = '"+data.consecutive_filling+"' data-toggle='modal' data-target='#editLeavesModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
							buttons += " <button type='button' id='delete-btn"+data.leaveid+"' data-id='"+data.leaveid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteLeavesModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
							buttons += "</center>";

							return buttons;
						}

					}]


			});
		});

//add new Position to DB
		$('#addLeaveBtn').click(function(){
		var description = $('#addLeave_desc').val();
		var days_before_filling = $('#days_before_filling').val();
		let late_filling = $('#late_filling').val();
		let consecutive_filling = $('#consecutive_filling').val();
		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			days_before_filling,
			late_filling,
			consecutive_filling
			//user_id:id
		};

			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Leaves/create',
				data: data,
				success:function(data){

					$('#addLeaveBtn').attr('disabled',false);
					$('#addLeaveBtn').html('Add');

					var result = JSON.parse(data);

					if(result.success == 1){
						$('#addLeave_desc').val("");
						$('#addLeavesModal').modal('toggle');
						notificationSuccess('Success',result.message);
						leavesTable.ajax.reload(null,false);
					}
					else{
						$('#addLeave_desc').val("");
						notificationError('Error',result.message);
					}
				}
			});
		});


	$('#addLeavesModal').on('hidden.bs.modal', function () {
		$('#addLeave_desc').val("");
	});


		//Update leaves selected from table


		$('#updateLeavesBtn').click(function(){

		var leaveid = $('.leaveid').val();
		var description = $('#updateLeave_info').val();
		var edit_days_before_filling = $('#edit_days_before_filling').val();
		let edit_late_filling = $('#edit_late_filling').val();
		let edit_consecutive_filling = $('#edit_consecutive_filling').val();
		var data = {
			id:leaveid,
			description:description,
			edit_days_before_filling,
			edit_late_filling,
			edit_consecutive_filling
		};

		$.ajax({
			url: base_url+'settings/Leaves/update',
			type:'POST',
			data:data,
			success:function(data) {

				if(data.success == 0){
					notificationError('Error',data.message);
				}else{
					$('#editLeavesModal').modal('toggle');
					notificationSuccess('Success',data.message);
					leavesTable.ajax.reload(null,false);
				}
			}


		});

	});

		$('.deleteLeaveBtn').click(function(){

		var leaveid = $('.leaveid').val();
		var data = {
			id:leaveid
		};

		$.ajax({
			url: base_url+'settings/Leaves/destroy',
			type:'POST',
			data:data,
			success:function(result) {

				$('#deleteLeavesModal').modal('toggle');
				notificationSuccess('Success',JSON.parse(result).message);
				leavesTable.ajax.reload(null,false);
			}
		});

	});
});
