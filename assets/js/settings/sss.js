$(function(){
		var base_url = $('body').data('base_url');

		// $('#editableTable').hide();
		// $('#addBtn').hide();

				var serialize = $('#addContribution-form').serialize();
				var sssTable = $('#editableTable').DataTable({
					processing:"true",
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'settings/Sss/sssjson',
							dataSrc:'data'
						},
						columns:[
							{data:'salRange_from'},
							{data:'salRange_to'},
							{data:'monthly_sal_cred'},
							{data:'ss_er'},
							{data:'ss_ee'},
							{data:'ss_total'},
							{data:'ec_er'},
							{data:'tc_er'},
							{data:'tc_ee'},
							{data:'tc_total'},
							{data:'ofw'},
						],
						columnDefs:[{
							"targets":11,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.id,function(){

									var id = $(this).data('id');
									var data = {id:id};

									$.ajax({
										type: 'POST',
										url:base_url+ 'settings/Sss/get_ss_by_id',
										data:data,
										success:function(data){

												var res = data.result;
												// console.log(JSON.stringify(res, undefined, 2));
												//console.log(res[0].salRange_from)
												$('#id').val(id);
												$('#editrangefrom_desc').val(res[0].salRange_from);
												$('#editrangeto_desc').val(res[0].salRange_to);
												$('#editSalCred_desc').val(res[0].monthly_sal_cred);
												$('#editER').val(res[0].ss_er);
												$('#editEE').val(res[0].ss_ee);
												$('#editSSTotal').val(res[0].ss_total);
												$('#editEC').val(res[0].ec_er);
												$('#editContirbutionER').val(res[0].tc_er);
												$('#editContributionEE').val(res[0].tc_ee);
												$('#editTotalCont').val(res[0].tc_total);
												$('#edit_SVO_totalContribution').val((res[0].SV_VM_OFW == 0)? parseFloat(res[0].ss_er) + parseFloat(res[0].ss_ee) : res[0].SV_VM_OFW);
										}
									});
								});


								$(document).on('click','#delete-btn'+data.id,function(){

									var id = $(this).data('id');
									var salRange_from = $(this).data('salRange_from');
									var salRange_to = $(this).data('salRange_to');
									var monthly_sal_cred = $(this).data('monthly_sal_cred');
									var ss_er = $(this).data('ss_er');
									var ss_ee = $(this).data('ss_ee');
									var ss_total = $(this).data('ss_total');
									var ec_er = $(this).data('ec_er');
									var tc_er = $(this).data('tc_er');
									var tc_ee = $(this).data('tc_ee');
									var tc_total = $(this).data('tc_total');


									$('.id').val(id);
									$('#editrangefrom_desc').html(salRange_from);
									$('#editrangeto_desc').html(salRange_to);
									$('#editSalCred_desc').html(monthly_sal_cred);
									$('#editER').html(ss_er);
									$('#editEE').html(ss_ee);
									$('#editSSTotal').html(ss_total);
									$('#EC').html(ec_er);
									$('#editTotalER').html(tc_er);
									$('#editTotalEE').html(tc_ee);
									$('#editTotalCont').html(tc_total);
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-toggle='modal' data-target='#updatSSSModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-toggle='modal' data-target='#deleteSSSModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
								buttons += "</center>";

								return buttons;
							}

						}]


				});




	$('#addBtnControls').click(function(){
		$('.sssTable').hide();
		$('.sssTbl_header').hide();
		$('.sssDataTable').show();
		$('.dataTbl_header').show();
		// $(this).hide();
		// $('#addBtn').show();
		// $('#editableTable').show();
		// $('#sssViewOnly').hide();
	});

	var total_sss = 0;
	$('#addER').blur(function(){
		if($('#addEE').val() != ""){
			total_ss = parseFloat($(this).val()) + parseFloat($('#addEE').val());
			$('#addTotalSS').val(total_ss);
			$('#SVO_totalContribution').val(total_ss);
		}
	});

	$('#addEE').blur(function(){
		if($('#addER').val() != ""){
			total_ss = parseFloat($(this).val()) + parseFloat($('#addER').val());
			$('#addTotalSS').val(total_ss);
			$('#SVO_totalContribution').val(total_ss);
		}
	});

	var total_contribution = 0;
	$('#addContributionER').blur(function(){
		if($('#addContributionEE').val() != ""){
			total_contribution = parseFloat($(this).val()) + parseFloat($('#addContributionEE').val());
			$('#TotalContribution').val(total_contribution);
		}
	});

	$('#addContributionEE').blur(function(){
		if($('#addContributionER').val() != ""){
			total_contribution = parseFloat($(this).val()) + parseFloat($('#addContributionER').val());
			$('#TotalContribution').val(total_contribution);
		}
	});

		//add new SSS information to DB
		$('#addSSSinfo').click(function(){

			var error = 0;
			var errorMsg = "";
			var serialize = $('#addContribution-form').serialize();
			$('.sss_required').each(function(){
				if($(this).val() == ""){
					error = 1;
					errorMsg = "Please fill up all the required fields";
					$(this).css("border", "1px solid #ef4131");
				}else{
					$(this).css("border", "1px solid gainsboro");
				}

				$('.sss_required').each(function(){
					if($(this).val() == ""){
						$(this).focus();
						return;
					}
				});

				if(parseFloat($('#addrangefrom_desc').val()) >= parseFloat($('#addrangeto_desc').val())){
					error = 1;
					errorMsg = "Range Compensation Error. Please check your input value";
					$('#addrangefrom_desc, #addrangeto_desc').css("border", "1px solid #ef4131");
				}
			});

			if(error == 0){
				$.ajax({
					type: 'POST',
					url: base_url + 'settings/Sss/create',
					data: serialize,
					success:function(data){


						var result = JSON.parse(data);

						if(result.success == 1){

							$('#addContribution-form input').val("");
							$('#addContributionModal').modal('toggle');
							notificationSuccess('Success',result.message);
							sssTable.ajax.reload(null,false);
						}
						else{
							$('#addContribution-form input').val("");
							notificationError('Error',result.message);
							$('#addPositionModal').modal('toggle');
							sssTable.ajax.reload(null,false);

						}
					}
				});
			}else{
				notificationError('Error', errorMsg);
			}
		});

		//Update position selected from table
		//show the current position on the input field


		var edit_total_sss = 0;
		$('#editER').blur(function(){
			if($('#editEE').val() != ""){
				edit_total_ss = parseFloat($(this).val()) + parseFloat($('#editEE').val());
				$('#editSSTotal').val(edit_total_ss);
				$('#edit_SVO_totalContribution').val(edit_total_ss);
			}
		});

		$('#editEE').blur(function(){
			if($('#editER').val() != ""){
				edit_total_ss = parseFloat($(this).val()) + parseFloat($('#editER').val());
				$('#editSSTotal').val(edit_total_ss);
				$('#edit_SVO_totalContribution').val(edit_total_ss);
			}
		});

		var edit_total_contribution = 0;
		$('#editContirbutionER').blur(function(){
			if($('#editContributionEE').val() != ""){
				edit_total_contribution = parseFloat($(this).val()) + parseFloat($('#editContributionEE').val());
				$('#editTotalCont').val(edit_total_contribution);
			}
		});

		$('#editContributionEE').blur(function(){
			if($('#editContirbutionER').val() != ""){
				edit_total_contribution = parseFloat($(this).val()) + parseFloat($('#editContirbutionER').val());
				$('#editTotalCont').val(edit_total_contribution);
			}
		});

		$('#updateSSS').click(function(){


		var serialize = $('#updateSSS-form').serialize();
		$.ajax({
			url: base_url+'settings/Sss/update',
			type:'POST',
			data:serialize,
			success:function(data) {

				//var result = JSON.parse(data);


				if(data.success == 0){
					notificationError('Error',data.message);
				}else{
					$('#updateSSS-form').val();
					$('#updatSSSModal').modal('toggle');
					notificationSuccess('Success',data.message);
				sssTable.ajax.reload(null,false);
				}
			}
		});

	});
		$('.deleteContriBtn').click(function(){

		var id = $('#id').val();

		var data = {
			id:id
		};

		$.ajax({
			url: base_url+'settings/Sss/destroy',
			type:'POST',
			data:data,
			success:function(data) {
				console.log(data)
				var result = data;
				console.log(result)
				$('#deleteSSSModal').modal('toggle');
				notificationSuccess('Success',result.message);
				sssTable.ajax.reload(null,false);
			}
		});

	});
});
