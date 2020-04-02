$(function(){
	console.log("Timerecordsummary_range");
		var base_url = $('body').data('base_url');
				//var serialize = $('#addCA-form').serialize();
				var trs_range_tbl = $('#caTable').DataTable({
						processing:false,
						serverSide:true,
						ajax:{
							url: base_url+'settings/Timerecordsummary_range/trs_range',
						},
						columns:[
							{data:'id'},
							{data:'range_start'},
							{data:'range_end'},
							{data:'description'},
							{data: function(data, type, dataToSet){
								var setactive = "";
								if(data.current_date_used == 1){
									setactive += "<span class = 'text-success'>Active</span>";
								}else if(data.current_date_used == 0){
									setactive += "<span class = 'text-warning'>Inactive</span>";
								}else{
									setactive += "<span class = 'text-danger'>Error</span>";
								}
								return setactive;
							}},

						],
						columnDefs:[
						{
							"targets":5,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.id,function(){

									var id = $(this).data('id');
									var range_start = $(this).data('range_start');
									var range_end = $(this).data('range_end');
									var description = $(this).data('description');

									//will pass the data from var caID, description, etc
									$('.trsid').val(id);
									$("#trs_start_edit").val(range_start);
									$("#trs_end_edit").val(range_end);
									$("#trs_desc_edit").val(description);

								});


								$(document).on('click','#delete-btn'+data.id,function(){
									var trsid = $(this).data('id');
									$('.trsid').val(trsid);
								});
								$(document).on('click', '#setactive-btn'+data.id,function(){
									var trsid = $(this).data('id');
									$('.trsid').val(trsid);
								})

								var buttons = "";
								buttons += " <button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-range_start='"+data.range_start+"'data-range_end='"+data.range_end+"' data-description='"+data.description+"' data-toggle='modal' data-target='#edittrsmodal' class='btn btn-primary' style='width:26%;'> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-toggle='modal' data-target='#deltrsmodal' class='btn btn-danger' style='width:26%;'> Delete</button>";
								buttons += " <button type='button' id='setactive-btn"+data.id+"' data-id = '"+data.id+"' class='btn btn-info' data-toggle = 'modal' data-target = '#setactivemodal' style='width:30%;'>Set Active</button>";


								return buttons;

							}

						}],


				});
 $('#searchButton').click(function(){
 	var searchText = $('#caTableTB').val();
 	trs_range_tbl.search(searchText).draw();
 });
//add new Position to DB
	$('#addtrsbtn').click(function(){
		var start_date = $("#trs_start_add").val();
		var end_date = $("#trs_end_add").val();
		var description = $("#trs_desc_add").val();
		console.log(description);
		$(this).attr('disabled',true);
		$(this).html('Please Wait');
		var data = {
			start_date:start_date,
			end_date:end_date,
			description:description,
		};
			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Timerecordsummary_range/create',
				data: data,
				success:function(data){
					var result = JSON.parse(data);
					$('#addtrsbtn').attr('disabled',false);
					$('#addtrsbtn').html('Add');
					if(result.success == 0){
						$("#trs_desc_add").val("");
						notificationError('Error',result.message);
						trs_range_tbl.ajax.reload(null,false);
						}
					else{
						$.LoadingOverlay('show');
						$("#trs_desc_add").val("");
						$('#addCAModal').modal('toggle');
						notificationSuccess('Success',result.message);
						trs_range_tbl.ajax.reload(null,false);
						$.LoadingOverlay('hide');

						}
				}
			});
	});

		$('#addCAModal').on('hidden.bs.modal', function () {
			$('#trs_desc_add').val("");
		});

		//Update position selected from table
		//show the current position on the input field

		$('#edittrsbtn').click(function(){
		var id = $(".trsid").val();
		var start_date = $("#trs_start_edit").val();
		var end_date = $("#trs_end_edit").val();
		var description = $("#trs_desc_edit").val();
		var data = {
			id:id,
			start_date:start_date,
			end_date:end_date,
			description:description
		};
		$.ajax({
			url: base_url+'settings/Timerecordsummary_range/update',
			type:'POST',
			beforesend:function(){
					$.LoadingOverlay('show');
								},
			data:data,
			success:function(data) {
				var result = JSON.parse(data);
				if(result.success == 0){
					$('#edittrsmodal').modal('toggle');
					notificationError('Error',result.message);
					trs_range_tbl.ajax.reload(null,false);
				}else{
					$.LoadingOverlay('show');
					$('#edittrsmodal').modal('toggle');
					notificationSuccess('Success',result.message);
					trs_range_tbl.ajax.reload(null,false);
					$.LoadingOverlay('hide');
				}
			}


		});

	});
		$('.deltrsbtn').click(function(){
		var trsid = $('.trsid').val();
		var data = {
			id:trsid
		};

		$.ajax({
			url: base_url+'settings/Timerecordsummary_range/destroy',
			type:'POST',
			data:data,
			success:function(data) {
				console.log(data)
				var result = JSON.parse(data);
				console.log(result)
				$.LoadingOverlay('show');
				$('#deltrsmodal').modal('toggle');
				notificationSuccess('Success',result.message);
				trs_range_tbl.ajax.reload(null,false);
				$.LoadingOverlay('hide');

			}
		});

	});
	$("#setactivebtn").click(function(){
		var id = $(".trsid").val();
		var data = {
			id:id
		};
		$.ajax({
			url: base_url + "settings/Timerecordsummary_range/set_active",
			type:'POST',
			data:data,
			beforesend: function(){
				$.LoadingOverlay('show');
			},
			success:function(data){
				var result = JSON.parse(data);
				// console.log(result);
				if(result.success == 1){
					$.LoadingOverlay('hide');
					$("#setactivemodal").modal('toggle');
					notificationSuccess("Success", result.message);
					trs_range_tbl.ajax.reload(null,false);

				}else{
					$.LoadingOverlay('hide');
					$("#setactivemodal").modal('toggle');
					$notificationError("Error", result.message);
					trs_range_tbl.ajax.reload(null,false);
				}

			}
		});
	});
});
