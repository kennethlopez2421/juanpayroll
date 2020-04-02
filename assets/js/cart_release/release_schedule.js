$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var token = $('.token').text();

	// get the date today
	var d = new Date();
	var date_today = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	var data = [];

	function reset() {
		data = [];	
		$("#r_date").val("");
		$("#r_franchisee").val('none').change();
		$("#r_location").val("");
		$('#r_concept').val('none').change();
		$('#r_type').val('none').change();
		$('#r_size').val('none').change();
		$('#r_improvements').val('none').change();
		$('#r_mode').val('none').change();
		$("#r_notes").val("");
		$(".save").prop("disabled",false);
		$("r_location").prop('disabled', true);
	}

	$('.save').click(function(e) {
		var date = $("#r_date").val();
		var franchisee = $("#r_franchisee").val();
		var location = $("#r_location").val();
		var concept = $("#r_concept").val();
		var type = $("#r_type").val();
		var size = $("#r_size").val();
		var improvements = $("#r_improvements").val();
		var mode = $("#r_mode").val();
		var date1 = formatDate(date);

		if(date1 == "" || franchisee == "none" || location == "" || concept == "none" || type == "none" || size == "none" || improvements == "none" || mode == "none") {
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
				$('#m_franchisee').html($("#r_franchisee option:selected").text());
				$('#m_date').html($("#r_date").val());
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

	$('#confirmForm').submit(function(event) {
		event.preventDefault();

		var form = $(this);

		var data = {
			'date' : formatDate($("#r_date").val()),
			'franchisee' : $("#r_franchisee").val(),
			'location' : $("#r_location").val(),
			'concept' : $("#r_concept").val(),
			'type' : $("#r_type").val(),
			'size' : $("#r_size").val(),
			'improvements' : $("#r_improvements").val(),
			'mode' : $("#r_mode").val(),
			'notes' : $("#r_notes").val()
		}

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
				 	window.location.replace(base_url+'Main_cart/rst_history/'+token);

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
	  			}
	  		}
  		});
	});

	$("#r_franchisee").change(function () {
	    var franchisee = $('#r_franchisee').val();

	    $.ajax({
	  		url: base_url+"Main_cart/get_branch_location",
            type: 'post',
			data: {'franchisee':franchisee},
	  		success:function(data){
	  			if (data.address == "") {
	  				$.toast({
					    heading: 'Note',
					    text: 'Franchisee have no address. You may manually enter an address.',
					    icon: 'error',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'orange',
						textColor: 'white'  
					});
					$("#r_location").prop('disabled', false);
					$("#r_location").val("");
	  			}
	  			else {
	  				$('#r_location').val(data.address);
	  				$("#r_location").prop('disabled', true);
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
