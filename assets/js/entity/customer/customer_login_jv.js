var base_url = $("body").data('base_url');
$(function(){
	// var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	// $('.datepicker2').datepicker({
	// 	format: 'yyyy/mm/dd',
 //    	startDate: '0',
	// });
	$("#save_changes").click(function(e){
		e.preventDefault();
		var err = validate_required("#customer_login_info",".req",);
		if (err ==false){
			var thiss = $("#customer_info").serialize();
			alert ("hello");
			// if ($("#button_switch").val() == "add"){
			// 	$.ajax({
			//   		type: 'post',
			//   		url: base_url+'Main_entity_jv/add_customer',
			//   		data: thiss,
			//   		success:function(data){
			//   			if (data.success==1){
			//   				$.toast({
			// 				    heading: 'Success',
			// 				    text: data.message,
			// 				    icon: 'success',
			// 				    loader: false,  
			// 				    stack: false,
			// 				    position: 'top-center', 
			// 				    bgColor: '#5cb85c',
			// 					textColor: 'white',
			// 					allowToastClose: false,
			// 					hideAfter: 10000
			// 				});
			//   			}
			//   			else{
			//   				$.toast({
			// 				    heading: 'Warning',
			// 				    text: data.message,
			// 				    icon: 'error',
			// 				    loader: false,  
			// 				    stack: false,
			// 				    position: 'top-center', 
			// 					allowToastClose: false,
			// 					bgColor: '#d9534f',
			// 					textColor: 'white'  
			// 				});
			//   			}
			//   		},
			//   		error: function(error){
			//   			$.toast({
			// 			    heading: 'Warning',
			// 			    text: 'Something went wrong. Please try again.',
			// 			    icon: 'error',
			// 			    loader: false,  
			// 			    stack: false,
			// 			    position: 'top-center', 
			// 				allowToastClose: false,
			// 				bgColor: '#d9534f',
			// 				textColor: 'white'  
			// 			});
			//   		}
			//   	});
			// }
			// else if ($("#button_switch").val() == "update"){
			// 	// alert ("updating...");
			// 	thiss = $("#customer_info").serialize();
			// 	$.ajax({
			//   		type: 'post',
			//   		url: base_url+'Main_entity_jv/update_customer',
			//   		data: thiss,
			//   		success:function(data){
			//   			if (data.success==1){
			//   				$.toast({
			// 				    heading: 'Success',
			// 				    text: data.message,
			// 				    icon: 'success',
			// 				    loader: false,  
			// 				    stack: false,
			// 				    position: 'top-center', 
			// 				    bgColor: '#5cb85c',
			// 					textColor: 'white',
			// 					allowToastClose: false,
			// 					hideAfter: 10000
			// 				});
			//   			}
			//   			else{
			//   				$.toast({
			// 				    heading: 'Warning',
			// 				    text: data.message,
			// 				    icon: 'error',
			// 				    loader: false,  
			// 				    stack: false,
			// 				    position: 'top-center', 
			// 					allowToastClose: false,
			// 					bgColor: '#d9534f',
			// 					textColor: 'white'  
			// 				});
			//   			}
			//   		},
			//   		error: function(error){
			//   			$.toast({
			// 			    heading: 'Warning',
			// 			    text: 'Something went wrong. Please try again.',
			// 			    icon: 'error',
			// 			    loader: false,  
			// 			    stack: false,
			// 			    position: 'top-center', 
			// 				allowToastClose: false,
			// 				bgColor: '#d9534f',
			// 				textColor: 'white'  
			// 			});
			//   		}
			//   	});
			// }
			
			
		}
		// alert (err);
	});
	// $(".add_new").click(function(){
	// 	$("#customer_info").get(0).reset()
 //        $("#addItemModal").modal({backdrop: "static"});
 //        $("#button_switch").val("add");
 //        // alert($("#button_switch").val());
 //    });
	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"columnDefs": [
    		{ targets: 3, orderable: false, "sClass":"text-center" }
		],
		"ajax":{
			url :base_url+"Main_entity_jv/get_customer_login_data", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="7" style = "text-align: center;">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});
	$('.search-input-text').on('keyup', function(){   // for text boxes
		var i =$(this).attr('data-column');  // getting column index
		var v =$(this).val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});
	$('.filterBtn').click(function(){

		if($('.searchAppName').val() != "" || $('.searchDate').val != "" || $('.searchDate2').val != ""){ //all

			var c =$('.searchAppName').attr('data-column');  // getting column index
			var d =$('.searchAppName').val();  // getting search input value

			var e =$('.searchDate').attr('data-column');  
			var f =$('.searchDate').val();  

			var g =$('.searchDate2').attr('data-column');  
			var h =$('.searchDate2').val();  

			dataTable.columns(c).search(d);
			dataTable.columns(e).search(f);
			dataTable.columns(g).search(h).draw();
		}else{
			dataTable.columns(0).search("");
			dataTable.columns(1).search("");
			dataTable.columns(2).search("").draw();
		};
	});
});
function display_info(idno){
	// $("#customer_login_info").get(0).reset();
        $("#addItemModal").modal({backdrop: "false"});
        // $("#button_switch").val("update");
        // alert($("#button_switch").val());
	// $(".card-body").html(idno);
    $(idno).click(function(e){
        e.preventDefault();
    });
    $.ajax({
        type: 'post',
        url: base_url+'Main_entity_jv/get_customer',
        data: {'idno':idno},
        success:function(data){
            // $('.btnLogin').attr('disabled',false);
            if(data.success == 1) {
                // alert("ting!");
                // $(".disabler").prop("readOnly", "true");
                // $("#cust_bday").addClass("datepicker");
                var bdate = new Date(data.customer_info.bday);//birthdate
                var EoC = new Date(data.customer_info.regdate);//end of contract
                
                // alert(data.customer_info.idno);
                $("#idno").val(data.customer_info.idno);
                $("#full_name").val(data.customer_info.lname+", "+data.customer_info.fname+" "+data.customer_info.mname);
                //franchise information
                $("#branch_name").val(data.customer_info.branchname);
            }
        }
    });
	
}

///required fields validator
function validate_required(form_caller, required_fields){//form_caller is the class or id of the  form
		var serial = $(form_caller).serialize(); // collect all user input
		var error = false; //declare error 
		// if ($(termsCheckbox).is(':checked')){ // if checked terms and condition
			$(form_caller).find(required_fields).each(function(){ //loop all input field then validate
				if ($(this).val() == ""){
					$(this).css("border-color", "#d9534f"); //change all empty to color red
				}else{
					$(this).css("border-color", "#eee");  //rollback when not empty
				}
			});

			$(form_caller).find(required_fields).each(function(){ //loop all input field then validate
				if ($(this).val() == ""){ // if empty show error
					error = true; //update error to 1
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

					//focus first empty fields
				}
			});
			// if (error == false) { // if no error 
			// 	if (!validate_email($(register_email).val())) { //validate email
			// 		error = true; //update error to 1
			// 		$(this).focus();
			// 		$.toast({
			// 		    heading: 'Warning',
			// 		    text: 'Please fill out email properly',
			// 		    icon: 'warning',
			// 		    loader: false,   
			// 		    stack: false,
			// 		    position: 'top-center',     
			// 		    bgColor: '#f0ad4e;',
			// 			textColor: 'white'
			// 		});
			// 	}
			// }
			return error;
			// 

		// 	if (error == 0) { // if no error then execute
		// 		$.ajax({
		// 			type: 'post',
		// 			url: base_url+'Main/register_player',
		// 			data: serial,
		// 			beforeSend:function(data){
		// 				$('.btnReg').attr('disabled',true);
		// 				$('.btnReg').text('Please wait...');
		// 				$('.btnregCancel').attr('disabled',true);
		// 			},
		// 			success:function(data){
		// 				$('.btnReg').text('Register');
		// 				$('.btnReg').attr('disabled',false);
		// 				$('.btnregCancel').attr('disabled',false);
		// 				if(data.success == 1) {
		// 					$('.btnReg').attr('disabled',true);
		// 					$('.btnregCancel').attr('disabled',true);
		// 					$.toast({
		// 					    heading: 'Success',
		// 					    text: 'Successfully Registered! Please check your email for verification.',
		// 					    icon: 'success',
		// 					    loader: false,  
		// 					    stack: false,
		// 					    position: 'top-center', 
		// 					    bgColor: '#5cb85c',
		// 						textColor: 'white',
		// 						allowToastClose: false,
		// 						hideAfter: 10000
		// 					});
		// 					setTimeout(function() {
		// 						window.location.href = ''+base_url+'/Main/index'+'';
		// 					}, 5000);

		// 				}else{
		// 					$.toast({
		// 					    heading: 'Error',
		// 					    text: data.message,
		// 					    icon: 'error',
		// 					    loader: false,   
		// 					    stack: false,
		// 					    position: 'top-center',  
		// 					    bgColor: '#d9534f',
		// 						textColor: 'white'        
		// 					});
		// 				}
		// 			}
		// 		});
		// 	}
		// }else{ // show error if unclick
		// 	$.toast({
		// 	    heading: 'Warning',
		// 	    text: 'Please check Terms and Conditions',
		// 	    icon: 'warning',
		// 	    loader: false,   
		// 	    stack: false,
		// 	    position: 'top-center',     
		// 	    bgColor: '#f0ad4e;',
		// 		textColor: 'white'
		// 	});
		// }
		

}
function validate_email(email) { 
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
// $(function(){
// 	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
// 	$('.btnregCancel').click(function(e){
// 		e.preventDefault();
// 		window.location.href = ''+base_url+'/Main/index';
// 	});

// 	// $("select").select2();
// 	$('.datepicker').datepicker({});

// 	$("#register-form").submit(function(e){ // target form element
// 		e.preventDefault();
// 		var serial = $(this).serialize(); // collect all user input
// 		var error = 0; //declare error 
// 		if ($('.termsCheckbox').is(':checked')){ // if checked terms and condition
// 			$(this).find('.required_fields').each(function(){ //loop all input field then validate
// 				if ($(this).val() == ""){
// 					$(this).css("border-color", "#d9534f"); //change all empty to color red
// 				}else{
// 					$(this).css("border-color", "#eee");  //rollback when not empty
// 				}
// 			});

// 			$(this).find('.required_fields').each(function(){ //loop all input field then validate
// 				if ($(this).val() == ""){ // if empty show error
// 					error = 1; //update error to 1
// 					// $(this).css("border-color","#d9534f");
// 					$(this).focus();
// 					$.toast({
// 					    heading: 'Warning',
// 					    text: 'Please fill out this field',
// 					    icon: 'warning',
// 					    loader: false,   
// 					    stack: false,
// 					    position: 'top-center',     
// 					    bgColor: '#f0ad4e;',
// 						textColor: 'white'
// 					});

// 					return false; //focus first empty fields
// 				}
// 			});



// 			if (error == 0) { // if no error 
// 				if (!validate_email($("#register-email").val())) { //validate email
// 					error = 1; //update error to 1
// 					$(this).focus();
// 					$.toast({
// 					    heading: 'Warning',
// 					    text: 'Please fill out email properly',
// 					    icon: 'warning',
// 					    loader: false,   
// 					    stack: false,
// 					    position: 'top-center',     
// 					    bgColor: '#f0ad4e;',
// 						textColor: 'white'
// 					});
// 				}
// 			}


// 			if (error == 0) { // if no error then execute
// 				$.ajax({
// 					type: 'post',
// 					url: base_url+'Main/register_player',
// 					data: serial,
// 					beforeSend:function(data){
// 						$('.btnReg').attr('disabled',true);
// 						$('.btnReg').text('Please wait...');
// 						$('.btnregCancel').attr('disabled',true);
// 					},
// 					success:function(data){
// 						$('.btnReg').text('Register');
// 						$('.btnReg').attr('disabled',false);
// 						$('.btnregCancel').attr('disabled',false);
// 						if(data.success == 1) {
// 							$('.btnReg').attr('disabled',true);
// 							$('.btnregCancel').attr('disabled',true);
// 							$.toast({
// 							    heading: 'Success',
// 							    text: 'Successfully Registered! Please check your email for verification.',
// 							    icon: 'success',
// 							    loader: false,  
// 							    stack: false,
// 							    position: 'top-center', 
// 							    bgColor: '#5cb85c',
// 								textColor: 'white',
// 								allowToastClose: false,
// 								hideAfter: 10000
// 							});
// 							setTimeout(function() {
// 								window.location.href = ''+base_url+'/Main/index'+'';
// 							}, 5000);

// 						}else{
// 							$.toast({
// 							    heading: 'Error',
// 							    text: data.message,
// 							    icon: 'error',
// 							    loader: false,   
// 							    stack: false,
// 							    position: 'top-center',  
// 							    bgColor: '#d9534f',
// 								textColor: 'white'        
// 							});
// 						}
// 					}
// 				});
// 			}
// 		}else{ // show error if unclick
// 			$.toast({
// 			    heading: 'Warning',
// 			    text: 'Please check Terms and Conditions',
// 			    icon: 'warning',
// 			    loader: false,   
// 			    stack: false,
// 			    position: 'top-center',     
// 			    bgColor: '#f0ad4e;',
// 				textColor: 'white'
// 			});
// 		}
		
// 	});


// 	function validate_email(email) { 
// 	    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
// 	    return re.test(String(email).toLowerCase());
// 	}
// });