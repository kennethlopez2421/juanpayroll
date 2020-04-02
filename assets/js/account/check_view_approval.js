$(document).ready(function(){

	var base_url = $("body").data('base_url');

	$('.submitcheckbtn').click(function(e){
		var id = e.currentTarget.id;

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/approve_check',
	  		data:{'id':id},
	  		success:function(data){

	  			console.log(data);
	  			
	  			data = JSON.parse(data);

	  			$.toast({
				    heading: 'Success',
				    text: data.message,
				    icon: 'success',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: 'yellowgreen',
					textColor: 'white'  
				});

	  			setTimeout(function(){
	  				var currUrl = window.location.href;

				  	currUrl = currUrl.replace("check_view_approval", "check_approval");
				  	var rep = "/"+id;
				  	currUrl = currUrl.replace(rep,'');
				  	window.location = currUrl;

	  			}, 500);

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Error',
				    text: 'Something went wrong. Please try again.',
				    icon: 'error',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#d9534f',
					textColor: 'white'  
				});
	  		}
	  	});


	})
})