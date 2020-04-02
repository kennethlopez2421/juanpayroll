$(function(){

    //Variables Declaration//////////////////////////////////////////

    var base_url = $("body").data("base_url");
    
    var idno;
    var id;
    var name;
    var suppliername;
    var id;
    var contactno;
    var contactperson;
    var credit_term;
    var address;
    var otherinf;   
    var searchtype = "all";
    var flag = false;
    var errorFound = false;
    var editidno1;
    
    // var dataTable = $('#table-grid').DataTable({
    //         "processing": true,
    //         "serverSide": true,
    //         "destroy": true,
    //         // "columnDefs": [{ "orderable": false, "targets": [ 3 ], "sClass":"text-center" }],
    //         "order": [[ 1, "asc"]],
    //         "ajax":{
    //         url :base_url+"Main_entity/supplierList", // json datasource
    //         type: "post",  // method  , by default get
    //         data:{'idno':idno,'name':name, 'searchtype': searchtype},
    //         beforeSend:function(data)
    //         {
    //             $("#table-grid").LoadingOverlay("show"); 
    //         },
    //         complete: function()
    //         {
    //             $("#table-grid").LoadingOverlay("hide"); 
    //         },
    //             error: function(){  // error handling
    //                 $(".table-grid-error").html("");
    //                 // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
    //                 $("#table-grid_processing").css("display","none");
    //             }
    //         }
    //     });//data table


    //LISTENERS///////////////////////////////////////////////////

    $('.searchBtn').on('click', function(){   // for select box     
        getValues();
        showTable(idno,suppliername,searchtype);
    });

    $('.addsupp').on('click', function(){   // for select box     
    $('#addsupplier').modal('show');   
    });

     $('.dividno').show('slow');
    $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

           if(searchtype == "dividno")
           {
             $('.dividno').show('slow');
             $('.divname').hide('slow');
             $("#nameSearch").val("");
             $("#idnosearch").val("");      
           }
           else if(searchtype == "divname")
           {
             $('.divname').show('slow');
             $('.dividno').hide('slow');    
             $("#nameSearch").val("");
             $("#idnosearch").val("");
           }
         
    });

    $('#table-grid').delegate(".editBtn", "click", function(){

        var id = $(this).data('value');


        $.ajax({
            type: 'post',
            url: base_url+'Main_entity/get_supplierlist',
            data:{'id':id},
            success:function(data){


                data = JSON.parse(data);

                $('#suppliername').val(data.suppliername);
                $('#id').val(data.id);
                $('#contact').val(data.contactperson);
                $('#contactno').val(data.contactno);
                $('#credit_term').val(data.credit);
                $('#address').val(data.address);
                $('#other_info').val(data.otherinf);

                $('#editSupplier').modal();

            },
            error: function(error){
                $.toast({
                    heading: 'Note',
                    text: 'Something went wrong. Please try again.',
                    icon: 'info',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    allowToastClose: false,
                    bgColor: '#FFA500',
                    textColor: 'white'  
                });
            }
        });


    });

    $('#table-grid').delegate(".btnDelete", "click", function(){

        id = this.id;
        idno = $(this).data('value');
        name = $(this).data('name');
        $('.confirmMsg').text(name);
        $('#confirmationBox').modal('show');

    });    

    $('.updateBtn').on('click', function(){   // for select box     
        var suppliername = $('#suppliername').val();
        var id = $('#id').val();
        var contact =$('#contact').val();
        var contactno = $('#contactno').val();
        var credit_term = $('#credit_term').val();
        var address = $('#address').val();
        var other_info = $('#other_info').val();
        var checker=0;

        if(suppliername == "" || id == "" || contact == "" || contactno == "" || credit_term == "" || address == "")
        {
            checker=0;
             $.toast({
                    heading: 'Note:',
                    text: 'Please fill all the required fields.',
                    icon: 'info',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    allowToastClose: false,
                    bgColor: '#FFA500',
                    textColor: 'white'  
                });

        }
        else
        {
            checker=1;
        }

       
       if(checker == 1)
       {

        $.ajax({
            type:'post',
            url: base_url+'Main_entity/update_supplierlist',
            data: {'suppliername':suppliername , 'id':id , 'contactno':contactno , 'contact':contact , 'credit_term':credit_term , 'address':address,
            'other_info':other_info},
                    
            beforeSend:function(data){
                $("body").LoadingOverlay("show"); 
            },
            complete: function(){
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                $(".updateBtn").text("Save Record");
                    
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
                        heading: 'Note:',
                        text: data.message,
                        icon: 'info',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                    });
                }

                setTimeout(function(){
                location.reload();
                },2000);
                resetData();

            }

        });
       }      

    });

    $('.savebtn').on('click', function(){   // for select box     
        var add_suppliername = $('#add_suppliername').val();
        // var add_id = $('#add_id').val();
        var add_contact =$('#add_contact').val();
        var add_contactno = $('#add_contactno').val();
        var add_credit_term = $('#add_credit_term').val();
        var add_address = $('#add_address').val();
        var add_other_info = $('#add_other_info').val();
        var checker=0;

        if(add_suppliername == "" || add_contact == "" || add_contactno == "" || add_credit_term == "" || add_address == "")
        {
            checker=0;
             $.toast({
                    heading: 'Note:',
                    text: 'Please fill all the required fields.',
                    icon: 'info',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    allowToastClose: false,
                    bgColor: '#FFA500',
                    textColor: 'white'  
                });

        }
        else
        {
            checker=1;
        }

       if(checker == 1)
       {

        $.ajax({
            type:'post',
            url: base_url+'Main_entity/save_supplierlist',
            data: {'add_suppliername':add_suppliername , 'add_contact':add_contact , 'add_contactno':add_contactno , 'add_credit_term':add_credit_term ,
            'add_address':add_address, 'add_other_info':add_other_info},
                    
            beforeSend:function(data){
                $("body").LoadingOverlay("show"); 
            },
            complete: function(){
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                $(".savebtn").text("Save Record");
                    
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
                        heading: 'Note:',
                        text: data.message,
                        icon: 'info',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                    });
                }

                setTimeout(function(){
                location.reload();
                },2000);
                resetData();

            }

        });
       }      

    });


     $('.deleteBtn').on('click', function(){   // for select box   
            var suppliername = $('#suppliername').val();  

            $.ajax({
            type:'post',
            url: base_url+'Main_entity/delete_supplierlist',
            data: {'id':id,'suppliername':suppliername},          
            beforeSend:function(data){
                $("body").LoadingOverlay("show"); 
            },
            complete: function(){
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                $(".deleteBtn").text("Delete");
                    
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
                        heading: 'Note:',
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

                setTimeout(function(){
                location.reload();
                },2000);
                resetData();

            }

        });//ajax

    });


    $('.cancelBtn').on('click', function(){   // for select box
        resetData();
    });    


    //FUNCTIONS///////////////////////////////////////////////////

    function saveNewagent(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/save_Agent',
            data: {'idno':inpIdno,'name':inpName},
                    
            beforeSend:function(data){
                $(".saveBtn").text("Please wait...");
                $(".saveBtn").prop("disabled",true); 
            },
            success:function(data){
                $(".saveBtn").text("Save Record");
                    
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
                        heading: 'Note:',
                        text: data.message,
                        icon: 'info',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                    });
                }

                setTimeout(function(){
                location.reload();
                },2000);
                resetData();

            }

        });//ajax        
    }

    function updatesupplierlist(){
   
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/update_supplierlist',
            data: {'suppliername':suppliername , 'id':id , 'contactno':contactno , 'contactperson':contactperson , 'credit_term':credit_term , 'address':address,
            'otherinf':otherinf},
                    
            beforeSend:function(data){
                $(".updateBtn").text("Please wait...");
                $(".updateBtn").prop("disabled",true); 
            },
            success:function(data){
                $(".updateBtn").text("Save Record");
                    
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
                        heading: 'Note:',
                        text: data.message,
                        icon: 'info',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                    });
                }

                setTimeout(function(){
                location.reload();
                },2000);
                resetData();

            }

        });//ajax        
    }

    function resetData(){

        idno = "";
        id = "";
        name = "";
        inpIdno = "";
        inpName = "";    
        flag = false;
        errorFound = false;

    }

    function showTable(idno,suppliername,searchtype){
        var checker=0;
        if(searchtype == "dividno")
        {
            if(idno == "")
            {
                 $.toast({
                    heading: 'Note:',
                    text: 'Please fill in id number field.',
                    icon: 'info',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    allowToastClose: false,
                    bgColor: '#FFA500',
                    textColor: 'white'  
                });
                checker=0;
            }
            else
            {
                checker=1;
            }
           
        }
        else
        {
            if(suppliername == "")
            {
                 $.toast({
                    heading: 'Note:',
                    text: 'Please fill in name field.',
                    icon: 'info',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    allowToastClose: false,
                    bgColor: '#FFA500',
                    textColor: 'white'  
                });
                checker=0;
            }
            else
            {
                checker=1;
            } 
        }

        if(checker == 1)
        {
            var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            // "columnDefs": [{ "orderable": false, "targets": [ 3 ], "sClass":"text-center" }],
            "order": [[ 1, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/supplierList", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'suppliername':suppliername, 'searchtype': searchtype},
            beforeSend:function(data)
            {
                $("#table-grid").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("#table-grid").LoadingOverlay("hide"); 
            },
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
            });//data table
            $("#table-grid").prop('hidden', false);
        }
        
            
        
        
    };

    function deleteAgent(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/delete_Agent',
            data: {'id':id,'name':name},
                    
            beforeSend:function(data){
                $(".deleteBtn").text("Please wait...");
                $(".deleteBtn").prop("disabled",true); 
            },
            success:function(data){
                $(".deleteBtn").text("Delete");
                    
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
                        heading: 'Note:',
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

                setTimeout(function(){
                location.reload();
                },2000);
                resetData();

            }

        });//ajax 
    }

    function getValues(){
        idno =  $(".idnosearch").val();
        suppliername = $(".nameSearch").val();
        searchtype = $('#divsearchfilter').val();
    }

    function getInputs(val){

        if(val == 'add'){
            inpIdno =  $(".idno").val();
            inpName = $(".name").val();
        }else if(val == 'update'){
            inpIdno =  $(".editidno").val();
            inpName = $(".editname").val();
            inpIdno1 = $(".editidno1").val();

        }else{
            //do nothing
        }

    }

    function checkInputs($formname){
    $($formname).find('.required_fields').each(function(){ //loop all input field then validate
        if ($(this).val() == ""){
            $(this).css("border-color", "#d9534f"); //change all empty to color red
        }else{
            $(this).css("border-color", "#eee");  //rollback when not empty
            errorFound = false;
            flag = true;
        }
    });

    $($formname).find('.required_fields').each(function(){ //loop all input field then validate
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
    }

});//main


function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}
