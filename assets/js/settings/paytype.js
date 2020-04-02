$(function(){
		var base_url = $('body').data('base_url');

		var serialize = $('#payType_add-form').serialize();
		var tableGrid = $('#payTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Paytype/payjson',
							beforeSend:function() {
								$.LoadingOverlay('show');
							},
							complete:function() {
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'paytypeid'},
							{data:'description'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.paytypeid,function(){

									var paytypeid = $(this).data('id');
									var description = $(this).data('description');
									var frequency = $(this).data('frequency');
									var date_range = $(this).data('range');
									var date_arr = date_range.toString().split('-');
									var from = date_arr[0];
									var to = date_arr[1];

									$('.paytypeid').val(paytypeid);
									$('#pay_info').val(description);
									$('#edit_frequency').val(frequency);
									$('#edit_date_range_from').val(from);
									$('#edit_date_range_to').val(to);

								});


								$(document).on('click','#delete-btn'+data.paytypeid,function(){

									var deptId = $(this).data('id');
									var description = $(this).data('description');
									$('.paytypeid').val(deptId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.paytypeid+"' data-id='"+data.paytypeid+"' data-description='"+data.description+"' data-frequency = '"+data.frequency+"' data-range = '"+data.date_range+"' data-toggle='modal' data-target='#editPayTypeModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.paytypeid+"' data-id='"+data.paytypeid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deletePayTypeModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}

						}]


				});

		$('#btnSearchPayType').click(function(){
			var payType = $('.searchArea').val();
			$('#payTable').DataTable().destroy();

			var tableGrid = $('#payTable').DataTable({
							processing:false,
							serverSide:true,
							searching: false,
							ajax:{
								url: base_url+'settings/Paytype/payjson',
								beforeSend:function() {
									$.LoadingOverlay('show');
								},
								data: { searchValue: payType },
								complete:function() {
									$.LoadingOverlay('hide');
								}
							},
							columns:[
								{data:'paytypeid'},
								{data:'description'}
							],
							columnDefs:[{
								"targets":2,
								"data":null,
								"render":function(data, type, row, meta) {

									$(document).on('click','#edit-btn'+data.paytypeid,function(){

										var paytypeid = $(this).data('id');
										var description = $(this).data('description');
										var frequency = $(this).data('frequency');
										var date_range = $(this).data('range');
										var date_arr = date_range.toString().split('-');
										var from = date_arr[0];
										var to = date_arr[1];

										$('.paytypeid').val(paytypeid);
										$('#pay_info').val(description);
										$('#edit_frequency').val(frequency);
										$('#edit_date_range_from').val(from);
										$('#edit_date_range_to').val(to);

									});


									$(document).on('click','#delete-btn'+data.paytypeid,function(){

										var deptId = $(this).data('id');
										var description = $(this).data('description');
										$('.paytypeid').val(deptId);
										$('.info_desc').html(description)
									});

									var buttons = "";
									buttons += "<center>";
									buttons += "<button type='button' id='edit-btn"+data.paytypeid+"' data-id='"+data.paytypeid+"' data-description='"+data.description+"' data-frequency = '"+data.frequency+"' data-range = '"+data.date_range+"' data-toggle='modal' data-target='#editPayTypeModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
									buttons += " <button type='button' id='delete-btn"+data.paytypeid+"' data-id='"+data.paytypeid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deletePayTypeModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
									buttons += "</center>";

									return buttons;
								}

							}]


					});
		});

		$('#addPayTypeBtn').click(function(){

		var description = $('#addPay_desc').val();
		var frequency = $('#frequency').val();
		var date_range_from = $('#date_range_from').val();
		var date_range_to = $('#date_range_to').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			pay_desc:description,
			frequency: frequency,
			date_range_from: date_range_from,
			date_range_to: date_range_to
		};

			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Paytype/create',
				data: data,
				beforeSend:function() {
					$.LoadingOverlay('show');
				},
				success:function(data){


						var result = JSON.parse(data);
						$('#addPayTypeBtn').attr('disabled',false);
						$('#addPayTypeBtn').html('Add');
						$.LoadingOverlay('hide');

						if(result.success == 1){
							$('#addPay_desc').val("");
							$('#addPayTypeModal').modal('toggle');
							notificationSuccess('Success',result.message);
							tableGrid.ajax.reload(null,false);
						}
						else{
							$('#addPay_desc').val("");
							notificationError('Error',result.message);
						}
				}



			});
		});

		$('#addPayTypeModal').on('hidden.bs.modal', function () {
			$('#addPay_desc').val("");
		});

		$('#editPayTypeBtn').click(function(){

		var paytypeid = $('.paytypeid').val();
		var description = $('#pay_info').val();

		var data = {
			id:paytypeid,
			pay_desc:description
		};

		$.ajax({
			url: base_url+'settings/Paytype/update',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				$('#editPayTypeModal').modal('toggle');
				$.LoadingOverlay('hide');
				notificationSuccess('Success',data.message);
				tableGrid.ajax.reload(null,false);
			}
		});

	});


		$('#delPayTypeBtn').click(function(){

		var paytypeid = $('.paytypeid').val();

		var data = {
			id:paytypeid
		};

		$.ajax({
			url: base_url+'settings/Paytype/destroy',
			type:'POST',
			data:data,
			beforeSend:function() {
				$.LoadingOverlay('show');
			},
			success:function(data) {

				var result = JSON.parse(data);
				$('#deletePayTypeModal').modal('toggle');
				$.LoadingOverlay('hide');
				notificationSuccess('Success',result.message);
				tableGrid.ajax.reload(null,false);
			}
		});

	});
});
