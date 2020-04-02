$(document).ready(function(){

	var base_url = $("body").data('base_url');

	$('#markascleared').click(function(){
		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/clear_check',
	  		data:{'chkno': $('#lbl_check_number').html() },
	  		beforeSend:function(data) {
				$("body").LoadingOverlay("show"); 
			},
			complete: function() {
				$("body").LoadingOverlay("hide"); 
			},
	  		success:function(data){

	  			data = JSON.parse(data);

	  			if(data.valid == true){
		  				$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#5cb85c',
						textColor: 'white'  
					});

		  		//setTimeout(function(){ window.close() }, 1000);

		  		setTimeout(function(){ 
		  			var currUrl = window.location.href;

		  			currUrl = currUrl.replace("check_transaction_history_view_tag", "check_transaction_history");
	  				//window.open(currUrl+"/"+id,'_blank');

	  				currUrl = currUrl.split('/');

	  				currUrl = currUrl[0]+'/'+currUrl[1]+'/'+currUrl[2]+'/'+currUrl[3]+'/'+currUrl[4]+'/'+currUrl[5]+'/'+currUrl[6];

	  				console.log(currUrl);
		  			window.location = currUrl;

		  		}, 1000);
		  		
	  			}
	  			else{
	  				$.toast({
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#FFA500',
						textColor: 'white'  
					});
	  			}

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Note',
				    text: 'Something went wrong. Please try again.',
				    icon: 'info',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
	  		}
	  	});
	});
})