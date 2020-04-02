$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();


	$("#personalinfo-form").submit(function(e){
		e.preventDefault();

		$(this).find('input, select').prop('disabled',false);
		$(this).find('.datepicker').prop('readonly',false);
		$(this).find('input:first').focus();

		$(this).find('input:submit').prop('hidden',true);
		$(this).find('button').prop('hidden',false);
		$(this).find('.asterisk').prop('hidden', false);

		$(this).find('.emailAdd').prop('disabled',true);
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

	$(".cancelEditBtn").click(function(e){
		
		e.preventDefault();

		var thiss = $("#personalinfo-form");
		//SaveEditBtn
		$(thiss).find('input, select').prop('disabled',true);
		$(thiss).find('.datepicker').prop('readonly',false);

		$(thiss).find('input:submit').prop('hidden',false);
		$(thiss).find('input:submit').prop('disabled',false);
		$(thiss).find('button').prop('hidden',true);
		$(thiss).find('.asterisk').prop('hidden', true);

	});


	$(".saveEditBtn").click(function(e){
		
		e.preventDefault();

		var thiss = $("#personalinfo-form");

		var serial = thiss.serialize();
		// $(thiss).find('input, select').prop('disabled',true);
		// $(thiss).find('.datepicker').prop('readonly',false);

		// $(thiss).find('input:submit').prop('hidden',false);
		// $(thiss).find('input:submit').prop('disabled',false);
		// $(thiss).find('button').prop('hidden',true);

		$.ajax({
			type:'post',
			url: base_url+'Main/saveInfo_user',
			data: serial,
			beforeSend:function(data){
				$(".cancelEditBtn, .saveEditBtn").prop('disabled', true); 
				
			},
			success:function(data){
				$(".cancelEditBtn, .saveEditBtn").prop('disabled', false);
				if (data.success == 1) {

					$(thiss).find('input, select').prop('disabled',true);
					$(thiss).find('.datepicker').prop('readonly',false);

					$(thiss).find('input:submit').prop('hidden',false);
					$(thiss).find('input:submit').prop('disabled',false);
					$(thiss).find('button').prop('hidden',true);
					$(thiss).find('.asterisk').prop('hidden', true);

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
});