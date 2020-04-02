$(function(){

	var base_url = $("body").data("base_url");

    var itemid;
    var editArray = [];
    var bomidArr = [];
    var qtyArr = [];

    var itemidArr = [];
    var itemqtyArr = [];

    var currentSelectedItemId;
    var currentSelectedItemName;
    var currentSelectedUnit;

    var errorFound = false;
    var matchFound = false;

    var table = $('#table-materials-edit').DataTable({
                "order": [[ 1, "asc" ]],
                "columnDefs": [{ "orderable": false, "targets": [ 4 ], "sClass":"text-center" }]
        });

    loadTables();

    function loadTables(){

        itemid = $('.lblItemnameedit').data('value');

        $.ajax({
            type: 'post',
            url: base_url+'Main_manufacturing/material_balance_edit',
            data:{"itemid":itemid,"type":'edit'},
            
            success:function(data){
                if(data.success == 1){
                    var count = data.count;
                    var bomidData = data.bomidData;
                    var itemnameData = data.itemnameData;
                    var qtyData = data.qtyData;
                    var unitData = data.unitData;
                    var delBtn = data.delBtn;
                    var notes = data.notes;
                    clearTable();

                    try{
                        for(i=0;i<count;i++){
                            editArray = [bomidData[i],itemnameData[i],qtyData[i],unitData[i],delBtn[i]];
                            populateTable(editArray,1);
                        }
                    }catch(e){
                        // console.log("Error msg",e);
                    }
                    
                    $(".lblItemnameedit").text(data.itemname);
                    $(".lblUnitedit").text(data.unit);
                    $("#txtareanotes").text(data.notes);



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

                    // setTimeout(function(){
                    // location.reload();
                    // },3000);

                }

        });       
    }


    $(".add_inventory_modal").click(function(e){
            checkInputs('#addRow');
            var quantity = $("#qty").val();
            var item = $("#item").val();
            if(flag){
                if((currentSelectedItemId == null) || (currentSelectedItemName == null) || (currentSelectedUnit == null) || (quantity == null)){
                $('#viewAddrowModal').modal('toggle');
                    clearAddform();
                    $.toast({
                    heading: 'Note',
                    text: "Item doesnt exist",
                    icon: 'error',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    allowToastClose: false,
                    bgColor: '#f0ad4e',
                    textColor: 'white'  
                });// if there is no seleccted item or the input item is not on the list
                }else{

                    if(item != currentSelectedItemName){
                    $('#viewAddrowModal').modal('toggle');
                    clearAddform();
                    $.toast({
                    heading: 'Note',
                    text: "Item doesnt exist",
                    icon: 'error',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    allowToastClose: false,
                    bgColor: '#f0ad4e',
                    textColor: 'white'  
                    });
                    }else{

                        matchFound = false;

                        var dataIDstackup = table.rows().columns(0).data();
                        tableID = [];
                
                        dataIDstackup.each(function (value, index) {    
                            tableID = tableID.concat(value);
                        });//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP 
                        
                        for(i=0;i<tableID.length;i++){
                            
                            if(tableID[i] == currentSelectedItemId){

                                var oldValraw = table.row(i).column(2).data();//getting all the value qty from the table record
                                var oldVal = oldValraw[i].split(',').join('');

                                var quantity = $("#qty").val();//getting the input values in quantity input box
                                var inputVal = parseFloat(quantity);//inserting the value of input box in inputVal variable and converting the value to float/double

                                var newVal =  parseFloat(oldVal) + parseFloat(inputVal);//Sum the two values above the oldVal and inputVal
                                //oldVal[i] is the index of the match record

                                deleteMatchRow(currentSelectedItemId);//delete function delete the old record that match the new input
                                //dinedelete niya yung luma instead na palitan ng value yung nagmatch na record

                                selectedDataarray = [currentSelectedItemId,currentSelectedItemName.toUpperCase(),accounting.formatMoney(newVal),currentSelectedUnit,"<center><button class='btn btn-danger btnDeleteadd btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"];// adding selected data to array 
                                populateTable(selectedDataarray,2);//adding of new record that have the updated qty value that we sum up above

                                matchFound =  true;//setting the matchFound boolean to true to avoid adding new record, kasi pag walang ganito magadd lng siya ng new record hindi siya magdedelete pag may magkaparehas
                        
                                break;

                            }else{
                                matchFound = false;
                            }
                    
                        }//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP 

                    

                        if(matchFound == false){
                            selectedDataarray = [currentSelectedItemId,currentSelectedItemName.toUpperCase(),accounting.formatMoney(quantity),currentSelectedUnit,"<center><button class='btn btn-danger btnDeleteadd btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"];// adding selected data to array 
                            populateTable(selectedDataarray,2);                       
                        }

                        $('#viewAddrowModal').modal('toggle');
                        clearAddform();                       
                    }
        
                }
            }

    });

    $("#table-materials-edit").on('click', '.btnDeleteadd', function () {

        table.row($(this).parents('tr')).remove().draw(false);
    });


    $("#table-materials-edit").on('click', '.btnDelete', function () {

        table.row($(this).parents('tr')).remove().draw(false);
    });

    $('.updateMatBtn').on('click', function(){   // for select box

        var notes = $("#txtareanotes").val();
        var itemname = $(".lblItemnameedit").text();

        //getting data in datatable
        var dataID = table.rows().columns(0).data();
        dataID.each(function (value, index) {
            
            if((value == null) || (value == 0) || (value == '')){
                tableisEmpty = true;
            }else{
                tableisEmpty = false;
            }//check if table is empty

            bomidArr = bomidArr.concat(value);
        });

        //getting data in datatable
        var dataQTY = table.rows().columns(2).data();
        dataQTY.each(function (value, index) {
            
            if((value == null) || (value == 0) || (value == '')){
                tableisEmpty = true;
            }else{
                tableisEmpty = false;
            }//check if table is empty

            qtyArr = qtyArr.concat(value);
        });        


        $.ajax({
            type: 'post',
            url: base_url+'Main_manufacturing/update_Material_Balance',
            data:{'itemid':itemid,'bomidArr':bomidArr,'qtyArr':qtyArr,'notes':notes,'itemname':itemname},
            
            beforeSend:function(data){
                $(".updateMatBtn").text("Please wait...");
                $(".updateMatBtn").prop("disabled",true); 
            },
            success:function(data){
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
                    backtoMaterialBalance();
                    },3000);

                }

        });        
            
    });

                         ////////////////////////////    
                        //EASY AUTO COMPLETE DATA////
                        ////////////////////////////
    var options = {

        url: function(phrase) {
            return base_url+'Main_manufacturing/load_itemoption'
        },

        getValue: function(element) {
            return element.itemname;
        },

        list: {
            onClickEvent: function() {
            //var selectedItemValue = $("#f2_inventory").getSelectedItemData().id;

                currentSelectedItemId = $("#item").getSelectedItemData().id;
                currentSelectedItemName = $("#item").getSelectedItemData().itemname;
                currentSelectedUnit = $("#item").getSelectedItemData().description;

            //console.log(currentSelectedItemName);



            //$("#inputTwo").val(selectedItemValue).trigger("change");
            },
        //onHideListEvent: function() {
        //  $("#inputTwo").val("").trigger("change");
        //}
        },


        ajaxSettings: {
           dataType: "json",
           method: "POST",
           data: {
            dataType: "json"
           }
        },

        preparePostData: function(data) {
           data.phrase = $("#item").val();
           return data;
        },

    // requestDelay: 400
    };

    $("#item").easyAutocomplete(options);

    $('.easy-autocomplete').css('width','100%');

                         ////////////////////////////    
                        //EASY AUTO COMPLETE DATA////
                        ////////////////////////////

                        ////////////////////////////////
                        //FLOAT AND NUMBER VALUE REGEX//
                        ///////////////////////////////

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

                        ////////////////////////////////
                        //FLOAT AND NUMBER VALUE REGEX//
                        ///////////////////////////////
    function clearTable(){
        table.clear();         
        table.draw();           
    }

    function populateTable(data,val){
        if(val == 1){
            table.row.add(editArray);         
            table.draw();           
        }else if(val == 2){
            table.row.add(selectedDataarray);         
            table.draw();
        }else{
            alert('There was an error populating table, Please check material balance and material edit table codes');
        }
        

    }
 

    function clearAddform(){
    $("#qty").css("border-color", "#eee");  //rollback when not empty
    $("#qty").val('');
    $("#item").val('');
    }

    function checkInputs(formname){
        $(formname).find('.required_fields').each(function(){ //loop all input field then validate
            if ($(this).val() == ""){ // if empty show error
                flag = false; //update error to 1
                    // $(this).css("border-color","#d9534f");
                $(this).css("border-color", "#d9534f"); //change all empty to color red
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
                flag = true;
                $(this).css("border-color", "#eee");  //rollback when not empty
                errorFound = false;
            }

        });

        if(errorFound == false){
            $(formname).find('.qty').each(function(){ //loop all input field then validate
                if (($(this).val() <= 0) || ($(this).val() == '.')){ // if empty show error
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
                    $(this).css("bg-color", "#eee");
                }

            });            
        }
               
    }

    function backtoMaterialBalance(){

        var currUrl = window.location.href;
        currUrl = currUrl.replace("Material_balance_edit_view", "Material_balance");
        window.location = currUrl;      

    }

    function deleteMatchRow(matchValue){
        var filteredData = table
        .rows()
        .indexes()
        .filter( function ( value, index ) {
        return table.row(value).data()[0] == matchValue; 
        });

        table.rows( filteredData )
        .remove()
        .draw();
    }                   

});//main
