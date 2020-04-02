$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$(".avatar_file").change(function(){
		var avatar_file = $(this).val();

		if (avatar_file == ""){
			$(".saveChangeAvatarBtn").prop('disabled',true);
		}else{
			$(".saveChangeAvatarBtn").prop('disabled',false);
		}
	});

	//File Upload profile pic
    $("#saveChangeAvatarForm").submit(function(e){
    	e.preventDefault();
    	var data = new FormData($(this)[0]);
    	$.ajax({ 
            type:"POST",
            url:base_url+"Main_profile_settings/save_changeavatar",
            data:data,
            mimeType: "multipart/form-data",
            contentType: false,
            processData: false,
            success: function(data) {
                if(data == '{"success":1}'){
                	$.toast({
					    heading: 'Success',
					    text: "Your Avatar change successfully!",
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
					    bgColor: '#5cb85c',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000
					});
                    // $("#changepicModal").modal("hide");
                    setTimeout(function(){
                    	 window.location.reload();
                    },1000);
                   
                }else{
                    $.toast({
					    heading: 'Note',
					    text: "please attach the allowed type file",
					    icon: 'error',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#f0ad4e',
						textColor: 'white'  
					});
                }
            }
        });
    });
		
	$("#saveChangePassForm").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		var thiss = $(this);
		var pass = $(".secNewpass").val();

        //if (validate_strong_password(pass)) {
			$.ajax({
				type:'post',
				url: base_url+'Main_profile_settings/save_changepass_user',
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
						// $.toast({
						//     heading: 'Error',
						//     text: data.message,
						//     icon: 'error',
						//     loader: false,   
						//     stack: false,
						//     position: 'top-center',  
						//     bgColor: '#d9534f',
						// 	textColor: 'white'        
						// });

						$.toast({
						    heading: 'Note',
						    text: data.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#f0ad4e',
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
});