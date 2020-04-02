$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.btnregCancel').click(function(e){
		e.preventDefault();
		window.location.href = ''+base_url+'/Main/index'+'';
	});

	href="<?=base_url('Main/index');?>"
	var token = $(".page-header").data("token");
	var fis_app_id = $(".page-header").data("id");

	var formData = new FormData();

	$(".confirmBtn").click(function(e){

		e.preventDefault();
		var serial = $("#endorsement_for_ocular").serialize();
		var error = 0;

		$('.required_fields').each(function(){ //loop all input field then validate
			if ($(this).val() == ""){
				$(this).css("border-color", "#d9534f"); //change all empty to color red
			}else{
				$(this).css("border-color", "#eee");  //rollback when not empty
			}
		});

		$('.required_fields').each(function(){ //loop all input field then validate
			if ($(this).val() == ""){ // if empty show error
				error = 1; //update error to 1
				// $(this).css("border-color","#d9534f");
				$(this).focus();
				$.toast({
				    heading: 'Warning',
				    text: 'Please fill out this field',
				    icon: 'warning',
				    loader: false,   
				    stack: false,
				    position: 'top-center',     
				    bgColor: '#f0ad4e;',
					textColor: 'white'
				});

				return false; //focus first empty fields
			}
		});

		if(error == 0){
			$.ajax({
				method: 'post',
				url: base_url+'Main_entity/save_endorsement_for_ocular',
				data: serial,
				beforeSend:function(data){
					$('.confirmBtn').attr('disabled',true);
					$('.confirmBtn').text('Please wait...');
					$('.btnregCancel').attr('disabled',true);
				},
				success:function(data){
					if(data.success == 1) {
						$('.btnReg').attr('disabled',false);
						$('.btnReg').text('Submit');
						$(this).trigger("reset");
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
					    	window.location = base_url+'Main_entity/view_fis_transaction_history_moni_officer/'+fis_app_id+'/'+token;
						}, 2 * 1000);
					}else{
						$('.btnReg').attr('disabled',false);
						$('.btnReg').text('Submit');
						$.toast({
						    heading: 'Warning',
						    text: data.message,
						    icon: 'warning',
						    loader: false,   
						    stack: false,
						    position: 'top-center',  
						    bgColor: '#f0ad4e;',
							textColor: 'white'        
						});
					}
				}
			});
		}
	
	});

	$(".btnAddEndorsement").click(function(e){
		e.preventDefault();
		$(this).hide();
		$(".bntCancel").css('display','block');
		$("#endorsement_for_ocular_div").css("display", "block");

		//scroll to top
		$('html, body').animate({
	        scrollTop: $("#initial_assessment").offset().top
	    }, 1500);
		$("#initial_assessment").focus();
	});	

	$(".bntCancel").click(function(e){
		e.preventDefault();
		$(".btnAddEndorsement").show();
	    $("#endorsement_for_ocular_div").css("display", "none");
	});


	$("#bntReg").click(function(e){
		e.preventDefault();
		var error = 0;
		$('.required_fields').each(function(){ //loop all input field then validate
			if ($(this).val() == ""){
				$(this).css("border-color", "#d9534f"); //change all empty to color red
			}else{
				$(this).css("border-color", "#eee");  //rollback when not empty
			}
		});

		$('.required_fields').each(function(){ //loop all input field then validate
			if ($(this).val() == ""){ // if empty show error
				error = 1; //update error to 1
				// $(this).css("border-color","#d9534f");
				$(this).focus();
				$.toast({
				    heading: 'Warning',
				    text: 'Please fill out this field',
				    icon: 'warning',
				    loader: false,   
				    stack: false,
				    position: 'top-center',     
				    bgColor: '#f0ad4e;',
					textColor: 'white'
				});
				return false; //focus first empty fields
			}
		});

		var warning_message  = "";
		$("input[name='status']").each(function(){
			if(this.checked){
				var status = "";
				if(this.value == 1){
					status= 'Approve';
					warning_message = "You are trying to " + status + ' this Proposed Location. You will not be able to undo the changes once you changed the status to ' + status +'. Click "Confirm" to proceed.'			
				}else if(this.value == 2){
					status='Decline';
					warning_message = "You are trying to " + status + ' this Proposed Location. You will not be able to undo the changes once you changed the status to ' + status +'. Click "Confirm" to proceed.'				
				}else{
					status='Check Distance';
					warning_message = "You are trying to change the status of this Proposed Location to '" + status + "'. Please note that you will no longer be able to revert the status once you click 'Confirm'.";
				}
				return false;
			}
		});

		if(error == 0){
			$("#warning_message").html(warning_message);
			$('#confirmModal').modal('show');
		}
		
	});

	$('#ADDASSESSMENT').click(function (event){
        event.preventDefault();
        addAssessment();
 
    });

    function addAssessment(){

        var html = '';
        html += '<div class="col-md-12">';
        html += '<input class="form-control" rows="4" style="margin-bottom:20px" name ="initial_assessment[]"></input>';
        html += '</div>';

        $(".assessment_div").append(html);
    };

});