$(function(){

	var base_url = $("body").data('base_url');

	$('#taxTable').hide();
	$('#newTaxBtn').hide();

	 var taxTable = $('#taxTable').DataTable({
						processing:"false",
						serverSide:true,
						bFilter:false,
						lengthChange:false,
						ajax:{
							url: base_url+'settings/Tax/taxjson',
							beforeSend:function(data){
								$.LoadingOverlay("show");
							},
							complete:function(data) {
								$.LoadingOverlay("hide");
							}
						},
						columns:[
							{data:'aibLowerLimit'},
							{data:'aibUpperLimit'},
							{data:'tr1LowerLimit'},
							{data:'tr1ExcessLimit'},
							{data:'tr2LowerLimit'},
							{data:'tr2ExcessLimit'}
						],
						columnDefs:[{
							"targets":6,
							"data":null,
							"width":'20%',
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.id,function() {

									var taxId = $(this).data('id');
									var aibLowerLimit = $(this).data('aiblowerlimit');
									var aibUpperLimit = $(this).data('aibupperlimit');
									var tr1LowerLimit = $(this).data('tr1lowerlimit');
									var tr1ExcessLimit = $(this).data('tr1excesslimit');
									var tr2LowerLimit = $(this).data('tr2lowerlimit');
									var tr2ExcessLimit = $(this).data('tr2excesslimit');

									$('.taxid').val(taxId);
									$('#editAibLowerLimit').val(aibLowerLimit);
									$('#editAibUpperLimit').val(aibUpperLimit);
									$('#editTr1LowerLimit').val(tr1LowerLimit);
									$('#editTr1ExcessLimit').val(tr1ExcessLimit);
									$('#editTr2LowerLimit').val(tr2LowerLimit);
									$('#editTr2ExcessLimit').val(tr2ExcessLimit);
								});

								$(document).on('click','#delete-btn'+data.id,function(){

									var taxId = $(this).data('id');
									$('.taxid').val(taxId);
								});

								var buttons = "";
								buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-aiblowerlimit='"+data.aibLowerLimit+"' data-aibupperlimit='"+data.aibUpperLimit+"' data-tr1lowerlimit='"+data.tr1LowerLimit+"' data-tr1excesslimit='"+data.tr1ExcessLimit+"' data-tr2lowerlimit='"+data.tr2LowerLimit+"' data-tr2excesslimit='"+data.tr2ExcessLimit+"' data-toggle='modal' data-target='#editTaxModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-toggle='modal' data-target='#delTaxModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Del</button>";

								return buttons;
							}
						}]

					});



	$('#ctrlBtn').click(function(){
		$('#newTaxBtn').show();
		$('#taxTableView').hide();
		$('#taxTable').show();
		$(this).hide();
		// $('#taxTable').css('border', '2px solid #505050');
		// $('#taxTable tr:nth-child(2)').css('border', '2px solid #505050 ');
		// $('#taxTable td:nth-child(2)').css('border-right', '2px solid #505050 ');
		// $('#taxTable td:nth-child(4)').css('border-right', '2px solid #505050 ');
		// $('#taxTable td:nth-child(6)').css('border-right', '2px solid #505050 ');
		// $('#taxTable td:nth-child(2)').attr('style', 'border:2px solid #505050 !important');
		// $('#taxTable td:nth-child(4)').attr('style', 'border:2px solid #505050 !important');
		// $('#taxTable td:nth-child(6)').attr('style', 'border:2px solid #505050 !important');
	});

	$('#addTaxModalBtn').click(function(){

		var aibLowerLimit = $('#aibLowerLimit').val();
		var aibUpperLimit = $('#aibUpperLimit').val();
		var tr1LowerLimit = $('#tr1LowerLimit').val();
		var tr1ExcessLimit = $('#tr1ExcessLimit').val();
		var tr2LowerLimit = $('#tr2LowerLimit').val();
		var tr2ExcessLimit = $('#tr2ExcessLimit').val();

		var data = {
			aibLowerLimit:aibLowerLimit,
			aibUpperLimit:aibUpperLimit,
			tr1LowerLimit:tr1LowerLimit,
			tr1ExcessLimit:tr1ExcessLimit,
			tr2LowerLimit:tr2LowerLimit,
			tr2ExcessLimit:tr2ExcessLimit
		};

		$.ajax({
			url: base_url+'settings/Tax/create',
			type:'POST',
			data:data,
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data) {

				var result = JSON.parse(data);

				$('#aibLowerLimit').val("");
				$('#aibUpperLimit').val("");
				$('#tr1LowerLimit').val("");
				$('#tr1ExcessLimit').val("");
				$('#tr2LowerLimit').val("");
				$('#tr2ExcessLimit').val("");

				$('#addTaxBtn').attr('disabled',false);
				$('#addTaxBtn').html('Add');

				$.LoadingOverlay("hide");
				$('#addTaxModal').modal('hide');
				notificationSuccess('Success',result.message);
				taxTable.ajax.reload(null,false);
			}
		});


	});

	$('#addCityModal').on('hidden.bs.modal', function () {
		$('#addCityDesc').val("");
	})

	$('#editTaxBtn').click(function() {

		var taxId = $('.taxid').val();
		var aibUpperLimit = $('#editAibUpperLimit').val();
		var aibLowerLimit = $('#editAibLowerLimit').val();
		var tr1LowerLimit = $('#editTr1LowerLimit').val();
		var tr1ExcessLimit = $('#editTr1ExcessLimit').val();
		var tr2LowerLimit = $('#editTr2LowerLimit').val();
		var tr2ExcessLimit = $('#editTr2ExcessLimit').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');


		var data = {
			taxId:taxId,
			aibLowerLimit:aibLowerLimit,
			aibUpperLimit:aibUpperLimit,
			tr1LowerLimit:tr1LowerLimit,
			tr1ExcessLimit:tr1ExcessLimit,
			tr2LowerLimit:tr2LowerLimit,
			tr2ExcessLimit:tr2ExcessLimit
		};

		$.ajax({
			url: base_url+'settings/Tax/update',
			type:'POST',
			data:data,
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data) {

				var result = JSON.parse(data);

				$('#editTaxBtn').attr('disabled',false);
				$('#editTaxBtn').html('Save Changes');

				$.LoadingOverlay("hide");
				$('#editTaxModal').modal('toggle');
				notificationSuccess('Success',result.message);
				taxTable.ajax.reload(null,false);
			}
		});

	});

	$('#delTaxBtn').click(function(){

		var taxId = $('.taxid').val();

		var data = {
			id:taxId
		};

		$.ajax({
			url: base_url+'settings/Tax/destroy',
			type:'POST',
			data:data,
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data) {
				$.LoadingOverlay("hide");
				var result = JSON.parse(data);
				$('#delTaxModal').modal('hide');
				notificationSuccess('Success',result.message);
				taxTable.ajax.reload(null,false);
			}
		});

	});


});
