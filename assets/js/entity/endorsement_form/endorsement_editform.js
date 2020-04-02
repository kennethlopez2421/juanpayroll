$(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	href="<?=base_url('Main/index');?>";

	$("#preview_endorsement_form :input").prop("disabled", true);
	$(".btnViewApp").prop('disabled',false);
	$(".btnEditApp").prop('disabled',false);

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$(".btnViewApp").click(function(e){
		e.preventDefault();
		var lsf_app_id = $(this).data('value');

		window.location.href = base_url+"Main_entity/pdf_endorsement_form/"+lsf_app_id;

		// $.ajax({
		// 		type: 'post',
		// 		url: base_url+'Main_entity/pdf_endorsement_form',
		// 		data: {'lsf_app_id':lsf_app_id},
		// 		beforeSend:function(data){
		// 			$('.btnViewApp').attr('disabled',true);
		// 			$('.btnViewApp').text('Please wait...');
		// 		},
		// 		success:function(data){
		// 			$('.btnViewApp').attr('disabled',false);
  //                   $('.btnViewApp').text('Export to PDF');
		// 		}
		// 	});
	});	

	$(".btnEditApp").click(function(e){
		e.preventDefault();
		$(".btnSaveApp").prop('hidden',false);
		$(".btnCancel").prop('hidden',false);
		$(this).prop('hidden', true);
	

		$("#preview_endorsement_form :input").prop("disabled",false);
		$('.not-editable').prop('disabled',true);
		// $("#other_value").prop("disabled", true);
		$.toast({
		    heading: 'Information',
		    text: 'The fields are editable',
		    icon: 'info',
		    loader: false,  
		    stack: false,
		    position: 'top-center', 
		    bgColor: 'rgb(43, 144, 217)',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 4000
		});
	});

	$(".btnCancel").click(function(e){
		e.preventDefault();
		$(".btnSaveApp").prop('hidden',true);
		$(".btnCancel").prop('hidden',true);
		$(".btnEditApp").prop('hidden',false);
		$("#preview_endorsement_form :input").prop("disabled",true);
		$(".btnEditApp").prop('disabled',false);
		$(".btnViewApp").prop('disabled',false);
	});

	$(".btnSaveApp").click(function(e){
		e.preventDefault();
		var serial = $("#preview_endorsement_form").serialize();
	
		$.ajax({
			type: 'post',
			url: base_url+'Main_entity/edit_endorsement',
			data: serial,
			beforeSend:function(data){
				$('.btnSaveApp').attr('disabled',true);
				$('.btnSaveApp').text('Please wait...');
				$('.btnCancel').attr('disabled',true);
			},
			success:function(data){
				$('.btnSaveApp').text('Submit');
				$('.btnSaveApp').attr('disabled',false);
				$('.btnCancel').attr('disabled',false);
				if(data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
					    bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 10000
					});
					setTimeout(function () { 
				      location.reload();
					}, 2 * 1000);
				}else{
					$.toast({
					    heading: 'Error',
					    text: data.message,
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#d9534f',
						textColor: 'white'        
					});
				}
			}
		});
	});

	$("input[name='franchisee_type']").click(function(){

		if($("input[name='franchisee_type']:checked").val() == 1){
			// disable and uncheck checkbox from existing franchisee
			$('.efcheckbox').prop('disabled', true);
			$('.efcheckbox').prop('checked', false);

			//enabled checkbox for new franchisee
			$('.nfcheckbox').prop('disabled', false); 


		}else if($("input[name='franchisee_type']:checked").val() == 2){
			// disable and uncheck checkbox from new franchisee
			$('.nfcheckbox').prop('disabled', true);
			$('.nfcheckbox').prop('checked', false);

			//enabled checkbox for existing franchisee
			$('.efcheckbox').prop('disabled', false);
		}
	})

});