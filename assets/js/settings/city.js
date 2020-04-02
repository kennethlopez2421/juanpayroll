$(function(){

	var base_url = $("body").data('base_url');

	 var cityTable = $('#cityTable').DataTable({
						filter:true,
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/City/cityjson',
							beforeSend:function(data){
								$.LoadingOverlay("show");
							},
							complete:function(data) {
								$.LoadingOverlay("hide");
							}
						},
						columns:[
							{data:'cityid'},
							{data:'description'},
							{data:'country_desc'}

						],
						columnDefs:[{
							"targets":3,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.cityid,function(){

									var cityId = $(this).data('id');
									var description = $(this).data('description');
									var countryId = $(this).data('countryid');
									var country = $(this).data('country');


									//show blank if the country is deleted
									if(country == null) {
										$('#editCountry option:eq(0)').prop('selected','selected').change();
									}else {
										$('#editCountry option:contains("'+country+'")').prop('selected','selected').change();
									}

									$('.cityid').val(cityId);
									$('#editCityDesc').val(description);

								});

								$(document).on('click','#delete-btn'+data.cityid,function(){

									var cityId = $(this).data('id');
									var description = $(this).data('description');
									$('.cityid').val(cityId);
									$('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.cityid+"' data-id='"+data.cityid+"' data-description='"+data.description+"' data-countryid='"+data.countryid+"' data-country='"+data.country+"' data-toggle='modal' data-target='#editCityModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.cityid+"' data-id='"+data.cityid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delCityModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";
								return buttons;
							}
						}]

					});


	// $('#btn_searchCity').click(function(){
	// 	var city = $('#searchCity').val();
	// 	if(!city == ""){
	// 		cityTable.ajax.url(base_url+'settings/City/searchcityjson/'+city).load();
	// 	}
	// });

	$('#btn_searchCity').click(function(){
	 	var searchText = $('#searchCity').val();

 	 $('#cityTable').DataTable().destroy();



	 $('#cityTable').DataTable({
	 				 filter:true,
	 				 processing:false,
	 				 serverSide:true,
	 				 searching: false,
	 				 ajax:{
	 					 url: base_url+'settings/City/cityjson',
						 type:'GET',
						 data:{
							 'searchData':searchText
						 },
	 					 beforeSend:function(data){
	 						 $.LoadingOverlay("show");
	 					 },
	 					 complete:function(data) {
	 						 $.LoadingOverlay("hide");
	 					 }
	 				 },
	 				 columns:[
	 					 {data:'cityid'},
	 					 {data:'description'}
	 				 ],
	 				 columnDefs:[{
	 					 "targets":2,
	 					 "data":null,
	 					 "render":function(data, type, row, meta) {

	 						 $(document).on('click','#edit-btn'+data.cityid,function(){

	 							 var cityId = $(this).data('id');
	 							 var description = $(this).data('description');
	 							 var countryId = $(this).data('countryid');
	 							 var country = $(this).data('country');


	 							 //show blank if the country is deleted
	 							 if(country == null) {
	 								 $('#editCountry option:eq(0)').prop('selected','selected').change();
	 							 }else {
	 								 $('#editCountry option:contains("'+country+'")').prop('selected','selected').change();
	 							 }

	 							 $('.cityid').val(cityId);
	 							 $('#editCityDesc').val(description);

	 						 });

	 						 $(document).on('click','#delete-btn'+data.cityid,function(){

	 							 var cityId = $(this).data('id');
	 							 var description = $(this).data('description');
	 							 $('.cityid').val(cityId);
	 							 $('.info_desc').html(description)
	 						 });

	 						 var buttons = "";
	 						 buttons += "<center>";
	 						 buttons += "<button type='button' id='edit-btn"+data.cityid+"' data-id='"+data.cityid+"' data-description='"+data.description+"' data-countryid='"+data.countryid+"' data-country='"+data.country+"' data-toggle='modal' data-target='#editCityModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
	 						 buttons += " <button type='button' id='delete-btn"+data.cityid+"' data-id='"+data.cityid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delCityModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
	 						 buttons += "</center>";
	 						 return buttons;
	 					 }
	 				 }]

	 			 });




	});

	$('#addCityBtn').click(function(){

		var description = $('#addCityDesc').val();
		var countryId = $('#country').val();
		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			countryId:countryId
		};

		$.ajax({
			url: base_url+'settings/City/create',
			type:'POST',
			data:data,
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data) {

				var result = JSON.parse(data);
				$.LoadingOverlay("hide");
				$('#addCityBtn').attr('disabled',false);
				$('#addCityBtn').html('Add');

				if(result.success == 1) {
					$('#addCityDesc').val("");
					$('#addCityModal').modal('toggle');
					notificationSuccess('Success',result.message);
					cityTable.ajax.reload(null,false);
				}else {
					$('#addCityDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addCityModal').on('hidden.bs.modal', function () {
		$('#addCityDesc').val("");
	})

	$('#editCityBtn').click(function(){

		var cityId = $('.cityid').val();
		var editCityDesc = $('#editCityDesc').val();
		var countryId = $('#editCountry').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');


		var data = {
			id:cityId,
			description:editCityDesc,
			countryid:countryId
		};

		$.ajax({
			url: base_url+'settings/City/update',
			type:'POST',
			data:data,
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data) {

				var result = JSON.parse(data);
				$('#addCityBtn').attr('disabled',false);
				$('#addCityBtn').html('Add');

				$.LoadingOverlay("hide");
				$('#editCityModal').modal('toggle');
				notificationSuccess('Success',result.message);
				cityTable.ajax.reload(null,false);
			}
		});

	});

	$('#delCityBtn').click(function(){

		var cityId = $('.cityid').val();

		var data = {
			id:cityId
		};

		$.ajax({
			url: base_url+'settings/City/destroy',
			type:'POST',
			data:data,
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data) {
				$.LoadingOverlay("hide");
				var result = JSON.parse(data);
				$('#delCityModal').modal('toggle');
				notificationSuccess('Success',result.message);
				cityTable.ajax.reload(null,false);
			}
		});

	});


});
