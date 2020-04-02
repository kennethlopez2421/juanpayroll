$(function(){

	var base_url = $("body").data('base_url');

	 var holidayTypeTable = $('#holidayTypeTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Holidaytype/holidaytypejson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'holidaytypeid'},
							{data:'description'},
							{data: 'payratio'},
							{data: 'payratio2'}
						],
						columnDefs:[{
							"targets":4,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.holidaytypeid,function(){

									var holidayTypeId = $(this).data('id');
									var description = $(this).data('description');
									var payRatio = $(this).data('pay_ratio');
									var payRatio2 = $(this).data('pay_ratio2');
									let htype = $(this).data('htype');
									// alert(payRatio);

									$('.holidaytypeid').val(holidayTypeId);
									$('#editHolidayTypeDesc').val(description);
									$('#currentHolidayTypeDesc').val(description);
									$('#edit_payRatio').val(payRatio);
									$('#edit_payRatio2').val(payRatio2);
									$('#edit_type option[value = "'+htype+'"]').prop('selected', true).trigger('change');

								});

								$(document).on('click','#delete-btn'+data.holidaytypeid,function(){

									var holidayTypeId = $(this).data('id');
									var description = $(this).data('description');
									$('.holidaytypeid').val(holidayTypeId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.holidaytypeid+"' data-id='"+data.holidaytypeid+"' data-description='"+data.description+"' data-pay_ratio = '"+data.payratio+"' data-pay_ratio2 = '"+data.payratio2+"' data-htype = '"+data.type+"' data-toggle='modal' data-target='#editHolidayTypeModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.holidaytypeid+"' data-id='"+data.holidaytypeid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delHolidayTypeModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}
						}]

					});

	$('#btnSearchHolidayType').click(function(){
		var hType = $('.searchArea').val();
		$('#holidayTypeTable').DataTable().destroy();

		var holidayTypeTable = $('#holidayTypeTable').DataTable({
 						processing:false,
 						serverSide:true,
 						searching: false,
 						ajax:{
 							url: base_url+'settings/Holidaytype/holidayTypeJSON',
 							beforeSend:function() {
 								$.LoadingOverlay('show');
 							},
							data: { searchValue: hType },
 							complete:function() {
 								$.LoadingOverlay('hide');
 							}
 						},
 						columns:[
 							{data:'holidaytypeid'},
 							{data:'description'},
 							{data: 'payratio'},
 							{data: 'payratio2'}
 						],
 						columnDefs:[{
 							"targets":4,
 							"data":null,
 							"render":function(data, type, row, meta) {

 								$(document).on('click','#edit-btn'+data.holidaytypeid,function(){

 									var holidayTypeId = $(this).data('id');
 									var description = $(this).data('description');
 									var payRatio = $(this).data('pay_ratio');
 									// alert(payRatio);

 									$('.holidaytypeid').val(holidayTypeId);
 									$('#editHolidayTypeDesc').val(description);
 									$('#currentHolidayTypeDesc').val(description);
 									$('#edit_payRatio').val(payRatio);

 								});

 								$(document).on('click','#delete-btn'+data.holidaytypeid,function(){

 									var holidayTypeId = $(this).data('id');
 									var description = $(this).data('description');
 									$('.holidaytypeid').val(holidayTypeId);
 									$('.info_desc').html(description)
 								});

 								var buttons = "";
								buttons += "<center>";
 								buttons += "<button type='button' id='edit-btn"+data.holidaytypeid+"' data-id='"+data.holidaytypeid+"' data-description='"+data.description+"' data-pay_ratio = '"+data.payratio+"' data-toggle='modal' data-target='#editHolidayTypeModal' class='btn btn-primary mr-1' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 								buttons += "<button type='button' id='delete-btn"+data.holidaytypeid+"' data-id='"+data.holidaytypeid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delHolidayTypeModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

 								return buttons;
 							}
 						}]

 					});

	});

	$('#addHolidayTypeBtn').click(function(){

		var description = $('#addHolidayTypeDesc').val();
		var payRatio = $('#payRatio').val();
		var payRatio2 = $('#payRatio2').val();
		let add_type = $('#add_type').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			add_type,
			payRatio,
			payRatio2
		};

		$.ajax({
			url: base_url+'settings/Holidaytype/create',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');
				$('#addHolidayTypeBtn').attr('disabled',false);
				$('#addHolidayTypeBtn').html('Add');

				if(result.success == 1) {
					$('#addHolidayTypeDesc').val("");
					$('#addHolidayTypeModal').modal('toggle');
					notificationSuccess('Success',result.message);
					holidayTypeTable.ajax.reload(null,false);
				}else {
					$('#addHolidayTypeDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addHolidayTypeModal').on('hidden.bs.modal', function () {
		$('#addHolidayTypeDesc').val("");
	});

	$('#editHolidayTypeBtn').click(function(){

		var holidayTypeId = $('.holidaytypeid').val();
		var description = $('#editHolidayTypeDesc').val();
		var currentDescription = $('#currentHolidayTypeDesc').val();
		var payRatio = $('#edit_payRatio').val();
		var payRatio2 = $('#edit_payRatio2').val();
		let edit_type = $('#edit_type').val();

		var data = {
			id:holidayTypeId,
			description:description,
			currentDescription,
			payRatio,
			payRatio2,
			edit_type
		};

		$.ajax({
			url: base_url+'settings/Holidaytype/update',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');

				if(result.success == 1){
					$('#editHolidayTypeModal').modal('toggle');
					notificationSuccess('Success',result.message);
					holidayTypeTable.ajax.reload(null,false);
				}else{
					notificationError('Error', result.message);
				}
			}
		});

	});

	$('#delHolidayTypeBtn').click(function(){

		var holidayTypeId = $('.holidaytypeid').val();

		var data = {
			id:holidayTypeId
		};

		$.ajax({
			url: base_url+'settings/Holidaytype/destroy',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay('hide');
				$('#delHolidayTypeModal').modal('toggle');
				notificationSuccess('Success',result.message);
				holidayTypeTable.ajax.reload(null,false);
			}
		});

	});


});
