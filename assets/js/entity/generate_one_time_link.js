$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$(".btnGenerate").click(function(e){
		e.preventDefault();
		$.ajax({
			method: 'post',
			url: base_url+'Main_entity/generate_onetimelink',
			data:{"sample":"sample"},
			beforeSend:function(data)
				{
					$.LoadingOverlay("show"); 
				},
				complete: function()
				{
					$.LoadingOverlay("hide"); 
				},
			success:function(data){
				if(data.success == 1) {
					var link = data.one_time_link;
					$('.btnGenerate').attr('disabled',false);
					$('.btnGenerate').text('Generate');
					$('.btnGenerate').css('display','none');
					$('.btnClear').css('display','block');
					$("#one_time_link").val(link);
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
				}
			}
		});
	});	

	$('.btnClear').click(function(e){
		e.preventDefault();
		$('.btnGenerate').css('display','block');
		$('.btnClear').css('display','none');
		$('#one_time_link').val('');
	})
	

});