$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$("#login-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		var loginUsername = $("#login-username").val();
		var loginPassword = $("#login-password").val();

		if (loginUsername == '' || loginPassword == '') {

		}else{
			$.ajax({
				type: 'post',
				url: base_url+'Main/login',
				data: serial,
				beforeSend:function(data){
					$('.btnLogin').attr('disabled',true);
				},
				success:function(data){
					$('.btnLogin').attr('disabled',false);

					if(data.success == 1) {
						var userData = data.userData;
						var token = data.token_session;
						// if(data.visible == 1)
						window.location.href = ''+base_url+'Main/home/'+token;

					}else {
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
						// setTimeout(function() {

						// }, 5000);
					}
				}
			});
		}

	});

	$("#reset_passForm").submit(function(e){
		e.preventDefault();
		var forgot_pw_email = $('#forgot_pw_email').val();
		if(forgot_pw_email != ''){
			$('#forgot_pw_email').css('border', '1px solid gainsboro');
			$.ajax({
			  url: base_url+'Main/forgot_pass',
			  type: 'post',
			  data: new FormData(this),
				contentType: false,
				processData: false,
			  beforeSend: function(){
			    $.LoadingOverlay('show');
					$('#btn_reset_pass').attr('disabled');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
					$('#btn_reset_pass').prop('disabled', false);
			    if(data.success == 1){
						$('#forgot_pw_modal').modal('hide');
						notificationSuccess('Success',data.message);
			    }else{
						notificationError('Error', data.message);
			    }
			  }
			});
		}else{
			$('#forgot_pw_email').css('border', '1px solid #ef4131');
			notificationError('Error', ' Please fill up all required fields');
		}
	});

	// $('.js-example-basic-single').select2({
	// 	placeholder: 'Select an option'
	// });

	$(document).on('click','.login_nav', function(){
		var nav_id = $(this).get(0).id;
		$('#nav_div').hide('slow');
		switch (nav_id) {
			case 'admin_tab':
				$('#emplogin_div').hide('slow')
				$('#adminlogin_div').show('slow');
				break;
			case 'emp_tab':
				$('#adminlogin_div').hide('slow');
				$('#emplogin_div').show('slow')
				break;
			default:

		}
	});

	$(document).on('click', '.btnBack', function(){
		$('.login_div').hide('slow');
		$('#nav_div').show('slow');
	});

	$(document).on('submit', '#adminlogin-form', function(e){
		e.preventDefault();

		$.ajax({
		  url: base_url+'Main/login',
		  type: 'post',
		  data:{
				loginUsername: $('#adminlogin-username').val(),
				loginPassword: $('#adminlogin-password').val(),
				login_type: 'admin'
			},
		  beforeSend: function(){
		    $.LoadingOverlay('show');
				$('.btnLogin').prop('disabled',true);
		  },
		  success: function(data){
		    $.LoadingOverlay('hide');
		    if(data.success == 1){
					var userData = data.userData;
					var token = data.token_session;
					window.location.href = ''+base_url+'Main/home/'+token;
		    }else{
					$('.btnLogin').prop('disabled',false);
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
	});

	$(document).on('submit', '#emplogin-form', function(e){
		e.preventDefault();

		$.ajax({
		  url: base_url+'Main/login',
		  type: 'post',
		  data:{
				loginUsername: $('#login-username').val(),
				loginPassword: $('#login-password').val(),
				login_type: 'employee'
			},
		  beforeSend: function(){
		    $.LoadingOverlay('show');
				$('.btnLogin').prop('disabled',true);
		  },
		  success: function(data){
		    $.LoadingOverlay('hide');
		    if(data.success == 1){
					var userData = data.userData;
					var token = data.token_session;
					window.location.href = ''+base_url+'Main/home/'+token;
		    }else{
					$('.btnLogin').prop('disabled',false);
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
	});

	$(document).on('click', '#btn_forgot_pw_modal', function(){
		$('#forgot_pw_modal').modal();
	});

	$(document).on('submit', '#reset_form', function(e){
		e.preventDefault();
		var error = 0;
		var errorMsg = "";

		$('.rq_pass').each(function(){
		  if($(this).val() == ""){
		    $(this).css("border", "1px solid #ef4131");
		  }else{
		    $(this).css("border", "1px solid gainsboro");
		  }
		});

		$('.rq_pass').each(function(){
		  if($(this).val() == ""){
		    $(this).focus();
		    error = 1;
		    errorMsg = "Please fill up all required fields.";
		    return false;
		  }
		});

		if(error == 0){
		  $.ajax({
		    url: base_url+'Main/reset_change_pass',
		    type: 'post',
		    data: new FormData(this),
				contentType: false,
				processData: false,
		    beforeSend: function(){
		      $.LoadingOverlay('show');
					$('#btn_reset_pass2').attr('disabled');
		    },
		    success: function(data){
		      $.LoadingOverlay('hide');
					$('#btn_reset_pass2').prop('disabled', false);
		      if(data.success == 1){
						notificationSuccess('Success', data.message);
						setTimeout(() => {
							window.location.href = base_url+'/'+data.bcode;
						},2000);
		      }else{
						notificationError('Error', data.message);
		      }
		    }
		  });
		}else{
		  notificationError('Error', errorMsg);
		}
	})
});
