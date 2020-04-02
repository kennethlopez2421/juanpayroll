$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.btnregCancel').click(function(e){
		e.preventDefault();
		window.location.href = ''+base_url+'/Main/index'+'';
	});

	var token = $(".page-header").data("token");
	var fis_app_id = $(".page-header").data("id");
	href="<?=base_url('Main/index');?>"


	$("#endorsement_form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();
	
			$.ajax({
				type: 'post',
				url: base_url+'Main_entity/save_endorsement_form',
				data: serial,
				beforeSend:function(data){
					$('.btnReg').attr('disabled',true);
					$('.btnReg').text('Please wait...');
					$('.btnregCancel').attr('disabled',true);
				},
				success:function(data){
					$('.btnReg').text('Submit');
					$('.btnReg').attr('disabled',false);
					$('.btnregCancel').attr('disabled',false);
					if(data.success == 1) {
						$('.btnReg').attr('disabled',true);
						$('.btnregCancel').attr('disabled',true);
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
					      	window.location = base_url+'Main_entity/view_fis_transaction_history/'+fis_app_id+'/'+token;
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
});