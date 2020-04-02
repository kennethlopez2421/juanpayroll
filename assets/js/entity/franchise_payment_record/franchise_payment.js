$(function(){

    //Variables Declaration//////////////////////////////////////////

    var base_url = $("body").data("base_url");

    //variables for adding and updating record
    var date;
    var name;
    var paymenttype;
    var paymentfor;
    var paymentamount;
    var refno;
    var notes;

    var flag = true;
    var errorFound = false;

    //LISTENERS///////////////////////////////////////////////////

    $('#frmPaymentRecord').submit(function(e){   // for select box 
        e.preventDefault();   
        checkInputs('#frmPaymentRecord');
        getInputs('add');
        
        if(flag){
            saveFranchisePayment();    
        }

    });  

    //FUNCTIONS///////////////////////////////////////////////////
    function saveFranchisePayment(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/addnew_Franchisepayment',
            data: {'date':date,'name':name,'paymenttype':paymenttype,'paymentfor':paymentfor,'paymentamount':paymentamount,'refno':refno,'notes':notes},
                    
            beforeSend:function(data){
                $(".saveBtn").text("Please wait...");
                $(".saveBtn").prop("disabled",true); 
                $(".hidethis").LoadingOverlay("show");
            },
            success:function(data){
                $(".saveBtn").text("Save");
                    
                if(data.success == 1){
                    $.toast({
                        heading: 'Success',
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
                        bgColor: '#FFA500',
                        textColor: 'white'  
                    });
                }

                $("#date").val("");
                $('#name').val('none').change();
                $("#paymenttype").val("");
                $("#paymentfor").val("");
                $("#paymentamount").val("")
                $("#refno").val("");
                $("#notes").val("");

                $(".hidethis").LoadingOverlay("hide");

            }

        });//ajax        
    }

    function getInputs(val){

        if(val == 'add'){
            date = $("#date").val();
            name = $("#name").val();
            paymenttype =  $("#paymenttype").val();
            paymentfor = $("#paymentfor").val();
            paymentamount = $("#paymentamount").val()
            refno = $("#refno").val()
            notes = $("#notes").val()
        }

    }

    //allowing numeric with decimal 
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
    
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
        }
    });     

    function checkInputs(formname){
    $(formname).find('.required_fields').each(function(){ //loop all input field then validate
        if ($(this).val() == ""){
            $(this).css("border-color", "#d9534f"); //change all empty to color red
        }else{
            $(this).css("border-color", "#eee");  //rollback when not empty
            errorFound = false;
            flag = true;
        }
    });

    $(formname).find('.required_fields').each(function(){ //loop all input field then validate
        if ($(this).val() == ""){ // if empty show error
            flag = false;
            // $(this).css("border-color","#d9534f");
            $(this).focus();
                $.toast({
                    heading: 'Note:',
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
                flag = true;
                errorFound = false;
            }
    });

        if(errorFound == false){
            $(formname).find('.payment').each(function(){ //loop all input field then validate
                if ($(this).val() <= 0){ // if empty show error
                    flag = false; //update error to 1
                    // $(this).css("border-color","#d9534f");
                    $(this).css("border-color", "#d9534f"); //change all empty to color red
                    $(this).focus();
                
                    $.toast({
                        heading: 'Note:',
                        text: 'Payment amount must not be less than zero',
                        icon: 'warning',
                        loader: false,   
                        stack: false,
                        position: 'top-center',     
                        bgColor: '#FFA500;',
                        textColor: 'white'
                    });

                    return false; //focus first empty fields

                }else{
                    flag = true;
                    $(this).css("border-color", "#eee");  //rollback when not empty
                }         
            
            });

            $(formname).find('.refno').each(function(){ //loop all input field then validate
                if ($(this).val() <= 0){ // if empty show error
                    flag = false; //update error to 1
                    // $(this).css("border-color","#d9534f");
                    $(this).css("border-color", "#d9534f"); //change all empty to color red
                    $(this).focus();
                
                    $.toast({
                        heading: 'Note:',
                        text: 'Ref no must not be less than zero',
                        icon: 'warning',
                        loader: false,   
                        stack: false,
                        position: 'top-center',     
                        bgColor: '#FFA500;',
                        textColor: 'white'
                    });

                    return false; //focus first empty fields

                }else{
                    flag = true;
                    $(this).css("border-color", "#eee");  //rollback when not empty
                }         
            
            });             

        }

    }

});//main
