 $(function(){
	var base_url = $("body").data("base_url");

    $('.savedBtn').on('click', function(){   // for select box

        var trandate = $(".trandate").val();
        var idno = $(".customerList").val();
        var tickettype = $(".type").val();
        var ticketstatus = $(".status").val();
        var details = $(".txtareadetails").val();
        var checker = 0;

        if(trandate == "" || idno == "" || tickettype == "none" || ticketstatus == "none")
        {
            checker=0;
            $.toast({
                    heading: 'Note:',
                    text: "Please fill all required fields.",
                    icon: 'info',
                    loader: false,   
                    stack: false,
                    position: 'top-center',  
                    bgColor: '#FFA500',
                    textColor: 'white',
                    allowToastClose: false,
                    hideAfter: 3000          
            });
        }
        else
        {
            checker=1;
            
        }

        if(checker == 1)
        {
            $.ajax({
            type: 'post',
            url: base_url+'Main_entity/save_ticket',
            data:{'trandate':trandate,'idno':idno,'tickettype':tickettype,'ticketstatus':ticketstatus,'details':details},
            
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
                        text: data.message,
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#d9534f',
                        textColor: 'white'  
                        });
                    }
                    clearAddform();
                }
            }); 
        }     
    });

//clear all form function, please add/change other input to clear if needed
function clearAddform(){
    $('#customerList').val(null).trigger('change');
    //$(".customerList").val("none");
    //$(".customerList").text("Select Customer");
    $(".trandate").val("");
    $(".type").val("");
    $(".status").val("");
    $(".txtareadetails").val("");
}

});//main
