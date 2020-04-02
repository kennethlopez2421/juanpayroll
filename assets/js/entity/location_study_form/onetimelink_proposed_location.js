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

	$("#location_study_form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		var hasRequirement = $('input[type=file]').val(); 
		if (hasRequirement != "") {

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

			//adding qrcode
			var reference_no = getReferenceNo(10);
	        var qrcode_text ='';
	        qrcode_text += reference_no;
	        $('#qrcode').qrcode({
				width: 128,
				height: 128,
				text: qrcode_text
			});

	        var canvas = $('#qrcode canvas');
	        var blob = canvas.get(0).toDataURL("image/png");

	        formData.append('reference_no',reference_no);
		  	formData.append('qr_code',blob);

			if(error == 0){
				var serial = $("#location_study_form").serializeArray();
		        for(var i = 0; i < serial.length; i++){
				    var formDataItem = serial[i];
				    if(formDataItem.value != ""){
				    	formData.append(formDataItem.name, formDataItem.value);
				    }
				}
				
		  		formData.append('location_image', $('input[type=file]')[0].files[0]); 
		  		var link_token = $("#link_token").val();

				$.ajax({
					method: 'post',
					url: base_url+'Main_entity/save_proposed_location',
					data: formData,
					dataType: 'json',
					processData: false,
					contentType:false,
					beforeSend:function(data){
						$('.btnReg').attr('disabled',true);
						$('.btnReg').text('Please wait...');
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
							$("#location_study_form :input").attr("disabled", true);
							$(".bntReg").css('display', 'none');
							setTimeout(function(){
								$.ajax({
									type: 'post',
									url: base_url+'Main_entity/deactivate_pl_onetimelink',
									data: {'link_token':link_token},
									success:function(data){
										if(data.success == 1) {
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
												hideAfter: 5000
											});
											location.reload();
										}else{
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
							}, 5000);
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
							    bgColor: '#f0ad4e',
								textColor: 'white'        
							});
						}
					}
				});
			}else{
				$.toast({
				    heading: 'Warning',
				    text: "Please fill out all required fields",
				    icon: 'warning',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#f0ad4e',
					textColor: 'white'        
				});
			}
		}else{
			$.toast({
			    heading: 'Warning',
			    text: "Please upload photo or sketch of preferred location",
			    icon: 'warning',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#f0ad4e',
				textColor: 'white'        
			});
		}

	});

	 $('#ADDFILE').click(function (event){
        event.preventDefault();
        $('#submit').css('display', 'block');
        addFileInput();

    });

    function addFileInput(){

        var html = '';
        html += '<div class = "alert alert-info">';
        html += '<button type = "button" class = "close" data-dismiss = "alert" aria-hidden = "true">&times;</button>';
        html += '<strong>Upload file</strong>';
        html += '<input type="file" name="images[]" class="req_upload">';
        html += '</div>';

        $(".uploadFileContainer").append(html);
    }

    function hasExtension(inputID, exts) {
        var fileName = inputID.val();
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }

    $('#location_image').change(function(e){
        var filesize = $(this)[0].files[0].size;
        if(!hasExtension($('#location_image'),['.jpg', '.png','.JPG','.PNG']) || filesize > 2000000){
            $.toast({
                heading: 'Warning',
                text: 'Please select valid file to upload. Only PNG and JPG file are allowed',
                icon: 'warning',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: false,
                bgColor: '#f0ad4e;',
                textColor: 'white'  
            });
           $(this).val("");
           $('#preview').css('display',"none");
        }
    });

    function getReferenceNo(length) {
    	return Math.floor(Math.pow(10, length-1) + Math.random() * 9 * Math.pow(10, length-1));
    }

});