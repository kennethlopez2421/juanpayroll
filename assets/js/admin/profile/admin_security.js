$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$(".secOldpass, .secNewpass, .secRetypenewpass").keyup(function(){
		var secOldpass = $('.secOldpass').val();
		var secNewpass = $('.secNewpass').val();
		var secRetypenewpass = $('.secRetypenewpass').val();

		if (secOldpass == "" || secNewpass == "" || secRetypenewpass == "") {
			$(".saveChangePassBtn").prop('disabled',true);
		}else{
			$(".saveChangePassBtn").prop('disabled',false);
		}
	});
		
	$("#saveChangePassForm").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		var thiss = $(this);
		var pass = $(".secNewpass").val();

        //if (validate_strong_password(pass)) {
			$.ajax({
				type:'post',
				url: base_url+'Main/saveChangePass_user',
				data: serial,
				beforeSend:function(data){
					$(".saveChangePassBtn").prop('disabled', true); 
					$(".saveChangePassBtn").text("Please wait..."); 
				},
				success:function(data){
					$(".saveChangePassBtn").prop('disabled', false); 
					$(".saveChangePassBtn").text("Save");
					if (data.success == 1) {
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
							hideAfter: 4000
						});
						$(thiss).find('input').val(''); //clear the password fields
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
		//}
	});

	$(".secEmail").keyup(function(){
		var secEmail = $('.secEmail').val();

		if (secEmail == "") {
			$(".saveChangeEmailBtn").prop('disabled',true);
		}else{
			$(".saveChangeEmailBtn").prop('disabled',false);
		}
	});

	$("#saveChangeEmailForm").submit(function(e){
		
		e.preventDefault();

		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/saveChangeEmail_player',
			data: serial,
			beforeSend:function(data){
				$(".saveChangeEmailBtn").prop('disabled', true); 
				$(".saveChangeEmailBtn").text("Please wait..."); 
			},
			success:function(data){
				$(".saveChangeEmailBtn").prop('disabled', false); 
				$(".saveChangeEmailBtn").text("Save");
				if (data.success == 1) {
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
						hideAfter: 4000
					});

					$(thiss).find('input').val('');
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