$(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var empty_signature = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/2wBDAQMDAwQDBAgEBAgQCwkLEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBD/wAARCABiASoDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAn/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFAEBAAAAAAAAAAAAAAAAAAAAAP/EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/AKpgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//Z";
	var sig = $('#app_signature').signature();
	var sig2 = $('#acknowledged_by_signature').signature();
	var sig3 = $('#approver_signature').signature();



	$('.btnregCancel').click(function(e){
		e.preventDefault();
		window.location.href = ''+base_url+'/Main/index'+'';
	});

	href="<?=base_url('Main/index');?>";
	
	$('.btnReg').click(function(e){
		var app_signature = sig.signature('toDataURL', 'image/jpeg');
		var acknowledged_by_signature = sig2.signature('toDataURL', 'image/jpeg');
		$("input[name='app_signature'").val(app_signature);
		$("input[name='acknowledged_by_signature'").val(acknowledged_by_signature);
	});

	$("#register_franchise_form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();
		var thiss = $(this);
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

		var reference_no = getReferenceNo(10);
        var qrcode_text ='';
        qrcode_text += reference_no;
        $('#qrcode').qrcode({
			width: 128,
			height: 128,
			text: 'IS_'+qrcode_text
		});

        var canvas = $('#qrcode canvas');
        var blob = canvas.get(0).toDataURL("image/png");
        formData.append('reference_no','IS_'+reference_no);
        formData.append('qrcode',blob);
        
		var fileInputs = $('.req_upload');
	    $.each(fileInputs, function(i,fileInput){
            if( fileInput.files.length > 0 ){
                $.each(fileInput.files, function(k,file){
                    formData.append('images[]', file);
                });
            }
    	});

		var serial = $("#register_franchise_form").serializeArray();
        for(var i = 0; i < serial.length; i++){
		    var formDataItem = serial[i];
		    if(formDataItem.value != ""){
		    	formData.append(formDataItem.name, formDataItem.value);
		    }
		}

		if(error == 0){
			$.ajax({
				type: 'post',
				url: base_url+'Main_entity/register_franchise',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType:false,
				beforeSend:function(data)
				{
					$.LoadingOverlay("show"); 
				},
				complete: function()
				{
					$.LoadingOverlay("hide"); 
				},
				success:function(data){
					$('.btnReg').text('Submit');
					$('.btnReg').prop('disabled',false);
					$('.btnregCancel').attr('disabled',false);
					if(data.success == 1) {
						$(window).scrollTop(0);
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
							hideAfter: 5000
						});

						$(thiss).find('input, select').val('');
						$('.checkbox-template').prop('checked',false);
						$('#preview').removeAttr("src");
						$('#preview').css("display","none");
						sig3.signature('clear');
						$(".signature_container3").prop('hidden', true);
						$(".signature_pad3").prop('hidden', false);					

						sig2.signature('clear');
						$(".signature_container2").prop('hidden', true);
						$(".signature_pad2").prop('hidden', false);					

						sig.signature('clear');
						$(".signature_container").prop('hidden', true);
						$(".signature_pad").prop('hidden', false);
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
		}
	});

	$('#clear').click(function(e) {
		e.preventDefault();
		sig.signature('clear');
		$(".signature_container").prop('hidden', true);
	});

	$('#clear2').click(function(e) {
		e.preventDefault();
		sig2.signature('clear');
		$(".signature_container2").prop('hidden', true);
		$(".signature_pad2").prop('hidden', false);
	});			

	$('#clear3').click(function(e) {
		e.preventDefault();
		sig3.signature('clear');

		$(".signature_container3").prop('hidden', true);
		$(".signature_pad3").prop('hidden', false);
	});		

	// $('#done').click(function(e) {
	// 	e.preventDefault();
	// 	var app_signature = sig.signature('toDataURL', 'image/jpeg');

	// 	if(app_signature == empty_signature){
	// 		$.toast({
	// 		    heading: 'Error',
	// 		    text: "Please affix your signature",
	// 		    icon: 'error',
	// 		    loader: false,   
	// 		    stack: false,
	// 		    position: 'top-center',  
	// 		    bgColor: '#d9534f',
	// 			textColor: 'white'        
	// 		});
	// 	}else{
	// 		$("input[name='app_signature'").val(app_signature);
	// 		$("#sample_sig").attr('src',sig.signature('toDataURL', 'image/jpeg'));
	// 		$(".signature_container").prop('hidden', false);
	// 		$(".signature_pad").prop('hidden', true);
	// 	}
	// });		

	// $('#done2').click(function(e) {
	// 	e.preventDefault();
	// 	var acknowledged_by_signature = sig2.signature('toDataURL', 'image/jpeg');
	// 	if(acknowledged_by_signature == empty_signature){
	// 		$.toast({
	// 		    heading: 'Error',
	// 		    text: "Please affix your signature",
	// 		    icon: 'error',
	// 		    loader: false,   
	// 		    stack: false,
	// 		    position: 'top-center',  
	// 		    bgColor: '#d9534f',
	// 			textColor: 'white'        
	// 		});
	// 	}else{
	// 		$("input[name='acknowledged_by_signature'").val(acknowledged_by_signature);
	// 		$("#sample_sig2").attr('src',sig2.signature('toDataURL', 'image/jpeg'));
	// 		$(".signature_container2").prop('hidden', false);
	// 		$(".signature_pad2").prop('hidden', true);
	// 	}
	// });		

	// Removed 3rd signature will be relocated
	// $('#done3').click(function(e) {
	// 	e.preventDefault();
	// 	var approved_by_signature = sig3.signature('toDataURL', 'image/jpeg');
	// 	if(approved_by_signature == empty_signature){
	// 		$.toast({
	// 		    heading: 'Error',
	// 		    text: "Please affix your signature",
	// 		    icon: 'error',
	// 		    loader: false,   
	// 		    stack: false,
	// 		    position: 'top-center',  
	// 		    bgColor: '#d9534f',
	// 			textColor: 'white'        
	// 		});
	// 	}else{
	// 		$("input[name='approved_by_signature'").val(approved_by_signature);
	// 		$("#sample_sig3").attr('src',sig3.signature('toDataURL', 'image/jpeg'));
	// 		$(".signature_container3").prop('hidden', false);
	// 		$(".signature_pad3").prop('hidden', true);
	// 	}
	// });

	$('#datepicker').datepicker({
		todayBtn: "linked",
		endDate: '+0d'
	});

	$('#datepicker2').datepicker({
		todayBtn: "linked"
	});	

	$('#datepicker3').datepicker({
		todayBtn: "linked"
	});

	if($('#inlineCheckbox6').is(':checked')){
		$(this).val($('#other_value').val());	
	}

	$("#others").click(function(){
		if($(this).is(":checked")){
			$("#other_value").prop("disabled", false);
		}else{
			$("#other_value").prop("disabled", true);
			$("#other_value").val("");
		}
	});
		

	// function loadFile(event) {
	//     var preview = document.getElementById('preview');
	//     preview.src = URL.createObjectURL(event.target.files[0]);
	   	
	// 	var reader = new FileReader();
	// 	reader.readAsDataURL(event.target.files[0]); 
	// 	reader.onloadend = function() {
	// 	    var base64data = reader.result; 
	// 	    $('.app_image').val(base64data);            
	//   	}
	//   }



	$("input[name='upload_type']").click(function(){

		if($("input[name='upload_type']:checked").val() == 1){ //for webcam
			$('#my_camera').css('display',"block");
			$('#preview').css('display',"none");
			$('#File').val('');
			Webcam.attach('#my_camera');
			$('.snapshot_btn').prop('disabled', false);
			$('.upload_btn').prop('disabled', true);
			// console.log($(".app_image").val());

		}else if($("input[name='upload_type']:checked").val() == 2){ //for file upload
			backtozero();
			$('#preview').css('display',"block");
			$('.upload_btn').prop('disabled', false);
			// console.log($(".app_image").val());
		}
	});

	function take_snapshot() {
		Webcam.snap( function(data_uri) {
			$('#my_camera').css('display',"none");
			$('#preview').css('display',"block");
			$("#preview").prop("src",data_uri);
			$(".app_image").val(data_uri);
		});
	
	}

	function backtozero(){
		Webcam.reset();
		$('#File').val('');
		$('.retake_btn').css('display', "none");
		$('#my_camera').css('display',"none");
		$('.snapshot_btn').prop('disabled', true);
		$('.snapshot_btn').css('display', "block");
		$(".app_image").val('');
		if($("#preview").prop('src') == ""){

		}else{
			$("#preview").prop('src', base_url+"assets/img/white_picture.jpg");
			// $('#preview').removeAttr('src');
		}
	};

	$('.snapshot_btn').click(function(e){
		e.preventDefault();
		take_snapshot();
		$(this).css("display","none");
		$('.retake_btn').css("display","block");

	});	

	$('.retake_btn').click(function(e){
		e.preventDefault();
		
		$(this).css("display","none");
		$('.snapshot_btn').css("display","block");

		$('#my_camera').css('display',"block");
		$('#preview').css('display',"none");
		$('#File').val('');
		Webcam.attach('#my_camera');

	});

	function getReferenceNo(length) {
    	return Math.floor(Math.pow(10, length-1) + Math.random() * 9 * Math.pow(10, length-1));
    }

	// 
    function getAge(dateString){
        var today = new Date();
        var birthDate = new Date(dateString);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
        }
        return age;
    }

    $('.app_dob').on('changeDate', function(){
        var date = $(this).val();
        var age = getAge(date);
        $('.app_age').val(age);
    });


    $('.number').keypress(function(e){
        if(!((e.keyCode > 95 && e.keyCode < 106)
        || (e.keyCode > 47 && e.keyCode < 58) 
        || e.keyCode == 8)) {
        return false;
        }
    });

    //ID Images
    $('#ADDFILE').click(function (event){
        event.preventDefault();
        addFileInput();
    });

    var counter = 0;
    function addFileInput(){
    	var html='';
    	html += '<div class="col-md-12" id="id_back_photo">';
        html += '<input type="file" name="images[]" class="req_upload btn btn-primary btn-sm"> ';
        html += '<button class="btn btn-danger btn-sm" id="remove"><span class="fa fa-minus-circle" style="font-size:20px;"></span></button>';
        html += '</div>';

    	if(counter == 0){
	        $(".uploadFileContainer").append(html);
	        counter++;
    	}else{
    		$.toast({
                heading: 'Warning',
                text: 'You can only upload up to 2 photos',
                icon: 'warning',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: false,
                bgColor: '#f0ad4e;',
                textColor: 'white'  
            });
    	}
    }

    $(".uploadFileContainer").delegate("#remove", "click", function(e){
    	e.preventDefault();
    	$(this).parent().css('display', 'none');
    	counter = 0;
    });

    function hasExtension(inputID, exts) {
        var fileName = inputID.val();
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }

	//Validated the photos to be uploaded
	$('.uploadFileContainer').delegate('.req_upload', 'change', function(e){
		var filesize = $(this)[0].files[0].size;
		if(!hasExtension($('.req_upload'),['.jpg', '.png']) || filesize > 2000000){
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
		   	$(this).val("");
		}
	});	

	$('.upload_btn').change(function(e){
		var filesize = $(this)[0].files[0].size;
		if(!hasExtension($('.upload_btn'),['.jpg','.png']) || filesize > 2000000){
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
		   	$(this).val("");
		   	$('#preview').css('display',"none");
		}
	});
});

	var loadFile = function(event){
	    $('#preview').css('display',"block");
	    var reader = new FileReader();
	    reader.onload = function(){
	    var preview = document.getElementById('preview');
	    preview.src = reader.result;
	    $('.app_image').val(reader.result);
	    };
	    reader.readAsDataURL(event.target.files[0]);
	};