$(function(){

	var base_url = $("body").data('base_url');

	 var employeeChargesTable = $('#EmployeeChargesTable').DataTable({
						processing:false,
						serverSide:true,
						ordering: true,
						order: [[0, "asc"]],
						ajax:{
							url: base_url+'settings/Employeecharges/chargesjson',
							// beforeSend:function(){
							// 	$.LoadingOverlay('show');
							// },
							// complete:function(){
							// 	$.LoadingOverlay('hide');
							// }
						},
						columns:[
							{data:'employee_charges_id'},
							{data:'description'},
							{data:'amount'},
						],
						columnDefs:[{
 							"targets":3,
 							"data":null,
 		 					"render":function(data, type, row, meta) {
 		 						var html = "";
 		 						if(data.charge_status == "released") {
									html += "<span class = 'text-success'><b>"+data.charge_status+"</b></span>";
 		 						}else if(data.charge_status == "approved") {
 		 							html += "<span class = 'text-primary'><b>"+data.charge_status+"</b></span>";
 		 						}
 		 						else{
 		 							html += "<span class = 'text-danger'><b>"+data.charge_status+"</b></span>";
 		 						}
 		 						return html;
 		 					}
						},
						{
							"targets":4,
							"data":null,
							"render":function(data, type, row, meta) {
								var buttonvalidation = data.charge_status;
								if(buttonvalidation == 'approved' || buttonvalidation == 'released')
								{
									$('#edit-btn'+data.employee_charges_id).hide();
									$('#delete-btn'+data.employee_charges_id).hide();
									var n_a = "";
									n_a += "<p>N/A</p>";
									return n_a;
								}

								$(document).on('click','#edit-btn'+data.employee_charges_id,function(){
									var EmployeeChargesid = $(this).data('id');
									var description = $(this).data('description');
									var amount = $(this).data('amount');
									var charge_status = $(this).data('status');
									$('.EmployeeChargesid').val(EmployeeChargesid);
									$("#description").val(description);
									$('#editEmployeeChargesAmount').val(amount);

									// if(charge_status == 'approved')
									// 	{
									// 		$("#editCADesc").prop('disabled', true);
									// 		$("#editCAAmount").prop('disabled', true);
									// 		$("#editEmployeeChargesBtn").hide();
									// 		$("#charges_status_prompt").html(" - Cannot be edited. Data is in Approved status.");

									// 	}
									// else if(charge_status == 'released')
									// 	{
									// 		$("#description").prop('disabled', true);
									// 		$("#editEmployeeChargesAmount").prop('disabled', true);
									// 		$("#charges_status_prompt").html("- Cannot be edited. Data is in Released status.");
									// 		$("#editEmployeeChargesBtn").hide();
									// 	}
									// else{
									// 		$("#description").prop('disabled', false);
									// 		$("#editEmployeeChargesAmount").prop('disabled', false);
									// 		$("#charges_status_prompt").html("");
									// 		$("#editEmployeeChargesBtn").show();
									// }
								});

								$(document).on('click','#delete-btn'+data.employee_charges_id,function(){

									var EmployeeChargesId = $(this).data('id');
									var description = $(this).data('description');
									var charge_status = $(this).data('status');
									$('.EmployeeChargesid').val(EmployeeChargesId);
									$('.info_desc').html(description);

									 if(charge_status == 'approved')
										{

											$("#qmark").html("");
										}
									else if(charge_status == 'released')
										{
											$("#qmark").html("");
										}
									else if(charge_status == 'waiting for approval'){
										$("#charges_status_prompt_delete").html("Are you sure you want to delete ");
										$("#qmark").html("?");
										$("#delEmployeeChargesBtn").show();
									}
									else{

									}
								});

								var buttons = "";
								buttons += "<button type='button' id='edit-btn"+data.employee_charges_id+"' data-id='"+data.employee_charges_id+"' data-amount='"+data.amount+"' data-status='"+data.charge_status+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editEmployeeChargesModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.employee_charges_id+"' data-id='"+data.employee_charges_id+"' data-description='"+data.description+"' data-status='"+data.charge_status+"' data-toggle='modal' data-target='#delEmployeeChargesModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";

								return buttons;
							}
						}]

					});


	$('#searchButton').click(function(){
 		var searchText = $('#chargesTableTB').val();
 		employeeChargesTable.search(searchText).draw();
 	});

	$('#addEmployeeChargesBtn').click(function(){

		var description = $('#addEmployeeChargesDesc').val();
		var amount = $('#addEmployeeChargesAmount').val();
		console.log(amount);

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			amount:amount
		};

		$.ajax({
			url: base_url+'settings/Employeecharges/create',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);

				$('#addEmployeeChargesBtn').attr('disabled',false);
				$('#addEmployeeChargesBtn').html('Add');

				if(result.success == 0) {
					$('#addEmployeeChargesDesc').val("");
					$('#addEmployeeChargesAmount').val("");
					notificationError('Error',result.message);
				}else {
					$.LoadingOverlay("show");
					$('#addEmployeeChargesDesc').val("");
					$('#addEmployeeChargesModal').modal('toggle');
					notificationSuccess('Success',result.message);
					employeeChargesTable.ajax.reload(null,false);
					$.LoadingOverlay("hide");
				}
			}
		});
	});

	$('#addEmployeeChargesModal').on('hidden.bs.modal', function () {
		$('#addEmployeeChargesDesc').val("");
		$('#addEmployeeChargesAmount').val("");
	});

	$('#editEmployeeChargesBtn').click(function(){

		var EmployeeChargesId = $('.EmployeeChargesid').val();
		var description = $('#description').val();
		var amount = $('#editEmployeeChargesAmount').val();



		var data = {
			id:EmployeeChargesId,
			description:description,
			amount:amount
		};

		$.ajax({
			url: base_url+'settings/Employeecharges/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay("hide");
				if(result.success == 0){
					$('#editEmployeeChargesModal').modal('toggle');
					notificationError('Success',result.message);
					employeeChargesTable.ajax.reload(null,false);
				}else{
					$.LoadingOverlay("show");
					$('#editEmployeeChargesModal').modal('toggle');
					notificationSuccess('Success',result.message);
					employeeChargesTable.ajax.reload(null,false);
					$.LoadingOverlay("hide");
				}
			}
		});
	});

	$('#delEmployeeChargesBtn').click(function(){

		var EmployeeChargesId = $('.EmployeeChargesid').val();

		var data = {
			id:EmployeeChargesId
		};

		$.ajax({
			url: base_url+'settings/Employeecharges/destroy',
			type:'POST',
			data:data,
			success:function(data) {
				$.LoadingOverlay("show");
				var result = JSON.parse(data);
				$.LoadingOverlay("hide");
				$('#delEmployeeChargesModal').modal('toggle');
				notificationSuccess('Success',result.message);
				employeeChargesTable.ajax.reload(null,false);
				$.LoadingOverlay("hide");
			}
		});

	});


});
