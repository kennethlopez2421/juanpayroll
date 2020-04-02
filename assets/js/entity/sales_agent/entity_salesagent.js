$(function(){

    //Variables Declaration//////////////////////////////////////////

    var base_url = $("body").data("base_url");

    var idno;
    var id;
    var name;
    var inpIdno;
    var inpName;    
    var searchtype = "all";
    var flag = false;
    var errorFound = false;
    var editidno1;

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

    var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "columnDefs": [{ "orderable": false, "targets": [ 3 ], "sClass":"text-center" }],
            "order": [[ 1, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/salesagent_List", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'name':name, 'searchtype': searchtype},
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


    //LISTENERS///////////////////////////////////////////////////

    $('.searchBtn').on('click', function(){   // for select box     
        getValues();
        showTable(idno,name,searchtype);
    });

    $('.addItem').on('click', function(){   // for select box     
    $('#addSalesAgent').modal('show');   
    });

    $('.saveBtn').on('click', function(){   // for select box     
        checkInputs('#newAgent');
        getInputs('add');
    
        if(flag){
            saveNewagent();
        }

    });

    $('#table-grid').delegate(".editBtn", "click", function(){

        id = this.id;
        inpIdno = $(this).data('value');
        inpName = $(this).data('name');

        $('#editSalesAgent').modal('show');

        $('.editname').val(inpName);
        $('.editidno').val(inpIdno);

        $('.editidno1').val(inpIdno);


    });

    $('#table-grid').delegate(".btnDelete", "click", function(){

        id = this.id;
        idno = $(this).data('value');
        name = $(this).data('name');
        $('.confirmMsg').text(name);
        $('#confirmationBox').modal('show');

    });    

    $('.updateBtn').on('click', function(){   // for select box     
        checkInputs('#updateAgent');
        getInputs('update');
    
        if(flag){
            updateAgent();
        }

    });


    $('.deleteBtn').on('click', function(){   // for select box     
        if(id != ""){
            deleteAgent();
        }else{
            //do nothing
        }

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

    function updateAgent(){
   
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/update_Agent',
            data: {'idno':inpIdno,'name':inpName,'id':id, 'idnoorig': inpIdno1},
                    
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

    function showTable(idno,name,searchtype){
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
            if(name == "")
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
            "columnDefs": [{ "orderable": false, "targets": [ 3 ], "sClass":"text-center" }],
            "order": [[ 1, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/salesagent_List", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'name':name, 'searchtype': searchtype},
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
        name = $(".nameSearch").val();
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
