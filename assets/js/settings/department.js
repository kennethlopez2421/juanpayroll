$(function(){
	var base_url = $('body').data('base_url');

	var serialize = $('#department-add-form').serialize();
	var tableGrid = $('#departmentTable').DataTable({
			processing:false,
			serverSide:true,
			searching: false,
			ajax:{
				url: base_url+'settings/Department/deptjson',
				beforeSend:function() {
					$.LoadingOverlay('show');
				},
				complete:function() {
					$.LoadingOverlay('hide');
				}
			},
			columns:[
				{data:'departmentid'},
				{data:'description'},
				{data:'dept_type'}
			],
			columnDefs:[{
				"targets":3,
				"data":null,
				"render":function(data, type, row, meta) {

					$(document).on('click','#edit-btn'+data.departmentid,function(){

						var departmentid = $(this).data('id');
						var description = $(this).data('description');
						let dept_type = $(this).data('dept_type');

						$('.departmentid').val(departmentid);
						$('#dept_info').val(description);
						$('#edit_dept_type option[value="'+dept_type+'"]').prop('selected', true).trigger('change');

					});


					$(document).on('click','#delete-btn'+data.departmentid,function(){

						var deptId = $(this).data('id');
						var description = $(this).data('description');
						$('.departmentid').val(deptId);
						$('.info_desc').html(description)
					});

					var buttons = "";
					buttons += "<center>";
					buttons += "<button type='button' id='edit-btn"+data.departmentid+"' data-id='"+data.departmentid+"' data-description='"+data.description+"' data-dept_type = '"+data.department_type+"' data-toggle='modal' data-target='#editDepartmentModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
					buttons += " <button type='button' id='delete-btn"+data.departmentid+"' data-id='"+data.departmentid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteDepartmentModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
					buttons += "</center>";

					return buttons;
				}

			}]


	});

	$('#btnSearchDept').click(function(){
		var dept = $('.searchArea').val();

		$('#departmentTable').DataTable().destroy();
		var tableGrid = $('#departmentTable').DataTable({
				processing:false,
				serverSide:true,
				searching: false,
				ajax:{
					url: base_url+'settings/Department/deptjson',
					beforeSend:function() {
						$.LoadingOverlay('show');
					},
					data: {searchValue: dept},
					complete:function() {
						$.LoadingOverlay('hide');
					}
				},
				columns:[
					{data:'departmentid'},
					{data:'description'},
					{data:'dept_type'}
				],
				columnDefs:[{
					"targets":3,
					"data":null,
					"render":function(data, type, row, meta) {

						$(document).on('click','#edit-btn'+data.departmentid,function(){

							var departmentid = $(this).data('id');
							var description = $(this).data('description');
							let dept_type = $(this).data('dept_type');

							$('.departmentid').val(departmentid);
							$('#dept_info').val(description);

						});


						$(document).on('click','#delete-btn'+data.departmentid,function(){

							var deptId = $(this).data('id');
							var description = $(this).data('description');
							$('.departmentid').val(deptId);
							$('.info_desc').html(description)
						});

						var buttons = "";
						buttons += "<center>";
						buttons += "<button type='button' id='edit-btn"+data.departmentid+"' data-id='"+data.departmentid+"' data-description='"+data.description+"' data-dept_type = '"+data.department_type+"' data-toggle='modal' data-target='#editDepartmentModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
						buttons += " <button type='button' id='delete-btn"+data.departmentid+"' data-id='"+data.departmentid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteDepartmentModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
						buttons += "</center>";

						return buttons;
					}

				}]


		});

	});

	$('#addDepartmentBtn').click(function(){

		var description = $('#addDepartment_desc').val();
		let dept_type = $('#dept_type').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			dept_type
		};

		$.ajax({
			type: 'POST',
			url: base_url + 'settings/Department/create',
			data: data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data){


					var result = JSON.parse(data);
					$.LoadingOverlay('hide');
					$('#addDepartmentBtn').attr('disabled',false);
					$('#addDepartmentBtn').html('Add');

					if(result.success == 1){
						$('#addDepartment_desc').val("");
						$('#addDepartmentModal').modal('toggle');
						notificationSuccess('Success',result.message);
						tableGrid.ajax.reload(null,false);
					}
					else{
						$('#addDepartment_desc').val("");
						notificationError('Error',result.message);
					}
			}



		});
	});

	$('#addDepartmentModal').on('hidden.bs.modal', function () {
		$('#addDepartment_desc').val("");
	});

	$('#updateDepartmentBtn').click(function(){

		var departmentid = $('.departmentid').val();
		var description = $('#dept_info').val();
		let edit_dept_type = $('#edit_dept_type').val();

		var data = {
			id:departmentid,
			description:description,
			edit_dept_type
		};

		$.ajax({
			url: base_url+'settings/Department/update',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				$('#editDepartmentModal').modal('toggle');
				$.LoadingOverlay('hide');
				notificationSuccess('Success',data.message);
				tableGrid.ajax.reload(null,false);
			}
		});

	});

	$('.deleteDepartmentBtn').click(function(){

		var levelId = $('.departmentid').val();

		var data = {
			id:levelId
		};

		$.ajax({
			url: base_url+'settings/Department/destroy',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');

				$('#deleteDepartmentModal').modal('toggle');
				notificationSuccess('Success',result.message);
				tableGrid.ajax.reload(null,false);
			}
		});

	});


});
