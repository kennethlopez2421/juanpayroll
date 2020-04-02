$(function(){
	var base_url = $("body").data("base_url");
    $(".interface2").css('display','none');
	var flag = false;
	var itemOptions;

    var dataArr = [];
    var selectedDataarray = [];
    var itemidArr = [];
    var itemqtyArr = [];

    var currentSelectedItemId;
    var currentSelectedItemName;
    var currentSelectedUnit;

    var currentSelectedMdf;

    var mdfnoArr = [];

    var ingId;
    var locId;

    var errorFound = false;
    var matchFound = false;
    var tableID = [];

    var table = $('#table-grid').DataTable({
                    columnDefs: [
                        { targets: [4], visible: true, orderable: false, sClass: 'text-center'}
                    ],
                    columnDefs: [
                        { targets: [0], sClass: 'td_id'}
                    ]
                    });//data table

    get_all_mdfno();

	$("#frmaddIngredients").submit(function(e){

		e.preventDefault();
		var form = $("#frmaddIngredients");

		var serial = form.serialize();

        checkInputs('#frmaddIngredients');

        if(flag){

        var mdf = $(".mdfno").val();
        if((jQuery.inArray(mdf, mdfnoArr) == -1)){
            $.toast({
                heading: 'Note',
                text: "MDF No. doesnt exist",
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: false,
                bgColor: '#f0ad4e',
                textColor: 'white'  
            });// if there is no seleccted item or the input item is not on the list
            $(".mdfno").val('');
        }else{
            // if(mdf != currentSelectedMdf){
            //     $.toast({
            //         heading: 'Warning',
            //         text: "MDF No. doesnt exist",
            //         icon: 'error',
            //         loader: false,  
            //         stack: false,
            //         position: 'top-center', 
            //         allowToastClose: false,
            //         bgColor: '#f0ad4e',
            //         textColor: 'white'  
            //     });
            //     $(".mdfno").val('');

            // }else{

            $(".interface1").css('display','none');
        	$(".interface2").css('display','block');
            $(".interface2").css('background',"#EEF5F9");
        	$(".tbl-details").css('display','block');
        	$(".btnSaveIngredient").css('display','block');
        	$(".lblprepDate").text($("#prepDate").val());
        	$(".lblbuildDate").text($("#buildDate").val());
        	$(".lblIngredients").text($("#ing>option:selected").html());
        	$(".lblBuild").text($("#ing>option:selected").html());
            $(".lblMDF").text($(".mdfno").val());

            
            ingId = $("#ing").val();
            
            locId = $("#build").val();
                
            //}
        }
    
        }


    });


    function populateTable(data){

        table.row.add(selectedDataarray);         
        table.draw();
    }

    $("#table-grid").on('click', '.btnDelete', function () {
        table.row($(this).parents('tr')).remove().draw(false);
    });

    $(".cancelBtn").click(function(e){
        clearAddform(); 
    });

    $(".add_inventory_modal").click(function(e){
        checkInputs('#addRow');
        var quantity = $("#qty").val();
        var item = $("#item").val();
        var price = $("#price").val();
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

                        var price = $("#price").val();


                        var inputVal = parseFloat(quantity);//inserting the value of input box in inputVal variable and converting the value to float/double

                        var newVal =  parseFloat(oldVal) + parseFloat(inputVal);//Sum the two values above the oldVal and inputVal
                        //oldVal[i] is the index of the match record

                        deleteMatchRow(currentSelectedItemId);//delete function delete the old record that match the new input
                        //dinedelete niya yung luma instead na palitan ng value yung nagmatch na record

                        selectedDataarray = [currentSelectedItemId,currentSelectedItemName.toUpperCase(),accounting.formatMoney(newVal),currentSelectedUnit,accounting.formatMoney(price),"<center><button class='btn btn-danger btnDelete btn-manufacturing btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"];// adding selected data to array 
                        populateTable(selectedDataarray);//adding of new record that have the updated qty value that we sum up above

                        matchFound =  true;//setting the matchFound boolean to true to avoid adding new record, kasi pag walang ganito magadd lng siya ng new record hindi siya magdedelete pag may magkaparehas
                        
                        break;

                    }else{
                        matchFound = false;
                    }
                    
                }//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP 

                    

                if(matchFound == false){

                    var total =  parseFloat(quantity) * parseFloat(price);

                    selectedDataarray = [currentSelectedItemId,currentSelectedItemName.toUpperCase(),accounting.formatMoney(quantity),currentSelectedUnit,accounting.formatMoney(price),accounting.formatMoney(total),"<center><button class='btn btn-danger btnDelete btn-manufacturing btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"];// adding selected data to array 
                    populateTable(selectedDataarray);                       
                }

                $('#viewAddrowModal').modal('toggle');
                clearAddform();                       
                }
        
            }
        }

    });



    $(".btnSaveIngredient").click(function(e){

        //getting needed inputs in form1
        var prepDate = $(".lblprepDate").text();
        var buildDate = $(".lblbuildDate").text();
        var mdfnum = $(".lblMDF").text();
        var notes = $("#txtareanotes").val();
        var tableisEmpty = false;

        //getting data in datatable
        var dataID = table.rows().columns(0).data();
        dataID.each(function (value, index) {
            
            if((value == null) || (value == 0) || (value == '')){
                tableisEmpty = true;
            }else{
                tableisEmpty = false;
            }//check if table is empty

            itemidArr = itemidArr.concat(value);
        });

        var dataQTY = table.rows().columns(2).data();
        dataQTY.each(function (value, index) {
            itemqtyArr = itemqtyArr.concat(value);
        });

        if(tableisEmpty){
            $.toast({
                heading: 'Note',
                text: 'No data available in table',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: false,
                bgColor: '#f0ad4e',
                textColor: 'white'  
            });
        }else{

            $.ajax({
                type: 'post',
                url: base_url+'Main_manufacturing/save_Ingredients',
                data:{'itemidArr':itemidArr,'itemqtyArr':itemqtyArr,'prepDate':prepDate,'buildDate':buildDate,'ingId':ingId,'locId':locId,'notes':notes,'mdfnum':mdfnum},
            
                beforeSend:function(data){
                    $(".btnSaveIngredient").text("Please wait...");
                    $(".btnSaveIngredient").prop("disabled",true); 
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
                        text: 'Something went wrong. Please try again.',
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
                }

            });

        }

    }); 
                         ////////////////////////////    
                        //EASY AUTO COMPLETE DATA////
                        ////////////////////////////
	var options = {

		url: function(phrase) {
			return base_url+'Main_manufacturing/load_itemoption_ingaddition'
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
        //	$("#inputTwo").val("").trigger("change");
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
           data.mdf = $(".lblMDF").text();
    	   return data;
        },

    // requestDelay: 400
    };

	$("#item").easyAutocomplete(options);

    $('.easy-autocomplete').css('width','100%');

    var options2 = {

        url: function(phrase) {
            return base_url+'Main_manufacturing/load_mdfno'
        },

        getValue: function(element) {
            return element.buildno;
        },

        list: {
            onClickEvent: function() {
            //var selectedItemValue = $("#f2_inventory").getSelectedItemData().id;

                // currentSelectedItemId = $("#item").getSelectedItemData().id;
                currentSelectedMdf = $("#mdfno").getSelectedItemData().buildno;
                // currentSelectedUnit = $("#item").getSelectedItemData().description;

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
           data.phrase = $("#mdfno").val();
           return data;
        },

    // requestDelay: 400
    };

    $("#mdfno").easyAutocomplete(options2);

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

    //allowing numeric without decimal 
    $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
        $(this).val($(this).val().replace(/[^\d].+/, ""));
    
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
 
                        ////////////////////////////////
                        //FLOAT AND NUMBER VALUE REGEX//
                        ///////////////////////////////


    function clearAddform(){
    $("#qty").css("border-color", "#eee");  //rollback when not empty
    $("#qty").val('');
    $("#item").val('');
    }

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
                flag = true;
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
                }

            }); 

        }


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


    function get_all_mdfno(){

            $.ajax({
                type: 'post',
                url: base_url+'Main_manufacturing/get_all_mdfno',
                data:{},

                success:function(data){
                    if(data.success == 1){
                        mdfnoArr = mdfnoArr.concat(data.mdfnoarr);
                    }else{
                        alert("theres error getting mdfno");
                    }
                }

            });

    }

});//main
