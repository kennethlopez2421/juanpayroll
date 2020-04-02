$(function(){

	var base_url = $("body").data('base_url');

	 var subDeptTable = $('#subDeptTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Subdepartment/subdeptjson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'subdeptid'},
							{data:'description'},
							{data:'department'}
						],
						columnDefs:[{
							"targets":3,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.subdeptid,function(){

									var subDeptId = $(this).data('id');
									var description = $(this).data('description');
									var departmentid = $(this).data('departmentid');
									var department = $(this).data('department');

									//show blank if the country is deleted
									if(department == null) {
										$('#editDepartment option:eq(0)').prop('selected','selected').change();
									}else {
										$('#editDepartment option:contains("'+department+'")').prop('selected','selected').change();
									}

									$('.subdeptid').val(subDeptId);
									$('#editSubDeptDesc').val(description);

								});

								$(document).on('click','#delete-btn'+data.subdeptid,function(){

									var subDeptId = $(this).data('id');
									var description = $(this).data('description');
									$('.subdeptid').val(subDeptId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.subdeptid+"' data-id='"+data.subdeptid+"' data-description='"+data.description+"' data-departmentid='"+data.departmentid+"' data-department='"+data.department+"' data-toggle='modal' data-target='#editSubDeptModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.subdeptid+"' data-id='"+data.subdeptid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delSubDeptModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>"

								return buttons;
							}
						}]

					});



	$('#btnSearchSubDept').click(function(){
		var subDept = $('.searchArea').val();
		$('#subDeptTable').DataTable().destroy();

		var subDeptTable = $('#subDeptTable').DataTable({
 						processing:false,
 						serverSide:true,
 						searching: false,
 						ajax:{
 							url: base_url+'settings/Subdepartment/subdeptjson',
 							beforeSend:function() {
 								$.LoadingOverlay('show');
 							},
							data: { searchValue: subDept },
 							complete:function() {
 								$.LoadingOverlay('hide');
 							}
 						},
 						columns:[
 							{data:'subdeptid'},
 							{data:'description'}
 						],
 						columnDefs:[{
 							"targets":2,
 							"data":null,
 							"render":function(data, type, row, meta) {

 								$(document).on('click','#edit-btn'+data.subdeptid,function(){

 									var subDeptId = $(this).data('id');
 									var description = $(this).data('description');
 									var departmentid = $(this).data('departmentid');
 									var department = $(this).data('department');

 									//show blank if the country is deleted
 									if(department == null) {
 										$('#editDepartment option:eq(0)').prop('selected','selected').change();
 									}else {
 										$('#editDepartment option:contains("'+department+'")').prop('selected','selected').change();
 									}

 									$('.subdeptid').val(subDeptId);
 									$('#editSubDeptDesc').val(description);

 								});

 								$(document).on('click','#delete-btn'+data.subdeptid,function(){

 									var subDeptId = $(this).data('id');
 									var description = $(this).data('description');
 									$('.subdeptid').val(subDeptId);
 									$('.info_desc').html(description)
 								});

 								var buttons = "";
 								buttons += "<center>";
 								buttons += "<button type='button' id='edit-btn"+data.subdeptid+"' data-id='"+data.subdeptid+"' data-description='"+data.description+"' data-departmentid='"+data.departmentid+"' data-department='"+data.department+"' data-toggle='modal' data-target='#editSubDeptModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 								buttons += " <button type='button' id='delete-btn"+data.subdeptid+"' data-id='"+data.subdeptid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delSubDeptModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
 								buttons += "</center>"

 								return buttons;
 							}
 						}]

 					});
	});

	$('#addSubDeptBtn').click(function(){

		var description = $('#addSubDeptDesc').val();
		var subdept = $('#addDeptId').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			subdept:subdept
		};

		$.ajax({
			url: base_url+'settings/Subdepartment/create',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#addSubDeptBtn').attr('disabled',false);
				$('#addSubDeptBtn').html('Add');

				if(result.success == 1) {
					$('#addSubDeptDesc').val("");
					$('#addSubDeptModal').modal('toggle');
					notificationSuccess('Success',result.message);
					subDeptTable.ajax.reload(null,false);
				}else {
					$('#addSubDeptDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addSubDeptModal').on('hidden.bs.modal', function () {
		$('#addSubDeptDesc').val("");
	});

	$('#editSubDeptBtn').click(function(){

		var subDeptId = $('.subdeptid').val();
		var description = $('#editSubDeptDesc').val();
		var editDepartment = $('#editDepartment').val();

		var data = {
			id:subDeptId,
			description:description,
			department:editDepartment
		};

		$.ajax({
			url: base_url+'settings/Subdepartment/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				if(result.success == 0){
					notificationError('Error',result.message);
				}else{
					$('#editSubDeptModal').modal('toggle');
					notificationSuccess('Success',result.message);
					subDeptTable.ajax.reload(null,false);
				}
			}
		});

	});

	$('#delSubDeptBtn').click(function(){

		var subDeptId = $('.subdeptid').val();

		var data = {
			id:subDeptId
		};

		$.ajax({
			url: base_url+'settings/Subdepartment/destroy',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#delSubDeptModal').modal('toggle');
				notificationSuccess('Success',result.message);
				subDeptTable.ajax.reload(null,false);
			}
		});

	});


});
