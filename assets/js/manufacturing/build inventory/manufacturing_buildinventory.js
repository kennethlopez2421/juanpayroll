$(function(){
    var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
    $(".inter2").css('display','none');
    var prepDate;
    var mixDate;
    var buildDate;
    var inv;
    var invid;
    var ingLoc;
    var meatLoc;
    var buildLoc;
    var qty;
    var notes;
    var unit;
    var flag = true;
    var message = '';
    var error = 0;
    var errorFound = false;
      

    $("#frmbuildInventory").submit(function(e){
        e.preventDefault();
        var serial = $(this).serialize(); // collect all user input

            $(this).find('.required_fields').each(function(){ //loop all input field then validate
                if ($(this).val() == ""){
                    $(this).css("border-color", "#d9534f"); //change all empty to color red
                }else{
                    $(this).css("border-color", "#eee");  //rollback when not empty
                    errorFound = false;
                }
            });
           $(this).find('.required_fields').each(function(){ //loop all input field then validate
                if ($(this).val() == ""){ // if empty show error
                    flag = false;
                    // $(this).css("border-color","#d9534f");
                    $(this).focus();
                    $.toast({
                        heading: 'Note',
                        text: 'Please fill out this field',
                        icon: 'error',
                        loader: false,   
                        stack: false,
                        position: 'top-center',     
                        bgColor: '#f0ad4e;',
                        textColor: 'white'
                    });
                    errorFound = true;
                    return false; //focus first empty fields
                }else{
                    errorFound = false;
                }
            });

        if(errorFound == false){
            $(this).find('.qty').each(function(){ //loop all input field then validate
                if ($(this).val() <= 0 || $(this).val() == "."){ // if empty show error
                    flag = false; //update error to 1
                    // $(this).css("border-color","#d9534f");
                    $(this).css("border-color", "#d9534f"); //change all empty to color red
                    $(this).focus();
                
                    $.toast({
                        heading: 'Note',
                        text: 'Quantity must not be less than zero',
                        icon: 'error',
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
       


        if(flag){// if no error then execute 
            
            prepDate = $('#prep_date').val();
            mixDate = $('#mix_date').val();
            buildDate = $('#build_date').val();
            invid = $('#inv').val();
            inv = $("#inv>option:selected").html();
            ingLoc = $("#ing>option:selected").html();
            meatLoc = $("#meat>option:selected").html();
            buildLoc = $("#build>option:selected").html();
            qty = $('#qty').val();
            notes = $('#notes').val();           

            $.ajax({
                type:'post',
                url: base_url+'Main_manufacturing/get_unitDesc',
                data: serial,
                    
                beforeSend:function(data){
                    $(".proceedBtn").text("Please wait...");
                    $(".proceedBtn").prop("disabled",true); 
                },
                success:function(data){
                    $(".proceedBtn").text("Proceed");
                    $(".lblUnit").text(data.unit.description);
                    
                    if(data.success == 1){

                        $(".interface1").css('display','none');
                        $(".inter2").css('display','block');
                        $(".tbl-details").css('display','block');
                        $(".buildButton").css('display','block');
                        $(".inter2").css('background',"#EEF5F9");

                        $(".lblprepDate").text(prepDate);
                        $(".lblmixDate").text(mixDate);
                        $(".lblbuildDate").text(buildDate);
                        $(".lblQty").text(accounting.formatMoney(qty));

                        $(".lblItem").text(inv);
                        $(".lblIngredients").text(ingLoc);
                        $(".lblMeat").text(meatLoc);
                        $(".lblBuild").text(buildLoc);
                        $(".txtarea").text(notes);     

                        var dataTable = $('#table-grid').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "ajax":{
                                url :base_url+"Main_manufacturing/table_bom", // json datasource
                                type: "post",  // method  , by default get
                                data:{'invid':invid,'qty':qty},
                                error: function(){  // error handling
                                    $(".table-grid-error").html("");
                                    $("#table-grid_processing").css("display","none");
                                }
                            }
                        });//data table                                                                         

                    }else{
                        $.toast({
                            heading: 'Note',
                            text: message,
                            icon: 'error',
                            loader: false,  
                            stack: false,
                            position: 'top-center', 
                            allowToastClose: false,
                            bgColor: '#f0ad4e',
                            textColor: 'white'  
                        });              
                    }
                }

                });//ajax

        }


    });//frm

    $('.searchBtn').on('click', function(){   // for select box
        
        var oTable = $('#table-grid').dataTable();

        oTable.fnFilter( $(".search_item").val(), '0' );
        oTable.fnFilter( $(".search_quantity").val(), '1' );
        oTable.fnFilter( $(".search_unit").val(), '2' );
             
    });

    $(".buildButton").click(function(e){
        var form = $("#frmbuildInventory");

        var serial = form.serialize();

        $.ajax({
            type:'post',
            url: base_url+'Main_manufacturing/buildInventory',
            data: serial,

            beforeSend:function(data){
                $(".buildButton").text("Please wait...");
                $(".buildButton").prop("disabled",true);

            },

            success:function(data){
                $(".buildButton").text("Build Inventory");

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
                        bgColor: '#f0ad4e',
                        textColor: 'white'  
                    });
            }

                setTimeout(function(){
                    location.reload();
                },3000);

            }//success
        });//ajax

    });//build

/////js toh sa baba

    function isNumberKeyOnly(evt)   
    {    
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
        return true;
    }


    //allowing numeric with decimal 
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
    
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
        }
    }); 


    $('#prep_date').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-1d',
            endDate: '+90d'
    });

    $('#mix_date').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-1d',
            endDate: '+90d'
    });


    $('#build_date').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-1d',
            endDate: '+90d'
    });     

});//main
