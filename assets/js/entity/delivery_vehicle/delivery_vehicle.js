$(function(){

    //Variables Declaration//////////////////////////////////////////

    var base_url = $("body").data("base_url");
    $('.interface2').css('display','none');
    var dateTo;
    var dateFrom;
    var delVehicle;
    var category;
    var status;
    var id;
    var date;
    var vehicle;
    var amount;
    var category;
    var details;

    //variables for adding and updating record
    var adddate;
    var addvehicle;
    var addcategory;
    var addamount;
    var adddetails;

    var searchtype;

    var flag = true;
    var errorFound = false;

     $('.divdate').show('slow');
     $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();
           if(searchtype == "divdelid")
           {
             $('.divdelid').show('slow');
             $('.divdate').show('slow');
   
             $("#vehicle").val("");
             $("#idno").val("");      
           }
           else
           {
             $('.divdate').show('slow');
             $('.divdelid').hide('slow');
        
             $("#vehicle").val("");
             $("#idno").val("");    
           }
         
    });
  

     var dataTable = $('#table-grid').DataTable({
            "destroy": true,
            //"processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
            url :base_url+"Main_entity/delvehicle_List", // json datasource
            type: "post",  // method  , by default get
            data:{'searchtype':searchtype},
            beforeSend:function(data)
            {
                $("body").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("body").LoadingOverlay("hide"); 
            },
            error: function(){  // error handling
                $(".table-grid-error").html("");
                // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#table-grid_processing").css("display","none");
            }
           }
        });//data table


     //start
    $(".searchBtn").click(function(e){
        e.preventDefault();
        var vehicle = $("#vehicle").val();
        var datefrom = $("#datefrom").val();
        var dateto = $("#dateto").val();
        var searchtype = $('#divsearchfilter').val();

        var date1 = formatDate(datefrom);
        var date2 = formatDate(dateto);
     


        var checker=0;
        if(searchtype == "divdate")
        {
            if( datefrom == "" || dateto == "")
            {
                checker=0;
                $.toast({
                    heading: 'Note:',
                    text: "Please fill date range field.",
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
        }
        else
        {
            if( datefrom == "" || dateto == "")
            {
                checker=0;
                $.toast({
                    heading: 'Note:',
                    text: "Please fill date range field.",
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
        }
    


        
        if(checker == 1)
        {
            var dataTable = $('#table-grid').DataTable({
                "destroy": true,
                //"processing": true,
                "serverSide": true,
                "columnDefs": [
                    { targets: 5, orderable: false, "sClass":"text-center" }
                ],
                "ajax":{
                    url :base_url+"Main_entity/delvehicle_List", // json datasource
                    type: "post",  // method  , by default get
                    data:{'searchtype': searchtype, 'datefrom': date1, 'dateto': date2, 'status':  vehicle},
                    beforeSend:function(data)
                    {
                        $("body").LoadingOverlay("show"); 
                    },
                    complete: function()
                    {
                        $("body").LoadingOverlay("hide"); 
                    },
                    error: function(){  // error handling
                        $(".table-grid-error").html("");
                        $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="6" style = "text-align: center;">No data found in the server</th></tr></tbody>');
                        $("#table-grid_processing").css("display","none");
                    }
                }
            });
        }

    });
    //end


    var dateNow = ("0" + ((new Date).getMonth() + 1)).slice(-2) + "/" + ("0" + (new Date).getDate()).slice(-2) + "/" + (new Date).getFullYear();
    //LISTENERS///////////////////////////////////////////////////

    $('.searchBtn').on('click', function(){   // for select box     
        getValues();
        // showTable(dateTo,dateFrom,delVehicle,category,status,searchtype);
    });

    $('.addItem').on('click', function(){   // for select box
    $('.interface1').css('display','none');
    $('.interface2').css('display','block');
    formState = "add";
    });

    $('.backBtn').on('click', function(){   // for select box     
    $('.interface1').css('display','block');
    $('.interface2').css('display','none'); 
    clearForm();
    });

    $('#frmDelVehicle').submit(function(e){   // for select box 
        e.preventDefault();   
        checkInputs('#frmDelVehicle');
        getInputs('add');
        
        if(flag){

            if(formState == "add"){
                saveDeliveryVehicle();    
            }

            if(formState == "edit"){
                updateDeliveryVehicle();
            }

            if(formState == "close"){
                closeDeliveryVehicle();
            }
            
        }

    });

    $('#table-grid').delegate(".editBtn", "click", function(){

        clearForm();
        id = this.id;
        date = $(this).data('date');
        vehicle = $(this).data('vehicle');
        amount = $(this).data('amount');
        category = $(this).data('category');
        details = $(this).data('details');
        editVehicle();

        formState = "edit";

        $('.interface1').css('display','none');
        $('.interface2').css('display','block');
        $(".saveBtn").text("Update Delivery Vehicle");
        $("#adddate").prop("disabled",true); 

    });

    $('#table-grid').delegate(".btnView", "click", function(){

        id = this.id;
        date = $(this).data('date');
        vehicle = $(this).data('vehicle');
        amount = $(this).data('amount');
        category = $(this).data('category');
        details = $(this).data('details');

        $('#viewVehicle').modal('show');
        vehicleView();

    });

    $('#table-grid').delegate(".btnClose", "click", function(){

        clearForm();
        id = this.id;
        date = $(this).data('date');
        vehicle = $(this).data('vehicle');
        amount = $(this).data('amount');
        category = $(this).data('category');
        details = $(this).data('details');
        editVehicle();

        formState = "close";

        $('.interface1').css('display','none');
        $('.interface2').css('display','block');
        $(".saveBtn").text("Close Delivery Vehicle");
        $("#adddate").prop("disabled",true); 

    });            

    $('#table-grid').delegate(".btnDelete", "click", function(){

        id = this.id;
        vehicle = $(this).data('vehicle');
        $('.confirmMsg').text(vehicle);
        $('#confirmationBox').modal('show');

    });    

    $('.deleteBtn').on('click', function(){   // for select box     
        if(id != ""){
            deleteVehicle();
        }else{
            //do nothing
        }

    });

    //FUNCTIONS///////////////////////////////////////////////////
    var adddate;
    var addvehicle;
    var addcategory;
    var addamount;
    var adddetails;
    function saveDeliveryVehicle(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/addnew_Vehicle',
            data: {'adddate':adddate,'addvehicle':addvehicle,'addcategory':addcategory,'addamount':addamount,'adddetails':adddetails},
                    
            beforeSend:function(data)
            {
                $("body").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                $(".saveBtn").text("Save");
                    
                if(data.success == 1){
                    $.toast({
                        heading: 'Note',
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
                        icon: 'info',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                    });
                }
                //setTimeout(function(){location.reload();});resetData();
            }

        });//ajax        
    }

    function updateDeliveryVehicle(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/update_delVehicle',
            data: {'adddate':adddate,'addvehicle':addvehicle,'addcategory':addcategory,'addamount':addamount,'adddetails':adddetails,'id':id},
                    
            beforeSend:function(data){
                $(".saveBtn").text("Please wait...");
                $(".saveBtn").prop("disabled",true); 
            },
            success:function(data){
                $(".saveBtn").text("Update Delivery Vehicle");
                    
                if(data.success == 1){
                    $.toast({
                        heading: 'Note',
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
                        icon: 'info',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                    });
                }

                // setTimeout(function(){
                // location.reload();
                // },2000);
                // clearForm();


            }

        });//ajax        
    }

    function closeDeliveryVehicle(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/close_delVehicle',
            data: {'adddate':adddate,'addvehicle':addvehicle,'addcategory':addcategory,'addamount':addamount,'adddetails':adddetails,'id':id},
                    
            beforeSend:function(data)
            {
                $("body").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                $(".saveBtn").text("Close Delivery Vehicle");
                    
                if(data.success == 1){
                    $.toast({
                        heading: 'Note',
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
                        heading: 'Warning',
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

                // setTimeout(function(){
                // location.reload();
                // },2000);
                // clearForm();


            }

        });//ajax        
    }    

    function clearForm(){
        $("#adddate").val(dateNow);
        $("#addvehicle").val('');
        $("#addcategory").val('');
        $("#addamount").val('');
        $("#adddetails").val('');
        $(".saveBtn").text("Save");
        $('.required_fields').css("border-color", "#eee");
    }

    function showTable(dateTo,dateFrom,delVehicle,category,status,searchtype){
        var dataTable = $('#table-grid').DataTable({
            "destroy": true,
            //"processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
            url :base_url+"Main_entity/delvehicle_List", // json datasource
            type: "post",  // method  , by default get
            data:{'searchtype':searchtype},
            beforeSend:function(data)
            {
                $("#table-grid").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("body").LoadingOverlay("hide"); 
            },
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });//data table
    };

    function deleteVehicle(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/delete_delVehicle',
            data: {'id':id},
                    
            beforeSend:function(data)
            {
                $("body").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                $(".deleteBtn").text("Delete");
                    
                if(data.success == 1){
                    $.toast({
                        heading: 'Note',
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
                        heading: 'Warning',
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

    function getValues(){
        dateFrom =  $(".search-input-select1").val();
        dateTo = $(".search-input-select2").val();
        delVehicle =  $(".vehicle").val();
        category = $(".maintenance").val();
        status =  $(".status").val();
        searchtype = $('#divsearchfilter').val();
    }

    function getInputs(val){

        if(val == 'add'){
            adddate =  $("#adddate").val();
            addvehicle = $("#addvehicle").val();
            addcategory =  $("#addcategory").val();
            addamount = $("#addamount").val();
            adddetails =  $("#adddetails").val();
        }else if(val == 'update'){
            adddate =  $("#adddate").val();
            addvehicle = $("#addvehicle").val();
            addcategory =  $("#addcategory").val();
            addamount = $("#addamount").val();
            adddetails =  $("#adddetails").val();
        }else{
            //do nothing
        }

    }

    function vehicleView(){

        $(".lblDate").text(date);
        $(".lblDelvehicle").text(vehicle);
        $(".lblAmount").text(amount);
        $(".lblCategory").text(category);
        $(".parDetails").text(details);
        $(".lblticket").text('Delivery Vehicle #'+id);

    }

    function editVehicle(){

        $("#adddate").val(date);

        $('[name=addvehicle] option').filter(function() { 
            return ($(this).text() == vehicle); //To select Blue
        }).prop('selected', true);

        $('[name=addcategory] option').filter(function() { 
            return ($(this).text() == category); //To select Blue
        }).prop('selected', true);

        $("#addamount").val(amount);
        $("#adddetails").val(details);

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
                    heading: 'Warning',
                    text: 'Please fill out this field',
                    icon: 'info',
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
            $(formname).find('.amount').each(function(){ //loop all input field then validate
                if ($(this).val() <= 0){ // if empty show error
                    flag = false; //update error to 1
                    // $(this).css("border-color","#d9534f");
                    $(this).css("border-color", "#d9534f"); //change all empty to color red
                    $(this).focus();
                
                    $.toast({
                        heading: 'Warning',
                        text: 'Amount must not be less than zero',
                        icon: 'info',
                        loader: false,   
                        stack: false,
                        position: 'top-center',     
                        bgColor: '#f0ad4e;',
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


function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}   