$(function(){
		var base_url = $('body').data('base_url');

			// $('#PHtable').hide();
			// $('#addPHICbtn').hide();

				var philtable = $('#PHtable').DataTable({
					processing:"true",
						serverSide:true,
						ajax:{
							url: base_url+'settings/Philhealth/philhealthjson',
						},
						columns:[
							{data:'phID'},
							{data:'basic_mo_sal'},
							{data:'basic_mo_sal1'},
							{data: 'mo_contribution'},
							{data:'mo_contribution1'},
							{data:'employee_share'},
							{data:'employee_share1'},
							{data:'employer_share'},
							{data:'employer_share1'}
						],
						columnDefs:[{
							"targets":9,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.phID,function(){

									var id = $(this).data('id');
									var sal1 = $(this).data('sal1');
									var sal2 = $(this).data('sal2');
									var mocon1 = $(this).data('mocon1');
									var mocon2 = $(this).data('mocon2');
									var eeShare1 = $(this).data('eeshare1');
									var eeShare2 = $(this).data('eeshare2');
									var erShare1 = $(this).data('ershare1');
									var erShare2 = $(this).data('ershare2');

									$('.philhealthid').val(id);
									$('#editBasicSal1').val(sal1);
									$('#editBasicSal2').val(sal2);
									$('#editMonthlyCon1').val(mocon1);
									$('#editMonthlyCon2').val(mocon2);
									$('#editEmployeeShare1').val(eeShare1);
									$('#editEmployeeShare2').val(eeShare2);
									$('#editEmployerShare1').val(erShare1);
									$('#editEmployerShare2').val(erShare2);

								});


								$(document).on('click','#delete-btn'+data.phID,function(){

									var id = $(this).data('id');

									$('#phID').val(id);

								});

								var buttons = "";
								buttons += "<button type='button' id='edit-btn"+data.phID+"' data-id='"+data.phID+"' data-sal1='"+data.basic_mo_sal+"' data-sal2='"+data.basic_mo_sal1+"' data-mocon1='"+data.mo_contribution+"' data-mocon2='"+data.mo_contribution1+"' data-eeshare1='"+data.employee_share+"' data-eeshare2='"+data.employee_share1+"' data-ershare1='"+data.employer_share+"' data-ershare2='"+data.employer_share1+"' data-toggle='modal' data-target='#editPhilhealthModal' class='btn btn-primary' style='width:80px;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.phID+"' data-id='"+data.phID+"' data-toggle='modal' data-target='#deletePhilHealtModal' class='btn btn-danger' style='width:80px;'><i class='fa fa-trash'></i> Delete</button>";

								return buttons;
							}

						}]


				});



	$('#ctrlBtn').click(function(){
		$('.philHealthTbl').hide();
		$('.philHealthTbl_header').hide();
		$('.philHealthDataTbl').show();
		$('.dataTable_header').show();
			// $('#PHtable').show();
			// $('#addPHICbtn').show();
			// $('#viewPHICtable').hide();
			// $(this).hide();
	});



//add new SSS information to DB
		$('#addPhilHealth').click(function(){

			var error = 0;
			var errorMsg = "";

			var basicSal1 = $('#addBasicSal1').val();
			var basicSal2 = $('#addBasicSal2').val();
			var monthlyCon1 = $('#addMonthlyCon1').val();
			var monthlyCon2 = $('#addMonthlyCon2').val();
			var employeeShare1 = $('#addEmployeeShare1').val();
			var employeeShare2 = $('#addEmployeeShare2').val();
			var employerShare1 = $('#addEmployerShare1').val();
			var employerShare2 = $('#addEmployerShare2').val();

			var data = {
				basicSal1:basicSal1,
				basicSal2:basicSal2,
				monthlyCon1:monthlyCon1,
				monthlyCon2:monthlyCon2,
				employeeShare1:employeeShare1,
				employeeShare2:employeeShare2,
				employerShare1:employerShare1,
				employerShare2:employerShare2
			};

			if(parseFloat(basicSal1) >= parseFloat(basicSal2)){
				error = 1;
				errorMsg = "Invalid Basic Monthly Salary Range. Please check your input";
				$('.addBasicDesc').css('border', '1px solid #ef4131');
			}else{
				$('.addBasicDesc').css('border', '1px solid gainsboro');
			}

			if(parseFloat(monthlyCon1) >= parseFloat(monthlyCon2)){
				error = 1;
				errorMsg = "Invalid Monthly Contribution Range. Please check your input";
				$('.addMonthlyDesc').css('border', '1px solid #ef4131');
			}else{
				$('.addMonthlyDesc').css('border', '1px solid gainsboro');
			}

			if(parseFloat(employeeShare1) >= parseFloat(employeeShare2)){
				error = 1;
				errorMsg = "Invalid Employee Share Range. Please check your input";
				$('.addEmployeeShare1').css('border', '1px solid #ef4131');
			}else{
				$('.addEmployeeShare2').css('border', '1px solid gainsboro');
			}

			if(parseFloat(employerShare1) >= parseFloat(employerShare2)){
				error = 1;
				errorMsg = "Invalid Employer Share Range. Please check your input";
				$('.addEmployerShare').css('border', '1px solid #ef4131');
			}else{
				$('.addEmployerShare').css('border', '1px solid gainsboro');
			}


			if(error == 0){
				$.ajax({
					type: 'POST',
					url: base_url + 'settings/Philhealth/create',
					data: data,
					beforeSend: function(){
						$.LoadingOverlay('show');
					},
					success:function(data){
						$.LoadingOverlay('hide');

						var result = JSON.parse(data);

						if(result.success == 1){

							$('#addBasicSal1').val("");
							$('#addBasicSal2').val("");
							$('#addMonthlyCon1').val("");
							$('#addMonthlyCon2').val("");
							$('#addEmployeeShare1').val("");
							$('#addEmployeeShare2').val("");
							$('#addEmployerShare1').val("");
							$('#addEmployerShare2').val("");

							$('#addPhilhealthModal').modal('toggle');
							notificationSuccess('Success',result.message);
							philtable.ajax.reload(null,false);
						}
						else{

							if(result.salRangeExist == 1){
								notificationError('Error', result.message);
								// $('.addBasicDesc').css('border', '1px solid #ef4131');
								return;
							}
							// $('#addBasicSal1').val("");
							// $('#addBasicSal2').val("");
							// $('#addMonthlyCon1').val("");
							// $('#addMonthlyCon2').val("");
							// $('#addEmployeeShare1').val("");
							// $('#addEmployeeShare2').val("");
							// $('#addEmployerShare1').val("");
							// $('#addEmployerShare2').val("");

							notificationError('Error',result.message);
							philtable.ajax.reload(null,false);

						}
					}
				});

			}else{
				notificationError('Error', errorMsg);
			}
		});

		//Update position selected from table
		//show the current position on the input field

		$('#editPhilhealthBtn').click(function(){

			var error = 0;
			var erroMsg = "";

			var id = $('.philhealthid').val();
			var editBasicSal1 = $('#editBasicSal1').val();
			var editBasicSal2 = $('#editBasicSal2').val();
			var editMonthlyCon1 = $('#editMonthlyCon1').val();
			var editMonthlyCon2 = $('#editMonthlyCon2').val();
			var editEmployeeShare1 = $('#editEmployeeShare1').val();
			var editEmployeeShare2 = $('#editEmployeeShare2').val();
			var editEmployerShare1 = $('#editEmployerShare1').val();
			var editEmployerShare2 = $('#editEmployerShare2').val();

			var data = {
				id:id,
				editBasicSal1:editBasicSal1,
				editBasicSal2:editBasicSal2,
				editMonthlyCon1:editMonthlyCon1,
				editMonthlyCon2:editMonthlyCon2,
				editEmployeeShare1:editEmployeeShare1,
				editEmployeeShare2:editEmployeeShare2,
				editEmployerShare1:editEmployerShare1,
				editEmployerShare2:editEmployerShare2
			};

			if(parseFloat(editBasicSal1) >= parseFloat(editBasicSal2)){
				error = 1;
				errorMsg = "Invalid Basic Monthly Salary Range. Please check your input";
				$('.editBasicDesc').css('border', '1px solid #ef4131');
			}else{
				$('.editBasicDesc').css('border', '1px solid gainsboro');
			}

			if(parseFloat(editMonthlyCon1) >= parseFloat(editMonthlyCon2)){
				error = 1;
				errorMsg = "Invalid Monthly Contribution Range. Please check your input";
				$('.editMonthlyDesc').css('border', '1px solid #ef4131');
			}else{
				$('.editMonthlyDesc').css('border', '1px solid gainsboro');
			}

			if(parseFloat(editEmployeeShare1) >= parseFloat(editEmployeeShare2)){
				error = 1;
				errorMsg = "Invalid Employee Share Range. Please check your input";
				$('.editEmployeeShare').css('border', '1px solid #ef4131');
			}else{
				$('.editEmployeeShare').css('border', '1px solid gainsboro');
			}

			if(parseFloat(editEmployerShare1) >= parseFloat(editEmployerShare2)){
				error = 1;
				errorMsg = "Invalid Employer Share Range. Please check your input";
				$('.editEmployerShare').css('border', '1px solid #ef4131');
			}else{
				$('.editEmployerShare').css('border', '1px solid gainsboro');
			}

		if(error == 0){
			$.ajax({
				url: base_url+'settings/Philhealth/update',
				type:'POST',
				data:data,
				success:function(data) {

					var result = JSON.parse(data);

					if(result.success == 0){
						notificationError('Error',result.message);
					}else{
						$('#editPhilHealth-form').val();
						$('#editPhilhealthModal').modal('toggle');
						notificationSuccess('Success',JSON.parse(data).message);
						philtable.ajax.reload(null,false);
					}
				}
			});
		}else{
			notificationError('Error', errorMsg);
		}

	});

	$('.deletePhilHealthBtn').click(function(){
		var id = $('#phID').val();

		var data = {
			id:id
		};

		$.ajax({
			type:'POST',
			url: base_url+'settings/Philhealth/destroy',
			data:data,
			success:function(data) {
				console.log(data)
				var result = data;
				console.log(result)
				$('#deletePhilHealtModal').modal('toggle');
				notificationSuccess('Success',result.message);
				philtable.ajax.reload(null,false);
			}
		});

	});
});
