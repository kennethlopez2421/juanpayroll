$(function(){
		var base_url = $('body').data('base_url');


				var serialize = $('#addEmpStatus-form').serialize();
				var tableGrid = $('#EmpStatTable').DataTable({
					processing:"true",
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Employmentstatus/empjson',
							dataSrc:'data'
						},
						columns:[
							{data:'empstatusid'},
							{data:'description'},
							{data:'regular_holiday'},
							{data:'special_non_working_holiday'},
							{data: 'leave_pay'}
						],
						columnDefs:[{
							"targets":5,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.empstatusid,function(){

									var empstatusid = $(this).data('id');
									var description = $(this).data('description');
									var reg_holiday = $(this).data('reg_holiday');
									var spec_holiday = $(this).data('spec_holiday');
									let leave_pay = $(this).data('leave_pay');

									$('.empstatusid').val(empstatusid);
									$('#updateEmpStatus_desc').val(description);
									$('#current_desc').val(description);
									$('#update_reg_holiday option[value = "'+reg_holiday+'"]').prop('selected', true);
									$('#update_spec_holiday option[value = "'+spec_holiday+'"]').prop('selected', true);
									$('#update_leave option[value = "'+leave_pay+'"]').prop('selected', true);

								});


								$(document).on('click','#delete-btn'+data.empstatusid,function(){

									var statusId = $(this).data('id');
									var description = $(this).data('description');
									$('.empstatusid').val(statusId);
									$('#updateEmpStatus_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.empstatusid+"' data-id='"+data.empstatusid+"' data-description='"+data.description+"' data-reg_holiday = '"+data.regular_holiday+"' data-spec_holiday = '"+data.special_non_working_holiday+"' data-leave_pay = '"+data.leave_pay+"' data-toggle='modal' data-target='#updateEmpStatusModal' class='btn btn-primary mr-1' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += "<button type='button' id='delete-btn"+data.empstatusid+"' data-id='"+data.empstatusid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteEmpStatusModal' class='btn btn-danger mr-1' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}

						}]


				});




		$('#btnSearchEmpStatus').click(function(){
			var empStatus = $('.searchArea').val();
			$('#EmpStatTable').DataTable().destroy();

			var tableGrid = $('#EmpStatTable').DataTable({
				processing:"true",
					serverSide:true,
					searching: false,
					ajax:{
						url: base_url+'settings/Employmentstatus/empjson',
						dataSrc:'data'
					},
					columns:[
						{data:'empstatusid'},
						{data:'description'},
						{data:'regular_holiday'},
						{data:'special_non_working_holiday'},
						{data: 'leave_pay'}
					],
					columnDefs:[{
						"targets":5,
						"data":null,
						"render":function(data, type, row, meta) {

							$(document).on('click','#edit-btn'+data.empstatusid,function(){

								var empstatusid = $(this).data('id');
								var description = $(this).data('description');
								var reg_holiday = $(this).data('reg_holiday');
								var spec_holiday = $(this).data('spec_holiday');
								let leave_pay = $(this).data('leave_pay');

								$('.empstatusid').val(empstatusid);
								$('#updateEmpStatus_desc').val(description);
								$('#current_desc').val(description);
								$('#update_reg_holiday option[value = "'+reg_holiday+'"]').prop('selected', true);
								$('#update_spec_holiday option[value = "'+spec_holiday+'"]').prop('selected', true);
								$('#update_leave option[value = "'+leave_pay+'"]').prop('selected', true);

							});


							$(document).on('click','#delete-btn'+data.empstatusid,function(){

								var statusId = $(this).data('id');
								var description = $(this).data('description');
								$('.empstatusid').val(statusId);
								$('#updateEmpStatus_desc').html(description)
							});

							var buttons = "";
							buttons += "<center>";
							buttons += "<button type='button' id='edit-btn"+data.empstatusid+"' data-id='"+data.empstatusid+"' data-description='"+data.description+"' data-reg_holiday = '"+data.regular_holiday+"' data-spec_holiday = '"+data.special_non_working_holiday+"' data-leave_pay = '"+data.leave_pay+"' data-toggle='modal' data-target='#updateEmpStatusModal' class='btn btn-primary mr-1' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
							buttons += "<button type='button' id='delete-btn"+data.empstatusid+"' data-id='"+data.empstatusid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteEmpStatusModal' class='btn btn-danger mr-1' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
							buttons += "</center>";

							return buttons;
						}

					}]


			});
		});

		$('#addEmpStatusBtn').click(function(){

			var description = $('#addempStatus_desc').val();
			var reg_holiday = $('#reg_holiday').val();
			var spec_holiday = $('#spec_holiday').val();
			let add_leave = $('#add_leave').val();

			var error = 0;
			var errorMsg = "";

			var data = {
				description:description,
				reg_holiday,
				spec_holiday,
				add_leave
			};


			$('.req').each(function(){
			  if($(this).val() == ""){
			    $(this).css("border", "1px solid #ef4131");
			  }else{
			    $(this).css("border", "1px solid gainsboro");
			  }
			});

			$('.req').each(function(){
			  if($(this).val() == ""){
			    $(this).focus();
			    var error = 1;
			    var errorMsg = "Please fill up all required fields.";
			    return false;
			  }
			});

			if(error == 0){
			  $.ajax({
			    url: base_url+ 'settings/Employmentstatus/create',
			    type: 'post',
			    data:data,
			    beforeSend: function(){
			      $.LoadingOverlay('show');
			    },
			    success: function(data){
			      $.LoadingOverlay('hide');
						var result = JSON.parse(data);

						if(result.success == 1){
							$('#addempStatus_desc').val("");
							$('#addEmpStatusModal').modal('toggle');
							notificationSuccess('Success',result.message);
							tableGrid.ajax.reload(null,false);
						}
						else{
							$('#addempStatus_desc').val("");
							notificationError('Error',result.message);
							$('#addEmpStatusModal').modal('toggle');

						}
			    }
			  });
			}else{
			  notificationError('Error', errorMsg);
			}

		});

		$('#updateEmpStatusBtn').click(function(){

		var empstatusid = $('.empstatusid').val();
		var description = $('#updateEmpStatus_desc').val();
		var current_desc = $('#current_desc').val();
		var reg_holiday = $('#update_reg_holiday').val();
		var spec_holiday = $('#update_spec_holiday').val();
		let update_leave = $('#update_leave').val();

		var data = {
			id:empstatusid,
			description:description,
			current_desc,
			reg_holiday,
			spec_holiday,
			update_leave
		};

		$.ajax({
			url: base_url+'settings/Employmentstatus/update',
			type:'POST',
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data) {
				$.LoadingOverlay('hide');
				if(data.success == 0){
					notificationError('Error',data.message)
				}else{
					$('#updateEmpStatusModal').modal('toggle');
					notificationSuccess('Success',data.message);
					tableGrid.ajax.reload(null,false);
				}
			}
		});

	});

			$('#deleteEmpStatusBtn').click(function(){

		var levelId = $('.empstatusid').val();

		var data = {
			id:levelId
		};

		$.ajax({
			url: base_url+'settings/Employmentstatus/destroy',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#deleteEmpStatusModal').modal('toggle');
				notificationSuccess('Success',result.message);
				tableGrid.ajax.reload(null,false);
			}
		});

	});


});
