$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var token = $('.token').text();

	// get the date today
	var d = new Date();
	var date_today = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	var data = [];

	function reset(){
		data = [];	
		$("#p_date").val("");
		$("#p_franchisee").val("");
		$("#p_franchiseeid").val("");
		$("#p_location").val("");
		$('#p_concept').val("");
		$('#p_mode').val("");
		$('#p_package').val('none').change();
		$('#p_category').val('none').change();
		$("#p_notes").val("");
		$(".save").prop("disabled",false);
	}

	$('.save').click(function(e){
		var date = $("#p_date").val();
		var rdno = $("#p_rdno").val();
		var package = $("#p_package").val();
		var category = $("#p_category").val();
		var date1 = formatDate(date);

		if(date1 == "" || rdno == "" || package == "none" || category == "") {
			$.toast({
			    heading: 'Note:',
			    text: "Please fill out all required fields.",
			    icon: 'error',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#FFA500',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 3000          
			});
		}
		else {
			if (date1 >= formatDate(date_today)) {
				$('#m_franchisee').html($("#p_franchisee").val());
				$('#m_date').html($("#p_date").val());
				$('#confirmModal').modal();
			}
			else {
				$.toast({
				    heading: 'Note:',
				    text: "Date cannot be earlier than today.",
				    icon: 'error',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 3000          
				});
			}
		}
	});

	$('#confirmForm').submit(function(event){
		event.preventDefault();

		var form = $(this);

		var data = {
			'date' : formatDate($("#p_date").val()),
			'rdno' : $("#p_rdno").val(),
			'concept' : $("#p_concept").val(),
			'franchiseeid' : $("#p_franchiseeid").val(),
			'category' : $("#p_category").val(),
			'package' : $("#p_package").val(),
			'notes' : $("#p_notes").val()
		}

		console.log(data);

		$.ajax({
	  		url: form.attr('action'),
            type: form.attr('method'),
			data: {'data':data},
	  		beforeSend:function(data){
				$.LoadingOverlay("show");
				$(".save").prop("disabled",true);
			},
	  		success:function(data){
	  			if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: 'You have successfully saved the record.',
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});
				 	setTimeout(function(){
						$.LoadingOverlay("hide");
						$('#confirmModal').modal('hide');
					},500);

				 	window.location.replace(base_url+'Main_cart/prt_history/'+token);
	  			}
	  		}
  		});
	});

	$('#p_rdno').on('input', function() {
		var rdno = $('#p_rdno').val();

	    $.ajax({
	  		url: base_url+"Main_cart/get_release_details",
            type: 'post',
			data: {'rdno':rdno},
	  		success:function(data){
	  			if (data == "") {
	  				$.toast({
					    heading: 'Error',
					    text: 'RD No. does not exists.',
					    icon: 'warning',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'orange',
						textColor: 'white'  
					});

					reset();
	  			}
	  			else if (data == "unavailable") {
	  				$.toast({
					    heading: 'Error',
					    text: 'Cannot encode Package Release for franchisee.',
					    icon: 'warning',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'orange',
						textColor: 'white'  
					});

					reset();
	  			}
	  			else {
	  				$('#p_franchisee').val(data.name);
	  				$("#p_franchiseeid").val(data.franchisee_id);
	  				$('#p_location').val(data.location);
		  			$('#p_concept').val(data.concept);
		  			$('#p_mode').val(data.mor);
	  			}
	  			
	  		}
  		});
	});
});

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function isNumberKeyOnly(evt) {    
  	var charCode = (evt.which) ? evt.which : evt.keyCode;
  	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
     	return false;
  	return true;
}
