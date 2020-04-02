$(document).ready(function(){

	base_url = $("body").data('base_url');

	function tofixed(x){
		return numberWithCommas(parseFloat(x).toFixed(2));
	}
	function numberWithCommas(x){
	  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	$('#coh_date').datepicker('setDate', Date());

	resetData = function(){

		$('#coh_date').datepicker('setDate', Date());

		$('#coh_amount').val('');
		$('#coh_enchkno').val('');
	}

	$('#add_coh_form').submit(function(event){
		event.preventDefault();

		var valid = true;
		var coh_date = $('#coh_date').val();
		var coh_amount = $('#coh_amount').val();
		var coh_enchkno = $('#coh_enchkno').val();

		if(coh_date==""){
			valid = false;
		}
		if(coh_amount==""){
			valid = false;
		}
		if(coh_enchkno==""){
			valid = false;
		}

		if(valid==true){
			var entry = {
				date: $('#coh_date').val() ,
				description: $('#t_description').val() ,
				amount: $('#t_amount').val() ,
				deb_account: $("#t_debit_account option:selected").text() ,
				cre_account: $("#t_credit_account option:selected").text()
			}

			$.ajax({
			  	type: 'post',
			  	url: base_url+'Main_account/save_coh_record',
			  	data:{'coh_date':coh_date, 'coh_amount':coh_amount, 'coh_enchkno':coh_enchkno},
			  	beforeSend:function(data){
				$.LoadingOverlay("show"); 
				},
				complete: function(data)
				{
					$.LoadingOverlay("hide"); 
				},
			  	success:function(data){
			  			
			  		data = JSON.parse(data);

			  		// console.log(data);

			  		if(data.valid==false){
			  			$.toast({
						    heading: 'Note',
						    text: data.message,
						    icon: 'info',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#FFA500',
							textColor: 'white',
							hideAfter: 2000   
						});
			  		}
			  		else{
			  			$.toast({
						    heading: 'Success',
						    text: data.message,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#5cb85c',
							textColor: 'white',
							hideAfter: 2000   
						});

						resetData();
			  		}
			  		$(".btnCashhand").prop("disabled",false);
			  	},
			  	error: function(error){
			  		data = JSON.parse(error);
			  	}
			});
		}
		else
		{
			$.toast({
			    heading: 'Note',
			    text: 'Please fill out all required fields',
			    icon: 'info',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#FFA500',
				textColor: 'white',
				hideAfter: 3000   
			});
		}
	});
});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}