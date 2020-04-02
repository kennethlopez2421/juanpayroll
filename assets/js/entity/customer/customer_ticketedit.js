 $(function(){
	var base_url = $("body").data("base_url");

    var flag = false;
    var errorFound = false;

    var trandate;
    var idno;
    var status;
    var txtareadetailsresolution;
    var ticketid; 
    var token;

    $('#updateTicket').submit(function(e){
        e.preventDefault();
        checkInputs('#updateTicket');

        if(flag){

            getValues();


            $.ajax({
                type: 'post',
                url: base_url+'Main_entity/update_ticket',
                data:{'trandate':trandate,'idno':idno,'status':status,'txtareadetailsresolution':txtareadetailsresolution,'ticketid':ticketid},
            
                beforeSend:function(data)
                {
                    $("body").LoadingOverlay("show"); 
                },
                complete: function()
                {
                    $("body").LoadingOverlay("hide"); 
                },
                success:function(data){
                    if(data.success == 1){
                        $.toast({
                        heading: 'NOTE',
                        text: data.message,
                        icon: 'success',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#5cb85c',
                        textColor: 'white'  
                        });    
                    }else{
                        $.toast({
                        heading: 'Note',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                        });
                    }

                    setTimeout(function(){
                        window.location.href=base_url+"Main_entity/entity_ticketlist/" + token;
                    },2000);
                }

            });
        }
             
    });    

    function checkInputs(formname){
        $(formname).find('.required_fields').each(function(){ //loop all input field then validate
            if ($(this).val() == ""){
                $(this).css("border-color", "#d9534f"); //change all empty to color red
            }else{
                $(this).css("border-color", "#eee");  //rollback when not empty
                errorFound = false;
            }
        });

        $(formname).find('.required_fields').each(function(){ //loop all input field then validate
            if ($(this).val() == ""){ // if empty show error
                flag = false; //update error to 1
                    // $(this).css("border-color","#d9534f");
                $(this).css("border-color", "#d9534f"); //change all empty to color red
                $(this).focus();
                
                $.toast({
                        heading: 'Note',
                        text: 'Please fill out this field',
                        icon: 'warning',
                        loader: false,   
                        stack: false,
                        position: 'top-center',     
                        bgColor: '#FFA500;',
                        textColor: 'white'
                });
                errorFound = true;
                return false; //focus first empty fields

            }else{
                errorFound = false;
                flag = true;
            }

        }); 
    }

    function getValues(){
        trandate =  $(".trandate").val();
        idno = $(".idno").val();
        status = $(".status").val();
        txtareadetailsresolution = $(".txtareadetailsresolution").val();
        ticketid = $(".ticketid").val(); 
         token  =  $("#token").val();      
    } 


});//main
