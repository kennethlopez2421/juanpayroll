$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	href="<?=base_url('Main/index');?>";

	$("#preview_franchise_form :input").prop("disabled", true);
	$(".btnViewApp").prop('disabled',false);
	$(".btnEditApp").prop('disabled',false);

	
	$(".btnViewApp").click(function(e){
		e.preventDefault();
		var fis_app_id = $(this).data('value');
        // console.log(fis_app_id);

        window.location.href = base_url+"Main_entity/pdf_fis_form/"+fis_app_id;

	});

	$(".btnEditApp").click(function(e){
		e.preventDefault();
		$('.text-danger').show();
		$(".btnSaveApp").prop('hidden',false);
		$(".btnCancel").prop('hidden',false);

		$(this).prop('hidden', true);
		$("#datepicker").datepicker({  format: 'mm/dd/yyyy' });
		$("#datepicker2").datepicker({  format: 'mm/dd/yyyy' });
		$("#datepicker3").datepicker({  format: 'mm/dd/yyyy' });


		$("#preview_franchise_form :input").prop("disabled",false);
		$('.not-editable').prop('disabled',true);
		// $("#other_value").prop("disabled", true);
		$.toast({
		    heading: 'Information',
		    text: 'The fields are editable',
		    icon: 'info',
		    loader: false,  
		    stack: false,
		    position: 'top-center', 
		    bgColor: 'rgb(43, 144, 217)',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 4000
		});
	});

	$(".btnCancel").click(function(e){

		$(".btnSaveApp").prop('hidden',true);
		$(".btnCancel").prop('hidden',true);
		$(".btnEditApp").prop('hidden',false);
		$("#preview_franchise_form :input").prop("disabled",true);
		$(".btnEditApp").prop('disabled',false);
		$(".btnViewApp").prop('disabled',false);
	});

	$(".btnSaveApp").click(function(e){
		e.preventDefault();
		var error = 0;
		var formData = new FormData();

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

		// var fileInputs = $('.req_upload');
	 //    $.each(fileInputs, function(i,fileInput){
  //           if( fileInput.files.length > 0 ){
  //               $.each(fileInput.files, function(k,file){
  //               	if(file != ""){
  //               		alert();
  //                  		formData.append('images[]', file);
  //               	}
  //               });
  //           }
  //   	});

    	formData.append('front_image', $('#upload_id0')[0].files[0]); 
    	formData.append('back_image', $('#upload_id1')[0].files[0]); 

		var serial = $("#preview_franchise_form").serializeArray();
        for(var i = 0; i < serial.length; i++){
		    var formDataItem = serial[i];
		    if(formDataItem.value != ""){
		    	formData.append(formDataItem.name, formDataItem.value);
		    }
		}


		if(error == 0){
			$.ajax({
				type: 'post',
				url: base_url+'Main_entity/edit_fis',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType:false,
				beforeSend:function(data){
					$('.btnSaveApp').attr('disabled',true);
					$('.btnSaveApp').text('Please wait...');
					$('.btnCancel').attr('disabled',true);
				},
				success:function(data){
					$('.btnSaveApp').text('Save Changes');
					$('.btnSaveApp').attr('disabled',false);
					$('.btnCancel').attr('disabled',false);
					if(data.success == 1) {
						$(window).scrollTop(0);
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
							hideAfter: 4000
						});
						$("#preview_franchise_form :input").prop("disabled", true);
						$(".btnViewApp").prop('disabled',false);
						$(".btnEditApp").prop('disabled',false);
						$(".btnSaveApp").prop('hidden',true);
						$(".btnCancel").prop('hidden',true);
						$(".btnEditApp").prop('hidden',false);
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
		}
	});

	$("#others").click(function(){
		if($(this).is(":checked")){
			$("#other_value").prop("disabled", false);
		}else{
			$("#other_value").prop("disabled", true);
			$("#other_value").val("");
		}
	});

	$(".preview_div").delegate("#uploaded_id_0",'click',function(e){
		e.preventDefault();
		 $('#upload_id0').click();
	});

	$(".preview_div").delegate("#uploaded_id_1",'click',function(e){
		e.preventDefault();
		 $('#upload_id1').click();
	});

});

	//Preview image

	var loadFile0 = function(event){
		if(hasExtension(event.target,['.jpg', '.png'])){
			var reader = new FileReader();
		    reader.onload = function(e){
			    var preview = document.getElementById('uploaded_id_0');
			    preview.src = reader.result;
			}
		    reader.readAsDataURL(event.target.files[0]);
		}else{
			 $.toast({
		        heading: 'Warning',
		        text: 'Please select valid file to upload. Only PNG and JPG file are allowed.',
		        icon: 'warning',
		        loader: false,  
		        stack: false,
		        position: 'top-center', 
		        allowToastClose: false,
		        bgColor: '#f0ad4e;',
		        textColor: 'white'  
		    });
			event.target.value ="";
		}
	};

	var loadFile1 = function(event){

	    if(hasExtension(event.target,['.jpg', '.png','.JPG','.PNG'])){
			var reader = new FileReader();
		    reader.onload = function(e){
			    var preview = document.getElementById('uploaded_id_1');
			    preview.src = reader.result;
			}
		    reader.readAsDataURL(event.target.files[0]);
		}else{
			 $.toast({
		        heading: 'Warning',
		        text: 'Please select valid file to upload. Only PNG and JPG file are allowed.',
		        icon: 'warning',
		        loader: false,  
		        stack: false,
		        position: 'top-center', 
		        allowToastClose: false,
		        bgColor: '#f0ad4e;',
		        textColor: 'white'  
		    });
			event.target.value ="";
		}
	};

	function hasExtension(inputID, exts) {
        var fileName = inputID.value;
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }
