$(function(){

    //Variables Declaration//////////////////////////////////////////

    var base_url = $("body").data("base_url");
    $('.inter2').css('display','none'); 
    var dateFrom;
    var dateTo;
    var id;
    var supplier;
    var amount;
    var status;

    //variables for adding and updating record
    var addsupplier;
    var addstart;
    var addend;
    var addamount;
    var addstatus;
    var searchtype = "none";

    var flag = true;
    var errorFound = false;

    //Getting date now with mm-dd-yyyy format
    var dateNow = ("0" + ((new Date).getMonth() + 1)).slice(-2) + "/" + ("0" + (new Date).getDate()).slice(-2) + "/" + (new Date).getFullYear();

    //LISTENERS///////////////////////////////////////////////////

    $('.searchBtn').on('click', function(){   // for select box     
        getValues();
        showTable(dateFrom,dateTo,id,supplier,searchtype);
    });

    $('.addItem').on('click', function(){   // for select box
    $('.interface1').hide();
    $('.inter2').show();
    formState = "add";
    });

    $('.backBtn').on('click', function(){   // for select box     
    $('.interface1').show();
    $('.inter2').hide();
    clearForm();
    });

    $('#frmSupplierLimit').submit(function(e){   // for select box 
        e.preventDefault();   
        checkInputs('#frmSupplierLimit');
        getInputs('add');
        
        if(flag){

            if(formState == "add"){
                saveSupplierLimit();    
            }

        }

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

    var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
            url :base_url+"Main_entity/supplier_limit_List", // json datasource
            type: "post",  // method  , by default get
            data:{'dateFrom':dateFrom,'dateTo':dateTo,'id':id,'supplier':supplier,'searchtype':searchtype},
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
                    $("#table-grid_processing").css("display","none");
                }
            }
    });//data table

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
        $('.inter2').css('display','block');
        $(".saveBtn").text("Update Delivery Vehicle");
        $("#adddate").prop("disabled",true); 

    });

    $('#table-grid').delegate(".btnView", "click", function(){

        id = this.id;
        supplier = $(this).data('suppliername');
        dateFrom = $(this).data('start');
        dateTo = $(this).data('end');
        amount = $(this).data('amount');

        $('#viewSupplierinfo').modal('show');
        supplierView();

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
        $('.inter2').css('display','block');
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
    function saveSupplierLimit(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity/addnew_SupplierLimit',
            data: {'addsupplier':addsupplier,'addstart':addstart,'addend':addend,'addamount':addamount},
                    
            beforeSend:function(data){
                $(".saveBtn").text("Please wait...");
                $(".saveBtn").prop("disabled",true); 
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

    function updateDeliveryVehicle(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity_jv/update_delVehicle',
            data: {'adddate':adddate,'addvehicle':addvehicle,'addcategory':addcategory,'addamount':addamount,'adddetails':adddetails,'id':id},
                    
            beforeSend:function(data){
                $(".saveBtn").text("Please wait...");
                $(".saveBtn").prop("disabled",true); 
            },
            success:function(data){
                $(".saveBtn").text("Update Delivery Vehicle");
                    
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
                clearForm();


            }

        });//ajax        
    }

    function closeDeliveryVehicle(){
        $.ajax({
            type:'post',
            url: base_url+'Main_entity_jv/close_delVehicle',
            data: {'adddate':adddate,'addvehicle':addvehicle,'addcategory':addcategory,'addamount':addamount,'adddetails':adddetails,'id':id},
                    
            beforeSend:function(data){
                $(".saveBtn").text("Please wait...");
                $(".saveBtn").prop("disabled",true); 
            },
            success:function(data){
                $(".saveBtn").text("Close Delivery Vehicle");
                    
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
                clearForm();


            }

        });//ajax        
    }    

    function clearForm(){
        $("#addstart").val(dateNow);
        $("#addsupplier").val('');
        $("#addend").val('');
        $("#addamount").val('');
        $('.required_fields').css("border-color", "#eee");
    }

    function showTable(dateFrom,dateTo,id,supplier,searchtype){
        var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
            url :base_url+"Main_entity/supplier_limit_List", // json datasource
            type: "post",  // method  , by default get
            data:{'dateFrom':dateFrom,'dateTo':dateTo,'id':id,'supplier':supplier,'searchtype':searchtype},
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
    };

    function getValues(){

        dateFrom = $(".search-input-select1").val();
        dateTo = $(".search-input-select2").val();
        id =  $(".idnosearch").val();
        supplier = $(".nameSearch").val();
        // amount =  $(".amountsearch").val();
        // status = $(".statussearch").val();
        searchtype = $('#divsearchfilter').val();

    }

    function getInputs(val){

        if(val == 'add'){
            addstart = $("#addstart").val();
            addend = $("#addend").val();
            addsupplier =  $("#addsupplier").val();
            addamount = $("#addamount").val();
        }

    }

    function supplierView(){

        $(".lblSupplier").text(supplier);
        $(".lblStart").text(dateFrom);
        $(".lblEnd").text(dateTo);
        $(".lblAmount").text(amount);

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
                    heading: 'Note',
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
                        heading: 'Note',
                        text: 'Amount must not be less than zero',
                        icon: 'info',
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

            addstart = $(".addstart").val();
            addend = $(".addend").val();

            if(addend < addstart){
                $.toast({
                    heading: 'Note',
                    text: 'End date must not be less than start date',
                    icon: 'info',
                    loader: false,   
                    stack: false,
                    position: 'top-center',     
                    bgColor: '#FFA500;',
                    textColor: 'white'
                });
                $(".addend").val('');                
                $('.addend').focus();           
                return false; //focus first empty fields 
            }

        }

    }

});//main


function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}