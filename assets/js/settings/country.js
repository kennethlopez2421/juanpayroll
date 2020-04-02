$(function(){

	var base_url = $("body").data('base_url');

	 var countryTable = $('#countryTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Country/countryjson',
							beforeSend:function(){
								$.LoadingOverlay('show');
							},
							complete:function(){
								$.LoadingOverlay('hide');
							}
						},
						columns:[
							{data:'countryid'},
							{data:'description'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.countryid,function(){

									var countryId = $(this).data('id');
									var description = $(this).data('description');

									$('.countryid').val(countryId);
									$('#description').val(description);

								});

								$(document).on('click','#delete-btn'+data.countryid,function(){

									var countryId = $(this).data('id');
									var description = $(this).data('description');
									$('.countryid').val(countryId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.countryid+"' data-id='"+data.countryid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editCountryModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.countryid+"' data-id='"+data.countryid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delCountryModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";
								return buttons;
							}
						}]

					});


	$('#btnSearchCountry').click(function(){
		$('#countryTable').DataTable().destroy();
		var search = $('.searchArea').val();

		var countryTable = $('#countryTable').DataTable({
 						processing:false,
 						serverSide:true,
						searching: false,
 						ajax:{
 							url: base_url+'settings/Country/countryjson',
 							beforeSend:function(){
 								$.LoadingOverlay('show');
 							},
							data: {searchData: search},
 							complete:function(){
 								$.LoadingOverlay('hide');
 							}
 						},
 						columns:[
 							{data:'countryid'},
 							{data:'description'}
 						],
 						columnDefs:[{
 							"targets":2,
 							"data":null,
 							"render":function(data, type, row, meta) {

 								$(document).on('click','#edit-btn'+data.countryid,function(){

 									var countryId = $(this).data('id');
 									var description = $(this).data('description');

 									$('.countryid').val(countryId);
 									$('#description').val(description);

 								});

 								$(document).on('click','#delete-btn'+data.countryid,function(){

 									var countryId = $(this).data('id');
 									var description = $(this).data('description');
 									$('.countryid').val(countryId);
 									$('.info_desc').html(description)
 								});

 								var buttons = "";
 								buttons += "<center>";
 								buttons += "<button type='button' id='edit-btn"+data.countryid+"' data-id='"+data.countryid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editCountryModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 								buttons += " <button type='button' id='delete-btn"+data.countryid+"' data-id='"+data.countryid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delCountryModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
 								buttons += "</center>";
 								return buttons;
 							}
 						}]

 					});
	});

	$('#addCountryBtn').click(function(){

		var description = $('#addCountryDesc').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
		};

		$.ajax({
			url: base_url+'settings/Country/create',
			type:'POST',
			data:data,
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay("hide");
				$('#addCountryBtn').attr('disabled',false);
				$('#addCountryBtn').html('Add');

				if(result.success == 1) {
					$('#addCountryDesc').val("");
					$('#addCountryModal').modal('toggle');
					notificationSuccess('Success',result.message);
					countryTable.ajax.reload(null,false);
				}else {
					$('#addCountryDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addCountryModal').on('hidden.bs.modal', function () {
		$('#addCountryDesc').val("");
	});

	$('#editCountryBtn').click(function(){

		var countryId = $('.countryid').val();
		var description = $('#description').val();

		$.LoadingOverlay("show");

		var data = {
			id:countryId,
			description:description
		};

		$.ajax({
			url: base_url+'settings/Country/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay("hide");

				$('#editCountryModal').modal('toggle');
				notificationSuccess('Success',result.message);
				countryTable.ajax.reload(null,false);
			}
		});
	});

	$('#delCountryBtn').click(function(){

		var countryId = $('.countryid').val();

		var data = {
			id:countryId
		};

		$.ajax({
			url: base_url+'settings/Country/destroy',
			type:'POST',
			data:data,
			beforeSend:function(data) {
				$.LoadingOverlay("show");
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay("hide");
				$('#delCountryModal').modal('toggle');
				notificationSuccess('Success',result.message);
				countryTable.ajax.reload(null,false);
			}
		});

	});


});
